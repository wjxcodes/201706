<?php
/**
 * @author demo
 * @date 2015年3月20日
 */
/**
 * 自定义分组模型类，用于处理自定义分组相关操作
 */
namespace User\Manage;
class UserCustomGroupManage extends BaseController  {
    var $moduleName = '自定义分组管理';
    /**
     * 自定义分组列表
     * @author demo
     */
    public function index() {
        $pageName = '自定义分组管理';
        //获取检索条件
        $map = array ();
        $data = ' 1=1 ';
        if ($_REQUEST['name']) {//简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND GroupName like "%' . $_REQUEST['name'] . '%" ';
        } 
        $perpage = C('WLN_PERPAGE'); //每页显示数
        $userCustomGroup = $this->getModel('UserCustomGroup');
        $count = $userCustomGroup->selectCount($data,'GroupID'); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page = page($count,$_GET['p'],$perpage).','.$perpage;
        $list = $userCustomGroup->pageData(
                '*',
                $data,
                'GroupID DESC',
                $page);  
        $this->pageList($count, $perpage, $map);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加自定义分组
     * @author demo
     */
    public function add() {
        $pageName = '添加分组';
        $act = 'add'; //模板标识
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑自定义分组
     * @author demo
     */
    public function edit() {
        $groupID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty($groupID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑用户';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('UserCustomGroup')->selectData(
            '*',
            'GroupID=' . $groupID,
            '',
            1);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display('UserCustomGroup/add');
    }
    /**
     * 删除自定义分组
     * @author demo
     */
    public function delete() {
        $idStr = $_POST['id'];
        $data['CustomGroup']='0';
        //删除分组时，把该分组用户的分组默认成 0
        $buffer = $this->getModel('User')->updateData(
            $data,
            'CustomGroup in ( '.$idStr.')');

        if ($this->getModel('UserCustomGroup')->deleteData(
         'GroupID in ('.$idStr.')') === false) {
        $this->setError('30302'); //删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName, '删除用户Group为【' . $idStr . '】的自定义分组数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
    /**
     * 保存分组
     * @author demo
     */
    public function save() {
        $groupID = $_POST['GroupID'];
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($groupID) && $act == 'edit') {
            $this->setError('30301'); //数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223'); //模板标识不能为空！
        }
        //获取字段
        $data = array ();
        $data['GroupName'] = $_POST['GroupName'];
        $data['Description'] = $_POST['Description'];
        if ($act == 'add') {
            //检查分组名重复
            $buffer = $this->getModel('UserCustomGroup')->selectData(
                'GroupID',
                'GroupName = "'.$data['GroupName'].'"');
            if($buffer){
                $this->setError('1X431'); //分组名重复请更换！
            }
            $lastID = $this->getModel('UserCustomGroup')->insertData(
                $data);
            if ($lastID === false) {
                $this->setError('30310'); //添加失败！
            } else {
                //写入日志
                $this->adminLog($this->moduleName, '添加自定义分组【' . $_POST['GroupName'] . '】');
                $this->showSuccess('添加成功！', __URL__);
            }
        } else if ($act == 'edit') {
            $data['GroupID'] = $groupID;
            if ($this->getModel('UserCustomGroup')->updateData(
                $data,
                'GroupID='.$groupID) === false) {
                 $this->setError('30311'); //修改失败！
            } else {
                //写入日志
                $this->adminLog($this->moduleName, '修改组ID为【' . groupID . '】的数据');
                $this->showSuccess('修改成功！', __URL__);
            }
        }
    }
    /**
     * 浏览用户
     * @author demo
     */
    public function userList() {
        $pageName = '自定义分组用户浏览';
        $map = array ();
        $data = ' 1=1 ';
        if ($_REQUEST['name']) {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND UserName like "%' . $_REQUEST['name'] . '%" ';
        } else {
            //高级查询
            if ($_REQUEST['UserName']) {
                $map['UserName'] = $_REQUEST['UserName'];
                $data .= ' AND UserName like "%' . $_REQUEST['UserName'] . '%" ';
            }
            if ($_REQUEST['RealName']) {
                $map['RealName'] = $_REQUEST['RealName'];
                $data .= ' AND RealName like "%' . $_REQUEST['RealName'] . '%" ';
            }
        }
        $data .= ' AND CustomGroup = "'.$_GET['id'].'"';
        $list = $this->getModel('User')->selectData(
            "*",
            $data,
            'UserID DESC'
            );
        //获取用户组数据
        $groupArray=array();//用户组数据集
        $defaultArray=array();//默认数据集
        $powerArray=array();//无需验证时间数据集
        $buffer=$this->getModel('PowerUser')->selectData(
            '*',
            '1=1');
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
        $userGroupInfo = $this->getModel('UserGroup')->selectData(
            'UserID,GroupID,GroupName,LastTime',
            'UserID in ('.implode(',',$userArray).')'
        );
        $userArray=array();
        foreach($userGroupInfo as $i=>$iUserGroupInfo){
            $userArray[$iUserGroupInfo['UserID']][]=$iUserGroupInfo;
        }
        //载入组名和到期时间
        $groupName = $this->getModel('PowerUser','getGroupName');
        foreach($list as $i=>$iList){
            $list[$i]['UserCustomGroup']=$this->getModel('UserCustomGroup')->findData(
                'GroupName',
                'GroupID ='.$iList['CustomGroup']
            )['GroupName'];
            $gpArray=array();//记录分组数据
            if($userArray[$iList['UserID']]){
                $list[$i]['EndTime'] = $userArray[$iList['UserID']][0]['LastTime'];
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
                    $tmpStr='';
                    foreach($gpArray as $j=>$jGpArray){
                        $tmpStr.=$groupName[$j].'：'.implode('、',$jGpArray).'<br/>';
                    }
                    $list[$i]['UserGroup'] = $tmpStr;
                }
            }
        }
        /*载入模板标签*/
        //分组用户数
        $groupTotal=count($list);
        $this->assign('list',$list);
        $this->assign('id',$_GET['id']);
        $this->assign('totalMsg','该分组共有'.$groupTotal.'个用户');
        $this->assign('pageName',$pageName);
        $this->display();
    }
    /**
     * 锁定用户
     * @author demo
     */
    public function locked() {
        $data = array ();
        $Status = $_GET['Status'];
        $userID = $_GET['id']; //获取数据标识
        if(!isset($Status)) $Status=1;
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
            $this->showSuccess('更改状态成功！');
        }
    }
    /**
     * 批量修改用户到期时间、所属组、当前状态
     * @author demo
     */
    public function editArr(){
        if(!$_REQUEST['id']){
            $this->setError('30301'); //数据标识不能为空！
        }
        $userIDArr=$_REQUEST['id'];
        $pageName = '批量修改用户权限';
        $userPowerTmp =SS('powerUserId');
        $groupName = $this->getModel('PowerUser','getGroupName');
        $defaultStr = '';
        foreach($userPowerTmp as $i=>$iUserPowerTmp){
            $userPower[$iUserPowerTmp['GroupName']][$i]=$iUserPowerTmp;
            $powerName[$iUserPowerTmp['GroupName']] = $groupName[$iUserPowerTmp['GroupName']];
            if($iUserPowerTmp['IfDefault']==1){
                $defaultStr .= ','.$iUserPowerTmp['PUID'];
            }
        }
        //获取自定义分组
        $userID=end(explode(',',$userIDArr));
        $userModel = $this->getModel('User');
        $customGroupName = $userModel->unionSelect(
            'userCustomGroupSelectByUserId',
            $userID
        );
        $userNameArr = $userModel->selectData(
            'UserName',
            'UserID in('.$userIDArr.')');

        $defaultStr = ltrim($defaultStr,',');
        //获取学科数据集
        /*载入模板标签*/
        $this->assign('idArr',$userIDArr);
        $this->assign('powerName',$powerName);
        $this->assign('groupMsg',$customGroupName[0]);
        $this->assign('userName',$userNameArr);
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('default',$defaultStr);//默认权限组
        $this->assign('userPower',$userPower);//用户组权限
        $this->display();
    }
    /**
     * 修改用户自定义分组
     * @author demo
     */
    public function editMsg(){
        $pageName = '分配修改用户自定义分组';
        if(!$_REQUEST['UserID']){
            $this->setError('30301');
        }
        $userID=$_REQUEST['UserID'];
        $userCustomGroup = $this->getModel('UserCustomGroup');
        $userMsg = $this->getModel('User')->selectData(
            '*',
            'UserID='.$userID);
        $userGroupName = $userCustomGroup->selectData(
            '*',
            'GroupID='.$userMsg[0]['CustomGroup']
        );
        $allGroup = $userCustomGroup->selectData(
            '*',
            '1=1',
            'GroupID DESC');
        if($userGroupName){
            $userMsg[0]['CustomGroupName']=$userGroupName[0]['GroupName'];
        }else{
            $userMsg[0]['CustomGroupName']='未设置';
        }


        /*载入模板标签*/
        $this->assign('userMsg',$userMsg[0]);//用户信息
        $this->assign('userCustomGroup',$allGroup);//用户信息
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 批量保存用户修改(到期时间、所属组、当前状态)
     * @author demo
     */
    public function saveArr(){
        $endTime = $_POST['EndTime']; 
        $idArr = explode(',',$_POST['idStr']);
        $userGroup = $this->getModel('UserGroup');
        if(!isset($Status)) $Status=1;
        $data['Status'] = $_POST['status'];
        $this->getModel('User')->updateData(
            $data,
            'UserID in ('.$_POST['idStr'].')');
        foreach($idArr as $i=>$iIdArr){
            $buffer = $userGroup->selectData(
                'UserID,UGID,GroupID,GroupName',
                'UserID in ('.$idArr[$i].')');
            $groupArr = array();
            if($buffer){
                foreach($buffer as $j=>$iBuffer){
                    $groupArr[$iBuffer['GroupName']][]=$iBuffer;
                }
            }
            $data = array ();
            $data['UserID'] = $idArr[$i];
            $data['AddTime'] = time();
            $data['LastTime'] = strtotime($endTime);
            $group = $this->getModel('PowerUser','getGroupName');
            foreach($group as $j => $iGroup){
                $data['GroupName'] = $j;
                $id = $_POST['groupname_'.$j];
                if(is_array($id)){
                    $n = 0;//计数
                    foreach($id as $k => $jID){
                        $data['GroupID'] = $jID;
                        if($groupArr[$j] && $n<count($groupArr[$j])){
                            $data['UGID'] = $groupArr[$j][$n]['UGID'];
                            $result = $this->getModel('UserGroup')->updateData(
                                $data,
                                'UGID='.$groupArr[$j][$n]['UGID']);
                            $n++;
                        }else{
                            unset($data['UGID']);
                            $result = $userGroup->insertData($data);
                        }
                    }
                    if($groupArr[$j] && $k<count($groupArr[$j])){
                        $uGid = array();
                        for(;$k<count($groupArr[$j]);$k++){
                            $ugid[] = $groupArr[$j][$k]['UGID'];
                        }
                        $result = $userGroup->deleteData(
                            'UGID in ('.implode(',',$uGid).')');
                    }
                }else if($id){
                    $data['GroupID'] = $id;
                    if($groupArr[$j]){
                        $data['UGID'] = $groupArr[$j][0]['UGID'];
                        $result = $userGroup->updateData(
                            $data,
                            'UGID='.$groupArr[$j][0]['UGID']);
                    }else{
                        $result = $userGroup->insertData(
                            $data);
                    }
                }else{
                    $result = $userGroup->deleteData(
                        'UserID ='.$idArr[$i].' and GroupName='.$j);
                }
            }
            if ($result === false) {
                $this->setError('1X432'); //更改包月失败！
            } 
        }
        //写入日志
        $this->adminLog($this->moduleName, '修改组ID为【'.$_POST['groupID'].'】的用户权限');
        $this->showSuccess('更改成功！','__MODULE__-User');
    }

    /**
     * 保存用户自定义组
     * @author demo
     */
    public function saveCustomGroup(){
        $userID=$_REQUEST['UserID'];
        if(!$userID){
            $this->setError('30301');//缺少参数或标识
        }
        $data['CustomGroup']=$_POST['CustomGroupID'];
        $result=$this->getModel('User')->updateData($data,'UserID ='.$userID);
        if($result){
            $this->showSuccess('用户自定义分组修改成功！','__URL__');
        }else{
            $this->setError('30311'); //分组修改失败！
        }
    }

    /**
     * 将用户从自定义分组中移除
     * @author demo
     */
    public function remove(){
        $userID=$_REQUEST['UserID'];
        if(!$userID){
            $this->setError('30301');//缺少参数或标识
        }
        $data['CustomGroup']=0;
        $result=$this->getModel('User')->updateData(
            $data,
            'UserID='.$userID
        );
        if($result){
            $this->showSuccess('用户自定义分组移除成功！','__URL__');
        }else{
            $this->setError('30311'); //分组修改失败！
        }
    }
    /**
     * 批量设置权限
     * @author demo
     */
    public function saveUserMsg(){
        if(empty($_REQUEST['UserID'])){
            $this->setError('30301');//缺少参数或标识
        }
        $data = array ();
        if($_POST['EndTime']){
            $endTime = $_POST['EndTime'];
            if(!checkString('isDate',$endTime)){
                $this->setError('1X433'); //日期格式有误！
            }
            $data['LastTime'] = strtotime($endTime);
        }
        $data['groupname_1']=$_POST['groupname_1'];
        $data['groupname_2']=$_POST['groupname_2'];
        $data['groupname_3']=$_POST['groupname_3'];
        $userIdArr=explode(",",$_POST['UserID']);
        $id=$this->getModel('User')->selectData('CustomGroup','UserID='.end($userIdArr));
        $user=$this->getModel('User');
        foreach($userIdArr as $i=>$iUserIdArr){
            $result=$user->updateMonth($iUserIdArr,$data);
            if(!$result){
                $msg[]=$iUserIdArr;
            }
        }
        $idStr=implode(',',$msg);
        if(count($msg)>0){
            $this->setError('1X434',0,'',$idStr);//修改权限组失败！
        }else{
            $this->showSuccess('修改权限组成功！','__URL__/userList/id/'.$id[0]['CustomGroup']);
        }
    }
    /**
     * 批量加入用户至指定分组中
     * @author demo
     */
    public function addUser(){
        $pageName='批量为该分组加入用户';
        $groupID=$_REQUEST['id'];
        if(!$groupID){
            $this->setError('30301');//缺少参数或标识
        }
        $groupMsg=$this->getModel('UserCustomGroup')->selectData(
            'GroupID,GroupName',
            'GroupID='.$groupID
        );
        /*载入模板标签*/
        $this->assign('groupMsg',$groupMsg[0]);
        $this->assign('pageName', $pageName);
        $this->display();
    }
    /**
     * 执行添加用户到分组
     * @author demo
     */
    public function updateUserCustomGroup(){
        //判断是否有权限添加学生
        $groupID=$_REQUEST['GroupID'];
        $con=formatString('stripTags',$_POST['UserName']);
        $arr=array();
        //根据回车分割换行
        if($con){
            $arr=explode("\n",str_replace("\r",'',$con));
            $arr=array_filter($arr);
            if(count($arr)==0) $this->setError('30304',1);
        }else{
            $this->setError('30304',1);
        }
        $nameStr=implode("','",$arr);
        $userMsg=$this->getModel('User')->selectData( //根据提供的用户名查询对应用户信息
            'UserName,CustomGroup',
            'UserName in (\''.$nameStr.'\')'
        );
        foreach($userMsg as $j=>$jUserMsg){ //对结果进行处理
            $newMsg[]=$jUserMsg['UserName']; //提供且存在的用户
        }
        foreach($arr as $i=>$iArr){ //踢出未能查询出结果的用户，做返回提示内容
            if(!in_array($iArr,$newMsg)){
                $errorName[]=$iArr;
            }
        }
        if(!empty($newMsg)){
            $updateNameStr=implode("','",$newMsg);
            $this->getModel('User')->updateData( //根据提供的用户名查询对应用户信息
                'CustomGroup='.$groupID,
                'UserName in (\''.$updateNameStr.'\')'
            );
        }
        $result['errorName']=$errorName;
        $result['successName']=$newMsg;
        $this->setBack($result,1);
    }
}
