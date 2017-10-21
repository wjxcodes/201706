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
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content">
<!--  功能操作区域  -->
<div class="title"><?php echo ($pageName); ?> [ <A HREF="/User/User">返回列表</A> ]   [ <A HREF="javascript:history.go(-1);">返回上一页</A> ]</div>
<!-- 功能操作区域结束 -->
<div id="result" class="result none"></div>
<!-- 内容显示区域  -->
<FORM METHOD="POST" action="" id="form1" >
<TABLE cellpadding="5" cellspacing="0" class="list" border="1">
<tr><td height="5" colspan="7" class="topTd" ></td></tr>
<TR>
    <TD class="tRight" width="100">用户名：</TD>
    <TD class="tLeft" >
        <?php if(isset($edit["UserID"])): echo ($edit["UserName"]); ?>
        <?php else: ?>
        <INPUT TYPE="text" class="bLeft"  check='Require' warning="用户名名称不能为空" NAME="UserName" value=""> * 请输入手机或邮箱<?php endif; ?>
    </TD>
</TR>
<?php if(!isset($edit["UserID"])): ?><TR>
    <TD class="tRight" >密码：</TD>
    <TD class="tLeft" ><INPUT TYPE="password" class="bLeft" NAME="Password" value="" /></TD>
</TR>
<TR>
    <TD class="tRight" >重复密码：</TD>
    <TD class="tLeft" ><INPUT TYPE="password" class="bLeft"  NAME="Password2" value="" /></TD>
</TR><?php endif; ?>
<TR>
    <TD class="tRight tTop">真实姓名：</TD>
    <TD class="tLeft"><INPUT TYPE="text" class="large bLeft"  check='Require' warning="真实姓名不能为空" NAME="RealName" value="<?php echo ((isset($edit["RealName"]) && ($edit["RealName"] !== ""))?($edit["RealName"]):''); ?>"></TD>
</TR>
<?php if($edit["Nickname"] != ''): ?><TR>
    <TD class="tRight tTop">昵称：</TD>
    <TD class="tLeft"><?php echo ((isset($edit["Nickname"]) && ($edit["Nickname"] !== ""))?($edit["Nickname"]):''); ?></TD>
</TR><?php endif; ?>
<?php if($edit["UserPic"] != ''): ?><TR>
    <TD class="tRight tTop">头像：</TD>
    <TD class="tLeft"><img alt="" src="<?php echo ($edit["UserPic"]); ?>"></TD>
</TR><?php endif; ?>
<TR>
    <TD class="tRight">性别：</TD>
    <TD class="tLeft">
    <label><INPUT TYPE="radio" class="bLeft"  check='raido' warning="请选择性别" NAME="Sex" value="0" <?php if(($edit["Sex"] == '0') or ($edit["Sex"] == '')): ?>checked="checked"<?php endif; ?>> 男</label> 
    <label><INPUT TYPE="radio" class="bLeft" NAME="Sex" value="1" <?php if(($edit["Sex"]) == "1"): ?>checked="checked"<?php endif; ?>> 女</label>
    </TD>
</TR>
<TR>
    <TD class="tRight tTop">电话：</TD>
    <TD class="tLeft"><INPUT TYPE="text" class="large bLeft"  NAME="Phonecode" value="<?php echo ((isset($edit["Phonecode"]) && ($edit["Phonecode"] !== ""))?($edit["Phonecode"]):''); ?>"></TD>
</TR>
<TR>
    <TD class="tRight tTop">验证电话：</TD>
    <TD class="tLeft"><?php if($edit['CheckPhone'] == 1): ?>已验证<?php else: ?>未验证<?php endif; ?></TD>
</TR>
<TR>
    <TD class="tRight tTop">email：</TD>
    <TD class="tLeft"><INPUT TYPE="text" class="large bLeft"  NAME="Email" value="<?php echo ((isset($edit["Email"]) && ($edit["Email"] !== ""))?($edit["Email"]):''); ?>"></TD>
</TR>
<TR>
    <TD class="tRight tTop">验证email：</TD>
    <TD class="tLeft"><?php if($edit['CheckEmail'] == 1): ?>已验证<?php else: ?>未验证<?php endif; ?></TD>
</TR>
<TR>
    <TD class="tRight tTop">地址：</TD>
    <TD class="tLeft"><INPUT TYPE="text" class="large bLeft"  NAME="Address" value="<?php echo ((isset($edit["Address"]) && ($edit["Address"] !== ""))?($edit["Address"]):''); ?>"></TD>
</TR>
<TR>
    <TD class="tRight tTop">邮编：</TD>
    <TD class="tLeft"><INPUT TYPE="text" class="large bLeft"  NAME="PostCode" value="<?php echo ((isset($edit["PostCode"]) && ($edit["PostCode"] !== ""))?($edit["PostCode"]):''); ?>"></TD>
</TR>
<TR>
    <TD class="tRight tTop">用户身份：</TD>
    <TD class="tLeft">
        <label><INPUT TYPE="radio" class="bLeft" NAME="Whois" value="0" <?php if($edit["Whois"] == '0'): ?>checked="checked"<?php endif; ?>> 学生</label> 
        <label><INPUT TYPE="radio" class="bLeft" NAME="Whois" value="1" <?php if(($edit["Whois"]) == "1"): ?>checked="checked"<?php endif; ?>> 教师</label>
		<label><INPUT TYPE="radio" class="bLeft" NAME="Whois" value="3" <?php if(($edit["Whois"]) == "3"): ?>checked="checked"<?php endif; ?>> 校长</label>
    </TD>
</TR>
<?php if($edit["Whois"] == '0'): ?><TR>
    <TD class="tRight tTop">学号：</TD>
    <TD class="tLeft"><?php echo ((isset($edit["OrderNum"]) && ($edit["OrderNum"] !== ""))?($edit["OrderNum"]):''); ?></TD>
</TR>
<?php else: ?>
<TR>
    <TD class="tRight tTop">教师身份认证：</TD>
    <TD class="tLeft"><?php echo ($edit['authTitle'][$edit['IfAuth']]); ?></TD>
</TR><?php endif; ?>
<TR>
    <TD class="tRight" width="100">所属学科：</TD>
    <TD class="tLeft" ><SELECT id="subject" class="large bLeft" NAME="SubjectID">
    <option value="">请选择</option>
    <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label="<?php echo ($vo["SubjectName"]); ?>">
        <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $edit["SubjectStyle"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
        </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
    <?php else: ?>
        <option value="0">请添加学科</option><?php endif; ?>
    </SELECT></TD>
</TR>
<TR>
    <TD class="tRight" width="100">所属年级：</TD>
    <TD class="tLeft" >
        <select name="GradeID" class="GradeID" id="grade">
            <option value="">请先选择学科</option>
            <?php if(is_array($grade)): $i = 0; $__LIST__ = $grade;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$gvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($gvo["GradeID"]); ?>" <?php if(($gvo["GradeID"]) == $edit["GradeID"]): ?>selected="selected"<?php endif; ?>><?php echo ($gvo["GradeName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
    </TD>
</TR>
<TR>
    <TD class="tRight tTop">版本：</TD>
    <TD class="tLeft">
    <?php if($edit['Version'] == 1): ?>高考版
    <?php elseif($edit['Version'] == 2): ?> 同步版<?php endif; ?>
    </TD>
</TR>
<?php if($edit == ''): if(is_array($userPower)): $num = 0; $__LIST__ = $userPower;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$userPowerTmp): $mod = ($num % 2 );++$num;?><TR>
    <TD class="tRight tTop"><?php echo ($powerName[$num]); ?>：</TD>
    <TD class="tLeft">
        <?php if($num != 3): if(is_array($userPowerTmp)): $i = 0; $__LIST__ = $userPowerTmp;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vi): $mod = ($i % 2 );++$i;?><label><INPUT TYPE="radio" class="bLeft" name="groupname_<?php echo ($num); ?>" value="<?php echo ($vi["PUID"]); ?>" <?php if(in_array(($vi["PUID"]), is_array($default)?$default:explode(',',$default))): ?>checked="checked"<?php endif; ?>><?php echo ($vi["UserGroup"]); ?>&nbsp&nbsp</label><?php endforeach; endif; else: echo "" ;endif; ?>
        <?php else: ?>
        <?php if(is_array($userPowerTmp)): $i = 0; $__LIST__ = $userPowerTmp;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vi): $mod = ($i % 2 );++$i;?><label><INPUT TYPE="checkbox" class="bLeft" name="groupname_<?php echo ($num); ?>[]" value="<?php echo ($vi["PUID"]); ?>" <?php if(in_array(($vi["PUID"]), is_array($default)?$default:explode(',',$default))): ?>checked="checked"<?php endif; ?>><?php echo ($vi["UserGroup"]); ?>&nbsp&nbsp</label><?php endforeach; endif; else: echo "" ;endif; endif; ?>
    </TD>
</TR><?php endforeach; endif; else: echo "" ;endif; ?>
<TR>
    <TD class="tRight">到期日期：</TD>
    <TD class="tLeft"><INPUT TYPE="text" class="bLeft inputDate" NAME="EndTime" value=""  check="Require" warning="请填写日期" /></TD>
</TR><?php endif; ?>
<?php if($customGroup): ?><TR>
    <TD class="tRight tTop">自定义分组：</TD>
    <TD class="tLeft">    
        <?php if(is_array($customGroup)): $i = 0; $__LIST__ = $customGroup;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vi): $mod = ($i % 2 );++$i;?><label><INPUT TYPE="radio" class="bLeft" name="customGroup" value="<?php echo ($vi["GroupID"]); ?>" <?php if(in_array(($vi["GroupID"]), is_array($edit["CustomGroup"])?$edit["CustomGroup"]:explode(',',$edit["CustomGroup"]))): ?>checked="checked"<?php endif; ?>><?php echo ($vi["GroupName"]); ?>&nbsp&nbsp</label><?php endforeach; endif; else: echo "" ;endif; ?>
    </TD>
</TR><?php endif; ?>
<?php if($edit): ?><TR>
    <TD class="tRight tTop">注册时间：</TD>
    <TD class="tLeft"><?php echo (date("Y-m-d H:i:s",$edit["LoadDate"])); ?></TD>
</TR>
<TR>
    <TD class="tRight tTop">安全码：</TD>
    <TD class="tLeft"><?php echo ($edit["SaveCode"]); ?></TD>
</TR>
<TR>
    <TD class="tRight tTop">下载次数：</TD>
    <TD class="tLeft"><?php echo ($edit["Times"]); ?></TD>
</TR>
<TR>
    <TD class="tRight tTop">组卷次数：</TD>
    <TD class="tLeft"><?php echo ($edit["ComTimes"]); ?></TD>
</TR>
<TR>
    <TD class="tRight tTop">登录次数：</TD>
    <TD class="tLeft"><?php echo ($edit["Logins"]); ?></TD>
</TR>
<TR>
    <TD class="tRight tTop">最后一次登录：</TD>
    <TD class="tLeft"><?php echo (date("Y-m-d H:i:s",$edit["LastTime"])); ?></TD>
</TR>
<TR>
    <TD class="tRight tTop">最后一次登录ip：</TD>
    <TD class="tLeft"><?php echo ($edit["LastIP"]); ?></TD>
</TR>
<TR>
    <TD class="tRight tTop">所在地区：</TD>
    <TD class="tLeft"><?php echo ($edit["AreaName"]); ?></TD>
</TR>
<TR>
    <TD class="tRight tTop">所在学校：</TD>
    <TD class="tLeft"><?php echo ($edit["SchoolName"]); ?></TD>
</TR>
<TR>
    <TD class="tRight tTop">当前点数：</TD>
    <TD class="tLeft"><?php echo ($edit["Points"]); ?></TD>
</TR>
<TR>
    <TD class="tRight tTop">金币：</TD>
    <TD class="tLeft"><?php echo ($edit["Cz"]); ?></TD>
</TR>
<TR>
    <TD class="tRight tTop">经验值：</TD>
    <TD class="tLeft"><?php echo ($edit["ExpNum"]); ?></TD>
</TR>
<TR>
    <TD class="tRight tTop">当前状态：</TD>
    <TD class="tLeft"><?php if($edit.Status==1): ?>锁定<?php else: ?>正常<?php endif; ?></TD>
</TR>
    <TR>
        <TD class="tRight tTop">当前用户组：</TD>
        <TD class="tLeft"><?php echo ($edit["UserGroup"]); ?></TD>
    </TR>
<TR>
    <TD class="tRight tTop">包月到期时间：</TD>
    <TD class="tLeft"><?php if($edit["EndTime"] == 0): ?>未使用包月<?php else: echo (date("Y-m-d H:i:s",$edit["EndTime"])); endif; ?></TD>
</TR><?php endif; ?>
<TR>
    <TD ></TD>
    <TD class="center"><div style="width:85%;margin:5px">
        <INPUT TYPE="hidden" name="UserID" value="<?php echo ($edit["UserID"]); ?>">
        <INPUT TYPE="hidden" name="act" value="<?php echo ($act); ?>">
    <div class="impBtn fLeft"><INPUT tag='form1' u="<?php echo U('User/save');?>" TYPE="button" value="保存" class="save imgButton mysubmit"></div>
    <div class="impBtn fLeft m-l10"><INPUT TYPE="reset" class="reset imgButton" value="清空" ></div>
    </div></TD>
</TR>
<tr><td height="5" colspan="7" class="bottomTd" ></td></tr>
</TABLE>
</FORM>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<script>
$('#subject').subjectSelectChange('/User/User',{'style':'getMoreData','list':'grade'});
</script>
<!-- 主页面结束 -->

</body>
</html>