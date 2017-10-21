<?php
/**
 * 作业处理类(包括普通作业和导学案作业)
 * @author demo、、、、
 * @date 2015年8月26日
 */
/**
 * 作业模块控制器类，用于处理作业操作
 */
namespace Work\Controller;
class WorkController extends BaseController {

    private $workStyle = 'test';
    private $workType  = 1;
    private $workTitle = '作业';
    /**
     * 设置作业类型
     * @author demo
     */
    public function _initialize(){
        $workStyle=$_REQUEST['workStyle'];
        if($workStyle=='case'){
            $this->workStyle = 'case';
            $this->workType  = 2;
            $this->workTitle = '导学案';
        }
    }
    /**布置作业开始**/

    /**
     * 布置作业
     */
    public function addWork() {
        $this->assign('pageName','布置'.$this->workTitle);

        $tpl='';
        if($this->workStyle=='case'){
            $tpl='Work/addCaseWork';
        }

        $this->display($tpl);
    }

    /**
     * 获取教师留作业的存档
     */
    public function getLeavedUserWork(){
        $userName  = $this->getCookieUserName();
        $subjectID = cookie('SubjectId');
        if(!$subjectID) $this->setError('1X3304',1);
        if(empty($_POST['page'])) $_POST['page']=1;//分页为空 返回第一页内容
        $perPage=10;//每页显示数目
        $param=array(
            'userName'=>$userName,
            'subjectID'=>$subjectID,
            'perPage'=>$perPage
        );

        switch($this->workStyle){
            case 'test':
                $result = $this->getLeavedUserWorkByTest($param);
                break;
            case 'case':
                $result = $this->getLeavedUserWorkByCase($param);
                break;
        }

        if(!$result['count']) $this->setBack(array('error','暂无'.$this->workTitle.'存档！'));

        $output = array();
        $output[0] = 'success';
        $output[1] = $result['content'];
        $output[2] = $result['count'];
        $output[3] = $perPage;
        $this->setBack($output);
    }

    /**
     * 删除教师留作业的存档
     */
    public function delLeavedUserWork(){
        $id = $_POST['id'];
        if(!is_numeric($id)) $this->setError('30301',1);
        $userName = $this->getCookieUserName();
        $param=array(
            'id'=>$id,
            'userName'=>$userName
        );

        switch($this->workStyle){
            case 'test':
                $saveName = $this->delLeavedUserWorkByTest($param);
                break;
            case 'case':
                $saveName = $this->delLeavedUserWorkByCase($param);
                break;
        }

        //写入动态
        $user = $this->getModel('User');
        $buffer = $user->getInfoByName($userName);
        $uid=$buffer[0]['UserID'];
        $this->classDynamic(0,array($uid),'删除了'.$this->workTitle.'存档<span class="v_id" vid="'.$id.'">#'.$saveName
            .'</span>。');
        $this->setBack('success');
    }

    /**
     * 更改教师留作业的的名称
     */
    public function changeLeavedUserWorkName(){
        $id       =  $_POST['id'];
        $userName = $this->getCookieUserName();
        $newName  = formatString('stripTags',$_POST['newName']);
        if(!is_numeric($id)) $this->setError('30301',1);
        $param=array(
            'id'=>$id,
            'userName'=>$userName,
            'newName'=>$newName
        );

        switch($this->workStyle){
            case 'test':
                $oldName = $this->changeLeavedUserWorkNameByTest($param);
                break;
            case 'case':
                $oldName = $this->changeLeavedUserWorkNameByCase($param);
                break;
        }

        //写入动态
        $user=$this->getModel('User');
        $buffer=$user->getInfoByName($userName);
        $uid=$buffer[0]['UserID'];
        $this->classDynamic(0,array($uid),'修改了'.$this->workTitle.'名称，由<span class="v_id" vid="'.$id.'">#'.$oldName.'</span>改为<span class="v_id" vid="'.$id.'">#'.$newName.'</span>。');
        $this->setBack('success');
    }

    /**
     * 查看已留作业的内容
     */
    public function showLeavedUserWork(){
        $id        = $_POST['id'];
        $userName = $this->getCookieUserName();
        if(!is_numeric($id)) $this->setError('30301',1);
        $param=array(
            'id'=>$id,
            'userName'=>$userName
        );
        switch($this->workStyle){
            case 'test':
                $result=$this->showLeavedUserWorkByTest($param);
                break;
            case 'case':
                $result=$this->showLeavedUserWorkByCase($param);
                break;
        }

        if($result) $this->setBack($result);
    }

    /**
     * 给学生布置作业
     */
    public function assignLeavedUserWork(){
        $workID    = $_POST['workID'];
        $workName  = $_POST['workName'];

        //布置作业成功后保存该文档 @notice当前流程同一份文档可能会保存多次
        if($_POST['workAddStyle']=='zujuan'){//来自组卷 保留存档
            //构建数组
            $zujuanData = array();
            $zujuanData['data']      = $_POST['data'];
            $zujuanData['papername'] = $workName;
            $zujuanData['testlist']  = $_POST['testList'];
            //存档
            $workID = R('Home/Index/saveWork',array($zujuanData,true));//返回插入docsave表的主键id 来自组卷时$workID=$_POST['workID']为空 此处获得有效的$workID
        }
        //处理前台提交数组 前台名称做了统一
        $userName    = $this->getCookieUserName();
        $saveID      = $workID;//导学案ID
        $userList    = $_POST['userList'];//用户列表
        $startTime   = strtotime($_POST['startTime']);//开始时间
        $endTime     = strtotime($_POST['endTime']);//结束时间
        $assignStyle = $_POST['assignStyle'];//答题方式
        $assignOrder = $_POST['assignOrder']?$_POST['assignOrder']:0;//导学案的取消顺序 默认为0
        $description = formatString('changeStr2Html',$_POST['description']);//作业描述

        //验证数据
        if($endTime-$startTime<10*60) $this->setError('1X3209',1);//结束时间>=开始时间+10分钟
        if(time()-$endTime>0)         $this->setError('1X3210',1);//对结束时间是否过期进行验证
        if(empty($userList))          $this->setError('1X3057',1);//没有选择用户返回错误

        //处理班级用户数据
        $userListArray  = array();//存储班级id和用户id
        $classListArray = array();//存储班级id
        $tmpArr=array_filter(explode('|',$userList));
        $ii = 0;
        foreach($tmpArr as $i=>$iTmpArr){
            $tmpArr1                        = explode(';',$iTmpArr);
            $userListArray[$ii]['ClassID']  = $tmpArr1[0];
            if($tmpArr1[1]==0){//全选储存为0
                $userListArray[$ii]['UserID'] = 0;
            }else{
                $tmpArr2                      = array_unique(array_filter(explode(',',$tmpArr1[1])));
                $userListArray[$ii]['UserID'] = $tmpArr2;
            }
            $classListArray[] = $tmpArr1[0];
            $ii++;
        }

        //验证班级
        $buffer=D('ClassList')->selectData(
            'ClassID,ClassName,GradeID',
            'ClassID in ('.implode(',',$classListArray).')'
        );
        if(count($buffer)!=count($classListArray)) $this->setError('1X3058',1);//部分班级不存在

        //构建班级id名称对应数组
        $classNameArr = array();
        foreach($buffer as $i=>$iBuffer){
            $classNameArr[$iBuffer['ClassID']]=$iBuffer['ClassName'];
        }

        //传入参数
        $param=array(
            'saveID'      => $saveID,
            'userName'    => $userName,
            'workName'    =>  $workName,
            'startTime'   => $startTime,
            'endTime'     => $endTime,
            'assignStyle' => $assignStyle,
            'assignOrder' => $assignOrder,
            'description' => $description
        );

        switch($this->workStyle){
            case 'test':
                $result = $this->assignLeavedUserWorkByTest($param);
                break;
            case 'case':
                $result = $this->assignLeavedUserWorkByCase($param);
                break;
        }

        extract($result);
        //写入动态
        foreach($userListArray as $i=>$iUserListArray){
            if($iUserListArray['UserID']==0){//布置给全部学生
                $data            = array();
                $data['WorkID']  = $workID;
                $data['Status']  = 0;
                $data['ClassID'] = $iUserListArray['ClassID'];
                D('UserWorkClass')->insertData(
                    $data
                );
                $buffer=D('ClassUser')->selectData(
                    'UserID',
                    'ClassID='.$iUserListArray['ClassID']
                );
                $userArr=array();
                foreach($buffer as $j=>$jBuffer){
                    $userArr[]=$jBuffer['UserID'];
                }

                $this->classDynamic($iUserListArray['ClassID'],$userArr,'向<span class="c_id" cid="'.$iUserListArray['ClassID'].'">#'.$classNameArr[$iUserListArray['ClassID']].'</span>布置了'.$this->workTitle.'<span class="w_id" wid="'.$workID.'">#'
                    .$workName.'</span>。');
            }else{//教师选择了部分学生
                $data            = array();
                $data['WorkID']  = $workID;
                $data['Status']  = 1;
                $data['ClassID'] = $iUserListArray['ClassID'];
                $workToID=D('UserWorkClass')->insertData(
                    $data
                );

                $buffer=D('ClassUser')->selectData(
                    'UserID,SubjectID',
                    'ClassID='.$iUserListArray['ClassID'].' and Status=0'
                );
                $userArr=array();
                foreach($buffer as $j=>$jBuffer){
                    if($jBuffer['SubjectID']) $userArr[]=$jBuffer['UserID'];
                }

                foreach($iUserListArray['UserID'] as $j=>$jUserListArray){
                    $userArr[]        = $jUserListArray;
                    $data             = array();
                    $data['WorkToID'] = $workToID;
                    $data['UserID']   = $jUserListArray;
                    D('UserWorkUser')->insertData(
                        $data
                    );
                }
                $buffer = $this->getModel('User')->selectData(
                    'RealName,UserName,UserID',
                    'UserID in ('.implode(',',$iUserListArray['UserID']).')'
                );
                $tmpArr=array();
                foreach($buffer as $j=>$jBuffer){
                    $tmpArr[]='<span class="u_id" uid="'.$jBuffer['UserID'].'">#'.(empty($jBuffer['RealName']) ? $jBuffer['UserName'] : $jBuffer['RealName']).'</span>';

                }
                $this->classDynamic($jUserListArray['ClassID'],$userArr,'向'.implode(',',$tmpArr).'布置了'.$this->workTitle.'<span class="w_id" wid="'.$workID.'">#'.$workName.'</span>。');
            }
        }
        $this->setBack('success');
    }

    /**
     * 下载已留作业
     */
    public function workDown(){
        $workID       = $_POST['WorkID']; //作业id
        $isSaveRecord = $_POST['issaverecord']; //是否记录
        $docVersion   = $_POST['docversion']; //文档类型
        $paperSize    = $_POST['papersize']; //试卷版式
        $paperType    = $_POST['papertype']; //试卷类型
        $verifyCode   = $_POST['verifycode']; //验证码
        $key          = $_POST['key']; //密钥

        if(md5($verifyCode) != session('verify')){
            $this->setError('30101',1);
        }

        //判断是否有权限下载
        $userName=$this->getCookieUserName();
        if($key!=md5(C('DOC_HOST_KEY').$userName.date("Y.m.d",time()))){
            $this->setError('30803',1);
        }

        //获取试卷属性
        $buffer=D('UserWork')->selectData(
            'UserName,WorkName,SubjectID,CookieStr,TestList',
            'WorkID='.$workID
        );
        if(!$buffer) $this->setError('1X3066',1);
        $workName=$buffer[0]['WorkName']; //文档名称

        $param=array();
        $param['subjectID']=$buffer[0]['SubjectID']; //学科id
        $param['isSaveRecord']=$isSaveRecord; //是否存档
        $param['docVersion']=$docVersion; //文档类型
        $param['paperSize']=$paperSize; //纸张大小
        $param['paperType']=$paperType; //试卷类型
        $param['backType']=0; //是否仅返回路径
        $param['testList']=$buffer[0]['TestList']; //试题id字符串以英文逗号间隔
        $param['docName']=$buffer[0]['WorkName']; //文档名称
        $param['downStyle']=2; //区分下载作业还是试卷 答题卡4 导学案3 作业2，试卷1
        $userName=$this->getCookieUserName();
        switch($this->workStyle){
            case 'test':
                $param['cookieStr']=$buffer[0]['CookieStr']; //内容
                $path=$this->getApiDoc('Doc/getDownUrlByCookie',$param,$userName);
                break;
            case 'case':
                $param['cookieStr']=unserialize($buffer[0]['CookieStr']); //导学案内容
                $caseTpl=$this->getModel('CaseTpl');
                $path=$caseTpl->getDownUrlByCookie($param,$userName);
                break;
        }


        if($path[0]=='false'){
            $this->setError($path['msg'],1,'',$path['replace']);
        }

        //写入动态
        $this->classDynamic(0,array($this->getCookieUserID()),'下载了'+$this->workTitle+'<span class="w_id" wid="'.$workID.'">#'.$workName.'</span>。');
        $this->setBack('success#$#'.$path['msg']);
    }

    /**布置作业结束**/

     /**批改作业开始**/
    /**
     * 批改作业(获取已布置的作业列表)
     * @author demo
     */
    public function checkWork() {
        $userName  = $this->getCookieUserName();
        $key       = md5(C('DOC_HOST_KEY').$userName.date("Y.m.d",time()));//下载使用
        $classID   = 0;

        if($_GET['cid'] && is_numeric($_GET['cid'])){//作业跳转指定班级
            $classID=$_GET['cid'];
        }

        $tpl='';
        if($this->workStyle=='case'){
            $tpl='Work/checkCaseWork';
        }
        $this->assign('classID', $classID);
        $this->assign('key', $key); //密钥
        $this->assign('pageName','检查'.$this->workTitle);
        $this->display($tpl);
    }

    /**
     * 获取已布置作业/导学案列表
     * @author demo
     */
    public function getAssignedWork(){
        $classID   = $_POST['ClassID'];//班级id
        $subjectID = cookie('SubjectId');//学科id
        $page      = max($_POST['p'],1);//分页
        $pageSize  = 10;

        if(!is_numeric($classID)) $this->setError('30734',1);//班级不存在

        //班级验证
        $buffer=D('ClassList')->selectData(
            'ClassName,Creator,GradeID,StudentCount',
            'ClassID='.$classID
        );

        if(!$buffer) $this->setError('30734',1);

        $studentCount=$buffer[0]['StudentCount'];//该班级的总学生数
        //获取作业总数
        $totalCount = D('Base')->unionSelect('userWorkClassCount',$classID,$subjectID,$this->workType);

        if($totalCount==0){
            $this->setBack(array('error','本班还没有布置过'.$this->workTitle.'呢'));
        }
        //获取作业数据
        $buffer = D('Base')->unionSelect('userWorkClassSelectPage',$classID,$subjectID,$page.','.$pageSize,
            $this->workType);
        $output = array();
        //@todo 此处待优化
        //G('forBegin');
        //获取班级作业相关数据
        $userSendWork  = $this->getModel('UserSendWork');
        foreach($buffer as $i=>$iBuffer){
            //过期
            if($iBuffer['EndTime']<time()){
                $buffer[$i]['outDate']=1;//过期
            }else{
                $buffer[$i]['outDate']=0;//未过期
            }

            //是否开始
            if($iBuffer['StartTime']<time()){
                $buffer[$i]['ifStart']=1;//已开始
            }else{
                $buffer[$i]['ifStart']=0;//开始开始
            }

            //作业格式化
            $buffer[$i]['StartTime'] = date('y/m/d H:i',$buffer[$i]['StartTime']);
            $buffer[$i]['EndTime']   = date('y/m/d H:i',$buffer[$i]['EndTime']);

            //获取此次作业总人数
            if($buffer[$i]['Status']=='0'){//布置给全班
                $buffer[$i]['StudentCount']=$studentCount;
            }else{//布置给部分学生
                $buffer[$i]['StudentCount']=D('UserWorkUser')->selectCount(
                    'WorkToID='.$iBuffer['WorkToID'],
                    'WorkToID'
                );
            }

            //获取已经提交人数 增加status条件
            $buffer[$i]['SendNum']=$userSendWork->selectCount(
                'WorkID='.$iBuffer['WorkID'].' and ClassID='.$classID .' and Status>0',
                'SendID'
            );

            //获取已经批改人数
            $buffer[$i]['CorrectNum']=$userSendWork->selectCount(
                'WorkID='.$iBuffer['WorkID'].' and ClassID='.$classID.' and Status=2',
                'SendID'
            );
        }
        //G('forEnd');
        //echo G('forBegin','forEnd',8);exit;
        $output[0] = $buffer;
        $output[1] = $totalCount;
        $output[2] = $pageSize;
        $this->setBack(array('success',$output));
    }

    /**
     *  删除已布置的作业/导学案
     *  @notice 前台设计已经没有删除功能,该方法暂不删除(详见最新版设计)/返回的错误代码采用了作业的错误代码,未定义导学案错误代码
     *  @author demo
     */
    public function delAssignedWork(){
        //验证权限
        $userName = $this->getCookieUserName();
        $workID   = $_POST['WorkID'];
        if(!is_numeric($workID))$this->setError('30301',1);//数据标识错误

        $buffer=D('UserWork')->selectData(
            '*',
            'WorkID='.$workID
        );
        if(!$buffer) $this->setError('1X3002',1);//没有找到作业
        if($buffer[0]['UserName']!=$userName) $this->setError('1X3004',1);//不能删除别人的作业存档
        if($buffer[0]['IfDelete']==1) $this->setError('1X3061',1);//当前作业已经不能删除！
        $workName=$buffer[0]['WorkName'];

        //删除作业
        $buffer=D('UserWorkClass')->selectData(
            '*',
            'WorkID='.$workID
        );
        $classUser = D('ClassUser');
        $userWorkUser = D('UserWorkUser');
        $userWorkClass = D('UserWorkClass');
        foreach($buffer as $i=>$iBuffer){
            if($iBuffer['Status']==1){
                $tmpArr=$classUser->selectData(
                    'SubjectID,UserID',
                    'ClassID='.$iBuffer['ClassID']
                );
                $userArr=array();
                foreach($tmpArr as $j=>$jTmpArr){
                    if($jTmpArr['SubjectID']) $userArr[]=$jTmpArr['UserID'];
                }
                $tmpArr=$userWorkUser->selectData(
                    'UserID',
                    'WorkToID='.$iBuffer['WorkToID']
                );
                foreach($tmpArr as $j=>$jTmpArr){
                    $userArr[]=$jTmpArr['UserID'];
                }
                if($userWorkUser->deleteData(
                        'WorkToID='.$iBuffer['WorkToID'])===false){
                    $this->setError('1X3062',1);//删除作业用户关系出错
                }else{
                    $this->classDynamic($iBuffer['ClassID'],$userArr,'删除了'.$this->workTitle.'<span class="w_id" wid="'.$workID.'">#'.$workName.'</span>。');
                    if($userWorkClass->deleteData(
                            'WorkToID='.$iBuffer['WorkToID'])===false){
                        $this->setError('1X3063',1);//删除作业班级关系出错
                    }
                }
            }else{
                $tmpArr=$classUser->selectData(
                    'UserID',
                    'ClassID='.$iBuffer['ClassID']
                );
                $userArr=array();
                foreach($tmpArr as $j=>$jTmpArr){
                    $userArr[]=$jTmpArr['UserID'];
                }
                if($userWorkClass->deleteData(
                        'WorkToID='.$iBuffer['WorkToID'])===false){
                    $this->setError('1X3064',1);//删除作业关系出错
                }else{
                    $this->classDynamic($iBuffer['ClassID'],$userArr,'删除了'.$this->workTitle.'<span class="w_id" wid="'.$workID.'">#'.$workName.'</span>。');
                }
            }
        }
        if(D('UserWork')->deleteData(
                'WorkID='.$workID)===false){
            $this->setError('1X3005',1);//删除作业失败
        }
        $this->setBack('success');
    }

    /**
     * 批改作业详细情况
     * @author demo
     */
    public function checkWorkDetail() {
        $workID  = $_GET['id'];
        $classID = $_GET['cid'];

        $tpl='';
        if($this->workStyle=='case'){
            $tpl='Work/checkCaseWorkDetail';
        }
        
        $this->assign('WorkID', $workID); //作业id
        $this->assign('ClassID', $classID); //班级id
        $this->assign('pageName','检查'.$this->workTitle);
        $this->display($tpl);
    }

    /**
     * 获取作业答题情况
     * @author demo
     */
    public function getWorkInfo(){
        //验证作业/权限
        $userName = $this->getCookieUserName();
        $workID   = $_POST['WorkID'];
        $classID  = $_POST['ClassID'];

        $userWorkArr=D('UserWork')->selectData(
            '*',
            'WorkID='.$workID
        );
        if(!$userWorkArr) $this->setError('1X3066',1); //作业不存在
        if($userWorkArr[0]['UserName']!=$userName) $this->setError('1X3065',1); //您不能查看别人的作业
        $buffer = D('UserWorkClass')->selectData(
            '*',
            'WorkID='.$workID.' and ClassID='.$classID
        );
        if(!$buffer){
            $this->setError('1X3066',1); //作业不存在
        }

        //查询作业对应用户
        if($buffer[0]['Status']==1){ //对个人布置作业
            $where='uwc.WorkID='.$workID.' and uwc.ClassID='.$classID.' and cu.Status=0';
            $workBuffer = D('Base')->unionSelect('userWorkSelectByWhere',$where);
        }else{ //对班级布置作业
            $workBuffer= D('Base')->unionSelect('userWorkSelectByWorkIdClassID',$workID,$classID);
        }
        if(!$workBuffer){
            $this->setError('30214',1); //用户不存在
        }

        //验证是否包含试题
        if(!$this->ifContainTest($userWorkArr[0]['TestList'])){
            $this->setError('1X3067',1);//载入试题失败
        }

        //提交作业记录 aat的提交机制:点击做作业就插入send_work status 0 提交后改变状态 1
        $userSendWork=D('UserSendWork')->selectData(
            '*',
            'WorkID='.$workID.' and ClassID='.$classID.' and Status>0'
        );

        //对提交作业的处理
        $userWorkContent=array();//用户作业内容
        if($userSendWork){//有用户提交作业

            $sendIDList=array();//提交作业的id
            $userIDList=array();//sendid和userid对应数组
            foreach($userSendWork as $i=>$iUserSendWork){
                $sendIDList[]=$iUserSendWork['SendID'];
                $userIDList[$iUserSendWork['UserID']]=$iUserSendWork['SendID'];
            }

            //获取user_send_work_content表中已存在的数据
            $result=$this->getUserworkBySendID($sendIDList,$userIDList);

            $contentSendIDList = $result['sendIDList']; //已同步的sendid
            $userWorkContent   = $result['content'];//已同步的数据集合

            //获得未同步的sendid
            $unContentID=$sendIDList;
            if($contentSendIDList){
                foreach($contentSendIDList as $i=>$iContentSendIDList){
                    $tmpKey=array_search($iContentSendIDList,$unContentID);
                    if($tmpKey!==false){
                        unset($unContentID[$tmpKey]);
                    }
                }
            }

            //新提交数据写入表user_send_work_content
            if($unContentID){
                $testList=$userWorkArr[0]['TestList'];//试题id

                //索引获取本次作业试题数据[包括复合题数据]
                $result=$this->getSplitDataByTestID($testList);

                //以原试题id顺序获取格式化数据
                $newResult=array();
                $testArray=R('Common/TestLayer/cutIDStrByChar',array($testList,2));
                foreach($testArray as $i=>$iTestArray){
                    if(array_key_exists($iTestArray,$result)){
                        $newResult[]=$result[$iTestArray];
                    }else{//如果索引中未找到该题,前台不显示该题,记录日志
                        $error = array();
                        $error['description'] = '获取试题索引失败';
                        $error['msg'] = '题号为:' . $iTestArray . '的试题获取索引时失败';
                        D('Base')->unionSelect('addErrorLog',$error);
                    }

                }

                //对作答内容和试题数据合并
                $output=$this->formatAnswerRecord($newResult,$unContentID,$workID);

                //新同步的数据并入$userWorkContent 以userid为索引
                foreach($output as $i=>$iOutput){
                    $tmpKey=array_search($i,$userIDList);
                    if($tmpKey!==false){
                        $userWorkContent[$tmpKey]=$iOutput;
                        unset($userIDList[$tmpKey]);
                    }
                }
            }
            //格式化需从send_work表中附加的数据 userid为索引
            $appendData=$this->appdendWorkData($userSendWork);
            unset($userSendWork);
        }
        $workBuffer=$this->formatReturnInfo($workBuffer,$userWorkContent,$userWorkArr,$appendData);

        //处理返回数据
        if($workBuffer){
            $this->setBack(array('success',$workBuffer[0],$workBuffer[1],$workBuffer[2]));
        }else{
            $this->setError('1X3312',1);
        }
    }

    /**
     * 提交评语
     */
    public function setComment(){
        $sendID=$_POST['sid'];
        $userID=$_POST['uid'];
        $content=formatString('changeStr2Html',$_POST['content']);
        $workID=$_POST['workID'];
        $classID=$_POST['classID'];
        $userName=$this->getCookieUserName();

        $buffer=D('UserWork')->selectData(
            'UserName,WorkName',
            'WorkID='.$workID
        );
        if($buffer[0]['UserName']!=$userName) $this->setError('1X3065',1);
        $workName=$buffer[0]['WorkName'];

        $buffer=D('UserWorkClass')->selectData(
            '*',
            'WorkID='.$workID.' and ClassID='.$classID
        );
        if(!$buffer) $this->setError('1X3066',1);
        $userArr=array();
        if(!empty($sendID)){
            //针对个人
            $buffer=D('UserSendWork')->selectData(
                '*',
                'SendID='.$sendID
            );
            if($buffer[0]['WorkID']!=$workID) $this->setError('1X3071',1);
            if($buffer[0]['ClassID']!=$classID) $this->setError('1X3072',1);
            $userArr[]=$buffer[0]['UserID'];

            if(D('UserSendWork')->updateData(
                    array('Comment'=>$content,'Status'=>2,'CheckTime'=>time()),
                    'SendID='.$sendID)===false){
                $this->setError('1X3073',1);
            }
        }else if(!empty($userID)){
            //针对多人
            $buffer=D('UserSendWork')->selectData(
                '*',
                'WorkID='.$workID.' and ClassID='.$classID.' and UserID in ('.$userID.')'
            );
            if(!$buffer) $this->setError('1X3074',1);

            $sendList=array();
            foreach($buffer as $i=>$iBuffer){
                if($iBuffer['Status']==1){
                    $sendList[]=$iBuffer['SendID'];
                    $userArr[]=$iBuffer['UserID'];
                }
            }
            if(D('UserSendWork')->updateData(
                    array('Comment'=>$content,'Status'=>2,'CheckTime'=>time()),
                    'SendID in ('.implode(',',$sendList).')')===false){
                $this->setError('1X3073',1);
            }
        }
        $tmpStr=array();
        $buffer = $this->getModel('User')->selectData(
            'RealName,UserName,UserID',
            'UserID in ('.implode(',',$userArr).')'
        );
        foreach($buffer as $i=>$iBuffer){
            $uName=empty($iBuffer['RealName']) ? $iBuffer['UserName'] : $iBuffer['RealName'];
            $tmpStr[]='<span class="u_id" uid="'.$iBuffer['UserID'].'">@'.$uName.'</span>';
        }
        $buffer=D('ClassUser')->selectData(
            'UserID,SubjectID',
            'ClassID='.$classID
        );
        $userArr=array();
        foreach($buffer as $i=>$iBuffer){
            if($iBuffer['SubjectID']) $userArr[]=$iBuffer['UserID'];
        }
        $this->classDynamic($classID,$userArr,'给'.implode(',',$tmpStr).'的作业<span class="w_id" wid="'.$workID.'">#'.$workName.'</span>填写了评语。');
        $this->setBack('success');
    }

    /**
     * 获取试题批改信息[统一单题批阅和快速批阅]
     * @author demo
     */
    public function getTestCorrect(){
        //前台提交数据
        $username = $this->getCookieUserName();
        $workID   = $_POST['wid'];  //作业id
        $classID  = $_POST['cid']; //班级id
        $sendID   = $_POST['sid']; //提交作业id
        $testID   = $_POST['tid']; //该题试题id
        $ifTest   = $_POST['ifTest'];//是否获取信息

        //验证权限
        $userWorkArr = D('UserWork')->selectData(
            '*',
            'WorkID=' . $workID
        );
        if (!$userWorkArr) $this->setError('1X3066', 1); //作业不存在
        if ($userWorkArr[0]['UserName'] != $username) $this->setError('1X3065', 1); //您不能查看别人的作业

        $buffer = D('UserWorkClass')->selectData(
            '*',
            'WorkID=' . $workID . ' and ClassID=' . $classID
        );
        if (!$buffer) {
            $this->setError('1X3066', 1); //作业不存在
        }
        //试题id列表
        $testList = $userWorkArr[0]['TestList'];
        //$testArr  = R('Common/TestLayer/cutIDStrByChar',array($testList,2));
        //是否获取试题数据
        $testResult = array();
        if($ifTest==1){
            $testResult = $this->getTestLoreData($testList);
            $testResult = $this->buildPaperStruct($userWorkArr[0]['CookieStr'],$testResult);
            $testResult = $testResult['testResult'];
            if(!$testResult) {
                $this->setError('1X3067', 1); //载入试题失败
            }
        }

        //获取试题答案
        $userAnswer = $this->getSingleTestAnswer($sendID,$testID);

        $this->setBack(array('success',$userAnswer,$testResult));
    }

    /**
     * 批阅试题入库 多题或者单题
     * @author demo
     */
    public function correctTest(){
        $username = $this->getCookieUserName();
        $workID   = $_POST['wid']; //作业id
        $sendID   = $_POST['sid']; //提交作业id
        $testID   = $_POST['tid']; //该题试题id
        //数组
        $judgeArr  = $_POST['jid']; //复合题id
        $scoreArr    = $_POST['score'];
        $contentArr  = $_POST['content'];

        if(!is_numeric($workID) || !is_numeric($sendID)){
             $this->setError('30301',1); //数据标识错误
        }
        if(!$contentArr){
            $this->setError('30301',1); //数据标识错误
        }
        //验证合法性
        $userWorkArr=D('UserWork')->selectData(
            '*',
            'WorkID='.$workID
        );
        if(!$userWorkArr) $this->setError('1X3066',1); //作业不存在
        if($userWorkArr[0]['UserName']!=$username) $this->setError('1X3103',1); //您不能查看别人的作业

        foreach($judgeArr as $i=>$iJudge){
              if(D('UserSendWorkContent')->updateData(
                ['CorrectContent'=>$contentArr[$i],'IfCorrect'=>1,'Star'=>$scoreArr[$i]],
                'SendID='.$sendID.' and TestID="'.$testID.'" and JudgeID='.$judgeArr[$i]
              )!==false){
                  D('Base')->unionSelect('conAddData', 'UserSendWork',
                      'CorrectNum=CorrectNum+1',
                      'SendID='.$sendID);
              }else{
                  $this->setError('1X3104',1); //数据标识错误
              }
        }
        $this->setBack(array('success'));
    }

    /**
     * 获取试题选项统计
     * @author demo
     */
    public function getTestCheck(){

        //权限检测
        $workID   = $_POST['WorkID'];
        $classID  = $_POST['ClassID'];
        $userName = $this->getCookieUserName();
        if(!is_numeric($workID) || !is_numeric($classID)){
            $this->setError('30301'); //数据标识错误！
        }
        $buffer=D('UserWork')->selectData(
            'UserName,TestList',
            'WorkID='.$workID
        );
        if($buffer[0]['UserName']!=$userName) $this->setError('1X3065',1); //您不能查看别人的作业！
        $testList = $buffer[0]['TestList'];//试题列表

        $bufferWork=D('UserSendWork')->selectData(
            'SendID',
            'WorkID='.$workID.' and ClassID='.$classID.' and Status>0'
        );
        if(!$bufferWork){
            $this->setBack('暂时没有提交'.$this->workTitle);
        }


        //获取用户答案
        foreach($bufferWork as $i=>$iBufferWork){
            $sendIDList[]=$iBufferWork['SendID'];
        }
        $buffer    = $this->getUserworkBySendID($sendIDList,false);
        $buffer    = $buffer['content'];
        $sendNum   = count($buffer);

        $checkInfo = array();
        $systemAnswer = $this->getSplitDataByTestID($testList);//获取试题答案

        $ifFirst=true;
        foreach($buffer as $i=>$iBuffer) {
            foreach ($iBuffer as $j => $jBuffer) {
                if($jBuffer['SystemAnswer']=='0'){
                    $checkInfo[$j]['ifChoose']=0;//非选择
                    if($jBuffer['IfCorrect']==1 && $jBuffer['Star']==10){
                        $checkInfo[$j]['rightNum'] += 1;//正确数
                    }else{
                        $checkInfo[$j]['rightNum'] += 0;
                    }
                }else{
                    if($ifFirst){//初始化选项
                        $checkInfo[$j]['A']=0;
                        $checkInfo[$j]['B']=0;
                        $checkInfo[$j]['C']=0;
                        $checkInfo[$j]['D']=0;
                        $checkInfo[$j]['E']=0;
                    }
                    $checkInfo[$j]['ifChoose']=1;//选择

                    if($jBuffer['UserAnswer']=='A'){
                        $checkInfo[$j]['A']+=1;
                    }else if($jBuffer['UserAnswer']=='B'){
                        $checkInfo[$j]['B']+=1;
                    }else if($jBuffer['UserAnswer']=='C') {
                        $checkInfo[$j]['C']+=1;
                    }else if($jBuffer['UserAnswer']=='D'){
                        $checkInfo[$j]['D']+=1;
                    }else{//其他或者
                        $checkInfo[$j]['E']+=1;
                    }

                    $checkInfo[$j]['right'] = R('Common/TestLayer/delMoreTag',array($systemAnswer[$jBuffer['TestID']]['answersplit'][$jBuffer['JudgeID'] == 0 ? 0 : $jBuffer['JudgeID']-1],2));

                    //处理开始
                    if($jBuffer['IfRight']==2) {
                        $checkInfo[$j]['rightNum'] += 1;//正确数
                    }else{
                        $checkInfo[$j]['rightNum'] += 0;
                    }
                }
                $checkInfo[$j]['testID']  = $jBuffer['TestID'];//试题ID
                $checkInfo[$j]['order']   = $j;//前台按题号排序使用
                $checkInfo[$j]['judgeID'] = $jBuffer['JudgeID'];//前台按题号排序使用
            }
            if($ifFirst){
                $ifFirst=false;
            }
        }
        //返回统计信息 已提交人数
        if($checkInfo){
            $this->setBack(array('success',$checkInfo,$sendNum));
        }else{
            $this->setError('30805',1);
        }
    }

    /**
     * 获取试题选项统计具体详情
     * @author demo
     */
    public function getTestCheckDetail(){
        $workID   = $_POST['workID'];
        $userName = $this->getCookieUserName();
        $testID   = $_POST['tid'];//试题id
        $judgeID  = $_POST['jid'];//复合题id

        if(!is_numeric($workID)  || !is_numeric($judgeID)){
            $this->setError('30301',1);
        }
        $buffer=D('UserWork')->selectData(
            'UserName,TestList',
            'WorkID='.$workID
        );
        if($buffer[0]['UserName']!=$userName) $this->setError('1X3065',1);

        $testBuffer=array();
        //获取题目的数据
        if($_POST['ifTest']==1){
            $testBuffer = $this->getSplitDataByTestID($buffer[0]['TestList']);
            if(empty($testBuffer)){
                $this->setError('1X3067',1);
            }
        }

        $buffer=D('UserSendWorkContent')->selectData(
            'IfRight,UserAnswer,SystemAnswer,JudgeID',
            'WorkID='.$workID.' and TestID="'.$testID.'" and JudgeID='.$judgeID
        );
        $answerStatus = array();
        $answerStatus['A']=0;
        $answerStatus['B']=0;
        $answerStatus['C']=0;
        $answerStatus['D']=0;
        $answerStatus['E']=0;
        $answerStatus['ifchoose']=0;
        $answerStatus['correct']='';
        $choose=array('A','B','C','D','E','F','G','H','I');
        foreach($buffer as $i=>$iBuffer){
               if(in_array(trim($iBuffer['SystemAnswer']),$choose)){
                   $answerStatus['ifchoose']=1;
                   if($iBuffer['UserAnswer']=='A'){
                       $answerStatus['A']+=1;
                   }elseif($iBuffer['UserAnswer']=='B'){
                       $answerStatus['B']+=1;
                   }elseif($iBuffer['UserAnswer']=='C'){
                       $answerStatus['C']+=1;
                   }elseif($iBuffer['UserAnswer']=='D'){
                       $answerStatus['D']+=1;
                   }else{
                       $answerStatus['E']+=1;
                   }
                   if($iBuffer['SystemAnswer']=='A'){
                       $answerStatus['correct']=='A';
                   }elseif($iBuffer['SystemAnswer']=='B'){
                       $answerStatus['correct']=='B';
                   }elseif($iBuffer['SystemAnswer']=='C'){
                       $answerStatus['correct']=='C';
                   }elseif($iBuffer['SystemAnswer']=='D'){
                       $answerStatus['correct']=='D';
                   }else{
                       $answerStatus['correct']=='E';
                   }
               }
        }
        $this->setBack(array('success',$answerStatus,$testBuffer));
    }

    /**
     * 获取作业试题内容
     * @author demo
     */
    public function getTestContent(){
        $userName = $this->getCookieUserName();
        $workID   = $_POST['workID'];
        $ifTest   = $_POST['ifTest'];
        if(!is_numeric($workID)){
            $this->setError('30301');
        }
        $userWorkArr=D('UserWork')->selectData(
            '*',
            'WorkID='.$workID
        );
        if(!$userWorkArr) $this->setError('1X3066',1);
        if($userWorkArr[0]['UserName']!=$userName) $this->setError('1X3065',1);
        //获取试题内容
        $testBuffer = '';
        if($ifTest==1){//获取试题
            $testBuffer = $this->getTestLoreData($userWorkArr[0]['TestList']);
            if(!$testBuffer){
                $this->setError('1X3067',1);//载入试题失败
            }
        }

        //获取试题结构
        $result = $this->buildPaperStruct($userWorkArr[0]['CookieStr'],$testBuffer);

        if($result){
            $this->setBack(array($result['struct'],$result['testResult']));
        }else{
            $this->setError('1X3075',1);//载入内容失败
        }
    }

    /**
     * 获取用户答案
     * @author demo
     */
    public function getUserAnswer(){
        //权限检测
        $sendID = $_POST['sid'];
        $workID = $_POST['workID'];//用户id
        $userName = $this->getCookieUserName();
        if(!is_numeric($sendID) || !is_numeric($workID)){
            $this->setError('30301',1);
        }
        $userWorkArr=D('UserWork')->selectData(
            '*',
            'WorkID='.$workID
        );
        if(!$userWorkArr) $this->setError('1X3066',1);
        if($userWorkArr[0]['UserName']!=$userName) $this->setError('1X3065',1);

        //获取用户答案
        $userAnswer = $this->getSingleUserWorkContent($sendID);

        if($userAnswer){
            $this->setBack(array('success',$userAnswer));
        }else{
            $this->setError('1X3075',1);
        }
    }

    /**批改作业数据处理**/

    /**
     * 附加作业数据格式化 构成以用户id为索引的数据
     * @param array $workInfo user_send_work表中取出的数据
     * @return array
     * @author demo
     */
    private function  appdendWorkData($workInfo){
        if(!is_array($workInfo)){
            return '';
        }
        $result=array();
        foreach($workInfo as $i=>$iWorkInfo){
            $result[$iWorkInfo['UserID']]=$workInfo[$i];
        }
        return $result;
    }

    /**
     * 获取多用户的答案数据
     * @param array | int $sendid
     * @param array $userid | bool  userid为false时构成成以sendid为索引的数据类型
     * @return array 返回已经同步的sendid和以用户id或者sendid为索引的作业数据
     * @author demo
     */
    private function getUserworkBySendID($sendid,$userid){
        $content=D('UserSendWorkContent')->selectData(
            '*',
            'SendID in ('.join(',',$sendid).')',
            'SendID ASC,ContentID ASC,OrderID ASC'
        );
        $return     = array();
        $sendIDList = array();
        if($content){
            $init=0;
            $ii=1;
            foreach($content as $i=>$iContent){
                if($init!=$iContent['SendID']){
                    $init=$iContent['SendID'];
                    $ii=1;
                }elseif($init==$iContent['SendID']){
                    $ii++;
                }
                if($userid) {
                    $tmpKey = array_search($iContent['SendID'], $userid);
                    if ($tmpKey !== false) {
                        $return[$tmpKey][$ii] = $iContent;
                    }
                }else{
                    $return[$iContent['SendID']][$ii]=$iContent;
                }
                if(!in_array($iContent['SendID'],$sendIDList)){
                    $sendIDList[]=$iContent['SendID'];
                }
            }
        }
        return array('content'=>$return,'sendIDList'=>$sendIDList);
    }

    /**
     * 获取单用户的作业内容
     * @param  int $sendID 用户提交作业的sendid
     * @return array
     * @author demo
     */
     private function getSingleUserWorkContent($sendID){
         $content=D('UserSendWorkContent')->selectData(
             '*',
             'SendID='.$sendID,
             'ContentID ASC,OrderID ASC'
         );
         $newContent = array();
         if($content){
             foreach($content as $i=>$iContent){
                  if($iContent['JudgeID']!=0){//统一格式
                      $iContent['JudgeID']+=-1;
                  }
                  $iContent['UserAnswer'] = stripslashes_deep(formatString('IPReturn',$iContent['UserAnswer']));
                  $newContent[$iContent['TestID']][$iContent['JudgeID']]=$iContent;

             }

         }
         return $newContent;
     }

    /**
     * 获取某道试题(单题或者复合题)的答案
     * @param int $sendID 用户提交作业的sendid
     * @param int $testID 试题id
     * @notice testID有可能是字符串 如:校本题库
     * @return array
     * @author demo
     */
    private function getSingleTestAnswer($sendID,$testID){
        $userAnswer = D('UserSendWorkContent')->selectData(
            'JudgeID,UserAnswer,IfCorrect,CorrectContent,Star,SystemAnswer',
            'SendID='.$sendID.' and TestID="'.$testID.'"',
            'JudgeID ASC'
        );
        if(!$userAnswer) $this->setError('1X3067',1); //载入试题失败(@todo错误信息待修改)

        foreach($userAnswer as $i=>$iUserAnswer){//格式化返回数据[单题也按照复合题的格式返回]
            $userAnswer[$i]['IfRight']=0;
            if($iUserAnswer['SystemAnswer']=='0'){//非选择 规则:10=正确 0=错误 0-10=半对
                $userAnswer[$i]['IfChoose']=0;
                if($iUserAnswer['IfCorrect']==1){
                    if($iUserAnswer['Star']==10){
                        $userAnswer[$i]['IfRight']=1;
                    }
                }
            }else{//选择
                $userAnswer[$i]['IfChoose']=1;
                if($iUserAnswer['UserAnswer']==$iUserAnswer['SystemAnswer']){
                    $userAnswer[$i]['IfRight']=1;
                }
            }
            unset($userAnswer[$i]['SystemAnswer']);
            $userAnswer[$i]['UserAnswer']=stripslashes_deep(formatString('IPReturn',$iUserAnswer['UserAnswer']));
        }
        return $userAnswer;
    }

    /**
     * 检测作业中是否包含试题
     * @param string $testList 试题id字符串
     * @return bool
     * @author demo
     */
    private function ifContainTest($testList){
        $testArr = R('Common/TestLayer/cutIDStrByChar',array($testList,1));
        if(empty($testArr[0]) && empty($testArr['c'])){
            return false;
        }
        return true;
    }

    /**
     * 计算试题数目(包括复合题)
     * @param array $testList 试题id数组
     * @return int
     * @author demo
     */
    private  function getTestCount($testList){
        $testList      = R('Common/TestLayer/cutIDStrByChar',array($testList,2));
        $testReal      = $this->getModel('TestReal');
        $judgeTestList = $testReal->getJudgeByIDs($testList);
        $time=0;

        foreach($testList as $i=>$iTestList){
            if($judgeTestList && array_key_exists($iTestList, $judgeTestList)){
                foreach ($judgeTestList[$iTestList] as $j => $jJudge) {
                    $time++;
                }
            }else{
                $time++;
            }
        }
        return $time;
    }

    /**
     * 获取以试题id或者知识id为索引的试题和知识数组
     * @param string $testList 试题id字符串
     * @return array
     */
    private function getTestLoreData($testList){
        //获取试题数据
        $testResult=$this->getSplitDataByTestID($testList);
        //导学案加入知识
        if($this->workStyle=='case'){
            $loreResult=$this->getDataByLoreID($testList);
            foreach($loreResult as $i=>$iLore){
                $testResult[$i]=$iLore;
            }
        }

        return $testResult;
    }

    /**
     * 获取分割试题数据
     * @notice 试题有小题会分割,答案会分割,解析会分割
     * @param string $testList 试题id
     * @return array 返回以试题id为索引的试题信息
     * @author demo
     */
    private function getSplitDataByTestID($testList){
        //构建索引查询条件
        $where = array('TestID'=>$testList);
        //是否获取judge待优化
        $field = array('testid','answersplit','ifchoose','testnum','judge','analyticsplit','testsplit');
        $order = '';
        $page  = array('perpage'=>100);

        $testRealQuery=$this->getModel('TestRealQuery');
        $result = $testRealQuery->getIndexTest($field,$where,$order,$page,0,2);
        return $result;
    }

    /**
     * 获取知识数据
     * @param string $testList 试题id包含知识id
     * @return array
     * @author demo
     */
    private function getDataByLoreID($testList){
        $testIDArr = R('Common/TestLayer/cutIDStrByChar',array($testList,1)); //切割字母开头的字符串id为数组;
        $loreID    = array_merge((array)$testIDArr['l'],(array)$testIDArr['u']);
        $result    = array();
        if($loreID){
            //构建索引查询条件
            $where = array('LoreID'=>implode(',',$loreID));
            $page  = array('perpage'=>100);
            $loreField = array('LoreID','Lore','Answer');

            $caseLoreQuery = $this->getModel('CaseLoreQuery');
            $result        = $caseLoreQuery->getLore($loreField,$where,'',$page,0,2);
        }
        //返回格式处理
        return $result;
    }

    /**
     * 向zj_user_send_work_content插入数据
     * @param array $result 试题索引数据
     * @param array $unContentID 未同步的sendID
     * @param int $workID 布置的作业id对应zj_user_work表
     * @return array
     * @author demo
     */
    private function formatAnswerRecord($result,$unContentID,$workID){
        //获取用户作答内容
        $answerBuffer= $this->getModel('UserAnswerRecord')->selectData(
            'SendID,TestID,Number,AnswerText,IfChoose,IfRight,OrderID',
            'SendID in ('.implode(',',$unContentID).')',
            'SendID ASC,Number ASC,OrderID ASC'
        );
        $unContentArr    = array();
        $newAnswerBuffer = array();

        //极端情况容错
        foreach($unContentID as $i=>$iUnContentID){
            $unContentArr[$iUnContentID]=[];
        }
        foreach($answerBuffer as $i=>$iAnswerBuffer){
            $newAnswerBuffer[$iAnswerBuffer['SendID']][$iAnswerBuffer['TestID'].'-'.$iAnswerBuffer['OrderID']]=$iAnswerBuffer;
        }
        $newAnswerBuffer = $newAnswerBuffer+$unContentArr;

        $output=array(); //输出以SendID为键的数组

        $data=array();
        foreach($newAnswerBuffer as $i=>$iNewAnswerBuffer) {
            $ii=1;
            foreach($result as $j=>$jResult){

                $tmpArr=array();

                //复合题处理
                if($jResult['testnum']>1){
                    for($k=0;$k<$jResult['testnum'];$k++){//复合题容错处理
                        if(!$jResult['judge']){
                            $error = array();
                            $error['description'] = '获取试题的复合题相关信息失败';
                            $error['msg'] = '题号为:' . $jResult['testid'] . '的试题获取复合题相关信息失败';
                            D('Base')->unionSelect('addErrorLog',$error);
                        }
                        $kResult = $jResult['judge'][$k];//此处如果复合题获取失败,则插入相关内容按空处理
                        $tmpArr = $iNewAnswerBuffer[$jResult['testid'] . '-' . $kResult['OrderID']];
                        $answer = $kResult['IfChoose'] == 3 ? R('Common/TestLayer/delMoreTag',array($jResult['answersplit'][$kResult['OrderID'] - 1],2)):0;

                        $star = 0; //星星值
                        if ($tmpArr['IfRight'] == 2) $star = 10;

                        if (empty($tmpArr)) {
                            $tmpArr['AnswerText'] = '';
                            $tmpArr['Number'] = $j;
                            $tmpArr['SendID'] = $i;
                            $tmpArr['IfRight'] = $kResult['IfChoose'] == 0 ? -1 : 1;
                            $tmpArr['TestID'] = $jResult['testid'];
                            $tmpArr['OrderID'] = $k+1;
                        }

                        $tmpDataArray = array(
                            'UserAnswer' => $tmpArr['AnswerText'],
                            'SystemAnswer' => $answer,
                            'CorrectContent' => '',
                            'OrderID' => $j + 1,
                            'IfCorrect' => 0,
                            'SendID' => $i,
                            'IfRight' => $tmpArr['IfRight'],
                            'Star' => $star,
                            'WorkID' => $workID,
                            'TestID' => $tmpArr['TestID'],
                            'JudgeID' => $tmpArr['OrderID']
                        );
                        $output[$i][$ii] = $tmpDataArray;
                        $data[] = $tmpDataArray;
                        $ii++;
                    }
                }else{
                    $tmpArr=$iNewAnswerBuffer[$jResult['testid'].'-0'];
                    $answer=$jResult['ifchoose']==3 ? R('Common/TestLayer/delMoreTag',array($jResult['answersplit'][0],2)):0;

                    $star=0; //星星值
                    if($tmpArr['IfRight']==2) $star=10;

                    if(empty($tmpArr)){
                        $tmpArr['AnswerText']='';
                        $tmpArr['Number']=$j;
                        $tmpArr['SendID']=$i;
                        $tmpArr['IfRight']=$jResult['IfChoose']==0 ? -1 : 1;
                        $tmpArr['TestID']=$jResult['testid'];
                        $tmpArr['OrderID']=0;
                    }

                    $tmpDataArray=array(
                        'UserAnswer'=>$tmpArr['AnswerText'],
                        'SystemAnswer'=>$answer,
                        'CorrectContent'=>'',
                        'OrderID'=>$j+1,
                        'IfCorrect'=>0,
                        'SendID'=>$tmpArr['SendID'],
                        'IfRight'=>$tmpArr['IfRight'],
                        'Star'=>$star,
                        'WorkID'=>$workID,
                        'TestID'=>$tmpArr['TestID'],
                        'JudgeID'=>$tmpArr['OrderID']
                    );
                    $output[$i][$ii]=$tmpDataArray;
                    $data[]=$tmpDataArray;
                    $ii++;
                }
            }
        }
        if(!empty($data)){

            if(D('UserSendWorkContent')->addAllData($data)===false){
                $this->setError('1X3068',1);
            }
        }

        return $output;
    }

    /**
     * 格式化答题情况
     * 去除之前选择题正确率增加提交作业是否超期
     * @param array $users 用户
     * @param array $answer 用户答案
     * @param array $work 作业内容
     * @param array $sendWork 发送的作业信息
     * @return array
     */

    private function formatReturnInfo($users,$answer,$work,$sendWork){
        foreach($users as $i=>$iUser){
            $users[$i]['NewName']=empty($iUser['RealName']) ? $iUser['UserName'] : $iUser['RealName'];
            unset($users[$i]['RealName']);
            unset($users[$i]['UserName']);
            //去除选择题错误率改用提交作业是否过期
            if(empty($answer[$iUser['UserID']])){
                $users[$i]['SendID']   = 0;
                $users[$i]['Content']  = '';
                $users[$i]['Delay']    = ($work[0]['EndTime']<time())?1:0;
                $users[$i]['SendTime'] = '';
                $users[$i]['DoTime']   = '';
                $users[$i]['Status']   = -1;
            }else{
                $users[$i]['SendID']      = $sendWork[$iUser['UserID']]['SendID'];
                $users[$i]['Content']     = $this->buildUserAnswer($answer[$iUser['UserID']]);
                $users[$i]['Delay']       = ($work[0]['EndTime']<$sendWork[$iUser['UserID']]['SendTime'])?1:0;
                $users[$i]['SendTime']    = date('Y-m-d H:i',$sendWork[$iUser['UserID']]['SendTime']);
                $users[$i]['DoTime']      = $sendWork[$iUser['UserID']]['DoTime'];
                $users[$i]['Status']      = $sendWork[$iUser['UserID']]['Status'];
                $users[$i]['CorrectNum']  = $sendWork[$iUser['UserID']]['CorrectNum']?$sendWork[$iUser['UserID']]['CorrectNum']:0;//已经批改数
                $users[$i]['TestNum']     = count($answer[$iUser['UserID']]);//试题总数
                $users[$i]['CheckTime']   = date('Y-m-d H:i',$sendWork[$iUser['UserID']]['CheckTime']);
                $users[$i]['Comment']     = $sendWork[$iUser['UserID']]['Comment'];
            }
        }

        //导学案知识试题映射表
        $tcMap='';
        if($this->workStyle=='case'){
            $tcMap=$this->buildTestCategoryMap($work);
        }
        //试题总数
        $testCount=$this->getTestCount($work[0]['TestList']);

        return array($users,$tcMap,$testCount);
    }

    /**
     * 格式化用户答题情况,对用户答案的试题正确与否,是否批改判断
     * @param array $buffer 用户答案数组
     * @return array 答题情况对应数组
     * 返回数组status字段规则如下:
     *   0=>未答题
     *   1=>正确
     *   2=>错误
     *   3=>未批阅
     *   4=>半对
     * @author demo
     */
    private function buildUserAnswer($buffer){
        $return = array();
        foreach($buffer as $i=>$iBuffer){
            //构建试题状态
            if($iBuffer['UserAnswer']){
                if($iBuffer['SystemAnswer']=='0'){//非选择
                    if($iBuffer['IfCorrect']==1){//已批阅
                        if($iBuffer['Star']==10){//10个星=正确
                            $return[$i]['status']=1;
                        }elseif($iBuffer['Star']==0){//没有星=错误
                            $return[$i]['status']=2;
                        }else{//0-10个星半对
                            $return[$i]['status']=4;
                        }
                    }else{
                        $return[$i]['status']=3;
                    }
                }else{//选择只显示对错
                    if($iBuffer['SystemAnswer']==$iBuffer['UserAnswer']){//答案正确
                        $return[$i]['status']=1;
                    }else{
                        $return[$i]['status']=2;
                    }
                }
            }else{//只要用户未作答 不管选择还是非选择统一显示为未做
                $return[$i]['status']=0;
            }
            //填充其他信息
            $return[$i]['testid']   = $iBuffer['TestID'];
            $return[$i]['judgeid']  = $iBuffer['JudgeID'];
            $return[$i]['sendid']   = $iBuffer['SendID'];
            $return[$i]['ifchoose'] = $iBuffer['SystemAnswer']=='0'?0:1;
            $return[$i]['ifcorrect'] = $iBuffer['IfCorrect'];
        }
        return $return;
    }

    /**
     * 构建导学案和试题对应名称<导学案前台显示使用>
     * @param array $param 作业内容
     * @return array
     */
    private function buildTestCategoryMap($param){
        $testCategory=array();
        $forum = unserialize($param[0]['CookieStr']);
        $forum = $forum['forum'];
        foreach($forum as $i=>$iForum){
            foreach($iForum[2] as $j=>$jForum){
                if($jForum['ifTest']==0){//跳过知识
                    continue;
                }else{ //查试题表
                    if($jForum['menuContent']==''){//栏目下没有试题
                        continue;
                    }else{
                        $testArr=explode(';',$jForum['menuContent']);
                        foreach($testArr as $k=>$kTestArr){
                            $testMsg=explode('|',$kTestArr);
                            if($testMsg[1]!='0'){
                                $testMsg[0]='c'.$testMsg[0];
                            }
                            if($testMsg[2]>1){
                                for($l=1;$l<$testMsg[2]+1;$l++){
                                    $testCategory[$testMsg[0].'-'.$l]=$jForum['menuName'];
                                }
                            }else{
                                $testCategory[$testMsg[0].'-0']=$jForum['menuName'];
                            }
                        }
                    }
                }
            }
        }
        return $testCategory;
    }

    /**
     * 构造试卷结构,在试题中加入题号,分割后小题的题号在前台js里添加
     * @notice 试卷结构,以cookieStr里的试题为准,索引丢失试题情况,答题情况和错题排行不再显示该题,作业详情现实试题丢失,不保留试题数量信息
     * @param string $cookieStr cookieStr数据
     * @param array $testResult 试题数据
     * @notice 获取的索引数据可能会少,以coookieStr里的数据为准
     * @notice 提分端有类似方法封装在TestRealModel里,以后可以统一化格式,统一方法
     * @return array $result
     * @author demo
     */
    private function buildPaperStruct($cookieStr,$testResult){
        $result    = array();

        if($this->workStyle=='test'){
            $result = $this->buildStructureByTest($cookieStr,$testResult);
        }elseif($this->workStyle=='case'){
            $result = $this->buildStructureByCase($cookieStr,$testResult);
        }

        return $result;
    }

    /**
     * 构造作业试卷结构
     * @param string $cookieStr cookieStr数组
     * @param array $testResult 试题数据
     * @return array $result
     * @author demo
     */
    private function buildStructureByTest($cookieStr,$testResult){
        //获取结构数组
        //$baseModel = D('Base');
        $cookieStr = R('Common/DocLayer/formatPaperCookie',array($cookieStr));
        $cookieStr = $cookieStr['parthead'];

        $output   = array();//重构结构数组
        $ii       = 1;

        foreach($cookieStr as $i=>$iCookieStr){//第一层#一二卷
            foreach($iCookieStr['questypehead'] as $j=>$jCookieStr){//第二层#题型
                $output[$ii]['questype']=$jCookieStr[2];//题型名称
                $output[$ii]['chinesenum']=formatString('num2Chinese',$ii);//题型中文序号
                foreach($jCookieStr[5] as $k=>$kCookieStr){//第三层#获取该题型下的所有试题
                    static $startNum = 1;//题号开始位置
                    $testNum = $kCookieStr[1];//试题数量
                    $testID  = $kCookieStr[0];//该题试题id
                    $output[$ii]['test'][$k] = $testID;
                    if($testResult) {
                        if (array_key_exists($testID, $testResult)) {//在题文中加入序号
                             $testResult[$testID]['testsplit']['content'] = R('Common/TestLayer/changeTagToNum',array($testResult[$testID]['testsplit']['content'], $startNum,0,true,$testResult[$testID]['testnum']));
                            $testResult[$testID]['error']=0;
                        } else {//索引中没有该数据
                            $testResult[$testID]['error']=1;
                            $testResult[$testID]['testnum'] = $testNum;//记录丢失的试题数目
                        }
                    }
                    $startNum+=$testNum;
                }
                $ii++;
            }
        }

        return  array('struct'=>$output,'testResult'=>$testResult);
    }

    /**
     * 构造导学案试卷结构
     * @param string $forum 导学案结构字符串
     * @param array $testResult 试题数据
     * @return array $result
     * @author demo
     */
    private function buildStructureByCase($forum,$testResult){
        //获取结构数组
        $forum = unserialize($forum);
        $forum = $forum['forum'];
        $menuIDArray = array(); //记录栏目id，相同栏目id的序号顺延
        $caseMenu    = SS('caseMenu');

        $output = array();//重构结构数组
        $ii     = 1;
        foreach($forum as $i=>$iForum){
            $output[$ii]['title']=$iForum[0];
            $output[$ii]['titlealias']=$iForum[1];
            foreach($iForum[2] as $j=>$jForum){
                if(empty($menuIDArray[$jForum['menuID']])){
                    $menuIDArray[$jForum['menuID']]=1;
                }
                $testNum=$menuIDArray[$jForum['menuID']];//试题序号

                $output[$ii]['test'][$j]['menuName'] = $jForum['menuName'];
                $output[$ii]['test'][$j]['ifTest']   = $jForum['ifTest'];
                if(!$jForum['menuContent']){
                    $output[$ii]['test'][$j]['testlist'] = '';
                }else{
                    $testArr=explode(';',$jForum['menuContent']);
                    foreach($testArr as $k=>$kTestArr){
                        $testMsg=explode('|',$kTestArr);
                        //获取真实id
                        if($jForum['ifTest']==0){//知识
                            if($testMsg[1]=='0'){
                                $testID='l'.$testMsg[0];
                            }else{
                                $testID='u'.$testMsg[0];
                            }
                        }else{//试题
                            if($testMsg[1]=='0'){
                                $testID=$testMsg[0];
                            }else{
                                $testID='c'.$testMsg[0];
                            }
                        }
                        $output[$ii]['test'][$j]['testlist'][] = $testID;//获取试题id

                        if($testResult) {
                            if (array_key_exists($testID, $testResult)) {
                                //在题文中加入序号
                                if ($jForum['ifTest'] == 0) {//知识
                                    $testResult[$testID]['Lore'] = R('Common/TestLayer/changeTagToNum',array($testResult[$testID]['Lore'], $testNum, $caseMenu[$jForum['menuID']]['NumStyle']));

                                    if(!empty($testResult[$testID]['Answer'])) {
                                        $testResult[$testID]['Answer'] = R('Common/TestLayer/changeTagToNum', array($testResult[$testID]['Answer'], $testNum, $caseMenu[$jForum['menuID']]['NumStyle']));
                                    }else{
                                        $testResult[$testID]['Answer'] = '';
                                    }


                                } else {//试题
                                    $testResult[$testID]['testsplit']['content'] = R('Common/TestLayer/changeTagToNum',array($testResult[$testID]['testsplit']['content'], $testNum,0,true,$testResult[$testID]['testnum']));
                                }
                                $testResult[$testID]['error']=0;
                            } else {
                                $testResult[$testID]['error']=1;
                                $testResult[$testID]['testnum'] = $testMsg[2]<2 ? 1 : $testMsg[2];//记录丢失的试题数目
                            }
                        }

                        $testNum+=($testMsg[2]<2 ? 1 : $testMsg[2]);
                    }
                }

                $menuIDArray[$jForum['menuID']]=$testNum;//试题序号
            }
            $ii++;
        }
        return array('struct'=>$output,'testResult'=>$testResult);
    }

    /**批改作业数据处理**/

    /**批改作业结束**/

    /**作业导学案相关方法的具体实现**/

    /**
     * 获取work留作业作业存档
     * getLeavedUserWork的具体实现
     * $param array 见母方法
     * @author demo
     */
    private function getLeavedUserWorkByTest($param){
        extract($param);
        $where=' StyleState=1 and UserName="'.$userName.'" and SubjectID='.$subjectID;
        $docSave = $this->getModel('DocSave');
        $count=$docSave->selectCount(
            $where,
            'SaveID'
        );
        if(!$count){ return ''; }
        $page=page($count,$_POST['page'],$perPage);
        $buffer=$docSave->selectData(
            'SaveID,TestNum,LoadTime,TestList,SubjectID,SaveName',
            $where,
            'LoadTime desc',
            ($perPage*($page-1)).','.$perPage
        );
        $subjectArray = SS('subject');//加入学科名称
        foreach($buffer as $i=>$iBuffer){
            $buffer[$i]['SubjectName']=$subjectArray[$buffer[$i]['SubjectID']]['SubjectName'];
            $buffer[$i]['LoadTime']=date('Y-m-d',$buffer[$i]['LoadTime']);
        }

        return array('content'=>$buffer,'count'=>$count);
    }

    /**
     * 获取case留作业作业存档
     * getLeavedUserWork的具体实现
     * $param array 见母方法
     * @author demo
     */
    private function getLeavedUserWorkByCase($param){
        extract($param);
        $where = ' IfSystem=2 and UserName="'.$userName.'" and SubjectID='.$subjectID;
        $caseTpl = $this->getModel('CaseTpl');
        $count = $caseTpl->selectCount(
            $where,
            'TplID'
        );
        if(!$count){ return ''; }
        //获取导学案数据
        $page    = page($count,$_POST['page'],$perPage);
        $buffer  = $caseTpl->selectData(
            '*',
            $where,
            'AddTime desc',
            ($perPage*($page-1)).','.$perPage
        );
        $subjectArray = SS('subject');//加入学科名称 课时id更换名称
        $chapterArray = SS('chapterList');//加入课时名称
        foreach($buffer as $i=>$iBuffer){
            if(isset($subjectArray[$buffer[$i]['SubjectID']])){
                $buffer[$i]['SubjectName']=$subjectArray[$buffer[$i]['SubjectID']]['SubjectName'];
            }else{
                $buffer[$i]['SubjectName']='';
            }

            if(isset($chapterArray[$iBuffer['ChapterID']])){
                $buffer[$i]['ChapterName']=$chapterArray[$iBuffer['ChapterID']]['ChapterName'];
            }else{
                $buffer[$i]['ChapterName']='';
            }

            $buffer[$i]['AddTime']=date('Y-m-d',$buffer[$i]['AddTime']);
        }
        return array('content'=>$buffer,'count'=>$count);
    }

    /**
     * 删除教师已留work作业的存档
     * delLeavedUserWork的具体实现
     * @author demo
     */
    private function delLeavedUserWorkByTest($param){
        extract($param);
        $docSave = $this->getModel('DocSave');
        $buffer=$docSave->selectData(
            '*',
            'SaveID='.$id
        );
        if(!$buffer) $this->setError('1X3003',1);
        $saveName=$buffer[0]['SaveName'];
        if($userName!=$buffer[0]['UserName']) $this->setError('1X3004',1);
        if($docSave->deleteData(
                'SaveID='.$id)===false){
            $this->setError('1X3005',1);
        }
        return $saveName;
    }

    /**
     * 删除教师已留case作业的存档
     * delLeavedUserWork的具体实现
     * @author demo
     */
    private function delLeavedUserWorkByCase($param){
        extract($param);
        $caseTpl = $this->getModel('CaseTpl');
        $buffer=$caseTpl->selectData(
            '*',
            'TplID='.$id
        );
        if(!$buffer) $this->setError('1X3203',1);
        $tempName=$buffer[0]['TempName'];
        if($userName!=$buffer[0]['UserName']) $this->setError('1X3207',1);
        if($caseTpl->deleteData(
                'TplID='.$id)===false){
            $this->setError('1X3206',1);
        }
        return $tempName;
    }

    /**
     * 更改教师留作业的的名称的具体实现
     * @author demo
     */
    private function changeLeavedUserWorkNameByTest($param){
        extract($param);
        if(!empty($paperName)) $this->setError('1X3006',1);
        $docSave = $this->getModel('DocSave');
        $buffer=$docSave->selectData(
            '*',
            'SaveID='.$id
        );
        if(!$buffer) $this->setError('1X3002',1);
        if($userName!=$buffer[0]['UserName']) $this->setError('1X3007',1);
        $oldName=$buffer[0]['SaveName'];

        if($docSave->updateData(
                array('SaveName'=>$newName),
                'SaveID='.$id)===false){
            $this->setError('1X3008',1);
        }
        return $oldName;
    }

    /**
     * 更改教师留作业的的名称的具体实现
     * @author demo
     */
    private function changeLeavedUserWorkNameByCase($param){
        extract($param);
        if(empty($newName)) $this->setError('1X3202',1);
        $caseTpl = $this->getModel('CaseTpl');
        $buffer=$caseTpl->selectData(
            '*',
            'TplID='.$id
        );
        if(!$buffer) $this->setError('1X3203',1);
        if($userName!=$buffer[0]['UserName']) $this->setError('1X3204',1);

        $oldName=$buffer[0]['TempName'];
        if($caseTpl->updateData(
                array('TempName'=>$newName),
                'TplID='.$id)===false){
            $this->setError('1X3205',1);
        }
        return $oldName;
    }


    /**
     * 查看已留作业的内容的具体实现
     * @author demo
     */

    private  function showLeavedUserWorkByTest($param){
        extract($param);
        $docSave = $this->getModel('DocSave');
        $buffer=$docSave->selectData(
            '*',
            'SaveID='.$id
        );
        $result='';
        if($buffer){
            if($buffer[0]['UserName']!=$userName) $this->setError('1X3102',1);
            $result[0] = $buffer[0]['CookieStr'];
            return $result;
        }else{
            $this->setError('1X3002',1);
        }
    }
    /**
     * 查看已留作业的内容的具体实现
     * @author demo
     */
    private  function showLeavedUserWorkByCase($param){
        extract($param);
        $caseTpl = $this->getModel('CaseTpl');
        $buffer=$caseTpl->selectData(
            '*',
            'TplID='.$id
        );
        if($buffer[0]['UserName']!=$userName || $buffer[0]['IfSystem']!=2) $this->setError('1X3211',1);
        $content='';
        if(!empty($buffer)){
            $content              = unserialize($buffer[0]['Content']);
            $content['subjectID'] = $buffer[0]['SubjectID'];
            $content['tplID']     = $buffer[0]['TplID'];
            $content['tempName']  = $buffer[0]['TempName'];
        }else{
            $this->setError('1X3213',1);
        }
        return $content;
    }

    /**
     * 给学生布置作业的具体实现
     * @param $param array 参数数组
     * @return mixed
     * @author demo
     */
    private function assignLeavedUserWorkByTest($param){
        extract($param);
        //获取saveid数据
        $docSave = $this->getModel('DocSave');
        $buffer=$docSave->selectData(
            '*',
            'SaveID='.$saveID
        );
        if($buffer[0]['UserName']!=$userName || $buffer[0]['StyleState']==0) $this->setError('1X3059',1);

        $stuWorkDown='';
        //是否下载作答 生成学生下载
        if($assignStyle==1){

            $doc = $this->getModel('Doc');
            $param=array();
            $param['subjectID']=$buffer[0]['SubjectID']; //学科id
            $param['cookieStr']=$buffer[0]['CookieStr']; //内容
            $param['isSaveRecord']=0; //是否存档
            $param['docVersion']='.docx'; //文档类型
            $param['paperSize']='A4'; //纸张大小
            $param['paperType']='student'; //试卷类型
            $param['backType']=1; //是否仅返回路径
            $param['testList']=$buffer[0]['TestList']; //试题id字符串以英文逗号间隔
            $param['docName']=$buffer[0]['SaveName']; //文档名称
            $param['downStyle']=2; // 区分下载作业还是试卷 答题卡4 导学案3 作业2，试卷1
            $userName=$this->getCookieUserName();
            $path=$this->getApiDoc('Doc/getDownUrlByCookie',$param,$userName);
            if($path[0]=='false'){
                $this->setError($path['msg'],1,'',$path['replace']);
            }else{
                $stuWorkDown=$path;
            }
        }
        $saveName=$buffer[0]['SaveName'];
        if($workName) $saveName=$workName;
        //写入work表
        $data=array();
        $data['UserName']    = $userName;
        $data['WorkName']    = $saveName;//作业名称
        $data['TestList']    = $buffer[0]['TestList'];
        $data['CookieStr']   = $buffer[0]['CookieStr'];
        $data['StuWorkDown'] = $stuWorkDown;
        $data['WorkStyle']   = $assignStyle;
        $data['StartTime']   = $startTime;
        $data['EndTime']     = $endTime;
        $data['WorkOrder']   = $assignOrder;
        $data['Message']     = $description;
        $data['TestNum']     = $buffer[0]['TestNum'];
        $data['LoreNum']     = 0;//知识点给默认值
        $data['SubjectID']   = cookie('SubjectId');
        $data['LoadTime']    = time();
        $data['WorkType']    = $this->workType;//作业类型 普通作业1 导学案2...

        if(($workID=D('UserWork')->insertData(
                $data
            ))===false){
            $this->setError('1X3060',1);
        }
        //添加任务完成记录，针对在线作答  2015-11-16
        if(0 == $assignStyle){
            $this->getModel('MissionHallRecords')->finishTask(
                $this->getCookieUserID()
            );
        }
        return array(
            'workID'   => $workID,
            'workName' => $buffer[0]['SaveName']
        );
    }

    /**
     * 给学生布置作业的具体实现
     * @param  $param array 参数数组
     * @return mixed
     * @author demo
     */
    private function assignLeavedUserWorkByCase($param){
        extract($param);
        //获取模板数据
        $caseTpl = $this->getModel('CaseTpl');
        $buffer=$caseTpl->selectData(
            '*',
            'TplID='.$saveID
        );
        if($buffer[0]['UserName']!=$userName || $buffer[0]['IfSystem']!=2) $this->setError('1X3211',1);

        $stuWorkDown = ''; //下载链接

        if($assignStyle==1){//若指定下载作答

            $param=array();
            $param['subjectID']=0;
            $param['cookieStr']=unserialize($buffer[0]['Content']); //导学案内容
            $param['isSaveRecord']=0;
            $param['docVersion']='.docx';
            $param['paperSize']='A4';
            $param['paperType']='student';
            $param['subjectID']=$buffer[0]['SubjectID'];
            $param['backType']=1;
            $param['docName']=$buffer[0]['SaveName'];
            $param['downStyle']=2; // 区分下载作业还是试卷 答题卡4 导学案3 作业2，试卷1

            $userName=$this->getCookieUserName();
            $path=$caseTpl->getDownUrlByCookie($param,$userName);

            if($path[0]=='false'){
                $this->setError($path['msg'],1);
            }else{
                $stuWorkDown=$path;
            }
        }

        //获取试题id和知识id
        $userTestList        = $caseTpl->reSetTestIdAndLoreId(unserialize($buffer[0]['Content']));//导学案的知识点ID和试题id


        $saveName=$buffer[0]['TempName'];
        if($workName) $saveName=$workName;

        //写入user_work表
        //构建data
        $data                 = array();
        $data['UserName']    = $userName;
        $data['WorkName']    = $saveName;//导学案作业名称
        $data['TestList']    = $userTestList;
        $data['CookieStr']   = $buffer[0]['Content'];
        $data['StuWorkDown'] = $stuWorkDown;//学生下载地址
        $data['WorkStyle']   = $assignStyle;
        $data['StartTime']   = $startTime;
        $data['EndTime']     = $endTime;
        $data['WorkOrder']   = $assignOrder;
        $data['Message']     = $description;
        $data['TestNum']     = $buffer[0]['TestNum'];//题目数量
        $data['LoreNum']     = $buffer[0]['LoreNum'];//知识点数量 新增加知识点字段
        $data['SubjectID']   = cookie('SubjectId');
        $data['LoadTime']    = time();
        $data['WorkType']    = $this->workType;//作业类型 普通作业1 导学案2...

        if(($workID=D('UserWork')->insertData(
                $data
            ))===false){
            $this->setError('1X3212',1);
        }
        return array(
            'workID'=>$workID,
            'workName'=>$buffer[0]['TempName']
        );
    }


    /**作业导学案相关方法的具体实现**/
}