<?php
/**
 * @author demo
 * @date 2014年8月4日
 */
/**
 * 管理员控制器类，用于处理管理员相关操作
 */
namespace Manage\Controller;
class AdminController extends BaseController  {
    var $moduleName='管理员管理'; //模块名称
    /**
     * 浏览管理员列表；
     * @author demo
     */
    public function index() {
        $pageName = '管理员管理';
        $adminGroupArray = $this->getModel('PowerAdmin')->selectData(
            '*',
            '1=1'
        );
        $map=array();
        $data=' 1=1 ';
        if($_REQUEST['name']){
            //简单查询
            $map['name']=$_REQUEST['name'];
            $data.=' AND a.AdminName like "%'.$_REQUEST['name'].'%" ';
        }else{
            //高级查询
            if($_REQUEST['AdminName']){
                $map['AdminName']=$_REQUEST['AdminName'];
                $data.=' AND a.AdminName like "%'.$_REQUEST['AdminName'].'%" ';
            }
            if($_REQUEST['RealName']){
                $map['RealName']=$_REQUEST['RealName'];
                $data.=' AND a.RealName like "%'.$_REQUEST['RealName'].'%" ';
            }
            if($_REQUEST['GroupID']){
                $map['GroupID']=$_REQUEST['GroupID'];
                $data.=' AND a.GroupID ="'.$_REQUEST['GroupID'].'" ';
            }
            if(is_numeric($_REQUEST['Status'])){
                $map['Status']=$_REQUEST['Status'];
                $data.=' AND a.Status = "'.$_REQUEST['Status'].'" ';
            }
        }
        $perpage=C('WLN_PERPAGE'); //每页行数
           $admin=$this->getModel('Admin');
        $count = $admin->selectCount(
            $data,
            '*',
            'a'
        ); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=page($count,$_GET['p'],$perpage).','.$perpage;
        $list = $admin->unionSelect('adminSelectByPage',
            $data,
            'a.AdminID DESC',
            $page
           );
        $subject = SS('subject');
        foreach($list as $i=>$iList){
            if($iList['ListID']=='all'){
                $list[$i]['SubjectName'] = '全部';
            }else if($iList['MySubject']){
                $listArr = explode(',',$iList['MySubject']);
                $subjectArr = '';
                foreach($listArr as $j=>$jListArr){
                    $subjectArr.=','.$subject[$subject[$jListArr]['PID']]['SubjectName'].$subject[$jListArr]['SubjectName'];
                }
                $list[$i]['SubjectName'] = ltrim($subjectArr,',');
            }else{
                $list[$i]['SubjectName'] = '无';
            }
        }
        $this->pageList($count,$perpage,$map);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('adminGroupArray', $adminGroupArray); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加管理员；
     * @author demo
     */
    public function add() {
        $pageName = '添加管理员';
        $act = 'add'; //模板标识
        $subjectArray = SS('subjectParentId'); //获取学科数据集
        $powerAdminArray=SS('powerAdmin');
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray',$subjectArray);
        $this->assign('powerAdminArray', $powerAdminArray); //父类数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑管理员；
     * @author demo
     */
    public function edit() {
        $adminID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($adminID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑管理员';
        $subjectArray = SS('subjectParentId'); //获取学科数据集
        $act = 'edit'; //模板标识
        $edit = $this->getModel('Admin')->selectData(
            '*',
            'AdminID='.$adminID);
        $powerAdminArray=SS('powerAdmin');
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('subjectArray',$subjectArray);
        $this->assign('powerAdminArray', $powerAdminArray); //父类数据集
        $this->assign('pageName', $pageName);
        $this->display('Admin/add');
    }
    /**
     * 保存管理员；
     * @author demo
     */
    public function save() {
        $adminID = $_POST['AdminID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($adminID) && $act == 'edit') {
            $this->setError('30301'); //数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223'); //模板标识不能为空！
        }
        $admin = $this->getModel('Admin');
        $data = array();
        $data['RealName'] = $_POST['RealName'];
        $data['Email'] = $_POST['Email'];
        $data['GroupID'] = $_POST['GroupID'];
        $data['Status'] = $_POST['Status'];
        if($_POST['MySubject']){
            $data['MySubject'] = join(',',$_POST['MySubject']);
        }else{
            $data['MySubject'] = '';
        }
        //判断新密码
        if($_POST['Password']!="" || $_POST['Password2']!=""){

            if($_POST['Password']!=$_POST['Password2']){
                $this->setError('30207'); //两次输入的密码不一致！
            }
            //密码规范
            if(checkString('checkUserPassWord',$_POST['PassWord'])){
                $this->setError('10135');
            }
            $data['SaveCode']=$admin->saveCode();
        }
        //判断原密码
        if($adminID==1){
            $data['GroupID'] = 1;
            $data['Status'] = 0;
            if($_POST['Passwordy']!=""){
                //为超级管理员 比较原密码是否正确
                $adminArray=$admin->selectData(
                    'Password',
                    'AdminID ='.$adminID);
                if(md5($_POST['Passwordy'])!=$adminArray[0]['Password'] and md5('admin'.$_POST['Passwordy'])!=$adminArray[0]['Password']){
                    $this->setError('30208'); //原密码不正确！
                    exit;
                }
            }
        }
        if ($act == 'add') {
            //检查管理员名称长度
            if(!checkString('isEngLength',4, 20, $_POST['AdminName'])){
                $this->setError('10301'); //管理员名称必须为4-20位字母或数字！
            }
            $data['AdminName'] = $_POST['AdminName'];
            $data['Password'] = md5($data['AdminName'].$_POST['Password']);
            //检查管理员名称重复
            $buffer = $admin->selectData(
                'AdminID',
                'AdminName="'.$data['AdminName'].'"');
            if($buffer){
                $this->setError('30718');// 用户名重复请更换
            }
            $data['IfWeak']=1;
            if($admin->insertData(
                    $data)===false){
                $this->setError('30310'); //添加失败
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加管理员【'.$_POST['AdminName'].'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        } elseif ($act == 'edit') {
            $data['AdminID'] = $adminID;
            $buffer = $admin->selectData(
                'AdminID,AdminName',
                'AdminID="'.$data['AdminID'].'"');
            if(!$buffer){
                $this->setError('10303'); //管理员不存在
            }
            if($_POST['Password']!="" || $_POST['Password2']!="") $data['Password'] = md5($buffer[0]['AdminName'].$_POST['Password']);
            if($admin->updateData(
                $data,
                'AdminID='.$data['AdminID'])===false){
                $this->setError('30311'); //修改失败
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改管理员AdminID为【'.$adminID.'】的数据');
                $this->showSuccess('修改成功！', __URL__);
            }
        }
    }
    /**
     * 删除管理员；
     * @author demo
     */
    public function delete() {
        $adminID = $_POST['id']; //获取数据标识
        $idArray=explode(',',$adminID);
        if(in_array(1,$idArray)){
            $this->setError('30811'); // 删除失败，请不要删除编号为1的超级管理员
        }
        if (!$adminID) {
            $this->setError('30301','',__URL__); //数据标识不能为空
        }
        if ($this->getModel('Admin')->deleteData(
                'AdminID in ('.$adminID.')') === false) {
            $this->setError('30302'); //删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName,'删除管理员AdminID为【'.$adminID.'】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
}