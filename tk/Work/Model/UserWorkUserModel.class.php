<?php
//程序生成的文件  2015-12-18
namespace Work\Model;
class UserWorkUserModel extends BaseModel{
    /**
     * @覆盖父类方法。
     * @author demo 2015-12-21
     */
    public function insertData($data, $modelName=''){
        $result = parent::insertData($data,$modelName);
        if($result !== false){
            $this->getModel('StatisticsCounter')->increase('homeWorkNum');
        }
        return $result;
    }

    /**
     * @覆盖父类方法。
     * @author demo 
     */
    public function deleteData($where, $modelName=''){
        $result = (array)$this->selectData('WTUID', $where);
        $num = count($result);
        if($num > 0){
            $this->getModel('StatisticsCounter')->increase('homeWorkNum',$num*-1);
        }
        return parent::deleteData($where, $modelName);
    }
}