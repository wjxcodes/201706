<?php
/**
 * @author demo 
 * @date 2015年12月9日
 *
 */
/**
 * 题库官网个人主页相关
 */
namespace User\Controller;
class IndexCenterController extends BaseController{
    /**
     * 开启用户登录验证
     * @author demo
     * @date 2015年12月11日
     */
    public function __construct(){
        parent::__construct();
        //验证登录
        $checkRes = $this->checkLogin('Index',1);
        if( $checkRes[0] != 1){//登录验证未通过或者身份不符合
             $this->setError($checkRes);
             exit();
        }
    }
    /**
     * 个人中心首页
     * @author demo
     */
    public function index(){
        $cookieUserID = $this->getCookieUserID('Home');
        $cookieUserName = $this->getCookieUserName('Home');
        //用户等级相关
        $buffer=$this->getModel('User')->selectData(
            '*',
            'UserID="'.$cookieUserID.'"'
        );
        if($buffer[0]['UserPic']){
            if(!preg_match('/^http:.*/i',$buffer[0]['UserPic'])){//判断是不是QQ头像
                $buffer[0]['UserPic'] = C('WLN_DOC_HOST') . $buffer[0]['UserPic'];//非QQ头像
            }
        }else{//调用默认头像
            $buffer[0]['UserPic'] = __ROOT__ . '/Public/index/imgs/icon/photo.jpg';
        }
        //用户组权限
        $powerUserArr = $this->getApiCommon('User/powerUserId');
        foreach($powerUserArr as $i=>$iPowerUserArr){
            if($iPowerUserArr['IfDefault']=='1' && $iPowerUserArr['GroupName']=='1'){
                $defaultGroup=$iPowerUserArr;
            }
            if($iPowerUserArr['ListID']=='all'){
                $allPowerArr = $iPowerUserArr['PUID'];
            }
        }
        //获取当前用户权限
        $userGroupArr = $this->getModel('UserGroup')->selectData(
            'GroupName,GroupID,LastTime',
            'UserID='.$buffer[0]['UserID'].' AND GroupName=1',
            'GroupName asc'
        );
        if($userGroupArr[0]['LastTime']>time() && $userGroupArr[0]['GroupID'] != $defaultGroup['PUID']){
            $buffer[0]['UserJF']="包月用户";
            $buffer[0]['UserJZRQ']=date('Y-m-d',$userGroupArr[0]['LastTime']);
            $buffer[0]['UserJZSY']=ceil(($userGroupArr[0]['LastTime']-time())/3600/24);
            $buffer[0]['UserJFMS']='按包月计费';
        }else{
            $buffer[0]['UserJF']="普通用户";
            $buffer[0]['UserJZRQ']='';
            $buffer[0]['UserJFMS']="按点值计费";
        }
        $buffer[0]['GroupID'] = '';
        if(empty($userGroupArr[0]['GroupID']) || (($userGroupArr[0]['GroupID']!=$allPowerArr) && $userGroupArr[0]['LastTime']<time())){
            $buffer[0]['GroupID'] = $defaultGroup['PUID'];
        }else{
            $buffer[0]['GroupID'] = $userGroupArr[0]['GroupID'];
        }
        if($buffer[0]['GroupID'] == $defaultGroup['PUID']){
            $buffer[0]['UserJF']="普通用户";
            $buffer[0]['UserJFMS']="按点值计费";
        }else{
            $buffer[0]['UserJF']="包月用户";
            $buffer[0]['UserJFMS']='按包月计费';
        }
        foreach($powerUserArr as $i=>$iPowerUserArr){
            if(in_array($iPowerUserArr['PUID'],explode(',',$buffer[0]['GroupID']))){
                $buffer[0]['UserGroup']=$iPowerUserArr['UserGroup'];
            }
        }
        //等级相关信息
        $userLevelMsg=$this->getModel('UserLevel')->getLevelMsg($cookieUserName,1);
        //升级经验差值
        $userLevelMsg['expCha']=$userLevelMsg[0]['LevelExpMax']-$buffer[0]['ExpNum'];
        //经验条百分比
        $userLevelMsg['baifen']=($buffer[0]['ExpNum']/$userLevelMsg[0]['LevelExpMax'])*100;
        //用户下载
        $downNum=$this->getModel('DocDown')->selectCount(
            'UserName="'.$cookieUserName.'"',
            'DownID'
        );
        //用户资源上传
        $docNum=$this->getModel('UserDynamic')->selectCount(
            'UserName="'.$cookieUserName.'"',
            'UDID'
        );
        $expList=$this->getApiCommon('User/expList');
        //调取该用户的经验获取记录总数
        $log=$this->getModel('UserExpRecord')->selectData(
            '*',
            'UserID='.$cookieUserID
        );
        foreach($log as $i=>$iLog){
            switch($expList[$iLog['ExpTag']]['ExpTime']){
                //仅一次
                case '0':
                    $expList[$iLog['ExpTag']]['done']=1;
                case '1':
                    if(date('Y-m-d',time())==date('Y-m-d',$iLog['AddTime'])){
                        $expList[$iLog['ExpTag']]['done']=1;
                    }
            }
        }
        $expTotal='';
        if($buffer[0]['IfAuth']=='1'){
            foreach($expList as $i=>$iExp){
                if(empty($iExp['done'])){
                    $expTotal+=$iExp['ExpAuthPoint'];
                }
            }
        }else{
            foreach($expList as $i=>$iExp){
                if(empty($iExp['done'])){
                    $expTotal+=$iExp['ExpPoint'];
                }
            }
        }

        //获取用户关注和粉丝数目
        $follow = [
            'following' => 0,//关注数目
            'follower'  => 0//关注者数目
        ] ;
        $userFollowModel = $this->getModel('UserFollow');
        $userFollowCount = $userFollowModel->getFollowCount($cookieUserID);

        if($fCount=$userFollowCount[$cookieUserID]){
            $follow['following'] = $fCount['following'];
            $follow['follower']  = $fCount['follower'];
        }

        //获取动态
        $users =  $userFollowModel->getFollowingListAll($cookieUserID,1);
        $where['UserID'] = [
            'IN',
            $users
        ];
        $dynamics  = $this->getModel('Dynamic')->selectData(
            'Content,LoadTime',
            $where,
            'DynamicID DESC',
            15
        );
        //更改时间格式
        foreach($dynamics as $i=>$iDynamics){
            $dynamics[$i]['LoadTime'] = date('Y-m-d H:i:s',$iDynamics['LoadTime']);
        }
        $this->assign('userMsg',$buffer[0]); //等级相关信息
        $this->assign('dynamics',$dynamics);//
        $this->assign('downNum',$downNum); //下载相关信息
        $this->assign('docNum',$docNum); //上传相关信息
        $this->assign('fansNum',$follow['follower']); //粉丝信息
        $this->assign('followNum',$follow['following']); //关注信息
        $this->assign('levelMsg',$userLevelMsg); //等级相关信息
        $this->assign('expTotal',$expTotal); //获取经验总数相关信息
        $this->display();
    }

    /**
     * 基础任务，即可以完成获取经验的任务
     * @author demo
     */
    public function baseWork(){
        //用户信息 显示是否认证过教师
        $buffer=$this->getModel('User')->selectData(
            '*',
            'UserID="'.$this->getCookieUserID('Home').'"'
        );
        //基础任务相关
        $expList=$this->getApiCommon('User/expList');
        //调取该用户的经验获取记录
        $userID=$this->getCookieUserID('Home');
        $log=$this->getModel('UserExpRecord')->selectData(
            '*',
            'UserID='.$userID
        );
        foreach($log as $i=>$iLog){
            switch($expList[$iLog['ExpTag']]['ExpTime']){
                //仅一次
                case '0':
                    $expList[$iLog['ExpTag']]['done']=1;
                case '1':
                    if(date('Y-m-d',time())==date('Y-m-d',$iLog['AddTime'])){
                        $expList[$iLog['ExpTag']]['done']=1;
                    }
            }
        }
        $titleName='基础任务';
        $this->assign('buffer',$buffer[0]); //用户基础信息
        $this->assign('explist',$expList); //经验任务表
        $this->assign('function','baseWork'); //方法名称
        $this->assign('titleName',$titleName); //小标题名称
        $this->assign('pageName',$titleName); //小标题名称
        $this->display();
    }
    
    /**
     * 公用分页
     * @param array 分页参数 及 页码
     * @author demo
     */
    protected function page($count){
        $count = $count[0]['c'];//总记录数
        $perpage=C('WLN_PERPAGE');//每页记录数
        $url = C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
        $url = MODULE_NAME.__ACTION__.$url;
        $Page = useToolFunction('WLNPage','init',array($count,$perpage,array('url'=>$url)));
        $nowPage = page($count,$_POST['page'],$perpage);//规范当前页数
        $pageLimit = ($nowPage-1)*$pageLimit.','.$perpage;
        $show = $Page->show();// 分页显示输出
        return array(
            'pageLimit' =>$pageLimit,
            'pages' => $show
        );
    }
    
    /**
     * 任务大厅任务
     * @author demo
     */
    public function missionHall(){
        $this->assign('level', $this->getModel('MissionHallTasks')->getLevel());
        $this->assign('function','missionHall');
        $this->display();
    }
    
    /**
     * 任务列表数据
     * @author demo 
     */
    public function myTasksList(){
        $params['p'] = $_REQUEST['p'];
        if(!$params['p']){
            $params['p'] = 1;
        }
        if($_REQUEST['startTime']){
            $params['startTime'] = $_REQUEST['startTime'];
        }
        if($_REQUEST['endTime']){
            $params['endTime'] = $_REQUEST['endTime'];
        }
        if($_REQUEST['level']){
            $params['level'] = $_REQUEST['level'];
        }
        if($_REQUEST['status'] >= 0){
            $params['status'] = $_REQUEST['status'];
        }
        $params['field'] = array('l.MHLID,t.MHTID, t.Title, t.Url, t.Level,l.AddTime as recordAddTime,l.Status');
        $params['uid'] = $this->UserID;
        $mhr = $this->getModel('MissionHallLog')->recordList($params);
        $output[0]=$mhr[0];
        $output[1]=$mhr[1];
        $output[2]=20;
        $this->setBack($output);
    }
    

    /**
     * 用户等级特权
     * @author demo
     */
    public function levelPower(){
        $titleName='等级特权';
        //获取该用户等级下的权限值
        $userName = $this->getCookieUserName('Home');
        $levelValueList=$this->getModel('UserLevel')->getLevelMsg($userName);
        $levelList=$this->getModel('UserLevel')->levelPower();
        $this->assign('levelList',$levelList[1]);
        $this->assign('valueList',$levelList[0]);
        $this->assign('thisLevelMsg',$levelValueList[0]);
        $this->assign('function','levelPower'); //方法名称
        $this->assign('titleName',$titleName); //小标题名称
        $this->assign('pageName',$titleName); //小标题名称
        $this->display();
    }

    /**
     * 用户所有权限组
     * @author demo
     */
    public function userPower(){
        $titleName="用户权限";
        $result = $this->getModel('PowerUser')->getTable(array(1,2,3,4,5,6), true);
        $list = $result[0];
        $header = array_shift($list);
        $userGroup = $this->getModel('UserGroup')->findData('GroupID', 'GroupName=1 AND UserID='.$this->getCookieUserID('Home'));
        $nowGroupName=$this->getApiCommon('User/powerUserGroup')[1]['groupList'][$userGroup['GroupID']]['UserGroup'];
        $result = $result[1];
        $group = array();
        $eq = 0 ;
        foreach($result as $key=>$value){
            $g = 'team';
            if($key >= 3){
                $g = 'person';
            }
            if($value == (int)$userGroup['GroupID']){
                $eq = $key + 2 ;
                $group[$g][$key]['belong'] = 'true';
            }
            $group[$g][$key]['name'] = $header[$key+1];
        }
        unset($result);
        $this->assign('eq',$eq);
        $this->assign('group', $group);
        $this->assign('groupName', $nowGroupName);
        $this->assign('header', $header);
        $this->assign('list', $list);
        $this->assign('function','userPower'); //方法名称
        $this->assign('titleName',$titleName); //小标题名称
        $this->assign('pageName',$titleName); //小标题名称
        $this->display();
    }
    /**
     * 获取试题内容
     * @param array $field 查询的字段
     * @param array $where 查询的条件
     * @param string $order 排序
     * @param array $page 分页
     * @param int $reload
     * @return array
     * @author demo
     */
    protected function getTest($field,$where,$order,$page,$reload=0){
        $TestReal=$this->getModel('TestReal'); //试题
        $tmpStr=$TestReal->getTestIndex($field,$where,$order,$page);
        if($tmpStr === false){
            $this->setError('30504', (IS_AJAX ? 1 : 0));
        }
        if($reload){
            $tmpStr[0] = R('Common/TestLayer/reloadTestArr',array($tmpStr[0]));
        }
        return $tmpStr;
    }
    /**
     * 我的收藏试题页面
     * @author demo
     */
    public function myTestFav(){
        $buffer = $this->getApiCommon('Types/typesSubject');
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                foreach($iBuffer as $j=>$jBuffer){
                    $output[$i][$j]['TypesID']=$jBuffer['TypesID'];
                    $output[$i][$j]['TypesName']=$jBuffer['TypesName'];
                    $output[$i][$j]['SubjectID']=$jBuffer['SubjectID'];
                    $output[$i][$j]['TypesScore']=$jBuffer['TypesScore'];
                    $output[$i][$j]['DScore']=$jBuffer['DScore'];
                    $output[$i][$j]['Volume']=$jBuffer['Volume'];
                    $output[$i][$j]['IfDo']=$jBuffer['IfDo'];
                    $output[$i][$j]['Num']=$jBuffer['Num'];
                }
            }
        }
        $this->assign('types',json_encode($output, JSON_UNESCAPED_UNICODE));
        $this->assign('function','myTestFav');
        $this->assign('pageName','试题收藏');
        $this->display();
    }

    /**
     * 我的收藏试题列表
     * @author demo
     */
    public function myTestFavList(){
        $catalogID = $_POST['catalogID'];
        $curPage = $_POST['page']?$_POST['page']:1;
        $subjectID = $_POST['subjectID'];
        $userName=$this->getCookieUserName('Home');
        if(empty($subjectID)){
            $this->setError('30733',1);
        }
        $testList = $this->getMyTestFav($catalogID,$curPage,$subjectID,$userName);
        if($testList[0] == 1){
            $this->setError($testList[1],1);
        }
        $this->setBack($testList[1]);
    }

    /**
     * 获取单个试题答案解析等内容
     * @author demo
     */
    public function myTestFavOneInfo(){
        $testID = $_POST['id'];
        if(empty($testID)) {
            $this->setError('30301',1);
        }
        $where=array('TestID'=>$testID);
        $field=array('kllist','analytic','answer','remark','firstloadtimeint','typeid');
        $page=array('page'=>1,'perpage'=>1);
        $tmpStr=$this->getTest($field,$where,'',$page);

        if($tmpStr){
            //4.15后上传的同步测试类文档随机显示试题解析
            if($tmpStr[0][0]['loadtimeint']>strtotime('2015-4-15') && $tmpStr[0][0]['typeid']==7){
                //随机概率0.4
                $randInt=rand(100000, 999999);
                if($randInt<600000){
                    $tmpStr[0][0]['analytic']='';
                }
            }
            $this->setBack($tmpStr[0][0]);
        }
        $this->setError($testInfo[1],1);
    }

    /**
     * 添加评论
     * @author demo 16-4-1
     */
    public function addComment(){
        $comment=formatString('changeStr2Html',$_POST['comment']);
        $quesID=$_POST['id'];
        $quesScore=$_POST['score'];
        $subject=$this->mySubject;
        $username=$this->getCookieUserName('Home');
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

        if($this->getModel('Message','insertData',$data)===false){
            $this->setError('1X443',1);
        }else{
            $this->setBack('success');
        }
    }

    /**
     * 试题纠错
     * @author demo 16-4-1
     */
    public function correct(){
        $correctContent = trim($_POST['correctcontent']);
        if(empty($correctContent) || $correctContent=="我来说两句~"){
            $this->setError('1X444',1);
        }
        $data['Ctime']=time();
        $result['status']='success';
        $result['msg']='信息提交成功！';
        $data['UserName']=$this->getCookieUserName('Home');
        $data['TestID']=$_POST['testID'];
        $data['SubjectID']=$this->mySubject;
        $data['Content']=formatString('IPReplace',$correctContent);//formatString('changeStr2Html',$correctContent);
        $data['From']='0';//是组卷前台，根据该状态是0；
        if(!empty($_POST['TypeID'])){
            $data['TypeID']=$_POST['TypeID'];
        }else{
            $data['TypeID']=0;//没有选中错误类型，默认成错误类型：其他(0)
        }
        $correctLogResult=$this->getModel('CorrectLog','insertData',$data);
        if(!empty($correctLogResult)){//判断成功
            $buffer = $this->getModel('MissionHallRecords','finishTask',$this->getCookieUserID('Home'));
            $this->setBack($result);
        }else{
            $this->setError('30310',1);
        }
    }

    /**
     * 获取评论信息
     * @author demo 16-3-30
     */
    public function commentList(){
        $id = $_REQUEST['id'];
        $page = $_REQUEST['p'];
        if(empty($page)){
            $page = 1;
        }
        $comments = $this->getModel('Message')->getMessagesById($id, $page, 5);
        foreach($comments['data'] as $key=>&$value){
            $value['LoadDate'] = date('Y/m/d H:i:s', $value['LoadDate']);
        }
        $pagtion = $this->fillPagtion(array('id'=>$id), $comments['count'], $page, $comments['prepage'], '/User/IndexCenter/commentList', 5);
        $data = array(
            'pagtion' => $pagtion['pages'],
            'comments' => $comments
        );
        $this->setBack($data);
    }

    /**
     * 移动试题，获取目录结构
     * @author demo 16-3-25
     */
    public function getUserCatalog(){
        $username = $this->getCookieUserName('Home');
        $subject = $this->mySubject;
        if($subject!=''){
            $where['SubjectID'] = $subject;
        }
        $fid = $_POST['fid']?intval($_POST['fid']):0;
        $catalog=$this->getModel('UserCatalog');
        $where['UserName']=$username;
        $where['FatherID']=$fid;
        $catalogList=$catalog->getArrList_2($where);
        $output[0]=$catalogList;
        $this->setBack($output);
    }

    /**
     * 收藏夹目录重命名
     * @author demo 16-3-29
     */
    public function rename(){
        $id = (int)$_POST['id'];
        if(!$id){
            $this->setError('30301', 1);
        }
        $data = $_POST['name'];
        $result = $this->getModel('UserCatalog')->updateData(array(
            'CatalogName'=>$_POST['name']
        ), 'CatalogID='.$id);
        if($result === false){
            $this->setError('30311', 1);
        }
        $this->setBack('success');
    }

    /**
     * 添加目录
     * @author demo 16-3-29
     */
    public function addFav(){
        $parentId = (int)$_POST['parent'];
        $name = $_POST['name'];
        $order = (int)$_POST['order'];
        $user = $this->getModel('User')->findData('UserName,SubjectStyle','UserID='.$this->getCookieUserID('Home'));
        $data = array(
            'CatalogName' => $name,
            'AddTime' => time(),
            'OrderID' => $order,
            'UserName' => $user['UserName'],
            'SubjectID' => $user['SubjectStyle'],
            'FatherID' => $parentId
        );
        $result = $this->getModel('UserCatalog')->insertData($data);
        if($result === false){
            $this->setError('30310', 1);
        }
        $this->setBack('success|'.$result);
    }

    /**
     * 删除目录及目录下的子目录和试题
     * @author demo
     * @date  2014年9月20日
     */
    public function delCatalogByID(){
        $catalogID=$_POST['catalogID'];
        $catalog=$this->getModel('UserCatalog');
        //删除该目录下的子目录包括自身
        $where='FatherID='.$catalogID;
        $field='CatalogID';
        $subCata=$catalog->getCatalogList($field,$where);
        $cataid=array();
        $cataid[0]=$catalogID;
        $i=1;
        if($subCata){
            foreach($subCata as $ii=>$iSubCata){
                $cataid[$i]=$iSubCata['CatalogID'];
                $i++;
            }
        }
        $catalogID=implode(',',$cataid);
        $cataDe=$this->getModel('UserCollect')->deleteData(
            'CatalogID in('.$catalogID.')'
        );              //用于删除该目录下的试题
        $buffer=$this->getModel('UserCatalog')->deleteData(
            'CatalogID in('.$catalogID.')'
        );              //用于删除该目录下的子目录，包括其本身
        if($buffer===false){
            $this->setError('1X443',1);
        }elseif($cataDe===false){
            $this->setError('1X412',1);
        }elseif($cataDe===false && $buffer===false){
            $this->setError('30302',1);
        }else{
            $this->setBack('success');
        }
    }

    /**
    * 试题收藏移动
    * @author demo
    * @date 2014年9月22日
    */
    public function updateFavSave(){
        $username = $this->getCookieUserName('Home');
        $subject = $this->mySubject;
        $id=$_POST['id'];
        $data='CatalogID='.$_POST['catalogID'];
        $where='UserName="'.$username.'" and TestID="'.$id.'" and SubjectID="'.$subject.'" AND `From`=1';
        $buffer = $this->getModel('UserCollect')->updateData(
            $data,
            $where
        );
        $msg = 'success';
        if($buffer===false){
            $msg = 'failure';
        }
        $this->setBack($msg);
    }

    /**
     * 删除收藏试题
     * @author demo
     */
    public function myTestFavDelete(){
        $tid = $_POST['tid'];
        $subjectID = $_POST['subjectID'];
        $userName=$this->getCookieUserName('Home');
        if(empty($id)) {
            $this->setError('30301',1);
        }
        if(empty($subjectID)){
            $this->setError('30733',1);
        }
        $testFav = $this->delTestFav($userName,$tid,$subjectID);
        if($testFav[0] == 1){
            $this->setError($testFav[1],1);
        }
        $this->setBack('success');
    }

    /**
     * 试题反馈
     * @author demo
     */
    public function myTestComment(){
        $this->assign('function','myTestComment');
        $this->assign('pageName','试题反馈');
        $this->display();
    }

    /**
     * 获取评论列表
     * @author demo
     */
    public function getMyTestCommentList(){
        $curPage=$_POST['curpage'];//当前页码
        $pageSize=$_POST['pagesize'];//每页数量
        if(empty($curPage) || $curPage<1) $curPage=1;
        if(empty($pageSize)) $pageSize=10;
        $where=array(
            "Status"=>0,//未锁定状态
            "SubjectID"=>cookie('SubjectId')//区分学科
        );
        if($_POST['ifUser']){//是否区分用户
            $where['UserName'] = $this->getCookieUserName('Home');
        }
        if($_POST['testID']){//是否指定试题ID
            $where['TestID'] = $_POST['testID'];
        }
        $message =  $this->getModel('Message');
        $count = $message->selectCount($where,'*');
        $pageCount = ceil($count/$pageSize);
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
     * 试题相关的历史存档页面
     * @author demo
     */
    public function myTestDocSave(){
        //获取学科缓存数据
        $subjectCache = $this->getApiCommon('Subject/subject');
        $subjectData = array();
        foreach($subjectCache as $value){
            if($value['PID'] != 0 && '高中' == $value['ParentName']){
                $subjectData[$value['SubjectID']] = array(
                    'SubjectID'=>$value['SubjectID'],
                    'SubjectName'=>'高中'.$value['SubjectName']
                );
            }
        }
        $this->assign('SubjectID',cookie('SubjectId'));
        $this->assign('subject', $subjectData);
        $this->assign('function','myTestDocSave');
        $this->assign('pageName','历史存档');
        $this->display();
    }

    /**
     * 试题相关历史存档的列表
     * @author demo
     */
    public function myTestDocSaveList(){
        $dateDiff=$_POST['dateDiff'];
        $curPage=$_POST['page']?$_POST['page']:1;
        $subjectID=$_POST['subjectID'];
        $userName=$this->getCookieUserName('Home');
        if(empty($subjectID)){
            $this->setError('30733',1);
        }
        $output = $this->myDocSaveList($dateDiff,$curPage,$subjectID,$userName);
        $output[3] = '高中'.$this->getApiCommon('Subject/subject')[$subjectID]['SubjectName'];
        $this->setBack($output);
    }

    /**
     * 试题相关历史下载页面
     * @author demo
     */
    public function myTestDown(){
        //获取学科缓存数据
        $subjectCache = $this->getApiCommon('Subject/subject');
        $subjectData = array();
        foreach($subjectCache as $value){
            if($value['PID'] != 0 && '高中' == $value['ParentName']){
                $subjectData[$value['SubjectID']] = array(
                    'SubjectID'=>$value['SubjectID'],
                    'SubjectName'=>'高中'.$value['SubjectName']
                );
            }
        }
        $this->assign('SubjectID',cookie('SubjectId'));
        $this->assign('subject', $subjectData);
        $this->assign('function','myTestDown');
        $this->assign('pageName','历史下载');
        $this->assign('function','myTestDown');
        $this->display();
    }

    /**
     * 试题相关历史下载列表
     * @author demo
     */
    public function myTestDownList(){
        $dateDiff=$_POST['dateDiff'];//时间
        $curPage=$_POST['page']?$_POST['page']:1;//页码
        $subjectID=$_POST['subjectID'];//学科ID
        $area = $_POST['area']?$_POST['area']:1;//默认为全部题库
        if(empty($subjectID)){
            $this->setError('30733',1);
        }
        $userName = $this->getCookieUserName('Home');
        $output = $this->myDocDownList($dateDiff,$curPage,$subjectID,$userName,$area);
        $output[3] = '高中'.$this->getApiCommon('Subject/subject')[$subjectID]['SubjectName'];
        $this->setBack($output);
    }

    /**
     * 试题相关删除记录列表
     * @author demo
     */
    public function myTestDelete(){
        $userName = $this->getCookieUserName('Home');
        $id = $_POST['id'];
        $style = $_POST['style'];
        if(!$id){
            $this->setError('30301',1);
        }
        if($style == 'DocSave') {
            $output = $this->delDocSave($userName, $id);
        }else if($style == 'Down'){
            $output = $this->delDown($userName, $id);
        }
        if($output[0] == 1){
            $this->setError($output[1],1);
        }
        $this->setBack('success');
    }

    /**
     * 用户中心我的订单
     * @author demo
     */
    public function myOrder(){
        $userID = $this->getCookieUserID('Home');//用户ID

        //分页参数处理
        $page = $_GET['p'];
        if(empty($page) || !is_numeric($page)){
            $page = 1;
        }
        $page    = (int)$page;//强制转化
        $perPage = 12;//C('WLN_PERPAGE');//每页显示数目

        //模式处理[全部,未付款,已完成]
        $mode    = $_GET['getMode'];
        $modeArr = [1,2,3];
        if(!in_array($mode,$modeArr)){
            $mode = 1;
        }

        //数据处理
        $orderList    = $this->getModel('OrderList');

        //获取数据类型选择
        $where   = [];
        switch($mode)
        {
            case 1:
                $where = ['UID'=>$userID,'IfHidden'=>0];//全部
                break;
            case 2:
                $where = ['UID'=>$userID,'IfHidden'=>0,'OrderStatus'=>0];//待付款
                break;
            case 3:
                $where = ['UID'=>$userID,'IfHidden'=>0,'OrderStatus'=>1];//已完成
                break;
        }

        //获取总数
        $count=$orderList->selectCount(
            $where,
            'OLID'
        );

        $result   = [];//返回的数据
        $pageList = '';//返回的分页
        if($count>=1){
            //获取数据
            $page = page($count,$page,$perPage);
            $result  = $orderList->selectData(
                'OLID,OrderID,OrderTime,OrderStatus,TotalFee,OrderName,IfHidden',
                $where,
                'OLID DESC',
                ($perPage*($page-1)).','.$perPage
            );
            //格式转化,过滤敏感数据
            foreach($result as $i=>$iResult){
                $result[$i]['OrderTime'] = date('Y-m-d H:i:s',$iResult['OrderTime']);
                $result[$i]['OrderName'] = str_replace('#',' ',$iResult['OrderName']);
                unset($result[$i]['IfHidden']);//删除非必需字段
                unset($result[$i]['OLID']);//删除真实ID
            }

            //获取分页
            $pageObj   = useToolFunction('WLNPage','init',[$count,$perPage]);
            $pageList  = $pageObj->show();
        }

        //待付款数
        $icount = $count;
        if($mode !=2 ){
            $icount=$orderList->selectCount(
                ['UID'=>$userID,'IfHidden'=>0,'OrderStatus'=>0],
                'OLID'
            );
        }
        $icount = max(0,$icount);

        $this->assign('pageName','我的订单');
        $this->assign('icount',$icount);
        $this->assign('page',$pageList);
        $this->assign('result',$result);
        $this->assign('function','myOrder');
        $this->display();
    }

    /**
     * 删除订单
     * @author demo
     */
    public function delOrder(){
        $userID = $this->getCookieUserID('Home');//用户ID
        $oid = $_POST['oid'];

        $orderList = $this->getModel('OrderList');
        $buffer=$orderList->selectData(
            'OLID',
            ['UID'=>$userID,'OrderID'=>$oid],
            '',
            1
        );
        if(!$buffer) $this->setBack(array('error','订单异常,请重试!'));

        //软删除
        if($this->getModel('OrderList')->updateData(
                ['IfHidden'=>1],
                ['UID'=>$userID,'OrderID'=>$oid])===false){
            $this->setBack(array('error','删除失败!请稍后再试!'));
        }
        $this->setBack(array('success'));
    }

    /**
     * 用户去支付
     * @author demo
     */
    public function goToPay(){
        $userID = $this->getCookieUserID('Home');//用户ID
        $oid    = $_GET['oid'];
        //查询
        $orderList = $this->getModel('OrderList');
        $buffer = $orderList->selectData(
            'OLID,TotalFee,OrderID,OrderName,OrderDetail',
            ['UID' => $userID, 'OrderID' => $oid],
            '',
            1
        );
        if(!$buffer) $this->setError('1X423');
        $buffer = $buffer[0];
        //查询订单支付宝订单数据
        $param = array();
        $param['orderNum']    = $buffer['OrderID'];
        $param['orderName']   = $buffer['OrderName'];
        $param['orderDetail'] = $buffer['OrderDetail'];
        $param['totalFee']    = $buffer['TotalFee'];

        useToolFunction('Alipay/Alipay','doPay',array($param));
        exit();
    }


    /**
     * 金币获取记录列表
     * @author demo
     */
    public function myGold(){
        $this->assign('pageName','金币记录');
        $this->assign('titleName','金币记录');
        $this->assign('function','myGold');
        $this->display();
    }

    /**
     * ajax请求分页，金币获取记录
     * @author demo
     */
    public function myGoldList(){
        $dateDiff=$_POST['dateDiff'];//时间
        $dataTime= handleDate('conversionTime',$dateDiff);//将时间转换成时间戳
        if ($dataTime[1]) {//昨天的记录，存在时间区间
            $where["AddTime"] = array('between',array($dataTime[0],$dataTime[1]));
        } else {
            $where['AddTime'] =array('gt',$dataTime[0]);
        }
        $userID = $this->getCookieUserID('Home');
        $where['UserID']=$userID;
        $curPage=$_POST['page']?$_POST['page']:1;
        $perPage=10;//默认查找的记录数量为10条
        $payCount=$this->getModel('Pay')->selectCount($where,'PayID');
        $pageCount=ceil($payCount/$perPage);
        if($curPage>$pageCount) $curPage=$pageCount;
        if($curPage<1 || !is_numeric($curPage)) $curPage=1;
        $buffer=$this->getModel('Pay')->selectData(
            'PayID,PayName,PayMoney,UserID,AddTime',
             $where,
            'PayID Desc',($curPage-1)*$perPage.','.$perPage);
        foreach($buffer as $i=>$iBuffer){
            $buffer[$i]['AddTime']=date('Y-m-d H:i',$buffer[$i]['AddTime']);
            $buffer[$i]['PayMoney']=floatval($buffer[$i]['PayMoney']);
        }
        $output[0]=$buffer;
        $output[1]=$payCount;
        $output[2]=$perPage;
        $this->setBack($output);
    }

    /**
     * 用户分享文档记录
     * @author demo
     */
    public function myShareDoc(){
        $this->assign('pageName','分享历史下载');
        $this->assign('titleName','分享历史下载');
        $this->assign('function','myShareDoc');
        $this->display();
    }

    /**
     * ajax获取用户分享记录列表
     * @author demo
     */
    public function myShareDocList(){
        $dateDiff=$_POST['dateDiff'];//时间
        $dataTime= handleDate('conversionTime',$dateDiff);//将时间转换成时间戳
        if ($dataTime[1]) {//昨天的记录，存在时间区间
            $where["a.ShareTime"] = array('between',array($dataTime[0],$dataTime[1]));
        } else {
            $where['a.ShareTime'] =array('gt',$dataTime[0]);
        }
        $where['a.SharerID']=$this->getCookieUserID('Home');
        $curPage=$_POST['page']?$_POST['page']:1;
        $perPage=10;//默认查找的记录数量为10条
        $shareCount=$this->getModel('DocShare')->getShareTotal($where);
        $pageCount=ceil($shareCount/$perPage);
        if($curPage>$pageCount) $curPage=$pageCount;
        if($curPage<1 || !is_numeric($curPage)) $curPage=1;
        $buffer=$this->getModel('DocShare')->getShareList($where,($curPage-1)*$perPage.','.$perPage);
        foreach($buffer as $i=>$iBuffer){
            $buffer[$i]['ShareTime']=date('Y-m-d H:i',$buffer[$i]['ShareTime']);
            //生成一个加密代码
            $buffer[$i]['id']=md5($iBuffer['DownID'].'(*&!@#%^&#@$)(@!^^#!%@#&*@!)');
        }
        $output[0]=$buffer;
        $output[1]=$shareCount;
        $output[2]=$perPage;
        $this->setBack($output);
    }
    /**
     * 获取可接收题的目录
     * @param $userName
     * @param $subjectID
     * @param $testID
     * @return array
     * @author demo
     */
    public function getCanMoveMenu($userName,$subjectID,$testID){
        //获取用户收藏目录
        $catalogList = $this->userCatalog($userName,$subjectID,0);
        if($catalogList){
            //查找该试题所在目录
            $catalogID = $this->getCatalogIDByTestID($userName,$subjectID,$testID)['CatalogID'];
            foreach($catalogList as $iCatalogList){
                if($iCatalogList['CatalogID'] == $catalogID){
                    unset($iCatalogList);
                }
                if(!empty($iCatalogList['child'])){
                    foreach($iCatalogList['child'] as $jCatalogList){
                        if($jCatalogList['CatalogID'] == $catalogID){
                            unset($jCatalogList);
                        }
                    }
                }
            }
            return $catalogList;
        }
        return array();
    }
    /**
     * 根据试题ID查询试题所在收藏目录
     * @author demo
     * @date 2014年9月24日
     *
     */
    public function getCatalogIDByTestID($userName,$subjectID,$testID){
        //查找该试题所在目录
        $collect=$this->getModel('UserCollect');
        $field='CatalogID';
        $where = array(
            'UserName' => $userName,
            'SubjectID' => $subjectID,
            'TestID' => $testID,
            'From' => 1
        );
        $catalog=$collect->findData($field,$where);
        return $catalog;
    }
    /**
     * 获取用户收藏目录，用于用户收藏页面列表
     * @author demo
     * @date 2014年9月20日
     */
    public function userCatalog($userName,$subjectID,$fid){
        $catalog=$this->getModel('UserCatalog');
        $where = array(
            'UserName' => $userName,
            'SubjectID' => $subjectID,
            'FatherID' => $fid
        );
        $catalogList=$catalog->getArrList_2($where);
        return $catalogList;
    }

    /**
     * 获取收藏目录ID
     * @param int $fid 父类ID
     * @param string $userName 用户名
     * @return array
     * @author demo
     */
    public function getCatalogIDByFID($fid,$userName){
        $catalog=$this->getModel('UserCatalog');
        //验证收藏目录是该用户
        $catalogUser = $catalog->findData('UserName','CatalogID='.$fid);
        if($userName != $catalogUser['UserName']){
            return array(1,'1X405');//
        }
        //查找收藏目录下的子目录
        $where['FatherID'] = $fid;
        $field = 'CatalogID';
        $catalogList = $catalog->getCatalogList($field,$where);
        //目录ID集合
        $catalogIDArray = array();
        if($catalogList){
            foreach($catalogList as $iCatalogList){
                $catalogIDArray[]=$iCatalogList['CatalogID'];
            }
        }
        array_push($catalogIDArray,$fid);
        return array('in',implode(',',$catalogIDArray));
    }


    /**
     * ajax获取试题 试题列表显示试题内容
     * @param int $testID 试题ID
     * @return array
     * @author demo
     */
    public function getOneTestByID($testID) {
        $where=array('TestID'=>$testID);
        $field=array('kllist','analytic','answer','remark','firstloadtimeint','typeid');
        $page=array('page'=>1,'perpage'=>1);
        $tmpStr=$this->getTest($field,$where,'',$page);

        if($tmpStr){
            //4.15后上传的同步测试类文档随机显示试题解析
            if($tmpStr[0][0]['loadtimeint']>strtotime('2015-4-15') && $tmpStr[0][0]['typeid']==7){
                //随机概率0.4
                $randInt=rand(100000, 999999);
                if($randInt<600000){
                    $tmpStr[0][0]['analytic']='';
                }
            }
            return $tmpStr;
        }
        return array(1,'');//没有答案解析
    }
    /**
     * 试题收藏列表
     * @param int $catalogID 目录ID
     * @param int $curPage 当前页码
     * @param int $subjectID 当前学科
     * @param string $userName 用户名
     * @return array
     * @author demo
     * @date 2014年9月20日
     */
    public function getMyTestFav($catalogID,$curPage,$subjectID,$userName){

        $where = array();//条件
        $perPage=10;//每次查询的数量
        $where['SubjectID'] = $subjectID;
        $where['UserName'] = $userName;
        $where['From'] = 1;//收藏来源，组卷端（后期可根据用户身份调整）
        if($catalogID=='all'){//全部
            $where['CatalogID'] = array('egt',0);
        }elseif($catalogID==0){//默认目录，没有收藏目录
            $where['CatalogID'] = 0;
        }else{
            $catalogs = $this->getCatalogIDByFID($catalogID,$userName);
            if($catalogs[0] == 1){
                return $catalogs;//收藏目录不属于该用户
            }
            $where['CatalogID'] = $catalogs;
        }
        //查询收藏试题总数量
        $saveCount=$this->getModel('UserCollect')->selectCount($where,'TestID');
        if($saveCount){
            //验证当前页码是否合法
            $pageCount=ceil($saveCount/$perPage);//总页数
            if($curPage > $pageCount){//当前页码大于总页数时返回最大页码数
                $curPage = $pageCount;
            }
            if($curPage<1 || !is_numeric($curPage)){//小于1或者不合法，重置为第一页
                $curPage=1;
            }
            //分页获取试题ID数据集（每次查询10道试题）
            $testIDList=$this->getModel('UserCollect')->selectData(
                'TestID',
                $where,
                'LoadTime desc',
                ($curPage-1)*$perPage.','.$perPage
            );
            //转化试题ID格式
            $testList=array();
            foreach($testIDList as $iTestIDList){
                $testList[] = $iTestIDList['TestID'];
            }
            $where=array('TestID'=>implode(',',$testList));
            $field=array('testid','typesid','typesname','testnum','test','diff','docname','firstloadtime');
            $testArray=$this->getTest($field,$where,'','',1);
            if(!$testArray[0]){
                return array(1,'1X404');
            }
            $tmpArr=array();
            foreach($testList as $iTestList){
                if(empty($testArray[0][$iTestList])){
                    continue;
                }
                $tmpArr[]=$testArray[0][$iTestList];
            }
            unset($testIDList);
            unset($testArray);
            $output[0]=$tmpArr;
            $output[1]=$saveCount;
            $output[2]=$perPage;
            return array(0,$output);
        }else{
            //暂时没有收藏
            return array(1,'1X404');
        }
    }
    /**
     * 用户历史存档
     * @param $dateDiff string 查询时间
     * @param $curPage string 页码
     * @param $subjectID int 学科ID
     * @return array 第一个键值为1时，标识返回的是错误码数据，否则返回的是正确数据
     * @author demo
     */
    public function myDocSaveList($dateDiff,$curPage,$subjectID,$userName){

        $dataTime = handleDate('conversionTime',$dateDiff)[0];//将时间转换成时间戳
        $perPage=10;//默认查找的记录数量为10条

        $docSave = $this->getModel('DocSave');

        $where=array(
            'StyleState'=>0 ,
            'UserName'=>$userName,
            'LoadTime'=>array('gt',$dataTime),
            'SubjectID'=>$subjectID
        );
        $saveCount=$docSave->selectCount($where,'*');
        $pageCount=ceil($saveCount/$perPage);
        if($curPage>$pageCount) $curPage=$pageCount;
        if($curPage<1 || !is_numeric($curPage)) $curPage=1;
        $buffer=$docSave->selectData(
            '*',
            $where,
            'LoadTime desc',
            ($curPage-1)*$perPage.','.$perPage
        );
        $output[0]=$buffer;
        $output[1]=$saveCount;
        $output[2]=$perPage;
        return $output;
    }

    /**
     * 用户历史下载
     * @param string $dateDiff 查询时间
     * @param string $curPage 页码
     * @param int $subjectID 学科ID
     * @param string $userName 用户名
     * @param int $area 是否区分学科
     * @return array 第一个键值为1时，标识返回的是错误码数据，否则返回的是正确数据
     * @author demo
     */
    public function myDocDownList($dateDiff,$curPage,$subjectID,$userName,$area){

        $dataTime = handleDate('conversionTime',$dateDiff);//将时间转换成时间戳
        $perPage=10;//默认查找的记录数量为10条

        $where["IfShow"] = 1;
        $where["UserName"] = $userName;
        if($area) {
            $where["SubjectID"] = $subjectID;
        }
        if ($dataTime[1]) {//昨天的记录，存在时间区间
            $where["LoadTime"] = array('between',array($dataTime[0],$dataTime[1]));
        } else {
            $where['LoadTime'] =array('gt',$dataTime[0]);
        }
        $docDown = $this->getModel('DocDown');
        $downCount=$docDown->selectCount($where,'DownID');
        $pageCount=ceil($downCount/$perPage);
        if($curPage>$pageCount) $curPage=$pageCount;
        if($curPage<1 || !is_numeric($curPage)) $curPage=1;
        $buffer=$docDown->selectData('*',$where,'LoadTime desc',($curPage-1)*$perPage.','.$perPage);
        foreach($buffer as $i=>$iBuffer){
            $buffer[$i]['id']=md5($iBuffer['DownID'].'(*&!@#%^&#@$)(@!^^#!%@#&*@!)');
        }

        $output[0]=$buffer;
        $output[1]=$downCount;
        $output[2]=$perPage;
        return $output;
    }

    /**
     * 删除试题收藏
     * @param string $userName 用户名
     * @param int $tid 试题ID
     * @param int $subjectID 学科
     * @return array
     * @author demo
     */
    protected function delTestFav($userName,$tid,$subjectID){
        $where['UserName'] = $userName;
        $where['TestID'] = $tid;
        $where['SubjectID'] = $subjectID;
        $where['From'] = 1;
        $userCollect = $this->getModel('UserCollect');
        $testFav = $userCollect->selectData('CollectID',$where);
        if($testFav) {
            if ($userCollect->deleteData($where)) {
                return array(0);
            } else {
                return array(1, '30302');
            }
        }else{
            return array(1,'1X428');
        }
    }

    /**
     * 删除历史存档
     * @param $userName string 用户名
     * @param $id int 删除的文档ID
     * @return array
     * @author demo
     */
    protected function delDocSave($userName,$id){
        $docSave = $this->getModel('DocSave');
        $buffer=$docSave->findData(
            'UserName',
            'SaveID='.$id
        );
        if($buffer) {
            if ($buffer['UserName'] == $userName) {
                if($docSave->deleteData('SaveID=' . $id)){
                    return array(0);
                }else{
                    return array(1,'30302');
                }
            }else{
                return array(1,'30507');
            }
        }else{
            return array(1,'1X429');
        }
    }

    /**
     * 删除历史下载
     * @param $userName string 用户名
     * @param $id int 删除的记录ID
     * @return array
     * @author demo
     */
    protected function delDown($userName,$id){
        $docDown = $this->getModel('DocDown');
        $buffer=$docDown->findData(
            'UserName',
            'DownID='.$id
        );
        if($buffer) {
            if ($buffer['UserName'] == $userName) {
                if($docDown->updateData(array('IfShow'=>0),'DownID=' . $id)){
                    return array(0);
                }else{
                    return array(1,'30302');
                }
            }else{
                return array(1,'30507');
            }
        }else{
            return array(1,'1X429');
        }
    }

    /**
     * 我的粉丝
     * @author demo
     */
    public function getMyFollower(){
        $cookieUserID = $this->getCookieUserID('Home');//用户ID

        //分页参数处理
        $page = (int)$_GET['p'];
        if(empty($page) || !is_numeric($page)){
            $page = 1;
        }
        $perPage = 12;//C('WLN_PERPAGE');//每页显示数目

        //数据相关模型
        $userFollow = $this->getModel('UserFollow');

        //获取粉丝总数
        $count = $userFollow->getFollowCount($cookieUserID,2);



        $result   = [];//返回的数据
        $pageList = '';//返回的分页

        if($count = $count[$cookieUserID]['follower']){
            //获取分页数据
            $result   = $userFollow->getFollowerList($cookieUserID,$page,$perPage);
            $result = $this->getFollowInfo($result);
            //获取分页
            $pageObj   = useToolFunction('WLNPage','init',[$count,$perPage,[]]);
            $pageList  = $pageObj->show();
        }

        $this->assign('pageName','我的粉丝');
        $this->assign('page',$pageList);
        $this->assign('result',$result);
        $this->assign('function','getMyFollower');
        $this->display();
    }

    /**
     * 我的关注
     * @author demo
     */
    public function getMyFollowing(){
        $cookieUserID = $this->getCookieUserID('Home');//用户ID

        //分页参数处理
        $page = (int)$_GET['p'];
        if(empty($page) || !is_numeric($page)){
            $page = 1;
        }
        $perPage = 12;//C('WLN_PERPAGE');//每页显示数目

        //数据相关模型
        $userFollow = $this->getModel('UserFollow');

        //获取关注总数
        $count = $userFollow->getFollowCount($cookieUserID,1);



        $result   = [];//返回的数据
        $pageList = '';//返回的分页
        if($count = $count[$cookieUserID]['following']){
            //获取分页数据
            $result   = $userFollow->getFollowingList($cookieUserID,$page,$perPage);

            $result   = $this->getFollowInfo($result);
            //获取分页
            $pageObj   = useToolFunction('WLNPage','init',[$count,$perPage,[]]);
            $pageList  = $pageObj->show();
        }
        $this->assign('pageName','我的关注');
        $this->assign('page',$pageList);
        $this->assign('result',$result);
        $this->assign('function','getMyFollowing');
        $this->display();
    }

    /**
     * 关注
     * @author demo
     */
    public function doFollow(){
        if(IS_AJAX){
            $result = $this->getModel('UserFollow')->doFollow($this->getCookieUserID('Home'), $_POST['fid']);
            $this->returnInfo($result);
        }
        exit();
    }

    /**
     * 取消关注
     * @author demo
     */
    public function unFollow(){
        if(IS_AJAX) {
            $result = $this->getModel('UserFollow')->unFollow($this->getCookieUserID('Home'), $_POST['fid']);
            $this->returnInfo($result);
        }
        exit();
    }

    /**
     * 关注的返回信息
     * @author demo
     */

    private function returnInfo($return){

        switch($return)
        {
          case 'Follow Success':
                $this->setBack(['info'=>'关注成功','return'=>2,'status'=>1]);
                break;
          case 'unFollow Success':
                $this->setBack(['info'=>'取消成功','return'=>2,'status'=>1]);
                break;
          case 'Data Error':
                $this->setBack(['info'=>'数据异常','return'=>2,'status'=>0]);
                break;
          case 'Has Followed':
                 //已经关注
                $this->setBack(['info'=>'已经关注','return'=>2,'status'=>0]);
                break;
          case 'Followeder Not Exist':
                //关注的人不存在
                $this->setBack(['info'=>'关注的人不存在','return'=>2,'status'=>0]);
                break;
          case 'Follow Self Error':
                //不能关注自己
                $this->setBack(['info'=>'不能关注自己','return'=>2,'status'=>0]);
                break;
          case 'Parameter Error':
                //参数异常
                $this->setBack(['info'=>'参数异常','return'=>2,'status'=>0]);
                break;
          case 'Not Followed':
                //之前未关注
                $this->setBack(['info'=>'之前未关注','return'=>2,'status'=>0]);
                break;
          default:
                $this->setBack(['info'=>'数据异常','return'=>2,'status'=>0]);
        }
    }

    /**
     * 获取关注相关用户信息
     * @access private
     * @param $result array 关注关系数组
     * @author demo
     */
    private function getFollowInfo(&$result){
        //查询粉丝个人信息
        $followerInfo = $this->getModel('User')->selectData(
            'UserID,UserName,NickName,IfAuth,Whois,ExpNum,UserPic,SchoolID,SubjectStyle,GradeID',
            ['UserID'=>['IN',$result[0]]]
        );

        //查询粉丝个人网站等级信息
        $followerLevelInfo = $this->getModel('UserLevel')->getLevelMsgs($followerInfo);

        //批量获取学校信息
        $schoolIDArr = [];//学校ID数组
        foreach($followerInfo as $i=>$iData){
              $schoolIDArr[$iData['UserID']] = $iData['SchoolID'];
        }
        $where     = ['SchoolID'=>['IN',array_unique($schoolIDArr)]];
        $schoolArr = $this->getModel('School')->selectData(
               'SchoolName,SchoolID',
               $where
        );
        $schoolArr    = array_column($schoolArr,'SchoolName','SchoolID');
        unset($schoolIDArr);


        //组合信息[按照原来的键值]
        $newResult = [];
        foreach($result[0] as $i=>$iResult){
            foreach($followerInfo as $j=>$jData){
                if($jData['UserID']==$iResult){
                    $newResult[$i]['Name']   = $jData['NickName']?$jData['NickName']:$jData['UserName'];
                    $newResult[$i]['TAuth']  = $jData['IfAuth']==2 ? 1 : 0;// ==2时表示教师资格认证通过
                    $newResult[$i]['UID']    = $jData['UserID'];//用户ID
                    $newResult[$i]['School'] = $schoolArr[$jData['SchoolID']] ? $schoolArr[$jData['SchoolID']] : '';//学校

                    //设置年级信息
                    $newResult[$i]['Grade']  = '';//年级信息
                    if($jData['SubjectStyle']!=0){//未设置学科 显示为空
                        $subjectGrade = $this->getData([
                            'style'     => 'grade',
                            'subjectID' => $jData['SubjectStyle']
                        ]);
                        $subjectGrade = array_column($subjectGrade,'GradeID');
                        if($gradeName = $subjectGrade[$jData['SubjectStyle']]['GradeName']){
                            $newResult[$i]['Grade']  = $gradeName;
                        }
                    }

                    //头像处理
                    if($jData['UserPic']){
                        if(!preg_match('/^http:.*/i',$jData['UserPic'])){//判断是不是QQ头像
                            $jData['UserPic'] = C('WLN_DOC_HOST') . $jData['UserPic'];//非QQ头像
                        }
                    }else{//调用默认头像
                        $jData['UserPic'] = __ROOT__ . '/Public/index/imgs/icon/photo.jpg';
                    }
                    $newResult[$i]['Pic']    = $jData['UserPic'];

                    //身份处理
                    if($jData['Whois'] == 0){
                        $jData['Whois'] = '学生';
                    }elseif($jData['Whois'] == 1){
                        $jData['Whois'] = '教师';
                    }elseif($jData['Whois'] == 2){
                        $jData['Whois'] = '家长';
                    }else{
                        $jData['Whois'] = '校长';
                    }
                    $newResult[$i]['Status'] = $jData['Whois'];
                    $newResult[$i]['unLock']   = 1;//保证用户合法
                }
            }

            foreach($followerLevelInfo as $j=>$jData){
                if($j==$iResult){
                    $newResult[$i]['Exp'] = $jData;
                }
            }
            foreach($result[1] as $j=>$jData){
                if($j==$iResult){
                    if($newResult[$i]['unLock']) {//保证用户合法
                        $newResult[$i]['Rel'] = $jData;//1表示互相关注
                    }
                }
            }

        }
        unset($result);
        return $newResult;
    }

    /**
     * 我的动态列表[包括关注的人]
     * @author demo
     */
    public function myDynamic(){
        $cookieUserID = $this->getCookieUserID('Home');
        //获取自己关注的人
        $users = $this->getModel('UserFollow')->getFollowingListAll($cookieUserID,1);
        $where['UserID'] =[
            'IN',
            $users
        ];
        $dynamicModel = $this->getModel('Dynamic');
        $count = $dynamicModel->selectCount(
              $where,
              'DynamicID'
        );
        $result   = [];
        $pageList = '';
        if($count){
            $page = (int)$_GET['p'];
            $perPage = 12;//C('WLN_PERPAGE');//每页显示数目
            $page    = page($count,$page,$perPage);
            $limit   = ($perPage*($page-1)).','.$perPage;
            $result  = $this->getModel('Dynamic')->selectData(
                'Content,LoadTime',
                $where,
                'DynamicID DESC',
                $limit
            );
            //更改时间格式
            foreach($result as $i=>$iResult){
                  $result[$i]['LoadTime'] = date('Y-m-d H:i:s',$iResult['LoadTime']);
            }
            //获取分页数据
            $pageObj   = useToolFunction('WLNPage','init',[$count,$perPage,[]]);
            $pageList  = $pageObj->show();
            //获取用户头像
            $userInfo = $this->getModel('User')->selectData(
                'UserPic',
                $where
            );
            if($userPic = $userInfo[0]['UserPic']){
                if(!preg_match('/^http:.*/i',$userPic)){//判断是不是QQ头像
                    $buffer[0]['UserPic'] = C('WLN_DOC_HOST') . $userPic;//非QQ头像
                }
            }else{
                 $userPic = __ROOT__ . '/Public/index/imgs/icon/photo.jpg';
            }
        }
        $this->assign('pageName','我的动态');
        $this->assign('function','myDynamic');
        $this->assign('userPic',$userPic);
        $this->assign('count',$count);
        $this->assign('page',$pageList);
        $this->assign('result',$result);
        $this->display();
    }

    /**
     * 个人资料
     * @author demo
     */
    public function myUserInfo(){
        $userName = $this->getCookieUserName('Home');
        $data = $this->getUserInfo($userName);
        //初始化学校
        $schoolList=$this->getSchoolListByAreaID('SchoolName,SchoolID',$data['AreaID']);
        $gradeArray = $this->getData(array('style'=>'getSingle','cacheName'=>'gradeListSubject'));//年级
        $subjectArray = $this->getData(array('style'=>'getGradeSubject','gradeID'=>$data['GradeID']));//学科
        $this->assign('userData',$data);
        $this->assign('schoolList',$schoolList);
        $this->assign('gradeList',$gradeArray);
        $this->assign('subjectList',$subjectArray);
        $this->assign('pageName','个人资料');
        $this->assign('titleName','个人资料');
        $this->assign('function','myUserInfo');
        $this->display();
    }

    /**
     * 获取学校列表
     * @author demo
     */
    public function ajaxGetSchoolList(){
        $areaID = $_POST['areaID'];
        if(!$areaID){
            $this->setError('',1);
        }
        $schoolList=$this->getSchoolListByAreaID('SchoolName,SchoolID',$areaID);
        $this->setBack($schoolList);
    }
    /**
     * 保存个人资料
     * @author demo
     */
    public function saveMyInfo(){
        $userID = $this->getCookieUserID('Home');
        $userData = $this->getModel("User")->selectData('UserID,UserName,Password',array('UserID'=>$userID));
        $output = $this->saveUserInfo($userData[0],$_POST);
        if($output[0]==0){
            $this->setError($output[1],1);
        }
        $this->setBack('success');
    }


}