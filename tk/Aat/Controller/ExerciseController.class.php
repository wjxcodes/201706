<?php

/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-3-21
 * Time: 下午2:20
 */
namespace Aat\Controller;
class ExerciseController extends BaseController
{
    protected $userAnswerRecordModel;
    protected $testRealModel;
    protected $testJudgeModel;

    public function _initialize() {
        //增加权限检查在这里或者在BaseController中做检查
//        $this->userAnswerRecordModel = $this->getModel('UserAnswerRecord');
//        $this->testRealModel = $this->getModel('TestReal');
//        $this->testJudgeModel = $this->getModel('TestJudge');
    }

    /**
     * 显示练习页面
     * @author demo
     */
    public function index() {
        $recordID = $_REQUEST['id'];
        $check = $this->getApiAat('Base/checkExerciseRID',$recordID,$ifDone = false,$this->getUserName());
        if ($check===false) {
            //检测不通过
            $this->redirect('ExerciseReport/index', ['id' => $recordID]);
        }
        //渲染页面
        $this->assign([
            'pageName'=>'试题作答',
            'record_id'=>$recordID
        ]);
        $this->display();
    }

    /**
     * 验证订单号
     * @author demo 16-4-26
     */
    public function veirfyOrderNo(){
        $no = (int)$_POST['no'];
        // $result = $this->getModel('UserDocInviteCode')->useInviteCode($no, $this->getUserID());
        $result = $this->getApiTest('Test/selectData', 'NumbID,TypesID','DocID='.$attrBuffer['DocID'],'NumbID ASC','','TestAttrReal');
        if($result !== ''){
            // $this->ajaxReturn(null, $result, 0);
            return $this->setBack($result);
        }
        // $this->ajaxReturn(null, 'success', 1);
         $this->setBack('success');
    }

    /**
     * 专题练习
     * @author demo
     */
    public function topic(){
        $tpid = (int)$_REQUEST['topicPaperID'];
        $subID = (int)$_REQUEST['subID'];
        $this->setCookieSUBJECTID($subID);
        $params = trim(strrchr($_SERVER['REQUEST_URI'],'?'),'?');
        $username = $this->getUserName();
        $userID = $this->getUserID();

        $param = array('topicPaperID' => $tpid);

        //判断是否测试过如果测试过则直接转入
        $recordIDIData=$this->getModel('UserTestRecordAttr')->getIfDoTopic($tpid,$username);
        if(empty($recordIDIData)) $recordIDIData = $this->getApiAat('PushTest/pushTest',$userID,$username,8,$subID,$param);
        else $recordIDIData=[1,$recordIDIData];

        //返回跳转页面的数据
        if($recordIDIData[0]==0){
            // $this->ajaxReturn(null,$recordIDIData[1],0);
           exit($recordIDIData[1].'<a href="/Aat">返回首页</a>');
        }
        if($params=='/') $params=array('id'=>$recordIDIData[1]);
        else{
            $newParams=array('id'=>$recordIDIData[1]);
            $arr=explode('&',$params);
            foreach($arr as $iArr){
                $jArr=explode('=',$iArr);
                $newParams[$jArr[0]]=$jArr[1];
            }
            $params=$newParams;
        }
        header('Location:'.U('Aat/Exercise/index',$params));
    }

    /**
     * 获取测试题目信息
     * @author demo
     */
    public function getTest(){
        $this->checkRequest();
        $testRecordID = $_POST['id'];
        $username = $this->getUserName();
        $IData =$this->getApiAat('Exercise/getTest',$testRecordID,$username);
        if($IData[0]==0){
            // $this->ajaxReturn(null,$IData[1],0);
            $this->setError($IData[1],1);
        }
        $noDo=C('WLN_TYPE_FILTER');
        //遍历此次测试的所有试题和之前作答信息

        $testArray = [];
        $IData = $IData[1];
        $type = array(); //试题类型
        $typeNum = array(); //试题类型对应试题的数量
        foreach ($IData['test'] as $i => $k) {
            $testArray[$i]['score'] = $k['score'];
            $testArray[$i]['testNum'] = $k['TestNum'];
            $testArray[$i]['test_id'] = $k['TestID'];
            $testArray[$i]['test_title'] = $k['Test']['title'];
            $testArray[$i]['test_type'] = $k['TypesID'];
            $testArray[$i]['if_choose'] = $k['IfChoose'];
            $type[] = $k['TypesID'];
            $typeNum[] = $k['TestNum'];
            $testArray[$i]['doc_id'] = $k['DocID'];
            //$testArray[$i]['right_answer'] = strip_tags($k['Answer']);
            $testArray[$i]['ifCanDo']=1;
            if(strpos($noDo[$k['SubjectID']],$k['TypesID'])!==false && $IData['record']['AatTestStyle']!=1){
                $testArray[$i]['ifCanDo']=0;
            }
            //根据题型判断
            if ($k['IfChoose'] == 3) {
                //单选
                $testArray[$i]['answer_id'] = $testRecordID.'-'.$k['TestID'].'-'.$k['doAnswer'][0]['Number'].'-'.$k['doAnswer'][0]['OrderID'].'-'.$k['IfChoose'];
                $testArray[$i]['number'] = $k['doAnswer'][0]['Number'];
                $testArray[$i]['test_options'] = $k['Test']['options'];
                $testArray[$i]['answer'] = $k['doAnswer'][0]['AnswerText'];
            }elseif($k['IfChoose'] == 2){
                //多选
                $testArray[$i]['answer_id'] = $testRecordID.'-'.$k['TestID'].'-'.$k['doAnswer'][0]['Number'].'-'.$k['doAnswer'][0]['OrderID'].'-'.$k['IfChoose'];
                $testArray[$i]['number'] = $k['doAnswer'][0]['Number'];
                $testArray[$i]['test_options'] = $k['Test']['options'];
                $testArray[$i]['answer'] = explode(',',$k['doAnswer'][0]['AnswerText']);
            } elseif ($k['IfChoose'] == 1) {
                //复合题
                $testArray[$i]['number'] = $k['doAnswer'][1]['Number'];
                foreach ($k['Test']['sub'] as $j => $m) {
                    //$k['Test']['sub']是从1开始
                    $answerRecord = $k['doAnswer'][$j];
                    $testArray[$i]['sub'][$j]['answer_id'] = $testRecordID.'-'.$k['TestID'].'-'.$answerRecord['Number'].'-'.$answerRecord['OrderID'].'-'.$answerRecord['IfChoose'];
                    $testArray[$i]['sub'][$j]['order'] = $answerRecord['OrderID'];
                    $testArray[$i]['sub'][$j]['sub_title'] = $m['title']?$m['title']:'请作答：';
                    $testArray[$i]['sub'][$j]['if_choose'] = $answerRecord['IfChoose'];
                    if ($answerRecord['IfChoose'] == 3) {
                        //复合题中单选
                        $testArray[$i]['sub'][$j]['sub_options'] = $m['options'];
                        $testArray[$i]['sub'][$j]['sub_answer'] = $answerRecord['AnswerText'];
                    }elseif($answerRecord['IfChoose'] == 2){
                        //复合体中多选
                        $testArray[$i]['sub'][$j]['sub_options'] = $m['options'];
                        $testArray[$i]['sub'][$j]['sub_answer'] = explode(',',$answerRecord['AnswerText']);
                    } else {
                        //复合题中大题
                        $testArray[$i]['sub'][$j]['sub_answer'] = formatString('IPReturn',stripslashes($answerRecord['AnswerText']));
                    }
                }
            } else {
                //其它
                $testArray[$i]['number'] = $k['doAnswer'][0]['Number'];
                $testArray[$i]['answer_id'] = $testRecordID.'-'.$k['TestID'].'-'.$k['doAnswer'][0]['Number'].'-'.$k['doAnswer'][0]['OrderID'].'-'.$k['IfChoose'];
                $testArray[$i]['answer'] = formatString('IPReturn',stripslashes($k['doAnswer'][0]['AnswerText']));
                $testArray[$i]['right_answer'] = $k['Answer'];
            }
        }
        //因为数据时索引中获取的，所以要对数据排序：对testArray进行排序 按照Number字段排序（索引）
//        foreach ($testArray as $i => $k) {
//            $numberArr[$i] = $i;//$k['number'];
//        }
//        array_multisort($numberArr,SORT_ASC,$testArray);
        //排序后再生成Type，以防止题型顺序破坏试题的顺序
        $result = [];//需要返回的数据
        $typeTest = [];//临时题型-试题对应关系数组
        //测试使用：获取试题的正确答案
//        $rightAnswer = array();
//        foreach($testArray as $k){
//            //遍历出试题答案
//            $rightAnswer[] = array('number'=>$k['number'],'right_answer'=>$k['right_answer']);
//        }
//        $result['right_answer'] = $rightAnswer;
        //end获取试题的正确答案
        //获取题型顺序 如果试题以文档形式则优先按照文档结构展示
        $userAnswerRecordAttrModel = $this->getModel('UserTestRecordAttr');
        $attrBuffer=$userAnswerRecordAttrModel->findData('DocID','TestRecordID='.$testRecordID);
        $isWarp = ($attrBuffer['DocID']>0);
        if($isWarp){
            $type = array();
            $typeNum = array(); //试题类型对应试题的数量
            $testBuffer=$this->getModel('TestAttrReal')->selectData('NumbID,TestNum,TypesID','DocID='.$attrBuffer['DocID'],'NumbID ASC', '', 'TestAttrReal');
            foreach($testBuffer as $iTestBuffer){
                $type[]=$iTestBuffer['TypesID'];
                $typeNum[]=$iTestBuffer['TestNum'];
            }
        }
        $arr = array();
        foreach($type as $i=>$t){
            if(empty($typeNum[$i])) $typeNum[$i]=1;
            if(!isset($arr[$t])){
                $arr[$t] = $typeNum[$i];
            }else{
                $arr[$t]+=$typeNum[$i];
            }
        }
        $type = array_unique($type);

        //对不是整套试卷的题型排序 按照题型表顺序
        $typeCache = SS('types');
        if(!$isWarp){
            sort($type);
        }
        //$testArray = $this->getModel('UserTestRecord')->sequenceByType($testArray, $type);
        foreach($testArray as $k){
            $result['test'][] = $k;
        }
        foreach($type as $m){
            if(empty($typeCache[$m]['TypesName'])) continue;
            $result['type'][] = [
                'typeName'=>$typeCache[$m]['TypesName']?$typeCache[$m]['TypesName']:'其它',//测试题型名称
                'typeID'=>$m,
                'typeAmount'=>$arr[$m]
            ];
        }
        $styleCache = C('WLN_TEST_STYLE_NAME');
        $result['info'] = [
            'style'=>$styleCache[$IData['record']['Style']],//测试类型
            'time'=>$IData['record']['RealTime'],//获取测试所耗时间，有时间则填充
            'isContainHearing' => $IData['record']['IsContainHearing'], //是否已包含听力，包含听力则是一个url下载路径
            'aatTestStyle' => $IData['record']['AatTestStyle'], //试卷是否打分，1为打分，0为不打分
            'jumpUrl' => $IData['record']['jumpUrl'] //切换学科使用重定向
        ];
        if($IData['record']['topicName']){
            $result['info']['style'] = $IData['record']['topicName'];
        }
        // $this->ajaxReturn($result,'success',1);
         $this->setBack($result);
    }

    /**
     * 提交试卷处理
     * 判断单选和多选的题目正确性，插入zj_user_answer_record表答题记录，更新zj_user_test_record表信息
     * @author demo 5.5.27
     */
    public function doSubmit() {
        $this->checkRequest();
        $testRecordID = $_REQUEST['TestRecordID'];
        $realTime = $_REQUEST['RealTime'];
        $username = $this->getUserName();
        $testRecordIDIData = $this->getApiAat('Exercise/submit',$testRecordID,$realTime,$username);
        //跳转报告页面
        if( $testRecordIDIData[0] ==1){
            $this->setBack($testRecordIDIData[1]);
        }else{
            $this->setError($testRecordIDIData[1],1);
        }
        // $this->ajaxReturn($testRecordIDIData);
    }

    /**
     * 处理每次点击试题答案时提交的数据
     * @author demo
     */
    public function doAnswer() {
        $this->checkRequest();
        $answerID = $_REQUEST['answer_id'];
        //处理answer字段，兼容数组和非数组，非数组多选题，逗号分割@todo 以后统一
        $oldAnswer = $_REQUEST['answer'];
        if(is_array($oldAnswer)){
            $answerText = trim(implode(',', $oldAnswer));
        }else{
            $answerText = trim($oldAnswer);
        }
        $doTime = $_POST['RealTime']?$_POST['RealTime']:null;
        $IData = $this->getApiAat('Exercise/doAnswer',$answerID,$answerText,$doTime);
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }
        // $this->ajaxReturn($IData);
    }

    /**
     * 处理用户浏览器关闭或者刷新页面时提交的做题用时数据
     * @author demo
     */
    public function doClose() {
        $this->checkRequest();
        $testRecordID = $_REQUEST['TestRecordID'];
        $doTime = $_REQUEST['RealTime'];
        $username = $this->getUserName();
        $IData = $this->getApiAat('Exercise/close',$testRecordID,$doTime,$username);
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }
        // $this->ajaxReturn($IData);
    }

   /**
    * ajax纠错信息提交；
    * @author demo
    */
    public function correct(){
        $testID = $_POST['testID'];
        $content = $_POST['correctContent'];
        $typeID = $_POST['typeID'];
        $username = $this->getUserName();
        $IData = $this->getApiAat('Exercise/testFeedback',$testID,$typeID,$content,$username,$isApp=false);
        if($IData[0] ==1){
            $this->setBack($IData[1]);
        }else{
            $this->setError($IData[1],1);
        }
        // $this->ajaxReturn($IData);
    }
}