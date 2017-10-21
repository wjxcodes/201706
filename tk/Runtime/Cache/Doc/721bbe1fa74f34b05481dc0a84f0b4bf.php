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
var URL = '/Doc/DocFile';
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
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="新增" onclick="" class="btadd add imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="编辑" onclick="" class="btedit edit imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
    <!-- 查询区域 -->
    <FORM METHOD="POST" ACTION="/Doc/DocFile">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="name" value="<?php echo ($_REQUEST['name']); ?>" title="管理员查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
    <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
    <TR>
            <TD class="tRight" width="80">文档名称：</TD>
            <TD ><INPUT TYPE="text" NAME="docName" class="small" value="<?php echo ($_REQUEST['DocName']); ?>"></TD>
            <TD class="tRight" width="80">文档编号：</TD>
            <TD ><INPUT TYPE="text" NAME="fileID" class="small" value="<?php echo ($_REQUEST['FileID']); ?>"></TD>
            <TD class="tRight" width="80">管 理 员：</TD>
            <TD><INPUT TYPE="text" NAME="admin" class="small" value="<?php echo ($_REQUEST['Admin']); ?>" ></TD>
            <TD class="tRight" width="80">用户名：</TD>
            <TD><INPUT TYPE="text" NAME="userName" class="small" value="<?php echo ($_REQUEST['UserName']); ?>" ></TD>
            <TD class="tRight" width="80">修改状态：</TD>
            <TD><SELECT class="small bLeft" NAME="iFDown">
            <option value="">选择</option>
            <option value="0" <?php if(($_REQUEST['Status']) == ""): ?>selected="selected"<?php endif; ?>>正常</option>
            <option value="1" <?php if(($_REQUEST['Status']) == "1"): ?>selected="selected"<?php endif; ?>>不能修改</option>
            </SELECT></TD>
            <TD class="tRight" width="50">学科：</TD>
            <TD><SELECT class="normal bLeft" NAME="subjectID">
            <option value="0">请选择学科</option>
            <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["SubjectID"]); ?>"><?php echo ($vo["ParentName"]); echo ($vo["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php else: ?>
                <option value="0">请添加学科</option><?php endif; ?>
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
        <th width="50">编号</th>
        <th>文档名</th>
        <th>解析任务描述</th>
        <th>添加时间</th>
        <th>学科</th>
        <th>管理员</th>
        <th>任务人</th>
        <th>下载次数/上传次数</th>
        <th>最后下载时间</th>
        <th width='80'>操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
        <td><input type="checkbox" class="key" value="<?php echo ($node["FileID"]); ?>"></td>
        <td><?php echo ($node["FileID"]); ?></td>
        <td>
            <a href="#" class="btedit" thisid="<?php echo ($node["FileID"]); ?>"><?php echo ($node["DocName"]); ?></a><br/>
            【<?php if($node["CheckStatus"] == 1): ?><font color='red'>需审核</font>&nbsp;&nbsp;<a href="<?php echo U('Doc/DocFile/down',array('fid'=>$node['FileID']));?>" thisid="<?php echo ($node["FileID"]); ?>">下载</a>
            <?php else: ?>
            无需审核<?php endif; ?>】
        </td>
        <td><?php echo ($node['FileDescription']); ?></td>
        <td><?php echo (date("Y-m-d H:i:s",$node["AddTime"])); ?></td>
        <td><?php echo ($node["gradeInfo"]); ?></td>
        <td><?php echo ($node["Admin"]); ?></td>
        <td><?php echo ($node["UserName"]); if($node.RealName): ?>(<?php echo ($node["RealName"]); ?>)<?php endif; ?></td>
        <td><?php echo ($node["Points"]); ?>/<?php echo ($node["uploadTimes"]); ?></td>
        <td><?php if($node["LastLoad"] == 0): ?>暂无下载
            <?php else: ?>
            <?php echo (date("Y-m-d H:i:s",$node["LastLoad"])); endif; ?>
        </td>
        <td>
            <?php if($node["UserName"] == ''): ?><a href="#" class="btedit" thisid="<?php echo ($node["FileID"]); ?>">分配任务</a><br/><?php endif; ?><a href="#" class="btedit" thisid="<?php echo ($node["FileID"]); ?>">编辑</a><br/><a href="<?php echo U('Doc/DocFile/showMsg',array('id'=>$node['FileID']));?>" class="showmsg" thisid="<?php echo ($node["FileID"]); ?>">查看详情</a>
            <?php if($node["Points"] > 0 and $node["uploadTimes"] > 0 and $node["CheckStatus"] == 1): ?><br/><a href="<?php echo U('Doc/DocFile/check',array('status'=>2,'id'=>$node['FileID']));?>" class="pass">审核通过</a><?php endif; ?>
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
<!-- 主页面结束 -->

</body>
</html>