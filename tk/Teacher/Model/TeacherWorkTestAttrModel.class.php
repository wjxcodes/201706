<?php
/**
 * @author demo
 * @date 2014年8月19日
 */
/**
 * 教师审核任务试题属性模型类，用于处理教师审核任务试题属性相关操作
 */
namespace Teacher\Model;
use Common\Model\BaseModel;
class TeacherWorkTestAttrModel extends BaseModel{
    /**
     * 更新NowRight和NowCheck为0；；
     * @param string $WCID 审核id
     * @param string $checkTimes 审核次数
     * @param string $did 文档id
     * @return array
     * @author demo
     */
    public function updateNowData($WCID,$checkTimes,$did){
        $buffer=$this->dbConn->teacherWorkTestAttrGetSelectByIdArr($WCID,$checkTimes,$did);
        $attrArray=array();
        foreach($buffer as $i=>$iBuffer){
            $attrArray[]=$iBuffer['AttrID'];
        }
        if(empty($attrArray)) return '';
        return $this->updateData(
            array('NowRight'=>0,'NowCheck'=>0),
            'AttrID in ('.implode(',',$attrArray).')');
    }
}