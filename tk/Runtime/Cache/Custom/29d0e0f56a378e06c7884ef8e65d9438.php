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
var URL = '/Custom/CustomTest';
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
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
    <!-- 查询区域 -->
    <FORM METHOD="POST" ACTION="/Custom/CustomTest">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="name" value="<?php echo ($_REQUEST['name']); ?>" title="试题编号查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR><TD class="tRight" width="80">试题编号：</TD>
            <TD><INPUT TYPE="text" NAME="TestID" class="small" value="<?php echo ($_REQUEST['TestID']); ?>" ></TD>
            <TD class="tRight" width="80">文档编号：</TD>
            <TD><INPUT TYPE="text" NAME="docid" class="small" value="<?php echo ($_REQUEST['docid']); ?>" ></TD>
            <TD class="tRight" width="80">所属学科：</TD>
            <TD><SELECT id="subject" class="normal bLeft" NAME="SubjectID" check='Require' warning="所属学科不能为空">
            <option value="">请选择</option>
            <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label='<?php echo ($vo["SubjectName"]); ?>'>
                <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $edit["SubjectID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php else: ?>
                <option value="0">请添加学科</option><?php endif; ?>
            </SELECT></TD><TD class="tRight" width="50">状态：</TD>
            <TD><SELECT class="w90px bLeft" NAME="Status">
            <option value="">—请选择—</option>
            <?php if(is_array($testStatus)): $i = 0; $__LIST__ = $testStatus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            </SELECT></TD>
            <TD class="tRight" width="100">原创模板试题：</TD>
            <TD>
                <SELECT class="w90px bLeft" NAME="IsTpl">
                    <option value="">全部</option>
                    <option value="0">否</option>
                    <option value="1">是</option>
                </SELECT>
            </TD>
        </tr>
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
    <tr><td height="5" colspan="9" class="topTd" ></td></tr>
    <tr class="row" style='text-align:center'>
        <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th width="40">编号</th>
        <th width="350">试题内容</th>
        <th width="50">试题状态/是否模板试题</th>
        <th width="200">学科/题型/类型/难度/年级/用户名</th>
        <th width="100">技能/能力</th>
        <th width="60">添加时间</th>
        <th width="60">操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
        <td><input type="checkbox" class="key" value="<?php echo ($node["TestID"]); ?>"></td>
        <td>
        <?php echo ($node["TestID"]); ?>
        </td>
        <td width="400">
            <div class="text_source">来源：<a href="" title=""><?php echo ($node["Source"]); ?></a></div>
            <div class="testdivbak" style="height:120px; width:500px; overflow-Y:scroll">
                <a href="javascript:void(0);" class="" thisid="<?php echo ($node["TestID"]); ?>"><p><?php echo ((isset($node["Test"]) && ($node["Test"] !== ""))?($node["Test"]):"无</p>"); ?></a>
            </div>
        </td>
        <td id="error<?php echo ($node["TestID"]); ?>">
            <font color="red"><?php echo ($node["Status"]); ?></font><br>
            <?php if($node["IsTpl"] > 0): ?>是
            <?php else: ?>
            否<?php endif; ?>
        </td>
        <td><?php echo ((isset($node["SubjectName"]) && ($node["SubjectName"] !== ""))?($node["SubjectName"]):"<font color='red'>无</font>"); ?><br/>
            <span id="types<?php echo ($node["TestID"]); ?>"><?php echo ((isset($node["TypesName"]) && ($node["TypesName"] !== ""))?($node["TypesName"]):"<font color='red'>无</font>"); ?></span><br/>
            <span id="choose<?php echo ($node["TestID"]); ?>"><?php if(($node["IfChoose"]) == "0"): ?>非选择题<?php endif; if(($node["IfChoose"]) == "1"): ?>复合题<?php endif; if(($node["IfChoose"]) == "2"): ?>多选题<?php endif; if(($node["IfChoose"]) == "3"): ?>单选题<?php endif; ?></span><br/>
            <span id="diff<?php echo ($node["TestID"]); ?>"><?php echo ((isset($node["Diff"]) && ($node["Diff"] !== ""))?($node["Diff"]):"<font color='red'>无</font>"); ?></span><br/>
            <span id="grade<?php echo ($node["TestID"]); ?>">【<?php echo ((isset($node["GradeName"]) && ($node["GradeName"] !== ""))?($node["GradeName"]):"<font color='red'>无</font>"); ?>】</span><br>
            <span >【<?php echo ($node["UserName"]); ?>】</span>
        </td>
        <td id="knowledge<?php echo ($node["TestID"]); ?>"><?php echo ((isset($node["haveSkill"]) && ($node["haveSkill"] !== ""))?($node["haveSkill"]):"<font color='red'>未标注技能</font>"); ?><br><?php echo ((isset($node["haveCap"]) && ($node["haveCap"] !== ""))?($node["haveCap"]):"<font color='red'>未标注能力</font>"); ?></td>
        <td id="knowledge<?php echo ($node["TestID"]); ?>"><?php echo (date("Y-m-d H:i:s",$node["AddTime"])); ?></td>
        <td>
            <a href="<?php echo U('Custom/CustomTest/edit',array('id'=>$node[TestID]));?>" class="showmsg" thisid="<?php echo ($node["PaperID"]); ?>">编辑</a>&nbsp;
            <a href="<?php echo U('Custom/CustomTest/delete',array('id'=>$node[TestID]));?>" class="" thisid="<?php echo ($node["PaperID"]); ?>">删除</a>&nbsp;
        </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="9" class="bottomTd"></td></tr>
</table>
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