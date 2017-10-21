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
var URL = '/Manage/PowerUserList';
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
        <div class="title"><?php echo ($pageName); ?> [<a href="<?php echo U('PowerUserList/check');?>">未设置权限列表</a>] [<a href="<?php echo U('PowerUserList/updateCache');?>">更新缓存</a>]</div>
        <!--  功能操作区域  -->
        <div class="operate">
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="新增" onclick="" class="btadd add imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="编辑" onclick="" class="btedit edit imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
            <FORM id="form1" METHOD="POST" ACTION="/Manage/PowerUserList">
                <div class="fRig">
                    <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="PowerName" value="<?php echo ($_REQUEST['PowerName']); ?>" title="用户组权限名查询" class="medium" ></span></div>
                    <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
                    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
                </div>
                <!-- 高级查询区域 -->
                <div id="searchM" class=" none search cBoth">
                    <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
                        <TR>
                            <TD class="tRight" width="80">权限名称：</TD>
                            <TD ><INPUT TYPE="text" NAME="Power" class="small" value="<?php echo ($_REQUEST['Power']); ?>"></TD>
                            <TD class="tRight" width="80">权限代码：</TD>
                            <TD ><INPUT TYPE="text" NAME="PowerTag" class="small" value="<?php echo ($_REQUEST['PowerTag']); ?>"></TD>
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
                <tr><td height="5" colspan="10" class="topTd" ></td></tr>
                <tr class="row" >
                    <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
                    <th width="10%">编号</th>
                    <th>用户权限名称</th>
                    <th>权限代码</th>
                    <th>权限值</th>
                    <th>权限所属组</th>
                    <th>单位</th>
                    <th>权限序号</th>
                    <th>权限计算周期</th>
                    <th>操作</th>
                </tr>
                <?php if(is_array($powerArray)): $i = 0; $__LIST__ = $powerArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cnode): $mod = ($i % 2 );++$i; if(is_array($cnode)): $i = 0; $__LIST__ = $cnode;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
                    <td><input type="checkbox" class="key" value="<?php echo ($node["ListID"]); ?>"></td>
                    <td><?php echo ($node["ListID"]); ?></td>
                    <td><a href="#" class="btedit" thisid="<?php echo ($node["ListID"]); ?>"><?php echo ($node["PowerName"]); ?></a></td>
                    <td><?php echo ($node["PowerTag"]); ?></td>
                    <td><?php echo ($node["Value"]); ?></td>
                    <td><?php echo ($node["GroupName"]); ?></td>
                    <td><?php echo ((isset($node["TypeName"]) && ($node["TypeName"] !== ""))?($node["TypeName"]):"未设置"); ?></td>
                    <td><?php echo ($node["OrderID"]); ?></td>
                    <td><?php echo ($powerCycle[$node['Unit']]); ?></td>
                    <td><a href="#" class="btedit" thisid="<?php echo ($node["ListID"]); ?>">编辑</a>&nbsp;</td>
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