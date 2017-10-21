<?php
/**
 * @author demo
 * @date 2015年1月28日
 */
/**
 * 手机APP,处理关于手机APP的一些操作
 */
namespace Aat\Controller;
class AppController extends BaseController{
    /**
     * 首页显示
     * @author demo
     */
    public function index() {
        $this->assign('pageName','APP客户端下载');
        //检测手机端后跳转code
        if(isMobile()){
            header('location:'.U('Aat/App/code'));
            exit();
        }

        $this->display();
    }

    public function code(){
        //检测手机端后跳转code
        if(!isMobile()){
            header('location:'.U('Aat/App/index'));
            exit();
        }
        $this->assign('pageName','APP客户端下载');
        $this->display();
    }
    /**
     * 手机APP下载，判断如果是苹果设备则跳转到苹果商店，否则就直接下载APK文件
     * @author demo
     */
    public function down(){
        $type = $this->_getDeviceType();
        if ($type == 'ios') {
            header("Location: http://fusion.qq.com/app_download?appid=&platform=qzone&via=QZ.MOBILEDETAIL.QRCODE&u=");//目前还没有苹果商店的链接
        }else{
            header("Location: http://fusion.qq.com/app_download?appid=&platform=qzone&via=QZ.MOBILEDETAIL.QRCODE&u=");
        }
    }
    /**
     * 更新日志页面
     * @author demo
     */
    public function updateLog(){
        $this->assign(['list'=>$this->getUpdateData()]);
        $this->display();
    }

    /**
     * 描述：手机端Android更新日志页面
     * @author demo
     */
    public function mUpdateLog(){
        $this->assign(['list'=>$this->getUpdateData()]);
        $this->display();
    }

    /**
     * 描述：获取更新数据
     * @author demo
     */
    private function getUpdateData(){
        $data = [
            [
                'version'=>'1.0.0',
                'date'=>'2015-1-10',
                'content'=>['公测版正式发布',]
            ],
        ];
        return $data;
    }

    /**
     * @return string 设备操作系统类型 三种类型ios android other
     * @author demo
     */
    private function _getDeviceType(){
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $type = 'other';
        if(strpos($agent, 'iphone') || strpos($agent, 'ipad') || strpos($agent,'ipod')){
            $type = 'ios';
        }
        if(strpos($agent, 'android')){
            $type = 'android';
        }
        return $type;
    }
	
	/**
     * 移动端登录接口
     * @author demo 16-5-11
     */
    public function AppLogin(){
        $username = $_POST['username'];
        $possword = $_POST['password'];
        $role = $_POST['role'];
        $remember = $_POST['remember'];
		
		/*$username = '123456789@qq.com';
        $possword = '111111';
        $role = '0';
        $remember = '0';*/

        
        $result = $this->getApiUser('User/login', $username, $possword, $remember, $role);
        if(0 == $result[0]){
            exit(R('Common/SystemLayer/ajaxSetError',array($result[1],2)));
        }
        //成功后返回信息
        //$userinfo = $this->getUserInfo($username);
        $info['status'] = 1;
        $info['info'] = '';//如果是错误信息，放在这里
        $info['data']['UserName'] = $username;
        $info['data']['UserCode'] = $_COOKIE['wln_aat_CODE'];
        $info['data']['UserVersion'] = $_COOKIE['wln_aat_VERSIONID'];
		
        $info['needField'][] = 'UserName';
        $info['needField'][] = 'RealName';
        $info['needField'][] = 'NickName';
        $info['needField'][] = 'AreaIdD';
        $info['needField'][] = 'SchoolID';
        $info['needField'][] = 'GradeID';
        $info['needField'][] = 'Version';
        return json_encode($info,JSON_FORCE_OBJECT);
    }
	
	
}