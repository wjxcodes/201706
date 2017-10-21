<?php

namespace Test\Model;

/**
 * 试题能力逻辑类
 * @author demo
 */
class TestCapacityModel extends BaseModel {

    /**
     * 根据试题id获取对应能力id
     * @param array $testID 试题id
     * @author demo
     * @return array
     */
    public function getByTestID($testID) {
        $buffer=$this->selectData(
            '*',
            'TestID in ('.$testID.')');

        $arrTemp=[];
        if($buffer){
            $arrTemp=array();
            foreach($buffer as $iBuffer){
                $arrTemp[$iBuffer['TestID']][]=$iBuffer['CapacityID'];
            }
        }
        return $arrTemp;
    }
    /**
     * 修改试题对应属性数据
     * @param int $testID 试题id
     * @param array $idArr 改变后的技能id
     * @author demo
     * @return array
     */
    public function setForTestID($testID,$idArr){
        if(!is_array($idArr) && !empty($idArr)) $idArr=explode(',',$idArr);
        $testKl=$this;
        $buffer=$testKl->selectData(
            '*',
            'TestID='.$testID);
        //保存考点
        $addArr=array();
        $delArr=array();
        if($idArr){
            if($buffer){
                for($i=0;$i<count($buffer);$i++){
                    if($i>=count($idArr)){
                        $delArr[]=$buffer[$i]['TCID'];
                        continue;
                    }
                    $data=array();
                    $data['TestID']=$testID;
                    $data['CapacityID']=$idArr[$i];
                    $testKl->updateData(
                        $data,
                        'TCID='.$buffer[$i]['TCID']);
                }
                if($i<count($idArr)){
                    for(;$i<count($idArr);$i++){
                        $data=array();
                        $data['TestID']=$testID;
                        $data['CapacityID']=$idArr[$i];
                        $addArr[]=$data;
                    }
                }
            }else{
                foreach($idArr as $iIdArr){
                    $data=array();
                    $data['TestID']=$testID;
                    $data['CapacityID']=$iIdArr;
                    $addArr[]=$data;
                }
            }
        }else if($buffer){
            foreach($buffer as $iBuffer){
                $delArr[]=$iBuffer['TCID'];
            }
        }
        if(!empty($addArr)){
            $testKl->addAllData($addArr);
        }
        if(!empty($delArr)){
            $testKl->deleteData('TCID in ('.implode(',',$delArr).')');
        }
    }
}
