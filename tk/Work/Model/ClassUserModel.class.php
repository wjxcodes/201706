<?php
/**
 * @author demo
 * @date 2014年8月10日
 */
/**
 * 班级成员管理组模型类，用于处理班级成员管理相关操作
 */
namespace Work\Model;
class ClassUserModel extends BaseModel{
    /**
     * @覆盖父类方法。
     * @author demo 2015-12-21
     */
    public function insertData($data, $modelName=''){
        $result = parent::insertData($data,$modelName);
        if($result !== false){
            $this->getModel('StatisticsCounter','increase','homeWorkNum');
        }
        return $result;
    }

    /**
     * @覆盖父类方法。
     * @author demo 
     */
    public function deleteData($where, $modelName=''){
        $result = (array)$this->selectData('CUID', $where);
        $num = count($result);
        if($num > 0){
            $this->getModel('StatisticsCounter','increase','homeWorkNum',$num*-1);
        }
        return parent::deleteData($where, $modelName);
    }

    /**
     * 获取班级下学生列表
     * @param int $classID 班级ID
     * @return array 成功返回学生数组 [['UserID'=>'','RealName'=>'','OrderNum'=>'']]，失败返回空数组
     * @author demo
     */
    public function classStudents($classID){
        $studentList = $this->unionSelect('classUserByClassID',$classID);
        return $studentList?$studentList:array();
    }
}
