<?php
/**
 * @author demo
 * @date 2014年10月23日
 */
/**
 * 公式任务管理类
 */
namespace Teacher\Controller;
class StudentWorkController extends BaseController{
    var $moduleName = '公式任务分配'; //模块名称
    /**
     * 载入不同状态的任务列表；get方式或post方式
     * @param int $Status 任务状态
     * @author demo
     */
    public function index(){
        //页面标题
        $pageName = '公式任务分配';
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
        $map['Status']=$_REQUEST['Status'];
        if(is_numeric($_REQUEST['Status'])){
            $data.=' AND Status = "'.$_REQUEST['Status'].'" ';
        }else{
            $data.=' AND Status = 0 ';
        }
        $perpage=C('WLN_PERPAGE');
        $count = $this->getModel('StudentWork')->selectCount(
            $data,
            '*'
        ); // 查询满足要求的总记录数
        $page=page($count,$_POST['page'],$perpage);
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $this->getModel('StudentWork')->selectData(
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
        $workArray=$this->getModel('StudentWork')->selectData(
            '*',
            'WorkID='.$id
        );
        if(!$workArray){
            $this->setError('40112',NORMAL_ERROR,__URL__);
        }
        if($workArray[0]['UserName']!=$userName){
            $this->setError('40113',NORMAL_ERROR);
        }
        $buffer=$this->getModel('StudentWorkList')->selectData(
            '*',
            'WorkID='.$id.' and status!=2',
            'DocID asc'
        );
        if(!$buffer){
            $this->setError('40114',NORMAL_ERROR);
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
                foreach($docArray as $i=>$iDocArray){
                    if($iDocArray['DocID']==$buffern['DocID']) $docArray[$i]['Status']=$buffern['Status'];
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
     * 公式任务提交任务中的文档
     * @author demo
     */
    public function subjects(){
        $docid = $_GET['docid'];
        $workid = $_GET['workid'];
        if(!is_numeric($docid) || !is_numeric($workid)){
            $this->setError('30502');
        }
        $StudentWork = $this->getModel('StudentWork');
        $data = $StudentWork->selectData(
            '*',
            'WorkID='.$workid,
            '',
            1
        );
        if(empty($data)){
            $this->setError('40112',NORMAL_ERROR,__URL__);
        }
        if($this->getCookieUserName() != $data[0]['UserName']){
            $this->setError('40113',NORMAL_ERROR,__URL__);
        }
        //查询文档信息
        $doc=$this->getModel('Doc')->selectData(
            'DocName',
            'DocID='.$docid
        );
        if(empty($doc)){
            $this->setError('40118',NORMAL_ERROR,__URL__);
        }
        $list['DocName'] = $doc[0]['DocName'];
        //获取试题数据
        $test = $this->getModel('Test');
        $testBuffer = array_merge(
            (array)$test->selectData(
                'TestID,Test, "t" as classify',
                'DocID='.$docid,
                'NumbID asc'
            ),
            (array)$this->getModel('TestReal')->selectData(
                'TestID,Test,"tr" as classify',
                'DocID='.$docid,
                'NumbID asc'
            )
        );
        usort($testBuffer, function($a1, $a2){
            if($a1['TestID'] > $a2['TestID']){
                return 1;
            }
            return -1;
        });
        $host=C('WLN_DOC_HOST');
        foreach($testBuffer as $key=>$value){
            foreach($value as $k=>$v){
                if('Test' == $k){
                    $testBuffer[$key][$k] =  R('Common/TestLayer/strFormat',array($testBuffer[$key][$k]));
                }
            }
        }
        $list['docid'] = $docid;
        $list['workid'] = $workid;
        $list['list'] = $testBuffer;
        unset($testBuffer,$host,$test);
        $this->assign('list',$list);
        $this->display();
    }

    /**
     * 显示题型的内容
     * @author demo
     */
    public function showTestContent(){
        $docid = $_GET['docid'];//变量未使用
        $workid = $_GET['workid'];
        $testid = $_GET['testid'];
        $classify = $_GET['classify'];
        if(!is_numeric($docid) || !is_numeric($workid) || !is_numeric($testid)){
            $this->setError('30502');
        }
        $studentWork=$this->getModel('StudentWork');
        $data = $studentWork->selectData(
            '*',
            'WorkID='.$workid,
            '',
            1
        );
        if(empty($data)){
            $this->setError('40112',AJAX_ERROR);
        }
        if($this->getCookieUserName() != $data[0]['UserName']){
            $this->setError('40113',AJAX_ERROR,__URL__);
        }
        $test = $this->getModel('Test');
        $testData = array();
        if($classify == 't'){
            $testData = $this->getModel('Test')->findData(
                'Test,Equation',
                'TestID='.$testid
            );
        }else{
            $testData = $this->getModel('TestReal')->findData(
                'Test,Equation',
                'TestID='.$testid
            );
        }
        $testData['Test'] =  R('Common/TestLayer/strFormat',array($testData['Test']));
        $testData['Equation'] = $test->getEquations($testData['Equation']);
        $equations = $this->getModel('TestDoc')->fetchEquationById($testid);
        $len = count($equations['DocTest']);
        $eqLen = count($testData['Equation']);
        if($eqLen > $len){
            $len = $eqLen;
        }
        $this->assign('testData',$testData);
        $this->assign('verify',md5($data['UserName'].$workid.$testid));
        $this->assign('len',$len);
        $this->assign('workid',$workid);
        $this->assign('testid',$testid);
        $this->assign('classify',$classify);
        $this->setBack($this->fetch('StudentWork/edit'));
    }

    /**
     * 保存
     * @author demo
     */
    public function save(){
        $verify = $_POST['verify'];
        if(!$verify){
            $this->setError('30301',AJAX_ERROR);
        }
        $workid = $_POST['workid'];
        $testid = $_POST['testid'];
        if(!is_numeric($workid) || !is_numeric($testid)){
            $this->setError('30502');
        }
        $classify = $_POST['classify'];
        $data = $this->getModel('StudentWork')->selectData(
            '*',
            'WorkID='.$workid,
            '',
            1
        );
        if($verify != md5($data['UserName'].$workid.$testid)){
            $this->setError('40119',AJAX_ERROR);
        }
        if(empty($data)){
            $this->setError('40112',AJAX_ERROR,__URL__);
        }
        $studentWork=$this->getModel('StudentWork');
        if($this->getCookieUserName() != $data[0]['UserName']){
            $this->setError('40113',AJAX_ERROR,__URL__);
        }
        $test = $this->getModel('Test');
        $arr['equation'] = $_POST['equation'];
        $arr['classify'] = $_POST['classify'];
        $result = $test->addEquation($testid,$arr);
        if($result === false)
            $this->setError('30307',AJAX_ERROR);
        $this->setBack('success');
    }

    /**
     * 提交试题
     * @author demo
     */
    public function finish(){
        $docid = $_POST['docid'];
        $workid = $_POST['workid'];
        // $url = __URL__.'-checkwork-id-'.$workid;
        $url = U('Teacher/StudentWork/checkwork', array('id'=>$workid));
        if(!is_numeric($docid) || !is_numeric($workid)){
            $this->setError('30502');
        }
        $data = $this->getModel('StudentWork')->selectData(
            '*',
            'WorkID='.$workid,
            '',
            1
        );
        if(empty($data)){
            $this->setError('40112',NORMAL_ERROR, $url);
        }
        $studentWork=$this->getModel('StudentWork');
        if($this->getCookieUserName() != $data[0]['UserName']){
            $this->setError('40113',NORMAL_ERROR,$url);
        }
        $studentWorkList = $this->getModel('StudentWorkList');
        $where['DocID'] = $docid;
        $where['WorkID'] = $workid;
        $result = $studentWorkList->findData(
            '*',
            $where
        );
        $result = $studentWorkList->updateStatusByWorkListId($result['WLID'],1);
        if($result === false){
            $this->setError('40120',NORMAL_ERROR,$url);
        }
        $this->showSuccess('提交成功！',$url);
    }

    /**
     * 提交任务
     * @author demo
     */
    public function submit(){
        $workid = $_GET['workid'];
        // $url = __URL__.'-checkwork-id-'.$workid;
        $url = U('Teacher/StudentWork/checkwork', array('id'=>$workid));
        if(!is_numeric($workid)){
            $this->setError('30502');
        }
        $studentWork = $this->getModel('StudentWork');
        $data = $studentWork->selectData(
            '*',
            'WorkID='.$workid,
            '',
            1
        );
        if(empty($data)){
            $this->setError('40112',NORMAL_ERROR, $url);
        }
        if($this->getCookieUserName() != $data[0]['UserName']){
            $this->setError('40113',NORMAL_ERROR,$url);
        }
        $studentWorkList = $this->getModel('StudentWorkList');
        if(!$studentWorkList->isSpecifiesState($workid,array(1,2))){
            $this->setError('40401',NORMAL_ERROR,$url);
        }
        if(!$studentWork->updateStatus($workid,1)){
            $this->setError('40121',NORMAL_ERROR,$url);
        }
        // $this->showSuccess('提交任务成功！',__URL__.'-index-Status-'.$data['Status']);
        $this->showSuccess('提交任务成功！',U('Teacher/StudentWork/index', array('Status'=>$data['Status'])));
    }
    /**
     * 公式下载
     * @author demo
     * @date 2015-3-3
     */
    public function formulaeDownload(){
        $testid = $_POST['testid'];
        sort($testid);
        $testDoc = $this->getModel('TestDoc');
        $formulaeList = $testDoc->selectData(
            'TestID,DocTest',
            'TestID IN('.implode(',',$testid).')'
        );
        $text = '<p align=center style="text-align:center;line-height:150%"><span 
style="font-size:14.0pt;line-height:150%;mso-bidi-font-weight:bold">'.$docName['DocName'].'</span></p>';//变量未定义
        foreach($formulaeList as $value){
            if(empty($value['DocTest'])){
                continue;
            }
            $value['DocTest'] = getStaticFunction('TestDocModel','fetchEquation',$value['DocTest']);
            $size = count($value['DocTest']);
            $text .= "<p><p><strong>试题编号：{$value['TestID']}，共有{$size}个公式：</strong></p>";
            foreach($value['DocTest'] as $val){
                $text .= $val.'&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            $text .= '</p>';
        }
        $doc = $this->getModel('Doc');
        $result = $doc->createDoccon(array(), $text, $docName['DocName'].'公式列表', '.docx' ,0 ,1);
        if(is_array($result)){
            $this->setError('40125', AJAX_ERROR);
        }else{
            $doc->deleteAllFile(array('DocPath'=>$result));
            $this->setBack(C('WLN_DOC_HOST').$result);
        }
    }
}