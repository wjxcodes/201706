<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>试题反馈</title>
<meta id="keywordsmeta" name="keywords" content="在线组卷系统,试题反馈"/>
<meta id="descriptmeta" name="description" content="在线组卷系统组卷试题反馈"/>
    <link type="text/css" href="/Public/default/css/common.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/user.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script>
    var local='<?php echo U('Index/main');?>';
    </script>
    <script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/paper.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
</head>
<body>
<div id="righttop">
        <div id="categorylocation">
            <span class="nowPath">当前位置：</span>> <span id="loca_text"><span></span> > 用户档案 > 试题反馈 </span>
        </div>
</div>

<div id="divbox" class="sc_list_box">
    <div id="paperinfod" class="list_px clearfix tab-list-top">
        <div class="sc_sx">
        <a class="dated " id="mycomment">我发布的试题反馈</a>
        <a class="dated dated_current" id="allcomment">全部的试题反馈</a>
        </div>
        <div id="searchinfo" class="right">
             共评论<a id="quescount">0</a>条 <a class="prev_page" title="上一页"></a>当前第
            <a id="curpage">1</a><a id="selectpageicon" style="display: inline-block;"></a>
            /<a id="pagecount">1</a>页 <a class="next_page" title="下一页"></a>
        </div>
    </div>
    <div id="commentbox" style="margin:0px 6px; background:#fff; border:#ddd solid 1px; border-top:none"></div>
    <div style="z-index: 1; display: block;" id="pagelistbox">
        <div class="pagebox">
            <span class="disabled">首页</span>
            <span class="current">1</span>
            <span class="disabled">末页</span>
        </div>
    </div>
</div>
<script type="text/javascript" src="/Public/default/js/user.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript">


$.UserMessage.init();

</script>

</body>
</html>