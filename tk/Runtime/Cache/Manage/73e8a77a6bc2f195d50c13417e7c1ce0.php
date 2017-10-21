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
    <div class="content" >
        <div class="title"><?php echo ($pageName); ?></div>

        <!-- 列表显示区域  -->
        <div class="list" >
            <div id="result" class="result none"></div>
            <?php if(($powerStr) == "true"): ?><div class="powerStr"><div class="powertit">提示</div><div class="powercont">你具有所有的操作权限，请谨慎使用，不能将密码告诉别人哦！</div></div>
            <?php else: ?>
            <!-- Think 系统列表组件开始 -->
            <table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
                <tr><td height="5" colspan="7" class="topTd" ></td></tr>
                <tr class="row" >
                    <th width="10%">编号</th>
                    <th>管理员权限名称</th>
                    <th>权限</th>
                    <th>是否区分用户</th>
                </tr>
                <?php if(is_array($powerList)): $i = 0; $__LIST__ = $powerList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
                    <td><?php echo ($key+1); ?></td>
                    <td><?php echo ($node["PowerName"]); ?></td>
                    <td class="powertrue">√</td>
                    <td><?php if($node["IfDiff"] == 1): ?><font color="red">是</font><?php else: ?>否<?php endif; ?></td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <tr><td height="5" colspan="7" class="bottomTd"></td></tr>
            </table><?php endif; ?>
            <!-- Think 系统列表组件结束 -->
        </div>
        <!-- 列表显示区域结束 -->
    </div>
    <!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>