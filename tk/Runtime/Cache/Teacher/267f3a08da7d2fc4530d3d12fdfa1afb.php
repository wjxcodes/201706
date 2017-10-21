<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<TITLE>『<?php echo (C("WLN_WEB_NAME")); ?>管理平台』</TITLE>
<link rel="stylesheet" type="text/css" href="/Public/teacher/css/style.css" />
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="/Public/plugin/tree1.js"></script>
<style>
html{overflow-x : hidden;}
ul{margin:0px;padding:0px;}
</style>
<base target="main" />
<script language='javascript'>
$(function(){
    $('#files').tree({
        expanded: 'li:first'
    });
});
</script>
</HEAD>
<body>
<div id="menu" class="menu">
<TABLE class="list shadow" cellpadding="5" cellspacing="0" border="1">
<tr>
    <td height='5' colspan=7 class="topTd" ></td>
</tr>
<TR class="row" >
    <th class="tCenter space"><IMG SRC="/Public/teacher/images/home.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="" align="absmiddle"> <?php if(isset($_GET['title'])): echo ($_GET['title']); endif; if(!isset($_GET['title'])): ?>后台首页<?php endif; ?> </th>
</TR>
<TR>
    <TD><table border='0'><tr><td>
<ul id="files">
<?php if($user["DocManager"] == '1'): ?><li><A HREF="#" title="解析任务">解析任务</A>
    <ul>
        <li><a href="<?php echo U('Teacher/DocManager/index');?>" title="文档管理">文档管理</a></li>
    </ul>
</li><?php endif; ?>
<?php if($user["IfTeacher"] == '1'): ?><li><A HREF="#" title="标引试题">标引试题</A>
    <ul>
        <li><a href="<?php echo U('Teacher/Task/index', array('Status'=>0));?>" title="未完成任务">未完成任务</a></li>
        <li><a href="<?php echo U('Teacher/Task/index', array('Status'=>1));?>" title="待审核任务">待审核任务</a></li>
        <li><a href="<?php echo U('Teacher/Task/index', array('Status'=>2));?>" title="已完成任务">已完成任务</a></li>
        <li><a href="<?php echo U('Teacher/Task/index', array('Status'=>3));?>" title="失败任务">失败任务</a></li>
    </ul>
</li><?php endif; ?>
<?php if($user["IfChecker"] == '1'): ?><li><A HREF="#" title="审核试题">审核试题</A>
    <ul>
        <li><a href="<?php echo U('Teacher/Check/index', array('Status'=>0));?>" title="未完成任务">未完成任务</a></li>
        <li><a href="<?php echo U('Teacher/Check/index', array('Status'=>1));?>" title="待审核任务">已提交任务</a></li>
        <li><a href="<?php echo U('Teacher/Check/index', array('Status'=>2));?>" title="已完成任务">已完成任务</a></li>
    </ul>
</li><?php endif; ?>
<?php if($user["Custom"] == 1): ?><li>
    <a href="#" title="校本题库">校本题库</a>
    <ul>
        <?php if($user["CustomIntro"] == 1): ?><li><a href="<?php echo U('Teacher/CustomIntro/taskTestList');?>">任务库</a></li>
        <li><a href="<?php echo U('Teacher/CustomIntro/individualTestList');?>">已领任务</a></li><?php endif; ?>
        <?php if($user["CustomCheck"] == 1): ?><li><a href="<?php echo U('Teacher/CustomCheck/index');?>" title="审核任务列表">审核任务列表</a></li><?php endif; ?>
        <li><a href="<?php echo U('Teacher/CustomTestLog/index');?>" title="日志列表">日志列表</a></li>
    </ul>
</li><?php endif; ?>
<li><A href="#" title="个人信息管理">个人信息管理</A>
    <ul>
        <li><a href="<?php echo U('Teacher/Public/password');?>" title="修改信息">修改信息或密码</a></li>
    </ul>
</li>
</ul></td></tr></table></td>
</TR>
<tr>
    <td height='5' colspan=7 class="bottomTd"></td>
</tr>
</TABLE>
</div>
</body>
</html>