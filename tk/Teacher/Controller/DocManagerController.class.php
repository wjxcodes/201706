<?php
/**
 * 教师试卷管理类
 * @author demo
 * @date 2014-11-3
 */
namespace Teacher\Controller;
class DocManagerController extends BaseController {
    private $moduleName = '前台文档编辑';

    public function index(){
        $limit = 20;
        $p = isset($_GET['p']) ? $_GET['p'] : $_POST['p'];
        $this->assign('pageName','下载文档列表');
        //查询文档信息
        $username = $this->getCookieUserName();
        $where = '`file`.UserName=\''.$username.'\'';
        $map = array();
        if ($_REQUEST['name']) {
            //简单查询
            $where .= ' AND doc.DocName like "%' . $_REQUEST['name'] . '%" ';
        } else {
            //高级查询
            if ($_REQUEST['DocName']) {
                $map['DocName'] = $_REQUEST['DocName'];
                $where .= ' AND doc.DocName like "%' . $_REQUEST['DocName'] . '%"';
            }
            if ($_REQUEST['DocID']) {
                $map['DocID'] = $_REQUEST['DocID'];
                $where .= ' AND `file`.DocID = ' . $_REQUEST['DocID'];
            }
        }
        $DocFile = $this->getModel('DocFile');
        $count = $DocFile->unionSelect('docFileSelectCountByWhere', $where);
        $this->pageList($count,$limit,$map);
        $p = page($count,$p,$limit);
        $list = $DocFile->unionSelect('docFilePageByDoc', $where,(($p-1)*$limit).','.$limit);
        $subjects=SS('subject');
        $this->assign('subjects',$subjects);
        foreach($list as $key=>$value){
            $list[$key]['FileDescription'] = preg_replace('/\r\n|\r|\n/i','<br>', $value['FileDescription']);
        }
        $this->assign('list',$list);
        $this->display();
    }

    /**
     * 生成标引任务信息
     * @author demo 
     */
    public function generateWork(){
        $docId = $_GET['docid'];
        if(empty($docId)){
            $this->setError('30301',NORMAL_ERROR,U('Teacher/DocManager/index'));
        }
        //验证属性
        $docFile = $this->getModel('DocFile');
        $where = '`file`.DocID IN('.$docId.')';
        $df = $docFile->unionSelect('docFileSelectByWhere', $where);
        $username = $this->getCookieUserName();
        foreach($df as $value){
            if(empty($value['DocID'])){
                $this->setError('40118',NORMAL_ERROR,U('Teacher/DocManager/index'),$value['DocID']);
            }else if($username != $value['UserName']){
                $this->setError('40105',NORMAL_ERROR,U('Teacher/DocManager/index'),$value['DocID']);
            }else if((int)$value['IfTask'] == 1 || is_null($value['IfTask'])){
                $this->setError('40106',NORMAL_ERROR,U('Teacher/DocManager/index'),$value['DocID']);
            }
        }
        $result = $docFile->createTask($docId,$username);
        if(empty($result)){
            $this->setError('30404',NORMAL_ERROR,U('Teacher/DocManager/index'));
        }
        $this->teacherLog($this->moduleName,'添加教师端任务分配，编号【'.implode(',', $result).'】');
        $this->succeed('文档已提交！您可以选择进入：&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.U('Teacher/Task/checkwork', array('id'=>$result[0])).'">标引试题</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.U('Teacher/DocManager/index').'">文档管理</a>');
    }

    /**
     * 下载试卷信息
     * @author demo
     */
    public function down(){
        $fid = $_GET['fid'];
        if(!is_numeric($fid)){
            $this->setError('30502');
        }
        $docFile = $this->getModel('DocFile');
        //判断权限
        $authorize = $docFile->isAccess($fid,$this->getCookieUserName());
        $this->failure($authorize,__URL__);
        $result = $docFile->findData(
            'DocName',
            'FileID='.$fid
        );
        $path = $docFile->fileDown($fid);
        if($path === ''){
            $this->setError('30706',NORMAL_ERROR,__URL__);
        }
        $paperName=$result['DocName'].'['.date('Y年m月d日').']';

        $host=C('WLN_DOC_HOST');
        if($host){
            //兼容后台文档中生成的解析任务  15-12-23
            if(strpos($path, 'docfile') !== false){
                $dir = 'docfile';
            }else if(strpos($path, 'word') !== false){
                $dir = 'word';
            }else{
                $this->setError('40802');
            }
            $url=$host.R('Common/UploadLayer/getDocServerUrl',array($path,'down', $dir ,$paperName));
            header('Location:'.$url);
        }else{
            $alias = iconv('utf-8', 'gbk', $paperName.strrchr($path,'.'));
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false);
            header("Content-Type: application/msword");
            header("Content-Disposition: attachment; filename=\"".$alias."\";" );
            header("Content-Transfer-Encoding: binary");
            readfile($path);
        }
    }

    /**
     * 上传试卷
     * @author demo
     */
    public function upload(){
        $fid = $_GET['fid'];
        if(!is_numeric($fid)){
            $this->setError('30502');
        }
        $docFile = $this->getModel('DocFile');
        //判断权限
        $authorize = $docFile->isAccess($fid,$this->getCookieUserName());
        $this->failure($authorize,__URL__);
        $result = $docFile->findData(
            '*',
            'FileID='.$fid
        );
        $result['DocPath'] = C('WLN_DOC_HOST').$result['fileDocPath'];
        $attributes = unserialize($result['Attribute']);
        $this->assign('attributes',$attributes);
        $this->assign('data',$result);
        $this->assign('fid',$fid);
        $this->assign('pageName','试卷上传');
        $this->display();
    }

    /**
     * 保存上传数据
     * @author demo
     */
    public function save(){
        set_time_limit(0);
        $fid = $_POST['fid'];
        if(!is_numeric($fid)){
            $this->setError('30502');
        }
        $docFile = $this->getModel('DocFile');
        //操作检查
        $authorize = $docFile->isAccess($fid,$this->getCookieUserName());
        // $this->failure($authorize,__URL__.'-upload-fid-'.$fid);
        $this->failure($authorize,U('Teacher/DocManager/upload', array('fid'=>$fid)));

        $docId = (int)$_POST['docid'];
        $result = $docFile->findData(
            '*',
            'FileID='.$fid
        );
        //提取属性信息
        $attributes = unserialize($result['Attribute']);
        $attributes['SubjectID'] = $result['SubjectID'];
        $attributes['Admin'] = $result['Admin'];
        $attributes['LoadTime'] = time();

        $result['DocName'] = preg_replace('/\[.*?\]$/', '' ,$result['DocName']);
        if(strchr($_FILES['photo']['name'],$result['DocName']) === false){
            // $this->setError('40103',NORMAL_ERROR,__URL__.'-upload-fid-'.$fid,$result['DocName']);
            $this->setError('40103',NORMAL_ERROR,U('Teacher/DocManager/upload', array('fid'=>$fid)),$result['DocName']);
        }

        //检查指定学科的文件后缀
        $ext = strrchr($_FILES['photo']['name'], '.');
        $ext = strtolower(ltrim($ext,'.'));
        if($ext !== 'docx'){
            $this->setError('30402',NORMAL_ERROR,U('Teacher/DocManager/upload', array('fid'=>$fid)));
        }
        //重名检查
        $doc = $this->getModel('Doc');
        $buffer=$doc->selectData(
            '*',
            'DocName="'.$attributes['DocName'].'"'
        );
        if(empty($docId) && $buffer){
            $this->setError('40104',NORMAL_ERROR,U('Teacher/DocManager/upload', array('fid'=>$fid)));
        }

        if (!empty ($_FILES['photo']['name']) && !empty ($_FILES['photo']['size'])) {
            $output=R('Common/UploadLayer/uploadWordAndCheck');

            if(is_numeric($output[0]) && !empty($output[0])) $this->setError($output[0],NORMAL_ERROR,U('Teacher/DocManager/upload', array('fid'=>$fid)),$output[1]);
            $attributes['DocPath']=$output[0];
            $attributes['DocHtmlPath']=$output[1];
            $attributes['DocFilePath']=$output[2];
        }else{
            $this->setError('40102',NORMAL_ERROR,U('Teacher/DocManager/upload', array('fid'=>$fid)));
        }

        $areas = $attributes['AreaID'];
        unset($attributes['AreaID']);

        if(!empty($docId)){
            $docId = $doc->selectData('DocID', 'DocID='.$docId);
            $docId = $docId[0]['DocID'];
        }
        //如果docid为空时，为新增数据
        if(empty($docId)){
            $docId = $doc->insertData(
                $attributes
            );
            if(!$docId){
                $this->setError('30310',NORMAL_ERROR, U('Teacher/DocManager/upload', array('fid'=>$fid)));
            }
            //添加区域信息
            foreach($areas as $i=>$iAttributes){
                $data = array();
                $data['DocID'] = $docId;
                $data['AreaID'] = $iAttributes;
                $this->getModel('DocArea')->insertData(
                    $data
                );
            }
            $docFile->updateData(
                array('DocID'=>$docId),
                'FileID='.$fid
            );
            //添加日志
            $this->teacherLog($this->moduleName, '添加文档【' . $attributes['DocName'] . '】');
        }else{
            $doc->deleteFile($docId);
            $doc->updateData(
                $attributes,
                'DocID='.$docId
            );
            $this->teacherLog($this->moduleName, '修改文档【' . $attributes['DocName'] . '】');
        }
        $docFile->addUploadTimes($fid);
        if(1 == $result['CheckStatus']){
            $this->succeed('该试卷需审核才能进行后续操作，请耐心等待！', '添加成功' ,U('Teacher/DocManager/index'));
            return;
        }
        //教师审核
        // $this->succeed('正在提取试题。', '添加成功',__URL__.'-preview-DocID-'.$docId);
        $this->succeed('正在提取试题。', '添加成功',U('Teacher/DocManager/preview', array('DocID'=>$docId)));
    }

    /**
     * 试题预览
     */
    public function preview() {
        $pageName = '试题预览';
        $docID = $_REQUEST['DocID']; //获取数据标识
        if(!$docID) $docID=$_GET['id']; //获取数据标识

        //检查当前文档的所属
        $authorize = $this->getModel('DocFile')->isAccess($docID,$this->getCookieUserName(),'doc');
        $this->failure($authorize,__URL__);

        $buffer = $this->getModel('TestTag')->selectData(
            '*',
            '1=1',
            'OrderID asc'
        );
        if (!$buffer)
            $this->setError('40108',NORMAL_ERROR, __URL__);

        $doc = $this->getModel('Doc');
        $edit = $doc->selectData(
            '*',
            'DocID in (' . $docID . ')',
            'DocID asc'
        );

        if(empty($edit[0]['DocHtmlPath'])){
            $result=$doc->checkHtmlPath($edit[0]['DocPath'],$edit[0]['DocID']);
            if(!empty($result)){
                $this->setError($result,NORMAL_ERROR, __URL__);
            }
        }elseif(strlen($edit[0]['DocHtmlPath'])<20){
            //判断文档是否正常
            $this->setError('30715',NORMAL_ERROR, __URL__); //文档有误，请检查文档是否正确上传！

        }

        $id = $_POST['key'];
        //提交数据
        if($_GET['id']){ //批量提取试题
            //查看试卷是否被锁定
            foreach($edit as $i=>$iEdit){
                if ($iEdit['Status'] == 1) {
                    $this->setError('40109',NORMAL_ERROR,'',$iEdit['DocID']);
                }
            }
            $doc->getTestByDoc($edit[0]['DocID']);
            if($edit[1]['DocID']){
                $idList=str_replace(','.$edit[0]['DocID'].',',',',','.$docID.',');
                $idList=substr($idList,1,count($idList)-2);
                // $this->showSuccess('提取【'.$edit[0]['DocID'].'】成功！下面转入【'.$edit[1]['DocID'].'】', __URL__ . '-preview-id-'.$idList);
                $this->showSuccess('提取【'.$edit[0]['DocID'].'】成功！下面转入【'.$edit[1]['DocID'].'】', U('Teacher/DocManager/preview', array('id'=>$idList)));
            }else{
                // $this->succeed('正在进入试题编辑页面。数据量过大时加载会稍慢，请稍微等待！','提取成功',__URL__.'-removeDuplicate-DocID-'.$docID);
                $this->succeed('正在进入试题编辑页面。数据量过大时加载会稍慢，请稍微等待！','提取成功',U('Teacher/DocManager/removeDuplicate', array('DocID'=>$docID)));
            }
        }else if (!empty ($id)) { //单个提取试题
            //查看试卷是否被锁定
            if ($edit[0]['Status'] == 1) {
                $this->setError('40109',NORMAL_ERROR);
            }

            if (empty ($id)) {
                $this->setError('40110',NORMAL_ERROR);
            }
            if (!is_array($id))
                $id = array (
                    $id
                );
            $doc->getTestByDoc($docID,$id,$this->getCookieUserName());
            // $this->succeed('正在进入试题编辑页面。数据量过大时加载会稍慢，请稍微等待！','提取成功',__URL__.'-removeDuplicate-DocID-'.$docID);
            $this->succeed('正在进入试题编辑页面。数据量过大时加载会稍慢，请稍微等待！','提取成功', U('Teacher/DocManager/removeDuplicate', array('DocID'=>$docID)));
        }else{ //预览试题
            //输出标签标题到列表项，无需结束标签
            $tag_array = $buffer;
            unset ($tag_array[count($tag_array) - 1]);

            $start = array ();
            $testField = array ();
            foreach ($buffer as $buffern) {
                $start[] = $buffern['DefaultStart'];
                $testField[] = $buffern['TestField'];
            }

            $newarr = $doc->showTestByDoc($docID,'',$this->getCookieUserName()); //过滤
            $host=C('WLN_DOC_HOST');
            if($newarr && $host){
                foreach($newarr as $ii=>$newarrn){
                    foreach($newarrn as $jj=>$newarrnn){
                        $newarr[$ii][$jj]= R('Common/TestLayer/strFormat',array($newarrnn));
                    }
                }
            }
        }
        if(!empty($edit)){
            //执行数据Pay录入
            $userID = $this->getCookieUserID();
            $this->getModel('Pay')->addPayBySubject($newarr,$userID,$docID,$edit[0]['SubjectID']);
        }
        $this->assign('newarr', $newarr);
        $this->assign('start', $start);
        $this->assign('testfield', $testField);
        $this->assign('tag_array', $tag_array);
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display();
    }

    /**
     * 试题排重，ajax获取数据
     * @author demo
     * @date 2015-6-19
     */
    public function duplicateTest(){
        //验证排重功能是否开启
        // if(($tmpError=R('Common/TestLayer/ifExcludeRepeat'))!=''){
        //     $this->setError($tmpError,1);
        // }
        $docID = (int)$_POST['docid']; //获取数据标识
        $in = (int)$_POST['in']; //获取数据标识
        $prepage = (int)$_POST['prepage'];
        //判断数据标识
        if(!is_numeric($docID)){
            $this->setError('30502',1);
        }
        //判断权限
        $authorize = $this->getModel('DocFile')->isAccess($docID,$this->getCookieUserName(),'doc');
        $this->failure($authorize,U('Teacher/DocManager/index'));
        $page = (int)$_POST['page'];
        // if(0 == $page){
        //     $page = 1;
        // }
        $testReal = $this->getModel('TestReal');
        $list = $testReal->getTestByDocId($docID, $in, $page, $prepage);
        $list['list'] = $testReal->getSimilarities($list['list'], $in);
        if($list['list'][0] === false){
            if(is_numeric($list['list'][1])) $this->setError($list['list'][1],1,'',$list['list'][2]);
            $this->setError('30504',1); //查询发生异常！
        }
        $this->setBack($list);
    }

    /**
     * 替换更改试题
     * @author demo
     */
    public function removeDuplicate(){
        $in = $_GET['in']; //获取数据标识
        if(empty($in)) $in=0;
        $docId = (int)$_GET['DocID']; //获取数据标识
        //判断数据标识
        if(!is_numeric($docId)){
            $this->setError('30502');
        }
        //判断权限
        $authorize = $this->getModel('DocFile')->isAccess($docId,$this->getCookieUserName(),'doc');
        $this->failure($authorize,U('Teacher/DocManager/index'));
        $this->assign('in', $in); // 赋值数据集
        $this->assign('docid', $docId);
        $this->assign('pageName', '试题去重'); //页面标题
        $this->display();
    }

    /**
     * 显示内容
     */
    public function showDuplicate() {
        $testID = $_POST['id']; //获取数据标识
        $in = $_POST['in']; //获取数据标识
        //判断数据标识
        if(!is_numeric($testID)){
            $this->setError('30502');
        }

        $pageName = '编辑试题';
        $act = 'edit'; //模板标识
        $test = $this->getModel('TestReal');
        $field=array('testid','test','docid','docname');
        $order=array();
        $page=array();
        $result=array();
        $where['TestID']=$testID;
        if(!$in){
            $test = $this->getModel('Test');
            $show=$test->selectData(
                'TestID,Test',
                'TestID='.$testID
            );
            $host=C('WLN_DOC_HOST');
            $show[0][0]['testid']=$show[0]['TestID'];
            if($host)
                $show[0][0]['test']= R('Common/TestLayer/strFormat',array($show[0]['Test']));
            else
                $show[0][0]['test']=$show[0]['Test'];
        }else
            $show=$test->getTestIndex($field,$where,$order,$page);//获取重复试题题文
        if($show === false){
            $this->setError('30504');
        }
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('show', $show[0][0]);
        $this->assign('pageName', $pageName);
        $this->setBack($this->fetch());
    }
    /**
     * 试题解析文档去重
     * @author demo
     */
    public function mark() {
        //修改试题属性 规则：
        //1.凡是试题属性为空，被标注重复后，试题属性和试题库试题属性一致。若被取消，属性不变。
        //2.凡是试题属性不为空，被标注重复后，其属性不变。若取消，属性不变。
        //3.凡是库内试题重复，各属性不变。
        $testID=$_POST['id']; //获取数据标识
        $Duplicate=$_POST['Duplicate'];//获取重复试题ID
        $style=$_POST['style'];//获取重复试题ID
        //处理试题
        $duplicate=explode(',',$Duplicate);
        $duplicate[]=$testID;
        $duplicate=array_filter($duplicate);
        $docid = $_POST['docid'];
        //判断数据标识
        if (empty ($testID) || empty($docid)) {
            $this->setError('30301',AJAX_ERROR);
        }

        if($Duplicate==''){
            $this->setError('40101',AJAX_ERROR);
        }

        $authorize = $this->getModel('DocFile')->isAccess($docid,$this->getCookieUserName(),'doc');
        $this->failure($authorize);


        $testAttr=$this->getModel('TestAttr');
        $testAttrReal=$this->getModel('TestAttrReal');
        //取消标记
        if($style=='unlock'){
            $data=array();
            $data['Duplicate']=0;
            $duplicate = implode(',',$duplicate);
            $testAttr->updateData(
                $data,
                'TestID in ('.$duplicate.')'
            );
            $testAttrReal->updateData(
                $data,
                'TestID in ('.$duplicate.')'
            );
            $alert = '取消标记成功';
            $str = '<span class=\'btlock\' thisid=\''.$testID.'\'>标记为重复</span>';
            $output='<script>document.getElementById("duplicate'.$testID.'").innerHTML="'.$str.'";';
            $output .='alert("'.$alert.'");</script>';
            $this->setBack($output);
        }

        $testChapter=$this->getModel('TestChapter');
        $testKl=$this->getModel('TestKl');
        $mins=min($duplicate);
        $attrReal=$testAttrReal->selectData(
            'TestID,TypesID,SpecialID,Diff,Mark,DfStyle',
            'TestID in ('.implode(',',$duplicate).')'
        );
        $attr=$testAttr->selectData(
            'TestID,TypesID,SpecialID,Diff,Mark,DfStyle',
            'TestID in ('.implode(',',$duplicate).')'
        );
        //获取试题的章节
        $chapterBuffer=array();
        $chapterRealBuffer=array();
        $buffer=$testChapter->selectData(
            'TestID,ChapterID',
            'TestID in ('.implode(',',$duplicate).')',
            'TCID ASC'
        );
        foreach($buffer as $i=>$iBuffer){
            $chapterBuffer[$iBuffer['TestID']][]=$iBuffer['ChapterID'];
        }
        $buffer=$this->getModel('TestChapterReal')->selectData(
            'TestID,ChapterID',
            'TestID in ('.implode(',',$duplicate).')',
            'TCID ASC'
        );
        foreach($buffer as $i=>$iBuffer){
            $chapterRealBuffer[$iBuffer['TestID']][]=$iBuffer['ChapterID'];
        }
        //获取试题的知识点
        $klBuffer=array();
        $klRealBuffer=array();
        $buffer=$testKl->selectData(
            'TestID,KlID',
            'TestID in ('.implode(',',$duplicate).')',
            'TKlID ASC'
        );
        foreach($buffer as $i=>$iBuffer){
            $klBuffer[$iBuffer['TestID']][]=$iBuffer['KlID'];
        }
        $buffer=$this->getModel('TestKlReal')->selectData(
            'TestID,KlID',
            'TestID in ('.implode(',',$duplicate).')',
            'TKlID ASC'
        );
        foreach($buffer as $i=>$iBuffer){
            $klRealBuffer[$iBuffer['TestID']][]=$iBuffer['KlID'];
        }
        unset($buffer);

        if($attr || $attrReal){
            $listAttr=array();
            $listAttrReal=array();
            foreach($attrReal as $iAttrReal){
                $listAttrReal[$iAttrReal['TestID']]=$iAttrReal;
            }
            foreach($attr as $iAttr){
                $listAttr[$iAttr['TestID']]=$iAttr;
            }
            //修改试题属性 如果试题有属性则不修改
            $minsBuffer=$listAttrReal[$mins];
            if(!$minsBuffer) $minsBuffer=$listAttr[$mins];
            $minsChapter=$chapterBuffer[$mins];
            if(!$minsChapter) $minsChapter=$chapterRealBuffer[$mins];
            $minsKl=$klBuffer[$mins];
            if(!$minsKl) $minsKl=$klRealBuffer[$mins];

            foreach($attr as $iAttr){
                if($iAttr['TestID']==$mins) continue;
                $data=array();
                $data['TestID']=$iAttr['TestID'];
                if(!$iAttr['TypesID']) $data['TypesID']=$minsBuffer['TypesID'];
                if(!$iAttr['SpecialID']) $data['SpecialID']=$minsBuffer['SpecialID'];
                if(!$iAttr['Diff'] || $iAttr['Diff']=='0.000'){
                    $data['Diff']=$minsBuffer['Diff'];
                    $data['DfStyle']=$minsBuffer['DfStyle'];
                }
                if(!$iAttr['Mark']){
                    $data['Mark']=$minsBuffer['Mark'];
                    $data['DfStyle']=$minsBuffer['DfStyle'];
                }
                $data['Duplicate']=$mins;
                $testAttr->updateData(
                    $data,
                    'TestID='.$iAttr['TestID']
                );
                //章节
                if($minsChapter && !$chapterBuffer[$iAttr['TestID']][0]){
                    $this->getModel('TestChapter')->deleteData(
                        'TestID in ('.$iAttr['TestID'].')'
                    );
                    foreach($minsChapter as $i=>$iMinsChapter){
                        $data=array();
                        $data['TestID']=$iAttr['TestID'];
                        $data['ChapterID']=$iMinsChapter;
                        $this->getModel('TestChapter')->insertData($data);
                    }
                }
                //知识点
                if($minsKl && !$klBuffer[$iAttr['TestID']][0]){
                    $testKl->deleteData(
                        'TestID in ('.$iAttr['TestID'].')'
                    );
                    foreach($minsKl as $i=>$iMinsKl){
                        $data=array();
                        $data['TestID']=$iAttr['TestID'];
                        $data['KlID']=$iMinsKl;
                        $testKl->insertData(
                            $data);
                    }
                }
            }
            //更新库内重题
            $testAttrReal->updateData(
                array('Duplicate'=>$mins),
                'TestID in ('.implode(',',$duplicate).')'
            );
        }

        $alert = '标记成功';
        $str = '<span class=\'btunlock\' thisid=\''.$testID.'\'><font color=\'red\'>取消重复标记</font></span>';
        $output='<script>document.getElementById("duplicate'.$testID.'").innerHTML="'.$str.'";';
        $output .='alert("'.$alert.'");</script>';
        $this->setBack($output);
    }

    /**
     * 试题属性编辑
     */
    public function editTest(){
        $docId = $_GET['DocID'];
        $authorize = $this->getModel('DocFile')->isAccess($docId,$this->getCookieUserName(),'doc');
        $this->failure($authorize,U('Teacher/DocManager/index'));
        $pageName = '编辑试题';
        $data=' 1=1 ';
        $map=array();
        $orderby=' TestID desc ';
        if ($_REQUEST['name']) {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' and TestID='.$_REQUEST['name'];
        } else {
            //高级查询
            if ($_REQUEST['TestID']) {
                $map['TestID'] = $_REQUEST['TestID'];
                $data .= ' and TestID='.$_REQUEST['TestID'];
            }
            if ($_REQUEST['DocID']) {
                $map['DocID'] = $_REQUEST['DocID'];
                $data .= ' and DocID='.$_REQUEST['DocID'];
                $orderby=' NumbID asc ';
            }
            if ($_REQUEST['SubjectID']) {
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $data .= ' and SubjectID='.$_REQUEST['SubjectID'];
            }
            if ($_REQUEST['order']) {
                $map['order'] = $_REQUEST['order'];
                $orderby=' '.$_REQUEST['order'].' desc ';
            }
            if (is_numeric($_REQUEST['Status'])) {
                $map['Status'] = $_REQUEST['Status'];
                $data .= ' and Status='.$_REQUEST['Status'];
            }
        }

        $test = $this->getModel('Test');
        $perpage=C('WLN_PERPAGE');
        //错误试题
        if($_GET['errortest']==1){
            $map['errortest'] = 1;
            $data=' (SubjectID=0 OR TypesID=0 OR TestID in (select TestID from zj_test_kl where KlID=0))';
        }
        $TestAttr = $this->getModel('TestAttr');
        //仅显示某一文档下试题
        if($_REQUEST['DocID']){
            $list = $TestAttr->selectData(
                '*',
                $data,
                $orderby
            );
        }else{
            $count = $TestAttr->selectCount(
                $data,
                'TestID'
            ); // 查询满足要求的总记录数
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $this->pageList($count, $perpage, $map);
            $page = page($count,$_GET['p'],$perpage).','.$perpage; //格式化分页
            $list = $TestAttr->pageData(
                '*',
                $data,
                $orderby,
                $page
            );
        }
        if($list){
            $types = SS('types');
            $knowledgeList = SS('knowledge');
            $knowledgeParent = SS('knowledgeParent');
            $chapterList = SS('chapterList');
            $chapterParent=SS('chapterParentPath');
            $special = SS('special');
            $subject = SS('subject');
            $gradeArr = SS('grade');

            //获取list下试题id
            $testIDArray=array(); //存储试题id
            $docIDArray=array(); //存储文档id
            foreach($list as $i=>$iList){
                $testIDArray[]=$iList['TestID'];
                $docIDArray[]=$iList['DocID'];
            }
            $docIDArray=array_unique($docIDArray);

            $test = $this->getModel('Test');
            $testKl = $this->getModel('TestKl');
            $testChapter = $this->getModel('TestChapter');
            $doc = $this->getModel('Doc');
            $knowledge = $this->getModel('Knowledge');
            $chapter = $this->getModel('Chapter');
            //存数以试题id为键值的数据
            $testListArrayByID=$test->getTestListByID($testIDArray); //试题内容
            $testKlArrayByID=$testKl->getTestListByID($testIDArray); //试题知识点
            $testChapterArrayByID=$testChapter->getTestListByID($testIDArray); //试题章节
            $docListArrayByID=$doc->getDocListByID($docIDArray); //文档内容

            $host=C('WLN_DOC_HOST');
            foreach($list as $i=>$iList){
                $list[$i]['error']=R('Common/TestLayer/checkTestAttr',array($iList));
                $list[$i]['TypesName']=$types[$list[$i]['TypesID']]['TypesName'];
                $list[$i]['SpecialName']=$special[$list[$i]['SpecialID']]['SpecialName'];
                $list[$i]['SubjectName']=$subject[$subject[$list[$i]['SubjectID']]['PID']]['SubjectName'].$subject[$list[$i]['SubjectID']]['SubjectName'];
                $list[$i]['GradeName']=$gradeArr[$list[$i]['GradeID']]['GradeName'];
                $list[$i]['Test']=$testListArrayByID[$iList['TestID']]['Test'];
                $list[$i]['Answer']=$testListArrayByID[$iList['TestID']]['Answer'];
                $list[$i]['Analytic']=$testListArrayByID[$iList['TestID']]['Analytic'];
                $list[$i]['Remark']=$testListArrayByID[$iList['TestID']]['Remark'];
                $list[$i]['DocName']=$docListArrayByID[$iList['DocID']]['DocName'];
                if($host){
                    $list[$i]['Test']= R('Common/TestLayer/strFormat',array($list[$i]['Test']));
                }

                //获取knowledge
                if($testKlArrayByID[$iList['TestID']] && $testKlArrayByID[$iList['TestID']][0]){
                    $list[$i]['KlName']=$knowledge->getKnowledgePath(
                        array(
                            'parent'=>$knowledgeParent,
                            'self'=>$knowledgeList,
                            'ID'=>$testKlArrayByID[$iList['TestID']],
                            'ReturnString'=>'<br/>'
                        )
                    );
                }
                //获取chapter
                if($testChapterArrayByID[$iList['TestID']] && $testChapterArrayByID[$iList['TestID']][0]){
                    $list[$i]['ChapterName']=$chapter->getChapterPath(
                        array(
                            'parent'=>$chapterParent,
                            'self'=>$chapterList,
                            'ID'=>$testChapterArrayByID[$iList['TestID']],
                            'ReturnString'=>'<br/>'
                        )
                    );
                }
            }
        }

        //难度
        $diffArray=C('WLN_TEST_DIFF');
        //学科
        $subjectArray=SS('subjectParentId'); //获取学科数据集

        /*载入模板标签*/
        $this->assign('docid',$docId);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('diff_array', $diffArray);
        $this->assign('subject_array', $subjectArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 编辑试题试题属性
     * @author demo
     */
    // public function editAttributes(){
    //     $testID = $_GET['id']; //获取数据标识
    //     //判断数据标识
    //     if (empty ($testID)) {
    //         $this->setError('30301',AJAX_ERROR);
    //     }
    //     $pageName = '编辑试题';

    //     $buffer = M('DocFile')->field('`file`.UserName,`file`.FileID')->table('zj_doc `doc`')->join('zj_doc_file `file` ON `doc`.DocID=`file`.DocID')->join('zj_test `test` ON `test`.DocID=`doc`.DocID')->where('`test`.TestID in('.$testID.')')->find();
    //     $authorize = $this->getModel('DocFile')->isAccess($buffer['FileID'],$this->getCookieUserName());
    //     $this->failure($authorize);
    //     $act = 'edit'; //模板标识
    //     $test = $this->getModel('Test');
    //     $edit = $test->field('a.*,t.TypesID,t.SpecialID,t.SubjectID,t.IfChoose,t.Diff,t.DfStyle,t.Mark,t.TestNum,t.TestStyle,t.OptionWidth,t.OptionNum')->table('zj_test a')->join('zj_test_attr t ON a.TestID=t.TestID')->where('a.TestID=' . $testID)->limit(1)->select();

    //     $testKl=M('TestKl');
    //     $buffer = $testKl->where('TestID='.$testID)->select();
    //     $edit[0]['KlID']=0;
    //     if($buffer){
    //         $arr_temp=array();
    //         foreach($buffer as $buffern){
    //             $arr_temp[]=$buffern['KlID'];
    //         }
    //         $edit[0]['KlID']=implode(',',$arr_temp);
    //     }
    //     $testChapter=M('TestChapter');
    //     $buffer = $testChapter->where('TestID='.$testID)->select();
    //     $edit[0]['ChapterID']=0;
    //     if($buffer){
    //         $arr_temp=array();
    //         foreach($buffer as $buffern){
    //             $arr_temp[]=$buffern['ChapterID'];
    //         }
    //         sort($arr_temp);
    //         $edit[0]['ChapterID']=implode(',',$arr_temp);
    //     }
    //     if(strstr($edit[0]['Mark'],'@')){
    //         $arr=explode('@',$edit[0]['Mark']);
    //         foreach($arr as $ii=>$arrn){
    //             $edit[0]['Markx'][$ii+1]=array_filter(explode('#',$arrn));
    //         }
    //     }else{
    //         $edit[0]['Markx'][1]=array_filter(explode('#',$edit[0]['Mark']));
    //     }

    //     //复合题
    //     $ChooseList='';
    //     if($edit[0]['IfChoose']==1){
    //         $testjudge=M('TestJudge');
    //         $buffer=$testjudge->where('TestID='.$testID)->order('OrderID asc')->select();
    //         $ChooseList=$buffer;
    //     }
    //     //试题路径
    //     $host=C('WLN_DOC_HOST');
    //     if($host){
    //         $edit[0]['Test']=$test->changeSrcValue($edit[0]['Test'],$host);
    //         $edit[0]['Answer']=$test->changeSrcValue($edit[0]['Answer'],$host);
    //         $edit[0]['Analytic']=$test->changeSrcValue($edit[0]['Analytic'],$host);
    //         $edit[0]['Remark']=$test->changeSrcValue($edit[0]['Remark'],$host);
    //     }
    //     //分割宽度 及数量
    //     $optionnum=explode(',',$edit[0]['OptionNum']);
    //     $optionwidth=explode(',',$edit[0]['OptionWidth']);
    //     for($i=0;$i<count($ChooseList);$i++){
    //         if(empty($optionwidth[$i])){
    //             $optionwidtharr[$i]=0;
    //         }else{
    //             $optionwidtharr[$i]=$optionwidth[$i];
    //         }
    //         if(empty($optionnum[$i])){
    //             $optionnumarr[$i]=0;
    //         }else{
    //             $optionnumarr[$i]=$optionnum[$i];
    //         }
    //     }
    //     /*载入模板标签*/
    //     $this->assign('act', $act); //模板标识
    //     $this->assign('edit', $edit[0]);
    //     $this->assign('optionnum',$optionnumarr);
    //     $this->assign('optionwidth',$optionwidtharr);
    //     $this->assign('ChooseList', $ChooseList);
    //     $this->assign('pageName', $pageName);
    //     $this->setBack($this->fetch());
    // }

    /**
     * ajax获取数据
     */
    public function getdata() {
        $s=$_GET['s'];
        $l=$_GET['l'];
        $ID=$_GET['id'];
        if($l=='t'){
            if(!$ID) $ID=0;
            $Types=$this->getModel('Types');
            $buffer=$Types->getParList($s);
            $this->setBack( $Types->setoption($buffer,$ID));
        }else if($l=='gk'){
            if(!$ID) $ID=0;
            $buffer=S('knowledge');
            $Knowledge=$this->getModel('Knowledge');
            $output=array();
            if(!$buffer[$ID]['Parent']){
                $output[0]=$Knowledge->setoption($Knowledge->getParList($s),$ID);
            }else{
                foreach($buffer[$ID]['Parent'] as $kk=>$bn){
                    if(count($buffer[$ID]['Parent'])-1==$kk){
                        $output[0]=$Knowledge->setoption($Knowledge->getParList($s),$buffer[$ID]['Parent'][count($buffer[$ID]['Parent'])-1]['KlID']);
                        if($buffer[$bn['KlID']]['Sub']){
                            $select='';
                            foreach($buffer[$bn['KlID']]['Sub'] as $a){
                                $ifselect='';
                                $tmp_kid=$buffer[$ID]['Parent'][$kk-1]['KlID'];
                                if(!$tmp_kid) $tmp_kid=$ID;
                                if($a['KlID']==$tmp_kid) $ifselect=' selected="selected" ';
                                $select.='<option value="'.$a['KlID'].'"'.$ifselect.'>'.$a['KlName'].'</option>';
                            }
                            $output[1].='<select class="knowledge" name="knowledge[]">'.$select.'</select>';
                        }
                    }else{
                        if($buffer[$bn['KlID']]['Sub']){
                            $select='';
                            foreach($buffer[$bn['KlID']]['Sub'] as $a){$ifselect='';
                                $ifselect='';
                                if($a['KlID']==$ID) $ifselect=' selected="selected" ';
                                $select.='<option value="'.$a['KlID'].'"'.$ifselect.'>'.$a['KlName'].'</option>';
                            }
                            $output[1].='<select class="knowledge" name="knowledge[]">'.$select.'</select>';
                        }
                    }
                }
            }
            $this->setBack($output);
        }else if($l == 'w'){
            //计算宽度
            $testID=$_GET['id'];
            $ifintro=$_GET['ifintro'];
            if(empty($ifintro)) $ifintro=1;
            $this->setBack($this->getWidth($testID,$ifintro));
        }
    }

    /*删除*/
    // public function delete() {
    //     $testID = $_GET['id']; //获取数据标识
    //     $test = $this->getModel('Test');
    //     $buffer = M('DocFile')->field('`file`.UserName,`file`.DocID')->table('zj_doc_file `file`')->join('zj_test `test` ON `test`.DocID=`file`.DocID')->where('`test`.TestID in('.$testID.')')->find();
    //     $authorize = $this->getModel('DocFile')->isAccess($buffer['DocID'],$this->getCookieUserName(),'doc');
    //     $this->failure($authorize,__URL__.'-editTest-DocID-'.$buffer['DocID']);

    //     //删除
    //     if ($test->where('TestID in (' . $testID . ')')->delete() === false) {
    //         $this->setError('40107',NORMAL_ERROR);
    //     } else {
    //         //删除doc
    //         $testdoc = M('Testdoc');
    //         $testdoc->where('TestID in ('.$testID.')')->delete();
    //         //删除替换
    //         $testreplace = M('Testreplace');
    //         $testreplace->where('TestID in ('.$testID.')')->delete();
    //         //删除属性
    //         $testAttr = M('TestAttr');
    //         $testAttr->where('TestID in ('.$testID.')')->delete();
    //         //删除知识点
    //         $testKl = M('TestKl');
    //         $testKl->where('TestID in ('.$testID.')')->delete();
    //         //删除章节
    //         $testChapter = M('TestChapter');
    //         $testChapter->where('TestID in ('.$testID.')')->delete();

    //         //写入日志
    //         $this->teacherLog($this->moduleName, '删除文档TestID为【' . $testID . '】的数据');
    //         $this->showSuccess('删除成功！');
    //     }
    // }

    /**
     * 试题修改
     * @author demo
     */
    // public function saveTest() {
    //     $testID = $_POST['TestID'];
    //     $buffer = M('DocFile')->field('`file`.UserName,`file`.DocID')->table('zj_doc_file `file`')->join('zj_test `test` ON `test`.DocID=`file`.DocID')->where('`test`.TestID in('.$testID.')')->find();
    //     $authorize = $this->getModel('DocFile')->isAccess($buffer['DocID'],$this->getCookieUserName(),'doc');
    //     $this->failure($authorize);
    //     //接收数据
    //     $TypesID=$_POST['TypesID'];
    //     $docID=$_POST['DocID'];
    //     $IfChoose=$_POST['IfChoose'];
    //     $ChooseList=$_POST['ChooseList'];
    //     $OptionWidth=$_POST['OptionWidth'];
    //     $OptionNum=$_POST['OptionNum'];
    //     if($IfChoose==1 && $ChooseList===''){
    //         $this->setError('40801',AJAX_ERROR);
    //     }
    //     $Status=$_POST['Status'];
    //     //更改状态
    //     $test = $this->getModel('Test');
    //     $data=array();

    //     $testjudge=$this->getModel('TestJudge');
    //     $buffer=$testjudge->where('TestID='.$testID)->order('`OrderID` asc')->select();
    //     $testNum=0;
    //     $testStyle=1;
    //     if($IfChoose==0) $testStyle=3;
    //     if($IfChoose==2 || $IfChoose==3) $testStyle=1;
    //     if($ChooseList!==''){
    //         $testStyle=0;
    //         $choose_arr=explode(',',$ChooseList);
    //         $testNum=count($choose_arr);
    //         //更改复合题
    //         if($buffer){
    //             foreach($choose_arr as $ii=>$choose_arrn){
    //                 if($testStyle==0){
    //                     if($choose_arrn!=0) $testStyle=1;
    //                     if($choose_arrn==0) $testStyle=3;
    //                 }else{
    //                     if($choose_arrn==0 && $testStyle==1) $testStyle=2;
    //                     if($choose_arrn==0 && $testStyle==3) $testStyle=3;
    //                     if($choose_arrn!=0 && $testStyle==3) $testStyle=2;
    //                 }
    //                 $data=array();
    //                 $data['TestID']=$testID;
    //                 $data['OrderID']=$ii+1;
    //                 $data['IfChoose']=$choose_arrn;
    //                 if($buffer[$ii]['JudgeID'])
    //                     $testjudge->data($data)->where('JudgeID：="'.$buffer[$ii]['JudgeID'].'"')->save();
    //                 else
    //                     $testjudge->data($data)->add();
    //             }
    //             if(count($buffer)>count($choose_arr)){
    //                 $tmp_arr=array();
    //                 for($ii=count($choose_arr);$ii<count($buffer);$ii++){
    //                     $tmp_arr[]=$buffer[$ii]['JudgeID'];
    //                 }
    //                 $testjudge->where('JudgeID in ('.implode(',',$tmp_arr).')')->delete();
    //             }
    //         }else{
    //             foreach($choose_arr as $ii=>$choose_arrn){
    //                 if($testStyle==0){
    //                     if($choose_arrn!=0) $testStyle=1;
    //                     if($choose_arrn==0) $testStyle=3;
    //                 }else{
    //                     if($choose_arrn==0 && $testStyle==1) $testStyle=2;
    //                     if($choose_arrn==0 && $testStyle==3) $testStyle=3;
    //                     if($choose_arrn!=0 && $testStyle==3) $testStyle=2;
    //                 }
    //                 $data=array();
    //                 $data['TestID']=$testID;
    //                 $data['OrderID']=$ii+1;
    //                 $data['IfChoose']=$choose_arrn;
    //                 $testjudge->data($data)->add();
    //             }
    //         }
    //     }else{
    //         //清空试题对应小题结构
    //         if($buffer){
    //             $tmp_arr=array();
    //             foreach($buffer as $buffern){
    //                 $tmp_arr[]=$buffern['JudgeID'];
    //             }
    //             $testjudge->where('JudgeID in ('.implode(',',$tmp_arr).')')->delete();
    //         }
    //     }

    //     //更改属性
    //     $testattr=$this->getModel('TestAttr');
    //     $data=array();
    //     $data['TypesID']=$TypesID;
    //     $data['IfChoose']=$IfChoose;
    //     $data['TestNum']=$testNum;
    //     $data['TestStyle']=$testStyle;
    //     $data['OptionWidth']=$OptionWidth;
    //     $data['OptionNum']=$OptionNum;

    //     switch($IfChoose){
    //         case 0:
    //         $choosestr='非选择题';
    //         break;
    //         case 1:
    //         $choosestr='复合题';
    //         break;
    //         case 2:
    //         $choosestr='多选题';
    //         break;
    //         case 3:
    //         $choosestr='单选题';
    //         break;
    //     }

    //     $Types=M('Types');
    //     $Types_array=$Types->where('TypesID='.$TypesID)->select();

    //     $Diff=0;

    //     $data['Diff']=$Diff;
    //     $data['DocID']=$docID;

    //     if($testattr->where('TestID='.$testID)->select()){
    //         $testattr->where('TestID='.$testID)->save($data);
    //     }else{
    //         $data['TestID']=$testID;
    //         $testattr->data($data)->add();
    //     }

    //     //计算返回数据
    //     $Typesstr=$Types_array[0]['TypesName'];

    //     //写入日志
    //     $this->teacherLog($this->moduleName, '修改试题TestID为【' . $testID . '】的数据');

    //     $list = $test->field('a.*,t.TypesID,t.IfChoose,t.SpecialID,t.Diff,t.SubjectID,t.TestNum,t.TestStyle,t.OptionWidth,t.OptionNum')->
    //             table('zj_test a')->
    //             join('zj_test_attr t ON t.TestID=a.TestID')->
    //             where('a.TestID='.$testID)->
    //             select();
    //     $Errorstr=R('Common/TestLayer/checkTestAttr',array($list[0]));
    //     if(empty($Errorstr)) $Errorstr='无';
    //     else $Errorstr="<font color='red'>".$Errorstr.'</font>';
    //     $this->setBack('<script>'.
    //             'document.getElementById("types'.$testID.'").innerHTML="'.$Typesstr.'";' .
    //             'document.getElementById("error'.$testID.'").innerHTML="'.$Errorstr.'";' .
    //             'document.getElementById("choose'.$testID.'").innerHTML="'.$choosestr.'";' .
    //             'document.body.removeChild(document.getElementById("popup_overlay"));' .
    //             'document.body.removeChild(document.getElementById("popup_container"));' .
    //             '</script>');
    // }

    /**
     * 替换试题
     */
    public function replace() {
        $testID = $_REQUEST['TestID']; //获取数据标识
        if (empty ($testID)) {
            $this->setError('30301',NORMAL_ERROR);
        }
        $test = $this->getModel('Test');
        $url = $_GET['url']; //跳转url，如果存在则输出到页面表单
        $docFile=$this->getModel('DocFile');
        $Doc = $this->getModel('Doc');
        $buffer = $Doc->unionSelect('docSelectByTestID', $testID);
        if(empty($buffer['UserName']) && !$buffer['HasReplace']){
            $wid = $_GET['wid'];
            $did = $_GET['did'];
            // $this->setError('30604',NORMAL_ERROR,__MODULE__.'-Test-index-wid-'.$wid.'-did-'.$did);
            $this->setError('30604',NORMAL_ERROR,U('Teacher/Test/index', array('wid'=>$wid, 'did'=>$did)));
        }
        if(!$buffer['HasReplace'])
            $authorize = $docFile->isAccess($buffer['DocID'],$this->getCookieUserName(),'doc');
        // $this->failure($authorize,__URL__.'-editTest-DocID-'.$buffer['DocID']);
        $this->failure($authorize,U('Teacher/DocManager/editTest', array('DocID'=>$buffer['DocID'])));
        if(IS_POST){
            //替换试题数据
            if (!empty ($_FILES['photo']['name']) && !empty ($_FILES['photo']['size'])) {

                //检查指定学科的文件后缀
                $ext = strrchr($_FILES['photo']['name'], '.');
                $ext = strtolower(ltrim($ext,'.'));
                if($ext != 'docx'){
                    // $this->setError('30402',NORMAL_ERROR,__URL__.'-replace-TestID-'.$testID);
                    $this->setError('30402',NORMAL_ERROR,U('Teacher/DocManager/replace', array('TestID'=>$testID)));
                }
                $data = array ();
                $data['TestID'] = $testID;
                $data['Admin'] = $this->getCookieUserName();
                $data['LoadTime'] = time();

                //取消word检测服务
                C('WLN_DOC_OPEN_CHECK',0);

                //上传word
                $output=R('Common/UploadLayer/uploadWordAndCheck');
                if(is_numeric($output[0]) && !empty($output[0])) $this->setError($output[0],NORMAL_ERROR,'',$output[1]);
                $data['DocPath']=$output[0];
                $docHtmlPath=$data['DocHtmlPath']=$output[1];
                $data['DocFilePath']=$output[2];

                $docid = $buffer['DocID'];
                $TestReplace = $this->getModel('TestReplace');
                //记录替换文档
                $buffer = $TestReplace->selectData(
                    '*',
                    'TestID=' . $testID,
                    '',
                    1
                );
                if ($buffer) {
                    $data['ReplaceID'] = $buffer[0]['ReplaceID'];
                    //删除原有Replace数据
                    $Doc->deleteReplaceFile($buffer[0]['ReplaceID']);

                    $TestReplace->updateData(
                        $data,
                        'ReplaceID='.$buffer[0]['ReplaceID']
                    );
                    $this->teacherLog($this->moduleName, '修改替换试题TestID为【' . $testID . '】');
                } else {
                    $TestReplace->insertData(
                        $data
                    );
                    $this->teacherLog($this->moduleName, '添加替换试题TestID为【' . $testID . '】');
                }
                //替换试题内容
                $data = array ();
                $data['TestID'] = $testID;

                $buffer = $this->getModel('TestTag')->selectData(
                    '*',
                    '1=1',
                    'OrderID asc'
                );
                if (!$buffer){
                    // $this->setError('30707',NORMAL_ERROR, __MODULE__ . '/TestTag/index');
                    $this->setError('30707',NORMAL_ERROR, U('Teacher/TestTag/index'));
                }

                $start = array ();
                $testField = array ();
                foreach ($buffer as $i=>$iBuffer) {
                    $start[] = $iBuffer['DefaultStart'];
                    $testField[] = $iBuffer['TestField'];
                }
                $html = $Doc->getDocContent($docHtmlPath);  //获取html数据

                $arrDoc = $Doc->division($html, $start,1); //分割
                $arrHtml = $Doc->division($html, $start,2); //分割
                $newArrDoc = $Doc->changeArrFormatDoc($arrDoc); //doc过滤
                $newArr = $Doc->changeArrFormat($arrHtml); //html过滤

                $testFieldArr = $Doc->getTestField(); //数据表字段对应数组

                $datax=array();
                $datax['TestID']=$testID;
                //单条数据记录
                foreach ($newArr[0] as $i => $iNewArr) {
                    if(!strstr($testField[$i],'属性')){
                        $data[$testFieldArr[$testField[$i]]] = $iNewArr;
                        $datax['Doc' . $testFieldArr[$testField[$i]]] = $newArrDoc[0][$i];
                    }
                }
                $test->updateData(
                    $data,
                    'TestID='.$testID
                );
                $this->getModel('TestDoc')->updateData(
                    $datax,
                    'TestID='.$testID
                );
                //如果存在，则处理传递过来的url链接地址。
                $url = $_POST['url'];
                if($url == ''){
                    // $url = __URL__.'-editTest-DocID-'.$docid;
                    $url = U('Teacher/DocManager/editTest', array('DocID'=>$docid));
                }else{
                    $url = base64_decode($url);
                }
                $this->showSuccess('试题替换成功！', $url);
            }
            $this->setError('40111',NORMAL_ERROR);
        }

        $pageName = '替换试题';
        $edit = $test->unionSelect('docSelectJoinByTestID', $testID);
        $typesBuffer=SS('types');
        $edit[0]['TypesName']=$typesBuffer[$edit[0]['TypesID']]['TypesName'];
        $host=C('WLN_DOC_HOST');

        $width=600;
        $edit[0]['Test']=$test->formatTest($edit[0]['Test'],1,$width,0,1,$edit[0]['OptionWidth'],$edit[0]['OptionNum'],$edit[0]['TestNum'],$edit[0]['IfChoose'],1);
        $edit[0]['Answer']=$test->formatTest($edit[0]['Answer'],1,0,0,0,0,0,$edit[0]['TestNum'],0,1);
        $edit[0]['Analytic']=$test->formatTest($edit[0]['Analytic'],1,0,0,0,0,0,$edit[0]['TestNum'],0,1);
        $edit[0]['Remark']=$test->formatTest($edit[0]['Remark'],1,0,0,0,0,0,$edit[0]['TestNum'],0,1);
        if($host){
            $edit[0]['Test']=  R('Common/TestLayer/strFormat',array($edit[0]['Test']));
            $edit[0]['Answer']= R('Common/TestLayer/strFormat',array($edit[0]['Answer']));
            $edit[0]['Analytic']= R('Common/TestLayer/strFormat',array($edit[0]['Analytic']));
            $edit[0]['Remark']= R('Common/TestLayer/strFormat',array($edit[0]['Remark']));
        }

        /*载入模板标签*/
        if(!$url){
            $url = base64_decode($url); //将存在的url进行编码
        }
        $this->assign('url',$url);
        $this->assign('docid',$buffer['DocID']);
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display();
    }

    /**
     * 试题word下载
     */
    public function testdown() {
        $testID = $_GET['id']; //获取数据标识
        //$w = $_GET['w']; //获取数据标识
        //判断数据标识
        if (empty ($testID)) {
            $this->setError('30301',NORMAL_ERROR);
        }
        $test = $this->getModel('Test');
        $doc = $this->getModel('Doc');
        //$buffer = $this->getModel('DocFile')->field('`file`.UserName,`doc`.DocID,`work`.HasReplace,`work`.UserName as workUserName')->table('zj_doc `doc`')->join('zj_teacher_work_list `list` ON `list`.DocID=`doc`.DocID')->join('zj_teacher_work `work` ON `work`.WorkID=`list`.WorkID')->join('zj_doc_file `file` ON `doc`.DocID=`file`.DocID')->join('zj_test `test` ON `test`.DocID=`doc`.DocID')->where('`test`.TestID in('.$testID.')')->find();
        $buffer = $doc->unionSelect('docSelectByTestID', $testID);
        //为空时，则证明改试题来自后台编辑上传
        if(!$buffer['UserName']){
            if($buffer['workUserName'] != $this->getCookieUserName() || $buffer['HasReplace'] == 0){
                // $this->setError('30604',NORMAL_ERROR,__URL__.'-replace-TestID-'.$testID);
                $this->setError('30604',NORMAL_ERROR,U('Teacher/DocManager/replace',array('TestID'=>$testID)));
            }
        }else{
            $authorize = $this->getModel('DocFile')->isAccess($buffer['DocID'],$this->getCookieUserName(),'doc');
            //如果无权限访问同时没有替换试题权限【为0】则输出错误
            if($authorize[0] != '30604' || !$buffer['HasReplace']){
                // $this->failure($authorize,__URL__.'-replace-TestID-'.$testID);
                $this->failure($authorize,U('Teacher/DocManager/replace',array('TestID'=>$testID)));
            }
        }
        //$edit = $test->field('t.TestID,t.NumbID,d.DocTest,d.DocAnswer,d.DocAnalytic,d.DocRemark,a.TypesID,a.DocID')->table('zj_test t')->join('zj_test_attr a on t.TestID=a.TestID')->join('zj_test_doc d on t.TestID=d.TestID')->where('t.TestID=' . $testID)->limit(1)->select();
        $edit = $this->getModel('TestAttr')->unionSelect('testSelectJoinById', $testID);
        $buffer=$doc->selectData(
            '*',
            'DocID="'.$edit[0]['DocID'].'"'
        );
        $buffer=$this->getModel('Subject')->selectData(
            '*',
            'SubjectID="'.$buffer[0]['SubjectID'].'"'
        );
        $fontSize=10.5;
        if($buffer[0]['FontSize']>1) $fontSize=$buffer[0]['FontSize'];

        $str=$doc->getSingleDoc($edit[0],$fontSize);

        $style=$_GET['style'];
        if(!$style) $style=".docx";

        $host=C('WLN_DOC_HOST');
        if($host){
            $urlPath=R('Common/UploadLayer/setWordDocument',array( $str ,substr($style,1)));
            if(strstr($urlPath,'error')){
                $this->setError('30405',NORMAL_ERROR); //下载异常！请稍联系管理员。
            }

            $paperName='试题-'.$testID;
            $url=$host.R('Common/UploadLayer/getDocServerUrl',array($urlPath,'down','',$paperName));

            header('Location:'.$url);
        }else{
            $host_in="http://" . $_SERVER['HTTP_HOST'] . "/";
            $path=realpath('./').'/Uploads/mht/'.date('Y/md/',time());
            if(!file_exists($path)) $test->createpath($path);

            $content=$doc->getWordDocument( $str ,$host_in);
            $docPath=$path.'试题-'.$testID.'.mht';
            file_put_contents(iconv('UTF-8','GBK//IGNORE',$docPath),$content);

            $newPath=$doc->htmltoword(iconv('UTF-8', 'GBK//IGNORE', $docPath),substr($style,1));
            unlink(iconv('UTF-8','GBK//IGNORE',$docPath));

            if($newPath!=iconv('UTF-8', 'GBK//IGNORE', $docPath)){
                $urlPath=$host.$newPath;
            }else{
                $urlPath = str_replace('.mht',$style,$docPath);
            }
            $content=file_get_contents($urlPath);

            $doc->wordheader('试题-'.$testID,$style);
            echo $content;
        }
    }

    /**
     * 获取最大选项宽度；
     * @param int $testID 试题id
     * @param ini $ifIntro=1 判断是否入库
     * @return array
     * @author demo
     */
    private function getWidth($testID,$ifIntro=1){
        $output=array();
        $test=$this->getModel('Test');
        if($ifIntro==1){
            $testArr=$test->selectData(
                'Test',
                'TestID='.$testID
            );
        }else{
            $testArr=$this->getModel('TestReal')->selectData(
                'Test',
                'TestID='.$testID
            );
        }
        if(empty($testArr)){
            $output=array('试题不存在！');
        }
        $arr=$test->getOptionWidth($testArr[0]['Test']);
        if(!$output){
            if(!$arr){
                $output=array(array(0,0));
            }else{
                $output=$arr;
            }
        }
        return $output;
    }

    /**
     *　操作成功后的页面跳转
     * @author demo
     */
    protected function succeed($msg_detail, $msgTitle='',$link='',$seconds = 0){
        if($msgTitle == ''){
            $msgTitle = '操作成功';
        }
        $this->assign('msgTitle', $msgTitle);
        $this->assign('message', $msg_detail);
        $this->assign('link', $link);
        $this->assign('seconds', $seconds);
        $this->display('DocManager/succeed');
        exit;
    }

    /**
     * 验证失败的信息输出
     * @param array $authorize 验证后返回的数组结果
     * @param string $url 跳转的url地址
     * @author demo
     */
    private function failure($authorize,$url=''){
        $flag = NORMAL_ERROR;
        if(IS_AJAX){
            $flag = AJAX_ERROR;
        }
        if($authorize !== true){
            $replace = '';
            if($authorize[0] == '30705'){
                $replace = $authorize[1];
            }
            $this->setError($authorize[0],$flag,$url,$replace);
        }
    }
}