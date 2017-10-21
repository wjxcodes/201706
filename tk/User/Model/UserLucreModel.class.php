<?php
/**
 * @author demo
 * @date 2015年8月21日
 */
/**
 * 用户分成管理模型类，用于用户分成管理相关操作
 */
namespace User\Model;
use Common\Model\BaseModel;
class UserLucreModel extends BaseModel{
    
    /**
     * 根据用户动作标示，录入分成记录及累加用户分成金额到用户表
     * @param string $userName 分成用户名
     * @param string $tagName  标示
     * @param string $testID   完成任务试题ID
     * @return bool
     * @author demo
     */
     public function insertLucreByTag($userName,$tagName,$testID){
        if($tagName){
            $LucreNum=$this->getModel('UserLevel')->getUserLevelPower($userName,$tagName);   //获取分成金额
            $data['LucreNum']=$LucreNum;  
            $data['AddTime']=time();
            $data['LucreTestID']=$testID;
            $data['LucreUserName']=$userName;
            $data['LucreTag']=$tagName;
            //累加用户分成金额到用户表
            $this->getModel('UserLucre')->addUserLucre($userName,$LucreNum);
            //用户分成记录到分成记录中
            $lastResult=$this->insertData(
                $data
            );
            return $lastResult;
        }
     }

    /**
     * 用户表，用户分成金额字段累加
     * @param string $userName 用户名
     * @param string $LucreNum 分成金额数
     * @return bool
     * @author demo
     */
    public function addUserLucre($userName,$LucreNum){
        if($userName && $LucreNum){
            $result=$this->getModel('User')->conAddData(
                'LucreNum=LucreNum+'.$LucreNum,
                'UserName ="'.$userName.'"'
            );
            return $result;
        }
    }
}