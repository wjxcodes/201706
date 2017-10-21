<?php
/**
 * @author demo
 * @date 2014年10月28日
 */
/**
 * 提分系统手机客户端试题接口类
 * @todo 过期
 */
namespace AatApi\Controller;
class ApiExerciseController extends BaseController
{
    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
    }

    /**
     * 描述：手机端上传
     * 兼容之前版本，转发todo2.2.0之后版本删除
     * @param string action 固定：appUserAnswer  true
     * @param image photo 上传的图片   true
     * @return array
     *   [
     *    "data"=>图片URL链接, 
     *    "info"=>success, 
     *    "status"=>1
     *    ]
     * @author demo
     */
    public function uploadImage() {
        A('Exercise')->uploadImage();
    }

    /**
     * 手机端获取测试题目信息
     * 兼容之前版本，转发todo2.2.0之后版本删除
     * @author demo
     */
    public function getTest() {
        A('Exercise')->getTest();
    }

}