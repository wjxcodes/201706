<?php
/**
 * @author demo 
 * @date 2014年10月23日
 */
/**
 * 文档控制器类，用于处理试题关键字搜索日志查看操作记录相关操作
 */
namespace Home\Manage;
class LogSearchManage extends BaseController  {
    /**
     * @搜索日志列表页
     * @author demo
     */
    public function index(){
        $pageName = '关键词搜索记录';
        $where=$this->getWhere($_REQUEST);
        $subjectArr=SS('subject');
        $subjectArray =  SS('subjectParentId'); //获取学科数据集
        $perpage = C('WLN_PERPAGE');
        $logSearch = $this->getModel('LogSearch');
        $count = $logSearch->selectCount(
            $where['data'],
            '*'
        );
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=page($count,$_GET['p'],$perpage) . ',' . $perpage;
        $list = $logSearch->pageData(
            '*',
            $where['data'],
            "SearchID DESC",
            $page);
        foreach($list as $i=>$iList){//从缓存中得到所在学科
            if($list[$i]['SubjectID']=='0'){
                $list[$i]['SubjectName']='无';
            }else{
                $list[$i]['SubjectName']=$subjectArr[$list[$i]['SubjectID']]['ParentName'].$subjectArr[$list[$i]['SubjectID']]['SubjectName'];
            }
        }
        $this->pageList($count, $perpage, $where['map']);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('subjectArray',$subjectArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * @智能组卷详情页
     * @author demo
     */
    public function showMsg(){
        if(!$_GET['id']){
            $this->setError('30301'); //缺失数据参数，请重试
        }
        $pageName = '关键词搜索记录详情';
        $searchID=$_GET['id'];
        $where='SearchID='.$searchID;
        $thisTpl =  $this->getModel('LogSearch')->selectData(
            '*',
            $where,
            '',
            '1'
        );
        $subjectarr=SS('subject');
        if($thisTpl[0]['SubjectID']!=0){
        $thisTpl[0]['SubjectName']=$subjectarr[$thisTpl[0]['SubjectID']]['ParentName'].$subjectarr[$thisTpl[0]['SubjectID']]['SubjectName'];
        }else{
            $thisTpl[0]['SubjectName']='无';
        }
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('edit',$thisTpl[0]);
        $this->display();
    
    }
    /**
     *@试题搜索记录导出
     *@author demo
     */
    public function export() {
        $where=$this->getWhere($_REQUEST);
        //写入日志
        $this->adminLog($this->moduleName,'导出日志记录where【'.$where['data'].'】');
        $logSearch = $this->getModel('LogSearch');
        $count = $logSearch->selectCount(
            $where['data'],
            'SearchID'); // 查询满足要求的总记录
        $perpage = 2000;
        if($count>2000 && empty($_REQUEST['p'])){
            $count = $logSearch->selectCount(
                $where['data'],
                'SearchID'); // 查询满足要求的总记录
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
        $subjectArray=SS('subject');
        $logData =$logSearch->pageData(
            'SearchID,UserName,KeyWord,Nums,LastTime,SubjectID',
            $where['data'],
            'SearchID desc',
            $page);
        foreach($logData as $i=>$iLogData){
            $iLogData['SubjectID']=$subjectArray[$iLogData['SubjectID']]['ParentName'].$subjectArray[$iLogData['SubjectID']]['SubjectName'];
            $iLogData['LastTime']= date('Y-m-d H:i:s', $iLogData['AddTime']);
            $logData[$i]=array_values($iLogData);
        }
        $keyName=array('编号','用户名','搜索内容','搜索次数','时间','所属学科');
        $keyWidth=array('10','20','60','10','20','20');
        $excelName=array('title'=>'试题搜索记录日志列表','excelName'=>'试题搜索记录信息导出Excel');
        $excelMsg=R('Common/SystemLayer/resetExcelMsg',array($keyName,$keyWidth));
        R('Common/SystemLayer/excelExport',array($excelMsg,$logData,$excelName));
    }
    /**
     * 获取查询条件
     * @param array $where
     * @return array
     * @author demo
     */
    private function getWhere($where){
        $adminName=$this->getCookieUserName();
        $map = array ();
        $data = ' 1=1 ';
        if($this->ifDiff){
            $data .=' and UserName="'.$adminName.'"';
        }
        if ($_REQUEST['name']) {
            if($this->ifDiff && $adminName!=$_REQUEST['name']){
                $this->setError('30313'); //您没有权限查看该内容！
                return;
            }
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND UserName ="'. $_REQUEST['name'] . '"';
        } else {
            //高级查询
            if ($_REQUEST['UserName']) {
                if($this->ifDiff && $adminName!=$_REQUEST['name']){
                    $this->setError('30313'); //您没有权限查看该内容！
                    return;
                }
                $map['UserName'] = $_REQUEST['UserName'];
                $data .= ' AND UserName ="'. $_REQUEST['UserName'] . '"';
            }
            if($_REQUEST['SubjectID']){
                $data.=' AND SubjectID='.$_REQUEST['SubjectID'];
            }
            $start = $_REQUEST['Start'];
            if(strstr($start,'-')){
                $start=strtotime($start);
            }
            $end = $_REQUEST['End'];
            if(strstr($end,'-')){
                $end=strtotime($end);
            }
            if ($start){
                if (empty ($end)) $end = time();
                $map['Start'] = $start;
                $map['End'] = $end;
                $_REQUEST['Start']=date('Y-m-d',$start);
                $_REQUEST['End']=date('Y-m-d',$end);
                $data .= ' AND LastTime between ' . ($start) . ' and ' . ($end) . ' ';
            }
        }
        $where['data']=$data;
        $where['map']=$map;
        return $where;
    }



    
}