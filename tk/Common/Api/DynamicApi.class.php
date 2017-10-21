<?php
/**
 * 站内动态接口
 * @author demo 2016-5-19
 */
namespace Common\Api;
class DynamicApi extends CommonApi{

    /**
     * 获取动态类型
     * @return array
     * @author demo
     */
    public function getDynamicType(){
        return $this->getModel('Dynamic')->getDynamicType();
    }
}