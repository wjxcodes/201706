<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo ($pageName); ?> - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>"/>
    <meta name="description" content="<?php echo ($config["Description"]); ?>"/>
    <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/work.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/Public/plugin/jquery.SuperSlide.2.1.1.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/artTemplate-3.0.3.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/paper.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <!--[if lte IE 6]>
    <script type="text/javascript" src="/Public/plugin/png.js"></script>
    <script>DD_belatedPNG.fix('a,img,div,span');</script>
    <![endif]-->
    <script type="text/javascript">
        var local = "<?php echo U('WorkReport/classIndex');?>";
        //artTemplate配置开始结束标签避免和Thinkphp模版标签冲突
        template.config('openTag','{%');
        template.config('closeTag','%}');
    </script>
    <script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <style type="text/css">
        .dialog .bar{
            background-color:#32393d;
        }
    </style>
</head>
<body>
<div id="workDiv" class="exerciseReportClass">
    <div id="workBox" class="PublicBox">
        <div class="classList wLeft">
            <div class="classTit">班级列表</div>
            <div class="loadClass">
                <div class="prev" onselectstart="return false;" oncontextmenu="return false" title="向上滚动">向上移动</div>
                <div class="bd"></div>
                <div class="next" onselectstart="return false;" oncontextmenu="return false" title="向下滚动">向下移动</div>
            </div>
        </div>
        <div id="rightdiv" class="teacher">
            <div id="righttop">
                <div id="categorylocation">
                    <span class="newPath">当前位置：</span>> 学情评价 > 练习动态学情分析
                </div>
            </div>
            <div class="classStudent classMargin">
                <div class="studentTit">班级学生
                    <div class="message">
                       提示：灰色姓名是未激活的学生账号
                    </div>
                </div>
                <div class="studentInfo"></div>
            </div>
            <div class="classExercise">
                <div class="classExerciseTit">学生练习列表
                    <div id="pagediv" class="fr">共
                        <a id="quescount">？</a>次练习
                        <a class="prev_page" title="上一页"></a>
                        <span id="pagebox" class="tspan">
                            <a id="curpage">？</a>
                            <a id="selectpageicon" style="display: inline-block;"></a>
                            /<a id="pagecount">？</a>
                        </span>
                        <a class="next_page" title="下一页"></a>
                    </div>
                </div>
                <div class="classExerciseInfo"></div>
            </div>
            <div id="pagelistbox"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $.myExreciseReport.init();
        $(window).bind("resize", function () {
            $.myExreciseReport.initDivBoxHeight();
            $.myExreciseReport.classOver(true);
        });
    });
    $.myExreciseReport={
        c: $('.exerciseReportClass'),
        page:1,//默认页码
        studentID:0,//学生ID
        initDivBoxHeight:function(){
            var a = $(window).width();
            var b = $(window).height();
            var c = $(window).height()-220;
            if(a<800) a=800;
            $("#workBox").height(b).width(a);
            var e = parseInt(a) - parseInt($('.classList').width())-15;
            if(e<600) e=600;
            $(".teacher").height(b).width(e);
            $('.classList').height(b);//班级列表高度
            var lh=b-$('.classTit').outerHeight(true);
            $('.loadClass').height(lh);
            $('.content .testInfo').height(c);//弹出框高度
            $('#exercisediv').css('top','70px');
        },
        //载入班级
        loadClass:function(){
            var self = this;
            $('.loadClass .bd').html($.myCommon.loading());
            $.post(U('Work/MyClass/getClassList'),{'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    $('.loadClass').html('<p style="text-align: center;">班级加载失败</p>');
                    return false;
                }
                var tit='提示信息';
                if(data['data'][0]=='success'){
                    var output='<ul>';
                    var current='';
                    for(var i in data['data'][1]){
                        if(i==0){
                            current=' class="current"';
                        }else{
                            current ='';
                        }
                        output+='<li'+current+' cid="'+data['data'][1][i]['ClassID']+'" title="'+data['data'][1][i]['ClassName']+'">'+data['data'][1][i]['ClassName']+'</li>';
                    }
                    output+='</ul>';
                    $('.loadClass .bd').html(output);
                    self.loadInfo($('.loadClass li.current').attr('cid'),1);//加载信息
                }else if(data['data'][0]=='add'){
                    $('.loadClass').html('<p style="text-align: center;">请添加班级</p>');
                    $.myDialog.normalMsgBox('loadclassmsgdiv',tit,450,'您还未加入任何班级！是否立刻加入？',3);
                    $('#loadclassmsgdiv .normal_yes').live('click',function(){
                        location.href=U('MyClass/myClass');
                    });
                    return false
                }
            });
        },
        /**
         * 班级过多增加点击滚动
         * @param ifNumChange 班级数目变化引起的班级过载 班级显示高度变化/增加班级/删除班级/解散班级
         */
        classOver:function(ifNumChange){
            if(ifNumChange==true){
                var ulStr=$('.loadClass .bd .tempWrap').html();
                $('.loadClass .bd').html(ulStr);
                $('.loadClass ul').css({'top':0});
            }
            //班级数目过载
            if($('.loadClass ul').height()>$('.loadClass').height()){
                if($('.prev:visible').length==0) {
                    $('.prev,.next').slideDown(10);
                }
                var lh = $(window).height()-$('.classTit').outerHeight(true)-40;

                //计算班级应显示的个数
                var num=Math.floor(lh/$('.loadClass ul li').outerHeight());
                jQuery(".loadClass").slide({mainCell:".bd ul",autoPage:true,type:'slide',effect:"top",autoPlay:false,vis:num,trigger:"click",scroll:num,easing:'swing'});
            }else{
                //判断点击条是否显示 显示则隐藏
                if($('.prev:visible').length>0){
                    $('.prev,.next').slideUp(10);
                    $('.loadClass .bd').html('<ul>'+$('.loadClass ul').html()+'</ul>');
                }
            }
        },
        //获取当前班级id
        getCurrentClassId: function () {
            return this.c.find('.loadClass li.current').attr('cid');
        },
        //切换班级
        changeClass: function () {
            var c = this.c;
            var self = this;
            c.on('click', '.loadClass li', function () {
                c.find('.loadClass li').removeClass('current');
                $(this).addClass('current');
                var cid = $(this).attr('cid');
                self.page=1;
                self.studentID=0;
                self.loadInfo(cid,1);
            });
        },
        //切换学生
        changeStudent:function(){
            var c=this.c;
            var self=this;
            c.on('click','.studentInfo li.click',function(){
                c.find('.studentInfo li').removeClass('hover');
                $(this).addClass('hover');
                var sid=$(this).attr('sid');
                self.studentID=sid;
                self.page=1;
                self.loadStudentExercise(sid,1);
            })
        },
        //切换分页
        changePage:function(){
            var c=this.c;
            var self=this;
            c.on('click','.pagebox a',function(){
                var classID=self.getCurrentClassId();
                var pageNum=parseInt($(this).attr('page'));
                self.page=pageNum;
                $('.teacher').scrollTop(0);
                if(self.studentID){//学生个人练习列表
                    self.loadStudentExercise(self.studentID,pageNum);
                }else {//班级所有学生练习列表
                    self.loadInfo(classID, pageNum);
                }
            });
            //上一页
            c.on('click','.prev_page',function(){
                var classID=self.getCurrentClassId();
                if(parseInt(self.page)>1){
                    self.page=parseInt(self.page)-1;
                }
                if(self.studentID){//学生个人练习列表
                    self.loadStudentExercise(self.studentID,self.page);
                }else {//班级所有学生练习列表
                    self.loadInfo(classID, self.page);
                }
            });
            //下一页
            c.on('click','.next_page',function(){
                var classID=self.getCurrentClassId();
                var pageCount=parseInt($('#pagecount').html());
                if(parseInt(self.page)<pageCount){
                    self.page=parseInt(self.page)+1;
                }else{
                    self.page=pageCount;
                }
                if(self.studentID){//学生个人练习列表
                    self.loadStudentExercise(self.studentID,self.page);
                }else {//班级所有学生练习列表
                    self.loadInfo(classID, self.page);
                }
            });
            //三角形分页跳转
            c.on('click','#quicktopage a',function(){
                $('#quicktopage a').removeClass('current');
                $(this).addClass('current');
                var nowPage=$(this).html().replace('No.','');
                $("#quicktopage").empty().remove();
                self.page=parseInt(nowPage);

                var classID=self.getCurrentClassId();
                if(self.studentID){//学生个人练习列表
                    self.loadStudentExercise(self.studentID,self.page);
                }else {//班级所有学生练习列表
                    self.loadInfo(classID, self.page);
                }
            });
        },
        //获取班级练习信息包括学生列表及班级练习列表
        loadInfo: function (classID,page) {
            var self = this;
            $(".classExerciseInfo,.studentInfo").html($.myCommon.loading());
            $.post(U('WorkReport/classExerciseData'), {'classID': classID,'page':page}, function (e) {
                if ($.myCommon.backLogin(e) == false) {
                    return false;
                }
                if (e.data) {
                    self.showInfo(e.data);
                }
            })
        },
        //加载学生个人练习列表
        loadStudentExercise:function(studentID,page){
            var c=this.c;
            var self=this;
            if(studentID=='' || page==''){
                alert('非法操作！');
                return false;
            }
            $(".classExerciseInfo").html($.myCommon.loading());
            $.post(U('WorkReport/studentExercise'),{'sid':studentID,'page':page},function(e){
                if ($.myCommon.backLogin(e) == false) {
                    return false;
                }

                if(e.data.length) {
                    c.find('.classExerciseInfo').html('该学生没有练习数据');
                }else{
                    self.showInfo(e.data);
                }
            });
        },
        //展示信息
        showInfo:function(data){
            var self=this;
            //学生数据
            if(!self.studentID) {
                self.setStudentInfo(data.student);
            }
            //练习数据
            self.setExerciseInfo(data.exercise);
            //展示分页
            if(data.page) {
                $.myPage.showPage(data.page[0], data.page[1], self.page,1);
                //展示三角形快速跳转分页
                $.myPage.showQuickSkip();
            }else{
                $.myPage.showPage(1, 1, self.page,1);
            }
        },
        setStudentInfo:function(data){
            if(data) {
                var str = '';
                str += '<ul>';
                for (var i in data) {
                    str += '<li sid="' + data[i].UserID + '" title="' + data[i].UserName + '" class="';
                    if (data[i].RealName != '') {
                        str += 'click'
                    }
                    str+='" >';
                    str += data[i].RealName + '(' + data[i].OrderNum + ')';
                    str += '</li>';
                }
                str += '</ul>';
                $('.studentInfo').html(str);//学生列表
                return false;
            }
            $('.studentInfo').html('<div class="noStudent">没有学生数据！</div>');//学生列表

        },
        setExerciseInfo:function(data){
            if(data){
                var str='';
                str+='<ul>';
                for(var i in data){
                      str+='<li><div class="fl">';
                      str+='<div><span class="exerciseStyle">'+data[i].Style+'</span>';
                      str+='&nbsp;&nbsp';
                      str+='<span class="userName">'+data[i].UserName+'</span>';
                      str+='</div>';
                      str+='<p>测试时间：'+data[i].LoadTime+'&nbsp;&nbsp;答题情况：[正确'+data[i].RightAmount+'/'+'总数'+data[i].AllAmount+']&nbsp;&nbsp;测试分数：';
                      if(data[i].Score==-1){
                          str+='暂无';
                      }else{
                          str+=data[i].Score;
                      }
                      str+='&nbsp;&nbsp;耗时：'+data[i].RealTime+'分钟</p>';
                      str+='</div>';
                      str+='<div  class="anBox fr"><a class="exerciseInfo" href="javascript:void(0);" eid="'+data[i].TestID+'">查看详情</a></div>';
                      str+='</li>';
                }
                str+='</ul>';
                $('.classExerciseInfo').html(str);
                return false;
            }
            $('.classExerciseInfo').html('<div class="noExercise">没有练习数据！</div>');
        },
        //查看练习详情
        showExerciseInfo:function(){
            var c=this.c;
            var self=this;
            c.on('click','.anBox .exerciseInfo',function(){
                var eid=$(this).attr('eid');
                if(eid==''){
                    alert('数据错误');
                    return false;
                }
                $.myDialog.normalMsgBox('exercisediv','作业详情',700,'正在加载请稍候...');
                $.post(U('WorkReport/getExerciseInfo'),{'eid':eid},function(e){
                    if ($.myCommon.backLogin(e) == false) {
                        return false;
                    }
                    var tmpStr=self.showTest(e['data'][1]);
                    var c = $(window).height()-220;
                    $.myDialog.normalMsgBox('exercisediv','作业详情',700,tmpStr);
                    $('.content .testInfo').height(c);//弹出框高度
                    $('#exercisediv').css('top','70px');
                });
            })
        },
        showTest:function(arr){
            var output='';
            var a_2,a_3,tmp_1,tmp_2,tmp_3;
            a_2=1;//序号
            a_3=1;//序号
            for(var i=0;i<arr.length;i++){
                if(arr[i]['error']==1){
                    output+='<div class="workbox" id="workbox'+arr[i]['testid']+'">数据被移除</div>';
                    continue;
                }
                if(arr[i]['testid']==null||arr[i]['testid']==''||typeof(arr[i]['testid'])=='undefined') {
                    continue;
                }
                output+='<div class="workbox" id="workbox'+arr[i]['testid']+'">';
                output+='<div class="workdiv" id="workdiv'+arr[i]['testid']+
                '"><div class="workbody">';
                a_2=a_3;
                if(arr[i]['test'].indexOf('【小题')!=-1){
                    tmp_1=arr[i]['test'].split('【小题');
                    for(var j=1;j<tmp_1.length;j++){
                        tmp_1[j]='<span class="workindex"><b>'+a_3+'．</b></span>'+tmp_1[j].substring(tmp_1[j].indexOf('】')+1);
                        a_3++;
                    }
                    output+='<p>'+tmp_1.join('');
                }else{
                    output+='<p><span class="workindex"><b>'+a_3+'．</b></span>'+$.myTest.removeLeftTag(arr[i]['test'],'<p>');
                    a_3++;
                }
                output+='</div>'+
                '<div class="workanswer" tid="'+arr[i]['testid']+'">'+
                '<div class="quesanswer_tit">答案</div>';
                a_3=a_2;
                if(arr[i]['answer'].indexOf('【小题')!=-1){
                    tmp_1=arr[i]['answer'].split('【小题');
                    if(arr[i]['u_answer']){
                        tmp_2=arr[i]['u_answer'].split('【小题】');
                        tmp_3=arr[i]['IfRight'].split('【小题】');
                        for(var k=1;k<tmp_1.length;k++){
                            tmp_1[k]='<span class="workindex"><b>'+a_3+'．</b></span>正确答案：'+tmp_1[k].substring(tmp_1[k].indexOf('】')+1)+' <p>学生答案：'+tmp_2[k]+'</p>';
                            a_3++;
                        }
                    }else{
                        for(var k=1;k<tmp_1.length;k++){
                            tmp_1[k]='<span class="workindex"><b>'+a_3+'．</b></span>'+tmp_1[k].substring(tmp_1[k].indexOf('】')+1);
                            a_3++;
                        }
                    }
                    output+='<p>'+tmp_1.join('');
                }else{
                    if(arr[i]['u_answer']) {//添加class类名及font标签，来区分查看试卷详情时的用户回答及标准答案的区别
                        if(arr[i]['u_answer'].indexOf('【小题')!=-1){
                            var uAnswer=arr[i]['u_answer'].split('【小题】');
                            arr[i]['u_answer']='';
                            for(var l=1;l<=arr[i]['testnum'];l++){
                                arr[i]['u_answer']+='<br /><span lang="EN-US" style="font-size:10.0pt;mso-bidi-font-size:12.0pt">&nbsp;&nbsp;'+l+'．</span>';
                                arr[i]['u_answer']+=uAnswer[l];
                            }
                        }
                        output += '<p class="right_answer"><span class="workindex"><b>' + a_3 + '．</b></span><font style="color:#69be83">正确答案</font>：' + $.myTest.removeLeftTag(arr[i]['answer'],"<p>") + ' <p class="student_answer">&nbsp;&nbsp;&nbsp;<font style="color:#57c2f2">学生答案</font>：' + arr[i]['u_answer'] + '</p>';
                    }else {
                        output += '<p><span class="workindex"><b>' + a_3 + '．</b></span>' + $.myTest.removeLeftTag(arr[i]['answer'], '<p>');
                    }
                    a_3++;
                }
                output+='</div>';
                if(arr[i]['analytic']){
                    output+='<div class="workanalytic" tid="'+arr[i]['testid']+'">'+
                    '<div class="quesanswer_tit">解析</div>';
                    a_3=a_2;
                    if(arr[i]['analytic'].indexOf('【小题')!=-1){
                        tmp_1=arr[i]['analytic'].split('【小题');
                        for(var jj=1;jj<tmp_1.length;jj++){
                            tmp_1[jj]='<span class="workindex"><b>'+a_3+'．</b></span>'+tmp_1[jj].substring(tmp_1[jj].indexOf('】')+1);
                            a_3++;
                        }
                        output+='<p>'+tmp_1.join('');
                    }else{
                        output+='<p><span class="workindex"><b>'+a_3+'．</b></span>'+$.myTest.removeLeftTag(arr[i]['analytic'],'<p>');
                        a_3++;
                    }
                    output+='</div>';
                }
                output+='<div class="workparse"></div>';
                output+='</div></div>';
            }
            return '<div class="testInfo">'+output+'</div>';
        },
        init: function () {
            var c = this.c, self = this;
            c.unbind();
            self.initDivBoxHeight();
            self.changeClass();//切换班级
            self.changeStudent();//切换学生
            self.changePage();//切换分页
            self.showExerciseInfo();//查看练习详情
            self.loadClass();
            self.classOver(false);
        }
    }
</script>

</body>
</html>