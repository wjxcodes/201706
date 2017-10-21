<?php
/**
 * @author demo 
 * @date 2014年9月22日
 */
/**
 * 用户中心类
 */
namespace AatApi\Controller;
class UserController extends BaseController
{

    protected $userModel;//用户表模型

    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
        $this->userModel = $this->getModel('User');
    }

    /**
     * 切换使用版本
     * @param int version 版本 取值：高考版1 同步版2 true
     * @return array
     *        正确
     *   [
     *       "data"=>null, 
     *       "info"=>"success", 
     *       "status"=>1
     *   ]
     * @author demo
     */
    public function changeVersion() {
        $version = $_REQUEST['version'];
        $username = $this->getUserName();
        $IData = $this->getApiUser('User/changeVersion', $version,$username);
        if($IData[0] == 1){
            $this->setBack($IData[1]);
        }
        $this->setError($IData[1], 1);
    }

    /**
     * 上传头像
     * @author demo
     */
    public function uploadHeaderImg() {
        $username = $this->getUserName();

        $host = C('WLN_DOC_HOST');
        $path = R('Common/UploadLayer/uploadImgOnly',array('userAvatar',2048000));
        if(!strstr($path,'Uploads')){
            $this->setError($path,1);
        }
        $filePath = realpath('./') . $path;
        //上传文件到服务器
        $fileUrl = R('Common/UploadLayer/upFileToServer',array($filePath, 'bbs', 'userAvatar'));
        @unlink($filePath); //删除本地图片
        //bbs接口上传到userAnswer目录
            if (strstr($fileUrl, 'error')) {
                $this->setError('连接远程文件服务器失败，请重试！',1);
            } else {
                $updatePic = $this->getModel('User')->updateData(
                    array('UserPic' => $fileUrl),
                    'UserName="'.$username.'"'
                );
                if ($updatePic) {
                    $this->setBack($host . $fileUrl);
                } else {
                    $this->setError('更新头像失败，请重试！',1);
                }
            }
    }

    /**
     * APP 用户反馈
     * @param string company   反馈内容 true
     * @param string contactWay  用户的联系方式（邮箱/电话/QQ） true
     * @param string deviceName  设备名称(用户手机型号) true
     * @param string systemName  系统(用户系统版本等数据) true
     * @param int network 请求的网络，网络与数字对应关系 true
     * @param sting userName   用户名true
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
     *       "info"=>"反馈失败，请重试！", 
     *       "status"=>0
     *   ]
     * @author demo
     */
    public function feedback() {
        $company = strip_tags($_REQUEST['company'], "<br>");//反馈的内容
        $contactWay = trim($_REQUEST['contactWay']);//邮箱/电话/QQ
        $deviceName = trim($_REQUEST['deviceName']);//设备名称
        $systemName = trim($_REQUEST['systemName']);//系统名称
        $networkName = isset($_POST['network'])?C('NETWORK_TYPE')[$_REQUEST['network']]:'未知网络';//请求的网络
        //验证表单数据格式
        if (!preg_match('/[0-9a-zA-Z\x{4e00}-\x{9fa5}]{4,}/u', $company)) {
            return $this->setError('51008', 1); //反馈内容至少大于四个汉字！
        }
        if (!preg_match('/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$|^[0-9]{2,4}\-?[0-9]{3,7}$/', $contactWay)) {
            return $this->setError('51001', 1); //请输入联系方式！
        }
        //存数据库
        $data['Style'] = 1;
        $data['From'] = 'aatMobile';
        $data['LoadTime'] = time();
        $data['Content'] = '内容：'.$company."<br>邮箱/电话/QQ：".$contactWay."<br>设备名：".$deviceName."<br>系统名称：".$systemName."<br>请求网络：".$networkName;
        $data['UserName'] = $_REQUEST['userName'];
        $feedbackModel = $this->getModel('Feedback');
        $res = $feedbackModel->insertFeedback($data);
        if ($res) {
            $this->setBack(null);
        } else {
            $this->setError('51002', 1); //反馈失败，请重试！
        }
    }
}