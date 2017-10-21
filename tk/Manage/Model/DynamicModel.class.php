<?php
/**
 * @author demo
 * @date 2015年6月26日
 */
/**
 * Dynamic班级动态model
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class DynamicModel extends BaseModel{
    /**
     * 数据录入
     * @param array $data 需要录入的数据数组
     * @return bool
     * @author demo
     */
    public function addDynamic($data){
        return $this->insertData(
            $data
        );
    }
    /**
     * 获取动态类型
     * @return array
     * @author demo
     */
    public function getDynamicType(){
        return array('Class' => 'Dynamic_Class');
    }
}