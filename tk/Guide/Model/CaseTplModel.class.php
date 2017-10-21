<?php
/**
 * @author demo
 * @date 15-5-4 上午11:28
 */
/**
 * 导学案模板类，用于导学案模板的操作
 */
namespace Guide\Model;
use Doc\Model\HandleWordModel;
class CaseTplModel extends HandleWordModel{

    /**
     * @覆盖父类方法。
     * @author demo 2015-12-18
     */
    public function insertData($data, $modelName=''){
        $result = parent::insertData($data,$modelName);
        if($result !== false && 2 == $data['IfSystem']){
            $this->getModel('StatisticsCounter')->increase('caseHomeWorkNum');
        }
        return $result;
    }

    /**
     * @覆盖父类方法。
     * @author demo
     */
    public function deleteData($where, $modelName=''){
        $data = (array)$this->selectData('IfSystem', $where);
        $num = 0;
        foreach($data as $value){
            if(2 == $value['IfSystem']){
                $num++;
            }
        }
        if($num > 0)
            $this->getModel('StatisticsCounter')->increase('caseHomeWorkNum', $num*-1);
        return parent::deleteData($where, $modelName);
    }

    /**
     * 导学案模板内容数组格式化为字符串
     * @param $content array 模板内容数组
     * @return string
     * @author demo
     */
    public function formatContent($content){
        $menuTmp=array();  //栏目数据数组
        $forumOrder=1;//板块序号
        $splitOrder=0;//分割序号
        foreach($content as $i=>$iContent){
            if(!is_array($iContent)){
                $menuTmp[$splitOrder]=$i.'@$@'.$iContent;
            }else{
                foreach($iContent as $j=>$jContent){
                    $menuTmp[$splitOrder]=$j;
                    foreach($jContent as $k=>$kContent){
                        if(!is_array($kContent)){
                            $menuTmp[$splitOrder].='@$@'.$kContent;
                        }else{
                            foreach($kContent as $n=>$nContent){
                                $splitOrder++;
                                $menuTmp[$splitOrder].=$nContent['menuOrder'].'@$@'.$nContent['menuName'].'@$@'.$nContent['menuContent'].'@$@'.$nContent['ifTest'].'|'.$nContent['ifAnswer'].'|'.$nContent['menuID'];
                            }
                        }
                        $forumOrder++;
                    }
                    $splitOrder++;
                }
            }
            $splitOrder++;
        }
        return join('@#@',$menuTmp);
    }
    /**
     * 根据POST过来的导学案第二步的JSON数据获取对应知识点及试题ID
     * @param array $content json数据
     * @return string
     * @author demo
     */
    public function reSetTestIdAndLoreId($content){
        $forum=$content['forum'];
        $testIdArr=array();
        foreach($forum as $i=>$iForum){
            foreach($iForum[2] as $j=>$jForum){
                if($jForum['ifTest']==0){  //知识
                    $testArr=explode(';',$jForum['menuContent']);
                    foreach($testArr as $k=>$kTestArr){
                        $testMsg=explode('|',$kTestArr);
                        //判断ID是否存在
                        if(!empty($testMsg[0])){
                            if($testMsg[1]==0){
                                $testIdArr[]='l'.$testMsg[0];
                            }else{
                                $testIdArr[]='u'.$testMsg[0];
                            }
                        }
                    }
                }else{ //试题
                    $testArr=explode(';',$jForum['menuContent']);
                    foreach($testArr as $k=>$kTestArr){
                        $testMsg=explode('|',$kTestArr);
                        if(!empty($testMsg[0])){
                            if($testMsg[1]==0){
                                $testIdArr[]=$testMsg[0];
                            }else{
                                $testIdArr[]='c'.$testMsg[0];
                            }
                        }
                    }
                }
            }
        }
        $testIdStr=implode(',',$testIdArr); //把POST过来的JSON数据中的知识点ID，试题ID提取出来
        return $testIdStr;
    }

    /**
     * 获取导学案试题和知识混合下载内容
     * @param string|array $idList 试题id或知识id
     * @param array $testField 试题字段
     * @param array $loreField 知识字段
     * @author demo
     */
    public function getCaseAndTestForDown($idList,$testField=array('TestID','Test','Answer','Remark','Analytic','OptionWidth','OptionNum','TestNum','IfChoose'),$loreField=array('LoreID','Lore','Answer')){
        $idList=R('Common/TestLayer/cutIDStrByChar',array($idList,1)); //切割字母开头的字符串id为数组

        $page=array('perpage'=>100);
        //获取知识
        $loreID=array_merge((array)$idList['l'],(array)$idList['u']);
        if($loreID){
            $caseLoreQuery = $this->getModel('CaseLoreQuery');
            $where = array('LoreID'=>implode(',',$loreID));
            $loreResult = $caseLoreQuery->getLore($loreField,$where,'',$page,0,1);
            $dataArray['l']  = $loreResult['l'];
            $dataArray['u']  = $loreResult['u'];
        }

        //获取试题
        $testID=array_merge((array)$idList['c'],(array)$idList[0]);
        if($testID){
            $testRealQuery = $this->getModel('TestRealQuery');
            $where = array('TestID'=>implode(',',$testID));
            $testResult = $testRealQuery->getDownTest($testField,$where,1);
            $dataArray[0]   = $testResult[0];
            $dataArray['c'] = $testResult['c'];
        }
        return (array)$dataArray['l']+(array)$dataArray['u']+(array)$dataArray['c']+(array)$dataArray[0];
    }

    /**
     * 获取导学案试题和知识混合页面显示内容
     * @param string|array $idList 试题id或知识id
     * @param array $testField 试题字段
     * @param array $loreField 知识字段
     * @author demo
     */
    public function getCaseAndTestFromIndex($idList,$testField,$loreField){

        $idList=R('Common/TestLayer/cutIDStrByChar',array($idList,1)); //切割字母开头的字符串id为数组

        $page=array('perpage'=>100);
        //查询自建和系统试题
        $testID=array_merge((array)$idList['c'],(array)$idList[0]);

        if($testID){
            $testRealQuery = $this->getModel('TestRealQuery');
            $where=array('TestID'=>implode(',',$testID));
            $testResult=$testRealQuery->getIndexTest($testField,$where,'',$page,0,1);


            //转换字段名称
            $dataArray[0]   = $testResult[0];
            $dataArray['c'] = $testResult['c'];

        }
        //获取知识
        $loreID=array_merge((array)$idList['l'],(array)$idList['u']);
        if($loreID){
            $caseLoreQuery = $this->getModel('CaseLoreQuery');
            $where=array('LoreID'=>implode(',',$loreID));
            $loreResult=$caseLoreQuery->getLore($loreField,$where,'',$page,0,1);
            $dataArray['l']  = $loreResult['l'];
            $dataArray['u']  = $loreResult['u'];
        }
        return (array)$dataArray['l']+(array)$dataArray['u']+(array)$dataArray['c']+(array)$dataArray[0];
    }
    /**
     * 构造下载导学案相关内容
     * @param array $content 导学案json数据
     * @param int $downStyle 下载文档样式
     * @param string $allIdStr 导学案json数据中所有的试题ID，及知识点ID（以 , 间隔）
     * @return array
     * @author demo
     */
    public function buildDownContent($content,$downStyle,$allIdStr){
        if(!$content) return '';

        $testResult=$this->getCaseAndTestForDown($allIdStr); //根据试题获取对应内容函数

        if(!$testResult) return '';
        //对数据进行处理
        return $this->changeDataForDown($content,$downStyle,$testResult);
    }
    /**
     * 改变下载数据内容格式
     * @param array $content 导学案json数据
     * @param int $downStyle 下载文档样式
     * @param string $allIdStr 导学案json数据中所有的试题ID，及知识点ID（以 , 间隔）
     * @return array
     * @author demo
     */
    public function changeDataForDown($content,$downStyle,$testResult){

        //$tmpStr[0]=R('Common/TestLayer/reloadTestArr',array($tmpStr[0]));
        $forum=$content['forum'];

        $caseMenu=SS('caseMenu'); //栏目缓存
        $menuIDArray=array(); //记录栏目id，相同栏目id的序号顺延

        foreach($forum as $i=>$iForum){
            $forum[$i]['forumName']=$iForum[0].' · '.$iForum[1];

            foreach($iForum[2] as $j=>$jForum){

                //作为序号
                if(empty($menuIDArray[$jForum['menuID']])){
                    $menuIDArray[$jForum['menuID']]=1;
                }
                $testNum=$menuIDArray[$jForum['menuID']];//试题序号

                if($jForum['ifTest']==0){  //查知识表
                    if($jForum['menuContent']==''){
                        $forum[$i][2][$j]['testContent']=''; //知识为空
                        continue;
                    }

                    //处理知识数据
                    $testArr=explode(';',$jForum['menuContent']);

                    //单个知识不处理题号
                    $changeNum=1; //处理题号
                    if(count($testArr)<2) $changeNum=0;

                    foreach($testArr as $k=>$kTestArr){
                        $testMsg=explode('|',$kTestArr);
                        $remark='remark'.$testMsg[0];

                        //获取知识id
                        if($testMsg[1]=='0'){
                            $keyID='l'.$testMsg[0];
                        }else{
                            $keyID='u'.$testMsg[0];
                        }

                        //为知识赋值
                        if($testResult[$keyID]){
                            //为知识增加序号
                            $numStyle=$caseMenu[$jForum['menuID']]['NumStyle'];
                            $lore=$testResult[$keyID]['Lore'];
                            //试题序号大于1则格式化序号
                            if($changeNum || $testMsg[2]>1) $lore=R('Common/TestLayer/changeTagToNum',array($lore, $testNum,$numStyle));
                            $forum[$i][2][$j]['testContent'][$remark]['Lore']=$lore;

                            $answer=$testResult[$keyID]['Answer'];
                            if(($answer && $downStyle=='normal' && $changeNum) || $testMsg[2]>1){
                                $answer=R('Common/TestLayer/changeTagToNum',array($answer, $testNum,$numStyle));
                            }
                            $forum[$i][2][$j]['testContent'][$remark]['Answer']=$answer;

                            $forum[$i][2][$j]['testContent'][$remark]['testNum']=$testMsg[2];
                            $forum[$i][2][$j]['testContent'][$remark]['system']=$testMsg[1];
                            $testNum+=($testMsg[2]==0 ? 1 :$testMsg[2]);

                        }else{
                            $forum[$i][2][$j]['testContent'][$remark]['Lore']=""; //未查询到该知识点
                            $forum[$i][2][$j]['testContent'][$remark]['Answer']='';
                            $forum[$i][2][$j]['testContent'][$remark]['testNum']='1';
                            $forum[$i][2][$j]['testContent'][$remark]['system']=$testMsg[1];
                        }
                    }

                }else{ //查试题表
                    if($jForum['menuContent']==''){
                        $forum[$i][2][$j]['testContent']=''; //试题内容为空
                        continue;
                    }

                    //处理试题数据
                    $testArr=explode(';',$jForum['menuContent']);

                    //单个知识不处理题号
                    $changeNum=1;//处理题号
                    if(count($testArr)<2) $changeNum=0;

                    foreach($testArr as $k=>$kTestArr){
                        $testMsg=explode('|',$kTestArr);
                        $remark='remark'.$testMsg[0];

                        //获取试题id
                        if($testMsg[1]=='0'){
                            $keyID=$testMsg[0];
                        }else{
                            $keyID='c'.$testMsg[0];
                        }


                        //        $test=$this->getModel('Test');
                        //        $width=500;
                        //        foreach($result as $i=>$iResult){
                        //            $testArray[$i]['testid']=$iResult['TestID'];
                        //            $testArray[$i]['testold']=$test->formatTest($iResult['DocTest'],0,$width,0,1,$iResult['OptionWidth'],$iResult['OptionNum'],$iResult['TestNum'],$iResult['IfChoose']);
                        //            $testArray[$i]['answerold']=$test->formatTest($iResult['DocAnswer'],0,0,0,0,0,0,$iResult['TestNum'],0);
                        //            $testArray[$i]['analyticold']=$test->formatTest($iResult['DocAnalytic'],0,0,0,0,0,0,$iResult['TestNum'],0);
                        //            $testArray[$i]['remark']=$iResult['DocRemark'];
                        //        }

                        $test=$this->getModel('Test');
                        $width=500;
                        //为试题赋值
                        if($testResult[$keyID]){
                            //为试题增加序号
                            $numStyle=$caseMenu[$jForum['menuID']]['NumStyle'];
                            //$lore=$testResult[$keyID]['doctest'];
                            $lore=$test->formatTest($testResult[$keyID]['doctest'],0,$width,0,1,$testResult[$keyID]['optionwidth'],$testResult[$keyID]['optionnum'],$testResult[$keyID]['testnum'],$testResult[$keyID]['ifchoose']);
                            if($changeNum || $testMsg[2]>1) $lore=R('Common/TestLayer/changeTagToNum',array($lore, $testNum,$numStyle));
                            $forum[$i][2][$j]['testContent'][$remark]['Lore']=$lore;

                            //$answer=$testResult[$keyID]['docanswer'];
                            $answer=$test->formatTest($testResult[$keyID]['docanswer'],0,0,0,0,0,0,$testResult[$keyID]['testnum'],0);
                            if(($answer && $downStyle=='normal' && $changeNum) || $testMsg[2]>1){
                                $answer=R('Common/TestLayer/changeTagToNum',array($answer, $testNum,$numStyle));
                            }
                            $forum[$i][2][$j]['testContent'][$remark]['Answer']=$answer;
                            //检查备注是否存在
                            if(empty($testResult[$keyID]['docremark'])){
                                $testResult[$keyID]['docremark'] = '无</p>';
                            }

                            $testResult[$keyID]['docanalytic']=$test->formatTest($testResult[$keyID]['docanalytic'],0,0,0,0,0,0,$testResult[$keyID]['testnum'],0);
                            $analytic=$testResult[$keyID]['docanalytic'].'<p>【备注】'.$testResult[$keyID]['docremark'];
                            if($testMsg[2]>1){
                                $tmpNum='';
                                $analytic=R('Common/TestLayer/changeTagToNum',array($analytic, $tmpNum,$numStyle));
                            }
                            $forum[$i][2][$j]['testContent'][$remark]['Analytic']=$analytic;

                            $forum[$i][2][$j]['testContent'][$remark]['testNum']=$testMsg[2];
                            $forum[$i][2][$j]['testContent'][$remark]['system']=$testMsg[1];

                            $testNum+=($testMsg[2]==0 ? 1 :$testMsg[2]);
                        }else{
                            $forum[$i][2][$j]['testContent'][$remark]['Lore']=""; //未查询到该试题
                            $forum[$i][2][$j]['testContent'][$remark]['Answer']=""; //未查询到该试题
                            $forum[$i][2][$j]['testContent'][$remark]['Analytic']=""; //未查询到该试题
                            $forum[$i][2][$j]['testContent'][$remark]['testNum']=1;
                            $forum[$i][2][$j]['testContent'][$remark]['system']=$testMsg[1];
                        }
                    }
                }
                //合并答案
                $result=$this->buildAnswerArr($forum[$i][2][$j]['testContent'],$downStyle);
                $forum[$i][2][$j]['testContent']=$result[0];
                if($result[1]) $forum[$i][2][$j]['answerContent']=$result[1];

                $menuIDArray[$jForum['menuID']]=$testNum;//试题序号
            }
        }
        $content['forum']=$forum;
        return $content;
    }
    /**
     * 构造答案显示结构
     * @param array $contentData 需要处理的数组
     * @param int $style 答案结构标识  1 只显示题文，不显示答案.2 题文答案分开，集中显示。3一题一答案
     * @return string
     * @author demo
     */
    private function buildAnswerArr($contentData,$style){
        $test='';
        $answer='';
        if($style=='student'){
            //只显示题文，不显示答案
            foreach($contentData as $i=>$iContentData){
                $test.=$contentData[$i]['Lore'];
            }
        }elseif($style=='normal'){
            //题文答案分开，集中显示
            foreach($contentData as $i=>$iContentData){
                $test.=$contentData[$i]['Lore'];
                //带小题的交叉特殊显示
                if($contentData[$i]['Answer']){
                    $answer.=$this->crossAnswerAnalytic($contentData[$i]['Answer'],$contentData[$i]['Analytic']);
                }else{
                    $answer.="<p>&nbsp;</p>";
                }
            }
        }elseif($style=='teacher'){
            foreach($contentData as $i=>$iContentData){
                $test.=$contentData[$i]['Lore'];
                if($contentData[$i]['Answer']){
                    $test.='<p>【答案】'.R('Common/TestLayer/removeLeftTag',array($this->crossAnswerAnalytic($contentData[$i]['Answer'],$contentData[$i]['Analytic']),'<p>'));
                }
            }
        }
        return array($test,$answer);
    }
    /**
     * 设置导学案下载板式
     * @author demo
     */
    public function setDocCon($arr){

        $this->setDocStyle($arr['docstyle'],0);    //设置word样式

        $output=''; //输出

        //导学案名称
        $tempName="<p class=MsoNormal style='margin-top:30px;margin-bottom:30px;text-align:center;font-size:16.0pt;font-family:黑体'><b>".$arr['tempName']."</b></p>";

        $output.=$tempName;
        $output.="<p class=MsoNormal style='margin-top:15px;text-align:center;font-size:10.0pt'>".$arr['tempDesc']."</p>";

        $answer=''; //答案
        $answer.=$tempName;
        $answer.="<p class=MsoNormal style='margin-top:15px;text-align:center;font-size:12.0pt;font-family:\"微软雅黑\"'><b style='border:solid windowtext 1.0pt;'>&nbsp;&nbsp;&nbsp;&nbsp;详细答案&nbsp;&nbsp;&nbsp;</b></p>";

        foreach($arr['forum'] as $iArr){
            $tmpForum="<p class=MsoNormal style='margin-top:20px;margin-bottom:10px;text-align:center;'><span style='font-size:14.0pt;font-family:楷体;color:#0097D3'>&#9810;&#9810;&#9810;&#9810;&#9810;&#9810;&#9810;<b>".$iArr['forumName']."</b>&#9810;&#9810;&#9810;&#9810;&#9810;&#9810;&#9810;</span></p>";
            $output.=$tmpForum;

            $answer.=$tmpForum;
            foreach($iArr[2] as $jArr){
                $output.="<p class=MsoNormal style='margin-top:15.0pt;margin-right:0cm;margin-bottom:7.5pt;margin-left:0cm'>
                <b style='mso-bidi-font-weight:normal'><span lang=EN-US
style='font-size:12.0pt;mso-bidi-font-size:11.0pt;font-family:\"微软雅黑\",\"sans-serif\";
color:white;mso-themecolor:background1;background:#47B8E1'><span
style='mso-spacerun:yes'>&nbsp;&nbsp;&nbsp;&nbsp; </span></span></b><b
style='mso-bidi-font-weight:normal'><span style='font-size:12.0pt;mso-bidi-font-size:
11.0pt;font-family:\"微软雅黑\",\"sans-serif\";color:white;mso-themecolor:background1;
background:#47B8E1'>".$jArr['menuName']."<span lang=EN-US><span
style='mso-spacerun:yes'>&nbsp;&nbsp;&nbsp;&nbsp; </span></span></span></b><b
style='mso-bidi-font-weight:normal'><span lang=EN-US style='font-size:12.0pt;
mso-bidi-font-size:11.0pt;font-family:\"微软雅黑\",\"sans-serif\";color:white;
mso-themecolor:background1;background:white;mso-shading-themecolor:background1'>1</span></b><b
style='mso-bidi-font-weight:normal'><span lang=EN-US style='font-size:12.0pt;
mso-bidi-font-size:11.0pt;font-family:\"微软雅黑\",\"sans-serif\";color:white;
mso-themecolor:background1;background:#47B8E1'> </span></b>";

                $output.="<p class=MsoNormal style='margin-top:15px;text-align:left;'>".$jArr['testContent']."</p>";
                if($jArr['ifAnswer']!=0 && $jArr['answerContent']){
                    $answer.="<p class=MsoNormal style='margin-top:20px;margin-bottom:10px;text-align:left;'>【".$jArr['menuName']."】</p>";
                    $answer.="<p class=MsoNormal style='margin-top:15px;text-align:left;'>".$jArr['answerContent']."</p>";
                }
            }
        }
        //是否改变字体为罗马
        $output=$this->formatDocOutput($output,$arr['subjectID'],$arr['docversion']);

        $output='<div class=Section1 style=\'layout-grid:15.6pt\'>'.$output .'</div>';

        if($arr['doctype']=='normal' && $answer){
            $output.='<span lang=EN-US style=\'font-size:10.5pt;mso-bidi-font-size:11.0pt;font-family:
"Times New Romance","sans-serif";mso-fareast-font-family:宋体;mso-bidi-font-family:"Times New Roman";
mso-ansi-language:EN-US;mso-fareast-language:ZH-CN;mso-bidi-language:AR-SA\'><br clear=all style=\'page-break-before:always;mso-break-type:section-break\'></span>';    //换页

            $output.=$this->formatDocOutput($answer,$arr['subjectID'],$arr['docversion']);

        }
        return $output;
    }

    /**
     * 根据cookie数据下载word文档
     * @param array @param 参数
     * @param['subjectID']=$subjectID; //学科id
     * @param['cookieStr']=$cookieStr; //内容
     * @param['isSaveRecord']=$isSaveRecord; //是否存档
     * @param['docVersion']=$docVersion; //文档类型
     * @param['paperSize']=$paperSize; //纸张大小
     * @param['paperType']=$paperType; //试卷类型
     * @param['backType']=0; //是否仅返回路径
     * @param['docName']=$docName; //下载名称
     * @param['downStyle']=3; // 区分下载作业还是试卷 答题卡4 导学案3 作业2，试卷1
     * @author demo
     */
    public function getDownUrlByCookie($param,$userName,$cookieSubjectID){
        extract($param);

        //获取试题id和知识id
        $testList = $this->reSetTestIdAndLoreId($cookieStr);//导学案的知识点ID和试题id

        //验证试题数量
        $tmpArr=explode(',',$testList);
        if(count($tmpArr)>100){
            $data=array();
            $data[0]='false';
            $data['msg']='30809';
            return $data; //导学案知识和试题超出！请控制在100以内。
        }

        $testList=rtrim($testList,',');
        //若有系统的题,记录其下载试题次数
        if(empty($testList)){
            $data=array();
            $data[0]='false';
            $data['msg']='30810';
            return $data; //导学案数据为空！请加入试题或知识后重试。
        }

        //记录下载次数
        $idList=R('Common/TestLayer/cutIDStrByChar',array($testList,1)); //切割字母开头的字符串id为数组
        if($idList[0]){
            $testDown = $this->getModel('TestDown');
            $testDown->setDownTime($idList[0]);
            unset($testDown);
        }

        //加入参数
        $paperStyle='普通用卷';
        if($paperType){
            switch($paperType){
                case 'normal':
                    $paperStyle='普通用卷';
                    break;
                case 'teacher':
                    $paperStyle='教师用卷';
                    break;
                case 'student':
                    $paperStyle='学生用卷';
                    break;
            }
        }

        //获取知识和试题内容
        $cookieParam=$this->buildDownContent($cookieStr,$paperType,$testList);

        //组合试题和知识到版式
        $cookieParam['issaverecord']=$isSaveRecord; //是否记录
        $cookieParam['docversion']=$docVersion; //文档类型
        $cookieParam['docstyle']=$paperSize; //试卷版式
        $cookieParam['doctype']=$paperType; //试卷类型

        if(empty($docName)){
            $cookieParam['tempName']=str_replace('$!$','·',$cookieParam['tempName']);
            $docName=$cookieParam['tempName'].'('.$paperStyle.')'; //下载名称
        }
        $cookieParam['subjectID']=$subjectID; //增加学科判断英语 做特殊处理

        $str = $this->setDocCon($cookieParam);
        if(!$str){
            $data=array();
            $data[0]='false';
            $data['msg']='30737'; // 写入文档失败！请重试...',
            return $data;
        }

        $param=array();
        $param['downStyle']=$downStyle; // 区分下载作业还是试卷 答题卡4 导学案3 作业2，试卷1
        $param['str']=$str;
        $param['docName']=$docName;
        $param['docVersion']=$docVersion;
        $param['isSaveRecord']=$isSaveRecord;
        $param['backType']=$backType;
        $param['subjectID']=$subjectID;
        $param['ifShare']=$ifShare;
        $param['cookieStr']=serialize($cookieStr);
        return $this->createDocCon($param,$userName,$cookieSubjectID);
    }
}