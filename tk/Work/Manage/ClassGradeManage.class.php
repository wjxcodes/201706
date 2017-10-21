<?php
/**
 * @author demo
 * @date 2014年8月11日
 *  
 */
 /**
  *年级管理控制器类，用于处理年级管理相关操作
  */
namespace Work\Manage;
class ClassGradeManage extends BaseController  {
    var $moduleName = '年级配置';
    /**
     * 按条件浏览年级；
     * @author demo    
     */
    public function index() {
        $pageName = '年级管理';
        $node=array();
        $subjectAll = D('ClassGrade')->selectData(
             '*',
             '1=1',
            'OrderID asc,GradeID asc'); //获取所有年级
        if($subjectAll){
            foreach($subjectAll as $isubjectAll){
                $subjectArr[$isubjectAll['GradeID']]['GradeName']=$isubjectAll['GradeName'];
                $subjectArr[$isubjectAll['GradeID']]['SubjectID']=$isubjectAll['SubjectID'];
                $subjectArr[$isubjectAll['GradeID']]['OrderID']=$isubjectAll['OrderID'];
            }
        }
        $grade=$subjectArr;//设置ID为索引的数组
        $list=SS('subjectParent');//获取学科父类
        foreach($list as $i=>$iList){
            $node[$i]['SubjectID'] = 0;
            $node[$i]['GradeID'] = $iList['SubjectID'];
            $node[$i]['GradeName'] = $iList['SubjectName'];
            $node[$i]['OrderID'] = $iList['OrderID'];
            foreach($grade as $j=>$jGrade){
                if($grade[$j]['SubjectID']==$list[$i]['SubjectID']){
                    $grade[$j]['GradeID']=$j;
                    $node[$i]['sub'][]=$grade[$j];
                }
            }
        }
        /*载入模板标签*/
        $this->assign('gradeArray', $node); //年级数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加年级；
     * @author demo    
     */
    public function add() {
        $pageName = '添加年级';
        $act = 'add'; //模板标识
        $gradeArray = SS('subjectParent'); //父类数据集
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('gradeArray', $gradeArray); //父类数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑年级；
     * @author demo    
     */
    public function edit() {
        $gradeID=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($gradeID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑年级';
        $act = 'edit'; //模板标识
        $edit= D('ClassGrade')->selectData(
            "*",
            "GradeID=$gradeID",
            "",
            1
        );//调取model中的新增方法
        $gradeArray = SS('subjectParent'); //父类数据集
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('gradeArray', $gradeArray);
        $this->assign('pageName', $pageName);
        $this->display('ClassGrade/add');
    }
    /**
     * 保存年级；
     * @author demo    
     */
    public function save() {
        $gradeID=$_POST['GradeID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($gradeID) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空!
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        $data=array();
        $data['SubjectID']=$_POST['SubjectID'];
        $data['GradeName']=$_POST['GradeName'];
        $data['OrderID']=$_POST['OrderID'];
        if($act=='add'){
            if(D('ClassGrade')->insertData(
                    $data
                )===false){//调取model中的新增方法
                $this->setError('30310'); //添加年级失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加年级【'.$data['GradeName'].'】');
                $this->showSuccess('添加年级成功！',__URL__);
            }
        }else if($act=='edit'){
            $data['GradeID']=$gradeID;
            if(D('ClassGrade')->updateData(
                    $data,
                    'GradeID='.$gradeID
                )===false){
                $this->setError('30311');//修改年级失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改GradeID为【'.$data['GradeID'].'】的数据');
                $this->showSuccess('修改年级成功！',__URL__);
            }
        }
    }
    /**
     * 删除年级；
     * @author demo    
     */
    public function delete(){
        $gradeID=$_POST['id'];    //获取数据标识
        if(!$gradeID){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if(D('ClassGrade')->deleteData(
                'GradeID in ('.$gradeID.')')===false){//调取model中的新增方法
            $this->setError('30302','',__URL__); //删除年级失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除GradeID为【'.$gradeID.'】的数据');
            $this->showSuccess('删除年级成功！',__URL__);
        }
    }
    /**
     * 更新缓存；
     * @author demo    
     */
    public function updateCache(){
        $ClassGrade=D('ClassGrade');
        $ClassGrade->setcache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }
}