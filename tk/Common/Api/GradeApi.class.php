<?php
/**
 * 年级接口
 * @author demo 2015-1-5
 */
namespace Common\Api;
class GradeApi extends CommonApi{
    /**
     * 获取指学科ID的获取对应阶段的年级
     * @param array $param 参数格式:
     * $param = array(
     *     'style'=>'grade',
     *     'subjectID'=>学科ID
     * )
     * @return array $return 返回数据格式
     * $return =Array
     *    (
     *       [0] => Array
     *       (
     *           [GradeID] => 2  //年级ID
     *           [GradeName] => 高一 //年级名称
     *           [SubjectID] => 2 //指定学科的父类ID
     *           [OrderID] => 99 //排序
     *       ),
     *       [1] => Array
     *       (
     *           [GradeID] => 3
     *           [GradeName] => 高二
     *           [SubjectID] => 2
     *           [OrderID] => 99
     *       ),
     *       [2] => Array
     *       (
     *           [GradeID] => 4
     *           [GradeName] => 高三
     *           [SubjectID] => 2
     *           [OrderID] => 99
     *       )
     *   )
     * @author demo
     */
    public function gradeCache($param){
        extract($param);
        $classGrade=SS('gradeListSubject');
        $subjectArr=SS('subject');
        return $classGrade[$subjectArr[$subjectID]['PID']]['sub'];
    }

    /**
     * 获取年级列表以年级id为键值
     * @param int $gradeID 年级id 如果没有则返回所有数据；如果有仅返回当前年级id对应数据
     * @return 以年级id为键值数据集或指定年级id数据集
     * @author demo
     */
    public function grade($gradeID=0){
        $classGrade=SS('grade');
        if(!empty($gradeID)) return $classGrade[$gradeID];
        return $classGrade;
    }

    /**
     * 获取年级列表以学科为键值
     * @return 以学科为键值数据集
     * @author demo
     */
    public function gradeListSubject(){
        return SS('gradeListSubject');
    }
}