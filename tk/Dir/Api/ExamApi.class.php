<?php

/**
 * 考试类别接口
 * @author demo
 * @date 2016-4-8
 */
namespace Dir\Api;
use Common\Api\CommonApi;
class ExamApi extends CommonApi {

    /**
     * 返回考试类别
     * @return type
     */
    public function examType() {
        return SS('examType');
    }

}
