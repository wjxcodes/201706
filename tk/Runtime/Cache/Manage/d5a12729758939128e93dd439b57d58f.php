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
var URL = '/Manage/PowerUser';
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
        <div class="title"><?php echo ($pageName); ?> [ <A HREF="/Manage/PowerUser">返回列表</A> ]</div>
        <!-- 功能操作区域结束 -->
        <div id="result" class="result none"></div>
        <!-- 内容显示区域  -->
        <FORM METHOD="POST" action="" id="form1" >
            <TABLE cellpadding=5 cellspacing=0  class="list" border="1">
                <tr><td height="5" colspan="7" class="topTd" ></td></tr>
                <TR>
                    <TD class="tRight" width="100">用户组名称：</TD>
                    <TD class="tLeft" ><INPUT TYPE="text" class="large bLeft"  check='Require' warning="标题不能为空" NAME="UserGroup" value="<?php echo ((isset($edit["UserGroup"]) && ($edit["UserGroup"] !== ""))?($edit["UserGroup"]):''); ?>"></TD>
                </TR>
                <TR>
                    <TD class="tRight" >是否默认组：</TD>
                    <TD class="tLeft" ><label><INPUT TYPE="radio" class="bLeft"  check='raido' warning="请选择默认组" NAME="IfDefault" value="1" <?php if(($edit["IfDefault"]) == "1"): ?>checked="checked"<?php endif; ?>> 是</label>  <label><INPUT TYPE="radio" class="bLeft" NAME="IfDefault" value="0" <?php if(($edit["IfDefault"] == 0) or ($edit["IfDefault"] == '')): ?>checked="checked"<?php endif; ?>> 否</label> </TD>
                </TR>
                <TR>
                    <TD class="tRight">排序编号</TD>
                    <TD class="tLeft">
                        <INPUT TYPE="text" class="large bLeft" NAME="OrderID" value="<?php echo ($edit["OrderID"]); ?>">&nbsp;不为空时，值为1-98之间
                    </TD>
                </TR>
                <TR>
                    <TD class="tRight">用户权限所属组：</TD>
                    <TD class="tLeft">
                        <?php if(is_array($userGroup)): $i = 0; $__LIST__ = $userGroup;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><label><INPUT TYPE="radio" class="bLeft group" NAME="GroupName" value="<?php echo ($vo["GroupName"]); ?>" <?php if(($edit["GroupName"]) == $vo["GroupName"]): ?>checked="checked"<?php endif; ?>><?php echo ($vo["UserGroupName"]); ?>&nbsp&nbsp</label><?php endforeach; endif; else: echo "" ;endif; ?>
                    </TD>
                </TR>
                <TR class="teacherGroup">
                    <TD class="tRight">所属教师组：</TD>
                    <TD class="tLeft">
                        <?php if(is_array($teacherArr)): $i = 0; $__LIST__ = $teacherArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><label><INPUT TYPE="radio" class="bLeft" NAME="PowerUser" value="<?php echo ($vo["GroupNum"]); ?>" <?php if(($edit["PowerUser"]) == $vo["GroupNum"]): ?>checked="checked"<?php endif; ?>><?php echo ($vo["TeacherGroup"]); ?>&nbsp&nbsp</label><?php endforeach; endif; else: echo "" ;endif; ?>
                    </TD>
                </TR>

                <TR>
                    <TD class="tRight" >是否开放购买：</TD>
                    <TD class="tLeft" ><label><INPUT TYPE="radio" class="bLeft"  check='raido' warning="" NAME="OpenBuy" value="1" <?php if(($edit["OpenBuy"]) == "1"): ?>checked="checked"<?php endif; ?>> 是</label>  <label><INPUT TYPE="radio" class="bLeft" NAME="OpenBuy" value="0" <?php if(($edit["OpenBuy"] == 0) or ($edit["OpenBuy"] == '')): ?>checked="checked"<?php endif; ?>> 否</label> </TD>
                </TR>

                <TR>
                    <TD class="tRight">权限单价/月</TD>
                    <TD class="tLeft">
                        <INPUT TYPE="text" class="large bLeft" NAME="Price" value="<?php echo ($edit["Price"]); ?>">&nbsp;值为0.00-99999.99之间
                    </TD>
                </TR>

                <TR>
                    <TD class="tRight" width="100">用户组权限说明：</TD>
                    <TD class="tLeft" >权限名称后面紧跟的数字和字母为该权限的限制值,0为该用户组没有此权限,all为不限,noOneYear为近一年不能用,
                        其他则为具体可操作次数,括号里为权限所对应的权限代码</TD>
                </TR>
                <TR>
                    <TD class="tRight">用户组权限：</TD>
                    <TD class="tLeft userPower">
                        <?php if(is_array($powerArray)): $i = 0; $__LIST__ = $powerArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$powerArraytmp): $mod = ($i % 2 );++$i;?><span class="group<?php echo ($key); ?>">
                        <?php if(is_array($powerArraytmp)): $i = 0; $__LIST__ = $powerArraytmp;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pnode): $mod = ($i % 2 );++$i; if(is_array($pnode)): $i = 0; $__LIST__ = $pnode;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i; if($node["ListID"] == 1): ?><div style="display:inline-block;width:400px;line-height:25px;"><label style="white-space:nowrap;"><INPUT name="ListID[]" id="AllPower" type="checkbox" value="<?php echo ($node["ListID"]); ?>" <?php if(in_array(($node["ListID"]), is_array($edit["ListID"])?$edit["ListID"]:explode(',',$edit["ListID"]))): ?>checked=checked<?php endif; ?>  <?php if(($i) == "1"): ?>check='radio' warning="请选择权限"<?php endif; ?>/> <?php echo ($node["PowerName"]); ?>(<?php echo ($node["PowerTag"]); ?>) </label></div>
                        <?php else: ?>
                        <div style="display:inline-block;width:400px;line-height:25px;"><label style="white-space:nowrap;"><INPUT name="ListID[]" class="power" type="checkbox" value="<?php echo ($node["ListID"]); ?>" <?php if(in_array(($node["ListID"]), is_array($edit["ListID"])?$edit["ListID"]:explode(',',$edit["ListID"]))): ?>checked=checked<?php endif; ?>  <?php if(($i) == "1"): ?>check='radio' warning="请选择权限"<?php endif; ?>/> <?php echo ($node["PowerName"]); ?>(<?php echo ($node["PowerTag"]); ?>) </label></div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                        <hr style="border-style:dashed"><?php endforeach; endif; else: echo "" ;endif; ?>
                        </span><?php endforeach; endif; else: echo "" ;endif; ?>
                    </TD>
                </TR>
                <TR>
                    <TD ></TD>
                    <TD class="center"><div style="width:85%;margin:5px">
                        <INPUT TYPE="hidden" name="PUID" value="<?php echo ($edit["PUID"]); ?>">
                        <INPUT TYPE="hidden" name="act" value="<?php echo ($act); ?>">
                        <div class="impBtn fLeft"><INPUT tag='form1' u="<?php echo U('PowerUser/save');?>" TYPE="button" value="保存" class="save imgButton mysubmit"></div>
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
    $('.userGroup').hide();
    var groupName = $('input[name="GroupName"]:checked').val();
    $('.userPower').find("span").hide();
    $('#userGroup'+groupName).show();
    if(groupName=='3'){
        $('.teacherGroup').show();
    }else{
        $('.teacherGroup').hide();
    }
    $('.group'+groupName).show();
    if(groupName == undefined){
        $('input[name="GroupName"][value="1"]').attr('checked','checked');
        $('.group1').show();
    }
    $('.group').live('change',function(){
        var checked = $('input[name="GroupName"]:checked').val();
        if(checked == '3'){
            $('.teacherGroup').show();
        }else{
            $('.teacherGroup').hide();
        }
        $('.userPower').find("span").hide();
        $('.userGroup').hide();
        $('#userGroup'+checked).show();
        $('.group'+checked).show();
    })
</script>
<!-- 主页面结束 -->

</body>
</html>