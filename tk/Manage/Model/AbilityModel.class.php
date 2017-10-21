<?php
/**
 * @author demo 
 * @date 2014年8月11日
 * @update 2014年12月26日
 */
/**
 *能力管理Model类，用于处理能力管理相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class AbilityModel extends BaseModel{
    /**
     * 更新缓存；
     * @return array
     * @author demo 
     */
    public function setCache(){
        $ability=array();
        $abilitySubject=array();
        $buffer = $this->selectData(
            '*',
            '1=1',
            'SubjectID asc,OrderID asc,AbID asc');
        if($buffer)
            foreach($buffer as $i=>$iBuffer){
                $ability[$iBuffer['AbID']]['AbilitName']=$iBuffer['AbilitName'];
                $ability[$iBuffer['AbID']]['SubjectID']=$iBuffer['SubjectID'];
                $ability[$iBuffer['AbID']]['OrderID']=$iBuffer['OrderID'];
                $abilitySubject[$iBuffer['SubjectID']][]=$iBuffer;
            }
        //题型按照分卷调整顺序
        S('ability',$ability);//所有能力属性缓存
        S('abilitySubject',$abilitySubject);//根据学科分组能力
    }
    /**
     * 获取缓存；
     * @param string $str 缓存名称
     * @param int $num 防止死循环
     * @return array
     * @author demo,
     */
    public function getCache($str='ability',$num=0){
        switch($str){
            case 'ability':
                $buffer=S('ability');
                break;
            case 'abilitySubject':
                $buffer=S('abilitySubject');
                break;
            default :
                return false;
                break;
        }
        if(empty($buffer) and $num==0){
            $this->setCache();
            $buffer=$this->getCache($str,1);
        }
        return $buffer;
    }
}