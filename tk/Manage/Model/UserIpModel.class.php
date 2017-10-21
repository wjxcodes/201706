<?php
/**
 * @author demo 
 * @date 2014年10月17日
 * @update 2015年9月28日
 */

/**
 * 用户ip管理类，用于管理用户ip
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class UserIpModel extends BaseModel {

    /**
     * @覆盖父类方法。
     * @author demo 2015-12-21
     */
    public function insertData($data, $modelName=''){
        $result = parent::insertData($data,$modelName);
        if($result !== false){
            $this->getModel('StatisticsCounter')->increase('schoolNum');
        }
        return $result;
    }

    /**
     * @覆盖父类方法。
     * @author demo 
     */
    public function deleteData($where, $modelName=''){
        $result = (array)$this->selectData('IPID', $where);
        $num = count($result);
        if($num > 0){
            $this->getModel('StatisticsCounter')->increase('schoolNum', $num*-1);
        }
        return parent::deleteData($where, $modelName);
    }
    
    /**
     * 查询用户ip信息
     * @param string $field 查询字段 默认全部
     * @param string $where 查询条件
     * @author demo
     */
    public function userIp($field='*',$where){
        return $this->selectData(
                    $field,
                    $where
                );
    }
}