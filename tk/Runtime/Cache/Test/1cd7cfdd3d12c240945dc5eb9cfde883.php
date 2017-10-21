<?php if (!defined('THINK_PATH')) exit();?><style>
<?php if($edit["DfStyle"] == 1): ?>.kgdf{display:none;}
<?php else: ?>
.zgdf{display:none;}<?php endif; ?>
</style>
<TABLE cellpadding="5" cellspacing="0" class="list" border="1">
<tr><td height="5" colspan="2" class="topTd" ></td></tr>
<TR>
    <TD class="tRight" style="width:100px">试题预览：</TD>
    <TD class="tLeft"><div style="height:200px;width:430px;overflow:auto;">
    <p>【题文】<?php echo ((isset($edit["Test"]) && ($edit["Test"] !== ""))?($edit["Test"]):'无</p>'); ?>
    <p><font color="red">【答案】</font><?php echo ((isset($edit["Answer"]) && ($edit["Answer"] !== ""))?($edit["Answer"]):'无</p>'); ?>
    <p><font color="red">【解析】</font><?php echo ((isset($edit["Analytic"]) && ($edit["Analytic"] !== ""))?($edit["Analytic"]):'无</p>'); ?>
    <p><font color="red">【备注】</font><?php echo ((isset($edit["Remark"]) && ($edit["Remark"] !== ""))?($edit["Remark"]):'无</p>'); ?>
    </div></TD>
</TR>
<TR>
    <TD class="tRight" style="width:80px">用户自评分值：</TD>
    <TD class="tLeft" ><input type="text" name='Score' id='score' value='<?php echo ($edit["Score"]); ?>'>&nbsp;<font color='red'>带小题的分值用英文逗号分开</font></TD>
</TR>
<TR>
    <TD class="tRight" style="width:80px">题型：</TD>
    <TD class="tLeft" ><SELECT id="types" class="large bLeft" NAME="TypesID" check='Require' warning="所属题型不能为空">
    <option value="">请选择</option>
    </SELECT></TD>
</TR>
<TR>
    <TD class="tRight" style="width:80px">知识点：</TD>
    <TD class="tLeft" ><SELECT id="knowledge" class="knowledge bLeft selectKnowledge" NAME="KlID">
    <option value="">请选择</option>
    </SELECT> <div class="impBtn" style="display:inline;padding:3px 0px;"><input id="addkl" name="addkl" class="add imgButton" type="button" value="添加"></div>
    <div class="klinput" id="knowledgeList"></div>
    </TD>
</TR>
<TR>
    <TD class="tRight" style="width:80px">章节：</TD>
    <TD class="tLeft" ><SELECT id="chapter" class="chapter bLeft selectChapter" NAME="ChapterID">
    <option value="">请选择</option>
    </SELECT> <div class="impBtn" style="display:inline;padding:3px 0px;"><input id="addcp" name="addcp" class="add imgButton" type="button" value="添加"/></div> <div class="impBtn" style="display:inline;padding:3px 0px;"><a id="adddcp" style="cursor:pointer;">载入默认章节</a></div>
    <div class="cpinput" id='chapterList'></div>
    </TD>
</TR>
<TR>
    <TD class="tRight" style="width:80px">技能：</TD>
    <TD class="tLeft" ><SELECT id="skill" class="sk bLeft selectSkill" NAME="SkillID">
    <option value="">请选择</option>
    </SELECT> <div class="impBtn" style="display:inline;padding:3px 0px;"><input id="addSkill" name="addSkill" class="add imgButton" type="button" value="添加"></div>
    <div class="skillinput" id="skillList"></div>
    </TD>
</TR>
<TR>
    <TD class="tRight" style="width:80px">能力：</TD>
    <TD class="tLeft" ><SELECT id="capacity" class="cc bLeft selectCapacity" NAME="CapacityID">
    <option value="">请选择</option>
    </SELECT> <div class="impBtn" style="display:inline;padding:3px 0px;"><input id="addCapacity" name="addCapacity" class="add imgButton" type="button" value="添加"></div>
    <div class="capacityinput" id="capacityList"></div>
    </TD>
</TR>
</TR>
<TR>
    <TD class="tRight" style="width:80px">专题：</TD>
    <TD class="tLeft" ><SELECT id="special" class="large bLeft" NAME="SpecialID">
    <option value="">请选择</option>
    </SELECT></TD>
</TR>
<TR>
    <TD class="tRight" style="width:80px">测试类型：</TD>
    <TD class="tLeft" >
        <label><INPUT TYPE="radio" class="choose bLeft"  check='raido' warning="请选择测试类型" NAME="IfChoose" value="0" <?php if(($edit["IfChoose"] == '0') or ($edit["IfChoose"] == '')): ?>checked="checked"<?php endif; ?>> 非选择题</label>
        <label><INPUT TYPE="radio" class="choose bLeft" NAME="IfChoose" value="3" <?php if(($edit["IfChoose"]) == "3"): ?>checked="checked"<?php endif; ?>> 单选题</label> 
        <label><INPUT TYPE="radio" class="choose bLeft" NAME="IfChoose" value="2" <?php if(($edit["IfChoose"]) == "2"): ?>checked="checked"<?php endif; ?>> 多选题</label> 
        <label><INPUT TYPE="radio" class="choose bLeft" NAME="IfChoose" value="1" <?php if(($edit["IfChoose"]) == "1"): ?>checked="checked"<?php endif; ?>> 复合题（带小题）</label> 
        <div id="showxt" style="display:none">
            <input name="addt" id="addt" type="button" value="增加小题" style="cursor:pointer" /> <input name="delt" id="delt" type="button" value="删除小题" style="cursor:pointer" /> <input name="deltall" id="deltall" type="button" value="清空小题" style="cursor:pointer" />
            <div id="xt" style="width:100%;overflow-y:auto;max-height:100px;"><p class='xtList'>小题1：<label><INPUT TYPE="radio" class="choose1 bLeft" check='raido' warning="请选择测试类型" NAME="IfChoose1" value="0" checked="checked"> 非选择题</label>
            <label><INPUT TYPE="radio" class="choose1 bLeft" NAME="IfChoose1" value="3"> 单选题</label> 
            <label><INPUT TYPE="radio" class="choose1 bLeft" NAME="IfChoose1" value="2"> 多选题</label> 
            </p></div>
        </div>
    </TD>
</TR>
<TR>
    <TD class="tRight" style="width:80px">选项宽度：<br/><a id="getWidth" style="cursor:pointer;">计算宽度</a><br/><span id="widthCon"></span></TD>
    <TD class="tLeft" >
        <div id="showwidth" >
            <div id="wd" style="width:100%;overflow-y:auto;max-height:100px;">
                <?php if(is_array($optionwidth)): $j = 0; $__LIST__ = $optionwidth;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ow): $mod = ($j % 2 );++$j;?><p class="optionwidth_<?php echo ($j); ?> optionwidth"><?php if($edit["IfChoose"] == '1'): ?>小题<?php echo ($j); ?>：<?php endif; ?><label><INPUT TYPE="text" class="optionwidth<?php echo ($j); ?> bLeft"  warning="请填入选项宽度" size="2" NAME="optionwidth<?php echo ($j); ?>" value="<?php echo ($ow); ?>" ></label> </p><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
    </TD>
</TR>
<TR>
    <TD class="tRight" style="width:80px">选项数量：</TD>
    <TD class="tLeft" >
        <div id="shownum" >
            <div id="num" style="width:100%;overflow-y:auto;max-height:100px;">
                <?php if(is_array($optionnum)): $k = 0; $__LIST__ = $optionnum;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$onum): $mod = ($k % 2 );++$k;?><p class="optionnum_<?php echo ($k); ?> optionnum"><?php if($edit["IfChoose"] == '1'): ?>小题<?php echo ($k); ?>：<?php endif; ?><label><INPUT TYPE="text" class="optionnum<?php echo ($k); ?> bLeft"  warning="请填入选项数量" NAME="optionnum<?php echo ($k); ?>" size="2" value="<?php echo ($onum); ?>" ></label> </p><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
    </TD>
</TR>

<?php if($send==1): ?><TR>
    <TD class="tRight tTop" style="width:80px">状态：</TD>
    <TD class="tLeft"><label><INPUT TYPE="radio" class="status bLeft"  check='raido' warning="请选择状态" NAME="Status" value="0" <?php if(($edit["Status"] == '0') or ($edit["Status"] == '')): ?>checked="checked"<?php endif; ?>> 正常</label> <label><INPUT TYPE="radio" class="status bLeft" NAME="Status" value="1" <?php if(($edit["Status"]) == "1"): ?>checked="checked"<?php endif; ?>> 锁定</label> </TD>
</TR><?php endif; ?>
<TR>
    <TD class="tRight tTop" style="width:80px">打分模式：</TD>
    <TD class="tLeft"><label><INPUT TYPE="radio" id="kg" class="DfStyle bLeft" check='raido' warning="请选择打分模式" NAME="DfStyle" value="0" <?php if(($edit["DfStyle"] == '0') or ($edit["DfStyle"] == '')): ?>checked="checked"<?php endif; ?>> 客观打分</label> <label><INPUT id="zg" TYPE="radio" class="DfStyle bLeft" NAME="DfStyle" value="1" <?php if(($edit["DfStyle"]) == "1"): ?>checked="checked"<?php endif; ?>> 主观打分</label> </TD>
</TR>
<TR style="background-color:#efefef;font-weight:bold;" class="zgdf">
    <TD class="tCenter tTop" colspan='2'>
    试题打分
    </TD>
</TR>
<TR class="zgdf">
    <TD class="tRight" style="width:80px">难度值：</TD>
    <TD class="tLeft" ><INPUT type="text" value="<?php echo ($edit["Diff"]); ?>" name="Diff" id="Diff" /> （0-1之间 最多4位小数）</TD>
</TR>

<?php if($mark_array): ?><TR style="background-color:#efefef;font-weight:bold;" class="kgdf">
    <TD class="tCenter tTop" colspan='2'>
    <input name="xttimes" id="xttimes" value="<?php echo ($times); ?>" type="hidden"/>
    <?php if($times>1): $__FOR_START_14005__=1;$__FOR_END_14005__=$times+1;for($i=$__FOR_START_14005__;$i < $__FOR_END_14005__;$i+=1){ ?><span id="xt<?php echo ($i); ?>" <?php if(($i) == "1"): ?>class="xtcurrent xt_title" <?php else: ?> class="xt xt_title"<?php endif; ?>>小题<?php echo ($i); ?>打分</span><?php } ?>
    <?php else: ?>
    试题打分<?php endif; ?>
    </TD>
</TR>
        <?php $__FOR_START_8522__=1;$__FOR_END_8522__=$times+1;for($ii=$__FOR_START_8522__;$ii < $__FOR_END_8522__;$ii+=1){ if(is_array($mark_array)): $j = 0; $__LIST__ = $mark_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($j % 2 );++$j;?><tr class="kgdf xt_con_<?php echo ($ii); ?> xt_con <?php if(($ii) != "1"): ?>none<?php endif; ?>">
    <TD class="tRight" style="width:80px"><?php echo ($vo["MarkName"]); ?>：</TD>
    <TD class="tLeft" ><SELECT id="xt_select_<?php echo ($ii); ?>_<?php echo ($j); ?>" class="mark large bLeft" NAME="Mark[]">
    <option value="">请选择</option>
    <?php if(is_array($vo["MarkListx"])): $i = 0; $__LIST__ = $vo["MarkListx"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item[3]); ?>" <?php if(is_array($edit["Markx"]["{$ii}"])): $i = 0; $__LIST__ = $edit["Markx"]["{$ii}"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$mk): $mod = ($i % 2 );++$i; if($mk == $item[3]): ?>selected="selected"<?php endif; endforeach; endif; else: echo "" ;endif; ?>><?php echo ($item[1]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
    </SELECT><?php if(($vo["Style"]) == "1"): ?>(辅助参数)<?php else: ?>(主要参数)<?php endif; ?></TD>
</tr><?php endforeach; endif; else: echo "" ;endif; } endif; ?>
<TR>
    <TD style="width:80px">&nbsp;</TD>
    <TD class="center"><div style="width:85%;margin:5px">
        <INPUT TYPE="hidden" id="DocID" name="DocID" value="<?php echo ($edit["DocID"]); ?>">
        <INPUT TYPE="hidden" id="TestID" name="TestID" value="<?php echo ($edit["TestID"]); ?>">
        <INPUT TYPE="hidden" name="act" value="<?php echo ($act); ?>">
        <INPUT TYPE="hidden" id="real" name="real" value="<?php echo ($real); ?>">
    <div class="impBtn fLeft"><INPUT tag='form1' id="datasave" u="<?php echo U('Test/Test/save');?>" TYPE="button" value="保存" class="save imgButton mysubmit"></div>
    <div class="impBtn fLeft m-l10" style="display:none"><INPUT TYPE="reset" class="reset imgButton" value="清空" ></div>
    </div></TD>
</TR>
<tr><td height="5" colspan="2" class="bottomTd" ></td></tr>
</table>
<script language="javascript">
$(document).bind("selectstart",function(){return false;});
$(document).ready(function(){
    var subjectID='<?php echo ($edit["SubjectID"]); ?>';
    var specialID='<?php echo ($edit["SpecialID"]); ?>';
    var typeID='<?php echo ($edit["TypesID"]); ?>';
    var skillID='<?php echo ($edit["SkillID"]); ?>';
    var capacityID='<?php echo ($edit["CapacityID"]); ?>';
    var knowID='<?php echo ($edit["KlID"]); ?>';
    var chapID='<?php echo ($edit["ChapterID"]); ?>';
    if("<?php echo ($act); ?>"=='edit'){
        $('#knowledge').allSelectLoad('/Test/Test',{"style":"getMoreData","list":"knowledgeList,chapterList,special,types,knowledge,chapter,skill,skillList,capacity,capacityList","subjectID":subjectID,"idList":{
        "knowledgeList":knowID,"chapterList":chapID,"skillList":skillID,"capacityList":capacityID,"special":specialID,"types":typeID,"knowledge":"0","chapter":"0","skill":"0","capacity":"0"}});
    }
    //写入小题结构
    if("<?php echo ($edit["IfChoose"]); ?>"==1){
        $('#showxt').css({'display':'block'});
        var str='';
        var tmp_i=1;
        <?php if(is_array($chooseList)): $i = 0; $__LIST__ = $chooseList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>str+='<p class="xtList">小题'+tmp_i+'：<label><INPUT TYPE="radio" class="choose'+tmp_i+' bLeft"  check="raido" warning="请选择测试类型" NAME="IfChoose'+tmp_i+'" value="0" <?php if(($vo["IfChoose"] == '0') or ($vo["IfChoose"] == '')): ?>checked="checked"<?php endif; ?>> 非选择题</label> '+
        '<label><INPUT TYPE="radio" class="choose'+tmp_i+' bLeft" NAME="IfChoose'+tmp_i+'" value="3" <?php if(($vo["IfChoose"]) == "3"): ?>checked="checked"<?php endif; ?>> 单选题</label> '+
        '<label><INPUT TYPE="radio" class="choose'+tmp_i+' bLeft" NAME="IfChoose'+tmp_i+'" value="2" <?php if(($vo["IfChoose"]) == "2"): ?>checked="checked"<?php endif; ?>> 多选题</label> '+
        '</p>';
        tmp_i++;<?php endforeach; endif; else: echo "" ;endif; ?>
        $('#xt').empty().html(str);
    }
});
$.testOperation.addTestWidthNum();
$.testOperation.delTestWidthNum();
$.testOperation.clearAllWidthNum();
$.testOperation.testChooseChange();
$.testOperation.resize();
    var input='<div>#str# <span class="delhang">x</span><input class="kl" name="kl[]" type="hidden" value="#value#"/></div>';
    var inputcp='<div>#str# <span class="delhang">x</span><input class="cp" name="cp[]" type="hidden" value="#value#"/></div>'
    var inputskill='<div>#str# <span class="delhang">x</span><input class="skill" name="skill[]" type="hidden" value="#value#"/></div>'
    var inputcapacity='<div>#str# <span class="delhang">x</span><input class="capacity" name="capacity[]" type="hidden" value="#value#"/></div>'

</script>