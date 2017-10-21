<?php
/**
 * @author demo
 * @date 2014年11月6日
 *
 */
/**
 * 纠错记录管理控制器类，用于纠错记录管理相关操作
 */
namespace Manage\Controller;
class CorrectLogController extends BaseController  {
    var $moduleName = '纠错记录';
    /**
    * 按条件浏览；
    * @author demo
    */
    public function index() {
        $pageName = '纠错记录';
        $CorrectLog = $this->getModel('CorrectLog');
        $subjectArray =  SS('subjectParentId'); //父类数据集
        $data='1=1';
        if($this->ifSubject && $this->mySubject){
            $data .= ' and SubjectID in ('.$this->mySubject.')';
        }elseif($this->ifDiff){
            $data .= ' and Admin="'.$this->getCookieUserName().'" ';
        }
            //高级查询
            if ($_REQUEST['TestID']) {
                if(is_numeric($_REQUEST['TestID'])){
                    $map['TestID'] = $_REQUEST['TestID'];
                    $data .= ' AND TestID = "' . $_REQUEST['TestID'] . '" ';
                }else{
                    $this->setError('30502');
                }
            }
            if ($_REQUEST['UserName']){
                $map['UserName'] = $_REQUEST['UserName'];
                $data .= ' AND UserName = "' . $_REQUEST['UserName'] . '" ';
            }
            if ($_REQUEST['SubjectID']) {
                if($this->ifSubject && $this->mySubject){
                    if(!in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                        // $this->showerror('您不能搜索非所属学科纠错记录！');
                        $this->setError('30313','','','');
                    }
                }
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $data .= ' AND SubjectID = ' . $_REQUEST['SubjectID'] . ' ';
            }
            if($_REQUEST['IfAnswer']!=3 && $_REQUEST['IfAnswer']!=''){
                $map['IfAnswer']= $_REQUEST['IfAnswer'];
                $data .= ' AND IfAnswer='. $_REQUEST['IfAnswer'] . ' ';
            }

        $perpage = C('WLN_PERPAGE');
        $Subject=SS('subject');
        $count = $CorrectLog->selectCount(
            $data,
            '*');//统计总数
        //条件分页内容
        $page = page($count,$_GET['p'],$perpage);
        $page=$page.','.$perpage;
        $list = $CorrectLog->pageData(
            '*',
            $data,
            'CorrID desc',
            $page);//分页内容信息
        foreach($list as $key =>$val){
            $list[$key]['SubjectName']=$Subject[$list[$key]['SubjectID']]['ParentName'].$Subject[$list[$key]['SubjectID']]['SubjectName'];
            $list[$key]['Content'] = stripslashes_deep(formatString('IPReturn',$list[$key]['Content']));
        }
        //执行分页
        $this->pageList($count, $perpage, $map);
        /*载入模板标签*/
        $this->assign('subject_array', $subjectArray); //学科属性
        $this->assign('list', $list); //纠错数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
    * 按调取编辑页面；
    * @author demo
    */
    public function edit() {
        $CorrID=$_GET['id'];//获取数据标识
        //判断数据标识
        if(empty($CorrID)){
             $this->setError('30301'); //数据标识不能为空
        }
        $typeArr=array('0'=>'其他','1'=>'试题内容','2'=>'答案解析','3'=>'所属章节','4'=>'知识点属性');
        $Subject = SS('subject');
        $pageName = '查看错误详情';
        $act = 'edit'; //模板标识
           $correctLog=$this->getModel('CorrectLog');
        $correctData =$correctLog->selectData(
            '*',
            'CorrID='.$CorrID);
        if($this->ifSubject && $this->mySubject){
            if (!in_array($correctData[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30507'); //您不能处理非所属学科纠错记录
            }
        }elseif ($this->ifDiff) {
            //判断是否可以编辑
            if ($correctData[0]['Admin'] != $this->getCookieUserName()) {
                $this->setError('30812'); //您没有权限处理
            }
        }
        $edit = $correctLog->selectData(
            '*',
            "CorrID=$CorrID",
            '',
            '1');//获取内容
        $test = $this->getModel('Test');//调取试题信息
        $testmsg = $this->getModel('CorrectLog')->unionSelect('testRealByID',$edit[0]['TestID']);
        $host=C('WLN_DOC_HOST');
        if($host){//修改图片地址
            $edit[0]['Test']= R('Common/TestLayer/strFormat',array($testmsg[0]['Test']));
            $edit[0]['Answer']= R('Common/TestLayer/strFormat',array($testmsg[0]['Answer']));
            $edit[0]['Analytic']= R('Common/TestLayer/strFormat',array($testmsg[0]['Analytic']));
            $edit[0]['Remark']= R('Common/TestLayer/strFormat',array($testmsg[0]['Remark']));
            $edit[0]['Content'] = stripslashes_deep(formatString('IPReturn',$edit[0]['Content']));
        }
        $edit[0]['Test']=$test->formatTest($edit[0]['Test'],1,500,0,1,$testmsg[0]['OptionWidth'],$testmsg[0]['OptionNum'],$testmsg[0]['TestNum'],$testmsg[0]['IfChoose'],1);
        $edit[0]['Answer']='【答案】'.$test->formatTest($edit[0]['Answer'],1,0,0,0,0,0,$testmsg[0]['TestNum'],0,1);
        $edit[0]['Analytic']='【解析】'.$test->formatTest($edit[0]['Analytic'],1,0,0,0,0,0,$testmsg[0]['TestNum'],0,1);
        $edit[0]['Remark']='【备注】'.$test->formatTest($edit[0]['Remark'],1,0,0,0,0,0,$testmsg[0]['TestNum'],0,1);
        $edit[0]['SubjectName']=$Subject[$edit[0]['SubjectID']]['ParentName'].$Subject[$edit[0]['SubjectID']]['SubjectName'];//整理所属学科名称
        $typeid=explode(',',$edit[0]['TypeID']);
        $typeName='';
        foreach($typeid as $i=>$iTypeID){//错误类型
            if($iTypeID!=''){
                $typeName.='[<b>'.$typeArr[$iTypeID].'</b>]';
            }
        }
        $edit[0]['TypeName']=$typeName;
        //*载入模板标签*
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display('CorrectLog/answer');
    }
    /*
    *执行数据整合保存
    * @author demo
    *
    */
    public function save(){
        $data['AnswerName']=$this->getCookieUserName();
        $data['AnswerContent']=$_POST['AnswerContent'];
        $data['CorrID']=$_POST['CorrID'];
        $data['IfAnswer']=$_POST['IfAnswer'];
        $data['IfError']=$_POST['IfError'];
        $correctLog=$this->getModel('CorrectLog');
        $correctData = $correctLog->selectData(
            '*',
            'CorrID='.$data['CorrID']);
        if($this->ifSubject && $this->mySubject){
            if (!in_array($correctData[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712'); //您不能处理非所属学科纠错记录
            }
        }elseif ($this->ifDiff) {
            //判断是否可以编辑
            if ($correctLog['Admin'] != $this->getCookieUserName()) {
                $this->setError('30812'); //您没有权限处理
            }
        }
        $result=$correctLog->updateData(
            $data,
            'CorrID='.$data['CorrID']);
        if(!$result){
            $this->setError('30305'); //错误处理失败！请重试！
        }else{
            $this->showSuccess('处理成功！', __URL__);
        }
    }

    /**
     * 统计
     * @author demo 
     */
    public function stat(){
        $params = array(
            'page'=>$_REQUEST['p'],
            'prepage'=>$_REQUEST['prepage'],
            'begintime'=>$_REQUEST['begintime'],
            'endtime'=>$_REQUEST['endtime'],
            'subjectid'=>$_REQUEST['subjectid'],
            'username'=>$_REQUEST['username']
        );
        $data = $this->getModel('CorrectLog')->stat($params);
        $prepage = $data['params']['prepage'];
        unset($data['params']['prepage']);
        $this->pageList($data['count'], $prepage, $data['params']);
        $this->assign('list', $data['data']);
        $this->assign('pageName', '纠错任务统计');
        $this->assign('subject_array', SS('subjectParentId')); 
        $this->display('stat');
        //IfAnswer 0为未处理 1为处理
        //IfError 1为实质性错误，2为非实质性错误，0为未标注

        /*
        SELECT username,SUM(IF(IfError=1,1,0)) AS error, SUM(IF(IfAnswer=0, 1, 0)) AS undisposed, SUM(IF(IfAnswer=1, 1, 0)) AS dispose FROM zj_correct_log WHERE 1=1 AND UserName='15838201264' GROUP BY UserName;

        select * from zj_correct_log where UserName='15838201264'
        */
    }

    /**
     * 详情
     * @author demo 
     */
    public function detail(){
        $username = $_POST['username'];
        if(empty($username)){
            $this->setBack(array());
        }
        $begintime = $_POST['begintime'];
        $endtime = $_POST['endtime'];
        $subjectid = $_POST['subjectid'];
        $where = 'UserName="'.$username.'"';
        if($subjectid){
            $where .= ' AND SubjectID="'.$subjectid.'"';
        }
        if($begintime){
            $begintime = strtotime($begintime);
        }else{
            $begintime = 0;
        }
        if($endtime){
            $endtime = strtotime($endtime);
        }else{
            $endtime = time()+1000;
        }
        $page = $_POST['p'];
        if(!$page){
            $page = 1;
        }
        $where .= ' AND Ctime BETWEEN '.$begintime.' AND '.$endtime;
        $count = $this->getModel('CorrectLog')->selectCount($where, 'CorrID');
        $page = page($count, $page, 30);
        $limit = (($page-1)*30).',30';
        $result = $this->getModel('CorrectLog')->selectData('Ctime, CorrID, TestID, SubjectID, Content, IfError,IfAnswer, AnswerContent,From', $where, 'CorrID DESC', $limit);
        $subjects = SS('subjectParentId');
        $subjectPair = array();
        foreach($subjects as $key=>$value){
            foreach($value['sub'] as $k=>$v){
                $subjectPair[$v['SubjectID']] = $value['SubjectName'].$v['SubjectName'];
            }
        }
        foreach($result as $key=>$value){
            $result[$key]['SubjectName'] = $subjectPair[$value['SubjectID']];
            $result[$key]['Ctime'] = date('Y-m-d H:i:s',$value['Ctime']);
            unset($result[$key]['SubjectID']);
            $result[$key]['Content'] = formatString('IPReturn',stripslashes_deep($value['Content']));
        }
        $this->setBack(array('data'=>$result, 'count'=>$count));
    }
}