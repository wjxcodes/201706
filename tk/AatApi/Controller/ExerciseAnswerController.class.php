<?php

/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-4-11
 * Time: 下午3:53
 */
namespace AatApi\Controller;
class ExerciseAnswerController extends BaseController{
    /**
     * 返回试题信息和作答信息
     * @param int id 作业ID或者练习ID
     * @return array
     *[
     * data=>[
     *   time=>2, //当前测试耗时，单位秒
     *   style=>"整卷练习（试卷）", //当前测试名称 
     *   test=>[
     *       [
     *           point=>"1", // 试题分值是否有小数 0为没有小数 1为有小数 小数以0.5为步长
     *           score=>"5", //试题分值 如果是复合题则使用小题中的分值
     *           test_id=>"389359", 
     *           test_title=>"在下面。。。", 
     *           test_type=>"71", 
     *           if_choose=>"3", //试题选择类型：0非选择 1复合题 2单选 3多选
     *           doc_name=>"2015年普通高等学校招生全国统一考试·语文（江苏卷）", 
     *           doc_id=>"25223", 
     *           kl_list=>知识点, 
     *           number=>"1", //试题序号，用于显示
     *           test_options=>选项, //如果是选择题，会出现此字段，数组元素格式，请查看示例
     *           answer_id=>"5", // 用户回答问题的id
     *           answer_score=>"5", //  用户回答问题的分值
     *           answer=>"B", //用户作答数据如“A,B” 或者“汉字文字”
     *           if_right=>"2", //正确情况 -1未答题0无法判断（非选择）1错误2正确
     *           analytic=>"本题。。。。", // 试题解析
     *           right_answer=>"B"//试题正确答案
     *       ], 
     *       ...
     *   ], 
     *   exercise_info=>[//每个题的答题情况，数组，用来生成答题卡
     *       [
     *           TestID=>"389359", 
     *           Number=>"1", //试题序号
     *           OrderID=>"0", //复合题小题的序号，如果不是复合题，为0
     *           IfRight=>"2", // 正确情况 -1未答题0无法判断（非选择）1错误2正确
     *           TestType=>"1"
     *       ]
     *       ...
     *   ], 
     *   exercise_info_amount=>[// 试题统计数量
     *       right=>2, //正确试题数量
     *       wrong=>5, // 错误试题数量
     *       un_judge=>3,//无法判断正误试题数量 
     *       undo=>17, //未做试题数量
     *       all=>27//总数量
     *   ]
     * ], 
     * info=>"success", 
     * status=>1
     * ]
     * @author demo
     */
    public function returnTestList() {
        $this->checkRequest();
        // $IData = A('Exercise/Answer','Logic')->exerciseAnswer($_POST['id'],$this->getUserName());
		$userName = $_POST['userName'];
        $IData = $this->getApiAat('Answer/exerciseAnswer',$_POST['id'],$userName);
        // $this->ajaxReturn($IData);
            if($IData[0] ==1){
                $this->setBack($IData[1]);
            }else{
                $this->setError('51009');
            }
    }

    /**
     * 保存打分数据
     * @author demo
     */
    public function evaluateTest(){
        $answerId = $_POST['answerId'];
        $recordId = $_POST['recordId'];
        $score = $_POST['score'];
        // $data = A('Exercise/Answer','Logic')->evaluateTest($answerId, $recordId, $score, $this->getUserName());
        $data = $this->getApiAat('Answer/evaluateTest',$answerId, $recordId, $score, $this->getUserName());
        if($data[0] ==1){
            $this->setBack($data[1]);
        }else{
            $this->setError($data[1],1);
        }
        // $this->ajaxReturn($data);
    }

    /**
     * 是否完成自评
     * @author demo
     */
    public function isCompleteEvaluation(){
        $record = $_POST['recordId'];
        // $data = A('Exercise/Answer','Logic')->isCompleteEvaluation($record);
        $data = $this->getApiAat('Answer/isCompleteEvaluation',$record);
        if($data){
            $this->setBack(array(1,'success'));
        }
        $this->setError('failure');
    }
}