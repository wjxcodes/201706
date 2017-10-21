<?php
/**
 * @author demo
 * @date 2015年12月29日
 */

namespace Aat\Api;

class AnswerApi extends BaseApi
{
    /**
     * 描述：练习答案
     * @param $testRecordID
     * @param $username
     * @return array
     * @author demo
     */
    public function exerciseAnswer($testRecordID,$username){
//        $testModel = $this->getModel('Test');
//        $testRealModel = $this->getModel('TestReal');
        $userAnswerRecordModel = $this->getModel('UserAnswerRecord');
        if(!$testRecordID){
            return [0,'数据参数错误，请重试！'];
        }
        if ($this->checkExerciseRID($testRecordID,$ifDone=true,$username) === false) {
            return [0,'没有权限查看此试卷,或者试卷不存在！'];
        }
//        $data = $testRealModel->getTestAnswerByIndex($testRecordID);
        $data = $this->getApiTest('Test/getTestAnswerByIndex', $testRecordID);

        //此次测试试题中的题型
        $typeHaveList=array();//保留题型id集合
        //遍历此次测试的所有试题和之前作答信息‘
        $noDo=C('WLN_TYPE_FILTER');
        $testArray = [];
        $types = SS('typesSubject');

        foreach ($data['test'] as $i => $k) {
            if(count($types) > 1){
                $types = $types[$k['SubjectID']];
                $temp = array();
                foreach($types as $value){
                    $temp[$value['TypesID']] = $value['IfPoint'];
                }
                $types = array();
                $types[] = $temp;
                unset($temp);
            }
            $testArray[$i]['point'] = $types[0][$k['TypesID']];
            $testArray[$i]['score'] = $k['Score'];
            $testArray[$i]['testNum'] = $k['TestNum'];
            $testArray[$i]['test_id'] = $k['TestID'];
            $testArray[$i]['test_title'] = $k['Test']['title'];
            $testArray[$i]['test_type'] = $k['TypesID'];
            $typeHaveList[$k['SubjectID']][]=$k['TypesID'];
            $testArray[$i]['if_choose'] = $k['IfChoose'];
            $testArray[$i]['doc_name'] = $k['DocName'];
            $testArray[$i]['doc_id'] = $k['DocID'];
            if(empty($k['KlList'])) $k['KlList']='';
            $testArray[$i]['kl_list'] = $k['KlList'];
            $testArray[$i]['ifCanDo'] = 1;
            //判断是否显示用户作答信息
            if(strpos($noDo[$k['SubjectID']],$k['TypesID'])!==false && $data['record']['AatTestStyle']!=1){
                $testArray[$i]['ifCanDo'] = 0;
            }
            //根据题型判断
            if ($k['IfChoose'] == 3 || $k['IfChoose'] == 2) {
                //单选 多选
                $testArray[$i]['number'] = $k['doAnswer'][0]['Number'];
                $testArray[$i]['test_options'] = $k['Test']['options'];
                $testArray[$i]['answer'] = $k['doAnswer'][0]['AnswerText'];
                $testArray[$i]['if_right'] = $k['doAnswer'][0]['IfRight'];
                $testArray[$i]['answer_score'] = (float)$k['doAnswer'][0]['Score'];
                $testArray[$i]['answer_id'] = (int)$k['doAnswer'][0]['AnswerID'];
                $testArray[$i]['analytic'] = $k['Analytic'];
                $testArray[$i]['right_answer'] = strip_tags($k['Answer']);
            } elseif ($k['IfChoose'] == 1) {
                //复合题
                $testArray[$i]['number'] = $k['doAnswer'][1]['Number'];
//                $rightAnswer = $testModel->xtnum($k['Answer'], 3);
                $rightAnswer = $this->getApiTest('Test/xtnum', $k['Answer'], 3);
//                $analytic = $testModel->xtnum($k['Analytic'], 3);
                $analytic = $this->getApiTest('Test/xtnum', $k['Analytic'], 3);
                $scoreArray=explode(',',$k['Score']);
                foreach ($k['Test']['sub'] as $j => $m) {
                    //因为$k['Test']['sub']是从1开始
                    $answerRecord = $k['doAnswer'][$j];
                    $subRightAnswer = $rightAnswer[$j]?$rightAnswer[$j]:$k['Answer'];
                    $subAnalytic = $analytic[$j]?$analytic[$j]:$k['Analytic'];
                    $testArray[$i]['sub'][$j]['order'] = $answerRecord['OrderID'];
                    $testArray[$i]['sub'][$j]['score'] = is_numeric($scoreArray[$j-1]) ? $scoreArray[$j-1] : '0';
                    $testArray[$i]['sub'][$j]['sub_title'] = $m['title']?$m['title']:'请作答：';
                    $testArray[$i]['sub'][$j]['sub_analytic'] = $subAnalytic;
                    $testArray[$i]['sub'][$j]['if_choose'] = $answerRecord['IfChoose'];
                    $testArray[$i]['sub'][$j]['if_right'] = $answerRecord['IfRight'];
                    $testArray[$i]['sub'][$j]['answer_score'] = (float)$answerRecord['Score'];
                    $testArray[$i]['sub'][$j]['answer_id'] = (int)$answerRecord['AnswerID'];
                    if ($answerRecord['IfChoose'] == 3 || $answerRecord['IfChoose'] == 2) {
                        //复合题中单选多选
                        $testArray[$i]['sub'][$j]['sub_options'] = $m['options'];
                        $testArray[$i]['sub'][$j]['sub_answer'] = $answerRecord['AnswerText'];
                        $testArray[$i]['sub'][$j]['sub_right_answer'] = strip_tags($subRightAnswer);
                    } else {
                        //复合题中大题
                        $testArray[$i]['sub'][$j]['sub_answer'] = $answerRecord['AnswerText'];
                        $testArray[$i]['sub'][$j]['sub_right_answer'] = $subRightAnswer;
                    }
                }
            } else {
                //其它
                $testArray[$i]['number'] = $k['doAnswer'][0]['Number'];
                $testArray[$i]['answer'] = formatString('IPReturn',$k['doAnswer'][0]['AnswerText']);
                $testArray[$i]['analytic'] = $k['Analytic'];
                $testArray[$i]['right_answer'] = $k['Answer'];
                $testArray[$i]['if_right'] = $k['doAnswer'][0]['IfRight'];
                $testArray[$i]['answer_score'] = (float)$k['doAnswer'][0]['Score'];
                $testArray[$i]['answer_id'] = (int)$k['doAnswer'][0]['AnswerID'];
            }
        }
        //因为数据时索引中获取的，所以要对数据排序：对test_array进行排序 按照Number字段排序（索引）
//        foreach ($testArray as $i => $k) {
//            $numberArr[$i] = $i;
//        }
//        array_multisort($numberArr,SORT_ASC,$testArray);

        //获取测试所耗时间，有时间则填充
        $result['time'] = $data['record']['RealTime']<60?1:(int)($data['record']['RealTime']/60);
        //测试题型
        $typeAmount = array();//记录题型下的试题数量

        //获取题型顺序 如果试题以文档形式则优先按照文档结构展示
        $userAnswerRecordAttrModel = $this->getModel('UserTestRecordAttr');
        $attrBuffer=$userAnswerRecordAttrModel->findData('DocID','TestRecordID='.$testRecordID);
        $type=array();
        $isWarp = ($attrBuffer['DocID']>0);
        if($isWarp){ //整卷数据
//            $testBuffer=$this->getModel('TestAttrReal')->selectData('NumbID,TypesID','DocID='.$attrBuffer['DocID'],'NumbID ASC');
            $testBuffer = $this->getApiTest('Test/selectData', 'NumbID,TypesID','DocID='.$attrBuffer['DocID'],'NumbID ASC', '', 'TestAttrReal');
            foreach($testBuffer as $iTestBuffer){
                $type[]=$iTestBuffer['TypesID'];
            }
        }else{
//            $type = SS('typesSubject')[$data['record']['SubjectID']];
            $type = $this->getApiCommon('Types/typesSubject');
            $typeTmp=array();
            foreach($typeHaveList as $i=>$iTypeHaveList){
                foreach($type[$i] as $j=>$jType){
                    if(in_array($jType['TypesID'],$iTypeHaveList)){
                        $typeTmp[]=$jType['TypesID'];
                    }
                }
            }
            $type=$typeTmp;
        }

        foreach($typeHaveList as $iType){
            foreach($iType as $jType){
                $typeAmount[$jType]++;
            }
        }
        $type = array_unique($type);
        if(!$isWarp)
            sort($type);

//        $testArray = $this->getModel('UserTestRecord')->sequenceByType($testArray, $type);exit(print_r($testArray));

        $newTestArray=array();
        foreach($testArray as $i=>$iTestArray){
            $newTestArray[]=$iTestArray;
        }
        $testArray=$newTestArray;

        $typeCache = $this->getApiCommon('Types/types');
        foreach($type as $j=>$m){
            $result['type'][$j] = $typeCache[$m];
            $result['type'][$j]['TypesID'] = $m;
            $result['type'][$j]['TypesAmount'] = $typeAmount[$m];//提醒下的试题数量
        }
        //测试类型
        $result['style'] = $data['record']['Style'];
        $styleConfig = C('WLN_TEST_STYLE_NAME');
        $result['style'] = $styleConfig[$result['style']]?$styleConfig[$result['style']]:'本次测试';
        $result['test'] = $testArray;
        //答题情况
        $result['exercise_info'] = $userAnswerRecordModel->getExerciseInfo($testRecordID,$type=2,$isHomework=false);
        $amountRight = 0;
        $amountWrong = 0;
        $amountUnJudge = 0;
        $amountUndo = 0;
        $scores = array();//查出已打分的主键，分值
        foreach($result['exercise_info'] as $k){
            if($k['IfRight'] == -1){
                $amountUndo += 1;
            }elseif($k['IfRight'] == 0){
                $amountUnJudge += 1;
            }elseif($k['IfRight'] == 1){
                $amountWrong += 1;
            }elseif($k['IfRight'] == 2){
                $amountRight += 1;
            }
            $scores[$k['AnswerID']] = $k['Score'];
        }
        $result['exercise_info_amount'] = array(
            'right'=>$amountRight,
            'wrong'=>$amountWrong,
            'un_judge'=>$amountUnJudge,
            'undo'=>$amountUndo,
            'all'=>$amountRight+$amountWrong+$amountUnJudge+$amountUndo,
            'aatTestStyle' => $data['record']['AatTestStyle'],
            'evaluateDescription' => $data['record']['EvaluateDescription'],
            'answerTitle' => $data['record']['AnswerTitle'],
            'jumpUrl' => $data['record']['JumpUrl']
        );
        if($data['record']['AnswerTitle']){
            $result['style'] = $data['record']['AnswerTitle'];
        }
        return [1,$result];
    }

    /**
     * 描述：作业解析
     * @param $sendID
     * @param $userID
     * @return array
     * @author demo
     */
    public function homeworkAnswer($sendID,$userID){
//        $testRealModel = $this->getModel('TestReal');
        if(!$sendID){
            return [0,'缺少ID参数，请重试！'];
        }
        if ($this->checkHomeworkRID($sendID,$ifDone=true,$userID) === false) {
            return [0,'作业还没有提交或者没有权限查看此作业！'];
        }
//        $data = $testRealModel->getHomeworkByIndex($sendID);
        $data = $this->getApiTest('Test/getHomeworkByIndex', $sendID);
        return [1,$data];
    }

    /**
     * 对试题进行自定义打分
     * @param int $recordId 作答记录id
     * @param int $testId 试题id
     * @param string $userName 用户名
     * @return array
     * @author demo 16-4-28
     */
    public function evaluateTest($answerId, $recordId, $score, $userName){
        $data = $this->getModel('UserTestRecordAttr')->findData('UserName', "TestRecordID={$recordId}");
        if($data['UserName'] != $userName){
            return array(0, '不能对其他用户的试题进行评分！');
        }
        $model = $this->getModel('UserAnswerRecord');
        $data = $model->updateData(array(
            'Score' => $score
        ), 'AnswerID='.(int)$answerId);

        if($data === false){
            return array(0, '打分失败！');
        }

        //对试题总分进行修正
        $buffer=$model->selectData('AnswerID,Score','TestRecordID='.$recordId);
        $totalScore=0;
        foreach($buffer as $iBuffer){
            if($iBuffer['Score']>0) $totalScore+=$iBuffer['Score'];
        }
        //更新总分
        $data=$this->getModel('UserTestRecord')->UpdateData(array('Score'=>$totalScore),'TestID='.$recordId);

        if($data === false){
            return array(0, '打分失败2！');
        }
        return array(1, 'success');
    }

    /**
     * //针对打分试卷，判断是否完成打分
     * @param int $record 用户答题试卷id
     * @param boolean
     * @author demo 16-4-28
     */
    public function isCompleteEvaluation($record){
        $list = (array)$this->getModel('UserAnswerRecord')->selectData('Score', "TestRecordID={$record}");
        foreach($list as $value){
            $score = (int)$value['Score'];
            if($score < 0){
                return false;
            }
        }
        return true;
    }

}