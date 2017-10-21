<?php
/**
 * @author demo 
 * @date 2014年8月12日
 */
/**
 * 试题属性模型类，用于处理试题属性相关操作
 */
namespace Test\Model;
class TestAttrModel extends BaseModel{
    /**
     * 查询数据ById；
     * @param string $GradeID 查询的年级字段
     * @param string $SubjectID 查询的学科字段
     * @return array 
     * @author demo
     */
    public function getAttrNum($GradeID,$SubjectID){
        return $this->selectData(
            'count(TestID) as TotalNum',
            '1=1 and GradeID='.$GradeID.' and SubjectID='.$SubjectID
        );
    }

    
    /**
     * 试题手工去重
     * @param array $id 用于将 duplicate=$dupid 多个试题id
     * @param int $dupid 重复试题id
     * @return boolean
     * @author demo
     * @date 2015-6-30
     */
    public function customRemoveDuplication($ids, $dupid){
        $ids = trim(implode(',', $ids), ',');
        $dupid = (int)$dupid;
        if(empty($ids) || 0 == $dupid){
            return false;
        }
        $result = $this->updateData(
            array('Duplicate' => $dupid),
            'TestID IN('.$ids.')'
        );
        if($result === false){
            return false;
        }
        $result = $this->updateData(
            array('Duplicate' => 0),
            'TestID='.$dupid
        );
        return true;
    }

     /**
     * 查询已标重试题
     * @param string $dupid 重复试题id
     * @return array
     * @author demo
     */
    public function getTheDuplicateTest($dupid){
        $result = $this->selectData(
            'TestID,Duplicate',
            'TestID IN('.$dupid.') AND Duplicate > 0'
        );
        return $result;
    }

    /**
     * 根据重复试题id查询重题
     * @param int $id 重复试题id
     * @author demo
     */
    public function getDuplicationByDupId($id){
        $test = $this->unionSelect('getDuplicateTestJoinQuery','TestAttr', 'Test' ,'a.TestID, Test, a.Duplicate', 't.TestID = a
        .TestID AND a
        .TestID='.$id);
        $testModel = $this->getModel('Test');
        $test = $test[0];
        if(!empty($test)){
            $test['Test'] = R('Common/TestLayer/strFormat',array($test['Test']));
        }
        //查询出Duplicate为$id的相关试题，
        $duplications = $this->unionSelect('getDuplicateTestJoinQuery','TestAttr', 'Test' ,'a.TestID, Test, a.Duplicate', 't
        .TestID = a
        .TestID AND Duplicate='.$id
        );
        foreach($duplications as $key=>$value){
            $duplications[$key]['Test'] = R('Common/TestLayer/strFormat',array($testModel->replaceStr($value['Test'], 1)));
        }
        // $duplications = array(   #37|1#38|1#39|2#40|2#75|0.02#76|0.01#77|-0.02#
        //     array('TestID'=>213123, 'Test'=>'sdfsdfsfsdfsdf'),
        //     array('TestID'=>11111, 'Test'=>'aaaaaaaaaaa'),
        //     array('TestID'=>3232321, 'Test'=>'============='),
        //     array('TestID'=>13123451, 'Test'=>'15787962323487912121'),
        // );
        return array($test, $duplications);
    }

    /**
     * 返回指定docid下的所有试题的排重状态
     * @param int $docid
     * @param array 
     * @author demo 15-12-23
     */
    public function getDuplicateStatusByDocId($docid){
        $result = $this->selectData('TestID, Duplicate, NumbID', "DocID={$docid}", 'NumbID ASC');
        foreach($result as $key=>$value){
            $order = str_replace($docid, '', $value['NumbID']);
            $order = preg_replace('/(?:^-0|-)/im', '', $order);
            if((int)$value['Duplicate'] > 0){
                $result[$key] = "第{$order}个【题文】：无需解析";
            }else{
                unset($result[$key]);
            }
        }
        return $result;
    }
}