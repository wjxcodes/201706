<?php
/**
 * @author demo
 * @date 2014年11月11日
 */
/**
 * 校本题库任务领取列表模型类，用于查看试题领取列表相关操作
 */
namespace Custom\Manage;
class CustomTestTaskListManage extends BaseController  {
    var $moduleName = '校本题库领取列表';
    
    /**
     * 校本题库试题领取列表查看(分学科，查看)
     * @author demo
     */
     public function index(){
        $pageName='任务领取列表';
        $data=' 1=1 '; //初始化条件
        $map=array(); //分页条件
        //浏览谁的试题.区分学科
        if($this->ifSubject && $this->mySubject){
             $data .= 'and b.SubjectID in ('.$this->mySubject.') ';
         }
        if ($_REQUEST['name'] || $_REQUEST['name']==='0') {
             //简单查询
            if(is_numeric($_REQUEST['name'])){
                $map['name'] = $_REQUEST['name'];
                $data .= ' and a.TestID='.$_REQUEST['name'];
            }else{
                $this->setError('30502');
            }
        }else {
             //高级查询
            if ($_REQUEST['TestID']) {
                if($_REQUEST['TestID']){
                    $map['TestID'] = $_REQUEST['TestID'];
                    $data .= ' and a.TestID='.$_REQUEST['TestID'];
                }else{
                    $this->setError('30502');
                }

            }
            if ($_REQUEST['UserName']) {
                $map['UserName'] = $_REQUEST['UserName'];
                $data .= ' and a.UserName like "%'.$_REQUEST['UserName'].'%"';
            }
            if ($_REQUEST['SubjectID']) {
                 $map['SubjectID'] = $_REQUEST['SubjectID'];
                 $data .= ' and b.SubjectID='.$_REQUEST['SubjectID'];
            }
            if (is_numeric($_REQUEST['Status'])) {
                 $map['Status'] = $_REQUEST['Status'];
                 $data .= ' and a.Status="'.$_REQUEST['Status'].'"';
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
         $perPage = C('WLN_PERPAGE'); //每页 页数
         $customTestTaskList = $this->getModel('CustomTestTaskList');
         $count=$customTestTaskList->customTestTaskListSelectCount($data);
         $this->pageList($count, $perPage, $map);
         $page=page($count,$_GET['p'],$perPage).','.$perPage;
         $list=$customTestTaskList->customTestTaskListSelectByPage($data,$page);
         $subject = $this->getApiCommon('Subject/subject');
         $testStatus=array(  //试题状态
             '-2'=>'失败',
             '-1'=>'失败返回',
             '0'=>'领取',
             '1'=>'审核中',
             '2'=>'完成'
         );
         //获取记录详细信息
         foreach($list as $i=>$iList){
             if($iList['TestStatus']==0 && $iList['TaskTime']<time()){
                 $list[$i]['Status']='已过期';
             }else{
                 $list[$i]['Status']=$testStatus[$iList['TestStatus']];
             }
             $list[$i]['SubjectName']=$subject[$subject[$list[$i]['SubjectID']]['PID']]['SubjectName'].$subject[$list[$i]['SubjectID']]['SubjectName'];
         }
         $subjectArray=$this->getApiCommon('Subject/subjectParentID'); //获取学科数据集
         /*载入模板标签*/
         $this->assign('list', $list); // 赋值数据集
         $this->assign('subjectArray', $subjectArray);
         $this->assign('testStatus', $testStatus);
         $this->assign('pageName', $pageName);
         $this->display();
     }

    /**
     * 超时统计列表
     * @author demo
     */
    public function overGroup(){
        $pageName='超时统计列表';
        $time=time();
        $data=' 1=1 and a.Status=0 and a.IfDel=0 and a.TaskTime<'.$time; //初始化条件
        $map=array(); //分页条件

        //浏览谁的试题.区分学科
        if($this->ifSubject && $this->mySubject){
            $data .= 'and b.SubjectStyle in ('.$this->mySubject.') ';
        }
        if ($_REQUEST['name'] || $_REQUEST['name']==='0') {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' and a.UserName='.$_REQUEST['name'];
        }else {
            //高级查询
            if ($_REQUEST['UserName']) {
                $map['UserName'] = $_REQUEST['UserName'];
                $data .= ' AND a.UserName like "%' . $_REQUEST['UserName'] . '%" ';
            }
            $start = $_REQUEST['Start'];
            $end = $_REQUEST['End'];
            if ($start) {
                if (empty ($end))
                    $end = date('Y-m-d', time());
                if (!checkString('isDate',$start) || !checkString('isDate',$end)) {
                    $this->setError('30719'); //日期格式不正确
                }
                $data .= ' AND a.TaskTime between ' . strtotime($start) . ' and ' . strtotime($end) . ' ';
            }
            if ($_REQUEST['SubjectID']) {
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $data .= ' and b.SubjectStyle='.$_REQUEST['SubjectID'];
            }
        }
        $perPage = C('WLN_PERPAGE'); //每页 页数
        $model = $this->getModel('CustomTestTaskList');
        //获取数据，即SQL语句，第三张表
        $asTable=$model->customTestTaskListSelectByWhere($data);
        //根据第三表进行分组统计
        $list=$model->customTestTaskListGroupBy($asTable);
        //获取总数
        $count=count($list); //统计总数
        $page=page($count,$_GET['p'],$perPage).','.$perPage;
        //分页查询
        $list=$model->customTestTaskListGroupPage($asTable,$page);
        $subject = $this->getApiCommon('Subject/subject');
        foreach($list as $i=>$iList){
            $list[$i]['start']=strtotime($start);
            $list[$i]['end']=strtotime($end);
            $list[$i]['SubjectName']=$subject[$subject[$list[$i]['SubjectStyle']]['PID']]['SubjectName'].$subject[$list[$i]['SubjectStyle']]['SubjectName'];
        }
       if(empty($start) && empty($end)){ //如果不选择时间，不显示数据
            $list='';
        }else{
            $this->pageList($count, $perPage, $map);
            $this->assign('start', $start);
            $this->assign('end', $end);
        }
        $subjectArray=$this->getApiCommon('Subject/subjectParentID'); //获取学科数据集
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName);
        $this->display();
    }

    /**
     * 根据用户名，查看该用户的所有超时记录及扣分情况
     * @author demo
     */
    public function showUserList(){
        $pageName='用户记录详情列表';
        $userName=$_REQUEST['UserName'];
        $end = date('Y-m-d', $_REQUEST['End']);
        $start = date('Y-m-d', $_REQUEST['Start']);
        $data=' 1=1 and a.UserName="'.$userName.'"'; //初始化条件
        $map=array(); //分页条件
        if ($_REQUEST['name'] || $_REQUEST['name']==='0') {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' and a.TestID='.$_REQUEST['name'];
        }else {
            //高级查询
            if ($_REQUEST['TestID']) {
                $map['TestID'] = $_REQUEST['TestID'];
                $data .= ' and a.TestID='.$_REQUEST['TestID'];
            }
            $start = $_REQUEST['Start'];
            $end = $_REQUEST['End'];
            if ($start) {
                if(!empty($start) && !checkString('isDate',$start)){
                    $start = date('Y-m-d', $start);
                    
                }
                if(!empty($end) && !checkString('isDate',$end)){
                    $end = date('Y-m-d', $end);
                    
                }
                if (empty ($end))
                    $end = date('Y-m-d', time());
                if (!checkString('isDate',$start) || !checkString('isDate',$end)) {
                    $this->setError('30719'); //日期格式不正确
                }
                $map['start']=strtotime($start);
                $map['end']=strtotime($end);
                $map['UserName']=$userName;
                $data .= ' AND a.TaskTime between ' . strtotime($start) . ' and ' . strtotime($end) . ' ';
            }
        }
        $perPage = C('WLN_PERPAGE'); //每页 页数
        //统计总数
        $model = $this->getModel('CustomTestTaskList');
        $count=$model->customTestTaskListSelectCountByWhere($data);
        $page=page($count,$_GET['p'],$perPage).','.$perPage;
        //分页查看数据
        $list=$model->customTestTaskListSelectByUserPage($data,$page);
        //获取用户档案信息
        $userMsg=$this->getModel('User')->selectData(
            '*',
            'UserName ="'.$userName.'"'
        );
        
        $subject = $this->getApiCommon('Subject/subject');
        foreach($list as $i=>$iList){
            $list[$i]['SubjectName']=$subject[$subject[$list[$i]['SubjectStyle']]['PID']]['SubjectName'].$subject[$list[$i]['SubjectStyle']]['SubjectName'];
        }
        $subjectArray=$this->getApiCommon('Subject/subjectParentID'); //获取学科数据集
        /*载入模板标签*/
        $this->pageList($count, $perPage, $map);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName);
        $this->assign('start', $start);
        $this->assign('end', $end);
        $this->assign('userMsg',$userMsg[0]);
        $this->display();
    }

    /**
     * 根据ajax参数扣除分值
     * @author demo
     */
    public function delPoint(){
        $start = $_POST['start'];
        $end = $_POST['end'];
        $userName=$_POST['UserName'];
        $total=$_POST['Total'];
        $listID=$_POST['ListID'];
        $point=C('WLN_TASK_TIMEOUT_DELPOINT');
        $where='1=1';
        if($userName){
            $where.=' and UserName="'.$userName.'"';
        }
        if($start){
            if (empty ($end))
                $end = date('Y-m-d', time());
            if (!checkString('isDate',$start) || !checkString('isDate',$end)) {
                $this->setError('30719'); //日期格式不正确
            }
            $where.= ' AND TaskTime between ' . strtotime($start) . ' and ' . strtotime($end) . ' ';
        }
        //根据用户名跟时间段查看符合条件的内容 CustomTestTaskList
        if(empty($listID)){
            $result=$this->getModel('CustomTestTaskList')->selectData(
                 'ListID',
                 $where);
            foreach($result as $i=>$iResult){
                $idArr[]=$iResult['ListID'];
            }
        }else{
            $result=true;
            $idArr[0]=$listID;
        }
        if($result){
            $idStr=implode(',',$idArr);
            //修改TaskList表中ifDel中的状态
            $res=$this->getModel('CustomTestTaskList')->updateData(
                array('IfDel'=>'1'),
                'ListID in ('.$idStr.')');
            //修改User表中的用户分值
            if($res){
                $cutPoint=$total*$point; //计算需要扣分总数
                $userMsg=$this->getModel('User')->selectData(
                    'Cz',
                    array('UserName'=>$userName)
                );
                //计算余额
                $point=$userMsg[0]['Cz']-$cutPoint;
                $lastResult=$this->getModel('User')->updateData(
                    array('Cz'=>$point),
                    'UserName="'.$userName.'"'
                );
            }
        }
        $data['status']='';
        if($lastResult){
            $data['status']='success';
            $data['lastMoney']= $point;
        }
        $this->setBack($data);
    }
}