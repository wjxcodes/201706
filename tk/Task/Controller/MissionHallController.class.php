<?php
/**
 * @author demo
 * @date 2015年10月20日
 */
namespace Task\Controller;
class MissionHallController extends BaseController {
    /**
     * 任务大厅列表页
     * @author demo
     */
    public function index(){
        $subject = SS('subjectParentId')[2]['sub'];
        $taskTypes = D('MissionHallTasks')->getLevel();
        //获取查询参数
        $type= I('type','0','trim');//任务类型
        $end= I('end','0','trim');//结束时间
        $rewardType= I('rewardType','0','trim');//奖励形式
        $timeOrder= I('time','','trim');//时间排序
        $hotOrder= I('hot','','trim');//热度排序
        $rewardOrder= I('reward','','trim');//奖励排序
        $subjectWhere = I('subject','','trim');//学科查询
        //查询条件
        if($subjectWhere){//学科
            $where[] = 'mha.SubjectID = '.(int)$subjectWhere ;
        }
        if($type){//任务级别
            $where[] = 'Level = '.(int)$type;
        }
        if($rewardType){//奖励类型
            $where[] = 'RewardType = '.(int)$rewardType ;
        }
        switch ($end){
            case 1: $where[] ='EndTime >'.time() . ' AND BeginTime > '.time(); break;//即将开始
            case 2: $where[] ='EndTime BETWEEN '.time().' AND '.(time()+3600*24*7); break;//一周以内
            case 3: $where[] ='EndTime BETWEEN '.time().' AND '.(time()+3600*24*7*30); break;//一月以内
            case 4: $where[] ='EndTime >'.(time()+3600*24*7*30); break;//长期有效
            default: $where[] = 'EndTime >'.time();break;
        }
        //排序
        if($hotOrder != ''){//热度排序
            if($hotOrder == '1'){
                $order['hot'] = 'Num DESC,AddTime DESC' ;
            }else{
                $order['hot'] = 'Num ASC,AddTime ASC';
            }
        }else if($timeOrder != ''){
            //时间排序
            if($timeOrder == '1'){
                $order['time'] = 'BeginTime DESC' ;
            }else{
                $order['time'] = 'BeginTime ASC';
            }
        }else if($rewardOrder != ''){
            //奖励排序
            if($rewardOrder == '1'){
                $order['reward'] = 'Reward DESC' ;
            }else{
                $order['reward'] = 'Reward ASC';
            }
        }
        
        $mhtasks = D('MissionHallTasks');
        $where = implode(' AND ', $where);
        
        /*分页start*/
        $count = $mhtasks->getList('count(mht.MHTID) as c',$where);//总记录数
        $count = $count[0]['c'];//总记录数
        $pageLimit = 10;//每页记录数
        $page = useToolFunction('WLNPage','init',array($count,$pageLimit,''));
        $nowPageParam = I('p',1,'intval');//当前页数参数
        $nowPage = page($count,$nowPageParam,$pageLimit);//规范当前页数
        $show = $page->show();// 分页显示输出
        /*分页end*/
        //获取数据 处理数据
        $htresult = $mhtasks->getList('mht.MHTID,mht.Title,mht.Description,RewardType,Reward,mht.Level,Hot,Num,mht.EndTime,mha.SubjectID',
                                    $where,implode(',', $order),$nowPage.','.$pageLimit);
        $levelArr = $mhtasks->getLevel();//任务等级数组
        foreach ($htresult as $i=>$rs){
            $endtime = ceil(($rs['EndTime']-time())/(3600*24));
            $htresult[$i]['EndDay'] = $endtime > 100 ? '长期' : $endtime.'天';
            $htresult[$i]['Num']=$rs['Num'];
            $htresult[$i]['Description']=formatString('getHtmlDescription',$rs['Description'],150);//去掉图片
            $tmp = $mhtasks->showReward($rs['RewardType'], $rs['Reward']);//前台奖励显示
            $htresult[$i]['Img'] = $tmp['Img'];
            $htresult[$i]['Reward'] = $tmp['Reward'].$tmp['RewardType'];
            $htresult[$i]['subjectName'] = $this->getSubjectName($subject,$rs['SubjectID']);//学科名
            $htresult[$i]['levelName'] = $levelArr[$rs['Level']].'任务';//任务等级
        }
        
        //分配模板变量
        if(IS_AJAX && isMobile() && $nowPageParam>$nowPage){//移动端请求分页 分页参数大于总页数
            $show = false;
            $htresult = false;
        }
        if(ceil($count/$pageLimit) > 1){//数据大于一页输出分页
            $this->assign('pages',$show);
        }
        
        $this->assign('result',$htresult);
        if(IS_AJAX && isMobile()){//移动端分页请求
            $this->display('indexWapPage');
            die;
        }
        
        $this->assign('subject',$subject);//学科
        $this->assign('subjectWhere',$subjectWhere);
        $this->assign('type',$type);
        $this->assign('end',$end);
        $this->assign('rewardType',$rewardType);
        $this->assign('time',$timeOrder);
        $this->assign('hot',$hotOrder);
        $this->assign('reward',$rewardOrder);
        $this->assign('taskTypes',$taskTypes);//任务类型
        $this->assign('typeName','任务大厅');
        if(isMobile()){//移动端访问 移动端模板
            $this->display('indexWap');
            die;
        }
        $isLogin = $this->getCookieUserID('Home');
        $this->assign('isLogin', !empty($isLogin));
        $this->display();
    }
    
    /**
     * 任务详情页
     * @author demo
     */
    public function taskDetails(){
        $thisUrl=U('/Task','',false);

        isset($_GET['id']) or $this->setError('81002',0,$thisUrl);
         
        $id = (int)$_GET['id'];
        $mhtModel = D('MissionHallTasks');
        $mhinfo = $mhtModel->getList('mht.*,mha.SubjectID','mht.MHTID = '.$id);
        if(empty($mhinfo)) $this->setError('81003',0,$thisUrl);//无任务
        
        /* 处理数据 */
        $mhinfo = $mhinfo[0];
        $tmp = $mhtModel->showReward($mhinfo['RewardType'], $mhinfo['Reward']);//前台奖励显示
        $mhinfo['Reward'] = $tmp['Reward'];
        $mhinfo['RewardType'] = $tmp['RewardType'].'奖励';
        $mhinfo['Img'] = $tmp['Img']; 
        $mhinfo['levelName'] = $mhtModel->getLevel()[$mhinfo['Level']].'任务';//任务等级
        
        //处理任务学科
        $subject = SS('subjectParentId')[2]['sub'];
        $mhinfo['subjectName'] = $this->getSubjectName($subject,$mhinfo['SubjectID']);
        //结束时间
        if($mhinfo['EndTime'] == '2000000000'){//任务长期
            $mhinfo['EndTime'] = '长期' ;
        }else if($mhinfo['EndTime'] > time()){
            $mhinfo['EndTime'] = date('Y-m-d H:i:s', $mhinfo['EndTime']);
        }else{
            $this->setError('81001',0,$thisUrl);//任务结束不再显示
        }
        //当前登陆用户是否已领取此任务
        $uid = $this->getCookieUserID('Home');
        $isReceived = TRUE;//默认未领取此任务
        if($uid && $this->getCookieUserName('Home')){
            $isReceived = D('MissionHallLog')->isCanReceive($uid, $id);
        }
        //替换内容标签
        $mhinfo['Description'] = str_replace(array("\r\n", "\r", "\n"), '<br>', $mhinfo['Description']);//任务描述
        $mhinfo['Description']=stripslashes_deep(formatString('IPReturn',$mhinfo['Description']));
        $mhinfo['personNum'] = $mhinfo['Num'];//参与人数
        $this->assign('isReceived', $isReceived);
        $this->assign('info',$mhinfo);
        $this->assign('typeName','任务详情');
        $this->taskRecordsAjax($id);//领取记录
        if(isMobile()){//移动端访问 移动端模板
            $this->display('taskDetailsWap');
            die;
        }
        $this->display();
    }
    
    /**
     * 获取学科二维数组中学科名名
     * @param 学科二维数组 $subjectArr
     * @param 学科ID $subjectID
     * @return String 
     * @author demo
     */
    protected function getSubjectName($subjectArr,$subjectID){
        $subject = array_values(array_filter($subjectArr, function($arr) use ($subjectID) { return $arr['SubjectID'] == $subjectID; }));
        return $subject[0]['SubjectName'];//获取学科名
    }
    
    /**
     * 任务记录分页效果ajax请求方式
     * @author demo
     */
    public function taskRecordsAjax($mhtid=''){
        if(IS_AJAX && $_REQUEST['MHTID']){
            $mhtid = (int)$_REQUEST['MHTID'];
        }elseif (empty($mhtid)){
            $this->setError('30309',1,U('/Task','',false));
        }
//         if(!IS_AJAX) $this->setError('30309',1,U('MissionHall/index'));
//         if($_REQUEST['id']){//获取对应任务的记录
//             $mhtid = $_REQUEST['id'];
            $page = $_REQUEST['p'];
            if(!$page){
                $page = 1;
            }
            $prepage = 20;//当前页记录数
            $result = D('MissionHallRecords')->getRecordList($mhtid, $page, $prepage);
            $list = array_shift($result);
            if(empty($list)){
                $list = array();
            }
            $pagtion = $this->fillPagtion(array('MHTID'=>$mhtid), $result[0], $page, $prepage, U('MissionHall/taskRecordsAjax','',false), 10);
            $this->assign('page', $pagtion['pages']);
            $this->assign('list', $list);
            if(isMobile()){//移动端访问
                if(IS_AJAX){
                    exit(json_encode(array('data'=>$this->fetch('taskDetailsRecordsWap'),'count'=>ceil($result[0]/$prepage))));
                }else{
                    $this->assign('page',$this->fetch('taskDetailsRecordsWap'));
                }
            }else {
                if(IS_AJAX){
                    exit($this->fetch('taskDetailsRecords'));
                }else{
                    $this->assign('page',$this->fetch('taskDetailsRecords'));
                }
            }
            
//         }else{
//             $this->setError('81002',0,U('MissionHall/index'));
//         }
    }
    
    /**
     * 领取任务（验证登录）
     * @author demo
     */
    public function receiveTask(){
        if(!IS_AJAX || empty($_POST['MHTID'])) return false;//非法请求
        $mhtID = (int)$_POST['MHTID'];
        /* 获取用户ID */
        $userID = $this->getCookieUserID('Home');
        if(empty($userID)) $this->setBack(array('status'=>0,'title'=>'请先登录'));
        /* 领取任务 */
        $mht = D('MissionHallTasks') ;
        $limit = $mht->getList('Url,Limit,EndTime','mht.MHTID ='.$mhtID,'',1);//任务是否需要审核 是否结束
        if($limit[0]['EndTime'] < time()){
            $this->setBack(array('status'=>1,'title'=>'任务已结束，不能再申请了'));
        }

        $log = D('MissionHallLog');
        $title = $log->isCanReceive($userID, $mhtID);
        if(is_string($title)){
            $this->setBack(array('status'=>1, 'title'=>$title));
        }
        
        //验证当前任务是否已经领取过
        $mhr = D('MissionHallRecords');
        $record = $mhr->isReceived($mhtID,$userID);
        if(empty($record)){
            $rid = $mhr->receiveTask($mhtID,$userID,$limit[0]['Limit']);
            $mht->updateColumn('Num = Num+1','MHTID ='.$mhtID);//人数加一
        }else{
            $rid = $record[0]['MHRID'];
        }
        if($rid !== false){
            $title= $limit[0]['Limit'] == 2 ? '申请成功，您已参与' : '申请成功，请等待审核';
            $link = false;
            if($limit[0]['Limit'] == 2 ){
                cookie('repeatTask', $rid, 3600*2);
                session('repeatTask', $rid);
                //直接领取的任务生成日志信息
                $log->add($rid, 1);
                $title = $limit[0]['Limit'] = '申请成功，您已参与';
                if(!empty($limit[0]['Url'])) $link = $limit[0]['Url'];
            }else {
                $log->add($rid, 0);
                $title = '申请成功，请等待审核';
                $link = '';
            }
            $this->setBack(array('status'=>2,'title'=>$title,'rt'=>$limit[0]['Limit'],'link'=>$link));
        }
    }
    
    /**
     * 删除cookie，用于任务大厅一次领取，多次做任务的
     * @author demo 2015-12-10
     */
    public function clearSession(){
        session('repeatTask', null);
    }

    /**
     * 任务列表
     * @author demo 
     */
    public function myTask(){
        $params['p'] = $_REQUEST['p'];
        if(!$params['p']){
            $params['p'] = 1;
        }
        if($_REQUEST['startTime'] && $_REQUEST['startTime'] != '开始时间'){
            $map['startTime'] = $params['startTime'] = $_REQUEST['startTime'];
        }
        if($_REQUEST['endTime'] && $_REQUEST['endTime'] != '结束时间'){
            $map['endTime'] = $params['endTime'] = $_REQUEST['endTime'];
        }
        if($_REQUEST['level']){
            $map['level'] = $params['level'] = $_REQUEST['level'];
        }
        if(isset($_REQUEST['status']) && $_REQUEST['status'] >= 0){
            $map['status'] = $params['status'] = (int)$_REQUEST['status'];
        }
        $params['prepage'] = 20;
        $params['field'] = array('l.MHLID,t.Title,t.RewardType, t.Reward,  t.Url,l.AddTime as recordAddTime,l.Status');
        $params['uid'] = $this->getCookieUserID('Home');
        $mhr = $this->getModel('MissionHallLog')->recordList($params);
        $pagtion = $this->fillPagtion($map, $mhr[1], $params['p'], $params['prepage'], '/Task/MissionHall/myTask', 5);
        $this->assign('pagtion', $pagtion);
        $this->assign('list', $mhr[0]);
        $this->assign('count', $mhr[1]);
        $this->assign('page', $params['p']);
        $this->assign('prepage', $params['prepage']);
        $status = array(1=>'申请中', 2=>'已领取', 3=>'已终止', 4=>'拒绝', 5=>'完成');
        $this->assign('statusList',$status);
        $this->assign('level', $this->getModel('MissionHallTasks')->getLevel());
        $this->display();
    }
}