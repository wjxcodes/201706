<?php
/**
 * @author demo
 * @date 15-3-26 下午5:35
 * @update 15-3-26 下午5:35
 */
/**
 * 个人题库试题审核类，用于个人题库试题的审核操作
 */
namespace Teacher\Controller;
class CustomCheckController extends BaseController {
    private $moduleName = '个人题库审核';
    /**
     * 个人题库审核任务列表
     * @author demo
     */
    public function index() {
        $pageName='审核任务列表';
        $userName=$this->getCookieUserName();
        $userInfo=$this->getModel('User')->selectData(
            'SubjectStyle',
            'UserName="'.$userName.'"'
        );
        if(!($userInfo)){
            $this->assign('pageName',$pageName);
            $this->display();
            exit;
        }
        $map=array();
        $where='attr.SubjectID='.$userInfo[0]['SubjectStyle'].' AND status.Status=1';
        $map['SubjectID']=$userInfo[0]['SubjectStyle'];
        $map['Status']=1;
        if($_POST['Status']){
            $_REQUEST['Status']=$_POST['Status'];
        }
        //简单查询
        if(isset($_REQUEST['TestID']) && $_REQUEST['TestID']!=''){
            $map=array();
            $where='attr.SubjectID='.$userInfo[0]['SubjectStyle'].' AND status.status<>0';
            $where.=' AND status.TestID='.$_REQUEST['TestID'];
            $map['TestID']=$_REQUEST['TestID'];
        }elseif(isset($_REQUEST['Status']) && $_REQUEST['Status']!=''){         //高级查询
            $map=array();
            $where='attr.SubjectID='.$userInfo[0]['SubjectStyle'].' AND status.status<>0';
            $map['SubjectID']=$userInfo[0]['SubjectStyle'];
            if(!in_array($_REQUEST['Status'],['all',0,2])){
                $where.=' AND status.Status='.$_REQUEST['Status'];
                $map['Status']=$_REQUEST['Status'];
            }else if($_REQUEST['Status']=='0'){
                $where.=' AND status.Status=2 AND status.DocPath=""';
                $map['Status']=0;
            }else if($_REQUEST['Status']==2){
                $where.=' AND status.Status=2 AND status.DocPath<>""';
                $map['Status']=2;
            }else if($_REQUEST['Status']=='all'){
                $map['Status']='all';
            }
        }

        $perPage=C('WLN_PERPAGE');
        $model=$this->getModel('CustomTestTaskStatus');
        $testStatus=$model->getCustomtestStatus();;
        $testStatus[0]='试题无法下载';
        $testStatus[1]='待审核';
        $testStatus['all']='全部';
        $count = $this->getModel('CustomTestAttr')->unionSelect('CustomTestStatusSelectByCount', $where);// 查询满足要求的总记录数
        $page=page($count,$_GET['p'],$perPage). ',' . $perPage;
        $list=$model->unionSelect('customTestSelectByPage',$where,$page,'status.AddTime Desc');
        if(!empty($list)){
            //循环得到需要查询的TestAuthorID和TaskUserName用户查询UserName和RealName值
            $whereUsername = [];
            $whereUserID = [];
            foreach($list as $i=>$iList){
                $whereUserID[] = $iList['TestAuthorID'];
                $whereUsername[] = $iList['TaskUserName'];
            }
            //查询User表数据
            $userDb =$this->getModel('User')->selectData(
                'UserID,UserName,RealName',
                ['UserID'=>['in',$whereUserID],'UserName'=>['in',$whereUsername],'_logic'=>'OR']
                );
            //遍历UserDb数据为UserID和UserName为键的数据，方便后续循环操作获取RealName
            $dataUserID = [];
            $dataUserName = [];
            foreach($userDb as $i=>$iUserDb){
                $dataUserID[$iUserDb['UserID']] = $iUserDb;
                $dataUserName[$iUserDb['UserName']] = $iUserDb;
            }
            foreach($list as $i=>$iList){
                //处理用户数据
                $list[$i]['TestAuthorName'] = $dataUserID[$iList['TestAuthorID']]['UserName'];
                $list[$i]['TestAuthorRealName'] = $dataUserID[$iList['TestAuthorID']]['RealName'];
                $list[$i]['TaskUserName'] = $iList['TaskUserName'];
                $list[$i]['TaskRealName'] = $dataUserName[$iList['TaskUserName']]['RealName'];
                //处理状态
                switch($iList['Status']){
                    case '1':
                        $list[$i]['StatusName']='待审核';
                        break;
                    case '-1':
                        $list[$i]['StatusName']='审核不通过返回';
                        break;
                    case '-2':
                        $list[$i]['StatusName']='审核失败';
                        break;
                    default:
                        break;
                }
                if($iList['Status']==2 && $iList['DocPath']==''){
                    $list[$i]['StatusName']='试题无法下载';
                }else if($iList['Status']==2 && $iList['DocPath']!=''){
                    $list[$i]['StatusName']='审核完成';
                }
            }
        }

        $this->pageList($count,$perPage,$map); //载入分页
        //载入模板标签
        $this->assign('testStatus',$testStatus);//试题状态
        $this->assign('list',$list);
        $this->assign('pageName',$pageName);
        $this->display();
    }

    /**
     * 个人题库审核
     * @author demo
     */
    public function checkWork(){
        $pageName='试题审核';
        $statusID=$_GET['statusID'];
        if(!is_numeric($statusID)){
            $this->setError('30502');
        }
        $statusInfo=$this->getModel('CustomTestTaskStatus')->selectData(
            '*',
            'StatusID='.$statusID
        );
        $realTag=1; //是否异常题
        $ifIntro=0;//是否库内题
        $realInfo = $this->getModel('CustomTestAttr')->unionSelect('customTestSelectById', $statusInfo[0]['TestID']);
        if($statusInfo[0]['IfIntro']!=0){             //库内题
            $realTag=0;
            $ifIntro=1;
            $introInfo=$this->getModel('TestReal')->unionSelect('testRealByID',$statusInfo[0]['IfIntro']);
            //  2015-6-5 修改库内题图片不正确问题
            $model =$this->getModel('Base');
            $host = C('WLN_DOC_HOST');
            $introInfo[0]['Test'] = R('Common/TestLayer/strFormat',array($introInfo[0]['Test']));
            if($introInfo[0]['Test']){
                $introInfo[0]['Test'] = '<p>'.$introInfo[0]['Test'];
            }
            $introInfo[0]['Answer'] =  R('Common/TestLayer/strFormat',array($introInfo[0]['Answer']));
            if($introInfo[0]['Answer']){
                $introInfo[0]['Answer'] = '<p>'.$introInfo[0]['Answer'];
            }
            $introInfo[0]['Analytic'] =  R('Common/TestLayer/strFormat',array($introInfo[0]['Analytic']));
            if($introInfo[0]['Analytic']){
                $introInfo[0]['Analytic'] = '<p>'.$introInfo[0]['Analytic'];
            }

            $introInfoArr=$this->getModel('TestMark')->markStrToArr($introInfo[0]['SubjectID'],$introInfo[0]['Mark']);
            if(strstr($introInfo[0]['Mark'],'@')){
                $arr=explode('@',$introInfo[0]['Mark']);
                foreach($arr as $i=>$iArr){
                    $introInfo[0]['Markx'][$i+1]=array_filter(explode('#',$iArr));
                }
            }else{
                $introInfo[0]['Markx'][1]=array_filter(explode('#',$introInfo[0]['Mark']));
            }
            $times=$this->getModel('Test')->xtnum($introInfo[0]['Test'],1);
            if(!$times) $times=1;
        }else if(!$statusInfo[0]['IfDel']){
            $testInfo = $this->getModel('CustomTestCopy')->unionSelect('customTestCopySelectById',$statusInfo[0]['TestID']);
            $realTag=0;
            //从库自定义打分
            if(strstr($testInfo[0]['Mark'],'@')){
                $arr=explode('@',$testInfo[0]['Mark']);
                foreach($arr as $i=>$iArr){
                    $testInfo[0]['Markx'][$i+1]=array_filter(explode('#',$iArr));
                }
            }else{
                $testInfo[0]['Markx'][1]=array_filter(explode('#',$testInfo[0]['Mark']));
            }
            $times=$this->getModel('Test')->xtnum($testInfo[0]['Test'],1);
            if(!$times) $times=1;
        }else{
            $times=$this->getModel('Test')->xtnum($realInfo[0]['Test'],1);
            if(!$times) $times=1;
        }
        //主库自定义打分
        $realInfoArr=$this->getModel('TestMark')->markStrToArr($realInfo[0]['SubjectID'],$realInfo[0]['Mark']);
        if(strstr($realInfo[0]['Mark'],'@')){
            $arr=explode('@',$realInfo[0]['Mark']);
            foreach($arr as $i=>$iArr){
                $realInfo[0]['Markx'][$i+1]=array_filter(explode('#',$iArr));
            }
        }else{
            $realInfo[0]['Markx'][1]=array_filter(explode('#',$testInfo[0]['Mark']));
        }
        //格式化选项数量，选项宽度
        $testInfo[0]['OptionNum']=explode(',',$testInfo[0]['OptionNum']);
        $testInfo[0]['OptionWidth']=explode(',',$testInfo[0]['OptionWidth']);
        $realInfo[0]['OptionNum']=explode(',',$realInfo[0]['OptionNum']);
        $realInfo[0]['OptionWidth']=explode(',',$realInfo[0]['OptionWidth']);
        $introInfo[0]['OptionNum']=explode(',',$introInfo[0]['OptionNum']);
        $introInfo[0]['OptionWidth']=explode(',',$introInfo[0]['OptionWidth']);

        $host=C('WLN_DOC_HOST');
        if($host){
            //主库变量
            $realInfo[0]['Test']=formatString('IPReturn',stripslashes_deep($realInfo[0]['Test']));
            $realInfo[0]['Answer']=formatString('IPReturn',stripslashes_deep($realInfo[0]['Answer']));
            $realInfo[0]['Analytic']=formatString('IPReturn',stripslashes_deep($realInfo[0]['Analytic']));
            //从库变量
            $testInfo[0]['Test']=formatString('IPReturn',stripslashes_deep($testInfo[0]['Test']));
            $testInfo[0]['Answer']=formatString('IPReturn',stripslashes_deep($testInfo[0]['Answer']));
            $testInfo[0]['Analytic']=formatString('IPReturn',stripslashes_deep($testInfo[0]['Analytic']));
            //库内题变量
            $introInfo[0]['Test']=formatString('IPReturn',stripslashes_deep($introInfo[0]['Test']));
            $introInfo[0]['Answer']=formatString('IPReturn',stripslashes_deep($introInfo[0]['Answer']));
            $introInfo[0]['Analytic']=formatString('IPReturn',stripslashes_deep($introInfo[0]['Analytic']));
        }
        //年级
        $gradeArr=SS('grade');
        $realInfo[0]['GradeName']=$gradeArr[$realInfo[0]['GradeID']]['GradeName'];
        $introInfo[0]['GradeName']=$gradeArr[$introInfo[0]['GradeID']]['GradeName'];
        //题型
        $typesArr=SS('types');
        $realInfo[0]['TypesName']=$typesArr[$realInfo[0]['TypesID']]['TypesName'];
        $introInfo[0]['TypesName']=$typesArr[$introInfo[0]['TypesID']]['TypesName'];
        //专题
        $specialArr = SS('special');
        $realInfo[0]['SpecialName']=$specialArr[$realInfo[0]['SpecialID']]['SpecialName'];
        $introInfo[0]['SpecialName']=$specialArr[$introInfo[0]['SpecialID']]['SpecialName'];

        //复合题
        $chooseList='';
        if($realInfo[0]['IfChoose']==1){
            $buffer=$this->getModel('CustomTestJudge')->selectData(
                '*',
                'TestID='.$statusInfo[0]['TestID'],
                'OrderID asc');
            $chooseList=$realInfo[0]['chooseList']=$buffer;
        }
        if($introInfo[0]['IfChoose']==1){
            $buffer=$this->getModel('TestJudge')->selectData(
                '*',
                'TestID='.$statusInfo[0]['IfIntro'],
                'OrderID asc');
            $introInfo[0]['chooseList']=$buffer;
        }
        if($testInfo[0]['IfChoose']==1){
            $buffer=$this->getModel('CustomTestJudgeCopy')->selectData(
                '*',
                'TestID='.$statusInfo[0]['TestID'],
                'OrderID asc');
            $chooseList=$testInfo[0]['chooseList']=$buffer;
        }

        //主表知识点章节
        $knowledgeArr = array();//主库知识点数据
        $knowledgeArr = $this->getKnowledgeInfo('CustomTestKnowledge',$statusInfo[0]['TestID']);
        $realInfo[0]['KlID']=$knowledgeArr['KlID'];
        $realInfo[0]['knowledge']=$knowledgeArr['knowledge'];
        $chapterArr = array();//主库知识点数据
        $chapterArr = $this->getChapterInfo('CustomTestChapter',$statusInfo[0]['TestID']);
        $realInfo[0]['ChapterID']=$chapterArr['ChapterID'];
        $realInfo[0]['chapter']=$chapterArr['chapter'];
        //库内题知识点章节
        $introKArr = array();
        $introKArr = $this->getKnowledgeInfo('TestKlReal',$statusInfo[0]['IfIntro']);
        $introInfo[0]['KlID']=$introKArr['KlID'];
        $introInfo[0]['knowledge']=$introKArr['knowledge'];
        $introCArr = array();
        $introCArr = $this->getChapterInfo('TestChapterReal',$statusInfo[0]['IfIntro']);
        $introInfo[0]['ChapterID']=$introCArr['ChapterID'];
        $introInfo[0]['chapter']=$introCArr['chapter'];
        //失败原因信息
        $failMsg=C('WLN_TASK_FAILURE_MSG');
        foreach($failMsg as $i=>$iFailMsg){
            $msgArr[]=explode('|',$iFailMsg);
        }
        //分割宽度及数量
        if(!$statusInfo[0]['IfDel']){
            $optionNum=explode(',',$testInfo[0]['OptionNum']);
            $optionWidth=explode(',',$testInfo[0]['OptionWidth']);
        }else{
            $optionNum=explode(',',$realInfo[0]['OptionNum']);
            $optionWidth=explode(',',$realInfo[0]['OptionWidth']);
        }

        for($i=0;$i<count($chooseList);$i++){
            if(empty($optionWidth[$i])){
                $optionWidthArr[$i]=0;
            }else{
                $optionWidthArr[$i]=$optionWidth[$i];
            }
            if(!is_numeric($optionNum[$i])){
                $optionNumArr[$i]=0;
            }else{
                $optionNumArr[$i]=$optionNum[$i];
            }
        }
        //从表知识点章节
        $testInfo[0]['KlID']=implode(',',$this->getModel('CustomTestKnowledgeCopy')->getKnowledges($statusInfo[0]['TestID'])[$statusInfo[0]['TestID']]);
        $testInfo[0]['ChapterID']=implode(',',$this->getModel('CustomTestChapterCopy')->getChapters($statusInfo[0]['TestID'])[$statusInfo[0]['TestID']]);
        if($statusInfo[0]['IfIntro']!=0){
            $jsonData=json_encode($introInfo[0]);
        }else if(!$statusInfo[0]['IfDel']){
            $jsonData=json_encode($testInfo[0]);
        }else{
            $jsonData=json_encode($realInfo[0]);
        }
        $this->assign('optionNum',$optionNumArr);//选项数量
        $this->assign('optionWidth',$optionWidthArr);//选项宽度
        $this->assign('markArray',$realInfoArr[1]);//自定义打分数据
        $this->assign('times',$times);
        $this->assign('realTag',$realTag);//是否异常题
        $this->assign('ifIntro',$ifIntro);//是否库内题
        $this->assign('failMsg',$msgArr);//审核失败原因数组
        $this->assign('testInfo',$testInfo[0]);//从库数据
        $this->assign('realInfo',$realInfo[0]);//主库数据
        $this->assign('introInfo',$introInfo[0]);//库内题数据
        $this->assign('statusData', $statusInfo);
        $this->assign('data',$jsonData);//json格式数据，编辑器使用
        $this->assign('pageName',$pageName);
        $this->display();
    }

    /**
     * 个人题库审核保存
     * @author demo
     */
    public function save(){
        //获取参数
        $testID = $_POST['TestID']; //试题id
        $typesID=$_POST['TypesID']; //题型

        $isPass = $_POST['isPass']; //审核通过

        $userName=$this->getCookieUserName();

        //判断试题id是否存在
        if(!is_numeric($testID)){
            $this->setError('30502'); //查询内容不是数字
        }

        //获取试题状态
        $customTestTaskStatus=$this->getModel('CustomTestTaskStatus');
        $testState=$customTestTaskStatus->getFieldByTestID('StatusID,IfDel,IfIntro',$testID);
        //获取教师状态
        $customTestTaskList=$this->getModel('CustomTestTaskList');
        // $testList=$customTestTaskList->getFieldByTestID('ListID,UserName',$testID);
        $testList=$this->getModel('CustomTestTaskList')->selectData('ListID,TestID,UserName','Status=1 AND TestID='.$testID);
        if(empty($testList)){
            $this->setError('40906');
        }
        $testList = $testList[0];
        $customTest=$this->getModel('CustomTest');
        $customTestAttr=$this->getModel('CustomTestAttr');

        $addPoint=0;//为教师加分值
        $ifDelCopyTable=0; //是否删除副表数据 0不删除 1删除
        $types=SS('types');
        //审核通过
        if($isPass==1){
            $testStatus=0; //试题主表状态
            $testListStatus=0; //试题队列状态
            $addPoint=$types[$typesID]['ScoreNormal'];//加分
            $ifDelCopyTable1=1; //删除副表数据
            $userName=$this->getCookieUserName();
            $status = '';
            //需要删除的题
            if($testState['IfDel']){
                $addPoint=C('WLN_TASK_SUCCESS_DELTEST'); //异常题加分

                $testStatus=-1; //修改试题主表状态为-1
                $testListStatus=-2; //试题队列状态
                $status = '删除试题';
                $content = $userName.'审核删除试题TestID【'.$testID.'】';
            }else{
                $testStatus=1; //修改试题主表状态为1
                $testListStatus=2; //试题队列状态

                //获取参数用于写入数据到主表     非库内题
                // $testData['Test']=formatString('IPReplace',$_POST['Test']);
                // $testData['Answer']=formatString('IPReplace',$_POST['Answer']);
                // $testData['Analytic']=formatString('IPReplace',$_POST['Analytic']);
                // $testData['ReMark']=$_POST['reMark'];
                // $testData['Source']=$_POST['source'];
                // $data['GradeID']=$_POST['GradeID'];
                // $data['TypesID']=$_POST['TypesID'];
                // $klData=explode(',',$_POST['KlID']);
                // $ChapterData=explode(',',$_POST['ChapterID']);
                // $data['SpecialID']=$_POST['SpecialID'];
                // $data['IfChoose']=$_POST['IfChoose'];
                $data['Mark'] = '';
                //打分数据
                if($_POST['Mark'] && $_POST['DfStyle']==0){
                    $mark=$_POST['Mark'];
                }
                if($mark){
                    if($_POST['ifIntro']!=1){
                        $buffer=$this->getModel('CustomTestCopy')->selectData(
                            '*',
                            'TestID='.$testID
                        );   //提取试题信息作为判断小题数的依据
                        $testM=$this->getModel('Test');
                        $times=$testM->xtnum($buffer[0]['Test'],1);    //小题数量
                        if($times){    //如果存在分组
                            $ci=count($mark)/$times;    //每个分组的打分项数目
                            $str='';
                            foreach($mark as $i=>$iMark){
                                //小题分组字符串
                                if($i%$ci==0 && $i!=0){
                                    $str.='@#';//分组字符串标记
                                }
                                if($iMark) $str.=$iMark.'#';
                            }
                            $data['Mark']='#'.$str;
                        }else{
                            $mark=array_filter($mark);
                            $data['Mark']='#'.implode('#',$mark).'#';
                        }
                    }else{
                        $data['Mark']=$mark;
                    }
                }
                $testData['Test']=formatString('IPReplace',$_POST['Test']);
                $testData['Answer']=formatString('IPReplace',$_POST['Answer']);
                $testData['Analytic']=formatString('IPReplace',$_POST['Analytic']);
                $testData['Remark']=$_POST['reMark'];
                $testData['Source']=$_POST['source'];
                $data['GradeID']=$_POST['GradeID'];
                $data['TypesID']=$_POST['TypesID'];
                $klData=explode(',',$_POST['KlID']);
                $ChapterData=explode(',',$_POST['ChapterID']);
                $data['SpecialID']=$_POST['SpecialID'];
                $data['IfChoose']=$_POST['IfChoose'];
                $data['LastUpdateTime']=time();
                if($data['IfChoose']==1){
                    for($i=1;$i<50;$i++){
                        $tag=$_POST['IfChoose'.$i];
                        if($tag==''){
                            break;
                        }
                        $judgeTmp['ChooseJudge'][$i]=$tag;
                        $optionWidthArr[$i]=$_POST['optionwidth'.$i];
                        $optionNumArr[$i]=$_POST['optionnum'.$i];
                    }
                }else{
                    $optionWidthArr[0]=$_POST['optionwidth1'];
                    $optionNumArr[0]=$_POST['optionnum1'];
                }
                $data['TestNum']=count($optionWidthArr)==1?0:count($optionWidthArr);
                $data['OptionWidth']=join(',',$optionWidthArr);
                if(!$data['OptionWidth']){
                    $data['OptionWidth'] = 0;
                }
                $data['OptionNum']=join(',',$optionNumArr);
                if(!$data['OptionNum']){
                    $data['OptionNum'] = 0;
                }
                $data['DfStyle']=$_POST['DfStyle'];
                $diff = 0;
                if($data['DfStyle']==1){
                    $data['Diff']=$_POST['Diff'];
                }else{ //处理客观打分  2015-10-10
                    $arr = explode('#',$data['Mark']);
                    $total = 0;
                    foreach($arr as $value){
                        $value = explode('|', $value);
                        $value = (Double)$value[1];
                        if($value >= 1){
                            $total += $value;
                        }else{
                            $diff += $value;
                        }
                    }
                    $scores = C('WLN_TEST_DATA');
                    $diff += (Double)$scores[$total];
                    $data['Diff'] = $diff;
                }
                //库内题
                if($testState['IfIntro']){
                    $addPoint=$types[$typesID]['ScoreIntro']; //库内题加分
                    //拷贝库内题到用户试题表
                    if($customTest->copyTestFromPublic($testID,$testState['IfIntro'])==false){
                        $this->setError('40904'); //库内试题拷贝出错！请重试。
                    }
                }else{
                    $addPoint=$types[$typesID]['ScoreNormal']; //普通题加分
                    //标引教师编辑过的题  填充数据到主表
                    $data = array(
                        'test' => $testData,
                        'attr' => $data,
                        'knowledge' => $klData,
                        'chapter' => $ChapterData,
                        'judge' => $judgeTmp['ChooseJudge']
                    );
                    if($customTest->copyTestFromData($data,$testID)==false){
                        $this->setError('40905'); //提交试题数据写入出错!请重试。
                    }
                }

                //是否图片版
                if($_POST['isPic']){
                    $addPoint=$types[$typesID]['ScorePic'];
                }

                //设置日志记录
                $status='审核通过';
                $content=$userName.'审核试题'.$testID.',通过';
            }
            //修改试题主表状态
            $customTestAttr->setStatus($testID,$testStatus);
            //修改试题队列状态表状态为
            $customTestTaskStatus->setStatus($testID,$testListStatus);

            //更新教师领取任务状态
            $customTestTaskList->setStatus($testList['ListID'],2);
        }else{
            //获取审核不通过参数
            $backData['isDec']=$_POST['isDec'];
            $backData['isReduce']=$_POST['isReduce'];
            $backData['isReset']=$_POST['isReset'];
            $backData['errorMsg']=$_POST['errorMsg'];

            //审核失败原因及扣分值
            $msg=explode('|',$backData['errorMsg']);

            //是否扣除教师分值
            if($backData['isDec']==1){
                if(!is_numeric($msg[1])){
                    $msg[1]=0;
                }
                $addPoint=$msg[1];
            }

            //是否重置试题
            if($backData['isReset']==1){
                //重置试题并且返回修改
                if($backData['isReduce']==1){
                      if($customTest->resetTestContent($testID)==false){
                          $this->setError('40903'); //重置试题失败，请重试
                      }
                }else{
                    $ifDelCopyTable1=1; //删除副表数据
                }
            }

            //清除库内题属性 清除需要删除属性
            $customTestTaskStatus->clearTestStatus($testID);

            //是否返回重改
            if($backData['isReduce']==1){

                //更新试题队列状态
                $taskTime=time()+C('WLN_TASK_TIMEOUT');
                $customTestTaskStatus->setStatus($testID,-1,$taskTime);

                //更新教师领取任务状态
                $customTestTaskList->setStatus($testList['ListID'],-1,$taskTime,$msg[0]);
                //试题编辑返回次数+1
                $customTestTaskList->setBackTimesCount($testList['ListID']);

                $status='退回重做';
                $content=$userName.'审核试题'.$testID.',退回重做';

            }else{
                //更新试题队列状态
                $customTestTaskStatus->setStatus($testID,0,time());
                //试题队列返回次数+1
                $customTestTaskStatus->setBackTimesCount($testState['StatusID']);

                //更新教师领取任务状态
                $customTestTaskList->setStatus($testList['ListID'],-2,0,$msg[0]);

                $status='返回任务队列';
                $content=$userName.'审核试题'.$testID.',返回任务队列';
            }
        }
        //这两个状态涉及到了金额统计，改变任务记录状态，便于统计用户分成
        if($status=='审核通过' || $status=='删除试题'){
            $ifTotal=1;
            //插入支出表
            $testAttr = $customTestAttr->findData('UserID', 'TestID='.$testList['TestID']);
            $userMsg =$this->getModel('User')->getInfoByName($testList['UserName']);
            if($status=='审核通过'){
                $payData['PayDesc']="教师[".$this->getCookieUserID()."]成功审核作者[".$testAttr['UserID']."]试题ID为【".$testID."】";
            }else{
                $payData['PayDesc']="教师[".$this->getCookieUserID()."]成功删除作者[".$testAttr['UserID']."]试题ID为【".$testID."】";
            }
            //试题通过审核,给作者积累经验
            $authorMsg=$this->getModel('User')->getInfoByID($testAttr['UserID']);
            $this->getModel('UserExp')->addUserExpAll($authorMsg['UserName'],'passCheck');
            $payData['PayMoney']=C('WLN_CHECK_TEST_MONEY');
            $payData['UserID']=$userMsg[0]['UserID'];
            $payData['PayName']='校本题库试题';
            $payData['AddTime']=time();
            $this->getModel('Pay')->addPayLog($payData);
            $thisUserBuffer=$this->getModel('User')->findData('UserName','UserID='.$testAttr['UserID']);
            $userNameArray = array(
                'UserName'=>$thisUserBuffer['UserName'],
                'Admin' => $this->getCookieUserName()
            );
        }else{
            $ifTotal = 0;
        }
        //记录日志
        $thisUserName=$userName;
        if($userNameArray) $thisUserName=$userNameArray;
        $this->customLog($testID,$status,$thisUserName,$content,$ifTotal,$addPoint);

        //删除副表数据
        if($ifDelCopyTable1==1){
            $customTest->delCopyData($testID);
        }

        //记录教师分值变化
        if($addPoint){
            $user=$this->getModel('User');
            $user->addPoint($userName,$addPoint);
        }

        //检测修改后的试题（非库内题）下载是否成功
        if($isPass==1 && !$testState['IfIntro']){
            $result=R('Common/UploadLayer/checkCreateWord',array($testData['Test'].$testData['Answer'].$testData['Analytic']));
            if($result=='error'){
                $status='试题下载失败';
                $content=$userName.'审核试题'.$testID.',试题下载失败';
                $this->customLog($testID,$status,$userName,$content);
            }else{
                $customTestTaskStatus->setDocPath($testID,$result);
            }
        }
        // $this->showSuccess('提交成功！',__MODULE__.'/CustomCheck/index');
        $this->showSuccess('提交成功！',U('Teacher/CustomCheck/index'));
    }
}