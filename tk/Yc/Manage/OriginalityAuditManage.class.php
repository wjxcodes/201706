<?php
/**
 * 模板审核管理
 * @author demo 2015-9-8
 */
namespace Yc\Manage;
class OriginalityAuditManage extends BaseController {
    private $responseStatus;
    private $moduleName = '审核任务管理';
    public function __construct(){
        parent::__construct();
        //当学科为设定时，将无法查询相关数据
        if(empty($this->mySubject)){
            $this->mySubject = 1000;
        }
        $this->responseStatus = IS_AJAX ? 1 : 0;
    }

    /**
     * 后台原创模板审核列表
     * @author demo 
     */
    public function index(){
        $tid = (int)$_GET['tid'];
        $params = array();
        if($tid != 0){
            $params['TID'] = $map['tid'] = (int)$_REQUEST['tid'];
        }
        if($this->ifSubject){
            $params['SubjectID'] = $map['SubjectId'] = $this->mySubject;
        }
        $oa = new \Yc\Model\OriginalityAuditModel();
        $perpage = 15;
        $page = (int)$_REQUEST['p'];
        if(empty($page)){
            $page = 1;
        }
        $oa->setPagtion($perpage, $page);
        $result = $oa->getListByPagtion($params);
        $this->pageList($result[1], $perpage, $map);
        $subjetCache = SS('subject');
        $subjects = array();
        foreach($subjetCache as $key=>$value){
            $subjects[$key] = $value['SubjectName'];
        }
        $this->assign('list', $result[0]);
        $this->assign('subjects', $subjects);
        $this->assign('pageName', '审核任务管理');
        $this->display();
    }

    /**
     * 后台原创模板审核通过
     * @author demo 
     */
    public function passAudit(){
        $id = (int)$_POST['id'];
        if(0 == $id){
            $this->setError('30301', $this->responseStatus);
        }
        $oa = new \Yc\Model\OriginalityAuditModel($id);
        if($oa->pass() === false){
            $this->setError('30307', $this->responseStatus);
        }
        $this->adminLog($this->moduleName,"将编号【{$id}】的任务审核通过");
        $this->setBack('success');
    }

    /**
     * 后台原创模板审核获取审核试题列表
     * @author demo 
     */
    public function getAuditTestList(){
        $aid = (int)$_GET['aid'];
        $tid = (int)$_GET['tid'];
        if(0 == $aid || 0 == $tid){
            $this->setError('30301', $this->responseStatus);
        }
        $ott = new \Yc\Model\OriginalityTemplateTestModel();
        $result = $ott->getTplTestById($tid, 'TTID');
        $list = array();
        foreach($result as $value){
            $list[] = $value['TTID'];
        }
        unset($result);
        $page = 1;
        if($_GET['p']){
            $page = (int)$_GET['p'];
        }
        $prepage = 10;
        $ort = new \Yc\Model\OriginalityRelateTestModel();
        $ort->setPagtion($prepage,$page);
        $result = $ort->getTestList($list, 1);
        $list = array();
        foreach($result[0] as $key=>$value){
            $result[0][$key]['Test'] = formatString('IPReturn',stripslashes_deep($value['Test']));
            $result[0][$key]['Answer'] = formatString('IPReturn',stripslashes_deep($value['Answer']));
            $result[0][$key]['Analytic'] = formatString('IPReturn',stripslashes_deep($value['Analytic']));
            $list[] = (int)$value['RTID'];
        }
        $oa = new \Yc\Model\OriginalityAuditTestModel();
        $data = $oa->getTestList($list, $aid);
        $list = array();
        //此处组合出备选试题
        foreach($data as $value){
            if((int)$value['ReserveTest'] > 0){
                $value['Test'] = formatString('IPReturn',stripslashes_deep($value['Test']));
                $value['Answer'] = formatString('IPReturn',stripslashes_deep($value['Answer']));
                $value['Analytic'] = formatString('IPReturn',stripslashes_deep($value['Analytic']));
            }
            $list[$value['RTID']] = $value;
        }
        $this->setBack(array($result, $list));
    }

    /**
     * 后台原创模板审核更新选题
     * @author demo 
     */
    public function upgradeSelectedTest(){
        $rtid = (int)$_POST['rtid'];
        $selectedTestId = (int)$_POST['selectedTestId'];
        $orginalTestId = (int)$_POST['orginalTestId'];
        $atid = (int)$_POST['atid'];
        $tid = (int)$_POST['tid'];
        if(0 == $tid || 0 == $rtid || 0 == $selectedTestId || 0 == $atid || 0 == $orginalTestId){
            $this->setError('30301', $this->responseStatus);
        }
        $oa = new \Yc\Model\OriginalityAuditModel();
        $result = $oa->getStatusByTemplateId($tid);
        if(3 == (int)$result['Status']){
            $this->setError('82001', $this->responseStatus);
        }
        $ort = new \Yc\Model\OriginalityRelateTestModel($rtid);
        if($ort->upgradeSelectedTest($atid, $orginalTestId, $selectedTestId) === false){
            $this->setError('30303', $this->responseStatus);
        }
        $this->adminLog($this->moduleName,"更新编号【{$rtid}】的选题为【{$selectedTestId}】");
        $this->setBack('success');
    }
}