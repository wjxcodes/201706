<?php
/**
 * @author demo、、、
 * @date 2014年11月3日
 */
/**
 * 首页相关控制器类，用于处理首页操作
 */
namespace Home\Controller;
header('Content-Type:text/html;charset=utf-8');
class IndexController extends BaseController {
    /**
     * 首页
     */
    public function index() {
        $u='';
        if(isset($_REQUEST['u'])) $u=str_replace('_','/',$_REQUEST['u']);
        $this->assign('useAction',$u);
        $this->display();
    }

    /**
     * 检测数据库是否正常
     */
    public function monitor() {
        if($this->getApiCommon('Counter/checkDBConnect')===true){
            exit('success');
        }
    }
    /**
     * 框架页
     */
    public function main() {
//        $str='<b>Four benefits of writing by hand</b></p>
//<p>Today is National Handwriting Day! Although we don’t write like we used to, here are four ways handwriting is still helpful.</p>
//<p><b>It’s better for learning.</b></p>
//<p><u>&nbsp; </u><u>【题号】&nbsp; </u>&nbsp;That’s because putting ink to paper stimulates (刺激) the brain.&nbsp;&nbsp;One study from 2010 found that the brain areas related to learning “lit up” much more when kids were asked to write words like “spaceship” by hand versus just studying the word closely.</p>
//<p><u>&nbsp;&nbsp; </u><u>【题号】</u></p>
//<p>Many famous authors prefer writing by hand to the use of a typewriter or computer. Writer Susan Sontag&nbsp;once said that she penned her first drafts (草稿) before typing them up for editing later. She said, “<u>&nbsp; 【题号】</u>”&nbsp;A 2009 study seems to support Sontag’s preference for writing by hand: Elementary school students who wrote essays with a pen not only wrote more than their keyboard-tapping friends, but they also wrote faster and in more complete sentences.</p>
//<p><b>It will prevent you from being distracted (分心).</b></p>
//<p>The computer in front of you is really a distraction. <u>&nbsp;&nbsp;</u><u>【题号】</u>In 2012, scientists even suggested that taking five-minute breaks to browse Tumblr or BuzzFeed could make you a more productive worker. However, when it’s time to work on that essay, have only a pen and paper in front of you.</p>
//<p><b>It keeps your brain sharp as you get older.</b></p>
//<p><u>&nbsp; </u><u>【题号】</u>According to&nbsp;<i>The&nbsp;Wall</i>
//<i>Street</i>
//<i>Journal</i>,&nbsp;some physicians say&nbsp;that the act of writing is good exercise for those who want to keep their minds sharp as they age.</p>
//<p>A. It makes you a better writer.</p>
//<p>B. Writing is good for your brain.</p>
//<p>C. Of course, the Internet isn’t all bad.</p>
//<p>D. I like the slowness of writing by hand.</p>
//<p>E. Try writing by hand at least 20 minutes each day.</p>
//<p>F. Many writers have a preference for writing by hand.</p>
//<p>G. One of the most effective ways to study is to rewrite your notes by hand.</p>
//';
//        exit(R('Common/TestLayer/strFormat',array($this->getModel('Test')->formatTest($str,1,$width,0,1,$iRes['attrs']['optionwidth'],$iRes['attrs']['optionnum'],5,$iRes['attrs']['ifchoose'],1))));
//        $tmp=$this->getModel('ApiCache')->paperContentCache(array('savecode'=>'413147b8f'));
//        exit(print_r($tmp));
        $pageName = "在线组卷";

        //获取学科数据集
        $output = $this->getApiCommon('Subject/getSubjectFullList');
		// dump($output);die;
        $subject = json_encode($output, JSON_UNESCAPED_UNICODE);

        //获取题型数据集
        $output=$this->getApiCommon('Types/getTypeFullList');
        $types = json_encode($output, JSON_UNESCAPED_UNICODE);

        //获取难度数据集
        $output=$this->getApiCommon('Diff/getDiffStringList');
        $diff = json_encode($output, JSON_UNESCAPED_UNICODE);

        $userInfoArray=$this->getApiUser('User/getUserInfoForHome',$this->getCookieUserName());
        $userBuffer = json_encode($userInfoArray, JSON_UNESCAPED_UNICODE);
		dump($userBuffer);
		// dump($this->getCookieUserID());die;
        $ifSetInfo='';//是否完善信息
        $ifGoActivity='';//是否参与送书活动
        $mustField = $this->getApiUser('User/mustField',$this->getCookieUserID());
        if(count($mustField) > 0){//需要补充字段
            //如果仅仅需要补充的是用户昵称字段，
            //则只显示昵称补充的信息，
            //并且不参与补全信息送书活动
            if(count($mustField)==1 && $mustField[0]=='Nickname'){
                $ifSetInfo = '2';//表示只有昵称需要补充
            }else {
                $ifSetInfo = '1';
                //是否符合参与送书活动(2015年9月1日之前注册的用户)
                if ($userInfoArray['LoadDate'] < strtotime('2015-9-1')) {
                    $ifGoActivity = '1';
                }
            }
        }

        //在进入首页时，自动加载完善信息的基础数据
        $gradeArray='';
        $areaArray='';
        if($ifSetInfo==1){
            $areaArray=$this->getApiCommon('Area/areaCache',array('pID'=>0));
            $gradeArray =$this->getApiCommon('Grade/gradeListSubject');
        }
        $this->assign('gradeList',json_encode($gradeArray)); //年级内容
        $this->assign('arrArea',json_encode($areaArray));

        //如果有指定跳转则跳转，没有的化默认Index-Main则通过cookie判断是否跳转其他地方
        if(isset($_REQUEST['u'])){
            $uArr=explode('_',$_REQUEST['u']);
            $uArr=array_filter($uArr);
            if(count($uArr)>3){
                $u='';
                for($i=0;$i<count($uArr);$i++){
                    if($i<3){
                        $u.='/'.$uArr[$i];
                    }elseif($i==3){
                        $u.='?'.$uArr[$i];
                    }elseif($i%2==0){
                        $u.='='.$uArr[$i];
                    }else{
                        $u.='&'.$uArr[$i];
                    }
                }
            }else{
                $u=implode('/',$uArr);
            }

            $this->assign('useAction',$u);
        }else if(cookie('user_ORIGINALITY')){
            $this->assign('useAction', 'CustomTestStore/customNav');
        }

        $this->assign('subject', $subject); //学科
        $this->assign('types', $types); //题型
        $this->assign('ifSetInfo', $ifSetInfo); //是否完善信息
        $this->assign('ifGoActivity', $ifGoActivity); //是否参与活动
        $this->assign('diff', $diff); //难度
        $this->assign('user', $userBuffer); //用户信息
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 组卷页
     */
    public function zuJuan() {
        $pageName = "在线组卷";
        $subjectID = cookie('SubjectId');
        if (!$subjectID)
            $this->setError('30730',1);

        $buffer = SS('typesSubject');
        $types = json_encode($buffer, JSON_UNESCAPED_UNICODE);
        $userName=$this->getCookieUserName();
        $key=md5(C('DOC_HOST_KEY').$userName.date("Y.m.d",time()));
        $this->getModel('User')->conAddData(
            'ComTimes=ComTimes+1',
            'UserName="'.$userName.'"'
        );
        $data = array(
            'UserID' => $this->getCookieUserID(),
            'Type' => 1,
            'AddTime' => time()
        );
        $this->getModel('TestpaperCenterLog')->insertData( $data);
        //获取当前用户所在学校
        $userID=$this->getCookieUserID();
        $userSchool=$this->getModel('User')->getUserSchool($userID);
        $school="XXX学校";
        if(!empty($userSchool)){
            $school=$userSchool['SchoolName'];
        }
        $this->assign('key', $key); //页面标题
        $this->assign('Types', $types); //页面标题
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('school', $school); //用户当前学校
        $this->display();
    }
    /**
     * 组卷下载
     */
    public function zjDown(){
        $banBen=$_GET['b'];
        if(empty($banBen)) $banBen=0;
        $zhiZhang=$_GET['z'];
        if(empty($zhiZhang)) $zhiZhang=0;
        $leiXing=$_GET['l'];
        if(empty($leiXing)) $leiXing=0;
        $jiLu=$_GET['j'];
        if(empty($jiLu)) $jiLu=0;
        $tikaheyi=$_GET['t'];
        if(empty($tikaheyi)) $tikaheyi=0;
        $yanZhengMa=$_GET['y'];
        if(empty($yanZhengMa)) $yanZhengMa=0;
        $this->assign('banben', $banBen); //版本
        $this->assign('zhizhang', $zhiZhang); //纸张
        $this->assign('leixing', $leiXing); //类型
        $this->assign('jilu', $jiLu); //记录
        $this->assign('yanzhengma', $yanZhengMa); //验证码
        $this->assign('tikaheyi', $tikaheyi); //题卡合一
        $content = $this->fetch();
        $this->setBack($content);
    }

    /**
     * 组卷设置页
     * @notice 未发现该方法被使用
     */
    public function zjSet() {
        $content = $this->fetch();
        $this->setBack($content);
    }

    /**
     * 公告列表页
     */
    public function newsList() {
        $pageName = "公告列表页";
        $pageSize=10;
        $where='Status=0 and Types in ("通用","组卷")';
        $news = $this->getModel('News');
        $count=$news->selectCount(
            $where,
            '*'
        );
        $page=page($count,$_GET['page'],$pageSize);
        $buffer=$news->selectData(
            '*',
            $where,
            'NewID DESC',
            (($page-1)*$pageSize).','.$pageSize
        );
        $this->assign('count', $count); //总数
        $this->assign('pagesize', $pageSize); //每页数量
        $this->assign('page', $page); //当前页
        $this->assign('buffer', $buffer); //公告数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 公告内容页
     */
    public function newsCon() {
        $pageName = "公告内容页";
        $id=$_GET['id'];
        if($id<1 || !is_numeric($id)){
            $this->setError('20102');
            exit();
        }
        $buffer = $this->getModel('News')->selectData(
            '*',
            'Status=0 and Types in ("通用","组卷") and NewID="'.$id.'"'
        );
        if(!$buffer){
            $this->setError('20103');
            exit();
        }

        $buffer[0]['NewContent']=formatString('IPReturn',$buffer[0]['NewContent']);

        $buffer[0]['Hits']+=1;
        $this->assign('NewID',$id);
        $this->assign('buffer', $buffer[0]); //公共数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 新闻浏览次数
     * @author demo
     */
    public function newHits(){
        $newID = $_POST['NewID'];
        if(empty($newID)){
            $this->setError('30301');
            exit();
        }
        $this->getModel('News')->conAddData(
            'Hits=Hits+1',
            'NewID="'.$newID.'"'
        );
    }

    /**
     * 系统登陆后首页
     */
    public function content() {
        $pageName = "公告信息";
        //获取公告
        $newsArr = $this->getModel('News')->selectData(
            '*',
            'Status=0 and Types in ("通用","组卷")',
            'NewID DESC',
            6
        );
        foreach($newsArr as $i=>$iNewsArr){
            $newsArr[$i]['NewTitle']=string('msubstr',$iNewsArr['NewTitle'],0,18);
        }
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('IP', get_client_ip(0,true)); //ip
        $this->assign('news', $newsArr); //公告
        $this->display('Index/content');
    }

    /**
     * 生成试卷
     */
    public function create() {
        $testList=$_POST['testList']; //试题id
        //试题分类
        //$classify = ltrim(strrchr($testList, '|'), '|');
        $testList = preg_replace('/\|\d{1}/m', '', $testList);
        $tmpArr=explode(',',$testList);
        if(count($tmpArr)>70){
            $this->setError('20104',1);
        }
        $subjectID=$_POST['SubjectID'];
        $cookieStr=$_POST['cookiestr']; //cookiestr
        $isSaveRecord=$_POST['issaverecord']; //是否记录
        $docVersion=$_POST['docversion']; //文档类型
        $paperSize=$_POST['papersize']; //试卷版式
        $paperType=$_POST['papertype']; //试卷类型
        $verifyCode=$_POST['verifycode']; //验证码
        $ifShare=$_POST['ifShare'];//是否分享
        $key=$_POST['key']; //密钥
        if(md5($verifyCode) != session('verify')){
            $this->setError('30101',1);
        }

        if (!$testList) {
            $this->setError('20220',1);
        }
        //判断是否有权限下载
        $userName=$this->getCookieUserName();
        if($key!=md5(C('DOC_HOST_KEY').$userName.date("Y.m.d",time()))){
            $this->setError('30803',1);
        }

        $param=array();
        $param['subjectID']=$subjectID; //学科id
        $param['cookieStr']=$cookieStr; //内容
        $param['isSaveRecord']=$isSaveRecord; //是否存档
        $param['ifShare']=$ifShare; //是否分享
        $param['docVersion']=$docVersion; //文档类型
        $param['paperSize']=$paperSize; //纸张大小
        $param['paperType']=$paperType; //试卷类型
        $param['backType']=0; //是否仅返回路径
        $param['testList']=$testList; //试题id字符串以英文逗号间隔
        $param['downStyle']=1; // 区分下载作业还是试卷 答题卡4 导学案3 作业2，试卷1
        $userName=$this->getCookieUserName();

        $path=$this->getApiDoc('Doc/getDownUrlByCookie',$param,$userName);

        if($path[0]=='false'){
            $this->setError($path['msg'],1,'',$path['replace']);
        }else{
            $buffer = $this->getModel('MissionHallRecords')->finishTask(
                $this->getCookieUserID()
            );
            $this->setBack('success#$#'.$path['msg']);
        }
    }

    /**
     * 配置答题卡
     * @author demo
     */
    public function arswer(){
        $this->display();
    }

    /**
     * 生成默认答题卡
     * @author demo
     */
    public function arswerDown(){
        $sheetType = $_POST['sheettype'];
        $sheetXml = $_POST['sheetxml'];
        $docVersion = $_POST['docversion'];
        $sheetChoice = $_POST['sheetinput'];
        $style = $_POST['style']; //ab卷 默认0或a不用处理


        if(empty($sheetXml)){
            $this->setBack("<script>
                $('#dtktishi').html('<b style=\"color:red;align:center;\">答题卡数据不存在，请重试！</b>');
                </script>");
        }

        $doc=$this->getModel('Doc');
        switch($sheetType){
            case 0:
                $result=$doc->arswerWord($sheetXml,$sheetChoice,$docVersion);
                break;
            case 1:
                $result=$doc->arswerPdf($sheetXml,$sheetChoice,$docVersion,$style);
                break;
        }
        if(empty($result[0])){
            if(strstr($result[1],'<script>')==false) $result[1]="<script>
                $('#dtktishi').html('<b style=\"color:red;align:center;\">".$result[1]."</b>');
                </script>";
            $this->setBack($result[1]);
        }

        $urlPath=$result[1];
        $mainTitle=$result[2];
        $sheetArray=$result[3];
        $userName=$this->getCookieUserName();
        $subjectID=cookie('SubjectId');
        $result=$doc->saveArswerResult($userName,$urlPath,$mainTitle,$subjectID,serialize($sheetArray));
        $this->setBack($result[1]);
    }

    /**
     * 生成自定义答题卡
     * @param string $sheetxml 答题卡对应json字符串
     * @author demo
     */
    public function arswerMyDown(){
        $sheetXml=json_decode(stripslashes($_POST['sheetxml']),true);
        if(empty($sheetXml)){
            $this->setBack("<script>
                $('#dtktishi').html('<b style=\"color:red;align:center;\">答题卡数据不存在，请重试！</b>');
                </script>");
        }

        $doc=$this->getModel('Doc');
        $result=$doc->arswerMyPdf($sheetXml,'pdf');
        if(empty($result[0])){
            $this->setBack($result[1]);
        }

        $urlPath=$result[1];
        $mainTitle=$result[2];
        $userName=$this->getCookieUserName();
        $subjectID=cookie('SubjectId');
        $result=$doc->saveArswerResult($userName,$urlPath,$mainTitle,$subjectID);
        $this->setBack($result);
    }

    /**
     * 获取题文中的图像
     * @param int $testID 试题id
     * @return [0=>'http://192.168.4.99:99/a.jpg',
     *          1=>'http://192.168.4.99:99/a.jpg'
     * ]
     * @author demo
     */
    public function testImageChoose(){
        $testID=$_GET['testID'];
        if(!is_numeric($testID)) $this->setError ('30301',1);

        //获取试题对应题文
        $field = array('testid','testold');
        $where = array('TestID'=>$testID);
        $order = array();
        $page = array('perpage'=>1);

        //获取合并后的索引数据
        $buffer = $this->getModel('TestRealQuery')->getIndexTest($field,$where,$order,$page,0,2);

        $test=$buffer[$testID]['testold'];

        //获取试题中的图片并返回
        $host=C('WLN_DOC_HOST');
        $imgArr=array();
        preg_match_all('/<img[\s]*(style=[\"|\'][^\"\']*[\"|\'])?[\s]*src=[\'|\"]([^\"\']*)[\"|\']/is',$test,$arr);
        foreach($arr[2] as $arrn){
            if(!strstr($arrn,'http')) $arrn=$host.$arrn;
            $imgArr[]=$arrn;
        }
        $this->setBack($imgArr);
    }

    /**
     * 分析试题
     */
    public function analytic(){
        $data=$_POST['data'];
        $tmpStr=explode('@#@',$data);
        $testList=array();//试题id
        $testNum=array();//试题小题数
        $testScore=array();//试题分值
        $testIfChoose=array();//试题是否选做
        $testChooseNum=array();//试题选做个数
        $totalNum=0;//试题总题数
        //获取试题列表
        foreach($tmpStr as $i=>$iTmpStr){
            $tmpStr1=explode('@$@',$iTmpStr);
            if(count($tmpStr1)==7 && !empty($tmpStr1[5])){
                $tmpStr2=explode(';',$tmpStr1[5]);
                $tmflag=0;//未使用
                foreach($tmpStr2 as $j=>$jTmpStr){
                    $tmpStr3=explode('|',$jTmpStr);
                    $testList[]=$tmpStr3[0];
                    $testNum[]=$tmpStr3[1];
                    $tmpScore=array();
                    if(strstr($tmpStr3[2],',')){
                        $tmpScore=explode(',',$tmpStr3[2]);
                    }else{
                        $tmpScore=array($tmpStr3[2]);
                    }
                    $testIfChoose[]=$tmpStr3[3];
                    $testChooseNum[]=$tmpStr3[4];
                    $testScore[]=array_sum($tmpScore);
                    $totalNum+=$tmpStr3[1];
                }
            }
        }
        $nowTmpArray=array(); //记录每套选做题总个数
        foreach($testChooseNum as $i=>$iTestChooseNum){
            if($iTestChooseNum!=0){
                $nowTmpArray[]=count(array_keys($testChooseNum, $iTestChooseNum));
            }else{
                $nowTmpArray[]=0;
            }
        }
        $testChooseNum=$nowTmpArray;
        //查找试题
        $buffer = array();
        $params = array(
            'field' => array('testid','klid','typesid','typesname','testnum','diff','docname','firstloadtime'),
            'page' => array('page'=>1,'perpage'=>100),
            'where' => array('UserID'=>$this->getCookieUserID()),
            'convert' => 'testid',
            'ids' => implode(',',$testList)
        );

        $buffer = getStaticFunction('TestQuery','query', $params);
        if($buffer ===  false){
            $this->setError('30504', (IS_AJAX ? 1 : 0));
        }
        $buffer = $buffer['Base'];
        $diffArray=C('WLN_TEST_DIFF');//试卷难度
        $diffArr=array();//试卷难度统计
        $testArr=array();//试卷属性统计
        $testDiff=array();//试卷难度数组
        $k=1;//题号计数
        foreach($testList as $i=>$iTestList){
            if(!is_numeric($diffArr[$buffer[0][$iTestList]['diffid']]))
                $diffArr[$buffer[0][$iTestList]['diffid']]=0;
            $diffArr[$buffer[0][$iTestList]['diffid']]+=$testNum[$i]; //试题难度统计

            $testDiff[$i]=$buffer[0][$iTestList]['diff'];

            //获取知识点
            $klOutput=array();
            $klArr=$buffer[0][$iTestList]['klid'];
            if($klArr){
                foreach($klArr as $j=>$jKlArr){
                    $klPar=array();
                    $param['style']='knowledgeList';
                    $param['ID']=$jKlArr;
                    $param['return']=2;
                    $klPar=$this->getData($param);
                    if($klPar){
                        $klPath=array();
                        foreach($klPar as $l=>$lKlPar){
                            $klPath[]=$lKlPar['KlName'];
                        }
                        krsort($klPath);
                        $klOutput[]=implode(' >> ',$klPath);
                    }
                }
            }
            $testArr[$i]=array($buffer[0][$iTestList]['testid'],($testNum[$i]>1 ? ($k.'-'.($k+$testNum[$i]-1)) : $k),$buffer[0][$iTestList]['typesname'],$buffer[0][$iTestList]['diffstar'],$buffer[0][$iTestList]['diffname'],$buffer[0][$iTestList]['docname'],$klOutput,$buffer[0][$iTestList]['diffxing']);
            $k+=$testNum[$i];
        }
        $output=array();
        $output[0]= R('Common/TestLayer/calcPaperDiff',array(array(
            'diff'=>$testDiff,
            'score'=>$testScore,
            'chooseNum'=>$testChooseNum,
            'ifChoose'=>$testIfChoose,
            'point'=>3
        )));//试卷难度
        foreach($diffArray as $i=>$iDiffArray){
            $tmpDiff=is_numeric($diffArr[$i]) ? $diffArr[$i] : 0;
            $output[1][$i]=array($iDiffArray[0],$tmpDiff,$tmpDiff!=0 ? '('. number_format(($tmpDiff/$totalNum*100),1).'%)' : '',$iDiffArray[3].'-'.$iDiffArray[4]);
        }
        $output[2]=$testArr;

        $this->setBack($output);
    }

    /**
     * 试卷存档
     */
    public function savePaper(){
        $saveName=formatString('changeStr2Html',$_POST['papername']);
        $savePwd=$_POST['paperpwd'];
        $cookieStr=$_POST['data'];
        $testList=$_POST['testlist'];
        $saveID=$_POST['saveid'];

        $result = getStaticFunction('TestQuery','query', array(
            'field'=>'TestNumVerify.',
            'ids' => $testList
        ));
        $result = $result['TestNumVerify'];

        if(!empty($$result)){
            $this->setError('30738', 1, '', $result);
        }
        // $test=D('Test');
        // $result=$test->checkTypeOver($testList);
        // if($result){
        //     $this->setError('30738',1,'',$result);
        // }

        //文综理综
        $styleNum=0;
        $cookieAttr=R('Common/DocLayer/formatPaperCookie',array($cookieStr));
        if($cookieAttr['attr'][4]) $styleNum=$cookieAttr['attr'][4];
        if(!is_numeric($styleNum)) $styleNum=0;

        //写入存档
        $data=array();
        $data['SaveName']=$saveName;
        $data['SavePwd'] = $savePwd==='' ? 0 : md5($savePwd);
        $data['SubjectID']=cookie('SubjectId');
        $data['CookieStr']=$cookieStr;
        $data['TestList']=$testList;
        $data['LoadTime']=time();
        $data['StyleState']=0;
        $data['UserName']=$this->getCookieUserName();
        $data['TestNum']=$this->getCookieTestNum($cookieStr);
        $data['StyleNum']=$styleNum;

        $result=false;
        $docSave=$this->getModel('DocSave');
        if(!empty($saveID)){
            //验证saveID是否是当前用户的saveID
            $buffer=$docSave->findData('SaveID,UserName,SavePwd,SaveCode','SaveID='.$saveID);
            if(empty($buffer) || empty($buffer['SaveCode']) || $buffer['UserName']!=$data['UserName']){
                $this->setError('20122',1); //存档数据异常。
            }

            $result=$docSave->updateData($data,'SaveID='.$saveID);
        }else{
            $result=$saveID=$docSave->insertData($data);
        }

        if($result===false){
             $this->setError('20120',1); //存档失败，请重试
        }else{

            //更新存档里的cookie里的存档id，和提取码
            $oldSaveID=$cookieAttr['attr'][1];
            $docSave->saveCodeAndID($saveID,$oldSaveID,$cookieStr);

            //生成默认答题卡数据 在用户没有手动编辑的模式下
            $docCard=$this->getModel('DocCard');
            $cardBuffer=$docCard->findData('CardID,Style','SaveID='.$saveID);
            if($cardBuffer['Style']!=1){
                $buffer = $this->getModel('SheetFormat')->getSheetFormat(array('CookieStr'=>$data['CookieStr']));
                if($buffer[0]==1){
                    $data=[
                        'DataStr'=> serialize($buffer[1]),
                        'UserID'=>$this->getCookieUserID(),
                        'SaveID'=>$saveID,
                        'AnswerName'=>$saveName,
                        'LoadTime'=>time(),
                        'Style'=>0
                    ];
                    if($cardBuffer){
                        $result=$docCard->updateData($data,'CardID='.$cardBuffer['CardID']);
                    }else{
                        $result=$docCard->insertData($data);
                    }
                    if(!$result){
                        //更新答题卡信息失败
                    }
                }
            }
            $this->setBack($saveID);
        }
    }

    /**
     * 存储答题卡对应json数据
     * @param string Json 答题卡json数据
     * @param int SaveID 试卷存档id 没有写0
     * @return array ['success']
     * @author demo
     */
    public function saveAnswerJson(){
        $json=json_decode(stripslashes($_POST['Json']),true);
        $saveID=$_POST['SaveID'];
        if(!is_numeric($saveID) || empty($json)){
            $this->setError('30301',1); //数据标识错误！
        }

        $docCard=$this->getModel('DocCard');
        $cardBuffer=$docCard->findData('CardID,UserID,Style','SaveID='.$saveID);

        $userID=$this->getCookieUserID();
        if($cardBuffer && $cardBuffer['UserID']!=$userID){
            $this->setError('30206',1); //用户身份有误，请确认！
        }
        $data=[
            'DataStr'=> serialize($json),
            'UserID'=>$userID,
            'SaveID'=>$saveID,
            'AnswerName'=>$json['title']['content'],
            'LoadTime'=>time(),
            'Style'=>1
        ];
        if($cardBuffer){
            $result=$docCard->updateData($data,'CardID='.$cardBuffer['CardID']);
        }else{
            $result=$docCard->insertData($data);
        }
        if(!$result){
            //更新答题卡信息失败
            $this->setError('30308',1); //操作失败！
        }
        $this->setBack('success');
    }
    /**
     * 获取试卷对应答题卡json数据
     * @param string CookieStr 试卷cookie数据
     * @param int SaveID 试卷存档id
     * @param int Style 获取数据类型 0根据cookie获取答题卡默认数据  1根据saveid获取存档答题卡数据 2同时获取0和1的数据
     * @return array ['Auto'=>自动生成json,'Data'=>数据库对应数据]
     *      数据库对应数据[
     *              'CardID'=>答题卡id
     *              'DataStr'=>答题卡json
     *              'SaveID'=>对应试卷存档id
     *              'Style'=>生成方式 0自动生成 1手动生成
     *          ]
     * @author demo
     */
    public function getAnswerJson(){
        //参数
        $style=$_POST['Style'];
        $cookie=$_POST['CookieStr'];
        $saveID=$_POST['SaveID'];

        $output=array();//输出数据

        $buffer = $this->getModel('SheetFormat')->getSheetFormat(array('CookieStr'=>$cookie));
        if($buffer[0]==0) $this->setError($buffer[1],1); //错误信息
        $output['Auto']=$buffer[1];
        if(empty($style)){
            $this->setBack($output);
            exit();
        }

        //生成默认答题卡数据 在用户没有手动编辑的模式下
        if($saveID){
            $docCard=$this->getModel('DocCard');
            $cardBuffer=$docCard->findData('CardID,DataStr,SaveID,UserID,Style','SaveID='.$saveID);
            if($cardBuffer){
                if($cardBuffer['UserID']!=$this->getCookieUserID()){
                    $this->setError('30206',1); //用户身份有误，请确认！
                }
                $cardBuffer['DataStr'] =  unserialize($cardBuffer['DataStr']);
                $output['Data']=$cardBuffer;
            }
            if($style==1){
                $this->setBack($output);
                exit();
            }
        }
        $this->setBack($output);
    }

    /**
     * 留作业
     * @param array $zujuanData
     * @param array $addData 添加作业的数组
     * @return mixed
     * @author demo
     */
    public function saveWork($zujuanData=array(),$addData=false){
        if(!$zujuanData) $zujuanData = $_POST;
        $saveName  = formatString('changeStr2Html',$zujuanData['papername']);
        $cookieStr = $zujuanData['data'];
        $testList  = $zujuanData['testlist'];
        $data = getStaticFunction('TestQuery', 'query', array(
            'field'=>'TestNumVerify.',
            'ids' => $testList
        ));
        if($data === false){
            $this->setError('30504', 1);
        }
        // $query = \Test\Model\TestQueryModel::getInstance('TestDownloadQuery');
        // $query->setParams(array(
        //     'where' => array('UserID'=>$this->getCookieUserID())
        // ),$testList);
        // $data = $query->getResult();
        $data = $data['TestNumVerify'];
        if(!empty($data)){
            $this->setError('30738',1,'',$data);
        }
        // $test=D('Test');
        // $result=$test->checkTypeOver($testList);
        // if($result){
        //     $this->setError('30738','1','',$result);
        // }


        //文综理综
        $styleNum=0;
        $cookieAttr=R('Common/DocLayer/formatPaperCookie',array($cookieStr));
        if($cookieAttr['attr'][4]) $styleNum=$cookieAttr['attr'][4];
        if(!is_numeric($styleNum)) $styleNum=0;

        //写入存档
        $data=array();
        $data['SaveName']= $saveName;
        $data['SavePwd'] = 0;
        $data['SubjectID']=cookie('SubjectId');
        $data['CookieStr']=$cookieStr;
        $testList=str_replace(array('|0'),'',$testList);
        $data['TestList']=$testList;
        $data['LoadTime']=time();
        $data['StyleState']=1;
        $data['UserName']=$this->getCookieUserName();
        $data['StyleNum']=$styleNum;
        $testNum=0;
        $tmpStr1=explode('@#@',$cookieStr);
        foreach($tmpStr1 as $i=>$iTmpStr1){
            $tmpStr2=explode('@$@',$iTmpStr1);
            if(count($tmpStr2)==7 && !empty($tmpStr2[5])){
                $tmpStr3=explode(';',$tmpStr2[5]);
                foreach($tmpStr3 as $j=>$jTmpStr3){
                    $tmpStr4=explode('|',$jTmpStr3);
                    $testNum+=$tmpStr4[1];
                }
            }
        }
        $data['TestNum']=$testNum;
        $docSave=$this->getModel('DocSave');
        $addResult=$docSave->insertData($data);
        if($addResult===false){
            $this->setError('30737',1);
        }else{
            //更新存档里的cookie里的存档id，和提取码
            $oldSaveID=$cookieAttr['attr'][1];
            $docSave->saveCodeAndID($addResult,$oldSaveID,$cookieStr);

            if(!empty($addData)){
                return $addResult;
            }else{
                $this->setBack('success');
            }
        }
    }

    /**
     * 试卷历史存档
     */
    public function saveList(){
        $dateDiff=$_POST['datediff'];
        $curPage=$_POST['page'];
        $subjectID=$_POST['subjectID'];
        if(empty($subjectID)){
            $this->setError('30733',1);
        }
        $username=$this->getCookieUserName();
        $dataTime=0;
        $perPage=10;
        switch($dateDiff){
            case 'today':
                $dataTime=strtotime(date("Y-m-d",time()));
            break;
            case 'yestoday':
                $dataTime=strtotime(date("Y-m-d",time()-24*3600));
            break;
            case 'curweek':
                $dataTime=strtotime("-1 week");
            break;
            case 'curmonth':
                $dataTime=strtotime(date('Y-m-1'));
            break;
            case 'all':
                $dataTime=0;
            break;
        }


        $where=' (StyleState=0 and UserName="'.$username.'" and LoadTime >'.$dataTime.' and SubjectID='.$subjectID.') ';

        //学科对应综合属性
        $subject=SS('subject');
        $styleNum=$subject[$subjectID]['Style'];
        if($styleNum==1 || $styleNum==2){
            $where.=' or StyleNum='.$styleNum;
        }

        $docSave = $this->getModel('DocSave');
        $saveCount=$docSave->selectCount($where,'*');
        $pageCount=ceil($saveCount/$perPage);
        if($curPage>$pageCount) $curPage=$pageCount;
        if($curPage<1 || !is_numeric($curPage)) $curPage=1;
        $buffer=$docSave->selectData(
            'SaveID,SaveName,LoadTime,SavePwd,SaveCode',
            $where,
            'LoadTime desc',
            ($curPage-1)*$perPage.','.$perPage
        );

        foreach($buffer as $i=>$iBuffer){
            $buffer[$i]['SavePwd']= $iBuffer['SavePwd']==0 ? 0 : 1;
            $buffer[$i]['SaveCode']= $iBuffer['SaveCode']=='' ? 0 : 1;
        }

        $output[0]=$buffer;
        $output[1]=$saveCount;
        $output[2]=$perPage;
        $this->setBack($output);
    }

    /**
     * 判断存档密码
     */
    public function isSavePwd(){
        $id=$_POST['id'];
        $pwd=$_POST['key'];
        if(empty($pwd)) $pwd=0;
        $buffer = $this->getModel('DocSave')->selectData(
            '*',
            'SaveID='.$id.' and SavePwd="'.($pwd==0 ? 0 : md5($pwd)).'"'
        );
        if(count($buffer)>=1){
            $this->setBack($buffer[0]['CookieStr']);
        }else{
            $this->setError('30804',1);
        }
    }

    /**
     * 获取存档提取码
     */
    public function getSaveCode(){
        $paperID=$_POST['paperID'];
        $buffer = $this->getModel('DocSave')->findData(
            'SaveID,SaveName,SaveCode,CookieStr,UserName',
            'SaveID='.$paperID
        );
        $userName=$this->getCookieUserName();
        if($userName!=$buffer['UserName']){
            $this->setError('30301',1); //数据标示错误
        }

        //判断答题卡类型
        $cookieArray=R('Common/DocLayer/formatPaperCookie',array($buffer['CookieStr']));

        $buffer['AnswerStyle']='无';
        if($cookieArray['attr'][2]==1) $buffer['AnswerStyle']='统一答题卡';
        else if($cookieArray['attr'][2]==2) $buffer['AnswerStyle']='ab卷';

        $this->setBack($buffer);
    }

    /**
     * 删除历史存档
     */
    public function delSave(){
        $username=$this->getCookieUserName();
            $id=$_POST['id'];
            $docSave = $this->getModel('DocSave');
            $buffer=$docSave->selectData(
                '*',
                'SaveID='.$id
            );
            if($buffer[0]['UserName']==$username){
                $docSave->deleteData(
                    'SaveID='.$id
                );
                $this->setBack('true');
            }
    }

    /**
     * 删除历史下载
     */
    public function delDown(){
        $username=$this->getCookieUserName();
        $id=$_POST['id'];
        $docDown = $this->getModel('DocDown');
        $buffer=$docDown->selectData(
            '*',
            'DownID='.$id
        );
        if($buffer[0]['UserName']==$username){
            $docDown->updateData(
                array('IfShow'=>0),
                'DownID='.$id
            );
            $this->setBack('true');
        }
    }

    /**
     * 历史下载
     */
    public function saveDown(){
        $dateDiff=$_POST['datediff'];
        $curPage=$_POST['page'];
        $area=$_POST['area'];
        $subject=$_POST['subjectID'];
        $username=$this->getCookieUserName();
        $dataTime=0;
        $perPage=10;
        switch($dateDiff){
            case 'today':
                $dataTime=strtotime(date("Y-m-d",time()));
            break;
            case 'yestoday':
                $dataTime  = strtotime(date("Y-m-d",time()-24*3600));
                $dataTime2 = strtotime(date("Y-m-d",time()));
            break;
            case 'curweek':
                $theDay    = date('N',time());//计算本周是第几天 1-7
                $theDay   -= 1;
                $dataTime  = strtotime(date('Y-m-d',time()-24*3600*$theDay));
            break;
            case 'curmonth':
                $dataTime=strtotime(date('Y-m-1'));
            break;
            case 'all':
                $dataTime=0;
            break;
        }
        $where="IfShow=1";
        if($area){
            if($dataTime2)
            $where.=' and UserName="'.$username.'" and LoadTime between '.$dataTime.' and '.$dataTime2.' and SubjectID='.$subject;
            else
            $where.=' and UserName="'.$username.'" and LoadTime >'.$dataTime.' and SubjectID='.$subject;
        }
        else{
            $where.=' and UserName="'.$username.'" and LoadTime >'.$dataTime;
        }
            $docDown = $this->getModel('DocDown');
            $downCount=$docDown->selectCount($where,'*');
            $pageCount=ceil($downCount/$perPage);
            if($curPage>$pageCount) $curPage=$pageCount;
            if($curPage<1 || !is_numeric($curPage)) $curPage=1;
            $buffer=$docDown->selectData(
                '*',
                $where,
                'LoadTime desc',
                ($curPage-1)*$perPage.','.$perPage
            );
        foreach($buffer as $i=>$iBuffer){
            $buffer[$i]['id']=md5($iBuffer['DownID'].'(*&!@#%^&#@$)(@!^^#!%@#&*@!)');
        }

        $output[0]=$buffer;
        $output[1]=$downCount;
        $output[2]=$perPage;
        $this->setBack($output);
    }

    /**
     * 下载保存的试卷
     */
    public function loadSaveWord(){
        $host=C('WLN_DOC_HOST');
        $downID=$_POST['DownID'];
        $id=$_POST['id'];
        $error=0;
        if($id!=md5($downID.'(*&!@#%^&#@$)(@!^^#!%@#&*@!)')){
            $this->setError('30113',1);
        }

        if($host){
            $buffer = $this->getModel('DocDown')->selectData(
                '*',
                'DownID='.$downID
            );
            if(!$buffer) $this->setError('30805',1);
            if(!checkString('canLoad',$buffer[0]['DocPath'])){
                $this->setError('20221',1);//文档路径有误
            }else{
                $paperName = $buffer[0]['DocName'];
                $url=$host.R('Common/UploadLayer/getDocServerUrl',array($buffer[0]['DocPath'],'down','',$paperName));
                $this->setBack($url);
            }
        }else{
            $this->setBack(U('Index/worddownload?DownID='.$downID.'&id='.$id));
        }
    }

    /**
     * 本地试卷下载
     */
    public function wordDownLoad() {
        $downID=$_GET['DownID'];
        $id=$_GET['id'];
        $error=0;
        $doc = $this->getModel('Doc');
        if($id!=md5($downID.'(*&!@#%^&#@$)(@!^^#!%@#&*@!)')){
            $error=1;
        }else{
            $buffer = $this->getModel('DocDown')->selectData(
                '*',
                'DownID='.$downID
            );
            if($buffer){
                $paperName = $buffer[0]['DocName'];
                $path = $buffer[0]['DocPath'];
                $tmpArr=explode('.',$path);
                $docVersion = '.'.$tmpArr[count($tmpArr)-1];
                $path=realpath('./').$path;
                $content = file_get_contents(iconv('UTF-8', 'GBK//IGNORE', $path));
                $doc->wordheader($paperName, $docVersion);
                echo $content;
            }else{
                $error=1;
            }
        }
        if($error==1){
            $doc->wordheader('下载错误', '.doc');
            exit();
        }
    }

    /**
     * ajax获取试题属性
     */
    public function getTypes() {
        $output = array ();
        $subjectID = $_POST['id'];
        $style = $_POST['style'];
        $param['style']='types';
        $param['subjectID'] = $subjectID;
        $param['return'] = 2;
        $output[1] = $this->getData($param); //题型
        //难度
        $param2['style']='diff';
        $param2['return'] = 2;
        $output[2] = $this->getData($param2);
        //试卷属性
        $output[4] = $this->getModel('DocType')->getDocAttr($style);
        //能力属性
        $param5['style']='ability';
        $param5['subjectID'] = $subjectID;
        $param5['return'] = 2;
        $output[5]=$this->getData($param5);
        $output[6] = SS('docSource');
        $this->setBack($output);
    }


    /**
     * ajax获取试题学科数据
     */
    public function getSubejctByTestID() {
        $testList=$_POST['testlist'];
        if(empty($testList)){
            $this->setBack('');
        };
        $where=array('TestID'=>$testList);
        $field=array('testid','subjectid');
        $page=array('page'=>1,'perpage'=>100);
        $tmpStr=$this->getTest($field,$where,'',$page);
        if($tmpStr[0]){
            $subjectIDArray=array();
            foreach($tmpStr[0] as $iTmpStr){
                $subjectIDArray[]=$iTmpStr['subjectid'];
            }
            $subjectIDArray=array_unique($subjectIDArray);
            $subjectIDArray=array_filter($subjectIDArray);
            $this->setBack(implode(',',$subjectIDArray));
        }
            $this->setBack('');
    }

    /**
     * ajax获取试题 试题列表显示试题内容
     */
    public function getOneTestById() {
        $output=array();
        $id = $_POST['id'];
        if(empty($id)) $this->setError('30301',1);
        $width = $_POST['width'];//未使用
        $where=array('TestID'=>$id);
        $field=array('kllist','analytic','answer','remark','firstloadtimeint','typeid');
        $page=array('page'=>1,'perpage'=>1);
        $tmpStr=$this->getTest($field,$where,'',$page);

        //4.15后上传的同步测试类文档随机显示试题解析
        if($tmpStr[0][0]['loadtimeint']>strtotime('2015-4-15') && $tmpStr[0][0]['typeid']==7){
            //随机概率0.4
            $randInt=rand(100000, 999999);
            if($randInt<600000){
                $tmpStr[0][0]['analytic']='';
            }
        }

        if($tmpStr){
            $output[0]='success';
            $output[1]=$tmpStr;
            $this->setBack($output);
        }
    }

    /**
     * ajax获取试题 组卷页面显示试题
     */
    public function getZjTestById() {
        $output=array();
        $id = $_POST['id'];
        if(empty($id)) $this->setError('30301',1);

        //获取试题对应题文
        $field = array('testid','typesid','typesname','testnum','testold','diff','docname');
        $where = array('TestID'=>$id);
        $order = array();
        $page = array('page'=>1,'perpage'=>100,'limit'=>100);

        //获取合并后的索引数据
        $result = $this->getModel('TestRealQuery')->getIndexTest($field,$where,$order,$page,0,2);

        //获取当前用户所在学校
        $userID=$this->getCookieUserID();
        $userSchool=$this->getModel('User')->getUserSchool($userID);
        if($result === false){
            $this->setError('30504', (IS_AJAX ? 1 : 0));
        }
        //$customTest = D('CustomTest');//未使用
        foreach($result as $key=>$value){
            $result[$key]['test'] = $value['testold'];
            unset($result[$key]['testold']);
        }
        $max=count($result);
        $result=array($result,$max,100);
        if(!empty($userSchool)){
            $result['schoolName']=$userSchool['SchoolName'];
        }
        $this->setBack($result);
        // //提取试题分类，同时替换掉分类
        // $classify = ltrim(strrchr($id, '|'), '|');
        // $id = preg_replace('/\|\d{1}/m', '', $id);
        // //如果为校本试题进行查库  2015-2-6
        // if(1 == $classify){
        //     $where['UserName'] = $this->getCookieUserName();
        //     $where['TestID'] = $id;
        //     $customTest = D('CustomTest');
        //     $result = $customTest->getDataList($where, '', array('disposeContent'=>false));
        //     $data = array();
        //     foreach($result['data'] as $value){
        //         $value['test'] = $customTest->replaceText($value['test']);
        //         $data[$value['testid']] = $value;
        //     }
        //     unset($result);
        //     $this->setBack(array(
        //         $data, count($data), 100
        //     ));
        // }
        // $width = $_POST['width'];
        // $where=array('TestID'=>$id, 'classify'=>$classify);
        // $field=array('testid','typesid','typesname','testnum','testold','diff','docname');
        // $page=array('page'=>1,'perpage'=>100,'limit'=>100);
        // $tmpStr=$this->getTest($field,$where,'',$page,1);
        // if($tmpStr){
        //     foreach($tmpStr[0] as $i=>$iTmpStr){
        //         $tmpStr[0][$i]['test']=$tmpStr[0][$i]['testold'];
        //         unset($tmpStr[0][$i]['testold']);
        //     }
        //     $this->setBack($tmpStr);
        // }
    }

    /**
     * ajax获取试题 组卷页面详细单个试题
     */
    public function getDetailTestById() {
        $output=array();
        $id = $_POST['id'];
        if(empty($id)) $this->setError('30301',1);
        $width = $_POST['width'];
        $result = getStaticFunction('TestQuery', 'query', array(
            'field' => array('testid','test','answer','docname','firstloadtime','analytic','remark','typesname','diff'),
            'convert' => 'testid',
            'where' => array('width'=>$width, 'UserID'=>$this->getCookieUserID()),
            'page' => array('page'=>1,'perpage'=>1),
            'ids' => $id
        ));
        if($result === false){
            $this->setError('30504', (IS_AJAX ? 1 : 0));
        }
        $result = $result['Base'];
        $this->setBack($result);
        // if(0 === strpos($id, 'c')){
        //     $where['UserName'] = $this->getCookieUserName();
        //     $where['TestID'] = $id;
        //     $customTest = D('CustomTest');
        //     $result = $customTest->getDataList($where);
        //     $result = $result['data'];
        //     $other = $customTest->getOtherData($where);
        //     $data = array();
        //     if(!empty($result))
        //         $data[$result[0]['testid']] = array_merge($result[0],$other);
        //     unset($result, $other);
        //     dump($data);exit;
        //     $query->setMode(\Test\Model\TestQueryModel::PRI);
        //     $data = $query->getPrvList(array(
        //             'field' => array('testid','test','answer','docname','firstloadtime','analytic','remark','typesname','diff'),
        //             'convert' => true
        //         ), $id);
        //     $this->setBack(array(
        //         $data, 1, 100
        //     ));
        // }
        // $where=array('TestID'=>$id,'width'=>$width);
        // $field=array('testid','test','answer','docname','firstloadtime','analytic','remark','typesname','diff');
        // $page=array('page'=>1,'perpage'=>1);
        // $tmpStr=$this->getTest($field,$where,'',$page,1);
        // if($tmpStr){
        //     $this->setBack($tmpStr);
        // }
    }

    /**
     * ajax获取试题 所有页面试题列表
     */
    public function getTestList(){
        $width = $_POST['width'];
        $subjectID= $_POST['sid']; //学科
        $docID = $_POST['did']; //试卷
        $docTypeID = $_POST['dtid']; //试卷类型
        $SourceID=$_POST['sourceid'];//文档来源
        $diff = $_POST['dif']; //难度
        $typeID = $_POST['tid']; //题型
        $klID = $_POST['kid']; //知识点
        $chapterID = $_POST['cid']; //章节
        $specialID = $_POST['pid']; //专题
        $abilityID = $_POST['abi'];//能力选项
        $key = $_POST['key']; //关键字
        $time = $_POST['time']; //时间
        $order = $_POST['o']; //排序
        $page = $_POST['page']; //页数
        $perPage = $_POST['perpage']; //每页数量
        $ifInter = $_POST['ifInter']; //截取题文
        $interLen = $_POST['interLen']; //截取题文
        $where=array();
        if($subjectID) $where['SubjectID']=$subjectID;
        if($docID) $where['DocID']=$docID;
        if($docTypeID) $where['DocTypeID']=$docTypeID;
        if($SourceID) $where['SourceID']=$SourceID; //来源
        if($diff) $where['Diff']=$diff;
        if($typeID) $where['TypesID']=$typeID;
        if($klID) $where['KlID']=$klID;
        if($chapterID && empty($abilityID)) $where['ChapterID']=$chapterID;
        if($specialID) $where['SpecialID']=$subjectID;
        if($key) $where['key']=$key;
        if($width) $where['width']=$width;
        $where['ShowWhere']=array(0,1);
        $lastTime = 0;
        if ($time) {
            switch ($time) {
                case "onemonth" :
                    $lastTime = time() - 30 * 24 * 3600;
                    break;
                case "threemonth" :
                    $lastTime = time() - 3 * 30 * 24 * 3600;
                    break;
                case "sixmonth" :
                    $lastTime = time() - 6 * 30 * 24 * 3600;
                    break;
                case "oneyear" :
                    $lastTime = time() - 12 * 30 * 24 * 3600;
                    break;
                case "oneyearold" :
                    $lastTime = -time() + 12 * 30 * 24 * 3600;
                    break;
            }
        }
        if($lastTime) $where['LastTime']=$lastTime;
        if(empty($abilityID)) $where['Duplicate']=0;
        $orderList='';

        //章节选题不按顺序显示
        $random = $_POST['randoms']; //是否随机
        $secondOrder='@id DESC';
        if($random) $secondOrder='diff DESC';

        if($order){
            switch($order){
                case 'pdown':
                $orderList='times DESC, '.$secondOrder;
                break;
                case 'pup':
                $orderList='times ASC, '.$secondOrder;
                break;
                case 'ddown':
                $orderList='diff DESC, '.$secondOrder;
                break;
                case 'dup':
                $orderList='diff ASC, '.$secondOrder;
                break;
                case 'tdown':
                $orderList='firstloadtime DESC, '.$secondOrder;
                break;
                case 'tup':
                $orderList='firstloadtime ASC, '.$secondOrder;
                break;
                case 'weight':
                $orderList='@weight DESC, '.$secondOrder;
                break;
            }
        }
        if($orderList) $order=array($orderList);
        else if($random) $order=array('@random');
        else $order=array('docyear DESC ,firstloadtime DESC,'.$secondOrder);

        //是否选中能力
        if($abilityID){
            $where=array();
            if($subjectID) $where['SubjectID']=$subjectID;
            $seach='AbilitID='.$abilityID.' and CaptID='.$chapterID;
            $docIDArr = $this->getModel('DocAbiCapt')->selectData(
                'DocID',
                $seach
            );
            if($docIDArr){
                $IDArr=array();
                foreach($docIDArr as $key=>$val){
                    $IDArr[]=$val['DocID'];
                }
                $where['DocID']=implode(',',$IDArr);
                $order=array('@id ASC');
            }else{
                $data[0]=array('4');
                $data[1]=0;
                $data[2]='20';
                $this->setBack($data);
            }
        }
        $field=array('testid','typesid','sourceid','subjectname','typesname','testnum','test','diff','docname','firstloadtime');
        $page=array('page'=>$page,'perpage'=>$perPage);
        if($key) $page['limit']=1000;
        $tmpStr=$this->getTest($field,$where,$order,$page);
        $docSource=SS('docSource');
        foreach($tmpStr[0] as $i=>$iTmpStr){
            $tmpStr[0][$i]['sourcePic']='';
            if($docSource[$tmpStr[0][$i]['sourceid']]['IfShowLogi']==1){
                $tmpStr[0][$i]['sourcePic']=$docSource[$tmpStr[0][$i]['sourceid']]['LogoPath'];
            }
        }
        if($ifInter){
            foreach($tmpStr[0] as $i=>$iTmpStr){
                $tmpStr[0][$i]['test']=mb_substr(preg_replace('/<[^>]*>/i','',$iTmpStr['test']),0,$interLen,'UTF-8').'...';
            }
        }
        if($tmpStr){
            $this->setBack($tmpStr);
        }else{
            $this->setBack('抱歉！暂时没有符合条件的试卷，请尝试更换查询条件。');
        }
    }

    /**
     * @param $field
     * @param $where
     * @param $order
     * @param $page
     * @param int $reload
     * @return mixed
     */
    protected function getTest($field,$where,$order,$page,$reload=0){
        $tmpStr=R('Common/TestLayer/indexTestList',array($field,$where,$order,$page));
        if($tmpStr === false){
             $this->setError('30504', (IS_AJAX ? 1 : 0));
        }
        if($reload) $tmpStr[0]=R('Common/TestLayer/reloadTestArr',array($tmpStr[0]));
        return $tmpStr;
    }

    /**
     * ajax获取试题 组卷页面提取试题
     * @author demo
     */
    public function getSameById() {
        $id = $_POST['id'];
        $allID = $_POST['allid'];
        if (empty ($id))
            $this->setError('20115',1);

        $arrAll=explode(',',$allID);//数据与该数组不重复
        $buffer=$this->getTest(array('testid','typesid','klid','gradeid'),array('TestID'=>$id),'',array('page'=>1,'perpage'=>1,'limit'=>1),1);

        if(empty($buffer[1])) $this->setError('20115',1);
        $arr=array();
        $arr['TypesID']=$buffer[0][$id]['typesid'];
        $arr['KlID']=implode(',',$buffer[0][$id]['klid']);
        $arr['GradeID']=$buffer[0][$id]['gradeid'];
        //此处仅从共有题库中取相关试题
        $buffer=$this->getTest(array('testid'),$arr,array('@random'),array('page'=>1,'perpage'=>100,'limit'=>100),1);

        if(empty($buffer[1]))  $this->setError('20115',1);
        $arr=array();
        //除去与页面重复数据
        $tmpArr=array();
        foreach($buffer[0] as $i=>$iBuffer){
            if(!in_array($iBuffer['testid'],$arrAll)){
                $tmpArr[]=$iBuffer;
            }
        }
        $countNum=count($tmpArr);
        if($countNum<1){
            $this->setError('20115',1);
        }else if($countNum>10){
            $tmpArr2=array();
            foreach($tmpArr as $i=>$iTmpArr){
                $tmpArr2[]=$iTmpArr['testid'];
            }
            $maxTotal=count($tmpArr2);
            //随机找十个数据
            $roundArr = R('Common/TestLayer/getRandArr',array(array(),10,$maxTotal));
            foreach($roundArr as $i=>$iRoundArr){
                $arr[]=$tmpArr2[$roundArr[$i]];
            }
        }else{
            foreach($tmpArr as $i=>$iTmpArr){
                $arr[]=$iTmpArr['testid'];
            }
        }
        if(!empty($arr[0])){
            $result['data']=$this->getTest(array('testid','typesid','typesname','testnum','testold','answerold','analyticold','remarkold','diff','docname','firstloadtime'),array('TestID'=>implode(',',$arr)),'',array('page'=>1,'perpage'=>10,'limit'=>10),1);

            foreach($result['data'][0] as $i=>$iResult){
                $result['data'][0][$i]['test'] = $iResult['testold'];
                $result['data'][0][$i]['answer'] = $iResult['answerold'];
                $result['data'][0][$i]['analytic'] = $iResult['analyticold'];
                $result['data'][0][$i]['remark'] = $iResult['remarkold'];
            }
        }
        $result['total']=$countNum;
        $this->setBack($result);
    }
    /**
     * 通过试题ID获取和它相似的试题ID及属性
     * @author demo
     */
    public function getSameTestIDByID(){
        $id=$_POST['id'];//试卷中心所有试题ID
        if (empty ($id)) {
            $this->setError('20115', 1);
        }
        $arrAll=explode(',',$id);//新查找的数据与该数组不能重复
        //获取试卷试题的属性
        $testInfoList=$this->getTest(array('testid','typesid','klid','gradeid','subjectid','testnum'),array('TestID'=>$id),'','',1);
        if(empty($testInfoList[1])) $this->setError('20115',1);
        $arr=array();
        $subjectIDArr=array(12,14);//需要限制小题数量的学科ID
        foreach($testInfoList[0] as $i =>$iTestInfoList){
            $testArr=array();
            $testArr['SubjectID']=$iTestInfoList['subjectid'];
            $testArr['TypesID']=$iTestInfoList['typesid'];
            $testArr['KlID']=implode(',',$iTestInfoList['klid']);
            $testArr['GradeID']=$iTestInfoList['gradeid'];

            //此处仅从共有题库中取相关试题
            $buffer=$this->getTest(array('testid','testnum'),$testArr,array('@random'),array('page'=>1,'perpage'=>10),1);

            //除去与页面重复数据
            $tmpArr=array();
            if(!empty($buffer[1])){
                foreach($buffer[0] as $j=>$jBuffer){
                    //英语和语文学科加小题数量限制，防止页面分值错误
                    if(in_array($iTestInfoList['subjectid'],$subjectIDArr)) {//是英语或者语文学科
                        if(!in_array($jBuffer['testid'],$arrAll) && $iTestInfoList['testnum']==$jBuffer['testnum']){
                            $tmpArr[]=$jBuffer;
                        }
                    }else{
                        if(!in_array($jBuffer['testid'],$arrAll)){//去除和页面上重复的试题
                            $tmpArr[]=$jBuffer;
                        }
                    }
                }
            }

            if(empty($tmpArr)){//如果没有相似试题就还是用之前的试题ID
                $arr[$i]['testID']=$i;
                $arr[$i]['testNum']=$iTestInfoList['testnum'];
            }else{//将试题ID替换成相似试题ID
                $tmpArrRand = array_rand($tmpArr,1);
                $arr[$i]['testID']=$tmpArr[$tmpArrRand]['testid'];
                $arr[$i]['testNum']=$tmpArr[$tmpArrRand]['testnum'];
            }
        }
        $this->setBack($arr);
    }

    /**
     * ajax获取文档
     */
    public function getDocList(){
        $subjectID=$_POST['sid'];
        $typeID=$_POST['tid'];
        $areaID=$_POST['area'];
        $order=$_POST['o'];
        $page=$_POST['page'];
        $perPage=$_POST['perpage'];
        $gradeID=$_POST['grade'];
        $key=$_POST['key'];
        $oldKey=$_POST['oldkey'];
        if(empty($perPage)) $perPage=20;
        if(!$subjectID){
            $this->setBack('chooseSubject');
        }

        if(strlen($key)>1 && $oldKey!=$key){
            //记录关键字查询
            $username = $this->getCookieUserName();
            $this->getModel('LogSearch')->addKeyWord($key,$subjectID,$username);
        }

        $where=array();
        $where['SubjectID']=$subjectID;
        $where['ShowWhere']=array(0,1);
        $docType=SS('docType');
        if($typeID && $docType[$typeID]['IfHidden']==1) {
            $this->setBack('');//抱歉！暂时没有符合条件的试卷，请尝试更换查询条件。
        }else if($typeID){
            $where['TypeID'] = $typeID;
        }else{
            $typeID=array();
            foreach($docType as $i=>$iDocType){
                if($iDocType['IfHidden']!=1){
                    $typeID[]=$i;
                }
            }
            $where['TypeID']=implode(',',$typeID);
        }
        if($key){
            $where['key']=$key;
        }
        if($areaID){
            $where['AreaID']=$areaID.',0';
        }
        if($gradeID){
            $where['GradeID']=$gradeID;
        }
        $orderList='';
        if($order){
            switch($order){
                case 'ydown':
                $orderList='docyear DESC, introfirsttime DESC';
                break;
                case 'yup':
                $orderList='docyear ASC, introfirsttime DESC';
                break;
                case 'tdown':
                $orderList='introfirsttime DESC, @id DESC';
                break;
                case 'tup':
                $orderList='introfirsttime ASC, @id DESC';
                break;
                case 'rdown': //IfRencon倒序、第一次入库时间倒序
                $orderList='ifrecom DESC, introfirsttime DESC';
                break;
            }
        }
        //$order=array('docyear DESC, introtime DESC');
        if($orderList) $order=array($orderList);
        else $order=array('docyear DESC,introfirsttime Desc');
        $field=array('docid','docname','subjectname','typename','docyear','areaname','loadtime','introfirsttime','introtime');
        $page=array('page'=>$page,'perpage'=>$perPage);
        $doc=$this->getModel('Doc');
        $buffer=$doc->getDocIndex($field,$where,$order,$page);

        if($buffer){
            $this->setBack($buffer);
        }else{
            $this->setBack('');//抱歉！暂时没有符合条件的试卷，请尝试更换查询条件。
        }
    }

    /**
    * ajax纠错信息提交；
    * @author demo 2014年8月18日
    */
    public function correct(){
        $correctContent = trim($_POST['correctcontent']);
        if(empty($correctContent) || $correctContent=="我来说两句~"){
            $this->setError('20114',1);
        }
        $data['Ctime']=time();
        $result['status']='success';
        $result['msg']='信息提交成功！';
        $data['UserName']=$this->getCookieUserName();
        $data['TestID']=$_POST['testID'];
        $data['SubjectID']=$_POST['SubjectId'];
        $data['Content']=formatString('IPReplace',$correctContent);//formatString('changeStr2Html',$correctContent);
        $data['From']='0';//是组卷前台，根据该状态是0；
        if(!empty($_POST['TypeID'])){
            $data['TypeID']=$_POST['TypeID'];
        }else{
            $data['TypeID']=0;//没有选中错误类型，默认成错误类型：其他(0)
        }
        $correctLogResult=$this->getModel('CorrectLog')->insertData($data);
        if(!empty($correctLogResult)){//判断成功
            $buffer = $this->getModel('MissionHallRecords')->finishTask($this->getCookieUserID());
            $this->setBack($result);
        }else{
            $this->setError('20113',1);
        }
    }

    /**
     * 试题收藏列表
     * @author demo
     * @date 2014年9月20日
     */
    public function testFav(){
        $cataID=$_POST['cataID'];
        $curPage=$_POST['page'];
        $subject=$_POST['subjectID'];
        if($subject != ''){
            $subjectWhere=' AND SubjectID='.$subject.'';
        }
        $username=$this->getCookieUserName();
        $dataTime=0;
        $perPage=10;
        //将之前的按时间归类，改为按目录结构归类
        $catalog=$this->getModel('UserCatalog');
        if($cataID=='all'){
            $catalogID=' CatalogID>=0';
        }elseif($cataID==0){
            $catalogID=' CatalogID=0';
        }else{
            $data='UserName="'.$username.'" and FatherID='.$cataID. $subjectWhere.' AND `From`=1';
            $field='CatalogID';
            $cataList=$catalog->getCatalogList($field,$data);
            $cataid=array();
            $cataid[0]=$cataID;
            $i=1;
            if($cataList){
                foreach($cataList as $iCataList){
                    $cataid[$i]=$iCataList['CatalogID'];
                    $i++;
                }
            }
            $catalogs=implode(',',$cataid);
            $catalogID=' CatalogID in('.$catalogs.')';
        }
        $where='UserName="'.$username.'" AND '.$catalogID.$subjectWhere.' AND `From`=1';
        $userCollect = $this->getModel('UserCollect');
        $saveCount=$userCollect->selectCount($where,'*');
        $pageCount=ceil($saveCount/$perPage);
        if($curPage>$pageCount) $curPage=$pageCount;
        if($curPage<1 || !is_numeric($curPage)) $curPage=1;
        $buffer=$userCollect->selectData(
            '*',
            $where,
            'LoadTime desc',
            ($curPage-1)*$perPage.','.$perPage
        );
        //查询试题
        $testList=array();
        foreach($buffer as $i=>$iBuffer){
            $testList[]=$iBuffer['TestID'];
        }

        $where=array('TestID'=>implode(',',$testList));
        $field=array('testid','typesid','typesname','testnum','test','diff','docname','firstloadtime');
        $page=array('page'=>1,'perpage'=>100);
        $tmpStr=$this->getTest($field,$where,'',$page,1);
        if(!$tmpStr[0]) $this->setBack('');
        $testArray=$tmpStr[0];
        $j=0;
        $tmpArr=array();
        foreach($buffer as $i=>$iBuffer){
            if(empty($testArray[$iBuffer['TestID']])) continue;
            $tmpArr[]=$testArray[$iBuffer['TestID']];
            $j++;
        }
        unset($buffer);
        unset($testArray);
        $output[0]=$tmpArr;
        $output[1]=$saveCount;
        $output[2]=$perPage;
        $this->setBack($output);
    }

    /**
     * 试题收藏
     */
    public function favSave(){
        $id=$_POST['id'];
        $favName=$_POST['favname'];
        $subject=cookie('SubjectId');
        $username=$this->getCookieUserName();
        $catalogID=$_POST['catalogid'];
        $userCollect = $this->getModel('UserCollect');
        $buffer=$userCollect->selectData(
            'CollectID',
            'UserName="'.$username.'" AND SubjectID="'.$subject.'" and TestID="'.$id.'" AND `From`=1'
        );
        if(count($buffer)==0){
            $data=array();
            $data['TestID']=$id;
            $data['LoadTime']=time();
            $data['UserName']=$username;
            $data['SubjectID']=$subject;
            $data['FavName']=$favName;
            $data['CatalogID']=$catalogID;
            $data['From']=1;
            if($userCollect->insertData($data)===false){
                $this->setError('20222',1);
            }else{
                $this->setBack('true');
            }
        }else{
            $this->setBack('试题已收藏！');
        }
    }

    /**
     * 试题收藏删除
     */
    public function delFavSave(){
        $username=$this->getCookieUserName();
        $subject=cookie('SubjectId');
        $id=$_POST['id'];
        $userCollect = $this->getModel('UserCollect');
        $buffer=$userCollect->selectData(
            '*',
            'UserName="'.$username.'" AND TestID="'.$id.'" and SubjectID="'.$subject.'" AND `From`=1'
        );
        if($buffer){
            $userCollect->deleteData(
                'CollectID='.$buffer[0]['CollectID']
            );
            $this->setBack('true');
        }else{
            $this->setError('30209',1);
        }
    }

   /**
    * 试题收藏移动
    * @author demo
    * @date 2014年9月22日
    */
    public function updateFavSave(){
        $username=$this->getCookieUserName();
        $subject=cookie('SubjectId');
        $id=$_POST['id'];
        $data='CatalogID='.$_POST['catalogID'];
        $where='UserName="'.$username.'" and TestID="'.$id.'" and SubjectID="'.$subject.'" AND `From`=1';
        $buffer = $this->getModel('UserCollect')->updateData(
            $data,
            $where
        );
        if($buffer===false){
            $this->setError('20223',1);
        }else{
            $this->setBack('true');
        }
    }

    /**
     * 根据试题ID查询试题所在收藏目录
     * @author demo
     * @date 2014年9月24日
     *
     */
    public function getCataByTestID(){
        $username=$this->getCookieUserName();
        $subject=cookie('SubjectId');
        $id=$_POST['id'];
        $collect=$this->getModel('UserCollect');
        $field='CatalogID';
        $data='UserName="'.$username.'" and TestID="'.$id.'" and SubjectID="'.$subject.'" AND `From`=1';
        $buffer=$collect->getCatalogList($field,$data);
        if($buffer){
            $this->setBack($buffer);
        }else{
            $this->setBack('error');
        }
    }

    /**
     * 评论列表
     */
    public function commentList(){
        $userID=$_POST['userid'];
        $curPage=$_POST['curpage'];
        $pageSize=$_POST['pagesize'];
        $id=$_POST['id'];
        if(empty($curPage) || $curPage<1) $curPage=1;
        if(empty($pageSize)) $pageSize=10;
        $subject=cookie('SubjectId');
        $username=$this->getCookieUserName();
        $where=" Status=0 and SubjectID=".$subject;
        if($userID){
            $where.=' and UserName="'.$username.'" ';
        }
        if($id){
            $where.=' and TestID="'.$id.'" ';
        }
        $message =  $this->getModel('Message');
        $count=$message->selectCount($where,'*');
        $pageCount=ceil($count/$pageSize);
        if($curPage>$pageCount) $curPage=$pageCount;
        if($curPage<1 || !is_numeric($curPage)) $curPage=1;
        $buffer = $message->selectData(
            '*',
            $where,
            'ID desc',
            ($curPage-1)*$pageSize.','.$pageSize
        );
        $output[0]=$buffer;
        $output[1]=$count;
        $output[2]=$pageSize;
        $this->setBack($output);
    }

    /**
     * 发表评论
     */
    public function comment(){
        $comment=formatString('changeStr2Html',$_POST['comment']);
        $quesID=$_POST['quesid'];
        $quesScore=$_POST['quesscore'];
        $subject=cookie('SubjectId');
        $username=$this->getCookieUserName();
        $ip=get_client_ip(0,true);

        $data=array();
        $data['UserName']=$username;
        $data['Status']=1;
        $data['SubjectID']=$subject;
        $data['TestID']=$quesID;
        $data['Content']=$comment;
        $data['LoadDate']=time();
        $data['IP']=$ip;
        $data['Reply']='';
        $data['ReplyTime']=0;
        $data['Score']=$quesScore;

        if($this->getModel('Message')->insertData($data)===false){
            $this->setError('20224',1);
        }else{
            $this->setBack('true');
        }
    }

    /**
     * 提取视频
     */
    public function video(){
        $kid=$_REQUEST['kid'];
        $tid=$_REQUEST['tid'];
        if(empty($tid)) $tid=0;
        $klStudyBuffer = $this->getModel('KlStudy')->selectData(
            '*',
            'KlID in ('.$kid.')',
            'StudyID asc'
        );
        if($klStudyBuffer[0]['VideoList']){
            $tmpArrAll=array();
            foreach($klStudyBuffer as $i=>$iKlStudyBuffer){
                $tmpArr=explode('#@#',stripslashes($iKlStudyBuffer['VideoList']));
                foreach($tmpArr as $j=>$jTmpArr){
                    $tmpArrAll[]=$jTmpArr;
                }
            }
            if($tmpArrAll[$tid]){
                $tmpArr2=explode('#$#',$tmpArrAll[$tid]);
                if(!empty($tmpArr2[0])){
                    $video=$tmpArr2[0];
                    $url=$video;
                    if(empty($url)) $this->setError('您访问的资源不存在。',1);

                    $this->assign('url',$url);
                    $this->display('Custom@MicroClass/Play');
//                    $this->setBack($video);
                }else{
                    $video='20111';
                    $this->setError($video,1);
                }
            }
        }
        if(!$video){
            $video='20112';
            $this->setError($video,1);
        }
    }

    /**
     * 验证码显示
     */
    public function verify() {
        R('Common/UserLayer/verify');
    }

    /**
     * 用户注册
     * @author demo
     */
    public function register() {
        if(!C('WLN_OPEN_REGEDIT')){
            $this->setError('20109', 1,U('Index/index'));
        }

        header("Location:http://www.tk.com/Index/Register/index");
        $invit=C('WLN_OPEN_INVIT'); //是否开启邀请码注册

        //判断用户是否在可注册的IP下
        $ip=get_client_ip(0,true);
        $buffer = $this->getModel('UserIp')->selectData(
            'IPID,PUID,LastTime,IfReg',
            'IPAddress='.ip2long($ip)
        );
        $ipUser = $buffer[0];
        if($buffer && $invit){
            if($buffer[0]['LastTime']>time() && $buffer[0]['IfReg']==1){
                $invit=0; //允许不使用邀请码注册
            }else{
                $invit=1;
            }
        }

        if(IS_POST && $_POST['UserName']){
            $username=formatString('stripTags',$_POST['UserName']);
            $phoneCode = $_POST['phoneCode'];
            $password = $_POST['Password'];
            $password1 = $_POST['Password1'];

            if($username=='' || $username=='请填写用户名'){
                $this->setError('20206',1);
            }
            if(!checkString('checkIfPhone',$username)){//验证手机号是否合法
                $this->setError('30211',1);
            }
            if($password=='' || $password1==''){
                $this->setError('30202',1);
            }
            if($password!=$password1){
                $this->setError('30207',1);
            }
            if(strlen($password)<6 || strlen($password)>18){
                $this->setError('30221',1);
            }
            //验证手机验证码是否正确
            $output=R('Common/UserLayer/checkPhoneCode',array($username,$phoneCode,$this->getCookieUserID(),1));

            if($output[0] == 1){
                $this->setError($output[1],1); //返回错误提示
            }

            //在检查手机验证码是否正确的函数里已验证过是否重复
            //判断用户名是否重复
            $user=$this->getModel('User');
            $buffer=$user->checkUser($username);
            if($buffer){
                $this->setError($buffer,1);
            }

            //检查用户名合法
            $backStr=$user->NameFilter($username);

            if($backStr['errornum']!='success'){
                $this->setError($backStr['errornum'],1,'',$backStr['replace']);
            }

            if($invit){
                $invitName=formatString('stripTags',$_POST['InvitName']);
                $buffer = $this->getModel('UserInvitation')->selectData(
                    '*',
                    'InvitName="'.$invitName.'"'
                );

                if(!$buffer){
                    $this->setError('20225',1);
                }
                if($buffer[0]['IfUsed']){
                    $this->setError('20226',1);
                }
                $this->getModel('UserInvitation')->updateData(
                    array('UserName'=>$username,'UsedTime'=>time(),'IfUsed'=>1,'Status'=>1),
                    'InvitID='.$buffer[0]['InvitID']
                );
            }
            //获取编号
            $autoInc=$this->getModel('AutoInc');
            $orderNum=$autoInc->getOrderNum();

            $data=array();
            $data['UserName']=$username;
            $data['Password']=md5($username.$password);
            $data['RealName']='';
            $data['Sex']=0;
            $data['Phonecode']=$username;
            $data['Email']='';
            $data['Address']='';
            $data['PostCode']='';
            $data['LoadDate']=time();
            $data['LastTime']=time();
            $data['Logins']=0;
            $data['Whois']=1;
            $data['LastIP']=$ip;
            $data['CheckPhone']=1; //已验证手机号
            $data['SaveCode']=$user->saveCode();
            $data['OrderNum']=$orderNum;
            $result = $user->insertData($data);
            if($result){
                $data['UserID']=$result;
                //注册时添加指定分组  2015-9-2
                $this->getModel('UserGroup')->addDefaultGroupAtRegistration(
                    $result,
                    $ipUser
                );
                $this->getModel('RegisterLog')->insertRegisterLog(
                    $result,
                    $ip
                );
                R('Common/UserLayer/setHomeLoginCookie',array($data));
                $user->changeUserLoginInfo($data['UserID'],$data['Logins'],$data['SaveCode']);
            }
            $this->setBack('success');
        }
        $this->assign('invit', $invit); //开启邀请码
        $this->display();
    }

    /**
     * 登录
     */
    public function login() {
        $username=formatString('stripTags',$_POST['UserName']);
        $ifSave=$_POST['ifsave'];
        $password=$_POST['Password'];

        if (empty ($username)) {
            $this->setError('30201',1);
        }
        if (empty ($password)) {
            $this->setError('30202',1);
        }

        //if (md5($_POST['verify']) != session('verify')) {
        //    exit ('<font color="#ff0000">验证码有误！</font>');
        //}
        $output = $this->getApiUser('User/login',$username,$password,$ifSave);
        // $output = R('Common/UserLayer/login',array($username,$password,$ifSave));
        if($output[0]==0){
            $this->setError($output[1],1);
        }
        //组卷中心登录 用户经验累加
        $this->getModel('UserExp')->addUserExpAll($output[1]['UserName'],'login');

        $this->userLog('用户登录', '用户【' . $output[1]['UserName'] . '】登录组卷系统',$output[1]['UserName']);
        $this->setBack('success');
    }


    /**
     * 重置密码
     */
    public function setPass() {
        $checkStr=$_REQUEST['k'];
        $userID=$_REQUEST['u'];
        $checkStr1=md5($userID.date('YmdH',time()).C('REG_KEY'));
        $checkStr2=md5($userID.date('YmdH',time()-3600).C('REG_KEY'));
        //检测验证码
        if($checkStr!=$checkStr1 and $checkStr!=$checkStr2){
            if(IS_POST) $this->setError('30228',1);
            $this->setError('30228','',U('User/Index/getPassword'));
        }
        $user=$this->getModel('User');
        $userName=$user->getInfoByID($userID,'UserName')['UserName'];
        if(IS_POST){
            $password=$_POST['p'];
            $password1=$_POST['p2'];

            if($password=='' || $password1==''){
                $this->setError('30202',1);
            }
            if($password!=$password1){
                $this->setError('30207',1);
            }
            if(strlen($password)<6 || strlen($password)>18){
                $this->setError('30221',1);
            }

            //重置密码

            $data=array();
            $data['SaveCode'] = $user->saveCode();
            $data['Password'] = md5($userName.$password);
            $user->updateData(
                $data,
                'UserID='.$userID
            );
            $this->userLog('用户登录', '用户【' . $userName . '】通过找回密码修改密码',$userName);
            $this->setBack('success');
        }
        $this->assign('userName',$userName);
        $this->display();
    }

    /**
     * 退出
     */
    public function loginOut() {
        $username=$this->getCookieUserName();
        if($username){
            $this->setCookieUserName( NULL);
            $this->setCookieTime( NULL);
            $this->setCookieCode( NULL);
            $this->setCookieUserID( NULL);
            $this->userLog('用户登录', '用户【' . $username . '】退出组卷系统',$username);
        }
        header('location:/User/Index/passport');
        exit;
        $this->showSuccess('成功退出系统！', U('Index/index?u='.$_REQUEST['u']));
    }

    /**
     * 下载添加学生excel模板文件
     */
    public function excelStudent() {
        header('Location:'.C('WLN_HTTP').'/fl.xls');
    }
    /**
     * 获取服务条款HTML
     * @author demo
     */
    public function getServiceTerm(){
        $getService = R('Common/UserLayer/getServiceTerm');
        $this->setBack($getService);
    }
    /**
     * ajax提交用户下载反馈
     * @author demo
     */
    public function addFeedBack(){
        if($_POST['typeVal'] && $_POST['content']){
            //先写入DocSave表返回ID作为feedback表的OpenStyle
            //写入存档
            $cookieStr=$_POST['cookieContent'];
            $data=array();
            $data['SaveName']=$_POST['paperName'];
            $data['SavePwd'] =0;
            $data['SubjectID']=cookie('SubjectId');
            $data['CookieStr']=$cookieStr;
            $data['testList']=$_POST['testList'];
            $data['LoadTime']=time();
            $data['StyleState']=2;
            $data['UserName']=$this->getCookieUserName();
            $data['TestNum']=$this->getCookieTestNum($cookieStr);
            $docSaveID = $this->getModel('DocSave')->insertData($data);
            if($docSaveID){
                $backData['OpenStyle']=$docSaveID;
                $backData['Content']='【'.$_POST['typeVal'].'】'.$_POST['content'];
                $backData['Status']=0;
                $backData['UserName']=$this->getCookieUserName();
                $backData['LoadTime']=time();
                $backData['Style']=1;//留言反馈
                $backData['From']='home';
                $result=$this->getModel('Feedback')->insertData($backData);

                if($result){
                    $res['status']=1;
                    $this->setBack($res);
                }
            }
        }
    }
    /**
     * 获取cookie中的试题数量
     */
    protected function getCookieTestNum($cookieStr){
        $testNum=0;
        if(is_numeric($cookieStr)){//作业时$cookieStr为作业ID
            return $testNum;
        }
        $tmpStr1=explode('@#@',$cookieStr);
        foreach($tmpStr1 as $i=>$iTmpStr1){
            $tmpStr2=explode('@$@',$iTmpStr1);
            if(count($tmpStr2)==7 && !empty($tmpStr2[5])){
                $tmpStr3=explode(';',$tmpStr2[5]);
                foreach($tmpStr3 as $j=>$jTmpStr3){
                    $tmpStr4=explode('|',$jTmpStr3);
                    $testNum+=$tmpStr4[1];
                }
            }
        }
        return $testNum;
    }

    /**
     * 获取系统生成的用户昵称
     * @author demo
     */
    public function getNickname(){
        $nickName = R('Common/UserLayer/produceNickname');
        $this->setBack($nickName);
    }

}