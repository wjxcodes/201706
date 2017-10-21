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
var URL = '/Index/WlnFeedback';
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
    $('.setCheck').live('click',function(){
        var keyValue = $(this).attr('thisid');
        jInfo('处理中请稍候。。。','处理数据');
        $.get(U('Index/WlnFeedback/check?id='+keyValue+'&status=1&'+Math.random()), function(data){
            jClose();
            //权限验证
            if(checkPower(data)=='error'){
                return false;
            }
            if(data['data']=='success'){
                alert('修改成功！');
                $('#check'+keyValue).html('已处理');
            }
        });
    });
});
</script>
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content" >
<div class="title"><?php echo ($pageName); ?></div>
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="output" value="导出" onclick="" class="btexport output imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
    <!-- 查询区域 -->
    <FORM id="form1" METHOD="POST" ACTION="/Index/WlnFeedback">
    <div class="fRig">
        
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR>
            <TD class="tRight" width="45">日期：</TD>
            <TD width="350"><INPUT TYPE="text" NAME="Start" class="medium inputTime" value="<?php echo ($_REQUEST['Start']); ?>"> - 
            <INPUT TYPE="text" NAME="End" class="medium inputTime" value="<?php echo ($_REQUEST['End']); ?>">
            </TD>
            <TD class="tRight" width="45">来源：</TD>
            <TD><select name="from" >
                <option value="">全部</option>
                <?php if(is_array($from)): $i = 0; $__LIST__ = $from;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($_REQUEST['from']) == $key): ?>selected="selected"<?php endif; ?>><?php echo ($node); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
 
            </select></TD>
            <TD class="tRight" width="45">所属：</TD>
            <TD><select name="style" >
                <option value="" selected="selected">全部</option>
                <?php if(is_array($style)): $i = 0; $__LIST__ = $style;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($node); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            </select></TD>
            <TD class="tRight" width="45">状态：</TD>
            <TD><select name="Status" >
                <option value="">全部</option>
                <option value="0" <?php if(($_REQUEST['Status']) == "0"): ?>selected="selected"<?php endif; ?>>未处理</option>
                <option value="1" <?php if(($_REQUEST['Status']) == "1"): ?>selected="selected"<?php endif; ?>>已处理</option>
            </select></TD>
            <TD class="tRight" width="80">开通方式：</TD>
            <TD><select name="openStyle" >
                <option value="">全部</option>
                <?php if(is_array($openStyle)): $i = 0; $__LIST__ = $openStyle;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($_REQUEST['openStyle']) == $key): ?>selected="selected"<?php endif; ?>><?php echo ($node); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            </select></TD>
        </TR>
        </TABLE>
    </div>
    </FORM>
</div>
<!-- 功能操作区域结束 -->

<!-- 列表显示区域  -->
<div class="list" >
<div id="result" class="result none"></div>
<!-- Think 系统列表组件开始 -->
<table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
    <tr><td height="5" colspan="9" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th width="10%">编号</th>
        <th>来源</th>
        <th>留言类型</th>
        <th>留言时间</th>
        <th>用户</th>
        <th width="40%">内容</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
        <td><input type="checkbox" class="key" value="<?php echo ($node["FeedbackID"]); ?>"></td>
        <td><?php echo ($node["FeedbackID"]); ?></td>
        <td><?php echo ($node["From"]); ?></td>
        <td><?php echo ($node["Style"]); ?></td>
        <td><?php echo (date("Y-m-d H:i:s",$node["LoadTime"])); ?></td>
        <td><?php echo ($node["UserName"]); ?><br><?php echo ($node["OpenStyle"]); ?></td>
        <td><?php echo ($node["Content"]); ?></td>
        <td id="check<?php echo ($node["FeedbackID"]); ?>"><?php if(($node["Status"]) == "0"): ?><a href="javascript:void(0);" class="setCheck"  thisid="<?php echo ($node["FeedbackID"]); ?>"><font color="red">未处理</font></a><?php else: ?>已处理<?php endif; ?></td>
        <td><div><a href="#" class="btdelete" thisid="<?php echo ($node["FeedbackID"]); ?>">删除</a></div><div><?php if($node["url"] != ''): ?><a href="<?php echo U('Index/WlnFeedback/downExcel',array('id'=>$node['FeedbackID']));?>">下载附件</a><?php endif; if($node["ShowLink"] == '1'): ?><a href="<?php echo U('Doc/DocSave/view',array('id'=>$node['OpenStyle']));?>">查看文档</a><?php endif; ?></div></td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="9" class="bottomTd"></td></tr>
</table>
<!-- Think 系统列表组件结束 -->
</div>
<!--  分页显示区域 -->
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>