<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <title>登录 - 智慧云题库云平台</title>
    <meta name="viewport" content="initial-scale=1, width=device-width, maximum-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <meta name="renderer" content="webkit">
    <link type="text/css" href="/Public/index/css/wln-base.css" rel="stylesheet" />
    <link type="text/css" href="/Public/index/css/login.css" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
</head>

<body class="bg-white">
    <div class="top-logo-wrap">
        <a class="top-logo hidden-x" href="/" title="返回首页">
            <img src="/Public/index/imgs/publ/logo.png" alt="题库logo">
        </a>
        <a class="top-logo hide hidden-x" href="/" title="返回首页">
            <img src="/Public/index/imgs/publ/logo_sub.png" alt="题库logo">
        </a>
        <h1 class="page-title">用户登录</h1>
    </div>
    <!-- 登录框 -->
    <div class="login-page-wrap clearfix">
        <div class="login-page-content respw">
            <form action="" class="lf-login-wrap" id="login">
                <div class="lf-login-item">
                    <i class="iconfont">&#xe606;</i>
                    <input type="text" name="userName" id="userName" placeholder="用户名/邮箱/手机号">
                </div>
                <div class="lf-login-item">
                    <i class="iconfont">&#xe607;</i>
                    <input type="password" name="password" id="passWord" placeholder="登录密码">
                </div>
                <div class="lf-login-userid">
                    <label for="userid1">
                        <input id="userid1" type="radio" name="role" class='role' value="teacher" />老师</label>
                    <label for="userid3">
                        <input id="userid3" type="radio" name="role" class='role' value="student"/>学生</label>
                    <label for="userid2">
                        <input id="userid2" type="radio" name="role" class='role' value="bywork"/>兼职</label>
					<label for="userid4">
                        <input id="userid4" type="radio" name="role" class='role' value="school"/>校长</label>
                </div>
                <div class="lf-login-msg" id='error' style='display:none;'>
                    <i class="iconfont">&#xe619;</i>
                    <span class="errorMsg">密码错误</span>
                </div>
                <div class="lf-login-other clearfix">
                    <label class="auto-login" for="ifSave">
                        <input type="checkbox" name="ifSave" id="ifSave">下次自动登录
                    </label>
                    <a class="find-password" id="zuJuan" href="<?php echo U('User/Index/getPassword');?>">找回密码</a>
                </div>
                <div class="lf-login-btn">
                    <input class="login-big-btn nor-btn" type="button" value="登录" id="loginSubmit">
                </div>
                <div class="lf-reg-btn">
                    还没有账户？
                    <a href="<?php echo U('User/Index/registerIndex');?>">马上注册</a>
                </div>
                <div class="lf-quick-login center">
                    其他登录方式：
                    <a id="QQLogin" href="/User/Index/QQLogin" target="_blank"><img src="/Public/index/imgs/icon/qq.png" alt=""></a>
					<a class="ForLogin" href="http://web.forclass.net/OAuth/authorize?response_type=token&client_id=Diagnosis20170808&redirect_uri=http://tk.forclass.net/User/Index/ForClassCalllogin">
                             <img style="padding-left: 5px;" src="/Public/index/imgs/icon/logom.png" alt=""/>
                         </a>
                </div>
            </form>
        </div>
    </div>
    <!-- 登录框 END-->
 

    <script>
        var redirect = '<?php echo ($redirect); ?>';
        //根据redirect选中用户角色
        var roles = {
            'bywork' : ['Teacher'],
            'student' : ['Aat'],
            'teacher' : ['Home'],
			'school' : ['statistics']
        }
        if(window.top != window.self){
            window.top.location=window.location.href;
        }

        if(redirect){
            redirect = redirect.replace(/^\//, '');
            redirect = decodeURIComponent(redirect);
            var url = redirect.toLowerCase();
            for(var role in roles){
                for(var i=0; i<roles[role].length; i++){
                    roles[role][i] = roles[role][i].toLowerCase();
                    if(url.indexOf(roles[role][i]) === 0){
                        setRole(role);
                        break;
                    }
                }
            }
        }
        $('#userName').focus();
        $('#loginSubmit').on('click', function(){
            var username = $('#userName');
            var username_val = username.val();
            if(!username_val){
                promptInfo('用户名不能为空！');
                username.focus();
                return false;
            }
            // if(!/13\d{9}/g.test(username_val) && !/[\w|\d]+@[\w|\d]+\.[\w]{3}/g.test(username_val)){
            //     promptInfo('用户名只能为手机号码或者邮箱！');
            //     username.select();
            //     return false;
            // }
            var password = $('#passWord');
            var password_val = password.val();
            if(!password_val){
                promptInfo('密码不能为空！');
                password.focus();
                return false;
            }
            var remember = '';
            if($('#ifSave').attr('checked')){
                remember = 'remember';
            }
            var role = $('.role:checked').val() || '';
            promptInfo('正在登录...');
            var data = {
                'username' : username_val,
                'password' : password_val,
                'role' : role,
                'remember' : remember
            };
            $.post('/User/Index/passportLogin', data, function(rep){
                if(rep != 'success'){
                    promptInfo(rep);
                }else{
                    var jumpTo = '/';
                    if(redirect != '' && role != ''){
                        redirect = jumpTo + redirect;
                        var go = jumpTo + roles[role][0].firstLetterToUpperCase();
                        if(redirect.indexOf(go) !== 0){
                            window.location.href = go;
                        }else{
                            window.location.href = redirect;
                        }
                        return;
                    }else{
                        if(redirect != ''){
                            window.location.href = jumpTo+redirect;
                            return;
                        }
                        if(role != ''){
                            window.location.href = jumpTo+roles[role][0].firstLetterToUpperCase();
                            return;
                        }
                    }
                    window.location.href = jumpTo;
                }
            });
        });
        
        $(document).keyup(function(e){
            if(13 == e.keyCode && $('#userName').val() != '' && $('#passWord').val() != ''){
                $('#loginSubmit').trigger('click');
            }
        });

        function promptInfo(info){
            if(info.indexOf('管理员')!=-1) info=info.replace('管理员','<a href="/Index/About/about/param/aboutOnlineS.html" target="_blank">管理员</a>');
            $('#error').show().find('.errorMsg').html(info);
        }

        function setRole(role){
            $('.role').each(function(){
                var that = $(this);
                var status = false;
                if(role == that.val()){
                    status = true;
                }
                that.attr('checked', status);
            });
        }
        String.prototype.firstLetterToUpperCase = function(){
            if(this.length < 1){
                return this;
            }
            return this[0].toUpperCase()+this.substr(1);
        }
    </script>
</body>

</html>