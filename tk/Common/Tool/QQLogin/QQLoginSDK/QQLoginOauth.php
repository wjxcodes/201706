<?php
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright © 2013, Tencent Corporation. All rights reserved.
 */
/**
 * QQ互联权限验证核心类
 * @author demo
 */
class QQLoginOauth extends QQLoginTool{

    const VERSION = "2.0";
    const GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize"; //获取authcode URL地址
    const GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token";  //获取accesstoken URL地址
    const GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me";           //获取openid URL地址

    /**
     * Step1:跳转函数 获取Authorization Code；
     * @author demo
     */
    public function qq_login(){
        //获取配置
        $appid    = $this->recorder->readInc("appid");
        $callback = $this->recorder->readInc("callback");
        $scope    = $this->recorder->readInc("scope");

        //-------生成唯一随机串防CSRF攻击
        $state = md5(uniqid(rand(), TRUE));
        $this->recorder->write('state',$state);

        //-------构造请求参数列表
        $keysArr = array(
            "response_type" => "code",
            "client_id"     => $appid,
            "redirect_uri"  => $callback,
            "state"         => $state,
            "scope"         => $scope
        );
        //拼装URL
        $login_url =  $this->combineURL(self::GET_AUTH_CODE_URL, $keysArr);

        header("Location:$login_url");
    }

    /**
     * Step2:回调函数 通过Authorization Code获取Access Token
     * @author demo
     */
    public function qq_callback(){
        $state = $this->recorder->read("state");

        //--------验证state防止CSRF攻击
        if($_GET['state'] != $state){
            return $this->showError("30001");
        }

        //-------请求参数列表
        $keysArr = array(
            "grant_type"    => "authorization_code",
            "client_id"     => $this->recorder->readInc("appid"),
            "redirect_uri"  => urlencode($this->recorder->readInc("callback")),
            "client_secret" => $this->recorder->readInc("appkey"),
            "code"          => $_GET['code']
        );

        //------构造请求access_token的url
        $token_url = $this->combineURL(self::GET_ACCESS_TOKEN_URL, $keysArr);
        $response  = $this->get_contents($token_url);

        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);

            if(isset($msg->error)){
                return $this->showError($msg->error, $msg->error_description);
            }
        }

        $params = array();
        parse_str($response, $params);

        $this->recorder->write("access_token", $params["access_token"]);
        return $params["access_token"];

    }

    /**
     * Step3:获取openid 通过Access Token获取OpenID
     * ##说明
     * OpenID是此网站上或应用中唯一对应用户身份的标识，网站或应用可将此ID进行存储，便于用户下次登录时辨识其身份，或将其与用户在网站上或应用中的原有账号进行绑定。
     * ##
     * @author demo
     */
    public function get_openid(){

        //-------请求参数列表
        $keysArr = array(
            "access_token" => $this->recorder->read("access_token")
        );

        $graph_url = $this->combineURL(self::GET_OPENID_URL, $keysArr);
        $response  = $this->get_contents($graph_url);

        //--------检测错误是否发生
        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos -1);
        }

        $user = json_decode($response);
        if(isset($user->error)){
            return $this->showError($user->error, $user->error_description);
        }

        //------记录openid
        $this->recorder->write("openid", $user->openid);
        return $user->openid;

    }
}
