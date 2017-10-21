<?php
/**
 * 公式任务管理类
 */
/**
 * @author demo
 * @date 2014年11月10日
 * @update 2015年1月22日
 */
namespace Teacher\Manage;
class StudentWorkBManage extends BaseController {
    /**
     * 公式任务列表
     * @author demo
     */
    public function index(){
        $data=isset($_GET['p']) ? $_GET : $_POST;
        $param = array('UserName'=>false,'WorkID'=>false,'Status'=>false,'name'=>false,'p'=>false);
        $where = array_intersect_key($data,$param);
        $whereData = '1=1';
        $page = 1;
        if($where['p'] != ''){
            $page = $where['p'];
        }
        unset($where['p']);
        foreach($where as $i=>$iWhere){
            if($iWhere != ''){
                if($i=='WorkID'){
                    if(is_numeric($iWhere)){
                        $whereData .= ' AND '.$i.'=\''.$iWhere.'\'';
                    }else{
                        $this->setError('30502');
                        break;
                    }
                }else{
                    $whereData .= ' AND '.$i.'=\''.$iWhere.'\'';
                }

            }
        }
        if($this->ifSubject && $this->mySubject){
            $whereData .= ' AND SubjectID IN ('.$this->mySubject.')';
        }
        $StudentWork = $this->getModel('StudentWork');
        $count = $StudentWork->selectCount(
            $whereData,
            'WorkID');
        $limit= C('WLN_PERPAGE');
        $this->pageList($count,$limit);
        $data= $StudentWork->pageData(
            '*',
            $whereData,
            'LastTime DESC',
            (($page-1)*$limit).','.$limit);
        $this->assign('pageName','公式任务列表');
        $this->assign('list', $data);
        $this->display();
    }
    /**
     * 添加公式任务
     * @author demo
     */
    public function add(){
        $pageName = '添加公式任务';
        $act = 'add'; //模板标识
        //学科
        $subjectArray=SS('subjectParentId'); //获取学科数据集
        $verify = md5('add'.$this->getCookieUserName());
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('verify',$verify);
        $this->display();
    }
    /**
     * 添加用户信息
     * @author demo 
     */
    public function user(){
        C('SHOW_PAGE_TRACE', false);
        $powerUser = $_GET['s'];//教师类别
        $subjectID = $_GET['SubjectID'];//学科查找
        if($this->ifSubject && $this->mySubject){
            if(!in_array($subjectID,explode(',',$this->mySubject))){
                $this->setError('30712');//您不能添加非所属学科用户！
            }
        }
        $userName = $_GET['name'];//用户名查找
        //查询标引权限分组ID
        $buffer = $this->getModel('PowerUser')->selectData(
            'PUID',
            'PowerUser="'.$powerUser.'"');
        $puid = $buffer[0]['PUID'];
        //查询属于标引权限分组的用户ID
        $userIdArr = $this->getModel('UserGroup')->selectData(
            'UserID',
            'GroupName = 3 AND GroupID = "'.$puid.'"');
        $userArr = array();//存放用户ID；
        foreach($userIdArr as $i => $iUserIdArr){
            $userArr[] = $iUserIdArr['UserID'];
        }
        $userStr = implode(',',$userArr);//用户ID字符串
        //查询条件
        $data = 'UserID in ('.$userStr.')';
        $data .= ' and Status=0 and Whois=1';
        if($userName){
            $data .= ' and UserName like "%'.$userName.'%" ';
            $map['name'] = $userName;
        }
        if($subjectID){
            $data .= ' and SubjectStyle="'.$subjectID.'"';
            $map['SubjectID'] = $subjectID;
        }
        $user = $this->getModel('User');
        $count = $user->selectCount(
            $data,
            '*'); // 查询满足要求的总记录数
        // 进行分页数据查询
        $perpage = C('WLN_PERPAGE');
        $page = page($count,$_GET['p'],$perpage);
        $page=$page.','.$perpage;
        $list=$user->pageData(
            '*',
            $data,
            'UserID DESC',
            $page);
        $this->pageList($count,$perpage,$map);
        $subjectArray =  SS('subjectParentId'); //获取学科数据集
        
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('s',$powerUser);
        $this->assign('subjectArray', $subjectArray);
        $this->display();
    }
    /**
     * 获取doc
     * @author demo
     */
    public function doc(){    
        C('SHOW_PAGE_TRACE', false);
        $subjectID = $_GET['SubjectID'];
        $perpage = C('WLN_PERPAGE');
        $data = '';
        if($subjectID){
            if($this->ifSubject && $this->mySubject){
                if(!in_array($subjectID,explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能添加非所属学科试卷！
                }
            }
            $data .= 'IfIntro=0 AND IfEq=0 and SubjectID="'.$subjectID.'"';
            $map['SubjectID']=$subjectID;
        }
        $doc = $this-getModel('Doc');
        $count = $doc->selectCount(
            $data,
            "*"); // 查询满足要求的总记录数
        $page = page($count,$_GET['p'],$perpage);
        $page = $page.','.$perpage;
        $list=$doc->pageData(
            '*',
            $data,
            'DocID asc',
            $page);
        $this->pageList($count,$perpage,$map);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->display();
    }
    /**
     * 删除
     * @author demo
     */
    public function delete(){
        $ids = $_GET['id'];
        if(empty($ids)){
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        $studentWork = $this->getModel('StudentWork');
        $result = $studentWork->isSpecifiesState($ids);
        if($this->ifSubject && $this->mySubject){
            $studentWorkData = $studentWork->selectData(
                'SubjectID',
                'WorkID in ('.$ids.')');
            foreach($studentWorkData as $i => $iStudentWorkData){
                if(!in_array($iStudentWorkData['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能删除非所属学科任务！
                    
                }
            }
        }
        if($result !== true){
            $this->setError('40402','',__URL__,implode(',', $result));//记录已完成或正在重做，不能被删除！
        }
        $result = $studentWork->deleteRecords($ids, $this->getCookieUserName());
        if($result !== true){
            $this->setError('40403','',__URL__,implode(',', $result));//记录因无权限未能被删除！
        }
        $this->showSuccess('数据删除成功！', __URL__);
    }
    /**
     * 保存
     * @author demo
     */
    public function save(){
        $act = $_POST['act'];
        unset($_POST['act']);
        if($act == 'add' || $act == 'edit'){
            $cookie = $this->getCookieUserName();
            $verify = ($act == 'add') ? ('add'.$cookie) : ($cookie.$_POST['WorkID']);
            $verify = md5($verify);            
            if($_POST['verify'] != $verify){
                $this->setError('40404','',__URL__);//不合法的操作
            }
            $studentWorkList = $this->getModel('StudentWorkList');
            $doclist = $_POST['doclist'];
            //添加验证
            if($act == 'add'){
                $result = $studentWorkList->checkIdentifier($doclist);
                if($result !== true){
                    $this->setError('30725','','',implode(', ', $result));
                }
            }
            $data['SubjectID'] = $_POST['SubjectID'];
            $data['UserName'] = $_POST['UserName'];
            $data['Content'] = $_POST['Content'];
            $data['WorkID'] = $_POST['WorkID'];
            $data['Admin'] = $cookie;
            if($this->ifSubject && $this->mySubject){
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能添加非所属学科公式任务！
                }
            }
            $result = $this->getModel('StudentWork')->saveData($data,$act);
            if($result === false){
                $this->setError($act == 'add' ? '30307' : '30311');//'保存失败' : '修改失败'
            }
            $msg = '保存成功！';
            if($act == 'add'){
                //插入任务列表
                $doclist = explode(',', $doclist);
                $listData['WorkID'] = $result;
                foreach($doclist as $iDoclist){
                    $listData['DocID'] = $iDoclist;
                    $this->getModel('StudentWorkList')->insertData(
                        $listData);
                    $this->getModel('Doc')->updateData(
                        array('IfEq'=>1),
                        'DocID='.(int)$iDoclist);
                }
            }
            $this->showSuccess($msg, __URL__);
        }else{
            $this->setError('30223');//模板标识不能为空！
        }
    }
    /**
     * 编辑公式任务
     * @author demo
     */
    public function edit(){
        $id = (int)$_GET['id'];
        if(empty($id)){
            $this->setError('30223');//数据标识不能为空！
        }
        $pageName = '编辑公式任务';
        $act = 'edit'; //模板标识 
        //提取编辑内容
        $studentWork = $this->getModel('StudentWork');
        $edit = $studentWork->selectData('*','WorkID='.$id,'',1); //该selectData中已处理
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712');//您不能编辑非所属学科任务！
            }
        }

        if($this->ifDiff && $this->getCookieUserName() != $edit[0]['Admin']){
            $this->setError('30812','',__URL__);//您没有权限操作该任务！
        }
        if($edit[0]['Status'] == 1){
            $this->setError('40405','',__URL__);//该任务已经分配！
        }
        if($edit[0]['Status'] == 2){
            $this->setError('40407','',__URL__);//该任务已经完成！
        }
        //查询文档内容
        $docList = $this->getModel('StudentWorkList')->unionSelect('studentWorkByDocId',$edit[0]['WorkID']);
        $this->assign('docArray',$docList);
        //学科
        $subjectArray=SS('subjectParentId'); //获取学科数据集
        $verify = md5($this->getCookieUserName().$id);
        /*载入模板标签*/
        $this->assign('edit',$edit[0]);
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('verify',$verify);
        $this->display('StudentWorkB/add');
    }
    /**
     * 审核任务
     * @author demo
     */
    public function audit(){
        $workID = $_POST['workid'];
        if(!$workID){
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        $content = $_POST['Content'];
        $studentWork = $this->getModel('StudentWork');
        $data = $studentWork->selectData(
            '*',
            'WorkID='.$workID,
            '',
            1);
        if($this->ifSubject && $this->mySubject){
            if(!in_array($data[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712');//您不能审核非所属学科任务！
            }
        }
        if(empty($data)){
            $this->setError('40408','',__URL__);//任务不存在！
        }
        if($this->ifDiff && $this->getCookieUserName() != $data[0]['Admin']){
            $this->setError('30812','',__URL__);//您没有权限操作该任务！
        }
        if($data[0]['Status'] == 0){
            $this->setError('40406','',__URL__);//该任务未完成！
        }
        if($data[0]['Status'] == 2){
            $this->setError('40407','',__URL__);//该任务已完成！
        }
        $studentWorkList = $this->getModel('StudentWorkList');
        $result = $studentWorkList->selectData(
            'WLID',
            'WorkID='.$workID);
        if(empty($result)){
            $this->setError('40409','',__URL__);//该任务不包含文档，无法被审核！
        }
        if(!$studentWorkList->isSpecifiesState($workID,array(2,3))){
            $this->setError('40410','',__URL__);//提交任务失败，存在未审核的试题！
        }
        //此处检查是否存在状态为3的任务列表，不包含则任务完成，或者为重做状态
        $status = $studentWorkList->isSpecifiesState($workID,array(0,1,2)) ? 2 : 3;
        if(!$studentWork->updateStatus($workID,$status,$content)){
            $this->setError('40411','',__URL__);//提交任务失败！
        }
        $this->showSuccess('提交任务成功！',__URL__);
    }
    /**
     * 任务进度
     * @author demo
     */
    public function taskSchedule(){
        $where = 'WorkID='.(int)$_GET['id'];
        $studentWork = $this->getModel('StudentWork');
        $sw = $studentWork->findData(
            '*',
            $where);
        if(empty($sw)){
            $this->setError('30301');//数据不存在！
        }
        if($this->ifSubject && $this->mySubject){
            if(!in_array($sw['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712');//您不能查看非所属学科任务进度！
            }
        }
        if($this->ifDiff && $this->getCookieUserName() != $sw['Admin']){
            $this->setError('30812','',__URL__);//您没有权限操作该任务！
        }
        $docList = $this->getModel('StudentWorkList')->unionSelect('studentWorkByDocId', $sw['WorkID']);
        $this->assign('docArray',$docList);
        $this->assign('workArray',$sw);
        $this->display();
    }
    /**
     * 删除指定任务列表的信息
     * @author demo
     */
    public function deldoc(){
        $wID = $_GET['wid'];
        $wlID = $_GET['wlid']; 
        if(!$wID || !$wlID) {
            $this->setError('30301',1);//数据标识不能为空！
        }
        $studentWork = $this->getModel('StudentWork');
        $data = $studentWork->selectData(
            '*',
            'WorkID='.$wID,
            '',
            1);
        if($this->ifSubject && $this->mySubject){
            if(!in_array($data[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712',1);//您不能删除非所属学科公式任务！   
            }
        }
        if(empty($data)){
            $this->setError('40408',1);//任务不存在！
        }
        if($this->ifDiff && $this->getCookieUserName() != $data[0]['Admin']){
            $this->setError('30812',1);//您没有权限操作该任务！
        }
        $studentWorkList = $this->getModel('StudentWorkList');
        $result = $studentWorkList->deleteRecordsByWorkListId($wlID);
        if($result === false){
            $this->setError('30302',1);//删除失败
        }
        $this->setBack('success');
    }
    /**
     * 修改文档状态
     * @author demo
     */
    public function checkdoc(){
        $wlID = $_GET['wlid'];
        $wID = $_GET['wid'];
        $s = $_GET['s'];
        if(!$wlID or !$wID or !$s){
            $this->setError('30301',1);//数据标识不能为空！
        }
        $studentWork = $this->getModel('StudentWork');
        $data = $studentWork->selectData(
            '*',
            'WorkID='.$wID,
            '',
            1);
        if($this->ifSubject && $this->mySubject){
            if(!in_array($data[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712',1);//您不能修改非所属学科文档状态！
            }
        }
        if(empty($data)){
            $this->setError('40408',1);//任务不存在！
        }
        if($this->ifDiff && $this->getCookieUserName() != $data[0]['Admin']){
            $this->setError('30812',1);//您没有权限操作该任务！
        }
        $studentWorkList = $this->getModel('StudentWorkList');
        if($studentWorkList->updateStatusByWorkListId($wlID,$s) === false){
            $this->setError('30308',1);//操作失败！
        }
        $this->setBack('success');
    }
    /**
     * 查看公式任务信息列表
     * @author demo
     */
    public function subjects(){
        $docID = $_GET['docid'];
        $workID = $_GET['workid'];
        if(empty($docID) || empty($workID)){
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        $studentWork = $this->getModel('StudentWork');
        $data = $studentWork->selectData(
            '*',
            'WorkID='.$workID,
            '',
            1);
        if($this->ifSubject && $this->mySubject){
            if(!in_array($data[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30313');//您不能查看非所属学科任务信息！
            }
        }
        if(empty($data)){
            $this->setError('40408',0,__URL__);//任务不存在！
        }
        if($this->ifDiff && $this->getCookieUserName() != $data[0]['Admin']){
            $this->setError('30812','',__URL__);//您没有权限操作该任务！
        }
        //查询文档信息
        $doc = $this->getModel('Doc')->selectData(
            'DocName',
            'DocID='.$docID);
        if(empty($doc)){
            $this->setError('30815','',__URL__);//文档不存在！
        }
        $list['DocName'] = $doc[0]['DocName'];
        //获取试题数据
        $testBuffer = $this->getModel('Test')->selectData(
            'TestID,Test,Equation',
            'DocID='.$docID,
            'NumbID asc');
        $host=C('WLN_DOC_HOST');
        $testdoc = $this->getModel('TestDoc');
        foreach($testBuffer as $i=>$iTestBuffer){
            foreach($iTestBuffer as $j=>$jTestBuffer){
                if('Test' == $j){
                    $testBuffer[$i][$j] =  R('Common/TestLayer/strFormat',array($testBuffer[$i][$j]));
                }
            }
            $content = $testdoc->fetchEquationById($iTestBuffer['TestID']);
            $testBuffer[$i]['orginalNum'] = count($content['DocTest']);
            $test=$this->getModel('Test');
            $testBuffer[$i]['addNum'] = count($test->getEquations($iTestBuffer['Equation']));
        }
        $list['docid'] = $docID;
        $list['workid'] = $workID;
        $list['list'] = $testBuffer;
        unset($testBuffer,$host,$test);
        $this->assign('list',$list);
        $this->display();
    }
    /**
     * 显示题型的内容
     * @author demo
     */
    public function showTestContent(){
        $docID = $_GET['docid'];
        $workID = $_GET['workid'];
        $testid = $_GET['testid'];
        if(!$docID || !$workID || !$testid){
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        $studentWork = $this->getModel('StudentWork');
        $data = $studentWork->selectData(
            '*',
            'WorkID='.$workID,
            '',
            1);
        if($this->ifSubject && $this->mySubject){
            if(!in_array($data[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712','',__URL__);//您不能查看非所属学科题型内容！
            }
        }
        if(empty($data)){
            $this->setError('40408',0,__URL__);//任务不存在！
        }
        if($this->ifDiff && $this->getCookieUserName() != $data[0]['Admin']){
            $this->setError('30812','',__URL__);//您没有权限操作该任务！
        }
        $test = $this->getModel('Test');
        $testData = $test->selectData(
            'Test,
            Equation',
            'TestID='.$testid,
            '',
            '1');
        $testData = $testData[0];
        $testData['Test'] =  R('Common/TestLayer/strFormat',array($testData['Test']));
        $testData['Equation'] = $test->getEquations($testData['Equation']);
        $this->assign('testData',$testData);
        $equations = $this->getModel('TestDoc')->fetchEquationById($testid);
        $this->assign('len',count($testData['Equation']));
        //$this->display('StudentWork/preview');
        $this->setBack($this->fetch('StudentWorkB/preview'));
    }
    /**
     * 公式任务数据统计
     * @author demo
     */
    public function statistic(){
        $param = isset($_GET['username']) ? $_GET : $_POST;
        $result = $this->getModel('StudentWork')->statistic($param);
        $this->assign('datas',$result);
        $this->display();
    }
    /**
     * 公式任务数据统计明细
     * @author demo
     */
    public function statisticDetail(){
        $username = $_POST['username'];
        if(!$username){
            $this->setError('30301');//数据标识不能为空！
        }
        $result = $this->getModel('StudentWork')->statisticDetail($username,$_POST);
        $this->assign('result',$result);
        $this->display();
    }
}