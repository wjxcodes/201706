<?php
/**
 * @author demo
 * @date 2014年10月10日
 * @updatetime 2015.3.14
 */

/**
 * 试题管理类，用于管理试题的相关操作
 */
namespace Test\Manage;
class TestManage extends BaseController  {
    var $moduleName = '试题管理';

    /**
     * 浏览试题列表
     * @author demo
     */
    public function index() {
        $pageName = '试题管理';
        $data=' 1=1 '; //初始化条件
        $map=array(); //分页条件
        $perpage = C('WLN_PERPAGE'); //每页 页数
        $orderby=' TestID desc '; //默认排序

        //浏览谁的试题
        if($this->ifSubject && $this->mySubject){
            $data .= 'and SubjectID in ('.$this->mySubject.') ';
        }elseif($this->ifDiff){
            $data .= ' and Admin="'.$this->getCookieUserName().'" ';
        }

        //获取查询条件
        if ($_REQUEST['name']) {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' and TestID="'.$_REQUEST['name'].'"';
        } else {
            //高级查询
            if ($_REQUEST['TestID']) {
                if(is_numeric($_REQUEST['TestID'])){
                    $map['TestID'] = $_REQUEST['TestID'];
                    $data .= ' and TestID='.$_REQUEST['TestID'];
                }else{
                    $this->setError('30502');
                }
            }
            if ($_REQUEST['DocID']) { //仅显示某一文档下试题 默认排序按文档试题正序
                if(is_numeric($_REQUEST['DocID'])){
                    $map['DocID'] = $_REQUEST['DocID'];
                    $data .= ' and DocID='.$_REQUEST['DocID'];
                }else{
                    $this->setError('30502');
                }
                $orderby=' NumbID asc ';
            }
            if ($_REQUEST['SubjectID']) {
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $data .= ' and SubjectID='.$_REQUEST['SubjectID'];
            }
            if ($_REQUEST['order']) {
                $map['order'] = $_REQUEST['order'];
                $orderby=' '.$_REQUEST['order'].' desc ';
            }
            if (is_numeric($_REQUEST['Status'])) {
                $map['Status'] = $_REQUEST['Status'];
                $data .= ' and Status='.$_REQUEST['Status'];
            }
            if (is_numeric($_REQUEST['ShowWhere'])) {
                $map['ShowWhere'] = $_REQUEST['ShowWhere'];
                $data .= ' and ShowWhere='.$_REQUEST['ShowWhere'];
            }
        }

        //错误试题条件
        if($_GET['errortest']==1){
            $map['errortest'] = 1;
            $data = ' (SubjectID=0 OR TypesID=0 OR TestID in (select TestID from zj_test_kl where KlID=0))';
        }

        //仅显示某一文档下试题
        $testAttrObj = $this->getModel('TestAttr');
        if($_REQUEST['DocID']){
            $list = $testAttrObj->selectData(
                '*',
                $data,
                $orderby
            );
        }else{
            $count = $testAttrObj->selectCount(
                $data,
                'TestID'
            ); // 查询满足要求的总记录数
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $this->pageList($count, $perpage, $map);
            $page = page($count,$_GET['p'],$perpage).','.$perpage; //格式化分页
            $list = $testAttrObj->pageData(
                '*',
                $data,
                $orderby,
                $page
            );
        }
        if($list){
            $types = SS('types');
            $knowledgeList = SS('knowledge');
            $knowledgeParent = SS('knowledgeParent');
            $chapterList = SS('chapterList');
            $chapterParent=SS('chapterParentPath');
            $special = SS('special');
            $subject = SS('subject');
            $gradeArr = SS('grade');

            //获取list下试题id
            $testIDArray=array(); //存储试题id
            $docIDArray=array(); //存储文档id
            foreach($list as $i=>$iList){
                $testIDArray[]=$iList['TestID'];
                $docIDArray[]=$iList['DocID'];
            }
            $docIDArray=array_unique($docIDArray);

            $test = $this->getModel('Test');
            $testKl = $this->getModel('TestKl');
            $testChapter = $this->getModel('TestChapter');
            $doc = $this->getModel('Doc');
            $knowledge = $this->getModel('Knowledge');
            $chapter = $this->getModel('Chapter');
            //存数以试题id为键值的数据
            $testListArrayByID=$test->getTestListByID($testIDArray); //试题内容
            $testKlArrayByID=$testKl->getTestListByID($testIDArray); //试题知识点
            $testChapterArrayByID=$testChapter->getTestListByID($testIDArray); //试题章节
            $docListArrayByID=$doc->getDocListByID($docIDArray); //文档内容

            //$host=C('WLN_DOC_HOST');
            foreach($list as $i=>$iList){
                $list[$i]['TypesName']=$types[$list[$i]['TypesID']]['TypesName'];
                $list[$i]['SpecialName']=$special[$list[$i]['SpecialID']]['SpecialName'];
                $list[$i]['SubjectName']=$subject[$subject[$list[$i]['SubjectID']]['PID']]['SubjectName'].$subject[$list[$i]['SubjectID']]['SubjectName'];
                $list[$i]['GradeName']=$gradeArr[$list[$i]['GradeID']]['GradeName'];
                $list[$i]['Test']=$testListArrayByID[$iList['TestID']]['Test'];
                $list[$i]['Answer']=$testListArrayByID[$iList['TestID']]['Answer'];
                $list[$i]['Analytic']=$testListArrayByID[$iList['TestID']]['Analytic'];
                $list[$i]['Remark']=$testListArrayByID[$iList['TestID']]['Remark'];
                $list[$i]['DocName']=$docListArrayByID[$iList['DocID']]['DocName'];
                $list[$i]['Test']=R('Common/TestLayer/strFormat',array($list[$i]['Test']));
                $list[$i]['error']= R('Common/TestLayer/checkTestAttr',array($list[$i]));

                //获取knowledge
                if($testKlArrayByID[$iList['TestID']] && $testKlArrayByID[$iList['TestID']][0]){
                    $list[$i]['KlName']=$knowledge->getKnowledgePath(
                        array(
                            'parent'=>$knowledgeParent,
                            'self'=>$knowledgeList,
                            'ID'=>$testKlArrayByID[$iList['TestID']],
                            'ReturnString'=>'<br/>'
                        )
                    );
                }
                //获取chapter
                if($testChapterArrayByID[$iList['TestID']] && $testChapterArrayByID[$iList['TestID']][0]){
                    $list[$i]['ChapterName']=$chapter->getChapterPath(
                        array(
                            'parent'=>$chapterParent,
                            'self'=>$chapterList,
                            'ID'=>$testChapterArrayByID[$iList['TestID']],
                            'ReturnString'=>'<br/>'
                        )
                    );
                }
            }
        }
        //难度
        $diffArray=C('WLN_TEST_DIFF');
        //学科
        $subjectArray=SS('subjectParentId'); //获取学科数据集

        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('diffArray', $diffArray);
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑试题信息
     * @author demo
     */
    public function edit() {
        $testID = $_GET['id']; //获取数据标识
        if(isset($_REQUEST['workID'])) $workID=$_REQUEST['workID'];//任务编号
        //判断数据标识
        if (empty ($testID)) {
            $this->setError('30301',1); //判断数据标识
        }
        $pageName = '编辑试题';
        $act = 'edit'; //模板标识
        $test = $this->getModel('Test');
        $edit = $test->unionSelect('testSelectByTestId',$testID);
        $docID=$edit[0]['DocID'];

        if(empty($docID)){
            $this->setError('1X2006',1); //数据标示不对称！请通知管理员核实
        }

        $docInfo=$this->getModel('Doc')->selectData(
                'DocID,DocName,SubjectID,Admin',
                'DocID='.$docID
            );
        //编辑谁的文档
        if($this->ifSubject && $this->mySubject && !in_array($docInfo[0]['SubjectID'],explode(',',$this->mySubject))){
            $this->setError('30712',1);
        }elseif($this->ifDiff && $docInfo[0]['Admin']!=$this->getCookieUserName()){
            //判断是否可以编辑
            $this->setError('30712',1);
        }

        $buffer = $this->getModel('TestSkill')->getByTestID($testID);
        $edit[0]['SkillID']=0;
        if($buffer){
            $edit[0]['SkillID']=implode(',',$buffer[$testID]);
        }
        $buffer = $this->getModel('TestCapacity')->getByTestID($testID);
        $edit[0]['CapacityID']=0;
        if($buffer){
            $edit[0]['CapacityID']=implode(',',$buffer[$testID]);
        }

        $buffer = $this->getModel('TestKl')->selectData(
            '*',
            'TestID='.$testID);
        $edit[0]['KlID']=0;
        if($buffer){
            $arrTemp=array();
            foreach($buffer as $iBuffer){
                $arrTemp[]=$iBuffer['KlID'];
            }
            $edit[0]['KlID']=implode(',',$arrTemp);
        }
        $buffer = $this->getModel('TestChapter')->selectData(
            '*',
            'TestID='.$testID);
        $edit[0]['ChapterID']=0;
        if($buffer){
            $arrTemp=array();
            foreach($buffer as $iBuffer){
                $arrTemp[]=$iBuffer['ChapterID'];
            }
            sort($arrTemp);
            $edit[0]['ChapterID']=implode(',',$arrTemp);
        }
        if(strstr($edit[0]['Mark'],'@')){
            $arr=explode('@',$edit[0]['Mark']);
            foreach($arr as $i=>$iArr){
                $edit[0]['Markx'][$i+1]=array_filter(explode('#',$iArr));
            }
        }else{
            $edit[0]['Markx'][1]=array_filter(explode('#',$edit[0]['Mark']));
        }

        //自定义打分
        $testMarkArray=SS('testMarkSubject');
        $markArray=$testMarkArray[$edit[0]['SubjectID']];
        if($markArray){
            foreach($markArray as $i=>$iMarkArray){
                $markArray[$i]['MarkListx']=formatString('str2Arr',$markArray[$i]['MarkList']);
                foreach($markArray[$i]['MarkListx'] as $j=>$jMarkList){
                    $markArray[$i]['MarkListx'][$j][3]=$markArray[$i]['MarkID'].'|'.$markArray[$i]['MarkListx'][$j][0];
                }
            }
        }

        //自定义打分次数
        $times=$this->getModel('Test')->xtnum($edit[0]['Test'],1);
        if(!$times) $times=1;

        //复合题
        $chooseList='';
        if($edit[0]['IfChoose']==1){
            $buffer=$this->getModel('TestJudge')->selectData(
                '*',
                'TestID='.$testID,
                'OrderID asc');
            $chooseList=$buffer;
        }
        //试题路径
        //$host=C('WLN_DOC_HOST');

        $arrField=array('Test','Answer','Analytic','Remark');
        foreach($arrField as $i=>$iArrField){
            $edit[0][$iArrField]=R('Common/TestLayer/strFormat',array($edit[0][$iArrField]));
        }

        //分割宽度 及数量
        $optionNum=explode(',',$edit[0]['OptionNum']);
        $optionWidth=explode(',',$edit[0]['OptionWidth']);
        for($i=0;$i<count($chooseList);$i++){
            if(empty($optionWidth[$i])){
                $optionWidthArr[$i]=0;
            }else{
                $optionWidthArr[$i]=$optionWidth[$i];
            }
            if(!is_numeric($optionNum[$i])){
                $optionNumArr[$i]=$optionNum[0];
            }else{
                $optionNumArr[$i]=$optionNum[$i];
            }
        }

        //是否需要审核意见
        $testError='';
        if($workID){
            $teacherWork=$this->getModel('TeacherWork');
            $checkTimes=$teacherWork->getCheckTimes($workID);
            $teacherWorkCheck=$this->getModel('TeacherWorkCheck');
            $testError=$teacherWorkCheck->getTestCheckErr($checkTimes,$workID,$docID,$testID);
        }
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('optionNum',$optionNumArr);
        $this->assign('optionWidth',$optionWidthArr);
        $this->assign('times', $times);
        $this->assign('markArray', $markArray);
        $this->assign('chooseList', $chooseList);
        $this->assign('errorInfo',$testError);//审核意见
        $this->assign('doc',$docInfo[0]);//文档信息
        $this->assign('pageName', $pageName);
        $this->setBack($this->fetch('Test/newEdit'));
    }

    /**
     * 编辑入库试题
     * @author demo
     */
    public function editIntro() {

        $testID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($testID)) {
            $this->setError('30301',1);
        }
        $pageName = '编辑试题';
        $act = 'edit'; //模板标识
        if($this->ifSubject && $this->mySubject){
            $subject = $this->getModel('TestAttrReal')->selectData(
                'SubjectID',
                'TestID='.$testID);
            if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712',1);
            }
        }
        $test = $this->getModel('TestReal');
        $edit = $test->unionSelect('testRealSelectByTestId',$testID);

        $buffer = $this->getModel('TestSkill')->getByTestID($testID);
        $edit[0]['SkillID']=0;
        if($buffer){
            $edit[0]['SkillID']=implode(',',$buffer[$testID]);
        }
        $buffer = $this->getModel('TestCapacity')->getByTestID($testID);
        $edit[0]['CapacityID']=0;
        if($buffer){
            $edit[0]['CapacityID']=implode(',',$buffer[$testID]);
        }

        $buffer = $this->getModel('TestKlReal')->selectData(
            '*',
            'TestID='.$testID);
        $edit[0]['KlID']=0;
        if($buffer){
            $arrTemp=array();
            foreach($buffer as $iBuffer){
                $arrTemp[]=$iBuffer['KlID'];
            }
            $edit[0]['KlID']=implode(',',$arrTemp);
        }
        $buffer = $this->getModel('TestChapterReal')->selectData(
            '*',
            'TestID='.$testID);
        $edit[0]['ChapterID']=0;
        if($buffer){
            $arrTemp=array();
            foreach($buffer as $iBuffer){
                $arrTemp[]=$iBuffer['ChapterID'];
            }
            sort($arrTemp);
            $edit[0]['ChapterID']=implode(',',$arrTemp);
        }
        if(strstr($edit[0]['Mark'],'@')){
            $arr=explode('@',$edit[0]['Mark']);
            foreach($arr as $i=>$iArr){
                $edit[0]['Markx'][$i+1]=array_filter(explode('#',$iArr));
            }
        }else{
            $edit[0]['Markx'][1]=array_filter(explode('#',$edit[0]['Mark']));
        }

        //自定义打分
        $testMarkArray=SS('testMarkSubject');
        $markArray=$testMarkArray[$edit[0]['SubjectID']];
        if($markArray){
            foreach($markArray as $i=>$iMarkArray){
                $markArray[$i]['MarkListx']=formatString('str2Arr',$markArray[$i]['MarkList']);
                foreach($markArray[$i]['MarkListx'] as $j=>$jMarkList){
                    $markArray[$i]['MarkListx'][$j][3]=$markArray[$i]['MarkID'].'|'.$markArray[$i]['MarkListx'][$j][0];
                }
            }
        }
        //自定义打分次数
        $times=$this->getModel('Test')->xtnum($edit[0]['Test'],1);
        if(!$times) $times=1;

        //复合题
        $chooseList='';
        if($edit[0]['IfChoose']==1){
            $buffer=$this->getModel('TestJudge')->selectData(
                '*',
                'TestID='.$testID,
                'OrderID asc');
            $chooseList=$buffer;
        }
        //试题路径
        //$host=C('WLN_DOC_HOST');

        $edit[0]['Test']=R('Common/TestLayer/strFormat',array($edit[0]['Test']));
        $edit[0]['Answer']=R('Common/TestLayer/strFormat',array($edit[0]['Answer']));
        $edit[0]['Analytic']=R('Common/TestLayer/strFormat',array($edit[0]['Analytic']));
        $edit[0]['Remark']=R('Common/TestLayer/strFormat',array($edit[0]['Remark']));

        //分割宽度 及数量
        $optionNum=explode(',',$edit[0]['OptionNum']);

        $optionWidth=explode(',',$edit[0]['OptionWidth']);
        for($i=0;$i<count($chooseList);$i++){
            if(empty($optionWidth[$i])){
                $optionWidthArr[$i]=0;
            }else{
                $optionWidthArr[$i]=$optionWidth[$i];
            }
            if(!is_numeric($optionNum[$i])){
                $optionNumArr[$i]=$optionNum[0];        //选项数量默认为第一个参数
            }else{
                $optionNumArr[$i]=$optionNum[$i];
            }
        }
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('optionnum',$optionNumArr);
        $this->assign('optionwidth',$optionWidthArr);
        $this->assign('times', $times);
        $this->assign('mark_array', $markArray);
        $this->assign('chooseList', $chooseList);
        $this->assign('pageName', $pageName);
        $this->setBack($this->fetch('Test/addIntro'));
    }

    /**
     * 查看试题列表
     * @author demo
     */
    public function showtest() {

        $testID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($testID)) {
            $this->setError('30301',1);
        }
        $pageName = '查看试题';
        $act = 'show'; //模板标识
        $test = $this->getModel('Test');
        $edit = $test->unionSelect('testRealSelectById',$testID);
        //编辑谁的文档
        if($this->ifSubject && $this->mySubject){
            //判断是否可以编辑
            $buffer=$this->getModel('Doc')->selectData(
                '*',
                'DocID='.$edit[0]['DocID']
            );
            if(!in_array($buffer[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712',1);
            }
        }elseif($this->ifDiff){
            //判断是否可以编辑
            $buffer=$this->getModel('Doc')->selectData(
                '*',
                'DocID='.$edit[0]['DocID']);
            if($buffer[0]['Admin']!=$this->getCookieUserName()){
                $this->setError('30812',1);
            }
        }

        $buffer = $this->getModel('TestKlReal')->selectData(
                '*',
                'TestID='.$testID
            );
        if($buffer){
            $arrTemp=array();
            foreach($buffer as $iBuffer){
                $arrTemp[]=$iBuffer['KlID'];
            }
            $edit[0]['KlID']=implode(',',$arrTemp);
        }
        if(strstr($edit[0]['Mark'],'@')){
            $arr=explode('@',$edit[0]['Mark']);
            foreach($arr as $i=>$iArr){
                $edit[0]['Markx'][$i+1]=array_filter(explode('#',$iArr));
            }
        }else{
            $edit[0]['Markx'][1]=array_filter(explode('#',$edit[0]['Mark']));
        }

        //自定义打分
        $testMarkArray=SS('testMarkSubject');
        $markArray=$testMarkArray[$edit[0]['SubjectID']];
        if($markArray){
            foreach($markArray as $i=>$iMarkArray){
                $markArray[$i]['MarkListx']=formatString('str2Arr',$markArray[$i]['MarkList']);
                foreach($markArray[$i]['MarkListx'] as $j=>$jMarkList){
                    $markArray[$i]['MarkListx'][$j][3]=$markArray[$i]['MarkID'].'|'.$markArray[$i]['MarkListx'][$j][0];
                }
            }
        }

        //自定义打分次数
        $times=$test->xtnum($edit[0]['Test']);
        if(!$times) $times=1;
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('times', $times);
        $this->assign('mark_array', $markArray);
        $this->assign('pageName', $pageName);
        $this->setBack($this->fetch('Test/show'));
    }

    /**
     * 删除试题
     * @author demo
     */
    public function delete() {
        $testID = $_POST['id']; //获取数据标识
        if (!$testID) {
            $this->setError('30301');
        }

        //删除谁的文档
        if($this->ifSubject && $this->mySubject){
            $buffer = $this->getModel('TestAttr')->selectData(
                'SubjectID',
                'TestID in ('.$testID.')');
            foreach($buffer as $iBuffer){
                if(!in_array($iBuffer['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30812',0);
                }
            }
        }else if($this->ifDiff){
            $buffer=$this->getModel('Test')->unsionSelect('testDocSelectByTestID',$testID);
            //判断是否可以编辑
            $adminName=$this->getCookieUserName();
            foreach($buffer as $iBuffer){
                if($iBuffer['Admin']!=$adminName){
                   $this->setError('30812');
                }
            }
        }
        //删除
        if ($this->getModel('Test')->deleteData(
                'TestID in ('.$testID.')') === false) {
            $this->setError('30302');
        } else {
            //删除属性
            $this->getModel('TestAttr')->deleteData('TestID in ('.$testID.')');
            //删除知识点
            $this->getModel('TestKl')->deleteData('TestID in ('.$testID.')');
            //删除章节
            $this->getModel('TestChapter')->deleteData('TestID in ('.$testID.')');
            //删除技能
            $this->getModel('TestSkill')->deleteData('TestID in ('.$testID.')');
            //删除能力
            $this->getModel('TestCapacity')->deleteData('TestID in ('.$testID.')');

            //判断试题是否在库内
            $buffer = $this->getModel('TestAttrReal')->selectData(
                'TestID',
                'TestID in ('.$testID.')');
            if($buffer){
                $testIDArray=explode(',',$testID);
                $testIDArray=array_unique($testIDArray);
                foreach($buffer as $ibuffer){
                    unset($testIDArray[array_keys($testIDArray,$ibuffer['TestID'])[0]]);
                }
                $testID=implode(',',$testIDArray);
            }
            if(!empty($testID)){
                //删除doc
                $this->getModel('TestDoc')->deleteData('TestID in ('.$testID.')');
                //删除替换
                $this->getModel('TestReplace')->deleteData('TestID in ('.$testID.')');
            }
            //写入日志
            $this->adminLog($this->moduleName, '删除文档TestID为【' . $testID . '】的数据');
            $this->showSuccess('删除成功！');
        }
    }

    /**
     * 审核试题
     * @author demo
     */
    public function check() {
        $test=$this->getModel('Test');
        $testID=$_POST['id']; //获取数据标识
        $buffer=$this->getModel('Test')->unionSelect('testDocSelectByTestID',$testID);
        if($this->ifSubject && $this->mySubject){
            foreach($buffer as $iBuffer){
                if(!in_array($iBuffer['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712',1);
                }
            }
        }elseif($this->ifDiff){
            //判断是否可以审核
            $adminName=$this->getCookieUserName();
            foreach($buffer as $iBuffer){
                if($iBuffer['Admin']!=$adminName){
                    $this->setError('1X2007',1);
                }
            }
        }
        //判断数据标识
        $output='<script>';
        if (empty ($testID)) {
            $output.='alert("数据标识不能为空！");';
        }else{
            $testID=explode(',',$testID);

            $Status=$_POST['Status'];
            if(!is_numeric($Status)){
                $Status=0;
            }
            $str = $Status==1 ? '<font color=\'red\'>锁定</font>' : '正常';
            $alert = $Status==1 ? '锁定成功' : '审核成功';
            $classStr = $Status==1 ? 'btcheck' : 'btlock';
            $this->getModel('TestAttr')->updateData(
                array('Status'=>$Status),
                'TestID in ('.implode(',',$testID).')');
            foreach($testID as $iTestID){
                $output.='document.getElementById("status'.$iTestID.'").innerHTML="<span class=\''.$classStr.'\' thisid=\''.$iTestID.'\'>'.$str.'</span>";';
            }
        }

        $output.='alert("'.$alert.'");</script>';
        $this->setBack($output);
    }

    /**
     * 试题入库
     * @author demo
     */
    public function intro() {
        $testID = $_POST['id']; //获取数据标识
        //判断数据标识
        if (empty ($testID)) {
            $this->setError('30301',0);
        }

        //判断试题是否被审核
        $ids=array();
        $dids=array();
        $buffer=$this->getModel('TestAttr')->selectData(
            'TestID,Status,DocID,FirstLoadTime,SubjectID,Admin,TypesID,DocID,Diff,Duplicate,ShowWhere',
            'TestID in ('.$testID.')');
        foreach($buffer as $iBuffer){
            if($iBuffer['Status']==0){
                $ids[]=$iBuffer['TestID'];
                $dids[]=$iBuffer['DocID'];
            }
        }
        //如果与提交数据不一致提示
        $arr=explode(',',$testID);
        if(count($arr)!=count($ids)){
            $this->setError('1X2008',0);
        }

        //判断权限
        if($this->ifSubject && $this->mySubject){
            foreach($buffer as $iBuffer){
                if(!in_array($iBuffer['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30812',0);
                }
            }
        }elseif($this->ifDiff){
            //判断是否可以审核
            $adminName=$this->getCookieUserName();
            foreach($buffer as $iBuffer){
                if($iBuffer['Admin']!=$adminName){
                    $this->setError('30812',0);
                }
            }
        }

        //判断试题必要属性是否存在
        $ids=array();
        $dids=array();
        $ndids=array(); //错误试题列表用作提示信息
        foreach($buffer as $iBuffer){
            if(!empty($iBuffer['SubjectID']) and !empty($iBuffer['TypesID']) and !empty($iBuffer['DocID'])){
                if($iBuffer['Duplicate']!=0 or (!empty($iBuffer['Diff']) and $iBuffer['Diff']!='0.000') or $iBuffer['ShowWhere']==3){
                    $ids[]=$iBuffer['TestID'];
                    $dids[]=$iBuffer['DocID'];
                }else{
                    $ndids[]=$iBuffer['TestID'];
                }
            }
        }

        if(count($arr)!=count($ids)){
            $this->setError('1X2009',IS_AJAX,'',implode('、',$ndids)); //试题属性不完整！请确认。
        }

        $testID=implode(',',$ids); //试题id
        $testData=array(); //试题所有属性

        //获取试题临时库属性
        $buffer=$this->getModel('Test')->selectData(
            '*',
            'TestID in ('.$testID.')');

        foreach($buffer as $i=>$iBuffer){
            $testData[$iBuffer['TestID']]=$iBuffer;
        }
        $buffer=$this->getModel('TestAttr')->selectData(
            '*',
            'TestID in ('.$testID.')');

        foreach($buffer as $i=>$iBuffer){
            $testData[$iBuffer['TestID']]=array_merge($testData[$iBuffer['TestID']],$iBuffer);
        }
        $buffer=$this->getModel('TestKl')->selectData(
            '*',
            'TestID in ('.$testID.')');

        foreach($buffer as $i=>$iBuffer){
            $testData[$iBuffer['TestID']]['Kl'][]=$iBuffer['KlID'];
        }
        $buffer=$this->getModel('TestChapter')->selectData(
            '*',
            'TestID in ('.$testID.')');

        foreach($buffer as $i=>$iBuffer){
            $testData[$iBuffer['TestID']]['Chapter'][]=$iBuffer['ChapterID'];
        }
        //获取入库试题
        $idIn=array();
        $buffer=$this->getModel('TestReal')->selectData(
            'TestID',
            'TestID in ('.$testID.')');
        foreach($buffer as $i=>$iBuffer){
            $idIn[0][]=$iBuffer['TestID'];
        }
        $buffer=$this->getModel('TestAttrReal')->selectData(
            'TestID',
            'TestID in ('.$testID.')');
        foreach($buffer as $i=>$iBuffer){
            $idIn[1][]=$iBuffer['TestID'];
        }
        $buffer=$this->getModel('TestKlReal')->selectData(
            '*',
            'TestID in ('.$testID.')');
        foreach($buffer as $i=>$iBuffer){
            $idIn[2][]=$iBuffer['TestID'];
        }
        $buffer=$this->getModel('TestChapterReal')->selectData(
            '*',
            'TestID in ('.$testID.')');
        foreach($buffer as $i=>$iBuffer){
            $idIn[3][]=$iBuffer['TestID'];
        }
        foreach($testData as $i=>$iTestData){
            //如果入库修改试题属性 如果没有则添加
            $data=array();
            $data['TestID']=$iTestData['TestID'];
            $data['NumbID']=$iTestData['NumbID'];
            $data['Test']=$iTestData['Test'];
            $data['Analytic']=$iTestData['Analytic'];
            $data['Answer']=$iTestData['Answer'];
            $data['Remark']=$iTestData['Remark'];
            $data['DocID']=$iTestData['DocID'];
            $data['Equation']=$iTestData['Equation'];
            if(in_array($i,$idIn[0])){
                if($this->getModel('TestReal')->updateData(
                        $data,
                        'TestID='.$iTestData['TestID'])===false){
                    $this->setError('30311',0);
                }
            }else{
                if($this->getModel('TestReal')->insertData(
                        $data)===false){
                    $this->setError('30311',0);
                }
            }
            $data=array();
            $data['TestID']=$iTestData['TestID'];
            $data['DocID']=$iTestData['DocID'];
            $data['TypesID']=$iTestData['TypesID'];
            $data['SpecialID']=$iTestData['SpecialID'];
            $data['SubjectID']=$iTestData['SubjectID'];
            $data['Diff']=$iTestData['Diff'];
            $data['Mark']=$iTestData['Mark'];
            $data['Admin']=$iTestData['Admin'];
            $data['DfStyle']=$iTestData['DfStyle'];
            $data['IfChoose']=$iTestData['IfChoose'];
            $data['Duplicate']=$iTestData['Duplicate'];
            $data['TestNum']=$iTestData['TestNum'];
            $data['TestStyle']=$iTestData['TestStyle'];
            $data['OptionWidth']=$iTestData['OptionWidth'];
            $data['OptionNum']=$iTestData['OptionNum'];
            $data['GradeID']=$iTestData['GradeID'];
            $data['NumbID']=$iTestData['NumbID'];
            $data['Status']=$iTestData['Status'];
            $data['IfWL']=$iTestData['IfWL'];
            $data['Score']=$iTestData['Score'];
            $data['AatTestStyle']=$iTestData['AatTestStyle'];
            $data['ShowWhere']=$iTestData['ShowWhere'];

            if($iTestData['FirstLoadTime']==0)
                $data['FirstLoadTime']=time();
            else
                $data['FirstLoadTime']=$iTestData['FirstLoadTime'];
            $data['LoadTime']=time();

            if(in_array($i,$idIn[1])){
                $result=$this->getModel('TestAttrReal')->updateData(
                    $data,
                    'TestID='.$iTestData['TestID']);
                if($result==false)
                    $this->setError('30311'); //试题属性更新失败！
            }else{
                $result=$this->getModel('TestAttrReal')->insertData(
                    $data);
                if($result==false)
                    $this->setError('30310'); //试题属性添加失败！
            }

            if(in_array($i,$idIn[2])) $this->getModel('TestKlReal')->deleteData(
                'TestID in ('.$i.')');
            foreach($iTestData['Kl'] as $n){
                $data=array();
                $data['KlID']=$n;
                $data['TestID']=$iTestData['TestID'];
                $result=$this->getModel('TestKlReal')->insertData(
                    $data);
                if($result==false)
                    $this->setError('30311'); //试题知识点属性更新失败！
            }
            if(in_array($i,$idIn[3])) $this->getModel('TestChapterReal')->deleteData(
                'TestID in ('.$i.')');
            foreach($iTestData['Chapter'] as $n){
                $data=array();
                $data['ChapterID']=$n;
                $data['TestID']=$iTestData['TestID'];
                $result=$this->getModel('TestChapterReal')->insertData(
                    $data);
                if($result==false)
                    $this->setError('30311'); //试题章节属性更新失败！
            }

            //删除原始数据 逐条删除 防止错误
            $this->getModel('Test')->deleteData('TestID in ('.$i.')');
            $this->getModel('TestAttr')->deleteData('TestID in ('.$i.')');
            $this->getModel('TestKl')->deleteData('TestID in ('.$i.')');
            $this->getModel('TestChapter')->deleteData('TestID in ('.$i.')');
        }

        //检查试题是否全部入库
        //试题对应docid  docid对应iftest是否为1 试题是否全部入库
        $dids=array_unique($dids);
        $buffer=$this->getModel('Doc')->selectData(
            'DocID,IfTest,IntroFirstTime',
            'DocID in ('.implode(',',$dids).')'
        );

        $firstDocID=array();
        if($buffer){
            $testDocIDList=array();
            $IntroDocIDList=array();
            foreach($buffer as $iBuffer){
                $testNum=$this->getModel('TestAttr')->selectCount(
                    'DocID='.$iBuffer['DocID'],
                    'TestID');
                $testRealNum=$this->getModel('TestAttrReal')->selectCount(
                    'DocID='.$iBuffer['DocID'],
                    'TestID'
                );
                if($testNum==0 and $testRealNum>0){
                    if($iBuffer['IfTest']==1){
                        $testDocIDList[]=$iBuffer['DocID'];    //全部入库并可测试
                    }
                    if($iBuffer['IntroFirstTime']==0){
                        $firstDocID[]=$iBuffer['DocID'];    //全部入库并可测试
                    }
                    //记录需要更新的文档id
                    $IntroDocIDList[]=$iBuffer['DocID'];
                }
            }
            //更新文档的IfTest属性
            if($testDocIDList) $this->getModel('Doc')->updateData(
                array('IfTest'=>2),
                'DocID in ('.implode(',',$testDocIDList).')');
            //更新文档的IfIntro属性
            if($IntroDocIDList) $this->getModel('Doc')->updateData(
                array('IfIntro'=>1,'IntroTime'=>time()),
                'DocID in ('.implode(',',$IntroDocIDList).')');
            //更新文档的IfIntro属性
            if($firstDocID) $this->getModel('Doc')->updateData(
                array('IntroFirstTime'=>time()),
                'DocID in ('.implode(',',$firstDocID).')');
        }
        //写入日志
        $this->adminLog($this->moduleName, '入库文档TestID为【' . $testID . '】的数据');
        $this->showSuccess('入库成功！');
    }

    /**
     * 入库试题列表
     * @author demo
     */
    public function introlist() {
        $pageName = '入库试题';
        //每页 页数
        $perpage = C('WLN_PERPAGE');
        $perpage = 10;
        $page=$_GET['p'];
        if(!$page || $page<1) $page=1;
        $page = intval($page);
        $off = ($page-1)*$perpage;
        $ifindex=0; //是否进行索引查询

        $whereSearch=array(); //查询条件
        $orderSearch=array('loadtime DESC'); //排序if($orderlist) $order=array($orderlist);
        $pageSearch=array('page'=>$page,'perpage'=>$perpage);

        //浏览谁的试题
        if($this->ifSubject && $this->mySubject){
            $ifindex=1;
            $whereSearch['SubjectID']=$this->mySubject;
        }elseif($this->ifDiff){
            $ifindex=1;
            $whereSearch['Admin']=$this->getCookieUserName();
        }

        if ($_REQUEST['name']) {
            //简单查询
            $ifindex=1;
            $map['name'] = $_REQUEST['name'];
            $whereSearch['TestID']=$_REQUEST['name'];
            //$key=$_REQUEST['name'];
        } else {
            //高级查询
            if ($_REQUEST['TestID']) {
                if(is_numeric($_REQUEST['TestID'])){
                    $map['TestID'] = $_REQUEST['TestID'];
                    $whereSearch['TestID'] = $_REQUEST['TestID'];
                }else{
                    $this->setError('30502');
                }
                $ifindex=1;
            }
            if ($_REQUEST['DocID']) {
                if(is_numeric($_REQUEST['DocID'])){
                    $map['DocID'] = $_REQUEST['DocID'];
                    $whereSearch['DocID'] = $_REQUEST['DocID'];
                }else{
                    $this->setError('30502');
                }
                $orderSearch=array('numbid ASC');
                $pageSearch['perpage']=200;
                $ifindex=1;
            }
            if ($_REQUEST['Diff']) {
                $ifindex=1;
                $map['Diff'] = $_REQUEST['Diff'];
                $whereSearch['Diff'] = $_REQUEST['Diff'];
            }
            if ($_REQUEST['SubjectID']) {
                $ifindex=1;
                if($this->ifSubject && $this->mySubject){
                    if(!in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                        $this->setError('30712',0);
                    }
                }
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $whereSearch['SubjectID'] = $_REQUEST['SubjectID'];
            }
            if ($_REQUEST['TypesID']) {
                $ifindex=1;
                $map['TypesID'] = $_REQUEST['TypesID'];
                $whereSearch['TypesID'] = $_REQUEST['TypesID'];
            }
            if ($_REQUEST['KlID']) {
                $ifindex=1;
                $kk=$_REQUEST['KlID'];
                if(!is_array($kk)) $kk=array($kk);
                $kk=array_filter($kk);

                if($kk){
                    $lastkl=str_replace('t','',$kk[count($kk)-1]);
                    $map['KlID'] = $lastkl;
                    $whereSearch['KlID'] = $lastkl;
                }
            }
            if ($_REQUEST['SpecialID']) {
                $ifindex=1;
                $map['SpecialID'] = $_REQUEST['SpecialID'];
                $whereSearch['SpecialID'] = $_REQUEST['SpecialID'];
            }
            if (is_numeric($_REQUEST['Status'])) {
                $ifindex=1;
                $map['Status'] = $_REQUEST['Status'];
                $whereSearch['Status'] = $_REQUEST['Status'];
            }
            if ($_REQUEST['Test']) {
                $ifindex=1;
                $map['Test'] = $_REQUEST['Test'];
                $whereSearch['key']=$_REQUEST['Test'];
                $key=$_REQUEST['Test'];
                $orderSearch=array('@weight DESC, @id DESC');
            }
            if ($_REQUEST['searchStyle']) {
                $ifindex=1;
                $map['searchStyle'] = $_REQUEST['searchStyle'];
                $whereSearch['searchStyle']=$_REQUEST['searchStyle'];
            }
            if ($_REQUEST['field']) {
                $ifindex=1;
                $map['field'] = $_REQUEST['field'];
                $whereSearch['field']=$_REQUEST['field'];
            }
            if ($_REQUEST['orderby']) {
                $ifindex=1;
                $map['orderby'] = $_REQUEST['orderby'];
                $orderSearch=array(strtolower($_REQUEST['orderby']));
            }
            $showWhere=$_REQUEST['ShowWhere'];
            if ($showWhere) {
                if($showWhere=='All') $showWhere='0,1,2,3';
                $ifindex=1;
                $map['ShowWhere'] = $showWhere;
                $whereSearch['ShowWhere']=$showWhere;
            }
        }

        //显示所有试题
        $whereSearch['AatTestStyle'] = -1;

        $testReal = $this->getModel('TestReal'); //试题
        if($ifindex){
            $fieldSearch=array('testid','test','docid','answer','analytic','remark','docname','subjectname','typesname','specialname','diff','ifchoose','klnameonly','chapternameall','duplicate','gradename','score');

            $tmpStr=$testReal->getTestIndex($fieldSearch,$whereSearch,$orderSearch,$pageSearch);
            if($tmpStr === false){
                $this->setError('30504', 0);
            }
            $list=$tmpStr[0];
            $this->pageList($tmpStr[1], $tmpStr[2], $map);
            $idlist=array();
            foreach($list as $i=>$iList){
                $idlist[]=$iList['testid'];
            }
            if($idlist){
                $idList=implode(',',$idlist);
                $buffer = $testReal->unionSelect(
                    'testRealSelectByTestId',
                    $idList
                );
                $bufferX=array();
                foreach($buffer as $i=>$iBuffer){
                    $bufferX[$iBuffer['TestID']]['error']= R('Common/TestLayer/checkTestAttr',array($iBuffer));
                }
            }
            foreach($list as $i=>$iList){
                $list[$i]['error']=$bufferX[$iList['testid']]['error'];
            }
        }else{
            $types = SS('types');
            $knowledge = SS('knowledge');
            $knowledgeParent = SS('knowledgeParent');
            $chapterlist = SS('chapterList');
            $chapterparent = SS('chapterParentPath');
            $special = SS('special');
            $subject = SS('subject');
            $gradeArr=SS('grade');
            $where="1=1";
            $perpage=10;
            $testAttrReal=$this->getModel('TestAttrReal');
            $count = $testAttrReal->selectCount(
                $where,
                'TestID');
            $page = page($count,$_GET['p'],$perpage).','.$perpage;
            $buffer = $testAttrReal->pageData(
                'TestID',
                $where,
                'TestID desc',
                $page);
            $ids=array();
            foreach($buffer as $iBuffer){
                $ids[]=$iBuffer['TestID'];
            }
            if($ids){
                $list = $testReal->unionSelect('testRealSelectByTestId',implode(',',$ids));
            }
            $this->pageList($count, $perpage, $map);
            //$host=C('WLN_DOC_HOST');

            $dids=array(); //文档id列表
            $docArray=array(); //文档数据列表
            foreach($list as $listn){
                $dids[]=$listn['DocID'];
            }
            $buffer=$this->getModel('Doc')->selectData(
                'DocID,DocName',
                'DocID in ('.implode(',',$dids).') ');
            foreach($buffer as $iBuffer){
                $docArray[$iBuffer['DocID']]=$iBuffer;
            }

            foreach($list as $i=>$iList){
                $list[$i]=array_change_key_case($iList, CASE_LOWER);
                $list[$i]['docname']=$docArray[$iList['DocID']]['DocName'];
                $list[$i]['typesname']=$types[$list[$i]['typesid']]['TypesName'];
                $list[$i]['gradename']=$gradeArr[$list[$i]['gradeid']]['GradeName'];
                $list[$i]['specialname']=$special[$list[$i]['specialid']]['SpecialName'];
                $list[$i]['subjectname']=$subject[$subject[$list[$i]['subjectid']]['PID']]['SubjectName'].$subject[$list[$i]['subjectid']]['SubjectName'];
                $list[$i]['test']=R('Common/TestLayer/strFormat',array($list[$i]['test']));
                $list[$i]['answer']=R('Common/TestLayer/strFormat',array($list[$i]['answer']));
                $list[$i]['analytic']=R('Common/TestLayer/strFormat',array($list[$i]['analytic']));
                $list[$i]['remark']=R('Common/TestLayer/strFormat',array($list[$i]['remark']));
                $list[$i]['Score']=$iList['Score'];

                //获取knowledge
                $buffer = $this->getModel('TestKlReal')->selectData(
                    '*',
                    'TestID='.$iList['TestID']);
                if($buffer && $buffer[0]['KlID']){
                    $arrTemp=array();
                    foreach($buffer as $j=>$jBuffer ){
                        $arrTemp[]=$jBuffer['KlID'];
                    }
                    $list[$i]['klnameonly']=implode('<br/>',$this->getModel('Knowledge')->getKlOnly($arrTemp,array($knowledge,$knowledgeParent)));
                }
                //获取chapter
                $buffer = $this->getModel('TestChapterReal')->selectData(
                    'ChapterID',
                    'TestID='.$list[$i]['testid']);
                if($buffer){
                    $arrTemp=array();
                    foreach($buffer as $jBuffer){
                        $arrTemp[]=$jBuffer['ChapterID'];
                    }
                    $list[$i]['chapternameall']=implode('<br/>',$this->getModel('Chapter')->getChapterAll($arrTemp,$chapterparent,$chapterlist));
                }

                $list[$i]['error']= R('Common/TestLayer/checkTestAttr',array($iList));
            }
        }
        //难度
        $diffArray=C('WLN_TEST_DIFF');
        //学科
        $subjectArray=SS('subjectParentId'); //获取学科数据集

        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('diffArray', $diffArray);
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 将试题复制出试题库
     * @author demo
     */
    public function out() {
        $testID = $_POST['id']; //获取数据标识
        //判断数据标识
        if (empty ($testID)) {
            $this->setError('30301',1);
        }
        //判断权限
        if($this->ifSubject && $this->mySubject){
            $buffer = $this->getModel('TestAttrReal')->selectData(
                'SubjectID',
                'TestID in ('.$testID.')');
            foreach($buffer as $iBuffer){
                if(!in_array($iBuffer['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712',1);
                }
            }
        }elseif($this->ifDiff){
            $buffer=$this->getModel('TestReal')->unionSelect(
                'testRealDocSelectByTestId',$testID
            );
            //判断是否可以移出
            $adminName=$this->getCookieUserName();
            foreach($buffer as $iBuffer){
                if($iBuffer['Admin']!=$adminName){
                    $this->setError('30812',1);
                }
            }
        }
        $copyId=explode(',',$testID);
        $deleteId=array();

        //判断试题是否在正式数据表
        $buffer=$this->getModel('TestAttrReal')->selectData(
            'TestID',
            'TestID in ('.$testID.')');
        if(count($buffer)!=count($copyId)){
            $this->setError('1X2001',1); //所选试题部分已经不在试题库中
        }

        //判断试题是否在临时数据表
        $buffer=$this->getModel('TestAttr')->selectData(
            'TestID',
            'TestID in ('.$testID.')');
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                if(strstr(','.$testID.',',','.$iBuffer['TestID'].',')){
                    $tmpStr=str_replace(','.$iBuffer['TestID'].',',',',','.implode(',',$copyId).',');
                    $copyId=array_filter(explode(',',$tmpStr));
                }else{
                    $deleteId[]=$iBuffer['TestID'];
                }
            }
        }else{
            $deleteId=explode(',',$testID);
        }
        if($deleteId){
            $this->getModel('Test')->deleteData('TestID in ('.implode(',',$deleteId).')');
            $this->getModel('TestKl')->deleteData('TestID in ('.implode(',',$deleteId).')');
            $this->getModel('TestChapter')->deleteData('TestID in ('.implode(',',$deleteId).')');
        }
        if($copyId){
            $testID=implode(',',$copyId);
            //如果试题不在临时数据表 拷贝数据到库
            $this->getModel('Test')->insertSelect('','*','TestReal',$testID);
            $this->getModel('TestAttr')->insertSelect('','TestID,NumbID,DocID,TypesID,SpecialID,SubjectID,Diff,Mark,Admin,DfStyle,IfChoose,Duplicate,TestNum,TestStyle,OptionWidth,OptionNum,GradeID,FirstLoadTime,Status,IfWL,ShowWhere,Score,AatTestStyle','TestAttrReal',$testID);
            $this->getModel('TestKl')->insertSelect('TestID,KlID','TestID,KlID','TestKlReal',$testID);
            $this->getModel('TestChapter')->insertSelect('TestID,ChapterID','TestID,ChapterID','TestChapterReal',$testID);
            //写入日志
            $this->adminLog($this->moduleName, '移出修改试题TestID为【' . $testID . '】的数据');
        }
        $this->showSuccess('复制成功！');
    }
    /**
     * 将试题移出试题库
     * @author demo
     */
    public function outAndDelete() {
        $testID = $_POST['id']; //获取数据标识
        $oldTestID=$testID;
        //判断数据标识
        if (empty ($testID)) {
            $this->setError('30301',1);
        }
        //判断权限
        if($this->ifSubject && $this->mySubject){
            $buffer=$this->getModel('TestAttrReal')->selectData(
                'SubjectID',
                'TestID in ('.$testID.')');
            foreach($buffer as $iBuffer){
                if(!in_array($iBuffer['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712',1);
                }
            }
            unset($buffer);
        }elseif($this->ifDiff){
            $buffer=$this->testRealDocSelectByTestId($testID);
            $adminName=$this->getCookieUserName();
            foreach($buffer as $iBuffer){
                if($iBuffer['Admin']!=$adminName){
                    $this->setError('30812',1);
                }
            }
            unset($buffer);
        }
        $copyid=explode(',',$testID);
        //判断试题是否在临时数据表
        $testBuffer=$this->getModel('TestAttr')->selectData(
            'TestID,DocID',
            'TestID in ('.$testID.')');
        if($testBuffer){
            foreach($testBuffer as $i=>$iBuffer){
                if(strstr(','.$testID.',',','.$iBuffer['TestID'].',')){
                    $tmpStr=str_replace(','.$iBuffer['TestID'].',',',',','.implode(',',$copyid).',');
                    $copyid=array_filter(explode(',',$tmpStr));
                }
            }
        }
        //获取试题对应docID
        $dids=array();//存储需要操作的文档id
        $testBuffer=$this->getModel('TestAttrReal')->selectData(
            'TestID,DocID',
            'TestID in ('.$testID.')');
        if($testBuffer){
            foreach($testBuffer as $i=>$iTestBuffer){
                $dids[]=$iTestBuffer['DocID'];
            }
        }

        //复制试题到临时库
        if($copyid){
            $flag=array(1,1,1,1);
            $testID=implode(',',$copyid);
            //如果试题不在临时数据表 拷贝数据到库
            $flag[0]=$this->getModel('Test')->insertSelect('','*','TestReal',$testID);
            $flag[1]=$this->getModel('TestAttr')->insertSelect('','TestID,NumbID,DocID,TypesID,SpecialID,SubjectID,Diff,Mark,Admin,DfStyle,IfChoose,Duplicate,TestNum,TestStyle,OptionWidth,OptionNum,GradeID,FirstLoadTime,Status,IfWL,ShowWhere,Score,AatTestStyle','TestAttrReal',$testID);
            $flag[2]=$this->getModel('TestKl')->insertSelect('TestID,KlID','TestID,KlID','TestKlReal',$testID);
            $flag[3]=$this->getModel('TestChapter')->insertSelect('TestID,ChapterID','TestID,ChapterID','TestChapterReal',$testID);
            if($flag[0]===false || $flag[1]===false || $flag[2]===false || $flag[3]===false){
                $this->setError('1X2002',1);
            }
        }
        //删除试题
        if($oldTestID){
            //更新文档IfTest属性 和 IfIntro属性
            $dids=array_unique($dids);
            $lastdIds=array(); //更新文档测试
            $lastdIdsIntro=array(); //更新文档入库
            $buffer=$this->getModel('Doc')->selectData(
                'DocID,IfTest',
                'DocID in ('.implode(',',$dids).')');
            if($buffer){
                foreach($buffer as $iBuffer){
                    if($iBuffer['IfTest']==2){
                        $lastdIds[]=$iBuffer['DocID'];
                    }
                    $lastdIdsIntro[]=$iBuffer['DocID'];
                }
            }
            if($lastdIds) $this->getModel('Doc')->updateData(
                array('IfTest'=>1),
                'DocID in ('.implode(',',$lastdIds).')');
            if($lastdIdsIntro){
                $this->getModel('Doc')->updateData(
                    array('IfIntro'=>0,'IntroTime'=>0),
                    'DocID in ('.implode(',',$lastdIdsIntro).')');
            }
            $this->getModel('TestReal')->deleteData('TestID in ('.$oldTestID.')');
            $this->getModel('TestAttrReal')->deleteData('TestID in ('.$oldTestID.')');
            $this->getModel('TestKlReal')->deleteData('TestID in ('.$oldTestID.')');
            $this->getModel('TestChapterReal')->deleteData('TestID in ('.$oldTestID.')');
        }

        //删除索引
        $testReal=$this->getModel('TestReal');
        $testReal->deleteIndex($testID);

        if($lastdIdsIntro){
            $doc=$this->getModel('Doc');
            $doc->deleteIndex(implode(',',$lastdIdsIntro));
        }

        //写入日志
        $this->adminLog($this->moduleName, '移除试题TestID为【' . $oldTestID . '】的数据');
        $this->showSuccess('移除成功！');
    }

    /**
     * 试题word下载
     * @author demo
     */
    public function testdown() {
        $testID = $_GET['id']; //获取数据标识
        //$w = $_GET['w']; //获取数据标识
        //判断数据标识
        if (empty ($testID)) {
            $this->setError('30301',0);
        }
        if($this->ifSubject && $this->mySubject){
            $subject = $this->getModel('TestAttr')->selectData(
                'SubjectID',
                'TestID='.$testID);
            if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712',0);
            }
        }
        R('Common/TestLayer/singleDown',array($testID,0,1));
    }

    /**
     * 试题修改保存
     * @author demo
     */
    public function save() {
        $testID = $_POST['TestID'];
        //判断数据标识
        if (empty ($testID)) {
            $this->setError('30301',1);
        }
        //接收数据
        $typesID=$_POST['TypesID'];
        $kl=$_POST['kl'];
        $skill=$_POST['skill'];
        $capacity=$_POST['capacity'];
        $cp=$_POST['cp'];
        $specialID=$_POST['SpecialID'];
        $docID=$_POST['DocID'];
        $mark=$_POST['Mark'];
        $dfStyle=$_POST['DfStyle'];
        $ifChoose=$_POST['IfChoose'];
        $chooseList=$_POST['ChooseList'];
        $optionWidth=$_POST['OptionWidth'];
        $Score=$_POST['Score'];
        $optionNum=$_POST['OptionNum'];
        if($ifChoose==1 && $chooseList===''){
            $this->setError('1X2010',1);
        }
        $status=$_POST['Status'];
        //更改状态
        $test=$this->getModel('Test');

        //编辑谁的试题
        if($this->ifSubject && $this->mySubject){
            $subject = $this->getModel('TestAttr')->selectData(
                'SubjectID',
                'TestID='.$testID);
            if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712',1);
            }
        }elseif($this->ifDiff){
                $edit=$this->getModel('TestReal')->unionSelect(
                    'testRealDocSelectByTestId',
                    $testID
                );
                //判断是否可以编辑
                if($edit[0]['Admin']!=$this->getCookieUserName()){
                    $this->setError('30812',1);
                }
            }
        $data=array();
        if(is_numeric($status)){
            $data['Status']=$status;
            $this->getModel('Test')->updateData(
                $data,
                'TestID='.$testID);
        }
        $this->getModel('TestSkill')->setForTestID($testID,$skill);
        $this->getModel('TestCapacity')->setForTestID($testID,$capacity);

        $testKl=$this->getModel('TestKl');
        $buffer=$testKl->selectData(
            '*',
            'TestID='.$testID);
        //保存考点
        if($kl){
            $kl=explode(',',$kl);
            if($buffer){
                for($i=0;$i<count($buffer);$i++){
                    if($i>=count($kl)){
                        $testKl->deleteData(
                            'TklID in ('.$buffer[$i]['TklID'].')');
                        continue;
                    }
                    $data=array();
                    $data['TestID']=$testID;
                    $data['KlID']=$kl[$i];
                    $testKl->updateData(
                        $data,
                        'TklID='.$buffer[$i]['TklID']);
                }
                if($i<count($kl)){
                    for(;$i<count($kl);$i++){
                        $data=array();
                        $data['TestID']=$testID;
                        $data['KlID']=$kl[$i];
                        $testKl->insertData(
                            $data);
                    }
                }
            }else{
                foreach($kl as $iKl){
                    $data=array();
                    $data['TestID']=$testID;
                    $data['KlID']=$iKl;
                    $testKl->insertData(
                        $data);
                }
            }
        }else if($buffer){
            $delId=array();
            foreach($buffer as $iBuffer){
                $delId[]=$iBuffer['TklID'];
            }
            $testKl->deleteData(
                'TklID in ('.implode(',',$delId).')');
        }

        //保存章节
        $testChapter=$this->getModel('TestChapter');
        $buffer=$this->getModel('TestChapter')->selectData(
            '*',
            'TestID='.$testID);
        //if($cp){
        //    $Chapter=$this->getModel('Chapter');
        //    $cp=$Chapter->filterChapterID($cp);
        //}
        if($cp){
            $cp=explode(',',$cp);
            if($buffer){
                for($i=0;$i<count($buffer);$i++){
                    if($i>=count($cp)){
                        $this->getModel('TestChapter')->deleteData(
                            'TCID in ('.$buffer[$i]['TCID'].')');
                        continue;
                    }
                    $data=array();
                    $data['TestID']=$testID;
                    $data['ChapterID']=$cp[$i];
                    $this->getModel('TestChapter')->updateData(
                        $data,
                        'TCID='.$buffer[$i]['TCID']);
                }
                if($i<count($cp)){
                    for(;$i<count($cp);$i++){
                        $data=array();
                        $data['TestID']=$testID;
                        $data['ChapterID']=$cp[$i];
                        $this->getModel('TestChapter')->insertData(
                            $data);
                    }
                }
            }else{
                foreach($cp as $iCp){
                    $data=array();
                    $data['TestID']=$testID;
                    $data['ChapterID']=$iCp;
                    $this->getModel('TestChapter')->insertData(
                        $data);
                }
            }
        }else if($buffer){
            $delId=array();
            foreach($buffer as $iBuffer){
                $delId[]=$iBuffer['TCID'];
            }
            $this->getModel('TestChapter')->deleteData(
                'TCID IN ('.implode(',',$delId).')');
        }
        $buffer=$this->getModel('TestJudge')->selectData(
            '*',
            'TestID='.$testID,
            '`OrderID` asc');
        $testNum=0;
        $testStyle=1;
        if($ifChoose==0) $testStyle=3;
        if($ifChoose==2 || $ifChoose==3) $testStyle=1;

        if($chooseList!==''){
            $testStyle=0;
            $chooseArr=explode(',',$chooseList);
            $testNum=count($chooseArr);
            //更改复合题
            if($buffer){
                foreach($chooseArr as $i=>$iChooseArr){
                    if($testStyle==0){
                        if($iChooseArr!=0) $testStyle=1;
                        if($iChooseArr==0) $testStyle=3;
                    }else{
                        if($iChooseArr==0 && $testStyle==1) $testStyle=2;
                        if($iChooseArr==0 && $testStyle==3) $testStyle=3;
                        if($iChooseArr!=0 && $testStyle==3) $testStyle=2;
                    }
                    $data=array();
                    $data['TestID']=$testID;
                    $data['OrderID']=$i+1;
                    $data['IfChoose']=$iChooseArr;
                    if($buffer[$i]['JudgeID'])
                        $this->getModel('TestJudge')->updateData(
                            $data,
                            'JudgeID="'.$buffer[$i]['JudgeID'].'"');
                    else
                        $this->getModel('TestJudge')->insertData(
                            $data);
                }
                if(count($buffer)>count($chooseArr)){
                    $tmpArr=array();
                    for($i=count($chooseArr);$i<count($buffer);$i++){
                        $tmpArr[]=$buffer[$i]['JudgeID'];
                    }
                    $this->getModel('TestJudge')->deleteData(
                        'JudgeID in ('.implode(',',$tmpArr).')');
                }
            }else{
                foreach($chooseArr as $i=>$iChooseArr){
                    if($testStyle==0){
                        if($iChooseArr!=0) $testStyle=1;
                        if($iChooseArr==0) $testStyle=3;
                    }else{
                        if($iChooseArr==0 && $testStyle==1) $testStyle=2;
                        if($iChooseArr==0 && $testStyle==3) $testStyle=3;
                        if($iChooseArr!=0 && $testStyle==3) $testStyle=2;
                    }
                    $data=array();
                    $data['TestID']=$testID;
                    $data['OrderID']=$i+1;
                    $data['IfChoose']=$iChooseArr;
                    $this->getModel('TestJudge')->insertData(
                        $data);
                }
            }
        }else{
            //清空试题对应小题结构
            if($buffer){
                $tmpArr=array();
                foreach($buffer as $iBuffer){
                        $tmpArr[]=$iBuffer['JudgeID'];
                }
                $this->getModel('TestJudge')->deleteData(
                    'JudgeID in ('.implode(',',$tmpArr).')');
            }
        }


        //更改属性
        $testAttr=$this->getModel('TestAttr');
        $data=array();
        $data['TypesID']=$typesID;
        $data['DfStyle']=$dfStyle;
        $data['SpecialID']=$specialID;
        $data['IfChoose']=$ifChoose;
        $data['TestNum']=$testNum;
        $data['TestStyle']=$testStyle;
        $data['OptionWidth']=$optionWidth;
        $data['OptionNum']=$optionNum;
        $data['Score'] = $Score;

        switch($ifChoose){
            case 0:
            $chooseStr='非选择题';
            break;
            case 1:
            $chooseStr='复合题';
            break;
            case 2:
            $chooseStr='多选题';
            break;
            case 3:
            $chooseStr='单选题';
            break;
        }

        //如果有小题难度项目按照小题分组

        $types=SS('types');
        $typesArray=$types[$typesID];
        $diff=0;
        $testData=C('WLN_TEST_DATA');//难度和分值转换数组
        if($mark){
            $mark=explode(',',$mark); //所有mark分组
            //if(!is_array($Mark)){
            //    $Mark=array($Mark);
            //}
            $buffer=$this->getModel('Test')->selectData(
                '*',
                'TestID='.$testID);  //提取试题信息作为判断小题数的依据
            $times=$test->xtnum($buffer[0]['Test'],1);    //小题数量
            if($times){    //如果存在分组
                $ci=count($mark)/$times;    //每个分组的打分项数目
                $str='';
                $DiffArr=0;
                foreach($mark as $i=>$iMark){
                    //小题分组字符串
                    if($i%$ci==0 && $i!=0){
                        $DiffArr+=$testData[$diff];
                        $diff=0;
                        $str.='@#';//分组字符串标记
                    }
                    if($iMark) $str.=$iMark.'#';
                    //分组计算分值
                    $n=array();
                    $n=explode('|',$iMark);
                    if($n[1]>=1) $diff+=$n[1];
                }
                $diff=$DiffArr+$testData[$diff];
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
        }

        //计算难度值    带小题的试题计算平均难度保留两位小数
        //$Diff=count($kl);
        //if($Diff>3) $Diff=3;
        //$Diff+=$Types_array[0]['Num'];

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

        if($dfStyle) $diff=$_POST['Diff'];

        if(empty($diff)) $diff=0;
        $data['Diff']=$diff;
        $data['DocID']=$docID;

        if($this->getModel('TestAttr')->selectData(
            '*',
            'TestID='.$testID)){
            $this->getModel('TestAttr')->updateData(
                $data,
                'TestID='.$testID);
        }else{
            $data['TestID']=$testID;
            $this->getModel('TestAttr')->insertData(
                $data);
        }

        //计算返回数据
        $statusStr = $status==1 ? '<font color=\"red\">锁定</font>' : '正常';

        $typesStr=$typesArray['TypesName'];
        $special=SS('special');
        $specialStr=$special[$specialID]['SpecialName'];
        if(empty($specialStr)) $specialStr="<font color='red'>无</font>";
        $knowledgeStr="<font color='red'>无</font>";
        if(!empty($kl)){
            if(!is_array($kl)){
                $kl=array($kl);
            }
            $knowledge=SS('knowledge');
            $knowledgeParent=SS('knowledgeParent');
            //获取knowledge
            $arrTemp=array();
            foreach($kl as $i=>$iKl){
                $tmpStr='('.($i+1).')';
                foreach($knowledgeParent[$iKl] as $kKnowledgeParent){
                    $tmpStr.='>>'.$kKnowledgeParent['KlName'];
                }
                $arrTemp[]=$tmpStr.'>>'.$knowledge[$iKl]['KlName'];
            }
            $knowledgeStr=implode('<br/>',$arrTemp);
        }
        $chapterStr="<font color='red'>无</font>";
        if(!empty($cp)){
            if(!is_array($cp)){
                $cp=array($cp);
            }
            $chapterArr=SS('chapterList');
            $chapterParentArr=SS('chapterParentPath');
            $arrTemp=array();
            foreach($cp as $i=>$iCp){
                $tmpStr='('.($i+1).')';
                foreach($chapterParentArr[$iCp] as $jChapterParentArr){
                    $tmpStr.='>>'.$jChapterParentArr['ChapterName'];
                }
                $arrTemp[]=$tmpStr.'>>'.$chapterArr[$iCp]['ChapterName'];
            }
            $chapterStr=implode('<br/>',$arrTemp);
        }
        $diffStr=$diff;


        //写入日志
        $this->adminLog($this->moduleName, '修改试题TestID为【' . $testID . '】的数据');
        $list = $this->getModel('Test')->unionSelect('testSelectByTestId',$testID);
        $errorStr= R('Common/TestLayer/checkTestAttr',array($list[0]));
        if(empty($errorStr)) $errorStr='无';
        else $errorStr="<font color='red'>".$errorStr.'</font>';
        $this->setBack('<script>'.( is_numeric($status) ? 'document.getElementById("status'.$testID.'").innerHTML="'.$statusStr.'";' : '') .
                'document.getElementById("types'.$testID.'").innerHTML="'.$typesStr.'";' .
                'document.getElementById("special'.$testID.'").innerHTML="'.$specialStr.'";' .
                'document.getElementById("knowledge'.$testID.'").innerHTML="'.$knowledgeStr.'";' .
                'document.getElementById("chapter'.$testID.'").innerHTML="'.$chapterStr.'";' .
                'document.getElementById("diff'.$testID.'").innerHTML="'.$diffStr.'";' .
                'document.getElementById("error'.$testID.'").innerHTML="'.$errorStr.'";' .
                'document.getElementById("choose'.$testID.'").innerHTML="'.$chooseStr.'";' .
                'document.body.removeChild(document.getElementById("popup_overlay"));' .
                'document.body.removeChild(document.getElementById("popup_container"));' .
                '</script>');
    }

    /**
     * 入库试题修改
     * @author demo
     */
    public function saveIntro() {

        $testID = $_POST['TestID'];
        //判断数据标识
        if (empty ($testID)) {
            $this->setError('30301',1);
        }
        //接收数据
        $typesID=$_POST['TypesID'];
        $kl=$_POST['kl'];
        $cp=$_POST['cp'];
        $skill=$_POST['skill'];
        $capacity=$_POST['capacity'];
        $specialID=$_POST['SpecialID'];
        $docID=$_POST['DocID'];
        $mark=$_POST['Mark'];
        $dfStyle=$_POST['DfStyle'];
        $ifChoose=$_POST['IfChoose'];
        $chooseList=$_POST['ChooseList'];
        $optionWidth=$_POST['OptionWidth'];
        $optionNum=$_POST['OptionNum'];
        $Score=$_POST['Score'];
        if($ifChoose==1 && $chooseList===''){
            $this->setError('1X2015',1);
        }
        $status=$_POST['Status'];
        //更改状态
        $test=$this->getModel('TestReal');

        //编辑谁的试题
        if($this->ifSubject && $this->mySubject){
            $subject = $this->getModel('TestAttrReal')->selectData(
                'SubjectID',
                'TestID='.$testID);
            if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712',1);
            }
        }elseif($this->ifDiff){
                $edit=$this->getModel('Test')->unionSelect(
                    'testDocSelectByTestID',
                    $testID
                );
                //判断是否可以编辑
                if($edit[0]['Admin']!=$this->getCookieUserName()){
                    $this->setError('30812',1);
                }
            }
        $data=array();
        if(is_numeric($status)){
            $data['Status']=$status;
            $this->getModel('TestReal')->updateData(
                $data,
                'TestID='.$testID);
        }

        $this->getModel('TestSkill')->setForTestID($testID,$skill);
        $this->getModel('TestCapacity')->setForTestID($testID,$capacity);

        $buffer=$this->getModel('TestKlReal')->selectData(
            '*',
            'TestID='.$testID);
        //保存考点
        if($kl){
            $kl=explode(',',$kl);
            if($buffer){
                for($i=0;$i<count($buffer);$i++){
                    if($i>=count($kl)){
                        $this->getModel('TestKlReal')->deleteData(
                            'TklID in ('.$buffer[$i]['TklID'].')');
                        continue;
                    }
                    $data=array();
                    $data['TestID']=$testID;
                    $data['KlID']=$kl[$i];
                    $data['TklID']=$buffer[$i]['TklID'];
                    $this->getModel('TestKlReal')->updateData(
                        $data,
                        'TklID='.$buffer[$i]['TklID']);
                }
                if($i<count($kl)){
                    for(;$i<count($kl);$i++){
                        $data=array();
                        $data['TestID']=$testID;
                        $data['KlID']=$kl[$i];
                        $this->getModel('TestKlReal')->insertData(
                            $data);
                    }
                }
            }else{
                foreach($kl as $iKl){
                    $data=array();
                    $data['TestID']=$testID;
                    $data['KlID']=$iKl;
                    $this->getModel('TestKlReal')->insertData(
                        $data);
                }
            }
        }else if($buffer){
            $delId=array();
            foreach($buffer as $iBuffer){
                $delId[]=$iBuffer['TklID'];
            }
            $this->getModel('TestKlReal')->deleteData(
                'TklID in ('.implode(',',$delId).')');
        }

        //保存章节
        $buffer=$this->getModel('TestChapterReal')->selectData(
            '*',
            'TestID='.$testID);
        //if($cp){
        //    $Chapter=$this->getModel('Chapter');
        //    $cp=$Chapter->filterChapterID($cp);
        //}
        if($cp){
            $cp=explode(',',$cp);
            if($buffer){
                for($i=0;$i<count($buffer);$i++){
                    if($i>=count($cp)){
                        $this->getModel('TestChapterReal')->deleteData(
                            'TCID in ('.$buffer[$i]['TCID'].')');
                        continue;
                    }
                    $data=array();
                    $data['TestID']=$testID;
                    $data['ChapterID']=$cp[$i];
                    $this->getModel('TestChapterReal')->updateData(
                        $data,
                        'TCID='.$buffer[$i]['TCID']);
                }
                if($i<count($cp)){
                    for(;$i<count($cp);$i++){
                        $data=array();
                        $data['TestID']=$testID;
                        $data['ChapterID']=$cp[$i];
                        $this->getModel('TestChapterReal')->insertData(
                            $data);
                    }
                }
            }else{
                foreach($cp as $iCp){
                    $data=array();
                    $data['TestID']=$testID;
                    $data['ChapterID']=$iCp;
                    $this->getModel('TestChapterReal')->insertData(
                        $data);
                }
            }
        }else if($buffer){
            $delId=array();
            foreach($buffer as $iBuffer){
                $delId[]=$iBuffer['TCID'];
            }
            $this->getModel('TestChapterReal')->deleteData(
                'TCID in('.implode(',',$delId).')');
        }

        $buffer=$this->getModel('TestJudge')->selectData(
            '*',
            'TestID='.$testID,
            '`OrderID` asc');
        $testNum=0;
        $testStyle=1;
        if($ifChoose==0) $testStyle=3;
        if($ifChoose==2 || $ifChoose==3) $testStyle=1;

        if($chooseList!==''){
            $testStyle=0;
            $chooseArr=explode(',',$chooseList);
            $testNum=count($chooseArr);
            //更改复合题
            if($buffer){
                foreach($chooseArr as $i=>$iChooseArr){
                    if($testStyle==0){
                        if($iChooseArr!=0) $testStyle=1;
                        if($iChooseArr==0) $testStyle=3;
                    }else{
                        if($iChooseArr==0 && $testStyle==1) $testStyle=2;
                        if($iChooseArr==0 && $testStyle==3) $testStyle=3;
                        if($iChooseArr!=0 && $testStyle==3) $testStyle=2;
                    }
                    $data=array();
                    $data['TestID']=$testID;
                    $data['OrderID']=$i+1;
                    $data['IfChoose']=$iChooseArr;
                    if($buffer[$i]['JudgeID'])
                        $this->getModel('TestJudge')->updateData(
                            $data,
                            'JudgeID="'.$buffer[$i]['JudgeID'].'"');
                    else
                        $this->getModel('TestJudge')->insertData(
                            $data);
                }
                if(count($buffer)>count($chooseArr)){
                    $tmpArr=array();
                    for($i=count($chooseArr);$i<count($buffer);$i++){
                        $tmpArr[]=$buffer[$i]['JudgeID'];
                    }
                    $this->getModel('TestJudge')->deleteData(
                        'JudgeID in ('.implode(',',$tmpArr).')');
                }
            }else{
                foreach($chooseArr as $i=>$iChooseArr){
                    if($testStyle==0){
                        if($iChooseArr!=0) $testStyle=1;
                        if($iChooseArr==0) $testStyle=3;
                    }else{
                        if($iChooseArr==0 && $testStyle==1) $testStyle=2;
                        if($iChooseArr==0 && $testStyle==3) $testStyle=3;
                        if($iChooseArr!=0 && $testStyle==3) $testStyle=2;
                    }
                    $data=array();
                    $data['TestID']=$testID;
                    $data['OrderID']=$i+1;
                    $data['IfChoose']=$iChooseArr;
                    $this->getModel('TestJudge')->insertData(
                        $data);
                }
            }
        }else{
            //清空试题对应小题结构
            if($buffer){
                $tmpArr=array();
                foreach($buffer as $iBuffer){
                        $tmpArr[]=$iBuffer['JudgeID'];
                }
                $this->getModel('TestJudge')->deleteData(
                    'JudgeID in ('.implode(',',$tmpArr).')');
            }
        }


        //更改属性
        $testAttr=$this->getModel('TestAttrReal');
        $data=array();
        $data['TypesID']=$typesID;
        $data['DfStyle']=$dfStyle;
        $data['SpecialID']=$specialID;
        $data['IfChoose']=$ifChoose;
        $data['TestNum']=$testNum;
        $data['TestStyle']=$testStyle;
        $data['OptionWidth']=$optionWidth;
        $data['OptionNum']=$optionNum;
        $data['Score']=$Score;

        switch($ifChoose){
            case 0:
            $chooseStr='非选择题';
            break;
            case 1:
            $chooseStr='复合题';
            break;
            case 2:
            $chooseStr='多选题';
            break;
            case 3:
            $chooseStr='单选题';
            break;
        }

        //如果有小题难度项目按照小题分组

        $types=SS('types');
        $typesArray=$types[$typesID];

        $diff=0;
        $testData=C('WLN_TEST_DATA');//难度和分值转换数组
        if($mark){

            $mark=explode(',',$mark); //所有mark分组
            //if(!is_array($Mark)){
            //    $Mark=array($Mark);
            //}
            $buffer=$this->getModel('TestReal')->selectData(
                '*',
                'TestID='.$testID);    //提取试题信息作为判断小题数的依据
            $times=$this->getModel('Test')->xtnum($buffer[0]['Test'],1);    //小题数量
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
        }
        //计算难度值    带小题的试题计算平均难度保留两位小数
        //$Diff=count($kl);
        //if($Diff>3) $Diff=3;
        //$Diff+=$Types_array[0]['Num'];

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

        if($dfStyle) $diff=$_POST['Diff'];

        if(empty($diff)) $diff=0;
        $data['Diff']=$diff;
        $data['DocID']=$docID;
        $data['LoadTime']=time(); //更新索引
        if($this->getModel('TestAttrReal')->selectData(
            '*',
            'TestID='.$testID)){
            $this->getModel('TestAttrReal')->updateData(
                $data,
                'TestID='.$testID);
        }else{
            $data['TestID']=$testID;
            $this->getModel('TestAttrReal')->insertData(
                $data);
        }

        //计算返回数据
        $statusStr = $status==1 ? '<font color=\"red\">锁定</font>' : '正常';

        $typesStr=$typesArray['TypesName'];
        $special=SS('special');
        $SpecialArray=$special[$specialID];
        $specialStr=$SpecialArray['SpecialName'];
        if(empty($specialStr)) $specialStr="<font color='red'>无</font>";
        $knowledgeStr="<font color='red'>无</font>";
        if(!empty($kl)){
            if(!is_array($kl)){
                $kl=array($kl);
            }
            $knowledge=SS('knowledge');
            $knowledgeParent=SS('knowledgeParent');
            //获取knowledge
            $arrTemp=array();
            foreach($kl as $i=>$iKl){
                $tmpStr='('.($i+1).')';
                foreach($knowledgeParent[$iKl] as $kKnowledgeParent){
                    $tmpStr.='>>'.$kKnowledgeParent['KlName'];
                }
                $arrTemp[]=$tmpStr.'>>'.$knowledge[$iKl]['KlName'];
            }
            $knowledgeStr=implode('<br/>',$arrTemp);
        }
        $chapterStr="<font color='red'>无</font>";
        if(!empty($cp)){
            if(!is_array($cp)){
                $cp=array($cp);
            }
            $chapterArr=SS('chapterList');
            $chapterParentArr=SS('chapterParentPath');
            $arrTemp=array();
            foreach($cp as $i=>$iCp){
                $tmpStr='('.($i+1).')';
                foreach($chapterParentArr[$iCp] as $jChapterParentArr){
                    $tmpStr.='>>'.$jChapterParentArr['ChapterName'];
                }
                $arrTemp[]=$tmpStr.'>>'.$chapterArr[$iCp]['ChapterName'];
            }
            $chapterStr=implode('<br/>',$arrTemp);
        }
        $diffstr=$diff;


        //写入日志
        $this->adminLog($this->moduleName, '修改试题TestID为【' . $testID . '】的数据');
        $list = $this->getModel('TestReal')->unionSelect('testRealSelectByTestId',$testID);
        $errorStr= R('Common/TestLayer/checkTestAttr',array($list[0]));
        if(empty($errorStr)) $errorStr='无';
        else $errorStr="<font color='red'>".$errorStr.'</font>';
        $this->setBack('<script>'.
                'document.getElementById("types'.$testID.'").innerHTML="'.$typesStr.'";' .
                'document.getElementById("error'.$testID.'").innerHTML="'.$errorStr.'";' .
                'document.getElementById("choose'.$testID.'").innerHTML="'.$chooseStr.'";' .
                'document.getElementById("knowledge'.$testID.'").innerHTML="'.$knowledgeStr.'";' .
                'document.getElementById("chapter'.$testID.'").innerHTML="'.$chapterStr.'";' .
                'document.getElementById("diff'.$testID.'").innerHTML="'.$diff.'";' .
                'document.body.removeChild(document.getElementById("popup_overlay"));' .
                'document.body.removeChild(document.getElementById("popup_container"));' .
                '</script>');
    }

    /**
     * 替换试题
     * @author demo
     */
    public function replace() {
        $testID = $_REQUEST['TestID']; //获取数据标识
        //判断数据标识
        if (empty ($testID)) {
            $this->setError('30301',0);
        }
        $test = $this->getModel('Test');
        //判断替换权限
        if($this->ifSubject && $this->mySubject){
            $subject = $this->getModel('TestAttr')->selectData(
                'SubjectID',
                'TestID='.$testID);
            if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712',0);
            }
        }elseif($this->ifDiff){
            $edit=$this->getModel('Test')->unionSelect('testDocSelectByTestID',$testID);
            //判断是否可以编辑
            if($edit[0]['Admin']!=$this->getCookieUserName()){
                $this->setError('30812',0);
            }
        }

        if(IS_POST){
            //替换试题数据
            if (!empty ($_FILES['photo']['name']) && !empty ($_FILES['photo']['size'])) {

                $data = array ();
                $data['TestID'] = $testID;
                $data['Admin'] = $this->getCookieUserName();
                $data['LoadTime'] = time();

                //此处不检测word文档
                C('WLN_DOC_OPEN_CHECK',0);

                //上传并检测word文档
                $doc = $this->getModel('Doc');
                $output=R('Common/UploadLayer/uploadWordAndCheck');
                if(is_numeric($output[0]) && !empty($output[0])) $this->setError($output[0],0,'',$output[1]);
                $data['DocPath']=$output[0];
                $docHtmlPath=$data['DocHtmlPath']=$output[1];
                $data['DocFilePath']=$output[2];

                //记录替换文档
                $buffer = $this->getModel('TestReplace')->selectData(
                    '*',
                    'TestID=' . $testID,1);
                if ($buffer) {
                    //删除原有Replace数据
                    $doc->deleteReplaceFile($buffer[0]['ReplaceID']);

                    $this->getModel('TestReplace')->updateData(
                        $data,
                        'ReplaceID='.$buffer[0]['ReplaceID']);
                    $this->adminLog($this->moduleName, '修改替换试题TestID为【' . $testID . '】');
                } else {
                    $this->getModel('TestReplace')->insertData(
                        $data);
                    $this->adminLog($this->moduleName, '添加替换试题TestID为【' . $testID . '】');
                }
                //替换试题内容
                $data = array ();
                $data['TestID'] = $testID;

                $buffer = $this->getModel('TestTag')->selectData(
                    '*',
                    '1=1',
                    'OrderID asc');
                if (!$buffer)
                    $this->setError('30707',0,U('TestTag/index'));


                $start = array ();
                $testField = array ();
                foreach ($buffer as $iBuffer) {
                    $start[] = $iBuffer['DefaultStart'];
                    $testField[] = $iBuffer['TestField'];
                }
                $html = $doc->getDocContent($docHtmlPath);  //获取html数据

                $arrDoc = $doc->division($html, $start,1); //分割
                $arrHtml = $doc->division($html, $start,2); //分割
                $newArrDoc = $doc->changeArrFormatDoc($arrDoc); //doc过滤
                $newArr = $doc->changeArrFormat($arrHtml); //html过滤


                $testFieldArr = $doc->getTestField(); //数据表字段对应数组

                $dataX=array();
                $dataX['TestID']=$testID;
                //单条数据记录
                foreach ($newArr[0] as $i => $iNewArr) {
                    if(!strstr($testField[$i],'属性')){
                        $data[$testFieldArr[$testField[$i]]] = $iNewArr;
                        $dataX['Doc' . $testFieldArr[$testField[$i]]] = $newArrDoc[0][$i];
                    }
                }

                $this->getModel('Test')->updateData(
                    $data,
                    'TestID='.$testID);
                $this->getModel('TestDoc')->updateData(
                    $dataX,
                    'TestID='.$testID);
                $this->showSuccess('试题替换成功！',U('Test/replace',array('TestID'=>$testID)));
            }
            $this->setError('1X2003',0);
        }

        $pageName = '替换试题';
        $edit = $this->getModel('Test')->unionSelect('testSelectByTestId',$testID);
        $typesBuffer=SS('types');
        $edit[0]['TypesName']=$typesBuffer[$edit[0]['TypesID']]['TypesName'];
        //$host=C('WLN_DOC_HOST');
        $width=600;
        $edit[0]['Test']=$test->formatTest($edit[0]['Test'],1,$width,0,1,$edit[0]['OptionWidth'],$edit[0]['OptionNum'],$edit[0]['TestNum'],$edit[0]['IfChoose'],1);
        $edit[0]['Answer']=$test->formatTest($edit[0]['Answer'],1,0,0,0,0,0,$edit[0]['TestNum'],0,1);
        $edit[0]['Analytic']=$test->formatTest($edit[0]['Analytic'],1,0,0,0,0,0,$edit[0]['TestNum'],0,1);
        $edit[0]['Remark']=$test->formatTest($edit[0]['Remark'],1,0,0,0,0,0,$edit[0]['TestNum'],0,1);
        $edit[0]['Test']= R('Common/TestLayer/strFormat',array($edit[0]['Test']));
        $edit[0]['Answer']=R('Common/TestLayer/strFormat',array($edit[0]['Answer']));
        $edit[0]['Analytic']=R('Common/TestLayer/strFormat',array($edit[0]['Analytic']));
        $edit[0]['Remark']=R('Common/TestLayer/strFormat',array($edit[0]['Remark']));


        /*载入模板标签*/
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display('Test/replace');
    }
    /**
     * 试题排重，ajax获取数据
     * @author demo
     * @date 2015-6-19
     */
    public function duplicateTest(){
        //验证排重功能是否开启
        if(($tmpError=R('Common/TestLayer/ifExcludeRepeat'))!=''){
            $this->setError($tmpError,1);
        }

        $docID = (int)$_POST['docid']; //获取数据标识
        $in = (int)$_POST['in']; //获取数据标识
        //判断数据标识
        if (empty ($docID)) {
            $this->setError('30301',1);
        }
        //判断权限
        if($this->ifSubject && $this->mySubject){
            $docList=$this->getModel('Doc')->selectData(
                'DocID,Admin,SubjectID',
                'DocID='.$docID);
            if(!in_array($docList[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30812',1);
            }
        }elseif($this->ifDiff){
            $docList=$this->getModel('Doc')->selectData(
                'DocID,Admin',
                'DocID='.$docID);
            if($docList[0]['Admin']!=$this->getCookieUserName()){
                $this->setError('30812',1);
            }
        }
        $page = (int)$_POST['page'];
        // if(0 == $page){
        //     $page = 1;
        // }
        $prepage = (int)$_POST['prepage'];
        $testReal = $this->getModel('TestReal');
        $list = $testReal->getTestByDocId($docID, $in, $page, $prepage);
        $list['list'] = $testReal->getSimilarities($list['list'], $in);
        if($list['list'][0] === false){
            if(is_numeric($list['list'][1])) $this->setError($list['list'][1],1,'',$list['list'][2]);
            $this->setError('30504',1); //查询发生异常！
        }
        $this->setBack($list);
    }

    /**
     * 试题去重操作
     * @author demo
     * @date 2014年10月14日
     */
    public function removeDuplicate(){
        $in = $_GET['in']; //获取数据标识
        if(empty($in)) $in=0;
        $docId = (int)$_GET['DocID']; //获取数据标识
        $this->assign('in', $in); // 赋值数据集
        $this->assign('docid', $docId);
        $this->assign('pageName', '试题去重'); //页面标题
        $this->display("Test/removeduplicate");
    }


    /**
     * 自定义排重，重题查看
     * @author demo
     * @date 2015-7-10
     */
    public function getDuplicateTest(){
        $id = $_POST['id'];
        if(!is_numeric($id)){
            $this->setError('30301', 1);
        }
        //有限查找已入库试题
        $result = $this->getModel('TestAttrReal')->getDuplicationByDupId($id);
        if(empty($result[0])){
            $result = $this->getModel('TestAttr')->getDuplicationByDupId($id);
        }
        $this->setBack($result);
    }

    /**
     * 自定义去重
     * @author demo
     * @date 2015-6-30
     */
    public function customRemoveDuplication(){
        $id = (int)$_POST['id'];
        $dupid = $_POST['dupid'];
        if(0 == $id || empty($dupid)){
            $this->setError('30301',1);
        }
        //验证所发送的重题id是否和数据库查询的数据对应
        // $dupid = array(key1|dup1(dup=$key)|dup2(dup=$key),key2|dup23432(dup=$key)|dup445(dup=$key),....);
        $dupid = explode(',', $dupid);
        $temp = array(); //用于最小的试题id查找
        //分解出第一个试题及其相似题
        $firstElemetns = $duplications = array();
        foreach($dupid as $key=>$value){
            $value = explode('|', $value);
            $first = array_shift($value);
            $temp[] = $first;
            if(!empty($first)){
                $firstElemetns[] = $first;
                //如果$value不为空，则存在其他重复试题
                if(!empty($value)){
                    $duplications[$first] = $value;
                }
                $temp = array_merge($temp, $value);
            }else{
                unset($dupid[$key]);
            }
        }
        $testResult = $this->getModel('TestAttr')->unionSelect(
            'getDuplicateTestJoinQuery',
            'TestAttr',
            'Test' ,
            'a.TestID, Duplicate',
            't.TestID = a.TestID AND Duplicate IN ('.implode(',', $firstElemetns).')'
        );
        $testRealResult = $this->getModel('TestAttrReal')->unionSelect(
            'getDuplicateTestJoinQuery',
            'TestAttrReal',
            'TestReal' ,
            'a.TestID, Duplicate',
            't.TestID = a.TestID AND Duplicate IN ('.implode(',', $firstElemetns).')'
        );
        $result = array_merge((array)$testResult, (array)$testRealResult); //将已入库试题与未入库试题合并
        unset($testResult, $testRealResult);
        //获取以duplicate为键的关联数组，用于后部比对
        $firstElemetns = array();
        foreach($result as $value){
            $duplicateid = $value['Duplicate'];
            $testid = $value['TestID'];
            //兼容$testResult与$testRealResult存在相同数据的问题
            if(!in_array($testid, $firstElemetns[$duplicateid])){
                $firstElemetns[$duplicateid][] = $testid;
            }
        }
        //查询firstElemetns中的相关
        foreach($duplications as $key=>$value){
            if(!array_key_exists($key, $firstElemetns)){
                $firstElemetns[$key] = array();
            }
            $verify = array_merge(array_diff($value, $firstElemetns[$key]),array_diff($firstElemetns[$key], $value));
            //$key查询出的试题数量与所选的key中dup试题数量不一致
            if(count($verify) > 0){
                $msg = '所选的试题['.$key.']，相似题['.implode(' ', $value).']，差异试题['. implode(' ', $verify).']';
                $this->setError('1X2011', 1, '', $msg);
            }
        }
        unset($firstElemetns);
        //将当前试题id加入到重题编号中，同时查出最小值
        $temp[] = $id;
        $minimum = min($temp);
        $this->copyAttr($temp);
        $this->setBack($minimum);
    }

    /**
     * 标重之前检查相关试题是已标重
     * @author demo
     * @date 2015-7-8
     */
    public function checkDuplicateTest(){
        $dupid = $_POST['dupid'];
        $isDumplate = $_POST['isDumplate'];
        $result = array();
        if($isDumplate){
            $result = $this->getModel('TestAttrReal')->getTheDuplicateTest($dupid);
        }else{
            $result = $this->getModel('TestAttr')->getTheDuplicateTest($dupid);
        }
        if(empty($result)){
            $this->setBack('success');
        }
        $this->setBack($result);
    }

    /**
     * 试题重复标记
     * @author demo
     * @date 2014年10月14日
     */
    public function mark() {
        //修改试题属性 规则：
        //1.凡是试题属性为空，被标注重复后，试题属性和试题库试题属性一致。若被取消，属性不变。
        //2.凡是试题属性不为空，被标注重复后，其属性不变。若取消，属性不变。
        //3.凡是库内试题重复，各属性不变。
        $testID=$_POST['id']; //获取数据标识
        $duplicate=$_POST['Duplicate'];//获取重复试题ID
        $style=$_POST['style'];//状态

        //处理试题
        $duplicate=explode(',',$duplicate);
        $duplicate[]=$testID;
        $duplicate=array_filter($duplicate);

        //判断数据标识
        if (empty ($testID)) {
            $this->setError('30301',1);
        }
        if($duplicate==''){
            $this->setError('1X2013',1);
        }
        if($this->ifSubject && $this->mySubject){
            $testArr = $this->getModel('TestAttr')->selectData(
                'SubjectID',
                'TestID in ('.implode(',',$duplicate).')');
            foreach($testArr as $i=>$iTestArr){
                if(!in_array($iTestArr['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712',1);
                }
            }
        }
        //取消标记
        if($style=='unlock'){
            $data=array();
            $data['Duplicate']=0;
            $this->getModel('TestAttr')->updateData(
                $data,
                'TestID in ('.implode(',',$duplicate).')');
            $this->getModel('TestAttrReal')->updateData(
                $data,
                'TestID in ('.implode(',',$duplicate).')');
            $this->setBack('success');
        }

        $result=$this->copyAttr($duplicate);
        $this->setBack($result);
    }

    //复制重题属性 库内题仅改变duplicate字段
    private function copyAttr($duplicate){
        $mins=min($duplicate);
        $attrReal=$this->getModel('TestAttrReal')->selectData(
            'TestID,TypesID,SpecialID,Diff,Mark,DfStyle',
            'TestID in ('.implode(',',$duplicate).')');
        $attr=$this->getModel('TestAttr')->selectData(
            'TestID,TypesID,SpecialID,Diff,Mark,DfStyle',
            'TestID in ('.implode(',',$duplicate).')');
        /*//获取试题的类型属性
        $testType=array();
        $buffer=$testJudge->selectData('TestID,OrderID,IfChoose','TestID in ('.implode(',',$duplicate).')','OrderID ASC');
        foreach($buffer as $i=>$iBuffer){
            $testType[$iBuffer['TestID']][]=$iBuffer['IfChoose'];
        }*/
        //获取试题的章节
        $chapterBuffer=array();
        $chapterRealBuffer=array();
        $buffer=$this->getModel('TestChapter')->selectData(
            'TestID,ChapterID',
            'TestID in ('.implode(',',$duplicate).')',
            'TCID ASC');
        foreach($buffer as $i=>$iBuffer){
            $chapterBuffer[$iBuffer['TestID']][]=$iBuffer['ChapterID'];
        }
        $buffer=$this->getModel('TestChapterReal')->selectData(
            'TestID,ChapterID',
            'TestID in ('.implode(',',$duplicate).')',
            'TCID ASC');
        foreach($buffer as $i=>$iBuffer){
            $chapterRealBuffer[$iBuffer['TestID']][]=$iBuffer['ChapterID'];
        }
        //获取试题的知识点
        $klBuffer=array();
        $klRealBuffer=array();
        $buffer=$this->getModel('TestKl')->selectData(
            'TestID,KlID',
            'TestID in ('.implode(',',$duplicate).')',
            'TklID ASC');
        foreach($buffer as $i=>$iBuffer){
            $klBuffer[$iBuffer['TestID']][]=$iBuffer['KlID'];
        }
        $buffer=$this->getModel('TestKlReal')->selectData(
            'TestID,KlID',
            'TestID in ('.implode(',',$duplicate).')',
            'TklID ASC');
        foreach($buffer as $i=>$iBuffer){
            $klRealBuffer[$iBuffer['TestID']][]=$iBuffer['KlID'];
        }
        unset($buffer);

        if($attr || $attrReal){
            $listAttr=array();
            $listAttrReal=array();
            foreach($attrReal as $iAttrReal){
                $listAttrReal[$iAttrReal['TestID']]=$iAttrReal;
            }
            foreach($attr as $iAttr){
                $listAttr[$iAttr['TestID']]=$iAttr;
            }
            //修改试题属性 如果试题有属性则不修改
            $minsBuffer=$listAttrReal[$mins];
            if(!$minsBuffer) $minsBuffer=$listAttr[$mins];
            $minsChapter=$chapterBuffer[$mins];
            if(!$minsChapter) $minsChapter=$chapterRealBuffer[$mins];
            $minsKl=$klBuffer[$mins];
            if(!$minsKl) $minsKl=$klRealBuffer[$mins];
            foreach($attr as $iAttr){
                if($iAttr['TestID']==$mins) continue;
                $data=array();
                $data['TestID']=$iAttr['TestID'];
                if(!$iAttr['TypesID']) $data['TypesID']=$minsBuffer['TypesID'];
                if(!$iAttr['SpecialID']) $data['SpecialID']=$minsBuffer['SpecialID'];
                if(!$iAttr['Diff'] || $iAttr['Diff']=='0.000'){
                    $data['Diff']=$minsBuffer['Diff'];
                    $data['DfStyle']=$minsBuffer['DfStyle'];
                }
                if(!$iAttr['Mark']){
                    $data['Mark']=$minsBuffer['Mark'];
                    $data['DfStyle']=$minsBuffer['DfStyle'];
                }
                $data['Duplicate']=$mins;
                $this->getModel('TestAttr')->updateData(
                    $data,
                    'TestID='.$iAttr['TestID']);
                //章节
                if($minsChapter && !$chapterBuffer[$iAttr['TestID']][0]){
                    $this->getModel('TestChapter')->deleteData(
                        'TestID in ('.$iAttr['TestID'].')');
                    foreach($minsChapter as $i=>$iMinsChapter){
                        $data=array();
                        $data['TestID']=$iAttr['TestID'];
                        $data['ChapterID']=$iMinsChapter;
                        $this->getModel('TestChapter')->insertData(
                            $data);
                    }
                }
                //知识点
                if($minsKl && !$klBuffer[$iAttr['TestID']][0]){
                    $this->getModel('TestKl')->deleteData(
                        'TestID in ('.$iAttr['TestID'].')');
                    foreach($minsKl as $i=>$iMinsKl){
                        $data=array();
                        $data['TestID']=$iAttr['TestID'];
                        $data['KlID']=$iMinsKl;
                        $this->getModel('TestKl')->insertData(
                            $data);
                    }
                }
            }
            //更新库内重题
            $this->getModel('TestAttrReal')->updateData(
                array('Duplicate'=>$mins),
                'TestID in ('.implode(',',$duplicate).')');
            $this->getModel('TestAttrReal')->updateData(
                array('Duplicate'=>0),
                'TestID ='.$mins);
            $this->getModel('TestAttr')->updateData(
                array('Duplicate'=>0),
                'TestID ='.$mins);
        }
        return 'success';
    }
    /**
     * 重复试题列表，用于清除试题的时候查看
     * @author demo
     * @date 2014年10月9日
     */
    public function duplicateList(){
        //通过传递过来的重复试题ID，找到所有的重复ID等于这个ID的试题，和试题ID等于这个ID的试题
        $duplicate=$_POST['duplicate'];
        $pageName = '消除重复';
        $act='清除重复';                    //模板标识
        $field="TestID,Test";
        //$host=C('WLN_DOC_HOST');
        //$test=$this->getModel('Test');
        $data=' (Duplicate='.$duplicate.' or TestID='.$duplicate.') ';
        if($this->ifSubject && $this->mySubject){
            $data .= ' AND SubjectID in ('.$this->mySubject.') ';
        }
        $testIdList=$this->getModel('TestAttr')->selectData(
            'TestID',
            $data);//先找试题ID
        if(!empty($testIdList)){
            foreach($testIdList as $iTestIdList){
                $testID[]=$iTestIdList['TestID'];
            }
            $where='TestID in ('.implode(',',$testID).')';
            $testList=$this->getModel('Test')->selectData(
                $field,
                $where);
            if($testList){
                foreach($testList as $i=>$iTestList){
                    if($duplicate==$iTestList['TestID']){
                        $testList[$i]['CheckBox']=1;
                    }else{
                        $testList[$i]['CheckBox']=0;
                    }

                    $testList[$i]['Test']=R('Common/TestLayer/strFormat',array($testList[$i]['Test']));

                }
            }
        }
        $data=' (Duplicate='.$duplicate.' or TestID='.$duplicate.') ';
        if($this->ifSubject && $this->mySubject){
            $data .= ' AND SubjectID in ('.$this->mySubject.') ';
        }
        $testIdListR=$this->getModel('TestAttrReal')->selectData(
            'TestID',
            $data);//先找试题ID
        if(!empty($testIdListR)){
            foreach($testIdListR as $iTestIdListR){
                $testrID[]=$iTestIdListR['TestID'];
            }
            $id=implode(',',$testrID);
            $testListR=$this->getModel('TestReal')->selectData(
                $field,
                'TestID in ('.$id.')');
            if($testListR){
                foreach($testListR as $i=>$iTestListR){
                    if($duplicate==$iTestListR['TestID']){
                        $testListR[$i]['CheckBox']=1;
                    }else{
                        $testListR[$i]['CheckBox']=0;
                    }

                    $testListR[$i]['Test']=R('Common/TestLayer/strFormat',array($testListR[$i]['Test']));

                }
            }
        }
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('testList', $testList);
        $this->assign('testListR', $testListR);
        $this->assign('pageName', $pageName);
        $this->setBack($this->fetch('Test/showduplist'));
    }
    /**
     * 清除重复标记
     * @author demo
     * @date 2014年10月10日
     */
    public function removeMark(){
        $duplicate=$_POST['DuplicateL']; //获取数据标识
        $in=$_POST['in']; //获取数据标识
        $duplicateR=$_POST['DuplicateR'];//获取重复试题ID
        $duplicate=substr($duplicate,0,-1);             //去掉最后一个,
        $duplicateR=substr($duplicateR,0,-1);             //去掉最后一个,
        //判断数据标识
        if($duplicate=='' && $duplicateR==''){
            $this->setError("30301",1);
        }else{
            if($duplicate!=''){
                $bufferTest=$this->getModel('TestAttr')->updateData(
                    array('Duplicate'=>0),
                    'TestID in('.$duplicate.')');
            }
            if($duplicateR!=''){
                $bufferTestReal=$this->getModel('TestAttrReal')->updateData(
                    array('Duplicate'=>0),
                    'TestID in('.$duplicateR.')');
            }
            if($bufferTest===false){
                $this->setError("30306",1);
            }elseif($bufferTestReal===false){
                $this->setError("30306",1);
            }else{
                $this->setBack('<script>alert("清除重复成功！");</script>');
            }
        }
    }
    /**
     * 重复试题查看
     * @author demo
     * @date 2014年10月14日
     * */
    public function showDuplicate() {

        $testID = $_POST['id']; //获取数据标识
        $in = $_POST['in']; //获取数据标识
        //判断数据标识
        if (empty ($testID)) {
            $this->setError('30301',1);
        }
        $pageName = '编辑试题';
        $act = 'edit'; //模板标识
        if($this->ifSubject && $this->mySubject){
            $mark = 0;
            $subject = $this->getModel('TestAttrReal')->selectData(
                'SubjectID',
                'TestID='.$testID);
            if(in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                $mark = 1;
            }
            $subject = $this->getModel('TestAttr')->selectData(
                'SubjectID',
                'TestID='.$testID);
            if(in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                $mark = 1;
            }
            if(!$mark){
                $this->setError("30712",1);
            }
        }
        $field=array('testid','test','docid','docname');
        $order=array();
        $page=array();
        $result=array();
        $where['TestID']=$testID;
        $test = $this->getModel('TestReal');
        $show = $test->getTestIndex($field,$where,$order,$page);//获取重复试题题文
        if($show === false){
            $this->setError('30504',1);
        }
        if(empty($show[0])){
            $show=$this->getModel('Test')->selectData(
                'TestID,Test',
                'TestID='.$testID
            );
            //$host=C('WLN_DOC_HOST');
            $show[0][0]['testid']=$show[0]['TestID'];
            $show[0][0]['test']=R('Common/TestLayer/strFormat',array($show[0]['Test']));

        }
        // if(!$in){
        //     $show=$this->getModel('Test')->selectData(
        //         'TestID,Test',
        //         'TestID='.$testID);
        //     $host=C('WLN_DOC_HOST');
        //     $show[0][0]['testid']=$show[0]['TestID'];
        //     if($host)
        //     $show[0][0]['test']=R('Common/TestLayer/strFormat',array($show[0]['Test'],$host);
        //     else
        //     $show[0][0]['test']=$show[0]['Test'];
        // }else
        //     $show=$test->getTestIndex($field,$where,$order,$page);//获取重复试题题文
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('show', $show[0][0]);
        $this->assign('pageName', $pageName);
        $this->setBack($this->fetch('Test/showduplicate'));
    }
    /**
     * 提取试题关键字
     * @author demo
     */
    public function showWord(){
        $testID=$_POST['TestID'];

        //获取试题


    }
    /**
     * 获取默认章节
     * @author demo
     */
    public function getchapter(){
        $kl=$_GET['kl'];
        $id=$_GET['id'];
        //获取是否查询关键字
        $buffer=$this->getModel('TestAttr')->selectData(
            'SubjectID',
            'TestID in ('.$id.')'
        );
        $SubjectID=$buffer[0]['SubjectID'];
        $subjectArray=SS('subject');
        $chapterSet=$subjectArray[$SubjectID]['ChapterSet'];
        unset($buffer);
        unset($testAttr);
        unset($subjectArray);
        if(!$kl and $chapterSet!=1){
            $this->setError('1X2014',1);
        }
        $idlist=array();//存储章节id
        //根据关键字查询章节
        if($chapterSet){
            //获取试题解析
            $buffer=$this->getModel('Test')->selectData(
                'Analytic',
                'TestID in ('.$id.')');
            $analytic=$buffer[0]['Analytic'];
            if($analytic){
                $buffer=$this->getModel('ChapterKey')->selectData(
                    'ChapterID,Keyword',
                    'SubjectID='.$SubjectID);
                if($buffer){
                    foreach($buffer as $iBuffer){
                        if(strstr($analytic,$iBuffer['Keyword'])){
                            $idlist[]=$iBuffer['ChapterID'];
                        }
                    }
                }
            }
        }
        if($kl){
            $buffer=$this->getModel('ChapterKl')->selectData(
                '*',
                'KID in ('.$kl.')');
            if($buffer){
                foreach($buffer as $buffern){
                    $idlist[]=$buffern['CID'];
                }
            }
        }
        $idlist=array_unique($idlist); //排重
        $Chapter=$this->getModel('Chapter');
        $idlist=$Chapter->filterChapterID($idlist);
        $buffer=array();
        $chapterIdStr=implode(',',array_filter($idlist));

        if($idlist){
            $buffer=$this->getModel('Chapter')->selectData(
                '*',
                'ChapterID in ('.$chapterIdStr.')',
                'ChapterID asc');
            $bufferx=SS('chapterParentPath');
            foreach($buffer as $kk=>$buffern){
                $output='';
                if($bufferx[$buffern['ChapterID']]){
                    krsort($bufferx[$buffern['ChapterID']]);
                    foreach($bufferx[$buffern['ChapterID']] as $a){
                        $output.='>>'.$a['ChapterName'];
                    }
                        $output.='>>'.$buffern['ChapterName'];
                    $buffer[$kk]['ChapterName']=$output;
                }else{
                        $output='>>'.$buffern['ChapterName'];
                }
            }
        }else{
            $buffer='';
        }
        $this->setBack($buffer);
    }
    /**
     * ajax获取最大选项宽度；
     * @author demo
     */
    public function getOptionWidth(){
        $testID=$_POST['id'];
        $style=$_POST['style'];
        $ifintro=$_POST['ifintro'];
        if(empty($style)) $style=1;
        if(empty($ifintro)) $ifintro=1;
        $data = $this->getWidth($testID,$ifintro);
        if($style==1) $this->setBack($data);
        else exit($data);
    }
    /**
     * 获取最大选项宽度；
     * @param int $testID 试题id
     * @param int @ifIntro 是否入库 默认1
     * @return array
     * @author demo
     */
    public function getWidth($testID,$ifIntro=1){
        if($ifIntro==1){
            $test='Test';
        }else{
            $test='TestReal';
        }
        $testArr=$this->getModel($test)->selectData(
            'Test',
            'TestID='.$testID);
        if(empty($testArr)){
            $this->setError('30306',1);
        }
        $test=$this->getModel('Test');
        $arr=$test->getOptionWidth($testArr[0]['Test']);

        if(!$arr){
            $output=array(array(0,0));
        }else{
            $output=$arr;
        }
        return $output;
    }
}