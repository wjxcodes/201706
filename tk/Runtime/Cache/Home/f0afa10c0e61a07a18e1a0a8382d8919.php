<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title><?php echo ($pageName); ?> - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/index.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/Public/plugin/layer/layer.js"></script>
    <script>
        var local='<?php echo U('Index/main');?>';
    </script>
    <!--[if lte IE 6]>
    <script type="text/javascript" src="/Public/plugin/png.js"></script>
    <script>DD_belatedPNG.fix('a,img,div,span');</script>
    <![endif]-->
    <script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
</head>
<body class="main_body">
<div class="header">
    <div class="nav">
        <a href="<?php echo U('Index/main');?>" class="index">首页</a>
        <a href="<?php echo U('Index/loginOut');?>" class="logout">退出</a>
    </div>
    <div class="logo"><?php echo ($config["SiteName"]); ?></div>
    <div id="cursub"><span><a id="cursubject" class="xktit" href="javascript:void(0);">选择学科</a></span></div>
    <div id="cursubjectmore" class="none">
        <span><label><input name="subjectmore" id="subjectmore" type="checkbox" value="1"/><span>文综</span></label></span>
    </div>
    <div class="user">欢迎&nbsp;<span id="userinfo"><span id="user_name"></span>&nbsp;<span id="user_icon" class="none">&nbsp;</span></span>&nbsp;，<span id="user_msg"></span></div>
</div>
<div class="main">
    <div class="mleft">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:auto;height:100%;">
            <tbody>
            <tr>
                <td width="99%" valign="top">
                    <div id="leftcontent">
                        <div id="basket">
                            <div id="basket_title"><div class="tit"></div><strong style="font-size:14px;">我的试题</strong>(共<span id="quescount">0</span>题)<a id="emptybasket" href="javascript:void(0);">清空</a></div>
                            <div id="quescountdetail"></div>
                            <div class="basket_Fold">
                            <a href="#" class="open">展开</a>
                            <a href="#" class="putaway" style="display:none">收起</a>
                            </div>
                            <div><a class="basketmenu" href="javascript:$.myCommon.go(U('Index/zuJuan'));">试卷预览</a></div>
                        </div>

                        <div>
                            <div class="menu ">
                                <div class="menutitle"><a class="ico01"></a><span>手工出题</span></div>
                                <div class="menulist">
                                    <a class="list_this" href="javascript:$.myCommon.go(U('Manual/Index/zsd'));">知识点出题</a>
                                    <a href="javascript:$.myCommon.go(U('Manual/Index/zj'));">教材章节出题</a>
                                    <!--<a href="javascript:$.myCommon.go(U('Index/zt')');">按专题浏览选题</a>-->
                                    <a href="javascript:$.myCommon.go(U('Manual/Index/sj'));">试卷出题</a>
                                    <a class="list_end" href="javascript:$.myCommon.go(U('Manual/Index/gjz'));">关键词找题</a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="menu">
                                <div class="menutitle"><a class="ico02"></a><span>智能组卷</span></div>
                                <div class="menulist">
                                    <a class="list_this" href="javascript:$.myCommon.go(U('Ga/Index/index'));">智能参数组卷</a>
                                    <a class="list_end" href="javascript:$.myCommon.go(U('Dir/Index/index'));">动态模板组卷</a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="menu">
                                <div class="menutitle"><a class="ico07"></a><span>高效同步课堂</span></div>
                                <div class="menulist">
                                    <a href="javascript:$.myCommon.go(U('Guide/Case/index'));">生成导学案</a>
                                     <a href="javascript:$.myCommon.go(U('Work/Work/addWork?workStyle=case'));">分发导学案</a>
                                     <a href="javascript:$.myCommon.go(U('Work/Work/checkWork?workStyle=case'));">批改导学案</a>
                                     <a class="list_end"  href="javascript:$.myCommon.go(U('Guide/Case/myLoreManager'));">知识管理</a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="menu">
                                <div class="menutitle"><a class="ico04"></a><span>作业模块</span></div>
                                <div class="menulist">
                                    <a  href="javascript:$.myCommon.go(U('Work/MyClass/myClass'));">我的班级</a>
                                    <a  href="javascript:$.myCommon.go(U('Work/Work/addWork'));">布置作业</a>
                                    <a class="list_end" href="javascript:$.myCommon.go(U('Work/Work/checkWork'));">批改作业</a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="menu">
                                <div class="menutitle"><a class="ico05"></a><span>学情评价</span></div>
                                <div class="menulist">
                                    <a class="list_this" href="javascript:$.myCommon.go(U('Work/WorkReport/classIndex'));">作业动态学情分析</a>
                                    <a class="list_end" href="javascript:$.myCommon.go(U('Work/WorkReport/classExerciseIndex'));">练习动态学情分析</a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="menu">
                                <div class="menutitle"><a class="ico06"></a><span>校本题库</span></div>
                                <div class="menulist">
                                    <a href="javascript:$.myCommon.go(U('Custom/CustomTestStore/index'));">校本试题</a>
                                    <a href="javascript:$.myCommon.go(U('Custom/CustomTestStore/customNav'));">添加试题</a>
                                    <a href="javascript:$.myCommon.go(U('Custom/MicroClass/index'));">校本微课</a>
                                    <a class="list_end" href="javascript:$.myCommon.go(U('Custom/MicroClass/add'));">添加微课</a>
                                </div>
                            </div>
                        </div>
                         <div>
                            <div class="menu">
                                <div class="menutitle"><a class="ico03"></a><span>用户档案</span></div>
                                <div class="menulist">
                                    <a href="javascript:$.myCommon.go(U('User/Home/info'));">用户信息</a>
                                    <a href="javascript:$.myCommon.go(U('User/Home/myTask'));">任务列表</a>
                                    <a href="javascript:$.myCommon.go(U('User/Home/testSave'));">收藏夹</a>
                                    <a href="javascript:$.myCommon.go(U('User/Home/message'));">试题反馈</a>
                                    <a href="javascript:$.myCommon.go(U('User/Home/docSave'));">历史存档</a>
                                    <a class="list_end" href="javascript:$.myCommon.go(U('User/Home/down'));">历史下载</a>
                                    <a class="list_end" href="javascript:$.myCommon.go(U('User/Home/checkOrder'));">查看订单</a>
                                </div>

                            </div>
                        </div>
                        <div style="border-bottom:1px solid #4d6175;"></div>
                    </div>
                </td><td id="switch"><a href="javascript:void(0);" id="switchhandle" title="隐藏左边栏"></a></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="mright">
        <iframe id="iframe" name="iframe" src="<?php echo U('Index/content');?>" scrolling="no" frameborder="0" height="100%" width="100%"></iframe>
    </div>
</div>
<script type="text/javascript" src="/Public/default/js/paper.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/default/js/getSelectData.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script>
var subject=<?php echo ($subject); ?>;
var diff=<?php echo ($diff); ?>;
Types=<?php echo ($types); ?>;
var user=<?php echo ($user); ?>;
var subjectID = Cookie.Get("SubjectId");
var ifSetInfo='<?php echo ($ifSetInfo); ?>';
if(ifSetInfo==1){
    var firstArea=<?php echo ($arrArea); ?>;
    var gradeList=<?php echo ($gradeList); ?>;
}
//loadMode类型
var loadMode = '<?php echo ($loadMode); ?>';
jQuery.myMain = {
    //初始化
    init:function(){
        var useAction='<?php echo ($useAction); ?>';
        if(useAction!=''){
            this.useActionSkip(useAction);
        }else {
            $('div.menutitle').eq(0).click(); //默认展开第一个菜单
        }
        //载入完善信息
        if(ifSetInfo =='1'){
            $.myMain.loadInfo();
        }else if(ifSetInfo == '2'){
            $.myMain.loadNickname();
        }
        
        //初始化cookie
        editData.setCookieInit();
        
        this.loadUserInfo(); //载入用户信息提醒
        this.checkSubject(); //检测是否有学科
        this.loadSubjectChoose(); //载入学科选择
        this.chooseSubjectClick();//选择学科点击事件
        this.hiddenLeft();//隐藏左边栏目
        this.clearTypes();//清空题型
        this.clearAllTypes();//清空所有题型
        this.menuClick();//左侧菜单点击事件
        this.checkPaperValue(); //试题篮折叠
        this.initDivBoxHeight(); //重置框架
        this.getSystemNickname();//获取用户昵称
        $(window).bind("resize",function() {$.myMain.initDivBoxHeight();});
        //根据docid加载组卷中心试题
        if(loadMode == 'zujuan'){
            $.myCommon.go(U('Index/zuJuan'));
        }
    },
    //根据id返回学科
    getQuesBank:function(a){
        var b;
        for (var i in subject) {
            if (typeof b == "object") {
                break
            }
            if(typeof(subject[i].sub)!='undefined'){
                for (var j  in subject[i].sub) {
                    if (subject[i].sub[j].SubjectID == a) {
                        b = subject[i].sub[j];
                        break;
                    }
                }
            }
        }
        if (typeof b != "object") {
            return;
        }
        return b;
    },
    basketInit:function(){
        var sub=this.getQuesBank(subjectID);
        var output="<table cellpadding='0' cellspacing='2' border='0' align='center'><tbody>";
        var g='';
        var f=55;
        var k=0;
        var t='0.0';
        var h=0;
        var tmpData=editData.gettestlist();
        var len = tmpData.length;
        for (var i = 0; i < len-1; i++) {
            g=tmpData[i][0];
            h=tmpData[i][2];
            k=Math.floor(h/tmpData[len-1]*f);
            if(tmpData[len-1]>0) t=(h/tmpData[len-1]*100).toFixed(1);
            output+="<tr title='占"+t+"%'>" + "<td align='right' title='" + g + "'"+(g.length>6 ? " width='105' " : '')+">" +  g + "：</td>" + "<td align='left'><span class='bilibox' style='width:" + f + "px;'>" + "<span class='bilibg' style='width:" + k + "px;'></span>" + "</span></td>" + "<td align='right'>" + h + "题</td>" + "<td><a class='emptyquestype' href='javascript:void(0);' title='清空 " + g + "'></a></td>" + "</tr>";
        }
        //改变总题数
        $('#quescount').html(tmpData[len-1]);
        output+="</tbody></table>";
        $('#quescountdetail').empty().html(output);
        if(sub) {
            $("#cursubject").html(sub.SubjectName);
            $("#cursubject").attr('sid',sub.SubjectID);
            $("#cursubject").attr('layout',sub.Layout);
            $("#cursubject").attr('thisstyle',sub.Style);
        }
    },
    //根据学科id显示综合
    setSubjectMoreByID:function(id){
        //获取学科id对应学科类型style
        var currentsubject=$.myMain.getQuesBank(id);
        if(currentsubject){
            var checked=editData.getAttr(4);
            $.myMain.setSubjectMore(currentsubject['Style'],checked);
        }
    },
    //获取是否删除数据 true不删除 false删除
    getSubjectMoreByID:function(id){
        //获取学科id对应学科类型style
        if($('#subjectmore').attr('checked')!='checked') return false;
        var currentsubject=$.myMain.getQuesBank(id);
        if(currentsubject){
            var checked=editData.getAttr(4);
            if(currentsubject['Style']==checked){
                return true;
            }
        }
        return false;
    },
    //综合学科设置与显示
    setSubjectMore:function(style,checked){
        if(typeof(style)=='undefined' || style=='') style=3;
        if(typeof(checked)=='undefined' || checked=='' || checked==0) checked=false;
        else checked='checked';
        switch(parseInt(style)){
            case 1:
                $('#cursubjectmore').removeClass('none');
                $('#subjectmore').val(1);
                $('#subjectmore').next().html('文综');
                $('#subjectmore').attr('checked',checked);
                break;
            case 2:
                $('#cursubjectmore').removeClass('none');
                $('#subjectmore').val(2);
                $('#subjectmore').next().html('理综');
                $('#subjectmore').attr('checked',checked);
                break;
            case 3:
                $('#cursubjectmore').addClass('none');
                break;
        }
    },
    //选择科目
    setCurrentQuesBank:function(a,reloads){
        if(reloads=='' || typeof(reloads)=='undefined') reloads=0;
//      if(!Types[a]){
//          //选择学科
//          $.myCommon.chooseSubject();
//          return false;
//      }
        $('#quesbanklist').remove();
        //判断是否是综合
        this.setSubjectMoreByID(a);
        var changethis=this.getSubjectMoreByID(a);//是否切换学科并删除数据
        //是否有缓存题型名称和对应id
        var tmpData=editData.gettestlist();
        Cookie.Set("SubjectId", a, 7);
        if(tmpData && (subjectID==a || changethis)){
            this.basketInit();
            if(subjectID!=a){
                $.myMain.reSubjectShow(a);
                $.myMain.reflashIframe();
            }
            editData.editAttr(a,3);
        }else{
            this.clearAllPaper(a,reloads);
        }
        subjectID=a;
        return true;
    },
    //清空试卷
    clearAllPaper:function(a,reloads){
        var output="<table cellpadding='0' cellspacing='2' border='0' align='center'><tbody>";
            /*
            var e=Types[a];
            var g='';
            var f=55;
            var k=0;
            var t='0.0';
            var h=0;
            var len = e.length;
            var j=1;
            var x=1;
            for (var i = 0; i < len; i++) {
                g=e[i]['TypesName'];
                output+="<tr>" + "<td align='right' title='" + g + "'"+(g.length>6 ? " width='105' " : '')+">" + g + "：</td>" + "<td align='left'><span class='bilibox' style='width:" + f + "px;'>" + "<span class='bilibg' style='width:" + k + "px;'></span>" + "</span></td>" + "<td align='right'>" + h + "题</td>" + "<td><a class='emptyquestype' href='javascript:void(0);' title='清空 " + g + "'></a></td>" + "</tr>";

                if(e[i]['Volume']==1){
                    juan1.push('questypehead1_'+j+'@$@1@$@'+g+'@$@（题型注释）@$@1@$@0@$@'+e[i]['DScore']+'|'+e[i]['TypesScore']+'|'+e[i]['IfDo']);
                    j++;
                }else if(e[i]['Volume']==2){
                    juan2.push('questypehead2_'+x+'@$@1@$@'+g+'@$@（题型注释）@$@1@$@0@$@'+e[i]['DScore']+'|'+e[i]['TypesScore']+'|'+e[i]['IfDo']);
                    x++;
                }
            }
            */

            //存入试题和试卷初始cookie
            $('#quescount').html(0);
            editData.clear();
            editData.setCookieInit();
            output+="</tbody></table>";
            $('#quescountdetail').empty().html(output);
            $('#subjectmore').attr('checked',false);
            $.myMain.reSubjectShow(a);
            editData.editAttr(a,3);
            if(reloads==0){
                $.myMain.reflashIframe();
            }
    },
    //mian刷新学科显示
    reSubjectShow:function(a){
        var sub=this.getQuesBank(a);
        $("#cursubject").html(sub.SubjectName);
        $("#cursubject").attr('sid',sub.SubjectID);
        $("#cursubject").attr('layout',sub.Layout);
        $("#cursubject").attr('thisstyle',sub.Style);
    },
    //mian刷新小框架
    reflashIframe:function(){
        if($("#iframe").attr('src').indexOf('Manual/Index/sj/did')!=-1 && $("#iframe").attr('src').indexOf('SubjectID-'+a)==-1)
                    $("#iframe").attr('src',U('Manual/Index/sj'));
        else $("#iframe").get(0).contentWindow.location.reload();
    },
    //载入完善信息
    loadInfo:function(){
        var idName='infodiv';
        var ifEdit='';//是否允许更改
        var ifGoActivity='<?php echo ($ifGoActivity); ?>';
        if(user.CheckPhone!=0){
            ifEdit='disabled="true"　readOnly="true"';
        }
        var areaOption='<option value="">—请选择—</option>>';
        if(firstArea!=''){
            for(var i in firstArea){
                areaOption+='<option value="'+firstArea[i]['AreaID']+'" last="'+firstArea[i]['Last']+'">'+firstArea[i]['AreaName']+'</option>';
            }
        }
        var gradeOption='<option value="">—请选择—</option>>';
        if(gradeList!=''){
            for(var j in gradeList){
                gradeOption+='<optgroup label="'+gradeList[j]['GradeName']+'">';
                for(var k in gradeList[j]['sub']){
                    gradeOption+='<option value="'+gradeList[j]['sub'][k]['GradeID']+'">　'+gradeList[j]['sub'][k]['GradeName']+'</option>';
                }
                gradeOption+='</optgroup>';
            }
        }
        var tmpStr= $.myCommon.loading();
        $.myDialog.normalMsgBox(idName,'完善个人信息',500,tmpStr,3);
        tmpStr = '<div class="showinput">' +
                 '    <span class="item">姓名：</span>' +
                 '    <input name="RealName" class="RealName" value="'+user.RealName+'" size="28"/>' +
                 '    <span id="RealNameerr"></span>' +
                 '</div>'+
                 '<div class="showinput">' +
                 '    <span class="item">昵称：</span>' +
                 '    <input type="text" name="Nickname" id="Nickname" value="'+user.Nickname+'" size="15" maxlength="15"/>' +
                 '    <input class="pointer nickName" type="button" value="系统昵称" style="margin-left:19px;">'+
                 '    <span id="Nicknameerr"></span>' +
                 '</div>'+
                 '<div class="showinput">' +
                 '    <span class="item">地区：</span>' +
                 '    <select class="AreaID selectArea">'+areaOption+'</select>' +
                 '    <span id="AreaIDerr"></span>' +
                 '</div>'+
                 '<div class="showinput">' +
                 '    <span class="item">学校：</span>' +
                 '    <select class="SchoolID" id="school"><option value="">加载中..</option></select>' +
                 '    <span id="SchoolIDerr"></span>' +
                 '</div>'+
                 '<div class="showinput">' +
                 '    <span class="item">年级：</span>' +
                 '    <select class="GradeID">'+gradeOption+'</select>' +
                 '    <span id="GradeIDerr"></span>' +
                 '</div>'+
                 '<div class="showinput">' +
                 '    <span class="item">学科：</span>' +
                 '    <select class="SubjectID"><option value="">加载中..</option></select>' +
                 '    <span id="SubjectIDerr"></span>' +
                 '</div>'+
                 '<div class="showinput">' +
                 '    <span class="item">地址：</span>' +
                 '    <input class="text" name="Address" id="Address" type="text" size="28" value="'+user.Address+'"/>' +
                 '    <span id="Addresserr"></span>' +
                '</div>';
        if(user.CheckPhone==0){
        tmpStr +='<div class="showinput">' +
                '<span class="item">手机：</span>' +
                '<input name="Phonecode" id="Phonecode" type="text" size="28" value="'+user.Phonecode+'" '+ifEdit+'/>' +
                '<span id="Phonecodeerr"></span>' +
                '</div>'+
                '<div class="showinput">' +
                '<span class="item">图片验证码：</span>' +
                '<input name="verifyCode" id="verifyCode" type="text" size="10" value="" />' +
                '<img height="22" width="95" id="verifyImg" src="'+U('Index/verify')+'" border="0" title="点击刷新验证码" style="cursor:pointer;margin:0 0 0 30px;" align="absmiddle" />' +
                '<span id="verifyImgerr"></span>' +
                '</div>';//+
//              '<div class="showinput">' +
//              '<span class="item">短信验证码：</span>' +
//              '<input name="messageCode" id="messageCode" type="text" size="10" value="" />' +
//              '<input class="pointer getPhoneRand" type="button" value="发送短信验证码" id="sendPhoneCode" style="margin-left:19px;">' +
//              '<span id="messageCodeerr"></span>' +
//              '</div>';
        }
        tmpStr +='<div class="showinput">' +
                '<span class="item">邮箱：</span>' +
                '<input name="Email" id="Email" type="text" size="28" value="'+user.Email+'"/>' +
                '<span id="Emailerr"></span>' +
                '</div>';
        $('#'+idName+' .normal_msg').html(tmpStr);
        $.myDialog.normalAddCon(idName);
        $.myMain.loadArea();
        $('#infodiv .SchoolID').html('<option value="">请选择地区</option>');
        $.myMain.loadSubject(0);
        //发送手机验证码
        $("#sendPhoneCode").die().live('click',function(){
            //验证图形验证码
            var imgCode=$('#verifyCode').val();
            var phoneNum=$('#Phonecode').val();
            if(phoneNum==''){
                $.myMain.showError('Phonecode','请填写手机号!');
                return false;
            }
            if($.myCommon.checkPhoneNum(phoneNum)==false){
                $.myMain.showError('Phonecode','请输入正确手机号!');
                return false;
            }
            if(imgCode.length!=4){
                $.myMain.showError('verifyImg','请填写图片验证码!');
                $('#verifyCode').focus();
                return false;
            }
            //发送手机短信验证码
            $.myCommon.sendPhoneCode(phoneNum,imgCode);
        });

        //切换年级学科联动
        $('.GradeID').live('change',function(){
            var tmp_id=$(this).val();
            $('.SubjectID').html('<option value="">请选择年级</option>');
            if(tmp_id=='' || typeof(tmp_id)=='undefined') return;
            $.myMain.loadSubject(1);
        });
        //确定完善信息
        $('#infodiv .normal_yes').live('click',function(){
            var err=0;
            $('.showinput').each(function(){
                $(this).find('span').last().html('');
            });
            //判断姓名
            var RealName=$('.RealName').val();
            if(RealName.length<1){
                $.myMain.showError('RealName','请填写正确的姓名!');
                err=1;
            }
            var Nickname=$('#Nickname').val();
            var preg=/^[\u4e00-\u9fa5a-zA-Z0-9]+$/;
            if(!preg.test(Nickname)){
                $.myMain.showError('Nickname','昵称只允许汉字，字母和数字组合！');
                return false;
            }
            var NicknameLength=Nickname.replace(/[^\x00-\xff]/g, 'xxx').length;
            if(NicknameLength<3 || NicknameLength>15){
                $.myMain.showError('Nickname','昵称为3-15个字符!');
                err=1;
            }
            //判断手机号
            if(user.CheckPhone==0){
                var Phonecode=$('#Phonecode').val();
                if(Phonecode.length!=11 || Phonecode.replace(/[0-9\-]/ig,'')){
                    $.myMain.showError('Phonecode','请输入正确手机号!');
                    err=1;
                }
                //判断短信验证码
//              var messageCode=$('#messageCode').val();
//              if(messageCode.length!=6){
//                  $.myMain.showError('messageCode','请输入短信验证码!');
//                  err=1;
//              }
            }
            //判断邮箱
            var Email=$('#Email').val();
            if(Email.indexOf('@')==-1 || Email.indexOf('.')==-1){
                $.myMain.showError('Email','请输入正确的邮箱!');
                err=1;
            }
            //判断地址
            var Address=$('#Address').val();
            if(Address.length<5){
                $.myMain.showError('Address','请输入正确的地址!');
                err=1;
            }
            //判断地区
            var AreaID=$('.selectArea').last().find("option:selected").val();
            if(AreaID==0 || AreaID=='' || typeof(AreaID)=='undefined'){
                $.myMain.showError('AreaID','请选择所在地区!');
                err=1;
            }
            //判断学校
            var SchoolID=$('.SchoolID').val();
            if(SchoolID=='' || typeof(SchoolID)=='undefined'){
                $.myMain.showError('SchoolID','请选择所在学校!');
                err=1;
            }
            //判断年级
            var GradeID=$('.GradeID').val();
            if(GradeID=='' || typeof(GradeID)=='undefined'){
                $.myMain.showError('GradeID','请选择所在年级!');
                err=1;
            }
            //判断学科
            var SubjectID=$('.SubjectID').val();
            if(SubjectID=='' || typeof(SubjectID)=='undefined'){
                $.myMain.showError('SubjectID','请选择所在年级!');
                err=1;
            }
            if(err==1) return false;
            var myData='';
            //if(user.CheckPhone==0) myData={'RealName':RealName,'Nickname':Nickname,'Phonecode':Phonecode,'messageCode':messageCode,'Email':Email,'Address':Address,'AreaID':AreaID,'SchoolID':SchoolID,'GradeID':GradeID,'SubjectID':SubjectID,'times':Math.random()};
            if(user.CheckPhone==0) myData={'RealName':RealName,'Nickname':Nickname,'Phonecode':Phonecode,'Email':Email,'Address':Address,'AreaID':AreaID,'SchoolID':SchoolID,'GradeID':GradeID,'SubjectID':SubjectID,'times':Math.random()};
            else myData={'RealName':RealName,'Nickname':Nickname,'Email':Email,'Address':Address,'AreaID':AreaID,'SchoolID':SchoolID,'GradeID':GradeID,'SubjectID':SubjectID,'times':Math.random()};
            
            var sendLoad = layer.load();//等待提示
            $.post(U('User/Home/changeInfo'),myData,function(data){
                layer.close(sendLoad);//关闭等待提示
                if($.myCommon.backLogin(data)==false){
                    if(data.data=='请重新登录'){
                        $('#msgdiv .tcClose').css({'display':'none'});
                        $('#msgdiv .normal_no').bind('click',function(){
                            window.parent.location.href=U('Index/index');
                        });
                    }
                    return false;
                }
                if(data.data=='success'){
                    $('#infodiv .tcClose').click();
                    $.myDialog.normalMsgBox('msgdiv','完善个人信息',450,'修改成功!',1);
//                    if(ifGoActivity != '') {
//                        window.parent.location.href = "<?php echo U('Index/Special/teachersDay2015');?>";
//                    }
                }else{
                    var tmp_arr=new Array();
                    tmp_arr=data['data'].split('#@#');
                    var tmp_arr2=new Array();
                    tmp_arr2=tmp_arr[1].split('#$#');
                    var tmp_arr3=new Array();
                    for(var ii=0;ii<tmp_arr2.length;ii++){
                        tmp_arr3=tmp_arr2[ii].split('|');
                        $.myMain.showError(tmp_arr3[0],tmp_arr3[1]);
                    }
                }
            });
        });
    },
    //载入完善用户昵称
    loadNickname:function(){
        var idName="nicknameInfo";
        var tmpStr= $.myCommon.loading();
        $.myDialog.normalMsgBox(idName,'完善个人信息',400,tmpStr,3);
        tmpStr ='<div class="showinput">' +
                '    <span class="item">昵称：</span>' +
                '    <input type="text" name="Nickname" id="Nickname" value="" size="25" maxlength="15"/>' +
                '    <input class="pointer nickName" type="button" value="系统昵称" style="margin-left:19px;">'+
                '</div>'+
                '<div id="Nicknameerr"></div>';
        $('#'+idName+' .normal_msg').html(tmpStr);
        $.myDialog.normalAddCon(idName);
        //确认完善昵称
        $('#nicknameInfo .normal_yes').live('click',function(){
            $('#Nicknameerr').html('');//清除错误提示
            var Nickname=$('#Nickname').val();//获取用户输入的昵称
            var preg=/^[\u4e00-\u9fa5a-zA-Z0-9]+$/;
            if(!preg.test(Nickname)){
                $.myMain.showError('Nickname','昵称只允许汉字，字母和数字组合！');
                return false;
            }
            var NicknameLength=Nickname.replace(/[^\x00-\xff]/g, 'xxx').length;
            if(NicknameLength<3 || NicknameLength>15){//判断昵称长度是否合法
                $.myMain.showError('Nickname','昵称为3-15个字符!');
                return false;
            }
            //提交
            $.post(U('User/Home/updateNickname'),{'Nickname':Nickname,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    //$('#nicknameInfo .tcClose').click();
                    if(data.data=='请重新登录'){
                        $('#msgdiv .tcClose').css({'display':'none'});
                        $('#msgdiv .normal_no').bind('click',function(){
                            window.parent.location.href=U('Index/index');
                        });
                    }
                    return false;
                }
                if(data.data=='success'){
                    $('#nicknameInfo .tcClose').click();
                    $.myDialog.normalMsgBox('msgdiv','完善个人信息',450,'修改成功!',1);
                }
            });
        });
    },
    //检测是否有学科
    checkSubject:function(){
        if($.myCommon.checkSubject()==false){
            //默认学科
            var nowSubjectID=user['SubjectStyle'];
            if(nowSubjectID>20 || nowSubjectID==0) $.myCommon.chooseSubject(); //选择学科
            else{
                $.myMain.setCurrentQuesBank(nowSubjectID,1);
            }
        }else{
            $.myMain.setCurrentQuesBank(subjectID,1);
        }
    },
    //载入用户信息提醒
    loadUserInfo:function(){
        var myDate = new Date();
        var hour = myDate.getHours();
        if(hour>12) $('#user_msg').html('下午好！');
        if(hour==12) $('#user_msg').html('中午好！');
        if(hour<12) $('#user_msg').html('上午好！');
        $('#user_name').html(user.UserName);
        $('#userinfo').mouseenter(function(){
            $('#user_icon').css({'background':'url(/Public/default/image/piclist1.gif) 0px -40px no-repeat','padding':'0px 4px','line-height':'15px'});

            if($('#user').length==0){
                $('#userinfo').append('<div id="user" class="pd5"><p>用户名：'+user['UserName']+'</p><p>真实姓名：'+user['RealName']+'</p><p><a href="javascript:$.myCommon.go(U(\'User/Home/info\'));">更多用户信息>></a></p></div>');
            }
            $('#user').css({'display':'block'});
        });
        $('#userinfo').mouseleave(function(){
            $('#user_icon').css({'background':'url(/Public/default/image/piclist1.gif) 0px -40px no-repeat','padding':'0px 4px','line-height':'15px'});
            $('#user').css({'display':'none'});
        });
    },
    //载入学科选择
    loadSubjectChoose:function(){
        $('#cursubject').mouseenter(function(){
            if($('#quesbanklist').length==0){
                $.myMain.loadAllSubject(); //载入所有学科 供选择
            }else{
                $("#quesbanklist").css('display','block');
            }
        });
        $('#cursubject').mouseleave(function() {
            $("#quesbanklist").css('display','none');
        });
    },
    //载入所有学科 供选择
    loadAllSubject:function(){
        var output='<div id="quesbanklist"><div id="quesbanklist_inner"><div class="edu">';
        for(var i in subject){
            output+='<div class="eduname">'+subject[i]['FullName']+'</div><div class="banks">';
            for(var j in subject[i]['sub']){
                if(parseInt(subjectID)!=parseInt(subject[i]['sub'][j]['SubjectID'])){
                    output+='<a class="bank" thisstyle="'+subject[i]['sub'][j]['Style']+'" layout="'+subject[i]['sub'][j]['Layout']+'" sid="'+subject[i]['sub'][j]['SubjectID']+'" title="点击换到 '+subject[i]['sub'][j]['SubjectName']+'">'+subject[i]['sub'][j]['SubjectName']+'</a>';
                }else{
                    output+='<a class="bank_current" thisstyle="'+subject[i]['sub'][j]['Style']+'" layout="'+subject[i]['sub'][j]['Layout']+'" title="点击换到 '+subject[i]['sub'][j]['SubjectName']+'">'+subject[i]['sub'][j]['SubjectName']+'</a>';
                }
            }
            output+='</div>';
        }
        output+='</div></div></div>';
        $('#cursubject').append(output);
    },
    //选择学科点击事件
    chooseSubjectClick:function(){
        $('.bank').live('click',function(){
            $.myMain.setCurrentQuesBank($(this).attr('sid'));
            if($('#chooseSubject').length>0){
                $('#chooseSubject .tcClose').click();
            }
        });
        
        //综合试卷切换
        $('#subjectmore').live('change',function(){
            if($(this).attr('checked')=='checked'){
                //获取学科对应综合属性
                var thisstyle=$('#cursubject').attr('thisstyle');
                if(thisstyle==3) thisstyle=0;
                editData.editAttr(thisstyle,4);
            }else{
                //累加学科和单一学科需要区别
                var subjectlist=editData.getAttr(3);
                var testlist=editData.gettestid();
                
                if(testlist==''){
                    editData.editAttr(0,4);
                    return;
                }
                
                $.myDialog.normalMsgBox('msgdiv','提示信息',450,'切换学科属性，请稍候...',5);
                $.post(U('Index/getSubejctByTestID'),{'testlist':testlist,'times':Math.random()},function(data){
                    $('#msgdiv .tcClose').click();
                    if($.myCommon.backLogin(data)==false){
                        $.myDialog.normalMsgBox('msgdiv','提示信息',450,'切换失败，',1);
                        return false;
                    }
                    if(typeof(data['data'])!='undefined' &&  data['data'].indexOf(',')!=-1){
                        if(confirm('取消该属性需要清空试题，是否确认？')){
                            $.myMain.clearAllPaper($('#cursubject').attr('sid'));
                        }else{
                            $('#subjectmore').attr('checked','checked');
                        }
                    }else{
                        editData.editAttr(0,4);
                        $.myMain.setCurrentQuesBank(parseInt(data['data']));
                    }
                });
            }
        });
    },
    //隐藏左边栏目
    hiddenLeft:function(){
        $('#switchhandle').click(function(){
            if($('#leftcontent').css('display')=='none'){
                $('.mleft').css({'width':'230px'});
                $('#leftcontent').css({'display':'block'});
                $('#switchhandle').addClass("toggle").attr("title", "隐藏左边栏");
                $(".mright").css({
                    left: 230,
                    width: $(window).width() - 230
                })
            }else{
                $('.mleft').css({'width':'6px'});
                $('#leftcontent').css({'display':'none'});
                $('#switchhandle').addClass("toggle").attr("title", "显示左边栏");
                $(".mright").css({
                    left: 6,
                    width: $(window).width() - 6
                })
            }
        });
    },
    //显示错误信息
    showError:function(id,str){
        $('#'+id+'err').html('<font color="#ff0000">'+str+'</font>');
    },
    //清空题型
    clearTypes:function(){
        $('.emptyquestype').live('click',function(){
            if($(this).parent().prev().html()!='0题'){
                //判断其他的是不是为空
                var tmp_m=$(this).attr('title').replace('清空 ','');
                if($(this).attr('clear')!='all'){
                    if(!confirm('确认清空'+tmp_m+'？')){
                        return false;
                    }
                }
                $.myMain.changeTestNum(0-parseInt($(this).parent().prev().html().replace('题')),tmp_m);
                $(this).parent().parent().attr('title','占0.0%');
                $(this).parent().prev().html('0题');
                $(this).parent().prev().prev().find('.bilibg').css('width','0px');

                var ifclear=1;
                $(this).parents('table').last().find('tr').each(function(){
                    if(ifclear==1){
                        if($(this).attr('title')!="占0.0%"){
                            ifclear=0;
                        }else{
                            return true;
                        }
                    }else{
                        return true;
                    }
                });
                if(ifclear){
                    $('#iframe').contents().find('.addtestall').removeClass('an01').addClass('an02');
                    $('#iframe').contents().find('.addtestall').each(function(){
                        $(this).find('a').eq(0).html('全部加入');
                    });
                }
                var testlist=editData.selecttypename(tmp_m);
                if(testlist){
                    //清空题型
                    $('#iframe').contents().find('#'+testlist[0]).next().empty();
                    $('#iframe').contents().find('#s_'+testlist[0]).next().empty();
                    var tmp_str,tmp_str_1;
                    if($('#iframe').contents().find('.questypedscore').length>0){
                        tmp_str=$('#iframe').contents().find('.questypedscore').html();
                        tmp_str_1=tmp_str.split(' ');
                        //$('#iframe').contents().find('#'+testlist[0]+' .questypedscore').html('： 共0题 '+tmp_str_1[1]+'  共0分');questypedscore
                        $('#iframe').contents().find('#'+testlist[0]+' .questypedscore').html('');
                        $('#iframe').contents().find('#'+testlist[0]+' .questypedscore').hide();
                    }
                    tmp_str=testlist[1].split(';');

                    for(var tmp_i=0;tmp_i<tmp_str.length;tmp_i++){
                        tmp_str_1=tmp_str[tmp_i].split('|');
                        $('#iframe').contents().find('#quesselect'+tmp_str_1[0]).attr('class','addquessel');
                        $('#iframe').contents().find('#quesbox'+tmp_str_1[0]).find('.selmore').css({'display':'inline-block'});//样式不能show
                        $('#iframe').contents().find('#quesbox'+tmp_str_1[0]).find('.selpicleft').css({'display':'inline-block'});//样式不能show
                    }

                    //更新右侧试题序号
                    var tmp_k=1;
                    $('#iframe').contents().find('#s_paperbody .s_quesindex').each(function(i){
                        if(parseInt($(this).attr('queschildnum'))!=1){
                            $(this).html(tmp_k+'-'+(tmp_k+parseInt($(this).attr('queschildnum'))-1)+'．');
                            tmp_k=tmp_k+parseInt($(this).attr('queschildnum'))-1;
                        }else $(this).html(tmp_k+'．');
                        tmp_k++;
                    });

                    //更新左侧试题序号
                    tmp_k=1;
                    $('#iframe').contents().find('#pui_body .quesindex').each(function(i){
                        $(this).find('b').html(tmp_k+'．');
                        tmp_k++;
                    });
                }
                editData.deltypetest(tmp_m);
            }
        });
    },
    //清空所有题型
    clearAllTypes:function(){
        $('#emptybasket').live('click',function(){
            if(confirm('确认清空所有试题？')){
                $.myMain.clearAllTestBasket();
                //删除存档信息
                editData.addAttr(0,0);
                var iconExam=$('#iframe').contents().find('#iconExam');
                if(iconExam.attr('class')=='iconExam') iconExam.removeClass('iconExam').addClass('iconExamNo');
            }
        });
    },
    //清空所有试题
    clearAllTestBasket:function(){
        $('#quescountdetail tr').each(function(){
            $(this).find('.emptyquestype').each(function(){
                $(this).attr('clear','all');
            });
        });
        $('#quescountdetail tr').each(function(){
            $(this).find('.emptyquestype').click();
        });
        if($('#iframe').contents().find('.addtestall').length>0){
            $('#iframe').contents().find('#paperviewbox').find('.papercontent').each(function(){//清空试题时，清除所有页面中的样式及文字
                $(this).find('.addtestall').removeClass('an01').addClass('an02');
                $(this).find('.addtestall').find('a').eq(0).html('全部加入');
            });
        }
        $('#quescountdetail tr').each(function(){
            $(this).find('.emptyquestype').each(function(){
                $(this).attr('clear','');
            });
        });
    },
    //更新试题数量$num 试题数  $style试题类型
    changeTestNum:function(num,style){
        //获取原有题数
        num=parseInt(num);
        var shitinum=0;
        var nowshitinum=0;
        var width=0;
        $('#quescountdetail tr').each(function(){
            var _this=$(this).find('td:first');
            shitinum+=parseInt(_this.next().next().html().replace('题'));
        });
        shitinum=shitinum+num;
        //设置新题数
        $('#quescountdetail tr').each(function(){
            var _this=$(this).find('td:first');
            nowshitinum=parseInt(_this.next().next().html().replace('题'));
            if(_this.attr('title')==style){
                _this.next().next().html((nowshitinum+num)+'题');
                if(shitinum==0){
                    baifen=0;
                    width=0;
                }else{
                    baifen=(nowshitinum+num)/shitinum*100;
                    width=(nowshitinum+num)/shitinum*55;
                }
            }else{
                if(shitinum==0){
                    baifen=0;
                    width=0;
                }else{
                    baifen=nowshitinum/shitinum*100;
                    width=nowshitinum/shitinum*55;
                }
            }
            _this.next().find('.bilibg').css('width',Math.round(width)+'px');
            _this.parent().attr('title','占'+baifen.toFixed(1)+'%');
            $('#quescount').html(shitinum);
        });
    },
    //左侧菜单点击事件
    menuClick:function(){
        $('div.menutitle').live('click',function(){
            if($(this).parent('.menu').hasClass('menu_this')==true){
                $(this).next().slideUp();
                $(this).parent('.menu').removeClass('menu_this');
                return;
            }
            $('div.menutitle').parent('.menu').removeClass('menu_this');
            $(this).parent('.menu').addClass('menu_this');
            $('div.menutitle').next().slideUp();
            $(this).next().slideDown(500);
        });
        $('.menulist a').live('click',function(){
            $('.menulist a').removeClass('list_this');
            $(this).addClass('list_this');
        });
    },
    //重置框架
    initDivBoxHeight:function() {
        var a = $(window).width();
        var b = $(window).height();
        var c = $(".header").outerHeight();
        var d = $(".mleft").outerWidth();
        $(".mright").width(a - d);
        $(".mleft,.mright,.mright #iframe").height(b - c);

        $(".mleft").css({'overflow-y':'auto','overflow-x':'hidden'});
        $(".main").height(b - c)
        $('#iframe').css({'height':b-c});
    },
    //载入地区
    loadArea:function(){
        $('.selectArea').areaSelectChange('Home/Index',1);
    },
    //载入学校
    loadSchool:function(tmp_id){
        $.post(U('User/ajaxSchool'),{'id':tmp_id,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                tmp_str+='<option value="">'+data['data']+'</option>';
                $('#infodiv .SchoolID').html(tmp_str);
                return false;
            };
            var tmp_str='';
            tmp_str+='<option value="">请选择</option>';
            var jibie='(高中)';
            for(var ii in data['data']){
                if(data['data'][ii]['Type']==1) jibie='(高中)';
                else if(data['data'][ii]['Type']==2) jibie='(初中)';
                else if(data['data'][ii]['Type']==3) jibie='(职高)';
                tmp_str+='<option value="'+data['data'][ii]['SchoolID']+'">'+data['data'][ii]['SchoolName']+jibie+'</option>';
            }
            $('#infodiv .SchoolID').html(tmp_str);
        });
    },
    //载入年级
    loadGrade:function(){
        $.get(U('MyClass/getGrade'),{'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                output+='<option value="">'+data['data']+'</option>';
                $('#infodiv .GradeID').html(output);
                return false;
            };
            var gid=user.GradeID;
            if(typeof(gid)=='undefined' || gid=='') gid=0;
            var output='';
            if(gid==0) output='<option value="0">请选择年级</option>';
            var j;
            var selected='';
            for(var i in data['data']){
                output+='<optgroup label="'+data['data'][i]['GradeName']+'"></optgroup>';
                if(data['data'][i]['sub']){
                    for(j in data['data'][i]['sub']){
                        selected='';
                        if(gid==data['data'][i]['sub'][j]['GradeID']) selected=' selected="selected" ';
                        output+='<option value="'+data['data'][i]['sub'][j]['GradeID']+'" '+selected+'>'+data['data'][i]['sub'][j]['GradeName']+'</option>';
                    }
                }
            }

            $('#infodiv .GradeID').html(output);
        });
    },
    //获取地区id
    getAreaID:function(){
        var tmp_id=$('.AreaID').last().val();
        if($('.AreaID').last().find("option:selected").attr('iflast')==0){
            tmp_id=0;
        }
        return tmp_id;
    },
    //载入学科
    loadSubject:function(type){
        $('.SubjectID').html('<option value="">请选择学科</option>');
        var gid=0;
        if(type==1){
            gid=$('.GradeID').val();
        }else{
            gid=user.GradeID;
        }

        if(typeof(gid)=='undefined' || gid=='' || gid==0){
            $('.SubjectID').html('<option value="">请选择年级</option>');
            return;
        }
        $.post(U('Index/getData'),{'style':'getGradeSubject','gradeID':gid},function(data){
            if($.myCommon.backLogin(data)==false){
                output+='<option value="">'+data['data']+'</option>';
                $('.SubjectID').html(output);
                return false;
            };
            var sid=user.SubjectStyle;
            if(typeof(sid)=='undefined' || sid=='') sid=0;
            var output='';
            var selected='';
            for(var i in data['data']){
                selected='';
                if(sid==data['data'][i]['SubjectID']) selected=' selected="selected" ';
                output+='<option value="'+data['data'][i]['SubjectID']+'" '+selected+'>'+data['data'][i]['SubjectName']+'</option>';
            }
            $('.SubjectID').html(output);
        });
    },
    //检测试题篮展开or关闭
    checkPaperValue:function(){
        $('.putaway').live('click',function(){
            $.myMain.paperPutAway();
        });
        $('.open').live('click',function(){
            $.myMain.paperOpen(2);
        });
        var paperValue='';
        if(Cookie.Has('paperValue')){
            paperValue=Cookie.Get('paperValue');
            paperValue=paperValue.split(',');
            paperValue=paperValue[0];
        }else{
            paperValue=2;//默认展开
        }

        if (paperValue==1) {
            $.myMain.paperPutAway();
        }else if(paperValue==2){
            $.myMain.paperOpen(1);
        }
    },
    //校本试题 折叠
    paperPutAway:function(){
        $('#quescountdetail').slideUp();
        $('.putaway').hide();
        $('.open').show();
        Cookie.Set("paperValue", 1, 7);
    },
    //校本试题 展开
    paperOpen:function(type){
        if (type==1) {
            $('#quescountdetail').show();
        }else if(type==2){
            $('#quescountdetail').slideDown();
        }
        $('.open').hide();
        $('.putaway').show();
        Cookie.Set("paperValue", 2, 7);
    },
    //使用指定功能跳转
    useActionSkip:function(useAction){
        if(useAction.indexOf('/') == 0){
            useAction = useAction.substring(1);
        }
        $('#leftcontent').find('div.menulist').each(function(i){
            var menuObj=$(this);//当前菜单对象
            menuObj.find('a').each(function(j){
                //当前功能函数名称的链接地址
                var hrefStr=$(this).attr('href');
                //判断用户请求的功能是否是当前功能
                if(hrefStr.indexOf(useAction)!=-1){
                    menuObj.css('display','block');//当前功能所在目录列表展开
                    //当前目录的父级添加class类名，用于判断当前目录是否展开
                    menuObj.parent('.menu').addClass('menu_this');
                    //去除功能列表选中样式
                    $('.menulist a').removeClass('list_this');
                    //对当前功能增加选中样式
                    $(this).addClass('list_this');
                    //子框架展示当前功能
                    $.myCommon.go(U(useAction));
                }else{
                    $('div.menutitle').eq(0).click(); //默认展开第一个菜单
                    $('#iframe').attr('src',U(useAction));
                }
            })
        })
    },
    //获取系统昵称
    getSystemNickname:function(){
        $(document).on('click','.nickName',function(){
            $.post(U('Index/getNickname'),function(e){
                if($.myCommon.backLogin(e)==false){
                    return false;
                };
                $('#Nickname').val(e.data);
            })
        })
    }
}

$(document).ready(function(){
    //设置框架高度
    $('.mleft').css({'height':$(window).height()-$('.header').height()});
    $('.mright').css({'height':$(window).height()-$('.header').height()});
    $('.mright').css({'width':$(window).width()-$('.mleft').width()});
    $('#iframe').css({'height':$(window).height()-$('.header').height()});

    $.myMain.init();
});
</script>
</body>
</html>