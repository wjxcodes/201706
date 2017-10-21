<?php
/**
 * 模板管理
 * @author demo 2015-9-8
 */
namespace Yc\Manage;
class OriginalityTemplateManage extends BaseController {
    private $responseStatus;
    private $moduleName = '原创模板管理';
    public function __construct(){
        parent::__construct();
        $this->responseStatus = IS_AJAX ? 1 : 0;
    }

    /**
     * 原创-模板管理
     * @author demo
     */
    public function index(){
        $params = array();
        $page = (int)$_REQUEST['p'];
        if(empty($page)){
            $page = 1;
        }
        $params['SID'] = $map['sid'] = (int)$_REQUEST['sid'];
        if($this->ifSubject){
            if(empty($this->mySubject)){
                $this->mySubject = 10000;
            }
            $params['SubjectID'] = $this->mySubject;
        }
        $perpage = 15;
        $ot = $this->getModel('OriginalityTemplate');
        $ot->setPagtion($perpage, $page);
        $result = $ot->getListByPagtion($params);
        $this->pageList($result[1], $perpage, $map);
        //提取学科数据
        $subjetCache = SS('subject');
        $subjects = array();
        foreach($subjetCache as $key=>$value){
            $subjects[$key] = $value['SubjectName'];
        }
        // $this->assign('docType', SS('examType'));
        $this->assign('subjects', $subjects);
        $this->assign('list', $result[0]);
        $this->assign('pageName', '原创模板管理');
        $this->display();
    }

    /**
     * 后台原创模板创建审核任务
     * @author demo 
     */
    public function createAuditTask(){
        $id = $_GET['id'];
        if(empty($id)){
            $this->setError('30301', $this->responseStatus);
        }

        $ot = new \Yc\Model\OriginalityTemplateModel($id);
        $result = $ot->isFinal();
        //存在为选题的内容
        if(is_array($result)){
            $this->setError('82004', $this->responseStatus, '', implode('|', $result));
        }
        $data = $ot->getOneRecord();
        $list = $this->getModel('User')->getTaskUserByGroup(6, array(
            'SubjectID' => $data['SubjectID'],
            'name' => $_GET['name'],
            'p' => 1
        ));
        $this->setBack($list);
    }

    /**
     * 后台原创模板保存审核任务
     * @author demo 
     */
    public function saveAuditTask(){
        $tid = (int)$_POST['tid'];
        $userid = (int)$_POST['userid'];
        if(0 == $tid || 0 == $userid){
            $this->setError('30301', $this->responseStatus);
        }
        $oa = new \Yc\Model\OriginalityAuditModel(); 
        if($oa->isCreate($tid)){
            $this->setError('82005', $this->responseStatus);
        }
        $data = array(
            'TID' => $tid,
            'UserID' => $userid
        );
        $result = $oa->insert($data);
        if($result === false){
            $this->setError('30307', $this->responseStatus);
        }
        $id = $oa->getId();
        $this->adminLog($this->moduleName,"添加编号【{$id}】的审核任务");
        $this->setBack('success');
    }

    /**
     * 后台原创模板返回用户列表数据
     * @author demo 
     */
    public function getUserList(){
        $tid = (int)$_GET['tid'];
        if(0 == $tid){
            $this->setError('30301', $this->responseStatus);
        }
        $page = $_GET['p'];
        if(!$page){
            $page = 1;
        }
        $ot = new \Yc\Model\OriginalityTemplateModel($tid);
        $data = $ot->getOneRecord();
        $list = $this->getModel('User')->getTaskUserByGroup(6, array(
            'SubjectID' => $data['SubjectID'],
            'name' => $_GET['name'],
            'p' => $page
        ));
        $this->setBack($list);
    }
}