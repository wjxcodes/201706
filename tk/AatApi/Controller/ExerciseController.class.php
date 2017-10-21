<?php

/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-3-21
 * Time: 下午2:20
 */
namespace AatApi\Controller;
class ExerciseController extends BaseController
{
    protected $userAnswerRecordModel;
    protected $testRealModel;
    protected $testJudgeModel;

    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
        //增加权限检查在这里或者在BaseController中做检查
        $this->userAnswerRecordModel = $this->getModel('UserAnswerRecord');
        $this->testRealModel = $this->getModel('TestReal');
        $this->testJudgeModel = $this->getModel('TestJudge');
    }

    /**
     * 检测对应测试id是否做过
     * @param int id 测试id true
     * @return array
     *   [
     *       "data"=>[
     *          true/false//为true则没有测试过。为false则测试过。跳转到测试报告页
     *      ],
     *      "info"=>"success",
     *       "status"=>1
     *   ]
     * @author demo
     */
    public function getCheck(){
        $this->checkRequest();
        $testRecordID = $_POST['id'];
        // $check = A('Exercise/Base','Logic')->checkExerciseRID($testRecordID,$ifDone = false,$this->getUserName());
        $check = $this->getApiAat('Base/checkExerciseRID',$testRecordID,$ifDone = false,$this->getUserName());
        // $this->ajaxReturn($check, 'success', 1);
        $this->setBack($check);
    }

    /**
     * 获取测试题内容byid
     * @author demo
     */
    public function getTestByID(){
        $this->checkRequest();
        $testID = $_POST['id'];
        $recordID = $_POST['recordID'];
        $isHomeWork = $_POST['isHomeWork'];
        $ifAnswer = $_POST['ifAnswer'];
        $IData = $this->getApiAat('Exercise/getTestByID',$testID,$recordID,$isHomeWork,$ifAnswer);

        if($IData[0]==0){
            // $this->ajaxReturn(null,$IData[1],0);
            $this->setError($IData[1]);
        }

        $k=$IData[1][$testID];
        if(empty($ifAnswer)){
            unset($k['Answer']);
            unset($k['Analytic']);
        }
        $testArray['test_id'] = $k['TestID'];
        $testArray['test_title'] = $k['Test']['title'];
        $testArray['test_type'] = $k['TypesID'];
        $testArray['if_choose'] = $k['IfChoose'];
        $testArray['doc_id'] = $k['DocID'];
        $testArray['right_answer'] = strip_tags($k['Answer']);
        //根据题型判断
        if ($k['IfChoose'] == 3) {
            //单选
            $testArray['answer_id'] = $recordID . '-' . $k['TestID'] . '-' . $k['doAnswer'][0]['Number'] . '-' . $k['doAnswer'][0]['OrderID'] . '-' . $k['IfChoose'];
            $testArray['number'] = $k['doAnswer'][0]['Number'];
            $testArray['test_options'] = $k['Test']['options'];
            $testArray['answer'] = $k['doAnswer'][0]['AnswerText'];
        } elseif ($k['IfChoose'] == 2) {
            //多选
            $testArray['answer_id'] = $recordID . '-' . $k['TestID'] . '-' . $k['doAnswer'][0]['Number'] . '-' . $k['doAnswer'][0]['OrderID'] . '-' . $k['IfChoose'];
            $testArray['number'] = $k['doAnswer'][0]['Number'];
            $testArray['test_options'] = $k['Test']['options'];
            $testArray['answer'] = explode(',', $k['doAnswer'][0]['AnswerText']);
        } elseif ($k['IfChoose'] == 1) {
            //复合题
            $testArray['number'] = $k['doAnswer'][1]['Number'];
            foreach ($k['Test']['sub'] as $j => $m) {
                //$k['Test']['sub']是从1开始
                $answerRecord = $k['doAnswer'][$j];
                $testArray['sub'][$j]['answer_id'] = $recordID . '-' . $k['TestID'] . '-' . $answerRecord['Number'] . '-' . $answerRecord['OrderID'] . '-' . $answerRecord['IfChoose'];
                $testArray['sub'][$j]['order'] = $answerRecord['OrderID'];
                $testArray['sub'][$j]['sub_title'] = $m['title'];
                $testArray['sub'][$j]['if_choose'] = $answerRecord['IfChoose'];
                if ($answerRecord['IfChoose'] == 3) {
                    //复合题中单选
                    $testArray['sub'][$j]['sub_options'] = $m['options'];
                    $testArray['sub'][$j]['sub_answer'] = $answerRecord['AnswerText'];
                } elseif ($answerRecord['IfChoose'] == 2) {
                    //复合体中多选
                    $testArray['sub'][$j]['sub_options'] = $m['options'];
                    $testArray['sub'][$j]['sub_answer'] = explode(',', $answerRecord['AnswerText']);
                } else {
                    //复合题中大题
                    $answerImageArray = $this->processAppUserAnswer('explode',formatString('IPReturn',stripslashes($answerRecord['AnswerText'])));
                    $testArray['sub'][$j]['sub_answer'] = $answerImageArray[0];
                    $testArray['sub'][$j]['subAnswerImage'] = $answerImageArray[1];
                }
            }
        } else {
            //其它
            $testArray['number'] = $k['doAnswer'][0]['Number'];
            $testArray['answer_id'] = $recordID . '-' . $k['TestID'] . '-' . $k['doAnswer'][0]['Number'] . '-' . $k['doAnswer'][0]['OrderID'] . '-' . $k['IfChoose'];
            $answerImageArray = $this->processAppUserAnswer('explode',formatString('IPReturn',stripslashes($k['doAnswer'][0]['AnswerText'])));
            $testArray['answer'] = $answerImageArray[0];
            $testArray['answerImage'] = $answerImageArray[1];
            $testArray['right_answer'] = $k['Answer'];
        }
        $this->setBack($testArray);
    }

    /**
     * 获取测试题目信息
     * @param int send_id  作业ID true
     * @param int id 练习ID true
     * @param int ifShowID 返回试题属性，不返回题文答案内容，0返回内容 1返回属性 false
     * @return array
     * [
     * "data"=> [
     *   "time"=> "4", //当前测试耗时，单位秒
     *   "style"=> "整卷练习（试卷）",//当前测试名称
     *   "aatTestStyle"=> "是否是专题测试",
     *   "jumpUrl"=> "做完试题后的跳转地址",
     *   //如果ifShowID为1则test字段返回 以英文逗号间隔的id号
     *   "test"=> [
     *       [
     *           "test_id"=> "389359",
     *           "test_title"=>试题标题,
     *           "test_type"=> "71",
     *           "if_choose"=> "3", //试题选择类型：0非选择 1复合题 2单选 3多选
     *           "doc_id"=> "25223",
     *           "right_answer"=> "B",
     *           "answer_id"=> "156323-389359-1-0-3",// 用户作答数据如“A,B” 或者“汉字文字”
     *           "number"=> 1, //试题序号，用于显示
     *           "test_options"=> [
     *               //答案选项
     *           ],
     *           "answer"=> //答案
     *       ]
     *       ....
     *   ]
     * ],
     * "info"=> "success",
     * "status"=> 1
     * ]
     * @author demo
     */
    public function getTest(){
        $this->checkRequest();
        $testRecordID = $_POST['id'];
        $ifShowID = $_POST['ifShowID'];
        $username = $this->getUserName();
        // $IData = A('Exercise/Exercise','Logic')->getTest($testRecordID,$username);
        $IData = $this->getApiAat('Exercise/getTest',$testRecordID,$username,$ifShowID);
        if($IData[0]==0){
            // $this->ajaxReturn(null,$IData[1],0);
            $this->setError($IData[1]);
        }
        $IData = $IData[1];

        $result=array();
        //获取测试所耗时间，有时间则填充
        $result['time'] = $IData['record']['RealTime'];
        //测试类型
        $result['style'] = $IData['record']['Style'];
        $styleConfig = C('WLN_TEST_STYLE_NAME');
        $result['style'] = $styleConfig[$result['style']];
        if($IData['record']['TopicName']){
            $result['style'] = $IData['record']['TopicName'];
        }
        $result['aatTestStyle'] = $IData['record']['AatTestStyle'];
        $result['jumpUrl'] = $IData['record']['JumpUrl']; //做完试题后的跳转地址
if($_REQUEST['ss']==111){
            exit(print_r($IData['test']));
        }

        //遍历此次测试的所有试题和之前作答信息
        $testArray = [];
        foreach ($IData['test'] as $i => $k) {
            $testArray[$i]['test_id'] = $k['TestID'];
            if(empty($ifShowID)){
                $testArray[$i]['test_title'] = $k['Test']['title'];
                $testArray[$i]['right_answer'] = strip_tags($k['Answer']);
            }
            $testArray[$i]['test_type'] = $k['TypesID'];
            $testArray[$i]['if_choose'] = $k['IfChoose'];
            $testArray[$i]['doc_id'] = $k['DocID'];
            //根据题型判断
            if ($k['IfChoose'] == 3) {
                //单选
                $testArray[$i]['answer_id'] = $testRecordID . '-' . $k['TestID'] . '-' . $k['doAnswer'][0]['Number'] . '-' . $k['doAnswer'][0]['OrderID'] . '-' . $k['IfChoose'];
                $testArray[$i]['number'] = $k['doAnswer'][0]['Number'];
                $testArray[$i]['if_answer']=$k['doAnswer'][0]['AnswerText']?1:0;
                if(empty($ifShowID)){
                    $testArray[$i]['test_options'] = $k['Test']['options'];
                    $testArray[$i]['answer'] = $k['doAnswer'][0]['AnswerText'];
                }
            } elseif ($k['IfChoose'] == 2) {
                //多选
                $testArray[$i]['answer_id'] = $testRecordID . '-' . $k['TestID'] . '-' . $k['doAnswer'][0]['Number'] . '-' . $k['doAnswer'][0]['OrderID'] . '-' . $k['IfChoose'];
                $testArray[$i]['number'] = $k['doAnswer'][0]['Number'];
                $testArray[$i]['if_answer'] = $k['doAnswer'][0]['AnswerText']?1:0;
                if(empty($ifShowID)){
                    $testArray[$i]['test_options'] = $k['Test']['options'];
                    $testArray[$i]['answer'] = explode(',', $k['doAnswer'][0]['AnswerText']);
                }
            } elseif ($k['IfChoose'] == 1) {
                //复合题
                $testArray[$i]['number'] = $k['doAnswer'][1]['Number'];
                foreach ($k['Test']['sub'] as $j => $m) {
                    //$k['Test']['sub']是从1开始
                    $answerRecord = $k['doAnswer'][$j];
                    $testArray[$i]['sub'][$j]['answer_id'] = $testRecordID . '-' . $k['TestID'] . '-' . $answerRecord['Number'] . '-' . $answerRecord['OrderID'] . '-' . $answerRecord['IfChoose'];
                    $testArray[$i]['sub'][$j]['order'] = $answerRecord['OrderID'];
                    $testArray[$i]['sub'][$j]['sub_title'] = $m['title'];
                    $testArray[$i]['sub'][$j]['if_choose'] = $answerRecord['IfChoose'];
                    $testArray[$i]['sub'][$j]['if_answer'] = $answerRecord['AnswerText']?1:0;

                    if(empty($ifShowID)){
                        if ($answerRecord['IfChoose'] == 3) {
                            //复合题中单选
                            $testArray[$i]['sub'][$j]['sub_options'] = $m['options'];
                            $testArray[$i]['sub'][$j]['sub_answer'] = $answerRecord['AnswerText'];
                        } elseif ($answerRecord['IfChoose'] == 2) {
                            //复合体中多选
                            $testArray[$i]['sub'][$j]['sub_options'] = $m['options'];
                            $testArray[$i]['sub'][$j]['sub_answer'] = explode(',', $answerRecord['AnswerText']);
                        } else {
                            //复合题中大题
                            $answerImageArray = $this->processAppUserAnswer('explode',formatString('IPReturn',stripslashes($answerRecord['AnswerText'])));
                            $testArray[$i]['sub'][$j]['sub_answer'] = $answerImageArray[0];
                            $testArray[$i]['sub'][$j]['subAnswerImage'] = $answerImageArray[1];
                        }
                    }
                }
            } else {
                //其它
                $testArray[$i]['number'] = $k['doAnswer'][0]['Number'];
                $testArray[$i]['answer_id'] = $testRecordID . '-' . $k['TestID'] . '-' . $k['doAnswer'][0]['Number'] . '-' . $k['doAnswer'][0]['OrderID'] . '-' . $k['IfChoose'];
                $testArray[$i]['if_answer']=$k['doAnswer'][0]['AnswerText']?1:0;

                if(empty($ifShowID)){
                    $answerImageArray = $this->processAppUserAnswer('explode',formatString('IPReturn',stripslashes($k['doAnswer'][0]['AnswerText'])));
                    $testArray[$i]['answer'] = $answerImageArray[0];
                    $testArray[$i]['answerImage'] = $answerImageArray[1];
                    $testArray[$i]['right_answer'] = $k['Answer'];
                }
            }
        }
        //因为数据时索引中获取的，所以要对数据排序：对test_array进行排序 按照Number字段排序（索引）
        foreach ($testArray as $i => $iTestArray) {
            $numberArr[$i] = $iTestArray['number'];
        }
        array_multisort($numberArr, SORT_ASC, $testArray);
        //排序后再生成Type，以防止题型顺序破坏试题的顺序
        $type = array();//此次测试试题中的题型
        $typeAmount = array();//记录题型下的试题数量
        foreach ($testArray as $iTestArray) {
            array_push($type, $iTestArray['test_type']);
            $typeAmount[$iTestArray['test_type']]++;
        }

        //测试题型
        $type = array_unique($type);
        $typeCache = SS('types');
        foreach ($type as $j => $jType) {
            $result['type'][$j]['TypesName'] = $typeCache[$jType]['TypesName'] ? $typeCache[$jType]['TypesName'] : '其它';
            $result['type'][$j]['TypesID'] = $jType;
            $result['type'][$j]['TypesAmount'] = $typeAmount[$jType];//提醒下的试题数量
        }
        $result['test'] = $testArray;
        // $this->ajaxReturn($result, 'success', 1);
        $this->setBack($result);
    }

    /**
     * 描述：手机端上传
     * @author demo
     */
    public function uploadImage() {
        R('Common/UploadLayer/appUpload');
    }

    /**
     * 提交试卷处理
     * 判断单选和多选的题目正确性，插入zj_user_answer_record表答题记录，更新zj_user_test_record表信息
     * @param string send_id 作业-作业ID  true
     * @param int do_time  作业-作答时间，单位秒  true
     * @param string TestRecordID  练习ID  true
     * @param int RealTime 作答时间，单位秒  true
     * @return array
     *        正确
     *   [
     *       "data"=>null
     *      "info"=>"success",
     *       "status"=>1
     *   ]
     *   错误返回类似下面格式的错误信息
     *   [
     *       "data"=>null,
     *       "info"=>"失败原因",
     *       "status"=>0
     *   ]
     * @author demo
     */
    public function doSubmit() {
        $this->checkRequest();
        $testRecordID = $_REQUEST['TestRecordID'];
        $realTime = $_REQUEST['RealTime'];
        $username = $this->getUserName();
        // $testRecordIDIData = A('Exercise/Exercise','Logic')->submit($testRecordID,$realTime,$username);
        $testRecordIDIData = $this->getApiAat('Exercise/submit',$testRecordID,$realTime,$username);
        // $this->ajaxReturn($testRecordIDIData);
        if($testRecordIDIData[0] ==1){
            $this->setBack($testRecordIDIData[1]);
        }else{
            $this->serError($testRecordIDIData[1]);
        }
    }

    /**
     * 处理每次点击试题答案时提交的数据
     * @param string|array answer 单选：“A”,多选：数组“[A,B]“，非选择：”文字” true
     * @param array answerImage 用户上传图片地址，必须是数组，一张图片也必须是数组 false
     * @param string answer_id 答案ID true
     * @return array
     *        正确
     *   [
     *       "data"=>null
     *      "info"=>"success",
     *       "status"=>1
     *   ]
     *   错误返回类似下面格式的错误信息
     *   [
     *       "data"=>null,
     *       "info"=>"失败原因",
     *       "status"=>0
     *   ]
     * @author demo
     */
    public function doAnswer() {
        $this->checkRequest();
        $answerID = $_REQUEST['answer_id'];
        //处理answer字段，兼容数组和非数组，非数组多选题，逗号分割
        $oldAnswer = $_REQUEST['answer'];
        if(is_array($oldAnswer)){
            $answerText = trim(implode(',', $oldAnswer));
        }else{
            $answerText = trim($oldAnswer);
        }
        $imageArr = $_POST['answerImage'];
        if($imageArr&&is_array($imageArr)){
            $answerText = $this->processAppUserAnswer('implode',$answerText,$imageArr);
        }
        // $IData = A('Exercise/Exercise','Logic')->doAnswer($answerID,$answerText,$doTime=null);
        $IData = $this->getApiAat('Exercise/doAnswer',$answerID,$answerText,$doTime=null);
        // $this->ajaxReturn($IData);
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }
    }

    /**
     * 处理用户浏览器关闭或者刷新页面时提交的做题用时数据
     * @param string send_id 作业-作业ID  true
     * @param int do_time 作业-作答时间，单位秒  true
     * @param string TestRecordID 练习ID  true
     * @param int RealTime 作答时间，单位秒  true
     * @author demo
     */
    public function doClose() {
        $this->checkRequest();
        $testRecordID = $_REQUEST['TestRecordID'];
        $doTime = $_REQUEST['RealTime']?$_REQUEST['RealTime']:1;
        $username = $this->getUserName();
        // $IData = A('Exercise/Exercise','Logic')->close($testRecordID,$doTime,$username);
        $IData = $this->getApiAat('Exercise/close',$testRecordID,$doTime,$username);
        // $this->ajaxReturn($IData);
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }
    }

   /**
    * ajax纠错信息提交；
    * @param string testID 试题ID  true
    * @param string correctContent 纠错内容、字数限制500字  true
    * @param string typeID 英文逗号分隔；0题目类型、1题目答案、2题目解析3、题目知识点、4其他  true
    * @return array
    *   [
    *    "data"=>null,
    *    "info"=>"提交成功，感谢您的反馈！",
    *    "status"=>1
    *    ]
    * @author demo
    */
    public function correct(){
        $testID = $_POST['testID'];
        $content = $_POST['correctContent'];
        $typeID = $_POST['typeID'];
        $username = $this->getUserName();
        // $IData = A('Exercise/Exercise','Logic')->testFeedback($testID,$typeID,$content,$username,$isApp=true);
        $IData = $this->getApiAat('Exercise/testFeedback',$testID,$typeID,$content,$username,$isApp=true);
        // $this->ajaxReturn($IData);
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1]);
        }
    }

    /**
     * 下载音频文件
     * @author demo
     */
    public function downloadAudio(){
        $docId = (int)$_GET['docId'];
        if(empty($docId)){
            // $this->ajaxReturn(null,'文档编号不能为空！', 0); //数据标识不能为空！
            $this->setBack('文档编号不能为空！');
        }
        $result = $this->getModel('DocHearing')->downloadAudioFile($docId);
        if($result === false){
            // $this->ajaxReturn(null, '下载失败！',0);
            $this->setError('下载失败！');
        }
    }
}