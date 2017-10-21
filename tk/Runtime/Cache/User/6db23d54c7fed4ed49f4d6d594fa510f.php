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
var URL = '/User/User';
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
<div class="title"><?php echo ($pageName); ?> [ <A HREF="/User/User">返回列表</A> ]</div>
<!-- 功能操作区域结束 -->
<div id="result" class="result none"></div>
<!-- 内容显示区域  -->
<FORM METHOD="POST" action="" id="form1" >
<TABLE cellpadding="5" cellspacing="0" class="list" border="1">
<tr><td height="5" colspan="7" class="topTd" ></td></tr>
<TR>
    <TD class="tRight" width="100">用户名称：</TD>
    <TD class="tLeft" >
        <?php echo ($edit["UserName"]); ?>
    </TD>
</TR>
<?php if(is_array($powerArray)): $num = 0; $__LIST__ = $powerArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($num % 2 );++$num;?><TR>
<TD class="tRight"><?php echo ($groupArray[$num]['UserGroupName']); ?>：</TD>
<TD class="tLeft">
    <?php if($num != 3): if(is_array($vo)): $i = 0; $__LIST__ = $vo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vi): $mod = ($i % 2 );++$i;?><label><INPUT TYPE="radio" class="bLeft" NAME="groupname_<?php echo ($num); ?>" value="<?php echo ($vi["PUID"]); ?>" <?php if($vi["IsSelect"] == 1): ?>checked="checked"<?php endif; ?>><?php echo ($vi["UserGroup"]); ?>&nbsp&nbsp</label><?php endforeach; endif; else: echo "" ;endif; ?>
    <?php else: ?>
    <?php if(is_array($vo)): $i = 0; $__LIST__ = $vo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vi): $mod = ($i % 2 );++$i;?><label><INPUT TYPE="checkbox" class="bLeft" NAME="groupname_<?php echo ($num); ?>[]" value="<?php echo ($vi["PUID"]); ?>" <?php if($vi["IsSelect"] == 1): ?>checked="checked"<?php endif; ?>><?php echo ($vi["UserGroup"]); ?>&nbsp&nbsp</label><?php endforeach; endif; else: echo "" ;endif; endif; ?>
</TD>
</TR><?php endforeach; endif; else: echo "" ;endif; ?>
<TR>
    <TD class="tRight" >到期日期：</TD>
    <TD class="tLeft" ><font color="red"><?php if($edit["EndTime"] == 0): ?>未包月或已到期<?php else: echo (date("Y-m-d",$edit["EndTime"])); endif; ?></font></TD>
</TR>
<TR>
    <TD class="tRight" >到期日期：</TD>
    <TD class="tLeft" ><INPUT TYPE="text" class="bLeft inputDate" NAME="EndTime" value=""  /></TD>
</TR>


<TR>
    <TD ></TD>
    <TD class="center"><div style="width:85%;margin:5px">
        <INPUT TYPE="hidden" name="UserID" value="<?php echo ($edit["UserID"]); ?>">
    <div class="impBtn fLeft"><INPUT tag='form1' u="<?php echo U('User/month');?>" TYPE="button" value="保存" class="save imgButton mysubmit"></div>
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