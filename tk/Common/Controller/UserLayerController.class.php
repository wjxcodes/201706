<?php
/**
 * @author demo
 * @date 2015年6月7日
 */

/**
 * 基础控制器类，用于处理官网基础数据相关操作
 */
namespace Common\Controller;
class UserLayerController extends CommonController{
    /**
     * 获取服务协议
     * @return string
     * @author demo
     */
    public function getServiceTerm(){
        $output     =array();
        $serviceTerm=$this->fetch('Common@/serviceTerm');
        if(empty($serviceTerm)){
            $output['info']='error';
        }
        else{
            $output['info']='success';
            $output['data']=$serviceTerm;
        }
        return $output;
    }

    /**
     * 设置登录后Cookie信息
     * @param array $data 需要设置的cookie内容
     * @param bool $ifSave 是否保持长期登录 默认false
     * @author demo
     */
    public function setLoginCookie($data,$ifSave=false){
        $cookieTime = 24 * 3600;
        if($ifSave) {
            $cookieTime = 7 * 24 * 3600;
        }
        if($data['Whois'] == 1){//教师
            $mainModule = 'Home';
            if(!cookie('SubjectId')){
                cookie('SubjectId',$data['SubjectStyle'],$cookieTime);
            }
        }else{//学生
            $mainModule = 'Aat';
            $this->setCookieVersionID($data['Version'],31104000,'Aat');
        }
        $time=C('WLN_COOKIE_TIMEOUT');
        $userCode=md5($data['UserID'].$data['UserName'].$data['SaveCode'].ceil(time()/$time));
        $this->setCookieUserName($data['UserName'],$cookieTime,$mainModule);
        $this->setCookieUserID($data['UserID'],$cookieTime,$mainModule);
        $this->setCookieCode($userCode,$cookieTime,$mainModule);
    }

    /**
     * 退出功能
     * @param int $who 身份，1为教师，0为学生
     * @author demo
     */
    public function loginOut($who){
        if($who==1){//组卷
            $mod = 'Home';
        }else if($who==0){
            $mod = 'Aat';
        }
        $this->setCookieUserName(null,null,$mod);
        $this->setCookieCode(null,null,$mod);
        $this->setCookieUserID(null,null,$mod);
        $this->setCookieTime(null,null,$mod);
    }
    /**
     * 验证码显示
     * @param $imageMsg array 验证码设置属性
     * @author demo
     */
    public function verify($imageMsg=''){
        import("Common.Tool.Image");
        if(!empty($imageMsg)){
            extract($imageMsg);
            \Image :: buildImageVerify($total,$num,$style,$width,$height,$action);
        }else{
            \Image :: buildImageVerify();
        }
    }

    /**
     * 产生随机字符串
     * @param int $type 字符串类型
     * @param int $len 字符串长度
     * @return string
     * @author demo
     */
    protected function setCode($type=1,$len=6){
        return string('randString',$len,$type);
    }

    /**
     * 发送短信验证码
     * @param string $phoneNum 手机号
     * @param string $imgCode 图形验证码
     * @param int $userID 用户id
     * @return array
     * @author demo
     */
    public function sendPhoneCode($phoneNum,$imgCode='phoneList',$userID=0){

        //图形验证码
        if(strlen($imgCode) != 4 && $imgCode!='phoneList'){
            return array(1,'30105'); //请填写正确的验证码！
        }

        if(!$userID){//防止传递过来的userID为null
            $userID = 0;
        }
        /*//验证电话是否是当前用户的
        $user      =D('User');
        $userBuffer=$user->getInfoByID($userID,'Phonecode');
        if($userBuffer['Phonecode'] != $phoneNum){
            return array(1,'30106'); //手机验证码发送失败！请重试。
        }*/
        //检验用户提交的手机号是否重复（兼容用户初始手机号为空和用户注册）
        $user=$this->getModel('User');
        $repeatError=$user->checkUser('',$phoneNum,'',$userID);
        if(!empty($repeatError)){
            return array(1,$repeatError);//已被其他用户占用
        }

        return $this->sendPhoneCodeOnly($phoneNum,$userID);
    }
    /**
     * 发送手机验证码
     * @suthor
     */
    public function sendPhoneCodeOnly($phoneNum,$userID){
        if(C('WLN_SEND_AUTH_MOBILE')==0){
            return array(1,'30108'); //未开启手机短信验证功能！
        }

        $mCode=$this->setCode(1,6);

        $logSms  = $this->getModel('LogSms');

        //验证用户最近一条发送时间是否间隔一分钟
        $sendLast=$logSms->getLastSendLog($phoneNum);
        if($sendLast['AddTime']>time()-60){
            return array(1,'30115'); //短信验证码发送频繁！
        }


        //验证用户每小时内发送手机短信个数(修改为验证同一个手机号每小时内发送的手机短信个数)
        $sendTimes=$logSms->smsLogCount($phoneNum,1,strtotime(date('Y-m-d',time())));
        if($sendTimes >= C('WLN_SEND_AUTH_TIMES')){
            return array(1,'30107'); //验证次数已超出！请明天再试。
        }


        //发送短信
        $phoneSms= useToolFunction('Sms/SmsModel','','');
        $status = $phoneSms->sendTemplateSMS($phoneNum, array($mCode,'5'),34195);
        //写入日志记录
        $data           =array();
        $data['UserID'] =$userID;
        $data['Content']=$status[1];
        $data['Receive']=$phoneNum;
        $data['Status'] =$status[0];
        $data['Type']   =1;
        $data['CodeNum']=$mCode;
        $logSms->smsLog($data);

        //发送失败
        if($status[0]){
            return array(1,'30106'); //手机验证码发送失败！请重试。
        }

        return array(0); //发送成功
    }

    /**
     * 验证短信验证码
     * @param string $phoneNum 手机号
     * @param int $code 短信验证码
     * @param int $userID 用户ID
     * @param int $status 状态
     * @return array
     * @author demo
     */
    public function checkPhoneCode($phoneNum,$code,$userID,$status){
        if(C('WLN_SEND_AUTH_MOBILE')==0){
            return array(1,'30108'); //未开启手机短信验证功能！
        }

        if(strlen($code) != 6){
            return array(1,'30102'); //验证码有误！
        }

        //短信发送时间
        $logSms   =$this->getModel('LogSms');
        if($userID) {
            $logBuffer = $logSms->getCodeSendTime($userID, $code, 1);
        }else{
            $logBuffer = $logSms->getCodeByReceive($phoneNum,$code,1);
        }
        if(empty($logBuffer['AddTime']) || $logBuffer['CodeNum'] != $code){
            return array(1,'30102'); //短信验证码有误！
        }

        //验证短信过期时间
        $outTime=C('WLN_SEND_AUTH_OUT_TIME') * 60; //过期的秒数

        if($logBuffer['AddTime'] + $outTime < time()){
            return array(1,'30109'); //验证码已过期！
        }

        //修改数据库手机验证状态
        if($userID) {
            $data['CheckPhone'] = $status;
            $data['Phonecode'] = $phoneNum;//兼容用户初始手机号为空和修改已认证手机的情况
            $user = $this->getModel('User');
            $user->changeUserData($userID, $data);
        }
        //修改短信日志数据状态IfConfirm
        $logSms->setIfConfirm($logBuffer['SmsID']);

        return array(0);
    }

    /**
     * 发送邮箱验证码
     * @param string $email 邮箱地址
     * @param string $userID 用户ID
     * @return Array
     * @author demo
     */
    public function sendEmailCode($email,$userID){
        if(C('WLN_SEND_AUTH_EMAIL')==0){
            return array(1,'30110'); //未开启邮箱验证功能！
        }
        $user=$this->getModel('User');
        $repeatError=$user->checkUser('','',$email,$userID);
        if($repeatError == '30225'){
            return array(1,'30225');//已被其他用户占用
        }
        $mCode=$this->setCode(1,6);//获取验证码
        $logSms  =$this->getModel('LogSms');
        //验证用户每小时内发送邮件个数
        $sendTimes=$logSms->smsLogCount($email,2,strtotime(date('Y-m-d',time())));
        if($sendTimes >= C('WLN_SEND_AUTH_TIMES')){
            return array(1,'30107'); //验证次数已超出！请明天再试。
        }
        $userBuffer=$user->getInfoByID($userID,'UserName');
        //发送邮件

        $mails = useToolFunction('PHPMailer/MailModel','','');

        $ret   = $mails->getEmailMcode($email,$mCode,$userBuffer['UserName'],'邮箱验证 - 题库平台');
        $status='1'; //发送状态，0成功，1未成功
        if($ret == 'success'){
            $status='0';
        }else{
            $this->getModel('LogError')->setLine(array('description'=>$ret));
        }

        //写入日志记录
        $data           =array();
        $data['UserID'] =$userID;
        $data['Content']=$userBuffer['UserName'].'<br/>验证码：'.$mCode;
        $data['Receive']=$email;
        $data['Status'] =$status;
        $data['Type']   =2;
        $data['CodeNum']=$mCode;
        $logSms->smsLog($data);

        //发送失败
        if($status==1){
            return array(1,'30111'); //邮箱验证码发送失败！请重试。
        }

        return array(0); //发送成功
    }

    /**
     * 验证邮箱验证码
     * @param string $email 邮箱
     * @param string $code 验证码
     * @param string $userID 用户ID
     * @param string $status 状态 默认为1（认证），2为修改已认证邮箱时状态
     * @return Array
     * @author demo
     */
    public function checkEmailCode($email,$code,$userID,$status){
        if(C('WLN_SEND_AUTH_EMAIL')==0){
            return array(1,'30110'); //未开启邮箱验证功能！
        }

        if(strlen($code) != 6){
            return array(1,'30103'); //验证码有误！
        }

        //获取验证码信息
        $logSms   =$this->getModel('LogSms');
        if($userID) {
            $logBuffer = $logSms->getCodeSendTime($userID, $code, 2);
        }else{
            $logBuffer = $logSms->getCodeByReceive($email,$code,2);
        }
        if(empty($logBuffer['AddTime']) || $logBuffer['CodeNum'] != $code || $logBuffer['Receive'] != $email){
            return array(1,'30103'); //验证码有误！
        }

        //验证短信过期时间
        $outTime=C('WLN_SEND_AUTH_OUT_TIME') * 60; //过期的秒数

        if($logBuffer['AddTime'] + $outTime < time()){
            return array(1,'30109'); //验证码已过期！
        }
        if($userID) {
            $data['CheckEmail'] = $status;
            //兼容用户初始邮箱为空和修改已验证邮箱的情况
            $data['Email'] = $email;
            $user = $this->getModel('User');
            $user->changeUserData($userID, $data);
        }
        //修改短信日志数据状态IfConfirm
        $logSms->setIfConfirm($logBuffer['SmsID']);

        return array(0);
    }

    /**
     * 登录页面
     * @author demo 2015-8-31
     */
    public function loginHtml(){
        $this->display("../Public/login");
    }

    /**
     * 根据用户属性判断对用户名称处理
     * @param string $userName 用户名
     * @param string $realName 用户真实名
     * @param int $whoIs=0 用户属性（默认是学生）
     * @return  string $resultName 处理后的用名
     * @author demo
     */
    public function showUserName($userName,$realName,$whoIs){
/*        //判断首字母是中文
        if(preg_match("/^[\x81-\xfe][\x40-\xfe]?/",$realName)){
            //判断是教师
            if($whoIs==1){
                if(!empty($realName)){
                    $resultName=mb_substr($realName,0,1,'utf-8').'老师';
                }
            }else{
                //处理学生
                if(!empty($realName)){
                    $resultName=mb_substr($realName,0,1,'utf-8').'同学';
                }
            }
        }*/
        //直接处理隐藏用户名部分内容
        if(empty($resultName)){
            $resultName=formatString('hiddenUserName',$userName);
        }
        return $resultName;
    }

    /**
     * 检查字段内容是否重复及合法
     * @param array $field 字段内容
     * @return bool
     * @author demo
     */
    public function checkField($field){
        //检验是否合法（用户名、真实姓名和昵称需要检验是否合法）
        //检验是否重复（用户名，昵称，手机号，邮箱号）
        //检验字段长度是否符合（用户名，真实姓名，昵称）
        //正则检验（手机号，邮箱）
        //确定哪些字段是不能重复的字段
        if(!is_array($field) || empty($field)){
            return false;
        }
        $err=array();
        $where=array();
        $content=array();
        $user=$this->getModel('User');
        foreach($field as $i=>$iField){
            if($i=='Nickname' || $i=='RealName'){
                $iField = formatString('stripTags',$iField);//去掉html标签
                $length = strlen($iField);
                if($i=='Nickname' && ($length<4 || $length>15)){
                    $err[$i]='30218';
                }
                if($i=='RealName' && ($length<4||$length>30)){
                    $err[$i]='30216';
                }
                if(!$err[$i]) {
                    //检查用户名合法
                    $backStr = $user->NameFilter($iField);
                    if ($backStr['errornum'] != 'success') {
                        $err[$i] = array($backStr['errornum'],$backStr['replace']);
                    }
                }
            }
            if($i=='Phone'){//正则验证
                if(!checkString('checkIfPhone', $iField)){
                    $err[$i]='30211';
                }
            }
            if($i=='Email'){//正则验证
                if(!checkString('checkIfEmail', $iField)){
                    $err[$i]='30212';
                }
            }
            $where[$i]=$iField;
            $content[]=$i;
        }
        $content[]='IfShowTime';
        $content[]='UserID';
        $where['_logic']='OR';
        $result=$user->getInfoByWhere($content,$where);
        if($result[0]['IfShowTime']==1){
            return $result[0]['UserID'];
        }

        if(!empty($result)){
            foreach($result as $iResult){
                if($iResult['UserName']==$field['UserName'] && $field['UserName']){
                    if(!$err['UserName']) {
                        $err['UserName'] = '30222';
                    }
                }
                if($iResult['Nickname']==$field['Nickname'] && $field['Nickname']){
                    if(!$err['Nickname']){
                        $err['Nickname']='30217';
                    }
                }
                if($iResult['Phonecode']==$field['Phonecode'] && $field['Phonecode']){
                    if(!$err['Phonecode']) {
                        $err['Phonecode'] = '30224';
                    }
                }
                if($iResult['Email']==$field['Email'] && $field['Email']){
                    if(!$err['Email']) {
                        $err['Email'] = '30225';
                    }
                }
            }
        }
        return $err;
    }

    /**
     * 生成五个汉字的昵称
     * @return string
     * @author demo
     */
    public function produceNickname(){
        include_once(APP_PATH.'Common/Common/nameWord.php');
        $xCount=count($_nameWord[0]);
        $mCount=count($_nameWord[1]);
        $xName=$_nameWord[0][mt_rand(0,$xCount)];
        $mName=$_nameWord[1][mt_rand(0,$mCount)];
        $xNameLength=strlen($xName);//形容词长度
        $mNameLength=strlen($mName);//名词长度
        $zLength=$xNameLength+$mNameLength;//总长度
        //如果形容词过长，或者名称过长，
        //会导致整体昵称长度超出
        $nickname='';
        if($xNameLength<=6 && $mNameLength<=6){
            $nickname=$xName.'的'.$mName;
        }else if($zLength==15){
            $nickname=$xName.$mName;
        }else if($zLength>15){//需要截取字符串
            $nickname=substr($xName.$mName,0,15);
        }
        if(!$nickname || strlen($nickname)!=15){//防止出现错误的名称
            return $this->produceNickname();
        }else {
            $user=$this->getModel('User');
            $where['Nickname']=$nickname;
            $result=$user->getInfoByWhere('UserID',$where);
            if ($result) {//已存在
                return $this->produceNickname();
            }else{
                return $nickname;
            }
            return $nickname;
        }
    }

    /**
     * 描述：用户登录失败次数过多限制处理
     * [使用方法]：1、在用户登录前调用check检测2、登录成功后调用success3、登录失败后调用failed
     * [注意]：1、放到密码检测的前面2、放在提示用户是否存在前面3、登录失败包括用户名不存在和密码错误，两种都要记录
     * [处理逻辑]：1、同一IP下不存在的用户名尝试登录，所有账户半小时1000次限制2、同一IP下存在用户或者不同IP存在用户，单个
     * 用户限制半小时30次，更换用户后重新计数3、IP下登录成功后IP不存在用户计数清零4、单个用户登录成功后，计数1清零
     * @param int $userID 用户ID [重要]：如果登录失败时或者检测时，没有该用户，取值0
     * @param string $type 取值： check 检测  success 登录成功 failed登录失败
     * @return string
     * @author demo
     */
    public function loginFailedProcess($userID,$type){
        $logLoginM = $this->getModel('LogLogin');
        $config = C('WLN_LOGIN_FAILED_CHECK');
        $userID = $userID?$userID:0;
        if($type=='check'){
            if($userID==0){
                //用户不存在
                $count = $logLoginM->getIpCount($time=$config['IP']['TIME']);
                $configCount = $config['IP']['COUNT'];
            }else{
                //用户存在
                $count = $logLoginM->getUserIDCount($userID,$time=$config['USER']['TIME']);
                $configCount = $config['USER']['COUNT'];
            }
            return $count>=$configCount?'error':'success';
        }elseif($type=='success'){
            $logLoginM->delLoginFailedRecord($userID);
        }elseif($type=='failed'){
            $logLoginM->addLoginFailedRecord($userID);
        }
    }

    /**
     * 检查找回密码的信息是否正确
     * 如果正确，返回一些用户信息
     * 如果不正确，返回错误码
     * @param $userStyle int 用户找回密码的方式
     * @param $userName string 用输入的信息
     * @return array
     * @author demo
     */
    public function checkGetPasswordInfo($userStyle,$userName){
        $user = $this->getModel('User');
        //根据找回密码的方式确定查找用户信息的条件
        $where=array();
        if($userStyle=='Email'){//查找邮箱
            $where['Email']=$userName;
        }else if($userStyle=='Phonecode'){//查找手机号
            $where['Phonecode']=$userName;
        }else{//查找用户名
            $where['UserName']=$userName;
        }

        //查找用户信息
        $result = $user->selectData('UserID,UserName,Phonecode,Email',$where);

        if($result){
            if($result[0]['Phonecode']=='' && checkString('checkIfPhone', $result[0]['UserName'])){
                $result[0]['Phonecode']=$result[0]['UserName'];
            }
            if($result[0]['Email']=='' && checkString('checkIfEmail', $result[0]['UserName'])){
                $result[0]['Email']=$result[0]['UserName'];
            }

            //判断用户是否有可以用来找回密码的工具
            if($result[0]['Phonecode']=='' && $result[0]['Email']==''){
                return array(1,'30732');
            }
            //组合返回的信息
            $data['userID'] = $result[0]['UserID'];

            foreach($result as $i => $iResult){
                if($iResult['Phonecode']) {
                    $data['phone'] = $iResult['Phonecode'];
                }
                if($iResult['Email']) {
                    $data['email'] = $iResult['Email'];
                }
                //如果用户名是手机号时，覆盖data[0]中的数据
                if(checkString('checkIfPhone', $iResult['UserName'])){
                    $data['phone'] = $iResult['UserName'];
                }
                //如果用户名是邮箱号时，覆盖data[1]中的数据
                if(checkString('checkIfEmail', $iResult['UserName'])){
                    $data['email'] = $iResult['UserName'];
                }
            }
            return array(0,$data);
        }else{
            return array(1,'30214');
        }
    }

    /**
     * 设置用户密码
     * @param $userID int 用户ID
     * @param $password string 第一次输入的用户密码
     * @param $password1 string 第二次输入的用户密码
     * @return array
     * @author demo
     */
    public function setPassword($userID,$password,$password1){

        if($password=='' || $password1==''){
            return array(1,'30202');
        }
        if($password!=$password1){
            return array(1,'30207');
        }
        if(strlen($password)<6 || strlen($password)>18){
            return array(1,'30221');
        }

        //重置密码
        $user=$this->getModel('User');
        $userName=$user->getInfoByID($userID,'UserName')['UserName'];
        $data=array();
        $data['SaveCode'] = $user->saveCode();
        $data['Password'] = md5($userName.$password);
        if($user->changeUserData($userID,$data)==false){
            return array(1,'30709');
        }
        $backArray=array('userName'=>$userName,'userID'=>$userID);
        return array(0,$backArray);
    }
}