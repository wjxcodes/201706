<?php
/**
 * @createDate 2014年10月22日
 * @editDate 2015年1月23日
 * @author demo
 */
/**
 * 试题推送类
 */
namespace AatApi\Controller;
class PushTestController extends BaseController
{
    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
    }

    /**
     * 00检查学科、系统版本、教材选择
     * 学科Cookie必须存在、如果选择同步学习版，教材必选，高考冲刺版可以不选择教材
     * @return array
     *        正确
     *   [
     *       "data"=>[
     *           "record_id"=> 157439, 
     *           "phone"=> 1
     *      ], 
     *      "info"=>"success", 
     *       "status"=>1
     *   ]
     *   错误返回类似下面格式的错误信息
     *   [
     *       "data"=>null, 
     *       "info"=>"请先选择学科", 
     *       "status"=>0
     *   ]
     *   [
     *       "data"=>null, 
     *       "info"=>"请先进入个人中心选择素材", 
     *       "status"=>0
     *   ]
     * @author  demo
     */
    public function checkCondition() {
        $username = $this->getUserName();
        $subjectID = $this->getSubjectID();
        $IData = $this->getApiAat('PushTest/checkCondition', $username,$subjectID,$isApp=false);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

    /**
     * 01智能测试
     * @author demo
     */
    public function intelligence(){

    }

    /**
     * 04目标训练
     * @author demo
     */
    public function advanced(){

    }

    /**
     * 02根据用户所选择教材获取章节列表
     * @param int subjectID 本地学科ID true
     * @return array
     *    [
     *   "data"=> [
     *       [
     *           "chapterID"=> "141", 
     *           "chapterName"=> "必修3", 
     *           "sub"=> [//教材章节下级数据同上级定义
     *               [
     *                   "chapterID"=> "142", 
     *                   "chapterName"=> "第一章 算法初步", 
     *                   "sub"=> [
     *                       [
     *                           "chapterID"=> "143", 
     *                           "chapterName"=> "1.1 算法与程序框图"
     *                       ]...
     *                   ]
     *               ], 
     *               ...
     *       ]
     *       ....
     *   ], 
     *   "info"=> "success", 
     *   "status"=> 1
     * ]
     * @author demo
     */
    public function getChapterList(){
        $username = $this->getUserName();
        $subjectID = $this->getSubjectID();
        $chapterList = $this->getModel('UserChapter')->getUserChapterList($username,$subjectID);
        if(!$chapterList){
            return $this->setError('51006', 1); //同步学习版需选择教材，请进入个人中心来选择教材！
        }
        $this->setBack($chapterList);
    }

    /**
     * 03 套卷试卷类型（手机端使用）
     * @param int subjectID 本地学科ID true
     * @return array
     *    [
     *   "data"=> [
     *       [
     *           "typeID"=> "1", 
     *           "typeName"=> "历年真题", 
     *       ], 
     *       [
     *           "typeID"=> "2", 
     *           "typeName"=> "模拟联考", 
     *       ]
     *   ], 
     *   "info"=> "success", 
     *   "status"=> 1
     * ]
     * @author demo
     */
    public function getTestPaperStyle(){
        $testPaperStyle = $this->getApiAat('PushTest/canExerciseDocType');
        if(!$testPaperStyle){
            return $this->setError('51007', 1); //获取套卷试卷类型失败，请重试！
        }
        $this->setBack($testPaperStyle);
    }
}