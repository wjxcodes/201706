<?php
/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-5-23
 * Time: 上午9:45
 */
namespace AatApi\Controller;
class MyExerciseController extends BaseController
{
    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
    }

    /**
     * 返回用户某学科下作答试题总量、错误试题数量、收藏试题数量的数据，用户手机端使用
     * @author demo
     */
    public function ajaxGetAmount(){
        $this->checkRequest();
        $username = $this->getUserName();
        $subjectID = $this->getSubjectID();
        //获取做过试题总数量
        $allAmount = $this->getModel('UserForecast')->getAllAmount($username,$subjectID);
        //获取错误总数量
        $wrongAmount = $this->getModel('UserAnswerRecord')->getWrongAmount($username,$subjectID);
        //获取收藏试题总数量
        $collectAmount = $this->getModel('UserCollect')->getCollectAmount($username,$subjectID);


        $this->setBack([
            'allAmount' => $allAmount,
            'wrongAmount' => $wrongAmount,
            'collectAmount' => $collectAmount
        ]);
    }

}