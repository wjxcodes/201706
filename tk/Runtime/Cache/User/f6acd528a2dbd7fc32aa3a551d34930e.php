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
var URL = '/User/User';
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
<style>
    input[value="批量上传"] {
        width:100px;
    }
</style>
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content" >
<div class="title"><?php echo ($pageName); ?></div>
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="新增" onclick="" class="btadd add imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="编辑" onclick="" class="btedit edit imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="uploads" value="批量上传" onclick="" class="btuploads uploads imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="output" value="导出" onclick="" class="btexport output imgButton"></div>

    <!-- 查询区域 -->
    <form method="post" action="/User/User" id="form1">
    <div class="fRig">
        <div class="fLeft">
            <span id="key">
                <input id="name" type="text" name="name" value="<?php echo ($_REQUEST['name']); ?>" title="用户名查询" class="medium" >
            </span>
        </div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <table border="0" cellpadding="1" cellspacing="3" width="100%">
        <tr>
            <td class="tRight" width="60">用户ID：</td>
            <td>
                <input type="text" name="UserID" class="small" value="<?php echo ($_REQUEST['UserID']); ?>" >
            </td>
            <td class="tRight" width="60">用户名：</td>
            <td>
                <input type="text" name="UserName" class="small" value="<?php echo ($_REQUEST['UserName']); ?>" >
            </td>
            <td class="tRight" width="60">IP：</td>
            <td>
                <input type="text" name="IP" class="small" value="<?php echo ($_REQUEST['IP']); ?>" >
            </td>
            <td class="tRight" width="50">所属分组：</td>
            <td>
                <select class="normal bLeft" id='groupID' name="groupID">
                    <option value="">请选择分组</option>
                    <?php if($powerList): if(is_array($powerList)): $i = 0; $__LIST__ = $powerList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label="<?php echo ($vo["GroupName"]); ?>">
                            <?php if($vo['groupList']): if(is_array($vo['groupList'])): $i = 0; $__LIST__ = $vo['groupList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["PUID"]); ?>" <?php if(($item["PUID"]) == $_REQUEST['groupID']): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["UserGroup"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                            </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
                    <?php else: ?>
                        <option value="">请选择分组</option><?php endif; ?>
                </select>
            </td>
            <td class="tRight" width="50">学科：</td>
            <td>
                <select class="normal bLeft" id='subject' name="SubjectID">
                    <option value="">请选择学科</option>
                    <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label="<?php echo ($vo["SubjectName"]); ?>">
                            <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $_REQUEST['SubjectID']): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                            </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
                    <?php else: ?>
                        <option value="">请添加学科</option><?php endif; ?>
                </select>
            </td>
            <td class="tRight" width="80">真实姓名：</td>
            <td >
                <input type="text" name="RealName" class="small" value="<?php echo ($_REQUEST['RealName']); ?>">
            </td>
        </TR>
        <TR>
            <td class="tRight" width="60">性别：</td>
            <td>
                <select class="medium bLeft" name="Sex">
                    <option value="">选择</option>
                    <option value="0" <?php if(($_REQUEST['Sex']) == "0"): ?>selected="selected"<?php endif; ?>>男</option>
                    <option value="1" <?php if(($_REQUEST['Sex']) == "1"): ?>selected="selected"<?php endif; ?>>女</option>
                </select>
            </td>
            <td class="tRight" width="50">状态：</td>
            <td>
                <select class="small bLeft" name="Status">
                    <option value="">选择</option>
                    <option value="0" <?php if(($_REQUEST['Status']) == "0"): ?>selected="selected"<?php endif; ?>>正常</option>
                    <option value="1" <?php if(($_REQUEST['Status']) == "1"): ?>selected="selected"<?php endif; ?>>锁定</option>
                </select>
            </td>
            <td class="tRight" width="50">身份：</td>
            <td>
                <select class="small bLeft" name="Whois">
                    <option value="">全部</option>
                    <option value="0" <?php if(($_REQUEST['Whois']) == "0"): ?>selected="selected"<?php endif; ?>>学生</option>
                    <option value="1" <?php if(($_REQUEST['Whois']) == "1"): ?>selected="selected"<?php endif; ?>>教师</option>
                </select>
            </td>
            <td class="tRight" width="60">地区：</td>
            <td>
                <select id="sf" class="selectArea">
                    <option value="">请选择省份</option>
                    <?php if(is_array($arrArea)): $i = 0; $__LIST__ = $arrArea;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><option value="<?php echo ($sub["AreaID"]); ?>" last="<?php echo ($sub["Last"]); ?>"><?php echo ($sub["AreaName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </td>
            <td class="tRight" >学校：</td>
            <td class="tLeft" >
                <select class="medium bLeft" name="SchoolID" id='school' value='school'>
                    <option value="">——请选择上级——</option>
                </select>
            </td>
            <td class="tRight" width="45">状态：</td>
            <td><select class="medium bLeft" name="Status">
                    <option value="">全部</option>
                    <option value="0">正常</option>
                    <option value="1">锁定</option>
                </select></td>
        </tr>
        </table>
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
    <tr class="row tCenter" >
        <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th width="8%">编号</th>
        <th width="12%">用户</th>
        <th width="8%">昵称</th>
        <th>性别</th>
        <th>用户信息</th>
        <th>所属学科</th>
        <th width="20%">所属组</th>
        <th>权限截止时间</th>
        <th width="5%">点值</th>
        <th width="5%">状态</th>
        <th>登录状态</th>
        <th>操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists tCenter" jl=''>
        <td><input type="checkbox" class="key" value="<?php echo ($node["UserID"]); ?>"></td>
        <td><?php echo ($node["UserID"]); ?>( <?php echo ($node["OrderNum"]); ?> )</td>
        <td>
        <a href="#" class="btedit" thisid="<?php echo ($node["UserID"]); ?>"><?php echo ($node["UserName"]); ?>( <?php echo ($node["RealName"]); ?> ) 【<?php if(($node["Whois"]) == "0"): ?>学生<?php else: ?>教师<?php endif; ?>】</a></td>
            <td><?php if(($node["Nickname"]) == ""): ?>无<?php else: echo ($node["Nickname"]); endif; ?></td>
        <td><?php if(($node["Sex"]) == "0"): ?>男<?php else: ?>女<?php endif; ?></td>
        <td style='text-align:left'>
            电话：<?php echo ($node["Phonecode"]); ?><br/>
            邮箱：<?php echo ($node["Email"]); ?><br/>
            地址：<?php echo ($node["Address"]); ?><br/>
        </td>
        <td><?php if(($node["SubjectName"]) == ""): ?><span style="color:red">未设置</span><?php else: echo ($node["SubjectName"]); endif; ?></td>
        <td align="left">
        <?php echo ($node["UserGroup"]); ?>
        自定义分组：<?php if(($node["CustomGroup"]) == "0"): ?>未设置<?php else: echo ($node["GroupName"]); endif; ?>
        </td>
        <td><?php if(($node["EndTime"]) == "0"): ?>未设置<?php else: echo (date("Y-m-d",$node["EndTime"])); endif; ?></td>
        <td><?php echo ($node["Cz"]); ?></td>
        <td wid="<?php echo ($node["UserID"]); ?>"><?php if(($node["Status"]) == "0"): ?><a style="cursor:pointer" status="0" class="lock">正常</a>
            <a style="cursor:pointer;display:none;color:red" status="1" class="lock">锁定</a>
            <?php else: ?>
            <a style="cursor:pointer;display:none" status="0" class="lock">正常</a>
            <a style="cursor:pointer;color:red" status="1" class="lock">锁定</a><?php endif; ?></td>
        <td style="text-align:left">
            注册时间:<?php echo (date("Y-m-d H:i:s",$node["LoadDate"])); ?><br/>
            登录IP:<?php echo ($node["LastIP"]); ?><br/>
            最后时间:<?php if(($node["LastTime"]) == "0"): ?>未登录<?php else: echo (date("Y-m-d H:i:s",$node["LastTime"])); endif; ?>
        </td>
        <td>
            <a href="#" class="btedit" thisid="<?php echo ($node["UserID"]); ?>">编辑</a>&nbsp;
            <a href="<?php echo U('User/password',array('UserID'=>$node['UserID']));?>">密码</a>&nbsp;
            <a href="<?php echo U('User/month',array('UserID'=>$node['UserID']));?>">包月</a>&nbsp;
            <a href="<?php echo U('User/point',array('UserID'=>$node['UserID']));?>">点数</a>&nbsp;
            <a href="<?php echo U('UserCustomGroup/editMsg',array('UserID'=>$node['UserID']));?>">自定义分组</a>&nbsp;
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
<script>
$(document).ready(function(){
    var areaParent="<?php echo ($areaParent); ?>";
    $('.selectArea').areaSelectChange("/User/User",1);
    if("<?php echo ($act); ?>"=="edit"){
        $('#sf').areaSelectLoad('/User/User',areaParent);
    }
});
    $('.btuploads').click(function(){
        location.href  = U(URL+"/uploads");
    })
    $('.lock').live('click',function(){
        exchange('/User/User',$(this));
    })
</script>
<!-- 主页面结束 -->

</body>
</html>