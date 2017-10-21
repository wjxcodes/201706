<?php
/**
 * QQ互联工具类
 * 包括错误提示和URL处理
 * @author demo
 */
class QQLoginTool{

    protected $recorder;
    /**
     * 构造函数
     */
    public function __construct(){
        $this->recorder = new QQLoginRecorder();
    }

    /**
     * combineURL
     * 拼接url
     * @param string $baseURL   基于的url
     * @param array  $keysArr   参数列表数组
     * @return string           返回拼接的url
     */
    public function combineURL($baseURL,$keysArr){
        $combined = $baseURL."?";
        $valueArr = array();

        foreach($keysArr as $key => $val){
            $valueArr[] = "$key=$val";
        }

        $keyStr = implode("&",$valueArr);
        $combined .= ($keyStr);

        return $combined;
    }

    /**
     * get_contents
     * 服务器通过get请求获得内容
     * @param string $url       请求的url,拼接后的
     * @return string           请求返回的内容
     */
    public function get_contents($url){
        //统一使用curl 打开allow_url_fopen会有一定风险
        //if (ini_get("allow_url_fopen") == "1") {
        //    $response = file_get_contents($url);
        //}else{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $response =  curl_exec($ch);
            curl_close($ch);
        //}

        //-------请求为空
        if(empty($response)){
            //此处因为服务器网络问题也会为空,如:防火墙未开相应端口,造成curl超时
            echo '<meta charset="UTF-8">网络异常,请重试!如果再次出现,请联系管理员!<script
>setTimeout("window.location.href=\'' .C('WLN_HTTP') . '\'",2000);</script>';
            exit();
            //$this->showError("50001");
        }

        return $response;
    }

    /**
     * get
     * get方式请求资源
     * @param string $url     基于的baseUrl
     * @param array $keysArr  参数列表数组
     * @return string         返回的资源内容
     */
    public function get($url, $keysArr){
        $combined = $this->combineURL($url, $keysArr);
        return $this->get_contents($combined);
    }

    /**
     * post
     * post方式请求资源
     * @param string $url       基于的baseUrl
     * @param array $keysArr    请求的参数列表
     * @param int $flag         标志位
     * @return string           返回的资源内容
     */
    public function post($url, $keysArr, $flag = 0){

        $ch = curl_init();
        if(! $flag) curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $keysArr);
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);

        curl_close($ch);
        return $ret;
    }

    /**
     * showError
     * 显示错误信息
     * @param int $code 错误代码
     * @param string $description 描述信息（可选）
     */
    public function showError($code, $description = ''){
        $showErr = C('WLN_QQ_LOGIN_CONFIG.errorReport');//是否显示错误信息
        if(!$showErr){
            die();//静默模式
        }
        $errorMsg = array(
            "30001" => "<h2>请不要重复刷新页面。<br/>The state does not match.<br/> You may be a victim of CSRF.</h2>",
            "50001" => "<h2>可能是服务器无法请求https协议</h2>可能未开启curl支持,请尝试开启curl支持，<br/>重启web服务器，如果问题仍未解决，请联系我们"
        );

        if(empty($description)){
            return array($code,$errorMsg[$code]);
        }else{
            return array('30704',$code.$description); //自定义错误信息
        }
    }
}
