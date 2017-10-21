<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<TITLE>『<?php echo (C("WLN_WEB_NAME")); ?>管理平台』</TITLE>
<link rel="stylesheet" type="text/css" href="/Public/zjadmin/css/style.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" />
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
<!--基础文件，分别是jQuery基库和拖拽UI插件-->
<script src="/Public/plugin/jquery.ui.draggable.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" type="text/javascript"></script>
<script src="/Public/plugin/testOperation.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" type="text/javascript"></script>
<!-- 对话框核心JS文件和对应的CSS文件-->
<script src="/Public/plugin/alert/jquery.alerts.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" type="text/javascript"></script>
<link href="/Public/plugin/alert/jquery.alerts.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="/Public/plugin/ueditor/ueditor.config.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/ueditor/ueditor.all.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/editor.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script LANGUAGE="JavaScript">
//指定当前组模块URL地址 
var URL = '/Statistics/StatisticsB';
var APP     =     '';
var PUBLIC = '/Public';
var softdog=0;
<?php if(C("openKeysoft")== 1): ?>softdog=1;<?php endif; ?>
</script>
<script type="text/javascript" src="/Public/zjadmin/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
</HEAD>

<body>
<?php if(C("openKeysoft")== 1): ?><div style="display:none;"><embed id="s_simnew31"  type="application/npsyunew3-plugin" hidden="true"> </embed></div><?php endif; ?>
<div id="loader" >页面加载中...</div>
<!-- 主页面开始 -->
<div id="main" class="main" >
    <!-- 主体内容  -->
    <div class="content" >
        <div class="title"><?php echo ($pageName); ?> [ <A HREF="javascript:history.go(-1);">返回上一页</A> ]</div>
        <!--  功能操作区域  -->
        <div class="operate">

            <!-- 功能操作区域结束 -->
            [ <a href="<?php echo U('Statistics/StatisticsB/index');?>">组卷统计</a> ]
            [ <a href="<?php echo U('Statistics/StatisticsB/system');?>">系统统计</a> ]
            [ <a href="<?php echo U('Statistics/StatisticsB/user');?>">用户统计</a> ]
            [ <a href="<?php echo U('Statistics/StatisticsB/school');?>">学校分省使用情况</a> ]
            [ <a href="<?php echo U('Statistics/StatisticsB/classes');?>">班级创建区域</a> ]
            [ <a href="<?php echo U('Statistics/StatisticsB/homeWorkList');?>">作业统计</a> ]
            [ <a href="<?php echo U('Statistics/StatisticsB/areaSchoolUserList');?>">地区学校人数统计</a> ]
            [ <a href="<?php echo U('Statistics/StatisticsB/schoolUseList');?>">学校下载活跃度统计</a> ]
            [ <a href="<?php echo U('Statistics/StatisticsB/adminWork');?>">工作量统计</a> ]
            [ <a href="<?php echo U('Statistics/StatisticsB/userAnswer');?>">答题统计</a> ]
            <!-- Think 系统列表组件结束 -->
        </div>
        <!-- 列表显示区域结束 -->
    </div>
    <!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>