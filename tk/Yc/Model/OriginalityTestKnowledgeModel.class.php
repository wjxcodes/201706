<?php
/**
 * 原创模板试题知识点Model
 * @author demo 2015-9-7
 */
namespace Yc\Model;
class OriginalityTestKnowledgeModel extends OriginalityTplBaseModel{
    /**
     * 实现父类抽象方法
     */
    public function getCondition($type=''){
        return 'TKID='.$this->getId();
    }

    /**
     * 保存操作
     * @param array $data
     * @param boolean $act 保存为true或者为修改
     * @return boolean
     * @author demo 
     */
    public function save($data, $act){
        if($act){
            $result = $this->addAllData($data);
            if($result === false){
                $this->setErrorMessage('30511');
                return false;
            }
        }else{
            //暂未做修改处理
        }
        return true;
    }

    /**
     * 返回指定原创模板试题id的知识点
     * @param int $ttid 模板试题id
     * @return array
     * @author demo 
     */
    public function getKnowledgeByTplTestId($ttid){
        return $this->select('KlID', 'TTID='.(int)$ttid, 'KlID ASC');
    }

    /**
     * 根据给定知识点id列表，期次，学科查询出相关知识点
     * @param array $knowledge 知识点列表
     * @param int $currentStageId
     * @param int $subjectId 学科id
     * @param int $stageId 给定的期次id，默认为0
     * @return array  array(0=>array('ttid', 'kl'),...)
     * @author demo 
     */
    public function getKnowledges($knowledge, $currentStageId, $subjectId, $stageId=0){
        if(empty($knowledge)){
            return array();
        }
        if(strpos($subjectId, ',') >= 0){
            $subjectId = 'tpl.SubjectID IN('.$subjectId.')';
        }else{
            $subjectId = 'tpl.SubjectID='.(int)$subjectId;
        }
        /*
            期次不为空时，需查询出该期次的相关的模板试题数据，或者查询出小于当前期的所有关联试题
            查询结果按照知识点升序排列
        */
        $sql = "SELECT kl.TTID as ttid, kl.KlID as kl FROM `zj_originality_test_knowledge` kl LEFT JOIN `zj_originality_template_test` test ON kl.TTID=test.TTID LEFT JOIN `zj_originality_template` tpl ON tpl.TID=test.TID WHERE %s AND {$subjectId} AND kl.KlID IN(".implode(',', $knowledge).') ORDER BY kl';
        if(!empty($stageId)){
            $sql = sprintf($sql, "tpl.SID={$stageId}");
        }else{
            $sql = sprintf($sql, "tpl.SID<{$currentStageId}");
        }
        return M()->query($sql);
    }
}