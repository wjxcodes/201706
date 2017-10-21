<?php
/**
 * @author demo
 * @date 2014年10月27日
 */
/**
 * 提分系统中用户测试类
 */
namespace Aat\Model;

use Common\Model\BaseModel;
class UserTestRecordModel extends BaseModel
{   
    /**
     * 是否完成所有的试题
     * @param int $record
     * @return boolean 
     * @author demo 16-4-28
     */
    public function isCompleteAllTheAnswers($record){
        $record = (int)$record;
        $answerRecords = $this->getModel('UserAnswerRecord')->selectData('TestID', 'TestRecordID='.$record);
        if(empty($answerRecords)){
            return false;
        }
        $count = count($answerRecords);
        unset($answerRecords);
        $data = $this->findData('Content', 'TestID='.$record);
        $data = $this->getModel('TestAttrReal')->selectData('TestNum', "TestID IN({$data['Content']})");
        $testNum = 0; //获取小题数量
        foreach($data as $value){
            $num = (int)$value['TestNum'];
            if(0 == $num){
                $num = 1;
            }
            $testNum += $num;
        }
        return $count == $testNum;
    }

    /**
     * 按照题型对试题排序
     * @author demo 16-6-6
     */
    public function sequenceByType($list, $types){
        $temp = array();
        $type = array_shift($types);
        if(empty($type)){
            return $list;
        }
        while(!empty($type)){
            foreach($list as $key=>$value){
                if($value['test_type'] == $type){
                    $temp[] = $value;
                    unset($list[$key]);
                }
            }
            $type = array_shift($types);
        }
        return $temp;
    }
}