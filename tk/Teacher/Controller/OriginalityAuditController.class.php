<?php
/**
 * 教师原创模板审核action
 * @author demo 2015-9-16
 */
namespace Teacher\Controller;
class OriginalityAuditController extends BaseController {
    private $moduleName = '原创模板审核';

    /**
     * 教师端原创模板审核列表
     * @author demo 
     */
    public function index(){
        $params['SubjectID'] = $this->mySubject;
        $params['UserID'] = $this->getCookieUserID();
        $oa = new \Yc\Model\OriginalityAuditModel();
        $perpage = 15;
        $page = (int)$_REQUEST['p'];
        if(empty($page)){
            $page = 1;
        }
        $map = array();
        $oa->setPagtion($perpage, $page);
        $result = $oa->getListByPagtion($params);
        $this->pageList($result[1], $perpage, $map);
        $this->assign('list', $result[0]);
        $this->assign('pageName', '原创模板审核');
        $this->display();
    }  

    /**
     * 教师端原创模板审核显示试题
     * @author demo 
     */
    public function showContent(){
        $tid = (int)$_GET['tid'];
        $aid = (int)$_GET['aid'];
        if(0 == $tid || 0 == $aid){
            $this->setError('30502');
        }
        $ot = new \Yc\Model\OriginalityTemplateModel($tid);
        $templateData = $ot->getOneRecord();
        $docTypes = explode(',', $templateData['DocType']);
        //获取试卷类型
        $docType = SS('docType');
        $templateData['DocType'] = array();
        foreach($docTypes as $key=>$value){
            $docTypes[$key] = $docType[$value]['TypeName'];
        }
        $templateData['DocType'] = implode('&nbsp;|&nbsp;', $docTypes);
        unset($docType);

        //查询出该指定模板下面的所有模板试题
        $ott = new \Yc\Model\OriginalityTemplateTestModel();
        $data = $ott->getTplTestById($tid, 'TTID');
        $list = array();
        foreach($data as $value){
            $list[] = $value['TTID'];
        }
        // dump($list);exit;
        //查询出模板试题id的数据
        $ott->setPagtion(10000,1);
        $where = array();
        $where['TID'] = $tid;
        $where['SubjectID'] = $this->mySubject;
        $testData = $ott->getList($where);
        $testData = (array)$testData[0];
        // dump($testData);exit;
        //查询出关联试题id的数据
        $ort = new \Yc\Model\OriginalityRelateTestModel();
        $ort->setPagtion(false);
        $relateTestdata = $ort->getTestList($list, 1);
        $relateTestdata = $relateTestdata[0];
        // dump($relateTestdata);exit;
        //输出试卷数量
        $templateData['testnum'] = count($list);
        unset($list);
        //组合数据
        $num = 1;
        $list = array();
        foreach($testData as $key=>$value){
            $tempNum = $num;
            $test = formatString('IPReturn',stripslashes_deep($relateTestdata[$key]['Test']));
            $relateTestdata[$key]['Test'] = R('Common/TestLayer/changeTagToNum',array($test, $num));
            $num = $tempNum;
            $answer = formatString('IPReturn',stripslashes_deep($relateTestdata[$key]['Answer']));
            if($value['TestNum'] > 1){
                $relateTestdata[$key]['Answer'] = R('Common/TestLayer/changeTagToNum',array($answer, $num));
            }else{
                $relateTestdata[$key]['Answer'] = $answer;
            }
            $num = $tempNum;
            $analytic = formatString('IPReturn',stripslashes_deep($relateTestdata[$key]['Analytic']));
            if($value['TestNum'] > 1){
                $relateTestdata[$key]['Analytic'] = R('Common/TestLayer/changeTagToNum',array($analytic, $num));
            }else{
                $relateTestdata[$key]['Analytic'] = $analytic;
            }
            $testData[$key] = array_merge($value, $relateTestdata[$key]);
            $num += $value['TestNum'];
            $list[$testData[$key]['TypesID']]['data'][] = $testData[$key];
            $list[$testData[$key]['TypesID']]['typeid'] = $testData[$key]['TypesID'];
            unset($testData[$key], $relateTestdata[$key]);
        }
        // dump($list);exit;
        $this->assign('aid', $aid);
        $this->assign('list', $list);
        $this->assign('template', $templateData);
        $this->assign('types', SS('types'));
        $this->display();
    }

    /**
     * 教师端原创模板审核返回其他试题
     * @author demo 
     */
    public function getUnselectedTests(){
        $id = (int)$_GET['id'];
        if(0 == $id){
            $this->setError('40119');
        }
        $oat = new \Yc\Model\OriginalityAuditTestModel();
        $data = $oat->getDataByTtid($id);
        $ort = new \Yc\Model\OriginalityRelateTestModel();
        $ort->setPagtion(false);
        $list = $ort->getTestList($id, 0);
        $list = $list[0];
        foreach($list as $key=>$value){
            $list[$key]['Test'] =  R('Common/TestLayer/changeTagToNum',array(formatString('IPReturn',stripslashes_deep($list[$key]['Test'])), 1));
            $list[$key]['Answer'] =  R('Common/TestLayer/changeTagToNum',array(formatString('IPReturn',stripslashes_deep($list[$key]['Answer'])), 1));
            $list[$key]['Analytic'] =  R('Common/TestLayer/changeTagToNum',array(formatString('IPReturn',stripslashes_deep($list[$key]['Analytic'])), 1));
        }
        $this->setBack(array($list,$data));
    }

    /**
     * 教师端原创模板审核给定意见或者选择备选试题时的保存
     * @author demo 
     */
    public function auditTestSave(){
        $suggestion = $_POST['suggestion'];
        $atid = (int)$_POST['atid'];
        $testid = (int)$_POST['testid'];
        $ttid = (int)$_POST['ttid'];
        $aid = (int)$_POST['aid'];
        if(0 == $aid || 0 == $ttid){
            $this->setError('40119', 1);
        }
        $oa = new \Yc\Model\OriginalityAuditModel($aid);
        $data = $oa->getOneRecord();
        if(2 == $data['Status']){
            $this->setError('40126', 1);
        }
        //更新操作
        $oat = $result = null;
        if($atid > 0){
            $oat = new \Yc\Model\OriginalityAuditTestModel($atid);
            $result = $oat->update(array(
                'TestAuditSuggestion' => $suggestion,
                'ReserveTest' => $testid
            ));
        }else{
            $oat = new \Yc\Model\OriginalityAuditTestModel();
            $result = $oat->insert(array(
                'TestAuditSuggestion' => $suggestion,
                'ReserveTest' => $testid,
                'AID' => $aid,
                'TTID' => $ttid
            ));
        }
        if($result === false){
            $this->setError('30307', 1);
        }
        $oa->update(array(
            'AuditTime' => time()
        ));
        $id = $oat->getId();
        $this->teacherLog($this->moduleName,"保存选题及给定意见，编号【{$id}】");
        $this->setBack($oat->getId());
    }

    /**
     * 教师端原创模板完成审核
     * @author demo 
     */
    public function completeAudit(){
        $id = $_POST['id'];
        if(0 == $id){
            $this->setError('40119', 1);
        }
        $oa = new \Yc\Model\OriginalityAuditModel($id);
        $data = $oa->getOneRecord();
        if(2 == $data['Status']){
            $this->setError('40126', 1);
        }
        $result = $oa->update(array(
            'Status' => 2,
            'AuditSuggestion' => $_POST['suggestion']
        ));
        if($result === false){
            $this->setError('30307', 1);
        }
        $this->teacherLog($this->moduleName,"完成审核，编号【{$id}】");
        $this->setBack('succes');
    }
}