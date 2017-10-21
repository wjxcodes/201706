<?php
/**
 * @author demo
 * @date 2015年12月28日
 */

namespace Aat\Api;

class PushTestApi extends BaseApi
{
    /**
     * 描述：获取知识点两级属性结构的数据，包含用户的能力值和作答数据
     * @param $username
     * @param $subjectID
     * @return array
     * @author demo
     */
    public function userAbilityKl($username,$subjectID){
        if (!$subjectID) {
            return [0,'学科数据错误，请重试！'];
        }
//        $IData =  (new KlAbilityApi())->userKlAbility($username,$subjectID);
        $IData = $this->getApiAat('KlAbility/userKlAbility', $username,$subjectID);
        if($IData){
            return [1,$IData];
        }else{
            return [0,'获取知识点数据失败，请刷新页面重试！'];
        }
    }

    /**
     * 描述：获取用户不同等级的知识点二级数据接口
     * @param $username
     * @param $subjectID
     * @param $level
     * @return mixed
     * @author demo
     */
    public function userLevelKl($username,$subjectID,$level){
        if(!$level||!$subjectID){
            return [0,'学科或等级参数错误！'];
        }
//        return (new KlAbilityApi())->userLevelKl($username,$subjectID,$level);
        return $this->getApiAat('KlAbility/userLevelKl',$username,$subjectID,$level);
    }

    /**
     * 描述：目标测试记录
     * @param $subjectID
     * @param $username
     * @return array
     * @author demo
     */
    public function amiRecord($subjectID,$username){
        $userTestRecord = $this->getModel('UserTestRecord');
        if (!$subjectID) {
            return [0,'缺少学科数据，请重试！'];
        }
        $style = 6;
        $where = [
            'zj_user_test_record.UserName' => $username,
            'zj_user_test_record.SubjectID' => $subjectID,
            'zj_user_test_record.Style' => $style
        ];
        // 查询满足要求的总记录数
        $count = $userTestRecord->selectCount(
            $where,
            'TestID'
        );
        $page = handlePage('init',$count,5,['type'=>1]);
        $show = $page->show(false); // 分页显示输出
        $pageStr=$page->firstRow . ',' . $page->listRows;
        $db = $userTestRecord->unionSelect('testUserRecordPageData',$where,$pageStr);
        if($db){
            $list = [];
            foreach($db as $i=>$k){
                $list[$i]['test_record_id'] = $k['TestID'];
                $list[$i]['is_submit'] = $k['Score']=='-1'?0:1;
                $scoreArr = explode(',',$k['AimScore']);
                $list[$i]['score'] = $scoreArr[0];
                $list[$i]['time'] = date('Y年m月d日 H:i',$k['LoadTime']);
                $strKlName = '';//知识点名称
                $over = '';
//                $klCache = SS('knowledge');
                $klCache = $this->getApiCommon('Knowledge/knowledge');
                foreach(explode(',',$k['KlID']) as $j=>$m){
                    //默认显示15个以内的知识点
                    if($j<=15){
                        $strKlName = $strKlName.$klCache[$m]['KlName'].'、';
                    }else{
                        $over = '...';
                    }
                }
                $strKlName = substr($strKlName,0,-3).$over;
                $list[$i]['kl_name'] = $strKlName;
            }
            return [1,['show'=>$show,'list'=>$list]];
        }else{
            return [0,'暂时没有目标测试的记录！'];
        }
    }

    public function type($subjectID){
//        $typesArray = SS('typesSubject');
        $typesArray = $this->getApiCommon('Types/typesSubject');
        if(!$subjectID){
            return [0,'请先选择学科！'];
        }
        $types = $typesArray[$subjectID];
        if ($types) {
            return [1,$types];
        } else {
            return [0,'获取题型数据失败，请重试！'];
        }
    }

    /**
     * 描述：获取试卷
     * @param $subjectID
     * @param $style
     * @param $year
     * @param $searchKey
     * @param $pageSize
     * @return array
     * @author demo
     */
    public function docList($subjectID,$style,$year,$searchKey,$pageSize){
        if (!$subjectID || !$style) {
            return [0,'数据参数错误，请重试！'];
        }

        $where=array();
        $where['TypeID']=$style;
        $where['SubjectID']=$subjectID;
        $where['IfTest']=2;
        $where['ShowWhere']=array(1,2);
        if($year) $where['DocYear'] = $year;
        if($searchKey) $where['key'] = $searchKey;

        $pageNum=is_numeric($_REQUEST['p']) ? $_REQUEST['p'] : 1 ;
        $field=array('docid','docname');
        $page=array('page'=>$pageNum,'perpage'=>$pageSize);
        $order=array('docyear DESC,introfirsttime DESC');

//        $doc=$this->getModel('Doc');
//        $buffer=$doc->getDocIndex($field,$where,$order,$page);
        $buffer = $this->getApiDoc('Doc/getDocIndex', $field,$where,$order,$page);

        if(!$buffer[1] && $pageNum>1){
             return [0,'没有更多试卷了'];
        }
        if(!$buffer[1]){
            return [0,'暂时没有此类试卷，请先看看其它试卷！'];
        }

        $list=array();
        if($buffer[0]){
            $docIDArray=array();
            foreach($buffer[0] as $iBuffer){
                $docIDArray[]=$iBuffer['docid'];
            }
            $testArray=array();
//            $testBuffer=$this->getModel('DocTestData')->selectData('DocID,AatTestTimes','DocID in ('.implode(',',$docIDArray).')');
            $testBuffer = $this->getApiDoc('Doc/selectData', 'DocID,AatTestTimes','DocID in ('.implode(',',$docIDArray).')', '', '', 'DocTestData');
            $testArray=$this->getApiTest('Test/reloadTestArr',$testBuffer,'DocID');

            foreach($buffer[0] as $iBuffer){
                $list[]=array(
                    'DocID'=>$iBuffer['docid'],
                    'DocName'=>$iBuffer['docname'],
                    'AatTestTimes'=>$testArray[$iBuffer['docid']]['AatTestTimes']
                );
            }
        }

        $page = handlePage('init',$buffer[1],$pageSize,['type'=>1]);
        $show = $page->show(false); // 分页显示输出

        return [1,[
            'show'=>$show,
            'list'=>$list,
            'year'=>$year,
            'searchKey'=>$searchKey
        ]];
    }

    public function subject($gradeID){
        $cacheGradeSubject = $this->getApiCommon('Grade/grade',$gradeID);
        $cacheSubject = $this->getApiCommon('Subject/subject');
        $subjectID = $cacheGradeSubject['SubjectID'];
        $subject = array();
        foreach($cacheSubject as $i=>$k){
            if($k['PID'] == $subjectID){
                $subject[$i]['subject_id'] = $i;
                $subject[$i]['subject_name'] = $k['SubjectName'];
                $subject[$i]['total_score'] = $k['TotalScore'];
            }
        }
        if($subject){
            return [1,$subject];
        }else{
            return [0,'50018']; //获取学科数据失败！
        }
    }

    /**
     * 描述：总分
     * @param $subjectID
     * @return array
     * @author demo
     */
    public function normalScore($subjectID){
        if(!$subjectID){
            return [0,'请先选择学科！'];
        }
//        $subjectCache = SS('subject');
        $subjectCache = $this->getApiCommon('Subject/subject');
        $subjectScore = $subjectCache[$subjectID]['TotalScore'];
        return [1,['score'=>$subjectScore,'id'=>$subjectID]];
    }

    //获取试题的数量 测试试题数量验证 超出部分去除
    private function getRealTestCon($arr,$num){
        if(empty($num)) return [$arr,0];
        if(empty($arr)) return [$arr,0];

        $newArr=array();
        $j=1;
        foreach($arr as $iArr){
            if($j>$num) break;
            $newArr[]=$iArr;
            if(empty($iArr['testnum'])) $iArr['testnum']=1;
            $j+=(int)$iArr['testnum'];
        }
        return [$newArr,$j];
    }
    /**
     * 描述：推题方法
     * @param $userID
     * @param $username
     * @param $type
     * @param $subjectID
     * @param $param
     * @return array
     * @author demo
     */
    public function pushTest($userID,$username,$type,$subjectID,$param){
        if (!$subjectID) {
            return [0, '请先选择学科！'];
        }
        if(preg_match('/^([1-8])$/',$type)==0){
            return [0,'测试类型错误，请重试！'];
        }
//        $httpLimit = R('Common/SystemLayer/setHttpLimit',[$username,5,10]);
//        if($httpLimit>0){
//            return [0,'访问过快,请稍后访问！('.$httpLimit.')'];
//        }
        $this->authPushTest(0,$userID,$username,$subjectID);//权限验证-测试总次数是否超标
        $this->authPushTest($type,$userID,$username,$subjectID);//权限验证-某一类型测试次数超标
        //索引搜索字段
        $field = ['testid','typesid','testnum'];
        //索引搜索条件
        $where = [];
        if($type!=8){//专题不区分学科目前
            $where['SubjectID'] = $subjectID;
        }
        //使用范围为通用和提分专用
        $where['ShowWhere'] = [1,2];
        //测试记录属性
        $recordAttr = [];
        $recordAttr['Style'] = $type;
        //测试试题ID数组
        $testIDArray = [];
//        $testRealModel = $this->getModel('TestReal');
        switch ($type) {
            case 1:
                //智能测试
                $diff = $this->testDiff($username,$subjectID);//测试难度 五个等级1-5五段
                $version = $param['version'];//高考版 同步版
                $where['Diff'] = $diff;//1-5五段
                $where['TestStyle'] = 1;//全选择题
                if($version == 2){//如果用户当前选择的是同步版
//                    $userChapterModel = $this->getModel('UserChapter');
//                    $chapterID = $userChapterModel->getUserAllChapter($username,$subjectID,$isAll = false);
                    $chapterID = $this->getApiUser('User/getUserAllChapter',$username,$subjectID,$isAll = false);
                    $where['ChapterID'] = $chapterID;
                }
                $typeFilter = C('WLN_TYPE_FILTER')[$subjectID];
                if($typeFilter){//学科下需要过滤的题型
                    $where['TypesID'] = $typeFilter;
                    $where['TypeFilter'] = 1;
                }
                $order = ['@random'];
                $page = ['page'=>1,'perpage'=>10, 'limit'=>100];
//                $testIndexArr = $testRealModel->getTestIndex($field,$where,$order,$page);
                $testIndexArr = $this->getApiTest('Test/getTestIndex', $field,$where,$order,$page);

                if($testIndexArr[1]<1){//修改难度推题
                    if($diff<3){
                        $diff++;
                    }else{
                        $diff--;
                    }
                    $where['Diff'] = $diff;
//                    $testIndexArr = $testRealModel->getTestIndex($field,$where,$order,$page);
                    $testIndexArr = $this->getApiTest('Test/getTestIndex', $field,$where,$order,$page);
                }
                if($testIndexArr[1]<1){//如果第二次还没有试题，那说明试题这个难度太少了，前台提示
                    return [0,'没有找到符合条件的试题，请修改条件后重试！（'.$testIndexArr[1].'）'];
                }
                $testIndexArr2=$this->getRealTestCon($testIndexArr[0],10);
                $testIDArray = $testIndexArr2[0];
                //测试记录属性表赋值
                $recordAttr['Diff'] = $diff;//注意，和整卷练习的难度不一样取值
                $recordAttr['TestAmount'] =  $testIndexArr2[1];
                break;
            case 2:
                $klID = $param['klID'];
                if(!$klID){
                    return [0,'请选择你要练习的知识点！'];
                }
                $where['KlID'] = $klID;//where不限制TestStyle，可以有所有题型
                $order = ['@random'];
                $page = ['page'=>1,'perpage'=>10, 'limit'=>100];
//                $testIndexArr = $testRealModel->getTestIndex($field,$where,$order,$page);
                $testIndexArr = $this->getApiTest('Test/getTestIndex', $field,$where,$order,$page);
                if($testIndexArr[1]<1){
                    return [0,'没有找到符合条件的试题，请修改条件后重试！（'.$testIndexArr[1].'）'];
                }
                $testIndexArr2=$this->getRealTestCon($testIndexArr[0],10);
                $testIDArray = $testIndexArr2[0];
                //测试记录属性表赋值
                $recordAttr['KlID'] = $klID;
                $recordAttr['TestAmount'] =  $testIndexArr2[1];
                break;
            case 3:
                //整卷练习-现有套卷
                $docID = $param['docID'];
                if(!$docID){
                    return [0,'请先选择试卷进行测试！'];
                }
                //按照DocID获取试题
                $where['DocID'] = $docID;
                $order = ['TestID ASC']; //排序
                $page = ['page'=>1,'perpage'=>500, 'limit'=>500];//一套试卷极限500题上线
//                $testIndexArr = $testRealModel->getTestIndex($field,$where,$order,$page);
                $field[]='numbid';
                $testIndexArr = $this->getApiTest('Test/getTestIndex', $field,$where,$order,$page);
                if ($testIndexArr[1] == 0) {
                    return [0, '该试卷没有试题，请选择其它的测试！'];
                }

                //试题关于numbid排序
                $newTestList=array();
                foreach($testIndexArr[0] as $i=>$iTestIndexArr){
                    $tmpArr=explode('-',$iTestIndexArr['numbid']);
                    $newTestList[(int)$tmpArr[1]]=$iTestIndexArr;
                }
                ksort($newTestList);
                $testIndexArr[0]=$newTestList;

                $testIndexArr2=$this->getRealTestCon($testIndexArr[0],100);
                $testIDArray = $testIndexArr2[0];
                $recordAttr['DocID'] = $docID;
                $recordAttr['TestAmount'] = $testIndexArr2[1];
                //试卷测试次数+1
//                $this->getModel('DocTestData')->addTestTimes($docID);
                $this->getApiDoc('Doc/addTestTimes', $docID);
                break;
            case 4:
                //整卷练习-自定义智能组卷
                $diff = $param['diff'];//难度
                $cover = $param['cover'];//知识点覆盖率
                $typesID = $param['typesID'];
                $typesNum = $param['typesNum'];
                $typesScore = $param['typesScore'];
                $dScore = $param['dScore'];
                $klID = $param['klID'];
                if (!$typesNum || !$typesID || !$diff || !$cover || !$dScore || !$typesScore || !$klID) {
                    return [0,'提交参数错误，请重试！'];
                }
//                $GAResult = $this->getModel('Ga')->main(20,[
//                    'SubjectID'=>$subjectID, //设置学科
//                    'KlCover'=>$klID,//设置知识点或章节 id
//                    'Diff'=>$diff, //设置难度值
//                    'KlPer'=>$cover/100,//设置知识点覆盖率
//                    'Types'=>[$typesID, $typesNum, $dScore, $typesScore],//设置试题类型
//                ]);
                $GAResult = $this->getApiGa('Ga/main', 20,[
                    'SubjectID'=>$subjectID, //设置学科
                    'KlCover'=>$klID,//设置知识点或章节 id
                    'Diff'=>$diff, //设置难度值
                    'KlPer'=>$cover/100,//设置知识点覆盖率
                    'Types'=>[$typesID, $typesNum, $dScore, $typesScore],//设置试题类型
                ]);
                $testIDArray = $GAResult[0];//确保GA输入[0]是数组
                $testAmount = count($testIDArray);
                if ($testAmount==0) {
                    return [0,'没有找到符合条件的试题，请修改条件后重试！'];
                }
                $testIndexArr2=$this->getRealTestCon($testIDArray,100);
                $testIDArray = $testIndexArr2[0];

                //测试记录属性表赋值
                $recordAttr['KlID'] = $klID;
                $recordAttr['Diff'] = $diff;
                $recordAttr['Cover'] = $cover;
                $recordAttr['TypesID'] = $typesID;
                $recordAttr['TypesNum'] = $typesNum;
                $recordAttr['DScore'] = $dScore;
                $recordAttr['TypesScore'] = $typesScore;
                $recordAttr['TestAmount'] = $testIndexArr2[1];
                break;
            case 5:
                //阶段测试
                $klID = $param['klID'];
                if(!$klID){
                    return [0,'请先选择知识点！'];
                }
                $where['KlID'] = $klID;//where不限制TestStyle，可以有所有题型
                $order = ['@random'];
                $page = ['page'=>1,'perpage'=>10, 'limit'=>100];
//                $testIndexArr = $testRealModel->getTestIndex($field,$where,$order,$page);
                $testIndexArr = $this->getApiTest('Test/getTestIndex', $field,$where,$order,$page);
                if($testIndexArr[1]<1){
                    return [0,'没有找到符合条件的试题，请修改条件后重试！（'.$testIndexArr[1].'）'];
                }
                $testIndexArr2=$this->getRealTestCon($testIndexArr[0],10);
                $testIDArray = $testIndexArr2[0];
                //测试记录属性表赋值
                $recordAttr['KlID'] = $klID;
                $recordAttr['TestAmount'] = $testIndexArr2[1];
                break;
            case 6:
                //目标测试
                $score = $param['score'];
                $totalScore = $param['totalScore'];
                $klID = $param['klID'];
                if (!$score ||!$totalScore || !$klID || $score>=$totalScore) {
                    return [0,'提交数据错误，请重试！'];
                }
                $where['KlID'] = $klID;//where不限制TestStyle，可以有所有题型
                $diff = 3;//难度默认适中
                $diffArray = C('WLN_TEST_DIFF');
                $diffNum = 1 - round($score / $totalScore, 2);
                foreach ($diffArray as $i => $iDiffArray) {
                    if ($diffNum >= $iDiffArray[3] && $diffNum <= $iDiffArray[4]) {
                        $diff = $i;
                        break;
                    }
                }
                $where['Diff'] = $diff;//1-5五段
                $order = ['@random'];
                $page = ['page'=>1,'perpage'=>10, 'limit'=>100];
//                $testIndexArr = $testRealModel->getTestIndex($field,$where,$order,$page);
                $testIndexArr = $this->getApiTest('Test/getTestIndex', $field,$where,$order,$page);
                if($testIndexArr[1]<1){
                    return [0,'没有找到符合条件的试题，请修改条件后重试！（'.$testIndexArr[1].'）'];
                }
                $testIndexArr2=$this->getRealTestCon($testIndexArr[0],10);
                $testIDArray = $testIndexArr2[0];
                //测试记录属性表赋值
                $recordAttr['KlID'] = $klID;
                $recordAttr['AimScore'] = $score.','.$totalScore;
                $recordAttr['TestAmount'] = $testIndexArr2[1];
                break;
            case 7:
                //章节测试
                $chapterID = $param['chapterID'];
                if(!$chapterID){
                    return [0,'请选择需要练习的章节！'];
                }
                $where['ChapterID'] = $chapterID;
                $order = ['@random'];
                $page = ['page'=>1,'perpage'=>10, 'limit'=>100];
//                $testIndexArr = $testRealModel->getTestIndex($field,$where,$order,$page);
                $testIndexArr = $this->getApiTest('Test/getTestIndex', $field,$where,$order,$page);
                if($testIndexArr[1]<1){
                    return [0,'没有找到符合条件的试题，请修改条件后重试！（'.$testIndexArr[1].'）'];
                }
                $testIndexArr2=$this->getRealTestCon($testIndexArr[0],10);
                $testIDArray = $testIndexArr2[0];
                //测试记录属性表赋值
                $recordAttr['ChapterID'] = $chapterID;
                $recordAttr['TestAmount'] = $testIndexArr2[1];
                break;
            case 8:
                // 专题练习
                $topicPaperID = $param['topicPaperID'];
                if(!$topicPaperID){
                    return [0,'请选择专题试卷！'];
                }
                $topicPaperDb = $this->getModel('TopicPaper')->findData(
                    'TopicPaperID,TopicPaperName,SubjectID,PaperType,DocID,TestIDs,TestTimes',
                    ['TopicPaperID'=>$topicPaperID,'Status'=>1]
                );
                if(!$topicPaperDb){
                    return [0,'专题试卷不存在或已锁定！'];
                }
                //---------------- begin 验证试卷状态  16-4-26------------------
                $docId = (int)$topicPaperDb['DocID'];
                $score = 0;
                if($docId > 0){
//                $docData = $this->getModel('Doc')->findData('AatTestStyle', 'DocID='.$docId);
                    $docData = $this->getApiDoc('Doc/findData', 'AatTestStyle', 'DocID='.$docId, '', 'Doc');
                    $score = (int)$docData['AatTestStyle'];
                //验证打分专题试卷是否验证过订单号
//                if(1 == $score && !$this->getModel('UserDocInviteCode')->isDocToUsed($docId, $userID)){
//                    return array(0, 'noToken');
//                }
                }else{
                    //判断所有试题所在文档是否都支持打分
                    $thisBuffer=$this->getModel('TestAttrReal')->selectData('TestID,DocID','TestID in ('.$topicPaperDb['TestIDs'].')');
                    $docArr=array();
                    foreach($thisBuffer as $iThisBuffer){
                        $docArr[]=$iThisBuffer['DocID'];
                    }
                    $docArr=  array_unique($docArr);
                    $docBuffer = $this->getModel('Doc')->selectData( 'DocID,AatTestStyle', 'DocID in ('.implode(',',$docArr).')');
                    $score=1;
                    foreach($docBuffer as $iDocBuffer){
                        if($iDocBuffer['AatTestStyle']!=1) $score=0;
                    }
                }

                //验证该专题文档是否已经生成做题记录。
                if(1 == $score && ((int)$topicPaperDb['TestTimes']) > 0){
                    $record = $this->getModel('UserTestRecordAttr')->findData('TestRecordID', "UserName='{$username}' AND TopicPaperID={$topicPaperID}",'TestRecordAttrID DESC');
                    $record = (int)$record['TestRecordID'];
                    if($record > 0){
                        $recordScore = $this->getModel('UserTestRecord')->findData('Score', "TestID=".$record);
                        if($recordScore['Score']==-1){
                            return [1,$record];
                        }
                        return [1,$record,false];
                    }
                }
                //---------------- end 验证试卷状态 ------------------
                if($topicPaperDb['PaperType'] == 1){
                    $where['DocID'] = $topicPaperDb['DocID'];
                    $recordAttr['DocID'] = $topicPaperDb['DocID'];
                }elseif($topicPaperDb['PaperType'] == 2){
                    $where['TestID'] = $topicPaperDb['TestIDs'];
                }else{
                    return [0,'专题试卷查询出错！'];
                }
                if($score==1){
                    $where['AatTestStyle'] = 1;
                }

                $subjectID = $topicPaperDb['SubjectID'];//注意：这里必须覆盖$subjectID ，之前的subjectID是cookie得到的
                $field[]='numbid';
                $order = ['testid asc'];
                $page = ['page'=>1,'perpage'=>100, 'limit'=>100];
//                $testIndexArr = $testRealModel->getTestIndex($field,$where,$order,$page);
                $testIndexArr = $this->getApiTest('Test/getTestIndex', $field,$where,$order,$page);
                if($testIndexArr[1]<1){
                    return [0,'没有找到符合条件的试题，请修改条件后重试！（'.$testIndexArr[1].'）'];
                }

                if($docId>0){
                    //试题关于numbid排序
                    $newTestList=array();
                    foreach($testIndexArr[0] as $i=>$iTestIndexArr){
                        $tmpArr=explode('-',$iTestIndexArr['numbid']);
                        $newTestList[(int)$tmpArr[1]]=$iTestIndexArr;
                    }
                    ksort($newTestList);
                    $testIndexArr[0]=$newTestList;
                }

                $testIndexArr2=$this->getRealTestCon($testIndexArr[0],100);
                $testIDArray = $testIndexArr2[0];
                //写入test_record_attr表
                $recordAttr['TestRecordName'] = $topicPaperDb['TopicPaperName'];
                $recordAttr['TopicPaperID'] = $topicPaperDb['TopicPaperID'];
                $recordAttr['TestAmount'] = $testIndexArr2[1];
                //更新topic_paper表
                $this->getModel('TopicPaper')->updateData(
                    ['TestTimes'=>$topicPaperDb['TestTimes']+1],
                    ['TopicPaperID'=>$topicPaperDb['TopicPaperID']]
                );
                break;
            default:
                return [0,'数据参数错误，请刷新页面后重试！'];
        }
        //进入测试
        //对试题进行排序
        $content = [];//逗号分隔的试题ID，用于testRecord表存储
        $typeCache = SS('types');
        $tmp=array();
        foreach ($testIDArray as $iTestIDArray) {
            if(is_array($iTestIDArray)){
                $typeCache[$iTestIDArray['typesid']]['typesIDs'][]=$iTestIDArray['testid'];
                if($docId>0) $content[]=$iTestIDArray['testid'];
            }else{
                $content[] = $iTestIDArray;//GA得到的数组没有testid键
            }
        }
        if(!empty($typeCache) && $docId<=0){
            foreach($typeCache as $iTypeCache){
                $content[]=implode(',',$iTypeCache['typesIDs']);
            }
        }
        $content=array_filter($content);

        $data['Style'] = $type;
        $data['LoadTime'] = time();
        $data['Content'] = implode(',', $content);
        $data['UserName'] = $username;
        $data['SubjectID'] = $subjectID;
        //写入user_test_record数据库
        $recordID = $this->getModel('UserTestRecord')->insertData(
            $data
        );
        //写入user_test_record_attr数据库
        $recordAttr['TestRecordID'] = $recordID;
        $recordAttr['UserName'] = $data['UserName'];
        $recordAttr['SubjectID'] = $subjectID;
        $recordAttr['LoadTime'] = $data['LoadTime'];
        $testRecordAttrID = $this->getModel('UserTestRecordAttr')->insertData(
            $recordAttr
        );

        //返回跳转页面的数据
        if(!$recordID||!$testRecordAttrID){
            return [0,'数据保存错误，请重试！'];
        }
        return [1,$recordID];
    }

    /**
     * 用户测试权限提示
     * @param int $style 测试类型 默认为0
     * @param $userID
     * @param $userName
     * @param $subjectID
     * @return array
     * @author demo
     */
    private function authPushTest($style=0,$userID,$userName,$subjectID){
        $frequencyNum=$this->getApiUser('User/totalFrequency',$style,$userID,$userName,$subjectID);
        if(is_numeric($frequencyNum)){
            return [0,'今天已测试'.$frequencyNum.'次，请试试其他的测试吧！'];
        }elseif($frequencyNum===true){
            return [0,'您没有该操作权限！'];
        }
        return [1,null];
    }

    /**
     * 描述：检测测试条件
     * @param $username
     * @param $subjectID
     * @param $isApp
     * @return array
     * @author demo
     */
    public function checkCondition($username,$subjectID,$isApp){
        if($isApp){//手机版错误信息
            $errorMsg['subject'] = '请先选择学科！';
            $errorMsg['chapter'] = '请先进入个人中心选择教材！';
        }else{
            $errorMsg['subject'] = '请点击左侧选择学科！';
            $errorMsg['chapter'] = '同步学习版需选择教材，请先进入个人中心选择教材选择！';
        }
        if (!$subjectID) {
            return [0,$errorMsg['subject']];
        }
        //如果是同步学习版查询用户当前学科下的教材是否选择
//        $userDb = $this->getModel('User')->findData(
//            'Version',
//            ['UserName'=>$username]
//        );
        $userDb = $this->getApiUser('User/findData', 'Version', ['UserName'=>$username], '', 'User');
        if($userDb['Version'] == 2){
//            $chapterDb = $this->getModel('UserChapter')->findData(
//                'UserChapterID',
//                ['UserName'=>$username,'SubjectID'=>$subjectID]
//            );
            $chapterDb = $this->getApiUser('User/findData', 'UserChapterID', ['UserName'=>$username,'SubjectID'=>$subjectID], '', 'UserChapter');
            if (!$chapterDb) {
                return [0,$errorMsg['chapter']];
            }
        }
        return [1,''];
    }

    /**
     * 描述：可以测试的试卷类型
     * @return array|null
     * @author demo
     */
    public function canExerciseDocType(){
//        $docTypeCache = SS('docType');
        $docTypeCache = $this->getApiDoc('DocType/docType');
        $docTypeData = [];//试卷类型数据
        if(!$docTypeCache){
            return null;
        }
        foreach ($docTypeCache as $i => $iDocTypeCache) {
            if($iDocTypeCache['DefaultTest'] == 1){
                $docTypeData[] = [
                    'typeID' => $i,
                    'typeName' => $iDocTypeCache['TypeName']
                ];
            }
        }
        return $docTypeData;
    }

    /**
     * 自适应测试的试题难度
     * @param $username
     * @param $subjectID
     * @return int 配置中WLN_TEST_DIFF的难度等级取值1-5
     * @author demo
     */
    private function testDiff($username,$subjectID){
        //用户最近一次生成的能力值,取值[-3,3]
        $ability = $this->getModel('UserForecast')->getLastAbility($username,$subjectID)['ForecastAbility'];
        if(!$ability){
            //如果用户暂时没有能力值则取中间值
            $ability = 0;
        }
        $a = 1;//区分度
        $c = 0.25;//猜测系数
        $b = $ability - log((1+sqrt(1+8*$c))/2)/(1.7*$a);//难度取值区间[-3,3]
        $diff = $this->getModel('Normal')->val2Pro($b);//[0,1]范围区间的难度值
        $diffNum = 3;//默认难度等级为3,
        $diffRange = C('WLN_TEST_DIFF');
        foreach ($diffRange as $i => $iDiffRange) {
            if ($diff >= $iDiffRange[3] && $diff <= $iDiffRange[4]) {
                $diffNum = $i;
            }
        }
        return $diffNum;
    }

}