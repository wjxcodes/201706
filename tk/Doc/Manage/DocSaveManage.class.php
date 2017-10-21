<?php
/**
 * @author demo
 * @date 2014年8月7日
 */
/**
 * 试卷存档管理类，管理试卷存档数据
 */
namespace Doc\Manage;
class DocSaveManage extends BaseController  {
    var $moduleName='试卷存档管理';
    /**
     * 浏览文档存档信息
     * @author demo
     */
    public function index() {
        $pageName = '试卷存档管理';
        $map = array();
        $data=' 1 = 1 ';
        if ($this->ifSubject && $this->mySubject){
            $data .= ' AND a.SubjectID in (' . $this->mySubject . ')';
        }
        if($_REQUEST['name']){
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND a.UserName like "%'.$_REQUEST['name'].'%" ';
        }else{
            //高级查询
            if($_REQUEST['UserName']){
                $map['UserName'] = $_REQUEST['UserName'];
                $data .= ' AND a.UserName like "%'.$_REQUEST['UserName'].'%" ';
            }
            if($_REQUEST['SaveID']){
                if(is_numeric($_REQUEST['SaveID'])){
                    $map['SaveID'] = $_REQUEST['SaveID'];
                    $data .= ' AND a.SaveID = '.$_REQUEST['SaveID'].' ';
                }else{
                    $this->setError('30502');
                }
            }

            if(!empty($_REQUEST['StyleState'])){
                $map['StyleState'] = $_REQUEST['StyleState'];
                $data .= ' AND a.StyleState = '.$_REQUEST['StyleState'].' ';
            }
        }
        $perPage = C('WLN_PERPAGE');
        $count = $this->getModel('DocSave')->selectCount(
            $data,
            '*',
            'a'); // 查询满足要求的总记录数
        $page = page($count,$_GET['p'],$perPage);
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page =$page.','.$perPage;
        $list = D('Base')->unionSelect('docSavePageData',$data,$page);
        $this->pageList($count,$perPage,$map);

        /*载入模板标签*/
        $this->assign('list', $list); //菜单数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 查看试卷存档信息
     * @author demo
     */
    public function view() {
        $pageName = '查看试卷存档';
        $docSave = $this->getModel('DocSave');
        $saveID = $_GET['id'];
        if($this->ifSubject && $this->mySubject){
            $saveIDArr = $this->getModel('DocSave')->selectData(
                'SubjectID',
                'SaveID in ('.$saveID.')');
            $subjectIDArr = explode(',',$this->mySubject);
            foreach($saveIDArr as $i => $iSaveIDArr){
                if(!in_array($iSaveIDArr['SubjectID'],$subjectIDArr)){
                    $this->setError('30313'); //您不能查看非所属学科文档存档
                }
            }
        }
        if(empty($saveID)){
            $this->setError('30301');//数据标识不能为空
        }
        $edit = $this->getModel('DocSave')->selectData(
            '*',
            'SaveID = '.$saveID);
        $subject = SS('subject');
        $edit[0]['SubjectName'] = $subject[$edit[0]['SubjectID']]['SubjectName'];
        // $testList = $this->dbConn->testSelectById($edit[0]['TestList']);
        $query = getStaticFunction('TestQuery', 'getInstance', 'ArchiveQuery');
        $query->setParams(array(), $edit[0]['TestList']);
        $testList = $query->getResult(true)[0];
        $test = $this->getModel('Test');
        //试题排序
        $testArray = array();
        $listArray = explode(',',str_replace(\Test\Model\TestQueryModel::DIVISION, '', $edit[0]['TestList']));
        $testArray = $test->replaceTest($listArray,$testList);
        
        $host = C('WLN_DOC_HOST');
        if($host && $testArray){
            foreach($testArray as $i => $iTestArray){
                $testArray[$i]['Test'] = R('Common/TestLayer/strFormat',array($iTestArray['Test']));
                $testArray[$i]['Answer'] = R('Common/TestLayer/strFormat',array($iTestArray['Answer']));
                $testArray[$i]['Analytic'] = R('Common/TestLayer/strFormat',array($iTestArray['Analytic']));
                $testArray[$i]['Remark'] = R('Common/TestLayer/strFormat',array($iTestArray['Remark']));
            }
        }
        
        $this->assign('edit', $edit[0]); //菜单数据集
        $this->assign('testArray', $testArray); //菜单数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 将指定ID号的文档，配置给超级管理员
     * @author demo
     */
    public function copyToAdmin(){
        $docSave = $this->getModel('DocSave');
        $saveID = $_GET['id'];
        if($this->ifSubject && $this->mySubject){
            $saveIDArr = $docSave->selectData(
                'SubjectID',
                'SaveID in ('.$saveID.')');
            $subjectIDArr = explode(',',$this->mySubject);
            foreach($saveIDArr as $i => $iSaveIDArr){
                if(!in_array($iSaveIDArr['SubjectID'],$subjectIDArr)){
                    $this->setError('30313'); //您不能查看非所属学科文档存档
                }
            }
        }
        if(empty($saveID)){
            $this->setError('30301');//数据标识不能为空
        }
        $edit = $docSave->selectData(
            '*',
            'SaveID = '.$saveID);
        if(!empty($edit)){
            $newMsg['UserName']='admin';
            $newMsg['LoadTime']=$edit[0]['LoadTime'];
            $newMsg['TestList']=$edit[0]['TestList'];
            $newMsg['CookieStr']=$edit[0]['CookieStr'];
            $newMsg['SubjectID']=$edit[0]['SubjectID'];
            $newMsg['SaveName']=$edit[0]['SaveName'];
            $newMsg['SavePwd']=$edit[0]['SavePwd'];
            $newMsg['StyleState']=$edit[0]['StyleState'];
            $newMsg['TestNum']=$edit[0]['TestNum'];
            $lastResult=$docSave->insertData($newMsg);
            if($lastResult){
                $this->showSuccess('文档已成功指定给管理员！',__URL__);
            }else{
                $this->setError('1X1015'); //指派给管理员失败
            }
        }
    }
}