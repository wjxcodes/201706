<?php
/**
 * 空模型
 */
namespace Index\Controller;
use Think\Controller;
class EmptyController extends Controller {
    public function _empty() {
        emptyUrl();
    }
}