<?php
/**
 * 权限验证接口
 * @author demo 2015-1-5
 */
namespace Common\Api;
class PowerApi extends CommonApi{

    /**
     * 获取home分组路径对应权限
     * @param array $pageTag 当前操作 例如 'Dir/Index/saveSysTemplateList'
     * @param array $username 用户名 默认当前登录用户
     * @return bool
     * @author demo
     */
    public function homeCheckThisPower($pageTag,$userID=''){
        $rules = array(); //验证规则 作为返回值

        $powerData = SS('powerUserByID');//ID为键用户权限数组
        if(empty($userID)) $userID = $this->getCookieUserID();
        $userData = $this->getModel('UserGroup')->selectData(
            'GroupID,LastTime',
            'GroupName=1 AND UserID="'.$userID.'"');
        $pageTag = $this->getModel('PowerUserList')->powerVerify($pageTag, $userData[0]);
        if($pageTag===true){
            return true;
        }

        $rules['pageTag']=$pageTag;

        //获取默认组
        $default = SS('powerUserId');
        foreach($default as $i=>$iDefault){
            if($iDefault['IfDefault']==1 && $iDefault['GroupName']==1){
                $defaultGroup=$iDefault['PUID'];
                $listIDArray=explode(',',$iDefault['ListID']);
            }
        }
        foreach($listIDArray as $i=>$iListIDArray){
            $defaultGroupList[$i]=$powerData[$iListIDArray];  //默认组权限数据
        }
        if(!$userData[0]['GroupID']){
            $userGroupList = $defaultGroupList;
        }else{
            $groupID = $userData[0]['GroupID'];
            $userEndTime = $userData[0]['LastTime'];
            if($default[$groupID]['ListID'] === 'all'){
                return true;//是管理员则跳过
            }
            $userGroupListTmp = explode(',',$default[$groupID]['ListID']);
            foreach($userGroupListTmp as $i=>$iUserGroupListTmp){
                $userGroupList[$iUserGroupListTmp] = $powerData[$iUserGroupListTmp];
            }
            //判断特殊权限到期,使用默认权限组权限
            $time = time();
            if($time >= $userEndTime && $groupID!=$defaultGroup){
                $userGroupList = $defaultGroupList;
            }
        }
        //IP所属权限组
        $ip = ip2long(get_client_ip(0,true));//客户端ip
        $ipData = $this->getModel('UserIp')->selectData(
            'PUID,LastTime',
            'IPAddress="'.$ip.'"');
        if($ipData){
            $ipPuidStr = $ipData[0]['PUID'];
            $ipPuidArr = explode(',',$ipPuidStr);
            $ipPuid = $ipPuidArr[0];
            $ipLastTime = $ipData[0]['LastTime'];
            $ipGroupList = '';
            //特殊权限到期,使用默认权限组权限
            $time = time();
            if($time >= $ipLastTime && $ipPuid!=$defaultGroup){
                $ipGroupList=$defaultGroupList;
                $ipPuid = $defaultGroup;
            }
            if($ipPuid == $defaultGroup){
                $ipGroupList = $defaultGroupList;
                //$ipPuid = $defaultGroup;
            }else if($ipGroupList == ''){
                $ipGroupTmp=$default[$ipPuid];
                $ipGroupListArr=explode(',',$ipGroupTmp['ListID']);
                foreach($ipGroupListArr as $i=>$iIpGroupListArr){
                    $ipGroupList[$i] = $powerData[$iIpGroupListArr];
                }
            }
        }

        //获取用户最高权限
        $powerValue = '';//权限值
        foreach($userGroupList as $i=>$iUserGroupList){
            if($pageTag == $iUserGroupList['PowerTag']){
                if($iUserGroupList['Value']=='all'){
                    $powerValue = 'all';
                    break;
                }
                if(empty($rules['unit']) || $rules['unit']>(int)$iUserGroupList['Unit']){
                    $powerValue = $iUserGroupList['Value'];
                    $rules['unit'] = (int)$iUserGroupList['Unit']; //获取查询计算周期 2015-8-19
                }else if($rules['unit']==(int)$iUserGroupList['Unit']){
                    if($powerValue<$iUserGroupList['Value']) $powerValue = $iUserGroupList['Value'];
                }
            }
        }

        if($powerValue==='all')
            return true;//权限值足够

        foreach($ipGroupList as $i=>$iIpGroupList){
            if($pageTag == $iIpGroupList['PowerTag']){
                if($iIpGroupList['Value']=='all'){
                    $powerValue = 'all';
                    break;
                }
                if(empty($rules['unit']) || $rules['unit']>(int)$iIpGroupList['Unit']){
                    $powerValue = $iIpGroupList['Value'];
                    $rules['unit'] = (int)$iIpGroupList['Unit'];
                }else if($rules['unit']==(int)$iIpGroupList['Unit']){
                    if($powerValue<$iIpGroupList['Value']) $powerValue = $iIpGroupList['Value'];
                }
            }
        }

        //当权限为all，或者没有指定权限的时候，则通过验证
        if($powerValue==='all')
            return true;//权限值足够
        $rules['value'] = $powerValue;
        return $rules;
    }
}