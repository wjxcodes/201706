<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<TITLE>『<?php echo (C("WLN_WEB_NAME")); ?>管理平台』</TITLE>
<link rel="stylesheet" type="text/css" href="/Public/teacher/css/style.css" />
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="/Public/plugin/jquery.newplaceholder.js"></script>
<script type="text/javascript" src="/Public/teacher/js/common1.js"></script>
<script type="text/javascript" src="/Public/teacher/js/common.js"></script>

<SCRIPT LANGUAGE="JavaScript">
//指定当前组模块URL地址 
var URL = '/Teacher/CustomCheck';
var APP     =     '';
var PUBLIC = '/Public';
</SCRIPT>
</HEAD>

<body>
<div id="loader" >页面加载中...</div>
<!-- 主页面开始 -->
<div id="main" class="main" >
    <!-- 主体内容  -->
    <div class="content" >
        <div class="title"><?php echo ($pageName); ?></div>
        <!--  功能操作区域  -->
        <div class="operate">
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
            <!-- 查询区域 -->
            <form id="form1" method="POST" acTion="<?php echo U('Teacher/CustomCheck/index');?>">
                <div class="fRig">
                    <div class="fLeft"><span id="key"><input id="name" type="text" name="TestID" value="<?php echo ($_REQUEST['name']); ?>" placeholder="用户名查询" title="用户名查询" class="medium" ></span></div>
                    <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
                    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
                </div>
                <!-- 高级查询区域 -->
                <div id="searchM" class=" none search cBoth">
                    <table border="0" cellpadding="1" cellspacing="3" width="100%">
                        <tr>
                            <td class="tRight" width="80">试题状态：</TD>
                            <td>
                                <select class="medium bLeft" name="Status">
                                    <option value="">选择</option>
                                    <?php if(is_array($testStatus)): $i = 0; $__LIST__ = $testStatus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
        <!-- 功能操作区域结束 -->
        <!-- 列表显示区域  -->
        <div class="list" >
            <div id="result" class="result none"></div>
            <!-- Think 系统列表组件开始 -->
            <table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
                <tr><td height="5" colspan="13" class="topTd" ></td></tr>
                <tr class="row" >
                    <th width="75">试题编号</th>
                    <th width="80">试题添加时间</th>
                    <th width="80">任务领取时间</th>
                    <th width="80">试题作者</th>
                    <th width="80">任务完成人</th>
                    <th width='80'>任务返回次数</th>
                    <th width='80'>是否删除</th>
                    <th width="80">是否锁定</th>
                    <th width='80'>是否库内题</th>
                    <th width='150'>需要删除试题描述</th>
                    <th width="120">状态</th>
                    <th width="70">操作</th>
                </tr>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
                    <td><?php echo ($node["TestID"]); ?></td>
                    <td><?php echo (date("Y-m-d H:i",$node["AddTime"])); ?></td>
                    <td><?php echo (date("Y-m-d H:i",$node["TaskTime"])); ?></td>
                    <td><?php echo ($node["TestAuthorRealName"]); ?>（<?php echo ($node["TestAuthorName"]); ?>）</td>
                    <td><?php echo ($node["TaskRealName"]); ?>（<?php echo ($node["TaskUserName"]); ?>）</td>
                    <td><?php echo ($node["BackTimes"]); ?></td>
                    <td><?php if($node["IfDel"] == 1): ?>是<?php else: ?>否<?php endif; ?></td>
                    <td><?php if($node["IfLock"] == 1): ?>是<?php else: ?>否<?php endif; ?></td>
                    <td><?php if($node["IfIntro"] != 0): ?>是<?php else: ?>否<?php endif; ?></td>
                    <td><?php echo ($node["ErrorMsg"]); ?></td>
                    <td><?php echo ($node["StatusName"]); ?></td>
                    <td><?php if($node["Status"] == 1): ?><a href="<?php echo U('Teacher/CustomCheck/checkWork', array('statusID'=>$node['StatusID']));?>">审核</a>
                        <?php else: ?><span style="color:#ccc">审核</span><br /><?php endif; ?></td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <tr><td height="5" colspan="13" class="bottomTd"></td></tr>
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