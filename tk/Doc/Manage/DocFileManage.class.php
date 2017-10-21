<?php
/**
 * @author demo
 * @date 2014年10月31日
 */
/**
 * 管理员后台上传文档
 */
namespace Doc\Manage;
class DocFileManage extends BaseController  {
    var $moduleName = '解析分配任务管理';
    /**
     * 浏览文档列表
     * @author demo
     */
    public function index() {
        $pageName = '解析分配任务列表';
        $map = array ();
        //浏览谁的文档
        $data = ' 1=1 ';
        if($this->ifSubject && $this->mySubject){
            $data .= ' AND d.SubjectID in ('.$this->mySubject.') ';
        }elseif ($this->ifDiff) {
            $data .= ' AND d.Admin = "'.$this->getCookieUserName().'" ';
        }
        if ($_REQUEST['name']) {
            //简单查询
            $map['name'] = $_REQUEST['name'];//管理员名
            $data .= ' AND d.Admin = "' . $_REQUEST['name'] . '" ';
        } else {
            //高级查询
            if ($_REQUEST['admin']) {
                if($this->ifDiff && $this->getCookieUserName() != $_REQUEST['admin']){
                    $this->showerror('您没有权限查看该内容！');
                    return;
                }
                $map['admin'] = $_REQUEST['admin'];//管理员名
                $data .= ' AND d.Admin like "%'.$_REQUEST['admin'].'%" ';
            }
            if ($_REQUEST['fileID']) {//文档编号
                if(is_numeric($_REQUEST['fileID'])){
                    $map['fileID'] = $_REQUEST['fileID'];
                    $data .= ' AND d.FileID = "'.$_REQUEST['fileID'].'" ';
                }else{
                    $this->setError('30502');
                }
            }
            if ($_REQUEST['docName']) {//文档名
                $map['docName'] = $_REQUEST['docName'];
                $data .= ' AND d.DocName like "%'.$_REQUEST['docName'].'%" ';
            }
            if ($_REQUEST['userName']) {//用户名
                $map['userName'] = $_REQUEST['userName'];
                $data .= ' AND d.UserName like "%'.$_REQUEST['userName'].'%" ';
            }
            if ($_REQUEST['subjectID']) {//学科名
                if($this->ifSubject && $this->mySubject){
                    if(!in_array($_REQUEST['subjectID'],explode(',',$this->mySubject))){
                        $this->showerror('您不能搜索非所属学科文档！');
                    }
                }
                $map['subjectID'] = $_REQUEST['subjectID'];
                $data .= ' AND d.SubjectID = "'.$_REQUEST['subjectID'].'"';
            }
            if ($_REQUEST['iFDown']) {//是否可以修改
                $map['iFDown'] = $_REQUEST['iFDown'];
                $data .= 'AND d.IFDown = "'.$_REQUEST['iFDown'].'"';
            }
        }
        $perPage = C('WLN_PERPAGE');
        $count = $this->getModel('DocFile')->selectCount(
            $data,
            'FileID',
            'd'); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        
        $page=page($count,$_GET['p'],$perPage). ',' . $perPage;
        $list=D('Base')->unionSelect('docFilePageData',$data,$page);
        $this->pageList($count, $perPage, $map);
        $subjectBuffer = SS('subject');
        foreach($list as $i => $val){
            $list[$i]['gradeInfo'] = $subjectBuffer[$val['SubjectID']]['ParentName'].$subjectBuffer[$val['SubjectID']]['SubjectName'];
            $list[$i]['FileDescription'] = preg_replace('/\r\n|\r|\n/i','<br>', $val['FileDescription']);
        } 
        
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('subjectArray', $subjectBuffer);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加文档页面
     * @author demo
     */
    public function add() {
        $pageName = '解析分配任务添加文档';
        $act = 'add'; //模板标识
        $subjectArray = SS('subjectParentId'); //获取学科数据集
        $doctypeArray = SS('docType');//试卷类型
        $docSource=SS('docSource'); //文档来源
        $param['style'] = 'area';
        $param['pID'] = 0;
        $areaArray = $this->getData($param);
        /*载入模板标签*/
        $thisYear=date('Y',time())+1;//当前年份+1，从下一年开始循环
        $this->assign('thisYear', $thisYear);
        $this->assign('act', $act); //模板标识
        $this->assign('docSource', $docSource);
        $this->assign('subjectArray', $subjectArray);//学科数据
        $this->assign('doctypeArray', $doctypeArray);//类型数据
        $this->assign('areaArray', $areaArray);//地区数据
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑文档页面
     * @author demo
     */
    public function edit() {
        $fileID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($fileID)) {
            $this->setError('30301');//数据标识不能为空！
        }
        $pageName = '编辑文档';
        $where['FileID'] = $fileID;
        $edit = D('Base')->unionSelect('docFilePageData',$where,'1,1');
        if($edit[0]['IfDown']){
            $this->setError('1X1013');//文档已被下载，不能修改！
            exit;
        }
        $act = 'edit'; //模板标识
        $docList = unserialize($edit[0]['Attribute']);
        $docFileList = array_merge($docList,$edit[0]);
        //获取年级数据
        $classGrade = SS('gradeListSubject');
        $subject = SS('subject');
        $gradeArr = $classGrade[$subject[$edit[0]['SubjectID']]['PID']];
        //编辑谁的文档
        if($this->ifSubject && $this->mySubject){
            if (!in_array($docFileList['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712');//您不能编辑非所属学科文档！
            }
        }elseif ($this->ifDiff) {
            //判断是否可以编辑
            if ($docFileList['Admin'] != $this->getCookieUserName()) {
                $this->setError('30812','',__URL__);//您没有权限编辑！
            }
        }
        foreach ($docList['AreaID'] as $i => $value) {
            $areaList[] = $value;
        }
        $docFileList['AreaList'] = implode(',',$areaList);

        $subjectArray = SS('subjectParentId'); //获取学科数据集
        $doctypeArray = SS('docType');//试卷类型
        $param['style'] = 'area';
        $param['pID'] = 0;
        $areaArray = $this->getData($param);//地区数据
        $thisYear=date('Y',time())+1;//当前年份+1，从下一年开始循环
        $this->assign('thisYear', $thisYear);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $docFileList);//页面数据
        $this->assign('grade',$gradeArr['sub']);//班级数据
        $this->assign('subjectArray', $subjectArray);//学科数据
        $this->assign('doctypeArray', $doctypeArray);//类型数据
        $this->assign('areaArray', $areaArray);//地区数据
        $this->assign('pageName', $pageName);//页面数据
        $this->display('DocFile/add');//操作类型
    }
    /**
     * 保存文档
     * @author demo
     */
    public function save() {
        set_time_limit(0);
        $fileID = $_POST['fileID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($fileID) && $act == 'edit') {
            $this->setError('30301');//数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223');//模板标识不能为空！
        }

        $docFile = $this->getModel('DocFile');
        $datal = array ();
        $data['Admin'] = $this->getCookieUserName();
        $data['UserName'] = $_POST['userName'];
        $data['DocName'] = $_POST['docName'];
        $data['SubjectID'] = $_POST['subjectID'];
        $data['CheckStatus'] = $_POST['CheckStatus'];
        $data['FileDescription'] = $_POST['fileDescription'];

        if ($act == 'add') {
            if($this->ifSubject && $this->mySubject){
                if (!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能添加非所属学科文档！
                }
            }
            $buffer =  $this->getModel('DocFile')->selectData(
                'FileID',
                'DocName = "'.$data['DocName'].'"');
            if(!$buffer){
                $buffer =  $this->getModel('Doc')->selectData(
                    'DocID',
                    'DocName = "'.$data['DocName'].'"');
            }
        }elseif($act == 'edit'){
            $fileData = $this->getModel('DocFile')->selectData(
                'DocID,SubjectID,IfDown',
                'FileID='.$fileID);
            if($this->ifSubject && $this->mySubject){
                if (!in_array($fileData[0]['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能编辑非所属学科文档！
                }elseif(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能编辑为非所属学科文档！
                }
            }
            if($fileData[0]['IfDown'] == 1){
                $this->setError('1X1014');//用户已经下载过试卷，您不能修改！
            }
            $buffer =  $this->getModel('DocFile')->selectData(
                'FileID',
                'DocName = "'.$data['DocName'].'" and FileID != '.$fileID);
            if(!$buffer){
                $buffer =  $this->getModel('Doc')->selectData(
                    'DocID',
                    'DocName = "'.$data['DocName'].'" AND DocID!='.$fileData[0]['DocID']);
            }
        }
        if($buffer){
            $this->setError('30817');//文档名称重复！
        }
        //需串行化数据
        $datal['DocName'] = $_POST['docName'];
        $datal['Description'] = $_POST['description'];
        $datal['TypeID'] = $_POST['typeID'];
        $datal['DocYear'] = $_POST['docYear'];
        $datal['TotalScore'] = $_POST['totalScore'];
        $datal['TestTime'] = $_POST['testTime'];
        $datal['IfTest'] = $_POST['ifTest'];
        $datal['GradeID'] = $_POST['docGrade'];
        $datal['AreaID'] = $_POST['areaID'];
        $datal['Status'] = $_POST['status'];
        $datal['SourceID']=$_POST['SourceID'];
        
        if (!empty ($_FILES['photo']['name']) && !empty ($_FILES['photo']['size'])) {

            $urlPath = R('Common/UploadLayer/uploadWord',array('docfile','work')); //上传word文件
            if(is_array($urlPath)){
                $this->setError($urlPath[0],0,'',$urlPath[1]);
            }
            $data['DocPath']=$urlPath;

        }else if($act == 'add'){
             $this->setError('30822');//添加失败！请添加word文档。
        }
        if ($act == 'add') {
            $data['AddTime'] = time();
            $data['Attribute'] = serialize($datal);
            if ($this->getModel('DocFile')->insertData(
                    $data) === false) {
                $this->setError('30310');//添加失败！
            }
            //写入日志
            $this->adminLog($this->moduleName, '添加文档【' . $_POST['docName'] . '】');
            $this->showSuccess('添加成功！', __URL__);
        } else if ($act == 'edit') {

            //修改文件
            if (!empty ($_FILES['photo']['name']) && !empty ($_FILES['photo']['size'])) {
                $docFile->deleteFile($fileID);
            }
            $data['Attribute'] = serialize($datal);
            $where['FileID'] = $fileID;
            if ($this->getModel('DocFile')->updateData(
                    $data,
                    'FileID ='.$fileID) === false) {
                $this->setError('30311');//修改失败！
            } 
            $this->adminLog($this->moduleName, '修改文档FileID为【' . $_POST['fileID'] . '】的数据');
            $this->showSuccess('修改成功！', __URL__);
        } 
    }
    /**
     * 删除文档
     * @author demo
     */
    public function delete() {
        $fileID = $_POST['id']; //获取数据标识
        if (!$fileID) {
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        //删除谁的文档
        if($this->ifSubject && $this->mySubject){
            $docFileData = $this->getModel('DocFile')->selectData(
                'SubjectID',
                'FileID in ('.$fileID.')');
            foreach($docFileData as $i=>$iDocFileData){
                if(!in_array($iDocFileData['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712','',__URL__,'');//您不能删除非所属学科文档！
                }
            }
        }elseif ($this->ifDiff) {
            $buffer = $this->getModel('DocFile')->selectData(
                '*',
                'FileID in (' . $fileID . ')');
            //判断是否可以删除
            $admin = $this->getCookieUserName();
            foreach ($buffer as $iBuffer) {
                if ($iBuffer['Admin'] != $admin) {
                    $this->setError('30507');//您没有权限删除该文档！
                }
            }
        }       
        if ($this->getModel('DocFile')->deleteData(
                'FileID in ('.$fileID.')') === false) {
            $this->setError('30302','',__URL__);//删除失败！
        }else{
            $this->showSuccess('删除成功！');
        }
    }
    /**
     * 预览文档html
     * @author demo
     */
    public function showMsg() {
        $pageName = '解析分配任务详情';
        $fileID = $_GET['id']; //获取数据标识
        if (!$fileID) {
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        $where['FileID'] = $fileID;
        $thisTpl = D('Base')->unionSelect('docFilePageData',$where,'1,1');//获取单页数据
        $docList = unserialize($thisTpl[0]['Attribute']);//反串行化数据库字段信息
        $grade = SS('grade');
        $gradeData = $grade[$docList['GradeID']]['GradeName'];
        $thisTpl[0]['GradeName'] = $gradeData;
        $docTypeArr=SS('docType');
        $typeData = $docTypeArr[$docList['TypeID']]['TypeName'];
        $thisTpl[0]['TypeName'] = $typeData;
        $docFileList = array_merge($docList,$thisTpl[0]);
        /*载入模板标签*/
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('edit',$docFileList);
        $this->display();
    }
    /**
     * 选择上传教师
     * @author demo
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
        $userObj = $this->getModel('User');
        $count=$userObj->selectCount(
            $data,
            'UserID');
        // 进行分页数据查询
        $page = page($count,$_GET['p'],$perPage).','.$perPage;
        $list = $userObj->pageData(
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
     * 下载试卷信息
     * @author demo
    */
    public function down(){
        $fid = $_GET['fid'];
        if(!$fid){
            $this->setError('30301',NORMAL_ERROR,__URL__);
        }
        $docFile = $this->getModel('DocFile');
        $data='FileID='.$fid;
        if($this->ifSubject && $this->mySubject){
            $data .= ' AND SubjectID in ('.$this->mySubject.') ';
        }
        $result = $this->getModel('DocFile')->findData(
            'DocName',
            $data
        );
        $path = $docFile->fileDown($fid,false);
        if($path === ''){
            $this->setError('30706',NORMAL_ERROR,__URL__);
        }
        $paperName=$result['DocName'].'['.date('Y年m月d日').']';

        $host=C('WLN_DOC_HOST');
        if($host){
            $style='docfile';
            if(!strstr($path,$style)) $style='word';
            $url=$host.R('Common/UploadLayer/getDocServerUrl',array($path,'down',$style,$paperName));
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
     * 对文档进行审核
     * @author demo 
     */
    public function check(){
        $id = (int)$_GET['id'];
        if(!$id){
            $this->setError('30301',NORMAL_ERROR,__URL__);
        }
        $status = $_GET['status'];
        $result = $this->getModel('DocFile')->check($id, $status);
        if($result === false){
            $this->setError('1X1001','',__URL__);//删除失败！
        }
        $this->showSuccess('审核成功！');
    }

    /**
     * 获取试卷类型
     * @author demo
     */
    public function gettypes(){
        $TypeID=$_POST['TypeID'];
        $typeArray=SS('docType');
        $this->setBack($typeArray[$TypeID]);
    }
}