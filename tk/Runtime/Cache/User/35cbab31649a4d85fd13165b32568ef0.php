<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>购买权限_<?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Description"]); ?>" />
    <meta name="description" content="<?php echo ($config["Keyword"]); ?>" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <!--[if lte IE 6]>
    <script type="text/javascript" src="/Public/plugin/png.js"></script>
    <script>DD_belatedPNG.fix('a,img,div,span');</script>
    <![endif]-->
    <link rel="stylesheet" href="/Public/index/css/wln-base.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"/>
    <link rel="stylesheet" href="/Public/index/css/register.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"/>
    <script>
        var vipPrice = <?php echo ($vipPrice); ?>;
        var superVipPrice = <?php echo ($superVipPrice); ?>;
        var discount = <?php echo ($discount); ?>;
        var ifLogin = <?php echo ($ifLogin); ?>;
    </script>
</head>
<body class="bg-white">
<!--登录注册头部-->
<!--顶部条-->
<div class="top-bar-wrap clearfix">
    <div class="top-bar-box w1000">
        <span class="top-contact">

        </span>
        <!--未登录
        <span class="top-use topLogin">
            <?php if(($hideLogin) != "1"): ?><a class="tu-login left topLoginButton" href="javaScript:;">登录</a><?php endif; ?>
            <a class="tu-reg left" href="<?php echo U('User/Index/registerIndex');?>" target="_blank">注册</a>
        </span>-->
        <!--已登录
        <span class="top-use topLoginSuccess" style="display: none">
            <span class="left">欢迎,</span>
            <span class="topBarUserName left"></span>
            <a class="tu-login-out loginOut left" href="javaScript:void (0);" whois="">退出</a>
            <span class="goToSystem left"></span>
        </span>-->
    </div>
</div>
<!--顶部条-end-->
<!--logo-->
<div class="bg-white">
    <div class="top-logo-wrap w1000" style="padding:15px 0 20px">
    <a class="top-logo" href="/">
        <img src="/Public/index/imgs/publ/logo.png" alt="题库logo"/>
    </a>
    <h1 class="usercenter-page-title"><?php echo ($title); ?></h1>
    <div class="has-account hide topLogin">我已经注册，现在就
        <!--<a class="link topLoginButton" href="javascript:;">登录</a>-->
        <a class="link" href="<?php echo U('Index/Index/index');?>">登录</a>
    </div>
</div>
</div>

<!--登录注册头部-end-->

<!--登录注册头部-end-->
<!--首页头部-end-->
<div class="form-wrap bg-f8f8f8">
    <div class="form-container w1000 clearfix">
        <div class="form-bd-wrap wln-pay-bd-wrap clearfix">
            <div class="pay-left-container">
                <div class="show-selected-vip vipLevel1" style="display: block;">
                    <div class="vipicon"><img src="/Public/index/images/vip-icon1.png" alt="VIP至尊用户" /></div>
                    <div class="vip-detail">
                        <p class="title">VIP专享用户</p>
                        <p class="money"><span><?php echo ($vipPrice); ?></span> 元/月</p>
                    </div>
                </div>
                <div class="show-selected-vip vipLevel2" style="display: none;">
                    <div class="vipicon"><img src="/Public/index/images/vip-icon2.png" alt="VIP至尊用户" /></div>
                    <div class="vip-detail">
                        <p class="title">VIP至尊用户</p>
                        <p class="money"><span><?php echo ($superVipPrice); ?></span> 元/月</p>
                    </div>
                </div>
            </div>
            <div class="pay-right-container">
                    <div class="form-item" style="padding-top:0px">
                        <span class="item-tit">开通服务：</span>
                            <span class="item-content member-type select-id">
                                <span class="id-option io-left on" who="0">VIP专享用户</span>
                                <span class="id-option" who="1"> VIP至尊用户</span>
                            </span>
                    </div>
                    <div class="form-item">
                        <span class="item-tit">付费模式：</span>
                            <span class="item-content vip-optime select-id">
                                <span class="id-optime id-option io-left on" time="0">按月付费</span>
                                <span class="id-optime id-option" time="1">按年付费</span>
                            </span>
                    </div>
                    <div class="form-item">
                        <span class="item-tit">开通时长：</span>
                                <span class="item-content"><span class="short-input-wrap left">
                                    <input class="short-input" type="text" value="1" maxlength="3" id="ktTime"  />
                                </span> <span class="kt-optime">月</span>
                                </span>
                        <span class="item-msg hide" id="timeMsg"><i class="false iconfont">&#xe634;</i>请输入正确的时长!</span>
                        <span class="item-msg hide" id="timeBigMsg"><i class="false iconfont">&#xe634;</i>土豪君,目前最长年限为80年哦!</span>
                    </div>
                    <div class="form-item">
                        <span class="item-tit">应付金额：</span>
                                <span class="item-content">
                                    <span class="money totalMoney"><?php echo ($vipPrice); ?></span><span class="optime">元</span>
                                    <?php echo ($slogan); ?>
                                </span>
                    </div>
                    <div class="form-item">
                        <span class="item-tit"></span>
                        <span class="item-content"><a href="javascript:void(0);" class="nor-btn blue-btn" id="submitBtn">立即支付</a></span>
                    </div>
            </div>
            <form action="" method="post" id="operBuy" class="hide">
                <input type="hidden" value="0" name="memberType" id="memberType" />
                <input type="hidden" value="0" name="timeType" id="timeType" />
                <input type="hidden" value="1" name="times" id="times" />
                <input type="submit" value="确定" />
            </form>
        </div>
    </div>
</div>
<script>
    jQuery.pay={
          init:function(){
              this.bindEvent();
          },
          bindEvent:function(){
              $('.member-type').on('click','.id-option',function(){//服务模式
                  if($(this).hasClass('on')) return false;
                  $(this).addClass('on').siblings().removeClass('on');
                  var who = $(this).attr('who');
                  if(who==0){
                      $('.vipLevel1').show();
                      $('.vipLevel2').hide();
                      $('#memberType').val(0);
                  }else{
                      $('.vipLevel2').show();
                      $('.vipLevel1').hide();
                      $('#memberType').val(1);
                  }
                  $('#ktTime').val(1);
                  calculate(who,$('#timeType').val(),1);
              });
              $('.vip-optime').on('click','.id-optime',function(){//时间模式
                  if($(this).hasClass('on')) return false;
                  $(this).addClass('on').siblings().removeClass('on');
                  var time = $(this).attr('time');
                  if(time==0){
                      $('.kt-optime').text('月');
                      $('#timeType').val(0);
                  }else{
                      $('.kt-optime').text('年');
                      $('#timeType').val(1);
                  }
                  $('#ktTime').val(1);
                  calculate($('#memberType').val(),time,1);
              });
              //动态计算
              if($.browser.msie && parseFloat($.browser.version)<9.0){//IE LT9
                  $('#ktTime').on('propertychange',function(){
                      if(window.event.propertyName == 'value') changeFee();
                  });
              }else{
                  $('#ktTime').on('input',function(){changeFee();});
              }
              $('#ktTime').on('keyup',function(){
                  var  v = $(this).val();
                  $(this).val(v.replace(/\D/gi,''));
                  if(v==0 || v==''){
                      $('#timeBigMsg').hide();
                      $('#timeMsg').show();
                      $('.totalMoney').text(0);
                  }else{
                      $('#timeMsg').hide();
                  }
              });
              function changeFee(){//计算函数
                  var val = $('#ktTime').val();
                  var reg= /^[0-9]+$/;
                  if(reg.test(val)){
                      var ttype = $('#timeType').val();
                      if(ttype == 1){//年费,限制年限
                           if(val>80){
                               $('#timeBigMsg').show();
                           }
                      }
                      calculate($('#memberType').val(),$('#timeType').val(),$('#ktTime').val());
                  }
              }
              function calculate(mtype,ttype,time){//计算函数
                  if(time<=80) $('#timeBigMsg').hide();
                  $('#timeMsg').hide();
                  var basePrice = vipPrice;
                  if(mtype==1) basePrice=superVipPrice;
                  var totalFee = basePrice*time;
                  if(ttype==1) totalFee = totalFee*12*discount;//年费折扣
                  totalFee=totalFee.toFixed(2);
                  $('.totalMoney').text(totalFee);
              }
              //提交事件
              $('#submitBtn').on('click',function(){
                  //非法验证
                  var mtype = $('#memberType').val();
                  var ttype = $('#timeType').val();
                  var time  = $('#ktTime').val();
                  $('#times').val(time);
                  if(mtype!=0 && mtype!=1 || ttype!=0 && ttype!=1) return false;//检验合法性
                  if(time>999) return false; //时间最大数值验证
                  if(ttype==1){//检验年费时长
                      if(time>80){
                          return false;
                      }
                  }
                  $.post(U('User/Index/operOrder'),{}, function(data) {
                        if(data.data!=1){
                            if(data.data=='30203'){
                                layer.closeAll();
                                layer.open({
                                    type: 0,
                                    content: '您的账户已被锁定,暂时不能购买会员服务'
                                });
                            }else if(data.data=='30205'){
                                layer.closeAll();
                                layer.open({
                                    type: 0,
                                    content: '请登录!如已登录,请确认您是教师用户!'
                                });
                            }else{
                                $('.topLoginButton:visible').trigger('click');
                            }
                            return false;
                        }
                        $('#operBuy').submit();
                  });
              });
          }
    };
    $(function(){
         if(ifLogin!=1) {
             $('.topLoginButton:visible').trigger('click');
         }
         jQuery.pay.init();
    });
</script>
<div class="footer-wrap second-footer-wrap">
    <div class="footer-box clearfix w1000">
        
    </div>
</div>
</body>
<script type="text/javascript" src="/Public/plugin/jquery.cookie.js<?php echo (C("UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/layer/layer.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/index/js/wlnBase.js<?php echo (C("UPDATE_FILE_DATE")); ?>"></script>
</html>