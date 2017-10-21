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
var URL = '/Work/ClassUser';
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
<div class="content">
<!--  功能操作区域  -->
<div class="title"><?php echo ($pageName); ?> [ <A HREF="<?php echo U('ClassUser/index');?>">返回列表</A> ]</div>
<!-- 功能操作区域结束 -->
<div id="result" class="result none"></div>
<!-- 内容显示区域  -->
<FORM METHOD="POST" action="" id="form1" >
<TABLE cellpadding=5 cellspacing=0 class="list" border="1">
<tr><td height="5" colspan="7" class="topTd" ></td></tr>
<TR>
    <TD class="tRight" width="100">账户：</TD>
    <TD class="tLeft" ><?php echo ((isset($subjectSelfArr["UserName"]) && ($subjectSelfArr["UserName"] !== ""))?($subjectSelfArr["UserName"]):''); ?></TD>
</TR>
<TR>
    <TD class="tRight" width="100">所在年级：</TD>
    <TD class="tLeft" ><?php echo ((isset($subjectSelfArr["GradeName"]) && ($subjectSelfArr["GradeName"] !== ""))?($subjectSelfArr["GradeName"]):''); ?></TD>
</TR>
<TR>
    <TD class="tRight" width="100">所在班级：</TD>
    <TD class="tLeft" ><?php echo ((isset($subjectSelfArr["ClassName"]) && ($subjectSelfArr["ClassName"] !== ""))?($subjectSelfArr["ClassName"]):''); ?></TD>
</TR>
<TR>
    <TD class="tRight" width="100">目前所在学科：</TD>
    <TD class="tLeft" ><input type='text' name='nowSubjectName' value='<?php echo ((isset($subjectSelfArr["SubjectName"]) && ($subjectSelfArr["SubjectName"] !== ""))?($subjectSelfArr["SubjectName"]):''); ?>' disabled></TD>
</TR>

<TR>
    <TD class="tRight" width="100">可选学科：</TD>
    <TD class="tLeft" >
       <select name='SubjectID'>
        <?php if(is_array($subArr)): $i = 0; $__LIST__ = $subArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value='<?php echo ($vo["SubjectID"]); ?>'><?php echo ($vo["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
       </select>
    </TD>
</TR>
<tr>
    <td></td>
    <td>
        <INPUT TYPE="hidden" name="CUID" value="<?php echo ($cuID); ?>">
        <INPUT TYPE="hidden" name="nowSubjectID" value="<?php echo ((isset($subjectSelfArr["SubjectID"]) && ($subjectSelfArr["SubjectID"] !== ""))?($subjectSelfArr["SubjectID"]):''); ?>">
        <INPUT TYPE="hidden" name="act" value="<?php echo ($act); ?>">       
    <div class="impBtn fLeft"><INPUT tag='form1' u="<?php echo U('ClassUser/save');?>" TYPE="button" value="保存" class="save imgButton mysubmit"></div>
    <div class="impBtn fLeft m-l10"><INPUT TYPE="reset" class="reset imgButton" value="清空" ></div>
    </td>
</tr>
<tr><td height="5" colspan="7" class="bottomTd" ></td></tr>
</TABLE>
</FORM>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>