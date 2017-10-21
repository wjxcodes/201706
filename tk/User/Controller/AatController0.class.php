<?php
/**
 * @author demo 
 * @date 2014年9月22日
 */
/**
 * 用户中心类
 */
namespace User\Controller;
use Common\Controller\DefaultController;
class AatController extends DefaultController{

    protected $userModel;//用户表模型

    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
        $this->userModel = $this->getModel('User');
        IS_GET && $this->assign('_csrf', $this->generateCsrfToken('aat', $this->getCookieUserName()));

    }

    /**
     * 用户中心首页显示
     * @author demo
     */
    public function index() {
        $this->assign('pageName','用户中心');
        $this->display();
    }

    /**
     * 返回用户信息
     * @return string 个人信息
     * @author demo
     */
    public function UserInfo() {
        $user = $this->userModel->findData(
            '*',
            'UserName="'.$this->getCookieUserName().'"'
        );
        $result = [];//返回数据
        if ($user) {
            $result['userName'] = $user['UserName'];
            $result['realName'] = $user['RealName'];
            $result['orderNum'] = $user['OrderNum'];
            $result['version'] = $user['Version'];
            $result['loginTime'] = date('Y-m-d H:i', $user['LastTime']);//查询上次登录信息
            $result['loginIP'] = $user['LastIP'];
            $result['gradeID'] = $user['GradeID'];
            $result['phone'] = $user['Phonecode'];
            $result['email'] = $user['Email'];
            $result['checkEmail']=$user['CheckEmail'];
            $result['checkPhone']=$user['CheckPhone'];
            //头像兼容性处理
            if($user['UserPic']){
                if(!preg_match('/^http:.*/i',$user['UserPic'])){//判断是不是QQ头像
                    $user['UserPic'] = C('WLN_DOC_HOST') . $user['UserPic'];//非QQ头像
                }
            }else{//调用默认头像
                $user['UserPic'] = __ROOT__ . '/Public/newAat/images/default.jpg';
            }
            //$result['userPic'] = $user['UserPic'] ? (C('WLN_DOC_HOST') . $user['UserPic']) :(__ROOT__ . '/Public/newAat/images/default.jpg');//用户图像
            $result['userPic'] = $user['UserPic'];

            if ($user['EndTime'] > time()) {
                $result['chargeMode'] = '包月用户';
                $result['chargeEnd'] = date('Y-m-d', $user[0]['EndTime']);
                $result['chargeLeave'] = ceil(($user[0]['EndTime'] - time()) / 3600 / 24);
            } else {
                $result['chargeMode'] = '普通用户';
                $result['chargeLeave'] = 0;
            }
            //-------------------地区学校--------------------------------------------------------------------------------
            if ($user['AreaID']) {
                $areaModel = $this->getModel('Area');

                $result['areaName'] = $areaModel->getAreaPathById($user['AreaID']);
                $result['schoolName'] = $this->getModel('School')->selectData(
                    'SchoolName',
                    'SchoolID='.$user['SchoolID']
                )[0]['SchoolName'];
                //初始化地区和学校
                //$areaParent = $areaModel->getAreaParentById($user['AreaID']);
                $areaParent = SS('areaParentPath')[$user['AreaID']];
                //@todo model层get_cache改为了getCache确认后删除 12月19日
                //$areaChildArr = $areaModel->get_cache('AreaChildList');
                $areaChildArr =SS('areaChildList');
                $areaInit = '';
                if ($areaParent) {
                    //krsort($areaParent);使用新的缓存后不需要逆向排序
                    $tmpArr = array();
                    foreach ($areaParent as $iAreaParent) {
                        $tmpArr[] = $iAreaParent;
                    }
                    $areaParent = $tmpArr;
                    $areaParent[]['AreaID'] = $user['AreaID'];
                    foreach ($areaParent as $i => $iAreaParent) {
                        if ($i == 0){
                            $tmpId = 0;
                        }else{
                            $tmpId = $areaParent[$i - 1]['AreaID'];
                        }
                        $areaChild = $areaChildArr[$tmpId];
                        $areaInit .= '<select class="areaSelect">';
                        foreach ($areaChild as $iAreaChild) {
                            $isEnd = 0;
                            $select = '';
                            if ($iAreaChild['Last'] == 1) {
                                $isEnd = 1;
                            }
                            if ($iAreaChild['AreaID'] == $iAreaParent['AreaID']){
                                $select = ' selected="selected" ';
                            }
                            $areaInit .= '<option value="' . $iAreaChild['AreaID'] . '" isEnd="' . $isEnd . '"' . $select . '>' . $iAreaChild['AreaName'] . '</option>';
                        }
                        $areaInit .= '</select>';
                    }
                }
                $result['areaInit'] = $areaInit;

                $where = array(
                    'AreaID' => $user['AreaID'],
                    'Status' => 2,// 0未审核 1不可用 2可用
                    'Type' => array('in', array(1, 2))
                );
                $school = $this->getModel('School')->selectData(
                    'SchoolID,SchoolName',
                    $where
                );
                $schoolInt = '';
                if ($school) {
                    $schoolInt = '<select class="schoolSelect">';
                    foreach ($school as $iSchool) {
                        $select = '';
                        if ($iSchool['SchoolID'] == $user['SchoolID']){
                            $select = ' selected="selected" ';
                        }
                        $schoolInt .= '<option value="' . $iSchool['SchoolID'] . '"' . $select . '>' . $iSchool['SchoolName'] . '</option>';
                    }
                    $schoolInt .= '</select>';
                }
                $result['schoolInit'] = $schoolInt;
            }
            $this->setBack($result);
        } else {
            $this->setError('获取用户信息错误',1);
        }
    }

    /**
     * 切换使用版本
     * @author demo
     */
    public function changeVersion() {
        $version = $_REQUEST['version'];
        $result = $this->getApiUser('User/changeVersion', $version, $this->getCookieUserName());
        if(0 == $result['status']){
            if('30301' == $result['info']){
                $this->setError('参数错误，请重试！',1);
            }else{
                $this->setError('更新用户数据失败，请重试！',1);
            }
        }
        $this->setBack(null);
        // if($version!=1&&$version!=2){
        //     $this->ajaxReturn(['data'=>null,'info'=>'参数错误，请重试！','status'=>0]);
        // }
        // $updateData['Version'] = $version;
        // $update = $this->getModel('User')->updateData(
        //     $updateData,
        //     'UserName="'.$this->getCookieUserName().'"'
        // );
        // if ($update !== false) {
        //     //$update 可能等于零
        //     $this->ajaxReturn(['data'=>null,'info'=>'success','status'=>1]);
        // }else{
        //     $this->ajaxReturn(['data'=>null,'info'=>'更新用户数据失败，请重试！','status'=>0]);
        // }

    }

    /**
     * 修改个人信息
     * @return string 修改结果
     * @author demo
     */
    public function changeInfo() {
        $realName = formatString('stripTags',$_REQUEST['realName']);
        $oldPassword = $_REQUEST['password'][0];
        $password = $_REQUEST['password'][1];
        $areaID = $_REQUEST['areaID'];
        $schoolID = $_REQUEST['schoolID'];
        $gradeID = $_REQUEST['gradeID'];
        $phone = $_REQUEST['phone'];
        //$email = $_REQUEST['email'];

        $userName = $this->getCookieUserName();
        $updateData = [];//需要更新的数据
        //----------------------用户真实姓名或昵称-----------------------------------------------------------------------
        $realNameLength = strlen($realName);
        if($realNameLength<6||$realNameLength>30){
            $this->setError('昵称必须大于2个汉字小于10个汉字！',1);
        }
        //用户昵称过滤
        $filter = $this->userModel->NameFilter($realName);
        if($filter['errornum'] !== 'success'){
            $replace='';
            if($filter['replace']){
                $replace='【'.$filter['replace'].'】';
            }
            $this->setError('非法数据'.$replace.'，请更换',1);
        }
        $updateData['RealName'] = $realName;
        $userID = $this->getCookieUserID();
        //----------------------用户密码---------------------------------------------------------------------------------
        if ($oldPassword != '') {
            //需要修改密码
            if(strlen($oldPassword)<6||strlen($oldPassword)>18){
                $this->setError('原密码必须是6至18位！',1);
            }
            $check = R('Common/PowerLayer/commonCheckLogin',[$userName, $oldPassword, 'password']);//如果出错，此函数会返回错误信息字符串
            if ($check !== 1) {
                $this->setError('原密码错误！',1);
            }
            if (strlen($password) < 6 || strlen($password) > 18) {
                $this->setError('请填写6-18位新密码！',1);
            }
            $updateData['Password'] = md5($userName . $password);
            $updateData['SaveCode'] = $this->userModel->saveCode();//更新安全码
            $updateData['TmpPwd'] = 0;
        }
        //----------------------地区------------------------------------------------------------------------------------
        $buffer=SS('areaList')[$areaID]['AreaName'];
        if($buffer == null){
            $this->setError('选择的地区不存在，请重试！',1);
        }
        $updateData['AreaID'] = $areaID;
        //----------------------学校------------------------------------------------------------------------------------
        $schoolData = $this->getModel('School')->selectData(
                        '*',
                        'SchoolID='.$schoolID
        )[0];
        if($schoolData == null){
            $this->setError('选择的学校不存在，请重试！',1);
        }
        $updateData['SchoolID'] = $schoolID;
        //----------------------年级------------------------------------------------------------------------------------
        $classGrade = $this->getModel('School')->selectData(
            '*',
            'GradeID='.$gradeID,
            '',
            '',
            'ClassGrade'
        )[0];
        if($classGrade == null){
            $this->setError('选择的年级不存在，请重试！',1);
        }
        $updateData['GradeID'] = $gradeID;
        //----------------------检查重复性的错误-------------------------------------------------------------------------
        $repeatError = $this->userModel->checkUser('',$phone,'',$currId=$this->getCookieUserID());
        //@todo 之后使用错误码代替
        if($repeatError == '30224'){
            $this->setError('手机号码已经存在！',1);
        }
        if($repeatError == '30225'){
            $this->setError('邮箱已经存在！',1);
        }
        //----------------------保存------------------------------------------------------------------------------------
        $update = $this->getModel('User')->updateData(
            $updateData,
            'UserName="'.$userName.'"'
        );
        if ($update !== false) {
            //$update 可能等于零
            if($updateData['Password']){
                //如果修改了密码，需要更新SaveCode，本地需要更新Cookie
                $time = $_POST['phone']?3600*24*365:C('WLN_COOKIE_TIMEOUT');
                $code = md5($userID . $userName . $updateData['SaveCode'].ceil(time()/$time));
                $this->setCookieCode($code,$expire=0);
            }
            $this->setBack(null);
        }else{
            $this->setError('更新用户数据失败，请重试！',1);
        }
    }

    /**
     * 购买服务（未做）
     * @todo 实现
     */
    public function buy() {
        exit('<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>购买续费 <a href="' . $this->config['PrePath'] . '">返回首页</a>');
    }
    /**
     * 发送邮箱验证码，用于用户中心邮箱验证功能
     * @author demo
     */
    public function sendEmailCode(){
        $email=$_POST['data'];
        if(!$email){
            $this->setError('邮箱不能为空！',1);
        }
        $userID  =$this->getCookieUserID();
        $output  = R('Common/UserLayer/sendEmailCode',array($email,$userID));

        //输出错误信息
        if($output[0] == 1){
            $this->setError($output[1]);
        }
        $this->setBack('');
    }

    /**
     * 检验邮箱验证码
     * @author demo
     */
    public function checkEmailCode(){
        $email=$_POST['data'];//邮箱
        if(!$email){
            $this->setError('邮箱不能为空！',1);
        }

//      $code=$_POST['code'];//验证码
//      if(!$code){
//          $this->setError('验证码不能为空！',1);
//      }

        $status = 1;//状态
        if($_POST['dataAttr']=='edit'){
            $status = 2;
        }

        $userID  = $this->getCookieUserID();
//      $output  = R('Common/UserLayer/checkEmailCode',array($email,$code,$userID,$status));

        //输出错误信息
//      if($output[0] == 1){
//          $this->setError($output[1],1);
//      }

        $this->setBack('验证成功！');
    }

    /**
     * 发送短信验证码
     * @author demo
     */
    public function sendPhoneCode(){
        $phoneNum=$_POST['data'];
        if(!$phoneNum){
            $this->setError('手机号不能为空！',1);
        }
        $imgCode=$_POST['imgCode'];
        if(md5($imgCode) != session('AatVerify')){
            $this->setError('验证码错误！',1);
        }
        $userID  =$this->getCookieUserID();
        $output  = R('Common/UserLayer/sendPhoneCode',array($phoneNum,$imgCode,$userID));
        //输出错误信息
        if($output[0] == 1){
            $this->setError($output[1],1);
        }

        $this->setBack('');
    }

    /**
     * 检验邮箱验证码
     * @author demo
     */
    public function checkPhoneCode(){
        $phoneNum=$_POST['data'];//邮箱
        if(!$phoneNum){
            $this->setError('手机号不能为空！',1);
        }

//      $code=$_POST['code'];//验证码
//      if(!$code){
//          $this->setError('验证码不能为空！',1);
//      }

        $status = 1;//状态
        if($_POST['dataAttr']=='edit'){
            $status = 2;
        }

        $userID  =$this->getCookieUserID();
        //$output  = R('Common/UserLayer/checkPhoneCode',array($phoneNum,$code,$userID,$status));

        //输出错误信息
//      if($output[0] == 1){
//          $this->setError($output[1],1);
//      }

        $this->setBack('验证成功！');
    }
    /**
     * 修改头像页面
     * @author demo
     */
    public function pic() {
        $username = $this->getCookieUserName();
        $key = md5(C('DOC_HOST_KEY') . $username . date('Y.m.d', time()));
        $this->assign(array('key' => $key, 'username' => $username)); //密钥
        $this->assign('pageName','修改头像');
        $this->display();
    }

    /**
     * 插件上传头像
     * @author demo
     */
    public function avatarUpload() {
        if ($_GET['key'] != md5(C('DOC_HOST_KEY') . $_GET['username'] . date('Y.m.d', time()))) {
            $this->setError('安全验证失败，请重试！',1);
        }
        $path = R('Common/UploadLayer/uploadImgOnly',array('userAvatar',2048000));

        if(!strstr($path,'Uploads')){
            $this->setError($path,1);
        }
        $imgSize = explode('"', getimagesize(realpath('./').$path)[3]);

        $result = array(
            'return' => 2,
            'picPath' => $path,
            'width' => $imgSize[1],
            'height' => $imgSize[3],
        );
        $this->setBack($result);
    }

    /**
     * 图片插入数据库
     * @author demo
     */
    public function avatarSave() {
        $host = C('WLN_DOC_HOST');
        if ($host) {
            $picPath = $_POST['picPath'];
            $width = $_POST['width'];
            $height = $_POST['height'];
            $x1 = $_POST['x1'];
            $y1 = $_POST['y1'];
            if (!isset($picPath) || !isset($width) || !isset($height) || !isset($x1) || !isset($y1)) {
                $this->setError('参数不完整，请刷新页面重试！',1);
            }
            $this->_avatarProcess($picPath, $width, $height, $x1, $y1);
            $filePath = realpath('./') . $picPath;
            //上传文件到服务器
            $fileUrl = R('Common/UploadLayer/upFileToServer',array($filePath, 'bbs', 'userAvatar'));
            @unlink($filePath); //删除本地图片
            //bbs接口上传到userAnswer目录
            if (strstr($fileUrl, 'error')) {
                $this->setError('连接远程文件服务器失败，请重试！',1);
            } else {
                $updatePic = $this->getModel('User')->updateData(
                    array('UserPic' => $fileUrl),
                    'UserName="'.$this->getCookieUserName().'"'
                );
                if ($updatePic) {
                    $this->setBack($host . $fileUrl);
                } else {
                    $this->setError('更新头像失败，请重试！',1);
                }
            }
        } else {
            $this->setError('服务器连接失败，请重试！',1);
        }
    }

    /**
     * 描述：用户重新上传时删除之前上传的图片
     * @author demo
     */
    public function avatarTpmDel() {
        $picPath = $_POST['picPath'];
        if($picPath){
            $filePath = realpath('./') . $picPath;
            @unlink($filePath); //删除本地图片
        }
        $this->setBack(null);
    }

    /**
     * 处理头像图片
     * @param string $picPath
     * @param int $width
     * @param int $height
     * @param int $x1
     * @param int $y1
     * @return bool 是否成功
     * @author demo
     */
    private function _avatarProcess($picPath, $width, $height, $x1, $y1) {
        $targetPic = realpath('./') . $picPath;//目标文件覆盖源文件
        //判断文件类型
        if (stristr($picPath, '.jpeg') != false || stristr($picPath, '.jpg') != false) {
            $image = imagecreatefromjpeg($targetPic);
        } elseif (stristr($picPath, '.png') != false) {
            $image = imagecreatefrompng($targetPic);
        } elseif (stristr($picPath, '.gif') != false) {
            $image = imagecreatefromgif($targetPic);
        } else {
            $this->setError('待处理图片格式错误！',1);
        }
        $copy = $this->_imageCrop($image, $x1, $y1, $width, $height);//裁剪
        imagejpeg($copy, $targetPic, 80);  //输出新图
    }

    /**
     * 裁切图片
     * @param  resource $image 图片资源
     * @param int $x 横坐标
     * @param int $y 纵坐标
     * @param int $w 裁切宽度
     * @param int $h 裁切高度
     * @return bool|resource 图片资源
     * @author demo
     */
    private function _imageCrop($image, $x, $y, $w, $h) {
        $tw = imagesx($image);
        $th = imagesy($image);

        if ($x > $tw || $y > $th || $w > $tw || $h > $th) {
            return FALSE;
        }
        $dstW = 150;//头像宽
        $dstH = 150;//头像高
        $temp = imagecreatetruecolor($dstW, $dstH);//创建真彩色图片资源连接
        //imagecopyresampled函数的参数：
        //(目标图象连接资源,源图象连接资源,目标X坐标点,目标Y坐标点,源的X坐标点,源的Y坐标点,目标宽度,目标高度,源图象的宽度,源图象的高度)
        imagecopyresampled($temp, $image, 0, 0, $x, $y, $dstW, $dstH, $w, $h);
        return $temp;
    }

}