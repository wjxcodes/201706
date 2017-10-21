<?php
/**
 * @author demo
 * @date 2017年3月20日
 */
/**
 * 在线联考类型管理管理控制器类
 */
namespace Manage\Controller;
class EOnlineTypeController extends BaseController{
    var $moduleName = '在线联考类型';
    /**
     * 浏览在线联考类型
     * @author demo
     */
    public function index() {
        $pageName = '在线联考类型';
        $map = array();
        $data = ' 1=1 ';
        if($_REQUEST['name']){
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND TypeName like "%'.$_REQUEST['name'].'%" ';
        }
        $list = $this->getModel('ExamType')->selectData(
            '*',
            $data,
            'TypeID desc'); //获取数据集
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加在线联考类型
     * @author demo
     */
    public function add() {
        $pageName = '添加在线联考类型';
        $act = 'add'; //模板标识
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑在线联考类型
     * @author demo
     */
    public function edit() {
        $typeID = $_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($typeID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑在线联考类型';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('ExamType')->selectData(
            '*',
            'TypeID='.$typeID,
            '',
            1);

        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display('EOnlineType/add');
    }
    /**
     * 保存在线联考类型
     * @author demo
     */
    public function save() {
        $typeID = $_POST['typeID']; //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($typeID) && $act == 'edit'){
            $this->setError('30301');
        }
        if(empty($act)){
            $this->setError('30223');
        }
        $data = array();
        $data['TypeName'] = $_POST['typeName'];
        $dirExamType=$this->getModel('ExamType');
        if($act == 'add'){
            if($dirExamType->insertData(
                    $data) === false){
                $this->setError('30310');
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加在线联考类型【'.$_POST['typeName'].'】');
                $this->showSuccess('添加在线联考类型成功！',__URL__);
            }
        }else if($act == 'edit'){
            if($dirExamType->updateData(
                    $data,
                    'TypeID='.$typeID) === false){
                $this->setError('30311');
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改TypeID为【'.$_POST['typeID'].'】的数据');
                $this->showSuccess('修改在线联考类型成功！',__URL__);
            }
        }
    }
    /**
     * 删除在线联考类型
     * @author demo
     */
    public function delete(){
        $typeID = $_POST['id']; //获取数据标识
        if(!$typeID){
            $this->setError('30301','',__URL__);
        }
        if($this->getModel('ExamType')->deleteData(
                'TypeID in ('.$typeID.')')===false){
            $this->setError('30302','',__URL__);
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除TypeID为【'.$typeID.'】的数据');
            $this->showSuccess('删除在线联考类型成功！',__URL__);
        }
    }
}