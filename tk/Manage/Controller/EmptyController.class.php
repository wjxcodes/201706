<?php
/**
 * 空模型
 */
namespace Manage\Controller;
use Think\Action;
class EmptyController extends Action {
    public function index() {
        emptyUrl();
    }
}