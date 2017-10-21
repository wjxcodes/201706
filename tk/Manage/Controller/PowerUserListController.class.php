<?php
/**
 * @author demo 
 * @Date: 2014年10月14日
 * @last updated date 2014年11月3日
 */
/**
 * 用户权限管理类，用于管理用户权限
 */
namespace Manage\Controller;
class PowerUserListController extends BaseController  {
    var $moduleName = '权限查看';
    /**
     * 用户权限列表
     * @author demo
     */
    public function index() {
        /*浏览*/
        $pageName = '用户权限查看';
        $data = '1=1';
        if($_REQUEST['PowerName']){
            //简单查询
            $powerName = $_REQUEST['PowerName'];
            $data .= ' and PowerName like "%'.$powerName.'%"';
        }else{
            //高级查询
            if($_REQUEST['Power']){
                $data.=' and PowerName like "%'.$_REQUEST['Power'].'%" ';
            }
            if($_REQUEST['PowerTag']){
                $data.=' and PowerTag like "%'.$_REQUEST['PowerTag'].'%" ';
            }
        }
        $powerArray = $this->getModel('PowerUserList')->selectData(
            '*',
            $data,
            'OrderID asc,ListID asc');//获取数据集
        $powerArrTmp = array();
        $group=$this->getModel('PowerUser')->getGroupName();
        foreach($powerArray as $i=>$iPowerArray){
            $tagArr = explode('/',$iPowerArray['PowerTag']);
            $powerArrTmp[$tagArr[0]][$i]=$iPowerArray;
            $powerArrTmp[$tagArr[0]][$i]['GroupName'] = $group[$iPowerArray['GroupName']];
        }
        /*载入模板标签*/
        $this->assign('powerArray', $powerArrTmp); //数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('powerCycle', \Manage\Model\PowerUserListModel::$powerCycle);
        $this->display();
    }

    /**
     * 添加用户权限
     * @author demo
     */
    public function add(){
        $pageName = '添加用户权限';
        $act = 'add'; //模板标识
        $powerUser = $this->getModel('PowerUser');
        $powerArray=$powerUser->selectData(
            'UserGroup,PUID',
            '1=1');
        $group=$powerUser->getGroupName();
        $userGroupArr = '';
        foreach($group as $i=>$iGroup){
            $userGroupArr[$i]['UserGroupName'] = $iGroup;
            $userGroupArr[$i]['GroupName'] = $i;
        }
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('userGroup',$userGroupArr);
        $this->assign('powerArray',$powerArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('powerCycle', \Manage\Model\PowerUserListModel::$powerCycle);
        $this->display();
    }

    /**
     * 编辑用户权限
     * @author demo
     */
    public function edit() {
        $groupId=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($groupId)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑用户权限';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('PowerUserList')->selectData(
            '*',
            'ListID in ('.$groupId.')');//当前数据集
        $group=$this->getModel('PowerUser')->getGroupName();
        foreach($group as $i=>$iGroup){
            $userGroupArr[$i]['UserGroupName'] = $iGroup;
            $userGroupArr[$i]['GroupName'] = $i;
        }

        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);//当前数据集
        $this->assign('userGroup',$userGroupArr);
        $this->assign('pageName', $pageName);
        $this->assign('powerCycle', \Manage\Model\PowerUserListModel::$powerCycle);
        $this->display('PowerUserList/add');
    }

    /**
     * 保存用户权限；
     * @author demo
     */
    public function save() {
        $groupId=$_POST['ListID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($groupId) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        $data=array();
        $data['PowerTag'] = trim($_POST['PowerTag']);
        $data['PowerName'] = trim($_POST['PowerName']);
        $data['Value'] = trim($_POST['Value']);
        $data['GroupName'] = $_POST['GroupName'];
        $data['Unit'] = $_POST['Unit'];
        $data['TypeName'] = $_POST['TypeName'];
        if($_POST['OrderID']!=''){
            $data['OrderID'] = trim($_POST['OrderID']);
        }
        if($data['PowerTag'] == ''){
            $this->setError('11001'); //权限代码不能为空
        }
        if($data['PowerName'] == ''){
            $this->setError('11002'); //权限名称不能为空
        }
        if($data['Value'] == ''){
            $this->setError('11003'); //限制次数不能为空
        }
        // if('add' == $act && $this->getModel('PowerUserList')->verifyTag($data['PowerTag'], $data['Value'], $data['GroupName'])){
        //     $this->setError('10128');
        // }
           $powerUserList=$this->getModel('PowerUserList');
        if($act=='add'){
            if($powerUserList->insertData(
                    $data)===false){
                $this->setError('30310'); //添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加用户组权限【'.$_POST['PowerName'].'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        }else if($act=='edit'){
            if($powerUserList->updateData(
                    $data,
                    'ListID='.$groupId)===false){
                 $this->setError('30311'); //修改失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改ListID为【'.$groupId.'】的数据');
                $this->showSuccess('修改成功！',__URL__);
            }
        }
    }

    /**
     * 删除用户权限；
     * @author demo
     */
    public function delete() {
        $groupId=$_POST['id'];    //获取数据标识
        if(!$groupId){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if($this->getModel('PowerUserList')->deleteData(
                'ListID in ('.$groupId.')')===false){
            $this->setError('30302'); //删除失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除ListID为【'.$groupId.'】的数据');
            $this->showSuccess('删除成功！',__URL__);
        }
    }

    /**
     * ajax保存管理员权限；用于权限分配情况使用
     * @author demo
     */
    public function ajaxSave() {
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($act)){
            $this->setError('30301','1'); //模板标识不能为空
        }
        $data=array();
        $data['PowerTag'] = trim($_POST['PowerTag']);
        $data['PowerName'] = trim($_POST['PowerName']);
        $data['Value'] = trim($_POST['Value']);
        $data['GroupName'] = $_POST['GroupName'];
        $data['TypeName'] = $_POST['TypeName'];
        if(!empty($_POST['OrderID'])){
            $data['OrderID'] = trim($_POST['OrderID']);
        }
        if($data['PowerTag'] == ''){
            $this->setError('11001','1'); //权限代码不能为空
        }
        if($data['PowerName'] == ''){
            $this->setError('11002','1'); //权限名称不能为空
        }
        if($data['Value'] == ''){
            $this->setError('11003','1'); //限制次数不能为空
        }
        if($this->getModel('PowerUserList')->insertData(
                $data)===false){
            $this->setError('30310','1'); //添加失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'添加用户组权限【'.$_POST['PowerName'].'】');
            $this->setBack('success');
        }
    }

    /**
     * 权限情况检查
     * @author demo
    */
    public function check(){
        $result = $this->getModel('PowerUserList')->selectData(
            'PowerName,PowerTag',
            '1=1');
        $paths = glob(APP_PATH.'*', GLOB_ONLYDIR);
        $filter = array('Manage', 'Runtime');
        foreach($paths as $key=>$value){
            $value = str_replace(APP_PATH, '', $value);
            if(in_array($value, $filter)){
                unset($paths[$key]);
            }else{
                $paths[$key] = $value;
            }
        }
        $result = $this->getModel('PowerCheck')->getComparesData($result,$paths);
        $group=$this->getModel('PowerUser')->getGroupName();
        $userGroupArr = '';
        foreach($group as $i=>$iGroup){
            $userGroupArr[$i]['UserGroupName'] = $iGroup;
            $userGroupArr[$i]['GroupName'] = $i;
        }
        foreach($result as $key=>$value){
            foreach($value as $k=>$v){
                $result[$key][$k]['group'] = $key;
            }
        }
        $this->assign('list', $result);
        $this->assign('userGroup',$userGroupArr);
        $this->assign('pageName','未设置权限列表');
        $this->display();
    }
    /**
     * 更新权限缓存
     * @author demo
     */
    public function updateCache(){
        $powerUserList = $this->getModel('PowerUserList');
        $powerUserList->setCache();
        //写入日志
        $this->adminLog('权限缓存','更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }
}