<?php
/**
 * @author demo
 * @update 2015年1月23日
 */
/**
 * 专题配置类，用于管理专题数据的相关操作
 */
namespace Index\Manage;
class WlnSpecialManage extends BaseController  {
    var $moduleName = '专题配置';
    /**
     * 浏览专题列表
     * @author demo
     */
    public function index() {
        $pageName = '专题管理';
        $subjectArray = SS('subjectParentId');//获取学科数据集
        $specialArray = SS('specialParent'); //获取数据集specialTree
        $map=array();
        $data=' 1=1 ';
        if($this->ifSubject && $this->mySubject){
            $data .= 'AND a.SubjectID in ('.$this->mySubject.')';
        }
        if($_REQUEST['name']){
            //简单查询
            $map['name']=$_REQUEST['name'];
            $data.=' AND a.SpecialName like "%'.$_REQUEST['name'].'%" ';
        }else{
            //高级查询
            if($_REQUEST['SpecialName']){
                $map['SpecialName']=$_REQUEST['KlName'];
                $data.=' AND a.SpecialName like "%'.$_REQUEST['SpecialName'].'%" ';
            }
            if($_REQUEST['SubjectID']){
                if($this->ifSubject && $this->mySubject){
                    if(!in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                        $this->setError('30712');//您不能搜索非所属学科专题！
                    }
                }
                $map['SubjectID']=$_REQUEST['SubjectID'];
                $data.=' AND a.SubjectID ="'.$_REQUEST['SubjectID'].'" ';
            } 
            if($_REQUEST['PID']){
                $map['PID']=$_REQUEST['PID'];
                $data.=' AND a.PID ="'.$_REQUEST['PID'].'" ';
            } 
        } 
        $perpage=C('WLN_PERPAGE');
        $count = $this->getModel('Special')->selectCount(
            $data,
            'SpecialID',
            'a'
        );
        $page = page($count,$_GET['p'],$perpage);
        $page=$page.','.$perpage;
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = D('Base')->unionSelect('specialPageData',$data,$page);//获取专题数据集
        $subject=SS('subject');
        foreach($list as $i=>$iList){
            $list[$i]['SubjectName']=$subject[$iList['SubjectID']]['ParentName'].$subject[$iList['SubjectID']]['SubjectName'];
        }
        $this->pageList($count,$perpage,$map);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('specialArray', $specialArray); //专题数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加专题
     * @author demo
     */
    public function add() {
        $pageName = '添加专题';
        $act = 'add'; //模板标识
        $subjectArray = SS('subjectParentId');//获取学科数据集
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑专题信息
     * @author demo
     */
    public function edit() {
        $SpecialID=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($SpecialID)){
            $this->setError('30301');//数据标识不能为空！
        }
        $pageName = '编辑专题';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('Special')->selectData(
            '*',
            'SpecialID='.$SpecialID,
            '',
            '1'
        );
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712');//您不能编辑非所属学科专题！
            }
        }
        $subjectArray = SS('subjectParentId');//获取学科数据集
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('pageName', $pageName);
        $this->display('WlnSpecial/add');
    }
    /**
     * 保存专题数据
     * @author demo
     */
    public function save() {
        $SpecialID = $_POST['SpecialID'];//获取数据标识
        $act = $_POST['act'];//获取模板标识
        //判断数据标识
        if(empty($SpecialID) && $act == 'edit'){
            $this->setError('30301');//数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223');//模板标识不能为空！
        }
        $data = array();
        $data['PID'] = $_POST['PID'];
        $data['SubjectID'] = $_POST['SubjectID'];
        $data['SpecialName'] = formatString('changeStr2Html',$_POST['SpecialName']);
        $data['OrderID'] = $_POST['OrderID'];
        $specialObj = $this->getModel('Special');
        if($data['PID']){
            $buffer = $specialObj->selectData(
                '*',
                'SpecialID='.$data['PID'],
                '',
                '1');
            if($buffer){
                $data['SubjectID'] = $buffer[0]['SubjectID'];
            }
        }
        if($act == 'add'){
            if($this->ifSubject && $this->mySubject){
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能添加非所属学科专题！
                }
            }
            if($specialObj->insertData(
                    $data)===false){
                $this->setError('30310');//添加专题失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加专题【'.$_POST['SpecialName'].'】');
                $this->showSuccess('添加专题成功！',__URL__);
            }
        }else if($act=='edit'){
            $subject = $specialObj->selectData(
                'SubjectID',
                'SpecialID='.$SpecialID);
            if($this->ifSubject && $this->mySubject){
                if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能编辑非所属学科专题！
                }
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能编辑为非所属学科专题！
                }
            }
            $data['SpecialID']=$SpecialID;
            if($SpecialID==$data['PID']){
                $this->setError('30311');//修改专题失败，不能属于自己！
            }
            
            if($specialObj->updateData(
                    $data,
                    'SpecialID='.$SpecialID)===false){
                $this->setError('30311');//修改专题失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改SpecialID为【'.$_POST['SpecialID'].'】的数据');
                $this->showSuccess('修改专题成功！',__URL__);
            }
        }
    }

    /**
     * 删除专题信息
     * @author demo
     */
    public function delete(){
        $SpecialID=$_POST['id'];//获取数据标识
        if(!$SpecialID){
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        $specialObj =  $this->getModel('Special');
        if($this->ifSubject && $this->mySubject){
            $specialData = $specialObj->selectData(
                'SubjectID',
                'SpecialID in ('.$SpecialID.')');
            foreach($specialData as $i=>$iSpecialData){
                if(!in_array($iSpecialData['SubjectID'],explode(',',$this->mySubject))){
                     $this->setError('30507');//您不能删除非所属学科专题！
                }
            }
        }
        if($specialObj->deleteData(
                'SpecialID in ('.$SpecialID.')')===false){
             $this->setError('30302');//删除专题失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除SpecialID为【'.$SpecialID.'】的数据');
            $this->showSuccess('删除专题成功！',__URL__);
        }
    }
    /**
     * 更新专题缓存
     * @author demo
     */
    public function updateCache(){
        $data=array();
        $specialObj =  $this->getModel('Special');
        $specialObj->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }
}