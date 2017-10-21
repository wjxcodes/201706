<?php
/**
 * @author demo  
 * @date 2014年10月14日 
 * @update 2015年1月13日
 */
/**
 * 管理员权限管理类，用于管理管理员权限
 */
namespace Manage\Controller;
class PowerAdminListController extends BaseController  {
    var $moduleName = '管理员权限查看';

    /**
     * 浏览管理员权限
     * @author demo
     */
    public function index() {
        $pageName = '管理员权限查看';
        
        $powerArrTmp = array();
        $data = '';
        if($_REQUEST['PowerName']){
            //简单查询
            $powerName = $_REQUEST['PowerName'];
            $data = ' PowerName like "%'.$powerName.'%"';
        }else{
            //高级查询
            if($_REQUEST['Power']){
                $data.='PowerName like "%'.$_REQUEST['Power'].'%" ';
            }
            if($_REQUEST['PowerTag']){
                $data.='PowerTag like "%'.$_REQUEST['PowerTag'].'%" ';
            }
        }
        $powerArray = $this->getModel('PowerAdminList')->selectData(
            '*',
            $data,
            'OrderID asc,ListID asc');//获取数据集
        foreach($powerArray as $i=>$iPowerArray){
            $tagArr = explode('/',$iPowerArray['PowerTag']);
            $powerArrTmp[$tagArr[0]][$i]=$iPowerArray;
        }
        /*载入模板标签*/
        $this->assign('powerArray', $powerArrTmp); //数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 添加管理员权限；
     * @author demo
     */
    public function add(){
        $pageName = '添加管理员权限';
        $act = 'add'; //模板标识
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 编辑管理员权限；
     * @author demo
     */
    public function edit() {
        $groupId=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($groupId)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑管理员权限';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('PowerAdminList')->selectData(
            '*',
            'ListID in ('.$groupId.')');//当前数据集
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);//当前数据集
        $this->assign('pageName', $pageName);
        $this->display('PowerAdminList/add');
    }

    /**
     * 保存管理员权限；
     * @author demo
     */
    public function save(){
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
        if($_POST['OrderID']!=''){
            $data['OrderID'] = trim($_POST['OrderID']);
        }
        if($data['PowerTag'] == ''){
            $this->setError('11001'); //权限代码不能为空
        }
        if($data['PowerName'] == ''){
            $this->setError('11002'); //权限名称不能为空
        }
        $data['IfSubject'] = $_POST['IfSubject'];
        $data['IfDiff'] = $_POST['IfDiff'];
           $powerAdminList=$this->getModel('PowerAdminList');
        if($act=='add'){
            if($powerAdminList->insertData(
                    $data)===false){
                $this->setError('30310'); //添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加管理组权限【'.$_POST['PowerName'].'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        }else if($act=='edit'){
            if($powerAdminList->updateData(
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
     * 删除管理员权限；
     * @author demo
     */
    public function delete() {
        $groupId=$_POST['id'];    //获取数据标识
        if(!$groupId){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }

        if($this->getModel('PowerAdminList')->deleteData('ListID in ('.$groupId.')')===false){
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
            $this->setError('30301','1'); //模板标识不能为空！
        }
        $data=array();
        $data['PowerTag'] = trim($_POST['PowerTag']);
        $data['PowerName'] = trim($_POST['PowerName']);
        if(!empty($_POST['OrderID'])){
            $data['OrderID'] = trim($_POST['OrderID']);
        }
        if($data['PowerTag'] == ''){
            $this->setError('11001','1'); //权限代码不能为空
        }
        if($data['PowerName'] == ''){
            // exit('权限名称不能为空');
            $this->setError('11002','1'); //权限名称不能为空
        }
        $data['IfDiff'] = $_POST['IfDiff'];
        $data['IfSubject'] = $_POST['IfSubject'];
        if($this->getModel('PowerAdminList')->insertData(
                $data)===false){
            $this->setError('30310','1'); //添加失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'添加管理组权限【'.$_POST['PowerName'].'】');
            $this->setBack('success');
        }
    }

    /**
     * 权限情况检查
     * @author demo
     */
    public function check(){
        $result = $this->getModel('PowerAdminList')->selectData(
            'PowerName,PowerTag',
            '1=1');
        $result = $this->getModel('PowerCheck')->getComparesData($result,array('Manage'));
        $this->assign('result',$result['Manage']);
        $this->assign('pageName','未设置权限列表');
        $this->display();
    }
    /**
     * 删除已经不用的方法
     * @author demo
     */
    public function deleteNoUse(){
        $innerData=$this->getModel('PowerAdminList')->selectData(
            '*',
            '1=1'
        );
        $result=$this->getModel('PowerCheck')->getComparesData('',array('Manage'),'/',0);
        foreach($result['Manage'] as $i=>$iResult){
            $newResult[]=$iResult['method'];
        }
        foreach($innerData as $i=>$iInsertData){
            if(!in_array($iInsertData['PowerTag'],$newResult)){
                    $idArr[$i]['ListID']=$iInsertData['ListID'];
                    $idArr[$i]['PowerTag']=$iInsertData['PowerTag'];
            }
        }
        $this->assign('result',$idArr);
        $this->assign('pageName','可删除的权限列表 ');
        $this->display();
    }
    /**
     * 更新管理员权限缓存
     * @author demo
     */
    public function updateCache(){
        $PowerAdminList = $this->getModel('PowerAdminList');
        $PowerAdminList->setcache();
        //写入日志
        $this->adminLog('权限缓存','更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }

    /**
     * 更新模型缓存
     * @author demo 
     */
    public function upgradeModelCache(){
       S('models', D('PowerCheck')->upgradeModelCache());
       $this->showSuccess('模型缓存更新成功！',__URL__);
    }
}