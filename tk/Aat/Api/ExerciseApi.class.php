<?php
/**
 * @author demo
 * @date 2015年12月29日
 */

namespace Aat\Api;

class ExerciseApi extends BaseApi
{
    /**
     * 描述：获取试题
     * @param $testRecordID
     * @param $username
     * @return array
     * @author demo
     */
    public function getTest($testRecordID,$username,$ifShowID=0){
        if(!$testRecordID){
            return [0,'数据参数错误，请重试！'];
        }
        if ($this->checkExerciseRID($testRecordID,$ifDone = false,$username) === false) {
            return [0,'没有权限查看此试卷或者试卷不存在！'];
        }
        //$data = $this->getModel('TestReal')->getTestAnswerByIndex($testRecordID);
        $data = $this->getApiTest('Test/getTestAnswerByIndex', $testRecordID,$ifShowID);
        return [1,$data];
    }

    /**
     * 描述：提交试卷
     * @param $testRecordID
     * @param $doTime
     * @param $username
     * @return array
     * @author demo
     */
    public function submit($testRecordID,$doTime,$username){
        $userAnswerRecordModel = $this->getModel('UserAnswerRecord');
        $userTestRecordModel = $this->getModel('UserTestRecord');
        //验证是否为打分试卷，如果是则将试题分值写入答题表
        if(($isEval = $this->getModel('UserTestRecordAttr')->isEvalTest($testRecordID))){
            // if(!$userTestRecordModel->isCompleteAllTheAnswers($testRecordID)){
            //     return array(0, '您有未答完的试题！打分的专题试卷，需答完所有试题。');
            // }
        }
        //防止重复插入数据
        if ($this->checkExerciseRID($testRecordID,$ifDone = false,$username) === false) {
            return [0,'试卷已经提交或者没有权限操作！'];
        }
        //更新题目正确情况IfRight
        $userAnswerRecordModel->updateIfRight($testRecordID, false, $isEval);
        //本次测试相关信息
        $recordInfo = $userAnswerRecordModel->getExerciseInfo($testRecordID,$type=1,$isHomework=false);
        //计算本次测试分数（正确率）插入本次测试时间和分数到数据库
        $rightAmount = $recordInfo['right_amount'];
        $wrongAmount = $recordInfo['wrong_amount'];
        $undoAmount = $recordInfo['undo_amount'];
        $notAmount = $recordInfo['un_judge_amount'];
        $scoreAmount = $rightAmount + $wrongAmount;//分数总计只计算正确和错误的，其它不考虑
        $recordData['Score'] = round($rightAmount / ($scoreAmount?$scoreAmount:1), 2) * 100;
        $recordData['RealTime'] = $doTime;
        $result = $userTestRecordModel->updateData(
            $recordData,
            ['TestID'=>$testRecordID]
        );
        //更新预测分能力值相关
        $testRecord = $userTestRecordModel->unionSelect('userTestRecordSelectById',$testRecordID);
        $subjectID = $testRecord['SubjectID'];
        $this->getApiAat('Ability/createAbility',
            $subjectID,
            $username,
            $testRecordID,
            $recordData['Score'],
            $rightAmount,$wrongAmount,$undoAmount,$notAmount);
//        (new AbilityLogic())->createAbility(
//            $subjectID,
//            $username,
//            $testRecordID,
//            $recordData['Score'],
//            $rightAmount,$wrongAmount,$undoAmount,$notAmount);
        if ($result) {//本地测试发现$result为result字符串，后期需要测试save方法
            return [1,$testRecordID];
        } else {
            return [0,'数据提交错误请重新操作'];
        }
    }

    /**
     * 描述：作答
     * @param $answerID
     * @param $answerText
     * @param null $doTime
     * @return array
     * @author demo
     */
    public function doAnswer($answerID,$answerText,$doTime=null){
        if (!$answerID) {
            return [0,'数据参数错误，请重试！'];
        }
        if($answerText === ''){
            return [0,'答案不能为空'];
        }
        $answerIDArray = explode('-',$answerID);
        $answer['TestRecordID'] = $answerIDArray[0];
        $answer['TestID'] = $answerIDArray[1];
        $answer['Number'] = $answerIDArray[2];
        $answer['OrderID'] = $answerIDArray[3];
        $answer['IfChoose'] = $answerIDArray[4];
        $answer['AnswerText'] = formatString('IPReplace',$answerText);
        $answer['LoadTime'] = time();
        $result = $this->getModel('UserAnswerRecord')->updateAnswer($answer,$isHomework = false);
        if($doTime) {
            $recordData['RealTime'] = $doTime;
            $this->getModel('UserTestRecord')->updateData(
                $recordData,
                ['TestID' => $answerIDArray[0]]
            );
        }
        if ($result == false) {
            return [0,'答案提交错误,请重试！'];
        }else{
            return [1,'提交成功！'];
        }
    }

    /**
     * 描述：关闭网页时触发
     * @param $testRecordID
     * @param $doTime
     * @param $username
     * @return array
     * @author demo
     */
    public function close($testRecordID,$doTime,$username){
        if (!$testRecordID || !$doTime) {
            return [0, '数据参数错误，请重试！'];
        }
        //判断题目正确性（没有作答情况）之后插入数据库
        //一下语句防止重复插入数据
        if ($this->checkExerciseRID($testRecordID, $ifDone = false, $username) !== false) {
            //检测通过
            $record['RealTime'] = $doTime;
            $updateResult = $this->getModel('UserTestRecord')->updateData(
                $record,
                ['TestID' => $testRecordID]
            );
            if ($updateResult == 1) {
                return [1, '自动保存成功！'];
            } else {
                return [0, '自动保存数据失败！'];
            }
        } else {
            return [0, '自动保存数据失败！'];
        }
    }

    /**
     * 描述：单题错误反馈
     * @param $testID
     * @param $typeID
     * @param $content
     * @param $username
     * @param $isApp
     * @return array
     * @author demo
     */
    public function testFeedback($testID,$typeID,$content,$username,$isApp){
        if(!$testID){
            return [0,'请选择错误试题！'];
        }
        if(!isset($typeID)){
            return [0,'请选择错误类型！'];
        }
        $length = strlen(trim($content));
        if($length<1||$length>300){
            return [0,'错误描述必须10-100字！'];
        }
        $data['Ctime']=time();
//        $data['SubjectID']=$this->getModel('TestReal')->unionSelect('testRealByID',$testID)[0]['SubjectID'];
        $data['SubjectID'] = $this->getApiTest('Test/unionSelect', 'testRealByID',$testID)[0]['SubjectID'];
        $data['UserName']=$username;
        $data['TestID']=$testID;
        $data['Content']=formatString('changeStr2Html',$content);
        $data['From']='1';//是提分前台，根据该状态是1；
        $data['TypeID']=$typeID;
        $CorrectLogResult=$this->getModel('CorrectLog')->insertData($data);
//        $CorrectLogResult = $this->getApiCommon('Common/insertData', $data, 'CorrectLog');
        if($CorrectLogResult!==false){//判断成功
            if($isApp){
                $message = '提交成功，感谢您的反馈！';
            }else{
                $message = '提交成功，感谢您的反馈！该页面会在3秒后自动关闭!';
            }
            return [1,$message];
        }else{
            return [0,'数据保存失败，请重试！'];
        }
    }

    /**
     * 描述：视频html
     * @param $kid
     * @param $tid
     * @return array
     * @author demo
     */
    public function testVideoHtml($kid,$tid){
        $tid=$tid?$tid:0;
//        $klStudyDb=$this->getModel('KlStudy')->selectData(
//            '*',
//            ['KlID'=>['in',$kid]],
//            'StudyID asc'
//        );
        $klStudyDb = $this->getApiCommon('Common/selectData', '*', ['KlID'=>['in',$kid]], 'StudyID asc', '', 'KlStudy');
        $video = '';
        if($klStudyDb[0]['VideoList']){
            $tmpArrAll=array();
            foreach($klStudyDb as $iKlStudyDb){
                $tmpArr=explode('#@#',stripslashes($iKlStudyDb['VideoList']));
                foreach($tmpArr as $iTmpArr){
                    $tmpArrAll[]=$iTmpArr;
                }
            }
            if($tmpArrAll[$tid]){
                $tmpArrTwo=explode('#$#',$tmpArrAll[$tid]);
                if($tmpArrTwo[0]){
                    $video=$tmpArrTwo[0];
                }
            }
        }
        if($video){
            return [1,$video];
        }else{
            return [0,'视频没有找到！'];
        }
    }

    /**
     * 描述：生成作业SendID的记录
     * @param $requestID
     * @param $userID
     * @return array
     * @author demo
     */
    public function homeworkCreateSendID($requestID,$userID){
//        $userSendWorkModel = $this->getModel('UserSendWork');
//        $userWorkModel = $this->getModel('UserWork');
        $workID = explode('-', $requestID)[0];
        $classID = explode('-', $requestID)[1];
        if (!$workID || !$classID) {
            return [0,'请求参数错误，请重试！'];
        }
        //验证用户是否属于此班级
//        $classUserDb = $this->getModel('ClassUser')->findData(
//            'CUID',
//            ['ClassID' => $classID, 'UserID' => $userID, 'Status' => 0]
//        );
        $classUserDb = $this->getApiWork('Class/findData', 'CUID', ['ClassID' => $classID, 'UserID' => $userID, 'Status' => 0], '', 'ClassUser');
        if (!$classUserDb) {
            return ['您没有权限请求此班级的作业！'];
        }
        //验证此作业是否是该学生的
//        $userWorkDb = $this->getModel('UserWorkClass')->unionSelect('userWorkClassSelectByIdArr',$classID,$workID,$userID);
        $userWorkDb = $this->getApiWork('Work/unionSelect', 'userWorkClassSelectByIdArr',$classID,$workID,$userID);
        if (!$userWorkDb) {
            return [0,'您没有权限做此次的作业！'];
        }
        //验证sendWork表中是否已经存在此次记录
//        $sendWorkDb = $userSendWorkModel->findData(
//            'SendID',
//            ['WorkID' => $workID, 'UserID' => $userID, 'ClassID' => $classID]
//        );
        $sendWorkDb = $this->getApiWork('Work/findData', 'SendID', ['WorkID' => $workID, 'UserID' => $userID, 'ClassID' => $classID], '', 'UserSendWork');
        if ($sendWorkDb) {
            return [0,'作业记录已经存在！'];
        }
        //查看作业形式（下载or在线）和作业学科
//        $userWorkDb = $userWorkModel->findData(
//            'WorkStyle,SubjectID',
//            ['workID' => $workID]
//        );
        $userWorkDb = $this->getApiWork('Work/findData', 'WorkStyle,SubjectID', ['workID' => $workID], '', 'UserWork');
        $workStyle = $userWorkDb['WorkStyle'];
        $workSubjectID = $userWorkDb['SubjectID'];
        //写入SendWork表数据
//        $this->getApiWork('Work/insertData', [
//                'WorkID' => $workID,
//                'UserID' => $userID,
//                'ClassID' => $classID,
//                'SubjectID' => $workSubjectID,
//                'Status' => $workStyle == 1 ? 1 : 0,//下载作答下载后直接完成
//                'SendTime' => $workStyle == 1 ? time() : 0,
//                'DoTime' => $workStyle == 1 ? -1 : 0,
//            ], 'UserSendWork');
//            //写入SendWork表数据
        $userSendWorkModel = $this->getModel('UserSendWork');
        $sendID = $userSendWorkModel->insertData(
            [
                'WorkID' => $workID,
                'UserID' => $userID,
                'ClassID' => $classID,
                'SubjectID' => $workSubjectID,
                'Status' => $workStyle == 1 ? 1 : 0,//下载作答下载后直接完成
                'SendTime' => $workStyle == 1 ? time() : 0,
                'DoTime' => $workStyle == 1 ? -1 : 0,
            ]
        );
        //写入SendWork表成功 更新work表IfDelete字段为1
        //$sendID=$sendWorkDb['SendID'];
        if ($sendID) {
//            $userWorkModel->updateData(
//                ['IfDelete' => 1],
//                ['WorkID' => $workID]
//            );
            $this->getApiWork('Work/updateData', ['IfDelete' => 1], ['WorkID' => $workID], 'UserWork');
            return [1,$sendID];
        } else {
            return [0,'数据写入错误，请重试！'];
        }
    }

    /**
     * 描述：获取试题byid
     * @param $testID
     * @param $recordID
     * @param $isHomeWork
     * @param $ifAnswer
     * @return array
     * @author demo
     */
    public function getTestByID($testID,$recordID=0,$isHomeWork=0,$ifAnswer=0){
        if (!$testID) {
            return [0,'请求参数错误，请重试！'];
        }
        $data = $this->getApiTest('Test/getTestByID', $testID,$recordID,$isHomeWork,$ifAnswer);
        if(!$data||!is_array($data)){
            return [0,'索引获取试题出错！'];
        }
        return [1,$data];
    }

    /**
     * 描述：作业获取试题
     * @param $sendID
     * @param $userID
     * @return array
     * @author demo
     */
    public function homeworkGetTest($sendID,$userID){
        if (!$sendID) {
            return [0,'请求参数错误，请重试！'];
        }
        if ($this->checkHomeworkRID($sendID,$ifDone=false,$userID)===false) {
            return [0,'作业已经提交或者没有权限查看此作业！'];
        }
//        $data = $this->getModel('TestReal')->getHomeworkByIndex($sendID);//索引得到的试题数据
        $data = $this->getApiTest('Test/getHomeworkByIndex', $sendID);
        if(!$data||!is_array($data)){
            return [0,'索引获取试题出错！'];
        }
        return [1,$data];
    }

    /**
     * 描述：导学案获取试题或答案
     * @param $sendID
     * @param $ifAnswer
     * @param $userID
     * @param $isApp
     * @return array
     * @author demo
     */
    public function homeworkCaseGetTest($sendID,$ifAnswer,$userID,$isApp){
        $baseController = $isApp?A('AatApi/Base'):A('Aat/Base');
//        $testModel = $this->getModel('Test');
        if (!$sendID) {
            return [0,'缺少作业测试ID,请重试！'];
        }
        if ($this->checkHomeworkRID($sendID,$ifDone=$ifAnswer?true:false,$userID) === false) {
            return [0,'作业已经提交或者没有权限查看此作业！'];
        }
//        $workDb = $this->getModel('UserSendWork')->unionSelect('userSendWorkSelectById',$sendID)[0];
        $workDb = $this->getApiWork('Work/unionSelect', 'userSendWorkSelectById',$sendID)[0];
//        $data = $this->getModel('TestReal')->getCaseContentData($IDStr = $workDb['TestList'],$testField=[],$sendID);//获取到的知识和试题的数据
        $data = $this->getApiTest('Test/getCaseContentData', $IDStr = $workDb['TestList'],$testField=[],$sendID);
//        $caseMenuCache=SS('caseMenu');//栏目缓存，用于读取里面的栏目属性
        $caseMenuCache = $this->getApiGuide('CaseMenu/caseMenu');
        $numListConfig = C('WLN_NUM_LIST');//文字序号
        $cookie = unserialize($workDb['CookieStr']);
        $result = [
            'tempName'=>$cookie['tempName'],
            'tempDesc'=>$cookie['tempDesc'],
            'message'=>$workDb['Message'],
            'workName'=>$workDb['WorkName'],
//            'subjectName'=>SS('subject')[$workDb['SubjectID']]['SubjectName'],
            'subjectName'=>$this->getApiCommon('Subject/subject')[$workDb['SubjectID']]['SubjectName'],
            'endTime'=>date('m-d H:i',$workDb['EndTime']),
            'doTime'=>$workDb['DoTime']?$workDb['DoTime']:0,
            'checkTime'=>$workDb['CheckTime']?date('m-d H:i',$workDb['CheckTime']):'',
            'score'=>$workDb['CorrectRate']*100,
        ];//ajax返回的数据
        $exerciseInfo = [];//答题结果数据
        $exerciseAmount = ['right'=>0,'wrong'=>0,'not'=>0,'undo'=>0,'all'=>0];//答题结果统计数据包含正确n题错误m题等
        foreach($cookie['forum'] as $i=>$tempForum){
            //循环得到板块
            $number = 0;//板块开始重置题号
            $result['forum'][$i]['title'] = $tempForum[0];
            $result['forum'][$i]['subTitle'] = $tempForum[1];
            $exerciseInfo[$i]['name'] = $tempForum[0];//答题卡小版块名称
            foreach($tempForum[2] as $j=>$menu){
                //循环得到栏目
                $result['forum'][$i]['menu'][$j]['title'] = $menu['menuName'];
                $result['forum'][$i]['menu'][$j]['isTest'] = $menu['ifTest'];
                $isLore = $menu['ifTest']?false:true;
                $menuContentArr = explode(';',$menu['menuContent']);
                if(is_array($menuContentArr)&&count($menuContentArr)>0){
                    foreach($menuContentArr as $k=>$content){
                        if(!$content){
                            continue;
                        }
                        //循环得到试题和知识内容
                        $contentArr = explode('|',$content);
                        $id = $contentArr[0];
                        $isSys = $contentArr[1]?false:true;
                        $prefix = '';//ID前缀
                        if($isLore&&$isSys){
                            $prefix = 'l';
                        }elseif($isLore&&!$isSys){
                            $prefix = 'u';
                        }elseif(!$isLore&&!$isSys){
                            $prefix = 'c';
                        }
                        $id = $prefix.$id;//带c u l的前缀的ID
                        $iData = $data[$id];//试题或知识数据
                        $item = []; //知识或试题处理后用于返回的数据
                        if(array_key_exists('LoreID',$iData)){
                            //(1)知识处理
                            $number += 1;
                            $numStyle = $caseMenuCache[$menu['menuID']]['NumStyle'];
                            $item = [
                                'loreID'=>$iData['LoreID'],
                                'lore'=>$iData['Lore'],
                                'loreAnswer'=>$iData['Answer'],
                                'number'=>($numStyle==1&&$numListConfig)?$numListConfig[$number-1]:$number,//如果NumStyle为1，则为汉子序号
                            ];
                        }elseif(array_key_exists('TestID',$iData)){
                            //（2）试题处理
                            $item = [
                                'testID' => $iData['TestID'],
                                'test'=>$iData['Test'],//array包含options和title
                                'ifChoose'=>$iData['IfChoose'],
                                'diffID'=>$iData['DiffID'],
                            ];
                            $analyticArr = $rightAnswerArr = [];
                            if($ifAnswer){
                                $item['docID'] = $iData['DocID'];
                                $item['docName'] = $iData['DocName'];
                                $item['klID'] = $iData['KlID'];
                                $item['klList'] = $iData['KlList'];
                                if($iData['IfChoose'] == 1){
                                    //解析界面复合题分隔转换为数组
                                    $analyticArr = $this->getApiTest('Test/xtnum', $iData['Analytic'], 3);
                                    $rightAnswerArr = $this->getApiTest('Test/xtnum', $iData['Answer'], 3);
                                }
                            }
                            if($iData['doAnswer']&&is_array($iData['doAnswer'])){
                                //doAnswer赋值 exerciseInfo赋值
                                foreach($iData['doAnswer'] as $l=>$lDoAnswer){
                                    $number += 1;
                                    $item['doAnswer'][$l]['number'] = $number;//题号赋值
                                    $item['doAnswer'][$l]['ifChoose'] = $lDoAnswer['IfChoose'];
                                    $item['doAnswer'][$l]['answerID'] = $sendID.'-'.$id.'-'.$number.'-'.$lDoAnswer['OrderID'].
                                        '-'.$lDoAnswer['IfChoose'];//answerID赋值
                                    if($isApp){
                                        $answerImageArray = $baseController->processAppUserAnswer(
                                            'explode',
                                            formatString('IPReturn', $lDoAnswer['AnswerText'])
                                        );//用户作答
                                        $item['doAnswer'][$l]['userAnswer'] = $answerImageArray[0];
                                        $item['doAnswer'][$l]['userAnswerImage'] = $answerImageArray[1];
                                    }else{
                                        $item['doAnswer'][$l]['userAnswer'] = formatString('IPReturn', $lDoAnswer['AnswerText']);
                                    }
                                    if($ifAnswer){
                                        if($iData['IfChoose'] == 1){//复合题正确答案和分析处理
                                            $item['doAnswer'][$l]['rightAnswer'] = formatString('stripTags',$rightAnswerArr[$l]);
                                            $item['analytic'] .= '<span>'.$number.'. </span>'.$analyticArr[$l];
                                        }else{//非复合题答案和分析
                                            $item['doAnswer'][$l]['rightAnswer'] = formatString('stripTags',$iData['Answer']);
                                            $item['analytic'] .= $iData['Analytic'];
                                        }
                                        $item['doAnswer'][$l]['ifRight'] = $lDoAnswer['IfRight'];
                                        $exerciseInfo[$i]['test'][] = [
                                            'number'=>$number,
                                            'answerID'=>$item['doAnswer'][$l]['answerID'],
                                            'ifRight'=>$lDoAnswer['IfRight'],
                                        ];
                                        //答题数据数量的统计
                                        if($lDoAnswer['IfRight']==2){//正确
                                            $exerciseAmount['right']+=1;
                                        }elseif($lDoAnswer['IfRight']==1){//错误
                                            $exerciseAmount['wrong']+=1;
                                        }elseif($lDoAnswer['IfRight']==0){//无法判断
                                            $exerciseAmount['not']+=1;
                                        }else{//未作
                                            $exerciseAmount['undo']+=1;
                                        }
                                        $exerciseAmount['all'] += 1;
                                    }
                                }
                            }
                        }
                        if($item){
                            $result['forum'][$i]['menu'][$j]['content'][] = $item;
                        }else{
                            $errorLog = [
                                'msg' => '导学案中内容不存在，试题或知识ID:'.$id.'；作业sendID：'.$sendID,
                                'sql' => '空',
                                'description' => ''
                            ];
//                            $testModel->addErrorLog($errorLog);
                            $this->getApiTest('Test/addErrorLog', $errorLog);
                        }
                    }
                    //处理随机试题 如果栏目第一个内容是试题且作业为随机排序且为练习界面
                    if(array_key_exists('testID',$result['forum'][$i]['menu'][$j]['content'][0])&&$workDb['WorkOrder']==1&&!$ifAnswer){
                        shuffle($result['forum'][$i]['menu'][$j]['content']);
                    }
                }
            }
        }
        if($ifAnswer){
            //解析界面增加答题卡数据
            $result['exerciseInfo'] = $exerciseInfo;
            $result['exerciseAmount'] = $exerciseAmount;
        }
        $result['ifAnswer'] = $ifAnswer;//js模板引擎使用方便设置
        return [1,$result];
    }

    /**
     * 描述：作业提交
     * @param $sendID
     * @param $doTime
     * @param $userID
     * @param $realName
     * @return array
     * @author demo
     */
    public function homeworkSubmit($sendID,$doTime,$userID,$realName){
        $userAnswerRecordModel = $this->getModel('UserAnswerRecord');
//        $userSendWorkModel = $this->getModel('UserSendWork');
        $isHomework = true;
        if ($this->checkHomeworkRID($sendID,$ifDone=false,$userID) === false) {//一下语句防止重复插入数据
            return [0,'作业已经提交，请刷新重试！'];
        }
        $userAnswerRecordModel->updateIfRight($sendID,$isHomework);//更新题目正确情况IfRight
        $recordData = $userAnswerRecordModel->getExerciseInfo($sendID,$type=1,$isHomework);//本次测试相关信息
        //1.-------------------------计算本次测试分数（正确率）插入本次测试时间和分数到数据库-------------------------------
        $rightAmount = $recordData['right_amount'];
        $wrongAmount = $recordData['wrong_amount'];
        $undoAmount = $recordData['undo_amount'];
        $notAmount = $recordData['un_judge_amount'];
        $allAmount = $recordData['all_amount'];
        $scoreAmount = $rightAmount + $wrongAmount;//分数总计只计算正确和错误的，其它不考虑
        $record['SendID'] = $sendID;
        $record['CorrectRate'] = number_format($rightAmount / ($scoreAmount?$scoreAmount:1), 3);
        $record['Status'] = 1;
        $record['SendTime'] = time();
        $record['DoTime'] = $doTime;
//        $result = $userSendWorkModel->updateData(
//            $record,
//            ['SendID'=>$sendID]
//        );
        $result = $this->getApiWork('Work/updateData', $record, ['SendID'=>$sendID], 'UserSendWork');
        //2.-----------------插入作业动态表信息--------------------------------------------------------------------------
//        $sendWorkDb = $userSendWorkModel->findData(
//            'WorkID,ClassID',
//            ['SendID'=>$sendID]
//        );
        $sendWorkDb = $this->getApiWork('Work/findData', 'WorkID,ClassID', ['SendID'=>$sendID], '', 'UserSendWork');
        $classID = $sendWorkDb['ClassID'];//获取班级ID
        $workID = $sendWorkDb['WorkID'];//获取作业ID 作业名
//        $userWorkDb = $this->getModel('UserWork')->findData(
//            'WorkName,SubjectID',
//            ['WorkID'=>$workID]
//        );
        $userWorkDb = $this->getApiWork('Work/findData', 'WorkName,SubjectID', ['WorkID'=>$workID], '', 'UserWork');
        $workName = $userWorkDb['WorkName'];
        $dynamicContent = '<span class="u_id" uid="'.$userID.'">@'.$realName.
            '</span>提交了<span class="w_id" wid="'.$workID.'">#'.$workName.'</span>。';
        //获取班级下用户ID Array类型
//        $classUserDb = $this->getModel('ClassUser')->selectData(
//            'UserID',
//            ['ClassID'=>$classID,'Status'=>0]
//        );
        $classUserDb = $this->getApiWork('Class/selectData', 'UserID', ['ClassID'=>$classID,'Status'=>0], '', '', 'ClassUser');
        $receiveUserID = [];
        foreach($classUserDb as $iClassUserDb){
            $receiveUserID[] = $iClassUserDb['UserID'];
        }
//        (new ClassLogic())->insertClassDynamic($classID,$userID,$dynamicContent,$receiveUserID);
        $this->getApiAat('Class/insertClassDynamic', $classID,$userID,$dynamicContent,$receiveUserID);
        //3.-------------更新能力值和知识点能力值相关---------------------------------------------------------------------
        if($allAmount>0){
            $subjectID = $userWorkDb['SubjectID'];
            $this->getApiAat('Ability/createWorkAbility', $subjectID,$classID,$userID,$sendID,
                $workID,$rightAmount,$wrongAmount,$undoAmount,$notAmount);
//            (new AbilityLogic())->createWorkAbility($subjectID,$classID,$userID,$sendID,
//                $workID,$rightAmount,$wrongAmount,$undoAmount,$notAmount);
        }
        if ($result) {
            return [1,$sendID];
        } else {
            return [0, '数据保存错误请重新操作'];
        }
    }

    /**
     * 描述：提交答案处理
     * @param $answerID
     * @param $answerText
     * @return array
     * @author demo
     */
    public function homeworkDoAnswer($answerID,$answerText){
        if (!$answerID) {
            return [0,'数据参数错误，请重试！'];
        }
        if($answerText === ''){
            return [0,'答案不能为空！'];
        }
        $answerIDArray = explode('-',$answerID);
        //错误处理
        if($answerIDArray[0]==''|$answerIDArray[1]==''|$answerIDArray[2]==''|$answerIDArray[3]==''|
            $answerIDArray[4]==''){
            return [0,'数据参数错误，请重试！'];
        }
        $answerData = [
            'SendID'=>$answerIDArray[0],
            'TestID'=>$answerIDArray[1],
            'Number'=>$answerIDArray[2],
            'OrderID'=>$answerIDArray[3],
            'IfChoose'=>$answerIDArray[4],
            'AnswerText'=>formatString('IPReplace',$answerText),
            'LoadTime'=>time(),
        ];
        $result = $this->getModel('UserAnswerRecord')->updateAnswer($answerData,$isHomework=true);
        if ($result == false) {
            return [0,'答案提交错误,请重试！'];
        }else{
            return [1,''];
        }
    }

    /**
     * 描述：作业关闭
     * @param $sendID
     * @param $doTime
     * @param $userID
     * @return array
     * @author demo
     */
    public function homeworkClose($sendID,$doTime,$userID){
        if (!$sendID) {
            return [0,'自动保存失败！'];
        }
        //判断题目正确性（没有作答情况）之后插入数据库
        if ($this->checkHomeworkRID($sendID,$ifDone=false,$userID) !== false) {//验证sendID检测通过
//            $updateResult = $this->getModel('UserSendWork')->updateData(
//                ['DoTime'=>$doTime],
//                ['SendID'=>$sendID]
//            );
            $updateResult = $this->getApiWork('Work/updateData', ['DoTime'=>$doTime], ['SendID'=>$sendID], 'UserSendWork');
            if($updateResult){
                return [1,''];
            }else{
                return [0,'自动保存失败！'];
            }
        }else{
            return [0,'自动保存失败！'];
        }
    }




}