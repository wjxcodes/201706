<?php
/**
 * @author demo
 * @date 2015年1月6日
 * @update 2015年2月25日
 */
/**
 * 基础控制器类，用于处理基础数据相关操作
 */
namespace Manage\Controller;
use Common\Controller\DefaultController;
class BaseController extends DefaultController {
    public $defaultPower=array(); //不验证权限的方法
    /**
     * 初始化控制器
     * @author demo
     */
    public function __construct() {
        parent::__construct();
    }
}