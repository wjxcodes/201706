<?php
/**
 * @author demo 
 * @date 2014年8月12日
 */
/**
 * 试题模型类，用于处理试题相关操作
 */
namespace Test\Model;
use Doc\Model\HandleWordModel;
class TestModel extends HandleWordModel{
    /**
     * 缩进试题中的A-D选项便于替换 $style是否添加标签说明
     * @param string $str 需要格式化的字符串
     * @param int $key 试题序号
     * @param int $width 文档宽度
     * @param $score=0 试题分值
     * @param int $ifFormat 是否格式化选项  默认0不排版 只有题文需要
     * @param int $optionWidth 选项宽度
     * @param int $optionNum 选项数量
     * @param int $testNum 小题数量
     * @param int $ifChoose 0为非选择题 1为选择题
     * @param int $ifAddNum 默认0加序号 1当key为1且没有小题时不加序号
     * @return string 组合后的试题字符串
     * @author demo
     */
    public function formatTest($str,$key,$width=550,$score=0,$ifFormat=0,$optionWidth=0,$optionNum=0,$testNum=0,$ifChoose=0,$ifAddNum=0) {
        if($testNum>0){
            $param=array();
            $param['Test']=$str;
            $param['Key']=$key;
            $param['Width']=$width;
            $param['Score']=$score;
            $param['IfFormat']=$ifFormat;
            $param['OptionWidth']=$optionWidth;
            $param['OptionNum']=$optionNum;
            $param['IfAddNum']=0;
            $str=$this->changeHZ($param);
        }else{
            if($ifChoose==0){
                $param=array();
                $param['Test']=$str;
                $param['Key']=$key;
                $param['Width']=0;
                $param['Score']=0;
                $param['IfFormat']=0;
                $param['OptionWidth']=0;
                $param['OptionNum']=0;
                $param['IfAddNum']=$ifAddNum;
                $str=$this->changeGS($param);
            }else{
                $param=array();
                $param['Test']=$str;
                $param['Key']=$key;
                $param['Width']=$width;
                $param['Score']=$score;
                $param['IfFormat']=$ifFormat;
                $param['OptionWidth']=$optionWidth;
                $param['OptionNum']=$optionNum;
                $param['IfAddNum']=$ifAddNum;
                $str=$this->changeGS($param);
            }
        }
        return $str;
    }

    /**
     * 替换试题的小题序号
     * @param string $str 试题内容
     * @param int $key 序号
     * @return array
     * @author demo
     */
    public function replaceStr($str,$key){
        $tmpStr=$this->xtnum($str,3); //切割小题
        if($tmpStr){
            foreach($tmpStr as $i=>$iTmpStr){
                if($i==0) continue;
                $tmpStr[$i]=$key.$this->symbolForNum.$iTmpStr;
                $key++;
            }
            $str=implode('',$tmpStr);
        }
        $tmpStr=$this->xtnum($str,4); //切割题号
        if($tmpStr){
            foreach($tmpStr as $i=>$iTmpStr){
                if($i==0) continue;
                $tmpStr[$i]='<u>　'.$key.'　</u>'.$iTmpStr;
                $key++;
            }
            $str=implode('',$tmpStr);
        }
        return $str;
    }

    /**
     * 排序替换小题显示序号  输出数组
     * @param array $listArray 试题id数组
     * @param array $testList 试题数据集
     * @return array
     * @author demo
     */
    public function replaceTest($listArray,$testList,$pre='') {
        $output=array(); //输出数据
        $testArray=array(); //存储以TestID为键的试题数据
        $key=1; //试题序号
        //获取以TestID为键的试题数据
        foreach($testList as $iTestList){
            $testArray[$iTestList['TestID']]=$iTestList;
        }
        //添加试题序号
        foreach($listArray as $iListArray){
            if($testArray[$iListArray]){
                //替换小题标签
                $countSubStr=$this->xtnum($testArray[$iListArray][$pre.'Test'],1);
                if($countSubStr){
                    $testArray[$iListArray][$pre.'Test']=$this->replaceStr($testArray[$iListArray][$pre.'Test'],$key);
                    $testArray[$iListArray][$pre.'Answer']=$this->replaceStr($testArray[$iListArray][$pre.'Answer'],$key);
                    $testArray[$iListArray][$pre.'Analytic']=$this->replaceStr($testArray[$iListArray][$pre.'Analytic'],$key);
                    $testArray[$iListArray][$pre.'Remark']=$this->replaceStr($testArray[$iListArray][$pre.'Remark'],$key);
                    $key+=$countSubStr;
                }else{
                    //添加序号
                    $testArray[$iListArray][$pre.'Test']=$key.$this->symbolForNum.$testArray[$iListArray][$pre.'Test'];
                    $key++;
                }
                $output[]=$testArray[$iListArray];
            }
        }
        return $output;
    }


    /**
     * 获取选项宽度
     * @param string $str 选项字符串
     * @return string
     * @author demo
     **/
    public function getStrWidth($str){
        //如果有多个相同的选项返回0
        $buffer=preg_split('/A(\.|．)/s',$str);
        if(count($buffer)>2) return array(0,0);

        $str=R('Common/TestLayer/delMoreTag',array($str)); //去除多余标记
        $keywords=$this->formatStrToArr($str); //提取选项

        $len=count($keywords); //选项数量+1
        $strWidth=$this->calcOptionWidth($keywords); //计算选项宽度

        $len = empty($len) ? 0 : ($len-1);
        $output=array();
        $output[0]=$len; //选项数量
        $output[1]=$strWidth; //选项宽度数组
        return $output;
    }

    /**
     * 计算选项宽度
     * @author demo
     */
    protected function calcOptionWidth($keywords){
        $len=count($keywords); //选项数量+1
        //循环选项
        for($i=1;$i<$len;$i++){
            $lenArr[$i]=$this->calcSingleOptionWidth($keywords[$i]);
        }

        return $lenArr;
    }

        /**
     * 计算选项宽度
     * @author demo
     */
    public function calcSingleOptionWidth($str){
        //文件服务器地址
        $hostIn=C('WLN_DOC_HOST_IN');
        $host=C('WLN_DOC_HOST');

        $length=0; //选项长度
        $ztlenChina=14;//单个中文字体像素
        $ztlenEnglish=7;//单个英文字体像素

        $tt=$str;
        if(strstr($tt,'<img')){
            $cacheImg=array();
            $tt=str_replace($host,'',$tt);
            //提取图片
            preg_match_all('/<img[\s]*(style=[\"|\'][^\"\']*[\"|\'])?[\s]*src=[\'|\"]([^\"\']*)[\"|\']/is',$tt,$arr);
            //获取图片宽度
            foreach($arr[2] as $arrn){
                if($hostIn) $imgpath=($hostIn.$arrn);
                else  $imgpath=(dirname(realpath(APP_PATH)).'/www'.$arrn);

                if($cacheImg[$imgpath]){
                    $imginfo=$cacheImg[$imgpath];
                }else{
                    $imginfo=getimagesize($imgpath);
                }
                $length+=$imginfo[0];
            }
        }
        $tt=preg_replace('/<[^>]*>/is','',$tt);
        $numArr = $this->strLength($tt);
        $cn = $numArr['cn']*$ztlenChina;
        $en = $numArr['en']*$ztlenEnglish;
        $length+=$cn+$en;
        return $length;
    }
    /**
     * 判断数据的测试类型
     * @param array $arr 数据数组 题文和答案 $arr(Test=>'',Answer=>'')
     * @return array
     * @author demo
     */
    public function judgeChoose($arr){
        $xt=$this->xtnum($arr['Test'],1); //小题或题号数
        if($xt==0){
            return $this->oneChoose($arr);
        }else{
            $tmpArr=array();
            $tw=$this->xtnum($arr['Test'],3); //小题切割
            if(empty($tw)) $tw=array();
            $da=$this->xtnum($arr['Answer'],3); //小题切割
            if(empty($da)) return 0;
            foreach($da as $i=>$iDa){
                if($i!=0){
                    $tmpArr[]=$this->oneChoose(array('Test'=>$tw[$i],'Answer'=>$iDa));
                }
            }
            return array(1,$tmpArr);
        }
    }
    /**
     * 判断一个没有小题的字符串的测试类型
     * @param array $arr 数据数组 题文和答案 $arr(Test=>'',Answer=>'')
     * @return array
     * @author demo
     */
    public function oneChoose($arr){
        $tmpArr=array();
        $tmp_test=preg_replace('/<[^>]*>|[\r\n\s　]*/s','',$arr['Test']);
        //判断题文有没有abcd。
        preg_match_all($this->_regAD,$tmp_test,$tmpArr); //提取选项
        $tmp_arr1=array_unique($tmpArr[1]);
        if(!empty($tmp_test)){
            if(count($tmp_arr1)<3 || count($tmpArr[1])!=count($tmp_arr1)){
                return 0;
            }
        }
        //判断答案
        $tmpStr=preg_replace('/<[^>]*>/s','',$arr['Answer']);
        if(strlen($tmpStr)>10) return 0;
        $tmpStr=preg_replace('/[^A-H]*/s','',$tmpStr);

        if(strlen($tmpStr)==1) return 3;//单选题
        if(strlen($tmpStr)>1 && strlen($tmpStr)<5) return 2;//多选题
        return 0;
    }

    /**
     * 获取最大选项宽度；
     * @param string $test 题文内容
     * @return int
     * @author demo
     */
    public function getOptionWidth($test){
        $buffer=$this->xtnum($test,3);
        if(empty($buffer)){
            $data=$this->getStrWidth($test);
            $num=$data[0];
            $width=max($data[1]);
            if($width) return array(array($num,$width));
            else return array(array(0,0));
        }else{
            $output=array();
            foreach($buffer as $i=>$iBuffer){
                if($i==0) continue;
                $data=$this->getStrWidth($iBuffer);
                $num=$data[0];
                $width=max($data[1]);
                if($width) $output[]=array($num,$width);
                else $output[]=array(0,0);
            }
            return $output;
        }
    }
    /*
     * 处理公式信息
     * @param string $cotnent 处理的内容
     * @return array
     * @author demo
     * @date 2014-10-28
    */
    public function getEquations($content,$delimiter='#E#'){
        $content = explode($delimiter, $content);
        if(empty($content[0])){
            return array();
        }
        return $content;
    }

    /*
     * 添加公式信息
     * @param int $id  test表id
     * @pararm array $data公式内容
     * @return boolean 成功返回true，或者返回false
    */
    public function addEquation($id,$data,$delimiter='#E#'){
        $tmpData['Equation'] = implode($delimiter, $data);
        $result = $this->updateData(
                $tmpData,
                'TestID='.$id);
        if($result === false){
            return $result;
        }
        return true;
    }
   /**
    *根据,间隔的ID字符串，判断该试卷的试题是否超出限制
    *@param string $TestIDStr；
    *@return string 返回提示信息
    *@author demo
    **/
    public function checkTypeOver($testIDStr){
        $types=SS('types');
        $buffer = $this->getModel('TestAttrReal')->selectData(
            '*',
            'TestID in ('.$testIDStr.')'
        );
        $i=0;
        foreach($buffer as $j=>$jBuffer){
            $testTotal[$i]['TypesID']=$buffer[$j]['TypesID'];
            $testTotal[$i]['Num']=$types[$buffer[$j]['TypesID']]['Num'];
            $testTotal[$i]['TypesName']=$types[$buffer[$j]['TypesID']]['TypesName'];
            $testTotal[$i]['total']=1;
            $i++;
        }
        $newArr=array();
        $result=array();
        foreach($testTotal as $i=>$iTestTotal){//按年级，分科分组
            $newArr[$iTestTotal['TypesName']]['items'][]=array_slice($iTestTotal,0,3);
        }
        foreach($newArr as $i=>$iNewArr){
            $result[]=array('TypesName'=>$i,'items'=>$iNewArr['items']);
        }
        $msg='';
        foreach($result as $i=>$iResult){
            if(count($result[$i]['items'])>$result[$i]['items'][0]['Num']){
                $msg.='['.$result[$i]['TypesName'].']';
            }
        }
        return $msg;
    }

    /**
     * 以试题id获取试题列表
     * @param int $testID 试题id
     * @return array 返回以试题id为键的数组
     * @author demo
     */
    public function getTestListByID($testID){
        //兼容数组和字符串
        if(is_array($testID)) $testID=implode(',',$testID);

        //获取试题id对应数据集
        $testListArray = $this->selectData(
            '*',
            'TestID in ('.$testID.')'
        );

        //以试题id为键值
        $testListArrayByID=array();
        foreach($testListArray as $iTestListArray){
            $testListArrayByID[$iTestListArray['TestID']]=$iTestListArray;
        }
        return $testListArrayByID;
    }

    /**
     * 把从索引里取出的答案丶解析和备注格式化为数组
     * @param string $str  索引取出的试题,答案,解析字符串
     * @param int $testNum 试题数量
     * @param int $testID 试题id 分割错误时记录日志使用
     * @param bool $ifTest 是否是试题字符串
     * ## 返回数据格式说明
     * 答案解析数组返回同等数目数组 array(0=>,1=>,...)
     * 试题返回 array('content'=>题文主题部分,1=>,2=>,3=>)1,2,3对应小题题文 英语特殊情况已做处理
     * ##
     * @return array
     * @autho 
     */
    public function formatToArray($str,$testNum,$testID,$ifTest=false){
         if($testNum==0){//非复合题 索引默认单题$testNum为0
             if($ifTest){
                return array('content'=>$str);//构造统一格式
             }
             return array($str);
         }
         //获取分割标签后的数组
         $arr = $this->xtnum($str,3);

         //通过数组看是否是特殊情况
         //目前已知特殊情况:英语学科 七选五/语法填空
         $isSpecial = false;
         if($arr==0){
             $isSpecial = true;
         }

         //获取分割的数据
         $split = array();
         for($i=0;$i<$testNum;$i++){//以$testNum为准构建数组
            $split[$i]=$arr[$i+1]?$arr[$i+1]:'';
         }

         //试题特殊处理
         if($ifTest){
            if($isSpecial){
                $split['content'] = $str;
            }else{
                $split['content'] = $arr[0];
            }
         }

         //错误记载
         if(count($arr)!=$testNum+1){
            if(!$isSpecial) {
                $data = array();
                $data['description'] = '复合题分割错误';
                $data['msg'] = '题号为:' . $testID . '的试题分割小题时出错';
                $this->unionSelect('addErrorLog',$data);
            }
         }

         return $split;
    }

    /**
     * 重复试题数据替换
     * @param array $source 原试题数据集
     * @param array $tag 新试题数据集
     * @param int $case 小写转换
     * @return 数据集
     * @author demo
     */
    public function changeArrayValue($source,$tag,$case=1){
        //对部分数据进行替换
        $array=array(
            'TestID','DocID','NumbID','Duplicate',
            'FirstLoadTime','LoadTime','ShowWhere',
            'Status','DocName','TypeID',
            'DocYear','SourceID','Times'
        );
        if($case){
            foreach($array as $i=>$iArray){
                $array[$i]=strtolower($iArray);
            }
        }

        foreach($source as $j=>$jSource){
            if(!in_array($j,$array)){
                if(empty($tag[$j])) continue;
                $source[$j]=$tag[$j];
            }
        }
        return $source;
    }
}
?>