<?php
/**
 * 自建题库试题 能力 Model类
 * @auhtor 
 * @date 2017-6-15
 */
namespace Custom\Model;
use Common\Model\BaseModel;
class CustomTestCapacityModel extends BaseModel{

    protected $modelName = 'CustomTestCapacity';

    /**
     * 删除数据；
     * @param int $testId 要删除的试题id
     * @param string $capacityID 要删除的技能id，默认为空时删除$testId所有技能
     * @return boolean
     * @author demo
     */
    public function deleteData($testId,$capacityID=''){
        $testId = ltrim($testId, \Test\Model\TestQueryModel::DIVISION);
        $where = 'TestID='.(int)$testId;
        if($capacityID != ''){
            $where .= " AND capacityID IN({$capacityID})";
        }
        return parent::deleteData(
            $where);
    }


    /**
     * 新增、修改数据
     * @param array $param 格式 array('testid'=>xx,'capacity'=>array(1,2,3...))
     * @param boolean 失败返回false
     * @return bool
     * @author demo
     */
    public function saveData($param){
        $diff = $this->findDifference($param['testid'],$param['capacity']);
        if(!empty($diff)){
            $insertData = $deleteData = array();
            foreach($diff as $val){
                //忽略为空数据
                if(!empty($val)){
                    if(in_array($val,$param['capacity'])){ //需新增的数据
                        $param['testid'] = ltrim($param['testid'], \Test\Model\TestQueryModel::DIVISION);
                        $insertData[] = array('TestID'=>$param['testid'],'CapacityID'=>$val);
                    }else{ //需删除的数据
                        $deleteData[] = $val;
                    }
                }
            }
            unset($diff);

            if(!empty($insertData)){
                $result = $this->addAllData($insertData);
                if($result === false)
                    return false;
            }
            if(!empty($deleteData)){
                $deleteData = implode(',', $deleteData);
                //------------------此处未进行sql检查-------------------
                $this->deleteData($param['testid'],$deleteData);
            }
        }
        return true;
    }

    /**
     * 查找指定试题的章节内容，同时根据comparer对比数据。返回与comparer的差集数据
     * @param int $id 试题id
     * @param array $comparer 对比的数据 格式：array('capacityID','capacityID1'...);
     * @return array 返回比较后的章节id数组
     * @author demo
     */
    public function findDifference($id,$comparer){
        $id = ltrim($id, \Test\Model\TestQueryModel::DIVISION);
        //确保返回数据的正确性
        foreach($comparer as $k=>$v){
            $comparer[$k] = (int)$v;
        }
        $data = $this->selectData(
            'CapacityID',
            array('TestID'=>$id));
        $orginal = array();
        foreach($data as $value){
            $orginal[] = (int)$value['CapacityID'];
        }
        unset($data);
        return array_merge(array_diff($comparer, $orginal),array_diff($orginal, $comparer));
    }

}