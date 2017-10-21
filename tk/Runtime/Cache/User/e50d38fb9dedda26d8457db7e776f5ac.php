<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="zh-CN">
<head>
    <title><?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <link type="text/css" href="/Public/index/css/wln-base.css" rel="stylesheet" />
    <link type="text/css" href="/Public/index/css/register.css" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/Public/plugin/layer/layer.js"></script>
</head>
<body class="bg-white">
    <!--首页头部-->
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
    <!--首页头部-end-->
    <div class="form-wrap bg-f8f8f8">
        <div class="form-container w1000 clearfix">
            <!-- 注册反馈信息 -->
            <div class="feedback-msg-container">
                <div class="fb-msg-title">
                    <i class="iconfont">&#xe631;</i>
                    <span class="msg">注册成功！</span>
                </div>
                <div class="fb-msg-content">
                <p class="fb-msg-conext"><a class="link"><?php echo ($Nickname); ?></a>，欢迎您加入题库！</p>
                    <div class="fb-msg-btn">
                        <a href="<?php echo U('User/Index/authTeacher');?>" class="nor-btn smler-btn light-btn">教师身份认证</a>
                        <a href="<?php echo U('Index/Index/index');?>" class="nor-btn smler-btn">进入首页</a>
                        <!--<a href="" class="nor-btn smler-btn">进入个人中心</a>-->
                    </div>
                </div>
            </div>
            <!-- 注册反馈信息 -->
        </div>
    </div>
    <!--footer-->
    <script type="text/javascript" src="/Public/plugin/jquery.SuperSlide.2.1.1.js"></script>
    <script type="text/javascript" src="/Public/index/js/pingtai.js"></script>
    <script type="text/javascript" src="/Public/default/js/common.js"></script>
    <!--[if lte IE 6]>
    <script type="text/javascript" src="/Public/plugin/png.js"></script>
    <script>DD_belatedPNG.fix('a,div,span,img,i');</script>
    <![endif]-->
    <div class="footer-wrap second-footer-wrap">
    <div class="footer-box clearfix w1000">
        
    </div>
</div>
</body>
<script type="text/javascript" src="/Public/plugin/jquery.cookie.js<?php echo (C("UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/layer/layer.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/index/js/wlnBase.js<?php echo (C("UPDATE_FILE_DATE")); ?>"></script>
</html>
    <!--footer-end-->