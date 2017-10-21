<?php
/**
 * @author demo
 * @date 2014年11月11日
 */
/**
 * 校本题库副本列表模型类，用于查看试题副本列表相关操作
 */
namespace Custom\Manage;
class CustomTestCopyManage extends BaseController  {
    var $moduleName = '校本题库副本列表';
    /**
     * 校本题库试题副本列表查看(分学科，查看)
     * @author demo
     */
     public function index(){
         $pageName='题库副本列表';
         $data=' 1=1 '; //初始化条件
         $map=array(); //分页条件

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
                 $map['TestID'] = $_REQUEST['TestID'];
                 $data .= ' and a.TestID='.$_REQUEST['TestID'];
             }
             if ($_REQUEST['SubjectID']) {
                 $map['SubjectID'] = $_REQUEST['SubjectID'];
                 $data .= ' and b.SubjectID='.$_REQUEST['SubjectID'];
             }
             if (is_numeric($_REQUEST['Status'])) {
                 $map['Status'] = $_REQUEST['Status'];
                 $data .= ' and b.Status='.$_REQUEST['Status'];
             }
         }
         $perPage = C('WLN_PERPAGE'); //每页 页数
         //副表数量统计
         $customTestCopy = $this->getModel('CustomTestCopy');
         $count=$customTestCopy->customTestCopySelectCount($data);
         $this->pageList($count, $perPage, $map);
         $page=page($count,$_GET['p'],$perPage).','.$perPage;
         //分页查询
         $list=$customTestCopy->customTestCopySelectByPage($data,$page);
         $typeCache=$this->getApiCommon('Types/types');
         $subject = $this->getApiCommon('Subject/subject');
         $special = $this->getApiCommon('Special/special');
         $grade = SS('grade');
         $testStatus=array(  //试题状态
             '-2'=>'不显示',
             '-1'=>'审核失败',
             '0'=>'优化中',
             '1'=>'正常'
         );
         //根据数据属性从缓存中换取对应属性名称
         foreach($list as $i=>$iList){
             $list[$i]['Test']=formatString('IPReturn',stripcslashes($iList['Test']));
             $list[$i]['Status']=$testStatus[$iList['Status']];
             $list[$i]['typesName']=$typeCache[$iList['TypesID']]['TypesName'];
             $list[$i]['SpecialName']=$special[$list[$i]['SpecialID']]['SpecialName'];
             $list[$i]['GradeName']=$grade[$list[$i]['GradeID']]['GradeName'];
             $list[$i]['SubjectName']=$subject[$subject[$list[$i]['SubjectID']]['PID']]['SubjectName'].$subject[$list[$i]['SubjectID']]['SubjectName'];
         }

         $subjectArray=$this->getApiCommon('Subject/subjectParentID'); //获取学科数据集
         /*载入模板标签*/
         $this->assign('list', $list); // 赋值数据集
         $this->assign('subjectArray', $subjectArray);
         $this->assign('testStatus', $testStatus);
         $this->assign('pageName', $pageName);
         $this->display();
        
     }
    /**
     * 查看校本题库副表单个试题详细情况
     * @author demo
     */
    public function showMsg(){
        $pageName='副本试题详情';
        $testID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($testID)) {
            $this->setError('30301',1);
        }
        //根据试题ID查看该试题详细信息
        $edit =$this->getModel('CustomTestCopy')->customTestCopySelectById($testID);
        //编辑谁的文档  分学科判断是否显示
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712',1);
            }
        }
        $typeCache=$this->getApiCommon('Types/types');
        $subject = $this->getApiCommon('Subject/subject');
        $special = $this->getApiCommon('Special/special');
        $grade = SS('grade');

        $testStatus=array(  //试题状态
            '-2'=>'不显示',
            '-1'=>'审核失败',
            '0'=>'优化中',
            '1'=>'正常'
        );
        //处理试题内容及属性查看
        $edit[0]['Test']=formatString('IPReturn',stripcslashes($edit[0]['Test']));
        $edit[0]['Analytic']=formatString('IPReturn',stripcslashes($edit[0]['Analytic']));
        $edit[0]['Answer']=formatString('IPReturn',stripcslashes($edit[0]['Answer']));
        $edit[0]['Status']=$testStatus[$edit[0]['Status']];
        $edit[0]['typesName']=$typeCache[$edit[0]['TypesID']]['TypesName'];
        $edit[0]['SpecialName']=$special[$edit[0]['SpecialID']]['SpecialName'];
        $edit[0]['GradeName']=$grade[$edit[0]['GradeID']]['GradeName'];
        $edit[0]['SubjectName']=$subject[$subject[$edit[0]['SubjectID']]['PID']]['SubjectName'].$subject[$edit[0]['SubjectID']]['SubjectName'];
        /*查看该试题对应的知识点*/
        $knowledgeArr=$this->getModel('CustomTestKnowledgeCopy')->selectData(
            'KlID',
            'TestID='.$testID);
        foreach($knowledgeArr as $i=>$iKnowledgeArr){
            $newArr[]=$iKnowledgeArr['KlID'];
        }
        $param['ID']=implode(',',$newArr);
        $param['style']='knowledgeList';
        $knowledgeStr=$this->getData($param);
        /*查看该试题对应的章节*/
        $chapterArr=$this->getModel('CustomTestChapterCopy')->selectData(
            'ChapterID',
            'TestID='.$testID);
        foreach($chapterArr as $i=>$iChapterArr){
            $newChapterArr[]=$iChapterArr['ChapterID'];
        }
        $param['ID']=implode(',',$newChapterArr);
        $param['style']='chapterList';
        $chapterStr=$this->getData($param);
        $this->assign('pageName', $pageName);
        $this->assign('knowledgeList', $knowledgeStr);
        $this->assign('chapterList', $chapterStr);
        $this->assign('edit', $edit[0]);
        $this->display();
    }
}