<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo ($pageName); ?> - <?php echo ($config["IndexName"]); ?></title>
    <meta name="keywords" content="公告列表页" />
    <meta name="description" content="公告列表页" />
    <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/index.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
</head>
<body>
<div id="righttop">
    <div id="categorylocation">
        <span class="nowPath">当前位置：</span>
        >
        <span id="loca_text">系统公告 > 公告列表</span>
    </div>
</div>

<div id="divbox" style="overflow-x: hidden;">
    <div class="content_01" id="xtgg" style="margin-bottom:10px;overflow:hidden;overflow-y:auto;">
        <div class="title">
            <span class="fl">系统公告</span>
            <a class="more fr" href="<?php echo U('Home/Index/content');?>">返回首页</a>
        </div>
        <div class="nr_box list_con">
            <?php if(is_array($buffer)): $i = 0; $__LIST__ = $buffer;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><p>
                <a href="<?php echo U('Index/newsCon?id='.$vo['NewID']);?>" style='color:#<?php echo ($vo["Color"]); ?>'><?php echo ($vo["NewTitle"]); ?></a>
                <span><?php echo (date("Y-m-d H:i:s",$vo["LoadDate"])); ?></span>
            </p><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
    <div style="z-index: 1; display: block;" id="pagelistbox">
        <div class="pagebox">
            <span class="disabled">首页</span>
            <span class="current">1</span>
            <span class="disabled">末页</span>
        </div>
    </div>
</div>
<div style="display:none;">
    <iframe id="paperurl" src="#" width="0" height="0" frameborder="0"></iframe>
</div>
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
<script>var local='<?php echo U('Index/main');?>';</script>
<script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script>
    //显示分页
    var page='<?php echo ($page); ?>';
    $('.pagebox a').live('click',function(){
        GotoPage(parseInt($(this).attr('page')));
    });
    function GotoPage(num){
        page=num;
        location.href=U('Index/newsList?page='+page);//获取试题
    }
    function InitDivXdBox(){
        $("#xtgg").height($(window).height()-$('#righttop').height()-$('#pagelistbox').height()-30);
    }
    $(window).resize(function() {
        InitDivXdBox();
    });
    InitDivXdBox();
    $(document).ready(function(){
        $.myPage.showPage('<?php echo ($count); ?>','<?php echo ($pagesize); ?>',page,0);
    });
</script>
<div style="display:none;">
    <?php echo (C("WLN_STATISTICAL_CODE.HOME")); ?>
</div>
</body>
</html>