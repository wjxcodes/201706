<?php
/**
 * 缓存
 * @author demo 16-5-20
 */
namespace Guide\Api;
use Common\Api\CommonApi;
class CaseMenuApi extends CommonApi{
    
    public function caseMenu(){
        return SS('caseMenu');
    }
}