<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<TITLE>『<?php echo (C("WLN_WEB_NAME")); ?>管理平台』</TITLE>
<link rel="stylesheet" type="text/css" href="/Public/zjadmin/css/style.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" />
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
<!--基础文件，分别是jQuery基库和拖拽UI插件-->
<script src="/Public/plugin/jquery.ui.draggable.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" type="text/javascript"></script>
<script src="/Public/plugin/testOperation.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" type="text/javascript"></script>
<!-- 对话框核心JS文件和对应的CSS文件-->
<script src="/Public/plugin/alert/jquery.alerts.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" type="text/javascript"></script>
<link href="/Public/plugin/alert/jquery.alerts.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="/Public/plugin/ueditor/ueditor.config.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/ueditor/ueditor.all.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/editor.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script LANGUAGE="JavaScript">
//指定当前组模块URL地址 
var URL = '/Custom/CustomTest';
var APP     =     '';
var PUBLIC = '/Public';
var softdog=0;
<?php if(C("openKeysoft")== 1): ?>softdog=1;<?php endif; ?>
</script>
<script type="text/javascript" src="/Public/zjadmin/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
</HEAD>

<body>
<?php if(C("openKeysoft")== 1): ?><div style="display:none;"><embed id="s_simnew31"  type="application/npsyunew3-plugin" hidden="true"> </embed></div><?php endif; ?>
<div id="loader" >页面加载中...</div>
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content">
<!--  功能操作区域  -->
<div class="title"><?php echo ($pageName); ?> [ <A HREF="<?php echo U('Custom/CustomTest/index');?>">返回列表</A> ]</div>
<!-- 功能操作区域结束 -->
<div id="result" class="result none"></div>
<!-- 内容显示区域  -->
<FORM METHOD="POST" action="" id="form1">
<TABLE cellpadding="5" cellspacing="0" class="list" border="1">
<tr><td height="5" colspan="7" class="topTd" ></td></tr>
<TR>
    <TD class="tRight" width="100">编号：</TD>
    <TD class="tLeft" ><?php echo ($edit["TestID"]); ?> 【<?php echo ($edit["Status"]); ?>】
    </TD>
</TR>
<TR>
    <TD class="tRight tTop">试题内容：</TD>
    <TD class="tLeft">
        <div class='editContainers'> </div>
    </TD>
</TR>
<TR>
    <TD class="tRight tTop">备注：</TD>
    <TD class="tLeft">
        <textarea class=" bLeft" cols="90" rows="5"  name='remark' id='remark'><?php echo ($edit["Remark"]); ?></textarea>
    </TD>
</TR>
<TR>
    <TD class="tRight tTop">来源：</TD>
    <TD class="tLeft">
        <div>
        <input type='text' name='Source' value='<?php echo ($edit["Source"]); ?>' width="100" id="source">
        </div>
    </TD>
</TR>
<TR>
    <TD class="tRight" >所属学科：</TD>
    <TD class="tLeft" ><SELECT id="SubjectID" class="medium bLeft" NAME="SubjectID" check='Require' warning="所属学科不能为空">
    <option value="">请选择</option>
    <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label="<?php echo ($vo["SubjectName"]); ?>">
        <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $edit["SubjectID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
        </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
    <?php else: ?>
        <option value="0">请添加学科</option><?php endif; ?>
    </SELECT></TD>
</TR>
<TR>
    <TD class="tRight" width="100">所选年级：</TD>
    <TD class="tLeft" >  
        <select name='GradeID' id='grade'>
            <option value=''>-请选择-</option>
            <?php if(is_array($grade)): $i = 0; $__LIST__ = $grade;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value='<?php echo ($vo["GradeID"]); ?>' <?php if(($vo["GradeID"]) == $edit["GradeID"]): ?>selected="selected"<?php endif; ?>><?php echo ($vo["GradeName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
    </TD>
</TR>
<TR>
    <TD class="tRight tTop">试题类型：</TD>
    <TD class="tLeft">
        <select name='TypesID' id='types'>
            <option value=''>-请选择-</option>
        </select>
    </TD>
</TR>
<TR>
    <TD class="tRight tTop">试题难度：</TD>
    <TD class="tLeft">
        <label class="difficulty" title="0.801-0.999"><input type="radio" name='diff' value='0.801'/>容易</label>
        <label class="difficulty" title="0.601-0.800"><input type="radio" name='diff' value='0.601'/>较易</label>
        <label class="difficulty" title="0.501-0.600"><input type="radio" name='diff' value='0.501'/>一般</label>
        <label class="difficulty" title="0.301-0.500"><input type="radio" name='diff' value='0.301'/>较难</label>
        <label class="difficulty" title="0.001-0.300"><input type="radio" name='diff' value='0.001'/>困难</label>
    </TD>
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
                <?php if(is_array($optionnum)): $k = 0; $__LIST__ = $optionnum;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$onum): $mod = ($k % 2 );++$k;?><p class="optionnum_<?php echo ($k); ?> optionnum"><?php if($edit["IfChoose"] == '1'): ?>小题<?php echo ($k); ?>：<?php endif; ?><label><INPUT TYPE="text" class="optionnum<?php echo ($k); ?> bLeft"  warning="请填入选项宽度" NAME="optionnum<?php echo ($k); ?>" size="2" value="<?php echo ($onum); ?>" ></label> </p><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
    </TD>
</TR>
<TR>
    <TD class="tRight" style="width:80px">知识点：</TD>
    <TD class="tLeft" ><SELECT id="knowledge" class="knowledge bLeft selectKnowledge" NAME="KlID">
    <option value="">请选择</option>
    </SELECT> <div class="impBtn" style="display:inline;padding:3px 0px;"><input id="addkl" name="addkl" class="add imgButton" type="button" value="添加"></div>
    <div id="knowledgeList" class="klinput"></div>
    </TD>
</TR>
<TR>
    <TD class="tRight" style="width:80px">章节：</TD>
    <TD class="tLeft" ><SELECT id="chapter" class="chapter bLeft selectChapter" NAME="ChapterID">
    <option value="">请选择</option>
    </SELECT> <div class="impBtn" style="display:inline;padding:3px 0px;"><input id="addcp" name="addcp" class="add imgButton" type="button" value="添加"/></div> <div class="impBtn" style="display:inline;padding:3px 0px;"><a id="adddcp" style="cursor:pointer;">载入默认章节</a></div>
    <div id="chapterList" class='cpinput'></div>
    </TD>
</TR>

<TR>
    <TD class="tRight" style="width:80px">技能：</TD>
    <TD class="tLeft" ><SELECT id="skill" class="skill bLeft selectSkill" NAME="SkillID">
    <option value="">请选择</option>
    </SELECT> <div class="impBtn" style="display:inline;padding:3px 0px;"><input id="addskill" name="addskill" class="add imgButton" type="button" value="添加"/></div> <!-- <div class="impBtn" style="display:inline;padding:3px 0px;"><a id="adddcp" style="cursor:pointer;">载入默认章节</a></div> -->
    <div id="skillList" class='skillinput'></div>
    </TD>
</TR>

<TR>
    <TD class="tRight" style="width:80px">能力：</TD>
    <TD class="tLeft" ><SELECT id="capacity" class="capacity bLeft selectCapacity" NAME="CapacityID">
    <option value="">请选择</option>
    </SELECT> <div class="impBtn" style="display:inline;padding:3px 0px;"><input id="addcapacity" name="addcapacity" class="add imgButton" type="button" value="添加"/></div> <!-- <div class="impBtn" style="display:inline;padding:3px 0px;"><a id="adddcp" style="cursor:pointer;">载入默认章节</a></div> -->
    <div id="capacityList" class='capacityinput'></div>
    </TD>
</TR>

<TR>
    <TD class="tRight tTop">添加：</TD>
    <TD class="tLeft"><?php echo (date("Y-m-d H:i:s",$edit["AddTime"])); ?>
    </TD>
</TR>
<TR>
    <TD style="width:80px">&nbsp;</TD>
    <TD class="center"><div style="width:85%;margin:5px">
        <INPUT TYPE="hidden" id="TestID" name="TestID" value="<?php echo ($edit["TestID"]); ?>">
        <INPUT TYPE="hidden" name="act" value="<?php echo ($act); ?>">
        <INPUT TYPE="hidden" name="UserName" id='UserName' value="<?php echo ($edit["UserName"]); ?>">
        <INPUT TYPE="hidden" name="UserID" id='UserID' value="<?php echo ($edit["UserID"]); ?>">
    <div class="impBtn fLeft "><INPUT TYPE="button" class="reset imgButton" id="formatTest" value="格式化试题" ></div>
    <div class="impBtn fLeft m-l10"><INPUT tag='form1'  TYPE="button" value="保存" id='saveTest' class="save imgButton "></div>
    </div></TD>
</TR>
</TABLE>
</FORM>
<!-- 列表显示区域结束 -->
</div>
<script src="/Public/plugin/ueditor/ueditor.config.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" type="text/javascript"></script>
<script src="/Public/plugin/ueditor/ueditor.all.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" type="text/javascript"></script>
<script src="/Public/plugin/editor.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" type="text/javascript"></script>
<script src="/Public/plugin/formatTest.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" type="text/javascript"></script>
<script>
//一次触发所有ajax请求
$(document).ready(function(){
    var subjectID='<?php echo ($edit["SubjectID"]); ?>';
    var specialID='<?php echo ($edit["SpecialID"]); ?>';
    var typeID='<?php echo ($edit["TypesID"]); ?>';
    var knowID='<?php echo ($edit["KlID"]); ?>';
    var chapID='<?php echo ($edit["ChapterID"]); ?>';
    var capID='<?php echo ($edit["CapacityID"]); ?>';
    var skillID='<?php echo ($edit["SkillID"]); ?>';
    var diff='<?php echo ($edit["Diff"]); ?>';
    if("edit"=='edit'){
        $('#knowledge').allSelectLoad('/Custom/CustomTest',{"style":"getMoreData","list":"knowledgeList,chapterList,skillList,capacityList,types,knowledge,chapter,skill,capacity","subjectID":subjectID,"idList":{
        "knowledgeList":knowID,"chapterList":chapID,"skillList":skillID,"capacityList":capID,"types":typeID,"knowledge":"0","chapter":"0","skill":"1","capacity":"1"}});
        $('.difficulty input').each(function(){
        if($(this).val() == diff){
            $(this).attr('checked',true);
        }
        });
    }
    //写入小题结构
    if("<?php echo ($edit["IfChoose"]); ?>"==1){
        $('#showxt').css({'display':'block'});
        var str='';
        var tmp_i=1;
        <?php if(is_array($chooseList)): $i = 0; $__LIST__ = $chooseList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>str+='<p>小题'+tmp_i+'：<label><INPUT TYPE="radio" class="choose'+tmp_i+' bLeft"  check="raido" warning="请选择测试类型" NAME="IfChoose'+tmp_i+'" value="0" <?php if(($vo["IfChoose"] == '0') or ($vo["IfChoose"] == '')): ?>checked="checked"<?php endif; ?>> 非选择题</label> '+
        '<label><INPUT TYPE="radio" class="choose'+tmp_i+' bLeft" NAME="IfChoose'+tmp_i+'" value="3" <?php if(($vo["IfChoose"]) == "3"): ?>checked="checked"<?php endif; ?>> 单选题</label> '+
        '<label><INPUT TYPE="radio" class="choose'+tmp_i+' bLeft" NAME="IfChoose'+tmp_i+'" value="2" <?php if(($vo["IfChoose"]) == "2"): ?>checked="checked"<?php endif; ?>> 多选题</label> '+
        '</p>';
        tmp_i++;<?php endforeach; endif; else: echo "" ;endif; ?>
        $('#xt').empty().html(str);
    }
    //添加试题宽度
    $.testOperation.addTestWidthNum();
    $.testOperation.delTestWidthNum();
    $.testOperation.clearAllWidthNum();
    $.testOperation.testChooseChange();
    $.testOperation.resize();
    //载入默认章节
    var input='<div>#str# <span class="delhang">x</span><input class="kl" name="kl[]" type="hidden" value="#value#"/></div>';
    var inputcp='<div>#str# <span class="delhang">x</span><input class="cp" name="cp[]" type="hidden" value="#value#"/></div>';
    var inputskill='<div>#str# <span class="delhang">x</span><input class="skill" name="skill[]" type="hidden" value="#value#"/></div>';
    var inputcapacity='<div>#str# <span class="delhang">x</span><input class="capacity" name="capacity[]" type="hidden" value="#value#"/></div>';

    function autoAddAttr(name,input,tag){
        if(name == 'Skill'){
            if($('.select'+name).last().val().indexOf(tag)==-1){
                    alert('请选择正确的技能');
                    return false;
                }
        }
        if(name == 'Capacity'){
            if($('.select'+name).last().val().indexOf(tag)==-1){
                    alert('请选择正确的能力');
                    return false;
                }
        }
        var kid=$('.select'+name).last().val().replace(tag,'');
        var xx_s="";
        $('.select'+name).each(function(){
            xx_s+=' >> '+$(this).find("option:selected").text();
        });
        var xx=input.replace('#value#',kid).replace('#str#',xx_s.replace(/┉/g,'').replace('┝',''));
        var obj='#'+name.toLowerCase()+'List';
        if($(obj).html().indexOf('value="'+kid+'"')==-1 && $(obj).html().indexOf('value='+kid+'')==-1){
            $(obj).append(xx);
            $('.delhang').css({'padding':'0px 5px','cursor':'pointer','color':'#f00'});
        }
    }
    $('#addskill').live('click',function(){
        autoAddAttr('Skill',inputskill,'t');
    });
    $('#addcapacity').live('click',function(){
        autoAddAttr('Capacity',inputcapacity,'t');
    });

    $('#adddcp').live('click',function(){
        var result='';
        $('.kl').each(function(){
            result += $(this).val()+",";
        });
        var kl=result.substring(0, result.length-1);
        var testid=$('#TestID').val();
        $.get(U('Test/Test/getchapter?kl='+kl+'&id='+testid+'&'+Math.random()),function(msg){
            //权限验证
            if(checkPower(msg)=='error'){
                return false;
            }
            var data = msg['data'];
            if(data){
                var flag=0;
                for(var i=0;i<data.length;i++){
                    var xx=inputcp.replace('#value#',data[i]['ChapterID']).replace('#str#',data[i]['ChapterName']);
                    if($('.cpinput').html().indexOf('value="'+data[i]['ChapterID']+'"')==-1 && $('.cpinput').html().indexOf("value='"+data[i]['ChapterID']+"'")==-1 && $('.cpinput').html().indexOf('value='+data[i]['ChapterID']+' ')==-1){
                        $('.cpinput').append(xx);
                        flag=1;
                    }
                }
                $('.delhang').css({'padding':'0px 5px','cursor':'pointer','color':'#f00'});
                if(!flag){
                    alert('默认章节已经全部载入！');
                }
            }else{
                alert('暂无对应章节！');
            }
        },'json');
    });
    $('#addcp').live('click',function(){
        //if($('.chapter').last().val().indexOf('c')==-1){
        //    alert('请选择正确的数据');
        //    return false;
        //}
        if(!$('.selectChapter:eq(1)').val()){
            alert('请选择正确的章节');
            return false;
        }
        
        var cid=$('.selectChapter').last().val().replace('c','');
        var tmp_position=0;
        if(!cid){
            tmp_position=1;
            cid=$('.selectChapter').last().prev().val().replace('c','');
        }
        var xx_s="";
        $('.selectChapter').each(function(i){
            if(!(tmp_position==1 && $('.selectChapter').length==(i+1)))
            xx_s+=' >> '+$(this).find("option:selected").text();
        });
        var xx=inputcp.replace('#value#',cid).replace('#str#',xx_s.replace(/┉/g,'').replace('┝',''));
        
        if($('#chapterList').html().indexOf('value="'+cid+'"')==-1 && $('#chapterList').html().indexOf('value='+cid+'')==-1){
            $('#chapterList').append(xx);
            $('.delhang').css({'padding':'0px 5px','cursor':'pointer','color':'#f00'});
        }
    });
    $('#addkl').live('click',function(){
        if($('.selectKnowledge').last().val().indexOf('t')==-1){
            alert('请选择正确的知识点');
            return false;
        }
        
        var kid=$('.selectKnowledge').last().val().replace('t','');
        var xx_s="";
        $('.selectKnowledge').each(function(){
            xx_s+=' >> '+$(this).find("option:selected").text();
        });
        var xx=input.replace('#value#',kid).replace('#str#',xx_s.replace(/┉/g,'').replace('┝',''));
        
        
        if($('#knowledgeList').html().indexOf('value="'+kid+'"')==-1 && $('#knowledgeList').html().indexOf('value='+kid+'')==-1){
            $('#knowledgeList').append(xx);
            $('.delhang').css({'padding':'0px 5px','cursor':'pointer','color':'#f00'});
        }
    });

    $('.delhang').live('click',function(){
            $(this).parent().remove();
    });
    $('.delhang').live('mouseover',function(){
            $(this).css({'background-color':'#f00','color':'#fff'});
    });
    $('.delhang').live('mouseout',function(){
            $(this).css({'background-color':'#fff','color':'#f00'});
    });
})
    var data = new Object(<?php echo ($data); ?>);
    $.Editor.init(U('Index/upload?dir=customTest'),'.editContainers');
    $.Editor.createContent(data['Test']);
    $.Editor.createSolution(data['Answer']);
    $.Editor.createAnalyze(data['Analytic']);
    $('#SubjectID').subjectSelectChange('/Custom/CustomTest',{'style':'getMoreData','list':'grade,types,knowledge,chapter','ifConfirm':'1'});
    $('.selectChapter').chapterSelectChange('/Custom/CustomTest');
    $('.selectKnowledge').knowledgeSelectChange('/Custom/CustomTest');
//form表单验证
$('#saveTest').click(function(){
    var can='';
    var table={};
    FormatTextManager.isForamt = false;
    for(var editor in $.Editor.instance){
            var editor = $.Editor.instance[editor];
            var name = editor.getOpt('textarea');
            table[name]=editor.getContent().replace(/\r\n|\r|\n/g, '');
            if(name =='Test' && !editor.hasContents()){
                alert('试题题文内容不能为空！');
                can='yes';
                 false;
            }
            if(name =='Answer' && !editor.hasContents()){
                alert('试题答案不能为空！');
                can='yes';
                return false;
            }
            
    }
    if(can=='yes'){
        return false;
    }
    table['TestID']=$('#TestID').val(); //试题ID
    
    if(!$('#SubjectID').val()){
        alert('试题所在年级不能为空！');
        return false;
    }
    table['SubjectID']=$('#SubjectID').val(); //学科
    
    if(!$('#grade').val()){
        alert('试题所在年级不能为空！');
        return false;
    }
    table['GradeID']=$('#grade').val(); //年级
    
    if(!$('#types').val()){
        alert('试题所在题型不能为空！');
        return false;
    }
    table['TypesID']=$('#types').val(); //题型
    
    if(!$("input[name='diff']:checked").val()){
        alert('试题难度不能为空！');
        return false;
    }
    table['Diff']=$("input[name='diff']:checked").val() //难度
    table['Source']=$("#source").val() //来源
    table['Remark']=$("#remark").val() //备注
    table['UserName']=$("#UserName").val() //用户名
    table['UserID']=$("#UserID").val() //用户ID
    
    table['KlID'] = [];
    var knowledge = $(".kl");
    knowledge.each(function(){
        table['KlID'].push($(this).val());  //知识点
    });
    table['ChapterID'] = [];
    var chapter = $(".cp");
    chapter.each(function(){
        table['ChapterID'].push($(this).val()); //章节
    });

    table['SkillID'] = [];
    var skill = $('.skill:not(select)');
    skill.each(function(){
        table['SkillID'].push($(this).val()); //技能
    });

    table['CapacityID'] = [];
    var capacity = $('.capacity:not(select)');
    capacity.each(function(){
        table['CapacityID'].push($(this).val()); //能力
    });

    FormatTextManager.types=$('#types');
    if(FormatTextManager.formatContent()){
        alert(FormatTextManager.err);  
        return false;
    }
    var msg=FormatTextManager.getTopic();
    table['attributes']=msg;
    console.log(table);
    $.post(U('Custom/CustomTest/save'),{'data':table},function(data){
        //console.log(data);return false;
        var msg=data;
        if(msg['data']!='success'){
            alert(msg['data']);
        }else{
            window.location.href = U('Custom/CustomTest/index');
        }
    })
})
$('#formatTest').click(function(){
    FormatTextManager.isForamt = true;
    FormatTextManager.types=$('#types');
    if(FormatTextManager.formatContent()){
        alert(FormatTextManager.err);   
    }
})
</script>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>