<?php
/**
 * 任务大厅模块表
 * @author demo 2015-11-9 
 */
namespace Task\Model;
use Common\Model\BaseModel;
class MissionHallModulesModel extends BaseModel{

    /**
     * 保存
     * @param int $tid 
     * @author demo 
     */
    public function save($tid, $data){
        $tid = (int)$tid;
        $record = $this->findData('MHTID', 'MHTID='.$tid);
        $result = true;
        if(empty($record)){
            $data['MHTID'] = $tid;
            $result = $this->insertData($data);
        }else{
            $result = $this->updateData($data, 'MHTID='.$tid);
        }
        return $result !== false;
    }
    
    /**
     * 删除试题
     * @param string $ids id列表，逗号分隔
     * @author demo 
     */
    public function delete($ids){
        return $this->deleteData("MHTID IN({$ids})");
    }

    /**
     * 返回完成方式
     * @return array
     * @author demo 
     */
    public function getCompleteType(){
        return array(
            1 => '组卷',
            2 => '教师端'
        );
    }
    
    /**
     * 获取信息
     * @param string $field 
     * @return array | bool 
     * @author demo
     */
    public function getInfo($where,$field = '*'){
        return $this->selectData($field, $where);
    }
}