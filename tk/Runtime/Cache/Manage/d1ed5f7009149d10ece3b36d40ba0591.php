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
var URL = '/Manage/Chapter';
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
<script language="javascript">
$(document).ready(function(){
    var s='<?php echo ($edit["SubjectID"]); ?>';
    var t='<?php echo ($edit["TID"]); ?>';
    var z='<?php echo ($edit["PID"]); ?>';
    var k='<?php echo ($edit["KlID"]); ?>';
    var x='';
    if(s!=''){
        $.get(U('Chapter/getMsg?s='+s+'&t='+t),function(msg){
            if(checkPower(msg)=='error'){//权限验证
                return false;
            }
            var data=msg['data'];
            $('#TID').html('<option value="">请选择</option><option value="0">新建版本</option>'+data[0]);
            $('#TID option').each(function(){
                if($(this).val()==t) $(this).attr('selected','selected');
            });
            if(t){
                $('#zsd').html('<option value="">请选择</option><option value="0">新建章节</option>'+data[1]);
                $('#zsd option').each(function(){
                    if($(this).val()==z) $(this).attr('selected','selected');
                });
            }
/*            if(t){
                $.get(U("/Manage/Chapter/getzsd?s="+s+"&t="+t),function(data){
                    $('#zsd').html('<option value="">请选择</option><option value="0">新建章节</option>'+data['data']);
                    $('#zsd option').each(function(){
                        if($(this).val()==z) $(this).attr('selected','selected');
                    });
                });
            }*/
        });
    }
    
    var input='<div>#str# <span class="delhang">x</span><input class="kl" name="kl[]" type="hidden" value="#value#"/></div>';
    if(s){
        if('<?php echo ($edit["ChapterSet"]); ?>'=='1'){
            $('.displayKey').css({'display':'table-row'});
        }
            $('#knowledge').html('<option value="">加载中。。。</option>');
            $.post(U('Index/getData'),{'subjectID':s,'style':'knowledge'},function(data){
                if(checkPower(data)=='error'){//权限验证
                    return false;
                }
                data['data']=setOption(data['data'],'','knowledge');
                $('#knowledge').html(data['data']);
            });
            if(k!='0'){
                //+s+"-l-kl-id-"+k
                $.post(U('Index/getData'),{'style':'knowledgeList','ID':k},function(msg){
                    var data=msg['data'];
                    if(data){
                    for(var i=0;i<data.length;i++){
                        var xx=input.replace('#value#',data[i]['KlID']).replace('#str#',data[i]['KlName']);
                        if($('#klinput').html().indexOf('value="'+data[i]['id']+'"')==-1){
                            $('#klinput').append(xx);
                        }
                    }
                    $('.delhang').css({'padding':'0px 5px','cursor':'pointer','color':'#f00'});
                    }
                });
            }
    }
    $('#subject').subjectSelectChange('/Manage/Chapter',{'style':'getMoreData','list':'knowledge'});
    $('#subject').change(function(){
        if($(this).find('option:selected').attr('kid')=='1'){
            $('.displayKey').css({'display':'table-row'});
        }else{
            $('.displayKey').css({'display':'none'});
        }
            $('#klinput').html('');
            $('#knowledge').nextAll('select').remove();
            if($(this).val()!=''){
                $.get(U('Chapter/getMsg?s='+$(this).val()),function(data){
                    //权限验证
                    if(checkPower(data)=='error'){
                        return false;
                    }
                    $('#TID').html('<option value="">请选择</option><option value="0">新建版本</option>'+data['data'][0]);
                });

            }else{
                $('#TID').html('<option value="">请选择</option><option value="0">新建版本</option>');
                $('#knowledge').html('<option value="">请选择</option>');
            }
            $('#zsd').html('<option value="">请选择</option><option value="0">新建章节</option>');
    });
    $('#TID').change(function(){
        if($(this).val()!='' && $(this).val()!=0){
            $.get(U('Chapter/getMsg?s='+$('#subject').val()+'&t='+$(this).val()),function(data){
                //权限验证
                if(checkPower(data)=='error'){
                    return false;
                }
                $('#zsd').html('<option value="">请选择</option><option value="0">新建章节</option>'+data['data'][1]);
            });
        }else{
            $('#zsd').html('<option value="">请选择</option><option value="0">新建章节</option>');
        }
    });
    //修改框事件
    $('.selectKnowledge').knowledgeSelectChange('/Manage/Chapter');
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
        
        
        if($('#klinput').html().indexOf('value="'+kid+'"')==-1 && $('#klinput').html().indexOf('value='+kid+'')==-1){
            $('#klinput').append(xx);
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
});
</script>
<!-- 主页面开始 -->
<div id="main" class="main" >
    <!-- 主体内容  -->
    <div class="content">
        <!--  功能操作区域  -->
        <div class="title">
            <?php echo ($pageName); ?>
            [ <A HREF="<?php echo U('Chapter/index');?>">返回列表</A> ]
            [ <A HREF="javascript:history.go(-1);">返回上一页</A> ]
        </div>
        <!-- 功能操作区域结束 -->
        <div id="result" class="result none"></div>
        <!-- 内容显示区域  -->
        <form method="post" action="" id="form1" >
            <table cellpadding=5 cellspacing=0 class="list" border="1">
                <tr><td height="5" colspan="7" class="topTd" ></td></tr>
                <tr>
                    <td class="tRight" >所属学科：</td>
                    <td class="tLeft" >
                        <select id="subject" class="medium bLeft" name="SubjectID" check='Require' warning="所属学科不能为空">
                            <option value="">请选择</option>
                            <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label="<?php echo ($vo["SubjectName"]); ?>">
                                        <?php if($vo.sub): if(is_array($vo["sub"])): $i = 0; $__LIST__ = $vo["sub"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" kid="<?php echo ($item["ChapterSet"]); ?>" <?php if(($item["SubjectID"]) == $edit["SubjectID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                    </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
                            <?php else: ?>
                                <option value="0">请添加学科</option><?php endif; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="tRight" width="100">所属版本：</td>
                    <td class="tLeft" ><select name="TID" id="TID">
                        <option value="">请选择</option>
                        <option value="0">新建版本</option>
                        <?php if(is_array($editionArray)): $i = 0; $__LIST__ = $editionArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["ChapterID"]); ?>" <?php if(($item["ChapterID"]) == $edit["ChapterID"]): ?>selected="selected"<?php endif; ?>><?php echo ($item["ChapterName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select></td>
                </tr>
                <tr>
                    <td class="tRight" width="100">所属章节：</td>
                    <td class="tLeft" ><select name="PID" id="zsd">
                        <option value="">请选择</option>
                        <option value="0">顶级章节</option>
                        <?php echo ($chapterOption); ?>
                    </select></td>
                </tr>
                <tr>
                    <td class="tRight" width="100">名称：</td>
                    <td class="tLeft" >
                        <input type="text" class="large bLeft"  check='Require' warning="名称不能为空" NAME="ChapterName" value="<?php echo ((isset($edit["ChapterName"]) && ($edit["ChapterName"] !== ""))?($edit["ChapterName"]):''); ?>">
                    </td>
                </tr>
                <tr>
                    <td class="tRight" width="100">是否显示：</td>
                    <td class="tLeft" >
                        <label >
                            <input type="radio" name="ifShow" value="0" <?php if($edit["IfShow"] == '0'): ?>checked='checked'<?php endif; ?>/> 否
                        </label>
                        <label style="margin-left:10px; ">
                            <input type="radio" name="ifShow" value="1" <?php if(($edit["IfShow"] == '1') or ($edit["IfShow"] == '')): ?>checked='checked'<?php endif; ?> /> 是
                        </label>
                    </td>
                </tr>
                <tr class="displayKl">
                    <td class="tRight" style="width:80px">关联知识点：</td>
                    <td class="tLeft" >
                        <select id="knowledge" class="selectKnowledge bLeft" NAME="KlID">
                            <option value="">请选择</option>
                        </select>
                        <div class="impBtn" style="display:inline;padding:3px 0px;">
                            <input id="addkl" name="addkl" class="add imgButton" type="button" value="添加">
                        </div>
                        <div id="klinput"></div>
                    </td>
                </tr>
                <tr class="displayKey none">
                    <td class="tRight" style="width:80px">关联关键字：</td>
                    <td class="tLeft" >
                        <textarea name="keyword" cols="30" rows="6" class="keyword">
                            <?php echo ($edit["Keyword"]); ?>
                        </textarea> * 每行一个
                    </td>
                </tr>
                <tr>
                    <td ></td>
                    <td class="center">
                        <div style="width:85%;margin:5px">
                            <input type="hidden" name="ChapterID" value="<?php echo ($edit["ChapterID"]); ?>">
                            <input type="hidden" name="act" value="<?php echo ($act); ?>">
                            <div class="impBtn fLeft">
                                <input tag='form1' u='<?php echo U("Chapter/save");?>' type="button" value="保存" class="save imgButton mysubmit">
                            </div>
                            <div class="impBtn fLeft m-l10">
                                <input type="reset" class="reset imgButton" value="清空" ></div>
                        </div>
                    </td>
                </tr>
                <tr><td height="5" colspan="7" class="bottomTd" ></td></tr>
            </table>
        </form>
        <!-- 列表显示区域结束 -->
    </div>
    <!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>