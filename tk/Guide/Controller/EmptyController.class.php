<?php
/**
 * 空模型
 */
namespace Guide\Controller;
use Think\Controller;
class EmptyController extends Controller {
    public function _empty() {
        emptyUrl();
    }
}