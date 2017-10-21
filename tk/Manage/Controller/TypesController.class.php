<?php
/**
 * @author demo
 * @date 2014-11-12 updatetime 2014-12-30
 */
/**
 * 题型配置类，用于题型的相关操作
 */
namespace Manage\Controller;
class TypesController extends BaseController  {
    var $moduleName = '题型配置';
    /**
     * 浏览题型列表
     * @author demo
     */
    public function index() {
        $pageName = '题型管理';
        $act = 'add'; //模板标识
        $subjectArray = SS('subjectParentId'); //父类数据集
        $map=array();
        $data=' 1=1 ';
            if($this->ifSubject && $this->mySubject){
                $data .= 'AND a.SubjectID in ('.$this->mySubject.')';
            }
            if($_REQUEST['name']){
                //简单查询
                $map['name']=$_REQUEST['name'];
                $data.=' AND a.TypesName like "%'.$_REQUEST['name'].'%" ';
            }else{
                //高级查询
                if($_REQUEST['TypesName']){
                    $map['TypesName']=$_REQUEST['TypesName'];
                    $data.=' AND a.TypesName like "%'.$_REQUEST['TypesName'].'%" ';
                }
                if($_REQUEST['SubjectID']){
                    if($this->ifSubject && $this->mySubject){
                        if(!in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                            $this->showerror('您不能搜索非所属学科题型！');
                        }
                    }
                    $map['SubjectID']=$_REQUEST['SubjectID'];
                    $data.=' AND a.SubjectID ="'.$_REQUEST['SubjectID'].'" ';
                }
            }
        $perpage=C('WLN_PERPAGE');
        $types=$this->getModel('Types');
        $count = $types->SelectCount($data,'TypesID','a'); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性;
        $page=page($count,$_GET['p'],$perpage).','.$perpage;
        $list = $types->unionSelect('typesSelectPageData',$data,$page); //获取题型数据集
        $this->pageList($count,$perpage,$map);
        if($list){
            $subjectCache=SS('subject');
            foreach($list as $i=>$iList){
                $list[$i]['SubjectName']=$subjectCache[$subjectCache[$list[$i]['SubjectID']]['PID']]['SubjectName'].$list[$i]['SubjectName'];
            }
        }
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('subjectArray', $subjectArray); //题型数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 添加题型
     * @author demo
     */
    public function add() {
        $pageName = '添加题型';
        $act = 'add'; //模板标识
        $subjectArray = SS('subjectParentId');//父类数据集
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray); //父类数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 编辑题型
     * @author demo
     */
    public function edit() {
        $TypesID=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($TypesID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑题型';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('Types')->selectData(
            '*',
            'TypesID='.$TypesID,
            '',
            1);
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712'); //您不能编辑非所属学科题型！
            }
        }
        $subjectArray = SS('subjectParentId'); //父类数据集
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName);
        $this->display('Types/add');
    }

    /**
     * 保存题型
     * @author demo
     */
    public function save() {
        $typesID=$_POST['TypesID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($typesID) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        if($_POST['MaxScore']<$_POST['DScore']){
            $this->setError('13801');//默认分值不能大于最大分值
        }
        if(!is_numeric($_POST['ScoreNormal'])){
            $this->setError('13802');//试题任务加分值必须为数字
        }
        if(!is_numeric($_POST['ScoreIntro'])){
            $this->setError('13803');//入库试题加分值必须为数字
        }
        if(!is_numeric($_POST['ScoreMiss'])){
            $this->setError('13804');//放弃标引扣分值必须为数字
        }
        if(!is_numeric($_POST['ScorePic'])){
            $this->setError('13805');//图片版加分值必须为数字
        }
        if($_POST['IfChooseNum']==0 && empty($_POST['IntelNum'])){
            $this->setError('13806'); //请以[,]间隔，设置选题数量
        }
        $data=array();
        $data['SubjectID']=$_POST['SubjectID'];
        $data['TypesName']=formatString('changeStr2Html',$_POST['TypesName']);
        $data['Num']=$_POST['Num'];
        $data['DScore']=$_POST['DScore'];
        $data['MaxScore']= $_POST['MaxScore'];
        $data['TypesStyle']=$_POST['TypesStyle'];
        $data['TypesScore']=$_POST['TypesScore'];
        $data['ScoreNormal']=$_POST['ScoreNormal'];
        $data['ScoreIntro']=$_POST['ScoreIntro'];
        $data['ScoreMiss']=$_POST['ScoreMiss'];
        $data['ScorePic']=$_POST['ScorePic'];
        $data['Volume']=$_POST['Volume'];
        $data['OrderID']=$_POST['OrderID'];
        $data['IfPoint']=$_POST['IfPoint'];
        $data['IfSingle']=$_POST['IfSingle'];
        $data['IfSearch']=$_POST['IfSearch'];
        $data['IfDo']=$_POST['IfDo'];
        $data['IfChooseType']=$_POST['IfChooseType'];
        $data['IfChooseNum']=$_POST['IfChooseNum'];
        $data['SelectType']=$_POST['SelectType'];
        $data['IntelName']=$_POST['IntelName'];
        $data['IntelNum']=$_POST['IntelNum'];
        $data['Underline']=$_POST['Underline'];
        $data['CardIfGetTest']=$_POST['CardIfGetTest'];
        $types=$this->getModel('Types');
        if($act=='add'){
            if($this->ifSubject && $this->mySubject){
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712'); // 您不能添加非所属学科题型！
                }
            }
            if(strstr($_POST['TypesName'],',')){
                $tmpArr=explode(',',$_POST['TypesName']);

                foreach($tmpArr as $iTmpArr){
                    $data['TypesName']=$iTmpArr;
                    $types->insertData(
                        $data);
                    $this->adminLog($this->moduleName,'添加题型【'.$iTmpArr.'】');
                }
                $this->showSuccess('添加题型成功！',__URL__);
            }else{
                if($types->insertData(
                        $data)===false){
                    $this->setError('30311'); //添加题型失败！
                }else{
                    //写入日志
                    $this->adminLog($this->moduleName,'添加题型【'.$_POST['TypesName'].'】');
                    $this->showSuccess('添加题型成功！',__URL__);
                }
            }
        }else if($act=='edit'){
            $subject = $types->selectData(
                'SubjectID',
                'TypesID='.$typesID);
            if($this->ifSubject && $this->mySubject){
                if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712'); //您不能编辑非所属学科题型！
                }elseif(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712'); //您不能编辑为非所属学科题型！
                }
            }
            $data['TypesID']=$typesID;
            if($types->updateData(
                    $data,
                    'TypesID='.$typesID)===false){
                $this->setError('30311'); //修改题型失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改TypesID为【'.$_POST['TypesID'].'】的数据');
                $this->showSuccess('修改题型成功！',__URL__);
            }
        }
    }

    /**
     * 删除题型
     * @author demo
     */
    public function delete(){
        $typesID=$_POST['id'];    //获取数据标识
        if(!$typesID){
            $this->setError('30301','',__URL__,'');//数据标识不能为空！
        }
        $types = $this->getModel('Types');
        if($this->ifSubject && $this->mySubject){
            $typeData = $this->getModel('Types')->selectData(
                'SubjectID',
                'TypesID in ('.$typesID.')');
            $subjectArr = explode(',',$this->mySubject);
            foreach($typeData as $i=>$iTypeData){
                if(!in_array($iTypeData['SubjectID'],explode(',',$this->mySubject))){
                     $this->setError('30507','',__URL__,''); //您不能删除非所属学科题型！
                }
            }
        }
        if($types->deleteData(
                'TypesID in('.$typesID.')')===false){
            $this->setError('30302','',__URL__,''); //删除题型失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除TypesID为【'.$typesID.'】的数据');
            $this->showSuccess('删除题型成功！',__URL__);
        }
    }

    /**
     * 更新题型缓存
     * @author demo
     */
    public function updateCache(){
        $data=array();
        $types=$this->getModel('Types');
        $types->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }
}