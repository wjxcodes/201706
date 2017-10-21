<?php
/**
 * @author demo 
 * @date 2014年8月4日
 * @update 2015年1月15日
 */
/**
 * 试题入库类，用于处理入库试题相关操作
 */
namespace Test\Model;
class TestRealModel extends BaseModel
{
    /**
     * 查询试题总数；
     * @author demo
     */
    public function getTotalRow() {
        return $this->selectCount(
            '1=1',
            'TestID');
    }
    /**
     * 试题索引
     * @param array $field 返回字段
     * @param array $where 条件 array('DocID'=>文档id（支持逗号间隔）,
     *                                         'TestID'=>试题id（支持逗号间隔）
     *                                          'maxtestid' => 不能超过试题id
     *                                          'testfilter' => 为1时则排除试题id
     *                                         'Diff'=>难度（数据类型1-5五段）
     *                                         'DocTypeID'=>文档类型
     *                                         'TestNum'=>小题数
     *                                         'TestStyle'=>试题类型
     *                                         'TypesID'=>题型id（支持逗号间隔）
     *                                         'TypeFilter'=>题型id排除，为1时排除TypesID的题型
     *                                         'SubjectID'=>学科id（支持逗号间隔）
     *                                         'SpecialID'=>专题id（支持逗号间隔）
     *                                         'GradeID'=>年级id（支持逗号间隔）
     *                                         'KlID'=>知识点id（支持逗号间隔）
     *                                         'ChapterID'=>章节id（支持逗号间隔）
     *                                         'LastTime'=>按时间查询（正数 时间--当前；负数 0-时间）
     *                                         'key'=>按字符串进行查询
     *                                         'width'=>试题的内容选项宽度
     *                                         'field'=>试题需要查询的字段 必须有key
     *                                         'searchStyle'=>查询类型 如果有key 默认any任何关键字匹配 normal全部关键字匹配
     *                                         'Duplicate'=>重复字段 0不重复
     *                                         'ShowWhere'=>0组卷端 1通用 2提分端 3前台禁用
     *                                         );
     * @param array $order 排序 array('字段1 DESC','字段2 ASC',...)
     * @param array $page 分页信息 array('page'=>当前页,'perpage'=>'每页几个');
     * @return array 返回数组    array(0=>试题数据集,1=>总数量,2=>每页数量);
     *         可用于返回数据数组
     *         array（
     *             'testid'=>试题id
     *             'docid'=>所属文档id
     *             'typeid'=>所属文档类型id
     *             'docname'=>所属文档名称
     *             'docyear'=>所属文档年份
     *             'numbid'=>所属文档试题编号
     *             'typesid'=>所属题型id
     *             'typesname'=>所属题型名称
     *             'subjectid'=>学科id
     *             'subjectname'=>学科名字
     *             ‘specialid'=>专题id
     *             ‘specialname'=>专题名字 //未做
     *             'test'=>题文被table分割选项后的字符串并且序号化
     *             'testold'=>题文被table分割选项后的字符串并且标签化
     *             'testnormal'=>题文未被处理
     *             'answer'=>答案序号化
     *             'answerold'=>答案标签化
     *             'answernormal'=>答案未被处理
     *             'analytic'=>解析序号化
     *             'analyticold'=>解析标签化
     *             'analyticnormal'=>解析未被处理
     *             'remark'=>解析序号化
     *             'remarkold'=>解析标签化
     *             'remarknormal'=>解析未被处理
     *             'firstloadtime'=>第一次入库时间（格式 ：年/月/日）
     *             'firstloadtimeint'=>第一次unix入库时间
     *             'loadtime'=>最近一次入库时间（格式 ：年/月/日）
     *             'loadtimeint'=>最近一次unix入库时间
     *             'testnum'=>小题数量
     *             'diff'=>难度值（3为小数）
     *             'diffid'=>难度id（1-5共五档）
     *             'diffstar'=>难度数据段标示（例如：0.001-0.300）
     *             'diffname'=>难度名称
     *             'diffxing'=>难度html星星标示（需要css）
     *             'mark'=>打分细则
     *             'kllist'=>知识点列表带知识点视频
     *             'klnameall'=>知识点名称路径
     *             'klnameonly'=>知识点名称
     *             'ifchoose'=>试题类型（0非选择题 1复合体 2多选 3单选）
     *             'times'=>试题下载次数
     *             'admin'=>管理员
     *             'specialname'=>专题名称
     *             'chapternameall'=>章节名称路径
     *             'gradename'=>年级
     *             'TestStyle'=>试题类型
     *             'OptionWidth'=>选项宽度
     *             'OptionNum'=>选项数量
     *         ）；
     * @author demo
     **/
    public function getTestIndex($field,$where,$order,$page,$openBackIndex=0){
        $index=$this->getModel('Index');
        $index->initTest($openBackIndex);
        return $index->getTestIndex($field,$where,$order,$page);
    }

    /**
     * 删除试题id索引
     * @param int $testID 试题id 支持以逗号间隔
     * @author demo
     */
    public function deleteIndex($testID){
        $index=$this->getModel('Index');
        $index->initTest();
        $index->deleteIndex($testID, array(2), array('status'));
    }

    /**
     * 通过索引和查表获取试题数据及作答信息
     * 供getTestAnswerByIndex和getHomeworkByIndex调用
     * @param array $testIDArray 试题ID数组；值字符串类型的TestID，可能包含c的
     * @param int $recordID TestRecordID或者SendID【根据IfHomework参数而不同，却分作业和非作业】
     * @param bool $ifHomework 是否是作业 如果是，$recordID表示SendID字段
     * @return array $resultTest 试题数据
     * @author demo
     */
    private function getTestData($testIDArray,$recordID=0,$ifHomework=false, $where=array()){
        $testIDString = implode(',',$testIDArray);
        //根据试题ID获取索引中的试题信息----------------------------------------------------------------------------------
//        $testQuery = getStaticFunction('TestQuery','getInstance');
//        $testQuery->setParams([
//            'field' => ['testid', 'testnormal', 'answernormal', 'analyticnormal', 'docname', 'docid', 'subjectid','typesid', 'diffid','klid', 'kllist', 'ifchoose','optionnum','testnum'],
//            'page' => ['page'=>1,'perpage'=>500],
//            'limit' =>500,
//            'convert' => 'testid'
//        ],$testIDString);
//        $testIndex = $testQuery->getResult($division =  false);//0 试题数组 1总数量 2每页数量

        $field = array('testid', 'testnormal', 'answernormal', 'analyticnormal', 'docname', 'docid', 'subjectid','typesid', 'diffid','klid', 'kllist', 'ifchoose','optionnum','testnum','judge','score');

        $where = array('TestID'=>$testIDString) + $where;
        $page=array('page'=>1,'perpage'=>500);
        $order=array();
        $testIndex = $this->getModel('TestRealQuery')->getIndexTest($field,$where,$order,$page,0,2);

        //索引获取试题出错
        if (!$testIndex) {
            return false;
        }

        //对查出来的数据进行排序，按照试题id参数对应顺序
        $tmp=array();
        foreach($testIDArray as $iTestIDArray){
            $tmp[$iTestIDArray]=$testIndex[$iTestIDArray];
        }
        $tmp=array_filter($tmp);
        $testIndex=$tmp;

//        //获取试题中的所有复合题的试题ID用于后续数据库查找复合题信息；需要两个数组区分校本题库和系统题库----------------------
//        $judgeIDsArray = [];//复合题ID的数组
//        foreach ($testIndex[0] as $iTestIndex) {//目的是区分两种试题类型，去分别查询数据库
//            if ($iTestIndex['ifchoose'] == 1) {
//                $judgeIDsArray[] = $iTestIndex['testid'];
//            }
//        }
//        $testJudgeDb = $this->getJudgeByIDs($judgeIDsArray);
        //获取试题作答情况-----------------------------------------------------------------------------------------------
        if($ifHomework === true){
            $field = 'SendID';
        }else{
            $field = 'TestRecordID';
        }

        if(!empty($recordID)){
            $answerWhere=$field.'='.$recordID;
            if(count($testIDArray)==1) $answerWhere.= ' AND TestID='.$testIDString;
            $answerDb = $this->getModel('UserAnswerRecord')->selectData(
                'TestID,AnswerID,AnswerText,IfRight,Number,OrderID,IfChoose,TestType,Score',
                $answerWhere,
                'Number asc,OrderID asc');//这里可以同时查询，因为是通过recordID查询的
            $answerData = [];//用户答题数据 两维数组 键为字符串类型的testID以及orderID
            if ($answerDb) {//如果用户有作答数据
                $host=C('WLN_DOC_HOST');
                foreach ($answerDb as $iAnswerDb) {
                    if($iAnswerDb['TestType'] == 2){//2 - 校本题库试题
                        $testID = \Test\Model\TestQueryModel::DIVISION.$iAnswerDb['TestID'];
                    }else{
                        $testID = $iAnswerDb['TestID'];
                    }
                    $iAnswerDb['AnswerText']=str_replace('{#$DocHost#}',$host,$iAnswerDb['AnswerText']);
                    //根据testid获取对应试题的number

                    $iAnswerDb['Number']=array_search($iAnswerDb['TestID'], $testIDArray) + 1;
                    $answerData[$testID][$iAnswerDb['OrderID']] = $iAnswerDb;
                    $answerData[$testID][$iAnswerDb['OrderID']]['AnswerText'] = stripslashes_deep($iAnswerDb['AnswerText']);
                }
            }
        }

        //处理所有获取的数据---------------------------------------------------------------------------------------------
        $resultTest = [];//最终需要返回的数据
        foreach ($testIndex as $i => $iTestIndex) {
            //处理转移和图片问题
            if(strpos($i,'c')===false){//系统试题

            }else{//自建个人试题
                $iTestIndex['testnormal'] = stripslashes_deep($iTestIndex['testnormal']);
                $iTestIndex['answernormal'] = stripslashes_deep($iTestIndex['answernormal']);
                $iTestIndex['analyticnormal'] = stripslashes_deep($iTestIndex['analyticnormal']);
            }
            $testID = $iTestIndex['testid'];//字符串类型的试题ID
            $doAnswer = [];//结构[orderID=>['Number'=>....]]
            //【1】处理单选和多选
            if ($iTestIndex['ifchoose'] == 2 || $iTestIndex['ifchoose'] == 3) {
                $resultTest[$i]['Test'] = $this->_splitSelectTest($iTestIndex['testnormal'],$iTestIndex['optionnum']);
                //答题情况的统计：如果AnswerRecord中没有记录，则表示试题没有作答
                if ($answerData[$testID]) {
                    $doAnswer = $answerData[$testID];
                } else {//用户未作答
                    //这里需要查询得到Number OrderID IfChoose IfRight=-1表示未作答这里
                    $doAnswer = [
                        0 => [
                            'Number' => array_search($testID, $testIDArray) + 1,
                            'OrderID' => 0,
                            'IfChoose' => $iTestIndex['ifchoose'],
                            'IfRight' => -1,
                            'AnswerText' => '']
                    ];
                }
            } elseif ($iTestIndex['ifchoose'] == 1) {
                //【2】处理复合题
                $optionNum = explode(',',$iTestIndex['optionnum']);//sub中选项数量array
                $optionNumArr = [];//选项数量array，和$optionNum的区别是数组键是从1开始
                foreach($optionNum as $j=>$jOptionNum){
                    $optionNumArr[$j+1] = $jOptionNum;
                }
                $testNum = $iTestIndex['testnum'];//小题数量int
                $testJudgeData = $iTestIndex['judge'];//复合题小题数据，包含TestID,OrderID,IfChoose数据
                //容错1：//如果复合题数据错误judge表数据错误，则此试题不显示
                if(!$testJudgeData){
                    $errorLog = [
                        'msg' => '此试题被判断为复合题但是Judge表中没有数据，试题ID:'.$testID,
                        'sql' => '空',
                        'description' => 'ifChoose错误或者小题数据缺失'
                    ];
                    $this->unionSelect('addErrorLog',$errorLog);
                    continue;//跳过 试题不显示
                }
                $resultTest[$i]['TestNum'] = $testNum;
                $resultTest[$i]['Test'] = $this->_splitMixedTest($iTestIndex['testnormal'],$testJudgeData,$optionNumArr,$testNum);
                //答题情况的统计：如果AnswerRecord中没有记录，则表示试题没有作答
                foreach ($testJudgeData as $j=>$jTestJudgeData) {
                    $orderID = $jTestJudgeData['OrderID'];//小题序号
                    //容错2：如果复合题小题的ifChoose判断错误，则该小题不显示
                    if($jTestJudgeData['IfChoose']==0&&$optionNumArr[$orderID]!=0){
                        $errorLog = [
                            'msg' => '此复合题的小题被判断为非选项题但是有选项，试题ID:'.$testID.' 小题序号:'.$orderID,
                            'sql' => '空',
                            'description' => 'ifChoose错误或者小题数据缺失'
                        ];
                        $this->unionSelect('addErrorLog',$errorLog);
                        //Test.sub中的小题需要删除
                        unset($resultTest[$i]['Test']['sub'][$orderID]);//不需要重建数组索引
                        continue;//跳过 试题不显示
                    }
                    if ($answerData[$testID][$orderID]) {
                        $doAnswer[$orderID] = $answerData[$testID][$orderID];
                    } else {
                        //用户未作答
                        $doAnswer[$orderID] = [
                            'Number' => array_search($testID, $testIDArray) + 1,
                            'OrderID' => $orderID,
                            'IfChoose' => $jTestJudgeData['IfChoose'],
                            'IfRight' => -1,
                            'AnswerText' => ''
                        ];
                    }
                }
            } else {
                //【3】大题不用转换
                $resultTest[$i]['Test'] = ['title' => $iTestIndex['testnormal']];
                //答题情况的统计：如果AnswerRecord中没有记录，则表示试题没有作答
                if ($answerData[$testID]) {
                    $doAnswer = $answerData[$testID];
                } else {
                    //这里需要查询得到Number OrderID IfChoose IfRight=-1表示未作答这里
                    $doAnswer = [
                        0 => [
                            'Number' => array_search($testID, $testIDArray) + 1,
                            'OrderID' => 0,
                            'IfChoose' => $iTestIndex['ifchoose'],
                            'IfRight' => -1,
                            'AnswerText' => ''
                        ]
                    ];
                }
            }

            if(empty($resultTest[$i]['TestNum'])) $resultTest[$i]['TestNum'] = 1;
            $resultTest[$i]['doAnswer'] = $doAnswer;
            $resultTest[$i]['TestID'] = $testID;//字符串类型 包含c
            $resultTest[$i]['Answer'] = $iTestIndex['answernormal'];
            $resultTest[$i]['Analytic'] = $iTestIndex['analyticnormal'];
            $resultTest[$i]['DocName'] = $iTestIndex['docname'];
            $resultTest[$i]['DocID'] = $iTestIndex['docid'];
            $resultTest[$i]['DocName'] = $iTestIndex['docname'];
            $resultTest[$i]['DocID'] = $iTestIndex['docid'];
            $resultTest[$i]['TypesID'] = $iTestIndex['typesid'];
            $resultTest[$i]['DiffID'] = $iTestIndex['diffid'];
            $resultTest[$i]['KlList'] = $iTestIndex['kllist'];
            $resultTest[$i]['KlID'] = $iTestIndex['klid'];
            $resultTest[$i]['IfChoose'] = $iTestIndex['ifchoose'];
            $resultTest[$i]['SubjectID'] = $iTestIndex['subjectid'];
            $resultTest[$i]['Score'] = $iTestIndex['score'];
        }
        return $resultTest;
    }

    /**
     * 根据试题ID获取复合题信息
     * 参数中IDsArray不一定都要是复合题，如果没有找到返回空数组
     * @param array $IDsArray [1,2,'c1']试题ID数组，可以包含c校本题库的ID
     * @return array [[TestID][OrderID]=>[TestID,OrderID,IfChoose],...]
     * @author demo
     */
    public function getJudgeByIDs($IDsArray){
        $testIfChoose3 = [];//复合题数组，数组值为int型testID
        $testCIfChoose3 = [];//校本题库复合题数组，数组值为int型testID
        $testJudgeDb = [];//复合题的ifChoose数据,字符串类型的试题ID为键
        foreach ($IDsArray as $testID) {//目的是区分两种试题类型，去分别查询数据库
            if (strpos($testID, \Test\Model\TestQueryModel::DIVISION) !== false) {
                $testCIfChoose3[] = str_replace(\Test\Model\TestQueryModel::DIVISION, '', $testID);
            } else {
                $testIfChoose3[] = $testID;
            }
        }
        //获取复合体中小题的类型数据，获取后合并
        if ($testIfChoose3) {
            $testJudgeDbTmp = $this->getModel('TestJudge')->selectData(
                'TestID,OrderID,IfChoose',
                ['TestID' => ['IN', $testIfChoose3]]
            );
            foreach ($testJudgeDbTmp as $iTestJudgeDbTmp) {
                $testJudgeDb[$iTestJudgeDbTmp['TestID']][$iTestJudgeDbTmp['OrderID']] = $iTestJudgeDbTmp;
            }
        }
        if ($testCIfChoose3){
            $testJudgeDbTmp = $this->getModel('CustomTestJudge')->selectData(
                'TestID,OrderID,IfChoose',
                ['TestID' => ['IN', $testCIfChoose3]]
            );
            foreach ($testJudgeDbTmp as $iTestJudgeDbTmp) {
                $testJudgeDb[\Test\Model\TestQueryModel::DIVISION.$iTestJudgeDbTmp['TestID']][$iTestJudgeDbTmp['OrderID']] = $iTestJudgeDbTmp;
            }
        }
        return $testJudgeDb;
    }


    /**
     * 通过索引和数据库查询获取试题测试信息
     * @param int $testID 试题ID
     * @param int $recordId 答题记录id
     * @param int $isHomeWork 是否是作业
     * @param int $ifAnswer 是否返回答案
     * @return array 试题数据信息
     * @author demo
     */
    public function getTestByID($testID,$recordId=0,$isHomeWork=0,$ifAnswer=0) {
        if(!is_array($testID)) $testID=array($testID);
        $ifSend=false;
        if(!empty($isHomeWork)) $ifSend=true;
        $where=array('AatTestStyle'=>-1);
        $resultTest = $this->getTestData($testID,$recordId,$ifSend,$where);
        return $resultTest;
    }

    /**
     * 通过索引和数据库查询获取试题测试信息
     * @param int $recordId 测试记录ID
     * @param int $ifShowID 是否返回id号
     * @return array 整个测试的试题信息和作答信息
     * @author demo
     */
    public function getTestAnswerByIndex($recordId) {
        //查询测试记录------------------------------------------------------------------------
        $testRecordDb = $this->getModel('UserTestRecord')->findData(
            'LoadTime,Content,Score,RealTime,UserName,Style,SubjectID',
            'TestID=' . $recordId);
        //------------- begin 验证是否为打分试卷  16-4-27 ----------------
        $testRecordAttr = $this->getModel('UserTestRecordAttr');
        $isEval = $testRecordAttr->isEvalTest($recordId);
        $where = array();
        $evaluateDescription = $answerTitle = $topicName = $jumpUrl = '';
        if($isEval){
            $data = $testRecordAttr->getEvaluateDescription($recordId);
            $evaluateDescription = $data['EvaluateDescription'];
            $answerTitle = $data['AnswerTitle'];
            $topicName = $data['TopicName'];
            $jumpUrl = $data['JumpUrl'];
            $topicPaperID = $data['TopicPaperID'];
            //获取试卷对应的试题id
            $topBuffer=$this->getModel('TopicPaper')->findData('*','TopicPaperID='.$topicPaperID);
            if(empty($topBuffer['DocID'])){
                $testIdArray = explode(',', str_replace(' ','',$topBuffer['TestIDs']));
            }

            $where = array(
                'AatTestStyle' => 1
            );
        }
        //------------- end 验证是否为打分试卷 ----------------
        $docId = '';
        if(empty($testIdArray)) $testIdArray = explode(',', str_replace(' ','',$testRecordDb['Content']));
        $testIdArray=array_filter($testIdArray);
        $resultTest = $this->getTestData($testIdArray,$recordId,false,$where);


        //------------- begin 验证是否包含听力  16-4-23 ----------------
        $docId = $resultTest[$testIdArray[0]]['DocID'];
        $isContainHearing = false;
        if(!empty($docId))
            $isContainHearing = $this->getModel('DocHearing')->isContainHearing($docId);
        if($isContainHearing){
            $docId = U('AatApi/Exercise/downloadAudio', array('docId'=>$docId));
        }else{
            $docId = '';
        }
        //------------- end 验证是否包含听力----------------

        $resultRecord = array(
            'RealTime' => $testRecordDb['RealTime'],
            'Style' => $testRecordDb['Style'],
            'LoadTime' => $testRecordDb['LoadTime'],
            'Score' => $testRecordDb['Score'],
            'SubjectID' => $testRecordDb['SubjectID'],
            'IsContainHearing' => $docId,
            'AatTestStyle' => ($isEval ? 1 : 0),
            'EvaluateDescription' => $evaluateDescription,
            'AnswerTitle' => $answerTitle,
            'TopicName' => $topicName,
            'JumpUrl' => $jumpUrl
        );
        return $result = array('test' => $resultTest, 'record' => $resultRecord);
    }

    /**
     * 通过索引和数据库查询获取试题测试信息
     * @param int $sendID 作业测试记录ID
     * @return array 整个作业测试的试题信息和作答信息
     * @author demo 15.5.18
     */
    public function getHomeworkByIndex($sendID){
        //查询测试记录------------------------------------------------------------------------
        $testRecordDb=$this->unionSelect('userSendWorkSelectById',$sendID)[0];

        $docId = '';
        $testIDArray = explode(',',$testRecordDb['TestList']);
        $resultTest = $this->getTestData($testIDArray,$sendID,$isHomework = true);//获取试题数据
        if(!$resultTest){
            return false;
        }

        //------------- begin 验证是否包含听力  16-4-23 ----------------
        $docId = $resultTest[$testIDArray[0]]['DocID'];
        $isContainHearing = $this->getModel('DocHearing')->isContainHearing($docId);
        if($isContainHearing){
            $docId = U('AatApi/Exercise/downloadAudio', array('docId'=>$docId));
        }else{
            $docId = '';
        }

        //------------- end 验证是否包含听力 ----------------
        $cacheSubject = SS('subject');
        $resultRecord = array(
            'StartTime'=>$testRecordDb['StartTime'],
            'EndTime'=>$testRecordDb['EndTime'],
            'LoadTime'=>$testRecordDb['LoadTime'],
            'UserName'=>$testRecordDb['UserName'],
            'Message'=>$testRecordDb['Message'],
            'WorkOrder'=>$testRecordDb['WorkOrder'],
            'SubjectName'=>$cacheSubject[$testRecordDb['SubjectID']]['SubjectName'],
            'SendTime'=>$testRecordDb['SendTime'],
            'DoTime'=>$testRecordDb['DoTime'],
            'CheckTime'=>$testRecordDb['CheckTime'],
            'Comment'=>$testRecordDb['Comment'],
            'CorrectRate'=>$testRecordDb['CorrectRate'],
            'IsContainHearing' => $docId
        );
        return $result = ['test'=>$resultTest,'record'=>$resultRecord];
    }

    /**
     * 通过索引和数据库查询获取试题和知识的数据
     * @param int $sendID 作业测试记录ID
     * @return array 试题和知识的数据
     * @author demo 15.6.3
     */
    public function getHomeworkCase($sendID) {

    }

    /**
     * 描述：根据IDs获取知识和试题数据
     * @param string $IDStr 正常试题 不加字母；校本题库 用字母c开头；后台加的 知识用字母l开头；用户加的 知识用字母u开头
     * @param array $testField  索引需要查询的字段
     * @param int $sendID 作业ID，存在如果，则需要格式化试题部分（home false；aat true）
     * @return array 知识和试题的数组，键为IDs
     * @author demo
     */
    public function getCaseContentData($IDStr,$testField,$sendID=0) {
        $IDArr = explode(',',$IDStr);
        if(!$IDArr||!is_array($IDArr)){
            return null;
        }
        $testCustom = $loreCustom = $testSys = $loreSys = [];
        foreach($IDArr as $ID){
            if(strpos($ID,'c')!==false){
                $testCustom[] = $ID;
            }elseif(strpos($ID,'l')!==false){
                $loreSys[] = substr($ID,1);//因为要从数据库里查询，所以去掉l
            }elseif(strpos($ID,'u')!==false){
                $loreCustom[] = substr($ID,1);//因为要从数据库里查询，所以去掉u
            }else{
                $testSys[] = $ID;
            }
        }
        $testIndexData = $loreSysData = $loreCustomData = [];
        //查询自建和系统试题
        if($testCustom||$testSys){
            if($sendID){
                //处理练习；格式化复合题和选项
                $testIndexData = $this->getTestData(array_merge($testCustom,$testSys),$sendID,$ifHomework=true);
            }else{
                //不需要格式化数据
                if(!$testField){
                    $testField = ['testid', 'testnormal', 'answernormal', 'analyticnormal', 'docname', 'docid',
                        'typesid', 'diffid', 'klid', 'kllist', 'ifchoose','optionnum','testnum'];
                }
                //根据试题ID获取索引中的试题信息
                $testQuery = getStaticFunction('TestQuery','getInstance');
                $testQuery->setParams([
                    'field' => $testField,
                    'page' => ['page'=>1,'perpage'=>500],
                    'limit' =>500,
                    'convert' => 'testid'
                ],array_merge($testCustom,$testSys));
                $testIndexData = $testQuery->getResult($division =  false)[0];//0 试题数组 1总数量 2每页数量
                //索引获取试题出错
                if (!$testIndexData) {
                    return false;
                }
            }
        }
        //$host=C('WLN_DOC_HOST');
        //查询系统知识
        if($loreSys){
            $loreSysDb = $this->getModel('CaseLore')->selectData(
                'LoreID,Lore,Answer',
                ['LoreID'=>['in',$loreSys]]
            );

            $loreSysData = R('Common/TestLayer/reloadTestArr',array($loreSysDb,$key = 'LoreID',$prefix = 'l'));
            foreach($loreSysData as $i=>$iLoreSysData){
                $loreSysData[$i]['Lore'] = R('Common/TestLayer/strFormat',array($iLoreSysData['Lore']));
                $loreSysData[$i]['Answer'] = R('Common/TestLayer/strFormat',array($iLoreSysData['Answer']));
            }
        }
        //查询自建知识
        if($loreCustom){
            $loreCustomDb = $this->getModel('CaseCustomLore')->selectData(
                'LoreID,Lore,Answer',
                ['LoreID'=>['in',$loreCustom]]
            );
            $loreCustomData = R('Common/TestLayer/reloadTestArr',array($loreCustomDb,$key = 'LoreID',$prefix = 'u'));
            foreach($loreCustomData as $i=>$iLoreCustomerData){
                $loreCustomData[$i]['Lore'] = formatString('IPReturn',stripslashes_deep($iLoreCustomerData['Lore']));
                $loreCustomData[$i]['Answer'] = formatString('IPReturn',stripslashes_deep($iLoreCustomerData['Answer']));
            }
        }
        $result = $testIndexData+$loreSysData+$loreCustomData;//array_merge数字键会重新编号
        return $result;
    }

    /**
     * 分割选择题 ifChoose字段为2或者3
     * 如果options为数字不为数组，则直接输出title，options不排版，根据options值确定选项数量
     * @param string $str 题目字符串 Test字段
     * @param int $optionNum 选项数量，如果不能分割试题，则options为选项数量，前台根据此排版
     * @return array title和options（options为数组）
     * @author demo
     */
    public function _splitSelectTest($str,$optionNum) {
        //formatStrToArr分隔题干和各个选项
        $testArray = $this->getModel('Test')->formatStrToArr($str);
        if(!is_array($testArray)){
            //如果不能切割，$testArray返回字符串@容错处理
            $options = $optionNum?$optionNum:4;//如果不能得到optionNum，则默认4个选项
            $configOptions = C('WLN_OPTIONS');
            $optionArr = [];//选项数组
            for($i=1;$i<=$options;$i++){
                $optionArr[] = $configOptions[$i];
            }
            $result = ['title'=>$testArray,'options'=>$optionArr];
        }else{
            $result['title'] = $testArray[0];
            array_shift($testArray);//去掉$test_array[0],剩下的为选项数组
            $result['options'] = $testArray;
        }
        return $result;
    }

    /**
     * 分隔复合题并且根据针对完形填空格式化选项显示 调用_splitTest函数
     * @param string $str 题文字符串
     * @param array $ifChooseArr IfChoose数组 按小题分割 数组索引从0开始
     * @param array $optionNumArr 选项数量数组 按小题分割 数组索引从1开始
     * @param int $testNum 小题数量
     * @return array 包含title和options 复合题包含sub键
     * @author demo
     */
    public function _splitMixedTest($str, $ifChooseArr, $optionNumArr, $testNum) {
        //分隔小题
        $testModel = $this->getModel('Test');
        $test = $testModel->xtnum($str, 3);
        if (!$test) {
            //分隔失败(系统ifChoose判断错误或者没有小题标签的)@容错处理
            $result['title'] = $testModel->changeHZ(['Test'=>$str, 'Key'=>1]);
            for($i=1;$i<=$testNum;$i++){
                $order = $i;//小题序号
                if($ifChooseArr[$order-1] != 0){
                    //小题是选择题 手动生成选项
                    $options = $optionNumArr[$order] ? $optionNumArr[$order] : 4;//使用optionnum字段，如果字段也不存在，默认4个选项
                    $configOptions = C('WLN_OPTIONS');
                    $optionArr = [];//选项数组
                    for($j=1;$j<=$options;$j++){
                        $optionArr[] = $configOptions[$j];
                    }
                    $result['sub'][$order] = ['title' => '', 'options' => $optionArr];
                }else{
                    //小题是大题 手动生成
                    $result['sub'][$order]['title'] = '';
                }
            }
        } else {
            //处理完形填空 把【题号】转化为序号，序号从1开始
            $result['title'] = $testModel->changeHZ(['Test'=>$test[0], 'Key'=>1]);
            array_shift($test);//去掉题文
            foreach ($test as $i => $iTest) {
                $order = $i+1;//小题序号
                if ($ifChooseArr[$order-1] != 0) {
                    //小题是选择题
                    $subTestArr = $this->getModel('Test')->formatStrToArr($iTest);
                    if (is_array($subTestArr)) {
                        //分隔成功
                        $result['sub'][$order]['title'] = $subTestArr[0];
                        array_shift($subTestArr);
                        $result['sub'][$order]['options'] = $subTestArr;
                    } else {
                        //分隔错误@容错处理
                        $options = $optionNumArr[$order] ? $optionNumArr[$order] : 4;//使用optionnum字段，如果字段也不存在，默认4个选项
                        $configOptions = C('WLN_OPTIONS');
                        $optionArr = [];//选项数组
                        for ($m = 1; $m <= $options; $m++) {
                            $optionArr[] = $configOptions[$m];
                        }
                        $result['sub'][$order] = ['title' => $iTest, 'options' => $optionArr];
                    }
                } else {
                    //小题不是选择题
                    $result['sub'][$order]['title'] = $iTest;
                }
            }
        }
        return $result;
    }

    /**
     * 根据limit值获取某学科下的入库试题的zj_test_real表和zj_testdoc表的数据（题文、答案、解析）
     * @param string $subjectID 学科ID
     * @param string $startID 开始ID
     * @param string $size 查询得到数量
     * @return array
     * @author demo
     */
    public function getDataBySubject($subjectID,$startID,$size){
        $limit=($startID-1).','.$size;
        $db=$this->unionSelect('testRealSelectBySubjectIdLimit',$subjectID,$limit);
        return $db;
    }

    /**
     * 获取指定testID的试题数据，包括zj_test_real和zj_testdoc表中的题文、答案、解析
     * @param array $testIDs 需要查找的TestIDs
     * @return array
     * @author demo
     */
    public function getDataByTestIDs($testIDs){
        $db =$this->unionSelect('testRealSelectByTestIDs',$testIDs);
        return $db;
    }

    /**
     * 批量更新zj_test_real表和zj_testdoc表中的题文、答案、解析数据
     * 注意：参数数组中必须包含主键
     * @param array $data 需要更新的数据数组，必须包含主键例如：array(array('id'=>1,'name'=>'a'),array('id'=>2,'name'=>'b'))id为主键
     * @author demo
     */
    public function updateErrorData($data){
        $dataTestReal = [];
        $dataTestDoc = [];
        foreach($data as $iData){
            if($iData['Test']){
                $dataTestReal['TestID'] = $iData['TestID'];
                $dataTestReal['Test'] = $iData['Test'];
            }
            if($iData['Answer']){
                $dataTestReal['TestID'] = $iData['TestID'];
                $dataTestReal['Answer'] = $iData['Answer'];
            }
            if($iData['Analytic']){
                $dataTestReal['TestID'] = $iData['TestID'];
                $dataTestReal['Analytic'] = $iData['Analytic'];
            }
            if($iData['Remark']){
                $dataTestReal['TestID'] = $iData['TestID'];
                $dataTestReal['Remark'] = $iData['Remark'];
            }
            if($dataTestReal['TestID']){
                $this->updateData(
                    $dataTestReal,
                    'TestID='.$dataTestReal['TestID']
                );
            }
            if($iData['DocTest']){
                $dataTestDoc['TestID'] = $iData['TestID'];
                $dataTestDoc['DocTest'] = $iData['DocTest'];
            }
            if($iData['DocAnswer']){
                $dataTestDoc['TestID'] = $iData['TestID'];
                $dataTestDoc['DocAnswer'] = $iData['DocAnswer'];
            }
            if($iData['DocAnalytic']){
                $dataTestDoc['TestID'] = $iData['TestID'];
                $dataTestDoc['DocAnalytic'] = $iData['DocAnalytic'];
            }
            if($iData['DocRemark']){
                $dataTestDoc['TestID'] = $iData['TestID'];
                $dataTestDoc['DocRemark'] = $iData['DocRemark'];
            }
            if($dataTestDoc['TestID']){
                $this->getModel('TestDoc')->updateData(
                    $dataTestDoc,
                    array('TestID'=>$dataTestDoc['TestID'])
                );
            }

        }
    }

    /**
     * 待删除 用一个表就能解决
     * 根据试题编号和学科获取该试题在此学科下所有试题的排序位置（ASC位置）
     * @param int $testID 试题编号
     * @param int $subjectID 学科编号
     * @return int ASC模式的序号
     */
    public function getNoByTestID($testID,$subjectID){
        return $this->unionSelect('testRealCountByTestIdSubjectId',$testID,$subjectID);
    }
    /**
     * 注释待修改(标准化过来的) 来自DocsaveAction
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function selectTest($where){
        return $this->unionSelect('testRealSelectByWhere',$where);
    }

    /**
     * 试题去重
     * @param int $docid 试卷id
     * @param boolean $isStored 是否为入库排重
     * @param int $page 当前页数 默认为1
     * @param int $radix 每页显示数量
     * @return array
     * @author demo
     * @date 2015-6-25
     */
    public function getTestByDocId($docid, $isStored, $page=1, $radix=3){
        if(!$docid){
            return array();
        }
        // if((int)$page < 1){
        //     $page = 1;
        // }
        $count = 0;
        if($isStored){
            //对已入库的试题排重
            $list = $this->unionSelect('testDocAttrRealSelectByDocID',$docid);
            $count = count($list);
            // $page = page($count, $page, $radix);
            $pager = $page.','.$radix;
            $list = $this->unionSelect('testDocAttrRealSelectByDocID',$docid, $pager);
        }else{
            //对未入库的试题排重
            $list = $this->unionSelect('testAttrSelectByDocID',$docid);
            $count = count($list);
            // $page = page($count, $page, $radix);
            $pager = $page.','.$radix;
            $list = $this->unionSelect('testAttrSelectByDocID',$docid, $pager);
        }
        return array('countPage'=>$count, 'list'=>$list);
    }

    /**
     * 返回相似试题
     * @param array $data 从该数据中查找对应的相似题、
     * @param boolean $isStored 是否为已入库试题
     * @return array
     * @author demo
     * @date 2015-6-25
     */
    public function getSimilarities($data, $isStored){
        if(empty($data)){
            return array();
        }
        $weight=0.9;
        $field=array('testid','weight','test','docid','docname','duplicate');
        $order=array();
        $page=array('perpage'=>10);
        $where['Duplicate']=0;//去除重复数据
        $where['red']=0;
        //$host=C('WLN_DOC_HOST');
        set_time_limit(0);
        foreach($data as $key=>$value){
            $result=array();
            if($isStored) $where['maxtestid']=$value['TestID']-1;//是否截止到当前id

            $data[$key]['Test']=R('Common/TestLayer/strFormat',array($value['Test']));//试题路径

            //去掉试题题文HTML标签
            $where['key']=preg_replace('/<[^>]*>|　|&nbsp;/i','',$value['Test']);
            //关键字为空时，不进行相似题查找
            if(empty($where['key'])){
                continue;
            }
            //限定学科条件，增加匹配准确度
            $where['SubjectID']=$value['SubjectID'];
            $where['TypesID']=$value['TypesID'];
            if(empty($where['SubjectID']) || empty($where['TypesID'])){
                return array(false,30512,$value['TestID']);
            }

            $result=$this->getTestIndex($field,$where,$order,$page,1);//获取重复试题题文集
            if($result === false){
                return array(false);
            }
            $firstWeight=0;
            if(!empty($result[0][0])){
                //去除重复数据
                if($isStored){
                    $tmpTestID=array();
                    foreach ($result[0] as $jResult){
                        if($firstWeight==0){
                            $firstWeight=$jResult['weight']*$weight;
                            $tmpTestID[]=$jResult['testid'];
                        }else if($firstWeight<$jResult['weight']){
                            $tmpTestID[]=$jResult['testid'];
                        }
                    }
                    $buffer=$this->getModel('TestAttrReal')->selectData(
                        'TestID,Duplicate',
                        'TestID in ('.implode(',',$tmpTestID).')');
                    if($buffer){
                        //获取标记重复试题的试题id数组
                        $tmpTestID=array();
                        foreach ($buffer as $j=> $jBuffer){
                            if($jBuffer['Duplicate']) $tmpTestID[]=$jBuffer['TestID'];
                        }
                        //试题没有被标记过重复的试题数组
                        $i=0;
                        $tmpTestID2=array();
                        foreach ($result[0] as $j=>$jResult){
                            if(!in_array($jResult['testid'],$tmpTestID)){
                                $tmpTestID2[$i]=$jResult;
                                $i++;
                            }
                        }
                        $result[0]=$tmpTestID2;
                    }
                }else{
                    $firstWeight=$result[0][0]['weight']*$weight;
                }
                $data[$key]['duplicate'][0]=$result[0][0];
                if($result[0][1]['weight']>$firstWeight)
                    $data[$key]['duplicate'][1]=$result[0][1];
                unset($result[0][0]);
                unset($result[0][1]);
                if(!empty($result[0])){
                    foreach ($result[0] as $j=> $jResult){
                        if($jResult['weight']>$firstWeight){
                            $data[$key]['duplicate']['ids'][$j]['testid']=$jResult['testid'];//取得其他重复试题ID集
                            $data[$key]['duplicate']['ids'][$j]['duplicate']=$jResult['duplicate'];
                        }
                    }
                }
            }
        }
        return $data;
    }
}