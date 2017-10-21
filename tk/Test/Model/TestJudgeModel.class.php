<?php
/**
 * @author demo
 * @date 2014年8月4日
 */
/**
 * 试题类型模型类，用于处理试题类型相关操作
 */
namespace Test\Model;
class TestJudgeModel extends BaseModel
{
    /**
     * 查询数据ById；
     * @param string $test_id 试题id
     * @return array
     * @author demo
     */
    public function getJudgeByTestID($test_id) {
        $result = $this->selectData(
            'TestID,OrderID,IfChoose',
            'TestID=' . $test_id,
            'JudgeID asc'
        );
        return $result;
    }
}