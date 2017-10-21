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
var URL = '/User/UserDynamic';
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
<div class="title"><?php echo ($pageName); ?></div>
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
    
    <!-- 查询区域 -->
    <FORM METHOD="POST" ACTION="/User/UserDynamic">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="name" placeholder="请输入模板名称" value="<?php echo ($_REQUEST['name']); ?>" title="模板查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
        <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR>
            <TD class="tRight" width="80">用户名：</TD>
            <TD width="80"><INPUT TYPE="text" NAME="UserName" class="small" value="<?php echo ($_REQUEST['UserName']); ?>"></TD>
            <TD class="tRight" width="80">模板名称：</TD>
            <TD width="80"><INPUT TYPE="text" NAME="TempName" class="small" value="<?php echo ($_REQUEST['TempName']); ?>"></TD>
            <td class="tRight" width="50">学科：</td>
            <td>
                <select class="normal bLeft" id='subject' name="SubjectID">
                    <option value="0">请选择学科</option>
                    <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label="<?php echo ($vo["SubjectName"]); ?>">
                            <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $_REQUEST['SubjectID']): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                            </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
                    <?php else: ?>
                        <option value="0">请添加学科</option><?php endif; ?>
                </select>
            </td>
            <td class="tRight" width="75">文档属性：</td>
            <td>
                <select class="normal bLeft" id='docFrom' name="docFrom">
                    <option value="0">选择属性</option>
                    <?php if(is_array($docFrom)): $i = 0; $__LIST__ = $docFrom;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($_REQUEST['docFrom']) == $key): ?>selected="selected"<?php endif; ?>><?php echo ($node); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </td>
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
    <tr><td height="5" colspan="13" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th width="5%">编号</th>
        <th>用户名</th>
        <th>文档名称</th>
        <th>学科</th>
        <th>文档属性</th>
        <th>审核状态</th>
        <th>是否分享</th>
        <th>时间</th>
        <th>操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
        <td><input type="checkbox" class="key" value="<?php echo ($node["UDID"]); ?>"></td>
        <td><?php echo ($node["AssociateID"]); ?></td>
        <td><?php echo ($node["UserName"]); ?></td>
        <td><?php echo ($node["Title"]); ?></td>
        <td><?php echo ($node["SubjectName"]); ?></td>
        <td><?php echo ($node["docFrom"]); ?></td>
        <td><?php if(($node["CheckStatus"]) == "0"): ?><font color="red">未通过审核</font><?php else: ?>审核通过<?php endif; ?></td>
        <td><?php if(($node["IfShare"]) == "0"): ?><font color="red">不分享</font><?php else: ?>分享<?php endif; ?></td>
        <td><?php echo (date("Y-m-d H:i:s",date($node["AddTime"]))); ?></td>
        <td>
            <a href="<?php echo U('Doc/DocDown/showMsg',array('id'=>$node[AssociateID]));?>" class="showmsg" thisid="<?php echo ($node["AssociateID"]); ?>">查看详情</a>&nbsp;
            <?php if(($node["CheckStatus"]) == "0"): ?><a href="<?php echo U('User/UserDynamic/check',array('id'=>$node[UDID],'checkStatus'=>1));?>" thisid="<?php echo ($node["UDID"]); ?>">审核通过</a><?php else: ?><a href="<?php echo U('User/UserDynamic/check',array('id'=>$node[UDID],'checkStatus'=>0));?>" thisid="<?php echo ($node["UDID"]); ?>">审核不通过</a><?php endif; ?>&nbsp;
        </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="13" class="bottomTd"></td></tr>
</table>
<!-- Think 系统列表组件结束 -->
</div>
<!--  分页显示区域 -->
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->
<script>
    $('.system').click(function(){
        exchange('/User/UserDynamic',$(this));
    })
    $('.check').click(function(){
        var stop=0;
        if($('input[class="key"]:checked').length<1){
            alert('请选择操作项');
            return false;
        }
        $('input[class="key"]:checked').each(function(){
            if($(this).parent().parent().find('.status').find('a:visible').attr('status')==1){
                alert('您选择的数据中存在系统模板，请重试！');
                stop=1;
                return false;
            }
            if($(this).parent().parent().find('.status').find('a:visible').length<1){
                alert('作业模板不能修改为系统模板！');
                stop=1;
                return false;
            }
        })
        if(stop==1){
            return false;
        }
        valueChanges('/User/UserDynamic',$('table'));
    })
</script>

</body>
</html>