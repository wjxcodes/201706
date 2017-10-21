<?php
/**
 * @author demo 
 * @date
 * @update 2015年1月16日
 */
/**
 * 文档标签管理类，用于文档标签的相关操作
 */
namespace Test\Manage;
class TestTagManage extends BaseController  {
    var $moduleName = '文档标签';
    /**
     * 浏览文档标签列表
     * @author demo
     */
    public function index() {
        $pageName = '文档标签管理';
        $testTag = $this->getModel('TestTag');
        $tagArray = $testTag->selectData(
            '*',
            '1=1',
            'OrderID asc');//获取数据集

        /*载入模板标签*/
        $this->assign('tagArray', $tagArray); //文档标签数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加文档标签
     * @author demo
     */
    public function add() {
        $pageName = '添加文档标签';
        $act = 'add'; //模板标识
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('testfield', $this->getModel('Test')->getTestField()); //标签对应数据库数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑文档标签
     * @author demo
     */
    public function edit() {
        $tagID = $_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($tagID)){
            $this->setError('30301');//数据标识不能为空！
        }
        $pageName = '编辑文档标签';
        $act = 'edit'; //模板标识
        $testTag = $this->getModel('TestTag');
        $edit = $testTag->selectData(
            '*',
            'TagID='.$tagID,
            '',
            '1');
        
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('testfield', $this->getModel('Test')->getTestField()); //标签对应数据库数据集
        $this->assign('pageName', $pageName);
        $this->display('TestTag/add');
    }

    /**
     * 保存文档标签
     * @author demo
     */
    public function save() {
        $tagID = $_POST['TagID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($tagID) && $act == 'edit'){
            $this->setError('30301');//数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223');//模板标识不能为空！
        }
        $data = array();
        $data['TagName'] = $_POST['TagName'];
        $data['DefaultStart'] = $_POST['DefaultStart'];
        $data['Description'] = $_POST['Description'];
        $data['TestField'] = $_POST['TestField'];
        $data['OrderID'] = $_POST['OrderID'];
        if($act == 'add'){
            if($this->getModel('TestTag')->insertData(
                    $data) === false){
                $this->setError('30310');//添加文档标签失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加文档标签【'.$_POST['TagName'].'】');
                $this->showSuccess('添加文档标签成功！',__URL__);
            }
        
        }else if($act == 'edit'){
            if($this->getModel('TestTag')->updateData(
                    $data,
                    'TagID='.$_POST['TagID']) === false){
                $this->setError('30311');//修改文档标签失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改TagID为【'.$_POST['TagID'].'】的数据');
                $this->showSuccess('修改文档标签成功！',__URL__);
            }
        }
    }
    /**
     * 删除文档标签
     * @author demo
     */
    public function delete(){
        $tagID = $_POST['id'];//获取数据标识
        if(!$tagID){
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        if($this->getModel('TestTag')->deleteData(
                'TagID in ('.$tagID.')') === false){
            $this->setError('30302');//删除文档标签失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除TagID为【'.$tagID.'】的数据');
            $this->showSuccess('删除文档标签成功！',__URL__);
        }
    }
}