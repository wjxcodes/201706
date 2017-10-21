<?php
/**
 * @author demo
 * @date 2014年11月19日
 */

/**
 * 试卷被测试数据记录表
 */
namespace Doc\Model;
class DocTestDataModel extends BaseModel
{
    /**
     * 试卷被测试的次数自增
     * 之后可以增加参数，自增其它字段
     * @param int $docID 试卷ID
     * @author demo
     */
    public function addTestTimes($docID) {
        $ifExist = $this->findData(
            'DocTestID',
            ['DocID' => $docID]);
        if ($ifExist) {
            $this->updateData('AatTestTimes=AatTestTimes+1',['DocID' => $docID]);
        } else {
            //新增一条数据
            $data = ['DocID' => $docID, 'AatTestTimes' => 1];
            $this->insertData(
                $data);
        }
    }

}