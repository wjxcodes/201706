<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title><?php echo ($pageName); ?> - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/paper.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <!--[if lte IE 6]>
    <script type="text/javascript" src="/Public/plugin/png.js"></script>
    <script>DD_belatedPNG.fix('a,img,div,span');</script>
    <![endif]-->
    <script>
    var local='<?php echo U('Index/main');?>';
    var school='<?php echo ($school); ?>';
    </script>
    <script type="text/javascript" src="/Public/default/js/common<?php echo (C("WLN_PICK_JS")); ?>.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/paper<?php echo (C("WLN_PICK_JS")); ?>.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/setWork<?php echo (C("WLN_PICK_JS")); ?>.js<?php echo (C("UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/zeroClipboard/zeroClipboard.min.js<?php echo (C("UPDATE_FILE_DATE")); ?>"></script>
</head>
<body>
    <div id="topdiv">
        <div align="center">
            <table border="0" cellpadding="0" cellspacing="0">
            <tr><td><a id="titleicon"></a></td>
            <td><span id="toptitle">组卷中心</span><span id="tmp"></span></td></tr>
            </table>
        </div>
    </div>

    <div id="main">
        <div id="leftdiv" style="width:240px;">
            <div id="paperview_title">试卷预览-》</div>
            <div id="paperstruct">
                <div id="paperstruct_head" style="_position:static;"></div>
                <div id="paperstruct_body" onselectstart="return false"></div>
            </div>
        </div>
        <div id="rightdiv" style="left:240px;">
            <div id="pui_box">
            <div id="pui_root" align="left"></div>
            </div>
        </div>
        <div id="rightmenu">
        </div>
    </div>
<script>
var Types=<?php echo ($Types); ?>;
var key='<?php echo ($key); ?>';
</script>
<script type="text/javascript" src="/Public/default/js/zujuan<?php echo (C("WLN_PICK_JS")); ?>.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/default/js/workdown<?php echo (C("WLN_PICK_JS")); ?>.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript">
    $(document).ready(function(){
        // 试卷生成提示信息&& loading
        if($('#quescount', window.parent.document).html()==0){
             $.myCommon.loadingHide();
             var tmp = '<p class="c">当前试卷中没有试题！</p>';
             $.myDialog.normalMsgBox('msgdiv','提示信息',500,tmp,4)
        }else{
            $.myCommon.loadingShow('正在生成试卷...');
        }
    })
</script>
<div style="display:none;">
    <?php echo (C("WLN_STATISTICAL_CODE.HOME")); ?>
</div>
</body>
</html>