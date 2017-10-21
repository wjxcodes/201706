<?php
/**
 * 原创模板试题Model
 * @author demo 2015-9-7
 */
namespace Yc\Model;
class OriginalityTemplateTestModel extends OriginalityTplBaseModel{
    /**
     * 实现父类抽象方法
     */
    public function getCondition($type=''){
        return 'TTID='.$this->getId();
    }

    /**
     * 返回后台管理相关数据
     * @param array $params 查询参数
     * @return array 分页信息及总页数
     * @author demo 
     */
    public function getListByPagtion($params){
        $where = $params;
        foreach($where as $key=>$value){
            if(empty($value)){
                unset($where[$key]);
            }
        }
        return $this->getList($where);
    }

    /**
     * 返回结果，本结果当主键不为空时，将返回一条记录
     * @param string|array $where
     * @param int $id 主键id，不为空时，按照主键id查询
     * @return array
     * @author demo 
     */
    public function getList($where=''){
        if(empty($where)){
            $where = '1=1';
        }else if(is_array($where)){
            $temp = [];
            while(list($k, $v) = each($where)){
                if($k == 'TID'){
                    $temp[] = "tt.TID={$v}";
                }else if('SubjectID' == $k){
                    if(strpos($v, ',') >= 0){
                        $temp[] = "t.SubjectID IN({$v})";
                    }else{
                        $temp[] = "t.SubjectID={$v}";
                    }
                }else{
                    $temp[] = "`{$k}`='{$v}'";
                }
            }
            $where = implode(' AND ', $temp);
            unset($temp);
        }
        $id = $this->getId();
        if(!empty($id)){
            $where .= ' AND TTID='.$id;
        }
        $sql = "SELECT %s FROM `zj_originality_template_test` tt LEFT JOIN `zj_originality_template` t ON t.TID=tt.TID LEFT JOIN `zj_originality_stage` s ON s.SID=t.SID WHERE %s ORDER BY tt.TTID ASC";
        $count = M()->query(sprintf($sql, 'COUNT(tt.TTID) AS num', $where));
        if(empty($count)){
            $count[0]['num'] = 0;
        }
        $count = $count[0]['num'];
        $this->pagtion['page'] = page($count, $this->pagtion['page'], $this->pagtion['recordsNum']);

        $limit = ($this->pagtion['recordsNum'] * ($this->pagtion['page'] - 1)).','.$this->pagtion['recordsNum'];
        $sql .= " LIMIT {$limit}";
        $result = M()->query(sprintf($sql, 'tt.*, t.Title, s.SID, 0 AS UserNum', $where));
        //取出TTID
        $list = array();
        foreach($result as $value){
            $list[] = $value['TTID'];
        }
        $ort = new OriginalityRelateTestModel();
        $data = $ort->getPartakeNumByList($list);
        foreach($result as $key=>$value){
            $result[$key]['UserNum'] = $data[$value['TTID']];
            if(empty($result[$key]['UserNum'])){
                $result[$key]['UserNum'] = 0;
            }
        }
        unset($data);
        return array($result, $count);
    }


    /**
     * 修改人数限制
     * @param int $num 更新的数量
     * @return boolean
     * @author demo 
     */
    public function updatePersonLimit($num){
        return $this->update(array(
            'LimitNum' => $num
        ));
    }


    /**
     * 批量更新人数
     * @param string $ids ttid列表，逗号分隔
     * @param int $num 人数
     * @return boolean
     * @author demo 
     */
    public function batchUpdatePersonLimit($ids, $num){
        return $this->updateData(
            array('LimitNum' => $num),
            'TTID IN('.$ids.')'
        );
    }


    /**
     * 保存数据
     * @param array $data
     * @param int $tid 模板id
     * @param boolean $act true时为插入操作，或者为修改
     * @return boolean
     * @author demo 
     */
    public function save($data, $tid, $act){
        if(!$act){
            //修改操作后期完善
            // $this->deleteData( 'TID='.$tid);   
        }else{
            $data = $this->combineData($data, $tid);
            $result = $this->addAllData($data[0]);
            if($result === false){
                $this->setErrorMessage('30510');
                return false;
            }
            //查询出试题数据，用于插入知识点数据
            $result = $this->selectData('TTID', 'TID='.$tid, 'TTID ASC');
            $knowledge = array();
            foreach($result as $key=>$value){
                foreach($data[1][$key] as $val){
                    $knowledge[] = array(
                        'TTID' => $value['TTID'],
                        'KlID' => (int)$val //此处强制转换为数字，为'all'的内容将被转换为0
                    );
                }
            }
            $otk = new OriginalityTestKnowledgeModel();
            if($otk->save($knowledge, $act) === false){
                $this->setErrorMessage($otk->getErrorMessage());
                return false;
            }
        }
        return true;
    }

    /**
     * 参与人数是否超限
     * @return boolean 超出返回true
     * @author demo 
     */
    public function isOverLimitOfPersons(){
        //查询出限定数量
        $limit = $this->findData('LimitNum', $this->getCondition());
        $limit = $limit['LimitNum'];
        $ort = new OriginalityRelateTestModel();
        $count = $ort->getPartakeCountByTplTestId($this->getId());
        return ($count > $limit);
    }

    /**
     * 根据模板id返回数据
     * @param int $tid 模板id
     * @param string $field
     * @return array
     * @author demo 
     */
    public function getTplTestById($tid, $field="*"){
        return $this->selectData(
            $field,
            'TID='.(int)$tid
        );
    }

    /**
     * 返回指定期次和学科的数据
     * @param int $currenStageId 当前期次id
     * @param int $subjectId 学科id
     * @param int $stageId，默认为0时，查询所有期次的试题
     * @return array
     * @author demo 
     */
    public function getTestByStageId($currentStageId, $subjectId, $stageId=0){
        $currentStageId = (int)$currentStageId;
        $stageId = (int)$stageId;
        //仅一期的时候，返回空数据
        if($currentStageId <= 1){
            return array(0, 0);
        }
        //给定的期次大于当前期次时，仅返回当前期次-1的数据
        if($currenStageId <= $stageId){
            $stageId = $currenStageId - 1;
        }
        //查询出当前试题的知识点
        $otk = new OriginalityTestKnowledgeModel();
        $otk->setPagtion(10000, 1); //不限制知识点
        $knowledge = $otk->getKnowledgeByTplTestId($this->getId());
        $knowledge = $knowledge['data'];
        foreach($knowledge as $k=>$v){
            if(0 != strtolower($v['KlID'])){
                $knowledge[$k] = (int)$v['KlID'];
            }
        }
        $result = $otk->getKnowledges($knowledge, $currentStageId, $subjectId, $stageId);
        $data = array();
        //按照模板试题id为键生成知识点数组信息
        foreach($result as $k=>$v){
            $data[$v['ttid']][] = $v['kl'];
        }
        unset($result);
        $size = count($knowledge);
        //获取$data[$k]与$knowledge的知识点交集，筛选出满足该知识点的数据
        foreach($data as $k=>$v){
            $intersect = array_intersect($v, $knowledge);
            if(count($intersect) != $size){
                unset($data[$k]);
            }else{
                $data[$k] = '';
            }
        }
        $ort = new OriginalityRelateTestModel();
        //运用当前的分页信息
        $ort->setPagtion($this->pagtion['recordsNum'], $this->pagtion['page']);
        return $ort->getTestList(array_keys($data), 0);
        //需要处理模板试题关联表的数据
    }

    /**
     * 拆分数据，然后返回统一插入操作
     * @param array $data
     * @return array 下标0为试题数据，1为知识点数据
     * @author demo 
     */
    private function combineData($data, $tid){
        $list = $knowledge = array();
        foreach($data as $value){
            foreach($value as $key=>$val){
                if(is_integer($key)){
                    $temp = array();
                    $temp['TID'] = $tid;
                    $temp['TypesID'] = $value['typeid'];
                    $temp['IfChoose'] = $val['ifchoose'];
                    $temp['Diff'] = $val['diff'];
                    $temp['Type'] = $val['testchoose'];
                    $temp['Score'] = $val['scores'];
                    $temp['TestNum'] = $val['nums'];
                    $list[] = $temp;
                    $knowledge[] = empty($val['rounds']) ? '' : explode(',', $val['rounds']);
                }
            }
        }
        return array($list, $knowledge);
    }
}