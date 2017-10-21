<?php
/**
 * @author demo 
 * @date 2014年12月26日
 */
 /**
  *能力管理控制器类，用于处理能力管理相关操作
  */
namespace Manage\Controller;
class AbilityController extends BaseController  {
    private $moduleName = '能力属性配置';
    /**
     * 浏览能力属性列表；
     * @author demo
     */
    public function index() {
        $pageName = '能力属性管理';
        $where='1=1';
        if($this->ifSubject && $this->mySubject){
            $where=' SubjectID in ('.$this->mySubject.') ';
        }
        $abilityArray = $this->getModel('Ability')->selectData(
            '*',
            $where,
            'SubjectID asc,OrderID asc,AbID asc'); //获取能力 属性数据集
        $subjectArray=$this->getApiCommon('Subject/subject');
        foreach($abilityArray as $i=>$iAbilityArray){
            $abilityArray[$i]['SubjectName']=$subjectArray[$subjectArray[$iAbilityArray['SubjectID']]['PID']]['SubjectName'].$subjectArray[$iAbilityArray['SubjectID']]['SubjectName'];
        }
        /*载入模板标签*/
        $this->assign('abilityArray', $abilityArray); //能力 属性数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加能力属性；
     * @author demo
     */
    public function add() {
        $pageName = '添加能力 属性';
        $act = 'add'; //模板标识
        //获取学科数据集
        $subjectArray=$this->getApiCommon('Subject/subjectParentId');
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('edit', 0); //页面标题
        $this->display();
    }
    /**
     * 编辑能力属性；
     * @author demo
     */
    public function edit() {
        $abID=$_GET['id']; //获取数据标识
        //判断数据标识
        if(empty($abID)){
            $this->setError('30301'); //数据标识不能为空
        }
        $pageName = '编辑能力 属性';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('Ability')->selectData(
            '*',
            'AbID='.$abID,
            '',
            1);
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30507'); //您不能编辑非所属学科能力属性！
            }
        }
        //获取学科数据集
        $subjectArray = $this->getApiCommon('Subject/subjectParentId');//获取学科数据集
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('pageName', $pageName);
        $this->display('Ability/add');
    }
    /**
     * 保存能力属性；
     * @author demo
     */
    public function save() {
        //接收参数
        $abID=$_POST['AbID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        $abilitName = $_POST['AbilitName']; //能力名称
        $subjectID = $_POST['SubjectID']; //学科id
        $orderID = $_POST['OrderID']; //排序id

        //判断数据标识
        if(empty($abID) && $act=='edit'){
             $this->setError('30301'); //'数据标识不能为空！
        }
        if(empty($_POST['SubjectID'])){
            $this->setError('30508'); //请选择学科！
        }
        $data=array();
        $data['AbilitName']=$abilitName;
        $data['SubjectID']=$subjectID;
        $data['OrderID']=$orderID;
           $ability=$this->getModel('Ability');
        if($act=='add'){
            if($this->ifSubject && $this->mySubject){
                if(!in_array($subjectID,explode(',',$this->mySubject))){
                    $this->setError('30507'); //您不能添加非所属学科能力属性！
                }
            }
            if($ability->insertData($data)===false){
                $this->setError('30310'); //添加能力 属性失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加能力属性【'.$abilitName.'】');
                $this->showSuccess('添加能力 属性成功！',__URL__);
            }
        }else if($act=='edit'){
            if($this->ifSubject && $this->mySubject){
                $subject = $ability->selectData(
                    'SubjectID',
                    'AbID ='.$abID);
                if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30507'); //您不能编辑非所属学科能力属性！
                }
                if(!in_array($subjectID,explode(',',$this->mySubject))){
                    $this->setError('30507'); //您不能编辑为非所属学科能力属性！
                }
            }
            if($ability->updateData($data,'AbID='.$abID)===false){
                $this->setError('30311'); //修改能力 属性失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改AbilitID为【'.$abID.'】的数据');
                $this->showSuccess('修改能力属性成功！',__URL__);
            }
        }
    }
    /**
     * 删除能力属性；
     * @author demo
     */
    public function delete(){
        $abID=$_POST['id']; //获取数据标识
        if(!$abID){
            $this->setError('30301','',__URL__); //数据标识不能为空！
              }
        $ability=$this->getModel('Ability');
        if($this->ifSubject && $this->mySubject){
            $abIDData = $ability->selectData(
                'SubjectID',
                'AbID in('.$abID.')');
            $subjectID = explode(',',$this->mySubject);
            foreach($abIDData as $i=>$iAbIDData){
                if(!in_array($iAbIDData['SubjectID'],$subjectID)){
                    $this->setError('30812','',__URL__); //您不能删除非所属学科能力属性！
                }
            }
        }
        if($ability->deleteData('AbID in ('.$abID.')')===false){
            $this->setError('30302','',__URL__); //删除能力 属性失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除AbID为【'.$abID.'】的数据');
            $this->showSuccess('删除能力 属性成功！',__URL__);
        }
    }
    /**
     * 更新能力属性缓存；
     * @author demo
     */
    public function updateCache(){
        $ability = $this->getModel('Ability');
        $ability->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }
}
