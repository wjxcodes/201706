<?php
/**
 * @author demo
 * @date 2015年6月26日
 */
/**
 * DynamicTo班级动态日志model
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class DynamicToModel extends BaseModel{
    /**
     * 批量插入数据；
     * @param array $data 插入数据表字段数组
     * @return bool
     * @author demo
     */
    public function addDynamicTo($data){
        return $this->addAllData($data);
    }
}