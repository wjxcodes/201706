<?php
/**
 * 用户数据
 * @author demo 16-4-11
 */
namespace Common\Api;
class UserApi extends CommonApi{
    /**
     * 获取用户所在学校
     * @author demo 16-4-11 
     */
    public function getUserSchool($userid,$field=''){
        return $this->getModel('User')->getUserSchool($userid, $field='');
    }

    /**
     * 获取用户信息
     * @author demo 16-4-11
     */
    public function getUser($userid, $field=''){
        return $this->getModel('User')->getInfoByID($userid, $field);
    }

    /**
     * 返回用户所在用户组的权限id列表
     * @author demo 16-4-13
     */
    public function powerUserId(){
        return SS('powerUserId');
    }

    /**
     * 返回任务列表的数据
     * @author demo 16-4-13
     */
    public function expList(){
        return SS('expList');
    }

    /**
     * 
     * @author demo 16-4-14
     */
    public function powerUserGroup(){
        return SS('powerUserGroup');
    }
}