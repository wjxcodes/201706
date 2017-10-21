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
var URL = '/Guide/CaseAnnounce';
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
<div class="title"><?php echo ($pageName); ?> [ <a href="<?php echo U('CaseAnnounce/index');?>">返回列表</a> ]</div>

<!-- 列表显示区域  -->
<div class="list" >
<div id="result" class="result none"></div>
<!-- Think 系统列表组件开始 -->
<FORM METHOD="POST" action="" id="form1" >
<table class="list" cellpadding="5" cellspacing="0" border="1">
<tr><td height="5" colspan="4" class="topTd" ></td></tr>
<tr><td width="113">编号</td><td><?php echo ($edit["WorkID"]); ?></td><td width="113">导学案发布名称</td><td><?php echo ($edit["WorkName"]); ?></td></tr>
<tr><td>学科</td><td><?php echo ($edit["SubjectName"]); ?></td><td>作答方式</td><td><?php if($edit["WorkStyle"] == 0): ?>在线作答<?php else: ?>下载作答<?php endif; ?></td></tr>
<tr><td>发布人</td><td><?php echo ($edit["UserName"]); ?></td><td>能否删除</td><td><?php if(($$edit["IfDelete"]) == "0"): ?>可以删除<?php else: ?>不能删除<?php endif; ?></td></tr>
<tr><td>发布时间</td><td><?php echo (date("Y-m-d H:i:s",$edit["LoadTime"])); ?></td><td>作答时间</td><td><?php echo (date("Y-m-d H:i:s",$edit["StartTime"])); ?>--<?php echo (date("Y-m-d H:i:s",$edit["EndTime"])); ?></td></tr>
<tr><td>布置作业留言</td><td><?php echo ($edit["Message"]); ?></td>
</tr>
<tr><td height="5" colspan="4" class="bottomTd" ></td></tr>
</table>
<INPUT TYPE="hidden" name="WorkID" value="<?php echo ($edit["WorkID"]); ?>">
</FORM>

<table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
    <tr><td height="5" colspan="5" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="5%">编号</th>
        <th>所属板块</th>
        <th>所属栏目</th>
        <th>内容</th>
    </tr>
    <?php if(is_array($forumArray)): $i = 0; $__LIST__ = $forumArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
            <td><?php echo ($node["Order"]); ?></td>
            <td><?php echo ($node["forum"]); ?></td>
            <td>
                <?php echo ($node["MenuName"]); ?>
            </td>
            <td>
                <?php echo ($node["ContentList"]); ?>
            </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="5" class="bottomTd"></td></tr>
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