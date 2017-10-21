<?php
/**
 * @author demo
 * @date 2014年12月29日
 */
/**
 * 用户经验记录模型类，用于用户用户经验记录相关操作
 */
namespace User\Model;
use Common\Model\BaseModel;
class UserExpRecordModel extends BaseModel{

    /**
     * 录入经验记录
     * @param string $userName 用户名
     * @param string $tagName 操作标示
     * @param string $expNum 经验值
     * @return string $expNum 经验值
     * @author demo
     */
    public function addUserExpLog($userName,$tagName,$expNum){
        $userMsg=$this->getModel('User')->getInfoByName($userName);   //获取用户信息
        $logData['UserID']=$userMsg[0]['UserID'];
        $logData['addTime']=time();
        $logData['ExpValue']=$expNum;
        $logData['ExpTag']=$tagName;
        $result=$this->insertData(
            $logData
        );
        return $result;
    }
    

}
?>