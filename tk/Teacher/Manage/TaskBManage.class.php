<?php
/**
 * @author demo
 * @date
 * @updatetime 2015.1.19
 */
/**
 * 试卷标引任务分配类，用于试卷标引任务分配的相关操作
 */
namespace Teacher\Manage;
class TaskBManage extends BaseController  {
    var $moduleName = '试卷标引分配';
    /**
     * 浏览试卷任务列表
     * @author demo
     */
    public function index() {
        $pageName = '试卷任务分配';
        $admin=$this->getCookieUserName();
        $map=array();
        $data=' 1=1 ';
        if ($this->ifSubject && $this->mySubject){ //判断是否区分学科
            $data .= ' AND t.SubjectID in (' . $this->mySubject . ') ';
        }else if($this->ifDiff){//判断是否区分用户
            $data.=' and t.Admin = "'.$admin.'"';
        }
        if($_REQUEST['name']){
            //简单查询
            $map['name']=$_REQUEST['name'];
            $data.=' AND t.UserName = "'.$_REQUEST['name'].'" ';
        }else{
            //高级查询WorkID
            if($_REQUEST['UserName']){
                $map['UserName']=$_REQUEST['UserName'];
                $data.=' AND t.UserName = "'.$_REQUEST['UserName'].'" ';
            }
            if($_REQUEST['DocID']){
                $map['DocID']=$_REQUEST['DocID'];
                $data.=' AND tl.DocID = "'.$_REQUEST['DocID'].'" ';
            }
            if($_REQUEST['CheckName']){
                $map['CheckName']=$_REQUEST['CheckName'];
                $data.=' AND wc.UserName = "'.$_REQUEST['CheckName'].'" ';
            }
            if($_REQUEST['WorkID']){
                if(is_numeric($_REQUEST['WorkID'])){
                    $map['WorkID']=$_REQUEST['WorkID'];
                    $data.=' AND t.WorkID = "'.$_REQUEST['WorkID'].'" ';
                }else{
                    $this->setError('30502');
                }
            }
            if($_REQUEST['Status'] || is_numeric($_REQUEST['Status'])){
                $map['Status']=$_REQUEST['Status'];
                if(strstr($_REQUEST['Status'],',')){
                    $tmparr=explode(',',$_REQUEST['Status']);
                    $data.=' AND t.Status = "'.$tmparr[0].'" ';
                    if(is_numeric($tmparr[1]))
                        $data.=' AND wc.Status = "'.$tmparr[1].'" ';
                    else
                        $data.=' AND wc.Status is '.$tmparr[1].' ';
                }else
                    $data.=' AND t.Status = "'.$_REQUEST['Status'].'" ';
            }
        }
        $TeacherWorkCheck = $this->getModel('TeacherWorkCheck');
        $TeacherWork = $this->getModel('TeacherWork');
        $perPage=C('WLN_PERPAGE');
        if(!empty($_REQUEST['DocID'])){
            $count = $TeacherWork->unionSelect('teacherCountDataByDocID', $data); // 查询满足要求的总记录数
        }else{
            $count = $TeacherWork->unionSelect('teacherCountData', $data); // 查询满足要求的总记录数
        }
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=page($count,$_GET['p'],$perPage).','.$perPage;
        if(!empty($_REQUEST['DocID'])){
            $list = $TeacherWorkCheck->unionSelect('teacherPageDataByDocID',$data,$page); // 查询满足要求的总记录数
        }else{
            $list=$TeacherWorkCheck->unionSelect('teacherPageData',$data,$page);
        }
        if($list){
            $idList=array();
            foreach($list as $i=>$iList){
                $idList[]=$iList['WorkID'];
            }
            $buffer=$TeacherWorkCheck->selectData(
                    'WCID,WorkID,UserName,CheckTimes',
                    'WorkID in ('.implode(',',$idList).')',
                    'CheckTimes DESC');
            if($buffer){
                //获取用户真实姓名
                $userList=array();
                foreach($buffer as $i=>$iBuffer){
                    $userList[]=$iBuffer['UserName'];
                }
                $userBuffer=$this->getModel('User')->selectData(
                    'UserName,RealName',
                    'UserName in ("'.implode('","',$userList).'")');
                $userList=array();
                foreach($userBuffer as $i=>$iUserBuffer){
                    $userList[$iUserBuffer['UserName']]=$iUserBuffer['RealName'];
                }
                //获取曾经审核用户
                $workList=array();
                foreach($buffer as $i=>$iBuffer){
                    if($userList[$iBuffer['UserName']]) $iBuffer['RealName']=$userList[$iBuffer['UserName']];
                    $workList[$iBuffer['WorkID']][$iBuffer['CheckTimes']]=$iBuffer;
                }
                foreach($list as $i=>$iList){
                    if($iList['CheckTimes'])
                    $list[$i]['oldChecker']=$workList[$iList['WorkID']];
                }
            }
        }
        $this->pageList($count,$perPage,$map);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 标引任务统计列表浏览
     * @author demo
     */
    public function tongji() {
        $pageName='标引任务统计';

        //获取学科树形数据集
        $subjectArray=SS('subjectParentId');
        $this->assign('subjectArray', $subjectArray); //学科树形数据集

        $where='1=1'; //查询条件
        //处理查询时间
        $time1=$_POST['LastTime1'];
        $time2=$_POST['LastTime2'];
        if($time1 || $time2){
            $time1=strtotime($time1);
            $time2=strtotime($time2);
            if($time1 and $time2){
                $where.=' and LastTime between '.$time1.' and '.($time2+3600*24);
            }else if($time1){
                $where.=' and LastTime >= '.$time1;
            }else if($time2){
                $where.=' and LastTime <= '.($time2+3600*24);
            }
        }else{
            $this->assign('pageName', $pageName); //页面标题
            $this->display();
            exit();
        }
        //查询用户名
        $userName=$_POST['UserName'];
        if($userName){
            $where.=' and UserName = "'.$userName.'"';
        }
        //查询学科
        $subjectID=$_POST['SubjectID'];
        if($subjectID){
            $where.=' and SubjectID = "'.$subjectID.'" ';
        }
        //判断权限
        if ($this->ifSubject && $this->mySubject){
            $where .= ' and SubjectID in (' . $this->mySubject . ') ';
        }else if($this->ifDiff){//判断是否区分用户
            $admin=$this->getCookieUserName();
            $where.=' and Admin = "'.$admin.'" ';
        }
        $bufferTw=$this->getModel('TeacherWork')->selectData(
            'WorkID,UserName,Status',
            $where,
            'UserName ASC');

        if($bufferTw){
            $workArray=array(); //存储任务id数据集
            $userArray=array(); //存储用户名数据集
            foreach($bufferTw as $iBufferTw){
                $workArray[]=$iBufferTw['WorkID'];
            }
            $bufferTwl=$this->getModel('TeacherWorkList')->selectData(
                'WorkID,DocID,Status',
                'WorkID in ('.implode(',',$workArray).')');
            $tmpArrTw=array();
            $tmpArrTwl=array();
            foreach($bufferTwl as $iBufferTwl){
                if(!$tmpArrTwl[$iBufferTwl['WorkID']]) $tmpArrTwl[$iBufferTwl['WorkID']]=1;
                else $tmpArrTwl[$iBufferTwl['WorkID']]++;
            }
            foreach($bufferTw as $iBufferTw){
                if(!$tmpArrTw[$iBufferTw['UserName']][$iBufferTw['Status']]['docnum']){
                    $tmpArrTw[$iBufferTw['UserName']][$iBufferTw['Status']]['docnum']=$tmpArrTwl[$iBufferTw['WorkID']];
                }else{
                    $tmpArrTw[$iBufferTw['UserName']][$iBufferTw['Status']]['docnum']+=$tmpArrTwl[$iBufferTw['WorkID']];
                }
                if(!$tmpArrTw[$iBufferTw['UserName']][$iBufferTw['Status']]['tasknum']){
                    $tmpArrTw[$iBufferTw['UserName']][$iBufferTw['Status']]['tasknum']=1;
                }else{
                    $tmpArrTw[$iBufferTw['UserName']][$iBufferTw['Status']]['tasknum']++;
                }
                $userArray[]=$iBufferTw['UserName'];
            }
            $userArray=$this->getModel('User')->selectData(
                'RealName,UserName',
                'UserName in ("'.implode('","',$userArray).'")');
            foreach($userArray as $iUserArray){
                $userArray[$iUserArray['UserName']]=$iUserArray['RealName'];
            }
            foreach($bufferTw as $iBufferTw){
                $tmpArrTw[$iBufferTw['UserName']]['RealName']=$userArray[$iBufferTw['UserName']];
            }
        }
        $this->assign('list', $tmpArrTw); //统计数据
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 添加试卷标引
     * @author demo
     */
    public function add(){
        $pageName = '添加试卷标引';
        $act = 'add'; //模板标识

        //学科
        $subjectArray=SS('subjectParentId'); //获取学科数据集

        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 任务信息统计
     * @author demo
     */
    public function info() {

        $pageName = '任务信息';
        $userName=$_POST['username'];
        $lastTime1=$_POST['LastTime1'];
        $lastTime2=$_POST['LastTime2'];

        if(!$userName){
            $this->setError('30301'); //数据标示为空！
        }

        $where=' UserName="'.$userName.'" ';
        $lastTime1=strtotime(str_replace(',','-',$lastTime1));
        $lastTime2=strtotime(str_replace(',','-',$lastTime2));
        $search=array();
        $search['UserName']=$userName;
        $search['Time']='不限';
        if($this->ifSubject && $this->mySubject){
            $where .= ' and SubjectID in ('.$this->mySubject.') ';
        }
        if($lastTime1 and $lastTime2){
            $where.=' and LastTime between '.$lastTime1.' and '.($lastTime2+3600*24);
            $search['Time']=date('Y-m-d',$lastTime1).' - '.date('Y-m-d',$lastTime2);
        }else if($lastTime1){
            $where.=' and LastTime >='.$lastTime1;
            $search['Time']=date('Y-m-d',$lastTime1).' - '.date('Y-m-d',time());
        }else if($lastTime2){
            $where.=' and LastTime <='.$lastTime2;
            $search['Time']=date('Y-m-d',0).' - '.($lastTime2+3600*24);
        }
        $buffer=$this->getModel('TeacherWork')->selectData(
            'WorkID,Status',
            $where);
        if(!$buffer){
            $this->setError('40502'); //没有任务信息！
        }
        //获取任务id
        $workidlist=array();
        foreach($buffer as $iBuffer){
            if($iBuffer['Status']==2) $workidlist[]=$iBuffer['WorkID'];
        }

        //获取文档id
        $buffer='';
        if($workidlist){
            $buffer=$this->getModel('TeacherWorkList')->selectData(
                'DocID',
                'WorkID in ('.implode(',',$workidlist).')');
        }

        if(!$buffer){
            $this->setError('40506'); //文档未标引，没有数据！
        }

        //获取文档数据
        $wordidlist=array();
        foreach($buffer as $iBuffer){
            $wordidlist[]=$iBuffer['DocID'];
        }
        $buffer=$this->getModel('Doc')->selectData(
            'DocID,DocName',
            'DocID in ('.implode(',',$wordidlist).')');
        if(!$buffer){
            $this->setError('40507'); //没有文档数据！
        }
        $docArr=array();
        foreach($buffer as $iBuffer){
            $docArr[$iBuffer['DocID']]=$iBuffer['DocName'];
        }

        $buffer=$this->getModel('TestAttrReal')->groupData(
            'DocID,COUNT(TestID) as num',
            'DocID in ('.implode(',',$wordidlist).') AND Duplicate=0',
            'DocID');

        $tongJi=array();
        foreach($buffer as $i=>$iBuffer){
            $buffer[$i]['DocName']=$docArr[$iBuffer['DocID']];
            if($iBuffer['num']<10){
                if(!$tongJi[0]['xtnum']) $tongJi[0]['xtnum']=$iBuffer['num'];
                else $tongJi[0]['xtnum']+=$iBuffer['num'];
                if(!$tongJi[0]['xtdocnum']) $tongJi[0]['xtdocnum']=1;
                else $tongJi[0]['xtdocnum']++;
            }else{
                if(!$tongJi[0]['dtnum']) $tongJi[0]['dtnum']=$iBuffer['num'];
                else $tongJi[0]['dtnum']+=$iBuffer['num'];
                if(!$tongJi[0]['dtdocnum']) $tongJi[0]['dtdocnum']=1;
                else $tongJi[0]['dtdocnum']++;
            }
        }

        $buffer1=$this->getModel('TestAttr')->groupData(
            'DocID,COUNT(TestID) as num',
            'DocID in ('.implode(',',$wordidlist).') AND Duplicate=0',
            'DocID');
        foreach($buffer1 as $i=>$iBuffer){
            $buffer1[$i]['DocName']=$docArr[$iBuffer['DocID']];
            if($iBuffer['num']<10){
                if(!$tongJi[1]['xtnum']) $tongJi[1]['xtnum']=$iBuffer['num'];
                else $tongJi[1]['xtnum']+=$iBuffer['num'];
                if(!$tongJi[1]['xtdocnum']) $tongJi[1]['xtdocnum']=1;
                else $tongJi[1]['xtdocnum']++;
            }else{
                if(!$tongJi[1]['dtnum']) $tongJi[1]['dtnum']=$iBuffer['num'];
                else $tongJi[1]['dtnum']+=$iBuffer['num'];
                if(!$tongJi[1]['dtdocnum']) $tongJi[1]['dtdocnum']=1;
                else $tongJi[1]['dtdocnum']++;
            }
        }

        /*载入模板标签*/
        $this->assign('search', $search);
        $this->assign('alltongji', $tongJi);
        $this->assign('list', $buffer);
        $this->assign('list1', $buffer1);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 审核统计浏览
     * @author demo
     * */
    public function stongji()
    {

        $pageName = '审核任务统计';
        $where='1=1';

        $time1=$_POST['LastTime1'];
        $time2=$_POST['LastTime2'];
        if($time1 || $time2){
            $time1=strtotime($time1);
            $time2=strtotime($time2);
            if($time1 and $time2){
                $where.=' and LastTime between '.$time1.' and '.($time2+3600*24);
            }else if($time1){
                $where.=' and LastTime >= '.$time1;
            }else if($time2){
                $where.=' and LastTime <= '.($time2+3600*24);
            }
        }else{
            $this->assign('pageName', $pageName); //页面标题
            $this->display();
            exit();
        }

        $userName=$_POST['UserName'];
        if($userName){
            $where.=' AND UserName = "'.$userName.'" ';
        }
        if($this->ifSubject && $this->mySubject){
            $where .= ' AND SubjectID in ('.$this->mySubject.') ';
        }
        $twc = $this->getModel('TeacherWorkCheck')->selectData(
            'WorkID,UserName,Status,CheckTimes,WCID',
            $where,
            'UserName asc');
        if ($twc) {
            $workArray=array();
            $userArray=array();
            foreach($twc as $iTwc){
                $workArray[]=$iTwc['WorkID'];
            }
            $twl=$this->getModel('TeacherWorkList')->selectData(
                'WorkID,DocID,Status',
                'WorkID in ('.implode(',',$workArray).')');

            $arrTwc = array(); //存储统计数据集
            $arrTwl = array(); //存储任务id
            $wcArray = array(); //存储审核id

            foreach ($twl as $iTwl) {
                if (!$arrTwl[$iTwl['WorkID']]) $arrTwl[$iTwl['WorkID']] = 1;
                else $arrTwl[$iTwl['WorkID']]++;
            }
            foreach ($twc as $iTwc) {
                // 文档数
                if (!$arrTwc[$iTwc['UserName']][$iTwc['Status']]['docnum']) {
                    $arrTwc[$iTwc['UserName']][$iTwc['Status']]['docnum'] = $arrTwl[$iTwc['WorkID']];
                } else {
                    $arrTwc[$iTwc['UserName']][$iTwc['Status']]['docnum'] += $arrTwl[$iTwc['WorkID']];
                }
                // 任务数
                if (!$arrTwc[$iTwc['UserName']][$iTwc['Status']]['tasknum']) {
                    $arrTwc[$iTwc['UserName']][$iTwc['Status']]['tasknum'] = 1;
                } else {
                    $arrTwc[$iTwc['UserName']][$iTwc['Status']]['tasknum']++;
                }
                if($iTwc['Status']==2) $wcArray[]=$iTwc['WCID']; // 记录审核id
                $userArray[]=$iTwc['UserName']; //记录用户名
            }

            //审核情况
            $buffer=$this->getModel('TeacherWorkTestAttr')->unionSelect('teacherSumCheck', $wcArray);
            foreach ($buffer as $iBuffer) {
                //审核人员审出错误
                if (!$arrTwc[$iTwc['UserName']][$iTwc['Status']]['docnum']){
                    $arrTwc[$iBuffer['UserName']]['rightNum'] = $iBuffer['RightNum'];
                    $arrTwc[$iBuffer['UserName']]['loseNum'] = $iBuffer['LoseNum'];
                }else{
                    $arrTwc[$iBuffer['UserName']]['rightNum'] += $iBuffer['RightNum'];
                    $arrTwc[$iBuffer['UserName']]['loseNum'] += $iBuffer['LoseNum'];
                }
                // 管理员审出错误
                if(!$arrTwc[$iBuffer['UserName']]['checkNum']){
                    $arrTwc[$iBuffer['UserName']]['checkNum']= $iBuffer['CheckNum'];
                    $arrTwc[$iBuffer['UserName']]['checkLoseNum']= $iBuffer['CheckLoseNum'];
                }else{
                    $arrTwc[$iBuffer['UserName']]['checkLoseNum']+= $iBuffer['CheckLoseNum'];
                    $arrTwc[$iBuffer['UserName']]['checkNum'] += $iBuffer['CheckNum'];
                }
            }
            //用户真实姓名
            $u=$this->getModel('User')->selectData(
                'RealName,UserName',
                'UserName in ("'.implode('","',$userArray).'") ');
            foreach($u as $iU){
                $userArray[$iU['UserName']]=$iU['RealName'];
            }
            foreach($twc as $iTwc){
                $arrTwc[$iTwc['UserName']]['RealName']=$userArray[$iTwc['UserName']];
            }
        }
        $this->assign('list', $arrTwc); //审核统计数据
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 统计审核详细
     * @author demo
     * */
    public function sinfo(){

        $pageName = '审核任务信息';
        $userName = $_POST['username'];
        $lastTime1 = $_POST['LastTime1'];
        $lastTime2 = $_POST['LastTime2'];
        if (!$userName) {
            $this->setError('30301'); //数据标示为空！
        }

        $where = ' UserName="' . $userName . '" ';
        $lastTime1 = strtotime(str_replace(',', '-', $lastTime1));
        $lastTime2 = strtotime(str_replace(',', '-', $lastTime2));
        $search = array();
        if($this->ifSubject && $this->mySubject){
            $where .= ' AND SubjectID in ('.$this->mySubject.') ';
        }
        $search['UserName'] = $userName;
        $search['Time'] = '不限';
        if ($lastTime1 and $lastTime2) {
            $where .= ' and LastTime between ' . $lastTime1 . ' and ' . ($lastTime2 + 3600 * 24);
            $search['Time'] = date('Y-m-d', $lastTime1) . ' - ' . date('Y-m-d', $lastTime2);
        } else if ($lastTime1) {
            $where .= ' and LastTime >=' . $lastTime1;
            $search['Time'] = date('Y-m-d', $lastTime1) . ' - ' . date('Y-m-d', time());
        } else if ($lastTime2) {
            $where .= ' and LastTime <=' . $lastTime2;
            $search['Time'] = date('Y-m-d', 0) . ' - ' . ($lastTime2 + 3600 * 24);
        }
        $buffer = $this->getModel('TeacherWorkCheck')->selectData(
            'WorkID,WCID,Status',
            $where);
        if (!$buffer) {
            $this->setError('40502'); //没有任务信息！
        }
        //获取任务id
        $workidlist = array();
        $wclist = array();
        foreach ($buffer as $iBuffer) {
            if ($iBuffer['Status'] == 2) {
                $workidlist[] = $iBuffer['WorkID'];
                $wclist[] = $iBuffer['WCID'];
            }
        }
        //获取文档id
        $buffer = $this->getModel('TeacherWorkList')->selectData(
            'DocID',
            'WorkID in (' . implode(',', $workidlist) . ')');
        if (!$buffer) {
            $this->setError('40507'); //没有文档信息！
        }
        $wordidlist = array();
        foreach ($buffer as $iBuffer) {
            $wordidlist[] = $iBuffer['DocID'];
        }
        $buffer = $this->getModel('Doc')->selectData(
            'DocID,DocName',
            'DocID in (' . implode(',', $wordidlist) . ')');
        if (!$buffer) {
            $this->setError('40507'); //没有文档数据！
        }
        $docArr = array(); //文档id
        foreach ($buffer as $iBuffer) {
            $docArr[$iBuffer['DocID']] = $iBuffer['DocName'];
        }
        $tongJi = array();
        $buffer = $this->getModel('TestAttrReal')->groupData(
            'DocID,COUNT(TestID) as num',
            'DocID in (' . implode(',', $wordidlist) . ') AND Duplicate=0',
            'DocID');
        $buffer2 = $this->getModel('TestAttr')->groupData(
            'DocID,COUNT(TestID) as num',
            'DocID in (' . implode(',', $wordidlist) . ') AND Duplicate=0',
            'DocID');
        $TestAttrReal = $this->getModel('TestAttrReal');
        $docSumArray=$TestAttrReal->unionSelect('teacherSumDoc', $wclist,1);
        $docSumArray2=$TestAttrReal->unionSelect('teacherSumDoc', $wclist);
        $sumArray=array();
        $sumArray2=array();
        foreach ($docSumArray as $i => $iDocSumArray) {
            $sumArray[$iDocSumArray['DocID']][0]=$iDocSumArray['RightNum'];
            $sumArray[$iDocSumArray['DocID']][1]=$iDocSumArray['CheckNum'];
            $sumArray[$iDocSumArray['DocID']][2]=$iDocSumArray['TestRight']+$iDocSumArray['TestLoseRight'];
            $sumArray[$iDocSumArray['DocID']][3]=$iDocSumArray['TestCheck']+$iDocSumArray['TestLoseCheck'];
            $sumArray[$iDocSumArray['DocID']][4]=$iDocSumArray['LoseNum'];
            $sumArray[$iDocSumArray['DocID']][5]=$iDocSumArray['CheckLoseNum'];
        }
        foreach ($docSumArray2 as $i => $iDocSumArray2) {
            $sumArray2[$iDocSumArray2['DocID']][0]=$iDocSumArray2['RightNum'];
            $sumArray2[$iDocSumArray2['DocID']][1]=$iDocSumArray2['CheckNum'];
            $sumArray2[$iDocSumArray2['DocID']][2]=$iDocSumArray2['TestRight']+$iDocSumArray2['TestLoseRight'];
            $sumArray2[$iDocSumArray2['DocID']][3]=$iDocSumArray2['TestCheck']+$iDocSumArray2['TestLoseCheck'];
            $sumArray2[$iDocSumArray2['DocID']][4]=$iDocSumArray2['LoseNum'];
            $sumArray2[$iDocSumArray2['DocID']][5]=$iDocSumArray2['CheckLoseNum'];
        }
        foreach ($buffer as $i => $iBuffer) {
            $buffer[$i]['DocName'] = $docArr[$iBuffer['DocID']];
            $buffer[$i]['RightNum'] = $sumArray2[$iBuffer['DocID']][0];
            $buffer[$i]['CheckNum'] = $sumArray2[$iBuffer['DocID']][1];
            $buffer[$i]['TestRight'] = $sumArray2[$iBuffer['DocID']][2];
            $buffer[$i]['TestCheck'] = $sumArray2[$iBuffer['DocID']][3];
            $buffer[$i]['TestError'] = $sumArray2[$iBuffer['DocID']][2]-$sumArray2[$iBuffer['DocID']][3];
            $buffer[$i]['LoseNum'] = $sumArray2[$iBuffer['DocID']][4];
            $buffer[$i]['CheckLoseNum'] = $sumArray2[$iBuffer['DocID']][5];
            if (!$tongJi[0]['checkerror']){
                $tongJi[0]['checkerror'] = $buffer[$i]['RightNum'];
                $tongJi[0]['checkerrorLose'] = $buffer[$i]['LoseNum'];
            }else{
                $tongJi[0]['checkerror']+=$buffer[$i]['RightNum'];
                $tongJi[0]['checkerrorLose'] += $buffer[$i]['LoseNum'];
            }
            if (!$tongJi[0]['error']){
                $tongJi[0]['error'] = $buffer[$i]['CheckNum'];
                $tongJi[0]['errorLose'] = $buffer[$i]['CheckLoseNum'];
            }else{
                $tongJi[0]['error']+=$buffer[$i]['CheckNum'];
                $tongJi[0]['errorLose'] += $buffer[$i]['CheckLoseNum'];
            }

            if (!$tongJi[0]['testRighterror']){
                $tongJi[0]['testRighterror']=$buffer[$i]['TestRight'];
            }else{
                $tongJi[0]['testRighterror']+=$buffer[$i]['TestRight'];
            }
            if (!$tongJi[0]['testCheckerror']) $tongJi[0]['testCheckerror']=$buffer[$i]['TestCheck'];
            else $tongJi[0]['testCheckerror']+=$buffer[$i]['TestCheck'];

            if ($iBuffer['num'] < 10) {
                if (!$tongJi[0]['xtnum']) $tongJi[0]['xtnum'] = $iBuffer['num'];
                else $tongJi[0]['xtnum'] += $iBuffer['num'];
                if (!$tongJi[0]['xtdocnum']) $tongJi[0]['xtdocnum'] = 1;
                else $tongJi[0]['xtdocnum']++;
            } else {
                if (!$tongJi[0]['dtnum']) $tongJi[0]['dtnum'] = $iBuffer['num'];
                else $tongJi[0]['dtnum'] += $iBuffer['num'];
                if (!$tongJi[0]['dtdocnum']) $tongJi[0]['dtdocnum'] = 1;
                else $tongJi[0]['dtdocnum']++;
            }
        }
        foreach ($buffer2 as $i => $iBuffer2) {
            $buffer2[$i]['DocName'] = $docArr[$iBuffer2['DocID']];
            $buffer2[$i]['RightNum'] = $sumArray[$iBuffer2['DocID']][0];
            $buffer2[$i]['CheckNum'] = $sumArray[$iBuffer2['DocID']][1];
            $buffer2[$i]['TestRight'] = $sumArray[$iBuffer2['DocID']][2];
            $buffer2[$i]['TestCheck'] = $sumArray[$iBuffer2['DocID']][3];
            $buffer2[$i]['TestError'] = $sumArray[$iBuffer2['DocID']][2]-$sumArray[$iBuffer2['DocID']][3];
            $buffer2[$i]['LoseNum'] = $sumArray[$iBuffer2['DocID']][4];
            $buffer2[$i]['CheckLoseNum'] = $sumArray[$iBuffer2['DocID']][5];
            if (!$tongJi[1]['checkerror']){
                $tongJi[1]['checkerror'] = $buffer2[$i]['RightNum'];
                $tongJi[1]['checkerrorLose'] = $buffer2[$i]['LoseNum'];
            }else{
                $tongJi[1]['checkerror']+=$buffer2[$i]['RightNum'];
                $tongJi[1]['checkerrorLose'] += $buffer2[$i]['LoseNum'];
            }
            if (!$tongJi[1]['error']){
                $tongJi[1]['error'] = $buffer2[$i]['CheckNum'];
                $tongJi[1]['errorLose'] = $buffer2[$i]['CheckLoseNum'];
            }else{
                $tongJi[1]['error']+=$buffer2[$i]['CheckNum'];
                $tongJi[1]['errorLose'] += $buffer2[$i]['CheckLoseNum'];
            }

            if (!$tongJi[1]['testRighterror']) $tongJi[1]['testRighterror']=$buffer2[$i]['TestRight'];
            else $tongJi[1]['testRighterror']+=$buffer2[$i]['TestRight'];
            if (!$tongJi[1]['testCheckerror']) $tongJi[1]['testCheckerror']=$buffer2[$i]['TestCheck'];
            else $tongJi[1]['testCheckerror']+=$buffer2[$i]['TestCheck'];


            if ($iBuffer2['num'] < 10) {
                if (!$tongJi[1]['xtnum']) $tongJi[1]['xtnum'] = $iBuffer2['num'];
                else $tongJi[1]['xtnum'] += $iBuffer2['num'];
                if (!$tongJi[1]['xtdocnum']) $tongJi[1]['xtdocnum'] = 1;
                else $tongJi[1]['xtdocnum']++;
            } else {
                if (!$tongJi[1]['dtnum']) $tongJi[1]['dtnum'] = $iBuffer2['num'];
                else $tongJi[1]['dtnum'] += $iBuffer2['num'];
                if (!$tongJi[1]['dtdocnum']) $tongJi[1]['dtdocnum'] = 1;
                else $tongJi[1]['dtdocnum']++;
            }
        }
        /*载入模板标签*/
        $this->assign('search', $search);
        $this->assign('alltongji', $tongJi);
        $this->assign('list', $buffer);
        $this->assign('list1', $buffer2);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 编辑试卷标引
     * @author demo
     */
    public function edit() {
        $workID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($workID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑试卷标引';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('TeacherWork')->selectData(
            '*',
            'WorkID=' . $workID,
            '',
            1);
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712'); //您没有权限编辑非所属学科任务！
            }
        }elseif($this->ifDiff){//判断是否区分用户
            if($this->getCookieUserName()!=$edit[0]['Admin']){
                $this->setError('30812'); //您没有权限编辑该任务！
            }
        }
        if($edit[0]['UserName']!=''){
            $this->setError('40508'); //该任务已经分配！
        }

        $buffer=$this->getModel('TeacherWorkList')->selectData(
            '*',
            'WorkID=' . $workID);
        if($buffer){
            $tmpArr=array();
            foreach($buffer as $iBuffer){
                $tmpArr[]=$iBuffer['DocID'];
            }
            $docArray=$this->getModel('Doc')->selectData(
                'DocID,DocName',
                ' DocID IN ('.implode(',',$tmpArr).') ',
                'DocID ASC');
            $this->assign('docArray', $docArray);
            $this->assign('docList', implode(',',$tmpArr));
        }

        //学科
        $subjectArray=SS('subjectParentId'); //获取学科数据集

        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray);
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display('TaskB/add');
    }

    /**
     * 保存标引分配信息
     * @author demo
     */
    public function save() {
        $workID = $_POST['WorkID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($workID) && $act == 'edit') {
            $this->setError('30301'); //数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223'); //模板标识不能为空！
        }

        $data = array ();
        $data['SubjectID'] = $_POST['SubjectID'];
        $data['UserName'] = $_POST['UserName'];
        $data['Content'] = $_POST['Content'];
        $data['Admin'] = $this->getCookieUserName();
        $data['HasReplace'] = (((int)$_POST['HasReplace']) == 1 && !empty($data['UserName'])) ? 1 : 0;
        $docID=$_POST['doclist'];
        if(empty($docID)){
             $this->setError('40509'); //标引分配文档不能为空！
        }

        $tmpArr=explode(',',$docID);
        //检查文档是否已经分配过
        $buffer=$this->getModel('TeacherWorkList')->selectData(
            'DocID,WorkID',
            'DocID in ('.$docID.') ');

        if($buffer){
            foreach($tmpArr as $i=>$iTmpArr){
                foreach($buffer as $iBuffer){
                    if($iBuffer['DocID']==$iTmpArr){
                        if($workID){
                            if($workID!=$iBuffer['WorkID']) unset($tmpArr[$i]);
                        }else{
                            unset($tmpArr[$i]);
                        }
                    }
                }
            }
        }
        if(empty($tmpArr)){
            $this->setError('40508'); //所选文档已经被分配过！
        }

        if ($act == 'add') {
            if($this->ifSubject && $this->mySubject){
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712'); //不能标引分配非所属学科文档！
                }
            }
            $data['AddTime'] = time();
            $data['LastTime'] = time();
            if(($workID=$this->getModel('TeacherWork')->insertData(
                    $data))===false){
                $this->setError('30310'); //添加失败！
            }else{
                //添加任务docid
                $data=array();
                $data['WorkID']=$workID;
                foreach($tmpArr as $iTmpArr){
                    $data['DocID']=$iTmpArr;
                    $this->getModel('TeacherWorkList')->insertData(
                        $data);
                }

                //修改doc的IfTask
                $this->getModel('Doc')->updateData(
                    array('IfTask'=>1),
                    'DocID in ('.implode(',',$tmpArr).')');

                //写入日志
                $this->adminLog($this->moduleName,'添加试卷标引分配【'.implode(',',$tmpArr).'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        } else
            if ($act == 'edit') {
                $TeacherWork = $this->getModel('TeacherWork');
                $subject = $TeacherWork->selectData(
                    'SubjectID',
                    'WorkID='.$workID);
                if($this->ifSubject && $this->mySubject){
                    if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                        $this->setError('30712'); //不能标引分配非所属学科文档！
                    }
                    if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                        $this->setError('30712'); //不能标引分配为非所属学科文档！
                    }
                }
                $data = array ();
                $data['UserName'] = $_POST['UserName'];
                $data['Content'] = $_POST['Content'];
                $data['Admin'] = $this->getCookieUserName();
                $data['LastTime'] = time();
                if($TeacherWork->updateData(
                        $data,
                        'WorkID='.$workID)===false){
                    $this->setError('30311'); //修改失败！
                }else{
                    $buffer=$this->getModel('TeacherWorkList')->selectData(
                        '*',
                        'WorkID ='.$workID);
                    if($buffer){
                        $docArray=array();
                        foreach($buffer as $iBuffer){
                            $docArray[]=$iBuffer['DocID'];
                        }

                        $i=0;
                        $count=count($buffer);
                        foreach($tmpArr as $iTmpArr){
                            if($i<$count){
                                //修改
                                $data=array();
                                $data['DocID']=$iTmpArr;
                                $this->getModel('TeacherWorkList')->updateData(
                                    $data,
                                    'WLID='.$buffer[$i]['WLID']);
                            }else{
                                //添加
                                $data=array();
                                $data['WorkID']=$workID;
                                $data['DocID']=$iTmpArr;
                                $this->getModel('TeacherWorkList')->insertData(
                                    $data);
                            }
                            $i++;
                        }
                        //删除
                        if($count>$i){
                            $tmpList=array();
                            for(;$i<$count;$i++){
                                $tmpList[]=$buffer[$i]['WLID'];
                            }
                            $this->getModel('TeacherWorkList')->deleteData(
                                'WLID in （'.implode(',',$tmpList).')');
                        }
                        //修改doc的IfTask
                        $doc = $this->getModel('Doc');
                        $doc->updateData(
                            array('IfTask'=>0),
                            'DocID in ('.implode(',',$docArray).')');
                        $doc->updateData(
                            array('IfTask'=>1),
                            'DocID in ('.implode(',',$tmpArr).')');
                    }else{
                        //添加任务docid
                        $data=array();
                        $data['WorkID']=$workID;
                        foreach($tmpArr as $iTmpArr){
                            $data['DocID']=$iTmpArr;
                            $this->getModel('TeacherWorkList')->insertData(
                                $data);
                        }
                        //修改doc的IfTask
                        $this->getModel('Doc')->updateData(
                            array('IfTask'=>1),
                            'DocID in ('.implode(',',$tmpArr).')');
                    }

                    //写入日志
                    $this->adminLog($this->moduleName,'修改试卷标引分配WorkID为【'.$workID.'】的数据');
                    $this->showSuccess('修改成功！', __URL__);
                }
            }
    }

    /**
     * 删除文档
     * @author demo
     */
    public function deldoc(){
        $workID = $_GET['wid'];
        $docID = $_GET['did'];

        if (!$workID || !$docID) {
            $this->setError('30301',1); //数据标识不能为空！
        }
        $wBuffer=$this->getModel('TeacherWork')->selectData(
            '*',
            'WorkID='.$workID);
        if($this->ifSubject && $this->mySubject){//判断是否区分学科
            if(!in_array($wBuffer[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30301',1); //您不能删除非所属学科的任务！
            }
        }elseif($this->ifDiff){//判断是否区分用户
            if($this->getCookieUserName()!=$wBuffer[0]['Admin']){
                $this->setError('40510',1); //您没有权限删除该任务！
            }
        }
        $buffer=$this->getModel('TeacherWorkList')->selectData(
            '*',
            'WorkID='.$workID.' and DocID='.$docID.'');

        $docBuffer=$this->getModel('Doc')->selectData(
            'DocID',
            'DocID='.$docID);
        if($docBuffer && !($buffer[0]['Status']==='0')){
            $this->setError('40511',1); //任务状态不允许删除！
        }

        //删除试卷
        if($this->getModel('TeacherWorkList')->deleteData(
                'WLID in('.$buffer[0]['WLID'].')')===false){
            $this->setError('30302',1); //删除失败
        }else{
            //判断试卷是否已经入库
            $buffer1=$this->getModel('TestAttr')->selectData(
                '*',
                'DocID='.$docID);
            $buffer2=$this->getModel('TestAttrReal')->selectData(
                '*',
                'DocID='.$docID);
            if(count($buffer1)==0 and count($buffer2)>0){
                //已经入库
            }else{
                if($docBuffer){
                    //修改试卷的属性
                    $this->getModel('Doc')->updateData(
                        array('IfTask'=>0),'DocID='.$docID);
                }
            }
            $this->setBack('success');
        }
    }

    /**
     * 删除任务
     * @author demo
     */
    public function delete() {
        $workID = $_REQUEST['id']; //获取数据标识
        if (!$workID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $TeacherWorkModel = $this->getModel('TeacherWork');
        $buffer=$TeacherWorkModel->selectData(
            '*',
            'WorkID in (' . $workID . ')');
        if ($buffer[0]['Status']>1){
            $this->setError('40512'); //任务已分配无法删除！
        }
        //判断是否区分用户
        $teacherWork=$TeacherWorkModel->selectData(
            'Admin,SubjectID',
            'WorkID in (' . $workID . ')');
        if($this->ifSubject && $this->mySubject){
            if($teacherWork){
                foreach($teacherWork as $iTeacherWork){
                    if(!in_array($iTeacherWork['SubjectID'],explode(',',$this->mySubject))){
                        $this->setError('30712'); //您不能删除非所属学科的任务！
                    }
                }
            }
        }elseif($this->ifDiff){
            if($teacherWork){
                foreach($teacherWork as $iTeacherWork){
                    if($this->getCookieUserName()!=$iTeacherWork['Admin']){
                        $this->setError('40510'); //您没有权限删除该任务！
                    }
                }
            }
        }
        if ($TeacherWorkModel->deleteData(
                'WorkID in ('.$workID.')')=== false) {
            $this->setError('30302'); //删除失败！
        } else {
            //删除审核任务
            $this->getModel('TeacherWorkCheck')->deleteData(
                'WorkID in ('.$workID.')');
            //查找workid对应docid
            $TeacherWorkList = $this->getModel('TeacherWorkList');
            $buffer=$TeacherWorkList->selectData(
                '*',
                'WorkID in (' . $workID . ')');
            if($buffer){
                $tmpArr=array();
                foreach($buffer as $iBuffer){
                    $tmpArr[]=$iBuffer['DocID'];
                }

                //删除任务docid
                $TeacherWorkList->deleteData(
                    'WorkID in ('.$workID.')');
                //修改doc的IfTask
                $this->getModel('Doc')->updateData(
                    array('IfTask'=>0),
                    'DocID in ('.implode(',',$tmpArr).')');
            }

            //写入日志
            $this->adminLog($this->moduleName,'删除试卷标引分配WorkID为【'.$workID.'】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }

    /**
     * 审核试卷标引
     * @author demo
     */
    public function checkwork() {
        $pageName='审核试卷标引';
        $id=$_GET['id'];
        if (!$id) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $workArray=$this->getModel('TeacherWork')->selectData(
            '*',
            'WorkID='.$id);
        if (!$workArray) {
            $this->setError('30306','',__URL__); //数据不存在！
        }
        //权限，是否区分用户
        if($this->ifSubject && $this->mySubject){
            if(!in_array($workArray[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712'); //您没有权限审核非所属学科任务！
            }
        }elseif($this->ifDiff){
            if($this->getCookieUserName()!=$workArray[0]['Admin']){
                $this->setError('40513'); //您没有权限审核该任务！
            }
        }
        $checkArray=$this->getModel('TeacherWorkCheck')->selectData(
            '*',
            'WorkID='.$id.' and CheckTimes='.$workArray[0]['CheckTimes']);
        if($checkArray){
            $workArray[0]['CheckStatus']=$checkArray[0]['Status'];
            $workArray[0]['CheckContent']=$checkArray[0]['Content'];
        }else $workArray[0]['CheckStatus']=null;

        $docArray=$this->getModel('TeacherWorkList')->unionSelect('teacherWorkSelectById', $id);
        if(!$docArray){
            $this->setError('40514','',__URL__); //该任务下没有试卷！
        }

        $this->assign('docArray', $docArray);
        $this->assign('workArray', $workArray[0]);
        $this->assign('pageName', $pageName);
        $this->display();
    }

    /**
     * 审核完成
     * @author demo
     */
    public function savecheck() {
        $content=$_POST['Content'];
        $checkContent=$_POST['CheckContent'];
        $workID=$_POST['WorkID'];
        if (empty($workID)) {
            $this->setError('30301','',__URL__);
        }
        //判断状态
        $where = 'WorkID='.$workID;
        if($this->ifSubject && $this->mySubject){
            $where .= ' And SubjectID in ('.$this->mySubject.')';
        }
        $buffer=$this->getModel('TeacherWork')->selectData(
            '*',
            $where);
        if (!$buffer) {
            $this->showerror('任务不存在，或者您没有该任务的操作权限！');
        }
        if ($buffer[0]['Status']!=1) {

            $this->setError('40515','',__URL__); //该任务当前不是审核状态！
        }
        $userMsg=$this->getModel('User')->getInfoByName($buffer[0]['UserName']);
        $ifTask=$buffer[0]['ifTask'];
        $checkTimes=$buffer[0]['CheckTimes'];

        $buffer=$this->getModel('TeacherWorkList')->selectData(
            '*',
            'WorkID='.$workID);

        $Status=2;

        if($buffer){
            foreach($buffer as $iBuffer){
                if($iBuffer['Status']<2){
                    $this->setError('40516','',__URL__); //该任务下有未审核的试卷！
                    exit();
                }
                if($iBuffer['Status']==3) $Status=3;
            }
        }

        $data=array();
        if($Status==2){
            $ifTask=2;
            $data['CheckTimes']=$checkTimes+1;
            $data['IfTask']=2;
        }
        $data['LastTime']=time();
        $data['Content']=$content;
        $data['Status']=$Status;
        if($this->getModel('TeacherWork')->updateData(
                $data,
                'WorkID='.$workID)===false){
            $this->setError('40517'); //审核失败！
        }else{
            $this->getModel('TeacherWorkCheck')->updateData(
                array('Content'=>$checkContent,'Status'=>$Status,'LastTime'=>time()),
                'WorkID='.$workID.' and CheckTimes='.$checkTimes);
                //给用户积分
                //加入支出表
                //用户所有任务完成，记录平台支出表
                if(!empty($buffer)){
                    foreach($buffer as $i=>$iBuffer){
                        $docIDArr[]=$buffer[$i]['DocID'];
                    }
                    $docIdStr=implode(',',$docIDArr);
                    $testArr=$this->getModel('TestReal')->selectData(
                        'TestID',
                        'DocID in ('.$docIdStr.')'
                    );
                    foreach($testArr as $i=>$iTestArr){
                        $testStr[]=$testArr[$i]['TestID'];
                    }
                    $testIdStr=implode(',',$testStr);
                    $totalTest=count($testArr);
                    $payData['UserID']=$userMsg[0]['UserID'];
                    $payData['PayName']='试题解析';
                    $payData['PayMoney']=C('WLN_TAG_TEST_MONEY')*$totalTest;
                    $payData['PayDesc']="教师[".$this->getCookieUserID()."]审核通过了作者ID为[".$userMsg[0]['UserID']."]任务ID为【".$workID."】,试题:[".$testIdStr."]";
                    $payData['AddTime']=time();
                    $this->getModel('Pay')->addPayLog($payData);
                }
            //写入日志
            $this->adminLog($this->moduleName,'审核试卷标引分配WorkID为【'.$workID.'】的数据');
            $this->showSuccess('审核成功！');
        }
    }

    /**
     * 审核文档
     * @author demo
     */
    public function checkdoc() {
        $did=$_GET['did'];
        $wid=$_GET['wid'];
        $s=$_GET['s'];
        if(!$did or !$wid or !$s){
            $this->setError('30301',1); //数据标示有误！
        }
        $buffer=$this->getModel('TeacherWork')->selectData(
            '*',
            'WorkID='.$wid,
            '',
            1);
        //权限，是否区分用户
        if($this->ifSubject && $this->mySubject){
            if(!in_array($buffer[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712',1); //您没有权限审核非所属学科任务！
            }
        }elseif($this->ifDiff){
            if($this->getCookieUserName()!=$buffer[0]['Admin']){
                $this->setError('40513',1); //您没有权限审核该任务！
            }
        }
        $buffer2=$this->getModel('TeacherWorkCheck')->selectData(
            '*',
            'WorkID='.$wid,
            'CheckTimes desc',
            1);
        $WCID=$buffer2[0]['WCID'];
        $checkTimes=$buffer2[0]['CheckTimes'];
        if(!$buffer){
            $this->setError('40408',1); //任务不存在！
        }
        if($buffer[0]['Status']!=1 || ($buffer2[0]['Status']!=0 && $buffer2[0]['Status']!=1)){
            $this->setError('40518',1); //任务当前状态不需要审核！
        }
        $checkStatus=1;
        if($s==2){
            $checkStatus=2;
        }else if($s==3){
            $checkStatus=0;
        }else{
            $this->setError('40519',1); //审核状态有误！
        }
        if($buffer2[0]['Status']==0) $checkStatus=2;
            $buffer=$this->getModel('TeacherWorkList')->selectData(
                '*',
                'DocID='.$did.' and WorkID='.$wid,'',
                1);
        if($buffer){
            if($buffer2[0]['Status']!=0){
                //清理审核标记记录NowCheck NowRight
                $teacherWorkTestAttr=$this->getModel('TeacherWorkTestAttr');
                if($teacherWorkTestAttr->updateNowData($WCID,$checkTimes,$did)===false){
                    // exit('修改状态失败.');
                    $this->setError('40520',1); //修改状态失败
                }
            }
            if($this->getModel('TeacherWorkList')->updateData(
                    array('Status'=>$s,'CheckStatus'=>$checkStatus),
                    'WLID='.$buffer[0]['WLID'])===false){
                $this->setError('40520',1); //修改状态失败
            }else{
                $this->setBack('success');
            }
        }
        $this->setError('30306',1); //数据不存在
    }

    /**
     * 选择教师
     * @author demo 
     */
    public function teacher() {
        C('SHOW_PAGE_TRACE', false);
        $powerUser = $_GET['s'];//教师类别
        $map['s']=$powerUser;
        $subjectID = $_GET['subjectID'];//按学科查找
        $userName = $_GET['name'];//按用户名查找
        $perPage = C('WLN_PERPAGE');
        //查询标引权限分组ID
        $buffer = $this->getModel('PowerUser')->selectData(
            'PUID',
            'PowerUser="'.$powerUser.'"');
        $puId = $buffer[0]['PUID'];
        //查询属于标引权限分组的用户ID
        $userIdArr = $this->getModel('UserGroup')->selectData(
            'UserID',
            'GroupName = 3 AND GroupID = "'.$puId.'"');
        $userArr = array();//存放用户ID；
        foreach($userIdArr as $i => $iUserIdArr){
            $userArr[] = $iUserIdArr['UserID'];
        }
        $userStr = implode(',',$userArr);//用户ID字符串
        //查询条件
        $data = 'UserID in ('.$userStr.')';
        $data .= ' and Status=0 and Whois=1';
        if($userName){
            $data .= ' and UserName="'.$userName.'"';
            $map['name']=$userName;
        }
        if($subjectID){
            if($this->ifSubject && $this->mySubject){
                if(!in_array($subjectID,explode(',',$this->mySubject))){
                    $this->setError('30712',0); //您不能选择非所属学科教师！
                }
            }
            $data .= ' and SubjectStyle="'.$subjectID.'"';
            $map['subjectID']=$subjectID;
        }
        $count = $this->getModel('User')->selectCount(
            $data,
            'UserID'); // 查询满足要求的总记录数
        // 进行分页数据查询
        $page=page($count,$_GET['p'],$perPage).','.$perPage;
        $list=$this->getModel('User')->pageData('*',$data,'UserID desc',$page);
        $this->pageList($count,$perPage,$map);
        $subjectArray =  SS('subjectParentId'); //获取学科数据集

        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('s',$powerUser);
        $this->assign('subjectArray', $subjectArray);
        $this->display();
    }

    /**
     * 选择试卷
     * @author demo
     */
    public function doc() {
        C('SHOW_PAGE_TRACE', false);
        $subjectID=$_GET['SubjectID'];
        $perPage=C('WLN_PERPAGE');
        $data='IfTask=0';
        if($subjectID){
            $data.=' and SubjectID="'.$subjectID.'"';
            $map['SubjectID']=$subjectID;
            if($this->ifSubject && $this->mySubject){
                if(!in_array($subjectID,explode(',',$this->mySubject))){
                    $this->setError('30712',1); //您不能选择非所属学科试卷！
                }
            }
        }
        $doc = $this->getModel('Doc');
        $count = $doc->selectCount(
            $data,
            'DocID'); // 查询满足要求的总记录数
        $page=page($count,$_GET['p'],$perPage).','.$perPage;
        $list=$doc->pageData(
            '*',
            $data,
            'DocID asc',
            $page);
        $this->pageList($count,$perPage,$map);

        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->display();
    }
    /**
     * 添加审核教师
     * @author demo
     */
    public function taskcheckadd(){
        $pageName = '分配审核任务';
        $act = 'edit'; //模板标识

        $id=$_GET['id'];
        //判断数据标识
        if (empty ($id)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        $buffer=$this->getModel('TeacherWork')->selectData(
            '*',
            'WorkID='.$id);
        if(!$buffer) {
            $this->setError('40408'); //任务不存在！
        }
        //权限,是否区分用户
        if($this->ifSubject && $this->mySubject){
            if(!in_array($buffer[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712'); //您没有权限审核非所属学科任务！
            }
        }elseif($this->ifDiff){
            if($this->getCookieUserName()!=$buffer[0]['Admin']){
                $this->setError('40513'); //您没有权限审核该任务！
            }
        }
        if($buffer[0]['IfTask']==2) {
            $this->setError('40521'); //任务已分配,请审核！
        }
        $edit=$this->getModel('TeacherWorkCheck')->selectData(
            '*',
            'WorkID='.$id.' and CheckTimes='.$buffer[0]['CheckTimes']);
        if(!$edit){
            $act = 'add'; //模板标识
            $edit[0]['WorkID']=$buffer[0]['WorkID'];
            $edit[0]['SubjectID']=$buffer[0]['SubjectID'];
        }
        $workArray=$this->getModel('TeacherWorkList')->unionSelect('teacherWorkSelectById',$id);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]); //页面标题
        $this->assign('subjectID', $buffer[0]['SubjectID']); //页面标题
        $this->assign('workArray', $workArray); //页面标题
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 保存审核任务
     * @author demo
     */
    public function checksave() {
        $wcID = $_POST['WCID']; //获取数据标识
        $workID = $_POST['WorkID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($wcID) && $act == 'edit') {
            $this->setError('30301'); //数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223'); //模板标识不能为空！
        }
        if (empty ($workID)) {
            $this->setError('30223'); //模板标识不能为空！
        }
        if (empty ($_POST['UserName'])) {
            $this->setError('40522'); //请选择审核教师！
        }
        $buffer=$this->getModel('TeacherWork')->selectData(
            '*',
            'WorkID='.$workID);
        if(!$buffer) $this->setError('40408'); //任务不存在！
        if($buffer[0]['IfTask']==1 && $act == 'add') $this->setError('40508'); //任务已分配！
        if($buffer[0]['IfTask']==2) $this->setError('40407'); //任务已完成！

        $data=array();
        $data['UserName'] = $_POST['UserName'];
        $data['CheckTimes'] = $buffer[0]['CheckTimes'];
        $data['Content'] = $_POST['Content'];
        $data['Admin'] = $this->getCookieUserName();
        if ($act == 'add') {
            $data['WorkID']=$workID;
            $data['Status']=0;
            $data['AddTime'] = time();
            $data['LastTime'] = time();
            $data['SubjectID'] = $_POST['SubjectID'];
            if($this->ifSubject && $this->mySubject){
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712'); //您不能添加非所属学科审核任务！
                }
            }
            if($this->getModel('TeacherWorkCheck')->insertData(
                    $data)===false){
                $this->setError('30310'); //添加失败！
            }else{
                //修改任务分配属性
                $data=array();
                $data['IfTask']=1;
                $this->getModel('TeacherWork')->updateData(
                    $data,
                    'WorkID='.$workID);

                //写入日志
                $this->adminLog($this->moduleName,'添加审核任务分配WorkID为【'.$workID.'】的数据');
                $this->showSuccess('添加成功！', __URL__);
            }
        }else if($act == 'edit'){
            $subject = $this->getModel('TeacherWorkCheck')->selectData(
                'SubjectID',
                'WCID='.$wcID);
            if($this->ifSubject && $this->mySubject){
                if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712'); //您不能编辑非所属学科审核任务！
                }
            }
            $data['WCID']=$wcID;
            $data['Status']=0;
            $data['LastTime'] = time();
            if($this->getModel('TeacherWorkCheck')->updateData(
                    $data,
                    'WCID='.$wcID)===false){
                $this->setError('30311'); //修改失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改审核任务分配WorkID为【'.$workID.'】的数据');
                $this->showSuccess('修改成功！', __URL__);
            }
        }

    }
    /**
     * 管理员审核试题
     * @author demo
     * */
    public function showCheck(){
        $docID = $_GET['DocID']; //获取数据标识
        $workID = $_GET['WorkID']; //获取数据标识
        if (empty ($workID) || empty ($docID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        //获取审核任务ID
        $buffer=$this->getModel('TeacherWorkCheck')->selectData(
            'WCID,SubjectID,CheckTimes,Admin',
            'WorkID='.$workID,
            'CheckTimes desc');
        if(!$buffer){
            $this->setError('40408'); //审核任务不存在！
        }
        //权限，是否区分学科,用户
        if($this->ifSubject && $this->mySubject){
            if(!in_array($buffer[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712'); //您没有权限审核非所属学科任务！
            }
        }elseif($this->ifDiff){
            if($this->getCookieUserName()!=$buffer[0]['Admin']){
                $this->setError('40513'); //您没有权限审核该任务！
            }
        }

        $checkTimes=$buffer[0]['CheckTimes'];
        $wcID=$buffer[0]['WCID'];
        //获取试题审核情况
        $testArray=$this->getModel('TeacherWorkTestAttr')->unionSelect('teacherCheckInfo', $wcID,$checkTimes,$docID);
        $testInfo=array();
        if($testArray){
            foreach ($testArray as $i=>$iTestArray){
                $testInfo[$iTestArray['TestID']][$iTestArray['Style']]['Content']=$iTestArray['Content'];
                $testInfo[$iTestArray['TestID']][$iTestArray['Style']]['IfRight']=$iTestArray['IfRight'];
                if($iTestArray['CheckResult']<2) {
                    $testInfo[$iTestArray['TestID']][$iTestArray['Style']]['Suggestion']='';
                    $testInfo[$iTestArray['TestID']][$iTestArray['Style']]['Checked']='checked="checked"';
                }else{
                    $testInfo[$iTestArray['TestID']][$iTestArray['Style']]['Suggestion']=$iTestArray['Suggestion'];
                    $testInfo[$iTestArray['TestID']][$iTestArray['Style']]['Checked2']='checked="checked"';
                }
                $testInfo[$iTestArray['TestID']]['WTID']=$iTestArray['WTID'];
            }
        }
        /*载入模板标签*/
        $this->assign('workID',$workID);
        $this->assign('testInfo', $testInfo);
        $this->display();
    }
    /**
     * 管理员评价试题
     * @author demo
     *
     * */
    public function saveCheckResult(){
        $data=array();
        $wtID=$_POST['WTID'];
        $data['test']=$_POST['test'];
        $data['knowledge']=$_POST['knowledge'];
        $data['chapter']=$_POST['chapter'];
        $data['special']=$_POST['special'];
        $data['diff']=$_POST['diff'];
        $dataText['testText']=$_POST['testText'];
        $dataText['knowledgeText']=$_POST['knowledgeText'];
        $dataText['chapterText']=$_POST['chapterText'];
        $dataText['specialText']=$_POST['specialText'];
        $dataText['diffText']=$_POST['diffText'];

        //获取该试题所有审核属性
        $buffer=$this->getModel('TeacherWorkTestAttr')->selectData(
            'IfRight,AttrID,Style,CheckResult,NowCheck,CheckNum,CheckLoseNum',
            'WTID='.$wtID);

        if(empty($buffer)){
            $this->setBack('教师未评审，无法评价。');
        }

        $testArray=array();
        foreach($buffer as $i=>$iBuffer){
            //将当前的审核状态标识为一级错误
            if($data[$iBuffer['Style']] == 2 && $iBuffer['IfRight'] == 2){
                $data[$iBuffer['Style']] = $iBuffer['IfRight']+1; //错误码兼容
            }
            $testArray[$iBuffer['Style']]['AttrID']=$iBuffer['AttrID'];
            $testArray[$iBuffer['Style']]['CheckResult']=$iBuffer['CheckResult'];
            $testArray[$iBuffer['Style']]['NowCheck']=$iBuffer['NowCheck'];
            $testArray[$iBuffer['Style']]['CheckNum']=$iBuffer['CheckNum'];
            $testArray[$iBuffer['Style']]['CheckLoseNum']=$iBuffer['CheckLoseNum'];
        }
        foreach($data as $i=> $iData){
            //检测统计数据
            $checkResult = $testArray[$i]['CheckResult'];
            $isRedo = $testArray[$i]['NowCheck'] == 0;
            $checkNum=$testArray[$i]['CheckNum'];
            $checkLoseNum = $testArray[$i]['CheckLoseNum'];

            if($checkResult != $iData || $isRedo){
                if(2 == $iData){
                    $checkLoseNum += 1;
                    if(!$isRedo && $checkResult == 2)
                        $checkNum -= 1;
                }else if(3 == $iData){
                    $checkNum += 1;
                    if(!$isRedo && $checkResult == 3)
                        $checkLoseNum -= 1;
                }else{
                    if(!$isRedo){
                        if($checkResult == 2){
                            $checkLoseNum -= 1;
                       }else if($checkResult == 3){
                            $checkNum -= 1;
                       }
                    }
                }
            }
            $this->getModel('TeacherWorkTestAttr')->updateData(
                array('Suggestion'=>$dataText[$i.'Text'],'CheckResult'=>$iData,'CheckLoseNum'=>$checkLoseNum,'NowCheck'=>1,'CheckNum'=>$checkNum),
                'AttrID ='.$testArray[$i]['AttrID']);
        }
        $output="评价成功";
        $this->setBack($output);
    }

    /**
     * 替换权限编辑
     * @author demo
    */
    public function replace(){
        $wId = (int)$_POST['wid'];
        $teacherWork = $this->getModel('TeacherWork');
        $data = $teacherWork->selectData(
            'HasReplace',
            'WorkID='.$wId);
        $data = $data[0]['HasReplace'];
        if((int)$data != 0 && empty($data)){
            $this->setError('30301');//无效的数据标识
        }
        if($data == 1){
            $data = 0;
        }else{
            $data = 1;
        }
        $result = $teacherWork->updateHasReplace($wId,$data);
        if(empty($result)){
            $this->setError('30303'); //更新失败
        }
        $this->setBack('修改成功');
    }
    /**
     * 新审核
     * @author demo
     */
    public function newCheck(){
        $pageName = '试题审核';
        $docID  = $_REQUEST['DocID'];
        $workID = $_REQUEST['WorkID']; //获取数据标识
        if (empty ($workID) || empty ($docID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        //获取审核任务ID
        $buffer=$this->getModel('TeacherWorkCheck')->selectData(
            'WCID,SubjectID,CheckTimes,Admin',
            'WorkID='.$workID,
            'CheckTimes desc');
        if(!$buffer){
            $this->setError('40408'); //审核任务不存在！
        }
        $data = 'DocID='.$docID;
        $orderby =' NumbID asc ';
        //权限，是否区分学科,用户
        if($this->ifSubject && $this->mySubject){
            if(!in_array($buffer[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712'); //您没有权限审核非所属学科任务！
            }
            $data .= ' and SubjectID in ('.$this->mySubject.') ';
        }elseif($this->ifDiff){
            if($this->getCookieUserName()!=$buffer[0]['Admin']){
                $this->setError('40513'); //您没有权限审核该任务！
            }
            $data .= ' and Admin="'.$this->getCookieUserName().'" ';
        }

        $list = $this->getModel('TestAttr')->selectData(
            '*',
            $data,
            $orderby
        );
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

            $host=C('WLN_DOC_HOST');
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
                if($host){
                    $list[$i]['Test']= R('Common/TestLayer/strFormat',array($list[$i]['Test']));
                }

                $list[$i]['error']=R('Common/TestLayer/checkTestAttr',array($list[$i]));

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

        $checkTimes=$buffer[0]['CheckTimes'];
        $wcID=$buffer[0]['WCID'];
        //获取试题审核情况
        $testArray=$this->getModel('TeacherWorkTestAttr')->unionSelect('teacherCheckInfo',$wcID,$checkTimes,$docID);
        $testInfo=array();
        if($testArray){
            foreach ($testArray as $i=>$iTestArray){
                $testInfo[$iTestArray['TestID']][$iTestArray['Style']]['Content']=$iTestArray['Content'];
                $testInfo[$iTestArray['TestID']][$iTestArray['Style']]['IfRight']=$iTestArray['IfRight'];
                if($iTestArray['CheckResult']<2) {
                    $testInfo[$iTestArray['TestID']][$iTestArray['Style']]['Suggestion']='';
                    $testInfo[$iTestArray['TestID']][$iTestArray['Style']]['Checked']='checked="checked"';
                }else{
                    $testInfo[$iTestArray['TestID']][$iTestArray['Style']]['Suggestion']=$iTestArray['Suggestion'];
                    $testInfo[$iTestArray['TestID']][$iTestArray['Style']]['Checked2']='checked="checked"';
                }
                $testInfo[$iTestArray['TestID']]['WTID']=$iTestArray['WTID'];
            }
        }

        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('workID',$workID);
        $this->assign('diffArray', $diffArray);
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('testInfo', $testInfo);
        $this->display();
    }
}
?>