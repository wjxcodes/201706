<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

/* folder save */
define('BUILD_DIR_SECURE',true);
define('DIR_SECURE_FILENAME', 'index.html');
define('DIR_SECURE_CONTENT', 'deney Access!');

// define('BIND_MODULE','Tstatistics');//注意，A要大写，生成之后要删除这一行

// 定义应用目录
define('APP_PATH','../tk/');
// 引入ThinkPHP入口文件
function exceptionHandler(){
    ini_set('ignore_repeated_errors',1);    //不重复记录出现在同一个文件中的同一行代码上的错误信息。

    $user_defined_err = error_get_last();
    if($user_defined_err['type'] > 0)
    {
        switch($user_defined_err['type']){
            case 1:
                $user_defined_errType = '致命的运行时错误(E_ERROR)';
                break;
            case 2:
                $user_defined_errType = '非致命的运行时错误(E_WARNING)';
                break;
            case 4:
                $user_defined_errType = '编译时语法解析错误(E_PARSE)';
                break;
            case 8:
                $user_defined_errType = '运行时提示(E_NOTICE)';
                break;
            case 16:
                $user_defined_errType = 'PHP内部错误(E_CORE_ERROR)';
                break;
            case 32:
                $user_defined_errType = 'PHP内部警告(E_CORE_WARNING)';
                break;
            case 64:
                $user_defined_errType = 'Zend脚本引擎内部错误(E_COMPILE_ERROR)';
                break;
            case 128:
                $user_defined_errType = 'Zend脚本引擎内部警告(E_COMPILE_WARNING)';
                break;
            case 256:
                $user_defined_errType = '用户自定义错误(E_USER_ERROR)';
                break;
            case 512:
                $user_defined_errType = '用户自定义警告(E_USER_WARNING)';
                break;
            case 1024:
                $user_defined_errType = '用户自定义提示(E_USER_NOTICE)';
                break;
            case 2048:
                $user_defined_errType = '代码提示(E_STRICT)';
                break;
            case 4096:
                $user_defined_errType = '可以捕获的致命错误(E_RECOVERABLE_ERROR)';
                break;
            case 8191:
                $user_defined_errType = '所有错误警告(E_ALL)';
                break;
            default:
                $user_defined_errType = '未知类型';
                break;
        }
        $msg = sprintf("%s %s %s %s %s \r\n",date("Y-m-d H:i:s"),$user_defined_errType,$user_defined_err['message'],$user_defined_err['file'],$user_defined_err['line']);

        //分组存贮
        error_log($msg,3,dirname(__FILE__) . '/' .MODULE_NAME.'-error-logs.txt');
    }
}
register_shutdown_function('exceptionHandler');

$globalTime=microtime(true);
require '../main/main.php';
//分组存贮
$url=__SELF__;
$str='执行时间：'.(microtime(true)-$globalTime) .' = URL：'.$url.' 当前时间:'.date('Y-m-d H:i:s')."\r\n";
file_put_contents(MODULE_NAME.'-time-logs.txt',$str,FILE_APPEND);

// 亲^_^ 后面不需要任何代码了 就是如此简单