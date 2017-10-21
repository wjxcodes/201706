<?php
/**
 * @author demo
 * @date 2014年11月3日
 */
/**
 * 空模型控制器类，用于处理空模型操作
 */
namespace Home\Controller;
use Think\Controller;
class EmptyController extends Controller {
    public function _empty() {
        emptyUrl();
    }
}
