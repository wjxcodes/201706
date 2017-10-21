<?php
/**
 * @author demo
 * @Date: 2014年10月14日
 */
/**
 * 日志管理Model组类，用于日志管理相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class LogModel extends BaseModel{
    /**
     * 所用用户操作日记录入操作
     * @param array $data 需要录入的参数
     * @return bool
     * @author demo
     */
    public function insertLog($data){
        return $this->insertData(
            $data
        );
    }
}
?>