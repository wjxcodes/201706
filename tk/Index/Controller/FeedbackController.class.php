<?php
/**
 * @author demo 
 * @date 2014年8月7日
 * @update 2015年1月28日
 */
/**
 * 题库官网表单处理
 */
namespace Index\Controller;
class FeedbackController extends BaseController{
    private $feedbackModel;
    
    public function _initialize(){
        parent::_initialize();
        $this->feedbackModel = $this->getModel('Feedback');
    }
    /**
     * 题库官网申请试用表单处理
     * @author demo 
     * @update 2015年9月29日
     */
    public function applyF(){
        if ($_SESSION['verify'] != md5($_POST['verify'])) {
            $this->setBack('verify');
        }
        if(empty($_POST['usetype'])){
            $this->setError('80111');
        }
        $province = $_POST['province'];//省
        $city = $_POST['city'];//市区
        $country = $_POST['country'];//县
        $school = trim($_POST['school']);//学校
        $userName = trim($_POST['username']);//联系人
        $phone = trim($_POST['phone']);//电话
        $email = $_POST['email'];//邮箱
        $address = $_POST['address'];//详细地址
        $useType = $_POST['usetype'];//开通方式 ip/名单
        $IP = $_POST['ip'];//ip地址
        $subject=$_POST['select_subject']; //个人所属学科
        $chapterFirst=$_POST['select_edition']; //所属版本
        $chapter=$_POST['select_section']; //所属章节
        //验证学校
        if(!((preg_match('/^[\x{4e00}-\x{9fa5}]+$/u',$school) && strlen($school)>=6 && strlen($school)<=75) || preg_match('/[a-zA-Z0-9\'‘\(\).。\-&]{2,25}/',str_replace(' ','',str_replace('\\','',$school))))){
            $this->setError('80101');
        }
        //验证用户名
        if(!(preg_match('/^[\x{4e00}-\x{9fa5}]{2,6}$/u',$userName))){
            $this->setError('80102');
        }

        //验证手机
        if(!checkString('checkIfPhone',$phone)){
            $this->setError('30211');
        }

        if(!empty($email)){
            if(!checkString('checkIfEmail',$email)){
                $this->setError('30212');
            }else{
                $email = '<br>邮箱：'.$email;
            }
        }else{
            $email='';
        }
        //存cookie,提交失败便与修改
        $indexInfo = serialize($_POST);
        cookie('indexInfo',$indexInfo,120);
        $address = '<br>地址：'.$province.$city.$country.$address;
        if($subject){
            $subject='<br>学科：'.$subject;
        }
        if($chapterFirst){
            $chapterFirst='<br>所选版本：'.$chapterFirst;
        }
        if($chapter){
            $chapter='<br>所选章节：'.$chapter;
        }
        if($useType=='ip'&&$IP){
            $ip = '<br>IP：'.$IP;
        }
        //根据用户名跟电话判断数据是否存在，防止重复提交
        //只对送书数据进行排重
        if($useType=='recvbook'){
            $where='UserName="'.$userName.'" or Content like "%电话：'.$phone.'%"';
            $checkunique = $this->feedbackModel->getFeedback('*',$where,'FeedbackID desc');
            if($checkunique){
                $this->setError('80113');
            }
        }
        $data['OpenStyle']=$useType;
        $data['Style'] = 0;
        $data['From'] = 'index';
        $data['LoadTime'] = time();
        $data['UserName'] = $userName;
        $data['Content'] = '学校名：'.$school.'<br>电话：'.$phone.$email.$address.$ip.$subject.$chapterFirst.$chapter;

        if($_POST['usetype'] == 'name'){

            $path=R('Common/UploadLayer/uploadExcel',array('bbs','work'));
            if(is_array($path)){
                $this->setError($path[0],0,'',$path[1]);
            }
            $data['FilePath'] = $path;

        }
        
        $res = $this->feedbackModel->insertFeedback($data);
        if ($res) {
            cookie('indexInfo',null);
            $successMsg='申请成功,我们会尽快与您取得联系';
            if($useType=='recvbook'){
                $successMsg='<p style="font-size:24px;">恭喜您已成功申请样书！</p><p style="font-size:12px; padding-top:5px;">请耐心等待，我们会以最快的速度将《教材帮》《一遍过》送到您的身边！</p>';
            }
            $this->showSuccess($successMsg);
        } else {
            $this->setError('80110');//申请失败,请重试
        }
    }
    /**
     * 题库官网在线留言表单处理
     * @author demo 
     * @update 2015年9月29日
     */
    public function onlineS(){
        if (empty($_POST)) { exit;}
        $company = strip_tags($_POST['company'], "<br>");
        $email = trim($_POST['email']);
        //验证表单数据格式
        if (!preg_match('/[0-9a-zA-Z\x{4e00}-\x{9fa5}]{4,}/u', $company)) {
            $this->setBack('company');
        } 
        if (!preg_match('/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$|^[0-9]{2,4}\-?[0-9]{3,7}$/', $email)) {
            $this->setBack('email');
        }
        if ($_SESSION['verify'] != md5($_POST['verify'])) {
            $this->setBack('verify');
        } else {
        //存数据库
            $data['Style'] = 1;
            $from=$_POST['from'];
            if(empty($from)){
                $from='index';
            }
            $data['From'] = $from;
            $data['LoadTime'] = time();
            $data['Content'] = '内容：'.strip_tags($_POST['company'])."<br>邮箱/电话/QQ：".strip_tags($_POST['email']);
            $data['UserName'] = '';
            if($cookieUserName=$this->getCookieUserName('Home')){
                $data['UserName'] = $cookieUserName;
            }
            $res = $this->feedbackModel->insertFeedback($data);
            if ($res) {
                $this->setBack('success');
            } else {
                $this->setBack('false');
            }
        }
    }
    
    /**
     * 显示反馈页面
     * @author demo
     */
    public function index() {
        $this->ifLoginHome();
        $this->display();
    }
    /**
     * 保存
     * @author demo
     */
    public function saveFeedback() {
        $content = array();
        //用户提交过来的表单数据
        $content['Visual'] = $_POST['Visual'];
        $content['DisplayValid'] = $_POST['DisplayValid'];
        $content['DisplayValidSugg'] = $_POST['DisplayValidSugg'];
        $content['SearchMethods'] = implode(",", $_POST['SearchMethods']); //可以多选的数据
        $content['KLCatalog'] = $_POST['KLCatalog'];
        $content['KLCatalogSugg'] = $_POST['KLCatalogSugg'];
        $content['MeetDemand'] = $_POST['MeetDemand'];
        $content['MeetDemandSugg'] = $_POST['MeetDemandSugg'];
        $content['SetScore'] = $_POST['SetScore'];
        $content['SetScoreSugg'] = $_POST['SetScoreSugg'];
        $content['WaitTime'] = $_POST['WaitTime'];
        $content['TwoTemp'] = $_POST['TwoTemp'];
        $content['TwoTempSugg'] = $_POST['TwoTempSugg'];
        $content['TypeSet'] = $_POST['TypeSet'];
        $content['DownProblem'] = $_POST['DownProblem'];
        $content['TestAnalysis'] = $_POST['TestAnalysis'];
        $content['TestAnalysisSugg'] = $_POST['TestAnalysisSugg'];
        $content['AnswerCard'] = $_POST['AnswerCard'];
        $content['AnswerCardSugg'] = $_POST['AnswerCardSugg'];
        $content['HomeWork'] = $_POST['Homework'];
        $content['HomeWorkSugg'] = $_POST['HomeworkSugg'];
        $content['DownNum'] = $_POST['DownNum'];
        $content['CompleteDown'] = $_POST['CompleteDown'];
        $content['CompDownSugg'] = $_POST['CompDownSugg'];
        $content['OrigPre'] = $_POST['OrigPre'];
        $content['PerQuesBank'] = $_POST['PerQuesBank'];
        $content['PerQuesBankSugg'] = $_POST['PerQuesBankSugg'];
        $content['LongTimeCoop'] = $_POST['LongTimeCoop'];
        $content['LongTimeCoopSugg'] = $_POST['LongTimeCoopSugg'];
        $content['CompreSugg'] = $_POST['CompreSugg'];
        $content['UserName'] = $this->getCookieUserName('Home');
        $content['AddTime'] = time();
        //保存数据
        if ($this->getModel('Question')->addQuestion($content) === false) {
            exit('error');
        } else {
            exit('success');
        }
    }
    
    /**
     * 是否登录组卷系统的处理
     * @author demo 
     * @update 2015年9月29日
     */
    public function ifLoginHome(){
        $userName=$this->getCookieUserName('Home');
        if($userName==''){
            redirect(U('/Home','',false));
        }
        $db = $this->getModel('User')->getInfoByWhere(
            'UserID,UserName,SaveCode',
            array('UserName'=>$userName)
        );
        $userCode=$this->getCookieCode('Home');
        $time=C('WLN_COOKIE_TIMEOUT');
        $code=md5($db[0]['UserID'].$db[0]['UserName'].$db[0]['SaveCode'].ceil(time()/$time));
        $code1=md5($db[0]['UserID'].$db[0]['UserName'].$db[0]['SaveCode'].(ceil(time()/$time)-1));

        if($userCode!=$code && $userCode!=$code1){
            redirect(U('/Home','',false));
        }
    }
    
    /**
     * 送书
     * @author demo
     */
    public function book(){
        if(C('WLN_SEND_BOOK')==0){
            $this->showSuccess('新一轮的赠书马上开启，敬请期待！',"location='".U('/Home','',false)."'");
        }
        $ip = get_client_ip(0,true);//获取ip
        $buffer = SS('areaChildList'); // 缓存子类list数据
        $areaArray = $buffer[0]; //省份数据集
        $this->assign('ip', $ip); //省份数据集
        $this->assign('areaArray', $areaArray); //省份数据集
        $this->display();
    }
}