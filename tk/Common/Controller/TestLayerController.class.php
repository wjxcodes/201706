<?php
/**
 * @author demo
 * @date 2015年6月7日
 */
/**
 * 用户处理试题相关操作处理
 */
namespace Common\Controller;
class TestLayerController extends CommonController{
    /**
     * 试题索引
     * @param array $field 返回字段
     * @param array $where 条件 array('DocID'=>文档id（支持逗号间隔）,
     *                                         'TestID'=>试题id（支持逗号间隔）
     *                                          'maxtestid' => 不能超过试题id
     *                                          'testfilter' => 为1时则排除试题id
     *                                         'Diff'=>难度（数据类型1-5五段）
     *                                         'DocTypeID'=>文档类型
     *                                         'TestNum'=>小题数
     *                                         'TestStyle'=>试题类型
     *                                         'TypesID'=>题型id（支持逗号间隔）
     *                                         'TypeFilter'=>题型id排除，为1时排除TypesID的题型
     *                                         'SubjectID'=>学科id（支持逗号间隔）
     *                                         'SpecialID'=>专题id（支持逗号间隔）
     *                                         'GradeID'=>年级id（支持逗号间隔）
     *                                         'KlID'=>知识点id（支持逗号间隔）
     *                                         'ChapterID'=>章节id（支持逗号间隔）
     *                                         'LastTime'=>按时间查询（正数 时间--当前；负数 0-时间）
     *                                         'key'=>按字符串进行查询
     *                                         'width'=>试题的内容选项宽度
     *                                         'field'=>试题需要查询的字段 必须有key
     *                                         'searchStyle'=>查询类型 如果有key 默认any任何关键字匹配 normal全部关键字匹配
     *                                         'Duplicate'=>重复字段 0不重复
     *                                         'ShowWhere'=>0组卷端 1通用 2提分端 3前台禁用
     *                                         );
     * @param array $order 排序 array('字段1 DESC','字段2 ASC',...)
     * @param array $page 分页信息 array('page'=>当前页,'perpage'=>'每页几个');
     * @return array 返回数组    array(0=>试题数据集,1=>总数量,2=>每页数量);
     *         可用于返回数据数组
     *         array（
     *             'testid'=>试题id
     *             'docid'=>所属文档id
     *             'typeid'=>所属文档类型id
     *             'docname'=>所属文档名称
     *             'docyear'=>所属文档年份
     *             'numbid'=>所属文档试题编号
     *             'typesid'=>所属题型id
     *             'typesname'=>所属题型名称
     *             'subjectid'=>学科id
     *             'subjectname'=>学科名字
     *             ‘specialid'=>专题id
     *             ‘specialname'=>专题名字 //未做
     *             'test'=>题文被table分割选项后的字符串并且序号化
     *             'testold'=>题文被table分割选项后的字符串并且标签化
     *             'testnormal'=>题文未被处理
     *             'answer'=>答案序号化
     *             'answerold'=>答案标签化
     *             'answernormal'=>答案未被处理
     *             'analytic'=>解析序号化
     *             'analyticold'=>解析标签化
     *             'analyticnormal'=>解析未被处理
     *             'remark'=>解析序号化
     *             'remarkold'=>解析标签化
     *             'remarknormal'=>解析未被处理
     *             'firstloadtime'=>第一次入库时间（格式 ：年/月/日）
     *             'firstloadtimeint'=>第一次unix入库时间
     *             'loadtime'=>最近一次入库时间（格式 ：年/月/日）
     *             'loadtimeint'=>最近一次unix入库时间
     *             'testnum'=>小题数量
     *             'diff'=>难度值（3为小数）
     *             'diffid'=>难度id（1-5共五档）
     *             'diffstar'=>难度数据段标示（例如：0.001-0.300）
     *             'diffname'=>难度名称
     *             'diffxing'=>难度html星星标示（需要css）
     *             'mark'=>打分细则
     *             'kllist'=>知识点列表带知识点视频
     *             'klnameall'=>知识点名称路径
     *             'klnameonly'=>知识点名称
     *             'ifchoose'=>试题类型（0非选择题 1复合体 2多选 3单选）
     *             'times'=>试题下载次数
     *             'admin'=>管理员
     *             'specialname'=>专题名称
     *             'chapternameall'=>章节名称路径
     *             'gradename'=>年级
     *             'TestStyle'=>试题类型
     *             'OptionWidth'=>选项宽度
     *             'OptionNum'=>选项数量
     *         ）；
     * @author demo
     * */
    public function indexTestList($field,$where,$order,$page,$openBackIndex=0){
        $testReal=$this->getModel('TestReal'); //试题
        return $testReal->getTestIndex($field,$where,$order,$page,$openBackIndex);
    }

    /**
     * 为字符串完善p标签对称
     * @param string $str 字符串
     * @return string
     * @author demo
     */
    private function addMoreTagP($str){
        $tmpCount1=substr_count($str,'</p>');
        $tmpCount2=substr_count($str,'<p');
        if($tmpCount1 - $tmpCount2 == 1)
            $str='<p>'.$str;
        else $str='<p>'.$str.'</p>';

        return $str;
    }

    /**
     * 在字符串中指定字符串添加序号
     * @access private
     */
    private function addNumToCutStr($str,$cutStr,$startNum,$separator=''){
        $tmpArr=explode($cutStr,$str);
        foreach($tmpArr as $i=>$iTmpArr){
            if($i == 0)
                continue;
            $tmpArr[$i]=$startNum.$separator.$tmpArr[$i];
            if($startNum !== '')
                $startNum++;
        }

        return implode($cutStr,$tmpArr);
    }

    /**
     * 改变td标签的边框
     * @author mbao
     */
    public function addStyleBorder($matches){
        //部分已经存在边框的设置则跳过
        if(strstr($matches[1],'border-') || strstr($matches[1],'border:'))
            return $matches[1];

        //已经存在style但是没有边框设置
        $tmp=preg_replace('/style=([\'\"])/is','style=\$1border:1px solid #000;',$matches[1]);

        //不存在style
        if($tmp == $matches[1])
            $tmp=str_replace('<td','<td style="border:1px solid #000"',$matches[1]);

        return $tmp;
    }

    /**
     * 试卷难度值计算
     * @param array $param ['ifChoose'] 选做个数
     * @param array $param ['chooseNum'] 选做题数量
     * @param array $param ['score'] 分值 以试题为单位的分值
     * @param array $param ['diff'] 难度
     * @param array $param ['point'] 返回难度值保留位数
     * @return float 难度值
     * @author demo
     */
    public function calcPaperDiff($param=array()){
        extract($param);
        $diffAll=0;
        $numAll =0;
        foreach($diff as $i=>$iDiff){
            if($ifChoose[$i]){
                $diffAll+=$score[$i] * $diff[$i] / $chooseNum[$i] * $ifChoose[$i];
                $numAll+=$score[$i] / $chooseNum[$i] * $ifChoose[$i];
            }
            else{
                $diffAll+=$score[$i] * $diff[$i];
                $numAll+=$score[$i];
            }
        }
        $return=$diffAll / $numAll; //返回难度
        if(!is_numeric($point))
            $point=3;
        if(!empty($point))
            $return=number_format($return,$point);

        return $return;
    }

    /**
     * 试卷总分值计算
     * @param array $param ['ifChoose'] 选做个数
     * @param array $param ['chooseNum'] 选做题数量
     * @param array $param ['score'] 分值 以试题为单位的分值
     * @return float 总分值
     * @notice 未发现引用 #
     * @author demo
     */
    public function calcPaperScore($param=array()){
        extract($param);
        $return  =0; //试卷分值
        $delScore=0; //选做题重复分值
        $jump    =0; //计数
        foreach($score as $i=>$iScore){
            $return+=$iScore;
            if(!$ifChoose[$i])
                $jump=0; //没有选做题
            if($ifChoose[$i]){
                $jump=$jump + 1;
                if($jump > $ifChoose[$i])
                    $delScore+=$iScore; //超出选做数量则减分
                if($jump >= $chooseNum[$i])
                    $jump=0; //容错 超出选做题数量则重置+
            }
        }

        return $return - $delScore;
    }

    /**
     * 处理试题标签变成序号
     * @param string $str 试题题文
     * @param int $startNum 开始序号
     * @param int $hanzi 序号类型
     * @param bool $fromSplit 题文是否来自testsplit(新添加的索引返回格式)
     * @param int $testNum 试题数量
     * @return string
     * @author demo
     */
    public function changeTagToNum($str,$startNum,$hanzi=0,$fromSplit=false,$testNum=0){
        $tmpFlag=0;
        if(strpos($str,'【小题') != false){
            $str=preg_replace('/【[<\/a-z=\'\":\s\->]*小[<\/a-z=\'\":\s\->]*题[<\/a-z=\'\":\s\->0-9]*】/is','<span class="quesindex"><b></b></span><span class="quesscore"></span>',$str);

            $tmpNum='';
            if($startNum !== '')
                $tmpNum='．';
            $str    =$this->addNumToCutStr($str,'<span class="quesindex"><b>',$startNum,$tmpNum);
            $tmpFlag=1;
        }

        if(strpos($str,'【题号') != false){
            $str    =preg_replace('/【[<\/a-z=\'\":\s\->]*题[<\/a-z=\'\":\s\->]*号[<\/a-z=\'\":\s\->0-9]*】/is','<span class="quesindexnum">　　</span>',$str);
            $str    =$this->addNumToCutStr($str,'<span class="quesindexnum">　',$startNum);
            $tmpFlag=1;
        }

        //对于新索引格式,题文已经分割,防止多加序号 例如分割后的阅读理解
        if($fromSplit && $testNum > 1 && $tmpFlag == 0){
            return $str;
        }

        if($tmpFlag == 0 && $hanzi == 0){
            $tmpNum='';
            if($startNum !== '')
                $tmpNum=$startNum.'．';
            $str='<p><span class="quesindex"><b>'.$tmpNum.'</b></span><span class="quesscore"></span><span class="tips"/>'.R('Common/TestLayer/removeLeftTag',array(
                    $str,
                    '<p>'
                ));
        }
        if($tmpFlag == 0 && $hanzi == 1){
            $tmpNum='';
            if($startNum !== '')
                $tmpNum=formatString('num2Chinese',$startNum).'、';
            $str='<p><span class="quesindex"><b>'.$tmpNum.'</b></span><span class="quesscore"></span><span class="tips"/>'.R('Common/TestLayer/removeLeftTag',array(
                    $str,
                    '<p>'
                ));
        }

        return $str;
    }

    /**
     * 改变数组中的部分字段的图片路径
     * @param array $arr 数据集
     * @param array $field 字段数组
     * @return array
     * @author demo
     */
    public function changeUrlByField($arr,$field){
        foreach($arr as $i=>$iArr){
            foreach($field as $j=>$jField){
                $arr[$i][$jField]=$this->strFormat($iArr[$jField]);
            }
        }

        return $arr;
    }

    /**
     * 检查试题属性是否正确；
     * @param array $testArray 试题属性
     * @return array
     * @author demo
     */
    public function checkTestAttr($testArray){
        $error=array();
        if(empty($testArray['TypesID']))
            $error[]='题型未标注';
        if(empty($testArray['SubjectID']))
            $error[]='学科未标注';
        if(empty($testArray['Diff']))
            $error[]='难度未标注';

        //判断试题数量 OptionWidth OptionNum 0例外
        if($testArray['TestNum'] == 0){
            if($testArray['TestStyle'] == 2)
                $error[]='小题参数混乱';
            $tmpArr=explode(',',$testArray['OptionNum']);
            if(count($tmpArr) > 1)
                $error[]='小题参数混乱';
            $tmpArr=explode(',',$testArray['OptionWidth']);
            if(count($tmpArr) > 1)
                $error[]='小题参数混乱';

            if($testArray['IfChoose'] == 1){
                $error[]='小题参数混乱';
            }
            else if($testArray['IfChoose'] == 0){
                if($testArray['OptionWidth'] > 0)
                    $error[]='选项宽度错误';
                if($testArray['TestStyle'] == 1)
                    $error[]='小题参数混乱';
                if($testArray['OptionNum'] > 0)
                    $error[]='选项数量错误';
            }
            else{
                if($testArray['OptionWidth'] < 10)
                    $error[]='选项宽度错误';
                if($testArray['TestStyle'] == 3)
                    $error[]='小题参数混乱';
                if($testArray['OptionNum'] < 3)
                    $error[]='选项数量错误';

                $tmpArr=explode('A.',$testArray['Test']);
                if(count($tmpArr) < 2)
                    $tmpArr=explode('A．',$testArray['Test']);
                if(count($tmpArr) > 2)
                    $error[]='A选项多于1个';
                if(count($tmpArr) < 2)
                    $error[]='A选项找不到';
                $tmpArr=explode('B.',$testArray['Test']);
                if(count($tmpArr) < 2)
                    $tmpArr=explode('B．',$testArray['Test']);
                if(count($tmpArr) > 2)
                    $error[]='B选项多于1个';
                if(count($tmpArr) < 2)
                    $error[]='B选项找不到';
                $tmpArr=explode('C.',$testArray['Test']);
                if(count($tmpArr) < 2)
                    $tmpArr=explode('C．',$testArray['Test']);
                if(count($tmpArr) > 2)
                    $error[]='C选项多于1个';
                if(count($tmpArr) < 2)
                    $error[]='C选项找不到';
            }
        }
        else{
            if($testArray['IfChoose'] != 1){
                $error[]='小题参数混乱';
            }
            else{
                $tmpArr =explode(',',$testArray['OptionNum']);
                $tmpArr2=explode(',',$testArray['OptionWidth']);

                //查询试题 IfChoose
                $buffer=$this->getModel('TestJudge')->selectData('JudgeID,TestID,IfChoose','TestID='.$testArray['TestID'],'OrderID asc');
                $style =0;
                foreach($buffer as $iBuffer){
                    if($style == 0){
                        if($iBuffer['IfChoose'] != 0)
                            $style=1;
                        if($iBuffer['IfChoose'] == 0)
                            $style=3;
                    }
                    else{
                        if($iBuffer['IfChoose'] == 0 && $style == 1)
                            $style=2;
                        if($iBuffer['IfChoose'] == 0 && $style == 3)
                            $style=3;
                        if($iBuffer['IfChoose'] != 0 && $style == 3)
                            $style=2;
                    }
                }
                if($style != $testArray['TestStyle'])
                    $error[]='小题参数混乱';
                if($style < 3){
                    if(count($tmpArr) == 1){
                        $error[]='选项数量错误';
                    }
                    else{
                        if(count($tmpArr) != $testArray['TestNum']){
                            $error[]='选项数量混乱';
                        }
                        foreach($tmpArr as $i=>$iTmpArr){
                            if($buffer[$i]['IfChoose'] != 0 && $iTmpArr == 0)
                                $error[]='选项数量错误';
                            else if($buffer[$i]['IfChoose'] == 0 && $iTmpArr != 0)
                                $error[]='选项数量错误';
                        }
                    }
                    if(count($tmpArr2) == 1){
                        $error[]='选项参数错误';
                    }
                    else{
                        if(count($tmpArr2) != $testArray['TestNum']){
                            $error[]='选项参数混乱';
                        }
                        foreach($tmpArr2 as $i=>$iTmpArr2){
                            if($buffer[$i]['IfChoose'] != 0 && $iTmpArr2 < 10)
                                $error[]='选项参数错误';
                            else if($buffer[$i]['IfChoose'] == 0 && $iTmpArr2 > 10)
                                $error[]='选项参数错误';
                        }
                    }
                }
                else if($style == 3){
                    if($testArray['OptionNum'] != 0)
                        $error[]='选项数量错误';
                    if($testArray['OptionWidth'] != 0 && $testArray['OptionWidth'] != 1)
                        $error[]='选项宽度错误';
                }
            }
        }

        return implode('<br/>',$error);
    }

    /**
     * 根据字符切割字符串，分类id
     * @param array|string $arr 需要处理的id
     * @param int $model 模式
     *   ##模式说明
     *   模式 0 切割结果不显示字母标识符
     *   模式 1 切割结果显示字母标识符 (现在的字符标识符有:c(校本题库),l(系统知识),u(自建知识))
     *   模式 2 只获取试题id数组(不获取知识)并保持原顺序
     *   ##
     * @return array 不带字母前缀的返回以0为键的数组
     * @author demo
     */
    public function cutIDStrByChar($arr,$model=0){
        if(empty($arr))
            return array();
        if(is_string($arr))
            $arr=explode(',',$arr);

        $output=array();
        foreach($arr as $i=>$iArr){
            $char=preg_replace('/[0-9]/i','',$iArr);
            if($char == '')
                $char=0;

            if($model == 0){
                $output[$char][]=preg_replace('/[a-z]/i','',$iArr);
            }
            elseif($model == 1){
                $output[$char][]=$iArr;
            }
            elseif($model == 2){
                if(preg_match('/^c?\d+$/',$iArr)){
                    $output[]=$iArr;
                }
            }
        }

        return $output;
    }

    /**
     * 对数据进行多余标记的处理
     * @param array $arr 数据集
     * @param array $field 字段数组
     * @return array
     * @author demo
     */
    public function changeMoreTagByField($arr,$field,$model=0){
        foreach($arr as $i=>$iArr){
            foreach($field as $j=>$jField){
                $arr[$i][$jField]=$this->delMoreTag($iArr[$jField],$model);
            }
        }

        return $arr;
    }

    /**
     * 去除字符串最后半个开始标记 例如 <p> <b> <i> <strong>
     * @param string $str 字符串
     * @param int $model 不同模式 0默认处理 1加p标签 2去除标签
     * @return string
     * @author demo
     */
    public function delMoreTag($str,$model=0){
        if($str){
            $tag       =array(
                '/^(&nbsp;|\s|　|\r|\n|<br\/?>)*/i',
                '/^(&nbsp;|\s|　|\r|\n|<br\/?>)*<\/p>(&nbsp;|\s|　|\r|\n|<br\/?>)*<p>/i',
                '/(&nbsp;|\s|　|\r|\n|<br\/?>)*$/i',
                '/<strong[^>]*>$/i',
                '/<i[^m>]*>$/i',
                '/<b[^>]*>$/i',
                '/<u[^>]*>$/i',
                '/<p[^>]*>$/i',
                '/<\/p>$/i'
            );
            $tagReplace=array();
            $count     =count($tag);
            //去除最后的标记
            for($i=0;$i < $count - 2;$i++){
                $str=preg_replace($tag,$tagReplace,$str);
            }
            if($model == 1){
                $str=$this->addMoreTagP($str);
            }
            if($model == 2){
                $str=strip_tags($str);
            }
        }

        return $str;
    }

    /**
     * 难度小数到html转换 返回难度区间
     * @param int $diff 难度小数 0.000
     * @param array $diffArr 难度数据集
     * @return string
     * @author demo
     */
    public function diff2Html($diff,$diffArr=''){
        if($diffArr == '')
            $diffArr=C('WLN_TEST_DIFF');

        return $this->int2Html($this->diff2Int($diff,$diffArr));
    }

    /**
     * 难度小数到字符串转换 难度名称
     * @param int $diff 难度小数 0.000
     * @param array $diffArr 难度数据集
     * @return string
     * @author demo
     */
    public function diff2Str($diff,$diffArr=''){
        if($diffArr == '')
            $diffArr=C('WLN_TEST_DIFF');

        return $diffArr[$this->diff2Int($diff,$diffArr)][0];
    }

    /**
     * 难度小数到数字转换 难度小数值转难度数值
     * @param int $diff 难度小数 0.000
     * @param array $diffArr 难度数据集
     * @return int
     * @author demo
     */
    public function diff2Int($diff,$diffArr=''){
        if($diffArr == '')
            $diffArr=C('WLN_TEST_DIFF');
        $diff=round($diff,3);
        foreach($diffArr as $i=>$iDiffArr){
            if($diff >= $iDiffArr[3] and $diff <= $iDiffArr[4])
                return $i;
        }
    }

    /**
     * 格式化试题id，去除不合法的字符
     * @param string $ids '111,2323,232323'
     * @return string
     * @author demo 2015-8-12
     */
    public function formatIds($ids){
        return preg_replace('/[^\d|,]/is','',$ids);
    }

    /**
     * 分割分值到小题上
     * @param $score int 总分值
     * @param $testNum int 小题数量
     * @return string
     * @author demo
     */
    public function gaCutScore($score,$testNum){
        //判断使用整数还是小数表达分值；
        $ifPoint=0; //默认不用小数标示
        if($score < $testNum)
            $ifPoint=1;
        $tmpScore=$score / $testNum; //平均分
        $tmpScore=number_format($tmpScore,$ifPoint); //保留一位小数

        //计算分值到小题
        $tmpScore2=array();
        for($k=0;$k < $testNum - 1;$k++){
            $tmpScore2[]=$tmpScore;
        }
        $tmpScore2[]=$score - $tmpScore * ($testNum - 1);

        return implode(',',$tmpScore2);
    }

    /**
     * 获取制定个数不重复的随机整数数据数组
     * @param array $arr 返回的数组
     * @param int $length 指定的长度
     * @param int $max 随机数最大值
     * @return array
     * @author demo
     */
    public function getRandArr($arr,$length,$max){
        //取长度为0的数组
        if($length <= 0)
            return array();

        //取长度大于总长度
        if($length > $max){
            for($i=0;$i < $max;$i++){
                $arr[]=$i;
            }

            return $arr;
        }

        //随机去指定长度数组
        $num=rand(0,$max - 1);
        if(!in_array($num,$arr))
            $arr[]=$num;
        if(count($arr) == $length){
            return $arr;
        }
        else{
            return $this->getRandArr($arr,$length,$max);
        }
    }

    /**
     * 难度数字1-5转换到html 返回难度区间
     * @param int $int 难度数 1-5
     * @return string
     * @author demo
     */
    public function int2Html($int,$diffArr=''){
        if($diffArr == '')
            $diffArr=C('WLN_TEST_DIFF');

        return $diffArr[$int][3].'-'.$diffArr[$int][4];
    }

    /**
     * 数字转换到html 难度星星
     * @param int $int 星星数 1-5
     * @return string
     * @author demo
     */
    public function int2Xing($int){
        $output='';
        for($i=0;$i < $int;$i++){
            $output.='<a class="staryellow"></a>';
        }
        for($i=0;$i < 5 - $int;$i++){
            $output.='<a class="stargray"></a>';
        }

        return $output;
    }

    /**
     * 判断是否开启试题排重
     * @return string
     * @author demo
     */
    public function ifExcludeRepeat(){
        if(C('WLN_INDEX_OPEN_EXCLUDE_REPEAT') == 0)
            return '30503';
        else return '';
    }

    /**
     * 将数据转换为以testid为key的数组
     * @param array $arr 试题数据集
     * @param string $field 字段
     * @param string $pre 前缀
     * @param int $ifArray 是否数组 0适合单个testid 1适用于多个相同的testid
     * @return array 试题数据集
     * @author demo
     */
    public function reloadTestArr($arr,$field='testid',$pre='',$ifArray=0){
        $output=array();
        if($arr && is_array($arr)){
            foreach($arr as $iArr){
                if($ifArray == 1){
                    $output[$pre.$iArr[$field]][]=$iArr;
                }
                else{
                    $output[$pre.$iArr[$field]]=$iArr;
                }
            }
        }

        return $output;
    }

    /**
     * 数组字段改成小写
     * @param array $arr 需要转换的数组
     * @Notice
     * 在使用R方法(call_user_func_array)调用此方法时传入参数也需要引用传递
     * 其他直接调用的时候,则不需要引用传递 否则会触发一个[call-time pass-by-reference has been
     * removed]错误
     * @examle
     * 参数引用传递[当前使用的是这种模式]
     * $arr = R('Common/TestLayer/lowercaseArrField',array(&$arr));
     * 参数传值传递
     * $testLayer = new TestLayer();//此处只是举例 当前项目并不会这样调用
     * $testLayer = $testLayer->lowercaseArrField($arr);//不要使用引用传递
     * @author demo
     */
    public function lowercaseArrField(&$arr){
        if(!is_array($arr) || empty($arr)){
            return [];
        }
        $arr =  array_change_key_case($arr, CASE_LOWER );

        foreach($arr as $i=>$iArr){
            if(is_array($iArr)){
                $self = __FUNCTION__;
                $this->$self($arr[$i]);
            }
        }
        return $arr;
    }

    /**
     * 去除开头标记
     * @param string $str 字符串
     * @param string $tag 要去除的标记
     * @author demo
     * @return string
     */
    public function removeLeftTag($str,$tag){
        if(strpos($str,$tag) === 0){
            return substr($str,strlen($tag));
        }

        return $str;
    }

    /**
     * 将输出的内容替换为争取的格式。原始：<p>sdfsdf</p> 替换为 sdfsdf</p>
     * @param string $str 替换的字符串
     * @return string
     */
    public function replaceText($str){
        return preg_replace('/^<p.*?>/i','',$str);
    }

    /**
     * 
		处理excel文档试题
     */
    public function strFormatxls($test,$url=''){
        //【注意】：1 2 3顺序不能变

        if(!$url) $url=C('WLN_DOC_HOST'); //结尾不包含/

        if(!$url){
            return $test;
        }
		
        //1.检测是否需要反转移
        if(strpos($test,'\'') !== false || strpos($test,'\\') !== false || strpos($test,'\"')){
            $test=stripslashes($test);
        }

        //2.检测是否需要替换图片
        if(strpos($test,'{#$DocHost#}') !== false){
            $test=str_replace('{#$DocHost#}',$url,$test);
        }
		
        //3.检测是否需要添加图src前缀
        preg_match_all('/<img\s.*?src=([\'|\"][^h][^\"\']*[\"|\'])/is',$test,$matches);
		// dump($matches);
		// dump($test);
        if($matches[1]){
            $sign='"';
            foreach($matches[1] as $iMatches){
                $srcContent=str_replace(array('"',"'"),"",$iMatches); //去除引号后src内容
				// dump($iMatches);
				// dump($srcContent);
				// "/QaRes/63551000/63551000-98609599971661.png"
				
                $test=str_replace($iMatches,$sign.$url.$srcContent.$sign,$test);
				// dump($test);
					
                
            }
        }
		// echo 111;
		
        return $test;
    }
	/*
		描述：格式化字符串，反转义，替换图片路径
     * 替换图片增加绝对路径要求图片相对路径以/开头
     * @param string $test 可能需要格式化的试题或者知识
     * @return string 格式化后文本
     * @author demo 5.7.13
	*/
	public function strFormat($test,$url=''){
        //【注意】：1 2 3顺序不能变

        if(!$url) $url=C('WLN_DOC_HOST'); //结尾不包含/

        if(!$url){
            return $test;
        }
		
        //1.检测是否需要反转移
        if(strpos($test,'\'') !== false || strpos($test,'\\') !== false || strpos($test,'\"')){
            $test=stripslashes($test);
        }

        //2.检测是否需要替换图片
        if(strpos($test,'{#$DocHost#}') !== false){
            $test=str_replace('{#$DocHost#}',$url,$test);
        }
		
        //3.检测是否需要添加图src前缀
        preg_match_all('/<img\s.*?src=([\'|\"][^h][^\"\']*[\"|\'])/is',$test,$matches);
		
        if($matches[1]){
            $sign='"';
            foreach($matches[1] as $iMatches){
                $srcContent=str_replace(array('"',"'"),"",$iMatches); //去除引号后src内容
				
				
                if(strstr($srcContent,'Uploads')){ //仅对上传数据进行处理
					
                    $test=str_replace($iMatches,$sign.$url.$srcContent.$sign,$test);	
                }
            }
        }
        return $test;
    }
    /**
     * 描述：单题下载处理程序
     * @param int $testID 试题id
     * @param int $auto 类型 0未入库试题 1入库试题
     * @param int $style 显示类型 0外网 1本地
     * @return string 格式化后文本
     * @author demo
     */
    public function singleDown($testID,$auto=0,$style=0){
        if(empty($auto)) $auto=0;

        $doc = $this->getModel('Doc');
        $edit = $doc->unionSelect('testSelectJoinById',$testID,$auto);

        //处理重复试题
        if($auto==1 && $style==0 && $edit[0]['Duplicate']){
            $editDuplicate = $doc->unionSelect('testSelectJoinById',$edit[0]['Duplicate'],$auto);
            $test=$this->getModel('Test');
            $edit[0]=$test->changeArrayValue($edit[0],$editDuplicate[0],1);
        }

        $buffer=$doc->selectData(
            '*',
            'DocID="'.$edit[0]['DocID'].'"');
        $subjectList=SS('subject');
        $buffer=$subjectList[$buffer[0]['SubjectID']];
        $fontSize=10.5;
        if($buffer[0]['FontSize']>1) $fontSize=$buffer[0]['FontSize'];

        //针对入库试题 4.15后上传的同步测试类文档随机显示试题解析
        if($auto==1 && $style==0 && $edit[0]['LoadTime']>strtotime('2015-4-15') && $buffer[0]['TypeID']==7){
            //随机概率0.4
            $randInt=rand(100000, 999999);
            if($randInt<600000){
                $edit[0]['DocAnalytic']='';
            }
        }

        $typeList=SS('types');
		
        $paperName=$subjectList[$edit[0]['SubjectID']]['ParentName'].$subjectList[$edit[0]['SubjectID']]['SubjectName'].$typeList[$edit[0]['TypesID']]['TypesName'].$testID.'-题库平台(www.tk.com)'; //文档名称

        if($style) $str=$doc->getSingleDoc($edit[0],$fontSize,$paperName,0,0);
        else $str=$doc->getSingleDoc($edit[0],$fontSize,$paperName,1,1);

        $style=$_GET['style'];
        if(!$style) $style=".docx";

        $host=C('WLN_DOC_HOST');

        if($host){
            $urlPath=R('Common/UploadLayer/setWordDocument',array( $str ,substr($style,1)));
            if(strstr($urlPath,'error')){
                $this->setError('30405',0); //下载异常！请稍联系管理员。
            }
            $url=$host.R('Common/UploadLayer/getDocServerUrl',array($urlPath,'down','mht',$paperName));
            header('Location:'.$url);
        }else{
            $hostIn="http://" . $_SERVER['HTTP_HOST'] . "/";
            $path=realpath('./').'/Uploads/mht/'.date('Y/md/',time());
            if(!file_exists($path)) $doc->createpath($path);

            $content=$doc->getWordDocument( $str ,$hostIn);
            $docPath=$path.'试题-'.$testID.'.mht';
            file_put_contents(iconv('UTF-8','GBK//IGNORE',$docPath),$content);

            $newPath=$doc->htmltoword(iconv('UTF-8', 'GBK//IGNORE', $docPath),substr($style,1));
            unlink(iconv('UTF-8','GBK//IGNORE',$docPath));

            if($newPath!=iconv('UTF-8', 'GBK//IGNORE', $docPath)){
                $urlPath=$host.$newPath;
            }else{
                $urlPath = str_replace('.mht',$style,$docPath);
            }
            $content=file_get_contents($urlPath);

            $doc->wordheader($paperName,$style);
            echo $content;
        }
    }
}