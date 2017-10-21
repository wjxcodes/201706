<?php
/**
 * @author demo
 * @date 2014年11月3日
 */
/**
 * 分组基类组卷类，用于处理分组通用操作
 */
namespace Home\Controller;
use Common\Controller\DefaultController;
class BaseController extends DefaultController{
    /**
     * 项目初始化
     */
    protected function _initialize(){
    }

    /**
     * 验证用户是否登录
     * @author demo
     * @date 2015-6-6
     */
    public function isLoginCheck(){
        $isAjax = IS_AJAX ? 1 : 0;
        $result = $this->checkLogin('Home', 1);
        if($this->getCookieUserID('Aat')){
            $this->setBack('only');
        }else if(is_string($result)){
            $this->setError($result, $isAjax);
        }
        
        $this->setBack('success');
    }
}
