<?php
/**
 * 作业练习（导学案）类
 */
/**
 * @author demo
 * @date 2014-6-24
 */
namespace Aat\Controller;
class MyHomeworkExerciseController extends BaseController
{
    public function _initialize() {
    }

    /**
     * 做练习（要求SendWork表中有记录，非第一次做题）
     * @author demo 5.6.3
     */
    public function index() {
        $sendID = $_REQUEST['id'];
        $checkResult = $this->checkTestRecordID($sendID,$ifDone=false,$recordType='homework');
        if ($checkResult === false) {
            //如果不通过，可能是已经提交的作业，所以跳转到看答案界面
            $this->redirect('MyHomeworkAnswer/index', ['id' => $sendID]);
        }
        $this->assign([
            'pageName'=>'作业训练',
            'send_id' => $sendID
        ]);
        $this->display();
    }

    /**
     * 描述：导学案练习页面
     * @author demo 5.6.8
     */
    public function caseIndex(){
        $sendID = $_REQUEST['id'];
        $checkResult = $this->checkTestRecordID($sendID,$ifDone=false,$recordType='homework');
        if ($checkResult === false) {
            //如果不通过，可能是已经提交的作业，所以跳转到看答案界面
            $this->redirect('MyHomeworkAnswer/caseIndex', ['id' => $sendID]);
        }
        $this->assign([
            'pageName'=>'导学案作业训练',
            'sendID' => $sendID,
            'ifAnswer'=>0
        ]);
        $this->display();
    }

    /**
     * 第一次做作业的情况
     * 获取作业workID和班级classID生成SendID并返回SendID代表作业记录生成成功
     * @author demo 5.6.3
     */
    public function indexCreate() {
        $requestID = $_REQUEST['id'];
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Exercise/homeworkCreateSendID', $requestID,$userID);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

    /**
     * 获取测试类作业的试题数据
     * @author demo 15.5.18
     */
    public function getTest() {
        $this->checkRequest();
        $sendID = $_REQUEST['send_id'];
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Exercise/homeworkGetTest', $sendID,$userID);
        if($IData[0]==0){
            return $this->setError($IData[1], 1);
        }
        //遍历此次测试的所有试题和之前作答信息
        $testArray = [];//用于ajax返回的试题数据数组
        $data = $IData[1];
        foreach ($data['test'] as $i => $k) {
            $testArray[$i]['test_id'] = $k['TestID'];
            $testArray[$i]['test_title'] = $k['Test']['title'];
            $testArray[$i]['test_type'] = $k['TypesID'];
            $testArray[$i]['if_choose'] = $k['IfChoose'];
            $testArray[$i]['doc_id'] = $k['DocID'];
            $testArray[$i]['testNum'] = $k['TestNum'];
            //根据题型判断
            if ($k['IfChoose'] == 3) {//单选
                $testArray[$i]['answer_id'] = $sendID.'-'.$k['TestID'].'-'.$k['doAnswer'][0]['Number'].'-'.
                    $k['doAnswer'][0]['OrderID'].'-'.$k['IfChoose'];
                $testArray[$i]['number'] = $k['doAnswer'][0]['Number'];
                $testArray[$i]['test_options'] = $k['Test']['options'];
                $testArray[$i]['answer'] = $k['doAnswer'][0]['AnswerText'];
                $testArray[$i]['right_answer'] = strip_tags($k['Answer']);
            }elseif($k['IfChoose'] == 2){//多选
                $testArray[$i]['answer_id'] = $sendID.'-'.$k['TestID'].'-'.$k['doAnswer'][0]['Number'].'-'.
                    $k['doAnswer'][0]['OrderID'].'-'.$k['IfChoose'];
                $testArray[$i]['number'] = $k['doAnswer'][0]['Number'];
                $testArray[$i]['test_options'] = $k['Test']['options'];
                $testArray[$i]['answer'] = explode(',',$k['doAnswer'][0]['AnswerText']);
                $testArray[$i]['right_answer'] = strip_tags($k['Answer']);
            } elseif ($k['IfChoose'] == 1) {//复合题
                $testArray[$i]['number'] = $k['doAnswer'][1]['Number'];
                $rightAnswerOne = $this->getApiTest('Test/xtnum', $iTestData['Answer'], 3);;//分隔正确答案
                foreach ($k['Test']['sub'] as $j => $m) {
                    //$k['Test']['sub']是从1开始
                    $answerArray = $k['doAnswer'][$j];
                    $testArray[$i]['sub'][$j]['answer_id'] = $sendID.'-'.$k['TestID'].'-'.$answerArray['Number'].'-'.
                        $answerArray['OrderID'].'-'.$answerArray['IfChoose'];
                    $testArray[$i]['sub'][$j]['order'] = $answerArray['OrderID'];
                    $testArray[$i]['sub'][$j]['sub_title'] = $m['title']?$m['title']:'请作答：';
                    $testArray[$i]['sub'][$j]['if_choose'] = $answerArray['IfChoose'];
                    if ($answerArray['IfChoose'] == 3) {//复合题中单选
                        $testArray[$i]['sub'][$j]['sub_options'] = $m['options'];
                        $testArray[$i]['sub'][$j]['sub_answer'] = $answerArray['AnswerText'];
                        $testArray[$i]['sub'][$j]['sub_right_answer'] = strip_tags($rightAnswerOne[$j]);
                    }elseif($answerArray['IfChoose'] == 2){//复合体中多选
                        $testArray[$i]['sub'][$j]['sub_options'] = $m['options'];
                        $testArray[$i]['sub'][$j]['sub_answer'] = explode(',',$answerArray['AnswerText']);
                        $testArray[$i]['sub'][$j]['sub_right_answer'] = strip_tags($rightAnswerOne[$j]);
                    } else {//复合题中大题
                        $testArray[$i]['sub'][$j]['sub_answer'] = formatString('IPReturn',$answerArray['AnswerText']);
                        $testArray[$i]['sub'][$j]['sub_right_answer'] = $rightAnswerOne[$j];
                    }
                }
            } else {
                //其它
                $testArray[$i]['number'] = $k['doAnswer'][0]['Number'];
                $testArray[$i]['answer_id'] = $sendID.'-'.$k['TestID'].'-'.$k['doAnswer'][0]['Number'].'-'.
                    $k['doAnswer'][0]['OrderID'].'-'.$k['IfChoose'];
                $testArray[$i]['answer'] = formatString('IPReturn',$k['doAnswer'][0]['AnswerText']);
                $testArray[$i]['right_answer'] = $k['Answer'];
            }
        }
        //因为数据时索引中获取的，所以要对数据排序：对testArray进行排序 按照Number字段排序（索引）
        foreach ($testArray as $i => $k) {
            $numberOrder[$i] = (int)$k['number'];
        }
        //array_multisort($numberOrder, SORT_ASC, $testArray);
        //排序后再生成Type，以防止题型顺序破坏试题的顺序,如果是乱序，题型顺序还按照正常的题型顺序
        $type = [];//此次测试试题中的题型
        $typeAmount = [];//记录题型下的试题数量
        foreach($testArray as $k){
            array_push($type,$k['test_type']);
            $typeAmount[$k['test_type']]++;
        }
        //如果是乱序做题，则需要重排数据
        if($data['record']['WorkOrder']){
            shuffle($testArray);
        }
        //获取测试所耗时间，有时间则填充 获取测试的其它信息
        $result = array(
            'StartTime'=>date('m月d日H:i',$data['record']['StartTime']),
            'EndTime'=>date('m月d日H:i',$data['record']['EndTime']),
            'LoadTime'=>date('m月d日H:i',$data['record']['LoadTime']),
            'UserName'=>$this->getUserInfo($data['record']['UserName'])['RealName'],
            'Message'=>$data['record']['Message'],
            'SubjectName'=>$data['record']['SubjectName'],
            'DoTime'=>$data['record']['DoTime'],
        );
        //测试使用：获取试题的正确答案------------------------------------------------------------------------------------
        if(C('AAT_LOAD_RIGHT_ANSWER')){
            $rightAnswer = [];
            foreach($testArray as $iTestArray){
                //遍历出试题答案
                $rightAnswer[] = ['number'=>$iTestArray['number'],'right_answer'=>$iTestArray['right_answer']];
            }
            $result['right_answer'] = $rightAnswer;
        }
        //测试题型
        $type = array_unique($type);
//        $typeCache = SS('types');
        $typeCache = $this->getApiCommon('Types/types');
        foreach ($type as $j => $m) {
            $result['type'][$j]['TypesName'] = $typeCache[$m]['TypesName']?$typeCache[$m]['TypesName']:'其它';
            $result['type'][$j]['TypesID'] = $m;
            $result['type'][$j]['TypesAmount'] = $typeAmount[$m];//题型下的试题数量
        }
        $result['test'] = $testArray;
        return $this->setBack($result);
    }

    /**
     * 描述：获取导学案作业的内容数据
     * @author demo
     */
    public function getCaseContent() {
        $this->checkRequest();
        $sendID = $_REQUEST['sendID'];
        $ifAnswer = $_REQUEST['ifAnswer'];
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Exercise/homeworkCaseGetTest', $sendID,$ifAnswer,$userID,$isApp=false);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

    /**
     * 判断单选和多选的题目正确性，插入AnswerRecord表答题记录，更新SendWork表信息
     * @author demo 5.5.27
     */
    public function doSubmit() {
        $this->checkRequest();
        $sendID = $_REQUEST['send_id'];
        $doTime = $_REQUEST['do_time'];
        $userID = $this->getUserID();
        $realName = $this->getRealName();
        $IData = $this->getApiAat('Exercise/homeworkSubmit', $sendID,$doTime,$userID,$realName);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

    /**
     * 处理每次点击试题答案时提交的数据
     * @author demo 5.18
     */
    public function doAnswer() {
        $this->checkRequest();
        $answerID = $_REQUEST['answer_id'];
        //处理answer字段，兼容数组和非数组，非数组多选题，逗号分割
        $oldAnswer = $_REQUEST['answer'];
        $answerText = is_array($oldAnswer)?trim(implode(',', $oldAnswer)):trim($oldAnswer);
        $IData = $this->getApiAat('Exercise/homeworkDoAnswer', $answerID,$answerText);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

    /**
     * 处理用户浏览器关闭或者刷新页面时提交的做题用时数据
     * @author demo
     */
    public function doClose() {
        $this->checkRequest();
        $sendID = $_REQUEST['send_id'];
        $doTime = $_REQUEST['do_time'];//答题时间
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Exercise/homeworkClose', $sendID,$doTime,$userID);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }
}