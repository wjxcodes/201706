<?php
/**
 * 校本题库文档试题关联model
 * @author demo 2015-12-2
 */
namespace Custom\Model;
use Common\Model\BaseModel;
class CustomTestDocModel extends BaseModel{
    /**
     * 新增数据
     * @param int $doc 文档id
     * @param array $arr 试题id数组
     * @param int $order 试题序号
     * @return boolean
     * @author demo 
     */
    public function add($doc, $arr, $order=0){
        $num = count($arr);
        $data = array();
        for($i=0; $i<$num; $i++){
            $data[] = array(
                'TestID' => $arr[$i],
                'DocID' => $doc,
                'OrderRule' => $order === 0 ? ($i+1) : $order
            );
        }
        unset($arr);
        $result = $this->addAllData($data);
        return ($result !== false);
    }

    /**
     * 修改数据
     * @param int $doc 文档id
     * @param array $arr 试题id数组
     * @param int $order 修改试题的顺序。大于等于0
     *                   为0时，将重新生成所有数据。
     *                   大于0时，将更新$order至count($arr)值之间的数据，超出则新增
     * @return boolean
     * @author demo 
     */
    public function update($doc, $arr, $order=0){
        $doc = (int)$doc;
        $order = (int)$order;
        //处理所有数据
        if($order <= 0){
            //删除数据
            $result = $this->deleteData('DocID='.$doc);
            if($result === false){
                return $result;
            }
            return $this->add($doc, $arr);
        }
        $count = $order-1+count($arr);
        $result = $this->sequentialQuery($doc, $order, $count);
        $resultCount = $order-1+count($result);
        unset($result);
        $i=0;  //获取$arr的试题id
        for(; $order<=$resultCount; $order++){
            $this->updateData(
                array(
                    'TestID' => $arr[$i++]
                ),
                'DocID='.$doc.' AND OrderRule='.$order
            );
        }
        //当结果小于$count时，$arr多出的数据执行新增操作
        if($count > $resultCount){
            $data = array();
            for(; $i<$count; $i++){
                $data[] = array(
                    'TestID' => $arr[$i],
                    'DocID' => $doc,
                    'OrderRule' => ++$resultCount  //在原有数据基础上进行累加                  
                );
            }
            $result = $this->addAllData($data);
            return ($result !== false);
        }
        return true;
    }

    /**
     * 查询出指定顺序区间的数据
     * @param int $doc
     * @param int $start
     * @param int $end 当为0时，查询$start之后的所有数据
     * @return array
     * @author demo 
     */
    public function sequentialQuery($doc, $start, $end=0){
        $doc = (int)$doc;
        $where = "DocID={$doc}";
        if(0 == $end){
            $where .= " AND OrderRule >= {$start}";
        }else{
            $where .= " AND OrderRule BETWEEN {$start} AND {$end}";
        }
        return $this->selectData('TestID,OrderRule', $where, 'OrderRule ASC');
    }

    /**
     * 根据试题id列表查询结果
     * @param array $ids
     * @return array
     * @author demo 
     */
    public function getTestByIds($ids){
        $result = $this->getModel('TestReal')->getTestIndex(
            array('testid','analytic','answer','remark', 'test'),
            array('TestID'=>implode(',', $ids)),
            array('@id ASC'),
            array('page'=>1,'perpage'=>10000)
        );
        if(empty($result[0])){
            return array();
        }
        $result = $result[0];
        $list = array();
        foreach($result as $value){
            $list[$value['testid']] = $value;
        }
        $result = $list;
        $list = array();
        foreach($ids as $id){
            if(isset($result[$id]))
                $list[$id] = $result[$id];
        }
        return $result=$list;
    }
}