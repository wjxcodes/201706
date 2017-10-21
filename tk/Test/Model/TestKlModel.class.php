<?php
/**
 * @author demo
 * @date 2014年10月28日
 */
/**
 * 试题对应知识点模型类，用于处理试题对应知识点相关操作
 */
namespace Test\Model;
class TestKlModel extends BaseModel{

    /**
     * 以试题id获取试题列表
     * @param int $testID 试题id
     * @return array 返回以试题id为键的数组
     * @author demo
     */
    public function getTestListByID($testID){
        //兼容数组和字符串
        if(is_array($testID)) $testID=implode(',',$testID);

        //获取试题id对应数据集
        $testListArray = $this->selectData(
            '*',
            'TestID in ('.$testID.')'
        );

        //以试题id为键值
        $testListArrayByID=array();
        foreach($testListArray as $iTestListArray){
            $testListArrayByID[$iTestListArray['TestID']][]=$iTestListArray['KlID'];
        }
        return $testListArrayByID;
    }

}