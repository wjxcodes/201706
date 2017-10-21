<?php
/**
 * 空模型
 */
namespace Doc\Controller;
use Think\Controller;
class EmptyController extends Controller {
    public function _empty() {
        emptyUrl();
    }
}