<?php
/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-6-24
 * Time: 下午3:53
 */
namespace AatApi\Controller;
class MyHomeworkAnswerController extends BaseController
{
    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
    }

    /**
     * 返回试题信息和作答信息
     * @param int id 作业ID或者练习ID
     * @return array
     *[
     * data=> [
     *   。。。其他同练习
     *   StartTime=> "08月06日16:22",//作业-作业开始时间（允许作答的开始时间）, 
     *   EndTime=>"08月09日23:59", // 作业-作业限制结束时间
     *   LoadTime> "08月06日16:22", 
     *   CheckTime=> null, //作业-教师批改时间
     *   UserName=>"admin", //作业-布置作业者的名称
     *   Message=> "", //   作业-留言
     *   SubjectName=> "数学", 
     *   DoTime=> "46", //作业-答题时间 
     *   Score=> 0, //  作业-本次作业分数
     *   。。。其他同练习
     * ], 
     * info=> "success", 
     * status=> 1
     * ]
     * @author demo
     */
    public function returnTestList() {
        $this->checkRequest();
        $sendID = $_REQUEST['id'];
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Answer/homeworkAnswer', $sendID,$userID);
        if($IData[0]==0){
            return $this->setError($IData[1], 1);
        }
        $testModel = $this->getModel('Test');
        $userAnswerRecordModel = $this->getModel('UserAnswerRecord');
        $testArr = [];//需要返回的试题数据
        $testData = $IData[1]['test'];//testRealModel中查询到的试题数据
        $recordData = $IData[1]['record'];//testRealModel中查询到的测试记录数据
        //遍历此次测试的所有试题和之前作答信息
        foreach ($testData as $i => $iTestData) {
            $testArr[$i]['test_id'] = $iTestData['TestID'];
            $testArr[$i]['test_title'] = $iTestData['Test']['title'];
            $testArr[$i]['test_type'] = $iTestData['TypesID'];
            $testArr[$i]['if_choose'] = $iTestData['IfChoose'];
            $testArr[$i]['doc_name'] = $iTestData['DocName'];
            $testArr[$i]['doc_id'] = $iTestData['DocID'];
            $testArr[$i]['kl_list'] = $iTestData['KlList'];
            //根据题型判断
            if ($iTestData['IfChoose'] == 3 || $iTestData['IfChoose'] == 2) {
                //单选 多选
                $testArr[$i]['number'] = $iTestData['doAnswer'][0]['Number'];
                $testArr[$i]['test_options'] = $iTestData['Test']['options'];
                $testArr[$i]['answer'] = $iTestData['doAnswer'][0]['AnswerText'];
                $testArr[$i]['if_right'] = $iTestData['doAnswer'][0]['IfRight'];
                $testArr[$i]['analytic'] = $iTestData['Analytic'];
                $testArr[$i]['right_answer'] = strip_tags($iTestData['Answer']);
            } elseif ($iTestData['IfChoose'] == 1) {
                //复合题
                $testArr[$i]['number'] = $iTestData['doAnswer'][1]['Number'];
                $rightAnswer = $testModel->xtnum($iTestData['Answer'], 3);
                $analytic = $testModel->xtnum($iTestData['Analytic'], 3);
                $testSub = $iTestData['Test']['sub'];
                foreach ($testSub as $j => $jTestSub) {
                    //因为$iTestData['Test']['sub']是从1开始
                    $answerData = $iTestData['doAnswer'][$j];
                    $subRightAnswer = $rightAnswer[$j]?$rightAnswer[$j]:$rightAnswer;
                    $subAnalytic = $analytic[$j]?$analytic[$j]:$analytic;
                    $testArr[$i]['sub'][$j]['order'] = $answerData['OrderID'];
                    $testArr[$i]['sub'][$j]['sub_title'] = $jTestSub['title']?$jTestSub['title']:'请作答：';
                    $testArr[$i]['sub'][$j]['sub_analytic'] = $subAnalytic;
                    $testArr[$i]['sub'][$j]['if_choose'] = $answerData['IfChoose'];
                    $testArr[$i]['sub'][$j]['if_right'] = $answerData['IfRight'];
                    if ($answerData['IfChoose'] == 3 || $answerData['IfChoose'] == 2) {
                        //复合题中单选多选
                        $testArr[$i]['sub'][$j]['sub_options'] = $jTestSub['options'];
                        $testArr[$i]['sub'][$j]['sub_answer'] = $answerData['AnswerText'];
                        $testArr[$i]['sub'][$j]['sub_right_answer'] = strip_tags($subRightAnswer);
                    } else {
                        //复合题中大题
                        $answerArray = $this->processAppUserAnswer('explode',formatString('IPReturn',$answerData['AnswerText']));
                        $testArr[$i]['sub'][$j]['sub_answer'] = $answerArray[0];
                        $testArr[$i]['sub'][$j]['subAnswerImage'] = $answerArray[1];
                        $testArr[$i]['sub'][$j]['sub_right_answer'] = formatString('IPReturn',$rightAnswer[$j]);
                    }
                }
            } else {
                //其它
                $testArr[$i]['number'] = $iTestData['doAnswer'][0]['Number'];
                $answerArray = $this->processAppUserAnswer('explode',formatString('IPReturn',$iTestData['doAnswer'][0]['AnswerText']));
                $testArr[$i]['answer'] = $answerArray[0];
                $testArr[$i]['answerImage'] = $answerArray[1];
                $testArr[$i]['analytic'] = $iTestData['Analytic'];
                $testArr[$i]['right_answer'] = formatString('IPReturn',$iTestData['Answer']);
                $testArr[$i]['if_right'] = $iTestData['doAnswer'][0]['IfRight'];
            }
        }
        //因为数据时索引中获取的，所以要对数据排序：对test_array进行排序 按照Number字段排序（索引）
        foreach ($testArr as $i => $iTestArr) {
            $numberOrder[$i] = $iTestArr['number'];
        }
        array_multisort($numberOrder,SORT_ASC,$testArr);
        //排序后再生成Type，以防止题型顺序破坏试题的顺序,如果是乱序，题型顺序还按照正常的题型顺序
        $type = [];//此次测试试题中的题型
        $typeAmount = [];//记录题型下的试题数量
        foreach($testArr as $iTestArr){
            array_push($type,$iTestArr['test_type']);
            $typeAmount[$iTestArr['test_type']]++;
        }
        $type = array_unique($type);
        $typeCache = SS('types');
        $result = [];//返回结果数据
        foreach($type as $j=>$m){
            $result['type'][$j]['TypesName'] = $typeCache[$m]['TypesName'];
            $result['type'][$j]['TypesID'] = $m;
            $result['type'][$j]['TypesAmount'] = $typeAmount[$m];//提醒下的试题数量
        }
        //获取测试所耗时间，有时间则填充 获取测试的其它信息
        $result['StartTime'] = date('m月d日H:i',$recordData['StartTime']);
        $result['EndTime'] = date('m月d日H:i',$recordData['EndTime']);
        $result['LoadTime'] = date('m月d日H:i',$recordData['LoadTime']);
        $result['CheckTime'] = $recordData['CheckTime'] ? date('m月d日H:i', $recordData['CheckTime']) : null;
        $result['UserName'] = $this->getUserInfo($recordData['UserName'])['RealName'];
        $result['Message'] = $recordData['Message'];
        $result['SubjectName'] = $recordData['SubjectName'];
        $result['DoTime'] = $recordData['DoTime'];
        $result['Score'] = (int)($recordData['CorrectRate'] * 100);
        //测试试题信息
        $result['test'] = $testArr;
        //答题情况
        $result['exercise_info'] = $userAnswerRecordModel->getExerciseInfo($sendID,$type=2,$isHomework=true);
        $amountRight = 0;
        $amountWrong = 0;
        $amountNot = 0;
        $amountUndo = 0;
        foreach($result['exercise_info'] as $k){
            if($k['IfRight'] == -1){
                $amountUndo += 1;
            }elseif($k['IfRight'] == 0){
                $amountNot += 1;
            }elseif($k['IfRight'] == 1){
                $amountWrong += 1;
            }elseif($k['IfRight'] == 2){
                $amountRight += 1;
            }
        }
        $result['exercise_info_amount'] = array(
            'right'=>$amountRight,
            'wrong'=>$amountWrong,
            'un_judge'=>$amountNot,
            'undo'=>$amountUndo,
            'all'=>$amountRight+$amountWrong+$amountNot+$amountUndo,
        );
        $this->setBack($result);
    }
}