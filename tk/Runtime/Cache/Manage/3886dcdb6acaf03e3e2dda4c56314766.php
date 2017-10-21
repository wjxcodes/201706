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
var URL = '/Manage/Chapter';
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
<div class="title"><?php echo ($pageName); ?> <a href="<?php echo U('Chapter/updateCache');?>">生成缓存</a></div>
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="新增" onclick="" class="btadd add imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="编辑" onclick="" class="btedit edit imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
</div>
    <div style="display: inline-block;float: right;margin: 8px 3px;">(注：章节是否显示，遵循父类隐藏，子类继承规则；若想显示该章节，则要确定该章节的父类也是可显示的)</div>
<!-- 功能操作区域结束 -->

<!-- 列表显示区域  -->
<div class="list" >
<div id="result" class="result none"><?php echo ($chapter_path); ?></div>
<!-- Think 系统列表组件开始 -->
<table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
    <tr><td colspan="6"><?php echo ($chapterPath); ?></td></tr>
    <tr><td height="5" colspan="6" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th width="10%">编号</th>
        <th>章节名称</th>
        <th>所属学科</th>
        <th>是否显示</th>
        <th>操作</th>
    </tr>
    <?php if(is_array($chapterArray)): $i = 0; $__LIST__ = $chapterArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
            <td><input type="checkbox" class="key" value="<?php echo ($node["ChapterID"]); ?>"></td>
            <td><?php echo ($node["ChapterID"]); ?></td>
            <td>
                <?php if($node["HaveChild"] == '＋ '): ?><a href="<?php echo U('Chapter/index',array('pID'=>$node['ChapterID'],'ifShow'=>$node['IfShow']));?>" ><?php echo ($node["HaveChild"]); echo ($node["ChapterName"]); ?></a>
                <?php else: ?>
                <?php echo ($node["HaveChild"]); echo ($node["ChapterName"]); endif; ?>
            </td>
            <td><?php echo ($node["SubjectName"]); ?></td>
            <td><?php if($node["IfShow"] == 1): ?>是<?php else: ?>否<?php endif; ?></td>
            <td>
                <a href="#" class="btedit" thisid="<?php echo ($node["ChapterID"]); ?>">编辑</a>&nbsp;
                <?php if($node["HaveChild"] == '＋ '): ?><a href="<?php echo U('Chapter/index',array('pID'=>$node['ChapterID'],'ifShow'=>$node['IfShow']));?>" >查看子类</a><?php endif; ?>
                <a href="<?php echo U('Chapter/add',array('pID'=>$node['ChapterID']));?>" >添加子类</a>
                <?php echo ($node["Order"]); ?>
            </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="7" class="bottomTd"></td></tr>
</table>
<!-- Think 系统列表组件结束 -->
</div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->
<script>
$('.orderup').live('click',function(){
    var _this=$(this);
    var tr=_this.parent().parent();
    $.get(U(_this.attr('url')),function(data){
        //权限验证
        if(checkPower(data)=='error'){
            return false;
        }
        if(data['data']=='success'){
            tr.prev().before(tr);
        }else{
            alert(data['data']);
        }
    });
});
$('.orderdown').live('click',function(){
    var _this=$(this);
    var tr=_this.parent().parent();
    $.get(U(_this.attr('url')),function(data){
        //权限验证
        if(checkPower(data)=='error'){
            return false;
        }
        if(data['data']=='success'){
            tr.next().after(tr);
        }else{
            alert(data['data']);
        }
    });
});
</script>

</body>
</html>