<?php
/**
 * @author demo
 * @date 2014年11月12日
 */
/**
 * 知识点配置类，管理知识点数据
 */
namespace Manage\Controller;
class KnowledgeController extends BaseController  {
    var $moduleName = '知识点配置';
    /**
     * 浏览知识点列表
     * @author demo
     */
    public function index() {
        $pageName = '知识点管理';
        $subjectArray = SS('subjectParentId');; //获取学科数据集
        $map = array();
        $data = ' 1=1 ';
        if($this->ifSubject && $this->mySubject){
            $data .= ' AND SubjectID in ('.$this->mySubject.')';
        }
        if($_REQUEST['name']){
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data.=' AND KlName like "%'.$_REQUEST['name'].'%" ';
        }else{
            //高级查询
            if($_REQUEST['KlName']){
                $map['KlName'] = $_REQUEST['KlName'];
                $data .= ' AND KlName like "%'.$_REQUEST['KlName'].'%" ';
            }
            if(!empty($_REQUEST['knowledge'])){
                $tt = $_REQUEST['knowledge'];
                if(!is_array($tt)) $tt = array($tt);
                $tt = array_filter($tt);
                if(!empty($tt[count($tt)-1])){
                    $map['knowledge'] = $tt[count($tt)-1];
                    $data .= ' AND PID ="'.$tt[count($tt)-1].'" ';
                }
            } 
            if($_REQUEST['SubjectID']){
                if($this->ifSubject && $this->mySubject){
                    if(!in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                        $this->setError('30712');//您不能搜索非所属学科知识点！
                        
                    }
                }
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $data .= ' AND SubjectID = "'.$_REQUEST['SubjectID'].'" ';
            } 
        }
        $perpage=C('WLN_PERPAGE');
        $count = $this->getModel('Knowledge')->selectCount(
            $data,
            'KlID',
            'a');// 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page = page($count,$_GET['p'],$perpage);
        $page =$page.','.$perpage;
        $list = $this->getModel('Knowledge')->pageData('*',$data,'KlID Desc',$page);//获取知识点数据集
        $subject=SS('subject');
        foreach($list as $i=>$iList){
            $list[$i]['SubjectName']=$subject[$iList['SubjectID']]['ParentName'].$subject[$iList['SubjectID']]['SubjectName'];
        }
        if(!$list) $this->setError('10122',0);//没有子类了！
        $this->pageList($count,$perpage,$map);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加知识点
     * @author demo
     */
    public function add() {
        $pageName = '添加知识点';
        $act = 'add'; //模板标识
        $subjectArray = SS('subjectParentId'); //父类数据集
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑知识点
     * @author demo
     */
    public function edit() {
        $knowledgeID = $_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($knowledgeID)){
            $this->setError('30301');//数据标识不能为空！
        }
        $pageName = '编辑知识点';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('Knowledge')->selectData(
            '*',
            'KlID='.$knowledgeID,
            '',
            '1');
        //验证权限
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712');//您不能修改非本学科知识点！
            }
        }
        //学科数据集
        $subjectArray = SS('subjectParentId');;

        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('pageName', $pageName);
        $this->display('Knowledge/add');
    }
    /**
     * 保存知识点数据
     * @author demo
     */
    public function save() {
        $knowledgeID = $_POST['KlID'];//获取数据标识
        $act = $_POST['act'];//获取模板标识
        //判断数据标识
        if(empty($knowledgeID) && $act == 'edit'){
            $this->setError('30301');//数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223');//模板标识不能为空！
        }
        $data = array();
        $data['PID'] = $_POST['PID'];
        $data['SubjectID'] = $_POST['SubjectID'];
        $data['KlName'] = formatString('changeStr2Html',$_POST['KlName']);
        $data['Frequency'] = $_POST['Frequency'];
        $data['IfTest'] = $_POST['IfTest'];
        $data['Style'] = $_POST['Style'];
        $data['OrderID'] = $_POST['OrderID'];
        $data['IfInChoose'] = $_POST['IfInChoose'];
        if($data['PID']){
            $buffer = $this->getModel('Knowledge')->selectData(
                '*',
                'KlID='.$data['PID'],
                '',
                '1');
            if($buffer){
                $data['SubjectID'] = $buffer[0]['SubjectID'];
            }
        }
        if($act=='add'){
            if($this->ifSubject && $this->mySubject){
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能添加非所属学科知识点！
                }
            }
            if($this->getModel('Knowledge')->insertData(
                    $data)===false){
                $this->setError('30310');//添加知识点失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加知识点【'.$_POST['KlName'].'】');
                $this->showSuccess('添加知识点成功！',__URL__);
            }
        }else if($act=='edit'){
            $subject = $this->getModel('Knowledge')->selectData(
                'SubjectID',
                'KlID='.$knowledgeID);
            if($this->ifSubject && $this->mySubject){
                if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能编辑非所属学科知识点！
                }elseif(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能编辑为非所属学科知识点！
                }
            }
            $data['KlID']=$knowledgeID;
            if($knowledgeID == $data['PID']){
                $this->setError('30311');//修改知识点失败，分类不能属于自己！
            }
            if($this->ifSubject && $this->mySubject){
                $buffer = $this->getModel('Knowledge')->selectData(
                    '*',
                    'KlID='.$knowledgeID);
                if(!in_array($buffer[0]['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能修改非本学科知识点！
                }
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能本学科知识点修改到非本学科！
                }
            }
            if($this->getModel('Knowledge')->updateData(
                    $data,
                    'KlID='.$knowledgeID)===false){
                $this->setError('30310');//修改知识点失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改KnowledgeID为【'.$_POST['KlID'].'】的数据');
                $this->showSuccess('修改知识点成功！',__URL__);
            }
        }
    }
    /**
     * 删除知识点
     * @author demo
     */
    public function delete(){
        $knowledgeID = $_POST['id'];//获取数据标识
        if(!$knowledgeID){
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        if($this->ifSubject && $this->mySubject){
            $buffer = $this->getModel('Knowledge')->selectData(
                '*',
                'KlID in ('.$knowledgeID.')');
            foreach($buffer as $i=>$iBuffer){
                if(!in_array($iBuffer['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30507');//您不能删除非本学科知识点！
                }
            }
        }
        if($this->getModel('Knowledge')->deleteData(
                'KlID in ('.$knowledgeID.')') === false){
            $this->setError('30302');//删除知识点失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除KnowledgeID为【'.$knowledgeID.'】的数据');
            $this->showSuccess('删除知识点成功！',__URL__);
        }
    }
    /**
     * 更新知识点缓存
     * @author demo
     */
    public function updateCache(){
        $knowledge=$this->getModel('Knowledge');
        $knowledge->setcache();
        //写入日志
        $this->adminLog($this->moduleName,'更新知识点缓存');
        $this->showSuccess('更新成功',__URL__);
    }
    /**
     * 获取知识点
     * @author demo
     */
    public function getzsd(){
        $subjectID = $_GET['s'];
        $knowledgeID = $_GET['z'];
        if(!$knowledgeID) $knowledgeID = 0;
        $knowledge = $this->getModel('Knowledge');
        $buffer = $knowledge->getArrList($subjectID);
        $this->setBack($knowledge->setoption($buffer,$knowledgeID));
    }
}