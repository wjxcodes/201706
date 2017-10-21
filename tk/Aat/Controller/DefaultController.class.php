<?php
/**
 * @author demo    
 * @date 2014年5月10日
 */
/**
 * 首页类
 */
namespace Aat\Controller;
class DefaultController extends BaseController
{

    public function _initialize() {
    }

    /**
     * 首页显示
     * @author demo
     */
    public function index() {
        //重新调整的url地址，该地址需要url编码
        $this->assign('jumpUrl', $_GET['url']);
        $this->assign('pageName','首页');
        $this->display();
    }

    /**
     * 微店
     * @author demo
     */
    public function weidian() {
        header('location:https://weidian.com/diyPage/index.php?id=144505');
    }

    /**
     * 退出登录 Ajax调用
     * @author demo
     */
    public function logout() {
        $userName = $this->getUserName();
        if($userName){
            $this->userLog('用户退出', '用户【' . $userName . '】退出提分系统');
        }
        $this->setUserCode(null);
        //因为这里没有清楚用户名的cookie，所以不需要重置_csrf
        $this->setBack('您已经安全退出！');
    }

    /**
     * 获取地区信息
     * @author demo
     */
    public function ajaxArea() {
        $this->checkRequest();
        $areaID = $_REQUEST['id'];

        // $this->ajaxReturn(A('User','Logic')->areaByID($areaID));

        $IData=$this->getApiAat('User/areaByID',$areaID);
        if($IData[0]== 1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }

    }

    /**
     * 根据地区返回学校数据
     * @author demo
     */
    public function ajaxSchool() {
        $this->checkRequest();
        $areaID = $_POST['id'];
        // $this->ajaxReturn(A('User','Logic')->schoolByAreaID($areaID));
        $IData=$this->getApiAat('User/schoolByAreaID',$areaID);
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }


    }

    /**
     * 获取年级信息
     * @author demo
     */
    public function ajaxGrade(){
        $this->checkRequest();
        // $this->ajaxReturn(A('User','Logic')->grade());
        $IData=$this->getApiAat('User/grade');
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }
    }

    /**
     * 前端7种测试推题函数
     * 新增记录到testRecord表和testRecordAttr表
     * 11月13日重写
     * @author demo
     */
    public function ajaxGetTest() {
        $this->checkRequest();
        $subjectID = $this->getSubjectID();
        $username = $this->getUserName();
        $userID = $this->getUserID();
        $type = $_REQUEST['id'];//测试类型
        switch ($type) {
            case 1:
                //智能测试
                $param = [
                    'version'=>$this->getVersion(),
                ];
                break;
            case 2:
                $param = [
                    'klID'=>$_POST['KID'],
                ];
                break;
            case 3:
                //整卷练习-现有套卷
                $param = [
                    'docID'=>$_POST['DocID'],
                ];
                break;
            case 4:
                //整卷练习-自定义智能组卷
                $param = [
                    'diff'=>$_REQUEST['Diff'],//难度
                    'cover'=>$_REQUEST['Cover'],//知识点覆盖率
                    'typesID'=>$_REQUEST['TypesID'],
                    'typesNum'=>$_REQUEST['TypesNum'],
                    'typesScore'=>$_REQUEST['TypesScore'],
                    'dScore'=>$_REQUEST['DScore'],
                    'klID'=>$_REQUEST['KlID'],
                ];
                break;
            case 5:
                //阶段测试
                $param = ['klID'=>$_REQUEST['KlID']];
                break;
            case 6:
                //目标测试
                $param = [
                    'score'=>$_REQUEST['Score'],
                    'totalScore'=>$_REQUEST['TotalScore'],
                    'klID'=>$_REQUEST['KlID'],
                ];
                break;
            case 7:
                //章节测试
                $param = ['chapterID'=>$_REQUEST['chapterID'],];
                break;

            //---------------- 增加专题作答  16-4-26 ------------------
            case 8:{
                $param = array(
                    'topicPaperID' => $_REQUEST['tpid']
                );
                break;
            }
            default:
                $param = [];
        }
        $recordIDIData = $this->getApiAat('PushTest/pushTest',$userID,$username,$type,$subjectID,$param);
        //返回跳转页面的数据
        if($recordIDIData[0]==0){
            // $this->ajaxReturn(null,$recordIDIData[1],0);
            $this->setError($recordIDIData[1],1);
        }
        // $this->ajaxReturn(['record_id' => $recordIDIData[1]], 'success', 1);
        if($recordIDIData[2]!==false){
            $recordIDIData[2]=true;
        }

        // $this->ajaxReturn(['record_id' => $recordIDIData[1],'ifNotDo' => $recordIDIData[2]], 'success', 1);
        $this->setBack(['record_id' => $recordIDIData[1],'ifNotDo' => $recordIDIData[2]]);
    }

    /**
     * ajax获取知识点
     * @author demo
     */
    public function ajaxKl() {
        $this->checkRequest();
        $subjectIData = $this->getApiAat('PushTest/userAbilityKl', $this->getUserName(), $this->getSubjectID());
//       var_dump($subjectIData);die;
        // $this->ajaxReturn($subjectIData);
        if($subjectIData[0] ==1){
             $this->setBack($subjectIData[1]);
        }else{
             $this->setError($subjectIData[1],1);
        }
    }

    /**
     * 获取目标测试记录
     * @author demo 5.6.24
     */
    public function ajaxAimRecord() {
        $this->checkRequest();
        $subjectID = $this->getSubjectID();
        $username = $this->getUserName();
        $aimRecordIData = $this->getApiAat('PushTest/amiRecord',$subjectID,$username);
        if($aimRecordIData[0]==1){
            // $this->ajaxReturn(['list'=>$aimRecordIData[1]['list']],'success',1);
             $this->setBack(['list'=>$aimRecordIData[1]['list']]);
        }else{
            // $this->ajaxReturn(null,$aimRecordIData[1],0);
             $this->setError($aimRecordIData[1],1);
        }
    }

    /**
     * 获取各个学科标准卷分数，用于目标训练添加目标
     * @author demo
     */
    public function ajaxNormRecord(){
        $this->checkRequest();
        $IData = $this->getApiAat('PushTest/normalScore',$this->getSubjectID());
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }
        // $this->ajaxReturn($IData);
    }

    /**
     * 获取用户不同等级的知识点
     * @author demo 05.6.19
     */
    public function ajaxLevelKl() {
        $this->checkRequest();
        $subjectID = $this->getSubjectID();
        $level = $_REQUEST['level'];
        $klIData = $this->getApiAat('PushTest/userLevelKl',$this->getUserName(),$subjectID,$level);

        // $this->ajaxReturn($klIData);
        if( $klIData[0] ==0){
            $this->setError($klIData[1],1);
        }else{
            $this->setBack($klIData[1]);
        }
    }

    /**
     * ajax获取学科试题类型
     * @author demo
     */
    public function ajaxType() {
        $IData=$this->getApiAat('PushTest/type',$this->getSubjectID());
        // $this->ajaxReturn(A('Exercise/PushTest','Logic')->type($this->getSubjectID()));
        if($IData[0] ==1 ){
             $this->setBack($IData[1]);
        }else{
             $this->setError($IData[1],1);
        }
    }

    /**
     * ajax获取试卷
     * @author demo
     */
    public function ajaxDoc() {
        $this->checkRequest();
        $subjectID = $this->getSubjectID();//兼容Android2.2.0之前的版本
        $style = $_POST['style'];
        $year = $_POST['year'];
        $searchKey = $_POST['search'];
        $pageSize = 10;
        $IData = $this->getApiAat('PushTest/docList',$subjectID,$style,$year,$searchKey,$pageSize);

        if($IData[0]==1){
        //     $this->ajaxReturn([
        //         'show'=>$IData[1]['show'],
        //         'list'=>$IData[1]['list'],
        //         'year'=>$IData[1]['year'],
        //         'searchKey'=>$IData[1]['searchKey'],
        //     ],'success',1);
             $this->setBack([
                'show'=>$IData[1]['show'],
                'list'=>$IData[1]['list'],
                'year'=>$IData[1]['year'],
                'searchKey'=>$IData[1]['searchKey'],
            ]);
        }else{
             $this->setError($IData[1],1);
        }
    }

    /**
     * 获取学科初中或高中的学科列表
     * @author demo
     */
    public function ajaxSubject() {
        $this->checkRequest();
        $IData = $this->getApiAat('PushTest/subject',$this->getGradeID());
        if($IData[0]==1){
         $this->setBack($IData[1]);
        }
        $this->setError($IData[1],1);
    }

    /**
     * 验证码显示
     * @return resource 验证码图片
     * @author demo
     */
    public function verify() {
        $imageMsg['total']=4;
        $imageMsg['num']=1;
        $imageMsg['style']='png';
        $imageMsg['width']=95;
        $imageMsg['height']=40;
        $imageMsg['action']='AatVerify';
        R('Common/UserLayer/verify',array($imageMsg));
    }

    /**
     * 获取服务条款HTML
     * @author demo
     */
    public function getServiceTerm(){
        $getService=R('Common/UserLayer/getServiceTerm');
        // $this->ajaxReturn($getService,'success',1);
        $this->setBack($getService);
    }

    /**
     * 测试方法通用
     * @author demo
     */
    public function apiTest() {
        //动态参数
        $phoneSn='111';
        $loginName='68698189@qq.com';
        $password='68698189';
        $remember=0;
        $buffer=$this->getApiUser('User/login',$loginName,$password,$remember);
        $phone='1';
        $phoneKey = C('APP_KEY');

        $authTimeout = $phone?3600*24*365:C('WLN_COOKIE_TIMEOUT');

        $saveCode=',K((rV$PJU~v4m,';

        $code=md5($buffer[1]['UserID'] . $buffer[1]['UserName'] . $saveCode . ceil(time()/$authTimeout));
        $userID=$buffer[1]['UserID'];
        $this->assign('params',array(
                        //参数名=》参数值
                        'phoneSn'=>$phoneSn,
                        'token'=>md5($phone . $phoneKey . $phoneSn),
                        'phone'=>$phone,
                        'versionCode'=>75,
                        'userName'=>$loginName,
                        'userCode'=>$code,
                        'userID'=>$userID
                    ));
        $this->display();
    }
}