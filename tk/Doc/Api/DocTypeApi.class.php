<?php
/**
 * 文档类型api
 * @author demo 2015-1-5
 */
namespace Doc\Api;
use Common\Api\CommonApi;
class DocTypeApi extends CommonApi{

    /**
     * 获取文档类型缓存
     * @param array $param
     * ##参数说明:
     * $param = array(
     *     'style'='docType',
     *     'ID'=>文档类型ID //可选项,有ID时,表示该ID对应的文档类型缓存,否则获取全部缓存
     *     'field'=> //可选项,指定获取文档类型的字段,有ID时,默认值为'DefaultTest',无ID时,默认值为'TypeID,TypeName,GradeList'
     * )
     * ##
     * @return mixed $return
     * ##返回数据格式:
     * 有ID时,没有给field传值则获取默认值'DefaultTest'字段的值,否则获取所传字段的值,这里以默认字段举例
     * $return = 2;
     * 无ID时,没有给field传值则获取默认值'TypeID,TypeName,GradeList'字段的值,否则获取所传字段的值,这里也用默认字段举例
     * $return = array(
     *      [0] => Array
     *           (
     *               [TypeID] => 8 //文档类型ID
     *               [TypeName] => 月考试卷 //文档类型名称
     *               [GradeList] => 2,3,4,6,7,8 //所属年级
     *           ),
     *      [1] => Array
     *           (
     *               [TypeID] => 9
     *               [TypeName] => 期中试卷
     *               [GradeList] => 2,3,4,6,7,8
     *           )
     *     ...
     * )
     * ##
     * @author demo
     */
    public function docTypeCache($param){
        extract($param);
        $typeArray=$this->docType();

        //返回指定id的指定字段
        if($ID){
            if(empty($field)) $field='DefaultTest';
            return $typeArray[$ID][$field];
        }

        if($ifDel){
            unset($typeArray[12]); //去掉自助招生
        }

        //返回以默认数字为键的数组
        if($typeArray){
            $k=0;
            if(empty($field)) $field='TypeID,TypeName,GradeList';
            $fieldArray=explode(',',$field);
            foreach($typeArray as $i=>$iTypeArray){
                foreach($fieldArray as $j=>$jFieldArray){
                    $output[$k][$jFieldArray]=$iTypeArray[$jFieldArray];
                }
                $k++;
            }
        }
        return $output;
    }


    public function docType(){
        return SS('docType');
    }
}