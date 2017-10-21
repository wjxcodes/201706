<?php if (!defined('THINK_PATH')) exit();?>
<!--顶部导航-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="zh-CN">
<head>
    <title>教学统计</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <meta property="qc:admins" content="167741560767461006375" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <link type="text/css" href="/Public/index/css/wln-base.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/index/css/index.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link rel="stylesheet" href="/Public/index/css/style.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"/>
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
</head>
<div class="top-logo-wrap w1000">
    <a class="top-logo" href="javascript:void(0)">
        <img src="/Public/index/imgs/publ/logo.png" alt="logo"/>
    </a>
</div>
<div class="top-nav-fixed">
    <div class="top-nav-wrap">
        <div class="top-nav w1000">
			<span>
            <a class="top-nav-item" href="<?php echo U('Statistics/Index/index');?>">教师列表</a>
			</span>
			<span>
            <a class="top-nav-item" href="<?php echo U('Statistics/StatisticsB/Teacherzj');?>">教师组卷下载统计</a>
			</span>
            <!-- <span class="top-nav-item"> -->
                <!-- <a class="tn-link" href="<?php echo U('Statistics/StatisticsB/studentWorkList');?>">学生做题统计</a> -->
            <!-- </span> -->
            <!-- <span class="top-nav-item"> -->
                <!-- <a class="tn-link" href="<?php echo U('Statistics/StatisticsB/wrongTest');?>">学生错题统计</a> -->
            <!-- </span> -->
			<!-- <span class="top-nav-item"> -->
                <!-- <a class="tn-link" href="<?php echo U('Statistics/StatisticsB/teachercheck');?>">教师批改统计</a> -->
            <!-- </span> -->
			<!-- <span class="top-nav-item"> -->
                <!-- <a class="tn-link" href="<?php echo U('Statistics/StatisticsB/teacherComment');?>">教师评语统计</a> -->
            <!-- </span> -->
			<!-- <span class="top-nav-item"> -->
                <!-- <a class="tn-link" href="<?php echo U('Statistics/StatisticsB/wkvedio');?>">微课(视频)统计</a> -->
            <!-- </span> -->
        </div>
    </div>
</div>
<!--顶部导航-end-->
<!-- 主页面开始 -->
<div id="main" class="main" >
    <!-- 主体内容  -->
    <div class="content" >
        <div class="title"><?php echo ($pageName); ?>[ <a href="javascript:history.go(-1);">返回上一页</a> ]</div>
        <!-- 列表显示区域  -->
        <div class="list" >
            <div id="result" class="result none"></div>
            <!-- Think 系统列表组件开始 -->
            <table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
                <tr><td height="5" colspan="8" class="topTd" ></td></tr>
                <tr class="row" >
                    <th>编号</th>
                    <th>班级</th>
                    <th>提交人</th>
                    <th>提交时间</th>
                    <th>作答时间</th>
                    <th>正确率</th>
					<th>教师评价</th>
                    <th>查看</th>
                </tr>
                <?php if(is_array($workInfoList)): $i = 0; $__LIST__ = $workInfoList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
                    <td><?php echo ($key+1); ?></td>
                    <td><?php echo ($node["ClassName"]); ?></td>
                    <td><?php echo ($node["RealName"]); ?></td>
                    <td><?php echo ($node["SendTime"]); ?></td>
                    <td><?php echo ($node["DoTime"]); ?></td>
                    <td><?php echo ($node["CorrectRate"]); ?></td>
					<td><?php echo ($node["Comment"]); ?></td>
                    <td><a href="<?php echo U('Statistics/StatisticsB/workAnswerInfo',array('sid'=>$node['SendID']));?>">批改情况详情</a></td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <tr><td height="5" colspan="8" class="bottomTd"></td></tr>
            </table>
            <!-- Think 系统列表组件结束 -->
        </div>
        <!-- 列表显示区域结束 -->
    </div>
    <!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>