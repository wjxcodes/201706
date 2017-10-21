<?php
/**
 * @author demo
 * @date 2014年8月7日
 */
/**
 *班级管理控制器类，用于处理班级管理相关操作
 */
namespace Work\Manage;
class ClassListManage extends BaseController  {
    var $moduleName = '班级配置';
    /**
     * 按条件浏览；
     * @author demo
     */
    public function index() {
        $pageName = '班级管理';
        $data='1=1';
        if ($_REQUEST['ClassID']) {
            //简单查询
            $map['ClassID'] = '';
            $data .= ' AND OrderNum = "' . $_REQUEST['ClassID'] . '" ';
        } else {
            //高级查询
            if ($_REQUEST['ClassNum']) {
                if(is_numeric($_REQUEST['ClassNum'])){
                    $map['ClassNum'] = $_REQUEST['ClassNum'];
                    $data .= ' AND cl.OrderNum = "' . $_REQUEST['ClassNum'] . '" ';
                }else{
                    $this->setError('30502');
                }
            }
            if ($_REQUEST['UserName']) {
                $map['UserName'] = $_REQUEST['UserName'];
                $data .= ' AND cl.Creator like "%' . $_REQUEST['UserName'] . '%" ';
            }
            if ($_REQUEST['GradeID']) {
                $map['GradeID'] = $_REQUEST['GradeID'];
                $data .= ' AND cl.GradeID = ' . $_REQUEST['GradeID'] . ' ';
            }
            if (end($_REQUEST['AreaID'])!="") {
                $map['AreaID'] = end($_REQUEST['AreaID']);
                $data .= ' AND s.AreaID = ' . end($_REQUEST['AreaID']) . ' ';
            }
        }
        $perpage = C('WLN_PERPAGE');
        $baseObj = D('Base');
        $count = $baseObj->unionSelect('classListCount',$data);
        //多表查询需要学校的地区AreaID
        $page=page($count,$_GET['p'],$perpage).','.$perpage;
        $list = $baseObj->unionSelect('classListPage',$data,$page);
        //执行分页
        $this->pageList($count, $perpage, $map);
        $gradeArray = SS('gradeListSubject'); //获取年级数据集
        //获取地区
        $param['style']='area';
        $param['pID']=0;
        $arrArea=$this->getData($param);
        /*载入模板标签*/
        $this->assign('gradeArray', $gradeArray); //年级数据集
        $this->assign('arrArea',$arrArea);//地区
        $this->assign('list', $list); //班级数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 按调取添加页面；
     * @author demo
     */
    public function add() {
        $pageName = '添加班级';
        $act = 'add'; //模板标识
        $gradeArray = SS('gradeListSubject'); //获取年级数据集
        //获取地区
        $param['style']='area';
        $param['pID']=0;
        $areaArray=$this->getData($param);

        //*载入模板标签*
        $this->assign('act', $act); //模板标识
        $this->assign('arrArea',$areaArray);//地区
        $this->assign('gradeArray', $gradeArray); //父类数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 按调取编辑页面；
     * @author demo
     */
    public function edit() {
        $classID=$_GET['id'];//获取数据标识
        //判断数据标识
        if(empty($classID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑班级';
        $act = 'edit'; //模板标识
        $edit = D('ClassList')->selectData(
            '*',
            "ClassID=".$classID,
            '',
            '1'
        );//获取内容
        $userMsg = $this->getModel('User')->selectData(
            'UserID',
            'UserName="'.$edit[0]['Creator'].'"'
        );
        $subjectID=D('ClassUser')->selectData(
            '*',
            "ClassID=".$classID.' and UserID='.$userMsg[0]['UserID']
        );
        $subject=SS('subject');
        $gradeRes=SS('grade'); //获取年级
        $edit[0]['SubjectName']=$subject[$subjectID[0]['SubjectID']]['ParentName'].$subject[$subjectID[0]['SubjectID']]['SubjectName'];
        $edit[0]['GradeName']=$gradeRes[$edit[0]['GradeID']]['GradeName'];
        $edit[0]['ClassName']=mb_substr($edit[0]['ClassName'],0,mb_strlen($edit[0]['ClassName'],'UTF-8')-mb_strlen('班','UTF-8'),'UTF-8');//去除"班"
        //*载入模板标签*
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display('ClassList/edit');
    }
    /**
     * 按条件添加或更新班级数据；
     * @author demo
     */
    public function save() {
        $classID=$_POST['ClassID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if(empty($classID) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        $classList = D('ClassList');
        $orderNum=$classList->getMaxNum();
        //生成学生初始密码
        $pwd=rand(100001,999999);
        //处理学校全称
        $areaID=$_POST['AreaID'];
        if(!is_array($areaID)) $areaID=array($areaID);
        $areaParent=SS('areaParentPath');
        $area=SS('areaList');
        $areaName=$areaParent[$areaID[count($areaID)-1]];
        $lastAreaName=$area[$areaID[count($areaID)-1]]['AreaName'];
        $fullName='';
        foreach($areaName as $iAreaName){
            $fullName.=$iAreaName['AreaName'];
        }
        $fullName.=$lastAreaName.$_POST['FullName'];
        $data=array();
        $allName=$fullName;
        $schoolMsg=array();
        if(!empty($_POST['SchoolName'])){//如果有自定义学校名称
            $schoolMsg['SchoolName']=$_POST['SchoolName'];
            if(empty($_POST['areaid_direct'])){
                $schoolMsg['AreaID']=$_POST['areaid_city'];
            }else{
                $schoolMsg['AreaID']=$_POST['areaid_direct'];
            }
            $schoolMsg['OrderID']='99';
            $schoolMsg['SchoolAddress']='自定义';
            $schoolMsg['Content']='自定义';
            $schoolMsg['Prelist']='自定义';
            $schoolMsg['Status']=0;
            $schoolID = $this->getModel('School')->insertData(
                $schoolMsg
            );//执行自定义学校录入
            if($schoolID){
                $data['SchoolID']=$schoolID;
            }else{
                $this->setError('1X3910','',__URL__,''); //自定义学校添加失败！请确认后，重新添加！
            }
        }else{
            $data['SchoolID']=$_POST['areaid_school'];
        }
    
        $data['SchoolFullName']=$allName;
        $data['OrderNum']=$orderNum;
        $data['ClassPwd']=$pwd;
        $data['LoadTime']=time();
        $data['ClassName']=$_POST['ClassName'].'班';
        $data['Creator']=$_POST['Creator'];
        $id=explode('_',$_POST['GradeID']);
        $data['GradeID']=$id[0];
        if($act=='add'){
            //通过用户名判断用户是否合法
            $userObj = $this->getModel('User');
            $buffer = $userObj->selectData(
                '*',
                'UserName="' .$data['Creator']. '" and Status=0 and Whois=1'
            );
            if(!$buffer){
                if(is_numeric($data['Creator'])){
                    //通过手机号判断是否合法
                    $buffer = $userObj->selectData(
                        '*',
                        'Phonecode="' . $data['Creator'] . '" and Status=0 and Whois=1'
                    );
                }else{
                    //通过邮箱判断用户是否合法
                    $buffer = $userObj->selectData(
                        '*',
                        'Email="' . $data['Creator'] . '" and Status=0 and Whois=1'
                    );
                }
            }
            if($buffer){//判断是不是老师
                $data['Creator']=$buffer[0]['UserName'];
                $classID=D('ClassList')->insertData($data);//添加班级成功
                    if($classID===false){
                        $this->setError('30310'); //添加班级失败！
                    }else{
                        //添加老师信息到班级成员表
                        $userData['UserID']=$buffer[0]['UserID'];
                        $userData['Status']=0;
                        $userData['SubjectID']=$_POST['SubjectID'];
                        $userData['LoadTime']=time();
                        $userData['ClassID']=$classID;
                        D('ClassUser')->insertData($userData);//执行数据录入
                        $this->adminLog($this->moduleName,'添加班级【'.$userData['ClassName'].'】');
                        $this->showSuccess('添加班级成功！',__URL__);
                    }
            }else{
                $this->setError('1X3911','',__URL__); //班级添加失败！分配班级所属老师不存在！请确认后，重新添加！
            }
        }else if($act=='edit'){
            $edit['ClassName']=$_POST['ClassName'];
            $edit['OrderNum']=$_POST['OrderID'];
            if(D('ClassList')->updateData($edit,'ClassID='.$classID)===false){ //判断保存
                $this->setError('30311'); //修改班级失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改ClassID为【'.$edit['ClassID'].'】的数据');
                $this->showSuccess('修改班级成功！',__URL__);
            }
        }
    }
    /**
     * 按条件删除班级；
     * @author demo
     */
    public function delete(){
        $classID=$_POST['id'];//获取数据标识
        if(!$classID){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if(D('ClassList')->deleteData('ClassID in ('.$classID.')')===false){
            $this->setError('30302','',__URL__); //删除班级失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除ClassID为【'.$classID.'】的数据');
            $this->showSuccess('删除班级成功！',__URL__);
        }
    }

    /**
     * 统计班级人数，更新当前数据
     */
    public function updateClassTotal(){
        $groupID=D('ClassUser')->groupData(
            'ClassID,count(*) as total',
            '1=1',
            'ClassID'
        );
        foreach($groupID as $i=>$iGroupID){
            $updateRes=D('ClassList')->updateData(
                'StudentCount='.$iGroupID['total'],
                'ClassID='.$iGroupID['ClassID']
            );
            if($updateRes==false){
                $result[]=$iGroupID['ClassID'];
            }
        }
        if(!empty($result)){
            $this->showSuccess('班级人数更新成功！',__URL__);
        }
    }
}