<?php
/**
 * @author demo
 * @date 2015年8月15日
 */
/**
 *  等级属性属性配置类，用于等级属性
 */
namespace User\Manage;
class UserLevelManage extends BaseController  {
    var $moduleName = '等级属性配置';
    /**
     * 等级属性属性列表浏览
     * @author demo
     */
    public function index() {
        $pageName = '等级属性管理';
        $LevelArray = $this->getModel('UserLevel')->selectData(
            '*',
            '1=1',
            'LevelID desc'); //获取等级数据集

        /*载入模板标签*/
        $this->assign('typeArray', $LevelArray); //等级属性数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 添加等级属性
     * @author demo
     */
    public function add() {
        $pageName = '添加等级属性';
        $act = 'add'; //模板标识
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 编辑等级属性
     * @author demo
     */
    public function edit() {
        $levelID=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($levelID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑等级属性';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('UserLevel')->selectData(
            '*',
            'LevelID='.$levelID,
            '',
            1);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display('UserLevel/add');
    }

    /**
     * 保存等级属性
     * @author demo
     */
    public function save() {
        $levelID=$_POST['LevelID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($levelID) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        $data=array();
        $data['LevelName']=$_POST['LevelName'];
        $data['LevelExpMin']=$_POST['LevelExpMin'];
        $data['LevelExpMax']=$_POST['LevelExpMax'];
        if($act=='add'){
            if($this->getModel('UserLevel')->insertData($data)===false){
                $this->setError('30310'); //添加等级属性失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加等级属性【'.$_POST['LevelName'].'】');
                $this->showSuccess('添加等级属性成功！',__URL__);
            }
        }else if($act=='edit'){
            if($this->getModel('UserLevel')->updateData(
                    $data,
                    'LevelID='.$levelID)===false){
                $this->setError('30311'); //修改属性失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改LevelID为【'.$_POST['LevelID'].'】的数据');
                $this->showSuccess('修改等级成功！',__URL__);
            }
        }
    }

    /**
     * 删除等级属性
     * @author demo
     */
    public function delete(){
        $levelID=$_POST['id'];    //获取数据标识
        if(!$levelID){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if($this->getModel('UserLevel')->deleteData(
                'LevelID in('.$levelID.')')===false){
            $this->setError('30302','',__URL__); //删除等级属性失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除TypeID为【'.$levelID.'】的数据');
            $this->showSuccess('删除等级属性成功！',__URL__);
        }
    }

    /**
     * 删除等级权限属性
     * @author demo
     */
    public function delThisLevelValue(){
        $valueID=$_REQUEST['id'];    //获取数据标识
        if(!$valueID){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if($this->getModel('UserLevelValue')->deleteData(
                'ValueID in('.$valueID.')')===false){
            $this->setError('30302','',__URL__); //删除等级权限属性失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除valueID为【'.$valueID.'】的数据');
            $this->showSuccess('删除等级权限属性成功！',__URL__);
        }
    }

    /**
     * 添加等级属性权限
     * @author demo
     */
    public function addUserLevelValue() {
        $levelID=$_REQUEST['id'];    //获取数据标识
        if(!$levelID){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $userLevel=$this->getModel('UserLevel')->selectData('LevelName,LevelID','LevelID='.$levelID);
        $pageName = '添加等级属性';
        $act = 'add'; //模板标识
        /*载入模板标签*/
        $this->assign('edit',$userLevel[0]);
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->display('UserLevel/addUserLevelValue');
    }

    /**
     * 显示等级对应权限内容对照表
     * @author demo
     */
    public function showLevelLucre(){
        $pageName = '经验值属性管理';
        $levelList=$this->getModel('UserLevel')->levelPower();
        $this->assign('levelList',$levelList[1]);
        $this->assign('valueList',$levelList[0]);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 编辑等值对应权限值
     * @author demo
     */
    public function editThisLevelValue(){
        $valueID=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($valueID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑等级权限属性';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('UserLevelValue')->selectData(
            '*',
            'ValueID='.$valueID,
            '',
            1);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $levelList=SS('levelList');
        /*载入模板标签*/
        $this->assign('levelList', $levelList); //页面标题
        $this->display();
    }

    /**
     * 保存修改权限值
     * @author demo
     */
    public function saveThisLevelValue(){
        $valueID=$_POST['ValueID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($valueID) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        $data=array();
        $data['ValueName']=$_POST['ValueName'];
        $data['ValueDesc']=$_POST['ValueDesc'];
        $data['LevelID']=$_POST['LevelID'];
        $data['Content']=$_POST['Content'];
        $data['LevelDesc']=$_POST['LevelDesc'];
        if($act=='add'){
            if($this->getModel('UserLevelValue')->insertData($data)===false){
                $this->setError('30310'); //添加等级权限属性失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加等级权限属性【'.$_POST['ValueName'].'】');
                $this->showSuccess('添加等级权限属性成功！',__URL__);
            }
        }else if($act=='edit'){
            if($this->getModel('UserLevelValue')->updateData(
                    $data,
                    'ValueID='.$valueID)===false){
                $this->setError('30311'); //修改属性失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改TypeID为【'.$_POST['ValueID'].'】的数据');
                $this->showSuccess('修改等级权限成功！',__URL__);
            }
        }
    }

    /**
     * 查看等级对应权限
     * @author demo
     */
    public function showThisLevelMsg(){
        $levelID=$_REQUEST['id'];    //获取数据标识
        if(!$levelID){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $levelMsg=SS('levelList');
        $pageName = '【'.$levelMsg[$levelID]['LevelName'].'】等级权限属性管理';
        $valueArray = $this->getModel('UserLevelValue')->selectData(
            '*',
            'LevelID='.$levelID,
            'ValueID desc'); //获取等级权限数据集
        $levelMsg=SS('levelList');
        foreach($valueArray as $i=>$iValueArray){
            $valueArray[$i]['LevelName']=$levelMsg[$iValueArray['LevelID']]['LevelName'];
        }
        /*载入模板标签*/
        $this->assign('typeArray', $valueArray); //等级权限属性数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 更新等级属性缓存
     * @author demo
     */
    public function updateCache(){
        $data=array();
        $userLevel = $this->getModel('UserLevel');
        $userLevel->setCache();
        $userExp = $this->getModel('UserLevelValue');
        $userExp->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }
}