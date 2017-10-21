$(document).ready(function(){
    switch(mark){
        case 'register':
            $.checkRegister.init();
            break;
        case 'setPass':
            $.checkSetPass.init();
            break;
        case 'getPass':
            $.checkGetPass.init();
            break;
        case 'login':
            $.checkLogin.init();
            break;
    }
});
//注册空间类名方法，注册相关验证
jQuery.checkRegister={
    //初始化函数
    init:function(){
        this.invitFocusin();
        this.keySubmit();
        this.passWordBlur();
        this.passWordFocus();
        this.passWordSecondFocus();
        this.registerSubmit();
        this.resetForm();
        //this.userFocusinCheck();
        //this.userFocusoutCheck();
        this.registerBind();
        this.verifyImgCheck();
        this.InitCommenDivHeight();
        this.resizeBind();
        this.getServiceTerm();
        this.setCheckbox();
        this.registerHelp();
    },
    //重置框位置
    resizeBind:function(){
        $(window).bind("resize",function() {$.checkRegister.InitCommenDivHeight();});
    },
    //回车激活提交
    keySubmit:function(){
        $('#Password1').keydown(function(e){
            if(e.keyCode==13){
                $('#loginSubmit').click(); //处理事件
            }
        });
    },
    //重置form表单
    resetForm:function(){
        $('#reset').click(function(){
            $('#UserName').val('');
            $('#UserName').focus();
            $('#Password').val('');
            $('#Password').css({'display':'none'});
            $('#Passworda').css({'display':'block'});
            $('#Password1').val('');
            $('#Password1').css({'display':'none'});
            $('#Password1a').css({'display':'block'});
        });
    },
    //用户名验证
    userFocusinCheck:function(){
        $('#UserName').focusin(function(){
            if($('#UserName').val().indexOf('请填写邮箱或手机号')!=-1) $('#UserName').val('');
            else if($('#UserName').val()==''){
                $('#UserName').val('请填写邮箱或手机号');
            }
        });
    },
    //用户名验证
    userFocusoutCheck:function(){
        $('#UserName').focusout(function(){
            if($('#UserName').val()=='') $('#UserName').val('请填写邮箱或手机号');
            else if($('#UserName').val()==''){
                $('#UserName').val('请填写邮箱或手机号');
            }
        });
    },
    //密码验证
    passWordFocus:function(){
        $('#Passworda').focus(function(){
            $('#Passworda').css({'display':'none'});
            $('#Password').val('');
            $('#Password').css({'display':'block'});
            $('#Password').focus();
        });
    },
    //密码验证
    passWordBlur:function(){
        $('#Password').blur(function(){
            if($('#Password').val()==''){
                $('#Password').css({'display':'none'});
                $('#Passworda').css({'display':'block'});
            }
        });
    },
    //重新输入密码验证
    passWordSecondFocus:function(){
        $('#Password1a').focus(function(){
            $('#Password1a').css({'display':'none'});
            $('#Password1').val('');
            $('#Password1').css({'display':'block'});
            $('#Password1').focus();
        });
    },
    //重新输入密码验证
    passWordBlur:function(){
        $('#Password1').blur(function(){
            if($('#Password1').val()==''){
                $('#Password1').css({'display':'none'});
                $('#Password1a').css({'display':'block'});
            }
        });
    },
    //邀请码验证
    invitFocusin:function(){
        if(invit==1){
            $('#InvitName').focusin(function(){
                if($('#InvitName').val().indexOf('请填写邀请码')!=-1) $('#InvitName').val('');
                else if($('#InvitName').val()==''){
                    $('#InvitName').val('请填写邀请码');
                }
            });
            $('#InvitName').focusout(function(){
                if($('#InvitName').val()=='') $('#InvitName').val('请填写邀请码');
                else if($('#InvitName').val()==''){
                    $('#InvitName').val('请填写邀请码');
                }
            });
        }
    },
    //注册提交
    registerSubmit:function(){
        $('#zcsubmit').click(function(){
            var phoneNum=$('#UserName').val();
            var passWord=$('#Password').val();
            var passWord1=$('#Password1').val();
            var phoneCode=$('#phoneCode').val();
            if(phoneNum=='' || phoneNum=='请填写手机号'){
                alert('请填写手机号！');
                return false;
            }
            if(phoneNum.length!=11 ){
                alert('请填写正确的手机号！');
                return false;
            }
            if(passWord==''){
                alert('请填写密码！');
                return false;
            }
            if(passWord!=passWord1){
                alert('两次密码输入不一致！');
                return false;
            }
            if(passWord.length<6 || passWord.length>18 ){
                alert('请输入6-18位密码！');
                return false;
            }
            if(phoneCode==''){
                alert('请输入短信验证码！');
                return false;
            }
            if(phoneCode.length!=6 || !/^\d+$/.test(phoneCode)){
                alert('请输入正确的短信验证码！');
                return false;
            }
            if(!$('#checkboxAgree').is(":checked")){
                alert('请接受服务条款');
                return false;
            }
            /*if(invit==1){
                if($('#InvitName').val()=='' || $('#InvitName').val()=='请填写邀请码'){
                    alert('请填写邀请码！');
                    return false;
                }
                data+='&InvitName='+$('#InvitName').val();
            }*/
            $.post(U('Index/register'),{'UserName':phoneNum,'Password':passWord,'Password1':passWord1,'phoneCode':phoneCode}, function(msg){
                if(msg['data']=='success'){
                    $('#loginform').css('display','none');
                    $('#loginMsg').html('<font color="#27ae61"><span style=" float:left; margin-top:1px;margin-right:10px; margin-left:40px"><img src="/Public/default/image/loading.gif"/></span><strong>注册成功！</strong></font>正在跳转...');
                    $('#loginMsg').css('display','block');
                    alert('注册成功！');
                    location.href=U('Index/index');
                }else{
                    var msgData = msg['data'].split('|');
                    alert(msgData[0]);
                    $('#verifyImg').attr('src',U('Index/verify'));
                }
            });

        });
    },
    //验证码切换
    verifyImgCheck:function(){
        $('#verifyImg').click(function(){
            var timenow = new Date().getTime();
            $('#verifyImg').attr('src',U('Index/verify?times='+timenow));
        });
    },
    //重置弹出框位置
    InitCommenDivHeight:function() {
        var mainwidth=$(window).width();
        var mainheight=$(window).height() - 2 - $('.header').height();
        var height = mainheight < 550 ? 550 : mainheight;
        var width=mainwidth < 960 ? 960 : mainwidth;
        var topwidth=(mainwidth > 960 ? 960 : mainwidth) < 600 ? 600 : (mainwidth > 960 ? 960 : mainwidth);
        $(".main").css({ 'height': height ,'width':width});
        $('.top').css({'width':topwidth});
        $('.header').css({'width':width});
        $('.footer').css({'width':topwidth});
    },
    //设置服务条款checkbox
    setCheckbox:function(){
       $('#checkboxAgree').on('click',function(){
             if(!$(this).is(':checked')){
                 $(this).attr({'checked':false});
             }else{
                 $(this).attr({'checked':'checked'});
             }
       });
    },
    //服务条款弹出框
    getServiceTerm:function(){
        $('.serviceTerm').on('click',function(){
            $.post(U('Index/getServiceTerm'),function(data){
                if(data['info']=='success'){
                    $.myDialog.normalMsgBox('serviceTerm', '题库用户注册协议', 850, data['data'],5);
                    $('#serviceTerm .normal_msg').css({'max-height':'none'});
                    $('.norBtn').on('click',function(){
                        $('#serviceTerm .tcClose').click();
                    });
                }else if(data['info']=='error'){
                    $.myDialog.normalMsgBox('serviceTerm', '题库用户注册协议', 850, '<p style="padding:15px 0;text-align: center;font-size: 16px;">服务条款获取失败,请稍后重试</p>',5);
                }
            });
        });
    },
    //注册说明弹出框
    registerHelp:function(){
        var tmpstr ='<div class="regHelp">'+
                    '<ul>'+
                    '<li><span>1.</span>如果贵校已经IP开通，请在学校IP地址范围内注册，享有集体用户权限。</li>'+
                    '<li><span>2.</span>如果贵校已经账号开通，请用您个人的账号密码直接<a href="Home">登录</a>即可，享有集体用户权限。</li>'+
                    '<li><span>3.</span>如果贵校未开通，请直接注册使用，享有个人用户对应的权限。</li>'+
                    '<li><span>4.</span>需要帮助请<a href="Index-Index-about">联系客服</a>。</li>'+
                    '</ul>'+
                    '</div>';
        $(".regIntro").on("click",function(){
            $.myDialog.normalMsgBox('regHelp','题库用户注册说明', 404,tmpstr,5);
        });
        $("#regHelp .tcClose").click(function(){
            $("#regHelp,#div_shadowregHelp").hide();
            $("#inputs .wbk01:first").focus();
        });

    },
    //发送短信验证码
    registerBind:function(){
        $(document).on('click','.getPhoneRand',function(){

            var phoneNum=$('#UserName').val();
            if(phoneNum==''){
                alert('请输入手机号！');
                return false;
            }
            if(!$.myCommon.checkPhoneNum(phoneNum)){
                alert('请输入正确的手机号！');
                return false;
            }
            var imgVerifyHtml='图片验证码：'+
                '<input id="verify" type="text" class="wbk01" size="4" maxlength="4" data-rule="require" data-display="验证码" name="verify" style="width:140px;margin:0;padding:0;" />'+
                '<img height="44" width="95" id="verifyImg" src="'+U('Index/verify')+'" border="0" title="点击刷新验证码" style="cursor:pointer;margin:0 0 0 30px;" align="absmiddle" />';
            $.myDialog.normalMsgBox('imgVerify', '请输入验证码', 450, imgVerifyHtml,4);
        });
        $(document).on('click','#imgVerify .normal_no',function(){
            var phoneNum=$('#UserName').val();
            var imgCode=$('#verify').val();
            if(imgCode==''){
                alert('请输入图片验证码！');
                return false;
            }
            if(imgCode.length!=4 || !/^\d+$/.test(imgCode)){
                alert('请输入正确的验证码！');
                return false;
            }
            $.myDialog.showMsg('正在发送...',0);
            $.myCommon.sendPhoneCode(phoneNum,imgCode);
        });
    }
}
//获取密码相关验证
jQuery.checkGetPass={
    //初始化函数
    init:function(){
        this.userFocusin();
        this.userFocusout();
        this.verifyFocusin();
        this.verifyFocusout();
        this.emailFocusin();
        this.emailFocusout();
        this.goBack();
        this.loginSubmit();
        this.verifyImg();
        this.InitCommenDivHeight();
        this.resizeBind();
    },
    //用户名验证
    userFocusin:function(){
        $('#UserName').focusin(function(){
            if($('#UserName').val().indexOf('请填写用户名')!=-1) $('#UserName').val('');
            else if($('#UserName').val()==''){
                $('#UserName').val('请填写用户名');
            }
        });
    },
    //用户名验证
    userFocusout:function(){
        $('#UserName').focusout(function(){
            if($('#UserName').val()=='') $('#UserName').val('请填写用户名');
            else if($('#UserName').val()==''){
                $('#UserName').val('请填写用户名');
            }
        });
    },
    //验证码验证
    verifyFocusin:function(){
        $('#verify').focusin(function(){
            if($('#verify').val().indexOf('请填写验证码')!=-1) $('#verify').val('');
            else if($('#verify').val()==''){
                $('#verify').val('请填写验证码');
            }
        });
    },
    //验证码验证
    verifyFocusout:function(){
        $('#verify').focusout(function(){
            if($('#verify').val()=='') $('#verify').val('请填写验证码');
            else if($('#verify').val()==''){
                $('#verify').val('请填写验证码');
            }
        });
    },
    //邮箱验证
    emailFocusin:function(){
        $('#Email').focusin(function(){
            if($('#Email').val().indexOf('请填写邮箱')!=-1) $('#Email').val('');
            else if($('#Email').val()==''){
                $('#Email').val('请填写邮箱');
            }
        });
    },
    //邮箱验证
    emailFocusout:function(){
        $('#Email').focusout(function(){
            if($('#Email').val()=='') $('#Email').val('请填写邮箱');
            else if($('#Email').val()==''){
                $('#Email').val('请填写邮箱');
            }
        });
    },
    //返回
    goBack:function(){
        $('#goback').click(function(){
            location.href=U('Index/index');
        });
    },
    //登陆验证
    loginSubmit:function(){
        $('#loginSubmit').click(function(){
            var UserName=$('#UserName').val();
            if(UserName=='' || UserName=='请填写用户名'){
                alert('请填写用户名');
                return false;
            }
            var Email=$('#Email').val();
            if(Email=='' || Email=='请填写邮箱'){
                alert('请填写邮箱');
                return false;
            }
            var verify=$('#verify').val();
            if(verify.length!=4){
                alert('请填写验证码');
                return false;
            }
            $.ajax({
                type: "POST",
                url: U('User/Index/getPassword'),
                data: "UserName="+UserName+"&Email="+Email+"&verify="+verify+"&times="+Math.random(),
                cache: false,
                success: function(msg){
                    if($.myCommon.backLogin(msg)==false){
                        $('#verifyImg').attr('src',U('Index/verify'));
                        return false;
                    }
                    alert("密码找回成功！请进入邮箱查看。");
                    $('#loginform').css('display','none');
                    $('#loginMsg').html('<font color="#27ae61"><span style=" float:left; margin-top:1px;margin-right:10px; margin-left:40px"><img src="/Public/default/image/loading.gif"/></span><strong>找回成功！</strong></font>正在跳转...');
                    $('#loginMsg').css('display','block');
                    location.href=U('Index/index');
                }
            });
        });
    },
    //验证码切换
    verifyImg:function(){
        $('#verifyImg').click(function(){
            var timenow = new Date().getTime();
            $('#verifyImg').attr('src',U('Index/verify?times='+timenow));
        });
    },
    //重置弹出框位置
    InitCommenDivHeight:function() {
        var mainwidth=$(window).width();
        var mainheight=$(window).height() - 2 - $('.header').height();
        var height = mainheight < 550 ? 550 : mainheight;
        var width=mainwidth < 960 ? 960 : mainwidth;
        var topwidth=(mainwidth > 960 ? 960 : mainwidth) < 600 ? 600 : (mainwidth > 960 ? 960 : mainwidth);
        $(".main").css({ 'height': height ,'width':width});
        $('.top').css({'width':topwidth});
        $('.header').css({'width':width});
        $('.footer').css({'width':topwidth});
    },
    //重置框位置及大小
    resizeBind:function(){
        $(window).bind("resize",function() {
            $.checkGetPass.InitCommenDivHeight();
        });
    }
}
//重新设置密码
jQuery.checkSetPass={
    //初始化函数
    init:function(){
        this.passWord();
        this.passWordBlur();
        this.passWordSecond();
        this.passWordSecondBlur();
        this.loginSubmit();
        this.resizeBind();
        this.InitCommenDivHeight();
    },
    //密码验证
    passWord:function(){
        $('#Passworda').focus(function(){
            $('#Passworda').css({'display':'none'});
            $('#Password').val('');
            $('#Password').css({'display':'block'});
            $('#Password').focus();
        });
    },
    //密码验证
    passWordBlur:function(){
        $('#Password').blur(function(){
            if($('#Password').val()==''){
                $('#Password').css({'display':'none'});
                $('#Passworda').css({'display':'block'});
            }
        });
    },
    //重新输入密码验证
    passWordSecond:function(){
        $('#Password1a').focus(function(){
            $('#Password1a').css({'display':'none'});
            $('#Password1').val('');
            $('#Password1').css({'display':'block'});
            $('#Password1').focus();
        });
    },
    //重新输入密码验证
    passWordSecondBlur:function() {
        $('#Password1').blur(function () {
            if ($('#Password1').val() == '') {
                $('#Password1').css({'display': 'none'});
                $('#Password1a').css({'display': 'block'});
            }
        });
    },
    //提交验证
    loginSubmit:function(){
        $('#loginSubmit').click(function(){
            if($('#Password').val()==''){
                alert('请填写密码！');
                return false;
            }
            if($('#Password').val().length<6 || $('#Password').val().length>18 ){
                alert('请输入6-18位密码！');
                return false;
            }
            if($('#Password').val()!=$('#Password1').val()){
                alert('两次填写的密码不一致！');
                return false;
            }

            $.ajax({
                type: "POST",
                url: U('Index/setPass'),
                data: "u="+u+"&k="+k+"&p="+$('#Password').val()+"&p2="+$('#Password1').val()+"&m="+Math.random(),
                cache: false,
                success: function(msg){
                    if(msg['data']=='success'){
                        alert("密码重置成功！请登录。");
                        $('#loginform').css('display','none');
                        $('#loginMsg').html('<font color="#27ae61"><span style=" float:left; margin-top:1px;margin-right:10px; margin-left:40px"><img src="/Public/default/image/loading.gif"/></span><strong>密码重置成功！</strong></font>正在跳转...');
                        $('#loginMsg').css('display','block');
                        location.href=U('Index/index');
                    }else{
                        alert(msg['data']);
                    }
                }
            });
        });
    },
    //重置div框的位置及大小
    resizeBind:function(){
        $(window).bind("resize",function() {
            $.checkGetPass.InitCommenDivHeight();
        });
    },
    //重置弹出框位置
    InitCommenDivHeight:function() {
        var mainwidth=$(window).width();
        var mainheight=$(window).height() - 2 - $('.header').height();
        var height = mainheight < 550 ? 550 : mainheight;
        var width=mainwidth < 960 ? 960 : mainwidth;
        var topwidth=(mainwidth > 960 ? 960 : mainwidth) < 600 ? 600 : (mainwidth > 960 ? 960 : mainwidth);
        $(".main").css({ 'height': height ,'width':width});
        $('.top').css({'width':topwidth});
        $('.header').css({'width':width});
        $('.footer').css({'width':topwidth});
    }
}
//登录相关验证
jQuery.checkLogin={
    //初始化函数
    init:function(){
        this.resizeBind();
        this.verifyReplace();
        this.passWordKeydown();
        this.zsSubmitClick();
        this.loginSubmitClick();
        this.verifyImg();
        this.divReady();
        this.InitDivHeight();
    },
    //重置div框的位置及大小
    resizeBind:function(){
        $(window).bind("resize",function() {$.checkLogin.InitDivHeight();});
    },
    //验证码切换
    verifyReplace:function(){
        var timenow = new Date().getTime();
        $('#verifyImg').attr('src',U('Index/verify?times='+timenow));
        $('#verifyImg').css({'cursor':'pointer'});
    },
    //回车激活提交
    passWordKeydown:function(){
        $('#Password').keydown(function(e){
            if(e.keyCode==13){
               $('#loginSubmit').click(); //处理事件
            }
        }); 
    },
    //跳转注册
    zsSubmitClick:function(){
        $('#zcsubmit').click(function(){
            location.href=U('User/Index/registerIndex?who=teacher');
        });
    },
    //登录提交
    loginSubmitClick:function(){
        $('#loginSubmit').click(function(){
            var userName=$('#UserName').val();
            var passWord=$('#Password').val();
            if(userName==''){
                alert('请填写用户名');
                return false;
            }
            if(passWord==''){
                alert('请填写密码');
                return false;
            }
            var ifsavelogin=0;
            if($('#saveLogin').attr('checked')=='checked'){
                ifsavelogin=1;
            }
            var  u='';//用户使用参数
            if(useAction!=''){
                u='?u='+useAction.replace(/\//g,'_');
            }
            $.ajax({
                type: "POST",
                url: U('Home/Index/login'+u) ,
                data: {'UserName':userName,'Password':passWord,'ifsave':ifsavelogin,'times':Math.random},
                cache: false,
                success: function(msg){
                    if($.myCommon.backLogin(msg)==false){
                         var timenow = new Date().getTime();
                        $('#verifyImg').attr('src',U('Index/verify?times='+timenow));
                        return false;
                    }
                    $('#loginform').css('display','none');
                    $('#loginMsg').html('<font color="#27ae61"><span style=" float:left; margin-top:1px;margin-right:10px; margin-left:40px"><img src="/Public/default/image/loading.gif"/></span><strong>登录成功！</strong></font>正在跳转...');
                    $('#loginMsg').css('display','block');
                    location.href=U('Index/main'+u);
                }
            });
        });
    },
    //验证码切换
    verifyImg:function(){
        $('#verifyImg').click(function(){
            var timenow = new Date().getTime();
            $('#verifyImg').attr('src',U('Index/verify?times='+timenow));
        });
    },
    //检测div位置及大小，用户名验证
    divReady:function(){
        $.checkLogin.InitDivHeight();
        $('#UserName').focus();
    },
    //重置弹出框位置
    InitDivHeight:function() {
        var mainwidth=$(window).width();
        var mainheight=$(window).height() - 2 - $('.header').height();
        var height = mainheight < 550 ? 550 : mainheight;
        var width=mainwidth < 960 ? 960 : mainwidth;
        var topwidth=(mainwidth > 960 ? 960 : mainwidth) < 600 ? 600 : (mainwidth > 960 ? 960 : mainwidth);
        $(".main").css({ 'height': height ,'width':width});
        $('.top').css({'width':topwidth});
        $('.header').css({'width':width});
        $('.footer').css({'width':topwidth});
    }
}