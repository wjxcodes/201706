<?php
/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-4-22
 * Time: 上午9:14
 */
namespace AatApi\Controller;
class TestWrongController extends BaseController
{

    /**
     * 初始化方法
     * @author demo
     */ 
    public function _initialize() {
    }

    /**
     * 返回错误试题在知识点上的反应
     * [注意]不同次的测试，同一试题如果错误多次，会记录多次
     * @param int subjectID 学科ID true
     * @return array
     *    [
     *   "data"=> [
     *       [
     *           "klName"=> "语言知识基础", 
     *           "amount"=> "2", 
     *           "klID"=> "237", 
     *           "sub"=> [
     *               [
     *                   "klName"=> "语言知识运用", 
     *                   "amount"=> "2", 
     *                   "klID"=> "238"
     *               ]
     *           ]
     *       ],
     *       [
     *           "klName"=> "文学常识", 
     *           "amount"=> 0, 
     *           "klID"=> "325"
     *       ],
     *       ...
     *   ], 
     *   "info"=> "success", 
     *   "status"=> 1
     * ]
     * @author demo
     */
    public function returnKlInfo() {
        $username = $this->getUserName();
        $subjectID = $this->getSubjectID();
        $IData = $this->getApiAat('Test/wrongKlTree', $username,$subjectID);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

    /**
     * 返回错误试题
     * @param int id 知识点ID  true
     * @param int p 分页的页码 true
     * @param int startNo 从第几道题开始显示 true
     * @param int subjectID 当前学科ID  true
     * @return array
     *"data"=> [
     *   "test"=> [
     *       [
     *           "TestRecordID"=> 123, 
     *           "Test"=> 题文, 
     *           "AnswerText"=> 用户答案, 
     *           "Answer"=> 正确答案, 
     *           "IfRight"=> 是否正确, 
     *           "TestID"=> 试题id, 
     *           "Analytic"=> 解析, 
     *           "DocID"=> 文档id, 
     *           "DocName"=> 文档名称, 
     *           "No"=>  收藏试题序号 
     *       ], 
     *       
     *       ...
     *   ], 
     *   "first"=> 本页第一道题的试题序号, 
     *       "allAmount"=>  本知识点下的收藏试题的总数量, 
     *       "klName"=> 知识点名称
     * ], 
     *   "info"=> "success", 
     *   "status"=> 1
     * ]
     * @author demo
     */
    public function returnTestList() {
        $this->checkRequest();
        $id = $_POST['id'];
        $username = $this->getUserName();
        $IData = $this->getApiAat('Test/wrongTestList', $id,$username,$isApp=true);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

}