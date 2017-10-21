<?php
/**
 * @author demo
 * @date 2015年5月12日
 */
/**
 * 专题试卷操作相关
 */
namespace Aat\Manage;
class TopicPaperManage extends BaseController  {
    var $moduleName='专题试卷';
    /**
     * 试卷列表
     * @author demo
     */
    public function index() {
        $topicPaperModel = $this->getModel('TopicPaper');
        //获取检错条件
        $pageName="专题试卷管理";
        $data = '1=1';//查询条件
        $map=array();
        if ($_REQUEST['name']) {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND a.TopicPaperName like "%' . $_REQUEST['name'] . '%" ';
        } else {
            //高级查询
            if ($_REQUEST['AddUser']) {
                $map['AddUser'] = $_REQUEST['AddUser'];
                $data .= ' AND a.AddUser like "%' . $_REQUEST['AddUser'] . '%" ';
            }
            if ($_REQUEST['topicName']) {
                $map['TopicName'] = $_REQUEST['TopicName'];
                $data .= ' AND b.TopicName like "%' . $_REQUEST['topicName'] . '%" ';
            }
            if (is_numeric($_REQUEST['Status'])) {
                $map['Status'] = $_REQUEST['Status'];
                $data .= ' AND a.Status = "' . $_REQUEST['Status'] . '" ';
            }
            if (is_numeric($_REQUEST['SubjectID'])) {
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $data .= ' AND a.SubjectID = "' . $_REQUEST['SubjectID'] . '" ';
            }
        }
        $perpage = C('WLN_PERPAGE'); //每页显示数
        $count = $topicPaperModel->unionSelect('topicPaperSelectCount',$data);// 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=(isset ($_GET['p']) ? $_GET['p'] : 1) . ',' . $perpage;
        $list=$topicPaperModel->unionSelect('topicPaperSelectPageByWhere',$data,$page);
        //获取用户id
//        $subject = SS('subject');
        $subject = $this->getApiCommon('Subject/subject');
//        $subjectArray=SS('subjectParentId');
        $subjectArray = $this->getApiCommon('Subject/subjectParentID');
        foreach($list as $i=>$iList){
            $list[$i]['IfWLName']=$iList['IfWL']=='1'? '文科':'理科';
            if($iList['IfWL']==0) $list[$i]['IfWLName']='通用';
            $list[$i]['SubjectName']=$subject[$iList['SubjectID']]['ParentName'].$subject[$iList['SubjectID']]['SubjectName'];
        }
        $this->pageList($count, $perpage, $map);
        $this->assign('pageName',$pageName);
        $this->assign('list',$list);
        $this->assign('subjectArray',$subjectArray);
        $this->theme('Wln')->display();
    }
    /**
     * 新增试卷
     * @author demo
     */
    public function add() {
        $pageName = '添加试卷';
        $act = 'add'; //模板标识
        //获取专题数据集
        $topicData = $this->getModel('Topic')->selectData(
            'TopicID,TopicName,SubjectID',
            '1=1'
        );
        //获取学科数据集
//        $subjectArray = SS('subjectParentId');//学科信息
        $subjectArray = $this->getApiCommon('Subject/subjectParentID');
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('pageName',$pageName);
        $this->assign('topic',$topicData);//专题数据
        $this->assign('subjectArray', $subjectArray);
        $this->theme('Wln')->display();
    }
    /**
     * 编辑试卷
     * @author demo
     */
    public function edit() {
        $pageName = '编辑试卷';
        $act='edit';
        $paperID=$_GET['id'];
        if(empty($paperID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $edit=$this->getModel('TopicPaper')->selectData(
            'TopicPaperID,TopicPaperName,TopicPaperDesc,IfWL,SubjectID,TopicID,PaperType,IfDown,DocID,TestIDs,Status,TestTimes,AddUser',
            'TopicPaperID='.$paperID
        );
        if($edit[0]['TestTimes']>0){
            $this->setError('50005');//测试次数大于0不能修改
        }
        //获取专题数据集
        $topicData = $this->getModel('Topic')->selectData(
            'TopicID,TopicName,SubjectID',
            '1=1'
        );
        foreach($topicData as $i=>$iTopicData){
            if($iTopicData['TopicID'] == $edit[0]['TopicID']){
                $topicSubject = $iTopicData['SubjectID'];
            }
        }
//        $subjectArray = SS('subjectParentId');//学科信息
        $subjectArray = $this->getApiCommon('Subject/subjectParentID');
        $this->assign('act',$act);
        $this->assign('pageName',$pageName);
        $this->assign('topic',$topicData);//专题数据集
        $this->assign('subjectArray',$subjectArray);//学科数据集
        $this->assign('topicSubject',$topicSubject);//编辑试卷所属专题学科
        $this->assign('edit',$edit[0]);
        $this->theme('Wln')->display('TopicPaper/add');
    }

    /**
     * 状态切换
     * @author demo
     */
    public function changeStatus(){
        $status=$_POST['status'];
        $paperID=$_POST['paperID'];
        if($status==1){
            $data['Status']=2;
        }else{
            $data['Status']=1;
        }
        if($this->getModel('TopicPaper')->updateData(
            $data,
            'TopicPaperID='.$paperID)==false){
            $this->setError('30311');
        }else{
            $this->adminLog($this->moduleName, '修改试卷TopicPaperID为【' . $paperID . '】的状态【' . ($data['Status'] == 1 ? '正常' : '锁定') . '】');
            $this->setBack('状态切换成功');
        };
    }
    /**
     * 保存试卷
     * @author demo
     */
    public function save() {
        $topicPaperID = $_POST['TopicPaperID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($topicPaperID) && $act == 'edit') {
            $this->setError('30301'); //数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223'); //模板标识不能为空！
        }
        $_POST['TopicPaperName']=trim($_POST['TopicPaperName']);
        if (empty($_POST['TopicPaperName'])){
            $this->setError('50006'); //专题试卷名称不能为空！
        }
        if (empty($_POST['PaperType'])){
            $this->setError('50007');//试卷类型不能为空！
        }
        if (empty($_POST['TopicID'])){
            $this->setError('50008');//所属专题不能为空！
        }
        //获取字段
        $data = array ();
        if($_POST['PaperType']==1){
            if(!is_numeric($_POST['DocID'])){
                $this->setError('50009');//文档编号不能为非数字
            }elseif($_POST['DocID']==0){
                $this->setError('50010');//文档编号不能为0
            }
            $data['DocID']=$_POST['DocID'];
            $data['TestIDs']='';
        }else if($_POST['PaperType']==2){
            if(empty($_POST['TestIDs'])){
                $this->setError('50011');//试题编号不能为空
            }
            $data['TestIDs']=$_POST['TestIDs'];
            $data['DocID']='0';
        }
        if(empty($_POST['SubjectID'])){
            $this->setError('30730');//请选择学科
        }
        $data['TopicPaperName'] = $_POST['TopicPaperName'];
        $data['TopicPaperDesc'] = trim($_POST['TopicPaperDesc']);
        $data['SubjectID'] = $_POST['SubjectID'];
        $data['IfWL'] = $_POST['IfWL'];
        $data['TopicID'] = $_POST['TopicID'];
        $data['PaperType'] = $_POST['PaperType'];
        $data['IfDown'] = $_POST['IfDown'];
        $data['Status'] = $_POST['Status'];
        if ($act == 'add') {
            $userName = $this->getCookieUserName();
//        $userName = cookie(C('WLN_WLN_USER_AUTH_KEY'));
            $data['AddUser']=$userName;
            $data['AddTime']=time();
            if($this->getModel('TopicPaper')->insertData(
                $data)==false){
                $this->setError('30310');
            }else{
                //写入日志
                $this->adminLog($this->moduleName, '添加试卷【' . $_POST['TopicPaperName'] . '】');
                $this->showSuccess('添加成功！', __URL__);
            }
        } else if ($act == 'edit') {
                if ($this->getModel('TopicPaper')->updateData($data,'TopicPaperID='.$topicPaperID) === false) {
                     $this->setError('30311'); //修改失败！
                } else {
                    //写入日志
                    $this->adminLog($this->moduleName, '修改TopicPaperID为【' . $topicPaperID . '】的数据');
                    $this->showSuccess('修改成功！', __URL__);
                }
            }
    }

    /**
     * 删除试卷
     * @author demo
     */
    public function delete() {
        $topicPaperModel = $this->getModel('TopicPaper');
        $topicPaperID = $_POST['id']; //获取数据标识
        if (!$topicPaperID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $topicPaperData = $topicPaperModel->selectData(
            'TestTimes',
            'TopicPaperID in ( '.$topicPaperID.' )'
        );
        foreach($topicPaperData as $i=>$iTopicPaperData){
            if($iTopicPaperData['TestTimes']>0){
                $this->setError('30712');
            }
        }
        if ($topicPaperModel->deleteData(
                'TopicPaperID in ('.$topicPaperID.')') === false) {
            $this->setError('30302'); //删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName, '删除试卷TopicPaperID为【' . $topicPaperID . '】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }

}
