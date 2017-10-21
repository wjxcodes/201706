<?php
/**
 * 教师标引任务类，用于处理标引试题相关操作
 */
namespace Teacher\Controller;
class TaskController extends BaseController {
    var $moduleName = '试卷分配管理'; //模块名称
    /**
     * 载入不同状态的任务列表；get方式或post方式
     * @param int $Status 任务状态
     * @author demo
     */
    public function index(){
        //页面标题
        $pageName = '试卷分配任务';
        switch($_REQUEST['Status']){
            case 0:
                $pageName='未完成任务';
            break;
            
            case 1:
                $pageName='待审核任务';
            break;
            
            case 2:
                $pageName='已完成任务';
            break;
            
            case 3:
                $pageName='重做任务';
            break;
        }
        
        //查询数据
        $UserName=$this->getCookieUserName(); //用户名
        $data=' UserName="'.$UserName.'" '; //查询条件
        $map=array();
        if(is_numeric($_REQUEST['Status'])){
            $map['Status']=$_REQUEST['Status'];
            $data.=' AND Status = "'.$_REQUEST['Status'].'" ';
        }
        $perpage=C('WLN_PERPAGE');
        $count = $this->getModel('TeacherWork')->selectCount(
            $data,
            '*'
        );// 查询满足要求的总记录数
        $page=page($count,$_POST['page'],$perpage);
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $this->getModel('TeacherWork')->selectData(
            '*',
            $data,
            'WorkID desc',
            ($perpage*($page-1)).','.$perpage
        );
        $this->pageList($count,$perpage,$map); //载入分页
        
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 查看任务；
     * @author demo
     */
    public function checkwork(){
        $pageName='任务详情';
        $id=$_GET['id']; //任务id
        $userName=$this->getCookieUserName();
        //检查数据的合法性
        if(!is_numeric($id)){
            $this->setError('30502');
        }
        $workArray=$this->getModel('TeacherWork')->selectData(
            '*',
            'WorkID='.$id
        );
        if(!$workArray){
            $this->setError('40112',NORMAL_ERROR,__URL__);
        }
        if($workArray[0]['UserName']!=$userName){
            $this->setError('40113',NORMAL_ERROR);
        }
        $buffer=$this->getModel('TeacherWorkList')->selectData(
            '*',
            'WorkID='.$id.' and status!=2',
            'DocID asc'
        );
        if(!$buffer){
            $this->setError('40114',NORMAL_ERROR,__URL__);
        }
        $tmp_arr=array();//存储文档id
        foreach($buffer as $buffern){
            $tmp_arr[]=$buffern['DocID'];
        }
        //获取文档数据
        $docArray=$this->getModel('Doc')->selectData(
            '*',
            'DocID in ('.implode(',',$tmp_arr).')'
        );
        if($docArray){
            foreach($buffer as $buffern){
                foreach($docArray as $ii=>$iDocArray){
                    if($iDocArray['DocID']==$buffern['DocID']) $docArray[$ii]['Status']=$buffern['Status'];
                    //if(!$doc_array[$ii]['docsavecode']) $doc_array[$ii]['docsavecode']=md5($userName.$doc_arrayn['DocID'].$id.C('TEST_KEY'));
                }
            }
        }
        /*载入模板标签*/
        $this->assign('doc_array', $docArray);
        $this->assign('work_array', $workArray[0]);
        $this->assign('pageName', $pageName);
        $this->display();
    }
    
    /**
     * 提交任务等待审核
     * @author demo
     */
    public function submittask(){
        $WorkID=$_GET['id'];
        if(!is_numeric($WorkID)){
            $this->setError('30502');
        }
        $UserName=$this->getCookieUserName();
        //检查数据合法性
        $TeacherWork = $this->getModel('TeacherWork');
        $buffer=$TeacherWork->selectData(
            '*',
            'WorkID='.$WorkID
        );
        if(!$buffer){
            $this->setError('40112',NORMAL_ERROR);
        }
        if($buffer[0]['UserName']!=$UserName){
            $this->setError('40501',NORMAL_ERROR);
        }
        if($buffer[0]['Status']!=0 && $buffer[0]['Status']!=3){
            $this->setError('40115',NORMAL_ERROR);
        }
        $CheckTimes=$buffer[0]['CheckTimes'];
        //检查文档是否都编辑过了
        $buffer=$this->getModel('TeacherWorkList')->selectData(
            '*',
            'WorkID='.$WorkID
        );
        if(!$buffer){
            $this->setError('40502',NORMAL_ERROR);
        }
        foreach($buffer as $buffern){
            if($buffern['Status']!=1 && $buffern['Status']!=2){
                $this->setError('40503',NORMAL_ERROR);
            }
        }
        if($this->getModel('TeacherWork')->updateData(array('Status'=>1),'WorkID='.$WorkID)===false){
            $this->setError('40117',NORMAL_ERROR);
        }else{
            //修改重做试题的审核状态
            $this->getModel('TeacherWorkCheck')->updateData(
                array('Status'=>0),
                'WorkID='.$WorkID.' and CheckTimes='.$CheckTimes
            );
            //写入日志
            $this->teacherLog($this->moduleName, '写入提交任务WorkID为【' . $WorkID . '】的数据');
            $this->showSuccess('提交成功！');
        }
    }
    /**
     * 提交文档等待审核
     * @author demo
     */
    public function submitdoc(){
        $UserName=$this->getCookieUserName();
        //获取参数
        $did=$_GET['did']; //文档id
        $wid=$_GET['wid']; //任务id
        $s=$_GET['s']; //验证码
        if(!is_numeric($did) || !is_numeric($wid)){
            $this->setError('30502');
        }
        
        //文档验证码
        if($s!=md5($UserName.$did.$wid.C('TEST_KEY'))){
            $this->setError('40119',NORMAL_ERROR);
        }
        
        //判断任务状态
        $buffer=$this->getModel('TeacherWorkList')->selectData(
            '*',
            'DocID='.$did.' and WorkID='.$wid
        );
        if(!$buffer){
            $this->setError('40112',NORMAL_ERROR);
        }
        if($buffer[0]['Status']!=0 and $buffer[0]['Status']!=3){
            $this->setError('40115',NORMAL_ERROR);
        }
        
        $WLID=$buffer[0]['WLID'];//文档对应任务列表id
        
        //检查试卷下的试题是否都已经标注完
        $buffer=$this->getModel('TestAttr')->selectData(
            '*',
            'DocID='.$did
        );
        if(!$buffer){
            $this->setError('40504',NORMAL_ERROR);
        }
        $tmp_arr=array();
        foreach($buffer as $buffern){
            if($buffern['Diff']==0 || $buffern['Diff']=='0.000'){
                $this->setError('40505',NORMAL_ERROR);
            }
            $tmp_arr[]=$buffern['TestID'];
        }
        
        /*//以下数据请保留
        $TestKl=M('TestKl');
        $buffer=$TestKl->where(' TestID in ('.implode(',',$tmp_arr).') ')->select();
        if(!$buffer){
            $this->showerror('该试卷下试题知识点未标注！');
        }
        $tmp_arr2=array();
        foreach($buffer as $buffern){
            if(!in_array($buffern['TestID'],$tmp_arr2)){
                $tmp_arr2[]=$buffern['TestID'];
            }
        }
        if(count($tmp_arr2)!=count($tmp_arr)){
            $this->showerror('该试卷下试题知识点未标注完成！');
        }
        $TestChapter=M('TestChapter');
        $buffer=$TestChapter->where(' TestID in ('.implode(',',$tmp_arr).') ')->select();
        if(!$buffer){
            $this->showerror('该试卷下试题未标注完成！');
        }
        $tmp_arr2=array();
        foreach($buffer as $buffern){
            if(!in_array($buffern['TestID'],$tmp_arr2)){
                $tmp_arr2[]=$buffern['TestID'];
            }
        }
        if(count($tmp_arr2)!=count($tmp_arr)){
            $this->showerror('该试卷下试题章节未标注完成！');
        }
        */
        //修改文档状态为待审核
        if($this->getModel('TeacherWorkList')->updateData(
                array('Status'=>1,'CheckStatus'=>0),
                'WLID='.$WLID
            )===false){
            $this->setError('40117',NORMAL_ERROR);
        }else{
            //写入日志
            $this->teacherLog($this->moduleName, '提交标引文档WLID为【' . $WLID . '】的数据');
            // $this->showSuccess('提交成功！',__MODULE__.'/Task/checkwork/id/'.$wid);
            $this->showSuccess('提交成功！',U('Teacher/Task/checkwork', array('id'=>$wid)));
        }
    }
}