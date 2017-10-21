<?php
/**
 * @author demo
 * @date 2014年10月16日
 */
/**
 * 自增编号模型类，用于处理自增编号相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class AutoIncModel extends BaseModel{
    /**
     * 获取自增编号；
     * @return int 自增编号或者返回错误
     * @author demo
     */
    public function getOrderNum(){
        $orderNum=$this->insertData(
            array('LoadTime'=>time()));
        if($orderNum<10001){
            $orderNum=10001;
            $this->updateData(
                array('AutoID'=>'10001'),
                'AutoID='.$orderNum);
        }
        return $orderNum;
    }
}