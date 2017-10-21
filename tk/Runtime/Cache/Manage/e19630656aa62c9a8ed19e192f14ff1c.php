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
var URL = '/Manage/CorrectLog';
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
<div class="title"><?php echo ($pageName); ?> [<a href="<?php echo U('stat');?>">纠错任务统计</a>]</div>
<!--  功能操作区域  -->
<div class="operate">
    <!--<a href="<?php echo U('CorrectLog/rename');?>">替换数据</a>-->
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
    
<!-- 查询区域 -->
    <FORM METHOD="POST" ACTION="/Manage/CorrectLog" id='formclear'>
    <div class="fRig">
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR>
            <TD class="tRight" width="80">试题编号：</TD>
            <TD width="80"><INPUT TYPE="text" NAME="TestID" class="small" value=""></TD>
            <TD class="tRight" width="60">用户名：</TD>
            <TD  width="80"><INPUT TYPE="text" NAME="UserName" class="small" value="" ></TD>
            <TD class="tRight" width="60">状态：</TD>
            <TD  width="80">
                <select name='IfAnswer'>
                    <option value='3'>全部</option>
                    <option value='0'>未处理</option>
                    <option value='1'>已处理</option>
                </select>
            </TD>
            <TD class="tRight" width="80">所属学科：</TD>
            <TD><SELECT class="medium bLeft" NAME="SubjectID">
                <option value="">选择</option>
                <?php if(is_array($subject_array)): $i = 0; $__LIST__ = $subject_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label='<?php echo ($vo["SubjectName"]); ?>'>
                    <?php if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" >　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
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
        <th width="4%">编号</th>
        <th>试题编号</th>
        <th width="8%">所属学科</th>
        <th width="8%">错误来自</th>
        <th>用户名</th>
        <th width="35%">提交内容</th>
        <th>回复状态</th>
        <th>实质错误</th>
        <th>提交时间</th>
        <th>操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
        <td><input type="checkbox" class="key" value="<?php echo ($node["CorrID"]); ?>"></td>
        <td><?php echo ($node["CorrID"]); ?></td>
        <td><a href="#" class="btedit" thisid="<?php echo ($node["CorrID"]); ?>"><?php echo ($node["TestID"]); ?></a></td>
        <td><?php echo ($node["SubjectName"]); ?></td>
        <td>【<b><?php if($node["From"] == 0 ): ?>组卷中心<?php else: ?>提分系统<?php endif; ?></b>】</td>
        <td><?php echo ($node["UserName"]); ?></td>
        <td><?php echo ($node["Content"]); ?></td>
            <td><?php if($node["IfAnswer"] == '0' ): ?><font style='color:red'>未回复</font><?php else: ?><font style='color:green'>已回复</font><?php endif; if($node["AnswerName"] != '0' ): ?>【<?php echo ($node["AnswerName"]); ?> 】<?php endif; ?></td>
            <td><?php if($node["IfError"] == '1' ): ?><font style='color:red'>是</font><?php elseif($node["IfError"] == '2' ): ?><font style='color:green'>否</font><?php else: ?>未标注<?php endif; ?></td>
            <td><?php echo (date('Y-m-d H:i:s',$node["Ctime"])); ?></td>
        <td>
        <a href="#" class="btedit" thisid="<?php echo ($node["CorrID"]); ?>">处理</a>&nbsp;&nbsp;
        </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="11" class="bottomTd"></td></tr>
</table>
<!-- Think 系统列表组件结束 -->
</div>
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>