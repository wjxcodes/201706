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
        <div class="box02 mb20 mylx_box">
            <!--题目-->
            <div class="zt_title">
                <div class="fl pt20 pl20">我的练习</div>
            </div>
            <!--测试记录-错题-收藏Tab-->
            <div id="s_tab" class="tabnav01">
                <a class="this" data="s_record">练习历史</a>
                <a data="s_wrong">错题记录</a>
                <a data="s_collect">我的收藏</a>
            </div>
            <!--测试记录-->
            <div id="s_record" class="mysc_box">
                <!--试卷收藏-->
                <div class="stlb01">
                    <ul class="list_stlb01">
                        加载中...
                    </ul>
                    <div class="pagination" style="display: block;"></div>
                </div>
                <!--试卷收藏 END-->
            </div>
            <!--错题记录-->
            <div id="s_wrong" class="mysc_box" style="display: none;">
                <!--知识点-->
                <div style="" class="list_zsd_box">
                    <ul class="list_zsd">
                        加载中...
                    </ul>
                </div>
                <!--知识点 END-->
            </div>
            <!--我的收藏-->
            <div id="s_collect" class="mysc_box" style="display: none;">
                <!--知识点-->
                <div style="" class="list_zsd_box">
                    <ul class="list_zsd">
                       加载中...
                    </ul>
                </div>
                <!--知识点 END-->
            </div>
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
    var AatMyExercise = {
        init:function(){
            //测试记录
            this.exerciseRecord();
            //切换Tab事件
            this.tab();
            //返回TOP
            this.goTop();
        },
        tab:function() {
            var self = this;
            $('#s_tab a').on('click',function(){
                $('#s_tab a').removeClass('this');
                $(this).addClass('this');
                $('#s_record,#s_collect,#s_wrong').hide();
                if($(this).attr('data')=='s_wrong'){
                    self.testWrong();//错题记录
                }else if($(this).attr('data')=='s_collect'){
                    self.testCollect();//收藏记录
                }
                $('#'+$(this).attr('data')).show();
            });
        },

        exerciseRecord:function() {
            var self = this;
            self.recordAjax(1);
            //Ajax 分页
            $('#s_record').on('click','.ajax_page_class', function(){
                self.recordAjax($(this).attr('data'));
            });
        },

        testWrong:function() {
            $.get(U('TestWrong/returnKlInfo'),{times:Math.random()}, function (e) {
                if (e.status !== 1) {
                    $('#s_wrong .list_zsd').html(e.data);
                } else {
                    var str = '';
                    $.each(e.data,function(i,k){
                        //判断是否有下级知识点
                        var ico = k.sub?'ico_zd_01':'ico_zd_03';
                        var an = '',url = 'javascript:;';
                        if(k.amount == 0){
                            an = 'an02hs';
                        }else{
                            url = U('TestWrong/testList?id='+ k.klID);
                        }
                        str += '<li><a class="ico_zd '+ico+' fl"></a><div klid="'+ k.klID+'" class="zsd_bt fl" style="cursor: pointer">'+ k.klName+
                                '<span class="zsd_tj">(共有 '+ k.amount+' 道错题)</span></div>'+
                                '<div class="an_box fr" klid="'+ k.klID+'"><a class="an02 '+an+'" href="'+ url +'"><span class="an_left"></span>'+
                                '<span class="an_cen">查看题目</span><span class="an_right"></span></a></div></li>';
                        if(k.sub){
                            $.each(k.sub,function(j,m){
                                var an = '',url = 'javascript:;';
                                if(m.amount == 0){
                                    an = 'an02hs';
                                }else{
                                    url = U('TestWrong/testList?id='+ m.klID);
                                }
                                str += '<li class="lidj02 sub_kl_id_'+ k.klID+'" style="display: none;"><a class="ico_zd ico_zd_03 fl"></a><div class="zsd_bt fl">'+ m.klName+
                                        '<span class="zsd_tj">(共有 '+ m.amount+' 道错题)</span></div>'+
                                        '<div class="an_box fr" klid="'+ m.klID+'"><a class="an02 '+an+'" href="'+ url +'"><span class="an_left"></span>'+
                                        '<span class="an_cen">查看题目</span><span class="an_right"></span></a></div></li>';
                            });
                        }
                    });
                    $('#s_wrong .list_zsd').html(str);
                }
            });
            $('#s_wrong').off('click').on('click','.zsd_bt,.ico_zd',function(){
                $(this).parent().find('.ico_zd_01').switchClass('ico_zd_01','ico_zd_02',1);
                $(this).parent().find('.ico_zd_02').switchClass('ico_zd_02','ico_zd_01',1);
                var id = $(this).parent().find('.zsd_bt').attr('klid');
                $('.sub_kl_id_'+id).toggle('blind');
            });

        },

        testCollect:function(){
            $.get(U('TestCollect/returnKlInfo'),{times:Math.random()}, function (e) {
                if (e.status !== 1) {
                    $('#s_collect .list_zsd').html(e.data);
                } else {
                    var str = '';
                    $.each(e.data,function(i,k){
                        //判断是否有下级知识点
                        var ico = k.sub?'ico_zd_01':'ico_zd_03';
                        var an = '',url = 'javascript:;';
                        if(k.amount == 0){
                            an = 'an02hs';
                        }else{
                            url = U('TestCollect/testList?id='+ k.klID);
                        }
                        str += '<li><a class="ico_zd '+ico+' fl"></a><div klid="'+ k.klID+'" class="zsd_bt fl" style="cursor: pointer">'+ k.klName+
                                '<span class="zsd_tj">(共收藏 '+ k.amount+' 道题)</span></div>'+
                                '<div class="an_box fr" klid="'+ k.klID+'"><a class="an02 '+an+'" href="'+ url +'"><span class="an_left"></span>'+
                                '<span class="an_cen">查看题目</span><span class="an_right"></span></a></div></li>';
                        if(k.sub){
                            $.each(k.sub,function(j,m){
                                var an = '',url = 'javascript:;';
                                if(m.amount == 0){
                                    an = 'an02hs';
                                }else{
                                    url = U('TestCollect/testList?id='+ m.klID);
                                }
                                str += '<li class="lidj02 sub_kl_id_'+ k.klID+'" style="display: none;"><a class="ico_zd ico_zd_03 fl"></a><div class="zsd_bt fl">'+ m.klName+
                                        '<span class="zsd_tj">(共收藏 '+ m.amount+' 道题)</span></div>'+
                                        '<div class="an_box fr" klid="'+ m.klID+'"><a class="an02 '+an+'" href="'+ url +'"><span class="an_left"></span>'+
                                        '<span class="an_cen">查看题目</span><span class="an_right"></span></a></div></li>';
                            });
                        }
                    });
                    $('#s_collect .list_zsd').html(str);
                }
            });
            $('#s_collect').off('click').on('click','.zsd_bt,.ico_zd',function(){
                $(this).parent().find('.ico_zd_01').switchClass('ico_zd_01','ico_zd_02',1);
                $(this).parent().find('.ico_zd_02').switchClass('ico_zd_02','ico_zd_01',1);
                var id = $(this).parent().find('.zsd_bt').attr('klid');
                $('.sub_kl_id_'+id).toggle('blind');
            });

        },
        recordAjax:function(p) {
            $.ajax({
                type:'POST',
                url:U('ExerciseRecord/returnRecordInfo'),
                data:{'p':p,times:Math.random()},
                success:function(e){
                    if (e.status != 1) {
                        $('#s_record .list_stlb01').html(e.data);
                        $('#s_record .pagination').hide();
                    } else {
                        var list = '';
                        $.each(e.data.list, function (i, k) {
                            var a;
                            if (k.Score == -1) {
                                a = '<a class="an01 fr mr5" href="'+U("Exercise/index","id=" + k.TestID )+ '"><span class="an_left"></span><span class="an_cen">继续测试</span><span class="an_right"></span></a>';
                            } else {
                                a = '<a class="an02 fr mr5" href="'+U("ExerciseReport/index?id=" + k.TestID) + '"><span class="an_left"></span><span class="an_cen">查看报告</span><span class="an_right"></span></a>';
                            }
                            var testName = '';
                            if(k.DocName){
                                testName = '[ '+ k.DocName+' ]';
                            }
                            if(k.TestRecordName){
                                testName = '[ '+ k.TestRecordName+' ]';
                            }
                            list += '<li><div class="st_bt_box fl">'+
                                    '<div class="st_bt">'+ k.Style+testName+'</div>'+
                                    '<p>测试时间：' + k.LoadTime + '&nbsp;&nbsp;答题情况：[正确' + k.RightAmount +'/总数' + k.AllAmount + ']&nbsp;&nbsp;'+
                                    '测试分数：' + (k.Score == -1 ? '暂无' : k.Score) + '&nbsp;&nbsp;耗时：' + k.RealTime + '分钟</p>'+
                                    '</div><div class="an_box fr">'+a+
                                    '</div></li>';
                        });
                        $('#s_record .list_stlb01').html(list);
                        $('#s_record .pagination').html(e.data.show);
                    }
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
    AatMyExercise.init();

});

</script>
</body>
</html>