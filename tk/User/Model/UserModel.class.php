<?php
/**
 * @author demo   
 * @date 2014年8月4日
 * @update 2015年9月29日
 */
/**
 * 用户类，用于处理用户档案操作
 */
namespace User\Model;
use Common\Model\BaseModel;
class UserModel extends BaseModel{
    private $tableNames='zj_user';

    /**
     * @覆盖父类方法。
     * @author demo 2015-12-18
     */
    public function insertData($data, $modelName=''){
        $result = parent::insertData($data,$modelName);
        if($result !== false && isset($data['Whois'])){
            $sc = $this->getModel('StatisticsCounter');
            if(0 == $data['Whois']){
                $sc->increase('studentNum');
            }else if(1 == $data['Whois']){
                $sc->increase('teacherNum');
            }
        }
        return $result;
    }

    /**
     * @覆盖父类方法。
     * @author demo
     */
     public function deleteData($where, $modelName=''){
        $data = (array)$this->selectData('Whois', $where);
        $sc = $this->getModel('StatisticsCounter');
        foreach($data as $value){
            if(0 == $value['Whois']){
                $sc->increase('studentNum', -1);
            }else{
                $sc->increase('teacherNum', -1);
            }
        }
        return parent::deleteData($where, $modelName);
     }

    /**
     * 查询登录总数；
     * @author demo
     */
    public function getTotalRow(){
        return $this->sumData(
            'logins','1=1');
    }

    /**
     * 查询组卷总数；
     * @author demo
     */
    public function getComTimesRow(){
        return $this->sumData('ComTimes','1=1');
    }

    /**
     * 查询单个用户详细信息
     * @param int $userID 用户id
     * @return array|bool 单个用户的数组或者false错误
     * @author demo
     */
    public function getInfoByID($userID,$field='*') {
        return $this->findData(
            $field,
            'UserID ="'. $userID.'"');
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
        return $this->selectData(
            $field,
            'UserName ="'. $userName.'"');
    }

    /**
     * 按条件查询数据
     * @param string $field 查询的字段
     * @param string $where 查询的条件
     * @param string $order 查询的排序
     * @param string $limit 查询的数量
     * @return array
     * @author demo
     */
    public function getInfoByWhere($field='*',$where='',$order='',$limit=''){
        return $this->selectData($field, $where, $order, $limit );
    }

    /**
     * 查询条件下数据总数；
     * @param string $where 查询的条件
     * @return int
     * @author demo
     */
    public function getPageNum($where){
        return $this->selectCount(
            $where,
            'UserID');
    }

    /**
     * 查询条件下数据总数；
     * @param string $where 查询的条件
     * @param string $page  开始页
     * @param string $perpage 每页条数
     * @return array
     * @author demo
     */
     public function getPage($where,$page,$perpage){
         $fenye=(isset ($page) ? $page : 1) . ',' . $perpage;

        return $this->pageData(
            '*',
            $where,
            'UserID DESC',
            $fenye);
    }

    /**
     * 更改用户状态
     * @param int $userID 用户名id
     * @param array $data 需要改变的数据内容
     * @return bool
     * @author demo
     */
    public function changeUserData($userID,$data){
        return $this->updateData(
            $data,
            'UserID="'.$userID.'"');
    }

    /**
     * 更改用户状态
     * @param string $userName 用户名
     * @param string $status 状态
     * @return bool
     * @author demo
     */
    public function changeUserStatus($userName,$status){
        $data['Status']=$status;
        return $this->updateData(
            $data,
            'UserName="'.$userName.'"');
    }

    /**
     * 用户名，电话，邮箱重复验证
     * @param string $userName 用户名
     * @param string $phoneCode 电话
     * @param string $email 邮箱
     * @param string $currId 当前数据id（用于编辑用户信息验证）
     * @return string
     * @author demo
     */
    public function checkUser($userName='',$phoneCode='',$email='',$currId=''){
        //验证用户名
        if($userName){
            $data='UserName="'.$userName.'" ';
            $data.=' or PhoneCode="'.$userName.'" ';
            $data.=' or Email="'.$userName.'" ';
            $uNameID=$this->selectData(
                'UserID',
                $data);
        }
        if($uNameID && $uNameID[0]['UserID']!=$currId){
            return '30718';
        }
        //验证电话
        if($phoneCode){
            $data='UserName="'.$phoneCode.'" ';
            $data.=' or PhoneCode="'.$phoneCode.'" ';
            $uPhoneID = $this->selectData(
                'UserID',
                $data);
        }
        if($uPhoneID && $uPhoneID[0]['UserID']!=$currId){
            return '30224';
        }
        //验证邮箱
        if($email){
            $data='UserName="'.$email.'" ';
            $data.=' or Email="'.$email.'" ';
            $uEmail = $this->selectData(
                'UserID',
                $data);
        }
        if($uEmail && $uEmail[0]['UserID']!=$currId){
            return '30225';
        }
    }

    /**
     * 根据用户ID 修改包月信息
     * @param $userID int 用户ID
     * @param $POST array $POST数据
     * @return int
     * @author demo
     */
    public function updateMonth($userID,$POST){
        $group = $this->getModel('PowerUser','getGroupName');
        $data['UserID'] = $userID;
        $data['AddTime'] = time();
        if($POST['LastTime']){
            $data['LastTime'] = $POST['LastTime'];
        }
        $this->delSameMsg($userID);
        //重新获取用户所属组
        $buffer = $this->getModel('UserGroup')->selectData(
            '*',
            'UserID='.$userID);
        $groupArray=array();
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                if($iBuffer['LastTime']>time()){
                    $edit[0]['EndTime']=$iBuffer['LastTime'];
                }else{
                    $edit[0]['EndTime']='0';
                }
                $groupArray[$iBuffer['GroupName']][]=$iBuffer;
            }
        }

        foreach($group as $i=>$iGroup){
            $data['GroupName'] = $i;
            $id=$POST['groupname_'.$i];
            if(is_array($id)){
                $ii=0;//计数
                foreach($id as $j=>$jID){
                    $data['GroupID'] = $jID;
                    if($groupArray[$i] && $ii<count($groupArray[$i])){
                        $data['UGID'] = $groupArray[$i][$ii]['UGID'];
                        $result=$this->getModel('UserGroup')->updateData(
                            $data,
                            'UGID='.$groupArray[$i][$ii]['UGID']);
                        $ii++;
                    }else{
                        unset($data['UGID']);
                        if($POST['EndTime']){ //适应添加分组时使用
                            $data['LastTime']=strtotime($POST['EndTime']);
                        }
                        $result=$this->getModel('UserGroup')->insertData($data);
                    }
                }
                if($groupArray[$i] && ($j+1)<count($groupArray[$i])){
                    $uGid=array();
                    for(;$j<count($groupArray[$i]);$j++){
                        //遍历对应的组，与当前传递的是不是在一组中，不在一组中，执行删除
                        if(!in_array($groupArray[$i][$j]['GroupID'],$POST['groupname_'.$i])){
                            $uGid[]=$groupArray[$i][$j]['UGID'];
                        }
                    }
                    if(!empty($uGid)){
                        $uGID=implode(',',$uGid);
                        if(!empty($uGID)){
                            $result=$this->getModel('UserGroup')->deleteData(
                                'UGID in ('.$uGID.')'
                            );
                        }
                    }

                }
            }else if($id){
                $data['GroupID'] = $id;
                if($groupArray[$i]){
                    $data['UGID'] = $groupArray[$i][0]['UGID'];
                    $result=$this->getModel('UserGroup')->updateData(
                        $data,
                        'UGID='.$groupArray[$i][0]['UGID']);
                }else{
                    unset($data['UGID']);
                    if($POST['EndTime']){
                        $data['LastTime']=strtotime($POST['EndTime']);
                    }
                    $result=$this->getModel('UserGroup')->insertData($data);
                }
            }else{
                if($this->getModel('UserGroup')->deleteData(
                    'UserID ='.$userID.' and GroupName='.$i)===false){
                    $result=0;
                }else{
                    $result=1;
                };
            }
        }
        return $result;
    }
    /**
     * 数据排重
     * @param $userID int 用户ID
     * @return null
     * @author demo
     */
    public function delSameMsg($userID){
        //获取到期时间 用户所属组
        $buffer = $this->getModel('UserGroup')->selectData(
            '*',
            'UserID='.$userID);
        foreach($buffer as $i=>$iBuffer){
            $newBuffer[$iBuffer['GroupName']][]=$iBuffer;
        }
        //因为根据分组，组卷端，提分端，只能一个选项
        //组卷端排重
        if(count($newBuffer[1])>1){
            foreach($newBuffer[1] as $i=>$iNewBuffer){
                if($i==0){  //保留第一个分组数据
                    unset($iNewBuffer);
                }
                $delIdArr1[]=$iNewBuffer['UGID'];
            }
            array_shift($delIdArr1);
            $delIdStr1=implode(',',$delIdArr1);
        }
        //提分端排重
        if(count($newBuffer[2])>1){
            foreach($newBuffer[2] as $i=>$iNewBuffer){
                if($i==0){ //保留第一分组数据
                    unset($iNewBuffer);
                }
                $delIdArr2[]=$iNewBuffer['UGID'];
            }
            array_shift($delIdArr2);
            $delIdStr2=implode(',',$delIdArr2);
        }
        if($delIdStr1){ //提分删除
            $this->getModel('UserGroup')->deleteData('UGID in ('.$delIdStr1.')');
        }
        if($delIdStr2){ //组卷删除
            $this->getModel('UserGroup')->deleteData('UGID in ('.$delIdStr2.')');
        }
    }

    /**
     * 通用用户登录
     * @param string $username 用户名
     * @param string $password 密码
     * @param array $fields 成功后希望返回哪些数据
     * @author demo
     * @return mixed 失败返回false，成功返回改用户的数据
     * @date 2015-6-6
     */
    public function login($username, $password, $fields='*'){

        $userName = formatString('stripTags',$username);
        if($fields=='*' || empty($fields)) $fields = 'UserName,Password,UserID,OrderNum,SaveCode,Whois,Logins,LastTime,LastIP,Times,SubjectStyle,Version,OrderNum,Cz,UserPic,Status,AreaID';

        $userName = preg_replace('/\s+/', '', $userName);
        //之前的程序存在一种情况就是用户名是邮箱号
        //但是用户的邮箱字段的内容没有该用户名一样的就会出现找不到用户名的错误提示
        //如果使用或的关系的话，可能存在查找多条用户信息的情况
        //所以目前遵循只使用用户名登录，至于提分的学号，因是系统生成的无重复信息，所以可以使用或的关系
        $where['_logic']='OR';
        $where['UserName']=$userName;
        if(is_numeric($userName) && strlen($userName)!=11){
            //提分端可能使用的是学号
            $where['OrderNum'] = $userName;
        }
        if(is_numeric($userName) && strlen($userName)==11){
            $where['Phonecode'] = $userName;
        }
        if(!is_numeric($userName) && strstr($userName,'@')){
            $where['Email'] = $userName;
        }
        $data = $this->selectData(
            $fields,
            $where
        );
        if(empty($data)){
            return array(0,'30214');
        }
        if(count($data)>1){//查询出两条数据
            return array(0,'30112');//提示用户信息错误，并记录错误日志
        }
        $password = trim($password);
        if($data[0]['Password'] !== md5($data[0]['UserName'].$password)){
            return array(0,'30204');//密码错误
        }

        if(empty($data[0]['SaveCode'])){
            $data[0]['SaveCode']=$this->saveCode();
        }

        //修改用户登录信息
        $this->changeUserLoginInfo($data[0]['UserID'],$data[0]['Logins'],$data[0]['SaveCode']);

        //判断用户状态
        if ($data[0]['Status'] == 1) {
            //因为之前官网注册的学生身份可能存在注册即被锁定
            //所以要兼容一下
            if($data[0]['Whois']==0 && $data[0]['Logins'] < 1) {
                //通过手机号注册被锁定的
                if ($data[0]['Phonecode'] != '' && $data[0]['checkPhone'] == 1) {
                    //解除锁定
                    $re['Status'] = 0;
                    $this->changeUserData($data[0]['UserID'], $re);
                    return array(1,$data[0]);
                } else if ($data[0]['Email'] != '') {
                    //通过邮箱注册的，提示去邮箱激活
                    return array(0,'30710');
                }
            }
            return array(0,'30203');
        }
        //用户权限
        $userPower = $this->getUserPower($data[0]['UserID'],$data[0]['Whois']);
        $data[0]['ChargeMode'] =$userPower['ChargeMode'];
        //用户头像
        $data[0]['UserPic'] = $this->getUserPic( $data[0]['UserPic']);
        //上次登录信息
        if(empty($data[0]['LastTime'])) $data[0]['LastTime']=time();
        $data[0]['LoginTime'] = date('Y-m-d H:i:s',$data[0]['LastTime']);

        if(empty($data[0]['LastIP'])) $data[0]['LastIP'] = get_client_ip(0,true);//登录IP

        $phone = $_POST['phone'];
        $authTimeout = $phone?3600*24*365:C('WLN_COOKIE_TIMEOUT');
        $data[0]['UserCode']=md5($data[0]['UserID'] . $data[0]['UserName'] . $data[0]['SaveCode'] . ceil(time()/$authTimeout));

        return array(1,$data[0]);
    }

    /**
     * 转换用户头像地址
     * @param string $userPic 用户头像地址
     * @return string
     * @author demo
     */
    public function getUserPic($userPic){
        //更改首页头像
        if($userPic){
            if(!preg_match('/^http:.*/i',$userPic)){//判断是不是QQ头像
                $picPath = C('WLN_DOC_HOST') . $userPic;//非QQ头像
            }else {
                $picPath = $userPic;
            }
        }else{//调用默认头像
            $picPath = __ROOT__ . '/Public/index/imgs/icon/photo.jpg';
        }
        return $picPath;
    }

    /**
     * 获取用户权限信息
     * @param int $userID 用户ID
     * @param int $who 用户身份（根据用户身份判断用户所属权限分组）
     * @return array
     * @author demo
     */
    public function getUserPower($userID,$who){
        //用户组权限
        if($who == 0){//学生所属分组为2
            $who = 2;
        }
        $powerUserArr = SS('powerUserId');
        foreach($powerUserArr as $i=>$iPowerUserArr){
            if($iPowerUserArr['IfDefault']=='1' && $iPowerUserArr['GroupName'] == $who){
                $defaultGroup=$iPowerUserArr;
            }
            if($iPowerUserArr['ListID']=='all'){
                $allPowerArr = $iPowerUserArr['PUID'];
            }
        }
        //获取当前用户权限
        $userGroupArr = $this->getModel('UserGroup')->selectData(
            'GroupName,GroupID,LastTime',
            'UserID='.$userID.' AND GroupName='.$who,
            'GroupName asc'
        );
        if($userGroupArr[0]['LastTime']>time() && $userGroupArr[0]['GroupID'] != $defaultGroup['PUID']){
            $userPower['UserJF']="包月用户";
            $userPower['UserJZRQ']=date('Y-m-d',$userGroupArr[0]['LastTime']);
            $userPower['UserJZSY']=ceil(($userGroupArr[0]['LastTime']-time())/3600/24);
            $userPower['UserJFMS']='按包月计费';
        }else{
            $userPower['UserJF']="普通用户";
            $userPower['UserJZRQ']='';
            $userPower['UserJFMS']="按点值计费";
        }
        $userPower['GroupID'] = '';
        if(empty($userGroupArr[0]['GroupID']) || (($userGroupArr[0]['GroupID']!=$allPowerArr) && $userGroupArr[0]['LastTime']<time())){
            $userPower['GroupID'] = $defaultGroup['PUID'];
        }else{
            $userPower['GroupID'] = $userGroupArr[0]['GroupID'];
        }
        if($userPower['GroupID'] == $defaultGroup['PUID']){
            $userPower['UserJF']="普通用户";
            $userPower['UserJFMS']="按点值计费";
        }else{
            $userPower['UserJF']="包月用户";
            $userPower['UserJFMS']='按包月计费';
        }
        foreach($powerUserArr as $i=>$iPowerUserArr){
            if(in_array($iPowerUserArr['PUID'],explode(',',$userPower['GroupID']))){
                $userPower['ChargeMode']=$iPowerUserArr['UserGroup'];
            }
        }
        return $userPower;
    }
    /**
     * 添加相关登录信息
     * @param array $data 用户信息 [userID,userName,saveCode,logings,ifSave]
     * @return boolean 成功返回true
     * @author demo
     * @date 2015-6-6
     */
    public function addLoginedInfo($data){
        //判断用户安全码
        if(empty($data['SaveCode'])){
            $data['SaveCode']=$this->saveCode();
        }
        //设置组卷端登录cookie
        //$this->setHomeLoginCookie($data,$data['IfSave']);

        //修改用户登录信息
        return $this->changeUserLoginInfo($data['UserID'],$data['Logins'],$data['SaveCode']);
    }
    /**
     * 修改用户登录信息
     * @param int $userID 用户ID
     * @param int $logins 用户登录次数
     * @param string $saveCode 安全码
     * @return bool
     * @author demo
     */
    public function changeUserLoginInfo($userID,$logins,$saveCode=''){
        if($saveCode==''){//安全码
            $saveCode=$this->saveCode();
        }
        $data = array ();
        $data['Logins'] = $logins + 1;//登录次数
        $data['LastTime'] = time();//最后一次登录时间
        $data['SaveCode']=$saveCode;
        $data['IfShowTime']=0;
        $data['LastIP'] = get_client_ip(0,true);//登录IP
        $result = $this->updateData(//更新数据
            $data,
            'UserID='.$userID
        );
        //返回状态
        if($result === false){
            return false;
        }
        return true;
    }


    /**
     * 用户输入过滤检测器
     * @param string $str 字符串
     * @return string
     * @author demo
     */
    public function NameFilter($str){
        $msg=array();
        if(!$str) {
            $msg['errornum']='30720';
            return $msg;
        }
        //载入过滤字符串
        include_once(APP_PATH.'Common/Common/filter.php');
        if(in_array($str,$_filter)){
            $msg['errornum']='30721';
            $msg['replace']=implode('_',str_split($str,3));
            return $msg;
        }
        foreach($_filter as $iFilter){
            if(preg_match("/".$iFilter."/s",$str)){
                $msg['errornum']='30721';
                $msg['replace']=implode('_',str_split($iFilter,3));
                return $msg;
            }
        }
        $msg['errornum']='success';
        return $msg;
    }
    /**
     * 根据用户ID 获取用户分组状态
     * @param int $userID 用户ID
     * @param int $groupName 分组ID
     * @return string
     * @author demo
     */
    public function getUserGroupStatus($userID,$groupName){
        $userGroup=$this->getModel('UserGroup')->selectData(
            '*',
            'UserID='.$userID.' and GroupName='.$groupName
        );
        $defaultGroup = $this->getModel('PowerUser')->selectData(
            'PUID',
            'GroupName=1 AND IfDefault=1'
        );
        if ($userGroup[0]['LastTime'] > time() && $userGroup[0]['GroupID']!=$defaultGroup[0]['PUID']) {
            $groupStatus= "包月用户";
        } else {
            $groupStatus="普通用户";
        }
        return $groupStatus;
    }
    /**
     * 为用户增加分值
     * @param $userName string 用户名
     * @param $point int 分值（有正有负）
     * @return bool
     * @author demo
     */
    public function addPoint($userName,$point){
        if(is_nan($point)){//判断分值是否合法
            return false;
        }
        if($point<0){//判断分值是否是负数
            $str='Cz=Cz'.$point;//执行的操作，为负数时减分
        }else{//否则执行增加操作
            $str='Cz=Cz+'.$point;
        }
        if($this->conAddData(
            $str,
            'UserName="'.$userName.'"'
        )){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 检测用户所在年级
     * @param string $userName
     * @param array 年级段
     * @return mix
     * @author demo
     */
    public function checkUserGrade($userName,$gradeArray){
        $priGrade = array();  //个人年级属性
        $priGrade = $this->selectData(
            'GradeID',
            'UserName="'.$userName.'"');
        foreach($gradeArray as $i=>$iGradeArray){
            $gradeList[]=$iGradeArray['GradeID'];
        }
        if(in_array($priGrade[0]['GradeID'],$gradeList)){
            $result= $priGrade[0]['GradeID'];
        }else{
            $result = 0;
        }
        return $result;
    }

    /**
     * 改变用户邮箱和邮箱状态
     * @param string $email 邮箱
     * @param int $status 状态 默认为1 已认证
     * @param string $userName 用户名
     * @return mixed
     * @author demo
     */
    public function updateEmailStatus($email='',$status=1,$userName){
        //修改数据库邮箱验证状态
        $data=array('CheckEmail'=>$status);
        if($email){
            $data['Email']=$email;
        }
        $result=$this->updateData(
            $data,
            'UserName="'.$userName.'"'
        );
        return $result;
    }

    /**
     * 分配教师端任务时查询指定用户组用户(config.php:WLN_TEACHER_GROUP中的合法键)的数据，包含分页操作
     * @param int $group
     * @param array $params  参数：SubjectID：学科，name：用户名，p：分页，notApplicatePage：该值不为空时，返回所有数据
     * @return array
     * @author demo
     */
    public function getTaskUserByGroup($group, $params=array()){
        $cache = SS('powerUserGroup')[3];
        foreach($cache['groupList'] as $value){
            if($value['PowerUser'] == $group){
                $cache = $value['PUID'];
                break;
            }
        }
        $where = array();
        if($params['SubjectID']){
            $where['u.SubjectStyle'] = $params['SubjectID'];
        }
        if($params['name']){
            $where['u.UserName'] = $params['name'];
        }
        $where['g.GroupID'] = $cache;
        $str = '1=1 ';
        foreach($where as $key=>$value){
            $str .= "AND {$key}='{$value}' ";
        }
        $sql = "SELECT %s FROM `zj_user` u LEFT JOIN `zj_user_group` g ON g.UserID=u.UserID WHERE ".$str;
        $model = M();
        if($params['notApplicatePage']){
            return $model->query(sprintf($sql, 'u.UserID,u.UserName'));
        }
        $result = $model->query(sprintf($sql, 'COUNT(u.UserID) as num'));
        if(empty($result)){
            $result[0]['num'] = 0;
        }
        $count = $result[0]['num'];
        $perpage = C('WLN_PERPAGE');
        $page = page($count,$params['p'],$perpage);
        $sql .= " LIMIT ".(($page-1) * $perpage).','.$perpage;
        $result = $model->query(sprintf($sql, 'u.UserID,u.UserName'));
        return array($count, $result);
    }

    /**
     * 获取用户所在学校
     * @param int $userID 用户ID
     * @return array
     * @author demo
     */
    public function getUserSchool($userID, $field=''){
        if(empty($field))
            $field = 'a.SchoolID,b.SchoolName';
        $school=$this->unionSelect('getUserSchoolMsgByUserID',$field,$userID);
        return $school[0];
    }

    /**
     * 用户经验累加
     * @param string $userName 用户名
     * @param string $expNum 经验值
     * @return bool
     * @author demo
     */
    public function addUserExp($userName,$expNum){
        if($userName && $expNum){
            if($expNum>0){
                $result=$this->conAddData(
                    'ExpNum=ExpNum+'.$expNum,
                    'UserName ="'.$userName.'"'
                );
                return $result;
            }
        }
    }

    /**
     * 生成安全码
     * @param int $length 安全码长度
     * @return String
     * @author demo
     */
    public function saveCode($length=15){
        return formatString('saveCode',$length);
    }


    /**
     * 根据$type来确认添加积分还是金币
     * @param int $id 用户id
     * @param int $type 类型 为1时添加积分，为2时添加金币
     * @param int $num 数量
     * @return boolean
     * @author demo 2016-3-4
     */
    public function addNumByType($id, $type, $num){
        $result = false;
        if(1 == $type){
            $result = $this->addPoints($id, $num);
        }else if(2 == $type){
            $result = $this->addVritualCurrency($id, $num);
        }
        if($result !== false){
            return true;
        }
        return $result;
    }

    /**
     * 累加积分
     * @param int $id 用户id
     * @param int $points 积分数量
     * @return boolean
     * @author demo 2016-3-4
     */
    public function addPoints($id, $points){
        return $this->conAddData(
            'Points=Points+'.$points,
            'UserID ="'.(int)$id.'"'
        );
    }

    /**
     * 累加金币
     * @param int $id  用户id
     * @param int $vc 金币
     * @return boolean
     * @author demo 2016-3-4
     */
    public function addVritualCurrency($id, $vc){
        return $this->conAddData(
            'Cz=Cz+'.$vc,
            'UserID ="'.(int)$id.'"'
        );
    }
}