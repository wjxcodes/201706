<?php

/**
 * @author demo
 * @date 2014-11-12
 */
/**
 * 用户测试记录管理，用户用户测试记录的相关操作
 */
namespace Aat\Manage;
class UserTestManage extends BaseController  {
    var $moduleName = '用户测试管理';

    /**
     * 用户测试记录列表浏览
     * @author demo 
     */
    public function index() {
        $userTestRecordModel = $this->getModel('UserTestRecord');
        $pageName = '用户测试管理';
        $map=array();
        $data=' 1=1 ';
        if($this->ifSubject && $this->mySubject){
            $data .= 'AND SubjectID in ('.$this->mySubject.')';
        }
        if ($_REQUEST['id']) {
            $map['id'] = $_REQUEST['id'];
            $data .= ' AND TestID in (' . $_REQUEST['id'] . ') ';
        }else if ($_REQUEST['name']) {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND UserName ="' . $_REQUEST['name'] . '" ';
        } else {
            //高级查询
            if ($_REQUEST['UserName']) {
                $map['UserName'] = $_REQUEST['UserName'];
                $data .= ' AND UserName = "' . $_REQUEST['UserName'] . '" ';
            }
            if ($_REQUEST['SubjectID']) {
                if($this->ifSubject && $this->mySubject){
                    if(!in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                        $this->setError('50016'); //您不能搜索非所属学科测试记录
                    }
                }
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $data .= ' AND SubjectID = "' . $_REQUEST['SubjectID'] . '" ';
            }
            if ($_REQUEST['Style']) {
                $map['Style'] = $_REQUEST['Style'];
                $data .= ' AND Style = "' . $_REQUEST['Style'] . '" ';
            }
            if($_REQUEST['Score']=='2'){
                $data .=' and Score=-1';
                $map['Score']='2';
            }elseif($_REQUEST['Score']=='1'){
                $data .=' and Score>-1';
                $map['Score']='1';
            }
            $start = $_REQUEST['Start'];
            if(strstr($start,'-')){
                $start=strtotime($start);
            }
            $end = $_REQUEST['End'];
            if(strstr($end,'-')){
                $end=strtotime($end);
            }
            if ($start) {
                if (empty ($end)) $end = time();
                $map['Start'] = $start;
                $map['End'] = $end;
                $_REQUEST['Start']=date('Y-m-d H:i:s',$start);
                $_REQUEST['End']=date('Y-m-d H:i:s',$end);
                $data .= ' AND LoadTime between ' . ($start) . ' and ' . ($end) . ' ';
            }
        }
        $styleArray=C('WLN_TEST_STYLE_NAME');//获取测试类型
        $perpage=C('WLN_PERPAGE');
        $count = $userTestRecordModel->selectCount(
            $data,
            'TestID');; // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=page($count,$_GET['p'],$perpage);
        $list = $userTestRecordModel->pageData(
            '*',
            $data,
            'TestID desc',
            $page);

        if($list){
//            $subjectBuffer=SS('subject');
            $subjectBuffer = $this->getApiCommon('Subject/subject');
            foreach($list as $ii=>$listn){
                //测试类型
                $list[$ii]['StyleName']=$styleArray[$list[$ii]['Style']];
                $list[$ii]['SubjectName']=$subjectBuffer[$list[$ii]['SubjectID']]['ParentName'].$subjectBuffer[$list[$ii]['SubjectID']]['SubjectName'];
                //做题时间
                if($listn['RealTime']<60){
                    $list[$ii]['RealTime']=$list[$ii]['RealTime'].'秒';
                }else{
                    $list[$ii]['RealTime']=formatString('timeConversion',$list[$ii]['RealTime']);
                }
                //真实姓名
                $userNames=$this->getModel('User')->selectData(
                        'RealName',
                        'UserName="'.$list[$ii]['UserName'].'"',
                        '',
                        1);
                if($userNames){
                    $list[$ii]['UserName']=$list[$ii]['UserName'].'('.$userNames[0]['RealName'].')';
                }
                //试题编号
                $list[$ii]['Content']=explode(',',$list[$ii]['Content']);
            }
        }
        $this->pageList($count,$perpage,$map);
        
        
        //学科
//        $subjectArray=SS('subjectParentId'); //获取学科数据集
        $subjectArray = $this->getApiCommon('Subject/subjectParentID');
        
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('style_array', $styleArray); //测试类型
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->theme('Wln')->display();
    }

    /**
     * 用户测试回答记录编辑
     * @author demo 
     */
    public function edit() {
        $userTestRecordModel = $this->getModel('UserTestRecord');
        $id=$_GET['id'];
        if(empty($id)){
            $this->setError('30301'); //数据标识不能为空！
        }
        if($this->ifSubject && $this->mySubject){
            $subject = $userTestRecordModel->selectData(
                'SubjectID',
                'TestID='.$id);
            if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('50017'); //您不能编辑非所属学科测试记录
            }
        }
        //测试信息
        $edit=$userTestRecordModel->unionSelect('testUserRecordSelectById',$id);
        //查用户作答情况
        $userAnswerRecordModel= $this->getModel('UserAnswerRecord');
        $anser = $userAnswerRecordModel->selectData(
            'TestID,AnswerText,IfRight',
            'TestRecordID='.$id.' and TestID in('.$edit[0]['Content'].')'
        );

        foreach($anser as $anse){//将用户作答数据集变成已试题ID为下标的数据集
            $anser[$anse['TestID']]=$anse;
        }

        $knowledgeModel = $this->getModel('Knowledge');
        $testRealModel=$this->getModel('TestReal');

        //按测试记录包含的试题ID查询试题的正确答案和题文
        $testField='TestID,Test,Answer';
        $testWhere='TestID in('.$edit[0]['Content'].')';
        $list=$testRealModel->selectData(
            $testField,
            $testWhere);
        foreach($list as $ii=>$listn){
            $list[$ii]['Test']=R('Common/TestLayer/strFormat',array($listn['Test']));
            $list[$ii]['Answer']=R('Common/TestLayer/strFormat',array($listn['Answer']));
            if($anser[$listn['TestID']] ||$anser[$listn['TestID']]['AnswerText']!=''){//判断用户是否作答
                $list[$ii]['AnswerText']=$anser[$listn['TestID']]['AnswerText'];
            }else{
                $list[$ii]['AnswerText']='<span style="color:red">未作答</span>';
            }
            //判断是否正确
            if($edit[0]['Score']=='-1'){//如果未作答就不显示
                $list[$ii]['Right']='';
            }else{
                if($anser[$listn['TestID']]['IfRight']==2){ 
                    $list[$ii]['Right']='<span style="font-size:28px;color:#00FF00;font-weight:bold;">√</span>';
                }else{
                    $list[$ii]['Right']='<span style="font-size:28px;color:red;font-weight:bold;">×</span>';
                }
            }
        }
        //测试条件
        if($edit){
            $recordWhere='';
            $style_array=C('WLN_TEST_STYLE_NAME');//测试类型
            foreach($edit[0] as $ii=>$value){
                if($value!=''){
                    switch ($ii) {
                        case 'Style':
                            $edit[0]['StyleName']=$style_array[$edit[0]['Style']];
                            break;
                        case 'SubjectID':
//                            $subjectBuffer=SS('subject');//学科
                            $subjectBuffer = $this->getApiCommon('Subject/subject');
                            $edit[0]['SubjectName']=$subjectBuffer[$edit[0]['SubjectID']]['SubjectName'];
                            break;
                        case 'RealTime':
                            //作答时间
                            $edit[0]['RealTime']=formatString('timeConversion',$edit[0]['RealTime']);
                            break;
                        case 'Diff':
                            //测试条件
                            $recordWhere.='<p>难度：'.$value.'</p>';
                            break;
                        case 'AimScore':
                            //测试条件
                            $recordWhere.='<p>目标分数/总分：'.str_replace(',','/',$value).'</p>';
                            break;
                        case 'TestAmount':
                            //测试条件
                            $recordWhere.='<p>试题数量：'.$value.'</p>';
                            break;
                        case 'KlID':
                            //测试条件
                            $knowledgeList=$knowledgeModel->selectData(
                                    'KlID,KlName',
                                    'KlID in('.$edit[0]['KlID'].')');
                            foreach($knowledgeList as $i=>$iKnowledge){
                                $KlName[]='('.$iKnowledge['KlID'].')'.$iKnowledge['KlName'];
                            }
                            $recordWhere.='<p>知识点：'.implode('、',$KlName).'</p>';
                            break;
                        case 'Cover':
                            //测试条件
                            $recordWhere.='<p>考点覆盖率：'.$value.'%</p>';
                            break;
                        case 'ChapterID':
//                            $chapter=SS('chapterList');//章节
                            $chapter = $this->getApiCommon('Chapter/chapterList');
                            $chapterName=$chapter[$value]['ChapterName'];
                            $recordWhere.='<p>章节：'.$chapterName.'</p>';
                            break;
                        case 'DocID':
                            if($value){
                                $recordWhere.='<p>试卷ID：'.$value.'</p>';
                                $doc=$this->getModel('Doc');
                                $docName=$doc->getDocByID($value)['DocName'];
                                $recordWhere.='<p>试卷名字：'.$docName.'</p>';
                            }
                            break;
                        case 'TypesID':
//                            $types=SS('types');
                            $types = $this->getApiCommon('Types/types');
                            $typesArr=explode(',',$value);
                            $typesNum=explode(',',$edit[0]['TypesNum']);
                            $typesName='';
                            foreach($typesArr as $jj=>$typesID){
                                $typesName.='('.$types[$typesID]['TypesName'].'/'.$typesNum[$jj].')';
                            }
                            $recordWhere.='<p>题型/试题数量：'.$typesName.'</p>';
                            break;
                    }
                }
            }
        }
        $pageName = '用户回答记录';
        $edit[0]['recordWhere']=$recordWhere;
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('edit', $edit[0]); //数据
        $this->theme('Wln')->display();
    }

    /**
     * 用户测试记录删除
     * @author demo 
     */
    public function delete() {
        $userTestRecordModel = $this->getModel('UserTestRecord');
        $id = $_POST['id']; //获取数据标识
        if (!$id) {
            $this->setError('30301','',__URL__); //数据标识不能为空
        }
        if($this->ifSubject && $this->mySubject){
            $subjectID = $userTestRecordModel->selectData(
                    'SubjectID',
                    'TestID in('.$id.')');
            $subjectArr = explode(',',$this->mySubject);
            foreach($subjectID as $i=>$iSubjectID){
                if(!in_array($iSubjectID['SubjectID'],$subjectArr)){
                    $this->setError('50016'); //您不能删除非所属学科测试记录
                }
            }
        }
        if ($userTestRecordModel->deleteData(
                'TestID in (' . $id . ')') === false) {
            $this->setError('30302'); //删除失败
        } else {
            //写入日志
            $this->adminLog($this->moduleName,'删除用户测试信息TestID为【'.$id.'】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
}