<?php
namespace Teacher\Controller;
class PublicController extends BaseController {
    /**
     * 教师登陆
     * @author demo
     */
    public function login() {
        $pageName = '教师登陆';
        $this->assign('pageName',$pageName);
        $this->display();
    }
    /**
     * 教师注册
     * @author demo
     */
    public function register() {
        $pageName = '教师注册';
        if(IS_POST){
            $User=$this->getModel('User');
            $regUrl = U('Teacher/Public/register');
            $UserName=$_POST['account'];
            $password=$_POST['password'];
            $password2=$_POST['password2'];
            
            if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{9}$/", $UserName) and !preg_match('/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$/', $UserName)){
                $this->setError('40001',NORMAL_ERROR,$regUrl);
            }
            
            if(strlen($UserName)<6 || strlen($password)<6){
                $this->setError('40002',NORMAL_ERROR,$regUrl);
            }
            if($password!=$password2){
                $this->setError('30207',NORMAL_ERROR,$regUrl);
            }
            if (md5($_POST['verify'])!=session('verify')) {
                $this->setError('30101',NORMAL_ERROR,$regUrl);
            }
            //判断用户名是否重复
            $buffer=$User->checkUser($UserName);
            if($buffer){
                $this->setError($buffer,NORMAL_ERROR,$regUrl);
            }
            
            
            //检查用户名合法
            $backstr=$User->NameFilter($UserName);
            
            if($backstr['errornum']!='success'){
                $this->setError($backstr['errornum'],NORMAL_ERROR,$regUrl,$backstr['replace']);
            }
            //获取编号
            $autoinc=$this->getModel('AutoInc');
            $orderNum=$autoinc->getOrderNum();

            $data=array();
            $data['UserName']=$UserName;
            $data['Password']=md5($UserName.$password);
            $data['RealName']='';
            $data['Sex']=0;
            $data['Phonecode']='';
            $data['Email']='';
            $data['Address']='';
            $data['PostCode']='';
            $data['Status']=1;
            $data['LoadDate']=time();
            $data['LastTime']=time();
            $data['Logins']=0;
            $data['Whois']=1;
            $data['LastIP']=get_client_ip(0,true);
            $data['SaveCode']=$User->saveCode();
            $data['OrderNum']=$orderNum;
            $userID=$this->getModel('User')->insertData(
                $data
            );
            $this->getModel('RegisterLog')->insertRegisterLog($userID,$data['LastIP']);
            $this->showerror('注册成功！请联系管理员审核',U('Teacher/Public/login'));
        }
        $this->assign('pageName',$pageName);
        $this->display();
    }
    /**
     * 用户登录验证
     * @author demo 
     */
    public function check() {
        $loginUrl = U('Teacher/Public/login');
        $User = $this->getModel('User'); // 实例化对象
        $model = M('Model'); // 实例化对象
        if (!checkToken($_POST)) {
            // 令牌验证错误
            $this->setError('30312',NORMAL_ERROR,$loginUrl);
            exit ();
        }
        if (empty ($_POST['account'])) {
            $this->setError('30201',NORMAL_ERROR,$loginUrl);
            exit;
        }
        elseif (empty ($_POST['password'])) {
            $this->setError('30202',NORMAL_ERROR,$loginUrl);
            exit;
        }
        elseif (empty ($_POST['verify'])) {
            $this->setError('30104',NORMAL_ERROR,$loginUrl);
            exit;
        }
        elseif (md5($_POST['verify'])!=session('verify')) {
            $this->setError('30101',NORMAL_ERROR,$loginUrl);
            exit;
        }
        $userArray = $this->getModel('User')->selectData(
            '*',
            'UserName="' . $_POST['account'] . '"',
            '',
            1
        );
        //判断是否是教师用户
        if($userArray[0]['Whois']!=1){
            $this->setError('30206',NORMAL_ERROR,$loginUrl);
        }
        //账户是否被屏蔽
        if($userArray[0]['Status']!=0){
            $this->setError('30203',NORMAL_ERROR,$loginUrl);
        }  
        //判断教师端用户是否开通(给操作权限)
        $userID = $userArray[0]['UserID'];
        $res = $this->getModel('UserGroup')->selectData(
            'UGID',
            'GroupName=3 and UserID="'.$userID.'"'
        );
        if(!$res){
            $this->setError('40003',NORMAL_ERROR,$loginUrl);
        }
        //没有安全码添加一个
        if(empty($userArray[0]['SaveCode'])){
            $arr=array();
            $arr['SaveCode']=$User->saveCode();
            $this->getModel('User')->updateData(
                $arr,
                'UserName="' . $_POST['account'] . '"'
            );
            $userArray[0]['SaveCode']=$arr['SaveCode'];
        }
        
        if ($userArray && ($userArray[0]['Password']==md5($_POST['password']) || $userArray[0]['Password']==md5($_POST['account'].$_POST['password']))) {
            //修改最后一次登录ip
            $data=array();
            $data['UserID']=$userArray[0]['UserID'];
            $data['LastIP']=get_client_ip(0,true);
            if($this->getModel('User')->updateData($data,'UserID='.$userArray[0]['UserID'])===false){
                $this->setError('30305',NORMAL_ERROR,$loginUrl);
            }
            $time=C('WLN_COOKIE_TIMEOUT');//cookie记录超时
            $this->setCookieUserName($userArray[0]['UserName'],$time);
            $this->setCookieUserID($userArray[0]['UserID'],$time);
            $this->setCookieCode(md5($userArray[0]['UserID'].$userArray[0]['UserName'].$userArray[0]['SaveCode'].ceil(time()/$time)),$time);
            //写入日志
            $this->teacherLog('用户登录','【'.$userArray[0]['UserName'].'】登录教师系统');
            $this->showSuccess('登录成功！下面转入系统首页', __MODULE__);
        } else {
            $this->setError('30204',NORMAL_ERROR,$loginUrl);
        }
    }
    /**
     * 退出登陆
     * @author demo
     */
    public function logout() {
        //写入日志
        $userName=$this->getCookieUserName();
        if($userName){
            $this->teacherLog('用户登录','【'.$userName.'】退出教师系统');
            $this->setCookieUserName(null);
            $this->setCookieCode(null);
        }
        header('location:/User/Index/passport');
        exit;
        $this->showSuccess('你已经退出了登录！', __MODULE__);
    }
    /**
     * 修改密码资料
     * @author demo
     */
    public function password(){
        $pageName='修改信息';
        $UserName=$this->getCookieUserName();
        if(!$UserName){
            $this->setError('40004',NORMAL_ERROR);
        }
        $user = $this->getModel('User');
        $edit = $user->selectData(
            '*',
            'UserName="'.$UserName.'" AND Status="0"',
            '',
            1
        );
        if(!$edit){
            $this->setError('40004',NORMAL_ERROR);
        }
        //提交数据
        if(IS_POST){
            $data=array();
            if($_POST['Passwordy']){
                if(md5($_POST['Passwordy'])!=$edit[0]['Password'] && md5($edit[0]['UserName'].$_POST['Passwordy'])!=$edit[0]['Password']){
                    $this->setError('30208',NORMAL_ERROR);
                    exit;
                }
                if($_POST['Password']=='' || $_POST['Password2']==''){
                    $this->setError('40005',NORMAL_ERROR);
                    exit;
                }
                if($_POST['Password']!=$_POST['Password2']){
                    $this->setError('30207',NORMAL_ERROR);
                    exit;
                }
                $data['Password']=md5($edit[0]['UserName'].$_POST['Password']);
                $data['SaveCode']=$user->saveCode();
            }
            $data['Email']=$_POST['Email'];
            $data['RealName']=$_POST['RealName'];
            $data['Phonecode']=$_POST['Phonecode'];
            $data['Address']=$_POST['Address'];
            $data['PostCode']=$_POST['PostCode'];
            $data['Sex']=$_POST['Sex'];
            //判断用户名是否重复
            $buffer=$user->checkUser('',$data['Phonecode'],$data['Email'],$edit[0]['UserID']);
            if($buffer){
                $this->setError($buffer);
            }

            if($this->getModel('User')->updateData($data,' UserName="'.$UserName.'" ')===false){
                $this->setError('30311',NORMAL_ERROR);
            }else{
                //写入日志
                $this->teacherLog('用户管理','【'.$this->getCookieUserName().'】修改个人信息');
                $this->showSuccess('修改成功！');
            }
        }
        
        $this->assign('edit',$edit[0]);
        $this->assign('pageName',$pageName);
        $this->display();
    }
    /**
     * 验证码显示
     * @author demo
     */ 
    public function verify() {
        R('Common/UserLayer/verify');
    }
    /**
     * 框架页顶部
     * @author demo
     */ 
    public function top() {
        C('SHOW_PAGE_TRACE', false);
        $this->display();
    }
    /**
     * 我的权限
     * @author demo
     * @date 2014年11月8日
     */
    public function myPower(){
        $pageName = "我的权限";
        $powlstr = $this->publicInfo();//调用得到权限列表ID字符串
        $powerList = $this->getModel('PowerUserList')->selectData(
            'PowerName',
            'Value = "all" and ListID in ('.$powlstr.')'
        );
        $this->assign('powerList', $powerList);//权限数据集
        $this->assign('pageName', $pageName); //模板标识
        $this->display('Public/mypower');
    }
    /**
     * 框架页栏目
     * @author demo 
     */ 
    public function menu() {
        //显示菜单项
        $powlstr = $this->publicInfo();//调用得到权限列表ID字符串
        $pulBuf = $this->getModel('PowerUserList')->selectData(
            'PowerTag',
            'ListID in ('.$powlstr.')'
        );
        $powerArr = array();
        foreach($pulBuf as $i=>$val){
            $powerArr[] = $val['PowerTag'];
        }
        if(in_array('Teacher/Check/index',$powerArr)){$buffer['IfChecker'] = 1;}
        if(in_array('Teacher/Task/index',$powerArr)){$buffer['IfTeacher'] = 1;}
        if(in_array('Teacher/StudentWork/index',$powerArr)){$buffer['IfEq'] = 1;}
        if(in_array('Teacher/DocManager/index',$powerArr)){$buffer['DocManager'] = 1;}
        if(in_array('Teacher/CustomCheck/index',$powerArr)){$buffer['CustomCheck'] = 1;$buffer['Custom']=1;}
        if(in_array('Teacher/CustomIntro/taskTestList',$powerArr)){$buffer['CustomIntro'] = 1;$buffer['Custom']=1;}
        if(in_array('Teacher/OriginalityAudit/index',$powerArr)){
            $buffer['Originality'] = 1;
        }
        $this->assign('user',$buffer);
        $this->display();
    }
    /**
     * 框架页垂直拖动行
     * @author demo
     */ 
    public function drag() {
        $this->display();
    }
    /**
     * 框架页底部
     * @author demo
     */ 
    public function ends() {
        $this->display();
    }
    /**
     * 框架页主题
     * @author demo 
     */ 
    public function main() {
        //任务情况
        $powlstr = $this->publicInfo();//调用得到权限列表ID字符串
        $pulBuf = $this->getModel('PowerUserList')->selectData(
            'PowerTag',
            'ListID in ('.$powlstr.')'
        );
        $powerArr = array();
        foreach($pulBuf as $i=>$val){
            $powerArr[] = $val['PowerTag'];
        }
        if(in_array('Teacher/Check/index',$powerArr)){$ifChecker = 1;}
        if(in_array('Teacher/Task/index',$powerArr)){$ifTask = 1;}
        if(in_array('Teacher/StudentWork/index',$powerArr)){$ifEq = 1;}
        if(in_array('Teacher/DocManager/index',$powerArr)){$DocManager = 1;}
        if(in_array('Teacher/OriginalityAudit/index',$powerArr)){$Originality = 1;}
        $username=$this->getCookieUserName();
        //标引教师信息统计
        if($ifTask){
            $buffer=$this->getModel('TeacherWork')->selectData(
                'Status',
                ' UserName="'.$username.'" '
            );
            $data[0]['a']=0;
            $data[0]['b']=0;
            $data[0]['c']=0;
            $data[0]['d']=0;
            if($buffer){
                foreach($buffer as $buffern){
                    switch($buffern['Status']){
                        case 0:
                            $data[0]['a']++;
                        break;
                        case 1:
                            $data[0]['b']++;
                        break;
                        case 2:
                            $data[0]['c']++;
                        break;
                        case 3:
                            $data[0]['d']++;
                        break;
                    }
                }
            }
        }
        //审核教师信息统计
        if($ifChecker){
            $buffer=$this->getModel('TeacherWorkCheck')->selectData(
                'Status',
                ' UserName="'.$username.'" '
            );
            $data[1]['a']=0;
            $data[1]['b']=0;
            $data[1]['c']=0;
            if($buffer){
                foreach($buffer as $buffern){
                    switch($buffern['Status']){
                        case 0:
                            $data[1]['a']++;
                        break;
                        case 1:
                            $data[1]['b']++;
                        break;
                        case 2:
                            $data[1]['c']++;
                        break;
                    }
                }
            }
        }
        if($ifEq){
            $studentWork = $this->getModel('StudentWork')->frontPageStatInfo($username);
            $this->assign('studentWork',$studentWork);
        }
        if($DocManager){
            $num = $this->getModel('DocFile')->getTaskInfo($username);
            $this->assign('docfile',$num);
        }
        if($Originality){
            $info = $this->getModel('OriginalityAudit')->getInfo($this->getCookieUserID());
            $this->assign('info',$info);    
        }

        $this->assign('data',$data);
        $this->assign('IfTeacher',$ifTask);
        $this->assign('IfChecker',$ifChecker);
        $this->assign('IfEq',$ifEq);
        $this->assign('DocManager',$DocManager);
        $this->assign('Originality',$Originality);
        $this->display();
    }
    /**
     * 框架页底部
     * @author demo
     */ 
    public function footer() {
        C('SHOW_PAGE_TRACE', false);
        $this->display();
    }
}
?>