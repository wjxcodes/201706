<?php
/**
 * @author demo
 * @date 2015年3月31日
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class ProcessErrorPregModel extends BaseModel{

    /**
     * 生成pregError缓存
     * @return bool
     * @author demo
     */
    public function setCache(){
        $dbData = $this->selectData('*','1=1');
        if(!$dbData){
            return false;
        }
        $cache = [];
        foreach ($dbData as $iDbData) {
            $cache[$iDbData['PregID']] = $iDbData;
        }
        return S('pregError',$cache);
    }

    /**
     * 获取pregError缓存
     * @return array
     * @author demo
     */
    public function getCache(){
        $cache = S('pregError');
        if(!$cache){
            $this->setCache();
            $cache = S('pregError');
        }
        return $cache;
    }
}