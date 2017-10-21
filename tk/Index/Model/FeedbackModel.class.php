<?php
/**
 * @author demo 
 * @date 2014年8月13日
 * @update 2015年9月29日
 */
/**
 * Feedback用户反馈表model
 */
namespace Index\Model;
class FeedbackModel extends BaseModel{
    
    /**
     * 获取反馈信息信息
     * @param string $field 查询字段
     * @param string $where 查询条件
     * @param string $order 排序
     * @param string $limit 数量
     * @return array 用户信息
     * @author demo
     */
    public function getFeedback($field='*',$where='',$order='FeedbackID desc',$limit=''){
        return $this->selectData(
                    $field,
                    $where,
                    $order,
                    $limit
                );
    }
    
    /**
     * 添加反馈信息
     * @param array $data 插入数据表字段数组
     * @return bool
     * @author demo
     */
    public function insertFeedback($data){
        return $this->insertData($data);
    }
}
