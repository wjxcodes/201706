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
var URL = '/Manage/PowerAdmin';
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
        <div class="title"><?php echo ($pageName); ?> [ <A HREF="/Manage/PowerAdmin">返回列表</A> ]</div>
        <!-- 功能操作区域结束 -->
        <div id="result" class="result none"></div>
        <!-- 内容显示区域  -->
        <FORM METHOD="POST" action="" id="form1" >
            <TABLE cellpadding=5 cellspacing=0  class="list" border="1">
                <tr><td height="5" colspan="7" class="topTd" ></td></tr>
                <TR>
                    <TD class="tRight" width="100">管理员组名称：</TD>
                    <TD class="tLeft" ><INPUT TYPE="text" class="large bLeft"  check='Require' warning="标题不能为空" NAME="AdminGroup" value="<?php echo ((isset($edit["AdminGroup"]) && ($edit["AdminGroup"] !== ""))?($edit["AdminGroup"]):''); ?>"></TD>
                </TR>
                <TR>
                    <TD class="tRight" >默认组：</TD>
                    <TD class="tLeft" ><label><INPUT TYPE="radio" class="bLeft"  check='raido' warning="请选择默认组" NAME="IfDefault" value="1" <?php if(($edit["IfDefault"]) == "1"): ?>checked="checked"<?php endif; ?>> 是</label>  <label><INPUT TYPE="radio" class="bLeft" NAME="IfDefault" value="0" <?php if(($edit["IfDefault"] == 0) or ($edit["IfDefault"] == '')): ?>checked="checked"<?php endif; ?>> 否</label> </TD>
                </TR>
                <TR>
                    <TD class="tRight">管理员权限：</TD>
                    <TD class="tLeft">
                        <?php if(is_array($anchor)): $i = 0; $__LIST__ = $anchor;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="#<?php echo ($vo["ListID"]); ?>" style="border-style:none;width:24%;display:inline-block;margin-top:3px;margin-bottom:3px;vertical-align:top"><?php echo ($vo["anchor"]); ?></a>
                        <?php if($i%4 == 0): ?><hr style="border-style:dashed "><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                        <hr style="border-style:dashed">
                        <?php if(is_array($powerArray)): $i = 0; $__LIST__ = $powerArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pnode): $mod = ($i % 2 );++$i; if(is_array($pnode)): $i = 0; $__LIST__ = $pnode;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i; if($node["ListID"] == 1): ?><a name="<?php echo ($node["ListID"]); ?>" style="border-style:none"><div style="display:inline-block;width:350px;line-height:25px;"><label style="white-space:nowrap;"><INPUT name="ListID[]" id="AllPower" type="checkbox" value="<?php echo ($node["ListID"]); ?>" <?php if(in_array(($node["ListID"]), is_array($edit["ListID"])?$edit["ListID"]:explode(',',$edit["ListID"]))): ?>checked=checked<?php endif; ?>  <?php if(($i) == "1"): ?>check='radio' warning="请选择权限"<?php endif; ?>/> <?php echo ($node["PowerName"]); ?>(<?php echo ($node["PowerTag"]); ?>) </label></div></a>
                        <?php else: ?>
                        <a name="<?php echo ($node["ListID"]); ?>" style="border-style:none"><div style="display:inline-block;width:350px;line-height:25px;"><label style="white-space:nowrap;"><INPUT name="ListID[]" class="power" type="checkbox" value="<?php echo ($node["ListID"]); ?>" <?php if(in_array(($node["ListID"]), is_array($edit["ListID"])?$edit["ListID"]:explode(',',$edit["ListID"]))): ?>checked=checked<?php endif; ?>  <?php if(($i) == "1"): ?>check='radio' warning="请选择权限"<?php endif; ?>/> <?php echo ($node["PowerName"]); ?>(<?php echo ($node["PowerTag"]); ?>) </label></div></a><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                        <hr style="border-style:dashed"><?php endforeach; endif; else: echo "" ;endif; ?>
                    </TD>
                </TR>
                <TR>
                    <TD ></TD>
                    <TD class="center"><div style="width:85%;margin:5px">
                        <INPUT TYPE="hidden" name="PUID" value="<?php echo ($edit["PUID"]); ?>">
                        <INPUT TYPE="hidden" name="act" value="<?php echo ($act); ?>">
                        <div class="impBtn fLeft"><INPUT tag='form1' u="<?php echo U('PowerAdmin/save');?>" TYPE="button" value="保存" class="save imgButton mysubmit"></div>
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