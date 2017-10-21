<?php
/**
 * @author demo 
 * @date 2014年10月14日
 * @update 2015年9月28日
 */
/**
 * 图片轮换
 */
namespace Index\Model;
class ImagePlayModel extends BaseModel{
    
    /**
     * 查询轮换图片
     * @param string $field 查询字段 默认全部
     * @param string $where 查询条件
     * @param string $order 排序
     * @author demo
     */
    public function imagePlay($field='*',$where='Target = index',$order='OrderID ASC'){
        return $buffer = $this->selectData(
                    $field,
                    $where,
                    $order
                );
    }
}