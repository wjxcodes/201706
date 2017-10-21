<?php
/**
 * 校本题库优化任务领取Model
 * @author demo
 * @date 2015-3-26
 */
namespace Custom\Model;
use Common\Model\BaseModel;
class CustomTestTaskListModel extends BaseModel{

    protected $modelName = 'CustomTestTaskList';
    /**
     * 添加数据
     * @param array $data
     * @return 失败返回false，成功返回影响的行数
     */
    public function insertData($data){
        $data['AddTime'] = time();
        return parent::insertData(
            $data);
    }

    /**
     * 修改数据
     * @param array $data 数据
     * @param array $where 条件
     * @return 失败返回false，成功返回影响的行数
     */
    public function updateData($data, $where){
        return parent::updateData(
            $data,
            $where
        );
    }

    /**
     * 查询数据
     * @param array $field 字段
     * @param array $where 条件
     * @return 失败返回false，成功返回影响的行数
     */
    public function findData($field, $where, $order=''){
        return parent::findData(
            $field,
            $where,
            $order
        );
    }

    /**
     * 删除记录
     * @param int $id，试题id
     * @return 失败返回false，成功返回影响的行数
     */
    public function deleteData($id){
        return parent::deleteData(
            array('TestID' => $id)
        );
    }

    /**
     * 根据试题ID获取试题内容
     * @param $field 需要的字段
     * @param $testID 试题ID
     * @return array
     * @author demo
     */
    public function getFieldByTestID($field,$testID){
        return $this->findData($field,'TestID='.$testID.' and TaskTime>'.time(),'ListID Desc');
    }

    /**
     * 记录重新编辑次数
     * @param int $id 优化状态列表id
     * @return 失败返回false，成功返回影响的行数
     * @author demo
     */
    public function setBackTimesCount($listID){
        return $this->conAddData('BackTimes=BackTimes+1', 'ListID='.$listID);
    }

    /**
     * 修改教师领取任务表状态
     * @param $testID
     * @param $status
     * @return bool
     * @author demo
     */
    public function setStatus($listID,$status,$taskTime=0,$msg=''){
        $statusData['Status']=$status;
        if($taskTime) $statusData['TaskTime']=$taskTime;
        if($msg) $statusData['ErrorMsg']=$msg;
        if($this->updateData(
            $statusData,
            'ListID='.$listID
        )){
            return true;
        }else{
            return false;
        }
    }

    public function customTestTaskListSelectCount($where){
        return $this->unionSelect('customTestTaskListSelectCount',$where);
    }

    public function customTestTaskListSelectByPage($where,$page){
        return $this->unionSelect('customTestTaskListSelectByPage',$where,$page);
    }

    public function customTestTaskListSelectByWhere($where){
        return $this->unionSelect('customTestTaskListSelectByWhere',$where);
    }

    public function customTestTaskListGroupBy($sqlString){
        return $this->unionSelect('customTestTaskListGroupBy',$sqlString);
    }

    public function customTestTaskListGroupPage($sqlString,$page){
        return $this->unionSelect('customTestTaskListGroupPage',$sqlString,$page);
    }

    public function customTestTaskListSelectCountByWhere($where){
        return $this->unionSelect('customTestTaskListSelectCountByWhere',$where);
    }

    public function customTestTaskListSelectByUserPage($where,$page){
        return $this->unionSelect('customTestTaskListSelectByUserPage',$where,$page);
    }
}