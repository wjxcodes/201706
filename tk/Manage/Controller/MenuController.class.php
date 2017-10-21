<?php
/**
 * @author demo  
 * @date 2015年1月7日
 */
/**
 * 后台菜单类，用处理菜单配置相关操作
 */
namespace Manage\Controller;
class MenuController extends BaseController  {
    var $moduleName = '菜单配置';
    /**
     * 浏览菜单
     * @author demo
     */
    public function index() {
        $pageName = '菜单管理';
        $menu=$this->getModel('Menu');
        $list = $menu->selectData(
            '*',
            ' 1=1 ',
            'MenuOrder asc');
        $menuArray=$menu->formatMenu($list);
        /*载入模板标签*/
        $this->assign('menuArray', $menuArray); //菜单数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加菜单
     * @author demo
     */
    public function add() {
        $pageName = '添加菜单';
        $act = 'add'; //模板标识
        $menu=$this->getModel('Menu');
        $menuArray = $menu->selectData(
            '*',
            ' PMenu=0 ',
            'MenuOrder asc'); //父类数据集
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('menuArray', $menuArray); //父类数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑菜单
     * @author demo
     */
    public function edit() {
        $menuID=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($menuID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑菜单';
        $act = 'edit'; //模板标识
           $menu=$this->getModel('Menu');
        $edit = $menu->selectData(
            '*',
            'MenuID='.$menuID);
        $menuArray =$menu->selectData(
            '*',
            ' PMenu=0 ',
            'MenuOrder asc'); //父类数据集

        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('menuArray', $menuArray);
        $this->assign('pageName', $pageName);
        $this->display('Menu/add');
    }
    /**
     * 保存菜单
     * @author demo
     */
    public function save() {
        $menuID=$_POST['MenuID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($menuID) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        $menu = $this->getModel('Menu');
        $data=array();
        $data['PMenu']=$_POST['PMenu'];
        $data['MenuName']=$_POST['MenuName'];
        $data['MenuUrl']=$_POST['MenuUrl'];
        $data['Description']=$_POST['Description'];
        $data['MenuOrder']=$_POST['MenuOrder'];
        if($act=='add'){
            if($menu->insertData(
                    $data)===false){
                $this->setError('30310'); //添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加菜单【'.$_POST['MenuName'].'】');
                $this->showSuccess('添加菜单成功！',__URL__);
            }
        
        }else if($act=='edit'){
            $data['MenuID']=$_POST['MenuID'];
            if($menu->updateData(
                    $data,
                    'MenuID='.$data['MenuID'])===false){
                $this->setError('30311'); //修改菜单失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改MenuID为【'.$_POST['MenuID'].'】的数据');
                $this->showSuccess('修改菜单成功！',__URL__);
            }
        }
    }
    /**
     * 删除菜单
     * @author demo
     */
    public function delete(){
        $menuID=$_POST['id']; //获取数据标识
        if(!$menuID){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if($this->getModel('Menu')->deleteData(
                'MenuID in ('.$menuID.')')===false){
            $this->setError('30302','',__URL__); //删除菜单失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除菜单MenuID为【'.$menuID.'】的数据');
            $this->showSuccess('删除菜单成功！',__URL__);
        }
    }
    /**
     * 权限情况检查
     * @author demo
     */
    public function check(){
        $menuCheck = $this->getModel('MenuCheck');
        $where = 'PMenu > 0 AND MenuUrl like \'%index%\'';
        $result = $this->getModel('Menu')->selectData(
            'MenuName as PowerName,MenuUrl as PowerTag',
            $where);
        $result = $menuCheck->getComparesData($result,array('Manage'),'-');
        $this->assign('result',$result['Manage']);
        $this->assign('pageName','未设置菜单列表');
        $this->display();
    }
    /**
     * 更新菜单缓存
     * @author demo
     */
    public function updateCache(){
        $menu = $this->getModel('Menu');
        $menu->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }
}