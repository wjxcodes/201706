$.fn.LoginBox = function(opts){
    return $(this).each(function(){
        var that = $(this);
        var options = $.extend({
            'id' : 'loginBox', 
            'loginAction' : '', //登录action
            'isLoginCheckAction' : '', //验证是否登录action
            'findPasswordAction' : '#', //密码找回
            'verifyCodeAction' : '', //验证码action
            'placeZone' : 'body', //该对话框显示在那个元素之中
            'onSubmit' : function(){ return true }, //提交之前进行的相关操作，需返回boolean值
            'onLogin' : function(loginBox){},  //登录的操作
            'onRegister' : function(regBox){},  //注册时进行的操作
            'hasLogined' : function(){}, //用户已经登录
            'showDialog' : showDialog,
            'that' : that
        },opts);
        that.click(function(){
            if(options.onSubmit()){
                //验证当前用户是否已经登录，当为false时，直接执行hasLogined函数
                if(options.isLoginCheckAction === false){
                    options.hasLogined();
                }else{
                    $.get(options.isLoginCheckAction,function(response){
                        var success = response['data'] || 'failure';
                        if(success != 'success'){
                            options.showDialog();
                        }else{
                            options.hasLogined();
                        }
                    });
                }
            }
            return false;
        });

        function showDialog(){
            $.get(options.loginAction,function(html){
                var loginBox = $('<div id="'+options.id+'">'+html+'<div>');
                $(options.placeZone).append(loginBox);
                setMarker();
                setDialog($('#'+options.id).find('.dialogBox'));
                bindEvent(loginBox);
                loginBox.find('.loginDialog .forget').attr('href', options.findPasswordAction);
                loginBox.find('.regDialog .reg').trigger('click');
                loginBox.show();                      
            });
        }

        function bindEvent(loginBox){
            loginBox.find('.yanZM').attr('src', options.verifyCodeAction).click(function(){
                $(this).attr('src', options.verifyCodeAction+'-r-'+Math.random());
            });
            loginBox.find('.loginDialog .login').click(function(){
                options.onLogin(loginBox.find('.loginDialog'));
                return false;
            });
            loginBox.find('.inputNormal').InputActivity();
            loginBox.find('.regDialog .login').click(function(){
                options.onRegister(loginBox.find('.regDialog'));
                return false;
            });
            loginBox.find('.loginDialog .reg').click(function(){
                window.location.href="Home-Index-register";
                return false;
                loginBox.find('.regDialog').show().siblings('.dialogBox').hide();
                return false;
            });

            loginBox.find('.regDialog .reg').click(function(){
                loginBox.find('.loginDialog').show().siblings('.dialogBox').hide();
                return false;
            });

            loginBox.find('.termDialog .norBtn').click(function(){
                var agreed = loginBox.find('.regDialog #agreed');
                agreed.attr('checked',true);
                agreed.val(1);
                loginBox.find('.loginDialog .reg').trigger('click');
                return false;
            });

            loginBox.find('.regDialog .term').click(function(){
                loginBox.find('.termDialog').show().siblings('.dialogBox').hide();
                return false;
            });

            loginBox.find('.regDialog #agreed').click(function(){
                var that = $(this);
                if(that.attr('checked')){
                    that.val(1);
                }else{
                    that.val(0);
                }
            });
            
            loginBox.find('.closeBtn').each(function(){
                $(this).click(function(){
                    loginBox.remove();
                });
            });
            
            $(window).resize(function(){
                setDialog($('#'+options.id).find('.dialogBox'));
                setMarker();
            });
        }

        function setDialog(dialog){
            if('body' == options.placeZone)
                var _window = $(window);
            else{
                var _window = $(options.placeZone);
            }
            dialog.each(function(){
                var that = $(this);
                var left = (_window.width() - that.width()) / 2;
                var top = (_window.height() - that.height()) / 2;
                that.css({
                    'position':'fixed',
                    'z-index':'9999',
                    'top' : top,
                    'left' : left
                });
            });
        }

        function setMarker(){
            var loginBox = $('#'+options.id).find('.lightBox');
            var placeZone = $(options.placeZone);
            loginBox.css({
                'width' : placeZone.width(),
                'height' : placeZone.height()
            }).show();
        }
    });
}

$.fn.InputActivity = function(opts){
    return $(this).each(function(){
        var options = $.extend({
            'title' : 'title', //从相关属性中获取，在未进行相关操作或内容为空时显示的文字描述。
            'msgbox' : '.msg',
            'onFocus' : function(){
                var that = $(this);
                var errorMsg = that.nextAll('.msg');
                if(errorMsg.length > 0 && errorMsg.html() != ''){
                    that.nextAll('.msg').html('');
                }
            },
            'onBlur' : function(){}
        }, opts);
        var that = $(this);
        var title = that.attr(options.title);
        var messageBox = that.next(options.msgbox);

        if(that.val() == ''){
            messageBox.show().css("color","red").html(title);
        }
        that.focus(function(){
            if(that.val() == title){
                that.val('');
                messageBox.html('');
            }
            options.onFocus.call(that);
        });
        that.blur(function(){
            var val = that.val();
            if('' == val || title == val){
                that.val('');
                messageBox.html(title);
            }
            options.onBlur.call(that);
        });

        messageBox.click(function(){
            that.focus();
        });
    });
}