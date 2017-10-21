<?php
/**
 * @author demo
 * @date 2014年8月19日
 */
/**
 * 教师任务模型类，用于处理教师任务相关操作
 */
namespace Teacher\Model;
use Common\Model\BaseModel;
class TeacherWorkModel extends BaseModel{

    /**
     * 修改替换权限；
     * @param int $id 任务id
     * @param int $hasReplace 状态
     * @return boolean
     * @author demo
     */
    public function updateHasReplace($id,$hasReplace=0){
        return $this->updateData(
            array('HasReplace'=>(int)$hasReplace),
            'WorkID='.(int)$id);
    }
    /**
     * 审核次数
     * @param int $id 任务ID
     * @return int $checkTimes 次数
     * @author demo
     */
    public function getCheckTimes($id){
        return $this->dbConn->findData(
            'TeacherWork',
            'CheckTimes',
            'WorkID='.(int)$id
        )['CheckTimes'];
    }
}
?>