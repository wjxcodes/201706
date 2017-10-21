<?php
/**
 * @author demo
 * @date 2014年9月15日
 * @update 2015年1月21日
 */
/**
 * 收藏模型类，用于处理用户收藏目录操作
 */
namespace User\Model;
use Common\Model\BaseModel;
class UserCatalogModel extends BaseModel{

    /**
     * 按条件查找目录
     * @param array $data 查找的条件
     * @param string $field 返回的字段
     * @return array  
     * @author demo
     * @date 2014年9月15日 
     */
    public function getCatalogList($field,$data){
        return $this->selectData(
            $field,
            $data,
            'CatalogID ASC,OrderID ASC');
     }

  /**
   * 获取两级目录列表
   * @param string $data
   * @return array
   * @arthor 
   * @date 2014年9月15日
   */
    public function getArrList_2($data){
        $node = array();
        $list = $this->selectData(
            '*',
            $data,
            'OrderID ASC,CatalogID ASC');
        if($list){
            foreach ($list as $i => $iList) {
                 $node[$i] = $iList;
                 $listn = $this->selectData(
                     '*',
                     'FatherID = '. $iList['CatalogID'],
                     'AddTime ASC,OrderID ASC');
                 if($listn){
                     $node[$i]['child']=$listn;
                 }
            }
        }
        return $node;
    }
}