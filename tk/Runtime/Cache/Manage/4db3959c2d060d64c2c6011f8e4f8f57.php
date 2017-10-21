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
var URL = '/Manage/School';
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
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="新增" onclick="" class="btadd add imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="编辑" onclick="" class="btedit edit imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>

<!-- 查询区域 -->
    <FORM METHOD="POST" ACTION="/Manage/School">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="name" value="<?php echo ($_REQUEST['name']); ?>" title="学校查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR>
            <TD class="tRight" width="75">学校名称：</TD>
            <TD><INPUT TYPE="text" NAME="SchoolName" class="medium" value="<?php echo ($_REQUEST['SchoolName']); ?>" ></TD>
            <TD class="tRight" width="60">学校id：</TD>
            <TD><INPUT TYPE="text" NAME="SchoolID" class="small" value="<?php echo ($_REQUEST['SchoolID']); ?>" ></TD>
            <TD class="tRight" width="80">所属地区：</TD>
            <TD><SELECT name="AreaID[]" id="sf" class="selectArea w90px">
            <option value="">选择</option>
                <?php if(is_array($areaArray)): $i = 0; $__LIST__ = $areaArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["AreaID"]); ?>" last="<?php echo ($vo["Last"]); ?>"><?php echo ($vo["AreaName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            </SELECT></TD>
            <TD class="tRight" width="80">所属级别：</TD>
            <TD><SELECT name="Type" id="Type" class="w90px Type bLeft">
            <option value="">选择</option>
                <option value="1" <?php if($_REQUEST['Type']== '1'): ?>selected="selected"<?php endif; ?>>高中</option>
                <option value="2" <?php if($_REQUEST['Type']== '2'): ?>selected="selected"<?php endif; ?>>初中</option>
                <option value="3" <?php if($_REQUEST['Type']== '3'): ?>selected="selected"<?php endif; ?>>职高</option>
            </SELECT></TD>
            <TD class="tRight" width="80">状态：</TD>
            <TD><SELECT name="Status" class="w90px Status bLeft">
                <option value="">请选择</option>
                <option value="0" <?php if(($_REQUEST['Status']== "0") or ($_REQUEST['Status']!= "")): ?>selected="selected"<?php endif; ?>>未审核</option>
                <option value="1" <?php if($_REQUEST['Status']== "1"): ?>selected="selected"<?php endif; ?>>锁定</option>
                <option value="2" <?php if($_REQUEST['Status']== "2"): ?>selected="selected"<?php endif; ?>>正常</option>
            </SELECT></TD>
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
        <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th width="10%">编号</th>
        <th>学校名称</th>
        <th>所属地区</th>
        <th>级别</th>
        <th>状态</th>
        <th>排序</th>
        <th>操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
            <td><input type="checkbox" class="key" value="<?php echo ($node["SchoolID"]); ?>"></td>
            <td><?php echo ($node["SchoolID"]); ?></td>
            <td><?php echo ($node["SchoolName"]); ?></td>
            <td><?php echo ($node["AreaPath"]); ?></td>
            <td><?php if($node["Type"] == 1): ?>高中<?php endif; if($node["Type"] == 2): ?>初中<?php endif; if($node["Type"] == 3): ?>职高<?php endif; ?></td>
            <td><?php if($node["Status"] == "0"): ?><font color="red">未审核</font><?php endif; ?>
            <?php if($node["Status"] == "1"): ?><font color="blue">锁定</font><?php endif; ?>
            <?php if($node["Status"] == "2"): ?>正常<?php endif; ?></td>
            <td><?php echo ($node["OrderID"]); ?></td>
            <td><a href="#" class="btedit" thisid="<?php echo ($node["SchoolID"]); ?>">编辑</a>&nbsp;
            </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="8" class="bottomTd"></td></tr>
</table>
<!-- Think 系统列表组件结束 -->
</div>
<!-- 列表显示区域结束 -->
<!--  分页显示区域 -->
<div class="page"><?php echo ($page); ?></div>
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->
<script>
$('.selectArea').areaSelectChange("/Manage/School");
</script>

</body>
</html>