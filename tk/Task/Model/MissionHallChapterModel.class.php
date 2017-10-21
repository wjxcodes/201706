<?php
/**
 * @author demo
 * @date 2015年11月10日
 */
namespace Task\Model;
use Common\Model\BaseModel;
class MissionHallChapterModel extends BaseModel{
    /**
     * 构造函数（主要设置了此模型modelName）
     * @author demo
     */
    function __construct() {
        parent::__construct();
        $this->modelName = 'MissionHallChapter';
    }
    
    /**
     * 获取任务章节
     * @param string $field 要获取的字段
     * @param string $where 条件
     * @return array | false
     * @author demo
     */
    public function getList($field='*',$where=''){
        return $this->selectData($field, $where,'','',$this->modelName);
    }
    
    /**
     * 删除
     * @param string $where 删除条件
     * @return array | bool
     * @author demo
     */
    public function delete($where){
        $result = $this->getList('*',$where);
        $del = $this->deleteData($where,$this->modelName);
        if($del !== false){
            return $result;//要删除的结果集
        }else{
            return false;
        }
    }
    
    /**
     * 保存数据
     * 提供主键id则为修改操作
     * @param array $data 
     * @param int $MHTID
     * @return int | bool
     * @author demo
     */
    public function save($data,$MHTID=''){
        if(!empty($MHTID)){//任务章节一对多 修改先删除再添加
            $this->deleteData('MHTID  = '.$MHTID,$this->modelName);
        }
        return $this->addAllData($data,$this->modelName);
    }
    
}