<?php
/**
 * @author demo 
 * @date 2014年10月31日
 */
/**
 * 文档操作model类
 */
namespace Doc\Model;
class DocModel extends HandleWordModel {
    //$style1=iconv('UTF-8','GB2312//IGNORE',$style1);'; //标题样式
    /**
     * 设置一个word试题 用于：正式下载时替换小题
     * @param array $testn 试题数组 需要题文、答案、解析、小题数、宽度、小题数量
     * @param int $key 序号
     * @param int $type 是否带答案 0不带答案解析 1带答案解析随试题 2独立答案解析
     * @param int $width 宽度
     * @param array $score 分值
     * @return array
     * @author demo
     */
    public function getOneDocStr($testn,$key,$type=0,$width=550,$score) {
        //检查并建立对应关系
        $testStr='';
        $countSubStr=$testn['TestNum'];
        //替换小题标签
        if($countSubStr>0){
            $param=array();
            $param['Test']=$testn['DocTest'];
            $param['Key']=$key;
            $param['Width']=$width;
            $param['Score']=$score;
            $param['IfFormat']=1;
            $param['OptionWidth']=$testn['OptionWidth'];
            $param['OptionNum']=$testn['OptionNum'];
            $param['IfDoc']=1;
            $testn['DocTest']=$this->changeHZ($param);
            if($testn['DocRemark'] === ''){
                $testn['DocRemark'] = '无</p>';
            }else{
                $testn['DocRemark'] = R('Common/TestLayer/removeLeftTag',array($testn['DocRemark'],'<p>'));
            }
            $testn['DocRemark']="<p>【备注】".$testn['DocRemark'];
            if($type==2){
                //答案解析交叉
                $testn['DocAnswer']=$this->changeHZJX($testn['DocAnswer'],$testn['DocAnalytic'],$key).$testn['DocRemark'];
            }else{
                $testn['DocAnswer']="<p>【答案】".R('Common/TestLayer/removeLeftTag',array($this->changeHZ(array('Test'=>$testn['DocAnswer'], 'Key'=>$key)),'<p>'));
                $testn['DocAnalytic']="<p>【解析】".R('Common/TestLayer/removeLeftTag',array($this->changeHZ(array('Test'=>$testn['DocAnalytic'],'Key'=>$key)),'<p>'));
            }
            $key+=$testn['TestNum'];
        }else{
            //处理题文
            if($testn['IfChoose']!=0){
                $param=array();
                $param['Test']=$testn['DocTest'];
                $param['Key']=$key;
                $param['Width']=$width;
                $param['Score']=$score[0];
                $param['IfFormat']=1;
                $param['OptionWidth']=$testn['OptionWidth'];
                $param['OptionNum']=$testn['OptionNum'];
                $testn['DocTest']=$this->changeGS($param);
            }else{
                $param=array();
                $param['Test']=$testn['DocTest'];
                $param['Key']=$key;
                $param['Width']=$width;
                $param['Score']=$score[0];
                $testn['DocTest']=$this->changeGS($param);
            }
            //处理答案和解析
            $testn['DocAnswer']=$this->filterP(R('Common/TestLayer/delMoreTag',array($testn['DocAnswer'])));
            $testn['DocAnalytic']=$this->filterP(R('Common/TestLayer/delMoreTag',array($testn['DocAnalytic'])));
            $testn['DocRemark']=$this->filterP(R('Common/TestLayer/delMoreTag',array($testn['DocRemark'])));
            //判断答案和解析是否为空
            $testn['DocAnswer']= $this->ifEmptyStr($testn['DocAnswer'])==='' ? '无' : $this->myFilter($testn['DocAnswer']);
            $testn['DocAnalytic']= $this->ifEmptyStr($testn['DocAnalytic'])==='' ? '无' : $this->myFilter($testn['DocAnalytic']);
            $testn['DocRemark']= $this->ifEmptyStr($testn['DocRemark'])==='' ? '无' : $this->myFilter($testn['DocRemark']);

            $testn['DocAnswer']= '<p>'.$testn['DocAnswer'].'</p>';
            $testn['DocAnalytic']='<p>'.$testn['DocAnalytic'].'</p>';
            $testn['DocRemark']='<p>'.$testn['DocRemark'].'</p>';

            //排版答案和解析
            if($type==2){
                $testn['DocAnswer']="<p>".$key.".".R('Common/TestLayer/removeLeftTag',array($testn['DocAnswer'],'<p>'));
                $testn['DocAnswer'].="<p>【解析】".R('Common/TestLayer/removeLeftTag',array($testn['DocAnalytic'],'<p>'));
                $testn['DocAnswer'].="<p>【备注】".R('Common/TestLayer/removeLeftTag',array($testn['DocRemark'],'<p>'));
            }else{
                //$testn['DocAnswer']="<p class=MsoNormal>【答案】".$testn['DocAnswer'];
                //$testn['DocAnalytic']="<p class=MsoNormal>【解析】".$testn['DocAnalytic'];
                $testn['DocAnswer']="<p>【答案】".R('Common/TestLayer/removeLeftTag',array($testn['DocAnswer'],'<p>'));
                $testn['DocAnalytic']="<p>【解析】".R('Common/TestLayer/removeLeftTag',array($testn['DocAnalytic'],'<p>'));
                $testn['DocRemark']="<p>【备注】".R('Common/TestLayer/removeLeftTag',array($testn['DocRemark'],'<p>'));
            }
            $key++;
        }

        //排版
        if($type==0){
            $testStr=$testn['DocTest'];
        }else if($type==1){
            $testStr=$testn['DocTest'].$testn['DocAnswer'].$testn['DocAnalytic'].$testn['DocRemark'].'<p>&nbsp;</p>';
        }else if($type==2){
            $testStr=array();
            $testStr[0]=$testn['DocTest'];
            $testStr[1]=$testn['DocAnswer'];
            $testStr[2]=$testn['DocAnalytic'];
        }

        return array($key,$testStr);
    }

    /**
     * 后台单题下载过滤数据
     * @param array $testArr 试题属性数组
     * @param float $fontSize 试题字体大小
     * @return array
     * @author demo
     */
    public function getSingleDoc($testArr,$fontSize,$paperName='',$easy=0,$format=0) {
        $testArr['DocTest']=$this->filterP($testArr['DocTest']);
        $testArr['DocAnswer']=$this->filterP($testArr['DocAnswer']);
        $testArr['DocAnalytic']=$this->filterP($testArr['DocAnalytic']);
        $testArr['DocRemark']=$this->filterP($testArr['DocRemark']);

        $testModel=$this->getModel('Test');
        if($format){
            $testArr=$testModel->replaceTest(array($testArr['TestID']),array($testArr),'Doc')[0];
        }

        if($paperName=='') $paperName='题库(www.tk.com)试题编号：'.$testArr['TestID'].'-'.$testArr['NumbID'];

        $typesBuffer=SS('types');
        if($easy==1){
            $str = '<p>'.$paperName.'</p>';
            $str .= '<p>&nbsp;</p>';
            $str .= '<p>'.$testArr['DocTest'];
            $str .= '<p>&nbsp;</p>';
            $str .= '<p><font color="blue">答案：</font>' . $testArr['DocAnswer'];
            $str .= '<p>&nbsp;</p>';
            $str .= '<p><font color="blue">解析：</font>' . $testArr['DocAnalytic'];
        }else{
            $str = '<p>题库(www.tk.com)试题编号：'.$testArr['TestID'].'-'.$testArr['NumbID'].'</p><p><font color="blue">【题文】</font>'.$testArr['DocTest'];
            $str .= '<p><font color="blue">【答案】</font>' . $testArr['DocAnswer'];
            $str .= '<p><font color="blue">【解析】</font>' . $testArr['DocAnalytic'];
            $str .= '<p><font color="blue">【题型】</font>' . $typesBuffer[$testArr['TypesID']]['TypesName'];
            $str .= '<p><font color="blue">【备注】</font>' . $testArr['DocRemark'];
            $str .= '<p><font color="blue">【结束】</font></p>';
        }

        $str=mb_convert_encoding($str,'gbk','utf-8');
        $str=$this->html2word($str,$fontSize);

        return $str;
    }

    //根据cookiestr分解试卷为arr
    function getDocCon($cookieStr,$testList){
        $arr=array();

        //试题doc内容按照试题id索引排序
        $buffer = $this->getModel('TestDoc')->selectData(
            '*',
            'TestID in (' . $testList . ')');
        $buffer2 = $this->getModel('TestAttrReal')->selectData(
            'TestID,TestNum,OptionWidth,IfChoose,OptionNum',
            'TestID in (' . $testList . ')');
        $bufferTestDoc = array ();
        if ($buffer) {
            foreach ($buffer as $iBuffer) {
                $bufferTestDoc[$iBuffer['TestID']] = $iBuffer;
            }
        } else {
            return "您选择的试题已不存在！请选择试题后组卷";
        }
        unset($buffer);
        if ($buffer2) {
            foreach ($buffer2 as $iBuffer2) {
                $bufferTestDoc[$iBuffer2['TestID']] = array_merge($bufferTestDoc[$iBuffer2['TestID']],$iBuffer2);
            }
        }
        unset($buffer2);
        return $this->getTestContent($cookieStr, $bufferTestDoc);
    }
    /**
     * 获取试卷文档内容(getDocCon方法中提出)
     * @param string $cookieStr cookie信息
     * @param array $tests 需下载的试题内容
     * @return array
     * @author demo
     * @date 2015-2-9
     */
    public function getTestContent($cookieStr, $tests){
        $tmpStr1=explode('@#@',$cookieStr);
        $fenJuan=array();
        $j=0;
        foreach($tmpStr1 as $iTmpStr1){
            $tmpStr2=explode('@$@',$iTmpStr1);
            if($tmpStr2[0]=='rectest') continue; //回收站
            if($tmpStr2[0]=='attr') continue; //联考属性
            switch($tmpStr2[0]){
                case 'maintitle':
                    $arr['title_main'] = array ($tmpStr2[1],$tmpStr2[2]);
                    break;
                case 'subtitle':
                    $arr['title_sub'] = array ($tmpStr2[1],$tmpStr2[2]);
                    break;
                case 'seal':
                    $arr['line'] = array ($tmpStr2[1]);
                    break;
                case 'marktag':
                    $arr['title_secret'] = array ($tmpStr2[1],$tmpStr2[2]);
                    break;
                case 'testinfo':
                    $arr['title_info'] = array ($tmpStr2[1],$tmpStr2[2]);
                    break;
                case 'studentinput':
                    $arr['title_student'] = array ($tmpStr2[1],$tmpStr2[2]);
                    break;
                case 'score':
                    $arr['fill_score'] = array ($tmpStr2[1]);
                    break;
                case 'notice':
                    $arr['fill_careful'] = array ($tmpStr2[1],$tmpStr2[2]);
                    break;
                default :
                    $fenJuan[$j]=$tmpStr2;
                    $j++;
                    break;
            }
        }

        //第N卷
        $j=0; //分卷序号
        $t=0; //题型数量
        $arr['test_head'] = array();
        $tpArr=array();
        foreach($fenJuan as $iFenJuan){
            if(strstr($iFenJuan[0],'parthead')){
                $j++;
                //处理分卷
                $arr['test_head'][]=array($iFenJuan[1],$iFenJuan[2],$iFenJuan[3]);
                continue;
            }
            $t++;
            $tmpSum=0; //小题数
            $tmpTotal=0; //总分
            $ifSameScore=0; //所有分值是否相等 0相等 1不相等
            $tmpScore=0; //存储第一小题分数
            $tmpChoose=0; //存储选做序号
            $textArr=array(); //存储试题 题号 内容 分值
            $textChoose=array(); //临时存储试题选做情况
            $textChooseReal=array(); //存储试题选做情况
            $tmpFlag=0; //选做题序号
            $k=0; //试题序号
            //计算总分和描述
            if(!empty($iFenJuan[5])){  //$iFenJuan[5] != 0 的判断条件在存在字符串时始终为false
                $tmpN=explode(';',$iFenJuan[5]);
                foreach($tmpN as $iTmpN){
                    $tmpO=explode('|',$iTmpN);
                    $tmpP=explode(',',$tmpO[2]);
                    if($tmpO[3]!=0 and ($tmpChoose==0 or $tmpChoose!=$tmpO[4])){
                        $tmpChoose=$tmpO[4];
                        $textChoose[$tmpChoose][0]=$tmpO[3]; //选做个数
                        $textChoose[$tmpChoose][1]=1; //选做题数量
                        $textChoose[$tmpChoose][2]=$tmpO[0]; //选做题开始的试题id
                    }else if($tmpChoose==$tmpO[4] && !($tmpO[4]==0 && $tmpChoose==0)){
                        $textChoose[$tmpChoose][1]++;
                    }
                    if(!$ifSameScore){
                        foreach($tmpP as $iTmpP){
                            //判断所有试题分值是否相等
                            if($tmpScore===0) $tmpScore=$iTmpP;
                            else if($tmpScore!=$iTmpP){
                                $ifSameScore=1;
                            }
                        }
                    }
                    $tmpSum+=$tmpO[1];

                    if($tmpFlag!=$tmpO[4] && $tmpO[4]!=0){
                        $ifSameScore=1;
                        $tmpTotal+=array_sum($tmpP)*$tmpO[3];
                        $tmpFlag=$tmpO[4];
                    }else if($tmpO[4]==0){
                        $tmpTotal+=array_sum($tmpP);
                        $tmpFlag=0;
                    }
                    $textArr[$k][0] = $tmpO[0];
                    $textArr[$k][1] = $tests[$tmpO[0]];
                    $textArr[$k][2] = $tmpP; //存储分值
                    $k++;
                }
            }
            //处理选做题
            if($textChoose){
                foreach($textChoose as $iTextChoose){
                    $textChooseReal[$iTextChoose[2]][0]=$iTextChoose[0]; //选做序号
                    $textChooseReal[$iTextChoose[2]][1]=$iTextChoose[1]; //选做题数量
                }
            }
            //处理分值
            $tmpStr='';
            if($ifSameScore){
                if(!empty($tmpSum)) $tmpStr='：共'.$tmpSum.'题 共'.$tmpTotal.'分';
            }else{
                if(!empty($tmpSum)) $tmpStr='：共'.$tmpSum.'题 每题'.$tmpScore.'分 共'.$tmpTotal.'分';
                foreach($textArr as $i=>$iTextArr){
                    $textArr[$i][2]=0;
                }
            }
            /*
            $tmpN=explode('|',$iFenJuan[6]);
            $textArr=array();
            if ($iFenJuan[5]) {
                $tmpArr1 = explode(';', $iFenJuan[5]);
                foreach ($tmpArr1 as $tmpN) {
                    $tmpArr2 = explode('|', $tmpN);
                    $textArr[] = $bufferTestDoc[$tmpArr2[0]];
                    $tmpSum+=$tmpArr2[1];
                }
                if($tmpN[1]==2){
                    $tmpSum=count($tmpArr1);
                }
            }

            $tmpTotal=$tmpN[0]*$tmpSum; //总分
            $tmpC='';
            if($tmpN[2]>0 && is_numeric($tmpN[2])){
                $tmpC='任选'.$tmpN[2].'题';
                if($tmpSum>$tmpN[2])
                $tmpTotal=$tmpN[2]*$tmpN[0]; //总分
                else
                $tmpTotal=$tmpSum*$tmpN[0]; //总分
            }

            $tmpStr='';
            if($tmpN[1]==1){
                $tmpStr='：共'.$tmpSum.'题 '.$tmpC.' 每题'.$tmpN[0].'分 共'.$tmpTotal.'分';
            }else if($tmpN[1]==2){
                $tmpStr='：共'.$tmpSum.'大题 '.$tmpC.' 每题'.$tmpN[0].'分 共'.$tmpTotal.'分';
            }*/
            if($iFenJuan[3]=='（题型注释）') $iFenJuan[3]='';
            $tpArr[$j][]=array($iFenJuan[1],$iFenJuan[4],$iFenJuan[2],$iFenJuan[3],$textArr,$tmpStr,$textChooseReal);
        }
        foreach($arr['test_head'] as $i=>$iArr){
            $arr['test_head'][$i][3]=$tpArr[$i+1];
        }
        $arr['fill_score'][1] = $t;
        return $arr;
    }
    /**
     * 设置样式    $arr数组
     *     array()        docstyle        //word样式
     *                 title_main        //主标题        array(0=>是否显示,1=>显示内容)
     *                 title_sub        //副标题        array(0=>是否显示,1=>显示内容)
     *                 line            //装订线        array(0=>是否显示)
     *                 title_secret    //保密标记    array(0=>是否显示,1=>显示内容)
     *                 title_info        //试卷信息栏    array(0=>是否显示,1=>显示内容)
     *                 title_student    //学生输入栏    array(0=>是否显示,1=>显示内容)
     *                 fill_score        //填分栏        array(0=>是否显示,1=>题型数量)
     *                 fill_careful    //注意事项    array(0=>是否显示,1=>显示内容)
     *                 test_head        //试题头部
     *                             array(0=>array(    0=>是否显示,
         *                                         1=>卷标,
         *                                         2=>卷注,
         *                                         3=>array(
                 *                                         0=>array(0=>是否显示,1=>是否显示评分栏,2=>题型名称,3=>题型注解,4=>试题题号 内容 分值,5=>题型分值描述,7=>选做题数据),
                 *                                         1=>array(0=>是否显示,1=>是否显示评分栏,2=>题型名称,3=>题型注解,4=>试题题号 内容 分值,5=>题型分值描述,7=>选做题数据),
                 *                                         2=>array(0=>是否显示,1=>是否显示评分栏,2=>题型名称,3=>题型注解,4=>试题题号 内容 分值,5=>题型分值描述,7=>选做题数据)
         *                                             )
         *                                     )
     *                             )
     * @param $subjectID string 判断是不是英语
     * */
    public function setDocCon($arr){
        $output=''; //输出

        //word样式
        //装订线
        if($arr['docstyle'])
            $docWidth=$this->setDocStyle($arr['docstyle'],$arr['line'][0]);    //设置word样式

        //保密标记
        if($arr['title_secret'][0]){
            $output.=str_replace('{#juemi#}',$arr['title_secret'][1],$this->topStyle[0]);
        }
        //主标题
        if($arr['title_main'][0]){
            $output.=str_replace('{#maintitle#}',$arr['title_main'][1],$this->topStyle[1]);
        }
        //副标题
        if($arr['title_sub'][0]){
            $output.=str_replace('{#subtitle#}',$arr['title_sub'][1],$this->topStyle[2]);
        }
        //试卷信息栏
        if($arr['title_info'][0]){
            $output.=str_replace('{#titleinfo#}',$arr['title_info'][1],$this->topStyle[3]);
        }
        //学生输入栏
        if($arr['title_student'][0]){
            $output.=str_replace('{#titlestudent#}',$arr['title_student'][1],$this->topStyle[4]);
        }
        //试卷分数栏
        if($arr['fill_score'][0]){
            $strFill=$this->topStyle[5];
            $numList=C('WLN_NUM_LIST');
            //获取题型数量
            $tiHao='';
            $daFen='';
            for($i=0;$i<$arr['fill_score'][1];$i++){
                $tiHao.=str_replace('{#num#}',$numList[$i],$this->topStyle[6]);
                $daFen.=$this->topStyle[7];
            }
            $strFill=str_replace('{#tihao#}',$tiHao,$strFill);
            $output.=str_replace('{#defen#}',$daFen,$strFill);
        }
        //注意事项
        if($arr['fill_careful'][0]){
            $output.=str_replace('{#careful#}',$arr['fill_careful'][1],$this->topStyle[8]);
        }

        $content=''; //存储试题信息
        $t=0;
        $key=1;
        $tnum=C('WLN_NUM_LIST');
        //试卷内容
        $answer=array();
        $link = C('WLN_DOWNLOAD_RANDOM_CONTENT');
        $tags = array('<span %s>%s</span>', '<span %s><img src="%s" width="48" height="66" alt="题库"/></span>');
        $chooseArray=array(); //获取第一卷的选择题数据

        foreach($arr['test_head'] as $i=>$iArr){
            if($i!=0 && ($arr['doctype']=='normal' || $arr['doctype']=='week') && !empty($arr['test_head'][0][3])){
                $content.=$this->topStyle[10];    //换页
                if($arr['doctype']=='week') $content.='{#tikaheyi#}';    //周练专用 第二页答题卡留标签
            }

            if($iArr){
                //分卷
                if($iArr[0]){
                    $strHead=$this->topStyle[9];
                    $strHead=str_replace('{#juanbiao#}',$iArr[1],$strHead);
                    $content.=str_replace('{#juanzhu#}',$iArr[2],$strHead);
                }
                //题型
                foreach($iArr[3] as $jArr){
                    if($jArr[0]){
                        $strHead=$this->topStyle[12];
                        if($jArr[1]) $strHead=str_replace('{#pingfen#}',$this->topStyle[11],$strHead);
                        else $strHead=str_replace('{#pingfen#}','',$strHead);
                        $strHead=str_replace('{#tixing#}',$tnum[$t].'、'.$jArr[2].$jArr[5],$strHead);
                        $content.=str_replace('{#tizhu#}',$jArr[3],$strHead);
                        $t++;
                    }
                    //试题/////////////////////////////////////////////////////////////////
                    if($jArr[4]){
                        foreach($jArr[4] as $k=>$kArr){

                            if($iArr[0]==1){ //仅记录第一卷
                                $tmpKey=$key;
                                if($kArr[1]['ifchoose']>1){
                                    $chooseArray[]=$tmpKey;
                                }else if($kArr[1]['ifchoose']==1){
                                    foreach($kArr[1]['judge'] as $lArr){
                                        if($lArr['ifchoose']>1){
                                            $chooseArray[]=$tmpKey;
                                            $tmpKey++;
                                        }
                                    }
                                }
                            }


                            //判断是否有选做题组
                            if($jArr[6][$kArr[0]]){
                                $tmpArr=array();
                                $tmpArr2=array();
                                for($i=0;$i<$jArr[6][$kArr[0]][1];$i++){
                                    $tmpArr[]=$i+$key;
                                }
                                for($i=0;$i<$jArr[6][$kArr[0]][0];$i++){
                                    $tmpArr2[]=$tnum[$i];
                                }
                                $content.='<p><b>请考生在第 '.implode('、',$tmpArr).' '.$tnum[$jArr[6][$kArr[0]][1]-1].'题中任选'.$tnum[$jArr[6][$kArr[0]][0]-1].'道做答，注意：只能做所选定的题目。如果多做，则按所做的第'.implode('、',$tmpArr2).'个题目计分。</b></p>';
                            }
                            //试题字段名称转换
                            $kArr[1]=$this->changeDownFieldUpper($kArr[1]);
                            if($arr['doctype']=='teacher'){
                                $tmpArr1=$this->getOneDocStr($kArr[1],$key,1,$docWidth,$kArr[2]);
                                $key=$tmpArr1[0];
                                // $content.=\Common\Model\RandomIdentificationModel::addRandomContent($tmpArr1[1], $link, $tags);
                                $content.=$tmpArr1[1];
                            }else if($arr['doctype']=='normal' || $arr['doctype']=='week'){
                                $tmpArr1=$this->getOneDocStr($kArr[1],$key,2,$docWidth,$kArr[2]);
                                $key=$tmpArr1[0];
                                // $content.= \Common\Model\RandomIdentificationModel::addRandomContent($tmpArr1[1][0], $link, $tags);
                                $content .= $tmpArr1[1][0];
                                $answer[]=array($tmpArr1[1][1],$tmpArr1[1][2]);
                            }else{
                                $tmpArr1=$this->getOneDocStr($kArr[1],$key,0,$docWidth,$kArr[2]);
                                $key=$tmpArr1[0];
                                // $content.= \Common\Model\RandomIdentificationModel::addRandomContent($tmpArr1[1], $link, $tags);
                                $content .= $tmpArr1[1];
                            }
                        }
                    }
                }
            }
        }

        //为试卷附加周考版答题卡
        if($arr['doctype']=='week'){
            if($chooseArray){
                $tikaheyi=$this->topStyle[13];

                $tmpContent=array(); //记录每行数据
                $tmpI=0; //数组键值
                $tmpJ=0; //计数
                $tmpJG=10; //每X题间隔
                foreach($chooseArray as $iChooseArray){
                    $tmpContent[$tmpI].="<td width=67 style='width:50.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt'><p class=MsoNormal style='text-align:center'>".$iChooseArray.'</p></td>';
                    $tmpContent[$tmpI+1].="<td width=67 style='width:50.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt'>&nbsp;</td>";
                    $tmpJ++;
                    if($tmpJG<=$tmpJ){
                        $tmpI+=2;
                        $tmpJ=0;
                    }
                }
                if($tmpJ!=0 && $tmpI>0){
                    for(;$tmpJ<10;$tmpJ++){
                        $tmpContent[$tmpI].="<td width=67 style='width:50.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt'>&nbsp;</td>";
                        $tmpContent[$tmpI+1].="<td width=67 style='width:50.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt'>&nbsp;</td>";
                    }
                }

                $tmpStr='';
                foreach($tmpContent as $iTmpContent){
                    $tmpStr.="<tr style='mso-yfti-irow:1;mso-yfti-lastrow:yes;height:26.0pt'>".$iTmpContent.'</tr>';
                }
                $tikaheyi = str_replace('{#tihao#}',$tmpStr,$tikaheyi);

                $content = str_replace('{#tikaheyi#}',$tikaheyi,$content);
            }else{
                $content = str_replace('{#tikaheyi#}','',$content);
            }
        }

        //$content = \Doc\Model\RandomIdentificationModel::addRandomContent($content,$link, $tags);
        $content = getStaticFunction('RandomIdentification','addRandomContent',$content, $link, $tags);
        /*
        //是否改变字体为罗马
        $ifRoman=0;
        if($arr['subjectID']=='14' || $arr['subjectID']=='24'){
            $ifRoman=1;
        }

        //是否去除居中样式
        $ifMiddle=1;
        if(in_array($arr['subjectID'],array(13,15))){
            $ifMiddle=0;
        }

        //处理试题
        $content=$this->formatDocOutput($content,$ifRoman,$ifMiddle);*/
        $content=$this->formatDocOutput($content,$arr['subjectID'],$arr['docversion']);
        $output.=$content;

        $output='<div class=Section1 style=\'layout-grid:15.6pt\'>'.$output .'</div>';

        if(($arr['doctype']=='normal' || $arr['doctype']=='week') && $answer){
            $output.='<span lang=EN-US style=\'font-size:10.5pt;mso-bidi-font-size:11.0pt;font-family:
"Times New Romance","sans-serif";mso-fareast-font-family:宋体;mso-bidi-font-family:"Times New Roman";
mso-ansi-language:EN-US;mso-fareast-language:ZH-CN;mso-bidi-language:AR-SA\'><br clear=all style=\'page-break-before:always;mso-break-type:section-break\'></span>';    //换页
            $output.='<div style=\'layout-grid:15.6pt\'>';
            $output.='<p style="font-weight:bold;text-align:center;line-height:300%;">参考答案</p>';
            $answerOutput='';
            foreach($answer as $iAnswer){
                $answerOutput.=$iAnswer[0].'<p>&nbsp;</p>';
            }
            $answerOutput=$this->formatDocOutput($answerOutput,$arr['subjectID'],$arr['docversion']);

            $output.=$answerOutput.'</div>';
        }
        return $output;
    }

    /**
     * 查询入库文档总数；
     * @param bool $row 是否查询入库数据 默认true 已经入库 false全部数据
     * @return int 数量
     * @author demo
     */
    public function getTotalRow($row=true){
        if($row) return $this->selectCount('IfIntro=1','DocID');
        else return $this->selectCount('1=1','DocID');

    }

    //删除上传文件 zj_doc
    public function deleteFile($DocID) {
        $buffer = $this->selectData(
            '*',
            'DocID in (' . $DocID . ')');
        if ($buffer){
            foreach($buffer as $iBuffer){
                $this->deleteAllFile($iBuffer);
            }
        }
    }
    //删除上传文件 zj_test_replace
    public function deleteReplaceFile($ReplaceID) {
        $buffer = $this->getModel('TestReplace')->selectData(
            '*',
            'ReplaceID=' . $ReplaceID,'',1);
        if ($buffer) $this->deleteAllFile($buffer[0]);
    }
    //删除文件
    public function deleteDocFile($FilePath) {
        $host=C('WLN_DOC_HOST');
        if($host){
            $checkstr = md5(C('DOC_HOST_KEY').date("Y.m.d",time()));
            $tmpArr=explode('/',$FilePath);
            $tmpCount=count($tmpArr);
            $t=strtotime($tmpArr[$tmpCount-3].$tmpArr[$tmpCount-2]);
            $f=substr($tmpArr[$tmpCount-1],0,strrpos($tmpArr[$tmpCount-1],'.'));
            $url = $host."/comPaperDoc.php?checkstr=$checkstr&uptype=deldoc&t=$t&f=$f";
            @file_get_contents($url);
            return 'success';
        }

        //删除doc
        $docPath = realpath('./') . $FilePath;
        if (file_exists($docPath))
            unlink($docPath);
        //删除html
        $docHtmlPath = realpath('./') . substr($FilePath,0,strrpos($FilePath,'.')).'.htm';
        if (file_exists($docHtmlPath))
            unlink($docHtmlPath);
        //删除files
        $docFilePath = realpath('./') . substr($FilePath,0,strrpos($FilePath,'.')).'.files';
        if (file_exists($docFilePath)) {
            if (is_dir($docFilePath)) {
                if ($dh = opendir($docFilePath)) {
                    while (($file = readdir($dh)) !== false) {
                        if ($file != '.' || $file != '..')
                            @unlink($docFilePath . '/' . $file);
                    }
                    @closedir($dh);
                }
                @rmdir($docFilePath);
            }
            return 'success';
        }
    }
	//excel文档获取试题并入库 $docID文档id  
	public function  getTestByxls($docID,$idList=array(),$userName){
		$testReplace=array();//记录需要删除的替换试题id
        $buffer = $this->selectData(
            '*',
            '1=1',
            'OrderID asc',
            '',
            'TestTag');
        $start = array ();
        $testField = array ();
        foreach ($buffer as $iBuffer) {
            $start[] = $iBuffer['DefaultStart'];
            $testField[] = $iBuffer['TestField'];
        }
        $edit = $this->selectData(
            '*',
            'DocID=' . $docID,
            '',
            1);
		// dump($edit);die;
		$filePath= $edit[0]['DocPath'];
		//从doc目录下读取
		// $url=C('WLN_DOC_HOST_PATH');
		// $realPath = $url.$filePath;
		
		// $url=dirname(dirname($_SERVER['DOCUMENT_ROOT'])).('/doc');
		// $realPath = $url.$filePath;
		
		
		//从www目录下读取
		$realPath = realpath('./') . $filePath;
		
		// dump($realPath);die;
		
		
        $gradeID=$edit[0]['GradeID'];
        $html1 = $this->excelread($realPath); //获取html数据
		//转换为一维数组并组合成字符串
		// foreach($html1 as $k=>$v){
			// // dump($v);
			// $v['A']='【题文】'.$v['A'].'123';
			// $v['B']='【答案】'.$v['B'].'456';
			// $v['C']='【解析】'.$v['C'].'789';
			// $v['D']='【题型】'.$v['D'].'951';
			// $v['E']='【备注】'.$v['E'].'223';
		// }
		// $html=implode('', $v);
		// dump($html1);die;
		$nameArr = array();
		foreach($html1 as $k=>$v){
			
			$nameArr[] = array_values(array_slice($v,0,5));
			
			
		}
		// dump($nameArr);die;
		
		//分割文档
        // $arrDoc = $this->division($html, $start,1); //分割
        // $arrHtml = $this->division($html, $start,2); //分割
		
        // $newArrDoc = $this->changeArrFormatDoc($arrDoc); //doc过滤
		
        // $newArr = $this->changeArrFormat($arrHtml); //html过滤
		
        // if(count($newArrDoc)!=count($newArr)){
            // return '30717'; //您提取的文档标签有误!
        // }
		
        $testFieldArr = $this->getTestField(); //数据表字段对应数组
		//！取反，如果$idList存在 i++
		
		$newArr=$nameArr;
		
        if(!$idList){
			
            foreach($newArr as $i=>$iNewArr){
				
                $idList[]=$i+1;
            }
        }
		// dump($idList);die;
        foreach ($idList as $iIdList) {
            $data = array ();
            $data['DocID'] = $docID;
            $tmpIdn=$iIdList;
            if($iIdList<10) $tmpIdn='0'.$tmpIdn;
            $data['NumbID'] = $docID . '-' . $tmpIdn;

            //是否提取过，如果提取过则覆盖
            $testNumb = $this->selectData(
                'TestID',
                'NumbID="' . $data['NumbID'] . '"',
                '',
                '',
                'Test');
			
            if ($testNumb) {
                $data['TestID'] = $testNumb[0]['TestID'];
                $testReplace[]=$testNumb[0]['TestID'];
            }else{
                //是否入库过，如果入库过则跳过
                $testNumb = $this->selectData(
                    'TestID',
                    'NumbID="' . $data['NumbID'] . '"',
                    '',
                    '',
                    'TestReal');
                if ($testNumb) {
                    continue;
                }
            }
            //单条数据记录
            $tmpArr=array();
            $dataX = array ();
            foreach ($newArr[$iIdList -1] as $j => $jNewArr) {
                if(!strstr($testField[$j],'属性')){
                    $data[$testFieldArr[$testField[$j]]] = $jNewArr;
                    //设置路径后写入preg_replace('/src="([^\.]*)\.files/is','src="'.$http.'\\1.files',$str);
                    $dataX['Doc' . $testFieldArr[$testField[$j]]] = $newArr[$iIdList -1][$j];
                    if($testFieldArr[$testField[$j]]=='Test') $tmpArr['Test']=$jNewArr;
                    if($testFieldArr[$testField[$j]]=='Answer') $tmpArr['Answer']=$jNewArr;
                }
                else{
                    $attr[$testFieldArr[$testField[$j]]]=preg_replace('/<[^>]*>/is','',$jNewArr); //只保留汉字
                }
            }
			// dump($dataX);die;
            $testVal=$data['Test'];
            if (!empty ($data['TestID'])) {
                $testID = $data['TestID'];
                $this->getModel('Test')->updateData(
                    $data,
                    'TestID='.$testID);
            } else
                if (!empty ($data)) {
                    $testID = $this->getModel('Test')->insertData(
                        $data);
                }

            //添加Testdoc
            $dataX['TestID'] = $testID;
			// dump($testID);die;
            $testNumb = $this->selectData(
                '*',
                'TestID="'.$testID.'"',
                '',
                '',
                'TestDoc');
			
            if ($testNumb) {
                $this->getModel('TestDoc')->updateData(
                    $dataX,
                    'TestID="'.$testID.'"');
            } else {
                $this->getModel('TestDoc')->insertData(
                    $dataX);
            }

            //添加学科属性
            $data = array ();
            $data['DocID'] = $docID;
            $data['NumbID'] = $docID . '-' . $tmpIdn;
            $data['Status'] = 1;
            $data['SubjectID'] = $edit[0]['SubjectID'];
            $data['AatTestStyle'] = $edit[0]['AatTestStyle'];
            $data['ShowWhere'] = $edit[0]['ShowWhere'];
            $testNumb = $this->selectData(
                '*',
                'TestID=' . $testID,
                '',
                '',
                'TestAttr');
            if ($testNumb) {
                if(empty($testNumb['Admin'])) $data['Admin'] = $userName;
                $this->getModel('TestAttr')->updateData(
                    $data,
                    'TestID=' . $testID);
            } else {
                $data['TestID'] = $testID;
                //获取题型$attr['attr_types'].
                $typesId=0;
                $typesSubject=SS('typesSubject');
                $typesTmp='';
                foreach($typesSubject[$edit[0]['SubjectID']] as $iTypesSubject){
                    if($iTypesSubject['TypesName']==$attr['attr_types']){
                        $typesTmp=$iTypesSubject;
                    }
                }
                if($typesTmp){
                    $typesId=$typesTmp['TypesID'];
                }else{
                    $typesName='';

                    $buffer=$typesSubject[$edit[0]['SubjectID']];
                    foreach($buffer as $iBuffer){
                        if(strstr($attr['attr_types'],$iBuffer['TypesName'])){
                            if(mb_strlen($iBuffer['TypesName'],'UTF-8')>mb_strlen($typesName,'UTF-8')){
                                $typesId=$iBuffer['TypesID'];
                                $typesName=$iBuffer['TypesName'];
                            }
                        }
                    }
                }

                //如果没有提取过判断试题的测试类型
                $ifChoose=0;
                $testNum=0;
                $tmpTest=$this->getModel('Test')->judgeChoose($tmpArr);
                if(is_array($tmpTest)){
                    //记录试题的复合题测试类型
                    $ifChoose=$tmpTest[0];
                    $testStyle=0;
                    $dataJ=array();
                    foreach($tmpTest[1] as $j=>$jTmpTest){
                        if($testStyle==0){
                            if($jTmpTest==0) $testStyle=3;
                            else $testStyle=1;
                        }else{
                            if($jTmpTest==0){
                                if($testStyle==3) $testStyle=3;
                                else $testStyle=2;
                            }else{
                                if($testStyle==3) $testStyle=2;
                                else $testStyle=1;
                            }
                        }
                        $dataJ['TestID']=$testID;
                        $dataJ['OrderID']=$j+1;
                        $dataJ['IfChoose']=$jTmpTest;
                        $this->getModel('TestJudge')->insertData(
                            $dataJ);
                    }
                    $testNum=count($tmpTest[1]);

                    $optionWidthArr = $this->getModel('Test')->getOptionWidth($testVal);
                    $tmpWidth=array();
                    $tmpNum=array();
                    if($optionWidthArr) {
                        foreach($optionWidthArr as $i=>$iOptionWidthArr){
                            $tmpNum[]=$iOptionWidthArr[0];
                            $tmpWidth[]=$iOptionWidthArr[1];
                        }
                    }
                    $optionWidth=implode(',',$tmpWidth);
                    $optionNum=implode(',',$tmpNum);
                    if(empty($optionWidth)) $optionWidth=0;
                    if(empty($optionNum)) $optionNum=0;
                }else{
                    $ifChoose=$tmpTest;
                    $testStyle=1;
                    if($ifChoose==0){
                        $testStyle=3;
                        $optionWidth=0;
                        $optionNum=0;
                    }else{
                        $strInfo = $this->getModel('Test')->getOptionWidth($testVal);
                        if($strInfo[0]){
                            $optionNum=$strInfo[0][0];
                            $optionWidth=$strInfo[0][1];
                        }else{
                            $optionNum=0;
                            $optionWidth=0;
                        }
                    }
                }
                if(empty($typesId)) $typesId=0;
                $data['IfChoose'] = $ifChoose;
                $data['TypesID'] = $typesId;
                $data['SpecialID'] = 0;
                $data['TestNum'] = $testNum;
                $data['TestStyle'] = $testStyle;
                $data['OptionWidth'] = $optionWidth;
                $data['OptionNum'] = $optionNum;
                $data['GradeID'] = $gradeID;
                $data['Admin'] = $userName;
				
				// dump($data);die;
				//试题存表
                $this->getModel('TestAttr')->insertData(
                    $data);
                
				//添加知识点
                $testNumb = $this->getModel('TestKl')->selectData(
                    '*',
                    'TestID='.$testID);
                if (!$testNumb) {
                    $data = array ();
                    $data['TestID'] = $testID;
                    $data['KlID'] = 0;
                    $this->getModel('TestKl')->insertData(
                        $data);
                }
            }
            //删除替换试题记录
            if($testReplace){
                $idArr=implode(',',$testReplace);
                $buffer=$this->getModel('TestReplace')->selectData(
                    'TestID,DocPath',
                    'TestID in ('.$idArr.')');
                if($buffer){
                    foreach($buffer as $iBuffer){
                        $this->deleteAllFile($iBuffer);
                    }
                }
                $this->getModel('TestReplace')->deleteData(
                    'TestID in ('.implode(',',$testReplace).')');
            }
        }
		
		
		
	}
    //从文档转成的html中获取试题 并入库   $DocID文档id  $idList试题序号数组
    public function getTestByDoc($docID,$idList=array(),$userName=''){
        $testReplace=array();//记录需要删除的替换试题id
		
        $buffer = $this->selectData(
            '*',
            '1=1',
            'OrderID asc',
            '',
            'TestTag');
        $start = array ();
        $testField = array ();
        foreach ($buffer as $iBuffer) {
            $start[] = $iBuffer['DefaultStart'];
            $testField[] = $iBuffer['TestField'];
        }
        $edit = $this->selectData(
            '*',
            'DocID=' . $docID,
            '',
            1);
        $gradeID=$edit[0]['GradeID'];
		//获取html数据,相当于html页面源代码
        $html = $this->getDocContent($edit[0]['DocHtmlPath']); 
		// dump($html);die;
        $arrDoc = $this->division($html, $start,1); //分割
		
        $arrHtml = $this->division($html, $start,2); //分割
        $newArrDoc = $this->changeArrFormatDoc($arrDoc); //doc过滤
        $newArr = $this->changeArrFormat($arrHtml); //html过滤
		// dump($newArr);die;
        if(count($newArrDoc)!=count($newArr)){
            return '30717'; //您提取的文档标签有误!
        }
        $testFieldArr = $this->getTestField(); //数据表字段对应数组

        if(!$idList){
            foreach($newArr as $i=>$iNewArr){
                $idList[]=$i+1;
            }
        }
		// dump($idList);die;
		
		
		
        foreach ($idList as $iIdList) {
            $data = array ();
            $data['DocID'] = $docID;
            $tmpIdn=$iIdList;
            if($iIdList<10) $tmpIdn='0'.$tmpIdn;
            $data['NumbID'] = $docID . '-' . $tmpIdn;

            //是否提取过，如果提取过则覆盖
            $testNumb = $this->selectData(
                'TestID',
                'NumbID="' . $data['NumbID'] . '"',
                '',
                '',
                'Test');
            if ($testNumb) {
                $data['TestID'] = $testNumb[0]['TestID'];
                $testReplace[]=$testNumb[0]['TestID'];
            }else{
                //是否入库过，如果入库过则跳过
                $testNumb = $this->selectData(
                    'TestID',
                    'NumbID="' . $data['NumbID'] . '"',
                    '',
                    '',
                    'TestReal');
                if ($testNumb) {
                    continue;
                }
            }
            //单条数据记录
            $tmpArr=array();
            $dataX = array ();
            foreach ($newArr[$iIdList -1] as $j => $jNewArr) {
                if(!strstr($testField[$j],'属性')){
                    $data[$testFieldArr[$testField[$j]]] = $jNewArr;
                    //设置路径后写入preg_replace('/src="([^\.]*)\.files/is','src="'.$http.'\\1.files',$str);
                    $dataX['Doc' . $testFieldArr[$testField[$j]]] = $newArrDoc[$iIdList -1][$j];
                    if($testFieldArr[$testField[$j]]=='Test') $tmpArr['Test']=$jNewArr;
                    if($testFieldArr[$testField[$j]]=='Answer') $tmpArr['Answer']=$jNewArr;
                }
                else{
                    $attr[$testFieldArr[$testField[$j]]]=preg_replace('/<[^>]*>/is','',$jNewArr); //只保留汉字
                }
            }
            $testVal=$data['Test'];
            if (!empty ($data['TestID'])) {
                $testID = $data['TestID'];
                $this->getModel('Test')->updateData(
                    $data,
                    'TestID='.$testID);
            } else
                if (!empty ($data)) {
                    $testID = $this->getModel('Test')->insertData(
                        $data);
                }

            //添加Testdoc
            $dataX['TestID'] = $testID;
            $testNumb = $this->selectData(
                '*',
                'TestID="'.$testID.'"',
                '',
                '',
                'TestDoc');
            if ($testNumb) {
                $this->getModel('TestDoc')->updateData(
                    $dataX,
                    'TestID="'.$testID.'"');
            } else {
                $this->getModel('TestDoc')->insertData(
                    $dataX);
            }

            //添加学科属性
            $data = array ();
            $data['DocID'] = $docID;
            $data['NumbID'] = $docID . '-' . $tmpIdn;
            $data['Status'] = 1;
            $data['SubjectID'] = $edit[0]['SubjectID'];
            $data['AatTestStyle'] = $edit[0]['AatTestStyle'];
            $data['ShowWhere'] = $edit[0]['ShowWhere'];
            $testNumb = $this->selectData(
                '*',
                'TestID=' . $testID,
                '',
                '',
                'TestAttr');
            if ($testNumb) {
                if(empty($testNumb['Admin'])) $data['Admin'] = $userName;
                $this->getModel('TestAttr')->updateData(
                    $data,
                    'TestID=' . $testID);
            } else {
                $data['TestID'] = $testID;
                //获取题型$attr['attr_types'].
                $typesId=0;
                $typesSubject=SS('typesSubject');
                $typesTmp='';
                foreach($typesSubject[$edit[0]['SubjectID']] as $iTypesSubject){
                    if($iTypesSubject['TypesName']==$attr['attr_types']){
                        $typesTmp=$iTypesSubject;
                    }
                }
                if($typesTmp){
                    $typesId=$typesTmp['TypesID'];
                }else{
                    $typesName='';

                    $buffer=$typesSubject[$edit[0]['SubjectID']];
                    foreach($buffer as $iBuffer){
                        if(strstr($attr['attr_types'],$iBuffer['TypesName'])){
                            if(mb_strlen($iBuffer['TypesName'],'UTF-8')>mb_strlen($typesName,'UTF-8')){
                                $typesId=$iBuffer['TypesID'];
                                $typesName=$iBuffer['TypesName'];
                            }
                        }
                    }
                }

                //如果没有提取过判断试题的测试类型
                $ifChoose=0;
                $testNum=0;
                $tmpTest=$this->getModel('Test')->judgeChoose($tmpArr);
                if(is_array($tmpTest)){
                    //记录试题的复合题测试类型
                    $ifChoose=$tmpTest[0];
                    $testStyle=0;
                    $dataJ=array();
                    foreach($tmpTest[1] as $j=>$jTmpTest){
                        if($testStyle==0){
                            if($jTmpTest==0) $testStyle=3;
                            else $testStyle=1;
                        }else{
                            if($jTmpTest==0){
                                if($testStyle==3) $testStyle=3;
                                else $testStyle=2;
                            }else{
                                if($testStyle==3) $testStyle=2;
                                else $testStyle=1;
                            }
                        }
                        $dataJ['TestID']=$testID;
                        $dataJ['OrderID']=$j+1;
                        $dataJ['IfChoose']=$jTmpTest;
                        $this->getModel('TestJudge')->insertData(
                            $dataJ);
                    }
                    $testNum=count($tmpTest[1]);

                    $optionWidthArr = $this->getModel('Test')->getOptionWidth($testVal);
                    $tmpWidth=array();
                    $tmpNum=array();
                    if($optionWidthArr) {
                        foreach($optionWidthArr as $i=>$iOptionWidthArr){
                            $tmpNum[]=$iOptionWidthArr[0];
                            $tmpWidth[]=$iOptionWidthArr[1];
                        }
                    }
                    $optionWidth=implode(',',$tmpWidth);
                    $optionNum=implode(',',$tmpNum);
                    if(empty($optionWidth)) $optionWidth=0;
                    if(empty($optionNum)) $optionNum=0;
                }else{
                    $ifChoose=$tmpTest;
                    $testStyle=1;
                    if($ifChoose==0){
                        $testStyle=3;
                        $optionWidth=0;
                        $optionNum=0;
                    }else{
                        $strInfo = $this->getModel('Test')->getOptionWidth($testVal);
                        if($strInfo[0]){
                            $optionNum=$strInfo[0][0];
                            $optionWidth=$strInfo[0][1];
                        }else{
                            $optionNum=0;
                            $optionWidth=0;
                        }
                    }
                }
                if(empty($typesId)) $typesId=0;
                $data['IfChoose'] = $ifChoose;
                $data['TypesID'] = $typesId;
                $data['SpecialID'] = 0;
                $data['TestNum'] = $testNum;
                $data['TestStyle'] = $testStyle;
                $data['OptionWidth'] = $optionWidth;
                $data['OptionNum'] = $optionNum;
                $data['GradeID'] = $gradeID;
                $data['Admin'] = $userName;
				//试题存表
                $this->getModel('TestAttr')->insertData(
                    $data);
                //添加知识点
                $testNumb = $this->getModel('TestKl')->selectData(
                    '*',
                    'TestID='.$testID);
                if (!$testNumb) {
                    $data = array ();
                    $data['TestID'] = $testID;
                    $data['KlID'] = 0;
                    $this->getModel('TestKl')->insertData(
                        $data);
                }
            }
            //删除替换试题记录
            if($testReplace){
                $idArr=implode(',',$testReplace);
                $buffer=$this->getModel('TestReplace')->selectData(
                    'TestID,DocPath',
                    'TestID in ('.$idArr.')');
                if($buffer){
                    foreach($buffer as $iBuffer){
                        $this->deleteAllFile($iBuffer);
                    }
                }
                $this->getModel('TestReplace')->deleteData(
                    'TestID in ('.implode(',',$testReplace).')');
            }
        }
    }
    //从文档转成的html中获取试题 返回试题数组
    public function showTestByDoc($docID){
        $edit = $this->selectData(
            '*',
            'DocID='. $docID,
            '',
            '1');

        $buffer = $this->getModel('TestTag')->selectData(
            '*',
            '',
            'OrderID asc');
        $start = array ();
        foreach ($buffer as $iBuffer) {
            $start[] = $iBuffer['DefaultStart'];
        }
        $html = $this->getDocContent($edit[0]['DocHtmlPath']); //获取html数据

        $arr_doc = $this->division($html, $start,1); //分割
        $arrHtml = $this->division($html, $start,2); //分割
        //$newarr_doc = $this->changeArrFormatDoc($arr_doc); //doc过滤
        $newArr = $this->changeArrFormat($arrHtml); //html过滤

        return $newArr;
    }
    /**
     * 根据DocID获取试卷相关信息
     * @param $docId int DocID
     * @return array 单条数据信息
     */
    public function getDocByID($docId){
        $result = $this->selectData(
            'DocID,DocName',
            'DocID='.$docId);
        return $result[0];
    }
    /**
     * 获取doc索引
     * @param $field array 获取字段数组 例如 array(0=>'docid',1=>'docname')
     * @param $where array 查询条件数组 例如 array('DocID'=>'1,2,3','DocYear'=>2013)
     * @param $order array 排序数组 例如 array(0=>'@docid DESC',1=>'IntroTime DESC')
     * @param $page array 分页信息数组        perpage 每页几个 page当前页数 limit 返回总数最大数量
     * @return array 0:doc数据 1:数据总数 2:每页几个
     */
    public function getDocIndex($field,$where,$order,$page){
        $index=$this->getModel('Index');
        $index->initDoc();
        return $index->getDocIndex($field,$where,$order,$page);
    }

    /**
     * 删除文档id索引
     * @param int $docID 文档id 支持以逗号间隔
     * @author demo
     */
    public function deleteIndex($docID){
        $index=$this->getModel('Index');
        $index->initDoc();
        $index->deleteIndex($docID, array(2), array('ifintro'));
    }

    /**
    * 信息统计
    * @param array $params 查询参数
    * @param int $limit 分页限定
    * @return array
    * @author demo
    */
    public function stat($params,$limit=20){
        $day = 60*60*24;
        $subject = false;
        $start = strtotime(date('Y-m-d'));
        $end = time();
        $page = 1;
        if(!empty($params)){
            if($params['SubjectID']){
              $subject = (int)$params['SubjectID'];
            }
            if($params['date']){
              $start = $params['date'];
            }
            if($params['dateEnd']){
              $end = $params['dateEnd']+$day;
            }
            if($params['p']){
              $page = $params['p'];
            }
        }
        if($start >= $end){
            $start = $end-$day;
        }
        $where['IntroFirstTime']['start'] = $start;
        $where['IntroFirstTime']['end'] = $end;
        if($subject){
            $where['SubjectID'] = $subject;
        }
        $page = array('page'=>$page,'perpage'=>$limit);
        $field=array('docid','docname','subjectname','typename','docyear','areaname','loadtime','introfirsttime','introtime');
        $order = array('subjectid asc,introfirsttime desc,docid desc');
        $result = $this->getDocIndex($field,$where,$order,$page);
        import('ORG.Util.Page'); // 导入分页类
        $pagtion = handlePage('init',(int)$result[1],$limit); // 实例化分页类 传入总记录数和每页显示的记录数

        $page = page((int)$result[1],$page,$limit);
        return array(
            'data'=>$result[0],
            'page'=>$pagtion->show()
        );
    }
    /**
     * 为第一次提取的试题设置文档章节
     * @param int $docID 文档ID
     * @param array $id 试题序号
     * @return none
     * @author demo
     */
    public function setDefaultChapter($docID,$id){
        //检查是否需要添加章节数据
        //获取文档对应章节
        $arr=$this->getModel('DocAbiCapt')->selectData(
            '*',
            'DocID='.$docID);
        if($arr && count($arr)==1){
            $chapterID=$arr[0]['CaptID'];
            $buffer=$this->getModel('TestAttr')->selectData(
                '*',
                'DocID='.$docID);
            if(count($id)==count($buffer)){
                $testList=array();
                foreach($buffer as $i=>$iBuffer){
                    $testList[]=$iBuffer['TestID'];
                }

                $buffer=$this->getModel('TestChapter')->selectData(
                    '*',
                    'TestID in ('.implode(',',$testList).')');
                if(!$buffer){
                    //插入文档对应章节数据
                    $data=array();
                    foreach($testList as $i=>$iTestList){
                        $data[$i]['TestID']=$iTestList;
                        $data[$i]['ChapterID']=$chapterID;
                    }
                    $this->getModel('TestChapter')->addAllData($data);
                }
            }
        }
    }

    /**
     * 为试题设置提分测试类型
     * @param int $docID 文档ID
     * @param int $value 类型值
     * @return none
     * @author demo
     */
    public function setAatTestStyle($docID,$value){
        $this->getModel('TestAttr')->updateData(array('AatTestStyle'=>$value),'DocID ='.$docID);
        $this->getModel('TestAttrReal')->updateData(array('AatTestStyle'=>$value),'DocID ='.$docID);
    }

    /**
     * 以文档id获取文档列表
     * @param int $docID 文档id
     * @return array 返回以文档id为键的数组
     * @author demo
     */
    public function getDocListByID($docID){
        //兼容数组和字符串
        if(is_array($docID)) $docID=implode(',',$docID);

        //获取试题id对应数据集
        $testListArray = $this->selectData(
            '*',
            'DocID in ('.$docID.')'
        );

        //以试题id为键值
        $testListArrayByID=array();
        foreach($testListArray as $iTestListArray){
            $testListArrayByID[$iTestListArray['DocID']]=$iTestListArray;
        }
        return $testListArrayByID;
    }

    /**
     * 处理普通pdf答题卡
     * @param string $sheetXml 试卷cookie数据
     * @param string $sheetChoice 答题卡类型
     * @param string $docVersion 扩展名
     * @return array [1,url地址,pdf名称,答题卡配置数组]
     * @author demo
     */
    public function arswerPdf($sheetXml,$sheetChoice,$docVersion,$style){
        //获取默认答题卡数据
        $sheetFormat=$this->getModel('SheetFormat');
        if($style=='b') $style=1;
        else $style=0;
        $sheetFormat->getChooseStyle($style);
        $buffer = $sheetFormat->getSheetFormat(array('CookieStr'=>$sheetXml,'SheetChoice'=>strtoupper($sheetChoice)));

        if($buffer[0]==1){
            //答题卡数据是否有已存在历史下载
            $hash=md5(serialize($buffer[1]));
            $docDown=$this->getModel('DocDown');
            $bufferDown=$docDown->findData('DocPath,DocName','CookieHash="'.$hash.'"');
            if($bufferDown){
                //return [1,$bufferDown['DocPath'],$bufferDown['DocName'],$buffer[1]];
            }

            return $this->arswerMyPdf($buffer[1],$docVersion);
        }
        return $buffer;
    }

    /**
     * 处理自定义pdf答题卡
     * @param array $sheetXml 答题卡数组数据
     * @param string $docVersion 扩展名
     * @return array [1,url地址,pdf名称,答题卡配置数组]
     * @author demo
     */
    public function arswerMyPdf($sheetXml,$docVersion){
        //获取pdf内容
        $toPdf=$this->getModel('ToPDF');
        $pdfStr=$toPdf->index($sheetXml);

        //存到文件服务器
        $host = C('WLN_DOC_HOST');
        if ($host) {
            $urlPath = R('Common/UploadLayer/setPdfDocument',array($pdfStr, $docVersion));
            if(strstr($urlPath,'error')){
                $data=array();
                $data['description'] = '答题卡Pdf版内容存储失败，答题卡生成失败';
                $data['msg'] = serialize($sheetXml);
                $this->addErrorLog($data);
                return [0,"<script>
                $('#dtktishi').html('<b style=\"color:red;align:center;\">答题卡文档路径生成失败，请重试！</b>');
                </script>"];
            }
        } else {
            $hostIn = C('WLN_HTTP') . '/';
            $urlPath = '/Uploads/mht/'.date('Y/md/',time()) . uniqid(mt_rand()) . rand(100, 999) . '.pdf';
            $docPath = realpath('./') . $urlPath;
            $sizeLength=file_put_contents(iconv('UTF-8', 'GBK//IGNORE', $docPath), $str);
            if($sizeLength==0){
                $data=array();
                $data['description'] = '答题卡信息写入文档失败，答题卡生成失败';
                $data['msg'] = serialize($sheetXml);
                $this->addErrorLog($data);
                return [0, "<script>
                $('#dtktishi').html('<b style=\"color:red;align:center;\">答题卡信息写入文档失败，请重试！</b>');
                </script>"];
            }
            unlink($docPath);
        }
        return [1,$urlPath,$sheetXml['title']['content'],$sheetXml];
    }


    /**
     * 根据存档id获取答题卡对应数据
     * @param array $saveID 存档id
     * @return array
     * @author demo
     */
    private function getCardBySaveID($saveID){
        if(!is_numeric($saveID)){
            return [0,'数据标示有误。'];
        }

        //答题卡数据
        $docCard=$this->getModel('DocCard');
        $bufferCard=$docCard->findData('DataStr,UserID,SubjectID,AnswerName','SaveID='.$saveID);
        if(!$bufferCard){
            return [0,'数据有误。'];
        }
        return [1,$bufferCard];
    }

    /**
     * 获取答题卡对应图片
     * @param array $saveID 存档id
     * @param int $ifAb 是否是ab卷默认0不是  1是
     * @return array [1,[
     *      0=>[
     *          0=>'http://aaaaa.aaaa', //第一页
     *          1=>'http://aaaaa.aaaa'
     *      ]//a卷
     *      1=>[
     *          0=>'http://aaaaa.aaaa', //第一页
     *          1=>'http://aaaaa.aaaa'
     *      ]//b卷 如果统一答题卡则没有此数据
     *  ]]
     * @author demo
     */
    public function answerPdfImg($saveID,$ifAb=0){
        //获取存档对应答题卡数据集
        $tmp=$this->getCardBySaveID($saveID);
        if($tmp[0]==0){
            return $tmp;
        }
        $bufferCard=$tmp[1];

        //生成答题卡pdf
        $bufferDown=$this->answerPdfCreate($bufferCard,$ifAb);

        //如果存在判断图片是否存在
        $output=array();
        $tmp=$this->answerPdfCheckImg($bufferDown[0]['DocPath']);
        if($tmp[0]==0) return $tmp;
        $output[]=$tmp[1];
        if($ifAb==1){
            $tmp=$this->answerPdfCheckImg($bufferDown[1]['DocPath']);
            if($tmp[0]==0) return $tmp;
            $output[]=$tmp[1];
        }

        //返回图片数据
        return [1,$output];
    }

   /**
     * 获取答题卡pdf路径，如果没有则生成
     * @param array $bufferCard 答题卡数据集
     * @param int $ifAb 是否是ab卷默认0不是  1是
     * @return array array(
    *       0=>[//a卷
    *           'DocPath'=>pdf文档路径
    *           'DocName'=>文档名称
    *       ]
    *       1=>[//卷
    *           'DocPath'=>pdf文档路径
    *           'DocName'=>文档名称
    *       ]
    *   )
     * @author demo
     */
    private function answerPdfCreate($bufferCard,$ifAb=0){
        //获取cookieHash是否存在
        $len=1; //答题卡对应卷数量
        $hash=array(md5($bufferCard['DataStr']));
        if($ifAb==1){
            //增加一个hash
            $bDataStr = unserialize($bufferCard['DataStr']);
            $bDataStr = $this->getModel('SheetFormat')->changeChooseStyleByArray($bDataStr,$ifAb);
            $bDataStr = serialize($bDataStr);
            $hash[]=md5($bDataStr);
            $len=2; //答题卡对应卷数量
        }
        $docDown=$this->getModel('DocDown');
        //兼容数据实时生成，正式上线要改正
        $bufferDown1=$docDown->findData('DocPath,DocName,CookieHash','CookieHash in ("'.$hash[0].'")','DownID DESC');
        if($hash[1]) $bufferDown2=$docDown->findData('DocPath,DocName,CookieHash','CookieHash in ("'.$hash[1].'")','DownID DESC');
        if($bufferDown1) $bufferDown[]=$bufferDown1;
        if($bufferDown2) $bufferDown[]=$bufferDown2;

        if(!$bufferDown || count($bufferDown)<$len){
            if(!$bufferDown) $bufferDown=array();

            $cookieHash=array();
            if($bufferDown){
                foreach($bufferDown as $iBufferDown){
                    $cookieHash[]=$iBufferDown['CookieHash'];
                }
            }

            //获取用户名
            $userBuffer=$this->getModel('User')->getInfoByID($bufferCard['UserID'],'UserName');
            //a卷
            if(!in_array($hash[0],$cookieHash)){
                $tmp=$this->answerCreateRecord($bufferCard['DataStr'],$bufferCard['SubjectID'],$userBuffer['UserName']);
                if($tmp[0]==0) return $tmp;
                $tmp[1]['CookieHash']=$hash[0];
                $bufferDown[]=$tmp[1];
            }

            //b卷
            if($ifAb==1 && !in_array($hash[1],$cookieHash)){
                $tmp=$this->answerCreateRecord($bDataStr,$bufferCard['SubjectID'],$userBuffer['UserName']);
                if($tmp[0]==0) return $tmp;
                $tmp[1]['CookieHash']=$hash[1];
                $bufferDown[]=$tmp[1];
            }
        }

        //调整ab卷顺序
        if($bufferDown[0]['CookieHash']!=$hash[0]){
            $tmp=$bufferDown[0];
            $bufferDown[0]=$bufferDown[1];
            $bufferDown[1]=$tmp;
        }

        return $bufferDown;
    }

    /**
     * 生成pdf并保存历史下载
     * @param string $dataStr 答题卡格式数据集合 json字符串
     * @param int $subjectID 学科id
     * @param string UserName 用户名
     * @return array [1,['DocPath'=>'文档路径','DocName'=>'文档名称']
     * @author demo
     */
    private function answerCreateRecord($dataStr,$subjectID,$userName){
        //生成pdf下载数据
        $result=$this->arswerMyPdf(unserialize($dataStr),'pdf');
        if($result[0]==0){
            return [0,'生成pdf失败。'];
        }
        $bufferDown=array();
        $bufferDown['DocPath']=$result[1];
        $bufferDown['DocName']=$result[2];

        //生成存档信息
        $result=$this->saveArswerResult($userName,$result[1],$result[2],$subjectID,$dataStr);
        if($result[0]==0){
            return [0,'生成记录信息失败。'];
        }

        return [1,$bufferDown];
    }

    /**
     * 检查pdf生成图片
     * @param string $pdfPath 答题卡路径
     * @param int $ifCreate 如果没有是否生成 默认0不生成 1生成
     * @return array [1,[
     *  ]]
     * @author demo
     */
    private function answerPdfCheckImg($pdfPath){
        $host=C('WLN_DOC_HOST_IN');
        //获取答题卡对应图像数量
        $url = $host.R('Common/UploadLayer/getDocServerUrl',array($pdfPath,'pdf2img','pdf'));
        $result=file_get_contents($url);
        $tmp=explode('|',$result);
        if($tmp[0]!='success'){
            $data=array();
            $data['description'] = '答题卡Pdf获取图像失败.';
            $data['msg'] = '答题卡路径：'.$pdfPath.'<br/>返回结果：'.$result;
            $this->addErrorLog($data);
            return [0,"获取答题卡图像失败."];
        }
        $num=$tmp[1]; //答题卡图像数量

        $fix='.jpg'; //图片文件扩展名
        $imgPath=substr($pdfPath, 0,strrpos($pdfPath, ".")).'/1'.$fix;

        //生成返回数据
        $output=array();
        for($i=0;$i<$num;$i++){
            $output[]=str_replace('1'.$fix,($i+1).$fix,$imgPath);
        }
        return [1,$output];
    }

    /**
     * 保存答题卡数据pdf答题卡下载
     * @param string $userName 用户名
     * @param string $urlPath 答题卡地址
     * @param string $mainTitle 文档名称
     * @param string $subjectID 学科id
     * @param string $sheetxml 答题卡配置数据字符串
     * @return string
     * @author demo
     */
    public function saveArswerResult($userName,$urlPath,$mainTitle,$subjectID,$sheetxml=''){
        $host = C('WLN_DOC_HOST');

        $cookieStr='';
        $cookieHash='';
        if(!empty($sheetxml)){
            $cookieStr=$sheetxml;
            $cookieHash=md5($cookieStr);
        }
        //保存答题卡
        $data = array();
        $data['UserName'] = $userName;
        $data['LoadTime'] = time();
        $data['DocPath'] = $urlPath;
        $data['SubjectID'] = $subjectID;
        $data['DocName'] = $mainTitle;
        $data['Point'] = 0;
        $data['IP'] = get_client_ip(0,true);
        $data['IfShow'] = 0;
        $data['DownStyle'] =4; //答题卡4 导学案3 作业2，试卷1
        $data['CookieStr'] = $cookieStr;
        $data['CookieHash'] = $cookieHash;
        $downID = $this->getModel('DocDown')->insertData($data);
        if(!$downID){
            return [0,"<script>$('#dtktishi').html('<b style=\"color:red;align:center;\">生成记录失败，请重试...</b>');</script>"];
        }
        $id = md5($downID . '(*&!@#%^&#@$)(@!^^#!%@#&*@!)');
        if (!$host) $url = U('Index/wordDownLoad?DownID=' . $downID . '&id=' . $id);
        else {
            $style='';
            if(strstr($urlPath,'pdf')) $style='pdf';
            $url=$host.R('Common/UploadLayer/getDocServerUrl',array($urlPath,'down',$style,$mainTitle));
        }
        //输出doc路径
        //echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
         //   <form id='thisform' method='post' action='" . $url . "'></form>
        //    <script>document.getElementById('dtktishi').innerHTML='答题卡已经生成，请<a target=\"_blank\" href=\'" . $url . "\'>点击这里</a>将答题卡保存到本地。';$('#hiddenfrm').attr('src','".$url."');</script>";
        return [1,"<script>$('#dtktishi').html('答题卡已经生成，请<a target=\"_blank\" href=\'" . $url . "\'>点击这里</a>将答题卡保存到本地。');$('#hiddenfrm').attr('src','".$url."');</script>"];
    }

    /**
     * 获取答题卡坐标信息
     * @param int $saveID 存档id
     * @param int $ifAb 是否是ab卷默认0不是  1是
     * @return array
     * @author demo
     */
    public function answerCoordinate($saveID,$ifAb=0){
        //获取答题卡数据
        $tmp=$this->getCardBySaveID($saveID);
        if($tmp[0]==0){
            return $tmp;
        }
        $bufferCard=$tmp[1];

        //生成答题卡pdf
        $bufferDown=$this->answerPdfCreate($bufferCard,$ifAb);

        //获取pdf内容
        $toPdf=$this->getModel('ToPDF');
        $aDataArr=unserialize($bufferCard['DataStr']);
        $arrA=$toPdf->getCoordinate($aDataArr); //A卷
        $arrA=$this->answerCutMainImg($bufferDown[0]['DocPath'],$arrA);

        //处理B卷
        if($ifAb==1){
            $bDataArr = $this->getModel('SheetFormat')->changeChooseStyleByArray($aDataArr,$ifAb);
            $arrB = $toPdf->getCoordinate($bDataArr); //B卷
            $arrB = $this->answerCutMainImg($bufferDown[1]['DocPath'],$arrB);
            $arrA = $this->answerSheetMerge($arrA,$arrB);
        }

        return [1,$arrA];
    }

    /**
     * 获取答题卡对应标示图像
     * @param array $sheetArr 答题卡数据集
     * @return array 输出带标示图的切割数组
     * @author demo
     */
    public function answerCutMainImg($pdfPath,$sheetArr){
        //获取切割图标
        $xy = serialize($sheetArr[0]['Coordinate'][0]);
        $host = C('WLN_DOC_HOST_IN');
        //获取图像信息
        $url=$host.R('Common/UploadLayer/getDocServerUrl',array($pdfPath,'pdfCutMain','pdf',$xy));
        $result=  file_get_contents($url);
        $tmp=explode('|',$result);
        if($tmp[0]!='success'){
            $data=array();
            $data['description'] = '答题卡图像标志位截取失败.';
            $data['msg'] = '答题卡路径：'.$pdfPath.'<br/>返回结果：'.$result;
            $this->addErrorLog($data);
            return [0,"答题卡图像标志位截取失败."];
        }
        unset($tmp[0]);

        //把图像信息合并到答题卡数据
        foreach($sheetArr[0]['Coordinate'][0] as $i=>$iSheetArr){
            $sheetArr[0]['Coordinate'][0][$i]['url']=$tmp[$i+1];
        }

        return $sheetArr;
    }
    /**
     * 组合ab卷坐标信息
     * @param array $arrA a卷切割信息
     * @param array $arrB b卷切割信息
     * @return array $data 组合过后的切割信息
     * @author demo
     */
    private function answerSheetMerge($arrA,$arrB){
        if(empty($arrB)) return $arrA;

        foreach($arrB as $iArrB){
            $data[$iArrB['OrderID']][] = $iArrB['Coordinate'][0];
        }

        foreach($arrA as $i=>$iArrA){
            $arrA[$i]['Coordinate'][1] = $data[$iArrA['OrderID']];
        }

        return $arrA;
    }

    /**
     * 处理普通word答题卡
     * @param string $sheetXml 试卷cookie数据
     * @param string $sheetChoice 答题卡类型
     * @param string $docVersion 扩展名
     * @author demo
     */
    public function arswerWord($sheetXml,$sheetChoice,$docVersion){
        $tmpStr = explode('@#@', $sheetXml);
        $str = '';
        $mainTitle = '答题卡';
        $juan1 = array();
        $juan2 = array();
        $two1 = array(); //非选择题题号数组
        $start = 0; //区分卷1和卷2

        $testAll=array();//记录卷1和卷2试题
        //分解字符串
        foreach ($tmpStr as $i=>$iTmpStr) {
            $tmpStr1 = explode('@$@', $iTmpStr);
            if ($start == 1 && count($tmpStr1) == 7 && $tmpStr1[5]!='0') {
                $tmpStr2 = explode(';', $tmpStr1[5]);
                foreach ($tmpStr2 as $j=>$jTmpStr2) {
                    $tmpStr3 = explode('|', $jTmpStr2);
                    $juan1[0][] = $tmpStr3[0]; //题号id
                    $juan1[1][] = $tmpStr3[1]; //小题数
                    $testAll[]=$tmpStr3[0];
                }
            } else if ($start == 2 && count($tmpStr1) == 7 && $tmpStr1[5]!='0') {
                $tmpStr2 = explode(';', $tmpStr1[5]);
                foreach ($tmpStr2 as $j=>$jTmpStr2) {
                    $tmpStr3 = explode('|', $jTmpStr2);
                    $juan2[0][] = $tmpStr3[0]; //题号id
                    $juan2[1][] = $tmpStr3[1]; //小题数
                    $testAll[]=$tmpStr3[0];
                }
            }
            if ($tmpStr1[0] == 'maintitle') $mainTitle = $tmpStr1[2] . $mainTitle;
            if (strstr($tmpStr1[0], 'parthead')) {
                $start = str_replace('parthead', '', $tmpStr1[0]);
            }
        }

        $testAll=array_filter($testAll); //排除为空的题型下试题

        //全部试题
        $testID=implode(',', $testAll);

        $count = array_sum($juan1[1])+array_sum($juan2[1]);
        //生成长度为题型一试题数目的数组
        $one = array();
        for ($io = 0; $io < $count; $io++) {
            array_push($one, 0); //0为选择题 1为非选择题 2为作文
        }

        $field = array('testid','testnum','typesid','judge','ifchoose');
        $where = array('TestID'=>$testID);
        $order = array();
        $page = array('perpage'=>100);

        //获取合并后的索引数据
        $buffer = $this->getModel('TestRealQuery')->getIndexTest($field,$where,$order,$page,0,-1);

        //是否有作文
        $typesArray = SS('types');
        $testTypeList = array(); //存储以试题id为键值的试题题型属性
        if ($buffer) {
            foreach ($buffer as $i=>$iBuffer) {
                $testTypeList[$iBuffer['testid']] = $typesArray[$iBuffer['typesid']];
            }
        }
        for ($z = 0; $z < count($testAll); $z++) {
            if (strstr($testTypeList[$testAll[$z]]['TypesName'], '作文')) {
                $one[$z]=2;
                $article=1;
            }
        }

        //建立以试题id为索引的试题属性
        $tmpChoose = array(); //键为TestId值为IfChoose 卷1
        $numChoose = array(); //键为TestId值为IfChoose 卷1 且顺序与试卷对应

        if ($buffer) {
            foreach ($buffer as $i=>$iBuffer) {
                if($iBuffer['ifchoose']==1){
                    $tmpArray=array();
                    $tmpArray=array_fill(0,$iBuffer['testnum'],0);
                    if($iBuffer['judge']){
                        foreach($iBuffer['judge'] as $j=>$jBuffer){
                            $tmpArray[$j]=$jBuffer['IfChoose'];
                        }
                    }
                    $tmpChoose[$iBuffer['testid']]=$tmpArray;
                }else{
                    $tmpChoose[$iBuffer['testid']]=$iBuffer['ifchoose']; //非复合题
                }
            }
        }

        foreach($testAll as $iTestAll){
            $numChoose[$iTestAll]=$tmpChoose[$iTestAll];
        }

        if (!empty($numChoose)) {
            $testN=0;//当前第几个题
            foreach ($numChoose as $i => $iNumChoose) {
                if (is_array($iNumChoose)) { //复合题
                    if(empty($iNumChoose)){
                        $one[$testN]=1;
                        $testN++;
                    }else{
                        foreach($iNumChoose as $j=>$jNumChoose){
                            if($jNumChoose==0) $one[$testN]=1;
                            $testN++;
                        }
                    }
                    continue;
                }
                if ($iNumChoose == 0) { //非选择题
                    $one[$testN]=1;
                    $testN++;
                    continue;
                }
                $testN++;
            }
        }
        /*
        $count = array_sum($juan1[1]); //卷1的试题数(包括小题)
        $count2 = array_sum($juan2[1]); //卷2的试题数(包括小题)
        //生成长度为题型一试题数目的数组
        $one = array();
        for ($io = 0; $io < $count; $io++) {
            array_push($one, $io);
        }
        //生成长度为题型二试题数目的数组
        $two = array();
        for ($jo = 0; $jo < $count2; $jo++) {
            array_push($two, $jo);
        }
        //是否有作文
        $testID2 = implode(',', $juan2[0]);
        $buffer = array();
        if(0 == $classify){
            $buffer = $this->dbConn->selectData(
                'TestAttrReal',
                '*',
                'TestID in (' . $testID2 . ')'
            );
        }else{
            $buffer = D('CustomTestAttr')->getTestAttributes($testID2);
        }
        $types_array = SS('types');
        $testList = array();
        if ($buffer) {
            foreach ($buffer as $i=>$iBuffer) {
                $testList[$iBuffer['TestID']] = $types_array[$iBuffer['TypesID']];
            }
        }
        for ($z = 0; $z < count($juan2[0]); $z++) {
            if (strstr($testList[$juan2[0][$z]]['TypesName'], '作文')) {
                array_pop($two);
                $article=1;
            }
        }
        //查找卷1对应的题型
        $testID1 = implode(',', $juan1[0]); //144259,54273,48652,92774,9780
        $attrone = array();//复合题属性
        $attrAll = array();//所有试题属性
        if(0 == $classify){
            $attrone = $this->dbConn->selectData(
                'TestJudge',
                '*',
                'TestID in (' . $testID1 . ')',
                'TestID ASC,OrderID ASC'
            );//第一卷的试题复合题属性
            $attrAll = $this->dbConn->selectData('TestAttrReal','*','TestID in (' . $testID1 . ')');//第一卷的试题属性
        }else{
            $attrone = D('CustomTestJudge')->getTestJudges($testID1);
        }
        //建立以试题id为索引的试题属性
        $num = array(); //复合题 键为TestId值为IfChoose 卷1
        $numAll = array(); //所有 键为TestId值为IfChoose 卷1
        $num1 = array(); //键为TestId值为IfChoose 卷1 且顺序与juan[0]对应

        if ($attrone) {
            foreach ($attrone as $attroneval) {
                $num[$attroneval['TestID']][] = $attroneval['IfChoose']; //键为TestId值为IfChoose 卷1
            }
            foreach ($attrAll as $iAttrAll) {
                $numAll[$iAttrAll['TestID']] = $iAttrAll['IfChoose']; //键为TestId值为IfChoose 卷1
            }
            foreach ($juan1[0] as $value) {
                $num1[$value]=$num[$value];
            }
        }
        if (!empty($num1)) {
            foreach ($num1 as $i => $numi) {
                if ($numi == 0) { //非选择题
                    $qida = 0;
                    $fei = array_search($i, $juan1[0]); //找到TestID为$i在$juan1[0]的下标,计算题号
                    for ($n = 0; $n < $fei; $n++) { //统计前面的试题数(包括小题)
                        $qida += $juan1[1][$n]; //该复合题在试卷中的题号
                    }
                    unset($one[$qida]); //删除该复合题中不是选择题的题号
                    array_push($two1, $qida); //在非选择题题号数组中加上该题的题号
                }
                if ($numi == 1) { //复合题
                    $qidb = 0;
                    $fuhe1 = array_search($i, $juan1[0]); //找到TestID为$i在$juan1[0]的下标,计算题号
                    for ($n = 0; $n < $fuhe1; $n++) { //统计前面的试题数(包括小题)
                        $qidb += $juan1[1][$n]; //该复合题在试卷中的题号
                    }
                    if(0 == $classify)
                        $bufferju1 = $this->dbConn->selectData(
                            'TestJudge',
                            'IfChoose,OrderID',
                            'TestID ="' . $i . '"'
                        );
                    else{
                        $bufferju1 = D('CustomTestJudge')->getTestJudges($i);
                    }
                    foreach ($bufferju1 as $j => $bufj) {
                        if ($bufj['IfChoose'] == 0) { //该复合题中第几个不是选择题
                            $numj = $bufj['OrderID'] + $qidb - 1; //该小题的题号
                            unset($one[$numj]); //删除该复合题中不是选择题的题号
                            array_push($two1, $numj); //在非选择题题号数组中加上该题的题号
                        }
                    }
                }
            }
        }
        //查找卷2对应的题型
        $attrtwo = array();
        if(0 == $classify){
            $attrtwo = $buffer;//第二卷的试题属性
        }else{
            $attrtwo = D('CustomTestJudge')->getTestJudges($testID2);
        }
        $num2 = array(); //键为TestId值为IfChoose 卷2
        $numt = array(); //键为TestId值为IfChoose 卷2 且顺序与$juan2[0]对应
        if ($attrtwo) {
            foreach ($attrtwo as $attrk) {
                $num2[$attrk['TestID']] = $attrk['IfChoose']; //键为TestId值为IfChoose 卷2
            }
            foreach ($juan2[0] as $value) {
                $numt[$value]=$num2[$value];
            }
        }
        if (!empty($numt)) {
            foreach ($numt as $i => $numval) {
                if ($numval > 1) { //选择题
                    $qidc = 0;
                    $xuze = array_search($i, $juan2[0]); //找到TestID为$i在$juan2[0]的下标,计算题号
                    for ($j = 0; $j < $xuze; $j++) { //统计前面的试题数(包括小题)
                        $qidc += $juan2[1][$j]; //该选择题在试卷2中的题号
                    }
                    unset($two[$qidc]); //删除该选择题的题号
                    array_push($one, ($qidc+$count)); //在选择题数组中加上该题的题号,$count为卷一的题数
                }
                if ($numval == 1) { //复合题
                    $qidd = 0;
                    $fuhe2 = array_search($i, $juan2[0]); //找到TestID为$i在$juan2[0]的下标,计算题号
                    for ($k = 0; $k < $fuhe2; $k++) { //统计前面的试题数(包括小题)
                        $qidd += $juan2[1][$k]; //该复合题在试卷2中的题号
                    }
                    if(0 == $classify){
                        $bufferju2 = $this->dbConn->selectData(
                            'TestJudge',
                            'IfChoose,OrderID',
                            'TestID ="' . $i . '"'
                        );
                    }else{
                        $bufferju2 = D('CustomTestJudge')->getTestJudges($i);
                    }
                    foreach ($bufferju2 as $m => $bufm) {
                        if ($bufm['IfChoose'] > 1) { //该复合题中第几个是选择题
                            $num3 = $bufm['OrderID'] + $qidd - 1;
                            unset($two[$num3]); //删除该复合题中是选择题的题号
                            array_push($one, ($num3 + $count)); //在选择题数组中加上该题的题号,$count为卷一的题数
                        }
                    }
                }
            }
        }*/
        $oneList = array();
        $two1 = array();
        foreach ($one as $i=>$iOne) {
            if($iOne==0) $oneList[] = $i;
            if($iOne==1) $two1[] = $i;
        }
        $countX = count($oneList); //选择题的个数
        $countF = count($two1); //非选择题的个数

        switch ($sheetChoice) {
            case 1:
                $str = "<p class=MsoNormal align=center style='margin:7.8pt 0cm 7.8pt 0cm;text-align:center'><span lang=EN-US
style='font-size:14.0pt;mso-bidi-font-size:11.0pt;font-family:黑体;'>" . $mainTitle . '</span></p>';
                $str .= "<p class=MsoNormal align=center style='text-align:center'><span
style='font-family:黑体'>学校<span lang=EN-US>:___________</span>姓名：<span
lang=EN-US>___________</span>班级：<span lang=EN-US>___________</span>考号：<span
lang=EN-US>___________<o:p></o:p></span></span></p><p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
<p class=MsoNormal><span style='font-size:12.0pt;mso-bidi-font-size:11.0pt;
font-family:黑体'>选择题（请用<span lang=EN-US>2B</span>铅笔填写）</span></p>";
                $str1 = '';
                $str2 = '';
                if ($countX > 0) {
                    $str .= "<table class=MsoNormalTable border=1 cellspacing=0 cellpadding=0
     style='border-collapse:collapse;border:none;mso-border-alt:solid black .5pt;
     mso-border-themecolor:text1;mso-yfti-tbllook:1184;mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
     mso-border-insideh:.5pt solid black;mso-border-insideh-themecolor:text1;
     mso-border-insidev:.5pt solid black;mso-border-insidev-themecolor:text1'>";
                    for ($i = 0; $i < $countX; $i++) {
                        if ($i % 10 == 0) {
                            if ($i != 0) $str .= $str1 . '</tr>' . $str2 . '</tr>';
                            $str1 = '<tr>';
                            $str2 = '<tr>';
                        }
                        $str1 .= "<td width=57 valign=top style='width:42.55pt;border:solid black 1.0pt;
      mso-border-themecolor:text1;mso-border-alt:solid black .5pt;mso-border-themecolor:
      text1;background:#F2F2F2;mso-background-themecolor:background1;mso-background-themeshade:
      242;padding:2pt 5.4pt 2pt 5.4pt'><p class=MsoNormal align=center style='text-align:center'>" . ($oneList[$i] + 1) . "<p></td>";
                        $str2 .= "<td width=57 style='width:42.55pt;border-top:none;border-left:none;
      border-bottom:solid black 1.0pt;mso-border-bottom-themecolor:text1;
      border-right:solid black 1.0pt;mso-border-right-themecolor:text1;mso-border-top-alt:
      solid black .5pt;mso-border-top-themecolor:text1;mso-border-left-alt:solid black .5pt;
      mso-border-left-themecolor:text1;mso-border-alt:solid black .5pt;mso-border-themecolor:
      text1;padding:2pt 5.4pt 2pt 5.4pt;height:25.0pt'></td>";
                    }
                    $str .= $str1 . "<td style='mso-cell-special:placeholder;border:none;padding:0cm 0cm 0cm 0cm'
  colspan=" . ($i > 10 ? 10 - $i % 10 : 1) . "><p class='MsoNormal'>&nbsp;</p></td></tr>" . $str2 . "<td style='mso-cell-special:placeholder;border:none;padding:0cm 0cm 0cm 0cm'
  colspan=" . ($i > 10 ? 10 - $i % 10 : 1) . "><p class='MsoNormal'>&nbsp;</p></td></tr>";
                    $str .= "</table>";
                }
                break;
            case 2:
                $str = $this->getDocTop($mainTitle);
                $str .= "<p class=MsoNormal><span style='font-size:12.0pt;mso-bidi-font-size:11.0pt;
font-family:黑体'>选择题（请用<span lang=EN-US>2B</span>铅笔填写）</span></p>";
                if ($countX > 0) {
                    $str .= "<table class=MsoNormalTable border=1 cellspacing=0 cellpadding=0
     style='border-collapse:collapse;border:none;mso-border-alt:solid black .5pt;
     mso-border-themecolor:text1;mso-yfti-tbllook:1184;mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
     mso-border-insideh:.5pt solid black;mso-border-insideh-themecolor:text1;
     mso-border-insidev:.5pt solid black;mso-border-insidev-themecolor:text1'>";
                    $str1 = '';
                    $str2 = '';
                    for ($i = 0; $i < $countX; $i++) {
                        if ($i % 15 == 0 && $i != 0) {
                            $str .= $str1 . '</tr>' . $str2 . '</tr>';
                            $str1 = '';
                            $str2 = '';
                        }
                        if ($i % 15 == 0) $str .= "<tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'>";
                        $str1 .= "<td width=38 valign=top style='width:28.25pt;border-top:none;border-left:
      none;border-bottom:solid black 1.0pt;mso-border-bottom-themecolor:text1;
      border-right:solid black 1.0pt;mso-border-right-themecolor:text1;mso-border-top-alt:
      solid black .5pt;mso-border-top-themecolor:text1;mso-border-left-alt:solid black .5pt;
      mso-border-left-themecolor:text1;mso-border-alt:solid black .5pt;mso-border-themecolor:
      text1;padding:2pt 5.4pt 2pt 5.4pt'><p class=MsoNormal align=center style='text-align:center'><span lang=EN-US>" . ($oneList[$i] + 1) . "</span></p></td>";
                        $str2 .= "<td width=38 valign=top style='width:28.25pt;border:solid black 1.0pt;
      mso-border-themecolor:text1;mso-border-alt:solid black .5pt;mso-border-themecolor:
      text1;padding:2pt 5.4pt 2pt 5.4pt'><p class=MsoNormal align=center style='text-align:center'><span lang=EN-US>[A]</span></p>
      <p class=MsoNormal align=center style='text-align:center'><span lang=EN-US>[B]</span></p>
      <p class=MsoNormal align=center style='text-align:center'><span lang=EN-US>[C]</span></p>
      <p class=MsoNormal align=center style='text-align:center'><span lang=EN-US>[D]</span></p></td>";
                    }
                    if ($i % 15 != 0 && $i != 0) $str .= $str1 . "<td style='mso-cell-special:placeholder;border:none;padding:0cm 0cm 0cm 0cm'
      colspan=" . (15 - $i % 15) . "><p class='MsoNormal'>&nbsp;</p></td></tr>" . $str2 . "<td style='mso-cell-special:placeholder;border:none;padding:0cm 0cm 0cm 0cm'
      colspan=" . (15 - $i % 15) . "><p class='MsoNormal'>&nbsp;</p></td></tr>";
                    $str .= "</table>";
                }
                break;
            case 3:
                $str = $this->getDocTop($mainTitle);
                $str .= "<p class=MsoNormal><span style='font-size:12.0pt;mso-bidi-font-size:11.0pt;
font-family:黑体'>选择题（请用<span lang=EN-US>2B</span>铅笔填写）</span></p>";
                if ($countX > 0) {
                    $str .= "<table class=MsoNormalTable border=1 cellspacing=0 cellpadding=0
     style='border-collapse:collapse;border:none;mso-border-alt:solid black .5pt;
     mso-border-themecolor:text1;mso-yfti-tbllook:1184;mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
     mso-border-insideh:.5pt solid black;mso-border-insideh-themecolor:text1;
     mso-border-insidev:.5pt solid black;mso-border-insidev-themecolor:text1'>";
                    for ($i = 0; $i < $countX; $i++) {
                        if ($i % 5 == 0 && $i != 0) $str .= "</td>";
                        if ($i % 15 == 0 && $i != 0) $str .= "</tr>";
                        if ($i % 15 == 0) $str .= "<tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'>";
                        if ($i % 5 == 0) $str .= "<td width=188 valign=top style='width:141.0pt;border:solid black 1.0pt;
      mso-border-themecolor:text1;mso-border-alt:solid black .5pt;mso-border-themecolor:
      text1;padding:0cm 5.4pt 0cm 5.4pt'>";
                        $str .= "<p class=MsoNormal>" . ($oneList[$i] < 9 ? '0' . ($oneList[$i] + 1) : ($oneList[$i] + 1)) . "、[ A ] [ B ] [ C ] [ D ]</p>";
                    }
                    if ($i % 5 == 0) $str .= "</td>";
                    if ($i % 15 == 0) $str .= "</tr>";
                    $str .= "</table>";
                }
                break;
        }

        $str .= "<p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>" .
            "<p class=MsoNormal><span style='font-size:12.0pt;mso-bidi-font-size:11.0pt;" .
            "font-family:黑体'>非选择题（请在各试题的答题区内作答）</span></p>";

        if ($countF > 0) {
            $str .= "<table class=MsoNormalTable border=1 cellspacing=0 cellpadding=0
     style='border-collapse:collapse;border:none;mso-border-alt:solid black .5pt;
     mso-border-themecolor:text1;mso-yfti-tbllook:1184;mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
     mso-border-insideh:.5pt solid black;mso-border-insideh-themecolor:text1;
     mso-border-insidev:.5pt solid black;mso-border-insidev-themecolor:text1'>";
            $ij = 0;
            for ($i = 0; $i < $countF; $i++) {
                $str.="<tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'>
          <td width=569 valign=top style='width:426.45pt;border:solid black 1.0pt;
          mso-border-themecolor:text1;mso-border-alt:solid black .5pt;mso-border-themecolor:
          text1;padding:1pt 5.4pt 1pt 5.4pt'>";
                $str .= "<p class=MsoNormal><span lang=EN-US>" . ($two1[$ij] + 1) . "</span><span style='font-family:宋体;
          mso-ascii-font-family:Calibri;mso-ascii-theme-font:minor-latin;mso-fareast-font-family:
          宋体;mso-fareast-theme-font:minor-fareast;mso-hansi-font-family:Calibri;
          mso-hansi-theme-font:minor-latin'>题、</span></p><br/><br/>";
                $ij++;
                $str.="</tr>";
            }
            $str .= "</table>";
        }
        if ($article==1) {//是否有作文
                $str .= "<p class=MsoNormal><span style='font-family:宋体;mso-ascii-font-family:Calibri;
          mso-ascii-theme-font:minor-latin;mso-fareast-font-family:宋体;mso-fareast-theme-font:
          minor-fareast;mso-hansi-font-family:Calibri;mso-hansi-theme-font:minor-latin'>" . ($countX + $countF + 1) . "作文、<br/>作文题目：</span><span
          lang=EN-US>________________________________________</span></p>";
                for ($ii = 0; $ii < 50; $ii++) {
                    $str .= "<table class=MsoNormalTable border=1 cellspacing=0 cellpadding=0
           style='border-collapse:collapse;border:none;mso-border-alt:solid windowtext .5pt;
           mso-padding-alt:0cm 5.4pt 0cm 5.4pt;mso-border-insideh:.5pt solid windowtext;
           mso-border-insidev:.5pt solid windowtext'>
           <tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes;mso-yfti-lastrow:yes;
            height:21.0pt;mso-height-rule:exactly'>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;
            height:21.0pt;mso-height-rule:exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>
            <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
            </td>
            <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
            border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
            solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
            exactly'>";
                    if ($ii % 10 == 0 && $ii != 0) $str .= '<p class=MsoNormal><!--[if gte vml 1]><v:shapetype id="_x0000_t202"
             coordsize="21600,21600" o:spt="202" path="m,l,21600r21600,l21600,xe">
             <v:stroke joinstyle="miter"/>
             <v:path gradientshapeok="t" o:connecttype="rect"/>
            </v:shapetype><v:shape id="_x0000_s20' . (93 - ceil($ii / 10)) . '" type="#_x0000_t202" style=\'position:absolute;
             left:0;text-align:left;margin-left:10.8pt;margin-top:16.5pt;width:42.9pt;
             height:23.25pt;z-index:1;mso-position-horizontal-relative:text;
             mso-position-vertical-relative:text\' filled="f" stroked="f">
             <v:textbox style=\'mso-next-textbox:#_x0000_s20' . (93 - ceil($ii / 10)) . '\'/>
            </v:shape><![endif]--><![if !vml]><span style=\'mso-ignore:vglayout;
            position:absolute;z-index:1;left:0px;margin-left:14px;margin-top:17px;
            width:62px;height:35px\'>
            <table cellpadding=0 cellspacing=0>
             <tr>
              <td width=62 height=35 style=\'vertical-align:top\'><![endif]><![if !mso]><span
              style=\'position:absolute;mso-ignore:vglayout;left:0pt;z-index:1\'>
              <table cellpadding=0 cellspacing=0 width="100%">
               <tr>
                <td><![endif]>
                <div v:shape="_x0000_s20' . (93 - ceil($ii / 10)) . '" style=\'padding:3.6pt 7.2pt 3.6pt 7.2pt\'
                class=shape>
                <p class=MsoNormal><span style=\'font-size:6.5pt;font-family:宋体;
                mso-fareast-font-family:宋体;mso-fareast-theme-font:minor-fareast\'>▲<span
                lang=EN-US>' . (20 * $ii) . '</span>字</span><span lang=EN-US style=\'font-size:6.5pt\'><o:p></o:p></span></p>
                </div>
                <![if !mso]></td>
               </tr>
              </table>
              </span><![endif]><![if !mso & !vml]>&nbsp;<![endif]><![if !vml]></td>
             </tr>
            </table>
            </span><![endif]></p>';
                    else $str .= "<p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>";
                    $str .= "</td>
                                <td width=28 valign=top style='width:21.0pt;border:solid windowtext 1.0pt;
                                border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
                                solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:21.0pt;mso-height-rule:
                                exactly'>
                                <p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
                                </td>
                               </tr>
                              </table>
                              <p class=MsoNormal style='line-height:6.0pt;mso-line-height-rule:exactly'><span
                              lang=EN-US><o:p>&nbsp;</o:p></span></p>";
                }
            } else {
                $str .= "<p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>";
            }

        $str = $this->html2word(iconv('utf-8', 'GBK//IGNORE', $str));
        if(!$str){
            $data=array();
            $data['description'] = '答题卡内容转码失败，答题卡生成失败';
            $data['msg'] = $str;
            $this->addErrorLog($data);
            return [0,"<script>
                    $('#dtktishi').html('<b style=\"color:red;align:center;\">答题卡内容转码失败，请重试！</b>');
                </script>"];
        }
        $host = C('WLN_DOC_HOST');
        if ($host) {
            $urlPath = R('Common/UploadLayer/setWordDocument',array($str, $docVersion));
            if(strstr($urlPath,'error')){
                $data=array();
                $data['description'] = '答题卡内容转码失败，答题卡生成失败';
                $data['msg'] = $str;
                $this->addErrorLog($data);
                return [0,"<script>
                    $('#dtktishi').html('<b style=\"color:red;align:center;\">答题卡文档路径生成失败，请重试！</b>');
                </script>"];
            }
        } else {
            $hostIn = C('WLN_HTTP') . '/';
            $content = $this->getWordDocument($str, $hostIn);
            $urlPath = '/Uploads/mht/'.date('Y/md/',time()) . uniqid(mt_rand()) . rand(100, 999) . '.mht';
            $docPath = realpath('./') . $urlPath;
            $sizeLength=file_put_contents(iconv('UTF-8', 'GBK//IGNORE', $docPath), $content);
            if($sizeLength==0){
                $data=array();
                $data['description'] = '答题卡信息写入文档失败，答题卡生成失败';
                $data['msg'] = $content;
                $this->addErrorLog($data);
                return [0, "<script>
                    $('#dtktishi').html('<b style=\"color:red;align:center;\">答题卡信息写入文档失败，请重试！</b>');
                </script>"];
            }
            //文档写入异常
            $newPath = $this->htmltoword(iconv('UTF-8', 'GBK//IGNORE', $docPath), $docVersion);
            if(!$newPath){
                $data=array();
                $data['description'] = '答题卡文档转换Word失败，答题卡生成失败';
                $data['msg'] = $docVersion;
                $this->addErrorLog($data);
                return [0,"<script>
                    $('#dtktishi').html('<b style=\"color:red;align:center;\">答题卡文档转换Word失败，请重试！</b>');
                </script>)"];
            }
            //编码转换异常
            unlink($docPath);
            $urlPath = str_replace('.mht', $docVersion, $urlPath);
        }
        return [1,$urlPath,$mainTitle];
    }

    /**
     * 获取答题卡文档的头部
     * @param $mainTitle
     * @return string
     */
    protected function getDocTop($mainTitle){
            return '<p class=MsoNormal align=center style=\'margin-top:7.8pt;margin-right:0cm;margin-bottom:7.8pt;
margin-left:0cm;text-align:center\'><!--[if gte vml 1]><v:shapetype
 id="_x0000_t202" coordsize="21600,21600" o:spt="202" path="m,l,21600r21600,l21600,xe">
 <v:stroke joinstyle="miter"/>
 <v:path gradientshapeok="t" o:connecttype="rect"/>
</v:shapetype><v:shape id="_x0000_s2050" type="#_x0000_t202" style=\'position:absolute;
 left:0;text-align:left;margin-left:240.15pt;margin-top:50pt;width:176.25pt;
 height:55.35pt;z-index:1\' fillcolor="white [3201]" strokecolor="black [3200]"
 strokeweight="1pt">
 <v:stroke dashstyle="dash"/>
 <v:shadow color="#868686"/>
 <v:textbox>
  <![if !mso]>
  <table cellpadding=0 cellspacing=0 width="100%">
   <tr>
    <td><![endif]>
    <div>
    <p class=MsoNormal align=center style=\'text-align:center\'><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
    <p class=MsoNormal align=center style=\'text-align:center\'><b
    style=\'mso-bidi-font-weight:normal\'><span style=\'font-family:宋体;mso-ascii-font-family:
    Calibri;mso-ascii-theme-font:minor-latin;mso-fareast-font-family:宋体;
    mso-fareast-theme-font:minor-fareast;mso-hansi-font-family:Calibri;
    mso-hansi-theme-font:minor-latin\'>条</span> <span
    style=\'mso-spacerun:yes\'>&nbsp;</span></b><b style=\'mso-bidi-font-weight:
    normal\'><span style=\'font-family:宋体;mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin;mso-fareast-font-family:宋体;mso-fareast-theme-font:
    minor-fareast;mso-hansi-font-family:Calibri;mso-hansi-theme-font:minor-latin\'>码</span>
    <span style=\'mso-spacerun:yes\'>&nbsp;</span></b><b style=\'mso-bidi-font-weight:
    normal\'><span style=\'font-family:宋体;mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin;mso-fareast-font-family:宋体;mso-fareast-theme-font:
    minor-fareast;mso-hansi-font-family:Calibri;mso-hansi-theme-font:minor-latin\'>粘</span>
    <span style=\'mso-spacerun:yes\'>&nbsp;</span></b><b style=\'mso-bidi-font-weight:
    normal\'><span style=\'font-family:宋体;mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin;mso-fareast-font-family:宋体;mso-fareast-theme-font:
    minor-fareast;mso-hansi-font-family:Calibri;mso-hansi-theme-font:minor-latin\'>贴</span>
    <span style=\'mso-spacerun:yes\'>&nbsp;</span></b><b style=\'mso-bidi-font-weight:
    normal\'><span style=\'font-family:宋体;mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin;mso-fareast-font-family:宋体;mso-fareast-theme-font:
    minor-fareast;mso-hansi-font-family:Calibri;mso-hansi-theme-font:minor-latin\'>处</span><span
    lang=EN-US><o:p></o:p></span></b></p>
    <p class=MsoNormal align=center style=\'text-align:center\'><span
    style=\'font-family:宋体;mso-ascii-font-family:Calibri;mso-ascii-theme-font:
    minor-latin;mso-fareast-font-family:宋体;mso-fareast-theme-font:minor-fareast;
    mso-hansi-font-family:Calibri;mso-hansi-theme-font:minor-latin\'>（正面朝上贴在此虚线框内）</span></p>
    </div>
    <![if !mso]></td>
   </tr>
  </table>
  <![endif]></v:textbox>
</v:shape><![endif]--><![if !vml]><span style=\'mso-ignore:vglayout;position:
absolute;z-index:1;left:0px;margin-left:319px;margin-top:39px;width:241px;
height:80px\'><img width=241 height=80 src="333.files/image001.png"
alt="文本框: 条  码  粘  贴  处&#13;&#10;（正面朝上贴在此虚线框内）&#13;&#10;" v:shapes="_x0000_s2050"></span><![endif]><span
lang=EN-US style=\'font-size:15.0pt;font-family:黑体;mso-no-proof:yes\'>'.$mainTitle.'</span><span lang=EN-US style=\'font-size:15.0pt;
mso-fareast-font-family:黑体\'><o:p></o:p></span></p>

<p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal style="margin-top:4pt;margin-right:0cm;margin-bottom:4pt;
margin-left:0cm;"><span style=\'font-family:黑体\'>姓名：<span lang=EN-US>______________</span>班级：<span
lang=EN-US>______________<o:p></o:p></span></span></p>

<table class=MsoNormalTable border=1 cellspacing=0 cellpadding=0
 style=\'border-collapse:collapse;border:none;mso-border-alt:solid black .5pt;
 mso-border-themecolor:text1;mso-yfti-tbllook:1184;mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
 mso-border-insideh:.5pt solid black;mso-border-insideh-themecolor:text1;
 mso-border-insidev:.5pt solid black;mso-border-insidev-themecolor:text1\'>
 <tr style=\'mso-yfti-irow:0;mso-yfti-firstrow:yes;mso-yfti-lastrow:yes;
  height:22.7pt\'>
  <td width=62 style=\'width:46.8pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt;height:22.7pt\'>
  <p class=MsoNormal align=center style=\'text-align:center\'><span
  style=\'font-size:9.0pt;font-family:黑体\'>准考证号<span lang=EN-US><o:p></o:p></span></span></p>
  </td>
  <td width=23 style=\'width:17.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;border-left:none;mso-border-left-alt:solid black .5pt;mso-border-left-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt;height:22.7pt\'>
  <p class=MsoNormal align=center style=\'text-align:center\'><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
  </td>
  <td width=23 style=\'width:17.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;border-left:none;mso-border-left-alt:solid black .5pt;mso-border-left-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt;height:22.7pt\'>
  <p class=MsoNormal align=center style=\'text-align:center\'><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
  </td>
  <td width=23 style=\'width:17.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;border-left:none;mso-border-left-alt:solid black .5pt;mso-border-left-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt;height:22.7pt\'>
  <p class=MsoNormal align=center style=\'text-align:center\'><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
  </td>
  <td width=23 style=\'width:17.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;border-left:none;mso-border-left-alt:solid black .5pt;mso-border-left-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt;height:22.7pt\'>
  <p class=MsoNormal align=center style=\'text-align:center\'><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
  </td>
  <td width=23 style=\'width:17.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;border-left:none;mso-border-left-alt:solid black .5pt;mso-border-left-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt;height:22.7pt\'>
  <p class=MsoNormal align=center style=\'text-align:center\'><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
  </td>
  <td width=23 style=\'width:17.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;border-left:none;mso-border-left-alt:solid black .5pt;mso-border-left-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt;height:22.7pt\'>
  <p class=MsoNormal align=center style=\'text-align:center\'><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
  </td>
  <td width=23 style=\'width:17.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;border-left:none;mso-border-left-alt:solid black .5pt;mso-border-left-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt;height:22.7pt\'>
  <p class=MsoNormal align=center style=\'text-align:center\'><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
  </td>
  <td width=23 style=\'width:17.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;border-left:none;mso-border-left-alt:solid black .5pt;mso-border-left-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt;height:22.7pt\'>
  <p class=MsoNormal align=center style=\'text-align:center\'><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
  </td>
  <td width=23 style=\'width:17.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;border-left:none;mso-border-left-alt:solid black .5pt;mso-border-left-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt;height:22.7pt\'>
  <p class=MsoNormal align=center style=\'text-align:center\'><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
  </td>
  <td width=23 style=\'width:17.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;border-left:none;mso-border-left-alt:solid black .5pt;mso-border-left-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt;height:22.7pt\'>
  <p class=MsoNormal align=center style=\'text-align:center\'><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
  </td>
 </tr>
</table>

<p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
<p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal><!--[if gte vml 1]><v:shape id="_x0000_s2056" type="#_x0000_t202"
 style=\'position:absolute;left:0;text-align:left;margin-left:66.7pt;
 margin-top:2.4pt;width:349.7pt;height:79.8pt;z-index:4;
 mso-position-horizontal-relative:text;mso-position-vertical-relative:text\'/><![endif]--><![if !vml]><span
style=\'mso-ignore:vglayout;position:absolute;z-index:4;left:0px;margin-left:
88px;margin-top:2px;width:472px;height:113px\'>

<table cellpadding=0 cellspacing=0>
 <tr>
  <td width=472 height=113 bgcolor=white style=\'border:.75pt solid black;
  vertical-align:top;background:white\'><![endif]><![if !mso]><span
  style=\'position:absolute;mso-ignore:vglayout;left:0pt;z-index:4\'>
  <table cellpadding=0 cellspacing=0 width="100%">
   <tr>
    <td><![endif]>
    <div v:shape="_x0000_s2056" style=\'padding:4.35pt 7.95pt 4.35pt 7.95pt\'
    class=shape>
    <p class=MsoNormal style=\'line-height:10.0pt;mso-line-height-rule:exactly\'><b
    style=\'mso-bidi-font-weight:normal\'><span style=\'font-size:8.0pt;
    font-family:黑体;mso-ascii-font-family:Calibri;mso-ascii-theme-font:minor-latin\'>注意事项</span></b><b
    style=\'mso-bidi-font-weight:normal\'><span lang=EN-US style=\'font-size:8.0pt;
    mso-fareast-font-family:黑体\'><o:p></o:p></span></b></p>
    <p class=MsoNormal style=\'line-height:10.0pt;mso-line-height-rule:exactly\'><span
    lang=EN-US style=\'font-size:8.0pt;mso-fareast-font-family:黑体\'>1</span><span
    style=\'font-size:8.0pt;font-family:黑体;mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin\'>、</span><span style=\'font-size:8.0pt;
    font-family:黑体\'>答题前，考生先将自己的姓名、准考证号码填写清楚。<span lang=EN-US><o:p></o:p></span></span></p>
    <p class=MsoNormal style=\'line-height:10.0pt;mso-line-height-rule:exactly\'><span
    lang=EN-US style=\'font-size:8.0pt;mso-fareast-font-family:黑体\'>2</span><span
    style=\'font-size:8.0pt;font-family:黑体;mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin\'>、</span><span style=\'font-size:8.0pt;
    font-family:黑体\'>请将准考证条码粘贴在右侧的<span lang=EN-US>[</span>条码粘贴处<span
    lang=EN-US>]</span>的方框内<span lang=EN-US><o:p></o:p></span></span></p>
    <p class=MsoNormal style=\'line-height:10.0pt;mso-line-height-rule:exactly\'><span
    lang=EN-US style=\'font-size:8.0pt;mso-fareast-font-family:黑体\'>3</span><span
    style=\'font-size:8.0pt;font-family:黑体;mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin\'>、</span><span style=\'font-size:8.0pt;
    font-family:黑体\'>选择题必须使用<span lang=EN-US>2B</span>铅笔填涂；非选择题必须用<span
    lang=EN-US>0.5</span>毫米黑色字迹的签字笔填写，字体工整<span lang=EN-US><o:p></o:p></span></span></p>
    <p class=MsoNormal style=\'line-height:10.0pt;mso-line-height-rule:exactly\'><span
    lang=EN-US style=\'font-size:8.0pt;mso-fareast-font-family:黑体\'>4</span><span
    style=\'font-size:8.0pt;font-family:黑体;mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin\'>、</span><span style=\'font-size:8.0pt;
    font-family:黑体\'>请按题号顺序在各题的答题区内作答，超出范围的答案无效，在草纸、试卷上作答无效。</span><span
    lang=EN-US style=\'font-size:8.0pt;mso-fareast-font-family:黑体\'><o:p></o:p></span></p>
    <p class=MsoNormal style=\'line-height:10.0pt;mso-line-height-rule:exactly\'><span
    lang=EN-US style=\'font-size:8.0pt;mso-fareast-font-family:黑体\'>5</span><span
    style=\'font-size:8.0pt;font-family:黑体;mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin\'>、保持卡面清洁，不要折叠、不要弄破、弄皱，不准使用涂改液、刮纸刀。</span><span
    lang=EN-US style=\'font-size:8.0pt;mso-fareast-font-family:黑体\'><o:p></o:p></span></p>
    <p class=MsoNormal style=\'line-height:10.0pt;mso-line-height-rule:exactly\'><span
    lang=EN-US style=\'font-size:8.0pt;mso-fareast-font-family:黑体\'>6</span><span
    style=\'font-size:8.0pt;font-family:黑体;mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin\'>、填涂样例</span><span lang=EN-US
    style=\'font-size:8.0pt;mso-fareast-font-family:黑体\'><span
    style=\'mso-spacerun:yes\'>&nbsp;&nbsp; </span></span><span style=\'font-size:
    8.0pt;font-family:黑体;mso-ascii-font-family:Calibri;mso-ascii-theme-font:
    minor-latin\'>正确</span><span lang=EN-US style=\'font-size:8.0pt;mso-fareast-font-family:
    黑体\'><span style=\'mso-spacerun:yes\'>&nbsp; </span>[</span><span
    style=\'font-size:8.0pt;font-family:黑体;mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin\'>■</span><span lang=EN-US
    style=\'font-size:8.0pt;mso-fareast-font-family:黑体\'>] <span
    style=\'mso-spacerun:yes\'>&nbsp;</span></span><span style=\'font-size:8.0pt;
    font-family:黑体;mso-ascii-font-family:Calibri;mso-ascii-theme-font:minor-latin\'>错误</span><span
    lang=EN-US style=\'font-size:8.0pt;mso-fareast-font-family:黑体\'><span
    style=\'mso-spacerun:yes\'>&nbsp; </span>[</span><span lang=EN-US>--</span><span
    lang=EN-US style=\'font-size:8.0pt;mso-fareast-font-family:黑体\'>][</span><span
    style=\'font-size:8.0pt;font-family:黑体;mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin\'>√</span><span lang=EN-US
    style=\'font-size:8.0pt;mso-fareast-font-family:黑体\'>] [</span><span
    style=\'font-size:8.0pt;font-family:黑体;mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin\'>×</span><span lang=EN-US
    style=\'font-size:8.0pt;mso-fareast-font-family:黑体\'>]<o:p></o:p></span></p>
    </div>
    <![if !mso]></td>
   </tr>
  </table>
  </span><![endif]><![if !mso & !vml]>&nbsp;<![endif]><![if !vml]></td>
 </tr>
</table>

</span><![endif]><!--[if gte vml 1]><v:rect id="_x0000_s2055" style=\'position:absolute;
 left:0;text-align:left;margin-left:21.1pt;margin-top:18.85pt;width:13.1pt;
 height:6.15pt;z-index:3\'/><![endif]--><![if !vml]><span style=\'mso-ignore:
vglayout\'>

<table cellpadding=0 cellspacing=0 align=left>
 <tr>
  <td width=27 height=4></td>
 </tr>
 <tr>
  <td></td>
  <td><img width=20 height=10 src="333.files/image002.png" v:shapes="_x0000_s2055"></td>
 </tr>
</table>

</span><![endif]><!--[if gte vml 1]><v:shape id="_x0000_s2051" type="#_x0000_t202"
 style=\'position:absolute;left:0;text-align:left;margin-left:-6.6pt;
 margin-top:2.4pt;width:69.9pt;height:79.8pt;z-index:2;
 mso-position-horizontal-relative:text;mso-position-vertical-relative:text\'/><![endif]--><![if !vml]><span
style=\'mso-ignore:vglayout;position:absolute;z-index:2;left:0px;margin-left:
-10px;margin-top:2px;width:99px;height:113px\'>

<table cellpadding=0 cellspacing=0>
 <tr>
  <td width=99 height=113 bgcolor=white style=\'border:.75pt solid black;
  vertical-align:top;background:white\'><![endif]><![if !mso]><span
  style=\'position:absolute;mso-ignore:vglayout;left:0pt;z-index:2\'>
  <table cellpadding=0 cellspacing=0 width="100%">
   <tr>
    <td><![endif]>
    <div v:shape="_x0000_s2051" style=\'padding:4.35pt 7.95pt 4.35pt 7.95pt\'
    class=shape>
    <p class=MsoNormal style=\'line-height:10.0pt;mso-line-height-rule:exactly\'><b
    style=\'mso-bidi-font-weight:normal\'><span style=\'font-size:8.0pt;
    font-family:宋体;mso-ascii-theme-font:minor-fareast;mso-fareast-font-family:
    宋体;mso-fareast-theme-font:minor-fareast;mso-hansi-theme-font:minor-fareast\'>缺考标记<span
    lang=EN-US><o:p></o:p></span></span></b></p>
    <p class=MsoNormal style=\'line-height:10.0pt;mso-line-height-rule:exactly\'><span
    lang=EN-US style=\'font-size:8.0pt;font-family:宋体;mso-ascii-theme-font:minor-fareast;
    mso-fareast-font-family:宋体;mso-fareast-theme-font:minor-fareast;mso-hansi-theme-font:
    minor-fareast\'><o:p>&nbsp;</o:p></span></p>
    <p class=MsoNormal style=\'line-height:10.0pt;mso-line-height-rule:exactly\'><span
    style=\'font-size:8.0pt;font-family:宋体;mso-ascii-theme-font:minor-fareast;
    mso-fareast-font-family:宋体;mso-fareast-theme-font:minor-fareast;mso-hansi-theme-font:
    minor-fareast\'>考生禁止填涂缺考标记<span lang=EN-US>&shy;</span>！只能由监考老师负责用黑色字迹的签字笔填涂。<span
    lang=EN-US><o:p></o:p></span></span></p>
    </div>
    <![if !mso]></td>
   </tr>
  </table>
  </span><![endif]><![if !mso & !vml]>&nbsp;<![endif]><![if !vml]></td>
 </tr>
</table>
</span><![endif]></p>
<p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
<p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
<p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
<p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
<p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
<br style=\'mso-ignore:vglayout\' clear=ALL>';

    }


    /*
    public function wordUpload() {
        $upload = useToolFunction('UploadFile'); // 实例化上传类
        $upload->maxSize = 11457280; // 设置附件上传大小
        $upload->allowExts = array (
            'doc',
            'docx',
            'mht'
        ); // 设置附件上传类型
        $path = '/Uploads/word';
        $realpath = realpath('./')  . $path;
        $realpath .= '/' . date('Y/md', time());
        $path .= '/' . date('Y/md', time());

        if (!file_exists($realpath)) {
            $this->createpath($realpath);
        }
        $upload->savePath = $realpath . '/'; // 设置附件上传目录

        if (!$upload->upload()) { // 上传错误提示错误信息
            return $upload->getErrorMsg();
        } else { // 上传成功 获取上传文件信息
            $info = $upload->getUploadFileInfo();
            return $path . '/' . $info[0]['savename'];
        }
    }*/

}