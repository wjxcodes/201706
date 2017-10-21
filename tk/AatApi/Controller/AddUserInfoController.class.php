<?php
/**
 * @author demo
 * @date 2014年10月16日
 */
/**
 * 用户登录后补充信息类
 */
namespace AatApi\Controller;
class AddUserInfoController extends BaseController
{

    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
    }


    /**
     * Ajax检测登录后是否需要补充完善用户信息
     * @return array
     *        需要补全信息的情况
     *   [
     *       "data"=>[
     *           "Nickname", 
     *           "UserName",
     *           "RealName", 
     *           "AreaID", 
     *           "SchoolID", 
     *           "GradeID", 
     *           "Version"
     *       ], 
     *       "info"=>"需要完善用户信息！", 
     *       "status"=>0
     *   ]
     *   不需要补全信息的情况
     *  [
     *       "data"=> true, 
     *       "info"=> "success", 
     *       "status"=> 1
     *   ]
     * @author demo
     */
    public function check() {
        $this->checkRequest();
        // $needField = A('Aat/User','Logic')->needField($this->getUserID());
        $needField = $this->getApiUser('User/mustField',$this->getUserID());
        if(count($needField) == 0){
            //检测通过不需要补充数据
            // $this->ajaxReturn(true,'success',1);
            $this->setBack('success');
        }else{
            // $this->ajaxReturn($needField,'需要完善用户信息！',0);
            exit(json_encode(array(
                'info' => '需要完善用户信息！',
                'status' => 0,
                'data' => $needField,
            )));
            $this->setError($needField);
        }
    }

    /**
     * Ajax 保存用户补充的信息
     * 上传data字段为Json数组例如：{userName:'',realName:'',areaID:'',schoolID:'',gradeID:'',version:''}
     * @param array realName 真实姓名 false
     * @param array nickname  昵称2-5个汉字 6-15字符 false
     * @param array areaID 地区ID false
     * @param array gradeID 年级ID false
     * @param array schoolID 学校ID false
     * @param array version 版本 1高考 2同步 false
     * @param array userName 用户名，邮箱或手机号 false 
     * @param array phoneCode 手机验证码，如果账号是手机号则必须  false
     * @param array emailCode 邮箱验证码，如果账号是邮箱则必须 false
     * @return array
     *   [
     *   "data"=>$mobileNeedUpdate, 
     *   "info"=>"success", 
     *   "status"=>1
     *   ]
     * [
     *   "data"=>null, 
     *   "info"=>$updateData, 
     *   "status"=>0
     * ]
     * @author demo
     */
    public function save() {
        $this->checkRequest();
        $userData = $this->getUserInfo($this->getUserName());
        $userID = $userData['UserID'];
        // $updateData = A('Aat/User','Logic')->needFieldSave($userData);
        $updateData = $this->getApiAat('User/needFieldSave',$userData);

        $status = $updateData[0];
        $updateData = $updateData[1];
        if($status==1){
            $mobileNeedUpdate = [];//手机端需要写入到本地的数据
            //如果保存用户名成功，则需要更新数据库中的用户名和cookie中的user_name和user_code
            if ($updateData['UserName']) {
                //需要重新写入cookie 因为用户名修改了和登录时不同
                $saveCode = $userData['SaveCode'];//上面的操作没有修改SaveCode，所以可以使用$user变量

                $authTimeout = $_POST['phone']?3600*24*365:C('WLN_COOKIE_TIMEOUT');
                $userCode = md5($userID . $updateData['UserName'] . $saveCode. ceil(time()/$authTimeout));
                $mobileNeedUpdate['userName'] = $updateData['UserName'];
                $mobileNeedUpdate['userCode'] = $userCode;
            }
            //用户昵称
            if($updateData['Nickname']){
                $mobileNeedUpdate['Nickname']=$updateData['Nickname'];
            }
            if($updateData['Version']){
                $mobileNeedUpdate['version'] = $updateData['Version'];
            }
            if($updateData['GradeID']){
                $mobileNeedUpdate['gradeID'] = $updateData['GradeID'];
            }
            // $this->ajaxReturn($mobileNeedUpdate,'success',1);
            $this->setBack($mobileNeedUpdate);
        }else{
            // $this->ajaxReturn(null,$updateData,0);
            $this->setBack($updateData);
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
