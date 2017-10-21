<?php
/**
 * @author demo        
 * @date 2014年9月4日
 */
/**
 * 提分系统手机客户端接口类
 */
namespace AatApi\Controller;
class ApiUserController extends BaseController
{
    /**
     * 获取用户当前的精准预测分
     * 兼容支老师那边的手机客户端
     * @author demo
     */
    public function lastForecastScore(){
        $lastForecastScore = $this->getLastForecastScore();
        // $this->ajaxReturn($lastForecastScore,'success',1);
        $this->setBack($lastForecastScore);
    }

    /**
     * 按学科、测试类型和用户得到最后一次测试的testRecordID
     * @param string style 测试类型  true
     * @param string userName  用户名  true
     * @param int subjectID 学科ID   true
     * @return array
     *   [
     *    "data"=>"15742", //上次练习ID
     *    "info"=>success, 
     *    "status"=>1
     *    ]
     * @author demo
     */
    public function lastExercise(){
        $style=intval($_POST['style']);
        $where=[
            'UserName'=>$this->getUserName(),
            'SubjectID'=>$this->getSubjectID(),
            'Score'=>-1
        ];
        if($style){
            $where['Style']=$style;
        }
        $order="LoadTime desc";
        $limit="1";
        $userTestRecordAttr = $this->getModel('UserTestRecord')->selectData(
            'TestID',
            $where,
            $order,
            $limit
        );
        $testRecordID = $userTestRecordAttr?$userTestRecordAttr[0]['TestID']:0;
        if($testRecordID){
            // $this->ajaxReturn($testRecordID,'success',1);
            $this->setBack($testRecordID);
        }else{
            // $this->ajaxReturn(null,'请先进行测试！',0);
            $this->setError('请先进行测试！');
        }
    }

    /**
     * 手机端获取用户信息
     * 【重要】get可以一次获取多个，set只能一次设置一个
     * @param string option获取或者设置用户属性；get获取 set设置 true
     * @param string field 需要或者或者设置的字段名称，获取时可以多个，英文逗号分隔，设置时只能一个；可以取得值：userName用户名,realName真实姓名，areaID地区ID，schoolID学校ID，gradeID年级ID，address地址, phone手机号, email邮箱，password密码，version版本，userPicSrc用户头像, lastForecastScore最新预测分，gradeName年级名称，areaName地区名称，schoolName学校名称 true
     * @param string fieldValue option为set时，设置的字段的值 false
     * @param string oldPassWord option为set且field为password时，需要原密码字段oldPassWord false
     * @param int subjectID option为get且field包含 lastForecastScore 时，需要subjectID本字段 false
     * @return array
     *    [
     *   "data"=> [
     *       "userName"=> "15838201264", 
     *       "realName"=> "周欣", 
     *       "userPic"=> "http://192.168.4.99:8010/Uploads/userAvatar/2015/0716/55a7691444aa7535717.jpg", 
     *   ], 
     *   "info"=> "success", 
     *   "status"=> 1
     * ]
     * @author demo
     */
    public function userInfo(){
        $userModel = $this->getModel('User');
        $schoolModel = $this->getModel('School');
        $userName = $this->getUserName();
        $userID = $this->getUserID();
        $option = $_POST['option'];//数组；元素取值get set
        $field = $_POST['field'];//数组；元素取值
        //公共验证
        if(!$option||!$field){
            // $this->ajaxReturn(null,'请求参数缺失，请重试！',0);
            $this->setError('请求参数缺失，请重试！');
        }
        if(!is_array($field)){
            $field = explode(',',$field);
        }
        if(!is_array($field)){
            // $this->ajaxReturn($field,'field必须为数组！',0);
            $this->setError($field);
        }
        if($option == 'get'){
            $data = $userModel->selectData(
                '*',
                ['UserName'=>$userName]
            )[0];
            $result = [];
            foreach($field as $iField){
                if ($iField == 'userPic') $result['userPic'] = $data['UserPic'];
                if ($iField == 'userPicSrc') {
                    //头像兼容性处理
                    if($data['UserPic']){
                        if(!preg_match('/^http:.*/i',$data['UserPic'])){//判断是不是QQ头像
                            $data['UserPic'] = C('WLN_DOC_HOST') . $data['UserPic'];//非QQ头像
                        }
                    }else{//调用默认头像
                        $data['UserPic'] = __ROOT__ . '/Public/newAat/images/default.jpg';
                    }
                    $result['userPicSrc'] = $data['UserPic'];
                }
                if($iField == 'userName') $result['userName'] = $data['UserName'];
                if($iField == 'realName') $result['realName'] = $data['RealName'];
                if($iField == 'areaID') $result['areaID'] = $data['AreaID'];
                if($iField == 'areaName') {
                    $result['areaName'] = $this->getModel('Area')->getAreaPathById($data['AreaID'],' ');
                }
                if($iField == 'schoolID') $result['schoolID'] = $data['SchoolID'];
                if($iField == 'schoolName'){
                    $result['schoolName'] = $schoolModel->findData(
                        'SchoolName',
                        ['SchoolID'=>$data['SchoolID']]
                    )['SchoolName'];
                }
                if($iField == 'gradeID') $result['gradeID'] = $data['GradeID'];
                if($iField == 'gradeName'){
                    $result['gradeName'] = SS('grade')[$data['GradeID']]['GradeName'];
                }
                if($iField == 'address') $result['address'] = $data['Address'];
                if($iField == 'phone') $result['phone'] = $data['Phonecode'];
                if($iField == 'email') $result['email'] = $data['Email'];
                if($iField == 'lastForecastScore') $result['lastForecastScore'] = $this->getLastForecastScore($userName);
                if($iField == 'version') $result['version'] = $data['Version'];
            }
            if(!$result){
                // $this->ajaxReturn(null,'数据查询错误，请重试！',0);
                $this->setError('数据查询错误，请重试！');
            }
            // $this->ajaxReturn($result,'success',1);
            $this->setBack($result);
        }elseif($option == 'set'){
            $updateData = [];
            $fieldValue=$_POST['fieldValue'];//元素对应的数据;注（现在按一次修改一个信息规则）
            foreach($field as $i=>$iField){
                if(!$iField||$fieldValue==' '){
                    // $this->ajaxReturn(null,'数据不能为空！',0);
                    $this->setError('数据不能为空！');
                }
                //----------------用户姓名或昵称-------------------------------------------------------------------------
                if($iField == 'realName'){
                    $fieldValue = formatString('stripTags',$fieldValue);
                    $length = strlen($fieldValue);
                    if($length<6||$length>30){
                        // $this->ajaxReturn(null,'昵称必须大于2个汉字小于10个汉字！',0);
                        $this->setError('昵称必须大于2个汉字小于10个汉字！');
                    }
                    //用户昵称过滤
                    $filter = $userModel->NameFilter($fieldValue);
                    if($filter['errornum'] !== 'success'){
                        $replace='';
                        if($filter['replace']){
                            $replace='【'.$filter['replace'].'】';
                        }
                        // $this->ajaxReturn(null,'非法数据'.$replace.'，请更换',0);
                        $this->setError('非法数据'.$replace.'，请更换');
                    }
                    $updateData['RealName'] = $fieldValue;
                }
                //----------------地区----------------------------------------------------------------------------------
                if($iField == 'areaID'){
                    $buffer=SS('areaList')[$fieldValue]['AreaName'];
                    if($buffer == null){
                        // $this->ajaxReturn(null,'选择的地区不存在，请重试！',0);
                        $this->setError('选择的地区不存在，请重试！');
                    }
                    $updateData['AreaID'] = $fieldValue;
                }
                //----------------学校----------------------------------------------------------------------------------
                if($iField == 'schoolID'){
                    $schoolData = $schoolModel->findData(
                        'SchoolID',
                        'SchoolID='.$fieldValue
                    );
                    if($schoolData == null){
                        // $this->ajaxReturn(null,'选择的学校不存在，请重试！',0);
                        $this->setError('选择的学校不存在，请重试！');
                    }
                    $updateData['SchoolID'] = $fieldValue;
                }
                //----------------年级----------------------------------------------------------------------------------
                if($iField == 'gradeID'){
                    $classGrade = $this->getModel('ClassGrade')->selectData(
                        '*',
                        'GradeID='.$fieldValue
                    )[0];
                    if($classGrade == null){
                        // $this->ajaxReturn(null,'选择的年级不存在，请重试！',0);
                        $this->setError('选择的年级不存在，请重试！');
                    }
                    $updateData['GradeID'] = $fieldValue;
                }
                //----------------版本----------------------------------------------------------------------------------
                if($iField == 'version'){
                    if($fieldValue != 1&&$fieldValue != 2){
                        // $this->ajaxReturn(null,'选择的使用版本不存在，请重试！',0);
                        $this->setError('选择的使用版本不存在，请重试！');
                    }
                    $updateData['Version'] = $fieldValue;
                }
                //----------------地址----------------------------------------------------------------------------------
                if($iField == 'address'){
                    $iField = formatString('stripTags',$fieldValue);
                    $length = strlen($fieldValue);
                    if($length<6||$length>90){
                        // $this->ajaxReturn(null,'地址必须大于2个汉字小于30个汉字！',0);
                        $this->setError('地址必须大于2个汉字小于30个汉字！');
                    }
                    $updateData['Address'] = $fieldValue;
                }
                //----------------手机----------------------------------------------------------------------------------
                if($iField == 'phone') {
                    $pregPhone = checkString('checkIfPhone',$fieldValue);
                    if($pregPhone == false){
                        // $this->ajaxReturn(null,'手机号码格式错误！',0);
                        $this->setError('手机号码格式错误！');
                    }
                    $updateData['Phonecode'] = $fieldValue;
                }
                //----------------邮箱----------------------------------------------------------------------------------
                if($iField == 'email') {
                    $pregEmail = checkString('checkIfEmail',$fieldValue);
                    if($pregEmail == false){
                        // $this->ajaxReturn(null,'邮箱格式错误！',0);
                        $this->setError('邮箱格式错误！');
                    }
                    $updateData['Email'] = $fieldValue;
                }
                //----------------密码----------------------------------------------------------------------------------
                if($iField == 'password') {
                    //验证原密码
                    $old = $_POST['oldPassWord'];
                    if(md5($userName . $old) !== $this->getUserInfo($userName)['Password']){
                        // $this->ajaxReturn(null,'原密码错误，请重试！',0);
                         $this->setError('原密码错误，请重试！');
                    }
                    $new = $fieldValue;
                    if (strlen($new) < 6 || strlen($new) > 18) {
                        // $this->ajaxReturn(null,'请填写6-18位新密码！',0);
                          $this->setError('请填写6-18位新密码！');
                    }
                    $updateData['Password'] = md5($userName.$new);
                    $updateData['SaveCode'] = $userModel->saveCode();//更新安全码
                    $updateData['TmpPwd'] = 0;
                }
            }
            $update = $userModel->updateData(
                $updateData,
                ['UserName'=>$userName]
            );
            if($update !== false){
                //$update 可能等于零
                $result = null;
                if($updateData['Password']){
                    //如果修改了密码，需要更新SaveCode
                    $authTimeout = $_POST['phone']?3600*24*365:C('WLN_COOKIE_TIMEOUT');
                    $result = ['userCode'=>md5($userID . $userName . $updateData['SaveCode'].ceil(time()/$authTimeout))];
                }
                // $this->ajaxReturn($result,'success',1);
                $this->setBack($result);
            }else{
                // $this->ajaxReturn(null,'更新失败，请重试！',0);
                 $this->setError('更新失败，请重试！');
            }
        }else{
            // $this->ajaxReturn(null,'参数错误，请重试！',0);
             $this->setError('参数错误，请重试！');
        }
    }

    /**
     * 上传图片接口 保存base64编码的图片到文件服务器
     * pic 为base64字符串
     * type 为上传图片的类型 1为用户头像 2为答题的作答信息
     * @author demo
     */
    public function uploadPic() {
        if (!$_POST['pic']||!$_POST['type']) {
            // $this->ajaxReturn(null, 'pic和type字段不能为空', 0);
            $this->setError('pic和type字段不能为空');
        }
        $username=$this->getUserName();
        $time = time();
        $imgName=md5($username.$time);
        $type = $_POST['type'];
        $pathDir  = 'userAvatar';
        if($type == 1){
            $pathDir = 'userAvatar';
        }
        if($type == 2){
            $pathDir = 'userAnswer';
        }
        $path = './Uploads/'.$pathDir.'/' . $imgName . '.png';
        file_put_contents($path, base64_decode($_POST['pic']));

        $filePath = realpath('./') . $path;
        //上传文件到服务器
        $fileUrl = R('Common/UploadLayer/upFileToServer',array($filePath, 'bbs', $pathDir));
        @unlink($filePath); //删除本地图片
        if (strstr($fileUrl, 'error')) {
            $this->setError('保存图片失败，请重试！');
        } else {
            if ($type == 1) {//如果是保存用户头像
                $updatePic = $this->getModel('User')->updateData(
                    ['UserPic' => $fileUrl],
                    ['UserName'=>$username]
                );
                if (!$updatePic) {
                    // $this->ajaxReturn(null, '保存头像失败，请重试！', 0);
                     $this->setError('保存头像失败，请重试！');
                }
            }
            $host = C('WLN_DOC_HOST');
            // $this->ajaxReturn('<img src="' . $host . $fileUrl . '">', 'success', 1);
            $this->setBack('<img src="' . $host . $fileUrl . '">');
        }
    }

    /**
     * 获取用户当前的精准预测分
     * @param string $userName 用户名
     * @return string 最新预测分
     * @author demo
     */
    private function getLastForecastScore($userName = ''){
        $userName = $userName?$userName:$this->getUserName();
        $subjectID = $this->getSubjectID();
        $userForecastDb = null;;
        if($userName&&$subjectID){
            $userForecastDb = $this->getModel('UserForecast')->findData(
                'ForecastScore',
                [
                    'ForecastScore'=>['neq',-1],
                    'UserName'=>$userName,
                    'SubjectID'=>$subjectID
                ],
                'LoadTime Desc'
            );
        }
        return $userForecastDb?$userForecastDb['ForecastScore']:'';
    }
}