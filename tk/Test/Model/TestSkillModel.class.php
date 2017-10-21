<?php

namespace Test\Model;

/**
 * 试题技能逻辑类
 * @author demo
 */
class TestSkillModel extends BaseModel {

    /**
     * 根据试题id获取对应技能id
     * @param int $testID 试题id
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
                $arrTemp[$iBuffer['TestID']][]=$iBuffer['SkillID'];
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
                        $delArr[]=$buffer[$i]['TSID'];
                        continue;
                    }
                    $data=array();
                    $data['TestID']=$testID;
                    $data['SkillID']=$idArr[$i];
                    $testKl->updateData(
                        $data,
                        'TSID='.$buffer[$i]['TSID']);
                }
                if($i<count($idArr)){
                    for(;$i<count($idArr);$i++){
                        $data=array();
                        $data['TestID']=$testID;
                        $data['SkillID']=$idArr[$i];
                        $addArr[]=$data;
                    }
                }
            }else{
                foreach($idArr as $iIdArr){
                    $data=array();
                    $data['TestID']=$testID;
                    $data['SkillID']=$iIdArr;
                    $addArr[]=$data;
                }
            }
        }else if($buffer){
            foreach($buffer as $iBuffer){
                $delArr[]=$iBuffer['TSID'];
            }
        }
        if(!empty($addArr)){
            $testKl->addAllData($addArr);
        }
        if(!empty($delArr)){
            $testKl->deleteData('TSID in ('.implode(',',$delArr).')');
        }
    }
}
