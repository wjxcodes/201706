<?php
/**
 * @author demo 
 * @date 2014年8月4日
 */
/**
 * 邀请码控制器类，用于处理邀请码相关操作
 */
namespace User\Manage;
class UserInvitationManage extends BaseController  {
    var $moduleName='邀请码管理'; //模块名称
    /**
     * 按条件浏览；
     * @author demo 
     */
    public function index() {
        $pageName = '邀请码管理';
        $map=array();
        //是否是管理员
        if(!$this->ifDiff){
            $data=' 1=1 ';
        }else{
            $data=' AdminName="'.$this->getCookieUserName().'" ';
        }
            if($_REQUEST['name']){
                //简单查询
                $map['name']=$_REQUEST['name'];
                $data.=' AND InvitName ="'.$_REQUEST['name'].'" ';
            }else{
                //高级查询
                if($_REQUEST['InvitName']){
                    $map['InvitName']=$_REQUEST['InvitName'];
                    $data.=' AND InvitName ="'.$_REQUEST['InvitName'].'" ';
                }
                //高级查询
                if($_REQUEST['UserName']){
                    $map['UserName']=$_REQUEST['UserName'];
                    $data.=' AND UserName ="'.$_REQUEST['UserName'].'" ';
                }
                if($_REQUEST['AdminName']){
                    $map['AdminName']=$_REQUEST['AdminName'];
                    $data.=' AND AdminName ="'.$_REQUEST['AdminName'].'" ';
                }
                if($_REQUEST['IfUsed']){
                    $map['IfUsed']=$_REQUEST['IfUsed'];
                    $data.=' AND IfUsed ='.$_REQUEST['IfUsed'].' ';
                }
                if(is_numeric($_REQUEST['Status'])){
                    $map['Status']=$_REQUEST['Status'];
                    $data.=' AND Status = "'.$_REQUEST['Status'].'" ';
                }
            }
        $perpage=C('WLN_PERPAGE'); //每页行数
        $UserInvitation = $this->getModel('UserInvitation');
        $count = $UserInvitation->selectCount($data,'*'); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=isset($_GET['p']) ? $_GET['p'] : 1;
        $page=$page.','.$perpage;
        $list = $UserInvitation->pageData(
            '*',
            $data,
            'InvitID desc',
            $page);
        $this->pageList($count,$perpage,$map);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /*添加*/
    public function add() {
        $pageName = '添加邀请码';
        $act = 'add'; //模板标识
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    protected function getInvit($t=0){
        if($t>5) {
            $this->setError('1X435'); //生成数据中断，数据重复严重请检查！
        }
        $invitName=dechex(rand(1600000,15999999));
        $buffer=$this->getModel('UserInvitation')->selectData(
            'InvitID',
            'InvitName="'.$invitName.'"');
        if($buffer){
            $this->getInvit($t+1);
        }else{
            return $invitName;
        }
    }
    /*保存*/
    public function save() {
        $num = $_POST['num']; //获取数据标识
        //判断数据标识
        if (!is_numeric($num)) {
            $this->setError('1X436'); //请填写数字
        }
        $invit=array();
        for($i=0;$i<$num;$i++){
            $data = array ();
            $data['InvitName'] = $this->getInvit();
            $data['UserName'] = '';
            $data['AdminName'] = $this->getCookieUserName();
            $data['AddTime'] = time();
            if($this->getModel('UserInvitation')->insertData(
                    $data)===false){
                //写入日志
                if($invit) $this->adminLog($this->moduleName,'添加邀请码【'.implode(',',$invit).'】');
                $this->setError('30310'); //添加失败！添加过程中断。
            }else{
                $invit[]=$data['InvitName'];
            }
        }
        //写入日志
        if($invit) $this->adminLog($this->moduleName,'添加邀请码【'.implode(',',$invit).'】');
        $this->showSuccess('添加成功！',__URL__);
    }
    /*删除*/
    public function delete() {
        $invitID = $_POST['id']; //获取数据标识
        if (!$invitID) {
            $this->setError('30301','',__URL__);
        }
        if($this->ifDiff){
            $buffer=$this->getModel('UserInvitation')->selectData(
                'InvitID',
                'InvitID in ('.$invitID.') and IfUsed=0 and AdminName="'.$this->getCookieUserName().'"');
            $idList=explode(',',$invitID);
            if(count($buffer)!=count($idList)){
                $this->setError('1X437'); //您要删除的数据中存在已使用或者没有权限操作的邀请码

            }
        }
        if ($this->getModel('UserInvitation')->deleteData(
                'InvitID in ('.$invitID.')') === false) {
            $this->setError('30302','',__URL__); //删除失败
        } else {
            //写入日志
            $this->adminLog($this->moduleName,'删除邀请码InvitID为【'.$invitID.'】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
    /*验证码锁定*/
    public function check() {
        $InvitID=$_POST['id']; //获取数据标识
        //判断数据标识
        $output='<script>';
        if (empty ($InvitID)) {
            $output.='alert("数据标识不能为空！");';
        }else{
            $InvitID=explode(',',$InvitID);
        
            $Status=$_POST['Status'];
            if(!is_numeric($Status)){
                $Status=0;
            }
            $buffer=$this->getModel('UserInvitation')->selectData(
                '*',
                'InvitID in ('.implode(',',$InvitID).') and (Status='.$Status.' or IfUsed=1)');
            if($buffer){
                $output.='alert("您选择的数据中存在已经被使用或者已经被改变状态的情况，请重试！");location.reload();</script>';
                $this->setBack($output);
            }
            $str = $Status==1 ? '<font color=\'red\'>锁定</font>' : '正常';
            $alert = $Status==1 ? '锁定成功' : '恢复成功';
            $classstr = $Status==1 ? 'btcheck' : 'btlock';
            
            $this->getModel('UserInvitation')->updateData(
                array('Status'=>$Status),'InvitID in ('.implode(',',$InvitID).')');
            
            foreach($InvitID as $iInvitID){
                $output.='document.getElementById("status'.$iInvitID.'").innerHTML="<span class=\''.$classstr.'\' thisid=\''.$iInvitID.'\'>'.$str.'</span>";';
            }
        }
        $output.='alert("'.$alert.'");</script>';
        $this->setBack($output);
    }
}