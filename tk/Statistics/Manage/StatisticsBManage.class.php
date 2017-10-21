<?php
/**
 * 后台统计action
 * @author demo
 * @date 2015-5-6
 */
namespace Statistics\Manage;
header('content-Type:text/html;charset=utf8');
class StatisticsBManage extends BaseController {

    private $clearCahce = 0;

    public function __construct(){
        parent::__construct();
        if(isset($_GET['clearCache']))
            $this->clearCahce = 1;
    }

    /**
     * 后台罗列各种统计使用
     * @author demo
     */
    public function menuList(){
        $pageName = '数据统计';
        /*载入模板标签*/
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 系统统计
     * @author demo
     */
    public function system(){
        $stat = new \Statistics\Model\AjaxStatisticsModel();
        if($this->clearCahce === 1){
            $stat->clearAllCache();
        }
        $this->assign('mapping', implode(',', array_keys($stat->getMapping())));
        $this->display();
    }
    /**
     * 系统ajax分段统计
     * @author demo
     */
    public function systemAjaxResponse(){
        $stat = new \Statistics\Model\AjaxStatisticsModel();
        exit(dump($stat->getResult($_GET['param'])));
        $this->setBack($stat->getResult($_POST['param']));
    }

    /**
     * 用户统计
     * @author demo
     */
    public function user(){
        $stat = new \Statistics\Model\UserStatisticsModel();
        if($this->clearCahce === 1){
            $stat->clearAllCache();
        }
        $data = $stat->getResult();
        $this->assign('list', $data);
        $this->display();
    }

    /**
     * 学校试用情况统计
     * @author demo
     */
    public function school(){
        $stat = new \Statistics\Model\SchoolStatisticsModel();
        if($this->clearCahce === 1){
            $stat->clearAllCache();
        }
        $data = $stat->getResult();
        $this->assign('list', $data);
        $this->display();
    }

    /**
     * 班级创建区域统计
     * @author demo
     */
    public function classes(){
        $stat = new \Statistics\Model\ClassesStatisticsModel();
        if($this->clearCahce === 1){
            $stat->clearAllCache();
        }
        $data = $stat->getResult();
        $this->assign('list', $data);
        $this->display();
    }
    /**
     * 作业统计
     * @author demo
     */
    public function homeWorkList(){
        $pageName='作业统计';
		$userName=$this->getCookieUserName();
		// echo $userName;
		$subjectArray = SS('subjectParentId');
		
        $data['1']='1';
		$username=$_GET['id'];
		// dump($username);die;
		if($username){
			$map['name']=$_GET['id'];
            $data['UserName']=$_GET['id'];
		}
		
        if($_REQUEST['name']){//教师用户名
            //简单查询
            $map['name']=$_REQUEST['name'];
            $data['UserName']=$_REQUEST['name'];
        }else{
            //高级查询
            if($_REQUEST['teacherName']){//教师用户名
                $map['teacherName'] = $_REQUEST['teacherName'];
                $data['UserName'] = $_REQUEST['teacherName'];
            }
            if(is_numeric($_REQUEST['num'])){//试题数量
                $map['num']=$_REQUEST['num'];
                $data['TestNum']=$_REQUEST['num'];
            }
            if(is_numeric($_REQUEST['workType'])){//作业类型
                $map['workType']=$_REQUEST['workType'];
                $data['WorkType']=$_REQUEST['workType'];
            }
            if($_REQUEST['workStyle']){//作答方式
                $map['workStyle'] = $_REQUEST['workStyle'];
                $data['WorkStyle'] = $_REQUEST['workStyle'];
            }
            if(is_numeric($_REQUEST['doTime'])){//作业类型
                $map['doTime']=$_REQUEST['doTime'];
                $data['doTime']=$_REQUEST['doTime'];
            }
			if($_REQUEST['SubjectID']){//所属学科
				$map['SubjectID']=$_REQUEST['SubjectID'];
				$data['SubjectID']=$_REQUEST['SubjectID'];	
			}
			// if($_REQUEST['StartTime']){//出题时间
				// $map['StartTime']=strtotime($_REQUEST['StartTime']);
				// $data['StartTime']=$_REQUEST['StartTime'];
				// dump($map['StartTime']);
			// }
			if(IS_POST){//出题时间
				$start=strtotime($_POST['StartTime']);
				$end= $_POST['EndTime'] ? strtotime($_POST['EndTime']): time();
				// dump($end);
				$map['StartTime']  = array('between',array($start,$end));
				$data['StartTime'] = array('between',array($start,$end));	
			}
            
        }
		if(empty($_REQUEST)){
			$this->setError('请选择查询条件','');
		}else{
			
		}
		// dump($data);die;
        // $perPage = 75; //每页显示数
		// dump($data);
		$perPage=C('WLN_PERPAGE');
		// 查询满足要求的总记录数
        $count=$this->getModel('UserWork')->unionSelect('userWorkCount', $data);
		// dump($count);
        $page=page($count,$_GET['p'],$perPage).','.$perPage;
        $this->pageList($count, $perPage, $map);
        // 进行分页数据查询
        $workList=$this->getModel('UserWork')->unionSelect('userWorkStatistic', $data,$page);
		// $count1=count($workList);
		// dump($workList);
		// $count1=array();//作业数量
        $workDoNum=array();//作答人数
		$TestNum=array();//试题数量
		$LoreNum=array();//知识点数量
        if(!empty($workList)){
            $subject=SS('subject');
			// dump($workList);
            foreach($workList as $i => $iWorkList){
                $workID[$i]=$iWorkList['WorkID'];
				$TestNum[$i]=$iWorkList['TestNum'];
				$LoreNum[$i]=$iWorkList['LoreNum'];
				
				
				
                $subjectName=$subject[$iWorkList['SubjectID']]['ParentName'].$subject[$iWorkList['SubjectID']]['SubjectName'];
                $workList[$i]['WorkName']=$iWorkList['WorkName'].'【'.$subjectName.'】';
                $workList[$i]['StartTime']=date('Y-m-d H:i:s',$iWorkList['StartTime']);
                $workList[$i]['EndTime']=date('Y-m-d H:i:s',$iWorkList['EndTime']);
                if($iWorkList['WorkType']==1){
                    $workList[$i]['WorkType']='【试题作业】';
                }else{//2 是导学案作业
                    $workList[$i]['WorkType']='【导学案作业】';
                }
            }
			// dump($workList);
			// $count1=count($workList);
			$TestNum1=array_sum($TestNum);
			$LoreNum1=array_sum($LoreNum);
			
            $workIDList=implode(',',$workID);//获取作业ID
           
            //根据作业ID查询作答人数
            $num=$this->getModel('UserWork')->unionSelect('userWorkDoNum',$workIDList);
			
            foreach($num as $i =>$iNum){
                $workDoNum[$iNum['WorkID']]=$iNum['DoNum'];
            }
			
			$counts=array_sum($workDoNum);
			
        }
		// dump($workList);
		
        /*载入模板标签*/
		$this->assign('count',$count);
		$this->assign('counts',$counts);
		$this->assign('TestNum',$TestNum1);
		$this->assign('LoreNum',$LoreNum1);
		$this->assign('subjectArray', $subjectArray);
        $this->assign('workList', $workList); //年级数据集
        $this->assign('workDoNum',$workDoNum);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 作业作答详情
     * @author demo
     */
    public function userWorkInfo(){
        $pageName='作业作答情况';
        $workID=$_GET['id'];
        $data['WorkID']=$workID;
        $result=$this->getModel('UserSendWork')->unionSelect('userSendWorkInfo', $data);
		// dump($data);
		 
       
		// dump($sub);
		//获取作业作答信息
        if($result){
            foreach($result as $i => $iResult){
                if($iResult['Status']==0){
                    $result[$i]['SendTime'] = '未提交';
                    $result[$i]['DoTime'] = '未作答';
                }else {
                    $result[$i]['SendTime'] = date('Y-m-d H:i:s', $iResult['SendTime']);
                    $result[$i]['DoTime'] = formatString('timeConversion',$iResult['DoTime']);
                }
                $result[$i]['RealName']=$iResult['RealName']?$iResult['RealName']:$iResult['UserName'];
                $result[$i]['CorrectRate'] = ($iResult['CorrectRate']*100).'%';
            }
        }
		// dump($result);
        /*载入模板标签*/
        $this->assign('workInfoList', $result); //年级数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 暑假作业
     * @author demo
     */
    public function summerWork(){
        $pageName='暑假作业';
        //每次布置作业的题量不得少于10道(work表中的testNum大于10)
        $data=array();
        $data['TestNum']=array('gt',10);
        //作答时间不能少于8分钟
        $data['DoTime']=array('gt',480);
        //作业状态为提交
        $data['Status']=array('gt',0);
        //1名学生完成1次作业即为1次有效次数（每名学生可多次完成同一名 教师的多次作业)
        $perPage = C('WLN_PERPAGE'); //每页显示数
        $userWork = $this->getModel('UserWork');
        $dataList=$userWork->unionSelect('userSummerWorkCount', $data);// 查询满足要求的总记录数
        $count=count($dataList);
        $page=page($count,$_GET['p'],$perPage).','.$perPage;
        $this->pageList($count, $perPage, $data);
        $workList=$userWork->unionSelect('userSummerWork', $data, $page);
        /*载入模板标签*/
        $this->assign('workList', $workList); //年级数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 用户作业作答详情
     * @author demo
     */
    public function workAnswerInfo(){
        $sid = $_GET['sid'];//用户作业作答提交ID
		// echo $sid;
        if (empty($sid)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        //通过作答ID查找用户提交作业信息
        $sendInfo = $this->getModel('UserSendWork')->findData(
            'SendID,WorkID,SubjectID',
            'SendID=' . $sid
        );
		
		// dump($sendInfo);
		
		
        if(!empty($sendInfo)) {
            if($this->ifSubject && $this->mySubject) {
                if(!in_array($sendInfo['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712'); //您不能编辑非所属学科测试记录
                }
            }
            //测试信息从userword表中找出对应workid的testlist
            $testInfo = $this->getModel('UserWork')->findData(
                'TestList',
                'WorkID=' . $sendInfo['WorkID']
            );
			// dump($testInfo);
			
            //查用户作答是否正确的情况,查出试题编号testid和对应的答题情况
            $answer = $this->getModel('UserAnswerRecord')->unionSelect('userWorkRecordSelectByMsg', $sid, $testInfo['TestList']);
			// echo 111;
			// dump($answer);
            foreach ($answer as $iAnswer) {//将用户作答数据集变成以试题ID为下标的数据集
                $answer[$iAnswer['TestID']] = $iAnswer;
				
            }
			
			// dump($answer);
            $test = $this->getModel('TestReal');
			// echo 111;
            $host = C('WLN_DOC_HOST');
            //按测试记录包含的试题ID 查询试题的正确答案和题文
            $testField = 'TestID,Test,Answer';
            $testWhere = 'TestID in(' . $testInfo['TestList'] . ')';
            $list = $test->selectData(
                $testField,
                $testWhere);
			// echo 'list';
			// dump($list);
            foreach ($list as $ii => $listn) {
				//
                $list[$ii]['Test'] =  R('Common/TestLayer/strFormat',array($listn['Test']));
                $list[$ii]['Answer'] =  R('Common/TestLayer/strFormat',array($listn['Answer']));
                if ($answer[$listn['TestID']] || $answer[$listn['TestID']]['AnswerText'] != '') {//判断用户是否作答
                    $list[$ii]['AnswerText'] = $answer[$listn['TestID']]['AnswerText'];
                } else {
                    $list[$ii]['AnswerText'] = '<span style="color:red">未作答</span>';
                }
                //判断是否正确
                if ($answer[$listn['TestID']]['IfRight'] == 2) {
                    $list[$ii]['Right'] = '<span style="font-size:28px;color:#00FF00;font-weight:bold;">√</span>';
                } else {
                    $list[$ii]['Right'] = '<span style="font-size:28px;color:red;font-weight:bold;">×</span>';
                }
            }
			// dump($list);
			//获取地区
        $param['style']='area';
        $param['pID']=0;
		
        $arrArea=$this->getData($param);
		// dump($arrArea);
            $pageName = '用户作答记录';
            /*载入模板标签*/
            $this->assign('list', $list); // 赋值数据集
            $this->assign('pageName', $pageName); //页面标题
            $this->assign('edit', $testInfo); //数据
            $this->display();
        }
    }
    /**
     * 浏览用户各功能用户情况
     * @author demo
     */
    public function index() {
        $pageName = '组卷下载数据统计';
        //获取检错条件
		// dump($_REQUEST);
        $where=$this->getWhere($_REQUEST);
		
		// ['SchoolID'] = '1';
		$where['data'] .= "AND b.SchoolID = 99436";
		// dump($where);
        // $perpage = C('WLN_PERPAGE'); //每页显示数
        $dirResult=$this->getModel('LogIntellPaper')->unionSelect('dirTotalGroup', $where['data']); //统计智能组卷次数，做第三表
        $tplResult=$this->getModel('LogTplPaper')->unionSelect('tplTotalGroup', $where['data']); //统计模板组卷次数，做第三表
		// dump($dirResult);die;
        foreach($dirResult as $i=>$iDirResult){
            $lastResult[$iDirResult['UserName']]['UserName']=$iDirResult['UserName'];
            $lastResult[$iDirResult['UserName']]['SubjectStyle']=$iDirResult['SubjectStyle'];
            $lastResult[$iDirResult['UserName']]['ComTimes']=$iDirResult['ComTimes'];
            $lastResult[$iDirResult['UserName']]['Whois']=$iDirResult['Whois'];
            $lastResult[$iDirResult['UserName']]['AreaID']=$iDirResult['AreaID'];
            $lastResult[$iDirResult['UserName']]['dirTotal']=$iDirResult['dirTotal'];
            $lastResult[$iDirResult['UserName']]['SchoolName']=$iDirResult['SchoolName'];
            $lastResult[$iDirResult['UserName']]['RealName']=$iDirResult['RealName'];
            $lastResult[$iDirResult['UserName']]['Phonecode']=$iDirResult['Phonecode'];
            $lastResult[$iDirResult['UserName']]['Email']=$iDirResult['Email'];

        }
		// dump($lastResult);die;
		//在数组中添加组卷次数
        foreach($tplResult as $i=>$iTplResult){
            $lastResult[$iTplResult['UserName']]['UserName']=$iTplResult['UserName'];
            $lastResult[$iTplResult['UserName']]['SubjectStyle']=$iTplResult['SubjectStyle'];
            $lastResult[$iTplResult['UserName']]['ComTimes']=$iTplResult['ComTimes'];
            $lastResult[$iTplResult['UserName']]['Whois']=$iTplResult['Whois'];
            $lastResult[$iTplResult['UserName']]['AreaID']=$iTplResult['AreaID'];
            $lastResult[$iTplResult['UserName']]['SchoolName']=$iTplResult['SchoolName'];
            $lastResult[$iTplResult['UserName']]['RealName']=$iTplResult['RealName'];
            $lastResult[$iTplResult['UserName']]['Phonecode']=$iTplResult['Phonecode'];
            $lastResult[$iTplResult['UserName']]['Email']=$iTplResult['Email'];
            $lastResult[$iTplResult['UserName']]['tplTotal']=$iTplResult['tplTotal'];
            $lastResult[$iTplResult['UserName']]['Total']=$lastResult[$iTplResult['UserName']]['dirTotal']+$iTplResult['tplTotal'];
        }
		// dump($lastResult);die;
		//处理智能组卷和手工组卷次数
        foreach($lastResult as $i=>$iNewDir){
            if(!$iNewDir['tplTotal']){
                $lastResult[$i]['tplTotal']=0;
            }
            if(!$iNewDir['dirTotal']){
                $lastResult[$i]['dirTotal']=0;
            }
            $lastResult[$i]['Total']=$iNewDir['tplTotal']+$iNewDir['dirTotal'];
        }
		
        foreach ($lastResult as $i=>$iNewResult){
            $total[$i] = $iNewResult['Total'];
        }
		// dump($total);die;
        
        array_multisort($total,SORT_NUMERIC,SORT_DESC,$lastResult); //根据总数倒序排列
		$count=count($lastResult); //获取总数
		$perpage = C('WLN_PERPAGE'); 
        $page=page($count,$_GET['p'],$perpage); //分页条件
        $startPage=($page-1)*$perpage; //计算开始位置
        $lastResult=array_slice($lastResult,$startPage,$perpage);
		// dump($lastResult);die;
		
        $subject=SS('subject');
        $area=SS('areaList');
        $areaPath=SS('areaParentPath');
		
        foreach($lastResult as $i=>$iLastResult){
            foreach($areaPath[$lastResult[$i]['AreaID']] as $j=>$jAreaName){
				
                $lastResult[$i]['province']=$areaPath[$lastResult[$i]['AreaID']][0]['AreaName']; //获取省
                $lastResult[$i]['city']=$areaPath[$lastResult[$i]['AreaID']][1]['AreaName']; //获取市
            }
            $lastResult[$i]['AreaName'].=$area[$iLastResult['AreaID']]['AreaName']; //区名
            $lastResult[$i]['allTotal']=$iLastResult['dirTotal']+$iLastResult['tplTotal']; //总数
            $lastResult[$i]['SubjectName']=$subject[$lastResult[$i]['SubjectStyle']]['ParentName'].$subject[$lastResult[$i]['SubjectStyle']]['SubjectName']; //学科名称
        }
		// dump($lastResult);die;
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $this->pageList($count, $perpage, $where['map']);
        //获取地区
        $param['style']='area';
        $param['pID']=0;
		
        $arrArea=$this->getData($param);
		// dump($arrArea);
        /*载入模板标签*/
        $this->assign('arrArea',$arrArea);//地区
        /*载入模板标签*/
        $this->assign('list', $lastResult); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     *@用户组卷历史下载统计导出
     *@author demo
     */
    public function export() {
        //获取检错条件
        $where=$this->getWhere($_REQUEST);
        $perpage = 2000;
        if($_REQUEST['diffExport']){
            $userWork=$this->getModel('UserWork');
            $count=$userWork->unionSelect('userWorkCount', $where['data']);// 查询满足要求的总记录数
            if($count>2000 && empty($_REQUEST['p'])){
                $count=$userWork->unionSelect('userWorkCount', $where['data']);; // 查询满足要求的总记录
                // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
                $page=R('Common/SystemLayer/exportPageList',array($count,$perpage,$where['map']));
                $result['data']=$page;
                $result['ifPage']=1;
                $this->setBack($result);
            }
            if(!$_GET['p']){
                $this->setBack('');
            }
            if($_GET['p']=='all'){
                $_GET['p']=0;
            }
            $page=(page($count,$_GET['p'],$perpage)).','.$perpage; //分页条件
            $workList = $userWork->pageData(
                'WorkID,WorkName,SubjectID,WorkType,UserName,StartTime,EndTime,TestNum,LoreNum',
                $where['data'],
                'WorkID DESC',
                $page);
            $subject=SS('subject');
            foreach($workList as $i => $iWorkList){
                $workID[$i]=$iWorkList['WorkID'];
                $workList[$i]['SubjectName']=$subject[$iWorkList['SubjectID']]['ParentName'].$subject[$iWorkList['SubjectID']]['SubjectName'];
                $workList[$i]['WorkName']=$iWorkList['WorkName'];
                $workList[$i]['StartTime']=date('Y-m-d H:i:s',$iWorkList['StartTime']);
                $workList[$i]['EndTime']=date('Y-m-d H:i:s',$iWorkList['EndTime']);
                if($iWorkList['WorkType']==1){
                    $workList[$i]['WorkType']='【试题作业】';
                }else{
                    $workList[$i]['WorkType']='【导学案作业】';
                }
            }
            $workIDList=implode(',',$workID);//获取作业ID
            //根据作业ID查询作答人数
            $num=$this->getModel('UserWork')->unionSelect('userWorkDoNum',$workIDList);
            foreach($num as $i =>$iNum){
                $workDoNum[$iNum['WorkID']]=$iNum['DoNum'];
            }
            foreach($workList as $i=>$iWork){
                $iWork['DocNum']=0;
                if($workDoNum[$workList[$i]['WorkID']]){
                    $iWork['DocNum']=$workDoNum[$workList[$i]['WorkID']];
                }
                unset($iWork['SubjectID']);
                $workList[$i]=array_values($iWork);
            }
            //写入日志
            $this->adminLog($this->moduleName,'导出用户组卷历史下载统计记录where【'.$where['data'].'】');
            $keyName=array('作业编号','作业名称','作业类型','发布者','开始时间','结束时间','试题数量','知识点数量','学科','做题人数');
            $keyWidth=array('10','50','20','20','30','10','15','20','15','10');
            $excelName=array('title'=>'用户作业记录统计','excelName'=>'用户作业记录统计信息导出Excel');
            $excelMsg=R('Common/SystemLayer/resetExcelMsg',array($keyName,$keyWidth));
            R('Common/SystemLayer/excelExport',array($excelMsg,$workList,$excelName));
        }else{
            $count=$this->getTotal($where['data']);
            if($count>2000 && empty($_REQUEST['p'])){
                // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
                $page=R('Common/SystemLayer/exportPageList',array($count,$perpage,$where['map']));
                $result['data']=$page;
                $result['ifPage']=1;
                $this->setBack($result);
            }
            if(!$_GET['p']){
                $this->setBack('');
            }
            if($_GET['p']=='all'){
                $_GET['p']=0;
            }
            //写入日志
            $this->adminLog($this->moduleName,'导出用户组卷历史下载统计记录where【'.$where['data'].'】');

            $dirResult=$this->getModel('LogIntellPaper')->unionSelect('dirTotalGroup', $where['data']); //统计智能组卷次数，做第三表
            $tplResult=$this->getModel('LogTplPaper')->unionSelect('tplTotalGroup', $where['data']); //统计模板组卷次数，做第三表
            foreach($dirResult as $i=>$iDirResult){
                $lastResult[$iDirResult['UserName']]['UserName']=$iDirResult['UserName'];
                $lastResult[$iDirResult['UserName']]['SubjectStyle']=$iDirResult['SubjectStyle'];
                $lastResult[$iDirResult['UserName']]['ComTimes']=$iDirResult['ComTimes'];
                $lastResult[$iDirResult['UserName']]['Whois']=$iDirResult['Whois'];
                $lastResult[$iDirResult['UserName']]['AreaID']=$iDirResult['AreaID'];
                $lastResult[$iDirResult['UserName']]['dirTotal']=$iDirResult['dirTotal'];
                $lastResult[$iDirResult['UserName']]['SchoolName']=$iDirResult['SchoolName'];
                $lastResult[$iDirResult['UserName']]['RealName']=$iDirResult['RealName'];
                $lastResult[$iDirResult['UserName']]['Phonecode']=$iDirResult['Phonecode'];
                $lastResult[$iDirResult['UserName']]['Email']=$iDirResult['Email'];

            }
            foreach($tplResult as $i=>$iTplResult){
                $lastResult[$iDirResult['UserName']]['UserName']=$iTplResult['UserName'];
                $lastResult[$iDirResult['UserName']]['SubjectStyle']=$iTplResult['SubjectStyle'];
                $lastResult[$iDirResult['UserName']]['ComTimes']=$iTplResult['ComTimes'];
                $lastResult[$iDirResult['UserName']]['Whois']=$iTplResult['Whois'];
                $lastResult[$iDirResult['UserName']]['AreaID']=$iTplResult['AreaID'];
                $lastResult[$iDirResult['UserName']]['SchoolName']=$iTplResult['SchoolName'];
                $lastResult[$iDirResult['UserName']]['RealName']=$iTplResult['RealName'];
                $lastResult[$iDirResult['UserName']]['Phonecode']=$iTplResult['Phonecode'];
                $lastResult[$iDirResult['UserName']]['Email']=$iTplResult['Email'];
                $lastResult[$iTplResult['UserName']]['tplTotal']=$iTplResult['tplTotal'];
                $lastResult[$iTplResult['UserName']]['Total']=$lastResult[$iTplResult['UserName']]['dirTotal']+$iTplResult['tplTotal'];
            }
            foreach($lastResult as $i=>$iNewDir){
                if(!$iNewDir['tplTotal']){
                    $lastResult[$i]['tplTotal']=0;
                }
                if(!$iNewDir['dirTotal']){
                    $lastResult[$i]['dirTotal']=0;
                }
                $lastResult[$i]['Total']=$iNewDir['tplTotal']+$iNewDir['dirTotal'];
            }
            foreach ($lastResult as $i=>$iNewResult){
                $total[] = $iNewResult['Total'];
            }
            array_multisort($total,SORT_NUMERIC,SORT_DESC,$lastResult); //根据总数倒序排列

            foreach($lastResult as $i=>$iLastResult){
                $newArr[]=$iLastResult;
            }
            $subject=SS('subject');
            $area=SS('areaList');
            $areaPath=SS('areaParentPath');

            //显示具体数据
            foreach($newArr as $i=>$iLastResult){
                foreach($areaPath[$newArr[$i]['AreaID']] as $j=>$jAreaName){
                    $newArr[$i]['province']=$areaPath[$newArr[$i]['AreaID']][0]['AreaName'];
                    $newArr[$i]['city']=$areaPath[$newArr[$i]['AreaID']][1]['AreaName'];
                }
                $newArr[$i]['AreaName'].=$area[$iLastResult['AreaID']]['AreaName'];
                $newArr[$i]['allTotal']=$iLastResult['Total'];
                $newArr[$i]['SubjectName']=$subject[$newArr[$i]['SubjectStyle']]['ParentName'].$subject[$newArr[$i]['SubjectStyle']]['SubjectName'];
            }
            foreach($newArr as $i=>$iNewArr){
                if($iNewArr['Whois']=='0'){
                    $iNewArr['Whois']='【学生】';
                }elseif($iNewArr['Whois']=='1'){
                    $iNewArr['Whois']='【教师】';
                }
                unset($iNewArr['SubjectStyle']);
                unset($iNewArr['AreaID']);
                unset($iNewArr['allTotal']);
                $newArr[$i]=array_values($iNewArr);
            }

            $limit=page($count,$_GET['p'],$perpage); //分页条件
            if($limit){
                $newArr=array_slice($newArr,($limit-1)*$perpage,$perpage);
            }
            $keyName=array('用户名','组卷次数','身份','模板下载次数','学校名称','真实姓名','联系方式','邮箱','智能下载次数','下载总次数','省','市','区/县','所属学科');
            $keyWidth=array('30','10','10','20','30','20','20','20','10','10','20','20','20','20');
            $excelName=array('title'=>'用户组卷历史下载统计','excelName'=>'用户组卷历史下载统计信息导出Excel');
            $excelMsg=R('Common/SystemLayer/resetExcelMsg',array($keyName,$keyWidth));
            R('Common/SystemLayer/excelExport',array($excelMsg,$newArr,$excelName));
        }
    }
    /**
     * 获取查询条件
     * @param $where array
     * @return array
     * @author demo
     */
    private function getWhere($where){
        if($_REQUEST['diffExport']){
            $data='1=1';
            if($_REQUEST['name']){//教师用户名
                //简单查询
                $map['name']=$_REQUEST['name'];
                $data['UserName']=$_REQUEST['name'];
            }else{
                //高级查询
                if($_REQUEST['teacherName']){//教师用户名
                    $map['teacherName'] = $_REQUEST['teacherName'];
                    $data['UserName'] = $_REQUEST['teacherName'];
                }
                if(is_numeric($_REQUEST['num'])){//试题数量
                    $map['num']=$_REQUEST['num'];
                    $data['TestNum']=$_REQUEST['num'];
                }
                if(is_numeric($_REQUEST['workType'])){//作业类型
                    $map['workType']=$_REQUEST['workType'];
                    $data['WorkType']=$_REQUEST['workType'];
                }
                if($_REQUEST['workStyle']){//作答方式
                    $map['workStyle'] = $_REQUEST['workStyle'];
                    $data['WorkStyle'] = $_REQUEST['workStyle'];
                }
                if(is_numeric($_REQUEST['doTime'])){
                    $map['doTime']=$_REQUEST['doTime'];
                    $data['doTime']=$_REQUEST['doTime'];
                }
            }
            $where['data']=$data;
            $where['map']=$map;
        }else{
            //获取检错条件
            $map = array ();
            $data = ' 1=1 ';
            if ($_REQUEST['name']) {
                //简单查询
                $map['name'] = $_REQUEST['name'];
                $data .= ' AND a.UserName = "' . $_REQUEST['name'] . '" ';
            } else {
                //高级查询
                if ($_REQUEST['UserName']) {
                    $map['UserName'] = $_REQUEST['UserName'];
                    $data .= ' AND a.UserName = "' . $_REQUEST['UserName'] . '" ';
                }
                if ($_REQUEST['RealName']) {
                    $map['RealName'] = $_REQUEST['RealName'];
                    $data .= ' AND b.RealName = "' . $_REQUEST['RealName'] . '" ';
                }
                if (is_numeric($_REQUEST['Whois'])) {
                    $map['Whois'] = $_REQUEST['Whois'];
                    $data .= ' AND b.Whois = "' . $_REQUEST['Whois'] . '" ';
                }
                //获取地区
                if(!$_REQUEST['SchoolID']){
                    if($_REQUEST['AreaID']){
                        $map['AreaID']=end($_REQUEST['AreaID']);
                        if(empty($map['AreaID'])){
                            $this->setError('30727',IS_AJAX); //请选择最后一级地区数据！
                        }
                        $data .=' AND b.AreaID ='.end($_REQUEST['AreaID']);
                    }
                }else{
                    $map['SchoolID']=$_REQUEST['SchoolID'];
                    $data .=' AND b.SchoolID ='.$_REQUEST['SchoolID'];
                }
                $start = $_REQUEST['Start'];
                if(strstr($start,'-')){
                    $start=strtotime($start);
                }
                $end = $_REQUEST['End'];
                if(strstr($end,'-')){
                    $end=strtotime($end);
                }
                if ($start) {
                    if (empty ($end)) $end = time();
                    $map['Start'] = $start;
                    $map['End'] = $end;
                    $_REQUEST['Start']=date('Y-m-d',$start);
                    $_REQUEST['End']=date('Y-m-d',$end);
                    $data .= ' AND a.AddTime between ' . $start . ' and ' . $end . ' ';
                }
            }
            $where['data']=$data;
            $where['map']=$map;
        }

        return $where;
    }
    /**
     * 按条件查询总数
     * @param array $where
     * @return int
     * @author demo
     */
    private function getTotal($where){
        $dirResult=$this->getModel('LogIntellPaper')->unionSelect('dirTotalGroup',$where); //统计智能组卷次数，做第三表
        $tplResult=$this->getModel('LogTplPaper')->unionSelect('tplTotalGroup', $where); //统计模板组卷次数，做第三表
        foreach($dirResult as $i=>$iDirResult){
            $lastResult[$iDirResult['UserName']]['UserName']=$iDirResult['UserName'];
            $lastResult[$iDirResult['UserName']]['SubjectStyle']=$iDirResult['SubjectStyle'];
            $lastResult[$iDirResult['UserName']]['ComTimes']=$iDirResult['ComTimes'];
            $lastResult[$iDirResult['UserName']]['Whois']=$iDirResult['Whois'];
            $lastResult[$iDirResult['UserName']]['AreaID']=$iDirResult['AreaID'];
            $lastResult[$iDirResult['UserName']]['dirTotal']=$iDirResult['dirTotal'];
            $lastResult[$iDirResult['UserName']]['SchoolName']=$iDirResult['SchoolName'];
            $lastResult[$iDirResult['UserName']]['RealName']=$iDirResult['RealName'];
            $lastResult[$iDirResult['UserName']]['Phonecode']=$iDirResult['Phonecode'];
            $lastResult[$iDirResult['UserName']]['Email']=$iDirResult['Email'];

        }
        foreach($tplResult as $i=>$iTplResult){
            $lastResult[$iTplResult['UserName']]['UserName']=$iTplResult['UserName'];
            $lastResult[$iTplResult['UserName']]['SubjectStyle']=$iTplResult['SubjectStyle'];
            $lastResult[$iTplResult['UserName']]['ComTimes']=$iTplResult['ComTimes'];
            $lastResult[$iTplResult['UserName']]['Whois']=$iTplResult['Whois'];
            $lastResult[$iTplResult['UserName']]['AreaID']=$iTplResult['AreaID'];
            $lastResult[$iTplResult['UserName']]['SchoolName']=$iTplResult['SchoolName'];
            $lastResult[$iTplResult['UserName']]['RealName']=$iTplResult['RealName'];
            $lastResult[$iTplResult['UserName']]['Phonecode']=$iTplResult['Phonecode'];
            $lastResult[$iTplResult['UserName']]['Email']=$iTplResult['Email'];
            $lastResult[$iTplResult['UserName']]['tplTotal']=$iTplResult['tplTotal'];
            $lastResult[$iTplResult['UserName']]['Total']=$lastResult[$iTplResult['UserName']]['dirTotal']+$iTplResult['tplTotal'];
        }
        foreach($lastResult as $i=>$iNewDir){
            if(!$iNewDir['tplTotal']){
                $lastResult[$i]['tplTotal']=0;
            }
            if(!$iNewDir['dirTotal']){
                $lastResult[$i]['dirTotal']=0;
            }
            $lastResult[$i]['Total']=$iNewDir['tplTotal']+$iNewDir['dirTotal'];
        }
        foreach ($lastResult as $i=>$iNewResult){
            $total[$i] = $iNewResult['Total'];
        }
        $count=count($lastResult); //获取总数
        return $count;
    }

    //地区学校人数统计
    public function areaSchoolUserList(){
        //用户根据学校统计
        //简单查询
        $pageName="地区学校人数统计";

        //设置顶级省份
        $bufferz=SS('areaChildList');  // 缓存子类list数据
        $areaArray = $bufferz[0]; //省份数据集
        if(!empty($_REQUEST['AreaID'])){
        $data='1=1 and a.SchoolID > 0 ';
        if($_REQUEST['ip']){
            $ip=preg_replace("/\s/","",$_REQUEST['ip']);
            if(!empty($ip)){
                $map['IPAddress']=ip2long($ip);
                $data .= ' AND c.IPAddress='.ip2long($ip);
            }
        }
        //没有设置学校的人数
        $noSchool=$this->getModel('User')->selectCount(
            'SchoolID=0',
            'UserID'
        );
        $teacherList=$this->getModel('User')->unionSelect('userTeacherTotalBySchool', $data);
        $studentList=$this->getModel('User')->unionSelect('userStudentTotalBySchool', $data);
        foreach($teacherList as $i=>$iTeacher){
                $totalMsg[$iTeacher['SchoolID']]=$iTeacher;
                $totalMsg[$iTeacher['SchoolID']]['StudentTotal']=0;
        }
        foreach($studentList as $j=>$jStudent){
                $studentArr[$jStudent['SchoolID']]=$jStudent;
                $studentArr[$jStudent['SchoolID']]['TeacherTotal']=0;
        }
        foreach($studentArr as $i=>$iStudent){
            if(!empty($totalMsg[$i])){
                $totalMsg[$i]['StudentTotal']=$studentArr[$i]['StudentTotal'];
            }else{
                $totalMsg[$i]=$studentArr[$i];
            }
        }
        $areaPath=SS('areaParentPath');
        $areaList=SS('areaList');
        foreach($totalMsg as $i=>$iTotalMsg){
            //将数据用学校ID为索引
            $newTotalMsg[$totalMsg[$i]['AreaID']][]=$iTotalMsg;
            $totalMsg[$i]['AreaPath']=$areaPath[$iTotalMsg['AreaID']];
            $key=count($totalMsg[$i]['AreaPath']);
            $totalMsg[$i]['AreaPath'][$key]=$areaList[$iTotalMsg['AreaID']];
        }
        //处理省份地区
        $sheng='';
        foreach($totalMsg as $i=>$iTotalMsg){
            foreach($totalMsg[$i]['AreaPath'] as $j=>$jTotalMsg){
                if(!in_array($totalMsg[$i]['AreaPath'][0],$sheng) && !empty($totalMsg[$i]['AreaPath'][0])){
                    //设置查询指定省份
                    if($totalMsg[$i]['AreaPath'][0]['AreaID']==$_REQUEST['AreaID']){
                        $sheng[]=$totalMsg[$i]['AreaPath'][0];
                    }
                }
            }
        }
        //处理市
        foreach($totalMsg as $i=>$iTotalMsg){
            //直辖市
            if(count($totalMsg[$i]['AreaPath'])==2){
                foreach($totalMsg[$i]['AreaPath'] as $j=>$jTotalMsg){
                    foreach($sheng as $k=>$kSheng){
                        if($totalMsg[$i]['AreaPath'][0]['AreaID']==$sheng[$k]['AreaID'] ){
                            if(!in_array($totalMsg[$i]['AreaPath'][0],$sheng[$k]['sub'])){
                                $sheng[$k]['sub'][]=$totalMsg[$i]['AreaPath'][0];
                            }
                        }
                    }
                }
            }else{
                foreach($totalMsg[$i]['AreaPath'] as $j=>$jTotalMsg){
                    foreach($sheng as $k=>$kSheng){
                        if($totalMsg[$i]['AreaPath'][0]['AreaID']==$sheng[$k]['AreaID']){
                            if(!in_array($totalMsg[$i]['AreaPath'][1],$sheng[$k]['sub'])){
                                $sheng[$k]['sub'][]=$totalMsg[$i]['AreaPath'][1];
                            }
                        }
                    }
                }
            }
        }
        //处理区，县
        foreach($totalMsg as $i=>$iTotalMsg){
            foreach($totalMsg[$i]['AreaPath'] as $j=>$jTotalMsg){
                foreach($sheng as $k=>$kSheng){
                    foreach($sheng[$k]['sub'] as $l=>$lSheng){
                        if(count($totalMsg[$i]['AreaPath'])==2){
                            if($totalMsg[$i]['AreaPath'][0]['AreaID']==$sheng[$k]['sub'][$l]['AreaID']){
                                if(!in_array($totalMsg[$i]['AreaPath'][1],$sheng[$k]['sub'][$l]['sub'])){
                                    $sheng[$k]['sub'][$l]['sub'][]=$totalMsg[$i]['AreaPath'][1];
                                }
                            }
                        }else{
                            if($totalMsg[$i]['AreaPath'][1]['AreaID']==$sheng[$k]['sub'][$l]['AreaID']){
                                if(!in_array($totalMsg[$i]['AreaPath'][2],$sheng[$k]['sub'][$l]['sub'])){
                                    $sheng[$k]['sub'][$l]['sub'][]=$totalMsg[$i]['AreaPath'][2];
                                }
                            }
                        }

                    }
                }
            }
        }
        //省份整理完毕
        //根据学校的AreaID分配到各个区下
        foreach($sheng as $i=>$iSheng){
            foreach($sheng[$i]['sub'] as $j=>$jSheng){
                foreach($sheng[$i]['sub'][$j]['sub'] as $l=>$lSheng){
                    $sheng[$i]['sub'][$j]['sub'][$l]['schoolList']=$newTotalMsg[$sheng[$i]['sub'][$j]['sub'][$l]['AreaID']];
                    $sheng[$i]['sub'][$j]['sub'][$l]['countNum']=count($sheng[$i]['sub'][$j]['sub'][$l]['schoolList']);
                    $sheng[$i]['sub'][$j]['countNum']+=$sheng[$i]['sub'][$j]['sub'][$l]['countNum'];
                }
                $sheng[$i]['countNum']+=$sheng[$i]['sub'][$j]['countNum'];
            }

        }
        $td='<tr class="row">';
        foreach($sheng as $i=>$iList){
            $td.="<td rowspan='".$sheng[$i]['countNum']."' style='text-align:center'>".$sheng[$i]['AreaName']."</td>";
            foreach($iList['sub'] as $j=>$jList){
                $rowspan2=$jList['countNum'];
                $td.="<td style='text-align:center' rowspan='".$rowspan2."'>".$jList['AreaName']."</td>";
                foreach($jList['sub'] as $k=>$kList){
                    $rowspan3=$kList['countNum'];
                    $td.="<td style='text-align:center' rowspan='".$rowspan3."'>".$kList['AreaName']."</td>";
                    foreach($kList['schoolList'] as $l=>$lList){
                        $td.="<td>".$lList['SchoolName']."</td>";
                        $td.="<td>".($lList['TeacherTotal']+$lList['StudentTotal'])."</td>";
                        $td.="<td>".$lList['TeacherTotal']."</td>";
                        $td.="<td>".$lList['StudentTotal']."</td>";
                        $td.="<td><a target='_blank' href='".U('User/User/index',array('SchoolID'=>$lList['SchoolID']))."'>查看成员 </a></td>";
                        $td.="</tr>";
                    }
                }
            }
        }
        $td.='</tr>';
        $this->assign('td',$td);
        }
        $this->assign('noSchool', $noSchool); //页面标题
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('areaArray', $areaArray); //页面标题
        $this->display();
    }


    /**
     * 统计工作量
     * @author demo
     */
    public function adminWork(){
        $pageName = '工作量统计';

        if(IS_POST){
            $start=strtotime($_POST['Start']);
            $end= $_POST['End'] ? strtotime($_POST['End']): time();
            $where['IntroFirstTime']  = array('between',array($start,$end));

            $doc=$this->getModel('Doc');
            $docBuffer=$doc->groupData('count(DocID) as Num,Admin',$where,'Admin');
            $testBuffer=$doc->unionSelect('docSelectAllGroup',$where);
            $this->assign('docBuffer',$docBuffer);
            $this->assign('testBuffer',$testBuffer);
        }

        /*载入模板标签*/
        $this->assign('pageName',$pageName);
        $this->display();
    }

    /**
     * 用户答题统计
     * @author demo 16-5-23
     */
    public function userAnswer(){
			
        if(IS_POST || is_numeric($_GET['p'])){
            $params = array();
            $username = $_REQUEST['UserName'];
            if(empty($username)){
                $username = $_REQUEST['name'];
            }
            $params['p'] = (int)$_GET['p'];
            if(!$params['p']){
                $params['p'] = 1;
            }
            $params['prepage'] = 30;
            $params['UserName'] = $username;
            $params['TestID'] = (int)$_REQUEST['TestID'];
            $params['Start'] = $_REQUEST['Start'];
            $params['End'] = $_REQUEST['End'];
            $params['mode'] = $_REQUEST['mode'];
			// dump($params);die;
            $data = $this->getModel('UserAnswerRecord')->statUserAnswer($params, $params['mode']);
			//处理时间戳
            if(preg_match('/[^\d]/m', $params['Start'])){
                $params['Start'] = str_replace('+', ' ', $params['Start']);
                $params['Start'] = strtotime($params['Start']);
            }else{
                if(!empty($params['Start']))
                    $_REQUEST['Start'] = date('Y-m-d H:i:s', $params['Start']);
            }
            if(preg_match('/[^\d]/m', $params['End'])){
                $params['End'] = str_replace('+', ' ', $params['End']);
                $params['End'] = strtotime($params['End']);
            }else{
                if(!empty($params['End']))
                    $_REQUEST['End'] = date('Y-m-d H:i:s', $params['End']);
            }
            $this->pageList($data['count'], $params['prepage'], $params);
            $this->assign('list', $data['data']);
        }

        $this->assign('pageName', "用户作答统计");
        if($params['mode'] != ''){
            $this->assign('pageName', "用户【{$username}】作答明细");
            $this->display('userAnswerDetail');
        }else{
            $this->assign('total', $data['total']);
            $this->display();
        }
    }

    /**
     * 学校活跃度统计
     * @author demo
     */
    public function schoolUseList(){
        $pageName = '学校下载活跃度';
        if($_REQUEST['time']){
            $time=$_REQUEST['time'];
            if(!is_numeric($time)) $time=strtotime($time);
            $buffer=$this->getModel('Base')->unionSelect('userSchoolUseDown',$time);
        }
        /*载入模板标签*/
        $this->assign('buffer',$buffer);
        $this->assign('pageName',$pageName);
        $this->display();
    }
    /**
     * 学校活跃用户统计
     * @author demo
     */
    public function schoolUseListUser(){
        $pageName = '学校用户下载活跃度';
		
        if($_REQUEST['time'] && is_numeric($_REQUEST['SchoolID'])){
            $time=$_REQUEST['time'];
            $schoolID=$_REQUEST['SchoolID'];
            if(!is_numeric($time)) $time=strtotime($time);
            $buffer=$this->getModel('Base')->unionSelect('userSchoolUseDownUser',$time,$schoolID);
        }

        /*载入模板标签*/
        $this->assign('buffer',$buffer);
        $this->assign('pageName',$pageName);
        $this->display();
    }
	/**
	* 学生刷题统计
	*
	*/
	public function studentWorkList(){
		$pageName = '学生做题统计';
		$subjectArray = SS('subjectParentId');
		//获取查询条件
		$data['1']='1';
        if($_REQUEST['name']){//教师用户名
            //简单查询
            $map['name']=$_REQUEST['name'];
            $data['UserName']=$_REQUEST['name'];
        }else{
            //高级查询
            if($_REQUEST['teacherName']){//教师用户名
                $map['teacherName'] = $_REQUEST['teacherName'];
                $data['UserName'] = $_REQUEST['teacherName'];
            }
            if(is_numeric($_REQUEST['num'])){//试题数量
                $map['num']=$_REQUEST['num'];
                $data['TestNum']=$_REQUEST['num'];
            }
            if(is_numeric($_REQUEST['workType'])){//作业类型
                $map['workType']=$_REQUEST['workType'];
                $data['WorkType']=$_REQUEST['workType'];
            }
            if($_REQUEST['workStyle']){//作答方式
                $map['workStyle'] = $_REQUEST['workStyle'];
                $data['WorkStyle'] = $_REQUEST['workStyle'];
            }
            if(is_numeric($_REQUEST['doTime'])){
                $map['doTime']=$_REQUEST['doTime'];
                $data['doTime']=$_REQUEST['doTime'];
            }
        }
		// $data['SchoolID'] = '1';
		dump($data);
        $perPage = C('WLN_PERPAGE'); //每页显示数
        $count=$this->getModel('UserWork')->unionSelect('userWorkCount', $data);// 查询满足要求的总记录数
		// dump($count);
        $page=page($count,$_GET['p'],$perPage).','.$perPage;
        $this->pageList($count, $perPage, $map);
        // 进行分页数据查询
		// $data['SchoolID'] = '1';
        $workList=$this->getModel('UserWork')->unionSelect('userWorkStatistic', $data,$page);
		
		
		// dump($workList);
		foreach($workList as $i => $iWorkList){
			$arr[]=$iWorkList['UserName'];
			
		}
		
        $workDoNum=array();//作答人数
        if(!empty($workList)){
            $subject=SS('subject');
			//
			dump($subject);
            foreach($workList as $i => $iWorkList){
                $workID[$i]=$iWorkList['WorkID'];
                $subjectName=$subject[$iWorkList['SubjectID']]['ParentName'].$subject[$iWorkList['SubjectID']]['SubjectName'];
                $workList[$i]['WorkName']=$iWorkList['WorkName'].'【'.$subjectName.'】';
                $workList[$i]['StartTime']=date('Y-m-d H:i:s',$iWorkList['StartTime']);
                $workList[$i]['EndTime']=date('Y-m-d H:i:s',$iWorkList['EndTime']);
                if($iWorkList['WorkType']==1){
                    $workList[$i]['WorkType']='【试题作业】';
                }else{//2 是导学案作业
                    $workList[$i]['WorkType']='【导学案作业】';
                }
            }
			dump($workList);
            $workIDList=implode(',',$workID);//获取作业ID
            dump($workIDList);
            //根据作业ID查询作答人数
            $num=$this->getModel('UserWork')->unionSelect('userWorkDoNum',$workIDList);
			// dump($num);
            foreach($num as $i =>$iNum){
                $workDoNum[$iNum['WorkID']]=$iNum['DoNum'];
            }
			// dump($workDoNum);
        }
		
        /*载入模板标签*/
        $this->assign('workList', $workList); //年级数据集
        $this->assign('workDoNum',$workDoNum);
		$this->assign('subjectArray', $subjectArray);
		
		$this->assign('pageName',$pageName);
		$this->display();
	}
	// /**
	// * 教师评语统计
	// *
	// */
	public function teacherComment(){
		$pageName = '教师评语统计';
		$this->assign('pageName',$pageName);
		$this->display();
	}
	/**
	*  学生错题统计
	*
	*/
	public function wrongTest(){
		$pageName = '学生错题统计';
		$this->assign('pageName',$pageName);
		$this->display();
	}
	// /**
	// * 微课视频统计
	// *
	// */
	public function wkvedio(){
		$pageName = '微课视频统计';
		$wk = M('kl_study');
		$wkcount = $wk->count('StudyID');
		// dump($wkcount);
		
		$lstdata = $wk
		->join('zj_knowledge ON zj_kl_study.KLID=zj_knowledge.KLID','left')
		->join('zj_subject ON zj_knowledge.SubjectID=zj_subject.SubjectID','left')
		->select();
		
		// $lstdata = M()->table('zj_kl_study a,zj_knowledge b')
		// ->where('a.KLID=b.KLID')
		// ->field('a.LoadTime,b.KLName,b.SubjectID,b.PID')
		// ->select();
		
		
		
		// dump($lstdata);
		$this->assign(array(
			'wkcount'=>$wkcount,
		));
		$this->assign('pageName',$pageName);
		$this->display();
	}
	// /**
	// * 教师批改统计
	// *
	// */
	public function teachercheck(){
		$pageName = '教师批改统计';
		$this->assign('pageName',$pageName);
		$this->display();
	}
	
	// /**
	// * 教师批改统计
	// *
	// */
	public function Teacherzj(){
		//只统计有过智能组卷记录的教师
		$pageName = '教师组卷下载统计';
	 
		$where=$this->getWhere($_REQUEST);
		
		// $where['data'] .= "AND b.SchoolID= 1";
		// dump($where);
       
        $dirResult=$this->getModel('LogIntellPaper')->unionSelect('dirTotalGroup', $where['data']); //统计智能组卷次数，做第三表
		// dump($dirResult);
        $tplResult=$this->getModel('LogTplPaper')->unionSelect('tplTotalGroup', $where['data']); //统计模板组卷次数，做第三表
		
		// dump($tplResult);
        foreach($dirResult as $i=>$iDirResult){
            $lastResult[$iDirResult['UserName']]['UserName']=$iDirResult['UserName'];
            $lastResult[$iDirResult['UserName']]['SubjectStyle']=$iDirResult['SubjectStyle'];
            $lastResult[$iDirResult['UserName']]['ComTimes']=$iDirResult['ComTimes'];
            $lastResult[$iDirResult['UserName']]['Whois']=$iDirResult['Whois'];
            $lastResult[$iDirResult['UserName']]['AreaID']=$iDirResult['AreaID'];
            $lastResult[$iDirResult['UserName']]['dirTotal']=$iDirResult['dirTotal'];
            $lastResult[$iDirResult['UserName']]['SchoolName']=$iDirResult['SchoolName'];
            $lastResult[$iDirResult['UserName']]['RealName']=$iDirResult['RealName'];
            $lastResult[$iDirResult['UserName']]['Phonecode']=$iDirResult['Phonecode'];
            $lastResult[$iDirResult['UserName']]['Email']=$iDirResult['Email'];

        }
		// dump($lastResult);
		//在数组中添加组卷次数
        foreach($tplResult as $i=>$iTplResult){
            $lastResult[$iTplResult['UserName']]['UserName']=$iTplResult['UserName'];
            $lastResult[$iTplResult['UserName']]['SubjectStyle']=$iTplResult['SubjectStyle'];
            $lastResult[$iTplResult['UserName']]['ComTimes']=$iTplResult['ComTimes'];
            $lastResult[$iTplResult['UserName']]['Whois']=$iTplResult['Whois'];
            $lastResult[$iTplResult['UserName']]['AreaID']=$iTplResult['AreaID'];
            $lastResult[$iTplResult['UserName']]['SchoolName']=$iTplResult['SchoolName'];
            $lastResult[$iTplResult['UserName']]['RealName']=$iTplResult['RealName'];
            $lastResult[$iTplResult['UserName']]['Phonecode']=$iTplResult['Phonecode'];
            $lastResult[$iTplResult['UserName']]['Email']=$iTplResult['Email'];
            $lastResult[$iTplResult['UserName']]['tplTotal']=$iTplResult['tplTotal'];
            $lastResult[$iTplResult['UserName']]['Total']=$lastResult[$iTplResult['UserName']]['dirTotal']+$iTplResult['tplTotal'];
        }
		// dump($lastResult);die;
		//处理智能组卷和手工组卷次数
        foreach($lastResult as $i=>$iNewDir){
            if(!$iNewDir['tplTotal']){
                $lastResult[$i]['tplTotal']=0;
            }
            if(!$iNewDir['dirTotal']){
                $lastResult[$i]['dirTotal']=0;
            }
            $lastResult[$i]['Total']=$iNewDir['tplTotal']+$iNewDir['dirTotal'];
        }
		
        foreach ($lastResult as $i=>$iNewResult){
            $total[$i] = $iNewResult['Total'];
        }
		// dump($total);die;
        
        array_multisort($total,SORT_NUMERIC,SORT_DESC,$lastResult); //根据总数倒序排列
		$count=count($lastResult); //获取总数
		// $perpage = C('WLN_PERPAGE'); //每页显示数
		$perpage = 2; //每页显示数
        $page=page($count,$_GET['p'],$perpage); //分页条件
        $startPage=($page-1)*$perpage; //计算开始位置
        $lastResult=array_slice($lastResult,$startPage,$perpage);
		// dump($lastResult);die;
		
        $subject=SS('subject');
        $area=SS('areaList');
        $areaPath=SS('areaParentPath');
		
        foreach($lastResult as $i=>$iLastResult){
            foreach($areaPath[$lastResult[$i]['AreaID']] as $j=>$jAreaName){
				
                $lastResult[$i]['province']=$areaPath[$lastResult[$i]['AreaID']][0]['AreaName']; //获取省
                $lastResult[$i]['city']=$areaPath[$lastResult[$i]['AreaID']][1]['AreaName']; //获取市
            }
            $lastResult[$i]['AreaName'].=$area[$iLastResult['AreaID']]['AreaName']; //区名
            $lastResult[$i]['allTotal']=$iLastResult['dirTotal']+$iLastResult['tplTotal']; //总数
            $lastResult[$i]['SubjectName']=$subject[$lastResult[$i]['SubjectStyle']]['ParentName'].$subject[$lastResult[$i]['SubjectStyle']]['SubjectName']; //学科名称
        }
		// dump($lastResult);
		// die;
		foreach($lastResult as $i=>$idata){
			$arr0[]=$idata['RealName'];
			$arr1[]=(int)$idata['ComTimes'];
			$arr2[]=(int)$idata['dirTotal'];
			$arr3[]=(int)$idata['tplTotal'];
			$arr4[]=(int)$idata['allTotal'];
		}
		
		$RealName=json_encode($arr0);
		$ComTimes=json_encode($arr1);
		$dirTotal=json_encode($arr2);
		$tplTotal=json_encode($arr3);
		$allTotal=json_encode($arr4);
		// dump($arr1);
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $this->pageList($count, $perpage, $where['map']);
        //获取地区
        $param['style']='area';
        $param['pID']=0;
		
        $arrArea=$this->getData($param);
		// dump($arrArea);
        /*载入模板标签*/
        $this->assign('arrArea',$arrArea);//地区
        /*载入模板标签*/
		$this->assign('RealName',$RealName);
		$this->assign('ComTimes',$ComTimes);
		$this->assign('dirTotal',$dirTotal);
		$this->assign('tplTotal',$tplTotal);
		$this->assign('allTotal',$allTotal);
        $this->assign('list', $lastResult); // 赋值数据集
		
		$this->assign('pageName',$pageName);
		$this->display();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}