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
var URL = '/Manage/Types';
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
<div class="title"><?php echo ($pageName); ?> [ <A HREF="/Manage/Types">返回列表</A> ]</div>
<!-- 功能操作区域结束 -->
<div id="result" class="result none"></div>
<!-- 内容显示区域  -->
<FORM METHOD="POST" action="" id="form1" >
<TABLE cellpadding="5" cellspacing="0" class="list" border="1">
<tr><td height="5" colspan="7" class="topTd" ></td></tr>
<TR>
    <TD class="tRight" >所属学科：</TD>
    <TD class="tLeft" ><SELECT class="medium bLeft" NAME="SubjectID" check='Require' warning="所属学科不能为空">
    <?php if($subjectArray): ?><option value="">请选择学科</option>
    <?php if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label="<?php echo ($vo["SubjectName"]); ?>">
        <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $edit["SubjectID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
        </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
    <?php else: ?>
        <option value="0">请添加学科</option><?php endif; ?>
    </SELECT></TD>
</TR>
<TR>
    <TD class="tRight" width="100">题型名称：</TD>
    <TD class="tLeft" ><INPUT TYPE="text" class="large bLeft"  check='Require' warning="题型不能为空" NAME="TypesName" value="<?php echo ((isset($edit["TypesName"]) && ($edit["TypesName"] !== ""))?($edit["TypesName"]):''); ?>"></TD>
</TR>
<TR>
    <TD class="tRight">所属分卷：</TD>
    <TD class="tLeft">
    <select name="Volume">
        <option value="1" <?php if(($edit["Volume"]) == "1"): ?>selected="selected"<?php endif; ?>>分卷Ⅰ</option>
        <option value="2" <?php if(($edit["Volume"]) == "2"): ?>selected="selected"<?php endif; ?>>分卷Ⅱ</option>
    </select></TD>
</TR>
<TR>
    <TD class="tRight">最大个数：</TD>
    <TD class="tLeft">
    <select name="Num">
        <?php $__FOR_START_3507__=1;$__FOR_END_3507__=101;for($ff=$__FOR_START_3507__;$ff < $__FOR_END_3507__;$ff+=1){ ?><option value="<?php echo ($ff); ?>" <?php if(($edit["Num"]) == $ff): ?>selected="selected"<?php endif; ?>><?php echo ($ff); ?></option><?php } ?>
    </select></TD>
</TR>
<TR>
    <TD class="tRight">计分方式：</TD>
    <TD class="tLeft">
    <select name="TypesScore">
        <option value="1" <?php if(($edit["TypesScore"]) == "1"): ?>selected="selected"<?php endif; ?>>按小题计分</option>
        <option value="2" <?php if(($edit["TypesScore"]) == "2"): ?>selected="selected"<?php endif; ?>>按大题计分</option>
    </select></TD>
</TR>
<TR>
    <TD class="tRight">默认题型类型：</TD>
    <TD class="tLeft">
        <select name="TypesStyle">
            <option value="3" <?php if(($edit["TypesStyle"]) == "3"): ?>selected="selected"<?php endif; ?>>请选择</option>
            <option value="1" <?php if(($edit["TypesStyle"]) == "1"): ?>selected="selected"<?php endif; ?>>选择题</option>
            <option value="2" <?php if(($edit["TypesStyle"]) == "2"): ?>selected="selected"<?php endif; ?>>选择非选择混合</option>
            <option value="3" <?php if(($edit["TypesStyle"]) == "3"): ?>selected="selected"<?php endif; ?>>非选择</option>
        </select></TD>
</TR>
<TR>
    <TD class="tRight" width="100">是否需要0.5分：</TD>
    <TD class="tLeft" >
        <INPUT TYPE="radio" class="IfPoint" check='Require' warning="题型是否单选题型不能为空" NAME="IfPoint" value="1" <?php if(($edit["IfPoint"]) == "1"): ?>checked="checked"<?php endif; ?>>&nbsp;是 &nbsp;&nbsp;
        <INPUT TYPE="radio" class="IfPoint" NAME="IfPoint" value="0" <?php if(($edit["IfPoint"]) == "0"): ?>checked="checked"<?php endif; ?>>&nbsp;否
        * 判断试题是否需要设置0.5分</TD>
</TR>
<TR>
    <TD class="tRight">默认分值：</TD>
    <TD class="tLeft">
        <select name="DScore">
            <?php if(($edit["IfPoint"]) == "1"): $__FOR_START_3952__=0.5;$__FOR_END_3952__=100;for($ff=$__FOR_START_3952__;$ff <= $__FOR_END_3952__;$ff+=0.5){ ?><option value="<?php echo ($ff); ?>" <?php if(($edit["DScore"]) == $ff): ?>selected="selected"<?php endif; ?>><?php echo ($ff); ?></option><?php } ?>
            <?php else: ?>
                <?php $__FOR_START_25321__=1;$__FOR_END_25321__=100;for($ff=$__FOR_START_25321__;$ff <= $__FOR_END_25321__;$ff+=1){ ?><option value="<?php echo ($ff); ?>" <?php if(($edit["DScore"]) == $ff): ?>selected="selected"<?php endif; ?>><?php echo ($ff); ?></option><?php } endif; ?>
        </select></TD>
</TR>
<TR>
    <TD class="tRight">最大分值：</TD>
    <TD class="tLeft">
        <select name="MaxScore">
            <?php $__FOR_START_19310__=1;$__FOR_END_19310__=100;for($ff=$__FOR_START_19310__;$ff <= $__FOR_END_19310__;$ff+=0.5){ ?><option value="<?php echo ($ff); ?>" <?php if(($edit["MaxScore"]) == $ff): ?>selected="selected"<?php endif; ?>><?php echo ($ff); ?></option><?php } ?>
        </select></TD>
</TR>
<TR>
    <TD class="tRight">试题任务加分值：</TD>
    <TD class="tLeft">
        <input type="text" name="ScoreNormal" value="<?php echo ((isset($edit["ScoreNormal"]) && ($edit["ScoreNormal"] !== ""))?($edit["ScoreNormal"]):0); ?>"/>
    </TD>
</TR>
<TR>
    <TD class="tRight">入库试题加分值：</TD>
    <TD class="tLeft">
        <input type="text" name="ScoreIntro" value="<?php echo ((isset($edit["ScoreIntro"]) && ($edit["ScoreIntro"] !== ""))?($edit["ScoreIntro"]):0); ?>"/>
    </TD>
</TR>
<TR>
    <TD class="tRight">放弃标引扣分值：</TD>
    <TD class="tLeft">
        <input type="text" name="ScoreMiss" value="<?php echo ((isset($edit["ScoreMiss"]) && ($edit["ScoreMiss"] !== ""))?($edit["ScoreMiss"]):0); ?>"/>
    </TD>
</TR>
<TR>
    <TD class="tRight">图片版加分值：</TD>
    <TD class="tLeft">
        <input type="text" name="ScorePic" value="<?php echo ((isset($edit["ScorePic"]) && ($edit["ScorePic"] !== ""))?($edit["ScorePic"]):0); ?>"/>
    </TD>
</TR>
<TR>
    <TD class="tRight" width="100">是否单选：</TD>
    <TD class="tLeft" >
    <INPUT TYPE="radio"  check='Require' warning="题型是否单选题型不能为空" NAME="IfSingle" value="0" <?php if(($edit["IfSingle"]) == "0"): ?>checked="checked"<?php endif; ?>>&nbsp;是 &nbsp;&nbsp;
     <INPUT TYPE="radio" NAME="IfSingle" value="1" <?php if(($edit["IfSingle"]) == "1"): ?>checked="checked"<?php endif; ?>>&nbsp;否
     * 试题入库参数 判断是否是单选题</TD>
</TR>
<TR>
    <TD class="tRight" width="100">是否搜题：</TD>
    <TD class="tLeft" >
    <INPUT TYPE="radio"  check='Require' warning="是否设该题型为搜题参数" NAME="IfSearch" value="0" <?php if(($edit["IfSearch"]) == "0"): ?>checked="checked"<?php endif; ?>>&nbsp;是 &nbsp;&nbsp;
    <INPUT TYPE="radio" NAME="IfSearch" value="1" <?php if(($edit["IfSearch"]) == "1"): ?>checked="checked"<?php endif; ?>>&nbsp;否
    * 模板组卷参数 是否在本题型下搜索试题</TD>
</TR>
<TR>
    <TD class="tRight" width="100">是否有选择类型：</TD>
    <TD class="tLeft" >
    <INPUT TYPE="radio"  check='Require' warning="题型是否有选择类型" NAME="IfChooseType" value="0" <?php if(($edit["IfChooseType"]) == "0"): ?>checked="checked"<?php endif; ?>>&nbsp;是 &nbsp;&nbsp;
    <INPUT TYPE="radio" NAME="IfChooseType" value="1" <?php if(($edit["IfChooseType"]) == "1"): ?>checked="checked"<?php endif; ?>>&nbsp;否
    * 模板组卷参数 是否在本题型下出现选择类型</TD>
</TR>
<TR>
    <TD class="tRight" width="100">是否有选择小题：</TD>
    <TD class="tLeft" >
    <INPUT TYPE="radio"  check='Require' warning="题型是否选择小题" NAME="IfChooseNum" value="0" <?php if(($edit["IfChooseNum"]) == "0"): ?>checked="checked"<?php endif; ?>>&nbsp;是 &nbsp;&nbsp;
    <INPUT TYPE="radio" NAME="IfChooseNum" value="1" <?php if(($edit["IfChooseNum"]) == "1"): ?>checked="checked"<?php endif; ?>>&nbsp;否
    * 模板组卷参数 是否在本题型下出现带小题试题</TD>
</TR>
    <TR>
        <TD class="tRight" width="100">是否有选做题：</TD>
        <TD class="tLeft" >
            <INPUT TYPE="radio"  check='Require' warning="题型是否有选做不能为空" NAME="IfDo" value="0" <?php if(($edit["IfDo"]) == "0"): ?>checked="checked"<?php endif; ?>>&nbsp;是 &nbsp;&nbsp;
            <INPUT TYPE="radio" NAME="IfDo" value="1" <?php if(($edit["IfDo"]) == "1"): ?>checked="checked"<?php endif; ?>>&nbsp;否
            * 是否在本题型下出现选做题</TD>
    </TR>
    <TR>
        <TD class="tRight" width="100">
            选题单位：
        </TD>
        <TD class="tLeft">
            <INPUT TYPE="text" check="Require" warning="选题单位不能为空" NAME="IntelName" value="<?php echo ($edit["IntelName"]); ?>">*用于智能组卷，选取试题的数量单位(例如：个，篇，题)
        </TD>
    </TR>
    <TR>
        <TD class="tRight" width="100">选题方式：</TD>
        <TD class="tLeft" >
            <INPUT TYPE="radio"  check='Require' warning="选题方式不能为空" NAME="SelectType" value="0" <?php if(($edit["SelectType"]) == "0"): ?>checked="checked"<?php endif; ?>>&nbsp;是 &nbsp;&nbsp;
            <INPUT TYPE="radio" NAME="SelectType" value="1" <?php if(($edit["SelectType"]) == "1"): ?>checked="checked"<?php endif; ?>>&nbsp;否
            * 智能组卷方式是否忽略小题，例如：选1个完形填空出现20题 该选项为“是” 地理3个选择题选出1个试题id有3个小题的 该选项为“否”</TD>
    </TR>
    <?php if($edit["IfChooseNum"] == 0): ?><TR class="intelNum">
    <?php else: ?>
    <TR class="intelNum" style="display:none"><?php endif; ?>
        <TD class="tRight" width="100">选题数量：</TD>
        <TD class="tLeft" >
            <INPUT TYPE="text" name="IntelNum" value="<?php echo ($edit["IntelNum"]); ?>">*用于智能组卷,选题方式为'是'时在智能组卷选题方式显示每'题'(选取单位)多少小题,逗号间隔的数字为数量可选项,例如：5,15,25 （英文逗号）
        </TD>
    </TR>
<TR>
    <TD class="tRight" width="100">下划线：</TD>
    <TD class="tLeft" >
    <INPUT TYPE="radio"  check='Require' warning="下划线不能为空" NAME="Underline" value="0" <?php if(($edit["Underline"]) == "0"): ?>checked="checked"<?php endif; ?>>&nbsp;没有 &nbsp;&nbsp;
     <INPUT TYPE="radio" NAME="Underline" value="1" <?php if(($edit["Underline"]) == "1"): ?>checked="checked"<?php endif; ?>>&nbsp;有
     * 答题卡对应答题区域是否有下划线</TD>
</TR>
<TR>
    <TD class="tRight" width="100">显示题文：</TD>
    <TD class="tLeft" >
    <INPUT TYPE="radio"  check='Require' warning="显示题文不能为空" NAME="CardIfGetTest" value="0" <?php if(($edit["CardIfGetTest"]) == "0"): ?>checked="checked"<?php endif; ?>>&nbsp;不显示 &nbsp;&nbsp;
     <INPUT TYPE="radio" NAME="CardIfGetTest" value="1" <?php if(($edit["CardIfGetTest"]) == "1"): ?>checked="checked"<?php endif; ?>>&nbsp;显示
     * 答题卡对应答题区域是否需要显示题文</TD>
</TR>
<TR>
    <TD class="tRight">排序：</TD>
    <TD class="tLeft"><INPUT name="OrderID" type="text" value="<?php echo ((isset($edit["OrderID"]) && ($edit["OrderID"] !== ""))?($edit["OrderID"]):99); ?>" check='Require' warning="排序不能为空"/></TD>
</TR>
<TR>
    <TD ></TD>
    <TD class="center"><div style="width:85%;margin:5px">
        <INPUT TYPE="hidden" name="TypesID" value="<?php echo ($edit["TypesID"]); ?>">
        <INPUT TYPE="hidden" name="act" value="<?php echo ($act); ?>">
    <div class="impBtn fLeft"><INPUT tag='form1' u="<?php echo U('Types/save');?>" TYPE="button" value="保存" class="save imgButton mysubmit"></div>
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
<script>
    $(".IfPoint").change(function(){
        var ifPoint =  $("input[name='IfPoint']:checked").val();
        var output='';
        var maxlen=$("select[name=DScore]").find('option').last().val();

        if(ifPoint == 1){
            for(var j=0;j<maxlen;){
                j+=0.5;
                output+='<option value="'+j+'">'+j+'</option>';
            }
            $("select[name=DScore]").html(output);
        }else{
            for(var j=0;j<maxlen;){
                j+=1;
                output+='<option value="'+j+'">'+j+'</option>';
            }
            $("select[name=DScore]").html(output);
        }
    });

    $('input[name="IfChooseNum"]').live('click',function(){
        if($('input[name="IfChooseNum"]:checked').val()==0){
            $('.intelNum').show();
        }else{
            $('.intelNum').hide();
        }
    })
</script>
<!-- 主页面结束 -->

</body>
</html>