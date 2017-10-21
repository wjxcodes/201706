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
var URL = '/Manage/Admin';
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
<div class="title"><?php echo ($pageName); ?> [ <A HREF="<?php echo U('Admin/index');?>">返回列表</A> ]</div>
<!-- 功能操作区域结束 -->
<div id="result" class="result none"></div>
<!-- 内容显示区域  -->
<FORM METHOD="POST" action="" id="form1" >
<TABLE cellpadding=5 cellspacing=0  class="list" border="1">
<tr><td height="5" colspan="7" class="topTd" ></td></tr>
<TR>
    <TD class="tRight">所属组：</TD>
    <TD class="tLeft"><select name="GroupID" check='Require' warning="请选择管理员所属组">
    <option value="">请选择用户组</option>
    <?php if(is_array($powerAdminArray)): $i = 0; $__LIST__ = $powerAdminArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><option value="<?php echo ($node["PUID"]); ?>" <?php if(($node["PUID"]) == $edit["GroupID"]): ?>selected=selected<?php endif; ?>  <?php if(($act == 'add') and ($node["IfDefault"] == 1)): ?>selected=selected<?php endif; ?> > <?php echo ($node["AdminGroup"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?></select>
    </TD>
</TR>
<TR>
    <TD class="tRight" width="100">管理员名称：</TD>
    <TD class="tLeft" >
        <?php if(isset($edit["AdminID"])): echo ($edit["AdminName"]); ?>
        <?php else: ?>
        <INPUT TYPE="text" class="large bLeft"  check='Require' warning="管理员名称不能为空" NAME="AdminName" value=""> * 请输入4-20位字母和数字的组合<?php endif; ?>
    </TD>
</TR>
<?php if(isset($edit["AdminID"])): if(($edit["AdminID"]) == "1"): ?><TR>
    <TD class="tRight" >原密码：</TD>
    <TD class="tLeft" ><INPUT TYPE="password" class="bLeft" NAME="Passwordy" value="" />* 如果不修改请留空</TD>
</TR><?php endif; endif; ?>
<TR>
    <TD class="tRight" >密码：</TD>
    <TD class="tLeft" ><INPUT TYPE="password" class="bLeft" NAME="Password" value="" /><?php if(isset($edit["AdminID"])): ?>* 如果不修改请留空<?php endif; ?> 密码必须是8位以上数字、字母组合</TD>
</TR>
<TR>
    <TD class="tRight" >重复密码：</TD>
    <TD class="tLeft" ><INPUT TYPE="password" class="bLeft"  NAME="Password2" value="" /><?php if(isset($edit["AdminID"])): ?>* 如果不修改请留空<?php endif; ?> </TD>
</TR>
<TR>
    <TD class="tRight tTop">真实姓名：</TD>
    <TD class="tLeft"><INPUT TYPE="text" class="large bLeft"  check='Require' warning="真实姓名不能为空" NAME="RealName" value="<?php echo ((isset($edit["RealName"]) && ($edit["RealName"] !== ""))?($edit["RealName"]):''); ?>"></TD>
</TR>
<TR>
    <TD class="tRight tTop">email：</TD>
    <TD class="tLeft"><INPUT TYPE="text" class="large bLeft"  check='email' warning="email格式不正确" NAME="Email" value="<?php echo ((isset($edit["Email"]) && ($edit["Email"] !== ""))?($edit["Email"]):''); ?>"></TD>
</TR>
<TR>
    <TD class="tRight tTop">状态：</TD>
    <TD class="tLeft"><label><INPUT TYPE="radio" class="bLeft"  check='raido' warning="请选择状态" NAME="Status" value="0" <?php if(($edit["Status"] == '0') or ($edit["Status"] == '')): ?>checked="checked"<?php endif; ?>> 正常</label> <label><INPUT TYPE="radio" class="bLeft" NAME="Status" value="1" <?php if(($edit["Status"]) == "1"): ?>checked="checked"<?php endif; ?>> 锁定</label> </TD>
</TR>
<?php if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subjectArrayTmp): $mod = ($i % 2 );++$i;?><TR>
    <TD class="tRight tTop"><?php echo ($subjectArrayTmp["SubjectName"]); ?>：</TD>
    <TD class="tLeft">
    <?php if(subjectArrayTmp.sub): if(is_array($subjectArrayTmp["sub"])): $i = 0; $__LIST__ = $subjectArrayTmp["sub"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vi): $mod = ($i % 2 );++$i;?><label><INPUT TYPE="checkbox" class="bLeft"  check='checkbox' warning="请选择所属学科" NAME="MySubject[]" value="<?php echo ($vi["SubjectID"]); ?>" <?php if(in_array(($vi["SubjectID"]), is_array($edit["MySubject"])?$edit["MySubject"]:explode(',',$edit["MySubject"]))): ?>checked="checked"<?php endif; ?>> <?php echo ($vi["SubjectName"]); ?></label><?php endforeach; endif; else: echo "" ;endif; endif; ?>
    </TD>
</TR><?php endforeach; endif; else: echo "" ;endif; ?>
<TR>
    <TD ></TD>
    <TD class="center"><div style="width:85%;margin:5px">
        <INPUT TYPE="hidden" name="AdminID" value="<?php echo ($edit["AdminID"]); ?>">
        <INPUT TYPE="hidden" name="act" value="<?php echo ($act); ?>">
    <div class="impBtn fLeft"><INPUT tag='form1' u="<?php echo U('Admin/save');?>" TYPE="button" value="保存" class="save imgButton mysubmit"></div>
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