<?php

/**
 * Created by PhpStorm.
 * User: demo 
 * Date: 14-3-25
 * Time: 下午3:20
 */
namespace Aat\Controller;
class ExerciseReportController extends BaseController
{

    public function _initialize() {
    }

    /**
     * 显示测试报告页面
     * @author demo
     */
    public function index() {
        $id = $_GET['id'];
        if ($this->checkTestRecordID($id,$ifDone=true,$recordType='exercise') === false) {
            $this->setMsg('您所查看的测试报告不存在！');
            $this->redirect('Default/index');
        }

        //判断是否打分
        // if($this->getModel('UserTestRecordAttr')->isEvalTest($id) &&
        // !A('Exercise/Answer', 'Logic')->isCompleteEvaluation($id)){
        //     header('Location:'.U('Aat/ExerciseAnswer/index',array('id'=>$id,'AatTestStyle'=>1)));
        //     exit();
        // }
        $this->assign([
            'pageName'=>'测试报告',
            'record_id'=>$id
        ]);
        $this->display();
    }

    /**
     * 返回本次测试作答概况json
     * @author demo
     */
    public function returnExerciseInfo() {
        $this->checkRequest();
        $recordID = $_REQUEST['id'];
        $username = $this->getUserName();
        $IData = $this->getApiAat('Score/exerciseInfo',$recordID,$username,$isApp=false);
        // $this->ajaxReturn($IData);
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
             $this->setError($IData[1],1);
        }
    }

    /**
     * 返回最近10次测试的分数概况用以生成折线图
     * @author demo
     */
    public function returnScores() {
        $username = $this->getUserName();
        $subjectID = $this->getSubjectID();

        $IData = $this->getApiAat('Score/scoreList',$username,$subjectID,$isApp=false);
        if($IData[0] == 1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }
        // $this->ajaxReturn($IData);
    }

    /**
     * 返回本次测试对应的每个知识点的试题数量和正确率的树状结构用以生成树
     * @author demo
     */
    public function returnKlInfo() {
        $this->checkRequest();
        $recordID = $_REQUEST['id'];
        $username = $this->getUserName();
        $IData = $this->getApiAat('KlAbility/userKlInfo',$recordID,$username);
        // $this->ajaxReturn($IData);
        if($IData[0] == 1){
            $this->setBack($IData[1]);
        }else{
            $this->seterror($IData[1],1);
        }
    }

}