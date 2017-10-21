<?php
/**
 * 空模型
 */
namespace Dir\Controller;
use Think\Controller;
class EmptyController extends Controller {
    public function index() {
        emptyUrl();
    }
  
    public function _empty(){
        emptyUrl();
    }
}
