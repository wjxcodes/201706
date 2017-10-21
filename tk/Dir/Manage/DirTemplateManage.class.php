<?php
/**
 * @author demo
 * @date 2014年11月13日
 */
/**
 * 组卷模板管理类，用于组卷模板的相关操作
 */
namespace Dir\Manage;
class DirTemplateManage extends BaseController  {
    var $moduleName = '组卷模板';
    /**
     * 组卷模板列表浏览
     * @author demo
     */
    public function index() {
        $pageName = '模板组卷';
        $act = 'add'; //模板标识
        $map = array();
        $data = ' 1 = 1 ';
        $where = ' 1 = 1 ';
        if($this->ifSubject && $this->mySubject){
            $data .= 'AND a.SubjectID in ('.$this->mySubject.')';
        }
        if($_REQUEST['name']){
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND a.TempName like "%'.$_REQUEST['name'].'%" ';
            $where .= ' AND TempName like "%'.$_REQUEST['name'].'%" ';
        }
        $perPage = C('WLN_PERPAGE');
        $dirTemplate=$this->getModel('DirTemplate');
        $count = $dirTemplate->selectCount(
            $where,
            "*"); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=page($count,$_GET['p'],$perPage).','.$perPage;
        $list = $dirTemplate->unionSelect(
            'TemplatePageData',
            $data,
            'a.TempID DESC',
            $page); //获取题型数据集
        $this->pageList($count,$perPage,$map);
        
        if($list){
            $subject = SS('subject');
            foreach($list as $i => $iList){
                $list[$i]['SubjectName'] = $subject[$subject[$iList['SubjectID']]['PID']]['SubjectName'].$subject[$iList['SubjectID']]['SubjectName'];
            }
        }
        
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 添加组卷模板
     * @author demo
     */
    public function add() {
        $pageName = '添加组卷模板';
        $act = 'add'; //模板标识
        //学科数据集
        $subjectArray = SS('subjectParentId'); //获取学科数据集
        //考试类别数据集
        $examtypeArray = SS('examType');
        
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('examtypeArray', $examtypeArray); //考试类别数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 编辑组卷模板
     * @author demo
     */
    public function edit() {
        $tempID = $_GET['id'];//获取数据标识
        //判断数据标识
        if(empty($tempID)){
            $this->setError('30301');//数据标识不能为空！
        }
        $pageName = '编辑组卷模板';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('DirTemplate')->selectData(
            '*',
            'TempID = '.$tempID,
            '',
            1);
   
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712');//您不能修改非所属学科组卷模版
            }
        }
        //学科数据集
        $subjectArray = SS('subjectParentId'); //获取学科数据集
        //考试类别数据集
        $examtypeArray = SS('examType');
        $edit[0]['Content']=print_r(unserialize($edit[0]['Content']),true);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('examtypeArray', $examtypeArray); //考试类别数据集
        $this->assign('pageName', $pageName);
        $this->display('DirTemplate/add');
    }

    /**
     * 保存组卷模板
     * @author demo
     */
    public function save() {
        $tempID = $_POST['tempID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($tempID) && $act == 'edit'){
            $this->setError('30301');//数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223');//模板标识不能为空！
        }
        $data = array();
        $data['TempName'] = $_POST['tempName'];
        $data['TypeID'] = $_POST['typeID'];
        $data['UserName'] = $_POST['userName'];
          $dirTemplate=$this->getModel('DirTemplate');
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
        $data['IfDefault'] = $_POST['ifDefault'];

        // 添加原创模板相关操作  2015-9-8
        if('edit' == $act && 2 == $data['IfDefault']){
            //查询出该组卷模板的Content内容
            $result = $dirTemplate->findData(
                'Content',
                'TempID = '.$tempID
            );
            $result = $result['Content'];
            $ot = new \Yc\Model\OriginalityTemplateModel();
            $result = unserialize($result);
            $result['SubjectID'] = $_POST['subjectID'];
            $result['Admin'] = $this->getCookieUserID();
            $result = $ot->save($result, $_POST['tempName'], $data['TypeID']);
            if($result === false){
                $err = $ot->getErrorMessage();
                if($err){
                    $this->setError($err);
                }
                $this->setError('30307');
            }
            $id = $ot->getId();
            $this->adminLog($this->moduleName,"添加编号【{$id}】的原创模板");
            $this->showSuccess('添加原创模板成功！',__URL__);
        }

        $data['SubjectID'] = $_POST['subjectID'];
        $data['OrderID'] = $_POST['orderID'];
        $data['AddTime'] = time();
        if($act == 'add'){
            if($this->ifSubject && $this->mySubject){
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能添加非所属学科的组卷模版！
                }
            }
            if($dirTemplate->insertData($data) === false){
                $this->setError('30310');//添加模板失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加组卷模板【'.$_POST['typeName'].'】');
                $this->showSuccess('添加组卷模板成功！',__URL__);
            }
        }else if($act == 'edit'){
            $subject = $dirTemplate->selectData(
                'SubjectID',
                'TempID = '.$tempID);
            if($this->ifSubject && $this->mySubject){
                if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能编辑非所属学科组卷模板！
                }elseif(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能编辑为非所属学科组卷模板！
                }
            }
            if($dirTemplate->updateData(
                    $data,
                    'TempID='.$tempID) === false){
                $this->setError('30311');//修改组卷模板失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改TempID为【'.$_POST['tempID'].'】的数据');
                $this->showSuccess('修改组卷模板成功！',__URL__);
            }
        }
    }

    /**
     * 删除组卷模板
     * @author demo 
     */
    public function delete(){
        $tempID = $_POST['id'];    //获取数据标识
        if(!$tempID){
            $this->setError('30301','',__URL__);//数据标识不能为空！
              }
        $dirTemplate=$this->getModel('DirTemplate');
        if($this->ifSubject && $this->mySubject){
            $tempArr = $dirTemplate->selectData(
                'SubjectID',
                'TempID in ('.$tempID.')');
            $subjectArr = explode(',',$this->mySubject);
            foreach($tempArr as $i => $iTempArr){
                if(!in_array($iTempArr['SubjectID'],$subjectArr)){
                    $this->setError('30712');//您不能删除非所属学科组卷模版！
                }
            }
        }
        if($dirTemplate->deleteData(
                'TempID in ('.$tempID.')') === false){
            $this->setError('30302');//删除组卷模板失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除TempID为【'.$tempID.'】的数据');
            $this->showSuccess('删除组卷模板成功！',__URL__);
        }
    }
}