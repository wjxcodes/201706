<?php
/**
 * 校本题库属性Model类
 * @auhtor
 * @date 2015-1-6
 */
namespace Custom\Model;
use Common\Model\BaseModel;
class CustomTestAttrModel extends BaseModel{

    protected $modelName = 'CustomTestAttr';

    /**
     * 添加数据
     * @param array $data
     * @return 失败返回false，成功返回影响的行数
     */
    public function insertData($data){
        $data['AddTime'] = $data['LastUpdateTime'] = time();
        return parent::insertData($data);
    }

    /**
     * 修改数据
     * @param array $data
     * @return 失败返回false，成功返回影响的行数
     */
    public function updateData($testid, $data){
        $newtestid = ltrim($testid, \Test\Model\TestQueryModel::DIVISION);

        if(is_numeric(preg_replace('/,|\s/i','',$newtestid))){
            $data['LastUpdateTime'] = time();
            return parent::updateData($data, 'TestID IN('.$newtestid.')');
        }

        return parent::updateData($testid, $data);
    }

    /**
     * 删除指定试题id的属性
     * @param int $testid 试题id
     * @return 失败返回false，成功返回影响的行数
     */
    public function deleteData($testid){
        $testid = ltrim($testid, \Test\Model\TestQueryModel::DIVISION);
        return parent::deleteData(
            array('TestID' => $testid)
        );
    }

    /**
     * 查找试题属性信息
     * @param string $testid
     * @return array
     * @author demo
     */
    public function getTestAttributes($testid){
        $testid = ltrim($testid, \Test\Model\TestQueryModel::DIVISION);
        return $this->selectData(
                '*',
                'TestID IN('.$testid.')');
    }

    /**
     * 判断试卷试题是否超出
     * @param string $testid
     * @return string 未超出返回空字符串
     * @author demo
     */
    public function isOverLimit($testid){
        $result = $this->selectData(
            'TypesID',
            'TestID IN ('.$testid.')',
            'TestID DESC');
        $data = array();
        foreach($result as $value){
            $typesid = $value['TypesID'];
            if(!isset($data[$typesid]['num'])){
                $data[$typesid]['num'] = 0;
            }
            $data[$typesid]['num']++;
        }
        $result = '';
        $cache = $this->getApiCommon('Types/types');
        foreach($data as $key=>$value){
            $types = $cache[$key];
            if($value['num'] > $types['Num']){
                $result .= "[{$types['TypesName']}]";
            }
        }
        return $result;
    }

    /**
     * 当前试题是否可编辑
     * @param int $id 试题id
     * @return boolean|string 可以编辑返回true，或者返回错误码
     */
    public function whetherEdit($id){
        $id = ltrim($id, \Test\Model\TestQueryModel::DIVISION);
        $result = $this->getModel('CustomTestTaskStatus')->getRecordByTestId($id);
        $ifLock = $result['IfLock'];
        if(empty($ifLock)){
            return true;
        }
        $result = $this->findData('Status', 'TestID='.$id);
        $status = (int)$result['Status'];
        if(-1 == $status){
            return true;
        }
        if($status == 1){
            return '30702';
        }
        return '30701';
    }

    /**
     * 当前试题是否可删除
     * @param int $id 试题id
     * @return boolean 可以删除返回true
     */
    public function whetherDelete($id){
        $id = ltrim($id, \Test\Model\TestQueryModel::DIVISION);
        $result = $this->getModel('CustomTestTaskStatus')->getRecordByTestId($id);
        $ifLock = $result['IfLock'];
        if(empty($ifLock)){
            return true;
        }
        $result = $this->findData('Status', 'TestID='.$id);
        $status = (int)$result['Status'];
        if(1 == $status || -1 == $status){
            return true;
        }
        return false;
    }
    /**
     * 修改校本题库主表试题状态
     * @param $testID
     * @param $status
     * @return bool
     * @author demo
     */
    public function setStatus($testID,$status){
        $statusData['Status']=$status;
        if($this->updateData(
            $testID,
            $statusData
        )){
            return true;
        }else{
            return false;
        }
    }
}