<?php
/**
 * @author demo
 * @date 2016年11月22日
 */
/**
 * 获取答题卡格式输出类
 */
namespace Home\Model;
class SheetFormatModel extends BaseModel{
    const SHEET_DISPLAY = 1;//是否显示
    const SHEET_TYPE = 1;//顶部类型
    const SHEET_NUM_LENGTH = 13;//准考证号长度
    const SHEET_CHOOSE_TYPE = '第I卷';//
    const SHEET_CHOOSE_NOTICE = '（请用2B铅笔填涂）';//I卷注意事项
    const SHEET_CHOOSE_DISPLAY = 1;//客观题display
    const SHEET_CHOOSE_STYLE = 0;//客观题style 0横排 1竖排
    const SHEET_SUBJECT_TYPE = '第II卷';//顶部类型
    const SHEET_SUBJECT_NOTICE = '（请用黑色签字笔作答）';//II卷注意事项
    const SHEET_A3_WIDTH = 525;//A3纸最大宽度
    const SHEET_A4_WIDTH = 794;//A4纸最大宽度
    const SHEET_CARE_CONTENT = '1．答题前，请将姓名、班级、考场、座位号、准考证号填写清楚。
2．客观题答题，必须使用2B铅笔填涂，修改时用橡皮擦干净。
3．主观题使用黑色签字笔书写。
4．必须在题号所对应的答题区域内作答，超出答题区域书写无效。';//注意事项
    //文综数组
    private $heddleArr = [
                        18=>'政治',
                        19=>'历史',
                        20=>'地理',
                        28=>'政治',
                        29=>'历史',
                        30=>'地理'
                    ];
    //理综数组
    private $danielArr = [
                        15=>'物理',
                        16=>'化学',
                        17=>'生物',
                        25=>'物理',
                        26=>'化学',
                        27=>'生物'
                    ];
    private $style = [
                1=>'文科',
                2=>'理科',
                3=>'文理通用'
            ];
    //test逻辑层
    private $testLogic;
    //显示分数
    private $showScore = 0; //显示分数
    //客观题类型
    private $showChooseStyle = 0; //客观题显示类型横竖排
    //当前版式宽度
    private $thisPaperWidth = 525;
    //试卷开始题号
    private $thisTestOrder = 1;
    //操作的错误信息
    public $error = '';


    /**
     * 获取试卷字符串
     * @param array  $param 文档信息
     * @author demo
     */
    public function getSheetFormat($param){
        //查找对应试卷的字符串
        $cookieArr = R('Common/DocLayer/formatPaperCookie',array($param['CookieStr']));
        $testStr = R('Common/DocLayer/explodeCookieStr',array($param['CookieStr']));

        $this->getTestLogic();

        //获取试卷内试题属性
        $testArray=$this->getTestInfoData($testStr);
        if($this->getError()) return [0,$this->error];

        //获取试卷学科类型
        $subjectData = $this->getSheetHeader($testArray);
        if($this->getError()) return [0,$this->error];

        //获取试卷属性
        $data = $this->getPaperAttr($cookieArr,$subjectData);

        //试卷页面宽度
        if($param['SheetChoice']){//板式
            $data['layout']['style']=$param['SheetChoice'];
        }
        $this->setLayOut($data['layout']['style']);

        //第一卷信息
        $test1Arr = $cookieArr['parthead'][0]['questypehead'];
        if($test1Arr){
            $paperArr['title'] = static::SHEET_CHOOSE_TYPE;
            $paperArr['desc'] = static::SHEET_CHOOSE_NOTICE;
            $testData = $this->getSheetPaper($test1Arr,$testArray);
            $paperArr['list'] = $testData;
            $buffer[0] = $paperArr;
        }

        //第二卷信息
        $test2Arr = $cookieArr['parthead'][1]['questypehead'];
        if($test2Arr){
            $paper2Arr['title'] = static::SHEET_SUBJECT_TYPE;
            $paper2Arr['desc'] = static::SHEET_SUBJECT_NOTICE;
            $testData2 = $this->getSheetPaper($test2Arr,$testArray);
            if($testData2[0]['type'] == 0 && !empty($testData2[0]['content'])){//二卷的客观题
                $content = $testData2[0];
                unset($testData2[0]);
            }
            $testData2 = array_values($testData2);
            $paper2Arr['list'] = $testData2;
            $buffer[1] = $paper2Arr;
        }

        if($content){//所有客观题放在一卷
            if(empty($buffer[0]['list'][0]['content'])){
                $buffer[0]['list'][0]=$content;
            }else{
                $buffer[0]['list'][0]['content'] = array_merge($buffer[0]['list'][0]['content'],$content['content']);
            }
        }else if(empty($buffer[0]['list'][0]['content'])){
            unset($buffer[0]['list'][0]);
            array_values($buffer[0]['list']);
        }

        $data['paper'] = $buffer;
        return [1,$data];
    }

    /**
     * 设定当前版式
     * @return null
     */
    private function setLayOut($sheetChoice){
        $width = static::SHEET_A3_WIDTH;
        //试卷页面宽度
        if($sheetChoice == 'A4') {
            $width = static::SHEET_A4_WIDTH;
        }
        $this->thisPaperWidth=$width;
    }

    /**
     * 返回产生的错误信息
     * @return string
     */
    private function getError(){
        return $this->error;
    }

    /**
     * 获取客观题类型
     * @return string
     */
    public function getChooseStyle($showChooseStyle){
        $this->showChooseStyle=static::SHEET_CHOOSE_STYLE;
        if($showChooseStyle) $this->showChooseStyle=$showChooseStyle;
    }

    /**
     * 更改数据对应客观题排列方向 如果是a卷返回b卷
     * @param array $arr 答题卡格式数据集
     * @param int $value 客观题横排竖排 0不改变 1改变为b卷
     * @return string
     */
    public function changeChooseStyleByArray($arr,$value=0){
        $old=$arr['paper'][0]['list'][0]['style'];
        if($value==1){
            $old=($old==1?0:1);
        }
        $arr['paper'][0]['list'][0]['style']=$old;
        return $arr;
    }

    /**
     * 获取试卷属性信息
     * @param array  $cookieArr  试卷字符串信息
     * @param string  $subjectInfo  试卷学科属性
     * @return array $data 试卷属性
     * @author demo
     */
    private function getPaperAttr($cookieArr,$subjectInfo){
        $data = [];
        $data['top'] = ['display'=>static::SHEET_DISPLAY,'content'=>$cookieArr['studentinput'][2]];
        $data['title'] = ['display'=>static::SHEET_DISPLAY,'content'=>$cookieArr['maintitle'][2]];
        $data['sub']['display'] = static::SHEET_DISPLAY;
        $data['sub']['content'] = $subjectInfo['SubjectName'].'答题卡';
        $data['layout']['style'] = $subjectInfo['LayOut'];
        $data['type']['display'] = static::SHEET_TYPE;
        $data['code']['display'] = static::SHEET_DISPLAY;
        $data['num'] = ['display'=>static::SHEET_DISPLAY,'length'=>static::SHEET_NUM_LENGTH];
        $data['care'] = ['display'=>static::SHEET_DISPLAY,'content'=>static::SHEET_CARE_CONTENT];
        $data['miss']['display'] = static::SHEET_DISPLAY;//缺考
        $data['score']['display'] = $this->showScore;

        return $data;
    }

    /**
     * 获取试题属性信息
     * @param array  $testPaperArray  I/II卷面信息
     * @param array  $testArray  试题信息
     * @return array $buffer 组合后的卷面信息
     * @author demo
     */
    private function getSheetPaper($testPaperArray,$testArray){
        $typesArray = SS('types');
        $order=$this->thisTestOrder; //试题开始序号

        $k=1;//答题卡题型列表序号
        foreach($testPaperArray as $i=>$iTestPaperArray){
            $list[$k] = ['title'=>$iTestPaperArray[2],'display'=>1];
            foreach($iTestPaperArray[5] as $j=>$jTestPaperArray){
                $desc=''; //题文描述如果存在则行数为0 需要做特殊处理
                $testID=$jTestPaperArray[0];
                $testNum=$jTestPaperArray[1];
                $score=(float)$jTestPaperArray[2];
                $chooseNum=$jTestPaperArray[3];
                $chooseDo=$jTestPaperArray[4];

                //获取类型缓存
                $typeData = $typesArray[$testArray[$testID]['typesid']];
                if($typeData['CardIfGetTest'] == 1){
                    $desc = $testArray[$testID]['testold'];
                }

                if($chooseNum>0){//选做题
                    if($list[$k-1]['type']==2 && $list[$k-1]['num']==$chooseNum){
                        $k=$k-1;
                    }
                    if($list[$k]['type']!=2 || $list[$k]['num']!=$chooseNum){
                        if(!empty($list[$k]['content'])) $k++; //如果已经有数据则进入下一个
                        $list[$k]= ['title'=>$iTestPaperArray[2],'display'=>0,'num'=>$chooseNum,'type'=>2,'do'=>$chooseDo];
                    }
                    $hline = $this->getAnswerHline($testArray[$testID]['answerold'],$score);
                    $list[$k]['content'][]=[
                        'order'=>$order++,
                        'small'=>0,
                        'uline'=>$typeData['Underline'],
                        'hline'=>$hline,
                        'kong'=>1,
                        'score'=>$score,
                        'img'=>[],
                        'desc'=>''
                    ];
                    continue;
                }else{
                    //从选做题到非选做题需要自加K
                    if($list[$k]['type']==2){
                        $k++;
                        $list[$k]= ['title'=>$iTestPaperArray[2],'display'=>1];
                    }
                }

                if(strstr($typeData['TypesName'], '作文')){//作文类型
                    $list[$k]['type'] = 3;
                    $list[$k]['content'][] = ['order'=>$order++,'char'=>800,'score'=>$score,'desc'=>''];
                    continue;
                }else if(strstr($typeData['TypesName'], '书面表达')){
                    $list[$k]['type'] = 4;
                    $hline = $this->getAnswerHline($testArray[$testID]['answerold'],$score);
                    $list[$k]['content'][] = ['order'=>$order++,'uline'=>(float)$typeData['Underline'],'hline'=>$hline,'score'=>$score,'desc'=>''];
                    continue;
                }

                if($testArray[$testID]['teststyle'] == '1'){//客观题
                    $list[0]['title'] = '选择题';
                    $list[0]['type'] = 0;
                    $list[0]['display'] = 0;
                    $list[0]['style'] = $this->showChooseStyle;//板式
                    if($testNum>1){//有小题
                        $optionArr = explode(',',$testArray[$testID]['optionnum']);
                        for($n=0;$n<$testNum;$n++){
                            $option=(int)$optionArr[$n];
                            if(empty($option)) $option=$optionArr[0];
                            $list[0]['content'][] = ['style'=>1,'order'=>$order++,'small'=>0,'num'=>$option];
                        }
                    }else{//无小题
                        $list[0]['content'][] = ['style'=>1,'order'=>$order++,'small'=>0,'num'=>(int)$testArray[$testID]['optionnum']];
                    }
                }else if($testArray[$testID]['teststyle'] =='3'){//问答题
                    $list[$k]['type'] = 1;
                    if($testNum>1){//有小题
                        $answerArr = $this->cutMoreTest($testArray[$testID]['answerold']);
                        for($m=0;$m<$testNum;$m++){
                            if(is_array($jTestArr[2])){
                                $score = $jTestArr[2][$m];
                            }
                            $hline = $this->getAnswerHline($answerArr[$m],$score);
                            $list[$k]['content'][] = ['order'=>$order++,'small'=>0,'uline'=>(float)$typeData['Underline'],'kong'=>1,'hline'=>$hline,'score'=>(float)$score];
                        }
                    }else{//没有步骤
                        if($desc){
                            $list[$k]['content'][] = ['order'=>$order++,'small'=>0,'uline'=>(float)$typeData['Underline'],'kong'=>0,'hline'=>0,'score'=>(float)$score,'desc'=>$desc];
                        }else{
                            $hline = $this->getAnswerHline($testArray[$testID]['answerold'],$score);
                            $list[$k]['content'][] = ['order'=>$order++,'small'=>0,'uline'=>(float)$typeData['Underline'],'kong'=>1,'hline'=>$hline,'score'=>(float)$score];
                        }
                    }
                }else if($testArray[$testID]['teststyle'] =='2'){//混合题型
                    $answerArr = $this->cutMoreTest($testArray[$testID]['answerold']);
                    $optionArr = explode(',',$testArray[$testID]['optionnum']);
                    foreach($testArray[$testID]['judge'] as $l=>$lSmallChoose){
                        if($lSmallChoose['IfChoose'] >= 2){//选择题
                            $list[0]['title'] = '选择题';
                            $list[0]['type'] = 0;
                            $list[0]['display'] = 0;
                            $list[0]['style'] = $this->showChooseStyle;//板式
                            $option=(int)$optionArr[$l];
                            if(empty($option)) $option=$optionArr[0];
                            $list[0]['content'][] = ['style'=>1,'order'=>$order++,'small'=>0,'num'=>$option];
                        }else{
                            $list[$k]['type'] = 1;
                            $hline = $this->getAnswerHline($answerArr[$lSmallChoose['OrderID']-1],(float)$jTestArr[2][$lSmallChoose['OrderID']-1]);
                            $list[$k]['content'][] = ['order'=>$order++,'small'=>0,'uline'=>(float)$typeData['Underline'],'kong'=>1,'hline'=>$hline,'score'=>(float)$jTestArr[2][$lSmallChoose['OrderID']-1]];
                        }
                    }
                }
            }
            $k++; //更新题型序号
        }
        $buffer=array();
        $buffer[0] ='';
        foreach($list as $iList){//过滤没有题的
            if(empty($iList['content']) || empty($iList)){
                continue;
            }
            if($iList['type']==0){
                $buffer[0]=$iList;
                continue;
            }
            $buffer[] = $iList;
        }
        $this->thisTestOrder=$order;
        $buffer=array_filter($buffer);
        $buffer=array_values($buffer);
        return $buffer;
    }

    /**
     * 获取试题基本属性信息
     * @param string  $testStr  试题列表
     * @return array $data 试题基本属性细信息
     * @author demo
     */
    private function getTestInfoData($testStr){
        //获取试题数据
        $field=array('testid','testold','answerold','analyticold','subjectid','typesid','judge','teststyle','optionnum');
        $where=array('TestID'=>$testStr);
        $order=array();
        $page=array('page'=>1,'perpage'=>100,'limit'=>100);

        $tmpStr=$this->getModel('TestRealQuery')->getIndexTest($field,$where,$order,$page,0,2);
        if(empty($tmpStr)){
             $this->error='试题数据不存在！';
             return;
        }

        //调整试题的顺序
        $testArray=explode(',',$testStr);
        $output=array();
        foreach($testArray as $i=>$iTestArray){
            $output[$iTestArray]=$tmpStr[$iTestArray];
        }

        return $output;
    }

    /**
     * 获取试题行数
     * @param str  $answer  试题答案
     * @return int/float $hline 答案所占行高
     * @author demo
     */
    private function getAnswerHline($answer,$score){
        $width=$this->thisPaperWidth;
        $score = '（'.$score.'分）';
        $ansLen = $this->getAnswerLength($answer);//答案长度
        $scLen = 0;//分值长度
        if($this->showScore) $scLen = $this->getAnswerLength('(60分)');
        $titLen = $this->getAnswerLength('11(1)');//小题序号

        //检测是否有（1）数据来增加答案行数
        preg_match_all('/[\(（][0-9]+[\)）]/i',$answer,$arr);
        $hlineMore=0;
        if($arr && $arr[0]){
            $hlineMore=count($arr[0]);
        }

        $len = $ansLen+$titLen+$scLen;
        // 默认行数
        $hline = 3;
        if($len){
            $height = $len/$width;
            if($height<=0.33){
                $hline = 0.3;
            }else if(0.33<$height && $height<=0.5){
                $hline = 0.5;
            }else if(0.5<$height && $height<=1){
                $hline = 1;
            }else{
                $hline = ceil($height);
            }
        }
        $hline+=$hlineMore;
        $hline = floor($hline/5)+$hline;
        return $hline;
    }

    /**
     * 计算答案宽度
     * @author demo
     */
    private function getAnswerLength($tt){
        return $this->testLogic->calcSingleOptionWidth($tt);
    }
    /**
     * 获取字符串中英文和中文长度；
     * @param string $str 字符串
     * @return array
     * @author demo
     */
    private function strLength($str){
        $kb=explode('&nbsp;',$str);
        $str = preg_replace('/\&nbsp\;/','',$str);
        $length = strlen(preg_replace('/[\x00-\x7F]/', '', $str));
        $arr['en'] = strlen($str) - $length+count($kb)-1;
        $arr['cn'] = intval($length / 3);
        return $arr;
    }

    /**
     * 获取Test逻辑层
     * @param array $request
     */
    private function getTestLogic(){
        if(!isset($this->testLogic)) $this->testLogic = $this->getModel('Test');
    }


    /**
     * 切割带小题的数据
     * @param array  $str 答案数据
     * @return array
     * @author demo
     */
    private function cutMoreTest($str){
        $this->getTestLogic();
        $result=$this->testLogic->xtnum($str,3);
        unset($result[0]);
        return array_values($result);
    }

    /**
     * 获取试卷头答题卡标题信息
     * @param array  $testArray  试题数组
     * @return array [0,试卷学科不存在] [1,试卷板式，学科名称]
     * @author demo
     */
    private function getSheetHeader($testArray){
        foreach($testArray as $iTestArray){
            $subject[]=$iTestArray['subjectid'];
        }
        $subject = array_values(array_filter(array_unique($subject)));

        if(empty($subject)){
            $this->error = '试卷学科属性不存在';
            return;
        }
        $subjectCache=SS('subject');
        if(count($subject) == 1){//单一学科
            $info = $subjectCache[$subject[0]];
            $data['SubjectName']= $info['SubjectName'];
            $data['LayOut']= $info['Layout'];
        }else{
            $data['LayOut'] = 'A3';
            //判断文综
            $flag = 1;
            foreach ($subject as $iSubject) {
                if ($this->heddleArr[$iSubject]) {
                    continue;
                }else {
                    $flag = 0;
                    break;
                }
            }
            //判断理综
            $sign = 1;
            foreach ($subject as $iSubject) {
                if ($this->danielArr[$iSubject]) {
                    continue;
                }else {
                    $sign = 0;
                    break;
                }
            }
            if($flag){
                $data['SubjectName'] = '文综';
            }elseif($sign){
                $data['SubjectName'] = '理综';
            }
        }
        if(empty($data['SubjectName'])){
            $this->error = '答题卡学科类型不合法';
            return;
        }
        return $data;
    }
}