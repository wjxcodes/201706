<?php
/**
 * @author demo
 * @date 2014年10月11日
 */
/**
 * 空模型
 */
namespace Aat\Controller;
use Think\Controller;
class EmptyController extends Controller {
    public function _empty() {
        emptyUrl();
    }
}