<?php
/**
 * 智能组卷接口
 * @author demo 2016-05-19
 */
namespace Ga\Api;
use Common\Api\CommonApi;
class GaApi extends CommonApi{
    /**
     * 遗传算法函数
     * @param int $num 遗传代数
     * @param array $param 配置参数数组
     *           $this->setXk($param['SubjectID']);    //设置学科
     *           $this->setDocType($param['DocType']);    //设置文档类型
     *           $this->setKlType($param['KlType']);    //设置是否是知识点或者章节
     *           $this->setKlCover($param['KlCover']);    //设置知识点或章节 id
     *           $this->setDiff($param['Diff']);    //设置难度值
     *           $this->setKlPer($param['KlPer']);    //设置知识点覆盖率
     *           $this->setGrade($param['Grade']);    //设置年级
     *           $this->setChoose($param['Choose']);    //设置选做情况
     *           $this->setTypes($param['Types']);    //设置试题类型
     * @return array 返回最佳试题数组
     * @author demo
     * */
    public function main($num,$param){
        return $this->getModel('Ga')->main($num,$param);
    }

}