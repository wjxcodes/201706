<?php
/**
 * @author demo
 * @date 2015年12月30日
 */

namespace Aat\Api;


class AbilityApi extends BaseApi
{
    protected $userAnswerRecordModel;


    public function _initialize() {
        $this->userAnswerRecordModel = $this->getModel('UserAnswerRecord');
    }

    /**
     * 更新用户测试的能力值表和知识点能力值表（一共三张表）【ExerciseAction调用】
     * @param int $subjectID 学科ID
     * @param string $userName 用户名
     * @param int $testRecordID 测试ID
     * @param int $score 分值
     * @param int $right 正确
     * @param int $wrong 错误
     * @param int $undo 未做
     * @param int $not 无法判断的
     * @author demo
     */
    public function createAbility($subjectID,$userName,$testRecordID,$score,$right,$wrong,$undo,$not){
        //计算用户能力值
        $this->userAbility($subjectID,$userName,$testRecordID,$score,$right,$wrong,$undo,$not);
        //计算用户知识点能力值
        $this->userKlsAbility($subjectID,$userName,$testRecordID);
    }

    /**
     * 计算作业相关能力值并插入数据库【MyHomeworkExerciseAction调用】
     * 计算的但是为某学科某用户，不分班级，即每个用户如果每个学科加入两个班级，两个班级的数据是通用的在一起的
     * @param int $subjectID 学科ID
     * @param int $classID 班级ID
     * @param int $userID 用户ID
     * @param int $sendID 作业测试记录ID
     * @param int $workID 作业ID
     * @param int $right 正确试题数量
     * @param int $wrong 错误试题数量
     * @param int $undo 未做试题数量
     * @param int $not 无法判断正误试题数量
     * @author demo
     */
    public function createWorkAbility($subjectID,$classID,$userID,$sendID,$workID,$right,$wrong,$undo,$not){
        //计算学生作业能力值
        $this->workStudentAbility($subjectID,$classID,$userID,$sendID,$right,$wrong,$undo,$not);
        //计算学生作业对应知识点的能力值
        $this->workKlsAbility($subjectID,$classID,$userID,$sendID,$workID);
    }
    /**
     * 计算用户提分测试的能力值和预测分，user_forecast表写入数据
     * @param int $subjectID 学科ID
     * @param string $userName 用户名
     * @param int $testRecordID 测试记录ID
     * @param int $score 分数
     * @param int $right 正确
     * @param int $wrong 错误
     * @param int $undo 未做
     * @param int $not 无法判断正误
     * @author demo 5.5.27
     */
    private function userAbility($subjectID,$userName,$testRecordID,$score,$right,$wrong,$undo,$not){
        $userForecastModel = $this->getModel('UserForecast');
        $forecastData = [];//需要插入userForecast表的数据
        if($userForecastModel->ifAbility($userName,$subjectID)){//需要生成新的能力值
            $lastRecordID = $userForecastModel->getLastAbility($userName,$subjectID)['TestRecordID'];//最新一次生成能力值的testRecordID
            $lastRecordID = $lastRecordID?$lastRecordID:0;
            $recordIDs = $userForecastModel->getTestRecordIDs($userName,$subjectID,$lastRecordID);
            $testArray = $this->userAnswerRecordModel->getTestInRecords($recordIDs,$isWork = false);
            //如果$testArray计算为空，则下面的能力值和分数计算为null和-1
            $forecastData['ForecastAbility'] = $this->getAbilityByTestArray($testArray);
            $forecastData['ForecastScore'] = $userForecastModel->getForecastScore($subjectID, $forecastData['ForecastAbility']);
            //需要更新IsLast值【IsLast记录最后一次生成能力值】
            $userForecastModel->updateData(['IsLast'=>0],
                ['UserName'=>$userName,'SubjectID'=>$subjectID,'IsLast'=>1]
            );//把该用户本学科下其他IsLast的值改为0
            $forecastData['IsLast'] = 1;
        }else{//不需要生成能力值
            $forecastData['ForecastAbility'] = null;
            $forecastData['ForecastScore'] = -1;
            $forecastData['IsLast'] = 0;
        }
        //排名计算 1.新用户默认-3能力值计算排名2.新用户产生了第一次的能力值，排名正常计算3.用户不生成能力值的记录排名不变
        $forecastAbility = $forecastData['ForecastAbility']===null?null:$forecastData['ForecastAbility'];
        $ranking = $userForecastModel->getExerciseRanking($userName,$forecastAbility,$subjectID);
        $forecastData['ExerciseRanking'] = $ranking;

        $forecastData['SubjectID'] = $subjectID;
        $forecastData['UserName'] = $userName;
        $forecastData['TestRecordID'] = $testRecordID;
        $forecastData['ExerciseScore'] = $score;
        //计算当前答题量数据
        $lastRecord = $userForecastModel->getLastRecord($userName,$subjectID);
        $all = $right+$wrong+$undo+$not;
        $forecastData['RightAmount'] = $lastRecord['RightAmount']+$right;
        $forecastData['WrongAmount'] = $lastRecord['WrongAmount']+$wrong;
        $forecastData['UndoAmount'] = $lastRecord['UndoAmount']+$undo;
        $forecastData['NotAmount'] = $lastRecord['NotAmount']+$not;
        $forecastData['AllAmount'] = $lastRecord['AllAmount'] + $all;
        $forecastData['LoadTime'] = time();
        $userForecastModel->insertData(
            $forecastData
        );
    }

    /**
     * 【注意】zj_user_test_kls表的TestKlID暂时没有，没有写入数据，之后如果确定没有用，则删除此字段2014年12月5日
     * @param $subjectID
     * @param $userName
     * @param $testRecordID
     * @author demo 5.5.27
     */
    private function userKlsAbility($subjectID,$userName,$testRecordID){

        $isHomework = false;
        $testRecord = $this->userAnswerRecordModel->getKlInfoByID($testRecordID,$isHomework);
        //1.1得到本次测试的所有试题数据（kl为键的数组，包含所有的未作答 无法判断 正确 错误）$testRecord['klTest'];
        //1.2得到本次测试的所有试题的知识点，用于之后根据这些知识点搜索最近的试题$testRecord['klList'];

        //2.根据本次测试试题的知识点获得这些知识点最近500道测试的有效试题数据（难度，正确错误，猜测等），数据中的试题知识点和本次测试的相同
        //3第二步500道试题中的对应试题需要知识点3级结构来合并子知识点试题,计算能力值
        //4.遍历能力值写入数据
        $arr=array(
            'userName'=>$userName,
            'klList'=>$testRecord['klList'],
            'klTest'=>$testRecord['klTest'],
            'isHomework'=>$isHomework,
            'subjectID'=>$subjectID,
            'testRecordID'=>$testRecordID);
        $this->setKlData($arr);
        $arr=array(
            'userName'=>$userName,
            'klList'=>$testRecord['skillList'],
            'klTest'=>$testRecord['skillTest'],
            'isHomework'=>$isHomework,
            'subjectID'=>$subjectID,
            'testRecordID'=>$testRecordID);
        $this->setSkillData($arr);
        $arr=array(
            'userName'=>$userName,
            'klList'=>$testRecord['capacityList'],
            'klTest'=>$testRecord['capacityTest'],
            'isHomework'=>$isHomework,
            'subjectID'=>$subjectID,
            'testRecordID'=>$testRecordID);
        $this->setCapacityData($arr);
    }

    /**
     * 写入知识点数据
     * @author demo 5.5.20
     */
    public function setKlData($arr){
        extract($arr);
        $testKlModel = $this->getModel('UserTestKl');
        $testKlsModel = $this->getModel('UserTestKls');
        //2.根据本次测试试题的知识点获得这些知识点最近500道测试的有效试题数据（难度，正确错误，猜测等），数据中的试题知识点和本次测试的相同
        $kls = $this->userAnswerRecordModel->getKlsByID($userName,$klList,$isHomework);
        //3第二步500道试题中的对应试题需要知识点3级结构来合并子知识点试题,计算能力值
        $abilityAndAmount = $this->getAbilityAndAmount($subjectID,$klTest,$kls);
        $abilityData = $abilityAndAmount['abilityData'];
        $amountData = $abilityAndAmount['amountData'];
        //4.遍历能力值写入数据
        $testKls = $testKl = [
            'SubjectID'=>$subjectID,
            'UserName'=>$userName,
            'TestRecordID'=>$testRecordID,
            'LoadTime'=>time(),
        ];
        foreach($abilityData as $i=>$iAbilityData){//$i 知识点ID
            //4.1更新（新增）test_kl表数据【重要】如果是更新数据，能力值可以直接更新，数量需要累加
            $testKl['KlID'] = $testKls['KlID'] = $i;
            $testKl['KlAbility'] = $testKls['KlAbility'] = $iAbilityData;
            $testKl['RightAmount'] = count(array_unique($amountData[$i]['right']));
            $testKl['WrongAmount'] = count(array_unique($amountData[$i]['wrong']));
            $testKl['UndoAmount'] = count(array_unique($amountData[$i]['undo']));
            $testKl['NotAmount'] = count(array_unique($amountData[$i]['not']));
            $testKl['AllAmount'] = $testKl['RightAmount']+$testKl['WrongAmount']+$testKl['UndoAmount']+$testKl['NotAmount'];
            $testKlModel->updateAbilityAmount($subjectID,$userName,$testKl['KlID'],$testKl);//更新数据，没有则插入数据
            //4.2新增work_kls表数据
            $testKlsModel->insertData(
                $testKls
            );
        }
    }
    /**
     * 写入技能数据
     * @author demo 5.5.20
     */
    public function setSkillData($arr){
        extract($arr);
        $testSkillModel = $this->getModel('UserTestSkill');
        //2.根据本次测试试题的知识点获得这些知识点最近500道测试的有效试题数据（难度，正确错误，猜测等），数据中的试题知识点和本次测试的相同
        $kls = $this->userAnswerRecordModel->getSkillByID($userName,$skillList,$isHomework);
        //3第二步500道试题中的对应试题需要知识点3级结构来合并子知识点试题,计算能力值
        $abilityAndAmount = $this->getAbilityAndAmountSkill($subjectID,$skillTest,$kls);
        $abilityData = $abilityAndAmount['abilityData'];
        $amountData = $abilityAndAmount['amountData'];
        //4.遍历能力值写入数据
        $testSkill = [
            'SubjectID'=>$subjectID,
            'UserName'=>$userName,
            'TestRecordID'=>$testRecordID,
            'LoadTime'=>time(),
        ];
        foreach($abilityData as $i=>$iAbilityData){//$i 知识点ID
            //4.1更新（新增）test_kl表数据【重要】如果是更新数据，能力值可以直接更新，数量需要累加
            $testSkill['SkillID'] = $i;
            $testSkill['SkillAbility'] = $iAbilityData;
            //4.2新增work_kls表数据
            $testSkillModel->insertData(
                $testSkill
            );
        }
    }
    /**
     * 写入能力数据
     * @author demo 5.5.20
     */
    public function setCapacityData($arr){
        extract($arr);
        $testCapacityModel = $this->getModel('UserTestCapacity');
        //2.根据本次测试试题的知识点获得这些知识点最近500道测试的有效试题数据（难度，正确错误，猜测等），数据中的试题知识点和本次测试的相同
        $kls = $this->userAnswerRecordModel->getCapacityByID($userName,$capacityList,$isHomework);
        //3第二步500道试题中的对应试题需要知识点3级结构来合并子知识点试题,计算能力值
        $abilityAndAmount = $this->getAbilityAndAmountCapacity($subjectID,$capacityTest,$kls);
        $abilityData = $abilityAndAmount['abilityData'];
        $amountData = $abilityAndAmount['amountData'];
        //4.遍历能力值写入数据
        $testCapacity = [
            'SubjectID'=>$subjectID,
            'UserName'=>$userName,
            'TestRecordID'=>$testRecordID,
            'LoadTime'=>time(),
        ];
        foreach($abilityData as $i=>$iAbilityData){//$i 知识点ID
            //4.1更新（新增）test_kl表数据【重要】如果是更新数据，能力值可以直接更新，数量需要累加
            $testCapacity['CapacityID'] = $i;
            $testCapacity['CapacityAbility'] = $iAbilityData;
            //4.2新增work_kls表数据
            $testCapacityModel->insertData(
                $testCapacity
            );
        }
    }


    /**
     * 计算学生作业能力值，写入user_work_forecast表数据
     * @param int $subjectID 学科ID
     * @param int $classID 班级ID
     * @param int $userID 用户ID
     * @param int $sendID 作业测试记录ID
     * @param int $right 正确数量
     * @param int $wrong 错误数量
     * @param int $undo 未做数量
     * @param int $not 无法判断正误数量
     * @author demo 5.5.20
     */
    private function workStudentAbility($subjectID,$classID,$userID,$sendID,$right,$wrong,$undo,$not){
        $workForecastModel = $this->getModel('UserWorkForecast');
        $workForecastData = [];//需要插入userWorkForecast表的数据
        $workForecastData['SubjectID'] = $subjectID;
        $workForecastData['ClassID'] = $classID;
        $workForecastData['UserID'] = $userID;
        $workForecastData['SendID'] = $sendID;
        //判断是否需要生成新的能力值
        if($workForecastModel->ifAbility($userID,$subjectID)){
            //需要生成能力值
            $lastSendID = $workForecastModel->getLastAbility($userID, $subjectID)['SendID'];//最新一次生成能力值的sendID
            $lastSendID = $lastSendID?$lastSendID:0;
            $sendIDs = $workForecastModel->getSendIDs($userID,$subjectID,$lastSendID);
            $testArray = $this->userAnswerRecordModel->getTestInRecords($sendIDs,$isWork = true);//用于计算能力值的试题作答数据
            //如果$testArray计算为空，则下面的能力值和分数计算为null和-1
            $workForecastData['Ability'] = $this->getAbilityByTestArray($testArray);
        }else{
            //不需要生成能力值
            $workForecastData['Ability'] = null;
        }
        //计算当前答题量数据
        $lastRecord = $workForecastModel->getLastRecord($userID,$subjectID);
        $all = $right+$wrong+$undo+$not;
        $workForecastData['RightAmount'] = $lastRecord['RightAmount']+$right;
        $workForecastData['WrongAmount'] = $lastRecord['WrongAmount']+$wrong;
        $workForecastData['UndoAmount'] = $lastRecord['UndoAmount']+$undo;
        $workForecastData['NotAmount'] = $lastRecord['NotAmount']+$not;
        $workForecastData['AllAmount'] = $lastRecord['AllAmount'] + $all;
        $workForecastData['AddTime'] = time();
        $workForecastModel->insertData(
            $workForecastData
        );
    }

    /**
     * 计算知识点能力值并写入
     * 【重要】本函数主要难度在于遍历三层知识点，合并每一层的试题，获取上次试题的数据（统计和能力值）
     * 【注意】不能统计没有做的知识点
     * @param int $subjectID 学科ID
     * @param int $classID 班级ID
     * @param int $userID 用户ID
     * @param int $sendID 作业测试记录ID
     * @param int $workID 作业ID
     * @author demo 5.5.21
     */
    private function workKlsAbility($subjectID,$classID,$userID,$sendID,$workID){
        $workKlModel = $this->getModel('UserWorkKl');
        $workKlsModel = $this->getModel('UserWorkKls');
        $isHomework = true;
        $testRecord = $this->userAnswerRecordModel->getKlInfoByID($sendID,$isHomework);
        //1.1得到本次测试的所有试题数据（kl为键的数组，包含所有的未作答 无法判断 正确 错误）
        $klTest = $testRecord['klTest'];
        //1.2得到本次测试的所有试题的知识点，用于之后根据这些知识点搜索最近的试题
        $klList = $testRecord['klList'];
        //2.根据本次测试试题的知识点获得这些知识点最近500道测试的有效试题数据（难度，正确错误，猜测等），数据中的试题知识点和本次测试的相同
        $kls = $this->userAnswerRecordModel->getKlsByID($userID,$klList,$isHomework);
        //3第二步500道试题中的对应试题需要知识点3级结构来合并子知识点试题,计算能力值
        $abilityAndAmount = $this->getAbilityAndAmount($subjectID,$klTest,$kls);
        $abilityData = $abilityAndAmount['abilityData'];
        $amountData = $abilityAndAmount['amountData'];
        //4.遍历能力值写入数据
        $workKls = $workKl = [
            'SubjectID'=>$subjectID,
            'ClassID'=>$classID,
            'UserID'=>$userID,
            'SendID'=>$sendID,
            'AddTime'=>time(),
        ];
        $workKls['WorkID'] = $workID;
        if($abilityData&&is_array($abilityData)){
            foreach($abilityData as $i=>$iAbilityData){//$i 知识点ID
                //4.1更新（新增）work_kl表数据【重要】如果是更新数据，能力值可以直接更新，数量需要累加
                $workKl['KlID'] = $workKls['KlID'] = $i;
                $workKl['Ability'] = $workKls['Ability'] = $iAbilityData;
                $workKl['RightAmount'] = count(array_unique($amountData[$i]['right']));
                $workKl['WrongAmount'] = count(array_unique($amountData[$i]['wrong']));
                $workKl['UndoAmount'] = count(array_unique($amountData[$i]['undo']));
                $workKl['NotAmount'] = count(array_unique($amountData[$i]['not']));
                $workKl['AllAmount'] = $workKl['RightAmount']+$workKl['WrongAmount']+$workKl['UndoAmount']+$workKl['NotAmount'];
                $workKlModel->updateAbilityAmount($subjectID,$classID,$userID,$workKl['KlID'],$workKl);//更新数据，没有则插入数据
                //4.2新增work_kls表数据
                $workKlsModel->insertData(
                    $workKls
                );
            }
        }

    }

    /**
     * 3第二步500道试题中的对应试题需要知识点3级结构来合并子知识点试题,计算能力值
     * @param int $subjectID 学科ID
     * @param array $klTest 得到本次测试的所有试题数据（kl为键的数组，包含所有的未作答 无法判断 正确 错误）
     * @param array $kls 根据本次测试试题的知识点获得这些知识点最近500道测试的有效试题数据（难度，正确错误，猜测等），数据中的试题知识点和本次测试的相同
     * @return array 数据格式['abilityData'=>$abilityData,'amountData'=>$amountData]
     * @author demo 5.5.21
     */
    private function getAbilityAndAmount($subjectID,$klTest,$kls){
//        $kl1 = SS('klBySubject3')[$subjectID];
        $kl1 = $this->getApiCommon('Knowledge/klBySubject3')[$subjectID];
        $abilityData = [];//计算后的数据 知识点ID是键 值为能力值
        $amountData = [];//计算后的数据 知识点ID是键 值为不同作答情况的试题ID【注意】不是数量，是TestID,并且没有去重
        foreach($kl1 as $iKl1){
            $kl1Data = [];//第1层知识点数据 临时变量
            $kl1Amount = ['right'=>[],'wrong'=>[],'undo'=>[],'not'=>[]];//第1层试题作答情况统计，内容为TestID 临时变量
            $kl2 = $iKl1['sub'];
            if($kl2){//如果有子集
                foreach($kl2 as $jKl2){
                    $kl2Data = [];//第2层知识点数据 临时变量
                    $kl2Amount = ['right'=>[],'wrong'=>[],'undo'=>[],'not'=>[]];//第2层试题作答情况统计，内容为TestID 临时变量
                    $kl3 = $jKl2['sub'];
                    if($kl3){//如果有子集
                        foreach($kl3 as $hKl3){
                            if($kls[$hKl3['KlID']]){//计算能力值
                                $kl2Data = array_merge($kl2Data,$kls[$hKl3['KlID']]);//【重要】这里因为是字符串键，所以array_merge合并数组时去掉重复，不需要再做处理
                                $ability3 = $this->getAbilityByTestArray($kls[$hKl3['KlID']]);//第三级知识点的能力值
                                if($ability3!==null){
                                    $abilityData[$hKl3['KlID']] = $ability3;
                                }
                            }
                            if($klTest[$hKl3['KlID']]){//计算统计数量
                                if(!empty($klTest[$hKl3['KlID']]['right'])) $kl2Amount['right'] = array_merge($kl2Amount['right'],$klTest[$hKl3['KlID']]['right']);
                                if(!empty($klTest[$hKl3['KlID']]['wrong'])) $kl2Amount['wrong'] = array_merge($kl2Amount['wrong'],$klTest[$hKl3['KlID']]['wrong']);
                                if(!empty($klTest[$hKl3['KlID']]['undo'])) $kl2Amount['undo'] = array_merge($kl2Amount['undo'],$klTest[$hKl3['KlID']]['undo']);
                                if(!empty($klTest[$hKl3['KlID']]['not'])) $kl2Amount['not'] = array_merge($kl2Amount['not'],$klTest[$hKl3['KlID']]['not']);
                                $amountData[$hKl3['KlID']] = $klTest[$hKl3['KlID']];
                            }
                        }
                    }else{//如果没有子集
                        $kl2Data = $kls[$jKl2['KlID']];
                        $kl2Amount = $klTest[$jKl2['KlID']];
                    }

                    if($kl2Data){//有数据的时候计算能力值并且赋值$ability
                        //第2级计算能力值
                        $ability2 = $this->getAbilityByTestArray($kl2Data);
                        if($ability2!==null){
                            $abilityData[$jKl2['KlID']] = $ability2;
                        }
                        //第2级生成第一级计算能力值所需数据
                        $kl1Data = array_merge($kl1Data,$kl2Data);//【重要】这里因为是字符串键，所以array_merge合并数组时去掉重复，不需要再做处理
                    }
                    if((count($kl2Amount['right'])+count($kl2Amount['wrong'])+count($kl2Amount['undo'])+count($kl2Amount['not']))>0){
                        //第2级统计计算
                        $amountData[$jKl2['KlID']] = $kl2Amount;
                        //第2级统计生成第1级所需数据
                        if(!empty($kl2Amount['right'])) $kl1Amount['right'] = array_merge($kl1Amount['right'],$kl2Amount['right']);
                        if(!empty($kl2Amount['wrong'])) $kl1Amount['wrong'] = array_merge($kl1Amount['wrong'],$kl2Amount['wrong']);
                        if(!empty($kl2Amount['undo'])) $kl1Amount['undo'] = array_merge($kl1Amount['undo'],$kl2Amount['undo']);
                        if(!empty($kl2Amount['not'])) $kl1Amount['not'] = array_merge($kl1Amount['not'],$kl2Amount['not']);
                    }
                }
            }else{//如果没有子集
                $kl1Data = $kls[$iKl1['KlID']];
                $kl1Amount = $klTest[$iKl1['KlID']];
            }
            if($kl1Data){//有数据的时候计算能力值并且赋值$ability
                $ability1 = $this->getAbilityByTestArray($kl1Data);
                if($ability1){
                    $abilityData[$iKl1['KlID']] = $ability1;
                }
            }
            if((count($kl1Amount['right'])+count($kl1Amount['wrong'])+count($kl1Amount['undo'])+count($kl1Amount['not']))>0){
                $amountData[$iKl1['KlID']] = $kl1Amount;
            }
        }
        $abilityAndAmount = ['abilityData'=>$abilityData,'amountData'=>$amountData];
        return $abilityAndAmount;
    }
    /**
     * 3第二步500道试题中的对应试题需要技能1级结构来合并子知识点试题,计算能力值
     * @param int $subjectID 学科ID
     * @param array $skillTest 得到本次测试的所有试题数据（kl为键的数组，包含所有的未作答 无法判断 正确 错误）
     * @param array $kls 根据本次测试试题的知识点获得这些知识点最近500道测试的有效试题数据（难度，正确错误，猜测等），数据中的试题知识点和本次测试的相同
     * @return array 数据格式['abilityData'=>$abilityData,'amountData'=>$amountData]
     * @author demo 5.5.21
     */
    private function getAbilityAndAmountSkill($subjectID,$skillTest,$kls){
//        $kl1 = SS('klBySubject3')[$subjectID];
        $kl1 = SS('SkillBySubject3')[$subjectID];
        $abilityData = [];//计算后的数据 知识点ID是键 值为能力值
        $amountData = [];//计算后的数据 知识点ID是键 值为不同作答情况的试题ID【注意】不是数量，是TestID,并且没有去重
        foreach($kl1 as $iKl1){
            $kl1Data = [];//第1层知识点数据 临时变量
            $kl1Amount = ['right'=>[],'wrong'=>[],'undo'=>[],'not'=>[]];//第1层试题作答情况统计，内容为TestID 临时变量
            $kl1Data = $kls[$iKl1['SkillID']];
            $kl1Amount = $skillTest[$iKl1['SkillID']];
            if($kl1Data){//有数据的时候计算能力值并且赋值$ability
                $ability1 = $this->getAbilityByTestArray($kl1Data);
                if($ability1){
                    $abilityData[$iKl1['SkillID']] = $ability1;
                }
            }
            if((count($kl1Amount['right'])+count($kl1Amount['wrong'])+count($kl1Amount['undo'])+count($kl1Amount['not']))>0){
                $amountData[$iKl1['SkillID']] = $kl1Amount;
            }
        }
        $abilityAndAmount = ['abilityData'=>$abilityData,'amountData'=>$amountData];
        return $abilityAndAmount;
    }
    /**
     * 3第二步500道试题中的对应试题需要技能1级结构来合并子知识点试题,计算能力值
     * @param int $subjectID 学科ID
     * @param array $capacityTest 得到本次测试的所有试题数据（kl为键的数组，包含所有的未作答 无法判断 正确 错误）
     * @param array $kls 根据本次测试试题的知识点获得这些知识点最近500道测试的有效试题数据（难度，正确错误，猜测等），数据中的试题知识点和本次测试的相同
     * @return array 数据格式['abilityData'=>$abilityData,'amountData'=>$amountData]
     * @author demo 5.5.21
     */
    private function getAbilityAndAmountCapacity($subjectID,$capacityTest,$kls){
//        $kl1 = SS('klBySubject3')[$subjectID];
        $kl1 = SS('CapacityBySubject3')[$subjectID];
        $abilityData = [];//计算后的数据 知识点ID是键 值为能力值
        $amountData = [];//计算后的数据 知识点ID是键 值为不同作答情况的试题ID【注意】不是数量，是TestID,并且没有去重
        foreach($kl1 as $iKl1){
            $kl1Data = [];//第1层知识点数据 临时变量
            $kl1Amount = ['right'=>[],'wrong'=>[],'undo'=>[],'not'=>[]];//第1层试题作答情况统计，内容为TestID 临时变量
            $kl1Data = $kls[$iKl1['CapacityID']];
            $kl1Amount = $capacityTest[$iKl1['CapacityID']];
            if($kl1Data){//有数据的时候计算能力值并且赋值$ability
                $ability1 = $this->getAbilityByTestArray($kl1Data);
                if($ability1){
                    $abilityData[$iKl1['CapacityID']] = $ability1;
                }
            }
            if((count($kl1Amount['right'])+count($kl1Amount['wrong'])+count($kl1Amount['undo'])+count($kl1Amount['not']))>0){
                $amountData[$iKl1['CapacityID']] = $kl1Amount;
            }
        }
        $abilityAndAmount = ['abilityData'=>$abilityData,'amountData'=>$amountData];
        return $abilityAndAmount;
    }

    /**
     * 由试题作答情况获取能力值
     * @param $array array 要求有试题 Diff IfChoose IfRight 属性
     * @return float 能力值
     * @author demo 5.5.21
     */
    private function getAbilityByTestArray($array) {
        $diff = $ifRight = $c = [];
        foreach ($array as $i => $iArray) {
            //处理条件：1、单选和多选2、能判断正确错误的试题3、Diff不为空
            if ($iArray['IfChoose'] > 1 && $iArray['IfRight'] > 0 && $iArray['Diff']) {
                $diff[$i] = $iArray['Diff'];
                $ifRight[$i] = $iArray['IfRight'] == 2 ? 1 : 0;//if_right 取值：1正确 0错误未作答
                $c[$i] = $iArray['IfChoose'] == 3 ? 0.25000 : 0.09091;
            }
        }
        return $this->getModel('UserForecast')->getAbilityValue($diff, $ifRight, $c);
    }

}