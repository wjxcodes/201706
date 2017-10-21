<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title><?php echo ($pageName); ?> - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/lntel.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script>
    var local='<?php echo U('Ga/zn');?>';
    </script>
    <!--[if lte IE 6]>
    <script type="text/javascript" src="/Public/plugin/png.js"></script>
    <script type="text/javascript">DD_belatedPNG.fix('a,img,div,span');</script>
    <![endif]-->
    <script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/paper.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/blockmove.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>

</head>
<body style="position:relative;">
<div id="righttop">
    <div id="categorylocation">
        <span class="nowPath">当前位置：</span>> <span id="loca_text"><span></span> > 智能组卷 > 智能参数组卷</span>
    </div>
</div>
<div id="wizardbox">
    <div id="step1" class="step">
        <div style="overflow:auto;"><h1 class="this_h1">第一步：选择考查范围</h1><span class="fgjl"></span><h1>第二步：设置选题属性</h1><span class="fgjl"></span><h1>第三步：设置难度及考点覆盖率</h1><span class="fgjl"></span><h1>第四步：选题结果预览</h1></div>
        <div class="xd_box" style="position:relative;">
            <div class="selectRange"><p class="list_ts none"><span class="ico_dd">正在加载考查范围请稍候...</span></p></div>
            <div id="categorycheckbox"><p class="list_ts"><span class="ico_dd">正在加载知识点列表请稍候...</span></p></div>
        </div>
        <div class="last">
            <div class="an01 bgbt" hidevalue="0" id="cate_checkall"><span class="an_left"></span><a>全部选中</a><span class="an_right"></span></div>
            <div class="an01 bgbt tostep2"><span class="an_left"></span><a>下一步</a><span class="an_right"></span></div>
        </div>
    </div>
    <div id="step2" class="step">
        <div style="overflow:auto;"><h1>第一步：选择考查范围</h1><span class="fgjl"></span><h1 class="this_h1">第二步：设置选题属性</h1><span class="fgjl"></span><h1>第三步：设置难度及考点覆盖率</h1><span class="fgjl"></span><h1>第四步：选题结果预览</h1></div>
        <div class="xd_box" style="position:relative;">
            <div id="quesnumsetting" style="position:relative;"></div>
        </div>
        <div class="last">
            <div class="an01 bgbt tostep1"><span class="an_left"></span><a>上一步</a><span class="an_right"></span></div>
            <div class="an01 bgbt tostep3"><span class="an_left"></span><a>下一步</a><span class="an_right"></span></div></td>
        </div>
    </div>
    <div id="step3" class="step">
        <div style="overflow:auto;"><h1>第一步：选择考查范围</h1><span class="fgjl"></span><h1>第二步：设置选题属性</h1><span class="fgjl"></span><h1 class="this_h1">第三步：设置难度及考点覆盖率</h1><span class="fgjl"></span><h1>第四步：选题结果预览</h1></div>
        <div class="xd_box">
            <div id="diff">
                <div class="hk">
                    <div class="hkbox_nav">难度系数</div>
                    <div class="hkbg">
                        <div class="hk_sz">
                            <span style="float:left">0.0</span>
                            <span style="float:right">1.0</span>
                            <p>
                                <span class="scrollleft">难&lt;&lt;</span>
                                <span class="scrollnotice">合理区间为[0.25-0.75]</span>
                                <span class="scrollright">&gt;&gt;易</span>
                            </p>
                        </div>
                        <div class="hkline"></div>
                        <div class="hkblock" tid="0">
                            <input name="" type="image" src="/Public/default/image/hkblock.png" onclick="return false;">
                        </div>
                        <div class="hkbox" id="diffNum">0.50</div>
                        <div class="hkbar"><p>&nbsp;</p></div>
                    </div>
                </div>
                <div class="hk">
                    <div class="hkbox_nav">考点覆盖率</div>
                    <div class="hkbg">
                        <div class="hk_sz">
                            <span style="float:left">0%</span>
                            <span style="float:right">100%</span>
                            <p>
                                <span class="scrollleft">低&lt;&lt;</span>
                                <span class="scrollnotice">合理区间为[30%-100%]</span>
                                <span class="scrollright">&gt;&gt;高</span>
                            </p>
                        </div>
                        <div class="hkline"></div>
                        <div class="hkblock" tid="1">
                            <input name="" type="image" src="/Public/default/image/hkblock.png">
                        </div>
                        <div class="hkbox" id="klNum">50%</div>
                        <div class="hkbar"><p>&nbsp;</p></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="last">
            <div class="tostep2 an01 bgbt"><span class="an_left"></span><a>上一步</a><span class="an_right"></span></div>
            <div class="znxt an01 bgbt" id="submit"><span class="an_left"></span><a>开始智能组卷</a><span class="an_right"></span></div>
        </div>
    </div>

    <div id="step4" class="step">
        <div style="overflow:auto;"><h1>第一步：选择考查范围</h1><span class="fgjl"></span><h1>第二步：设置选题属性</h1><span class="fgjl"></span><h1>第三步：设置难度及考点覆盖率</h1><span class="fgjl"></span><h1 class="this_h1">第四步：选题结果预览</h1></div>
        <div id="step4Scroll" class="xd_box" style="position:relative;">
            <div id="quesresult" style="background-color:#fff;line-height:22px;"></div>
            <div id="queslistbox" style="background-color:#fff;padding-top:5px;"></div>
        </div>
        <div class="last">
            <div class="an01 bgbt tostep3"><span class="an_left"></span><a>上一步</a><span class="an_right"></span></div>
            <div class="an01 bgbt" id="topapercenter"><span class="an_left"></span><a>试卷预览(合并相同题型)</a><span class="an_right"></span></div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/Public/default/js/zn.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>

<div style="display:none;">
    <?php echo (C("WLN_STATISTICAL_CODE.HOME")); ?>
</div>
</body>
</html>