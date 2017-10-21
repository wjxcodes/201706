<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<TITLE>『<?php echo (C("WLN_WEB_NAME")); ?>管理平台』</TITLE>
<link rel="stylesheet" type="text/css" href="/Public/teacher/css/style.css" />
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="/Public/plugin/jquery.newplaceholder.js"></script>
<script type="text/javascript" src="/Public/teacher/js/common1.js"></script>
<script type="text/javascript" src="/Public/teacher/js/common.js"></script>

<SCRIPT LANGUAGE="JavaScript">
//指定当前组模块URL地址 
var URL = '/Teacher/Task';
var APP     =     '';
var PUBLIC = '/Public';
</SCRIPT>
</HEAD>

<body>
<div id="loader" >页面加载中...</div>
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content" >
<div class="title"><?php echo ($pageName); ?></div>

<!-- 列表显示区域  -->
<div class="list" >
<div id="result" class="result none"></div>
<!-- Think 系统列表组件开始 -->
<table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
    <tr><td height="5" colspan="8" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th width="10%">编号</th>
        <th>用户</th>
        <th>添加时间/最后操作时间</th>
        <th>管理员</th>
        <th>状态</th>
        <th>说明</th>
        <th>操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
        <td><input type="checkbox" class="key" value="<?php echo ($node["WorkID"]); ?>"></td>
        <td><a href="#" class="btedit" thisid="<?php echo ($node["WorkID"]); ?>"><?php echo ($node["WorkID"]); ?></a></td>
        <td><?php echo ($node["UserName"]); ?></td>
        <td><?php echo (date("Y-m-d H:i:s",$node["AddTime"])); ?>/<br><?php echo (date("Y-m-d H:i:s",$node["LastTime"])); ?></td>
        <td><?php echo ($node["Admin"]); ?></td>
        <td><?php if($node["Status"] == 0): ?>未完成<?php endif; ?>
        <?php if($node["Status"] == 1): ?><font color="red">待审核</font><?php endif; ?>
        <?php if($node["Status"] == 2): ?>已完成<?php endif; ?>
        <?php if($node["Status"] == 3): ?>重做<?php endif; ?>
        </td>
        <td><?php echo ($node["Content"]); ?></a></td>
        <td>
        <?php if($node["Status"] != 3): ?><a href="<?php echo U('Teacher/Task/checkwork', array('id'=>$node['WorkID']));?>">查看任务</a>&nbsp;<?php endif; ?>
        <?php if($node["Status"] == 3): ?><a href="<?php echo U('Teacher/Task/checkwork', array('id'=>$node['WorkID']));?>">重做任务</a>&nbsp;<?php endif; ?>
        </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="8" class="bottomTd"></td></tr>
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