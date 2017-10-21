<?php
/**
 * 试题难度接口
 * @author demo 2015-1-5
 */
namespace Common\Api;
class AbilityApi extends CommonApi{

    /**
     * 按学科ID获取对应能力值
     * @param array $param
     * ##参数格式:
     * $param = array(
     *     'style'=>'ability',
     *     'subjectID'=>学科ID
     * )
     * ##
     * @return array $return
     * ##返回数据格式:
     * $return = array(
     *      [0] => Array
     *           (
     *              [AbID] => 8 //能力ID
     *              [AbilitName] => 基础知识题 //能力名称
     *              [OrderID] => 1 //排序ID
     *              [SubjectID] => 12 //学科ID
     *           ),
     *      ...//表示更多同结构数据,下同
     * )
     * ##
     * @author demo
     */
    public function abilityCache($param){
        extract($param);
        $buffer=SS('abilitySubject'); //根据学科ID获取对应能力属性
        return $buffer[$subjectID];
    }
}