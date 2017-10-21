<?php
/**
 * @author demo
 * @date 2014年10月31日
 */

/**
 * 用户所属组管理类，用于管理用户所属组
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class UserGroupModel extends BaseModel {
    /**
     * 新用户注册时，添加相关的用户组信息
     * @param int $userid 用户id
     * @param array $ipUser ip用户数据
     * @param int $groupid 站点分组id
     * @param booelean 操作成功返回true
     * @author demo 2015-9-1
     */
    public function addDefaultGroupAtRegistration($userid, $ipUser, $groupid=1){
        $userid = (int)$userid;
        $groupid = (int)$groupid;
        if($userid === 0 || $groupid === 0){
            return false;
        }
        
        $config = $this->getConfigInfo();
        //如果集体用户数据不为空，则证明该用户在相关ip下注册，查询出对应$groupid的用户组编号
        if(false){ //去除用户在相关ip地址注册的处理
        // if(!empty($ipUser)){
            $cache = SS('powerUserGroup')[$groupid]['groupList'];
            $group = 0;
            $list = explode(',', $ipUser['PUID']);
            foreach($list as $value){
                if(array_key_exists($value, $cache)){
                    $group = (int)$value;
                    break;
                }
            }
            //此处当查询的试题为空时，将使用个人用户组的权限
            if(!empty($group)){
                $config = array(
                    'GroupID' => $group,
                    'LastTime' => $ipUser['LastTime']
                );
            }
        }
        //兼容当相关配置文件为空时，不进行相关操作
        if(empty($config['GroupID']) || empty($config['LastTime'])){
            return true;
        }
        return $this->saveDefaultGroup($userid, $config, $groupid);
    }

    /**
     * 保存指定用户的组
     * @param int $userid 用户id
     * @param array $config 相关配置文件 格式：array('GroupID'=>xxx, 'LastTime'=>'xxxx')
     * @param int $groupid 分组编号
     * @return boolean
     * @author demo 2015-9-24
     */
    public function saveDefaultGroup($userid, $config, $groupid=1){
        $result = $this->selectData( 'UGID', "UserID={$userid} AND GroupName={$groupid}");
        $result = !empty($result[0]);
        if($result){
            $result = $this->updateData(
                $config,
                "UserID={$userid} AND GroupName={$groupid}"
            );
        }else{
            $config['UserID'] = $userid;
            $config['GroupName'] = $groupid;
            $config['AddTime'] = time();
            $result = $this->insertData(
                $config
            );
        }
        if($result === false){
            return false;
        }
        return true;
    }

    /**
     * 获取用户权限组信息
     * @param array $where 条件
     * @param string $field 返回的数据
     * @return mixed
     * @author demo
     */
    public function getGroupByWhere($where,$field){
        $data=$this->selectData(
            $field,
            $where,
            'GroupName asc'
        );
        return $data;
    }

    /**
     * 返回默认组的相关信息
     * @return array
     * @author demo 2015-9-1
     */
    protected function getConfigInfo(){
        $specialConfig = C('WLN_SPECIAL_INTERVAL_REGISTER_GROUP');
        $config = C('WLN_REGISTER_GROUP'); //获取配置信息
        $day = 60 * 60 * 24;
        $current = time();
        $specialBeginTime = strtotime($specialConfig['beginTime']);
        $specialEndTime = strtotime($specialConfig['endTime']);
        unset($specialConfig['beginTime'], $specialConfig['endTime']);
        
        //检查指定注册时间段的起始时间和截止时间
        if($specialEndTime <= $specialBeginTime){
            $specialEndTime = $specialBeginTime + $day * 7;
        }
        //查看当前时间是否为指定注册时间段
        if($specialBeginTime <= $current && $specialEndTime > $current){
            $config = $specialConfig;
        }
        //兼容php的时间加减
        if(is_string($config['day'])){
            $lastTime = strtotime($config['day'], strtotime(date('Y-m-d 0:0:0', $current)));
        }else{
            $lastTime = (strtotime(date('Y-m-d 0:0:0', $current)) + $day * ((int)$config['day']) - 1);
        }
        // $lastTime = ($current + 60);测试
        return array(
            'GroupID' => (int)$config['group'],
            'LastTime' => $lastTime
        );
    }

    /**
     * 获取提分系统默认用户权限组
     * @return array 默认用户组信息
     * @author demo
     */
    public function getDefaultGroup() {
        $groupList=SS('powerUserGroup');
        $buffer=array();
        foreach($groupList[2]['groupList'] as $iGroupList){
            if($iGroupList['IfDefault']==1){
                $buffer=$iGroupList['TagArr'];
                return $buffer;
            }
        }
        return $buffer;
    }

    /**
     * 获取用户权限列表
     * @return array 用户权限列表
     * @author demo
     */
    public function getAuthList($userID) {
        //根据用户ID和提分系统标识查找该用户的权限组
        $groupWhere = array('UserID' => $userID, 'GroupName' => 2);
        $groupList = $this->getModel('UserGroup')->selectData(
            'GroupID',
            $groupWhere
        );
        if (!$groupList||$groupList[0]['GroupID']==0) {//如果该用户没有存在的用户组则将默认用户组的权限赋给该用户
            $buffer = $this->getDefaultGroup();
        } else {
            $listID=SS('powerUserGroup');
            $buffer=$listID[2]['groupList'][$groupList[0]['GroupID']]['TagArr'];
        }
        $powerUserList=SS('powerUserList');
        $powerList=[];
        foreach($buffer as $iBuffer){
            $powerList[]=$powerUserList[$iBuffer];
        }
        return $powerList;
    }
}