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
var URL = '/Guide/CaseLore';
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
</script>
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content" >
<div class="title"><?php echo ($pageName); ?> [ <A HREF="javascript:history.go(-1);">返回上一页</A> ]</div>
<!--  功能操作区域  -->
<div class="operate">
    <!--<div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="新增" onclick="" class="btadd add imgButton"></div>-->
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="intro" value="入库" onclick="" class="intro intro imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>

    <!-- 查询区域 -->
    <FORM METHOD="POST" ACTION="/Guide/CaseLore">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="name" value="<?php echo ($_REQUEST['name']); ?>" title="试题编号查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR><TD class="tRight" width="80">知识编号：</TD>
            <TD><INPUT TYPE="text" NAME="LoreID" class="small" value="<?php echo ($_REQUEST['TestID']); ?>" ></TD>
            <TD class="tRight" width="80">所属学科：</TD>
            <TD><SELECT id="subject" class="normal bLeft" NAME="SubjectID" check='Require' warning="所属学科不能为空">
            <option value="">请选择</option>
            <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label='<?php echo ($vo["SubjectName"]); ?>'>
                <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $edit["SubjectID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php else: ?>
                <option value="0">请添加学科</option><?php endif; ?>
            </SELECT></TD>
            <TD class="tRight" width="80">是否入库：</TD>
            <TD><SELECT name="IfIntro">
                <OPTION value="">请选择</OPTION>
                <OPTION value="0">未入库</OPTION>
                <OPTION value="1">已入库</OPTION>
            </SELECT></TD>

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
        <th width="100">学科</th>
        <th width="600">内容</th>
        <th width="60">栏目</th>
        <th width="180">章节</th>
        <th width="60">是否入库</th>
        <th>操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
        <td><input type="checkbox" class="key" value="<?php echo ($node["LoreID"]); ?>"></td>
        <td>
            <?php echo ($node["LoreID"]); ?>
        </td>
        <td><?php echo ($node["SubjectName"]); ?></td>
        <td width="400">
        <div class="text_source">来源：<a href="<?php echo U('Guide/CaseLoreDoc/edit',array('id'=>$node['DocID']));?>" title="<?php echo ($node["DocID"]); ?>:<?php echo ($node["DocName"]); ?>"><?php echo ($node["DocID"]); ?>:<?php echo ($node["DocName"]); ?></a></div>
        <div class="testdivbak"><a style="cursor:pointer" class="editLore" thisid="<?php echo ($node["LoreID"]); ?>"><p><?php echo ((isset($node["Content"]) && ($node["Content"] !== ""))?($node["Content"]):"无</p>"); ?></a></div></td>
        <td class="menu"><?php echo ((isset($node["MenuName"]) && ($node["MenuName"] !== ""))?($node["MenuName"]):"<font color='red'>无</font>"); ?></td>
        <td class="chapter"><?php echo ((isset($node["ChapterName"]) && ($node["ChapterName"] !== ""))?($node["ChapterName"]):"<font color='red'>无</font>"); ?></td>
        <td wid="<?php echo ($node["LoreID"]); ?>" class="status"><?php if($node["IfIntro"] == 1): ?><a style="color:red;cursor:pointer" class="system" status="1">是</a>
            <a style="cursor:pointer;display:none" class="system" status="0">否</a>
            <?php elseif($node["IfIntro"] == 0): ?><a style="cursor:pointer" class="system" status="0">否</a>
            <a style="cursor:pointer;display:none;color:red" class="system" status="1">是</a>
            <?php else: ?>
            作业模板<?php endif; ?>
        </td>
        <td><!--<a href="#" class="btedit" thisid="<?php echo ($node["TestID"]); ?>">编辑</a>&nbsp;&nbsp;<br/>-->
        <a href="javascript:void(0);" class="editLore" thisid="<?php echo ($node["LoreID"]); ?>">修改</a>&nbsp;&nbsp;<br/>
        <a href="<?php echo U('Guide/CaseLore/replaceLore',array('LoreID'=>$node['LoreID']));?>">替换</a>&nbsp;<br/>
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
<script>
    $('.editLore').live('click',function(){
        var loreID=$(this).attr('thisid');
        jInfo('加载中请稍候。。。','加载数据');
        $.post(U('Guide/CaseLore/edit?LoreID='+loreID+'&'+Math.random()),function(data){
            //权限验证
            if(checkPower(data)=='error'){
                return false;
            }
            jFrame(data['data'],'编辑试题：编号'+loreID);
        })
    })
    $('.system').live('click',function(){
        exchange('/Guide/CaseLore',$(this));
    })
    $('.intro').click(function(){
        var stop=0;
        if($('input[class="key"]:checked').length<1){
            alert('请选择操作项');
            return false;
        }
        $('input[class="key"]:checked').each(function(){
            if($(this).parent().parent().find('.status').find('a:visible').attr('status')==1){
                alert('您选择的数据中存在已入库，请重试！');
                stop=1;
                return false;
            }
        })
        if(stop==1){
            return false;
        }
        valueChanges('/Guide/CaseLore',$('#checkList'));
    })
    $('.save').live('click',function(){
        if($('#caseMenu').find('option:selected').val()==''){
            alert('请选择所属栏目');
            return false;
        }
        var menuID=$('#caseMenu').find('option:selected').val();
        var chapterList='';

        if($('.cp').length<1){
            alert('请选择章节');
            return false;
        }
        $('.cp').each(function(){
            chapterList+=','+$(this).val();
        })
        chapterList=chapterList.substring(1);
        var IfIntro = $('input[name="IfIntro"]:checked').val();
        var loreID=$('.loreID').attr('value');
        $.post(U('Guide/CaseLore/save'),{'LoreID':loreID,'MenuID':menuID,'chapterList':chapterList,'IfIntro':IfIntro},function(data){
            if(checkPower(data)=='error'){
                return false;
            }
            msg = data['data'];
            var loreID=msg['LoreID'];
            var chapterName=msg['ChapterName'];
            var menuName=msg['MenuName'];
            var ifIntro=msg['IfIntro'];
            var obj;
            $('input[class="key"]').each(function(){
                if($(this).val()==loreID){
                    obj=$(this).parent().parent();
                }
            })
            $(obj).find('.menu').html(menuName);
            $(obj).find('.chapter').html(chapterName);
            if(ifIntro==0){
                $(obj).find('.status').html('<a style="cursor:pointer" class="system" status="0">否</a><a style="cursor:pointer;display:none;color:red" class="system" status="1">是</a>');
            }else{
                $(obj).find('.status').html('<a style="color:red;cursor:pointer" class="system" status="1">是</a><a style="cursor:pointer;display:none" class="system" status="0">否</a>');
            }
            $('#popup_container').remove();
            $("#popup_overlay").remove();
        })
    })
    $('.selectChapter').chapterSelectChange("/Guide/CaseLore");
</script>
<!-- 主页面结束 -->

</body>
</html>