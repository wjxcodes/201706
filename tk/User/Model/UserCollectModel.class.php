<?php
/**
 * @author demo   
 * @date 2014年9月24日
 * @update 2015年1月21日
 */
/**
 * 试题收藏模型类，用于处理用户收藏试题操作
 */
namespace User\Model;
use Common\Model\BaseModel;
class UserCollectModel extends BaseModel{

    /**
     * 按条件查找收藏试题
     * @param array $data 查找的条件
     * @param string $field 返回的字段
     * @return array
     * @author demo
     * @date 2014年9月24日
     */
    public function getCatalogList($field,$data){
        return $this->selectData(
            $field,
            $data,
            'LoadTime desc');
     }
    /**
     * 获取用户某学科下的试题收藏总数量
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @return int 试题收藏总量\
     * @author demo
     */
    public function getCollectAmount($userName,$subjectID){
        $collectAmount = $this->selectCount(
            array('UserName' => $userName,'SubjectID'=>$subjectID),
            'TestID');
        return $collectAmount;
    }
}