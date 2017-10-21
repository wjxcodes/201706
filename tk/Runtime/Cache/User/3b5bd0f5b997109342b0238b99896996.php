<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<meta name="renderer" content="webkit">
<meta id="_csrf" name="_csrf" content="<?php echo ($_csrf); ?>">
<title><?php echo ($pageName); ?> - <?php echo ($config["IndexName"]); ?></title>
<meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
<meta name="description" content="<?php echo ($config["Description"]); ?>" />
<link href="/Public/newAat/css/common.css<?php echo C(WLN_UPDATE_FILE_DATE);?>" rel="stylesheet" type="text/css"/>
<!--jquery-->
<script type="text/javascript" src="/Public/plugin/jquery-1.11.1.min.js<?php echo C(WLN_UPDATE_FILE_DATE);?>"></script>
<script type="text/javascript" src="/Public/plugin/jquery.cookie.js<?php echo C(WLN_UPDATE_FILE_DATE);?>"></script>
<!--模版引擎-->
<script type="text/javascript" src="/Public/plugin/artTemplate-3.0.3.js<?php echo C(WLN_UPDATE_FILE_DATE);?>"></script>
<!--jquery-ui-->
<link type="text/css" href="/Public/plugin/jquery-ui/jquery-ui.min.css<?php echo C(WLN_UPDATE_FILE_DATE);?>" rel="stylesheet"/>
<script type="text/javascript" src="/Public/plugin/jquery-ui/jquery-ui.min.js<?php echo C(WLN_UPDATE_FILE_DATE);?>"></script>
<script type="text/javascript" src="/Public/plugin/jquery.ui.touch-punch.min.js<?php echo C(WLN_UPDATE_FILE_DATE);?>"></script>
<!--icheck-->
<link href="/Public/plugin/icheck/minimal/blue.css<?php echo C(WLN_UPDATE_FILE_DATE);?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="/Public/plugin/icheck.min.js<?php echo C(WLN_UPDATE_FILE_DATE);?>"></script>
<!--slimscroll-->
<script type="text/javascript" src="/Public/plugin/jquery.slimscroll.min.js<?php echo C(WLN_UPDATE_FILE_DATE);?>"></script>
<!--placeholder-->
<script type="text/javascript" src="/Public/plugin/jquery.placeholder.js<?php echo C(WLN_UPDATE_FILE_DATE);?>"></script>
<!--[if lte IE 6]>
<script type="text/javascript" src="/Public/plugin/png.js<?php echo C(WLN_UPDATE_FILE_DATE);?>"></script>
<script>DD_belatedPNG.fix('input,a,img,div,span');</script>
<![endif]-->
<script type="text/javascript">
    //AatCommon类配置文件
    commonConfig = {
        urlDepartment:'<?php echo C(URL_PATHINFO_DEPR);?>',
        isSub:0,
        appUrl:'/',
        groupName:'<?php echo (MODULE_NAME); ?>',
        cookiePrefix: '<?php echo C(WLN_AAT_USER_AUTH_KEY);?>',
        cookieUserID:'<?php echo C(WLN_AAT_USER_AUTH_KEY);?>_ID',
        cookieUsername:'<?php echo C(WLN_AAT_USER_AUTH_KEY);?>_USER',
        cookieUserCode:'<?php echo C(WLN_AAT_USER_AUTH_KEY);?>_CODE',
        cookieSubjectID:'<?php echo C(WLN_AAT_USER_AUTH_KEY);?>_SUBJECTID',
        cookieVersionID:'<?php echo C(WLN_AAT_USER_AUTH_KEY);?>_VERSIONID',
        cookieIndexMsg:'<?php echo C(WLN_AAT_USER_AUTH_KEY);?>_INDEXMSG'
    };
    //artTemplate配置开始结束标签避免和Thinkphp模版标签冲突
    template.config('openTag','{%');
    template.config('closeTag','%}');
</script>
<script type="text/javascript" src="/Public/newAat/js/common.js"></script>

</head>

<body>
<div id="wrapper">
<div id="leftsub">
    <div class="leftsub_box">
        <div id="xk_box">
            <ul class="xk_list">

            </ul>
        </div>
        <div id="cd_box">
            <div class="groove-hor"></div>
            <ul class="cd_list">
                <li><a href="<?php echo U('Aat/PersonalReport/index');?>"><span class="cd_ico01 fl"></span><span class="fl cd_text">学情评估</span></a></li>
                <li><a href="<?php echo U('Aat/MyExercise/index');?>"><span class="cd_ico02 fl"></span><span class="fl cd_text">我的练习</span></a></li>
                <li><a href="<?php echo U('Aat/MyHomework/index');?>"><span class="cd_ico02 fl"></span><span class="fl cd_text">我的作业</span></a></li>
            </ul>
        </div>
    </div>
</div>
<div class="leftsub_an02" style="position: fixed;z-index: 95;">
    <a class="leftsub_an_off" href="javascript:;"></a>
    <a class="leftsub_an_no" style="display:none;" href="javascript:;"></a>
</div>
<div id="header" style="position: fixed;z-index: 90;">
    <div class="head_logo mc">
        <img src="/Public/newAat/images/logo02.png"/>
    </div>
    <div class="head_right">
        <div class="an_shouye fr"><a href="<?php echo U('Aat/Default/index');?>"></a></div>
        <div class="phoneapp02 fr"><a href="javascirpt:alert('请下载提分APP端！')" title="请下载提分APP端!"></a></div>
        <!--登录后显示首页用户登录信息-->
        <div class="dlxx02 dlxx fr pr15 pl15">
            <span class="userid fl pr5" style="position: relative;">加载中...</span><span class="ico_jt01 fl" style="position: relative;"></span>
            <div id="dlxx_box02" style="display:none;">
                <ul>
                    <li class="li01"><a href="<?php echo U('User/Aat/index');?>"><span class="ico_01 dlxx_ico"></span><span class="text">个人中心</span></a>
                    </li>
                    <!--<li class="li02"><a href="#"><span class="ico_02 dlxx_ico"></span><span class="text">购买续费</span></a>-->
                    <!--</li>-->
                
                    <li class="li04"><a href="javascript:;" class="user_logout"><span class="ico_04 dlxx_ico"></span><span class="text">退出登陆</span></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div id="dialogCheckSubject" title="切换学科" style="display:none;">
    <p id="checkSubject" style="padding-top:15px;"></p>
</div>
<script type="text/javascript">
var redirect = '';
$(document).ready(function(){
    var AatHeader = {
        userName : '',
        init:function(){
            this.userName = AatCommon.getUserName();
            this.exEffectUser(this.userName);
            //左侧选择学科
            this.exEffectLeft();
            //顶部固定不动
            this.exEffectTop();
        },
        exEffectUser:function(userName){
            //截取用户昵称或者用户名
            var userNameLength = userName.replace(/[^\x00-\xff]/g, 'xx').length;
            if(userNameLength>20){
                userName = userName.substring(0,17)+'...';
            }else{
                var left = (20-userNameLength)/2*5;
                $('#header .userid').css('left',left);
                $('#header .userid').next().css('left',left);
            }
            $('#header .userid').html(userName);
            $('#header .dlxx02').stop().hover(
                    function () {
                        $('#header .ico_jt01').switchClass('ico_jt01', 'ico_jt02', 10);
                        $('#header #dlxx_box02').show('blind', 200);
                    },
                    function () {
                        $('#header .ico_jt02').switchClass('ico_jt02', 'ico_jt01', 10);
                        $('#header #dlxx_box02').hide('blind', 200);
                    }
            );
            $('#header .user_logout').click(function(){
                $.get(U('Aat/Default/logout'),{times:Math.random()},function(e){
                    if(e.status==1){
                        AatCommon.setMsg(e.data);
                        window.location = U('User/Index/passport');
                    }else{
                        alert('退出失败请重试！');
                    }
                })
            });
        },
        exEffectLeft:function(){
            var self = this;
            //学科添加
            $.post(U('Aat/Default/ajaxSubject'),{times:Math.random()},function(e){
                var subjectID = AatCommon.getSubjectID();
                var str = '';
                if(e.status == 1){
                    $.each(e.data,function(i,k){
                        var class_this = '';
                        if(k.subject_id == subjectID){
                            class_this = 'xk_this';
                        }
                        str += '<li id="subject_'+ k.subject_id+'" data="'+ k.subject_id+'" class="'+class_this+'"><a>'+ k.subject_name+'</a></li>';
                    });
                }else{
                    str = '<li id="subject_12" data="12"><a>语文</a></li>'+
                            '<li id="subject_13" data="13"><a>数学</a></li>'+
                            '<li id="subject_14" data="14"><a>英语</a></li>'+
                            '<li id="subject_15" data="15"><a>物理</a></li>'+
                            '<li id="subject_16" data="16"><a>化学</a></li>'+
                            '<li id="subject_17" data="17"><a>生物</a></li>'+
                            '<li id="subject_18" data="18"><a>政治</a></li>'+
                            '<li id="subject_19" data="19"><a>历史</a></li>'+
                            '<li id="subject_20" data="20"><a>地理</a></li>';
                }
                $('#leftsub .xk_list').html(str);
            });
            var left = $('#leftsub');
            //left高度
            left.height($(window).height()<575?575:$(window).height());
            $('#xk_box').height($(window).height()-190);
            $('.xk_list').slimScroll({
                height:'auto',
                size:'5px',
                wheelStep:15,
                touchScrollStep:15
            });
            $(window).resize(function(){
                if($(window).height()<575){
                    left.height(575);
                }else{
                    left.height($(window).height());
                }
                $('#xk_box').height($(window).height()-190);
                $('.xk_list').slimScroll({
                    height:'auto',
                    size:'7px',
                    wheelStep:5,
                    touchScrollStep:200,
                    distance:'2px',
                    railVisible:true,   //滚动条背景轨迹,默认false
                    railOpacity:1    //滚动条背景轨迹透明度,默认0.2
                });
            });
            // $(window).scroll(function(){
            //     left.css('top',$(window).scrollTop());
            // });
            var leftsubAn  = $('.leftsub_an02');
            $('.leftsub_an_no').click(function(){
                left.toggle('slide',{ direction: "left" }, 500);
                leftsubAn.toggleClass('effect_left',500);
                $(this).hide();
                $('.leftsub_an_off').show();

            });
            $('.leftsub_an_off').on('click',function(){
                left.toggle('slide',{ direction: "left" }, 500);
                leftsubAn.toggleClass('effect_left',500);
                $(this).hide();
                $('.leftsub_an_no').show();
            });
            var subject_id = AatCommon.getSubjectID();
            $('#subject_'+subject_id).addClass('xk_this');
            $('.xk_list').on('click','li',function(){
                var newSubjectID=$(this).attr('data');
                if(typeof(redirect) === 'string' && redirect !== ''){
                    if(redirect.indexOf('/') !== 0){
                        redirect = '/'+redirect;
                    }
                    window.location.href = redirect;
                    return false;
                }else if($('#test_submit').html()!=undefined || $('.an_jc').html()!=undefined){//首先判断是否跳转到首页
                    self.checkSubject('1',newSubjectID);
                }else if($('.zt_title .fl').html().indexOf('测试报告')!=-1){//其次判断是否跳转到我的练习页面
                    self.checkSubject('2',newSubjectID);
                }else{//最后判断是否刷新本页面
                    $('.xk_list li').removeClass('xk_this');
                    $(this).addClass('xk_this');
                    AatCommon.setSubjectID(newSubjectID);
                    location.reload();
                }
            });
            //点击左侧下面的链接，学科如果未选择，则提示
            $('.cd_list').on('click','li',function(){
                if(typeof(AatCommon.getSubjectID()) == 'undefined'){
                    AatCommon.setMsg('请先选择学科！');
                    AatCommon.showMsg();
                    return false;
                }
            });
        },
        exEffectTop:function(){
            $(window).scroll(function(){
                // $('#header').css('top',$(window).scrollTop());
                // $('.leftsub_an02').css('top',$(window).scrollTop());
            });
        },
//在与测试相关页面切换学科时的提示及操作
        checkSubject:function(style,newSubjectID){
            var dialog= '#dialogCheckSubject';
            var buttons = {};
            var contentStr='';//提示信息
            var hrefStr='';//跳转路径
            if(style=='1'){
                if($('.an_cen .fl').html()=='查看解析'){
                    contentStr='您正在查看测试的答案和解析，切换学科将跳转到首页！您可以进行其他测试';
                }else{
                    contentStr='您正在试题操作界面，切换学科将跳转到首页！您可以进行其他测试';
                }
                hrefStr='Default/index';
            }else{
                contentStr='您正在测试分析界面，切换学科将跳转到我的练习！';
                hrefStr='MyExercise/index';
            }
            buttons = {
                "切换": function () {//点击确定跳转到首页
                    $('.xk_list li').removeClass('xk_this');
                    $('#subject_'+newSubjectID).addClass('xk_this');
                    AatCommon.setSubjectID(newSubjectID);
                    location.href=U(hrefStr);
                },
                '取消': function () {//点击取消继续做题
                    $(this).dialog("close");
                }
            };
            $(dialog).dialog({
                modal: true,
                draggable: false,
                height:150,
                width:500,
                buttons: buttons,
                open: function () {
                    $('#checkSubject').html(contentStr);
                }
            });
        }
    };
    AatHeader.init();
});

</script>
<div class="w980 mc pt90 pb20 userCenter">
    <div class="box02 mb20">
        <div class="zt_title"><div class="fl pt20 pl20">个人中心</div></div>
        <div class="title02">基本信息</div>
        <div class="title03">
            <div class="tx_box">
                <img id="userPic" alt="头像" src=""/>
                <P><a href="<?php echo U('User/Aat/pic');?>">修改头像</a></P>
            </div>
            <ul class="zl_nr">
                <li><h5>用户账号：</h5><div class="zl_nr_nr" id="userName"></div></li>
                <li><h5>用户学号：</h5><div class="zl_nr_nr" id="orderNum"></div></li>
                <li>
                    <h5>使用版本：</h5>
                    <input type="button" id="version1" class="version" value="高考冲刺版" title="点击切换高考冲刺版系统" data="1">
                    <input type="button" id="version2" class="version" value="同步学习版" title="点击切换同步学习版系统" data="2">
                    <span class="versionError" style="display:none;"></span>
                    
                </li>
                <li class="teachBook" style="display:none;">
                    <h5>使用教材：</h5>
                    <input type="button" class="setChaper" value="设置教材" title="点击设置教材同步章节" />
                </li>
                <li>
                    <h5>用户级别：</h5><div class="zl_nr_nr" id="chargeMode"></div>
                    <h5>剩余点值：</h5><div class="zl_nr_nr" id="chargeLeave"></div>
                </li>
                <li>
                    <h5>登录时间：</h5><div class="zl_nr_nr" id="loginTime"></div>
                    <h5>登录地址：</h5><div class="zl_nr_nr" id="loginIP"></div>
                </li>
            </ul>
        </div>
        <div class="title02">
            <div class="fl">个人资料</div>
            <a id="change" class="fl" href="javascript:;" style="color: #0081CB;font-size: 16px;">修改个人信息</a>
        </div>
        <div class="title03">
            <div class="zl_nr">
                <ul class="show">
                    <li><h5>真实姓名：</h5><div class="zl_nr_nr"><span id="realName"></span></div></li>
                    <li><h5>当前密码：</h5><div class="zl_nr_nr"><span>******</span></div></li>
                    <li><h5>所在地区：</h5><div class="zl_nr_nr"><span id="areaName"></span></div></li>
                    <li><h5>所在学校：</h5><div class="zl_nr_nr"><span id="schoolName"></span></div></li>
                    <li><h5>当前年级：</h5><div class="zl_nr_nr"><span id="gradeName"></span></div></li>
                    <li><h5>手机号码：</h5><div class="zl_nr_nr"><span id="phone"></span><span id="operationPhone"></span></div></li>
                    <li><h5>常用邮箱：</h5><div class="zl_nr_nr"><span id="email"></span><span id="operationEmail"></span></div></li>
                </ul>
                <ul class="edit" style="display: none;">
                    <li>
                        <label for="realNameVal">真实姓名：</label>
                        <input class="wbk01" id="realNameVal" type="text" value=""/>
                        <span class="info">* 必填</span>
                    </li>
                    <li>
                        <label for="oldPasswordVal">当前密码：</label>
                        <input class="wbk01" id="oldPasswordVal" type="password" value=""/>
                        <span class="info">不修改密码请留空</span>
                    </li>
                    <li>
                        <label for="passwordVal">新设密码：</label>
                        <input class="wbk01" id="passwordVal" type="password"/>
                        <span class="info">不修改密码请留空</span>
                    </li>
                    <li>
                        <label for="passwordRepeat">重复密码：</label>
                        <input class="wbk01" id="passwordRepeat" type="password"/>
                        <span class="info">不修改密码请留空</span>
                    </li>
                    <li>
                        <label for="areaVal">所在地区：</label>
                        <span id="areaVal"></span>
                        <span class="info">* 必填</span>
                    </li>
                    <li>
                        <label for="schoolVal">所在学校：</label>
                        <span id="schoolVal"></span>
                        <span class="info">* 必填</span>
                    </li>
                    <li>
                        <label for="gradeVal">当前年级：</label>
                        <span id="gradeVal"></span>
                        <span class="info">* 必填</span>
                    </li>
                    <!--<li>
                        <label for="phoneVal">手机号码：</label>
                        <input class="wbk01" id="phoneVal" type="text" value=""/>
                        <span class="info">* 必填</span>
                    </li>
                    <li>
                        <label for="emailVal">常用邮箱：</label>
                        <input class="wbk01" id="emailVal" type="text" value=""/>
                        <span class="info">* 必填</span>
                    </li>-->
                    <div class="msg" style="display: none;"></div>
                    <div>
                        <input type="button" value="保 存" class="save"/>
                        <input type="button" value="取 消" class="reset"/>
                    </div>
                </ul>
             </div>
        </div>
    </div>
</div>
<div id="footer">
    <div class="foot">
    <div class="w980 mc">
        <div class="foot_nr">
           
        </div>
    </div>
</div>
</div>
<!-- 用户设置教材同步章节 -->
<div class="box03 userChapter" style="display: none;"></div>
</div>
<script type="text/javascript">
$(document).ready(function () {
    AatUserCenter = {
        c:$('.userCenter'),
        eRetainTime:0,//倒计时时间，可根据该变量判断是否可以继续点击发送按钮
        sendTimeStr : '',//倒计时程序
        sendType:1,//当前认证类型 1为邮箱，2为手机
        showMsg:function(msg,type,show){
            var color = type=='error'?'#ff0000':'#8c8c8c';
            var msgDiv = this.c.find('.msg').html(msg).css({'color':color});
            if(show){
                msgDiv.show().effect('shake');
            }else{
                msgDiv.hide();
            }
        },
        getRealName:function(){
            var realName = this.c.find('#realNameVal').val();
            var realNameLength = realName.replace(/[^\x00-\xff]/g, 'xxx').length;//一个汉字3个字节utf8
            if (realNameLength < 6||realNameLength > 30) {
                this.showMsg('姓名长度必须大于两个汉字且小于10个汉字！','error',true);
                return false;
            }
            return realName;
        },
        getPassword:function(){
            var c = this.c;
            var oldPassword = c.find('#oldPasswordVal').val();
            var password = c.find('#passwordVal').val();
            var passwordRepeat = c.find('#passwordRepeat').val();
            if(oldPassword != ''||password != '' || passwordRepeat != ''){
                if(oldPassword.length<6||oldPassword.length>18){
                    this.showMsg('原密码长度必须是6至18位字符！','error',true);
                    return false;
                }
                if(password !== passwordRepeat){
                    this.showMsg('两次新设置密码输入不一致！','error',true);
                    return false;
                }
                if(password.length<6||password.length>18){
                    this.showMsg('新设置密码必须为6至18位！','error',true);
                    return false;
                }
            }
            return [oldPassword,password];
        },
        getAreaID:function(){
            var areaID = this.c.find('.areaSelect:last').val();
            if(isNaN(areaID)||areaID==0){
                this.showMsg('请选择所在地区！','error',true);
                return false;
            }
            return areaID;
        },
        getSchoolID:function(){
            var schoolID = this.c.find('.schoolSelect').val();
            if(isNaN(schoolID)||schoolID==0){
                this.showMsg('请选择所在学校！','error',true);
                return false;
            }
            return schoolID;
        },
        getGradeID:function(){
            var gradeID = this.c.find('.gradeSelect').val();
            if(isNaN(gradeID)||gradeID==0){
                this.showMsg('请选择年级！','error',true);
                return false;
            }
            return gradeID;
        },
        getPhone:function(){
            var phone = this.c.find('#phoneVal').val();
            var phoneReg = AatCommon.preg.phone;
            if(!phoneReg.test(phone)){
                this.showMsg('请输入正确的手机号！','error',true);
                return false;
            }
            return phone;
        },
        getEmail:function(){
            var email = this.c.find('#emailVal').val();
            var emailReg = /^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;
            if(!emailReg.test(email)){
                this.showMsg('请输入正确的邮箱地址！','error',true);
                return false;
            }
            return email;
        },
        showGrade:function(gradeID){
            //@todo 之后这个需要合并入User/UserInfo 端口中
            var c = this.c;
            $.post(U('Aat/Default/ajaxGrade'), function (e) {
                if (e.status == 1) {
                    var gradeInit = '',gradeName = '';
                    gradeInit += '<select class="gradeSelect">';
                    $.each(e.data, function (i, k) {
                        gradeInit += '<option disabled="disabled" style="text-align:center;font-style:italic;" value="0">' + k.grade_name + '</li>';
                        $.each(k.sub, function (j, m) {
                            gradeInit += '<option value="' + m.grade_id + '" ' + (gradeID == m.grade_id ? 'selected="selected"' : '') + '>' + m.grade_name + '</li>';
                            if(gradeID == m.grade_id){
                                gradeName = m.grade_name;
                            }
                        });
                    });
                    gradeInit += '</select>';
                    c.find('#gradeVal').html(gradeInit);
                    c.find('#gradeName').html(gradeName);
                }
            });
        },
        showSchool:function(areaID){
            var c = this.c;
            $.post(U('Aat/Default/ajaxSchool'),{'id':areaID},function(e){
                var schoolSelect='';
                if(e.status == 1){
                    schoolSelect += '<option value="0">请选择学校</option>';
                    $.each(e.data,function(i,k){
                        schoolSelect+='<option value="'+k['SchoolID']+'">'+k['SchoolName']+'</option>';
                    });
                }
                c.find('.schoolSelect').html(schoolSelect);
            });
        },
        showArea: function () {
            var self = this;
            var c = this.c;
            if (c.find('#areaVal').html() == '') {
                c.find('#areaVal').html('<select class="areaSelect"><option value="0">加载中...</option></select>');
                $.post(U('Aat/Default/ajaxArea'),{id:0}, function (e) {
                    var areaSelect = '';
                    if (e.status == 1) {
                        areaSelect += '<option value="0">请选择</option>';
                        $.each(e.data,function(i,k){
                            var isEnd = k['Last']==1?1:0;
                            areaSelect += '<option value="' + k['AreaID'] + '" isEnd="' + isEnd + '">' + k['AreaName'] + '</option>';
                        });
                    }
                    c.find('.areaSelect:eq(0)').html(areaSelect);
                });
            }
            c.on('change','.areaSelect',function(){
                var _this=$(this),nextSelect='',areaID=_this.val();//当前选择的地区的ID
                _this.nextAll('select').remove();
                c.find('.schoolSelect').html('<option value="0">请选择学校</option>');//重置选择学校
                if(!areaID) {
                    return false;
                }
                if(_this.find("option:selected").attr('isEnd')==1){
                    //如果是最后的地区，则选择根据此ID查询学校
                    self.showSchool(areaID);
                    return false;
                }
                _this.after('<select class="areaSelect"><option value="0">加载中..</option></select>');
                $.post(U('Aat/Default/ajaxArea'),{'id':areaID},function(e){
                    if(e.status == 1){
                        nextSelect+='<option value="0">请选择</option>';
                        $.each(e.data,function(i,k){
                            var isEnd= k['Last']==1?1:0;
                            nextSelect+='<option value="'+k['AreaID']+'" isEnd="'+isEnd+'">'+k['AreaName']+'</option>';
                        });
                    }
                    _this.next('select').html(nextSelect);
                });
            });
        },
        showInfo:function(){
            var c = this.c;
            var self = this;
            $.post(U('User/Aat/UserInfo'), function (e) {
                if (e.status != 1) {
                    c.find('.zt_title div').html(e.data);
                    c.find('.title02,.title03').hide();
                } else {
                    if(e.data.version=='2'){
                        c.find('.teachBook').css({'display':'block'});
                    }
                    //基本信息显示
                    c.find('#userPic').attr('src', e.data.userPic);
                    c.find('#userName').html(e.data.userName);
                    c.find('#orderNum').html(e.data.orderNum);
                    c.find('#version'+ e.data.version).addClass('versionSelect');
                    c.find('#chargeMode').html(e.data.chargeMode);
                    c.find('#chargeLeave').html(e.data.chargeLeave);
                    c.find('#loginTime').html(e.data.loginTime);
                    c.find('#loginIP').html(e.data.loginIP);
                    //个人资料显示
                    c.find('#realName').html(e.data.realName);
                    c.find('#areaName').html(e.data.areaName);
                    c.find('#schoolName').html(e.data.schoolName);
                    c.find('#phone').html(e.data.phone);
                    c.find('#email').html(e.data.email);
                    self.operationString(e.data.email,e.data.checkEmail,'Email','邮箱');
                    self.operationString(e.data.phone,e.data.checkPhone,'Phone','手机');
                    //个人资料修改
                    c.find('#realNameVal').val(e.data.realName);
                    c.find('#areaVal').html(e.data.areaInit?e.data.areaInit:'');
                    c.find('#schoolVal').html(e.data.schoolInit?e.data.schoolInit:'');
                    c.find('#realNameVal').val(e.data.realName);
                    c.find('#phoneVal').val(e.data.phone);
                    c.find('#emailVal').val(e.data.email);
                    self.showGrade(e.data.gradeID);
                    self.showArea();
                }
            });
        },
        /**
         * 是否认证的提示（兼容手机和邮箱）
         * @param data 认证信息
         * @param ifCheck 是否认证
         * @param idName 显示的位置Class名
         * @param name 显示文字描述
         */
        operationString:function(data,ifCheck,idName,name){
            var c=this.c;
            var str='';
            if(!data){//没有数据
                str = '<a href="javascript:;" class="attestation'+idName+'" atr="set">设置'+name+'</a>';
            }else if(data!=''&& ifCheck==='0'){//有数据但未认证
                str = '<a href="javascript:;" class="attestation'+idName+'" atr="check">认证'+name+'</a>';
            }else{//有数据，并且已认证
                str = '<span class="alreadyAtt ico'+idName+'"><i></i>已认证</span><a href="javascript:;" class="attestation'+idName+' edit'+idName+'" atr="edit">[修改]</a>';
            }
            c.find('#operation'+idName).html(str);
        },
        /**
         * 认证功能弹框HTML（兼容邮箱和手机）
         * @param name 提示信息文字描述
         * @param att 类型
         * @param data 认证数据，可以是邮箱号或者手机号
         * @param showVerifyImg 是否显示图片验证码（默认手机显示，邮箱不显示）
         */
        alertCheckEmailHtml:function(name,att,data,showVerifyImg){
            var title = '认证'+name;
            var email = '<input class="email" type="text" value="'+data+'" name="email"/>';
            if (att === 'set'){
                title = '设置'+name;
                email = '<input class="email" type="text" value="" placeholder="请输入您的'+name+'" name="email"/>';
            }else if(att=='edit'){
                title='确定更换'+name+'?';
                email = '<input class="email" disabled="true"　readOnly="true" type="text" value="'+data+'" name="email"/>';

            }
            var height=280;//弹框高度
            var verify='';//图片验证码
            var type='Email';//类型
            if(showVerifyImg){
                height=340;
                type='Phone';
                // verify='<div class="divMargin">' +
                // '<input class="eInput code imgCode" name="verifyCode" id="verifyCode" size="4" maxlength="4"/><img class="verifyImg" id="verifyImg" src="'+U('Aat/Default/verify')+'" url="'+U('Aat/Default/verify')+'" border="0" title="点击刷新验证码" style="cursor:pointer" align="absmiddle">'+
                // '</div>';
            }
            var content='<div id="aatEmailAlert">' +
                    '<div class="divMargin">'+email+'<input class="emailAttr" type="hidden" value="'+att+'" /><input class="dataType" type="hidden" value="'+type+'" /></div>' +
                    verify+
                    // '<div class="divMargin codeDiv">' +
                    // '<input name="emailSaveCode" class="eInput code" placeholder="请输入验证码" id="emailSaveCode" size="12" maxlength="6"/>' +
                    // '<input class="pointer getEmailRand" type="button" value="发送验证码" /></div>' + '<div class="divMargin emailError"></div>' +
                    '<div class="divMargin">' +
                    '<input type="button" id="submit01" value="确定" title="点击确定保存" class="normalYes" />' + '</div>' +
                    '</div>';
            $.aDialog({
                'width': 350,
                'height': height,
                'title': title,
                'content': content});
        },
        bindEmailClick:function(){
            var c = this.c;
            var self=this;
            var userName=AatCommon.getUserName();
            c.on('click','.attestationEmail',function(){
                if(self.sendType==2){//如果从手机验证切换过来
                    self.eRetainTime=0;//重置倒计时时间
                    self.clearTime();//清除倒计时功能
                    self.sendType=1;//改成邮箱标示
                }
                var att=$(this).attr('atr');
                var email=$('#email').text();
                self.alertCheckEmailHtml('邮箱',att,email,0);
                if (self.eRetainTime!=0){
                    $('#dialogAat .pointer').removeClass('getEmailRand');
                }
            });
            $(document).on('click','#dialogAat .getEmailRand',function(){
                // var data=$("input[name='email']").val().replace(/[ ]/g, "");
                // var type=$(".dataType").val();
                // var imgCode=$('#verifyCode').val();
                // if(type=='Phone' && !self.checkPhone(data)){//验证手机
                //     return false;
                // }
                // if(type=='Email' && !self.checkEmail(data)){//验证邮箱
                //     return false;
                // }
                // if(type=='Phone' && !imgCode){
                //     $('#dialogAat .imgCode').addClass('errorBorder');
                //     $('#dialogAat .imgCode').focus();
                //     $('#dialogAat .emailError').html('请填图片验证码！');
                //     return false;
                // }
                // var second=120;//倒计时时间
                // if(self.eRetainTime!=0){second=self.eRetainTime;}
                // $(this).val('重新发送('+second+')');
                // $(this).removeClass('getEmailRand');
                // AatUserCenter.sendTimeStr=setInterval(function () {
                //     second--;
                //     if (second <= 0) {
                //         self.clearTime();//清除倒计时
                //         return false;
                //     }
                //     self.eRetainTime=second;
                //     $('#dialogAat .pointer').val('重新发送(' + second + ')');
                // }, 1000);
                // $.post(U('User/Aat/send'+type+'Code'),{'data':data,'userName':userName,'imgCode':imgCode},function(e){
                //     if(e.status!='1'){
                //         self.clearTime();//清除倒计时
                //         $('#dialogAat .emailError').html(e.data);
                //     }
                // })
            });
            //提交验证码进行验证
            $(document).on('click','#dialogAat .normalYes',function(){
                var code=$('#emailSaveCode').val();
                var data=$("input[name='email']").val().replace(/[ ]/g, "");
                var emailAttr=$('.emailAttr').val();
                var type=$('.dataType').val();
                if(type=='Email' && !self.checkEmail(data)){
                    return false;
                }
                if(type=='Phone' && !self.checkPhone(data)){
                    return false;
                }
                // if(code==''){
                //     $('#emailSaveCode').addClass('errorBorder');
                //     $('#emailSaveCode').focus();
                //     $('#dialogAat .emailError').html('请填写验证码！');
                //     return false;
                // }else{
                //     $('#emailSaveCode').removeClass('errorBorder');
                // }
                $.post(U('User/Aat/check'+type+'Code'),{'code':code,'data':data,'userName':userName,'dataAttr':emailAttr},function(e){
                    if(e.status=='1'){
                        self.eRetainTime=0;
                        clearInterval(self.sendTimeStr);
                        $('#dialogAat .close').click();
                        if(emailAttr=='edit'){
                            if(type=='Phone') {
                                self.alertCheckEmailHtml('手机号','set','',1);
                            }else{
                                self.alertCheckEmailHtml('邮箱','set','',0);
                            }
                            return false;
                        }

                        AatCommon.setMsg(e.data);
                        AatCommon.showMsg(null);
                        $('#operation'+type).html('<span class="alreadyAtt ico'+type+'"><i></i>已认证</span><a href="javascript:;" class="attestation'+type+' editEmail" atr="edit">[修改]</a>');
                        if(type=='Phone'){
                            $('#phone').html(data);
                        }else {
                            $('#email').html(data);
                        }
                    }else{
                        if(e.data=='codeError'){
                            $('#emailSaveCode').addClass('errorBorder');
                        }
                        $('#dialogAat .emailError').html(e.data);
                    }
                })
            });
        },
        clearTime:function(){
            clearInterval(AatUserCenter.sendTimeStr);
            $("#dialogAat .pointer").addClass('getEmailRand');
            $("#dialogAat .pointer").val('发送验证码');
        },
        checkEmail:function(email){
            if(!email){
                $('#dialogAat .email').addClass('errorBorder');
                $('#dialogAat .email').focus();
                $('#dialogAat .emailError').html('请填写邮箱！');
                return false;
            }
            if(!email.match(/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/)){
                $('#dialogAat .email').css('border-color','red');
                $('#dialogAat .email').focus();
                $('#dialogAat .emailError').html('请填写正确的邮箱地址！');
                return false;
            }
            $('#dialogAat .email').removeClass('errorBorder');
            return true;
        },
        bindPhoneClick:function(){
            var c=this.c,self=this;
            c.on('click','.attestationPhone',function(){
                if(self.sendType==1){//如果从邮箱验证切换过来
                    self.eRetainTime=0;//重置倒计时时间
                    self.clearTime();//清除倒计时功能
                    self.sendType=2;//改成手机标示
                }
                var att = $(this).attr('atr');
                var phoneNum = $('#phone').text();
                self.alertCheckEmailHtml('手机号',att,phoneNum,1);
                // if (self.eRetainTime!=0){
                //     $('#dialogAat .pointer').removeClass('getEmailRand');
                // }
            });
            // $(document).on('click','#verifyImg',function(){
            //     //重载验证码
            //     var timenow = new Date().getTime();
            //     $('#verifyImg').attr('src',U('Aat/Default/verify')+'-'+timenow);
            // })
        },
        checkPhone:function(phoneNum){
            if(!phoneNum){
                $('#dialogAat .email').addClass('errorBorder');
                $('#dialogAat .email').focus();
                $('#dialogAat .emailError').html('请填写手机号！');
                return false;
            }
            var phoneReg = AatCommon.preg.phone;
            if(!phoneReg.test(phoneNum) || phoneNum.length!=11){
                $('#dialogAat .email').addClass('errorBorder');
                $('#dialogAat .email').focus();
                $('#dialogAat .emailError').html('请填写正确的手机号！');
                return false;
            }
            $('#dialogAat .email').removeClass('errorBorder');
            return true;
        },
        save: function () {
            var c = this.c;
            var self = this;
            c.on('click', '.save', function () {
                var saveButton = $(this);
                self.showMsg('','error',false);//隐藏错误信息
                saveButton.val('保存中...').attr('disabled', 'disabled');
                var updateData = {
                    realName: self.getRealName(),
                    password: self.getPassword(),
                    areaID: self.getAreaID(),
                    schoolID: self.getSchoolID(),
                    gradeID: self.getGradeID()
                    //phone: self.getPhone()
                    //email: self.getEmail()
                };
                var ifCheck = true;
                $.each(updateData, function (i, k) {
                    if (k === false) {
                        ifCheck = false;
                        return false;
                    }
                });
                if(ifCheck == false){
                    saveButton.val('保 存').removeAttr('disabled');
                    return false;
                }
                $.post(U('User/Aat/changeInfo'),updateData,function(e){
                    saveButton.val('保 存').removeAttr('disabled');
                    if(e.status == 0){
                        self.showMsg(e.data,'error',true);
                        return false;
                    }
                    c.find('.reset').trigger('click');
                    self.showInfo();
                });

            });
        },
        changeVersion:function(){
            var c = this.c;
            c.find('.version').on('click',function(){
                var button = $(this);
                if(button.hasClass('versionSelect')){
                    return false;
                }
                var versionID = button.attr('data');
                var versionText = button.val();
                button.val('切换中....').attr('disabled','disabled');
                c.find('.versionError').hide();
                $.post(U('User/Aat/changeVersion'),{version:versionID},function(e){
                    button.val(versionText).removeAttr('disabled');
                    if(e.status == 0){
                        c.find('.versionError').html(e.data).show();
                        return false;
                    }
                    //成功
                    c.find('.version').not(button).removeClass('versionSelect');
                    button.addClass('versionSelect');
                    if(versionID=='1'){
                        $('.teachBook').css({'display':'none'});
                    }else{
                        $('.teachBook').css({'display':'block'});
                    }
                    //将版本号写入cookie
                    AatCommon.setVersionID(versionID);
                });
            });
        },
        checkChapter:function(){
            $.post(U('Aat/Chapter/check'), function (e) {
                if (e.status != 0) {
                    $('.setChaper').val('修改教材');
                    $('.setChaper').attr('title','点击修改教材同步章节')
                }
            });
        },
        init:function(){
            var c = this.c,self = this;
            self.showInfo();
            //点击保存
            self.save();
            //切换版本
            self.changeVersion();
            //判断是否需要选择同步章节
            self.checkChapter();
            //验证手机
            self.bindPhoneClick();
            //验证邮箱
            self.bindEmailClick();
            //点击修改或取消修改
            c.find('#change,.reset').click(function(){
                if(c.find('#change').html().indexOf('取消修改')!=-1){
                    c.find('#change').html('修改个人信息');
                    c.find('.show').show();
                    c.find('.edit').hide();
                }else{
                    c.find('#change').html('取消修改');
                    c.find('.show').hide();
                    c.find('.edit').show();
                }
            });
            c.find('.setChaper').click(function(){
                //检测是否选择学科
                var subjectName = $('.xk_this').find('a').html();
                console.log(subjectName);
                if(!AatCommon.getSubjectID() || subjectName==undefined){
                    var close = function(){
                        if($('#leftsub').is(':hidden')){
                            $('.leftsub_an_off').trigger('click');
                        }
                    };
                    AatCommon.setMsg('请先点击左侧选择学科！');
                    AatCommon.showMsg(close);
                    return false;
                }
                //需要补充用户信息
                $.get(U('Aat/Chapter/index'), function (data) {
                    $('.userChapter').html(data);
                });
                //载入页面内容后执行
                $('.userChapter').aBox({
                    height: 350
                });
            });
        }
    };
    AatUserCenter.init();
});
</script>

</body>
</html>