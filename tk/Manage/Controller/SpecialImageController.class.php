<?php
/**
 * 真题图片
 * @author demo 16-6-2
 */
namespace Manage\Controller;
class SpecialImageController extends BaseController{
    public function index(){
        $model = $this->getModel('SpecialImage');
        $page = (int)$_REQUEST['p'];
        if(!$page){
            $page = 1;
        }
        $prepage = 20;
        $params['Title'] = $_REQUEST['Title'];
        $params['Status'] = $_REQUEST['Status'];
        $params['UserName'] = $_REQUEST['UserName'];
        $params['Start'] = $_REQUEST['Start'];
        $params['End'] = $_REQUEST['End'];
        $start = strtotime($params['Start']);
        if($start !== false){
            $params['Start'] = $start;
        }
        $end = strtotime($params['End']);
        if($end !== false){
            $params['End'] = $end;
        }
        foreach($params as $key=>$value){
            if(empty($value)){
                unset($params[$key]);
            }
        }
        $data = $model->unionSelect('getImagesInfo', $params, $page, $prepage);
        $map = array();
        $this->pageList($data[0],$prepage,$params);
        $this->assign('host', C('WLN_DOC_HOST'));
        $this->assign('list', $data[1]);
        $this->assign('pageName', '专题图片管理');
        $this->display();
    }

    /**
     * 审核图片
     * @author demo 16-6-2
     */
    public function check(){
        $status = (int)$_POST['status'];
        $id = (int)$_POST['id'];
        if(empty($status) || empty($id)){
            $this->setError('30301', 1);
        }
        $model = $this->getModel('SpecialImage');
        $result = $model->updateData(array('Status'=>$status),  'IID='.$id);
        if($result === false){
            $this->setError('30311', 1);
        }
        $this->setBack('success');
    }

    /**
     * 删除图片
     * @author demo 16-6-2
     */
    public function del(){
        $id = (int)$_POST['id'];
        if(empty($id)){
            $this->setError('30301', 1);
        }
        $model = $this->getModel('SpecialImage');
        $data = $model->findData('Path', 'IID='.$id);
        if(empty($data)){
            $this->setError('30306', 1);
        }
        $result = $model->deleteData('IID='.$id);
        if($result === false){
            $this->setError('30302', 1);
        }
        $model->appendDelFile($data['Path']);
        $this->setBack('success');
    }
}