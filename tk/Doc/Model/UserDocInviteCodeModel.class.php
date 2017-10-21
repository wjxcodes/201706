<?php
/**
 * 试卷订单管理model
 * @author demo 16-4-21
 */
namespace Doc\Model;
class UserDocInviteCodeModel extends BaseModel{

    /**
     * 使用邀请码
     * @param string $code 邀请码
     * @param int $user 用户id
     * @return string 成功返回空字符串
     * @author demo 16-4-26
     */
    public function useInviteCode($code, $user){
        $data = $this->findData('ID,Usable', "CodeValue='{$code}'");
        if(empty($data)){
            return 'noExist'; //订单不存在
        }
        if(2 == (int)$data['Usable']){
            return 'used'; //已经使用
        }
        $data = array();
        $data['UserID'] = (int)$user;
        $data['Usable'] = 2;
        $data['UseTime'] = time();
        $result = $this->updateData($data, "CodeValue='{$code}'");
        if($result === false){
            return 'failure';
        }
        return '';
    }

    /**
     * 指定的试卷是否已经使用
     * @param int $docId 试卷编号
     * @param int $userId 用户编号
     * @return boolean
     * @author demo 16-4-26
     */
    public function isDocToUsed($docId, $userId){
        $where = array(
            'DocID' => (int)$docId,
            'UserID' => (int)$userId,
            'Usable' => 2
        );
        $data = $this->findData('Usable', $where);
        return !empty($data);
    }

    /**
     * 指定的邀请码是否已经被使用
     * @author demo 16-4-26
     */
    public function isCodeToUsed($code, $docId){
        $where = array(
            'CodeValue' => $code,
            'DocID' => $docId
        );
        $data = $this->findData('Usable', $where);
        if(((int)$data['Usable']) === 2){
            return true;
        }
        return false;
    }
}