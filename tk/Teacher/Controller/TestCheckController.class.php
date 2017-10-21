<?php
/**
 * 试题审核类，用于处理审核试题相关操作
 */
namespace Teacher\Controller;
class TestCheckController extends BaseController {
    var $moduleName = '试题审核管理'; //模块名称
    /**
     * 审核试题列表；get方式
     * @param int $did 文档id
     * @param int $wcid 审核任务id
     * @author demo
     */
    public function index() {
        $pageName = '试题审核管理';
        //参数
        $UserName=$this->getCookieUserName();
        $did=$_GET['did'];
        $wcid=$_GET['wcid'];
        if(!is_numeric($did) || !is_numeric($wcid)){
            $this->setError('30502');
        }
        $s=$_GET['s'];
        //文档验证码
        if($s!=md5($UserName.$did.$wcid.C('TEST_KEY'))){
            $this->setError('40119',NORMAL_ERROR);
        }
        
        //判断用户权限    
        $TeacherWorkCheck = $this->getModel('TeacherWorkCheck');
        $buffer=$TeacherWorkCheck->selectData(
            '*',
            ' WCID='.$wcid
        );
        unset($TeacherWorkCheck);
        if(!$buffer){
            $this->setError('40112',NORMAL_ERROR);
        }
        $CheckTimes=$buffer[0]['CheckTimes'];
        if($buffer[0]['UserName']!=$UserName){
            $this->setError('40113',NORMAL_ERROR);
        }
        $buffer=$this->getModel('TeacherWorkList')->selectData(
            '*',
            'DocID='.$did.' and WorkID='.$buffer[0]['WorkID']
        );
        if(!$buffer){
            $this->setError('40112',NORMAL_ERROR);
        }
        
        //获取试题数据
        $Test = $this->getModel('Test');
        $testBuffer=$Test->selectData(
            'TestID,Test',
            'DocID='.$did,
            'NumbID asc'
        );
        $testAttr = $this->getModel('TestAttr');
        $testAttrBuffer=$testAttr->selectData(
            'TestID,Duplicate',
            'DocID='.$did
        );
        $testAttrArray=array(); //以testid为键的数组
        foreach($testAttrBuffer as $iTestAttrBuffer){
            $testAttrArray[$iTestAttrBuffer['TestID']]=$iTestAttrBuffer;
        }

        if(!empty($testBuffer)) {
            //获取试题id
            $testList = array();
            foreach ($testBuffer as $iTestBuffer) {
                $testList[] = $iTestBuffer['TestID'];
            }
            //获取文档名称
            $Doc = $this->getModel('Doc');
            $docBuffer = $Doc->selectData(
                'DocName',
                'DocID=' . $did
            );
            unset($Doc);
            if (!$docBuffer)
                $this->setError('40118', NORMAL_ERROR);
            $docname = $docBuffer[0]['DocName'];
            unset($docBuffer);

            $attrBuffer = $this->getModel('TeacherWorkTestAttr')->unionSelect('teacherCheckContent','WCID=' . $wcid . ' and CheckTimes=' . $CheckTimes . ' and TestID in (' . implode(',', $testList) . ')');
            $tmpAttrBuffer = array();
            if ($attrBuffer) {
                foreach ($attrBuffer as $i => $iAttrBuffer) {
                    $tmpAttrBuffer[$iAttrBuffer['TestID'] . $iAttrBuffer['Style']] = $iAttrBuffer;
                }
            }

            $list = array();
            $list['DocName'] = $docname;
            $host = C('WLN_DOC_HOST');
            foreach ($testBuffer as $i => $iTestBuffer) {
                if ($host) $testBuffer[$i]['Test'] =  R('Common/TestLayer/strFormat',array($testBuffer[$i]['Test']));
                $testBuffer[$i]['savecode'] = md5($UserName . $iTestBuffer['TestID'] . C('TEST_KEY'));
                $testBuffer[$i]['IfTest'] = $tmpAttrBuffer[$iTestBuffer['TestID'] . 'test']['IfRight'];
                $testBuffer[$i]['WTID'] = $tmpAttrBuffer[$iTestBuffer['TestID'] . 'test']['WTID'];
                $testBuffer[$i]['Duplicate']=$testAttrArray[$iTestBuffer['TestID']]['Duplicate'];
                switch ($testBuffer[$i]['IfTest']) {
                    case '1':
                        $testBuffer[$i]['TestContent'] = $this->convertStr($tmpAttrBuffer[$iTestBuffer['TestID'] . 'test']['Content']);
                        if ($testBuffer[$i]['TestContent']) {
                            $testBuffer[$i]['TestContent'] = '<span class="red">【初审意见】：</span><br>' . $testBuffer[$i]['TestContent'];
                        }
                        if ($tmpAttrBuffer[$iTestBuffer['TestID'] . 'test']['Suggestion'] != '') {
                            $testBuffer[$i]['TestContent'] .= '<br><font color="red">【终审意见】：</font>' . $tmpAttrBuffer[$iTestBuffer['TestID'] . 'test']['Suggestion'];
                        }
                        break;
                    case '0':
                        $testBuffer[$i]['TestContent'] = '<font color="blue">通过</font>';
                        break;
                    default :
                        $testBuffer[$i]['TestContent'] = '<font color="red">未审核</font>';
                        break;
                }
                $testBuffer[$i]['IfKl'] = $tmpAttrBuffer[$iTestBuffer['TestID'] . 'knowledge']['IfRight'];
                switch ($testBuffer[$i]['IfKl']) {
                    case '2':
                    case '1':
                        $type = '<span class="red">缺少知识点：</span><br>';
                        if ($testBuffer[$i]['IfKl'] == 2) {
                            $type = '<span class="red">【初审意见】：</span><br>';
                        }
                        $testBuffer[$i]['KlContent'] = $type . $this->convertStr($tmpAttrBuffer[$iTestBuffer['TestID'] . 'knowledge']['Content']);
                        if ($tmpAttrBuffer[$iTestBuffer['TestID'] . 'knowledge']['Suggestion'] != '') {
                            $testBuffer[$i]['KlContent'] .= '<br><font color="red">【终审意见】：</font>' . $tmpAttrBuffer[$iTestBuffer['TestID'] . 'knowledge']['Suggestion'];
                        }
                        break;
                    case '0':
                        $testBuffer[$i]['KlContent'] = '<font color="blue">通过</font>';
                        break;
                    default :
                        $testBuffer[$i]['KlContent'] = '<font color="red">未审核</font>';
                        break;
                }
                $testBuffer[$i]['IfChapter'] = $tmpAttrBuffer[$iTestBuffer['TestID'] . 'chapter']['IfRight'];
                switch ($testBuffer[$i]['IfChapter']) {
                    case '1':
                        $testBuffer[$i]['ChapterContent'] = $this->convertStr($tmpAttrBuffer[$iTestBuffer['TestID'] . 'chapter']['Content']);
                        if ($testBuffer[$i]['ChapterContent']) {
                            $testBuffer[$i]['ChapterContent'] = '<span class="red">【初审意见】：</span><br>' . $testBuffer[$i]['ChapterContent'];
                        }
                        if ($tmpAttrBuffer[$iTestBuffer['TestID'] . 'chapter']['Suggestion'] != '') {
                            $testBuffer[$i]['ChapterContent'] .= '<br><font color="red">【终审意见】：</font>' . $tmpAttrBuffer[$iTestBuffer['TestID'] . 'chapter']['Suggestion'];
                        }
                        break;
                    case '0':
                        $testBuffer[$i]['ChapterContent'] = '<font color="blue">通过</font>';
                        break;
                    default :
                        $testBuffer[$i]['ChapterContent'] = '<font color="red">未审核</font>';
                        break;
                }
                $testBuffer[$i]['IfSpecial'] = $tmpAttrBuffer[$iTestBuffer['TestID'] . 'special']['IfRight'];
                switch ($testBuffer[$i]['IfSpecial']) {
                    case '1':
                        $testBuffer[$i]['SpecialContent'] = $this->convertStr($tmpAttrBuffer[$iTestBuffer['TestID'] . 'special']['Content']);
                        if ($testBuffer[$i]['SpecialContent']) {
                            $testBuffer[$i]['SpecialContent'] = '<span class="red">【初审意见】：</span><br>' . $testBuffer[$i]['SpecialContent'];
                        }
                        if ($tmpAttrBuffer[$iTestBuffer['TestID'] . 'special']['Suggestion'] != '') {
                            $testBuffer[$i]['SpecialContent'] .= '<br><font color="red">【终审意见】：</font>' . $tmpAttrBuffer[$iTestBuffer['TestID'] . 'special']['Suggestion'];
                        }
                        break;
                    case '0':
                        $testBuffer[$i]['SpecialContent'] = '<font color="blue">通过</font>';
                        break;
                    default :
                        $testBuffer[$i]['SpecialContent'] = '<font color="red">未审核</font>';
                        break;
                }
                $testBuffer[$i]['IfDiff'] = $tmpAttrBuffer[$iTestBuffer['TestID'] . 'diff']['IfRight'];
                switch ($testBuffer[$i]['IfDiff']) {
                    case '1':
                        $testBuffer[$i]['DiffContent'] = $tmpAttrBuffer[$iTestBuffer['TestID'] . 'diff']['Content'];
                        if ($testBuffer[$i]['DiffContent']) {
                            $testBuffer[$i]['DiffContent'] = '<span class="red">【初审意见】：</span><br>' . $testBuffer[$i]['DiffContent'];
                        }
                        if ($tmpAttrBuffer[$iTestBuffer['TestID'] . 'diff']['Suggestion'] != '') {
                            $testBuffer[$i]['DiffContent'] .= '<br><font color="red">【终审意见】：</font>' . $tmpAttrBuffer[$iTestBuffer['TestID'] . 'diff']['Suggestion'];
                        }
                        break;
                    case '0':
                        $testBuffer[$i]['DiffContent'] = '<font color="blue">通过</font>';
                        break;
                    default :
                        $testBuffer[$i]['DiffContent'] = '<font color="red">未审核</font>';
                        break;
                }
            }
        }
        $list['list']=$testBuffer;
        unset($testBuffer);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('did', $did); // 赋值数据集
        $this->assign('wcid', $wcid); // 赋值数据集
        $this->assign('docsavecode', $s); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 审核试题；get方式
     * @param int $id 试题id
     * @param int $w 审核试题情况id
     * @param int $wc 审核任务id
     * @author demo
     */
    public function audit() {
        $TestID = $_GET['id']; //获取数据标识 试题id
        $WCID = $_GET['wcid']; //获取数据标识 审核任务id
        $docid = $_GET['did'];
        //判断数据标识
        if(!is_numeric($WCID) || !is_numeric($docid)){
            $this->setError('30502');
        }
        $tids = array();

        $doc = $this->getModel('Doc')->findData(
            'DocID,DocName',
            'DocID='.$docid
        );
        $Test = $this->getModel('Test');
        $testAttr = $this->getModel('TestAttr');
        $tids = $testAttr->selectData(
            'TestID,Duplicate',
            'DocID='.$docid,
            'TestID asc'
        );//获取试题编号集
        if(empty($TestID)){
            if(empty($tids)){
                $this->setError('40703',NORMAL_ERROR);
            }
            $TestID = $tids[0]['TestID'];
        }
        //获取数据标识 审核试题情况id
        $WTID = $this->getModel('TeacherWorkTest')->findData(
            'WTID',
            'WCID='.$WCID.' AND TestID='.$TestID,
            'CheckTimes desc'
        );
        $WTID = $WTID['WTID'];
        
        //$edit = $Test->field('a.*,t.SubjectID,t.TypesID,t.SpecialID,t.DfStyle,t.Diff,t.Mark')->table('zj_test a')->join('zj_test_attr t ON a.TestID=t.TestID')->where('a.TestID=' . $TestID)->limit(1)->select();
        $edit = $Test->unionSelect('testSelectByTestId',$TestID);

        $nextid = $this->getNextId($tids,$TestID); //下一个试题id
        //如果当前是重题则无需标引
        if($edit[0]['Duplicate']!=0){
            if($nextid==-1){
                $this->setError('40605',NORMAL_ERROR,U("Teacher/TestCheck/audit?did=".$docid."&wcid=".$WCID));
            }else{
                header('Location:'.U("Teacher/TestCheck/audit?did=".$docid."&wcid=".$WCID."&id=".$nextid));
            }
        }

        //获取题型名称
        $typesArray=SS('types');
        $edit[0]['TypesName']=$typesArray[$edit[0]['TypesID']]['TypesName'];
        unset($typesArray);
        //获取试题知识点
        $buffer = $this->getModel('TestKl')->selectData(
            '*',
            'TestID='.$TestID
        );
        $edit[0]['KlID']=0;
        if($buffer){
            $arr_temp=array();
            foreach($buffer as $buffern){
                $arr_temp[]=$buffern['KlID'];
            }
            $edit[0]['KlID']=implode(',',$arr_temp);
        }
        //获取试题章节
        $buffer = $this->getModel('TestChapter')->selectData(
            '*',
            'TestID='.$TestID
        );
        $edit[0]['ChapterID']=0;
        if($buffer){
            $arr_temp=array();
            foreach($buffer as $buffern){
                $arr_temp[]=$buffern['ChapterID'];
            }
            sort($arr_temp);
            $edit[0]['ChapterID']=implode(',',$arr_temp);
        }
        //获取试题专题
        if($edit[0]['SpecialID']){
             $special=SS('special');
             $edit[0]['SpecialName']=$special[$special[$edit[0]['SpecialID']]['PID']]['SpecialName'].' >> '.$special[$edit[0]['SpecialID']]['SpecialName'];
             unset($special);
        }else{
            $edit[0]['SpecialName']='空';
        }
       
        //获取试题难度打分
        if(strstr($edit[0]['Mark'],'@')){
            $arr=explode('@',$edit[0]['Mark']);
            foreach($arr as $ii=>$arrn){
                $edit[0]['Markx'][$ii+1]=array_filter(explode('#',$arrn));
            }
        }else{
            $edit[0]['Markx'][1]=array_filter(explode('#',$edit[0]['Mark']));
        }

        //自定义打分
        $markArray=$this->getModel('TestMark')->selectData(
            '*',
            'SubjectID = '.$edit[0]['SubjectID'],
            'Style asc,OrderID asc'
        );
        if($markArray){
            foreach($markArray as $ii=>$iMarkArray){
                $markArray[$ii]['MarkListx']=formatString('str2Arr',$markArray[$ii]['MarkList']);
                foreach($markArray[$ii]['MarkListx'] as $jj=>$marklistn){
                    $markArray[$ii]['MarkListx'][$jj][3]=$markArray[$ii]['MarkID'].'|'.$markArray[$ii]['MarkListx'][$jj][0];
                    $markArray[$ii]['MarkListx'][$jj][4]=$markArray[$ii]['MarkID'];
                }
            }
        }
        
        //自定义打分次数
        $times=$Test->xtnum($edit[0]['Test'],1);
        if(!$times) $times=1;
        
        //试题路径
        $host=C('WLN_DOC_HOST');
        if($host){
            $edit[0]['Test']= R('Common/TestLayer/strFormat',array($edit[0]['Test']));
            $edit[0]['Answer']= R('Common/TestLayer/strFormat',array($edit[0]['Answer']));
            $edit[0]['Analytic']= R('Common/TestLayer/strFormat',array($edit[0]['Analytic']));
            $edit[0]['Remark']= R('Common/TestLayer/strFormat',array($edit[0]['Remark']));
        }
        $edit[0]['code']=$s; //验证码 //变量未定义
        $edit[0]['WTID']=$WTID; //worktestid
        
        //试题审核数据 如果试题审核数据id存在则查询  如果不存在则提取最近一次的审核信息
        $content=array();
        $tmpArray=array();
        $TeacherWorkTestAttr=$this->getModel('TeacherWorkTestAttr');
        if($WTID){
            $tmpArray=$TeacherWorkTestAttr->selectData(
                '*',
                'WTID='.$WTID);
        }else{
            $tmpArray=$TeacherWorkTestAttr->unionSelect('teacherCheckContent',
                'WCID='.$WCID.' and TestID='.$TestID,
                'CheckTimes desc');
        }
        $testError = '';
        if($tmpArray){
            foreach($tmpArray as $i=>$iTmpArray){
                $content['if'.$iTmpArray['Style']]=$iTmpArray['IfRight'];
                $content[$iTmpArray['Style']]=stripslashes($iTmpArray['Content']);
                $subject = '';
                switch($iTmpArray['Style']){
                    case 'knowledge':{
                        $subject = '知识点';
                    }
                    break;
                    case 'test':{
                        $subject = '题文';
                    }
                    break;
                    case 'special':{
                        $subject = '专题';
                    }
                    break;
                    case 'diff':{
                        $subject = '难度值';
                    }
                    break;
                    case 'chapter':{
                        $subject = '章节';
                    }
                    break;
                }
                $testError .= $this->getErrorHtml($subject,$iTmpArray);
            }
        }

        /*载入模板标签*/
        $username = $this->getCookieUserName();
        $this->assign('errorInfo',$testError);
        $this->assign('docsavecode',md5($username.$docid.$WCID.C('TEST_KEY')));
        $this->assign('doc',$doc);
        $this->assign('difficulties',C('WLN_TEST_DIFF'));
        $this->assign('difficulty',R('Common/TestLayer/diff2Str',array($edit[0]['Diff'])));
        $this->assign('code', md5($username.$TestID.C('TEST_KEY')));
        $this->assign('edit', $edit[0]);
        $this->assign('content', $content);
        $this->assign('times', $times);
        $this->assign('tid', $TestID);
        $this->assign('did', $docid); // 赋值数据集
        $this->assign('tids', $tids);
        $this->assign('wid', $WCID);
        $this->assign('mark_array', $markArray);
        $this->assign('pageName', $pageName);//变量未定义
        $this->display();
    }
    
    /**
     * 保存审核试题；post方式
     * @param int $TestID 试题id
     * @param int $WTID 审核试题情况id
     * @param int $WCID 审核任务id
     * @author demo
     */
    public function save() {
        $TestID = $_POST['TestID']; //试题id
        $WTID = $_POST['WTID']; //审核试题情况id //获得的数据未使用
        $WCID = $_POST['WCID']; //审核任务id
        //判断数据标识
        if(!is_numeric($WCID) || !is_numeric($TestID)){
            $this->setError('30502');
        }
        //验证数据合法性
        $s=$_POST['s'];
        $UserName=$this->getCookieUserName();
        if($s!=md5($UserName.$TestID.C('TEST_KEY'))){
            $this->setError('40119',AJAX_ERROR);
        }
        $TestAttr=$this->getModel('TestAttr');
        $buffer=$TestAttr->selectData(
            'DocID',
            'TestID='.$TestID
        );
        $DocID=$buffer[0]['DocID'];
        if(empty($DocID)) 
            $this->setError('40701',AJAX_ERROR);
        unset($buffer);
        unset($TestAttr);
        $TeacherWorkCheck=$this->getModel('TeacherWorkCheck');
        $buffer=$TeacherWorkCheck->selectData(
            'WorkID,CheckTimes',
            'WCID='.$WCID
        );
        $WorkID=$buffer[0]['WorkID'];
        $checkTimes=$buffer[0]['CheckTimes'];
        if(empty($DocID)) 
            $this->setError('40112',AJAX_ERROR);
        unset($buffer);
        unset($TeacherWorkCheck);
        $buffer=$this->getModel('TeacherWorkList')->selectData(
            'WorkID',
            'WorkID='.$WorkID.' and DocID='.$DocID
        );
        if(!$buffer) 
            $this->setError('40702',AJAX_ERROR);

        //判断正确的WTID 查询$WCID、$TestID、$CheckTimes对应的WTID
        $buffer=$this->getModel('TeacherWorkTestAttr')->unionSelect('teacherSingleInfo',$WCID,$TestID,$checkTimes);
        $attrArray=array(); //存储试题审核属性

        if(!$buffer){
            $WTID='';
        }else{
            $WTID=$buffer[0]['WTID'];
            foreach($buffer as $i=>$iBuffer){
                $attrArray[$iBuffer['Style']]['NowRight']=$iBuffer['NowRight'];
                $attrArray[$iBuffer['Style']]['RightNum']=$iBuffer['RightNum'];
                $attrArray[$iBuffer['Style']]['AttrID']=$iBuffer['AttrID'];
                $attrArray[$iBuffer['Style']]['LoseNum']=$iBuffer['LoseNum'];
                $attrArray[$iBuffer['Style']]['IfRight']=$iBuffer['IfRight'];
            }
        }

        //判断审核数据是否存在//查看是否有修改数据
        $act='add';
        if($WTID) {
            $act='edit';
        }
        //接收数据
        $IfTest=$_POST['IfTest'];
        $TestContent=$_POST['Test'];
        $IfKl=$_POST['IfKl'];
        $KlContent=$_POST['Kl'];
        $IfChapter=$_POST['IfChapter'];
        $ChapterContent=$_POST['Chapter'];
        $IfDiff=$_POST['IfDiff'];
        $DiffContent=$_POST['Diff'];
        $IfSpecial=$_POST['IfSpecial'];
        $SpecialContent=$_POST['Special'];

        $data=array();
        $data[1]['Style']='test';
        $data[1]['IfRight']=$IfTest;
        $data[2]['Style']='knowledge';
        $data[2]['IfRight']=$IfKl;
        $data[3]['Style']='chapter';
        $data[3]['IfRight']=$IfChapter;
        $data[4]['Style']='diff';
        $data[4]['IfRight']=$IfDiff;
        $data[5]['Style']='special';
        $data[5]['IfRight']=$IfSpecial;

        if($attrArray){
            $data[1]['AttrID']=$attrArray[$data[1]['Style']]['AttrID'];
            $data[2]['AttrID']=$attrArray[$data[2]['Style']]['AttrID'];
            $data[3]['AttrID']=$attrArray[$data[3]['Style']]['AttrID'];
            $data[4]['AttrID']=$attrArray[$data[4]['Style']]['AttrID'];
            $data[5]['AttrID']=$attrArray[$data[5]['Style']]['AttrID'];

            $data = $this->process($IfTest,$attrArray,$data,$TestContent,1);
            $data = $this->process($IfKl,$attrArray,$data,$KlContent,2);
            $data = $this->process($IfChapter,$attrArray,$data,$ChapterContent,3);
            $data = $this->process($IfDiff,$attrArray,$data,$DiffContent,4);
            $data = $this->process($IfSpecial,$attrArray,$data,$SpecialContent,5);
        }else{
            //$data[5]['NowRight']=1;
            if($IfTest){
                $data[1]['NowRight']=1;
                switch ($IfTest) {
                    case 2:
                        $data[1]['RightNum']=1; 
                        break;
                    default:
                        $data[1]['LoseNum']=1; 
                        break;
                }
                $data[1]['Content']=$TestContent;
            }
            if($IfKl){
                $data[2]['NowRight']=1;
                switch ($IfKl) {
                    case 2:
                        $data[2]['RightNum']=1; 
                        break;
                    default:
                        $data[2]['LoseNum']=1; 
                        break;
                }
                $data[2]['Content']=$KlContent;
            }
            if($IfChapter){
                $data[3]['NowRight']=1;
                switch ($IfChapter) {
                    case 2:
                        $data[3]['RightNum']=1; 
                        break;
                    default:
                        $data[3]['LoseNum']=1; 
                        break;
                }
                $data[3]['Content']=$ChapterContent;
            }
            if($IfDiff){
                $data[4]['NowRight']=1;
                switch ($IfDiff) {
                    case 2:
                        $data[4]['RightNum']=1;
                        break;
                    default:
                        $data[4]['LoseNum']=1; 
                        break;
                }
                $data[4]['Content']=$DiffContent;
            }
            if($IfSpecial){
                $data[5]['NowRight']=1;
                switch ($IfSpecial) {
                    case 2:
                        $data[5]['RightNum']=1; 
                        break;
                    default:
                        $data[5]['LoseNum']=1; 
                        break;
                }
                $data[5]['Content']=$SpecialContent;
            }
        }

        //更新数据
        if($act=='add'){
            $data[0]['TestID']=$TestID;
            $data[0]['WCID']=$WCID;
            $data[0]['CheckTimes']=$checkTimes;
            if(($WTID=$this->getModel('TeacherWorkTest')->insertData($data[0]))===false){
                $this->setError('40704',AJAX_ERROR);
            }else{
                //添加审核属性
                for($i=1;$i<6;$i++){
                    if(!isset($data[$i]['Content'])) $data[$i]['Content']='';
                    $data[$i]['WTID']=$WTID;
                    $this->getModel('TeacherWorkTestAttr')->insertData($data[$i]);
                }
                //写入日志
                $this->teacherLog($this->moduleName, '添加试题审核WTID为【' . $WTID . '】的数据');
            }
        }else if($act=='edit'){
            for($i=1;$i<6;$i++){
                $this->getModel('TeacherWorkTestAttr')->updateData(
                    $data[$i],
                    'AttrID='.$data[$i]['AttrID']
                );
            }
            //写入日志
            $this->teacherLog($this->moduleName, '修改试题审核WTID为【' . $WTID . '】的数据');
        }
        $this->setBack('保存成功！');
    }

    /**
     * 处理审核结果
     * author 
     */
    private function process($item,$attrArray,$data,$content,$index){
        $isRedo = $attrArray[$data[$index]['Style']]['NowRight'] == 0; //是否为重做
        if($attrArray[$data[$index]['Style']]['IfRight'] != $item || $isRedo){
            if(1 == $item){
                $data[$index]['LoseNum']=$attrArray[$data[$index]['Style']]['LoseNum']+1;
                if(!$isRedo && $attrArray[$data[$index]['Style']]['IfRight'] == 2)
                    $data[$index]['RightNum']=$attrArray[$data[$index]['Style']]['RightNum']-1;
            }else if(2 == $item){
                $data[$index]['RightNum']=$attrArray[$data[$index]['Style']]['RightNum']+1;
                if(!$isRedo && $attrArray[$data[$index]['Style']]['IfRight'] == 1)
                    $data[$index]['LoseNum']=$attrArray[$data[$index]['Style']]['LoseNum']-1;
            }else{
                if(!$isRedo){
                    if($attrArray[$data[$index]['Style']]['IfRight'] == 1){
                        $data[$index]['LoseNum']=$attrArray[$data[$index]['Style']]['LoseNum']-1; 
                   }else if($attrArray[$data[$index]['Style']]['IfRight'] == 2){
                        $data[$index]['RightNum']=$attrArray[$data[$index]['Style']]['RightNum']-1;
                   }
                }
            }
        }
        $data[$index]['Content'] = $content;
        $data[$index]['NowRight']=1;
        return $data;
    }

    private function convertStr($str){
        if(empty($str))
            return '';//变量未定义
        $result = '';
        $str = explode("\n", $str);
        $index = 1;
        foreach($str as $val){
            if(!empty($val))
               $result .= '('.($index++).')：'.$val.'<br>';
        }
        if($result === ''){
            return $str;
        }
        return $result;
    }

    /**
     * 错误信息
     */
    private function getErrorHtml($subject,$data){
        $error = '';
        if($data['Content'] != ''){
            $error = '【'.$subject.'初审意见】：<font color="red">'.$data['Content'].'</font><br>';
        }
        if($data['Suggestion'] != ''){
            $error .= '【'.$subject.'终审意见】：<font color="red">'.$data['Suggestion'].'</font><br>';
        }
        if($error != '')
            return $error.'<hr style="color:#ffc;">';
        return $error;
    }
}