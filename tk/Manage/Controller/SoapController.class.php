<?php
/**
 * 开发环境下的soap调用
 * @author demo 16-5-18
 */
namespace Manage\Controller;
class SoapController extends \Common\Controller\CommonController{

    /**
     * soap调用
     * @author demo 16-5-18
    */
    public function invoke(){
        $server = new \SoapServer(null, array(
            'uri' => \Common\Model\BaseModel::SOAP_URI
        ));
        //保存相关模块的配置信息
        $module = $_GET['mod'];
        if($module != MODULE_NAME){
            C(load_config(APP_PATH.$module.'/Conf/config'.CONF_EXT));
        }
        $server->setObject($this->getModel($_GET['ns'], '', true));
        $server->handle();
    }
}