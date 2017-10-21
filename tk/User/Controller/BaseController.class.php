<?php
/**
 * @author demo
 * @date 2015年11月9日
 */
/**
 * 分组基类组卷类，用于处理分组通用操作
 */
namespace User\Controller;
use Common\Controller\DefaultController;
class BaseController extends DefaultController {

    /**
     * 获取用户基础信息
     * @param string $userName 用户名
     * @return array
     * @author demo
     */
    public function getUserInfo($userName){
        $userModel = $this->getModel('User');
        $where['UserName'] = $userName;
        $userInfo = $userModel->selectData(
            'UserID,UserName,NickName,RealName,Whois,Logins,LastTime,LastIP,Times,UserPic,SchoolID,GradeID,AreaID,
            IfAuth,SubjectStyle,Cz',
            $where
        )[0];
        //地区
        $areaInfo = $this->getUserArea($userInfo['AreaID']);
        //学校名称
        $userInfo['SchoolName'] = $this->getUserSchool($userInfo['SchoolID']);
        //年级
        $userInfo['GradeName'] = $this->getUserGrade($userInfo['GradeID']);
        //学科
        $userInfo['SubjectName'] = $this->getUserSubjectName($userInfo['SubjectStyle']);
        //IP权限
        $userInfo['NowPowerName'] = $this->getUserIPPower();
        //是否认证
        $tmp = array('未认证','认证中','已认证','认证未通过');
        $userInfo['AuthTitle'] = $tmp[$userInfo['IfAuth']];
        //显示的用户名
        $userInfo['OkName']=R('Common/UserLayer/showUserName',array($userInfo['UserName'],$userInfo['RealName'],$userInfo['Whois']));
        //用户权限
        $userPower = $userModel->getUserPower($userInfo['UserID'],$userInfo['Whois']);
        //用户头像
        $userInfo['UserPic'] = $userModel->getUserPic($userInfo['UserPic']);
        //用户剩余点数
        $userInfo['ChargeLeave'] = $userInfo['Cz']?$userInfo['Cz']:0;
        //上次登录信息
        $lastInfo = $this->getLastTimeInfo($userInfo['UserName']);
        $userInfo['OldTime'] = $lastInfo['LastTime'];
        $userInfo['OldIP'] = $lastInfo['LastIP'];
        $userInfo['LoginTime'] = date('Y-m-d H:i:s',$userInfo['LastTime']);
        $newUserInfo = array_merge($userInfo,$userPower,$areaInfo);
        return $newUserInfo;
    }
    /**
     * 获取学校名称
     * @param int $schoolID 学校ID
     * @return string
     * @author demo
     */
    public function getUserSchool($schoolID){
        if(!$schoolID){
            return '';
        }
        $school=$this->getModel('School');
        $tmpArray=$school->getSchoolById($schoolID);
        if($tmpArray){
            return $tmpArray['SchoolName'];
        }
        return '';
    }

    /**
     * 获取地区下的学校列表
     * @param string $field 获取学校信息的字段
     * @param int $areaID 地区ID
     * @return mixed
     * @author demo
     */
    public function getSchoolListByAreaID($field,$areaID){
        $schoolModel = $this->getModel('School');
        $where['AreaID'] = $areaID;
        //初始化学校
        $schoolList = $schoolModel->selectData($field,$where);
        if(!$schoolList){
            return '';
        }
        return $schoolList;
    }

    /**
     * 获取用户地区信息
     * @param int $areaID 地区ID
     * @return array
     * @author demo
     */
    public function getUserArea($areaID){
        //查询areaid下的area数据 和 school数据
        if(!$areaID){
            return array();
        }
        $areaModel=$this->getModel('Area');
        $tmpStr=$areaModel->getAreaPathById($areaID);
        if($tmpStr) {
            $areaInfo['AreaStr']=$tmpStr;
        }
        //初始化地区
        $areaInfo['InitArea']=$areaModel->areaSelectByID($areaID);
        return $areaInfo;
    }

    /**
     * 获取学科名称
     * @param int $subjectID 学科ID
     * @return string
     * @author demo
     */
    public function getUserSubjectName($subjectID){
        if(!$subjectID){
            return '';
        }
        $subject=SS('subject');;
        return $subject[$subjectID]['SubjectName'];
    }

    /**
     * 获取年级名称
     * @param int $gradeID 年级ID
     * @return string
     * @author demo
     */
    public function getUserGrade($gradeID){
        $grade=SS('grade');
        $gradeName=$grade[$gradeID]['GradeName'];
        if($gradeName){
            return $gradeName;
        }
        return '';
    }

    /**
     * 获取上一次登录信息
     * @param string $userName 用户名
     * @return array
     * @author demo
     */
    public function getLastTimeInfo($userName){
        $filed = 'IP,LoadDate,Content';
        $where = array(
            'Module' => '用户登录',
            'IfAdmin' => 0,
            'UserName' =>$userName
        );
        //查询上次登录信息
        $bufferLog=$this->getModel('Log')->selectData(
            $filed,
            $where,
            'LogID desc',
            5
        );
        //去除退出日志的干扰
        foreach($bufferLog as $iBufferLog){
            if(stristr($iBufferLog['Content'],'登录')){
                $loginLogTrue[]=$iBufferLog;
            }
        }
        $lastInfo['LastIP'] = $loginLogTrue[1]['IP'];
        if(empty($loginLogTrue[1]['LoadDate'])) {
            //如果是第一次登录，就使用本次登录信息
            $lastInfo['LastIP'] = $loginLogTrue[10]['IP'];
            $loginLogTrue[1]['LoadDate'] = $loginLogTrue[0]['LoadDate'];
        }
        $lastInfo['LastTime']=date('Y-m-d H:i:s',$loginLogTrue[1]['LoadDate']);
        return $lastInfo;
    }


    //获取用户IP权限
    public function getUserIPPower(){
        $nowIP=ip2long(get_client_ip(0,true));
        $ipMsg=$this->getModel('UserIp')->selectData(
            'LastTime,PUID',
            'IPAddress='.$nowIP
        );
        if($ipMsg){
            $homePower=SS('powerUserGroup')[1];
            //特殊权限到期
            if(time() >= $ipMsg[0]['LastTime']){
                return '';
            }
            $thisGroup=explode(',',$ipMsg[0]['PUID']);
            return $homePower['groupList'][$thisGroup[0]]['UserGroup'];
        }
        return '';
    }

    /**
     * 获取认证信息
     * @param int $userID 用户ID
     * @param int $ifAuth 是否认证标识
     * @return mixed
     * @author demo
     */
    public function getUserAuth($userID,$ifAuth){
        //认证中 未通过 显示最后一次认证信息
        $field = 'IDNumber,UserID,Qualification,QuaPicSrc,Grade,GradePicSrc,AuthTime';
        if($ifAuth == 1 || $ifAuth ==3){
            $authData = $this->getModel('TeacherAuthinfo')->getList(
                $field,
                'UserID = '.$userID,
                'ID DESC',
                '1'
            )[0];
            if($authData){
                return $authData;
            }
        }
        return '';
    }

    /**
     * 保存用户信息
     * @param array $userData 用户本身信息，包含用户ID，用户名，密码
     * @param array $postData 用户提交的信息
     * @return array
     * @author demo
     */
    public function saveUserInfo($userData,$postData){
        $err=array();//错误信息
        $flag=0;//是否有错误信息
        $updateData = array();//需要更新的数据
        $userID = $userData['UserID'];//用户ID
        $userModel=$this->getModel('User');
        //真实姓名
        if($postData['RealName']){
            $realName  = formatString('stripTags',$postData['RealName']);
            if(strlen($realName)<1){//验证真实姓名长度
                $err['RealName']='30216';
                $flag=1;
            }
            //检查用户名合法
            $backStr=$userModel->NameFilter($realName);
            if($backStr['errornum']!='success'){
                $backStr=$this->setError($backStr['errornum'],2,'',$backStr['replace']);
                $err[]='RealName|'.str_replace('error|','',$backStr);
                $flag=1;
            }
            if(!$flag) {
                $updateData['RealName'] = $realName;
            }
        }
        //密码相关
        if($postData['Passwordy'] != ''){//原密码
            $oldPassword = $postData['Passwordy'];
            $password  = formatString('changeStr2Html',trim($postData['Password']));
            $password2 = formatString('changeStr2Html',$postData['Password2']);
            if(strlen($oldPassword) < 6){
                $err['Passwordy']='30208';
                $flag=1;
            }else{
                if($userData['Password']!=md5($postData['UserName'].$oldPassword)){
                    $err['Passwordy']='1X403';
                    $flag=1;
                }
            }
            if(strlen($password)<6 || strlen($password)>18){
                $err['Password']='30221';
                $flag=1;
            }
            if($password!=$password2){
                $err['Password2']='30207';
                $flag=1;
            }
            if(!$flag){
                $updateData['Password']=md5($userData['UserName'].$password);
                $updateData['SaveCode'] = $userModel->saveCode();
            }
        }
        //用户昵称
        if($postData['Nickname']){
            $nickname = formatString('stripTags',$postData['Nickname']);
            if (strlen($nickname) < 4 || strlen($nickname) > 15 ) {
                $err['Nickname'] = '30218';
                $flag = 1;
            }
            //检查用户名合法
            $backStr=$userModel->NameFilter($nickname);
            if($backStr['errornum']!='success'){
                $backStr=$this->setError($backStr['errornum'],2,'',$backStr['replace']);
                $err['Nickname']= str_replace('error|','',$backStr);
                $flag=1;
            }
            if(!$flag) {
                $updateData['Nickname'] = $nickname;
            }
        }
        //手机号
        if($postData['Phonecode']) {//兼容补全信息弹框和用户中心的修改信息
            $phoneCode = formatString('stripTags',$postData['Phonecode']);
            if (strlen($phoneCode) < 6 || strlen($phoneCode) > 15 || preg_replace('/[0-9\-]*/i', '', $phoneCode)) {
                $err['Phonecode'] = '30211';
                $flag = 1;
            }
            //验证邮箱是否重复
            $checkInfo = $userModel->checkUser('',$phoneCode,'',$userID);
            if($checkInfo){
                $err['Phonecode'] = $checkInfo;
                $flag = 1;
            }
            if(!$flag) {
                $updateData['Phonecode'] = $phoneCode;
                if ($postData['MessageCode']) {//因送书活动导致补全信息而加入的手机短信验证
                    $messageCode = $postData['messageCode'];
                    //验证手机验证码是否正确
                    $output = R('Common/UserLayer/checkPhoneCode', array($phoneCode, $messageCode, $userData['UserID'], 1));
                    if ($output[0] == 1) {
                        $err['messageCode'] = $output[1];
                        $flag = 1;
                    }
                }
            }
        }
        //邮箱
        if($postData['Email']) {//兼容补全信息弹框和用户中心的修改信息
            $email = formatString('stripTags',$postData['Email']);
            if (!strstr($email, '@') || !strstr($email, '.')) {
                $err['Email'] = '30212';
                $flag = 1;
            }
            //验证邮箱是否重复
            $checkEmailInfo = $userModel->checkUser('','',$email,$userID);
            if($checkEmailInfo){
                $err['Email'] = $checkEmailInfo;
                $flag = 1;
            }
            if(!$flag) {
                $updateData['Email'] = $email;
            }
        }
        //详细地址
        if($postData['Address']){
            $address = formatString('changeStr2Html',$postData['Address']);
            if(strlen($address)<5){
                $err['Address']='1X402';
                $flag=1;
            }
            if(!$flag) {
                $updateData['Address'] = $address;
            }
        }
        //地区
        if($postData['AreaID']){
            if($postData['AreaID'] <1 || !is_numeric($postData['AreaID'])){
                $err['AreaID']='30727';
                $flag=1;
            }
            if(!$flag) {
                $updateData['AreaID'] = $postData['AreaID'];
            }
        }
        //学校
        if($postData['SchoolID']){
            if($postData['SchoolID'] < 1 || !is_numeric($postData['SchoolID'])){
                $err['SchoolID']='30736';
                $flag=1;
            }
            if(!$flag) {
                $updateData['SchoolID'] = $postData['SchoolID'];
            }
        }
        //年级
        if($postData['GradeID']){
            if($postData['GradeID']<1 || !is_numeric($postData['GradeID'])){
                $err['GradeID']='30735';
                $flag=1;
            }
            if(!$flag) {
                $updateData['GradeID'] = $postData['GradeID'];
            }
        }
        //学科
        if($postData['SubjectID']){
            if($postData['SubjectID']<1 || !is_numeric($postData['SubjectID'])){
                $err['SubjectID']='30508';
                $flag=1;
            }
            if(!$flag) {
                $updateData['SubjectStyle'] = $postData['SubjectID'];
            }
        }

        if($flag==1){//如果有错误，将错误返回
            return array(0,$err);
        }

        if($userModel->updateData($updateData,'UserID='.$userID)===false){
            return array(0,'30307');
        }
        return array(1,'success');
    }

    /**
     * 为指定用户增加五年最高权限
     * @param $userID
     * @return array
     * @author demo
     */
    protected function addFiveYearPower($userID){
        //当前时间
        $userGroup = $this->getModel('UserGroup');
        $where = array(
            'UserID' =>$userID,
            'GroupName' =>1
        );
        $result = $userGroup->selectData('GroupID',$where);
        //只有普通用户可以参加
        if(empty($result) || $result[0]['GroupID'] != 44) {
            $lastTime = strtotime("+5 year", strtotime(date('Y-m-d', time())));
            $config = array(
                'GroupID' => 44,
                'LastTime' => $lastTime
            );
            $output = $userGroup->saveDefaultGroup($userID, $config);
            if($output === false){
                return array(0,'');
            }
        }
        return array(1,'success');
    }


    /**
     * 改变用户版本（提分端使用）
     * @param int $version 版本号（取值1或者2）
     * @param string $userName 用户名
     * @return array
     * @author demo
     */
    public function changeVersion($version,$userName){
        $result = $this->getApiUser('User/changeVersion', $version, $userName);
        return array($result['status'], $result['info']);
        // if($version != 1 && $version != 2){
        //     return array(0,'30301');//数据标识错误
        // }
        // $updateData['Version'] = $version;
        // $where['UserName'] = $userName;
        // $update = $this->getModel('User')->updateData(
        //     $updateData,$where
        // );
        // if ($update !== false) {//$update 可能等于零
        //     return array(1,'success');
        // }else{
        //     return array(0,'30311');
        // }
    }

    /**
     * 网络与数字对应关系
     * @param int $number 数字
     * @return string
     * @author demo
     */
    protected function netWork($number){
        switch($number){
            case -1:
                return '未知网络';
                break;
            case 0:
                return '无网络';
                break;
            case 1:
                return 'WIFI';
                break;
            case 2:
                return '2G';
                break;
            case 3:
                return '3G';
                break;
            case 4:
                return '4G';
                break;
            default :
                return 'error';
        }
    }

    /**
     * 获取home端cookie名称
     * @author demo
     */
    protected function teacherCookieName($name,$value='-1',$time=''){
        $cookieName='';
        switch($name){
            case 'userID': //用户id
                $cookieName=C('WLN_HOME_USER_AUTH_KEY').'_UID';
                break;
            case 'userName': //用户名
                $cookieName=C('WLN_HOME_USER_AUTH_KEY').'_USER';
                break;
            case 'code': //验证码
                $cookieName=C('WLN_HOME_USER_AUTH_KEY').'_CODE';
                break;
            case 'time': //时间
                $cookieName=C('WLN_HOME_USER_AUTH_KEY').'_TIME';
                break;
        }
        if($value==='-1'){
            return cookie($cookieName);
        }
        cookie($cookieName,$value,$time);
    }

    /**
     * 图片验证码
     * @author demo
     */
    public function verify(){
        R('Common/UserLayer/Verify');
    }

    /**
     * 发送邮件验证码
     * @param int $userID 用户ID
     * @param string $email 邮箱号
     * @return array
     * @author demo
     */
    public function sendUserEmailCode($userID,$email){
        if(!$email){
            return array(0,'1X422');//请填写邮箱
        }
        $output  = R('Common/UserLayer/sendEmailCode',array($email,$userID));

        //输出错误信息
        if($output[0] == 1){
           return array(0,$output[1]);
        }
        return array(1,'success');
    }
    /**
     * 发送短信验证码
     * @param int $userID 用户ID
     * @param int $imgCode 图片验证码
     * @param int $phoneNum 手机号
     * @return array
     * @author demo
     */
    public function sendUserPhoneCode($userID,$imgCode,$phoneNum){
        if(MD5($imgCode) != session('verify')){
            return array(0,'30101');//验证码有误！
        }
        $output=R('Common/UserLayer/sendPhoneCode',array($phoneNum,$imgCode,$userID));
        //输出错误信息
        if($output[0] == 1){
            return array(0,$output[1]);
        }
        return array(1,'success');
    }
    /**
     * 验证邮箱验证码
     * @param int $userID 用户ID
     * @param string $email 邮箱号
     * @param int $emailCode 邮箱验证码
     * @param string $emailAttr 操作标识
     * @return array
     * @author demo
     */
    public function checkUserEmailCode($userID,$email,$emailCode,$emailAttr){
        if(!$email){
            return array(0,'1X422');//请填写邮箱
        }

        if(!$emailCode){
            return array(0,'30104');
        }

        $status = 1;//状态
        //组卷端如果执行验证过的邮箱单独修改时开启
        if($emailAttr=='edit'){
            $status = 2;
        }

        $output  = R('Common/UserLayer/checkEmailCode',array($email,$emailCode,$userID,$status));

        //输出错误信息
        if($output[0] == 1){
            return array(0,$output[1]);
        }

        return array(1,'success'); //返回成功提示
    }
    /**
     * 验证短信验证码
     * @param int $userID 用户ID
     * @param int $phoneNum 手机号
     * @param int $messageCode 短信验证码
     * @param string $phoneAttr 操作标识
     * @param int $imgCode 图片验证码
     * @return array
     * @author demo
     */
    public function checkUserPhoneCode($userID,$phoneNum,$messageCode,$phoneAttr,$imgCode){
        if($imgCode){
            if(md5($imgCode) != session('verify')){
                return array(0,'30101');//验证码有误！
            }
        }
        $status=1;//验证状态
        if($phoneAttr=='edit'){//修改已验证的手机，通过已验证的手机验证后的状态
            $status = 2;
        }
        $output=R('Common/UserLayer/checkPhoneCode',array($phoneNum,$messageCode,$userID,$status));

        if($output[0] == 1){
            return array(0,$output[1]); //返回错误提示
        }

        return array(1,'success'); //返回成功提示
    }

}
