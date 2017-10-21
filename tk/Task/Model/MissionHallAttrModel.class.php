<?php
/**
 * @author demo
 * @date 2015年11月10日
 */
namespace Task\Model;
use Common\Model\BaseModel;
class MissionHallAttrModel extends BaseModel{
    /**
     * 构造函数（主要设置了此模型modelName）
     * @author demo
     */
    function __construct() {
        parent::__construct();
        $this->modelName = 'MissionHallAttr';
    }
    
    /**
     * 获取任务属性
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
     * @param string $where
     * @return int | bool
     * @author demo
     */
    public function save($data,$id=''){
        if(empty($id) || $this->getList('MHTID','MHTID ='.$id) == false){
            if(!empty($id)){
                $data['MHTID'] = $id ;
            }
            return $this->insertData($data,$this->modelName);
        }else {
            return $this->updateData($data,' MHTID = '.$id,$this->modelName);
        }
    }
    
}