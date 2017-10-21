<?php
/**
 * @author demo
 * @date 2014年11月17日
 */
/**
 * 配置设置类
 */
namespace Manage\Controller;
class SystemConfigController extends BaseController  {
    var $moduleName='提分系统基础设置'; //模块名称
    /**
     * 浏览基础配置列表
     * @author demo
     */
    public function index() {
        $pageName = '系统基础设置';
        $config=$this->getModel('SystemConfig');
        if ($_REQUEST['name']) {
            //简单查询
            $data['ConfigName'] = $_REQUEST['name'];
        } else {
            if ($_REQUEST['configName']) {
                $data['ConfigName'] =  $_REQUEST['configName'];
            }
            if ($_REQUEST['type']) {
                $data['Type'] = $_REQUEST['type'];
            }
        }
        $field='ConfigID,ConfigName,Value,Title,Desc,Type,EditTime,EditUserName';
        $configList=$config->selectData(
            $field,
            $data,
            'ConfigID desc');
        //配置组
        $configGroup=$this->getModel('PowerUser')->getGroupName();
        
        foreach($configList as $ii=> $config){
            $configList[$ii]['EditTime']=date('Y-m-d H:i:s',$config['EditTime']);
            $configList[$ii]['Type']=$configGroup[$config['Type']];
        }
        /*载入模板标签*/
        $this->assign([
            'list'=>$configList,//数据集
            'configGroup'=>$configGroup,//配置组
            'pageName'=>$pageName//页面标题
        ]);
        $this->display();
    }
    /**
     * 添加配置
     * @author demo
     */
    public function add() {
        $pageName = '添加管理员';
        $act = 'add'; //模板标识
        /*载入模板标签*/
        $this->assign([
            'act'=>$act,//模板标识
            'configGroup'=>$this->getModel('PowerUser')->getGroupName(),//配置组
            'pageName'=>$pageName//页面标题
        ]);
        $this->display();
    }
    /**
     * 编辑配置
     * @author demo
     */
    public function edit() {
        $pageName = '编辑配置项';
        $act = 'edit'; //模板标识
        $configID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($configID)) {
            $this->showerror('30301'); //获取数据标识不能为空
        }

        $field="ConfigID,ConfigName,Value,Title,Desc,Type";
        $edit=$this->getModel('SystemConfig')->selectData(
            $field,
            'ConfigID ='.$configID);
        /*载入模板标签*/
         $this->assign([
            'act'=>$act,//模板标识
            'edit'=>$edit[0],//数据集
            'configGroup'=>$this->getModel('PowerUser')->getGroupName(),//配置组
            'pageName'=>$pageName//页面标题
        ]);
        $this->display('SystemConfig/add');
    }
    /**
     * 保存配置
     * @author demo
     */
    public function save() {
        $configID = $_POST['configID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($configID) && $act == 'edit') {
            $this->showerror('30301！'); //数据标识不能为空
        }
        if (empty ($act)) {
            $this->showerror('30301！'); //模板标识不能为空
        }
        $config = $this->getModel('SystemConfig');
        $data = array ();
        $data['ConfigName'] = $_POST['configName'];
        $data['Value'] = $_POST['value'];
        $data['Title'] = $_POST['title'];
        $data['Desc'] = $_POST['desc'];
        $data['Type'] = $_POST['type'];
        //判断同一配置组中的配置名称是否重复
        $list=$config->getCache($_POST['type'],$_POST['configName']);
        if($list['ConfigID']!=$configID){
            $this->showerror('该配置组中已存在'.$_POST['configName'].'配置！');
        }
        if($act=='add'){
            $data['AddTime'] = time();
        }
        $data['EditTime'] = time();
        $data['EditUserName']=$this->getCookieUserName();
        
        if ($act == 'add') {
            if($config->insertData(
                    $data)===false){
                $this->showerror('30310'); //添加失败
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加配置项【'.$_POST['title'].'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        } else{
            if ($act == 'edit') {
                if($config->updateData(
                        $data,
                        'ConfigID='.$configID)===false){
                    $this->showerror('30303'); //修改失败
                }else{
                    //写入日志
                    $this->adminLog($this->moduleName,'修改配置项ConfigID为【'.$configID.'】的数据');
                    $this->showSuccess('修改成功！', __URL__);
                }
            }
        }
    }
    /**
     * 删除配置信息
     * @author demo
     */
    public function delete(){
        $configID=$_POST['id'];    //获取数据标识
        if(!$configID){
            $this->showerror('30301',__URL__); //获取数据标识不能为空
        }
        if($this->getModel('SystemConfig')->deleteData(
                'ConfigID in ('.$configID.')')===false){
            $this->showerror('30302'); //删除配置信息失败
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除ConfigID为【'.$configID.'】的数据');
            $this->showSuccess('删除配置信息成功！',__URL__);
        }
    }
    /**
     * 更新缓存
     * @author demo
     */
    public function updateCache(){
        $config = $this->getModel('SystemConfig');
        $config->setcache(); //更新缓存
    }
}