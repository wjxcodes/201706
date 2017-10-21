<?php
/**
 * @author demo
 * @date 15-5-4 下午4:29
 * @update 15-5-4 下午4:29
 */
/**
 * 导学案模板管理类，用于导学案模板的操作
 */
namespace Guide\Manage;
class CaseTplManage extends BaseController {
    var $moduleName = '导学案模板';
    /**
     * 导学案模板列表
     * @author demo
     */
    public function index(){
        $pageName="导学案模板列表";
        $map=array();
        $data = ' 1 = 1 ';
        if($this->ifSubject && $this->mySubject){
            $data .= 'AND SubjectID in ('.$this->mySubject.')';
        }
        if($_REQUEST['name']){
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND TempName like "%'.$_REQUEST['name'].'%" ';
        }else {
            //高级查询
            if ($_REQUEST['UserName']) {
                $map['UserName'] = $_REQUEST['UserName'];
                $data .= ' AND UserName = "' . $_REQUEST['UserName'] .'"';
            }
            if ($_REQUEST['TempName']) {
                $map['TempName'] = $_REQUEST['TempName'];
                $data .= ' AND TempName = "' . $_REQUEST['TempName'] .'"';
            }
            if (is_numeric($_REQUEST['SubjectID']) && $_REQUEST['SubjectID']!=0) {
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $data .= ' AND SubjectID ="' . $_REQUEST['SubjectID'] . '" ';
            }
            if (is_numeric($_REQUEST['IfSystem'])) {
                $map['IfSystem'] = $_REQUEST['IfSystem'];
                $data .= ' AND IfSystem ="' . $_REQUEST['IfSystem'] . '" ';
            }

        }
        $perPage = C('WLN_PERPAGE');
        $caseTpl = $this->getModel('CaseTpl');
        $count = $caseTpl->selectCount(
            $data,
            "*"); // 查询满足要求的总记录数
        $page=page($count,$_GET['p'],$perPage).','.$perPage;
        $list = $caseTpl->pageData(
            '*',
            $data,
            'TplID desc',
            $page
        );
        $this->pageList($count,$perPage,$map);//载入分页
        $subjectArray=SS('subject');
        $parent=SS('chapterParentPath');// 获取章节路径防止重复调用
        $self=SS('chapterList');

        $param=array();
        $param['style']='chapterList';
        $param['parent']=$parent;
        $param['self']=$self;
        foreach($list as $i=>$iList){
            $list[$i]['SubjectName']=$subjectArray[$iList['SubjectID']]['ParentName'].$subjectArray[$iList['SubjectID']]['SubjectName'];
            $param['ID']=$iList['ChapterID'];
            $chapterArray=$this->getData($param);
            $list[$i]['ChapterName']=$chapterArray[0]['ChapterName'];
        }
        $subjectList = SS('subjectParentId');; //获取学科数据集
        $this->assign('pageName',$pageName);
        $this->assign('subjectArray',$subjectList);
        $this->assign('list',$list);
        $this->display();
    }

    /**
     * 编辑导学案模板
     * @author demo
     */
    public function edit() {
        $tempID = $_GET['id'];//获取数据标识
        //判断数据标识
        if(empty($tempID)){
            $this->setError('30301');//数据标识不能为空！
        }
        $pageName = '编辑导学案模板';
        $caseTpl=$this->getModel('CaseTpl');
        $edit = $caseTpl->selectData(
            '*',
            'TplID = '.$tempID
            );
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712',1);//您不能修改非所属学科导学案模版
            }
        }
        $subjectArray=SS('subject');
        $edit[0]['SubjectName']=$subjectArray[$edit[0]['SubjectID']]['ParentName'].$subjectArray[$edit[0]['SubjectID']]['SubjectName'];
        $param['style']='chapterList';
        $param['ID']=$edit[0]['ChapterID'];
        $chapterArray=$this->getData($param);
        $edit[0]['ChapterName']=$chapterArray[0]['ChapterName'];
        $edit[0]['Content']=unserialize($edit[0]['Content']);
        $edit[0]['Content']=$caseTpl->formatContent($edit[0]['Content']);//格式化模板内容
        $subjectList = SS('subjectParentId');; //获取学科数据集
        $this->assign('subjectArray',$subjectList);
        /*载入模板标签*/
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display();
    }

    /**
     * 列表页模板状态切换方法
     * @author demo
     */
    public function replace(){
        $data = array ();
        $tplID = $_POST['wid']; //获取数据标识
        $status = $_POST['status'];
/*        $tplIDArray = explode(',',$tplID);
        $statusArray = explode(',',$status);*/
        $data['IfSystem']=1;
        if($status[0]==1){
            $data['IfSystem']=0;
        }
        if (!$tplID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if ($this->getModel('CaseTpl')->updateData(
                $data,
                'TplID in (' . $tplID . ')'
            ) === false) {
            $this->setError('30824'); //更改状态失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName, '修改导学案模板编号为【' . $tplID . '】的状态【' . ($data['Status'] == 1 ? '系统' : '个人') . '】');
            $this->setBack('更改状态成功！');
        }
    }


    /**
     * 保存导学案模板
     * @author demo
     */
    public function save(){
        $tempID = $_POST['TplID'];    //获取数据标识
        //判断数据标识
        if(empty($tempID)){
            $this->setError('30301');//数据标识不能为空！
        }
        $data=array();
        $data['TempName']=$_POST['TempName'];
        $data['IfSystem']=$_POST['IfSystem'];
        $data['OrderID']=$_POST['OrderID'];
        $data['UserName']=$_POST['UserName'];
        $data['SubjectID']=$_POST['SubjectID'];
        //如果填写账户
        if($data['UserName'] != 0){
            //判断username是否存在
            $buffer = $this->getModel('User')->selectData(
                'UserID',
                'UserName = "'.$data['UserName'].'" and Status = 0 and Whois = 1');
            if(!$buffer){
                $this->setError('30214');//您填写的账户不存在，或者不是教师账户！
            }
        }
        $subject = $this->getModel('CaseTpl')->selectData(
            'SubjectID',
            'TplID = '.$tempID);
        if($this->ifSubject && $this->mySubject){
            if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712');//您不能修改非所属学科导学案模板！
            }
        }
        if($this->getModel('CaseTpl')->updateData(
                $data,
                'TplID='.$tempID
            ) === false){
            $this->setError('30311');//修改导学案模板失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'修改TplID为【'.$_POST['TplID'].'】的数据');
            $this->showSuccess('修改导学案模板成功！',__URL__);
        }
    }

    /**
     * 导学案模板删除
     * @author demo
     */
    public function delete(){
        $tplID = $_POST['id'];//获取数据标识
        if(!$tplID){
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        $caseTpl=$this->getModel('CaseTpl');
        if($this->ifSubject && $this->mySubject){
            $buffer = $caseTpl->selectData(
                'SubjectID',
                'TplID in ('.$tplID.')'
            );
            foreach($buffer as $i=>$iBuffer){
                if(!in_array($iBuffer['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712',0); //您没有权限删除非所属学科模板！
                }
            }
        }
        if($this->getModel('CaseTpl')->deleteData(
                'TplID in ('.$tplID.')'
            ) === false){
            $this->setError('23011');//删除导学案模板失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除TplID为【'.$tplID.'】的数据');
            $this->showSuccess('删除导学案模板成功！',__URL__);
        }
    }
}