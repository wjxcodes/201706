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
var URL = '/Teacher/DocManager';
var APP     =     '';
var PUBLIC = '/Public';
</SCRIPT>
</HEAD>

<body>
<div id="loader" >页面加载中...</div>
        <!--基础文件，分别是jQuery基库和拖拽UI插件-->
        <script src="/Public/plugin/jquery.ui.draggable.js" type="text/javascript"></script>

        <!-- 对话框核心JS文件和对应的CSS文件-->
        <script src="/Public/plugin/alert/jquery.alerts.js" type="text/javascript"></script>
        <link href="/Public/plugin/alert/jquery.alerts.css" rel="stylesheet" type="text/css" media="screen" />
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content" >
<div class="title"><?php echo ($pageName); ?></div>
<!--  功能操作区域  -->
<div class="operate" style='width:100%;'>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="intro" value="标引" onclick="" class="generateWork intro imgButton"></div>
    <div style='float:right;'>
        <dt>
            <dd>下载上传提示：</dd>
            <dd>
                <dt>
                    <dd>①下载试卷后，请使用<strong class='red'>word2007以上版本</strong>，不得使用word2003及wps软件。</dd>
                    <dd>②请根据学科要求完善试题解析及相关内容，确保试题<strong class='red'>无任何错误</strong>后，<strong class='red'>不修改文件名</strong>上传。</dd>
                    <dd>③上传解析作品后，下面各环节请老师认真审查试题正确性。</dd>
                </dt>
            </dd>
        </dt>
    </div>
</div>

<!-- 功能操作区域结束 -->
<!-- 列表显示区域  -->
<div class="list" >
<div id="result" class="result none"></div>
<!-- Think 系统列表组件开始 -->
<table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
    <tr><td height="5" colspan="11" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th width="55">解析序号</th>
        <th width="50">文档编号</th>
        <th widht='100'>文档名称</th>
        <th width='70'>年级/学科</th>
        <th width="*">解析描述</th>
        <th width='80'>最后下载时间</th>
        <th width='80'>添加时间</th>
        <th width="50">下载次数/上传次数</th>
        <th width="140">状态/上传后是否审核</th>
        <th width="70">操作</th>
    </tr>
    <?php if(count($list) == 0): ?><tr><td colspan="11" align="center">暂无数据</td></tr>
    <?php else: ?>
        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
            <td><input type="checkbox" class="key" value="<?php echo ($node["DocID"]); ?>"></td>
            <td><?php echo ($node["FileID"]); ?></td>
            <td>
                <?php if($node["DocID"] == 0): ?>未上传文档
                <?php else: ?>
                    <?php echo ($node["DocID"]); endif; ?>
            </td>
            <td><?php echo ($node["DocName"]); ?></td>
            <td><?php echo ($subjects[$node['SubjectID']]['ParentName']); ?>/<?php echo ($node["SubjectName"]); ?></td>
            <td><?php echo ($node['FileDescription']); ?></td>
            <td>
                <?php if($node["LastLoad"] == "0"): ?><font color='red'>暂无历史下载</font>
                <?php else: ?>
                    <?php echo (date("Y-m-d H:i:s",(isset($node["LastLoad"]) && ($node["LastLoad"] !== ""))?($node["LastLoad"]):'暂无下载')); endif; ?>
            </td>
            <td><?php echo (date("Y-m-d H:i:s",$node["AddTime"])); ?></td>
            <td><?php echo ($node["Points"]); ?>/<?php echo ($node["uploadTimes"]); ?></td>
            <td>
                <?php if($node["DocID"] != 0 and $node["IfTask"] == 1): ?><font color="red">已生成标引任务</font><br/>
                    <?php if($node["IntroFirstTime"] > 0): ?><font color="red">试题已入库</font><?php endif; ?>
                <?php else: ?>
                    <?php if($node["uploadTimes"] == 0 or $node["DocID"] == 0): ?>未上传<br><?php if($node["CheckStatus"] == 1): ?>需审核<?php else: ?>无需审核<?php endif; ?>
                    <?php else: ?>
                        已上传<br/><font color='red'><?php if($node["CheckStatus"] == 1): ?>审核中<?php else: ?>审核通过<?php endif; ?></font><?php endif; endif; ?>

            </td>
            <td>
                <?php if($node["IntroFirstTime"] == 0): ?><a href="<?php echo U('Teacher/DocManager/down', array('fid'=>$node['FileID']));?>" class='downLink'>下载</a><br/>
                    <a href="<?php echo U('Teacher/DocManager/upload', array('fid'=>$node['FileID']));?>" class='uploadLink' points='<?php echo ($node["Points"]); ?>'>上传</a><br/>
                    <?php if($node["uploadTimes"] > 0 and $node["DocID"] > 0 and !is_null($node['IfIntro']) and $node["CheckStatus"] == 2): ?><a href="<?php echo U('Teacher/DocManager/preview', array('DocID'=>$node['DocID']));?>">提取试题</a><br/>
                        <?php if($node["IfIntro"] != 1): ?><a href="<?php echo U('Teacher/DocManager/removeDuplicate', array('DocID'=>$node['DocID']));?>">试题去重</a><br/>
                            <a href="<?php echo U('Teacher/DocManager/editTest', array('DocID'=>$node['DocID']));?>">编辑试题</a><br/>
                            <a href="<?php echo U('Teacher/DocManager/generateWork', array('docid'=>$node['DocID']));?>" onclick='return window.confirm("确定标引该试题？");'>标引试题</a><br/><?php endif; endif; endif; ?>
            </td>
            </tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
    <tr><td height="5" colspan="11" class="bottomTd"></td></tr>
</table>
<!-- Think 系统列表组件结束 -->
</div>
<!--  分页显示区域 -->
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<script src='/Public/teacher/js/common1.js'></script>
<script>
    $(document).ready(function(){
        $('.downLink').click(function(){
            $(this).siblings('.uploadLink').attr('points','1');
            return true;
        });
        $('.btdrall').click(function(){
            var keyValue = $(this).attr('thisid');
            if(!keyValue){
                keyValue = getSelectCheckboxValues();
            }
            if(!keyValue){
                alert('请选择提取项！');
                return false;
            }
            if (window.confirm('确实要提取选择项吗？')){
                location.href =  U("Teacher/DocManager/preview?id="+keyValue);
            }
        });
        $('.generateWork').click(function(){
            var keyValue = $(this).attr('thisid');
            if(!keyValue){
                keyValue = getSelectCheckboxValues();
            }
            if(!keyValue){
                alert('请选择提取项！');
                return false;
            }
            if (window.confirm('确实要标引选择项吗？')){
                location.href =  U("Teacher/DocManager/generateWork?docid="+keyValue);
            }
        });
        $('.uploadLink').each(function(){
            var that = $(this);
            that.click(function(){
                if(that.attr("points") == 0){
                    alert('请先下载试题！');
                    return false;
                }
                return true;
            }); 
        });
    });
    //获取checkbox选择项 返回数据1,数据2,数据3
    function getSelectCheckboxValues(){
        var result='';
        $('.key').each(function(){
            if($(this).attr('checked')=='checked'){
                result += $(this).val()+",";
            }
        });
        return result.substring(0, result.length-1);
    }
</script>
<!-- 主页面结束 -->

</body>
</html>