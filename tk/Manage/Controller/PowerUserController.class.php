<?php
/**
 * @author demo
 * @Date: 2014年10月14日
 */
/**
 * 用户管理组类，用于管理组
 */
namespace Manage\Controller;
class PowerUserController extends BaseController  {
    var $moduleName='用户组管理';
    /**
     * 用户组列表；
     * @author demo
     */
    public function index() {
        $pageName = '用户组管理';
        $powerUser=$this->getModel('PowerUser');
        $userGroupArray = $powerUser->selectData(
            '*',
            '1=1',
            'PUID asc');//获取数据集
        $group=$powerUser->getGroupName();
        $powerUserList= $this->getModel('PowerUserList');
        foreach($userGroupArray as $i=>$iUserGroupArray){
            $userGroupArray[$i]['UserGroupName'] = $group[$iUserGroupArray['GroupName']];
            if(strpos($iUserGroupArray['ListID'],',')){
                $ListName = array();
                $tmpArray = explode(',',$iUserGroupArray['ListID']);
                foreach($tmpArray as $j=>$tmpData){
                    $where = 'ListID='.$tmpData;
                    $powerTmpArr = $powerUserList->selectData(
                        'PowerName,PowerTag',
                        $where);
                    if(!$powerTmpArr){
                        continue;
                    }
                    $ListName[$j] = $powerTmpArr[0]['PowerName']."(".$powerTmpArr[0]['PowerTag'].")";
                }
                $userGroupArray[$i]['groupList'] = join(',',$ListName);
            }else{
                if($iUserGroupArray['ListID']=='all'){
                    $idList = $powerUserList->selectData(
                        'ListID');
                    foreach($idList as $iKey=>$iData){
                        $iDataTmp[$iKey] = $iData['ListID'];
                    }
                    $userGroupArray[$i]['ListID'] = join(',',$iDataTmp[$iKey]);
                    $userGroupArray[$i]['groupList'] = '全部(all)';
                }else{
                    $where = "ListID=".$iUserGroupArray['ListID'];
                    $powerTmpArr =$powerUserList->selectData(
                        'PowerName,PowerTag',
                        $where);
                    $ListName[$i] = $powerTmpArr[0]['PowerName']."(".$powerTmpArr[0]['PowerTag'].")";
                    $userGroupArray[$i]['groupList'] = $ListName[$i];
                }
            }
        }
        /*载入模板标签*/
        $this->assign('userGroupArray', $userGroupArray); //数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 添加用户组；
     * @author demo
     */
    public function add(){
        $pageName = '添加用户组';
        $act = 'add'; //模板标识
        $powerUserList=$this->getModel('PowerUserList');
        $powerArrayTmp = $powerUserList->selectData(
            '*',
            '1=1',
            'OrderID asc,ListID asc'); //权限数据集
        /*载入模板标签*/
        $tag = '';
        $powerArray = array();
        foreach($powerArrayTmp as $i=>$iPowerArray){
            $tagArr = explode('/',$iPowerArray['PowerTag']);
            $powerArray[$iPowerArray['GroupName']][$tagArr[0]][$i]=$iPowerArray;
            $powerArray[$iPowerArray['GroupName']][$tagArr[0]][$i]['PowerName'] = $iPowerArray['PowerName'];
        }
        $group=$this->getModel('PowerUser')->getGroupName();
        foreach($group as $i=>$iUserGroupArr){
            $userGroupArr[$i]['UserGroupName'] = $iUserGroupArr;
            $userGroupArr[$i]['GroupName'] = $i;
        }
        $teacherGroup = $powerUserList->getTeacherGroup();
        foreach($teacherGroup as $i=>$iTeacherGroup){
            $teacherArr[$i]['TeacherGroup'] = $iTeacherGroup;
            $teacherArr[$i]['GroupNum'] = $i;
        }
        $this->assign('act', $act); //模板标识
        $this->assign('teacherArr',$teacherArr);//教师所属组
        $this->assign('userGroup',$userGroupArr);//权限所属组
        $this->assign('powerArray', $powerArray);//权限数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 编辑用户组；
     * @author demo
     */
    public function edit() {
        $groupId=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($groupId)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑组';
        $act = 'edit'; //模板标识
        $powerUser=$this->getModel('PowerUser');
        $edit = $powerUser->selectData(
            '*',
            'PUID='.$groupId);//当前数据集
        $powerUserList=$this->getModel('PowerUserList');
        $powerArrayTmp = $powerUserList->selectData(
            '*',
            '1=1',
            'OrderID asc,ListID asc');
        if($edit[0]['ListID']=='all'){
            foreach($powerArrayTmp as $i=>$iPowerArrayTmp){
                $pDataArrTmp[$i] = $iPowerArrayTmp['ListID'];
            }
            $edit[0]['ListID']=join(',',$pDataArrTmp);
        }
        $powerArray = array();
        foreach($powerArrayTmp as $i=>$iPowerArrayTmp){
            $tagArr = explode('/',$iPowerArrayTmp['PowerTag']);
            $powerArray[$iPowerArrayTmp['GroupName']][$tagArr[0]][$i]=$iPowerArrayTmp;
            $powerArray[$iPowerArrayTmp['GroupName']][$tagArr[0]][$i]['PowerName'] = $iPowerArrayTmp['PowerName'].$iPowerArrayTmp['Value'];
        }
        $group=$powerUser->getGroupName();
        $userGroupArr = '';
        foreach($group as $i=>$iGroup){
            $userGroupArr[$i]['UserGroupName'] = $iGroup;
            $userGroupArr[$i]['GroupName'] = $i;
        }
        $teacherGroup = $powerUserList->getTeacherGroup();
        foreach($teacherGroup as $i=>$iTeacherGroup){
            $teacherArr[$i]['TeacherGroup'] = $iTeacherGroup;
            $teacherArr[$i]['GroupNum'] = $i;
        }
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('teacherArr',$teacherArr);//教师所属组
        $this->assign('edit', $edit[0]);//当前数据集
        $this->assign('userGroup',$userGroupArr);
        $this->assign('powerArray', $powerArray); //权限数据集
        $this->assign('pageName', $pageName);
        $this->display('PowerUser/add');
    }

    /**
     * 保存管理组；
     * @author demo
     */
    public function save() {
        $groupId=$_POST['PUID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($groupId) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }

        $where = 'PowerTag="all"';
           $powerUser=$this->getModel('PowerUser');
        $allId = $this->getModel('PowerUserList')->selectData(
            'ListID',
            $where);
        $data=array();
        $data['UserGroup']=trim($_POST['UserGroup']);
        if($data['UserGroup']==''){
            $this->setError('10901'); //用户组名称不能为空！
        }
        $data['GroupName'] = $_POST['GroupName'];
        $data['IfDefault'] = $_POST['IfDefault'];
        $data['OpenBuy']   = $_POST['OpenBuy'];//是否开放购买
        $data['Price']     = $_POST['Price'];//价格
        if(!empty($_POST['OrderID'])){
            $data['OrderID']=$_POST['OrderID'];
        }
        if($_POST['PowerUser']){
            $data['PowerUser'] = $_POST['PowerUser'];
        }else{
            $data['PowerUser'] = '0';
        }
        if(in_array($allId[0]['ListID'],$_POST['ListID'])){
            $data['ListID'] = 'all';
        }else{
            $data['ListID']=is_array($_POST['ListID']) ? implode(',',$_POST['ListID']) : $_POST['ListID'];
        }

        if($data['IfDefault']){
            if($powerUser->updateData(
                    array('IfDefault'=>'0'),
                    'GroupName='.$data['GroupName'])===false){
                $this->setError('10902'); //修改默认用户组失败！

            }
        }
        if($act=='add'){
            if($powerUser->insertData(
                $data)===false){
                $this->setError('30310'); //添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加用户组【'.$_POST['UserGroup'].'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        }else if($act=='edit'){
            if($powerUser->updateData(
                    $data,
                    'PUID='.$groupId)===false){
                $this->setError('30311'); //修改失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改GroupID为【'.$groupId.'】的数据');
                $this->showSuccess('修改成功！',__URL__);
            }
        }
    }
    /**
     * 删除管理组；
     * @author demo
     */
    public function delete() {
        $groupId=$_POST['id'];    //获取数据标识
        if(!$groupId){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $idArray=explode(',',$groupId);
        if(in_array(1,$idArray)){
            $this->setError('10903','',__URL__); //删除失败，请不要删除编号为1的系统管理员组！
            exit;
        }
        if($this->getModel('PowerUser')->deleteData(
                'PUID in ('.$groupId.')')===false){
            $this->setError('30302'); //删除失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除GroupID为【'.$groupId.'】的数据');
            $this->showSuccess('删除成功！',__URL__);
        }
    }
    /**
     * 更新缓存
     * @author demo
     */
    public function updateCache(){
        $powerUser=$this->getModel('PowerUser');
        $powerUser->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showsuccess('更新成功',__URL__);
    }
}