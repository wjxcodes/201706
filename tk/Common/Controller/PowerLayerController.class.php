<?php
/**
 * @author demo
 * @date 2016年01月12日
 */

namespace Common\Controller;


class PowerLayerController extends CommonController
{
//    //【Manage分组】manageVerifyPower 函数内赋值
//    public $manageIfDiff=0; //是否区分用户
//    public $manageIfSubject = 0; //是否区分学科
//    public $manageMySubject = ''; //当前用户所属
    //【teacher分组】teacherCheckLogin 函数内赋值

    /**
     * 描述：登录和权限验证
     * @param string $mainModule
     * @author demo
     */
    public function checkLoginAndPower($mainModule){
        //不需要登录验证的数组
        $notNeedLoginAction=[
            //Aat分组
            'Aat/App/index',
            'Aat/App/down',
            'Aat/App/code',
            'Aat/App/updateLog',
            'Aat/App/mUpdateLog',
            // 'Aat/Default/index',
            'Aat/Default/logout',
            'Aat/Default/weidian',
            // 'Aat/Default/login',
            'Aat/Default/ajaxSubject',
            'Aat/Default/verify',
            'Aat/Default/getServiceTerm',
            'User/Aat/avatarUpload',
            //AatApi分组
            'AatApi/Default/logout',
            'AatApi/Default/login',
            'AatApi/Default/register',
            'AatApi/Default/ajaxSubject',
            'AatApi/Default/verifyMobile',
            'AatApi/Default/getServiceTerm',
            'AatApi/Default/checkUpdate',
            'AatApi/Default/checkGetPasswordInfo',
            'AatApi/Default/getPasswordSendCode',
            'AatApi/Default/registerSendCode',
            'AatApi/Default/checkGetPasswordCode',
            'AatApi/Default/setPassword',
            'AatApi/UnionExam/showInfo',
            //Home分组
            //'Home/Index/index',
            //'Home/Index/login',
            'Home/Index/register',
            'Home/Index/verify',
            'Home/Index/getServiceTerm',
            'Home/Index/setPass',
            'Home/Index/monitor',
            'Home/Index/loginOut',
            'Home/Index/excelStudent',
            'Work/MyClass/uploadify',
            'Home/Base/isLoginCheck',
            'User/Home/sendPhoneCode',
            'User/Home/checkPhoneCode',
            'Custom/CustomTestStore/mobile',
            'Custom/CustomTestStore/avatarUpload',
            //Teacher分组
            // 'Teacher/Public/login',
            'Teacher/Public/check',
            'Teacher/Public/logout',
            'Teacher/Public/verify',
            'Teacher/Public/footer',
            'Teacher/Public/uploadify',
            'Teacher/Public/drag',
            'Teacher/Public/ends',
            'Teacher/Public/register',
            'Teacher/Public/top',
            'Teacher/Public/main',
            'Teacher/Public/menu',
            'Teacher/Public/getData',
            'Teacher/Public/getBasicData',
            'Teacher/Public/getOptionWidth',
            'Teacher/Public/getchapter',
            'Teacher/CustomCheck/upload',
            'Teacher/CustomIntro/upload',
            //Manage分组
            'Manage/Public/login',
            'Manage/Public/check',
            'Manage/Public/logout',
            'Manage/Public/verify',
            'Manage/Public/footer',
            'Manage/Public/uploadify',
            'Manage/Public/drag',
            'Manage/Public/ends',
            'User/User/uploadify',
            'Analysis/TjExamClass/uploadify',
            //Exam分组
            'Exam/Index/index',
            'Exam/Index/login',
            'Exam/Index/register',
            'Exam/Index/verify',
            'Exam/Index/getPhonecode',
            'Exam/Index/checkUserLogin',
            'Exam/Index/getServiceTerm',
            'Exam/Index/getPass',
            'Exam/Index/setPass',
            'Exam/Index/loginOut',
            'Exam/Index/getPosition',
            //Statistics分组
            'Statistics/Index/index',
            'Statistics/StatisticsB',
            'Statistics/StatisticsB/systemAjaxResponse',
            'Statistics/StatisticsB/workAnswerinfo',
            'Statistics/StatisticsB/index',
            'Statistics/StatisticsB/homeworklist',
            'Statistics/StatisticsB/homeWorkList',
            'Statistics/StatisticsB/studentWorkList',
            'Statistics/StatisticsB/workAnswerInfo',
			'Statistics/StatisticsB/Teacherzj',
            'Statistics/StatisticsB/teacherComment',
            'Statistics/StatisticsB/wkvedio',
            'Statistics/StatisticsB/userWorkInfo',
			'Statistics/StatisticsB/wrongTest',
			'Statistics/StatisticsB/teachercheck',
        ];
        $nowAction = MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        // dump($nowAction);
        $homeUrl='Home/Index/main';
        $u='';
        if(isset($_REQUEST['u'])) $u='?u='.$_REQUEST['u'];
        //登录界面跳转
        $loginActionArray = [
            'Home/Index/index'=>$homeUrl.$u,
            'Manage/Public/login'=>'Manage/Index/index',
            'Teacher/Public/login'=>'Teacher/Index/index',
        ];
        if(key_exists($nowAction,$loginActionArray)){
            $checkStatus=$this->checkLogin($mainModule,1);
            if(is_array($checkStatus) && $checkStatus[0]==1){//1,已经登录，否则错误码
                //执行经验录入
                if($mainModule=='Home'){
                    $userName=$this->getCookieUserName();
                    $this->getModel('UserExp')->addUserExpAll($userName,'login');
                }
                header('location:'.U($loginActionArray[$nowAction]));
                exit();
            }
        }
        //需要登录验证的跳转对应方法
        if(!in_array($nowAction,$notNeedLoginAction)){
            switch($mainModule){
                case 'Aat':
                    $this->aatPower();break;
                case 'AatApi':
                    $this->aatApiPower();break;
                case 'Home':
                case 'NewTeacher':
                    $this->homePower();break;
                case 'Teacher':
                    $this->teacherPower($nowAction);break;
                case 'Manage':
                    $this->managePower();break;
                case 'Exam':
                    $this->examPower();break;
                default:
                    ;
            }
        }
        return [
            'ifDiff'=>$this->ifDiff,
            'ifSubject'=>$this->ifSubject,
            'mySubject'=>$this->mySubject,
        ];
    }

    public function homePower(){
        $checkStatus=$this->checkLogin('Home',1);
        if(is_numeric($checkStatus)){
            $url = '/Home/Index/main';
            $this->passport($url);
            // $flag=0;
            // if(IS_AJAX){
            //     $flag=1;
            // }
            // $this->setError($checkStatus,$flag,U('Home/Index/index?u='.$_REQUEST['u']));
        }
        $this->homeCheckUserAction();
        $this->mySubject=$checkStatus[1][0]['SubjectStyle'];
    }
    /**
     * 根据用户权限标记，获取权限值
     * 调用用户cookie值和权限缓存，根据权限名称（例如：limitDown）返回权限值，
     * @param string $tag 用户权限标记
     * @return int
     * @author demo
     */
    public function getUserPowerByTag($tag){
        $userName = $this->getCookieUserName();
        $userID = $this->getModel('User')->selectData(
            'UserID',
            'UserName="'.$userName.'"');
        $userID = $userID[0]['UserID'];
        $userData = $this->getModel('UserGroup')->selectData(
            'GroupID,LastTime',
            'GroupName=1 AND UserID="'.$userID.'"');
        $powerUserList = SS('powerUserList');//ID为键用户权限数组
        $powerTagArr = SS('powerUserGroup');//需要验证权限的数组
        $userPowerMsg=0;  //1可以下载
        $thisUserPower=$powerTagArr[1]['groupList'][$userData[0]['GroupID']];
        if($thisUserPower['TagArr'][0]=='all'){
            $userPowerMsg='all';
        }else{
            foreach($thisUserPower['TagArr'] as $i=>$iThisUserPower){
                if($thisUserPower['TagArr'][$i]==$tag){
                    $userPowerMsg=$powerUserList[$tag]['Value'];
                }
            }
        }
        return $userPowerMsg;
    }


    /**
     * 验证用户登录
     * @param string $modelName 站点名称Home\Manage\Aat\Index\Teacher\Exam
     * @param int $flag 返回类型 1返回数字 0提示并跳转
     * @return int|none    1 已登录 否则返回错误码
     * @author demo
     */
    public function checkLogin($modelName='',$flag=0) {
        //默认系统
        $modelName=preg_replace('/[0-9]+/','',$modelName);
        if(empty($modelName)){
            $modelName=$this->getMainModuleName();
        }

        //系统要求 跳转路径、身份
        switch($modelName){
            case 'Index':
                $loginUrl=U('/');

                //官网特殊处理 优先组卷用户
                $home=$this->checkLogin('Home',1);
                if(is_numeric($home)){
                    $home=$this->checkLogin('Aat',1);
                    if(is_numeric($home)){
                        if($flag) return $home;
                        else $this->setError($home,0,$loginUrl);
                    }
                }
                return $home;
                break;
            case 'Home':
                $loginUrl=U('/Home','',false);
                $whoIs=1; //身份验证教师
                break;
            case 'Teacher':
                $loginUrl=U('/Teacher','',false);
                $whoIs=1; //身份验证教师
                break;
            case 'Aat':
                $loginUrl=U('/Aat','',false);
                $whoIs=0; //身份验证学生
                break;
            case 'Exam':
                $loginUrl = U('/Exam/Index/login');
                $whoIs=-1; //无需验证
                break;
            case 'Manage':
                $loginUrl=U('/Manage','',false);
                $whoIs=-1; //管理员
                break;
            case 'Statistics':
            $loginUrl=U('/Statistics','',false);
            $whoIs=3; //校长
            break;
        }

        $userName=$this->getCookieUserName($modelName);
        $oldCode=$this->getCookieCode($modelName);
        if(!$userName || !$oldCode){
            if($flag) return '30205';
            else $this->setError('30205',0,$loginUrl);
        }
        //获取用户信息
        $adminName='UserName';
        $adminID='UserID';
        if($modelName=='Manage'){
            $buffer=$this->getModel('Admin')->selectData(
                'AdminID,AdminName,SaveCode,Status,GroupID,IfWeak,MySubject',
                "AdminName='".$userName."'"
            );
            $adminName='AdminName';
            $adminID='AdminID';
        }elseif($modelName=='Exam'){
            $buffer = $this->getModel('ReserveUser')->selectData(
                'UserID,UserName,SaveCode,PositionID',
                'UserName="'.$userName.'"',
                '',
                ''
            );
        }else{
            $buffer = $this->getModel('User')->selectData(
                'UserID,UserName,RealName,SaveCode,Status,Whois,SubjectStyle,IfAuth,Cz,Times,Logins,LastTime,LastIP,UserPic',
                'UserName="'.$userName.'"'
            );
        }

        if($buffer[0]['Status']!=0){
            if($flag) return '30203';
            else $this->setError('30203',0,$loginUrl);
        }

        if($whoIs!=-1 && $buffer[0]['Whois']!=$whoIs){
            if($flag) return '30206';
            else $this->setError('30206',0,$loginUrl);
        }

        $time=C('WLN_COOKIE_TIMEOUT');
        $code=md5($buffer[0][$adminID].$buffer[0][$adminName].$buffer[0]['SaveCode'].ceil(time()/$time));
        $code1=md5($buffer[0][$adminID].$buffer[0][$adminName].$buffer[0]['SaveCode'].(ceil(time()/$time)-1));
        if($oldCode!=$code && $oldCode!=$code1){

            $this->setCookieUserName(null,$time,$modelName);
            $this->setCookieUserID(null ,$time,$modelName);
            $this->setCookieCode(null ,$time,$modelName);

            if($flag) return '30205';
            else $this->setError('30205',0,$loginUrl);
        }

        $cookieUserID = $this->getCookieUserID($modelName);//getCookieUserID要兼容手机端的
        if($cookieUserID){
            //如果使用了UserID
            if($cookieUserID!==$buffer[0][$adminID]){
                if($flag) return '30205';
                else $this->setError('30205',0,$loginUrl);
            }
        }

        if($oldCode==$code){
            $this->setCookieUserName($buffer[0][$adminName],$time,$modelName);
            $this->setCookieUserID($buffer[0][$adminID] ,$time,$modelName);
            $this->setCookieCode($code ,$time,$modelName);
        }

        return array(1,$buffer);
    }



    /**
     * 验证用户权限入口.如果有权限,调用专用函数,验证权限范围
     * @return boolean
     * @author demo
     */
    private function homeCheckUserAction(){
        $userName = $this->getCookieUserName();
        $userID = $this->getCookieUserID();
        $pageTag = MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        $rules = $this->getApiCommon('Power/homeCheckThisPower',$pageTag,$userID);
        if($rules===true){
            return true;
        }
        $pageTag=$rules['pageTag'];

        if(empty($rules['value'])){//没权限
            if(IS_AJAX){
                $this->setError('30826',1);
            }else{
                $this->setError('30826',0);
            }
        }
        //查看剩余权限值
        switch ($pageTag) {
            case 'Home/Index/arswerDown':
                return $this->homeCheckDownTime($rules, $userName, 4);
                break;
            case 'Home/Index/create'://下载(答题卡,试卷)次数权限
                return $this->homeCheckDownTime($rules,$userName, 1);
                break;
            case 'Guide/Case/create'://下载(答题卡,试卷)次数权限
                return $this->homeCheckDownTime($rules,$userName, 3);
                break;
            case 'Work/Work/workDown'://下载作业
                return $this->homeCheckDownTime($rules,$userName, 2);
                break;
            case 'Home/Index/savePaper'://存档数量权限
                return $this->homeCheckSavePaper($rules,$userName);
                break;
            case 'Ga/Index/ga'://智能组卷次数
                return $this->homeCheckIntellPaper($rules,$userName);
                break;
            case 'Dir/Index/getTestByContent'://模板组卷次数
                return $this->homeCheckTplPaper($rules,$userName);
                break;
            case 'Dir/Index/saveTemplate'://保存模板数量
                return $this->homeCheckSaveModel($rules,$userName);
                break;
            case 'Guide/Case/saveTemp'://导学案保存模板数量
                return $this->homeCheckSaveCaseModel($rules,$userName);
                break;
            case 'Home/Index/favSave':
                return $this->homeCheckCollection($rules, $userName);
                break;
            case 'Work/Work/assignLeavedUserWork/workStyle/test': //验证作业下载
                return $this->homeCheckWorkDownNum($rules, $userName);
                break;
            case 'Manual/Index/singleDown': //单题下载
                return $this->homeCheckSingleDown($rules, $userID);
                break;
            default:
                if(IS_AJAX){$this->setError(null,1);}else{$this->setError('30309',0);}
        }
    }

    /**
     * 验证用户下载次数
     * @param int $power 用户权限值
     * @param string $userName 用户名
     * @param int $type 历史下载分类
     * @return boolean
     * @author demo
     */
    private function homeCheckDownTime($power,$userName, $type){
        $unit = $power['unit'];
        list($b, $e) = $this->getModel('PowerUserList')->getCycleInterval($unit);
        $power = $power['value'];
        $buffer = $this->getModel('DocDown')->selectData(
            'Point',
            'DownStyle='.$type.' and UserName="'.$userName.'" and LoadTime between '.$b.' and '.$e
        );
        $result=$this->homeCheckLimitDown($_POST['testList']); //返回不能下载的结果内容
        if($result[0]!=1){
            //获取limitDown下载权限
            $ifLimitDown=$this->getUserPowerByTag('limitDown');
            //返回值为all 管理员组默认开启权限 返回1:该功能开启下载权限
            if($ifLimitDown==0){ //没有下载权限
                $this->setBack($result);
            }
        }
        if(is_null($buffer)){
            $buffer = array();
        }
        if(count($buffer) >= $power){
            if(!$unit)
                $this->setError('30827',1,'',count($buffer));
            else{
                $this->setError('30828',1,'',$power);
            }
        }
    }

    /**
     * 检查试题收藏梳理
     * @param int $rules 验证数据
     * @param string $userName 用户名
     * @author demo 2015-8-20
     */
    private function homeCheckCollection($rules, $userName){
        $unit = $rules['unit'];
        list($b, $e) = $this->getModel('PowerUserList')->getCycleInterval($unit);
        $power = $rules['value'];
        $buffer = $this->getModel('UserCollect')->selectCount(
            '`From`=1 and UserName="'.$userName.'" AND LoadTime BETWEEN '.$b.' AND '.$e,
            'CollectID'
        );
        if($buffer >= $power){
            $this->setError('30829',1,'',$buffer);
        }
    }

    /**
     * 验证用户作业下载，下载作答
     * @author demo
     * @date
     */
    private function homeCheckWorkDownNum($power, $userName){
        if(1 != (int)$_POST['assignStyle']){
            return true;
        }
        $unit = $power['unit'];
        list($b, $e) = $this->getModel('PowerUserList')->getCycleInterval($unit);
        $power = $power['value'];
        $buffer = $this->getModel('UserWork')->selectCount(
            'WorkStyle=1 and UserName="'.$userName.'" AND LoadTime BETWEEN '.$b.' AND '.$e,
            'WorkID'
        );
        if($buffer >= $power){
            $this->setError('30830',1,'',$buffer);
        }
    }

    /**
     * 验证用户存档次数
     * @param int $power 用户权限值
     * @param string $userName 用户名
     * @return boolean
     * @author demo
     */
    private function homeCheckSavePaper($power,$userName){
        $unit = $power['unit'];
        list($b, $e) = $this->getModel('PowerUserList')->getCycleInterval($unit);
        $power = $power['value'];
        $buffer = $this->getModel('DocSave')->selectCount(
            'StyleState=0 and UserName="'.$userName.'" AND LoadTime BETWEEN '.$b.' AND '.$e,
            'SaveID'
        );
        if($buffer >= $power){
            $this->setError('30831',1,'',$buffer);
        }
    }

    /**
     * 智能组卷次数
     * @param int $power 用户权限值
     * @param string $userName 用户名
     * @return boolean
     * @author demo
     */
    private function homeCheckIntellPaper($power,$userName){
        $unit = $power['unit'];
        list($b, $e) = $this->getModel('PowerUserList')->getCycleInterval($unit);
        $power = $power['value'];
        $buffer = $this->getModel('LogIntellPaper')->selectCount(
            'UserName="'.$userName.'" and AddTime between '.$b.' and '.$e,
            'PaperID');
        if($buffer >= $power){
            $this->setError('30832','1','',$buffer);
        }
    }

    /**
     * 模板组卷次数
     * @param int $power 用户权限值
     * @param string $userName 用户名
     * @return boolean
     * @author demo
     */
    private function homeCheckTplPaper($power,$userName){
        $unit = $power['unit'];
        list($b, $e) = $this->getModel('PowerUserList')->getCycleInterval($unit);
        $power = $power['value'];
        $buffer = $this->getModel('LogTplPaper')->selectCount(
            'UserName="'.$userName.'" and AddTime between '.$b.' and '.$e,
            'PaperID'
        );
        if($buffer >= $power){
            $this->setError('30833',1,'',$buffer);
        }
    }

    /**
     * 保存模板数量模板
     * @param int $power 用户权限值
     * @param string $userName 用户名
     * @return boolean
     * @author demo
     */
    private function homeCheckSaveModel($power,$userName){
        $power = $power['value'];
        $subjectID = $_POST['subjectId'];
        $tplID = $_POST['templateListId'];
        if($tplID == ''){
            $where='UserName="'.$userName.'" and SubjectID='.$subjectID;
            $resultTotal = $this->getModel('DirTemplate')->selectData(
                'TempID',
                $where
            );
            if(count($resultTotal)>=$power){
                $this->setError('30834',1,'',count($resultTotal));
            }
        }
    }

    /**
     * 保存导学案模板数量模板
     * @param int $power 用户权限值
     * @param string $userName 用户名
     * @return boolean
     * @author demo
     */
    private function homeCheckSaveCaseModel($power,$userName){
        $power = $power['value'];
        $subjectID = $_POST['subjectId'];
        $tplID = $_POST['TplID'];
        if($tplID == ''){
            $where='UserName="'.$userName.'" and SubjectID='.$subjectID;
            $resultTotal = $this->getModel('CaseTpl')->selectData(
                'TplID',
                $where
            );
            if(count($resultTotal)>=$power){
                $this->setError('30834',1,'',count($resultTotal));
            }
        }
    }

    /**
     * 根据试题ID，检测试题是否可以提供下载
     * @param string $testid 试题ID
     * @return array
     * @author demo
     */
    private function homeCheckLimitDown($testid){
        $testIdStr = \Test\Model\TestQueryModel::query(array(
            'field' => array('testid','typeid','testnum'),
            'convert' => 'testid',
            'where' => array('UserID'=>$this->getCookieUserID()),
            'page' => array('page'=>1,'perpage'=>100,'limit'=>100),
            'ids' => $testid
        ));
        $checkResult=array();
        $checkResult[0]=1;
        $docType=SS('docType');
        $testMsg=$testIdStr['Base'][0];
        $testNum=0;
        //根据POST过来的试题排序顺序处理
        $testArr=explode(',',$testid);   //题序
        foreach($testArr as $i=>$iTestArr){
            foreach($testMsg as $j=>$jTestMsg){
                $orderList[$iTestArr]=$testMsg[$iTestArr];
            }
        }

        foreach($orderList as $i=>$iTestMsg){
            if($iTestMsg['testnum']!=1){
                $start=$testNum+1;
                $testNum+=$iTestMsg['testnum'];
                $end=$testNum;
                $orderList[$i]['testNum']=$start.'-'.$end;

            }else{
                $testNum+=1;
                $orderList[$i]['testNum']=$testNum;
            }

            //LimitDown =0 不允许被下载
            if($docType[$iTestMsg['typeid']]['LimitDown']=='0'){   //改文档类型，是否是限制下载的
                $checkResult[0]=0;    //不能被下载
                //不能被下载的试题数组
                $checkResult[1][$iTestMsg['testid']][0]=$docType[$iTestMsg['typeid']]['TypeName'];
                $checkResult[1][$iTestMsg['testid']][1]=$docType[$iTestMsg['typeid']]['TypeID'];
                $checkResult[1][$iTestMsg['testid']][2]=$orderList[$i]['testNum'];
                $checkResult[1][$iTestMsg['testid']][3]=$orderList[$i]['testnum'];
            }
        }
        if(!empty($checkResult[1])){
            $list=$checkResult[1];
            foreach($list as $i=>$iList){
                $newList[$iList[1]]['Num'][]=$iList[2];
                $newList[$iList[1]]['TypeName']=$iList[0];
            }
            foreach($newList as $i=>$iNewList){
                $newList[$i]['TestNum']=implode('、',$newList[$i]['Num']);
            }
            $checkResult[1]=$newList;
        }
        return $checkResult;

    }

    /**
     * 根据试题ID，检测试题是否可以提供下载
     * @param string $testid 试题ID
     * @return array
     * @author demo
     */
    private function homeCheckSingleDown($rules,$userID){
        $where='UserID='.$userID.' and ';
        $limit=$rules['value'].'题';
        switch($rules['unit']){
            case 10:
                $limit.='/天';
                $where.=' AddTime>'.strtotime(date('Y-m-d',time()));
                break;
            case 20:
                $limit.='/周';
                $where.=' AddTime>'.strtotime("this Monday");
                break;
            case 30:
                $limit.='/月';
                $where.=' AddTime>'.strtotime(date('Y-m',time()));
                break;
            case 40:
                $limit.='/年';
                $where.=' AddTime>'.strtotime(date('Y',time()));
                break;
        }
        $logTestDown=$this->getModel('LogTestDown');
        $countLog=$logTestDown->selectCount($where,'LogID');
        if($countLog>=$rules['value']){
            header("Content-type: text/html; charset=utf-8");
            exit('您的试题下载超过限制('.$limit.')，<a href="/">返回首页</a>');
        }
    }


    public function aatPower(){
        $userName = $this->getCookieUserName();
        $userCode = $this->getCookieCode();
        $loginCheck = $this->commonCheckLogin($userName, $userCode, $type='code',$whoIs=0);
        if ($loginCheck !== 1) {
            $errorMsg = C('ERROR_'.$loginCheck);
            if (!IS_GET) {
                // $this->setError(['data'=>null,'info'=>$errorMsg,'status'=>0]);
                $this->setError($errorMsg);
            } else {
                $this->setCookieIndexMsg($errorMsg);
                $url = $_SERVER['REQUEST_URI'];
                $this->passport($url);
                // $url = preg_replace('/\.html$/', '', $url);
                // if(strpos($url, '/') !== 0){
                //     $url = '/'.$url;
                // }
                // $this->redirect('User/Index/passport',array('url'=>urlencode($url)));
            }
        }
    }

    public function aatApiPower(){
        $userName = $_POST['userName']?$_POST['userName']:$_POST['UserName'];
		if($_REQUEST['password']){
            $password = $_POST['password'];
            $loginCheck = $this->commonCheckLogin($userName, $password, $type='password',$whoIs=0);
        }else{
            $userCode = $_POST['userCode'];
            $loginCheck = $this->commonCheckLogin($userName, $userCode, $type='code',$whoIs=0);
        }
        //$userCode = $_POST['userCode'];
        //$loginCheck = $this->commonCheckLogin($userName, $userCode, $type='code',$whoIs=0);
        if ($loginCheck !== 1) {
            // $this->ajaxReturn(['data'=>null, 'info'=>C('ERROR_'.$loginCheck), 'status'=>0]);
            $this->setError($loginCheck);
        }
    }

    public function teacherPower($nowAction){
        $checkStatus=$this->checkLogin('Teacher',1);
        if(is_numeric($checkStatus)){
            $this->passport('Teacher/Index/index');
            // if($nowAction=='Teacher/Index/index'){
            //     header('Location:'.U('Teacher/Public/login'));
            // }
            // $flag = IS_AJAX?1:0;
            // $this->setError($checkStatus,$flag,$url=U('Teacher/Public/login'));
        }
        $this->mySubject=$checkStatus[1][0]['SubjectStyle'];
        if($nowAction!='Teacher/Index/index'){
            $this->teacherVerifyTeaPower();
        }
    }

    /**
     * 检查用户权限 验证失败则提示并跳转到登陆界面 验证成功则返回用户个人信息数组
     * @return array 1登录 否则返回错误码
     * @author demo
     * */
    private function teacherCheckLogin() {
        $userName=$this->getCookieUserName();
        $savecode=$this->getCookieCode();
        if(!$userName || !$savecode){
            return 30205;
        }
        //验证用户信息正确性
        $buffer=$this->getModel('User')->selectData(
            'UserID,UserName,SubjectStyle,SaveCode,Status,Whois,LastIP',
            'UserName="' . $userName . '"'
        );
        $this->mySubject = $buffer[0]['SubjectStyle'];
        if($buffer[0]['Status']!=0) return 30203;
        if($buffer[0]['Whois']!=1) return 30206;
        $time=C('WLN_COOKIE_TIMEOUT');
        $code=md5($buffer[0]['UserID'].$buffer[0]['UserName'].$buffer[0]['SaveCode'].ceil(time()/$time));
        $code1=md5($buffer[0]['UserID'].$buffer[0]['UserName'].$buffer[0]['SaveCode'].(ceil(time()/$time)-1));
        if($savecode!=$code && $savecode!=$code1){
            $this->setCookieUserName(null);
            $this->setCookieCode(null);
            return 30205;
        }
        $cookieUserID = $this->getCookieUserID();//getCookieUserID要兼容手机端的
        if($cookieUserID){
            //如果使用了UserID
            if($cookieUserID!==$buffer[0]['UserID']){
                return 30205;
            }
        }
        //验证用户的ip切换
        if(!C('WLN_NOT_CHECK_LOGIN_IP')){
            if($buffer[0]['LastIP']!=get_client_ip(0,true)) return 30205;
        }
        $this->setCookieUserName($userName,$time);
        $this->setCookieUserID($buffer[0]['UserID'] ,$time);
        $this->setCookieCode($code,$time);
        return 1;
    }

    /**
     * 权限验证
     * @return boolean 有无当前操作权限
     * @author demo
     * @date 2014年11月16日
     */
    private function teacherVerifyTeaPower(){
        //获取请求信息
        $module = MODULE_NAME;
        $action=CONTROLLER_NAME;
        $function=ACTION_NAME;
        if($action=='') $action='Index';
        if($function=='') $function='index';
        $object=$module.'/'.$action.'/'.$function;
        if($function=='getData'){
            return true;
        }
        $powlstr = $this->teacherPublicInfo();//调用得到权限列表ID字符串，查所对应的操作
        $powlarr = explode(',',$powlstr);
        //获取用户权限信息
        $powerUserList=SS('powerUserList');
        if($powerUserList[$object]['Value']=='all'){
            $ListID = $powerUserList[$object]['ListID'];
        }
        //并根据传递过来的用户名和操作对象名称进行比对，判断是否有操作权限，如果比对成功则返回true否则返回false
        if(in_array($ListID, $powlarr)){
            return true;
        }else{
            if(IS_AJAX){
                $this->setError('30604',1);
            }else{
                $this->setError('30604');
            }
        };
    }

    /**
     * 公共数据(多个方法需要此处数据)
     * @return string 权限列表ID字符串
     * @author demo
     * @date 2014年11月16日
     */
    public function teacherPublicInfo($userName=''){
        if($userName=='') $userName=$this->getCookieUserName();

        //根据cookie得到用户名查用户ID
        $userBuffer = $this->getModel('User')->selectData(
            'UserID',
            'UserName="'.$userName.'"'
        );
        $userID = $userBuffer[0]['UserID'];
        //根据用户ID得到权限属组ID
        $powerUser=SS('powerUserGroup');
        $userGrBu = $this->getModel('UserGroup')->selectData(
            'GroupID',
            'GroupName=3 and UserID="'.$userID.'"'
        );
        foreach($userGrBu as $j=>$jUserGrBu){
            $powlarr[]=$powerUser[3]['groupList'][$jUserGrBu['GroupID']]['ListID'];
        }
        $powlstr = trim(implode(',',$powlarr), ',');
        return $powlstr;
    }
    /**
     * 描述：Manage分组登录及权限认证
     * @author demo
     */
    public function managePower(){
        $url=U('Manage/Public/login');
        $check = $this->checkLogin('Manage',1);

        //验证加密狗数据
        if (C('WLN_OPEN_SOFTKEY') == 1) {
            $data = explode('#$#', cookie('encData'));
            $rnd = $data[0];
            $keyID = $data[1];
            $encData = $data[2];
            $softkey = $this->getModel('SoftKey');
            $buffer = $softkey->checkData($keyID, $encData, $rnd, '', 1);
            if ($buffer != 1) {
                $ajax = 0;
                if (IS_AJAX) $ajax = 1;
                $this->setError($buffer, $ajax, $url);
            }
            $softkey->setCookieStr($keyID);
        }

        $nowAction=MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
		// dump($_COOKIE);die;
        if (is_numeric($check)) {
            if($nowAction=='Manage/Index/index'){
                header('Location:'.U('Manage/Public/login'));
            }

            $ajax = 0;
            if (IS_AJAX) $ajax = 1;
            $this->setError($check, $ajax, $url);
        }

        $userDb = $check[1];

        //密码强度弱跳转到密码修改 0为弱 1 为强echo $_SERVER["QUERY_STRING"]
        if($userDb[0]['IfWeak']==0 && !in_array($nowAction,['Manage/Public/menu','Manage/Public/top','Manage/Public/password'])){
            header('Location:'.U('Manage/Public/password'));
        }

        if($nowAction!='Manage/Index/index'){
            $this->manageVerifyPower($userDb);
        }
    }

    /**
     * 验证学科权限是否异常
     * @param int|array $subjectID 学科id
     * @param int $admin 用户名
     * @param int $ajax是否是ajax 0不是 1是
     * @author demo
     */
    public function powerCheckSubject($subjectID,$admin,$ajax=0){
        if(is_numeric($subjectID)) $subjectID=array($subjectID);
        $my=explode(',',$this->mySubject);
        if($this->ifSubject && $this->mySubject){
            foreach($subjectID as $iSubjectID){
                if(!in_array($iSubjectID,$my)){
                    $this->setError('30712',$ajax); //您不能添加非所属学科文档！
                }
            }
        }else if($this->ifDiff && $admin!=''){
            //判断是否可以编辑
            if ($admin != $this->getCookieUserName()) {
                $this->setError('30826',$ajax); //您没有权限编辑！
            }
        }
    }

    /**
     * 检查用户登录
     * @return array [状态，用户DB数据] 状态为1登录，否则错误码
     * @author demo
     */
    private function manageCheckLogin(){
        $adminName=$this->getCookieUserName();
        $userMsg=$this->getModel('Admin')->selectData(
            '*',
            "AdminName='".$adminName."'"
        );
        $saveCode=$this->getCookieCode();
        if(!$userMsg[0]['AdminName'] || !$saveCode){
            return [30205,$userMsg];
        }
        $buffer=$userMsg;
        //验证用户信息正确性
        if($buffer[0]['Status']!=0) return [30203,$userMsg];
        $time=C('WLN_COOKIE_TIMEOUT');
        $code=md5($buffer[0]['AdminID'].$buffer[0]['AdminName'].$buffer[0]['SaveCode'].ceil(time()/$time));
        $code1=md5($buffer[0]['AdminID'].$buffer[0]['AdminName'].$buffer[0]['SaveCode'].(ceil(time()/$time)-1));
        if($saveCode!=$code && $saveCode!=$code1){
            $this->setCookieUserName(null);
            $this->setCookieCode(null);
            return [30205,$userMsg];
        }
        $cookieUserID = $this->getCookieUserID();//getCookieUserID要兼容手机端的
        if($cookieUserID){
            //如果使用了UserID
            if($cookieUserID!==$userMsg[0]['AdminID']){
                return [30205,$userMsg];
            }
        }
        //验证用户的ip切换
        if(!C('WLN_NOT_CHECK_LOGIN_IP')){
            if($buffer[0]['LastIP']!=get_client_ip(0,true)) return [30205,$userMsg];
        }
        $this->setCookieUserName($buffer[0]['AdminName'],$time);
        $this->setCookieCode($code ,$time);
        $this->setCookieUserID($userMsg[0]['AdminID'] ,$time);
        return [1,$buffer];
    }
    /**
     * 验证用户权限
     * @param $userMsg array 用户信息数据
     * @return bool
     * @author demo
     */
    private function manageVerifyPower($userMsg){
        //获取请求信息
        $module=MODULE_NAME;
        $action=CONTROLLER_NAME;
        $function=ACTION_NAME;
        if($module=='') $module='Manage';
        if($action=='') $action='Index';
        if($function=='') $function='index';
        $object=$module.'/'.$action.'/'.$function;
        if($function=='getData'){
            return true;
        }
        //首先要获取用户所在的权限组，然后找到这个权限组所对应的操作列表，
        //并根据传递过来的用户名和操作对象名称进行比对，如果比对成功则返回true否则返回false
        //获取用户权限信息
        $buffer=$userMsg;
        $powerTree=SS('powerAdmin');
        $power=$powerTree[$buffer[0]['GroupID']]['ListID'];
        $subjectID=$buffer[0]['MySubject'];
        if($power=='all'){
            return true;
        }
        $powerListTag=SS('powerAdminListTag');
        $allUser=$powerListTag['allUser'];
        $powerList=$powerTree[$buffer[0]['GroupID']]['sub'];
        if($powerList){
            $powerArray = explode(',',$power);
            foreach($powerList as $iPowerTag){
                $arr[]=$iPowerTag['PowerTag'];
                if($iPowerTag['IfSubject']=='1' && $object==$iPowerTag['PowerTag']){
                    $this->ifSubject=1;
                    $this->mySubject=$subjectID;
                }

                if($iPowerTag['IfDiff']=='1' && $object==$iPowerTag['PowerTag'] && !in_array($allUser['ListID'],$powerArray)){
                    $this->ifDiff=1;
                }
            }
            if(in_array($allUser['ListID'],$powerArray)){
                $this->ifDiff=0;
            }
            if($subjectID == 'all'){
                $this->ifSubject=0;
            }
            //判断是否有操作权限
            if(in_array($object,$arr)){
                return true;
            }
        }
        $ajax=0;
        if(IS_AJAX) $ajax=1;
        $this->setError('30604',$ajax);
    }


    /**
     * 描述：Exam分组登录及权限认证
     * @author demo
     */
    public function examPower(){
        $checkStatus=$this->checkLogin('Exam',1);
        if(is_numeric($checkStatus)){
            $flag=0;
            if(IS_AJAX){
                $flag=1;
            }
            $this->setError($checkStatus,$flag,U('Exam/Index/login'));
        }
    }

    /**
     * 描述：Exam分组验证用户登录
     * @param int $flag 返回类型 1返回数字 0提示并跳转
     * @return int|none    1 已登录 否则返回错误码
     * @author demo
     */
    public function examCheckLogin($flag=0){
        $loginUrl = U('Exam/Index/index');
        $userID=$this->getCookieUserID();

        if($userID==''){
            if($flag) return '30205';
            else $this->setError('30205',0,$loginUrl);
        }

        $buffer = $this->getModel('ReserveUser')->selectData(
            'UserID,UserName,SaveCode',
            'UserID="'.$userID.'"',
            '',
            ''
        );

        $thisUserID=$buffer[0]['UserID'];
        $thisUserName=$buffer[0]['UserName'];
        $thiaSaveCode=$buffer[0]['SaveCode'];

        $oldCode=$this->getCookieCode();
        $time=C('WLN_COOKIE_TIMEOUT');
        $code=md5($thisUserID.$thisUserName.$thiaSaveCode.ceil(time()/$time));
        $code1=md5($thisUserID.$thisUserName.$thiaSaveCode.(ceil(time()/$time)-1));

        if($oldCode!=$code && $oldCode!=$code1){
            if($flag) return '30205';
            else $this->setError('30205',0,$loginUrl);
        }
        if($oldCode!=$code){
            $time=$this->getCookieTime();
            if($time>7*24*3600 || $time<=24*3600 || !is_numeric($time)){
                $this->setCookieUserID(NULL);
                $this->setCookieUserName(NULL);
                $this->setCookieTime(NULL);
                $this->setCookieCode(NULL);
                if($flag) return '30205';
                else $this->setError('30205',0,$loginUrl);
            }
            $time=$time-24*3600;
            $this->setCookieUserID($thisUserID,$time);
            $this->setCookieUserName($thisUserName,$time);
            $this->setCookieTime($time,$time);
            $this->setCookieCode($code,$time);
        }
        return 1;
    }

    /**
     * 检查用户登录情况
     * 【注意】：此函数不做登录逻辑的用户错误提醒，请在JS和对应action中自行判断
     * @param string $userName 用户名
     * @param string $key 验证字段 userCode或者password
     * @param string $type 取值code或password
     * @param int $whoIs 用户角色
     * @return int 如果错误返回错误码，成功返回1
     * @author demo
     */
    public function commonCheckLogin($userName, $key, $type, $whoIs){
        if (!$userName|| !$key) {
            return 30835;//请登录！下面转入登录页面。
        }
        $db = $this->getModel('User')->getInfoByWhere(
            'UserID,UserName,Password,Email,SaveCode,Status,Whois',
            ['UserName' => $userName]
        );
        if (!$db) {
            return 30214;//用户不存在
        }
        $user = $db[0];
        if ($type != 'code' && $type != 'password') {
            return 30304;//无效数据！
        }
        $authTimeout = $_POST['phone']?3600*24*365:C('WLN_COOKIE_TIMEOUT');
        $code=md5($user['UserID'] . $user['UserName'] . $user['SaveCode'].ceil(time()/$authTimeout));
        $code1=md5($user['UserID'] . $user['UserName'] . $user['SaveCode'].(ceil(time()/$authTimeout)-1));

        if ($type == 'code' && $key != $code && $key != $code1) {
            return 30204;//您填写的账户或密码不正确！
        } elseif ($type == 'password' && $user['Password'] != md5($user['UserName'] . $key)) {
            return 30204;//您填写的账户或密码不正确！
        }
        $cookieUserID = $this->getCookieUserID();//getCookieUserID要兼容手机端的
        if($type == 'code'&&$cookieUserID){
            //如果使用了UserID
            if($cookieUserID!==$user['UserID']){
                return 30835;
            }
        }
        if ($user['Status'] == 1) {
            return 30203;//您的账号已被锁定，请联系管理员！
        }
        if ($user['Whois'] != $whoIs) {
            $whoIsArray = [
                0 => 30229,
                1 => 30230,
                2 => 30231,
                3 => 30232,
            ];
            return $whoIsArray[$whoIs];
        }
        //没有错误，正常登录
        return 1;
    }

    /**
     * 验证用户是否登录
     * @author demo
     * @date 2015-6-6
     */
    public function homeIsLoginCheck(){
        if($this->checkLogin('Home',1)[0]==1){
            if(IS_AJAX) $this->setBack('success');
            else return 'success';
        }
        if(IS_AJAX) $this->setError('30205', 1);
        return 'error';
    }
    /**
     * 官网验证登录
     * @param int $flag 返回数据方式
     * @param int $style 验证方式 1只验证教师，2教师和学生都验证
     * @return mixed
     * @author demo
     */
    public function indexCheckLogin($flag=0,$style){
        //验证教师
        $result = $this->checkLogin('Home',$flag);

        if($result[0] == 1){//如果是登录，则返回已登录标识和身份标识
            return array($result,'teacher');
        }
        //验证学生,
        //默认教师信息优先显示
        //在教师用户登录的情况下不需要查询学生是否登录
        if($style == 2 && $result[0]!=1){
            $userName = $this->getCookieUserName('Aat');
            $userCode = $this->getCookieCode('Aat');
            $result = $this->commonCheckLogin($userName,$userCode,'code',0);
            if($result  == 1){
                return array($result,'student');
            }
        }
        return $result;
    }

    /**
     * 未登录则跳转至通行证登录页面
     * @param string $url 默认为空时，指定当前的url地址
     * @author demo
     */
    private function passport($url=''){
        if(empty($url)){
            $url = MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        }
        $url = preg_replace('/\.html$/', '', $url);
        $url = ltrim($url, '/');
        $url = U('/User/Index/passport', array('url' => urlencode($url)));
        if(IS_AJAX){
            $this->setError('30835', 1, $url);
        }else{
            header("location:{$url}");
        }
        exit;
    }
}