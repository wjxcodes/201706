<?php
/**
 * @author demo 
 * @date 2014年10月23日
 */
/**
 * 文档控制器类，用于处理模板组卷操作记录相关操作
 */
namespace Dir\Manage;
class LogTplPaperManage extends BaseController{
    /**
     * @模板组卷列表页
     * @author demo
     */
    public function index(){
        $pageName = '模板组卷记录';
        $where=$this->getWhere($_REQUEST);
        $subjectArr=SS('subject');
        $subjectArray = SS('subjectParentId'); //获取学科数据集
        $perpage = C('WLN_PERPAGE');
        $logTplPaper=$this->getModel('LogTplPaper');
        $count = $logTplPaper->selectCount(
            $where['data'],
            '*'); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=page($count,$_GET['p'],$perpage) . ',' . $perpage;
        $list = $logTplPaper->pageData(
            '*',
            $where['data'],
            'PaperID DESC',
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
        $pageName = '模板组卷记录详情';
        $paperID=$_GET['id'];
        $where='PaperID='.$paperID;
        $thisTpl=$this->getModel('LogTplPaper')->selectData(
            '*',
            $where,
            '',
            '1');
        $subjectArr=S('subject');
        $thisTpl[0]['SubjectName']=$subjectArr[$thisTpl[0]['SubjectID']]['ParentName'].$subjectArr[$thisTpl[0]['SubjectID']]['SubjectName'];
        $thisTpl[0]['Param']=print_r(unserialize($thisTpl[0]['Param']),true);
        $this->assign('edit',$thisTpl[0]);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    
    }
    /**
     *@模板组卷记录导出
     *@author demo
     */
    public function export() {
        $where=$this->getWhere($_REQUEST); //获取查询条件
        //写入日志
        $this->adminLog($this->moduleName,'导出日志记录where【'.$where['data'].'】');
        $logTplPaper=$this->getModel('LogTplPaper');
        $count = $logTplPaper->selectCount(
            $where['data'],
            'PaperID'); // 查询满足要求的总记录
        $perpage = 2000;
        if($count>2000 && empty($_REQUEST['p'])){
            $count = $logTplPaper->selectCount(
                $where['data'],
                'PaperID'); // 查询满足要求的总记录
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
        $page=(page($count,$_GET['p'],$perpage)). ',' . $perpage; //分页条件
        $subjectArray=SS('subject');
        $logData = $logTplPaper->pageData(
            '*',
            $where['data'],
            'PaperID desc',
            $page);
        foreach($logData as $i=>$iLogData){
            $iLogData['SubjectID']=$subjectArray[$iLogData['SubjectID']]['ParentName'].$subjectArray[$iLogData['SubjectID']]['SubjectName'];
            $iLogData['AddTime']= date('Y-m-d H:i:s', $iLogData['AddTime']);
            unset($iLogData['TestList']);
            unset($iLogData['Content']);
            $logData[$i]=array_values($iLogData);
        }
        $keyName=array('编号','用户名','时间','所含试题','所属学科');
        $keyWidth=array('10','20','20','50','20','20');
        $excelName=array('title'=>'模板组卷记录日志列表','excelName'=>'模板组卷记录信息导出Excel');
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
        $adminname=$this->getCookieUserName();
        $map = array ();
        $data = ' 1=1 ';
        if($this->ifDiff){
            $data .=' and UserName="'.$adminname.'"';
        }
        if ($_REQUEST['name']) {
            if($this->ifDiff && $adminname!=$_REQUEST['name']){
                $this->setError('30313'); //您没有权限查看该内容！
                return;
            }
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND UserName ="'. $_REQUEST['name'] . '"';
        } else {
            //高级查询
            if ($_REQUEST['UserName']) {
                if($this->ifDiff && $adminname!=$_REQUEST['name']){
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
                $data .= ' AND AddTime between ' . ($start) . ' and ' . ($end) . ' ';
            }
        }
        $where['data']=$data;
        $where['map']=$map;
        return $where;
    }
}