<?php

namespace Dir\Api;
use Common\Api\CommonApi;
/**
 * 选题方式接口
 *
 * @author demo
 * @date 2016-4-11
 */
class ChooseApi extends CommonApi {

    /**
     * 获取选题方式
     * @return type
     */
    public function getChoose() {
        return array(array('val' => '1', 'chooseAttrName' => '知识点出题'),
            array('val' => '2', 'chooseAttrName' => '教材章节出题'));
    }

}
