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
var URL = '/Teacher/CustomTestLog';
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
<div class="title"><?php echo ($pageName); ?> </div>
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>

    <!-- 查询区域 -->
    <form id="form1" method="POST" action="/Teacher/CustomTestLog">
    <div class="fRig">
        <div class="fLeft">
            <span id="key">
                <input type="text" name="name" value="<?php echo ($_REQUEST['name']); ?>" placeholder="试题ID查询" title="试题ID查询" class="medium" >
            </span>
        </div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <table border="0" cellpadding="1" cellspacing="3" width="100%">
            <tr>
                <td class="tRight" width="60">试题ID：</TD>
                <td>
                    <input type="text" name="TestID" class="small" value="<?php echo ($_REQUEST['TestID']); ?>" >
                </td>
                <td class="tRight" width="60">日期：</td>
                <td width="200">
                    <input type="text" name="Start" class="small inputDate" value="<?php echo ($_REQUEST['Start']); ?>"> -
                    <input type="text" name="End" class="small inputDate" value="<?php echo ($_REQUEST['End']); ?>">
                </td>
            </tr>
        </table>
    </div>
    </form>
</div>
<!-- 功能操作区域结束 -->

<!-- 列表显示区域  -->
<div class="list" >
<div id="result" class="result none"></div>
<!-- Think 系统列表组件开始 -->
<table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
    <tr><td height="5" colspan="10" class="topTd" ></td></tr>
    <tr class="row" style="text-align:center">
        <th width="6%">日志编号</th>
        <th width="5%">试题ID</th>
        <th width="10%">所属学科</th>
        <th width="10%">用户名</th>
        <th>分值</th>
        <th width="10%">操作用户</th>
        <th width="12%">状态描述</th>
        <th width="30%">操作描述</th>
        <th>操作时间</th>

    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl='' >
        <td style="text-align:center"><?php echo ($node["LogID"]); ?></td>
        <td style="text-align:center"><?php echo ($node["TestID"]); ?></td>
        <td style="text-align:center"><?php echo ($node["SubjectName"]); ?></td>
        <td style="text-align:center"><?php echo ($node["UserName"]); ?></td>
        <td style="text-align:center"><?php echo ($node["Point"]); ?></td>
        <td style="text-align:center"><?php echo ($node["Admin"]); ?></td>
        <td><?php echo ($node["LogStatus"]); ?></td>
        <td><div style="max-width:480px;overflow:auto;"><?php echo ($node["Description"]); ?></div></td>
        <td style="text-align:center"><?php echo (date("Y-m-d H:i:s",$node["AddTime"])); ?></td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="10" class="bottomTd"></td></tr>
</table>
<!-- Think 系统列表组件结束 -->
</div>
<!-- 功能操作区域结束 -->
<!-- Think 系统列表组件结束 -->
</div>
<!--  分页显示区域 -->
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<form id="hiddenform" action="?" method="post" style="display:none">
<input name="id" id="testidlist" value=""/>
</form>
<!-- 主页面结束 -->

</body>
</html>