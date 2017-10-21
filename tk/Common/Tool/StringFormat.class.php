<?php
/**
 * 格式化字符串类  对数据进行符合标准的加工输出
 * @date 2015年6月17日
 */
class StringFormat {

    /**
     * 转义字符串为html；针对input数据
     * @param string $str
     * @return string
     * @author demo
     */
    public function changeStr2Html($str){
        return htmlspecialchars(stripslashes($str), ENT_QUOTES);
    }

    /**
     * 对字符串解码 还原url的参数
     * @param string $str 字符串
     * @return string
     * @author demo
     */
    public function decodeUrl($str) {
        return str_replace(array('%zz','%zy','%zx'),array('-','@','.'),rawurldecode($str));
    }

    /**
     * 去除用户扩展名 或返回扩展名
     * @param string $str 要检测的字符串
     * @param int $style 取值范围1,2；例子：1去除最后一个点及其后面的数据 例如 aaa.com变成aaa 2返回最后一个点后面的数据 例如 aaa.com变成com
     * @return string
     * @author demo
     */
    public function delPointData($str,$style=1){
        if(empty($style)) $style=1;

        $arr=explode('.',$str);
        if($style===1){
            if(count($arr)>1) unset($arr[count($arr)-1]);
            else return $str;
            return implode('.',$arr);
        }else if($style==2){
            if(count($arr)>1) return $arr[count($arr)-1];
            else return $str;
        }
        return $str;
    }

    /**
     * 对字符串编码 作为url的参数
     * @param string $str 字符串
     * @return string
     * @author demo
     */
    public function encodeUrl($str) {
        return rawurlencode(str_replace(array('-','@','.'),array('%zz','%zy','%zx'),$str));
    }

    /**
     * 字符串转义html实体
     * @param string $str 字符串
     * @return string 转义后的字符串
     * @author demo
     * */
    public function filterds($str){
        $str=str_replace(array('“','”'),array('"','"'),$str);
        $str=htmlspecialchars ($str,ENT_QUOTES );
        return $str;
    }

    /**
     * 生成支付宝订单唯一ID microtime+uid+随机数字
     * @author demo
     */
    public function genUUOrderID($userID){
        //microtime+uid保证唯一 随机数字增加混淆
        $time = explode('.',microtime(true));
        return $time[0].mt_rand(1,1000).$userID.$time[1];
    }

    /**
     * 生成安全码
     * @param int $length 安全码长度
     * @return String
     * @author demo
     */
    public function saveCode($length=15){
        $rand = '';
        for ($i = 0; $i < $length; $i++)
        {
            $rand .= chr(mt_rand(33, 126));
        }
        return $rand;
    }

    /**
     * @获取平均值
     * @param array $param 数据数组
     * @return float
     * @author demo
     **/
    public function getAvg($param){
        if(!empty($param) && is_array($param)){//判断数据不能为空且必须是数组
            $total=0;
            foreach($param as $val){
                $total+=$val;
            }
            return  $total/count($param);//平均数
        }else{
            return '';
        }
    }

    /**
     * 描述：隐藏部分用户名
     * @author demo
     */
    public function hiddenUserName($myName){
        //如果是邮箱
        $arr=explode('@',$myName);
        if(count($arr)>1){
            $tmpInt=strlen($arr[0]);
            if($tmpInt<3) $arr[0]=mb_substr($arr[0],0,2,'UTF-8').'***';
            else{
                $tmpInt2=floor($tmpInt/4);
                $start = $tmpInt2< 1 ? 1 : $tmpInt2;
                $arr[0]=mb_substr($arr[0],0,$start,'UTF-8').'***'.mb_substr($arr[0],$tmpInt-$start,$start,'UTF-8');
            }
            return implode('@',$arr);
        }
        //如果是手机号
        $tmpInt=strlen($myName);
        if($tmpInt==11 && is_numeric($myName)){
            $tmpStr=substr($myName,0,3).'****'.substr($myName,7,4);
            return $tmpStr;
        }
        //如果是昵称
        if($myName=='admin') $myName='a***';
        //如果账号是汉字
        /*$strlen='';
        if(preg_match("/^[\x81-\xfe][\x40-\xfe]?/",$myName)){
            //名字是三个字 UTF8
            $strlen     = mb_strlen($myName, 'utf-8');
            $firstStr     = mb_substr($myName, 0, 1, 'utf-8');
            $lastStr     = mb_substr($myName, -1, 1, 'utf-8');
            return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($myName, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
        }*/
        return $myName;
    }

    /**
     * 图片地址中IP替换指定属性(当IP地址修改以后，图片依然能够显示)
     * @param string $content 需要替换的内容
     * @return string
     * @author demo
     */
    public function IPReplace($content){
        $host=C('WLN_DOC_HOST');
        $newUrl=str_replace($host,'{#$DocHost#}',$content);
        return $newUrl;
    }

    /**
     * 把从数据库图片地址中替换成真实地址(当IP地址修改以后，图片依然能够显示)
     * @param string $content 需要替换的内容
     * @return string
     * @author demo
     */
    public function IPReturn($content){
        $host=C('WLN_DOC_HOST');
        $trueUrl=str_replace('{#$DocHost#}',$host,$content);
        return $trueUrl;
    }

    /**
     * @转换负数内容为0
     * @param int $val 数值
     * @return int 0
     * @author demo
     * @date 2014-10-21
     */
    public function neg2Zero($val){
        return ($val < 0 ? 0 : $val);
    }

    /**
     * 将指定的数字转换为中文
     * @param int $num
     * @return string
     * @author demo
     * @date 2015-6-5
     */
    public function num2Chinese($num){
        if($num==='') return '';
        $arr = array('一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五','十六');
        return $arr[$num-1];
    }

    /**
     * 描述：对象转数组
     * @param object $e 对象
     * @return array|bool 数组或者失败false
     * @author demo
     */
    public function object2Array($e){
        $e=(array)$e;
        foreach($e as $k=>$v){
            if( gettype($v)=='resource' ) return false;
            if( gettype($v)=='object' || gettype($v)=='array' )
                $e[$k]=(array)objectToArray($v);
        }
        return $e;
    }

    /**
     * 重置数组(适应formatString函数)
     * @param array $param 要重置的数组,需要转换的键名
     * @param string $pre   需要增加的键名前缀
     * @param int $ifArray  是否数组 0 适合单个$field 1适用于多个相同的$field
     * @return array
     * @author demo
     */
    public function reloadArr($param=array(),$pre='',$ifArray=0) {
        list($arr,$field) = $param;
        $output=array();
        if($arr && is_array($arr)) {
            foreach ($arr as $iArr) {
                if ($ifArray == 1) {
                    $output[$pre . $iArr[$field]][] = $iArr;
                } else {
                    $output[$pre . $iArr[$field]] = $iArr;
                }
            }
        }
        return $output;
    }

    /**
     * 移除数组中的字段 仅支持一维数组
     * @param array $array 要移除的数组
     * @param string $field 要移除的字段 以英文逗号间隔
     * @return array
     * @author demo
     */
    public function removeArrayField($array,$field){
        if(empty($array) || empty($field) || !is_array($array)) return $array;
        $fieldArray=explode(',',$field);
        foreach($fieldArray as $iFieldArray){
            unset($array[$iFieldArray]);
        }
        return $array;
    }

    /**
     * 过滤字符串中的html,php标签；针对input数据
     * @param string $str
     * @return string
     * @author demo
     */
    public function stripTags($str){
        return strip_tags(stripslashes($str), ENT_QUOTES);
    }

    /**
     * 数组化字符串 把字符串#1|2#1|2#改变成二维数组array(0=>array(1,2),1=>array(1,2))
     * @param string $str 字符串
     * @return array 二维数组
     * @author demo
     * */
    public function str2Arr($str){
        if($str){
            $arr_list = explode('#',$str);
            $arr_list = array_filter($arr_list);
            $arr_listx=array();
            foreach($arr_list as $ii=>$arr_listn){
                $arr_listx[]=explode('|',$arr_listn);
            }
            return $arr_listx;
        }
        return ;
    }

    /**
     * 字符串html实体反转义
     * @param string $str 字符串
     * @return string 反转义后的字符串
     * @author demo
     * */
    public function unfilterds($str){
        $str=htmlspecialchars_decode ($str,ENT_QUOTES );
        return $str;
    }

    /**
     * 获取方差
     * @param array $param 求方差及标准差数据
     * @return array
     * @author demo
     */
    public function getVariance($param){
        $avgnum=$this->getAvg($param);
        $totalnum=0;
        foreach($param as $val){
            $totalnum+=pow(($avgnum-$val),2);
        }
        $result['avg']=$totalnum/count($param);//总体标准偏差的方差
        $result['sqrt']=sqrt($result['avg']);//总体标准偏差
        return $result;
    }

    /**
     * 时间换算
     * @param $time 时间,以秒为单位
     * @return string 以小时分钟秒的格式的时间字符串
     * @author demo
     * @date 2014年11月13日
     */
    public function timeConversion($time){
        if($time<60){
            return $time.'秒';
        }else{
            $timeStr='';
            $sec=$time%60;//秒
            $min=floor($time/60);//分
            if($min>=60){
                $min=$min%60;
                $hour=floor($time/3600);//小时
            }
            if($hour){$timeStr.=$hour.'小时';}
            if($min){$timeStr.=$min.'分';}
            if($sec){$timeStr.=$sec.'秒';}
            return $timeStr;
        }
    }

    /**
     * 大写字母转小写 数据表查出的二维数组
     * @param array $testArray 数据表查出的二维数组
     * @param int $level 级别默认2 二维
     * @return array
     * @author demo
     */
    public function upperToLowerForArray($testArray,$level=2){
        if(empty($level)){
            $level=2;
        }
        $newTestArray=array();
        foreach($testArray as $i=>$iTestArray){
            foreach($iTestArray as $j=>$jTestArray){
                if($level==3){
                    foreach($jTestArray as $k=>$kTestArray){
                        $newTestArray[$i][$j][strtolower($k)]=$kTestArray;
                    }
                }elseif($level==2){
                    $newTestArray[$i][strtolower($j)]=$jTestArray;
                }
            }
        }
        return $newTestArray;
    }

    /**
     * 获取html中的描述数据
     * @param string $str html内容
     * @return array
     * @author demo
     */
    public function getHtmlDescription($str,$length=200){
        $str=preg_replace('/<[^>]*>|\r|\n|&nbsp;|\s|\t|	/','',$str);
        return string('msubstr',$str, 0, $length);
    }

    /**
     * 过滤标点符号
     * @param string $str html内容
     * @return array
     * @author demo
     */
    public function symbol($str){
        $arr=array(
            "，","。","!","！","：",";",
            "；",":","、","*",".",",","‘",
            "’","“","”","?","？","'",'\\',
            '/',"(",")","（","）","_","-","－"
        );
        $str=str_replace($arr,' ',$str);

        return $str;
    }
}
