<?php
/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-5-23
 * Time: 上午9:45
 */
namespace Aat\Controller;
class MyExerciseController extends BaseController
{
    public function _initialize() {
    }

    /**
     * 显示我的练习页面
     * @author demo
     */
    public function index() {
        $this->assign('pageName','我的练习');
        $this->display();
    }

}