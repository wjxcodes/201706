<?php

/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-3-25
 * Time: 下午3:20
 */
namespace AatApi\Controller;
class ExerciseReportController extends BaseController
{
    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
    }


    /**
     * 返回本次测试作答概况json
     * @param int id 练习ID true
     * @return array
     * [
     * "data"=>[
     *   "this_score"=>"0", //本次练习的分数
     *   "real_time"=>1, //本次练习所用时间
     *   "end_time"=>"2015-11-27 10:03",//本次练习结束的时间 
     *   "style"=>"专题模块训练（考点）", //本次练习的类型名称
     *   "avg_score"=>6, //全站平均分
     *   "rank"=>[//排名
     *       "percent"=>96,//击败学生的百分比 
     *       "rank"=>"52", //排名
     *       "all"=>1264//发生排名时的总人数
     *   ], 
     *   "right_amount"=>0, //做对的试题数量
     *   "wrong_amount"=>1, //做错的试题数量
     *   "un_judge_amount"=>0,//无法判断正误的试题数量 
     *   "undo_amount"=>9, 
     *   "all_amount"=>10, //总的试题数量
     *   "right"=>[ ], 
     *   "wrong"=>[//错误的试题信息
     *       [
     *           "TestID"=>"379207", 
     *           "Number"=>"1", 
     *           "OrderID"=>"0", 
     *           "IfRight"=>"1", 
     *           "TestType"=>"1"
     *       ]
     *   ], 
     *   "un_judge"=>[ ], 
     *   "undo"=>[//未做的试题信息
     *       [
     *           "TestID"=>"376838", 
     *           "Number"=>2, //试题在本次练习中的序号
     *           "OrderID"=>0, //试题排序
     *           "IfRight"=>-1//试题状态（-1表示未做）
     *       ]
     *       ...
     *   ]
     * ], 
     * "info"=>"success", 
     * "status"=>1
     * ]
     * @author demo
     */
    public function returnExerciseInfo() {
        $this->checkRequest();
        $recordID = $_REQUEST['id'];
        $username = $this->getUserName();
        // $IData = A('Exercise/Score','Logic')->exerciseInfo($recordID,$username,$isApp=true);
        $IData = $this->getApiAat('Score/exerciseInfo',$recordID,$username,$isApp=true);
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }
        // $this->ajaxReturn($IData);
    }

    /**
     * 返回最近10次测试的分数概况用以生成折线图
     * @param string username 用户名 true
     * @param int subjectID 学科ID true
     * @return array
     * [
     *   "data"=> [
     *       [
     *           "TestID"=> "157453", //练习的记录ID 
     *           "Score"=> "0", //本次练习分数
     *           "RealTime"=> "13", //本次练习所用的时间
     *           "LoadTime"=> "11-27", //本次练习的日期
     *           "SubjectName"=> "语文"//学科名称
     *       ]
     *      ...
     *   ], 
     *   "info"=> "success", 
     *   "status"=> 1
     * ]
     * @author demo
     */
    public function returnScores() {
        $username = $this->getUserName();
        $subjectID = $this->getSubjectID();
        // $IData = A('Exercise/Score','Logic')->scoreList($username,$subjectID,$isApp=true);
        $IData = $this->getApiAat('Score/scoreList',$username,$subjectID,$isApp=true);
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }
        // $this->ajaxReturn($IData);
    }

    /**
     * 返回本次测试对应的每个知识点的试题数量和正确率的树状结构用以生成树
     * @param int id 练习ID  true
     * @return array
     *        [
     *       "data"=>[
     *           [
     *               "KlID"=>"1515", //知识点ID
     *               "KlName"=>"基础知识1",//知识点名称 
     *               "Amount"=>"2/5", // 正确数/总数 
     *               "Rate"=>"40%", //正确率 百分比 例如30%
     *               "sub"=>[[//子类数据 如果有则为二级数据 数据格式同当前列表 
     *                   "KlID"=>"1516", 
     *                   "KlName"=>"基础知识子类1", 
     *                   "Amount"=>"2/5", 
     *                   "Rate"=>"40%", 
     *               ]]
     *           ]
     *       ], 
     *       "info"=>"success", 
     *       "status"=>1
     *   ]
     *    
     *   //错误的情况
     *    
     *   [
     *       "data"=>null
     *       "info"=>"数据参数错误，请重试！", 
     *       "status"=>0
     *   ]
     * @author demo
     */
    public function returnKlInfo() {
        $recordID = $_REQUEST['id'];
        $username = $this->getUserName();
        // $IData = A('Exercise/KlAbility','Logic')->userKlInfo($recordID,$username);
        // $this->ajaxReturn($IData);
           $IData = $this->getApiAat('KlAbility/userKlInfo',$recordID,$username);
        // $this->ajaxReturn($IData);
        if($IData[0] == 1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }
    }

}