<?php
/**
 * @author demo
 * @date 2014年12月29日
 */
/**
 * 文档来源管理模型类，用于文档来源管理相关操作
 */
namespace Doc\Model;
class DocSourceModel extends BaseModel{

    /**
     * 设置缓存
     * @author demo
     */
    public function setCache(){
        $docSource=array();
        $sourceResult = $this->selectData(
            '*',
            '1=1',
            'SourceID desc');

        foreach($sourceResult as $i=>$iSourceResult){
            $docSource[$sourceResult[$i]['SourceID']]=$sourceResult[$i];
        }
        S('docSource',$docSource);//以来源ID为键，文档来源缓存
    }
    /**
     * 获取缓存数据
     * @author demo
     */
    public function getCache($str='docSource',$num=0){
        switch($str){
            case 'docSource':
                $buffer=S('docSource');
                break;
            default:
                return false;
        }
        if(empty($buffer) && $num==0){
            $this->setCache();
            $buffer=$this->getCache($str,1);
        }
        return $buffer;
    }
}
?>