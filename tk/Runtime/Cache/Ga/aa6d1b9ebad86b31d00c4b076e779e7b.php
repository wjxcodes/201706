<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<title>错误信息</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/Public/zjadmin/css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="message">
<TABLE class="error" cellpadding=0 cellspacing=0 >
    <tr>
        <td height='5' class="topTd" ></td>
    </tr>
    <TR class="row" >
        <th  class="tCenter red"><IMG SRC='/Public/zjadmin/images/update.gif' class='img' align='absmiddle' BORDER='0'> <?php echo ($msg_detail); ?></th>
    </TR>
    <tr>
        <td height='5'  class="topTd" ></td>
    </tr>
    <tr>
        <td class="tCenter row" >
        您可以选择 [ <A HREF="javascript:location.reload();">重试</A> ] [ <A HREF="<?php echo ($jumpUrl); ?>">返回</A> ] 或者 [ <A HREF="/Ga" target="_top">回到首页</A> ]</td>
    </tr>
    <tr>
        <td class="tCenter row" >
        <?php if($auto_redirect): ?>如果您不做出选择，将在 <span id="spanSeconds"><?php echo ($waitSecond); ?></span> 秒后返回。<?php endif; ?>
        </td>
    </tr>
    <tr>
        <td height='5'  class="bottomTd"></td>
    </tr>
</TABLE>
</div>
<?php if($auto_redirect): ?><script language="JavaScript">
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

    intv=window.setInterval(redirection, 1000);
}
function redirection()
{
  if (seconds <= 0)
  {
    window.clearInterval(intv);
    return;
  }
  seconds --;
  document.getElementById('spanSeconds').innerHTML = seconds;

  if (seconds == 0)
  {
    window.clearInterval(intv);
    location.href = defaultUrl;
  }
}
//-->
</script>
<?php endif; ?>
</body>
</html>