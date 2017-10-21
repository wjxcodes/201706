<?php
/**
 * @author demo
 * @date 2015年9月28日
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class SystemEditionLogModel extends BaseModel{
    /**
     * 获取更新日志
     * @param string $field 获取字段 默认全部
     * @param string $where 获取条件
     * @param string $order 排序 默认显示时间倒序
     * @return array 日志信息
     * @author demo
     */
    public function getSystemEditionLog($field='*',$where='1=1',$order='ShowTime Desc'){
        $list=$this->selectData(
                $field,
                $where,
                $order
        );
        return stripslashes_deep($list);
    }

}