<?php
/**
 * 教师审核任务类，用于处理审核教师相关操作
 */
namespace Teacher\Controller;
class CheckController extends BaseController {
    var $moduleName = '试卷审核管理'; //模块名称
    /**
     * 审核任务列表；get方式或post方式
     * @author demo
     */
    public function index(){
        $pageName = '试卷分配任务';
        //页面标题
        $Status=0;
        if(isset($_REQUEST['Status'])) $Status=$_REQUEST['Status'];
        
        switch($Status){
            case 0:
                $pageName='未完成任务';
            break;
            
            case 1:
                $pageName='已提交任务';
            break;
            
            case 2:
                $pageName='已完成任务';
            break;
        }
        //查询数据
        $map=array();
        $UserName=$this->getCookieUserName(); //用户名
        $data=' UserName="'.$UserName.'" '; //查询条件
        if(is_numeric($Status)){
            $map['Status']=$Status;
            $data.=' AND Status = "'.$Status.'" ';
        }
        $perpage=C('WLN_PERPAGE');
        $teacherWorkCheck = $this->getModel('TeacherWorkCheck');
        $count = $teacherWorkCheck->selectCount(
            $data,
            '*'
        );// 查询满足要求的总记录数
        $page=page($count,$_POST['page'],$perpage);
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $teacherWorkCheck->selectData(
            '*',
            $data,
            'AddTime ASC',
            ($perpage*($page-1)).','.$perpage
        );
        $this->pageList($count,$perpage,$map); //载入分页
        
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 审核任务详情；get方式
     * @param int $id 审核任务id
     * @author demo
     */
    public function checkwork(){
        $pageName='任务详情';
        $id=$_GET['id'];
        //检查数据合法性
        if(!is_numeric($id)){
            $this->setError('30502');
        }
        $UserName=$this->getCookieUserName();
        $workArray=$this->getModel('TeacherWorkCheck')->selectData(
            '*',
            'WCID='.$id
        );
        if(!$workArray){
            $this->setError('40112',NORMAL_ERROR);
        }
        if($workArray[0]['UserName']!=$UserName){
            $this->setError('40113',NORMAL_ERROR);
        }
        $buffer=$this->getModel('TeacherWorkList')->selectData(
            '*',
            'WorkID='.$workArray[0]['WorkID'].' and CheckStatus!=2',
            'DocID asc'
        );
        if(!$buffer){
            $this->setError('40114',NORMAL_ERROR,__URL__);
        }
        $tmpArr=array();//存储docid
        $tmpArr2=array();//存储以docid为键的数据集
        foreach($buffer as $i=>$iBuffer){
            $tmpArr[]=$iBuffer['DocID'];
            $tmpArr2[$iBuffer['DocID']]=$iBuffer;
        }
        //获取文档数据
        $docArray=$this->getModel('Doc')->selectData(
            '*',
            'DocID in ('.implode(',',$tmpArr).')'
        );
        if($docArray){
            foreach($docArray as $i=>$iDocArray){
                $docArray[$i]['CheckStatus']=$tmpArr2[$iDocArray['DocID']]['CheckStatus'];
                $docArray[$i]['SaveCode']=md5($UserName.$iDocArray['DocID'].$id.C('TEST_KEY'));
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
        $WCID=$_GET['id'];
        if(!is_numeric($WCID)){
            $this->setError('30502');
        }
        $UserName=$this->getCookieUserName();
        //检查用户权限
        $buffer=$this->getModel('TeacherWorkCheck')->selectData(
            'UserName,WorkID',
            'WCID='.$WCID
        );
        if(!$buffer){
            $this->setError('40112',NORMAL_ERROR);
        }
        $WorkID=$buffer[0]['WorkID'];
        if($buffer[0]['UserName']!=$UserName){
            $this->setError('40113',NORMAL_ERROR);
        }
        if($buffer[0]['Status']!=0){
            $this->setError('40115',NORMAL_ERROR);
        }
        //检查文档是否都审核过了
        $buffer=$this->getModel('TeacherWorkList')->selectData(
            '*',
            'WorkID='.$WorkID
        );
        if(!$buffer){
            $this->setError('40112',NORMAL_ERROR);
        }
        foreach($buffer as $i=>$iBuffer){
            if($iBuffer['CheckStatus']!=1 && $iBuffer['CheckStatus']!=2){
                $this->setError('40116',NORMAL_ERROR);
            }
        }
        if($this->getModel('TeacherWorkCheck')->updateData(
                array('Status'=>1),
                'WCID='.$WCID
            )===false){
            $this->setError('40117',NORMAL_ERROR);
        }else{
            $this->showSuccess('提交成功！');
        }
    }
    /**
     * 提交文档等待审核
     * @author demo
     */
    public function submitdoc(){
        $UserName=$this->getCookieUserName();
        $did=$_GET['did'];
        $wcid=$_GET['wcid'];
        $s=$_GET['s'];
        if(!is_numeric($did) || !is_numeric($wcid)){
            $this->setError('30502');
        }
        //文档验证码
        if($s!=md5($UserName.$did.$wcid.C('TEST_KEY'))){
            $this->setError('30113',NORMAL_ERROR);
        }
        //获取workid
        $buffer=$this->getModel('TeacherWorkCheck')->selectData(
            'WorkID,CheckTimes',
            'WCID='.$wcid
        );
        if(!$buffer){
            $this->setError('40201',NORMAL_ERROR);
        }
        $CheckTimes=$buffer[0]['CheckTimes'];
        $WorkID=$buffer[0]['WorkID'];
        
        //判断任务状态
        $buffer=$this->getModel('TeacherWorkList')->selectData(
            '*',
            'DocID='.$did.' and WorkID='.$WorkID
        );
        if(!$buffer){
            $this->setError('40202',NORMAL_ERROR);
        }
        if($buffer[0]['CheckStatus']!=0){
            $this->setError('40115',NORMAL_ERROR);
        }
        
        $WLID=$buffer[0]['WLID'];
        
        //检查试卷下的试题是否都已经审核完
        $buffer=$this->getModel('TestAttr')->selectData(
            'TestID,Duplicate',
            'DocID='.$did
        );
        if(!$buffer){
            $this->setError('40203',NORMAL_ERROR);
        }
        //记录testid到数组
        $tmpArr=array();
        foreach($buffer as $i=>$iBuffer){
            if($iBuffer['Duplicate']!=0) continue;
            $tmpArr[]=$iBuffer['TestID'];
        }
        //获取审核后的试题
        $buffer=$this->getModel('TeacherWorkTest')->unionSelect('teacherWorkTestByDocInfo',$wcid,$CheckTimes,$did);
        if(count($buffer)<count($tmpArr)){
            $this->setError('40204',NORMAL_ERROR);
        }
        
        //修改文档状态为待审核
        if($this->getModel('TeacherWorkList')->updateData(
                array('CheckStatus'=>1),
                'WLID='.$WLID
            )===false){
            $this->setError('40117',NORMAL_ERROR);
        }else{
            // $this->showSuccess('提交成功！',__MODULE__.'-Check-checkwork-id-'.$wcid);
            $this->showSuccess('提交成功！',U('Teacher/Check/checkwork', array('id'=>$wcid)));
        }
    }
}
?>