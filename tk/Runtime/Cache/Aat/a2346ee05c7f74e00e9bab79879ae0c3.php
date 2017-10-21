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

    <script type="text/javascript" src="/Public/plugin/artTemplate-3.0.3.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript">
        //artTemplate配置开始结束标签避免和Thinkphp模版标签冲突
        template.config('openTag','{%');
        template.config('closeTag','%}');
    </script>
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
        <a class="an01 fr" href="<?php echo U('PersonalReport/index');?>" style="display: none;"><span class="an_left"></span><div class="an_cen">保存我的学情评估</div><span class="an_right"></span></a>
        <a class="an02 fr  mr7" href="#" style="display: none;"><span class="an_left"></span>
            <div class="an_cen"><span style=" float:left; margin-top:1px; margin-right:5px;"><img src="/Public/newAat/images/ico_fx.png"></span><font color="#7e7e7e">分享</font></div>
            <span class="an_right"></span>
        </a>
    </div>
</div>
<!--测试分数、全站平均分、排名-->
<div class="tj_fs">
    <div class="tj_fs_box">
        <h5>您的快速预测分：</h5>
        <p><span class="tj_sz ys_blue s_quick_score">?</span><span class="tj_wz">分</span></p>
        <p class="ts_xxbox quickScore"><span class="ts_wztext"></span></p>
        <div class="Helpexplain ico_Helpexplain" title="最后一次测试的得分"></div>
    </div>
    <div class="tj_fs_box ">
        <h5>您的精准预测分：</h5>
        <p><span class="tj_sz ys_blue s_ok_score">?</span><span class="tj_wz">分</span></p>
        <p class="ts_xxbox okScore"><span class="ts_wztext"></span></p>
        <div class="Helpexplain ico_Helpexplain" title="基于最近5次有效测试的预测分数"></div>
    </div>
    <div class="tj_fs_box tj_fs_box03">
        <h5>您当前的答题量：</h5>
        <p><span class="tj_sz ys_blue s_test_amount">?</span><span class="tj_wz">道</span></p>
        <p class="ts_xxbox testAmount"><span class="ts_wztext"></span></p>
        <div class="Helpexplain ico_Helpexplain hide"></div>
    </div>
</div>
<!--预测分趋势line图-->
<div class="nlpg_box02">
    <div class="fl box_left">
        <div class="title02">快速预测分趋势表   <div class="Helpexplain ico_Helpexplain hide"></div></div>
        <div class="dfqs_box">
            <div id="exercise_score_line" style="width: 420px;height: 250px;">
                <p><span class="tj_imgxl"></span></p>
                <p class="ts_xxbox"><span class="ts_wztext">请您先进行测试！</span></p>
            </div>
        </div>
    </div>
    <div class="fr box_right">
        <div class="title02">用户精准预测分趋势表  <div class="Helpexplain ico_Helpexplain hide"></div></div>
        <div class="dfqs_box">
            <div id="forecast_score_line" style="width: 420px;height: 250px;">
                <p><span class="tj_imgxl"></span></p>
                <p class="ts_xxbox"><span class="ts_wztext">您还没有生成精准预测分！</span></p>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<!--历次测试分数-->
    <div class="nlpg_box02">
        <div class="fl box_left">
            <div class="title02">知识雷达表  <div class="Helpexplain ico_Helpexplain"
                                             title="最近1-3次代表该知识点下最后三次生成能力值的大小；
                                             能力值取值-3至3数值越大能力值越高，雷达图折线越接近外部"></div>
            </div>
            <div class="dfqs_box">
                <div id="kl_polar" style="width: 420px;height: 250px;">
                    <p><span class="tj_imgxl"></span></p>
                    <p class="ts_xxbox"><span class="ts_wztext">您的测试数据不足，无法生成雷达图！</span></p>
                </div>
            </div>
        </div>

        <div class="fr box_right">
            <div class="title02">用户全站排名信息  <div class="Helpexplain ico_Helpexplain hide"></div></div>
            <div class="dfqs_box">
                <div id="exercise_ranking_line" style="width: 420px;height: 250px;">
                    <p><span class="tj_imgxl"></span></p>
                    <p class="ts_xxbox"><span class="ts_wztext">您的测试数据不足，无法生成排名！</span></p>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>

<!--历次测试技能能力-->
    <div class="nlpg_box02">
        <div class="fl box_left">
            <div class="title02">技能雷达表  <div class="Helpexplain ico_Helpexplain"
                                             title="最近1-3次代表该技能下最后三次生成能力值的大小；
                                             能力值取值-3至3数值越大能力值越高，雷达图折线越接近外部"></div>
            </div>
            <div class="dfqs_box">
                <div id="skill_polar" style="width: 420px;height: 250px;">
                    <p><span class="tj_imgxl"></span></p>
                    <p class="ts_xxbox"><span class="ts_wztext">您的测试数据不足，无法生成雷达图！</span></p>
                </div>
            </div>
        </div>

        <div class="fr box_right">
            <div class="title02">能力雷达表  <div class="Helpexplain ico_Helpexplain"
                                             title="最近1-3次代表该能力下最后三次生成能力值的大小；
                                             能力值取值-3至3数值越大能力值越高，雷达图折线越接近外部"></div>
            </div>
            <div class="dfqs_box">
                <div id="capacity_polar" style="width: 420px;height: 250px;">
                    <p><span class="tj_imgxl"></span></p>
                    <p class="ts_xxbox"><span class="ts_wztext">您的测试数据不足，无法生成雷达图！</span></p>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
<!--知识点考点统计信息-->
<div class="title02">考点分析</div>
<div class="this_kdfx_box pgbd_zsd_box">
    <ul class="list_zsd" id="kl_info">
        <li class="title">
            <div class="zsd_bt fl">专项/考点</div>
            <div title="掌握的不错，还需继续努力！" class="start_box fc">能力评估</div>
            <div class="zqsl_bt fc">正确/总量</div>
            <div class="zql_bt fc">正确率</div>
            <div class="an_box fr">操作</div>
        </li>
        <li class="load">加载中...</li>
    </ul>
</div>
<div class="an03 pt20 mc"> <a href="<?php echo U('Default/index');?>">返回首页 继续测试</a></div>
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
<script type="text/html" id="klListTpl">
    {%each knowledge as i%}
        <li>
            <a class="ico_zd {%if i.sub==null%}ico_zd_03{%else%}ico_zd_01{%/if%} fl"></a>
            <div class="zsd_bt fl" style="cursor: pointer">{%i.klName%}</div>
            {%#star(i.rightAmount,i.rightWrongAmount)%}
            <div class="zqsl_bt fc">{%i.rightAmount%}/{%i.rightWrongAmount%}</div>
            <div class="zqsl_bt fc">{%i.rightRate%}%</div>
            <div class="an_box fr" klid="{%i.klID%}">
                <a class="an02" href="javascript:;">
                    <span class="an_left"></span>
                    <span class="an_cen">我要练习</span>
                    <span class="an_right"></span>
                </a>
            </div>
        </li>
        <ul style="display: none;">
            {%each i.sub as subList j%}
                <li class="lidj02">
                    <a class="ico_zd ico_zd_03 fl"></a>
                    <div class="zsd_bt fl" style="cursor: pointer">{%subList.klName%}</div>
                    {%#star(subList.rightAmount,subList.rightWrongAmount)%}
                    <div class="zqsl_bt fc">{%subList.rightAmount%}/{%subList.rightWrongAmount%}</div>
                    <div class="zqsl_bt fc">{%subList.rightRate%}%</div>
                    <div class="an_box fr" klid="{%subList.klID%}">
                        <a class="an02" href="javascript:;">
                            <span class="an_left"></span>
                            <span class="an_cen">我要练习</span>
                            <span class="an_right"></span>
                        </a>
                    </div>
                </li>
            {%/each%}
        </ul>
    {%/each%}
</script>
<script type="text/javascript">
$(document).ready(function () {
    var AatReport = {
        init:function(){
            this.getReportInfo();//获取页面所需内容
            this.klInfo();//知识点树状结构和雷达图标
            this.goTop();//返回TOP
            $('.ico_Helpexplain').tooltip();//tooltip
        },
        getReportInfo:function(){
            var self = this;
            $.post(U('PersonalReport/returnScoreInfo'),{times:Math.random()}, function (e) {
                if (e.status != 1) {
                    $('.zt_title div').html(e.data);
                } else {
                    //标题
                    $('.zt_title div').html('个人水平能力学情评估<span class="title_fb">依据最新考试大纲智能评估你的考试能力</span>');
                    //答题信息
                    $('.zttj_text').html('报告生成时间：'+ e.data.time);
                    //分数排名信息
                    //快速预测分
                    if(e.data.ExerciseScore){
                        $('.s_quick_score').html(e.data.ExerciseScore);
                        $('.quickScore').html('');
                    }else{
                        $('.s_quick_score').removeClass('ys_blue');
                        $('.s_quick_score').addClass('ys_grey')
                        $('.s_quick_score').next().html('');
                        $('.quickScore .ts_wztext').html('您还需要做1次测试才能生成快速预测分哦！')
                    }
                    //答题量
                    if(e.data.AllAmount){
                        $('.s_test_amount').html(e.data.AllAmount);
                        $('.testAmount').html('');
                    }else{
                        $('.s_test_amount').removeClass('ys_blue');
                        $('.s_test_amount').addClass('ys_grey')
                        $('.s_test_amount').next().html('');
                        $('.testAmount .ts_wztext').html('您还没有做题哦，快去练习吧！')
                    }
                    //精准预测分
                    if(e.data.ForecastScore){
                        $('.s_ok_score').html(e.data.ForecastScore);
                        $('.okScore .ts_wztext').html('满分'+e.data.totalScore+'分！');
                    }else{
                        $('.s_ok_score').removeClass('ys_blue tj_sz');
                        $('.s_ok_score').addClass('tj_imgxl')
                        $('.s_ok_score').html('');
                        $('.s_ok_score').next().html('');
                        $('.okScore .ts_wztext').html('您还需要做'+e.data.needTimes+'次测试才能生成精准预测分哦！')
                    }
                    if(e.data.ExerciseLine){
                        //排名图
                        self.exerciseRankingLine(e.data);
                        //快速预测分趋势图
                        self.exerciseScoreLine(e.data);
                    }
                    if(e.data.ForecastLine){
                        //精准预测分趋势图
                        self.forecastScoreLine(e.data);
                    }
                }
            });
        },
        //知识点能力树状结构和雷达图
        klInfo:function() {
            var self = this;
            $.post(U('PersonalReport/returnKlInfo'),{times:Math.random()},function (e) {
                if (e.status !== 1) {
                    var str='<div id="exercise_score_line" style="height: 120px;">'+
                            '<p><span class="tj_imgxl"></span></p>'+
                            '<p class="ts_xxbox" style="text-align:center;"><span class="ts_wztext ico_gth">你需要进行有效测试才能生考点分析</span></p>'+
                            '</div>';
                    $('#kl_info').html(str);
                } else {
                    template.helper('star', AatCommon.star);
                    var klListTpl = template('klListTpl', e.data);//模板
                    $('#kl_info .load').hide();
                    $('#kl_info').append(klListTpl);
                    //生成能力雷达
                    self.klPolar();
                    self.skillPolar();
                    self.capacityPolar();
                }
            });
            //展开事件绑定
            $('#kl_info').on('click','.zsd_bt,.ico_zd',function(){
                $(this).parent().find('.ico_zd_01').switchClass('ico_zd_01','ico_zd_02',1);
                $(this).parent().find('.ico_zd_02').switchClass('ico_zd_02','ico_zd_01',1);
                $(this).parent().next('ul').toggle('blind');
            });
            //点击开始做题事件绑定
            $('#kl_info').on('click','.an_box',function(){
                var klID = $(this).attr('klid');
                //隐藏出题按钮
                var button = $(this);
                button.hide().after('<div class="fr load" style="width:87px;position: relative;"><div style="position: absolute;left:15px;top: 5px;">请稍等...</div></div>');
                //显示进度条
                var bar  = $(this).parent().children('.load');
                bar.progressbar({
                    value: false
                });
                $.post(U('Default/ajaxGetTest'),{'id':2,'SubjectID': AatCommon.getSubjectID(),'KID':klID,times:Math.random()},function(e){
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
        //生成排名趋势图
        exerciseRankingLine:function(data){
            var yData = [];
            var xData = [];
            $.each(data.ExerciseLine, function (i, k) {
                xData.push(k.LoadTime);
                yData.push(k.ExerciseRanking);
            });
            var exerciseRankingLines= echarts.init(document.getElementById('exercise_ranking_line'));
            exerciseRankingLines.showLoading({
                text: '正在努力的读取数据中...'   //loading话术
            });
            var option = {
                toolbox: {
                    show: true,
                    feature: {
                    }
                },
                tooltip: {
                    trigger: 'axis'
                },
                xAxis: [
                    {
                        type: 'category',
                        boundaryGap: false,
                        data: xData.reverse()
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        axisLabel: {
                            formatter: '第{value}名'
                        },
                        splitArea: {show: true}
                    }
                ],
                series: [
                    {
                        name: '全站排名',
                        type: 'line',
                        data: yData.reverse()

                    }
                ]
            };
            exerciseRankingLines.setOption(option);
        },
        //生成快速预测分趋势图
        exerciseScoreLine:function(data){
            var yData = [];
            var xData = [];
            $.each(data.ExerciseLine, function (i, k) {
                xData.push(k.LoadTime);
                yData.push(k.ExerciseScore);
            });
            var exerciseScoreLines= echarts.init(document.getElementById('exercise_score_line'));
            exerciseScoreLines.showLoading({
                text: '正在努力的读取数据中...'   //loading话术
            });
            var option = {
                toolbox: {
                    show: true,
                    feature: {
                    }
                },
                tooltip: {
                    trigger: 'axis'
                },
                xAxis: [
                    {
                        type: 'category',
                        boundaryGap: false,
                        data: xData.reverse()
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        axisLabel: {
                            formatter: '{value} 分'
                        },
                        splitArea: {show: true}
                    }
                ],
                series: [
                    {
                        name: '快速预测分',
                        type: 'line',
                        data: yData.reverse()

                    }
                ]
            };
            exerciseScoreLines.setOption(option);
        },
        //生成精准预测分趋势图
        forecastScoreLine:function(data){
            var yData = [];
            var xData = [];
            $.each(data.ForecastLine, function (i, k) {
                xData.push(k.LoadTime);
                yData.push(k.ForecastScore);
            });
            var forecaseScoreLines= echarts.init(document.getElementById('forecast_score_line'));
            forecaseScoreLines.showLoading({
                text: '正在努力的读取数据中...'   //loading话术
            });
            var option = {
                toolbox: {
                    show: true,
                    feature: {
                    }
                },
                tooltip: {
                    trigger: 'axis'
                },
                xAxis: [
                    {
                        type: 'category',
                        boundaryGap: false,
                        data: xData.reverse()
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        axisLabel: {
                            formatter: '{value} 分'
                        },
                        splitArea: {show: true}
                    }
                ],
                series: [
                    {
                        name: '精准预测分',
                        type: 'line',
                        data: yData.reverse()

                    }
                ]
            };
            forecaseScoreLines.setOption(option);
        },
        //生成能力雷达
        klPolar:function() {
            $.post(U('PersonalReport/returnKlsInfo'),{times:Math.random()}, function (e) {
                if (e.status == 1) {
                    var klPolar= echarts.init(document.getElementById('kl_polar'));
                    klPolar.showLoading({
                        text: '正在努力的读取数据中...'   //loading话术
                    });
                    var option = {
                        tooltip : {
                            trigger: 'axis'
                        },
                        legend: {
                            x : 'center',
                            data:['最近第1次','最近第2次','最近第3次']
                        },
                        toolbox: {
                            show : true,
                            feature : {
                            }
                        },
                        calculable : true,
                        polar : [
                            {
                                indicator : e.data.indicator,
                                radius : 70
                            }
                        ],
                        series : [
                            {
                                name: '知识点掌握情况',
                                type: 'radar',
                                itemStyle: {
                                    normal: {
                                        areaStyle: {
                                            type: 'default'
                                        }
                                    }
                                },
                                data : e.data.series
                            }
                        ]
                    };
                    klPolar.setOption(option);
                }
            });
        },
        //生成能力雷达
        skillPolar:function() {
            $.post(U('PersonalReport/returnSkillInfo'),{times:Math.random()}, function (e) {
                if (e.status == 1) {
                    var klPolar= echarts.init(document.getElementById('skill_polar'));
                    klPolar.showLoading({
                        text: '正在努力的读取数据中...'   //loading话术
                    });
                    var option = {
                        tooltip : {
                            trigger: 'axis'
                        },
                        legend: {
                            x : 'center',
                            data:['最近第1次','最近第2次','最近第3次']
                        },
                        toolbox: {
                            show : true,
                            feature : {
                            }
                        },
                        calculable : true,
                        polar : [
                            {
                                indicator : e.data.indicator,
                                radius : 70
                            }
                        ],
                        series : [
                            {
                                name: '技能掌握情况',
                                type: 'radar',
                                itemStyle: {
                                    normal: {
                                        areaStyle: {
                                            type: 'default'
                                        }
                                    }
                                },
                                data : e.data.series
                            }
                        ]
                    };
                    klPolar.setOption(option);
                }
            });
        },
        //生成能力雷达
        capacityPolar:function() {
            $.post(U('PersonalReport/returnCapacityInfo'),{times:Math.random()}, function (e) {
                if (e.status == 1) {
                    var klPolar= echarts.init(document.getElementById('capacity_polar'));
                    klPolar.showLoading({
                        text: '正在努力的读取数据中...'   //loading话术
                    });
                    var option = {
                        tooltip : {
                            trigger: 'axis'
                        },
                        legend: {
                            x : 'center',
                            data:['最近第1次','最近第2次','最近第3次']
                        },
                        toolbox: {
                            show : true,
                            feature : {
                            }
                        },
                        calculable : true,
                        polar : [
                            {
                                indicator : e.data.indicator,
                                radius : 70
                            }
                        ],
                        series : [
                            {
                                name: '能力掌握情况',
                                type: 'radar',
                                itemStyle: {
                                    normal: {
                                        areaStyle: {
                                            type: 'default'
                                        }
                                    }
                                },
                                data : e.data.series
                            }
                        ]
                    };
                    klPolar.setOption(option);
                }
            });

        },
        //返回TOP
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
    AatReport.init();
});

</script>
<script src="/Public/plugin/echarts-plain.js" type="text/javascript"></script>
</body>
</html>