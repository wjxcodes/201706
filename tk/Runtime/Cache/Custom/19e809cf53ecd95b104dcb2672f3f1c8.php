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
var URL = '/Custom/CustomTestTaskStatus';
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
<div class="title"><?php echo ($pageName); ?> </div>
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>

    <!-- 查询区域 -->
    <FORM id="form1" METHOD="POST" ACTION="/Custom/CustomTestTaskStatus">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="name" value="<?php echo ($_REQUEST['name']); ?>" title="试题ID查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR>
            <TD class="tRight" width="60">试题ID：</TD>
            <TD><INPUT TYPE="text" NAME="TestID" class="w90px" value="<?php echo ($_REQUEST['TestID']); ?>" ></TD>
            <TD class="tRight" width="60">日期：</TD>
            <TD width="350"><INPUT TYPE="text" NAME="Start" class="medium inputTime" value="<?php echo ($_REQUEST['Start']); ?>"> - 
            <INPUT TYPE="text" NAME="End" class="medium inputTime" value="<?php echo ($_REQUEST['End']); ?>">
            </TD>
            <TD class="tRight" width="80">所属学科：</TD>
            <TD><SELECT class="w90px bLeft" NAME="SubjectID">
                <option value="">选择</option>
                <?php if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label='<?php echo ($vo["SubjectName"]); ?>'>
                    <?php if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" >　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
            </SELECT></TD>
            <TD class="tRight" width="80">试题状态：</TD>
            <TD><SELECT class="w90px bLeft" NAME="Status">
                <option value="">-请选择-</option>
                <?php if(is_array($testStatus)): $i = 0; $__LIST__ = $testStatus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
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
    <tr class="row" style="text-align:center">
        <th width="5%">编号</th>
        <th width="5%">试题ID</th>
        <th width="8%">所属学科</th>
        <th width="10%">试题状态</th>
        <th width="15%">任务添加/过期时间</th>
        <th width="8%">任务返回次数</th>
        <th width="8%">任务编辑次数</th>
        <th width="6%">是否删除</th>
        <th width="6%">是否锁定</th>
        <th width="8%">是否是库内题</th>
        <th >需要删除试题描述</th>

    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl='' >
        <td style="text-align:center"><?php echo ($node["StatusID"]); ?></td>
        <td style="text-align:center"><?php echo ($node["TestID"]); ?></td>
        <td style="text-align:center"><?php echo ($node["SubjectName"]); ?></td>
        <td style="text-align:center"><?php echo ($node["Status"]); ?></td>
        <td style="text-align:center"><?php echo (date("Y-m-d H:i:s",$node["AddTime"])); ?> / <?php echo (date("Y-m-d H:i:s",$node["TaskTime"])); ?></td>
        <td style="text-align:center"><?php echo ($node["BackTimes"]); ?></td>
        <td style="text-align:center"><?php echo ($node["EditTimes"]); ?></td>
        <td style="text-align:center"><?php echo ($node["IfDel"]); ?></td>
        <td style="text-align:center"><?php echo ($node["IfLock"]); ?></td>
        <td style="text-align:center"><?php echo ($node["IfIntro"]); ?></td>
        <td style="text-align:center"><?php echo ($node["ErrorMsg"]); ?></td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="11" class="bottomTd"></td></tr>
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