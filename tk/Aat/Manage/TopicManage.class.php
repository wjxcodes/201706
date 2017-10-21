<?php
/**
 * @author demo
 * @date 2015年5月12日
 */
/**
 * 专题操作相关
 */
namespace Aat\Manage;
class TopicManage extends BaseController  {
    var $moduleName='专题管理';
    /**
     * 专题列表
     * @author demo
     */
    public function index() {
        $topicModel = $this->getModel('Topic');
        $pageName = '专题管理';
        //获取检错条件
        $map = array ();
        $data = ' 1=1 ';
        if ($_REQUEST['name']) {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND TopicName like "%' . $_REQUEST['name'] . '%" ';
        } else {
            //高级查询
            if ($_REQUEST['AddUser']) {
                $map['AddUser'] = $_REQUEST['AddUser'];
                $data .= ' AND AddUser like "%' . $_REQUEST['AddUser'] . '%" ';
            }
            if ($_REQUEST['Type']) {
                $map['Type'] = $_REQUEST['Type'];
                $data .= ' AND Type= "' .$_REQUEST['Type']. '"';
            }
            if (isset($_REQUEST['Status']) && $_REQUEST['Status']!='') {
                $map['Status'] = $_REQUEST['Status'];
                $data .= ' AND Status=' . $_REQUEST['Status'];
            }
        }
        $perpage = C('WLN_PERPAGE'); //每页显示数
        $count = $topicModel->selectCount(
            $data,
            'TopicID'); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=(isset ($_GET['p']) ? $_GET['p'] : 1) . ',' . $perpage;
        $list=$topicModel->pageData(
            'TopicID,TopicName,TopicDesc,SubjectID,Status,Type,StartTime,EndTime,AddUser,AddTime',
            $data,
            'TopicID desc',
            $page
        );
//        $subject = SS('subject');
        $subject = $this->getApiCommon('Subject/subject');
//        $subjectArray=SS('subjectParentId');
        $subjectArray = $this->getApiCommon('Subject/subjectParentID');
        foreach($list as $i=>$iList){
            if($iList['SubjectID']){
                $list[$i]['SubjectName']=$subject[$list[$i]['SubjectID']]['ParentName'].$subject[$list[$i]['SubjectID']]['SubjectName'];
            }else{
                $list[$i]['SubjectName']='不限';
            }
        }
        $this->pageList($count, $perpage, $map);

        $this->assign('pageName',$pageName);
        $this->assign('list',$list);
        $this->assign('subjectArray',$subjectArray);
        $this->theme('Wln')->display();
    }
    /**
     * 新增专题
     * @author demo
     */
    public function add() {
        $pageName = '添加专题';
        $act = 'add'; //模板标识

        //获取学科数据集
//        $subjectArray = SS('subjectParentId');//学科信息
        $subjectArray = $this->getApiCommon('Subject/subjectParentID');
        $time=time();
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('pageName',$pageName);
        $this->assign('time',$time);//默认当前时间
        $this->assign('subjectArray', $subjectArray);
        $this->theme('Wln')->display();
    }
    /**
     * 编辑专题
     * @author demo
     */
    public function edit() {
        $topicID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($topicID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑专题';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('Topic')->selectData(
            '*',
            'TopicID=' . $topicID);
        $topicPaper=$this->getModel('TopicPaper')->selectData(
            'TopicPaperID',
            'TopicID='.$topicID
        );
        $hasPaper=0;
        if(count($topicPaper)>0){
            $hasPaper=1;
        }
//        $subjectArray=SS('subjectParentId');
        $subjectArray = $this->getApiCommon('Subject/subjectParentID');
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('hasPaper',$hasPaper);//专题下是否包含试卷
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName);
        $this->theme('Wln')->display('Topic/add');
    }
    /**
     * 保存专题
     * @author demo
     */
    public function save() {
        $topicID = $_POST['TopicID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($topicID) && $act == 'edit') {
            $this->setError('30301'); //数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223'); //模板标识不能为空！
        }
        $_POST['TopicName']=trim($_POST['TopicName']);
        if (empty($_POST['TopicName'])){
            $this->setError('50013');//专题名称不能为空！
        }
        if (empty($_POST['Type'])){
            $this->setError('50014');//专题分类不能为空
        }
        //获取字段
        $data = array ();
        $data['TopicName'] = $_POST['TopicName'];
        $data['TopicDesc'] = trim($_POST['TopicDesc']);
        $data['EvaluateDescription'] = trim($_POST['EvaluateDescription']);
        $data['AnswerTitle'] = trim($_POST['AnswerTitle']);
        $data['JumpUrl'] = trim($_POST['JumpUrl']);
        if($_POST['SubjectID']){
            $data['SubjectID'] = $_POST['SubjectID'];
        }else{
            $data['SubjectID'] = 0;
        }

        $data['Type'] = $_POST['Type'];
        if(checkString('isDate',$_POST['StartTime'])){
            $data['StartTime'] = strtotime($_POST['StartTime']);
        }
        if(checkString('isDate',$_POST['EndTime'])){
            $data['EndTime'] = strtotime($_POST['EndTime']);
        }
        $data['Status']=$_POST['Status'];
        if ($act == 'add') {
            $userName = $this->getCookieUserName();
//        $userName = cookie(C('WLN_WLN_USER_AUTH_KEY'));
            $data['AddUser']=$userName;
            $data['AddTime']=time();
            if($this->getModel('Topic')->insertData(
                $data)==false){
                $this->setError('30310');//添加失败
            }else{
                //写入日志
                $this->adminLog($this->moduleName, '添加专题【' . $_POST['TopicName'] . '】');
                $this->showSuccess('添加成功！', __URL__);
            }
        } else if ($act == 'edit') {
                if ($this->getModel('Topic')->updateData($data,'TopicID='.$topicID) === false) {
                     $this->setError('30311'); //修改失败！
                } else {
                    //写入日志
                    $this->adminLog($this->moduleName, '修改专题TopicID为【' . $_POST['TopicID'] . '】的数据');
                    $this->showSuccess('修改成功！', __URL__);
                }
            }
    }
    /**
     * 删除专题
     * @author demo
     */
    public function delete() {
        $topicID = $_POST['id']; //获取数据标识
        if (!$topicID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $topicPaperData = $this->getModel('TopicPaper')->selectData(
            'TopicPaperID',
            'TopicID in ( '.$topicID.' )'
        );
        if($topicPaperData){
            $this->setError('50015');//专题下有试卷不能删除
        }

        if ($this->getModel('Topic')->deleteData(
                'TopicID in ('.$topicID.')') === false) {
            $this->setError('30302'); //删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName, '删除专题TopicID为【' . $topicID . '】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }

}
