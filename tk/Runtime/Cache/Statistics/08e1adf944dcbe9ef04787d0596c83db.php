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
            <span class="top-nav-item">
                <a class="tn-link" href="<?php echo U('Statistics/StatisticsB/studentWorkList');?>">学生做题统计</a>
            </span>
            <span class="top-nav-item">
                <a class="tn-link" href="<?php echo U('Statistics/StatisticsB/wrongTest');?>">学生错题统计</a>
            </span>
			<span class="top-nav-item">
                <a class="tn-link" href="<?php echo U('Statistics/StatisticsB/teachercheck');?>">教师批改统计</a>
            </span>
			<span class="top-nav-item">
                <a class="tn-link" href="<?php echo U('Statistics/StatisticsB/teacherComment');?>">教师评语统计</a>
            </span>
			<span class="top-nav-item">
                <a class="tn-link" href="<?php echo U('Statistics/StatisticsB/wkvedio');?>">微课(视频)统计</a>
            </span>
        </div>
    </div>
</div>
<!--顶部导航-end-->
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content" >
			
<div class="title"><?php echo ($pageName); ?></div>
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="output" value="导出" onclick="" class="btexport output imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>

    <!-- 查询区域 -->
    <FORM  id="form1" METHOD="POST" ACTION="<?php echo U('Statistics/StatisticsB/index');?>">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="name" value="<?php echo ($_REQUEST['name']); ?>" title="用户名查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR>
            <TD class="tRight" width="60">用户名：</TD>
            <TD><INPUT TYPE="text" NAME="UserName" class="small" value="<?php echo ($_REQUEST['UserName']); ?>" ></TD>
            <TD class="tRight" width="80">真实姓名：</TD>
            <TD ><INPUT TYPE="text" NAME="RealName" class="small" value="<?php echo ($_REQUEST['RealName']); ?>"></TD>
            <TD class="tRight" width="60">地区：</TD>
            <TD><select id="sf" class="w90px selectArea">
                <option value="">选择省份</option>
                <?php if(is_array($arrArea)): $i = 0; $__LIST__ = $arrArea;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><option value="<?php echo ($sub["AreaID"]); ?>" last="<?php echo ($sub["Last"]); ?>"><?php echo ($sub["AreaName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            </SELECT></TD>
            <TD class="tRight" width="45">学校：</TD>
            <TD class="tLeft" width="200" >
            <SELECT class="w90px bLeft" NAME="SchoolID" id='school'>
                <option value="">请选上级</option>
            </SELECT>
            </td>
            <TD class="tRight" width="60">日期：</TD>
            <TD width="350"><INPUT TYPE="text" NAME="Start" class="medium inputTime" value="<?php echo ($_REQUEST['Start']); ?>"> - 
            <INPUT TYPE="text" NAME="End" class="medium inputTime" value="<?php echo ($_REQUEST['End']); ?>">
            </TD>
        </TR>
        </TABLE>
    </div>
        </FORM>
</div>
<script>
$(document).ready(function(){
    var areaParent="<?php echo ($areaParent); ?>";
    $('.selectArea').areaSelectChange("/Statistics/StatisticsB",1);
    if("<?php echo ($act); ?>"=="edit"){
        $('#sf').areaSelectLoad('/Statistics/StatisticsB',areaParent);
    }
});
</script>
<!-- 功能操作区域结束 -->

<!-- 列表显示区域  -->
<div class="list" >
<div id="result" class="result none"></div>
<!-- Think 系统列表组件开始 -->
<table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
    <tr><td height="5" colspan="14" class="topTd" ></td></tr>
    <tr class="row tCenter" >
        <th width="8%">用户</th>
        
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists tCenter" jl=''>
        <td><?php echo ($node["UserName"]); ?></td>
        
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="14" class="bottomTd"></td></tr>
</table>
<!-- Think 系统列表组件结束 -->
</div>
<!--  分页显示区域 -->
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>