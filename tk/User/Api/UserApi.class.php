<?php
/**
 * 官网试卷更新，用户动态action
 * @author demo 2015-15
 */
namespace User\Api;
use Common\Api\CommonApi;
class UserApi extends CommonApi{
    private $user;
    /**
     * 项目初始化
     */
    protected function _initialize(){
        $this->user=$this->getModel('User');
    }

    /**
     * 检测用户所在年级
     * @param string $userName
     * @param array 年级段
     * @return mix
     * @author demo
     */
    public function checkUserGrade($userName,$gradeArray){
        return $this->user->checkUserGrade($userName,$gradeArray);
    }
    /**
     * 组卷端用户信息
     * @author demo
     */
    public function getUserInfoForHome($userName){
        $buffer = $this->getInfoByName($userName,
            'UserID,UserName,RealName,Nickname,AreaID,SchoolID,GradeID,SubjectStyle,Email,Phonecode,Address,CheckPhone,LoadDate'
        );
        //组卷端，默认groupName=1
        $buffer[0]['UserJF']=$this->user->getUserGroupStatus($buffer[0]['UserID'],1); //获取分组状态
        return  $buffer[0];
    }

    /**
     * 查询单个用户详细信息
     * @param  string $userName
     * @param  string $field 查询字段
     * @return array|bool 单个用户的数组或者false错误
     * @author demo  
     * @aupdate 2015年9月29日
     */
    public function getInfoByName($userName,$field='*') {
        return $this->user->selectData(
            $field,
            'UserName="' . $userName . '"'
        );
    }

    public function getInfoByWhere($field='*',$where='',$order='',$limit=''){
        return $this->user->getInfoByWhere($field,$where,$order,$limit);
    }


    /**
     * 获取用户需要补充的字段
     * @param string $userID 用户名
     * @return array 需要补充字段信息
     * @author demo
     */
    public function mustField($userID){
        $mustFieldArr = array('UserName','Nickname','RealName','AreaID','SchoolID','GradeID');

        $user=$this->getModel('User');
        $userData = $user->getInfoByID($userID);

        if($userData['Whois']==0){//学生端需要检测版本
            array_push($mustFieldArr,'Version');
        }

        $mustField=array();
        foreach($mustFieldArr as $iMustFieldArr){
            if($iMustFieldArr == 'UserName'){
                //UserName字段特殊判断（用户名为学号）
                if($userData[$iMustFieldArr] == $userData['OrderNum']){
                    array_push($mustField,$iMustFieldArr);
                }
            }
            //正常判断
            if(!$userData[$iMustFieldArr]){
                array_push($mustField,$iMustFieldArr);
            }
        }
        return $mustField;
    }

    /**
     * 用户登录
     * @return array 用户信息
     * @author demo
     */
    public function login($userName,$passWord,$ifSave,$role=''){
        if(empty($userName)){
            return array(0,'30201'); //用户名不能为空
        }
        if(empty($passWord)){
            return array(0,'30202'); //密码不能为空
        }
        $output=$this->user->login($userName,$passWord);
		// dump($output);
        $isByWork = '';
        if($output[0]==1){
            //判断用户角色是否对应
            if(!empty($role)){
                $who = $output[1]['Whois'];
                //教师
                if('teacher' == $role && $who != 1){
                    return array(0, '30836');
                }
                //学生
                if('student' == $role && $who != 0){
                    return array(0, '30837');
                }
                //兼职
                if('bywork' == $role){
                    $result = $this->getModel('UserGroup')->selectData(
                        'UGID',
                        'GroupName=3 and UserID="'.$output[1]['UserID'].'"'
                    );
                    if(empty($result)){
                        return array(0, '30838');
                    }
                    $isByWork = 'Teacher';
                }
				//校长
				if('school' == $role && $who !=3){
					return array(0,'您没有权限，请选择校长用户。');
				}
            }

            //添加用户登录Cookie信息
            $this->setLoginCookie($output[1], $ifSave, $isByWork);
			
            $system='组卷';
            if($output[1]['Whois']==0){
                $system='提分';
            }
			if($output[1]['Whois']==3){
				$system='校长';
			}
            $this->userLog('用户登录', '用户【' . $output[1]['UserName'] . '】登录'.$system.'系统',$output[1]['UserName']);

            //首页执行登录成功经验录入
            $this->getModel('UserExp')->addUserExpAll($output[1]['UserName'],'login');

            unset($output[1]['Password']);
            unset($output[1]['SaveCode']);

            return array(1,$output[1]);
        }

        return array(0,$output[1]); //错误信息
    }

    /**
     * 设置登录后Cookie信息
     * @param array $data 需要设置的cookie内容
     * @param bool $ifSave 是否保持长期登录 默认false
     * @param bool $mainModule 所属模块
     * @author demo
     */
    public function setLoginCookie($data,$ifSave=false,$mainModule=''){
        if(empty($mainModule)){
            if($data['Whois'] == 1){
                $mainModule = 'Home';
            }elseif($data['Whois'] == 0){
                $mainModule = 'Aat';
			
            }elseif($data['Whois'] == 3){
				$mainModule = 'Statistics';
			}
        }
        $cookieTime = 24 * 3600;
        if($ifSave) {
            $cookieTime = 14 * 24 * 3600;
        }
        switch($mainModule){
            case 'Home':
            case 'Teacher':
                if(!cookie('SubjectId')){
                    cookie('SubjectId',$data['SubjectStyle'],$cookieTime);
                }
                break;
            case 'Aat':
                $this->setCookieVersionID($data['Version'],31104000,'Aat');
                break;
			case 'Statistics':
                $this->setCookieVersionID($data['Version'],31104000,'Statistics');
                break;
        }

        $time=C('WLN_COOKIE_TIMEOUT');
        $userCode=md5($data['UserID'].$data['UserName'].$data['SaveCode'].ceil(time()/$time));
        $this->setCookieUserName($data['UserName'],$cookieTime,$mainModule);
        $this->setCookieUserID($data['UserID'],$cookieTime,$mainModule);
        $this->setCookieCode($userCode,$cookieTime,$mainModule);
    }
    /**
     * 清理cookie数据功能
     * @param int $modelName 模块名称
     * @author demo
     */
    public function clearLoginCookie($modelName){
        switch($modelName){
            case 'Home':
                $this->setCookieUserName(null,null,'Home');
                $this->setCookieCode(null,null,'Home');
                $this->setCookieUserID(null,null,'Home');
                break;
            case 'Aat':
                $this->setCookieUserName(null,null,'Aat');
                $this->setCookieCode(null,null,'Aat');
                $this->setCookieUserID(null,null,'Aat');
                $this->setCookieVersionID(null,null,'Aat');
                break;
			case 'Statistics':
                $this->setCookieUserName(null,null,'Statistics');
                $this->setCookieCode(null,null,'Statistics');
                $this->setCookieUserID(null,null,'Statistics');
                $this->setCookieVersionID(null,null,'Statistics');
                break;
        }
    }

    /**
     * 测试次数验证
     * @param string $typeID 测试类型
     * @param int $userID 用户ID
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @return boolean(false不提示，true提示没有权限)|int(提示已测试次数)
     * @author demo
     */
    public function totalFrequency($typeID,$userID,$userName,$subjectID) {
        //根据用户名和学科查找用户当他的测试次数
        $time = strtotime(date("Y-m-d", time()));
        $where = array(
            'UserName' => $userName ,
            'SubjectID' => $subjectID ,
            'LoadTime'=>array('gt',$time)
        );
        //如果有测试类型，则查该类型的测试次数
        if ($typeID != 0) {
            $where['Style'] = $typeID;
        }

        //用户当天指定测试记录总数（所有测试或指定类型测试）
        $recordNum = $this->getModel('UserTestRecordAttr')->selectCount(
            $where,
            'TestRecordAttrID'
        );
        $powerList = $this->getModel('UserGroup')->getAuthList($userID);//获取用户权限列表
        if ($powerList) {
            $cycleNum = 0;//循环遍历次数标识，用于判断是否遍历完成
            foreach ($powerList as $power) {
                if (strstr($power['PowerTag'],'/Default/ajaxGetTest/'. $typeID)) {
                    //比较权限限制次数和用户使用次数
                    if ($power['Value'] > $recordNum) {
                        return false;
                    } else {
                        return intval($power['Value']);
                    }
                    break;
                } else{//判断没权限的情况
                    $cycleNum++;
                    if ($cycleNum == count($powerList)) {
                        return true;
                    }
                }
            }
        }else{
            return true;
        }
    }

    /**
     * 用户注册
     * @param array $data
     * @author demo 16-5-10
     */
    public function register($data){
        //--------------------参数--------------------
        $who = $data['who'];//身份
        //--------------------参数--------------------
        $way = $data['way'];//注册方式，1为手机号，2为邮箱
        //--------------------参数--------------------
        $userName  = $data['userName'];//用户名
        //--------------------参数--------------------
        $passWord  = $data['password'];//密码
        //--------------------参数--------------------
        $passWord1 = $data['password1'];
        //--------------------参数--------------------
        $nickname  = $data['nickname'];//昵称

        if($way==1 || $who == 'teacher'){//当主持方式为手机号，或者身份是老师的情况下，验证手机号及短信验证码
            $preg_tel = checkString('checkIfPhone',$userName);
            if($preg_tel==false){
                return array(0, R('Common/SystemLayer/ajaxSetError',array('30211',2)));
            }
            //--------------------参数--------------------
            //$phoneCode = $data['phoneCode'];//手机验证码

            //验证手机验证码是否正确
            //$output=R('Common/UserLayer/checkPhoneCode',array($userName,$phoneCode,0,1));

//          if($output[0] == 1){
//              return array(0, R('Common/SystemLayer/ajaxSetError',array($output[1],2)));
//          }
            $result['Phonecode']=$userName;
            $result['CheckPhone'] = 1;
            $whereField['Phonecode']=$userName;
        }else{//验证邮箱
            $preg_email = checkString('checkIfEmail',$userName);
            if($preg_email==false){
                return array(0, R('Common/SystemLayer/ajaxSetError',array('30227',2)));
            }
            //--------------------参数--------------------
            //$emailCode = $data['emailCode'];//邮箱验证码

            //验证邮箱验证码是否正确
            //$output=R('Common/UserLayer/checkEmailCode',array($userName,$emailCode,0,1));

//          if($output[0] == 1){
//              return array(0, R('Common/SystemLayer/ajaxSetError',array($output[1],2)));
//          }
            $result['Email'] = $userName;
            $result['CheckEmail'] = 1;
            $whereField['Email']=$userName;
        }

        //判断密码是否合法
        if (strlen($passWord) < 6 || strlen($passWord) > 18) {
            return array(0, R('Common/SystemLayer/ajaxSetError',array('30221',2)));
        }
        //判断密码是否重复
        if ($passWord != $passWord1) {
            return array(0, R('Common/SystemLayer/ajaxSetError',array('30207',2)));
        }

        //判断用户名重复
        $whereField['UserName']=$userName;
        //判断昵称
        $nickname=formatString('stripTags',$nickname);
        $whereField['Nickname']=$nickname;
        $err=R('Common/UserLayer/checkField',array($whereField));

        if(!empty($err) && !is_numeric($err)){
            $error=R('Common/SystemLayer/formatError',array($err));
            return array(0, R('Common/SystemLayer/ajaxSetError',array($error[0],2,'',$error[1])));
        }

        //特殊处理
        if(!empty($err) && is_numeric($err)){
            $userID=$err; //用户id
        }
        $whois = 1;
        if($who == 'student'){
            $whois = 0;
        }

        $user=$this->getModel('User');
        //计算学号赋值
        $order_num = $this->getModel('AutoInc')->getOrderNum();
        $result['OrderNum'] = $order_num;
        $result['UserName'] = $userName;
        $result['Nickname'] = $nickname;
        $result['Password'] = MD5($userName . $passWord);
        $result['RealName'] = '';
        $result['Sex'] = 0;
        $result['Address'] = '';
        $result['PostCode'] = '';
        $result['Whois'] = $whois;
        $result['LoadDate'] = time();
        $result['LastTime'] = time();
        $result['Logins'] = 0;
        $result['LastIP'] = get_client_ip(0,true);
        $result['SaveCode'] = $user->savecode();
        $result['IfShowTime'] = 0;

        if($userID){
            $buffer = $user->updateData($result,array('UserID'=>$userID));
        }else{
            $buffer = $user->insertData($result);
        }

        if($buffer){
            if($whois==1 && !$userID) {
                $userID=$buffer;
                //判断用户是否在可注册的IP下
                // $ip=get_client_ip(0,true);
                // $buffer = $this->getModel('UserIp')->userIp(
                //     'IPID,PUID,LastTime,IfReg',
                //     'IPAddress='.ip2long($ip)
                // );
                // $ipUser = $buffer[0];
                $ipUser = array(); //此处根据要求不验证学校ip  2015-11-8
                //注册时添加指定分组  2015-9-2
                $this->getModel('UserGroup')->addDefaultGroupAtRegistration($userID, $ipUser);
            }
            //根据$way 通过方式 1 是手机，2是邮箱
            if($way==1){
                //教师用户注册成功后，默认成手机验证
                $this->getModel('UserExp')->addUserExpAll($userName,'mobile');
            }else{
                //学生用户注册成功后，默认邮箱注册
                $this->getModel('UserExp')->addUserExpAll($userName,'email');
            }
            if(!$userID) $userID=$buffer;
            $this->getModel('RegisterLog')->insertRegisterLog($userID,$result['LastIP']);
            // $this->setBack('注册成功！');
            return array(1, 'success');
        }
    }

     /**
     * 切换使用版本
     * @param int $version 指定的版本号
     * @author demo 16-5-12
     */
    public function changeVersion($version, $userName) {
        if($version!=1&&$version!=2){
           return array('data'=>null,'info'=>'30301','status'=>0);
        }
        $updateData['Version'] = $version;
        $update = $this->getModel('User')->updateData(
            $updateData,
            'UserName="'.$userName.'"'
        );
        if ($update !== false) {
            //$update 可能等于零
            return array('data'=>null,'info'=>'success','status'=>1);
        }
        return array('data'=>null,'info'=>'30311','status'=>0);
    }

    /**
     * 获取用户的所有章节数据
     * 用于通过章节推题时章节条件 用于判断用户学科下是否选择章节
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @param bool $isAll 是否需要子章节的所有数据
     * @return string|bool isAll=true用户学科下所有章节的子章节ID（英文逗号分隔）isAll=false 用户所选的章节ID，没有子章节数据
     * @author demo
     */
    public function getUserAllChapter($userName,$subjectID,$isAll = true){
        return $this->getModel('UserChapter')->getUserAllChapter($userName,$subjectID,false);
    }
    /**
     * 更新用户学科下的章节数据
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @param array $data 章节数组，数组每一项为章节ID
     * @return bool 是否更新成功
     * @author demo
     */
    public function updateUserChapter($userName,$subjectID,$data){
        return $this->getModel('UserChapter')->update($userName,$subjectID,$data);
    }
	
	
}