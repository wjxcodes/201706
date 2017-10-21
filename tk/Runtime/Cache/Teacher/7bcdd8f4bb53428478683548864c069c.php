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
var URL = '/Teacher/TaskB';
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
<div class="content" >
<div class="title"><?php echo ($pageName); ?> [ <a href="/Teacher/TaskB">返回列表</a> ]</div>

<!-- 列表显示区域  -->
<div class="list" >
<div id="result" class="result none"></div>
<!-- Think 系统列表组件开始 -->
<FORM METHOD="POST" action="" id="form1" >
<table class="list" cellpadding="5" cellspacing="0" border="1">
<tr><td height="5" colspan="4" class="topTd" ></td></tr>
<tr><td width="113">编号</td><td><?php echo ($workArray["WorkID"]); ?></td><td width="113">用户</td><td><?php echo ($workArray["UserName"]); ?></td></tr>
<tr><td>添加时间</td><td><?php echo (date("Y-m-d H:i:s",$workArray["AddTime"])); ?></td><td>最后操作时间</td><td><?php echo (date("Y-m-d H:i:s",$workArray["LastTime"])); ?></td></tr>
<tr><td>状态</td><td><?php if($workArray["Status"] == 0): ?>未完成<?php endif; ?>
        <?php if(($workArray["Status"] == 1) and ($workArray["CheckStatus"] == '')): ?><font color="red">待审核</font><?php endif; ?>
        <?php if(($workArray["Status"] == 1) and ($workArray["CheckStatus"] == '0')): ?><font color="red">教师审核中</font><?php endif; ?>
        <?php if(($workArray["Status"] == 1) and ($workArray["CheckStatus"] == '1')): ?><font color="red">教师审核完成</font><?php endif; ?>
        <?php if($workArray["Status"] == 2): ?>已完成<?php endif; ?>
        <?php if($workArray["Status"] == 3): ?>重做<?php endif; ?></td><td></td><td></td></tr>
<tr><td>给标引教师的说明</td><td><textarea cols="50" rows="5" name="Content"><?php echo ($workArray["Content"]); ?></textarea></td>
<td>给审核教师的说明</td><td><textarea cols="50" rows="5" name="CheckContent"><?php echo ($workArray["CheckContent"]); ?></textarea></td>
</tr>
<tr><td>操作：</td>
<td colspan="3"><div class="impBtn fLeft" valign="bottom"><INPUT tag='form1' u="<?php echo U('Teacher/TaskB/savecheck');?>" TYPE="button" value="审核" class="save imgButton mysubmit"></div></td></tr>
<tr><td height="5" colspan="4" class="bottomTd" ></td></tr>
</table>
<INPUT TYPE="hidden" name="WorkID" value="<?php echo ($workArray["WorkID"]); ?>">
</FORM>

<table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
    <tr><td height="5" colspan="5" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="10%">编号</th>
        <th>试卷名称</th>
        <th>标引状态</th>
        <th>审核状态</th>
        <th>操作</th>
    </tr>
    <?php if(is_array($docArray)): $i = 0; $__LIST__ = $docArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl='' id="tr<?php echo ($node["DocID"]); ?>">
        <td><?php echo ($node["DocID"]); ?></td>
        <td><a href="javascript:showdoc(<?php echo ($node["DocID"]); ?>);"><?php echo ((isset($node["DocName"]) && ($node["DocName"] !== ""))?($node["DocName"]):"试卷不存在"); ?></a>  <?php if($node["CheckStatus"] != 0): ?><span id="Check<?php echo ($node["DocID"]); ?>">【<a href="javascript:showCheck(<?php echo ($node["DocID"]); ?>,<?php echo ($node["WorkID"]); ?>);">审核情况</a>】【<a href="javascript:newCheck(<?php echo ($node["DocID"]); ?>,<?php echo ($node["WorkID"]); ?>);">新审核</a>】</span><?php endif; ?></td>
        <td id="Status<?php echo ($node["DocID"]); ?>"><?php if($node["Status"] == 0): ?>未完成<?php endif; ?>
        <?php if($node["Status"] == 1): ?><font color="red">待审核</font><?php endif; ?>
        <?php if($node["Status"] == 2): ?><font color="red">通过</font><?php endif; ?>
        <?php if($node["Status"] == 3): ?>重做<?php endif; ?>
        </td>
        <td id="CheckStatus<?php echo ($node["DocID"]); ?>"><?php if($node["CheckStatus"] == 0): ?><font color="red">未审核</font><?php endif; ?>
        <?php if($node["CheckStatus"] == 1): ?>已审核<?php endif; ?>
        <?php if($node["CheckStatus"] == 2): ?>已完成<?php endif; ?>
        </td>
        <td>
        <?php if(($workArray["Status"] == 0 and $node["Status"] == 0) or $node["DocName"] == ''): ?>[<a href="javascript:deldoc(<?php echo ($node["DocID"]); ?>,<?php echo ($workArray["WorkID"]); ?>)"><font color="red">删除</font></a>]&nbsp;<?php endif; ?>
        <?php if($workArray["Status"] == 1): ?>[<a href="javascript:checkdoc(<?php echo ($node["DocID"]); ?>,<?php echo ($workArray["WorkID"]); ?>,2)"><font color="red">通过</font></a>]&nbsp;
        [<a href="javascript:checkdoc(<?php echo ($node["DocID"]); ?>,<?php echo ($workArray["WorkID"]); ?>,3)">重做</a>]&nbsp;<?php endif; ?>
        </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="5" class="bottomTd"></td></tr>
</table>
<!-- Think 系统列表组件结束 -->
</div>
<div>
<iframe id="myframe" src="" scrolling="yes" frameborder="1"  width="800" height="500"></iframe>
</div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<script>
function showdoc(id){
    $('#myframe').attr('src',U('Test/Test/index?DocID='+id));
    changeiframe();
}
function showCheck(id,wid){
    $('#myframe').attr('src',U('Teacher/TaskB/showCheck?DocID='+id+'&WorkID='+wid));
    changeiframe();
}
function newCheck(id,wid){
    $('#myframe').attr('src',U('Teacher/TaskB/newCheck?DocID='+id+'&WorkID='+wid));
    changeiframe();
}
$(document).ready(function(){
    changeiframe();
});
$(window).resize(function(){changeiframe();});
function changeiframe(){
    if($(window).width()>800){
        $('#myframe').width($(window).width()-15);
    }
    if($(window).height()>500){
        $('#myframe').height($(window).height()-50);
    }
}
function deldoc(did,wid){
    if(confirm('是否确认删除任务？')){
        $.get(U('Teacher/TaskB/deldoc?did='+did+'&wid='+wid),function(data){
            //权限验证
            if(checkPower(data)=='error'){
                return false;
            }
            if(data['data']=="success"){
                $('#tr'+did).remove();
                alert('删除成功');
            }else{
                alert(data['data']);
            }
        });
    }
}
function checkdoc(did,wid,s){
    var t='';
    var x='';
    if(s==2){
        t='<font color="red">通过<font>';
        x='已完成';
    }else if(s==3){
        t='重做';
        x='<font color="red">未审核<font>';
    }else{
        alert('数据错误');
        return;
    }
    $.get(U('Teacher/TaskB/checkdoc?did='+did+'&wid='+wid+'&s='+s),function(data){
        if(checkPower(data)=='error'){
            return false;
        }
        if(data['data']=='success'){
            $('#Status'+did).html(t);
            $('#CheckStatus'+did).html(x);
            alert('审核成功');
        }else{
            alert(data['data']);
        }
    });
}

</script>
<!-- 主页面结束 -->

</body>
</html>