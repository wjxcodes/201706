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
var URL = '/Doc/DocInner';
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
    <script type="text/javascript" src="/Public/plugin/uploadify/jquery.uploadify.min.js"></script>
<link rel="stylesheet" type="text/css" href="/Public/plugin/uploadify/uploadify.css">
<style>
.sful{width:500px;margin:0px;padding:0px;}
.sful li{float:left;width:150px;list-style-type:none;margin:0px;padding:0px;}
.sfred{color:red;}
.sfblack{color:black;}
#fileQueue{height:200px;}
</style>
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content">
<!--  功能操作区域  -->
<div class="title"><?php echo ($pageName); ?> [ <A HREF="/Doc/DocInner">返回列表</A> ]</div>
<!-- 功能操作区域结束 -->
<div id="result" class="result none"></div>
<!-- 内容显示区域  -->
<FORM METHOD="POST" action="" id="form1" enctype="multipart/form-data">
<TABLE cellpadding="5" cellspacing="0"  class="list" border="1">
<tr><td height="5" colspan="2" class="topTd" ></td></tr>
<TR>
    <TD class="tRight" width="100">上传文件地址：</TD>
    <TD class="tLeft" >
        /work
    </TD>
</TR>
<TR>
    <TD class="tRight" width="100">提取文件方式：</TD>
    <TD class="tLeft" >
        <label><input class="getstyle" checked='checked' type="radio" name="getstyle" value="0" /> 提取知识点 </label>
        <label><input class="getstyle" type="radio" name="getstyle" value="1" /> 不提取知识点 </label>
        <label><input class="getstyle" type="radio" name="getstyle" value="2" /> 按章节上传 </label>
    </TD>
</TR>
<TR>
    <TD colspan="2">
        <table cellpadding=3 cellspacing=3 border="1" class="noborder param param1 none"><TR>
            <TD class="tRight" width="100" height="35">所属年份：</TD>
            <TD class="tLeft" >
                <select name="DocYear" id="DocYear">
                <option value="">请选择</option>
                <?php $__FOR_START_516__=$thisYear;$__FOR_END_516__=1990;for($vo=$__FOR_START_516__;$vo > $__FOR_END_516__;$vo+=-1){ ?><option value="<?php echo ($vo); ?>" <?php if(($vo) == $edit["DocYear"]): ?>selected="selected"<?php endif; ?>> <?php echo ($vo); ?></option><?php } ?>
                </select>
            </TD>
        </TR>
        <TR>
            <TD class="tRight" width="100" height="35">所属学科：</TD>
            <TD class="tLeft" ><SELECT id="SubjectID" class="large bLeft" NAME="SubjectID">
            <option value="">请选择</option>
            <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label='<?php echo ($vo["SubjectName"]); ?>'>
                <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $edit["SubjectID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
               </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php else: ?>
                <option value="0">请添加学科</option><?php endif; ?>
            </SELECT></TD>
        </TR>
        <TR>
            <TD class="tRight" width="100">所属年级：</TD>
            <TD class="tLeft" >
                <select name="DocGrade" class="DocGrade" id="grade">
                    <option value="">请选择学科</option>
                    <?php if(is_array($grade)): $i = 0; $__LIST__ = $grade;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$gvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($gvo["GradeID"]); ?>" <?php if(($gvo["GradeID"]) == $edit["GradeID"]): ?>selected="selected"<?php endif; ?>><?php echo ($gvo["GradeName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </TD>
        </TR>
        <TR>
            <TD class="tRight" width="100" height="35">所属类型：</TD>
            <TD class="tLeft" ><SELECT id="TypeID" class="large bLeft" NAME="TypeID">
            <option value="">请选择</option>
            <?php if($doctypeArray): if(is_array($doctypeArray)): $i = 0; $__LIST__ = $doctypeArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["TypeID"]); ?>" <?php if(($vo["TypeID"]) == $edit["TypeID"]): ?>selected="selected"<?php endif; ?>><?php echo ($vo["TypeName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php else: ?>
                <option value="0">请添加属性</option><?php endif; ?>
            </SELECT></TD>
        </TR>
        <TR>
            <TD class="tRight tTop">是否测试：</TD>
            <TD class="tLeft"><label><INPUT TYPE="radio" class="bLeft IfTest" NAME="IfTest" value="0"> 不测试</label> <label><INPUT TYPE="radio" class="bLeft IfTest" NAME="IfTest" value="1" checked="checked" > 测试</label> </TD>
        </TR>
        <TR>
            <TD class="tRight" width="100">所属省份：</TD>
            <TD class="tLeft" ><ul class="sful">
            <?php if($areaArray): if(is_array($areaArray)): $i = 0; $__LIST__ = $areaArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><label><input class="AreaID" name="AreaID[]" type="checkbox" value="<?php echo ($vo["AreaID"]); ?>" > <?php echo ($vo["AreaName"]); ?></label></li><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php else: ?>
                <li>请添加省份</li><?php endif; ?>
            </ul></TD>
        </TR>
        <tr>
        <td class="tRight tTop">文档来源：</td>
        <td class="tLeft">
            <?php if(is_array($docSource)): $i = 0; $__LIST__ = $docSource;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$source): $mod = ($i % 2 );++$i;?><label>
                    <input type="radio" class="bLeft" name="SourceID1" class='SourceID1' value="<?php echo ($source["SourceID"]); ?>"
                    <?php if(($edit["SourceID"] == '') and ($source["IfDefault"] == '1')): ?>checked="checked"{#elseif condition="$edit.SourceID eq $source.SourceID" #}checked="checked"<?php endif; ?>
                    > <?php echo ($source["SourceName"]); ?>
                </label><?php endforeach; endif; else: echo "" ;endif; ?>
        </td>
    </tr>
        </table>
        <table cellpadding=3 cellspacing=3 border="1" style="border:0px;" class="noborder param param2 none"><TR>
            <TD class="tRight" width="100" height="35">所属年份：</TD>
            <TD class="tLeft" >
                <select name="DocYear2" id="DocYear2">
                <option value="">请选择</option>
                <?php $__FOR_START_17389__=$thisYear;$__FOR_END_17389__=1990;for($vo=$__FOR_START_17389__;$vo > $__FOR_END_17389__;$vo+=-1){ ?><option value="<?php echo ($vo); ?>" <?php if(($vo) == $edit["DocYear"]): ?>selected="selected"<?php endif; ?>> <?php echo ($vo); ?></option><?php } ?>
                </select>
            </TD>
        </TR>
        <TR>
            <TD class="tRight" width="100" height="35">所属学科：</TD>
            <TD class="tLeft" ><SELECT id="SubjectID2" class="large bLeft" NAME="SubjectID2">
            <option value="">请选择</option>
            <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label='<?php echo ($vo["SubjectName"]); ?>'>
                <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $edit["SubjectID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
               </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php else: ?>
                <option value="0">请添加学科</option><?php endif; ?>
            </SELECT></TD>
        </TR>
        <TR>
            <TD class="tRight" width="100">所属年级：</TD>
            <TD class="tLeft" >
                <select name="DocGrade2" class="DocGrade2" id="searchgrade">
                    <option value="">请选择学科</option>
                    <?php if(is_array($grade)): $i = 0; $__LIST__ = $grade;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$gvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($gvo["GradeID"]); ?>" <?php if(($gvo["GradeID"]) == $edit["GradeID"]): ?>selected="selected"<?php endif; ?>><?php echo ($gvo["GradeName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </TD>
        </TR>
        <TR>
            <TD class="tRight" width="100" height="35">所属类型：</TD>
            <TD class="tLeft" ><SELECT id="TypeID2" class="large bLeft" NAME="TypeID2">
            <option value="">请选择</option>
            <?php if($doctypeArray): if(is_array($doctypeArray)): $i = 0; $__LIST__ = $doctypeArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["TypeID"]); ?>" <?php if(($vo["TypeID"]) == $edit["TypeID"]): ?>selected="selected"<?php endif; ?>><?php echo ($vo["TypeName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php else: ?>
                <option value="0">请添加属性</option><?php endif; ?>
            </SELECT></TD>
        </TR>
        <TR class="none">
            <TD class="tRight tTop">是否测试：</TD>
            <TD class="tLeft"><label><INPUT TYPE="radio" class="bLeft IfTest2" NAME="IfTest2" value="0" checked="checked"> 不测试</label> <label><INPUT TYPE="radio" class="bLeft IfTest2" NAME="IfTest2" value="1" > 测试</label> </TD>
        </TR>
        <TR>
            <TD class="tRight" width="100" height="35">所属章节：</TD>
            <TD class="tLeft" ><SELECT id="searchchapter" class="chapter bLeft selectChapter" NAME="ChapterID">
                <option value="">请选择</option>
                </SELECT> <div class="impBtn" style="display:inline;padding:3px 0px;"><input id="addcp" name="addcp" class="add imgButton" type="button" value="添加"/></div>
                <div id="cpinput"></div>
            </TD>
        </TR>
        <tr>
        <td class="tRight tTop">文档来源：</td>
        <td class="tLeft">
            <?php if(is_array($docSource)): $i = 0; $__LIST__ = $docSource;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$source): $mod = ($i % 2 );++$i;?><label>
                    <input type="radio" class="bLeft" name="SourceID2" class='SourceID2' value="<?php echo ($source["SourceID"]); ?>"
                    <?php if(($edit["SourceID"] == '') and ($source["IfDefault"] == '1')): ?>checked="checked"{#elseif condition="$edit.SourceID eq $source.SourceID" #}checked="checked"<?php endif; ?>
                    > <?php echo ($source["SourceName"]); ?>
                </label><?php endforeach; endif; else: echo "" ;endif; ?>
        </td>
    </tr>
        </table>
    </TD>
</TR>
<TR>
    <TD class="tRight" width="100">上传文件：</TD>
    <TD class="tLeft" ><table border="0" style="border:0px;margin:1px;padding:1px;"><tr style="border:0px;margin:1px;padding:1px;">
        <td style="border:0px;margin:1px;padding:1px;"><input type="file" name="file_upload" id="file_upload" multiple="true" /></td>
        <td style="border:0px;margin:1px;padding:1px;"><a href="javascript:void(0);" onclick="uploadfile()" class="btn">上传</a></td>
        <td style="border:0px;margin:1px;padding:1px;"><a href="javascript:jQuery('#file_upload').uploadify('cancel','*')" class="btn">取消上传</a></td>
        </tr></table>
    </TD>
</TR>
<TR>
    <TD class="tRight" width="100">上传队列：</TD>
    <TD class="tLeft" ><div id="queue"></div></TD>
</TR>
<TR>
    <TD class="tRight" width="100">上传结果：</TD>
    <TD class="tLeft" ><div id="resulttt"></div></TD>
</TR>
<tr><td height="5" colspan="2" class="bottomTd" ></td></tr>
</TABLE>
</FORM>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

<script>
    $(function(){
        var upUrl=U('Public/uploadify?s=<?php echo ($tkey); ?>&u=<?php echo ($userName); ?>');
        $("#file_upload").uploadify({
            'formData'    : {'m':Math.random()},
            'swf'     : '/Public/plugin/uploadify/uploadify.swf',
            'uploader'     : upUrl,
            'cancelImg'    : '/Public/plugin/uploadify/cancel.png',
            'queueID'        : 'queue',
            'fileTypeExt'       : '*.doc;*.docx;*.xls;*.xlsx', //允许文件上传类型,和fileDesc一起使用.
            'fileTypeDesc'      : '*.doc;*.docx;*.xls;*.xlsx',  //将不允许文件类型,不在浏览对话框的出现.
            'buttonImage' : '/Public/plugin/uploadify/select.jpg',
            'buttonText'  : '上传',
            'mothod'        : 'post',
            'auto'          : false,
            'multi'         : true,
            'onUploadSuccess':function(file, data, response){
                if(data.indexOf('Warning')!=-1 && data.indexOf('Content-Length')!=-1){
                    alert('文件大小超出限制');
                }else if(data.indexOf('Warning')!=-1){
                    alert(data);
                }else if(data=='type error'){
                    alert('您上传的文件类型有误！请上传*.doc,*.docx,*.xls,*.xlsx;文件');
                }else if(data==''){
                    alert('文件检测失败，请检查文档内容。');
                }else{
                    var arr=data.split('|');
                    if(data.indexOf('error')!=-1){$('#resulttt').append('<div class="sfred">'+arr[1]+'</div>');}
                    else if(data.indexOf('success')!=-1){$('#resulttt').append('<div class="sfblack">'+arr[1]+'--上传成功</div>');}
                }
            }
        });
        $('.getstyle').live('click',function(){
            $('.param').each(function(){
                $(this).css({'display':'none'});
            });
            $('.param'+$(this).val()).css({'display':'block'});
        });
    });
    function uploadfile(){
            var chapterid=getD();
            var tt=getT();
            if(tt=='2' && chapterid==''){
                alert('请添加章节！');
                return;
            }
            $('#file_upload').uploadify('settings','formData',{'t':getT(),'y':getY(),'b':getB(),'p':getP(),'r':getA(),'c':getC(),'cp':getD(),'gd':getGD(),'ds':getDocSource(),'x':Math.random()});jQuery('#file_upload').uploadify('upload','*');
    }
    function getT(){
        var output='';
        $('.getstyle').each(function(){
            if($(this).attr('checked')=='checked'){
                output = $(this).val();
            }
        });
        return output;
    }
    function getY(){
        var tt=getT();
        if(tt=='1'){
            return $('#DocYear').val();
        }else if(tt=='2'){
            return $('#DocYear2').val();
        }
        return '';
    }
    function getB(){
        var tt=getT();
        if(tt=='1'){
            return $('#SubjectID').val();
        }else if(tt=='2'){
            return $('#SubjectID2').val();
        }
        return '';
    }
    function getP(){
        var tt=getT();
        if(tt=='1'){
            return $('#TypeID').val();
        }else if(tt=='2'){
            return $('#TypeID2').val();
        }
        return '';
    }
    function getA(){
            var areaid='';
            $('.AreaID').each(function(){
                if($(this).attr('checked')=='checked'){
                    areaid+='#'+$(this).val();
                }
            });
            if(areaid.length>0) areaid=areaid.substr(1);
            return areaid;
    }
    function getD(){
            var chapterid='';
            $('.cp').each(function(){
                chapterid+='#'+$(this).val();
            });
            if(chapterid.length>0) chapterid=chapterid.substr(1);
            
            return chapterid;
    }
    function getC(){
        var output='';
        var tt=getT();
        if(tt=='1'){
            $('.IfTest').each(function(){
                if($(this).attr('checked')=='checked'){
                    output = $(this).val();
                }
            });
        }else if(tt=='2'){
            $('.IfTest2').each(function(){
                if($(this).attr('checked')=='checked'){
                    output = $(this).val();
                }
            });
        }
        return output;
    }
    //获取文档来源
    function getDocSource(){
        var output='';
        var tt=getT();
        if(tt=='1'){
           output=$('input[name="SourceID1"]:checked').val();
        }else if(tt=='2'){
           output=$('input[name="SourceID2"]:checked').val();
        }
        return output;
    } 
    //获取年级
    function getGD(){
        var output='';
        var tt=getT();
        if(tt=='1'){
            return $('.DocGrade').val();
        }else if(tt=='2'){
            return $('.DocGrade2').val();
        }
        return output;
    }
    //添加章节考点
    $('#addcp').live('click',function(){
        if($('.selectChapter').last().val().indexOf('c')==-1){
            alert('请选择正确的数据');
            return false;
        }
        
        var cid=$('.selectChapter').last().val().replace('c','');
        var xx_s="";
        $('.selectChapter').each(function(){
            xx_s+=' >> '+$(this).find("option:selected").text();
        });
        var inputcp='<div>#str# <span class="delhang">x</span><input class="cp" name="cp[]" type="hidden" value="#value#"/></div>';
        var xx=inputcp.replace('#value#',cid).replace('#str#',xx_s.replace(/┉/g,'').replace('┝',''));
        
        if($('#cpinput').html().indexOf('value="'+cid+'"')==-1 && $('#cpinput').html().indexOf('value='+cid+'')==-1){
            $('#cpinput').append(xx);
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
//一次触发所有ajax请求
$('#SubjectID').subjectSelectChange('/Doc/DocInner',{'style':'getMoreData','list':'grade'});
$('#SubjectID2').subjectSelectChange('/Doc/DocInner',{'style':'getMoreData','list':'grade,chapter','search':'search'});
$('.selectChapter').chapterSelectChange('/Doc/DocInner');
</script>

</body>
</html>