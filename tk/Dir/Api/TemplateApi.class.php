<?php

/**
 * 模板类别接口
 *
 * @author demo
 * @date 2016-4-11
 */
namespace Dir\Api;
use Common\Api\CommonApi;
class TemplateApi extends CommonApi {

    /**
     * 获取模板类别
     * @return type
     */
    public function getTemplate() {
        return array(array('val' => '3', 'styleName' => '系统模板'),
            array('val' => '4', 'styleName' => '我的模板'),
            array('val' => '2', 'styleName' => '自定义模板'));
    }

}
