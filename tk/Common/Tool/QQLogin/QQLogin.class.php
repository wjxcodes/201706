<?php
/**
 * QQ登录调用接口
 * 包含权限验证和调用API
 * @author demo
 */

class QQLogin {

    /**
     * Alipay constructor.
     * 加载QQLogin SDK
     */
    public function __construct(){
        //加载SDK文件
        defined('QQLOGIN_TOOL_PATH') or define('QQLOGIN_TOOL_PATH',dirname(__FILE__));
        require_cache(QQLOGIN_TOOL_PATH.DIRECTORY_SEPARATOR.'QQLoginSDK'.DIRECTORY_SEPARATOR.'QQLoginLoad.php');
    }


    /**
     * 调用权限和QQAPI方法
     * @param string $method 方法名称
     * ##API可调用方法:(可以在此方法再做对应原API方法的map)
     * @访问用户资料
     * get_user_info           获取登录用户的昵称、头像、性别
     *
     * @获取用户QQ会员信息 需要申请
     * get_vip_info            获取QQ会员的基本信息
     * get_vip_rich_info       获取QQ会员的高级信息
     *
     * @访问我的空间相册 需要申请
     * list_album              获取用户QQ空间相册列表
     * upload_pic              上传一张照片到QQ空间相册
     * add_album               在用户的空间相册里，创建一个新的个人相册
     * list_photo              获取用户QQ空间相册中的照片列表
     *
     * @访问我的腾讯微博资料
     * get_info                获取登录用户在腾讯微博详细资料
     *
     * @分享内容到我的腾讯微博
     * add_t                   发表一条微博
     * del_t                   删除一条微博
     * add_pic_t               发表一条带图片的微博
     * get_repost_list         获取单条微博的转发或点评列表
     *
     * @获得我的微博好友信息
     * get_other_info          获取他人微博资料
     * get_fanslist            我的微博粉丝列表
     * get_idollist            我的微博偶像列表
     * add_idol                收听某个微博用户
     * del_idol                取消收听某个微博用户
     *
     * @访问我的财付通信息 需要申请
     * get_tenpay_addr         在这个网站上将展现您财付通登记的收货地址
     * ##
     * @param array  $args 参数数组
     * @return mixed
     * @author demo
     */
    public function __call($method,$args){
        if(in_array($method,['QQLogin','QQCallback'])){//权限处理
            $auth = new QQLoginOauth();
            switch($method) {
                case 'QQLogin':
                    $auth->qq_login();
                    break;
                case 'QQCallback':
                    $result=$auth->qq_callback();
                    if(is_array($result) && is_numeric($result[0])){
                        return $result;
                    }
                    return $auth->get_openid();//此处需要返回openID
                    break;
                default:
                    exit();
            }
        }else{//调用QQ提供的API
            $api = new QQLoginConnector();
            if(is_callable(array($api,$method))) {
                switch (count($args)) {
                    case 0:
                        return $api->$method();

                    case 1:
                        return $api->$method($args[0]);

                    case 2:
                        return $api->$method($args[0], $args[1]);

                    case 3:
                        return $api->$method($args[0], $args[1], $args[2]);

                    case 4:
                        return $api->$method($args[0], $args[1], $args[2], $args[3]);

                    default:
                        return call_user_func_array([$api, $method], $args);
                }
            }else{
                exit();
            }
        }
    }
}