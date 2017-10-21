<?php
/**
 * @author demo 
 * @date 2014年10月10日
 */
/**
 * 提分系统基类，系统常用类属性和方法放在这里
 */
namespace AatApi\Controller;

use Common\Controller\DefaultController;

class BaseController extends DefaultController
{

    public $appType = '';//phone字段：AndroidPhone 1 AndroidPad 2 IPhone 3 IPad 4
    private $userInfo = null;//用户信息
    private $phoneSn = '';//安全码

    /**
     * __construct
     * @author demo
     */
    public function __construct() {
        //appType赋值，必须在其他代码前面
        //isset($_POST['phone']) && 
		$this->appType = $_REQUEST['phone'];
        $this->init();
        parent::__construct();
        //$this->checkRequest();
        //原因是旧版升级接口问题
    }

    /**
     * 前置操作
     * @author demo
     */
    private function init() {

    }

    /**
     * 请求检测
     * 不通过则中断程序返回错误信息
     * 【注意】flash图片上传不要使用
     * @author demo
     */
    protected function checkRequest() {
		//echo 111;die;
        $phoneKey = C('APP_KEY');
		//echo '未加密前：'.$this->appType . $phoneKey . $this->getPhoneSn();
		//echo "服务器：".md5($this->appType . $phoneKey . $this->getPhoneSn());
		//echo "post['token']:".$_POST['token'];die;
        //App平台
        if (md5($this->appType . $phoneKey . $this->getPhoneSn()) !== $_REQUEST['token']) {
            // $this->ajaxReturn(null, '用户token错误，请重启应用尝试!'.$this->appType.'|' . $phoneKey .'|'. $this->getPhoneSn(), 0);
            $this->setError( '用户token错误，请重启应用尝试!');
//            $this->ajaxReturn(null, '用户token错误，请重启应用尝试!', 0);
        }
    }

    /**
     * 获取用户表的用户信息 单例模式不合并
     * @param string $userName 用户名
     * @return array 用户数据
     * @author demo
     */
    public function getUserInfo($userName) {
        if (!$this->userInfo || $this->userInfo['UserName'] != $userName) {
            $this->userInfo = $this->getModel('User')->getInfoByName($userName,$field='*')[0];
        }
        return $this->userInfo;
    }

    /**
     * 获取学科名称
     * @param int $subjectID 学科ID
     * @return string|int 学科名或学科ID
     * @author demo
     */
    public function getSubjectName($subjectID) {
        // return A('Aat/Subject','Logic')->getNameByID($subjectID);
        return $this->getApiAat('Subject/getNameByID',$subjectID);
    }

    /**
     * 返回当前学科ID
     * @return int 获取学科ID
     * @author demo
     */
    protected function getSubjectID() {
        return $_POST['subjectID']?$_POST['subjectID']:$_POST['SubjectID'];
    }


    /**
     * 检测当前学科ID是否正确
     * @author demo
     */
    protected function checkSubjectID() {
        $subjectID = $this->getSubjectID();
        // if (A('Aat/Subject','Logic')->checkValidID($subjectID) == false) {
        if ($this->getApiAat('Subject/checkValidID',$subjectID) == false) {
                // $this->ajaxReturn(null, '请向右滑动屏幕选择学科！', 0);
                $this->setError('请向右滑动屏幕选择学科！');
        }
    }

    /**
     * 返回当前版本ID
     * @return int 获取版本ID
     * @author demo
     */
    protected function getVersionID() {
        return $this->getUserInfo($this->getUserName())['Version'];//0默认 1高考版 2同步版
    }

    /**
     * 返回用户名
     * @return string 用户名
     * @author demo
     */
    protected function getUserName() {
        return $this->getCookieUserName();
    }

    /**
     * 返回用户ID
     * @return int 用户ID
     * @author demo
     */
    protected function getUserID() {
        $userID = $_POST['userID'];
		$userName = $_POST['userName'];
        if(!$userID){
            $userID = $this->getUserInfo($userName)['UserID'];
        }
        return $userID;
    }

    /**
     * 返回用户姓名
     * @return string 用户姓名
     * @author demo
     */
    protected function getRealName() {
        return $this->getUserInfo($this->getUserName())['RealName'];
    }

    /**
     * 返回用户年级
     * @return int 用户年级
     * @author demo
     */
    protected function getGradeID() {
        return $this->getUserInfo($this->getUserName())['GradeID'];
    }

    /**
     * 返回用户使用版本
     * @return int 版本 0高考版 1同步版
     */
    protected function getVersion() {
        return $this->getUserInfo($this->getUserName())['Version'];
    }

    /**
     * 获取安全码
     * @author demo
     */
    protected function getUserCode() {
        return $this->getCookieCode();
    }

    /**
     * 检测testRecordID是否是用户可以测试的ID
     * id是否存在，是否是本人，是否是已经完成的
     * 有测试和作业之分，通过$isHomework区分
     * @param int $id testRecordID或者sendID 测试记录ID作业测试记录ID
     * @param bool $ifDone 是否是已经完成的记录（已经提交试卷的）默认不是
     * @param string $recordType 作业类型 可取值[exercise homework case] 默认 exercise
     * @return bool|array 如果检测通过返回true 否则返回false
     * @author demo 5.6.3
     */
    protected function checkTestRecordID($id, $ifDone = false, $recordType = 'exercise') {
        if ($recordType === 'exercise') {//提分测试
            $username = $this->getUserName();
            // return A('Exercise/TestRecord','Logic')->checkValidExerciseID($id, $ifDone, $username);
            return $this->getApiAat('TestRecord/checkValidExerciseID',$id, $ifDone, $username);
        } else if ($recordType === 'homework') {//作业
            $userID = $this->getUserID();
            // return A('Exercise/TestRecord','Logic')->checkValidSendID($id, $ifDone, $userID);
            return $this->getApiAat('TestRecord/checkValidSendID',$id, $ifDone, $userID);
        }
    }

    /**
     * 描述：implode组合APP提交的带有图片的答案，返回的组合方式为$answerString.'<br />【拍照图片】<br />'.$imageSrc
     * explode 分拆组合数据，返回的数组为['answerString'=>'...','imageSrc'=>['',''...]]
     * @param string $type 操作方式，取值：组合implode 分拆explode
     * @param string $answerString implode的时候为用户文字作答，explode时为整体作答数据
     * @param array $imageSrc 　图片数组，仅仅implode的时候有效
     * @return array|string 如果是分隔，返回数组第一个值为答案，第二个值为空字符串或者图片地址数组；如果是拼接，返回答案
     * @author demo
     */
    public function processAppUserAnswer($type = 'implode', $answerString = '', $imageSrc = []) {
        if ($type == 'implode') {
            if ($imageSrc) {
                $imageTagArr = [];
                foreach ($imageSrc as $src) {
                    $imageTagArr[] = '<img src="' . $src . '">';
                }
                $answerString .= '<br />【拍照图片】<br />' . implode($imageTagArr, '<br />');
            }
            return $answerString;
        } elseif ($type == 'explode') {
            if (strpos($answerString, '【拍照图片】') === false) {
                //不包含
                return [$answerString, []];
            } else {
                $answerArr = explode('【拍照图片】', $answerString);
                $imageTagArr = array_filter(explode('<br />', $answerArr[1]));
                $imageSrcArr = [];
                foreach ($imageTagArr as $imageTag) {
                    preg_match('/<img.+src=\"?(.+\.(jpg|gif|bmp|bnp|png))\"?.+>/i', $imageTag, $match);
                    $src = $match[1];
                    if(!empty($src)) $imageSrcArr[] = $src;
                }
                return [$answerArr[0], $imageSrcArr];
            }
        }
    }

    /**
     * 获取带参数固定缓存数据集
     * 包括：能力，地区，学科、专题、题型、知识点、章节、文档属性、用户权限、管理员权限、模板组卷考试类型、自定义打分规则、年级数据
     * ability area subject special types knowledge chapter doctype powerUserList powerAdminList dirExamtype testMark classGrade
     * @param array $param 数组
     *                  ['style'] 调用数据类型
     *                  ['return']为1则返回json为 2返回数据集
     *                  ['...'] 其他参数
     *
     * @return array
     * @author demo
     */
    public function getData($param = []) {
        $param = $param ? $param : $_POST;
        $result = SD($param);
        if ($param['return'] == 1 || (!$param['return'] && IS_AJAX)) {
            // $this->ajaxReturn($result, 'success', 1);
            $this->setBack($result);
        }
        return $result;
    }

    /**
     * 描述：判断是否是Android平台请求
     * @return bool
     * @author demo
     */
    protected function isAndroid(){
        if($this->appType==1||$this->appType==2){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 描述：判断是否是IOS平台请求
     * @return bool
     * @author demo
     */
    protected function isIOS(){
        if($this->appType==3||$this->appType==4){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 描述：获取平台名称
     * @return string
     * @author demo
     */
    protected function getPlatformName(){
        $appType = $this->appType;
        if($appType){
            $platformArray = [1=>'AndroidPhone',2=>'AndroidPad',3=>'IPhone',4=>'IPad'];
            $name = $platformArray[$appType];
        }else{
            $name = '未知平台';
        }
        return $name;
    }

    /**
     * 描述：获取手机编码
     * @return string
     * @author demo
     */
    protected function getPhoneSn(){
        if(!$this->phoneSn){
            $this->phoneSn = $_REQUEST['phoneSn'];
        }
        return $this->phoneSn;
    }
}
