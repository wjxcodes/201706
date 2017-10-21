<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<TITLE><?php echo (C("WLN_WEB_NAME")); ?>管理系统 V<?php echo (C("WLN_VERSION")); ?></TITLE>
<link rel="stylesheet" type="text/css" href='/Public/zjadmin/css/style.css' />
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>

<script type="text/javascript" src="/Public/zjadmin/js/common.js"></script>
 <SCRIPT LANGUAGE="JavaScript">
$(document).ready(function(){
    if('<?php echo ($openKeysoft); ?>'=='1'){
        login_onclick();
    }
    $('#verifyImg').click(function(){
        //重载验证码
        var timenow = new Date().getTime();
        $('#verifyImg').attr('src',$('#verifyImg').attr('url')+'?times='+timenow);
    });
    $('#verify').keydown(function(e){
        var e = e || event;
        if (e.keyCode==13)
        {
            $('#button1').click();
        }
    });
    $('#button1').click(function(){
        if($('#account').val()==''){
            $('#result').html($('#account').attr('warning'));
            $('#result').css({'display':'block'});
            return ;
        }
        if($('#password').val()==''){
            $('#result').html($('#password').attr('warning'));
            $('#result').css({'display':'block'});
            return ;
        }
        if($('#verify').val()==''){
            $('#result').html($('#verify').attr('warning'));
            $('#result').css({'display':'block'});
            return ;
        }
        $('#form1').attr('action','<?php echo U('Public/check');?>');
        $('#form1').submit();
    });
    $('#account').focus();
});
    if ( window != top){  window.top.location =window.location;}  

</SCRIPT>
<style>
    td,th,tr,table.login td{border:0px;}
    table.login td{line-height:30px;}
</style>
</HEAD>
<BODY>
<div style="display:none;"><embed id="s_simnew31"  type="application/npsyunew3-plugin" hidden="true"> </embed></div>
<FORM METHOD="POST" name="login" id="form1" >
<div class="tCenter hMargin">
<TABLE id="checkList" border="0" class="login shadow"  cellpadding=0 cellspacing=0 >

<TR class="row" ><Th colspan="2" class="tCenter space" style="background-color:#4b88e6;color:#fff;height:30px;"><img src="/Public/zjadmin/images/preview_f2.png" width="32" height="32" border="0" alt="" align="absmiddle"> <?php echo (C("WLN_WEB_NAME")); ?>管理系统 V<?php echo (C("WLN_VERSION")); ?></th></TR>

<TR class="row" ><TD colspan="2" class="tCenter"><div id="result" class="result none"></div></TD></TR>
<TR class="row" ><TD class="tRight" width="35%">帐 号：</TD><TD><INPUT id="account" TYPE="text" class="medium bLeftRequire" check="Require" warning="请输入帐号" NAME="account" value=""></TD></TR>
<TR class="row" ><TD class="tRight">密 码：</TD><TD><INPUT id="password" TYPE="password" class="medium bLeftRequire" check="Require" warning="请输入密码" NAME="password" value=""></TD></TR>
<TR class="row" ><TD class="tRight">验证码：</TD><TD><INPUT id="verify" TYPE="text" class="small bLeftRequire" check="Require" warning="请输入验证码" NAME="verify" > <IMG id="verifyImg" src="<?php echo U('Public/verify');?>" url="<?php echo U('Public/verify');?>" BORDER="0" ALT="点击刷新验证码" style="cursor:pointer" align="absmiddle"></TD></TR>
<TR class="row" ><TD class="tCenter" align="justify" colspan="2">
    <input name="EncData" TYPE="hidden" id="EncData" value=""   />
    <input name="KeyID" TYPE="hidden" id="KeyID" value=""   />
    <input name="KeyValue" TYPE="hidden" id="KeyValue" value=""   />
    <input name="KeyRnd" TYPE="hidden" id="KeyRnd" value="<?php echo ($keyRnd); ?>"   />
<INPUT TYPE="hidden" name="ajax" value="1">
<!-- <INPUT TYPE="reset" value="重 置" class="submit small"> -->
<INPUT id="button1" TYPE="button" value="登 录" class="submit medium hMargin" style="margin-top:10px;margin-bottom:15px;width:250px;background:#70a8ff;color:#fff;">
</td></tr>
</TABLE>
</div>
</FORM>
</BODY>
</HTML>