<?php
/**
 * @author demo
 * @date 2015年12月29日
 */

namespace Aat\Api;

use Common\Api\CommonApi;
class BaseApi extends CommonApi
{
    /**
     * 检测testRecordID是否是用户可以测试的ID
     * id是否存在，是否是本人，是否是已经完成的
     * @param int $id testRecordID或者sendID 测试记录ID作业测试记录ID
     * @param bool $ifDone 是否是已经完成的记录（已经提交试卷的）默认不是
     * @param string $username
     * @return bool|array 如果检测通过返回true 否则返回false
     * @author demo 5.6.3
     */
    public function checkExerciseRID($id,$ifDone = false,$username){
        return $this->getApiAat('TestRecord/checkValidExerciseID', $id, $ifDone, $username);
//        return (new TestRecordLogic())->checkValidExerciseID($id, $ifDone, $username);
    }
    /**
     * 检测testRecordID是否是用户可以测试的ID
     * id是否存在，是否是本人，是否是已经完成的
     * @param int $id testRecordID或者sendID 测试记录ID作业测试记录ID
     * @param bool $ifDone 是否是已经完成的记录（已经提交试卷的）默认不是
     * @param int $userID
     * @return bool|array 如果检测通过返回true 否则返回false
     * @author demo 5.6.3
     */
    public function checkHomeworkRID($id,$ifDone=false,$userID){
        return $this->getApiAat('TestRecord/checkValidSendID', $id, $ifDone, $userID);
//        return (new TestRecordLogic())->checkValidSendID($id, $ifDone, $userID);
    }

}