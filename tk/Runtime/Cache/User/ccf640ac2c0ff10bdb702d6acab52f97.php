<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="zh-CN">
<head>
    <title>ForClass快捷登录 - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="ForClass快捷登录,<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="ForClass快捷登录,<?php echo ($config["Description"]); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <link type="text/css" href="/Public/index/css/wln-base.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link rel="stylesheet" href="/Public/index/css/register.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"/>
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
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
<!-- qq登录弹框 绑定账号-->
<div class="w1000">
    <div class="qq-login-content">
        <h1>题库欢迎您！</h1>
        <p class="tips">您正在使用ForClass帐号登录</p>
        <div class="qq-id-box">
            <span class="qq-pic-box"><img src="<?php echo ((isset($imgurl) && ($imgurl !== ""))?($imgurl):'/Public/index/imgs/icon/photo.jpg'); ?>" width="60" height="60" alt="QQ头像" /></span><span class="qq-username"><?php echo ((isset($nick) && ($nick !== ""))?($nick):"尊敬的题库用户"); ?></span>
        </div>

        <div class="btn-site"><a href="/" id="loginNow" class="nor-btn">立即进入</a><!-- <a href="javascript:;" id="bindAccount" class="link">绑定题库帐号</a>--></div>
    </div>
</div>

<script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/jquery.newplaceholder.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/layer/layer.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>

<!--[if lte IE 6]>
<script type="text/javascript" src="/Public/plugin/png.js"></script>
<script>DD_belatedPNG.fix('a,div,span,img,i');</script>
<![endif]-->
<script type="text/javascript">
    $(function(){
        $.QQCallBack.init();
        $('input, textarea').placeholder();
    });
    jQuery.QQCallBack = {
           init:function(){
               $(document).on('selectstart',function(){return false;});
               this.bindEvent();
               $('.topLoginButton').on('click',function(){
                     window.location.href = '/';
               });
           },
           bindEvent:function(){
               //直接登录
               $('#loginNow').on('click',function(){
                    window.location.href = "";
               }
               // $('#loginNow').on('click',function(){
               //     var qqLoginMsg1 = '<div id="qqLoginMsg1"><div class="other-use-id-content"><div class="use-id"><a href="javascript:;" class="use-id-item selected" identity="1"><i class="iconfont teac-icon"></i><span class="id-name">我是老师</span></a><a href="javascript:;" class="use-id-item" identity="0"><i class="iconfont stud-icon"></i><span class="id-name">我是学生</span></a></div><div class="verticy"><div class="sms-msg-wrap"><p class="tips" style="padding-left:60px;">本次登录需要验证短信</p><div class="form-item" style="padding-left:60px;"><span class="item-content"><span class="normal-input-wrap"> <input class="normal-input phoneNum" type="text" value="" placeholder="输入您的手机号码" id="phoneNum"></span></span><span class="item-msg" id="phoneMsg" style="display:none;"> <i class="true iconfont"></i></span></div><div class="form-item" style="padding-left:60px;"><span class="item-content"><span class="short-input-wrap left"><input class="short-input phoneCode" id="phoneCode" type="text" placeholder="短信验证码"></span><input type="button" class="pointer getPhoneRand verification-btn" value="获取验证码" id="sendPhoneCode"></span><span class="item-msg" id="phoneCodeMsg" style="display:none;"> <i class="false iconfont"></i><span class="errorMsg"></span></span></div></div></div><div class="use-id-btn" style="margin-top:25px;"><input class="nor-btn" type="button" value="直接登录" id="directLogin"></div></div></div>';
               //      layer.open({
               //          type:1,
               //          area:['350px','460px'],
               //          title:'如果您之前未注册过题库账号',
               //          content:qqLoginMsg1
               //      });
               // });
               //切换身份
               $(document).on('click','.use-id-item',function(){
                   $(this).addClass("selected").siblings(".use-id-item").removeClass("selected");
               });
               //获取验证码
               $(document).on('click','.getPhoneRand',function(){
                   if($('#phoneNum').val()==''){
                       $('#qqLoginMsg1 #phoneCodeMsg .errorMsg').html('手机号码不能为空!');
                       $('#qqLoginMsg1 #phoneCodeMsg').show();
                       return false;
                   }
                   var str = '<div class="verification-code-msg"><span class="item-tit">图片验证码</span><input type="text" name="imgCode" id="imgCode" maxlength="6" class="normal-input imgCode"><img width="95" height="36" class="verification-image" id="verifyImg" src="<?php echo U('Index/Index/verify');?>" border="0" alt="点击刷新验证码" style="cursor:pointer" align="absmiddle"></div>';
                   var index2 = layer.open({
                       type: 1,
                       area:["440px","150px"],
                       title:'请输入验证码',
                       content: str,
                       btn:'确定',
                       yes:function(){
                           var index3 = layer.load();
                           $.post(U('User/Index/sendPhoneCode'),{'phoneNum':$('#phoneNum').val(),'imgCode':$('#imgCode').val(),'rand':Math.random()},function(e){
                               layer.close(index3);
                               if(e.data=='success'){
                                   layer.close(index2);
                                   $.myCommon.showLeaveTime();
                               }else{
                                   layer.close(index2);
                                   $('#qqLoginMsg1 #phoneCodeMsg .errorMsg').html(e.data);
                                   $('#qqLoginMsg1 #phoneCodeMsg').show();
                                   return false;
                               }
                           });
                       }
                   });
               });
               //直接进入
               $(document).on('click','#directLogin',function(){
                   var phoneNum  = $('#phoneNum').val();
                   var phoneCode = $('#phoneCode').val();
                   if($.myCommon.checkPhoneNum(phoneNum)==false){
                       $('#qqLoginMsg1 #phoneCodeMsg .errorMsg').html('请填写正确的手机号！');
                       $('#qqLoginMsg1 #phoneCodeMsg').show();
                       return false;
                   }
                   if( phoneCode == ''){
                       $('#qqLoginMsg1 #phoneCodeMsg .errorMsg').html('验证码不能为空');
                       $('#qqLoginMsg1 #phoneCodeMsg').show();
                       return false;
                   }
                   var index3 = layer.load();
                   $.post(U('User/Index/QQCallBack'),{'mobile': phoneNum,'mobileCode':phoneCode,'identity':$('.selected').attr('identity')},function(data) {
                       layer.close(index3);
                       if(data['data'][0]=='error'){
                           layer.open({
                               type: 1,
                               area:["300px","100px"],
                               title:'错误提示',
                               content:data['data'][0]
                           });
                           return false;
                       }
                       window.location.href = '/';
                   });

               });
               //绑定帐号
               $('#bindAccount').on('click',function(){
                   var qqLoginMsg2= '<div id="qqLoginMsg2"><div class="other-use-id-content"><form action="" class="lf-login-wrap" id="login"><div class="lf-login-item"><i class="iconfont"></i><input type="text" name="userName" id="userName" placeholder="用户名/邮箱/手机号"></div><div class="lf-login-item"><i class="iconfont"></i><input type="password" name="password" id="passWord" placeholder="登录密码"></div><div class="lf-login-other clearfix" style="margin-top:0px;"><a class="find-password" target="_blank" href="'+U('Index/Index/getPassword')+'">忘记密码?</a></div><div class="lf-login-msg" style="display:none"><i class="iconfont"></i><span class="errorMsg"></span></div><div class="lf-login-btn"><input class="login-big-btn nor-btn" type="button" value="绑定" id="QQloginSubmit"></div><p class="tips">绑定已有帐号方便您快速登录题库</p></form></div></div>';
                   layer.open({
                        type:1,
                        area:['350px','430px'],
                        title:'绑定已有帐号',
                        content:qqLoginMsg2
                   });
               });
               //绑定确定
               $(document).on('click','#QQloginSubmit',function(){
                   if(lock!='') return false;
                   var username = $('#userName').val();
                   var password = $('#passWord').val();
                   if(username == '' || password == ''){
                       $('#qqLoginMsg2 .lf-login-msg .errorMsg').html('账户或密码不能为空');
                       $('#qqLoginMsg2 .lf-login-msg').show();
                       return false;
                   }
                   lock = 'QQCallBack';
                   var index1 = layer.load();//过程提示
                   $.post(U('User/Index/QQCallBack'),{ 'username': username,'password':password,'ifBind':1}, function(data) {
                       layer.close(index1);
                       lock = '';
                       if(data['data'][0]=='success') {
                           window.location.href = '/';
                           return false;
                       }
                       $('#qqLoginMsg2 .lf-login-msg .errorMsg').html(data.data[1]);
                       $('#qqLoginMsg2 .lf-login-msg').show();
                   });
               });
           }
    }
</script>
<!--不使用底部文件-->
<div class="footer-wrap second-footer-wrap">
    <div class="footer-box clearfix w1000">
        <div class="footer-info">
            <a href="<?php echo U('Index/About/knowMe',array('param'=>'knowMeIntro'));?>" target="_blank">关于我们</a>
            |
            <a href="<?php echo U('Index/About/about',array('param'=>'aboutPrivacy'));?>" target="_blank">隐私条款</a>
            |
            <a href="<?php echo U('Index/About/about',array('param'=>'aboutRecruit'));?>" target="_blank">人才招聘</a>
            |
            <a href="<?php echo U('Index/About/leaveMess');?>">留言反馈</a>
            |
            <a href="<?php echo U('Index/About/about',array('param'=>'aboutFriendLinkl'));?>" target="_blank">友情链接</a>
            |
            <a href="<?php echo U('Index/About/about',array('param'=>'aboutOnlineS'));?>" target="_blank">联系我们</a>

            | <?php echo (C("STATISTICAL_CODE.INDEX")); ?><br>
            <cite class="f-yahei">©</cite> 2017 智慧云题库云平台 All rights reserved | 增值电信业务经营许可证：<a href="http://www.miitbeian.gov.cn/"><?php echo (C("KEEP_VERSION")); ?></a>
            |
            <a class="anbei" target="_blank" href="javascript:;">豫公网安备 37165433002002号</a>
        </div>
    </div>
</div>
</body>
</html>