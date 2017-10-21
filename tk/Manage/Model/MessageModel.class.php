<?php
/**
 * @author demo  
 * @date 2014年11月28日
 */

namespace Manage\Model;
use Common\Model\BaseModel;
class MessageModel extends BaseModel{
    /**
     * 返回指定试题的评论数据
     * @author demo 16-1-26
     */
    public function getMessagesById($testid, $page, $prepage=10, $fields=''){
        $where = array(
            'TestID' => $testid,
            'Status' => 0
        );
        $count = (int)$this->selectCount($where, 'ID');
        $limit = page($count, $page, $prepage);
        $limit = (($limit-1) * $prepage).','.$prepage;
        if(empty($fields)){
            $fields = '*';
        }
        $data = $this->selectData($fields, $where, 'LoadDate DESC', $limit);
        return array(
            'data' => (array)$data, 
            'count' => (int)$count, 
            'countPage' => (int)ceil($count/$prepage), 
            'page' => (int)$page, 
            'prepage' => (int)$prepage
        );
    }
}