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
var URL = '/Teacher/CustomIntro';
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
        <!-- 列表显示区域  -->
        <div class="operate">
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
            <!-- 查询区域 -->
            <form id="form1" method="POST" action="<?php echo U('Teacher/CustomIntro/individualTestList');?>">
            <div class="fRig">
                <div class="fLeft"><span id="key"><input id="name" type="text" name="name" value="<?php echo ($_REQUEST['name']); ?>" placeholder="用户名查询" title="用户名查询" class="medium" ></span></div>
                <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
                <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
            </div>
            <!-- 高级查询区域 -->
            <div id="searchM" class=" none search cBoth">
                <table border="0" cellpadding="1" cellspacing="3" width="100%">
                <tr>
                    <td class="tRight" width="60">试题ID：</td>
                    <td>
                        <input type="text" name="TestID" class="small" value="<?php echo ($_REQUEST['TestID']); ?>" >
                    </td>
                    <td class="tRight" width="60">日期：</td>
                    <td width="200">
                        <input type="text" name="Start" class="small inputDate" value="<?php echo ($_REQUEST['Start']); ?>"> - 
                        <input type="text" name="End" class="small inputDate" value="<?php echo ($_REQUEST['End']); ?>">
                    </td>
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
        <div class="list" >
            <div id="result" class="result none"></div>
            <!-- Think 系统列表组件开始 -->
            <table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
                <tr><td height="5" colspan="10" class="topTd" ></td></tr>
                <tr class="row" >
                    <th width="30">编号</th>
                    <th>试题ID</th>
                    <th>任务领取人</th>
                    <th>任务领取时间</th>
                    <th>过期时间</th>
                    <th>试题作者</th>
                    <th>试题添加时间</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
                        <td><?php echo ($i); ?></td>
                        <td><?php echo ($node["TestID"]); ?></td>
                        <td><?php echo ($node["CheckUser"]); ?></td>
                        <td><?php echo (date('Y-m-d H:i',$node["TaskStartTime"])); ?></td>
                        <td>
                            <?php if($node["StatusName"] == "已过期"): ?><strong style="color: #ff0000"><?php echo (date('Y-m-d H:i',$node["TaskTime"])); ?></strong>
                            <?php else: ?>
                            <?php echo (date('Y-m-d H:i',$node["TaskTime"])); endif; ?>
                        </td>
                        <td><?php echo ($node["TestAuthorRealName"]); ?>（<?php echo ($node["TestAuthorName"]); ?>）</td>
                        <td><?php echo (date('Y-m-d H:i',$node["AddTime"])); ?></td>
                        <td><?php echo ($node["StatusName"]); ?></td>
                        <td>
                            <?php if($node["IfDo"] != 0): ?><a href='<?php echo U('Teacher/CustomIntro/similarTestList', array('testID'=>$node['TestID']));?>'>相似试题</a>&nbsp;&nbsp;<br/>
                            <a href='<?php echo U('Teacher/CustomIntro/introTest', array('testID'=>$node['TestID']));?>'>编辑</a>&nbsp;&nbsp;
                            <?php else: ?>
                                <span style="color:#ccc">相似试题</span><br />
                                <span style="color:#ccc">编辑</span><?php endif; ?>
                        </td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <tr><td height="5" colspan="10" class="bottomTd"></td></tr>
            </table>
            <!-- Think 系统列表组件结束 -->
        </div>
        <!--  分页显示区域 -->
        <div class="page"><?php echo ($page); ?></div>
        <!-- 主体内容结束 -->
    </div>
</div>
<!-- 主页面结束 -->

</body>
</html>