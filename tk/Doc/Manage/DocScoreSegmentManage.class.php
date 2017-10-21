<?php
/**
 * 分值段管理后台控制器
 * @author demo 16-4-22
 */
namespace Doc\Manage;
class DocScoreSegmentManage extends BaseController {
    /**
     * 试卷分段管理列表
     * @author demo 16-4-22
     */
    public function index(){
        $requestParams = array(
            'DocName'=>'',
            'DocID'=>0,
            'p'=>1,
            'prepage' => 20
        );
        $keys = array_keys($requestParams);
        foreach($_REQUEST AS $key=>$value){
            if(in_array($key, $keys) && !empty($value)){
                $requestParams[$key] = $value;
            }
        }
        unset($keys);
        foreach($requestParams as $key=>$value){
            if(empty($value)){
                unset($requestParams[$key]);
            }
        }
        if($this->ifSubject){
            $requestParams['SubjectID'] = $this->mySubject;
        }
        $fields = array('ud.*', 'd.DocName');
        $model = $this->getModel('DocScoreSegment');
        $count = $model->unionSelect('getDocScoreSegmentList', $fields, $requestParams, true);
        $list = $model->unionSelect('getDocScoreSegmentList', $fields, $requestParams);
        $prepage = $requestParams['prepage'];
        unset($requestParams['prepage']);
        $this->pageList($count, $requestParams['prepage'], $requestParams);
        $this->assign('list', $list);
        $this->assign('pageName', '分段管理列表');
        $this->display();
    }

    public function save(){
        $model = $this->getModel('DocScoreSegment');
        $docId = (int)$_POST['DocID'];
        $id = (int)$_POST['id'];
        $data = array();
        foreach($_POST['Description'] as $key=>$value){
            $data[] = array(
                'Description' => $value,
                'BeginPosition' => (int)$_POST['BeginPosition'][$key],
                'EndPosition' => (int)$_POST['EndPosition'][$key]
            );
        }
        $result = $model->save($docId, $data, $id);
        if($result ===  false){
            $this->setError('30307', 1);
        }
        $fields = array('ud.*', 'd.DocName');
        if($id > 0){
            $where['ID'] = $id;
        }else{
            $where['ID'] = implode(',', $result);
        }
        $list = $model->unionSelect('getDocScoreSegmentList', $fields, $where);
        $this->setBack($list);
    }

    public function del(){
        $id = (int)$_GET['id'];
        if(empty($id)) {
            $this->setError('30301', 1); //数据标识不能为空！
        }
        $result = $this->getModel('DocScoreSegment')->deleteData('ID='.$id);
        if($result === false){
            $this->setError('30302', 1);
        }
        $this->setBack('success');
    }

    public function edit(){
        $id = $_GET['id'];
        if(empty($id)) {
            $this->setError('30301', 1); //数据标识不能为空！
        }
        $data = $this->getModel('DocScoreSegment')->findData('*', 'ID='.$id);
        $this->setBack($data);
    }
}
/*
跳转至指定页面做题->验证是否已经输入订单号->订单号已使用同时为本人，输入则直接进入作答页。或者提示输入订单号，然后将订单表改为已使用->作答结束，判断该试卷是否需要自己打分（条件：user_test_record_attr表中TopicPaperID关联DocID不为0，同时可能与表中的DocID一致？）->
需要打分：需要对每道主观题进行打分，同时不能超出该题的最大分数，然后给出总分描述。无需打分：直接提交。->完成作答
*/