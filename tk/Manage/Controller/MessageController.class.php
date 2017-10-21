<?php
/**
 * @author demo
 * @date 2015年1月9日
 */
/**
 * 评论控制器类，用于评论相关操作
 */
namespace Manage\Controller;
class MessageController extends BaseController  {
    var $moduleName = '评论管理';
    
    /**
     * 浏览评论信息
     * @author demo
     */
    public function index() {
        $pageName = '评论管理';
        $map=array();
        $data=' 1=1 ';
        if($this->ifSubject && $this->mySubject){
            $data .= 'AND SubjectID in ('.$this->mySubject.')';
        }
            if($_REQUEST['name']){
                //简单查询
                $map['name']=$_REQUEST['name'];
                $data.=' AND UserName like "%'.$_REQUEST['name'].'%" ';
            }else{
                //高级查询
                if($_REQUEST['UserName']){
                    $map['UserName']=$_REQUEST['UserName'];
                    $data.=' AND UserName like "%'.$_REQUEST['UserName'].'%" ';
                }
                if($_REQUEST['TestID']){
                    if(is_numeric($_REQUEST['TestID'])){
                        $map['TestID']=$_REQUEST['TestID'];
                        $data.=' AND TestID = '.$_REQUEST['TestID'].' ';
                    }else{
                        $this->setError('30502');
                    }
                }
                if(is_numeric($_REQUEST['Status'])){
                    $map['Status']=$_REQUEST['Status'];
                    $data.=' AND Status = '.$_REQUEST['Status'].' ';
                }
            }
        $perpage=C('WLN_PERPAGE');
        $message=$this->getModel('Message');
        $count = $message->selectCount(
            $data,
            '*'); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $message->pageData(
            '*',
            $data,
            'ID desc',
            (isset($_GET['p']) ? $_GET['p'] : 1).','.$perpage);
        $this->pageList($count,$perpage,$map);
        /*载入模板标签*/
        $this->assign('list', $list); //菜单数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 查看评论信息详情
     * @author demo
     */
    public function view() {
        $pageName='查看评论';
        $ID=$_GET['id'];
        if(empty($ID)){
            $this->setError('30301'); //数据标识不能为空
        }
           $message=$this->getModel('Message');
        if($this->ifSubject && $this->mySubject){
            $messageData = $message->selectData(
                'SubjectID',
                'ID='.$ID);
            if(!in_array($messageData[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30313'); //您不能查看非所属学科评论！
            }
        }
        $test = $this->getModel('Test');
        $edit=$message->unionSelect('testMessageByID',$ID);
        $host=C('WLN_DOC_HOST');
        $edit[0]['Test']=$test->formatTest($edit[0]['Test'],1,600,0,1,$edit[0]['OptionWidth'],$edit[0]['OptionNum'],$edit[0]['TestNum'],$edit[0]['IfChoose'],1);
        $edit[0]['Answer']=$test->formatTest($edit[0]['Answer'],1,0,0,0,0,0,$edit[0]['TestNum'],0,1);
        $edit[0]['Analytic']=$test->formatTest($edit[0]['Analytic'],1,0,0,0,0,0,$edit[0]['TestNum'],0,1);
        $edit[0]['Remark']=$test->formatTest($edit[0]['Remark'],1,0,0,0,0,0,$edit[0]['TestNum'],0,1);
        if($host){
            $edit[0]['Test']= R('Common/TestLayer/strFormat',array($edit[0]['Test']));
            $edit[0]['Answer']= R('Common/TestLayer/strFormat',array($edit[0]['Answer']));
            $edit[0]['Analytic']= R('Common/TestLayer/strFormat',array($edit[0]['Analytic']));
            $edit[0]['Remark']= R('Common/TestLayer/strFormat',array($edit[0]['Remark']));
        }
        
        $this->assign('edit', $edit[0]); //菜单数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /*更改评论状态和备注*/
    public function save() {
        $data=array();
        $data['ID']=$_POST['ID'];
        $data['Status']=$_POST['Status'];
        $data['Reply']=$_POST['Reply'];
        $data['ReplyTime']=time();
        $message=$this->getModel('Message');
        if($this->ifSubject && $this->mySubject){
            $subject = $message->selectData(
                'SubjectID',
                'ID='.$data['ID']);
            if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30507'); //您不能编辑非所属学科评论！
            }
        }
        if(empty($data['ID'])){
            $this->setError('30301'); //数据标识不能为空
        }
        if($message->updateData(
                $data,
                'ID='.$_POST['ID'])===false){
            $this->setError('30311'); //修改失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'修改评论ID为【'.$_POST['ID'].'】的数据');
            $this->showSuccess('修改成功！', __URL__);
        }
    }
    /*删除菜单*/
    public function delete(){
        $ID=$_POST['id'];    //获取数据标识
        if(!$ID){
            $this->setError('30301','',__URL__); //数据标识不能为空
        }
        $message=$this->getModel('Message');
        if($this->ifSubject && $this->mySubject){
            $messageData = $message->selectData(
                'SubjectID',
                'ID in ('.$ID.')');
            foreach($messageData as $i=>$iMessageData){
                if(!in_array($iMessageData['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30507'); //您不能删除非所属学科评论！
                }
            }
        }
        if($message->deleteData(
                'ID in ('.$ID.')')===false){
            $this->setError('30302'); //删除菜单失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除评论ID为【'.$ID.'】的数据');
            $this->showSuccess('删除菜单成功！',__URL__);
        }
    }
}