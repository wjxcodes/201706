<?php
/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-4-14
 * Time: 下午2:51
 */
namespace Aat\Model;

use Common\Model\BaseModel;
class StandardModel extends BaseModel{
    /**
     * 获取某学科的标准卷信息
     * @param $subjectId int subjectID
     * @return array 标准卷的难度 分数 猜测系数[Diff,Score,Guess]
     */
    public function getStandard($subjectId){
        $tmp = $this->selectData(
                'Diff,Score,Guess',
                'SubjectID='.$subjectId);
        $result = [];
        foreach($tmp as $k){
            $result['diff'][] = $k['Diff'];
            $result['score'][] = $k['Score'];
            $result['guess'][] = $k['Guess'];
        }
        return $result;
    }
}