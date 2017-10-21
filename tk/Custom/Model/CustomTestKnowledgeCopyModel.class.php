<?php
/**
 * 校本题库从库知识点Model类
 * @auhtor 
 * @date 2015-4-7
 */
namespace Custom\Model;
use Common\Model\BaseModel;
class CustomTestKnowledgeCopyModel extends BaseModel{

    protected $modelName = 'CustomTestKnowledgeCopy';
    /**
     * 删除数据；
     * @param int $testId 要删除的试题id
     * @param string $knowledgeId 要删除的知识点id，默认为空时删除$testId所有知识点
     * @return boolean
     * @author demo
     */
    public function deleteData($testId,$knowledgeId=''){
        $testId = ltrim($testId, \Test\Model\TestQueryModel::DIVISION);
        $where = 'TestID='.(int)$testId;
        if($knowledgeId != ''){
            $where .= " AND KlID IN({$knowledgeId})";
        }
        return parent::deleteData($where);
    }

    /**
     * 新增、修改数据
     * @param array $param 格式 array('testid'=>xx,'knowledge'=>array(1,2,3...))
     * @return bool
     * @author demo
     */
    public function saveData($param){
        $diff = $this->findDifference($param['testid'],$param['knowledge']);
        if(!empty($diff)){
            $insertData = $deleteData = array();
            foreach($diff as $val){
                //忽略为空数据
                if(!empty($val)){
                    if(in_array($val,$param['knowledge'])){ //需新增的数据
                        $param['testid'] = ltrim($param['testid'], \Test\Model\TestQueryModel::DIVISION);
                        $insertData[] = array('TestID'=>$param['testid'],'KlID'=>$val);
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
     * 查找指定试题的知识点内容，同时根据comparer对比数据。返回与comparer的差集数据
     * @param int $id 试题id
     * @param array $comparer 对比的数据 格式：array('id1','id2',...);
     * @return array 返回比较后的知识点id数组
     * @author demo
     */
    public function findDifference($id,$comparer){
        $id = ltrim($id, \Test\Model\TestQueryModel::DIVISION);
        //确保返回数据的正确性
        foreach($comparer as $k=>$v){
            $comparer[$k] = (int)$v;
        }
        $data = $this->selectData(
            'KlID',
            array('TestID'=>$id));
        $orginal = array();
        foreach($data as $value){
            $orginal[] = (int)$value['KlID'];
        }
        unset($data);
        return array_merge(array_diff($comparer, $orginal),array_diff($orginal, $comparer));
    }

    /**
     * 根据id查询知识点信息
     * @param string $testid 试题id，多个id用,分割
     * @author demo
     * @return array
     */
    public function getKnowledges($testid){
        $result = $this->selectData(
            '*',
            "TestID IN({$testid})",
            'TestID desc');
        $data = array();
        foreach($result as $value){
            if(!$data[$value['TestID']]){
                $data[$value['TestID']] = array();
            }
            $data[$value['TestID']][] = $value['KlID'];
        }
        unset($result);
        return $data;
    }
}