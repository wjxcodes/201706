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
var URL = '/Statistics/StatisticsB';
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
        <div class="title"><?php echo ($pageName); ?>  <A HREF="<?php echo U('StatisticsB/menuList');?>">统计菜单</A> </div>
        <!--  功能操作区域  -->
        <div class="operate">

            <!-- 查询区域 -->
            <FORM METHOD="POST" ACTION="<?php echo U('StatisticsB/adminWork');?>" id="ll">

                <div class="fRig">
                    <table cellspacing="3" cellpadding="1" border="0" width="100%">
                        <tbody><tr>
                            <td width="60" class="tRight">日期：</td>
                            <td width="350"><input type="text"  class="medium inputTime" value="<?php echo ($_GET['Start']); ?>" name="Start"> -
                                <input type="text" value="<?php echo ($_GET['End']); ?>" class="medium inputTime" name="End">
                            </td>
                            <td> <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div></td>
                        </tr>
                        </tbody>
                    </table>
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
                <tr class="row" >
                    <th width="30%">管理员</th>
                    <th width="20%">试卷数量</th>
                </tr>
                <?php if(is_array($docBuffer)): $i = 0; $__LIST__ = $docBuffer;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$con): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
                    <td><?php echo ($con["Admin"]); ?></td>
                    <td><?php echo ($con["Num"]); ?></td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <tr><td height="5" colspan="9" class="bottomTd"></td></tr>
            </table>
            <!-- Think 系统列表组件结束 -->
            <!-- Think 系统列表组件开始 -->
            <table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
                <tr><td height="5" colspan="9" class="topTd" ></td></tr>
                <tr class="row" >
                    <th width="30%">管理员</th>
                    <th width="20%">入库试题数量</th>

                </tr>
                <?php if(is_array($testBuffer)): $i = 0; $__LIST__ = $testBuffer;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$con): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
                    <td><?php echo ($con["Admin"]); ?></td>
                    <td><?php echo ($con["Num"]); ?></a></td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <tr><td height="5" colspan="9" class="bottomTd"></td></tr>
            </table>
            <!-- Think 系统列表组件结束 -->
        </div>
        <!--  分页显示区域 -->
        <!-- <div><?php echo ($page); ?></div> -->
        <div style="float:right"><?php echo ($pages); ?></div>
        <!-- 列表显示区域结束 -->
    </div>
    <!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>
<script>
    $("input[type=submit]").click(function(){
        var start= $("input[name=Start]").val();
        if(start == ''){
            alert('开始日期不能为空');
            return false;
        }else{
            $("#ll").submit();
        }

        return false;
    })
</script>