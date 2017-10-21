<?php
/**
 * @author demo 
 * @Date: 2014年10月14日
 */
/**
 * 管理员组配置类，用于配置管理员组
 */
namespace Manage\Controller;
class PowerAdminController extends BaseController  {
    var $moduleName='管理员组配置';
    
    /**
     * 管理组列表
     * @author demo
     */
    public function index() {
        $pageName = '管理员组配置';
        $adminGroupArray = $this->getModel('PowerAdmin')->selectData(
            '*',
            '1=1',
            'PUID asc'); //获取数据集
           $powerAdminList=$this->getModel('PowerAdminList');
        foreach($adminGroupArray as $i=>$iAdminGroupArray){
            if(strpos($iAdminGroupArray['ListID'],',')){
                $ListName = array();
                $tmpArray = explode(',',$iAdminGroupArray['ListID']);
                foreach($tmpArray as $j=>$jTmpArray){
                    $where = 'ListID='.$jTmpArray;
                    $powerTmpArr = $powerAdminList->selectData(
                        'PowerName,PowerTag',
                        $where);
                    if(!$powerTmpArr){
                        continue;
                    }
                    $ListName[$j] = $powerTmpArr[0]['PowerName']."(".$powerTmpArr[0]['PowerTag'].")";
                }
                $adminGroupArray[$i]['groupList'] = join(',',$ListName);
            }else{
                if($iAdminGroupArray['ListID']=='all'){
                    $idList = $powerAdminList->selectData(
                        'ListID',
                        '1=1');
                    foreach($idList as $j=>$jIdList){
                        $iDataTmp[$j] = $jIdList['ListID'];
                    }
                    $adminGroupArray[$i]['ListID'] = join(',',$iDataTmp[$j]);
                    $adminGroupArray[$i]['groupList'] = '全部(all)';
                }else{
                    $where = "ListID='".$iAdminGroupArray['ListID']."'";
                    $powerTmpArr = $powerAdminList->selectData(
                        'PowerName,PowerTag',
                        $where);
                    $ListName[$i] = $powerTmpArr[0]['PowerName']."(".$powerTmpArr[0]['PowerTag'].")";
                    $adminGroupArray[$i]['groupList'] = $ListName[$i];
                }
            }
        }
        /*载入模板标签*/
        $this->assign('adminGroupArray', $adminGroupArray); //数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 添加管理员组
     * @author demo
     */
    public function add(){
        $pageName = '添加管理员组';
        $act = 'add'; //模板标识
        $powerArrayTmp = $this->getModel('PowerAdminList')->selectData(
            '*',
            '1=1',
            'OrderID asc,ListID asc'); //权限数据集
        /*载入模板标签*/
        $powerArray = array();
        $anchor = array();
        $tagArrTmp = '';
        $order = 0;
        foreach($powerArrayTmp as $i=>$iPowerArrayTmp){
            $tagArr = explode('/',$iPowerArrayTmp['PowerTag']);
            $powerArray[$tagArr[0]][$i]=$iPowerArrayTmp;
            if($tagArr[0]!= $tagArrTmp){
                $anchor[$order]['anchor'] = $iPowerArrayTmp['PowerName'].'('.$iPowerArrayTmp['PowerTag'].')';
                $anchor[$order]['ListID'] = $iPowerArrayTmp['ListID'];
                $anchorOrder[$tagArr[0]] = $anchor[$order];
                $order++;
                $tagArrTmp = $tagArr[0];
            }
        }
        ksort($anchorOrder);
        $this->assign('act', $act); //模板标识
        $this->assign("anchor",$anchorOrder);//锚点数据
        $this->assign('powerArray', $powerArray); //权限数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 编辑管理组；
     * @author demo
     */
    public function edit() {
        $GroupID=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($GroupID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑组';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('PowerAdmin')->selectData(
            '*',
            'PUID='.$GroupID);//当前数据集
        $powerArrayTmp = $this->getModel('PowerAdminList')->selectData(
            '*',
            '1=1',
            'OrderID asc,ListID asc');
        if($edit[0]['ListID']=='all'){
            foreach($powerArrayTmp as $i=>$iPowerArrayTmp){
                $pDataArrTmp[$i] = $iPowerArrayTmp['ListID'];
            }
            $edit[0]['ListID']=join(',',$pDataArrTmp);
        }

        $anchor = array();
        $tagArrTmp = '';
        $order = 0;
        $powerArray = array();
        foreach($powerArrayTmp as $i=>$iPowerArrayTmp){
            $tagArr = explode('/',$iPowerArrayTmp['PowerTag']);
            $powerArray[$tagArr[0].$tagArr[1]][$i]=$iPowerArrayTmp;
            if($tagArr[0].$tagArr[1]!= $tagArrTmp){
                $anchor[$order]['anchor'] = $iPowerArrayTmp['PowerName'].'('.$iPowerArrayTmp['PowerTag'].')';
                $anchor[$order]['ListID'] = $iPowerArrayTmp['ListID'];
                $anchorOrder[$tagArr[0].$tagArr[1]] = $anchor[$order];
                $order++;
                $tagArrTmp = $tagArr[0].$tagArr[1];
            }
        }
        ksort($anchorOrder);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);//当前数据集
        $this->assign("anchor",$anchorOrder);//锚点数据
        $this->assign('powerArray', $powerArray); //权限数据集
        $this->assign('pageName', $pageName);
        $this->display('PowerAdmin/add');
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
        $powerAdminList=$this->getModel('PowerAdminList');
        $powerAdmin=$this->getModel('PowerAdmin');
        $allId = $powerAdminList->selectData(
            'ListID',
            $where);
        $data=array();
        $data['AdminGroup']=trim($_POST['AdminGroup']);
        if(!$data['AdminGroup']){
            $this->setError('11101'); //管理员组名称不能为空！
        }
        $data['IfDefault']=$_POST['IfDefault'];
        if(in_array($allId[0]['ListID'],$_POST['ListID'])){
            $data['ListID'] = 'all';
        }else{
            $data['ListID']=is_array($_POST['ListID']) ? implode(',',$_POST['ListID']) : $_POST['ListID'];
        }
        if($data['IfDefault']){
            if($powerAdmin->updateData(
                    array('IfDefault'=>'0'),
                    '1=1')===false){
                $this->setError('11102'); //修改默认用户组失败！
            }
        }
        if($act=='add'){
            if($powerAdmin->insertData(
                    $data)===false){
                $this->setError('30310'); //添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加管理员组【'.$_POST['adminGroup'].'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        }else if($act=='edit'){
            if($powerAdmin->updateData(
                    $data,
                    'PUID='.$groupId)===false){
                $this->setError('30311'); //修改失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改PUID为【'.$groupId.'】的数据');
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
            $this->setError('11103'); //删除失败，请不要删除编号为1的系统管理员组！
            exit;
        }
        if($this->getModel('PowerAdmin')->deleteData(
                'PUID in ('.$groupId.')')===false){
            $this->setError('30301'); //删除失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除PUID为【'.$groupId.'】的数据');
            $this->showSuccess('删除成功！',__URL__);
        }
    }
    /**
     * 更新缓存
     * @author demo
     */
    public function updateCache(){
        $powerAdmin=$this->getModel('PowerAdmin');
        $powerAdmin->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showsuccess('更新成功',__URL__);
    }
    /**
     * 我的权限
     * @author demo
     * @date 2014年10月23日
     */
    public function myPower(){
        $pageName="我的权限";
        $adminName=$this->getCookieUserName();
        $buffer=$this->getModel('Admin')->selectData(
            '*',
            'AdminName="'.$adminName.'" and Status=0',
            '1');
        //管理员权限
        $powerAdminList=SS('powerAdmin');
        $powerList=$powerAdminList[$buffer[0]['GroupID']]['sub'];
        if($powerAdminList[$buffer[0]['GroupID']]['ListID'] == 'all'){
            $powerStr='true';//判断是否是超级管理员
            $this->assign('powerStr', $powerStr); //权限集
        }else{
            /*载入模板标签*/
            $this->assign('powerList', $powerList);//权限数据集
        }
        $this->assign('pageName', $pageName); //模板标识
        $this->display('PowerAdmin/mypower');
    }
}