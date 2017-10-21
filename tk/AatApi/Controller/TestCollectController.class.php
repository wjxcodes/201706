<?php
/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-4-22
 * Time: 上午9:15
 */
namespace AatApi\Controller;
class TestCollectController extends BaseController
{
    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
    }

    /**
     * 显示知识点树状结构的收藏试题
     * @param int subjectID 学科ID true
     * @return array
     *    [
     *   "data"=> [
     *       [
     *           "klName"=> "语言知识基础", 
     *           "amount"=> "2", 
     *           "klID"=> "237", 
     *           "sub"=> [//下一级的数据，字段同上级
     *               [
     *                   "klName"=> "语言知识运用", 
     *                   "amount"=> "2", // 收藏、错题的数量 
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
        $subjectID = $this->getSubjectID();
        $username = $this->getUserName();
        $IData = $this->getApiAat('Test/collectKlTree', $subjectID,$username);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

    /**
     * 返回收藏的试题内容
     * @param int id 知识点ID true
     * @param int p 分页的页码 true
     * @param int startNo 从第几道题开始显示 true
     * @param int subjectID 当前学科ID true
     * @return array
     *    [
     *   "data"=> [
     *       "test"=> [
     *           [
     *               "CollectID"=> 收藏ID, 
     *               "FavName"=> "", 
     *               "TestID"=> 试题id, 
     *               "LoadTime"=> 收藏时间, 
     *               "Test"=> 题文, 
     *               "Answer"=> 答案, 
     *               "Analytic"=> 分析, 
     *               "No"=> 收藏试题序号
     *           ], 
     *           ....
     *       ], 
     *       "first"=> 本页第一道题的试题序号, 
     *       "allAmount"=>  本知识点下的收藏试题的总数量, 
     *       "klName"=> 知识点名称
     *   ], 
     *   "info"=> "success", 
     *   "status"=> 1
     * ]
     * @author demo
     */
    public function returnTestList() {
        $this->checkRequest();
        $id = $_REQUEST['id'];
        $username=$this->getUserName();
        $subjectID=$this->getSubjectID();
        $IData = $this->getApiAat('Test/collectTestList', $id,$username,$subjectID,$isApp=true);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

    /**
     * 收藏试题
     * @param string id 试题ID  true
     * @return array
     *    [
     *   "data"=>"试题已收藏", 
     *   "info"=>"success", 
     *   "status"=>1
     *   ]
     * @author demo
     */
    public function save() {
        $this->checkRequest();
        $testID = $_REQUEST['id'];
        $username = $this->getUserName();
        $IData = $this->getApiAat('Test/collectSave', $testID,$username);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

    /**
     * 删除收藏
     * @param string id 试题ID  true
     * @return array
     *    [
     *   "data"=> "已经取消收藏！", 
     *   "info"=> "success", 
     *   "status"=> 1
     *   ]
     * @author demo
     */
    public function del() {
        $this->checkRequest();
        $testID = $_REQUEST['id'];
        $username = $this->getUserName();
        $IData = $this->getApiAat('Test/collectDel', $testID,$username);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }
}