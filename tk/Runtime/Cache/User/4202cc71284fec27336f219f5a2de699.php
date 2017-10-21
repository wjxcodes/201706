<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>收藏夹</title>
    <meta id="keywordsmeta" name="keywords" content="在线组卷系统,收藏,收藏记录"/>
    <meta id="descriptmeta" name="description" content="在线组卷系统试题收藏记录"/>

    <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/user.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script>
        var local='<?php echo U('Index/main');?>';
    </script>
    <script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/paper.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/tree.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <!--[if lte IE 6]>
    <script type="text/javascript" src="/Public/plugin/png.js"></script>
    <script>DD_belatedPNG.fix('a,img,div,span');</script>
    <![endif]-->
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
                                <div class="tit">收藏目录</div>
                                <span class="deletecata" title="删除目录">X</span>
                                <span class="editcatalog" title="重命名">✎</span>
                                <span class="addcatalog" title="添加目录">+</span>
                            </div>
                            <div id="categorytreebox">
                                <div id="treeContainer">
                                    <ul id="treecon">
                                        <p class="list_ts" style="color:#fff">数据加载中请稍候...</p>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td id="bar"/>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="ui-resizable-handle ui-resizable-e">
    </div>
    <div id="rightdiv" style="overflow-y: auto; width: 290px; height: 291px;">
        <div id="righttop">
            <div id="categorylocation">
                <span class="nowPath">当前位置：</span>> <span id="loca_text"><span></span> > 用户档案 > 试题收藏夹 </span>
            </div>
        </div>
        <div id="divbox" class="sc_list_box">
            <div id="paperinfod" class="list_px favl-list-top clearfix">
                <div id="searchinfo" class="right">
                                                    共收藏<a id="quescount">0</a>道题 <a class="prev_page" title="上一页"></a>当前第
                    <a id="curpage">1</a><a id="selectpageicon" style="display: inline-block;"></a>
                    /<a id="pagecount">1</a>页 <a class="next_page" title="下一页"></a>
                </div>
            </div>
            <div style="z-index:2;overflow-y:auto;" id="paperlistbox" class="favl"></div>
            <div style="z-index: 1; display: block;" id="pagelistbox">
                <div class="pagebox">
                    <span class="disabled">首页</span>
                    <span class="current">1</span>
                    <span class="disabled">末页</span>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/Public/default/js/user.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript">
$.TestSave.init();
</script>

</body>
</html>