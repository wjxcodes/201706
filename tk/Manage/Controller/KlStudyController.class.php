<?php
/**
 * @author demo
 * @date 
 * @update 2015年1月26日
 */
/**
 * 考点学习管理类，用于自定义打分的相关操作
 */
namespace Manage\Controller;
class KlStudyController extends BaseController  {
    var $moduleName = '考点学习管理';
    /**
     * 考点学习浏览列表
     * @author demo
     */
    public function index() {
        $pageName = '考点学习管理';
        $map = array();
        $data = ' 1=1 ';
        if($_REQUEST['name']){
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND Content like "%'.$_REQUEST['name'].'%" ';
        }else{
            //高级查询
            if($_REQUEST['SubjectID'] and !$_REQUEST['KlID']){
                $this->setError('10223');//数据标识不能为空！
            }
            if($_REQUEST['KlID']){
                $klID = $_REQUEST['KlID'];
                $map['KlID'] = $klID;
                if(!is_array($klID) && strstr($klID,',')){
                    $data .= ' AND KlID in ('.$klID.') ';
                }else{
                    $buffer = SS('knowledge');//获取知识点
                    $bufferIDList = SS('klList');//获取知识点
                    if(!is_array($klID)) $klID=explode(',',$klID);
                    if($buffer[$klID[count($klID)-1]]['Last']==1){
                        $data .= ' AND KlID ="'.$klID[count($klID)-1].'" ';
                    }else{
                        if(!empty($bufferIDList[$klID[count($klID)-2]])){
                            $data .= ' AND KlID in ('.$bufferIDList[$klID[count($klID)-2]].') ';
                        }
                    }
                }
            }
            if($_REQUEST['id']){
                $map['id'] = $_REQUEST['id'];
                $data .= ' AND StudyID= "'.$_REQUEST['id'].'" ';
            }
            if($_REQUEST['Content']){
                $map['Content'] = $_REQUEST['Content'];
                $data .= ' AND Content like "%'.$_REQUEST['Content'].'%" ';
            }
            if(is_numeric($_REQUEST['Status'])){
                $map['Status'] = $_REQUEST['Status'];
                $data .= ' AND Status = "'.$_REQUEST['Status'].'" ';
            }
        }
        $perpage = C('WLN_PERPAGE');
           $klStudy=$this->getModel('KlStudy');
        $count = $klStudy->selectCount(
            $data,
            'StudyID',
            '');// 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page = page($count,$_GET['p'],$perpage);
        $page =$page.','.$perpage;
        $list = $klStudy->pageData(
            '*',
            $data,
            'StudyID DESC',
            $page);

        $this->pageList($count,$perpage,$map);
        if($list){
            foreach($list as $i=>$iList){
                $param['style']='knowledgeList';
                $param['ID']=$iList['KlID'];
                $pathName=$this->getData($param);
                $list[$i]['KlName']=$pathName[0]['KlName'];
            }
        }
        //学科
        $subjectArray = SS('subjectParentId'); //获取学科数据集
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加考点学习
     * @author demo
     */
    public function add() {
        $pageName = '添加考点学习';
        $act = 'add'; //模板标识
        //学科
        $subjectArray = SS('subjectParentId'); //获取学科数据集 
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑考点学习
     * @author demo
     */
    public function edit() {
        $studyID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($studyID)) {
            $this->setError('30301');//数据标识不能为空！
        }
        $pageName = '编辑考点学习';
        $act = 'edit'; //模板标识
        $klStudy = $this->getModel('KlStudy');
        $edit = $klStudy->selectData(
            '*',
            'StudyID='.$studyID,
            '',
            '1');
        $edit[0]['Content']=R('Common/TestLayer/strFormat',array($edit[0]['Content']));
        $edit[0]['Careful']=R('Common/TestLayer/strFormat',array($edit[0]['Careful']));
        $tmpStr = explode('#@#',$edit[0]['VideoList']);
        $video = array();
        foreach($tmpStr as $i=>$iTmpStr){
            $tmpStr2=explode('#$#',$iTmpStr);
            $video[$i]['Code'] = $tmpStr2[0];
            $video[$i]['VideoName'] = $tmpStr2[1];
        }
        $buffer = SS('knowledge');
        $edit[0]['SubjectID']=$buffer[$edit[0]['KlID']]['SubjectID'];
        //查找父类id
        $buffer=SS('knowledgeParent');  // 缓存父类路径数据
        $bufferx=SS('klBySubject3');  // 缓存子类list数据
        $knowledgeArray=$bufferx[$edit[0]['SubjectID']];//获取第一级选项
        $knowledgeParentStr='';//父类路径包括自己
        $bufferTmp=array();
        if($buffer[$edit[0]['KlID']]) krsort($buffer[$edit[0]['KlID']]);
        if($buffer[$edit[0]['KlID']]){
            foreach($buffer[$edit[0]['KlID']] as $iBuffer){
                $bufferTmp[]=$iBuffer['KlID'];
            }
            $knowledgeParentStr='|'.implode('|',$bufferTmp).'|t'.$edit[0]['KlID'].'|';
        }else{
            $knowledgeParentStr='|'.$edit[0]['KlID'].'|';
        }
        //学科
        $subjectArray=SS('subjectParentId'); //获取学科数据集
        $jsonEdit=json_encode( $edit[0]);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('data', $jsonEdit);
        $this->assign('Video', $video);
        $this->assign('knowledgeParentStr', $knowledgeParentStr);
        $this->assign('knowledgeArray', $knowledgeArray);
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName);
        $this->display('KlStudy/add');
    }
    /**
     * 保存考点学习
     * @author demo
     */
    public function save() {
        $studyID = $_POST['StudyID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($studyID) && $act == 'edit') {
            $this->setError('30301');//数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223');//模板标识不能为空！
        }
        $klIDArr=$_POST['KlID'];
        //检查KlID是否正确
        if($klIDArr){
            if(!is_array($klIDArr)){
                $klIDArr=array($klIDArr);
            }
            $buffer=SS('knowledge');
            $klID = str_replace('t','',$klIDArr[count($klIDArr)-1]);
            if($buffer[$klID]['Last'] != 1){
                $this->setError('10120');//不能在父类知识点上操作！
            }
        }else{
            $this->setError('10121');//知识点不能为空！
        }
        $data = array ();
        $data['Content']=formatString('IPReplace',$_POST['Content']);
        $data['Careful']=formatString('IPReplace',$_POST['Careful']);
        $data['Status'] = $_POST['Status'];
        $data['KlID'] = $klID;
        $code = $_POST['Code'];
        $videoName = $_POST['VideoName'];
        $videoList = '';
        if($code && $videoName){
            if(!is_array($code)){
                $videoList = $code.'#$#'.$videoName;
            }else{
                $tmpStr = array();
                foreach($code as $i => $iCode){
                    if($iCode && $videoName[$i]) $tmpStr[$i] = $iCode.'#$#'.$videoName[$i];
                }
                $videoList = implode('#@#',$tmpStr);
            }
              }
        $klStudy = $this->getModel('KlStudy');
        $data['VideoList'] = $videoList;
        if ($act == 'add') {
            $data['LoadTime'] = time();
            if($klStudy->insertData(
                    $data) === false){
                $this->setError('30310');//添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加考点学习【考点'.$klID.'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        } else
            if ($act == 'edit') {
                if($klStudy->updateData(
                        $data,
                        'StudyID='.$_POST['StudyID']) === false){
                    $this->setError('30311');//修改失败！
                }else{
                    //写入日志
                    $this->adminLog($this->moduleName,'修改考点学习StudyID为【'.$_POST['StudyID'].'】的数据');
                    $this->showSuccess('修改成功！', __URL__);
                }
            }
    }
    /**
     * 删除考点学习
     * @author demo
     */
    public function delete() {
        $studyID = $_POST['id']; //获取数据标识
        if (!$studyID) {
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        if ($this->getModel('KlStudy')->deleteData(
                'StudyID in ('.$studyID.')') === false) {
            $this->setError('30302');//删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName,'删除考点学习StudyID为【'.$studyID.'】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
}