<?php
/**
 * 模板关联试题model
 * @author demo 2015-9-8
 */
namespace Yc\Model;
class OriginalityRelateTestModel extends OriginalityTplBaseModel{
    /**
     * 实现父类抽象方法
     */
    public function getCondition($type=''){
        return 'RTID='.$this->getId();
    }

    /**
     * 保存选题
     * @param int $ttid 模板试题ttid
     * @return boolean
     * @author demo 
     */
    public function saveSelectedTopic($ttid){
        //验证该记录是否存在
        $data = $this->getOneRecord();
        if(empty($data)){
            $this->setErrorMessage('30306');
            return false;
        }
        //修改指定$ttid下Selected=1的试题为0
        $result = $this->updateData(
            array('Selected' => 0),
            "TTID={$ttid} AND Selected=1"
        );
        if($result === false){
            $this->setErrorMessage('30303');
            return false;
        }
        //判断当前数据是否为$ttid的关联内容
        if($data['TTID'] == $ttid){
            //删除往期数据，在未选中往期数据时，该$ttid相关的记录下，将不存在IsHistory=1的数据
            $result = $this->deleteData(
                "TTID={$ttid} AND isHistory=1"
            );
            return $this->update(array(
                'Selected' => 1
            ));  
        }
        //往期数据处理，将其添加到$ttid之下
        $data['TTID'] = $ttid;
        $data['IsHistory'] = 1;
        $data['Selected'] = 1;
        unset($data['RTID'], $data['AddTime']);
        return $this->insert($data);
    }

    /**
     * 覆盖父类insert方法
     * @return boolean
     * @author demo 
     */
    public function insert($data){
        $data['AddTime'] = time();
        if(!isset($data['IsHistory'])){
            $data['IsHistory'] = 0;
        }
        if(!isset($data['Selected'])){
            $data['Selected'] = 0;
        }
        return parent::insert($data);
    }

    /**
     * 根据模板试题id返回参与用户的数量
     * @param string $ttid 列表
     * @return int
     * @author demo 2015-9-8
     */
    public function getPartakeCountByTplTestId($ttid){
        //分组查询出参与用户的人数
        $sql = "SELECT COUNT(RTID) as rtid FROM `zj_originality_relate_test` WHERE TTID IN({$ttid}) GROUP BY UserID";
        $data = M()->query($sql);
        return count($data);
    }

    /**
     * 根据模板试题id列表，返回相关的校本题库试题的试题,此处返回是将使用分页方法
     * @param array $ids 模板试题id列表
     * @param int $selected 查看试题的选中类别，默认为-1，查看所有，0，查看未选试题，1，查看选中试题
     * @return array 
     * @author demo 
     */
    public function getTestList($ids, $selected=-1){
        if(!is_array($ids)){
            $ids = array($ids);
        }
        if(empty($ids)){
            return array(array(), 0);
        }
        $selectedSeg = 'rt.Selected >= 0';
        if((int)$selected >= 0){
            $selectedSeg = 'rt.Selected='.$selected;
        }
        $seg = 'rt.TTID IN('.implode(',', $ids).')';
        $sql = "SELECT %s FROM `zj_originality_relate_test` rt LEFT JOIN `zj_custom_test` t ON t.TestID=rt.TestID LEFT JOIN `zj_custom_test_attr` a ON a.TestID=t.TestID WHERE a.Status=1 AND {$selectedSeg} AND {$seg} ORDER BY rt.TTID ASC";
        $count = 0;
        if($this->pagtion !== false){
            $count = M()->query(sprintf($sql, 'count(rt.TestID) as num'));
        }
        if(empty($count)){
            $count = 0;
        }else{
            $count = $count[0]['num'];
        }
        $limit = '';
        if($this->pagtion !== false){
            $this->pagtion['page'] = page($count, $this->pagtion['page'], $this->pagtion['recordsNum']);
            $limit = ' Limit '.($this->pagtion['recordsNum'] * ($this->pagtion['page'] - 1)).','.$this->pagtion['recordsNum'];
        }
        $sql = sprintf($sql, 'rt.TTID, rt.RTID, rt.TestID, t.Test, t.Answer, t.Analytic, rt.Selected').$limit;
        $result = M()->query($sql);
        return array($result, $count);
    }

    /**
     * 根据多个ttid获取相关的数据
     * @param array $list
     * @param string $field
     * @return array
     * @author demo 
     */
    public function getSelectedTestByIdList($list, $field='*'){
        $ids = implode(',', $list);
        return $this->selectData(
            $field,
            'TTID IN('.$ids.') AND Selected=1',
            'TTID'
        );
    }

    /**
     * 根据给定的TTID列表，查询出参与人数
     * @param array $list
     * @return array
     * @author demo 
     */
    public function getPartakeNumByList($list){
        if(count($list) == 0){
            return array();
        }
        $list = implode(',', $list);
        $sql = "select TTID, UserID from `zj_originality_relate_test` where TTID IN ({$list}) GROUP BY TTID, UserID";
        $result = M()->query($sql);
        $list = array();
        foreach($result as $key=>$value){
            $id = $value['TTID'];
            if(isset($list[$id])){
                $increment = (int)$list[$id];
                $list[$id] = ($increment + 1);
            }else{
                $list[$id] = 1;
            }
        }
        unset($result);
        return $list;
    }

    /**
     * 更新选题
     * @param int $atid 审核关联试题id
     * @param int $orginalTestId 原始的试题id
     * @param int $selectedTestId 需选中的试题id
     * @return boolean
     * @author demo 
     */
    public function upgradeSelectedTest($atid, $orginalTestId, $selectedTestId){
        $relateTest = $this->getOneRecord();
        //现将本条信息标识为未选中
        $result = $this->updateData(
            array('Selected' => 0),
            'TTID='.$relateTest['TTID'].' AND TestID='.$orginalTestId
        );
        if($result === false){
            return false;
        }
        //更新指定TestID和TTID数据的Selected=1
        $result = $this->updateData(
            array('Selected' => 1),
            'TTID='.$relateTest['TTID'].' AND TestID='.$selectedTestId
        );
        if($result === false){
            //产生错误将数据还原
            $this->update(array(
                'Selected' => '1'
            ));
            return false;
        }
        //将当前Selected更新为0的数据，写入审核试题表的ReserveTest字段中作为备选
        $oat = new OriginalityAuditTestModel($atid);
        //此处未做检查
        $result = $oat->update(array(
            'ReserveTest' => $orginalTestId
        ));
        return $result;
    }
}