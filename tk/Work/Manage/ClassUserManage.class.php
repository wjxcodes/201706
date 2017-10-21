<?php
/**
 * @author demo
 * @date 2014年8月11日
 */
/**
 *班级用户控制器类，用于处理班级用户档案操作
 * 
 */
namespace Work\Manage;
class ClassUserManage extends BaseController  {
    var $moduleName = '班级成员管理';
    /**
     * 按条件浏览；
     * @author demo
     */
    public function index(){
        $map=array();
        $pageName = '班级成员管理';
        $data='1=1';
        if($_GET['ClassID']){//查看班级成员时所需班级ID
            if(is_numeric($_GET['ClassID'])){
                $map['ClassID']=$_GET['ClassID'];
                $data.=" and cu.ClassID=".$_GET['ClassID'];
            }else{
                $this->setError('30502');
            }
        }
        if ($_REQUEST['esayusername']) {//简单查询
            $map['UserName'] = '';
            $data .= ' AND u.UserName = "' . $_REQUEST['esayusername'] . '" ';
        } else { //高级查询，组合$data为where条件
            if ($_REQUEST['ClassNum']) {//班级编号
                if($_REQUEST['ClassNum']){
                    $map['ClassNum'] = $_REQUEST['ClassNum'];
                    $data .= ' AND cl.ClassID = "' . $_REQUEST['ClassNum'] . '" ';
                }else{
                    $this->setError('30502');
                }
            }
            if ($_REQUEST['UserName']) {//班级内容成员账号
                $map['UserID'] = $_REQUEST['UserID'];
                $data .= ' AND u.UserName = "' . $_REQUEST['UserName'] . '" ';
            }
            if ($_REQUEST['OrderID']) {//排序编号
                $map['OrderID'] = $_REQUEST['OrderID'];
                $data .= ' AND cl.OrderNum = "' . $_REQUEST['OrderID'] . '" ';
            }
            if ($_REQUEST['SchoolID']) {//学校ID
                $map['SchoolID'] = $_REQUEST['SchoolID'];
                $data .= ' AND cl.SchoolID = "' . $_REQUEST['SchoolID'] . '" ';
            }
            if ($_REQUEST['GradeID']) {//年级ID
                $map['GradeID'] = $_REQUEST['GradeID'];
                $data .= ' AND cl.GradeID = ' . $_REQUEST['GradeID'] . ' ';
            }
            //地区判断
            if ($_REQUEST['AreaID']!="") {
                $map['AreaID'] = end($_REQUEST['AreaID']);
                $data .= ' AND sc.AreaID = ' . end($_REQUEST['AreaID']). ' ';
            }
            if ($_REQUEST['Status']!="") {//成员的状态
                $map['Status'] = $_REQUEST['Status'];
                $data .= ' AND cu.Status = ' . $_REQUEST['Status'] . ' ';
            }
        }
        $perpage=C('WLN_PERPAGE');
        $baseObj = D('Base');
        $count = $baseObj->unionSelect('classUserCount',$data);//统计总数
        $page=page($count,$_GET['p'],$perpage).','.$perpage;//获取分页条件
        $list = $baseObj->unionSelect('classUserPage',$data,$page);//分页内容信息
        //调取函数执行分页
        $this->pageList($count, $perpage, $map);

        $gradeArray = SS('gradeListSubject'); //获取年级数据集
        $param['style']='area';
        $param['pID']=0;
        $arrArea=$this->getData($param);
        $this->assign('gradeArray', $gradeArray); //年级数据集
        $this->assign('areaArray',$arrArea);//地区
        $this->assign('list',$list);
        $this->assign('classID',$_GET['ClassID']);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 按条件删除；
     * @author demo
     */
    public function delete(){
        $UserID=$_POST['id'];
        $useridClassId=explode(',',$UserID);
        foreach($useridClassId as $i=>$iUseridClassId){//批量删除整理用户ID
            $new=explode("_",$iUseridClassId);
            $map=$new[2];
            $classId[]=$new[1];
            $cuidId[]=$new[0];
        }
        
        $endClassId=implode(',',array_unique($classId));//获取班级ID classId
        $where="ClassID in (".$endClassId.")";
        $classCreator=D('ClassList')->selectData(
            'Creator',
            $where);//获取班级创建者
        foreach($classCreator as $i=>$iClassCreator){//整合所有班级创建者
            $newCreator[]=$iClassCreator['Creator'];
        }
        foreach($cuidId as $i=>$iCuidId){//循环只调取一条用户数据进行比对
            $userArr = D('Base')->unionSelect('classUserByID',$iCuidId);
            if(!in_array($userArr['username'],$newCreator)){
                $canDel[]=$iCuidId;//把能够删除的id放入数组
            }else{
                $canNotDel[]=$iCuidId;//把不能删除的放入数组，做提示使用！
            }
        }
        $delId=implode(',',$canDel);
        $notDelId=implode(',',$canNotDel);
        //获取数据标识
        if(!$delId){
            $this->setError('1X3912','',U('ClassUser/Index',array('ClassID'=>$endClassId))); //选中的为班级创建者，请先解散班级！
        }
        if(D('ClassUser')->deleteData(
                'CUID in ('.$delId.')')===false){//用户数据删除
            $this->setError('30302'); //删除班级成员失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除ID为【'.$delId.'】的数据');
            if(empty($notdelid)){//判断执行是否成功！
                $this->showSuccess('删除班级成员成功！',U('ClassUser/Index',array('ClassID'=>$endClassId)));
            }else{
                $this->showSuccess('删除班级部分成员成功！编号为：'.$notDelId.'的成员为班级创建者，请先解散班级！',U('ClassUser/Index',array('ClassID'=>$endClassId)));
            }
        }
    }
    /**
     * 调取信息编辑页面；
     * @author demo
     */
    public function edit(){
        $pageName = '班级成员修改';
        if(empty($_GET['id'])){
            $this->setError('30301'); //未能获取到班级参数！
        }
        $idArr=explode('_',$_GET['id']);//切割参数 1 cuid 2 班级id
        $cuID=$idArr[0];
        $classID=$idArr[1];
        $where='CUID='.$cuID;
        $subjectID=D('ClassUser')->selectData(
            'SubjectID,UserID,ClassID',
            $where
        );//获取学科SubjectID
        $userArr = $this->getModel('User')->selectData(
            'UserName,Whois',
            'UserID='.$subjectID[0]['UserID']
        );
        $whoIs=$userArr[0]['Whois'];//获取是否是老师标识
        if($whoIs!=1){
            $this->setError('1X3913','',__URL__); //编辑成员信息失败！（不能对学生修改）！
        }

        $gradeArray=SS('grade');
        $classArr=D('ClassList')->selectData(
            'GradeID,ClassName',
            'ClassID='.$subjectID[0]['ClassID']
        );
        $gradeArr=$gradeArray[$classArr[0]['GradeID']];
        $subjectArray=SS('subject');
        $str=SS('subjectParentId');
        $subArr=$str[$gradeArr['SubjectID']]['sub'];
        if($subjectID[0]['SubjectID']){
            $subjectSelfArr=$subjectArray[$subjectID[0]['SubjectID']];//获取老师本身所在学科，作为显示
            $subjectSelfArr['SubjectName']=$subjectSelfArr['ParentName'].$subjectSelfArr['SubjectName'];
        }
        $subjectSelfArr['UserName']=$userArr[0]['UserName'];
        $subjectSelfArr['GradeName']=$gradeArr['GradeName'];
        $subjectSelfArr['ClassName']=$classArr[0]['ClassName'];

        $act = 'edit'; //模板标识
        $this->assign('cuID',$cuID);
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('subArr',$subArr);
        $this->assign('subjectSelfArr',$subjectSelfArr);
        $this->assign('ClassID', $classID); //页面标题
        $this->display();
    }
    /**
     * 按成员添加页面；
     * @author demo
     */
    public function addUser(){
        $pageName = '班级成员添加';
        if(empty($_GET['ClassID'])){
             $this->setError('30301'); //未能获取到班级ID！
        }
        $data['ClassID']=$_GET['ClassID'];
        $classMsg=D('ClassList')->selectData(
            '*',
            'ClassID='.$data['ClassID']
        );//调取班级所在年级
        $gradeID=SS('grade');
        $subjectID=$gradeID[$classMsg[0]['GradeID']]['SubjectID'];
        $subjectList=SS('subjectParentId');
        $act = 'add'; //模板标识
        $this->assign('act', $act); //模板标识
        $this->assign('subjectList', $subjectList[$subjectID]['sub']); //学科信息
        $this->assign('classMsg', $classMsg); //班级信息
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('ClassID', $data['ClassID']); //页面标题
        $this->display();
    }
    /**
     * 按条件执行添加，更新；
     * @author demo
     */
    public function save(){
        $map['ClassID']=$_POST['ClassID'];
        if($_POST['act']=="add"){
            $classTrue=D('ClassList')->selectData(
                '*',
                'ClassID='.$map['ClassID']
            );//判断班级是否还存在
            if(empty($classTrue)){
                $this->setError('1X3914'); //添加的班级已经不存在了！
            }
            if(empty($_POST['SubjectID'])){
                $_POST['SubjectID']=0;
            }
            if(!$_POST['UserName']){
                $this->setError('1X3915'); //添加的用户名不能为空！
            }
            $data['UserName']=$_POST['UserName'];
            //成员用户名验证
            $userObj = $this->getModel('User');
            $buffer = $userObj->selectData(
                '*',
                'UserName="' .$data['UserName']. '" and Status=0 '
            );
            if(!$buffer){//查找用户信息
                if(is_numeric($data['UserName'])){
                    //成员电话验证
                    $buffer = $userObj->selectData(
                        '*',
                        'Phonecode="' . $data['UserName'] . '" and Status=0 '
                    );
                }else{
                    //成员邮箱验证
                    $buffer = $userObj->selectData(
                        '*',
                        'Email="' . $data['UserName'] . '" and Status=0 '
                    );
                }
            }
            if($_POST['ifstudent'] != $buffer[0]['Whois']){
                $this->setError('1X3916','',U('ClassUser/Index',array('ClassID'=>$map['ClassID']))); //所选用户状态标识与实际用户状态不相符！请确认后重试！
            }
            if($buffer){
                //成员数据调取
                $classUserMsg=D('ClassUser')->selectData(
                    'UserID',
                    'ClassID='.$map['ClassID']
                );//调取该班级所有用户，防止反复添加用户
                if(!$classUserMsg){
                    $this->setError('1X3917','',U('ClassUser/Index',array('ClassID'=>$map['ClassID']))); //获取填写的成员的班级成员信息失败！该班级成员为空！
                }else{
                    foreach($classUserMsg as $i=>$iClassUserMsg){//整合用户ID到数组
                        $idArr[]=$iClassUserMsg['UserID'];
                    }
                    if(!in_array($buffer[0]['UserID'],$idArr)){//判断用户是否存在班级用户信息表里
                        $save['ClassID']=$map['ClassID'];
                        $save['UserID']=$buffer[0]['UserID'];
                        $save['LoadTime']=time();
                        $save['Status']=0;
                        $save['SubjectID']=$_POST['SubjectID'];
                        $goodRes=D('ClassUser')->insertData($save);//执行新增数据录入
                        if($goodRes){//判断添加是否成功
                            $this->showsuccess('添加班级成员成功！',U('ClassUser/Index',array('ClassID'=>$map['ClassID'])));
                        }else{
                            $this->setError('1X3918','',U('ClassUser/Index',array('ClassID'=>$map['ClassID'])),''); //添加班级成员失败！请联系管理员！
                        }
                    }else{
                        $this->setError('1X3919','',U('ClassUser/Index',array('ClassID'=>$map['ClassID'])),'');//填写成员已经是本班成员！请查看该班成员！
                    }
                }
            }else{
                $this->setError('1X3920','',U('ClassUser/Index',array('ClassID'=>$map['ClassID'])),''); //填写的成员信息不存在！请确认后重试！
            }
        }elseif($_POST['act'] =='edit'){//执行更新，更新指定用户信息
            if($_POST['nowSubjectID']!=$_POST['SubjectID']){
                $where="CUID=".$_POST['CUID'];
                $data['SubjectID']=$_POST['SubjectID'];
                $ok=D('ClassUser')->updateData($data,$where);//执行修改用户数据更新
                if($ok){
                    $this->showSuccess('用户信息修改成功！',__URL__);
                }else{
                    $this->setError('1X3921','',__URL__); //修改用户信息失败！出现未知错误！请联系管理员！
                }
            }else{
                $this->setError('1X3922','',__URL__); //修改老师信息失败！数据没有改变！
            }
        }
    }
    /**
     * Ajax按条件审核未通过成员；
     * @author demo
     */
    public function checkUser(){
        $result=array();
        if(empty($_POST['CUID'])){ //判断数据标传递失败
            $result[0]='false';
            $this->setBack(result); //Ajax返回错误提示
        }else{
            $data['Status']=0;
            $where="CUID=".$_POST['CUID'];
            $userMsgRes=D('ClassUser')->updateData($data,$where); //执行用户状态更新
            if($userMsgRes){ //更新操作，判断
                $result[0]='OK';
                $this->setBack($result);
            }else{
                $result[0]='false';
                $this->setBack($result);
            }
        }
    }
}
?>