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
var URL = '/Doc/WlnDoc';
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
        <div class="title"><?php echo ($pageName); ?> [ <a href="<?php echo U('Doc/WlnDoc/index');?>">返回列表</a> ] [ <a href="javascript:history.go(-1);">返回上一页</a> ]</div>
        <!-- 功能操作区域结束 -->
        <div id="result" class="result none"></div>
        <!-- 内容显示区域  -->
        <form method="post" action="" id="form1" enctype="multipart/form-data">
            <table cellpadding=5 cellspacing=0 class="list" border="1">
                <tr><td height="5" colspan="7" class="topTd" ></td></tr>
                <tr>
                    <td class="tRight" width="100">文档名称：</td>
                    <td class="tLeft" >
                        <input type="text" class="large bLeft" check='Require' warning="文档名称不能为空" name="DocName" value="<?php echo ((isset($edit["DocName"]) && ($edit["DocName"] !== ""))?($edit["DocName"]):''); ?>">
                    </td>
                </tr>
                <tr>
                    <td class="tRight" width="100">所属年份：</td>
                    <td class="tLeft" >
                        <select name="DocYear">
                            <option value="">请选择</option>
                            <?php $__FOR_START_19832__=$thisYear;$__FOR_END_19832__=1990;for($vo=$__FOR_START_19832__;$vo > $__FOR_END_19832__;$vo+=-1){ ?><option value="<?php echo ($vo); ?>" <?php if(($vo) == $edit["DocYear"]): ?>selected="selected"<?php endif; ?>> <?php echo ($vo); ?></option><?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="tRight" width="100">所属学科：</td>
                    <td class="tLeft" >
                        <select id="subject" class="large bLeft" name="SubjectID" check='Require' warning="所属学科不能为空">
                            <option value="">请选择</option>
                            <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label="<?php echo ($vo["SubjectName"]); ?>">
                                    <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $edit["SubjectID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                    </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
                            <?php else: ?>
                                <option value="0">请添加学科</option><?php endif; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="tRight" style="width:80px">章节：</td>
                    <td class="tLeft" >
                        <select id="chapter" class="chapter bLeft selectChapter" name="chapterID[]">
                            <option value="">请选择</option>
                            <?php if(is_array($chapterArray)): $i = 0; $__LIST__ = $chapterArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><option value="<?php echo ($sub["ChapterID"]); ?>" last="<?php echo ($sub["Last"]); ?>"><?php echo ($sub["ChapterName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                        <div id="cpinput"></div>
                    </td>
                </tr>
                <tr>
                    <td class="tRight" width="100">所属能力：</td>
                    <td class="tLeft" >
                        <select name="Ability" id="ability">
                            <option value="">请选择</option>
                            <?php if(is_array($ability)): $i = 0; $__LIST__ = $ability;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["AbID"]); ?>" <?php if(($edit['AbilitID']) == $item["AbID"]): ?>selected="selected"<?php endif; ?>> <?php echo ($item["AbilitName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select> * 此属性和章节需要同时选中才有效
                    </td>
                </tr>
                <tr>
                    <td class="tRight" width="100">所属年级：</td>
                    <td class="tLeft" >
                        <select name="DocGrade" class="DocGrade" id='grade'>
                            <?php if($edit["SubjectID"] == ''): ?><option value="">请先选择学科</option>
                            <?php else: ?>
                                <option value="">请选择年级</option>
                                <?php if(is_array($grade)): $i = 0; $__LIST__ = $grade;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$gvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($gvo["GradeID"]); ?>" <?php if(($gvo["GradeID"]) == $edit["GradeID"]): ?>selected="selected"<?php endif; ?>><?php echo ($gvo["GradeName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="tRight" width="100">所属省份：</td>
                    <td class="tLeft" >
                        <ul class="sful">
                            <?php if($areaArray): if(is_array($areaArray)): $i = 0; $__LIST__ = $areaArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><label><input name="AreaID[]" type="checkbox" value="<?php echo ($vo["AreaID"]); ?>" > <?php echo ($vo["AreaName"]); ?></label></li><?php endforeach; endif; else: echo "" ;endif; ?>
                            <?php else: ?>
                                <li>请添加省份</li><?php endif; ?>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="tRight" width="100">所属类型：</td>
                    <td class="tLeft" >
                        <select id="type" class="large bLeft" name="TypeID" check='Require' warning="所属属性不能为空">
                            <option value="">请选择</option>
                            <?php if($docTypeArray): if(is_array($docTypeArray)): $i = 0; $__LIST__ = $docTypeArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["TypeID"]); ?>" <?php if(($vo["TypeID"]) == $edit["TypeID"]): ?>selected="selected"<?php endif; ?>><?php echo ($vo["TypeName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            <?php else: ?>
                                <option value="0">请添加属性</option><?php endif; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="tRight" >文档路径：</td>
                    <td class="tLeft" >
                        <input type="file" class="large bLeft" name="photo" /><br/>
                        <?php if($edit['DocPath'] != ''): ?>doc-word:<a href="<?php echo U('Doc/WlnDoc/showWord',array('docID'=>$edit['DocID'],'style'=>1));?>" target="_blank">下载word</a><br/><?php endif; ?>
                        <?php if($edit['DocHtmlPath'] != ''): ?>doc-html:<a href="<?php echo U('Doc/WlnDoc/showWord',array('docID'=>$edit['DocID']));?>" target="_blank">打开网页</a><br/><?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="tRight" >听力上传：</td>
                    <td class="tLeft" >
                        <input type="file" class="large bLeft" name="audio" /><br/>
                        <?php if($edit['Hearing'] != 0): ?><a href="<?php echo U('Doc/WlnDoc/downloadAudioFile', array('docId'=>$edit['Hearing']));?>">下载听力</a><br/><?php endif; ?>&nbsp;<font color='red'>与word同时上传并且文件较大时，建议分两次上传</font>
                    </td>
                </tr>
                <tr>
                    <td class="tRight">试卷总分：</td>
                    <td class="tLeft">
                        <input name="TotalScore" type="text" value="<?php echo ((isset($edit["TotalScore"]) && ($edit["TotalScore"] !== ""))?($edit["TotalScore"]):0); ?>" check='Require' warning="试卷总分不能为空"/>分
                    </td>
                </tr>
                <tr>
                    <td class="tRight">答题时间：</td>
                    <td class="tLeft">
                        <input name="TestTime" type="text" value="<?php echo ((isset($edit["TestTime"]) && ($edit["TestTime"] !== ""))?($edit["TestTime"]):0); ?>" check='Require' warning="答题时间不能为空"/>分钟
                    </td>
                </tr>
                <tr>
                    <td class="tRight tTop">是否测试：</td>
                    <td class="tLeft">
                        <label>
                            <input type="radio" class="bLeft IfTest"  check='raido' warning="请选择是否测试" name="IfTest" value="0" <?php if(($edit["IfTest"] == '0') or ($edit["IfTest"] == '')): ?>checked="checked"<?php endif; ?>> 不测试
                        </label>
                        <label>
                            <input type="radio" class="bLeft IfTest" name="IfTest" value="1" <?php if(($edit["IfTest"]) == "1"): ?>checked="checked"<?php endif; ?> <?php if(($edit["IfTest"]) == "2"): ?>checked="checked"<?php endif; ?>> 测试
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tRight" >文档描述：</td>
                    <td class="tLeft" >
                        <textarea name="Description" cols="50" rows="5">
                            <?php echo ($edit["Description"]); ?>
                        </textarea>
                    </td>
                </tr>
                <tr>
                <td class="tRight" >是否推荐</td>
                    <td class="tLeft" ><input type="radio" name="IfRecom"  value="1" <?php if($edit['IfRecom'] == 1): ?>checked="checked"<?php endif; ?> />推荐
                        <input type="radio" name="IfRecom"  value="0" <?php if($edit['IfRecom'] == 0): ?>checked="checked"<?php endif; ?> />不推荐</td>
                </tr>
                <tr>
                    <td class="tRight tTop">状态：</td>
                    <td class="tLeft">
                        <label>
                            <input type="radio" class="bLeft"  check='raido' warning="请选择状态" name="Status" value="0" <?php if(($edit["Status"] == '0') or ($edit["Status"] == '')): ?>checked="checked"<?php endif; ?>> 正常
                        </label>
                        <label>
                            <input type="radio" class="bLeft" name="Status" value="1" <?php if(($edit["Status"]) == "1"): ?>checked="checked"<?php endif; ?>> 锁定
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tRight tTop">使用范围：</td>
                    <td class="tLeft">
                        <label>
                            <input type="radio" class="bLeft"  check='raido' warning="请选择使用范围" name="ShowWhere" value="1" <?php if(($edit["ShowWhere"] == '1') or ($edit["Status"] == '')): ?>checked="checked"<?php endif; ?>> 通用
                        </label>
                        <label>
                            <input type="radio" class="bLeft" name="ShowWhere" value="0" <?php if(($edit["ShowWhere"]) == "0"): ?>checked="checked"<?php endif; ?>> 组卷专用
                        </label>
                        <label>
                            <input type="radio" class="bLeft" name="ShowWhere" value="2" <?php if(($edit["ShowWhere"]) == "2"): ?>checked="checked"<?php endif; ?>> 提分专用
                        </label>
                        <label>
                            <input type="radio" class="bLeft" name="ShowWhere" value="3" <?php if(($edit["ShowWhere"]) == "3"): ?>checked="checked"<?php endif; ?>> 前台禁用
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tRight tTop">提分测试类型：</td>
                    <td class="tLeft">
                        <label><input type="radio" class="bLeft"  check='raido' warning="请选择测试类型" name="AatTestStyle" value="0" <?php if(($edit["AatTestStyle"] == '0') or ($edit["Status"] == '')): ?>checked="checked"<?php endif; ?>> 通用</label>
                        <label><input type="radio" class="bLeft" name="AatTestStyle" value="1" <?php if(($edit["AatTestStyle"]) == "1"): ?>checked="checked"<?php endif; ?>> 专题打分专用（第八种测试）</label>
                    </td>
                </tr>
                <tr>
                    <td class="tRight tTop">文档来源：</td>
                    <td class="tLeft">
                        <?php if(is_array($docSource)): $i = 0; $__LIST__ = $docSource;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$source): $mod = ($i % 2 );++$i;?><label>
                                <input type="radio" class="bLeft" name="SourceID" value="<?php echo ($source["SourceID"]); ?>"
                                <?php if(($edit["SourceID"] == '') and ($source["IfDefault"] == '1')): ?>checked="checked"<?php endif; if(($edit["SourceID"]) == $source["SourceID"]): ?>checked="checked"<?php endif; ?>
                                > <?php echo ($source["SourceName"]); ?>
                            </label><?php endforeach; endif; else: echo "" ;endif; ?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td class="center">
                        <div style="width:85%;margin:5px">
                            <input type="hidden" name="DocID" value="<?php echo ($edit["DocID"]); ?>">
                            <input type="hidden" name="act" value="<?php echo ($act); ?>">
                            <input type="hidden" name="DacID" value="<?php echo ($edit["DacID"]); ?>">
                            <div class="impBtn fLeft">
                                <input tag='form1' u="<?php echo U('Doc/WlnDoc/save');?>" type="button" value="保存" class="save imgButton mysubmit">
                            </div>
                            <div class="impBtn fLeft m-l10">
                                <input type="reset" class="reset imgButton" value="清空" >
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td height="5" colspan="7" class="bottomTd" ></td>
                </tr>
            </table>
        </form>
        <!-- 列表显示区域结束 -->
    </div>
<!-- 主体内容结束 -->
</div>
<script>
$(document).ready(function(){
    $('.sful input').inputCheck('<?php echo ($edit["AreaList"]); ?>'); //载入省份数据
    $('#type').docTypeSelectChange('/Doc/WlnDoc'); //文档属性切换改变测试类型
    var s='<?php echo ($edit["SubjectID"]); ?>';
    var chapterParentStr="<?php echo ($chapterParentStr); ?>";
    $('.selectChapter').chapterSelectChange("/Doc/WlnDoc");
    if("<?php echo ($act); ?>"=="edit"){
        $('#chapter').chapterSelectLoad('/Doc/WlnDoc',chapterParentStr);
    }

})
$('#subject').subjectSelectChange('/Doc/WlnDoc',{'style':'getMoreData','list':'chapter,grade,ability'});

</script>
<!-- 主页面结束 -->

</body>
</html>