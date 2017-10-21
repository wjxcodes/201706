<?php
/**
 * @author demo
 * @date 2014年11月20日
 */

/**
 * 作业动态学情分析类，控制班级作业动态学情分析和班级内学生个人作业动态学情分析
 */
namespace Work\Controller;
class WorkReportController extends BaseController {

    /**
     * 班级作业动态学情分析首页显示
     * @author demo
     */
    public function classIndex(){
        $classID=$_GET['cid']?$_GET['cid']:'';
        $this->assign('classID',$classID);
        $this->display();
    }

    /**
     * 获取首页需要显示的数据ajax返回
     * @author demo
     */
    public function classIndexData(){
        $classID     = $_POST['classID'];
        $subjectID   = cookie('SubjectId')?cookie('SubjectId'):12;
        $studentList = $this->classStudents($classID);//获取班级学生
        $radarInfo   = $this->classAbilities($subjectID,$classID);//雷达图
        $lineInfo    = $this->classScores($subjectID,$classID);//折线图
        $this->setBack(array('studentList'=>$studentList,'radarInfo'=>$radarInfo,'lineInfo'=>$lineInfo));
    }

    /**
     * 获取班级某学科学情评价雷达图所需数据
     * @param int $subjectID 学科ID
     * @param int $classID 班级ID
     * @return array 知识点能力值数组
     * ['series'=>
     * [['value'=>[1,2],'name'=>'时间1'],['value'=>[1,2],'name'=>'时间2']],
     * 'indicator'=>
     * [['text'=>'知识点1','max'=>'最大值3'],['text'=>'知识点2','max'=>'最大值3']]
     * ]
     * @author demo
     */
    private function classAbilities($subjectID,$classID){
        $param['style']='knowledge';
        $param['subjectID'] = $subjectID;
        $param['return'] = 2;
        $klArray = $this->getData($param);
        $kl = [];//需要的知识点
        foreach($klArray as $i=>$iKlArray){
            $kl[] = ['klID'=>$iKlArray['KlID'],'klName'=>$iKlArray['KlName']];
        }
        $workKlsModel = D('UserWorkKls');
        $indicator = [];
        $series = [];

        foreach($kl as $i=>$iKl){
            $klAbility = $workKlsModel->classKlAvgAbility($subjectID,$classID,$iKl['klID']);
            if($klAbility){
                $indicator[] = ['text'=>$iKl['klName'],'max'=>3,'min'=>-3];
                $series[0]['value'][] = $klAbility[0]?round($klAbility[0]['Ability'],2):-3;
                $series[0]['name'] = '最近第1次';
                $series[1]['value'][] = $klAbility[1]?round($klAbility[1]['Ability'],2):-3;
                $series[1]['name'] = '最近第2次';
                $series[2]['value'][] = $klAbility[2]?round($klAbility[2]['Ability'],2):-3;
                $series[2]['name'] = '最近第3次';
            }
        }

        //@@@68698189
        $userName=$this->getCookieUserName();
        if($userName == '18888888888'){
            //修改知识点雷达图数据
            foreach ($series as $i => $iSeries){
                foreach($iSeries['value'] as $j=>$jSeries){
                    $num=($jSeries+rand(10,30)/10);
                    $series[$i]['value'][$j] = $num>3?2.5:$num;
                }
            }
        }

        $result = ['series'=>$series,'indicator'=>$indicator];
        return $result;
    }

    /**
     * 获取班级某学科学情评价折线图数据
     * @param int $subjectID 学科ID
     * @param int $classID 班级ID
     * @return array 平均分数组 ['series'=>[60,75,90,84],'xAxis'=>['11月2日','11月3日','11月4日','11月5日']]
     * @author demo
     */
    private function classScores($subjectID,$classID){
        $userSendWorkModel = D('UserSendWork');
        $data = $userSendWorkModel->classAvgScore($subjectID,$classID);
        $data = array_reverse($data);//反序，因为数据库是按照时间倒序排列的
        $series = [];
        $xAxis = [];
        foreach($data as $i=>$iData){
            $series[] = round($iData['CorrectRate']*100,0);
            $xAxis[] = date('m月n日 H:i',$iData['SendTime']);
        }
        $result = ['series'=>$series,'xAxis'=>$xAxis];
        return $result;
    }

    /**
     * 获取班级下学生列表
     * @param int $classID 班级ID
     * @return array 成功返回学生数组 [['UserID'=>'','RealName'=>'','OrderNum'=>'']]，失败返回空数组
     * @author demo
     */
    private function classStudents($classID){
        $studentList = D('Base')->unionSelect('classUserByClassID',$classID);
        return $studentList?$studentList:'';
    }

    /**
     * 显示单个学生某个学科的整体作业动态学情分析（不包含单次）
     * @author demo
     */
    public function studentIndex(){
        $userID    = $_GET['id'];
        $classID   = $_GET['cid'];
        $this->assign('classID',$classID);
        $this->assign('studentID',$userID);
        $this->display();
    }

    /**
     * 获取学生报告首页界面的数据ajax返回
     * @author demo
     */
    public function studentIndexData(){
        $userID    = $_POST['uid'];
        $classID   = $_POST['cid'];
        $ifFirst   = $_POST['ifFirst'];
        $subjectID = cookie('SubjectId');

        //格式化学生信息
        $userInfo = $this->studentInfo($userID);
        $userInfo = $userInfo[0];
        $newUserInfo = array();
        $newUserInfo['name']   = $userInfo['RealName']?$userInfo['RealName']:$userInfo['UserName'];
        $newUserInfo['order']  = $userInfo['OrderNum'];
        $newUserInfo['phone']  = $userInfo['Phonecode'];
        $newUserInfo['sex']    = $userInfo['Sex']==0?'男':'女';
        $newUserInfo['email']  = $userInfo['Email'];
        unset($userInfo);
        $result['studentInfo'] =$newUserInfo;

        if($ifFirst==1){//首次载入加载学生列表
            $result['studentList']=$this->classStudents($classID);
        }
        $result['radarInfo'] =  $this->studentAbilities($userID,$classID,$subjectID);//雷达
        $result['lineInfo']  =  $this->studentScores($userID,$classID,$subjectID);//折线
        $result['knowledge']['klList']=$this->studentKlTree($userID,$classID,$subjectID);//知识点

        $this->setBack($result);
    }

    /**
     * 返回学生作业能力值历次记录，用于生成能力值雷达r
     * 返回知识点只需要第一级
     * @param int $userID 学生ID
     * @param int $classID 班级ID
     * @param int $subjectID 学科ID
     * @return array 返回格式参照classAbilities
     * @author demo
     */
    private function studentAbilities($userID,$classID,$subjectID){
        $param['style']='knowledge';
        $param['subjectID'] = $subjectID;
        $param['return'] = 2;
        $knowledge = $this->getData($param);
        $field='Ability,AddTime';
        $where=array("SubjectID"=>$subjectID,"UserID"=>$userID,"ClassID"=>$classID,'Ability'=>array('neq','null'));
        $order="AddTime DESC";
        $indicator=[];
        $series=[];
        foreach($knowledge as $i=>$iKnowledge){//按知识点ID查
            $where['KlID']=$iKnowledge['KlID'];
            $abilityList=D('UserWorkKls')->selectData(
                $field,
                $where,
                $order,
                3
            );
            if($abilityList){
                $indicator[]=['text'=>$iKnowledge['KlName'],'max'=>3,'min'=>-3];
                $series[0]['value'][]=$abilityList[0]?round($abilityList[0]['Ability'],2):-3;
                $series[0]['name']='最近第1次';
                $series[1]['value'][]=$abilityList[1]?round($abilityList[1]['Ability'],2):-3;
                $series[1]['name']='最近第2次';
                $series[2]['value'][]=$abilityList[2]?round($abilityList[2]['Ability'],2):-3;
                $series[2]['name']='最近第3次';
            }
        }
        $result['indicator']=$indicator;//组合雷达图的外围知识点数据结构
        $result['series']=$series;//组合雷达图的数据结构
        return $result;
    }

    /**
     * 返回学生作业历次分数记录，用于生成折线图
     * @param int $userID 学生ID
     * @param int $classID 班级ID
     * @param int $subjectID 学科ID
     * @return array 返回格式参照classScores
     * @author demo
     */
    private function studentScores($userID,$classID,$subjectID){
        $field="SendTime,CorrectRate";
        $where=array('UserID'=>$userID,'ClassID'=>$classID,'SubjectID'=>$subjectID,'Status'=>1);
        $order="SendTime DESC";
        $limit=20;
        $workList=D('UserSendWork')->selectData(
            $field,
            $where,
            $order,
            $limit
        );
        $workList=array_reverse($workList);//反序，因为数据库是按照时间倒序排列的
        $result=[];
        //将数据按照时间从小到大排列
        foreach($workList as $i => $iWorkList){
            $result['series'][]=floor($workList[$i]['CorrectRate']*100);
            $result['xAxis'][]=date('m月d日',$workList[$i]['SendTime']);
        }
        return $result;
    }

    /**
     * 返回学生作业知识点能力值历次记录，用于生成知识点树形结构（标星）
     * 返回知识点需要三级
     * @param int $userID 学生ID
     * @param int $classID 班级ID
     * @param int $subjectID 学科ID
     * @return array 返回格式 'sub'=>['klID'=>1,'klName'=>'','klAbility'=>'']
     * @author demo
     */
    private function studentKlTree($userID,$classID,$subjectID){
        $knowledge = SS('klBySubject3')[$subjectID];//知识点
        $field="KlID,RightAmount,AllAmount";
        $where=array('UserID'=>$userID,'ClassID'=>$classID,'SubjectID'=>$subjectID);
        $order="AddTime ASC";
        $limit='';
        $klList=D('UserWorkKl')->selectData(
            $field,
            $where,
            $order,
            $limit
        );
        foreach($klList as $i=>$iKlList){//转化成以知识点ID为下标的数据集
            $newKlList[$iKlList['KlID']]=$iKlList;
        }
        $result=[];//返回数据集
        foreach($knowledge as $i=>$iKnowledge){//将数据集按照知识点树结构加入到每个知识点下
            $klID=$iKnowledge['KlID'];
            $rightAmount=$newKlList[$klID]['RightAmount']?$newKlList[$klID]['RightAmount']:0;
            $allAmount = $newKlList[$klID]['AllAmount']?$newKlList[$klID]['AllAmount']:0;
            $result[$i]['KlID']=$iKnowledge['KlID'];
            $result[$i]['KlName']=$iKnowledge['KlName'];
            $result[$i]['rightAmount']=$rightAmount;
            $result[$i]['allAmount']=$allAmount;
            $result[$i]['correctRate']=round($rightAmount / ($allAmount?$allAmount:1), 2) * 100;
            if($iKnowledge['sub']){//第二级知识点
                foreach($iKnowledge['sub'] as $j=>$jKnowledge){
                    $jklID=$jKnowledge['KlID'];
                    $rightAmount=$newKlList[$jklID]['RightAmount']?$newKlList[$jklID]['RightAmount']:0;
                    $allAmount = $newKlList[$jklID]['AllAmount']?$newKlList[$jklID]['AllAmount']:0;
                    $result[$i]['sub'][$j]['KlID']=$jKnowledge['KlID'];
                    $result[$i]['sub'][$j]['KlName']=$jKnowledge['KlName'];
                    $result[$i]['sub'][$j]['rightAmount']=$rightAmount;
                    $result[$i]['sub'][$j]['allAmount']=$allAmount;
                    $result[$i]['sub'][$j]['correctRate']=round($rightAmount / ($allAmount?$allAmount:1), 2) * 100;
                }
            }
        }
        return $result;
    }

    /**
     * 返回学生数据，姓名 邮箱 等
     * @param int $userID 学生ID
     * @return array|bool|null 成功返回学生信息，失败返回false,没找到返回null
     * @author demo
     */
    private function studentInfo($userID){
        $field="UserName,RealName,Email,Phonecode,OrderNum,Sex";
        $where['UserID']=$userID;
        $userInfo = $this->getModel('User')->selectData(
            $field,
            $where,
            '',
            1
        );
        return $userInfo;
    }
    /**
     * 班级练习动态学情分析首页显示
     * @author demo
     */
    public function classExerciseIndex() {
        $this->display();
    }
    /**
     * 获取首页需要显示的数据ajax返回
     * @author demo
     */
    public function classExerciseData() {
        //班级列表
        $classID = $_POST['classID'];
        $page    = $_POST['page'];
        if(!is_numeric($page) || empty($page)){
            $page=1;
        }
        $result  = array();//返回数据

        //学生列表
        $classUserM = D('ClassUser');
        $student    = $classUserM->classStudents($classID);
        //班级练习列表
        if($student){
            $result['student']=$student;
            $userName=array();
            foreach($student as $i =>$iStudent){
                $userName[$iStudent['UserName']]=$iStudent['RealName'];
            }
            $subjectID=cookie('SubjectId')?cookie('SubjectId'):12;
            $exercise=$this->getClassExercise($userName,$subjectID,$page);
            if($exercise){
                $result['exercise']=$exercise['list'];
                $result['page']=$exercise['page'];
            }else{
                $result['exercise']='';
                $result['page']=[1,1];
            }
        }
        $this->setBack($result);
    }
    /**
     * 返回测试记录
     * @param $userNameArray array 以用户名为下标的用户真实姓名数组
     * @param $subjectID int 学科ID
     * @param $page int 页码，默认为1
     * @return mixed
     * @author demo
     */
    private function getClassExercise($userNameArray,$subjectID,$page=1) {
        // 导入分页类
        $map = array(
            'UserName' => array('in',array_keys($userNameArray)),
            'SubjectID' => $subjectID,
            'Score'=>array('neq',-1)
        );
        // 查询满足要求的总记录数
        $userTestRecord = $this->getModel('UserTestRecord');
        $count=$userTestRecord->selectCount(
            $map,
            'TestID'
        );

        $perPage = 8; //每页数量
        $pageStr=$page.','.$perPage;
        // 进行分页数据查询
        $list = $userTestRecord->pageData(
            'TestID,Style,Score,RealTime,Content,LoadTime,UserName',
            $map,
            'LoadTime desc',
            $pageStr
        );
        $configStyle = C('WLN_TEST_STYLE_NAME');
        foreach($list as $i=>$iList){
            $list[$i]['Style'] = $configStyle[$list[$i]['Style']];
            $list[$i]['RightAmount'] = (int)((substr_count($iList['Content'],',')+1)*$iList['Score']*0.01);
            $list[$i]['AllAmount'] = substr_count($iList['Content'],',')+1;
            $list[$i]['LoadTime'] = date('Y-m-d H:i',$iList['LoadTime']);
            $list[$i]['RealTime'] = (int)($iList['RealTime']/60)+1;
            $list[$i]['UserName'] = $userNameArray[$iList['UserName']];
        }
        $pageArr=array($count,$perPage);
        if($list){
            return $result=['list'=>$list,'page'=>$pageArr];
        }else{
            return false;
        }
    }

    /**
     * 获取学生个人练习列表
     * @author demo
     */
    public function studentExercise(){
        $studentID = $_POST['sid'];//学生ID
        $page      = $_POST['page'];//当前页
        if(!is_numeric($studentID) || $studentID==''){
            $this->setError('30301',1);
        }
        if(!is_numeric($page) || $page==''){
            $page=1;
        }
        $userInfo = $this->getModel('User')->findData(
            'UserName,RealName',
            'UserID='.$studentID
        );

        if($userInfo){
            $subjectID=cookie('SubjectId')?cookie('SubjectId'):12;
            $userNameArr[$userInfo['UserName']] = $userInfo['RealName'];
            $exerciseList=$this->getClassExercise($userNameArr,$subjectID,$page);
            $result=array();
            if($exerciseList){
                $result['exercise']=$exerciseList['list'];
                $result['page']=$exerciseList['page'];
            }else{
                $result['page']=[1,1];
            }
            $this->setBack($result);
        }
        $this->setError('30301',1);
    }

    /**
     * 返回测试详情
     * @author demo
     */
    public function getExerciseInfo(){
        $testID=$_POST['eid'];
        if(!is_numeric($testID) || empty($testID)){
            $this->setError('30301',1);
        }
        //获取试题ID列表
        $testIDList = $this->getModel('UserTestRecord')->findData(
            'Content',
            'TestID=' . $testID
        );
        //获取作答信息
        $answerBuffer = $this->getModel('UserAnswerRecord')->selectData(
            'TestID,AnswerID,AnswerText,IfRight,Number,OrderID,IfChoose',
            'TestRecordID='.$testID,
            'Number asc,OrderID asc'
        );
        $answerArr=array();
        if($answerBuffer){
            //将作答信息转换成以试题ID为下标的数组
            foreach($answerBuffer as $i=>$iAnswerBuffer){
                $answerArr[$iAnswerBuffer['TestID']][$iAnswerBuffer['OrderID']]=$iAnswerBuffer;
            }
            unset($answerBuffer);
        }
        //索引查找试题
        $TestList =$testIDList['Content'];
        $TestReal = $this->getModel('TestReal');
        $buffer=$TestReal->getTestIndex(
            array(
                'testid',
                'typesid',
                'typesname',
                'testnum',
                'test',
                'answer',
                'analytic',
                'diff',
                'docname',
                'firstloadtime'
            ),
            array('TestID'=>$TestList),
            '',
            array('page'=>1,'perpage'=>100)
        );
        if($buffer[0]){
            $buffer = R('Common/TestLayer/reloadTestArr',array($buffer[0]));
        }else{
            $this->setError('1X3070',1);
        }
        //组合试题信息
        $tmpArr=explode(',',$TestList);
        foreach($tmpArr as $i=>$iTmpArr){
            if($buffer[$iTmpArr]){
                $buffer[$iTmpArr]['u_answer']='';
                $buffer[$iTmpArr]['IfRight']=0;
                if($buffer[$iTmpArr]['analytic']=='' || $buffer[$iTmpArr]['analytic']=='</p>'){
                    $buffer[$iTmpArr]['analytic']='';
                }
                if($buffer[$iTmpArr]['testnum']>1){
                    for($j=0;$j<$buffer[$iTmpArr]['testnum'];$j++){
                        $tmpStr1=' <font color="red">（回答错误！）</font>';
                        if($answerArr[$iTmpArr][$j+1]['IfRight']==2)
                            $tmpStr1=' <font color="green">（回答正确！）</font>';
                        $buffer[$iTmpArr]['u_answer'].='【小题】'.(empty($answerArr[$iTmpArr][$j+1]['AnswerText']) ? '空' : $answerArr[$iTmpArr][$j+1]['AnswerText']).$tmpStr1;
                        $buffer[$iTmpArr]['IfRight'].='【小题】'.empty($answerArr[$iTmpArr][$j+1]['IfRight']) ? 0 : $answerArr[$iTmpArr][$j+1]['IfRight'];
                    }
                }else{
                    //判断用户答题：对，错，没有作答,无法判断的，各种提示
                    $option=array('A','B','C','D');
                    if($answerArr[$iTmpArr][0]['IfRight']==2 && in_array($answerArr[$iTmpArr][0]['AnswerText'],$option)){
                        $tmpStr1=' <font color="green">（回答正确！）</font>';
                    }elseif($answerArr[$iTmpArr][0]['AnswerText']==""){
                        $tmpStr1=' <font color="red">（没有作答！）</font>';
                    }elseif($answerArr[$iTmpArr][0]['IfRight']!=2 && in_array($answerArr[$iTmpArr][0]['AnswerText'],$option)){
                        $tmpStr1=' <font color="red">（回答错误！）</font>';
                    }else{
                        $tmpStr1=' <font color="red">（无法判断对错！）</font>';
                    }
                    $buffer[$iTmpArr]['IfRight']=$answerArr[$iTmpArr][0]['IfRight'];
                    $buffer[$iTmpArr]['u_answer']=(empty($answerArr[$iTmpArr][0]['AnswerText']) ? '空' : stripslashes($answerArr[$iTmpArr][0]['AnswerText'])).$tmpStr1;
                }

                $buffer[$iTmpArr]['error']=0;
                $output[]=$buffer[$iTmpArr];
            }else{
                $output[]=array('testid'=>$iTmpArr,'error'=>1);
            }
        }
        if($output){
            $this->setBack(array('success',$output));
        }else{
            $this->setError('1X3075',1);
        }
    }
}
