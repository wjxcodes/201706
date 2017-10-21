<?php
/**
 * @author demo
 * @date 2015年5月11日
 */
/**
 * 知识文档标签管理类，用于知识文档标签的相关操作
 */
namespace Guide\Manage;
class CaseLoreDocLabelManage extends BaseController  {
    var $moduleName = '知识文档标签';
    /**
     * 浏览知识文档标签列表
     * @author demo
     */
    public function index() {
        $pageName = '知识文档标签管理';
        $tagArray = $this->getModel('CaseLoreDocLabel')->selectData(
            '*',
            '1=1',
            'OrderID asc');//获取数据集

        /*载入模板标签*/
        $this->assign('tagArray', $tagArray); //文档标签数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加知识文档标签
     * @author demo
     */
    public function add() {
        $pageName = '添加知识文档标签';
        $act = 'add'; //模板标识
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('loreField', $this->getModel('CaseLoreDoc')->getTestField()); //标签对应数据库数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑知识文档标签
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
        $edit = $this->getModel('CaseLoreDocLabel')->selectData(
            '*',
            'LabelID='.$tagID);
        
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('loreField', $this->getModel('CaseLoreDoc')->getTestField()); //标签对应数据库数据集
        $this->assign('pageName', $pageName);
        $this->display('CaseLoreDocLabel/add');
    }

    /**
     * 保存知识文档标签
     * @author demo
     */
    public function save() {
        $tagID = $_POST['LabelID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($tagID) && $act == 'edit'){
            $this->setError('30301');//数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223');//模板标识不能为空！
        }
        if(empty($_POST['LabelName'])){
            $this->setError('');//标签名称不能为空！
        }
        if(empty($_POST['DefaultStart'])){
            $this->setError('');//默认标记不能为空！
        }
        $data = array();
        $data['LabelName'] = $_POST['LabelName'];
        $data['DefaultStart'] = $_POST['DefaultStart'];
        $data['Description'] = $_POST['Description'];
        $data['LoreField'] = $_POST['LoreField'];
        $data['OrderID'] = $_POST['OrderID'];
        if($act == 'add'){
            if($this->getModel('CaseLoreDocLabel')->insertData(
                    $data
                ) === false){
                $this->setError('30310');//添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加文档标签【'.$_POST['TagName'].'】');
                $this->showSuccess('添加文档标签成功！',__URL__);
            }
        }else if($act == 'edit'){
            if($this->getModel('CaseLoreDocLabel')->updateData(
                    $data,
                    'LabelID='.$_POST['LabelID']) === false){
                $this->setError('30311');//修改失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改LabelID为【'.$_POST['LabelID'].'】的数据');
                $this->showSuccess('修改知识文档标签成功！',__URL__);
            }
        }
    }
    /**
     * 删除知识文档标签
     * @author demo
     */
    public function delete(){
        $tagID = $_POST['id'];//获取数据标识
        if(!$tagID){
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        if($this->getModel('CaseLoreDocLabel')->deleteData(
                'LabelID in ('.$tagID.')') === false){
            $this->setError('30302');//删除失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除LabelID为【'.$tagID.'】的数据');
            $this->showSuccess('删除知识文档标签成功！',__URL__);
        }
    }

    /**
     * 知识文档标签缓存更新
     * @author demo
     */
    public function updateCache(){
        $caseLabel=$this->getModel('CaseLoreDocLabel');
        $caseLabel->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }

}