<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title><?php echo ($pageName); ?> - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <link type="text/css" href="/Public/default/css/common.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/zjwork.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script>
        var local="<?php echo U('Work/checkWork');?>";
        var key='<?php echo ($key); ?>';
        var classID='<?php echo ($classID); ?>'
    </script>
</head>
<body>
<div id="caseDiv">
    <div id="workBox" class="check-work">
        <div class="w-left class-list">
            <div class="class-tit f-yahei">班级列表</div>
            <div class="load-class">
                <div class="prev" title="向上滚动" onselectstart="return false;" oncontextmenu="return false">向上移动</div>
                <div class="bd"></div>
                <div class="next" title="向下滚动" onselectstart="return false;" oncontextmenu="return false" title="向上滚动">向下移动</div>
            </div>
        </div>
        <div class="w-right">
            <div id="rightTop" class="crumbs-wrap">
                <div id="categorylocation" class="g-crumbs">
                    <span class="now-path">当前位置：</span><span id="local_text"></span> > 作业模块 > 批改导学案
                </div>
            </div>
            <!--批改作业标题-->
            <div class="public-title">
                <h3 class="tit f-yahei">批改导学案</h3>
                <!--
                <div class="add-work-info">
                    <div class="info">
                        导学案流程：
                        <a href="<?php echo U('Index/zj');?>">手工出题</a>
                        （
                        <a href="<?php echo U('Ga/zn');?>">智能组卷</a>
                        ）
                        <cite>></cite>
                        <a href="<?php echo U('Index/zuJuan');?>">试卷预览</a>
                        <cite>></cite>
                        <a href="<?php echo U('Index/zuJuan');?>">创建作业</a>
                        <cite>></cite>
                        <a href="<?php echo U('Work/addWork');?>">分配作业</a>
                        <cite>></cite>
                        <a href="<?php echo U('Work/checkWork');?>">批改作业</a>
                    </div>
                </div>
                -->
            </div>
            <div class="result-filter resultFilter">
                <div id="pagediv">共<a id="quescount">？</a>次作业
                    <a class="prev_page" title="上一页"></a><span id="pagebox" class="tspan">
                <a id="curpage">？</a>
                <a id="selectpageicon" style="display: inline-block;"></a>
                /<a id="pagecount">？</a></span>
                    <a class="next_page" title="下一页"></a></div>
            </div>
            <!--学生作业列表-->
            <div class="mg-jc-li" id="assignedWork"></div>
            <div id="pagelistbox"></div>
        </div>
    </div>
</div>
<!--JS开始-->
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="/Public/plugin/jquery.SuperSlide.2.1.1.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<!--[if lte IE 6]>
<script type="text/javascript" src="/Public/plugin/png.js"></script>
<script>DD_belatedPNG.fix('a,img,div,span');</script>
<![endif]-->
<script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/default/js/paper.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/default/js/workdown.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/default/js/setWork.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/default/js/work.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script>
jQuery(function(){
    var subjectID=Cookie.Get("SubjectId");
    $('#local_text').html(parent.jQuery.myMain.getQuesBank(subjectID)['SubjectName']);
    $.myWorkCheck.init('case');
});
</script>
</body>
</html>