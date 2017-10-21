<?php
/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-4-22
 * Time: 上午9:14
 */
namespace Aat\Controller;
class TestWrongController extends BaseController
{

    public function _initialize() {
    }

    /**
     * 返回错误试题在知识点上的反应
     * [注意]不同次的测试，同一试题如果错误多次，会记录多次
     * @author demo
     */
    public function returnKlInfo() {
        $username = $this->getUserName();
        $subjectID = $this->getSubjectID();
        $IData = $this->getApiAat('Test/wrongKlTree', $username,$subjectID);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError('50200', 1); //请求数据错误，请重试！
    }

    /**
     * 返回任意知识点ID下的错误试题信息
     * @param $id int KlID
     * @author demo
     */
    public function testList($id) {
//        $knowledgeCache = SS('knowledge');
        $knowledgeCache = $this->getApiCommon('Knowledge/knowledge');
        $klName = $knowledgeCache[$id];
        $this->assign([
            'pageName'=>'我的错题',
            'kl_id' => $id,
            'kl_name' => $klName['KlName']
        ]);
        $this->display();
    }

    /**
     * 返回错误试题
     * @author demo
     */
    public function returnTestList() {
        $this->checkRequest();
        $id = $_REQUEST['id'];
        $username = $this->getUserName();
        $IData = $this->getApiAat('Test/wrongTestList', $id,$username,$isApp=false);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

}