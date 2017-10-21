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
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content">
<!--  功能操作区域  -->
<div class="title"><?php echo ($pageName); ?> </div>
<!-- 功能操作区域结束 -->
<div id="result" class="result none"></div>
<!-- 内容显示区域  -->
<FORM METHOD="POST" action="" id="form1" >
<TABLE cellpadding="5" cellspacing="0"  class="list" border="1">
<tr><td height="5" colspan="7" class="topTd" ></td></tr>
<TR>
    <TD class="tRight" width="100">用户名称：</TD>
    <TD class="tLeft" >
        <?php echo ($edit["UserName"]); ?>
    </TD>
</TR>
<TR>
    <TD class="tRight" >原密码：</TD>
    <TD class="tLeft" ><INPUT TYPE="password" class="bLeft" NAME="Passwordy" value="" />* 如果不修改请留空</TD>
</TR>
<TR>
    <TD class="tRight" >密码：</TD>
    <TD class="tLeft" ><INPUT TYPE="password" class="bLeft" NAME="Password" value="" /><?php if(isset($edit["AdminID"])): ?>* 如果不修改请留空<?php endif; ?></TD>
</TR>
<TR>
    <TD class="tRight" >重复密码：</TD>
    <TD class="tLeft" ><INPUT TYPE="password" class="bLeft"  NAME="Password2" value="" /><?php if(isset($edit["AdminID"])): ?>* 如果不修改请留空<?php endif; ?> </TD>
</TR>
<TR>
    <TD class="tRight tTop">真实姓名：</TD>
    <TD class="tLeft"><INPUT TYPE="text" class="large bLeft"  check='Require' warning="真实姓名不能为空" NAME="RealName" value="<?php echo ((isset($edit["RealName"]) && ($edit["RealName"] !== ""))?($edit["RealName"]):''); ?>"></TD>
</TR>
<TR>
    <TD class="tRight tTop">性别：</TD>
    <TD class="tLeft">
        <label><INPUT TYPE="radio" class="bLeft"  NAME="Sex" value="0" <?php if($edit["Sex"] == 0): ?>checked="checked"<?php endif; ?> /> 保密</label> 
        <label><INPUT TYPE="radio" class="bLeft"  NAME="Sex" value="1" <?php if($edit["Sex"] == 1): ?>checked="checked"<?php endif; ?> /> 女</label> 
        <label><INPUT TYPE="radio" class="bLeft"  NAME="Sex" value="2" <?php if($edit["Sex"] == 2): ?>checked="checked"<?php endif; ?> /> 男</label>
    </TD>
</TR>
<TR>
    <TD class="tRight tTop">手机：</TD>
    <TD class="tLeft"><INPUT TYPE="text" class="large bLeft" NAME="Phonecode" value="<?php echo ((isset($edit["Phonecode"]) && ($edit["Phonecode"] !== ""))?($edit["Phonecode"]):''); ?>"></TD>
</TR>
<TR>
    <TD class="tRight tTop">地址：</TD>
    <TD class="tLeft"><INPUT TYPE="text" class="large bLeft" NAME="Address" value="<?php echo ((isset($edit["Address"]) && ($edit["Address"] !== ""))?($edit["Address"]):''); ?>"></TD>
</TR>
<TR>
    <TD class="tRight tTop">email：</TD>
    <TD class="tLeft"><INPUT TYPE="text" class="large bLeft"  check='email' warning="email格式不正确" NAME="Email" value="<?php echo ((isset($edit["Email"]) && ($edit["Email"] !== ""))?($edit["Email"]):''); ?>"></TD>
</TR>
<TR>
    <TD class="tRight tTop">邮编：</TD>
    <TD class="tLeft"><INPUT TYPE="text" class="large bLeft"  NAME="PostCode" value="<?php echo ((isset($edit["PostCode"]) && ($edit["PostCode"] !== ""))?($edit["PostCode"]):''); ?>"></TD>
</TR>
<TR>
    <TD ></TD>
    <TD class="center"><div style="width:85%;margin:5px">
        <INPUT TYPE="hidden" name="AdminID" value="<?php echo ($edit["AdminID"]); ?>">
    <div class="impBtn fLeft"><INPUT tag='form1' u='<?php echo U('Teacher/Public/password');?>' TYPE="button" value="保存" class="save imgButton mysubmit"></div>
    <div class="impBtn fLeft m-l10"><INPUT TYPE="reset" class="reset imgButton" value="清空" ></div>
    </div></TD>
</TR>
<tr><td height="5" colspan="7" class="bottomTd" ></td></tr>
</TABLE>
</FORM>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>