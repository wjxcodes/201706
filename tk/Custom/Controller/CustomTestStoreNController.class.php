<?php
/**
 * 我的题库
 * @author demo
 * @date 2015-1-8
 */
namespace Custom\Controller;
use Common\Controller\DefaultController;
class CustomTestStoreNController extends DefaultController{

    private $responseCode = 0; //ajax或者正常响应
    private $username = ''; //当前用户名

    /**
     * 构造方法
     */
    public function __construct(){
        parent::__construct();
        $this->responseCode = IS_AJAX ? 1 : 0;
        $this->username = $this->getCookieUserName();
    }

    /**
     * 题库
     */
    public function index(){
        $output=array();
        $types = $this->getTypes();
        $this->assign('types', $types);
        $this->assign('pageName', '校本题库试题列表');
        $this->display();
    }

    /**
     * 数据保存
     * @author demo
     */
    public function save(){
        $verifyCode = $_POST['verifyCode'];
        $testID= $_POST['TestID']; //试题id
        //验证状态
        if($verifyCode != md5($testID.$_POST['act'].$this->username)){
            $this->setError('30113',$this->responseCode);
        }
        $_POST['UserID'] = $this->getCookieUserID();
        $result = $this->getModel('CustomTest')->saveData(
            $_POST,
            $this->getModel('CustomTestAttr'),
            $this->getModel('CustomTestKnowledge'),
            $this->getModel('CustomTestChapter'),
            $this->getModel('CustomTestJudge')
        );
        if(!$result){
            $this->setError('30307',$this->responseCode);
        }
        if(empty($testID))
            $this->getModel('MissionHallRecords')->finishTask($this->getCookieUserID());//任务完成
        //$this->setBack('保存成功，试题正在处理，稍后将会显示！'); 
        //初次完成单题上传用户经验累加
        $expResult=$this->getModel('UserExp')->addUserExpAll($this->username,'firstUpTest');
        if(empty($expResult)){
            //对不是初次的内容积累
            $this->getModel('UserExp')->addUserExpAll($this->username,'upContent');
        }
        $this->setBack('保存成功，试题正在处理，稍后将会显示！'); 
    }

    /**
     * 添加试题
     * @author demo
     */
    public function add(){
        //兼容原创模板试题添加
        $userOriginality = stripslashes(Cookie('user_ORIGINALITY'));
        if($userOriginality){
            $userOriginality = formatString('object2Array',json_decode($userOriginality));
            $userOriginality['diff'] = C('WLN_TEST_DIFF')[$userOriginality['diff']][3];
        }else{
            $userOriginality = ['ttID'=>false];
        }
        $this->assign('originality', json_encode($userOriginality));
        $act = 'add';
        $verifyCode = md5('0'.$act.$this->username);
        $this->assign('types', $this->getTypes());
        $this->assign('pageName', '添加试题');
        $this->assign('verifyCode', $verifyCode);
        $this->assign('act', $act);
        $this->assign('host',C('WLN_DOC_HOST'));
        $this->assign('testid',0);
        $this->display();
    }
    
    /**
     * 编辑的验证，验证成功相应一个验证码
     * @author demo
     */
    public function editVerify(){
        $testID = $_GET['testid'];
        if(!$testID){
            $this->setError('30301',$this->responseCode);
        }
        $customTestAttr = $this->getModel('CustomTestAttr');
        $response = $customTestAttr->whetherEdit($testID);
        if($response !== true){
            $this->setError($response,$this->responseCode); //该试题暂时不能编辑
        }
        $this->setBack(md5('edit'.$this->username.$testID));
    }

    /**
     * 试题编辑页面
     * @author demo
     */
    public function edit(){
        $testID = $_GET['testid'];
        $verify = $_GET['verify'];
        if(!$testID){
            $this->setError('30301',$this->responseCode);
        }
        if($verify != md5('edit'.$this->username.$testID)){
            $this->setError('30113', $this->responseCode);
        }
        $data = $this->getModel('CustomTest')->getDataById($testID);
        $data['chapter'] = $this->getModel('CustomTestChapter')->getParentRoute($data['chapter']);
        $data['knowledge'] = $this->getModel('CustomTestKnowledge')->getParentRoute($data['knowledge']);
        $data['basic'] = stripslashes_deep($data['basic']);
        $data['basic']['Test'] =  formatString('IPReturn',$data['basic']['Test']);
        $data['basic']['Analytic'] = formatString('IPReturn',$data['basic']['Analytic']);
        $data['basic']['Answer'] = formatString('IPReturn',$data['basic']['Answer']);
        $act = 'edit';
        $this->assign('pageName', '修改试题');
        $this->assign('verifyCode', md5($testID.$act.$this->username));
        $this->assign('act', $act);
        $this->assign('testid',$testID);
        $this->assign('host',C('WLN_DOC_HOST'));
        $this->assign('data',json_encode($data));
        $this->display('CustomTestStoreN/add');
    }

    /**
     * 删除试题
     * @author demo
     */
    public function del(){
        $testID = $_POST['testid'];
        if(!$testID){
            $this->setError('30301', $this->responseCode);
        }
        $customTestAttr = $this->getModel('CustomTestAttr');
        if(!$customTestAttr->whetherDelete($testID)){
            $this->setError('1X5001',$this->responseCode); //该试题暂时不能删除
        }
        $result = $this->getModel('CustomTest')->deleteData($testID);
        if($result === false){
            $this->setError('30302', $this->responseCode);
        }
        $this->setBack('success');
    }

    /**
     * ajax获取试题 所有页面试题列表
     */
    public function getTestList(){
        $width = $_POST['width'];
        $subjectID= $_POST['subject']; //学科
        $diff = $_POST['diff']; //难度
        $typesID = $_POST['types']; //题型
        $klID = $_POST['knowledge']; //知识点
        $chapterID = $_POST['chapter']; //章节
        $specialID = $_POST['pid']; //专题
        $abilityID = $_POST['abi'];//能力选项
        $key = $_POST['key']; //关键字
        $time = $_POST['time']; //时间
        $order = $_POST['order']; //排序
        $page = $_POST['page']; //页数
        $perPage = $_POST['perpage']; //每页数量
        $ifInter = $_POST['ifInter']; //截取题文
        $interLen = $_POST['interLen']; //截取题文
        $where=array();
        if($subjectID) $where['SubjectID']=$subjectID;
        if($diff){
            //难度
            $param['style']='diff';
            $param['return'] = 2;
            $diff = $this->getData($param)[$diff-1];
            $where['Diff'] = explode('-', $diff['DiffArea']);
        }
        if($typesID) $where['TypesID']=$typesID;
        if($klID) {
            $where['KlID'] = $klID.','.$this->getApiCommon('Knowledge/klList')[$klID];
        }
        if($chapterID && empty($abilityID)) $where['ChapterID']=$chapterID;
        if($specialID) $where['SpecialID']=$subjectID;
        if($key) $where['key']=$key;
        if($width) $where['width']=$width;
        $where['page'] = ($page ? $page : 1); //查库增加参数
        $where['UserID'] = $this->getCookieUserID();
        $lasttime = 0;
        if ($time) {
            switch ($time) {
                case "onemonth" :
                    $lastTime = time() - 30 * 24 * 3600;
                    break;
                case "threemonth" :
                    $lastTime = time() - 3 * 30 * 24 * 3600;
                    break;
                case "sixmonth" :
                    $lastTime = time() - 6 * 30 * 24 * 3600;
                    break;
                case "oneyear" :
                    $lastTime = time() - 12 * 30 * 24 * 3600;
                    break;
                case "oneyearold" :
                    $lastTime = -time() + 12 * 30 * 24 * 3600;
                    break;
            }
        }
        if($lastTime) $where['LastTime']=$lastTime;
        if(empty($abilityID)) $where['Duplicate']=0;
        if($order){
            if($order == 'def'){
                $order = 'addtime DESC';
            }
            if(strpos($order, 'saveTime') !== false){
                if(strpos($order, 'Desc')){
                    $order = 'addTime DESC';
                }else
                    $order = 'AddTime ASC';
            }
            if(strpos($order, 'diff') !== false){
                if(strpos($order, 'Desc')){
                    $order = 'diff DESC';
                }else
                    $order = 'diff ASC';
            }
            $order .= ', @id DESC';
        }
        $order = array($order);

        //是否选中能力
        if($abilityID){
            $where=array();
            if($subjectID) $where['SubjectID']=$subjectID;
            $seach='AbilitID='.$abilityID.' and CaptID='.$chapterID;
            $docIDArr=$this->getModel('DocAbiCapt')->selectData(
                'DocAbiCapt',
                'DocID',
                $seach
            );
            if($docIDArr){
                $IDArr=array();
                foreach($docIDArr as $i=>$iDocIDArr){
                    $IDArr[]=$iDocIDArr['DocID'];
                }
                $where['DocID']=implode(',',$IDArr);
                $order=array('@id ASC');
            }else{
                $data[0]=array('4');
                $data[1]=0;
                $data[2]='20';
                $this->setBack($data);
            }
        }

        $field=array('testid','typesid','subjectname','typesname','testnum','test','diff','docname','firstloadtime','status');
        $page=array('page'=>$page,'perpage'=>$perPage);
        if($key) $page['limit']=1000;
        $tmpStr=$this->getTest($field,$where,$order,$page);
        if($ifInter){
            foreach($tmpStr[0] as $i=>$iTmpStr){
                $tmpStr[0][$i]['test']=mb_substr(preg_replace('/<[^>]*>/i','',$iTmpStr['test']),0,$interLen,'UTF-8').'...';
            }
        }
        if(!empty($tmpStr[0])){
            $ids = array();
            foreach($tmpStr[0] as $value){
                $ids[] = $value['testid'];
            }
            $result = $this->getModel('CustomTestTaskStatus')->getRecordByTestIds($ids,'TestID, Status, IfLock, ErrorMsg');
            foreach($result as $key=>$value){
                unset($result[$key]);
                $result[$value['TestID']] = $value;
            }
            foreach($tmpStr[0] as $key=>$value){
                $id = str_replace(\Test\Model\TestQueryModel::DIVISION, '', $value['testid']);
                $tmpStr[0][$key]['schedule'] = $result[$id]['Status'];
                $tmpStr[0][$key]['iflock'] = $result[$id]['IfLock'];
                $tmpStr[0][$key]['ErrorMsg'] = $result[$id]['ErrorMsg'];
            }
            $this->setBack($tmpStr);
        }else{
            $this->setBack('抱歉！暂时没有符合条件的试卷，请尝试更换查询条件或者<a style="text-decoration:underline;color:#00a0e9;" href="'.__URL__.'/add">添加试题</a>。');
        }
    }

    /**
     * ajax获取单条试题信息，试题列表显示试题内容
     * @author demo
     */
    public function getOneTestById() {
        $output=array();
        $id = $_POST['id'];
        if(empty($id)) $this->setError('30301',1);
        /*
            $where['UserName'] = $this->username;
        $where['TestID'] = $id;
        $result = $this->getModel('CustomTest')->getOtherData($where);
        $kllist = $this->getModel('CustomTestKnowledge')->getParentRouteByTest($id);
        $temp = array();
        foreach($kllist as $value){
            foreach($value as $k=>$v){
                if($k == 'KlName'){
                    $temp[] = str_replace('>>', '&nbsp;>>&nbsp;', ltrim($v, '>>'));
                } 
            }
        }
        $result['kllist'] = implode('<br>', $temp);
        $data[0] = 'success';
        $data[1] = $result;
            $this->setBack($data);
        */
        //索引
        $width = $_POST['width'];
        /*
        $where=array('TestID'=>$id);
        $field=array('kllist','analytic','answer','remark');
        $page=array('page'=>1,'perpage'=>1);
        */
        // $query = \Test\Model\TestQueryModel::getInstance();
        // $query->setParams(array(
        //     'where' => array('width'=>$width,'UserID'=>$this->getCookieUserID()),
        //     'field' => array('kllist','analytic','answer','remark'),
        //     'page' => array('page'=>1,'perpage'=>1)
        // ), $id);
        // $tmpStr = $query->getResult(true);
        $tmpStr = $this->getModel('CustomTest')->getIndex(
            array('kllist','analytic','answer','remark'),
            array('width'=>$width,'UserID'=>$this->getCookieUserID(), 'TestID'=>ltrim($id,'c')),
            array(),
            array('page'=>1,'perpage'=>1)
        );
        if($tmpStr ===  false){
            $this->setError('30504', (IS_AJAX ? 1 : 0));
        }
        if($tmpStr){
            $output[0]='success';
            $output[1]=$tmpStr[0][0];
            $this->setBack($output);
        }
    }

    protected function getTest($field,$where,$order,$page,$reload=0){
         /*
             unset($where['Duplicate']);
             $where['UserName'] = $this->username;
             $result = $this->getModel('CustomTest')->getDataList($where, $order, array('verify'=>'del', 'limitPage'=>20));
             if(empty($result['data'])){
                 return false;
             }
             return array($result['data'], $result['total'], $where['page'], $result['recordTotal']);
        */
        // 索引
        $tmpStr=$this->getModel('CustomTest')->getIndex($field,$where,$order,$page);
        if($reload) $tmpStr[0]=R('Common/TestLayer/reloadTestArr',array($tmpStr[0]));
        return $tmpStr;
    }

    /**
     * ajax获取列表页相关属性信息
     * @author demo
     */
    public function getZsdInit(){

        $output = array ();
        $subjectID = $_POST['id'];
        $buffer = $this->getApiCommon('Subject/subject');
        $output[0] = $buffer[$buffer[$subjectID]['PID']]['SubjectName'].$buffer[$subjectID]['SubjectName']; //学科名称
        //题型

        $param1['style']='types';
        $param1['subjectID'] = $subjectID;
        $param1['return'] = 2;
        $output[1]= $this->getData($param1);

        //难度
        $param2['style']='diff';
        $param2['return'] = 2;
        $output[2] = $this->getData($param2);
        //知识点数据
        $param3['style']='knowledge';
        $param3['subjectID']=$subjectID;
        $param3['haveLayer']=3;
        $param3['return'] = 2;
        $output[3] = $this->getData($param3); //知识点数据
        //$output[4] = $this->getDocAttr(); //试卷属性
        //$output[4] = $this->getArea(); //试卷省份
        $this->setBack($output);
    }

    /**
     * 描述：上传图片实时显示页面的测试，之后应该写在校本题库上传试题页面中
     * @author demo
     */
    public function photograph(){
        $userOriginality = stripslashes(Cookie('user_ORIGINALITY'));
        if($userOriginality){
            $userOriginality = formatString('object2Array',json_decode($userOriginality));
            $userOriginality['diff'] = C('WLN_TEST_DIFF')[$userOriginality['diff']][3];
        }else{
            //非原创
            $userOriginality = ['ttID'=>false];
        }
        $this->assign('originality', json_encode($userOriginality));
        $key = md5(C('DOC_HOST_KEY') . $this->username . date('Y.m.d', time()));
        $this->assign([
            'username'=>$this->username,
            'key' => $key
        ]);
        $this->assign('types', $this->getTypes());
        $this->display();
    }

    /**
     * 描述：获取二维码图片
     * @author demo
     */
    public function qrCode(){
        $no = $_REQUEST['n'];
        $username = $_REQUEST['u'];
        import('Common.Tool.QrCode.QrCodeImageUploadModel');
        (new \Tool\QrCode\QrCodeImageUploadModel())->qrCode($username,$no);
    }

    /**
     * 描述：ajax poll轮询地址
     * @author demo
     */
    public function webImagePoll(){
        $no = $_REQUEST['n'];
        $username = $this->username;//这里必须是从web网站cookie得到username
        $filename = 'img-'.$username.'-'.$no;
        import('Common.Tool.QrCode.QrCodeImageUploadModel');
        $fileUrl = (new \Tool\QrCode\QrCodeImageUploadModel())->webImagePoll($filename);
        if(!$fileUrl===false){
            $imgSize = explode('"', getimagesize(realpath('./').$fileUrl)[3]);
            $imgInfo=array(
                'picPath' => $fileUrl,
                'width'   => $imgSize[1],
                'height'  =>$imgSize[3]
            );
            $this->setBack($imgInfo);
        }else{
            $this->setError('1X5002',1);
        }
    }

    /**
     * 描述：
     * @author demo
     */
    public function mobile(){
        $username = $_REQUEST['u'];
        $no = $_REQUEST['n'];
        if(IS_AJAX){
            $base64 = $_POST['imageBase64'];
            if(!$base64||!$username||!$no){
                $this->setError('1X5010',1);
            }
            $filename = 'img-'.$username.'-'.$no;
            if($this->token($type='validate',$username)==false){
                $this->setError('1X5003',1);//上传验证失败或者过期，请重新扫描二维码！
            }
            if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)){
                $suffix = $result[2];
                $filename = DIRECTORY_SEPARATOR.$filename.'.'.$suffix;
                $subMenu = date('Y',time()).'/'.date('md',time());
                if (file_put_contents(realpath('./Uploads/customTest/'.$subMenu).$filename, base64_decode(str_replace($result[1], '', $base64)))){
                    $this->setBack('success');
                }else{
                    $this->setError('1X5010',1);//上传失败，请重试
                }
            }else{
                $this->setError('1X5011',1);//图片格式错误，请重试
            }
        }else{
            $this->assign([
                'username'=>$username,
                'token'=>$this->token('create',$username),
            ]);
            $this->display();
        }
    }


    /**
     * 校本题库文档列表页
     * @author demo 
     */
    public function docList(){
        $this->assign('pageName', '我的文档');
        $status = (int)$_REQUEST['status'];
        $page = (int)$_REQUEST['p'];
        $params = array();
        if(0 == $page){
            $page = 1;
        }
        $title = '';
        if($_REQUEST['title']){
            $_REQUEST['title'] = formatString('decodeUrl',$_REQUEST['title']);
            $title = str_replace('/', '', $_REQUEST['title']);
        }
        $prepage = 20;
        $params['p'] = $page;
        $params['userid'] = $this->getCookieUserID();
        $params['status'] = $status;
        $params['prepage'] = $prepage;
        $params['title'] = $title;
        $list = $this->getModel('CustomDocUpload')->getList($params);
        unset($params['p'], $params['prepage'], $params['userid']);
        $pagtion = $this->fillPagtion($params, $list[0], $page, $prepage, '/Custom/CustomTestStoreNN/docList');
        $subjects = $this->getApiCommon('Subject/subject');
        foreach($subjects as $key=>$value){
            $subjects[$key] = $value['SubjectName'];
        }
        $this->assign('subjects', $subjects);
        $this->assign('subjectsJSON', json_encode($subjects));
        $this->assign('pageCount', ceil($list[0] / $prepage));
        $this->assign('host', C('WLN_DOC_HOST'));
        $this->assign('page', $page);
        $this->assign('prepage', $prepage);
        $this->assign('list', $list[1]);
        $this->assign('pagtion', $pagtion['pages']);
        $this->assign('count', $list[0]);
        $this->assign('status', $status);
        $this->display();
    }

    /**
     * 校本题库文档删除
     * @author demo 
     */
    public function docDel(){
        $id = (int)$_POST['id'];
        if(0 == $id){
            $this->setError('30301', 1);
        }
        $cdu = $this->getModel('CustomDocUpload');
        if($cdu->isCanOperate($id)){
            $this->setError('1X5004');
        }
        $result = $cdu->delete($id);
        if($result === false){
            $this->setError('30302', 1);
        }
        $this->userLog('校本题库文档删除', "删除编号【{$id}】的文档", $this->username);
        $this->setBack('success');
    }

    /**
     * 校本题库文档上传保存
     * @author demo 
     */
    public function docUpload(){
        $id = (int)$_POST['id'];
        $cdu = $this->getModel('CustomDocUpload');
        if($id > 0){
            if($cdu->isCanOperate($id)){
                $info = json_encode(array('不能操作正在审核或者已经审核完成的文档'));
                exit("<script>parent.uploadCb({$info});</script>");
            }
        }
        C('WLN_DOC_OPEN_CHECK',0);
        $info = R('Common/UploadLayer/uploadWord', array('docfile', 'work'));
        if(!$info || is_array($info)){
            $info = '上传失败';
        }else{
            $title = strstr($_FILES['doc']['name'], '.', true);
            $isTpl = -1;
            $data = array();
            //如果id大于0，则为修改操作
            if($id > 0){
                $data = $cdu->findData('*', 'DUID='.$id);
                if(empty($data)){
                    $cdu->appendDelFile($info);
                    $info = json_encode(array('编号【'.$id.'】的文档不存在！'));
                    exit("<script>parent.uploadCb({$info});</script>");
                }
                $title = $data['Title']; //修改操作不改变文档名称
                $cdu->appendDelFile($data['Path']);
            }else{
                //新增操作
                $isTpl = Cookie('user_ORIGINALITY')?1:0;
            }
            $saveData = [
                'Path' => $info,
                'Title' => $title,
                'UserID' => $this->getCookieUserID(),
            ];
            if($isTpl!==-1){
                $saveData['IsTpl'] = $isTpl;
            }
            $result = $cdu->save($saveData, $id);
            if($result === false){
                $info = '保存失败！';
            }else{
                $log = "修改编号【{$id}】的文档";
                if(0 == $id){
                    $this->getModel('MissionHallRecords')->finishTask($this->getCookieUserID());//任务完成
                    $log = "添加编号【{$result}】的文档";
                    $id = $result;
                    //初次完成校本题库文档上传上传用户经验累加
                    $userName=$this->username;
                    $this->getModel('UserExp')->addUserExpAll($userName,'upContent');
                }
                $info = $cdu->findData('*', 'DUID='.$id);
                unset($data);
                $info['AddTime'] = date('Y-m-d H:i:s', $info['AddTime']);
                $info['ModifiedTime'] = date('Y-m-d H:i:s', $info['ModifiedTime']);
            }
            $this->userLog('校本题库文档保存', $log, $this->username);
        }
        $info = json_encode(array($info));
        exit("<script>parent.uploadCb({$info});</script>");
    }

    /**
     * 校本题库文档名修改
     * @author demo 
     */
    public function docNameUpdate(){
        $id = (int)$_POST['id'];
        if(0 == $id){
            $this->setError('30301', 1);
        }
        $cdu = $this->getModel('CustomDocUpload');
        if($cdu->isCanOperate($id)){
            $this->setError('1X5004', 1);
        }
        $result = $cdu->save(array(
            'Title' => $_POST['docname']
        ), $id);
        if($result === false){
            $this->setError('30311', 1);
        }
        $this->userLog('校本题库文档名修改', "修改编号【{$id}】的文档名称", $this->username);
        $this->setBack('success');
    }

    /**
     * 提交后台管理员审核
     * @author demo 
     */
    public function submitCheck(){
        $id = (int)$_POST['id'];
        if(0 == $id){
            $this->setError('30301', 1);
        }
        $result = $this->getModel('CustomDocUpload')->setStatus($id, 1);
        if($result === false){
            $this->setError('30311', 1);
        }
        $this->userLog('校本题库文档审核提交', "提交编号【{$id}】的文档进行审核", $this->username);
        $this->setBack('success');
    }

    /**
     * 我的文档word下载
     * @author demo 
     */
    public function wordDownload(){
        $id = (int)$_GET['id'];
        if(0 == $id){
            $this->setError('30301', 1);
        }
        $result = $this->getModel('CustomDocUpload')->findData('Title, Path', 'DUID='.$id);
        if(empty($result['Path']) || strstr($result['Path'],'error')){
            $this->setError('30706', 1); //该文件不存在
        }

        $url=C('WLN_DOC_HOST').R('Common/UploadLayer/getDocServerUrl',array($result['Path'],'down','docfile',$result['Title']));
        header('location:'.$url);
    }

    /**
     * 显示文档试题
     * @author demo 2015-12-10 
     */
    public function showDocTest(){
        $id = (int)$_GET['id'];
        if(0 == $id){
            $this->setError('30301', 1);
        }
        $model = $this->getModel('CustomTestDoc');
        $result = $model->sequentialQuery($id, 1);
        if(empty($result)){
            $this->setError('1X5006', 1);
        }
        $testid = array();
        foreach($result as $value){
            $testid[] = $value['TestID'];
        }
        $testid = implode(',', $testid);
        $testid = array('300298','300299','300305','300300','300302','300301','300303','300304');
        $result = $this->getModel('CustomTestDoc')->getTestByIds($testid);
        $this->setBack($result);
    }

    /**
     * 描述：生成或者验证token
     * @param string $type create|validate 生成或者验证token
     * @param string $username
     * @return bool|string
     * @author demo
     */
    private function token($type='create',$username){
        $tokenHour = md5($username.Date('YmdH',time()).C('MOBILE_UPLOAD_KEY'));
        if($type=='create'){
            return $tokenHour;
        }elseif($type=='validate'){
            $tokenNextHour =  md5($username.Date('YmdH',time()+3600).C('MOBILE_UPLOAD_KEY'));
            $userToken = $_REQUEST['token'];
            if($userToken==$tokenHour||$userToken==$tokenNextHour){
                return true;
            }else{
                return false;
            }
        }
    }

    /**
     * 校本题库拍照上传裁切保存
     * @author demo
     */
    public  function imgCutSave(){
        $host = C('WLN_DOC_HOST');
        if ($host) {
            $picPath = explode('?',$_POST['picPath']);
            $picPath = $picPath[0];
            $width = $_POST['width'];
            $height = $_POST['height'];
            $x1 = $_POST['x1'];
            $y1 = $_POST['y1'];
            if (!isset($picPath) || !isset($width) || !isset($height) || !isset($x1) || !isset($y1)) {
                $this->setError('30726', 1);
            }
            $param[0]=array(
                'picPath'=>$picPath,
                'width'=>$width,
                'height'=>$height,
                'x1'=>$x1,
                'y1'=>$y1
            );
            $result=useToolFunction('ImgCut','imgProcess',$param);
            if($result!=''){
                $this->setError($result,1);
            }
            $imgSize = explode('"', getimagesize(realpath('./').$picPath)[3]);

            $imgInfo = array(
                'picPath' => $picPath,
                'width' => $imgSize[1],
                'height' => $imgSize[3],
            );
            $this->setBack($imgInfo);
        }else{
            $this->setError('1X5007', 1);
        }
    }

    /**
     * 将web服务器文件存储到文件服务器
     * @param $picPathArr array 需要保存的文件
     * @return mixed 错误返回错误码，正确返回文件地址数组
     * @author demo
     */
    public function upFileToServer($picPathArr){
        $host = C('WLN_DOC_HOST');
        if (is_array($picPathArr)) {
            $data = array();
            foreach ($picPathArr as $iPicPathArr) {
                $picPath = explode('?',$iPicPathArr['picPath']);
                $picPath = $picPath[0];
                $filePath = realpath('./') . $picPath;
                //上传文件到服务器
                $fileUrl = R('Common/UploadLayer/upFileToServer', array($filePath, 'bbs', 'customTest'));
                //bbs接口上传到userAnswer目录
                if (strstr($fileUrl, 'error')) {
                    return '1X5007';
                }
                @unlink($filePath); //删除本地图片
                $data[] = $host . $fileUrl;
            }
            return $data;
        }else{
            return false;
        }
    }

    /**
     * 插件上传图片试题
     * @author demo
     */
    public function avatarUpload() {
        if ($_GET['key'] != md5(C('DOC_HOST_KEY') . $_GET['username'] . date('Y.m.d', time()))) {
            $data['error']='数据验证失败。';
            $data['return']=2;
            $this->setBack($data);
        }
        $path = R('Common/UploadLayer/uploadImgOnly',array('customTest',2048000));

        if(!strstr($path,'Uploads')){
            $data['error']='图片上传失败。';
            $data['return']=2;
            $this->setBack($data);
        }
        $imgSize = explode('"', getimagesize(realpath('./').$path)[3]);

        $result = array(
            'picPath' => $path,
            'width' => $imgSize[1],
            'height' => $imgSize[3],
            'return'=>2
        );

        $this->setBack($result);

    }

    /**
     * 保存图片试题
     * @author demo 
     */
    public function imgTestSave(){
        //接收数据
        $data = $_POST['data'];
        $attr = $_POST['attr'];
        //将图片存储到文件服务器
        $testImgName = $this->upFileToServer($data);
        //图片试题内容
        $imgTest = '<p>';
        //循环组合图片试题
        if(is_array($testImgName)) {
            foreach ($testImgName as $i => $iTestImgName) {
                //替换服务器地址为标签
                $imgTestName = formatString('IPReplace', $iTestImgName);
                //对图片地址增加IMG标签
                $imgTestName = '<img src="' . $imgTestName . '" alt="智慧云题库组卷系统" />';
                //组合图片试题内容
                $imgTest .= $imgTestName . $data[$i]['testText'];
            }
            $imgTest.='</p>';
        }else if(is_string($testImgName)){
            $this->setError($testImgName,1);
        }else if($testImgName==false){
            $this->setError('30312',1);
        }
        $testData['Test'] = $imgTest;//保存到custom_test表
        $testAttrData['UserID'] = $this->getCookieUserID();//保存到custom_test_attr表
        $testAttrData['SubjectID'] = cookie('SubjectId');
        if($attr['GradeID']){
            $testAttrData['GradeID'] = $attr['GradeID'];//年级
        }
        if($attr['TypesID']){
            $testAttrData['TypesID'] = $attr['TypesID'];//题型
        }
        if($attr['Diff']){
            $testAttrData['Diff'] = $attr['Diff'];//难度
        }
        if($attr['Source']){
            $testData['Source'] = $attr['Source'];//来源
        }
        if($attr['KlID']){
            $testAttrData['KlID'] = $attr['KlID'];//知识点
        }
        if($attr['ChapterID']){
            $testAttrData['ChapterID'] = $attr['ChapterID'];//章节
        }
        if($attr['Remark']){
            $testData['Remark'] = $attr['Remark'];//备注
        }
        $userOriginality = stripslashes(Cookie('user_ORIGINALITY'));
        $ttID = 0;
        if($userOriginality) {
            $userOriginality = formatString('object2Array', json_decode($userOriginality));
            $ttID = $userOriginality['ttID'];
        }
        $result = $this->getModel('CustomTest')->saveImgTest($testData,$testAttrData,$ttID);
        if($result){
            $this->getModel('MissionHallRecords')->finishTask($this->getCookieUserID());//任务完成
            //$this->setBack($result);
            //初次完成拍题上传用户经验累加
            $expResult=$this->getModel('UserExp')->addUserExpAll($this->username,'firstUpPic');
            if(empty($expResult)){
                //对不是初次的内容经验积累
                $this->getModel('UserExp')->addUserExpAll($this->username,'upContent');
            }
            $this->setBack('保存成功，试题正在处理，校本试题中稍后将会显示！');
        }else{
            $this->setError('30307',1);
        }
    }

    /**
     * 添加校本试题导航
     * @author demo 
     */
    public function customNav(){
        $this->assign('pageName', '添加试题');
        $this->display();
    }

    /**
     * 描述：校本试题点击投稿按钮获取最近一次原创题协作的双向细目表
     * @author demo
     */
    public function originalityTemplate() {
        //获取学科缓存数据
        $subjectID = cookie('SubjectId');
        //查询该时间段内的指定期数
        $originalityStageModel = $this->getModel('OriginalityStage');
        $stage = $originalityStageModel->getCurrentStage();
        if(!$stage){
            $this->setError('1X5013',1);
        }
        $stageName = $stage['Title'];
        if($stage['EndTime']<time()){
            $this->setError('1X5016',$flag=1,$url='',$replace=$stageName);
        }
        //查询学科考试试卷模板数据
        $template = $originalityStageModel->selectOriginality(
            'OriginalityTemplate t',
            'TID,DocType',
            't.SID = ' . $stage['SID'] . ' and t.SubjectID = ' . $subjectID
        )[0];
        if(!$template){
            $this->setError('1X5014',$flag=1);
        }
        //获取试卷类型
        $docTypeName = SS('examType')[$template['DocType']];
        //获取试卷模版中单个试题模版信息
        $testTemplateDb = $originalityStageModel->selectOriginality(
            'OriginalityTemplateTest tt',
            'TTID,TID,TestNum,TypesID,IfChoose,Diff,Score,Type,LimitNum',
            'tt.TID = ' . $template['TID']
        );
        if(!$testTemplateDb){
            $this->setError('1X5015',$flag=1,$url='',$replace=$stageName);
        }
        $ttIDs = array_reduce($testTemplateDb,function($r,$k){
            $r[] = $k['TTID'];
            return $r;
        });
        $ttKlData = $originalityStageModel->selectOriginality('OriginalityTestKnowledge', 'TTID,KlID', ['TTID'=>['in',$ttIDs]]);
        $klCache = $this->getApiCommon('Knowledge/knowledge');
        $knowledge = [];//知识点数据
        foreach($ttKlData as $k){
            $knowledge[$k['TTID']]['klID'][] = $k['KlID'];
            $knowledge[$k['TTID']]['klName'][] = $klCache[$k['KlID']]['KlName'];
        }
        $user = [];//用户档案数据
        $ttUserData = $originalityStageModel->selectOriginality('OriginalityRelateTest rt', 'TTID, UserID', ['TTID'=>['in',$ttIDs]], 'UserID ASC');
        foreach($ttUserData as $k){
            $user[$k['TTID']][] = $k['UserID'];
        }
        $diffConfig = C('WLN_TEST_DIFF');
        $typeCache = $this->getApiCommon('Types/types');
        $testNoStart = 0;
        $testTemplateData = [];//试题模版列表数据
        foreach($testTemplateDb as $k){
            $ttID = $k['TTID'];
            $testTemplateData[$ttID] = [
                'ttID'=>$ttID,
                'tID'=>$k['TID'],
                'testNum'=>$k['TestNum'],
                'typeID'=>$k['TypesID'],
                'ifChoose'=>$k['IfChoose'],
                'diff'=>$k['Diff'],
                'score'=>$k['Score'],
                'type'=>$k['Type'],
                'limitNum'=>$k['LimitNum'],
            ];
            $testTemplateData[$ttID]['knowledge'] = [
                'klID'=>implode('<br />',$knowledge[$k['TTID']]['klID']),
                'klName'=>implode(',',$knowledge[$k['TTID']]['klName']),
            ];
            $testTemplateData[$ttID]['rightPercent'] = $diffConfig[$k['Diff']][3].'-'.$diffConfig[$k['Diff']][4];//难度 正答率
            $testTemplateData[$ttID]['typeName'] = $typeCache[$k['TypesID']]['TypesName'];
            //用户投稿情况
            $testTemplateData[$ttID]['userNum'] = [
                'have'=>count(array_unique($user[$ttID])),
                'leave'=>$k['LimitNum']-count(array_unique($user[$ttID])),
            ];
            //题号
            $testNoStart += $k['TestNum'];
            $testTemplateData[$ttID]['testNo'] = $k['TestNum']==1?$testNoStart:($testNoStart.'~'.($testNoStart+$k['TestNum']));
            //分数
            $score = '';
            $scoreArray = explode(',', $k['Score']);
            $scoreArrayUnique = array_unique($scoreArray);
            if(count($scoreArray) == count($scoreArrayUnique)){//小题分值一样
                $score .= '每小题'.$scoreArray[0].'分<br />共'.array_sum($scoreArray).'分';
            }else{//小题分值不一样
                foreach ($scoreArray as $j=>$m){
                    $score .= '第'.($j+1).'道小题'.$m.'分<br />';
                }
                $score .= '共'.count($scoreArray).'道小题，共'.array_sum($scoreArray).'分';
            }
            $testTemplateData[$ttID]['score'] = $score;
        }
        $result = [
            'stageName' => $stageName,
            'docTypeName' => $docTypeName,
            'testTemplate' => $testTemplateData,
        ];
        $this->setBack($result);
    }

    /**
     * 描述：ajax试题投稿操作
     * @author demo
     */
    public function setTestOriginality() {
        $ttID = $_POST['ttID'];
        $testID = $_POST['testID'];
        $testID = ltrim($testID, \Test\Model\TestQueryModel::DIVISION);
        $userID = $this->getCookieUserID();
        if(!$ttID||!$testID||!$userID){
            $this->setError('1X5008',1);
        }
        $customTestModel = $this->getModel('CustomTest');
        //检查用户本试题是否已经投稿
        if($customTestModel->checkIfSetOriginality($userID,$ttID,$testID)){
            $this->setError('1X5009',1);
        }
        $result = $customTestModel->setTestToOriginality($userID,$ttID,$testID);
        if($result){
            //@todo 请杜旺看下这里是否需要加下面代码
            //$this->getModel('MissionHallRecords')->finishTask($this->getCookieUserID());//任务完成
            $this->setBack('投稿成功！');
        }else{
            $this->setError('1X5008',1);
        }
    }

    /**
     * 返回题型
     * @author demo 
     */
    private function getTypes(){
        $buffer = $this->getApiCommon('Types/typesSubject');
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                foreach($iBuffer as $j=>$jBuffer){
                    $output[$i][$j]['TypesID']=$jBuffer['TypesID'];
                    $output[$i][$j]['TypesName']=$jBuffer['TypesName'];
                    $output[$i][$j]['SubjectID']=$jBuffer['SubjectID'];
                    $output[$i][$j]['TypesScore']=$jBuffer['TypesScore'];
                    $output[$i][$j]['DScore']=$jBuffer['DScore'];
                    $output[$i][$j]['Volume']=$jBuffer['Volume'];
                    $output[$i][$j]['IfDo']=$jBuffer['IfDo'];
                    $output[$i][$j]['Num']=$jBuffer['Num'];
                }
            }
        }
        return json_encode($output, JSON_UNESCAPED_UNICODE);
    }
}