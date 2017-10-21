<?php
/**
 * @author demo
 * @date 2015-5-5
 */
/**
 * 导学案知识文档管理类，用于导学案知识文档的操作
 */
namespace Guide\Manage;
class CaseLoreDocManage extends BaseController {
    var $moduleName = '导学案知识文档';
    /**
     * 导学案知识文档列表
     * @author demo
     */
    public function index(){
        $pageName="导学案知识文档列表";
        $map=array();
        $data = ' 1 = 1 ';
        if($this->ifSubject && $this->mySubject){
            $data .= 'AND SubjectID in ('.$this->mySubject.')';
        }
        if(isset($_REQUEST['name']) && $_REQUEST['name']!=''){
            //简单查询
            $_REQUEST['name'] = formatString('decodeUrl',$_REQUEST['name']);
            $map['name'] = formatString('encodeUrl',$_REQUEST['name']);
            $data .= ' AND DocName like "%'.$_REQUEST['name'].'%" ';
        }else{
            if(isset($_REQUEST['SubjectID']) && $_REQUEST['SubjectID']!=''){                          //高级查询
                if($this->ifSubject && !in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712',0); //您不能搜索非所属学科内容！
                    return;
                }
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $data .= ' AND SubjectID ='.$_REQUEST['SubjectID'];
            }
            if(isset($_REQUEST['Status']) && $_REQUEST['Status']!=''){
                $map['Status'] = $_REQUEST['Status'];
                $data .= ' AND Status ='.$_REQUEST['Status'];
            }
            if(isset($_REQUEST['DocName']) && $_REQUEST['DocName']!=''){
                $map['DocName'] = $_REQUEST['DocName'];
                $data .= ' AND DocName like "%'.$_REQUEST['DocName'].'%" ';
            }
        }
        $perPage = C('WLN_PERPAGE');
        $count = $this->getModel('CaseLoreDoc')->selectCount(
            $data,
            "*");// 查询满足要求的总记录数
        $page=page($count,$_GET['p'],$perPage).','.$perPage;
        $list = $this->getModel('CaseLoreDoc')->pageData(
            '*',
            $data,
            'DocID desc',
            $page
        );
        $this->pageList($count,$perPage,$map);//载入分页
        $subjectArray=SS('subject');
        $menuArray=SS('caseMenu');
        $parent=SS('chapterParentPath');// 获取已选中的章节路径
        $self=SS('chapterList');

        $param=array();
        $param['style']='chapterList';
        $param['parent']=$parent;
        $param['self']=$self;
        foreach($list as $i=>$iList){
            $list[$i]['IfGet']=0;
            $buffer=$this->getModel('CaseLoreAttr')->selectData(
                'LoreID',
                'DocID='.$list[$i]['DocID']);
            if($buffer) $list[$i]['IfGet']=1;

            $list[$i]['SubjectName']=$subjectArray[$iList['SubjectID']]['ParentName'].$subjectArray[$iList['SubjectID']]['SubjectName'];

            $param['ID']=$iList['ChapterID'];
            $chapterArray=$this->getData($param);
            $list[$i]['ChapterName']=$chapterArray[0]['ChapterName'];
            if($iList['MenuID']){
                $list[$i]['MenuName']=$menuArray[$iList['MenuID']]['MenuName'];
            }
        }
        $this->assign('subjectArray',SS('subjectParentId'));
        $this->assign('pageName',$pageName);
        $this->assign('list',$list);
        $this->display();
    }

    /**
     * 添加知识文档
     * @author demo
     */
    public function add(){
        $pageName="添加知识文档";
        $act='add';
        $subjectArray = SS('subjectParentId'); //获取学科数据集
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray',$subjectArray);
        $this->assign('pageName',$pageName);
        $this->display();
    }

    /**
     * 编辑导学案知识文档
     * @author demo
     */
    public function edit() {
        $docID = $_GET['id'];//获取数据标识
        //判断数据标识
        if(empty($docID)){
            $this->setError('30301');//数据标识不能为空！
        }
        $pageName = '编辑导学案知识文档';
        $act = 'edit'; //模板标识
        $edit =$this->getModel('CaseLoreDoc')->selectData(
            '*',
            'DocID = '.$docID,
            '',
            1);
        //获取栏目属性
        $param['style']='caseMenu';
        $param['subjectID']=$edit[0]['SubjectID'];
        $menuArray = $this->getData($param);

        $edit[0]['DocPath']=C('WLN_DOC_HOST').$edit[0]['DocPath'];
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712');//您不能修改非所属学科导学案知识文档
            }
        }
        $subjectArray = SS('subjectParentId'); //获取学科数据集
        $chapterId=$edit[0]['ChapterID'];

        $param=array();
        $param['style']='chapter';
        $param['subjectID']=$edit[0]['SubjectID'];
        $param['return']=2;
        $chapterArray=$this->getData($param);//获取版本

        if($chapterId){
            $buffer=SS('chapterParentPath');  // 缓存父类路径数据
            $chapterParentStr='';//父类路径包括自己
            $bufferTmp=array();
            if($buffer[$chapterId]) krsort($buffer[$chapterId]);
            if($buffer[$chapterId]){
                foreach($buffer[$chapterId] as $iBuffer){
                    $bufferTmp[]=$iBuffer['ChapterID'];
                }
                $chapterParentStr='|'.implode('|',$bufferTmp).'|c'.$chapterId.'|';
            }else{
                $chapterParentStr='|'.$chapterId.'|';
            }
        }

        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('chapterArray',$chapterArray);//章节数据集
        $this->assign('subjectArray',$subjectArray);//学科数据集
        $this->assign('menuArray',$menuArray);//栏目数据集
        $this->assign('chapterParentStr', $chapterParentStr);
        $this->assign('edit', $edit[0]);//编辑数据
        $this->assign('pageName', $pageName);
        $this->display('CaseLoreDoc/add');
    }

    /**
     * 列表页模板状态切换方法
     * @author demo
     */
    public function replace(){
        $data = array ();
        $docId = $_POST['wid']; //获取数据标识
        $status = $_POST['status'];
        $statusArray=explode(',',$status);
        $data['Status']=1;
        if($statusArray[0]==1){
            $data['Status']=0;
        }

        if (!$docId) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if ($this->getModel('CaseLoreDoc')->update(
                $data,
                'DocID in (' . $docId . ')'
            ) === false) {
            $this->setError('30824'); //更改状态失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName, '修改导学案知识文档编号为【' . $docId . '】的状态【' . ($data['Status'] == 1 ? '锁定' : '正常') . '】');
            $this->setBack('更改状态成功！');
        }
    }


    /**
     * 保存导学案知识文档
     * @author demo
     */
    public function save(){
        $docId = $_POST['DocID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($docId) && $act == 'edit'){
            $this->setError('30301');//数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223');//模板标识不能为空！
        }
        if(strstr('c',end($_POST['chapterID']))){
            $this->setError('30712',0); //请选择最终章节！
        }
        $data=array();
        $data['DocName']=trim($_POST['DocName']);
        $data['Admin'] = $this->getCookieUserName();
        $data['SubjectID']=$_POST['SubjectID'];
        $data['ChapterID']=str_replace('c','',end($_POST['chapterID']));
        $data['MenuID']=$_POST['MenuID'];
        $data['Description']=trim($_POST['Description']);
        $data['Status']=$_POST['Status'];
        $data['AddTime']=time();
        if($act=='add'){
            if($this->ifSubject && $this->mySubject){
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能编辑为非所属学科知识文档！
                }
            }
        }else{
            $buffer = $this->getModel('CaseLoreDoc')->selectData(
                'SubjectID,DocPath',
                'DocID = '.$docId
            );
            $oldDocPath=$buffer[0]['DocPath'];
            if($this->ifSubject && $this->mySubject){
                if(!in_array($buffer[0]['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能编辑非所属学科知识文档！
                }elseif(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能编辑为非所属学科知识文档！
                }
            }
        }
        $caseLoreDoc = $this->getModel('CaseLoreDoc');
        //验证文档名称重复
        if($act == 'add'){
            $buffer = $caseLoreDoc->selectData(
                'DocID',
                'DocName = "'.$data['DocName'].'"'
            );
        }else{
            $buffer = $caseLoreDoc->selectData(
                'DocID',
                'DocName = "'.$data['DocName'].'" and DocID != '.$docId);
        }
        if($buffer){
            $this->setError('30817');//文档名称重复！
        }

        $caseLoreDoc=$this->getModel('CaseLoreDoc');
        if (!empty ($_FILES['photo']['name']) && !empty ($_FILES['photo']['size'])) {
            //限制文件大小为1M以内
            $output=R('Common/UploadLayer/uploadWordAndCheck');
            if(is_numeric($output[0]) && !empty($output[0])) $this->setError($output[0],0,'',$output[1]);
            $data['DocPath']=$output[0];
            $data['DocHtmlPath']=$output[1];
            $data['DocFilePath']=$output[2];
        }else if($act == 'add'){
            $this->setError('30822');//添加失败！请添加word文档。
        }

        if ($act == 'add') {
            if ($caseLoreDoc->insertData(
                    $data
                ) === false) {
                $this->setError('30310');//添加失败！
            }
            //写入日志
            $this->adminLog($this->moduleName, '添加导学案知识文档【' . $_POST['DocName'] . '】');
            $this->showSuccess('添加成功！', __URL__);
        } else if ($act == 'edit') {
            //修改文件
            if (!empty ($_FILES['photo']['name']) && !empty ($_FILES['photo']['size'])) {
                //删除原来文档
                $caseLoreDoc->deleteAllFile($oldDocPath);
            }
            if ($caseLoreDoc->updateData(
                    $data,
                    'DocID ='.$docId) === false) {
                $this->setError('30311');//修改失败！
            }
            $this->adminLog($this->moduleName, '修改导学案知识文档DocID为【' . $_POST['DocID'] . '】的数据');
            $this->showSuccess('修改成功！', __URL__);
        }
    }

    /**
     * 知识提取
     * @author demo
     */
    public function testsave(){
        $pageName="知识预览";
        $docID = $_REQUEST['id']; //获取数据标识
        if(!$docID) $docID=$_GET['DocID'];
        if(!$docID) $docID=$_POST['DocID']; //获取数据标识
        if (!$docID) {
            $this->setError('30301',0); //数据标识不能为空！
        }
        //文档标签
        $buffer = $this->getModel('CaseLoreDocLabel')->selectData(
            '*',
            '1=1',
            'OrderID asc');
        if (!$buffer) {
            $this->setError('30821', 0, U('CaseLoreDocLabel/index')); //'文档标签规则不能为空！'
        }
        $edit = $this->getModel('CaseLoreDoc')->selectData(
            '*',
            'DocID in ('.$docID.')',
            'DocID asc'
        );
        $admin = $this->getCookieUserName();
        //判断权限
        if($this->ifSubject && $this->mySubject){
            foreach($edit as $i=>$iEdit){
                if(!in_array($iEdit['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30820',0,'',$iEdit['DocID']); //您不能提取非所属学科文档【'.$iEdit['DocID'].'】！'
                }
            }
        }else if($this->ifDiff){
            foreach($edit as $i=>$iEdit){
                if ($iEdit['Admin'] != $admin) {
                    $this->setError('30818',0,'',$iEdit['DocID']); //您没有权限提取文档【'.$iEdit['DocID'].'】！
                }
            }
        }

        $caseLoreDoc=$this->getModel('CaseLoreDoc');
        //判断文档是否需要提取
        if(empty($edit[0]['DocHtmlPath'])){
            $result=$caseLoreDoc->checkHtmlPath($edit[0]['DocPath'],$edit[0]['DocID']);
            if(!empty($result)){
                $this->setError($result);
            }
        }elseif(strlen($edit[0]['DocHtmlPath'])<20){//判断文档是否正常
            $this->setError('30715'); //文档有误，请检查文档是否正确上传！
        }

        $id = $_POST['key'];
        if($_REQUEST['id']){
            //查看文档是否被锁定
            foreach($edit as $i=>$iEdit){
                if ($iEdit['Status'] == 1) {
                    $this->setError('23004',0,'',$iEdit['DocID']); //文档【'.$iDocData['DocID'].'】被锁定，您无法提取知识！
                }
            }
            $loreData = $caseLoreDoc->extractLore($iEdit[0]['DocID'],$admin,array());
            if($loreData){
                $this->setError($loreData);
            }
            //写入日志
            $this->adminLog($this->moduleName, '提取知识,DocID为【' . $edit[0]['DocID'] . '】的数据');

            if($edit[1]['DocID']){
                $idlist=str_replace(','.$edit[0]['DocID'].',',',',','.$docID.',');
                $idlist=substr($idlist,1,count($idlist)-2);
                $this->showSuccess('提取【'.$edit[0]['DocID'].'】成功！下面转入【'.$edit[1]['DocID'].'】', U('CaseLoreDoc/testsave',array('id'=>$idlist)));
            }else{
                $this->showSuccess('全部提取成功！下面转入知识', U('CaseLore/index',array('DocID'=>$docID)));
                exit;
            }
        }else if(!empty($id)){
            //查看试卷是否被锁定
            if ($edit[0]['Status'] == 1) {
                $this->setError('30819'); //文档被锁定，您无法提取试题！
            }
            if (!is_array($id))
                $id = array ($id);
            $result=$caseLoreDoc->extractLore($edit[0]['DocID'],$admin,$id);
            if($result){
                $this->setError($result);
            }
            $idStr=implode(',',$id);
            //写入日志
            $this->adminLog($this->moduleName, '单个提取知识,DocID为【' . $docID.'-'.$idStr . '】的数据');
            $this->showSuccess('提取成功！下面转入知识', U('CaseLore/index',array('DocID'=>$docID)));
            exit;
        }else{
            $tagArray = $buffer;
            unset ($tagArray[count($tagArray) - 1]);

            $start = array ();
            $testField = array ();
            foreach ($buffer as $iBbuffer) {
                $start[] = $iBbuffer['DefaultStart'];
                $testfield[] = $iBbuffer['TestField'];
            }
            $newArr = $caseLoreDoc->showLoreByDoc($docID); //过滤
            $host=C('WLN_DOC_HOST');
            if($newArr && $host){
                foreach($newArr as $i=>$iNewArr){
                    foreach($iNewArr as $j=>$jNewArr){
                        $newArr[$i][$j]=R('Common/TestLayer/strFormat',array($jNewArr));
                    }
                }
            }
        }
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
     * 删除知识文档
     * @author demo
     */
    public function delete(){
        $docId=$_POST['id'];
        if(!$docId){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $caseLoreDoc=$this->getModel('CaseLoreDoc');
        //删除谁的文档
        $buffer =$this->getModel('CaseLoreDoc')->selectData(
            '*',
            'DocID in (' . $docId . ')');
        $admin = $this->geCookieUserName();
        if($this->ifSubject && $this->mySubject){
            if(!in_array($buffer[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30507',0); //您没有权限删除非所属学科文档！
            }
        }else if($this->ifDiff){
            //判断是否可以编辑
            if ($buffer[0]['Admin'] != $admin) {
                $this->setError('23005',0); //您没有权限删除别人的文档！
            }
        }
        $lore=$this->getModel('CaseLore');
        //删除知识替换数据
        $loreData = $lore->selectData(
            'LoreID',
            'DocID in ('.$docId.')'
        );
        $loreIdList='';
        if($loreData){                  //文档已提取知识时
            foreach ($loreData as $iBuffer)
                $caseLoreDoc->deleteAllFile($iBuffer);

            foreach($loreData as $i=>$iLoreData){
                $loreIdList=','.$iLoreData['LoreID'];
            }
            $this->getModel('CaseLoreReplace')->deleteData(
                'LoreID in ('.substr($loreIdList,1).')'
            );
            //删除知识数据
            $lore->deleteData(
                'DocID in ( '.$docId.' )'
            );
            $this->getModel('CaseLoreAttr')->deleteData(
                'DocID in ( '.$docId.' )'
            );
        }
        $caseLoreDoc->delFile($docId);
        $this->getModel('CaseLoreDoc')->deleteData(
            'DocID in ('.$docId.')'
        );
        //写入日志
        $this->adminLog($this->moduleName, '删除知识文档DocID为【' .  $docId . '】的数据');
        $this->showSuccess('删除成功！', __URL__);
    }

    /**
     * 预览文档html
     * @author demo
     */
    public function loreView() {
        $pageName='预览知识文档';
        $docID = $_GET['DocID']; //获取数据标识
        if (!$docID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $caseLore = $this->getModel('CaseLore');
        $buffer = $this->getModel('CaseLoreDoc')->selectData(
            'DocName,SubjectID,Admin',
            'DocID=' . $docID
        );
        if(!$buffer){
            $this->setError('30301','',__URL__); //数据标识不能为空！
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
        $buffer = $this->getModel('CaseLore')->selectData(
            '*',
            'DocID=' . $docID,
            'NumbID ASC'
        );
        $buffer2 = $this->getModel('CaseLoreAttr')->selectData(
            '*',
            'DocID=' . $docID
        );
        $testArray=array();
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                $testArray[$iBuffer['LoreID']]=$iBuffer;
            }
        }
        if($buffer2){
            foreach($buffer2 as $i=>$iBuffer){
                $testArray[$iBuffer['LoreID']]=array_merge($testArray[$iBuffer['LoreID']],$iBuffer);
            }
        }
        header("Content-type: text/html; charset=utf-8");
        //替换html相对路径
        $outPut='';
        if($testArray){
            $key=1;
            $host=C('WLN_DOC_HOST');
            foreach($testArray as $i=>$iBuffer){
                if($host){
                    $iBuffer['Lore'] = R('Common/TestLayer/strFormat',array($iBuffer['Lore']));
                    $iBuffer['Answer'] = R('Common/TestLayer/strFormat',array($iBuffer['Answer']));
                }
                $outPut.=$this->getModel('Test')->formatTest($iBuffer['Lore'],$key,500,0,0,0,0,0,0,1);
                $outPut.='<font color="blue">【答案】</font>'.$this->getModel('Test')->formatTest($iBuffer['Answer'],$key,0,0,0,0,0,0,0,1);
                $outPut.='<hr/>';
            }
        }else{
            $outPut='知识不存在';
        }
        $this->assign('docname', $docName); //页面内容
        $this->assign('output', $outPut); //页面内容
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
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
        $caseLoreDoc=$this->getModel('CaseLoreDoc');
        //查询文档数据
        $where='DocID=' . $docID;
        $edit = $caseLoreDoc->selectData(
            'DocName,SubjectID,Admin,DocHtmlPath,DocPath,DocFilePath',
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
            $strHtml=$caseLoreDoc->changeImgPath($edit[0]['DocFilePath'],$strHtml);
            $strHtml=R('Common/TestLayer/strFormat',array($strHtml));
            echo $strHtml;
        }
    }
}