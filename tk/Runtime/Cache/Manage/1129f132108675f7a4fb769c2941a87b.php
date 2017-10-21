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
var URL = '/Manage/OrderList';
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
<div class="title"><?php echo ($pageName); ?></div>
<!--  功能操作区域  -->
<div class="operate">
    <!--<div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>-->
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>

    <!-- 查询区域 -->
    <FORM METHOD="POST" ACTION="/Manage/OrderList">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="name" value="<?php echo ($_REQUEST['name']); ?>" title="订单号查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR>
            <TD class="tRight" width="60">订单号：</TD>
            <TD><INPUT TYPE="text" NAME="OrderNum" class="small" value="<?php echo ($_REQUEST['OrderNum']); ?>" ></TD>
            <TD class="tRight" width="60">用户ID：</TD>
            <TD><INPUT TYPE="text" NAME="UserID" class="small" value="<?php echo ($_REQUEST['UserID']); ?>" ></TD>
            <TD class="tRight" width="80">订单时间：</TD>
            <TD width="340"><INPUT TYPE="text" NAME="Start" class="w90px inputDate" placeholder="开始时间" value="<?php echo ($_REQUEST['Start']); ?>" > - <INPUT TYPE="text" NAME="End" class="w90px inputDate" placeholder="截止时间" value="<?php echo ($_REQUEST['End']); ?>" ></TD>

            <TD class="tRight" width="50">状态：</TD>
            <TD><SELECT class="small bLeft" NAME="Status">
            <option value="">选择</option>
            <option value="1" <?php if(($_REQUEST['Status']) == "0"): ?>selected="selected"<?php endif; ?>>已付款</option>
            <option value="0" <?php if(($_REQUEST['Status']) == "1"): ?>selected="selected"<?php endif; ?>>未付款</option>
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
    <tr><td height="5" colspan="15" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th width="3%">编号</th>
        <th>订单号</th>
        <th>订单名称</th>
        <th>用户ID</th>
        <th>用户支付宝</th>
        <th>购买权限ID</th>
        <th>年费</th>
        <th>支付宝返回组状态</th>
        <th>金额</th>
        <th>支付宝返回金额</th>
        <th>状态</th>
        <th>订单时间</th>
        <th>操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
        <td><input type="checkbox" class="key" value="<?php echo ($node["OLID"]); ?>"></td>
        <td><?php echo ($node["OLID"]); ?></td>
        <td><a href="<?php echo U('Manage/OrderList/view',array('id'=>$node[OLID]));?>" ><?php echo ($node["OrderID"]); ?></a></td>
        <td><?php echo ($node["OrderName"]); ?></td>
        <td><a href="<?php echo U('User/User/index',array('UserID'=>$node['UID']));?>"><?php echo ($node["UID"]); ?></a></td>
        <td><?php echo ($node["BuyerEmail"]); ?></td>
        <td><?php echo ($node["PowerID"]); ?></td>
        <td><?php if(($node["IsYear"]) == "1"): ?>是<?php else: ?>否<?php endif; ?></td>
        <td><?php echo ($node["AliTradeStatus"]); ?></td>
        <td><?php echo ($node["TotalFee"]); ?></td>
        <td><?php echo ($node["ReturnTotal"]); ?></td>
        <td><?php if(($node["OrderStatus"]) == "1"): ?>已付款<?php else: ?><font color="red">未付款</font><?php endif; ?></td>
        <td><?php echo (date("Y-m-d H:i:s",$node["OrderTime"])); ?></td>
        <td><a href="<?php echo U('Manage/OrderList/view',array('id'=>$node[OLID]));?>">查看</a>&nbsp;</td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="14" class="bottomTd"></td></tr>
</table>
<!-- Think 系统列表组件结束 -->
</div>
<!--  分页显示区域 -->
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>