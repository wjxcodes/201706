<?php
/**
 * @author demo
 * @date 2014年10月28日
 */
/**
 * 文档下载控制器类，用于处理作业下载试题历史下载相关操作
 */
namespace Doc\Manage;
class DocDownManage extends BaseController  {
    /**
     * 下载信息列表
     * @author demo
     */
    public function index(){
        $adminName = $this->getCookieUserName();
        $pageName = '试卷/作业记录';
        $where=$this->getWhere($_REQUEST);
        $perPage = C('WLN_PERPAGE');
        $subjectArr=SS('subject');
        $docDownObj = $this->getModel('DocDown');
        $count = $docDownObj->selectCount(
            $where['data'],
            'DownID'); // 查询满足要求的总记录数
        $subjectArray = SS('subjectParentId'); //获取学科数据集
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=page($count,$_GET['p'],$perPage) . ',' . $perPage;
        $list = $docDownObj->pageData(
            '*',
            $where['data'],
            'DownID DESC',
            $page
            );
        foreach($list as $i=>$iList){//从缓存中得到所在学科
            if($list[$i]['SubjectID']=='0'){
                $list[$i]['SubjectName']='无';
            }else{
                $list[$i]['SubjectName']=$subjectArr[$list[$i]['SubjectID']]['ParentName'].$subjectArr[$list[$i]['SubjectID']]['SubjectName'];
            }
        }
        $this->pageList($count, $perPage, $where['map']);
        $subjectBuffer = SS('subject');
        foreach($list as $i => $val){
            $list[$i]['gradeInfo'] = $subjectBuffer[$val['SubjectID']]['ParentName'].$subjectBuffer[$val['SubjectID']]['SubjectName'];
        }
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('subjectArray',$subjectArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

   /**
    * 下载信息详细
    * @author demo 
    */
    public function showMsg(){
        $pageName = '试卷/作业详情';
        if(!$_GET['id']){
            $this->setError('30301');//缺失数据参数，请重试
        }
        $downID = $_GET['id'];
        $where='DownID='.$downID;
        $thisTpl = $this->getModel('DocDown')->pageData(
            '*',
            $where,
            'DownID desc , DownStyle asc',
            1);
        $resultArray = array();
        //处理导学案
        if($thisTpl[0]['DownStyle']==3){
            $caseTpl=$this->getModel('CaseTpl');
            $cookitStr=unserialize($thisTpl[0]['CookieStr']);
            $testIdStr=$caseTpl->reSetTestIdAndLoreId($cookitStr); //获取模板json数据中的知识点ID及试题ID
            $resultArray=$caseTpl->getCaseAndTestFromIndex($testIdStr,array('testid','testold','answerold'),array('LoreID','Lore','Answer')); //获取知识和试题数据

        }else{
            //处理试卷
            $testStr=R('Common/DocLayer/explodeCookieStr',array($thisTpl[0]['CookieStr']));
            $query = getStaticFunction('TestQuery','getInstance','ArchiveQuery');
            $query->setParams(array(), $testStr);
            $testList = $query->getResult(true)[0];
            $test = $this->getModel('Test');
            //试题排序
            $listArray = explode(',',str_replace(\Test\Model\TestQueryModel::DIVISION, '', $testStr));
            $resultArray = $test->replaceTest($listArray,$testList);
        }
        //处理试题中的图片地址
        //$host=C('WLN_DOC_HOST');
        //$test=$this->getModel('Test');

        foreach($resultArray as $i=>$iResult){
            $resultArray[$i]['Test']=R('Common/TestLayer/strFormat',array($resultArray[$i]['Test']));
            $resultArray[$i]['Answer']=R('Common/TestLayer/strFormat',array($resultArray[$i]['Answer']));
            $resultArray[$i]['Analytic']=R('Common/TestLayer/strFormat',array($resultArray[$i]['Analytic']));
            $resultArray[$i]['Remark']=R('Common/TestLayer/strFormat',array($resultArray[$i]['Remark']));
        }

        if(checkString('canLoad',$thisTpl[0]['DocPath'])){
            $host=C('WLN_DOC_HOST');
            $paperName = $thisTpl[0]['DocName'];
            $url=$host.R('Common/UploadLayer/getDocServerUrl',array($thisTpl[0]['DocPath'],'down','',$paperName));

            if($host){
                $thisTpl[0]['DocPath']=$url;
            }
        }else{
            $thisTpl[0]['DocPath']='';
        }
        $contentList=$resultArray;
        $subject=SS('subject');
        $thisTpl[0]['SubjectName']=$subject[$thisTpl[0]['SubjectID']]['ParentName'].$subject[$thisTpl[0]['SubjectID']]['SubjectName'];
        /*载入模板标签*/
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('testList', $contentList); //页面标题
        $this->assign('edit',$thisTpl[0]);
        $this->display();
    }
    /**
     *@文档历史下载导出
     *@author demo
     */
    public function export() {
        $where=$this->getWhere($_REQUEST);
        $count = $this->getModel('DocDown')->selectCount(
            $where['data'],
            'DownID'); // 查询满足要求的总记录
        $perpage = 2000;
        if($count>2000 && empty($_REQUEST['p'])){
            $count = $this->getModel('DocDown')->selectCount(
                $where['data'],
                'DownID'); // 查询满足要求的总记录
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $page=R('Common/SystemLayer/exportPageList',array($count,$perpage,$where['map']));
            $result['data']=$page;
            $result['ifPage']=1;
            $this->setBack($result);
        }
        if(!$_GET['p']){
            $this->setBack('');
        }
        if($_GET['p']=='all'){
            $_GET['p']=0;
        }
        $page=(page($count,$_GET['p'],$perpage)). ',' . $perpage;
        //写入日志
        $this->adminLog($this->moduleName,'导出日志记录where【'.$where['data'].'】');
        $subjectArray=SS('subject');
        $logData = $this->getModel('DocDown')->pageData(
            'DownID,UserName,LoadTime,SubjectID,DocName,IP,DownStyle',
            $where['data'],
            'DownID desc',
            $page
        );
        foreach($logData as $i=>$iLogData){
            $iLogData['SubjectID']=$subjectArray[$iLogData['SubjectID']]['ParentName'].$subjectArray[$iLogData['SubjectID']]['SubjectName'];
            $iLogData['LoadTime']= date('Y-m-d H:i:s', $iLogData['LoadTime']);
            $logData[$i]=array_values($iLogData);
        }
        $keyName=array('编号','用户名','时间','所属学科','文档名称','IP','下载类型(1:试卷,2:作业,3:答题卡)');
        $keyWidth=array('10','20','20','10','60','20','20');
        $excelName=array('title'=>'文档历史下载日志列表','excelName'=>'文档历史下载信息导出Excel');
        $excelMsg=R('Common/SystemLayer/resetExcelMsg',array($keyName,$keyWidth));
        R('Common/SystemLayer/excelExport',array($excelMsg,$logData,$excelName));
    }
    /**
     * 获取查询条件
     * @param array $where 查询条件
     * @return array
     * @author demo
     */
    private function getWhere($where){
        $adminName = $this->getCookieUserName();
        $pageName = '试卷/作业记录';
        $map = array();
        $data = ' 1 = 1 ';
        if($this->ifSubject && $this->mySubject){
            $data .= ' AND SubjectID in ('.$this->mySubject.')';
        }
        if ($_REQUEST['name']) {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND UserName like "%'.$_REQUEST['name'].'%" ';
        } else {
            //高级查询
            if ($_REQUEST['userName']) {
                if($this->ifDiff && $adminName!=$_REQUEST['name']){
                    $this->setError('30313');//您没有权限查看该内容！
                    return;
                }
                $map['userName'] = $_REQUEST['userName'];
                $data .= ' AND UserName like "%'.$_REQUEST['userName'].'%" ';
            }
            if($_REQUEST['SubjectID']){
                $data.=' AND SubjectID='.$_REQUEST['SubjectID'];
            }
            $start = $_REQUEST['start'];
            if(strstr($start,'-')){
                $start = strtotime($start);
            }
            $end = $_REQUEST['end'];
            if(strstr($end,'-')){
                $end = strtotime($end);
            }
            if ($start){
                if (empty ($end)) $end = time();
                $map['start'] = $start;
                $map['end'] = $end;
                $_REQUEST['start'] = date('Y-m-d',$start);
                $_REQUEST['end'] = date('Y-m-d',$end);
                $data .= ' AND LoadTime between ' . ($start) . ' and ' . ($end) . ' ';
            }
        }
        $where['data']=$data;
        $where['map']=$map;
        return $where;
    }

    /**
     * 把用户名更新成UserID,更新完，请删除
     *
     */
      public function upDateUserNameToUserID($p=1,$size=1000){
          echo '<meta charset="utf-8">';
          if($p<=0||$size<=0||$size>1100){
              echo '参数错误';
          }
          $wrongAmount=0;
          $limit=($p-1)*$size.','.$size;
          $userMsg=D('Base')->field('a.DownID,b.UserID')
              ->table('zj_doc_down a')
              ->join(' zj_user b on a.UserName=b.UserName')
              ->where('a.UserID=0')
              ->limit($limit)
              ->select();
          if($userMsg){
              foreach($userMsg as $i=>$iUserMsg){
                  $DownID=$userMsg[$i]['DownID'];
                  $data['UserID']=$userMsg[$i]['UserID'];

                  $result=$this->getModel('DocDown')->updateData(
                      $data,
                      'DownID='.$DownID
                  );
                  if($result!=false){
                      echo '更新成功['.$DownID.']<br>';
                  }else{
                      $wrongAmount+=1;
                  }
              }
              if($wrongAmount===0){//一切ok，跳转
                  redirect(__ACTION__.'?p='.($p+1).'&size='.$size,1);
              }else{
                  echo '出现错误';
              }
          }

      }
}