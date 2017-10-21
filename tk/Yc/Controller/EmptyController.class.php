<?php
namespace Yc\Controller;
use Index\Controller\BaseController;
class EmptyController extends BaseController{
    public function index(){
        emptyUrl();
    }

    public function _empty(){
        emptyUrl();
    }
}