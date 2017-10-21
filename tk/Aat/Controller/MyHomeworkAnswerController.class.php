<?php
/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-6-24
 * Time: 下午3:53
 */
namespace Aat\Controller;
class MyHomeworkAnswerController extends BaseController
{
    public function _initialize() {
    }

    /**
     * 描述：作业解析页面
     * @author demo
     */
    public function index() {
        $id = $_GET['id'];
        if ($this->checkTestRecordID($id,$ifDone=true,$recordType='homework')===false) {
            $this->setMsg('您所操作的作业答题情况不存在！');
            $this->redirect('Default/index');
        }
        $this->assign([
            'pageName'=>'作业答案解析',
            'send_id'=>$id,
        ]);
        $this->display();
    }

    /**
     * 描述：导学案解析界面
     * @author demo 5.6.26
     */
    public function caseIndex(){
        $id = $_GET['id'];
        if ($this->checkTestRecordID($id,$ifDone = true,$recordType='homework') === false) {
            $this->setMsg('您所操作的作业答题情况不存在！');
            $this->redirect('Default/index');
        }
        $this->assign([
            'pageName' => '作业答案解析',
            'sendID' => $id,
            'ifAnswer' => 1
        ]);
        $this->display('MyHomeworkExercise:caseIndex');
    }

    /**
     * 返回试题信息和作答信息
     * @author demo 5.5.25
     */
    public function returnTestList() {
        $this->checkRequest();
        $sendID = $_REQUEST['id'];
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Answer/homeworkAnswer', $sendID,$userID);
        if($IData[0]==0){
            $this->setError($IData[1], 1);
        }
//        $testModel = $this->getModel('Test');
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
            $testArr[$i]['testNum'] = $iTestData['TestNum'];
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
                // $rightAnswer = $testModel->xtnum($iTestData['Answer'], 3);
                // $analytic = $testModel->xtnum($iTestData['Analytic'], 3);
                $rightAnswer = $this->getApiTest('Test/xtnum', $iTestData['Answer'], 3);
                $analytic =  $this->getApiTest('Test/xtnum', $iTestData['Answer'], 3);
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
                        $testArr[$i]['sub'][$j]['sub_answer'] = formatString('IPReturn',$answerData['AnswerText']);
                        $testArr[$i]['sub'][$j]['sub_right_answer'] = formatString('IPReturn',$rightAnswer[$j]);
                    }
                }
            } else {
                //其它
                $testArr[$i]['number'] = $iTestData['doAnswer'][0]['Number'];
                $testArr[$i]['answer'] = formatString('IPReturn',$iTestData['doAnswer'][0]['AnswerText']);
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
//        $typeCache = SS('types');
        $typeCache = $this->getApiCommon('Types/types');
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