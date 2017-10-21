<?php
/**
 * 学科接口
 * @author demo 2015-1-5
 */
namespace Common\Api;
class SubjectApi extends CommonApi{


    /**
     * 获取学科对应父类id数据集
     * @return 学科数据集
     * @author demo
     */
    public function subjectParentID(){
        return SS('subjectParentId');
    }

    /**
     * 返回键值学科缓存数据
     * @return array
     * @author demo
     */
    public function subject(){
        return SS('subject');
    }

    /**
     * 获取学科数据集
     * @return 学科数据集
     * @author demo
     */
    public function getSubjectFullList(){
        $buffer = $this->subjectParentID();

        $output=array();
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
//                if($iBuffer['SubjectID']>20) continue; //隐藏初中部分
                $output[$i]['SubjectID']=$iBuffer['SubjectID'];
                $output[$i]['SubjectName']=$iBuffer['SubjectName'];
                $output[$i]['FullName']=$iBuffer['FullName'];
                if($iBuffer['sub']){
                    foreach($iBuffer['sub'] as $j=>$jBuffer){
                        $output[$i]['sub'][$j]['SubjectID']=$jBuffer['SubjectID'];
                        $output[$i]['sub'][$j]['SubjectName']=$jBuffer['SubjectName'];
                        $output[$i]['sub'][$j]['Layout']=$jBuffer['Layout'];
                        $output[$i]['sub'][$j]['Style']=$jBuffer['Style'];
                    }
                }
            }
        }
        return $output;
    }
}