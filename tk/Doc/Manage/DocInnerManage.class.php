<?php
/**
 * @author demo  
 * @date 2014年8月7日
 * @update 2014年1月15日
 */
/**
 * 文档提取控制类 用于管理文档提取
 */
namespace Doc\Manage;
class DocInnerManage extends BaseController  {
    var $path='/work/';
    /**
     * 文档提取页面浏览
     * @author demo
     */
    public function index() {
        $style=$_GET['t'];
        $pageName = 'excel文档提取';
        if($style != "xls"){
            $style = "doc";
            $pageName = 'word文档提取';
        }
        //显示文档列表
        $tmpFilePath = $this->getModel('TmpFilePath');
        //是否区分用户
        if(!$this->ifDiff){
            $fileBuffer = $tmpFilePath->selectData(
                '*',
                'Status = "upload" and Style = "'.$style.'"',
                'AddTime DESC');
        }else{
            $fileBuffer = $tmpFilePath->selectData(
                '*',
                'Status = "upload" and UserName = "'.$this->getCookieUserName().'" and Style = "'.$style.'"',
                'AddTime DESC');
        }
        if($fileBuffer){
            //学科 属性 省份
            $subjectArr = SS('subject');
            $typeArr = SS('docType');
            $areaArr = SS('areaList');
            $chapterArr = SS('chapterList');
            $gradeArr = SS('grade');

            foreach($fileBuffer as $i => $iFileBuffer){
                switch($iFileBuffer['TypeID']){
                    case 1:
                        $arr = array();
                        $tmpArr = explode('|',$iFileBuffer['Attr']);
                        $tmpArr1 = explode('#',$tmpArr[3]);
                        if($tmpArr1){
                            foreach($tmpArr1 as $iTmpArr1){
                                $arr[] = $areaArr[$iTmpArr1]['AreaName'];
                            }
                        }
                        $fileBuffer[$i]['Attr'] = $tmpArr[0].'年|'.$subjectArr[$tmpArr[1]]['SubjectName'].'|'.$typeArr[$tmpArr[2]]['TypeName'].'|'.implode('#',$arr).'|'.($tmpArr[4] == 1 ? '测试' : '不测试').'|'.$gradeArr[$tmpArr[5]]['GradeName'];
                        unset($arr);
                        unset($tmpArr);
                        unset($tmpArr1);
                    break;
                    case 2:
                        $arr = array();
                        $tmpArr = explode('|',$iFileBuffer['Attr']);
                        $tmpArr1 = explode('#',$tmpArr[3]);
                        if($tmpArr1){
                            foreach($tmpArr1 as $iTmpArr1){
                                $arr[] = $chapterArr[$iTmpArr1]['ChapterName'];
                            }
                        }
                        $fileBuffer[$i]['Attr'] = $tmpArr[0].'年|'.$subjectArr[$tmpArr[1]]['SubjectName'].'|'.$typeArr[$tmpArr[2]]['TypeName'].'|'.implode('#',$arr).'|'.($tmpArr[4] == 1 ? '测试' : '不测试').'|'.$gradeArr[$tmpArr[5]]['GradeName'];
                        unset($arr);
                        unset($tmpArr);
                        unset($tmpArr1);
                    break;
                }
            }
        }
        /*载入模板标签*/
        $this->assign('list', $fileBuffer); // 赋值数据集
        $this->assign('style', $style); //页面数据类型
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 显示表格提取后数据
     * @author demo
     */
    public function getExcel(){
        $pageName = "excel知识点数据显示";
        $perPage = C('WLN_PERPAGE');
        $userName = $this->getCookieUserName(); //用户名
        if(!$this->ifDiff){
            $data = ' 1 = 1 ';
        }else{
            $data = ' UserName = "'.$userName.'" ';
        }
        if ($_REQUEST['name']) {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND DocName like "%' . $_REQUEST['name'] . '%" ';
        } else {
            if ($_REQUEST['docName']) {
                $map['DocName'] = $_REQUEST['docName'];
                $data .= ' AND DocName like "%' . $_REQUEST['docName'] . '%" ';
            }
            if (is_numeric($_REQUEST['status'])) {
                $map['Status'] = $_REQUEST['status'];
                $data .= ' AND Status = "' . $_REQUEST['status'] . '" ';
            }
        }
        $count = $this->getModel('TmpDocKl')->selectCount(
            $data,
            '*'); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=page($count,$_GET['p'],$perPage).','.$perPage;
        $list = $this->getModel('TmpDocKl')->pageData('*',$data,'TmpID DESC',$page);
        $this->pageList($count, $perPage, $map);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display('DocInner/getExcel');
    }
    /**
     * 上传文档
     * @author demo
     */
    public function add(){
        $pageName = "上传文档";
        //上传数据的参数
        //年份 学科 省份 类型 测试
        $subjectArray = SS('subjectParentId');//获取学科数据集
        $doctypeArray = SS('docType');//获取属性数据集
        $docSource=SS('docSource'); //文档来源
        $param['style'] = 'area';
        $param['pID'] = 0;
        $areaArray = $this->getData($param);//获取省份数据集
        $userName = $this->getCookieUserName();
        $key = md5(C('DOC_HOST_KEY').$userName.date("Y.m.d",time()));
        $thisYear=date('Y',time())+1;//当前年份+1，从下一年开始循环
        $this->assign('thisYear', $thisYear);

        /*载入模板标签*/
        $this->assign('subjectArray', $subjectArray);
        $this->assign('doctypeArray', $doctypeArray);
        $this->assign('areaArray', $areaArray);
        $this->assign('docSource', $docSource);
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('tkey', $key); //验证码
        $this->assign('userName', $userName); //用户名
        $this->display('DocInner/upload');
    }
    /**
     * 删除文档
     * @author demo
     */
    public function del(){
        $id = $_REQUEST['id'];
        if(!$id){
            $this->setError('30301');//数据标识不能为空！
        }
        $buffer = $this->getModel('TmpFilePath')->selectData(
            '*',
            'FileID in ('.$id.')');
        if($buffer){
            $userName = $this->getCookieUserName();
            foreach($buffer as $iBuffer){
                if($iBuffer['Status'] != 'upload'){
                    $this->setError('1X1002','',U('DocInner/index',array('t'=>$iBuffer['Style'])));//数据状态有误，删除数据失败！
                }
                if($iBuffer['UserName'] != $userName && $this->ifDiff){
                    $this->setError('1X1003','',U('DocInner/index',array('t'=>$iBuffer['Style'])));//用户权限有误，删除数据失败！
                }
            }
        }
        if($this->getModel('TmpFilePath')->updateData(
                array('Status' => 'delete'),
                'FileID in ('.$id.')') === false){
            $this->setError('30302','',U('DocInner/index',array('t'=>$buffer[0]['Style'])));//删除失败！
        }else{
            $this->showSuccess('删除成功！',U('DocInner/index',array('t'=>$buffer[0]['Style'])));
        }
    }
    /**
     * 删除excel提取数据
     * @author demo
     */
    public function delete(){
        $ID = $_REQUEST['id'];    //获取数据标识
        if(!$ID){
            $this->setError('30301');//数据标识不能为空！
        }
        $tmp = $this->getModel('TmpDocKl');
        $buffer = $this->getModel('TmpDocKl')->selectData(
            'TmpID in ('.$ID.')');
        $userName = $this->getCookieUserName();
        foreach($buffer as $iBuffer){
            if($iBuffer['UserName'] != $userName && $this->ifDiff){//是否区分用户
                $this->setError('1X1003','',U('DocInner/getExcel'));//用户权限有误，删除数据失败！
            }
        }
        if($this->getModel('TmpDocKl')->selectData(
                'TmpID in ('.$ID.')') === false){
            $this->setError('30302','',U('DocInner/getExcel'));//删除数据失败！
        }else{
            $this->showSuccess('删除数据成功！',U('DocInner/getExcel'));
        }
    }
    /**
     * 自动提取数据
     * @author demo
     */
    public function auto() {
        set_time_limit(0);
        $style = $_GET['t'];
        if(!$style){
            $this->showSuccess('正在提取请稍候。。。下面转入excel提取！',U('DocInner/auto',array('t'=>'excel')));
            exit();
        }
        $error = array();
        $userName = $this->getCookieUserName(); //用户名
        if($style == 'excel'){
            //提取excel
            if(!$this->ifDiff){//是否区分用户
                $buffer = $this->getModel('TmpFilePath')->selectData(
                    '*',
                    'Status = "upload" and Style = "xls"',
                    'AddTime ASC');
            }else{
                $buffer = $this->getModel('TmpFilePath')->selectData(
                    '*',
                    'Status = "upload" and Style = "xls" and UserName = "'.$userName.'"',
                    'AddTime asc');
            }
            if($buffer){
                foreach($buffer as $iBuffer){
                    $tmpError = $this->setExcel2db($iBuffer['FilePath']);
                    if(strstr($tmpError,'error')){
                        $tmpError .= '|'.$iBuffer['FileName'];
                        //修改数据标示状态
                        $this->getModel('TmpFilePath')->updateData(
                            array('Status' => "error"),
                            'FileID = '.$iBuffer['FileID']);
                    }else if($tmpError){
                        $tmpError .= '|'.$iBuffer['FileName'];
                        $this->getModel('TmpFilePath')->updateData(
                            array('Status' => "success"),
                            'FileID = '.$iBuffer['FileID']);
                    }else{
                        $this->getModel('TmpFilePath')->updateData(
                            array('Status' => "typeerror"),
                            'FileID = '.$iBuffer['FileID']);
                    }
                    if($tmpError) $error[] = $tmpError;
                }
                   if($this->checkError($error,'error')){
                    $this->addLog('一键提取数据',implode(';',$error),1);
                }else{
                    $this->addLog('一键提取数据',implode(';',$error),0);
                }
                $this->showSuccess('成功提取excel。。。下面转入word提取！',U('DocInner/auto',array('t'=>'word')));
                exit();
            }else{
                $style = 'word';
            }
        }
        if($style == 'word'){
            //提取word
            if(!$this->ifDiff){
                $buffer = $this->getModel('TmpFilePath')->selectData(
                    '*',
                    'Status = "upload" and Style = "doc"',
                    'AddTime ASC',
                    '2');
            }else{
                $buffer = $this->getModel('TmpFilePath')->selectData(
                    '*',
                    'Status = "upload" and Style = "doc" and UserName = "'.$userName.'"',
                    'AddTime ASC',
                    '2');
            }
            if($buffer){
                $bufferN = $buffer[0];
                    if(empty($bufferN['Attr']))
                        $tmpError = $this->setWord2db($bufferN['FilePath'],$bufferN['FileName']);
                    else
                        $tmpError = $this->setWord2db($bufferN['FilePath'],$bufferN['FileName'],$bufferN['Attr'],$bufferN['TypeID']);
                    if(strstr($tmpError,'error')){
                        $tmpError .= '|'.$bufferN['FileName'];
                        //修改数据标示状态
                        $this->getModel('TmpFilePath')->updateData(
                            array('Status' => "error"),
                            'FileID = '.$bufferN['FileID']);
                    }else if($tmpError){
                        $this->getModel('TmpFilePath')->updateData(
                            array('Status' => "success"),
                            'FileID = '.$bufferN['FileID']);
                    }else{
                        $this->getModel('TmpFilePath')->updateData(
                            array('Status' => "typeerror"),
                            'FileID = '.$bufferN['FileID']);
                    }
                    if($tmpError) $error[] = $tmpError;
                if($this->checkError($error,'error')){
                    $this->addLog('一键提取数据',implode(';',$error),1);
                }else{
                    $this->addLog('一键提取数据',implode(';',$error),0);
                }
                if($buffer[1]['FileName']){
                    $this->showSuccess('完成提取“'.$buffer[0]['FileName'].'”！下面提取“'.$buffer[1]['FileName'].'”',U('DocInner/auto',array('t'=>'word')));
                    exit();
                }
            }
        }
        if($this->checkError($error,'error')){
            $this->setError('1X1004','',U('DocInner/getlog'));//提取失败！请查看提取日志
        }else{
            $this->showSuccess('提取完成！请检查提取结果',U('DocInner/index'));
        }
    }
    /**
     * 日志查看
     * @author demo
     */
    public function getLog() {
        $pageName = '提取日志';
        $userName = $this->getCookieUserName(); //用户名
        if(!$this->ifDiff){//是否区分用户
            $data = ' 1 = 1 ';
        }else{
            $data = ' UserName = "'.$userName.'" ';
        }

        if ($_REQUEST['name']) {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND DocName like "%' . $_REQUEST['name'] . '%" ';
        } else {
            if ($_REQUEST['docName']) {
                $map['DocName'] = $_REQUEST['docName'];
                $data .= ' AND DocName like "%' . $_REQUEST['docName'] . '%" ';
            }
            if (is_numeric($_REQUEST['status'])) {
                $map['Status'] = $_REQUEST['status'];
                $data .= ' AND Status = "' . $_REQUEST['status'] . '" ';
            }
        }
        $perPage = C('WLN_PERPAGE');
        $page = isset ($_GET['p']) ? $_GET['p'] : 1;
        $count = $this->getModel('TmpLog')->selectCount(
            $data,
            "LogID"); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=$page.','.$perPage;
        $list = $this->getModel('TmpLog')->pageData(
            '*',
            $data,
            'LogID DESC',
            $page);

        if($list){
            foreach($list as $i => $iList){
                $tmpArr = explode(';',$iList['Remark']);
                if($tmpArr){
                    foreach($tmpArr as $j => $iTmpArr){
                        $jTmpArr = explode('|',$iTmpArr);
                        if($jTmpArr[0] == 'error') $tmpArr[$j] = '<font color = "red">'.$jTmpArr[2].'--状态：失败--原因：'.$jTmpArr[1].'</font>';
                        else if($jTmpArr[0] == 'success') $tmpArr[$j] = $jTmpArr[1].'--状态：成功';
                    }
                }
                $list[$i]['Remark'] = implode('<br/>',$tmpArr);
            }
        }
        $this->pageList($count, $perPage, $map);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display('DocInner/getLog');
    }
    /**
     * 文档提取
     * @author demo
     */
    public function getDocData() {
        set_time_limit(0);
        $id = $_REQUEST['id'];
        if (empty ($id)) {
            $this->setError('30301');//数据标识不能为空！
            exit();
        }
        $all = array('xlsx','xls','docx','doc');
        $typeExcel = array('xlsx','xls');
        $typeWord = array('docx','doc');
        $error = array();
        $style = 'excel';

        $buffer = $this->getModel('TmpFilePath')->selectData(
            '*',
            'FileID in ('.$id.')');
        if($this->ifDiff){//是否区分用户
            $userName = $this->getCookieUserName();
            foreach($buffer as $i => $iBuffer){
                if($iBuffer['Status'] != 'upload'){
                    $this->setError('1X1002');//数据状态有误！
                    exit();
                }
                if($iBuffer['UserName'] != $userName){
                    $this->setError('1X1002');//数据状态有误！
                    exit();
                }
            }
        }else{
            foreach($buffer as $i => $iBuffer){
                if($iBuffer['Status'] != 'upload'){
                    $this->setError('1X1002');//数据状态有误！
                    exit();
                }
            }
        }
        foreach($buffer as $i => $iBuffer){
            $ext = formatString('delPointData',$iBuffer['FilePath'],2);
            $tmpError = '';
            if(in_array($ext,$typeExcel)){
                $style = 'xls';
                $tmpError = $this->setExcel2db($iBuffer['FilePath']);
            }
            else if(in_array($ext,$typeWord)){
                $style = 'doc';
                if(empty($iBuffer['Attr']))
                    $tmpError = $this->setWord2db($iBuffer['FilePath'],$iBuffer['FileName']);
                else
                    $tmpError = $this->setWord2db($iBuffer['FilePath'],$iBuffer['FileName'],$iBuffer['Attr'],$iBuffer['TypeID']);
            }
            if(strstr($tmpError,'error')){
                $tmpError .= '|'.$iBuffer['FileName'];
                //修改数据标示状态
                $this->getModel('TmpFilePath')->updateData(
                    array('Status' => "error"),
                    'FileID = '.$iBuffer['FileID']);
            }else if($tmpError){
                $tmpError .= '|'.$iBuffer['FileName'];
                $this->getModel('TmpFilePath')->updateData(
                    array('Status' => "success"),
                    'FileID = '.$iBuffer['FileID']);
            }else{
                $this->getModel('TmpFilePath')->updateData(
                    array('Status' => "typeerror"),
                    'FileID = '.$iBuffer['FileID']);
            }
            if($tmpError) $error[] = $tmpError;
        }
        $tmpError = implode(';',$error);

        if($this->checkError($error,'error')){
            $errorData=[
                'msg'=>'文档ID为：'.$id.',试题提取失败，请管理员查看！',
                'sql'=>'空',
                'description'=>$tmpError
            ];
            D('Base')->addErrorLog($errorData);
            $this->addLog('手动提取数据',$tmpError,1);
            $this->setError('1X1004','',U('DocInner/getlog'));//提取失败，请查看日志！
            exit();
        }
        $this->addLog('手动提取数据',$tmpError,0);
        $this->showSuccess('提取完毕，请检查提取结果！',U('DocInner/index',array('t'=>$style)));
    }
    /**
     * 添加日志
     * @param $docName string 文档名称
     * @param $remark string 备注信息
     * @param int $status 日志状态
     * @author demo
     */
    protected function addLog($docName,$remark,$status = 0) {
        $data = array();
        $data['DocName'] = $docName;
        $data['Status'] = $status;
        $data['Remark'] = $remark;
        $data['LoadTime'] = time();
        $data['UserName'] = $this->getCookieUserName();
        $this->getModel('TmpLog')->insertData($data);
    }
    /**
     * 获取文档列表
     * @author demo
     */
    protected function getFileList($dir,$type = 'excel'){
        $typeArr = array();
        $all = array('xlsx','xls','docx','doc');
        if($type == 'all'){$typeArr = $all;}
        else if($type == 'excel'){$typeArr = array('xlsx','xls');}
        else if($type == 'word'){$typeArr = array('docx','doc');}
        else return;

        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if($file != '.' and $file != '..'){
                           if(!is_dir($dir.$file)){
                               $file = iconv('gbk','utf-8//IGNORE',$file);
                               $typeTmp = explode('.',$file);
                               if(in_array($typeTmp[count($typeTmp)-1],$typeArr) && count($typeTmp)>1) $arr[] = $file;
                               else if(!in_array($typeTmp[count($typeTmp)-1],$all)) $this->changeFile($file,2);//移动非word和excel到other文件夹
                           }
                    }
                }
                closedir($dh);
            }
        }
        return $arr;
    }
    /**
     * 检查数组$arr是否有$str字符串
     * @author demo
     */
    protected function checkError($arr,$str){
        if($arr){
            foreach($arr as $i => $iArr){
                if(strstr($iArr,$str)) return true;
            }
        }
        return false;
    }
    /**
     * 处理word文档 并入库
     * @author demo
     */
    protected function setWord2db($file,$docName,$attr = 0,$typeID = 0){
        $doc = $this->getModel('Doc');
        $tmpDocName = trim(formatString('delPointData',$docName));
        //判断docName是否重复
        $buffer = $this->getModel('Doc')->selectData(
            '*',
            'DocName = "'.$tmpDocName.'"');
        if($buffer){
            return 'error|'.$docName.'文档名称重复！';
        }

        $error = array();
        $host = C('WLN_DOC_HOST_IN');

        //复制word到Uploads文件夹
        if($host){
            $url=$host.R('Common/UploadLayer/getDocServerUrl',array($file,'movedoc',''));
            $docPath = file_get_contents($url);

            if(strstr($docPath,'error') || empty($docPath)){
                $data['description'] = "获取文件内容失败";
                $data['msg'] = $url;
                D('Base')->addErrorLog($data);
                return 'error|'.$docPath.'|'.$docName;
            }
        }else{
            $docPath = '/Uploads/word/'.date('Y/md',time());
            $target = realpath('./').$docPath;
            if(!file_exists($target)){
                mkdir($target,0755);
            }
            $newFile = uniqid().rand(100000,999999).'.'.formatString('delPointData',$file,2);
            $realPath = $target.'/'.$newFile;
            $docPath = $docPath.'/'.$newFile;

            if(!copy(iconv('utf-8','gbk//IGNORE',realpath('./').$file),$realPath)){
                $data['description'] = 'error|拷贝文件到上传路径失败|'.$docName;
                $data['msg'] = $realPath;
                D('Base')->addErrorLog($data);
                return 'error|拷贝文件到上传路径失败|'.$docName;
            }
            //word转html
            $doc->word2html($realPath);
        }

        //获取数据doc属性 存入数据库
        $docName = $tmpDocName;
        $strLs = formatString('delPointData',$docPath); //去掉扩展名
        $data = array();
        $data['DocName'] = $docName; //文档名称

        $docName = str_replace(array('（','）'),array('(',')'),$docName);
        $data['Description'] = $data['DocName'].'(文档自动入库)'; //描述
        switch($typeID){
            case 0:
                //查找知识点对应数据
                $userName = $this->getCookieUserName();
                //权限 是否区分用户
                if(!$this->ifDiff){
                    $bufferK = $this->getModel('TmpDocKl')->selectData(
                        '*',
                        'DocName = "'.str_replace(array('（','）'),array('(',')'),$docName).'"',
                        'TmpID DESC',
                        '1');
                }else{
                    $bufferK = $this->getModel('TmpDocKl')->selectData(
                        '*',
                        'UserName = "'.$userName.'" and DocName = "'.str_replace(array('（','）'),array('(',')'),$docName).'"',
                        'TmpID DESC',
                        '1');
                }
                if(!$bufferK){
                    $error[] = '找不到匹配的知识点文件|'.$docName;
                }

                //获取学科
                $subjectID = 0;
                $stwl = 0;//试题文理判断  只有数学有

                $bufferSubject = SS('subject'); //学科名称查id数组

                //从excel中提取学科
                if($bufferK){
                    //判断数学文理
                    if(strstr($bufferK[0]['SubjectName'],'数学文')){
                        $stwl = 3;
                        $bufferK[0]['SubjectName'] = str_replace('文','',$bufferK[0]['SubjectName']);
                    }else if(strstr($bufferK[0]['SubjectName'],'数学理')){
                        $stwl = 1;
                        $bufferK[0]['SubjectName'] = str_replace('理','',$bufferK[0]['SubjectName']);
                    }
                    foreach($bufferSubject as $i => $iBufferSubject){
                        if(strstr($iBufferSubject['SubjectName'],$bufferK[0]['SubjectName'])){
                            $subjectID = $iBufferSubject;
                            break;
                        }
                    }
                }
                //从word标题中提取学科
                if(!$subjectID){
                    //判断数学文理
                    //$t_subject = '';没找到下一个
                    if(strstr($docName,'数学文')){
                        $stwl = 3;
                    }else if(strstr($docName,'数学理')){
                        $stwl = 1;
                    }
                    foreach($bufferSubject as $i => $iBufferSubject){
                        if(strstr($docName,$iBufferSubject['SubjectName'])){
                            $subjectID = $iBufferSubject;
                            break;
                        }
                    }
                }
                $data['SubjectID'] = $subjectID;
                if(!$data['SubjectID']) $data['SubjectID'] = 0;

                $bufferDoctype = SS('docType'); //文档属性
                $typeID = 0;
                foreach($bufferDoctype as $i => $iBufferDoctype){
                    if(strstr($docName,$iBufferDoctype['Tag']) && !empty($iBufferDoctype['Tag'])){
                        $typeID = $i;
                        break;
                    }
                }
                $data['TypeID'] = $typeID;
                $yearStr = preg_replace('/^[^\d]*(\d{4}).*/is','\\1',$docName); //年份
                if(is_numeric($yearStr)) $data['DocYear'] = $yearStr;
                else $data['DocYear'] = 0;
            break;
            case 1:
                //预设值属性
                $tmpArr = explode('|',$attr);
                $data['SubjectID'] = $tmpArr[1];
                $data['TypeID'] = $tmpArr[2];
                $data['DocYear'] = $tmpArr[0];
                $data['IfTest'] = $tmpArr[4];
                $data['GradeID'] = $tmpArr[5];
                $arrAreaID = explode('#',$tmpArr[3]);
            break;
            case 2:
                //预设值属性
                $tmpArr = explode('|',$attr);
                $data['SubjectID'] = $tmpArr[1];
                $data['TypeID'] = $tmpArr[2];
                $data['DocYear'] = $tmpArr[0];
                $data['IfTest'] = $tmpArr[4];
                $data['GradeID'] = $tmpArr[5];

                $arrChapterID = explode('#',$tmpArr[3]);
            break;
        }
        $data['Admin'] = $this->getCookieUserName();
        $data['DocPath'] = $docPath;
        $data['DocFilePath'] = $strLs . '.files';
        $data['DocHtmlPath'] = $strLs . '.htm';
        $data['LoadTime'] = time();

        if (($docID = $this->getModel('Doc')->insertData(
                $data)) === false) {
            $error[] = '添加doc文档信息失败|'.$docName;
        } else {
            //添加中间表数据
            //获取省份
            if(empty($attr)){
                $arrAreaID = array();//存储省份id
                $bufferArea = SS('areaChildList');  // 缓存子类list数
                if($bufferK){
                    $bufferTmpdocklArea=explode(';',$this->changeBD($bufferK[0]['AreaName']));
                }else{
                    $bufferTmpdocklArea=explode(';',$this->changeBD(preg_replace('/[^\(]*[\(]([^\)]*)(.*)/is','\\1',$docName)));
                }

                if($bufferTmpdocklArea){
                    foreach($bufferArea[0] as $iBufferArea){
                        foreach($bufferTmpdocklArea as $jBufferTmpdocklArea){
                            if(strstr($iBufferArea['AreaName'],$jBufferTmpdocklArea)){
                                $arrAreaID[]=$iBufferArea['AreaID'];
                            }
                        }
                    }
                }
            }

            if($arrAreaID){
                foreach($arrAreaID as $iArrAreaId){
                    $data = array();
                    $data['DocID'] = $docID;
                    $data['AreaID'] = $iArrAreaId;
                    $this->getModel('DocArea')->insertData(
                        $data);
                }
            }
            /**提取试题**/
            $doc->getTestByDoc($docID,array(),$this->getCookieUserName());
            if($arrChapterID){
                /**添加章节**/
                $bufferTest = $this->getModel('Test')->selectData(
                    '*',
                    'DocID = '.$docID,'TestID ASC');
                foreach($bufferTest as $iBufferTest){
                    $data = array();
                    $data['TestID'] = $iBufferTest['TestID'];
                    foreach($arrChapterID as $iArrChapterid){
                        $data['ChapterID'] = $iArrChapterid;
                        $this->getModel('TestChapter')->insertData(
                            $data);
                    }
                }
            }

            if(empty($attr)){
                /**添加知识点**/
                //获取试题列表
                $this->getModel('TestAttr')->updateData(
                    array('IfWL' => $stwl),
                    'DocID = '.$docID);//修改试题对应数学文理属性
                $bufferTest = $this->getModel('Test')->selectData(
                    '*',
                    'DocID = '.$docID,
                    'TestID ASC');
                //获取试题对应知识点id
                $arr = explode('|',$bufferK[0]['KlList']);
                $arr1 = array();
                $arr2 = array();
                if($arr){
                    $j = 0; //知识点数组序号  检测是否有小题 如果有则自加
                    foreach($bufferTest as $iBufferTest){
                        $xt = $doc->xtnum($iBufferTest['Test'],1);
                        $klArr = array();
                        if($xt>0){
                            for($i = 0;$i<$xt;$i++){
                                $klArr = array_merge($klArr,$this->getKlListByStr($arr[$j]));
                                $j++;
                            }
                            $klArr = array_unique(array_filter($klArr));
                            if($klArr){
                                $this->getKlByList($subjectID,$iBufferTest['TestID'],$klArr);
                            }
                        }else{
                            $this->getKlByList($subjectID,$iBufferTest['TestID'],$this->getKlListByStr($arr[$j]));
                            $j++;
                        }
                    }
                }
            }
            if($error){
                return 'error|'.implode('#',$error);
            }
            return 'success|'.$docName;
        }
    }
    protected function getKlListByStr($str){
        $str = str_replace(array('“','”'),array('"','"'),$str);
        $arr1 = explode('#',$str);
        $arr2 = explode(';',$arr1[1]);
        return array_unique(array_filter($arr2));
    }
    /**
     * 查询知识点 并入库
     * @author demo
     */
    protected function getKlByList($subjectID,$testID,$klName){
        $arr = $klName;
        if($arr){
            $tmpKl = array(); //存储知识点数据
            $knowledge = $this->getModel('Knowledge');
            //$chapterKl = M('ChapterKl');未用到
            //$testChapter = M('TestChapter');未用到
            $arrZt = array();
            $strList = implode(';',$klName);
            if(strstr($strList,'[zt]') && !strstr($strList,'$')){
                foreach($arr as $i => $iArr){
                    if(strstr($iArr,'[zt]')){
                        $bufferKl = $this->getModel('Knowledge')->selectData(
                            '*',
                            'SubjectID = '.$subjectID.' and KlName = "'.str_replace('[zt]', '',$iArr).'"');
                        if($bufferKl){
                            $arrZt[] = $bufferKl[0]['KlID'];
                            $bufferKl = $this->getModel('Knowledge')->selectData(
                                '*',
                                'SubjectID = '.$subjectID.' and PID = "'.$bufferKl[0]['KlID'].'"');
                            if($bufferKl) unset($arr[$i]);
                            else $arr[$i] = str_replace('[zt]','',$arr[$i]);
                        }
                    }
                }
            }
            //清除试题对应知识点
            $this->getModel('TestKl')->deleteData(
                'TklID in （'.$testID.')');
            //循环添加试题对应知识点
            if (get_magic_quotes_gpc()) {
                $_GET = $this->stripslashes_deep($_GET);
                $_POST = $this->stripslashes_deep($_POST);
                $_COOKIE = $this->stripslashes_deep($_COOKIE);
            } else {
                $arr = $this->add_magic_quotes($arr);
            }
            foreach($arr as $iArr){
                $klID = 0;
                $ywChuli = 0;
                if(strstr($iArr,'$')){
                    $ywChuli = 1;
                    $aa = explode('$',$iArr);
                    $bufferKl = $this->getModel('Knowledge')->selectData(
                        '*',
                        'SubjectID = '.$subjectID.' and KlName = "'.$aa[1].'"');
                }else{
                    $bufferKl = $this->getModel('Knowledge')->selectData(
                        '*',
                        'SubjectID = '.$subjectID.' and KlName = "'.$iArr.'"');
                }

                if(count($bufferKl)>1){
                    if($arrZt){
                        foreach($arrZt as $iAarrZt){
                            $bufferKl2 = $this->getModel('Knowledge')->selectData(
                                '*',
                                'SubjectID = '.$subjectID.' and KlName = "'.$iArr.'" and PID = '.$iAarrZt);
                            if($bufferKl2) $klID = $bufferKl2[0]['KlID'];
                        }
                    }
                    if($ywChuli){
                        $bb = $this->getModel('Knowledge')->selectData(
                            '*',
                            'SubjectID = '.$subjectID.' and KlName="'.str_replace('[zt]','',$aa[0]).'"');
                        $bufferKl2 = $this->getModel('Knowledge')->selectData(
                            '*',
                            'SubjectID = '.$subjectID.' and KlName = "'.$aa[1].'" and PID = '.$bb[0]['KlID']);
                        if($bufferKl2) $klID = $bufferKl2[0]['KlID'];
                    }
                }
                else if(count($bufferKl) == 1){
                    $klID=$bufferKl[0]['KlID'];
                }
                if($klID){
                    $data=array();
                    $data['TestID'] = $testID;
                    $data['KlID'] = $klID;
                    $tmpKl[] = $klID;
                    $this->getModel('TestKl')->insertData(
                        $data);
                }
            }
            //查找知识点载入默认章节 带入章节规则
            if($tmpKl){
                $chapterKl = $this->getModel('ChapterKl');
                $chapterList=$chapterKl->getChapterByKl($tmpKl);
                if($chapterList){
                    $chapter = $this->getModel('Chapter');
                    $chapterList = $chapter->filterChapterID($chapterList);
                    if($chapterList){
                        foreach($chapterList as $iChapterList){
                            $data = array();
                            $data['TestID'] = $testID;
                            $data['ChapterID'] = $iChapterList;
                            $this->getModel('TestChapter')->insertData(
                                $data);
                        }
                    }
                }
            }
        }
    }
    /**
     * 处理excel文档 并入库
     * @author demo
     */
    protected function setExcel2db($file){
        $arr = $this->getKL($file);
        if(strstr($arr,'error')) return $arr;
        if($arr){
            foreach($arr as $iArr){
                $data = array();
                $a = array();
                $a = explode('|',$iArr[0]);
                $buffer = $this->getModel('TmpDocKl')->selectData(
                    '*',
                    'DocName="'.$a[0].'"',
                    '',
                    '1');
                if($buffer) $data['Repeat'] = 1;
                else $data['Repeat'] = 0;
                $klList = array();
                if($iArr[1]){
                    foreach($iArr[1] as $j => $jArr){
                        $klList[] = $j.'#'.$jArr;
                    }
                }
                $data['DocName'] = $a[0];
                $data['SubjectName'] = $a[1];
                $data['AreaName'] = $a[2];
                $data['KlList'] = implode('|',$klList);
                $data['SourceDocName'] = $file;
                $data['AddTime'] = time();
                $data['UserName'] = $this->getCookieUserName();
                $this->getModel('TmpDocKl')->insertData(
                    $data);
            }
            //$this->changeFile($file,1);
            return 'success';
        }else{
            //$this->changeFile($file,0);
            return 'error|知识点内容为空';
        }

    }
    /**
     * 移动文档并更名
     * @author demo
     */
    protected function changeFile($file,$success=1){
        //判断文档文件夹是否存在
        if($success == 1){
            $dir = realpath('./').$this->path.'success';
        }else if($success == 2){
            $dir = realpath('./').$this->path.'other';
        }else{
            $dir = realpath('./').$this->path.'error';
        }
        if(!file_exists($dir)){
            mkdir($dir,0755);
        }
        $timeFolder = date('Ymd',time());
        $dir = $dir.'/'.$timeFolder;
        if(!file_exists($dir)){
            mkdir($dir,0755);
        }
        $file = iconv('Utf-8','gbk//IGNORE',$file);
        rename(realpath('./').$this->path.$file,$dir.'/'.$file);
    }
    //获取分词 $str 要分词的字符串
    //未见本类调用************************************************
    protected function getKey($myData){
        $ignore = $showa = $stats = $duality = false;
        $checked_ignore = $checked_showa = $checked_stats = $checked_duality = '';
        $multi = 0;

        $cws = scws_new();
        $cws->set_charset('utf-8');
        $cws->set_rule(ini_get('scws.default.fpath') . '/rules.utf8.ini');
        $cws->set_dict(ini_get('scws.default.fpath') . '/dict.utf8.xdb');

        //
        // use default dictionary & rules
        //
        $cws->set_duality($duality);
        $cws->set_ignore($ignore);
        $cws->set_multi($multi);
        $cws->send_text($myData);
        $arr = array();
        while ($res = $cws->get_result())
        {
            foreach ($res as $tmp)
            {
                if ($tmp['len'] == 1 && $tmp['word'] == "\r")
                    continue;
                if ($tmp['len'] == 1 && $tmp['word'] == "\n")
                    continue;
                else if ($showa){
                   // printf("%s/%s ", $tmp['word'], $tmp['attr']);
                    if(mb_strlen($tmp['word'],'UTF-8')>1) $arr[]=$tmp['word'];
                }else{
                    //printf("%s ", $tmp['word']);
                    if(mb_strlen($tmp['word'],'UTF-8')>1) $arr[]=$tmp['word'];
                }
            }
        }
        return $arr;
    }
    /**
     * 获取试题对应知识点数组 $file 文件地址
     * @author demo
     */
    protected function getKL($filePath) {
        $host = C('WLN_DOC_HOST_IN');
        if($host){
            $url=$host.R('Common/UploadLayer/getDocServerUrl',array($filePath,'getxls',''));
            $tmpArr = file_get_contents($url);
            if(strstr($tmpArr,'error|') || !$tmpArr){
                return $tmpArr;
            }else{
                return json_decode($tmpArr,true);
            }
        }else{
            $filePath = realpath('./').$filePath;
        }
        import('ORG.Util.PHPExcel');
        //require 'Excel/Classes/PHPExcel/IOFactory.php';
        $filePath = iconv('utf-8','gbk//IGNORE',$filePath);
        $aa = new PHPExcel_Reader_Excel2007;
        $bb = new PHPExcel_Reader_Excel5;
        if(!$aa->canRead($filePath) && !$bb->canRead($filePath)){
            return 'error|文件无法读取';
        }
        $PHPExcel = PHPExcel_IOFactory::load($filePath);
        if(!$PHPExcel) return 'error|文件读取失败';
        $currentSheet = $PHPExcel->getSheet(0);//取得一共有多少列
        $allColumn = $currentSheet->getHighestColumn();//取得一共有多少行
        $allRow = $currentSheet->getHighestRow();
        $allColumn++;
        //起始点的横纵坐标
        $startl = -1;
        $starth = -1;
        $subjectTmp = $currentSheet->getCell('A1')->getValue();
        $subjectTmp = str_replace('：',':',$subjectTmp);
        $arr = explode(':',$subjectTmp);
        $subject = $arr[1];
        for ($currentRow = 1 ; $currentRow <= $allRow ; $currentRow++) {
            for ($currentColumn = 'A'; $currentColumn != $allColumn ; $currentColumn++) {
                $address = $currentColumn.$currentRow;
                if($currentSheet->getCell($address)->getValue() == '题号'){
                    $starth = $currentRow;
                    $startl = $currentColumn;
                    break;
                }
            }
        }
        if($starth == -1 || $startl == -1){
            return 'error|题号不存在请核对！';
        }

        $arrAll = array();
        //数据集合 与 考点对应关系
        $zt = 0;//专题
        $ztnameYw = '';//专题名称 语文
        for ($i = $starth-1; $i <= $allRow; $i++) {
            if($currentSheet->getCell('A'.$i)->getValue() == '专题'){
                $zt = 1;
            }
            $aa = trim($currentSheet->getCell('D'.$i)->getValue());
            if(empty($aa)) $aa = trim($currentSheet->getCell('B'.$i)->getValue());
            if(empty($aa)) $aa = trim($currentSheet->getCell('A'.$i)->getValue());

            if($subject == '语文'){
                if(trim($currentSheet->getCell('B'.$i)->getValue()))
                $ztnameYw = trim($currentSheet->getCell('B'.$i)->getValue());
            }

            if($i == $starth || (empty($aa) && $i!=$starth-1)) continue; //跳过知识点空行
            $startTmp = chr(ord($startl)-1);
            for ($j = $startTmp; $j != $allColumn; $j++) {
                $sf = $this->changeBD($currentSheet->getCell($j.($starth-2))->getValue());
                $bb = $currentSheet->getCell($j.$i)->getValue();
                $bb = str_replace('）',')',$bb);
                $bb = str_replace('（','(',$bb);

                if($j == $startTmp and $i == $starth-1) continue; //去掉第一行第一个

                $cj = $this->getLCN($j,$startl);
                if($i == $starth-1 and !empty($bb)){
                    //从标题提取省份
                    if((mb_strlen($sf,'UTF-8')<2 || (mb_strlen($sf,'UTF-8')>5 && !strstr($sf,';')) || is_numeric($sf)) and !empty($bb)){
                        $sf = preg_replace('/[^\(]*[\(]([^\)]*)(.*)/is','\\1',$bb);
                        $sf = str_replace('卷','',$sf);
                    }
                    $arrAll[$cj][0] = $bb.'|'.$subject.'|'.$sf;    //从第二列获取试卷名称
                }
                else if($j != $startTmp and !empty($bb)){
                    $arrTmp = explode(';',$this->changeBD($bb));
                    $arrTmp = array_filter($arrTmp);
                    foreach($arrTmp as $iArrTmp){
                        $iArrTmp = trim(str_replace('-','-',$iArrTmp));
                        if(strstr($iArrTmp,'-')){
                            $tmpA = explode('-',$iArrTmp);
                            for($k = $tmpA[0];$k <= $tmpA[1];$k++){
                                if($ztnameYw) $arrAll[$cj][1][$k] .= '[zt]'.$ztnameYw.'$'.$aa.';';//获取试题对应知识点
                                else if($zt) $arrAll[$cj][1][$k] .= '[zt]'.$aa.';';//获取试题对应知识点
                                else $arrAll[$cj][1][$k].=$aa.';';//获取试题对应知识点
                            }
                        }else{
                            if($ztnameYw) $arrAll[$cj][1][$iArrTmp] .= '[zt]'.$ztnameYw.'$'.$aa.';';//获取试题对应知识点
                            else if($zt) $arrAll[$cj][1][$iArrTmp] .= '[zt]'.$aa.';';//获取试题对应知识点
                            else $arrAll[$cj][1][$iArrTmp] .= $aa.';';//获取试题对应知识点
                        }
                    }
                    ksort($arrAll[$cj][1]);
                }
            }
        }
        ksort($arrAll);
        return $arrAll;
    }
    /**
     * 获取字母之间的差距
     * @author demo
     */
    protected function getLCN($a,$b){
        if($a<$b){return $this->getLCN($b,$a);}
        $output = 0;
        for($i = $b;$i != $a;$i++){
            $output++;
            if($output>1000) return 0;
        }
        return $output;
    }
    protected function changeBD($str) {
        $str = str_replace(array('（','）'),array('(',')'),$str);
        $str = preg_replace('/\n|\r|\s|①|②|③|④|⑤|\([^\)]*\)/is','',$str);
        $arr = array(
                ',' => ';',
                '，' => ';',
                ':' => ';',
                '：' => ';',
                '；' => ';'
            );
        foreach($arr as $i => $iArr){
            $find[] = $i;
            $replace[] = $iArr;
        }
        $str = str_replace($find,$replace,$str);
        return $str;
    }
}