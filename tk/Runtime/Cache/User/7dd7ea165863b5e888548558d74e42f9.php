<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>组卷历史下载</title>
<meta id="keywordsmeta" name="keywords" content="在线组卷系统,下载,组卷记录"/>
<meta id="descriptmeta" name="description" content="在线组卷系统组卷历史下载"/>
    <link type="text/css" href="/Public/default/css/common.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/user.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script>
    var local='<?php echo U('Index/main');?>';
    </script>
    <script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/paper.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
</head>
<body>
<div id="righttop">
    <div id="categorylocation">
        <span class="nowPath">当前位置：</span>> <span id="loca_text"><span></span> > 用户档案 > 任务列表 </span>
    </div>
</div>

<div id="divbox" class="sc_list_box">
    <div id="paperinfod" class="task-list-top clearfix">
        <div class="left search-task-list" id='sc_sx'>
            <span id="J_Date">
                开始时间：<input type="text" name='startTime' class='form-date startDate'/>
                截止时间：<input type="text" name='endTime' class='form-date endDate'/>
            </span>
            任务等级：
            <select name="level" id="" style="width:70px">
                <option value="">请选择</option>
                <?php if(is_array($level)): foreach($level as $key=>$val): ?><option value="<?php echo ($key); ?>"><?php echo ($val); ?></option><?php endforeach; endif; ?>
            </select>
            任务状态：
            <select name="status" style="width:70px">
                <option value="-1">请选择</option>
                <option value="0">申请中</option>
                <option value="1">已领取</option>
                <option value="2">终止的任务</option>
                <option value="3">拒绝</option>
                <option value="4">完成</option>
            </select>
            <a href="javascript:;" class="search-btn nor-btn" id='search'>查询</a>
        </div>
        <div id="searchinfo" class="right">
             共存档<a id="quescount">0</a>套卷 <a class="prev_page" title="上一页"></a>当前第
            <a id="curpage">1</a><a id="selectpageicon" style="display: inline-block;"></a>
            /<a id="pagecount">1</a>页 <a class="next_page" title="下一页"></a>
        </div>
    </div>
    <div style="z-index:2;" id="paperlistbox">
    </div>
    <div style="z-index: 1; display: block;" id="pagelistbox">
        <div class="pagebox">
        <span class="disabled">首页</span>
        <span class="current">1</span>
        <span class="disabled">末页</span>
        </div>
    </div>
</div>
<script type="text/javascript" src="/Public/default/js/myTask.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript">
$.MyTask.init();
</script>

</body>
</html>