<?php
/**
 * @author demo
 * @date 2015年3月28日
 */
/**
 *校本题库之试题标引 操作类
 */
namespace Teacher\Controller;
class CustomIntroController extends BaseController{
    //学科ID
    private $subjectID='';
    //用户名
    private $userName="";
    //试题状态
    private $testStatus=array(
        '-3'=>'已过期',
        '-2'=>'失败',
        '-1'=>'失败返回',
        '0'=>'领取',
        '1'=>'审核中',
        '2'=>'完成'
    );

    public function __construct(){
        parent::__construct();
        //用户名
        $this->userName=$this->getCookieUserName();
    }

   /**
    * 优化任务列表
    * @author demo
    */
    public function taskTestList(){
        $pageName= '优化任务列表';
        $map=array();
        $time=time();
        $subjectID=$this->mySubject;
        $data='TaskTime <='.$time.' AND SubjectID='.$subjectID.' AND a.Status in (-2,-1,0)';
        //查询
        if(isset($_REQUEST['TestID'])) $testID=$_REQUEST['TestID'];//试题编号
        if(is_numeric($testID)){
            $map['TestID']=$testID;
            $data.=' AND a.TestID='.$testID;
        }
        //载入分页类
        $perPage = C('WLN_PERPAGE');
        $CustomTestTaskStatus = $this->getModel('CustomTestTaskStatus');
         // 查询满足要求的总记录
        $count=$CustomTestTaskStatus->unionSelect('customTestTaskStatusSelectCount', $data);
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $this->pageList($count, $perPage, $map);
        $page=page($count,$_GET['p'],$perPage). ',' . $perPage;
        //查询数据库
        $list = $CustomTestTaskStatus->unionSelect('customTestTaskStatusSelectByPage', $data,$page);
        $subjectList=SS('subject');
        foreach($list as $i=>$iList){
            $list[$i]['SubjectName']=$subjectList[$subjectList[$iList['SubjectID']]['PID']]['SubjectName'].$subjectList[$iList['SubjectID']]['SubjectName'];
        }
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 个人任务列表
     * @author demo
     */
    public function individualTestList(){
        $pageName= '个人任务列表';
        $map=array();
        $data='1=1';
        $userName=$this->userName; //用户名
        $data.=' And a.UserName="'.$userName.'"';
        //浏览谁的试题.区分学科
        if($this->ifSubject && $this->mySubject){
            $data .= 'and b.SubjectID in ('.$this->mySubject.') ';
        }
        if (isset($_REQUEST['name']) && $_REQUEST['name']!='') {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' and a.TestID='.$_REQUEST['name'];
        }else {
            //高级查询
            if (isset($_REQUEST['TestID']) && $_REQUEST['TestID']!='') {
                $map['TestID'] = $_REQUEST['TestID'];
                $data .= ' and a.TestID='.$_REQUEST['TestID'];
            }
            if (isset($_REQUEST['UserName'])) {
                $map['UserName'] = $_REQUEST['UserName'];
                $data .= ' and a.UserName like "%'.$_REQUEST['UserName'].'%"';
            }
            if (is_numeric($_REQUEST['Status'])) {
                if($_REQUEST['Status']==-3){
                    $data.=' And a.Status=0 And a.TaskTime <'.time();
                }else{
                    $map['Status'] = $_REQUEST['Status'];
                    $data .= ' and a.Status="'.$_REQUEST['Status'].'"';
                }
            }
            $start = $_REQUEST['Start'];
            $end = $_REQUEST['End'];
            if ($start) {
                if(!checkString('isDate',$_REQUEST['Start'])){
                    $start = date('Y-m-d', $start);
                }
                if(!checkString('isDate',$_REQUEST['End'])){
                    $end = date('Y-m-d', $end);
                }
                if (empty ($end))
                    $end = date('Y-m-d', time());
                if (!checkString('isDate',$start) || !checkString('isDate',$end)) {
                    $this->setError('30719'); //日期格式不正确
                }
                $map['Start'] = strtotime($start);
                $map['End'] = strtotime($end);
                $data .= ' AND a.AddTime between ' . strtotime($start) . ' and ' . strtotime($end) . ' ';
            }
        }
        $perPage =C('WLN_PERPAGE');
        $CustomTestTaskList = $this->getModel('CustomTestTaskList');
        $count = $CustomTestTaskList->unionSelect('customTestTaskListSelectCount',$data);// 查询满足要求的总记录
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=page($count,$_GET['p'],$perPage). ',' . $perPage;
        $list = $CustomTestTaskList->unionSelect('customTestTaskListSelectByPage',$data,$page);
        if($list){
            foreach($list as $i=>$iList){
                $list[$i]['TaskStartTime'] = $iList['TaskTime']-C('WLN_TASK_TIMEOUT');
                if($iList['TestStatus']==0 && $iList['TaskTime']<time()){
                    $iList['TestStatus']='-3';//已过期
                }
                if($iList['TestStatus']==-1 && $iList['TaskTime']<time()){
                    $iList['TestStatus']='-4';
                }
                switch ($iList['TestStatus']) {
                    case '-4':
                        $list[$i]['StatusName']='失败返回已过期';
                        $list[$i]['IfDo']=0;
                        break;
                    case '-3':
                        $list[$i]['StatusName']='已过期';
                        $list[$i]['IfDo']=0;
                        break;
                    case '-2':
                        $list[$i]['StatusName']='失败';
                        $list[$i]['IfDo']=0;
                        break;
                    case '-1':
                        $list[$i]['StatusName']='失败返回';
                        $list[$i]['IfDo']=1;
                        break;
                    case '0':
                        $list[$i]['StatusName']='领取';
                        $list[$i]['IfDo']=1;
                        break;
                    case '1':
                        $list[$i]['StatusName']='审核中';
                        $list[$i]['IfDo']=0;
                        break;
                    case '2':
                        $list[$i]['StatusName']='完成';
                        $list[$i]['IfDo']=0;
                        break;
                }
            }
        }
        $testStatus=$this->testStatus;
        $this->pageList($count, $perPage, $map);//载入分页
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('testStatus', $testStatus); // 状态
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 更改试题锁定状态
     * @param $status int 试题状态
     * @param $testID int 试题ID
     * @return bool
     * @author demo
     */
    private function  ifLock($status,$testID){
        $statusData['IfLock']=$status;
        $re=$this->getModel('CustomTestTaskStatus')->updateData(
            $statusData,
            'TestID='.$testID
        );
        return $re?true:false;
    }
    /**
     * 判断是否可以领取任务
     * @param $userName string 用户名
     * @param $testID int 试题ID
     * @return bool
     * @author demo
     */
    private function ifGetTaskTest($userName,$testID){
        //验证是否和领取人的学科一致
        $subjectID=$this->mySubject;
        $subject=$this->getModel('CustomTestAttr')->findData(
            'SubjectID',
            'TestID='.$testID
        );
        if($subject['SubjectID']!=$subjectID){
            return true;
        }
        //验证是否已经被领取
        $time=time();
        $CustomTestTaskList = $this->getModel('CustomTestTaskList');
        $testID=$CustomTestTaskList->findData(
            'TestID',
            'TestID='.$testID.' AND TaskTime>'.$time
        );
        if($testID){
            return true;
        }
        //查询是否有未完成任务的试题，包括未提交，及失败返回未超时的，超过1条就不可以
        //1存在未超时的任务 和  Status为0（已领取）-1（失败返回的）
        $where='UserName="'.$userName.'" AND Status in(0,-1) AND TaskTime>'.$time;
        $count=$CustomTestTaskList->selectCount(
            $where,
            'ListID'
        );
        if($count>1){
            return true;
        }
        return false;
    }
    /**
     * 判断任务是否可操作
     * @param $testID int 试题ID
     * @author demo
     */
    protected function ifDo($testID){
        //判断数据标识
        if(!is_numeric($testID) || empty($testID)){
            //数据错误
            $this->setError('30301',NORMAL_ERROR,U('CustomIntro/individualTestList'));
        }
        //用户名
        $userName=$this->userName;
        //查询试题任务信息
        $testInfo=$this->getModel('CustomTestTaskList')->selectData(
            '*',
            'TestID='.$testID.' AND UserName="'.$userName.'"',
            'AddTime DESC'
        );
        //是否存在
        if(!$testInfo){
            $this->setError('40112',NORMAL_ERROR,U('CustomIntro/individualTestList'));
        }
        //是否过期（状态为领取和失败返回的试题，过期时间小于当前的）
        $taskTime=$testInfo[0]['TaskTime'];
        $status=$testInfo[0]['Status'];
        $time=time();
        if($taskTime<=$time && in_array($status,array(0,-1))){
            //$this->ifLock(0,$testID);
            $this->setError('40124',NORMAL_ERROR,U('CustomIntro/individualTestList'));
        }
        //是否可操作（处于审核状态，审核完成，失败的试题不可操作）
        if(in_array($status,array(-2,1,2))){
            $this->setError('40119',NORMAL_ERROR,U('CustomIntro/individualTestList'));
        }


    }
    /**
     * 领取任务
     * @author demo
     */
    public function getTaskTest(){
        //获取领取任务ID
        $testID=$_GET['testID'];
        //用户名
        $userName=$this->userName;

        if($this->ifGetTaskTest($userName,$testID)){
            //不能领取任务
            $this->setError('40122',NORMAL_ERROR,U('CustomIntro/taskTestList'));
        }
        if(!is_numeric($testID)){
            $this->setError('30502');
        }
        $status=array();//任务状态数据

        //更新试题-----------------------
        $CustomTestCopy = $this->getModel('CustomTestCopy');
        $testCopy=$CustomTestCopy->findData(
            'TestID',
            'TestID='.$testID
        );
        //是否是第一次被领取
        $test=$this->getModel('CustomTest')->selectData(
            '*',
            'TestID='.$testID
        );
        if(!$testCopy){
            //写入副表
            $testCopy=$CustomTestCopy->insertData(
                $test[0]
            );
        }else{
            unset($test[0]['TestID']);
            $testCopy = $CustomTestCopy->updateData(
                $test[0],
                'TestID='.$testID
            );
        }
        if($testCopy === false){
            $this->setError('04033');
        }

        //更新试题属性-----------------------
        $CustomTestAttrCopy = $this->getModel('CustomTestAttrCopy');
        $attrCopy=$CustomTestAttrCopy->findData(
            'TestID',
            'TestID='.$testID
        );
        $attr = $this->getModel('CustomTestAttr')->selectData(
            '*',
            'TestID='.$testID
        );
        unset($attr[0]['IsTpl']); //删除是否原创模板试题的字段
        if(!$attrCopy){
            $attrCopy=$CustomTestAttrCopy->insertData(
                $attr[0]
            );
        }else{
            unset($attr[0]['TestID']);
            $attrCopy = $CustomTestAttrCopy->updateData(
                $attr[0],
                'TestID='.$testID
            );
        }
        if($attrCopy === false){
            $this->setError('04033');
        }

        //更新知识点-----------------------
        $knowledgeList = $this->getModel('CustomTestKnowledge')->selectData(
            'KlID',
            'TestID='.$testID
        );
        $knowledge = array();
        foreach($knowledgeList as $value){
            $knowledge[] = $value['KlID'];
        }
        $data = array(
            'testid' => $testID,
            'knowledge' => $knowledge
        );
        $this->getModel('CustomTestKnowledgeCopy')->saveData($data);

        //更新章节-----------------------
        $chapterList = $this->getModel('CustomTestChapter')->selectData(
            'ChapterID',
            'TestID='.$testID
        );
        $chapter = array();
        foreach($chapterList as $value){
            $chapter[] = $value['ChapterID'];
        }
        $data = array(
            'testid' => $testID,
            'chapter' => $chapter
        );
        $this->getModel('CustomTestChapterCopy')->saveData($data);

        //更新小题-----------------------
        $judgeList=$this->getModel('CustomTestJudge')->selectData(
            'OrderID as no, IfChoose as type',
            'TestID='.$testID
        );
        $this->getModel('CustomTestJudgeCopy')->saveData($testID, $judgeList);
        $status['IfIntro']=0;
        $status['ErrorMsg']='0';
        $status['IfDel']=0;

        //修改优化状态表中的过期时间
        $taskTime=time()+C('WLN_TASK_TIMEOUT');
        $status['TaskTime']=$taskTime;
        $status['IfLock']=1;
        $this->getModel('CustomTestTaskStatus')->updateData(
            $status,
            'TestID='.$testID
        );
        //写入领取任务表
        $data=array();
        $data['TestID']=$testID;
        $data['Status']=0;
        $data['AddTime']=time();
        $data['TaskTime']=$taskTime;
        $data['UserName']=$userName;
        $taskStatusList=$this->getModel('CustomTestTaskList')->insertData(
            $data
        );
        if($taskStatusList){
            //记录任务领取日志
            $this->customLog($testID,'领取',$userName,'领取TestID为【' . $testID . '】的标引任务！');
            //返回提示信息
            // $this->showSuccess('领取成功！',__APP__.'/Teacher-CustomIntro-similarTestList-testID-'.$testID.'.html');
            $this->showSuccess('领取成功！',U('Teacher/CustomIntro/similarTestList', array('testID'=>$testID)));
        }else{
            $this->setError('40123');
        }
    }
    /**
     * 放弃 任务
     * @author demo
     */
    public function  ajaxAbortMission(){
        //试题ID
        $testID=$_POST['tid'];
        if(!is_numeric($testID)){
            $this->setError('30502');
        }
        $userName = $this->userName; //用户名
        //该试题状态，处于审核状态的不能放弃,不是自己的题不能放弃，过期的题不能放弃
        //本人的、优化中的、未过期的试题，
        $time=time();
        $where='TestID='.$testID.' AND UserName="'.$userName.'" AND TaskTime>'.$time.' AND Status in (-1,0)';
        $status=$this->getModel('CustomTestTaskList')->findData(
            'Status',
            $where
        );
        if(!$status){
            //不正常操作
            $this->setError('40119');
        }
        $CustomTestTaskStatus = $this->getModel('CustomTestTaskStatus');
        $oldStatus=$CustomTestTaskStatus->findData(
            'TaskTime,BackTimes',
            'TestID='.$testID
        );
        $data=array();
        $data['TaskTime']=$time;
        $data['BackTimes']=$oldStatus['BackTimes']+1;
        //修改试题状态表为待优化，过期时间为当前，返回次数加1
        $newStatus=$CustomTestTaskStatus->updateData(
            $data,
            'TestID='.$testID
        );
        //修改教师任务表状态为失败-2
        $taskList=array();
        $taskList['TaskTime']=$time;
        $taskList['Status']=-2;
        $this->getModel('CustomTestTaskList')->updateData(
            $taskList,
            $where
        );
        //扣分,目前扣一分，等标准出来了，按照标准扣分
        $this->getModel('User')->conAddData(
            'Cz=Cz-1',
            'UserName="'.$userName.'"'
        );
        //记录放弃任务日志
        $this->customLog($testID,'放弃',$userName,'放弃试题TestID为【' . $testID . '】的标引任务');
        $this->setBack('success');
    }
    /**
     * 相似题
     * @author demo
     */
    public function similarTestList(){
        set_time_limit(0);
        $pageName = '相似题';
        $testID = $_GET['testID']; //获取数据标识
        $testReal = $this->getModel('TestReal');
        //验证是否可操作
        $this->ifDo($testID);
        //查询条件
        $list=$this->getModel('CustomTestCopy')->unionSelect('customTestCopySelectById', $testID);
        $weight=0.9;
        $firstWeight=0;
        if($list){
            $field=array('testid','weight','test','docid','docname','duplicate');
            $order=array();
            $page=array('perpage'=>5);
            $where['Duplicate']=0;//去除重复数据
            $where['red']=0;
            $list[0]['Test']=formatString('IPReturn',stripslashes_deep($list[0]['Test']));//试题路径
            //去掉试题题文HTML标签
            $where['key']=preg_replace('/<[^>]*>| |　|\s|&nbsp;/i','',$list[0]['Test']);
            //限定学科条件，增加匹配准确度
            $where['SubjectID']=$list[0]['SubjectID'];
            $where['TypesID']=$list[0]['TypesID'];
            $result=$testReal->getTestIndex($field,$where,$order,$page);//获取重复试题题文集
            if($result === false){
                $this->setError('30504', (IS_AJAX ? 1 : 0));
            }
            if(!empty($result[0])){
                //去除重复数据
                    $firstWeight=$result[0][0]['weight']*$weight;
                    $list[0]['duplicate'][0]=$result[0][0];
                    if($result[0][1]['weight']>$firstWeight) $list[0]['duplicate'][1]=$result[0][1];
                    unset($result[0][0]);
                    unset($result[0][1]);
                    if(!empty($result[0])){
                        foreach ($result[0] as $j=> $re){
                            if($re['weight']>$firstWeight){
                                $list[0]['duplicate']['ids'][$j]['testid']=$re['testid'];//取得其他重复试题ID集
                                $list[0]['duplicate']['ids'][$j]['duplicate']=$re['duplicate'];
                            }
                        }
                    }
                }
        }

        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('testID',$testID);
        $this->display();
    }
    /**
     * 标记相似试题
     * @author demo
     */
    public function markSimilarTest(){
        //修改试题属性 规则：
        //1.凡是被标记为相似试题的，将库内试题的属性复制到任务试题的属性上
        $testID=$_POST['id']; //获取数据标识
        $duplicate=$_POST['Duplicate'];//获取重复试题ID
        //判断数据标识
        if (!is_numeric($duplicate)||empty($duplicate)) {
            $this->setError('30301',AJAX_ERROR);
        }
        //验证是否可操作
        $this->ifDo($testID);

        $userName = $this->userName; //用户名
        $testAttrCopy=$this->getModel('CustomTestAttrCopy')->findData(
            'TestID',
            'TestID='.$testID
        );

        $attrReal=$this->getModel('TestAttrReal')->findData(
            '*',
            'TestID='.$duplicate
        );
        //获取试题的章节
        $chapterRealBuffer=array();
        $buffer=$this->getModel('TestChapterReal')->selectData(
            'TestID,ChapterID',
            'TestID='.$duplicate,
            'TCID ASC'
        );
        foreach($buffer as $i=>$iBuffer){
            $chapterRealBuffer[]=$iBuffer['ChapterID'];
        }
        //获取试题的知识点
        $klRealBuffer=array();
        $buffer=$this->getModel('TestKlReal')->selectData(
            'TestID,KlID',
            'TestID='.$duplicate,
            'TKlID ASC'
        );
        foreach($buffer as $i=>$iBuffer){
            $klRealBuffer[]=$iBuffer['KlID'];
        }
        unset($buffer);
        $time=time();
        if($attrReal){
            //复制属性
            $data=array();
            $data['TestID']=$testID;
            $data['TypesID']=$attrReal['TypesID'];
            $data['SpecialID']=$attrReal['SpecialID'];
            $data['SubjectID']=$attrReal['SubjectID'];
            $data['Diff']=$attrReal['Diff'];
            $data['AddTime']=$time;
            $data['LastUpdateTime']=$time;
            $data['GradeID']=$attrReal['GradeID'];
            $data['Mark']=$attrReal['Mark'];
            $data['DfStyle']=$attrReal['DfStyle'];
            $data['IfChoose']=$attrReal['IfChoose'];
            $data['TestNum']=$attrReal['TestNum'];
            $data['TestStyle']=$attrReal['TestStyle'];
            $data['OptionWidth']=$attrReal['OprionWidth'];
            $data['OptionNum']=$attrReal['OptionNum'];
            //如果有属性则更新，没有则添加
            if($testAttrCopy){
                $this->getModel('CustomTestAttrCopy')->updateData(
                    $data,
                    'TestID='.$testID
                );
            }else{
                $this->getModel('CustomTestAttrCopy')->insertData(
                    $data
                );
            }
            //复制章节
            $CustomTestChapterCopy = $this->getModel('CustomTestChapterCopy');
            if($chapterRealBuffer){
                $CustomTestChapterCopy->deleteData(
                    'TestID='.$testID
                );
                foreach($chapterRealBuffer as $i=>$iChapterRealBuffer){
                    $data=array();
                    $data['TestID']=$testID;
                    $data['ChapterID']=$iChapterRealBuffer;
                    $CustomTestChapterCopy->insertData(
                        $data
                    );
                }
            }
            //复制知识点
            $CustomTestKnowledgeCopy = $this->getModel('CustomTestKnowledgeCopy');
            if($klRealBuffer){
                $CustomTestKnowledgeCopy->deleteData(
                    'TestID='.$testID
                );
                foreach($klRealBuffer as $i=>$iKlRealBuffer){
                        $data=array();
                        $data['TestID']=$testID;
                        $data['KlID']=$iKlRealBuffer;
                        $CustomTestKnowledgeCopy->insertData($data);
                }
            }
            //修改任务状态
            $taskListData=array();
            $taskListData['Status']=1;
            $taskListData['TaskTime']=$time;
            $this->getModel('CustomTestTaskList')->updateData(
                $taskListData,
                'TestID='.$testID.' AND TaskTime>'.$time
            );
            $taskListData['IfDel']=0;
            $taskListData['IfIntro']=$duplicate;
            $CustomTestTaskStatus = $this->getModel('CustomTestTaskStatus');
            $CustomTestTaskStatus->updateData(
                $taskListData,
                'TestID='.$testID
            );
            //更新用户编辑次数
            $CustomTestTaskStatus->conAddData(
                'EditTimes=EditTimes+1',
                'TestID='.$testID
            );
            //写入日志
            $this->customLog($testID,'相似题提交标引',$userName, '完成试题TestID为【' . $testID . '】数据的相似题标引工作');
        }
        $output='<script>alert("标记成功");</script>';
        $this->setBack($output);
    }
    /**
     * 标引试题
     * @author demo
     */
    public function introTest(){
        $testID = $_GET['testID'];
        //验证是否可操作
        $this->ifDo($testID);
        $userName=$this->userName;

        //生成验证信息
        $securityCode = md5($userName.$testID.C('TEST_KEY'));
        //查询试题信息
        $edit = $this->getModel('CustomTestCopy')->unionSelect('customTestCopySelectById',$testID);
        $edit = $edit[0];
        //该试题的任务状态信息
        $taskStatus=$this->getModel('CustomTestTaskStatus')->findData(
            'Status,IfDel,ErrorMsg',
            'TestID='.$testID
        );
        $edit['IfDel']=$taskStatus['IfDel'];
        $edit['statusErrorMsg']=$taskStatus['ErrorMsg'];
        $edit['listErrorMsg']='';
        if($taskStatus['Status']==-1){
            $listError=$this->getModel('CustomTestTaskList')->findData(
                'ErrorMsg',
                'TestID='.$testID.' AND Status=-1'
            );
            $edit['listErrorMsg']=$listError['ErrorMsg'];
        }
        //获取题型名称
        $subjectID=$this->mySubject;
        $param['style']='types';
        $param['subjectID'] = $subjectID;
        $param['return'] = 2;
        $types=$this->getData($param);
        //年级
        $grade['style']='grade';
        $grade['subjectID']=$subjectID;
        $grade['return']=2;
        $gradeList=$this->getData($grade);
        //试题对应知识点
        $buffer = $this->getModel('CustomTestKnowledgeCopy')->selectData(
            '*',
            'TestID='.$testID
        );
        $edit['KlID']=0;
        if($buffer){
            $arrTemp=array();
            foreach($buffer as $iBuffer){
                $arrTemp[]=$iBuffer['KlID'];
            }
            $edit['KlID']=implode(',',$arrTemp);
        }
        //试题对应章节
        $buffer = $this->getModel('CustomTestChapterCopy')->selectData(
            '*',
            'TestID='.$testID
        );
        $edit['ChapterID']=0;
        if($buffer){
            $arrTemp=array();
            foreach($buffer as $iBuffer){
                $arrTemp[]=$iBuffer['ChapterID'];
            }
            sort($arrTemp);
            $edit['ChapterID']=implode(',',$arrTemp);
        }
        //试题对应难度
        if(strstr($edit['Mark'],'@')){
            $arr=explode('@',$edit['Mark']);
            foreach($arr as $i=>$iArr){
                $edit['Markx'][$i+1]=array_filter(explode('#',$iArr));
            }
        }else{
            $edit['Markx'][1]=array_filter(explode('#',$edit['Mark']));
        }

        //自定义打分
        $markArray=$this->getModel('TestMark')->selectData(
            '*',
            'SubjectID = '.$edit['SubjectID'],
            'Style asc,OrderID asc'
        );
        if($markArray){
            foreach($markArray as $i=>$iMarkArray){
                $markArray[$i]['MarkListx']=formatString('str2Arr',$markArray[$i]['MarkList']);
                foreach($markArray[$i]['MarkListx'] as $j=>$jMarkArray){
                    $markArray[$i]['MarkListx'][$j][3]=$markArray[$i]['MarkID'].'|'.$markArray[$i]['MarkListx'][$j][0];
                }
            }
        }
        $Test=$this->getModel('Test');
        //自定义打分次数
        $times=$Test->xtnum($edit['Test'],1);
        if(!$times) $times=1;

        //试题路径
        $edit['Test']=formatString('IPReturn',stripslashes_deep($edit['Test']));
        $edit['Answer']=formatString('IPReturn',stripslashes_deep($edit['Answer']));
        $edit['Analytic']=formatString('IPReturn',stripslashes_deep($edit['Analytic']));
        $edit['Remark']=$edit['Remark'];
        /*载入模板标签*/
        $this->assign('edit', $edit);
        $this->assign('times', $times);
        $this->assign('securityCode',$securityCode);
        $this->assign('mark_array', $markArray);
        $this->assign('typesArray',$types);
        $this->assign('gradeList',$gradeList);
        $this->assign('tid',$testID);
        $this->display();
    }
    /**
     * 提交标引任务
     * @author demo
     */
    public function saveTaskTest(){
        $testID = $_POST['testID'];

        //验证是否可操作
        $this->ifDo($testID);

        //验证数据合法性
        $key=$_POST['s'];
        $userName=$this->userName;
        if($key!=md5($userName.$testID.C('TEST_KEY'))){
            $this->setError('40119',NORMAL_ERROR,U('CustomIntro/individualTestList'));
        }
        //接收数据
        $kl=$_POST['kl'];
        $cp=$_POST['cp'];
        $specialID=$_POST['SpecialID'];
        $mark=$_POST['Mark'];
        $dfStyle=$_POST['DfStyle'];
        //更新试题
        $test=array();
        $test['Test'] = formatString('IPReplace',$_POST['Test']);
        $test['Answer'] = formatString('IPReplace',$_POST['Answer']);
        $test['Analytic'] = formatString('IPReplace',$_POST['Analytic']);
        $test['Source'] = $_POST['Source'];
        $test['Remark'] = $_POST['Remark'];
        $this->getModel('CustomTestCopy')->updateData(
            $test,
            'TestID='.$testID
        );
        //提取出复合题内容
        $attributes = $_POST['attributes'];
        $complex = $attributes['complex'];
        $style = array();
        foreach($complex as $i=>$iComplex){
            $style[] = $iComplex['type'];  //获取小题题型
        }

        //更改状态
        $CustomTestKnowledgeCopy = $this->getModel('CustomTestKnowledgeCopy');
        $buffer=$CustomTestKnowledgeCopy->selectData(
            '*',
            'TestID='.$testID
        );
        //保存考点
        if($kl){
            $kl=explode(',',$kl);
            if($buffer){
                for($i=0;$i<count($buffer);$i++){
                    if($i>=count($kl)){
                        $CustomTestKnowledgeCopy->deleteData(
                            'TklID ='.$buffer[$i]['TklID'].' '
                        );
                        continue;
                    }
                    $data=array();
                    $data['TestID']=$testID;
                    $data['KlID']=$kl[$i];
                    $data['TklID']=$buffer[$i]['TklID'];
                    $CustomTestKnowledgeCopy->updateData(
                        $data,
                        'TklID='.$buffer[$i]['TklID']
                    );
                }
                if($i<count($kl)){
                    for($i=0;$i<count($kl);$i++){
                        $data=array();
                        $data['TestID']=$testID;
                        $data['KlID']=$kl[$i];
                        $CustomTestKnowledgeCopy->insertData(
                            $data
                        );
                    }
                }
            }else{
                foreach($kl as $iKl){
                    $data=array();
                    $data['TestID']=$testID;
                    $data['KlID']=$iKl;
                    $CustomTestKnowledgeCopy->insertData(
                        $data
                    );
                }
            }
        }else if($buffer){
            $knowledgeList=array();
            foreach($buffer as $iBuffer){
                $knowledgeList[]=$iBuffer['TklID'];
            }
            $CustomTestKnowledgeCopy->deleteData(
                'TklID in ('.implode(',',$knowledgeList).')'
            );
        }

        //保存章节
        $CustomTestChapterCopy = $this->getModel('CustomTestChapterCopy');
        $buffer=$CustomTestChapterCopy->selectData(
            '*',
            'TestID='.$testID
        );
        $cp=explode(',',$cp);
        if($cp){
            if($buffer){
                for($i=0;$i<count($buffer);$i++){
                    if($i>=count($cp)){
                        $CustomTestChapterCopy->deleteData(
                            'TCID ='.$buffer[$i]['TCID'].' '
                        );
                        continue;
                    }
                    $data=array();
                    $data['TestID']=$testID;
                    $data['ChapterID']=$cp[$i];
                    $data['TCID']=$buffer[$i]['TCID'];
                    $CustomTestChapterCopy->updateData(
                        $data,
                        'TCID ='.$buffer[$i]['TCID'].' '
                    );
                }
                if($i<count($cp)){
                    for(;$i<count($cp);$i++){
                        $data=array();
                        $data['TestID']=$testID;
                        $data['ChapterID']=$cp[$i];
                        $CustomTestChapterCopy->insertData(
                            $data
                        );
                    }
                }
            }else{
                foreach($cp as $iCp){
                    $data=array();
                    $data['TestID']=$testID;
                    $data['ChapterID']=$iCp;
                    $CustomTestChapterCopy->insertData(
                        $data
                    );
                }
            }
        }else if($buffer){
            $chapterList=array();
            foreach($buffer as $iBuffer){
                $chapterList[]=$iBuffer['TCID'];
            }
            $CustomTestChapterCopy->deleteData(
                'TCID in ('.implode(',',$chapterList).')'
            );
        }

        //更改属性
        $data=array();
        $complexSize = count($complex);
        $styleSize = count($style);
        $data['GradeID']=$_POST['GradeID'];
        $data['TypesID']=$_POST['TypesID'];
        $data['DfStyle']=$dfStyle;
        $data['SpecialID']=$specialID;
        $data['TestNum'] = $complexSize;
        //计算试题包含小题的类型(styles)
        if(in_array(0,$style) && $styleSize > 1){
            $data['TestStyle'] = 2;
        }else if($styleSize > 1 || $styleSize == 0){
            $data['TestStyle'] = 3;
        }else{
            $data['TestStyle'] = 1;
        }
        //如果有小题难度项目按照小题分组
        $diff=0;
        if($mark){
            $testData=C('WLN_TEST_DATA');//难度和分值转换数组
            $mark=explode(',',$mark); //所有mark分组
            $buffer=$this->getModel('CustomTestCopy')->selectData(
                '*',
                'TestID='.$testID
            );   //提取试题信息作为判断小题数的依据
            $testM=$this->getModel('Test');
            $times=$testM->xtnum($buffer[0]['Test'],1);    //小题数量
            if($times){    //如果存在分组
                $ci=count($mark)/$times;    //每个分组的打分项数目
                $str='';
                $diffArr=0;
                foreach($mark as $i=>$iMark){
                    //小题分组字符串
                    if($i%$ci==0 && $i!=0){
                        $diffArr+=$testData[$diff];
                        $diff=0;
                        $str.='@#';//分组字符串标记
                    }
                    if($iMark) $str.=$iMark.'#';
                    //分组计算分值
                    $n=array();
                    $n=explode('|',$iMark);
                    if($n[1]>=1) $diff+=$n[1];
                }
                $diff=$diffArr+$testData[$diff];
                $data['Mark']='#'.$str;
            }else{
                $mark=array_filter($mark);
                $data['Mark']='#'.implode('#',$mark).'#';
                //计算分值
                foreach($mark as $iMark){
                    $n=array();
                    $n=explode('|',$iMark);
                    if($n[1]>=1) $diff+=$n[1];
                }
                $diff=$testData[$diff];
            }
            //计算辅助因素
            $diffXs=0;
            if($mark){
                foreach($mark as $iMark){
                    $n=array();
                    $n=explode('|',$iMark);
                    if($n[1]<1) $diffXs+=$n[1];
                }
            }
            $diff+=$diffXs;
            if($times) $diff=round($diff/$times,4);    //求多小题的平均值
        }

        if($dfStyle) $diff=$_POST['Diff'];
        if(empty($diff)) $diff=0;
        $data['Diff']=$diff;
        $CustomTestAttrCopy = $this->getModel('CustomTestAttrCopy');
        if($CustomTestAttrCopy->selectData('*','TestID='.$testID)){
            $CustomTestAttrCopy->updateData(
                $data,
                'TestID='.$testID
            );
        }else{
            $data['TestID']=$testID;
            $CustomTestAttrCopy->insertData(
                $data
            );
        }
        //更改任务状态
        $time=time();
        $taskStatus=array();
        $taskStatus['Status']=1;
        $taskStatus['TaskTime']=$time;
        $statusWhere='TestID='.$testID.' AND TaskTime>'.$time;
        $this->getModel('CustomTestTaskList')->updateData(
            $taskStatus,
            $statusWhere
        );
        $taskStatus['IfIntro']=0;//重置库内题属性
        $taskStatus['IfDel']=$_POST['IfDel'];
        $taskStatus['ErrorMsg']=$_POST['ErrorMsg'];
        $CustomTestTaskStatus = $this->getModel('CustomTestTaskStatus');
        $CustomTestTaskStatus->updateData(
            $taskStatus,
            $statusWhere
        );
        $CustomTestTaskStatus->conAddData(
            'EditTimes=EditTimes+1',
            'TestID='.$testID
        );

        //写入日志
        $this->customLog($testID,'提交审核',$userName, '完成试题TestID为【' . $testID . '】数据的标引工作');
        //更新页面数据
        $this->setBack('success');
    }
}