<?php
/**
 * 校本题库任务状态Model
 * @author demo
 */
namespace Custom\Model;
use Common\Model\BaseModel;
class CustomTestTaskStatusModel extends BaseModel{
    /**
     * 获取校本题库试题状态
     * @return array
     * @author demo
     */
    public function getCustomtestStatus(){
        return array('0'  => '待优化',
                     '1'  => '完成标引',
                     '2'  => '审核成功',
                     '-1' => '优化不通过返回',
                     '-2' => '优化失败');
    }

    /**
     * 添加数据
     * @param array $data
     * @return 失败返回false，成功返回影响的行数
     */
    public function insertData($data){
        $data['AddTime'] = time();
        return parent::insertData($data);
    }

    /**
     * 修改状态
     * @param int $id 记录id
     * @param int $status 默认为1审核中
     * @return 失败返回false，成功返回影响的行数
     */
    public function updateStatus($id, $status=1){
        return $this->updateData(array('Status' => $status),'StatusID='.$id);
    }

    /**
     * 删除状态列表中的数据
     * @param int $id 主键id
     * @return 失败返回false，成功返回影响的行数
     */
    public function deleteData($id){
        return parent::deleteData(
            array('TestID' => $id)
        );
    }

    /**
     * 记录重新编辑次数
     * @param int $id 优化状态列表id
     * @return 失败返回false，成功返回影响的行数
     */
    public function setBackTimesCount($id){
        return $this->conAddData('BackTimes=BackTimes+1', 'StatusID='.$id);
    }

    /**
     * 记录编辑次数
     * @param int $id 优化状态列表id
     * @return 失败返回false，成功返回影响的行数
     */
    public function setEditTimesCount($id){
        return $this->conAddData('EditTimes=EditTimes+1', 'StatusID='.$id);
    }

    /**
     * 查看当前任务是否过期
     * @param int $id
     * @return boolean 失效返回true
     */
    public function isExpires($id){
        $result = $this->findData('TaskTime','StatusID='.$id);
        return $result['TaskTime'] < time();
    }

    /**
     * 获取指定testid的数据
     * @param int $testID 试题id
     * @return array
     */
    public function getRecordByTestId($testID){
        return $this->findData('*','TestID='.$testID);
    }
    /**
     * 根据试题ID获取试题内容
     * @param $field 需要的字段
     * @param $testID 试题ID
     * @return array
     * @author demo
     */
    public function getFieldByTestID($field,$testID){
        return $this->findData($field,'TestID='.$testID);
    }

    /**
     * 修改试题队列任务表状态
     * @param $testID
     * @param $status
     * @return bool
     * @author demo
     */
    public function setStatus($testID,$status,$taskTime=0){
        $statusData['Status']=$status;
        if($taskTime) $statusData['TaskTime']=$taskTime;

        if($this->updateData(
            $statusData,
            'TestID='.$testID
        )){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 清除试题任务状态表中的属性
     * @param $testID
     * @return bool
     * @author demo
     */
    public function clearTestStatus($testID){
        $statusData['IfIntro']=0;
        $statusData['IfDel']=0;
        $statusData['ErrorMsg']='';
        if($this->updateData(
            $statusData,
            'TestID='.$testID
        )){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 查询多条试题的相关信息
     * @param array $ids 试题id
     * @param string $fields 字段
     * @return array
     * @author demo
     */
    public function getRecordByTestIds($ids, $field='*'){
        $ids = implode(',', $ids);
        $ids = str_replace(\Test\Model\TestQueryModel::DIVISION, '', trim($ids, ','));
        if(empty($ids)){
            return array();
        }
        return $this->selectData(
            $field,
            'TestID IN ('.$ids.')'
        );
    }

    /**
     * 设置校本题库任务文档路径
     * @param Int $testID 试题ID
     * @param String $docPath 文档路径
     * @return bool
     * @author demo
     */
    public function setDocPath($testID,$docPath){
        $statusID=$this->selectData(
            'StatusID',
            'TestID='.$testID,
            'Status Desc',
            '1'
        )[0]['StatusID'];
        if(!$statusID){
            return false;
        }
        $data['DocPath']=$docPath;
        if($this->updateData(
            $data,
            'StatusID='.$statusID
        )){
            return true;
        }else{
            return false;
        }
    }

    public function customTestTaskStatusSelectCount($where){
        return $this->unionSelect('customTestTaskStatusSelectCount', $where);
    }

    public function customTestTaskStatusSelectByPage($where,$page){
        return $this->unionSelect('customTestTaskStatusSelectByPage', $where,$page);
    }
}