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
    <a class="an01 fr" href="<?php echo U('MyHomework/index');?>" style="margin-top: 15px;margin-right: 20px;"><span class="an_left"></span><div class="an_cen"><span style=" float:left; margin-top:5px; margin-right:5px;"><img src="/Public/newAat/images/ico_jj.png"></span>返回我的作业</div><span class="an_right"></span></a>
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
    <a class="an_go_top" href="javascript:;"></a>
    <a class="an_ctb02" href="javascript:;" style="display: none;">答题卡</a>
</div>
<!--以下是答题卡-->
<div class="dtk_box" style="display: none;">
    <div class="th_box">
        <div></div>
        <div class="clear"></div>
    </div>
    <div class="th_sm">
        <span class="fl"></span>
        <span class="fr sm_ys">
        <span><b class="ys_lv"></b><font>做对</font></span>
        <span><b class="ys_hong"></b><font>做错</font></span>
        <span><b class="ys_hui"></b><font>未做</font></span>
        <span><b class="ys_lan"></b><font>无法判断对错</font></span>
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
<div id="kl_video"></div>
<div id="correction" title="试题纠错" style="display:none;">
    <p id="errorDoAmount"></p>
</div>
<script type="text/javascript">
$(document).ready(function () {
    var AatMyHomeworkAnswer = {
        init:function(){
            this.getMyHomeworkAnswerData();
            this.testCollectSave();
            $('#test').on('click','.st_box img',function(){
                $(this).bigImage();
            });
        },
        getMyHomeworkAnswerData:function(){
            var self = this;
            //服务器返回的题目json数据并解析
            $.post(U('MyHomeworkAnswer/returnTestList'),{'id':'<?php echo ($send_id); ?>',times:Math.random()}, function (e) {
                if (e.status !== 1) {
                    $('.zt_title div:first-child').html(e.data);
                } else {
                    //顺序不能改变
                    //1显示页面做题统计，试题等数据信息 需要先调用showData生成页面数据
                    self.showData(e.data);
                    //2显示标题和信息栏、Tab、右侧向上和答题卡
                    self.showTop(e.data);
                    //6收藏试题
                    self.testCollectSave();
                    //视频播放
                    self.playVideo();
                }
            });
        },
        showData:function(e) {
            var self = this;
            //现实不同题型的父类Div
            $.each(e.type,function(i,type){
//                var str = '<div data="'+type.TypesID+'" class="test_'+type.TypesID+'" style="display:none;"></div>';
//                $('#test').append(str);
            });
            //显示试题
            self.showTest(e);
            //查看解析
            $('.see_analysis').click(function(){
                var str = $(this).children('.an_cen').children('font');
                str.text(str.text()=='隐藏解析'?'查看解析':'隐藏解析');
                $(this).parent().parent().next().toggle(300);
            });
            //做题统计答题卡
            self.showAnalysis(e);
        },

        showTop:function(e) {
            var self = this;
            var check_time = e.CheckTime?(' 审批时间：'+e.CheckTime):'';
            var score = e.CheckTime?(' 分数：'+e.Score+'分'):'';
            $('.zt_title div:first-child').html(e.SubjectName+'作业 '+ e.UserName+'于'+ e.LoadTime+'发布'+check_time+score);
            $('.kzt_title').show();
            //显示题型Tab
            self.showTestTab(e);
            //显示右侧向上按钮和答题卡
            self.goTop();
            //试题纠错
            $('.an_jc').on('click',function(){
                var testID=$(this).attr('data');
                AatCommon.correction(testID,false);
            })
        },

        testCollectSave:function() {
            $(document).on('click', '.collect_test', function () {
                var testID = $(this).attr('data');
                AatCommon.testCollectSave(testID);
            })
        },

        showTest:function(e) {
            var totalNum=0; //试题序号
            $.each(e.test, function (i, test) {
                //一道试题所有内容Html t_content
                var t_content = '';
                //var t_num = test.number;
                var t_num = totalNum+1;//试题序号
                var t_test_id = test.test_id;
                var t_title = test.test_title;
                if(!t_title){
                    //如果t_title没有值则跳出循环
                    return true;
                }
                var t_type = test.test_type;
                if (test.if_choose == 1) {
                    //复合题
                    t_content += '<div class="tw_box">' +
                            '<div class="title">'+
                            '<span class="ico_tw fl">'+t_num+'-'+(t_num+parseInt(test.testNum)-1)+'题</span>'+
                            '<span class="fl tit">'+''+'</span>' +
                            '</div>'+
                            '<div class="nr_box">'+t_title+'</div>' +
                            '</div>';
                    if(typeof(test.sub) != 'undefined'){
                        $.each(test.sub, function (sub_i, sub_test) {
                            t_content += '<div class="st_box">' +
                                    '<div class="st_tm_box">' +
                                    '<div class="title">'+
                                    '<span class="ico_th fl">'+(t_num-1+parseInt(sub_test.order))+'</span>'+
                                    '<span class="tit">'+sub_test.sub_title+'</span>' +
                                    '<div class="bjjt"></div>' +
                                    '</div>' +
                                    '</div>';
                            if (sub_test.if_choose > 1) {
                                //复合体中选择题
                                if(sub_test.sub_options[0] != 'A'){
                                    t_content += '<div class="st_wt_box">';
                                    $.each(sub_test.sub_options,function(op_i,op){
                                        t_content += '<p><span class="st_wt_bt">'+op.substr(0,1)+'.</span>'+op.substr(2)+'</p>';
                                    });
                                    t_content += '</div>';
                                }
                                t_content += '<div class="dt_box dt_box_xzt">' +
                                        '<div class="fl xx_xzt_da">';
                                var answer = sub_test.sub_answer ? sub_test.sub_answer : '空';
                                t_content +=    '正确答案：<font color="#27a152">'+sub_test.sub_right_answer+'</font>，' +
                                        '您的答案为 <font color="#00a0e9">'+answer+'</font> ，'+
                                        '<font color="#69be83" style="display: '+(sub_test.if_right == 2 ? 'inline' : 'none')+';"><strong>回答正确！</strong></font>'+
                                        '<font color="#fe7676" style="display:'+(sub_test.if_right == 2 ? 'none' : 'inline')+';"><strong>回答错误！</strong></font>' +
                                        '</div>'+
                                        '<div class="fr cz_an_box">' +
                                        '<a class="an_jc" href="#" data="' + t_test_id + '">我要纠错</a>'+
                                        '<a class="an_sc collect_test" href="javascript:;" data="' + t_test_id + '">收藏本题</a>'+
                                        '<a class="an02 fr an_jx see_analysis" href="javascript:;">' +
                                        '<span class="an_left"></span><div class="an_cen"><font color="#7e7e7e" class="fl mr5">查看解析</font><span style="margin-top:3px" class="fl"><img src="/Public/newAat/images/ico_jt01.png"></span></div><span class="an_right"></span>' +
                                        '</a>'+
                                        '</div>' +
                                        '<div class="clear"></div>' +
                                        '</div>';
                            }else if(sub_test.if_choose == 0){
                                //复合体中大题
                                t_content += '<div class="dt_box dt_box_wdt">' +
                                        '<div class="xx_wdt_da">' +
                                        '<font color="#27a152">正确答案：</font>'+
                                        sub_test.sub_right_answer+
                                        '</div>'+
                                        '<div class="xx_wdt_da">' +
                                        '<font color="#00a0e9">你的答案：</font>'+(sub_test.sub_answer?sub_test.sub_answer:'<font color="#00a0e9">空</font>')+
                                        '</div>'+
                                        '<div class="dt_box dt_box_xzt">'+
                                        '<div class="cz_an_box fr">' +
                                        '<a class="an_jc" href="javascript:;" data="'+ t_test_id +'">我要纠错</a>'+
                                        '<a class="an_sc collect_test" href="javascript:;" data="' + t_test_id + '">收藏本题</a>'+
                                        '<a class="an02 fr an_jx see_analysis" href="javascript:;">' +
                                        '<span class="an_left"></span><div class="an_cen"><font color="#7e7e7e" class="fl mr5">查看解析</font><span style="margin-top:3px" class="fl"><img src="/Public/newAat/images/ico_jt01.png"></span></div><span class="an_right"></span>' +
                                        '</a>'+
                                        '</div>' +
                                        '<div class="clear"></div>' +
                                        '</div>';
                            }
                            //以下为试题解析知识点和来源
                            t_content +=        '<div class="dan_box" style="display: none;">' +
                                    '<div class="dan_box_nr">' +
                                    '<div class="title">' +
                                    '<span class="an_left"></span><span class="an_cen">答案解析</span><span class="an_right"></span>' +
                                    '</div>'+
                                    sub_test.sub_analytic+
                                    '</div>'+
                                    '<div class="dan_box_nr dan_box_kd" style="display:block;">' +
                                    '<div class="title">' +
                                    '<span class="an_left"></span><span class="an_cen">考查考点</span><span class="an_right"></span>' +
                                    '</div>'+
                                    '<p>'+(test.kl_list?test.kl_list:'[暂无]')+'</p>' +
                                    '</div>'+
                                    '<div class="dan_box_nr">' +
                                    '<div class="title">' +
                                    '<span class="an_left"></span><span class="an_cen">试题来源</span><span class="an_right"></span>' +
                                    '</div>'+
                                    test.doc_name+
                                    '</div>' +
                                    '</div>';
                            t_content +=    '</div>';
                        });
                    }
                } else if (test.if_choose == 2||test.if_choose == 3) {
                    t_content += '<div class="st_box">' +
                            '<div class="st_tm_box">' +
                            '<div class="title">'+
                            '<span class="ico_th fl">'+t_num+'</span>'+
                            '<span class="tit">'+test.test_title+'</span>' +
                            '<div class="bjjt"></div>' +
                            '</div>' +
                            '</div>';
                    if(test.test_options[0] != 'A'){
                        t_content += '<div class="st_wt_box">';
                        $.each(test.test_options,function(op_i,op){
                            var option = [op.substr(0,1),op.substr(2)];
                            t_content += '<p><span class="st_wt_bt">'+option[0]+'.</span>'+option[1]+'</p>';
                        });
                        t_content += '</div>';
                    }
                    t_content += '<div class="dt_box dt_box_xzt">' +
                            '<div class="fl xx_xzt_da">';
                    //试题作答情况
                    var answer = test.answer ? test.answer : '空';
                    t_content +=    '正确答案：<font color="#27a152">'+test.right_answer+'</font>，' +
                            '您的答案为 <font color="#00a0e9">'+answer+'</font> ，'+
                            '<font color="#69be83" style="display: '+(test.if_right == 2 ? 'inline' : 'none')+';"><strong>回答正确！</strong></font>'+
                            '<font color="#fe7676" style="display:'+(test.if_right == 2 ? 'none' : 'inline')+';"><strong>回答错误！</strong></font>' +
                            '</div>'+
                            '<div class="fr cz_an_box">' +
                            '<a class="an_jc" href="#" data="' + t_test_id +'">我要纠错</a>'+
                            '<a class="an_sc collect_test" href="javascript:;" data="' + t_test_id + '">收藏本题</a>'+
                            '<a class="an02 fr an_jx see_analysis" href="javascript:;">' +
                            '<span class="an_left"></span><div class="an_cen"><font color="#7e7e7e" class="fl mr5">查看解析</font><span style="margin-top:3px" class="fl"><img src="/Public/newAat/images/ico_jt01.png"></span></div><span class="an_right"></span>' +
                            '</a>'+
                            '</div>' +
                            '<div class="clear"></div>' +
                            '</div>';
                    //以下为试题解析知识点和来源
                    t_content += '<div class="dan_box" style="display: none;">' +
                            '<div class="dan_box_nr">' +
                            '<div class="title">' +
                            '<span class="an_left"></span><span class="an_cen">答案解析</span><span class="an_right"></span>' +
                            '</div>'+
                            test.analytic+
                            '</div>'+
                            '<div class="dan_box_nr dan_box_kd" style="display:block;">' +
                            '<div class="title">' +
                            '<span class="an_left"></span><span class="an_cen">考察考点</span><span class="an_right"></span>' +
                            '</div>'+
                            '<p>'+(test.kl_list?test.kl_list:'[暂无]')+'</p>' +
                            '</div>'+
                            '<div class="dan_box_nr">' +
                            '<div class="title">' +
                            '<span class="an_left"></span><span class="an_cen">试题来源</span><span class="an_right"></span>' +
                            '</div>'+
                            test.doc_name+
                            '</div>' +
                            '</div>';
                    t_content += '</div>';
                } else {
                    //大题
                    t_content += '<div class="st_box">' +
                            '<div class="st_tm_box">' +
                            '<div class="title">'+
                            '<span class="ico_th fl">'+t_num+'</span>'+
                            '<span class="tit">'+test.test_title+'</span>' +
                            '<div class="bjjt"></div>' +
                            '</div>' +
                            '</div>';
                    t_content +=    '<div class="dt_box dt_box_wdt">' +
                            '<div class="xx_wdt_da">' +
                            '<font color="#27a152">正确答案：</font>'+test.right_answer+
                            '</div>'+
                            '<div class="xx_wdt_da">' +
                            '<font color="#00a0e9">你的答案：</font>'+(test.answer?test.answer:'<font color="#00a0e9">空</font>')+
                            '</div>'+
                            '<div class="dt_box dt_box_xzt">'+
                            '<div class="cz_an_box fr">' +
                            '<a class="an_jc" href="javascript:;"  data="' + t_test_id +'">我要纠错</a>'+
                            '<a class="an_sc collect_test" href="javascript:;" data="' + t_test_id + '">收藏本题</a>'+
                            '<a class="an02 fr an_jx see_analysis" href="javascript:;">' +
                            '<span class="an_left"></span><div class="an_cen"><font color="#7e7e7e" class="fl mr5">查看解析</font><span style="margin-top:3px" class="fl"><img src="/Public/newAat/images/ico_jt01.png"></span></div><span class="an_right"></span>' +
                            '</a>'+
                            '</div>' +
                            '<div class="clear"></div>' +
                            '</div>';
                    //以下为试题解析知识点和来源
                    t_content +=        '<div class="dan_box" style="display: none;">' +
                            '<div class="dan_box_nr">' +
                            '<div class="title">' +
                            '<span class="an_left"></span><span class="an_cen">答案解析</span><span class="an_right"></span>' +
                            '</div>'+
                            test.analytic+
                            '</div>'+
                            '<div class="dan_box_nr dan_box_kd" style="display:none;">' +
                            '<div class="title">' +
                            '<span class="an_left"></span><span class="an_cen">考查考点</span><span class="an_right"></span>' +
                            '</div>'+
                            '<p>'+(test.kl_list?test.kl_list:'[暂无]')+'</p>' +
                            '</div>'+
                            '<div class="dan_box_nr">' +
                            '<div class="title">' +
                            '<span class="an_left"></span><span class="an_cen">试题来源</span><span class="an_right"></span>' +
                            '</div>'+
                            test.doc_name+
                            '</div>' +
                            '</div>';
                    t_content += '</div>';
                }
                totalNum+=parseInt(test.testNum);
                $('#test').append(t_content);
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
            $('#test div:first-child').show();
            $('.tabnr_01 a').click(function(){
                //试题显示转变
                $('#test').children().hide();
                $('#test div[data='+$(this).attr('data')+']').show();
                //Tab转换
                $('.tabnr_01 a').removeClass('this');
                $(this).addClass('this');
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
            //show显示答题卡按钮
            $('#db_dh_box .an_ctb02').show();
            //页面滚动
            $('#db_dh_box .an_go_top').click(function(){$('html,body').animate({scrollTop : 0}, 300);});
            $('#db_dh_box .an_ctb02').click(function(){$('html,body').animate({scrollTop : $(document).height()}, 300);});
        },

        showAnalysis:function(e){
            var str = '';
            var totalNum=0;
            $.each(e.exercise_info,function(i,k){
                var num = k.OrderID==0? k.Number: k.Number+'('+ k.OrderID+')';
                var color;
                if(k.IfRight == -1){
                    color = '';
                }else if(k.IfRight == 0){
                    color = 'th_yz';
                }else if(k.IfRight == 1){
                    color = 'th_cw';
                }else if(k.IfRight == 2){
                    color = 'th_zq';
                }
                totalNum++;
                str += '<a class="'+color+'" href="javascript:;">'+ totalNum +'</a>';
            });
            $('.dtk_box .th_box div:first-child').html(str);
            $('.dtk_box .th_sm .fl').html('总共 <strong>'+ e.exercise_info_amount.all+'</strong> 小题，正确<strong><font color="#69BE83"> '+e.exercise_info_amount.right+
                    ' </font></strong> 道题，错误<strong><font color="#FE7676"> '+e.exercise_info_amount.wrong+
                    ' </font></strong> 道题，未做<strong><font color="#AAAAAA"> '+e.exercise_info_amount.undo+
                    ' </font></strong> 道题，无法判断对错<strong><font color="#00A0E9"> '+e.exercise_info_amount.un_judge+
                    ' </font></strong> 道题');

            $('.dtk_box').show();
        },
        dialogButtonDisable:function(disable) {
            var button = $('.ui-dialog-buttonpane button');
            if (disable === true) {
                button.addClass('ui-state-disabled');
                button.attr('disabled', 'disabled');
            } else {
                button.removeAttr('disabled');
                button.removeClass('ui-state-disabled');
            }
        },
        playVideo:function(){
            AatCommon.initVideo();
        }
    };
    AatMyHomeworkAnswer.init();
});



</script>

</body>
</html>