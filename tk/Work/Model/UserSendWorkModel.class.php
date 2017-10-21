<?php
/**
 * @author demo
 * @date 2014年11月6日
 */
/**
 * 用户完成作业记录类
 * 可以得到作业测试记录的信息。例如答题情况，时间，老师评论等
 */
namespace Work\Model;
class UserSendWorkModel extends BaseModel{

    /**
     * 获取班级平均数
     * @param $subjectID 学科ID
     * @param $classID 班级ID
     * @return mixd
     * @author demo
     */
    public function classAvgScore($subjectID,$classID){
        $db = $this->groupData(
            'SubjectID,ClassID,AVG(CorrectRate) AS CorrectRate,WorkID,SendTime',
            [
                'SubjectID'=>$subjectID,
                'ClassID'=>$classID,
                'Status'=>['gt',0]
            ],
            'WorkID',
            'SendTime DESC',
            10
        );
        return $db?$db:null;
    }
}