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
var URL = '/Doc/DocSave';
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
<script>
$(document).ready(function(){
    $('.answer').css('display','none');
    $('.test').each(function(){
        $(this).click(function(){
            if($(this).next().css('display')=='none'){
                $(this).next().css('display','block');
            }else{
                $(this).next().css('display','none');
            }
        });
    });
});
</script>
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content">
<!--  功能操作区域  -->
<div class="title"><?php echo ($pageName); ?> [ <A HREF="/Doc/DocSave">返回列表</A> ]</div>
<!-- 功能操作区域结束 -->
<div id="result" class="result none"></div>
<!-- 内容显示区域  -->
<FORM METHOD="POST" action="" id="form1" >
<TABLE cellpadding="5" cellspacing="0" class="list" border="1">
<tr><td height="5" colspan="7" class="topTd" ></td></tr>
<TR>
    <TD class="tRight" width="10%">编号：</TD>
    <TD class="tLeft"  width="90%"><?php echo ($edit["SaveID"]); ?>
    </TD>
</TR>
<TR>
    <TD class="tRight tTop">用户名：</TD>
    <TD class="tLeft"><?php echo ($edit["UserName"]); ?>
    </TD>
</TR>
<TR>
    <TD class="tRight tTop">日期：</TD>
    <TD class="tLeft"><?php echo (date("Y-m-d H:i:s",$edit["LoadTime"])); ?>
    </TD>
</TR>
<TR>
    <TD class="tRight tTop">学科：</TD>
    <TD class="tLeft"><?php echo ($edit["SubjectName"]); ?>
    </TD>
</TR>
<tr><td height="5" colspan="7" class="bottomTd" ></td></tr>
</TABLE>
</FORM>
        <table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
            <tr><td height="5" colspan="9" class="topTd" ></td></tr>
            
            <tr class="row" >
            <th width="50">编号</th>
            <th>试题内容</th>
            <th width="100">难度</th>
            </tr>
        
            <?php if(is_array($testArray)): $i = 0; $__LIST__ = $testArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
                <td><?php echo ($vo["TestID"]); ?></td>
                <TD class="tLeft">
                <div style="height:180px;overflow:auto;">
                    <div id="test<?php echo ($i); ?>" class="test"><?php echo ($vo["Test"]); ?></div>
                    <div id="answer<?php echo ($i); ?>" class="answer"><font color="red">【答案】</font><?php echo ($vo["Answer"]); ?><br/>
                    <font color="red">【解析】</font><?php echo ($vo["Analytic"]); ?></div>
                </div>
                </TD>
                <td><?php echo (number_format($vo["Diff"],3)); ?></td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </table>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>