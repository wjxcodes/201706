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
class TeacherWorkCheckModel extends BaseModel{

    /**
     * 根据任务ID和审核次数，获取任务编号
     * @param int $workID 任务ID
     * @param int $checkTimes 审核次数
     * @return int 任务编号
     * @author demo
     */
    public function getWCID($workID,$checkTimes){
        return $this->dbConn->findData(
            'TeacherWorkCheck',
            'WCID',
            'WorkID='.$workID.' AND CheckTimes='.$checkTimes
        )['WCID'];
    }
    /**
     * 根据审核次数、任务编号、文档ID和试题ID查询试题审核意见
     * @param int $checkTimes 审核次数
     * @param int $workID 任务ID
     * @param int $docID 文档ID
     * @param int $testID 试题ID
     * @return string 审核意见
     * @author demo
     */
    public function getTestCheckErr($checkTimes,$workID,$docID,$testID){
        $wcID=$this->getWCID($workID,$checkTimes);
        //处理审核意见
        $testError = '';
        //wcID为空，这本次标引为第一次标引，无错误信息  2015-6-4
        if(!$wcID){
            return $testError;
        }
        //获取试题审核及原因
        $buffer=$this->dbConn->teacherTestError($wcID,$checkTimes,$docID,$testID);
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                $subject = '';
                switch($iBuffer['Style']){
                    case 'knowledge':{
                        $subject = '知识点';
                    }
                    break;
                    case 'test':{
                        $subject = '题文';
                    }
                    break;
                    case 'special':{
                        $subject = '专题';
                    }
                    break;
                    case 'diff':{
                        $subject = '难度值';
                    }
                    break;
                    case 'chapter':{
                        $subject = '章节';
                    }
                    break;
                }
                $testError .= $this->getErrorHtml($subject,$iBuffer);
            }
        }
        return $testError;
    }
    /**
     * 错误信息
     * @param string $subject 主题
     * @param array $data 内容
     * @return string 审核意见
     */
    private function getErrorHtml($subject,$data){
        $error = '';
        if($data['Content'] != ''){
            $error = '【'.$subject.'初审意见】：<font color="red">'.$data['Content'].'</font><br>';
        }
        if($data['Suggestion'] != ''){
            $error .= '【'.$subject.'终审意见】：<font color="red">'.$data['Suggestion'].'</font><br>';
        }
        if($error != '')
            return $error.'<hr style="color:#ffc;">';
        return $error;
    }
}
