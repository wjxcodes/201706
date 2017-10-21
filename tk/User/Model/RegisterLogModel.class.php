<?php
/**
 * @author demo
 * @date 2015年11月7日
 */
/**
 * 评论管理Model类，评论管理相关操作
 */
namespace User\Model;
use Common\Model\BaseModel;
class RegisterLogModel extends BaseModel{

    /**
     * 根据参数录入注册日志
     * @author demo
     */
    public function insertRegisterLog($userID,$ip){
        if(!empty($ip)){
            $ip=ip2long($ip);
            $IPID=$this->getModel('UserIp')->selectData(
                'IPID',
                'IPAddress='.$ip
            );
            if(!empty($IPID)){
                $insertData['IPID']=$IPID[0]['IPID'];
                $insertData['RegTime']=time();
                $insertData['UserID']=$userID;
                $this->insertData($insertData);
            }
        }
    }
    
    /**
     * 获取注册日志列表
     * @author demo
     */
    public function getList($field='*',$where=''){
        return $this->selectData($field, $where);
    }

}