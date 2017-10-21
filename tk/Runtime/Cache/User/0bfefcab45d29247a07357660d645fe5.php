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
    <link rel="stylesheet" href="/Public/index/css/style.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"/>
    <style type="text/css">
    .has-account{display: block;}
    </style>
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/Public/plugin/strongPassword.js"></script>
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
            <div class="form-bd-wrap clearfix" style="padding-top:0px">
                <div class="form-left-bd" style="padding-left:30px;width:589px">
                    <form action="">
                        <div class="form-item" style="padding-top:0px">
                            <span class="item-tit"><s>*</s>身份：</span>
                            <span class="item-content user-id select-id">
                                <span class="id-option io-left <?php if(($who) == "teacher"): ?>on<?php endif; ?>" who="teacher">
                                    <span class="id-name">
                                        <i class="iconfont">&#xe605;</i>老师
                                    </span>
                                </span>
                                <span class="id-option <?php if(($who) == "student"): ?>on<?php endif; ?>"  who="student">
                                    <span class="id-name">
                                        <i class="iconfont">&#xe612;</i>学生
                                    </span>
                                </span>
                            </span>
                            <span class="item-msg"></span>
                        </div>
                        <div class="teacherWay">
                            <div class="form-item">
                                <span class="item-tit"><s>*</s>手机号：</span>
                            <span class="item-content">
                                <span class="normal-input-wrap">
                                    <input class="normal-input phoneNum" type="text" value="" id="PhoneNum" />
                                </span>
                            </span>
                                <span class="item-msg" id="phoneMsg"><i class="true iconfont">&#xe631;</i></span>
                            </div>
                            <!--<div class="form-item">
                                <span class="item-tit"><s>*</s>手机验证码：</span>
                            <span class="item-content"><span class="short-input-wrap left">
                                    <input class="short-input phoneCode" type="text" value="" />
                                    
                                </span>
                                <input type="button" class="pointer getPhoneCode verification-btn" value="获取验证码" id="sendPhoneCode" />
                                </span>
                                <span class="item-msg" id="phoneCodeMsg"></span>
                            </div>-->
                        </div>
                        <div class="emailWay" style="display: none;">
                            <div class="form-item">
                                <span class="item-tit"><s>*</s>邮箱：</span>
                                <span class="item-content">
                                    <span class="normal-input-wrap">
                                        <input class="normal-input emailNum" type="text" value="" id="Email" />
                                    </span>
                                </span>
                                <span class="item-msg" id="emailMsg"></span>
                            </div>
                            <!--<div class="form-item">
                                <span class="item-tit"><s>*</s>邮箱验证码：</span>
                                <span class="item-content">
                                    <span class="short-input-wrap left">
                                        <input class="short-input emailCode" type="text" value="" />
                                    </span>
                                    <input type="button" class="pointer getPhoneCode verification-btn" value="获取验证码" id="sendEmailCode" />
                                </span>
                                <span class="item-msg" id="emailCodeMsg"></span>
                            </div>-->
                        </div>
                        <div class="form-item">
                            <span class="item-tit"><s>*</s>昵称：</span>
                            <span class="item-content">
                                <span class="normal-input-wrap">
                                    <input class="normal-input nickname" type="text" value="" />
                                </span>
                            </span>
                            <span class="item-msg" id="nicknameMsg" style="padding:7px 0 7px 10px;height:auto; width:150px;line-height:1.4em"></span>
                        </div>
                        <div class="form-item">
                            <span class="item-tit"><s>*</s>密码：</span>
                            <span class="item-content">
                                <span class="normal-input-wrap">
                                    <input class="normal-input password" type="password" value="" id="password" name="password"/>
                                </span>
                            </span>
                            <span class="item-msg" id="passwordMsg"></span>
                            <span class="item-msg" id="strongPassword">
                                <span class="password-msg">
                                    <b>安全程度：</b>
                                    <span class="level">
                                        <cite>弱</cite>
                                        <cite>中</cite>
                                        <cite>强</cite>
                                    </span>
                                </span>
                            </span>
                        </div>
                        <div class="form-item">
                            <span class="item-tit"><s>*</s>确认密码：</span>
                            <span class="item-content">
                                <span class="normal-input-wrap">
                                    <input class="normal-input password1" type="password" value="" />
                                </span>
                            </span>
                            <span class="item-msg" id="password1Msg"></span>
                        </div>
                        <div class="form-item">
                            <span class="item-tit"></span>
                            <span class="item-content">
                                <label for="checkboxAgree">
                                    <input type="checkbox" name="checkboxAgree" id="checkboxAgree" checked="checked"/>
                                    已经阅读并同意</label><a href="javascript:;" class="wln-tos link serviceTerm">《题库用户协议》</a>

                            </span>
                        </div>
                        <div class="form-item">
                            <span class="item-tit"></span>
                            <span class="item-content"> <a href="javascript:void (0);" class="nor-btn blue-btn registerSave" id="registerSave">立即注册</a> </span>
                            <span class="item-msg" id="agreeMsg"></span>
                        </div>
                    </form>
                </div>
                <div class="form-right-bd reg-right-container">
                    <!-- 快捷登录 -->
                        <!--<p class="quick-login-site">
                        使用合作帐号快速登录 <br />
                            <a class="QQLogin" href="javascript:"><img
                                    src="/Public/index/imgs/icon/Connect_logo_4.png" alt=""/></a>
                        </p>-->
                        <!-- 快捷登录END -->
                    <div class="rr-container-inner reg-help teacherHelp" <?php if(($who) == "teacher"): ?>style="display:block"<?php else: ?>style="display:none"<?php endif; ?>>
                        <h3 class="title">教师用户注册说明</h3>
                        <div class="rh-context">
                            1. 如果贵校已经IP开通，请在学校IP地址范围内注册，享有集体用户权限。
                            <br />
                            2. 如果贵校已经账号开通，请用您个人的账号密码直接 <a class="link" href="<?php echo U('Index/Index/index');?>">登录</a> 即可，享有集体用户权限。<br />
                            3. 如果贵校未开通，请直接注册使用，享有个人用户对应的权限。

                            <br />
                        </div>
                    </div>
                    <div class="rr-container-inner reg-help studentHelp" <?php if(($who) == "student"): ?>style="display: block;"<?php else: ?>style="display:none"<?php endif; ?>>
                        您还可以使用<a class="link studentWay" way="1"> 邮箱注册</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--参加任务列表-->

    <!--footer-->
    <script type="text/javascript" src="/Public/plugin/jquery.SuperSlide.2.1.1.js"></script>
    <script type="text/javascript" src="/Public/index/js/pingtai.js"></script>

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