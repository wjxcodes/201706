<?php
/**
 * @author demo
 * @date 2014年11月11日
 */
/**
 * 用户模型类，用于处理用户档案操作
 */
namespace User\Manage;
class UserManage extends BaseController  {
    var $moduleName = '用户管理';
    /**
     * 浏览用户
     * @author demo
     */
    public function indexFull() {
        $pageName = '用户管理';

        //获取检错条件
        $where=$this->getWhere($_REQUEST);

        $p=(isset ($_GET['p']) ? $_GET['p'] : 1);
        if($where['data']==' 1=1 ') $p=1;

            $perpage = C('WLN_PERPAGE'); //每页显示数
            if(empty($_REQUEST['IPID'])){
                $page=$p . ',' . $perpage;
            }else{
                $page='0,'. $perpage;
            }
            if(!empty($_REQUEST['groupID'])){
                $count = $this->getModel('User')->unionSelect(
                    'userSelectByPageCount',
                    $where['data']
                ); // 查询满足要求的总记录数
                $list=$this->getModel('User')->unionSelect(
                    'userSelectByPageUserGroup',
                    'a.*,b.GroupName,c.UserID',
                    $where['data'],
                    $page
                );
            }else{
                $count = $this->getModel('User')->selectCount(
                    $where['data'],
                    '*',
                    'a');
                $list=$this->getModel('User')->unionSelect(
                    'userSelectByPage',
                    'a.*,b.GroupName',
                    $where['data'],
                    $page
                );
            }
            //获取用户组数据
            $groupArray=array();//用户组数据集
            $defaultArray=array();//默认数据集
            $powerArray=array();//无需验证时间数据集
            $buffer=SS('powerUserId');
            if($buffer){
                foreach($buffer as $i=>$iBuffer){
                    $groupArray[$iBuffer['PUID']]=$iBuffer;
                    if($iBuffer['IfDefault']){
                        $defaultArray[$iBuffer['GroupName']]=$iBuffer;
                        $powerArray[]=$iBuffer['PUID'];
                    }
                    if($iBuffer['ListID']=='all'){
                        $powerArray[]=$iBuffer['PUID'];
                    }
                }
            }

            //获取用户id
            $userArray=array();
            foreach($list as $i=>$iList){
                $userArray[]=$iList['UserID'];
            }
            //获取用户所属权限组
            if(!empty($userArray)){ //如果查询的用户不为空，才能继续查询用户对应的分组信息
                $userGroupInfo = $this->getModel('UserGroup')->selectData(
                    'UserID,GroupID,GroupName,LastTime',
                    'UserID in ('.implode(',',$userArray).')');
                $userArray=array();
                foreach($userGroupInfo as $i=>$iUserGroupInfo){
                    $userArray[$iUserGroupInfo['UserID']][]=$iUserGroupInfo;
                }
            }
            //载入组名和到期时间
            $groupName=$this->getModel('PowerUser','getGroupName');
            $subject=SS('subject');
            foreach($list as $i=>$iList){
                //获取自定义分组名称
                $list[$i]['SubjectName']=$subject[$list[$i]['SubjectStyle']]['ParentName'].$subject[$list[$i]['SubjectStyle']]['SubjectName'];
                $gpArray=array();//记录分组数据
                $list[$i]['EndTime'] = $userArray[$iList['UserID']][0]['LastTime'];
                if($userArray[$iList['UserID']]){
                    foreach($userArray[$iList['UserID']] as $j=>$jUserArray){
                        if(empty($jUserArray['GroupID'])){
                            $gpArray[$jUserArray['GroupName']][]=$defaultArray[$jUserArray['GroupName']]['UserGroup'];
                        }else if(!in_array($jUserArray['GroupID'],$powerArray) && $jUserArray['LastTime']<time() && $jUserArray['GroupName']!=3){
                            $gpArray[$jUserArray['GroupName']][]=$groupArray[$jUserArray['GroupID']]['UserGroup']."<span style='color:red'>(已过期)</span>&nbsp&nbsp";
                        }else{
                            $gpArray[$jUserArray['GroupName']][]=$groupArray[$jUserArray['GroupID']]['UserGroup'];
                        }
                    }
                    if($gpArray){
                        $tmpstr='';
                        foreach($gpArray as $j=>$jGpArray){
                            $tmpstr.=$groupName[$j].'：'.implode('、',$jGpArray).'<br/>';
                        }
                        $list[$i]['UserGroup'] = $tmpstr;
                    }
                }else{
                    foreach($groupName as $j=>$jGroupName){
                        if($defaultArray[$j]['UserGroup']){
                            $groupTmp[$j] = $jGroupName.':'.$defaultArray[$j]['UserGroup'];
                        }
                    }
                    $list[$i]['UserGroup'] = join('<br>',$groupTmp);
                }
                if($iList['Whois'] == 1){//教师用户显示认证情况
                    $tmp = array('未认证','认证中','已认证','认证未通过');
                    $list[$i]['AuthTitle'] = $tmp[$iList['IfAuth']];
                }
            }

        if($where['data']!=' 1=1 ') $this->pageList($count, $perpage, $where['map']);


        //学科
        $powerList=SS('powerUserGroup');
        $subjectArray=SS('subjectParentId'); //获取学科数据集
        $this->assign('subjectArray', $subjectArray);
        $this->assign('powerList', $powerList);
        /*载入模板标签*/
        //获取地区
        $param['style']='area';
        $param['pID']=0;
        $arrArea=$this->getData($param);
        $this->assign('arrArea',$arrArea);//地区
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 浏览用户
     * @author demo
     */
    public function index() {
        $pageName = '用户管理';
        //获取检错条件
        $where=$this->getWhere($_REQUEST);
        $perpage = C('WLN_PERPAGE'); //每页显示数
        $page=(isset ($_GET['p']) ? $_GET['p'] : 1) . ',' . $perpage;
        $userModel = $this->getModel('User');
        if(!empty($_REQUEST['groupID'])){
            $count = $userModel->unionSelect(
                'userSelectByPageCount',
                $where['data']
            ); // 查询满足要求的总记录数
            $list = $userModel->unionSelect(
                'userSelectByPageUserGroup',
                'a.*,b.GroupName,c.UserID',
                $where['data'],
                $page
            );
        }else{
            $count = $userModel->selectCount(
                $where['data'],
                '*',
                'a'
            ); // 查询满足要求的总记录数
            $list = $userModel->unionSelect(
                'userSelectByPage',
                'a.*,b.GroupName',
                $where['data'],
                $page
            );
        }
        //获取用户组数据
        $groupArray=array();//用户组数据集
        $defaultArray=array();//默认数据集
        $powerArray=array();//无需验证时间数据集
        $buffer=SS('powerUserId');
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                $groupArray[$iBuffer['PUID']]=$iBuffer;
                if($iBuffer['IfDefault']){
                    $defaultArray[$iBuffer['GroupName']]=$iBuffer;
                    $powerArray[]=$iBuffer['PUID'];
                }
                if($iBuffer['ListID']=='all'){
                    $powerArray[]=$iBuffer['PUID'];
                }
            }
        }

        //获取用户id
        $userArray=array();
        foreach($list as $i=>$iList){
            $userArray[]=$iList['UserID'];
        }
        //获取用户所属权限组
        if(!empty($userArray)){ //如果查询的用户不为空，才能继续查询用户对应的分组信息
            $userGroupInfo = $this->getModel('UserGroup')->selectData(
                'UserID,GroupID,GroupName,LastTime',
                'UserID in ('.implode(',',$userArray).')');
            $userArray=array();
            foreach($userGroupInfo as $i=>$iUserGroupInfo){
                $userArray[$iUserGroupInfo['UserID']][]=$iUserGroupInfo;
            }
        }
        //载入组名和到期时间
        $groupName=$this->getModel('PowerUser','getGroupName');
        $subject=SS('subject');
        foreach($list as $i=>$iList){
            //获取自定义分组名称
            $list[$i]['SubjectName']=$subject[$list[$i]['SubjectStyle']]['ParentName'].$subject[$list[$i]['SubjectStyle']]['SubjectName'];
            $gpArray=array();//记录分组数据
            $list[$i]['EndTime'] = $userArray[$iList['UserID']][0]['LastTime'];
            if($userArray[$iList['UserID']]){
                foreach($userArray[$iList['UserID']] as $j=>$jUserArray){
                    if(empty($jUserArray['GroupID'])){
                        $gpArray[$jUserArray['GroupName']][]=$defaultArray[$jUserArray['GroupName']]['UserGroup'];
                    }else if(!in_array($jUserArray['GroupID'],$powerArray) && $jUserArray['LastTime']<time() && $jUserArray['GroupName']!=3){
                        $gpArray[$jUserArray['GroupName']][]=$groupArray[$jUserArray['GroupID']]['UserGroup']."<span style='color:red'>(已过期)</span>&nbsp&nbsp";
                    }else{
                        $gpArray[$jUserArray['GroupName']][]=$groupArray[$jUserArray['GroupID']]['UserGroup'];
                    }
                }
                if($gpArray){
                    $tmpstr='';
                    foreach($gpArray as $j=>$jGpArray){
                        $tmpstr.=$groupName[$j].'：'.implode('、',$jGpArray).'<br/>';
                    }
                    $list[$i]['UserGroup'] = $tmpstr;
                }
            }else{
                foreach($groupName as $j=>$jGroupName){
                    if($defaultArray[$j]['UserGroup']){
                        $groupTmp[$j] = $jGroupName.':'.$defaultArray[$j]['UserGroup'];
                    }
                }
                $list[$i]['UserGroup'] = join('<br>',$groupTmp);
            }
        }
        $this->pageList($count, $perpage, $where['map']);
        //学科
        $powerList=SS('powerUserGroup');
        $subjectArray=SS('subjectParentId'); //获取学科数据集
        $this->assign('subjectArray', $subjectArray);
        $this->assign('powerList', $powerList);
        /*载入模板标签*/
        //获取地区
        $param['style']='area';
        $param['pID']=0;
        $arrArea=$this->getData($param);
        $this->assign('arrArea',$arrArea);//地区
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加用户
     * @author demo
     */
    public function add() {
        $pageName = '添加用户';
        $act = 'add'; //模板标识

        $userPowerTmp =SS('powerUserId');
        $groupName=$this->getModel('PowerUser','getGroupName');
        $defaultStr = '';
        foreach($userPowerTmp as $i=>$iUserPowerTmp){
            $userPower[$iUserPowerTmp['GroupName']][$i]=$iUserPowerTmp;
            $powerName[$iUserPowerTmp['GroupName']] = $groupName[$iUserPowerTmp['GroupName']];
            if($iUserPowerTmp['IfDefault']==1){
                $defaultStr .= ','.$iUserPowerTmp['PUID'];
            }
        }
        //获取自定义分组
        $customGroup = $this->getModel('UserCustomGroup')->selectData(
            '*',
            '1=1');
        $defaultStr = ltrim($defaultStr,',');
        //获取学科数据集
        $subjectArray = SS('subjectParentId');//学科信息
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray);
        $this->assign('powerName',$powerName);
        $this->assign('customGroup',$customGroup);
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('default',$defaultStr);//默认权限组
        $this->assign('userPower',$userPower);//用户组权限
        $this->display();
    }
    /**
     * 编辑用户
     * @author demo
     */
    public function edit() {
        $userID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($userID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑用户';
        $act = 'edit'; //模板标识

        $edit = $this->getModel('User')->selectData(
            '*',
            'UserID=' . $userID,
            '',
            1);
        if($edit){
            //获取地区和学校
            $buffer=SS('areaParentPath');  // 缓存父类路径path数据
            $buffer2=SS('areaList');
            if($buffer[$edit[0]['AreaID']]){
                foreach($buffer[$edit[0]['AreaID']] as $bb){
                    $edit[0]['AreaName'].='>>'.$bb['AreaName'];
                }
            }
            if($buffer2[$edit[0]['AreaID']]) $edit[0]['AreaName'].='>>'.$buffer2[$edit[0]['AreaID']]['AreaName'];
            else $edit[0]['AreaName'].='';
;            $buffer3=$this->getModel('School')->selectData(
                '*',
                'SchoolID='.$edit[0]['SchoolID']);
            $edit[0]['SchoolName']=$buffer3[0]['SchoolName'];
        }

        //获取用户组及到期时间
        $groupIdArr=$this->getModel('UserGroup')->selectData(
            'GroupID,GroupName,LastTime',
            'UserID='.$userID,
            'UGID asc');
        $edit[0]['EndTime'] = $groupIdArr[0]['LastTime'];

        $groupArray=array(); //用户组数据集
        $defaultArray=array();//默认组数据集
        $buffer=SS('powerUserId');
        foreach($buffer as $i=>$iBuffer){
            $groupArray[$iBuffer['PUID']]=$iBuffer;
            if($iBuffer['IfDefault']){
                $defaultArray[$iBuffer['GroupName']]=$iBuffer;
            }
        }
        foreach($groupIdArr as $i=>$iGroupIdArr){
            $priGroup[$iGroupIdArr['GroupName']][]=$iGroupIdArr;
        }
        $edit[0]['UserGroup']='';
        $groupName=$this->getModel('PowerUser','getGroupName');
        if($groupIdArr){            //获取用户权限组记录
            foreach($groupName as $i=>$iGroupName){
                if($priGroup[$i]){
                    if(count($priGroup[$i])>1){
                        foreach($priGroup[$i] as $j=>$jPriGroup){
                            $groupTmpArr[$j] = $iGroupName.':'.$groupArray[$jPriGroup['GroupID']]['UserGroup'];
                        }
                        $groupTmp[$i] = join(' ',$groupTmpArr);
                    }else{
                        $groupTmp[$i] = $iGroupName.':'.$groupArray[$priGroup[$i][0]['GroupID']]['UserGroup'];
                    }
                }else{
                    $groupTmp[$i] = $iGroupName.':'.$defaultArray[$i]['UserGroup'];
                }
            }
            $edit[0]['UserGroup']=join(' ',$groupTmp);
        }else{                  //默认权限组
            foreach($defaultArray as $i=>$iDefaultArray){
                $iDefaultArray['UserGroupName'] = $groupName[$i];
                $groupTmp[$i] = $iDefaultArray['UserGroupName'].':'.$iDefaultArray['UserGroup'].'　';
            }
            $edit[0]['UserGroup']=join('  ',$groupTmp);
        }
        //学科信息
        $subjectArray = SS('subjectParentId');//学科信息
        //年级信息
        $classGrade=SS('gradeListSubject');
        $subject=SS('subject');
        $gradeArray=$classGrade[$subject[$edit[0]['SubjectStyle']]['PID']];
        //获取自定义分组
        $customGroup = $this->getModel('UserCustomGroup')->selectData(
            '*',
            '1=1');
        /*载入模板标签*/
        $tmp = array('未认证','认证中','已认证','认证未通过');
        $this->assign('authTitle',$tmp);
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('grade',$gradeArray['sub']);
        $this->assign('subjectArray', $subjectArray);
        $this->assign('customGroup', $customGroup);
        $this->assign('pageName', $pageName);
        $this->display('User/add');
    }
    /**
     * 保存用户
     * @author demo
     */
    public function save() {
        $userID = $_POST['UserID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($userID) && $act == 'edit') {
            $this->setError('30301'); //数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223'); //模板标识不能为空！
        }
        $user = $this->getModel('User');
        //获取字段
        $data = array ();
        $data['RealName'] = $_POST['RealName'];
        $data['Sex'] = $_POST['Sex'];
        $data['Phonecode'] = $_POST['Phonecode'];
        $data['Email'] = $_POST['Email'];
        $data['Address'] = $_POST['Address'];
        $data['PostCode'] = $_POST['PostCode'];
        $data['Whois'] = $_POST['Whois'];
        $data['SubjectStyle'] = $_POST['SubjectID'];
        $data['GradeID']=$_POST['GradeID'];
        $data['CustomGroup']=isset($_POST['customGroup'])?$_POST['customGroup']:'';
        if(empty($data['Whois'])) $data['Whois']=0;
        $data['LastIP'] = get_client_ip(0,true);
        if ($act == 'add') {
            //检查用户名长度
            $userName=$_POST['UserName'];
            if(!checkString('checkIfPhone', $userName) and !checkString('checkIfEmail', $userName)){
                $this->setError('30739'); //用户名请使用邮箱或手机号！
            }
            //检查用户名合法
            $backstr=$user->NameFilter($userName);
            if($backstr['errornum']!='success'){
                if($backstr['replace']!=''){
                    $this->setError($backstr['errornum'],'','',$backstr['replace']);
                }else{
                    $this->setError($backstr['errornum']);
                }
            }
            $data['UserName'] = $userName;
            //检查用户名重复
            $buffer = $user->checkUser($data['UserName'],$data['Phonecode'],$data['Email']);
            if($buffer){
                $this->setError($buffer);
            }
            //检查密码
            if(empty($_POST['Password']) || empty($_POST['Password2'])){
                $this->setError('30202'); //请输入密码！
                exit;
            }
            if($_POST['Password']!=$_POST['Password2']){
                $this->setError('30207'); //两次输入的密码不一致！
                exit;
            }
            if(strlen($_POST['Password'])<6){
                $this->setError('30823'); //密码长度过短！至少6位。
                exit;
            }

            $data['SaveCode'] = $user->saveCode(); //安全码
            $data['Password'] = md5($data['UserName'].$_POST['Password']); //密码
            $data['LoadDate'] = time();
            //获取学号
            $autoInc=$this->getModel('AutoInc');
            $orderNum=$autoInc->getOrderNum();
            if(!$orderNum){
                $this->setError('1X438'); //生成学号失败！
                exit;
            }
            $data['OrderNum'] = $orderNum;

            $lastID = $this->getModel('User')->insertData(
                $data);
            if ($lastID === false) {
                $this->setError('30310'); //添加失败！
            } else {
                $this->getModel('User')->updateMonth($lastID,$_POST); //根据参数条件，添加用户权限组
                //写入日志
                $this->adminLog($this->moduleName, '添加用户【' . $_POST['UserName'] . '】');
                $this->showSuccess('添加成功！', __URL__);
            }
        } else if ($act == 'edit') {
                $data['UserID'] = $userID;
                $buffer = $user->checkUser($data['UserName'],$data['Phonecode'],$data['Email'],$userID);
                if($buffer){
                    $this->setError($buffer);
                }
                if ($this->getModel('User')->updateData($data,'UserID='.$userID) === false) {
                     $this->setError('30311'); //修改失败！
                } else {
                    //写入日志
                    $this->adminLog($this->moduleName, '修改用户UserID为【' . $_POST['UserID'] . '】的数据');
                    $this->showSuccess('修改成功！', __URL__);
                }
            }
    }

    /**
     * 修改用户密码
     * @author demo
     */
    public function password() {
        $data = array ();
        $data['UserID'] = $_REQUEST['UserID']; //获取数据标识
        if(empty($data['UserID'])){
            $this->setError('30301'); //数据标识不能为空！
        }
        $user = $this->getModel('User');
        $edit=$this->getModel('User')->selectData(
            '*',
            'UserID='.$data['UserID']);
        if(!$edit){
            $this->setError('30304','',__URL__); //无效数据！
        }
        //显示页面
        if(!IS_POST){
            $pageName='修改用户密码';
            $this->assign('pageName', $pageName); //页面标题
            $this->assign('edit',$edit[0]);
            $this->display();
            exit;
        }
        //判断新密码
        if (empty ($_POST['Password']) || empty ($_POST['Password2'])) {
            $this->setError('30209'); //请输入新密码！
        }
        if ($_POST['Password'] != $_POST['Password2']) {
            $this->setError('30207'); //两次输入的密码不一致！
        }
        if (strlen($_POST['Password']) < 6) {
            $this->setError('30823'); //密码长度过短！至少6位。
        }
        $data['Password'] = md5($edit[0]['UserName'].$_POST['Password']);
        $data['SaveCode'] = $user->saveCode();
        if ($this->getModel('User')->updateData(
                $data,
                'UserID='.$data['UserID']) === false) {
            $this->setError('30311'); //修改失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName, '修改用户UserID为【' . $_POST['UserID'] . '】的密码');
            $this->showSuccess('修改成功！', __URL__);
        }
    }
    /**
     * 设置包月
     * @author demo
     */
    public function month() {
        $userID = $_REQUEST['UserID']; //获取数据标识
        if (!$userID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $edit = $this->getModel('User')->selectData(
            '*',
            'UserID =' . $userID);
        if(!$edit){
            $this->setError('1X442','',__URL__); //无效数据！
        }
        //获取到期时间 用户所属组
        $buffer = $this->getModel('UserGroup')->selectData(
            '*',
            'UserID='.$userID);
        $groupIDArray=array();
        $groupArray=array();
        if($buffer){
            $edit[0]['EndTime']=0;
            foreach($buffer as $i=>$iBuffer){
                if($iBuffer['LastTime']>time() && empty($edit[0]['EndTime'])){
                    $edit[0]['EndTime']=$iBuffer['LastTime'];
                }
                $groupIDArray[]=$iBuffer['GroupID'];
                $groupArray[$iBuffer['GroupName']][]=$iBuffer;
            }
        }
        //显示页面
        $buffer =SS('powerUserId');
        if(!IS_POST){
            //获取用户组数据
            $group=$this->getModel('PowerUser','getGroupName');
            $powerArray=array();
            $groupArray=array();
            foreach($buffer as $i=>$iBuffer){
                if(in_array($iBuffer['PUID'],$groupIDArray)) $iBuffer['IsSelect']=1;
                $powerArray[$iBuffer['GroupName']][]=$iBuffer; //组列表
                $groupArray[$iBuffer['GroupName']]['UserGroupName'] = $group[$iBuffer['GroupName']];//分组名称
            }
            $pageName='更改用户包月';
            $this->assign('pageName', $pageName); //页面标题
            $this->assign('powerArray', $powerArray); //页面标题
            $this->assign('groupArray', $groupArray); //页面标题
            $this->assign('edit',$edit[0]);
            $this->display();
            exit;
        }
        $endTime=$_POST['EndTime'];
        $data = array ();
        if(checkString('isDate',$endTime)){
            $data['LastTime'] = strtotime($endTime);
        }
        $data['UserID'] = $userID;
        $data['AddTime'] = time();
        $data['groupname_1']=$_POST['groupname_1'];
        $data['groupname_2']=$_POST['groupname_2'];
        $data['groupname_3']=$_POST['groupname_3'];
        $result=$this->getModel('User')->updateMonth($userID,$data);
        if ($result === false) {
            $this->setError('1X432'); //更改包月失败！
        } else {
            //写入日志
            $groupName1=$buffer[$_POST['groupname_1']]['UserGroup'];
            $groupName2=$buffer[$_POST['groupname_2']]['UserGroup'];
            foreach($_POST['groupname_3'] as $i=>$iGroupName){
                $teacherGroup[]=$buffer[$iGroupName]['UserGroup'];
            }
            if($data['LastTime']){
                $timeMsg=$data['LastTime'];
            }else{
                $timeMsg='无';
            }
            $logMsg='修改用户UserID【' . $userID . '】的组卷端用户组【'.$groupName1.'】,提分端用户组【'.$groupName2.'】,教师端用户组【'.join(',',$teacherGroup).'】,包月截止日期【'.$timeMsg.'】';
            $this->adminLog($this->moduleName,$logMsg);
            $this->showSuccess('更改包月成功！', __URL__);
        }
    }
    /**
     * 修改点数
     * @author demo
     */
    public function point() {
        $userID = $_REQUEST['UserID']; //获取数据标识
        if (!$userID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $edit = $this->getModel('User')->selectData(
            '*',
            'UserID =' . $userID);
        if(!$edit){
            $this->setError('1X442','',__URL__); //无效数据！
        }
        //显示页面
        if(!IS_POST){
            $pageName='更改用户点数';
            $this->assign('pageName', $pageName); //页面标题
            $this->assign('edit',$edit[0]);
            $this->display();
            exit;
        }
        // 提交数据
        if ($this->getModel('User')->conAddData(
                'Cz=Cz+'.$_POST['Point'],
                "UserID=".$userID) === false) {
            $this->setError('1X439'); //更改点数失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName, '修改用户UserID【' . $userID . '】增加点数【' . $_POST['Point'] . '】');
            $this->showSuccess('更改点数成功！', __URL__);
        }
    }
    /**
     * 锁定用户
     * @author demo
     */
    public function replace() {
        $data = array ();
        $Status = $_POST['status'];
        $userID = $_POST['wid']; //获取数据标识
        if($Status){
            $Status=0;
        }else{
            $Status=1;
        }
        $data['Status'] = $Status;
        if (!$userID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if ($this->getModel('User')->updateData(
                $data,
                'UserID in (' . $userID . ')') === false) {
            $this->setError('30824'); //更改状态失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName, '修改用户UserID为【' . $userID . '】的状态【' . ($data['Status'] == 1 ? '锁定' : '正常') . '】');
            $this->setBack('更改状态成功！');
        }
    }
    /**
     * 删除用户
     * @author demo
     */
    public function delete() {
        $userID = $_POST['id']; //获取数据标识
        if (!$userID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if ($this->getModel('User')->deleteData(
                'UserID in ('.$userID.')') === false) {
            $this->setError('30302'); //删除失败！
        } else {
            $this->getModel('TeacherAuthinfo')->delete('UserID in ('.$userID.')');//删除教师认证信息信息
            //写入日志
            $this->adminLog($this->moduleName, '删除用户UserID为【' . $userID . '】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
    /**
     * 教师认证信息列表
     * @author demo
     */
    public function teacherAuthInfo(){
        $UserID = (int)$_REQUEST['UserID'];
        if($UserID < 1) $this->setError('30301'); //数据标识不能为空！
        $user = $this->getModel('User')->getInfoByID($UserID,'UserID,UserName,Nickname,RealName,Whois,IfAuth');
        if($user['Whois'] != 1) $this->setError('1X440');//当前用户不是教师
        $tmp = array('未认证','认证中','已认证','认证未通过');
        $user['AuthTitle'] = $tmp[$user['IfAuth']];
        if($user['IfAuth'] != 0){//有认证信息
            $count = $this->getModel('TeacherAuthinfo')->getList('count(id) as c','UserID = '.$UserID);//总数
            $perpage = C('WLN_PERPAGE'); //每页显示数
            $page = I('p','1','intval');
            $page = page($count,$page,$perpage);
            $page = ($page-1) * $perpage .','.$perpage;
            $info = $this->getModel('TeacherAuthinfo')->getList('*','UserID = '.$UserID,'ID DESC',$page);
            $this->pageList($count[0]['c'], $perpage);//分页
            $this->assign('authInfo',$info);//认证信息
        }
        $this->assign('pageName', '认证信息');//页面标题
        $this->assign('user',$user);//用户信息
        $this->display();
    }

    /**
     * 修改用户认证状态
     * @author demo
     */
    public function updataAuthStatus() {
        if(empty($_REQUEST['UserID']) && ($_REQUEST['Status'] != 2 || $_REQUEST['Status']!=3)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $UserID = (int)$_REQUEST['UserID'];
        $status = trim($_REQUEST['Status']) == 2 ? 2 : 3;//认证状态 2认证通过，认证为通过
        $userModel = $this->getModel('User');
        $userInfo = $userModel->getInfoByID($UserID,'');
        $rs = $userModel->changeUserData($UserID,array('IfAuth'=>$status));
        if ($rs === false) {
            $this->setError('30311'); //修改失败！
        } else {
            //教师认证经验累加
            $userName = $userInfo['UserName'] ;
            $this->getModel('UserExp')->addUserExpAll($userName,'auth');
            //写入日志
            $this->adminLog($this->moduleName, '修改用户UserID为【' . $UserID . '】的教师认证状态为'.$status);
            $this->showSuccess('修改成功！', __URL__);
        }
    }

    /**
     * 批量上传用户
     * @author demo
     */
    public function uploads(){
        $pageName='批量上传用户';
        $powerUser = $this->getModel('PowerUser');
        $userPowerTmp = SS('powerUserId');
        $groupName=$this->getModel('PowerUser','getGroupName');
        $defaultStr = '';
        foreach($userPowerTmp as $i=>$iUserPowerTmp){
            $userPower[$iUserPowerTmp['GroupName']][$i]=$iUserPowerTmp;
            $powerName[$iUserPowerTmp['GroupName']] = $groupName[$iUserPowerTmp['GroupName']];
            if($iUserPowerTmp['IfDefault']==1){
                $defaultStr .= ','.$iUserPowerTmp['PUID'];
            }
        }

        //获取地区
        $arrArea=SS('areaChildList');
        //获取学科数据集
        $subjectArray =SS('subjectParentId'); //获取学科信息
        //获取自定义分组
        $customGroup = $this->getModel('UserCustomGroup')->selectData(
            '*',
            '1=1');
        /*载入模板标签*/
        $this->assign('area_array',$arrArea[0]);//地区
        $this->assign('default',$defaultStr);//默认权限组
        $this->assign('subjectArray',$subjectArray);
        $this->assign('powerName',$powerName);
        $this->assign('userPower',$userPower);//用户组权限
        $this->assign('customGroup',$customGroup);//自定义分组
        $this->assign('pageName',$pageName);
        $this->display();
    }

    /**
     * 批量上传用户插件
     * @author demo
     */
    public function uploadify(){
        if (!empty($_FILES)) {

            $path=R('Common/UploadLayer/uploadExcel',array('user','excel_stu'));
            if(is_array($path)){
                exit($path[1]);
            }
            $brr=unserialize($path);
        }
        if(empty($brr)){
            $data['description']='未提取到用户信息，请检查上传文档！!';
            $data['msg']=$brr;
            $this->getModel('User')->addErrorLog($data);
            exit('未提取到用户信息，请检查上传文档！');
        }
        $output=array();
        $output=$this->createUser($brr);
        if(!is_array($output)) exit($output);
        exit('success');
    }

    /**
     * 批量上传用户上传文档处理
     * @param $arr array 从上传文档中获取到的数据
     * @author demo
     */
    protected function createUser($arr){
        $user = $this->getModel('User');
        $resultArr = array();
        $uploadInfo=array();//上传文档信息
        foreach($arr as $i=>$iArr){
            $num=0;
            foreach($iArr as $j=>$jArr){
                $iArr[$num] = str_replace(array("/r/n", "/r", "/n",' '), '', $jArr);
                $num++;
            }
            $uNameID = $uPhoneID = $uEmail='';
            $resultArr[$i]=$iArr;
            $resultArr[$i]['order']=$i+1;
            if(empty($iArr[0]) || empty($iArr[1])){
                $resultArr[$i]['error'] = 1;
                $resultArr[$i]['message'] = '用户名或密码为空';
                continue;
            }
            if(strlen($iArr[1])<6 || strlen($iArr[1])>18){
                $resultArr[$i]['error'] = 1;
                $resultArr[$i]['message'] = '请填写6-18位密码！';
                continue;
            }
            //标记上传文档重复数据
            if(in_array($iArr[0],$uploadInfo)){
                $resultArr[$i]['error'] = 1;
                $resultArr[$i]['message'] = '用户名重复';
                continue;
            }else{
                $uploadInfo[]=$iArr[0];
            }
            //验证用户名，电话，邮箱是否重复
            $checkInfo = $user->checkUser($iArr[0]);
            if($checkInfo=='30718'){
                $resultArr[$i]['error'] = 1;
                $resultArr[$i]['message'] = '用户名重复';
            }else if($checkInfo=='30224'){
                $resultArr[$i]['error'] = 1;
                $resultArr[$i]['message'] = '电话重复';
            }else if($checkInfo=='30225'){
                $resultArr[$i]['error'] = 1;
                $resultArr[$i]['message'] = '邮箱重复';
            }
        }
        $resultArr['serial'] = base64_encode(serialize($resultArr));
        $resultArr['return'] = 2;
        $this->setBack($resultArr);
    }



    /**
     * 批量上传用户保存
     * @author demo
     */
    public function addUsers(){
        $powerInfo = $_POST['powerInfo'];
        $delOrder = $_POST['delOrder'];
        $userInfo = unserialize(base64_decode($_POST['serialData']));
        $data = array();
        $data['CustomGroup']=isset($_POST['groupID'])?$_POST['groupID']:'';
        $power = array();
        $groupName=$this->getModel('PowerUser','getGroupName');
        $delArray = array();
        $delArray=explode(',',$delOrder);
        foreach($userInfo as $i=>$iUserInfo){
            if(empty($iUserInfo) || in_array($i+1,$delArray)){
                continue;
            }
            $data['UserName'] = trim($iUserInfo[0]);
            $data['Password'] = md5($data['UserName'].trim($iUserInfo[1]));
            if(!$iUserInfo[2] || $iUserInfo[2]=='空'){
                $iUserInfo[2]='';
            }
            $data['RealName'] = trim($iUserInfo[2]);
            $data['Sex'] = trim($iUserInfo[3]);
            $data['Phonecode'] = trim($iUserInfo[4]);
            $data['Email'] = trim($iUserInfo[5]);
            $data['Whois'] = $powerInfo[4];
            if($powerInfo[5]!=null){
                $data['SubjectStyle'] = $powerInfo[5];
            }else{
                $data['SubjectStyle'] = 0;
            }
            if($powerInfo[6]){
                $data['GradeID'] = $powerInfo[6];
            }
            if($powerInfo[7]){
                $power['LastTime'] = strtotime($powerInfo[7]);
            }else{
                $power['LastTime'] = time();
            }
            if($powerInfo[8]){
                $data['SchoolID'] = $powerInfo[8];
            }
            if($powerInfo[9]){
                $data['AreaID'] = $powerInfo[9];
            }
            $data['LoadDate'] =time();
            $autoInc=$this->getModel('AutoInc');
            $data['OrderNum']=$autoInc->getOrderNum();
            if(!$data['OrderNum']){
                $this->Error('1X441',1);
            }
            $userID = $this->getModel('User')->insertData(
                $data);
            if($userID){
                $power['UserID'] = $userID;
                foreach($groupName as $j=>$jGroupName){
                    if($powerInfo[$j]){
                        if($j!=3){
                            $power['GroupID'] = $powerInfo[$j];
                            $power['GroupName'] = $j;
                            $power['AddTime'] = time();
                            $this->getModel('UserGroup')->insertData(
                                $power);
                        }else{
                            $powerInfoArr = explode(',',$powerInfo[$j]);
                            foreach($powerInfoArr as $n=>$nUserInfo){
                                $power['GroupID'] = $nUserInfo;
                                $power['GroupName'] = $j;
                                $power['LastTime'] = $power['AddTime'] = time();
                                $this->getModel('UserGroup')->insertData(
                                    $power);
                            }
                        }
                    }
                }
            }else{
                $this->setError('30310',1);
            }
        }
        $this->setBack('success');
    }

    /**
     * 批量上传用户excel模板下载
     * @author demo
     */
    public function excelUser() {
        header('Location:'.C('WLN_HTTP').'/user.xls');
    }

    /**
     * 用户数据导出
     *@author demo
     */
    public function export() {
        //获取检错条件
        $where=$this->getWhere($_REQUEST);

        if($where['data'] == ' 1=1 '){
            $this->setError('30114',IS_AJAX); //请填写查询条件！
        }

        $count = $this->getModel('Log')->selectCount(
            $where['data'],
            'LogID',
            'a'); // 查询满足要求的总记录
        $perpage = 2000;
        $userModel = $this->getModel('User');
        if($count>2000 && empty($_REQUEST['p'])){
            $count = $userModel->selectCount(
                $where['data'],
                'UserID',
                'a'); // 查询满足要求的总记录
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
        $page=(page($count,$_GET['p'],$perpage)). ',' . $perpage;
        //写入日志
        $this->adminLog($this->moduleName,'导出日志记录where【'.$where['data'].'】');

        $list = $userModel->unionSelect(
            'userSelectByPage',
            'a.UserName,a.RealName,a.Phonecode,a.Email,a.LoadDate,a.Logins,a.LastTime,a.LastIP,a.SubjectStyle,a.Whois,a.ComTimes,a.AreaID,c.SchoolName',
            $where['data'],
            $page
        );
        $areaList=SS('areaParentPath');
        $thisArea=SS('areaList');
        $subject=SS('subject');
        //显示具体数据
        foreach($list as $i=>$iLastResult){
            $areaArr=$areaList[$list[$i]['AreaID']];
            if(count($areaArr)==1){
                $iLastResult['sheng']=$areaArr[0]['AreaName'];
                $iLastResult['shi']='';
            }else{
                $iLastResult['sheng']=$areaArr[0]['AreaName'];
                $iLastResult['shi']=$areaArr[1]['AreaName'];
            }
            $iLastResult['qu']=$thisArea[$list[$i]['AreaID']]['AreaName'];
            $iLastResult['LastTime']=date('Y-m-d H:i:s', $iLastResult['LastTime']);
            $iLastResult['LoadDate']=date('Y-m-d H:i:s', $iLastResult['LoadDate']);
            $iLastResult['Whois']= empty($iLastResult['Whois']) ? '学生' : '教师';
            $iLastResult['SubjectName']=$subject[$iLastResult['SubjectStyle']]['ParentName'].$subject[$iLastResult['SubjectStyle']]['SubjectName'];
            unset($iLastResult['SubjectStyle']);
            unset($iLastResult['AreaID']);
            $list[$i]=array_values($iLastResult);
        }
        $keyName=array('用户名','真实姓名','电话','邮箱','开通时间','登录次数','最后时间','最后登陆IP','身份','智能+模板总次数','所在学校','省份','市','区','所属学科');
        $keyWidth=array('25','10','20','30','20','10','20','30','20','10','40','20','15','15','10');
        $excelName=array('title'=>'系统用户信息列表','excelName'=>'系统用户信息导出Excel');
        $excelMsg=R('Common/SystemLayer/resetExcelMsg',array($keyName,$keyWidth));
        R('Common/SystemLayer/excelExport',array($excelMsg,$list,$excelName));
    }
    /**
     * 获取查询条件
     * @param $where array
     * @return array
     * @author demo
     */
    private function getWhere($where){
        //获取检错条件
        $map = array ();
        $data = ' 1=1 ';
        if($_REQUEST['IP']){
            //  IP地址正则验证
            if(!preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $_REQUEST['IP'])){
                $this->setError('30825');
            }
            $Ip=ip2long($_REQUEST['IP']);
            $IPID=$this->getModel('UserIp')->selectData(
                'IPID',
                'IPAddress='.$Ip
            );
            $_REQUEST['IPID']=$IPID[0]['IPID'];
            $map['IPID'] = $_REQUEST['IPID'];
        }
        if($_REQUEST['IPID']){
            $IPID=$_REQUEST['IPID'];
            $perpage = C('WLN_PERPAGE'); //每页显示数
            $page=(isset ($_GET['p']) ? $_GET['p'] : 1) . ',' . $perpage;
            $allUserID=$this->getModel('RegisterLog')->selectCount(
                'IPID='.$IPID,
                'UserID'

            );
            $where['count']=$allUserID;
            $userID=$this->getModel('RegisterLog')->pageData(
                'UserID',
                'IPID='.$IPID,
                'RegLogID desc',
                $page
            );
            if(!empty($userID)){
                $userIDStr='';
                foreach($userID as $i=>$iUserID){
                    $userIDStr[]=$userID[$i]['UserID'];
                }
                $_REQUEST['UserID']=implode(',',$userIDStr);
            }
            $map['IPID'] = $_REQUEST['IPID'];
        }
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
            if ($_REQUEST['Email']) {
                $map['Email'] = $_REQUEST['Email'];
                $data .= ' AND a.Email = "' . $_REQUEST['Email'] . '" ';
            }
            if ($_REQUEST['Phonecode']) {
                $map['Phonecode'] = $_REQUEST['Phonecode'];
                $data .= ' AND a.Phonecode = "' . $_REQUEST['Phonecode'] . '" ';
            }
            if ($_REQUEST['RealName']) {
                $map['RealName'] = $_REQUEST['RealName'];
                $data .= ' AND a.RealName = "' . $_REQUEST['RealName'] . '" ';
            }
            if (is_numeric($_REQUEST['Sex'])) {
                $map['Sex'] = $_REQUEST['Sex'];
                $data .= ' AND a.Sex ="' . $_REQUEST['Sex'] . '" ';
            }
            if (is_numeric($_REQUEST['Status'])) {
                $map['Status'] = $_REQUEST['Status'];
                $data .= ' AND a.Status = "' . $_REQUEST['Status'] . '" ';
            }
            if (is_numeric($_REQUEST['Whois'])) {
                $map['Whois'] = $_REQUEST['Whois'];
                $data .= ' AND a.Whois = "' . $_REQUEST['Whois'] . '" ';
            }

            //用户ID
            if ($_REQUEST['UserID']) {
                if(empty($_REQUEST['IPID'])){
                    $map['UserID'] = $_REQUEST['UserID'];
                }
                $data .= ' AND a.UserID in ('.$_REQUEST['UserID'].')';
            }
            //学科ID
            if (is_numeric($_REQUEST['SubjectID'])) {
                $map['SubjectStyle'] = $_REQUEST['SubjectID'];
                $data .= ' AND a.SubjectStyle = "' . $_REQUEST['SubjectID'] . '" ';
            }

            //分组ID
            if (is_numeric($_REQUEST['groupID'])) {
                $map['groupID'] = $_REQUEST['groupID'];
                $data .= ' AND c.groupID = "' . $_REQUEST['groupID'] . '" ';
            }
            //获取地区
            if($_REQUEST['AreaID']){
                $map['AreaID']=end($_REQUEST['AreaID']);
                $data .=' AND a.AreaID ='.end($_REQUEST['AreaID']);
            }
            if($_REQUEST['SchoolID']){
                $map['SchoolID']=$_REQUEST['SchoolID'];
                $data .=' AND a.SchoolID ='.$_REQUEST['SchoolID'];
            }
            //状态
            if ($_REQUEST['Status']) {
                if(is_numeric($_REQUEST['Status'])){
                    $map['Status'] = $_REQUEST['Status'];
                }
                $data .= ' AND a.Status in ('.$_REQUEST['Status'].')';
            }
        }
        $where['data']=$data;
        $where['map']=$map;
        return $where;
    }

    /**
     * 通过注册IP编号获得用户操作统计
     * @author demo
     */
    public function userOperStatisticsByIP(){
        if(empty($_REQUEST['IPID'])){
            $this->setError('30301'); //数据标识不能为空！
        }
        //通过注册IP获得用户
        $UserIDS = $this->getModel('RegisterLog')->getList('UserID','IPID = '.(int)$_REQUEST['IPID']);
        foreach ($UserIDS as $idArray){
            $UserWhere[] = $idArray['UserID'];
        }
        $userData = array();
        if(!empty($UserWhere)){
            $UserWhere = ' UserID IN ('.implode(',', $UserWhere).') ';

            $userData = $this->getModel('User')->getInfoByWhere('UserName,RealName,Phonecode,Email,Points,
                LoadDate,Logins,LastTime,Cz,SubjectStyle,Whois,CheckPhone,CheckEmail,ComTimes,IfAuth,ExpNum',$UserWhere);

            $subjectSource = SS('subject');
            $tmp = array('<b style="color:red;">未认证</b> ','<b style="color:red;">认证中</b>','<b style="color:green;">已认证</b>','<b style="color:red;">认证未通过</b>');
            foreach($userData as $i=>$data){
                unset($userData[$i]['Password']);
                if($data['Phonecode']){
                    $userData[$i]['CheckPhone'] = $data['CheckPhone'] == 1 ? '<b style="color:green;">已验证</b>' : '<b style="color:red;">未验证</b>';
                }else{
                    $userData[$i]['CheckPhone'] = '未设置';
                }
                if($data['Email']){
                    $userData[$i]['CheckEmail'] = $data['CheckEmail'] == 1 ? '<b style="color:green;">已验证</b>' : '<b style="color:red;">未验证</b>';
                }else {
                    $userData[$i]['CheckEmail'] = '未设置';
                }
                if($data['Whois'] == 1){
                    $userData[$i]['IfAuth'] = '教师身份'.$tmp[2];
                }else{
                    $userData[$i]['IfAuth'] = '学生';
                }
                $tmpName = $subjectSource[$data['SubjectStyle']];
                $userData[$i]['SubjectName'] = $tmpName['ParentName'].$tmpName['SubjectName'];
            }
        }
        $this->assign('pageName','用户操作统计');
        $this->assign('UserData',$userData);
        $this->display();

    }
    /**
     * 发送短信
     * @author demo
     */
    public function sendSms() {
        $tpl=array('0'=>'请选择短信模板',
            '169264'=>'考霸联赛开赛通知');
        $this->assign('tpl',$tpl);
        $this->assign('pageName','发送短信');
        $this->display();
    }
}
