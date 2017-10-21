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
    <!--顶部导航-->
<div class="top-logo-wrap w1000">
    <a class="top-logo" href="/">
        <img src="/Public/index/imgs/publ/logo.png" alt="logo"/>
    </a>
</div>
<div class="top-nav-fixed">
    <div class="top-nav-wrap">
        <div class="top-nav w1000">
            <a class="top-nav-item" href="/">首页</a>
            <span class="top-nav-item">
                <a class="tn-link" href="<?php echo U('/Home');?>">组卷</a>
            </span>
            <span class="top-nav-item">
                <a class="tn-link" href="<?php echo U('/Aat');?>">提分</a>
            </span>
        </div>
    </div>
</div>
<!--顶部导航-end-->
    <!--首页头部-end-->
    <div class="form-wrap bg-f8f8f8">
        <div class="form-container w1000">
            <!-- 注册反馈信息 -->
            <div class="feedback-msg-container">
                <div class="fb-msg-title">
                    <i class="iconfont">&#xe631;</i>
                    <span class="msg">注册成功！</span>
                </div>
                <div class="fb-msg-content">
                <!--<p class="fb-msg-conext">邮件已发送到你的注册邮箱，请您进入邮箱点击链接完成注册！</p>-->
                    <div class="fb-msg-btn">
                        <a href="<?php echo U('Index/Index/index');?>" class="nor-btn">进入首页</a>
                    </div>
                    
                    <!--<div class="fb-msg-tips">
                        <p>没有收到邮件？请坚持一下垃圾邮件，并将邮件设为非垃圾邮件。<br />
如果还是没有收到邮件？您可以：<a class="link" href="">重新发送激活邮件</a></p>
                    </div>-->
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