<?php
/**
 * 我的微课
 * @author demo
 * @date 2017-7-12
 */
namespace Custom\Controller;
use Common\Controller\DefaultController;
class MicroClassController extends DefaultController{

    private $username = ''; //当前用户名

    /**
     * 构造方法
     */
    public function __construct(){
        parent::__construct();
        $this->username = $this->getCookieUserName();
    }

    /**
     * 题库
     */
    public function index(){
        $this->display();
    }

    /**
     * 数据保存
     * @author demo
     */
    public function save(){
        $MID= $_POST['MID'];
        $verifyCode= $_POST['VerifyCode'];
        $act= $_POST['Act'];
        $subjectID= $_POST['SubjectID'];
        $gradeID= $_POST['GradeID'];
        $MName= $_POST['MName'];
        $remark= $_POST['Remark'];
        $klID= $_POST['KlID'];
        $url= $_POST['Url'];

        $userID = $this->getCookieUserID();

        if($act=='edit' && !is_numeric($MID)){
            $this->setError('数据标识异常，请刷新后重试。',1);
        }

        if($act!='add' && $act!='edit'){
            $this->setError('数据标识异常，请刷新后重试。',1);
        }

        if($verifyCode!=md5('0'.$act.$this->username)){
            $this->setError('效验失败，请刷新后重试。',1);
        }
        if(empty($MName)){
            $this->setError('请输入微课名称。',1);
        }
        if(empty($klID)){
            $this->setError('请选择知识点。',1);
        }
        if(empty($url)){
            $this->setError('请上传微课。',1);
        }

        $data=array();
        $data['MName']=$MName;
        $data['UserID']=$userID;
        $data['GradeID']=$gradeID;
        $data['SubjectID']=$subjectID;

        if(is_array($url)) $url=implode('@#@',$url);
        $data['Url']=$url;
        $data['Remark']=$remark;


        //判断数据是否已经添加过
        $microClass=$this->getModel('MicroClass');
        if($act=='edit'){
            $buffer=$microClass->findData('MID','MName="'.$MName.'" and UserID='.$userID.' and MID!='.$MID);
        }else{
            $buffer=$microClass->findData('MID','MName="'.$MName.'" and UserID='.$userID);
        }
        if(!empty($buffer)){
            $this->setError('微课名称重复，请更换。',1);
        }

        if($act=='add'){
            $data['AddTime']=time();
            $buffer=$microClass->insertData($data);
            $MID=$buffer;
        }else{
            $buffer=$microClass->updateData($data,'MID='.$MID);
        }
        if($buffer===false){
            $this->setError('数据保存失败，请重试。',1);
        }

        //写入知识
        $microClassKl=$this->getModel('MicroClassKl');
        $klBuffer=$microClassKl->selectData('*','MID='.$MID);

        if(!is_array($klID)) $klID=array($klID);
        if($klBuffer){
            $data=array();
            $data['MID']=$MID;
            $j=0;
            foreach($klBuffer as $iKlBuffer){
                if($klID[$j]){
                    $data['KlID']=$klID[$j];
                    $microClassKl->updateData($data,'KID='.$iKlBuffer['KID']);
                }else{
                    $microClassKl->deleteData('KID='.$iKlBuffer['KID']);
                }
                $j++;
            }

            $num=count($klID);
            if($j<$num){
                for(;$j<$num;$j++){
                    $data['KlID']=$klID[$j];
                    $microClassKl->insertData($data);
                }
            }
        }else{
            $data=array();
            $data['MID']=$MID;
            foreach($klID as $iKlID){
                $data['KlID']=$iKlID;
                $buffer=$microClassKl->insertData($data);
            }
        }


        $this->setBack('保存成功.');
    }

    /**
     * 添加试题
     * @author demo
     */
    public function add(){
        $act = 'add';
        $verifyCode = md5('0'.$act.$this->username);
        $this->assign('verifyCode', $verifyCode);
        $this->assign('pageName', '添加微课');
        $this->assign('act', $act);
        $this->display();
    }

    /**
     * 上传微课
     * @author demo
     */
    public function fileupload(){
        $info = R('Common/UploadLayer/uploadVideoFile', array('video', 'video',array('maxSize'=>100)));
        $result=array('msg'=>'','url'=>'');
        if(!$info || is_array($info)){
            if(!empty($info[1])) $result['msg']=$info[1];
            else $result['msg'] = '上传失败';
        }else{
            $result['msg'] = '上传成功';
            $result['url'] = $info;
        }
        $result['return']=2;
        $this->setBack($result);
    }

    /**
     * 试题编辑页面
     * @author demo
     */
    public function edit(){
        $mid = $_GET['mid'];
        if(!$mid){
            $this->setError('数据标识有误，',1);
        }

        $microClass = $this->getModel('MicroClass');
        $microClassKl = $this->getModel('MicroClassKl');

        $buffer=$microClass->findData('*','MID='.$mid);
        if(empty($buffer)){
            $this->setError('数据不存在，请刷新后重试。',1);
        }

        $bufferKl=$microClassKl->selectData('*','MID='.$mid);
        $klParentBuffer=SS('knowledgeParent');
        $klBuffer=SS('knowledge');
        $tmpArr=array();
        foreach($bufferKl as $j=>$jBufferKl){
            $tmpArr[$jBufferKl['KlID']]='';
            foreach($klParentBuffer[$jBufferKl['KlID']] as $kKl){
                $tmpArr[$jBufferKl['KlID']].=$kKl['KlName'].' > ';
            }
            $tmpArr[$jBufferKl['KlID']].=$klBuffer[$jBufferKl['KlID']]['KlName'];
        }

        $buffer['KlArr']=$tmpArr;
        $buffer['UrlArr']=explode('@#@',$buffer['Url']);

        $act = 'edit';
        $verifyCode = md5('0'.$act.$this->username);
        $this->assign('pageName', '修改微课');
        $this->assign('verifyCode', $verifyCode);
        $this->assign('act', $act);
        $this->assign('mid',$mid);
        $this->assign('data',json_encode($buffer));
        $this->display('MicroClass/add');
    }

    /**
     * 删除试题
     * @author demo
     */
    public function del(){
        $mid = $_POST['mid'];
        if(!$mid){
            $this->setError('数据标识有误。', 1);
        }
        $microClass = $this->getModel('MicroClass');
        $microClassKl = $this->getModel('MicroClassKl');

        $buffer=$microClass->findData('*','MID='.$mid);
        if(empty($buffer)){
            $this->setBack('success');
        }

        $microClass->deleteData('MID='.$mid);
        $microClassKl->deleteData('MID='.$mid);

        $this->setBack('success');
    }
    /**
     * 获取数据列表
     * @author demo
     */
    public function getMicroList(){
        $userID=$this->getCookieUserID();
        $subjectID= $_POST['subject']; //学科
        $klID = $_POST['knowledge']; //知识点
        $gradeID = $_POST['grade']; //知识点
        $page = $_POST['page']; //页数
        $perPage = $_POST['perpage']; //每页数量

        $where=array();
        if($subjectID) $where['mc.SubjectID']=$subjectID;
        $where['mc.UserID']=$userID;
        if($klID) {
            $where['mck.KlID'] = array('in',$klID.','.$this->getApiCommon('Knowledge/klList')[$klID]);
        }
        if($gradeID) $where['mc.GradeID']=$gradeID;

        $microClass=$this->getModel('MicroClass');
        $count=$microClass->unionSelect('countMicroClass',$where);
        if(!is_numeric($perPage) || $perPage<10) $perPage=20;
        $limit=page($count,$page,$perPage).','.$perPage;
        $buffer=$microClass->unionSelect('pageMicroClass',$where,$limit);

        if(!empty($buffer)){
//            $userIDArr = array();
//            foreach($buffer as $value){
//                $userIDArr[] = $value['UserID'];
//            }
            $gradeBuffer=SS('grade');
            $subjectBuffer=SS('subject');
            $klParentBuffer=SS('knowledgeParent');
            $klBuffer=SS('knowledge');
            foreach($buffer as $i=>$iBuffer){
                if(empty($iBuffer['GradeID'])) $buffer[$i]['GradeName'] ='';
                else $buffer[$i]['GradeName'] =$gradeBuffer[$iBuffer['GradeID']]['GradeName'];
                $buffer[$i]['SubjectName'] =$subjectBuffer[$iBuffer['SubjectID']]['SubjectName'];
                $buffer[$i]['AddTime'] = date('Y-m-d H:i:s',$iBuffer['AddTime']);
                $klArr=explode(',',$iBuffer['KlID']);
                $tmpArr=array();
                foreach($klArr as $j=>$jKlArr){
                    $tmpArr[$j]='';
                    foreach($klParentBuffer[$jKlArr] as $kKl){
                        $tmpArr[$j].=$kKl['KlName'].' >> ';
                    }
                    $tmpArr[$j].=$klBuffer[$jKlArr]['KlName'];
                }
                $buffer[$i]['KlIDArr']=$tmpArr;
                $buffer[$i]['Url']=explode('@#@',$iBuffer['Url']);
                foreach($buffer[$i]['Url'] as $h=>$hBuffer){
                    $buffer[$i]['UrlArr'][]='微课地址'.($h+1);
                }
                unset($buffer[$i]['Url']);
            }
            $tmpStr=array($buffer,$count,$perPage);
            $this->setBack($tmpStr);
        }else{
            $this->setBack('抱歉！暂时没有符合条件的微课，请尝试更换查询条件或者<a style="text-decoration:underline;color:#00a0e9;" href="'.__URL__.'/add">添加微课</a>。');
        }
    }
    /**
     * 获取查询分类数据
     * @author demo
     */
    public function getGradeInit(){
        $output = array ();
        $subjectID = $_POST['id'];
        $buffer = $this->getApiCommon('Subject/subject');
        $output[0] = $buffer[$buffer[$subjectID]['PID']]['SubjectName'].$buffer[$subjectID]['SubjectName']; //学科名称

        //难度
        $param2['style']='grade';
        $param2['subjectID']=$subjectID;
        $param2['return'] = 2;
        $output[1] = $this->getData($param2);

        //知识点数据
        $param3['style']='knowledge';
        $param3['subjectID']=$subjectID;
        $param3['haveLayer']=3;
        $param3['return'] = 2;
        $output[3] = $this->getData($param3); //知识点数据
        $this->setBack($output);
    }

    /**
     * 获取音频地址
     * @author demo
     */
    public function play(){
        $mid=$_GET['mid'];
        $startid=$_GET['startid'];
        $userID = $this->getCookieUserID();

        $microClass=$this->getModel('MicroClass');
        $buffer=$microClass->findData('Url,UserID','MID='.$mid);

        if($userID!=$buffer['UserID']) $this->setError('您没有权限访问。',1);

        $url=explode('@#@',$buffer['Url']);

        if(empty($url[$startid])) $this->setError('您访问的资源不存在。',1);

        $this->assign('url',C('WLN_DOC_HOST').$url[$startid]);
        $this->display();
    }
}