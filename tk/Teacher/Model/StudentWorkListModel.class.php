<?php
/**
 * @author demo
 * @date 2014年10月23日
 * @update 2015年1月29日
 */
/**
 * 公式任务列表管理类
 */
namespace Teacher\Model;
use Common\Model\BaseModel;
class StudentWorkListModel extends BaseModel{

    /**
     * 验证任务文档是否已经分配
     * @param int $doc 文档id
     * @return mixed 存在返回true，或者返回错误id数组
     */
    public function checkIdentifier($doc){
        $buffer=$this->dbConn->selectData('StudentWorkList',
            'DocID',
            'DocID in ('.$doc.')');
        $tmpArray=array();
        foreach($buffer as $iBuffer){
            $tmpArray[$iBuffer['DocID']]=$iBuffer['DocID'];
        }
        $doc = explode(',', $doc);
        $msg = array();
        foreach($doc as $iDoc){
            if(!empty($tmpArray[$iDoc])){
                $msg[] = $iDoc.'已经分配';
            }
        }
        if(empty($msg)){
            return true;
        }
        return $msg;
    }
    /**
     * 检查任务列表的所有任务是否为指定状态
     * @param int $workid 任务id
     * @param array() $status 
     * @return boolean 完成返回true
     */
    public function isSpecifiesState($workid,$status=array(1)){
        $where = 'WorkID='.(int)$workid;
        $result = $this->dbConn->selectData(
            'StudentWorkList',
            'Status',
            $where);
        foreach($result as $iResult){
            if(!in_array($iResult['Status'],$status)){
                return false;
            }
        }
        return true;
    }
    /**
     * 删除指定workid的内容
     * @param int $id workid
     * @return boolean 失败返回false
     */
    public function deleteRecordsByWorkId($workid){
        $where = 'WorkID='.$workid;
        $result = $this->dbConn->selectData(
            'StudentWorkList',
            '*',
            $where);
        //还原文档标识
        foreach($result as $iResult){
            $data['IfEq'] = 0;
            $this->getModel('Doc')->updateData(
                $data,
                'DocID='.$iResult['DocID']);
        }
        return $this->dbConn->selectData(
            'StudentWorkList',
            $where);
    }
    /**
     * 删除指定worklistid的内容
     * @param int $id workid
     * @return boolean 失败返回false
     */
    public function deleteRecordsByWorkListId($worklistid){
        $where = 'WLID='.$worklistid;
        $result = $this->dbConn->selectData(
            'StudentWorkList',
            '*',
            $where);
        //还原文档标识
        $data['IfEq'] = 0;
        $result = $this->getModel('Doc')->updateData(
            $data,
            'DocID='.$result['DocID']);
        if(empty($result))
            return false;
        return $this->deleteData(
            'WLID in （'.$worklistid.')');
    }
    /**
     * 更改指定workid的状态
     * @param int $workid
     * @param int $status
     * @return boolean 失败返回false
     */
    public function updateStatusByWorkId($workid,$status){
        $where = 'WorkID IN('.$workid.')';
        $data['Status'] = $status;
        $result = $this->updateData(
            $data,
            $where);
        return ($result === true);
    }
    /**
     * 更改指定worklistid的状态
     * @param int $worklistid
     * @param int $status
     * @return boolean 失败返回false
     */
    public function updateStatusByWorkListId($worklistid,$status){
        $where = 'WLID='.$worklistid;
        $data['Status'] = $status;
        $result = $this->updateData(
            $data,
            $where);
        return ($result !== false);
    }
}