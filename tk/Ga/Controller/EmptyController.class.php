<?php
/**
 * 空模型
 */
namespace Ga\Controller;
use Think\Controller;
class EmptyController extends Controller {
    public function _empty(){
        emptyUrl();
    }
}