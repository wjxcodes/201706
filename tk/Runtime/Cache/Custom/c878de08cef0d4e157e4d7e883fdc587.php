<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title></title>
<meta name="keywords" content="文档添加" />
<meta name="description" content="文档添加" />
    
<link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
<link type="text/css" href="/Public/default/css/customTest.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/Public/plugin/tree.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <!--[if lte IE 6]>
    <script type="text/javascript" src="/Public/plugin/png.js"></script>
    <script>DD_belatedPNG.fix('a,img,div,span');</script>
    <![endif]-->
    <script type="text/javascript" src="/Public/plugin/tips.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/json2.min.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/paper.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/ueditor/ueditor.config.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/ueditor/ueditor.all.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/editor.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/customTest.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/formatTest.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
</head>
<body>
<div id="righttop">
    <div id="categorylocation">
        <span class="nowPath">当前位置：</span>> <span id="loca_text"> <a href='<?php echo U('Custom/CustomTestStore/index');?>'>我的试题</a></span> > <span
            id="loca_text"> <?php echo ($pageName); ?></span>
    </div>
</div>

<div id="divbox" style='position:relative;'>
    <div class="content_01" id="wdgl">
        <div class="wdtj_box">  
            <div class="content">
                <!-- 内容显示区域  -->
                <form action="<?php echo U('Custom/CustomTestStore/save');?>" method="post" id='test-form'>
                    <input type="hidden" name='verifyCode' id='verifyCode' value='<?php echo ($verifyCode); ?>'/>
                    <input type="hidden" name='TestID' value='<?php echo ($testid); ?>' id='testid'/>
                    <input type="hidden" name='TTID' id='ttid'/>
                    <div class="title"><span class="fl">试题内容</span></div>            
                    <table cellpadding="5" cellspacing="0" class="" border="0" style='width:100%;'>
                        <tbody>
                        <tr>
                            <td align="right" class="tRight" style="width:80px">题文：</td>
                            <td class="tLeft">
                                <div class='editContainersTest editContainers' title='题文'></div>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" class="tRight" style="width:80px">答案：</td>
                            <td class="tLeft">
                                <div class='editContainersAnswer editContainers' title='答案'></div>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" class="tRight" style="width:80px">解析：</td>
                            <td class="tLeft">
                                <div class='editContainersAnalytic editContainers' title='解析'></div>
                            </td>
                        </tr>      
                         
                        <tr>
                            <td colspan='2' style='padding:0px;'>
                                <!-- 试题属性-显示/隐藏 -->
                                <div class="test-type-btn-wrap">
                                    <span class="tt-tit">试题属性（选填）</span><a class="tt-btn toggleAttributes f12" href="javascript:;">隐藏</a>
                                </div>
                                <!-- 试题属性-显示/隐藏 -end -->
                            </td>            
                        </tr>
                        <tr class="selItem">
                            <td align="right" class="tRight" style="width:80px">年级：</td>
                            <td class="tLeft">
                                <select id="grade" class="large bLeft" name="GradeID">
                                    <option value="">请选择</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="selItem">
                            <td align="right" class="tRight" style="width:80px">题型：</td>
                            <td class="tLeft">
                                <select id="types" class="large bLeft" name="TypesID" check="Require" warning="所属题型不能为空">
                                    <option value="">请选择</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="zgdf">
                            <td align="right" class="tRight" style="width:80px">难度值：</td>
                            <td class="tLeft">
                                <label class="difficulty" title="0.801-0.999"><input type="radio" name='diff' value='0.801'/>容易</label>
                                <label class="difficulty" title="0.601-0.800"><input type="radio" name='diff' value='0.601'/>较易</label>
                                <label class="difficulty" title="0.501-0.600"><input type="radio" name='diff' value='0.501'/>一般</label>
                                <label class="difficulty" title="0.301-0.500"><input type="radio" name='diff' value='0.301'/>较难</label>
                                <label class="difficulty" title="0.001-0.300"><input type="radio" name='diff' value='0.001'/>困难</label>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" class="tRight" style="width:80px">试题来源：</td>
                            <td class="tLeft">
                                <span class='boxlist_sel'><input type="text" name='source' id='source'/></span>
                            
                            </td>
                        </tr>
                        <tr class="selItem">
                            <td align="right" class="tRight" style="width:80px">知识点：</td>
                            <td class="tLeft knowledge-select-change-container">
                                <select id="knowledge" class="selection bLeft"><option value="">请选择</option>
                                </select> 
                                <span class='append'>添加</span>
                                <div id="klinput" class='inputs'>
                                </div>
                            </td>
                        </tr>
                        <tr class="selItem">
                            <td align="right" class="tRight" style="width:80px">章节：</td>
                            <td class="tLeft chapter-select-change-container">
                                <select id="chapter" class="selection bLeft" name="ChapterID">
                                </select>
                                <span class='append'>添加</span>
                                <div id="cpinput" class='inputs'></div>
                            </td>
                        </tr>

                        <tr class="selItem">
                            <td align="right" class="tRight" style="width:80px">技能：</td>
                            <td class="tLeft skill-select-change-container">
                                <select id="skill" class="selection bLeft" name="SkillID">
                                </select>
                                <span class='append'>添加</span>
                                <div id="skinput" class='inputs'></div>
                            </td>
                        </tr>

                        <tr class="selItem">
                            <td align="right" class="tRight" style="width:80px">能力：</td>
                            <td class="tLeft capacity-select-change-container">
                                <select id="capacity" class="selection bLeft" name="CapacityID">
                                </select>
                                <span class='append'>添加</span>
                                <div id="capinput" class='inputs'></div>
                            </td>
                        </tr>
                        
                        <tr>
                            <td align="right" class="tRight" style="width:80px">备注：</td>
                            <td class="tLeft">
                                <textarea name="Remark" id="remark" rows="5" style='width:90%;resize:horizontal;'></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;
                            </td>            
                        </tr> 
                        </tbody>
                    </table>
                        <div style="width:85%;padding:0 15px 20px;overflow:hidden">
                                <div style='padding:10px;color:red;font-size:12px;'>不存在小题的试题无需格式化 <a href="javascript:$.CustomTestStore.showTestDemo();"  style='color:#0000ff;font-size:12px;'>查看例子</a></div>
                        <div title='不包含小题的试题无需格式化' class="bgbt an01" style="float:left; margin-top:6px; margin-right:10px"><span class="an_left"></span><a href='#' id='format'>格式化试题</a><span class="an_right"></span></div>
                        <div class="bgbt an01" style="float:left; margin-top:6px; margin-right:10px"><span class="an_left"></span><a href='#' id='save'>保存</a><span class="an_right"></span></div>
                        </div>

                </form>
            <!-- 内容显示区域结束 -->
            </div>
        </div>
    </div>

    <div id='test'></div>
  <!--  分页显示区域 -->
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var data = new Object(<?php echo ($data); ?>);
        var originality = '<?php echo ($originality); ?>';
        originality = originality?JSON.parse(originality):{'ttID':false};
        var url = '/Custom/CustomTestStore';
        if(url.indexOf('/')===0){
            url = url.substring(1);
        }
        var ifImage = false;
        var act = '<?php echo ($act); ?>';
        $.customTestStoreTestAdd.init(data,originality,url,ifImage, act);
    });
</script>
</body>
</html>