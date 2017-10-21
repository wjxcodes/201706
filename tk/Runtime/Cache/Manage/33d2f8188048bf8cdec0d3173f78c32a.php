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
var URL = '/Manage/Menu';
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
<div class="title"><?php echo ($pageName); ?> [ <A HREF="/Manage/Menu">返回列表</A> ]</div>
<!-- 功能操作区域结束 -->
<div id="result" class="result none"></div>
<!-- 内容显示区域  -->
<FORM METHOD="POST" action="" id="form1" >
<TABLE cellpadding="5" cellspacing="0" class="list" border="1">
<tr><td height="5" colspan="7" class="topTd" ></td></tr>
<TR>
    <TD class="tRight" >所属菜单：</TD>
    <TD class="tLeft" ><SELECT class="medium bLeft" NAME="PMenu">
    <option value="0">顶级菜单</option>
    <?php if(is_array($menuArray)): $i = 0; $__LIST__ = $menuArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["MenuID"]); ?>" <?php if(($vo["MenuID"]) == $edit["PMenu"]): ?>selected="selected"<?php endif; ?>><?php echo ($vo["MenuName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
    </SELECT></TD>
</TR>
<TR>
    <TD class="tRight" width="100">菜单名称：</TD>
    <TD class="tLeft" ><INPUT TYPE="text" class="large bLeft"  check='Require' warning="标题不能为空" NAME="MenuName" value="<?php echo ((isset($edit["MenuName"]) && ($edit["MenuName"] !== ""))?($edit["MenuName"]):''); ?>"></TD>
</TR>
<TR>
    <TD class="tRight" >模块路径：</TD>
    <TD class="tLeft" ><INPUT TYPE="text" class="large bLeft"  check='Require' warning="URL不能为空" NAME="MenuUrl" value="<?php echo ((isset($edit["MenuUrl"]) && ($edit["MenuUrl"] !== ""))?($edit["MenuUrl"]):''); ?>"> * 例如：模块-方法-参数1-参数1值</TD>
</TR>
<TR>
    <TD class="tRight tTop">描 述：</TD>
    <TD class="tLeft"><TEXTAREA class="large bLeft" NAME="Description"  ROWS="5" COLS="57"><?php echo ((isset($edit["Description"]) && ($edit["Description"] !== ""))?($edit["Description"]):''); ?></TEXTAREA></TD>
</TR>
<TR>
    <TD class="tRight">排序：</TD>
    <TD class="tLeft"><INPUT name="MenuOrder" type="text" value="<?php echo ((isset($edit["MenuOrder"]) && ($edit["MenuOrder"] !== ""))?($edit["MenuOrder"]):99); ?>"   check='Require' warning="排序不能为空"/></TD>
</TR>
<TR>
    <TD ></TD>
    <TD class="center"><div style="width:85%;margin:5px">
        <INPUT TYPE="hidden" name="MenuID" value="<?php echo ($edit["MenuID"]); ?>">
        <INPUT TYPE="hidden" name="act" value="<?php echo ($act); ?>">
    <div class="impBtn fLeft"><INPUT tag='form1' u="<?php echo U('Menu/save');?>" TYPE="button" value="保存" class="save imgButton mysubmit"></div>
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