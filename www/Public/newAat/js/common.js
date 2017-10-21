//-------------------------初始化代码-----------------------------------------------------------------------------------
(function () {
    //--------------提分系统公共类--------------------------------------------------------------------------------------
    AatCommon = {
        config: {
            msgBox:'.msgBox'
        },
        preg: {
            phone:/^1[0-9]{10}$/,
            email:/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/
        },
        init: function (config) {
            this.config.urlDepartment = config.urlDepartment;
            this.config.isSub = config.isSub;
            this.config.appUrl = config.appUrl;
            this.config.groupName = config.groupName;
            this.config.cookiePrefix = config.cookiePrefix;
            this.config.cookieUserID = config.cookieUserID;
            this.config.cookieUsername = config.cookieUsername;
            this.config.cookieUserCode = config.cookieUserCode;
            this.config.cookieSubjectID = config.cookieSubjectID;
            this.config.cookieVersionID = config.cookieVersionID;
            this.config.cookieIndexMsg = config.cookieIndexMsg;
        },
        getUserName:function(){
            return $.cookie(this.config.cookieUsername);
        },
        getUserCode:function(){
            return $.cookie(this.config.cookieUserCode);
        },
        setUserName: function (userName,expire) {
            var cookieName = this.config.cookieUsername;
            if(cookieName === null){
                $.removeCookie(cookieName,{path:'/'});
            }else{
                $.cookie(cookieName, userName, {expires: expire, path:'/'});
            }
        },
        getSubjectID:function(){
            return $.cookie(this.config.cookieSubjectID);
        },
        setSubjectID:function(subjectID){
            var cookieName = this.config.cookieSubjectID;
            if(cookieName === null){
                $.removeCookie(cookieName,{path:'/'});
            }else{
                $.cookie(cookieName, subjectID, {expires: 360, path:'/'});
            }
        },
        getMsg:function(){
            return $.cookie(this.config.cookieIndexMsg);
        },
        getUrl:function(){
            return $.cookie(this.config.cookieIndexMsg+'url');
        },
        setUrl:function(message){
            var cookieName = this.config.cookieIndexMsg+'url';
            if(message === null){
                $.removeCookie(cookieName,{path:'/'});
            }else{
                $.cookie(cookieName,message,{path:'/'});
            }
        },
        setMsg:function(message){
            var cookieName = this.config.cookieIndexMsg;
            if(message === null){
                $.removeCookie(cookieName,{path:'/'});
            }else{
                $.cookie(cookieName,message,{path:'/'});
            }
        },
        checkCondition:function(success){
            var self = this;
            $.post(U('PushTest/checkCondition'),function(e){
                //如果出错
                if(e.status == 0){
                    //提示框关闭后执行的代码
                    var close = function(){
                        if($('#leftsub').is(':hidden')){
                            $('.leftsub_an_off').trigger('click');
                        }
                        //如果有已经选择的学科，则触发点击学科弹出选择教材版本窗口
                        if($('.xk_this')){
                            $('.xk_this').trigger('click');
                        }
                    };
                    self.setMsg(e.data);
                    if(e.data.indexOf('个人中心')!=-1) self.setUrl(U('User/Aat/index'));
                    if(e.data.indexOf('冻结')==-1){
                        self.showMsg(close);
                    }else{
                        self.showMsg();
                    }
                    return false;
                }else{
                    //通过后执行的函数
                    success();
                }
            });
        },
        showMsg:function(close){
            var content = this.getMsg();
            var url = this.getUrl();
            this.setUrl('');
            var msgBox = this.config.msgBox;
            if(content){
                content = '<div class="msgBox">'+
                            '<div class="msg">'+content+'</div>'+
                            '<input type="button" class="alertOk close" id="submit01" value="确 定">'+
                           '</div>';
                $.aDialog({
                    width:328,
                    height:190,
                    title:'提示',
                    content:content,
                    close:function(){
                        if(typeof(url)!='undefined' && url!=''){
                            location.href=url;
                        }
                        if(close){
                            close();
                        }
                    }
                });
                this.setMsg(null);
            }
        },
        star:function(right,all){
            //小星星显示函数
            var star = '', title = '', class01 = 'start_03', class02 = 'start_03', class03 = 'start_03', class04 = 'start_03', class05 = 'start_03';
            if (!all || all == 0) {//E
                title = '知识点还没有测试，请进行测试！';
            } else {
                var rate = right / all * 100;
                if (rate < 60) {//E
                    title = '知识点还没有掌握，多加努力！';
                    class01 = 'start_01';
                } else if (rate < 70) {//D
                    title = '知识点还没有掌握，还需努力！';
                    class01 = class02 = 'start_01';
                } else if (rate < 80) {
                    title = '知识点掌握的一般，还需努力！';
                    class01 = class02 = class03 = 'start_01';
                } else if (rate < 90) {
                    title = '知识点掌握的不错，还需努力！';
                    class01 = class02 = class03 = class04 = 'start_01';
                } else {
                    title = '知识点掌握的不错，继续努力！';
                    class01 = class02 = class03 = class04 = class05 = 'start_01';
                }
            }
            star = '<div class="start_box fc" title="'+title+'">'+
                        '<span class="'+class01+'"></span>'+
                        '<span class="'+class02+'"></span>'+
                        '<span class="'+class03+'"></span>'+
                        '<span class="'+class04+'"></span>'+
                        '<span class="'+class05+'"></span>'+
                    '</div>';
            return star;
        },
        initICheck:function(){
            var checkBox = $('input:checkbox');
            checkBox.iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue',
                increaseArea: '20%'
            });
            checkBox.parent().parent().show();
        },
        //显示浏览器升级信息
        showBrowserInfo:function(){
            var browserStr='<div id="explorer">您的浏览器版本太低，建议升级安装以下浏览器来访问题库提分系统，兼容好速度快。<br />'+
                           '<a id="ie" title="IE浏览器" href="http://www.microsoft.com/zh-cn/download/internet-explorer.aspx" target="_blank"></a>'+
                           '<a id="firefox" title="火狐浏览器(推荐)" href="http://firefox.com.cn/download/" target="_blank"></a>'+
                           '<a id="chrome" title="谷歌浏览器(推荐)" href="http://rj.baidu.com/soft/detail/14744.html" target="_blank"></a>'+
                           '<a id="sogou" title="搜狗浏览器" href="http://ie.sogou.com/" target="_blank"></a>'+
                           '</div>';
            var browser=this.checkBrowser();
            if(browser){
                $("#wrapper").before(browserStr);
            }
        },
        //检测浏览器版本
        checkBrowser:function(){
            var ie6 = /msie\s6/.test(navigator.userAgent.toLowerCase());
            var ie7 = /msie\s7/.test(navigator.userAgent.toLowerCase());
            var ie8 = /msie\s8/.test(navigator.userAgent.toLowerCase());
            if(ie6 || ie7){
                return true;
            }else{
                return false;
            }
        },
        //获取版本ID
        getVersionID:function(){
            return $.cookie(this.config.cookieVersionID);
        },
        //设置版本ID
        setVersionID:function(versionID,expire){
            var cookieName = this.config.cookieVersionID;
            if(cookieName === null){
                $.removeCookie(cookieName,{path:'/'});
            }else{
                $.cookie(cookieName, versionID, {expires: expire, path:'/'});
            }
        },
        //初始化视频播放相关
        initVideo:function(){
            $('body').on('click','.videolist',function(){
                var kID = $(this).attr('kid');
                var tID = $(this).attr('tid');
                var id = 'video-'+kID+'-'+tID;
                var video_div = '<div id="'+id+'" title="'+$(this).html()+'——考点视频解析">' +
                    '<iframe frameborder="0" frameborder="0" scrolling="no" width="640" height="480" ' +
                    'src="'+U('Aat/KnowledgeStudy/video?kID='+kID+'&tID='+tID)+'"></iframe>'+
                    '</div>';
                $('#kl_video').html(video_div);
                var vDialog = $('#'+id);
                vDialog.dialog({
                    modal: true,
                    height:535,
                    width:665,
                    resizable: false,
                    draggable: false,
                    close: function() {
                        vDialog.remove();
                    }
                });
            });
        },
        //检测网络连接
        connectError: function () {
            if($('.connectError').html()){
                return false;
            }
            var second = 5;
            var errorStr = '<div class="ts_box connectError">' +
                                '<i class="fa fa-bolt"></i>' +
                                '网络连接已断开！' +
                                '<p>当前页面会在<span class="spanSeconds">'+second+'</span>秒后自动刷新...</p>' +
                            '</div>';
            $('body').append(errorStr);
            var error = setInterval(function () {
                second--;
                if (second <= 0) {
                    clearInterval(error);
                    window.location.reload();
                }
                $('.spanSeconds').html(second);
            }, 1000);
        },
        /**
         * 试题纠错
         * @param testID 纠错试题ID
         * @param ifUse  是否使用答题时间的控制函数，true使用，false不使用
         * @author demo 4.11.5
         */
        correction:function(testID,ifUse){
            if(testID.indexOf('c')==0){
                alert('教师自建试题暂时不能纠错!');
                return false;
            }
            var self = this;
            if(ifUse){
                //purse_paper();
            }
            var dialog= '#correction';
            var buttons = {};
            var _saveCorrect=function(){
                var content=$('#correctval').val();
                var testID=$('#correctdata').attr('quesid');
                var typeID='';
                $("input[name='errortype']:checked").each(function(){
                    typeID+=$(this).val()+',';
                });
                if(typeID==''||content==''){
                    $('#warn').html('<div class="errorWarn">请选择错误类型,并填写错误描述！</div>');
                    $('.ui-dialog-buttonset :button').slice(0,1).removeAttr('disabled');
                    return;
                }
                $.post(U('Aat/Exercise/correct'),{'testID':testID,'correctContent':content,'typeID':typeID},function(e) {
                        if (e.status == 1) {
                            $('#warn').html('<div class="successWarn">'+e.data+'</div>');
                            var success = setInterval(function () {//提示成功，三秒后自动关闭
                                $(dialog).dialog("close");
                                clearInterval(success);
                             }, 3000);
                        }else{
                            $('.ui-dialog-buttonset :button').slice(0,1).removeAttr('disabled');
                            $('#warn').html('<div class="errorWarn">'+e.data+'</div>');
                        }
                });
            };
            buttons = {
                "确定": function () {
                    //_dialogButtonDisable(true);
                    $('.ui-dialog-buttonset :button').slice(0,1).attr('disabled','disabled');
                    $('#warn').html('<div class="connectOK">数据正在提交...</div>');
                    _saveCorrect();
                },
                '取消': function () {
                    $(this).dialog("close");
                    
                }
            };
            $(dialog).dialog({
                modal: true,
                draggable: false,
                height:286,
                width:500,
                buttons: buttons,
                open: function () {
                    var suggest = '<div id="correctdata" class="correct" quesid="'+testID+'">'+
                                      '<div>'+
                                          '<p class="errorTitle">错误类型：'+
                                              '<input type="checkbox" id="errortype1" value="0" name="errortype"><label for="errortype1">题目类型</label>'+
                                              '<input type="checkbox" id="errortype2" value="1" name="errortype"><label for="errortype2">题目答案</label>'+
                                              '<input type="checkbox" id="errortype3" value="2" name="errortype"><label for="errortype3">题目解析</label>'+
                                              '<input type="checkbox" id="errortype4" value="3" name="errortype"><label for="errortype4">题目知识点</label>'+
                                              '<input type="checkbox" id="errortype5" value="4" name="errortype"><label for="errortype5">其他</label>'+
                                          '</p>'+
                                      '</div>'+
                                      '<p class="errorTitle">错误描述：</p>'+
                                      '<div class="errorContent">'+
                                          '<textarea class="correctVal" id="correctval"></textarea>'+
                                      '</div>'+
                                      '<div id="warn"></div>'+
                                  '</div>';
                    $('#errorDoAmount').html(suggest);
                    self.initICheck();
                },
                close:function(){
                    if(ifUse){
                        //begin_paper();
                    }
                }
            });
        },
        /**
         * 试题收藏
         * @param string testID 收藏试题ID
         * @author demo 5.5.25
         */
        testCollectSave:function(testID){
            if(testID.indexOf('c')==0){
                alert('教师自建试题暂时不能收藏!');
                return false;
            }
            $.ajax({
                type: 'POST',
                cache: false,
                data: {id:testID},
                url: U('Aat/TestCollect/save'),
                success: function (e) {
                    if (e.status == 0) {
                        alert(e.data);
                    } else {
                        alert(e.data);
                    }
                }
            });
        },
        setCsrfAjaxData:function(_csrf){
            $.ajaxSetup({
                data:{'_csrf':_csrf}
            });
        }
    };
    //--------------------初始化操作------------------------------------------------------------------------------------
    //初始化AatCommon类
    AatCommon.init(commonConfig);
    //定位快捷函数
    U = function (models, vars, suffix) {
        if(typeof(suffix)!='boolean') suffix=true;
        if(typeof(vars)=='undefined') vars='';
        if(typeof(local)=='undefined') local='/Aat/';

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
    };
    var _csrf = $('#_csrf').attr('content');
    //初始化ajax全局信息
    $.ajaxSetup({
        cache:false,
        timeout:29000,//29秒超时
        data:{'_csrf':_csrf},
        error:function(){
            AatCommon.connectError();
        }
    });

//--------------------------------------------------扩展插件------------------------------------------------------------
    /**
     * 弹出框老版本，以后逐渐淘汰
     * @param options
     * @returns {boolean}
     * @author demo
     */
    $.fn.aBox = function (options) {
        var self = this;
        var _modalDiv = function (show) {
            var div = '<div id="modalDiv" style="position: absolute;top:0;left: 0;background: #000000;' +
                'filter:Alpha(opacity=60);opacity: 0.6;width: 100%;height: 100%;z-index: 98;"></div> ';
            if (show) {
                $('body').children('div').append(div);
            } else {
                $('#modalDiv').remove();
            }
        };
        if(options == 'close'){
            $(self).hide('drop', {'direction': 'up'}, 400);
            _modalDiv(false);
            return false;
        }
        var config = $.extend({}, $.fn.aBox.defaults, options);

        if(config.type == 'alert'){
            config.width = 328;
            config.height = 190;
        }
        var wWidth, wHeight, left, top;
        _modalDiv(true);
        wWidth = $(window).width();
        wHeight = $(window).height();
        left = (wWidth - config.width) / 2;
        top = (wHeight - config.height) / 2;
        top = top<0?0:top;
        $(self).css({'z-index': 100, 'position': 'fixed', 'left': left, 'top': top});
        $(self).show('drop', {'direction': 'up'}, 400, function () {
            config.open();
        });
        //窗口大小改变时调整位置
        $(window).resize(function () {
            wWidth = $(window).width();
            wHeight = $(window).height();
            left = (wWidth - config.width) / 2;
            top = (wHeight - config.height) / 2 + $('body').scrollTop();
            top = top<0?0:top;
            $(self).css({'left': left, 'top': top});
        });
        $(self).on('click',config.buttonClose,function () {
            $(self).hide('drop', {'direction': 'up'}, 400);
            _modalDiv(false);
            config.close();
        });
        if(config.type == 'alert'){
            $(self).on('click',config.buttonOk,function(){
                $(self).hide('drop', {'direction': 'up'}, 400);
                _modalDiv(false);
                config.close();
            });
        }
    };
    $.fn.aBox.defaults = {
        type: 'dialog',//dialog alert
        width: 728,
        height: 432,
        open: function () {
        },//打开页面后的回调
        close: function () {
        },//关闭回调
        buttonClose:'.an_close',
        buttonOk:'.alertOk'
    };
    /**
     * 新版弹出框
     * @param options
     * 参数options取值：
     * 数组{width:'',height:'',close:function(){}}
     * 或者hideLoading close
     * 关闭按钮：增加“close”的class
     * @author demo
     */
    jQuery.aDialog = function(options){
        var dialogID = 'dialogAat';
        var modalID = 'modalAat';
        if(options == 'hideLoading'){
            $('#'+dialogID+' .contentLoading').hide();
            return false;
        }
        var modalDiv = function (show) {
            if(show === false){
                $('#'+modalID).remove();
                return false;
            }
            var div = '<div id="'+modalID+'" style="position: absolute;top:0;left: 0;background: #000000;filter:Alpha(opacity=60);opacity: 0.6;width: 100%;height: 100%;z-index: 98;"></div> ';
            $('#wrapper').append(div);
        };
        var dialogDiv = function(show) {
            if(show === false){
                $('#'+dialogID).remove();
                return false;
            }
            var title = options.title.length>25?(options.title.substr(0,25)+'...'):options.title;
            var cenWidth = options.width-16;
            var cenHeight = options.height-64;
            var content = '<div class="contentLoading" style="margin-top:'+(cenHeight/2-20)+'px;"></div>';
            if(options.content){
                content = options.content;
            }
            var div =   '<div id="'+dialogID+'" class="dialog">'+
                            '<div class="top">'+
                                '<div class="topLeft fl"></div>'+
                                '<div class="topCen fc" style="width: '+cenWidth+'px;">'+
                                    '<span class="fl pl7">'+title+'</span>'+
                                    '<a class="fr close" href="javascript:;"></a>'+
                                '</div>'+
                                '<div class="topRight fl"></div>'+
                            '</div>'+
                            '<div class="content" style="width: '+options.width+'px;height: '+cenHeight+'px;">'+content+'</div>'+
                            '<div class="bottom">'+
                                '<div class="bottomLeft fl"></div>'+
                                '<div class="bottomCen fc" style="width: '+cenWidth+'px;"></div>'+
                                '<div class="bottomRight fl"></div>'+
                            '</div>'+
                        '</div>';
            $('#wrapper').after(div);
            var dialog, wWidth, wHeight, left, top;
            dialog = $('#'+dialogID);
            wWidth = $(window).width();
            wHeight = $(window).height();
            left = (wWidth - options.width) / 2;
            top = ((wHeight - options.height) / 2) -20;//靠上20px
            top = top<0?0:top;
            dialog.css({'z-index': 100, 'position': 'fixed', 'left': left, 'top': top})
                .show('fast', function () {
                    if(options.success){
                        options.success();
                    }
                });
            //窗口大小改变时调整位置
            $(window).resize(function () {
                wWidth = $(window).width();
                wHeight = $(window).height();
                left = (wWidth - options.width) / 2;
                top = ((wHeight - options.height) / 2) -20;//靠上20px
                top = top<0?0:top;
                dialog.css({'left': left, 'top': top});
            });
        };
        var closeDiv = function(){
            modalDiv(false);
            dialogDiv(false);
            $(window).off('scroll');
        };
        //-----------------初始化---------------------------

        if(options == 'close'){
            closeDiv();
            return false;
        }//手动关闭
        modalDiv(true);//初始化遮盖层
        dialogDiv(true);//初始化dialog
        $('#'+dialogID).unbind().on('click','.close',function(){
            closeDiv();
            if(options.close){
                options.close();
            }
        });//关闭按钮
    };
    /**
     * 选项卡
     * @param options
     */
    $.fn.aTab = function (options) {
        var config = $.extend({}, $.fn.aTab.defaults, options);
        var self = this;
        var tabs = $(self).find(config.tab);
        tabs.on('click','a', function () {
            $(this).addClass(config.activeClass);//tabNav增加选中class
            tabs.children('a').not($(this)).removeClass(config.activeClass);//tabNav其它去掉
            self.find(config.panel).children().hide();//隐藏所有panel元素
            self.find(config.panel).find($(this).attr('data')).show();
            config.afterClick($(this));
        });
    };
    $.fn.aTab.defaults = {
        tab:'.tabnav01',
        panel:'.tabPanel',
        activeClass:'this',
        afterClick:function(item){}
    };
    /**
     * 带placeholder值的input元素,在ie8中placeholder属性无效问题处理
     * @returns {boolean}
     */
    $.fn.aPlaceholder = function(){
        var config = $.extend({}, $.fn.aPlaceholder.defaults);
        var self = $(this);
        var ie6 = /msie\s6/.test(navigator.userAgent.toLowerCase());
        var ie7 = /msie\s7/.test(navigator.userAgent.toLowerCase());
        var ie8 = /msie\s8/.test(navigator.userAgent.toLowerCase());
        if(ie6 || ie7 || ie8){
            if(self.attr('type')!='text' && self.attr('type')!='password'){
                return false;
            }
            self.unbind();
            if(self.attr('type')=='text'){
                self.val(self.attr('placeholder'));
                self.on('focusin',function(){
                    if(self.val() == self.attr('placeholder')){
                        self.val('');
                    }
                })
                self.on('focusout',function(){
                    if(self.val()==''){
                        self.val(self.attr('placeholder'));
                    }
                })
            }
            if(self.attr('type')=='password'){
                var falsePwd='';
                falsePwd = 'fal'+Math.round(Math.random()*100000);
                self.hide();
                self.after('<input type="text" name="password" class="'+falsePwd+'" value="'+self.attr('placeholder')+'">');
                $('.'+falsePwd).addClass(self.attr('class')).attr('style',self.attr('style')).show();
                $('.'+falsePwd).on('focusin',function(){
                    $(this).hide().blur(function(){
                        self.focus();
                    })
                    self.css({width:$('.'+falsePwd).css('width')}).show();
                });
                self.on('focusout',function(){
                    if(self.val()==''){
                        self.hide()
                        $('.'+falsePwd).show();
                    }
                })
            }
        };
    }
    $.fn.aPlaceholder.defaults = {
        afterClick:function(item){}
    };
    /**
     * 验证类
     * 使用：
     * 1、需要验证的表单增加data-rule和data-display属性 data-rule可以|分隔多个验证
     * 2、$(表单).validate({callback:function(errors){处理代码}});
     * 回调参数errors:错误数组，可以全部输出或者输出errors[0]第一个错误
     * 注意：checkbox radio必须有name属性 相同name值只需设置一个的data-rule即可，data-display都必须设置
     * @param options
     */
    $.fn.validate = function(options){
        var config = $.extend({}, $.fn.validate.defaults, options);
        var self = this;
        var preg = {
            tel:AatCommon.preg.phone,
            email:AatCommon.preg.email,
            password:/^[a-z0-9\._-]{6,18}$/
        };
        var errors = [];
        var check = function(k,rule){
            var type = k.attr('type');//元素类型
            var tagName = k.get(0).tagName;
            var display  = k.attr('data-display')?k.attr('data-display'):'';
            switch (rule){
                case 'require':
                    if(type == 'checkbox'||type == 'radio'){//至少选择一个
                        var name = k.attr('name');
                        if(name){//存在多个checkbox或者radio只要选择一个就可以
                            if(!self.find('[name='+name+']').is(':checked')){
                                errors.push(display+'必须选择');
                                return false
                            }
                        }else{//如果没有name值，验证每一个checkbox和radio
                            if(!k.is(':checked')){
                                errors.push(display+'必须选择');
                                return false;
                            }
                        }

                    }else if(tagName == 'SELECT'){
                        if(!k.val()|| k.val()==0){
                            errors.push(display+'必须选择');
                            return false;
                        }
                    }else{
                        if(k.val().replace(/(^\s*)|(\s*$)/g,'')===''){
                            errors.push(display+'不能为空');
                            return false;
                        }
                    }
                    break;
                case 'userName':
                    if(!preg.email.test(k.val())&&!preg.tel.test(k.val())){
                        errors.push(display+'必须是手机号或邮箱');
                        return false;
                    }
                    break;
                case 'password':
                    if(!preg.password.test(k.val())){
                        errors.push(display+'必须是6-18位数字，字母，-，_，.');
                    }
                    break;
                default :
                    alert('data-rule错误');
            }
        };
        $('[data-rule]',self).each(function(i,item){
            var rules = $(item).attr('data-rule').split('|');//验证类型
            $.each(rules,function(j,rule){
                return check($(item),rule);//如果是false，不再重复验证本字段
            });
        });
        config.callback(errors);
    };
    $.fn.validate.defaults = {
        callback: function (errors) {
        }
    };
})();
