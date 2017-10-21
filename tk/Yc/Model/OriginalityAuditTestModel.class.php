<?php
/**
 * 原创模板试题审核model
 * @author demo 2015-9-7
 */
namespace Yc\Model;
class OriginalityAuditTestModel extends OriginalityTplBaseModel{
    /**
     * 实现父类抽象方法
     */
    public function getCondition($type=''){
        return 'ATID='.$this->getId();
    }

    /**
     * 根据试题id列表返回相关数据
     * @param array $list array(11,2323,32..);
     * @param int $aid
     * @return array
     * @author demo 
     */
    public function getTestList($list, $aid){
        if(count($list) == 0){
            return array();
        }
        $seg = 'rt.RTID IN('.implode(',', $list).') AND a.AID='.$aid;
        $sql = "SELECT rt.RTID,t.Test, t.Answer, t.Analytic, a.ReserveTest, a.ATID, a.TestAuditSuggestion FROM `zj_originality_relate_test` rt LEFT JOIN `zj_originality_audit_test` a ON a.TTID=rt.TTID LEFT JOIN `zj_custom_test` t ON t.TestID=a.ReserveTest WHERE {$seg}";
        return M()->query($sql);
    }

    /**
     * 根据ttid返回审核试题数据
     * @param $int $ttid
     * @return array
     * @author demo 
     */
    public function getDataByTtid($ttid){
        $result = $this->findData(
            '*',
            'TTID='.$ttid
        );
        return (empty($result)) ? array() : $result;
    }
}