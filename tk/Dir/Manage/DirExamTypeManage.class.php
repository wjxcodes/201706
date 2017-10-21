<?php
/**
 * @author demo
 * @date 2014年9月7日
 */
/**
 * 考试类型管理管理控制器类
 */
namespace Dir\Manage;
class DirExamTypeManage extends BaseController  {
    var $moduleName = '考试类型';
    /**
     * 浏览考试类型
     * @author demo
     */
    public function index() {
        $pageName = '考试类型';
        $map = array();
        $data = ' 1=1 ';
        if($_REQUEST['name']){
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND TypeName like "%'.$_REQUEST['name'].'%" ';
        }
        $list = $this->getModel('DirExamType')->selectData(
            '*',
            $data,
            'OrderID asc,TypeID asc'); //获取数据集
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加考试类型
     * @author demo
     */
    public function add() {
        $pageName = '添加考试类型';
        $act = 'add'; //模板标识
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑考试类型
     * @author demo
     */
    public function edit() {
        $typeID = $_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($typeID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑考试类型';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('DirExamType')->selectData(
            '*',
            'TypeID='.$typeID,
            '',
            1);
    
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display('DirExamType/add');
    }
    /**
     * 保存考试类型
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
        $data['DefaultStyle'] = intval($_POST['defaultStyle']);
        $data['OrderID'] = $_POST['orderID'];
        $dirExamType=$this->getModel('DirExamType');
        if($act == 'add'){
            if($dirExamType->insertData(
                    $data) === false){
                $this->setError('30310');
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加考试类型【'.$_POST['typeName'].'】');
                $this->showSuccess('添加考试类型成功！',__URL__);
            }
        }else if($act == 'edit'){
            if($dirExamType->updateData(
                    $data,
                    'TypeID='.$typeID) === false){
                $this->setError('30311');
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改TypeID为【'.$_POST['typeID'].'】的数据');
                $this->showSuccess('修改考试类型成功！',__URL__);
            }
        }
    }
    /**
     * 删除考试类型
     * @author demo
     */
    public function delete(){
        $typeID = $_POST['id']; //获取数据标识
        if(!$typeID){
            $this->setError('30301','',__URL__);
        }
        if($this->getModel('DirExamType')->deleteData(
                'TypeID in ('.$typeID.')')===false){
            $this->setError('30302','',__URL__);
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除TypeID为【'.$typeID.'】的数据');
            $this->showSuccess('删除考试类型成功！',__URL__);
        }
    }
    /**
     * 更新缓存
     * @author demo
     */
    public function updateCache(){
        $examType = $this->getModel('DirExamType');
        $examType->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }
}