<?php
/**
 * @author demo 
 * @date 2014年11月20日
 */
/**
 * 学生作业生成能力值类
 * 可以得到@todo
 */
namespace Aat\Model;

use Common\Model\BaseModel;
class UserWorkForecastModel extends BaseModel{
    protected $conditionN = 4;//间隔N轮有效测试（又能判断对错的作答）后的下一次可以生成能力值和预测分（计算还剩几次测试可以生成新预测分也需要此变量）默认4
    protected $conditionS = 10;//知识点下大于s道测试题时该知识点可以生成能力值 默认10

    /**
     * 判断是否满足生成能力值的条件
     * @param int $userID 用户ID
     * @param int $subjectID 学科ID
     * @return bool true 可以 false 不可以
     * @author demo
     */
    public function ifAbility($userID, $subjectID) {
        //从上次产生预测分到本次测试是否够N次测试
        $sendID = $this->getLastAbility($userID, $subjectID)['SendID'];
        $sendID = $sendID?$sendID:0;
        $count = $this->selectCount(
            ['UserID' => $userID, 'SubjectID' => $subjectID, 'SendID' => ['gt', $sendID]],
            'WorkForecastID');
        return $count >= $this->conditionN ? true : false;
    }

    /**
     * 判断是否满足生成知识点能力值的条件
     * @param int $allAmount 知识点下数量
     * @return bool true 可以 false 不可以
     * @author demo
     */
    public function ifKlAbility($allAmount){
        return $allAmount >= $this->conditionS ? true : false;
    }

    /**
     * 获取用户最后一次（最新）能力值的记录
     * @param int $userID 用户ID
     * @param int $subjectID 学科ID
     * @return array|null find()形式，没有能力值返回null
     * @author demo
     */
    public function getLastAbility($userID, $subjectID) {
        $db = $this->selectData(
            'SendID',
            ['SubjectID' => $subjectID, 'UserID' => $userID, 'Ability' => array('neq', null)],
            'AddTime desc',
            1
        );

        $sendID = $db[0] ? $db[0] : null;
        return $sendID;
    }

    /**
     * 获取指定用户sendID后的记录ID
     * @param int $userID 用户ID
     * @param int $subjectID 学科ID
     * @param int $sendID sendID
     * @return string 逗号分隔的sendIDs
     * @author demo
     */
    public function getSendIDs($userID,$subjectID,$sendID){
        $db =$this->getModel('UserSendWork')->selectData(
            'SendID',
            ['UserID'=>$userID,'SubjectID'=>$subjectID,'SendID'=>['gt',$sendID]]);
        $testRecordIDs = '';
        if($db){
            foreach($db as $iDb){
                $array[] = $iDb['SendID'];
            }
            $testRecordIDs = implode(',',$array);
        }
        return $testRecordIDs;
    }

    /**
     * 根据UserID SubjectID查找最近上一次的记录
     * @param int $userID 用户ID
     * @param int $subjectID 学科ID
     * @return array|null 最后一次的记录
     * @author demo
     */
    public function getLastRecord($userID, $subjectID) {
        $db = $this->selectData(
            'SubjectID,ClassID,UserID,SendID,AllAmount,RightAmount,WrongAmount,UndoAmount,NotAmount',
            array('UserID' => $userID, 'SubjectID' => $subjectID),
            'AddTime DESC',
            1);

        $result = $db[0] ? $db[0] : null;
        return $result;
    }

}