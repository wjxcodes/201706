<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="zh-CN">
<head>
    <title>找回密码 - 题库教学云平台</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <link type="text/css" href="/Public/index/css/wln-base.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/index/css/register.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/index/css/style.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet"/>
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
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
<div class="form-wrap bg-f8f8f8" id="getPassword">
    <div class="form-container w1000 clearfix">
        <div class="form-bd-wrap clearfix" style="padding-top:0px">
            <div class="form-process-wrap">
                <div class="fp-area fp-step1 getTab">
                    <span class="fp-area-three on" style="text-align: left;">确认账号</span>
                    <span class="fp-area-three">安全验证</span>
                    <span class="fp-area-three" style="text-align: right;">重置密码</span>
                </div>
            </div>
            <div class="form-left-bd gpContent">
                <form action="">
                    <input type="hidden" value="" id="userID"/>
                    <div class="getTab1">
                        <div class="form-item">
                            <span class="item-tit"><s>*</s>
                                <select id="thisStyle" name="thisStyle">
                                    <option value="UserName">用户名</option>
                                    <option value="Phonecode">手机号</option>
                                    <option value="Email">邮箱</option>
                                </select>：
                                </span>
                            <span class="item-content">
                                <span class="normal-input-wrap">
                                    <input class="normal-input userName" type="text" value="" placeholder="用户名/邮箱/手机号"/>
                                </span>
                            </span>
                            <span class="item-msg" id="userNameMsg"><i class="true iconfont">&#xe631;</i></span>
                        </div>
                        <div class="form-item">
                            <span class="item-tit"><s>*</s>验证码：</span>
                            <span class="item-content"><span class="short-input-wrap left">
                                    <input class="short-input imgCode" type="text" value="" placeholder="请输入4位数字验证码"/>
                            </span>
                                <img height="44px" src="<?php echo U('Index/Index/verify');?>" name="imgVerifuy" style="margin-left:19px;cursor: pointer" id="verifyImg"/>
                                </span>
                            <span class="item-msg" id="verifyImgMsg"></span>
                        </div>
                    </div>
                    <div class="getTab2" style="display: none;">
                        <div class="form-item">
                            <span class="item-tit"><s>*</s>验证方式：</span>
                            <span class="item-content">
                                <select class="style" name="style" id="checkStyle"></select>
                            </span>
                            <span class="item-msg" id="phoneMsg"><i class="true iconfont">&#xe631;</i></span>
                        </div>
                        <!--<div class="form-item">
                            <span class="item-tit"><s>*</s>验证码：</span>
                            <span class="item-content"><span class="short-input-wrap left">
                                    <input class="short-input phoneCode" type="text" value="" />

                                </span>
                                <input type="button" class="pointer getPhoneCode verification-btn" value="获取验证码" id="sendPhoneCode" />
                                </span>
                            <span class="item-msg sendMsg" id="sendMsg"></span>
                        </div>-->
                    </div>
                    <div class="getTab3" style="display: none;">
                        <div class="form-item">
                            <span class="item-tit"><s>*</s>密码：</span>
                            <span class="item-content">
                                <span class="normal-input-wrap">
                                    <input class="normal-input password" type="password" value="" id="password" name="password" placeholder="请输入6到18位的密码"/>
                                </span>
                            </span>
                            <span class="item-msg" id="passwordMsg"></span>
                        </div>
                        <div class="form-item">
                            <span class="item-tit"><s>*</s>确认密码：</span>
                            <span class="item-content">
                                <span class="normal-input-wrap">
                                    <input class="normal-input password1" type="password" value="" placeholder="重复上面的密码"/>
                                    <input class="normal-input savecodes" type="hidden" value=""/>
                                </span>
                            </span>
                            <span class="item-msg" id="password1Msg"></span>
                        </div>
                    </div>
                    <div class="form-item">
                        <span class="item-tit"></span>
                        <span class="item-content"><a href="javascript:void (0);" class="nor-btn blue-btn skipTwo" id="goNext">下一步</a></span>
                        <span class="item-msg" id="agreeMsg"></span>
                    </div>
                    <div id="goBack" style="display: none;">返回上一步</div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--参加任务列表-->

<!--footer-->
<script type="text/javascript" src="/Public/plugin/jquery.SuperSlide.2.1.1.js"></script>

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