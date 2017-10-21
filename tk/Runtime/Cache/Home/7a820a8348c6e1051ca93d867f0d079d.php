<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>演示</title>
</head>
<body style="background-color:#fff;">
<div id="a1"></div>
<script type="text/javascript" src="/Public/plugin/ckplayer/ckplayer.js" charset="utf-8"></script>
<script type="text/javascript">
	var flashvars={
		f:'<?php echo ($url); ?>',
		c:0,
		b:1,
		i:''
		};
	var video=['<?php echo ($url); ?>->video/mp4'];
	CKobject.embed('/Public/plugin/ckplayer/ckplayer.swf','a1','ckplayer_a1','600','400',false,flashvars,video)	
	function closelights(){//关灯
		alert(' 本演示不支持开关灯');
	}
	function openlights(){//开灯
		alert(' 本演示不支持开关灯');
	}
</script>
</body>
</html>