<?php
/**
 * @author demo
 * @date 2015年6月7日
 */
/**
 * 基础控制器类，用于处理官网基础数据相关操作
 */
namespace Common\Controller;
class DocLayerController extends CommonController{

    /**
     * 格式化试卷Cookie字符串返回数组；
     * @param array $param 参数 $param['CookieStr'] 试卷cookie字符串
     * @return array
     * @author demo
     */
    public function formatPaperCookie($str){
        $output=array();
        $tmpArray=explode('@#@',$str);
        $infoArray=array(
            'maintitle',
            'subtitle',
            'seal',
            'marktag',
            'testinfo',
            'studentinput',
            'score',
            'notice'
        );
        $ii=-1;//分卷计数
        $jj=-1;//题型计数
        foreach($tmpArray as $i=>$iTmpArray){
            $tmpArray2=explode('@$@',$iTmpArray);
            if(in_array($tmpArray2[0],$infoArray)){
                $output[$tmpArray2[0]]=$tmpArray2;
            }elseif(strstr($tmpArray2[0],'parthead')){
                $ii=$ii+1;
                $jj=-1;
                $output['parthead'][$ii]['info']=$tmpArray2;
            }elseif(strstr($tmpArray2[0],'questypehead')){
                $jj=$jj+1;
                if(!empty($tmpArray2[5])){
                    //格式化试题属性数据
                    $tmpArray3=explode(';',$tmpArray2[5]);
                    foreach($tmpArray3 as $j=>$iTmpArray3){
                        $tmpArray4=explode('|',$iTmpArray3);
                        if($tmpArray4[1]>1){
                            $tmpArray4[2]=explode(',',$tmpArray4[2]);
                        }
                        $tmpArray3[$j]=$tmpArray4;
                    }
                    $tmpArray2[5]=$tmpArray3;
                    //格式化默认分值等数据
                }
                $tmpArray2[6]=explode('|',$tmpArray2[6]);
                $output['parthead'][$ii]['questypehead'][$jj]=$tmpArray2;
            }
        }
        return $output;
    }

    /**
     * 处理试卷cookieStr提取试题ID
     * @param string $cookieStr cookie字符串
     * @return 以 , 间隔的试题ID
     * @author demo
     */
    public function explodeCookieStr($cookieStr){
        $tmpStr1=explode('@#@',$cookieStr);
        foreach($tmpStr1 as $i=>$iTmpStr1){
            $tmpStr2=explode('@$@',$iTmpStr1);
            if(count($tmpStr2) == 7 && !empty($tmpStr2[5])){
                $tmpStr3=explode(';',$tmpStr2[5]);
                foreach($tmpStr3 as $j=>$jTmpStr3){
                    $tmpStr4  =explode('|',$jTmpStr3);
                    $testNum[]=$tmpStr4[0];
                }
            }
        }
        $testStr=implode(',',$testNum);

        return $testStr;
    }
}

