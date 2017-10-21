<?php

/**
 * @author demo
 * @date 2015-2-2
 */
/**
 * 智能组卷默认题型配置类，用于组卷默认题型题型的相关操作
 */
namespace  Ga\Manage;
class TypesDefaultManage extends BaseController  {
    var $moduleName = '智能组卷题型配置';

    /**
     *智能组卷默认题型列表
     *@author demo
     */
    public function index(){
        $pageName = '智能组卷默认题型管理';
        $map=array();
        $subjectArray = SS('subjectParentId'); //父类数据集
        $data = '1=1';
        if($_REQUEST['DefaultID']){
            //简单查询
            $map['DefaultID']=$_REQUEST['DefaultID'];
            $data.=' AND DefaultID like "%'.$_REQUEST['DefaultID'].'%" ';
        }else{
            //高级查询
            if($_REQUEST['AreaID']){
                $map['AreaID']=$_REQUEST['AreaID'];
                $data.=' AND AreaID like "%'.$_REQUEST['AreaID'].'%" ';
            }
            if($_REQUEST['SubjectID']){
                if($this->ifSubject && $this->mySubject){
                    if(!in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                        $this->showerror('您不能搜索非所属学科题型！');
                    }
                }
                $map['SubjectID']=$_REQUEST['SubjectID'];
                $data.=' AND SubjectID ="'.$_REQUEST['SubjectID'].'" ';
            }
        }
        $perPage=C('WLN_PERPAGE');

        $typesDefault=$this->getModel('TypesDefault');
        $count = $typesDefault->selectCount(
            $data,
            '*'
        );
        $page = page($count,$_GET['p'],$perPage).','.$perPage;
        $list = $typesDefault->pageData(
            '*',
            $data,
            'DefaultID desc',
            $page
        );
        $this->pageList($count,$perPage,$map);
        $typeArr = SS('types');
        $areaArr = SS('areaList');
        $subjectArr = SS('subject');
        foreach($list as $i=>$iList){
            $typeId = array();
            $typeName = array();
            $typeId = explode('|',$iList['TypesID']);
            foreach($typeId as $j=>$jTypeId){
                $typeName[$j] = $typeArr[$jTypeId]['TypesName'];
                $selectType[$j]=$typeArr[$jTypeId]['SelectType'];
                $intelName[$j]=$typeArr[$jTypeId]['IntelName'];
            }
            $list[$i]['IntelName'] = $intelName;
            $list[$i]['TypesName'] = $typeName;
            $list[$i]['TypesID'] = $typeId;
            $list[$i]['Num'] = explode('|',$iList['Num']);
            $list[$i]['Score'] = explode('|',$iList['Score']);
            $list[$i]['ChooseNum'] = explode('|',$iList['ChooseNum']);
            $list[$i]['SelectType'] = $selectType;
            if(strstr($iList['IntelNum'],'|')){
                $list[$i]['IntelNum'] = explode('|',$iList['IntelNum']);
            }else{
                $list[$i]['IntelNum'] = 0;
            }
            if($iList['AreaID']==0){
                $list[$i]['AreaName'] = '全部地区';
            }else{
                $areaList = explode(',',$iList['AreaID']);
                $areaStr = array();
                foreach($areaList as $j=>$jAreaList){
                    $areaStr[$j] = $areaArr[$jAreaList]['AreaName'];
                }
                $list[$i]['AreaName'] = implode(',',$areaStr);
            }
            $list[$i]['SubjectName'] = $subjectArr[$iList['SubjectID']]['ParentName'].$subjectArr[$iList['SubjectID']]['SubjectName'];
        }
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('subjectArray',$subjectArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 添加智能组卷默认题型
     * @author demo
     */
    public function add(){
        $pageName = '添加模板组卷默认题型';
        $act = 'add'; //模板标识
        $subjectArray = SS('subjectParentId');//父类数据集
        $param['style']='area';//获取地区
        $param['pID']=0;
        $areaArray=$this->getData($param);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray); //父类数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('areaArray',$areaArray);//地区数据
        $this->display();
    }

    /**
     * 编辑智能组卷默认题型
     * @author demo
     */
    public function edit(){
        $pageName = '编辑智能组卷默认题型';
        $act = 'edit'; //模板标识
        $defaultID = $_GET['id'];
        $subjectArray = SS('subjectParentId');//父类数据集
        $param['style']='area';//获取地区
        $param['pID']=0;
        $areaArray=$this->getData($param);


        $typesDefault=$this->getModel('TypesDefault');
        $edit = $typesDefault->selectData(
            '*',
            'DefaultID='.$defaultID
        );
        if($edit[0]['AreaID']=='0'){
            $edit[0]['AreaName'] = '全部地区';
        }else{
            $areaTmp=SS('areaList');
            $areaList = explode(',',$edit[0]['AreaID']);
            $areaArr = array();
            foreach($areaList as $i=>$iAreaList){
                $areaArr[$i] = $areaTmp[$iAreaList]['AreaName'];
            }
            $edit[0]['AreaID'] = $areaList;
            $edit[0]['AreaIDList'] = join(',',$areaList);
            $edit[0]['AreaName'] = $areaArr;
            $edit[0]['AreaList'] = join(',',$areaArr);
        }
        $edit[0]['TypesID'] = explode('|',$edit[0]['TypesID']);
        $edit[0]['Num'] = explode('|',$edit[0]['Num']);
        $edit[0]['Score'] = explode('|',$edit[0]['Score']);
        $edit[0]['ChooseNum'] = explode('|',$edit[0]['ChooseNum']);

        if(empty($edit[0]['IntelNum'])){
            $edit[0]['IntelNum'] = array_fill(0,count($edit[0]['ChooseNum']),0);
        }else{
            $edit[0]['IntelNum'] = explode('|',$edit[0]['IntelNum']);
        }
        $typesTmp = SS('types');
        foreach($edit[0]['TypesID'] as $i=>$iEdit){
            $edit[0]['TypeInfo'][$i]=$typesTmp[$iEdit];
        }
        $param['style'] = 'types';
        $param['subjectID'] = $edit[0]['SubjectID'];
        $typesArray = $this->getData($param);
        $types=json_encode($typesArray, JSON_UNESCAPED_UNICODE);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray); //父类数据集
        $this->assign('typesArray',$typesArray);//题型数据
        $this->assign('types',$types);
        $this->assign('edit',$edit[0]);
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('areaArray',$areaArray);//地区数据
        $this->display('TypesDefault/add');
    }

    /**
     * 保存智能组卷默认题型
     * @author demo
     */
    public function save(){
        $defaultID=$_POST['DefaultID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        if(empty($defaultID) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        $chooseNum = $_POST['ChooseNum'];
        foreach($chooseNum as $i=>$iChooseNum){
            if($iChooseNum>$_POST['Num'][$i]){
                $this->setError('21001');
            }
        }
        $areaList = explode(',',$_POST['AreaList']);

        $typesDefault=$this->getModel('TypesDefault');
        if($act=='add'){
            $tmpArray = $typesDefault->selectData(
                'AreaID',
                'SubjectID='.$_POST['SubjectID']
            );
            if($_POST['AreaList']==0 && $typesDefault->selectData(
                    'DefaultID',
                    'SubjectID='.$_POST['SubjectID']
                )){
                $this->setError('21002');
            }
        }else{
            if($_POST['AreaList']==0 && $typesDefault->selectData(
                    'DefaultID',
                    'SubjectID='.$_POST['SubjectID'].' AND DefaultID<>'.$defaultID
                )){
                $this->setError('21002');
            }
            $tmpArray = $typesDefault->selectData(
                'AreaID',
                'SubjectID='.$_POST['SubjectID'].' AND DefaultID<>'.$defaultID
            );
        }
        foreach($areaList as $i=>$iAreaList){
            foreach($tmpArray as $i=>$iTmpArray){
                if(in_array($iAreaList,explode(',',$iTmpArray['AreaID']))){
                    $this->setError('21002');
                }
            }
        }
        $data = array();
        $data['SubjectID'] = $_POST['SubjectID'];
        $data['AreaID'] = $_POST['AreaList'];
        $data['TypesID'] = join('|',$_POST['TypesID']);
        $data['Num'] = join('|',$_POST['Num']);
        $data['Score'] = join('|',$_POST['Score']);
        $data['ChooseNum'] = join('|',$_POST['ChooseNum']);
        $data['IntelNum'] = join('|',$_POST['IntelNum']);
        if($act == 'add'){
            if($this->ifSubject && $this->mySubject){
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712'); // 您不能添加非所属学科题型！
                }
            }
            $defaultID = $typesDefault->insertData($data);
            if($defaultID){
                $this->adminLog($this->moduleName,'添加智能组卷默认题型【'.$defaultID.'】');
                $this->showSuccess('添加智能组卷默认题型成功！',__URL__);
            }else{
                $this->setError('21003');
            }
        }elseif($act == 'edit'){
            $subject = $typesDefault->selectData('SubjectID','DefaultID='.$defaultID);
            if($this->ifSubject && $this->mySubject){
                if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712'); //您不能编辑非所属学科题型！
                }elseif(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712'); //您不能编辑为非所属学科题型！
                }
            }
            $data['DefaultID'] = $defaultID;
            if($typesDefault->updateData($data,'DefaultID='.$defaultID)===false){
                $this->setError('30311'); //修改题型失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改DefaultID为【'.$_POST['DefaultID'].'】的数据');
                $this->showSuccess('修改模版组卷默认题型成功！',__URL__);
            }
        }
    }

    /**
     * 删除智能组卷默认题型
     * @author demo
     */
    public function delete(){
        $defaultID=$_POST['id'];    //获取数据标识
        if(!$defaultID){
            $this->setError('30301','',__URL__,'');//数据标识不能为空！
        }

        $typesDefault=$this->getModel('TypesDefault');
        if($this->ifSubject && $this->mySubject){
            $defaultData = $typesDefault->selectData(
                'SubjectID',
                'DefaultID in ('.$defaultID.')'
            );
            foreach($defaultData as $i=>$iDefaultData){
                if(!in_array($iDefaultData['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30507','',__URL__,''); //您不能删除非所属学科题型！
                }
            }
        }
        if($typesDefault->deleteData(
            'DefaultID in ('.$defaultID.')')===false){
            $this->setError('30302','',__URL__,''); //删除题型失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除DefaultID为【'.$defaultID.'】的数据');
            $this->showSuccess('删除智能组卷默认题型成功！',__URL__);
        }
    }

    /**
     * 更新学科缓存
     * @author demo
     */
    public function updateCache(){
        $this->getModel('TypesDefault')->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }
}