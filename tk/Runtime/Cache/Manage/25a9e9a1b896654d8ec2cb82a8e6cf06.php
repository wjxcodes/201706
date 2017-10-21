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
var URL = '/Manage/Knowledge';
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
<script language="javascript">
$(document).ready(function(){
    var s='<?php echo ($edit["SubjectID"]); ?>';
    var z='<?php echo ($edit["PID"]); ?>';
    var x='';
    if(s){
        $.get(U('Knowledge/getzsd?s='+s+'&z='+z),function(data){
            //权限验证
            if(checkPower(data)=='error'){
                return false;
            }
                x='';
                if(z=='0') x='selected="selected"';
                $('#zsd').html('<option value="">请选择</option><option value="0" '+x+'>顶级知识点</option>'+data['data']);
        });
    }
    
    $('#subject').change(function(){
        if($(this).val()!=''){
            $.get(U('Knowledge/getzsd?s='+$(this).val()),function(data){
                //权限验证
                if(checkPower(data)=='error'){
                    return false;
                }
                $('#zsd').html('<option value="">请选择</option><option value="0">顶级知识点</option>'+data['data']);
            });
        }else{
            $('#zsd').html('<option value="">请选择</option>');
        }
    });
});
</script>
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content">
<!--  功能操作区域  -->
<div class="title"><?php echo ($pageName); ?> [ <A HREF="/Manage/Knowledge">返回列表</A> ]</div>
<!-- 功能操作区域结束 -->
<div id="result" class="result none"></div>
<!-- 内容显示区域  -->
<FORM METHOD="POST" action="" id="form1" >
<TABLE cellpadding="5" cellspacing="0" class="list" border="1">
<tr><td height="5" colspan="7" class="topTd" ></td></tr>
<TR>
    <TD class="tRight" >所属学科：</TD>
    <TD class="tLeft" ><SELECT id="subject" class="medium bLeft" NAME="SubjectID" check='Require' warning="所属学科不能为空">
    <option value="">请选择</option>
    <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value=""><?php echo ($vo["SubjectName"]); ?></option>
        <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $edit["SubjectID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
    <?php else: ?>
        <option value="0">请添加学科</option><?php endif; ?>
    </SELECT></TD>
</TR>
<TR>
    <TD class="tRight" >所属知识点：</TD>
    <TD class="tLeft" ><SELECT id="zsd" class="large bLeft" NAME="PID" check='Require' warning="所属知识点不能为空">
    <option value="">请选择</option>
    </SELECT></TD>
</TR>
<TR>
    <TD class="tRight" width="100">知识点名称：</TD>
    <TD class="tLeft" ><INPUT TYPE="text" class="large bLeft"  check='Require' warning="知识点不能为空" NAME="KlName" value='<?php echo ((isset($edit["KlName"]) && ($edit["KlName"] !== ""))?($edit["KlName"]):''); ?>'></TD>
</TR>
<TR>
    <TD class="tRight">考频：</TD>
    <TD class="tLeft"><select name="Frequency">
    <option value="1" <?php if( $edit["Frequency"] == 1 ): ?>selected="selected"<?php endif; ?>>1</option>
    <option value="2" <?php if( $edit["Frequency"] == 2 ): ?>selected="selected"<?php endif; ?>>2</option>
    <option value="3" <?php if( $edit["Frequency"] == 3 ): ?>selected="selected"<?php endif; ?>>3</option>
    </select></TD>
</TR>
<TR>
    <TD class="tRight">文理：</TD>
    <TD class="tLeft"><select name="Style">
    <option value="1" <?php if( $edit["Style"] == 1 ): ?>selected="selected"<?php endif; ?>>理科</option>
    <option value="2" <?php if( $edit["Style"] == 2 ): ?>selected="selected"<?php endif; ?>>通用</option>
    <option value="3" <?php if( $edit["Style"] == 3 ): ?>selected="selected"<?php endif; ?>>文科</option>
    </select></TD>
</TR>
<TR>
    <TD class="tRight">加入测试：</TD>
    <TD class="tLeft"><select name="IfTest">
    <option value="0" <?php if( $edit["IfTest"] == 0 ): ?>selected="selected"<?php endif; ?>>否</option>
    <option value="1" <?php if( $edit["IfTest"] == 1 ): ?>selected="selected"<?php endif; ?>>是</option>
    </select></TD>
</TR>
<TR>
    <TD class="tRight">适用范围：</TD>
    <TD class="tLeft">
        <label><input type="radio" name='IfInChoose' value="0" <?php if( $edit["IfInChoose"] == 0 ): ?>checked="checked"<?php endif; ?>> 适用全部</label>
        <label><input type="radio" name='IfInChoose' value="1" <?php if( $edit["IfInChoose"] == 1 ): ?>checked="checked"<?php endif; ?>> 适用选做题</label>
    </TD>
</TR>
<TR>
    <TD class="tRight">排序：</TD>
    <TD class="tLeft"><INPUT name="OrderID" type="text" value="<?php echo ((isset($edit["OrderID"]) && ($edit["OrderID"] !== ""))?($edit["OrderID"]):99); ?>" check='Require' warning="排序不能为空"/></TD>
</TR>
<TR>
    <TD ></TD>
    <TD class="center"><div style="width:85%;margin:5px">
        <INPUT TYPE="hidden" name="KlID" value="<?php echo ($edit["KlID"]); ?>">
        <INPUT TYPE="hidden" name="act" value="<?php echo ($act); ?>">
    <div class="impBtn fLeft"><INPUT tag='form1' u="<?php echo U('Knowledge/save');?>" TYPE="button" value="保存" class="save imgButton mysubmit"></div>
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