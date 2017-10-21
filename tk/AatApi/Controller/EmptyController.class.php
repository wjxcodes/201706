<?php
/**
 * @author demo
 * @date 2014年10月11日
 */
/**
 * 空模型
 */
namespace AatApi\Controller;
use Think\Action;
class EmptyController extends Action {
    public function _empty() {
        emptyUrl();
    }
}