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
<div id="content" class="w980 mc pt90 pb20">
<div class="box02 mb20 csbg_box">
<!--题目-->
<div class="zt_title"><div class="fl pt20 pl20">数据加载中...</div></div>
<!--信息栏-->
<div class="kzt_title">
    <div class="time_box fl"><span class="ico_time fl"></span></div>
    <div class="zttj_text fl">加载中...</div>
    <div class="kz_an_box">
        <a class="an01 fr" href="<?php echo U('PersonalReport/index','id='.$record_id);?>"><span class="an_left"></span><div class="an_cen">查看我的学情评估</div><span class="an_right"></span></a>
        <a class="an02 fr mr7" href="<?php echo U('ExerciseAnswer/index','id='.$record_id);?>"><span class="an_left"></span><span class="an_cen"><font color="#7e7e7e">答案解析</font></span><span class="an_right"></span></a>
        <a class="an02 fr  mr7" href="#" style="display: none;"><span class="an_left"></span>
            <div class="an_cen"><span style=" float:left; margin-top:1px; margin-right:5px;"><img src="/Public/newAat/images/ico_fx.png"></span><font color="#7e7e7e">分享</font></div>
            <span class="an_right"></span>
        </a>
    </div>
</div>
<!--测试分数、全站平均分、排名-->
<div class="tj_fs">
    <div class="tj_fs_box">
        <h5>本次测试您的分数：</h5>
        <p><span class="tj_sz ys_blue this_score">0</span><span class="tj_wz">分</span></p>
    </div>
    <div class="tj_fs_box ">
        <h5>全站平均得分：</h5>
        <p><span class="tj_sz ys_blue avg_score">0</span><span class="tj_wz">分</span></p>
    </div>
    <div class="tj_fs_box tj_fs_box03">
        <h5>已击败考生：</h5>
        <p><span class="tj_sz ys_blue rank_percent">0</span><span class="tj_wz">%</span></p>
    </div>
</div>
<!--答题情况统计-->
<div class="tj_tms">
    <div class="tj_tms_tb" id="score_info_pie">加载中...</div>
    <div class="fr tj_tms_box">
        <div class="title">加载中...</div>
        <div class="tj_tms_xx">
            <ul>
                <li class="this" data="right"><span class="fl ico_tl ico_tl_01"></span><div class="fl tj_tms_js"><span class="fl">您共做对</span><span class="fr right_amount">0道题</span></div></li>
                <li data="wrong"><span class="fl ico_tl ico_tl_02"></span><div class="fl tj_tms_js"><span class="fl">您共做错</span><span class="fr wrong_amount">0道题</span></div></li>
                <li data="undo"><span class="fl ico_tl ico_tl_03"></span><div class="fl tj_tms_js"><span class="fl">您共未做</span><span class="fr undo_amount">0道题</span></div></li>
                <li data="un_judge"><span class="fl ico_tl ico_tl_04"></span><div class="fl tj_tms_js"><span class="fl">无法判断对错</span><span class="fr un_judge_amount">0道题</span></div></li>
            </ul>
        </div>
    </div>
    <div class="clear"></div>
    <div class="th_box s_test_number" style="height: 152px;">
        <span style="color: #999999">加载中...</span>
        <div class="clear"></div>
    </div>
</div>
<!--历次测试分数-->
<div class="title02">测试得分趋势图</div>
<div class="dfqs_box" ><div id="scores_line" style="height: 223px;width: 924px;">加载中...</div></div>
<!--知识点考点统计信息-->
<div class="title02">本次测试考点分析</div>
<div class="this_kdfx_box">
    <ul class="list_zsd" id="kl_info">
        <li class="title">
            <div class="zsd_bt fl">专项/考点</div>
            <div class="zqsl_bt fc">正确数量</div>
            <div class="zql_bt fc">正确率</div>
            <div class="an_box fr">
                操作
            </div>
        </li>
        <li class="load">加载中...</li>
    </ul>
</div>
<div class="an03 pt20 mc"> <a href="<?php echo U('PersonalReport/index','id='.$record_id);?>">查看我的学情评估</a></div>
    <!--右侧快捷按钮-->
<div id="db_dh_box" style="display: none;">
    <a class="an_go_top" href="javascript:;"></a>
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

<script type="text/javascript">
$(document).ready(function () {
    var AatExerciseReport = {
        recordID : '<?php echo ($record_id); ?>',
        init:function(){
            this.exerciseInfo(this.recordID);//本次测试信息
            this.scoresLine();//历次测试分数
            this.klInfo(this.recordID);//知识点树状结构
            this.goTop();//返回TOP
        },
        exerciseInfo:function(recordID) {
            $.post(U('ExerciseReport/returnExerciseInfo'),{'id':recordID,times:Math.random()}, function (e) {
                if (e.status != 1) {
                    $('.zt_title div').html(e.data);
                } else {
                    var right_str = '';
                    redirect = e.data.jumpUrl;
                    if(e.data.right){
                        $.each(e.data.right, function (i, k) {
                            if(k.OrderID=='0') k.OrderID=1;
                            right_str += '<a href="javascript:;">' + (k.Start +parseInt(k.OrderID)-1)+'</a>';
                        });
                    }
                    var wrong_str = '';
                    if(e.data.wrong){
                        $.each(e.data.wrong, function (i, k) {
                            if(k.OrderID=='0') k.OrderID=1;
                            wrong_str += '<a href="javascript:;">' + (k.Start +parseInt(k.OrderID)-1)+'</a>';
                        });
                    }
                    var undo_str = '';
                    if(e.data.undo){
                        $.each(e.data.undo, function (i, k) {
                            if(k.OrderID=='0') k.OrderID=1;
                            undo_str += '<a href="javascript:;">' + (k.Start +parseInt(k.OrderID)-1)+'</a>';
                        });
                    }
                    var un_judge_str = '';
                    if(e.data.un_judge){
                        $.each(e.data.un_judge, function (i, k) {
                            if(k.OrderID=='0') k.OrderID=1;
                            un_judge_str += '<a href="javascript:;">' + (k.Start +parseInt(k.OrderID)-1)+'</a>';
                        });
                    }
                    //标题
                    $('.zt_title div').html('测试报告 — '+ e.data.style);//+'- '+$('.xk_this a').html()因存在学科列表加载速度慢于该JS获取的速度导致存在找不到的情况
                    //答题信息
                    $('.zttj_text').html('交卷时间：'+ e.data.end_time+'&nbsp;&nbsp;&nbsp;&nbsp;答题用时：<strong style="color:#00a0e9">'+ e.data.real_time+'</strong> 分钟');
                    //分数排名信息
                    $('.this_score').html(e.data.this_score);
                    $('.avg_score').html(e.data.avg_score);
                    $('.rank_percent').html(e.data.rank.percent);
                    //答题统计
                    var amount_info = '总共：<span class="ys_blue">'+e.data.all_amount+'</span> 题  您已经做了 <span class="ys_blue">'+
                            (e.data.all_amount- e.data.undo_amount)+'</span> 道题，占总题数的 <span class="ys_blue">'+
                            ((1-e.data.undo_amount/ e.data.all_amount)*100).toFixed()+'%</span>';
                    $('.tj_tms_box .title').html(amount_info);
                    $('.tj_tms_xx .right_amount').html(e.data.right_amount+'道题');
                    $('.tj_tms_xx .wrong_amount').html(e.data.wrong_amount+'道题');
                    $('.tj_tms_xx .undo_amount').html(e.data.undo_amount+'道题');
                    $('.tj_tms_xx .un_judge_amount').html(e.data.un_judge_amount+'道题');
                    //各类统计下题目序号以及点击切换
                    $('.s_test_number span').hide();
                    $('.s_test_number').html(right_str);
                    $('.tj_tms_xx ul li').click(function(){
                        $('.tj_tms_xx ul li').removeClass('this');
                        $(this).addClass('this');
                        if($(this).attr('data') == 'right') $('.s_test_number').html(right_str);
                        if($(this).attr('data') == 'wrong') $('.s_test_number').html(wrong_str);
                        if($(this).attr('data') == 'undo') $('.s_test_number').html(undo_str);
                        if($(this).attr('data') == 'un_judge') $('.s_test_number').html(un_judge_str);
                    });
                    //答题正确情况统计饼形图
                    var chart_score_info = echarts.init(document.getElementById('score_info_pie'));
                    chart_score_info.showLoading({
                        text: '正在努力的读取数据中...'   //loading话术
                    });
                    var option = {
                        title:{
                            text: '共'+ e.data.all_amount+'道题',
                            x: 'center',
                            y: 'center',
                            textStyle : {
                                color : '#888888',
                                fontFamily : '微软雅黑',
                                fontSize : 20,
                                fontWeight : 'bolder'
                            }
                        },
                        color: ['#69BE83', '#FE7676', '#E0E0E0', '#57C2F2'],
                        toolbox: {
                            show: true,
                            feature : {
                                saveAsImage : {show: true}
                            }
                        },
                        tooltip: {
                            trigger: 'item',
                            formatter: "{a} <br/>{b} : {c} ({d}%)"
                        },
                        series: [
                            {
                                name: '答题情况',
                                type: 'pie',
                                clockWise: true,
                                radius: ['50%', '70%'],
                                itemStyle : {
                                    normal : {
                                        label : {
                                            show : false
                                        },
                                        labelLine:{
                                            show:false
                                        }
                                    }
                                },
                                data: [
                                    {value: e.data.right_amount, name: '正确'},
                                    {value: e.data.wrong_amount, name: '错误'},
                                    {value: e.data.undo_amount, name: '未做'},
                                    {value: e.data.un_judge_amount, name: '无法判断'}
                                ]
                            }
                        ]
                    };
                    chart_score_info.setOption(option);
                }
            });
        },

        scoresLine:function() {
            $.post(U('ExerciseReport/returnScores'),{times:Math.random()}, function (e) {
                if (e.status !== 1) {
                    $('#scores_line').html(e.data);
                } else {
                    var y_data = [];
                    var x_data = [];
                    $.each(e.data, function (i, k) {
                        y_data.push(k.LoadTime);
                        x_data.push(k.Score);
                    });
                    var chart_scores_line = echarts.init(document.getElementById('scores_line'));
                    chart_scores_line.showLoading({
                        text: '正在努力的读取数据中...'   //loading话术
                    });
                    var option = {
                        toolbox: {
                            show: true,
                            feature : {
                                saveAsImage : {show: true}
                            }
                        },
                        tooltip: {
                            trigger: 'axis'
                        },
                        xAxis: [
                            {
                                type: 'category',
                                boundaryGap: false,
                                data: y_data.reverse()
                            }
                        ],
                        yAxis: [
                            {
                                type: 'value',
                                axisLabel: {
                                    formatter: '{value} 分'
                                }
                            }
                        ],
                        series: [
                            {
                                name: '测试得分',
                                type: 'line',
                                data: x_data.reverse()

                            }
                        ]
                    };
                    chart_scores_line.setOption(option);
                }
            });
        },

        klInfo:function(recordID) {
            $.post(U('ExerciseReport/returnKlInfo'),{'id':recordID,'level':2,times:Math.random()}, function (e) {
                if (e.status !== 1) {
                    $('#kl_info').html(e.data);
                } else {
                    var str = '';
                    $.each(e.data,function(i,k){
                        //判断是否有下级知识点
                        var ico = k.sub?'ico_zd_01':'ico_zd_03';
                        str += '<li><a class="ico_zd '+ico+' fl"></a><div klid="'+ k.KlID+'" class="zsd_bt fl" style="cursor: pointer">'+ k.KlName+'</div>'+
                                '<div class="zqsl_bt fc">'+ k.Amount+'</div><div class="zql_bt fc">'+ k.Rate+'</div>'+
                                '<div class="an_box fr" klid="'+ k.KlID+'"><a class="an02" href="javascript:;"><span class="an_left"></span>'+
                                '<span class="an_cen">我要练习</span><span class="an_right"></span></a></div></li>';
                        if(k.sub){
                            $.each(k.sub,function(j,m){
                                str += '<li class="lidj02 sub_kl_id_'+ k.KlID+'" style="display: none;"><a class="ico_zd ico_zd_03 fl"></a><div class="zsd_bt fl">'+ m.KlName+'</div>'+
                                        '<div class="zqsl_bt fc">'+ m.Amount+'</div><div class="zql_bt fc">'+ m.Rate+'</div>'+
                                        '<div class="an_box fr" klid="'+ m.KlID+'"><a class="an02" href="javascript:;"><span class="an_left"></span>'+
                                        '<span class="an_cen">我要练习</span><span class="an_right"></span></a></div></li>';
                            });
                        }
                    });
                    $('#kl_info .load').hide();
                    $('#kl_info').append(str);
                }
            });
            //展开事件绑定
            $('#kl_info').on('click','.zsd_bt,.ico_zd',function(){
                $(this).parent().find('.ico_zd_01').switchClass('ico_zd_01','ico_zd_02',1);
                $(this).parent().find('.ico_zd_02').switchClass('ico_zd_02','ico_zd_01',1);
                var id = $(this).parent().find('.zsd_bt').attr('klid');
                $('.sub_kl_id_'+id).toggle('blind');
            });
            //点击开始做题事件绑定
            $('#kl_info').on('click','.an_box',function(){
                var kl_id = $(this).attr('klid');
                //隐藏出题按钮
                var button = $(this);
                button.hide().after('<div class="fr load" style="width:87px;position: relative;"><div style="position: absolute;left:15px;top: 5px;">请稍等...</div></div>');
                //显示进度条
                var bar  = $(this).parent().children('.load');
                bar.progressbar({
                    value: false
                });
                $.post(U('Default/ajaxGetTest'),{'id':2,'SubjectID': AatCommon.getSubjectID(),'KID':kl_id,times:Math.random()},function(e){
                    if(e.status == 1){
                        window.location.href=U('Exercise/index?id='+ e.data.record_id);
                    }else{
                        //删除进度条
                        bar.remove();
                        //显示出题按钮
                        button.show();
                        //显示错误信息
                        alert(e.data);
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
        }
    };
    AatExerciseReport.init();
});
</script>
<script src="/Public/plugin/echarts-plain.js" type="text/javascript"></script>
</body>
</html>