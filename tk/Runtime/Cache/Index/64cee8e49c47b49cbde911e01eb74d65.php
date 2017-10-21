<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="zh-CN">
<head>
    <title><?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <meta property="qc:admins" content="167741560767461006375" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <link type="text/css" href="/Public/index/css/wln-base.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/index/css/index.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link rel="stylesheet" href="/Public/index/css/style.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"/>
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
			<span class="top-nav-item">
                <a class="tn-link" href="<?php echo U('User/Index/passport/url/Statistics');?>">教学统计</a>
            </span>
        </div>
    </div>
</div>
<!--顶部导航-end-->
    <!--首页头部-end-->
    <!--登录框-->
    <div class="login-wrap w1000">
        <div class="login-form-box" id="loginForm">
            <div class="login-form-container">
                <form action="" class="lf-login-wrap" id="login">
                    <div class="lf-login-item">
                        <i class="iconfont">&#xe606;</i>
                        <input type="text" name="userName" id="userName" placeholder="用户名/邮箱/手机号"/>
                    </div>
                    <div class="lf-login-item">
                        <i class="iconfont">&#xe607;</i>
                        <input type="password" name="password" id="passWord" placeholder="登录密码"/>
                    </div>
                        <div class="lf-login-msg" style="display:none">
                            <i class="iconfont">&#xe619;</i>
                            <span class="errorMsg"></span>
                        </div>
                    <div class="lf-login-other clearfix">
                        <label class="auto-login" for="ifSave">
                            <input type="checkbox" name="ifSave" id="ifSave"/>
                            下次自动登录
                        </label>
                        <a class="find-password" id="zuJuan" href="<?php echo U('User/Index/getPassword');?>">找回密码</a>
                    </div>
                    <div class="lf-login-btn">
                        <input class="login-big-btn nor-btn" type="button" value="登录" id="loginSubmit" />
                    </div>
                    <div class="lf-reg-btn">
						没有账户？<a href="<?php echo U('User/Index/registerIndex');?>">立即注册</a>
                    </div>
                    <!--<div class="lf-reg-btn">
                        	没有账户？<a href="<?php echo U('User/Index/registerIndex');?>">立即注册</a> 或 使用合作帐号登录
                    </div>
                    <div class="lf-quick-login">
                         <a class="QQLogin" href="javascript:">
                             <img src="/Public/index/imgs/icon/Connect_logo_3.png" alt=""/>
                         </a>
                    </div>-->
					<div class="lf-quick-login">
                         <a class="ForLogin" href="http://web.forclass.net/OAuth/authorize?response_type=token&client_id=Diagnosis20170808&redirect_uri=http://tk.forclass.net/User/Index/ForClassCalllogin">
                             <img src="/Public/index/imgs/icon/forclass.png" alt=""/>
                         </a>
                    </div>

                </form>
            </div>
        </div>
        <!--登录后状态-->
        <div class="login-success-box" id="loginSuccess" style="display: none">
            <div class="ls-user-info-box clearfix">
                <span class="ls-user-photo">
                    <img class="userPic" height="64" width="64" src="" alt="头像"/>
                    <span class="photo-wrapper"></span>
                </span>
                <span class="ls-user-info">
                <a class="head elli whois" href="">
                </a>
                <p class="ls-user-cite years-service">
                    <i class="icon"></i><span class="groupName"></span></p>
                </span>
            </div>
            <div class="ls-user-sys-info">
                <p class="user-sys-item loginNum"></p>
                <p class="user-sys-item loginTime"></p>
                <p class="user-sys-item loginIP"></p>
                <p class="user-sys-item different"></p>
            </div>
            <div class="ls-come-in-sys comeIn"></div>
        </div>
        <!--登录后状态-end-->
    </div>
    <!--登录框-end-->
    <!--banner-->
    <div class="banner-wrap">
        <div class="banner-show-box">
            <div class="wln-slider">
                <div class="hd">
                    <div class="nav-bar">
                        <span></span>
                    </div>
                </div>
                <div class="bd">
                    <ul>
                        <li style="background: url(/Public/index/imgs/banner/bn2.jpg) 50% 60% no-repeat;" >
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--banner-end-->

    <!--footer-->
    <script>var userInfo=<?php echo ($userInfo); ?>;</script>
    <div class="footer-wrap">
    <div class="footer-box clearfix w1000">
       <!--footer box--><?php echo ($config["IndexName"]); ?> <?php echo (C("WLN_VERSION")); ?>
    </div>
</div>
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/jquery.cookie.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/layer/layer.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/index/js/wlnBase.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
</body>
</html>
    <!--footer-end-->
    <script type="text/javascript" src="/Public/plugin/jquery.SuperSlide.2.1.1.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/index/js/pingtai.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>

</body>
</html>