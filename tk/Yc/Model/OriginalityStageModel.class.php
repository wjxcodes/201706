<?php
/**
 * 期次model
 * @author demo 2015-9-7
 */
namespace Yc\Model;
class OriginalityStageModel extends OriginalityTplBaseModel{

    /**
     * 实现父类抽象方法
     */
    public function getCondition($type=''){
        return 'SID='.$this->getId();
    }

    /**
     * 返回后台管理相关数据
     * @param array $params 查询参数
     * @return array 分页信息及总页数
     * @author demo 
     */
    public function getListByPagtion($params=array()){
        $where = '';
        $count = $this->selectCount($where, 'SID');
        $this->pagtion['page'] = page($count, $this->pagtion['page'], $this->pagtion['recordsNum']);
        return array($this->getList($where), $count);
    }

    /**
     * 返回结果，本结果当主键不为空时，将返回一条记录
     * @param string $where
     * @return array
     * @author demo 
     */
    public function getList($where=''){
        if(empty($where)){
            $where = '1=1';
        }
        $id = $this->getId();
        if(!empty($id)){
            $where .= ' AND SID='.$id;
        }
        $limit = ($this->pagtion['recordsNum'] * ($this->pagtion['page'] - 1)).','.$this->pagtion['recordsNum'];
        $sql = "SELECT s.*, a.AdminName FROM `zj_originality_stage` s LEFT JOIN `zj_admin` a ON a.AdminID=s.Admin WHERE %s ORDER BY s.`Order` DESC LIMIT {$limit}";
        return M()->query(sprintf($sql, $where));
    }

    /**
     * 该期次是否到期
     * @return boolean 到期返回true
     */
    public function isAbort(){
        $result = $this->findData('EndTime', $this->getCondition());
        return (int)$result['EndTime'] < time();
    }

    /**
     * 返回当前期次
     * @return array 当前期次的相关数据
     * @author demo 
     */
    public function getCurrentStage(){
        $current = time();
        // $where = "BeginTime <= {$current} AND {$current} < EndTime"; //仅查询指定期内的数据
        $stage = $this->selectData('*', '1=1', '`Order` DESC', '0,1');
        $stage = $stage[0];
        if(empty($stage)){
            return array();
        }
        return $stage;
    }
    
    /**
     * 按条件查询数据
     * @param string $table 数据表名称
     * @param string $field 查询的字段
     * @param string $where 查询的条件
     * @param string $order 查询的排序
     * @param string $limit 查询的数量
     * @return array
     * @author demo
     */
    public function selectOriginality($table,$field,$where,$order='',$limit=''){
        return $this->selectData($field, $where, $order, $limit, $table);
    }
}