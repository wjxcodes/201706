<?php

/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-4-11
 * Time: 下午3:53
 */
namespace Aat\Controller;
class ExerciseAnswerController extends BaseController
{

    public function _initialize() {
    }

    /**
     * 显示练习作答信息的页面
     * @author demo
     */
    public function index() {
        $id = $_GET['id'];
        $check = $this->getApiAat('Base/checkExerciseRID',$id,$ifDone = true,$this->getUserName());
        if ($check === false) {
            $this->setMsg('您所操作的测试不存在！');
            $this->redirect('Default/index');
        }
        $this->assign([
            'pageName'=>'试题答案',
            'record_id'=>$id
        ]);
        $this->display();
    }

    /**
     * 返回试题信息和作答信息
     * @author demo
     */
    public function returnTestList() {
        $this->checkRequest();
        $IData = $this->getApiAat('Answer/exerciseAnswer',$_POST['id'],$this->getUserName());
        if($IData[0]==1 ){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }
        // $this->ajaxReturn($IData);
    }

    /**
     * 试题打分
     * @author demo 16-4-28
     */
    public function evaluateTest(){
        $this->checkRequest();
        $record = (int)$_POST['record'];
        $answer = (int)$_POST['answer'];
        $score = $_POST['score'];
        if(!is_numeric($score)) $score=0;
        $userName = $this->getUserName();
        $data = $this->getApiAat('Answer/evaluateTest', $answer, $record, $score, $userName);
        if($data[0] ==1){
            $this->setBack($data[1]);
        }else{
            $this->setError($data[1],1);
        }
        // $this->ajaxReturn($data);
    }
}