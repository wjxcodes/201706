<?php
/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-6-10
 * Time: 下午4:34
 */
namespace Aat\Controller;
class MyHomeworkController extends BaseController
{
    public function _initialize() {
    }

    /**
     * 描述：首页
     * @author demo
     */
    public function index() {
        $this->assign('pageName','我的作业');
        $this->display();
    }

    /**
     * 获取动态
     * @author demo
     */
    public function getNew(){
        $this->checkRequest();
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Class/classNews', $userID,$pageSize=20);
        if($IData[0]==0){
            $this->setError($IData[1], 1);
        }
        $list = [];
        //格式化时间和学科名和用户名
        foreach ($IData[1] as $i => $iListDb) {
            $list[$i]['time'] = date('Y年m月d日 H时i分', $iListDb['LoadTime']);
            $list[$i]['content'] = $iListDb['Content'];
        }
        $this->setBack($list);
    }

    /**
     * 获取未做试题列表
     * @author demo
     */
    public function getUndo() {
        $this->checkRequest();
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Class/undoHomework', $userID,$pageSize=10,$isApp=false);
        if($IData[0]==0){
            $this->setError($IData[1], 1);
        }else{
            $this->setBack(['show' => $IData[1]['show'], 'list' => $IData[1]['list']]);
        }
    }

    /**
     * 获取试题记录列表
     * @author demo
     */
    public function getRecord() {
        $this->checkRequest();
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Class/doneHomework', $userID,10,false);
        if($IData[0]==0){
            $this->setError($IData[1], 1);
        }else{
            $this->setBack(['show' => $IData[1]['show'], 'list' => $IData[1]['list']]);
        }
    }

    /**
     * 获取某用户所有所在的班级
     * @author demo
     */
    public function getClasses() {
        $this->checkRequest();
        $IData = $this->getApiAat('Class/userClasses', $this->getUserID());
        if($IData[0] == 1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1], 1);
        }
    }

    /**
     * 获取某班级的信息
     * @author demo
     */
    public function getClassInfo() {
        $this->checkRequest();
        $classID = $_REQUEST['class_id'];
        $IData = $this->getApiAat('Class/classInfoByID', $classID,$isApp=false);
        if($IData[0] == 1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1], 1);
        }
    }

    /**
     * 获取某用户某班级的作业列表
     * @author demo
     */
    public function getClassWork() {
        $this->checkRequest();
        $classID = $_REQUEST['class_id'];
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Class/classHomework', $classID,$userID,$isApp=false);
        if($IData[0]==0){
            $this->setError($IData[1], 1);
        }else{
            $this->setBack(['show' => $IData[1]['show'], 'list' => $IData[1]['list']]);
        }
    }

    /**
     * 申请加入班级
     * @author demo
     */
    public function newClass() {
        $this->checkRequest();
        $searchKey = $_REQUEST['class_num'];//用户输入的信息（班级编号或者手机号）
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Class/searchClassList', $searchKey,$userID,$isApp=false);
        if($IData[0] == 1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1], 1);
        }
    }

    /**
     * 加入班级
     * @author demo
     */
    public function introClass(){
        $classID=$_POST['cid'];
        $userID =$this->getUserID();
        $IData = $this->getApiAat('Class/joinClass', $classID,$userID);
        if($IData[0] == 1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1], 1);
        }
    }

}