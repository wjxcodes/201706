<?php
/**
 * @author demo
 * @date 2015年10月20日
 */
namespace Task\Manage;
class MissionHallBManage extends BaseController  {
    /**
     * 任务列表
     * @author demo
     */
    public function index(){
        $subject = $this->subject();//学科
        $where = '' ;
        $order = '' ;
        $mhcModel = D('MissionHallChapter');//章节model
        //查询条件
        $map=array();
        $_REQUEST = array_filter($_REQUEST);
        if($_REQUEST['order'] != 0){
            //结束时间倒序排序
            $orderVal = 1 ;//排序值
            $order = 'EndTime DESC';//排序语句
        }else{
            $orderVal = 0 ;
            $order = 'EndTime ASC';
        }
        $map['order'] = $orderVal;
        if($_REQUEST['Title1']){
            $map['Title1'] = $_REQUEST['Title1'];
            $where[] = 'Title like "%'.$_REQUEST['Title1'].'%"';
        }else{//高级查询
            if($_REQUEST['Title2']){//任务标题查询
                $map['Title2'] = $_REQUEST['Title2'];
                $where[] = 'Title like "%'.$_REQUEST['Title2'].'%"';
            }
            if(!$this->ifSubject && $_REQUEST['Subject']){//学科属性
                $map['Subject'] = $_REQUEST['Subject'];
                $where[] = 'mha.SubjectID = '.(int)$_REQUEST['Subject'];
                $chapterArray = $this->getChapterBySubjectID((int)$_REQUEST['Subject']);
                $this->assign('chapter',$chapterArray);
            }
            if($_REQUEST['Subject'] && $_REQUEST['ChapterID']){//章节版本 并 分配变量
                $map['ChapterID'] = $_REQUEST['ChapterID'];
                $chapterIDWhere = $_REQUEST['ChapterID'];
                $whereMHTID = $mhcModel->getList('MHTID','ChapterID IN ('.implode(',', $chapterIDWhere).')');
                if($whereMHTID){
                    foreach ($whereMHTID as $wmhtid){
                        $wTmp[] = $wmhtid['MHTID'];
                    }
                    $where[] = 'mha.MHTID in ('.implode(',', $wTmp).')';
                }
                $this->assign('chapterWhere',$_REQUEST['ChapterID']);
            }
            if($_REQUEST['Type']){//任务类型查询
                $map['Level'] = $_REQUEST['Level'];
                $where[] = 'Level = '.$_REQUEST['Type'];
            }
            switch ($_REQUEST['end']){//任务结束时间
                case 1:
                    $where[] ='EndTime >'.time() . ' AND BeginTime > '.time();
                    $map['end'] = 1;
                    break;//即将开始
                case 2:
                    $where[] ='EndTime BETWEEN '.time().' AND '.(time()+3600*24*7);
                    $map['end'] = 2;
                    break;//一周以内
                case 3:
                    $where[] ='EndTime BETWEEN '.time().' AND '.(time()+3600*24*7*30);
                    $map['end'] = 3;
                    break;//一月以内
                case 4:
                    $where[] ='EndTime >'.(time()+3600*24*7*30);
                    $map['end'] = 4;
                    break;//长期有效
                default:
                    $where[] = 'EndTime >'.time();
                    $tmp = 'default';
                    break;
            }
            if($_REQUEST['rewardType']){//奖励类型
                $where[] = 'rewardType = '.$_REQUEST['rewardType'];
            }
        }
        if($_REQUEST['Title1'] || count($where) == 1 && $tmp == 'default'){
            $this->assign('block',false);
        }else{
            $this->assign('block',true);//显示高级查询栏
        }
        $where = implode(' AND ', $where);
        $this->assign('order',$orderVal);//分配模板排序变量
        if($this->ifSubject){
            $where[] = 'mha.SubjectID IN('.$this->mySubject.')';
        }
        $pageName = '任务列表';
        //分页查询
        $perpage = C('WLN_PERPAGE'); //每页显示数
        $page=(isset ($_GET['p']) ? $_GET['p'] : 1) . ',' . $perpage;
        $mhtModel = D('MissionHallTasks');
        $mhtasks = $mhtModel->getList('mht.*,u.AdminName,u.RealName,mha.SubjectID',$where,$order,$page);
        $count = $mhtModel->getList('count(*) as c',$where);
        $this->pageList($count[0]['c'], $perpage, $map);
        $taskTypesName = $mhtModel->taskTypes();//任务类型数组 type=>name
        //处理数据
        foreach($mhtasks as $i=>$mht){
            $mhtasks[$i]['Type']= $taskTypesName[$mht['Type']];
            $tmp = $mhtModel->showReward($mht['RewardType'],$mht['Reward']);//前台奖励显示
            $mhtasks[$i]['RewardType'] = $tmp['RewardType'];
            $htresult[$i]['Reward'] = $tmp['Reward'].$tmp['RewardType'];
            $mhtasks[$i]['Reward'] = $mht['RewardType'] == 2 ? '￥'.$mht['Reward'] : (int)$mht['Reward'];
            $mhtasks[$i]['Subject'] = $this->returnSonArray($subject, '', $mhtasks[$i]['Subject']);
            $mhtasks[$i]['SubjectName'] = $subject[$mht['SubjectID']];//学科名
            $mhtasks[$i]['Limit'] = $mht['Limit'] == '1' ? '需要申请' : '无需申请' ;
            $mhtasks[$i]['Hot'] = $mht['Hot'] == '0' ? '' : '<i style="color:red">【置顶】</i>' ;
            $mhtasks[$i]['BeginTime'] = date('Y-m-d H:i:s', $mht['BeginTime']);
            $tmp = ceil(($mht['EndTime']-time())/(3600*24));
            if($mhtasks[$i]['EndTime'] == '2000000000'){//任务长期
                $mhtasks[$i]['EndTime'] = $mhtasks[$i]['BeginTime'] . '&nbsp;-&nbsp;长 期';
            }elseif ($mhtasks[$i]['EndTime'] <= time()){//任务已结束
                $mhtasks[$i]['EndTime'] = $mhtasks[$i]['BeginTime'] . '&nbsp;-&nbsp;'. date('Y-m-d H:i:s',$mhtasks[$i]['EndTime']) .'&nbsp;&nbsp;<i style="color:red;">已结束</i>';
            }else{
                $mhtasks[$i]['EndTime'] = $mhtasks[$i]['BeginTime'] . '&nbsp;-&nbsp;'. date('Y-m-d H:i:s',$mhtasks[$i]['EndTime']);
            }
            /* 获得当前任务的章节版本名称 */
            $mhtidArray[] = $mht['MHTID'] ;
            
        }
        
        $tasksChapterIDsTmp = $mhcModel->getList('ChapterID,MHTID','MHTID IN ('.implode(',', $mhtidArray).')');//所有任务的章节版本ID
        foreach ($mhtasks as $i=>$mht){//为每个任务添加章节版本名
            $tmp = $this->returnSonArray($tasksChapterIDsTmp, 'MHTID', $mht['MHTID']);
            $mhtasks[$i]['ChapterName'] = $this->getCurrentTaskChapterName($mht['SubjectID'], $tmp);
        }
        $taskTypes = $mhtModel->taskTypes();
        $this->assign('level', $mhtModel->getLevel());
        $this->assign('subject',$subject);//学科
        $this->assign('taskTypes',$taskTypes);
        $this->assign('pageName', '任务列表');//页面标题
        $this->assign('mhtasks',$mhtasks);
        $this->display();
    }
    
    /**
     * 获得高中学科（临时）
     * @return array 高中学科
     */
    protected function subject(){
        $subjectSource = SS('subjectParentId')[2]['sub'];
        foreach ($subjectSource as $source){
            $subject[$source['SubjectID']] = $source['SubjectName'];
        }
        return $subject;
    }
    
    /**
     * 通过学科获得章节版本（临时）
     * @return array 章节版本
     */
    public function getChapterBySubjectID($subjectId = ''){
        if(IS_AJAX) $subjectId = I('SubjectID');
        $chapterArr = $this->getData(array('style'=>'chapter','subjectID'=>$subjectId));
        if(IS_AJAX) $this->setBack($chapterArr);
        else return $chapterArr;
    }
    
    /**
     * 返回二维数组中的一维数组
     * @param array $array 要查找的数组
     * @param sting $where 查找条件
     * @return array
     * @author demo
     */
    private function returnSonArray($array,$key,$value){
        return array_filter($array,function($tmp) use($key,$value){return $tmp[$key] == $value; });
    }
    
    /**
     * 任务详情页
     * @author demo
     */
    public function info(){
        $MHTID=I('id','','intval');    //获取数据标识
        //判断数据标识
        if(empty($MHTID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(IS_AJAX){//ajax分页
            $mhrModel = D('MissionHallRecords');//任务领取记录model
            $status = I('status','','intval');
            if($status == '') die;
            $count = $mhrModel->getList('count(*) as c','','mhr.MHTID = '.$MHTID.' AND mhr.Status = '.$status);//记录总数
            $count = (int)$count[0]['c'];
            if($count === 0){
                exit(json_encode(array('page'=>'','data'=>array())));
            }
            //分页
            $listRows = 20;//每页记录数
            import('ORG.Util.Page');// 导入分页类
            $page = I('p','1','intval');//获得当前页
            if($page < 1) $page = 1;
            if($page > $count) $page = $count;
            $start = ($page-1)*$listRows ;
            $page = handlePage('init',$count,$listRows);
            $page = $page->show();
            //记录结果集
            $records = $mhrModel->getList('mhr.*,u.Nickname,u.UserName',false,'mhr.MHTID = '.$MHTID.' AND mhr.Status= ' .$status,'mhr.MHRID DESC',$start.','.$listRows);
            foreach ($records as $i=>$rs){
                $records[$i]['AddTime'] = date('Y-m-d H:i:s',$rs['AddTime']);
            }
                
            if(!empty($records)){
                exit(json_encode(array('page'=>$page,'data'=>$records)));
            }else {
                exit(false);
            }
        }else{
            //任务详情
            $act = 'info'; //模板标识
            $pageName = '任务详情';
            $mhtModel = D('MissionHallTasks');
            $taskTypes = $mhtModel->taskTypes();
            $where = 'mht.MHTID ='.$MHTID;
            if($this->ifSubject){
                $where .= ' AND mha.SubjectID IN('.$this->mySubject.')';
            }
            $mhtdata = $mhtModel->getList('mht.*,u.AdminName,mha.SubjectID',$where)[0];
            $mhtdata['SubjectName'] = $this->subject()[$mhtdata['SubjectID']];
            $mhtdata['BeginTime'] = date('Y-m-d H:i:s' , $mhtdata['BeginTime']);
            if($mhtdata['EndTime'] == '2000000000'){//长期任务
                $mhtdata['EndTime'] = '<i style="color:red">&nbsp;&nbsp;【长期有效】</i>';
            }elseif ($mhtdata['EndTime'] <= time()){//任务已结束
                $mhtdata['EndTime'] = date('Y-m-d H:i:s' , $mhtdata['EndTime']).'<i style="color:red;">已结束</i>';
            }else{
                $mhtdata['EndTime'] = date('Y-m-d H:i:s' , $mhtdata['EndTime']);
            }
            $mhtdata['Description']= stripslashes_deep(formatString('IPReturn',$mhtdata['Description']));
            $mhtdata['Hot'] = $mhtdata['Hot'] != 0 ? '<i style="color:red">【置顶】</i>' : '';
            $mhtdata['Type'] = $taskTypes[$mhtdata['Type']];
            $mhtdata['Limit'] = $mhtdata['Limit'] == 2 ? '任意参加' : '需要申请';
            if( $mhtdata['RewardType'] == '1' ){
                $mhtdata['RewardType'] = '积分';
            }elseif( $mhtdata['RewardType'] == '3' ){
                $mhtdata['RewardType'] = '金币';
            }elseif( $mhtdata['RewardType'] == '2' ){
                $mhtdata['RewardType'] = '现金';
            }
            $currentChapterID = D('MissionHallChapter')->getList('ChapterID','MHTID='.$MHTID);
            if($currentChapterID){//获得当前任务章节版本名
                $mhtdata['chapterName'] = $this->getCurrentTaskChapterName($mhtdata['SubjectID'], $currentChapterID);
            }
            /*载入模板标签*/
            $this->assign('module', D('MissionHallModules')->findData('*', 'MHTID='.$MHTID));
            $this->assign('level', $mhtModel->getLevel());
            $this->assign('act', $act); //模板标识
            $this->assign('edit', $mhtdata);//当前数据集
            $this->assign('pageName', $pageName);//页面标题
            $this->display();
        }
    }
    
    /**
     * 获得当前任务章节版本
     * @param int $subjectID 学科ID
     * @param array $curTaskChapterIDArray 当前任务拥有的章节版本ID数组
     * @return string 当前任务版本名
     * @author demo
     */
    protected function getCurrentTaskChapterName($subjectID,$curTaskChapterIDArray){
        $chapterArr = $this->getData(array('style'=>'chapter','subjectID'=>$subjectID));//当前学科对应的章节版本
        $chapterName = array() ;
        foreach ($curTaskChapterIDArray as $cp){
            $chapterName[] = array_values($this->returnSonArray($chapterArr, 'ChapterID',$cp['ChapterID']))[0]['ChapterName'];
        }
        return implode('&nbsp;&nbsp;', array_filter($chapterName));
    }
    
    /**
     * 添加任务
     * @author demo
     */
    public function add(){
        $act = 'add'; //模板标识
        $pageName = '添加任务';
        $subject = $this->subject();//学科
        $this->assign('subject',$subject);//学科
        $model = D('MissionHallTasks');
        $taskTypes = $model->taskTypes();
        $this->assign('level', $model->getLevel());
        $this->assign('pageName', $pageName);//页面标题
        $this->assign('taskTypes',$taskTypes);//任务类型名称
        $this->assign('act',$act);
        $this->display();
    }
    
    /**
     * 审核记录(通过、拒绝，取消)
     * @author demo
     */
    public function check(){
        $groupId = I('id');//任务申请记录ID
        $check = I('status');//审核 2通过 3拒绝取消
        if(empty($groupId) || empty($check)){//缺少标示
            $this->setError('30301', 1);
        }
        $rs = D('MissionHallLog')->check($check,$groupId);
        
        if(IS_AJAX){//ajax审核单条记录
            if($rs===false){
                $this->setError('30311', 1); //修改失败！
            }else{
                //写入日志
                $this->adminLog('MissionHallLog','审核MHLID为【'.$groupId.'】的数据'.$check);
                $this->setBack('success'); //审核成功
            }
        }
        if($rs===false){
            $this->setError('30311'); //修改失败！
        }else{
            //写入日志
            $this->adminLog('MissionHallLog','审核MHLID为【'.$groupId.'】的数据'.$check);
            $this->showSuccess('审核成功！','',true,0);
        }
    }

    /**
     * 单个任务记录列表
     * @author demo 2015-11-12
     */
    public function recordList(){
        $params['id'] = $_REQUEST['id'];
        $params['startTime'] = useToolFunction('StringFormat', 'decodeUrl', array($_REQUEST['startTime']));
        $params['endTime'] = useToolFunction('StringFormat', 'decodeUrl', array($_REQUEST['endTime']));
        $params['username'] = $_REQUEST['username'];
        $params['p'] = $_REQUEST['p'];
        $params['level'] = $_REQUEST['level'];
        if(!is_null($_REQUEST['status']) && $_REQUEST['status'] >= 0){
            $params['status'] = $_REQUEST['status'];
        }
        if($this->ifSubject){
            $params['SubjectID'] = $this->mySubject;
        }
        $log = D('MissionHallLog');
        $subjects = SS('subjectParentId');//学科
        $subject = array();
        foreach($subjects as $key=>$value){
            if('高中' == $value['SubjectName']){
                foreach($value['sub'] as $k=>$v){
                    $subject[$v['SubjectID']] = $v['SubjectName'];
                    unset($subjects[$key][$k]);
                }
            }else{
                unset($subjects[$key]);
            }
        }
        $result = $log->recordList($params);
        $this->assign('subjectJSON', json_encode($subject));
        $this->assign('subject', $subject);
        $this->assign('list', $result[0]);
        $this->assign('pageName', '记录列表');//页面标题
        $params['startTime'] = useToolFunction('StringFormat', 'encodeUrl', array($params['startTime']));
        $params['endTime'] = useToolFunction('StringFormat', 'encodeUrl', array($params['endTime']));
        $this->pageList($result[1], 20, $params);
        $this->assign('level', D('MissionHallTasks')->getLevel());
        $this->display();
    }

    /**
     * 获取用户简单信息
     * @author demo 
     */
    public function userInfo(){
        $id = $_GET['id'];
        //判断数据标识
        if(empty($id)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $field = 'PhoneCode, SubjectStyle, LoadDate, GradeID, b.SchoolName, a.AreaID';
        $data = $this->getModel('User')->getUserSchool($id, $field);
        $data['LoadDate'] = date('Y-m-d H:i:s', $data['LoadDate']);
        $area = $data['AreaID'];
        unset($data['AreaID']);
        $cache = SS('areaParentPath')[$area];
        if($cache){
            $data['Area'] = '';
            foreach($cache as $value){
                $data['Area'] .= $value['AreaName'];
            }
        }else{
            $data['Area'] = '未填写';
        }
        if(!$data['GradeID']){
            $data['Grade'] = '未填写';
        }else{
            $cache =$this->getData(array(
                'style'=>'grade',
                'subjectID'=>12,
                'return'=>2
            ));
            foreach($cache as $value){
                if($value['GradeID'] == $data['GradeID']){
                    $data['Grade'] = $value['GradeName'];
                    continue;
                }
            }
        }
        unset($data['GradeID']);
        $this->setBack($data);
    }
    
    /**
     * 修改任务
     * @author demo
     */
    public function edit(){
        $MHTID=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($MHTID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑任务';
        $act = 'edit'; //模板标识
        $subject = $this->subject();//学科
        $mhtModel = D('MissionHallTasks');
        $taskTypes = $mhtModel->taskTypes();
        $mhtdata = $mhtModel->getList('mht.*,u.AdminName,mha.SubjectID','mht.MHTID ='.$MHTID)[0];
        $chapterIDArray = D('MissionHallChapter')->getList('ChapterID',' MHTID ='.$MHTID);//获取当前任务章节版本
        $mhtdata['ChapterID'] = array();
        foreach ($chapterIDArray as $cid){
            $mhtdata['ChapterID'][] = $cid['ChapterID'];//二维数组变一维
        }
        $mhtdata['BeginTime'] = date('Y-m-d H:i:s' , $mhtdata['BeginTime']);
        if($mhtdata['EndTime'] == '2000000000'){//任务长期有效
            $mhtdata['longTiem'] = true;
        }
        $mhtdata['Description']=stripslashes_deep(formatString('IPReturn',$mhtdata['Description']));
        $mhtdata['Description'] = str_replace(array("\r","\n","\r\n"), '<br>',$mhtdata['Description']);
        $mhtdata['EndTime'] = date('Y-m-d H:i:s' , $mhtdata['EndTime']);
        //分配模板变量
        $this->assign('module', D('MissionHallModules')->findData('*', 'MHTID='.$MHTID));
        $this->assign('level', $mhtModel->getLevel());
        if($mhtdata['SubjectID']){
            $chapter = $this->getChapterBySubjectID($mhtdata['SubjectID']);//章节版本
            $this->assign('chapter',$chapter);//学科
        }
        $this->assign('subject',$subject);//学科
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $mhtdata);//当前数据集
        $this->assign('pageName', $pageName);//页面标题
        $this->assign('taskTypes',$taskTypes);//任务类型名称
        $this->display('add');
    }
    
    /**
     * 删除任务
     * @author demo
     */
    public function delete(){
        $groupId=$_POST['id'];    //获取数据标识
        if(!$groupId){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $mhtModel = D('MissionHallTasks');
        $result = $mhtModel->getList('mht.MHTID,mht.Num','mht.MHTID IN ('.$groupId.')');
        $noDel = '';
        foreach ($result as $rs){
            if($rs['Num'] >= 1){
                $noDel.= $rs['MHTID'].' ';//获取已经有参与人数的任务ID 不删除
            }else{
                $delID[] = $rs['MHTID'];//没人参与的任务ID 删除
            }
        }
        if(!empty($noDel)){//任务已有用户参与
            $noDel = '任务ID'.$noDel.'已有用户参与未删除！';
        }
        if(empty($delID)){//任务全部都有用户参与 不做删除操作
            $this->showSuccess($noDel,__URL__);
        }else{
            if($mhtModel->delete(implode(',', $delID))===false){
                $this->setError('30302'); //删除任务失败！
            }else{
                //删除任务成功 写入日志
                $this->adminLog('MissionHall','删除MHTID为【'.$groupId.'】的数据及关联MissionHallRecords任务记录');
                $this->showSuccess($noDel.'任务ID'.implode(' ', $delID).'删除成功！',__URL__);
            }
        }
    }
    
    /**
     * 获得任务领取记录通过任务ID
     * @param int $mhtid3
     * @return array | false
     * @author demo
     */
    private function getRecordsByOneTaskId($mhtid){
        $mhrecords = D('MissionHallRecords')->getList('mhr.*,u.Nickname,u.UserName',false,'mhr.MHTID = '.$mhtid);
        foreach ($mhrecords as $mhr){
            $mhr['AddTime'] = date('Y-m-d H:i:s',$mhr['AddTime']);
            if($mhr['Status'] == 1){//任务申请中
                $apply[] = $mhr;
            }elseif($mhr['Status'] == 2){//任务参与中
                $join[] = $mhr;
            }else{//不允许参加任务的
                $nopass[] = $mhr;
            }
        }
        return array('apply'=>$apply,'join'=>$join,'nopass'=>$nopass);
    }
    
    /**
     * 保存任务
     * @author demo
     */
    public function save(){
        if(!IS_POST) exit;
        $act = $_POST['act'];    //获取模板标识
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        $data['Title'] = htmlspecialchars(trim($_POST['Title']));
        $data['Description']=formatString('IPReplace',$_POST['Description']);
        //$data['Description'] = htmlspecialchars(trim($_POST['Description']));
        $data['Type'] = (int)$_POST['Type'];
        $data['Limit'] = (int)$_POST['Limit'];
        $data['Url'] = htmlspecialchars(trim($_POST['Url']));
        $data['BeginTime'] = strtotime($_POST['BeginTime']);
        $data['EndTime'] = empty($_POST['noEndTime']) ? strtotime($_POST['EndTime']) : 2000000000; //长期有效
        $data['RewardType'] = (int)$_POST['RewardType'];
        $data['Reward'] = trim($_POST['Reward']);
        $data['SubjectID'] = trim($_POST['SubjectID']);
        $data['Level'] = trim($_POST['Level']);
        $data['ReceiveTimes'] = trim($_POST['ReceiveTimes']);
        $data['Hot'] = empty($_POST['Hot']) ? 0 : time();
        $data['AdminID'] = $this->getCookieUserID();
        $data['ChapterID'] = $_POST['ChapterID'];//章节版本数组
        // if(preg_match('/^[\x{4e00}-\x{9fa5}\d\w_]{5,50}$/u',$data['Title']) < 1){
        //     R('Common/SystemLayer/showErrorMsg',array('任务名称请用适当的中文和数字字母但不能有空格', '', true, 3,'Public/error'));
        // }
        // if(strlen($data['Description']) < 10){
        //     R('Common/SystemLayer/showErrorMsg',array('任务描述太少了', '', true, 3,'Public/error'));
        // }
        if(!$data['BeginTime'] || !$data['EndTime']){
            R('Common/SystemLayer/showErrorMsg',array('任务时间不能为空', '', true, 3,'Public/error'));
        }
        if(!$data['Reward']){
            R('Common/SystemLayer/showErrorMsg',array('请填写任务奖励数量', '', true, 3,'Public/error'));
        }
        if($data['BeginTime'] >= $data['EndTime']){
            R('Common/SystemLayer/showErrorMsg',array('请让结束时间大于开始时间', '', true, 3,'Public/error'));
        }
        if(preg_match('/^\d+(\.\d{0,2})?$/', $data['Reward']) < 1 || $data['Reward'] > 99999.99){
            R('Common/SystemLayer/showErrorMsg',array('请输入正确的奖励数量不能超过100000.00', '', true, 3,'Public/error'));
        }
        // if(!empty($data['Url']) && preg_match('/^http:\/\/.*$/i',$data['Url'],$m) <= 0){
        //     R('Common/SystemLayer/showErrorMsg',array('如果输入任务URL请输入http://开头的正确地址', '', true, 3,'Public/error'));
        // }
        $moduleData['JumpUrl'] = $_POST['JumpUrl'];
        $moduleData['ApplicateUrl'] = $_POST['ApplicateUrl'];
        $moduleData['RealReward'] = $_POST['RealReward'];
        $moduleData['PromoteTime'] = $_POST['PromoteTime'];
        $moduleData['RealReward'] = $_POST['RealReward'];
        $mhtModel = D('MissionHallTasks');
        $module = D('MissionHallModules');
        if($act=='add'){//新建任务
            $data['Num'] = 0;
            $data['AddTime'] = time();
            $result = $mhtModel->save($data);
            if($result===false){
                $this->setError('30310'); //添加失败！
            }else{
                $module->save($result, $moduleData);
                //添加成功写入日志
                $this->adminLog('MissionHall','添加任务【'.$data['Title'].'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        }else if($act=='edit'){//修改任务
            $data['ModifiedTime'] = time();
            if($mhtModel->save($data,(int)$_POST['id'])===false){
                    $this->setError('30311'); //修改失败！
            }else{
                $module->save($_POST['id'], $moduleData);
                //修改成功写入日志
                $this->adminLog('MissionHall','修改MHTID为【'.(int)$_POST['MHTID'].'】的数据');
                $this->showSuccess('修改成功！',__URL__);
            }
        }
    }
    
}