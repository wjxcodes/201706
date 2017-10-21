<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<title>页面提示</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href ="/Public/zjadmin/css/style.css" />
</head>
<body>
<div class="message">
<TABLE class="message"  cellpadding=0 cellspacing=0 >
    <tr>
        <td height='5'  class="topTd" ></td>
    </tr>
    <TR class="row" >
        <th class="tCenter space"><?php echo ($msgTitle); ?></th>
    </TR>
    <TR class="row">
        <TD style="color:red"><?php echo ($message); ?></TD>
    </TR>
    <TR class="row">
        <TD>系统将在 <span id="spanSeconds" style="color:blue;font-weight:bold"><?php echo ($waitSecond); ?></span> 秒后自动跳转,如果不想等待,直接点击 <A HREF="<?php echo ($jumpUrl); ?>">这里</A> 跳转</TD>
    </TR>
    <tr>
        <td height='5' class="bottomTd"></td>
    </tr>
    </TABLE>
</div>
<script language="JavaScript">
<!--
var seconds = <?php echo ($waitSecond); ?>;
var defaultUrl = "<?php echo ($jumpUrl); ?>";
var intv;


onload = function()
{
  if (defaultUrl == 'javascript:history.go(-1)' && window.history.length == 0)
  {
    document.getElementById('redirectionMsg').innerHTML = '';
    return;
  }

    intv=setInterval(redirection, 1000);
}
function redirection()
{
  if (seconds <= 0)
  {
    clearInterval(intv);
    return;
  }
  seconds--;
  document.getElementById('spanSeconds').innerHTML = seconds;

  if (seconds == 0)
  {
    clearInterval(intv);
    location.href = defaultUrl;
  }
}
//-->
</script>

</body>
</html>