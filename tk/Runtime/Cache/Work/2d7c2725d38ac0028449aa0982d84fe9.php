<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title><?php echo ($pageName); ?> - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/work.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
</head>
<body>
<div id="workDiv">
    <div id="workBox" class="myClass PublicBox">
        <div class="wLeft classList">
            <div class="classTit">班级列表</div>
            <a class="addClass"><span><b>+</b>添加班级</span></a>
            <div class="loadClass">
                <div class="prev" onselectstart="return false;" oncontextmenu="return false" title="向上滚动">向上移动</div>
                <div class="bd"></div>
                <div class="next" onselectstart="return false;" oncontextmenu="return false" title="向下滚动">向下移动</div>
            </div>
        </div>
        <div class="wRight">
            <div id="rightTop">
                <div id="categorylocation">
                    <span class="newPath">当前位置：</span>> <span id="loca_text"><span></span> > 作业模块 > 我的班级</span>
                </div>
            </div>
            <div class="titInfo">
                <div class="current" tid='1'>班级信息</div>
                <div tid='2'>学生信息</div>
                <div tid='3'>教师信息</div>
                <div tid='4'>班级动态</div>
            </div>
            <div class="dataInfo">
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="/Public/plugin/jquery.SuperSlide.2.1.1.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script>
    var local='<?php echo U('MyClass/myClass');?>';
    var key='<?php echo ($key); ?>';
    var userName="<?php echo ($username); ?>";
    var gradeList=<?php echo ($gradeList); ?>;
</script>
<script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<!--[if lte IE 6]>
<script type="text/javascript" src="/Public/plugin/png.js"></script>
<script>DD_belatedPNG.fix('a,img,div,span');</script>
<![endif]-->
<script type="text/javascript" src="/Public/plugin/uploadify/jquery.uploadify.min.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<link rel="stylesheet" type="text/css" href="/Public/plugin/uploadify/uploadify.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>">

<script type="text/javascript" src="/Public/default/js/myClass.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>

<script>
var subject = <?php echo ($subject); ?>;
$(function(){
    var subjectID=Cookie.Get("SubjectId");
    $('#loca_text span').html(parent.jQuery.myMain.getQuesBank(subjectID)['SubjectName']);
    $.myClass.init();
    $('.t_changesubject').live('click',function(){
    	 var tmp_str='变更学科信息为：<select id="t_chsubject">';
    		for(var i in subject[21]['sub']){
    			tmp_str += '<option value="'+subject[21]['sub'][i]['SubjectName'].substr(2)+'">'+subject[21]['sub'][i]['SubjectName'].substr(2)+'</option>';
    		}
    		tmp_str += '</select><div id="changesubjectdiv_id" uid="'+$(this).attr('uid')+'"></div>';
    		$.myDialog.normalMsgBox('changesubjectdiv','修改学科',450,tmp_str,3);
    });
});
</script>

</body>
</html>