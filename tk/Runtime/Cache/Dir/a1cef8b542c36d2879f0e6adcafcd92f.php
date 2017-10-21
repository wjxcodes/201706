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
var URL = '/Dir/DirTemplate';
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
<div class="title"><?php echo ($pageName); ?> [ <a href="<?php echo U('DirTemplate/index');?>">返回列表</a> ]</div>
<!-- 功能操作区域结束 -->
<div id="result" class="result none"></div>
<!-- 内容显示区域  -->
<form method="POST" action="" id="form1" >
<table cellpadding="5" cellspacing="0" class="list" border="1">
<tr><td height="5" colspan="7" class="topTd" ></td></tr>
<TR>
    <TD class="tRight" >所属学科：</TD>
    <TD class="tLeft" ><SELECT class="medium bLeft" NAME="subjectID" check='Require' warning="所属学科不能为空">
    <?php if($subjectArray): ?><option value="">请选择学科</option>
    <?php if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label='<?php echo ($vo["SubjectName"]); ?>'>
        <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $edit["SubjectID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
        </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
    <?php else: ?>
        <option value="0">请添加学科</option><?php endif; ?>
    </SELECT></TD>
</TR>
<tr>
    <td class="tRight" >考试类型：</td>
    <td class="tLeft" ><select class="medium bLeft" name="typeID" check='Require' warning="所属类型不能为空">
    <?php if($examtypeArray): ?><option value="">请选择类型</option>
    <?php if(is_array($examtypeArray)): $i = 0; $__LIST__ = $examtypeArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ty): $mod = ($i % 2 );++$i;?><option value="<?php echo ($ty["TypeID"]); ?>" <?php if(($ty["TypeID"]) == $edit["TypeID"]): ?>selected="selected"<?php endif; ?>><?php echo ($ty["TypeName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
    <?php else: ?>
        <option value="0">请添加类型</option><?php endif; ?>
    </select></td>
</tr>
<tr>
    <td class="tRight" width="100">模板名称：</td>
    <td class="tLeft" ><input type="text" class="large bLeft"  check='Require' warning="模板不能为空" name="tempName" value="<?php echo ((isset($edit["TempName"]) && ($edit["TempName"] !== ""))?($edit["TempName"]):''); ?>"></td>
</tr>
<tr>
    <td class="tRight">所属用户：</td>
    <td class="tLeft">
      <input type="text" name="userName" value="<?php echo ((isset($edit["UserName"]) && ($edit["UserName"] !== ""))?($edit["UserName"]):''); ?>" />
    </td>
</tr>
<TR>
    <TD class="tRight tTop">模版参数：</TD>
    <TD class="tLeft"><textarea cols="60" rows="8" name="Content" style="line-height:20px" disabled><?php echo ($edit["Content"]); ?></textarea>
    </TD>
</TR>
<tr>
    <td class="tRight">默认模板：</td>
    <td class="tLeft">
      <input type="radio" name="ifDefault" value="0" <?php if(($edit["IfDefault"]) == "0"): ?>checked="checked"<?php endif; ?> />是
      <input type="radio" name="ifDefault" value="1" <?php if(($edit["IfDefault"]) == "1"): ?>checked="checked"<?php endif; ?> />否
      <!-- 原创模板选择 -->
      <input type="radio" id='originalityTemplate' name='ifDefault' value='2'/>添加原创模板
    </td>
</tr>
<tr>
    <td class="tRight">排序：</td>
    <td class="tLeft"><input name="orderID" type="text" value="<?php echo ((isset($edit["OrderID"]) && ($edit["OrderID"] !== ""))?($edit["OrderID"]):99); ?>" check='Require' warning="排序不能为空"/></TD>
</tr>
<tr>
    <td ></td>
    <td class="center"><div style="width:85%;margin:5px">
        <input type="hidden" name="tempID" value="<?php echo ($edit["TempID"]); ?>">
        <input type="hidden" name="act" value="<?php echo ($act); ?>">
    <div class="impBtn fLeft"><input tag='form1' u="<?php echo U('DirTemplate/save');?>" type="button" value="保存" class="save imgButton mysubmit"></div>
    <div class="impBtn fLeft m-l10"><input type="reset" class="reset imgButton" value="清空" ></div>
    </div></td>
</tr>
<tr><td height="5" colspan="7" class="bottomTd" ></td></tr>
</table>
</form>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>