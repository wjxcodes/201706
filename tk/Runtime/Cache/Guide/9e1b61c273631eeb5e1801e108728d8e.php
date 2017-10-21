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
var URL = '/Guide/CaseMenu';
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
<div class="title"><?php echo ($pageName); ?> [ <A HREF="<?php echo U('CaseMenu/index');?>">返回列表</A> ]</div>
<!-- 功能操作区域结束 -->
<div id="result" class="result none"></div>
<!-- 内容显示区域  -->
<FORM METHOD="POST" action="" id="form1" >
<TABLE cellpadding="5" cellspacing="0" class="list" border="1">
<tr><td height="5" colspan="7" class="topTd" ></td></tr>
<TR>
    <TD class="tRight" >所属学科：</TD>
    <TD class="tLeft" ><SELECT class="medium bLeft" NAME="SubjectID" check='Require' warning="所属学科不能为空">
    <?php if($subjectArray): ?><option value="">请选择学科</option>
    <?php if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label="<?php echo ($vo["SubjectName"]); ?>">
        <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $edit["SubjectID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
        </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
    <?php else: ?>
        <option value="0">请添加学科</option><?php endif; ?>
    </SELECT></TD>
</TR>
<TR>
    <TD class="tRight" width="100">栏目名称：</TD>
    <TD class="tLeft" ><INPUT TYPE="text" class="large bLeft"  check='Require' warning="栏目名称不能为空" NAME="MenuName" value="<?php echo ((isset($edit["MenuName"]) && ($edit["MenuName"] !== ""))?($edit["MenuName"]):''); ?>"></TD>
</TR>
<TR>
    <TD class="tRight">所属板块：</TD>
    <TD class="tLeft">
    <select name="ForumID">
        <?php if(is_array($forumArray)): $p = 0; $__LIST__ = $forumArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menuName): $mod = ($p % 2 );++$p;?><option value="<?php echo ($p); ?>" <?php if(($edit["ForumID"]) == $p): ?>selected=selected<?php endif; ?>><?php echo ($menuName["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
    </select></TD>
</TR>
<TR>
    <TD class="tRight">是否有试题：</TD>
    <TD class="tLeft">
        <label>
            <INPUT TYPE="radio"  check='Require' warning="是否有试题" NAME="IfTest" value="1" <?php if(($edit["IfTest"]) == "1"): ?>checked="checked"<?php endif; ?>>&nbsp;是 &nbsp;&nbsp;
        </label>
        <label>
            <INPUT TYPE="radio" NAME="IfTest" value="0" <?php if(($edit["IfTest"] == 0) or ($edit["IfTest"] == '')): ?>checked="checked"<?php endif; ?>>&nbsp;否
        </label>
    </TD>
</TR>
<TR>
    <TD class="tRight">是否带答案：</TD>
    <TD class="tLeft">
        <label>
            <INPUT TYPE="radio"  check='Require' warning="是否有答案" NAME="IfAnswer" value="1" <?php if(($edit["IfAnswer"]) == "1"): ?>checked="checked"<?php endif; ?>>&nbsp;是 &nbsp;&nbsp;
        </label>
        <label>
            <INPUT TYPE="radio" NAME="IfAnswer" value="0" <?php if(($edit["IfAnswer"] == 0) or ($edit["IfAnswer"] == '')): ?>checked="checked"<?php endif; ?>>&nbsp;否
        </label>
    </TD>
</TR>
    <TR>
        <TD class="tRight">序号符号：</TD>
        <TD class="tLeft">
            <label>
                <INPUT TYPE="radio" warning="序号符号" NAME="NumStyle" value="0" <?php if(($edit["NumStyle"] == 0) or ($edit["IfAnswer"] == '')): ?>checked="checked"<?php endif; ?>>&nbsp;数字序号
            </label>
            <label>
                <INPUT TYPE="radio"  check='Require'  NAME="NumStyle" value="1" <?php if(($edit["NumStyle"]) == "1"): ?>checked="checked"<?php endif; ?>>&nbsp;汉字序号
            </label>
        </TD>
    </TR>
<TR>
    <TD class="tRight">排序：</TD>
    <TD class="tLeft"><INPUT name="OrderID" type="text" value="<?php echo ((isset($edit["OrderID"]) && ($edit["OrderID"] !== ""))?($edit["OrderID"]):99); ?>" check='Require' warning="排序不能为空"/></TD>
</TR>
<TR>
    <TD ></TD>
    <TD class="center"><div style="width:85%;margin:5px">
        <INPUT TYPE="hidden" name="MenuID" value="<?php echo ($edit["MenuID"]); ?>">
        <INPUT TYPE="hidden" name="act" value="<?php echo ($act); ?>">
    <div class="impBtn fLeft"><INPUT tag='form1' u="<?php echo U('CaseMenu/save');?>" TYPE="button" value="保存" class="save imgButton mysubmit"></div>
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