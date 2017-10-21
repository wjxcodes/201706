<?php
/**
 * 通用方法数据接口
 * @author demo 2015-1-5
 */
namespace Common\Api;
use Common\Controller\CommonController;
class CommonApi extends CommonController{
    /**
     * 插入数据；
     * @param string $where 条件
     * @return array 插入后的行数 或 false
     * @author demo
     */
    public function insertData($data,$modelName){
        return $this->getModel($modelName)->insertData($data);
    }

    /**
     * 更新数据；
     * @param string $data 字段
     * @param string $where 条件
     * @return array 更新后的行数 或 false
     * @author demo
     */
    public function updateData($data,$where,$modelName){
        return $this->getModel($modelName)->updateData(
            $data,
            $where
        );
    }

    /**
     * 删除数据；
     * @param string $where 条件
     * @return array 删除后的行数 或 false
     * @author demo
     */
    public function deleteData($where,$modelName){
        return $this->getModel($modelName)->deleteData(
            $where
        );
    }

    /**
     * 按条件查询总数；
     * @param string $table 数据表名称
     * @param string $where 查询的条件
     * @param string $field 聚合字段
     * @param string $rename 数据表重命名
     * @return int 数量
     * @author demo
     */
    public function selectCount($where,$field='',$rename='',$modelName){
        return $this->getModel($modelName)->selectCount($where,$field,$rename);
    }

    /**
     * 查询数据；
     * @param string $field 字段
     * @param string $where 条件
     * @param string $order 查询
     * @param string $modelName 模块名称
     * @return array 查询后的数据集 或 false
     * @author demo
     */
    public function selectData($field,$where,$order='',$limit='',$modelName=''){
        return $this->getModel($modelName)->selectData(
            $field,
            $where,
            $order,
            $limit
        );
    }


    /**
     * 按条件精准查询数据(只查询一条数据)；
     * @param string $field 查询的字段
     * @param string $where 查询的条件
     * @param string $order 排序规则
     * @param string $modelName 模块名称
     * @return array
     * @author demo
     */
    public function findData($field,$where,$order='',$modelName=''){
        return $this->getModel($modelName)->findData(
            $field,
            $where,
            $order
        );
    }

    /**
     * 获取dbConn数据层中的方法
     * @param string $function 方法名
     * @param string param1,param2,param3,param4 可先参数4个
     * @return array
     * @author demo
     */
    public function unionSelect(){
        $param=func_get_args();
        return call_user_func_array(array($this->getModel('Base'),'unionSelect'),$param);
    }

    /**
     * 设置错误信息
     * @param array $params 错误参数
     * @return void
     * @author demo
     */
    public function addErrorLog($params){
        $this->getModel('Base')->addErrorLog($params);
    }
}