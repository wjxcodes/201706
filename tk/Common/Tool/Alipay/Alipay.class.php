<?php
/**
 * 支付宝调用工具类
 * @author demo
 */
class Alipay {

    /**
     * Alipay constructor.
     * 加载Alipay SDK
     */
      public function __construct(){
          //强制UTF-8编码,防止乱码导致的支付宝请求异常
          header("Content-type:text/html;charset=utf-8");
          //加载SDK文件
          defined('ALIPAY_TOOL_PATH') or define('ALIPAY_TOOL_PATH',dirname(__FILE__));
          require_cache(ALIPAY_TOOL_PATH.DIRECTORY_SEPARATOR.'AlipaySDK'.DIRECTORY_SEPARATOR.'AlipayLoad.php');
      }

    /**
     * 执行支付操作
     * @param $param array 支付相关参数
     * ##参数说明:
     * $param结构固定如:
     * $param = [
            'orderNum'     => $orderID,//本站订单号
            'orderName'   => $orderName,//订单名称
            'orderDetail' => $orderDetail,//订单详细
            'totalFee'    => $totalFee,//总费用
            'showUrl'     => '',//展示地址,如果为空,则为首页
       ]
     * ##
     * @author demo
     */
    public function doPay($param){
        extract($param);//获取参数变量

        //商品展示地址
        if(!isset($showUrl) || empty($showUrl)){
            $showUrl   = C('WLN_HTTP');
        }
        $alipayConfig  = C('ALIPAY_CONFIG');
        $sellerEmail   = $alipayConfig['seller_email'];//卖家支付宝帐户必填
        //请求的数据准备
        $paymentType   = "1"; //支付类型 //必填，不能修改
        $notifyUrl     = C('WLN_ALI_NOTIFY_URL'); //服务器异步通知页面路径
        $returnUrl     = C('WLN_ALI_RETURN_URL'); //页面跳转同步通知页面路径
        $anti_phishing_key = "";//防钓鱼时间戳 //若要使用请调用类文件submit中的query_timestamp函数
        $exter_invoke_ip   = get_client_ip(0,true); //客户端的IP地址

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service"            => "create_direct_pay_by_user",
            "partner"            => trim($alipayConfig['partner']),
            "payment_type"       => $paymentType,
            "notify_url"         => $notifyUrl,
            "return_url"         => $returnUrl,
            "seller_email"       => $sellerEmail,
            "out_trade_no"       => $orderNum,
            "subject"            => $orderName,
            "total_fee"          => $totalFee,
            "body"               => $orderDetail,
            "show_url"           => $showUrl,
            "anti_phishing_key"  => $anti_phishing_key,
            "exter_invoke_ip"    => $exter_invoke_ip,
            "_input_charset"     => trim(strtolower($alipayConfig['input_charset']))
        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($alipayConfig);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"post", "确认");
        echo $html_text;
    }

    /**
     * 支付宝服务器通知,本站属于即时支付,暂未使用
     * @author demo
     */
    public function getNotify(){
        $alipay_config=C('ALIPAY_CONFIG');
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result) {
            //验证成功
            if($_POST['trade_status'] == 'TRADE_FINISHED') {
                //
            }else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //
            }
            echo "success";        //请不要修改或删除
        }else {
            //验证失败
            echo "fail";
        }
    }

    /**
     * 支付宝支付完成的跳转页面
     * @notice 如果有不同逻辑实现 可以在PayAction里不再远程调用该方法,而是保持必要的逻辑,复写该方法
     * ##返回参数$_GET示例:
     * Array(
                [/Index-Pay-getReturn] => //请结合最新的URL规则
                [body] => 开通会员,更多特权等你来拿!
                [buyer_email]  => 123766297@qq.com
                [buyer_id]     => 2088312045877274
                [exterface]    => create_direct_pay_by_user
                [is_success]   => T
                [notify_id]    => RqPnCoPT3K9%2Fvwbh3InVadOVBLFHQ19xK%2FwvIbeDs0c%2BSujNha7Se3sBo3cGKu4qq700
                [notify_time]  => 2015-11-14 13:39:30
                [notify_type]  => trade_status_sync
                [out_trade_no] => 144747955834155045
                [payment_type] => 1
                [seller_email] => tesoon@163.com
                [seller_id]    => 2088901505895358
                [subject]      => 题库云平台会员服务
                [total_fee]    => 0.01
                [trade_no]     => 2015111421001004270078038192
                [trade_status] => TRADE_SUCCESS
                [sign]         => 1cab54ff78bd176c6b4a97323de9c450
                [sign_type]    => MD5
                [_URL_]        => Array
                                (
                                    [0] => Index
                                    [1] => Pay
                                    [2] => getReturn
                                )
      )
     * @author demo
     */
    public function getReturn(){
        $alipay_config=C('ALIPAY_CONFIG');
        $alipayNotify = new AlipayNotify($alipay_config);//计算得出通知验证结果
        $verify_result = $alipayNotify->verifyReturn();
        if($verify_result) {//验证成功
            $param   = array();
            $orderID = $_GET['out_trade_no']; //商户订单号
            //构造成功页面返回数据
            $return = array();
            $return['oid'] = $_GET['out_trade_no'];
            //初始化订单模型
            $orderListObj = D('Manage/OrderList');
            if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                //构建订单参数
                $param['AliTradeNum']    = $_GET['trade_no'];//支付宝交易号
                $param['AliTradeStatus'] = $_GET['trade_status'];//交易状态
                $param['BuyerEmail']     = $_GET['buyer_email'];//买家支付宝帐号
                $param['NotifyID']       = $_GET['notify_id'];//通知校验ID
                $param['NotifyTime']     = $_GET['notify_time'];//通知的发送时间
                $param['ReturnTotal']    = $_GET['total_fee'];//通知的发送时间
                //订单状态
                $param['OrderStatus']    = 1;//订单状态
                //更新订单
                $orderListObj->updateOrder($param,$orderID);
                //查询订单
                $orderInfo = $orderListObj->getOrderInfo('UID,IsYear,PowerID,OrderPrice,BuyNum,TotalFee', ['OrderID'=>$orderID],'',1);
                $orderInfo = $orderInfo[0];
                if($orderInfo['TotalFee']!=$_GET['total_fee']){
                    //此处概率几乎为0
                    //此处可记录日志也可在后台管理中实现作为安全检查
                }
                //时间值分情况处理
                //计算开通时间间隔值,方便统一使用
                $baseTime = 60*60*24*30;//基础时间是一个月的时间
                $baseTime = $baseTime * $orderInfo['BuyNum'];//开通总时长
                if(C('IS_PROMOTION')){//活动中
                    if(time()>=C('PROM_BEGIN_TIME') && time()<=C('PROM_END_TIME')){//判断活动时间
                        $baseTime = $baseTime * 2;//春节活动买一送一,其他活动类似
                    }
                }
                if($orderInfo['IsYear']==1){//如果是年费
                    $baseTime = $baseTime * 12;
                }
                //构造权限数组
                $group = array();
                $userGroupObj = D('Manage/UserGroup');
                //基于当前权限规则(组卷端用户只能保持一个特殊权限)
                $groupArr = $userGroupObj->getGroupByWhere(['UserID'=>$orderInfo['UID'],'GroupName'=>1],
                    'GroupID,AddTime,LastTime');
                if($groupArr){//判断曾经是否有特殊权限
                    $groupArr = $groupArr[0];
                    //判断曾经的权限是否和当前购买权限相同,相同就更新时间,不同更新权限
                    if($groupArr['GroupID']==$orderInfo['PowerID']){//
                        //判断权限是否到期
                        if((int)$groupArr['LastTime']<(int)time()){//已经过期 重置时间 目前体系 暂不考虑成长值和记录开通历史功能
                            $curTime = (int)time();
                            $group['AddTime']  = $curTime;
                            $group['LastTime'] = $curTime + $baseTime;
                        }else{//还拥有该权限 增加结束时间
                            $lastTime = (int)$groupArr['LastTime'] + $baseTime;
                            $group['LastTime'] = $lastTime;
                        }
                    }else{//如果权限不同,不管权限剩余时间 更新当前
                        $curTime = (int)time();
                        $group['AddTime']  = $curTime;
                        $group['LastTime'] = $curTime + $baseTime;
                        $group['GroupID']  = $orderInfo['PowerID'];//变更权限ID
                    }
                    $openResult = $userGroupObj->updateData(
                            $group,
                            ['UserID'=>$orderInfo['UID'],'GroupName'=>1]
                    );
                }else{//插入
                    $group['GroupName'] = 1;//分组分类
                    $group['UserID']    = $orderInfo['UID'];//用户ID
                    $group['GroupID']   = $orderInfo['PowerID'];//具体权限ID
                    $curTime = time();
                    $group['AddTime']   = $curTime;
                    $group['LastTime']  = $curTime + $baseTime;
                    $openResult = $userGroupObj->insertData($group);
                }
                if(!$openResult){//容错处理 记录开通失败的记录 以供人工再开通
                    //再增加表记录
                }
                //跳转至支付成功页面
                $url = U(C('WLN_ALI_SUCCESS_PAGE'),$return);
                redirect($url,0,'');
                exit();
            }else {
                //记录失败订单
                $param['AliTradeStatus'] = $_GET['trade_status'];//交易状态
                $orderListObj->updateOrder($param,$orderID);
                //跳转至支付失败页面
                $url = U(C('WLN_ALI_ERROR_PAGE'),$return);
                redirect($url,0,'');
                exit();
            }
        }else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数
            echo "支付失败！";
        }
    }

}
