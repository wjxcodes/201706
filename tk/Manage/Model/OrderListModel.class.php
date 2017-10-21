<?php
/**
 * @author demo
 * @date  2015-11-14
 */
/**
 * 支付订单处理Model
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class OrderListModel extends BaseModel{

    /**
     * 增加订单记录
     * @author demo
     */
    public function addOrder($param){
        return $this->insertData( $param);
    }

    /**
     * 查询订单信息
     * @author demo
     */
    public function getOrderInfo($field='*',$where,$order='',$limit=''){
         return $this->selectData($field,$where,$order,$limit);
    }

    /**
     * 更新订单数据
     * @param array $param 更新支付宝返回的数据
     * @author demo
     */
    public function updateOrder($param,$orderID){
        $this->updateData($param,['OrderID'=>$orderID]);
    }
}
?>