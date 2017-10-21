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

    <link href="/Public/newAat/css/avatar.css<?php echo C(WLN_UPDATE_FILE_DATE);?>" rel="stylesheet" type="text/css" />
    <script src="/Public/plugin/uploadify/jquery.uploadify.min.js<?php echo C(WLN_UPDATE_FILE_DATE);?>"></script>
    <script src="/Public/plugin/imgareaselect/jquery.imgareaselect.pack.js<?php echo C(WLN_UPDATE_FILE_DATE);?>"></script>
    <link rel="stylesheet" type="text/css" href="/Public/plugin/uploadify/uploadify.css<?php echo C(WLN_UPDATE_FILE_DATE);?>" />
    <link rel="stylesheet" type="text/css" href="/Public/plugin/imgareaselect/imgareaselect-animated.css<?php echo C(WLN_UPDATE_FILE_DATE);?>" />
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
            <div class="zt_title">
                <div class="fl pt20 pl20">个人中心</div>
            </div>
            <div class="title02">修改头像</div>
            <div class="sctx_box">
                <div class="sctx_title">
                    <h5>请选择上传方式</h5>
                    <p class="smtext">请选择新的照片，文件小于2M，支持图片格式为jpg，png，gif。</p>
                    <input type="file" id="fileUpload"/>
                    <div class="clear"></div></div>
                <div class="sctxyl_box" style="display: none;">
                    <h5>设置个人头像</h5>
                    <p class="smtext">按个人喜好，随意拉动和调整方格的大小直至满意为止，然后按下方的保存按键即可。</p>
                    <div>
                        <div class="txxq_box fl" id="originBox"><img id="originImg" src="" alt="头像裁剪"/></div>
                        <div class="txyl_box fl">
                            <div class="txyl01" id="prevBox"><img id="prevImg" src="" alt="头像预览" /></div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="sctx_an">
                        <p class="smtext" style="color: darkred;display: none;" id="errorMsg"></p>
                        <p class="smtext" style="display: none;" id="infoMsg"></p>
                        <a class="an01 fl" href="javascript:;" id="avatarSave"><span class="an_left"></span><div class="an_cen">保存头像</div><span class="an_right"></span></a>
                        <a class="cxsc_an" href="javascript:;" id="resetUpload"><font color="#0081cb">重新上传</font></a>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <input type="hidden" id="key" value="<?php echo ($key); ?>"/>
            <input type="hidden" id="username" value="<?php echo ($username); ?>"/>
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
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var AatUserPic = {
            picPath:'',//临时文件路径
            positionJson:{},//准备存放图片数据的变量，便于传回裁剪坐标
            init:function(){
                this.avatarUpload();//定义uploadify上传
                this.avatarSave();//头像选区
            },
            //头像上传
            avatarUpload:function(){
                var self = this;
                var key = $('#key').val();//上传验证安全码
                var username = $('#username').val();//用户名
                var fileUpload = $('#fileUpload');
                fileUpload.uploadify({
                    'buttonText': '上传图片',
                    'auto': true,
                    'removeCompleted' : false,
                    'swf': '/Public/plugin/uploadify/uploadify.swf',
                    'uploader': U('User/Aat/avatarUpload?key=' + key + '&username=' + username),
                    'method': 'post',
                    'multi': false,
                    'fileTypeDesc': '请上传.jpg;*.png;*.gif;类型文件',
                    'fileTypeExts': '*.gif; *.jpg; *.png',
                    'fileSizeLimit': '5120',
                    'onSelectError': function (file, errorCode, errorMsg) {
                        switch (errorCode) {
                            case -110:
                                alert('请上传小于' + fileUpload.uploadify('settings', 'fileSizeLimit') + "k的图片！");
                                break;
                        }
                    },
                    'onUploadSuccess': function (file, data, response) {
                        //每次成功上传后执行的回调函数，从服务端返回数据到前端
                        data = eval('(' + data + ')');//格式化json字符串为json对象
                        if(data.data){
                            fileUpload.uploadify('disable', true);  //(上传成功后)'disable'禁止再选择图片
                            picPath = data.data.picPath;
                            $('.sctxyl_box').show();
                            self.avatarSelectArea(data);
                        }else{
                            alert('您上传的图片不正确，请重新上传！');
                            return;
                        }

                    }
                });
            },
            //头像选区
            avatarSelectArea:function(e){
                if (e.status == 0) {
                    alert(e.data);
                    window.location.reload();
                }
                var self = this;
                var originW = e.data.width;//存储原图的宽高，用于计算
                var originH = e.data.height;
                var frameW = 420;  //原图的缩略图固定宽度，作为一个画布，限定宽度，高度自适应，保证了原图比例
                var frameH = Math.round(frameW*originH/originW);//根据原图宽高比和画布固定宽计算画布高，即originImg加载上传图后的高。此处不能简单用.height()获取，有DOM加载的延迟
                var prevFrameW = 150;  //预览图容器的高宽，宽度固定，高为需要裁剪的宽高比决定
                var prevFrameH = 150;
                var rangeX = 1;  //初始缩放比例
                var rangeY = 1;
                var prevImgW = prevFrameW;  //初始裁剪预览图宽高
                var prevImgH = prevFrameW;
                var originImg = $('#originImg');//画布
                var prevImg = $('#prevImg');//预览图

                $('#originBox').height(frameH).width(frameW);
                $('#prevBox').height(prevFrameH).width(prevFrameW);
                originImg.attr('src', e.data.picPath).height(frameH).width(frameW);//显示已上传的图片，此时图片已在服务器上

                //准备好数据后，开始配置imgAreaSelect使得图片可选区
                originImg.imgAreaSelect({
                    //配置imgAreaSelect
                    handles: true,   //选区样式，四边上4个方框
                    fadeSpeed: 300, //选区阴影建立和消失的渐变
                    aspectRatio: '1:1', //比例尺
                    'minHeight': 100,
                    x1: 0, y1: 0, x2: 150, y2: 150,
                    onInit:function(){
                        prevImg.attr('src', e.data.picPath).css({
                            'width':frameW,
                            'height':frameH,
                            'margin-left':0,
                            'margin-top':0
                        });
                    },
                    onSelectChange: function (img, selection) {
                        //选区改变时的触发事件
                        //selection包括x1,y1,x2,y2,width,height几个量，分别为选区的偏移和高宽
                        rangeX = selection.width / frameW;  //依据选取高宽和画布高宽换算出缩放比例
                        rangeY = selection.height / frameH;
                        prevImgW = prevFrameW / rangeX; //根据缩放比例和预览图容器高宽得出预览图的高宽
                        prevImgH = prevFrameH / rangeY;
                        //实时调整预览图预览裁剪后效果
                        prevImg.css({
                            'width': Math.round(prevImgW) + 'px',
                            'height': Math.round(prevImgH) + 'px',
                            'margin-left': '-' + Math.round((prevFrameW / selection.width) * selection.x1) + 'px',
                            'margin-top': '-' + Math.round((prevFrameH / selection.height) * selection.y1) + 'px'
                        });
                    },
                    onSelectEnd: function (img, selection) {
                        //放开选区后的触发事件
                        //计算实际对于原图的裁剪坐标
                        self.positionJson.x1 = Math.round(originW * selection.x1 / frameW);
                        self.positionJson.y1 = Math.round(originH * selection.y1 / frameH);
                        self.positionJson.width = Math.round(rangeX * originW);
                        self.positionJson.height = Math.round(rangeY * originH);
                    }
                });
            },
            //保存头像,重新上传
            avatarSave:function(){
                var self = this;
                $('#avatarSave').click(function(){
                    var width = self.positionJson.width;
                    var height = self.positionJson.height;
                    var x1 = self.positionJson.x1;
                    var y1 = self.positionJson.y1;
                    $('#infoMsg').html('正在提交请稍候...').show();
                    $('#errorMsg').hide();
                    if($('#originImg').attr('src') == ''){
                        $('#infoMsg').hide();
                        $('#errorMsg').html('请先上传头像！').show().effect('shake');
                        return false;
                    }
                    if (!self.positionJson.width) {
                        $('#infoMsg').hide();
                        $('#errorMsg').html('请先移动虚线框裁剪头像！').show().effect('shake');
                        return false;
                    }
                    $.post(U('User/Aat/avatarSave'), {picPath: picPath, width: width, height: height, x1: x1, y1: y1}, function (e) {
                        if (e.status == 1) {
                            window.location.href = U('User/Aat/index');
                        } else {
                            $('#infoMsg').hide();
                            $('#errorMsg').html(e.data).show().effect('shake');
                            return false;
                        }
                    });
                });
                $('#resetUpload').click(function(){
                    $.post(U('User/Aat/avatarTpmDel'),{picPath:picPath},function(e){
                        window.location.reload();
                    });
                });
            }
        };
        AatUserPic.init();
    });
</script>
</body>
</html>