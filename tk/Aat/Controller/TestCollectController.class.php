<?php
/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-4-22
 * Time: 上午9:15
 */
namespace Aat\Controller;
class TestCollectController extends BaseController
{

    public function _initialize() {
    }

    /**
     * 显示知识点树状结构的收藏试题
     * @author demo
     */
    public function returnKlInfo() {
        $subjectID = $this->getSubjectID();
        $username = $this->getUserName();
        $IData = $this->getApiAat('Test/collectKlTree', $subjectID,$username);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError('50200', 1);
    }

    /**
     * 某知识点下的收藏试题
     * @param $id int 知识点ID
     * @author demo
     */
    public function testList($id) {
//        $knowledgeCache = SS('knowledge');
        $knowledgeCache = $this->getApiCommon('Knowledge/knowledge');
        $klName = $knowledgeCache[$id];
        $this->assign([
            'pageName'=>'我的收藏',
            'kl_id'=>$id,
            'kl_name'=>$klName['KlName'],
        ]);
        $this->display();
    }

    /**
     * 返回收藏的试题内容
     * @author demo
     */
    public function returnTestList() {
        $this->checkRequest();
        $id = $_REQUEST['id'];
        $username=$this->getUserName();
        $subjectID=$this->getSubjectID();
        $IData = $this->getApiAat('Test/collectTestList', $id,$username,$subjectID,$isApp=false);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }


    /**
     * 描述:上线前需要操作的，统计UserCollect表中的数据到UserCollectTj表
     * @todo 上线后注释掉，组卷看需要不 【注意】，不能重复 修改limit
     * @author demo
     */
    /*
    public function generateTj(){
        $userDb = $this->dbConn->field('u.UserName,c.TestID,c.SubjectID')
            ->table($this->formatTable('UserCollect').' c')
            ->join($this->formatTable('User').' u ON c.UserName=u.UserName')
            ->where(['u.Whois'=>0])//0 提分用户
            ->limit(0,500)
            ->select();

        if($userDb){
            foreach($userDb as $iUserDb){
                $userName = $iUserDb['UserName'];
                $testID = $iUserDb['TestID'];
                $ifExist = $this->dbConn->findData(
                    'testAttrReal',
                    'TestID,SubjectID',
                    ['TestID'=>$testID]);
                if($ifExist){
                    $this->updateCollectTj($userName,$testID,'add',$ifExist['SubjectID']);//更新收藏统计表数据
                    echo $userName.'==='.$testID.' ok<br>';
                }else{
                    echo $userName.'==='.$testID.' errorerrorerrorerrorerrorerrorerrorerrorerrorerrorerrorerrorerrorerrorerrorerrorerrorerrorerrorerror<br>';
                }

            }
        }else{
            echo 'over!!!!!!!!!!!!!!!!';
        }

    }*/
    /**
     * 收藏试题
     * @author demo
     */
    public function save() {
        $this->checkRequest();
        $testID = $_POST['id'];
        $username = $this->getUserName();
        $IData = $this->getApiAat('Test/collectSave', $testID,$username);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

    /**
     * 删除收藏
     * @author demo
     */
    public function del() {
        $this->checkRequest();
        $testID = $_POST['id'];
        $username = $this->getUserName();
        $IData = $this->getApiAat('Test/collectDel', $testID,$username);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }
}