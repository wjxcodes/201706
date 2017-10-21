<?php
/**
 * 计数接口
 * @author demo 2015-1-5
 */
namespace Common\Api;
class CounterApi extends CommonApi{
    /**
     * 检测数据库是否正常
     * @author demo
     */
    public function checkDBConnect(){
        if($this->getModel('Counter')->selectCount(
            '1=1',
            '*'
        )){
            return true;
        }
        return false;
    }
}