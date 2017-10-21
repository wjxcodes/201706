<?php
/**
 * @author demo
 * @date 2014年11月12日
 */
/**
 *  文档属性配置类，用于文档属性
 */
namespace Doc\Manage;

class DocTypeManage extends BaseController  {
    var $moduleName = '文档属性配置';
    /**
     * 文档属性列表浏览
     * @author demo
     */
    public function index() {
        $pageName = '文档属性管理';
        $typeArray = $this->getModel('DocType')->selectData(
            '*',
            '1=1',
            'OrderID asc'); //获取文档属性数据集
        $classMsg=SS('grade');
        foreach($typeArray as $i=>$iTypeArray){
            $gradeArr=explode(',',$typeArray[$i]['GradeList']);
            foreach($gradeArr as $j=>$jGradeArr){
                $typeArray[$i]['GradeName'].='['.$classMsg[$jGradeArr]['GradeName'].']';
            }
        }
        /*载入模板标签*/
        $this->assign('typeArray', $typeArray); //文档属性数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 添加文档属性
     * @author demo
     */
    public function add() {
        $pageName = '添加文档属性';
        $act = 'add'; //模板标识
        $grade=SS('grade');
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('gradeArr',$grade);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 编辑文档属性
     * @author demo
     */
    public function edit() {
        $typeID=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($typeID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $grade=SS('grade');
        $pageName = '编辑文档属性';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('DocType')->selectData(
            '*',
            'TypeID='.$typeID,
            '',
            1);

        $gradeList=explode(',',$edit[0]['GradeList']);
        foreach($gradeList as $i=>$iGradeList){
            if(!empty($grade[$iGradeList]['GradeName'])){
                $grade[$iGradeList]['check']=1;
            }
        }
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('gradeArr',$grade);
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display('DocType/add');
    }

    /**
     * 保存文档属性
     * @author demo
     */
    public function save() {
        $typeID=$_POST['TypeID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($typeID) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($_POST['GradeList']) && $act=='edit'){
            $this->setError('1X1005'); //所属年级不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        $data=array();
        $data['TypeName']=$_POST['TypeName'];
        $data['OrderID']=$_POST['OrderID'];
        $data['DefaultTest']=$_POST['DefaultTest'];
        $data['Tag']=$_POST['Tag'];

        $data['ChapterOrder']=$_POST['ChapterOrder'];
        $data['GradeList']=implode(',',$_POST['GradeList']);
        $data['IfHidden']=$_POST['IfHidden'];
        $data['LimitDown']=$_POST['LimitDown'];
        if($act=='add'){
            if($this->getModel('DocType')->insertData($data)===false){
                $this->setError('30310'); //添加文档属性失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加文档属性【'.$_POST['TypeName'].'】');
                $this->showSuccess('添加文档属性成功！',__URL__);
            }
        }else if($act=='edit'){
            if($this->getModel('DocType')->updateData(
                    $data,
                    'TypeID='.$typeID)===false){
                $this->setError('30311'); //修改文档属性失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改TypeID为【'.$_POST['TypeID'].'】的数据');
                $this->showSuccess('修改文档属性成功！',__URL__);
            }
        }
    }

    /**
     * 删除文档属性
     * @author demo
     */
    public function delete(){
        $typeID=$_POST['id'];    //获取数据标识
        if(!$typeID){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if($this->getModel('DocType')->deleteData(
                'TypeID in('.$typeID.')')===false){
            $this->setError('30302','',__URL__); //删除文档属性失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除TypeID为【'.$typeID.'】的数据');
            $this->showSuccess('删除文档属性成功！',__URL__);
        }
    }

    /**
     * [updateCache description]
     * @return [type] [description]
     */
    public function updateCache(){
        $data=array();
        $docType = $this->getModel('DocType');
        $docType->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }

}
