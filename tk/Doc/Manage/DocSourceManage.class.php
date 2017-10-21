<?php
/**
 * @author demo
 * @date 2014年11月12日
 */
/**
 *  文档来源配置类，用于文档来源
 */
namespace Doc\Manage;
class DocSourceManage extends BaseController  {
    var $moduleName = '文档来源配置';
    /**
     * 文档来源列表浏览
     * @author demo
     */
    public function index() {
        $pageName = '文档来源管理';
        $sourceArray = $this->getModel('DocSource')->selectData(
            '*',
            '1=1',
            'SourceID desc'); //获取文档来源数据集
        foreach($sourceArray as $i=>$iSourceArray){
            $sourceArray[$i]['LogoPath']=str_replace(C('WLN_SOURCE_PIC_PATH'),'',$sourceArray[$i]['LogoPath']);
        }
        /*载入模板标签*/
        $this->assign('typeArray', $sourceArray); //文档来源数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 添加文档来源
     * @author demo
     */
    public function add() {
        $pageName = '添加文档来源';
        $act = 'add'; //模板标识
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 编辑文档来源
     * @author demo
     */
    public function edit() {
        $sourceID=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($sourceID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑文档来源';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('DocSource')->selectData(
            '*',
            'SourceID='.$sourceID,
            '',
            1);
        $edit[0]['LogoPath']=str_replace(C('WLN_SOURCE_PIC_PATH'),'',$edit[0]['LogoPath']);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display('DocSource/add');
    }

    /**
     * 保存文档来源
     * @author demo
     */
    public function save() {
        $sourceID=$_POST['SourceID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($sourceID) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        $data=array();
        $data['SourceName']=$_POST['SourceName'];
        $data['IfDefault']=$_POST['IfDefault'];
        $data['IfShowLogo']=$_POST['IfShowLogo'];
        $data['IfFree']=$_POST['IfFree'];
        $data['ValidityTime']=0;
        if($data['IfFree']==0){
            $data['ValidityTime']=strtotime($_POST['ValidityTime']);
        }
        $data['LogoPath']=C('WLN_SOURCE_PIC_PATH').$_POST['LogoPath'];
        if($act=='add'){
            if($this->getModel('DocSource')->insertData($data)===false){
                $this->setError('30310'); //添加文档来源失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加文档来源【'.$_POST['SourceName'].'】');
                $this->showSuccess('添加文档来源成功！',__URL__);
            }
        }else if($act=='edit'){
            if($this->getModel('DocSource')->updateData(
                    $data,
                    'SourceID='.$sourceID)===false){
                $this->setError('30311'); //修改文档来源失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改TypeID为【'.$_POST['TypeID'].'】的数据');
                $this->showSuccess('修改文档来源成功！',__URL__);
            }
        }
    }

    /**
     * 删除文档来源
     * @author demo
     */
    public function delete(){
        $sourceID=$_POST['id'];    //获取数据标识
        if(!$sourceID){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if($this->getModel('DocSource')->deleteData(
                'SourceID in('.$sourceID.')')===false){
            $this->setError('30302','',__URL__); //删除文档来源失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除SourceID为【'.$sourceID.'】的数据');
            $this->showSuccess('删除文档来源成功！',__URL__);
        }
    }

    /**
     * 更新文档来源缓存
     * @author demo
     */
    public function updateCache(){
        $DocSource = $this->getModel('DocSource');
        $DocSource->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }
}