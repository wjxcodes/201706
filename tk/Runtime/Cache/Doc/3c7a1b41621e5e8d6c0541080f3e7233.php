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
var URL = '/Doc/DocInner';
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
<div class="title"><?php echo ($pageName); ?> <a href="<?php echo U('Doc/DocInner/index',array('t'=>'xls'));?>" >显示表格数据</a> <a href="<?php echo U('Doc/DocInner/index',array('t'=>'doc'));?>" >显示文档数据</a> <a href="<?php echo U('Doc/DocInner/getExcel');?>" >显示表格提取数据</a>  <a href="<?php echo U('Doc/DocInner/getLog');?>" >显示日志</a>
</div>
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button"  name="add" value="提取"  class="add btget imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button"  name="delete" value="删除"  class="delete btdelete imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button"  name="revert" value="刷新"  class="revert btflush imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button"  name="add" value="上传" class="add btadd imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button"  name="edit" value="一键提取" onclick='javascript:location.href="'<?php echo U("Doc/DocInner/auto");?>'"";' class="edit imgButton" style="width:100px;"></div>
    <!-- 查询区域 -->
    <FORM METHOD="POST" ACTION="<?php echo U('Doc/DocInner/getexcel');?>">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="name" value="<?php echo ($_REQUEST['name']); ?>" title="文档名查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR>
            <TD class="tRight" width="80">文档名称：</TD>
            <TD><INPUT TYPE="text" NAME="docName" class="small" value="<?php echo ($_REQUEST['DocName']); ?>" ></TD>
            <TD class="tRight" width="60">状态：</TD>
            <TD><SELECT id="Repeat" class="medium bLeft" NAME="status">
            <option value="">选择</option>
            <option value="0" <?php if(($_REQUEST['Repeat']) == "0"): ?>selected="selected"<?php endif; ?>>不重复</option>
            <option value="1" <?php if(($_REQUEST['Repeat']) == "1"): ?>selected="selected"<?php endif; ?>>重复</option>
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
    <tr><td height="5" colspan="11" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th>文档名称</th>
        <th>学科</th>
        <th>地区</th>
        <th>来源</th>
        <th>用户</th>
        <th>时间</th>
        <th>操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
        <td><input type="checkbox" class="key" value="<?php echo ($node["TmpID"]); ?>"></td>
        <td><?php echo ($node["DocName"]); ?></td>
        <td><?php echo ($node["SubjectName"]); ?></td>
        <td><?php echo ($node["AreaName"]); ?></td>
        <td><?php echo ($node["SourceDocName"]); ?></td>
        <td><?php echo ($node["UserName"]); ?></td>
        <td><?php echo (date("Y-m-d H:i:s",$node["AddTime"])); ?></td>
        <td><a href="<?php echo U('Doc/DocInner/delete',array('id'=>$node[TmpID]));?>" onclick="if(!confirm('请确认您要删除id为：“<?php echo ($node["TmpID"]); ?>”的行')) return false;">删除</a>
        </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="11" class="bottomTd"></td></tr>
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