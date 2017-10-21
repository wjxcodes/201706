<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>组卷历史存档</title>
<meta id="keywordsmeta" name="keywords" content="在线组卷系统,存档,组卷记录"/>
<meta id="descriptmeta" name="description" content="在线组卷系统组卷历史存档"/>
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
        <span class="nowPath">当前位置：</span>> <span id="loca_text"><span></span> > 用户档案 > 试题存档 </span>
    </div>
</div>


<div id="divbox" class="sc_list_box">
    <div id="paperinfod" class="list_px tab-list-top clearfix">
        <div class="sc_sx left">
        <a style="border-left:none" class="dated dated_current" id="all">全部</a>
        <a class="dated" id="today">今天</a>
        <a class="dated" id="yestoday">昨天</a>
        <a class="dated" id="curweek">本周</a>
        <a class="dated" id="curmonth">本月</a>
        </div>
        <div id="searchinfo" class="right">
             共存档<a id="quescount">0</a>套卷 <a class="prev_page" title="上一页"></a>当前第
            <a id="curpage">1</a><a id="selectpageicon" style="display: inline-block;"></a>
            /<a id="pagecount">1</a>页 <a class="next_page" title="下一页"></a>
        </div>
    </div>
    <div style="z-index:2;" id="paperlistbox"></div>
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

$.DocSave.init();
</script>

</body>
</html>