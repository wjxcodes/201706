<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="zh-CN">
<head>
    <title><?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <link type="text/css" href="/Public/index/css/wln-base.css<?php echo (C("UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/index/css/register.css<?php echo (C("UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/Public/plugin/layer/layer.js"></script>
        <script>
        var local='<?php echo U('User/authTeacher');?>';
        var mark='register';
        var invit='<?php echo ($invit); ?>';
        var authInfo ='<?php echo ($authStatus); ?>';
    </script>
    <script type="text/javascript" src="/Public/index/js/authTeacher.js<?php echo (C("UPDATE_FILE_DATE")); ?>"></script>
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
        <div class="form-container w1000">
            <!-- 认证流程 -->
            <div class="form-process-wrap">
                <div class="fp-area fp-step2">
                    <span class="fp-area-three on" style="text-align: left;">提交资料</span>
                    <span class="fp-area-three on">等待审核</span>
                    <span class="fp-area-three" style="text-align: right;">认证成功</span>
                </div>
            </div>
            <!-- 认证流程 end-->
            <div class="form-bd-wrap clearfix">
                <div class="form-left-bd">
                <form action="<?php echo U('User/Index/addAuthData');?>" id="form" method="post" enctype="multipart/form-data">
                    <div class="form-item">
                        <span class="item-tit"><s>*</s>真实姓名：</span>
                        <span class="item-content">
                            <span class="normal-input-wrap">
                                <input id="id-realName-input" name="realName" type="text" class="normal-input" style="color:#666;" value="<?php echo ($realName); ?>"/>
                            </span>
                        </span>
                        <span class="item-msg"></span>
                    </div>
                    <div class="form-item">
                        <span class="item-tit"><s>*</s>身份证号：</span>
                        <span class="item-content">
                            <span class="normal-input-wrap">
                                <input id="id-idnumber-input" name="idnumber" type="text" class="normal-input" style="color:#666;" value="<?php echo ($data["IDNumber"]); ?>"/>
                            </span>
                        </span>
                        <span class="item-msg"></span>
                    </div>
                    <div class="form-item">
                        <span class="item-tit"><s>*</s>教师资格证编号：</span>
                        <span class="item-content" style="max-width:277px;">
                            <span class="normal-input-wrap">
                                <input id="id-qualification-input" name="qualification" type="text" class="normal-input" style="color:#666;" value="<?php echo ($data["Qualification"]); ?>"/>
                            </span>
                            <span class="upload-files-area">
                                <p class="uf-choose-files">
                                <input id="id-quaPicSrc-file" type="file" name="quaPicSrc" style="display:none;"/>
                                <a href="javascript:;" class="nor-btn upload-btn"><i class="iconfont">&#xe633;</i>上传文件</a>
                                格式支持JPG、GIF、PNG</p>
                                <ul class="file-list">
                                    <li><span class="file-name link" title="教师资格证"></span><a href="javaScript:;" class="del-btn" title="删除"><i class="iconfont">&#xe632;</i></a></li>
                                </ul>
                            </span>
                        </span>
                        <span class="item-msg"></span>
                    </div>
                    <div class="form-item">
                        <span class="item-tit"><s>*</s>教师等级证编号：</span>
                        <span class="item-content" style="max-width:277px;">
                            <span class="normal-input-wrap">
                                <input id="id-grade-input" name="grade" type="text"  class="normal-input" style="color:#666;" value="<?php echo ($data["Grade"]); ?>"/>
                            </span>
                            <span class="upload-files-area">
                                <p class="uf-choose-files">
                                <input id="id-gradePic-file" type="file" name="gradeImg" style="display:none;"/>
                                <a href="javascript:;" class="nor-btn upload-btn"><i class="iconfont">&#xe633;</i>上传文件</a>
                                格式支持JPG、GIF、PNG</p>
                                <ul class="file-list">
                                    <li><span class="file-name link" title="教师资格证"></span><a href="javaScript:;" class="del-btn" title="删除"><i class="iconfont">&#xe632;</i></a></li>
                                </ul>
                            </span>
                        </span>
                        <span class="item-msg"></span>
                    </div>
                    <div class="form-item">
                        <span class="item-tit"></span>
                        <span class="item-content"><input class="nor-btn" type="button" id="submit01" value="提交申请" /></span>
                        <span class="item-msg"></span>
                    </div>
                </form>
            </div>
            <div class="form-right-bd reg-right-container">
                <div class="rr-container-inner reg-help">
                    <h3 class="title">教师认证审核说明</h3>
                    <div class="rh-context">
                        1、请您确保提交审核资料真实有效；
                        <br />
                        2、证件要求清晰完整，大小不超过2M；
                        <br />
                        3、资料审核时间一般为3个工作日；
                        <br />
                        4、审核期间有任何问题，请及时联系客服人员。
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
    <!--footer-->
    <script type="text/javascript" src="/Public/plugin/jquery.SuperSlide.2.1.1.js"></script>
    <script type="text/javascript" src="/Public/index/js/pingtai.js<?php echo (C("UPDATE_FILE_DATE")); ?>"></script>
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