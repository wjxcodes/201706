<?php
/**
 * @author demo 
 * @date 2014年11月11日 updateTime 2015-4-2
 */
/**
 * 自建题库列表模型类，用于查看试题列表相关操作
 */
namespace Custom\Manage;
class CustomTestManage extends BaseController  {
    var $moduleName = '自建题库列表';
    private $Status=array(  //试题状态
        '-2'=>'不显示',
        '-1'=>'审核失败',
        '0'=>'优化中',
        '1'=>'正常'
    );
    /**
     * 自建题库试题列表查看(分学科，查看)
     * @author demo
     */
     public function index(){
         $pageName='自建题库列表';
         $data=' 1=1 '; //初始化条件
         $map=array(); //分页条件
         $doc = 0;
         //浏览谁的试题.区分学科
         if($this->ifSubject && $this->mySubject){
             $data .= 'and b.SubjectID in ('.$this->mySubject.') ';
         }
         if ($_REQUEST['name'] || $_REQUEST['name']==='0') {
             //简单查询
             $map['name'] = $_REQUEST['name'];
             $data .= ' and a.TestID='.$_REQUEST['name'];
         } else {
             //高级查询
             if ($_REQUEST['TestID']) {
                 if(is_numeric($_REQUEST['TestID'])){
                     $map['TestID'] = $_REQUEST['TestID'];
                     $data .= ' and a.TestID='.$_REQUEST['TestID'];
                 }else{
                     $this->setError('30502');
                 }
             }
             if($_REQUEST['docid']){
                $doc = $_REQUEST['docid'];
                $map['docid'] = $_REQUEST['docid'];
             }
             if ($_REQUEST['SubjectID']) {
                 $map['SubjectID'] = $_REQUEST['SubjectID'];
                 $data .= ' and b.SubjectID='.$_REQUEST['SubjectID'];
             }
             if ($_REQUEST['IsTpl'] !== ''){
                 $map['IsTpl'] = (int)$_REQUEST['IsTpl'];
                 if($map['IsTpl'] > 0){
                    $data .= ' and b.IsTpl>0';
                 }else{
                    $data .= ' and b.IsTpl=0';
                 }
             }
             if (is_numeric($_REQUEST['Status'])) {
                 $map['Status'] = $_REQUEST['Status'];
                 $data .= ' and b.Status='.$_REQUEST['Status'];
             }
         }
         $perPage = C('WLN_PERPAGE'); //每页 页数
         $customTest = $this->getModel('CustomTest');
         $count=$customTest->customTestSelectCount($data); //按统计自建题库试题总数
         $this->pageList($count, $perPage, $map);
         $page=page($count,$_GET['p'],$perPage).','.$perPage;
        
         $list=$customTest->customTestSelectByPageList($data,$page, $doc); //按统计自建题库试题分页查看数据 

         $typeCache=$this->getApiCommon('Types/types');
         $subject = $this->getApiCommon('Subject/subject');
         $special = $this->getApiCommon('Special/special');
         $grade = SS('grade');
         $testStatus=$this->Status;
         /*根据数据属性ID从缓存中获取对应属性名称*/
         foreach($list as $i=>$iList){
             $list[$i]['Test']=formatString('IPReturn',stripcslashes($iList['Test']));
             $list[$i]['Status']=$testStatus[$iList['Status']];
             $list[$i]['TypesName']=$typeCache[$iList['TypesID']]['TypesName'];
             $list[$i]['GradeName']=$grade[$list[$i]['GradeID']]['GradeName'];
             $list[$i]['SubjectName']=$subject[$subject[$list[$i]['SubjectID']]['PID']]['SubjectName'].$subject[$list[$i]['SubjectID']]['SubjectName'];

             //判断技能和能力标签是否标注
             $testid = $iList['TestID'];
             $skillModel = $this->getModel('CustomTestSkill');
             $skillData = $skillModel->selectData('SkillID',"TestID = $testid");
             if($skillData){
                $tmp = count($skillData);
                $str = '';
                foreach ($skillData as $k => $v) {
                    $SkillID = $v['SkillID'];
                    $NameData = $this->getModel('Skill')->selectData('SkillName',"SkillID = $SkillID");
                    if($k == ($tmp-1)){
                        $str .= $NameData[0]['SkillName'];
                    }else{
                        $str .= $NameData[0]['SkillName'].'-';
                   } 
                }
                $list[$i]['haveSkill'] = $str;
                
             }else{
                $list[$i]['haveSkill'] = '';
             }

             $capacityModel = $this->getModel('CustomTestCapacity');
             $capData = $capacityModel->selectData('CapacityID',"TestID = $testid");
             if($capData){
                $tmp1 = count($capData);
                $cstr ='';
                foreach ($capData as $ck => $cv) {
                    $CapacityID = $cv['CapacityID'];
                    $NameData = $this->getModel('Capacity')->selectData('CapacityName',"CapacityID = $CapacityID");
                    if($ck == ($tmp1-1)){
                        $cstr .= $NameData[0]['CapacityName'];
                    }else{
                        $cstr .= $NameData[0]['CapacityName'].'-';
                   } 
                }
                $list[$i]['haveCap'] = $cstr;
             }else{
                $list[$i]['haveCap'] = '';
             }
         }

         $subjectArray=$this->getApiCommon('Subject/subjectParentID'); //获取学科数据集
        //dump($list);die;
         /*载入模板标签*/
         $this->assign('list', $list); // 赋值数据集
         $this->assign('subjectArray', $subjectArray);
         $this->assign('testStatus', $testStatus);
         $this->assign('pageName', $pageName);
         $this->display();
        
     }
    /**
     * 查看单个试题详细情况
     * @author demo
     */
    public function edit(){
        $pageName='试题详情及修改';
        $testID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($testID)) {
            $this->setError('30301',1);
        }
        //编辑谁的文档  分学科判断是否显示
        $edit =$this->getModel('CustomTest')->customTestSelectByTestId($testID);
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712',1);
            }
        }
        $typeCache=$this->getApiCommon('Types/types');
        $subject = $this->getApiCommon('Subject/subject');
        $special = $this->getApiCommon('Special/special');

        $testStatus=$this->Status;
        $param['style']='grade';
        $param['subjectID']=$edit[0]['SubjectID'];
        $grade=$this->getData($param);
        $edit[0]['Status']=$testStatus[$edit[0]['Status']];
        $edit[0]['Test']=formatString('IPReturn',stripcslashes($edit[0]['Test']));
        $edit[0]['Analytic']=formatString('IPReturn',stripcslashes($edit[0]['Analytic']));
        $edit[0]['Answer']=formatString('IPReturn',stripcslashes($edit[0]['Answer']));
        $edit[0]['typesName']=$typeCache[$edit[0]['TypesID']]['TypesName'];
        $edit[0]['SpecialName']=$special[$edit[0]['SpecialID']]['SpecialName'];
        $edit[0]['GradeName']=$grade[$edit[0]['GradeID']]['GradeName'];
        $edit[0]['SubjectName']=$subject[$subject[$edit[0]['SubjectID']]['PID']]['SubjectName'].$subject[$edit[0]['SubjectID']]['SubjectName'];
        //获取试题对应知识点
        $knowledgeArr=$this->getModel('CustomTestKnowledge')->selectData(
            'KlID',
            'TestID='.$testID);
        foreach($knowledgeArr as $i=>$iKnowledgeArr){
            $newArr[]=$iKnowledgeArr['KlID'];
        }
        $edit[0]['KlID']=implode(',',$newArr);
        //获取试题对应章节
        $chapterArr=$this->getModel('CustomTestChapter')->selectData(
            'ChapterID',
            'TestID='.$testID);
        foreach($chapterArr as $i=>$iChapterArr){
            $newChapterArr[]=$iChapterArr['ChapterID'];
        }
        $edit[0]['ChapterID']=implode(',',$newChapterArr);

        //获取试题对应技能
        $Skill=$this->getModel('CustomTestSkill')->selectData(
            'SkillID',
            'TestID='.$testID);
        foreach($Skill as $i=>$iSkill){
            $newSkillArr[]=$iSkill['SkillID'];
        }
        $edit[0]['SkillID']=implode(',',$newSkillArr);


        //获取试题对应能力
        $capacityArr=$this->getModel('CustomTestCapacity')->selectData(
            'CapacityID',
            'TestID='.$testID);
        foreach($capacityArr as $i=>$icapacityArr){
            $newCapacityArr[]=$icapacityArr['CapacityID'];
        }
        $edit[0]['CapacityID']=implode(',',$newCapacityArr);
        //复合题
        $chooseList='';
        if($edit[0]['IfChoose']==1){
            $buffer=$this->getModel('CustomTestJudge')->selectData(
                '*',
                'TestID='.$testID,
                'OrderID asc');
            $chooseList=$buffer;
        }
        //分割宽度 及数量
        $optionNum=explode(',',$edit[0]['OptionNum']);
        $optionWidth=explode(',',$edit[0]['OptionWidth']);
        for($i=0;$i<count($chooseList);$i++){
            if(empty($optionWidth[$i])){
                $optionWidthArr[$i]=0;
            }else{
                $optionWidthArr[$i]=$optionWidth[$i];
            }
            if(!is_numeric($optionNum[$i])){
                $optionNumArr[$i]=0;
            }else{
                $optionNumArr[$i]=$optionNum[$i];
            }
        }
        
        /*载入模板标签*/
        //方便在编辑器中调用数据
        $jsonEdit=json_encode( $edit[0]);
        $subjectArray=$this->getApiCommon('Subject/subjectParentID'); //获取学科数据集
        $this->assign('pageName', $pageName);
        $this->assign('act', 'edit');
        $this->assign('grade',$grade);
        $this->assign('subjectArray', $subjectArray);
        $this->assign('edit', $edit[0]);
        $this->assign('optionnum',$optionNumArr);
        $this->assign('optionwidth',$optionWidthArr);
        $this->assign('data', $jsonEdit); //在编辑器中使用
        $this->assign('chooseList', $chooseList);
        $this->display();
    }

    /**
     * 数据保存
     * @author demo
     */
    public function save(){
        $result = $this->getModel('CustomTest')->saveData(
            $_POST['data'],
            $this->getModel('CustomTestAttr'),
            $this->getModel('CustomTestKnowledge'),
            $this->getModel('CustomTestChapter'),
            $this->getModel('CustomTestJudge')
        );
        if(!$result){
            $this->setError('30307',$this->responseCode);
        }
        $this->setBack('保存成功！');
    }
    /**
     * 删除试题
     * @author demo
     * 试题在未优化前可删除 试题优化状态表IfLock=0时可删除,清除添加的所有表关于试题id的数据；
     * 试题在优化失败以后可删除 试题表status状态为-1时可删除 即优化失败；清除添加的所有表关于试题id的数据；
     * 试题在优化以后仅隐藏 试题属性表状态为1时 修改试题表status状态为-2 隐藏试题
     */
    public function delete(){
        $id=$_REQUEST['id'];
        if(!$id){
            $this->setError('30301');//数据标识不能为空！
        }
        $result=$this->getModel('CustomTest')->customTestSelectByTestIDDel($id);
        $changeTestID='';
        $delTestID='';
        $lockTestID='';
        foreach($result as $i=>$iResult){
            if($iResult['IfLock']=='0'){
                $lockTestID[]=$iResult['TestID'];
            }else if($iResult['Status']=='-1'){
                $delTestID[]=$iResult['TestID'];
            }else if($iResult['Status']=='1'){
                $changeTestID[]=$iResult['TestID'];
            }
        }
        //修改试题状态
        if(!empty($changeTestID)){
            foreach($changeTestID as $id){
                $this->getModel('CustomTest')->deleteData($id);
            }
        }
        if(!empty($lockTestID)){ //试题优化状态表IfLock=0时的删除
            //删除主，副表
            $model = $this->getModel('CustomTest');
            foreach($lockTestID as $id){
                $msg=$model->deleteData($id);
                if($msg !== false){
                    $msg = $model->delCopyData($id);
                }
                if($msg !== false){
                    $msg=$this->getModel('CustomTestTaskList')->deleteData('TestID in ('.$id.')');
                }
                if($msg !== false){
                    $msg=$this->getModel('CustomTestTaskStatus')->deleteData('TestID in ('.$id.')');
                }
            }
        }
        if(!empty($delTestID)){ //试题表status状态为-1的可删除
            //删除主，副表
            $model = $this->getModel('CustomTest');
            foreach($lockTestID as $id){
                $msg=$model->deleteData($id);
                if($msg !== false){
                    $msg = $model->delCopyData($id);
                }
                if($msg !== false){
                    $msg=$this->getModel('CustomTestTaskList')->deleteData('TestID in ('.$id.')');
                }
                if($msg !== false){
                    $msg=$this->getModel('CustomTestTaskStatus')->deleteData('TestID in ('.$id.')');
                }
            }
        }
        if($msg === false){
            $this->setError('30302','',__URL__); //删除试题失败！
        }else{
            $this->adminLog($this->moduleName,'删除TestID为【'.$idStr.'】的数据');
            $this->showSuccess('删除试题成功！',__URL__);
        }
    }
}