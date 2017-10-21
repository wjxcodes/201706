<?php
/**
 * @author demo
 * @date 2015年12月26日
 */

namespace Aat\Api;

class UserApi extends BaseApi
{
    /**
     * 描述：提分登录逻辑
     * @param $loginName
     * @param $password
     * @param $remember
     * @return array
     * @author demo
     */
    public function login($loginName,$password,$remember){
        if (empty($loginName)) {
            return [0,'请填写用户名！']; //请填写用户名！
        }
        if (empty($password)) {
            return [0,'请填写密码！'];
        }
        if(!is_numeric($loginName)){
            $orderNum=-1;
        }else{
            $orderNum=$loginName;
        }
//        $userDb = $this->getModel('User')->selectData(
//            'UserID,UserName,Password,SaveCode,Logins,Version,OrderNum',
//            ['_logic'=>'OR','UserName'=>$loginName,'OrderNum'=>$orderNum]
//        );
        $userDb = $this->getApiUser('User/selectData',
                'UserID,UserName,Password,SaveCode,Logins,Version,OrderNum',
                ['_logic'=>'OR','UserName'=>$loginName,'OrderNum'=>$orderNum],
                '', '', 'User');
        if(count($userDb)>1){
            $errorLog = [
                'msg' => '查询用户:'.$loginName.'时，有两条数据在user表',
                'sql' => '空',
                'description' => 'OrderNum字段有为0的数据时可能出现错误'
            ];
//            $this->getModel('User')->addErrorLog($errorLog);
            $this->getApiUser('User/addErrorLog', $errorLog);
            return [0,'用户信息错误，请联系我们！'];
        }
        $checkLoginFailed=R('Common/UserLayer/loginFailedProcess',[$userDb?$userDb[0]['UserID']:0,'check']);
        if($checkLoginFailed=='error'){
            return [0,'您的账号或IP登录错误次数过多，请稍后再试或联系我们！'];
        }
        if(!$userDb){
            R('Common/UserLayer/loginFailedProcess',[$userDb?$userDb[0]['UserID']:0,'failed']);
            return [0,'用户名或学号不存在！'];
        }
        $userData = $userDb[0];
        //如果出错，此函数checkLogin会返回错误信息字符串
        $check = $this->checkLogin($userData['UserName'], $password, 'password',0);
        if ($check !== true) {
            R('Common/UserLayer/loginFailedProcess',[$userDb?$userDb[0]['UserID']:0,'failed']);
            return [0,$check];
        }
        //成功登录
        R('Common/UserLayer/loginFailedProcess',[$userDb?$userDb[0]['UserID']:0,'success']);
        $data = [];//需要更新的用户信息
        if (!$userData['SaveCode']) {
//            $data['SaveCode'] = $this->getModel('User')->saveCode();
            $data['SaveCode'] = $this->getApiUser('User/saveCode');
        }
        $data['Logins'] = $userData['Logins']+1;
        $data['LastTime'] = time();
        $data['LastIP'] = get_client_ip(0,true);
//        $this->getModel('User')->updateData(
//            $data,
//            ['UserID'=>$userData['UserID']]
//        );
        $this->getApiUser('User/updateData', $data, ['UserID'=>$userData['UserID']], 'User');

        //写入cookie
        $userName = $userData['UserName'];
        $userVersion = $userData['Version'];
        $authTimeout = $_POST['phone']?3600*24*365:C('WLN_COOKIE_TIMEOUT');
        $userCode = md5($userData['UserID'] . $userData['UserName'] . $userData['SaveCode'] . ceil(time()/$authTimeout));
        $expire = $remember == 1 ? 3600*24*7 : 0;
        return [1,[
            'userID'=>$userData['UserID'],
            'username'=>$userName,
            'userVersion'=>$userVersion,
            'userCode'=>$userCode,
            'expire'=>$expire,
        ]];
    }

    /**
     * 检查用户登录情况
     * @param string $userName 用户名
     * @param string $key 验证字段 userCode或者password
     * @param string $type 取值code或password
     * @param int $whoIs 用户角色
     * @return bool|string 如果错误返回错误字符串，成功返回true
     * @author demo
     */
    public function checkLogin($userName, $key, $type, $whoIs) {

        if ($userName == null || $key == null) {
            return '登录失败，请重试！';
        }
//        $db = $this->getModel('User')->getInfoByWhere(
//            'UserID,UserName,Password,Email,SaveCode,Status,Whois',
//            ['UserName' => $userName]
//        );
        $db = $this->getApiUser('User/getInfoByWhere', 'UserID,UserName,Password,Email,SaveCode,Status,Whois', ['UserName' => $userName]);
        if (!$db) {
            return '用户名或学号不存在！';
        }
        $user = $db[0];
        if ($type != 'code' && $type != 'password') {
            return '登录失败，请重试！';
        }
        if ($type == 'code' && $key != md5($user['UserID'] . $user['UserName'] . $user['SaveCode'])) {
            return '用户名或密码错误！';
        } elseif ($type == 'password' && $user['Password'] != md5($user['UserName'] . $key)) {
            return '用户名或密码错误！';
        }
        if ($user['Status'] == 1) {
            return '账号被冻结，请联系我们！';
        }
        if ($user['Whois'] != $whoIs) {
            $whoIsArray = [
                0 => '学生',
                1 => '老师',
                2 => '家长',
                3 => '校长',
            ];
            return '请使用' . $whoIsArray[$whoIs] . '账号登录！';
        }
        //没有错误，正常登录
        return true;
    }

    /**
     * 描述：保存补全的用户信息
     * @param array $userData 用户查询的数据，通过$this->getModel('User')->getInfoByName($userName,$field='*')[0];获取
     * @return array [状态，数据]
     * @author demo
     */
    public function needFieldSave($userData) {
//        $schoolModel = $this->getModel('School');
//        $classGradeModel = $this->getModel('ClassGrade');
        $userModel = $this->getModel('User');
        $userID = $userData['UserID'];
        $field =  $this->getApiUser('User/mustField',$userID);
        if (count($field) == 0) {
            return [0, '暂无需要保存的用户数据！'];
        }
        $data = $_POST['data'];
        if (!$data) {
            return [0, '参数为空，请重试！'];
        }
        if (!is_array($data)) {
            $data = json_decode(stripslashes_deep($data), true);
        }
        if (!is_array($data)) {
            return [0, '参数不为数组！'];
        }
        $updateData = [];//保存数据库的数据
        foreach ($field as $iField) {
            if ($iField == 'UserName') {
                $userName = $data['userName'];//新的设置的userName
                $pregEmail = checkString('checkIfEmail',$userName);
                $pregPhone = checkString('checkIfPhone',$userName);
                if ($pregEmail == false && $pregPhone == false) {
                    return [0, '用户名必须是手机号或邮箱！'];
                }
                //判断用户名重复
                $repeatError = $userModel->checkUser($userName);
                if ($repeatError) {
                    return [0, '您填写的用户名已被使用！'];
                }

                if($pregEmail == false){
                    $phoneCode = $data['phoneCode'];//手机验证码
                    //验证手机验证码是否正确
                    $output=R('Common/UserLayer/checkPhoneCode',array($userName,$phoneCode,0,1));
                    if($output[0] == 1){
                        return [0,$this->setError($output[1],2)];
                    }
                    $updateData['Phonecode']=$userName;
                    $updateData['CheckPhone'] = 1;
                }else{
                    $emailCode = $data['emailCode'];//邮箱验证码
                    //验证邮箱验证码是否正确
                    $output=R('Common/UserLayer/checkEmailCode',array($userName,$emailCode,0,1));
                    if($output[0] == 1){
                        return [0,$this->setError($output[1],2)];
                    }
                    $updateData['Email'] = $userName;
                    $updateData['CheckEmail'] = 1;
                }


                $updateData['UserName'] = $userName;
                $tmpPassword = $userData['TmpPwd'];
                $updateData['Password'] = md5($userName . $tmpPassword);
            }
            if ($iField == 'RealName') {
                $realName = formatString('stripTags', $data['realName']);//去掉html标签
                $length = strlen($realName);
                if ($length < 6 || $length > 30) {
                    return [0, '真实姓名必须大于2个汉字小于10个汉字！'];
                }
                //用户真是姓名过滤
                $filter = $userModel->NameFilter($realName);
                if ($filter['errornum'] !== 'success') {
                    $replace = '';
                    if ($filter['replace']) {
                        $replace = '【' . $filter['replace'] . '】';
                    }
                    return [0, '非法数据' . $replace . '，请更换!'];
                }
                $updateData['RealName'] = $realName;
            }
            if ($iField == 'Nickname') {
                $nickname = formatString('stripTags', $data['nickname']);
                $whereField['Nickname'] = $nickname;
                $err = R('Common/UserLayer/checkField', array($whereField));
                if (!empty($err)) {
                    $error = str_replace('error|', '', C('ERROR_' . $err['Nickname']));
                    return [0, $error];
                }
                $updateData['Nickname'] = $nickname;
            }
            if ($iField == 'AreaID') {
                $areaID = $data['areaID'];
//                $buffer = SS('areaList')[$areaID]['AreaName'];
                $buffer = $this->getApiCommon('Area/areaList')[$areaID]['AreaName'];
                if ($buffer == null) {
                    return [0, '选择的地区不存在，请重试！'];
                }
                $updateData['AreaID'] = $areaID;
            }
            if ($iField == 'SchoolID') {
                $schoolID = $data['schoolID'];
//                $schoolCount = $schoolModel->selectCount(
//                    ['SchoolID'=>$schoolID],
//                    'SchoolID'
//                );
                $schoolCount = $this->getApiCommon('School/selectCount', ['SchoolID'=>$schoolID], 'SchoolID', '', 'School');
                if (!$schoolCount) {
                    return [0, '选择的学校不存在，请重试！'];
                }
                $updateData['SchoolID'] = $schoolID;
            }
            if ($iField == 'GradeID') {
                $gradeID = $data['gradeID'];
//                $classGradeCount = $classGradeModel->selectCount(
//                    ['GradeID'=>$gradeID],
//                    'GradeID'
//                );
                $classGradeCount = $this->getApiWork('Class/selectCount', ['GradeID'=>$gradeID], 'GradeID', '', 'ClassGrade');
                if (!$classGradeCount) {
                    return [0, '选择的年级不存在，请重试！'];
                }
                $updateData['GradeID'] = $gradeID;
            }
            if ($iField == 'Version') {
                $version = $data['version'];
                if ($version != 1 && $version != 2) {
                    return [0, '使用版本选择错误，请重试！'];
                }
                $updateData['Version'] = $version;
            }
        }
        $update = $userModel->updateData(
            $updateData,
            'UserID=' . $userID
        );
        if ($update == false) {
            return [0, '保存失败，请重试！'];
        } else {
            return [1, $updateData];
        }


    }

    /**
     * 描述：地区
     * @param $id
     * @return array
     * @author demo
     */
    public function areaByID($id){
//        $area = SS('areaChildList');
        $area = $this->getApiCommon('Area/areaChildList');
        if ($area[$id]) {
            return [1,$area[$id]];
        } else {
            return [0,'获取地区数据错误！'];
        }
    }

    /**
     * 描述：学校
     * @param $areaID
     * @return array
     * @author demo
     */
    public function schoolByAreaID($areaID){
        if (!$areaID) {
            return [0,'请先选择所在地区！'];
        }
//        $schoolData = $this->getModel('School')->selectData(
//            'SchoolID,SchoolName',
//            [
//                'AreaID' => $areaID,
//                'Status' => 2,
//                'Type' => ['in',[1,2]]
//            ]
//        );
        $schoolData = $this->getApiCommon('School/selectData',
                'SchoolID,SchoolName',
                [
                    'AreaID' => $areaID,
                    'Status' => 2,
                    'Type' => ['in',[1,2]]
                ],
                '', '', 'School');
        if ($schoolData) {
            return [1,$schoolData];
        } else {
            return [0,'该地区没有学校数据！'];
        }
    }

    /**
     * 描述：年级
     * @return array
     * @author demo
     */
    public function grade(){
//        $gradeCache = SS('gradeListSubject');
        $gradeCache = $this->getApiCommon('Grade/gradeListSubject');
        if($gradeCache){
            $grade = [];
            foreach($gradeCache as $i=>$k){
                //屏蔽初中的年级，后期有初中的试题后再取消屏蔽
                if($i==21){
                    break;
                }
                $grade[$i]['grade_id'] = 0;
                $grade[$i]['grade_name'] = $k['GradeName'];
                foreach($k['sub'] as $j=>$m){
                    $grade[$i]['sub'][$j]['grade_id'] = $m['GradeID'];
                    $grade[$i]['sub'][$j]['grade_name'] = $m['GradeName'];
                }
            }
            return [1,$grade];
        }else{
            return [0,'年级数据获取错误！'];
        }
    }
}