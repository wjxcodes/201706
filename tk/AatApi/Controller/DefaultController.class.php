<?php
/**
 * @author demo
 * @date 2014年5月10日
 */
/**
 * 首页类
 */
namespace AatApi\Controller;
class DefaultController extends BaseController{
    /**
     * 退出登录 Ajax调用
     * @author demo
     */
    public function logout() {
        $userName = $this->getUserName();
        if($userName){
            $this->userLog('用户退出', '用户【' . $userName . '】退出提分系统（'.$this->getPlatformName().')');
        }
        // $this->ajaxReturn('您已经安全退出！', 'success', 1);
        $this->setBack('您已经安全退出！');
    }

    /**
     * 登录系统
     * @param string UserName 登录用户名；必须是手机号或者邮箱 true
     * @param string Password 密码 true
     * @return array
     *    [
     *   "data"=> [
     *       "userID"=>68,
     *       "userName"=> "152101262522",
     *       "userVersion"=> "0",
     *       "userCode"=> "632897945591f61a154850572d82dba0",
     *       "needField"=> [
     *           "RealName",
     *           "Nickname",
     *           "AreaID",
     *           "SchoolID",
     *           "GradeID",
     *           "Version"
     *       ]
     *   ],
     *   "info"=> "success",
     *   "status"=> 1
     * ]
     * @author demo
     */
    public function login() {
        $this->checkRequest();
        $loginName = $_REQUEST['userName'];
        $password = $_REQUEST['password'];
        $remember = $_REQUEST['remember'];
        // $loginIData = A('Aat/User','Logic')->login($loginName,$password,$remember);
        $buffer = $this->getApiUser('User/login',$loginName,$password,$remember);

        if($buffer[0]==1){
            $output=array();
            $output = [
                'userID'=>$buffer[1]['UserID'],
                'userName'=>$buffer[1]['UserName'],
                'userVersion'=>$buffer[1]['Version'],
                'userCode'=>$buffer[1]['UserCode'],
				'AreaID'=>$buffer[1]['AreaID']
            ];

            //需要更新的字段
            $needField = $this->getApiUser('User/mustField',$buffer[1]['UserID']);
            if(count($needField)){
                $output['needField'] = $needField;
            }

            $this->setBack($output);
        }
        $this->setError($buffer[1], 1);
    }

    /**
     * 描述：移动端注册提交的函数
     * @param int way 注册方式，1为手机号，2为邮箱 true
     * @param string UserName 用户名、正则同登录正则 true
     * @param string Password 密码、正则同登录正则 true
     * @param string Password2 重复密码 true
     * @param string nickname 用户昵称4-15位，中文字符算3位  true
     * @param string phoneCode 手机验证码 true
     * @param string emailCode 邮箱验证码 true
     * @return array
     *    [
     *   "data": null,
     *   "info": "success",
     *   "status": 1
     *   ]
     * @author demo
     */
    public function register(){
        //--------------------参数--------------------
        $data['who'] = 'student';//身份学生
        //--------------------参数--------------------
        $data['way']= $_POST['way'];//注册方式，1为手机号，2为邮箱
        //--------------------参数--------------------
        $data['userName'] = $_POST['username'];//用户名
        //--------------------参数--------------------
        $data['password'] = $_POST['password'];//密码
        //--------------------参数--------------------
        $data['password1'] = $_POST['password2'];
        //--------------------参数--------------------
        $data['nickname'] = $_POST['nickname'];//昵称
        //--------------------参数--------------------
        $data['phoneCode'] = $_POST['phoneCode'];//昵称
        //--------------------参数--------------------
        $data['emailCode'] = $_POST['emailCode'];//昵称

        $result = $this->getApiUser('User/register', $data);
        if($result[0]==1){
            $this->setBack($result[1]);
        }
        $this->setError($result[1], 1);
    }

    /**
     * 获取地区信息
     * @param int id  地区ID，请求省份数据时，请设置为0 true
     * @return array
     *    [
     *   "data"=>[
     *       [
     *           "AreaID"=>"2",
     *           "AreaName"=>"东城区",
     *           "Last"=>1
     *       ],
     *       [
     *           "AreaID"=>"3",
     *           "AreaName"=>"西城区",
     *           "Last"=>1
     *       ]
     *   ],
     *   "info"=>"success",
     *   "status"=>1
     * ]
     * @author demo
     */
    public function ajaxArea() {
        $areaID = $_POST['id'];
        $IData=$this->getApiAat('User/areaByID',$areaID);
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1]);
        }
        // $this->ajaxReturn(A('Aat/User','Logic')->areaByID($areaID));
    }

    /**
     * 根据地区返回学校数据
     * @param int id 地区ID，地区选择里最后一个地区的ID true
     * @return array
     *    [
     *   "data"=>[
     *       [
     *           "SchoolID"=>"3364",
     *           "SchoolName"=>"当城中学"
     *       ],
     *       [
     *           "SchoolID"=>"3365",
     *           "SchoolName"=>"杨柳青四中"
     *       ],
     *       [
     *           "SchoolID"=>"3366",
     *           "SchoolName"=>"杨柳青一中"
     *       ]
     *   ],
     *   "info"=>"success",
     *   "status"=>1
     * ]
     * @author demo
     */
    public function ajaxSchool() {
        $areaID = $_POST['id'];
        $IData=$this->getApiAat('User/schoolByAreaID',$areaID);
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1]);
        }
        // $this->ajaxReturn(A('Aat/User','Logic')->schoolByAreaID($areaID));
    }

    /**
     * 获取年级信息
     * @author demo
     */
    public function ajaxGrade(){
         $IData=$this->getApiAat('User/grade');
         if($IData[0] ==1){
            $this->setBack($IData[1]);
         }else{
            $this->setError($IData[1]);
         }
        // $this->ajaxReturn(A('Aat/User','Logic')->grade());
    }

    /**
     * 前端7种测试推题函数
     * 新增记录到testRecord表和testRecordAttr表
     * 11月13日重写
     * @param int SubjectID 学科ID true
     * @param  int id 测试类型（1：智能测试；2：专题模块训练；3：整卷练习-现有套卷；4：整卷练习-自定义智能组卷；5：阶段测试；6：目标测试；7：章节测试（即同步版的专题模块训练）,8：专题测试（管理员自定义试题测试））true
     * @param int KID   知识点ID，测试类型为2时，该参数为必须  false
     * @param int DocID 套卷ID，测试类型为3时，该参数为必须  false
     * @param int Diff  难度系数，测试类型为4时，该参数为必须  false
     * @param int Cover 知识点覆盖率，测试类型为4时，该参数为必须  false
     * @param string TypesID 以逗号隔开的题型ID，测试类型为4时，该参数为必须  false
     * @param string TypesNum 以逗号隔开的题型试题数量，测试类型为4时，该参数为必须  false
     * @param string DScore 以逗号隔开的题型分值，测试类型为4时，该参数为必须  false
     * @param string TypesScore  以逗号隔开的题型积分方式，测试类型为4时，该参数为必须  false
     * @param string KlID 以逗号隔开的知识点ID字符串，测试类型为4、5、6时，该参数为必须  false
     * @param int Score 目标分值，测试类型为6时，该参数为必须  false
     * @param int TotalScore 总分值，测试类型为6时，该参数为必须  false
     * @param int chapterID 章节ID，测试类型为7时，该参数为必须  false
     * @param int topicPaperID  专题ID，测试类型为8时，该参数为必须  false
     * @return array
     *        正确
     *   [
     *       "data"=>[
     *           "record_id"=> 157439, //练习ID
     *           "ifNotDo"=> true, //是否是做过的练习 true未做过 false做过
     *           "phone"=> 1//是否是手机请求（0：不是；1：是）
     *      ],
     *      "info"=>"success",
     *       "status"=>1
     *   ]
     *   错误返回类似下面格式的错误信息
     *   [
     *       "data"=>null,
     *       "info"=>"请先选择试卷进行测试",
     *       "status"=>0
     *   ]
     * @author demo
     */
    public function ajaxGetTest() {
        $subjectID = $this->getSubjectID();
        $username = $this->getUserName();
        $userID = $this->getUserID();
        $type = $_POST['id'];//测试类型
        switch ($type) {
            case 1:
                //智能测试
                $param = [
                    'version'=>$this->getVersion(),
                ];
                break;
            case 2:
                $param = [
                    'klID'=>$_POST['KID'],
                ];
                break;
            case 3:
                //整卷练习-现有套卷
                $param = [
                    'docID'=>$_POST['DocID'],
                ];
                break;
            case 4:
                //整卷练习-自定义智能组卷
                $param = [
                    'diff'=>$_REQUEST['Diff'],//难度
                    'cover'=>$_REQUEST['Cover'],//知识点覆盖率
                    'typesID'=>$_REQUEST['TypesID'],
                    'typesNum'=>$_REQUEST['TypesNum'],
                    'typesScore'=>$_REQUEST['TypesScore'],
                    'dScore'=>$_REQUEST['DScore'],
                    'klID'=>$_REQUEST['KlID'],
                ];
                break;
            case 5:
                //阶段测试
                $param = ['klID'=>$_REQUEST['KlID']];
                break;
            case 6:
                //目标测试
                $param = [
                    'score'=>$_REQUEST['Score'],
                    'totalScore'=>$_REQUEST['TotalScore'],
                    'klID'=>$_REQUEST['KlID'],
                ];
                break;
            case 7:
                //章节测试
                $param = ['chapterID'=>$_REQUEST['chapterID'],];
                break;
            case 8:
                // 专题练习
                $param = ['topicPaperID'=>$_REQUEST['topicPaperID']];
                break;
            default:
                $param = [];
        }
        // $recordIDIData = A('Exercise/PushTest','Logic')->pushTest($userID,$username,$type,$subjectID,$param);
        $recordIDIData = $this->getApiAat('PushTest/PushTest',$userID,$username,$type,$subjectID,$param);

        //返回跳转页面的数据
        if($recordIDIData[0]==0){
            // $this->ajaxReturn(null,$recordIDIData[1],0);
            $this->setError($recordIDIData[1]);
        }
        if($recordIDIData[2]!==false){
            $recordIDIData[2]=true;
        }

        // $this->ajaxReturn(['record_id' => $recordIDIData[1],'ifNotDo' => $recordIDIData[2]], 'success', 1);
        $this->setBack(['record_id' => $recordIDIData[1],'ifNotDo' => $recordIDIData[2]]);
    }

    /**
     * ajax获取知识点
     * @param int subjectID 本地学科ID true
     * @return array
     *    [
     *   "data"=> [
     *       [
     *           "klID"=>"1790",
     *           "klName"=>"集合与常用逻辑用语",
     *           "allAmount"=>"123",
     *           "rightWrongAmount"=>94,
     *           "rightAmount"=>"19",
     *           "wrongAmount"=>"75",
     *           "undoAmount"=>"19",
     *           "notAmount"=>"10",
     *           "klAbility"=>"-3.000",
     *           "loadTime"=>"1441512088",
     *           "rightRate"=>20,
     *           "sub"=>[
     *               [
     *                   "klID"=>"1791",
     *                   "klName"=>"集合",
     *                   "allAmount"=>"62", //  总的答题量（练习中的作答量，包括right+wrong+undo+not总数量）
     *                   "rightWrongAmount"=>54, //真确的+错误的 答题量
     *                   "rightAmount"=>"13", //正确答题量数据
     *                   "wrongAmount"=>"41",
     *                   "undoAmount"=>"5", //为作答数量
     *                   "notAmount"=>"3", // 不能判断正误的试题数量
     *                   "klAbility"=>"-3.000", //知识点能力值，取值范围看这里，注意如果是空字符，则等同于-3到-2不显示黄色星星
     *                   "loadTime"=>"1441512088",
     *                   "rightRate"=>24//  正确率 百分数的整数部分
     *               ]
     *              ...
     *           ]
     *       ]
     *     ....
     *   ],
     *   "info"=>"success",
     *   "status"=>1
     * ]
     * @author demo
     */
    public function ajaxKl() {
        // $subjectIData = A('Exercise/PushTest', 'Logic')->userAbilityKl($this->getUserName(), $this->getSubjectID());
        $subjectIData = $this->getApiAat('PushTest/userAbilityKl', $this->getUserName(), $this->getSubjectID());
        if($subjectIData[0] ==1){
            $this->setBack($subjectIData[1]);
        }else{
            $this->setError($subjectIData[1]);
        }
        // $this->ajaxReturn($subjectIData);
    }

    /**
     * 获取目标测试记录
     * @author demo
     */
    public function ajaxAimRecord() {
        $subjectID = $this->getSubjectID();
        $username = $this->getUserName();
        // $aimRecordIData = A('Exercise/PushTest','Logic')->amiRecord($subjectID,$username);
        $aimRecordIData = $this->getApiAat('PushTest/amiRecord',$subjectID,$username);
        if($aimRecordIData[0]==1){
            // $this->ajaxReturn(['show'=>$aimRecordIData[1]['show'],'list'=>$aimRecordIData[1]['list']],'success',1);
            $this->setBack(['show'=>$aimRecordIData[1]['show'],'list'=>$aimRecordIData[1]['list']]);
        }else{
            // $this->ajaxReturn(null,$aimRecordIData[1],0);
            $this->setError($aimRecordIData[1]);
        }
    }

    /**
     * 获取各个学科标准卷分数，用于目标训练添加目标
     * @param int subjectID 学科ID  true
     * @return array
     *    [
     *   "data"=>[
     *       "score"=>"150",
     *       "id"=>"12"
     *   ],
     *   "info"=>"success",
     *   "status"=>1
     * ]
     * @author demo
     */
    public function ajaxNormRecord(){
        // $IData = A('Exercise/PushTest','Logic')->normalScore($this->getSubjectID());
        $IData = $this->getApiAat('PushTest/normalScore',$this->getSubjectID());
        // $this->ajaxReturn($IData);
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1]);
        }
    }

    /**
     * 获取用户不同等级的知识点
     * @param int subjectID 学科ID true
     * @param string level 等级，E6中的值 true
     * @return array
     *    [
     *   "data"=>[
     *       [
     *           "sub"=>[
     *               [
     *                   "kl_id"=>"238",
     *                   "name"=>"语言知识运用",
     *                   "rate"=>19,
     *                   "amount"=>"答题量：332 正确率：19%",
     *                   "kl_ability"=>"-2.570"
     *               ]
     *           ],
     *           "kl_id"=>"237",
     *           "name"=>"语言知识基础",
     *           "amount"=>"答题量：332 正确率：19%",
     *           "kl_ability"=>"-2.570"//能力值，具体标星规则看这里，注意如果为空字符串，则等同于-3至-2 没有黄色星星
     *       ],
     *     ...
     *   ],
     *   "info"=>"success",
     *   "status"=>1
     * ]
     * @author demo
     */
    public function ajaxLevelKl() {
        $subjectID = $this->getSubjectID();
        $level = $_POST['level'];
        // $klIData = A('Exercise/PushTest','Logic')->userLevelKl($this->getUserName(),$subjectID,$level);
        $klIData = $this->getApiAat('PushTest/userLevelKl',$this->getUserName(),$subjectID,$level);
        if($klIData[0] ==1){
            $this->setBack($klIData[1]);
        }else{
            $this->setError($klIData[1]);
        }
        // $this->ajaxReturn($klIData);
    }

    /**
     * ajax获取学科试题类型
     * @param int subjectID 学科ID true
     * @return array
     *    [
     *   "data"=> [
     *       [
     *           "TypesID"=> "72",
     *           "TypesName"=> "古诗文阅读",
     *           "SubjectID"=> "12",
     *           "Num"=> "30",
     *           "DScore"=> "19",
     *           "TypesScore"=> "2",
     *       ]
     *   ],
     *   "info"=> "success",
     *   "status"=> 1
     * ]
     * @author demo
     */
    public function ajaxType() {
        $IData=$this->getApiAat('PushTest/type',$this->getSubjectID());
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1]);
        }
        // $this->ajaxReturn(A('Exercise/PushTest','Logic')->type($this->getSubjectID()));
    }

    /**
     * ajax获取试卷
     * @param int subjectID本地学科ID true
     * @param int p 页码 true
     * @param string search 搜索关键字，为空字符串时不限制  true
     * @param int style 试卷类型ID true
     * @param int year  试卷年份，如2015，为空字符串时为不限制 true
     * @return array
     *    [
     *   "data"=>[
     *       [
     *           "DocID"=>"1",
     *           "DocName"=>"历年真题",
     *       ]
     *   ],
     *   "info"=>"success",
     *   "status"=>1
     * ]
     * @author demo
     */
    public function ajaxDoc() {
        $subjectID = $this->getSubjectID();//兼容Android2.2.0之前的版本
        $style = $_POST['style'];
        $year = $_POST['year'];
        $searchKey = $_POST['search'];
        $pageSize = 5;
        // $IData = A('Exercise/PushTest','Logic')->docList($subjectID,$style,$year,$searchKey,$pageSize);
        $IData = $this->getApiAat('PushTest/docList',$subjectID,$style,$year,$searchKey,$pageSize);

        if($IData[0]==1){
            // $this->ajaxReturn([
            // 'list'=>$IData[1]['list'],
            // 'year'=>$IData[1]['year'],
            // 'searchKey'=>$IData[1]['searchKey'],
            // ],'success',1);
            $this->setBack([
            'list'=>$IData[1]['list'],
            'year'=>$IData[1]['year'],
            'searchKey'=>$IData[1]['searchKey'],
            ]);
        }else{
            // $this->ajaxReturn(null,$IData[1],0);
            $this->setError($IData[1]);
        }
    }

    /**
     * 获取学科初中或高中的学科列表
     * @return array
     *    [
     *   "data"=> [
     *       "12"=> [
     *           "subject_id"=> 12,
     *           "subject_name"=> "语文",
     *           "total_score"=> "150"//当前学科的总分
     *       ],
     *       ....
     *   ],
     *   "info"=> "success",
     *   "status"=> 1
     * ]
     * @author demo
     */
    public function ajaxSubject() {
        // $IData = A('Exercise/PushTest','Logic')->subject($this->getGradeID());
        $IData = $this->getApiAat('PushTest/subject',$this->getGradeID());
        if($IData[0]==1){
            $this->setBack($IData[1]);
        }
        $this->setError($IData[1],1);
    }

    /**
     * 描述：APP注册使用
     * @param string x-verify-token 【注意】：http head 头信息的方式返回 true
     * @author demo
     */
    public function verifyMobile($length=4, $mode=1, $type='png', $width=95, $height=40){
        import("Common.Tool.Image");
        import('Common.Tool.String');
        $randval = \String::randString($length, $mode);
        $token = md5(date('YmdH', time()).C('APP_KEY').$randval);//1小时过期
        header('x-verify-token:'.$token);
        $width = ($length * 10 + 10) > $width ? $length * 10 + 10 : $width;
        if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor($width, $height);
        } else {
            $im = imagecreate($width, $height);
        }
        $r = Array(225, 255, 255, 223);
        $g = Array(225, 236, 237, 255);
        $b = Array(225, 236, 166, 125);
        $key = mt_rand(0, 3);

        $backColor = imagecolorallocate($im, $r[$key], $g[$key], $b[$key]);    //背景色（随机）
        $borderColor = imagecolorallocate($im, 100, 100, 100);                    //边框色
        imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
        $stringColor = imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120));
        // 干扰
        for ($i = 0; $i < 10; $i++) {
            imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $stringColor);
        }
        for ($i = 0; $i < 25; $i++) {
            imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $stringColor);
        }
        for ($i = 0; $i < $length; $i++) {
            imagestring($im, 5, $i * 10 + 5, mt_rand(1, 8), $randval{$i}, $stringColor);
        }
        \Image::output($im, $type);
    }

    /**
     * 描述：
     * @param $verify
     * @param $verifyToken 一般通过$_POST['verifyToken']获得
     * @author demo
     */
    private function checkVerifyMobile($verify,$verifyToken){
        //判断验证码是否正确 只有APP端的验证了
        if ((md5(date('YmdH', time()) . C('APP_KEY') . $verify) != $verifyToken) &&
            (md5(date('YmdH', time() + 3600) . C('APP_KEY') . $verify) != $verifyToken)
        ) {
            // $this->ajaxReturn(null, '验证码错误或超时！', 0);
            $this->setError( '验证码错误或超时！');
        }
    }



    /**
     * 获取服务条款HTML
     * @author demo
     */
    public function getServiceTerm(){
        $getService=R('Common/UserLayer/getServiceTerm');
        // $this->ajaxReturn($getService,'success',1);
        $this->setBack($getService);
    }

    /**
     * 描述：获取版本更新数据
     * @param int versionCode 当前versionCode true
     * @return array
     *    [
     *   "data"=>[
     *      "fileUrl"=>apk下载真实地址 ,
     *      "updateType"=>"1", //0 不弹框提示更新 1提示更新 2强制更新
     *       "versionName"=>"2.0.0",
     *       "versionCode":"50",//版本更新码连续整数
     *       "updateContent":"1、xxx2、xxx"
     *   ],
     *   "info"=>"success",
     *   "status"=>1
     * ]
     * @author demo
     */
    public function checkUpdate(){
        $appType = $_POST['phone'];
        $versionCode = $_POST['versionCode'];
        $appType = 1;
        $versionCode = 1;
        if(!$appType||!$versionCode){
            $this->setError('更新检测参数不完整！');
        }
        $db = $this->getModel('AppVersionUpdate')->findData(
            'VersionName,VersionCode,UpdateType,FileUrl,UpdateContent',
            ['AppType'=>$appType,'NeedUpdateCodeStart'=>['elt',$versionCode],'NeedUpdateCodeEnd'=>['egt',$versionCode]],
            'VersionCode DESC'
        );
        if($db){
            // $this->ajaxReturn($data=[
            //     'versionName'=>$db['VersionName'],
            //     'versionCode'=>$db['VersionCode'],
            //     'updateType'=>$db['UpdateType'],
            //     'fileUrl'=>$db['FileUrl'],
            //     'updateContent'=>$db['UpdateContent']
            // ],$info='success',1);
             $this->setBack($data=[
                'versionName'=>$db['VersionName'],
                'versionCode'=>$db['VersionCode'],
                'updateType'=>$db['UpdateType'],
                'fileUrl'=>$db['FileUrl'],
                'updateContent'=>$db['UpdateContent']
            ]);
        }else{
            // $this->ajaxReturn(null,'',0);
            $this->setError('');
        }
    }

    /**
     * 找回密码第一步
     * 验证用户输入的信息是否正确
     * @param string userStyle 用户找回密码使用的方式，值可以为UserName（用户名）、Phonecode（手机号）、Email（邮箱）true
     * @param string userInfo 用户找回密码的信息 true
     * @return array
     *        正确
     *   [
     *       "data"=>[
     *           "userID"=>"2292",
     *           "phone"=>"15036703722",
     *           "email"=>"1305336313"
     *      ],
     *      "info"=>"success",
     *       "status"=>1
     *   ]
     *   错误返回类似下面格式的错误信息
     *   [
     *       "data"=>null,
     *       "info"=>"用户信息不存在！",
     *       "status"=>0
     *   ]
     * @author demo
     */
    public function checkGetPasswordInfo(){
        $userStyle = $_REQUEST['userStyle']; //找回密码方式
        $userInfo = $_REQUEST['userInfo'];//用户输入的信息

        $output = R('Common/UserLayer/checkGetPasswordInfo',array($userStyle,$userInfo));
        if($output[0] == 1){
            $error = C('ERROR_'.$output[1]);//兼容组卷端的错误码信息
            // $this->ajaxReturn(null, $error,0);
            $this->setError( $error);
        }
        // $this->ajaxReturn($output[1],'success',1);
        $this->setBack($output[1]);
    }

    /**
     * 找回密码发送验证码
     * @param string userID 用户ID true
     * @param string checkInfo 用户接收验证码的信息（可以是手机号或者邮箱）true
     * @return array
     *        正确
     *   [
     *       "data"=>null,
     *       "info"=>"success",
     *       "status"=>1
     *   ]
     *   错误返回类似下面格式的错误信息
     *   [
     *       "data"=>null,
     *       "info"=>"验证码已过期！",
     *       "status"=>0
     *   ]
     * @author demo
     */
    public function getPasswordSendCode(){
        $userID = $_POST['userID'];//用户ID
        $checkInfo = $_POST['checkInfo'];//手机号或者邮箱
        if(!$userID||!$checkInfo){
            // $this->ajaxReturn(null,'参数错误，请联系我们！',0);
            $this->setError('参数错误，请联系我们！');
        }
        $IData = $this->sendCodeProcess($checkInfo,$userID);
        //输出错误信息
        if($IData[0] == 1){
            $this->setBack($IData[1]);
        }
        $this->setError($IData[1]);
    }

    /**
     * 描述：注册发送手机号或邮箱验证码
     * @param string verify 用户填写的图片验证码 true
     * @param string verifyToken 验证码Token，通过获取图片验证码的接口（AatApi-Default-verifyMobile）的Header头信息获取 true
     * @param string checkInfo 用户接收验证码的信息（可以是手机号或者邮箱）true
     * @return array
     *        正确
     *   [
     *       "data": null,
     *       "info": "success",
     *       "status": 1
     *   ]
     *   错误返回类似下面格式的错误信息
     *   [
     *       "data": null,
     *       "info": "发送成功！",
     *       "status": 0
     *   ]
     * @author demo
     */
    public function registerSendCode(){
        $this->checkVerifyMobile($verify = $_POST['verify'],$verifyToken = $_POST['verifyToken']);
        $checkInfo = $_POST['checkInfo'];//手机号或者邮箱
        $IData = $this->sendCodeProcess($checkInfo,0);
        //输出错误信息
        // $this->ajaxReturn($IData);
        if($IData[0] == 1){
            $this->setBack($IData[1]);
        }
        $this->setError($IData[1]);
    }

    /**
     * 描述：
     * @param string $checkInfo 手机号或者邮箱
     * @param int $userID 用户ID，没有为0
     * @return array 中间数据格式
     * @author demo
     */
    private function sendCodeProcess($checkInfo,$userID=0){
        if(checkString('checkIfPhone',$checkInfo)){//手机
            $output = R('Common/UserLayer/sendPhoneCode',array($checkInfo,'phoneList',$userID));
        }else if(checkString('checkIfEmail',$checkInfo)){//邮箱
            $output = R('Common/UserLayer/sendEmailCode',array($checkInfo,$userID));
        }
        if($output[0]==1){
            //有错误
            return [0,$output[1]];
        }else{
            return [1,''];
        }
    }

    /**
     * 检测用户输入的验证码，用于密码找回安全验证
     * @param string userID 用户ID true
     * @param string checkInfo 用户选择的验证方式（手机或者邮箱） true
     * @param string code 用户输入的验证码 true
     * @return array
     *        正确
     *   [
     *       "data": null,
     *       "info": "success",
     *       "status": 1
     *   ]
     *   错误返回类似下面格式的错误信息
     *   [
     *       "data": null,
     *       "info": "短信验证码错误！",
     *       "status": 0
     *   ]
     * @author demo
     */
    public function checkGetPasswordCode(){
        $code = $_POST['code'];//用户输入的验证码
        $userID = $_POST['userID'];//用户ID
        $checkInfo = $_POST['checkInfo'];//手机号或者邮箱

        if (preg_match('/^\d{6}$/',$code) == 0) {
            // $this->ajaxReturn(null,'验证码格式错误！',0);
            $this->setError('验证码格式错误！');
        }
        if(checkString('checkIfPhone',$checkInfo)){//手机
            $output = R('Common/UserLayer/checkPhoneCode',array($checkInfo,$code,$userID,1));
        }else if(checkString('checkIfEmail',$checkInfo)){//邮箱
            $output = R('Common/UserLayer/checkEmailCode',array($checkInfo,$code,$userID,1));
        }
        //输出错误信息
        if($output[0] == 1){
            $error = C('ERROR_'.$output[1]);//兼容组卷端的错误码信息
            // $this->ajaxReturn(null,$error,0);
            $this->setError($error);
        }
        // $this->ajaxReturn(null,'success',1); //返回成功提示
        $this->setBack('success'); //返回成功提示
    }

    /**
     * 重置密码保存
     * @param string userID 用户ID true
     * @param string password 用户第一次输入的密码 true
     * @param string password1 用户第二次输入的密码 true
     * @return array
     *        正确
     *   [
     *        "data"=>{
     *           "userID"=>"2292"，
     *           "userName"=>"15036703722"
     *       },
     *       "info"=>"success",
     *       "status"=>1
     *   ]
     *   错误返回类似下面格式的错误信息
     *   [
     *       "data"=>null,
     *       "info"=>"超时或者验证错误，请重新找回密码",
     *       "status"=>0
     *   ]
     * @author demo
     */
    public function setPassword(){
        $userID = $_POST['userID'];
        $password = $_POST['password'];
        $password1 = $_POST['password1'];

        //设置新密码
        $output = R('Common/UserLayer/setPassword',array($userID,$password,$password1));
        if($output[0] == 1){
            $error = C('ERROR_'.$output[1]);//兼容组卷端的错误码信息
            // $this->ajaxReturn(null,$error,0);
            $this->setError($error);
        }

        $this->userLog('用户登录', '用户【' . $output[1]['userName'] . '】通过找回密码修改密码');
        // $this->ajaxReturn($output[1],'success',1);
        $this->setBack($output[1]);
    }

    /**
     * 记录错误信息
     * @param string $description 描述
     * @param string $msg 内容
     * @author demo
     */
    public function setErrorLog(){
        $desc=$_POST['description'];
        $msg=$_POST['msg'];

        if(empty($desc) || empty($msg)){
            $this->setError('提交失败。');
        }
        $data=array();
        $data['description'] = $desc;
        $data['msg'] = $msg;
        $this->getModel('Base')->addErrorLog($data);
        $this->setBack('success');
    }
}