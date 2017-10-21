<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title><?php echo ($pageName); ?> - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/manTopic.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/Public/plugin/tree.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <!--[if lte IE 6]>
    <script type="text/javascript" src="/Public/plugin/png.js"></script>
    <script>DD_belatedPNG.fix('a,img,div,span');</script>
    <![endif]-->
    <script>
        var mark='chapter';
        var local='<?php echo U('Index/main');?>';
    </script>
    <script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/paper.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>

</head>
<body>
<div id="main">
    <div id="leftdiv" class="ui-resizable">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
            <tbody>
            <tr>
                <td valign="top" style="border:0;">
                    <div id="category">
                        <div id="categorytop">
                            <div class="tit">当前教材：</div>
                        </div>
                        <div id="categorytreebox">
                            <div id="treeContainer">
                                <ul id="treecon"><p class="list_ts" style="color:#fff">数据加载中请稍候...</p>
                                </ul>
                            </div>
                        </div>
                    </div>
                </td>
                <td id="bar"></td>
            </tr>
            </tbody>
        </table>
        <div class="ui-resizable-handle ui-resizable-e"/>
    </div>
    <div id="rightdiv" style="overflow-y: auto; width: 290px; height: 291px;">
        <div id="righttop">
            <div id="categorylocation"><span class="nowPath">当前位置：</span>> <span id="loca_text"></span></div>
        </div>
        <div id="filterbox"></div>
        <div id="list_px"><span>排序：</span><a href="#" class="button button_current" type="0">默认</a><a href="#" type="pdown" class="button">人气<b></b></a><a href="#" type="ddown" class="button">难易度<b></b></a><a href="#" type="tdown" class="button">上传时间<b></b></a>
            <div id="pagediv">共<a id="quescount"></a>道题
                <a class="prev_page" title="上一页"></a><span id="pagebox" class="tspan">
                <a id="curpage"></a>
                <a id="selectpageicon" style="display: inline-block;"></a>
                /<a id="pagecount"></a></span>
                <a class="next_page" title="下一页"></a>
            </div>
        </div>
        <div id="ability"></div>
        <div id="queslistbox"><p class="list_ts"><span class="ico_dd"></span>&nbsp;&nbsp;&nbsp;&nbsp;试题加载中...</p></div>
        <div id="pagelistbox"></div>
    </div>
</div>
<script type="text/javascript" src="/Public/default/js/manual.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<div style="display:none;">
    <?php echo (C("WLN_STATISTICAL_CODE.HOME")); ?>
</div>
</body>
</html>