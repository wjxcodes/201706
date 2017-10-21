<?php
/**
 * @author demo
 * @date 2014年8月7日
 */
/**
 * 班级管理模型类，用于班级管理相关操作
 */
namespace Work\Model;
class ClassListModel extends BaseModel{
    /**
     * @覆盖父类方法。
     * @author demo 2015-12-18
     */
    public function insertData($data, $modelName=''){
        $result = parent::insertData($data,$modelName);
        if($result !== false){
            $this->getModel('StatisticsCounter','increase','classNum');
        }
        return $result;
    }

    /**
     * @覆盖父类方法。
     * @author demo 
     */
    public function deleteData($where, $modelName=''){
        $data = (array)$this->selectData('ClassID', $where);
        $num = count($data);
        if($num > 0)
            $this->getModel('StatisticsCounter','increase','classNum',$num*-1);
        return parent::deleteData($where, $modelName);
    }

   /**
    * 查询数据ById；
    * @param string $field 查询的字段
    * @param string $id 要查询的id字符串以英文逗号间隔
    * @return array 
    * @author demo 
    */
    public function selectById($field,$id){
        $result=$this->selectData(
            $field,
            'ClassID in ('.$id.')'
        );
        return $result[0];
    }
    /**
     * 获取最大的班级编号加1
     * @author demo
     */
    public function getMaxNum(){
        $orderNum=$this->maxData(
            'OrderNum'); //得到最大OrderNum
        if($orderNum<10001) $orderNum=10001;
        else $orderNum++;
        return $orderNum;
    }
}