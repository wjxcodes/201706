<?php if (!defined('THINK_PATH')) exit();?><div id="wrap">
    <div class='top_nr_box'>
        <div class="main_right">
            <div class="main_right_title"><span></span>属性标注</div>
                <table border="0" align="center" cellpadding="5" cellspacing="0" class="listFrame" style="">
                    <tr>
                        <td style="width:65px;" align="right" class="tRight" >知识点：</td>
                        <td class="tLeft" >
                            <select id="knowledge" class="knowledge bLeft selectKnowledge" name="KlID">
                                <option value="">请选择</option>
                            </select>
                            
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="tRight" colspan="2">
                            <div id="knowledgeList" class="klinput"></div>
                        </td>
                    </tr>
                    <tr class='addButton'>
                        <td></td>
                        <td align="right">
                            <div class="impBtn" style="display:inline;padding:3px 0px;">
                                <input id="addkl" name="addkl" class="add imgButton" type="button" value="添加">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="tRight">章&nbsp;&nbsp;节：</td>
                        <td class="tLeft" >
                            <select id="chapter" class="chapter selectChapter bLeft" name="ChapterID">
                                <option value="">请选章节，避免超纲</option>
                            </select> 
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="tRight" colspan="2">
                            <div id="chapterList" class='cpinput'></div>
                        </td>
                    </tr>
                    <tr class='addButton'>
                        <td></td>
                        <td align="right">
                            <div class="impBtn" style="display:inline;padding:3px 0px;">
                                <input id="addcp" name="addcp" class="add imgButton" type="button" value="添加"/>
                            </div>&nbsp;&nbsp;
                            <div class="impBtn" style="display:inline;padding:3px 0px;">
                                <a id="adddcp" style="cursor:pointer;">系统提示</a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:65px;" align="right" class="tRight" >技能：</td>
                        <td class="tLeft" >
                            <select id="skill" class="sk bLeft selectSkill" name="SkillID[]">
                                <option value="">请选择</option>
                            </select>
                            
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="tRight" colspan="2">
                            <div id="skillList" class="skillinput"></div>
                        </td>
                    </tr>
                    <tr class='addButton'>
                        <td></td>
                        <td align="right">
                            <div class="impBtn" style="display:inline;padding:3px 0px;">
                                <input id="addSkill" name="addSkill" class="add imgButton" type="button" value="添加">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:65px;" align="right" class="tRight" >能力：</td>
                        <td class="tLeft" >
                            <select id="capacity" class="cc bLeft selectCapacity" name="CapacityID[]">
                                <option value="">请选择</option>
                            </select>
                            
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="tRight" colspan="2">
                            <div id="capacityList" class="capacityinput"></div>
                        </td>
                    </tr>
                    <tr class='addButton'>
                        <td></td>
                        <td align="right">
                            <div class="impBtn" style="display:inline;padding:3px 0px;">
                                <input id="addCapacity" name="addCapacity" class="add imgButton" type="button" value="添加">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="tRight">专&nbsp;&nbsp;题：</td>
                        <td class="tLeft" >
                            <select id="special" class="bLeft" name="SpecialID">
                                <option value="">请选择</option>
                            </select>
                        </td>
                    </tr>
                </table>

                <input name="xttimes" id="xttimes" value="<?php echo ($times); ?>" type="hidden"/>
                <input name="xttimes3" id="xttimes3" value="<?php echo ($times); ?>" type="hidden"/>

                <div class="main_right_title">
                    <span></span>试题打分
                    <div class="dfms_box">
                        <label style='color:red;'>
                            <input type="radio" id="kg" class="DfStyle bLeft" check='raido' warning="请选择打分模式" name="DfStyle" value="0" <?php if(($edit['DfStyle'] == '0') or ($edit['DfStyle'] == '')): ?>checked="checked"<?php endif; ?>> 客观打分
                        </label> 
                        <label>
                            <input id="zg" type="radio" class="DfStyle bLeft" name="DfStyle" value="1" <?php if(($edit["DfStyle"]) == "1"): ?>checked="checked"<?php endif; ?>> 主观打分
                        </label>
                    </div>
                </div>

                <table border="0" align="center" cellpadding="5" cellspacing="0" class="listFrame zgdf" style="">
                    <tr>
                        <td align="right" class="tRight" >难度值：</td>
                        <td class="tLeft" >
                            <input type="text" value="<?php echo ($edit["Diff"]); ?>" name="Diff" id="Diff" style="width:80px" />(0-1之间 最多4位小数)
                        </td>
                    </tr>
                </table>
        <?php if(markArray): ?><table border="0" align="center" cellpadding="5" cellspacing="0" class="listFrame kgdf" style="">
            <?php if($times>1): ?><tr>
                    <td colspan="2" align="center">
                        <?php $__FOR_START_15441__=1;$__FOR_END_15441__=$times+1;for($i=$__FOR_START_15441__;$i < $__FOR_END_15441__;$i+=1){ ?><span id="xt<?php echo ($i); ?>" <?php if(($i) == "1"): ?>class="xtcurrent xt_title" <?php else: ?> class="xt xt_title"<?php endif; ?>>小题<?php echo ($i); ?></span><?php } ?>
                    </td>
                </tr><?php endif; ?>
            <?php $__FOR_START_3254__=1;$__FOR_END_3254__=$times+1;for($ii=$__FOR_START_3254__;$ii < $__FOR_END_3254__;$ii+=1){ if(is_array($markArray)): $j = 0; $__LIST__ = $markArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($j % 2 );++$j;?><tr class="xt_con_<?php echo ($ii); ?> xt_con <?php if(($ii) != "1"): ?>none<?php endif; ?>">
                    <td align="right" class="tRight" style="width:120px;">
                        <?php if(($vo["Style"]) == "0"): ?><font color="red" title='红色为必选项'><?php echo ($vo["MarkName"]); ?>：</font>
                        <?php else: ?>
                        <?php echo ($vo["MarkName"]); ?>：<?php endif; ?>
                    </td>
                    <td class="tLeft" ><select id="xt_select_<?php echo ($ii); ?>_<?php echo ($j); ?>" class="mark bLeft" NAME="Mark[]" style="max-width: 150px;">
                    <option value="">请选择</option>
                    <?php if(is_array($vo["MarkListx"])): $i = 0; $__LIST__ = $vo["MarkListx"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item[3]); ?>" <?php if(is_array($edit["Markx"]["{$ii}"])): $i = 0; $__LIST__ = $edit["Markx"]["{$ii}"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$mk): $mod = ($i % 2 );++$i; if($mk == $item[3]): ?>selected="selected"<?php endif; endforeach; endif; else: echo "" ;endif; ?>><?php echo ($item[1]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select></td>
                </tr><?php endforeach; endif; else: echo "" ;endif; } ?>
            </table><?php endif; ?>
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="an_box">
            <tr>
                <td align="center">
                    <input type="hidden" id="code" name="code" value="<?php echo ($securityCode); ?>">
                    <input type="hidden" id="TestID" name="TestID" value="<?php echo ($edit["TestID"]); ?>">
                    <div class="impBtn fLeft">
                        <input tag='form1' id="datasave" u="<?php echo U('Test/Test/save');?>" type="button" value="保存" class="save imgButton mysubmit">
                    </div>
                    <div class="impBtn fLeft m-l10"></div>
                </td>
            </tr>
        </table>
        </div>
        <div class="main_left">
        <div class="styl_box">
        <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
            <?php if($errorInfo != ''): ?><tr>
                <td width="90" align="center"><strong><font color='red'>意见</font></strong></td>
                <td width="89%"><?php echo ($errorInfo); ?></td>
            </tr><?php endif; ?>
            <tr>
                <td width="90" align="center"><strong>题文</strong></td>
                <td width="89%"><?php echo ($edit["Test"]); ?></td>
            </tr>
            <tr>
                <td align="center"><strong>答案</strong></td>
                <td><?php echo ($edit["Answer"]); ?></td>
            </tr>
            <tr>
                <td align="center"><strong>解析</strong></td>
                <td><?php echo ($edit["Analytic"]); ?></td>
            </tr>
            <tr>
                <td align="center"><strong>题型</strong></td>
                <td class="tLeft" >
                    <select id="types" class="bLeft" name="TypesID" check='Require' warning="所属题型不能为空">
                        <option value="">请选择</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td align="center"><strong>备注</strong></td>
                <td><?php echo ($edit["Remark"]); ?></td>
            </tr>
            <tr>
                <td align="center"><strong>用户自评分值：</strong></td>
                <td><input type="text" name='Score' id="score" value='<?php echo ($edit["Score"]); ?>' style="width:180px;">&nbsp;<font color='red'>带小题的分值用英文逗号分开</font></td>
            </tr>
            <tr>
                <td align="center"><strong>测试类型</strong></td>
                <td class="tLeft" >
                    <label>
                        <input type="radio" class="choose bLeft"  check='raido' warning="请选择测试类型" name="IfChoose" value="0" <?php if(($edit["IfChoose"] == '0') or ($edit["IfChoose"] == '')): ?>checked="checked"<?php endif; ?>> 非选择题
                    </label>
                    <label>
                        <input type="radio" class="choose bLeft" name="IfChoose" value="3" <?php if(($edit["IfChoose"]) == "3"): ?>checked="checked"<?php endif; ?>> 单选题
                    </label> 
                    <label>
                        <input type="radio" class="choose bLeft" name="IfChoose" value="2" <?php if(($edit["IfChoose"]) == "2"): ?>checked="checked"<?php endif; ?>> 多选题
                    </label> 
                    <label>
                        <input type="radio" class="choose bLeft" name="IfChoose" value="1" <?php if(($edit["IfChoose"]) == "1"): ?>checked="checked"<?php endif; ?>> 复合题（带小题）
                    </label> 
                    <div id="showxt" style="display:none">
                        <input name="addt" id="addt" type="button" value="增加小题" style="cursor:pointer" /> 
                        <input name="delt" id="delt" type="button" value="删除小题" style="cursor:pointer" /> 
                        <input name="deltall" id="deltall" type="button" value="清空小题" style="cursor:pointer" />
                        <div id="xt" style="width:100%;overflow-y:auto;max-height:100px;">
                            <p class='xtList'>小题1：
                                <label>
                                    <input type="radio" class="choose1 bLeft" check='raido' warning="请选择测试类型" name="IfChoose1" value="0" checked="checked"> 非选择题
                                </label>
                                <label>
                                    <input type="radio" class="choose1 bLeft" name="IfChoose1" value="3"> 单选题
                                </label> 
                                <label>
                                    <input type="radio" class="choose1 bLeft" name="IfChoose1" value="2"> 多选题
                                </label> 
                            </p>
                        </div>
                    </div>
                 </td>
            </tr>
            <tr>
                <td align="center"><strong>选项宽度</strong><br/><a id="getWidth" style="cursor:pointer;">计算宽度</a><br/><span id="widthCon"></span></TD>
                <td class="tLeft" >
                    <div id="showwidth" >
                        <div id="wd" style="width:100%;overflow-y:auto;max-height:100px;">
                            <?php if(is_array($optionWidth)): $j = 0; $__LIST__ = $optionWidth;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ow): $mod = ($j % 2 );++$j;?><p class="optionwidth_<?php echo ($j); ?> optionwidth">
                                    <?php if($edit["IfChoose"] == '1'): ?>小题<?php echo ($j); ?>：<?php endif; ?>
                                    <label>
                                        <input type="text" class="optionwidth<?php echo ($j); ?> bLeft"  warning="请填入选项宽度" size="2" name="optionwidth<?php echo ($j); ?>" value="<?php echo ($ow); ?>" >
                                    </label> 
                                </p><?php endforeach; endif; else: echo "" ;endif; ?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td align="center"><strong>选项数量</strong></td>
                <td class="tLeft" >
                    <div id="shownum" >
                        <div id="num" style="width:100%;overflow-y:auto;max-height:100px;">
                            <?php if(is_array($optionNum)): $k = 0; $__LIST__ = $optionNum;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$onum): $mod = ($k % 2 );++$k;?><p class="optionnum_<?php echo ($k); ?> optionnum">
                                    <?php if($edit["IfChoose"] == '1'): ?>小题<?php echo ($k); ?>：<?php endif; ?>
                                    <label>
                                        <input type="text" class="optionnum<?php echo ($k); ?> bLeft"  warning="请填入选项宽度" name="optionnum<?php echo ($k); ?>" size="2" value="<?php echo ($onum); ?>" >
                                    </label>
                                </p><?php endforeach; endif; else: echo "" ;endif; ?>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        </div>
        </div>
    </div>
    <div class="bottom_nr_box">
        <input type="hidden" id="DocID" name="DocID" value="<?php echo ($edit["DocID"]); ?>">
        <input type="hidden" id="WorkID" name="WorkID" value="<?php echo ($wid); ?>">
        <div class="title">试卷编号及名称：[<?php echo ($doc["DocID"]); ?>] <?php echo ($doc["DocName"]); ?>&nbsp; <a href="<?php echo U('Test/Test/index',array('DocID'=>$doc[DocID]));?>" class='lookAllTest' title='所有试题编辑完成后查看'>查看全部试题</a></div>
    </div>
</div>
<script language="javascript">
    var optionWidth='<?php echo ($optionwidth); ?>';//未使用
    var subjectID='<?php echo ($edit["SubjectID"]); ?>';
    var specialID='<?php echo ($edit["SpecialID"]); ?>';
    var typeID='<?php echo ($edit["TypesID"]); ?>';
    var skillID='<?php echo ($edit["SkillID"]); ?>';
    var capacityID='<?php echo ($edit["CapacityID"]); ?>';
    var knowID='<?php echo ($edit["KlID"]); ?>';
    var chapID='<?php echo ($edit["ChapterID"]); ?>';
    if("<?php echo ($act); ?>"=='edit'){
        $('#knowledge').allSelectLoad('/Test/Test',{"style":"getMoreData","list":"knowledgeList,chapterList,special,types,knowledge,chapter,capacity,capacityList,skill,skillList","subjectID":subjectID,"idList":{
        "knowledgeList":knowID,"skillList":skillID,"capacityList":capacityID,"chapterList":chapID,"special":specialID,"types":typeID,"knowledge":"0","chapter":"0","capacity":"0","skill":"0"}})
    }
    //写入小题结构
    if("<?php echo ($edit["IfChoose"]); ?>"==1){
        $('#showxt').css({'display':'block'});
        var str='';
        var tmp_i=1;
        <?php if(is_array($chooseList)): $i = 0; $__LIST__ = $chooseList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>str+='<p class="xtList">小题'+tmp_i+
                '：<label><input type="radio" class="choose'+tmp_i+' bLeft"  check="raido" warning="请选择测试类型" name="IfChoose'+tmp_i+ '" value="0" <?php if(($vo["IfChoose"] == '0') or ($vo["IfChoose"] == '')): ?>checked="checked"<?php endif; ?>> 非选择题</label> '+
        '<label><input type="radio" class="choose'+tmp_i+' bLeft" name="IfChoose'+tmp_i+'" value="3" <?php if(($vo["IfChoose"]) == "3"): ?>checked="checked"<?php endif; ?>> 单选题</label> '+
        '<label><input type="radio" class="choose'+tmp_i+' bLeft" name="IfChoose'+tmp_i+'" value="2" <?php if(($vo["IfChoose"]) == "2"): ?>checked="checked"<?php endif; ?>> 多选题</label> '+
        '</p>';
        tmp_i++;<?php endforeach; endif; else: echo "" ;endif; ?>
        $('#xt').empty().html(str);
    }
    //初始化打分方式
    <?php if($edit['DfStyle'] == 1): ?>$('.zgdf').show();
        $('.kgdf').hide();
    <?php else: ?>
        $('.zgdf').hide();
        $('.kgdf').show();<?php endif; ?>
    $.testOperation.addTestWidthNum();
    $.testOperation.delTestWidthNum();
    $.testOperation.clearAllWidthNum();
    $.testOperation.testChooseChange();
    // $.testOperation.resize();
    $(function(){
        $.newEdit.initBox();
        $(window).resize(function(){
            $.newEdit.initBox();
        });
    });
</script>