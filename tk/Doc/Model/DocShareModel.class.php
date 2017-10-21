<?php
/**
 * 文档分享model
 * @author demo 2015-9-17
 */
namespace Doc\Model;
class DocShareModel extends BaseModel{
    /**
     * 保存数据
     * @param int $did doc_down表主键
     * @param int $userid 用户id
     * @param array $data 其他数据
     * @return boolean
     * @author demo 
     */
    public function save($did, $userid, $data,$cookieUserID){
        $result = $this->selectData('ShareID', 'DownID='.$did.' AND SharerID='.$userid);
        if(!empty($result)){
            return true;
        }
        $data['SharerID'] = $userid;
        $data['DownID'] = $did;
        $data['ShareTime'] = time();

        $insertResult=$this->insertData($data);
        if($insertResult){
            //插入支出表
            $payData['UserID']=$data['AutherID'];
            $payData['PayName']='用户分享下载';
            $payData['PayMoney']=C('WLN_SHARE_DOC_MONEY');
            $payData['PayDesc']="用户[".$cookieUserID."]分享了作者[".$data['AutherID']."]文档ID为【".$did."】";
            $payData['AddTime']=time();
            $this->getModel('Pay')->addPayLog($payData);
            //用户分享试题累加经验
            $userName=$this->getModel('User')->getInfoByID($cookieUserID);
            $this->getModel('UserExp')->addUserExpAll($userName['UserName'],'shareTest');
            return $insertResult;
        }
    }

    /**
     * 根据用户ID及条件查看该用户分享记录总数
     * @para string $where
     * @author demo
     */
    public function getShareTotal($where){
        return $this->unionSelect('getDocShareTotal',$where);
    }

    /**
     * 根据用户ID及条件查看该用户分享记录
     * @para string $where 查询条件
     * @para string $limit='' 限制条数
     * @author demo
     */
    public function getShareList($where,$limit=''){
        return $this->unionSelect('getDocShareList',$where,$limit);
    }
}