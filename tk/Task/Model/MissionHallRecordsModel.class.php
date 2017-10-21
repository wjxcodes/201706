<?php
/**
 * @author demo
 * @date 2015年10月20日
 */
namespace Task\Model;
use Common\Model\BaseModel;
class MissionHallRecordsModel extends BaseModel{
    /**
     * 构造函数（主要设置了此模型modelName）
     * @author demo
     */
    function __construct() {
        parent::__construct();
        $this->modelName = 'MissionHallRecords';
    }
    
    /**
     * 删除任务记录
     * 删除成功则返回删除的结果集，失败返回false
     * @param string $where
     * @return array | false 
     * @author demo
     */
    public function delete($where){
        $result = $this->getList('*','',$where);
        $del = $this->deleteData($where,$this->modelName);
        if($del !== false){
            return $result;//要删除的结果集
        }else{
            return false;
        }
    }
    
    /**
     * 获得任务领取记录 默认关联用户表
     * @param string $field 要获取的字段
     * @param bool $join 关联表 默认关联用户表
     * @param string $where 条件
     * @param string $order 排序
     * @param string $limit 条数 
     * @return array | false
     * @author demo
     */
    public function getList($field='*',$join=false,$where='',$order='mhr.MHRID DESC',$limit='',$group=''){
        $join = $join === false ? 'zj_user u on u.UserID = mhr.UserID' : $join ;
        return $this->dbConn->table('zj_mission_hall_records mhr')
                        ->field($field)
                        ->join($join)
                        ->where($where)
                        ->group($group)
                        ->order($order)
                        ->limit($limit)
                        ->select();
    }

    /**
     * 获取任务领取记录关联领取记录日志表
     * @param int $tid 任务ID
     * @param int $page 当前页
     * @param int $prepage 每页显示数
     * @return array 0=>记录结果 1=>记录总数
     * @author demo
     */
    public function getRecordList($tid, $page, $prepage=20){
        $count = $this->unionSelect('countMissionHallRecordList',$tid);
        if(empty($count['c'])){
            $count['c'] = 0;
        }
        $limit = page($count['c'], $page, $prepage);
        $limit = (($limit-1)*$prepage).','.$prepage;
        $result = $this->unionSelect('getMissionHallRecordList', $tid, $limit);
        return array($result, $count['c']);
    }
    
    /**
     * 获得任务领取记录 关联任务表 用户表
     * @param string $field 要获取的字段
     * @param string $where 条件
     * @param string $order 排序
     * @param string $limit 条数
     * @return array | false
     * @author demo
     */
    public function getAndTstkAndUser($field='mhr.*,u.UserName,mht.Title',$where='',$order='',$limit=''){
        return $this->unionSelect('getMissionHallRecordByLimit',$field,$where,$order,$limit);
    }
    
    /**
     * 领取任务 保存任务ID 用户ID 领取状态
     * @param int $MHTID 任务ID
     * @param int $UserID 用户ID
     * @return int | false
     * @author demo
     */
    public function receiveTask($MHTID,$UserID,$status=''){
        $data['MHTID'] = (int)$MHTID;
        $data['UserID'] = (int)$UserID;
        $data['Status'] = empty($status) ? 1 : $status ;
        //if($this->isReceived($data['MHTID'],$data['UserID'])) return false;
        $data['AddTime'] = time();
        return $this->insertData($data);
    }
    
    /**
     * 任务是否被用户领取
     * @param int $MHTID 任务ID
     * @param int $UserID 用户ID
     * @return array | false
     * @author demo
     */
    public function isReceived($MHTID,$UserID){
        return $this->selectData('*','MHTID = '.$MHTID.' AND UserID = '.$UserID,'','',$this->modelName);
    }

    /**
     * 用户完成对应的URL下的任务
     * @param int $uid 用户id
     * @param int $rid 指定id。为zj_mission_hall_log表中的RecordID
     * @return array
     * @author demo 
     */
    public function finishTask($uid, $rid=0){
        $action = strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
        $where = "(JumpUrl='{$action}' OR ApplicateUrl='{$action}') AND UserID={$uid} AND l.Status=1";
        //在$rid不为空时，终止RecordID=$rid的任务
        if(!empty($rid)){
            $where .= ' AND RecordID='.$rid;
        }else{
            $where .= ' AND RecordID=0';
        }
        $log = $this->getModel('MissionHallLog');
        $data = $log->getList($where, 't.Title, l.MHLID, r.MHRID, r.MHTID, JumpUrl, ApplicateUrl, BeginTime, EndTime');

        $data[0]['JumpUrl'] = preg_replace('/\s+/m', '', $data[0]['JumpUrl']);
        $data[0]['ApplicateUrl'] = preg_replace('/\s+/m', '', $data[0]['ApplicateUrl']);
        $currentTime = time();
        $repeatTask = cookie('repeatTask'); //在未刷新页面时，将重复添加数据
        $isRepeatTask = empty($repeatTask);
        $isEmpty = empty($data[0]['MHLID']);
        //数据为空或者过期任务不在做统计
        if($isEmpty && $isRepeatTask || !$isEmpty && $data[0]['EndTime'] < $currentTime){
            return true;
        }
        //当日志为空，同时cookie不为空时，进行添加操作
        if($isEmpty && !$isRepeatTask){
            //防止cookie伪造
            if($repeatTask != session('repeatTask')){
                return true;
            }
            $result = $this->unionSelect('findModulesByRecordId',$repeatTask);
            //是否是对应的action，同时任务没有过期
            if(strtolower($result['JumpUrl']) == $action && $result['EndTime'] > $currentTime){
                return ($log->add($repeatTask, 4) && $this->finishedTaskAttachPoints($data[0]['MHTID'], $uid, $data[0]['Title']));
            }
            return true;
        }
        $jumpUrl = strtolower($data[0]['JumpUrl']);
        $applicateUrl = strtolower($data[0]['ApplicateUrl']);
        if($jumpUrl == $action && empty($applicateUrl) || $applicateUrl == $action){
            return ($log->finish($data[0]['MHLID']) && $this->finishedTaskAttachPoints($data[0]['MHTID'], $uid, $data[0]['Title']));
        }
        return true;
    }

    /**
     * 为完成的任务添加相应的分值
     * @param int $tid 任务的主键id
     * @param int $uid 用户id
     * @param string $description 描述
     * @param boolean $isSub 确定本次操作是否做减法运算，默认不是:false
     * @return boolean
     * @author demo 
     */
    public function finishedTaskAttachPoints($tid, $uid, $description, $isSub=false){
        $type = $this->getModel('MissionHallTasks')->getRewardType($tid);
        if(!empty($type)){
            if($isSub){
                $type[1] = -1 * $type[1];
            }
            if(2 == $type[0]){
                $data = array();
                $data['PayName'] = '任务大厅任务';
                $data['PayMoney'] = $type[1];
                $data['PayDesc'] = $description;
                $data['UserID'] = $uid;
                $data['AddTime'] = time();
                $this->getModel('Pay')->addPayLog($data);
            }
            return $this->getModel('User')->addNumByType($uid, $type[0], $type[1]);
        }
        return false;
    }
}