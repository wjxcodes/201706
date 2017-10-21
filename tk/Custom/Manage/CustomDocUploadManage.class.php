<?php
/**
 * @author demo
 * @date 2015年10月14日
 */
/**
 * 用户校本题库，文档上传管理
 */
namespace Custom\Manage;
class CustomDocUploadManage extends BaseController  {
    var $docStatus=array('0'=>'待审核',
                    '1'=>'审核中',
                    '2'=>'已完成',
                    '3'=>'失败',
    );
    /**
     * 列表看页面
     * @author demo
     */
    public function index(){
        $pageName = '校本题库文档上传管理';
        $doc = $this->getModel('CustomDocUpload');
        $perpage = C('WLN_PERPAGE'); //每页 页数
        $map = array ();
        //浏览谁的文档
        $data = ' 1=1 AND a.Status>0';
        //验证用户权限
        if ($this->ifSubject && $this->mySubject){
            $data .= ' and a.SubjectID in (' . $this->mySubject . ') ';
        }
        $count=$doc->selectCount(
            'DUID',
            $data,
            'a'
        );
        $this->pageList($count, $perpage, $map);
        $page = page($count,$_GET['p'],$perpage).','.$perpage; //格式化分页
        $list=$doc->getCustomDocUploadList($data,$page);
        $subject=$this->getApiCommon('Subject/subject');
        foreach($list as $i=>$iList){
            $list[$i]['Status']=$this->docStatus[$list[$i]['Status']];
            if($list[$i]['SubjectID']!=0){
                $list[$i]['SubjectName']=$subject[$list[$i]['SubjectID']]['SubjectName'];
            }
        }
        //学科
        $subjectArray=$this->getApiCommon('Subject/subjectParentId'); //获取学科数据集

        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('subjectArray', $subjectArray);
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
        //查询文档数据
        $where='DUID=' . $docID;
        $edit = $this->getModel('CustomDocUpload')->selectData(
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
            if(empty($edit[0]['Path']) || strstr($edit[0]['Path'],'error')){
                $this->setError('30706'); //该文件不存在
            }
            $url=C('WLN_DOC_HOST').R('Common/UploadLayer/getDocServerUrl',array($edit[0]['Path'],'down','docfile',$edit[0]['Title']));
            header('location:'.$url);
        }else{
            if(empty($edit[0]['Path'])){
                $this->setError('30813'); //文档未转换，请提取后预览
            }
            $urlHtml=C('WLN_DOC_HOST_IN').R('Common/UploadLayer/getDocServerUrl',array($edit[0]['Path'],'getWordFile','word',$edit[0]['Title']));
            header("Content-type: text/html; charset=GBK");
            //对图片路径进行转换
            $strHtml=file_get_contents($urlHtml);
            $strHtml=$doc->changeImgPath($edit[0]['Path'],$strHtml);
            $strHtml=R('Common/TestLayer/strFormat',array($strHtml));
            echo $strHtml;
        }
    }

    /**
     * 文档审核通过
     * @author demo
     */
    public function adopt(){
        $DuID=$_REQUEST['duID'];
        if(empty($DuID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $model = $this->getModel('CustomTestDoc');
        $result = $model->sequentialQuery($DuID, 1);
        if(empty($result)){
            $this->setError('30306');
        }
        $data['Status']=2;//审核完成
        $data['AdminID']= $this->getCookieUserID(); //管理员ID
        $data['LastAuditTime']= time(); //管理员ID
        $result=$this->getModel('CustomDocUpload')->updateData($data,'DUID='.$DuID);
        if($result!=false){
            $this->showSuccess('审核通过');
        }
    }

    /**
     * 文档审核不同过
     * @author demo
     */
    public function notAdopt(){
        $DuID=$_REQUEST['duID'];
        if(empty($DuID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $data['Status']=3;//审核完成
        $data['AdminID']= $this->getCookieUserID(); //管理员ID
        $data['LastAuditTime']= time(); //管理员ID
        $result=$this->getModel('CustomDocUpload')->updateData($data,'DUID='.$DuID);
        if($result!=false){
            $this->showSuccess('审核不通过');
        }
    }

    /**
     * 文档提取
     * @author demo 
     */
    public function extractTest(){
        $doc = $_GET['docid'];
        if(empty($doc)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $model = $this->getModel('CustomDocUpload');
        $buffer = $this->getModel('TestTag')->selectData(
            '*',
            '1=1',
            'OrderID asc');
        $tags = array ();
        $testField = array ();
        foreach ($buffer as $iBuffer) {
            $tags[] = $iBuffer['DefaultStart'];
            $testField[] = $iBuffer['TestField'];
        }
        array_pop($buffer);
        $data = $model->extractTest($doc, $tags);
        $doc = $model->findData('*', 'DUID='.$doc);
        if(1 != $doc['Status']){
            $this->setError('30814');
        }
        //获取题型的缓存，用于格式化的处理
        $cache = $this->getApiCommon('Types/typesSubject');
        $cache = $cache[$doc['SubjectID']];
        $this->assign('types', json_encode($cache));
        $this->assign('newarr', $data);
        $this->assign('start', $tags);
        $this->assign('testfield', $testField);
        $this->assign('tag_array', $buffer);
        $this->assign('edit', $doc);
        $this->assign('pageName', '校本题库文档试题提取');
        $this->display();
    }

    /**
     * 提取试题
     * @author demo 2015-12-7
     */
    public function testSave(){
        $doc = $_POST['docid'];
        if(empty($doc)){
            $this->setError('30301', 1); //数据标识不能为空！
        }
        $docModel = $this->getModel('CustomDocUpload');
        $uploadData = $docModel->findData('SubjectID,Title,UserID,Status,IsTpl', 'DUID='.$doc);
        if(empty($uploadData)){
            $this->setError('30815', 1); //文档不存在
        }
        if(1 != $uploadData['Status']){
            $this->setError('30814',1);
        }
        $model = $this->getModel('CustomTestDoc');
        $data = $_POST['data'];
        $data['SubjectID'] = $uploadData['SubjectID'];
        $data['UserID'] = $uploadData['UserID'];
        $data['Source'] = $uploadData['Title'];
        $data['attributes'] = $_POST['attributes'];
        $data['Diff'] = 0;
        $data['GradeID'] = 0;
        $data['act'] = 'add';
        $order = (int)$_POST['OrderRule'];
        $last = (int)$_POST['last'] === 0 ? true : false; //标识当前数据是否为当前文档中的最后一个序号
        $testModel = $this->getModel('CustomTest');
        $result = $model->sequentialQuery($doc, $order, $order);
        if($result === false){
            $this->setError('30308', 1);
        }
        $empty = empty($result);
        //修改当前的单题
        if(!$empty){
            $data['TestID'] = $result[0]['TestID'];
            $data['act'] = 'edit';
        }
        //操作个人试题库
        $result = $testModel->saveData(
            $data, 
            $this->getModel('CustomTestAttr'),
            $this->getModel('CustomTestKnowledge'),
            $this->getModel('CustomTestChapter'),
            $this->getModel('CustomTestJudge')
        );
        if($result === false){
            $error = !$empty ? '30303' : '30310';
            $this->setError($error, 1);
        }
        if(!isset($data['TestID'])){
            $data['TestID'] = $testModel->getLastId();
        }
        $testid = $data['TestID'];
        unset($data);
        //操作文档试题关联表
        if($empty){
            $model->add($doc, array($testid), $order);
        }
        //如果当前这次请求为最后一条数据，则对(OrderRule > $order)的数据进行处理
        if($last){
            $result = $model->sequentialQuery($doc, $order+1);
            if(!empty($result)){
                foreach($result as $value){
                    $testModel->deleteData($value['TestID']);
                }
                $model->deleteData("OrderRule>{$order} AND DocID={$doc}");
            }
        }
        $this->setBack('success');
    }

    /**
     * 上传标引后的文档
     * @author demo 
     */
    public function uploadWord(){
        $doc = $_POST['docid'];
        if(empty($doc)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $model = D("CustomDocUpload");
        $result = $model->findData('DocPath,Status', 'DUID='.$doc);
        $info = '';
        if(1 != $result['Status']){
            $info = '无法提取，该文档已经审核完成/失败！';
        }else{
            if(!empty($result['DocPath'])){
                $model->appendDelFile($result['DocPath']);
            }
            C('WLN_DOC_OPEN_CHECK',0);
            if(empty($_FILES['doc']) || $_FILES['doc']['error'] == 4){
                $info = '请选择文件！';
            }else{
                $info=R('Common/UploadLayer/uploadWordAndCheck');
                // dump($output);exit;
                // $info = R('Common/UploadLayer/uploadWord', array('docfile', 'work'));
                if(is_numeric($info[0]) && !empty($info[0])){
                    $info = $info[1];
                }else{
                    $result = $model->updateData(
                        array('DocPath'=>$info[1]),
                        'DUID='.$doc
                    );
                    if($result === false){
                        $info = '数据保存失败！';
                    }else{
                        $info = 'success';
                    }
                }
            }
        }
        exit("<script>parent.uploadCb('{$info}');</script>");
    }
}