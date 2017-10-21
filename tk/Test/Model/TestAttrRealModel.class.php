<?php
/**
 * @author demo 
 * @date 2014年8月12日
 */
/**
 * 试题属性模型类，用于处理试题属性相关操作
 */
namespace Test\Model;
class TestAttrRealModel extends BaseModel{

    /**
     * @覆盖父类方法。
     * @author demo 2015-12-21
     */
    public function insertData($data, $modelName=''){
        $result = parent::insertData($data,$modelName);
        if($result !== false){
            $doc = $this->unionSelect('selectDocByTestId',['r.TestID'=>$data['TestID']], 'TypeID');
            $sc  = $this->getModel('StatisticsCounter');
            if(3 == $doc['TypeID']){
                $sc->increase('selfTestNum');
            }
            $sc->increase('testNum');
        }
        return $result;
    }

    /**
     * @覆盖父类方法。
     * @author demo 
     */
     public function deleteData($where, $modelName=''){
        $data = (array)$this->unionSelect('selectDocByTestId',$where, 'TypeID');
        $num = 0; //原创题数量
        $total = count($data); //总数量
        foreach($data as $value){
            if(3 == $doc['TypeID']){
                $num++;
            }
        }
        unset($data);
        if($total > 0){
            $sc = $this->getModel('StatisticsCounter');
            if($num > 0){
                $sc->increase('selfTestNum', $num*-1);
            }
            $sc->increase('testNum', $total*-1);
        }
        
        return parent::deleteData($where, $modelName);
     }


    /**
     * 查询数据ById；
     * @param string $GradeID 查询的年级字段
     * @param string $SubjectID 查询的学科字段
     * @return array 
     * @author demo
     */
    public function getAttrRealNum($GradeID,$SubjectID){
        return $this->selectData(
            'count(TestID) as TotalNum',
            '1=1 and GradeID='.$GradeID.' and SubjectID='.$SubjectID
        );
    }

    /**
     * 根据文档id加载试题到组卷中心
     * @param int $docid 文档id
     * @author demo
     * @date 2015-6-6
     */
    public function loadTestByDocId($docid){
        $data = $this->selectData(
            'TestID,TestNum,TypesID,OptionNum',
            'DocID='.(int)$docid,
            'NumbID ASC'
        );
        return $data;
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
        $rseult = $this->updateData(
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
            'TestID IN('.$dupid.') AND Duplicate>0'
        );
        return $result;
    }

    /**
     * 根据重复试题id查询重题
     * @param int $id 重复试题id
     * @author demo
     * @date 
     */
   public function getDuplicationByDupId($id){
        $test = $this->unionSelect('getDuplicateTestJoinQuery','TestAttrReal', 'TestReal' ,'a.TestID, Test,a.Duplicate', 't
        .TestID = a
        .TestID AND a.TestID='.$id);
        $testModel = $this->getModel('Test');
        $test = $test[0];
        if(!empty($test)){
            $test['Test'] = R('Common/TestLayer/strFormat',array($test['Test']));
        }
        //查询出Duplicate为$id的相关试题，
        $duplications = $this->unionSelect('getDuplicateTestJoinQuery','TestAttrReal', 'TestReal' ,'a.TestID, Test, a.Duplicate', 't.TestID = a.TestID AND Duplicate='.$id);
        foreach($duplications as $key=>$value){
            $duplications[$key]['Test'] = R('Common/TestLayer/strFormat',array($testModel->replaceStr($value['Test'], 1)));
        }
        // $duplications = array(
        //     array('TestID'=>213123, 'Test'=>'sdfsdfsfsdfsdf'),
        //     array('TestID'=>11111, 'Test'=>'aaaaaaaaaaa'),
        //     array('TestID'=>3232321, 'Test'=>'============='),
        //     array('TestID'=>13123451, 'Test'=>'15787962323487912121'),
        // );
        return array($test, $duplications);
    }
}