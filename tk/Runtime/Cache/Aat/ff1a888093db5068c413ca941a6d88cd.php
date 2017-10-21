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

    <link href="/Public/plugin/ueditor/themes/default/css/ueditor.css<?php echo C(WLN_UPDATE_FILE_DATE);?>" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="/Public/plugin/ueditor/ueditor.config.js<?php echo C(WLN_UPDATE_FILE_DATE);?>"></script>
    <script type="text/javascript" src="/Public/newAat/js/ueditor.aat.config.js<?php echo C(WLN_UPDATE_FILE_DATE);?>"></script>
    <script type="text/javascript" src="/Public/plugin/ueditor/ueditor.all.js<?php echo C(WLN_UPDATE_FILE_DATE);?>"></script>
    <script type="text/javascript" src="/Public/plugin/bigImage.js<?php echo C(WLN_UPDATE_FILE_DATE);?>"></script>
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
<div id="content" class="w980 mc pt90 pb20">
<div class="box02 mb20">
<!--标题-->
<div class="zt_title">
    <div class="fl pt20 pl20">正在加载作业，请稍候...</div>
</div>
<!--信息栏-->
<div class="kzt_title" style="display: none;">
    <div class="time_box fl"><span class="ico_time fl"></span><span class="sz_time fl">00:00</span>
        <span id="parse_btn" class="an_time01 fl" style="cursor: pointer;"></span>
    </div>
    <div class="zttj_text fl">加载中...</div>
    <div class="kz_an_box">
        <a href="#" class="an01 fr"><span class="an_left"></span>
            <div id="test_submit" class="an_cen"><span style=" float:left; margin-top:5px; margin-right:5px;"><img
                    src="/Public/newAat/images/ico_jj.png"/></span>点击交卷
            </div>
            <span class="an_right"></span></a>
        <a id="test_next_submit" href="javascript:;" class="an02 fr mr7"><span class="an_left"></span><span class="an_cen" style="color: #7e7e7e;">
                下次再做</span><span class="an_right"></span></a>
    </div>
</div>
    <!--tab题型-->
    <div class="tabnav01 tabnav02" style="display: none;">
        <div class="left_an sub_an" style="display: none;"><a href="javascript:;"></a></div>
        <div class="right_an sub_an" style="display: none;"><a href="javascript:;"></a></div>
        <div class="tabnr_01">
        </div>
    </div>
    <!--提醒介绍-->
<div class="this_tab_title" style="display: none;">
</div>
<!--一以下为试题-->
<div id="test">
</div>
<!--右侧快捷按钮-->
<div id="db_dh_box" style="display: none;">
    <a class="an_go_top" href="#"></a>
    <a class="an_ctb02" href="#" style="display: none;">答题卡</a>
</div>
<!--以下是答题卡-->
<div class="dtk_box" style="display: none;">
    <div class="th_box">
        <a class="th_yz" href="#">1</a>
        <a class="th_cw" href="#">2</a>
        <a class="th_zq" href="#">3</a>
        <a href="#">4</a>
        <div class="clear"></div>
    </div>
    <div class="th_sm">
        <span class="fl">总共 <strong>20</strong> 题，已做<strong><font color="#00a0e9"> 1 </font></strong> 道题</span>
<span class="fr sm_ys">
<span><b class="ys_hui"></b><font>未做</font></span>
<span><b class="ys_lan"></b><font>已做</font></span>
<span><b class="ys_lv"></b><font>做对</font></span>
<span><b class="ys_hong"></b><font>做错</font></span>
</span>
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
</div>
<div id="dialog_pause" title="暂停做题" style="display: none;">
    <p>累了，休息一下吧！</p>

    <p>（ 暂停期间停止计时）</p>
</div>
<div id="dialog_submit" title="提交试卷" style="display: none;">
    <p id="do_amount"></p>
</div>
<div id="dialog_next_submit" title="下次再做" style="display: none;">
    <p id="next_do_amount"></p>

    <p>已经保存做题记录，现在您可以关闭页面？</p>
</div>
<div id="dialog_force_submit" title="提交试卷" style="display: none;">
    <p id="force_do_amount"></p>

    <p>答题时间已到，请提交试卷！</p>
</div>
<div id="correction" title="试题纠错" style="display:none;">
    <p id="errorDoAmount"></p>
</div>
<script type="text/javascript">
$(document).ready(function () {
    var AatMyHomeworkExercise = {
        sendID : '<?php echo ($send_id); ?>',
        init:function(){
            this.getMyHomeworkExerciseData();
            $('#test').on('click','.st_box img',function(){
                $(this).bigImage();
            });
        },
        getMyHomeworkExerciseData:function(){
            var self = this;
            //服务器返回的题目json数据并解析
            $.post(U('MyHomeworkExercise/getTest'),{'send_id':self.sendID,times:Math.random()}, function (e) {
                if (e.status !== 1) {
                    $('.zt_title div').html(e.data);
                } else {
                    window.timeA = (e.data.DoTime) * 1;
                    //顺序不能改变
                    //1显示页面做题统计，试题等数据信息 需要先调用showData生成页面数据
                    self.showData(e.data);
                    //2显示标题和信息栏、Tab
                    self.showTop(e.data);
                    //3页面关闭或者刷新的时候提交表单
                    self.submitClose();
                    //4初始化iCheck
                    self.initCheck();
                    //5选项发生改变时提交数据给服务器
                    self.postAnswer();
                    //6收藏试题
                    self.testCollectSave();
                    //7初始化placeholder
                    $('textarea[placeholder]:visible').placeholder();
                }
            });
        },
        showTop:function(e){
            var message = '',self = this;
            if(e.Message){
                $('#footer').after('<div id="dialog_message" style="display:none;overflow-y:auto;" title="'+ e.UserName+' 作业留言">'+ e.Message+'</div>');
                message = ' <a class="s_message" href="javascript:;" style="color: #069;">查看备注</a>';
            }
            //查看老师评语
            $('.zt_title').on('click','.s_message',function(){
                $('#dialog_message').dialog();
            });
            $('.zt_title div').html(e.SubjectName+'作业 '+ e.UserName+'于'+ e.LoadTime+'发布 结束时间：'+ e.EndTime +message);
            $('.kzt_title').show();
            //显示题型Tab
            self.showTestTab(e);
            //提交表单信息
            $('#test_submit').on('click', function () {
                self.submitForm('normal');
            });
            //下次再做
            $('#test_next_submit').on('click',function(){
                self.submitForm('next');
            });
            //暂停
            self.pause();
            //返回顶部
            self.goTop();
            //试题纠错
            $('.an_jc').on('click',function(){
                var testID=$(this).attr('data');
                AatCommon.correction(testID,true);
            })
        },

        submitClose:function(){
            var self = this;
            $(window).on('beforeunload', function () {
                var data = {};
                data['do_time'] = timeA;
                //recordID
                data['send_id'] = self.sendID;
                $.ajax({
                    type: 'POST',
                    cache: false,
                    data: data,
                    url: U('MyHomeworkExercise/doClose'),
                    async: false,
                    success: function () {
                    }
                });
            });
        },

        showData:function(e){
            var self = this;
            //现实不同题型的父类Div
            $.each(e.type,function(i,type){
//                var str = '<div data="'+type.TypesID+'" class="test_'+type.TypesID+'" style="display:none;"></div>';
//                $('#test').append(str);
            });
            //显示试题
            self.showTest(e);
            //试题赋值
            self.showTestVal(e);
            //显示试题统计信息
            self.showInfo();
        },

        postAnswer:function(){
            //单选
            $('input:radio').on('ifChecked', function () {
                var thisInput = $(this);
                var data = {};
                var answer = [];
                var answerID;
                answer[0] = $('.' + $(this).attr('class') + ':checked').val();
                answerID = $(this).attr('class');
                data['answer'] = answer;
                data['answer_id'] = answerID;
                data['times'] = Math.random();
                $.ajax({
                    type: 'POST',
                    cache: false,
                    data: data,
                    url: U('MyHomeworkExercise/doAnswer'),
                    success: function (e) {
                        if (e.status == 0) {
                            thisInput.iCheck('uncheck');
                            alert(e.data);
                        }
                    }
                });
            });
            //多选
            $('input:checkbox').on('ifToggled', function () {
                var thisInput = $(this);
                var data = {};
                var answer = [];
                var answerID;
                $('.' + $(this).attr('class') + ':checked').each(function (i, k) {
                    answer[i] = k.value;
                });
                answerID = $(this).attr('class');
                data['answer'] = answer;
                data['answer_id'] = answerID;
                data['times'] = Math.random();
                $.ajax({
                    type: 'POST',
                    cache: false,
                    data: data,
                    url: U('MyHomeworkExercise/doAnswer'),
                    success: function (e) {
                        if (e.status == 0) {
                            thisInput.iCheck('uncheck');
                            alert(e.data);
                        }
                    }
                });
            });

            //大题focus时初始化ueditor
            $('#test').on('focusin','.bd_hdk',function(){
                var self = $(this);
                UE.getEditor(self.attr('id'),ueditorAatConfig.config).ready(function(){
                    $("div.bd_hdk").css({border:'none',padding:'0'});
                    $('textarea[class='+self.attr('id')+']').hide();
                });
            });
            //大题确定答案
            $('#test').on('click','.test_ok',function(){
                var testOk = $(this);
                var textID = 'text_'+$(this).attr('data');//输入框ID
                var text = UE.getEditor(textID,ueditorAatConfig.config).getContent();
                if(text == ''){
                    alert('请输入试题答案！');
                    return;
                }
                var data = {};
                var answer = [];
                var answerID;
                answer[0] = text;
                answerID = $(this).attr('data');
                data['answer'] = answer;
                data['answer_id'] = answerID;
                data['times'] = Math.random();
                $.ajax({
                    type: 'POST',
                    cache: false,
                    data: data,
                    url: U('MyHomeworkExercise/doAnswer'),
                    success: function (e) {
                        if (e.status == 0) {
                            alert(e.data);
                        }else{
                            UE.getEditor(textID,ueditorAatConfig.config).setHide();
                            $('.box_'+ answerID).hide();//隐藏编辑框载体
                            $('#text_view_'+ answerID).html(text).show();//显示预览
                            testOk.parent().hide();//隐藏确定
                            testOk.parent().next().show();//显示编辑
                        }
                    }
                });
            });
            //大题修改答案
            $('#test').on('click','.test_ok_edit',function(){
                var self = $(this);
                var id = $(this).attr('data');
                UE.getEditor('text_'+ id,ueditorAatConfig.config).ready(function(){
                    $('#text_view_'+ id).hide();//隐藏预览DOM
                    this.show();
                    $('.box_'+ id).show();//显示输入框载体
                    $('div.bd_hdk').css({border:'none',padding:'0'});
                    self.parent().hide();//隐藏"编辑"按钮
                    self.parent().prev().show();//显示"确定"按钮
                });//初始化编辑器
            });
        },

        testCollectSave:function(){
            $(document).on('click', '.collect_test', function(){
                var testID = $(this).attr('data');
                AatCommon.testCollectSave(testID);
            })
        },

        showInfo:function(){
            var self = this;
            var amout = self.doAmount();
            var str = '已做 <strong style="color:#00a0e9">'+amout['do']+'</strong> 题 / 共 <strong>'+amout['all']+'</strong> 小题&nbsp;&nbsp;&nbsp;&nbsp;剩余'+
                    '<strong style="color:#00a0e9"> '+amout['undo']+'</strong> 题未作答';
            $('.zttj_text').html(str);
            $('#test').on('ifToggled','input:checkbox,input:radio',function(){
                amout = self.doAmount();
                str = '已做 <strong style="color:#00a0e9">'+amout['do']+'</strong> 题 / 共 <strong>'+amout['all']+'</strong> 题&nbsp;&nbsp;&nbsp;&nbsp;剩余'+
                        '<strong style="color:#00a0e9"> '+amout['undo']+'</strong> 题未作答';
                $('.zttj_text').html(str);
            });
            $('#test').on('click','.test_ok',function(){
                amout = self.doAmount();
                str = '已做 <strong style="color:#00a0e9">'+amout['do']+'</strong> 题 / 共 <strong>'+amout['all']+'</strong> 题&nbsp;&nbsp;&nbsp;&nbsp;剩余'+
                        '<strong style="color:#00a0e9"> '+amout['undo']+'</strong> 题未作答';
                $('.zttj_text').html(str);
            });
        },

        showTest:function(e){
            var totalNum=0; //试题序号
            $.each(e.test, function (i, test) {
                //一道试题所有内容Html tContent
                var tContent = '';
//                var tNum = test.number;
                var tNum = totalNum+1;//试题序号
                var tTestID = test.test_id;
                var tTitle = test.test_title;
                if(!tTitle){
                    //如果tTitle没有值则跳出循环
                    return true;
                }
                var tType = test.test_type;
                if (test.if_choose == 1) {
                    //复合题
                    tContent += '<div class="tw_box">' +
                            '<div class="title">'+
                            '<span class="ico_tw fl">'+tNum+'-'+(tNum+parseInt(test.testNum)-1)+'题</span>'+
                            '<span class="fl tit"></span>' +
                            '</div>'+
                            '<div class="nr_box">'+tTitle+'</div>' +
                            '</div>';
                    if(typeof(test.sub) != 'undefined'){
                        //复合体正常分割小题的情况
                        $.each(test.sub, function (iSub, subTest) {
                            tContent += '<div class="st_box">';//复合题st_box开始

                            tContent +=    '<div class="st_tm_box">' +
                                    '<div class="title">'+
                                    '<span class="ico_th fl">'+(tNum-1+parseInt(subTest.order))+'</span>'+
                                    '<span class="tit">'+subTest.sub_title+'</span>' +
                                    '<div class="bjjt"></div>' +
                                    '</div>' +
                                    '</div>';
                            if (subTest.if_choose > 1) {
                                //复合体中选择题
                                if(subTest.sub_options[0] != 'A'){
                                    //如果能够分割选项，不是写死的ABCD等，则显示
                                    tContent += '<div class="st_wt_box">';
                                    $.each(subTest.sub_options,function(op_i,op){
                                        tContent += '<p><span class="st_wt_bt">'+op.substr(0,1)+'.</span>'+op.substr(2)+'</p>';
                                    });
                                    tContent += '</div>';
                                }

                                tContent += '<div class="dt_box dt_box_xzt">' +
                                        '<div class="fl xx_xzt">';
                                $.each(subTest.sub_options, function (in_i, inp) {
                                    var input = inp.substr(0, 1);
                                    var inType = subTest.if_choose == 3 ? 'radio' : 'checkbox';
                                    var inName = subTest.if_choose == 3 ? subTest.answer_id : subTest.answer_id + '[]';
                                    tContent += '<label><input id="' + subTest.answer_id + '_' + input + '" type="' + inType + '" name="' + inName +
                                            '" class="' + subTest.answer_id + '" value="' + input + '" />&nbsp;&nbsp;' + input + '</label>';
                                });
                                tContent +=    '</div>' +
                                        '<div class="fr cz_an_box">' +
                                        '<a data="' + tTestID + '" class="an_jc" href="javascript:;">我要纠错</a>'+
                                        '<a class="an_sc collect_test" href="javascript:;" data="' + tTestID + '">收藏本题</a>'+
                                        '</div>' +
                                        '<div class="clear"></div>' +
                                        '</div>';
                            }else if(subTest.if_choose == 0){
                                //复合体中大题
                                var xxWdt = 'block';var xxWdtDa='none';var answer=subTest.sub_answer;
                                var placeHolder = ' placeholder="请输入您的答案"';
                                if(subTest.sub_answer != ''){
                                    //有答案时
                                    xxWdt = 'none';
                                    xxWdtDa='block';
                                }
                                tContent += '<div class="dt_box dt_box_wdt" style="display: block;">' +
                                        '<div class="xx_wdt box_'+subTest.answer_id+'" style="display: '+xxWdt+'">'+
                                        '<textarea id="text_' + subTest.answer_id + '" name="'+subTest.answer_id+'" class="bd_hdk" '+placeHolder+'>'+answer+'</textarea>' +
                                        '</div>'+
                                        '<div id="text_view_'+ subTest.answer_id + '" style="display: '+xxWdtDa+';width:940px;overflow:auto" class="xx_wdt_da s_test_view">'+
                                        answer+
                                        '</div>'+
                                        '<div class="an_box_wdt">' +
                                        '<div class="fl xx_wd_an" style="display: '+xxWdt+'">' +
                                        '<a class="an01 fl test_ok" href="javascript:;" data="' + subTest.answer_id + '">' +
                                        '<span class="an_left"></span><div class="an_cen">确定</div><span class="an_right"></span>' +
                                        '</a>' +
                                        '</div>'+
                                        '<div style="display:'+xxWdtDa+'" class="fl xx_wd_an">' +
                                        '<a class="an02 fr an_jx test_ok_edit" href="javascript:;" data="' + subTest.answer_id + '">' +
                                        '<span class="an_left"></span><div class="an_cen"><font color="#7e7e7e">修改答案</font><span style=" float:right; margin-top:4px; margin-left:5px;"></span></div><span class="an_right"></span>' +
                                        '</a>' +
                                        '</div>'+
                                        '<div class="cz_an_box fr">' +
                                        '<a class="an_jc" href="javascript:;" data="' + tTestID +  '">我要纠错</a>'+
                                        '<a class="an_sc collect_test" href="javascript:;" data="' + tTestID + '">收藏本题</a>'+
                                        '</div>' +
                                        '</div>'+

                                        '</div>';
                            }
                            tContent += '</div>';//复合题st_box结束
                        });
                    }

                } else if (test.if_choose == 2||test.if_choose == 3) {
                    tContent += '<div class="st_box">';//st_box

                    tContent +=   '<div class="st_tm_box">' +
                            '<div class="title">'+
                            '<span class="ico_th fl">'+tNum+'</span>'+
                            '<span class="tit">'+test.test_title+'</span>' +
                            '<div class="bjjt"></div>' +
                            '</div>' +
                            '</div>';
                    if (test.test_options[0] != 'A') {
                        tContent += '<div class="st_wt_box">';
                        $.each(test.test_options, function (op_i, op) {
                            var option = [op.substr(0, 1), op.substr(2)];
                            tContent += '<p><span class="st_wt_bt">' + option[0] + '.</span>' + option[1] + '</p>';
                        });
                        tContent += '</div>';
                    }

                    tContent +=      '<div class="dt_box dt_box_xzt">' +
                            '<div class="fl xx_xzt">';
                    $.each(test.test_options,function(in_i,inp){
                        var input = inp.substr(0,1);
                        var inType = test.if_choose==3?'radio':'checkbox';
                        var inName = test.if_choose==3?test.answer_id:test.answer_id+'[]';
                        tContent += '<label><input id="' + test.answer_id + '_' + input + '" type="' + inType + '" name="' + inName +
                                '" class="' + test.answer_id + '" value="' + input + '" />&nbsp;&nbsp;'+input+'</label>';
                    });
                    tContent +=            '</div>' +
                            '<div class="fr cz_an_box">' +
                            '<a class="an_jc" href="javascript:;" data="' + tTestID + '">我要纠错</a>'+
                            '<a class="an_sc collect_test" href="javascript:;" data="' + tTestID + '">收藏本题</a>'+
                            '</div>' +
                            '<div class="clear"></div>' +
                            '</div>';
                    tContent += '</div>';//st_box
                } else {
                    //大题
                    tContent += '<div class="st_box">';//st_box

                    tContent +=    '<div class="st_tm_box">' +
                            '<div class="title">'+
                            '<span class="ico_th fl">'+tNum+'</span>'+
                            '<span class="tit">'+test.test_title+'</span>' +
                            '<div class="bjjt"></div>' +
                            '</div>' +
                            '</div>';
                    var xxWdt = 'block',xxWdtDa='none',answer=test.answer;
                    var placeHolder = ' placeholder="请输入您的答案"';
                    if(test.answer !== ''){
                        //有答案时
                        xxWdt = 'none';
                        xxWdtDa='block';
                    }
                    tContent += '<div class="dt_box dt_box_wdt">' +
                            '<div class="xx_wdt box_'+test.answer_id+'" style="display: '+xxWdt+';">'+
                            '<textarea id="text_' + test.answer_id + '" class="bd_hdk" name="'+test.answer_id+'" '+placeHolder+'>'+answer+'</textarea>' +
                            '</div>'+
                            '<div id="text_view_'+ test.answer_id + '" style="display: '+xxWdtDa+';width:940px;overflow:auto" class="xx_wdt_da s_test_view">'+
                            answer+
                            '</div>'+
                            '<div class="an_box_wdt">' +
                            '<div class="fl xx_wd_an" style="display: '+xxWdt+'">' +
                            '<a class="an01 fl test_ok" href="javascript:;" data="' + test.answer_id + '">' +
                            '<span class="an_left"></span><div class="an_cen">确定</div><span class="an_right"></span>' +
                            '</a>' +
                            '</div>'+
                            '<div style="display:'+xxWdtDa+'" class="fl xx_wd_an">' +
                            '<a class="an02 fr an_jx test_ok_edit" href="javascript:;" data="' + test.answer_id + '">' +
                            '<span class="an_left"></span><div class="an_cen"><font color="#7e7e7e">修改答案</font><span style=" float:right; margin-top:4px; margin-left:5px;"></span></div><span class="an_right"></span>' +
                            '</a>' +
                            '</div>'+
                            '<div class="cz_an_box fr">' +
                            '<a class="an_jc" href="javascript:;" data="' + tTestID + '">我要纠错</a>'+
                            '<a class="an_sc collect_test" href="javascript:;" data="' + tTestID + '">收藏本题</a>'+
                            '</div>' +
                            '</div>'+
                            '</div>';
                    tContent += '</div>';//st_box
                }
                totalNum+=parseInt(test.testNum);
                $('#test').append(tContent);
            });
        },

        showTestVal:function(e){
            //进行赋值操作
            $.each(e.test, function (i, test) {
                if (test.if_choose == 1) {
                    //复合题
                    if(typeof(test.sub) != 'undefined'){
                        $.each(test.sub, function (sub_i, subTest) {
                            if (subTest.if_choose == 2) {
                                //多选
                                if(typeof(subTest.sub_answer) != 'undefined'){
                                    $.each(subTest.sub_answer, function (a_i, a_k) {
                                        $('#' + subTest.answer_id + '_' + a_k).iCheck('check');
                                    })
                                }

                            } else if (subTest.if_choose == 3) {
                                //单选
                                $('#' + subTest.answer_id + '_' + subTest.sub_answer).iCheck('check');
                            }
                            else if (subTest.if_choose = 0) {
                                //大题赋值
                                $('#' + subTest.answer_id).val(subTest.sub_answer);
                            }
                        });
                    }

                } else if (test.if_choose == 2) {
                    //多选
                    if(typeof(test.answer) != 'undefined'){
                        $.each(test.answer, function (a_i, a_k) {
                            $('#' + test.answer_id + '_' + a_k).iCheck('check');
                        })
                    }

                } else if (test.if_choose == 3) {
                    //单选
                    $('#' + test.answer_id + '_' + test.answer).iCheck('check');
                } else {
                    //大题赋值
                    $('#' + test.answer_id).val(test.answer);
                }
            });
        },

        showTestTab:function(e){
            var str = '试题数量：';
            $.each(e.type,function(i,k){
                var amount = k.TypesAmount;
                str += '<span data="'+ k.TypesID+'">'+ k.TypesName+'['+amount+']</span> ';
            });
            $('.tabnr_01').html(str);
            $('.tabnav01').show();
            if($('.tabnr_01').width()>958){
                $('.tabnav02 .right_an').show();
            }
            $('.tabnav02 .right_an').mouseover(function(){
                $('.tabnav02 .left_an').show();
                move_time = setInterval(function(){
                    var left = parseInt($('.tabnr_01').css('left'))-10;
                    if(left >= -($('.tabnr_01').width()-958)){
                        $('.tabnr_01').css('left',left);
                        if(left + ($('.tabnr_01').width()-958)<10){
                            $('.tabnav02 .right_an').hide();
                        }
                    }
                },50);
            }).mouseout(function(){
                clearInterval(move_time);
            });
            $('.tabnav02 .left_an').mouseover(function(){
                $('.tabnav02 .right_an').show();
                move_time = setInterval(function(){
                    var left = parseInt($('.tabnr_01').css('left'))+10;
                    if(left <= 0 ){
                        $('.tabnr_01').css('left',left);
                        if(left > -10){
                            $('.tabnav02 .left_an').hide();
                        }
                    }
                },50);
            }).mouseout(function(){
                clearInterval(move_time);
            });
            $('.tabnr_01 a:first-child').addClass('this');
            $('#test>div:first-child').show();
            $('.tabnr_01 a').click(function(){
                //试题显示转变
                $('#test').children().hide();
                $('#test div[data='+$(this).attr('data')+']').show();
                //Tab转换
                $('.tabnr_01 a').removeClass('this');
                $(this).addClass('this');
            });
        },
        submitForm:function(style) {
            var self = this;
            self.pursePaper();
            var dialog;
            var buttons = {};
            if(style == 'normal') {
                dialog = '#dialog_submit';
                buttons = {
                    "提交试卷": function () {
                        self.dialogButtonDisable(true);
                        $(this).dialog({title: '数据正在提交'});
                        $('#dialog_submit').html('<div id="progressbar" style="position: relative;"><div style="position: ' +
                                'absolute;left: 40%;top: 5px;">请稍候...</div></div><p></p>');
                        $("#progressbar").progressbar({
                            value: false
                        });
                        self.ajaxSubmit();
                    },
                    '继续做题': function () {
                        $(this).dialog("close");
                        self.beginPaper();
                    }
                };
            }else if(style == 'force') {
                dialog = '#dialog_force_submit';
                buttons = {
                    "提交试卷": function () {
                        self.dialogButtonDisable(true);
                        $(this).dialog({title: '数据正在提交'});
                        $('#dialog_submit').html('<div id="progressbar" style="position: relative;"><div style="position: ' +
                                'absolute;left: 40%;top: 5px;">请稍候...</div></div><p></p>');
                        $("#progressbar").progressbar({
                            value: false
                        });
                        self.ajaxSubmit();
                    }
                };
            }else if(style == 'next') {
                dialog = '#dialog_next_submit';
                buttons = {
                    '下次再做': function () {
                        location.href = '/Aat';
                    },
                    '继续做题': function () {
                        $(this).dialog("close");
                        self.beginPaper();
                    }
                };
            }
            $(dialog).dialog({
                modal: true,
                buttons: buttons,
                open: function () {
                    $('.ui-dialog-titlebar-close', $(this).parent('')).hide();
                    self.dialogButtonDisable(false);
                    var amount = self.doAmount();
                    var undoStr = amount['undo'] ? ('未做<span style="color: #ff0000;">' + amount['undo'] + '</span>道题') : '<span style="color: darkgreen;">试题已经全部完成</span>';
                    var suggest = amount['undo']?'<p>必须做完所有作业！</p>':'';
                    $('#do_amount').html('已做' + amount['do'] + '道题，' + undoStr + '，用时：' + $(".sz_time").text()+suggest);
                }
            });
        },

        ajaxSubmit:function() {
            var data = {};
            data['do_time'] = timeA;
            data['send_id'] = this.sendID;
            $.ajax({
                type: 'POST',
                cache: false,
                data: data,
                url: U('MyHomeworkExercise/doSubmit'),
                success: function (e) {
                    if (e.status !== 1) {
                        alert(e.data);
                    } else {
                        location.href = U('MyHomeworkAnswer/index?id=' + e.data);
                    }
                }
            });
        },

        doAmount:function(){
            var self = this;
            var doTest = [];
            var allTest = [];
            $('#test input').each(function (i, k) {
                //选择题
                if (k.checked === true) {
                    //作答题目
                    doTest.push(k.name);
                }
                //全部题目
                allTest.push(k.name);
            });
            $('#test .s_test_view').each(function (i, k) {
                //大题
                if ($(this).html() !== '') {
                    //作答题目
                    doTest.push(k.id);
                }
                //全部题目
                allTest.push(k.id);
            });
            var result = {};
            result['do'] = self.arrayUnique(doTest).length;
            result['undo'] = (self.arrayUnique(allTest).length - self.arrayUnique(doTest).length);
            result['all'] = self.arrayUnique(allTest).length;
            return result;
        },

        arrayUnique:function(res){
            var json = {};
            var result = [];
            $.each(res, function (i, k) {
                if (!json[k]) {
                    result.push(k);
                    json[k] = 1;
                }
            });
            return result;
        },

        dialogButtonDisable:function(disable){
            var button = $('.ui-dialog-buttonpane button');
            if (disable === true) {
                button.addClass('ui-state-disabled');
                button.attr('disabled', 'disabled');
            } else {
                button.removeAttr('disabled');
                button.removeClass('ui-state-disabled');
            }
        },

        pause:function(){
            var self = this;
            self.beginPaper();
            $("#parse_btn").click(function () {
                self.pursePaper();
                $("#dialog_pause").dialog({
                    modal: true,
                    buttons: {
                        "继续做题": function () {
                            self.beginPaper();
                            $(this).dialog("close");
                        }
                    },
                    open: function () {
                        $('.ui-dialog-titlebar-close', $(this).parent('')).hide();
                    }
                });
            });
        },

        goTop:function(){
            var bottom = $(document).height()-$(window).height()-107;
            $('#db_dh_box').css({'bottom':bottom,'_bottom':bottom}).show();
            $(window).resize(function(){
                bottom = $(document).height()-$(window).height()-107;
                bottom = bottom>0?bottom:0;
                $('#db_dh_box').css({'bottom':bottom,'_bottom':bottom}).show();
            });
            $(window).scroll(function(){
                bottom = $(document).height()-$(window).height()-107-$(window).scrollTop();
                bottom = bottom>0?bottom:0;
                $('#db_dh_box').css({'bottom':bottom,'_bottom':bottom}).show();
            });
            //页面滚动
            $('#db_dh_box .an_go_top').click(function(){$('html,body').animate({scrollTop : 0}, 300);});
        },

        initCheck:function(){
            //初始化check
            $('input:checkbox,input:radio').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue',
                increaseArea: '20%'
            });
            $('input:checkbox,input:radio').on('ifChecked',function(){$(this).attr({'checked':'checked'})});
            $('input:checkbox,input:radio').on('ifUnchecked',function(){$(this).removeAttr('checked')});
        },

        beginPaper:function(){
            var self = this;
            window.timeFunction = setInterval(function () {
                timeA += 1;
                var m = new String(parseInt(timeA / 60)).length == 1 ? "0" + new String(parseInt(timeA / 60)) : parseInt(timeA / 60);
                var s = new String(timeA % 60).length == 1 ? "0" + new String(timeA % 60) : timeA % 60;
                $(".sz_time").text(m + ":" + s);
                if (timeA >= 36000) {
                    self.submitForm('force');
                }
            }, 1000);
        },

        pursePaper:function(){
            clearInterval(window.timeFunction);
        },
    };
    AatMyHomeworkExercise.init();

});
</script>

</body>
</html>