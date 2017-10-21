<?php
/**
 * @date 2014年11月26日
 * @author demo
 */
/**
 * 用户作答数据表类
 * 该数据表记录了提分测试和作业测试中的用户作答数据
 * 只有用户作答后才会写入该表数据，如果用户没有作答提交试卷，该表也是没有记录的
 */
namespace Aat\Model;

use Common\Model\BaseModel;
class UserAnswerRecordModel extends BaseModel
{
    public $division = 'c';
    /**
     * @覆盖父类方法。
     * @param $data
     * @param $modelName
     * @return mixed
     * @author demo 2015-12-21
     */
    public function insertData($data, $modelName=''){
        $result = parent::insertData($data,$modelName);
        if($result !== false){
            $this->getModel('StatisticsCounter')->increase('studentAnswerNum');
        }
        return $result;
    }

    /**
     * 计算某次测试做题信息
     * 【重要】改函数复杂的原因
     * 1.包含提分测试和作业测试
     * 2.用户只有作答了数据才会进入zj_user_answer_record表，所以不能直接统计此表
     * 【是拿查询的复杂度换取了数据库空间有利有弊】
     * @param int $id userTestRecordID或者sendID
     * @param int $type 两种返回类型
     * @param bool $isHomework 是否是作业记录 默认不是
     * @return array 正确 错误 未做  题目信息
     * @author demo 5.18
     */
    public function getExerciseInfo($id,$type=1,$isHomework=false) {
        $userTestRecordModel = $this->getModel('UserTestRecord');
//        $testRealModel = $this->getModel('TestReal');
//        $userSendWorkModel = $this->getModel('UserSendWork');
        $userAnswerRecordModel = $this->getModel('UserAnswerRecord');
        //获取试题ID数据-------------------------------------------------------------------------------------------------
        if(!$isHomework){//练习
            $testRecordDb = $userTestRecordModel->findData(
                'Content',
                ['TestID'=>$id]);
            $testIDsArray = explode(',',$testRecordDb['Content']);//试题ID数组，包含c字符串的，自建和混合试题通用
        }else{//作业
//            $testRecordDb= $userSendWorkModel->unionSelect('userSendWorkSelectById',$id)[0];
            $testRecordDb = $this->getApiWork('Work/unionSelect', 'userSendWorkSelectById',$id)[0];
            $testIDsArray = explode(',',$testRecordDb['TestList']);//试题ID数组，包含c字符串的，自建和混合试题通用
        }
        //获取试题复合题信息---------------------------------------------------------------------------------------------
//        $testJudgeDb = $testRealModel->getJudgeByIDs($testIDsArray);//[[TestID][OrderID]=>[TestID,OrderID,IfChoose],...] TestID键包含c
        $testJudgeDb = $this->getApiTest('Test/getJudgeByIDs', $testIDsArray);

//        //排除$testIDsArray中不存在的试题；
//        $field=array('testid');
//        $where=array('TestID'=>$testIDsArray);
//        $order='TestID ASC';
//        $page=array('page'=>1,'perpage'=>100,'limit'=>100);
//        $curTestIDsArray=$this->getModel('TestRealQuery')->getIndexTest($field,$where,$order,$page,0,2);
//        $tmp=array();
//        foreach($testIDsArray as $i => $testID) {
//            if(strpos($testID,'u') || strpos($testID,'l')) $tmp[]=$testID;
//            if($curTestIDsArray[$testID]) $tmp[]=$testID;
//        }
//        $testIDsArray=$tmp;

        $allTest = [];//所有试题，键位TestID-OrderID，非复合题OrderID=0
        //获取所有试题的TestID-OrderID 默认为未做的试题IfRight=-1
        $start=1;
        foreach ($testIDsArray as $i => $testID) {
            if(strpos($testID,'u')===false&&strpos($testID,'l')===false){//知识目前不计入统计 以后知识如果可以作答，这里需要去掉
                if ($testJudgeDb[$testID] && is_array($testJudgeDb[$testID])) {//此试题是复合题
                    foreach ($testJudgeDb[$testID] as $j) {
                        $allTest[$testID . '-' . $j['OrderID']] = ['TestID' => $testID, 'Number' => $i + 1, 'OrderID' => $j['OrderID'], 'IfRight' => -1,'Start'=>$start];
                    }
                    $thisNum=count($testJudgeDb[$testID]);
                } else {//非复合题
                    $allTest[$testID . '-0'] = ['TestID' => $testID, 'Number' => $i + 1, 'OrderID' => 0, 'IfRight' => -1,'Start'=>$start];
                    $thisNum=1;
                }
                $start+=$thisNum;
            }
        }
        if(!$isHomework){//练习
            $answerData = $userAnswerRecordModel->selectData(
                'AnswerID, TestID,Number,OrderID,IfRight,TestType,Score',
                ['TestRecordID' => $id]
            );
        }else{//作业
            $answerData = $userAnswerRecordModel->selectData(
                'TestID,Number,OrderID,IfRight,TestType',
                ['SendID' => $id]
            );
        }
        //返回两种格式的数据---------------------------------------------------------------------------------------------
        $result = [];
        if ($type == 1) {
            $right = $wrong = $unJudge = $undo = $do =[];
            foreach ($answerData as $iAnswerData) {
                $iAnswerData['Start']=$allTest[$iAnswerData['TestID'].'-'.$iAnswerData['OrderID']]['Start'];
                if ($iAnswerData['IfRight'] == 2) {
                    $right[] = $iAnswerData;
                } elseif ($iAnswerData['IfRight'] == 1) {
                    $wrong[] = $iAnswerData;
                } elseif ($iAnswerData['IfRight'] == 0) {
                    $unJudge[] = $iAnswerData;
                }
                $testID = $iAnswerData['TestType'] == 2?$this->division.$iAnswerData['TestID']:$iAnswerData['TestID'];
                $do[$testID . '-' . $iAnswerData['OrderID']] = $iAnswerData;
            }
            foreach($allTest as $i=>$iAllTest){//除以做的试题，其他都是未作试题
                if(!$do[$i]){
                    $undo[] = $iAllTest;
                }
            }
            $result = array(
                'right_amount' => count($right),
                'wrong_amount' => count($wrong),
                'un_judge_amount' => count($unJudge),
                'undo_amount' => count($undo),
                'all_amount' => count($right) + count($wrong) + count($unJudge) + count($undo),
                'right' => $right,
                'wrong' => $wrong,
                'un_judge' => $unJudge,
                'undo' => $undo
            );
        } elseif ($type == 2) {
            $idAnswer=array();
            foreach ($answerData as $iAnswerData) {
                $idAnswer[$iAnswerData['TestID'].'-'.$iAnswerData['OrderID']]=$iAnswerData;
            }
            $result=array();
            foreach($allTest as $i=>$iAllTest){
                if($idAnswer[$i])
                    $result[]=$idAnswer[$i];
                else
                    $result[]=$iAllTest;
            }
//            $result = array_merge($right, $wrong, $unJudge, $undo);
//            //按照Number，OrderID正序排序  根据查询数据重新规划Number数据 $testIDsArray $answerData
//            foreach ($result as $i => $k) {
//                $orderNumber[$i] = $k['Number'];
//                $orderOrder[$i] = $k['OrderID'];
//            }
//            array_multisort($orderNumber, SORT_ASC, $orderOrder, SORT_ASC, $result);
        }
        return $result;
    }

    /**
     * 进行测试题答案添加操作，如果不存在则添加，存在则更新
     * @param array $answer zj_user_answer_record表字段信息数组
     * @param bool $isHomework 是否是作业
     * @return bool 操作是否成功
     * @author demo 5.18
     */
    public function updateAnswer($answer,$isHomework=false) {
        $map = [
            'Number' => $answer['Number'],
            'OrderID' => $answer['OrderID']
        ];
        if($isHomework){
            $map['SendID'] = $answer['SendID'];
        }else{
            $map['TestRecordID'] = $answer['TestRecordID'];
        }
        if(strpos($answer['TestID'], $this->division) !== false){//校本题库试题
            $map['TestType'] = 2;
            $answer['TestType'] = 2;
            $answer['TestID'] = str_replace($this->division,'',$answer['TestID']);//校本题库写入数据库去掉c
        }else{
            $map['TestType'] = 1;
            $answer['TestType'] = 1;
        }
        $answerData = $this->findData(
            'AnswerID',
            $map);
        if($answerData){
            //做更新操作
            $result = $this->updateData(
                    $answer,
                    'AnswerID=' . $answerData['AnswerID']
                );
        }else{
            //做添加操作
            $result = $this->insertData(
                $answer);
        }
        return $result == false ? false : true;
    }

    /**
     * 更新某次测试的试题正确情况
     * @param int $recordID testRecordID 或者 sendID
     * @param bool $isHomework 是否是作业
     * @return bool
     * @author demo
     */
    public function  updateIfRight($recordID,$isHomework=false,$isAatTestStyle=false) {
        $testModel = $this->getModel('Test');
        if(!$isHomework){//练习
            $where = ['TestRecordID'=>$recordID];
        }else{//作业
            $where = ['SendID'=>$recordID];
        }
        $answerDb = $this->selectData(
            'AnswerID,TestID,TestType,AnswerText,OrderID,IfChoose AS AnswerIfChoose',
            $where
        );
        if($answerDb){//如果有题目作答的信息,则进行下面的更新操作

            //获取试题对应分值  如果是打分数据则写入相应分值到作答表 目前仅系统试题
            $testScore=array();
            if($isAatTestStyle){
                $testList = $this->getModel('UserTestRecord')->findData('Content', "TestID={$recordID}");
                $testList = $testList['Content'];
                $testList = $this->getModel('TestAttrReal')->selectData('TestID, TestNum, Score', "TestID IN({$testList})");

                foreach($testList as $value){
                    $scoreTmp=$value['Score'];
                    if(strstr($scoreTmp,',')){
                        $scoreTmp=explode(',',$scoreTmp);
                    }

                    if($value['TestNum']>1){
                        if(!is_array($scoreTmp)){
                            $scoreTmp=array($scoreTmp);
                        }
                        $tmpCount=count($scoreTmp);
                        if($value['TestNum']>$tmpCount){
                            for($x=$tmpCount;$x<$value['TestNum'];$x++){
                                $scoreTmp[$x]=0;
                            }
                        }
                    }

                    $testScore[$value['TestID']]=$scoreTmp;
                }
            }

            //循环得到试题ID字符串（带c的）
            $testIDsArr = [];
            foreach($answerDb as $iAnswerDb){
                //1 系统 2 自建
                $testIDsArr[] = $iAnswerDb['TestType'] == 2?$this->division.$iAnswerDb['TestID']:$iAnswerDb['TestID'];
            }
            $testIDsStr = implode(',',$testIDsArr);
//            $testQuery = getStaticFunction('TestQuery','getInstance');
            $where['TestID'] = $testIDsStr;
            if($isAatTestStyle){
               $where['AatTestStyle'] = 1;
            }
//            $testQuery->setParams([
//                'field' => ['testid','ifchoose','answernormal'],
//                'page' => ['page'=>1,'perpage'=>500],
//                'limit' =>500,
//                'convert' => 'testid',
//                'where'=>$where
//            ],$testIDsStr);
//            $testIndex = $testQuery->getResult($division =  false);//0 试题数组 1总数量 2每页数量
//            $testIndex = $this->getApiTest('getAllIndexTest', ['testid','ifchoose','answernormal'], ['TestID'=>$testIDsStr,'AatTestStyle'=>1], '', ['page'=>1,'perpage'=>500], 0, 'testid');
            $testIndex = $this->getModel('TestRealQuery')->getIndexTest(['testid','ifchoose','answernormal'], $where, '', ['page'=>1,'perpage'=>500], 0, 1);
            $testIndexAnswer = $testIndex[0];//已经做了convert的testid为键的转换
            if(!$testIndexAnswer){//如果索引没有查询到
                return false;
            }
            foreach($answerDb as $i=>$iAnswerDb){
                $answerID = $iAnswerDb['AnswerID'];
                $testID = $iAnswerDb['TestType'] == 2?$this->division.$iAnswerDb['TestID']:$iAnswerDb['TestID'];
                $testIfChoose = $testIndexAnswer[$testID]['ifchoose'];
                $answerIfChoose = $iAnswerDb['AnswerIfChoose'];
                $answerOrderID = $iAnswerDb['OrderID'];
                if ($testIfChoose == 1) {//复合题
                    $rightAnswer = $testModel->xtnum($testIndexAnswer[$testID]['answernormal'], 3)[$answerOrderID];
                    if ($answerIfChoose == 0) {//无法判断 复合题中的大题
                        $this->updateData(
                            ['IfRight' => 0],
                            ['AnswerID' => $answerDb[$i]['AnswerID']]
                        );
                    } else {
                        $ifRight = $this->ifRight($rightAnswer, $answerDb[$i]['AnswerText']);
                        $data = ['IfRight' => $ifRight];
                        //当为打分试卷时写入默认得分数据
                        if($isAatTestStyle){
                            if(2 == $ifRight){
                                $data['Score'] =$testScore[$iAnswerDb['TestID']][$answerOrderID-1];
                            }else if(1 == $ifRight){
                                $data['Score'] = 0;
                            }
                        }
                        $this->updateData(
                            $data,
                            ['AnswerID' => $answerID]
                        );
                    }
                } elseif ($testIfChoose == 0) {//大题
                    $this->updateData(
                        ['IfRight' => 0],
                        ['AnswerID' => $answerDb[$i]['AnswerID']]
                    );
                } else {//选择题
                    $rightAnswer = $testIndexAnswer[$testID]['answernormal'];
                    $ifRight = $this->ifRight($rightAnswer, $answerDb[$i]['AnswerText']);
                    $data = ['IfRight' => $ifRight];
                    //当为打分试卷时写入默认得分数据
                    if($isAatTestStyle){
                        if(2 == $ifRight){
                            $data['Score'] =$testScore[$iAnswerDb['TestID']];
                        }else if(1 == $ifRight){
                            $data['Score'] = 0;
                        }
                    }

                    $this->updateData(
                        $data,
                        ['AnswerID' => $answerDb[$i]['AnswerID']]
                    );
                }
            }
        }
        return true;
    }

    /**
     * 获取指定testRecordID或者sendID所有测试中的可判断正确的题目的答题信息和题目信息
     * @param int $IDs TestRecordID或者sendID的逗号分隔 查询这些记录中的题目信息
     * @param bool $isHomework 默认false 为true时计算作业系统
     * @return array 键为试题ID；之后的测试记录的答题信息和题目信息：正确否 难度 猜测系数 试题类型（自建、系统）
     * @author demo 5.5.20
     */
    public function getTestInRecords($IDs,$isHomework = false) {
        if(!$IDs){
            return null;
        }
        $field = $isHomework?'SendID':'TestRecordID';
        $answerDb = $this->selectData(
            'AnswerID,TestID,IfRight,IfChoose,TestType',
            [$field=>['in',$IDs],'IfChoose'=>['gt',1]],
            'LoadTime ASC');//正序排序，之后的合并会新做的试题覆盖旧作的试题（如果有重复试题）
        //循环得到试题ID字符串（带c的）
        $testIDsArr = [];
        foreach($answerDb as $iAnswerDb){
            //1 系统 2 自建
            $testIDsArr[] = $iAnswerDb['TestType'] == 2?$this->division.$iAnswerDb['TestID']:$iAnswerDb['TestID'];
        }
        $testIDsStr = implode(',',array_unique($testIDsArr));
//        $testQuery = getStaticFunction('TestQuery','getInstance');
//        $testQuery->setParams([
//            'field' => ['testid','diff'],
//            'page' => ['page'=>1,'perpage'=>500],
//            'limit' =>500,
//            'convert' => 'testid'
//        ],$testIDsStr);
//        $testIndex = $testQuery->getResult($division =  false);//0 试题数组 1总数量 2每页数量
//        $testIndex = $this->getApiTest('getAllIndexTest', ['testid','diff'], ['TestID'=>$testIDsStr], '', ['page'=>1,'perpage'=>500], 0, 'testid');
        $testIndex = $this->getModel('TestRealQuery')->getIndexTest(['testid','diff'], ['TestID'=>$testIDsStr], '', ['page'=>1,'perpage'=>500], 0, 1);
        $result = [];
        foreach($answerDb as $iAnswerDb){
            $testID = $iAnswerDb['TestType'] == 2?$this->division.$iAnswerDb['TestID']:$iAnswerDb['TestID'];
            $result[$testID] = [
                'TestID'=>$testID,
                'IfRight'=>$iAnswerDb['IfRight'],
                'IfChoose'=>$iAnswerDb['IfChoose'],
                'TestType'=>$iAnswerDb['TestType'],
                'Diff'=>$testIndex[0][$testID]['diff'],
            ];
        }
        return $result;
    }

    /**
     * 获取最近500道有效作答 IfRight字段值为1或者2的
     * 用于计算知识点能力值 用于PersonalReport
     * 【重要】相同的题目做了多遍只算最后一次测试的作答情况
     * @param string|int $user 用户名或者用户ID
     * @param array $klListArr 需要查询的知识点数组
     * @param bool $isHomework 是否是作业
     * @return array 返回以知识点为键
     * @author demo 5.5.21
     */
    public function getKlsByID($user,$klListArr,$isHomework = false){
        $where['a.IfRight'] = ['gt', 0];//这里查询IfRight=1或者2的试题，即能正确或者错误的
        if($klListArr){
            $where['d.KlID|e.KlID'] = ['in',array_unique($klListArr)];
        }
        if($isHomework){
            $where['UserID'] = $user;
            $answerDb=$this->unionSelect('UserAnswerRecordSendWordByWhere',$where);
        }else{
            $where['UserName'] = $user;
            $answerDb=$this->unionSelect('UserAnswerRecordUserTestRecordByWhere',$where);
        }
        if(!$answerDb){
            return null;
        }
        //循环得到试题ID字符串（带c的）
        $testIDsArr = [];
        foreach($answerDb as $iAnswerDb){
            //1 系统 2 自建
            $testIDsArr[] = $iAnswerDb['TestType'] == 2?$this->division.$iAnswerDb['TestID']:$iAnswerDb['TestID'];
        }
        $testIDsStr = implode(',',$testIDsArr);
//        $testQuery = getStaticFunction('TestQuery','getInstance');
//        $testQuery->setParams([
//            'field' => ['testid','diff'],
//            'page' => ['page'=>1,'perpage'=>500],
//            'limit' =>500,
//            'convert' => 'testid'
//        ],$testIDsStr);
//        $testIndex = $testQuery->getResult($division =  false);//0 试题数组 1总数量 2每页数量
//        $testIndex = $this->getApiTest('getAllIndexTest', ['testid','diff'], ['TestID'=>$testIDsStr], '', ['page'=>1,'perpage'=>500], 0, 'testid');
        $testIndex = $this->getModel('TestRealQuery')->getIndexTest(['testid','diff'], ['TestID'=>$testIDsStr], '', ['page'=>1,'perpage'=>500], 0, 1);
        $result = [];//最后需要返回的结果
        foreach ($answerDb as $iAnswerDb) {
            $testID = $iAnswerDb['TestType'] == 2 ? $this->division . $iAnswerDb['TestID'] : $iAnswerDb['TestID'];
            $diff = $testIndex[0][$testID]['diff'];
            $klID = $iAnswerDb['CKlID']?$iAnswerDb['CKlID']:$iAnswerDb['SKlID'];
            $result[$klID]['TestID' . $testID] = [
                'KlID' => $klID,
                'TestID' => $testID,
                'IfRight' => $iAnswerDb['IfRight'],
                'IfChoose' => $iAnswerDb['IfChoose'],
                'Diff' => $diff,
            ];
        }
        return $result;
    }

    /**
     * 获取最近500道有效作答 IfRight字段值为1或者2的
     * 用于计算技能能力值 用于PersonalReport
     * 【重要】相同的题目做了多遍只算最后一次测试的作答情况
     * @param string|int $user 用户名或者用户ID
     * @param array $klListArr 需要查询的知识点数组
     * @param bool $isHomework 是否是作业
     * @return array 返回以知识点为键
     * @author demo 5.5.21
     */
    public function getSkillByID($user,$skillListArr,$isHomework = false){
        $where['a.IfRight'] = ['gt', 0];//这里查询IfRight=1或者2的试题，即能正确或者错误的
        if($skillListArr){
            $where['d.SkillID|e.SkillID'] = ['in',array_unique($skillListArr)];
        }
        if($isHomework){
            $where['UserID'] = $user;
            $answerDb=$this->unionSelect('UserAnswerRecordSendWordByWhereSkill',$where);
        }else{
            $where['UserName'] = $user;
            $answerDb=$this->unionSelect('UserAnswerRecordUserTestRecordByWhereSkill',$where);
        }
        if(!$answerDb){
            return null;
        }
        //循环得到试题ID字符串（带c的）
        $testIDsArr = [];
        foreach($answerDb as $iAnswerDb){
            //1 系统 2 自建
            $testIDsArr[] = $iAnswerDb['TestType'] == 2?$this->division.$iAnswerDb['TestID']:$iAnswerDb['TestID'];
        }
        $testIDsStr = implode(',',$testIDsArr);
//        $testQuery = getStaticFunction('TestQuery','getInstance');
//        $testQuery->setParams([
//            'field' => ['testid','diff'],
//            'page' => ['page'=>1,'perpage'=>500],
//            'limit' =>500,
//            'convert' => 'testid'
//        ],$testIDsStr);
//        $testIndex = $testQuery->getResult($division =  false);//0 试题数组 1总数量 2每页数量
//        $testIndex = $this->getApiTest('getAllIndexTest', ['testid','diff'], ['TestID'=>$testIDsStr], '', ['page'=>1,'perpage'=>500], 0, 'testid');
        $testIndex = $this->getModel('TestRealQuery')->getIndexTest(['testid','diff'], ['TestID'=>$testIDsStr], '', ['page'=>1,'perpage'=>500], 0, 1);
        $result = [];//最后需要返回的结果
        foreach ($answerDb as $iAnswerDb) {
            $testID = $iAnswerDb['TestType'] == 2 ? $this->division . $iAnswerDb['TestID'] : $iAnswerDb['TestID'];
            $diff = $testIndex[0][$testID]['diff'];
            $skillID = $iAnswerDb['CSkillID']?$iAnswerDb['CSkillID']:$iAnswerDb['SSkillID'];
            $result[$skillID]['TestID' . $testID] = [
                'SkillID' => $skillID,
                'TestID' => $testID,
                'IfRight' => $iAnswerDb['IfRight'],
                'IfChoose' => $iAnswerDb['IfChoose'],
                'Diff' => $diff,
            ];
        }
        return $result;
    }
    /**
     * 获取最近500道有效作答 IfRight字段值为1或者2的
     * 用于计算能力能力值 用于PersonalReport
     * 【重要】相同的题目做了多遍只算最后一次测试的作答情况
     * @param string|int $user 用户名或者用户ID
     * @param array $klListArr 需要查询的知识点数组
     * @param bool $isHomework 是否是作业
     * @return array 返回以知识点为键
     * @author demo 5.5.21
     */
    public function getCapacityByID($user,$capacityListArr,$isHomework = false){
        $where['a.IfRight'] = ['gt', 0];//这里查询IfRight=1或者2的试题，即能正确或者错误的
        if($capacityListArr){
            $where['d.CapacityID|e.CapacityID'] = ['in',array_unique($capacityListArr)];
        }
        if($isHomework){
            $where['UserID'] = $user;
            $answerDb=$this->unionSelect('UserAnswerRecordSendWordByWhereCapacity',$where);
        }else{
            $where['UserName'] = $user;
            $answerDb=$this->unionSelect('UserAnswerRecordUserTestRecordByWhereCapacity',$where);
        }
        if(!$answerDb){
            return null;
        }
        //循环得到试题ID字符串（带c的）
        $testIDsArr = [];
        foreach($answerDb as $iAnswerDb){
            //1 系统 2 自建
            $testIDsArr[] = $iAnswerDb['TestType'] == 2?$this->division.$iAnswerDb['TestID']:$iAnswerDb['TestID'];
        }
        $testIDsStr = implode(',',$testIDsArr);
//        $testQuery = getStaticFunction('TestQuery','getInstance');
//        $testQuery->setParams([
//            'field' => ['testid','diff'],
//            'page' => ['page'=>1,'perpage'=>500],
//            'limit' =>500,
//            'convert' => 'testid'
//        ],$testIDsStr);
//        $testIndex = $testQuery->getResult($division =  false);//0 试题数组 1总数量 2每页数量
//        $testIndex = $this->getApiTest('getAllIndexTest', ['testid','diff'], ['TestID'=>$testIDsStr], '', ['page'=>1,'perpage'=>500], 0, 'testid');
        $testIndex = $this->getModel('TestRealQuery')->getIndexTest(['testid','diff'], ['TestID'=>$testIDsStr], '', ['page'=>1,'perpage'=>500], 0, 1);
        $result = [];//最后需要返回的结果
        foreach ($answerDb as $iAnswerDb) {
            $testID = $iAnswerDb['TestType'] == 2 ? $this->division . $iAnswerDb['TestID'] : $iAnswerDb['TestID'];
            $diff = $testIndex[0][$testID]['diff'];
            $capacityID = $iAnswerDb['CCapacityID']?$iAnswerDb['CCapacityID']:$iAnswerDb['SCapacityID'];
            $result[$capacityID]['TestID' . $testID] = [
                'CapacityID' => $capacityID,
                'TestID' => $testID,
                'IfRight' => $iAnswerDb['IfRight'],
                'IfChoose' => $iAnswerDb['IfChoose'],
                'Diff' => $diff,
            ];
        }
        return $result;
    }

    /**
     * 根据测试记录TestRecordID或者SendID获取知识点对应的试题和作答情况
     * 被使用：PersonalReportAction
     * @param int $id TestRecordID|SendID
     * @param bool $isHomework 是否是作业 默认不是
     * @return array 知识点下试题的作答情况统计
     * @author demo 5.5.21
     */
    public function getKlInfoByID($id,$isHomework = false) {
        $field = $isHomework ? 'SendID' : 'TestRecordID';
        $answerDb = $this->selectData(
            'TestID,IfRight,TestType',
            [$field=>$id]
        );
        foreach($answerDb as $iAnswerDb){
            //1 系统 2 自建
            $testIDsArr[] = $iAnswerDb['TestType'] == 2?$this->division.$iAnswerDb['TestID']:$iAnswerDb['TestID'];
        }
        $testIDsStr = implode(',',$testIDsArr);
//        $testQuery = getStaticFunction('TestQuery','getInstance');
//        $testQuery->setParams([
//            'field' => ['testid','klid'],
//            'page' => ['page'=>1,'perpage'=>500],
//            'limit' =>500,
//            'convert' => 'testid'
//        ],$testIDsStr);
//        $testIndex = $testQuery->getResult($division =  false);//0 试题数组 1总数量 2每页数量
//        $testIndex = $this->getApiTest('getAllIndexTest', ['testid','klid'], ['TestID'=>$testIDsStr], '', ['page'=>1,'perpage'=>500], 0, 'testid');
        $testIndex = $this->getModel('TestRealQuery')->getIndexTest(['testid','klid','skillid','capacityid'], ['TestID'=>$testIDsStr], '', ['page'=>1,'perpage'=>500], 0, 1);
        $testKl = $testIndex[0];
        $klList = [];//知识点列表数组
        $klTest = [];//知识点答题情况试题ID
        $skillList = [];//技能列表数组
        $skillTest = [];//技能答题情况试题ID
        $capacityList = [];//能力列表数组
        $capacityTest = [];//能力答题情况试题ID
        foreach ($answerDb as $iAnswerDb) {
            $testID = $iAnswerDb['TestType'] == 2?$this->division.$iAnswerDb['TestID']:$iAnswerDb['TestID'];
            $ifRight = $iAnswerDb['IfRight'];
            $klArr = $testKl[$testID]['klid'];
            foreach($klArr as $iKlArr){
                $klList[] = $iKlArr;
                //【重要】下面不直接写数量，而是写testID是因为还需要根据知识点结构做合并，之后才能count统计
                if($ifRight == 2){
                    $klTest[$iKlArr]['right'][] = $testID;
                }elseif($ifRight == 1){
                    $klTest[$iKlArr]['wrong'][] = $testID;
                }elseif($ifRight == 0){
                    $klTest[$iKlArr]['not'][] = $testID;
                }elseif($ifRight == -1){
                    $klTest[$iKlArr]['undo'][] = $testID;
                }
            }

            $skillArr = $testKl[$testID]['skillid'];
            foreach($skillArr as $iSkillArr){
                $skillList[] = $iSkillArr;
                //【重要】下面不直接写数量，而是写testID是因为还需要根据知识点结构做合并，之后才能count统计
                if($ifRight == 2){
                    $skillTest[$iSkillArr]['right'][] = $testID;
                }elseif($ifRight == 1){
                    $skillTest[$iSkillArr]['wrong'][] = $testID;
                }elseif($ifRight == 0){
                    $skillTest[$iSkillArr]['not'][] = $testID;
                }elseif($ifRight == -1){
                    $skillTest[$iSkillArr]['undo'][] = $testID;
                }
            }


            $capacityArr = $testKl[$testID]['capacityid'];
            foreach($capacityArr as $iCapacityArr){
                $capacityList[] = $iCapacityArr;
                //【重要】下面不直接写数量，而是写testID是因为还需要根据知识点结构做合并，之后才能count统计
                if($ifRight == 2){
                    $capacityTest[$iCapacityArr]['right'][] = $testID;
                }elseif($ifRight == 1){
                    $capacityTest[$iCapacityArr]['wrong'][] = $testID;
                }elseif($ifRight == 0){
                    $capacityTest[$iCapacityArr]['not'][] = $testID;
                }elseif($ifRight == -1){
                    $capacityTest[$iCapacityArr]['undo'][] = $testID;
                }
            }
        }
        array_unique($klList);
        array_unique($skillList);
        array_unique($capacityList);
        return ['klTest'=>$klTest,'klList'=>$klList,'skillTest'=>$skillTest,'skillList'=>$skillList,'capacityTest'=>$capacityTest,'capacityList'=>$capacityList];

    }

    /**
     * 根据TestID和AnswerText判断试题是否正确（多选或者单选）
     * 【重要】不能判断带顺序的答案
     * 【重要】这里只判断选择题，所以stripTags去除html标签，如果处理大题，这里需要注意了
     * @param $rightAnswer string 单道试题正确答案（如果复合题则需要先分解）
     * @param $answer string 试题答案字符串
     * @return int -1未作答 0错误 1正确
     * @author demo  5.5.25
     */
    public function ifRight($rightAnswer, $answer) {
        $rightUpper = strtoupper(formatString('stripTags',$rightAnswer));
        $ifRight = 0;//默认无法判断0
        if ($answer == '') {
            $ifRight = -1; //未作答
        } else {
            $options = C('WLN_OPTIONS');
            if($options){
                $right = [];//正确答案格式化后的数组
                $myRight = [];//答案格式化后的数组
                $answer= strtoupper(formatString('stripTags',$answer));
                foreach($options as $iOptions){
                    if (strpos($answer, $iOptions) !== false) {
                        $myRight[] = $iOptions;
                    }
                    if (strpos($rightUpper, $iOptions) !== false) {
                        $right[] = $iOptions;
                    }
                }
                asort($myRight);
                asort($right);
                $ifRight = (implode(',', $myRight) == implode(',', $right)) ? 2 : 1; //2正确 1错误
            }
        }
        return $ifRight;
    }

    /**
     * 获取用户某学科下错误的试题数量
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @return int 错误试题数量
     * @author demo
     */
    public function getWrongAmount($userName,$subjectID){
        $wrongAmount = $this->unionSelect('userAnswerRecordSelectCount',$userName,$subjectID);
        return $wrongAmount;
    }

}