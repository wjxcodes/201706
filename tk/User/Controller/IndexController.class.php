<?php

/**
 * 官网注册类
 * @author demo
 * @date 2015年10月30日
 */
/**
 * 注册类，用于官网注册
 */
namespace User\Controller;
class IndexController extends BaseController{
	
    /**
     * 登录功能
     * @author demo
     */
    public function login(){
        $isAjax = IS_AJAX ? 1 : 0;
        $userName=$_POST['userName'];//用户名
        $passWord=$_POST['passWord'];//密码
        $ifSave=$_POST['ifSave'];//是否保持长期登录
        $buffer=$this->getApiUser('User/login',$userName,$passWord,$ifSave);
        if($buffer[0]==1){
            $this->setBack($buffer[1], $isAjax);
        }
        $this->setError($buffer[1], $isAjax);
    }


    /**
     * 通行证
     * @author demo 16-5-11
     */
    public function passport(){
        $url = $_GET['url'];
        $this->assign('redirect', $url);
        $this->display();
    }

    /**
     * 通行证登录
     * @author demo 16-5-11
     */
    public function passportLogin(){
        $username = $_POST['username'];
        $possword = $_POST['password'];
        $role = $_POST['role'];
        $remember = $_POST['remember'];
        $result = $this->getApiUser('User/login', $username, $possword, $remember, $role);
        if(0 == $result[0]){
            exit(R('Common/SystemLayer/ajaxSetError',array($result[1],2)));
        }
        exit('success');
    }

    /**
     * 退出功能
     * @author demo
     */
    public function loginOut(){
        $who=$_POST['who'];
        $system='组卷';
        $username = $this->getCookieUserName('Home');
        if($who==0){
            $system='提分';
            $username = $this->getCookieUserName('Aat');
        }
        R('Common/UserLayer/loginOut',array($who));
        if($username) {//防止出现用户在其他地方已经退出，用户名获取不到
            $this->userLog('用户登录', '用户【' . $username . '】从官网退出' . $system . '系统', $username);
        }
        $this->setBack('success');
    }

    /**
     * 展示找回密码页面
     * @author demo
     */
    public function getPassword(){
        $this->assign('title','找回密码');
        $this->display();
    }

    /**
     * 检查找回密码的信息是否正确
     * 正确返回array(0,array('email'=>用户邮箱号,'phone'=>用户手机号,'userID'=>用户ID))
     * 注：正确时返回的数据中邮箱号和手机号可能有也可能没有，取决于用户该字段是否有信息
     * 错误返回array(1,错误码)
     * @author demo
     */
    public function checkGetPasswordInfo(){
        $userStyle = $_POST['userStyle']; //找回密码方式
        $userInfo = $_POST['userName'];//找回密码的信息，与找回密码方式对应
        $imgCode = $_POST['imgCode'];
        if(MD5($imgCode) != session('verify')){
            $this->setError('30101',1);//验证码有误！
        }

        $output = R('Common/UserLayer/checkGetPasswordInfo',array($userStyle,$userInfo));

        if($output[0]==1){
            $this->setError($output[1],1);
        }
        $this->setBack($output[1]);
    }

    /**
     * 检测用户输入的验证码，用于密码找回安全验证
     * @author demo
     */
    public function checkCode(){
        //$code = $_POST['code'];
        //$type = $_POST['type'];
        $userID = $_POST['userID'];
        //$style = $_POST['style'];
//      if($type == 1){//手机
//          $output = R('Common/UserLayer/checkPhoneCode',array($style,$code,$userID,1));
//      }else if($type == 2){//邮箱
//          $output = R('Common/UserLayer/checkEmailCode',array($style,$code,$userID,1));
//      }
        //输出错误信息
//      if($output[0] == 1){
//          $this->setError($output[1],1);
//      }
        $s=md5($userID.date('Ymd').C('REG_KEY'));
        $this->setBack(array('success',$s)); //返回成功提示
    }

    /**
     * 重置密码保存
     * @author demo
     */
    public function setPassword(){
        $userID=$_REQUEST['userID'];
        $s=$_REQUEST['s'];
        $password=$_POST['password'];
        $password1=$_POST['password1'];

        if($s!=md5($userID.date('Ymd').C('REG_KEY'))){
            $this->setError('30228',1);
        }

        $output = R('Common/UserLayer/setPassword',array($userID,$password,$password1));

        if($output[0]==1){
            $this->setError($output[1],1);
        }
        $this->userLog('用户登录', '用户【' . $output[1]['userName'] . '】通过找回密码修改密码',$output[1]['userName']);
        $this->setBack('success');
    }

    /**
     * 展示注册页面
     * @author demo
     */
    public function registerIndex(){
        //判断是否开放注册
        if(C('OPEN_REGISTER') == 0){
            exit('系统暂时禁止注册，如有需要请联系我们！');
        }
        $who='teacher';//默认注册教师
        //如果请求中带有注册来源，就使用注册来源
        if(isset($_REQUEST['who'])){
            $who=$_REQUEST['who'];
        }
        $this->assign('title','注册');
        $this->assign('who',$who);
        $this->display();
    }

    /**
     * 保存注册信息
     * @author demo
     */
    public function registerSave(){
        $data=$_REQUEST['data'];
        $result = $this->getApiUser('User/register', $data);
        $result = array(
            'status' => $result[0],
            'data' => $result[1]
        );
        exit(json_encode($result));
        // $who = $data['who'];//身份
        // $way = $data['way'];//注册方式，1为手机号，2为邮箱
        // $userName  = $data['userName'];//用户名
        // $passWord  = $data['password'];//密码
        // $passWord1 = $data['password1'];
        // $nickname  = $data['nickname'];//昵称

        // if($way==1 || $who == 'teacher'){//当主持方式为手机号，或者身份是老师的情况下，验证手机号及短信验证码
        //     $preg_tel = checkString('checkIfPhone',$userName);
        //     if($preg_tel==false){
        //         $this->setError('30211',1);
        //     }
        //     $phoneCode = $data['phoneCode'];//手机验证码

        //     //验证手机验证码是否正确
        //     $output=R('Common/UserLayer/checkPhoneCode',array($userName,$phoneCode,0,1));

        //     if($output[0] == 1){
        //         $this->setError($output[1],1); //返回错误提示
        //     }
        //     $result['Phonecode']=$userName;
        //     $result['CheckPhone'] = 1;
        //     $whereField['Phonecode']=$userName;
        // }else{//验证邮箱
        //     $preg_email = checkString('checkIfEmail',$userName);
        //     if($preg_email==false){
        //         $this->setError('30227',1);
        //     }
        //     $emailCode = $data['emailCode'];//邮箱验证码

        //     //验证邮箱验证码是否正确
        //     $output=R('Common/UserLayer/checkEmailCode',array($userName,$emailCode,0,1));

        //     if($output[0] == 1){
        //         $this->setError($output[1],1); //返回错误提示
        //     }
        //     $result['Email'] = $userName;
        //     $result['CheckEmail'] = 1;
        //     $whereField['Email']=$userName;
        // }

        // //判断密码是否合法
        // if (strlen($passWord) < 6 || strlen($passWord) > 18) {
        //     $this->setError('30221', 1);
        // }
        // //判断密码是否重复
        // if ($passWord != $passWord1) {
        //     $this->setError('30207', 1);
        // }

        // //判断用户名重复
        // $whereField['UserName']=$userName;
        // //判断昵称
        // $nickname=formatString('stripTags',$nickname);
        // $whereField['Nickname']=$nickname;
        // $err=R('Common/UserLayer/checkField',array($whereField));

        // if(!empty($err) && !is_numeric($err)){
        //     $error=R('Common/SystemLayer/formatError',array($err));
        //     $this->setError($error[0],1,'',$error[1]);
        // }

        // //特殊处理
        // if(!empty($err) && is_numeric($err)){
        //     $userID=$err; //用户id
        // }
        // $whois = 1;
        // if($who == 'student'){
        //     $whois = 0;
        // }

        // $user=$this->getModel('User');
        // //计算学号赋值
        // $order_num = $this->getModel('AutoInc')->getOrderNum();
        // $result['OrderNum'] = $order_num;
        // $result['UserName'] = $userName;
        // $result['Nickname'] = $nickname;
        // $result['Password'] = MD5($userName . $passWord);
        // $result['RealName'] = '';
        // $result['Sex'] = 0;
        // $result['Address'] = '';
        // $result['PostCode'] = '';
        // $result['Whois'] = $whois;
        // $result['LoadDate'] = time();
        // $result['LastTime'] = time();
        // $result['Logins'] = 0;
        // $result['LastIP'] = get_client_ip(0,true);
        // $result['SaveCode'] = $user->savecode();
        // $result['IfShowTime'] = 0;

        // if($userID){
        //     $buffer = $user->updateData($result,array('UserID'=>$userID));
        // }else{
        //     $buffer = $user->insertData($result);
        // }

        // if($buffer){
        //     if($whois==1 && !$userID) {
        //         $userID=$buffer;
        //         //判断用户是否在可注册的IP下
        //         // $ip=get_client_ip(0,true);
        //         // $buffer = $this->getModel('UserIp')->userIp(
        //         //     'IPID,PUID,LastTime,IfReg',
        //         //     'IPAddress='.ip2long($ip)
        //         // );
        //         // $ipUser = $buffer[0];
        //         $ipUser = array(); //此处根据要求不验证学校ip  2015-11-8
        //         //注册时添加指定分组  2015-9-2
        //         $this->getModel('UserGroup')->addDefaultGroupAtRegistration($userID, $ipUser);
        //     }
        //     //根据$way 通过方式 1 是手机，2是邮箱
        //     if($way==1){
        //         //教师用户注册成功后，默认成手机验证
        //         $this->getModel('UserExp')->addUserExpAll($userName,'mobile');
        //     }else{
        //         //学生用户注册成功后，默认邮箱注册
        //         $this->getModel('UserExp')->addUserExpAll($userName,'email');
        //     }
        //     if(!$userID) $userID=$buffer;
        //     $this->getModel('RegisterLog')->insertRegisterLog($userID,$result['LastIP']);
        //     $this->setBack('注册成功！');
        // }
    }
    /**
     * 发送短信或者邮箱验证码
     * @author demo
     */
    public function sendCodeNum(){
        $sendNum=$_POST['sendNum'];
        $imgCode =$_POST['imgCode'];
        if(MD5($imgCode) != session('verify')){
            $this->setError('30101',1);//验证码有误！
        }
        if(checkString('checkIfPhone',$sendNum)===false){
            $output = R('Common/UserLayer/sendEmailCode',array($sendNum,0));
        }else{
            $output = R('Common/UserLayer/sendPhoneCode',array($sendNum,$imgCode,0));
        }

        //输出错误信息
        if($output[0] == 1){
            $this->setError($output[1],1);
        }

        $this->setBack('发送成功');
    }

    /**
     * 发送短信验证码
     * @author demo
     */
    public function sendPhoneCode(){
        $phoneNum=$_POST['phoneNum'];
        $imgCode =$_POST['imgCode'];
        $userID = $_POST['userID'];
        if(MD5($imgCode) != session('verify')){
            $this->setError('30101',1);//验证码有误！
        }
        $output=R('Common/UserLayer/sendPhoneCode',array($phoneNum,$imgCode,$userID));
        //输出错误信息
        if($output[0] == 1){
            $this->setError($output[1],1);
        }

        $this->setBack('发送成功');
    }

    /**
     * 发送邮箱验证码
     * @author demo
     */
    public function sendEmailCode(){
        $email = $_POST['email'];
        $userID = $_POST['userID'];
        if(!$email){
            $this->setError('1X422',1);//请填写邮箱
        }
        $output  = R('Common/UserLayer/sendEmailCode',array($email,$userID));

        //输出错误信息
        if($output[0] == 1){
            $this->setError($output[1],1);
        }

        $this->setBack('success');
    }

    /**
     * 显示已发送激活邮件的页面
     * @author demo
     */
    public function regSeedEmail(){
        $this->display();
    }
    /**
     * 显示教师注册成功的页面
     * @author demo
     */
    public function regSucceedTeacher(){
        $nickname = $_REQUEST['Nickname'];
            $this->assign('Nickname',$nickname);
            $this->display();
        }

    /**
     * 认证页面
     * @author demo
     */
    public function authTeacher(){
        $checks = $this->checkLogin('Home',1);
        if(is_array($checks) && $checks[0]==1){
            $userInfo = $checks[1][0];
            if($userInfo['IfAuth'] == 2) $this->setError('1X420');
            $this->assign('authStatus',$userInfo['IfAuth']);
            $this->assign('realName',$userInfo['RealName']);
            if($_COOKIE['AuthDatadata']){
                $cookData = unserialize(stripslashes_deep($_COOKIE['AuthDatadata']));
                $this->assign('data',$cookData);
            }elseif ($userInfo['IfAuth'] != 0){
                $authInfo = $this->getModel('TeacherAuthinfo')->getList('UserID,IDNumber,Qualification,QuaPicSrc,Grade,GradePicSrc','UserID = '.$userInfo['UserID'],'ID DESC','1');
                $this->assign('data',$authInfo[0]);
            }
            cookie('AuthDataname',null);
            cookie('AuthDatadata',null);
            $this->display();
        }else {
            $this->setError('30205',0,U('/Home','',false));
        }
    }

    /**
     * 是否有认证信息
     * @param int $userId
     * @return int | bool $userId
     * @author demo
     */
    protected function isHaveAuthData($userId){
        return $authData = $this->getModel('TeacherAuthinfo')->getList('ID,UserID,Qualification,QuaPicSrc,Grade,GradePicSrc,AuthTime',
                                 'UserID = '.$userId,'ID DESC','1')[0];
    }

    /**
     * 添加认证信息
     * 1防止刷新
     * 2添加或者修改
     * @param $param 其他方法调用
     * @author demo
     */
    public function addAuthData(){
        $uid = $this->getCookieUserID('Home');
        if(!$uid) $this->setError('30205',0,U('/Home','',false));
        if(!IS_POST) emptyUrl();
        //防止认证成功之后继续刷新当前页面
        $msgDetail['AuthDataname'] = $_COOKIE['AuthDataname'];
        $msgDetail['AuthDatadata'] = unserialize(stripslashes_deep($_COOKIE['AuthDatadata']));
        $authStatus = $this->getModel('User')->getInfoByID($uid,'IfAuth');
        if($authStatus['IfAuth'] == 2){
            $this->setError('1X420',0,U('Home/Index/main',array('u'=>'User_Home_info')));//已认证 跳到个人中心
        }
        if(empty($msgDetail['AuthDatadata'])){//新认证 或者 修改
            $displayContent='Common/error';//错误提示页面
            $data['UserID'] = $uid;
            $data['Qualification'] = trim($_POST['qualification']);
            $data['Grade'] = trim($_POST['grade']);
            $userdata['RealName'] = trim($_POST['realName']);
            $userdata['IfAuth'] = 1;//更改user表认证状态为 未认证
            $data['IDNumber'] = trim($_POST['idnumber']);
            //真实姓名
            if(!(preg_match('/^[\x{4e00}-\x{9fa5}]{2,10}$/u',$userdata['RealName']))){
                $this->setError('30216');
            }
            //验证身份证号
            if(!(preg_match('/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/i',$data['IDNumber']))){
                $this->setError('1X413');
            }
            //验证资格证书编号
            if(!(preg_match('/(^[789]\d{14}$)|(^19\d{15}$)|(^20[01]\d{14}$)/',$data['Qualification']))){
                $this->setError('1X414');
            }

            //验证等级证书编号 XXX 验证规则不明
            if(!(preg_match('/^[\d\w\_\-\/]{2,18}$/',$data['Grade']))){
                $this->setError('1X415');
            }

            if($authStatus['IfAuth'] != 0 ){//用户已经认证过 不一定上传认证图片
                foreach ($_FILES as $i=>$file){
                    if($file['error'] == 4){
                        unset($_FILES[$i]);
                    }
                }
            }

            if($authStatus['IfAuth'] == 0 && count($_FILES) != 2){//同时上传资格证与等级证
                $this->setError('1X416');
            }

            if($authStatus['IfAuth'] == 0 || !empty($_FILES)){
                //验证图片
                foreach ($_FILES as $file){
                    if(getimagesize($file['tmp_name']) === false){
                        $this->setError('1X416');
                    }
                }

                //上传 成功获得路径数组  失败获得错误字符串
                $pathArr = $this->getModel('Upload')->uploadImgArr();
                if(!is_array($pathArr)){
                    $this->showError($pathArr);
                }

                if($pathArr['quaPicSrc']){
                    $data['QuaPicSrc'] = $pathArr['quaPicSrc'];
                }
                if($pathArr['gradeImg']){
                    $data['GradePicSrc'] = $pathArr['gradeImg'];
                }

            }

            $data['AuthTime'] = time();
            if($authStatus['IfAuth'] == 1){//认证中修改认证信息
                $teacherAuthID = $this->isHaveAuthData($uid);
                $this->saveAuthInfo($data, $userdata,$teacherAuthID['ID']);
            }else if($authStatus['IfAuth'] == 0 || $authStatus['IfAuth'] == 3){//添加认证信息
                $this->saveAuthInfo($data, $userdata);//保存认证信息
            }
        }else {
            $this->assign('realName',$msgDetail['AuthDataname']);
            $this->assign('authInfo',$msgDetail['AuthDatadata']);
            $this->display('teacherAuthCheck');
        }
    }

    /**
     * 保存认证信息
     * @param array $data 认证信息
     * @param array $userRealName 用户表用户真实姓名数组
     * @param int $id 要修改的认证记录ID
     * @author demo
     */
    private function saveAuthInfo($data,$userRealName='',$id=''){
        if(empty($id)){//添加认证信息
            $rs = $this->getModel('TeacherAuthinfo')->save($data);
        }else{//修改认证信息
            $rs = $this->getModel('TeacherAuthinfo')->save($data,$id);
        }
        if($rs !== false){//成功
            if(!empty($userRealName)) $this->getModel('User')->changeUserData($data['UserID'],$userRealName);
            cookie('AuthDataname', $userRealName['RealName'], 60*60);//一小时有效期
            cookie('AuthDatadata', serialize($data), 60*60);
            $this->assign('realName',$userRealName['RealName']);
            $this->assign('authInfo',$data);
            $this->display('teacherAuthCheck');
        }else{//保存失败 删除图片 提示信息
            unlink(realpath('./') . $data['QuaPicSrc']);
            unlink(realpath('./') . $data['GradePicSrc']);
            $this->setError('1X417');//教师认证信息保存失败
        }
    }

    /**
     * 查看认证信息
     * @author demo
     */
    public function showAuthInfo(){
        $checks = $this->checkLogin('Home');
        if(is_array($checks) && $checks[0]==1){
            $UserInfo = $checks[1][0];
            $authInfo = $this->getModel('TeacherAuthinfo')->getList('UserID,IDNumber,Qualification,QuaPicSrc,Grade,GradePicSrc','UserID = '.$UserInfo['UserID'],'ID DESC','1');
            cookie('AuthDataname', $UserInfo['RealName'], 60*60);//一小时有效期
            cookie('AuthDatadata', serialize($authInfo[0]), 60*60);
            $this->assign('realName',$UserInfo['RealName']);
            $this->assign('authInfo',$authInfo[0]);
            if(in_array($UserInfo['IfAuth'],array(1,3))){
                //认证中、认证未通过
                $this->assign('authStatus',$UserInfo['IfAuth']);
                $this->display('teacherAuthCheck');
            }elseif ($UserInfo['IfAuth'] == 2){
                //认证成功
                $this->display('teacherAuthSucceed');
            }elseif ($UserInfo['Whois']){
                //未认证
                $this->display('authTeacher');
            }
        }else{
            $this->setError('30205',0,U('/Home','',false));
        }
    }

    /**
     * 完善信息页面
     * @author demo
     */
    public function completeData(){
        $username=$this->getCookieUserName('Home');
        if($username){
//             $buffer = $this->getModel('User')->getInfoByName(
//                 $username,
//                 'UserID,UserName,RealName,AreaID,SchoolID,GradeID,SubjectStyle,Email,Phonecode,Address'
//             );
            //是否完善信息
//             if($buffer[0]['AreaID']==0 || $buffer[0]['SchoolID']==0 || $buffer[0]['GradeID']==0 || $buffer[0]['SubjectStyle']==0){
//             }else {//已经完善
//             }
//             $user = json_encode($buffer[0], JSON_UNESCAPED_UNICODE);
//             $this->assign('user', $user); //用户信息
            $subject = SS('subjectParentId');//学科
            $classGrade = SS('gradeListSubject');//年级
            $this->assign('subject',$subject);
            $this->assign('classGrade',$classGrade);
            $this->display();
        }else $this->setError('30205',0,U('/Home','',false));
    }

    /**
     * 完善资料信息 数据库修改操作
     * @author demo
     */
    public function completeInfo(){
        if(IS_POST){
            $address=$_POST['Address'];
            if(!empty($address) && !preg_match('/^[\x{4e00}-\x{9fa5}\d\w\-\/]+$/u',$address,$match)){
                $this->setError('1X418');
            }
            $areaID= (int)$_POST['AreaID'];
            $schoolID= (int)$_POST['SchoolID'];
            $gradeID= (int)$_POST['GradeID'];
            $subjectID= (int)$_POST['SubjectID'];

            $user=$this->getModel('User');
            $username=$this->getCookieUserName('Home');
            $saveCode= $this->getCookieCode('Home');

            $buffer = $user->getInfoByName($username);
            $code=md5($buffer[0]['UserID'].$username.$buffer[0]['SaveCode'].date('Ymd'));
            $code1=md5($buffer[0]['UserID'].$username.$buffer[0]['SaveCode'].date('Ymd',time()-24*3600));

            $data=array();
            if($saveCode!=$code && $saveCode!=$code1){
                $this->setError('1X406',1);
            }
            $userID=$buffer[0]['UserID'];

            $email = formatString('stripTags',$_POST['Email']);
            //验证邮箱格式
            if(!(checkString('checkIfPhone',$email))){
                $this->setError('30227');
            }
            //验证邮箱是否已经存在
            if($this->getModel('User')->checkUser('','',$email) == '30225'){
                $this->setError('30225');
            }
            //区域ID
            if($areaID<1){
                $this->setError('30727');
            }
            //学校ID
            if($schoolID<1){
                $this->setError('30736');
            }
            //年纪ID
            if($gradeID<1 || $gradeID > 9){
                $this->setError('30735');
            }
            //学科ID
            if($subjectID<1 || $subjectID > 30){
                $this->setError('30508');
            }

            //验证信息是否重复
            $data['AreaID']=$areaID;
            $data['SchoolID']=$schoolID;
            $data['GradeID']=$gradeID;
            $data['SubjectStyle']=$subjectID;
            $data['UserID']=$userID;
            $data['Email']=$email;
            if($address) $data['Address']=formatString('changeStr2Html',$address);
            if($user->changeUserData($userID,$data)===false){
                $this->setError('30310',1);
            }else {
                $this->showSuccess('完善成功');
            }
        }else __hack_action();
    }

    /**
     * 购买用户权限订单页
     * @author demo
     */
    public function operOrder(){
        //用户提交前AJAX验证登录
        if(IS_AJAX){
            $checkRes = $this->checkLogin('Index',1);
            if($checkRes[0] != 1) {
                if($checkRes == '30205'){//学生程序走到108
                    $this->setBack('30205');
                }elseif($checkRes == '30203'){//账户锁定
                    $this->setBack('30203');
                }
                $this->setBack(0);
            }
            $this->setBack(1);
        }
        //定义价格
        $vipPrice      = 20;//专项会员每月的价格
        $superVipPrice = 40;//至尊会员每月的价格
        $discount      = 10/12;//年费折扣  目前折扣为小数,没有在数据库里存储
        //获取权限对应价格
        $map['PUID'] = array('in','43,44');
        $res = $this->getModel('PowerUser')->selectData(
            'PUID,OpenBuy,Price',
            $map,
            '',
            2
        );
        if(count($res)!=2) $this->setError('1X419',2);//未开放购买
        foreach($res as $i=>$iRes){
            if($iRes['OpenBuy']==0){
                $this->setError('1X419',2);//未开放购买
            }
            if($iRes['PUID']==43){//专项会员
                $vipPrice = $iRes['Price'];
            }
            if($iRes['PUID']==44){//至尊会员
                $superVipPrice = $iRes['Price'];
            }
        }

        //判断是否在活动中
        $slogan = '';
        if(C('IS_PROMOTION')){//活动中
            //判断活动时间
            if(time()>=C('PROM_BEGIN_TIME') && time()<=C('PROM_END_TIME')){
                $vipPrice      = C('VIP_PRICE');
                $superVipPrice = C('SUPER_VIP_PRICE');
                $slogan        = '[已优惠]'.C('PROMOTION_SLOGAN');
            }
        }

        //提交验证
        if (!empty($_POST)) {
            //验证登录
            $checkRes = $this->checkLogin('Index',1);
            if ($checkRes[0] != 1) exit();//登录验证

            //令牌验证
            if(!checkToken($_POST)){
                exit();
            }
            //计算费用
            $mType = (int)$_POST['memberType'];
            $tType = (int)$_POST['timeType'];
            $time  = (int)$_POST['times'];
            $price = $vipPrice;//单价
            //数据验证
            if($mType!=1) $mType = 0;//强制赋值
            if($tType!=1) $tType = 0;//强制赋值
            if($mType==1) $price = $superVipPrice;//更改价格
            if($time>999 || $time<1){ //购买时间限制
                exit();
            }
            if($tType==1) {//年费时间限制 不能超过80年
                if($time>80) exit();
            }

            $totalFee = $price * $time;//总费用
            if($tType==1) $totalFee = $totalFee * 12 * $discount;//年费
            //构造统一参数
            $orderID     = formatString('genUUOrderID',$this->getCookieUserID('Home'));//订单ID 在本站内是唯一的
            //订单名称和详细
            //动态生成订单名称 加#处理为了订单完成页面前台样式处理,如有更改,请查看订单完成页面程序
            $orderName  = '智慧云题库云平台会员服务#';
            $orderName .= $mType==0?'VIP专享用户':'VIP至尊用户';
            $orderName .= '#';
            if($tType==0){
                $orderName .= $time.'月';
            }else{
                $orderName .= $time.'年';
            }
            $orderDetail = '开通会员,更多特权等你来拿!';
            $powerID     = $mType==0?43:44;
            //构建订单参数
            $param = [
                'UID'         => $this->getCookieUserID('Home'),//用户ID
                'OrderTime'   => time(),
                'OrderName'   => $orderName,
                'OrderDetail' => $orderDetail,
                'OrderStatus' => 0,
                'TotalFee'    => $totalFee,
                'OrderPrice'  => $price,
                'BuyNum'      => $time,
                'IsYear'      => $tType,
                'OrderID'     => $orderID,
                'PowerID'     => $powerID
            ];

            //插入订单
            $return = $this->getModel('OrderList')->addOrder($param);
            if($return){
                //构造支付宝必需函数
                $param = [
                    'orderNum'    => $orderID,
                    'orderName'   => $orderName,
                    'orderDetail' => $orderDetail,
                    'totalFee'    => $totalFee,//0.01,//本地调试,可以将此处设置为0.01(1分),这样就可以不受前台选择的影响,而且订单也是正确的
                    'showUrl'     => C('WLN_HTTP') //可以为空,为空时取默认值
                ];
                useToolFunction('Alipay/Alipay','doPay',array($param));
                exit();
            }
            $this->setError('1X430',2);//插入数据库失败
            exit();
        }
        //验证是否登录
        $ifLogin  = 1;
        $checkRes = $this->checkLogin('Index',1);
        if($checkRes[0] != 1) $ifLogin = 0;
        $this->assign('ifLogin',$ifLogin);
        $this->assign('vipPrice',$vipPrice);
        $this->assign('superVipPrice',$superVipPrice);
        $this->assign('discount',$discount);
        $this->assign('slogan',$slogan);
        $this->assign('title','支付中心');
        $this->display();
    }

    /**
     * 支付完成返回界面
     * @author demo
     */
    public function orderResult(){
        $orderID  = $_GET['oid'];//返回的订单号
        if(!$orderID) {//缺少订单号
            $this->redirect('/');//跳转到首页
            exit();
        }
        $checkRes = $this->checkLogin('Index',1);
        if ($checkRes[0] != 1) {//未登录
            $this->redirect('/');//跳转到首页
            exit();
        }

        //查询该订单对应信息
        $result = $this->getModel('OrderList')->selectData(
            'UID,ReturnTotal,OrderName,NotifyTime,AliTradeStatus,IsView',
            ['OrderID'=>$orderID],
            '',
            1
        );
        if(!$result){//订单错误
            $this->redirect('/');//跳转到首页
            exit();
        }
        $result = $result[0];

        //验证用户合法性
        if($result['UID']!=$this->getCookieUserID('Home')){//非登录用户订单
            $this->redirect('/');//跳转到首页
            exit();
        }

        //验证订单是否已经查看过
        if($result['IsView']==1){
            $this->redirect('/Home/Index/main/u/User_Home_checkOrder');//跳转至用户订单列表页面
            exit();
        }

        //更新订单查看状态
        $this->getModel('OrderList')->updateData(
            ['IsView'=>1],
            ['OrderID'=>$orderID]
        );

        $orderStatus = 0;//订单状态
        if(trim($result['AliTradeStatus'])=='TRADE_SUCCESS'){//支付成功
            $orderStatus = 1;
        }
        $orderNameArr = explode('#',$result['OrderName']);
        $this->assign('orderStatus',$orderStatus);//订单状态
        $this->assign('orderID',$orderID);//订单号
        $this->assign('orderTime',$result['NotifyTime']);//订单完成时间
        $this->assign('orderName',$orderNameArr[0]);//订单前缀名
        $this->assign('orderPowerName',$orderNameArr[1]);//订单权限名称
        $this->assign('orderPowerTime',$orderNameArr[2]);//订单权限时长
        $this->assign('totalFee',$result['ReturnTotal']);//订单费用
        $this->assign('title','订单结果');
        $this->display();
    }

    /**
     * QQ登录调用接口
     * @author demo
     */
    public function QQLogin(){
        import('Common.Tool.sdk.ThinkOauth');
        $type = \Tool\sdk\ThinkOauth::getInstance('qq');
        redirect($type->getRequestCodeURL());exit();
        useToolFunction('QQLogin/QQLogin','QQLogin');
    }

    /**
     * QQ登录回调接口
     * @author demo
     */
    public function QQCallBack(){
        //###未授权提交数据处理
        if(IS_AJAX && IS_POST){
            $sq = $_SESSION['QC_userData'];//session中存储的授权信息

            if($sq && $sq['openid'] && $sq['access_token']){//判断是否存在授权

                //先通过QQAPI调取授权QQ信息,此处只是调用头像 授权页面让用户补全了手机信息,昵称采用之前方法生成
                import('Common.Tool.sdk.ThinkOauth');
                $sns  = \Tool\sdk\ThinkOauth::getInstance('qq',$sq);
                $info = $sns->call('user/get_user_info');
                $userPic = '';
                if($info['ret'] == 0){
                    $userPic = $info['figureurl_qq_2'];//用户QQ头像
                }

                //判断是绑定账户还是直接登录,(直接登录 == 新注册一个账户)
                if(isset($_POST['ifBind']) && $_POST['ifBind']){//绑定方式
                    $username = trim($_POST['username']);
                    $password = $_POST['password'];
                    //验证登录
                    $user=$this->getModel('User');
                    $userInfo=$user->login($username,$password,'UserName,Password,UserID,OrderNum,SaveCode,Whois,Logins');
                    if($userInfo[1]==1){//验证未通过
                        $this->setBack(array('error','帐号或者密码错误'));
                    }

                    //构建数据
                    $param = array();
                    $param['OrderNum']    = $userInfo['OrderNum'];
                    $param['OpenID']      = $sq['openid'];
                    $param['IfAssociate'] = 1;
                    $param['LoadTime']    = time();

                    //判断是否已经绑定
                    $userQqObj = $this->getModel('UserQq');
                    $existBind = $userQqObj->selectData('QID,OrderNum',['OrderNum'=>$userInfo['OrderNum']],'',1);
                    if(!$existBind){
                        //插入user_qq表
                        $res = $userQqObj->insertData($param);

                        if($res){
                            //删除QQ相关的SEESION
                            unset($_SESSION['QC_userData']);
                            //cookie处理
                            $system   = '组卷';
                            R('Common/UserLayer/setLoginCookie',array($userInfo,0));
                            if($userInfo['Whois']==0){//学生用户
                                $system   = '提分';
                            }
                            //更新用户头像
                            if($userPic){
                                $user->updateData(['UserPic'=>$userPic],['OrderNum'=>$userInfo['OrderNum']]);
                            }
                            //更改最后登录信息
                            $user->changeUserLoginInfo($userInfo['UserID'],$userInfo['Logins'],$userInfo['SaveCode']);
                            //记录日志
                            $this->userLog('用户登录', '用户【' . $userInfo['UserName'] . '】通过QQ登录'.$system.'系统',$userInfo['UserName']);
                            //返回正确信息
                            $this->setBack(array('success'));
                        }
                        $this->setBack(array('error','数据发生异常'));
                    }
                    $this->setBack(array('error','该帐号已经被绑定'));
                }else{//直接登录==新注册
                    $mobile      = $_POST['mobile'];//手机号
                    $mobileCode  = $_POST['mobileCode'];//手机验证码
                    $identity    = $_POST['identity'];//身份
                    if($identity!=0){//强制设置
                        $identity=1;
                    }

                    //验证手机号码
                    $output = R('Common/UserLayer/checkPhoneCode',array($mobile,$mobileCode,0,1));
                    if($output[0] == 1){ //验证失败
                        $this->setBack(array('error',C('ERROR_'.$output[1])));
                    }
                    //生成自增OrderNum
                    $orderNum  = $this->getModel('AutoInc')->getOrderNum();

                    $user = $this->getModel('User');
                    //构建数组
                    $param = array();
                    $param['UserName'] = $mobile;
                    $param['Nickname'] = R('Common/UserLayer/produceNickname',array());
                    $param['Password'] = md5($mobile . String('randString',10));//此处产生随机密码
                    $param['RealName'] = '';
                    $param['Sex']      = 0;
                    $param['Address']  = '';
                    $param['PostCode'] = '';
                    $param['Whois']    = $identity;
                    $param['LoadDate'] = time();
                    $param['LastTime'] = '';
                    $param['Logins']   = 0;
                    $param['LastIP']   = '';
                    $param['SaveCode'] = $user->saveCode();;
                    $param['IfShowTime'] = 0;
                    $param['OrderNum']  = $orderNum;
                    $param['Phonecode'] = $mobile;
                    $param['CheckPhone'] = 1;
                    if($userPic){//加入QQ头像
                        $param['UserPic'] = $userPic;
                    }
                    //插入user表
                    $res = $user->insertData($param);
                    if($res){//$res获得的新的uid
                        //加入默认权限组
                        $ipUser = array();
                        $this->getModel('UserGroup')->addDefaultGroupAtRegistration($res, $ipUser);

                        //插入user_QQ表
                        $Qparam = array();
                        $Qparam['OrderNum'] = $orderNum;
                        $Qparam['OpenID']   = $sq['openid'];
                        $Qparam['IfAssociate'] = 0;
                        $Qparam['LoadTime']    = time();

                        $this->getModel('UserQq')->insertData($Qparam);
                        //删除QQ相关的SEESION
                        $_SESSION['QC_userData']=array();

                        //cookie处理
                        $param['UserID'] = $res;//cookie要用到uid

                        $system   = '组卷';
                        R('Common/UserLayer/setLoginCookie',array($param,0));
                        if($identity==0){//学生用户
                            $system = '提分';
                        }
                        //更改最后登录信息
                        $user->changeUserLoginInfo($param['UserID'],$param['Logins'],$param['SaveCode']);
                        //记录日志
                        $this->userLog('用户登录', '用户【' . $param['UserName'] . '】通过QQ登录'.$system.'系统',$param['UserName']);

                        $this->setBack(array('success','登录成功!'));
                    }
                    $this->setBack(array('error','数据异常,请重试!'));
                }
            }
            $this->setBack(array('error','验证信息出错，请重试'));
        }
        //###未授权提交数据处理结束

        //调用回调函数 获取openID标识
        $code = $_GET['code'];
        import('Common.Tool.sdk.ThinkOauth');
        $sns  = \Tool\sdk\ThinkOauth::getInstance('qq');
        //请妥善保管这里获取到的Token信息，方便以后API调用
        //调用方法，实例化SDK对象的时候直接作为构造函数的第二个参数传入
        //如： $qq = ThinkOauth::getInstance('qq', $token);
        $backArray = $sns->getAccessToken($code);
        $_SESSION['QC_userData']=$backArray;
        $openID=$backArray['openid'];
        //在userqq表中查找该openid
        $userInfo = $this->getModel('UserQq')->selectData('OrderNum',['OpenID'=>$openID], '',1);

        //###已经授权的部分
        if(!empty($userInfo)){
            //中间跳转信息
            $user     = $this->getModel('User');
            $userInfo = $user->findData('UserID,UserName,Password,SaveCode,Whois,Logins',['OrderNum'=>$userInfo[0]['OrderNum']]);
            $system   = '组卷';
            //根据身份设置cookie信息
            R('Common/UserLayer/setLoginCookie',array($userInfo,0));
            if($userInfo['Whois']==0){//学生用户
                $system   = '提分';
            }
            //更改最后登录信息
            $user->changeUserLoginInfo($userInfo['UserID'],$userInfo['Logins'],$userInfo['SaveCode']);
            //记录日志
            $this->userLog('用户登录', '用户【' . $userInfo['UserName'] . '】通过QQ登录'.$system.'系统',$userInfo['UserName']);
            //直接跳转
            echo '<meta charset="UTF-8">正在登录...<script >setTimeout("window.location.href=\'' .C('WLN_HTTP') . '\'",20);</script>';
            exit();
        }
        //###已经授权结束

        //###未授权部分,显示默认页面
        $info  = $sns->call('user/get_user_info');
        if($info['ret'] == 0){
            $QQImg      = $info['figureurl_qq_2'];//QQ头像
            $qqNickname = $info['nickname'];//QQ昵称
            $this->assign('imgurl',$QQImg);
            $this->assign('nick',$qqNickname);
        }
        $this->assign('title','QQ快捷登录');
        $this->display();
    }

    /**
     * 支付宝通知地址 暂未使用
     * @author demo
     */
    public function getNotify(){
        useToolFunction('Alipay/Alipay','getNotify');
    }


    /**
     * 支付宝回调地址
     * @auhtor demo
     */
    public function getReturn(){
        useToolFunction('Alipay/Alipay','getReturn');
    }

    /**
     * 获取服务条款HTML
     * @author demo
     */
    public function getServiceTerm(){
        $getService=R('Common/UserLayer/getServiceTerm');
        $this->setBack($getService['data']);
    }

    /**
     * 上传试卷图片
     * @author demo 16-5-31
     */
    public function uploadZhenTiImg(){
        $user = $this->getCookieUserID('Home');
        if(empty($user)){
            $user = $this->getCookieUserID('Aat');
            if(empty($user)){
                $data = json_encode(array(
                    'status' => 'login'
                ));
                if(!IS_AJAX){
                    exit('<script>parent.uploadCallback('.$data.')</script>');
                }
                exit($data);
            }
        }
        $model = $this->getModel('SpecialImage');
        if(!IS_AJAX){
            $data = $model->uploadFile($_FILES['file'], $user, $_POST['Title']);
            exit('<script>parent.uploadCallback('.json_encode($data).')</script>');
        }
        $data = $model->upload($_POST, $user);
        exit(json_encode($data));
    }
	
	/**
     * ForClass第三方登录回调接口
     * @author demo
     */
    public function ForClassCalllogin(){
		//header("Content-type: text/html; charset=utf-8"); 
        $access_token = $_REQUEST['access_token'];
		
        $url = "http://web.forclass.net/OAuth/getuserinfo?access_token=".$access_token.'&expire_in=3600';
		$user=$this->getModel('User');
        $orderNum  = $this->getModel('AutoInc')->getOrderNum();
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        $data = curl_exec($ch);
        curl_close( $ch );
        if(!$data){
            $this->setBack(array('error','ForClass第三方登录认证失败！'));
        }

        $data = json_decode($data,true);
        $forClassID = $data['id'];  //这是forclass用户ID 
        $UserForclass = $this->getModel('UserForclass');
		//dump($forClassID);die;
        //dump($data);die;
        $existBind = $UserForclass->selectData('FID,ForClassUserID',['ForClassUserID'=>$forClassID],'',1);//dump($existBind);die;
        if(!$existBind){//没有用forclass用户登录，就新建一个tk账号和forclass账户绑定
            $param = array();
            $param['UserName'] = $data['username'];
            $param['Nickname'] = $data['username'];
            $param['Password'] = MD5($userName . '123456');//
            $param['RealName'] = $data['truename'];
            if($data['gender'] == 1){
                $sex = 0; //男
            }
            if($data['gender'] == 2){
                $sex = 1; //女
            }
            $param['Sex']      = $sex;
            $param['Address']  = '';
            $param['PostCode'] = '';
            if($data['user_type'] == 1){
                $identity = 0;//学生
            }
            if($data['user_type'] == 2){
                $identity = 1;//老师
            } 
            $param['Whois']    = $identity;
            $param['LoadDate'] = time();
            $param['LastTime'] = '';
            $param['Logins']   = 0;
            $param['LastIP']   = '';

            $param['SaveCode'] = $user->saveCode();;
            $param['IfShowTime'] = 0;
            $param['OrderNum']  = $orderNum;
            $param['Phonecode'] = '';
            $param['CheckPhone'] = 0;
            //插入user表
            $res = $user->insertData($param);
            if($res){//$res获得的新的uid
                //加入默认权限组
                $ipUser = array();
                $this->getModel('UserGroup')->addDefaultGroupAtRegistration($res, $ipUser);

                //插入user_Forclass表
                $Fparam = array();
                $Fparam['OrderNum'] = $orderNum;
                $Fparam['ForClassUserID']  = $forClassID;
                $Fparam['IfAssociate'] = 1;
                $Fparam['LoadTime']    = time();

                $this->getModel('UserForclass')->insertData($Fparam);

                //cookie处理
                $param['UserID'] = $res;//cookie要用到uid

                $system   = '组卷';
                R('Common/UserLayer/setLoginCookie',array($param,0));
                if($identity==0){//学生用户
                    $system = '提分';
                }
                //更改最后登录信息
                $user->changeUserLoginInfo($param['UserID'],$param['Logins'],$param['SaveCode']);
                //记录日志
                $this->userLog('用户登录', '用户【' . $param['UserName'] . '】通过QQ登录'.$system.'系统',$param['UserName']);

                $this->setBack(array('success','登录成功!'));
            }
    
        }else{ //已经绑定的用户
            $userInfo = $this->getModel('UserForclass')->selectData('OrderNum',['ForClassUserID'=>$forClassID], '',1);
			//dump($userInfo);die;
            //###已经授权的部分
            if(!empty($userInfo)){
                //中间跳转信息
                $user     = $this->getModel('User');
                $userInfo = $user->findData('UserID,UserName,Password,SaveCode,Whois,Logins,SubjectStyle,Version',['OrderNum'=>$userInfo[0]['OrderNum']]);
				//dump($userInfo);die;
                $system   = '组卷';
                //dump($userInfo );die;
                //根据身份设置cookie信息
                R('Common/UserLayer/setLoginCookie',array($userInfo,0));
                if($userInfo['Whois']==0){//学生用户
                    $system   = '提分';
                }
                //更改最后登录信息
                $user->changeUserLoginInfo($userInfo['UserID'],$userInfo['Logins'],$userInfo['SaveCode']);
                //记录日志
                $this->userLog('用户登录', '用户【' . $userInfo['UserName'] . '】通过ForClass登录'.$system.'系统',$userInfo['UserName']);
				 $this->setBack(array('success','登录成功!'));
                //直接跳转
                //echo '<meta charset="UTF-8">正在登录...<script >setTimeout("window.location.href=\'' .C('WLN_HTTP') . '\'",20);</script>';
                //exit();
            }
        }
    }
	
	/*安卓*/
	public function UserInfo(){
		$userName = $_GET['username'];
		$check = $_GET['check'];
		$mp = 'lF4!!GDcC!ji2NnM';
		if($check != $mp){
			echo json_encode('检查错误！');die;
		}
        if(!$userName){
            echo json_encode('请先登录！');die;
        }
        $rs = $this->getUserInfo($userName);
        unset($rs['InitArea']);
		$data =array(
			'status' => 'success',
			'data' =>$rs,
			);
        echo json_encode($data);die;
	}
}
?>