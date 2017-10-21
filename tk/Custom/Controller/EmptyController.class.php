<?php
/**
 * 空控制器
 * @author demo 15-12-31
 */
namespace Custom\Controller;
use Common\Controller\DefaultController;
class EmptyController extends DefaultController{
    public function _empty(){
        emptyUrl();
    }
}