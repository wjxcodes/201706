<?php
namespace Task\Controller;
use Think\Controller;
class EmptyController extends Controller{
    public function index(){
        emptyUrl();
    }

    public function _empty(){
        emptyUrl();
    }
}