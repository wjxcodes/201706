<?php
/**
 * @author demo
 * @date 2015年12月28日
 */

namespace Aat\Api;

class ScoreApi extends BaseApi
{
    /**
     * $info array 字段[ExerciseScore ExerciseLine ForecastScore ForecastLine AllAmount RightAmount
     * WrongAmount UndoAmount]
     * @param $userName
     * @param $subjectID
     * @return array
     * @author demo
     */
    public function scoreLineMobile($userName,$subjectID) {
        $userForecastModel = $this->getModel('UserForecast');
        $info = $userForecastModel->getScoreInfo($userName, $subjectID, $isMobile=true);
        $result = [
            'time'=>date('Y-m-d',time()),//当前时间
            'userName'=>$userName,
//            'totalScore'=>SS('subject')[$subjectID]['TotalScore'],
            'totalScore'=>$this->getApiCommon('Subject/subject')[$subjectID]['TotalScore'],
            'allAmount'=>$info['AllAmount'],
            'needTimes'=>$userForecastModel->forecastNeedTimes($userName,$subjectID),
            'exerciseScore'=>$info['ExerciseScore'],
            'exerciseLine'=>$info['ExerciseLine'],
            'forecastScore'=>$info['ForecastScore'],
            'forecastLine'=>$info['ForecastLine']
        ];
        return $result;
    }

    /**
     * 描述：
     * 返回【时间 该学科精准预测分总分 还有几次可以生成精准预测分
     * 快速预测分 精准预测分 答题数量 排名信息 精准趋势 快速趋势 】信息
     * @param $userName
     * @param $subjectID
     * @return array
     * @author demo
     */
    public function scoreInfoWeb($userName,$subjectID){
        $userForecastModel = $this->getModel('UserForecast');
        $result['time'] = date('Y年m月d日 H:i:s',time());//当前时间
//        $result['totalScore'] = SS('subject')[$subjectID]['TotalScore'];//
        $result['totalScore'] = $this->getApiCommon('Subject/subject')[$subjectID]['TotalScore'];
        $result['needTimes'] = $userForecastModel->forecastNeedTimes($userName,$subjectID);
        $info = $userForecastModel->getScoreInfo($userName, $subjectID);
        $result = array_merge($result,$info);
        return [1,$result];
    }

    /**
     * 描述：练习情况
     * @param $recordID
     * @param $username
     * @param $isApp
     * @return array
     * @author demo
     */
    public function exerciseInfo($recordID,$username,$isApp){
        $userTestRecordModel = $this->getModel('UserTestRecord');
        if(!$recordID){
            return [0,'数据参数错误，请重试！'];
        }
        if ($this->checkExerciseRID($recordID,$ifDone=true,$username)===false) {
            return [0,'数据参数错误，请重试！'];
        }

        //重新汇总分数//判断是否打分
        $userTestRecordAttr=$this->getModel('UserTestRecordAttr');

        if($userTestRecordAttr->isEvalTest($recordID) && $this->getApiAat('Answer/isCompleteEvaluation', $recordID)){
            $sum=$this->getModel('UserAnswerRecord')->sumData('Score','TestRecordID='.$recordID.' AND Score>0');
            $userTestRecordModel->updateData(array('Score'=>$sum),'TestID='.$recordID);
        }

        //本次测试信息
        $thisScoreArr = $userTestRecordModel->unionSelect('userTestRecordSelectById',$recordID);
        $subjectID = $thisScoreArr['SubjectID'];
        //全站平均分数
        $avgScore = (int)$userTestRecordModel->avgData(
            'Score',
            'Score>0 and RealTime>0 and SubjectID='.$subjectID
        );

        //获取整卷练习总分值
        $attrBuffer=$userTestRecordAttr->findData('DocID','TestRecordID='.$recordID);
        $result['total_score'] = 100;
        if($attrBuffer['DocID']>0){
//            $docBuffer=$this->getModel('Doc')->findData('TotalScore','DocID='.$attrBuffer['DocID']);
            $docBuffer = $this->getApiDoc('Doc/findData', 'TotalScore','DocID='.$attrBuffer['DocID'], '', 'Doc');
            if(!empty($docBuffer['TotalScore']) && is_numeric($docBuffer['TotalScore'])){
                $result['total_score']=$docBuffer['TotalScore'];
            }
        }else{
            $recordBuffer=$userTestRecordModel->selectData('Content','TestID='.$recordID);
            if($recordBuffer['Content']){
                $testAttrbuffer=$this->getModel('TestAttrReal')->selectData('TestID,Score','TestID IN ('.$recordBuffer['Content'].')');
                $total=0;
                foreach($testAttrbuffer as $iBuffer){
                    if(!$iBuffer['Score']){
                        $total=0;
                        break;
                    }
                    $tmp=explode(',',$iBuffer['Score']);
                    $tmp=array_filter($tmp);
                    $total+=array_sum($tmp);
                }

                if($total>0) $result['total_score']=$total;
            }
        }

        //总过多少题 已做题目 正确题 错误题 没做题
        $exerciseInfo = $this->getModel('UserAnswerRecord')->getExerciseInfo($recordID,$type=1,$isHomework=false);
        $result['this_score'] = floatval($thisScoreArr['Score']);
        $realTime = (int)($thisScoreArr['RealTime']/60);
        $result['real_time'] = $realTime?$realTime:1;
        $timeFormat = $isApp?'n月j日 H:i':'Y-m-d H:i';
        $result['end_time'] = date($timeFormat,$thisScoreArr['LoadTime']+$thisScoreArr['RealTime']);
        $result['style'] = $thisScoreArr['Style'];
        $styleConfig = C('WLN_TEST_STYLE_NAME');
        $result['style'] = $styleConfig[$result['style']];

        //如果是专题测试则显示专题名称
        $thisTopic=$this->getModel('UserTestRecordAttr')->getEvaluateDescription($recordID);

        if($thisTopic){
            if($thisTopic['TopicName']){
                $result['style'] = $thisTopic['TopicName'];
            }
            $result['jumpUrl'] = $thisTopic['JumpUrl'];
        }

        $result['avg_score'] = $avgScore;
        //排名
        $result['rank'] =$this->getModel('UserForecast')->getRankingPercent($username,$subjectID,$recordID);
        //@@@68698189@qq.com
//        if($username == '68698189@qq.com'){
//            $result['avg_score'] = 86;
//        }
        $result = array_merge($result, $exerciseInfo);
        if ($result&&is_array($result)) {
            return [1,$result];
        } else {
            return [0,'查询数据错误，请重试！'];
        }
    }

    /**
     * 描述：练习分数记录，用于生成折线图
     * @param $username
     * @param $subjectID
     * @param $isApp
     * @return array
     * @author demo
     */
    public function scoreList($username,$subjectID,$isApp){
        //近10次测试分数、测试时间
        $exerciseList = $this->getModel('UserTestRecord')->unionSelect('userTestRecordListData',$username,$subjectID);
        $timeFormat = $isApp?'n-j':'m月d日 H:i';
        foreach ($exerciseList as $i => $iExerciseList) {
            $exerciseList[$i]['LoadTime'] = date($timeFormat, $iExerciseList['LoadTime']);
        }
        if ($exerciseList&&is_array($exerciseList)) {
            return [1,$exerciseList];
        } else {
            return [0,'获取数据失败，请重试！'];
        }
    }
}