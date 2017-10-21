<?php
/**
 * @author demo
 * @date 2014年11月12日
 * @update 2015年1月23日
 */
/**
 * 专题模型类，用于处理专题相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class SpecialModel extends BaseModel{

    /**
     * 缓存数组
     * @author demo
     */
    public function setCache(){
        $specialArr=array();//专题
        $specialSon=array();//专题子类
        $specialTree=array();//按学科，专题树状图
        $specialParent=array();//顶级分类数据集
        $buffer = $this->selectData(
            '*',
            '1=1',
            'OrderID asc,SpecialID asc');
        if($buffer){
            // 获取专题子类
            foreach($buffer as $iBuffer){
                if($iBuffer['PID']!=0){
                    $specialSon[$iBuffer['PID']][$iBuffer['SpecialID']]=$iBuffer;
                }else{
                    $specialParent[$iBuffer['SubjectID']][]=$iBuffer;
                }
            }
            foreach($buffer as $i=>$iBuffer){
                if($iBuffer['PID']=='0'){
                    if($specialSon[$iBuffer['SpecialID']]){
                        $iBuffer['sub']=$specialSon[$iBuffer['SpecialID']];
                    }
                    $specialTree[$iBuffer['SubjectID']][]=$iBuffer;
                }
                $specialArr[$iBuffer['SpecialID']]['SpecialName']=$iBuffer['SpecialName'];
                $specialArr[$iBuffer['SpecialID']]['PID']=$iBuffer['PID'];
                $specialArr[$iBuffer['SpecialID']]['SubjectID']=$iBuffer['SubjectID'];
            }
        }
        S('special',$specialArr);//ID作键的专题
        S('specialParent',$specialParent);//专题父级一级分类
        S('specialTree',$specialTree);//专题树状图，学科ID为索引
    }
    /**
     * 获取缓存
     * @param $str string 缓存名称
     * @return array
     * @author demo
     */
    public function getCache($str='special',$num=0){
        switch ($str) {
            case 'special':
                $buffer=S('special');
                break;
            case 'specialParent':
                $buffer=S('specialParent');
                break;
            case 'specialTree':
                $buffer=S('specialTree');
                break;    
        }
        if(empty($buffer) && $num==0){
            $this->setCache();
            $buffer=$this->getCache($str,1);
        }
        return $buffer;
    }
}