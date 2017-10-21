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

    <!--<script type="text/javascript" src="/Public/newAat/js/exercise.js<?php echo C(WLN_UPDATE_FILE_DATE);?>"></script>-->
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
    <div class="fl pt20 pl20">正在加载试题，请稍候...</div>
    <a class="an01 fr" href="<?php echo U('ExerciseReport/index','id='.$record_id);?>" style="margin-top: 15px;margin-right: 20px;"><span class="an_left"></span><div class="an_cen"><span style=" float:left; margin-top:5px; margin-right:5px;"><img src="/Public/newAat/images/ico_jj.png"></span>查看本次测试报告</div><span class="an_right"></span></a>
</div>
<!--tab题型-->
<div class="tabnav01 tabnav02" style="display: none;">
    <div class="left_an sub_an" style="display: none;"><a href="javascript:;"></a></div>
    <div class="right_an sub_an" style="display: none;"><a href="javascript:;"></a></div>
    <div class="tabnr_01" style="line-height:30px;margin-left:20px;">
    </div>
</div>
<!--提醒介绍-->
<div class="this_tab_title" style="display: none;">
</div>
<!--一以下为试题-->
<div id="test">
</div>
<!--右侧快捷按钮-->
<div class="fixedBox">
    <div class="fixedBtn">
        <div id="" class="db_dh_box">
            <a class="an_go_top" href="javascript:"></a>
            <a class="an_ctb02" href="javascript:" style="display: block;">答题卡</a>
        </div>
    </div>
</div>
<!--以下是答题卡-->
<div class="answerCard" style="display:none;">
<div class="ansBox">
    <div class="onePart">
    <h3 class="title">答题卡</h3>
    <div class="th_box inBox">
        <a class="th_yz" href="#">1</a>
        <a class="th_cw" href="#">2</a>
        <a class="th_zq" href="#">3</a>
        <a href="#">4</a><a class="th_yz" href="#">1</a>
        <a class="th_cw" href="#">2</a>
        <a class="th_zq" href="#">3</a>
        <a href="#">4</a><a class="th_yz" href="#">1</a>
        <a class="th_cw" href="#">2</a>
        <a class="th_zq" href="#">3</a>
        <a href="#">4</a><a class="th_yz" href="#">1</a>
        <a class="th_cw" href="#">2</a>
        <a class="th_zq" href="#">3</a>
        <a href="#">4</a>
        <div class="clear"></div>
    </div>
    </div>
    <div class="th_sm">
        <span class="fl">
            总共 <strong>24</strong>
            小题，正确 <strong><font color="#69BE83">4</font></strong> 
            道题，错误
            <strong><font color="#FE7676">6</font></strong> 
            道题，未做
            <strong>
                <font color="#AAAAAA">14</font>
            </strong>
            道题，无法判断对错
            <strong>
                <font color="#00A0E9">0</font>
            </strong>
            道题
        </span>
        <span class="fr sm_ys">
            <span> <b class="ys_lv"></b> <font>做对</font>
            </span>
            <span> <b class="ys_hong"></b> <font>做错</font>
            </span>
            <span>
                <b class="ys_hui"></b>
                <font>未做</font>
            </span>
            <span>
                <b class="ys_lan"></b>
                <font>无法判断对错</font>
            </span>
        </span>
    </div>
</div>
</div>
<!-- 答题卡结束 -->
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
<div id='evaluation' title='自评分'>
    <p id='evalInformation'></p>
</div>
<script type="text/javascript">
$(document).ready(function () {
    var AatExerciseAnswer = {
        recordID : '<?php echo ($record_id); ?>',
        aatTestStyle : 0,
        evaluateDescription : '', //评分提示框描述
        init:function(){
            this.getExerciseAnswerData();
            this.testCollectSave();
            this.showErrorPre();
        },
        //显示上一页的错误
        showErrorPre:function(){
            var aatTestStyle='<?php echo ($_REQUEST['AatTestStyle']); ?>';
            if(aatTestStyle=='1'){
                    $.aDialog({
                        width:428,
                        height:223,
                        title:'提示信息',
                        success:function(){
                            $('#dialogAat .content').append('<p style="padding:15px 0;text-align: center;font-size: 16px;">请完成自评，再查看学情评估！</p>');
                        }
                    });
                    $.aDialog('hideLoading');
            }
        },
        //获取页面所需内容
        getExerciseAnswerData:function(){
            var self = this;
            //服务器返回的题目json数据并解析
            $.post(U('ExerciseAnswer/returnTestList'),{'id':self.recordID,'phone':'0',times:Math.random()}, function (e) {
                if (e.status !== 1) {
                    $('.zt_title div:first-child').html(e.data);
                } else {
                    self.aatTestStyle = e.data.exercise_info_amount.aatTestStyle;
                    redirect = e.data.exercise_info_amount.jumpUrl;
                    if(1 == self.aatTestStyle && e.data.exercise_info_amount.evaluateDescription){
                        self.evaluateDescription = e.data.exercise_info_amount.evaluateDescription;
                    }
                    //顺序不能改变
                    //1显示页面做题统计，试题等数据信息 需要先调用showData生成页面数据
                    self.showData(e.data);
                    //2显示标题和信息栏、Tab、右侧向上和答题卡
                    self.showTop(e.data);
                    //视频播放
                    AatCommon.initVideo();
                }
            });
        },
        //展示页面信息
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
        //展示页面头部信息
        showTop:function(e) {
            var self = this;
            $('.zt_title div:first-child').html(e.style+'—答案解析');
            $('.kzt_title').show();
            //显示题型Tab
            self.showTestTab(e);
            //显示右侧向上按钮和答题卡
            $('.db_dh_box .an_go_top').click(function(){$('html,body').animate({scrollTop : 0}, 300);});
            //试题纠错
            $('.an_jc').on('click',function(){
                var testID=$(this).attr('data');
                AatCommon.correction(testID,false);
            })
            $('.eval').on('click', function(){
                var that = $(this);
                var answerid = that.attr('answerid');
                if(!answerid || answerid == 0){
                    alert('该试题无作答记录，无法进行自评分！');
                    return false;
                }
                var buttons = {
                    "确定": function () {
                        var current = $(this);
                        var score = current.find('.scoreBox select').val();
                        var data = {
                            'record' : self.recordID,
                            'score' : score,
                            'answer' : answerid,
                            times:Math.random()
                        }
                        $.post(U('Aat/ExerciseAnswer/evaluateTest'), data, function(e){
                            if (e.status == 1) {
                                that.attr('evaluationScore', score);
                                var html = that.html();
                                if(!/\[.*\]/.test(html)){
                                    html += '[ '+score+' 分]';
                                }else{
                                    html = html.replace(/\[.*\]/, '[ '+score+' 分]');
                                }
                                that.html(html.replace('未评分','自评分'));
                                current.dialog("close");
                            } else {
                                alert(e.data);
                                current.dialog("close");
                            }
                        });
                    },
                    '取消': function () {
                        $(this).dialog("close");
                    }
                };
                $('#evaluation').dialog({
                    modal: true,
                    draggable: false,
                    height:200,
                    width:250,
                    buttons: buttons,
                    open: function () {
                        var score = that.attr('score');
                        var evaluationScore = that.attr('evaluationScore');
                        if(!evaluationScore){
                            evaluationScore = score;
                        }
                        var html = '<select name="" id="">';
                        var point = that.attr('point');
                        if(1 == point){
                            point = 0.5;
                        }else{
                            point = 1;
                        }
                        for(; score>=0; score-=point){
                            var selected = ' ';
                            if(score == evaluationScore){
                                selected = " selected='selected'";
                            }
                            html += '<option value="'+score+'"'+selected+'>'+score+'分</option>';
                        }
                        html += '</select>';
                        var suggest = '<div>'+
                        '<p class="title">请根据密卷评分标准，进行自评！<br><font color="red">提示：若不自评，系统默认为0分。祝2016高考顺利！</font></p>'+
                                          '<div class="scoreBox">自评分数：'+html+'</div>'+
                                          '<div id="warn"></div>'+
                                      '</div>';
                        if(self.evaluateDescription){
                            suggest = "<div><p>"+self.evaluateDescription+"</p><div class='scoreBox'>自评分数："+html+"</div></div>";
                        }
                        $('#evalInformation').html(suggest);
                    }
                });
            })
        },
        //展示试题内容
        showTest:function(e) {
            var that = this;
            var totalNum=0; //试题序号
            $.each(e.test, function (i, test) {
                //一道试题所有内容Html tContent
                var tContent = '';
                var tNum = totalNum+1;//试题序号
                var tTestID = test.test_id;
                var tTitle = test.test_title;
                var tScore = test.score || '0';
                var point = test.point;
                var isAatTestStyle = (that.aatTestStyle === 1);
                if(!tTitle){
                    //如果tTitle没有值则跳出循环
                    return true;
                }
                var tType = test.test_type;
                if (test.if_choose == 1) {
                    //复合题
                    tContent += '<div class="tw_box">' +
                            '    <div class="title">'+
                            '        <span class="ico_tw fl">'+tNum+'-'+(tNum+parseInt(test.testNum)-1)+'题</span>'+
                            '        <span class="fl tit">'+''+'</span>' +
                            '    </div>'+
                            '    <div class="nr_box">'+tTitle+'</div>' +
                            '</div>';
                    if(typeof(test.sub) != 'undefined'){
                        var subScore = tScore.split(/,/);
                        var index = 0;
                        $.each(test.sub, function (iSub, subTest) {
                            tContent += '<div class="st_box">' +
                                    '    <div class="st_tm_box">' +
                                    '        <div class="title">'+
                                    '            <span class="ico_th fl locate-'+test.number+'-'+subTest.order+'" data-test-type="'+test.test_type+'">'+
                                    (tNum-1+parseInt(subTest.order))+
                                    '            </span>'+
                                    '            <span class="tit">'+
                                    subTest.sub_title+
                                    '            </span>' +
                                    '        <div class="bjjt"></div>' +
                                    '    </div>' +
                                    '</div>';
                            if (subTest.if_choose > 1) {
                                //复合体中选择题
                                if(subTest.sub_options[0] != 'A'){
                                    //如果能够分割选项，不是写死的ABCD等，则显示
                                    tContent += '<div class="st_wt_box">';
                                    $.each(subTest.sub_options,function(op_i,op){
                                        tContent += '<p><span class="st_wt_bt">'+op.substr(0,1)+'.</span>&nbsp;'+op.substr(2)+'</p>';
                                    });
                                    tContent += '</div>';
                                }
                                var answer = subTest.sub_answer ? subTest.sub_answer : '空';
                                tContent += '<div class="dt_box dt_box_xzt">' +
                                        '    <div class="fl xx_xzt_da">';
                                tContent += '        正确答案：<font color="#27a152">'+subTest.sub_right_answer+'</font>' ;
                                if(test.ifCanDo==1) {
                                    tContent += '，您的答案为 <font color="#00a0e9">' + answer + '</font> ，' +
                                            '<font color="#69be83" style="display: ' + (subTest.if_right == 2 ? 'inline' : 'none') + ';"><strong>回答正确！</strong></font>' +
                                            '<font color="#fe7676" style="display:' + (subTest.if_right == 2 ? 'none' : 'inline') + ';"><strong>回答错误！</strong></font>';
                                }
                                tContent += '</div>';
                                //添加打分
                                var evalHtml = '';
                                if(isAatTestStyle){
                                    var score = subTest.answer_score;
                                    if(/\.0/.test(score.toString())){
                                        score = parseInt(score);
                                    }
                                    if(score < 0){
                                        score = 0;
                                    }
                                    
                                    evalHtml = '<a class="" point="'+point+'" score="'+subTest.score+'" style="color:red" href="javascript:;" data="' + tTestID +'">[ '+score+' 分]</a>&nbsp;&nbsp;';
                                    
                                    if(subTest.if_choose==2){
                                        var str = '自评分';
                                        if(/\.0/.test(subTest.answer_score.toString())){
                                            subTest.answer_score = parseInt(subTest.answer_score);
                                        }
                                        var evaluationScore = subTest.answer_score;
                                        var answerid = subTest.answer_id;
                                        if(evaluationScore >= 0){
                                            str += '[ '+evaluationScore+' 分]';
                                        }else{
                                            str = '未评分';
                                            subTest.answer_score = evaluationScore = 0;
                                        }
                                        var className = 'eval';
                                        if(!answerid || answerid == 0){
                                            className = '';
                                            str = '[ 0 分]';
                                        }
                                        evalHtml = '<a class="'+className+'" answerid="'+answerid+'" point="'+point+'" evaluationScore="'+evaluationScore+'" score="'+subTest.score+'" style="color:red" href="javascript:;" data="' + tTestID +'">'+str+'</a>&nbsp;&nbsp;';
                                    }
                                }
                                tContent += '<div class="fr cz_an_box">' + evalHtml +
                                        '    <a class="an_jc" href="javascript:;" data="' + tTestID +'">我要纠错</a>'+
                                        '    <a class="an_sc collect_test" href="javascript:;" data="' + tTestID  + '">收藏本题</a>'+
                                        '    <a class="an02 fr an_jx see_analysis" href="javascript:;">' +
                                        '        <span class="an_left"></span>' +
                                        '        <div class="an_cen">' +
                                        '            <font color="#7e7e7e" class="fl mr5">查看解析</font>' +
                                        '            <span style="margin-top:3px" class="fl">' +
                                        '                <img src="/Public/newAat/images/ico_jt01.png">' +
                                        '            </span>' +
                                        '        </div>' +
                                        '        <span class="an_right"></span>' +
                                        '    </a>'+
                                        '</div>' +
                                        '<div class="clear"></div>' +
                                        '</div>';
                            }else if(subTest.if_choose == 0){
                                //复合体中大题
                                tContent += '<div class="dt_box dt_box_wdt">' +
                                        '    <div class="xx_wdt_da">' +
                                        '        <font color="#27a152">正确答案：</font>'+subTest.sub_right_answer+
                                        '    </div>';
                                if(test.ifCanDo==1) {
                                    tContent += '<div class="xx_wdt_da">' +
                                            '    <font color="#00a0e9">你的答案：</font>' + (subTest.sub_answer ? subTest.sub_answer : '<font color="#00a0e9">空</font>') +
                                            '</div>';
                                }
                                var evalHtml = '';
                                if(isAatTestStyle){
                                    var str = '自评分';
                                    if(/\.0/.test(subTest.answer_score.toString())){
                                        subTest.answer_score = parseInt(subTest.answer_score);
                                    }
                                    var evaluationScore = subTest.answer_score;
                                    var answerid = subTest.answer_id;
                                    if(evaluationScore >= 0){
                                        str += '[ '+evaluationScore+' 分]';
                                    }else{
                                        str = '未评分';
                                        subTest.answer_score = evaluationScore = 0;
                                    }
                                    var className = 'eval';
                                    if(!answerid || answerid == 0){
                                        className = '';
                                        str = '[ 0 分]';
                                    }
                                    evalHtml = '<a class="'+className+'" answerid="'+answerid+'" point="'+point+'" evaluationScore="'+evaluationScore+'" score="'+subTest.score+'" style="color:red" href="javascript:;" data="' + tTestID +'">'+str+'</a>&nbsp;&nbsp;';
                                }
                                tContent+= '<div class="dt_box dt_box_xzt">'+ 
                                        '    <div class="cz_an_box fr">' +evalHtml+
                                        '        <a class="an_jc"  href="javascript:;" data="' + tTestID + '">我要纠错</a>'+
                                        '        <a class="an_sc collect_test" href="javascript:;" data="' + tTestID +  '">收藏本题</a>'+
                                        '        <a class="an02 fr an_jx see_analysis" href="javascript:;">' +
                                        '            <span class="an_left"></span>' +
                                        '            <div class="an_cen">' +
                                        '                <font color="#7e7e7e" class="fl mr5">查看解析</font>' +
                                        '                <span style="margin-top:3px" class="fl">' +
                                        '                    <img src="/Public/newAat/images/ico_jt01.png">' +
                                        '                </span>' +
                                        '            </div><span class="an_right"></span>' +
                                        '        </a>'+
                                        '    </div>' +
                                        '    <div class="clear"></div>' +
                                        '</div>';
                            }
                            //以下为试题解析知识点和来源
                            tContent += '<div class="dan_box" style="display: none;">' +
                                    '    <div class="dan_box_nr">' +
                                    '        <div class="title">' +
                                    '            <span class="an_left"></span>' +
                                    '            <span class="an_cen">答案解析</span>' +
                                    '            <span class="an_right"></span>' +
                                    '        </div>'+
                                    subTest.sub_analytic+
                                    '    </div>'+
                                    '    <div class="dan_box_nr dan_box_kd" style="display:block;">' +
                                    '        <div class="title">' +
                                    '            <span class="an_left"></span>' +
                                    '            <span class="an_cen">考查考点</span>' +
                                    '            <span class="an_right"></span>' +
                                    '        </div>'+
                                    '        <p>'+(test.kl_list?test.kl_list:'[暂无]')+'</p>' +
                                    '    </div>'+
                                    '    <div class="dan_box_nr">' +
                                    '        <div class="title">' +
                                    '            <span class="an_left"></span>' +
                                    '            <span class="an_cen">试题来源</span>' +
                                    '            <span class="an_right"></span>' +
                                    '        </div>'+
                                    test.doc_name+
                                    '    </div>' +
                                    '</div>';
                            tContent += '</div>';
                        });
                    }
                } else if (test.if_choose == 2||test.if_choose == 3) {
                    tContent += '<div class="st_box">' +
                            '<div class="st_tm_box">' +
                            '<div class="title">'+
                            '<span class="ico_th fl locate-'+test.number+'-0'+'" data-test-type="'+test.test_type+'">'+tNum+'</span>'+
                            '<span class="tit">'+test.test_title+'</span>' +
                            '<div class="bjjt"></div>' +
                            '</div>' +
                            '</div>';

                    if(test.test_options[0] != 'A'){
                        tContent += '<div class="st_wt_box">';
                        $.each(test.test_options,function(op_i,op){
                            var option = [op.substr(0,1),op.substr(2)];
                            tContent += '<p><span class="st_wt_bt">'+option[0]+'.</span>&nbsp;'+option[1]+'</p>';
                        });
                        tContent += '</div>';
                    }
                    tContent += '<div class="dt_box dt_box_xzt">' +
                            '<div class="fl xx_xzt_da">';
                    //试题作答情况
                    var answer = test.answer ? test.answer : '空';
                    tContent +=    '正确答案：<font color="#27a152">'+test.right_answer+'</font>' ;
                    if(test.ifCanDo==1) {
                        tContent += '，您的答案为 <font color="#00a0e9">' + answer + '</font> ，' +
                                '<font color="#69be83" style="display: ' + (test.if_right == 2 ? 'inline' : 'none') + ';"><strong>回答正确！</strong></font>' +
                                '<font color="#fe7676" style="display:' + (test.if_right == 2 ? 'none' : 'inline') + ';"><strong>回答错误！</strong></font>';
                    }
                    tContent += '</div>';
                    var evalHtml = '';
                    if(isAatTestStyle){
                        var score = test.answer_score;
                        if(/\.0/.test(score.toString())){
                            score = parseInt(score);
                        }
                        if(score < 0){
                            score = 0;
                        }
                        evalHtml = '<a class="" point="'+point+'" score="'+tScore+'" style="color:red" href="javascript:;" data="' + tTestID +'">[ '+score+' 分]</a>&nbsp;&nbsp;';
                        
                        if(test.if_choose==2){
                            var evaluationScore = test.answer_score;
                            var str = '自评分';
                            if(/\.0/.test(evaluationScore.toString())){
                                evaluationScore = parseInt(evaluationScore);
                            }
                            if(evaluationScore < 0){
                                str = '未评分';
                                evaluationScore = 0;
                            }else if(evaluationScore >= 0){
                                str += '[ '+evaluationScore+' 分]';
                            }
                            var className = 'eval';
                            if(!test.answer_id || test.answer_id == 0){
                                className = '';
                                str = '[ 0 分]';
                            }
                            evalHtml = '<a class="'+className+'" answerid="'+test.answer_id+'" point="'+point+'" evaluationScore="'+evaluationScore+'" score="'+tScore+'" style="color:red" href="javascript:;" data="' + tTestID +'">'+str+'</a>&nbsp;&nbsp;';
                        }
                    }
                    tContent += '<div class="fr cz_an_box">' + evalHtml +
                            '<a class="an_jc" href="javascript:;" data="' + tTestID +'">我要纠错</a>'+
                            '<a class="an_sc collect_test" href="javascript:;" data="' + tTestID +  '">收藏本题</a>'+
                            '<a class="an02 fr an_jx see_analysis" href="javascript:;">' +
                            '<span class="an_left"></span>' +
                            '<div class="an_cen">' +
                            '<font color="#7e7e7e" class="fl mr5">查看解析</font><span style="margin-top:3px" class="fl"><img src="/Public/newAat/images/ico_jt01.png"></span>' +
                            '</div>' +
                            '<span class="an_right"></span>' +
                            '</a>'+
                            '</div>' +
                            '<div class="clear"></div>' +
                            '</div>';
                    //以下为试题解析知识点和来源
                    tContent += '<div class="dan_box" style="display: none;">' +
                            '<div class="dan_box_nr">' +
                            '<div class="title">' +
                            '<span class="an_left"></span><span class="an_cen">答案解析</span><span class="an_right"></span>' +
                            '</div>'+
                            test.analytic+
                            '</div>'+
                            '<div class="dan_box_nr dan_box_kd" style="display:block;">' +
                            '<div class="title">' +
                            '<span class="an_left"></span>' +
                            '<span class="an_cen">考查考点</span>' +
                            '<span class="an_right"></span>' +
                            '</div>'+
                            '<p>'+(test.kl_list?test.kl_list:'[暂无]')+'</p>' +
                            '</div>'+
                            '<div class="dan_box_nr">' +
                            '<div class="title">' +
                            '<span class="an_left"></span>' +
                            '<span class="an_cen">试题来源</span>' +
                            '<span class="an_right"></span>' +
                            '</div>'+
                            test.doc_name+
                            '</div>' +
                            '</div>';
                    tContent += '</div>';
                } else {
                    //大题
                    tContent += '<div class="st_box">' +
                            '<div class="st_tm_box">' +
                            '<div class="title">'+
                            '<span class="ico_th fl locate-'+test.number+'-0'+'" data-test-type="'+test.test_type+'">'+tNum+'</span>'+
                            '<span class="tit">'+test.test_title+'</span>' +
                            '<div class="bjjt"></div>' +
                            '</div>' +
                            '</div>';

                    tContent += '<div class="dt_box dt_box_wdt">' +
                            '    <div class="xx_wdt_da">' +
                            '        <font color="#27a152">正确答案：</font>' + test.right_answer +
                            '</div>' ;
                    if(test.ifCanDo==1) {
                        tContent += '<div class="xx_wdt_da">' +
                                '    <font color="#00a0e9">你的答案：</font>' + (test.answer ? test.answer : '<font color="#00a0e9">空</font>') +
                                '</div>';
                    }
                    var evalHtml = '';
                    if(isAatTestStyle){
                        var evaluationScore = test.answer_score;
                        var str = '自评分';
                        if(/\.0/.test(evaluationScore.toString())){
                            evaluationScore = parseInt(evaluationScore);
                        }
                        if(evaluationScore < 0){
                            str = '未评分';
                            evaluationScore = 0;
                        }else if(evaluationScore >= 0){
                            str += '[ '+evaluationScore+' 分]';
                        }
                        var className = 'eval';
                        if(!test.answer_id || test.answer_id == 0){
                            className = '';
                            str = '[ 0 分]';
                        }
                        evalHtml = '<a class="'+className+'" answerid="'+test.answer_id+'" point="'+point+'" evaluationScore="'+evaluationScore+'" score="'+tScore+'" style="color:red" href="javascript:;" data="' + tTestID +'">'+str+'</a>&nbsp;&nbsp;';
                    }
                    tContent+='<div class="dt_box dt_box_xzt">'+
                            '<div class="cz_an_box fr">' + evalHtml +
                            '<a class="an_jc" href="javascript:;"  data="' + tTestID +'">我要纠错</a>'+
                            '<a class="an_sc collect_test" href="javascript:;" data="' + tTestID + '">收藏本题</a>'+
                            '<a class="an02 fr an_jx see_analysis" href="javascript:;">' +
                            '<span class="an_left"></span>' +
                            '<div class="an_cen">' +
                            '<font color="#7e7e7e" class="fl mr5">查看解析</font><span style="margin-top:3px" class="fl"><img src="/Public/newAat/images/ico_jt01.png"></span>' +
                            '</div>' +
                            '<span class="an_right"></span>' +
                            '</a>'+
                            '</div>' +
                            '<div class="clear"></div>' +
                            '</div>';
                    //以下为试题解析知识点和来源
                    tContent +=        '<div class="dan_box" style="display: none;">' +
                            '<div class="dan_box_nr">' +
                            '<div class="title">' +
                            '<span class="an_left"></span><span class="an_cen">答案解析</span><span class="an_right"></span>' +
                            '</div>'+
                            test.analytic+
                            '</div>'+
                            '<div class="dan_box_nr dan_box_kd">' +
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
                            '</div>'+
                            '</div>';
                    tContent += '</div>';

                }
                totalNum+=parseInt(test.testNum);
//                $('#test div[data='+tType+']').append(tContent);
                $('#test').append(tContent);
            });
        },
        //展示头部试题类型选项卡
        showTestTab:function(e){
            var str = '试题数量：';
            $.each(e.type,function(i,k){
                var amount = k.TypesAmount;
                str += '<span data="'+ k.TypesID+'">'+ k.TypesName+'['+amount+']</span>';
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
//            $('.tabnr_01 a').click(function(){
//                //试题显示转变
//                $('#test').children().hide();
//                $('#test div[data='+$(this).attr('data')+']').show();
//                //Tab转换
//                $('.tabnr_01 a').removeClass('this');
//                $(this).addClass('this');
//            });
        },

        showAnalysis:function(e){
            var str = '';
            var totalNum=0;
            $.each(e.exercise_info,function(i,k){
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
                str += '<a class="'+color+'" href="javascript:;" data-locate="'+ k.Number+'-'+ k.OrderID+'">'+ totalNum +'</a>';
            });
            $('.answerCard .inBox').html(str);
            $('.answerCard .th_sm .fl').html('总共 <strong>'+ e.exercise_info_amount.all+'</strong> 小题，正确<strong><font color="#69BE83"> '+e.exercise_info_amount.right+
                    ' </font></strong> 道题，错误<strong><font color="#FE7676"> '+e.exercise_info_amount.wrong+
                    ' </font></strong> 道题，未做<strong><font color="#AAAAAA"> '+e.exercise_info_amount.undo+
                    ' </font></strong> 道题，无法判断对错<strong><font color="#00A0E9"> '+e.exercise_info_amount.un_judge+
                    ' </font></strong> 道题');
            //点击跳转到答题卡
            $('.an_ctb02').show().click(function(){
                var answerCard = $('.answerCard'),content = $('#content');
                var answerCardHeight = answerCard.height();
                if(answerCard.is(':hidden')){//如果当前隐藏，则增加marginBottom
                    content.css('margin-bottom',answerCardHeight-120);//减去footer高度，如果底部修改了，这里需要修改
                }else{//如果当前显示，则减少marginBottom
                    content.css('margin-bottom',0);
                }
                answerCard.toggle(300);
            });
            //答题卡跳转
            var locateAnimate = function(locate){
                var locateClass = $('.locate-'+locate);
                var testType = locateClass.attr('data-test-type');
                $('.tabnr_01 a[data='+testType+']').click();
                $('html,body').animate({scrollTop:locateClass.offset().top-87},500);//需要减去顶部高度73和半个行高
            };
            $(document).on('click','.answerCard a',function(){
                locateAnimate($(this).attr('data-locate'));
            });

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
        testCollectSave:function(){
        $(document).on('click', '.collect_test', function () {
            var data = {};
            data['id'] = $(this).attr('data');
            $.ajax({
                type: 'POST',
                cache: false,
                data: data,
                url: U('TestCollect/save'),
                success: function (e) {
                    if (e.status == 0) {
                        alert(e.data);
                    } else {
                        alert(e.data);
                    }
                }
            });
        })
    }
    };
    AatExerciseAnswer.init();
    $('#test').on('click','.st_box img',function(){
        $(this).bigImage();
    });
});

</script>

</body>
</html>