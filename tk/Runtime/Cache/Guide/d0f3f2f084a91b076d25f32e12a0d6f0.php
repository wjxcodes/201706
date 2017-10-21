<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title><?php echo ($pageName); ?> - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/teachPlan.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/paper.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/ueditor/ueditor.config.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/ueditor/ueditor.all.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/editor.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <!--[if lte IE 6]>
    <script type="text/javascript" src="/Public/plugin/png.js"></script>
    <script>DD_belatedPNG.fix('a,img,div,span');</script>
    <![endif]-->
</head>
<body>
<div id="righttop">
    <div id="categorylocation">
        <span class="nowPath">当前位置：</span>> <span id="loca_text"><span></span> > 智能组卷 > 导学案向导</span>
    </div>
</div>

<div id="wizardbox">
    <div id="dir1" class="step">
        <div class="ova"><h1 class="this_h1">第一步：选择模板</h1><span class="fgjl"></span><h1>第二步：编辑模板</h1><span class="fgjl"></span><h1>第三步：导学案预览</h1></div>
        <div class="dir">
            <div class='diffline difflineSelect' >
                <label class='typename'>选择教材：</label>
                <div class='typeval exametypebehind intoChapterFirst'>
                </div>
            </div>
            <div class='diffline'>
                <label class='typename'>选择章节：</label>
                <div class='typeval chapterbehind'>
                    <select name='LastChapter'  class='selectChapter'>
                        <option value=''>-请选择教材-</option>
                    </select>
                </div>
            </div>
            <div class='diffline difflineSelect'>
                <label class='typename'>导学案模板：</label>
                <div class='typeval choosetypebehind intoTestStyle'>
                </div>
                <div style="clear:both"></div>
                <div class='shownone mbsel'>
                    <div class='typeval tempbehind'>
                        <div class='selmb ova'>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="last">
        <table border="0"><tbody><tr>
            <td><div class="an01 bgbt tostep2 step2"><span class="an_left"></span><a>下一步</a><span class="an_right"></span></div></td>
            </tr>
        </tbody></table>
        </div>
    </div>
    
    <div id="dir2" class="step">
        <div class="ova"><h1>第一步：选择模板</h1><span class="fgjl"></span><h1 class="this_h1">第二步：编辑模板</h1><span class="fgjl"></span><h1>第三步：选题结果预览</h1></div>
        <div class="dir"><!--步骤2内容--></div>
        <div class="last">
            <div class="an01 bgbt tostep1"><span class="an_left"></span><a>上一步</a><span class="an_right"></span></div>
            <div class="an01 bgbt addtpl"><span class="an_left"></span><a>保存模板</a><span class="an_right"></span></div>
            <div class="an01 bgbt step2 tostep3"><span class="an_left"></span><a>下一步</a><span class="an_right"></span></div>
        </div>
    </div>
    
    <div id="dir3" class="step">
        <div class="ova"><h1>第一步：选择模板</h1><span class="fgjl"></span><h1>第二步：编辑模板</h1><span class="fgjl"></span><h1 class="this_h1">第三步：选题结果预览</h1></div>
        <div class="dir"><!--步骤3内容--></div>
        <div class="last">
            <div class="an01 bgbt Retostep2"><span class="an_left"></span><a>上一步</a><span class="an_right"></span></div>
            <div class="an01 bgbt" id="paperdownload"><span class="an_left"></span><a>下载Word</a><span class="an_right"></span></div>
            <div class="an01 bgbt sendWork" id="topapercenter"><span class="an_left"></span><a>发布导学案</a><span class="an_right"></span></div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/Public/default/js/case.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/default/js/workdown.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript">
    var URL = '<?php echo U('Index/getData');?>';
    var Types=parent.Types;
    var Subject=parent.Subject;
    var testStyle=<?php echo ($testStyle); ?>;
    var forumMsg=<?php echo ($forumMsg); ?>;
    var menuSubject=<?php echo ($menuSubject); ?>;
    var caseUploadUrl='/Guide/Case';
    if(caseUploadUrl.indexOf('/')===0){
        caseUploadUrl = caseUploadUrl.substring(1);
    }
    var key='<?php echo ($key); ?>';
    $.caseCommon.init();
    $(function(){
        $(window).bind("resize", function () {
            $.caseCommon.resetTestBox();
        });
    });
</script>
<div style="display:none;">
    <?php echo (C("WLN_STATISTICAL_CODE.HOME")); ?>
</div>
</body>
</html>