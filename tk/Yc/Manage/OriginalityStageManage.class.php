<?php
/**
 * 模板期次管理
 * @author demo 2015-9-8
 */
namespace Yc\Manage;
class OriginalityStageManage extends BaseController {
    private $responseStatus;
    private $moduleName = '模板期次管理';
    public function __construct(){
        parent::__construct();
        $this->responseStatus = IS_AJAX ? 1 : 0;
    }

    /**
     * 后台原创模板期次列表
     * @author demo 
     */
    public function index(){
        $params = array();
        $page = (int)$_REQUEST['p'];
        if(empty($page)){
            $page = 1;
        }
        $perpage = 10;
        $os = new \Yc\Model\OriginalityStageModel();
        $os->setPagtion($perpage, $page);
        $result = $os->getListByPagtion($params);
        $this->pageList($result[1], $perpage, array());
        $this->assign('list', $result[0]);
        $this->assign('pageName', '模板期次管理');
        $this->display();
    }

    /**
     * 后台原创模板期次编辑
     * @author demo 2015-9-9
     */
    public function edit(){
        $id = (int)$_GET['id'];
        if(empty($id)){
            $this->setError('30301', $this->responseStatus);
        }
        $os = new \Yc\Model\OriginalityStageModel($id);
        $result = $os->getList();
        if(empty($result)){
            $this->setError('30306', $this->responseStatus);
        }
        foreach($result as $key=>$value){
            $result[$key]['BeginTime'] = date('Y-m-d', $value['BeginTime']);
            $result[$key]['EndTime'] = date('Y-m-d', $value['EndTime']);
        }
        $this->setBack($result);
    }

    /**
     * 后台原创模板期次删除
     * @author demo 2015-9-9
     */
    public function del(){
        $id = (int)$_GET['id'];
        if(empty($id)){
            $this->setError('30301', $this->responseStatus);
        }
        $ot = new \Yc\Model\OriginalityTemplateModel();
        $result = $ot->getTemplates($id);
        if(!empty($result['data'])){
            $this->setError('82002', $this->responseStatus);
        }
        //验证该期次下面是否已经添加相关模板
        $os = new \Yc\Model\OriginalityStageModel($id);
        $result = $os->delete();
        if($result === false){
            $this->setError('30302', $this->responseStatus);
        }
        $this->adminLog($this->moduleName,'删除期次信息【'.$id.'】');
        $this->setBack('success');
    }

    /**
     * 后台原创模板期次数据保存
     * @author demo 2015-9-9
     */
    public function save(){
        $id = (int)$_POST['id'];
        $act = ($id === 0); //true时为新增操作
        $os = new \Yc\Model\OriginalityStageModel($id);
        $data = $this->getRequestParams();
        if($act){
            $data['AddTime'] = time();
            $result = $os->insert($data);
        }else{
            $result = $os->update($data);
        }
        if($result === false){
            $err = $act ? '30310' : '30303';
            $this->setError($err, $this->responseStatus);
        }
        $info = $act ? '保存' : '修改';
        $this->adminLog($this->moduleName, $info.'期次信息【'.$os->getId().'】');
        $result = $os->getList();
        $result[0]['BeginTime'] = date('Y-m-d', $result[0]['BeginTime']);
        $result[0]['EndTime'] = date('Y-m-d', $result[0]['EndTime']);
        $result[0]['AddTime'] = date('Y-m-d H:i:s', $result[0]['AddTime']);
        $this->setBack($result);
    }

    /**
     * 返回需要处理的参数
     * @author demo 
     */
    private function getRequestParams(){
        return array(
            'Title' => $_POST['Title'],
            'Order' => $_POST['Order'],
            'BeginTime' => strtotime($_POST['BeginTime']),
            'EndTime' => strtotime($_POST['EndTime']),
            'Admin' => $this->getCookieUserID()
        );
    }
}