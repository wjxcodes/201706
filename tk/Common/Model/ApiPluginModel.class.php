<?php
/**
 * @author demo
 * @date 2015年10月19日
 */
/**
 * 远程接口类，用于处理远程模式下普通类输出相关操作
 */
namespace Common\Model;
class ApiPluginModel{
    protected $modelName='Model';
    protected $layerName='';

    /**
     * 初始化模型为 客户端存储D方法类数据
     * @author demo
     */
    function __construct($name='',$layer='') {
        if($name){
            $this->modelName=$name;
        }
        $this->layerName=C('DEFAULT_M_LAYER');
        if($layer){
            $this->layerName=$layer;
        }
    }

    /**
     * 函数说明
     * @param $name
     * @param $arguments
     * @return array
     * @author demo
     */
    public function __call($name, $arguments){
        $param=array();
        $param['modelName']=$this->modelName;
        $param['functionName']=$name;
        $param['layerName']=$this->layerName;
        $param['param']=$arguments;
        return $this->getMethodResult($param);
    }

    /**
     * 为远程调用存贮对应模型名称
     * @param string $modelName 模型名称
     * @param string $layerName 所在层
     * @author demo
     */
    public function setName($modelName, $layerName=''){
        $this->modelName=$modelName;
        $this->layerName=$layerName;
    }
    /**
     * 远程获取模型执行结果；
     * @param array $param 1参数数组
     *                  className 类名
     *                  functionName 函数名
     *                  layerName 所在层名
     *                  param 参数数组
     * @return array
     * @author demo
     */
    public function getMethodResult($param){
        $mName=$param['modelName'];
        $fName=$param['functionName'];
        $lName=$param['layerName'];
        $pName=$param['param'];
        if(file_exists(MODE_PATH.$param['modelName'].'Model.class.php')){
            $model=D($mName,$lName);
            if(method_exists($model,$fName)){
                return $model->$fName($pName[0],$pName[1],$pName[2],$pName[3],$pName[4]);
            }
        }

        $postUrl=C('WLN_CURRENT_MODEL_URL').MODULE_NAME.'-Plugin-getApi';
        $postData=array();
        $postData['WLN_style']='model';
        $postData['WLN_modelName']=$mName;
        $postData['WLN_functionName']=$fName;
        $postData['WLN_layerName']=$lName;
        $postData['WLN_param']=serialize($pName);
        $result=CURL($postData,$postUrl);
        $output=$this->returnResult($result); //返回数据
        return $output['result'];
    }

    /**
     * 远程获取控制器执行结果；
     * @param array $data 1参数数组
     *                  functionName 方法名
     *                  arguments 参数数组
     * @return array
     * @author demo
     */
    public function getActionResult($data){
        $arguments=$data['arguments'];
        $arguments['post']=serialize($_POST);
        $arguments['get']=serialize($_GET);
        $arguments['baseData']=$this->outParam('',1); //基础变量同步 cookie和类变量

        $param=array();
        $param['modelName']=$data['modelName']; //模型名
        $param['functionName']=$data['functionName']; //方法名
        $param['param']=$arguments; //传递参数


        $postData=array();
        $postData['WLN_style']='base';
        $postData['modelName']=$param['modelName'];
        $postData['WLN_functionName']=$param['functionName'];
        $postData['WLN_param']=serialize($param['param']);
        $postUrl=C('WLN_CURRENT_MODEL_URL').MODULE_NAME.'-Plugin-getApi';

        $result=CURL($postData,$postUrl);
        $output = $this->returnResult($result); //返回数据

        $this->setParam($output);
        return $output['result'];
    }

    /**
     * 远程获取夸控制器执行结果；
     * @param array $param 1参数数组
     *                  className 类名
     *                  functionName 函数名
     *                  layerName 所在层名
     *                  param 参数数组
     * @return array
     * @author demo
     */
    public function getPublicResult($param){
        $fName=$param['functionName'];
        $lName=$param['layerName'];
        $pName=$param['param'];
        $str=R($fName,array($pName[0],$pName[1],$pName[2],$pName[3]),$lName);
        if($str===false){
            $postUrl=C('WLN_CURRENT_MODEL_URL').MODULE_NAME.'-Plugin-getApi';
            $postData=array();
            $postData['WLN_style']='public';
            $postData['WLN_functionName']=$fName;
            $postData['WLN_layerName']=$lName;
            $postData['WLN_param']=serialize($pName);
            $result=CURL($postData,$postUrl);
            return $this->returnResult($result); //返回数据
        }
    }
    /**
     * 远程获取配置参数结果；
     * @param array $param 1参数数组
     *                  configName 配置项名
     *                  value 配置项值
     * @return array
     * @author demo
     */
    public function getConfigResult($param){
        $configName=$param['configName'];
        $value=$param['value'];
        $myConfig=C($configName);
        if($myConfig && $value===''){
            return array('result'=>$myConfig);
        }

        $postUrl=C('WLN_CURRENT_MODEL_URL').MODULE_NAME.'-Plugin-getApi';
        $postData=array();
        $postData['WLN_style']='config';
        $postData['WLN_configName']=$configName;
        $postData['WLN_value']=$value;
        $result=CURL($postData,$postUrl);
        return $this->returnResult($result); //返回数据
    }

    /**
     * 远程获取缓存参数结果；
     * @param array $param 1参数数组
     *                  cacheName 配置项名
     *                  value 配置项值
     * @return array
     * @author demo
     */
    public function getCacheResult($param){
        $cacheName=$param['cacheName'];
        $value=$param['value'];

        $postUrl=C('WLN_CURRENT_MODEL_URL').MODULE_NAME.'-Plugin-getApi';
        $postData=array();
        $postData['WLN_style']='cache';
        $postData['WLN_cacheName']=$cacheName;
        $postData['WLN_value']=$value;
        $result=CURL($postData,$postUrl);
        return $this->returnResult($result); //返回数据
    }

    /**
     * 返回远程处理结果；
     * @param string $result 远程处理结果
     * @return array
     * @author demo
     */
    protected function returnResult($result){
        if($result){
            $tmpResult=unserialize($result); //返回序列化数据
            if(!empty($tmpResult)) return $tmpResult;
            if($result){
                if(!is_null(json_decode($result))){
                    header('Content-Type:application/json; charset=utf-8');
                    exit($result);
                }
                exit($result);
            }
        }
    }

    /**
     * 远程客户端 设置变量到分组action
     * @param array $output 远程返回的数组数据
     * @author demo
     */
    public function setParam($output){
        foreach($output['cookie'] as $i=>$iOutput){
            cookie($i,$iOutput);
        }
    }

    /**
     * 返回数据及分组action变量
     * @param array $data 需要返回的数据或者数组
     * @param array $type 类型 0输出序列化数据 1返回数组数据
     * @return string|array
     * @author demo
     */
    public function outParam($data,$type=0){
        $output['cookie'] = $_COOKIE;
        $output['result'] = $data;
        if($type==0) exit(serialize($output)); //返回序列化数据
        elseif($type==1) return $output; //返回数组数据
    }
}