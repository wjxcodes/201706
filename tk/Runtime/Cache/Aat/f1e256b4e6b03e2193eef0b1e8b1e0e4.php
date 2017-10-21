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

    <link href="/Public/newAat/css/index.css<?php echo C(WLN_UPDATE_FILE_DATE);?>" rel="stylesheet" type="text/css"/>
</head>
<body style="min-height:100%;height:100%;">
<div id="wrapper">
<!--首页左侧选择学科-->
<div id="leftsub">
    <div class="leftsub_box">
        <div id="xk_box">
            <ul class="xk_list" style="">

            </ul>
        </div>
        <div id="cd_box">
            <div class="groove-hor"></div>
            <ul class="cd_list" id="s_pg_cs">
                <li><a style="cursor: pointer;" data="<?php echo U('PersonalReport/index');?>"><span class="cd_ico01 fl"></span><span class="fl cd_text">学情评估</span></a></li>
                <li><a style="cursor: pointer;" data="<?php echo U('MyExercise/index');?>"><span class="cd_ico02 fl"></span><span class="fl cd_text">我的练习</span></a></li>
                <li><a style="cursor: pointer;" data="<?php echo U('MyHomework/index');?>"><span class="cd_ico03 fl"></span><span class="fl cd_text">我的作业</span></a></li>
            </ul>
        </div>
    </div>
</div>
<!--首页选择学科开关-->
<div class="leftsub_an">
    <a class="leftsub_an_off" href="javascript:;" ></a>
    <a class="leftsub_an_no" style="display:none" href="javascript:;"></a>
</div>
<!--首页头部-->
<div class="head_01">
    <div class="phoneapp fr"><a href="#" title="请在手机端浏览">手机APP</a></div>
    <!--登录后显示首页用户登录信息-->
    <div class="dlxx01 dlxx fr pr15" style="display: none;width: 160px;">
        <div class="user_dis" style="width: 100%;height: 100%;">
            <span class="userid fl pr5" style="position: relative;font-size: 13px;"></span>
            <span class="ico_jt01 fl" style="position: relative;"></span>
        </div>
        <div id="dlxx_box" style="display: none;">
            <ul>
                <li class="li01"><a href="<?php echo U('User/Aat/index');?>"><span class="ico_01 dlxx_ico"></span><span class="text">个人中心</span></a>
                </li>
               
                <li class="li04"><a href="javascript:;" class="user_logout"><span class="ico_04 dlxx_ico"></span><span class="text">退出登录</span></a>
                </li>
            </ul>

        </div>
    </div>
    <!--登录后显示首页用户登录信息 end-->
</div>
<!--首页logo-->
<div id="head" class="w980 mc pt20">
    <div class="head_02 mc"><a href="/Aat" class="logo_img"></a><img src="/Public/newAat/images/logowz.png"/><div class="bb_box"></div></div>
</div>
<!--登录弹出框-->
<div class="box01 login_box" style="display:none;margin-top:50px;">
    <div class="top"></div>
    <div class="zj">
        <div class="bd">
            <form id="login">
                <fieldset class="inputs">
                    <input type="hidden" name='jumpUrl' value='<?php echo ($jumpUrl); ?>'/>
                    <input style="padding:10px 20px;height:20px;width:220px;line-height:20px;" id="login_username" type="text" placeholder="用户名/学号"
                           data-rule="require" data-display="用户名/学号" class="wbk01 login_username" value="" />
                    <input style="padding:10px 20px;height:20px;width:220px;line-height:20px;" id="login_password" type="password" placeholder="登录密码"
                           data-rule="require" data-display="密码" class="wbk01 login_password" value="" />
                    <div class="pb5"><label for="checkbox_remember" class="login_checkbox">
                        <input type="checkbox" value="1" id="checkbox_remember"/>&nbsp;&nbsp;&nbsp;两周内自动登录
                    </label>
                        <div class="wjmm fr"><a href="<?php echo U('User/Index/getPassword');?>">忘记密码？</a></div>
                        <div class="clear"></div>
                    </div>
                    <div class="error_msg" style="color: #b40504;margin-top: 5px;display: none;"></div>
                    <div class="submit_msg" style="color: #FFFFFF;margin-top: 5px;display: none;">正在登录系统请稍候...</div>
                    <div id="loginSubmit" class="an03 pt20"><a href="javascript:;">登录</a></div>
                </fieldset>
            </form>
        </div>
    </div>
    <div class="bot"><img src="/Public/newAat/images/box01_bot_01.png"/></div>
</div>
<!--注册弹出框-->
<div class="box01 zhuce_box" style="display: none;margin-top:20px;">
    <div class="top"></div>
    <div class="zj">
        <div class="bd">
            <form id="zhuce">
                <fieldset class="inputs">
                    <input id="reg_username" type="text" placeholder="请输入您的邮箱/手机号"
                           data-rule="require|userName" data-display="用户名" class="wbk01" />
                    <input id="reg_password" type="password" placeholder="请设置6位数以上的密码"
                           data-rule="require|password" data-display="密码" class="wbk01" />
                    <input id="reg_password2" type="password" placeholder="请重复您的密码"
                           data-rule="require|password" data-display="重复密码" class="wbk01" />
                    <input id="reg_verify" type="text" class="wbk01"  placeholder="请输入验证码"
                           data-rule="require" data-display="验证码" name="verify" style="width:166px;" />
                    <img id="verifyImg" src="<?php echo U('Default/verify');?>" border="0" title="点击刷新验证码" style="cursor:pointer;margin-bottom:10px;" align="absmiddle" />
                    <div class="pb5"><label for="checkbox_agree" class="reg_checkbox">
                        <input type="checkbox" value="1" id="checkbox_agree"
                               data-rule="require" data-display="服务条款" />&nbsp;&nbsp;&nbsp;<span style="color:#FFFFFF;cursor:pointer;">已经阅读并同意</span>
                    </label><a class="serviceTerm" href="javascript:;">服务条款</a>
                        <div class="clear"></div>
                    </div>
                    <div class="error_msg" style="color: #b40504;margin-top: 5px;display: none;"></div>
                    <div class="submit_msg" style="color: #FFFFFF;margin-top: 5px;display: none;">正在注册请稍候...</div>
                    <div id="zhuceSubmit" class="an03 pt20"><a href="javascript:;">注册新帐号</a></div>
                </fieldset>
            </form>
        </div>
    </div>
    <div class="bot"><img src="/Public/newAat/images/box01_bot_02.png"/></div>
</div>
<!--登录前显示界面-->
<div id="dlzc" style="display: none;">
    <div id="an_login"><a class="dlzc_an fl" href="javascript:;">登录</a></div>
    <div id="an_zhuce"><a class="dlzc_an fr" href="javascript:;">注册</a></div>
</div>
<!--登录后显示首页模块菜单-->
<div id="content" class="w980 mc" style="display:none">
    <div id="index_nav" class="mb10">
        <a class="index_nav01 fl" href="javascript:;">
            <div class="nav_a"><span class="nav_ico nav_ico01"><img style="position: absolute;" src="/Public/newAat/images/index_nav_01.png"/></span><span
                    class="nav_bt">自适应训练</span></div>
        </a>
        <a class="index_nav02 fl" href="javascript:;">
            <div class="nav_a"><span class="nav_ico nav_ico01"><img style="position: absolute;" src="/Public/newAat/images/index_nav_02.png"/></span><span
                    class="nav_bt">专题模块训练</span></div>
        </a>
        <a class="index_nav03 fl" href="javascript:;">
            <div class="nav_a"><span class="nav_ico nav_ico01"><img style="position: absolute;" src="/Public/newAat/images/index_nav_03.png"/></span><span
                    class="nav_bt">整卷练习</span></div>
        </a>
        <a class="index_nav04 fl" href="javascript:;">
            <div class="nav_a"><span class="nav_ico nav_ico01"><img style="position: absolute;" src="/Public/newAat/images/index_nav_04.png"/></span><span
                    class="nav_bt">目标训练</span></div>
        </a>
    </div>
</div>
<!--第一次登录后补充个人信息-->
<div class="box02 xxws" style="display: none;">加载中...</div>
<div class="box03 userChapter" style="display: none;">加载中...</div>
<!--自适应训练-->
<div class="box03 znlx" style="display: none;">
    <div class="top">
        <div class="top_left fl"></div>
        <div class="top_cen fc"><font face="微软雅黑" class="fl pl7">自适应训练</font><a class="fr an_close" href="javascript:;"></a></div>
        <div class="top_right fr"></div>
    </div>
    <div class="zj">
        <div class="tsxx">
            <p><font face="微软雅黑"> 科学定位你的能力水平，一对一智能推送试题! </font></p>

            <p><font face="微软雅黑"> 赶快练习吧！</font></p>
        </div>
        <div class="error_msg" style="color: #b40504;text-align: center;display: none;"></div>
        <div class="submit_msg" style="position: relative; display: none; width: 60%;margin: 0 auto;">
            <div style="position: absolute;left: 30%;top: 5px;">正在为您智能组卷，请稍等...</div>
        </div>
        <div class="an03 pt20"><a id="znlx_submit" href="javascript:;">开始练习</a></div>
    </div>
    <div class="bot">
        <div class="bot_left fl"></div>
        <div class="bot_cen fc"></div>
        <div class="bot_right fr"></div>
    </div>
</div>
<!--专题模块训练-->

<!--整卷练习-->
<div class="box03 tjcs" style="display: none;">
    <div class="top">
        <div class="top_left fl"></div>
        <div class="top_cen fc"><font face="微软雅黑" class="fl pl7">整卷练习</font><a class="fr an_close" href="javascript:;"></a></div>
        <div class="top_right fr"></div>
    </div>
    <div class="zj">

    </div>
    <div class="bot">
        <div class="bot_left fl"></div>
        <div class="bot_cen fc"></div>
        <div class="bot_right fr"></div>
    </div>
</div>
<!--目标训练-->
<div class="box03 jjts" style="display: none;">
    <div class="top">
        <div class="top_left fl"></div>
        <div class="top_cen fc"><font face="微软雅黑" class="fl pl7">目标训练</font><a class="fr an_close" href="javascript:;"></a></div>
        <div class="top_right fr"></div>
    </div>
    <div class="zj">
        <div class="ejcd">
            <a class="jdlx_go" href="javascript:;"><font face="微软雅黑">阶段练习</font></a>
            <a class="mbcs_go" href="javascript:;"><font face="微软雅黑">目标测试</font></a>
        </div>
        <!--阶段练习-->
        <div id="s_jdlx" style="display: none;">
            <!--tab-->
            <div class="tabnav01">
                <a data="e" class="this">基础练习</a>
                <a data="d">达标训练</a>
                <a data="c">巩固提升</a>
                <a data="b">难点突破</a>
                <a data="a">冲刺高分</a>
            </div>
            <div class="title_bz">
                <div class="fl bz_title_box"><div class="tit">选择知识点范围</div></div>
                <a class="an02 fr mr5 select_all" href="javascript:;" flag="0"><span class="an_left"></span><span class="an_cen">全部选择</span><span class="an_right"></span></a>
                <div class="clear"></div>
            </div>
            <!--知识点-->
            <ul class="list_zsd">
                知识点加载中，请稍候...
            </ul>
            <div class="error_msg" style="color: #b40504;text-align: center;display: none;"></div>
            <div class="submit_msg" style="position: relative; display: none; width: 60%;margin: 20px auto 0;">
                <div style="position: absolute;left: 30%;top: 5px;">正在为您智能组卷，请稍等...</div>
            </div>
            <div class="an03 pt20 pb10 mc start_button"> <a href="javascript:;">开始练习</a></div>
        </div>
        <!--目标测试-->
        <div id="s_mbcs" class="mbcs" style="display: none;">
            <div class="tsxx s_no_aim" style="display: none;">
                <p><font face="微软雅黑"> 您还没有制定学习目标哦! </font></p>
                <p><font face="微软雅黑"> 点击下方按钮快速制定您的私人目标提升测试！</font></p>
            </div>
           <div class="an03 s_no_aim  pb20 pt20"> <a id="s_new_aim" href="javascript:;">新建目标</a></div>
            <!--目标测试第一步-->
            <div id="s_mbcs_01" style="display: none;">
                <div class="title_bz">
                    <div class="fl bz_title_box"><span class="bz_txt mr10">第一步</span>
                        <div class="tit">设置分数</div>
                    </div>
                    <a class="an02 fr mr5" href="javascript:;"><span class="an_left"></span><span class="an_cen set_default">默认设置</span><span class="an_right"></span></a>
                    <div class="clear"></div>
                </div>
                <div class="mc" id="diff">
                    <div class="hk">
                        <div class="hkbox_nav">请选择目标分数：</div>
                        <div id="hkbg" class="hkbg">
                            <div class="hk_sz">
                                <span style="float:left">0</span>
                                <span style="float:right" class="maxScore">150</span>
                                <p>
                                    <span style="float:left">低</span>
                                    <span style="float:left; font-size:12px; color:#888888; margin-left:95px;" class="extent">合理区间为[90-140]</span>
                                    <span style="float:right">高</span>
                                </p>
                            </div>
                            <div class="slider_cover"></div>
                            <div id="slider_cover_num" style="left: 177px;" class="hkbox">90分</div>
                        </div>
                    </div>
                </div>
                <div class="error_msg" style="color: #b40504;text-align: center;display: none;"></div>
                <div class="an03 pt20 pb10 mc next"><a href="javascript:;">下一步</a></div>
            </div>
            <div id="s_mbcs_02" style="display: none;">
                <!--目标测试第二步-->
                <div class="title_bz">
                    <div class="fl bz_title_box"><span class="bz_txt mr10">第二步</span><div class="tit">选择考点范围</div></div>
                    <a class="an02 fr mr5 select_all" href="javascript:;" flag="0"><span class="an_left"></span><span class="an_cen">全部选择</span><span class="an_right"></span></a>
                    <div class="clear"></div>
                </div>
                <ul class="list_zsd">加载中...</ul>
                <div class="error_msg" style="color: #b40504;text-align: center;display: none;"></div>
                <div class="submit_msg" style="position: relative; display: none; width: 60%;margin: 20px auto 0;">
                    <div style="position: absolute;left: 30%;top: 5px;">正在为您添加目标测试，请稍等...</div>
                </div>
                <div class="an03 pt20 pb10 mc next"> <a href="javascript:;">添加目标</a></div>
            </div>
            <div id="s_mbcs_list" style="display: none;">
                <div class="pt15" id="s_add_aim"><a class="tjmu_box"><span></span>添加目标</a></div>
                <div class="stlb01">
                    <ul class="list_stlb01" style="height: 350px;overflow-y: auto;padding-right: 10px;">加载中...
                    </ul>
                </div>
                <div class="pagination"></div>
            </div>
        </div>
    </div>
    <div class="bot">
        <div class="bot_left fl"></div>
        <div class="bot_cen fc"></div>
        <div class="bot_right fr"></div>
    </div>
</div>
<!--显示Msg信息-->
<div class="box02 msgBox" style="display: none;">
    <div class="top">
        <div class="top_left fl"></div>
        <div class="top_cen fc">提示信息</div>
        <div class="top_right fr"></div>
    </div>
    <div class="zj">
        <div class="content">
            <div class="msg"></div>
            <fieldset class="actions">
                <input type="button" class="alertOk" id="submit01" value="确 定">
            </fieldset>
        </div>
    </div>
    <div class="bot">
        <div class="bot_left fl"></div>
        <div class="bot_cen fc"></div>
        <div class="bot_right fr"></div>
    </div>
</div>

<!--页面尾部footer-->
<div id="footer">
    <div class="foot">
    <div class="w980 mc">
        <div class="foot_nr">
           
        </div>
    </div>
</div>
</div>
<img src="/Public/newAat/images/loading0.gif" alt="预加载" style="display: none;" />
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var userCode = AatCommon.getUserCode();
        var userName = AatCommon.getUserName();
        AatCommon.showBrowserInfo();
        //初始化Dom大小/初始化icheck/初始化页面消息
        init();
        //左侧选择学科
        effect_left();
        if(!userCode||!userName){
            //没有登录
            //登录注册切换效果
            effect_login_reg();
            //是否显示版本
            var versionID= AatCommon.getVersionID();
            if(versionID==undefined || versionID==0){
                $('.bb_box').css({'display':'none'});
            }else if(versionID==1){
                $('.bb_box').css({'display':'block'});
                $('.bb_box').html('<a href="'+U('User/Aat/index')+'"><font face="微软雅黑">高考冲刺版</font></a>');
            }else{
                $('.bb_box').css({'display':'block'});
                $('.bb_box').html('<a href="'+U('User/Aat/index')+'"><font face="微软雅黑">同步学习版</font></a>');
            }
        }else{
            //已登录
            login_success();
        }
        $('input[placeholder]').placeholder();
        //刷新验证码
        $('#verifyImg').click(function(){
            //重载验证码
            var timenow = new Date().getTime();
            $('#verifyImg').attr('src',U('Default/verify')+'-'+timenow);
        });
    });
    function login_success(){
        //隐藏登录注册相关信息
        $('#dlzc,.login_box,.zhuce_box').hide();
        $('.head_02').removeClass('pt20');
        $('.logo_img').slideDown(400,function(){});
        var user_name = AatCommon.getUserName();
        //是否显示版本
        var versionID= AatCommon.getVersionID();
        if(versionID==undefined || versionID==0){
            $('.bb_box').css({'display':'none'});
        }else if(versionID==1){
            $('.bb_box').css({'display':'block'});
            $('.bb_box').html('<a class="verBtn" href="'+U('User/Aat/index')+'"><font face="微软雅黑">高考冲刺版</font></a>');
        }else{
            $('.bb_box').css({'display':'block'});
            $('.bb_box').html('<a class="verBtn" href="'+U('User/Aat/index')+'"><font face="微软雅黑">同步学习版</font></a>');
        }
        //四个测试按钮的效果
        effect_exercise();
        //用户中心操作
        effect_user(user_name);
        //补充用户信息
        add_user_info();
        //自适应训练
        exercise_01();
        //专题模块训练
        exercise_02();
        //整卷练习
        exercise_03();
        //目标训练
        exercise_04();
    }
    function init(){
        var window_height = $(window).height();
        var window_width = $(window).width();
        var subOuter = $('#leftsub').outerWidth();
        //$('body').height($(window).height());
        $('#xk_box').height(window_height-190);
        $('.xk_list').slimScroll({
                height:'auto',
                size:'7px',
                wheelStep:5,
                touchScrollStep:200,
                distance:'2px',
                railVisible:true,
                railOpacity:1,
                allowPageScroll:false
        });
        //$('body').width(window_width-20);
        _init_icheck();
        $(window).resize(function(){
            window_height = $(window).height();
            window_width = $(window).width();
            //if(window_height<575) window_height = 575;
            if(window_width<980) window_width = 980;
            //$('body').height(window_height);
            $('#xk_box').height(window_height-190);
            $('.xk_list').slimScroll({
                height:'auto',
                size:'7px',
                wheelStep:5,
                touchScrollStep:200,
                distance:'2px',
                railVisible:true,
                railOpacity:1,
                allowPageScroll:false
            });
            $('body').width(window_width);
        });
        $(document).on('ifChecked','input:checkbox',function(){$(this).attr({'checked':'checked'})});
        $(document).on('ifUnchecked','input:checkbox',function(){$(this).removeAttr('checked')});
        //初始化错误信息
        // AatCommon.showMsg(null);
    }
    function _init_icheck(){
        $('input:checkbox').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue',
            increaseArea: '20%'
        });
        $('input:checkbox').parent().parent().show();
    }
    function subject_display(){
        $.post(U('Default/ajaxSubject'),{times:Math.random()},function(e){
            var subject_id = AatCommon.getSubjectID();
            var str = '';
            if(e.status == 1){
                $.each(e.data,function(i,k){
                    var class_this = '';
                    if(k.subject_id == subject_id){
                        class_this = 'xk_this';
                    }
                    str += '<li id="subject_'+ k.subject_id+'" data="'+ k.subject_id+'" class="'+class_this+'"><a>'+ k.subject_name+'</a></li>';
                });
            }else{
                str =   '<li id="subject_12" data="12"><a>语文</a></li>'+
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
    }
    function effect_left(){
        //学科添加
        subject_display();

        var left = $('#leftsub');
        var leftsub_an  = $('.leftsub_an');
        $('.leftsub_an_no').click(function(){
            left.toggle('slide',{ direction: "left" }, 500);
            leftsub_an.toggleClass('effect_left_170',500);
            $(this).hide();
            $('.leftsub_an_off').show();

        });
        $('.leftsub_an_off').on('click',function(){
            left.toggle('slide',{ direction: "left" }, 500);
            leftsub_an.toggleClass('effect_left_170',500);
            $(this).hide();
            $('.leftsub_an_no').show();
        });

        $('.xk_list').on('click', 'li', function () {
            //验证是否登录
            if (!AatCommon.getUserCode()) {
                AatCommon.setMsg('请登录后操作！');
                AatCommon.showMsg(null);
                return false;
            }
            $('.xk_list li').removeClass('xk_this');
            AatCommon.setSubjectID($(this).attr('data'));
            $(this).addClass('xk_this');
            //点击选择章节
            $.post(U('Chapter/check'),{times:Math.random()}, function (e) {
                if (e.status == 0) {
                    //需要补充用户信息
                    $.get(U('Chapter/index'),{times:Math.random()}, function (data) {
                        $('.userChapter').html(data);
                    });
                    //载入页面内容后执行
                    $('.userChapter').aBox({
                        height: 350
                    });
                    return false;
                }
            });
        });
        //点击我的练习和学情评估时检查登录
        $('#s_pg_cs a').click(function () {
            var self = this;
            AatCommon.checkCondition(function () {
                window.location.href = $(self).attr('data');
            });
        });
    }
    function effect_login_reg(){
        //显示登录注册按钮
        $('#dlzc').show();
        //切换登录
        $('#an_login').on('click',function(){
            // $('.head_02').addClass('pt20');
            $('#an_login a').addClass('this');
            $('#an_zhuce a').removeClass('this');
            $('.logo_img').hide();
            $('.zhuce_box').slideUp(350,function(){
                $('.login_box').slideDown(550);
            });
        });
        //切换注册
        $('#an_zhuce').on('click',function(){
            location.href="<?php echo U('User/Index/registerIndex?who=student');?>";
            return false;
            // $('.head_02').addClass('pt20');
            /*$('#an_zhuce a').addClass('this');
            $('#an_login a').removeClass('this');
            $('.logo_img').hide();
            $("#head").css({"margin-bottom":"10px"});
            $('.login_box').slideUp(350,function(){
                $('.zhuce_box').slideDown(550);
            });*/
        });
        //登录系统
        var loginValidate = function(errors){
            var username=$('.login_box #login_username').val();
            var password=$('.login_box #login_password').val();
            var remember=$('.login_box #checkbox_remember:checked').val()?1:0;
            if(errors.length>0){
                $('.login_box .error_msg').html(errors[0]+'！').show().effect('shake');
            }else{
                $('.login_box .error_msg').hide();
                $('.login_box .submit_msg').show();
                $.post(U('Default/login'),{'UserName':username,'Password':password,'Remember':remember,'times':Math.random()},function(e){
                    $('.login_box .submit_msg').hide();
                    if(e.status==1){
                        var url = $('#login').find('input[name="jumpUrl"]').val();
                        if(url != ''){
                            url = decodeURIComponent(url);
                            window.location.href = url;
                            return false;
                        }
                        //登录成功更新本地csrf
                        $('#_csrf').attr('content', e.data._csrf);
                        AatCommon.setCsrfAjaxData(e.data._csrf);
                        //更新学科列表
                        subject_display();
                        //其他操作
                        login_success();
                    }else{
                        $('.login_box .error_msg').html(e.data).show().effect('shake');
                    }
                });
            }
        };
        $('#loginSubmit').click(function(){
            $('#login').validate({callback:loginValidate});
        });
        //回车键登录
        $('.login_box #login_password').on('keydown',function(e){
            if(e.keyCode==13) $('#loginSubmit').trigger('click');
        });
        //用户注册
        var registerValidate = function(errors){
            var username=$('.zhuce_box #reg_username').val();
            var password=$('.zhuce_box #reg_password').val();
            var password2=$('.zhuce_box #reg_password2').val();
            var verify=$('.zhuce_box #reg_verify').val();
            if(errors.length>0){
                $('.zhuce_box .error_msg').html(errors[0]+'！').show().effect('shake');
            }else if(password!=password2){//单独处理的验证
                $('.zhuce_box .error_msg').html('两次输入的密码不一致').show().effect('shake');
            }else{
                $('.zhuce_box .error_msg').hide();
                $('.zhuce_box .submit_msg').show();
                $.post(U('Default/register'),{'username':username,'password':password,'password2':password2,'verify':verify,'times':Math.random()},function(e){
                    $('.zhuce_box .submit_msg').hide();
                    if(e.status==1){
                        AatCommon.setMsg('注册成功，请登录！');
                        location.href=U('Default/index');
                    }else{
                        $('.zhuce_box .error_msg').html(e.data).show().effect('shake');
                    }
                });
            }
        };
        $('#zhuceSubmit').click(function(){
            $('#zhuce').validate({callback:registerValidate});
        });
    }
    function effect_exercise(){
        $('#content').show();
        var imgWid = 0;
        var imgHei = 0;
        var big = 10;//放大像素
        $('#index_nav a').hover(
                function(){
                    $(this).find("img").stop(true,true);
                    imgWid = $(this).find("img").width();
                    imgHei = $(this).find("img").height();
                    var imgWid2 = imgWid + big;
                    var imgHei2 = imgHei + big;
                    $(this).find("img").css({"z-index":2});
                    $(this).find("img").animate({"width":imgWid2,"height":imgHei2,'left':-big/2,'top':-big/2});
                    //$(this).find('.nav_bt').animate({'font-size':'24px','paddingTop':25});
                },
                function(){
                    $(this).find("img").stop().animate({"width":imgWid,"height":imgHei,'left':0,'top':0,'z-index':1});
                    //$(this).find('.nav_bt').stop().animate({'font-size':'20px','paddingTop':15});
                }
        );
    }
    function effect_user(user_name){
        $('.dlxx01').show();
        //截取用户昵称或者用户名
        //中文按照2个字符计算
        var user_name_length = user_name.replace(/[^\x00-\xff]/g, 'xx').length;
        if(user_name_length>24){
            user_name = user_name.substring(0,21)+'...';
        }else{
            var left = (20-user_name_length)/2*3;
            $('.userid').css('left',left);
            $('.userid').next().css('left',left);
        }
        $('.userid').html(user_name);
        $('.head_01 .dlxx01').hover(
                function () {
                    $('.head_01 .ico_jt01').switchClass('ico_jt01', 'ico_jt02', 10);
                    $('.head_01 #dlxx_box').show();
                },
                function () {
                    $('.head_01 .ico_jt02').switchClass('ico_jt02', 'ico_jt01', 10);
                    $('.head_01 #dlxx_box').hide();
                }
        );
        $('.user_logout').click(function(){
            $.get(U('Default/logout'),{times:Math.random()},function(e){
                if(e.status==1){
                    AatCommon.setMsg(e.data);
                    window.location = U('User/Index/passport');
                }else{
                    alert('退出失败请重试！');
                }
            })
        });
    }
    function add_user_info(){
        //检查是否需要补充用户信息
        var addUserInfo = $('.xxws');
        $.post(U('AddUserInfo/check'),{'times':Math.random()},function(e){
            if(e.data!='success'){
                //需要补充的字段写入data属性
                addUserInfo.attr('data', e.data);
                //需要补充用户信息
                $.post(U('AddUserInfo/index'),function(html){
                    addUserInfo.html(html);
                });
                addUserInfo.aBox({width:328,height:418});
            }
        });
    }
    function exercise_01() {
        //显示对话框
        $('.index_nav01').click(function(){
            AatCommon.checkCondition(function(){
                _box('.znlx',true,{width:728,height:394});
            });
        });
        //正在出题请稍候
        $('#znlx_submit').click(function(){
            //隐藏错误信息
            $('.znlx .error_msg').hide();
            //隐藏出题按钮
            $(this).parent().hide();
            //显示进度条
            $('.znlx .submit_msg').show();
            $(".znlx .submit_msg").progressbar({
                value: false
            });
            $.post(U('Default/ajaxGetTest'),{'id':1,'SubjectID': AatCommon.getSubjectID(),times:Math.random()},function(e){
                if(e.status == 1){
                    window.location.href=U('Exercise/index','id='+e.data.record_id);
                }else{
                    //隐藏进度条
                    $('.znlx .submit_msg').hide();
                    //显示出题按钮
                    $('.znlx .an03').show();
                    //显示错误信息
                    $('.znlx .error_msg').html(e.data).show().effect('shake');
                }
            });
        });
        //关闭对话框
        $('.znlx .an_close').click(function(){
            _box('.znlx',false);
        });
    }
    function exercise_02() {
        var dialog = function(){
            $.aDialog({
                width:728,
                height:463,
                title:'专题模块训练',
                success:function(){
                    $.post(U('PushTest/special'),{times:Math.random()},function(e){
                        $('#dialogAat .content').append(e.data);
                    });
                }
            });
        };
        $('.index_nav02').click(function(){
            AatCommon.checkCondition(dialog);
        });
    }
    function exercise_03() {
        //远程获取知识点详情
        $('.index_nav03').click(function(){
            var dialog = function(){
                $.aDialog({
                    width:800,
                    height:520,
                    title:'整卷练习',
                    success:function(){
                        $.post(U('PushTest/testPaper'),{times:Math.random()},function(e){
                            $('#dialogAat .content').append(e.data);
                        });
                    }
                });
            };
            AatCommon.checkCondition(dialog);
        });
    }
    function exercise_04() {
        //打开弹窗
        $('.index_nav04').click(function(){
            var dialog = function(){
                $('#s_jdlx,#s_mbcs').hide();
                $('.jjts .ejcd').show();
                $('.jjts .jdlx_go').click(function(){
                    //阶段练习
                    $('.jjts .ejcd').hide();
                    $('#s_jdlx .tabnav01 a').removeClass('this');
                    $('#s_jdlx .tabnav01 a[data="e"]').addClass('this');
                    _getLevelKl('e');
                    $('#s_jdlx').show();
                });
                $('.jjts .mbcs_go').off();
                $('.jjts .mbcs_go').on('click',function(){
                    //目标测试
                    $('.jjts .ejcd,#s_jdlx,#s_mbcs_02,#s_mbcs_01,#s_mbcs_list,.s_no_aim').hide();
                    _getAimRecord(1);
                    $('#s_mbcs').show();
                });
                _box('.jjts',true,{width:728,height:508});
            };
            AatCommon.checkCondition(dialog);
        });
        exercise_04_01();
        exercise_04_02();
        //关闭弹窗
        $('.jjts .an_close').click(function(){
            _box('.jjts',false);
        });
    }
    function exercise_04_01(){
        //阶段练习TAB
        $('#s_jdlx .tabnav01 a').click(function(){
            $('#s_jdlx .tabnav01 a').removeClass('this');
            $(this).addClass('this');
            $('#s_jdlx .select_all').attr('flag',0);
            $('#s_jdlx .select_all .an_cen').html('全部选择');
            //隐藏错误信息
            $('#s_jdlx .error_msg').hide();
            _getLevelKl($(this).attr('data'));
        });
        //树形结构绑定点击事件
        $('#s_jdlx .list_zsd').on('click','.zsd_bt',function(){
            $(this).parent().find('.ico_zd_01').switchClass('ico_zd_01','ico_zd_02',1);
            $(this).parent().find('.ico_zd_02').switchClass('ico_zd_02','ico_zd_01',1);
            var id = $(this).attr('klid');
            $('.sub_kl_id_'+id).toggle('blind');
        });
        //知识点全选事件
        $('#s_jdlx .select_all').click(function(){
            if($(this).attr('flag')== 0){
                $(this).attr('flag',1);
                $('#s_jdlx').find('input:checkbox').iCheck('check');
                $('#s_jdlx .select_all .an_cen').html('全部取消');
            }else if($(this).attr('flag') == 1){
                $(this).attr('flag',0);
                $('#s_jdlx').find('input:checkbox').iCheck('uncheck');
                $('#s_jdlx .select_all .an_cen').html('全部选择');
            }
        });
        //点击checkbox事件绑定
        $('#s_jdlx .list_zsd').on('ifChecked','input:checkbox',function(){
            $('#s_jdlx .sub_kl_id_'+$(this).val()).find('input:checkbox').iCheck('check');
        });
        $('#s_jdlx .list_zsd').on('ifUnchecked','input:checkbox',function(){
            $('#s_jdlx .sub_kl_id_'+$(this).val()).find('input:checkbox').iCheck('uncheck');
        });
        $('#s_jdlx .start_button').click(function(){
            if($('#s_jdlx input:checkbox[checked=checked]').length<1){
                $('#s_jdlx .error_msg').html('至少选择一个知识点！').show().effect('shake');
                return false;
            }
            //隐藏错误信息
            $('#s_jdlx .error_msg').hide();
            //隐藏出题按钮
            $(this).hide();
            //显示进度条
            $('#s_jdlx .submit_msg').show().progressbar({
                value: false
            });
            var kl_id = '';
            $('#s_jdlx input:checkbox[checked=checked]').each(function(){kl_id += $(this).val()+','});
            kl_id  = kl_id.substring(0,kl_id.length-1);
            $.post(U('Default/ajaxGetTest'),
                    {'id': 5, 'SubjectID': AatCommon.getSubjectID(), 'KlID': kl_id,times:Math.random()}, function (e) {
                        if (e.status == 1) {
                            window.location.href = U('Exercise/index','id=' + e.data.record_id);
                        } else {
                            //隐藏进度条
                            $('#s_jdlx .submit_msg').hide();
                            //显示出题按钮
                            $('#s_jdlx .start_button').show();
                            //显示错误信息
                            $('#s_jdlx .error_msg').html(e.data).show().effect('shake');
                        }
                    });
        });

    }
    function exercise_04_02(){
        //点击添加目标测试第一步
        $('#s_mbcs').on('click','#s_new_aim',function(){
            _getNormRecord();
           
            //目标分数默认值
            $('#s_mbcs').on('click','.set_default',function(){
                _getNormRecord();
            });
        });
        //点击下一步进入第二步
        $('#s_mbcs_01').on('click','.next',function(){
            $('#s_mbcs_01').hide();
            $.post(U('Default/ajaxKl'),{'subject_id': AatCommon.getSubjectID(),times:Math.random()},function(e){
                if(e.status == 1){
                    var str = '';
                    $.each(e.data,function(i,k){
                        //判断是否有下级知识点
                        var ico = k.sub?'ico_zd_01':'ico_zd_03';
                        str += '<li><a class="ico_zd '+ico+' fl"></a><div klid="'+ k.klID+'" class="zsd_bt fl" style="cursor: pointer">'+ k.klName+'</div>'+
                                '<div class="fr" style="margin-right: 10px;display: none;"><input type="checkbox" name="tjcs_klid" value="'+ k.klID+'"></div></li>';
                        if(k.sub){
                            $.each(k.sub,function(j,m){
                                str += '<li class="lidj02 sub_kl_id_'+ k.klID+'" style="display: none;"><a class="ico_zd ico_zd_03 fl"></a><div class="zsd_bt fl">'+ m.klName+'</div>'+
                                        '<div class="fr" style="margin-right: 10px;display: none;"><input type="checkbox" name="tjcs_klid" value="'+ m.klID+'"></div></li>';
                            });
                        }
                    });
                    $('#s_mbcs_02 .list_zsd').html(str);
                    _init_icheck();
                }else{
                    $('#s_mbcs_02 .list_zsd').html(e.data);
                }
            });
            $('#s_mbcs_02').show(function(){
                //隐藏进度条
                $('#s_mbcs_02 .submit_msg').hide();
                //显示出题按钮
                $('#s_mbcs_02 .next').show();
                //复位全部选择
                $('#s_mbcs_02 .select_all').attr('flag',0);
                $('#s_mbcs_02 .select_all .an_cen').html('全部选择');
            });
        });
        //第二步-树形结构绑定点击事件
        $('#s_mbcs_02 .list_zsd').on('click','.zsd_bt',function(){
            $(this).parent().find('.ico_zd_01').switchClass('ico_zd_01','ico_zd_02',1);
            $(this).parent().find('.ico_zd_02').switchClass('ico_zd_02','ico_zd_01',1);
            var id = $(this).attr('klid');
            $('.sub_kl_id_'+id).toggle('blind');
        });
        //第二步-知识点全选事件
        $('#s_mbcs_02 .select_all').click(function(){
            if($(this).attr('flag')== 0){
                $(this).attr('flag',1);
                $('#s_mbcs_02').find('input:checkbox').iCheck('check');
                $('#s_mbcs_02 .select_all .an_cen').html('全部取消');
            }else if($(this).attr('flag') == 1){
                $(this).attr('flag',0);
                $('#s_mbcs_02').find('input:checkbox').iCheck('uncheck');
                $('#s_mbcs_02 .select_all .an_cen').html('全部选择');
            }
        });
        $('#s_mbcs_02 .list_zsd').on('ifChecked','input:checkbox',function(){
            $('#s_mbcs_02 .sub_kl_id_'+$(this).val()).find('input:checkbox').iCheck('check');
        });
        $('#s_mbcs_02 .list_zsd').on('ifUnchecked','input:checkbox',function(){
            $('#s_mbcs_02 .sub_kl_id_'+$(this).val()).find('input:checkbox').iCheck('uncheck');
        });
        //第二步添加目标
        $('#s_mbcs_02').on('click','.next',function(){
            if($('#s_mbcs_02 input:checkbox[checked=checked]').length<1){
                $('#s_mbcs_02 .error_msg').html('至少选择一个知识点！').show().effect('shake');
                return false;
            }else{
                //隐藏错误信息
                $('#s_mbcs_02 .error_msg').hide();
                //隐藏出题按钮
                $(this).hide();
                //显示进度条
                $('#s_mbcs_02 .submit_msg').show().progressbar({
                    value: false
                });
                var kl_id = '';
                $('#s_mbcs_02 input:checkbox[checked=checked]').each(function(){kl_id += $(this).val()+','});
                var kl_id  = kl_id.substring(0,kl_id.length-1);
                var score = $('#s_mbcs_01 .slider_cover').slider('value');
                var total_score = 150;
                $.post(U('Default/ajaxGetTest'), {'id': 6, 'SubjectID': AatCommon.getSubjectID(), 'KlID': kl_id,'Score':score,'TotalScore':total_score,times:Math.random()}, function (e) {
                    if (e.status == 1) {
                        //隐藏第一步 第二步
                        $('#s_mbcs_01,#s_mbcs_02,.s_no_aim').hide();
                        //显示目标测试记录
                        _getAimRecord(1);
                        //点击ajax换页
                    } else {
                        //隐藏进度条
                        $('#s_mbcs_02 .submit_msg').hide();
                        //显示出题按钮
                        $('#s_mbcs_02 .next').show();
                        //显示错误信息
                        $('#s_mbcs_02 .error_msg').html(e.data).show().effect('shake');
                    }
                });
            }
        });
        //第二步翻页
        $('#s_mbcs_list').on('click','.ajax_page_class',function(){
            _getAimRecord($(this).attr('data'));
        });
        //添加目标按钮
        $('#s_mbcs_list').on('click','#s_add_aim',function(){
            _getNormRecord()
        });
    }
    //目标训练-目标测试-获取各学科标准卷分数
    function _getNormRecord(){
        $.post(U('Default/ajaxNormRecord'),{'subjectID':AatCommon.getSubjectID(),times:Math.random()},function(e){
            if(e.status==1){
                var defaultScore,defaultCss,num,extent;
                if(e.data.score==150){
                    defaultScore=90;//默认分数
                    defaultCss=177;//默认样式
                    num=2.3;//样式系数,用于设置分数时的样式
                    extent='合理区间为[90-140]';
                }else if(e.data.score==100){
                    defaultScore=60;
                    defaultCss=175;
                    num=3.4;
                    extent='合理区间为[60-90]';
                }
                //隐藏其它界面
                $('#s_mbcs .s_no_aim,#s_mbcs_02,#s_mbcs_list').hide();
                //显示目标测试第一步设置分数
                $('#s_mbcs_01').show(function(){
                    $('.maxScore').html(e.data.score);
                    $('#s_mbcs #slider_cover_num').html(defaultScore+'分').css('left',defaultCss);
                    $('#s_mbcs .extent').html(extent);
                });
                //设置目标分数
                $('#s_mbcs .slider_cover').slider({min:1,max:e.data.score,range:'min',value:defaultScore,slide:function(e,ui){
                    $('#s_mbcs #slider_cover_num').html(ui.value+'分').css('left',num*ui.value-30);
                }});
            }
        })
    }
    //目标训练-目标测试-获取目标测试记录
    function _getAimRecord(p){
        $.post(U('Default/ajaxAimRecord'),{'p':p,'subject_id': AatCommon.getSubjectID(),times:Math.random()},function(e){
            if(e.status == 0){
                $('#s_mbcs .s_no_aim').show();
            }else{
                var str = '';
                $.each(e.data.list,function(i,k){
                    var an;
                    var button_name;
                    var url;
                    if(k.is_submit == 1){
                        an = 'an01';
                        url = U('ExerciseReport/index','id='+ k.test_record_id);
                        button_name = '查看报告';
                    }else{
                        an = 'an02';
                        url = U('Exercise/index','id='+ k.test_record_id);
                        button_name = '开始测试';
                    }
                    str += '<li><div class="st_bt_box fl"><div class="st_bt">目标分数：'+ k.score+'分<span>'+ k.time+'</span></div>'+
                            '<p>考点范围：'+ k.kl_name+'</p></div><div class="an_box fr">'+
                            '<a class="'+an+' fr mr5" href="'+url+'"><span class="an_left"></span><span class="an_cen">'+button_name+'</span><span class="an_right"></span></a>'+
                            '</div></li>';
                });
                $('#s_mbcs_list .list_stlb01').html(str);
                $('#s_mbcs_list .pagination').html(e.data.show);
                $('#s_mbcs_list').show();
            }
        });
    }
    //目标训练-阶段练习-获取知识点树
    function _getLevelKl(level) {
        $.post(U('Default/ajaxLevelKl'),{'subject_id': AatCommon.getSubjectID(),'level':level,times:Math.random()},function(e){
            if(e.status == 1){
                var str = '';
                $.each(e.data,function(i,k){
                    //判断是否有下级知识点
                    var ico = k.sub?'ico_zd_01':'ico_zd_03';
                    str += '<li><div klid="'+ k.kl_id+'" class="zsd_bt fl" style="cursor: pointer"><a class="ico_zd '+ico+' fl"></a>'+
                            k.name+' ('+ k.amount+')</div>'+
                            '<div class="fr" style="margin-right: 10px;display: none;"><input type="checkbox" name="tjcs_klid" value="'+ k.kl_id+'"></div></li>';
                    if(k.sub){
                        $.each(k.sub,function(j,m){
                            str += '<li class="lidj02 sub_kl_id_'+ k.kl_id+'" style="display: none;"><div class="zsd_bt fl"><a class="ico_zd ico_zd_03 fl"></a>'+
                                    m.name+' ('+ m.amount+')</div>'+
                                    '<div class="fr" style="margin-right: 10px;display: none;"><input type="checkbox" name="tjcs_klid" value="'+ m.kl_id+'"></div></li>';
                        });
                    }
                });
                $('#s_jdlx .list_zsd').html(str);
                _init_icheck();
            }else{
                $('#s_jdlx .list_zsd').html(e.data);
            }
        });
    }

    //对话框函数
    function _box(select,show,options){
        options = arguments[2]?arguments[2]:{width:728,height:432};
        var w_width,w_height,left,top;
        if(show){
            _modalDiv(true);
            w_width = $(window).width();
            w_height = $(window).height();
            left = (w_width-options.width)/2;
            top = (w_height-options.height)/2;
            $(select).css({'z-index':99,'position':'absolute','left':left,'top':top});
            $(select).show('drop',{'direction':'up'},400,function(){
                //初始化checkbox并绑定事件
                _init_icheck();
            });
            //窗口大小改变时调整位置
            $(window).resize(function(){
                w_width = $(window).width();
                w_height = $(window).height();
                left = (w_width-options.width)/2;
                top = (w_height-options.height)/2;
                $(select).css({'z-index':99,'position':'absolute','left':left,'top':top});
            });
        }else{
            $(select).hide('drop',{'direction':'up'},400);
            _modalDiv(false);
        }
    }
    //屏蔽层函数
    function _modalDiv(show){
        var div = '<div id="modal_div" style="position: absolute;top:0;left: 0;background: #000000;filter:Alpha(opacity=60);opacity: 0.6;width: 100%;height: 100%;z-index: 98;"></div> ';
        if(show){
            $('#wrapper').append(div);
        }else{
            $('#modal_div').remove();
        }
    }
    //小星星显示函数
    function _star(right,all){
        var str = '';
        if(!all||all==0){
            //E
            str = '<div class="start_box fc" title="知识点还没有测试，请进行测试！">'+
                  '<span class="start_03"></span><span class="start_03"></span><span class="start_03"></span><span class="start_03"></span><span class="start_03"></span>'+
                  '</div>';
        }else{
            var rate = right/all*100;
            if(rate<60){
                //E
                str = '<div class="start_box fc" title="知识点还没有掌握，多加努力！">'+
                        '<span class="start_01"></span><span class="start_03"></span><span class="start_03"></span><span class="start_03"></span><span class="start_03"></span>'+
                        '</div>';
            }else if(rate<70){
                //D
                str = '<div class="start_box fc" title="知识点还没有掌握，还需努力！">'+
                        '<span class="start_01"></span><span class="start_01"></span><span class="start_03"></span><span class="start_03"></span><span class="start_03"></span>'+
                        '</div>';

            }else if(rate<80){
                str = '<div class="start_box fc" title="知识点掌握的一般，还需努力！">'+
                        '<span class="start_01"></span><span class="start_01"></span><span class="start_01"></span><span class="start_03"></span><span class="start_03"></span>'+
                        '</div>';
            }else if(rate<90){
                str = '<div class="start_box fc" title="知识点掌握的不错，还需努力！">'+
                        '<span class="start_01"></span><span class="start_01"></span><span class="start_01"></span><span class="start_01"></span><span class="start_03"></span>'+
                        '</div>';
            }else{
                str = '<div class="start_box fc" title="知识点掌握的不错，继续努力！">'+
                        '<span class="start_01"></span><span class="start_01"></span><span class="start_01"></span><span class="start_01"></span><span class="start_01"></span>'+
                        '</div>';
            }
        }
        return str;
    }
    //服务条款弹出框
    $('.serviceTerm').on('click',function(){
        $.aDialog({
            width:850,
            height:570,
            title:'用户注册协议',
            success:function(){
                $.post(U('Default/getServiceTerm'),{times:Math.random()},function(data){
                    if(data['info']=='success'){
                        $('#dialogAat .content').html(data.data);
                        $('.norBtn').on('click',function(){
                            $('#dialogAat .close').click();
                        });
                    }else if(data['info']=='error'){
                        $('#dialogAat .content').html('<p style="padding:15px 0;text-align: center;font-size: 16px;">服务条款获取失败,请稍后重试</p>');
                    }
                });
            }
        });
    });
</script>
</body>

</html>