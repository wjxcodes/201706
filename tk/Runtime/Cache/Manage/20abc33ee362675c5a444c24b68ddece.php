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
var URL = '/Manage/ErrLog';
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
<div class="title"><?php echo ($pageName); ?> 【<a href="<?php echo U('ErrLog/del');?>" id='del' onclick="return window.confirm('确认删除最近的数据？');">删除历史数据</a>】</div>
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
    <!-- 查询区域 -->
    <FORM id="form1" METHOD="POST" ACTION="<?php echo U('ErrLog/index');?>">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="groupName" value="<?php echo ($_REQUEST['name']); ?>" title="分组查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR>
            <TD class="tRight" width="60">表名：</TD>
            <TD>
                <INPUT TYPE="text" NAME="tableName" class="medium" id='tableNameSearch' value="<?php echo ($_REQUEST['tableName']); ?>" title='未选中[所有日志]时该查询仅搜索当前页面'>
            </TD>
            <TD class="tRight" width="60">日期：</TD>
            <TD width="350"><INPUT TYPE="text" NAME="start" class="medium inputTime" value="<?php echo ($_REQUEST['start']); ?>"> - 
            <INPUT TYPE="text" NAME="end" class="medium inputTime" value="<?php echo ($_REQUEST['end']); ?>">
            </TD>
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
    <tr><td height="5" colspan="8" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="7"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th width="50">编号</th>
        <th>分组</th>
        <th>时间</th>
        <th>描述</th>
    </tr>
    <?php if(is_array($fileDate)): $i = 0; $__LIST__ = $fileDate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row" jl=''>
            <td rowspan="5"><input type="checkbox" class="key" value="<?php echo ($i); ?>"></td>
            <td rowspan="5" align="center"><?php echo ($node['ErrorID']); ?></td>
            <td><?php echo ($node['Url']); ?></td>
            <td><?php echo (date("Y-m-d H:i:s",$node['AddTime'])); ?></td>
            <td><?php echo ($node['Description']); ?></td>
        </tr>
        <tr>
            <td colspan="3" style="background-color:#cdd;"><strong>参数：</strong></td>
        </tr>
        <tr class="row lists" jl=''>
            <td colspan="3">
                <div class='tableDiv' style="overflow:auto;width:600px;"><?php echo ($node['Params']); ?></div>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="background-color:#cdd;"><strong>执行语句：</strong></td>
        </tr>
        <tr class="row lists" jl=''>
            <td colspan="3">
                <div class='tableSearch tableDiv' style="overflow:auto;width:600px;"><?php echo ($node['SqlContent']); ?></div>
            </td>
        </tr>
        <tr><td height='15' colspan="5"></td></tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="7" class="bottomTd"></td></tr>
</table>
<!-- Think 系统列表组件结束 -->
</div>
<!--  分页显示区域 -->
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<script>
    $('#checkList tr').each(function(){
        var that = $(this);
        that.find('.tableDiv').each(function(){
            $(this).width(that.width()-300);
        });
    });
    $('#tableNameSearch').blur(function(){
        var that = $(this);
        var val = that.val();
        var all = $('#all');
        if(val != '' && all.attr('checked')){
            $('#form1').submit();
            return false;
        }
        $('.lists').each(function(){
            var list = $(this);
            list.find('td .tableSearch').each(function(){
                var text = $(this).html();
                if(text.indexOf(val) >= 0 || val == ''){
                    list.show();
                }else{
                    list.hide();
                }
            });
        });
    });
</script>
<!-- 主页面结束 -->

</body>
</html>