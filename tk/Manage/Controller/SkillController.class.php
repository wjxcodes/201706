<?php
/**
 * @author demo
 * @date 2014年11月12日
 */
/**
 * 技能配置类，管理技能数据
 */
namespace Manage\Controller;
class SkillController extends BaseController  {
    var $moduleName = '技能配置';
    /**
     * 浏览技能列表
     * @author demo
     */
    public function index() {
        $pageName = '技能管理';
        $subjectArray = SS('subjectParentId');; //获取学科数据集
        $map = array();
        $data = ' 1=1 ';
        if($this->ifSubject && $this->mySubject){
            $data .= ' AND SubjectID in ('.$this->mySubject.')';
        }
        if($_REQUEST['name']){
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data.=' AND SkillName like "%'.$_REQUEST['name'].'%" ';
        }else{
            //高级查询
            $capacityName=$_REQUEST['SkillName'];
            if($capacityName){
                $map['SkillName'] = $capacityName;
                $data .= ' AND SkillName like "%'.$capacityName.'%" ';
            }
            $tt=$_REQUEST['SkillID'];
            if(!empty($tt)){
                $tt = $tt;
                if(!is_array($tt)) $tt = array($tt);
                $tt = array_filter($tt);
                if(!empty($tt[count($tt)-1])){
                    $map['SkillID'] = $tt[count($tt)-1];
                    $data .= ' AND PID ="'.$tt[count($tt)-1].'" ';
                }
            }
            if($_REQUEST['SubjectID']){
                if($this->ifSubject && $this->mySubject){
                    if(!in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                        $this->setError('30712');//您不能搜索非所属学科技能！

                    }
                }
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $data .= ' AND SubjectID = "'.$_REQUEST['SubjectID'].'" ';
            }
        }
        $perpage=C('WLN_PERPAGE');
        $capacity=SM('Skill');
        $count = $capacity->selectCount(
            $data,
            'SkillID',
            'a');// 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page = page($count,$_GET['p'],$perpage);
        $page =$page.','.$perpage;
        $list = $capacity->pageData('*',$data,'SkillID Desc',$page);//获取技能数据集
        $subject=SS('subject');
        foreach($list as $i=>$iList){
            $list[$i]['SubjectName']=$subject[$iList['SubjectID']]['ParentName'].$subject[$iList['SubjectID']]['SubjectName'];
        }
        $this->pageList($count,$perpage,$map);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加技能
     * @author demo
     */
    public function add() {
        $pageName = '添加技能';
        $act = 'add'; //模板标识
        $subjectArray = SS('subjectParentId'); //父类数据集
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑技能
     * @author demo
     */
    public function edit() {
        $capacityID = $_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($capacityID)){
            $this->setError('30301');//数据标识不能为空！
        }
        $pageName = '编辑技能';
        $act = 'edit'; //模板标识
        $capacity=SM('Skill');
        $edit = $capacity->selectData(
            '*',
            'SkillID='.$capacityID,
            '',
            '1');
        //验证权限
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712');//您不能修改非本学科技能！
            }
        }
        //学科数据集
        $subjectArray = SS('subjectParentId');;

        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('pageName', $pageName);
        $this->display('Skill/add');
    }
    /**
     * 保存技能数据
     * @author demo
     */
    public function save() {
        $capacityID = $_POST['SkillID'];//获取数据标识
        $act = $_POST['act'];//获取模板标识
        //判断数据标识
        if(empty($capacityID) && $act == 'edit'){
            $this->setError('30301');//数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223');//模板标识不能为空！
        }
        $data = array();
        $data['PID'] = $_POST['PID'];
        $data['SubjectID'] = $_POST['SubjectID'];
        $data['SkillName'] = formatString('changeStr2Html',$_POST['SkillName']);
        $data['OrderID'] = $_POST['OrderID'];
        $capacity=SM('Skill');
        if($data['PID']){
            $buffer = $capacity->selectData(
                '*',
                'SkillID='.$data['PID'],
                '',
                '1');
            if($buffer){
                $data['SubjectID'] = $buffer[0]['SubjectID'];
            }
        }
        if($act=='add'){
            if($this->ifSubject && $this->mySubject){
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能添加非所属学科技能！
                }
            }
            if($capacity->insertData(
                    $data)===false){
                $this->setError('30310');//添加技能失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加技能【'.$_POST['SkillName'].'】');
                $this->showSuccess('添加技能成功！',__URL__);
            }
        }else if($act=='edit'){
            $subject = $capacity->selectData(
                'SubjectID',
                'SkillID='.$capacityID);
            if($this->ifSubject && $this->mySubject){
                if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能编辑非所属学科技能！
                }elseif(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能编辑为非所属学科技能！
                }
            }
            $data['SkillID']=$capacityID;
            if($capacityID == $data['PID']){
                $this->setError('30311');//修改技能失败，分类不能属于自己！
            }
            if($this->ifSubject && $this->mySubject){
                $buffer = $capacity->selectData(
                    '*',
                    'SkillID='.$capacityID);
                if(!in_array($buffer[0]['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能修改非本学科技能！
                }
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能本学科技能修改到非本学科！
                }
            }
            if($capacity->updateData(
                    $data,
                    'SkillID='.$capacityID)===false){
                $this->setError('30310');//修改技能失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改SkillID为【'.$_POST['SkillID'].'】的数据');
                $this->showSuccess('修改技能成功！',__URL__);
            }
        }
    }
    /**
     * 删除技能
     * @author demo
     */
    public function delete(){
        $capacityID = $_POST['id'];//获取数据标识
        if(!$capacityID){
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        $capacity=SM('Skill');
        if($this->ifSubject && $this->mySubject){
            $buffer = $capacity->selectData(
                '*',
                'SkillID in ('.$capacityID.')');
            foreach($buffer as $i=>$iBuffer){
                if(!in_array($iBuffer['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30507');//您不能删除非本学科技能！
                }
            }
        }
        if($capacity->deleteData(
                'SkillID in ('.$capacityID.')') === false){
            $this->setError('30302');//删除技能失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除SkillID为【'.$capacityID.'】的数据');
            $this->showSuccess('删除技能成功！',__URL__);
        }
    }
    /**
     * 更新技能缓存
     * @author demo
     */
    public function updateCache(){
        $capacity=SM('Skill');
        $capacity->setcache();
        //写入日志
        $this->adminLog($this->moduleName,'更新技能缓存');
        $this->showSuccess('更新成功',__URL__);
    }
    /**
     * 获取技能
     * @author demo
     */
    public function getzsd(){
        $subjectID = $_GET['s'];
        $capacityID = $_GET['c'];
        if(!$capacityID) $capacityID = 0;
        $capacity=SM('Skill');
        $buffer = $capacity->getArrList($subjectID);
        $this->setBack($capacity->setoption($buffer,$capacityID));
    }
}