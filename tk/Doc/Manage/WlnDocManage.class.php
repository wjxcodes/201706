<?php
/**
 * @author demo
 * @date 2014年8月7日
 */
/**
 * 文档控制器类，用于处理文档相关操作
 */
namespace Doc\Manage;
header("Content-type: text/html; charset=utf-8");
class WlnDocManage extends BaseController  {
    var $moduleName = '文档管理';
    /**
     * 浏览文档列表
     * @author demo
     */
    public function index() {
        $pageName = '文档管理';
        $doc = $this->getModel('Doc');
		// dump($doc);die;
        $map = array ();
        //浏览谁的文档
        $data = ' 1=1 ';
        //验证用户权限
        if ($this->ifSubject && $this->mySubject){
            $data .= ' and a.SubjectID in (' . $this->mySubject . ') ';
        }else if($this->ifDiff){
            $data .= ' and a.Admin="' . $this->getCookieUserName() . '" ';
        }


        if ($_REQUEST['name']) {
            $_REQUEST['name'] = formatString('decodeUrl',$_REQUEST['name']);
            //简单查询
            $map['name'] = formatString('encodeUrl',$_REQUEST['name']);
            $data .= ' AND a.DocName like "%' . $_REQUEST['name'] . '%" ';
        } else {
            //高级查询
            if ($_REQUEST['Admin']) {
                if($this->ifDiff && $this->getCookieUserName()!=$_REQUEST['Admin']){
                    $this->setError('30313',0); //您没有权限查看该内容！
                    return;
                }
                $map['Admin'] = $_REQUEST['Admin'];
                $data .= ' AND a.Admin = "' . $_REQUEST['Admin'] . '" ';
            }
            if ($_REQUEST['DocName']) {
                $map['DocName'] = $_REQUEST['DocName'];
                $data .= ' AND a.DocName like "%' . $_REQUEST['DocName'] . '%" ';
            }
            if ($_REQUEST['GradeID']) {
                $map['GradeID'] = $_REQUEST['GradeID'];
                $data .= ' AND a.GradeID = ' . $_REQUEST['GradeID'] . ' ';
            }
            if ($_REQUEST['DocID']) {
                if(is_numeric($_REQUEST['DocID'])){
                    $map['DocID'] = $_REQUEST['DocID'];
                    $data .= ' AND a.DocID = ' . $_REQUEST['DocID'] . ' ';
                }else{
                    $this->setError('30502');
                }
            }
            if ($_REQUEST['SubjectID']) {
                if($this->ifSubject && !in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712',0); //您不能搜索非所属学科文档！
                    return;
                }
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $data .= ' AND a.SubjectID = ' . $_REQUEST['SubjectID'] . ' ';
            }
            if ($_REQUEST['DocYear']) {
                $map['DocYear'] = $_REQUEST['DocYear'];
                $data .= ' AND a.DocYear = ' . $_REQUEST['DocYear'] . ' ';
            }
            if ($_REQUEST['TypeID']) {
                $map['TypeID'] = $_REQUEST['TypeID'];
                $data .= ' AND a.TypeID = ' . $_REQUEST['TypeID'] . ' ';
            }
            if (current($_REQUEST['AreaID'])) {
                $map['AreaID'] = $_REQUEST['AreaID'];
                $data .= ' AND t.AreaID = ' . current($_REQUEST['AreaID']). ' ';
            }
            if (is_numeric($_REQUEST['Status'])) {
                $map['Status'] = $_REQUEST['Status'];
                $data .= ' AND a.Status = "' . $_REQUEST['Status'] . '" ';
            }
            if (is_numeric($_REQUEST['IfIntro'])) {
                $map['IfIntro'] = $_REQUEST['IfIntro'];
                $data .= ' AND a.IfIntro = "' . $_REQUEST['IfIntro'] . '" ';
            }
            if(is_numeric($_REQUEST['ShowWhere'])){
                $map['ShowWhere'] = $_REQUEST['ShowWhere'];
                $data .= ' AND a.ShowWhere = "' . $_REQUEST['ShowWhere'] . '" ';
            }
        }
		$data .= ' AND a.is_xls = 0';
        $perpage = C('WLN_PERPAGE');
        // 查询满足要求的总记录数
        if (array_filter($_REQUEST['AreaID'])) {
            $count = D('Base')->unionSelect('docSelectCount',$data);
        }else{
            $count = $this->getModel('Doc')->selectCount($data,'a.DocID',' a');
        }
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page = page($count,$_GET['p'],$perpage).','.$perpage;

        $list = D('Base')->unionSelect('docPageData',$data, $page);

		// dump($list);die;
        $this->pageList($count, $perpage, $map);
        //获取缓存数据
        $subjectArray=SS('subject');
        $gradeArray=SS('grade');
        $subjectParentArray = SS('subjectParentId');; //获取学科数据集
        $docTypeArray = SS('docType');
        $areaArray = SS('areaChildList');
        $areaArray=$areaArray[0];
        $arrayBuffer=SS('areaList');
		
        if($list){
            foreach($list as $i=>$iList){
                $tmpArr=explode(',',$iList['AreaID']);
                $tmpArrX=array();
                if($tmpArr){
                    foreach($tmpArr as $jTmpArr){
                        $tmpArrX[]=$arrayBuffer[$jTmpArr]['AreaName'];
                    }
                    krsort($tmpArrX);
                }
                $list[$i]['SubjectName']=$subjectArray[$subjectArray[$list[$i]['SubjectID']]['PID']]['SubjectName'].$subjectArray[$list[$i]['SubjectID']]['SubjectName'];
                $list[$i]['GradeName']=$gradeArray[$list[$i]['GradeID']]['GradeName'];
                $list[$i]['TypeName']=$docTypeArray[$list[$i]['TypeID']]['TypeName'];
                $list[$i]['AreaName']=implode(',',$tmpArrX);
                $list[$i]['IfGet']=0;
                $list[$i]['Hearing'] = (int)$list[$i]['Hearing'];
                switch($list[$i]['ShowWhere']){
                    case 0:
                        $list[$i]['ShowWhere']='【组卷专用】';
                        break;
                    case 1:
                        $list[$i]['ShowWhere']='【通用】';
                        break;
                    case 2:
                        $list[$i]['ShowWhere']='【提分专用】';
                        break;
                    case 3:
                        $list[$i]['ShowWhere']='【前台禁用】';
                        break;
                }
                if($list[$i]['IfIntro']==0){
                    $buffer=$this->getModel('TestAttr')->selectData(
                        'TestID',
                        'DocID='.$list[$i]['DocID']);
                    if($buffer) $list[$i]['IfGet']=1;
                    else{
                        $buffer=$this->getModel('TestAttrReal')->selectData(
                            'TestID',
                            'DocID='.$list[$i]['DocID']);
                        if($buffer) $list[$i]['IfGet']=1;
                    }
                }else{
                    $list[$i]['IfGet']=1;
                }
            }
        }

        $gradeArray=SS('grade');
        /*载入模板标签*/
        $thisYear=date('Y',time())+1;//当前年份+1，从下一年开始循环
        $this->assign('thisYear', $thisYear);

		$this->assign('list', $list); // 赋值数据集
        $this->assign('subjectArray', $subjectParentArray);
        $this->assign('docTypeArray', $docTypeArray);
        $this->assign('gradeArray', $gradeArray);
        $this->assign('areaArray', $areaArray);
        $this->assign('pageName', $pageName); //页面标题
		$this->display();






    }
	/**
     * 浏览文档列表
     * @author demo
     */
    public function xlslist() {
        $pageName = '文档管理';
        $doc = $this->getModel('Doc');
        $map = array ();
        //浏览谁的文档
        $data = ' 1=1 ';
        //验证用户权限
        if ($this->ifSubject && $this->mySubject){
            $data .= ' and a.SubjectID in (' . $this->mySubject . ') ';
        }else if($this->ifDiff){
            $data .= ' and a.Admin="' . $this->getCookieUserName() . '" ';
        }
        if ($_REQUEST['name']) {
            $_REQUEST['name'] = formatString('decodeUrl',$_REQUEST['name']);
            //简单查询
            $map['name'] = formatString('encodeUrl',$_REQUEST['name']);
            $data .= ' AND a.DocName like "%' . $_REQUEST['name'] . '%" ';
        } else {
            //高级查询
            if ($_REQUEST['Admin']) {
                if($this->ifDiff && $this->getCookieUserName()!=$_REQUEST['Admin']){
                    $this->setError('30313',0); //您没有权限查看该内容！
                    return;
                }
                $map['Admin'] = $_REQUEST['Admin'];
                $data .= ' AND a.Admin = "' . $_REQUEST['Admin'] . '" ';
            }
            if ($_REQUEST['DocName']) {
                $map['DocName'] = $_REQUEST['DocName'];
                $data .= ' AND a.DocName like "%' . $_REQUEST['DocName'] . '%" ';
            }
            if ($_REQUEST['GradeID']) {
                $map['GradeID'] = $_REQUEST['GradeID'];
                $data .= ' AND a.GradeID = ' . $_REQUEST['GradeID'] . ' ';
            }
            if ($_REQUEST['DocID']) {
                if(is_numeric($_REQUEST['DocID'])){
                    $map['DocID'] = $_REQUEST['DocID'];
                    $data .= ' AND a.DocID = ' . $_REQUEST['DocID'] . ' ';
                }else{
                    $this->setError('30502');
                }
            }
            if ($_REQUEST['SubjectID']) {
                if($this->ifSubject && !in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712',0); //您不能搜索非所属学科文档！
                    return;
                }
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $data .= ' AND a.SubjectID = ' . $_REQUEST['SubjectID'] . ' ';
            }
            if ($_REQUEST['DocYear']) {
                $map['DocYear'] = $_REQUEST['DocYear'];
                $data .= ' AND a.DocYear = ' . $_REQUEST['DocYear'] . ' ';
            }
            if ($_REQUEST['TypeID']) {
                $map['TypeID'] = $_REQUEST['TypeID'];
                $data .= ' AND a.TypeID = ' . $_REQUEST['TypeID'] . ' ';
            }
            if (current($_REQUEST['AreaID'])) {
                $map['AreaID'] = $_REQUEST['AreaID'];
                $data .= ' AND t.AreaID = ' . current($_REQUEST['AreaID']). ' ';
            }
            if (is_numeric($_REQUEST['Status'])) {
                $map['Status'] = $_REQUEST['Status'];
                $data .= ' AND a.Status = "' . $_REQUEST['Status'] . '" ';
            }
            if (is_numeric($_REQUEST['IfIntro'])) {
                $map['IfIntro'] = $_REQUEST['IfIntro'];
                $data .= ' AND a.IfIntro = "' . $_REQUEST['IfIntro'] . '" ';
            }
            if(is_numeric($_REQUEST['ShowWhere'])){
                $map['ShowWhere'] = $_REQUEST['ShowWhere'];
                $data .= ' AND a.ShowWhere = "' . $_REQUEST['ShowWhere'] . '" ';
            }
        }
		$data .= ' AND a.is_xls = 1';
        $perpage = C('WLN_PERPAGE');
        // 查询满足要求的总记录数
        if (array_filter($_REQUEST['AreaID'])) {
            $count = D('Base')->unionSelect('docSelectCount',$data);
        }else{
            $count = $this->getModel('Doc')->selectCount($data,'a.DocID',' a');
        }
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page = page($count,$_GET['p'],$perpage).','.$perpage;

        $list = D('Base')->unionSelect('docPageData',$data, $page);
        $this->pageList($count, $perpage, $map);
        //获取缓存数据
        $subjectArray=SS('subject');
        $gradeArray=SS('grade');
        $subjectParentArray = SS('subjectParentId');; //获取学科数据集
        $docTypeArray = SS('docType');
        $areaArray = SS('areaChildList');
        $areaArray=$areaArray[0];
        $arrayBuffer=SS('areaList');
        if($list){
            foreach($list as $i=>$iList){
                $tmpArr=explode(',',$iList['AreaID']);
                $tmpArrX=array();
                if($tmpArr){
                    foreach($tmpArr as $jTmpArr){
                        $tmpArrX[]=$arrayBuffer[$jTmpArr]['AreaName'];
                    }
                    krsort($tmpArrX);
                }
                $list[$i]['SubjectName']=$subjectArray[$subjectArray[$list[$i]['SubjectID']]['PID']]['SubjectName'].$subjectArray[$list[$i]['SubjectID']]['SubjectName'];
                $list[$i]['GradeName']=$gradeArray[$list[$i]['GradeID']]['GradeName'];
                $list[$i]['TypeName']=$docTypeArray[$list[$i]['TypeID']]['TypeName'];
                $list[$i]['AreaName']=implode(',',$tmpArrX);
                $list[$i]['IfGet']=0;
                $list[$i]['Hearing'] = (int)$list[$i]['Hearing'];
                switch($list[$i]['ShowWhere']){
                    case 0:
                        $list[$i]['ShowWhere']='【组卷专用】';
                        break;
                    case 1:
                        $list[$i]['ShowWhere']='【通用】';
                        break;
                    case 2:
                        $list[$i]['ShowWhere']='【提分专用】';
                        break;
                    case 3:
                        $list[$i]['ShowWhere']='【前台禁用】';
                        break;
                }
                if($list[$i]['IfIntro']==0){
                    $buffer=$this->getModel('TestAttr')->selectData(
                        'TestID',
                        'DocID='.$list[$i]['DocID']);
                    if($buffer) $list[$i]['IfGet']=1;
                    else{
                        $buffer=$this->getModel('TestAttrReal')->selectData(
                            'TestID',
                            'DocID='.$list[$i]['DocID']);
                        if($buffer) $list[$i]['IfGet']=1;
                    }
                }else{
                    $list[$i]['IfGet']=1;
                }
            }
        }

        $gradeArray=SS('grade');
        /*载入模板标签*/
        $thisYear=date('Y',time())+1;//当前年份+1，从下一年开始循环
        $this->assign('thisYear', $thisYear);
		$this->assign('list', $list); // 赋值数据集
        $this->assign('subjectArray', $subjectParentArray);
        $this->assign('docTypeArray', $docTypeArray);
        $this->assign('gradeArray', $gradeArray);
        $this->assign('areaArray', $areaArray);
        $this->assign('pageName', $pageName); //页面标题
		$this->display();

    }
    /**
     * 添加文档
     * @author demo
     */
    public function add() {
        $pageName = '添加word文档';
        $act = 'add'; //模板标识
        //获取学科数据集
        $subjectArray = SS('subjectParentId');
        //获取属性数据
        $docTypeArray=SS('docType');
        //获取省份数据集
        $areaArray = SS('areaChildList');
        $docSource=SS('docSource');
        $thisYear=date('Y',time())+1;//当前年份+1，从下一年开始循环
        $this->assign('thisYear', $thisYear);
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray);
        $this->assign('docTypeArray', $docTypeArray);
        $this->assign('docSource', $docSource);
        $this->assign('areaArray', $areaArray[0]);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
	/**
     * 添加xls文档
     * @author wjx
     */
    public function addex() {
        $pageName = '添加excel文档';
        $act = 'addex'; //模板标识
        //获取学科数据集
        $subjectArray = SS('subjectParentId');
        //获取属性数据
        $docTypeArray=SS('docType');
        //获取省份数据集
        $areaArray = SS('areaChildList');
        $docSource=SS('docSource');
        $thisYear=date('Y',time())+1;//当前年份+1，从下一年开始循环
        $this->assign('thisYear', $thisYear);
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray);
        $this->assign('docTypeArray', $docTypeArray);
        $this->assign('docSource', $docSource);
        $this->assign('areaArray', $areaArray[0]);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑文档
     * @author demo
     */
    public function edit() {
        $pageName = '编辑word文档';
        $docID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($docID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        //查询文档数据
        $where='DocID=' . $docID;
        $act = 'edit'; //模板标识
        $edit = $this->getModel('Doc')->selectData(
            '*',
            $where,
            '',
            1);
        //编辑谁的文档
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30812'); //您不能编辑非所属学科文档！
            }
        }else if($this->ifDiff){
            //判断是否可以编辑
            if ($edit[0]['Admin'] != $this->getCookieUserName()) {
                $this->setError('30313'); //您没有权限编辑！
            }
        }
        //获取该文档能力属性及章节属性
        $dacResult=$this->getModel('DocAbiCapt')->selectData(
            '*',
            $where);
        $edit[0]['AbilitID']=$dacResult[0]['AbilitID'];
        $edit[0]['CaptID']=$dacResult[0]['CaptID'];
        $edit[0]['DacID']=$dacResult[0]['DacID'];
        $captID=$edit[0]['CaptID'];
        unset($dacResult);

        $param=array();
        $param['style']='chapter';
        $param['subjectID']=$edit[0]['SubjectID'];
        $param['return']=2;
        $chapterArray=$this->getData($param);//获取版本

        //从缓存获取对应的父章节内容
        if($captID){
            $buffer=SS('chapterParentPath');  // 缓存父类路径数据
            $chapterParentStr='';//父类路径包括自己
            $bufferTmp=array();
            if($buffer[$edit[0]['CaptID']]) krsort($buffer[$edit[0]['CaptID']]);
            if($buffer[$edit[0]['CaptID']]){
                foreach($buffer[$edit[0]['CaptID']] as $iBuffer){
                    $bufferTmp[]=$iBuffer['ChapterID'];
                }
                $chapterParentStr='|'.implode('|',$bufferTmp).'|c'.$edit[0]['CaptID'].'|';
            }else{
                $chapterParentStr='|'.$edit[0]['CaptID'].'|';
            }
        }

        //获取年级数据
        $classGrade=SS('gradeListSubject');
        $subject=SS('subject');
        $gradeArray=$classGrade[$subject[$edit[0]['SubjectID']]['PID']];
        unset($classGrade);
        unset($subject);
        //转换文档路径
        $host=C('WLN_DOC_HOST');
        if($host && !empty($edit[0]['DocHtmlPath'])){
            $edit[0]['DocHtmlPath']=$host.$edit[0]['DocHtmlPath'];
        }
        //文档所属地区
        $docAreaBuffer=$this->getModel('DocArea')->selectData(
            '*',
            'DocID='.$docID);
        if($docAreaBuffer){
            foreach($docAreaBuffer as $iDocAreaBuffer){
                $tmpArr[]=$iDocAreaBuffer['AreaID'];
            }
            $edit[0]['AreaList']=implode(',',$tmpArr);
        }
        $data = $this->getModel('DocHearing')->findData('DocID', 'DocID='.$docID);
        if(!empty($data)){
            $edit[0]['Hearing'] = (int)$data['DocID'];
        }
        unset($docAreaBuffer);
        //获取学科数据集
        $subjectArray = SS('subjectParentId');
        unset($subject);
        //获取属性数据
        $docTypeArray= SS('docType');
        //获取省份数据集
        $areaArray = SS('areaChildList');
        //获取能力数据集
        $ability=SS('abilitySubject');
        $docSource=SS('docSource');
        $thisYear=date('Y',time())+1;//当前年份+1，从下一年开始循环
        $this->assign('thisYear', $thisYear);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('grade',$gradeArray['sub']);
        $this->assign('subjectArray', $subjectArray);
        $this->assign('chapterArray', $chapterArray);
        $this->assign('docSource', $docSource);
        $this->assign('chapterParentStr', $chapterParentStr);
        $this->assign('docTypeArray', $docTypeArray);
        $this->assign('areaArray', $areaArray[0]);
        $this->assign('ability',$ability[$edit[0]['SubjectID']]);
        $this->assign('pageName', $pageName);
        $this->display('WlnDoc/add');
    }
	/**
     * 编辑xls文档
     * @author wjx
     */
    public function editxls() {
        $pageName = '编辑xls文档';
        $docID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($docID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        //查询文档数据
        $where='DocID=' . $docID;
        $act = 'edit'; //模板标识
        $edit = $this->getModel('Doc')->selectData(
            '*',
            $where,
            '',
            1);
        //编辑谁的文档
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30812'); //您不能编辑非所属学科文档！
            }
        }else if($this->ifDiff){
            //判断是否可以编辑
            if ($edit[0]['Admin'] != $this->getCookieUserName()) {
                $this->setError('30313'); //您没有权限编辑！
            }
        }
        //获取该文档能力属性及章节属性
        $dacResult=$this->getModel('DocAbiCapt')->selectData(
            '*',
            $where);
        $edit[0]['AbilitID']=$dacResult[0]['AbilitID'];
        $edit[0]['CaptID']=$dacResult[0]['CaptID'];
        $edit[0]['DacID']=$dacResult[0]['DacID'];
        $captID=$edit[0]['CaptID'];
        unset($dacResult);

        $param=array();
        $param['style']='chapter';
        $param['subjectID']=$edit[0]['SubjectID'];
        $param['return']=2;
        $chapterArray=$this->getData($param);//获取版本

        //从缓存获取对应的父章节内容
        if($captID){
            $buffer=SS('chapterParentPath');  // 缓存父类路径数据
            $chapterParentStr='';//父类路径包括自己
            $bufferTmp=array();
            if($buffer[$edit[0]['CaptID']]) krsort($buffer[$edit[0]['CaptID']]);
            if($buffer[$edit[0]['CaptID']]){
                foreach($buffer[$edit[0]['CaptID']] as $iBuffer){
                    $bufferTmp[]=$iBuffer['ChapterID'];
                }
                $chapterParentStr='|'.implode('|',$bufferTmp).'|c'.$edit[0]['CaptID'].'|';
            }else{
                $chapterParentStr='|'.$edit[0]['CaptID'].'|';
            }
        }

        //获取年级数据
        $classGrade=SS('gradeListSubject');
        $subject=SS('subject');
        $gradeArray=$classGrade[$subject[$edit[0]['SubjectID']]['PID']];
        unset($classGrade);
        unset($subject);
        //转换文档路径
        $host=C('WLN_DOC_HOST');
        if($host && !empty($edit[0]['DocHtmlPath'])){
            $edit[0]['DocHtmlPath']=$host.$edit[0]['DocHtmlPath'];
        }
        //文档所属地区
        $docAreaBuffer=$this->getModel('DocArea')->selectData(
            '*',
            'DocID='.$docID);
        if($docAreaBuffer){
            foreach($docAreaBuffer as $iDocAreaBuffer){
                $tmpArr[]=$iDocAreaBuffer['AreaID'];
            }
            $edit[0]['AreaList']=implode(',',$tmpArr);
        }
        $data = $this->getModel('DocHearing')->findData('DocID', 'DocID='.$docID);
        if(!empty($data)){
            $edit[0]['Hearing'] = (int)$data['DocID'];
        }
        unset($docAreaBuffer);
        //获取学科数据集
        $subjectArray = SS('subjectParentId');
        unset($subject);
        //获取属性数据
        $docTypeArray= SS('docType');
        //获取省份数据集
        $areaArray = SS('areaChildList');
        //获取能力数据集
        $ability=SS('abilitySubject');
        $docSource=SS('docSource');
        $thisYear=date('Y',time())+1;//当前年份+1，从下一年开始循环
        $this->assign('thisYear', $thisYear);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('grade',$gradeArray['sub']);
        $this->assign('subjectArray', $subjectArray);
        $this->assign('chapterArray', $chapterArray);
        $this->assign('docSource', $docSource);
        $this->assign('chapterParentStr', $chapterParentStr);
        $this->assign('docTypeArray', $docTypeArray);
        $this->assign('areaArray', $areaArray[0]);
        $this->assign('ability',$ability[$edit[0]['SubjectID']]);
        $this->assign('pageName', $pageName);
        $this->display('WlnDoc/addex');
    }
    /**
     * 查看文档word和mht
     * @author demo
     */
    public function showWord(){
        $docID=$_GET['docID'];
        $style=$_GET['style'];
        //判断数据标识
        if (empty ($docID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        //查询文档数据
        $where='DocID=' . $docID;
        $edit = $this->getModel('Doc')->selectData(
            '*',
            $where,
            '',
            1);

        //编辑谁的文档
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30812'); //您不能编辑非所属学科文档！
            }
        }else if($this->ifDiff){
            //判断是否可以编辑
            if ($edit[0]['Admin'] != $this->getCookieUserName()) {
                $this->setError('30313'); //您没有权限编辑！
            }
        }

        //获取文档浏览和下载路径
        $doc=$this->getModel('Doc');
        if($style){
            if(empty($edit[0]['DocPath']) || strstr($edit[0]['DocPath'],'error')){
                $this->setError('30706'); //该文件不存在
            }

            $url=C('WLN_DOC_HOST').R('Common/UploadLayer/getDocServerUrl',array($edit[0]['DocPath'],'down','word',$edit[0]['DocName']));
            header('location:'.$url);
        }else{
            if(empty($edit[0]['DocHtmlPath'])){
                $this->setError('30813'); //文档未转换，请提取后预览
            }

            $urlHtml=C('WLN_DOC_HOST_IN').R('Common/UploadLayer/getDocServerUrl',array($edit[0]['DocHtmlPath'],'getWordFile','word',$edit[0]['DocName']));

            header("Content-type: text/html; charset=GBK");
            //对图片路径进行转换
            $strHtml=file_get_contents($urlHtml);
            $strHtml=$doc->changeImgPath($edit[0]['DocFilePath'],$strHtml);
            $strHtml=R('Common/TestLayer/strFormat',array($strHtml));
            echo $strHtml;
        }
    }

	/**
     * 保存文档
     * @author demo
     */
    public function save() {
        //括号里数字代表程序执行时间，为0 说明程序永久执行直到结束
        set_time_limit(0);
        //验证章节和能力
        $capter=$_POST['chapterID'];
        if(strstr('c',end($capter)) && $_POST['Ability']!=''){
            $this->setError('30712',0); //请选择最终章节！
        }
        if($capter=="" && $_POST['Ability']==''){
            $this->setError('1X1012',0); //请选择能力属性
        }

        //判断数据标识
        $docID = $_POST['DocID']; //获取数据标识
		// dump($docID);die;
        $act = $_POST['act']; //获取模板标识
        if (empty ($docID) && $act == 'edit') {
            $this->setError('1X1012',0); //数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30712',0); //模板标识不能为空！
        }

        $data = array ();
		$data['is_xls']='0';
        $data['DocName'] = $_POST['DocName'];
        $data['SubjectID'] = $_POST['SubjectID'];
        $data['Description'] = $_POST['Description'];
        $data['TypeID'] = $_POST['TypeID'];
        $data['DocYear'] = $_POST['DocYear'];
        $data['TotalScore'] = $_POST['TotalScore'];
        $data['TestTime'] = $_POST['TestTime'];
        $data['IfTest'] = $_POST['IfTest'];
        $data['GradeID']=is_numeric($_POST['DocGrade']) ? $_POST['DocGrade'] : 0;
        $data['ShowWhere']=$_POST['ShowWhere'];
        $data['IfRecom']=$_POST['IfRecom'];
        $data['Status'] = $_POST['Status'];
        $data['AatTestStyle'] = $_POST['AatTestStyle'];
        $data['Admin'] = $this->getCookieUserName();//获取cookie里存储的用户名
        if(!empty($_POST['SourceID'])) $data['SourceID'] = $_POST['SourceID'];

        //验证文档名称重复及权限，通过隐藏域传过来的
        if($act=='add'){
            $subjectIDData=$data['SubjectID'];
            $adminData='';

            $where='DocName="'.$data['DocName'].'"';
        }else{
            $buffer=$this->getModel('Doc')->selectData(
                'Admin,SubjectID',
                'DocID="'.$docID.'"');
            $subjectIDData=array($buffer[0]['SubjectID'],$data['SubjectID']);
            $adminData=$buffer[0]['Admin'];

            $where='DocName="'.$data['DocName'].'" and DocID != '.$docID;
			// $where='DocName="'.$data['DocName'];
        }
        $this->powerCheckSubject($subjectIDData,$adminData,0);

        $buffer=$this->getModel('Doc')->selectData(
            '*',
            $where);
        if($buffer){
            $this->setError('30817',0); //文档名称重复！
        }

        $doc = $this->getModel('Doc');

        //处理上传文件
        if (!empty ($_FILES['photo']['name']) && !empty ($_FILES['photo']['size'])) {

			$output=R('Common/UploadLayer/uploadWordAndCheck', array(true));
			// dump($output);die;
            $data['DocPath']=$output[0];
        }else{
            if($act == 'add')
                $this->setError('1X1011'); //1X1011:word 1X101X:excel
        }
		// dump($docID);
        //获取能力章节数据
        $captID=str_replace('c','',end($capter));
        $ability=$_POST['Ability'];
        $error=''; //记录文档对应章节和能力是否添加成功，error等于空 说明添加成功
        $adminId = $this->getCookieUserID();
        if ($act == 'add') {
            $data['LoadTime'] = time();
            //写入数据库
            if (($docID=$this->getModel('Doc')->insertData(
                    $data)) === false) {
                $this->setError('30310',0); //添加失败！
            } else {
                //----开始上传听力文件----
                $info = $this->getModel('DocHearing')->fileUpload($docID,$adminId);
                if(is_array($info)){
                    $this->setError($info[0], 0, '', $info[1]);
                }
                if('success' != $info){
                    $this->setError('1X1016',0);
                }
                //----结束上传听力文件----
                //添加中间表数据
                $areaList=$_POST['AreaID'];
                if(empty($areaList)){
                    $areaList=0;
                }
				//不是数组给他转换为数组，然后存入zj_docarea表
                if(!is_array($areaList)){
                    $areaList=array($areaList);
                }
                foreach($areaList as $iAreaList){
                    $dataArea=array();
                    $dataArea['DocID']=$docID;
                    $dataArea['AreaID']=$iAreaList;
					// dump($dataArea);
                    $this->getModel('DocArea')->insertData(
                        $dataArea);
                }
				// die;
                if(!empty($ability) && !empty($captID)){
                    $buffer=$this->getModel('DocAbiCapt')->selectData(
                        '*',
                        'CaptID='.$captID.' and AbilitID='.$ability);
                    if(!$buffer){
                        $logData=array();
                        $logData['DocID']=$docID;
                        $logData['CaptID']=$captID;
                        $logData['AbilitID']=$ability;
                        $this->getModel('DocAbiCapt')->insertData(
                            $logData);
                    }else{
                        $error='错误信息：本章节下已经存在对应能力的文档；请勿重复添加!';
                    }

                }
				// dump($docID);die;
                $doc->setAatTestStyle($docID,$data['AatTestStyle']);
                //写入日志
                $this->adminLog($this->moduleName, '添加文档【' . $_POST['DocName'] . '】');
                $this->showSuccess('添加word文档成功！'.$error, __URL__);
            }
        } elseif ($act == 'edit') {
            //删除上传文件
            if (!empty ($_FILES['photo']['name']) && !empty ($_FILES['photo']['size'])) {
                $doc->deleteFile($docID);
            }else{
                if($data['IfTest']==1){
                    //判断试题是否都入库
                    $testNum=$this->getModel('TestAttr')->selectCount(
                        'DocID='.$docID,
                        'TestID');
                    $testRealNum=$this->getModel('TestAttrReal')->selectCount(
                        'DocID='.$docID,
                        'TestID');
                    if($testNum==0 and $testRealNum>10){
                        $data['IfTest']=2;//全部入库并可测试
                        $data['IntroTime'] = time(); //更新索引
                    }
                }
            }
            //----开始上传听力文件----
            $info = $this->getModel('DocHearing')->fileUpload($docID,$adminId,array('maxSize'=>100));
            if(is_array($info)){
                $this->setError($info[0], 0, '', $info[1]);
            }
            if('success' != $info){
                $this->setError('1X1016',0);
            }
            //----结束上传听力文件----
            $data['DocID'] = $docID;
            $buffer=$this->getModel('Doc')->selectData(
                'DocID,SubjectID,GradeID',
                'DocID='.$docID);
            $gradeID=$data['GradeID']; //当前年级
            $subjectID=$data['SubjectID']; //当前学科
            $showWhere=$data['ShowWhere']; //当前使用范围
            $oldGradeID=$buffer[0]['GradeID']; //修改前年级
            $oldSubjectID=$buffer[0]['SubjectID']; //修改前学科
            $oldShowWhere=$buffer[0]['ShowWhere']; //修改前的使用范围
            if ($this->getModel('Doc')->updateData(
                    $data,
                    'DocID='.$docID) === false) {
                $this->setError('30311',0); //修改失败！
            } else {
                //更新章节能力中间表数据
                if(!empty($ability) && !empty($captID)){
                    $buffer=$this->getModel('DocAbiCapt')->selectData( //每个章节，只能对应一个文档
                        '*',
                        'CaptID='.$captID.' and AbilitID='.$ability);
                    if(empty($buffer[0]['DocID']) || $buffer[0]['DocID']==$docID ){
                        $logData['DocID']=$docID;
                        $logData['CaptID']=$captID;
                        $logData['AbilitID']=$ability;
                        if(!empty($_POST['DacID'])){
                            $logData['DacID']=$_POST['DacID'];
                            $this->getModel('DocAbiCapt')->updateData(
                                $logData,
                                'DacID='.$_POST['DacID']);
                        }else{
                            $this->getModel('DocAbiCapt')->insertData(
                                $logData);
                        }
                    }else{
                        $error='错误信息：本章节下已经存在对应能力的文档；请勿重复添加!';
                    }
                }else{
                    if(!empty($_POST['DacID'])){
                        $this->getModel('DocAbiCapt')->deleteData('DacID='.$_POST['DacID']);
                    }
                }
                //更新地区中间表数据
                $areaList=$_POST['AreaID'];
                if(empty($areaList)){
                    $areaList=0;
                }
                if(!is_array($areaList)){
                    $areaList=array($areaList);
                }

                $buffer=$this->getModel('DocArea')->selectData(
                    '*',
                    'DocID='.$docID); //查询该文档所在的所有地区
                if($buffer){
                    for($i=0;$i<count($buffer);$i++){
                        if($i>=count($areaList)){
                            $this->getModel('DocArea')->deleteData(
                                'DAID='.$buffer[$i]['DAID']);
                            continue;
                        }
                        $dataArea=array();
                        $dataArea['DocID']=$docID;
                        $dataArea['AreaID']=$areaList[$i];
                        $dataArea['DAID']=$buffer[$i]['DAID'];
                        $this->getModel('DocArea')->updateData(
                            $dataArea,
                            'DAID='.$buffer[$i]['DAID']);
                    }
                    if($i<count($areaList)){
                        for(;$i<count($areaList);$i++){
                            $dataArea=array();
                            $dataArea['DocID']=$docID;
                            $dataArea['AreaID']=$areaList[$i];
                            $this->getModel('DocArea')->insertData($dataArea);
                        }
                    }
                }else{
                    for($i=0;$i<count($areaList);$i++){
                        $dataArea=array();
                        $dataArea['DocID']=$docID;
                        $dataArea['AreaID']=$areaList[$i];
                        $this->getModel('DocArea')->insertData($dataArea);
                    }
                }
                $flagAttr=0;
                $editData=array();
                if($oldGradeID!=$gradeID){
                    //如果修改年级，则修改test_attr及test_attr_real
                    $where="DocID=".$docID;
                    $editData['GradeID']=$gradeID;
                    $flagAttr=1;
                }
                if($oldSubjectID!=$subjectID){
                    //如果修改学科，则修改test_attr及test_attr_real
                    $where="DocID=".$docID;
                    $editData['SubjectID']=$subjectID;
                    $flagAttr=1;
                }
                if($oldShowWhere!=$showWhere){
                    //如果修改使用范围，则修改test_attr及test_attr_real
                    $where='DocID='.$docID;
                    $editData['ShowWhere']=$showWhere;
                    $flagAttr=1;
                }
                if($flagAttr==1){
                    $this->getModel('TestAttr')->updateData(
                        $editData,
                        $where);
                    $this->getModel('TestAttrReal')->updateData(
                        $editData,
                        $where);
                }

                $doc->setAatTestStyle($docID,$data['AatTestStyle']);
                //写入日志
                $this->adminLog($this->moduleName, '修改文档DocID为【' . $_POST['DocID'] . '】的数据');
                $this->showSuccess('修改成功！'.$error, U('Doc/WlnDoc/edit',array('id'=>$docID)));
            }
        }
    }
	/*
	* savexls保存xls文件
	* @author wjx
	*/
	
	public function savexls(){
		//括号里数字代表程序执行时间，为0 说明程序永久执行直到结束
        set_time_limit(0);
        //验证章节和能力
        $capter=$_POST['chapterID'];
        // if(strstr('c',end($capter)) && $_POST['Ability']!=''){
            // $this->setError('30712',0); //请选择最终章节！
        // }
        // if($capter=="" && $_POST['Ability']==''){
            // $this->setError('1X1012',0); //请选择能力属性
        // }

        // //判断数据标识
        $docID = $_POST['DocID']; //获取数据标识

        $act = $_POST['act']; //获取模板标识
		// dump($act);die;
        if (empty ($docID) && $act == 'edit') {
            $this->setError('1X1012',0); //数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30712',0); //模板标识不能为空！
        }
        $data = array ();
        $data['DocName'] = $_POST['DocName'];
        $data['SubjectID'] = $_POST['SubjectID'];
        $data['Description'] = $_POST['Description'];
        $data['TypeID'] = $_POST['TypeID'];
        $data['DocYear'] = $_POST['DocYear'];
        $data['TotalScore'] = $_POST['TotalScore'];
        $data['TestTime'] = $_POST['TestTime'];
        $data['IfTest'] = $_POST['IfTest'];
        $data['GradeID']=is_numeric($_POST['DocGrade']) ? $_POST['DocGrade'] : 0;
        $data['ShowWhere']=$_POST['ShowWhere'];
        $data['IfRecom']=$_POST['IfRecom'];
        $data['Status'] = $_POST['Status'];
        $data['AatTestStyle'] = $_POST['AatTestStyle'];
		$data['is_xls']='1';
        $data['Admin'] = $this->getCookieUserName();//获取cookie里存储的用户名
        if(!empty($_POST['SourceID'])) $data['SourceID'] = $_POST['SourceID'];

        //验证文档名称重复及权限，通过隐藏域传过来的
        if($act=='addex'){
            $subjectIDData=$data['SubjectID'];
            $adminData='';

            $where='DocName="'.$data['DocName'].'"';
        }else{
            $buffer=$this->getModel('Doc')->selectData(
                'Admin,SubjectID',
                'DocID="'.$docID.'"');

            $subjectIDData=array($buffer[0]['SubjectID'],$data['SubjectID']);
            $adminData=$buffer[0]['Admin'];

            $where='DocName="'.$data['DocName'].'" and DocID != '.$docID;
        }
		//检验学科权限是否正常
        $this->powerCheckSubject($subjectIDData,$adminData,0);
        $buffer=$this->getModel('Doc')->selectData(
            '*',
            $where);
        if($buffer){
            $this->setError('30817',0); //文档名称重复！
        }
        $doc = $this->getModel('Doc');
		// dump($_FILES['photo']['name']);die;
		//处理上传文件
		//&& !empty ($_FILES['photo']['size'])
		if (!empty ($_FILES['photo']['name']) && !empty ($_FILES['photo']['size'])) {
			
		    $output=R('Common/UploadLayer/uploadExcel',array(true));//文件路径
			
			// $output1 = R('Common/UploadLayer/uploadexcelToServer($output)',array(true));
			// dump($output);die;
			
			$data['DocPath']=$output;
			$data1 = array ();
			$data1['DocName'] = $_POST['DocName'];
			$data1['DocPath']=$output;
			$data1['LoadTime']=time();
			// dump($output);die;
			}else{
				if($act == 'addex')
				$this->setError('1X101X'); //1X1011:word 1X101X:excel
		}
        //获取能力章节数据
        $captID=str_replace('c','',end($capter));
        $ability=$_POST['Ability'];
        $error=''; //记录文档对应章节和能力是否添加成功，error等于空 说明添加成功
        $adminId = $this->getCookieUserID();
        if ($act == 'addex') {
            $data['LoadTime'] = time();
            //写入数据库
            if (($docID=$this->getModel('Doc')->insertData(
                    $data)) === false) {
                $this->setError('30310',0); //添加失败！
            } else {

                //----开始上传听力文件----
                // $info = $this->getModel('DocHearing')->fileUpload($docID,$adminId);
                // if(is_array($info)){
                    // $this->setError($info[0], 0, '', $info[1]);
                // }
                // if('success' != $info){
                    // $this->setError('1X1016',0);
                // }
                //----结束上传听力文件----
                //添加中间表数据 area表
                $areaList=$_POST['AreaID'];
                if(empty($areaList)){
                    $areaList=0;
                }
				//不是数组给他转换为数组，然后存入zj_docarea表
                if(!is_array($areaList)){
                    $areaList=array($areaList);
                }
                foreach($areaList as $iAreaList){
                    $dataArea=array();
                    $dataArea['DocID']=$docID;
                    $dataArea['AreaID']=$iAreaList;
					// dump($dataArea);
                    $this->getModel('DocArea')->insertData(
                        $dataArea);
                }


                if(!empty($ability) && !empty($captID)){
                    $buffer=$this->getModel('DocAbiCapt')->selectData(
                        '*',
                        'CaptID='.$captID.' and AbilitID='.$ability);
                    if(!$buffer){
                        $logData=array();
                        $logData['DocID']=$docID;
                        $logData['CaptID']=$captID;
                        $logData['AbilitID']=$ability;
                        $this->getModel('DocAbiCapt')->insertData(
                            $logData);
                    }else{
                        $error='错误信息：本章节下已经存在对应能力的文档；请勿重复添加!';
                    }

                }
				$data1['DocID']=$docID;
				// dump($data1);die;
				$this->getModel('xxls')->insertData($data1);
                $doc->setAatTestStyle($docID,$data['AatTestStyle']);

                //写入日志
                $this->adminLog($this->moduleName, '添加文档【' . $_POST['DocName'] . '】');
				// die(__URL__);
				
                $this->showSuccess('添加excel文档成功！'.$error, __URL__.'/xlslist');
            }
        } elseif ($act == 'edit') {
            //删除上传文件
            if (!empty ($_FILES['photo']['name']) && !empty ($_FILES['photo']['size'])) {
                $doc->deleteFile($docID);
            }else{
                if($data['IfTest']==1){
                    //判断试题是否都入库
                    $testNum=$this->getModel('TestAttr')->selectCount(
                        'DocID='.$docID,
                        'TestID');
                    $testRealNum=$this->getModel('TestAttrReal')->selectCount(
                        'DocID='.$docID,
                        'TestID');
                    if($testNum==0 and $testRealNum>10){
                        $data['IfTest']=2;//全部入库并可测试
                        $data['IntroTime'] = time(); //更新索引
                    }
                }
            }
            // //----开始上传听力文件----
            // $info = $this->getModel('DocHearing')->fileUpload($docID,$adminId,array('maxSize'=>100));
            // if(is_array($info)){
                // $this->setError($info[0], 0, '', $info[1]);
            // }
            // if('success' != $info){
                // $this->setError('1X1016',0);
            // }
            // //----结束上传听力文件----
            $data['DocID'] = $docID;
            $buffer=$this->getModel('Doc')->selectData(
                'DocID,SubjectID,GradeID',
                'DocID='.$docID);
            $gradeID=$data['GradeID']; //当前年级
            $subjectID=$data['SubjectID']; //当前学科
            $showWhere=$data['ShowWhere']; //当前使用范围
            $oldGradeID=$buffer[0]['GradeID']; //修改前年级
            $oldSubjectID=$buffer[0]['SubjectID']; //修改前学科
            $oldShowWhere=$buffer[0]['ShowWhere']; //修改前的使用范围

            if ($this->getModel('Doc')->updateData(
                    $data,
                    'DocID='.$docID) === false) {
                $this->setError('30311',0); //修改失败！
            } else {
                //更新章节能力中间表数据
                if(!empty($ability) && !empty($captID)){
                    $buffer=$this->getModel('DocAbiCapt')->selectData( //每个章节，只能对应一个文档
                        '*',
                        'CaptID='.$captID.' and AbilitID='.$ability);
                    if(empty($buffer[0]['DocID']) || $buffer[0]['DocID']==$docID ){
                        $logData['DocID']=$docID;
                        $logData['CaptID']=$captID;
                        $logData['AbilitID']=$ability;
                        if(!empty($_POST['DacID'])){
                            $logData['DacID']=$_POST['DacID'];
                            $this->getModel('DocAbiCapt')->updateData(
                                $logData,
                                'DacID='.$_POST['DacID']);
                        }else{
                            $this->getModel('DocAbiCapt')->insertData(
                                $logData);
                        }
                    }else{
                        $error='错误信息：本章节下已经存在对应能力的文档；请勿重复添加!';
                    }
                }else{
                    if(!empty($_POST['DacID'])){
                        $this->getModel('DocAbiCapt')->deleteData('DacID='.$_POST['DacID']);
                    }
                }
                //更新地区中间表数据

                $areaList=$_POST['AreaID'];
                if(empty($areaList)){
                    $areaList=0;
                }
                if(!is_array($areaList)){
                    $areaList=array($areaList);
                }

                $buffer=$this->getModel('DocArea')->selectData(
                    '*',
                    'DocID='.$docID); //查询该文档所在的所有地区

                if($buffer){
                    for($i=0;$i<count($buffer);$i++){
                        if($i>=count($areaList)){
                            $this->getModel('DocArea')->deleteData(
                                'DAID='.$buffer[$i]['DAID']);
                            continue;
                        }
                        $dataArea=array();
                        $dataArea['DocID']=$docID;
                        $dataArea['AreaID']=$areaList[$i];
                        $dataArea['DAID']=$buffer[$i]['DAID'];
                        $this->getModel('DocArea')->updateData(
                            $dataArea,
                            'DAID='.$buffer[$i]['DAID']);
                    }
                    if($i<count($areaList)){
                        for(;$i<count($areaList);$i++){
                            $dataArea=array();
                            $dataArea['DocID']=$docID;
                            $dataArea['AreaID']=$areaList[$i];
                            $this->getModel('DocArea')->insertData($dataArea);
                        }
                    }
                }else{
                    for($i=0;$i<count($areaList);$i++){
                        $dataArea=array();
                        $dataArea['DocID']=$docID;
                        $dataArea['AreaID']=$areaList[$i];
                        $this->getModel('DocArea')->insertData($dataArea);
                    }
                }
                $flagAttr=0;
                $editData=array();
                if($oldGradeID!=$gradeID){
                    //如果修改年级，则修改test_attr及test_attr_real
                    $where="DocID=".$docID;
                    $editData['GradeID']=$gradeID;
                    $flagAttr=1;
                }
                if($oldSubjectID!=$subjectID){
                    //如果修改学科，则修改test_attr及test_attr_real
                    $where="DocID=".$docID;
                    $editData['SubjectID']=$subjectID;
                    $flagAttr=1;
                }
                if($oldShowWhere!=$showWhere){
                    //如果修改使用范围，则修改test_attr及test_attr_real
                    $where='DocID='.$docID;
                    $editData['ShowWhere']=$showWhere;
                    $flagAttr=1;
                }
                if($flagAttr==1){
                    $this->getModel('TestAttr')->updateData(
                        $editData,
                        $where);
                    $this->getModel('TestAttrReal')->updateData(
                        $editData,
                        $where);
                }



                $doc->setAatTestStyle($docID,$data['AatTestStyle']);
                //写入日志
                $this->adminLog($this->moduleName, '修改文档DocID为【' . $_POST['DocID'] . '】的数据');
                $this->showSuccess('修改成功！'.$error, U('Doc/WlnDoc/editxls',array('id'=>$docID)));
            }
        }
	}
    /**
     * 下载听力
     * @author demo 16-4-21
     */
    public function downloadAudioFile(){
        $docId = (int)$_GET['docId'];
        if(empty($docId)){
            $this->setError('30301',0,__URL__); //数据标识不能为空！
        }
        $result = $this->getModel('DocHearing')->downloadAudioFile($docId);
        if($result === false){
            $this->setError('1X1017');
        }
        exit();
    }

    /**
     * 删除文档
     * @author demo
     */
    public function delete() {
        $docID = $_POST['id']; //获取数据标识
        if (!$docID) {
            $this->setError('30301',0,__URL__); //数据标识不能为空！
        }
        //删除谁的文档
        $buffer = $this->getModel('Doc')->selectData(
            '*',
            'DocID in (' . $docID . ')');
        $admin = $this->getCookieUserName();
        if($this->ifSubject && $this->mySubject){
            if(!in_array($buffer[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30507',0); //您没有权限删除非所属学科文档！
            }
        }else if($this->ifDiff){
            //判断是否可以编辑
            if ($buffer[0]['Admin'] != $admin) {
                $this->setError('30812',0); //您没有权限删除别人的文档！
            }
        }
        //判断是否能删除
        $buffer=$this->getModel('TestAttrReal')->selectData(
            'TestID',
            'DocID in (' . $docID . ')');
        if($buffer){
            $this->setError('1X1009',0); //数据已入库请先移出入库数据！
        }
        //删除上传文件
        $doc = $this->getModel('Doc');
        $doc->deleteFile($docID);

        //删除替换文档文件
        $buffer=$this->getModel('TestAttr')->selectData(
            'TestID',
            'DocID in (' . $docID . ')');
        $testID=array();
        if ($buffer) {
            foreach ($buffer as $iBuffer)
                $testID[]=$iBuffer['TestID'];
        }

        if($testID){
            $buffer = $this->getModel('TestReplace')->selectData(
                '*',
                'TestID in (' . implode(',',$testID) . ') ');
            if ($buffer) {
                foreach ($buffer as $iBuffer)
                    $doc->deleteAllFile($iBuffer);
            }
        }
        //删除试题相关
        $baseObj = D('Base');
        $baseObj->unionSelect('docDeleteById','TestKl','TestAttr',$docID);
        $baseObj->unionSelect('docDeleteById','TestChapter','TestAttr',$docID);
        $baseObj->unionSelect('docDeleteById','TestDoc','TestAttr',$docID);
        $baseObj->unionSelect('docDeleteById','TestReplace','TestAttr',$docID);
        unset($baseObj);
        $this->getModel('Test')->deleteData(' DocID in ('.$docID.') ');
        $this->getModel('TestAttr')->deleteData(' DocID in ('.$docID.') ');
        $where['DocID']=$docID;
        if ($this->getModel('Doc')->deleteData(
                'DocID in ('.$docID.')') === false) {
            $this->setError('30302'); //删除失败！
        } else {
            //删除中间表
            $this->getModel('DocArea')->deleteData(
                'DAID in ('.$docID.')');
            $this->getModel('DocAbiCapt')->deleteData(
                'DocID in ('.$docID.')');//从能力关联表删除

            //删除索引
            $doc->deleteIndex($docID);

            //写入日志
            $this->adminLog($this->moduleName, '删除文档DocID为【' . $docID . '】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
    /**
     * 审核文档
     * @author demo
     */
    public function check(){
        $id=$_POST['id']; //获取数据标识
        //判断数据标识
        if (empty ($id)) {
            $this->setError('30301',1); //数据标识不能为空！
        }
        //审核谁的试卷
        $buffer = $this->getModel('Doc')->selectData(
            '*',
            'DocID in (' . $id . ')');
        if($this->ifSubject && $this->mySubject){
            $subjectArray=explode(',',$this->mySubject);
            foreach ($buffer as $i=>$iBuffer) {
                if(!in_array($iBuffer['SubjectID'],$subjectArray)){
                    $this->setError('30712',1); //您不能编辑非所属学科文档！
                }
            }
        }else if($this->ifDiff){
            $admin = $this->getCookieUserName();
            foreach ($buffer as $i=>$iBuffer) {
                if ($iBuffer['Admin'] != $admin) {
                    $this->setError('1X1008',1); //您没有权限审核别人的文档！
                }
            }
        }
        $docID=explode(',',$id);
        $status=$_POST['Status'];
        if(!is_numeric($status)){
            $status=0;
        }
        $str = $status==1 ? '<font color=\'red\'>锁定</font>' : '正常';
        $alert = $status==1 ? '锁定成功' : '审核成功';
        $classStr = $status==1 ? 'btcheck' : 'btlock';

        $this->getModel('Doc')->updateData(
            array('Status'=>$status),
            'DocID in ('.implode(',',$docID).')');
        $output='<script>';
        foreach($docID as $i=>$iDocID){
            $output.='document.getElementById("status'.$iDocID.'").innerHTML="<span class=\''.$classStr.'\' thisid=\''.$iDocID.'\'>'.$str.'</span>";';
        }
        $output.='alert("'.$alert.'");</script>';
        $this->setBack($output);
    }

	/**
     * 提取excel文档试题
     * @author wjx
     */
	public function testsavexls(){
		$username = trim($this->getCookieUserName());
		$pagename = 'xls试题预览';

		//获取数据标识
		$docID = $_REQUEST['DocID'];
		// dump($docID);
		$doc = $this->getModel('Doc')->selectData(
		'*',
		'DocID in (' . $docID . ')',
        'DocID asc');
		// dump($doc);die;
		//excel读取,路径是绝对路径
		$filePath= $doc[0]['DocPath'];
		//从doc目录下读取
		// $url=C('WLN_DOC_HOST_PATH');
		// dump($GLOBALS);die;
		// dump($_SERVER['DOCUMENT_ROOT']);die;
		// $realPath = $url.$filePath;
		
		// dump($realPath);die;
		
		// $url=dirname(dirname($_SERVER['DOCUMENT_ROOT'])).'/doc';
		// $realPath = $url.$filePath;
		// dump($realPath);die;
		// dump($url);die;
		//从www目录下读取
		
		$realPath = realpath('./') . $filePath;
		
		
		$data=$this->excelread($realPath);
		if($data){
                foreach($data as $i=>$idata){
                    foreach($idata as $j=>$jdata){
						// dump($jdata);
                        $data[$i][$j]=R('Common/TestLayer/strFormatxls',array($jdata));
                    }
                }
			}
		// dump($data);
		// die;
		//从表中读取文档信息
        $doc = $this->getModel('Doc');
        $edit = $this->getModel('Doc')->selectData(
            '*',
            'DocID in (' . $docID . ')',
            'DocID asc');
		// dump($edit);

		
        //判断权限
        if($this->ifSubject && $this->mySubject){
            foreach($edit as $i=>$iEdit){
                if(!in_array($iEdit['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30820',0,'',$iEdit['DocID']); //您不能提取非所属学科文档【'.$iEdit['DocID'].'】！'
                }
            }
        }else if($this->ifDiff){
            $admin=$this->getCookieUserName();
            foreach($edit as $i=>$iEdit){
                if ($iEdit['Admin'] != $admin) {
                    $this->setError('30818',0,'',$iEdit['DocID']); //您没有权限提取文档【'.$iEdit['DocID'].'】！
                }
            }
        }
		
        // //判断文档是否需要提取
        // if(empty($edit[0]['DocHtmlPath'])){
            // $result=$doc->checkHtmlPath($edit[0]['DocPath'],$edit[0]['DocID']);
            // if(!empty($result)){
                // $this->setError($result);
            // }
        // }elseif(strlen($edit[0]['DocHtmlPath'])<20){//判断文档是否正常
            // $this->setError('30715'); //文档有误，请检查文档是否正确上传！
        // }

        $id = $_POST['key'];
		
        //批量提取试题  提取试题下的导入操作
        if($_POST['id']){
            //查看试卷是否被锁定
            foreach($edit as $i=>$iEdit){
                if ($iEdit['Status'] == 1) {
                    $this->setError('30818',0,'',$iEdit['DocID']); //文档【'.$iEdit['DocID'].'】被锁定，您无法提取试题！
                }
            }
			//试题存表
            $result=$doc->getTestByxls($edit[0]['DocID'],array(),$username);
			
			
            if($result){
                $this->setError($result);
            }
            //写入日志
            $this->adminLog($this->moduleName, '提取试题,DocID为【' . $edit[0]['DocID'] . '】的数据');

            if($edit[1]['DocID']){
                $idlist=str_replace(','.$edit[0]['DocID'].',',',',','.$docID.',');
                $idlist=substr($idlist,1,count($idlist)-2);
                $this->showSuccess('提取【'.$edit[0]['DocID'].'】成功！下面转入【'.$edit[1]['DocID'].'】', U('Doc/WlnDoc/testsavexls',array('id'=>$idlist)));
            }else{
                $this->showSuccess('全部提取成功！下面转入试题',U('Test/Test/index'));
            }
        }else if (!empty ($id)) { //单个提取试题
            //查看试卷是否被锁定
            if ($edit[0]['Status'] == 1) {
                $this->setError('30819'); //文档被锁定，您无法提取试题！
            }

            if (empty ($id)) {
                $this->setError('1X1007'); //请选择入库题目！
            }
            if (!is_array($id))
                $id = array ($id);
			
			//入库操作
            $result=$doc->getTestByxls($docID,$id,$username);
            if($result){
                $this->setError($result);
            }

            $doc->setDefaultChapter($docID,$id);
            if(!empty($id)){
                $idStr=implode(',',$id);
            }

            //写入日志
            $this->adminLog($this->moduleName, '单个提取试题,DocID为【' . $docID.'-'.$idStr . '】的数据');
            $this->showSuccess('提取成功！下面转入试题', U('Test/Test/index/DocID/'.$docID));
        }else{ //预览试题
            //输出标签标题到列表项，无需结束标签
            $tagArray = $buffer;
            unset ($tagArray[count($tagArray) - 1]);

            $start = array ();
            $testField = array ();
            foreach ($buffer as $iBbuffer) {
                $start[] = $iBbuffer['DefaultStart'];
                $testfield[] = $iBbuffer['TestField'];
            }
			// dump($docID);
            $newArr = $doc->showTestByDoc($docID); //过滤
            //$host=C('WLN_DOC_HOST');
            if($newArr){
                foreach($newArr as $i=>$iNewArr){
                    foreach($iNewArr as $j=>$jNewArr){
                        $newArr[$i][$j]=R('Common/TestLayer/strFormat',array($jNewArr));
                    }
                }
            }
        }
		


		//获取tag
		$buffer = $this->getModel('TestTag')->selectData(
            '*',
            '1=1',
            'OrderID asc');
		//输出标签标题到列表项，无需结束标签（结束）
		// dump($buffer);die;
        $tagArray = $buffer;
		//删除最后的结束标签
        unset ($tagArray[count($tagArray) - 1]);
		//数组转换为字符串
		// $v1=implode('', $v);
		
		// dump($data);die;
		
		/*载入模板标签*/
        $this->assign('data', $data);
        $this->assign('start', $start);
        $this->assign('testfield', $testField);
        $this->assign('tag_array', $tagArray);
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display();


	}
    /**
     * 提取文档试题
     * @author demo
     */
    public function testsave() {
        $username = trim($this->getCookieUserName());

        $pageName = '试题预览';

        $docID = $_REQUEST['DocID']; //获取数据标识
        if(!$docID) $docID=$_REQUEST['id']; //获取数据标识
		
        if (!$docID) {
            $this->setError('30301',0); //数据标识不能为空！
        }
		//获取tag
        $buffer = $this->getModel('TestTag')->selectData(
            '*',
            '1=1',
            'OrderID asc');

		// dump($buffer);die;
        if (!$buffer)
            $this->setError('30821',0,U('Test/TestTag/index')); //'文档标签规则不能为空！'
		//从表中读取文档信息
        $doc = $this->getModel('Doc');
        $edit = $this->getModel('Doc')->selectData(
            '*',
            'DocID in (' . $docID . ')',
            'DocID asc');
		// dump($edit);die;
        //判断权限
        if($this->ifSubject && $this->mySubject){
            foreach($edit as $i=>$iEdit){
                if(!in_array($iEdit['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30820',0,'',$iEdit['DocID']); //您不能提取非所属学科文档【'.$iEdit['DocID'].'】！'
                }
            }
        }else if($this->ifDiff){
            $admin=$this->getCookieUserName();
            foreach($edit as $i=>$iEdit){
                if ($iEdit['Admin'] != $admin) {
                    $this->setError('30818',0,'',$iEdit['DocID']); //您没有权限提取文档【'.$iEdit['DocID'].'】！
                }
            }
        }

        //判断文档是否需要提取
        if(empty($edit[0]['DocHtmlPath'])){
            $result=$doc->checkHtmlPath($edit[0]['DocPath'],$edit[0]['DocID']);
            if(!empty($result)){
                $this->setError($result);
            }
        }elseif(strlen($edit[0]['DocHtmlPath'])<20){//判断文档是否正常
            $this->setError('30715'); //文档有误，请检查文档是否正确上传！
        }

        $id = $_POST['key'];
        //批量提取试题  提取试题下的导入操作
        if($_POST['id']){
            //查看试卷是否被锁定
            foreach($edit as $i=>$iEdit){
                if ($iEdit['Status'] == 1) {
                    $this->setError('30818',0,'',$iEdit['DocID']); //文档【'.$iEdit['DocID'].'】被锁定，您无法提取试题！
                }
            }
			//
            $result=$doc->getTestByDoc($edit[0]['DocID'],array(),$username);
			// dump($result);die;
            if($result){
                $this->setError($result);
            }
            //写入日志
            $this->adminLog($this->moduleName, '提取试题,DocID为【' . $edit[0]['DocID'] . '】的数据');

            if($edit[1]['DocID']){
                $idlist=str_replace(','.$edit[0]['DocID'].',',',',','.$docID.',');
                $idlist=substr($idlist,1,count($idlist)-2);
                $this->showSuccess('提取【'.$edit[0]['DocID'].'】成功！下面转入【'.$edit[1]['DocID'].'】', U('Doc/WlnDoc/testsave',array('id'=>$idlist)));
            }else{
                $this->showSuccess('全部提取成功！下面转入试题',U('Test/Test/index'));
            }
        }else if (!empty ($id)) { //单个提取试题
            //查看试卷是否被锁定
            if ($edit[0]['Status'] == 1) {
                $this->setError('30819'); //文档被锁定，您无法提取试题！
            }

            if (empty ($id)) {
                $this->setError('1X1007'); //请选择入库题目！
            }
            if (!is_array($id))
                $id = array ($id);
			// dump($id);die;
            $result=$doc->getTestByDoc($docID,$id,$username);
            if($result){
                $this->setError($result);
            }

            $doc->setDefaultChapter($docID,$id);
            if(!empty($id)){
                $idStr=implode(',',$id);
            }

            //写入日志
            $this->adminLog($this->moduleName, '单个提取试题,DocID为【' . $docID.'-'.$idStr . '】的数据');
            $this->showSuccess('提取成功！下面转入试题', U('Test/Test/index/DocID/'.$docID));
        }else{ //预览试题
            //输出标签标题到列表项，无需结束标签
            $tagArray = $buffer;
            unset ($tagArray[count($tagArray) - 1]);

            $start = array ();
            $testField = array ();
            foreach ($buffer as $iBbuffer) {
                $start[] = $iBbuffer['DefaultStart'];
                $testfield[] = $iBbuffer['TestField'];
            }

            $newArr = $doc->showTestByDoc($docID); //过滤
            //$host=C('WLN_DOC_HOST');
            if($newArr){
                foreach($newArr as $i=>$iNewArr){
                    foreach($iNewArr as $j=>$jNewArr){
                        $newArr[$i][$j]=R('Common/TestLayer/strFormat',array($jNewArr));
                    }
                }
            }
        }
		// dump($newArr);
        /*载入模板标签*/
        $this->assign('newarr', $newArr);
        $this->assign('start', $start);
        $this->assign('testfield', $testField);
        $this->assign('tag_array', $tagArray);
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display();
    }
    /**
     * 预览文档html
     * @author demo
     */
    public function viewtest() {
        $pageName='预览文档';
        $docID = $_GET['DocID']; //获取数据标识
        if (!$docID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $test = $this->getModel('Test');
        $buffer = $this->getModel('Doc')->selectData(
            'DocName,SubjectID,Admin',
            'DocID=' . $docID);
        if(!$buffer){
            $this->setError('30301','',__URL__); //文档不存在！
        }
        //文档权限
        if($this->ifSubject && $this->mySubject){
            if(!in_array($buffer[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712','',__URL__); //您不能编辑非所属学科文档！
            }
        }else if($this->ifDiff){
            //判断是否可以编辑
            if ($buffer[0]['Admin'] != $this->getCookieUserName()) {
                $this->setError('30812','',__URL__); //您没有权限编辑！
            }
        }
        $docName=$buffer[0]['DocName'];
        $buffer = $this->getModel('Test')->selectData(
            '*',
            'DocID=' . $docID,
            'NumbID ASC');
        $buffer2 = $this->getModel('TestAttr')->selectData(
            '*',
            'DocID=' . $docID);
        $testArray=array();
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                $testArray[$iBuffer['TestID']]=$iBuffer;
            }
        }
        if($buffer2){
            foreach($buffer2 as $i=>$iBuffer){
                $testArray[$iBuffer['TestID']]=array_merge($testArray[$iBuffer['TestID']],$iBuffer);
            }
        }
        header("Content-type: text/html; charset=utf-8");
        //替换html相对路径
        $outPut='';
        if($testArray){
            $key=1;
            //$host=C('WLN_DOC_HOST');
            foreach($testArray as $i=>$iBuffer){
                $iBuffer['Test']     = R('Common/TestLayer/strFormat',array($iBuffer['Test']));
                $iBuffer['Answer']   = R('Common/TestLayer/strFormat',array($iBuffer['Answer']));
                $iBuffer['Analytic'] = R('Common/TestLayer/strFormat',array($iBuffer['Analytic']));
                $iBuffer['Remark']   = R('Common/TestLayer/strFormat',array($iBuffer['Remark']));

                $outPut.=$test->formatTest($iBuffer['Test'],$key,500,0,1,$iBuffer['OptionWidth'],$iBuffer['OptionNum'],$iBuffer['TestNum'],$iBuffer['IfChoose']);
                $outPut.='<font color="blue">【答案】</font>'.$test->formatTest($iBuffer['Answer'],$key,0,0,0,0,0,$iBuffer['TestNum'],0,1);
                $outPut.='<font color="blue">【解析】</font>'.$test->formatTest($iBuffer['Analytic'],$key,0,0,0,0,0,$iBuffer['TestNum'],0,1);
                $outPut.='<font color="blue">【备注】</font>'.$test->formatTest($iBuffer['Remark'],$key,0,0,0,0,0,$iBuffer['TestNum'],0,1);
                $outPut.='<hr/>';
                $xtnum=$iBuffer['TestNum'];
                if($xtnum==0) $xtnum=1;
                $key+=$xtnum;
            }
        }else{
            $outPut='试题不存在或已经入库';
        }
        $this->assign('docname', $docName); //页面内容
        $this->assign('output', $outPut); //页面内容
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 创建解析任务
     * @author demo 2015-12-23
     */
    public function createTask(){
        $id = $_GET['id'];
        if (!$id) {
            $this->setError('30301',1,__URL__); //数据标识不能为空！
        }
        $docFile = $this->getModel('DocFile');
        if($docFile->isCreateTaskByDocId($id)){
            $this->setError('1X1006', 1);
        }
        $doc = $this->getModel('Doc');
        $data = $doc->findData('SubjectID', 'DocID='.$id);
        if(empty($data)){
            $this->setError('30815', 1);
        }
        $data = array(
            'des' => (string)implode("\r\n", $this->getModel('TestAttr')->getDuplicateStatusByDocId($id)),
            'sid' => $data['SubjectID'],
            'did' => $id
        );
        $this->setBack($data);
    }

    /**
     * 保存解析任务
     * @author demo 15-12-23
     */
    public function saveTask(){
        $id = $_POST['id'];
        if (!$id) {
            $this->setError('30301',1,__URL__); //数据标识不能为空！
        }
        $docFile = $this->getModel('DocFile');
        if($docFile->isCreateTaskByDocId($id)){
            $this->setError('1X1006', 1);
        }
        $data = $this->getModel('Doc')->findData('DocName,DocPath,Description,TypeID,DocYear,TotalScore,TestTime,IfTest,GradeID,Status,SourceID, SubjectID', 'DocID='.$id);
        if(empty($data)){
            $this->setError('30815', 1);
        }
        if(1 == $data['Status']){ //锁定的文档不能分配
            $this->setError('1X1020', 1);
        }
        $docFileData = array();
        $docFileData['DocPath'] = $data['DocPath'];
        $docFileData['SubjectID'] = $data['SubjectID'];
        $docFileData['CheckStatus'] = $_POST['CheckStatus'];
        $docFileData['FileDescription'] = $_POST['FileDescription'];
        $docFileData['UserName'] = $_POST['UserName'];
        $docFileData['Admin'] = $this->getCookieUserName();
        $docFileData['DocName'] = $data['DocName'];
        $docFileData['AddTime'] = time();
        $docFileData['DocID'] = $id;
        unset($data['SubjectID'],$data['DocPath']);
        $data['AreaID'] = (array)$this->getModel('DocArea')->selectData('AreaID', 'DocID='.$id);
        $docFileData['Attribute'] = serialize($data); //获取当前文档的属性
        if($docFile->insertData($docFileData) === false){
            $this->setError('30307', 1);
        }
        $this->setBack('success');
    }


    /**
     * 添加任务获取教师信息
     * @author demo 15-12-23
     */
    public function teacher() {
        C('SHOW_PAGE_TRACE', false);
        $powerUser = $_GET['s'];
        $map['s'] = $powerUser;
        $subjectID = $_GET['subjectID'];
        if($subjectID){
            if($this->ifSubject && $this->mySubject){
                if(!in_array($subjectID,explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能选择非所属学科老师！
                }
            }
        }
        $userName = $_GET['name'];
        $perPage = C('WLN_PERPAGE');
        //查询标引权限分组ID
        $buffer = $this->getModel('PowerUser')->selectData(
            'PUID',
            'PowerUser="'.$powerUser.'"');
        $puid = $buffer[0]['PUID'];
        //查询属于标引权限分组的用户ID
        $userIdArr = $this->getModel('UserGroup')->selectData(
            'UserID',
            'GroupName = 3 AND GroupID = "'.$puid.'"');
        $userArr = array();//存放用户ID；
        foreach($userIdArr as $i => $iUserIdArr){
            $userArr[] = $iUserIdArr['UserID'];
        }
        $userStr = implode(',',$userArr);//用户ID字符串
        //查询条件
        $data = 'UserID in ('.$userStr.')';
        $data .= ' and Status=0 and Whois=1 ';
        if($userName){
            $data .= ' and UserName="'.$userName.'"';
            $map['name']=$userName;
        }
        if($subjectID){
            $data .= ' and SubjectStyle="'.$subjectID.'"';
            $map['subjectID'] = $subjectID;
        }
        $count=$this->getModel('User')->selectCount(
            $data,
            'UserID');
        // 进行分页数据查询
        $page = page($count,$_GET['p'],$perPage).','.$perPage;
        $list = $this->getModel('User')->pageData(
            '*',
            $data,
            'UserID DESC',
            $page
        );
        $this->pageList($count,$perPage,$map);
        $subjectArray = SS('subjectParentId'); //获取学科数据集

        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('s',$powerUser);
        $this->assign('subjectArray', $subjectArray);
        $this->display();
    }

    /**
     * 试卷订单管理
     * @author demo 16-4-21
     */
    public function inviteCode(){
        $requestParams = array(
            'DocName'=>'',
            'DocID'=>0,
            'UserID'=>0,
            'Start'=>0,
            'End'=>0,
            'Type'=>0,
            'p'=>1,
            'prepage' => 20
        );
        $keys = array_keys($requestParams);
        foreach($_REQUEST AS $key=>$value){
            if(in_array($key, $keys) && !empty($value)){
                $requestParams[$key] = $value;
            }
        }
        unset($keys);
        foreach($requestParams as $key=>$value){
            if(empty($value)){
                unset($requestParams[$key]);
            }
        }
        if($this->ifSubject){
            $requestParams['SubjectID'] = $this->mySubject;
        }
        $fields = array('ud.*', 'd.DocName', 'u.UserName', 'a.AdminName');
        $model = $this->getModel('UserDocInviteCode');
        $count = $model->unionSelect('getUserDocOrder', $fields, $requestParams, true);
        $list = $model->unionSelect('getUserDocOrder', $fields, $requestParams);
        $prepage = $requestParams['prepage'];
        unset($requestParams['prepage']);
        $this->pageList($count, $requestParams['prepage'], $requestParams);
        $this->assign('list', $list);
        $this->assign('pageName', '试卷订单管理');
        $this->display();
    }

    public function inviteCodeAdd(){
        $id = (int)$_GET['id'];
        $act = 'add';
        $data = array();
        if(!empty($id)){
            $act = 'edit';
            $pageName = '试卷订单编辑';
            $data = $this->getModel('UserDocInviteCode')->findData('ID, DocID, CodeValue', 'ID='.$id);
        }else{
            $pageName = '试卷订单添加';
        }
        $this->assign('data', $data);
        $this->assign('pageName', $pageName);
        $this->display();
    }

    /**
     * 试卷订单保存
     * @author demo
     */
    public function inviteCodeSave(){
        $id = (int)$_POST['id'];
        $act = ($id === 0) ? 'insert' : 'update';
        $model = $this->getModel('UserDocInviteCode');
        $docid = (int)$_POST['DocID'];
        if(!$this->getModel('Doc')->findData('DocID', 'DocID='.$docid)){
            $this->setError('1X1018');
        }
        $data = $model->findData('Usable', 'ID='.$id);
        if(2 == $data['Usable']){
            $this->setError('1X1019');
        }
        $data = array();
        $data['CodeValue'] = $_POST['CodeValue'];
        $data['DocID'] = $_POST['DocID'];
        $data['AddTime'] = time();
        $data['AdminID'] = $this->getCookieUserID();
        $result = false;
        if($act == 'insert'){
            $result = $model->insertData($data);
        }else{
            $result = $model->updateData($data, 'ID='.$id);
        }
        if($result === false){
            $this->setError('30307');
        }
        $this->showSuccess('保存成功！', U('Doc/WlnDoc/inviteCode'));
    }

    /**
     * 订单删除
     * @author demo 16-4-22
     */
    public function inviteCodeDel(){
        $id = (int)$_GET['id'];
        if (empty ($id)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        $model = $this->getModel('UserDocInviteCode');
        $data = $model->findData('Usable', 'ID='.$id);
        if(2 == $data['Usable']){
            $this->setError('1X1019');
        }
        $result = $model->deleteData('ID='.$id);
        if($result === false){
            $this->setError('30302');
        }
        $this->showSuccess('删除成功！', U('Doc/WlnDoc/inviteCode'));
    }
}


/**
 * 添加订单管理菜单
     添加订单管理action Doc-WlnDoc-inviteCode
   添加分段管理菜单
     添加订单管理后台权限 Doc-UserDocScoreSegment-index
 * @author demo
 */
