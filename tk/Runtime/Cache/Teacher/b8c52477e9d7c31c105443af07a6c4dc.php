<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<TITLE>『<?php echo (C("WLN_WEB_NAME")); ?>管理平台』</TITLE>
<link rel="stylesheet" type="text/css" href="/Public/teacher/css/style.css" />
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="/Public/plugin/jquery.newplaceholder.js"></script>
<script type="text/javascript" src="/Public/teacher/js/common1.js"></script>
<script type="text/javascript" src="/Public/teacher/js/common.js"></script>

<SCRIPT LANGUAGE="JavaScript">
//指定当前组模块URL地址 
var URL = '/Teacher/Public';
var APP     =     '';
var PUBLIC = '/Public';
</SCRIPT>
</HEAD>

<body>
<div id="loader" >页面加载中...</div>
<!-- 菜单区域  -->
<SCRIPT LANGUAGE="JavaScript">
<!--

//-->
</SCRIPT>
<div class="main" >
<div class="content">
<TABLE id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
<tr><td height="5" colspan="2" class="topTd" ></td></tr>
<TR class="row" ><th colspan="2" class="space">欢迎信息</th></tr>
<?php if($DocManager == '1'): ?><TR class="row lists" jl=''><TD colspan="2" width="15%">欢迎登录·试题属性编辑系统·，请在 <a href='<?php echo U('Teacher/DocManager/index');?>'>文档管理</a> 中对试题进行编辑，在您确认无误后提交任务！</TD></TR>
    <TR class="row lists" jl=''><TD width="15%">您有未下载的解析任务：</td><td><font color="red"><?php echo ($docfile); ?>个</font></td></tr><?php endif; ?>
<?php if($IfTeacher == '1'): ?><TR class="row lists" jl=''><TD colspan="2" width="15%">欢迎登录·试题属性编辑系统·，请在 <a href='<?php echo U('Teacher/Task/index');?>'>未完成标引任务</a> 中对试题进行编辑，在您确认无误后提交任务！</TD></TR>
<TR class="row lists" jl=''><TD width="15%">您有未完成标引任务：</td><td><font color="red"><?php echo ($data[0]['a']); ?>个</font></td></tr>
<TR class="row lists" jl=''><TD width="15%">您有待审核标引任务：</td><td><?php echo ($data[0]['b']); ?>个</td></tr>
<TR class="row lists" jl=''><TD width="15%">您有已完成标引任务：</td><td><?php echo ($data[0]['c']); ?>个</td></tr>
<TR class="row lists" jl=''><TD width="15%">您有失败标引任务：</td><td><font color="red"><?php echo ($data[0]['d']); ?>个</font></td></tr><?php endif; ?>

<?php if($IfChecker == '1'): ?><TR class="row lists" jl=''><TD colspan="2" width="15%">欢迎登录·试题属性编辑系统·，请在 <a href='<?php echo U('Teacher/Check/index');?>'>未完成审核任务</a> 中对试题进行审核，在您确认无误后提交任务！</TD></TR>
<TR class="row lists" jl=''><TD width="15%">您有未完成审核任务：</td><td><font color="red"><?php echo ($data[1]['a']); ?>个</font></td></tr>
<TR class="row lists" jl=''><TD width="15%">您有已提交审核任务：</td><td><?php echo ($data[1]['b']); ?>个</td></tr>
<TR class="row lists" jl=''><TD width="15%">您有已完成审核任务：</td><td><?php echo ($data[1]['c']); ?>个</td></tr><?php endif; ?>

<tr><td height="5" colspan="2" class="bottomTd"></td></tr>
</TABLE>
</div>
</div> 
<!-- 主页面结束 -->

</body>
</html>