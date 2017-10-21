<?php
/**
 * @author demo
 * @date 2015年5月8日
 * @updated
 */
/**
 * 导学案类，用于处理导学案相关操作
 */
namespace Guide\Controller;
use Common\Controller\DefaultController;
class CaseController extends DefaultController {
    /**
     * 导学案模板首页
     * @author demo
     */
    public function index() {
        //载入题型
        $types = SS('typesSubject');
        $menuSubject=SS('menuSubject');
        //处理缓存，因栏目ID导致的排序问题
        foreach($menuSubject as $i=>$iMenuSubject){
            foreach($menuSubject[$i] as $j=>$jMenuSubject){
                $newMenuSubject[$i]['o'.$jMenuSubject['MenuID']]=$jMenuSubject;
            }
        }
        $menuSubject=json_encode($newMenuSubject, JSON_UNESCAPED_UNICODE);
        $types = json_encode($types, JSON_UNESCAPED_UNICODE);
        $forumMsg = json_encode($this->getModel('CaseMenu')->getCaseForum(), JSON_UNESCAPED_UNICODE);
        if(!$types){
            $types=0;
        }
        //载入学科
        $subject=array();
        $tmp = SS('subject');
        foreach($tmp as $i=>$iTmp){
            $subject[$i]['SubjectName']=$iTmp['SubjectName'];
        }
        $subject=json_encode($subject, JSON_UNESCAPED_UNICODE);
        if(!$subject){
            $subject=0;
        }
        //组卷方式数组chooseTest  0按系统模板组卷、1按我的模板组卷、2自定义模板、3编辑系统模板、4编辑我的模板、
        /*$testStyle=array(array('val'=>'0','styleName'=>'按系统模板组卷'),
                         array('val'=>'1','styleName'=>'按我的模板组卷'),
                         array('val'=>'2','styleName'=>'编辑系统模板'),
                         array('val'=>'3','styleName'=>'编辑我的模板'),
                         array('val'=>'4','styleName'=>'自定义模板'));     */

        $testStyle=array(
            array('val'=>'3','styleName'=>'系统模板'),
            array('val'=>'4','styleName'=>'我的模板'),
            array('val'=>'2','styleName'=>'自定义模板')
            );

        $username=$this->getCookieUserName();
        $key=md5(C('DOC_HOST_KEY').$username.date("Y.m.d",time()));

        //选题方式
        $testStyle=json_encode($testStyle);
        $this->assign('testStyle',$testStyle); //组卷方式
        $this->assign('Subject',$subject); //学科
        $this->assign('Types', $types); //载入考试类别
        $this->assign('menuSubject', $menuSubject); //载入考试类别
        $this->assign('forumMsg', $forumMsg); //载入考试类别
        $this->assign('key', $key); //页面标题
        $this->assign('pageName','导学案选题'); //学科

        $this->display();
    }

    /**
     * 我的知识点管理
     * @author demo
     */
    public function myLoreManager(){
        $chapter = $this->getData(
            array(
                'style' => 'chapter',
                'subjectID' => cookie('SubjectId')
            )
        );
        $subjectID = cookie('SubjectId');
        $modules = $this->getModel('CaseMenu')->getCaseForum();
        $menuSubject = SS('menuSubject')[$subjectID];
        $menus = array();
        foreach($menuSubject as $value){
            foreach($modules as $k=>$v){
                if($k == $value['ForumID']){
                    $menus[$k]['otherName'] = $v['otherName'];
                    $menus[$k]['name'] = $v['name'];
                    $menus[$k]['content'][] = $value;
                }
            }
        }
        $this->assign('modulesJSON', json_encode($modules));
        $this->assign('menusJSON', json_encode($menus));
        $this->assign('chapter', json_encode($chapter));
        $this->assign('pageName','我的知识点管理');
        $this->display();
    }

    /**
     * 我的知识点列表数据
     * @author demo
     */
    public function getLoreList(){
        $page = $_POST['page'];
        if(!$page){
            $page = 1;
        }
        //查询知识信息
        $subjectid = $_POST['subjectid'];
        $params = array(
            'id' => $_POST['id'],
            'menuid' => $_POST['menuid'],
            'chapterid' => $_POST['chapterid'],
            'UserName' => $this->getCookieUserName(),
            'subjectid' => $subjectid,
            'page' => $page
        );
        $result = array();
        $data = $this->getModel('CaseCustomLore')->getListByParams($params);
        $result = $data['result'];
        unset($data['result']);
        $subjects = SS('subject');


        $parent=SS('chapterParentPath');// 获取已选中的章节路径
        $self=SS('chapterList');

        $param=array();
        $param['style']='chapterList';
        $param['parent']=$parent;
        $param['self']=$self;
        $param['return']=2;
        foreach($result as $key=>$value){
            $param['ID']=$value['ChapterID'];
            $chapterName = $this->getData($param)[0]['ChapterName'];
            if(!$chapterName){
                $chapterName = '无';
            }
            $result[$key]['Lore'] = formatString('IPReturn',stripslashes_deep($result[$key]['Lore']));
            $result[$key]['Answer'] = formatString('IPReturn',stripslashes_deep($result[$key]['Answer']));
            $result[$key]['SubjectName'] = $subjects[$value['SubjectID']]['SubjectName'];
            $result[$key]['ChapterName'] = $chapterName;
            $result[$key]['AddTime'] = date('Y-m-d H:i:s', $result[$key]['AddTime']);
        }
        $this->setBack(array('result'=>$result, 'page'=>$data['page']));
    }

    /**
     * 保存我的知识
     * @author demo
     */
    public function saveLore(){
        $id = (int)$_POST['LoreID'];
        $username = $this->getCookieUserName();
        $data = array(
            'Lore' => formatString('IPReplace',$_POST['Lore']),
            'Answer' => formatString('IPReplace',$_POST['Answer']),
            'MenuID' => $_POST['MenuID'],
            'ChapterID' => $_POST['ChapterID'],
            'SubjectID' => cookie('SubjectId'),
            'UserName' => $username,
        );
        $result = $this->getModel('CaseCustomLore')->saveLore($data, $id);
        if($result){
            $content = ($id > 0) ? "修改知识管理LoreID为【{$id}】的信息" : '新增知识管理信息';
            $this->userLog('组卷知识管理', $content, $username);
            if(empty($id))
                $this->getModel('MissionHallRecords')->finishTask($this->getCookieUserID());//任务完成
            $this->setBack('success');
        }
        $error = $id > 0 ? '30307' : '30311';
        $this->setError($error, 1);
    }

    /**
     * 保存在导学案模板修改的知识
     * @author demo
     */
    public function saveTempLore(){
        if(strrpos($_POST['LoreID'],'l')){//修改系统试题则新增
            $id=0;
        }else{//否则就更新
            $id = (int)$_POST['LoreID'];
        };
        $username = $this->getCookieUserName();
        $data = array(
            'Lore' => formatString('IPReplace',$_POST['Lore']),
            'Answer' => formatString('IPReplace',$_POST['Answer']),
            'MenuID' => $_POST['MenuID'],
            'ChapterID' => $_POST['ChapterID'],
            'SubjectID' => cookie('SubjectId'),
            'UserName' => $username,
        );
        $result = $this->getModel('CaseCustomLore')->saveLore($data, $id);
        if($result){
            $content = '新增知识管理信息';
            $this->userLog('组卷知识管理', $content, $username);
            $res[0]='success';
            $res[1]=$this->getModel('CaseCustomLore')->getId();
            $this->setBack($res);
        }
        $error = $id > 0 ? '30307' : '30311';
        $this->setError($error, 1);
    }
    /**
     * 删除我的知识
     * @author demo
     * @date
     */
    public function deleteMyLore(){
        $id = $_POST['id'];
        if(!$id){
            $this->setError('30301', 1);
        }
        $result = $this->getModel('CaseCustomLore')->delLore($id);
        if(!$result){
            $this->setError('30302', 1);
        }
        $this->userLog('组卷知识管理', '删除我的知识【编号'.$id.'】', $this->getCookieUserName());
        $this->setBack('success');
    }

    /**
     * 编辑我的知识
     * @author demo
     * @date 2015-5-22
     */
    public function editMyLore(){
        $id = $_POST['id'];
        $params = array(
            'id' => $id,
            'subjectid' => cookie('SubjectId')
        );
        if($_POST['ifSystem']){
            $this->setBack(array('系统知识暂不支持编辑'));
            /**
            $params['db']='zj_case_lore';
            $params['join']=' ';
            $params['name']='Admin';
            $params['userName']=$this->getCookieUserName();
            $params['fields']='`lore`.LoreID,Lore,Answer,SubjectID,ChapterID,MenuID';
            $result = $this->getModel('CaseLore')->getListByParams($params);
            $result = $result['result'][0];
            //对系统知识里的路径进行转换
            $caseLore = $this->getModel('CaseLore');
            $result['Lore']=$caseLore->changeSrcValue($result['Lore'],C('WLN_DOC_HOST'));
            $result['Answer']=$caseLore->changeSrcValue($result['Answer'],C('WLN_DOC_HOST'));
            **/
        }else {
            $params['UserName'] = $this->getCookieUserName();
            $result = $this->getModel('CaseCustomLore')->getListByParams($params);
            $result = $result['result'][0];
        }
        $result['Lore'] = formatString('IPReturn',stripslashes_deep($result['Lore']));
        $result['Answer'] = formatString('IPReturn',stripslashes_deep($result['Answer']));
        $chapter = $this->getData(array(
            'style'=>'chapterList',
            'ID' => $result['ChapterID'],
            'return'=>2
        ));
        $result['showChapterZone'] = $chapter[0]['ChapterName'];
        $this->setBack($result);
    }

    /**
     * 获取试题类型；
     * @return array
     * @author demo
     */
    protected function getDocAttr() {
        $output = array();
        $buffer = SS('docType');
        if($buffer){
            $j=0;
            foreach($buffer as $i=>$iBuffer){
                $output[$j]['TypeID']=$i;
                $output[$j]['TypeName']=$iBuffer['TypeName'];
                $output[$j]['GradeList']=$iBuffer['GradeList'];
                $j++;
            }
        }
        return $output;
    }
   /**
    * ajax根据条件获取对应模板；
    * @return array
    * @author demo ,
    */
    public function getTemplateList(){
        $subjectID=$_POST['subjectID'];
        //choosetypeid  0 按系统模板组卷  1按我的模板组卷 2 自定义模板
        if($_POST['check']==1){
            $bool=$this->getApiCommon('Power/homeCheckThisPower','Case/saveSysTemplateList');
            if($bool===true){
                $this->setBack(1);
            }else{
                $this->setBack('');
            }
        }
        $chooseTypeID=$_POST['choosetypeid'];
        $chapterID=$_POST['chapterID'];
        $username = $this->getCookieUserName();
        $where='1=1';
        if($chooseTypeID=='0' || $chooseTypeID=='3'){ //判断是系统模板
            $where.= ' and SubjectID='.$subjectID.' and IfSystem=1 and ChapterID='.$chapterID;
        }else if($chooseTypeID=='1' || $chooseTypeID=='4'){ //判断是个人模板
            $where.= ' and SubjectID='.$subjectID.' and IfSystem=0 and UserName="'.$username.'" and ChapterID='.$chapterID;
        }
        $tempArr = $this->getModel('CaseTpl')->selectData(
            'TplID,UserName,SubjectID,TempName,ChapterID,Content,IfSystem,AddTime,UpdateTime',
            $where,
            'OrderID Desc,AddTime Desc'
        );
        foreach($tempArr as $i=>$iTempArr){
            $tempArr[$i]['UpdateTime']=date('Y.n.j',$iTempArr['UpdateTime']);
        }
        $tempRes['content']=$tempArr;
        $this->setBack($tempRes);
    }
    /**
     * ajax根据模板ID获取模板内容；
     * @return json
     * @author demo
     */
    public function getTemplateByID(){
        //序列化  反序列化 json
        $arr['status']='1';
        $username = $this->getCookieUserName();
        $data['TplID'] = $_POST['mbid'];
        $where = "TplID=".$data['TplID'];
        $result = $this->getModel('CaseTpl')->selectData(
            'Content,IfSystem,UserName',
            $where
        );
        if($username!=$result[0]['UserName'] and $result[0]['IfSystem']!=1){
            $this->setError('30806',1);
        }else if($result){
            $this->setBack(unserialize($result[0]['Content']));
        }
    }
    /**
     * ajax根据最终章节ID 获取父类PathID
     * @author demo
     */
    public function getChapterList(){
        $captID=$_POST['chapterID'];
        $subjectID=$_POST['subjectID'];
        $output=array();
        if($captID==-1){
            $output[0]=0;
            $output[1]=$this->getdata(array('style'=>'chapter','subjectID'=>$subjectID,'return'=>2));
        }else{
            $output=$this->getdata(array('style'=>'chapterParentList','subjectID'=>$subjectID,'id'=>$captID,'return'=>2));
        }
        $this->setBack($output);
    }
    /**
     * 保存模板
     * @author demo
     */
    public function saveTemp(){
        //获取数据
        if(empty($_POST['content'])) {
            $this->setError('30301',1);
        }
        $userName=$this->getCookieUserName();
        if(empty($_POST['tplID'])){
            $doubleName = $this->getModel('CaseTpl')->selectData(
                'TplID',
                'TempName="'.$_POST['tName'].'" and UserName="'.$userName.'" and ChapterID='.$_POST['content']['chapterID']
            );
            if($doubleName){ //判断名称是否存在
                $this->setError('30807',1);
            }
        }
        $content=$_POST['content'];
        $caseMsg=$this->caseDataTotal($content);

        $data['UserName']=$userName;
        $data['ChapterID']=$content['chapterID'];
        $data['SubjectID']=$content['subjectID'];
        $data['TempName']=$_POST['tName'];
        $data['Content'] = serialize($content);
        $data['LoreNum']=$caseMsg['loreTotal'];
        $data['testNUm']=$caseMsg['testTotal'];
        //是替换还是新增
        if(empty($_POST['tplID'])){ //判断模板ID是否存在，判定是新增还是替换
            $data['AddTime']=time();
            $data['UpdateTime']=time();
            $result = $this->getModel('CaseTpl')->insertData(
                $data
            );
        }else{
            $tid=$_POST['tplID'];
            $data['UpdateTime']=time();
            $result = $this->getModel('CaseTpl')->updateData(
                $data,
                'TplID='.$tid
            );
        }
        if($result===false){
            $res['msg']='保存失败！';
        }else{
            $res['status']=1;
            $res['msg']='保存成功!';
        }
        $this->setBack($res);

    }
    /**
     * 根据栏目属性获取知识
     * @author demo
     */
    public function ajaxGetLore(){
        $ifSystem=$_POST['ifSystem'];//系统还是个人
        //章节ID
        $chapterID=$_POST['chapterID'];
        //栏目ID
        $menuID=$_POST['menuID'];
        //当前页
        $page=$_POST['page'];
        //学科ID
        $subjectID=$_POST['subjectID'];
        //用户名
        $userName=$this->getCookieUserName();

        if(!$subjectID){
            //返回首页提示选择学科
            $this->setError('30508');
        }
        if(!$chapterID){
            //请选择章节
            $this->setError('23003');
        }
        if(!$menuID){
            //栏目ID不能为空
            $this->setError('30308');//操作失败
        }
        if($ifSystem==1){
            //获取系统知识
            $db='zj_case_lore';
            $field='lore.LoreID as testid,Lore as test,Answer,Equation,attr.AddTime as firstloadtime,attr.DocID,DocName as docname';
            $join=' LEFT JOIN zj_case_lore_doc doc ON doc.DocID=attr.DocID ';
        }else{
            //获取个人知识
            $db='zj_case_custom_lore';
            $field='lore.LoreID as testid,Lore as test,Answer,Equation,attr.AddTime as firstloadtime';
            $join=' ';
        }
        $params = array(
            'id' => $_POST['id'],
            'menuid' => $menuID,
            'db'     => $db,
            'fields' => $field,
            'chapterid' => $chapterID,
            'join'    =>$join,
            'subjectid' => $subjectID,
            'page' => $page
        );
        if($ifSystem!=1){
            $params['userName']=$userName;
            $params['custom']=1;
        }
        $host = C('WLN_DOC_HOST');
        $caseLore = $this->getModel('CaseLore');
        $data =$caseLore->getListByParams($params);
        $result = $data['result'];
        if($result){
            foreach($result as $i=>$iResult){
                if($ifSystem==0){
                    $result[$i]['testid']='u'.$result[$i]['testid'];//个人试题标识
                }else{
                    $result[$i]['testid']='l'.$result[$i]['testid'];//系统试题标识
                }
                if($host){
                    $result[$i]['test']=R('Common/TestLayer/strFormat',array($iResult['test']));
                    $result[$i]['Answer'] = R('Common/TestLayer/strFormat',array($iResult['Answer']));
                }
                $result[$i]['test'] = formatString('IPReturn',stripslashes_deep($result[$i]['test']));
                $result[$i]['Answer'] = formatString('IPReturn',stripslashes_deep($result[$i]['Answer']));
                $result[$i]['firstloadtime']=date('Y-m-d H:i:s',$iResult['firstloadtime']);
                $result[$i]['testnum']=1;
            }
        }
        $option[0]=$result;
        $option[1]=$data['page'][0];
        $option[2]=$data['page'][1];
        $this->setBack($option);
    }

    /**
     * 通过个人知识ID获取知识内容
     * @author demo
     */
    public function ajaxGetLoreByID(){
        $id = $_POST['id'];
        $result = $this->getModel('CaseCustomLore')->selectData(
            'Lore,Answer',
            'LoreID='.$id
        );
        if($result){
            $result[0]['Lore'] = formatString('IPReturn',stripslashes_deep($result[0]['Lore']));
            $result[0]['Answer'] = formatString('IPReturn',stripslashes_deep($result[0]['Answer']));
            $res[0]='success';
            $res[1]=$result[0];
            $this->setBack($res);
        }
        $this->setError('30308');
    }

    /**
     * 根据分解后的cookie参数，获取对应的试题ID题文内容
     * 数据结构如下：
    cookieContent={
    'chapterID':'1',
    'tempType':'1',
    'tplID':'1',
    'tempName':'第一课时 集合的含义',
    'tempDesc':'班级:__________姓名:__________设计人__________日期__________',
    'forum':{
            'tempForum1':{
            '0':'课前预习',
            '1':'预习案',
            '2':{
                '0':{'ifAnswer':'0','ifTest':'0','menuContent':'27|1|0;28|1|0','menuName':'自学目标','menuOrder':'tempMenu1_1'},
                '1':{'ifAnswer':'0','ifTest':'0','menuContent':'81|0|0;256|0|0','menuName':'自学引导','menuOrder':'tempMenu1_2'}
                }
            },
           'tempForum2':{
            '0':'知识拓展',
            '1':'探究案',
            '2':{
                '0':{'ifAnswer':'1','ifTest':'1','menuContent':'127|0|0','menuName':'自主梳理','menuOrder':'tempMenu2_1'}

                }
            },
            'tempForum3':{
            '0':'课后练习',
            '1':'训练案',
            '2':{
                '0':{'ifAnswer':'0','ifTest':'1','menuContent':'90|1|2','menuName':'基础巩固','menuOrder':'tempMenu3_1'}

                }
            }
     }
}
     * @param string $caseContent json数据
     * @author demo
     */
    public function buildTestContent(){
        $content=$_POST['content'];
        $ifShowNum=$_POST['ifShowNum'];
        $data = array(
            'UserID' => $this->getCookieUserID(),
            'Type' => 2,
            'AddTime' => time()
        );
        $this->getModel('TestpaperCenterLog')->insertData($data);
        if($content){
            $caseTpl = $this->getModel('CaseTpl');
            $testIdStr=$caseTpl->reSetTestIdAndLoreId($content); //获取模板json数据中的知识点ID及试题ID
            $testResult=$caseTpl->getCaseAndTestFromIndex($testIdStr,array('testid','testold','answerold'),array('LoreID','Lore','Answer')); //获取知识和试题数据

            if(!empty($testResult)){
                //根据试题ID查询出来的试题内容不为空
                $forum=$content['forum'];
                $forum=$this->buildCaseHtml($forum,$testResult,$ifShowNum);
                $content['forum']=$forum;
                $content['status']='success';
            }else{
                $content='<b>没有查询到该导学案相关数据！</b>';
            }
        }else{
            $content='<b>没有查询到该导学案相关数据！</b>';
        }
        $this->setBack($content);
    }
    /**
     * 下载导学案
     * @author demo
     */
    public function create() {
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
            $this->setError('30101',1); //验证码不正确，请重新填写。（不用区分大小写）
        }
        //判断是否有权限下载
        $userName=$this->getCookieUserName();
        if($key!=md5(C('DOC_HOST_KEY').$userName.date("Y.m.d",time()))){
            $this->setError('30803',1); //验证错误！请刷新页面后重试。
        }

        $param=array();
        $param['subjectID']=$subjectID;
        $param['cookieStr']=$cookieStr;
        $param['isSaveRecord']=$isSaveRecord;
        $param['ifShare']=$ifShare; //是否分享
        $param['docVersion']=$docVersion;
        $param['paperSize']=$paperSize;
        $param['paperType']=$paperType;
        $param['backType']=0; //是否仅返回路径
        $param['downStyle']=3; // 区分下载作业还是试卷 答题卡4 导学案3 作业2，试卷1

        //根据参数获取下载链接
        $caseTpl = $this->getModel('CaseTpl');
        $path=$caseTpl->getDownUrlByCookie($param,$userName,cookie('SubjectId'));

       if($path[0]=='false'){
            $this->setError($path['msg'],1);
        }else{
            /*
            //记录组卷次数
            $this->getModel('User')->conAddData(
                'Times=Times+1',
                'UserName="'.$username.'"'
            );
            //记录下载试题次数
            $testDown = $this->getModel('TestDown');
            //此处过滤掉自有题库的标识字符串，保证写入正确  2015-4-21

            $testList = rtrim(preg_replace('/'.\Common\Model\TestQueryModel::DIVISION.'\d+,?/s', '', $testList),',');
            $testDown->setDownTime($testList);
            unset($testDown);
            */
            $this->getModel('MissionHallRecords')->finishTask($this->getCookieUserID());//任务完成
            $this->setBack('success#$#'.$path['msg']);
        }
    }
    /**
     * 第三步骤，发布导学案
     * @author demo
     */
    public function addWorkTpl(){
        $totalMsg=$this->caseDataTotal($_POST['content']);
        $tplData['TempName']=$_POST['tplName'];
        $tplData['IfSystem']=2;//保存为作业
        $tplData['ChapterID']=$_POST['ChapterID'];
        $tplData['SubjectID']=cookie('SubjectId');
        $tplData['AddTime']=time();
        $tplData['Content']=serialize($_POST['content']); //需要修改
        $tplData['LoreNum']=$totalMsg['loreTotal'];
        $tplData['testNum']=$totalMsg['testTotal'];
        $tplData['UserName']=$this->getCookieUserName();
        if($tplData){
            $result = $this->getModel('CaseTpl')->insertData(
                $tplData
            );
            if($result){
                $data['status']='success';
                $data['msg']='导学案发布成功！';
            }else{
                $data['status']='false';
                $data['msg']='发布失败！';
            }
            $this->setBack($data);
        }
    }

    /**
     * 保存导学案替换系统模板方法,该方法需要判断，保存模板时，是否显示系统模板
     * @author demo
     */
    public function saveSysTemplateList(){
        if(empty($_POST)){
            $this->setError('30301',1);
        }
        $content=$_POST['content'];
        $tplID=$_POST['tplID'];
        $caseMsg=$this->caseDataTotal($content);
        $tmpData = array();
        $tmpData['TempName'] = $content['tempName'];
        $tmpData['UserName'] = $this->getCookieUserName();
        $tmpData['IfSystem'] = 1;
        $tmpData['LoreNum']=$caseMsg['loreTotal'];
        $tmpData['TestNum']=$caseMsg['testTotal'];
        $tmpData['Content'] =serialize($content);//模板数据序列化后存入
        $tmpData['AddTime'] = time();
        $tmpData['UpdateTime'] = time();
        $tmpData['SubjectID'] = $content['subjectID'];
        $tmpData['OrderID']='99';
        if($tplID != ''){
            $where = 'TplID='.$tplID;
            $buffer = $this->getModel('CaseTpl')->selectData(
                'IfSystem',
                $where
            );
            if($buffer[0]['IfSystem']==1){ //判断是否是系统模版
                $tmpData['TplID']=$tplID;
                $tmpData['IfSystem'] = 1;
                $str=$this->getUserPowerByTag('Case/saveSysTemplateList');
                if($str !=='all'){
                    $return="30808";
                    $this->setError($return,1);
                }else{
                    $result = $this->getModel('CaseTpl')->updateData(
                        $tmpData,
                        'TplID='.$tplID
                    );
                    if($result){
                        $data['status']='success';
                        $data['msg']='系统模板替换成功！';
                    }else{
                        $data['status']='false';
                        $data['msg']='系统模板替换失败！';
                    }
                    $this->setBack($data);
                }
            }
        }
    }


    /**
     * 根据ajax提交的json内容，统计导学案中的知识点数量及试题数量
     * @param array $caseMsg 导学案json数据
     * @return array   试题数量/知识点数量
     * @author demo
     */
    private function caseDataTotal($caseMsg){
        $caseData=$caseMsg['forum'];
        $testTotal=0;
        $loreTotal=0;
        if($caseData){
            foreach($caseData as $i=>$iCaseData){
                foreach($caseData[$i][2] as $j=>$jCaseData){
                    if($caseData[$i][2][$j]['ifTest']=='0' && $caseData[$i][2][$j]['menuContent']!=''){
                        $loreTotal+=count(explode(';',$caseData[$i][2][$j]['menuContent']));
                    }else if($caseData[$i][2][$j]['ifTest']=='1' && $caseData[$i][2][$j]['menuContent']!=''){
                        $testArr=explode(';',$caseData[$i][2][$j]['menuContent']);
                        foreach($testArr as $k=>$kTestArr){
                            $testNum=explode('|',$kTestArr);
                            if($testNum[1]==0){
                                $testNum[1]=1;
                            }
                            $testTotal+=$testNum[1];
                        }


                    }
                }
            }
            $result['testTotal']=$testTotal;
            $result['loreTotal']=$loreTotal;
            return $result;
        }
    }

    /**
     * PHP构造word页面内容
     * @param array $forum json模板数据
     * @param array $testResult 模板中的试题数据
     * 数据结构如下：
    cookieContent={
    'chapterID':'1',
    'tempType':'1',
    'tplID':'1',
    'tempName':'第一课时 集合的含义',
    'tempDesc':'班级:__________姓名:__________设计人__________日期__________',
    'forum':{
        'tempForum1':{
            '0':'课前预习',
            '1':'预习案',
            '2':{
                '0':{'ifAnswer':'0','ifTest':'0','menuContent':'27|1|0;28|1|0','menuName':'自学目标','menuOrder':'tempMenu1_1'},
                '1':{'ifAnswer':'0','ifTest':'0','menuContent':'81|0|0;256|0|0','menuName':'自学引导','menuOrder':'tempMenu1_2'}
            }
            },
        'tempForum2':{
            '0':'知识拓展',
            '1':'探究案',
            '2':{
                '0':{'ifAnswer':'1','ifTest':'1','menuContent':'127|0|0','menuName':'自主梳理','menuOrder':'tempMenu2_1'}

            }
        },
        'tempForum3':{
            '0':'课后练习',
            '1':'训练案',
            '2':{
                '0':{'ifAnswer':'0','ifTest':'1','menuContent':'90|1|2','menuName':'基础巩固','menuOrder':'tempMenu3_1'}

                }
            }
        }
    }
     * @return array
     * @author demo
     */
    private function buildCaseHtml($forum,$testResult,$ifShowNum=0){
        $menuIDArray=array(); //记录栏目id，相同栏目id的序号顺延
        $caseMenu=SS('caseMenu');
        foreach($forum as $i=>$iForum){
            foreach($iForum[2] as $j=>$jForum){

                //作为序号
                if(empty($menuIDArray[$jForum['menuID']])){
                    $menuIDArray[$jForum['menuID']]=1;
                }
                $testNum=$menuIDArray[$jForum['menuID']];//试题序号

                if($jForum['ifTest']==0){  //查知识表
                    if($jForum['menuContent']==''){
                        $forum[$i][2][$j]['testContent']=''; //知识为空
                    }else{
                        $testArr=explode(';',$jForum['menuContent']);
                        foreach($testArr as $k=>$kTestArr){
                            $testMsg=explode('|',$kTestArr);
                            $remark='remark'.$testMsg[0];
                            if($testMsg[1]=='0'){
                                $keyID='l'.$testMsg[0];
                            }else{
                                $keyID='u'.$testMsg[0];
                            }

                            if($testResult[$keyID]){
                                if($testMsg[2]>1 || $ifShowNum) $testResult[$keyID]['Lore']=R('Common/TestLayer/changeTagToNum',array($testResult[$keyID]['Lore'],$testNum,$caseMenu[$jForum['menuID']]['NumStyle']));
                                $forum[$i][2][$j]['testContent'][$remark]['Lore']=$testResult[$keyID]['Lore'];
                                $forum[$i][2][$j]['testContent'][$remark]['Answer']=$testResult[$keyID]['Answer'];
                                $forum[$i][2][$j]['testContent'][$remark]['testNum']=$testMsg[2];
                                $forum[$i][2][$j]['testContent'][$remark]['system']=$testMsg[1];

                                $testNum+=($testMsg[2]<2 ? 1 : $testMsg[2]);
                            }else{
                                $forum[$i][2][$j]['testContent'][$remark]['Lore']=""; //未查询到该知识点
                                $forum[$i][2][$j]['testContent'][$remark]['testNum']='1';
                                $forum[$i][2][$j]['testContent'][$remark]['Answer']='';
                                $forum[$i][2][$j]['testContent'][$remark]['system']=$testMsg[1];
                            }
                        }
                    }
                }else{ //查试题表
                    if($jForum['menuContent']==''){
                        $forum[$i][2][$j]['testContent']=''; //试题内容为空
                    }else{

                        $testArr=explode(';',$jForum['menuContent']);
                        foreach($testArr as $k=>$kTestArr){
                            $testMsg=explode('|',$kTestArr);
                            $remark='remark'.$testMsg[0];

                            if($testMsg[1]=='0'){
                                $keyID=$testMsg[0];
                            }else{
                                $keyID='c'.$testMsg[0];
                            }

                            if($testResult[$keyID]){
                                if($testMsg[2]>1 || $ifShowNum) $testResult[$keyID]['testold']=R('Common/TestLayer/changeTagToNum',array($testResult[$keyID]['testold'],$testNum,$caseMenu[$jForum['menuID']]['NumStyle']));

                                $forum[$i][2][$j]['testContent'][$remark]['Lore']=$testResult[$keyID]['testold'];
                                if($testResult[$keyID]['answerold']) $forum[$i][2][$j]['testContent'][$remark]['Answer']=$testResult[$keyID]['answerold'];
                                $forum[$i][2][$j]['testContent'][$remark]['testNum']=$testMsg[2];
                                $forum[$i][2][$j]['testContent'][$remark]['system']=$testMsg[1];

                                $testNum+=($testMsg[2]<2 ? 1 : $testMsg[2]);
                            }else{
                                $forum[$i][2][$j]['testContent'][$remark]['Lore']=""; //未查询到该试题
                                $forum[$i][2][$j]['testContent'][$remark]['testNum']=1;
                                $forum[$i][2][$j]['testContent'][$remark]['system']=$testMsg[1];
                            }
                        }
                    }
                }

                $menuIDArray[$jForum['menuID']]=$testNum;//试题序号
            }
        }
        return $forum;
    }
    /**
     * 根据ajax提交过来的tplID,删除自己的模板
     * @author demo
     */
    public function delCaseTplByID(){
        $tplID=$_POST['tplID'];
        if($tplID){
            $userName=$this->getCookieUserName();
            $where = 'UserName="'.$userName.'" and TplID='.$tplID;  //用户只能删除自己的模板
            $result = $this->getModel('CaseTpl')->deleteData(
                $where
            );
            if($result==false){
                $result=0;
            }else{
                $result=1;
            }
            $this->setBack($result);
        }
    }
    /**
     * 验证第一节根最后一节
     * @author demo
     */
    public function checkChapter(){
        $chapterID=$_POST['chapterID'];
        if($chapterID){
            $chapterParentPath=SS('chapterParentPath');
            $chapterChildList=SS('chapterChildList');
            $chapterList=$chapterChildList[end($chapterParentPath[$chapterID])['ChapterID']];
/*            $result['last']=count($chapterList)-1;
            foreach($chapterList as $i=>$iChapterList){
                if($chapterList[$i]['ChapterID']==$chapterID){
                    $result['start']=$i;
                }
            }*/
            $this->setBack($chapterList);
        }
    }
}