<?php
/**
 * 作业练习（导学案）类
 */
/**
 * @author demo
 * @date 2014-6-24
 */
namespace AatApi\Controller;
class MyHomeworkExerciseController extends BaseController
{
    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
    }

    /**
     * 第一次做作业的情况
     * 获取作业workID和班级classID生成SendID并返回SendID代表作业记录生成成功
     * @author demo
     */
    public function indexCreate() {
        $requestID = $_REQUEST['id'];
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Exercise/homeworkCreateSendID', $requestID,$userID);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

    /**
     * 获取测试类作业的试题数据
     * @param int send_id  作业ID  true
     * @param int id 练习ID  true
     * @param int ifShowID 返回试题属性，不返回题文答案内容，0返回内容 1返回属性 false
     * @return array
     * [
     * "data"=> [
     *   "StartTime"=> 开始作答时间, 
     *   "EndTime"=> 结束作答时间, 
     *   "LoadTime"=> 提交时间, 
     *   "UserName"=> "demo", 
     *   "Message"=> "", 
     *   "SubjectName"=> "数学", 
     *   "DoTime"=> "24", 
     *   "test"=> [
     *       [
     *           "test_id"=> 80718, 
     *           "test_title"=>题目, 
     *           "test_type"=> 79, 
     *           "if_choose"=> 3, 
     *           "doc_id"=> 5255, 
     *           "answer_id"=> "9438-80718-1-0-3", 
     *           "number"=> 1, 
     *           "test_options"=> [
     *              选项
     *           ], 
     *           "answer"=> "", 
     *           "right_answer"=> "B"
     *       ]
     *   ]
     * ], 
     * "info"=> "success", 
     * "status"=> 1
     * ]
     * @author 
     */
    public function getTest() {
        $this->checkRequest();
        $sendID = $_REQUEST['send_id'];
        $userID = $this->getUserID();
        $ifShowID = $_POST['ifShowID'];
        $IData = $this->getApiAat('Exercise/homeworkGetTest', $sendID,$userID);
        if($IData[0]==0){
            return $this->setError($IData[1], 1);
        }

        $data = $IData[1];
        //获取测试所耗时间，有时间则填充 获取测试的其它信息
        $result = array(
            'StartTime'=>date('m月d日H:i',$data['record']['StartTime']),
            'EndTime'=>date('m月d日H:i',$data['record']['EndTime']),
            'LoadTime'=>date('m月d日H:i',$data['record']['LoadTime']),
            'UserName'=>$this->getUserInfo($data['record']['UserName'])['RealName'],
            'Message'=>$data['record']['Message'],
            'SubjectName'=>$data['record']['SubjectName'],
            'DoTime'=>$data['record']['DoTime'],
        );


        //遍历此次测试的所有试题和之前作答信息
        $testArray = [];//用于ajax返回的试题数据数组
        foreach ($data['test'] as $i => $k) {
            $testArray[$i]['test_id'] = $k['TestID'];

            if(empty($ifShowID)){
                $testArray[$i]['test_title'] = $k['Test']['title'];
            }

            $testArray[$i]['test_type'] = $k['TypesID'];
            $testArray[$i]['if_choose'] = $k['IfChoose'];
            $testArray[$i]['doc_id'] = $k['DocID'];
            //根据题型判断
            if ($k['IfChoose'] == 3) {//单选
                $testArray[$i]['answer_id'] = $sendID.'-'.$k['TestID'].'-'.$k['doAnswer'][0]['Number'].'-'.
                    $k['doAnswer'][0]['OrderID'].'-'.$k['IfChoose'];
                $testArray[$i]['number'] = $k['doAnswer'][0]['Number'];
                if(empty($ifShowID)){
                    $testArray[$i]['test_options'] = $k['Test']['options'];
                    $testArray[$i]['answer'] = $k['doAnswer'][0]['AnswerText'];
                    $testArray[$i]['right_answer'] = strip_tags($k['Answer']);
                }
            }elseif($k['IfChoose'] == 2){//多选
                $testArray[$i]['answer_id'] = $sendID.'-'.$k['TestID'].'-'.$k['doAnswer'][0]['Number'].'-'.
                    $k['doAnswer'][0]['OrderID'].'-'.$k['IfChoose'];
                $testArray[$i]['number'] = $k['doAnswer'][0]['Number'];
                if(empty($ifShowID)){
                    $testArray[$i]['test_options'] = $k['Test']['options'];
                    $testArray[$i]['answer'] = explode(',',$k['doAnswer'][0]['AnswerText']);
                    $testArray[$i]['right_answer'] = strip_tags($k['Answer']);
                }
            } elseif ($k['IfChoose'] == 1) {//复合题
                $testArray[$i]['number'] = $k['doAnswer'][1]['Number'];
                $rightAnswerOne = $this->getModel('Test')->xtnum($k['Answer'], 3);//分隔正确答案
                foreach ($k['Test']['sub'] as $j => $m) {
                    //$k['Test']['sub']是从1开始
                    $answerArray = $k['doAnswer'][$j];
                    $testArray[$i]['sub'][$j]['answer_id'] = $sendID.'-'.$k['TestID'].'-'.$answerArray['Number'].'-'.
                        $answerArray['OrderID'].'-'.$answerArray['IfChoose'];
                    $testArray[$i]['sub'][$j]['order'] = $answerArray['OrderID'];
                    $testArray[$i]['sub'][$j]['if_choose'] = $answerArray['IfChoose'];

                    if(empty($ifShowID)){
                        $testArray[$i]['sub'][$j]['sub_title'] = $m['title']?$m['title']:'请作答：';
                        if ($answerArray['IfChoose'] == 3) {//复合题中单选
                            $testArray[$i]['sub'][$j]['sub_options'] = $m['options'];
                            $testArray[$i]['sub'][$j]['sub_answer'] = $answerArray['AnswerText'];
                            $testArray[$i]['sub'][$j]['sub_right_answer'] = strip_tags($rightAnswerOne[$j]);
                        }elseif($answerArray['IfChoose'] == 2){//复合体中多选
                            $testArray[$i]['sub'][$j]['sub_options'] = $m['options'];
                            $testArray[$i]['sub'][$j]['sub_answer'] = explode(',',$answerArray['AnswerText']);
                            $testArray[$i]['sub'][$j]['sub_right_answer'] = strip_tags($rightAnswerOne[$j]);
                        } else {//复合题中大题
                            $answerImageArray = $this->processAppUserAnswer('explode',formatString('IPReturn',$answerArray['AnswerText']));
                            $testArray[$i]['sub'][$j]['sub_answer'] = $answerImageArray[0];
                            $testArray[$i]['sub'][$j]['subAnswerImage'] = $answerImageArray[1];
                            $testArray[$i]['sub'][$j]['sub_right_answer'] = $rightAnswerOne[$j];
                        }
                    }
                }
            } else {
                //其它
                $testArray[$i]['number'] = $k['doAnswer'][0]['Number'];
                $testArray[$i]['answer_id'] = $sendID.'-'.$k['TestID'].'-'.$k['doAnswer'][0]['Number'].'-'.
                    $k['doAnswer'][0]['OrderID'].'-'.$k['IfChoose'];

                if(empty($ifShowID)){
                    $answerImageArray = $this->processAppUserAnswer('explode',formatString('IPReturn',$k['doAnswer'][0]['AnswerText']));
                    $testArray[$i]['answer'] = $answerImageArray[0];
                    $testArray[$i]['answerImage'] = $answerImageArray[1];
                    $testArray[$i]['right_answer'] = $k['Answer'];
                }
            }
        }
        //因为数据时索引中获取的，所以要对数据排序：对testArray进行排序 按照Number字段排序（索引）
        foreach ($testArray as $i => $k) {
            $numberOrder[$i] = (int)$k['number'];
        }
        array_multisort($numberOrder, SORT_ASC, $testArray);
        //排序后再生成Type，以防止题型顺序破坏试题的顺序,如果是乱序，题型顺序还按照正常的题型顺序
        $type = [];//此次测试试题中的题型
        $typeAmount = [];//记录题型下的试题数量
        foreach($testArray as $k){
            array_push($type,$k['test_type']);
            $typeAmount[$k['test_type']]++;
        }
        //如果是乱序做题，则需要重排数据
        if($data['record']['WorkOrder']){
            shuffle($testArray);
        }

        //测试使用：获取试题的正确答案------------------------------------------------------------------------------------
        if(C('AAT_LOAD_RIGHT_ANSWER')){
            $rightAnswer = [];
            foreach($testArray as $iTestArray){
                //遍历出试题答案
                $rightAnswer[] = ['number'=>$iTestArray['number'],'right_answer'=>$iTestArray['right_answer']];
            }
            $result['right_answer'] = $rightAnswer;
        }
        //测试题型
        $type = array_unique($type);
        $typeCache = SS('types');
        foreach ($type as $j => $m) {
            $result['type'][$j]['TypesName'] = $typeCache[$m]['TypesName']?$typeCache[$m]['TypesName']:'其它';
            $result['type'][$j]['TypesID'] = $m;
            $result['type'][$j]['TypesAmount'] = $typeAmount[$m];//题型下的试题数量
        }
        $result['test'] = $testArray;
        $this->setBack($result);
    }

    /**
     * 描述：获取导学案作业的内容数据
     * @param int sendID 导学案ID，在作业列表中有 true
     * @param int ifAnswer 是否为看答案接口 0 做题练习 1看答案解析 true
     * @return array
     * [
     * "data"=>[
     *   "tempName"=>"沁园春$!$长沙（课时2）", //导学案名称
     *   "tempDesc"=>"班级:__________姓名:__________设计人:__________日期:__________", //导学案描述 
     *   "message"=>"",//本次作业的备注  
     *   "workName"=>"沁园春·长沙（课时2）", //作业名称
     *   "subjectName"=>null, //当前作业的学科名称 
     *   "endTime"=>"12-31 23:59", //本次作业结束时间，日期字符串 
     *   "doTime"=>null, //做题时间，单位秒，为null时或者0时表示之前没有作答，从0开始计时
     *   "checkTime"=>"01-01 08:00", //老师检查时间，空字符串或者具体日期 
     *   "score"=>0, //本次作业的成绩 
     *   "forum"=>[
     *       "tempForum1"=>[导学案 预习案 
     *           "title"=>"课前预习", 
     *           "subTitle"=>"预习案", 
     *           "menu"=>[
     *               [
     *                   "title"=>"学习目标", 
     *                   "isTest"=>"0", 
     *                   "content"=>[
     *                       [
     *                           "loreID"=>"349", 
     *                           "lore"=>"仔细探讨品味诗歌语言，把握其精练、准确的特点</p>", 
     *                           "loreAnswer"=>"", 
     *                           "number"=>1
     *                       ],
     *                       ...
     *                   ]
     *               ], 
     *       ]
     *   ], 
     *   "ifAnswer"=>"0"
     * ], 
     * ... 
     * "info"=>"success", 
     * "status"=>1
     * ]
     * @author demo 
     */
    public function getCaseContent() {
        $this->checkRequest();
        $sendID = $_REQUEST['sendID'];
        $ifAnswer = $_REQUEST['ifAnswer'];
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Exercise/homeworkCaseGetTest', $sendID,$ifAnswer,$userID,$isApp=true);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        $this->setError($IData[1], 1);
    }

    /**
     * 判断单选和多选的题目正确性，插入AnswerRecord表答题记录，更新SendWork表信息
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
        $sendID = $_REQUEST['send_id'];
        $doTime = $_REQUEST['do_time'];
        $userID = $this->getUserID();
        $realName = $this->getRealName();
        $IData = $this->getApiAat('Exercise/homeworkSubmit', $sendID,$doTime,$userID,$realName);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        $this->setError($IData[1], 1);
    }

    /**
     * 处理每次点击试题答案时提交的数据
     * @param string|array answer 单选：“A”,多选：数组“[A,B]“，非选择：”文字” true
     * @param array answerImage 用户上传图片地址，必须是数组，一张图片也必须是数组 false
     * @param string answer_id 答案ID  true
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
        $answerID = $_POST['answer_id'];
        //处理answer字段，兼容数组和非数组，非数组多选题，逗号分割
        $oldAnswer = $_POST['answer'];
        $answerText = is_array($oldAnswer)?trim(implode(',', $oldAnswer)):trim($oldAnswer);
        $imageArr = $_POST['answerImage'];
        if($imageArr&&is_array($imageArr)){
            $answerText = $this->processAppUserAnswer('implode',$answerText,$imageArr);
        }
        $IData = $this->getApiAat('Exercise/homeworkDoAnswer', $answerID,$answerText);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        $this->setError($IData[1], 1);
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
        $sendID = $_POST['send_id'];
        $doTime = $_POST['do_time'];//答题时间
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Exercise/homeworkClose', $sendID,$doTime,$userID);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        $this->setError($IData[1], 1);
    }
}