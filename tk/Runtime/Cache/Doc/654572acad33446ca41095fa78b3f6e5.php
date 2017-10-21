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
var URL = '/Doc/WlnDoc';
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
<div class="title"><?php echo ($pageName); ?> [ <A HREF="<?php echo U('Doc/WlnDoc/xlslist');?>">返回列表</A> ]  [ <A
        HREF="javascript:history.go(-1);">返回上一页</A> ]</div>
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="导入" onclick="" class="xlsdr add imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
</div>
<!-- 功能操作区域结束 -->

<!-- 列表显示区域  -->
<div class="list" >
<div id="result" class="result none"></div>
<!-- Think 系统列表组件开始 -->
<form id="form1" name="form1" action="?" method="post">
<table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
    <tr><td height="5" colspan="8" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="20"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <?php if(is_array($tag_array)): $i = 0; $__LIST__ = $tag_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><th><?php echo ($vo["TagName"]); ?></th><?php endforeach; endif; else: echo "" ;endif; ?>
		<!-- <th>章节</th> -->
    </tr>
    <?php if(is_array($data)): $t = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$no): $mod = ($t % 2 );++$t;?><tr class="row lists" jl=''>
        <td><input type="checkbox" name="key[]" class="key" value="<?php echo ($t); ?>"></td>
        <td><div style="width:200px;height:100px;overflow:auto;"><p><?php echo ($no["A"]); ?></td>
        <td><div style="width:200px;height:100px;overflow:auto;"><p><?php echo ($no["B"]); ?></td>
		<td><div style="width:200px;height:100px;overflow:auto;"><p><?php echo ($no["C"]); ?></td>
		<td><div style="width:200px;height:100px;overflow:auto;"><p><?php echo ($no["D"]); ?></td>
		<td><div style="width:200px;height:100px;overflow:auto;"><p><?php echo ($no["E"]); ?></td>
		<!-- <td><div style="width:200px;height:100px;overflow:auto;"><p><?php echo ($no["F"]); ?></td> -->
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="8" class="bottomTd"></td></tr>
</table>
<input name="DocID" type="hidden" value="<?php echo ($edit["DocID"]); ?>"/>
<?php if(is_array($start)): $i = 0; $__LIST__ = $start;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><input name="start[]" type="hidden" value="<?php echo ($vo); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
<?php if(is_array($testfield)): $i = 0; $__LIST__ = $testfield;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><input name="testfield[]" type="hidden" value="<?php echo ($vo); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
</form>
<!-- Think 系统列表组件结束 -->
</div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<script>
//手动提取试题
    if($('.xlsdr').length>0){
        $('.xlsdr').click(function(){
            var keyValue  = getSelectCheckboxValues();
                if(!keyValue){
                    alert('请选择提取项！');
                    return false;
                }
                if (window.confirm('确实要提取吗？如果已提取过则覆盖原有数据')){
                    $('#form1').attr('action',U(URL+"/testsavexls"));
                    $('#form1').submit();
                }
        });
    }
</script>
<!-- 主页面结束 -->

</body>
</html>