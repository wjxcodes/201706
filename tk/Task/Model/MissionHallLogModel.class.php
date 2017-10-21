<?php
/**
 * 任务大厅日志类
 * @author demo 2015-11-10 
 */
namespace Task\Model;
use Common\Model\BaseModel;
class MissionHallLogModel extends BaseModel{

    /**
     * 返回指定用户的任务日志
     * @author demo 
     */
    public function getList($where, $field){
        return $this->unionSelect('getMissionHallLogList',$field, $where);
        // return $this->dbConn->table('zj_mission_hall_log l')
        //                 ->field($field)
        //                 ->join('zj_mission_hall_records r ON l.MHRID=r.MHRID')
        //                 ->join('zj_mission_hall_modules m ON m.MHTID=r.MHTID')
        //                 ->join('zj_mission_hall_tasks t ON t.MHTID=m.MHTID')
        //                 ->where($where)
        //                 ->select();
    }

    /**
     * 审核任务申请
     * @param int $status 状态
     * @param string $ids records表中的主键，多个，分割
     * @return int | bool·
     * @author demo 
     */
    public function check($status, $ids){
        $status = (int)$status;
        $where = "MHLID IN({$ids})";
        $result = $this->updateData(array(
            'Status' => $status
        ), $where);  
        return $result !== false;
    }

    /**
     * 查询日志信息
     * @param array $params 可能值，MHTID,AddTime,当前页码
     * @author demo 
     */
    public function recordList($params){
        $where = '1=1';
        if(!$params['p']){
            $params['p'] = 1;
        }
        if($params['id']){
            $where .= " AND r.MHTID={$params['id']}";
        }
        $params['startTime'] = strtotime($params['startTime']);
        if($params['startTime']){
            $where .= ' AND l.AddTime >= '.$params['startTime'];
        }
        $params['endTime'] = strtotime($params['endTime']);
        if($params['endTime']){
            $where .= ' AND l.AddTime <= '.$params['endTime'];
        }
        if($params['username']){
            $where .= ' AND u.UserName = "'.$params['username'].'"';
        }
        if($params['SubjectID']){
            $where .= ' AND a.SubjectID IN('.$params['SubjectID'].')';
        }
        if($params['level']){
            $where .= ' AND t.Level='.$params['level'];
        }
        if($params['uid']){
            $where .= ' AND r.UserID='.$params['uid'];
        }
        if(!is_null($params['status']) && $params['status'] >= 0){
            $where .= ' AND l.Status='.$params['status'];
        }
        $order = 'l.AddTime DESC,u.UserID';
        // $field = 'count(l.MHLID) as c';
        $result = $this->unionSelect('countMissionHallLogRecordList',$where);
        // $result = $this->dbConn->table('zj_mission_hall_log l')
        //                 ->field($field)
        //                 ->join('zj_mission_hall_records r ON l.MHRID=r.MHRID')
        //                 ->join('zj_mission_hall_attr a ON a.MHTID=r.MHTID')
        //                 ->join('zj_mission_hall_tasks t ON t.MHTID=r.MHTID')
        //                 ->join('zj_user u ON u.UserID=r.UserID')
        //                 ->where($where)
        //                 ->find();
        $count = 0;
        if($result['c']){
            $count = $result['c'];
        }
        $perpage = 20;
        if($params['prepage']){
            $perpage = $params['prepage'];
        }
        $page = page($count,$params['p'],$perpage);
        $limit = (($page-1) * $perpage).','.$perpage;
        if($params['field']){
            $field = $params['field'];
        }else{
            $field = array(
                'l.*, t.Title, t.MHTID, u.UserName, r.AddTime as recordAddTime, r.MHRID, r.UserID, m.RealReward'
            ); 
        }
        $field = implode(',', $field);
        $result = $this->unionSelect('getMissionHallLogRecordList', $field, $where, $order, $limit);
        // $result = $this->dbConn->table('zj_mission_hall_log l')
        //                 ->field($field)
        //                 ->join('zj_mission_hall_records r ON l.MHRID=r.MHRID')
        //                 ->join('zj_mission_hall_attr a ON a.MHTID=r.MHTID')
        //                 ->join('zj_mission_hall_tasks t ON t.MHTID=r.MHTID')
        //                 ->join('zj_mission_hall_modules m ON m.MHTID=r.MHTID')
        //                 ->join('zj_user u ON u.UserID=r.UserID')
        //                 ->where($where)
        //                 ->order($order)
        //                 ->limit($limit)
        //                 ->select();
        return array($result, $count);
    }

    /**
     * 添加
     * @param int $id zj_mission_hall_records 主键
     * @param int $rid 给定的相关表主键，默认为0
     * @return boolean
     * @author demo 
     */
    public function add($id, $status, $rid=0){
        $data = array(
            'MHRID' => $id,
            'RecordID' => $rid,
            'Status' => $status,
            'AddTime' => time()
        );
        $result = $this->insertData($data);
        return $result !== false;
    }

    /**
     * 在指定的操作中加入相关表的主键
     * @param int $uid 用户id
     * @param int $pk 主键id
     * @author demo 
     */
    public function relatePrimaryKey($uid, $pk){
        $pk = (int)$pk;
        if(0 == $pk){
            return true;
        }
        $action = strtolower(MODULE_NAME.'-'.CONTROLLER_NAME.'-'.ACTION_NAME);
        $where = "JumpUrl='{$action}' AND l.Status=1 AND r.UserID={$uid}";
        $data = $this->getList($where, 'l.MHLID, l.RecordID, JumpUrl, ApplicateUrl');
        //理论上该判断会始终返回false
        if(empty($data)){
            return true;
        }
        $lastId = 0;
        foreach($data as $value){
            if($value['RecordID'] == $pk){ //此处是为容错
                $lastId = true; //此处标识为mark，确保下面不在执行
                continue;
            }else if(0 == $value['RecordID']){
                $lastId = $value['MHLID'];
            }
        }
        if($lastId === true){
            return $lastId;
        }
        //更新当前的记录
        $data = array(
            'RecordID' => $pk
        );
        return $this->updateData($data, 'MHLID='.$lastId);
    }

    /**
     * 指定id的任务完成
     * @param int $id 主键id
     * @return boolean
     * @author demo 
     */
    public function finish($id){
        $data = array(
            'Status' => 4
        );
        return $this->updateData($data, 'MHLID='.$id);
    }

    /**
     * 根据$uid和$tid，验证该$uid是否可以领取任务
     * @param int $uid 用户id
     * @param int $tid 任务id
     * @param int $preTaskNum 申请高级任务时 得完成上级任务
     * @return boolean|string
     * @author demo 
     */
    public function isCanReceive($uid, $tid ,$preTaskNum = 20){
        $mhtModel = $this->getModel('MissionHallTasks') ;
        //$preTaskNum = $this->getModel('MissionHallModules')->getInfo('MHTID ='.$tid)[0]['PromoteTime'];//获得任务升级次数（未启用）
        /* 获取当前任务等级及参与人数 只有一条记录取0下标 */
        $result = $mhtModel->getList('mht.MHTID,mht.Level,mht.ReceiveTimes','mht.MHTID ='.$tid)[0];
        $level = $result['Level'];
        if($level == 1 && $result['ReceiveTimes'] == 0){//初级任务 且 不限制人数
            return true;    
        }
        //$level = $mhtModel->getLevelByTid($tid);
        $condition = 1; //任务等级查询条件，任务为初级和一级时，使用查询level=1的数据
        
        // if(3 == $level){
        //     $condition = 2;
        // }
        if(2 == $level){ //仅验证一级任务的领取
            $condition = 1;
        }else{
            $condition = $level;
        }
        
        if($level == 1 || $level > 2){//最低等级为1 初级任务
            $userHaveTasksRecordsWhere = 'mhr.UserID ='.$uid;//查询当前用户
            $userHaveTasksRecordsWhere .= ' AND mhr.MHTID ='.$tid;//查询当前任务
            $userHaveTasksRecords = $this->getModel('MissionHallRecords')
                ->getList('count(l.MHLID) as c','zj_mission_hall_log l ON l.MHRID=mhr.MHRID',$userHaveTasksRecordsWhere);//查询当前用户参与当前任务总次数
            if($result['ReceiveTimes'] > 0 && $userHaveTasksRecords[0]['c'] >= $result['ReceiveTimes']){
                return '该任务领取次数已达上限';
            }
        }
        if($level == 2){//一级任务判断初级级任务完成情况
            /* 获取当前上级任务 */
            $current = time();
            $conditionWhere = 'mht.BeginTime < '.$current ;//已经开始的
            $conditionWhere .= ' AND mht.EndTime > '.$current;//还没结束的
            $conditionWhere .= ' AND mht.Level ='.$condition ;//还没结束的
            $tasks = $mhtModel->getList('mht.MHTID',$conditionWhere);
            if(empty($tasks)){
                return '该任务需要先完成初级任务';
            }
            $tasksCount = count($tasks) ;//上个等级任务总数
            $taskIDs = array();
            foreach ($tasks as $id){
                $taskIDs[] = $id['MHTID'];
            }
            unset($tasks);
            /* 用户完成 任务 任务总数 */
            $userHaveTasksRecordsWhere = 'mhr.UserID ='.$uid;//查询当前用户
            $userHaveTasksRecordsWhere .= ' AND l.Status = 4';//查询完成状态 不查询申请状态
            $userHaveTasksRecordsWhere .= ' AND mhr.MHTID in ('.implode(',',$taskIDs).')';//查询上个等级任务
            $userHaveTasksRecords = $this->getModel('MissionHallRecords')
                ->getList('mhr.MHTID,count(l.MHLID) as c','zj_mission_hall_log l ON l.MHRID=mhr.MHRID',$userHaveTasksRecordsWhere,'','','mhr.MHRID');
            
            /*判断用户完成上级总次数 和 任务次数*/
            $userHaveTasksCount = count($userHaveTasksRecords);//当前用户完成任务总数
            $userHaveTasksRecordsCount = 0;//计算当前用户完成任务总次数
            foreach ($userHaveTasksRecords as $uhtr){
                $userHaveTasksRecordsMHTIDs[] = $uhtr['MHTID'] ;
                $userHaveTasksRecordsCount += $uhtr['c'];
            }
            unset($userHaveTasksRecords);
            
            /* 用户领取任务为一级任务 判断用户上级任务完成情况 */
            if($userHaveTasksCount < $tasksCount ){
                //完成所有上级任务$taskIDs
                //$id = array_diff($taskIDs, $userHaveTasksRecordsMHTIDs);
                return '别急，继续体验新的初级任务吧';
            }
            if($userHaveTasksRecordsCount < $preTaskNum){
                //完成任务总数达到规定默认20次
                return '别急，继续体验初级任务吧';
            }
        }
        return true;
    }
}