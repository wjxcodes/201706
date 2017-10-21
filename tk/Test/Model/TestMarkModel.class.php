<?php
/**
 * @author demo 
 * @date 
 * @update 2015年1月26日
 */
/**
 * 自定义打分管理类Model，用于自定义打分的相关操作
 */
namespace Test\Model;
class TestMarkModel extends BaseModel{
    /**
     * 获取所有数据
     * @return array
     * @author demo
     */
    public function getAllData(){
        return $this->selectData(
            '*',
            '1=1',
            'MarkID asc');
    }

    /**
     * 缓存数组；
     * @author demo
     */
    public function setcache(){
        $testMark=array();
        $testMarkSubject=array();
        $buffer = $this->selectData(
            '*',
            '1=1',
            'MarkID asc');
        if($buffer)
            foreach($buffer as $iBuffer){
                $testMark[$iBuffer['MarkID']]['MarkName']=$iBuffer['MarkName'];
                $testMark[$iBuffer['MarkID']]['SubjectID']=$iBuffer['SubjectID'];
                $testMark[$iBuffer['MarkID']]['OrderID']=$iBuffer['OrderID'];
                $testMark[$iBuffer['MarkID']]['MarkList']=$iBuffer['MarkList'];
                $tmp=explode('#',$iBuffer['MarkList']);
                $tmp=array_filter($tmp);
                $tmp=array_values($tmp);
                $tmpArr=array();
                foreach($tmp as $jTmp){
                    $tmp2=explode('|',$jTmp);
                    $tmpArr[(string)round($tmp2[0],2)]=$tmp2[1];
                }
                $testMark[$iBuffer['MarkID']]['MarkListArray']=$tmpArr;
                $testMark[$iBuffer['MarkID']]['Style']=$iBuffer['Style'];
                $testMarkSubject[$iBuffer['SubjectID']][]=$iBuffer;
            }
        //题型按照分卷调整顺序
        S('testMark',$testMark);//试卷分类，ID作键
        S('testMarkSubject',$testMarkSubject);//根据学科ID分组缓存
        //
    }
    /**
     * 获取返回+缓存验证生成
     * @param string 缓存标识
     * @return array 对应标识的缓存数据
     * @author demo
     */
    public function getCache($str='testMark',$num=0){
        if($str=='testMark'){
            $buffer=S('testMark');
        }elseif($str=='testMarkSubject'){
            $buffer=S('testMarkSubject');
        }
        if(empty($buffer) && $num==0){
            $this->setCache();
            $buffer=$this->getCache($str,1);
        }
        return $buffer;
    }

    /**
     * 编辑试题自定义打分mark字段分割
     * @param int 学科
     * @param string mark字段字符串
     * @author demo
     */
    public function markStrToArr($subjectID,$markStr){
        $testMarkArray=SS('testMarkSubject');
        $markArray=$testMarkArray[$subjectID];
        if($markArray){
            foreach($markArray as $i=>$iMarkArray){
                $markArray[$i]['MarkListx']=formatString('str2Arr',$markArray[$i]['MarkList']);
                foreach($markArray[$i]['MarkListx'] as $j=>$jMarkList){
                    $markArray[$i]['MarkListx'][$j][3]=$markArray[$i]['MarkID'].'|'.$markArray[$i]['MarkListx'][$j][0];
                    $markInfo[$iMarkArray['MarkID']][$jMarkList['0']]=$jMarkList;
                    $markInfo[$iMarkArray['MarkID']][$jMarkList['0']][0]=$iMarkArray['MarkID'];
                }
            }
            $markArr[0]=formatString('str2Arr',$markStr);
            $markArr[1]=$markArray;
            $markArr[2]=$markInfo;
            return $markArr;
        }
    }
}