<?php
/**
 * @author demo
 * @date 2015年8月8日
 */

/**
 * 用户登录错误日志Model类，用于处理用户登录错误尝试的相关操作
 */
namespace User\Model;
use Common\Model\BaseModel;
class LogLoginModel extends BaseModel{

    /**
     * 描述：新增用户登录失败记录
     * @param $userID
     * @return bool
     * @author demo
     */
    public function addLoginFailedRecord($userID){
        $ip = get_client_ip(0,true);
        $time = time();
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        return $this->insertData(['UserID'=>$userID,'LoginAgent'=>$userAgent,'LoginTime'=>$time,'LoginIp'=>$ip]);
    }

    /**
     * 描述：获取某个用户登录指定时间段内登录失败的日志记录
     * @param int $userID 用户ID
     * @param int $time 时间段 默认半个小时
     * @return int 登录失败次数
     * @author demo
     */
    public function getUserIDCount($userID,$time=1800){
        $where = [
            'UserID'=>$userID,
            'LoginTime'=>['between',[time()-$time,time()]],
        ];
        return $this->selectCount($where,'LoginID');
    }

    /**
     * 描述：获取某个IP指定时间内登录错误的日志记录
     * @param int $time 时间段 默认半个小时
     * @return int 登录失败次数
     * @author demo
     */
    public function getIpCount($time=1800){
        $ip = get_client_ip(0,true);
        $where = [
            'LoginIp'=>$ip,
            'LoginTime'=>['between',[time()-$time,time()]],
        ];
        return $this->selectCount($where,'LoginID');
    }

    /**
     * 用户登录成功后，删除用户登录失败记录，包括该$userID的，还有该IP下的userID为0的记录
     * @param int $userID 用户id
     * @author demo
     */
    public function delLoginFailedRecord($userID){
        $ip = get_client_ip(0,true);
        $where = ['UserID'=>$userID,['LoginIp'=>$ip,'UserID'=>0],'_logic'=>'OR'];
        $this->deleteData($where);
    }
}