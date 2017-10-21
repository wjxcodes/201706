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
    <div class="content" >
        <div class="title"><?php echo ($pageName); ?> <a href="<?php echo U('Subject/updateCache');?>">更新缓存</a></div>
        <!--  功能操作区域  -->
        <div class="operate">
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="新增" onclick="" class="btadd add imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="编辑" onclick="" class="btedit edit imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
        </div>
        <!-- 功能操作区域结束 -->

        <!-- 列表显示区域  -->
        <div class="list" >
            <div id="result" class="result none"></div>
            <!-- Think 系统列表组件开始 -->
            <table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
                <tr><td height="5" colspan="10" class="topTd" ></td></tr>
                <tr class="row" >
                    <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
                    <th width="10%">编号</th>
                    <th>学科名称</th>
                    <th>学科属性</th>
                    <th>试卷总分</th>
                    <th>答题时间</th>
                    <th>格式化doc</th>
                    <th>答题卡版式</th>
                    <th>序号</th>
                    <th>操作</th>
                </tr>
                <?php if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
                    <td><input type="checkbox" class="key" value="<?php echo ($node["SubjectID"]); ?>"></td>
                    <td><?php echo ($node["SubjectID"]); ?></td>
                    <td><a href="#" class="btedit" thisid="<?php echo ($node["SubjectID"]); ?>"><?php echo ($node["SubjectName"]); ?></a></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><?php echo ($node["OrderID"]); ?></td>
                    <td><a href="#" class="btedit" thisid="<?php echo ($node["SubjectID"]); ?>">编辑</a>&nbsp;</td>
                </tr>
                <?php if(is_array($node['sub'])): $i = 0; $__LIST__ = $node['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
                    <td><input type="checkbox" class="key" value="<?php echo ($sub["SubjectID"]); ?>"></td>
                    <td>　　<?php echo ($sub["SubjectID"]); ?></td>
                    <td>　　<a href="#" class="btedit" thisid="<?php echo ($sub["SubjectID"]); ?>"><?php echo ($sub["SubjectName"]); ?></a></td>
                    <td><?php if($sub["Style"] == 3): ?>文理通用<?php elseif($sub["Style"] == 2): ?>理科<?php elseif($sub["Style"] == 1): ?>文科<?php else: ?>无<?php endif; ?></td>
                    <td><?php echo ($sub["TotalScore"]); ?>分</td>
                    <td><?php echo ($sub["TestTime"]); ?>分钟</td>
                    <td><?php if($sub["FormatDoc"] == 0): ?>自动识别（公式居中优先）<?php endif; ?>
                        <?php if($sub["FormatDoc"] == 1): ?>垂直自适应（公式居中）<?php endif; ?>
                        <?php if($sub["FormatDoc"] == 2): ?>垂直居中（图片居中）<?php endif; ?></td>
                    <td><?php echo ($sub["Layout"]); ?></td>
                    <td><?php echo ($sub["OrderID"]); ?></td>
                    <td><a href="#" class="btedit" thisid="<?php echo ($sub["SubjectID"]); ?>">编辑</a>&nbsp;</td>
                </tr><?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
                <tr><td height="5" colspan="10" class="bottomTd"></td></tr>
            </table>
            <!-- Think 系统列表组件结束 -->
        </div>
        <!-- 列表显示区域结束 -->
    </div>
    <!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>