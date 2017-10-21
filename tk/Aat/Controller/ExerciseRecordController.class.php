<?php

/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-4-21
 * Time: 上午11:04
 */
namespace Aat\Controller;
class ExerciseRecordController extends BaseController
{
    protected $userTestRecordModel;

    public function _initialize() {
//        $this->userTestRecordModel = $this->getModel('UserTestRecord');
    }

    /**
     * 返回测试记录ajax数据
     * @author demo
     */
    public function returnRecordInfo() {
        $username = $this->getUserName();
        $subjectID = $this->getSubjectID();
        $IData = $this->getApiAat('TestRecord/testRecordList',$username,$subjectID);
        if($IData[0]==1){
            // $this->ajaxReturn(['list'=>$IData[1]['list']],'success',1);
            $this->setBack($IData[1]);
        }else{
             // $this->ajaxReturn(null,$IData[1],0);
            $this->setError($IData[1],1);
        }
    }
}