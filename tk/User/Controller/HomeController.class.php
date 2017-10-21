<?php
/**
 * @author demo
 * @date 2014年11月3日
 */
/**
 * 用户控制器类，用于用户档案操作
 */
namespace User\Controller;
use Common\Controller\DefaultController;
class HomeController extends DefaultController {

    /**
     * 组卷用户中心首页显示
     * @author demo
     */
    public function info(){
        $buffer=$this->getModel('User')->selectData(
            '*',
            'UserID="'.$this->getCookieUserID().'"'
        );
        //用户组权限
        $powerUserArr = SS('powerUserId');
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

        //查询areaid下的area数据 和 school数据
        if($buffer[0]['AreaID']){
            $school=$this->getModel('School');
            $grade=SS('grade');
            $area=$this->getModel('Area');
            $subject=SS('subject');;
            $buffer[0]['SubjectName']=$subject[$buffer[0]['SubjectStyle']]['SubjectName'];
            $tmpArray=$school->getSchoolById($buffer[0]['SchoolID']);
            if($tmpArray) $buffer[0]['SchoolName']=$tmpArray['SchoolName'];
            $tmpArray=$grade[$buffer[0]['GradeID']]['GradeName'];
            if($tmpArray) $buffer[0]['GradeName']=$tmpArray;
            $tmpStr=$area->getAreaPathById($buffer[0]['AreaID']);
            if($tmpStr) $buffer[0]['AreaStr']=$tmpStr;
            //初始化地区
            $buffer[0]['InitArea']=$area->areaSelectByID($buffer[0]['AreaID']);

            unset($school);
            unset($grade);
            unset($area);
            unset($tmpArray);
        }
        //查询上次登录信息
        $bufferLog=$this->getModel('Log')->selectData(
            '*',
            'Module="用户登录" and IfAdmin=0 and UserName="'.$this->getCookieUserName().'"',
            'LogID desc',
            5
        );
        //去除退出日志的干扰
        foreach($bufferLog as $iBufferLog){
            if(stristr($iBufferLog['Content'],'登录')){
                $loginLogTrue[]=$iBufferLog;
            }
        }
        //查找父类id
        $areaParent=$this->getModel('Area')->getAreaStr($buffer[0]['AreaID']);
        //初始化学校
        $schoolList=$this->getModel('School')->selectData('SchoolName,SchoolID','AreaID='.$buffer[0]['AreaID']);
        $bufferx=SS('areaChildList');  // 缓存子类list数据
        $buffer[0]['AreaName']=$bufferx[0];//获取省份
        $gradeArray = $this->getData(array('style'=>'getSingle','cacheName'=>'gradeListSubject'));//年级
        $subjectArray = $this->getData(array('style'=>'getGradeSubject','gradeID'=>$buffer[0]['GradeID']));//学科
        /*载入模板标签*/
        $this->assign('areaParent', $areaParent); //父类数据
        $buffer[0]['LastIP']=$loginLogTrue[1]['IP'];
        if(empty($loginLogTrue[1]['LoadDate'])) $loginLogTrue[1]['LoadDate']=time();
        $buffer[0]['LastTime']=date('Y-m-d H:i:s',$loginLogTrue[1]['LoadDate']);
        $nowIP=ip2long(get_client_ip(0,true));
        $ipMsg=$this->getModel('UserIp')->selectData(
            'PUID',
            'IPAddress='.$nowIP.' and LastTime>'.time()
        );
        if($ipMsg){
            $homePower=SS('powerUserGroup')[1];
            $thisGroup=explode(',',$ipMsg[0]['PUID']);
            $buffer[0]['nowPowerName']=$homePower['groupList'][$thisGroup[0]]['UserGroup'];
        }
        $tmp = array('未认证','认证中','已认证','认证未通过');
        $buffer[0]['authTitle'] = $tmp[$buffer[0]['IfAuth']];
        //认证中 未通过 显示最后一次认证信息
        if($buffer[0]['IfAuth'] == 1 || $buffer[0]['IfAuth'] ==3){
            $authData = $this->getModel('TeacherAuthinfo')->getList('IDNumber,UserID,Qualification,QuaPicSrc,Grade,GradePicSrc,AuthTime', 'UserID = '.$buffer[0]['UserID'],'ID DESC','1')[0];
            $this->assign('authData',$authData);
        }
        $buffer[0]['okName']=R('Common/UserLayer/showUserName',array($buffer[0]['UserName'],$buffer[0]['RealName'],$buffer[0]['Whois']));
        //等级相关信息
        $userLevelMsg=$this->getModel('UserLevel')->getLevelMsg($this->getCookieUserName(),1);
        //升级经验差值
        $userLevelMsg['expCha']=$userLevelMsg[0]['LevelExpMax']-$buffer[0]['ExpNum'];
        //经验条百分比
        $userLevelMsg['baifen']=($buffer[0]['ExpNum']/$userLevelMsg[0]['LevelExpMax'])*100;
        $this->assign('user', $buffer[0]); //用户信息
        $this->assign('arrArea',$buffer[0]['AreaName']);
        $this->assign('levelMsg',$userLevelMsg); //等级相关信息
        $this->assign('schoolList',$schoolList); //学校内容
        $this->assign('gradeList',$gradeArray); //年级内容
        $this->assign('subjectArray',$subjectArray); //年级内容
        $this->display();
    }

    /**
     * 用户权限
     * @author demo
     */
    public function userPower(){
        $userID=$_POST['uid'];//用户ID
        //用户组权限
        $powerUserArr = SS('powerUserId');
        //GroupName为分组权限，1代表组卷，2代表提分
        foreach($powerUserArr as $i=>$iPowerUserArr){
            if($iPowerUserArr['IfDefault']=='1' && $iPowerUserArr['GroupName']=='1'){
                $defaultGroup=$iPowerUserArr;//默认用户组
            }
            if($iPowerUserArr['ListID']=='all'){
                $allPowerArr = $iPowerUserArr['PUID'];//特殊用户组ID
            }
        }
        //获取当前用户权限
        $userGroupArr = $this->getModel('UserGroup')->selectData(
            'GroupName,GroupID,LastTime',
            'UserID='.$userID.' AND GroupName=1',
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
                $groupInfo[0]=$iPowerUserArr;
            }
        }
        if($groupInfo[0]['ListID']=='all'){
            $buffer[0]['UserPower'] = 'all';
            $buffer[0]['UserGroup'] = $groupInfo[0]['UserGroup'];
        }else{
            $ip = ip2long(get_client_ip(0,true));
            $ipInfo = $this->getModel('UserIp')->selectData(
                'LastTime,PUID',
                'IPAddress='.$ip
            );
            $ipInfoArr = explode(',',$ipInfo[0]['PUID']);
            $ipInfo[0]['PUID'] = $ipInfoArr[0];
            $powerUserListId = SS('powerUserByID');
            if($ipInfo && $ipInfo[0]['LastTime']>time()){
                $powerList = $powerUserArr[$ipInfo[0]['PUID']];
                $allArr = array_merge(explode(',',$groupInfo[0]['ListID']),explode(',',$powerList['ListID']));
                $allArr = array_unique($allArr);
                foreach($powerUserListId as $i=>$iPowerUserListId){
                    if(in_array($i,$allArr)){
                        $powerInfo[$i] = $iPowerUserListId;
                    }
                }
                foreach($powerInfo as $i=>$iPowerInfo){
                    $tmpArr[$iPowerInfo['PowerTag']][] = $iPowerInfo;
                }
                $resultArr = array();//权限数组
                $order = 0;
                foreach($tmpArr as $i=>$iTmpArr){
                    $compare[0]=$iTmpArr[0]['Value'];
                    $compare[1]=$iTmpArr[1]['Value'];
                    if(in_array('all',$compare)){
                        $resultArr[$order]['Value'] = 'all';
                    }elseif(in_array('',$compare)){
                        $compare[0]==''?$resultArr[$order]['Value']=$compare[1]:$resultArr[$order]['Value']=$compare[0];
                    }else{
                        $resultArr[$order]['Value']=max($compare[0],$compare[1]);
                    }
                    $resultArr[$order]['PowerTag']=$i;
                    $resultArr[$order]['PowerName']=$iTmpArr[0]['PowerName'];
                    $order++;
                }
                $buffer[0]['UserGroup'] = $groupInfo[0]['UserGroup'];
                $buffer[0]['IpUserGroup'] = $powerList['UserGroup'];
                $buffer[0]['UserPower'] = $resultArr;
            }else{
                $idList = array();//权限名数组
                foreach($powerUserListId as $i=>$iPowerUserListId){
                    if(in_array($i,explode(',',$groupInfo[0]['ListID']))){
                        $idList[$i] = $iPowerUserListId;
                    }
                }
                $buffer[0]['UserGroup'] = $groupInfo[0]['UserGroup'];
                $buffer[0]['UserPower'] = $idList;
            }
            foreach($buffer[0]['UserPower'] as $i=>$iBuffer){
                if($iBuffer['Value']=='0'){
                    $buffer[0]['UserPower'][$i]['Value'] = '不可用';
                }elseif($iBuffer['Value']=='all'){
                    $buffer[0]['UserPower'][$i]['Value'] = '无限制';
                }elseif($iBuffer['PowerTag']=='Dir/Index/getTemplateList'&&$iBuffer['Value']=='1'){
                    $buffer[0]['UserPower'][$i]['Value'] = '不可用';
                }elseif($iBuffer['PowerTag']=='Dir/Index/getTemplateList'&&$iBuffer['Value']=='2'){
                    $buffer[0]['UserPower'][$i]['Value'] = '可用';
                }
                if($iBuffer['Unit']){
                    $unit = $iBuffer['Unit'];
                    $buffer[0]['UserPower'][$i]['Value'] .= '【'.\Common\Model\PowerUserListModel::$powerCycle[$unit].'】';
                }
            }
        }
        foreach($buffer[0]['UserPower'] as $i=>$iBuffer){
            if(strlen($buffer[0]['UserPower'][$i]['PowerName'])>18){
                $buffer[0]['UserPower'][$i]['PowerName'] = mb_substr($buffer[0]['UserPower'][$i]['PowerName'],0,6,'utf-8').'***';
            }
        }
        $this->setBack($buffer[0]);
    }

    /**
     * 任务列表
     * @author demo
     */
    public function myTask(){
        $this->assign('level', $this->getModel('MissionHallTasks')->getLevel());
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
        $params['field'] = array('l.MHLID,t.Title, t.Url,l.AddTime as recordAddTime,l.Status');
        $params['uid'] = $this->getCookieUserID();
        $mhr = $this->getModel('MissionHallLog')->recordList($params);
        $output[0]=$mhr[0];
        $output[1]=$mhr[1];
        $output[2]=20;
        $this->setBack($output);
    }

    /**
     * changeInfo
     */
    public function changeInfo(){
        $realName=formatString('stripTags',$_POST['RealName']);
        $passwordy=$_POST['Passwordy'];
        $password=formatString('changeStr2Html',trim($_POST['Password']));
        $password2=formatString('changeStr2Html',$_POST['Password2']);
        $address=formatString('changeStr2Html',$_POST['Address']);
        $areaID=$_POST['AreaID'];
        $schoolID=$_POST['SchoolID'];
        $gradeID=$_POST['GradeID'];
        $subjectID=$_POST['SubjectID'];
        $err=array();
        $flag=0;
        $changePwd=0;
        if(strlen($realName)<1){
            $err[]='RealName|请填写正确的姓名!';
            $flag=1;
        }
        $user=$this->getModel('User');
        $username=$this->getCookieUserName();
        $saveCode=$this->getCookieCode();
        $buffer=$this->getModel('User')->selectData(
            '*',
            'UserName="'.$username.'"'
        );
        $data=array();
        $userID=$buffer[0]['UserID'];
        if($passwordy!=''){
            if(strlen($passwordy)<6){
                $err[]='Passwordy|请填写正确的原密码!';
                $flag=1;
            }else{
                if($buffer[0]['Password']!=md5($username.$passwordy)){
                    $err[]='Passwordy|请填写正确的原密码!';
                    $flag=1;
                }
            }
            if(strlen($password)<6 || strlen($password)>18){
                $err[]='Password|请填写6-18位新密码!';
                $flag=1;
            }
            if($password!=$password2){
                $err[]='Password2|两次输入的新密码不一致!';
                $flag=1;
            }
            $changePwd=1;
        }
        if($_POST['Nickname']){
            $nickname = formatString('stripTags',$_POST['Nickname']);
            if (strlen($nickname) < 3 || strlen($nickname) > 15 ) {
                $err[] = 'Nickname|请输入正确的昵称!';
                $flag = 1;
            }
            $data['Nickname']=$nickname;
        }
        if($_POST['Phonecode']) {//兼容补全信息弹框和用户中心的修改信息
            $phoneCode = formatString('stripTags',$_POST['Phonecode']);
            if (strlen($phoneCode) < 6 || strlen($phoneCode) > 15 || preg_replace('/[0-9\-]*/i', '', $phoneCode)) {
                $err[] = 'Phonecode|请输入正确的联系电话!';
                $flag = 1;
            }
            $data['Phonecode']=$phoneCode;
        }
        if($_POST['Email']) {//兼容补全信息弹框和用户中心的修改信息
            $email = formatString('stripTags',$_POST['Email']);
            if (!strstr($email, '@') || !strstr($email, '.')) {
                $err[] = 'Email|请输入正确的邮箱!';
                $flag = 1;
            }
            $data['Email']=$email;
        }
        $end=0;
//      if($_POST['messageCode']){//因送书活动导致补全信息而加入的手机短信验证
//          $messageCode=$_POST['messageCode'];
//          //验证手机验证码是否正确
//          $output=R('Common/UserLayer/checkPhoneCode',array($phoneCode,$messageCode,$this->getCookieUserID(),1));
//          if($output[0] == 1){
//              $error=C('ERROR_'.$output[1]);//兼容组卷端的错误码信息
//              $err[]='messageCode|'.$error;
//              $flag=1;
//          }
//          $end = strtotime('2016-11-12');//只有通过补全信息的方式才可以获得五年
//      }
        if(strlen($address)<5){
            $err[]='Address|请输入正确的地址!';
            $flag=1;
        }
        if($areaID<1 || !is_numeric($areaID)){
            $err[]='AreaID|请选择所在地区!';
            $flag=1;
        }
        if($schoolID<1 || !is_numeric($schoolID)){
            $err[]='SchoolID|请选择所在学校!';
            $flag=1;
        }
        if($gradeID<1 || !is_numeric($gradeID)){
            $err[]='GradeID|请选择所在年级!';
            $flag=1;
        }
        if($subjectID<1 || !is_numeric($subjectID)){
            $err[]='SubjectID|请选择所在学科!';
            $flag=1;
        }
        //检查用户名合法
        $backstr=$user->NameFilter($realName);
        if($backstr['errornum']!='success'){
            $backstr=$this->setError($backstr['errornum'],2,'',$backstr['replace']);
            $err[]='RealName|'.str_replace('error|','',$backstr);
            $flag=1;
        }
        if($flag==1){
            $this->setBack('error#@#'.implode('#$#',$err));
        }
        //验证信息是否重复
        $checkInfo = $user->checkUser('',$phoneCode,$email,$userID);
        if($checkInfo){
            $this->setError($checkInfo,1);
        }
        $data['RealName']=$realName;
        if($changePwd){
            $data['Password']=md5($username.$password);
            $saveCode=$user->saveCode();
            $data['SaveCode'] = $saveCode;
        }

        $data['Address']=$address;
        $data['AreaID']=$areaID;
        $data['SchoolID']=$schoolID;
        $data['GradeID']=$gradeID;
        $data['SubjectStyle']=$subjectID;
        $data['UserID']=$userID;

        if($this->getModel('User')->updateData(
                $data,
                'UserID='.$userID)===false){
            $this->setError('30310',1);
        }
        if($changePwd){
            $time=C('WLN_COOKIE_TIMEOUT');
            $code=md5($buffer[0]['UserID'].$username.$saveCode.ceil(time()/$time));
            cookie(C('WLN_HOME_USER_AUTH_KEY') . '_USER',$username,$time);
            cookie(C('WLN_HOME_USER_AUTH_KEY') . '_TIME',$time,$time);
            cookie(C('WLN_HOME_USER_AUTH_KEY') . '_CODE',$code ,$time);
        }
        //补全信息活动 
        $current = time();
        $result = $this->getModel('UserGroup')->selectData(
            'GroupID,LastTime',
            "UserID={$userID} AND GroupName=1"
        );
        if($buffer[0]['LoadDate'] <= $end ){
            $lastTime = strtotime("+2 month", strtotime(date('Y-m-d',$current)));
            if(empty($result) || $result[0]['LastTime'] < $lastTime){
                $config = array(
                    'GroupID'=>44,
                    'LastTime'=>$lastTime
                );
                $this->getModel("UserGroup")->saveDefaultGroup($userID, $config);
            }
        }
        $this->setBack('success');
    }

    public function testSave(){
        $this->display();
    }

    public function message(){
        $this->display();
    }

    public function docSave(){
        $this->display();
    }

    public function down(){
        $this->display();
    }


    /**
     * 获取用户收藏目录，用于用户收藏页面列表
     * @author demo
     * @date 2014年9月20日
     */
    public function userCatalog(){
        $username = $this->getCookieUserName();
        $subject = $_POST['subjectID'];
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
     * 保存目录
     * @author demo
     * @date  2014年9月20日
     */

    public function saveCatalog(){
        $username = $this->getCookieUserName();
        $subject = cookie('SubjectId');
        $data['CatalogName']=formatString('changeStr2Html',$_POST['catalogName']);
        $data['FatherID']   =$_POST['fatherID'];
        $data['UserName']   =$username;
        $data['AddTime']    =time();
        $data['OrderID']    =$_POST['OrderID'];
        $data['SubjectID']  =$subject;
        $buffer=$this->getModel('UserCatalog')->insertData(
            $data
        );
        if($buffer==false){
            $this->setError('1X406',1);
        }else{
            $this->setBack('success|'.$buffer.'|'.formatString('stripTags',$_POST['catalogName']).'|'.$_POST['fatherID']);
        }
    }

    /**
     * 获取一级目录，且该目录下无试题，用于添加目录
     * @author demo
     * @date 2014年9月20日
     */
    public function getUserCatalog(){
        $catalogID=$_POST['fid'];
        $username = $this->getCookieUserName();
        $subject=cookie('SubjectId');
        $catalog=$this->getModel('UserCatalog');
        $where='UserName="'.$username.'" and FatherID=0 and CatalogID !='.$catalogID.' and SubjectID='.$subject;
        $field='CatalogID,CatalogName';
        $catalogList=$catalog->getCatalogList($field,$where);
        if($catalogList){
            foreach($catalogList as $i=>$cataList){
                //查找各个目录下的试题
                $buffer=$this->getModel('UserCollect')->selectData(
                    '*',
                    'UserName="'.$username.'" and SubjectID="'.$subject.'" and CatalogID="'.$cataList['CatalogID'].'"'
                );
                if(count($buffer)!=0){
                    unset($catalogList[$i]);//如果有则清除该目录
                }
            }
        }
        $this->setBack($catalogList);
    }

   /**
    * 获取目录名称，用于修改目录名称
    * @author demo
    * @date 2014年9月20日
    */
    public function getCatalogName(){
        $catalogID=$_POST['catalogID'];
        //$subject=cookie('SubjectId');//未使用
        $catalogList=$this->getModel('UserCatalog');
        $where='CatalogID='.$catalogID;
        $field='CatalogID,CatalogName';
        $buffer=$catalogList->getCatalogList($field,$where);
        if(!$buffer) $this->setError('1X407',1);
        $catalogName=$buffer[0]['CatalogName'];
        $catalogID=$buffer[0]['CatalogID'];
        $output=array();
        $output[0]='success';
        $output[1]=$catalogName;
        $output[2]=$catalogID;
        $this->setBack($output);
    }

    /**
     * 修改目录名称
     * @author demo
     * @date 2014年9月20日
     * 未应用
     */
    public function setCatalogName(){
        $catalogID=$_POST['catalogID'];
        $catalogName=$_POST['catalogName'];
        if(!$catalogID){
            $this->setError('1X408',1);
        }
        if($catalogName===''){
            $this->setError('1X409',1);
        }
        $data['CatalogName']=$catalogName;
        $where='CatalogID='.$catalogID;
        if(($buffer=$this->getModel('UserCatalog')->updateData($data,$where))===false){
            $this->setError('1X410',1);
        }else{
            $this->setBack('success#$#'.$catalogName);
        }
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
            $this->setError('1X411',1);
        }elseif($cataDe===false){
            $this->setError('1X412',1);
        }elseif($cataDe===false && $buffer===false){
            $this->setError('30302',1);
        }else{
            $this->setBack('success');
        }
    }

    /**
     * 取可添加内容的目录,用于试题收藏
     * @author demo
     * @date 2014年9月20日
     *
     */
    public function getCanUseCata(){
        $catalog=$this->getModel('UserCatalog');
        $username = $this->getCookieUserName();
        $subject = cookie('SubjectId');
        $data='UserName="'.$username.'" and FatherID=0 and SubjectID='.$subject;
        $field="CatalogID,CatalogName,FatherID";
        $buffer=$catalog->getCatalogList($field,$data);//获取第一级目录
        if($buffer){
            foreach($buffer as $i=> $canUseCata){
                $newData='UserName="'.$username.'" and FatherID='.$canUseCata['CatalogID'];
                $newBuffer=$catalog->getCatalogList($field,$newData);//查找该目录下是否有子目录
                if($newBuffer){
                    unset($buffer[$i]);                              //如果有，则清除
                    $buffer[$i]['deep']=$newBuffer;                  //替换成该目录下的子目录
                }
            }
        }
        $this->setBack($buffer);
    }

    /**
     * 发送短信验证码
     * @author demo
     */
    public function sendPhoneCode(){
        $phoneNum=$_POST['phoneNum'];
        $imgCode =$_POST['imgCode'];

        if(md5($imgCode) != session('verify')){
            $this->setError('30101',1);//验证码有误！
        }
        $cookieUid = $this->getCookieUserID();
        //验证是否有userid
        if(empty($cookieUid)){
            $this->homeIsLoginCheck();
        }

        $output=R('Common/UserLayer/sendPhoneCode',array($phoneNum,$imgCode,$cookieUid));

        //输出错误信息
        if($output[0] == 1){
            $this->setError($output[1],1);
        }

        $this->setBack('success');
    }

    /**
     * 验证短信验证码
     * @author demo
     */
    public function checkPhoneCode(){
        //$code=$_POST['code'];
        $phoneNum=$_POST['phoneNum'];
        if($_POST['imgCode']){
            if(md5($_POST['imgCode']) != session('verify')){
                $this->setError('30101',1);//验证码有误！
            }
        }
        $cookieUid = $this->getCookieUserID();
        //验证是否有userid
        if(empty($cookieUid)){
            $this->homeIsLoginCheck();
        }
        $status=1;//验证状态
        if($_POST['phoneAttr']=='edit'){//修改已验证的手机，通过已验证的手机验证后的状态
            $status = 2;
        }
		if($cookieUid) {
            $data['CheckPhone'] = $status;
            //兼容用户初始电话为空和修改已验证电话的情况
            $data['Phonecode'] = $phoneNum;
            $user = $this->getModel('User');
            $user->changeUserData($cookieUid, $data);
        }
        //$output=R('Common/UserLayer/checkPhoneCode',array($phoneNum,$code,$cookieUid,$status));

//      if($output[0] == 1){
//          $this->setError($output[1],1); //返回错误提示
//      }

        $this->setBack('success'); //返回成功提示
    }

    /**
     * 发送邮箱验证码
     * @author demo
     */
    public function sendEmailCode(){
        $email = $_POST['email'];
        if(!$email){
            $this->setError('1X422',1);//请填写邮箱
        }
        $output  = R('Common/UserLayer/sendEmailCode',array($email,$this->getCookieUserID()));

        //输出错误信息
        if($output[0] == 1){
            $this->setError($output[1],1);
        }

        $this->setBack('success');
    }

    /**
     * 验证邮箱验证码
     * @author demo
     */
    public function checkEmailCode(){
        $email=$_POST['email'];//邮箱
        if(!$email){
            $this->setError('1X422',1);//请填写邮箱
        }

//      $code=$_POST['code'];//验证码
//      if(!$code){
//          $this->setError('30104',1);
//      }
		
		$cookieUid = $this->getCookieUserID();
        //验证是否有userid
        if(empty($cookieUid)){
            $this->homeIsLoginCheck();
        }
		
        $status = 1;//状态
        //组卷端如果执行验证过的邮箱单独修改时开启
        if($_POST['emailAttr']=='edit'){
            $status = 2;
        }
		
		if($cookieUid) {
            $data['CheckEmail'] = $status;
            //兼容用户初始邮箱为空和修改已验证邮箱的情况
            $data['Email'] = $email;
            $user = $this->getModel('User');
            $user->changeUserData($cookieUid, $data);
        }
		
        //$output  = R('Common/UserLayer/checkEmailCode',array($email,$code,$this->getCookieUserID(),$status));

        //输出错误信息
//      if($output[0] == 1){
//          $this->setError($output[1],1);
//      }

        $this->setBack('success'); //返回成功提示
    }

    /**
     * 组卷权限表
     * @author demo
     */
    public function userLevelIntro(){
        $result = $this->getModel('PowerUser')->getTable(array(1,2,3,4,5,6), true);
        $list = $result[0];
        $header = array_shift($list);
        $userGroup = $this->getModel('UserGroup')->findData('GroupID', 'GroupName=1 AND UserID='.$this->getCookieUserID());
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

        //获取权限价格
        $vipPrice = 20;
        $superVipPrice = 40;
        $map['PUID'] = array('in','43,44');
        $res = $this->getModel('PowerUser')->selectData(
            'PUID,Price',
            $map,
            '',
            2
        );
        foreach($res as $i=>$iRes){
            if($iRes['PUID']==43){//专项会员
                $vipPrice = $iRes['Price'];
            }
            if($iRes['PUID']==44){//至尊会员
                $superVipPrice = $iRes['Price'];
            }
        }

        $slogan = '';
        if(C('IS_PROMOTION')){//活动中
            //判断活动时间
            if(time()>=C('PROM_BEGIN_TIME') && time()<=C('PROM_END_TIME')){
                $slogan = C('PROMOTION_SLOGAN');
                $vipPrice      = C('VIP_PRICE');
                $superVipPrice = C('SUPER_VIP_PRICE');
            }
        }
        $this->assign('slogan',$slogan);
        $this->assign('vipPrice',$vipPrice);
        $this->assign('superVipPrice',$superVipPrice);
        $this->assign('eq',$eq);
        $this->assign('group', $group);
        $this->assign('header', $header);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 单独更新用户昵称
     * @author demo
     */
    public function updateNickname(){
        $nickname=formatString('stripTags',$_POST['Nickname']);
        $data['Nickname']=$nickname;
        $output=R('Common/UserLayer/checkField',array($data));
        if(!empty($output)){
            $this->setError($output['Nickname'],1);
        }
        $user=$this->getModel('User');
        $result=$user->changeUserData($this->getCookieUserID(),$data);
        if($result===false){
            $this->setError('30310',1);
        }
        $this->setBack('success'); //返回成功提示
    }

    /**
     * 用户查看自己订单
     * @author demo
     */
    public function checkOrder(){
          $pageName = '用户订单';
          $this->assign('pageName',$pageName);
          $this->display();
    }

    /**
     * 获取订单列表
     * @author demo
     */
    public function getOrderList(){
        $userID = $this->getCookieUserID();//用户ID
        if(empty($_POST['page']) || !is_numeric($_POST['page'])){
            $_POST['page'] = 1;
        }
        $perPage = C('WLN_PERPAGE');

        $count=$this->getModel('OrderList')->selectCount(
            ['UID'=>$userID,'IfHidden'=>0],
            'OLID'
        );
        if($count<1){
            $this->setBack(array('error','您还没有购买过题库会员服务!'));
        }

        $page = page($count,$_POST['page'],$perPage);
        $result = $this->getModel('OrderList')->selectData(
            'OLID,OrderID,OrderTime,OrderStatus,TotalFee,OrderName,IfHidden',
            ['UID'=>$userID,'IfHidden'=>0],
            'OLID DESC',
            ($perPage*($page-1)).','.$perPage
        );
        //格式转化
        foreach($result as $i=>$iResult){
            $result[$i]['OrderTime'] = date('Y-m-d H:i:s',$iResult['OrderTime']);
            $result[$i]['OrderName'] = str_replace('#',' ',$result[$i]['OrderName']);
            unset($result[$i]['IfHidden']);//删除非必需字段
            unset($result[$i]['OLID']);//删除真实ID
        }
        $output = array();
        $output[0] = 'success';
        $output[1] = $result;
        $output[2] = $count;
        $output[3] = $perPage;
        $this->setBack($output);
    }

    /**
     * 删除订单
     * @author demo
     */
    public function delOrder(){
        $userID = $this->getCookieUserID();//用户ID
        $oid = $_POST['oid'];
        $buffer=$this->getModel('OrderList')->selectData(
            'OLID',
            ['UID'=>$userID,'OrderID'=>$oid]
        );
        if(!$buffer) $this->setBack(array('error','删除失败!'));

        //软删除
        if($this->getModel('OrderList')->updateData(
                ['IfHidden'=>1],
                ['UID'=>$userID,'OrderID'=>$oid])===false){
            $this->setBack(array('error','删除失败!'));
        }
        /*
        if($this->getModel('OrderList')->deleteData(
                ['UID'=>$userID,'OrderID'=>$oid])===false){
            $this->setBack(array('error','删除失败!'));
        }
        */
        $this->setBack(array('success'));
    }

    /**
     * 用户去支付
     * @author demo
     */
    public function goToPay(){
        $userID = $this->getCookieUserID();//用户ID
        $oid    = $_GET['oid'];
        //查询
        $buffer = $this->getModel('OrderList')->selectData(
            'OLID,TotalFee,OrderID,OrderName,OrderDetail',
            ['UID' => $userID, 'OrderID' => $oid]
        );
        if(!$buffer) $this->setError('1X423',0);
        $buffer = $buffer[0];
        //查询订单支付宝订单数据
        $param = array();
        $param['orderNum']    = $buffer['OrderID'];
        $param['orderName']   = $buffer['OrderName'];
        $param['orderDetail'] = $buffer['OrderDetail'];
        $param['totalFee']    = $buffer['TotalFee'];
        $param['showUrl']    = C('WLN_HTTP'); //可以为空,为空时取默认值

        useToolFunction('Alipay/Alipay','doPay',array($param));
        exit();
    }

    /**
     * 获取等级权限对照表
     * @author demo
     */
    public function levelInfo(){
        //获取该用户等级下的权限值
        $userName = $this->getCookieUserName();
        $levelValueList=$this->getModel('UserLevel')->getLevelMsg($userName);
        $levelList=$this->getModel('UserLevel')->levelPower();
        $this->assign('levelList',$levelList[1]);
        $this->assign('valueList',$levelList[0]);
        $this->assign('thisLevelMsg',$levelValueList[0]);
        $this->display();
    }

    /**
     * 用户经验获取对照表
     * @author demo
     */
    public function userExpList(){
        $expList=SS('expList');
        //调取该用户的经验获取记录
        $userID = $this->getCookieUserID();
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
        $this->assign('explist',$expList);
        $this->display();
    }

    /**
     * 用户积分列表模板
     * @author demo
     */
    public function recordList(){
        $this->display();
    }

    /**
     * 用户获取奖励列表
     * @author demo
     */
    public function getRecordList(){
        $userID = $this->getCookieUserID();//用户ID
        if(empty($_POST['page']) || !is_numeric($_POST['page'])){
            $_POST['page'] = 1;
        }
        $perPage = C('WLN_PERPAGE');

        $count=$this->getModel('Pay')->selectCount(
            ['UserID'=>$userID],
            'PayID'
        );
        if($count<1){
            $this->setBack(array('error','您没有相关奖励记录!'));
        }

        $page = page($count,$_POST['page'],$perPage);
        $result = $this->getModel('Pay')->selectData(
            '*',
            ['UserID'=>$userID],
            'PayID DESC',
            ($perPage*($page-1)).','.$perPage
        );
        //格式转化
        foreach($result as $i=>$iResult){
            $result[$i]['PayMoney']=floatval($result[$i]['PayMoney']);
            $result[$i]['AddTime'] = date('Y-m-d H:i:s',$iResult['AddTime']);
        }
        $output = array();
        $output[0] = 'success';
        $output[1] = $result;
        $output[2] = $count;
        $output[3] = $perPage;
        $this->setBack($output);
    }

}
