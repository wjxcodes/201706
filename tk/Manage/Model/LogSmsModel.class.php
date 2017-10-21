<?php
/**
 * @author demo
 * @date 2015年8月8日
 */

/**
 * 短信日志Model类，用于处理短信日志相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class LogSmsModel extends BaseModel{

    /**
     * 获取短信日志发送时间记录
     * @param string $userName 用户
     * @param string $code 验证码
     * @param string $type 类型 1短信 2邮件
     * @author demo
     */
    public function getCodeSendTime($userID,$code,$type){
        $where           =array();
        $where['UserID'] =$userID;
        $where['CodeNum']=$code;
        $where['Type']   =$type;
        $order           ='SmsID DESC'; //倒序排列
        return $this->findData('SmsID,Receive,CodeNum,AddTime',$where,$order);
    }

    /**
     * 通过接收方信息查找发送记录
     * @param $receive string 接收方信息
     * @param $code string 验证码
     * @param $type int 验证类型
     * @return array
     * @author demo
     */
    public function getCodeByReceive($receive,$code,$type){
        $where           =array();
        $where['Receive'] =$receive;
        $where['CodeNum']=$code;
        $where['Type']   =$type;
        $order           ='SmsID DESC'; //倒序排列
        return $this->findData('SmsID,Receive,CodeNum,AddTime',$where,$order);
    }
    /**
     * 写入日志记录
     * @param string $content 内容
     * @param string $receive 接收方，可以是手机号或者邮箱
     * @param string $status 状态
     * @param int $type 类型 1短信 2邮件
     * @param string $mCode 验证码
     * @param int $userID 用户id
     * @author demo
     */
    public function smsLog($param){
        $param['AddTime']=time();
        $result          =$this->insertData($param);

        return $result;
    }

    /**
     * 获取单位时间内信息发送的数量
     * @param string $receive 接收方信息（可以是邮箱号或者是手机号）
     * @param string $type 类型 1短信 2邮件
     * @param string $time 在这个时间之后
     * @return mixed
     * @author demo 
     */
    public function smsLogCount($receive,$type,$time){
        $where           =array();
        $where['Receive'] =$receive;
        $where['Type']   =$type;
        $where['Status'] =0;
        $where['AddTime']=array('egt',$time);
        $result          =$this->selectCount($where,'SmsID');

        return $result;
    }

    /**
     * 修改日志状态IfConfirm
     * @param int $smsID 日志id
     * @author demo
     */
    public function setIfConfirm($smsID){
        $data =array('IfConfirm'=>1);
        $where=array('SmsID'=>$smsID);

        return $this->updateData($data,$where);
    }

    /**
     * 通过条件获取验证码日志数量
     * @param $where
     * @return mixed
     * @author demo
     */
    public function getLogSmsCount($where){
        $count = $this->selectCount(
            $where,
            'SmsID');
        return $count;
    }
    /**
     * 获取验证码日志
     * @param $where
     * @return mixed
     * @author demo
     */
    public function getLogSmsByWhere($where){
        $list = $this->unionSelect('LogSmsPageData',$where);
        return $list;
    }

    /**
     * 查找用户激活码
     * @param $userID int 用户ID
     * @param $code string 用户激活码
     * @return mixed
     * @author demo
     */
    public function getActivation($userID,$code){
        $result = $this->findData(
            'Content',
            'UserID='.$userID.' AND Content="'.$code.'" AND Type=3'
        );
        return $result;
    }

    /**
     * 获取最近一次发送短信的信息
     * @param $phoneNum int 手机号
     * @return mixed
     * @author demo
     */
    public function getLastSendLog($phoneNum){
        $result = $this->findData(
            'UserID,AddTime,Status',
            'Receive="'.$phoneNum.'" AND Type=1',
            'AddTime DESC'
        );
        return $result;
    }
}