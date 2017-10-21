<?php
/**
 * @author demo
 * @date 2015年12月26日
 */

namespace Aat\Api;

class TestRecordApi extends BaseApi
{
    /**
     * 描述：check练习ID
     * @param $id
     * @param bool $ifDone
     * @param $username
     * @return bool
     * @author demo
     */
    public function checkValidExerciseID($id, $ifDone = false, $username) {
        $testRecordDb = $this->getModel('UserTestRecord')->findData(
            'UserName,Score,SubjectID',
            ['TestID' => $id]
        );
        if (!$testRecordDb || $testRecordDb['UserName'] != $username) {
            return false;
        }
        if (($ifDone == true && $testRecordDb['Score'] == -1) ||
            ($ifDone == false && $testRecordDb['Score'] != -1)
        ) {
            //如果要求是已经完成但是分数是-1 或者 没有完成但是分数不是-1的 都返回false
            return false;
        }
        return $testRecordDb;
    }

    /**
     * 描述：check 作业ID
     * @param $id
     * @param bool $ifDone
     * @param $userID
     * @return bool
     * @author demo
     */
    public function checkValidSendID($id, $ifDone = false, $userID) {
        //Status字段 0未做 1已提交 2已完成
//        $workRecordDb = $this->getModel('UserSendWork')->findData(
//            'UserID,Status',
//            ['SendID' => $id]
//        );
        $workRecordDb = $this->getApiWork('Work/findData', 'UserID,Status', ['SendID' => $id], '', 'UserSendWork');
        if (!$workRecordDb || $workRecordDb['UserID'] != $userID) {
            return false;
        }
        if (($ifDone == true && $workRecordDb['Status'] == 0) || ($ifDone == false && $workRecordDb['Status'] != 0)) {
            //如果要求是已经完成但是状态是0 或者 没有完成但是状态不是0的 都返回false
            return false;
        }
        return true;
    }

    /**
     * 描述：测试记录
     * @param $username
     * @param $subjectID
     * @return array
     * @author demo
     */
    public function testRecordList($username,$subjectID){
        $userTestRecordModel = $this->getModel('UserTestRecord');
        $where = ['zj_user_test_record.UserName' => $username,
            'zj_user_test_record.SubjectID' => $subjectID];
        // 查询满足要求的总记录数
        $count = $userTestRecordModel->selectCount(
            $where,
            'TestID'
        );
        $page = handlePage('init',$count,10,['type'=>1]);
        $show = $page->show(false); // 分页显示输出
        // 进行分页数据查询
        $pageStr=$page->firstRow . ',' . $page->listRows;
        if($_REQUEST['p']>$page->totalPages){
            return [0,'已经加载完毕！'];
        }
        $list = $userTestRecordModel->unionSelect('userTestRecordPageBySubjectUserName',$username,$subjectID,$pageStr);
        if($list){
            $configStyle = C('WLN_TEST_STYLE_NAME');
            $tempTime = [];//辅助数组
            foreach($list as $i=>$k){
                $tempTime[$i]['UnixTime'] = $k['LoadTime'];
                $list[$i]['Style'] = $configStyle[$list[$i]['Style']];
                $list[$i]['RightAmount'] = (int)((substr_count($k['Content'],',')+1)*$k['Score']*0.01);
                $list[$i]['AllAmount'] = substr_count($k['Content'],',')+1;
                if(date('Y-m-d',$tempTime[$i]['UnixTime'])!==date('Y-m-d',$tempTime[$i-1]['UnixTime'])){
                    $list[$i]['LoadDate'] = date('Y-m-d',$tempTime[$i]['UnixTime']);
                }
                $list[$i]['LoadTime'] = date('Y-m-d H:i',$k['LoadTime']);
                $list[$i]['RealTime'] = (int)($k['RealTime']/60)+1;
            }
            return [1,['list'=>$list,'show'=>$show]];
        }else{
            return [0,'暂无更多测试记录，请进行测试！'];
        }
    }


}