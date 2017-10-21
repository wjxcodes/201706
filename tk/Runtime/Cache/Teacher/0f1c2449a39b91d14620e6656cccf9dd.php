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
        <div class="operate">
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
            <!-- 查询区域 -->
            <form id="form1" method="POST" accion="<?php echo U('Teacher/CustomIntro/taskTestList');?>">
                <div class="fRig">
                    <div class="fLeft"><span id="key"><input id="name" type="text" name="TestID" value="<?php echo ($_REQUEST['TestID']); ?>"
                                                             placeholder="试题ID查询" title="试题ID查询" class="medium" ></span></div>
                    <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
                </div>
            </form>
        </div>
        <!-- 列表显示区域  -->
        <div class="list" >
            <div id="result" class="result none"></div>
            <!-- Think 系统列表组件开始 -->
            <table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
                <tr><td height="5" colspan="8" class="topTd" ></td></tr>
                <tr class="row" >
                    <th width="30">编号</th>
                    <th>试题ID</th>
                    <th>学科</th>
                    <th>试题添加时间</th>
                    <th>试题最后更新时间</th>
                    <th>试题作者</th>
                    <th>操作</th>
                </tr>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
                        <td><?php echo ($i); ?></td>
                        <td><?php echo ($node["TestID"]); ?></td>
                        <td><?php echo ($node["SubjectName"]); ?></td>
                        <td><?php echo (date('Y-m-d H:i',$node["AddTime"])); ?></td>
                        <td><?php if($node["LastUpdateTime"] > 0): echo (date('Y-m-d H:i',$node["LastUpdateTime"])); else: ?>无<?php endif; ?></td>
                        <td><?php echo ($node["RealName"]); ?>（<?php echo ($node["UserName"]); ?>）</td>
                        <td>
                            <a href='<?php echo U('Teacher/CustomIntro/getTaskTest', array('testID'=>$node['TestID']));?>'>领取</a>&nbsp;&nbsp;<br/>
                        </td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <tr><td height="5" colspan="8" class="bottomTd"></td></tr>
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