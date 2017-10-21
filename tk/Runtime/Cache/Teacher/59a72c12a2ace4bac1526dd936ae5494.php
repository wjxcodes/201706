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
var URL = '/Teacher/TaskB';
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
<div class="content" >
<div class="title"><?php echo ($pageName); ?> <a href="<?php echo U('Teacher/TaskB/tongji');?>">[ 任务统计 ]</a> <a href="<?php echo U('Teacher/TaskB/stongji');?>">[ 审核任务统计 ]</a></div>
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="新增" onclick="" class="btadd add imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="编辑" onclick="" class="btedit edit imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>

    <!-- 查询区域 -->
    <FORM METHOD="POST" ACTION="/Teacher/TaskB">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="name" value="<?php echo ($_REQUEST['name']); ?>" title="用户名查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR>
            <TD class="tRight" width="60">文档ID：</TD>
            <TD><INPUT TYPE="text" NAME="DocID" class="small" value="<?php echo ($_REQUEST['DocID']); ?>" ></TD>
            <TD class="tRight" width="75">标引用户：</TD>
            <TD><INPUT TYPE="text" NAME="UserName" class="small" value="<?php echo ($_REQUEST['UserName']); ?>" ></TD>
            <TD class="tRight" width="75">审核用户：</TD>
            <TD><INPUT TYPE="text" NAME="CheckName" class="small" value="<?php echo ($_REQUEST['CheckName']); ?>" ></TD>
            <TD class="tRight" width="60">任务ID：</TD>
            <TD><INPUT TYPE="text" NAME="WorkID" class="small" value="<?php echo ($_REQUEST['WorkID']); ?>" ></TD>
            <TD class="tRight" width="45">状态：</TD>
            <TD><SELECT class="small bLeft" NAME="Status">
            <option value="">选择</option>
            <option value="0" <?php if(($_REQUEST['Status']) == "0"): ?>selected="selected"<?php endif; ?>>未完成</option>
            <option value="1,null" <?php if(($_REQUEST['Status']) == "1,null"): ?>selected="selected"<?php endif; ?>>待审核</option>
            <option value="1,0" <?php if(($_REQUEST['Status']) == "1,0"): ?>selected="selected"<?php endif; ?>>教师审核中</option>
            <option value="1,1" <?php if(($_REQUEST['Status']) == "1,1"): ?>selected="selected"<?php endif; ?>>教师审核完成</option>
            <option value="2" <?php if(($_REQUEST['Status']) == "2"): ?>selected="selected"<?php endif; ?>>已完成</option>
            <option value="3" <?php if(($_REQUEST['Status']) == "3"): ?>selected="selected"<?php endif; ?>>重做</option>
            </SELECT></TD>
        </TR>
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
    <tr><td height="5" colspan="11" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th width="10%">编号</th>
        <th>标引用户</th>
        <th>审核用户</th>
        <th>添加时间/最后操作时间</th>
        <th>管理员</th>
        <th>状态</th>
        <th>审核次数</th>
        <th>替换试题</th>
        <th>说明</th>
        <th>操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
        <td><input type="checkbox" class="key" value="<?php echo ($node["WorkID"]); ?>"></td>
        <td><a href="#" class="btedit" thisid="<?php echo ($node["WorkID"]); ?>"><?php echo ($node["WorkID"]); ?></a></td>
        <td><?php echo ($node["UserName"]); if($node["RealName"] != ''): ?>(<?php echo ($node["RealName"]); ?>)<?php endif; ?></td>
        <td>
            <?php if($node["CheckUser"] != ""): ?>当前审核：<?php echo ($node["CheckUser"]); ?>
                <?php if($node["CheckReal"] != ''): ?>(<?php echo ($node["CheckReal"]); ?>)<?php endif; endif; ?>
            <?php if($node["oldChecker"] != ''): ?><br/>
                曾经审核：<?php if(is_array($node["oldChecker"])): $i = 0; $__LIST__ = $node["oldChecker"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; echo ($vo["UserName"]); ?>
                    <?php if($vo["RealName"] != ''): ?>(<?php echo ($vo["RealName"]); ?>)<?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
        </td>
        <td><?php echo (date("Y-m-d H:i:s",$node["AddTime"])); ?>/<br><?php echo (date("Y-m-d H:i:s",$node["LastTime"])); ?></td>
        <td><?php echo ($node["Admin"]); ?></td>
        <td><?php if($node["Status"] == 0): ?>未完成<?php endif; ?>
        <?php if(($node["Status"] == '1') and ($node["CheckStatus"] == '')): ?><font color="red">待审核</font><?php endif; ?>
        <?php if(($node["Status"] == '1') and ($node["CheckStatus"] == '0')): ?><font color="red">教师审核中</font><?php endif; ?>
        <?php if(($node["Status"] == '1') and ($node["CheckStatus"] == '1')): ?><font color="red">教师审核完成</font><?php endif; ?>
        <?php if($node["Status"] == 2): ?>已完成<?php endif; ?>
        <?php if($node["Status"] == 3): ?>重做<?php endif; ?>
        </td>
        <td><?php echo ($node["CheckTimes"]); ?></a></td>
        <td wid='<?php echo ($node["WorkID"]); ?>'>
            <?php if($node["HasReplace"] == 1): ?><a href='#' class='red notReplace' title='取消替换权限'>可以替换</a>
                <a href='#' class='canReplace' title='可以替换试题' style='display:none;'>不能替换</a>
            <?php else: ?>
                <a href='#' class='red notReplace' title='取消替换权限' style='display:none;'>可以替换</a>
                <a href='#' class='canReplace' title='可以替换试题'>不能替换</a><?php endif; ?>
        </td>
        <td><?php echo ($node["Content"]); ?></a></td>
        <td>
        <?php if($node["UserName"] == ''): ?><a href="#" class="btedit" thisid="<?php echo ($node["WorkID"]); ?>">分配标引任务</a>&nbsp;<?php endif; ?>
        <?php if(($node["Status"] == '1') and ($node["CheckStatus"] == '')): ?><a href="<?php echo U('Teacher/TaskB/taskcheckadd',array('id'=>$node[WorkID]));?>">分配审核任务</a>&nbsp;<?php endif; ?>
        <?php if(($node["Status"] == '1') and ($node["CheckStatus"] == '0')): ?><a href="<?php echo U('Teacher/TaskB/taskcheckadd',array('id'=>$node[WorkID]));?>">重新分配审核任务</a>&nbsp;<?php endif; ?>
        <a href="<?php echo U('Teacher/TaskB/checkwork',array('id'=>$node[WorkID]));?>">查看进度</a>
        </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="11" class="bottomTd"></td></tr>
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
$('#subject').change(function(){
        if($(this).val()!=''){
            $.get(U('test/getdata?s='+$(this).val()+'&l=k'),function(msg){
                //权限验证
                if(checkPower(msg)=='error'){
                    return false;
                }
                var data=msg['data'];
                $('#knowledge').html('<option value="">请选择</option>'+data);
            });
        }else{
            $('#knowledge').html('<option value="">请选择</option>');
        }
    });
    $('.knowledge').live('change',function(){
        $(this).nextAll(".knowledge").remove();
        var tt=$(this);
        if(tt.val()=='') return;
        $.get(U('test/getdata?s='+$('#subject').val()+"&l=k&pid="+tt.val()+'&'+Math.random()),function(msg){
            //权限验证
            if(checkPower(msg)=='error'){
                return false;
            }
            output='';
            var data=msg['data'];
            if(data[0]){
                output+='<option value="">'+data[2]+'</option>';
                for(datan in data[1]){
                    output+='<option value="'+data[1][datan]['KlID']+'">'+data[1][datan]['KlName']+'</option>';
                }
                tt.after('<select class="knowledge" '+data[3]+'>'+output+'</select>');
            }
        },'json');
    });
    
    $('.canReplace').click(function(){
        exchange('/Teacher/TaskB',$(this));
        return false;
    });
    $('.notReplace').click(function(){
        exchange('/Teacher/TaskB',$(this));
        return false;
    });
    
</script>
<!-- 主页面结束 -->

</body>
</html>