<?php
/**
 * @author demo
 * @date 2015年10月20日
 */
namespace Task\Model;
use Common\Model\BaseModel;
class MissionHallTasksModel extends BaseModel{
    /**
     * 构造函数（主要设置了此模型modelName）
     * @author demo
     */
    function __construct() {
        parent::__construct();
        $this->modelName = 'MissionHallTasks';
    }
    
    /**
     * 删除
     * @param array $id 
     * @return array | bool
     * @author demo
     */
    public function delete($id){
        $where = 'MHTID in ('.$id.')';//删除操作条件
        /* 删除记录 */
        $recordModel = $this->getModel('MissionHallRecords');//记录model
        $delRecords = $recordModel->delete($where);//删除任务记录
        if($delRecords === false ) return false;
        
        /* 删除学科属性 */
        $mhaModel = $this->getModel('MissionHallAttr');//属性学科model
        $delMha = $mhaModel->delete($where);//删除学科属性
        if($delMha === false ){
                $recordModel->insertData($delRecords,$recordModel->modelName);
                return false;
        }        
        /* 删除章节 */
        $mhcModel = $this->getModel('MissionHallChapter');//属性学科model
        $delMhc = $mhcModel->delete($where);//删除章节属性
        if($delMha === false ){
            $recordModel->insertData($delRecords,$recordModel->modelName);
            $mhaModel->insertData($delRecords,$mhaModel->modelName);
            return false;
        }
        
        /* 删除任务 */
        $rs = $this->deleteData($where, $this->modelName);
        if($rs !== false){//删除任务成功
            return $rs;
        }else{//删除任务失败，重新添加任务记录 属性学科 章节
            $recordModel->insertData($delRecords,$recordModel->modelName);
            $mhaModel->insertData($delMha,$mhaModel->modelName);
            $mhcModel->insertData($delMhc,$mhcModel->modelName);
            return false;
        }
    }
    /**
     * 返回数据，支持分页查询，当params给定page时
     * 默认排序规则置顶，最新HOT DESC,MHTID DESC
     * @author demo
     */
    public function getList($field='mht.*,u.AdminName,mha.SubjectID',$where,$order='',$page=''){
        $order = empty($order) ? 'mht.HOT DESC,mht.Level ASC,mht.MHTID DESC' : 'HOT DESC,mht.Level ASC,'.$order ;
        return $this->unionSelect('getMissionHallTasksByPage',$field,$where,$order,$page);
    }
    
    /**
     * 保存数据
     * 提供主键id则为修改操作
     * @param array $data 
     * @param int $id
     * @return int | bool
     * @author demo
     */
    public function save($data,$id=''){
        if($data['ChapterID']){//多个版本 数组
            $chapter = $data['ChapterID'];
        }
        unset($data['ChapterID']);
        if($data['SubjectID']){//学科
            $subject['SubjectID'] = $data['SubjectID'];
        }
        unset($data['SubjectID']);
        $mhaModel = $this->getModel('MissionHallChapter');//章节版本model
        if(empty($id)){
            $id = $this->insertData($data,$this->modelName);
            if($id===false) return false;
            if($chapter){
                foreach($chapter as $i=>$cp){
                    $chapterDate[$i]['ChapterID'] = $cp;
                    $chapterDate[$i]['MHTID'] = $id;
                }
                $this->getModel('MissionHallChapter')->save($chapterDate);
            }
            if($subject){
                $subject['MHTID'] = $id;
                $this->getModel('MissionHallAttr')->save($subject);
            }
            return $id; 
        }else {
            $rs = $this->updateData($data,'MHTID ='.$id,$this->modelName);
            if($rs===false) return false;
            if($chapter){//修改章节版本
                foreach($chapter as $i=>$cp){
                    $chapterData[$i]['ChapterID'] = $cp;
                    $chapterData[$i]['MHTID'] = $id;
                }
                $mhaModel->save($chapterData,$id);
            }else {//删除章节版本
                $mhaModel->delete('MHTID = '.$id);
            }
            if($subject){//修改学科
                $this->getModel('MissionHallAttr')->save($subject,$id);
            }else {//删除学科
                $this->getModel('MissionHallAttr')->delete('MHTID = '.$id);
            }
            return $rs;
        }
    }
        
    /**
     * 任务类型
     * @author demo
     * @return array
     */
    public function taskTypes(){
        return array(1=>'内容创作',2=>'资源标引',3=>'热门活动');
    }

    /**
     * 返回等级数据
     * @return array
     * @author demo 
     */
    public function getLevel(){
        return array(
            1 => '初级',
            2 => '一级',
            3 => '二级',
            4 => '特级'
        );
    }

    /**
     * 方便前台显示 奖励类型 奖励数量
     * @param int $type
     * @param float $number
     * @return array
     * @author demo
     */
    public function showReward($type,$number){
        $reward = array('Img'=>'','Reward'=>'','RewardType'=>'');
        switch ($type){
            case 1:
                $reward['Img'] = __PUBLIC__.'/index/imgs/task/task-icon-integral.jpg';
                $reward['Reward'] = (int)$number;
                $reward['RewardType'] = '积分';
                break;
            case 2:
                $reward['Img'] = __PUBLIC__.'/index/imgs/task/task-icon-cash.jpg';
                $reward['Reward'] = '￥'.$number;
                $reward['RewardType'] = '现金';
                break;
            case 3:
                $reward['Img'] = __PUBLIC__.'/index/imgs/task/task-icon-wlb.jpg';
                $reward['Reward'] = (int)$number;
                $reward['RewardType'] = '金币';
                break;
            default:
                $reward['Img'] = __PUBLIC__.'/index/imgs/task/task-icon-integral.jpg';
                $reward['Reward'] = (int)$number;
                $reward['RewardType'] = '积分';
                break;
        }
        return $reward;
    }
    
    /**
     * 返回指定任务的级别
     * @param int $tid 任务id
     * @return int
     * @author demo 
     */
    public function getLevelByTid($tid){
        $data = $this->findData('Level', 'MHTID='.$tid);
        return (int)$data['Level'];
    }
    
    /**
     * 修改数据 更新自加数据
     * @param string $val
     * @param string $wehre
     * @return int | bool
     * @author demo
     */
    public function updateColumn($val,$wehre){
        return $this->conAddData($val,$wehre,$this->modelName);
    }
    

    /**
     * 返回任务的奖励类型
     * @param int $id 任务id
     * @return array 当返回不是一个空数组时，该数组下标0为类型，1为奖励数量
     * @author demo 
     */
    public function getRewardType($id){
        $data = $this->findData('RewardType, Reward', 'MHTID='.$id);
        $type = (int)$data['RewardType'];
        if(1 == $type){ //积分
            return array(1,$data['Reward']);
        }
        if(3 == $type){//兼容外网数据，为金币
            return array(2,$data['Reward']);;
        }
        return array();
    }
}