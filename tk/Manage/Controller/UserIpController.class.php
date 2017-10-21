<?php
/**
 * @author demo 
 * @date 2015年1月7日
 */
/**
 * 用户ip管理类，用于配置用户ip
 */
namespace Manage\Controller;
class UserIpController extends BaseController  {
    var $moduleName='用户IP管理';
    /**
     * 浏览用户ip列表
     * @author demo 
     */
    public function index(){
        $pageName = "用户IP管理";
        $where=$this->getWhere($_REQUEST);
        $userIPModel = $this->getModel('UserIp');
        $count = $userIPModel->selectCount(
            $where['data'],
            'IPID',
            'ip'); // 查询满足要求的总记录数
        $perpage = C('WLN_PERPAGE');
        $page = page($count,$_GET['p'],$perpage);
        $page = $page.','.$perpage;
        $info = $userIPModel->unionSelect(
            'getUserIPByPage',
            $where['data'],
            $page
        );
        $group = $this->getModel('PowerUser')->getGroupName();;
        //以下部分可被缓存代替
        $puIDArray=array(); //存储用户权限id
        foreach($info as $i=>$iInfo){
            $puIDArray=array_merge(explode(',',$iInfo['PUID']),$puIDArray);
        }
        $puIDListArr=array_unique($puIDArray);
        $powerUserId=SS('powerUserId');
        foreach($puIDListArr as $iPuIDList){
            if($powerUserId[$iPuIDList]){
                $infoArray[]=$powerUserId[$iPuIDList];
            }
            if($powerUserId[$iPuIDList]['IfDefault']==1){
                $defaultGroup[]=$powerUserId[$iPuIDList];
            }
        }
        $puIDArray=array(); //存储用户权限名称 以id为键
        foreach($infoArray as $iInfoArray){
            $puIDArray[$iInfoArray['PUID']]=$iInfoArray;
        }

        //获取默认组
        $defaultArray=array();
        $defaultAll=array();
        foreach($defaultGroup as $iDefaultGroup){
            $defaultArray[$iDefaultGroup['GroupName']]=$iDefaultGroup;
            $defaultAll[]=$iDefaultGroup['PUID'];
        }
        //以上部分可被缓存代替
        foreach($info as $i=>$iInfo){
            $idArr = explode(',',$iInfo['PUID']);
            foreach($idArr as $j=>$iIdArr){
                $groupName = $group[$puIDArray[$iIdArr]['GroupName']];
                if($info[$i]['LastTime']<time()){
                    $info[$i]['UserGroup'] .= $groupName.':'.$defaultArray[$puIDArray[$iIdArr]['GroupName']]['UserGroup']."<span style='color:red'>(已过期)</span>　";
                    $info[$i]['IfReg']=0;
                }else{
                    $info[$i]['UserGroup'] .= $groupName.':'.$puIDArray[$iIdArr]['UserGroup'].'　';
                }
            }

            $info[$i]['AddTime'] = date("Y-m-d",$iInfo['AddTime']);
            $info[$i]['IPAddress']=long2ip($iInfo['IPAddress']);
            $info[$i]['LastTime']=date("Y-m-d",$iInfo['LastTime']);
        }
        $this->pageList($count, $perpage, $where['map']);
        $this->assign("info",$info);
        $this->assign("pageName",$pageName);
        $this->display();
    }

    /**
     * 添加用户ip
     * @author demo
     */
    public function add() {
        $pageName = '添加用户IP';
        $act = 'add'; //模板标识
        $group=$this->getModel('PowerUser')->getGroupName();
        $groupResult = array();
        foreach($group as $i=>$iGroup){
            $groupResult[$i] = $iGroup;
        }
        $powerUserId=SS('powerUserId');
        foreach($powerUserId as $iPowerUserId){
            if($iPowerUserId['IfDefault']==1){
                $defaultStrTmp[]=$iPowerUserId;
            }
            if($iPowerUserId['ListID']!='all'){
                $userPowerTmp[]=$iPowerUserId;
            }
        }
        $defaultStr = '';
        foreach($defaultStrTmp as $i=>$iDefaultStrTmp){
            $defaultStr['PUID'] .= ','.$iDefaultStrTmp['PUID'];
        }
        $defaultStr['PUID'] = ltrim($defaultStr['PUID'],',');
        foreach($userPowerTmp as $i=>$iUserPowerTmp){
            $userPower[$iUserPowerTmp['GroupName']][$i]=$iUserPowerTmp;
        }
        //获取地区
        $param['style']='area';
        $param['pID']=0;
        $areaArray=$this->getData($param);
        /*载入模板标签*/
        $this->assign('arrArea',$areaArray);//地区
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('edit',$defaultStr);
        $this->assign('groupResult',$groupResult);
        $this->assign('userPower',$userPower);
        $this->display();
    }

    /**
     * 编辑用户ip
     * @author demo
     */
    public function edit(){
        $pageName = '编辑用户IP';
        $PUID = $_GET['id'];
        $act = 'edit'; //模板标识
        $userIp = $this->getModel('UserIp');
        $group=$this->getModel('PowerUser')->getGroupName();
        foreach($group as $i=>$iUserGroupArr){
           $groupResult[$i] = $iUserGroupArr;
        }
        $edit=$userIp->selectData(
            '*',
            'IPID='.$PUID);
        $edit[0]['IPAddress'] = long2ip($edit[0]['IPAddress']);
        $edit[0]['AddTime']=date('Y-m-d',$edit[0]['AddTime']);
        $edit[0]['LastTime']=date("Y-m-d",$edit[0]['LastTime']);
        $powerUserId=SS('powerUserId');
        foreach($powerUserId as $iPowerUserId){
            if($iPowerUserId['ListID']!='all'){
                $userPowerTmp[]=$iPowerUserId;
            }
        }
        foreach($userPowerTmp as $i=>$iUserPowerTmp){
            $userPower[$iUserPowerTmp['GroupName']][$i]=$iUserPowerTmp;
        }
        //获取地区
        $param['style']='area';
        $param['pID']=0;
        $areaArray=$this->getData($param);
        /*载入模板标签*/
        $areaParent=$this->getModel('Area')->getAreaStr($edit[0]['AreaID']);
        //根据地区ID,查询该ID下的所有学校
        $schoolList=$this->getModel('School')->selectData('SchoolID,SchoolName','AreaID='.$edit[0]['AreaID']);
        $this->assign('arrArea',$areaArray);//地区
        $this->assign('areaParent', $areaParent); //父类数据
        $this->assign('schoolList',$schoolList);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('groupResult',$groupResult);
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('userPower',$userPower);
        $this->display("UserIp/add");
    }

    /**
     * 保存用户ip
     * @author demo
     */
    public function save(){
        $ipId=$_POST['IPID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($ipId) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        $data=array();

        $data['AddTime'] = time();
        $rep = "/^(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[0-9]{1,2})(\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[0-9]{1,2})){3}$/";
        if(!preg_match($rep,$_POST['ipAddress'])){
            $this->setError('30825'); //IP地址格式错误
        }
        $data['IPAddress']=ip2long($_POST['ipAddress']);
        $data['Description']=$_POST['Description'];
        $data['Remark']=$_POST['Remark'];
        $data['IPID']=$_POST['IPID'];
        $data['IfReg']=$_POST['IfReg'];

        $data['LastTime'] = strtotime($_POST['EndTime']);
        $data['SchoolID']=$_POST['areaid_school'];
        $data['AreaID']=end($_POST['AreaID']);
        //获取地区和学校
        $buffer=SS('areaParentPath');  // 缓存父类路径path数据
        $buffer2=SS('areaList');
        if($buffer[$data['AreaID']]){
            foreach($buffer[$data['AreaID']] as $bb){
                $data['Description'].=$bb['AreaName'];
            }
        }
        if($buffer2[$data['AreaID']])  $data['Description'].=$buffer2[$data['AreaID']]['AreaName'];
        else  $data['Description'].='';
        $data['Description'].=$_POST['schoolname']; //处理地区学校为描述
        $data['Remark']=$_POST['remark']; //备注信息
        if(!checkString('isDate',$_POST['EndTime'])){
            $this->setError('30719'); //日期格式有误！
        }
        $groupData=$this->getModel('PowerUser')->getGroupName();
        foreach($groupData as $i=>$iGroupData){
            $group = '';
            if(empty($_POST['group_'.$i])){
                continue;
            }
            if($i!=3){
                $group = $_POST['group_'.$i];
            }else{
                $group = join(',',$_POST['group_'.$i]);
            }
            $data['PUID'] .= ','.$group;
        }
        $data['PUID']=ltrim($data['PUID'],',');
        if($act=='add'){
            if($this->getModel('UserIp')->selectData(
                'IPID',
                'IPAddress='.$data['IPAddress'])){
                $this->setError('10803'); //此ip已存在！
            }
            if($this->getModel('UserIp')->insertData(
                    $data)===false){
                $this->setError('30310'); //添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加用户IP【'.$_POST['ipAddress'].'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        }else if($act=='edit'){
            $data['IPID']=$ipId;
            if($this->getModel('UserIp')->updateData(
                    $data,
                    'IPID ='.$ipId)===false){
                $this->setError('30311'); //修改失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改IPID为【'.$ipId.'】的数据');
                $this->showSuccess('修改成功！',__URL__);
            }
        }
    }

    /**
     * 删除用户ip；
     * @author demo
     */
    public function delete() {
        $ipId=$_POST['id'];    //获取数据标识
        if(!$ipId){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if($this->getModel('UserIp')->deleteData('IPID in ('.$ipId.')')===false){
            $this->setError('30301'); //删除失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除IPID为【'.$ipId.'】的数据');
            $this->showSuccess('删除成功！',__URL__);
        }
    }
    /**
     *@IP开通记录导出
     *@author demo
     */
    public function export(){
        $where=$this->getWhere($_REQUEST);
        //写入日志
        $this->adminLog($this->moduleName,'导出日志记录where【'.$where['data'].'】');
        $userIPModel = $this->getModel('UserIp');
        $count = $userIPModel->selectCount(
            $where['data'],
            'IPID'); // 查询满足要求的总记录数
        $perpage = 2000;
        if($count>2000 && empty($_REQUEST['p'])){
            $count = $userIPModel->selectCount(
                $where['data'],
                'IPID'); // 查询满足要求的总记录数
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
        $logData = $userIPModel->pageData(
            '*',
            $where['data'],
            'IPID DESC',
            $page);
        $result=$this->getModel('User')->groupData(
            'LastIP,count(*) as total',
            '1=1 and LastIP<>"" ',
            'LastIP'
        );
        if ($logData) {
            foreach ($logData as $i => $value) {
                foreach($result as $j=>$jResult){
                    if($logData[$i]['IPAddress']==ip2long($result[$j]['LastIP'])){
                        $logData[$i]['total']=$result[$j]['total'];
                    }
                }
            }
        }
        foreach ($logData as $i=>$iLogData){
            $total[$i] = $iLogData['total'];
        }
        array_multisort($total,SORT_NUMERIC,SORT_DESC,$logData); //根据总数倒序排列
        foreach($logData as $i=>$iLogData){
            unset($iLogData['IPID']);
            unset($iLogData['PUID']);
            unset($iLogData['LastTime']);
            unset($iLogData['IfReg']);
            unset($iLogData['AreaID']);
            unset($iLogData['SchoolID']);
            unset($iLogData['Remark']);
            $iLogData['AddTime']=date('Y-m-d H:i:s', $iLogData['AddTime']);
            $iLogData['IPAddress']=long2ip($iLogData['IPAddress']);
            $logData[$i]=array_values($iLogData);
        }
        $keyName=array('IP地址','开通时间','学校名称','开通数量');
        $keyWidth=array('20','20','60','10');
        $excelName=array('title'=>'IP开通学校信息列表','excelName'=>'IP开通学校信息导出Excel');
        $excelMsg=R('Common/SystemLayer/resetExcelMsg',array($keyName,$keyWidth));
        R('Common/SystemLayer/excelExport',array($excelMsg,$logData,$excelName));
    }
    /**
     * 获取查询条件
     * @param $where array
     * @return array
     * @author demo
     */
    private function getWhere($where){
        $map = array();
        $data = '1=1';
        //简单查询
        if($_REQUEST['ip']){
            $ip=preg_replace("/\s/","",$_REQUEST['ip']);
            if(!empty($ip)){
                $map['IPAddress']=$ip;
                $data .= ' AND ip.IPAddress='.ip2long($ip);
            }
        }else{
            if($_REQUEST['ipAddress']){
                $ip=preg_replace("/\s/","",$_REQUEST['ipAddress']);
                if(!empty($ip)){
                    $map['IPAddress']=$ip;
                    $data .= ' AND ip.IPAddress='.ip2long($ip);
                }
            }
            $start = $_REQUEST['Start'];
            if(strstr($start,'-')){
                $start=strtotime($start);
            }
            $end = $_REQUEST['End'];
            if(strstr($end,'-')){
                $end=strtotime($end);
            }
            if ($start){
                if (empty ($end)) $end = time();
                $map['Start'] = $start;
                $map['End'] = $end;
                $_REQUEST['Start']=date('Y-m-d',$start);
                $_REQUEST['End']=date('Y-m-d',$end);
                $data .= ' AND ip.LastTime between ' . ($start) . ' and ' . ($end) . ' ';
            }
            if($_REQUEST['IfReg']){
                if($_REQUEST['IfReg']!=''){
                    if($_REQUEST['IfReg']==1){
                        $map['IfReg']=$_REQUEST['IfReg'];
                        $data .= ' AND ip.IfReg='.$_REQUEST['IfReg'].' AND ip.LastTime >'.time();
                    }else{
                        $map['IfReg']=$_REQUEST['IfReg'];
                        $data .= ' AND ip.IfReg='.$_REQUEST['IfReg'].' or ip.LastTime <'.time();
                    }
                }
            }
            if($_REQUEST['Description']){
                $map['Description']=$_REQUEST['Description'];
                $data .= ' AND ip.Description LIKE  "%'.$_REQUEST['Description'] . '%" ';
            }
        }
        $where['data']=$data;
        $where['map']=$map;
        return $where;

    }
}