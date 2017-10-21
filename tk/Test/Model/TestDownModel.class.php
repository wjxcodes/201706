<?php
/**
 * @author demo
 * @date 2015年5月8日
 */
/**
 * 试题下载次数记录模型类，用于处理试题下载次数相关操作
 */
namespace Test\Model;
class TestDownModel extends BaseModel{
    /**
     * 记录下载试题次数
     * @param string $testList 以逗号间隔的试题id
     * @return NULL
     * @author demo
     */
    public function setDownTime($testList){
        if(empty($testList)) return ;
        if(!is_array($testList)) $testList=explode(',',$testList);
        $tmpArr=array_unique($testList); //去重
        $testList=implode(',',$tmpArr);

        $testIDBuffer=array(); //存储数据表中存在的试题id
        $testIDTimes=array(); //存储试题id为键的试题下载次数
        $buffer = $this->selectData(
            '*',
            'TestID in ('.$testList.')');

        if($buffer){
            foreach($buffer as $iBuffer){
                $testIDBuffer[]=$iBuffer['TestID'];
                $testIDTimes[$iBuffer['TestID']]=$iBuffer['Times'];
            }
        }

        if($tmpArr){
            foreach($tmpArr as $iTmpArr){
                if(in_array($iTmpArr,$testIDBuffer)){
                    //更新试题下载次数
                    $this->updateData(
                        array('Times'=>($testIDTimes[$iTmpArr]+1)),
                        'TestID='.$iTmpArr);
                    continue;
                }
                $this->insertData(
                    array('TestID'=>$iTmpArr,'Times'=>1));
            }
        }
    }
}
?>