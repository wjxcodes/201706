<?php
/**
 * @author demo 
 * @date 2014年10月10日
 */
/**
 * 提分系统基类，系统常用类属性和方法放在这里
 */
namespace Aat\Controller;

use Common\Controller\DefaultController;

class BaseController extends DefaultController
{
    private $userInfo = null;

    /**
     * __construct
     * 优先级 Action>Index>AatBase
     * @todo 兼容手机端之前的-形式的请求道AatApi分组
     * @author demo
     */
    public function __construct() {
        $this->init();
        parent::__construct();
        //判断平台和设备【注意】不能放到init方法中，是因为assign需要Action中的构建函数里的view初始化
        //web请求
        IS_GET && $this->assign('_csrf', $this->generateCsrfToken('aat', $this->getUserName()));
    }

    /**
     * 前置操作
     * @author demo
     */
    private function init() {

    }

    /**
     * App和Web端请求检测，集成CSRF功能
     * 不通过则中断程序返回错误信息
     * 【注意】flash图片上传不要使用
     * @author demo
     */
    protected function checkRequest() {
        //可以跳过的路径
        $arr=array('UnionExam');
        if(in_array(CONTROLLER_NAME,$arr)) return ;

        //web平台
        if ($this->checkCsrfToken('aat', $this->getUserName()) === false) {
            if (IS_AJAX) {
                // $this->ajaxReturn(null, '用户token错误，请刷新重试！', 0);
                $this->setBack('用户token错误，请刷新重试！');
            } else {
                header('Location:'.U('Aat/Default/index'));
            }
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
            $this->userInfo = $this->getApiUser('User/getInfoByName',$userName,'*')[0];
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
         return $this->getApiAat('Subject/getNameByID',$subjectID);
    }

    /**
     * 返回当前学科ID
     * @return int 获取学科ID
     * @author demo
     */
    protected function getSubjectID() {
        $cookieSubjectID = $this->getCookieSubjectID();
        if (empty($cookieSubjectID)) {
            $cookieSubjectID = $_REQUEST['subjectID'];
        }
        return $cookieSubjectID;
    }

    /**
     * 检测当前学科ID是否正确
     * @author demo
     */
    protected function checkSubjectID() {
        $subjectID = $this->getSubjectID();
        if ($this->getApiDir('Subject/checkValidID',$subjectID) == false) {
            if (IS_AJAX) {
                // $this->ajaxReturn(null, '请先选择学科！', 0);
                $this->setBack('请先选择学科！');
            } else {
                $this->setMsg('请先选择学科！');
                $this->redirect('Default/index');
            }
        }
    }

    /**
     * 设置版本ID
     * @param int $versionID 版本ID
     * @param int $expire 过期时间，默认一年（ 永不过期）
     * @author demo
     */
    protected function setVersionID($versionID, $expire = 31104000) {
        $this->setCookieVersionID($versionID,$expire);
    }

    /**
     * 返回当前版本ID
     * @return int 获取版本ID
     * @author demo
     */
    protected function getVersionID() {
        $cookieVersionID = $this->getCookieVersionID();
        if (empty($cookieVersionID)) {
            $cookieVersionID = $_REQUEST['userVersion'];
        }
        return $cookieVersionID;
    }

    /**
     * 设置学科ID
     * @param int $subjectID 学科ID
     * @param int $expire 过期时间，默认一年（ 永不过期）
     * @author demo
     */
    protected function setSubjectID($subjectID, $expire = 31104000) {
        $this->setCookieSubjectID($subjectID,$expire);
    }

    /**
     * 返回用户名
     * @return string 用户名
     * @author demo
     */
    protected function getUserName() {
        $userName = $this->getCookieUserName();
        if (empty($userName)) {
            $userName = $_REQUEST['userName']?$_REQUEST['userName']:$_REQUEST['UserName'];
        }
        return $userName;
    }

    /**
     * 返回用户ID
     * @return int 用户ID
     * @author demo
     */
    protected function getUserID() {
        $userID = $this->getCookieUserID();
        if(!$userID){
            $userID = $this->getUserInfo($this->getUserName())['UserID'];
        }
        return $userID;
    }

    /**
     * 描述：写入
     * @param $code
     * @param int $expire
     * @author demo
     */
    protected function setUserID($code, $expire = 31104000){
        $this->setCookieUserID($code,$expire);
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
     * 设置用户名
     * @param string $userName 用户名
     * @param int $expire 过期时间
     * @author demo
     */
    protected function setUserName($userName, $expire = 31104000) {
        $this->setCookieUserName($userName,$expire);
    }

    /**
     * 获取安全码
     * @author demo
     */
    protected function getUserCode() {
        $userCode = $this->getCookieCode();
        if (empty($userCode)) {
            $userCode = $_REQUEST['userCode'];
        }
        return $userCode;
    }

    /**
     * 设置安全码
     * @param string|null $code 安全码
     * @param int $expire 过期时间
     * @author demo
     */
    protected function setUserCode($code, $expire = 0) {
        $this->setCookieCode($code,$expire);
    }

    /**
     * 获取首页错误弹出框信息
     * @author demo
     */
    protected function getMsg() {
        return $this->getCookieIndexMsg();
    }

    /**
     * 写入首页错误弹出框信息
     * @param string $message 消息
     * @author demo
     */
    protected function setMsg($message) {
        $this->setCookieIndexMsg($message);
    }

    /**
     * 写入cookie年级ID
     * @param int $gradeID 年级ID
     * @param int $expire 过期时间
     * @author demo
     */
    protected function setGradeID($gradeID, $expire = 31104000) {
        $this->setCookieGradeID($gradeID,$expire);
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
            return $this->getApiAat('TestRecord/checkValidExerciseID',$id,$ifDone,$username);
        } else if ($recordType === 'homework') {//作业
            $userID = $this->getUserID();
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
            return $answerString;
        } elseif ($type == 'explode') {
            return [$answerString, []];
        }
    }


    /**
     * 编辑器图片上传 2015-5-28
     * @author demo
     */
    public function upload() {
        $dir = $_REQUEST['dir'];
        R('Common/UploadLayer/upload', array($dir));
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

}
