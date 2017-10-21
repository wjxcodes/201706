<?php
/**
 * 短信验证码类
 * @author demo
 * @date 2015年3月6日
 */
class SmsModel {

    //主帐号,对应开官网发者主账号下的 ACCOUNT SID
    private $accountSid= 'aaf98f894efcded4014f06c78a010eeb';

    //主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
    private $accountToken= '7099ea6eaac8482d8ec1d61acab31dc4';

    //应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
    //在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
    private $appId='8a48b5514efd1c3a014f06ca2ff20ead';

    //请求地址
    //沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
    //生产环境（用户应用上线使用）：app.cloopen.com
    private $serverIP='app.cloopen.com';
    //private $serverIP='app.cloopen.com';

    //请求端口，生产环境和沙盒环境一致
    private $serverPort='8883';

    //REST版本号，在官网文档REST介绍中获得。
    private $softVersion='2013-12-26';


    /**
     * 发送模板短信
     * @param int $to 手机号码集合,用英文逗号分开
     * @param array $datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
     * @param int $tempId 模板Id,测试应用和未上线应用使用测试模板请填写1，正式应用上线后填写已申请审核通过的模板ID
     * @return array array(0,msg)成功 array(1,msg)出错
     */
    public function sendTemplateSMS($to,$datas,$tempId)
    {
        if(!class_exists('REST')){
            import('Common.Tool.Sms.CCPRestSmsSDK');
        }
        // 初始化REST SDK
        $rest = new \REST($this->serverIP,$this->serverPort,$this->softVersion);
        $rest->setAccount($this->accountSid,$this->accountToken);
        $rest->setAppId($this->appId);

        // 发送模板短信
        $msg = "模板 : $tempId<br/>内容：".implode(',',$datas).'<br/>';
        $result = $rest->sendTemplateSMS($to,$datas,$tempId);
        if($result == NULL ) {
            $msg .= "result error!";
            return array(1,$msg);
        }
        if($result->statusCode!=0) {
            $msg .= "error code :" . $result->statusCode . "<br>";
            $msg .= "error msg :" . $result->statusMsg . "<br>";
            return array(1,$msg);
        }else{
            $msg .="Sendind TemplateSMS success!<br/>";
            // 获取返回信息
            $smsmessage = $result->TemplateSMS;
            $msg .="dateCreated:".$smsmessage->dateCreated."<br/>";
            $msg .="smsMessageSid:".$smsmessage->smsMessageSid."<br/>";
            return array(0,$msg);
        }
    }
}
?>