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
var URL = '/Test/Test';
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
<div class="title"><?php echo ($pageName); ?> [ <A HREF="/Test/Test">返回列表</A> ] [ <A HREF="javascript:history.go(-1);">返回上一页</A> ]</div>
<!-- 功能操作区域结束 -->
<div id="result" class="result none"></div>
<!-- 内容显示区域  -->
<FORM METHOD="POST" action="" id="form1" enctype="multipart/form-data">
<TABLE cellpadding="5" cellspacing="0"  class="list" border="1">
<tr><td height="5" colspan="7" class="topTd" ></td></tr>
<TR>
    <TD class="tRight" width="100">试题预览：</TD>
    <TD class="tLeft"><p>
    <?php echo ((isset($edit["Test"]) && ($edit["Test"] !== ""))?($edit["Test"]):'无'); ?>
    <p><font color="red">【答案】</font><?php echo ((isset($edit["Answer"]) && ($edit["Answer"] !== ""))?($edit["Answer"]):'无</p>'); ?>
    <p><font color="red">【解析】</font><?php echo ((isset($edit["Analytic"]) && ($edit["Analytic"] !== ""))?($edit["Analytic"]):'无</p>'); ?>
    <p><font color="red">【题型】</font><?php echo ((isset($edit["TypesName"]) && ($edit["TypesName"] !== ""))?($edit["TypesName"]):'无'); ?></p>
    <p><font color="red">【备注】</font><?php echo ((isset($edit["Remark"]) && ($edit["Remark"] !== ""))?($edit["Remark"]):'无</p>'); ?>
    </TD>
</TR>
<TR>
    <TD class="tRight" width="100">试题下载编辑：</TD>
    <TD class="tLeft" ><a href="<?php echo U('Test/Test/testdown',array('id'=>$edit[TestID],'style'=>'.docx'));?>" target="_blank" id="down2007">word 2007下载</a></TD>
</TR>
<TR>
    <TD class="tRight" width="100">试题上传：</TD>
    <TD class="tLeft" ><INPUT TYPE="file" class="large bLeft" NAME="photo" /></TD>
</TR>
<TR>
    <TD ></TD>
    <TD class="center"><div style="width:85%;margin:5px">
        <INPUT TYPE="hidden" name="TestID" value="<?php echo ($edit["TestID"]); ?>">
    <div class="impBtn fLeft"><INPUT tag='form1' u="<?php echo U('Test/Test/replace');?>" TYPE="button" value="保存" class="save imgButton mysubmit"></div>
    <div class="impBtn fLeft m-l10"><INPUT TYPE="reset" class="reset imgButton" value="清空" ></div>
    </div></TD>
</TR>
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