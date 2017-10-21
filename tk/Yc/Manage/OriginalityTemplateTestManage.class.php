<?php
/**
 * 模板试题管理
 * @author demo 2015-9-8
 */
namespace Yc\Manage;
class OriginalityTemplateTestManage extends BaseController {
    private $responseStatus;
    private $moduleName = '原创模板管理';
    public function __construct(){
        parent::__construct();
        //当学科为设定时，将无法查询相关数据
        if(empty($this->mySubject)){
            $this->mySubject = 1000;
        }
        $this->responseStatus = IS_AJAX ? 1 : 0;
    }

    /**
     * 后台原创模板试题列表
     * @author demo 
     */
    public function index(){
        $params = array();
        $page = (int)$_REQUEST['p'];
        if(empty($page)){
            $page = 1;
        }
        $params['TID'] = $map['tid'] = (int)$_REQUEST['tid'];
        if($this->ifSubject){
            $params['SubjectID'] = $this->mySubject;
        }
        $perpage = 25;
        $ot = new \Yc\Model\OriginalityTemplateTestModel();
        $ot->setPagtion($perpage, $page);
        $result = $ot->getListByPagtion($params);
        $this->pageList($result[1], $perpage, $map);
        $typesCache = SS('types');
        $types = array();
        foreach($typesCache as $key=>$value){
            $types[$key] = $value['TypesName'];
        }
        unset($typesCache);
        $this->assign('diff', C('WLN_TEST_DIFF'));
        $this->assign('types', $types);
        $this->assign('list', $result[0]);
        $this->assign('pageName', '模板试题管理');
        $this->display();
    }

    /**
     * 后台原创模板试题修改人数限制
     * @author demo 
     */
    public function editLimitNum(){
        $id = (int)$_GET['id'];
        if(empty($id)){
            $this->setError('30301', $this->responseStatus);
        }
        // $this->isCreateAudit($id);
        //验证当前是否已经有用户参与
        $ort = new \Yc\Model\OriginalityRelateTestModel();
        if($ort->getPartakeCountByTplTestId($id) > 0){
            $this->setError('82003', $this->responseStatus);
        }
        $ott = new \Yc\Model\OriginalityTemplateTestModel($id);
        $result = $ott->getOneRecord();
        if(empty($result)){
            $this->setError('30306', $this->responseStatus);
        }
        $this->setBack(array(
            'TTID' => $result['TTID'],
            'LimitNum' => $result['LimitNum'],
        ));
    }

    /**
     * 后台原创模板试题保存修改人数限制
     * @author demo 
     */
    public function saveLimitNum(){
        $id = $_POST['id'];
        if(empty($id)){
            $this->setError('30301', $this->responseStatus);
        }
        $this->isCreateAudit($id);
        $ott = null;
        $type = $_POST['type'];
        if(!$type){
            $type = 'normal';
        }
        $ort = new \Yc\Model\OriginalityRelateTestModel();
        //验证当前是否已经有用户参与
        if($ort->getPartakeCountByTplTestId($id) > 0){
            $this->setError('82003', $this->responseStatus);
        }
        $ott = null;
        $limitNum = (int)$_POST['LimitNum'];
        //单个用户的数据操作
        if('normal' == $type){
            $ott = new \Yc\Model\OriginalityTemplateTestModel($id);
            $ott = $ott->updatePersonLimit($limitNum);
        }else{ //批量操作
            $ott = new \Yc\Model\OriginalityTemplateTestModel();
            $ott = $ott->batchUpdatePersonLimit($id, $limitNum);
        }
        if($ott === false){
            $this->setError('30303', $this->responseStatus);
        }
        $this->adminLog($this->moduleName,"修改编号为【{$id}】人数限制为【{$limitNum}】");
        $this->setBack($limitNum);
    }


    /**
     * 后台原创模板试题选题
     * @author demo 
     */
    public function selectedTopic(){
        $id = (int)$_GET['id'];
        if(0 == $id){
            $this->setError('30301', $this->responseStatus);
        }
        // $this->isCreateAudit($id);
        $page = $_GET['p'];
        if(empty($page)){
            $page = 1;
        }
        $prepage = 8;
        $result = array();
        $sid = (int)$_GET['sid'];
        $currentStage = $sid;
        if(isset($_GET['currentStage'])){
            $currentStage = $_GET['currentStage'];
            $ott = new \Yc\Model\OriginalityTemplateTestModel($id);
            $currentStage = (int)$_GET['currentStage'];
            if(0 == $currentStage){
                $this->setError('30301', $this->responseStatus);
            }
            $ott->setPagtion($prepage, $page);
            $result = $ott->getTestByStageId($currentStage, $this->mySubject, $sid);
        }else{
            $ort = new \Yc\Model\OriginalityRelateTestModel();
            $ort->setPagtion($prepage,$page);
            $result = $ort->getTestList($id);
        }
        foreach($result[0] as $key=>$value){
            $result[0][$key]['Test'] = formatString('IPReturn',stripslashes_deep($value['Test']));
            $result[0][$key]['Answer'] = formatString('IPReturn',stripslashes_deep($value['Answer']));
            $result[0][$key]['Analytic'] = formatString('IPReturn',stripslashes_deep($value['Analytic']));
        }
        $os = new \Yc\Model\OriginalityStageModel();
        $os->setPagtion(1000, 1);
        $stage = $os->getList();
        $stageArr = array();
        foreach($stage as $value){
            if($value['SID'] != $currentStage)
                $stageArr['s'.$value['SID']] = $value['Order'];
        }
        unset($stage);
        $this->setBack(array($result[0], $stageArr, $result[1], $sid));
    }

    /**
     * 后台原创模板试题保存选题
     * @author demo 
     */
    public function saveSelectedTopic(){
        $rtid = (int)$_POST['rtid'];
        $ttid = (int)$_POST['ttid'];
        if(0 == $rtid || 0 == $ttid){
            $this->setError('30301', $this->responseStatus);
        }
        $this->isCreateAudit($ttid);
        $ort = new \Yc\Model\OriginalityRelateTestModel($rtid);
        $result = $ort->saveSelectedTopic($ttid);
        if($result === false){
            $err = $ort->getErrorMessage();
            if(!empty($err))
                $this->setError($err);
            $this->setError('30307');
        }
        $this->adminLog($this->moduleName,"保存选题，模板试题编号【{$ttid}】，关联试题编号【{$rtid}】");
        $this->setBack('success');
    }

    //检查是否已经创建审核任务
    private function isCreateAudit($id){
        $model = new \Yc\Model\OriginalityTemplateTestModel($id);
        $data = $model->getOneRecord();
        $model = new \Yc\Model\OriginalityAuditModel();
        if($model->isCreate($data['TID'])){
            $this->setError('82005', $this->responseStatus);
        }
    }
}