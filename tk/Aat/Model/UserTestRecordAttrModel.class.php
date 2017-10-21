<?php

namespace Aat\Model;

use Common\Model\BaseModel;
class UserTestRecordAttrModel extends BaseModel
{
    /**
     * 是否为打分试题
     * @param int $recordId
     * @return boolean
     * @author demo 16-4-27
     */
    public function isEvalTest($recordId){
        $score=0;
        $buffer=$this->getModel('UserTestRecordAttr')->findData('*','TestRecordID='.$recordId);
        if($buffer['DocID']){
            $data = $this->unionSelect('getDocByRecordID', $recordId);
            $score = (int)$data['AatTestStyle'];
        }else{
            $buffer=$this->getModel('UserTestRecord')->findData('*','TestID='.$recordId);
            //判断所有试题所在文档是否都支持打分
                    $thisBuffer=$this->getModel('TestAttrReal')->selectData('TestID,DocID','TestID in ('.$buffer['Content'].')');
                    $docArr=array();
                    foreach($thisBuffer as $iThisBuffer){
                        $docArr[]=$iThisBuffer['DocID'];
                    }
                    $docArr=  array_unique($docArr);
                    $docBuffer = $this->getModel('Doc')->selectData( 'DocID,AatTestStyle', 'DocID in ('.implode(',',$docArr).')');
                    $score=1;
                    foreach($docBuffer as $iDocBuffer){
                        if($iDocBuffer['AatTestStyle']!=1) $score=0;
                    }
        }
        if(1 == $score){
            return true;
        }
        return false;
    }

    /**
     * 返回自评描述
     * @param int $record 试卷id
     * @author demo 16-5-3
     */
    public function getEvaluateDescription($record){
        return $this->unionSelect('getEvaluateDescription', $record);
    }

    /**
     * 判断专题试卷是否已经做过
     * @param int $id 专题id
     * @param string $userName 用户名
     * @author demo
     */
    public function getIfDoTopic($id,$userName){
        $buffer=$this->findData('TestRecordAttrID,TestRecordID','UserName="'.$userName.'" and TopicPaperID='.$id,'TestRecordAttrID DESC');
        if(empty($buffer)) return 0;
        return $buffer['TestRecordID'];
    }
}