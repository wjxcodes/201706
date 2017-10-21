<?php
/**
 * @author demo
 * @date 2015年8月15日
 */
/**
 *  经验值属性属性配置类，用于文档属性
 */
namespace User\Manage;
class UserExpManage extends BaseController  {
    var $moduleName = '经验值性配置';
    /**
     * 经验值属性属性列表浏览
     * @author demo
     */
    public function index() {
        $pageName = '经验值属性管理';
        $expArray = $this->getModel('UserExp')->selectData(
            '*',
            '1=1',
            'ExpID desc'); //获取经验值数据集
        /*载入模板标签*/
        $this->assign('typeArray', $expArray); //文档属性数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 添加经验值属性
     * @author demo
     */
    public function add() {
        $pageName = '添加文档属性';
        $act = 'add'; //模板标识
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 编辑经验值属性
     * @author demo
     */
    public function edit() {
        $expID=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($expID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑文档属性';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('UserExp')->selectData(
            '*',
            'ExpID='.$expID,
            '',
            1);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display('UserExp/add');
    }

    /**
     * 保存经验值属性
     * @author demo
     */
    public function save() {
        $expID=$_POST['ExpID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($expID) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        $data=array();
        $data['ExpName']=$_POST['ExpName'];
        $data['ExpDesc']=$_POST['ExpDesc'];
        $data['ExpPoint']=$_POST['ExpPoint'];
        $data['ExpAuthPoint']=$_POST['ExpAuthPoint'];
        $data['ExpTime']=$_POST['ExpTime'];
        if($act=='add'){
            if($this->getModel('UserExp')->insertData($data)===false){
                $this->setError('30310'); //添加经验值属性失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加文档属性【'.$_POST['ExpName'].'】');
                $this->showSuccess('添加经验值属性成功！',__URL__);
            }
        }else if($act=='edit'){
            if($this->getModel('UserExp')->updateData(
                    $data,
                    'ExpID='.$expID)===false){
                $this->setError('30311'); //修改属性失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改TypeID为【'.$_POST['ExpID'].'】的数据');
                $this->showSuccess('修改经验值成功！',__URL__);
            }
        }
    }

    /**
     * 删除经验值属性
     * @author demo
     */
    public function delete(){
        $expID=$_POST['id'];    //获取数据标识
        if(!$expID){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if($this->getModel('UserExp')->deleteData(
                'ExpID in('.$expID.')')===false){
            $this->setError('30302','',__URL__); //删除经验值属性失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除TypeID为【'.$expID.'】的数据');
            $this->showSuccess('删除经验值属性成功！',__URL__);
        }
    }

    /**
     * 更新经验值属性缓存
     * @author demo
     */
    public function updateCache(){
        $data=array();
        $userExp = $this->getModel('UserExp');
        $userExp->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }
}