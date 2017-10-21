<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title><?php echo ($pageName); ?> - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/template.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script>
    var local='<?php echo U('Dir/Index/index');?>';
    </script>
    <script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/paper.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script>
        var Types=<?php echo ($Types); ?>;
        var Diff=<?php echo ($Diff); ?>;
        var Subject=<?php echo ($Subject); ?>;
        var schoolName='<?php echo ($schoolName); ?>';
    </script>
    <!--[if lte IE 6]>
    <script type="text/javascript" src="/Public/plugin/png.js"></script>
    <script>DD_belatedPNG.fix('a,img,div,span');</script>
    <![endif]-->
</head>
<body style="position:relative;">
<div id="righttop">
    <div id="categorylocation">
        <span class="nowPath">当前位置：</span>> <span id="loca_text"><span></span> > 智能组卷 > 动态模板组卷</span>
    </div>
</div>

<div id="wizardbox">
    <div id="dir1" class="step">
        <div class="ova"><h1 class="this_h1">第一步：选择模板</h1><span class="fgjl"></span><h1>第二步：编辑模板</h1><span class="fgjl"></span><h1>第三步：选题结果预览</h1></div>
        <div class="dir">
            <div class='diffline'>
                <label class='typename'>考试类别：</label>
                <div class='typeval exametypebehind'>
                  <?php if(is_array($examType)): $i = 0; $__LIST__ = $examType;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$examval): $mod = ($i % 2 );++$i;?><label> <input type="radio" name="examtype" defaultstyle="<?php echo ($examval["DefaultStyle"]); ?>" value='<?php echo ($examval["TypeID"]); ?>' class='examtype' showName="<?php echo ($examval["TypeName"]); ?>"/><?php echo ($examval["TypeName"]); ?>&nbsp;</label><?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
            </div>
            <div class='diffline' style="background:#edeef0;overflow:hidden;">
                <label class='typename'>选择模板：</label>
                <div class='typeval choosetypebehind'>
                    <?php if(is_array($testStyle)): $i = 0; $__LIST__ = $testStyle;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$styleval): $mod = ($i % 2 );++$i;?><label><input type="radio" name="choosetype" value='<?php echo ($styleval["val"]); ?>' class='choosetype' /><?php echo ($styleval["styleName"]); ?>&nbsp;</label><?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
                <div style="clear:both"></div>
                <div class='shownone none mbsel'>
                    <div class='typeval tempbehind'>
                        <div class='selmb ova'>
                        </div>
                    </div>
                </div>
            </div>
            <div class='diffline' >
                <label class='typename'>选题方式：</label>
                 <div class='typeval chooseattrbehind'>
                     <?php if(is_array($chooseAttr)): $i = 0; $__LIST__ = $chooseAttr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attrvo): $mod = ($i % 2 );++$i;?><label><input type="radio" name="chooseattr" value='<?php echo ($attrvo["val"]); ?>'/><?php echo ($attrvo["chooseAttrName"]); ?>&nbsp;</label><?php endforeach; endif; else: echo "" ;endif; ?>
                 </div>
            </div>
            <div class='diffline' style="background:#edeef0">
                <label class='typename'>年级：</label>
                <div class='grademsg chooseattrbehind'>

                </div>
            </div>
            <div class='diffline'>
                <label class='typename'>试题类型：</label>
                 <div class='typeval doctypebehind'>
                    <label><input type='checkbox' name='selectall' value='' id='checkall'>全选</label>
                    <?php if(is_array($testType)): $i = 0; $__LIST__ = $testType;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><label><input type="checkbox" name="doctype[]" class='checkall' thisid="<?php echo ($vo["GradeList"]); ?>" value='<?php echo ($vo["TypeID"]); ?>'/><?php echo ($vo["TypeName"]); ?>&nbsp;</label><?php endforeach; endif; else: echo "" ;endif; ?>
                 </div>
                 <div style="clear:both"></div>
            </div>
            <div class='diffline' style="background:#edeef0">
                <label class='typename'>地区：</label>
                <div class='typeval areabehind'>
                    <label><input type='checkbox' name='areaall' value='' id='areaall'>全选</label>
                    <?php if(is_array($area)): $i = 0; $__LIST__ = $area;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><label><input type="checkbox" name="area[]" class='areaall' value='<?php echo ($vo["AreaID"]); ?>'/><?php echo ($vo["AreaName"]); ?>&nbsp;</label><?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
            </div>
        </div>
        <div class="last">
            <div class="an01 bgbt tostep2 step2"><span class="an_left"></span><a>下一步</a><span class="an_right"></span></div>
        </div>
    </div>
    
    <div id="dir2" class="step">
        <div class="ova"><h1>第一步：选择模板</h1><span class="fgjl"></span><h1 class="this_h1">第二步：编辑模板</h1><span class="fgjl"></span><h1>第三步：选题结果预览</h1></div>
        <div class="dir">
        <!--步骤2内容-->
            这是步骤2的内容部分，请将自己的东西放置在这里！！
        <!--步骤2内容结束-->
        </div>
        <div class="last">
            <div class="an01 bgbt tostep1"><span class="an_left"></span><a>上一步</a><span class="an_right"></span></div>
            <div class="an01 bgbt addtpl"><span class="an_left"></span><a>保存模板</a><span class="an_right"></span></div>
            <div class="an01 bgbt step2 tostep3"><span class="an_left"></span><a>下一步</a><span class="an_right"></span></div>
        </div>
    </div>
    
    <div id="dir3" class="step">
        <div class="ova"><h1>第一步：选择模板</h1><span class="fgjl"></span><h1>第二步：编辑模板</h1><span class="fgjl"></span><h1 class="this_h1">第三步：选题结果预览</h1></div>
        <div class="dir">
        <!--步骤2内容-->
            这是步骤3的内容部分，请将自己的东西放置在这里！！
        <!--步骤2内容结束-->
        </div>
        <div class="last">
            <div class="an01 bgbt tostep2"><span class="an_left"></span><a>上一步</a><span class="an_right"></span></div>
            <div class="an01 bgbt" id="topapercenter"><span class="an_left"></span><a>试卷预览</a><span class="an_right"></span></div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/Public/default/js/dir.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<div style="display:none;">
    <?php echo (C("WLN_STATISTICAL_CODE.HOME")); ?>
</div>
</body>
</html>