<?php
/**
 * 首页控制器类，用于处理首页相关操作
 */
namespace Teacher\Controller;
class IndexController extends BaseController {
    /**
     * 载入首页
     * @author demo
     */
    public function index(){
        $this->assign('TeacherName',$this->getCookieUserName()); //用户名称
        $this->assign('pageName', '后台管理中心'); //页面标题
        $u = $_GET['u'];
        if(!empty($u)){
            if(strpos($u, '_') === 0){
                $u = str_replace('_', '/', $u);
            }
        }
        $this->assign('u', $u);
        $this->display();
    }
}