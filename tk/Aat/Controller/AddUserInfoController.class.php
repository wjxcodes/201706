<?php
/**
 * @author demo
 * @date 2014年10月16日
 */
/**
 * 用户登录后补充信息类
 */
namespace Aat\Controller;
class AddUserInfoController extends BaseController
{

    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
    }

    /**
     * 补充信息界面显示
     * @author demo
     */
    public function index() {
        $this->checkRequest();
        $field = $this->getApiUser('User/mustField',$this->getUserID());
        if(count($field) == 0){
            $this->setBack('暂无需要保存的用户数据！');
        }
        $this->display();
    }

    /**
     * Ajax检测登录后是否需要补充完善用户信息
     * @author demo
     */
    public function check() {
        $this->checkRequest();
        $needField = $this->getApiUser('User/mustField',$this->getUserID());
        if(count($needField) == 0){
            //检测通过不需要补充数据
            $this->setBack('success');
        }else{
            $this->setBack($needField);
        }
    }

    /**
     * Ajax 保存用户补充的信息
     * 上传data字段为Json数组例如：{userName:'',realName:'',areaID:'',schoolID:'',gradeID:'',version:''}
     * @author demo
     */
    public function save() {
        $this->checkRequest();
        $userData = $this->getUserInfo($this->getUserName());
        $userID = $userData['UserID'];
        $updateData = $this->getApiAat('User/needFieldSave',$userData);
        if($updateData[0]){
            $updateData=$updateData[1];
            //如果保存用户名成功，则需要更新数据库中的用户名和cookie中的user_name和user_code
            $_csrf='';
            $userName='';
            if ($updateData['UserName']) {
                //需要重新写入cookie 因为用户名修改了和登录时不同
                $saveCode = $userData['SaveCode'];//上面的操作没有修改SaveCode，所以可以使用$user变量
                $authTimeout = $_POST['phone']?3600*24*365:C('WLN_COOKIE_TIMEOUT');
                $userCode = md5($userID . $updateData['UserName'] . $saveCode.ceil(time()/$authTimeout));

                $this->setUserName($updateData['UserName']);
                $this->setUserCode($userCode);

                //需要更新用户csrf
                $_csrf=$this->generateCsrfToken('aat',$updateData['UserName']);
                $userName=$updateData['UserName'];
            }
            //用户昵称
            if($updateData['Nickname']){}
            //如果保存成功，则需要更新cookie中的版本标识
            if($updateData['Version']){
                $this->setVersionID($updateData['Version']);
            }
            if($updateData['GradeID']){
                $this->setGradeID($updateData['GradeID']);
            }

            // $this->ajaxReturn(array('_csrf'=>$_csrf,'UserName'=>$userName),'success',1);
            $this->setBack(array('_csrf'=>$_csrf,'UserName'=>$userName));
        }else{
            // $this->ajaxReturn(null,$updateData[1],0);
            $this->setBack($updataData[1]);
        }
    }

    /**
     * 获取系统生成的用户昵称
     * @author demo
     */
    public function getNickname(){
        $nickName = R('Common/UserLayer/produceNickname');
        // $this->ajaxReturn($nickName,'success',1);
        $this->setBack($nickName);
    }
}
