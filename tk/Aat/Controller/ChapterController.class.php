<?php
/**
 * @author demo        
 * @date 2014年10月10日
 */
/**
 * 用户章节控制器
 */
namespace Aat\Controller;
class ChapterController extends BaseController
{
    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
    }

    /**
     * 显示用户修改
     * @author demo
     */
    public function index(){
        $this->assign('pageName','我的教材');
        $this->display();
    }

    /**
     * 检测用户某学科是否选择章节
     * @author demo
     */
    public function check() {
        $buffer=$this->getApiAat('Chapter/check',$this->getVersion());
        if($buffer[0]==1){
            $this->setBack($buffer[1]);
        }
        $this->setError($buffer[1],1);
    }

    /**
     * 根据学科获取教材版本
     * @author demo
     */
    public function getType() {
        $this->checkRequest();
        $IData=$this->getApiAat('Chapter/types',$this->getSubjectID());
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }
        // $this->ajaxReturn($this->getApiAat('Chapter/types',$this->getSubjectID()));
    }

    /**
     * 根据教材版本获取教材书本信息
     * @author demo   
     */
    public function getBook() {
        $this->checkRequest();
        $chapterID = $_REQUEST['id'];//ChapterID
        $username = $this->getUserName();
        $subjectID = $this->getSubjectID();
        $IData = $this->getApiAat('Chapter/book',$chapterID,$username,$subjectID);
        // $this->ajaxReturn($IData);
        if($IData[0] == 1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }
        
    }

    /**
     * 更新用户不同学科下的教材版本信息
     * @author demo
     */
    public function update() {
        $this->checkRequest();
        $chapterIDString = $_REQUEST['chapterIDString'];//逗号分隔的章节ID
        $username = $this->getUserName();
        $subjectID = $this->getSubjectID();
        $IData = $this->getApiAat('Chapter/update',$chapterIDString,$username,$subjectID);
      
        if($IData[0] == 1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }
        // $this->ajaxReturn($IData);
    }

}