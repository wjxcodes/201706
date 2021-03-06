<?php if (!defined('THINK_PATH')) exit();?><div class="top">
    <div class="top_left fl"></div>
    <div class="top_cen fc" style="font: '微软雅黑';">用户信息完善</div>
    <div class="top_right fr"></div>
</div>
<div class="zj">
    <div class="bd">
        <div id="wanshan">
            <!--用户名-->
            <div class="inputs sUserName" style="display: none;">
                <input id="userName" type="text" value="" class="wbk01" placeholder="用户名 手机号或邮箱 只能修改一次">
            </div>
            <div class="inputs sUserName" style="display: none;">
                <input id="verify" type="text" value="" class="wbk01" placeholder="图形验证码" style="width:165px;">
                <img class="verification-image" id="verifyImg" src="<?php echo U('Index/Index/verify');?>" alt="点击刷新验证码" style="cursor:pointer;float: right;" height="36" width="95" align="absmiddle" border="0">
            </div>
            <div class="inputs sUserName" style="display: none;">
                <input id="phoneNum" type="text" value="" class="wbk01" placeholder="短信验证码" style="width:132px;">
                <input type="button" value="获取验证码" id="getPhoneCode" class="nicknameC getPhoneCode">
            </div>
            <!--姓名-->
            <div class="inputs sRealName" style="display: none;">
                <input id="realName" type="text" value="" class="wbk01" placeholder="您的姓名">
            </div>
            <!--昵称-->
            <div class="inputs sNickname" style="display: none;">
                <input id="nickname" type="text" value="" class="wbk01" placeholder="您的昵称，保存后不可修改" style="width:180px;">
                <input type="button" value="系统昵称" id="getNick" class="nicknameB">
            </div>
            <!--地区-->
            <div class="pb10 sAreaID" style="display: none;height:43px;">
                <div id="mc" style="position:relative;cursor:pointer;height:20px; width:105px; float:left;">
                    <input class="wbk02 showinput" type="text" value="省/直辖市" data="省/直辖市"
                           style="position:absolute; left:0;width: 97px;"/>
                    <ul id="option2" no="0" class="Option provinceUL"
                        style="position:absolute; left: 0; top:40px; display:none; z-index:10">
                    </ul>
                </div>
                <div id="mc2" style=" position:relative;cursor:pointer;height:20px;width:89px; float:left;" >
                    <input class="wbk02 showinput" type="text" value="市/区" data="市/区"
                           style="position:absolute; left:0;width:81px; float:left;"/>
                    <ul class="Option cityUL" no="1"
                        style="position:absolute; left:-105px; top:40px; display:none; z-index:10">
                    </ul>
                </div>
                <div id="mc3" style=" position:relative;cursor:pointer;height:20px;width:80px; float:left;" >
                    <input class="wbk02 showinput" type="text" value="区/县" data="区/县"
                           style="position:absolute; left:0;width:81px; float:left;"/>
                    <ul class="Option countyUL" no="2"
                        style="position:absolute; left:-194px; top:40px; display:none; z-index:10">
                    </ul>
                </div>
            </div>
            <!--学校-->
            <div class="mb10 sSchoolID" style="display: none;position:relative;cursor:pointer;height:40px;width:100px;">
                <input class="wbk02" id="school" type="text" value="请选择您就读的学校" data="请选择您就读的学校"
                       style="position:absolute; left:0; float:left; width:275px"/>
                <ul class="Option Option02 schoolUL" no="3"
                    style="position:absolute; left: 0; top:40px; display:none; z-index:9">
                </ul>
            </div>
            <!--年级-->
            <div class="mb10 sGradeID" style="display: none;position:relative;cursor:pointer;height:40px;width:100px;">
                <input class="wbk02" id="grade" type="text" value="请选择年级" data="请选择年级"
                       style="position:absolute; left:0; float:left; width:275px"/>
                <ul class="Option Option02 gradeUL" style="position:absolute; left: 0; top:40px; display:none; z-index:8">
                </ul>
            </div>
            <!--版本-->
            <div class="mb10 sVersion" style="display: none;position:relative;cursor:pointer;height:40px;width:100px;">
                <input class="wbk02" id="version" type="text" value="请选择使用版本" data="请选择使用版本"
                       style="position:absolute; left:0; float:left; width:275px"/>
                <ul class="Option versionUL" style="position:absolute; left: 0; top:40px; display:none; z-index:8">
                    <li aid="1">高考冲刺版</li>
                    <li aid="2">同步学习版</li>
                </ul>
            </div>

            <div class="errorMsg" style="color: #b40504;margin-top: 5px;display: none;"></div>
            <div class="submitMsg" style="color: #000000;margin-top: 5px;display: none;">正在提交数据请稍候...</div>
            <div class="actions">
                <input type="button" id="submit01" value="完 成">
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>
<div class="bot">
    <div class="bot_left fl"></div>
    <div class="bot_cen fc"></div>
    <div class="bot_right fr"></div>
</div>
<script type="text/javascript">
$(document).ready(function () {
    $.AatAddUserInfo = {
        c: $('.xxws'),
        saveData:{},
        ifLock:'',
        pRetainTime:0,
        sendTimeStr:'',
        _showError:function(error){
            this.c.find('.errorMsg').html(error).show().effect('shake');
        },
        _getUserName:function(){
            var userName = this.c.find('#userName').val();
            //需要输入用户名
            var emailReg = AatCommon.preg.email;
            var phoneReg = AatCommon.preg.phone;
            if(!emailReg.test(userName)&&!phoneReg.test(userName)){
                this._showError('请输入手机或邮箱作为您的账户名！');
                return false;
            }
            return userName;
        },
        _getCodeEvent:function(){
            //切换验证码
            $(document).on('click','#verifyImg',function(){
                var time = new Date().getTime();
                $(this).attr('src',U('Index/Index/verify')+'?'+time);
            });

            //获取短息验证码
            $(document).on('click','.getPhoneCode',function(){
                var imgCode = $('#verify').val();
                var phoneNum = $('#phoneNum').val();
                var userName = $.AatAddUserInfo.c.find('#userName').val();
                //需要输入用户名
                var emailReg = AatCommon.preg.email;
                var phoneReg = AatCommon.preg.phone;
                if(!emailReg.test(userName)&&!phoneReg.test(userName)){
                    $.AatAddUserInfo._showError('请输入手机或邮箱作为您的账户名！');
                    return false;
                }
                if(imgCode==''){
                    //提示
                    $.AatAddUserInfo._showError('请输入图片验证码！');
                    return false;
                }
                var pregStr=/^[0-9]{4}$/;
                if(!pregStr.test(imgCode)){
                    $.AatAddUserInfo._showError('图片验证码只能是4位数字！');
                    return false;
                }
                $.AatAddUserInfo._getCodeNum(userName,imgCode);
            });
        },
        //发送短信验证码
        _getCodeNum:function(sendNum,imgCode){
            var self = this;
            if(self.ifLock != ''){//防止重复点击
                return false;
            }
            self.ifLock = 'sendCode';
            self._showError('发送中请稍候...');
            $.post(U('User/Index/sendCodeNum'),{'sendNum':sendNum,'imgCode':imgCode,'rand':Math.random()},function(e){
                self.ifLock = '';
                if(e.status==1){
                    $.AatAddUserInfo._showError('发送成功，请查收。');
                    $.AatAddUserInfo.showLeaveTime('getPhoneCode');
                }else{
                    $.AatAddUserInfo._showError(e.data);
                    return false;
                }
            });
        },
        //显示倒计时
        showLeaveTime:function(idName){
            var second=60;
            if($.AatAddUserInfo.pRetainTime!=0){second=$.AatAddUserInfo.pRetainTime;}
            $("#"+idName).val('重新发送('+second+')');
            $('#getPhoneCode').removeClass('getPhoneCode');

            $.AatAddUserInfo.sendTimeStr = setInterval(function () {
                second--;
                if (second <= 0) {
                    $.AatAddUserInfo.clearLeaveTime(idName);
                    return false;
                }
                $.AatAddUserInfo.pRetainTime = second;
                $("#"+idName).val('重新发送('+second+')');
            }, 1000);
        },
        //清除倒计时程序
        clearLeaveTime:function(idName){
            $.AatAddUserInfo.pRetainTime=0;
            $("#"+idName).val('获取验证码');
            $('#getPhoneCode').addClass('getPhoneCode');
            clearInterval($.AatAddUserInfo.sendTimeStr);
        },
        _getRealName:function(){
            var realName = this.c.find('#realName').val();
            var realNameLength = realName.replace(/[^\x00-\xff]/g, 'xxx').length;//一个汉字3个字节utf8
            if (realNameLength < 6||realNameLength > 30) {
                this._showError('姓名长度必须大于2个汉字且小于10个汉字！');
                return false;
            }
            return realName;
        },
        _getNickname:function(){
            var nickname = this.c.find('#nickname').val();
            if(nickname==''){
                this._showError('昵称不能为空！');
                return false;
            }
            var preg=/^[\u4e00-\u9fa5a-zA-Z0-9]+$/;
            if(!preg.test(nickname)){
                this._showError('昵称只允许汉字，字母和数字组合！');
                return false;
            }
            var nicknameLength = nickname.replace(/[^\x00-\xff]/g, 'xxx').length;//一个汉字3个字节utf8
            if (nicknameLength < 3||nicknameLength > 15) {
                this._showError('昵称长度必须大于1个汉字且小于5个汉字！');
                return false;
            }
            return nickname;
        },
        _getAreaID:function(showError){
            var areaID = this.c.find('.sAreaID li[isEnd=1][class=this]').attr('aid');
            if(isNaN(areaID)||areaID==0){
                if(showError){
                    this._showError('请选择所在地区！');
                }
                return false;
            }
            return areaID;
        },
        _getSchoolID:function(){
            var schoolID = this.c.find('.schoolUL li[class=this]').attr('aid');
            if(isNaN(schoolID)||schoolID==0){
                this._showError('请选择所在学校！');
                return false;
            }
            return schoolID;
        },
        _getGradeID:function(){
            var gradeID = this.c.find('.gradeUL li[class=this]').attr('aid');
            if(isNaN(gradeID)||gradeID==0){
                this._showError('请选择所处年级！');
                return false;
            }
            return gradeID;
        },
        _getVersion:function(){
            var version = this.c.find('.versionUL li[class=this]').attr('aid');
            if(version!=1&&version!=2){
                this._showError('请选择高考版或者同步版！');
                return false;
            }
            return version;
        },
        _fixCss:function(e){
            e.css({
                'border-bottom-style':'solid',
                'border-radius': '3px',
                'background-position': 'right -106px',
                'z-index': '1'
            });
        },
        _initWbk02: function () {
            var c = this.c;
            var self = this;
            c.find('.showinput').attr('readonly', 'readonly');
            c.find('.Option').on('mouseover mouseout', 'li[class!=this]', function () {
                $(this).toggleClass('on');
            });
            //点击input框
            c.on('click', '.wbk02', function () {
                if ($(this).next('ul').is(':hidden')) {
                    c.find('.wbk02').each(function () {
                        self._fixCss($(this));
                        $(this).next('ul').hide();
                    });
                    $(this).css({
                        'border-bottom-style': 'none',
                        'border-radius': '3px 3px 0px 0px',
                        'background-position': 'right -155px',
                        'z-index': '10'
                    });
                    $(this).next('ul').show();
                } else {
                    self._fixCss($(this));
                    $(this).next('ul').hide();
                }
            });
            //点击li元素
            c.find('.Option').on('click', 'li', function () {
                        var _this = $(this);
                        if (_this.attr('data') == '0') {
                            //默认“请选择”不反应
                            return false;
                        }
                        var no = _this.parent().attr('no');
                        //选择前面后面需要重置
                        c.find('.Option[no]').map(function () {
                            if ($(this).attr('no') > no) {
                                $(this).html('');
                                $(this).prev().val($(this).prev().attr('data'));
                            }
                        });
                        var current = _this.parent().prev();
                        current.val(_this.html());//当前input赋值当前点击的地区
                        self._fixCss(current);
                        _this.siblings('li').removeClass('this');//取消所有li元素this选中css
                        _this.addClass('this');//选中css
                        _this.parent().hide();//关闭当前UL
                        if (_this.attr('isEnd') == 0) {
                            //不是最后一级
                            $.post(U('Default/ajaxArea'), {'id': _this.attr('aid'),times:Math.random()}, function (e) {
                                if (e.status == 1) {
                                    var next = '', isEnd;
                                    $.each(e.data, function (i, k) {
                                        isEnd = 0;
                                        if (k['Last'] == 1) {
                                            isEnd = 1;
                                        }
                                        next += '<li aid="' + k['AreaID'] + '" isEnd="' + isEnd + '">' + k['AreaName'] + '</li>';
                                    });
                                    _this.parent().parent().next().find('.Option').html(next);//填充下一级地区并显示
                                    _this.parent().parent().next().find('input').show().trigger('click');
                                } else {
                                    _this.parent().parent().next().find('input').hide();
                                }
                            });
                        } else if (_this.attr('isEnd') == 1) {
                            //如果是最后一级的地区
                            _this.parent().parent().next().find('.wbk02').hide();
                        }
                    }
            );
        },
        _initArea:function(){
            var c = this.c;
            var initProvince = function(){
                $.post(U('Default/ajaxArea'), {'id': 0,times:Math.random()}, function (e) {
                    if (e.status == 1) {
                        var province = '',isEnd;
                        $.each(e.data, function (i, k) {
                            isEnd = 0;
                            if (k['Last'] == 1) {
                                isEnd = 1
                            }
                            province += '<li aid="' + k['AreaID'] + '" isEnd="' + isEnd + '">' + k['AreaName'] + '</li>';
                        });
                        c.find('.provinceUL').html(province);
                    }
                });
            };

            //初始化省份数据
            initProvince();
        },
        _initSchool:function(){
            var c = this.c;
            var schoolUL = c.find('.schoolUL');
            var self = this;
            c.on('click', '#school', function () {
                //为空的则请求远程学校数据
                var areaID = self._getAreaID();
                if(areaID === false){
                    schoolUL.html('<li data="0">请先选择地区！</li>');
                    return false;
                }
                var schoolStr = '';
                $.post(U('Default/ajaxSchool'), {'id': areaID,times:Math.random()}, function (e) {
                    if (e.status == 1) {
                        $.each(e.data, function (i, k) {
                            schoolStr += '<li aid="' + k['SchoolID'] + '">' + k['SchoolName'] + '</li>';
                        });
                        schoolUL.html(schoolStr);
                    } else {
                        schoolUL.html('<li data="0">'+ e.data+'</li>');
                    }
                });
            });
        },
        _initGrade:function(){
            var c = this.c;
            $.post(U('Default/ajaxGrade'),{times:Math.random()}, function (e) {
                if (e.status == 1) {
                    var gradeStr = '';
                    $.each(e.data, function (i, k) {
                        gradeStr += '<li data="0" style="text-align:center;">' + k.grade_name + '</li>';
                        $.each(k.sub, function (j, m) {
                            gradeStr += '<li aid="' + m.grade_id + '">' + m.grade_name + '</li>';
                        });
                    });
                    c.find('.gradeUL').html(gradeStr);
                } else {
                    c.find('.gradeUL').html('<li data="0">'+ e.data+'</li>');
                }
            });
        },
        //总的显示框架
        displayNeed: function(){
            var c = this.c;
            var self =this;
            var needArray = c.attr('data').split(',');
            $.each(needArray,function(i,k){
                //显示需要补充的用户输入框
                c.find('.s'+k).show();
                //初始化不同的数据
                if(k == 'AreaID'){
                    self._initArea();
                }
                if(k == 'SchoolID'){
                    self._initSchool();
                }
                if(k == 'GradeID'){
                    self._initGrade();
                }
            });
            this._initWbk02();
        },
        //保存操作
        save: function(){
            var self = this;
            var c = this.c;
            c.on('click', '#submit01', function () {
                //验证
                var pass = true;//默认通过
                var needArray = c.attr('data').split(',');
                $.each(needArray,function(i,k){
                    if(k == 'UserName') {
                        var userName = self._getUserName();
                        if(userName === false){
                            pass = false;
                            return false;//跳出循环
                        }
                        var codeNum = $('#phoneNum').val();
                        self.saveData.userName = userName;
                        self.saveData.phoneCode = codeNum;
                        self.saveData.emailCode = codeNum;
                    }
                    if(k == 'RealName') {
                        var realName = self._getRealName();
                        if(realName === false){
                            pass = false;
                            return false;
                        }
                        self.saveData.realName = realName;
                    }
                    if(k == 'Nickname'){
                        var nickname = self._getNickname();
                        if(nickname === false){
                            pass = false;
                            return false;
                        }
                        self.saveData.nickname = nickname;
                    }
                    if(k == 'AreaID') {
                        var areaID = self._getAreaID(true);
                        if(areaID === false){
                            pass = false;
                            return false;
                        }
                        self.saveData.areaID = areaID;
                    }
                    if(k == 'SchoolID') {
                        var schoolID = self._getSchoolID();
                        if(schoolID === false){
                            pass = false;
                            return false;
                        }
                        self.saveData.schoolID = schoolID;
                    }
                    if(k == 'GradeID') {
                        var gradeID = self._getGradeID();
                        if(gradeID === false){
                            pass = false;
                            return false;
                        }
                        self.saveData.gradeID = gradeID;
                    }
                    if(k == 'Version') {
                        var version = self._getVersion();
                        if(version === false){
                            pass = false;
                            return false;
                        }
                        self.saveData.version = version;
                    }
                });
                if(pass === true){
                    var submitMsg = c.find('.submitMsg');
                    var errotMsg = c.find('.errorMsg');
                    errotMsg.hide();
                    submitMsg.show();
                    var updateData = self.saveData;
                    $.post(U('AddUserInfo/save'), {data:updateData,times:Math.random()}, function (e) {
                        submitMsg.hide();
                        if (e.status == 1) {
                            //关闭补充信息的对话框
                            $('.xxws').aBox('close');
                            if(e.data._csrf!=''){
                                //更新成功更新本地csrf
                                $('#_csrf').attr('content', e.data._csrf);
                                AatCommon.setCsrfAjaxData(e.data._csrf);
                            }
                            if(e.data.UserName!=''){
                                $('.userid').eq(0).html(e.data.UserName);
                            }
                            AatCommon.setMsg('您已成功提交个人资料！');
                            AatCommon.showMsg();
                        } else {
                            self._showError(e.data);
                        }
                    });
                }
            });
        },
        getNickname:function(){
            $(document).on('click','#getNick',function(){
                $.post(U('AddUserInfo/getNickname'),{times:Math.random()},function(e){
                    $('#nickname').val(e.data);
                });
            })
        },
        //初始化
        init: function () {
            this.displayNeed();
            this.save();
            this.getNickname();
            this._getCodeEvent();
            $('input[placeholder]:visible').placeholder();
        }
    };
    $.AatAddUserInfo.init();
});

</script>