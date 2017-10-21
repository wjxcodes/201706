<?php
/**
 * @author demo
 * @date 2015年1月3日
 */
/**
 * 提分系统手机客户端报告接口类
 */
namespace AatApi\Controller;
class ApiReportController extends BaseController
{   
    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
    }

    /**
     * 个人学情评估
     * 返回手机端所需数据
     * @param int subjectID 当前学科 true
     * @return array
     * [
     * "data"=>[
     *  "indicator"=>[//雷达图极坐标数据
     *       "集合与常用逻辑用语", 
     *       "函数、导数及其应用", 
     *       ...
     *   ], 
     *   "series"=>[//雷达图折线数据
     *       [
     *           0, 0, 0, 0, 2, 0, 0, 33, 0, 0, 0, 0, 0
     *       ]
     *       ...
     *   ], 
     *   "time"=>"2015-08-25", //报告生成时间
     *   "userName"=>"15838201264",//用户名 
     *   "totalScore"=>"150",//精准预测分总分 
     *   "allAmount"=>"762", //当前答题总量
     *   "needTimes"=>1, //还需要测试几次能生成精准预测分
     *   "exerciseScore"=>"11", //快速预测分
     *   "exerciseLine"=>[//快速预测分折线图数据
     *       [
     *           "ForecastID"=>"108507", 
     *           "ExerciseScore"=>"11", //快速预测分
     *           "ExerciseRanking"=>"428", 
     *           "AllAmount"=>"762", 
     *           "RightAmount"=>"120", 
     *           "WrongAmount"=>"301", 
     *           "UndoAmount"=>"336", 
     *           "LoadTime"=>"8/24"//时间
     *       ]
     *       ...
     *   ], 
     *   "forecastScore"=>"16", //精准预测分
     *   "forecastLine"=>[//精准预测分折线数据
     *       [
     *           "ForecastID"=>"108457", 
     *           "ForecastAbility"=>"-3.000", 
     *           "ForecastScore"=>"16", //精准预测分
     *           "LoadTime"=>"8/6"//时间
     *       ]
     *       ...
     *   ]
     * ], 
     * "info"=>"success", 
     * "status"=>1
     * ]
     * @author demo
     */
    public function data(){
        $this->checkRequest();
        $userName = $this->getUserName();
        $subjectID = $this->getSubjectID();
        // $data1 = A('Exercise/KlAbility','Logic')->abilityRadarMobile($userName,$subjectID);
        $data1 = $this->getApiAat('KlAbility/abilityRadarMobile',$userName,$subjectID);
        // $data2 = A('Exercise/Score','Logic')->scoreLineMobile($userName,$subjectID);
        $data2 = $this->getApiAat('Score/scoreLineMobile',$userName,$subjectID);
        $result = array_merge($data1,$data2);
        // $this->ajaxReturn($result,'success',1);
        $this->setBack($result);
    }
}