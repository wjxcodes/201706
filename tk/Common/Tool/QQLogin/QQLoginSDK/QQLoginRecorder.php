<?php
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright © 2013, Tencent Corporation. All rights reserved.
 */
/**
 * QQ文件配置类
 */
class QQLoginRecorder{
    private static $data;
    private $inc;

    public function __construct(){
        //载入配置,更改默认配置文件载入方法,适应TP
        $this->inc = C('WLN_QQ_LOGIN_CONFIG');
        if(empty($this->inc)){
            echo "<meta charset=\"UTF-8\">";
            echo "未找到配置文件,请联系管理员!";
            exit();
        }

        if(empty($_SESSION['QC_userData'])){
            self::$data = array();
        }else{
            self::$data = $_SESSION['QC_userData'];
        }
    }

    public function write($name,$value){
        self::$data[$name] = $value;
    }

    public function read($name){
        if(empty(self::$data[$name])){
            return null;
        }else{
            return self::$data[$name];
        }
    }

    public function readInc($name){
        if(empty($this->inc[$name])){
            return null;
        }else{
            return $this->inc[$name];
        }
    }

    public function delete($name){
        unset(self::$data[$name]);
    }

    public function __destruct(){
        $_SESSION['QC_userData'] = self::$data;
    }
}
