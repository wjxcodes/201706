<?php
/**
 * 空模型
 */
namespace Work\Controller;
use Think\Controller;
class EmptyController extends Controller {
    public function _empty() {
        emptyUrl();
    }
}