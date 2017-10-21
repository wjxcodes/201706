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
var URL = '/Manage/Subject';
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
    <div class="content">
        <!--  功能操作区域  -->
        <div class="title"><?php echo ($pageName); ?> [ <A HREF="/Manage/Subject">返回列表</A> ] [ <A HREF="javascript:history.go(-1);">返回上一页</A> ]</div>
        <!-- 功能操作区域结束 -->
        <div id="result" class="result none"></div>
        <!-- 内容显示区域  -->
        <FORM METHOD="POST" action="" id="form1" >
            <TABLE cellpadding="5" cellspacing="0" class="list" border="1">
                <tr><td height="5" colspan="7" class="topTd" ></td></tr>
                <TR>
                    <TD class="tRight" >所属学科：</TD>
                    <TD class="tLeft" ><SELECT class="medium bLeft" NAME="PID">
                        <option value="0">顶级学科</option>
                        <?php if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["SubjectID"]); ?>" <?php if(($vo["SubjectID"]) == $edit["PID"]): ?>selected="selected"<?php endif; ?>><?php echo ($vo["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </SELECT></TD>
                </TR>
                <TR>
                    <TD class="tRight" width="100">学科名称：</TD>
                    <TD class="tLeft" ><INPUT TYPE="text" class="large bLeft"  check='Require' warning="学科不能为空" NAME="SubjectName" value="<?php echo ((isset($edit["SubjectName"]) && ($edit["SubjectName"] !== ""))?($edit["SubjectName"]):''); ?>"></TD>
                </TR>
                <TR>
                    <TD class="tRight" width="100">学科属性：</TD>
                    <TD class="tLeft" >
                        <label><INPUT TYPE="radio" class="status bLeft"  check='radio' warning="属性不能为空" NAME="Style" value="0" <?php if($edit["Style"] == 0): ?>checked="checked"<?php endif; ?>> 无</label>
                        <label><INPUT TYPE="radio" class="status bLeft" NAME="Style" value="3" <?php if($edit["Style"] == 3): ?>checked="checked"<?php endif; ?>> 文理通用</label>
                        <label><INPUT TYPE="radio" class="status bLeft" NAME="Style" value="1" <?php if($edit["Style"] == 1): ?>checked="checked"<?php endif; ?>> 文科</label>
                        <label><INPUT TYPE="radio" class="status bLeft" NAME="Style" value="2" <?php if($edit["Style"] == 2): ?>checked="checked"<?php endif; ?>> 理科 </label>
                    </TD>
                </TR>
                <TR>
                    <TD class="tRight">默认试卷总分：</TD>
                    <TD class="tLeft"><INPUT name="TotalScore" type="text" value="<?php echo ((isset($edit["TotalScore"]) && ($edit["TotalScore"] !== ""))?($edit["TotalScore"]):150); ?>" check='Require' warning="试卷总分不能为空"/>分</TD>
                </TR>
                <TR>
                    <TD class="tRight">默认答题时间：</TD>
                    <TD class="tLeft"><INPUT name="TestTime" type="text" value="<?php echo ((isset($edit["TestTime"]) && ($edit["TestTime"] !== ""))?($edit["TestTime"]):120); ?>" check='Require' warning="答题时间不能为空"/>分钟</TD>
                </TR>
                <TR>
                    <TD class="tRight">试卷字体大小：</TD>
                    <TD class="tLeft" >
                        <label><INPUT TYPE="radio" class="FontSize bLeft"  check='radio' warning="字体大小不能为空" NAME="FontSize" value="0" <?php if($edit["FontSize"] == 0): ?>checked="checked"<?php endif; ?>> 默认</label>
                        <label><INPUT TYPE="radio" class="FontSize bLeft" NAME="FontSize" value="10.5" <?php if($edit["FontSize"] == 10.5): ?>checked="checked"<?php endif; ?>> 五号</label>
                        <label><INPUT TYPE="radio" class="FontSize bLeft" NAME="FontSize" value="12" <?php if($edit["FontSize"] == 12): ?>checked="checked"<?php endif; ?>> 小四</label>
                    </TD>
                </TR>
                <TR>
                    <TD class="tRight">章节对应关系：</TD>
                    <TD class="tLeft" >
                        <label><INPUT TYPE="radio" class="ChapterSet bLeft"  check='radio' warning="章节对应关系不能为空" NAME="ChapterSet" value="0" <?php if($edit["ChapterSet"] == 0): ?>checked="checked"<?php endif; ?>> 对应知识点</label>
                        <label><INPUT TYPE="radio" class="ChapterSet bLeft" NAME="ChapterSet" value="1" <?php if($edit["ChapterSet"] == 1): ?>checked="checked"<?php endif; ?>> 对应知识点和关键字</label>
                    </TD>
                </TR>
                <TR>
                    <TD class="tRight">doc垂直排版：</TD>
                    <TD class="tLeft" >
                        <label><INPUT TYPE="radio" class="FormatDoc bLeft"  check='radio' warning="章节对应关系不能为空" NAME="FormatDoc" value="0" <?php if($edit["FormatDoc"] == 0): ?>checked="checked"<?php endif; ?>> 自动识别（公式居中优先）</label>
                        <br/><label><INPUT TYPE="radio" class="FormatDoc bLeft" NAME="FormatDoc" value="1" <?php if($edit["FormatDoc"] == 1): ?>checked="checked"<?php endif; ?>> 垂直自适应（公式居中）</label>
                        <br/><label><INPUT TYPE="radio" class="FormatDoc bLeft" NAME="FormatDoc" value="2" <?php if($edit["FormatDoc"] == 2): ?>checked="checked"<?php endif; ?>> 垂直居中（图片居中）</label>
                    </TD>
                </TR>
                <TR>
                    <TD class="tRight">排序：</TD>
                    <TD class="tLeft"><INPUT name="OrderID" type="text" value="<?php echo ((isset($edit["OrderID"]) && ($edit["OrderID"] !== ""))?($edit["OrderID"]):99); ?>" check='Require' warning="排序不能为空"/></TD>
                </TR>
                <TR>
                    <TD class="tRight">支付方式：</TD>
                    <TD class="tLeft" >
                        <label><INPUT TYPE="radio" class=" bLeft"  check='radio' warning="支付方式不能为空" NAME="MoneyStyle" value="0" <?php if($edit["MoneyStyle"] == 0): ?>checked="checked"<?php endif; ?>> 按题</label>
                        <label><INPUT TYPE="radio" class=" bLeft" NAME="MoneyStyle" value="1" <?php if($edit["MoneyStyle"] == 1): ?>checked="checked"<?php endif; ?>> 按套卷</label>【解析试题分成】
                    </TD>
                </TR>
                <TR>
                    <TD class="tRight">支付金额：</TD>
                    <TD class="tLeft"><INPUT name="PayMoney" type="text" value="<?php echo ((isset($edit["PayMoney"]) && ($edit["PayMoney"] !== ""))?($edit["PayMoney"]):''); ?>" check='Require' warning="支付金额不能为空"/>元</TD>
                </TR>
                <TR>
                    <TD class="tRight">答题卡版式：</TD>
                    
                    <TD class="tLeft" >
                        <label><INPUT TYPE="radio" class=" bLeft"  check='radio' warning="答题卡版式不能为空" NAME="Layout" value="A3" <?php if($edit["Layout"] == 'A3'): ?>checked="checked"<?php endif; ?>> A3</label>
                        <label><INPUT TYPE="radio" class=" bLeft" NAME="Layout" value="A4" <?php if($edit["Layout"] == 'A4'): ?>checked="checked"<?php endif; ?>> A4</label>
                    </TD>
                </TR>
                <TR>
                    <TD ></TD>
                    <TD class="center"><div style="width:85%;margin:5px">
                        <INPUT TYPE="hidden" name="SubjectID" value="<?php echo ($edit["SubjectID"]); ?>">
                        <INPUT TYPE="hidden" name="act" value="<?php echo ($act); ?>">
                        <div class="impBtn fLeft"><INPUT tag='form1' u="<?php echo U('Subject/save');?>" TYPE="button" value="保存" class="save imgButton mysubmit"></div>
                        <div class="impBtn fLeft m-l10"><INPUT TYPE="reset" class="reset imgButton" value="清空" ></div>
                    </div></TD>
                </TR>
                <tr><td height="5" colspan="7" class="bottomTd" ></td></tr>
            </TABLE>
        </FORM>
        <!-- 列表显示区域结束 -->
    </div>
    <!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>