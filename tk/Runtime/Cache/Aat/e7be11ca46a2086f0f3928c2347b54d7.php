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

    <style type="text/css">
        #s_new ul span{margin: 0 2px;color: #00a0e9}
    </style>
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
                <div class="fl pt20 pl20">我的作业</div>
            </div>
            <!--Tab-->
            <div id="s_tab" class="tabnav01">
                <a class="this" data="s_new">最新动态</a>
                <a data="s_undo">未做作业</a>
                <a data="s_record">作业记录</a>
                <a data="s_class">我的班级</a>
            </div>
            <!--最新动态-->
            <div id="s_new" class="mysc_box" page="1" style="font-family: Arial,'宋体';">
                <div class="stlb01">
                    <ul class="list_stlb01">
                        <li class="loading"><div class="st_bt">加载中...</div></li>
                    </ul>
                    <div class="pagination" style="display: block;"></div>
                </div>
            </div>
            <!--未完成作业-->
            <div id="s_undo" class="mysc_box" style="display: none;">
                <div class="stlb01">
                    <ul class="list_stlb01">
                        <li class="loading"><div class="st_bt">加载中...</div></li>
                    </ul>
                    <div class="pagination" style="display: block;"></div>
                </div>
            </div>
            <!--作业记录-->
            <div id="s_record" class="mysc_box" style="display: none;">
                <div class="stlb01">
                    <ul class="list_stlb01">
                        <li class="loading"><div class="st_bt">加载中...</div></li>
                    </ul>
                    <div class="pagination" style="display: block;"></div>
                </div>
            </div>
            <!--我的班级-->
            <div id="s_class" class="mysc_box" style="display: none;color: #222222;font-family: Arial,'宋体';">
                <div class="s_class_list" style="border-bottom: 1px solid #D7D7D7;font-family:'微软雅黑';font-size:16px;line-height:54px;height: 54px;width: 100%;">
                    <div class="fl pl20 s_class_radio">
                        加载中...
                    </div>
                </div>
                <div>
                    <!--班级信息-->
                    <div class="fl s_info" style="padding-top: 20px;width: 588px;">
                        <div class="title02" style="margin: 0 10px 0 20px;">班级信息</div>
                        <div class="" style="padding: 5px 30px;">
                            <div style="line-height: 26px;">班级名称：<span class="s_class_name"></span></div>
                            <div style="line-height: 26px;">班级编号：<span class="s_class_no"></span></div>
                            <div style="line-height: 26px;">所属学校：<span class="s_school_name"></span></div>
                            <div style="line-height: 26px;">总人数：<span class="s_stu_num"></span>人</div>
                            <div style="line-height: 26px;">创建人：<span class="s_class_creater"></span></div>
                            <div style="line-height: 26px;">任课老师：<span class="s_class_tea_list">加载中...</span></div>
                        </div>
                    </div>
                    <!--班级设置-->
                    <div class="fr s_set" style="overflow: hidden;padding-top: 20px;width: 389px;">
                        <div class="title02" style="margin: 0 20px 0 10px;">班级设置</div>
                        <div class="" style="padding: 5px 30px;">
                            <div>
                                <input type="text" value=""  id="s_new_class_no" name="new_class_no" class="wbk01" placeholder="班级编号/手机号" style="height: 29px;margin-top: 6px;padding: 0 5px;width: 210px;margin-right: 10px;line-height:29px;">
                                <input type="button" style="background:url(/Public/default/image/an01_z.png);color:#fff;border:0px;background-color:#fff;padding:5px 15px;height: 30px;margin-top: 6px;cursor: pointer;" id="s_new_class" value="搜索班级">
                            </div>
                            <div id="new_class_error" style="line-height: 20px; color: darkred; display: none;"></div>
                            <div id="classInfo" style="display: none;">
                                <div class="closeList">X</div>
                                <ul id="classList">
                                </ul>
                            </div>
                            <div style="line-height: 30px;">班级编号获取：</div>
                            <div style="line-height: 30px;">
                                <span style="padding-right:17px; ">班级编号或手机号请向您的老师询问获取。</span>
                            </div>
                            <!--<div style="line-height: 30px;">您可能感兴趣的班级：</div>-->
                            <!--<div style="line-height: 30px;">-->
                                <!--<span style="padding-right:17px; "><a>高三语文提高班 编号：02142</a></span>-->
                                <!--<span style="padding-right:17px; "><a>英语阅读班 编号：02142</a></span>-->
                                <!--<span style="padding-right:17px; "><a>政治时事班 编号：02142</a></span>-->
                                <!--<span style="padding-right:17px; "><a>数学快速提高班 编号：02142</a></span>-->
                            <!--</div>-->
                        </div>

                    </div>
                    <div class="clear"></div>
                </div>
                <div>
                    <!--班级作业-->
                    <div class="fl s_work" style="padding-top: 20px;width: 588px;">
                        <div class="title02" style="margin: 0 10px 0 20px;">班级作业</div>
                        <div class="s_class_work" style="padding: 5px 30px;">
                            <ul class="list_stlb01">
                                加载中...
                            </ul>
                            <div style="display: block;" class="pagination"></div>
                        </div>
                    </div>
                    <!--班级成员-->
                    <div class="fr s_user" style="overflow: hidden;padding-top: 20px;width: 389px;">
                        <div class="title02" style="margin: 0 20px 0 10px;">作业排行</div>
                        <div style="padding: 15px 30px;">
                            <div class="s_class_stu_ranking_img">
                                <div style="width: 80px;float: left;margin: 2px 12px 0 12px;text-align: center;">本次最高分</div>
                                <div style="width: 80px;float: left;margin: 2px 12px 0 12px;text-align: center;">平均最高分</div>
                                <div style="width: 80px;float: left;margin: 2px 12px 0 12px;text-align: center;">进步最大</div>
                            </div>
                            <div style="clear: both;"></div>
                            <div class="s_class_stu_ranking_img">
                                <div style="height: 80px;width: 80px;float: left;margin: 6px 12px 0 12px;">
                                    <img id="rankHight" src="" alt="" width="80" height="80" />
                                </div>
                                <div style="height: 80px;width: 80px;float: left;margin: 6px 12px 0 12px;">
                                    <img id="rankAvg" src="" alt="" width="80" height="80" />
                                </div>
                                <div style="height: 80px;width: 80px;float: left;margin: 6px 12px 0 12px;">
                                    <img id="rankImp" src="" alt="" width="80" height="80" />
                                </div>
                            </div>
                            <div style="clear: both;"></div>
                            <div class="s_class_stu_ranking_list">
                                加载中...
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                        <div class="title02" style="margin: 10px 15px 0;">班级成员</div>
                        <div class="s_class_stu_list" style="padding: 15px 30px; line-height: 2em;">
                            加载中...
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
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
    <div id="dialog_down" title="下载作业" style="display: none;">
        <p>您的作业已经开始下载！</p>
        <p>如果没有弹出下载框，请手动<a href="" class="url" target="_blank"><span style="color: #00A0E9;font-weight: bold;">点击这里</span></a>下载！</p>
        <p>下载后可以在 作业记录 中重新下载</p>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    var AatMyHomework = {
        init:function(){
            this.tab();
            this.getNew();
            this.doHomeWork();
            this.newClass();
        },
        workDown:function(down){
            if(down == ''){
                //如果为空，下载链接错误，提示
                alert('下载链接错误，请联系老师从新生成作业文档！');
                return false;
            }
            //弹出对话框
            $('#dialog_down .url').attr('href',down);
            window.location.href=down;
            $("#dialog_down").dialog({
                buttons: {
                    "取消": function () {
                        $(this).dialog("close");
                    }
                }
            });
        },
        //切换Tab事件
        tab:function(){
            var self = this;
            $('#s_tab a').click(function(){
                $('#s_tab a').removeClass('this');
                $(this).addClass('this');
                $('#s_new,#s_undo,#s_record,#s_class,#s_teacher').hide();
                $('#'+$(this).attr('data')).show();
            });
            //未做作业
            $('#s_tab a[data=s_undo]').click(function(){
                self.getUndo();
            });
            //作业记录
            $('#s_tab a[data=s_record]').click(function(){
                self.getRecord();
            });
            //我的班级
            $('#s_tab a[data=s_class]').click(function(){
                self.getClass();
            });
        },
        //最新消息
        getNew:function() {
            var getNewAjax = function(p){
                $.ajax({
                    type:'POST',
                    url:U('MyHomework/getNew'),
                    data:{'p':p,times:Math.random()},
                    success:function(e){
                        if (e.status != 1) {
                            clearInterval(s);//循环停止
                            $('#s_new .loading div').html(e.data);
                            $('#s_new .loading').show();
                            return false;
                        } else {
                            var list = '';
                            $.each(e.data, function (i, k) {
                                list += '<li><div class="st_bt_box fl">'+
                                        '<p style="line-height: 17px;padding-top: 0;">'+ k.content+'</p>'+
                                        '</div><div class="fr" style="color: #888888;line-height: 17px;">'+ k.time+
                                        '</div></li>';
                            });
                            $('#s_new .loading').before(list);
                            if(e.data.length<20){//防止出现动态很少不够第一页的时候
                                clearInterval(s);//循环停止
                                $('#s_new .loading div').html('动态已经加载完毕！');
                            }
                            $('#s_new').attr('page',parseInt($('#s_new').attr('page'))+1);
                        }
                    }
                });
            };
            //默认载入第一页
            getNewAjax(1);
            var s = setInterval(function () {
                if ($('#s_tab a[data=s_new]').attr('class') == 'this') {
                    //滚动条到页面3/4 时加载数据
                    var p = $('#s_new').attr('page');
                    // 3 前两页的数据默认都显示
                    if ((($(window).scrollTop() + $(window).height() >= $(document).height() * 9 / 10) && $(window).scrollTop() > 0)) {
                        $('#s_new .loading div').html('正在加载....');
                        $('#s_new .loading').show();
                        getNewAjax(p);
                    }
                }
            }, 1500);//每隔1.5秒判断滚动条是否在3/4处,是的话加载数据
        },
        //未做作业
        getUndo:function() {
            var ajaxGet = function(p){
                $.ajax({
                    type:'POST',
                    url:U('MyHomework/getUndo'),
                    data:{'p':p,times:Math.random()},
                    success:function(e){
                        if (e.status != 1) {
                            $('#s_undo .list_stlb01 .st_bt').html(e.data);
                            $('#s_undo .pagination').hide();
                        } else {
                            var list = '';
                            $.each(e.data.list, function (i, k) {
                                var a='',b='',id=k.WorkID+'-'+k.ClassID,showTime='';
                                if(k.Flag == 'out_date' || k.Flag == 'normal'){
                                    //已经开始和过期
                                    if(k.IDType == 'send_id'){
                                        id= k.SendID;
                                    }
                                    if(k.WorkStyle==1){
                                        a = '<a class="an01 fr mr5" data="'+ id+'" down="'+ k.DownUrl+'" href="javascript:;"><span class="an_left"></span><span class="an_cen">下载作业</span><span class="an_right"></span></a>';
                                    }else{
                                        a = '<a class="an01 fr mr5" data="'+ id+'" data-workType="'+ k.WorkType+'" down="-1" href="javascript:;"><span class="an_left"></span><span class="an_cen">做作业</span><span class="an_right"></span></a>';
                                    }
                                    if(k.Flag == 'out_date'){
                                        b = ' <span style="color: darkred;">已过期</span>';
                                    }
                                }else{
                                    //暂未开始
                                    a = '<a class="an02 fr mr5" data="0" href="javascript:;"><span class="an_left"></span><span class="an_cen">暂未开始</span><span class="an_right"></span></a>';
                                }
                                if(k.Flag == 'noraml'){
                                    showTime = ' 剩余答题时间：'+ k.LeftTime;
                                }else{
                                    showTime = ' 答题时间：'+ k.StartTime+' 至 '+ k.EndTime;
                                }
                                list += '<li><div class="st_bt_box fl">'+
                                        '<div class="st_bt">'+ k.WorkName+'（'+ k.LoadDate+ k.SubjectName+'）'+b+'</div>'+
                                        '<p><b>'+ k.UserName+'</b> 于'+ k.LoadTime+'发布 题量：'+ k.TestNum+showTime+'</p>'+
                                        '</div><div class="an_box fr">'+a+
                                        '</div></li>';
                            });
                            $('#s_undo .list_stlb01').html(list);
                            $('#s_undo .pagination').html(e.data.show);
                        }
                    }
                });
            };
            ajaxGet(1);
            //Ajax 分页
            $('#s_undo').on('click','.ajax_page_class', function(){
                ajaxGet($(this).attr('data'));
            });
        },
        //点击做作业
        doHomeWork:function(){
            var self = this;
            //做作业按钮事件
            $('#s_undo').on('click', '.an_box', function () {
                var id = $(this).find('a').attr('data');
                var down = $(this).find('a').attr('down');
                var workType = $(this).find('a').attr('data-workType');
                if(id == 0){
                    return false;
                }
                if (id.indexOf('-') == -1) {
                    //sendID
                    if(down != -1){
                        self.workDown(down);
                    }else{
                        if(workType==1){
                            window.location.href = U('MyHomeworkExercise/index?id=' + id);
                        }else if(workType == 2){
                            window.location.href = U('MyHomeworkExercise/caseIndex?id=' + id);
                        }
                    }
                } else {
                    //work_id
                    //隐藏出题按钮
                    var button = $(this);
                    button.hide();
                    button.after('<div class="fr load" style="width:73px;position: relative;"><div style="position: absolute;left:12px;top: 5px;">请稍等...</div></div>');
                    //显示进度条
                    var bar = button.parent().children('.load');
                    bar.progressbar({
                        value: false
                    });
                    $.post(U('MyHomeworkExercise/indexCreate'), {'id': id,times:Math.random()}, function (e) {
                        if (e.status == 1) {
                            if (down != -1) {
                                //删除进度条
                                bar.remove();
                                //下载做题
                                //弹出对话框
                                self.workDown(down);
                                button.parent().fadeOut();
                            } else {
                                //在线做题
                                if(workType==1){
                                    window.location.href = U('MyHomeworkExercise/index?id=' + e.data);
                                }else if(workType == 2){
                                    window.location.href = U('MyHomeworkExercise/caseIndex?id=' + e.data);
                                }
                            }
                        } else {
                            //删除进度条
                            bar.remove();
                            //显示出题按钮
                            button.show();
                            //显示错误信息
                            alert(e.data);
                        }
                    });
                }
            });
        },
        //作业记录
        getRecord:function() {
            var self = this;
            var ajaxGet = function(p){
                $.ajax({
                    type:'POST',
                    url:U('MyHomework/getRecord'),
                    data:{'p':p,times:Math.random()},
                    success:function(e){
                        if (e.status != 1) {
                            $('#s_record .list_stlb01 .st_bt').html(e.data);
                            $('#s_record .pagination').hide();
                        } else {
                            var list = '';
                            $.each(e.data.list, function (i, k) {
                                var b='',time='';
                                var a = '';
                                if(k.WorkStyle==1){
                                    a = '<a class="an01 fr mr5" data="'+ k.SendID+'" down="'+ k.DownUrl+'" href="javascript:;"><span class="an_left"></span><span class="an_cen">下载作业</span><span class="an_right"></span></a>';
                                }else{
                                    a = '<a class="an01 fr mr5" data="'+ k.SendID+'" data-workType="'+ k.WorkType+'" down="-1" href="javascript:;"><span class="an_left"></span><span class="an_cen">答题情况</span><span class="an_right"></span></a>';
                                }
                                if(k.Status==1){
                                    //已提交
                                    b=' <span style="color: #00A0E9;">已提交</span>';
                                    time=' 提交时间：'+ k.SendTime;
                                }else if(k.Status==2){
                                    //已批改
                                    var comment = '';
                                    if(k.Comment){
                                        $('#footer').after('<div id="dialog_comment_'+ k.SendID+'" style="display:none;overflow-y:auto;" title="'+ k.UserName+' 评语">'+ k.Comment+'</div>')
                                        comment = '<a class="comment" href="javascript:;" data="'+ k.SendID+'">查看评语</a>';
                                    }
                                    b=' <span style="color: darkgreen;">已批改</span> '+comment;
                                    time=' 审批时间：'+ k.CheckTime+' 分数：<b>'+ k.CorrectRate+'</b>分';
                                }
                                list += '<li><div class="st_bt_box fl">'+
                                        '<div class="st_bt" style="color: #888888;">'+ k.WorkName+'（'+ k.LoadDate+ k.SubjectName+'）'+b+'</div>'+
                                        '<p>'+ k.UserName+' 于'+ k.LoadTime+'发布 题量：'+ k.TestNum+time+' 班级：'+k.ClassName+'</p>'+
                                        '</div><div class="an_box fr">'+a+
                                        '</div></li>';
                            });
                            $('#s_record .list_stlb01').html(list);
                            $('#s_record .pagination').html(e.data.show);
                        }
                    }
                });
            }
            ajaxGet(1);
            //Ajax 分页
            $('#s_record').on('click','.ajax_page_class', function(){
                ajaxGet($(this).attr('data'));
            });
            //查看老师评语
            $('#s_record').on('click','.comment',function(){
                $('#dialog_comment_'+$(this).attr('data')).dialog();
            });
            //做作业按钮事件
            $('#s_record').on('click', '.an_box', function () {
                var id = $(this).find('a').attr('data');
                var down = $(this).find('a').attr('down');
                var workType = $(this).find('a').attr('data-workType');
                if (down == -1) {
                    if(workType== 1){
                        window.location.href = U('MyHomeworkAnswer/index?id=' + id);
                    }else if(workType == 2){
                        window.location.href = U('MyHomeworkAnswer/caseIndex?id=' + id);
                    }
                }else{
                    self.workDown(down);
                }
            });
        },
        //我的班级
        getClass:function() {
            var classID,self = this;
            //获取某班级信息
            var getClassInfo = function(classID){
                $.ajax({
                    type:'POST',
                    url:U('MyHomework/getClassInfo'),
                    data:{'class_id':classID,times:Math.random()},
                    success:function(e){
                        if(e.status != 1){
                            $('#s_class .s_info,.s_set,.s_work,.s_user').hide();
                            alert(e.data);
                        }else{
                            $('.s_class_name').html(e.data.class_info.ClassName);
                            $('.s_class_no').html(e.data.class_info.OrderNum);
                            $('.s_school_name').html(e.data.class_info.SchoolName);
                            $('.s_stu_num').html(e.data.class_info.StudentAll);
                            $('.s_class_creater').html(e.data.class_info.Creator);
                            var classTeaList = '';
                            $.each(e.data.teacher,function(i,k){
                                classTeaList += '<span style="padding-right:17px; "><a>#'+ k.SubjectName+'</a> <a>@'+ (k.RealName? k.RealName: k.UserName)+'</a></span>';
                            });
                            $('.s_class_tea_list').html(classTeaList);
                            var classStuList = '';
                            $.each(e.data.student,function(i,k){
                                classStuList += '<span class="stuName"><a>@'+ (k.RealName? k.RealName: k.UserName)+'</a></span>';
                            });
                            $('.s_class_stu_list').html(classStuList);
                            var classStuRankingList =  '<div style="height: 30px;line-height:30px;width: 80px;float: left;margin: 0 12px 0 12px;text-align: center;">'+
                                    (e.data.ranking.last.RealName?e.data.ranking.last.RealName: e.data.ranking.last.UserName)+'</div>'+
                                    '<div style="height: 30px;line-height:30px;width: 80px;float: left;margin: 0 12px 0 12px;text-align: center;">'+
                                    (e.data.ranking.avg.RealName?e.data.ranking.avg.RealName: e.data.ranking.avg.UserName)+'</div>'+
                                    '<div style="height: 30px;line-height:30px;width: 80px;float: left;margin: 0 12px 0 12px;text-align: center;">'+
                                    (e.data.ranking.improve.RealName?e.data.ranking.improve.RealName: e.data.ranking.improve.UserName)+'</div>';
                            $('.s_class_stu_ranking_list').html(classStuRankingList);
                            $('#rankHight').attr('src',e.data.ranking.last.UserPic);
                            $('#rankAvg').attr('src',e.data.ranking.avg.UserPic);
                            $('#rankImp').attr('src',e.data.ranking.improve.UserPic);
                        }
                    }
                });
            };
            //获取某班级作业列表
            var getClassWork = function(classID,p){
                $.ajax({
                    type:'POST',
                    url:U('MyHomework/getClassWork'),
                    data:{'class_id':classID,'p':p,times:Math.random()},
                    success:function(e){
                        if(e.status != 1){
                            $('#s_class .s_class_work ul').html(e.data);
                            $('#s_class .s_class_work .pagination').html('');
                        }else{
                            var classWorkList = '';
                            $.each(e.data.list,function(i,k){
                                var a='',b='';
                                var down = -1,downLable = false;//down默认必须-1
                                if(k.WorkStyle == 1){
                                    down = k.DownUrl;
                                    downLable = '下载作业';
                                }
                                if(k.Status==1){
                                    //已提交
                                    a = '<a class="an02 fr mr5 s_exercise_answer" data="'+ k.SendID+'" data-workType="'+ k.WorkType+'" down="'+down+'" href="javascript:;"><span class="an_left"></span><span class="an_cen">'+(downLable?downLable:'答题情况')+'</span><span class="an_right"></span></a>';
                                    b=' <span style="color: #00A0E9;">已提交</span>';
                                }else if(k.Status==2){
                                    //已批改
                                    a = '<a class="an02 fr mr5 s_exercise_answer" data="'+ k.SendID+'" data-workType="'+ k.WorkType+'"  down="'+down+'" href="javascript:;"><span class="an_left"></span><span class="an_cen">'+(downLable?downLable:'答题情况')+'</span><span class="an_right"></span></a>';
                                    var comment = '';
                                    if(k.Comment){
                                        $('#footer').after('<div id="dialog_comment_'+ k.SendID+'" style="display:none;overflow-y:auto;" title="'+ k.UserName+' 评语">'+ k.Comment+'</div>')
                                        comment = '<a class="comment" href="javascript:;" data="'+ k.SendID+'">查看评语</a>';
                                    }
                                    b=' <span style="color: darkgreen;">已批改</span> '+comment;
                                }else{
                                    //未提交
                                    var id = k.IDType=='work_id'?(k.WorkID+'-'+ k.ClassID): k.SendID;
                                    if(k.Flag=='no_start'){
                                        //未开始
                                        a = '<a class="an02 fr mr5" href="javascript:;"><span class="an_left"></span><span class="an_cen">暂未开始</span><span class="an_right"></span></a>';
                                    }else if(k.Flag=='out_date'){
                                        //已过期
                                        b = ' <span style="color: darkred;">已过期</span>';
                                        a = '<a class="an01 fr mr5 s_do_exercise" data="'+id+'" data-workType="'+ k.WorkType+'"  down="'+down+'" href="javascript:;"><span class="an_left"></span><span class="an_cen">'+(downLable?downLable:'做作业')+'</span><span class="an_right"></span></a>';
                                    }else{
                                        //正常
                                        a = '<a class="an01 fr mr5 s_do_exercise" data="'+id+'" data-workType="'+ k.WorkType+'"  down="'+down+'" href="javascript:;"><span class="an_left"></span><span class="an_cen">'+(downLable?downLable:'做作业')+'</span><span class="an_right"></span></a>';
                                    }
                                }
                                classWorkList += '<li><div class="st_bt_box fl">'+
                                        '<div class="st_bt">'+k.WorkName+'（'+ k.LoadDate+ k.SubjectName+'）'+b+'</div>'+
                                        '<p>'+ k.UserName+'于'+ k.LoadTime+'发布 数量：'+ k.TestNum+'</p>'+
                                        '</div><div class="an_box fr">'+a+
                                        '</div></li>';
                                $('#s_class .s_class_work ul').html(classWorkList);
                                $('#s_class .s_class_work .pagination').html(e.data.show);
                            });
                        }
                    }
                });
            };
            //获取学生班级
            var getClasses = function(){
                $.ajax({
                    type:'POST',
                    url:U('MyHomework/getClasses'),
                    data:{times:Math.random()},
                    success:function(e){
                        if (e.status != 1) {
                            $('#s_class .s_class_list,.s_info,.s_work,.s_user').hide();
                            $('#s_class .s_set').css({'width':'100%','paddingBottom':'20px'});
                        } else {
                            var classList = '';
                            $.each(e.data,function(i,k){
                                classList += '<a href="javascript:;" data="'+ k.ClassID+'" style="background: #888888; color: #FFFFFF; padding: 8px;">'+ k.ClassName+'</a>';
                            });
                            $('#s_class .s_class_list div').html(classList);
                            //初始化第一个班级为默认选中状态 classID
                            var firstClass = $('.s_class_list a:first');
                            classID = firstClass.attr('data');
                            firstClass.css('background','#00A0E9');
                            getClassInfo(classID);
                            getClassWork(classID,1);

                        }
                    }
                });
            };
            //班级点击Tab事件
            var classListTab = function(){
                $('.s_class_list').on('click','a',function(){
                    $('.s_class_list a').css('background','#888888');
                    $(this).css('background','#00A0E9');
                    classID = $(this).attr('data');
                    getClassInfo(classID);
                    getClassWork(classID,1);
                });
            };
            getClasses();
            classListTab();
            //Ajax 分页
            $('#s_class .s_class_work').on('click','.ajax_page_class', function(){
                getClassWork(classID,$(this).attr('data'));
            });
            //查看老师评语
            $('#s_class .s_class_work').on('click','.comment',function(){
                $('#dialog_comment_'+$(this).attr('data')).dialog();
            });
            //做作业按钮事件
            $('.s_class_work').on('click', '.an_box', function () {
                var sClass = $(this).find('a').attr('class');
                var id = $(this).find('a').attr('data');
                var down = $(this).find('a').attr('down');
                var workType = $(this).find('a').attr('data-workType');
                if(down != -1){
                    //下载作业
                    self.workDown(down);
                }else{
                    if(sClass.indexOf('s_do_exercise') == -1){
                        //跳转看答案界面
                        if(workType==1){
                            window.location.href=U('MyHomeworkAnswer/index?id='+id);
                        }else if(workType==2){
                            window.location.href=U('MyHomeworkAnswer/caseIndex?id='+id);
                        }
                    }else{
                        //跳转答题界面
                        if(id.indexOf('-') == -1){
                            //send_id
                            if(workType==1){
                                window.location.href=U('MyHomeworkExercise/index?id='+id);
                            }else if(workType==2){
                                window.location.href=U('MyHomeworkExercise/caseIndex?id='+id);
                            }
                        }else{
                            //work_id
                            //隐藏出题按钮
                            var button = $(this).parent();
                            button.hide();
                            button.after('<div class="fr load" style="width:73px;position: relative;"><div style="position: absolute;left:12px;top: 5px;">请稍等...</div></div>');
                            //显示进度条
                            var bar = button.parent().children('.load');
                            bar.progressbar({
                                value: false
                            });
                            $.post(U('MyHomeworkExercise/indexCreate'), {'id': id,times:Math.random()}, function (e) {
                                if (e.status == 1) {
                                    if(workType==1){
                                        window.location.href = U('MyHomeworkExercise/index?id='+ e.data);
                                    }else if(workType==2){
                                        window.location.href = U('MyHomeworkExercise/caseIndex?id='+ e.data);
                                    }
                                } else {
                                    //删除进度条
                                    bar.remove();
                                    //显示出题按钮
                                    button.show();
                                    //显示错误信息
                                    alert(e.data);
                                }
                            });
                        }
                    }
                }
            });
        },
        //加入新班级
        newClass:function() {
            var lock='';
            $('.s_set').on('click','#s_new_class',function(){
                var class_no = $('#s_new_class_no').val();
                var reg = /^\d+$/;
                if(!class_no){
                    $('#new_class_error').text('请输入班级编号/教师手机号！').show();
                    return false;
                }
                if(!reg.test(class_no)){
                    $('#new_class_error').text('请输入正确的班级编号/教师手机号！').show();
                    return false;
                }
                $.post(U('MyHomework/newClass'),{'class_num':class_no,times:Math.random()},function(e){
                    $('#new_class_error').hide();
                    if(e.status==1){
                        var classListHTML='';
                        var status='';
                        var joinClass='';
                        $.each(e.data,function(i,k){
                            if(k.Status==-1){
                                joinClass='  joinClass';
                                status='<span class="addFont">+申请加入</span>';
                            }else if(k.Status==0){
                                status='<span class="addYes">已加入</span>';
                            }else{
                                status='<span class="addYes">待审核</span>';
                            }
                            classListHTML+='<li class="cList"><span class="cName">'+ k.ClassName+'('+ k.OrderNum+')</span><a class="addButton'+joinClass+'" href="javaScript:void(0);" cid="'+ k.ClassID+'">'+status+'</a></li>';
                        });
                        $('#classInfo').show();
                        $('#classList').html(classListHTML);
                    }else{
                        $('#new_class_error').text(e.data).show();
                        $('.closeList').click();
                    }
                });
            });

            //申请加入
            $('.s_set').on('click','.joinClass',function(){
                if(lock!=''){
                    return false;
                }
                var classID=$(this).attr('cid');
                var obj=$(this);
                obj.removeClass('joinClass');
                $.post(U('MyHomework/introClass'),{'cid':classID,times:Math.random()},function(e){
                    if(e.status==1){
                        $('#new_class_error').hide();
                        obj.find('span').html('等待审核');
                        obj.find('span').removeClass('addFont');
                        obj.find('span').addClass('addYes');
                    }else{
                        $('#new_class_error').text(e.data).show();
                    }
                })
            });
            //关闭列表
            $('.s_set').on('click','.closeList',function(){
                $('#classInfo').hide();
                $('#classList').html('');
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
    AatMyHomework.init();
});

</script>
</body>
</html>