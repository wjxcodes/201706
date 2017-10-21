<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<TITLE>『<?php echo (C("WLN_WEB_NAME")); ?>管理平台』</TITLE>
<link rel="stylesheet" type="text/css" href="/Public/teacher/css/style.css" />
<base target="main" />
</HEAD>
<body>
<!-- 头部区域 -->
<div id="header" class="header">
<div class="headTitle" style="margin:8pt 10pt"> <?php echo (C("WLN_WEB_NAME")); ?> 管理平台 </div>
    <!-- 功能导航区 -->
    <div class="topmenu">
<ul>
<li><span><a href="<?php echo U('Teacher/Public/main');?>">后台首页</a></span></li>
<li><span><a href="/" target="_blank">前台首页</a></span></li>
</ul>
</div>
    <div class="nav">
    欢迎你！<?php echo (cookie('wln_teacher_USER')); ?> <A HREF="/" target="_blank">首页</A>
    <A HREF="<?php echo U('Teacher/Public/password');?>"><img src="/Public/teacher/images/checked_out.png" width="16" height="16" border="0" alt="" align="absmiddle"> 修改个人信息</A><A HREF="<?php echo U('Public/myPower');?>"><img src="/Public/zjadmin/images/access.gif" width="16" height="16" border="0" alt="" align="absmiddle"> 我的权限</A> <A HREF="<?php echo U('Teacher/Public/logout');?>" target="_top"><IMG SRC="/Public/teacher/images/error.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="" align="absmiddle"> 退 出</A></div>
</div>
<script>
function sethighlight(n) {
    var lis = document.getElementsByTagName('span');
    for(var i = 0; i < lis.length; i++) {
        lis[i].className = '';
    }
    lis[n].className = 'current';
}
sethighlight(0);
</script>
</body>
</html>