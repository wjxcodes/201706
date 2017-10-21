<?php
/**
 * @author demo
 * @date 2014-12-26
 */
/**
 * 学科配置类，用于学科信息的相关操作
 */
namespace Manage\Controller;
class SubjectController extends BaseController  {
    var $moduleName = '学科配置';
    /**
     * 浏览学科列表
     * @author demo
     */
    public function index() {
        $pageName = '学科管理';
        //获取学科数据集
        $buffer = $this->getModel('Subject')->selectData(
            '*',
            '1=1',
            'OrderID asc,subjectID asc');
        $subjectChild=array();
        $subjectTree=array(); //树形数据集
        foreach($buffer as $iBuffer){
            if($iBuffer['PID']!=0){
                $subjectChild[$iBuffer['PID']][]=$iBuffer;
            }
        }
        foreach($buffer as $iBuffer){
            if($iBuffer['PID']==0){
                $subjectTree[$iBuffer['SubjectID']]=$iBuffer;
                if($subjectChild[$iBuffer['SubjectID']]){
                    $subjectTree[$iBuffer['SubjectID']]['sub']=$subjectChild[$iBuffer['SubjectID']];
                }
            }
        }
        /*载入模板标签*/
        $this->assign('subjectArray', $subjectTree); //学科数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 添加学科信息
     * @author demo
     */
    public function add() {
        $pageName = '添加学科';
        $act = 'add'; //模板标识
        $subjectArray = $this->getModel('Subject')->selectData(
            '*','PID=0'); //父类数据集

        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray); //父类数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 编辑学科数据
     * @author demo
     */
    public function edit() {
        $subjectID=$_GET['id']; //获取数据标识
        //判断数据标识
        if(empty($subjectID)){
            $this->setError('30301'); //数据标识不能为空！
        }

        $pageName = '编辑学科';
        $act = 'edit'; //模板标识
        $subject = $this->getModel('Subject');
        $edit =  $subject->selectData(
            '*',
            'SubjectID='.$subjectID); //当前数据集
        $subjectArray =  $subject->selectData(
            '*',
            'PID=0'); //父类数据集
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName);
        $this->display('Subject/add');
    }

    /**
     * 保存学科数据
     * @author demo
     */
    public function save() {
        $subjectID=$_POST['SubjectID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if(empty($subjectID) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        $subject = $this->getModel('Subject');
        $data=array();
        $data['PID']=$_POST['PID'];
        $data['SubjectName']=$_POST['SubjectName'];
        $data['OrderID']=$_POST['OrderID'];
        $data['Style']=$_POST['Style'];
        $data['TotalScore']=$_POST['TotalScore'];
        $data['TestTime']=$_POST['TestTime'];
        $data['FontSize']=$_POST['FontSize'];
        $data['ChapterSet']=$_POST['ChapterSet'];
        $data['FormatDoc']=$_POST['FormatDoc'];
        $data['MoneyStyle']=$_POST['MoneyStyle'];
        $data['PayMoney']=$_POST['PayMoney'];
        $data['Layout']=$_POST['Layout'];

        if($act=='add'){
            if( $subject->insertData(
                    $data)===false){
                $this->setError('30310'); //添加学科失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加学科【'.$_POST['SubjectName'].'】');
                $this->showSuccess('添加学科成功！',__URL__);
            }
        }else if($act=='edit'){
            if($subject->updateData(
                    $data,
                    'SubjectID='.$subjectID)===false){
                $this->setError('30311'); //修改学科失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改SubjectID为【'.$_POST['SubjectID'].'】的数据');
                $this->showSuccess('修改学科成功！',__URL__);
            }
        }
    }
    /**
     * 删除学科信息
     * @author demo
     */
    public function delete(){
        $subjectID=$_POST['id']; //获取数据标识
        if(!$subjectID){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if($this->getModel('Subject')->deleteData(
            'SubjectID in ('.$subjectID.')')===false){
            $this->setError('30302'); //删除学科失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除SubjectID为【'.$subjectID.'】的数据');
            $this->showSuccess('删除学科成功！',__URL__);
        }
    }
    /**
     * 更新学科缓存
     * @author demo
     */
    public function updateCache(){
        $subject=$this->getModel('Subject');
        $subject->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }
}