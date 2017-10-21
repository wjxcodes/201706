<?php
/**
 * @author demo  
 * @date 2014年9月2日
 * @lastUpdateDate 2014年11月24日
 */
/**
 * zj_testdoc表操作
 */
namespace Test\Model;
class TestDocModel extends BaseModel{

    // 提取公式使用 
    private static $startLimitStr = '[if'; //查询公式起始字符
    private static $endLimitStr = 'endif'; //查询公式截止字符
    /**
    * 根据Doc表主键id，提取公式
    * @param int $id 指定的id
    * @param boolean $fetchAnalytic 是否提取解析内容，默认为false
    * @return array
    * @author demo
    */
    public function fetchEquationByDocId($id,$fetchAnalytic=false){
            $result = $this->unionSelect('TestDocRealSelectByDocID',$id);
        if(empty($result)){
            $result = $this->unionSelect('TestDocSelectByDocID',$id);
        }
        $data = array();
        foreach($result as $value){
            $data[$value['TestID']] = $this->fetchEquationById($value['TestID'],$fetchAnalytic);
        }
        return $data;
    }

    /**
    * 根据表主键id，提取公式
    * @param int $id 指定的id
    * @param boolean $fetchAnalytic 是否提取解析内容，默认为false
    * @return array
    * @author demo
    */
    public function fetchEquationById($id,$fetchAnalytic=false){
        $field = 'DocTest';
        if($fetchAnalytic){
            $field .= ',DocAnalytic';
        }
        $result = $this->findData(
            $field,
            'TestID='.(int)$id);
        //---------------------------test print--------------------
        //print_r('题号：'.$id.'<br>'.htmlspecialchars($result['DocTest']));
        //echo '<br>';
        $docTest = self::fetchEquation($result['DocTest']);
        if(!$fetchAnalytic){
            return array('DocTest'=>$docTest);
        }
        //---------------------------test print--------------------
        //echo '<br>解析<br>';
        //print_r('题号：'.$id.'<br>'.htmlspecialchars($result['DocAnalytic']));
        //echo '<br>';
        $docAnalytic = self::fetchEquation($result['DocAnalytic']);
        return array(
            'DocTest'=>$docTest,
            'DocAnalytic'=>$docAnalytic
        );
    }

    /**
     * 根据$content内容提取公式
     * @param string $content 需要提取的字符串
     * @return array 根据公式出现先后顺序
     * @author demo
    */
    public static function fetchEquation($content){
        $assemble = self::findConditionPos($content);
        #dump($assemble);
        //根据键的长度进行排序
        $rules = self::getRules();
        usort($rules['start'],function($key,$value){
            if(strlen($key) < strlen($value)){
                return 1;
            }
            return 0;
        });
        $data = array();
        $temp = $assemble;
        foreach($temp as $k=>$v){
            $boundary = self::findBoundary($content,$k);
            if($boundary === false){
                unset($assemble[$k]);
                continue;
            }
            $rule = $rules['start'];
            $mark = 'start';
            if(self::$endLimitStr == $v){
                $mark = 'end';
                $rule = $rules['end'];
            }
            $pos = self::isLawful($content,$boundary['left'],$boundary['right'],$rule);
            if(!$pos){
                unset($assemble[$k]);
                continue;
            }
            $boundary['mark'] = $mark;
            $data[] = $boundary;
        }
        unset($temp);
        $data = self::getEquations($content,$data);
        //---------------------------test print--------------------
        //echo '<hr>';
        return $data;
    }

    /**
     * 查询指定条件的位置
     * @param string $content 查找的内容
     * @return array返回查找后的数组
     * @author demo
    */
    private static function findConditionPos($content){
        $content =  strtolower($content); //转为小写便于条件查询
        $assemble = array(); // 保存位置信息的数组
        $temp = $content;
        while(($p = strrpos($temp,self::$startLimitStr)) !== false){
            $assemble[$p] = self::$startLimitStr;
            $temp = substr($temp,0,$p);
        }
        $temp = $content;
        while(($p = strrpos($temp,self::$endLimitStr)) !== false){
            $assemble[$p] = self::$endLimitStr;
            $temp = substr($temp,0,$p);
        }
        ksort($assemble);
        unset($temp,$content);
        return $assemble;
    }

       /**
        * 查找两侧标签'<','>'
        * @param string $content 需要查询的内容
        * @param int $pos 查找的指定位置
        * @param string $leftLimit 左侧标签
        * @param string $rightLimit 右侧标签
        * @return mixed 找到返回键值数组，或者返回false
        * @author demo
       */
       private static function findBoundary($content,$pos,$leftLimit='<',$rightLimit='>'){
           $left = $right = $pos;
           $len = strlen($content);
           //左侧查找
           while($content[--$left] != $leftLimit && $left >= -1){}
           //右侧查找
           while($content[++$right] != $rightLimit && $right <= $len-1){}
           if($left < 0 || $right == $len){
               return false;
           }
           return array('left'=>$left,'right'=>$right);
       }

    /**
     * 检查字符串片段在content是否为合法的公式段落
     * @param string $content 检查的原始内容
     * @param int $start 所在位置索引
     * @param int $end 索引截止位置
     * @param array $rule 验证规则
     * @param string $reg 指定正则表达式规则 例如：'^[0-9]%s\s+$'
     * @return boolean 合法返回true
     * @author demo
    */
    private static function isLawful($content,$start,$end,$rule=array(),$reg=''){
        foreach($rule as $value){
            $segement = substr($content,$start,$end-$start+1);
            if($reg !== ''){
                $value = sprintf($reg,$value);
            }
            if(preg_match('/'.$value.'/m', $segement)){
                return true;
            }
        }
        return false;
    }

    /**
     * 获取公式
     * @param string $content 查询的字符串
     * @param array $data处理后的数组
     * @return array
     * @author demo
    */
    private static function getEquations($content,$data){
        if(!self::isTwain($data)){
            return array(); 
        }
        $size = count($data);
        $temp = $data;
        foreach($temp as $k=>$v){
            if($k+1 == $size)
                continue;
            $next = $data[$k+1];
            //判断当前语法是否为临近
            if('end' == $v['mark'] && 'start' == $next['mark']  && ($v['right']+1) == $next['left']){
                $rules = self::getRules();
                //验证$v['left']到$next['rigth']之间部分是否是特殊的部分
                if(self::isLawful($content,$v['left'],$next['right'],$rules['specialEnd'])){
                    unset($data[$k],$data[$k+1]); //此部分的数据将不再进行处理
                }
            }
        }
        unset($temp);
        $set = array();
        $size = count($data);
        $index = 0;
        foreach($data as $k=>$v){
            if(0 == $k){
                $set[$index][] = $v;
            }else if($k+1 == $size){
                $set[$index][] = $v;
            }else{
                if(self::isTwain($set[$index])){
                    $index++;
                }
                $set[$index][] = $v;
            }
        }
        $data = array();
        $rules = self::getRules();
        foreach($set as $value){
            $start = $value[0]['left'];
            $end = $value[count($value)-1]['right'];
            $str = substr($content,$start,$end-$start+1);
            if(!self::isLawful($str,0,strlen($str)-1,$rules['include'],'^%s')){
                continue;
            }
            $data[] = $str;
            //---------------------------test print--------------------
            //echo ('<br>位置偏移：left:'.$start.' right:'.$end.'<br>');
            //print_r(htmlspecialchars($str));
            //echo '<br>';
        }
        return $data;
    }

    /**
    * 检查start,end是否结对
    * @param $data
    * @return boolean 是为ture
    * @author demo
    */
    private static function isTwain($data){
        $left = $right = 0;
        foreach($data as $value){
            if($value['mark'] == 'start'){
                $left++;
            }else{
                $right++;
            }
        }
        return $left == $right;
    }

    /**
     * 验证规则
     * @author demo
    */
    private static function getRules(){
        return array(
                'start' => array(
                    '<\!\-\-\[if gte msEquation \d{1,}\]>',
                    '<\!\[if \!msEquation\]>',
                    '<\!\[if \!vml\]>',
                    '<\!\[if gte vml \d{1,}\]>',
                    '<\!\-\-\[if supportFields\]>',
                    '<\!\-\-\[if gte vml \d{1,}\]>',
                    '<\!\-\-\[if gte mso \d{1,}\]>'
                ),
                'end' => array(
                    '<\!\[endif\]\-\->',
                    '<\!\[endif\]>'
                ),
                //特殊的截止形式，用于判别之前和之后的对应关系
                'specialEnd' => array(
                    '<\!\[endif\]\-\-><\!\[if \!msEquation\]>',
                    '<\!\[endif\]\-\-><\!\[if \!vml\]>'
                    //'<\![endif\]><\!\-\-\[if gte mso 9\]><xml> <o:OLEObject Type="Embed" ProgID="Word\.Document\.\d{1,}"'
                ),
                //如果不包含此规则的内容将不会被输出，getEquations()使用
                'include' => array(
                    '<\!\-\-\[if gte msEquation \d{1,}\]>',
                    '<\!\[if \!msEquation\]>'
                    //'<\!\-\-\[if gte mso \d{1,}\]>' mathtype公式 ，但是同时包含了Word.Document.12的内容。。
                )
            );
    }
}