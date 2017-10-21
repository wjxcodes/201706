//js  U方法生成访问路径
function U(models,vars,suffix){
    if(typeof(suffix)!='boolean') suffix=true;
    if(typeof(vars)=='undefined') vars='';
    if(typeof(local)=='undefined') local='/';

    var depr='/';
    var lastName='.html';
    var leftstr=local;
    var wenhao='?'; //结尾数据

    //去掉开头的斜杠
    var tmp=models.substr(0,1);
    if(tmp==depr){
        models=models.substr(1,models.length);
        leftstr='/';
    }

    //处理了连接中的数据到数组
    var tmpVar,tmpStr1,tmpStr2,tmpStr3,tmpStr4;

    if(vars!='' && typeof(vars)=='object'){
        tmpVar=new Array();
        var j=0;
        for(var i in vars){
            tmpVar[j]=i+'='+vars[i];
            j++;
        }
        vars=tmpVar.join('&');
    }

    tmpStr1=models.split(depr);
    if(tmpStr1[tmpStr1.length-1].indexOf('?')!=-1){
        tmpStr2=tmpStr1[tmpStr1.length-1].split('?');
        tmpStr1[tmpStr1.length-1]=tmpStr2[0];
        if(vars!='') vars=tmpStr2[1]+'&'+vars;
        else vars=tmpStr2[1];
    }

    //处理vars
    if(vars!=''){
        tmpStr2=new Array();
        tmpStr3=vars.split('&');
        if(tmpStr3.length==1 && tmpStr3[0].indexOf('=')==-1){
            wenhao+=tmpStr3[0];
        }else{
            for(var i=0;i<tmpStr3.length;i++){
                tmpStr4=tmpStr3[i].split('=');
                tmpStr2[i*2]=tmpStr4[0];
                tmpStr2[i*2+1]=tmpStr4[1];
            }
        }
    }

    //合并数组tmpstr1 tmpstr2
    var myUrl='';
    if(tmpStr1){
        if(leftstr.indexOf('Index')!=-1 && tmpStr1[0]!='Index' || tmpStr1.length==3){
            leftstr='/';
        }
        if(tmpStr1[0]=='Index' && tmpStr1.length==2){
            leftstr='/Index/';
        }

        leftstr+=tmpStr1.join(depr);
        if(vars!='') leftstr+=depr+tmpStr2.join(depr);
        myUrl=leftstr;
    }

    //添加后缀
    if(suffix==true){
        if(myUrl.substr(-1,1)!='/' && myUrl.indexOf(lastName)==-1){
            myUrl+=lastName;
        }
    }

    if(wenhao!='?'){
        myUrl+=wenhao;
    }

    return myUrl;
}

//cookie
(function() {
    window.Cookie = (function() {
        return {
            Set: function(a, b, c) {
                var d = new Date();
                d.setTime(d.getTime() + c * 24 * 60 * 60 * 1000);
                document.cookie = a + "=" + encodeURIComponent(b) + ";expires=" + d.toGMTString() + ";path=/"
            },
            Get: function(a) {
                var c=document.cookie.split(a+'=');
                if(c.length>1){
                    var d=c[c.length-1].split(';');
                    if(d.length>1){
                        return decodeURIComponent(d[0])
                    }else{
                        return decodeURIComponent(c[c.length-1])
                    }
                }else{
                    return null
                }

                //var b = document.cookie.match(new RegExp("(^| )" + a + "=([^;]*)(;|$)"));
                //if(a=='paperstyle') alert(b.length);
                //if (b != null) {
                //    return decodeURIComponent(b[2])
                //} else {
                //    return null
                //}
            },
            Del: function(a) {
                var b = new Date();
                b.setTime(b.getTime() - 100000);
                var c = this.Get(a);
                if (c != null) {
                    document.cookie = a + "=;expires=" + b.toGMTString() + ";path=/"
                }
            },
            Has: function(name){
                var ck=document.cookie.indexOf(name+"=");
                if(ck==-1) {
                    return false;
                }
                return true;
            }
        }
    })();
})();

//动态调取头部统计数据
jQuery.indexShowTotal={
    magicNumber:function(value,flag) {
        var flagArr=flag.split(',');
        for(var i in flagArr){
            var num = $('#'+flagArr[i]+'');
            alert(flagArr[i]);
            alert(value[flagArr[i]]);
            num.animate({count: value[flagArr[i]]}, {

                duration: 1500,
                step: function() {
                    if(this.count!=undefined){
                        num.text(String(parseInt(this.count)));
                    }    
                }
            });
        }

    },
    updateTotalMsg:function(flag,url) {
        if(typeof(url)=='undefined'){
            url=U('Statistics/Index/totalMsg');
        }
        $.ajax({
            url:url,
            type: 'POST',
            dataType: "json",
            data:{'flag':flag,'times':Math.random()},
            cache: false,
            timeout: 10000,
            success: function(data){
                var msg=data.data;
                //$.indexShowTotal.setTotalNum(msg,flag);
            }
        });
    },
    setTotalNum:function(value,flag){
        var flagArr=flag.split(',');
        for(var i in flagArr){
            var num = $('#'+flagArr[i]+'');
            if(value[flagArr[i]]!=undefined){
                num.text(String(parseInt(value[flagArr[i]])));
            }
        }
    }
};
jQuery.indexCommon={
    alertObj:'',//弹框对象,用于关闭layer弹框
    ifLock:'',//是否锁定点击事件标识
    pRetainTime:'',//倒计时时间
    sendTimeStr:'',//倒计时程序
    init:function(){
        this.bindLoginClick();//登录操作
        this.bindRegisterClick();//注册操作
        this.loginOut();//退出操作
        this.topLogin();//顶部登录
        this.showTopInfo();//显示顶部用户信息
        this.modalTab();//通用tab切换 class=g-tab
        this.goTop();//返回顶部
        this.changeImgVerify();//改变图片验证码
        this.changeLeft();//二级页面左侧切换
        this.bindGetPassword();
        this.loadDateEvent(); //载入日期

        if(typeof(userInfo)!=='undefined'){
            if(userInfo!==false){
                $.indexCommon.showUserInfo(userInfo);
            }
        }
        window.onscroll = function () {
            var x = document.body.scrollTop || document.documentElement.scrollTop;
            if (x < 500)$("#goTop").css("display","none");
            else $("#goTop").css("display","block");
        };
    },
    //载入日期
    loadDateEvent:function(){
        if($('.inputDate').length>0 || $('.inputTime').length>0 || $('.startDate').length>0 || $('.endTime').length>0){
            // 加载js文件
            var urls = new Array();
            urls.push("Public/plugin/laydate/laydate.js");
            load_script(urls);
        }
    },
    changeUrl:function(url){
        var tmpArr=url.split('/');
        var len=tmpArr.length;
        switch(len){
            case 3:
                var output=new Array();
                for(var i=1;i<len-1;i++){
                    output[i]=tmpArr[i];
                }
                return '/';
                break;
            case 4:
                var output=new Array();
                for(var i=1;i<len-2;i++){
                    output[i]=tmpArr[i];
                }
                return output.join('/')+'/';
                break;
        }
        return url;
    },
    //首页JS入口
    indexInit:function(){
        this.indexTop();//顶部的一些事件
//        this.ajaxTotal();//顶部统计
    },
    //首页顶部的一些事件
    indexTop:function(){
        var isIE6 = $.browser.msie && $.browser.version == "6.0";
        var topNavList = $(".top-nav-child-nav"),
            topNavWrap = $(".top-nav-wrap"),
            topNav = $(".top-nav-item");
        var leaveTime,enterTime;
        //导航条鼠标经过事件
        topNav.hover(function(){
            $(this).addClass("hover");

        },function(){
            $(this).removeClass("hover");
        })
        //导航自动固定到顶部
        if ($(".top-nav-fixed").length > 0) {
            $(window).scroll(function() {
                var topH = $(".top-nav-fixed").offset().top + 50,
                    scrolltop = $(window).scrollTop();
                if (scrolltop >= topH) {
                    topNavWrap.addClass("top-nav-wrap-fixed");
                    topNavWrap.stop().animate({
                        "top": "0"
                    }, 400);
                } else {
                    topNavWrap.removeClass("top-nav-wrap-fixed");
                    topNavWrap.css({
                        "top": ""
                    })
                }
            });
        };
    },
    //顶部统计
    ajaxTotal:function () {
        var url=U('Statistics/Index/totalMsg');
        var flag ='classNum,schoolNum,teacherNum,studentNum';
        $.indexShowTotal.updateTotalMsg(flag,url);
    },
    //鼠标经过延时插件
    hoverDelay:function(obj,opts){
        // hover延时执行插件
        // delayEnter 移入等待时长
        // delayLeave 移出等待时长
        // enterEvent 移入事件
        // leaveEvent 移出事件
        var defaults = {
            delayEnter: 200,
            delayLeave: 200,
            enterEvent: function () {
                obj.noop();
            },
            leaveEvent: function () {
                obj.noop();
            }
        };
        var opt = obj.extend(defaults, opts || {});
        var enterTimer, leaveTimer;
        obj.each(function () {
            obj.hover(function () {
                clearTimeout(leaveTimer);
                enterTimer = setTimeout(opt.enterEvent, opt.delayEnter);
            }, function () {
                clearTimeout(enterTimer);
                leaveTimer = setTimeout(opt.leaveEvent, opt.delayLeave);
            })
        })
    },
    //tab切换-组件
    modalTab: function () {
        $('.g-tab .tab-nav li').each(function () {
            var e = $(this);
            var trigger = e.closest('.g-tab-panel').attr("data-toggle");
            if (trigger == "hover") {
                e.mouseover(function () {
                    $showtabs(e);
                });
                e.click(function () {
                    return false;
                });
            } else {
                e.click(function () {
                    $showtabs(e);
                    return false;
                });
            }
        });
        $showtabs = function (e) {
            var detail = e.attr("data-href");
            e.closest('.g-tab .tab-nav').find("li").removeClass("on");
            e.closest('.g-tab').find(".tab-body .tab-panel").removeClass("on");
            e.addClass("on");
            $(detail).addClass("on");
        };
    },
    //返回顶部
    goTop:function(){
        $("#goTop").click(function(){
            $(document.body||document.documentElement).animate({"scrollTop":0},300);
            document.documentElement.scrollTop = 0;
            document.body.scrollTop = 0;
        });
    },
    //改变图片验证码
    changeImgVerify:function(){
        $('.change-validcode').click(function(){
            var time = new Date().getTime();
            $(this).prev().attr('src',U('Index/verify?'+time));
        });
    },
    //子页面左侧切换
    changeLeft:function(){
        var mid = window.location.search;
        var id = mid.replace("?","0");
        if (id !=="" && typeof(id)!=undefined && id!=null){
            var num = $('.childNav li').length;
            if(id>num){
                id="01";
            }
            var list = "ex-nr-";
            $('.childNav li').removeClass('this');
            $('.childNav').children().eq(id-1).addClass('this');
            $('.nrshow').hide();
            $("#"+list+id).show();
        }
        $('.childNav li').bind('click', function () {
            var curlist = 'ex-nr-0' + $(".childNav li.this").find('a').attr('data-type');
            $('.childNav li').removeClass('this');
            $(this).addClass('this');
            $('.nrshow').hide();
            var listID = 'ex-nr-0' + $(this).find('a').attr('data-type');
            $("#" + listID).show();
        });
    },
    //登录系统
    submitLogin:function(check){
        var self=this;
        if(self.ifLock!=''){//防止多次点击
            return false;
        }
        var userName=$('#userName').val();
        var passWord=$('#passWord').val();
        var ifSave=$('#login #ifSave:checked').val()?1:0;

        if(!userName){
            $.indexCommon.loginMsg();
            $('#login .errorMsg').html('请输入用户名！');
            return false;
        }
        if(!passWord){
            $.indexCommon.loginMsg();
            $('#login .errorMsg').html('请输入密码！');
            return false;
        }

        //$('#login .lf-login-msg').slideUp(200);
        self.ifLock=U('User/login');
        $.post(U('User/Index/login'),{'userName':userName,'passWord':passWord,'ifSave':ifSave,'times':Math.random()},function(e){
            $('.loginBox .submitMsg').hide();
            if(e.status==1){//登录成功
                if(check){
                    window.location.href="/";
                    return false;
                }
                //提示登录成功信息
                layer.msg('登录成功',{icon:1});
                //根据用户身份显示用户信息
                self.showTopInfo();
                self.showUserInfo(e.data);
                $('#login .lf-login-msg').hide();
            }else{
                self.loginMsg();
                var info=e.data;
                if(info.indexOf('管理员')!=-1) info=info.replace('管理员','<a href="/Index/About/about/param/aboutOnlineS.html" target="_blank">管理员</a>');
            
                $('#login .errorMsg').html(info);
            }
            self.ifLock='';
        });
    },
    //错误信息提示特效
    loginMsg:function(){
        if($('#login .lf-login-msg').is(':visible')){
            $('#login .lf-login-msg').fadeOut(1).fadeIn(1);
        }else{
            $(".login-form-box").animate({"top":"22px"},150);
            $('#login .lf-login-msg').slideDown(150).addClass("upspring");
        }
    },
    //登录的一些点击事件
    bindLoginClick:function(){
        var self=this;
        $(document).on('click','#loginSubmit',function(){
            var check = false;
            if($(this).hasClass('iQQLogin')){
                check = true;
            }
            self.submitLogin(check);
        });
        //光标进入移出事件
        $("#userName,#passWord").on({
            focusin:function(){
                $(this).parent().css("border-color","#4b88e6");
            },
            focusout:function(){
                $(this).parent().css("border-color","#ccc");
            }
        });
        //回车键登录
        $('#passWord').keydown(function(e){
            if(e.keyCode==13){
                $('#loginSubmit').click();
            }
        });
        //QQ登录事件
        $(document).on('click','.QQLogin',function(){
            window.open('http://www.tk.com/User/Index/QQLogin');
        });
    },
    baseLogin:function(){
        var self=this;
        var alertHtml = '<div class="dialogBox loginDialog">' +
            '    <div class="login-form-container loginForm" id="login">' +
            '        <div class="lf-login-item"> ' +
            '            <i class="iconfont">&#xe606;</i>' +
            '            <input type="text" name="userName" id="userName" placeholder="用户名/邮箱/手机号"/>' +
            '        </div>' +
            '        <div class="lf-login-item">' +
            '            <i class="iconfont">&#xe607;</i>' +
            '            <input type="password" name="password" id="passWord" placeholder="登录密码"/>' +
            '        </div>' +
            '        <div class="lf-login-other clearfix">' +
            '            <label class="auto-login" for="ifSave">' +
            '                <input type="checkbox" name="ifSave" id="ifSave"/>下次自动登录 ' +
            '            </label>' +
            '            <a class="find-password" id="zuJuan" href="'+U('User/Index/getPassword')+'">找回密码</a>' +
            '        </div>' +
            '        <div class="lf-login-btn">' +
            '            <input class="login-big-btn g-btn btn-primary" type="button" value="登录" id="loginSubmit" />' +
            '        </div>' +
            '        <div class="lf-reg-btn">' +
            '            没有账户？<a href="'+U('User/Index/registerIndex')+'">立即注册</a> 或 使用合作帐号登录'+
            '        </div>'+
            '        <div class="lf-quick-login">'+
            '            <a class="QQLogin" href="javascript:"><img src="/Public/index/imgs/icon/Connect_logo_3.png" alt=""/></a>'+
            '        </div>'+
            '    </div>' +
            '</div>';
        self.alertObj=layer.open({
            type: 1,
            title:'用户登录',
            shadeClose:true,
            area: ['304px', '400px'], //宽高
            content: alertHtml
        });
        $('.lightBox').hide();
        $('.loginDialog').show();
        self.bindAlertLogin();//弹窗登录的点击事件
    },
    //顶部登录按钮
    topLogin:function() {
        var self=this;
        $('.topLogin').on('click', '.topLoginButton',function () {
            self.baseLogin();
        });
    },
    //顶部弹窗登录的一些点击事件
    bindAlertLogin:function(){
        var self=this;
        //弹框中的图片验证码切换
        $('.loginDialog').find('.yanZM').attr('src', U('Base/verify')).click(function(){
            $(this).attr('src', U('Base/verify?r='+Math.random()));
        });
        $('.loginDialog').on('click','#loginSubmit',function(){
            self.topSubmitLogin();
        })
    },
    //登录后调用函数
    loginCallBack:function(data, user){

    },

    //顶部登录提交
    topSubmitLogin:function(){
        var self=this;
        if(self.ifLock!=''){//防止多次点击
            return false;
        }
        var userName=$('#userName').val();
        var passWord=$('#passWord').val();
        var ifSave=$('#login #ifSave:checked').val()?1:0;

        if(!userName){
            layer.msg('请输入用户名！',{icon:5});
            return false;
        }
        if(!passWord){
            layer.msg('请输入密码！',{icon:5});
            return false;
        }

        //$('#login .lf-login-msg').slideUp(200);
        self.ifLock=U('User/login');
        $.post(U('User/Index/login'),{'userName':userName,'passWord':passWord,'ifSave':ifSave},function(e){
            $('.loginBox .submitMsg').hide();
            if(e.status==1){//登录成功
                layer.msg('登录成功',{icon:1});
                //根据用户身份显示用户信息
                self.showTopInfo();
                //关闭弹框
                layer.close(self.alertObj);
            }else{
                layer.msg(e.data,{icon:5});
            }
            self.loginCallBack(e.status, e.data);
            self.ifLock='';
        });
    },
    //退出操作
    loginOut:function(){
        $(document).on('click','.loginOut',function(){
            var whois=$(this).attr('whois');
            $.post(U('User/Index/loginOut'),{'who':whois},function(e){
                if(e.status==1){
                    $('.topLoginSuccess').hide();
                    $('#loginSuccess').hide();
                    $('.top-bar-wrap .topLogin').show();
                    $('#loginForm').show();
                }else{
                    alert('退出失败请重试！');
                }
            })
        });
    },
    //显示用户信息
    showUserInfo:function(data){
        var whoName='<cite>学生</cite>';
        var different='<span>剩余点数：</span>'+data.Cz;
        var comeIn='<a class="in-sys-btn nor-btn" href="'+U("/Aat",'',false)+'" target="_blank">进入提分系统</a>';
        var userHref=U('User/Aat/index');
        if(data.Whois==1){
            whoName='<cite>教师</cite>';
            different='<span>组卷下载：</span>'+data.Times+'次';
            comeIn='<a class="in-sys-btn nor-btn" href="'+U("/Home",'',false)+'" target="_blank">进入题库系统</a>';
            userHref=U('Home/Index/main?u=User_Home_info');
        }
        $('.userPic').attr('src',data.UserPic);
        $('.whois').attr('href',userHref);
        $('.whois').html(whoName+data.UserName);
        $('.groupName').html(data.ChargeMode);
        $('.loginNum').html('<span>登录次数：</span>'+data.Logins+'次');
        $('.loginTime').html('<span>登录时间：</span>'+data.LoginTime);
        $('.loginIP').html('<span>登录IP：</span>'+data.LastIP);
        $('.different').html(different);
        $('.comeIn').html(comeIn);
        $('#loginForm').hide();
        $('#loginSuccess').show();
    },
    //显示顶部信息
    showTopInfo:function(){
        $.post(U('Index/Index/ajaxCheckLogin'),{'times':Math.random()},function(e){
            if(e.status == '1'){//已登录
                var topUserInfo='';//用户信息
                var goToSystem = '';//进入系统信息
                var data = e.data[0];
                if(data['Whois']==0){//显示学生
                    topUserInfo = '<span class="top-usename-container">' +
                        '    <a class="top-usename elli" href="'+U('User/Aat/index')+'">' + data['UserName']+
                        '    </a>' +
                        '</span>';
                    goToSystem = '<a href="'+U('/Aat','',false)+'">[ 进入提分系统 ]</a>';
                }else{//显示教师
                    topUserInfo = '<span class="top-usename-container">' +
                        '    <a class="top-usename elli" href="'+U('Home/Index/main?u=User_Home_info')+'"  target="_blank">'+
                        data['UserName']+
                        '    </a>'+
                        '    <i class="iconfont">&#xe618;</i>'+
                        '</span>'+
                        '<span class="tu-usemenu topBarUserMenu">'+
                        '    <i class="arrow-top"><em></em></i>' +
                        '    <a href="'+U('Home/Index/main?u=User_Home_info')+'" target="_blank">用户信息</a>' +
                        '    <a href="'+U('Home/Index/main?u=User_Home_testSave')+'" target="_blank">试题收藏</a>' +
                        '    <a href="'+U('Home/Index/main?u=User_Home_down')+'" target="_blank">历史下载</a>' +
                        '</span>';
                    goToSystem = '<a href="'+U("/Home",'',false)+'">[ 进入题库系统 ]</a>';
                }
                $('.topBarUserName').html(topUserInfo);
                $('.goToSystem').html(goToSystem);
                $('.loginOut').attr('whois',data['Whois']);
                $('.topLogin').hide();
                $('.topLoginSuccess').show();
            }else{
                $('.topLoginSuccess').hide();
                return false;
            }
            return false;
        });
    },
    //绑定注册事件
    bindRegisterClick:function(){
        var self=this;
        //密码强度提示
        if(typeof(strongPassword)=='object'){
            $('input:password#password').strongPassword();
        }
        //身份选项卡切换
        $('.user-id').on('click','.id-option',function(){
            var who = $(this).attr('who');//打算切换到的注册身份
            var onWho = $('.on').attr('who');//当前选中的身份
            if(who==onWho){//如果点击的对象是当前已选中的方式，就不做任何事情
                return false;
            }
            $('.user-id span').removeClass('on');
            $(this).addClass('on');
            $('.item-msg').hide();
            if(who=='student'){//学生
                $('.studentWay').attr('way',1);
                $('.studentWay').html('邮箱注册');
                $('.studentHelp').show();
                $('.teacherHelp').hide();
            }else{//教师（只能使用手机号注册）
                $('.studentHelp').hide();
                $('.emailWay').hide();
                $('.teacherWay').show();
                $('.teacherHelp').show();
            }
        });
        //学生注册方式切换
        $('.studentWay').on('click',function(){
            var way = $(this).attr('way');
            if(way==1){//注册方式从手机切换为邮箱
                $('.teacherWay').hide();
                $('.emailWay').show();
                $(this).attr('way',0);
                $(this).html('手机号注册');
            }else{//注册方式从邮箱切换回手机
                $('.teacherWay').show();
                $('.emailWay').hide();
                $(this).attr('way',1);
                $(this).html('邮箱注册');
            }
        });
        //获取图片验证码弹框
        $('.teacherWay').on('click','.getPhoneCode',function(){
            var phoneNum=$('#PhoneNum').val();
            var pregStr=/^0?(13|14|15|17|18)[0-9]{9}$/;
            if(phoneNum==''){
                self.showRegisterError('phone','手机号不能为空');
                return false;
            }
            if(!pregStr.test(phoneNum)){
                self.showRegisterError('phone','请输入正确的手机号');
                return false;
            }
            self.showRegisterSuccess('phone');
            var alertHTML='<div>' +
                '<div  class="verification-code-msg">' +
                '<span class="item-tit">图片验证码</span>' +
                '<input type="text" name="imgCode" maxlength="6" class="normal-input imgCode">' +
                '<img width="95" height="36" class="verification-image" id="verifyImg" src="'+U('Index/verify')+'" BORDER="0" ALT="点击刷新验证码" style="cursor:pointer" align="absmiddle"></div>'+
                    '<div class="sendMsg" id="sendMsg"></div>'+
                    '<div class="sendYes">' +
                '<span class="sendYesBtn nor-btn" id="sendYes">确定</span>' +
                '</div>'+
                '</div>';
            self.alertObj=layer.open({
                type: 1,
                title:'请输入验证码',
                closeBtn:1,
                area: ['410px', '180px'], //宽高
                content: alertHTML
            });
        });
        //确定发送手机验证码
        $(document).on('click','#sendYes',function(){
            var imgCode = $('.imgCode').val();
            var phoneNum = $('.phoneNum').val();
            if(imgCode==''){
                //提示
                self.showRegisterError('send','请输入图片验证码');
                return false;
            }
            var pregStr=/^[0-9]{4}$/;
            if(!pregStr.test(imgCode)){
                self.showRegisterError('send','图片验证码只能是4位数字');
                return false;
            }
            self.sendPhoneCode(phoneNum,imgCode,0);
        });
        //切换验证码
        $(document).on('click','#verifyImg',function(){
            var time = new Date().getTime();
            $(this).attr('src',U('Index/verify')+'?'+time);
        });
        //通过邮箱注册时，获取邮箱验证码
        $('.emailWay').on('click','.getPhoneCode',function(){
            var email = self.getEmail();
            if(email == false){
                return false;
            }
            self.sendEmailCode(email,0,1);
        });
        //判断是否显示密码强度
        $('input:password#password').on('keyup',function(){
            $('#passwordMsg').hide();
            $('#strongPassword').show();
        });
        //服务条款弹出框
        $('.serviceTerm').on('click',function(){
            self.alertObj=layer.open({
                type : 1,
                title: '题库用户注册协议',
                area : ['850px','570px'],
                success:function(){
                    $.post(U('User/Index/getServiceTerm'),function(data){
                        if(data['info']=='success'){
                            $('.layui-layer-content').html(data.data);

                        }else if(data['info']=='error'){
                            $('.layui-layer-content').html('<p style="padding:15px 0;text-align: center;font-size: 16px;">服务条款获取失败,请稍后重试</p>');
                        }
                    });
                }
            });
        });
        //关闭服务条款弹框
        $(document).on('click','.norBtn ',function(){
            layer.close($.indexCommon.alertObj);
            $('#checkboxAgree').attr('checked','checked');
        });
        //不勾选同意服务协议
        $('#checkboxAgree').on('click',function(){
            if(!$('#checkboxAgree').is(":checked")){
                $('#registerSave').removeClass('registerSave');
                $('#registerSave').removeClass('blue-btn');
                $('#registerSave').addClass('grey-btn');
            }else{
                $('#registerSave').removeClass('grey-btn');
                $('#registerSave').addClass('blue-btn');
                $('#registerSave').addClass('registerSave');
            }
        });
        //保存注册信息
        $(document).on('click','.registerSave',function(){
            var reg = 0;//是否终止程序标识
            var data = {};//提交内容
            var userName = '';//用户名
            var who = $('.on').attr('who');//註冊身份
            var way = 1;//註冊方式，默认是手机号
            if(who == 'student'){//學生
                way = $('.studentWay').attr('way');
            }
            if(way == 1){//使用手机号注册
                //获取手机号
                userName = self.getPhoneNum();
                if(userName == false){
                    reg = 1;
                }
//              //获取用户输入的手机验证码
//              var phoneCode = self.getUserPhoneCode();
//              if(phoneCode == false){
//                  reg = 1;
//              }else{
//                  data['phoneCode'] = phoneCode;
//              }
            }else{//使用邮箱
                userName = self.getEmail();
                if(userName == false){
                    reg = 1;
                }
//              var emailCode = self.getUserEmailCode();
//              if(emailCode == false){
//                  reg = 1;
//              }else{
//                  data['emailCode'] = emailCode;
//              }
            }
            //获取用户昵称
            var nickname = self.getNickname();
            if(nickname == false){
                reg = 1;
            }else{
                data['nickname'] = nickname;
            }
            //获取密码
            var passwordArr = self.getPassword();
            if(passwordArr == false){
                reg = 1;
            }else{
                data['password'] = passwordArr[0];
                data['password1'] = passwordArr[1];
            }
            //是否选中注册协议
            if(!$('#checkboxAgree').is(":checked")){
                self.showRegisterError('agree', '请接受服务条款！');
                reg = 1;
            }else{
                $('#agreeMsg').hide();
            }
            if(reg == 1){
                return false;
            }
            data['who'] = who;
            data['way'] = way;
            data['userName'] = userName;
            $.post(U('User/Index/registerSave'),{'data':data},function(e){
                if(e.status == 1){//注册成功
                    if(who == 'student') {
                        //如果是学生注册的，显示注册成功页面
                        window.location.href = U('User/Index/regSeedEmail');
                    }else {
                        //如果是手机注册的，显示注册成功教师认证页面
                        window.location.href = U('User/Index/regSucceedTeacher?Nickname='+nickname);
                    }
                }else{//注册失败
                    //弹框提示错误信息
                    layer.msg(e.data,{icon:5});
                }
            },'json');
        });
    },
    //获取手机号
    getPhoneNum:function(){
        var phoneNum=$('.phoneNum').val();
        var reg = 0;//错误标识，防止重复验证
        if(phoneNum==''){
            this.showRegisterError('phone','手机号不能为空');
            reg=1;
        }
        if(reg!=1) {
            var pregStr=/^0?(13|14|15|17|18)[0-9]{9}$/;//验证正则
            if (!pregStr.test(phoneNum)) {
                this.showRegisterError('phone', '请输入正确的手机号');
                reg=1;
            }
        }
        if(reg==1){
            return false;
        }
        this.showRegisterSuccess('phone');
        return phoneNum;
    },
    //获取用户输入的手机短信验证码
    getUserPhoneCode:function(){
        var phoneCode = $('.phoneCode').val();
        var reg = 0;//错误标识
        if(phoneCode == ''){
            this.showRegisterError('phoneCode','请输入手机验证码');
            reg = 1;
        }
        if(reg != 1) {
            var pregStr = /^[0-9]{6}$/;
            if(!pregStr.test(phoneCode)){
                this.showRegisterError('phoneCode','短信验证码为6位数字');
                reg = 1;
            }
        }
        if(reg == 1){
            return false;
        }
        this.showRegisterSuccess('phoneCode');
        return phoneCode;
    },
    //获取邮箱号
    getEmail:function(){
        var email = $('.emailNum').val();//用户输入的邮箱号
        var reg = 0;//错误标识
        if(email == ''){
            this.showRegisterError('email','请输入邮箱号');
            reg = 1;
        }
        if(email != '') {
            var emailReg = /^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;
            if (!emailReg.test(email)) {
                this.showRegisterError('email','请输入正确的邮箱号');
                reg = 1;
            }
        }
        if(reg == 1){
            return false;
        }
        this.showRegisterSuccess('email');
        return email;
    },
    //获取用户输入的邮箱验证码
    getUserEmailCode:function(){
        var emailCode = $('.emailCode').val();
        var reg = 0;//错误标识
        if(emailCode == ''){
            this.showRegisterError('emailCode','请输入邮箱验证码');
            reg = 1;
        }
        if(reg != 1) {
            var pregStr = /^[0-9]{6}$/;
            if(!pregStr.test(emailCode)){
                this.showRegisterError('emailCode','邮箱验证码为6位数字');
                reg = 1;
            }
        }
        if(reg == 1){
            return false;
        }
        this.showRegisterSuccess('emailCode');
        return emailCode;
    },
    //获取用户输入的昵称
    getNickname:function(){
        var nickname=$('.nickname').val();//昵称
        var ne=0;//昵称错误标识
        if(nickname==''){
            this.showRegisterError('nickname','昵称不能为空！');
            ne = 1;
        }
        if(ne!=1) {
            var preg = /^[\u4e00-\u9fa5a-zA-Z0-9]+$/;
            if (!preg.test(nickname)) {
                this.showRegisterError('nickname', '昵称只允许汉字，字母和数字组合！');
                ne = 1;
            }
        }
        if(ne!=1) {
            var nicknameLength = nickname.replace(/[^\x00-\xff]/g, 'xxx').length;//一个汉字3个字节utf8
            if (nicknameLength < 3 || nicknameLength > 15) {
                this.showRegisterError('nickname', '昵称长度必须大于1个汉字且小于5个汉字！');
                ne = 1;
            }
        }
        if(ne==1){
            return false;
        }
        this.showRegisterSuccess('nickname');
        return nickname;
    },
    //获取密码
    getPassword:function(){
        var passWord=$('.password').val();//密码
        var passWord1=$('.password1').val();//第二次输入的密码
        var pe=0;//密码错误标识，防止重复验证
        if(passWord==''){
            this.showRegisterError('password','请填写密码！');
            pe=1;
        }
        if(pe!=1) {
            if (passWord.length < 6 || passWord.length > 18) {
                this.showRegisterError('password', '请输入6-18位密码！');
                pe = 1;
            }
        }
        if (passWord != passWord1) {
            this.showRegisterError('password1', '两次密码输入不一致！');
            pe = 1;
        }
        if(pe==1){
            $('#strongPassword').hide();
            return false;
        }
        this.showRegisterSuccess('password1');
        return [passWord,passWord1];
    },
    //发送短信验证码
    sendPhoneCode:function(phoneNum,imgCode,userID){
        var self = this;
        if(self.ifLock != ''){//防止重复点击
            return false;
        }
        self.ifLock = 'sendPhoneCode';
        var sendLoad = layer.load();//等待提示
        $.post(U('User/Index/sendPhoneCode'),{'phoneNum':phoneNum,'imgCode':imgCode,'userID':userID,'rand':Math.random()},function(e){
            layer.close(sendLoad);//关闭等待提示
            $('.sendMsg').html('');
            self.ifLock = '';
            if(e.status==1){
                layer.close(self.alertObj);
                layer.msg(e.data);
                self.showLeaveTime('sendPhoneCode');
            }else{
                self.showRegisterError('send',e.data);
                return false;
            }
        });
    },
    //发送邮箱验证码
    sendEmailCode:function(email,userID,way){
        var self = this;
        if(self.ifLock != ''){//防止重复点击
            return false;
        }
        self.ifLock = 'sendEmailCode';
        var sendLoad = layer.load();//等待提示
        $.post(U('User/Index/sendEmailCode'),{'email':email,'userID':userID},function(e){
            layer.close(sendLoad);//关闭等待提示
            $('.sendMsg').html('');
            self.ifLock = '';
            if(e.status==1){
                layer.msg('发送成功！');
                if(way==1) {//兼容发送短信验证码按钮和发送邮箱验证码按钮在同一个页面的情况
                    self.showLeaveTime('sendEmailCode');
                }else{//这个是发送手机验证码和邮箱验证码为同一个按钮的情况
                    self.showLeaveTime('sendPhoneCode');
                }
            }else{
                if(way==1) {
                    layer.msg(e.data,{icon:5});
                }else{
                    self.showRegisterError('send',e.data);
                }

                return false;
            }
        })
    },
    //显示倒计时
    showLeaveTime:function(idName){
        var second=60;
        if($.indexCommon.pRetainTime!=0){second=$.indexCommon.pRetainTime;}
        $("#"+idName).val('重新发送('+second+')');
        $(".pointer").removeClass('getPhoneCode');

        $.indexCommon.sendTimeStr = setInterval(function () {
            second--;
            if (second <= 0) {
                $.indexCommon.clearLeaveTime();
                return false;
            }
            $.indexCommon.pRetainTime = second;
            $("#"+idName).val('重新发送('+second+')');
        }, 1000);
    },
    //清除倒计时程序
    clearLeaveTime:function(){
        $.indexCommon.pRetainTime=0;
        $(".pointer").addClass('getPhoneCode');
        $(".pointer").val('获取验证码');
        clearInterval($.indexCommon.sendTimeStr);
    },
    //显示错误信息
    showRegisterError:function(id,str){
        $('#'+id+'Msg').html('<i class="false iconfont">&#xe634;</i>'+str);
        $('#'+id+'Msg').show();
    },
    showRegisterSuccess:function(id){
        $('#'+id+'Msg').html('<i class="true iconfont">&#xe631;</i>').show();
    },
    //绑定找回密码事件
    bindGetPassword:function(){
        var self=this;
        var type = '';
        //跳转到第二步
        $('#getPassword').on('click','.skipTwo',function(){
            var userStyle = $('#thisStyle').val();
            //获取用户输入的用户名
            var userName = $('#getPassword .userName').val();
            if(userName == ''){
                self.showRegisterError('userName','请输入用户名');
                return false;
            }
            //获取用户输入的验证码
            var imgCode = $('#getPassword .imgCode').val();
            if(imgCode == ''){
                self.showRegisterError('verifyImg','请输入图片验证码');
                return false;
            }
            //进行用户名是否存在的验证，及图片验证码的验证
            $.post(U('User/Index/checkGetPasswordInfo'),{'userName':userName,'imgCode':imgCode,'userStyle':userStyle,'time':Math.random()},function(e){
                if(e.status==1){
                    //如果用户输入的信息是正确的
                    var dataArr=e.data;
                    $('#userID').val(dataArr['userID']);
                    var opt = '';
                    //将第二步可以验证的信息写入
                    if(dataArr['phone']) {
                        opt += '<option value="' + dataArr['phone'] + '">' + dataArr['phone'] + '</option>';
                    }
                    if(dataArr['email']) {
                        opt += '<option value="' + dataArr['email'] + '">' + dataArr['email'] + '</option>';
                    }
                    $('#checkStyle').html(opt);
                    $('.getTab1').hide();
                    $('.getTab2').show();
                    $('.getTab').removeClass('fp-step1');
                    $('.getTab').addClass('fp-step2');
                    $('.getTab').find('span:lt(2)').addClass('on');
                    $('#goNext').removeClass('skipTwo');
                    $('#goNext').addClass('skipThree');
                    $('#goBack').show();//返回上一步
                }else{
                    layer.msg(e.data,{icon:5});
                    return false;
                }
            })
        });
        //第二步操作
        //根据用户选择的验证方式发送验证码
        $('#getPassword').on('click','.getPhoneCode',function(){
            //获取用户选择的验证方式
            var style = $('#checkStyle').val();
            var userID = $('#userID').val();
            //通过JS正则验证，判断是邮箱还是手机号
            //如果为手机号，则发送短信
            var pregStr=/^0?(13|14|15|17|18)[0-9]{9}$/;//验证正则
            if (pregStr.test(style)) {
                var imgCode = $('#getPassword .imgCode').val();
                self.sendPhoneCode(style,imgCode,userID);
                type = 1;
                return false;
            }
            //如果为邮箱，则发送邮件
            var emailReg = /^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;
            if(emailReg.test(style)){
                self.sendEmailCode(style,userID,0);
                type = 2;
                return false;
            }
        });
        //根据用户是否改变验证方式来确定发送验证码按钮是否显示倒计时
        $('#checkStyle').change(function(){
            if($.indexCommon.pRetainTime!=0){
                self.clearLeaveTime();
            }
        });
        //跳转到第三步
        $(document).on('click','.skipThree',function(){
            //首先验证验证码是否正确
            //var code = $('.phoneCode').val();
            var style = $('#checkStyle').val();
            var userID = $('#userID').val();
//          if(code == ''){
//              self.showRegisterError('send','请输入验证码');
//              return false;
//          }
//          if(code.length != 6){
//              self.showRegisterError('send','请输入6位数字的验证码');
//              return false;
//          }
//          if(type == ''){
//              self.showRegisterError('send','你还没获取验证码');
//              return false;
//          }
            //$.post(U('User/Index/checkCode'),{'code':code,'userID':userID,'type':type,'style':style},function(e){
            $.post(U('User/Index/checkCode'),{'userID':userID,'type':type,'style':style},function(e){
                //如果正确，跳转到第三步
                if(e.status==1){
                    $('.getTab2').hide();
                    $('.getTab3').show();
                    $('.getTab').removeClass('fp-step1');
                    $('.getTab').addClass('fp-step3');
                    $('.getTab').find('span:lt(3)').addClass('on');
                    $('#goNext').removeClass('skipThree');
                    $('#goNext').addClass('setPassword');
                    $('#goNext').html('重置密码');

                    //设置安全码
                    $('.savecodes').val(e['data'][1]);

                }else{
                    //如果不正确，提示
                    layer.msg(e.data,{icon:5});
                }

            });
        });
        //重置密码
        $(document).on('click','.setPassword',function(){
            var data = self.getPassword();
            var userID = $('#userID').val();
            if(data== false){
                return false;
            }
            var savecodes=$('.savecodes').val();
            $.post(U('User/Index/setPassword'),{'userID':userID,'password':data[0],'password1':data[1],'s':savecodes,'times':Math.random()},function(e){
                if(e.status==1){
                    //弹出成功提示，及跳转按钮
                    var alertSuccess = '<div class="getPasswordAlert">' +
                        '<div class="gp-msg-title">' +
                        '<i class="iconfont">&#xe631;</i>' +
                        '<span class="setPasswordMsg">修改密码成功</span></div>' +
                        '<div class="nor-btn skipYse"><span id="skipYesBtn">确定</span></div>' +
                        '</div>';
                    self.alertObj=layer.open({
                        type : 1,
                        closeBtn:false,
                        title:false,
                        area: ['410px', '120px'], //宽高
                        content: alertSuccess
                    });
                }else{
                    layer.msg(e.data);
                    return false;
                }
            })
        });
        //确定后跳转
        $(document).on('click','#skipYesBtn',function(){
            layer.close($.indexCommon.alertObj);
            window.location.href = '/';
        });
        //返回上一步
        $(document).on('click','#goBack',function(){
            $('.getTab2').hide();
            $('.getTab3').hide();
            $('.getTab1').show();
            $('.getTab').removeClass('fp-step2');
            $('.getTab').addClass('fp-step1');
            $('.getTab').find('span:lt(1)').addClass('on');
            $('#goNext').removeClass('skipThree');
            $('#goNext').addClass('skipTwo');
            $(this).hide();
        })
    }
};


if(typeof(local)=='undefined'){
    var local = '/Index/Index/index';
}
local = $.indexCommon.changeUrl(local);
$.indexCommon.init();