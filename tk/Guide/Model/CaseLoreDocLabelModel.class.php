<?php
/**
 * @author demo
 * @date 15-5-4 上午11:28
 */
/**
 * 导学案知识文档标签类，用于导学案知识文档标签的操作
 */
namespace Guide\Model;
use Common\Model\BaseModel;
class CaseLoreDocLabelModel extends BaseModel{

    /**
     * 生成缓存数组
     * @author demo
     */
    public function setCache(){
        $menuArray=array();
        $buffer=$this->selectData(
            '*',
            '1=1',
            'OrderID asc'
        );
        foreach($buffer as $i=>$iBuffer){
            $menuArray[$iBuffer['LabelID']]=$iBuffer;
        }
        S('lorelabel',$menuArray);//ID作键的导学案栏目缓存
    }

    /**
     * 获取缓存array
     * @param $str string 默认标识
     * @param $num int 重复次数
     * @return
     * @author demo
     */
    public function getCache($str='lorelabel',$num=0){
        $buffer=S('lorelabel');
        if(empty($buffer) && $num==0){
            $this->setCache();
            $buffer=$this->getCache($str,1);
        }
        return $buffer;
    }
}