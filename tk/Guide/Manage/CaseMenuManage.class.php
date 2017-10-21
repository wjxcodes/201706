<?php
/**
 * @author demo
 * @date 2015-5-9
 */
/**
 * 导学案栏目配置类，用于导学案栏目的相关操作
 */
namespace Guide\Manage;
class CaseMenuManage extends BaseController  {
    var $moduleName = '导学案栏目配置';
    /**
     * 浏览导学案栏目
     * @author demo
     */
    public function index() {
        $pageName = '导学案栏目管理';
        $map=array();
        $data=' 1=1 ';
            if($this->ifSubject && $this->mySubject){
                $data .= 'AND SubjectID in ('.$this->mySubject.')';
            }
            if($_REQUEST['name']){
                //简单查询
                $map['name']=$_REQUEST['name'];
                $data.=' AND MenuName like "%'.$_REQUEST['name'].'%" ';
            }else{
                //高级查询
                if($_REQUEST['SubjectID']){
                    if($this->ifSubject && $this->mySubject){
                        if(!in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                            $this->setError('30712');//您不能搜索非所属学科内容
                        }
                    }
                    $map['SubjectID']=$_REQUEST['SubjectID'];
                    $data.=' AND SubjectID ="'.$_REQUEST['SubjectID'].'" ';
                }
                if($_REQUEST['ForumID']){
                    $map['ForumID']=$_REQUEST['ForumID'];
                    $data.=' AND ForumID = "'.$_REQUEST['ForumID'].'"';
                }
            }
        $perpage=C('WLN_PERPAGE');
        $caseMenu=$this->getModel('CaseMenu');
        $count = $caseMenu->selectCount(
            $data,
            'MenuID'
        ); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性;
        $page=page($count,$_GET['p'],$perpage).','.$perpage;
        $list = $caseMenu->pageData(
            '*',
            $data,
            'MenuID desc',
            $page); //获取题型数据集
        $this->pageList($count,$perpage,$map);
        $forumArray = $this->getModel('CaseMenu')->getCaseForum();
        if($list){
            $subjectArray=SS('subject');
            foreach($list as $i=>$iList){
                $list[$i]['ForumName']=$forumArray[$iList['ForumID']]['name'];
                $list[$i]['SubjectName']=$subjectArray[$iList['SubjectID']]['ParentName'].$subjectArray[$iList['SubjectID']]['SubjectName'];
            }
        }
        $subject = SS('subjectParentId'); //父类数据集
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('forumArray',$forumArray);//板块数据集
        $this->assign('subjectArray', $subject); //题型数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 添加导学案栏目
     * @author demo
     */
    public function add() {
        $pageName = '添加导学案栏目';
        $act = 'add'; //模板标识
        $subjectArray = SS('subjectParentId');//父类数据集
        $forumArray = $this->getModel('CaseMenu')->getCaseForum();
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('forumArray',$forumArray);//板块数据集
        $this->assign('subjectArray', $subjectArray); //父类数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 编辑导学案栏目
     * @author demo
     */
    public function edit() {
        $MenuID=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($MenuID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑导学案栏目';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('CaseMenu')->selectData(
            '*',
            'MenuID='.$MenuID);
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712'); //您不能编辑非所属学科内容！
            }
        }
        $forumArray = $this->getModel('CaseMenu')->getCaseForum();
        $subjectArray = SS('subjectParentId'); //父类数据集
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('forumArray',$forumArray);//板块数据集
        $this->assign('edit', $edit[0]);
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName);
        $this->display('CaseMenu/add');
    }

    /**
     * 保存导学案栏目
     * @author demo
     */
    public function save() {
        $menuID=$_POST['MenuID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($menuID) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        $data=array();
        if(is_numeric($_POST['OrderID'])){
            $data['OrderID']=$_POST['OrderID'];
        }else{
            $this->setError('23010');//排序不能为非数字
        }
        $data['SubjectID']=$_POST['SubjectID'];
        $data['MenuName']=trim($_POST['MenuName']);
        $data['ForumID']=$_POST['ForumID'];
        $data['IfTest']=$_POST['IfTest'];
        $data['IfAnswer']=$_POST['IfAnswer'];
        $data['NumStyle']=$_POST['NumStyle'];
        if($act=='add'){
            if($this->ifSubject && $this->mySubject){
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712'); // 您不能添加非所属学科内容！
                }
            }
            if($this->getModel('CaseMenu')->insertData(
                    $data)===false){
                $this->setError('30310'); //添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加栏目【'.$_POST['MenuName'].'】');
                $this->showSuccess('添加学案栏目成功！',__URL__);
            }

        }else if($act=='edit'){
            $subject = $this->getModel('CaseMenu')->selectData(
                'SubjectID',
                'menuID='.$menuID);
            if($this->ifSubject && $this->mySubject){
                if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712'); //您不能编辑非所属学科内容！
                }elseif(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712'); //您不能编辑为非所属学科内容！
                }
            }
            $data['MenuID']=$menuID;
            if($this->getModel('CaseMenu')->updateData(
                    $data,
                    'MenuID='.$menuID
                )===false){
                $this->setError('30311'); //修改失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改MenuID为【'.$_POST['MenuID'].'】的数据');
                $this->showSuccess('修改学案栏目成功！',__URL__);
            }
        }
    }

    /**
     * 删除导学案栏目
     * @author demo
     */
    public function delete(){
        $menuID=$_POST['id'];    //获取数据标识
        if(!$menuID){
            $this->setError('30301','',__URL__,'');//数据标识不能为空！
        }
        $caseMenu = $this->getModel('CaseMenu');
        if($this->ifSubject && $this->mySubject){
            $menuData = $this->getModel('CaseMenu')->selectData(
                'SubjectID',
                'MenuID in ('.$menuID.')');
            foreach($menuData as $i=>$iMenuData){
                if(!in_array($iMenuData['SubjectID'],explode(',',$this->mySubject))){
                     $this->setError('30507','',__URL__,''); //您没有权限删除非所属学科内容！
                }
            }
        }
        if($this->getModel('CaseMenu')->deleteData(
                'MenuID in ('.$menuID.')')===false){
            $this->setError('30302','',__URL__,''); //删除失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除MenuID为【'.$menuID.'】的数据');
            $this->showSuccess('删除学案栏目成功！',__URL__);
        }
    }

    /**
     * 更新导学案栏目缓存
     * @author demo
     */
    public function updateCache(){
        $menu=$this->getModel('CaseMenu');
        $menu->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }
}