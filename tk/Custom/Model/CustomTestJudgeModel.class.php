<?php
/**
 * 校本题库小题Model类
 * @auhtor 
 * @date 2015-1-6
 */
namespace Custom\Model;
use Common\Model\BaseModel;
class CustomTestJudgeModel extends BaseModel{

    protected $modelName = 'CustomTestJudge';
    /**
     * 查找试题小题
     * @param string $testid
     * @return array
     * @author demo
     */
    public function getTestJudges($testid){
        $testid = str_replace(\Test\Model\TestQueryModel::DIVISION, '', $testid);
        return $this->selectData(
             '*',
            'TestID IN('.$testid.')');
    }

    /**
     * 删除数据；
     * @param int $testId 要删除的试题id
     * @param string $orderId 要删除的OrderId字段编号，默认为空时删除$testId所有小题
     * @return boolean
     * @author demo
     */
    public function deleteData($testId,$orderId=''){
        $testId = str_replace(\Test\Model\TestQueryModel::DIVISION, '', $testId);
        $where = 'TestID='.(int)$testId;
        if($orderId != ''){
            $where .= " AND OrderID IN({$orderId})";
        }
        return parent::deleteData(
            $where);
    }

    /**
     * 保存小题
     * @param int $id 试题id
     * @param array $data 小题数据
     * @return boolean
     * @author demo
     */
    public function saveData($testId,$data){
        $testId = str_replace(\Test\Model\TestQueryModel::DIVISION, '', $testId);
        $size = count($data);
        $num = $this->getOrder($testId, $size);
        //结果为0，将直接修改数据
        if(0 != $num){      
            $total = $i = 0;
            //$num > 0 $data小题数量小于表中小题数量，将删除表中的小题，或者添加小题到表中
            if($num > 0){
                $total = $size + $num; //获取表中对应试题的小题数
                $i = $size;            //表中将删除数据的起始序号(i+1)(OrderID)
            }else{
                $total = $size;        //获取$data小题数
                $i = $total + $num;    //$data需添加数据到表中起始序号(i+1)
            }
            $datas = array();
            for(; $i<$total; $i++){
                $datas[] = $i + 1;     //获取小题序号
            }
            unset($total,$size,$i);
            if($num > 0){
                $result = $this->deleteData($testId,implode(',', $datas));
                if($result === false)
                    return false;
            }else{
                $newData = array();
                $i = count($datas)-1;
                //得到需添加数据的数组
                for(; $i>=0; $i--){
                    $judge = $data[$datas[$i]-1]; //得到$data中需新增到表中的数据，减1获取正确的索引
                    $newData[] = array(
                        'TestID'=>$testId,
                        'OrderID'=>(int)$judge['no'],
                        'IfChoose'=>(int)$judge['type']
                    );
                    unset($data[$datas[$i]-1]); //新插入的数据将不在进行后面的修改操作
                }
                $result = $this->addAllData($newData);
                if($result === false)
                    return false;
            }
        }
        //修改数据，------------------此处未进行sql检查-------------------
        foreach($data as $value){
            $where = array('TestID'=>$testId,'OrderID'=>(int)$value['no']);
            $this->updateData(
                array('IfChoose'=>(int)$value['type']),
                $where);
        }
        return true;
    }

    /**
     * 返回小题
     * @param int $id 试题编号id
     * @param int $num 当前小题数量
     * @return int
     */
    public function getOrder($id, $num){
        $id = str_replace(\Test\Model\TestQueryModel::DIVISION, '', $id);
        $result = $this->selectData(
            'count(JudgeID) as num',
            array('TestID'=>(int)$id)
        );
        return ((int)$result[0]['num'] - $num);
    }
}