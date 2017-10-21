<?php
/**
 * @author demo
 * @date 2014年8月25日
 */
/**
 * 考试类型模型类，用于处理考试类型相关操作
 */
namespace Dir\Model;
use Common\Model\BaseModel;
class DirExamTypeModel extends BaseModel{

    /**
     * 缓存数组
     * @return int 考试类型数量 
     * @author demo
     */
    public function setCache(){
        $examTypeArr = array();
        $buffer = $this->getModel('DirExamType')->selectData(
            '*',
            '1=1',
            'OrderID ASC,TypeID ASC'
        );
        if($buffer){
            foreach($buffer as $iBuffer){
                $examTypeArr[$iBuffer['TypeID']]['TypeID'] = $iBuffer['TypeID'];
                $examTypeArr[$iBuffer['TypeID']]['TypeName'] = $iBuffer['TypeName'];
                $examTypeArr[$iBuffer['TypeID']]['DefaultStyle'] = $iBuffer['DefaultStyle'];
                $examTypeArr[$iBuffer['TypeID']]['OrderID'] = $iBuffer['OrderID'];
            }
        }
        S('examType',$examTypeArr);//设置考试类型
    }
    /**
     * 获取缓存
     * @param string 缓存标识
     * @return array 对应的缓存数组
     * @author demo
     */
    public function getCache($str = 'examType',$num = 0){
        if($str == 'examType'){
            $buffer = S('examType');
        }
        if(empty($buffer) && $num == 0){
            $this->setCache();
            $buffer = $this->getCache($str,1);
        }
        return $buffer;
    }
}