<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
    <title>提示信息</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/index.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <!--[if lte IE 6]>
    <script type="text/javascript" src="/Public/plugin/png.js"></script>
    <script>DD_belatedPNG.fix('input,a,img,div,span');</script>
    <![endif]-->
</head>
<body>
    <div class="header">
    <div class="top">
        <div class="nav">
            <a href="<?php echo U('Index/index');?>">首页</a> | 
            <a target="_blank" href="<?php echo U('Index/About/coreSystem');?>">功能介绍</a> |
            <a target="_blank" href="<?php echo U('Index/About/coreSystemInfo',array('param'=>'flow','csid'=>1));?>">使用手册</a> |
            <a target="_blank" href="<?php echo U('Index/About/about',array('param'=>'aboutOnlineS'));?>">联系我们</a> |
            <a target="_blank" href="<?php echo U('Index/About/videoStudy');?>">帮助中心</a>
        </div>
        <div class="logo" style="width:300px"><?php echo ($config["SiteName"]); ?></div>
    </div>
</div>
    <div class="main">
        <div class="mtop">
            <div class="tsxx">
                <div class="t">
                    <div class="t_nr">
                        <span class="t_nr_text">
                            <span class="<?php echo ($icon); ?>"></span>
                            <?php echo ($msg_detail); ?>
                        </span>
                    </div>
                </div>
                <div class="b">
                    <?php if($auto_redirect): ?><p>
                        您可以选择
                        <a href="<?php echo ($_SERVER['PHP_SELF']); ?>">[重试]</a>
                        <a target="_top" href="<?php echo ($jumpUrl); ?>">[返回]</a>
                        或者
                        <a target="_top" href="<?php echo U('Index/index');?>">[回到首页]</a>
                    </p>
                    <p>
                        将在 <font color="#00a0ea" style="padding:0 5px" id="spanSeconds"><?php echo ($waitSecond); ?></font>
                        秒后自动返回...
                    </p><?php endif; ?>
                </div>

            </div>
        </div>
        <div class="footer">
    <div class="ftop">
        <a target="_blank" href="http://www.tk.com">题库</a> -
        <a target="_blank" href="http://www.tk.com/bottom/gywz.htm">关于我们</a> -
        <!--<a target="_blank" href="#">价格体系</a> --->
        <a href="javascript:void(0)" onclick="AddFavorite('<?php echo ($config["IndexName"]); ?>','http://www.tk.com<?php echo U('Index');?>');">收藏本站</a> -
        <?php echo (C("WLN_STATISTICAL_CODE.HOME")); ?>
    </div>
    <div class="fbottom">
        Copyright © 2000-2017, tk.COM, All Rights Reserved<br/>
        智慧云题库系统V<?php echo (C("WLN_VERSION")); ?> （软著登字第0693456号）<br/>
    </div>
</div>
<script type="text/javascript">
    function AddFavorite(title, url) {
        try {
            window.external.addFavorite(url, title);
        }
        catch (e) {
            try {
                window.sidebar.addPanel(title, url, "");
            }
            catch (e) {
                alert("抱歉，您所使用的浏览器无法完成此操作。\n\n加入收藏失败，请使用Ctrl+D进行添加");
            }
        }
    }
</script>
    </div>
    <?php if($auto_redirect): ?><script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script language="JavaScript">
<!--
var seconds = <?php echo ($waitSecond); ?>;
var defaultUrl = "<?php echo ($jumpUrl); ?>";
var alertWindow='';

onload = function()
{
  if (defaultUrl == 'javascript:history.go(-1)' && window.history.length == 0)
  {
   // document.getElementById('redirectionMsg').innerHTML = '';
    return;
  }

    alertWindow=window.setInterval(redirection, 1000);
}
function redirection()
{
  if (seconds <= 0)
  {
    window.clearInterval(alertWindow);
    return;
  }
  seconds --;
  document.getElementById('spanSeconds').innerHTML = seconds;

  if (seconds == 0)
  {
    window.clearInterval(alertWindow);
    window.parent.location.href = defaultUrl;
  }
}
function InitDivHeight() {
    var mainwidth=$(window).width();
    var mainheight=$(window).height() - 2 - $('.header').height();
    var height = mainheight < 550 ? 550 : mainheight;
    var width=mainwidth < 960 ? 960 : mainwidth;
    var topwidth=(mainwidth > 960 ? 960 : mainwidth) < 600 ? 600 : (mainwidth > 960 ? 960 : mainwidth);
    $(".main").css({ 'height': height ,'width':width});
    $('.top').css({'width':topwidth});
    $('.header').css({'width':width});
    $('.footer').css({'width':topwidth});
}
InitDivHeight();
$(window).bind("resize",function() {InitDivHeight();});
//-->
</script>
    <?php endif; ?>
</body>
</html>