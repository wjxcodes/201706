<?php
/**
 * 下载word之前，生成随机的内容加入word
 * @author demo
 * @date 2015-4-27
 */
namespace Doc\Model;
class RandomIdentificationModel{

    private static $percent = 0.15;

    /**
     * 添加随机的内容，处理后返回改数据
     * @param string $text 处理的字符串
     * @param string|array $content 当为字符串时，将根据$tag参数处理相关信息，
     *                          当为数组时格式为：array('xxx',....)，$tag也需为索引所租，与其对应
     * @param string|array $tag 添加到文档中的标签，包含1到2个占位符
     * @return string 
     */
    public static function addRandomContent($text, $content ,$tag='<span %s>%s</span>'){
        $textPositions = self::getTextPositions($text);
        $keyPos = array_keys($textPositions);
        $textPositionsSize = count($textPositions);
        $several = is_array($content);
        $finished = 0;
        $max = 0;
        if($several){
            $max = count($content)-1;
        }
        for($i=0; $i<$textPositionsSize; $i++){
            if(!$several){
                $val = self::produce($tag, $content);
            }else{
                $random = rand(0, $max);
                $val = self::produce($tag[$random], $content[$random]);
            }
            //由于$textPositions[$i]的值是在加入$val之前生成，所以此处要加上已经生成过的$val的长度
            $pos =  $keyPos[$i] + $finished;
            $sp = $textPositions[$keyPos[$i]][0] + $finished;
            $ep = $textPositions[$keyPos[$i]][1] + $finished;
            $text = self::interceptText($text, $pos, $sp, $ep, $val);
            $finished += strlen($val); //获取已经添加的字符串长度
        }
        return $text;
    }

    /**
     * 处理生成的相关内容
     * @return string
     */
    private static function produce($tag, $content){
        $count = substr_count($tag, '%s');
        $isPic = (strpos(strtolower($tag), '<img') !== false);
        $str = $content;
        if(!$isPic){
            $str = self::getStr($content, self::getPositions($content));
        }
        if(1 == $count){
            return sprintf($tag, $str);
        }else if(2 == $count){
            return sprintf($tag, self::getRandomAttr(), $str);
        }
        return '';
    }


    /**
     * 返回插入标识的随机的位置
     * @param string $str
     * @return array 包含位置索引的数组
     */
    private static function getPositions($text){
        $positions = array();
        $len = strlen($text);
        $probability = ceil($len / self::$percent / 100);
        for($i=0; $i<$probability; $i++){
            $positions[] = rand(0, $len-1);
        }
        sort($positions);
        return $positions;
    }

    /**
     * 获取内容的运行操作的位置偏移
     * @param string $text
     * @return array
     */
    private static function getTextPositions($text){
        $positions = array();
        //匹配公式标签及所有html标签，由于影响版式所以不再表格标签中添加随机标签
        $reg = '/<\!(?:\-\-|\[).*?[^>]>.*?<\!\[[a-zA-Z]+.*?].*?[^>]>|<[A-Za-z]+:[A-Za-z]+>.*?[^<]<\/[A-Za-z]+:[A-Za-z]+>|<table\s*.*?>.*?(?!\/table)<\/table>|<.*?[^>]>/is';
        preg_match_all($reg, $text, $matches, PREG_OFFSET_CAPTURE);
        if(!empty($matches[0])){
            $matches = self::getMatchesOffsets($matches[0]); //返回可以插入相关标签的位置数组
            $matchesSize = count($matches);
            $probability = self::getProbability($matches, $text);
            //按照$probability的数量来获取随机插入内容的位置，该结果将始终小于$matchesSize
            for($i=0; $i<$probability; $i++){
                list($start, $end) = $matches[rand(0, $matchesSize-1)]; //随机获取开始和截止位置
                $random = rand($start, $end);
                $seg = substr($text, $start, $end- $start);
                if(!preg_match('/\s+|\r\n|\r|\n/', $seg) && strlen($seg) > 2){
                    $positions[$random] = array($start,$end); //此处随机位置为键，起始、截止位置为值
                }
            }
        }
        ksort($positions);
        return $positions;
    }

    /**
     * 生成随机的标签
     * @return string
     */
    private static function getRandomAttr(){
        $styles = array('color:red;', 'text-align:center;', 'height:auto;', 'cursor:pointer;');
        $length = count($styles)-1;
        $random = rand(1, $length);
        $styleStr = 'display:none;font-size:0px;width:1px;height:1px;';
        for($i=0; $i<$random; $i++){
            $index = rand(0, $length);
            $styleStr .= $styles[$index];
        }
        $classStr = '';
        $class = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz*.%$#@-_+|';
        //生成随机样式
        $length = strlen($class);
        $random = rand(5, 9);
        for($i=0; $i<$random; $i++){
            $index = rand(0, $length);
            $classStr .= $class[$index];
        }
        return sprintf('style="%s" class="%s"', $styleStr, $classStr);
    }

    /**
     * 返回随机的字符组合 
     * @param string $text 产生随机内容的字符串
     * @param array $positions 位置数组
     * @return string
     */
    private static function getStr($text, $positions){
        if(!$text){
            return '';
        }
        $str = '!@#$%*_|+()?';
        $length = strlen($str);
        $size = count($positions);
        $finished = 0; //之前的长度
        for($i=0; $i<$size; $i++){
            $index = rand(0, $length);
            $pos = $positions[$i] + $finished;
            $text = mb_substr($text, 0, $pos, 'utf-8').$str[$index].mb_substr($text, $pos, strlen($text),'utf-8');
            $finished += strlen($str[$index]);
        }
        return $text;
    }   

    /**
     * 处理字符串信息
     * @param string $text 需处理的内容
     * @param int $pos 处理的位置
     * @param int $sp <=$pos的起始位置
     * @param int $ep >=$pos的截止位置
     * @param string $str 添加的字符串
     * @return string 处理后的$text
     */
    private static function interceptText($text, $pos, $sp, $ep, $str){
        $left = $right = $pos; 
        $pos = $sp;  // 暂时在每个标签的开始部分插入标签
        // //向下查找非中文字符
        // while(!self::getWhithoutAsciiNum($text[$left]) && $left >= $sp){
        //     $left--;
        // }
        // //向上查找非中文字符
        // while(!self::getWhithoutAsciiNum($text[$right]) && $right <= $ep){
        //     $right++;
        // }
        // /*
        //     $string = 'x的说法撒的发生的范德萨ab'
        //     如果left与right不相同，则left将指向y，right将指向a
        // */
        // if($left != $right){
        //     //为真时，则证明该位置为一个ascii字符
        //     if($left >= $sp){
        //         $left++;
        //     }else{
        //         $left = $sp; //表明从$sp到$sp+2的位置是一个中文字符
        //     }
        //     //为真时，则证明该位置为一个ascii字符
        //     if($right <= $ep){
        //         $right--;
        //     }else{
        //         $right = $ep; //表明从$ep-2到$ep的位置是一个中文字符
        //     }
        //     $sub = $right - $left;
        //     //仅有一个汉字
        //     if($sub == 3){
        //         $pos = $left;
        //     }else{ //存在多个中文字符
        //         $pos = $right;//$left + rand(1, $sub / 3) * 3; //随机取出中文中需要分割的位置
        //     }
        //     //处理实体字符
        //     // if(($n = strpos('&', substr($text, $sp, $ep - $sp))) >= 0){
        //     //     if($n < $pos || $n > $pos){
        //     //         for(; $temp < $pos; ){
        //     //             if(38 == ord($text[--$temp])){ //用于对实体字符进行处理
        //     //                 $pos = $temp;
        //     //                 break;
        //     //             }
        //     //         }    
        //     //     }
        //     // }
        //     //确保不越界
        //     if($pos < $sp){
        //         $pos = $sp;
        //     }
        //     if($pos > $ep){
        //         $pos = $ep;
        //     }
        // }
        $before = substr($text, 0, $pos);
        $after = substr($text, $pos);
        return $before.$str.$after;  
    }

    /**
     * 返回存在的非ascii字符数量
     * @param string $str
     * @return boolean
     */
    private static function getWhithoutAsciiNum($str){
        $str = ord($str);
        return $str >= 0 && $str < 128;
    }

    /**
     * 根据匹配的数组，返回文本内容开始和截止的位置
     * @param array $matches
     * @return array
     */
    private static function getMatchesOffsets($matches){
        $offsets = array();
        $size = count($matches);
        for($i=0; $i<$size-1; $i++){
            list($len, $offset) = $matches[$i];
            $nextOffest = $matches[$i+1][1];
            $len = strlen($len);
            //位置索引加上长度小于下一个匹配项的数量，则证明之间存在内容
            $start = $offset + $len;
            if($nextOffest > $start){
                //提取之间的开始于截止位置
                $offsets[] = array($start, $nextOffest);
            }
        }
        return $offsets;
    }

    /**
     * 按比例计算有多少个$matches值可以被插入标签
     * @param array $matches 匹配的结果数组
     * @param int $textLeng 文本的长度
     * @return int
     */
    private static function getProbability($matches, $text){
        return floor(count($matches)*self::$percent);
    }
}