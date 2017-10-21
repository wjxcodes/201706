<?php
/**
 * @createDate 2014年10月22日
 * @editDate 2015年1月23日
 * @author demo
 */
/**
 * 试题推送类
 */
namespace Aat\Controller;
class PushTestController extends BaseController
{
    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
        $this->checkRequest();
    }

    /**
     * 00检查学科、系统版本、教材选择
     * 学科Cookie必须存在、如果选择同步学习版，教材必选，高考冲刺版可以不选择教材
     * @author demo
     */
    public function checkCondition() {
        $username = $this->getUserName();
        $subjectID = $this->getSubjectID();
        $IData = $this->getApiAat('PushTest/checkCondition', $username,$subjectID,$isApp=false);
        if($IData[0] == 1){
             $this->setBack($IData[1]);
        }
         $this->setError($IData[1], 1);
    }

    /**
     * 01智能测试
     * @author demo
     */
    public function intelligence(){

    }

    /**
     * 02专题模块训练
     * @author demo
     */
    public function special(){
        $version = $this->getVersion();
        if($version == 1){
            $this->knowledge();
        }elseif($version == 2){
            $this->chapter();
        }
    }

    /**
     * 03整卷练习
     * @author demo
     */
    public function testPaper(){
        $display = $this->fetch();
        return $this->setBack($display);
    }

    /**
     * 04目标训练
     * @author demo
     */
    public function advanced(){

    }

    /**
     * 02根据用户所选择教材获取章节列表
     * @author demo
     */
    public function getChapterList(){
        $username = $this->getUserName();
        $subjectID = $this->getSubjectID();
        $chapterList = $this->getModel('UserChapter')->getUserChapterList($username,$subjectID);
        // $chapterList =  $this->getApiUser('User/getUserChapterList',$username,$subjectID);
        if(!$chapterList){
            return $this->setError('50201', 1); //同步学习版需选择教材，请进入个人中心来选择教材！
        }
        return $this->setBack($chapterList);
    }

    /**
     * 03获取整卷练习试卷类型和自定义初始化的知识点数据
     * @author demo
     */
    public function getTestPaperInit() {
        $subjectID = $this->getSubjectID();
        $username = $this->getUserName();
        $docTypeData = $this->getApiAat('PushTest/canExerciseDocType');
        $klData = $this->getModel('UserTestKl')->getKlInfo($username, $subjectID);//知识点树形结构
        if (!$docTypeData||!$klData) {
            return $this->setError('50202', 1); //获取试卷类型和知识点数据失败，请重试！
        }
        $result = ['docType'=>$docTypeData,'knowledge'=>$klData];
        return $this->setBack($result);
    }

    /**
     * 02知识点选题界面【string】
     * @author demo
     */
    private function knowledge(){
        $display = $this->fetch('knowledge');
        return $this->setBack($display);
    }

    /**
     * 02章节选题界面
     * @author demo
     */
    private function chapter(){
        $display = $this->fetch('chapter');
        return $this->setBack($display);
    }

}