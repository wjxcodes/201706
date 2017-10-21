<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title><?php echo ($pageName); ?> - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/work.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script>
        var local='<?php echo U('Work/addWork');?>';
    </script>
</head>
<body>
<div id="workDiv">
    <div id="workBox" class="PublicBox">
        <div id="rightTop">
            <div id="categorylocation">
                <span class="newPath">当前位置：</span>> <span id="loca_text"><span></span> > 高效同步课堂 > 分发导学案</span>
            </div>
        </div>
        <div class="PublicBoxnr">
            <div class="Publictitle">
                <h3 class="addTit">分发导学案</h3>
                <div class="addDes">
                    <a href="<?php echo U('Guide/Case/index');?>" class="selectright">创建导学案</a>
                </div>
            </div>
            <div class="workHistory">
            </div>
            <div id="pagelistbox">
                <div class="pagebox">
                    <span class="disabled">首页</span>
                    <span class="current">1</span>
                    <span class="disabled">末页</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!--JS部分-->
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/default/js/case.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>

<!--[if lte IE 6]>
<script type="text/javascript" src="/Public/plugin/png.js"></script>
<script>DD_belatedPNG.fix('a,img,div,span');</script>
<![endif]-->

<script type="text/javascript" src="/Public/default/js/setWork.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/default/js/work.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script>
    jQuery(function($){
        var subjectID=Cookie.Get("SubjectId");
        $.myWork.init('case');
        $('#loca_text span').html(parent.jQuery.myMain.getQuesBank(subjectID)['SubjectName']);
    });
</script>
</body>
</html>