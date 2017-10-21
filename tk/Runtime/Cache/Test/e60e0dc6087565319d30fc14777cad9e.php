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
var URL = '/Test/Test';
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
<link type="text/css" rel="stylesheet" href="/Public/zjadmin/css/newedit.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" />
<script src="/Public/zjadmin/js/newEdit.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript">
var currentUrl=U('Test');
    $(function(){
        $.myTest.init();
        $(document).bind("selectstart",function(){return false;});
        $.newEdit.init();
    });
    $.myTest={
        init:function(){
             this.bindEvent();
        },
        //绑定事件
        bindEvent:function(){
            //编辑试题弹出框
            $('.nowedit').live('click',function(){
                var a=$(this).attr('thisid');
                jInfo('加载中请稍候。。。','加载数据');
                //获取数据
                $.get(U('Test/Test/edit?id='+a), function(data){
                    jClose();
                    //权限验证
                    if(checkPower(data)=='error'){
                        return false;
                    }
                    jFrame(data['data'],'编辑试题：编号'+a);
                });
            });
            //审核
            $('.btcheck').live('click',function(){
                var keyValue = $(this).attr('thisid');
                if(!keyValue){
                    var result='';
                    $('.key').each(function(){
                        if($(this).attr('checked')=='checked'){
                            result += $(this).val()+",";
                        }
                    });
                    keyValue = result.substring(0, result.length-1);
                }
                if(!keyValue){
                    alert('请选择审核项！');
                    return false;
                }
                jInfo('审核中请稍候。。。','审核数据');
                $.post(U('Test/Test/check'),{'id':keyValue,'m':Math.random()}, function(data){
                    jClose();
                    //权限验证
                    if(checkPower(data)=='error'){
                        return false;
                    }
                    $('body').append(data['data']);
                });
            });
            //锁定
            $('.btlock').live('click',function(){
                var keyValue = $(this).attr('thisid');
                if(!keyValue){
                    var result='';
                    $('.key').each(function(){
                        if($(this).attr('checked')=='checked'){
                            result += $(this).val()+",";
                        }
                    });
                    keyValue = result.substring(0, result.length-1);
                }
                if(!keyValue){
                    alert('请选择锁定项！');
                    return false;
                }
                jInfo('锁定中请稍候。。。','锁定数据');
                $.post(U('Test/Test/check'),{'id':keyValue,'Status':1,'m':Math.random()}, function(data){
                    jClose();
                    //权限验证
                    if(checkPower(data)=='error'){
                        return false;
                    }
                    $('body').append(data['data']);
                });
            });
            //获取与之相关的重复试题列表[重复功能]
            $('.showDuplicate').live('click',function(){
                var a=$(this).attr('thisid');
                jInfo('加载中请稍候。。。','加载数据');
                //获取数据
                $.post(U('Test/Test/duplicateList'),{'duplicate':a,'times':Math.random()}, function(data){
                    jClose();
                    //权限验证
                    if(checkPower(data)=='error'){
                        return false;
                    }
                    jFrame(data['data'],'清除重复');
                });
            });
        }
    }
</script>
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content" >
<div class="title"><?php echo ($pageName); ?> [ <a href="<?php echo U('Test/Test/index',array('errortest'=>1));?>">错误试题查看</a> ]  [ <A HREF="/Test/Test">返回试题列表</A> ]  [ <A HREF="javascript:history.go(-1);">返回上一页</A> ]</div>
<!--  功能操作区域  -->
<div class="operate">
    <!--<div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="新增" onclick="" class="btadd add imgButton"></div>-->
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="intro" value="入库" onclick="" class="btintro intro imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="check" value="审核" onclick="" class="btcheck check imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="lock" value="锁定" onclick="" class="btlock lock imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>

    <!-- 查询区域 -->
    <FORM METHOD="POST" ACTION="/Test/Test">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="name" value="<?php echo ($_REQUEST['name']); ?>" title="试题编号查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR><TD class="tRight" width="80">试题编号：</TD>
            <TD><INPUT TYPE="text" NAME="TestID" class="small" value="<?php echo ($_REQUEST['TestID']); ?>" ></TD>
            <TD class="tRight" width="80">所属学科：</TD>
            <TD><SELECT id="subject" class="normal bLeft" NAME="SubjectID" check='Require' warning="所属学科不能为空">
            <option value="">请选择</option>
            <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label='<?php echo ($vo["SubjectName"]); ?>'>
                <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $edit["SubjectID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php else: ?>
                <option value="0">请添加学科</option><?php endif; ?>
            </SELECT></TD><TD class="tRight" width="50">状态：</TD>
            <TD><SELECT class="small bLeft" NAME="Status">
            <option value="">选择</option>
            <option value="0" <?php if(($_REQUEST['Status']) == "0"): ?>selected="selected"<?php endif; ?>>正常</option>
            <option value="1" <?php if(($_REQUEST['Status']) == "1"): ?>selected="selected"<?php endif; ?>>锁定</option>
            </SELECT></TD>
            <TD class="tRight" width="50">排序：</TD>
            <TD><SELECT class="medium bLeft" NAME="order">
            <option value="">选择</option>
            <option value="Diff" <?php if(($_REQUEST['order']) == "Diff"): ?>selected="selected"<?php endif; ?>>难度 降序</option>
            <option value="TestID" <?php if(($_REQUEST['order']) == "TestID"): ?>selected="selected"<?php endif; ?>>id 降序</option>
            </SELECT></TD>
            <td class="tRight" width="90px">使用范围：</td>
                            <td>
                                <select class="normal bLeft" name="ShowWhere">
                                    <option value="">请选择</option>
                                    <option value="1" <?php if(($_REQUEST['ShowWhere']) == "1"): ?>selected="selected"<?php endif; ?>>通用</option>
                                    <option value="0" <?php if(($_REQUEST['ShowWhere']) == "0"): ?>selected="selected"<?php endif; ?>>组卷专用</option>
                                    <option value="2" <?php if(($_REQUEST['ShowWhere']) == "2"): ?>selected="selected"<?php endif; ?>>提分专用</option>
                                    <option value="3" <?php if(($_REQUEST['ShowWhere']) == "3"): ?>selected="selected"<?php endif; ?>>前台禁用</option>
                                </select>
                            </td>
        </tr>
        </TABLE>
    </div>
        </FORM>
</div>
<!-- 功能操作区域结束 -->

<!-- 列表显示区域  -->
<div class="list" >
<div id="result" class="result none"></div>
<!-- Think 系统列表组件开始 -->
<table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
    <tr><td height="5" colspan="8" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th width="40">编号</th>
        <th width="100">错误</th>
        <th width="400">试题及来源</th>
        <th width="60">学科/题型/专题/类型/难度/年级/用户自评分</th>
        <th width="100">知识点</th>
        <th width="200">章节</th>
        <th width="45">操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
        <td><input type="checkbox" class="key" value="<?php echo ($node["TestID"]); ?>"></td>
        <td>
        <?php if(($node["ReplaceID"]) == ""): else: ?><font color="red">已替换</font><br /><?php endif; ?>
        <?php echo ($node["TestID"]); ?>(<?php echo ($key+1); ?>)<div id="status<?php echo ($node["TestID"]); ?>"><?php if(($node["Status"]) == "0"): ?><span class="btlock" thisid="<?php echo ($node["TestID"]); ?>">正常</span><?php else: ?><span class="btcheck" thisid="<?php echo ($node["TestID"]); ?>"><font color="red">锁定</font></span><?php endif; ?></div>
        <?php if(($node["Duplicate"]) == "0"): else: ?><a href="javascript:void(0)" class="showDuplicate" thisid="<?php echo ($node["Duplicate"]); ?>"><font color="red">重复</font></a><br /><?php endif; ?>
        </td>
        <td id="error<?php echo ($node["TestID"]); ?>"><font color="red"><?php echo ((isset($node["error"]) && ($node["error"] !== ""))?($node["error"]):"<font color='black'>无</font>"); ?></font></td>
        <td width="400">
        <div class="text_source">来源：<a href="<?php echo U('Test/Test/index',array('DocID'=>$node[DocID]));?>" title="<?php echo ($node["DocID"]); ?>:<?php echo ($node["DocName"]); ?>"><?php echo ($node["DocID"]); ?>:<?php echo ($node["DocName"]); ?></a></div>
        <div class="testdivbak"><a href="javascript:void(0);" class="nowedit" thisid="<?php echo ($node["TestID"]); ?>"><p><?php echo ((isset($node["Test"]) && ($node["Test"] !== ""))?($node["Test"]):"无</p>"); ?></a></div></td>
        <td><?php echo ((isset($node["SubjectName"]) && ($node["SubjectName"] !== ""))?($node["SubjectName"]):"<font color='red'>无</font>"); ?><br/>
            <span id="types<?php echo ($node["TestID"]); ?>"><?php echo ((isset($node["TypesName"]) && ($node["TypesName"] !== ""))?($node["TypesName"]):"<font color='red'>无</font>"); ?></span><br/>
            <span id="special<?php echo ($node["TestID"]); ?>"><?php echo ((isset($node["SpecialName"]) && ($node["SpecialName"] !== ""))?($node["SpecialName"]):"<font color='red'>无</font>"); ?></span><br/>
            <span id="choose<?php echo ($node["TestID"]); ?>"><?php if(($node["IfChoose"]) == "0"): ?>非选择题<?php endif; if(($node["IfChoose"]) == "1"): ?>复合题<?php endif; if(($node["IfChoose"]) == "2"): ?>多选题<?php endif; if(($node["IfChoose"]) == "3"): ?>单选题<?php endif; ?></span><br/>
            <span id="diff<?php echo ($node["TestID"]); ?>"><?php echo ((isset($node["Diff"]) && ($node["Diff"] !== ""))?($node["Diff"]):"<font color='red'>无</font>"); ?></span><br/>
            <span id="grade<?php echo ($node["TestID"]); ?>"><?php echo ((isset($node["GradeName"]) && ($node["GradeName"] !== ""))?($node["GradeName"]):"<font color='red'>无</font>"); ?></span><br/>
             <span><?php echo ((isset($node["Score"]) && ($node["Score"] !== ""))?($node["Score"]):"未设置分值"); ?></span>
        </td>
        <td id="knowledge<?php echo ($node["TestID"]); ?>"><?php echo ((isset($node["KlName"]) && ($node["KlName"] !== ""))?($node["KlName"]):"<font color='red'>无</font>"); ?></td>
        <td id="chapter<?php echo ($node["TestID"]); ?>"><?php echo ((isset($node["ChapterName"]) && ($node["ChapterName"] !== ""))?($node["ChapterName"]):"<font color='red'>无</font>"); ?></td>
        <td><!--<a href="#" class="btedit" thisid="<?php echo ($node["TestID"]); ?>">编辑</a>&nbsp;&nbsp;<br/>-->
        <a href="javascript:void(0);" class="nowedit" thisid="<?php echo ($node["TestID"]); ?>">修改</a>&nbsp;&nbsp;<br/>
        <a href="<?php echo U('Test/Test/replace',array('TestID'=>$node[TestID]));?>">替换</a>&nbsp;<br/>
        </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="8" class="bottomTd"></td></tr>
</table>
<!-- Think 系统列表组件结束 -->
</div>
<!--  分页显示区域 -->
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<form id="hiddenform" action="?" method="post" style="display:none">
<input name="id" id="testidlist" value=""/>
</form>
<!-- 主页面结束 -->

</body>
</html>