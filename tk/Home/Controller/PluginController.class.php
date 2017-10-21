<?php
/**
 * @author demo
 * @date 2015年10月14日
 */
/**
 * 插件类
 */
namespace Home\Controller;
class PluginController extends BaseController {
    /**
     * 插件调用
     * @author demo
     */
    public function index() {
        //判断插件是否存在
        $plugName=MODULE_NAME.CONTROLLER_NAME;
        if(!file_exists(BASE_LIB_PATH . 'Widget/' .$plugName.'Widget.class.php')){
            if(C('WLN_CURRENT_MODEL')==0){
                //转入远程获取
                $data['get']=$_GET;
                $data['post']=$_POST;
                $output=WB(ACTION_NAME,$data);
                if($output){
                    if(IS_AJAX){
                        $this->setBack($output);
                    }else{
                        exit($output);
                    }
                }
            }

            emptyUrl(); //不存在的类转向错误页
        }

        C('TAGLIB_BUILD_IN','cx'); //加载模板标签

        //设置模板路径
        $group  =  defined('MODULE_NAME')?MODULE_NAME.'/':'';
        $theme  =   C('DEFAULT_THEME');
        // 获取当前主题的模版路径
        if(1==C('APP_GROUP_MODE')){ // 独立分组模式
            define('THEME_PATH',   dirname(BASE_LIB_PATH).'/'.$group.basename(TMPL_PATH).'/'.$theme);
            define('APP_TMPL_PATH',__ROOT__.'/'.APP_NAME.(APP_NAME?'/':'').C('APP_GROUP_PATH').'/'.$group.basename(TMPL_PATH).'/'.$theme);
        }else{
            define('THEME_PATH',   TMPL_PATH.$group.$theme);
            define('APP_TMPL_PATH',__ROOT__.'/'.APP_NAME.(APP_NAME?'/':'').basename(TMPL_PATH).'/'.$group.$theme);
        }

        //传递参数
        $data=$this->get();
        $data['function']=ACTION_NAME;

        //获取内容
        $result=W($plugName,$data,true);

        //过滤数据引入模板常量
        tag('view_filter',$result);

        //输出解析后的模板内容
        echo $result;

        //加载调试数据
        tag('view_end');
        exit();
    }

    /**
     * 远程调用接口返回数据
     * @author demo
     */
    public function getApi(){
        if(C('WLN_CURRENT_MODEL')!=1){
            exit('not server!');
        }

        $type=array('common','base','model','public','config','cache');
        $wlnStyle=$_POST['WLN_style'];

        if(!in_array($wlnStyle,$type)){
            exit('not access!');
        }
        $result=''; //返回数据结果
        $param=unserialize(stripslashes_deep($_POST['WLN_param']));
        $fName=$_POST['WLN_functionName'];
        $mName=$_POST['WLN_modelName'];

        $baseData=$param['baseData']; //基础变量同步 cookie和类变量
        unset($param['baseData']);

        $apiPlugin=$this->getModel('ApiPlugin');
        $apiPlugin->setParam($baseData);

        switch($wlnStyle){
            case 'common':
                $result=$fName($param[0],$param[1],$param[2],$param[3]);
                break;
            case 'base':
                $_POST=unserialize(stripslashes_deep($param['post']));
                unset($param['post']);
                $_GET=unserialize(stripslashes_deep($param['get']));
                unset($param['get']);

                if($fName=='getData') $mName='Index'; //兼容错误模型调用getdata

                $result=R(MODULE_NAME.'/'.$mName.'/'.$fName,$param);
                break;
            case 'model':
                $result = $this->getModel($mName)->$fName($param[0],$param[1],$param[2],$param[3]);
                break;
            case 'public':
                if(!strstr($fName,'Public/')){
                    exit('no access!');
                }
                $result=R($fName,array($param[0],$param[1],$param[2],$param[3]),$_POST['WLN_layerName']);
                break;
            case 'config':
                if(!strstr($_POST['WLN_configName'],'WLN_')){
                    exit('no access!');
                }
                $result=C($_POST['WLN_configName']);
                break;
            case 'cache':
                $result=SS($_POST['WLN_cacheName']);
                break;
        }

        $apiPlugin->outParam($result); //处理返回结果

    }
}