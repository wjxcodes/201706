jQuery.myClass={
    init:function(){
        this.initDivBoxHeight(false); //重置框架
        this.loadClass();//载入班级
        this.switchTab();//滑动门切换
        this.classAbout();//班级相关操作
        this.studentAbout();//学生相关操作
        this.teacherAbout();//教师相关操作
        this.classDynamicAbout();//班级动态相关操作
        //this.windowOnScroll();//窗口滚动事件
        $(window).resize(function() { $.myClass.initDivBoxHeight(true); });
    },
    /**
     * 重置框架
     * @param ifResize 是否是窗口变化引起重置框架
     */
    initDivBoxHeight:function(ifResize){
        var a = $(window).width();
        var b = $(window).height();
        var c = $('.wLeft').outerWidth();
        if(a<790) a=790;
        $("#workBox").width(a).height(b);
        $('.wRight').width(a-c-25);
        var lh=b-$('.classTit').outerHeight(true)-$('.addClass').outerHeight(true);
        $('.loadClass').height(lh);
        if(ifResize==true){//刷新窗口不引起班级过载载入班级时自动过载
            $.myClass.classOver(true);
        }
    },
    //滑动门切换
    switchTab:function(){
        $('.titInfo div').live('click',function(){
            //$('.titInfo').css({'position':'','top':'','left':''});
            //$('#rightTop').css({'margin-bottom':'0px'});
            var tid=$(this).attr('tid');
            var cid=$.myClass.getCurrentClassID();

            if(cid=='' || typeof(cid)=='undefined'){
                $('.addClass').click();
                return;
            }

            $('.titInfo div').removeClass('current');
            $(this).addClass('current');
            $('.dataInfo').empty().html($.myCommon.loading());
            switch(tid){
                case '1':
                    $.myClass.loadClassInfo(cid);
                    break;
                case '2':
                    $.myClass.loadStudentInfo(cid);
                    break;
                case '3':
                    $.myClass.loadTeacherInfo(cid);
                    break;
                case '4':
                    $.myClass.loadClassDynamic();
                    break;
            }
        });
    },
    //载入班级
    loadClass:function(){
        $('.loadClass .bd').html($.myCommon.loading());
        $.post(U('Work/MyClass/getClassList'),{'Status':1,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                $('.loadClass .bd').html('班级加载失败');
                return false;
            }
            var tit='提示信息';
            if(data['data'][0]=='success'){
                var output='<ul>';
                var current='';
                var yq='';
                for(var i in data['data'][1]){
                    if(i==0){
                        //载入班级信息
                        $.myClass.loadClassInfo(data['data'][1][i]['ClassID']);
                        current=' class="current"';
                    }
                    else current='';

                    if(data['data'][1][i]['Status']=='1'){
                        yq='<span class="hfontspan">（申）</span>';
                    }else if(data['data'][1][i]['Status']=='2'){
                        yq='<span class="hfontspan">（邀）</span>';
                    }else{
                        yq='';
                    }

                    output+='<li'+current+' cid="'+data['data'][1][i]['ClassID']+'" title="'+data['data'][1][i]['ClassName']+'">'+yq+data['data'][1][i]['ClassName']+'</li>';
                }
                output+='</ul>';
                $('.loadClass .bd').html(output);
                $.myClass.classOver();
            }else if(data['data'][0]=='add'){
                $('.loadClass .bd').html('请添加班级');
                $('.dataInfo').html('<p class="tips2">请添加班级！</p>');
                $('.addClass').click();
            }else{
                $.myDialog.normalMsgBox('msgdiv',tit,450,data.data,2);
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
            var lh = $(window).height()-$('.classTit').outerHeight(true)-$('.addClass').outerHeight(true)-40;

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
    //载入班级信息
    loadClassInfo:function(classID){
        $('.dataInfo').html($.myCommon.loading());
        var tit='提示信息';
        $.post(U('MyClass/getClassInfo'),{'id':classID,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                $('.dataInfo').html('<p style="text-align: center;margin:0px;padding:20px;">载入班级信息失败!</p>');
                return false;
            }
            if(data['data'][0]=='success'){
                var thisdata=data['data'][1][0];
                var tc='退出班级';
                var t_enter='';
                var t_rename='';
                if(thisdata['IsCreator']==1){
                    tc='解散班级';
                    t_rename=' <span class="btn_normal classRename">点击修改</span>';
                }
                if(thisdata['Status']==2){
                    t_enter='<span class="t_enter">加入班级</span>';
                }
                var output='<div class="classInfo">'+
                    '<div><span>班级编号</span><span>'+thisdata['OrderNum']+'</span></div>'+
                    '<div><span>班级名称</span><span id="classname'+thisdata['ClassID']+'">'+thisdata['ClassName']+'</span>'+t_rename+'</div>'+
                    '<div><span>学生人数</span><span>'+thisdata['SCount']+'人</span></div>'+
                    '<div><span>教师人数</span><span>'+thisdata['TCount']+'人</span></div>'+
                    '<div><span>所属学校</span><span>'+thisdata['SchoolFullName']+'</span></div>'+
                    '<div><span>创 建 人</span><span>'+thisdata['Creator']+'</span></div>'+
                    '<div><span>创建日期</span><span>'+thisdata['LoadTime']+'</span></div>'+
                    '<div class="exit">'+t_enter+'<span class="tc">'+tc+'</span>'+'</div>'+
                    '</div>';
                $('.dataInfo').empty().append(output);
            }else{
                $.myDialog.normalMsgBox('msgdiv',tit,450,data['data'],2);
                $('.addClass').click();
            }
        });
    },
    //我的班级相关操作(受邀请后创建班级/加入班级/退出班级/添加已有班级)
    classAbout:function(){
        //切换班级
        $('.loadClass li').live('click',function(){
            //$('.titInfo').css({'position':'','top':'','left':''});
            //$('#rightTop').css({'margin-bottom':'0px'});
            var cid=$(this).attr('cid');
            $('.loadClass li').removeClass('current');
            $(this).addClass('current');

            $('.titInfo div').removeClass('current');
            $('.titInfo div:eq(0)').addClass('current');

            $.myClass.loadClassInfo(cid);
        });
        //确认邀请后加入班级
        $('.classInfo .t_enter').live('click',function(){
            $.myDialog.normalMsgBox('enterdiv','接受邀请',450,'确认加入<span class="hfontspan">'+$('.loadClass li.current').html().replace(/<span[^>]*[^<]*<\/span>/,'')+'</span>?',3);
        });
        //确认邀请后加入班级确定
        $('#enterdiv .normal_yes').live('click',function(){
            var ClassID=$('.loadClass li.current').attr('cid');
            $.post(U('MyClass/enterTea2Class'),{'ClassID':ClassID,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                if(data['data']=='success'){
                    $.myDialog.normalMsgBox('enterdiv','接受邀请',450,'成功加入班级！',1);
                    $('.classInfo .t_enter').remove();
                    $('.loadClass li.current').html($('.loadClass li.current').html().replace(/<span[^>]*[^<]*<\/span>/,''));
                }else{
                    $.myDialog.normalMsgBox('enterdiv','接受邀请',450,data['data'],2);
                }
            });
        });
        //退出班级 解散班级
        $('.classInfo .exit .tc').live('click',function(){
            var tmp_str='';
            if($(this).html()=='解散班级'){
                //输入密码解散
                tmp_str='<div>请输入登录密码：<input class="txt_input" name="e_password" id="e_password" value="" type="password" size="20" /></div>';

            }else{
                tmp_str='退出班级会清空您与该班级的一切信息！';
            }
            $.myDialog.normalMsgBox('exitdiv',$(this).html(),450,tmp_str,3);
            $('.txt_input').focus();
        });
        //退出班级 解散班级确定
        $('#exitdiv .normal_yes').live('click',function(){
            var ClassID=$('.loadClass li.current').attr('cid');
            if(!ClassID){
                alert('班级信息有误！');
                return;
            }
            var pass='';
            if($('#exitdiv').css('display')=='block' &&  $('#e_password').length>0){
                var pass=$.trim($('#e_password').val());
                if(pass.length<6){
                    alert('请填写正确的密码！');
                    return;
                }
            }


            var tit=$('.classInfo .exit').html();
            $.post(U('MyClass/exitClass'),{'pwd':pass,'ClassID':ClassID,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                var tmp_str=tit+'成功！';
                $.myDialog.normalMsgBox('exitdiv',tit,450,tmp_str,1);
                $('.loadClass li.current').remove();
                if($('.loadClass li').length>0){
                    $('.loadClass li:eq(0)').click();
                    $.myClass.classOver(true);//退出班级时执行班级过载
                } else{
                    $('.dataInfo').html('<p class="tips2">请添加新班级！</p>');
                    $('#exitdiv .tcClose').click();
                    $('.addClass').click();
                }
            });
        });
        //添加班级
        $('.addClass').live('click',function(){
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
            var tmp_str='<div class="addnewclass">'+
                '<div class="tit">添加新班级</div>'+
                '<div class="con">'+
                '<div class="condiv"><span class="l_tit">所在学校：</span><span class="l_schoolname"></span><span class="l_selectschool">选择学校</span><input name="SchoolID" id="SchoolID" type="hidden"/><input name="AreaID" id="AreaID" type="hidden"/><input name="SchoolName" id="SchoolName" type="hidden"/></div>'+
                '<div class="condiv"><span class="l_tit">所在年级：</span><select class="normal bLeft" id="GradeID" NAME="GradeID">'+
                 gradeOption+
                '</select></div>'+
                '<div class="condiv"><span class="l_tit">所在班级：</span><input name="l_myclass" type="text" size="5" class="l_myclass" id="l_myclass" /> 班</div>'+
                '<div class="condiv"><span class="l_tit">教学科目：</span><select class="l_subject" id="l_subject">'+
                '<option value="语文">语文</option>'+
                '<option value="数学">数学</option>'+
                '<option value="英语">英语</option>'+
                '<option value="物理">物理</option>'+
                '<option value="化学">化学</option>'+
                '<option value="生物">生物</option>'+
                '<option value="政治">政治</option>'+
                '<option value="历史">历史</option>'+
                '<option value="地理">地理</option>'+
                '</select></div>'+
                '<div class="l_addclass"><span class="bgbt an01"><span class="an_left"></span><a>创建</a><span class="an_right"></span></span></div>'+
                '</div>'+
                '</div>'+
                '<div class="addoldclass">'+
                '<div class="tit">加入已有班级</div>'+
                '<div class="con">'+
                '<div class="condiv"><span class="l_tit">班级编号：</span><input name="l_otherclass" type="text" size="15" class="l_otherclass" /></div>'+
                '<div class="condiv"><span class="l_tit">教学科目：</span><select class="l_othersubject">'+
                '<option value="语文">语文</option>'+
                '<option value="数学">数学</option>'+
                '<option value="英语">英语</option>'+
                '<option value="物理">物理</option>'+
                '<option value="化学">化学</option>'+
                '<option value="生物">生物</option>'+
                '<option value="政治">政治</option>'+
                '<option value="历史">历史</option>'+
                '<option value="地理">地理</option>'+
                '</select></div>'+
                '<div class="l_insertclass"><span class=" bgbt an01"><span class="an_left"></span><a>加入</a><span class="an_right"></span></span></div>'+
                '<div class="l_des"><p>从哪里获得班级编号？</p>您所教的班级有可能已经由其他任课老师创建了班级，可以向他们询问班级编号，直接加入该班级。</div>'+
                '</div>'+
                '</div>';
            $.myDialog.normalMsgBox('classdiv','添加新班级',550,tmp_str);
            var newH=$('.addnewclass .con').height();
            var oldH=$('.addoldclass .con').height();
            var maxH=newH>oldH?newH:oldH;
            $('.addnewclass .con,.addoldclass .con').height(maxH+20);
        });
        //添加班级确定
        $('.l_addclass').live('click',function(){
            var SchoolName=$('#SchoolName').val();
            var SchoolID=$('#SchoolID').val();
            var SchoolFullName=$('.l_schoolname').html();
            var GradeID=$('#GradeID').val();
            var ClassName=$.trim($('#l_myclass').val());
            var Subject=$('#l_subject').val();
            var AreaID=$('#AreaID').val();
            if(GradeID==''){
                $.myDialog.normalMsgBox('msgdiv','提示信息',450,'请选择年级！',2);
                return;
            }
            if(ClassName==''){
                $.myDialog.normalMsgBox('msgdiv','提示信息',450,'请填写班级名称！',2);
                return;
            }
            if(ClassName.length>20){
                $.myDialog.normalMsgBox('msgdiv','提示信息',450,'输入过长,限20字符内！',2);
                return;
            }
            if(SchoolFullName==''){
                $.myDialog.normalMsgBox('msgdiv','提示信息',450,'请选择学校！',2);
                return;
            }
            if(Subject==''){
                $.myDialog.normalMsgBox('msgdiv','提示信息',450,'请选择学科！',2);
                return;
            }

            $.post(U('MyClass/createClass'),{'SchoolName':SchoolName,'SchoolID':SchoolID,'SchoolFullName':SchoolFullName,'GradeID':GradeID,'AreaID':AreaID,'ClassName':ClassName,'Subject':Subject,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                if(data['data'].indexOf('success')!=-1){
                    var tmp_str=data['data'].split('|');
                    $.myDialog.normalMsgBox('msgdiv','提示信息',450,'创建班级成功！',1);
                    if($('.loadClass ul').length==0) $('.loadClass').html('<ul></ul>');
                    //载入班级
                    $('.loadClass ul').prepend('<li cid="'+tmp_str[1]+'">'+tmp_str[2]+'</li>');
                    $('.loadClass ul li:eq(0)').click();
                    $.myClass.classOver(true);//添加班级时执行班级过载
                }else{
                    $.myDialog.normalMsgBox('msgdiv','提示信息',450,'提交失败！'+data['data'],2);
                }
            });
            $('#classdiv .tcClose').click();
        });
        //加入已有班级
        $('.l_insertclass').live('click',function(){
            var classorder=$.trim($('.l_otherclass').val());
            var subject=$('.l_othersubject').val();
            var reg=/^[0-9]*$/;
            if(!reg.test(classorder)){ //判断必须是数字
                $.myDialog.normalMsgBox('msgdiv','提示信息',450,'请填写正确的班级编号',2);
                return false;
            }

            $.post(U('MyClass/joinClass'),{'classorder':classorder,'subject':subject,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return '';
                }
                if(data['data']=='success'){
                    $.myDialog.normalMsgBox('msgdiv','提示信息',450,'加入班级成功！等待班级管理员审核。',2);
                }else{
                    $.myDialog.normalMsgBox('msgdiv','提示信息',450,'加入班级失败！'+data['data'],2);
                }
            });
        });
        //选择学校
        $('.l_selectschool').live('click',function(){
            $('.addnewclass').css({'display':'none'});
            $('.addoldclass').css({'display':'none'});
            $('#classdiv .bar .title b').html('选择学校');

            if($('.schoollist').length>0){
                $('.schoollist').css({'display':'block'});
                $.myDialog.tcDivPosition('classdiv');
            }else{
                tmp_str='<div class="schoollist"><div class="schoolcon"></div>'+
                    '<div class="normal_btn">'+
                    '<span class="addschoolbtn normal_yes bgbt an01"><span class="an_left"></span><a>确定</a><span class="an_right"></span></span>'+
                    '<span class="delschoolbtn normal_no bgbt an02"><span class="an_left"></span><a>取消</a><span class="an_right"></span></span>'+
                    '</div>'+
                    '</div>'
                $('#classdiv .content').append(tmp_str);
                var newH=$('.addnewclass .con').height();
                var oldH=$('.addoldclass .con').height();
                var maxH=newH>oldH?newH:oldH;
                $('.addnewclass .con,.addoldclass .con').height(maxH+20);
                $.myClass.getAreaList(0,0);
            }
        });
        //搜索学校
        $('.searchschoolbtn').live('click',function(){
            $('.myschool ul').html($.myCommon.loading());
            var txt=$('#searchschoolval').val();
            var areaid=$('.myschool').prev().find('li.current').attr('aid');
            $.post(U('MyClass/searchSchool'),{'key':txt,'id':areaid,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    $('.myschool ul').html('<p style="text-align: center;">没有找到该学校...<br />您可以尝试用其他关键词搜索或者点击[没有我的学校?],手动输入您的学校!</p>');
                    return false;
                }
                if(data['data']){
                    var output='';
                    var jibie='(高中)';
                    for(var i in data['data']){
                        if(data['data'][i]['Type']==1) jibie='(高中)';
                        else if(data['data'][i]['Type']==2) jibie='(初中)';
                        else if(data['data'][i]['Type']==3) jibie='(职高)';
                        output+='<li aid="'+data['data'][i]['SchoolID']+'" end="1" title="'+data['data'][i]['SchoolName']+'">'+data['data'][i]['SchoolName']+jibie+'</li>';
                    }
                    $('.myschool ul').html(output);
                }else{
                    $('.myschool ul').html('没有相关学校');
                }
            });
        });
        //没有学校
        $('.noschool').live('click',function(){
            $('.searchschool').css({'display':'none'});
            $('.myschool ul').css({'display':'none'});
            $('.insertschool').css({'display':'block'});
        });
        //返回选择学校
        $('.backschool').live('click',function(){
            $('.insertschool').css({'display':'none'});
            $('.searchschool').css({'display':'block'});
            $('.myschool ul').css({'display':'block'});
        });
        //选择学校确定
        $('.addschoolbtn').live('click',function(){
            //获取所选学校
            var schoolname=$('.myschool .current').html();
            if($('.insertschool').css('display')!='none'){
                schoolname=$('#insertschoolval').val();
            }
            if(schoolname=='' || typeof(schoolname)=='undefined'){
                alert('请选择学校');
                return;
            }
            schoolname='';
            $('.myarea').each(function(){
                schoolname+=$(this).find('li.current').html();
            });

            //是否自设置学校
            if($('.insertschool').css('display')!='none'){
                $('#SchoolID').val('');
                $('#SchoolName').val($.myCommon.removeHTML($('#insertschoolval').val()));
                $('#AreaID').val($('.myschool').prev().find('li.current').attr('aid'));
                schoolname=schoolname.replace($('.myschool li.current').html(),'')+$.myCommon.removeHTML($('#insertschoolval').val());
            }else{
                $('#SchoolID').val($('.myschool li.current').attr('aid'));
                $('#SchoolName').val('');
            }

            $('.l_schoolname').html(schoolname);

            //切换回新班级界面
            $('#classdiv .bar .title b').html('添加新班级');
            $('.schoollist').css({'display':'none'});
            $('.addnewclass').css({'display':'block'});
            $('.addoldclass').css({'display':'block'});
            $.myDialog.tcDivPosition('classdiv');
        });
        //选择学校取消
        $('.delschoolbtn').live('click',function(){
            //切换回新班级界面
            $('#classdiv .bar .title b').html('添加新班级');
            $('.schoollist').css({'display':'none'});
            $('.addnewclass').css({'display':'block'});
            $('.addoldclass').css({'display':'block'});
            $.myDialog.tcDivPosition('classdiv');
        });
        //地区选择
        $('.schoollist li').live('click',function(){
            if($('.schoolcon .list_ts').length>0){
                alert('正在载入请稍候');
                return ;
            }

            var div=$(this).parent().parent();
            div.find('li.current').removeClass('current');
            $(this).addClass('current');
            if($(this).attr('end')!='1'){
                //载入下一级地区 如果没有载入学校
                div.nextAll('div.myarea').remove();
                $.myClass.getAreaList($(this).attr('aid'),$(this).attr('last'));
                $.myDialog.tcDivPosition('classdiv');
            }
        });
    },
    //获取年级
    getGrade:function(str,gid){
        $.post(U('Work/getData'),{'style':'getSingle','cacheName':'gradeListSubject'},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            }
            if(typeof(gid)=='undefined' || gid=='') gid=0;
            var output='';
            var j;
            var selected='';
            if(data['data']){
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
            }else{
                output+='<option value="">暂无年级</option>';
            }
            $('#'+str).html(output);
        });
    },
    //载入地区
    getAreaList:function(areaid,ifLast){
        if(areaid==0 || typeof(areaid)=='undefined') {
            areaid=0;
        }
        var style='area';
        if(ifLast==1){
            style='areaToSchool';
        }
        $('.schoolcon').append($.myCommon.loading());
        $.post(U('Work/getData'),{'pID':areaid,'style':style,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            }
            $('.schoolcon .list_ts').remove();
            if(data['data']){
                var output='';
                if(ifLast==1){
                    output='<div class="myarea myschool">'+
                        '<div class="searchschool"><table border="0" cellpadding="3" cellspacing="0"><tr><td>输入学校名称直接搜索：</td><td><input type="text" name="searchschoolval" id="searchschoolval" size="15" value=""/></td>'+
                        '<td width="80"><span class="searchschoolbtn"><a>搜索</a></span></span></td>'+
                        '<td><span class="noschool btn_normal">没有我的学校？</span></td></tr></table></div>'+
                        '<div class="insertschool"><table border="0" cellpadding="3" cellspacing="0"><tr><td>请输入您的学校名称：</td><td><input type="text" name="insertschoolval" id="insertschoolval" size="15" value=""/></td>'+
                        '<td><span class="backschool btn_normal">返回选择学校</span></td></tr></table></div>'+
                        '<ul>';
                }
                else{
                    output='<div class="myarea"><ul>';
                }
                if(ifLast==0){
                    for(var i in data['data']){
                        output+='<li aid="'+data['data'][i]['AreaID']+'" last="'+data['data'][i]['Last']+'" title="'+data['data'][i]['AreaName']+'">'+data['data'][i]['AreaName']+'</li>';
                    }
                }
                if(data['data'][0]=='school'){
                    for(var i in data['data'][1]){
                        output+='<li aid="'+data['data'][1][i]['AreaID']+'" end="'+data['data'][1][i]['end']+'" title="'+data['data'][1][i]['AreaName']+'">'+data['data'][1][i]['AreaName']+'</li>';
                    }
                }
                output+='</ul></div>';
                if(areaid==0) $('.schoolcon').html(output);
                else $('.schoolcon').append(output);
            }else{
                if(areaid==0) $('.schoolcon').html('<div class="myarea">载入失败</div>');
                else $('.schoolcon').append('<div class="myarea">载入失败</div>');
            }

            $.myDialog.tcDivPosition('classdiv');
        });
    },
    //获取当前班级id
    getCurrentClassID:function(){
        return $('.loadClass li.current').attr('cid');
    },
    //学生帮助
    helpStudent:function(){
        return '<div class="addHelp" id="student">'+
            '<div class="fl">'+
            '<div class="tit">如何添加学生？</div>'+
            '<div>方式1：学生向老师询问班级编号，在学生端申请加入班级。</div>'+
            '<div>方式2：教师添加学生。 '+
            '</div></div>'+
            '<div class="fr">' +
            '<a class="downStuBtn">下载学生名单</a>'+
            '<a class="addStuBtn">添加学生帐号</a>'+
            '</div>'+
            '</div>';
    },
    //载入学生信息
    loadStudentInfo:function(classID){
        $('.dataInfo').empty().append($.myClass.helpStudent());
        $('.dataInfo').append('<div class="studentInfo">'+$.myCommon.loading()+'</div>');
        //var ClassID=$('.loadClass li.current').attr('cid');
        $.post(U('MyClass/getStudentList'),{'ClassID':classID,'Status':'all','times':Math.random()},function(data){//添加一个Status状态参数，使控制器中的getStudentList方法，调取不同的数据！！
            if($.myCommon.backLogin(data)==false){
                return false;
            }
            var output='<table class="hasTable">'+
                '<thead><tr>'+
                '<th>学号（登录名）</th>'+
                '<th>登录密码</th>'+
                '<th>姓名</th>'+
                '<th>上次登录时间</th>'+
                '<th>操作</th>'+
                '</tr></thead><tbody>';
            if(data['data']){
                if(data['data'][0]=='success'){
                    for(var i in data['data'][1]){
                        output+='<tr id="tr'+data['data'][1][i]['UserID']+'">'+
                            '<td width="140">'+data['data'][1][i]['OrderNum']+(data['data'][1][i]['UserName']!=data['data'][1][i]['OrderNum'] ? ('('+data['data'][1][i]['UserName']+')') : '')+(data['data'][1][i]['Status']=='1' ? '<font color="red">（待审）</font>' : '')+'</td>'+
                            '<td>'+data['data'][1][i]['TmpPwd']+'</td>'+
                            '<td width="120">'+data['data'][1][i]['RealName']+'</td>'+
                            '<td>'+data['data'][1][i]['LastTime']+'</td>'+
                            '<td class="lastOperate">';
                        if(data['data'][1][i]['IsCreator']==1){
                            if(data['data'][1][i]['IsDel']==1){
                                output+='<span class="s_exit" uid="'+data['data'][1][i]['UserID']+'" uname="'+data['data'][1][i]['RealName']+'">删除账号</span>';
                            }else{
                                if(data['data'][1][i]['Status']=='1'){
                                    output+='<span class="s_insert" uid="'+data['data'][1][i]['UserID']+'" uname="'+data['data'][1][i]['RealName']+'">审核通过</span> ';
                                }else{
                                    output+='<span class="s_resetpwd" uid="'+data['data'][1][i]['UserID']+'" uname="'+data['data'][1][i]['RealName']+'">重置密码</span> ';
                                }
                                output+='<span class="s_exit" uid="'+data['data'][1][i]['UserID']+'" uname="'+data['data'][1][i]['RealName']+'">请离本班</span>';
                            }
                        }
                        output+='</td></tr>';
                    }
                }else{
                    output+='<tr><td colspan="5" style="text-align: center;">'+data['data']+'</td></tr>';
                }
            }else{
                output+='<tr><td colspan="5" style="text-align: center;">数据载入失败！请重试。</td></tr>';
            }
            output+='</tbody></table>';
            $('.studentInfo').html(output);
        });
    },
    //学生相关操作
    studentAbout:function(){
        //重置密码
        $('.s_resetpwd').live('click',function(){
            var uname=$(this).attr('uname');
            if(uname==''){
                uname=$(this).parent().parent().find('td:eq(0)').html();
            }
            var tmp_str='是否确定将学生：<span class="gfont">'+uname+'</span>的密码进行重置？<div class="none" id="s_pwd_id" uid="'+$(this).attr('uid')+'"></div>';
            $.myDialog.normalMsgBox('stupasswddiv','重置密码',450,tmp_str,3);
        });
        //确认重置密码
        $('#stupasswddiv .normal_yes').live('click',function(){
            var id=$('#s_pwd_id').attr('uid');
            var ClassID=$('.loadClass li.current').attr('cid');
            $.post(U('MyClass/resetStuPwd'),{'id':id,'ClassID':ClassID,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                if(data['data'].indexOf('success')!=-1){
                    var arr=data['data'].split('|');
                    var tmp_str='修改密码成功，请尝试用密码<span class="gfont">'+arr[1]+'</span>登录';
                    $.myDialog.normalMsgBox('stupasswddiv','重置密码',450,tmp_str,1);
                    $('#tr'+id).find('td:eq(1)').html(arr[1]);
                }else{
                    $.myDialog.normalMsgBox('stupasswddiv','重置密码',450,data['data'],2);
                }
            });
        });
        //同意学生加入
        $('.s_insert').live('click',function(){
            var uname=$(this).attr('uname');
            if(uname=='') uname=$(this).parent().parent().find('td:eq(0)').html();
            var tmp_str='是否确定将学生：<span class="gfont">'+uname+'</span>加入本班？<div id="s_insert_id" uid="'+$(this).attr('uid')+'"></div>';
            $.myDialog.normalMsgBox('stuinsertdiv','加入班级',450,tmp_str,3);
        });
        //确认同意学生加入
        $('#stuinsertdiv .normal_yes').live('click',function(){
            var tmp_str='学生<span class="gfont">'+$('#stuinsertdiv .gfont').html()+'</span>加入班级成功.';
            var ClassID=$('.loadClass li.current').attr('cid');
            var id=$('#s_insert_id').attr('uid');
            $.post(U('MyClass/checkStudent'),{'id':id,'ClassID':ClassID,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                if(data['data'].indexOf('success')!=-1){
                    var tmp_str='加入班级成功！';
                    $.myDialog.normalMsgBox('stuinsertdiv','加入班级',450,tmp_str,1);
                    $('#tr'+id).find('.s_insert').html('重置密码').addClass('s_resetpwd').removeClass('s_insert');
                    $('#tr'+id).find('font').remove();
                }else{
                    $.myDialog.normalMsgBox('stuinsertdiv','加入班级',450,data['data'],2);
                }
            });
        });
        //请离本班
        $('.s_exit').live('click',function(){
            var tit=$(this).html();
            var uname=$(this).attr('uname');
            if(uname==''){
                uname=$(this).parent().parent().find('td:eq(0)').html();
            }
            var tmp_str='是否确定将学生：<span class="gfont">'+uname+'</span> '+tit+'？<div class="none" id="s_exit_id" uid="'+$(this).attr('uid')+'"></div>';
            $.myDialog.normalMsgBox('stuexitdiv',tit,450,tmp_str,3);
        });
        //确认请离本班
        $('#stuexitdiv .normal_yes').live('click',function(){
            $('#stuexitdiv .tcClose').click();
            var id=$('#s_exit_id').attr('uid');
            var ClassID=$('.loadClass li.current').attr('cid');
            $.post(U('MyClass/delStu2Class'),{'id':id,'ClassID':ClassID,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                if(data['data']=='success'){
                    $('#tr'+id).fadeOut('2000',function(){$('#tr'+id).remove();});
                    if($('.studentInfo tbody tr').length==0){
                        $('.studentInfo tbody').fadeIn('2000',function(){
                            $('.studentInfo tbody').append('<tr><td colspan="5" style="text-align: center;">没有学生数据！请添加学生</td></tr>');
                        });
                    }
                }else{
                    $.myDialog.normalMsgBox('stuexiterrordiv','删除帐号失败',450,data['data'],2);
                }
            });
        });
        //添加学生账号
        $('.addStuBtn').live('click',function(){
            var tmp_str='<div class="addStuDes">给您的学生创建帐号后，学生可以接收您布置的作业，请使用以下三种方式为学生创建帐号：</div>'+
                '<div class="addStuMain"><ul class="addStu_tit">'+
                '<li class="current" tag="addStu_list1">上传学生名单</li>'+
                '<li tag="addStu_list2">输入学生名单</li>'+
                '<li tag="addStu_list3">输入学生人数</li>'+
                '</ul>'+
                '<div class="addStu_con">'+
                '<div class="addStu_list addStu_list1">'+
                '<div class="addStu_list1_h1">参考模板，<span class="addStu_downexcel">点此下载excel模板</span></div>'+
                '<div class="addStu_list1_h2">'+
                '<table border="0" cellpadding="5" cellapseing="0" style="margin-right:10px;"><tr><td><table border="1" cellpadding="5" cellapseing="0" style="margin-right:10px;">'+
                '<tr><td>学生</td></tr>'+
                '<tr><td>毛小明</td></tr>'+
                '<tr><td>林小丽</td></tr>'+
                '<tr><td>张小华</td></tr>'+
                '<tr><td>王小豆</td></tr>'+
                '</table>'+
                '</td><td valign="top"><input type="file" name="file_upload" id="file_upload" multiple="false" />'+
                '<div id="queues" class="none"></div><div id="resulttt"></div></td></tr></table>'+
                '</div>'+
                '</div>'+
                '<div class="addStu_list addStu_list2">'+
                '<div class="addStu_list2_l1">'+
                '<div>请输入学生姓名，每行一个</div>'+
                '<textarea class="addStu_textarea"></textarea>'+
                '</div>'+
                '<div class="addStu_list2_l2">'+
                '<div>参考范例</div>'+
                '<ul>'+
                '<li>张明</li>'+
                '<li>李小鹏</li>'+
                '<li>蔡信威</li>'+
                '<li>范思雨</li>'+
                '<li>盖磊</li>'+
                '<li>郭建伟</li>'+
                '</ul>'+
                '</div>'+
                '</div>'+
                '<div class="addStu_list addStu_list3">'+
                '<div class="addStu_list3_h1">请输入学生数量：<input name="addStu_num" type="text" value="" size="15" class="addStu_num"/> 每个班级最多100人，已有'+($('.studentInfo tr:eq(1) td').length==1 ? '0' : ($('.studentInfo tr').length-1))+'人</div>'+
                '</div>'+
                '</div></div>';

            $.myDialog.normalMsgBox('addStudiv','添加学生帐号',550,tmp_str,3);
            var uploadSuccess=0; //上传状态
            $(function(){
                $("#file_upload").uploadify({
                    'swf'     : '/Public/plugin/uploadify/uploadify.swf',
                    'uploader'      : U('Work/MyClass/uploadify'),
                    'queueID'       : 'queues',
                    'fileTypeExts'  : '*.xls; *.xlsx', //允许文件上传类型,和fileDesc一起使用.
                    'fileTypeDesc'  : 'excel file',  //将不允许文件类型,不在浏览对话框的出现.
                    'buttonText'    : '上传excel名单',
                    'method'        : 'post',
                    'auto'          : true,
                    'multi'         : false,
                    'onUploadStart':function(file){
                        $('#file_upload').uploadify('settings','formData',{'codes':key,'user':userName,'ClassID':$('.loadClass li.current').attr('cid'),'zz':Math.random()});
                    },
                    'onUploadError':function(file, errorCode, errorMsg, errorString){
                        alert(errorMsg);
                    },
                    'onUploadSuccess':function(file, data, response){
                        if(data.indexOf('Warning')!=-1 && data.indexOf('Content-Length')!=-1){
                            $.myDialog.normalMsgBox('msgdiv','提示信息',450,'文件大小超出限制',2);
                        }else if(data.indexOf('Warning')!=-1){
                            $.myDialog.normalMsgBox('msgdiv','提示信息',450,data,2);
                        }else if(data=='type error' || data==''){
                            $.myDialog.normalMsgBox('msgdiv','提示信息',450,'您上传的文件类型有误！请上传*.xls,*.xlsx;文件',2);
                        }else if(data.indexOf('filesize exceeds')!=-1){
                            $.myDialog.normalMsgBox('msgdiv','提示信息',450,'文件大小超出限制',2);
                        }else if(data.indexOf('file not upload')!=-1){
                            $.myDialog.normalMsgBox('msgdiv','提示信息',450,'文件无法上传',2);
                        }else if(data.indexOf('success')!=-1){
                            uploadSuccess=1;
                        }else{
                            $.myDialog.normalMsgBox('msgdiv','提示信息',450,data,2);
                        }
                    },
                    'onUploadComplete' : function(file) {
                        if(uploadSuccess==1){
                            $.myDialog.normalMsgBox('addStudiv','提示信息',450,'添加成功！',1);
                            $('.titInfo div:eq(1)').click();
                            uploadSuccess=0;
                        }
                    }
                });
            });
        });
        //确认添加学生账号
        $('#addStudiv .normal_yes').live('click',function(){
            var tag=$('#addStudiv .addStu_tit li.current').attr('tag');
            var ClassID=$('.loadClass li.current').attr('cid');
            switch(tag){
                case 'addStu_list1':
                    $('#addStudiv .tcClose').click();
                    break;
                case 'addStu_list2':
                    var con=$.trim($('#addStudiv .addStu_textarea').val());
                    if(con==''){
                        $.myDialog.normalMsgBox('msgdiv','提示信息',450,'请输入学生姓名，每行一个。',2);
                        return;
                    }
                    $.post(U('Work/MyClass/createStudent'),{'con':con,'ClassID':ClassID,'times':Math.random()},function(data){
                        if($.myCommon.backLogin(data)==false){
                            return false;
                        }
                        if(data['data'][0]=='success'){
                            //载入学生账号
                            if($('.studentInfo tr:eq(1) td').length==1){
                                $('.studentInfo tr:eq(1)').remove();
                            }
                            var output='';
                            for(var i in data['data'][1]){
                                output+='<tr id="tr'+data['data'][1][i]['UserID']+'">'+
                                    '<td>'+data['data'][1][i]['OrderNum']+"("+data['data'][1][i]['UserName']+")"+'</td>'+
                                    '<td>'+data['data'][1][i]['TmpPwd']+'</td>'+
                                    '<td>'+data['data'][1][i]['RealName']+'</td>'+
                                    '<td>'+data['data'][1][i]['LastTime']+'</td>'+
                                    '<td class="lastOperate"><span class="s_exit" uid="'+data['data'][1][i]['UserID']+'" uname="'+data['data'][1][i]['RealName']+'">删除账号</span></td>'+
                                    '</tr>';
                            }
                            $('.studentInfo table').append(output);

                            $.myDialog.normalMsgBox('msgdiv','提示信息',450,'创建学生账户成功',1);
                            $('#addStudiv .addStu_textarea').val('');
                            $('#addStudiv .tcClose').click();
                        }else{
                            $.myDialog.normalMsgBox('msgdiv','提示信息',450,'创建学生账户失败！'+data['data'][1],2);
                        }
                    });
                    break;
                case 'addStu_list3':
                    var num=$.trim($('.addStu_num').val());
                    if(num=='' || isNaN(num)){
                        $.myDialog.normalMsgBox('msgdiv','提示信息',450,'请输入需要生成的学生数量。',2);
                        return;
                    }
                    $.post(U('MyClass/createStudent'),{'num':num,'ClassID':ClassID,'times':Math.random()},function(data){
                        if($.myCommon.backLogin(data)==false){
                            return false;
                        }
                        if(data['data'][0]=='success'){
                            //载入学生账号
                            if($('.studentInfo tr:eq(1) td').length==1){
                                $('.studentInfo tr:eq(1)').remove();
                            }
                            var output='';
                            for(var i in data['data'][1]){
                                output+='<tr id="tr'+data['data'][1][i]['UserID']+'">'+
                                    '<td>'+data['data'][1][i]['OrderNum']+"("+data['data'][1][i]['UserName']+")"+'</td>'+
                                    '<td>'+data['data'][1][i]['TmpPwd']+'</td>'+
                                    '<td>'+data['data'][1][i]['UserName']+'</td>'+
                                    '<td>'+data['data'][1][i]['LastTime']+'</td>'+
                                    '<td class="lastOperate"><span class="s_exit" uid="'+data['data'][1][i]['UserID']+'" uname="'+data['data'][1][i]['OrderNum']+'">删除账号</span></td>'+
                                    '</tr>';
                            }
                            $('.studentInfo table').append(output);
                            $.myDialog.normalMsgBox('msgdiv','提示信息',450,'创建学生账户成功',1);
                        }else{
                            $.myDialog.normalMsgBox('msgdiv','提示信息',450,'创建学生账户失败！'+data['data'][1],2);
                        }
                    });
                    $('#addStudiv .tcClose').click();
                    break;
            }
        });

        //下载学生名单
        $('.downStuBtn').live('click',function(){
            if($('.studentInfo tr:eq(1) td').length <= 1){
                $.myDialog.normalMsgBox('downerror','下载学生名单',450,'本班级还没有学生呢~赶快添加吧',4);
                return false;
            }
            var tmp_str='确认下载所有学生名单。';
            $.myDialog.normalMsgBox('downstudiv','下载学生名单',450,tmp_str,3);
        });
        //确认下载学生名单
        $('#downstudiv .normal_yes').live('click',function(){
            $.myDialog.tcCloseDiv('downstudiv');
            var ClassID=$('.loadClass li.current').attr('cid');
            $.myDialog.showMsg('名单生成中请稍等...',0,-1);
            $.post(U('MyClass/downStudentList'),{'ClassID':ClassID,'times':Math.random()},function(data){
                $.myDialog.tcCloseDiv('msgdiv');
                if(data['data'].indexOf('success')!=-1){
                    var tmp_arr=data['data'].split('|');
                    var tmp_str='<p>　</p><p align="center">名单已生成，请【<a class="studentListDown" href="'+tmp_arr[1]+'" target="_blank">点击这里</a>】下载！</p><p>　</p>';
                    $.myDialog.normalMsgBox('downlink','下载学生名单',450,tmp_str,5);
                    $('.studentListDown').click(function(){
                        $.myDialog.tcCloseDiv('downlink');
                    });
                }else{
                    $.myDialog.normalMsgBox('downerror','下载学生名单',450,data['data'],2);
                }
            });
        });
        //下载上传模板excel
        $('.addStu_downexcel').live('click',function(){
            location.href=U('Home/Index/excelStudent');
        });
        //添加学生方式切换
        $('.addStu_tit li').live('click',function(){
            $('.addStu_tit li.current').removeClass('current');
            $(this).addClass('current');
            var tag=$(this).attr('tag');
            $('.addStu_con .addStu_list').each(function(){
                $(this).css({'display':'none'});
            });
            $('.addStu_con .'+tag).css({'display':'block'});
        });
    },
    //教师帮助
    helpTeacher:function(){
        return '<div class="addHelp" id="teacher">'+
            '<div class="fl"><div class="tit">如何添加老师？</div>'+
            '<div>方式1：老师向班级管理员询问班级编号，在教师端申请加入班级。</div>'+
            '<div>方式2：邀请教师加入。 </div>'+
            '</div>'+
            '<div class="fr"><a class="addTeaBtn">邀请教师加入</a></div>'+
            '</div>';
    },
    //载入教师信息
    loadTeacherInfo:function(classID){
        //var ClassID=$('.loadClass li.current').attr('cid');
        if(classID=='' || typeof(classID)=='undefined'){
            $.myDialog.normalMsgBox('msgdiv','提示信息',450,'请添加新班级',2);
            $('.dataInfo').empty().html('请添加新班级');
            return;
        }
        $('.dataInfo').empty().append($.myClass.helpTeacher());
        $('.dataInfo').append('<div class="teacherinfo">'+$.myCommon.loading()+'</div>');

        $.post(U('MyClass/getTeacherList'),{'ClassID':classID,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            }
            var output='<table class="hasTable">'+
                '<thead><tr>'+
                '<th>姓名</th>'+
                '<th>学科</th>'+
                '<th>加入班级日期</th>'+
                '<th>上次登录时间</th>'+
                '<th>操作</th></tr></thead><tbody>';
            if(data['data']){
                if(data['data'][0]=='success'){
                    for(var i in data['data'][1]){
                        output+='<tr id="tr'+data['data'][1][i]['UserID']+'">'+
                            '<td>'+data['data'][1][i]['RealName']+(data['data'][1][i]['IsCreatorMe']==1 ? '<span class="sfont">(创建者)</span>' : '')+(data['data'][1][i]['Status']==2 ? '<span class="hfontspan">(邀请中)</span>' : '')+(data['data'][1][i]['Status']==1 ? '<span class="hfontspan">(待审核)</span>' : '')+'</td>'+
                            '<td>'+data['data'][1][i]['SubjectName']+'</td>'+
                            '<td>'+data['data'][1][i]['LoadTime']+'</td>'+
                            '<td>'+data['data'][1][i]['LastTime']+'</td>'+
                            '<td class="lastOperate">';
                        if(data['data'][1][i]['IsCreator']==1){
                            if(data['data'][1][i]['Status']==1){
                                output+='<span class="t_pass btn_normal" uid="'+data['data'][1][i]['UserID']+'" uname="'+data['data'][1][i]['RealName']+'">审核通过</span> ';
                            }
                            if(data['data'][1][i]['IsMe']==1){
                                output+='<span class="t_changesubject btn_normal" uid="'+data['data'][1][i]['UserID']+'">修改学科</span> ';
                                output+='<span class="t_change btn_normal" uid="'+data['data'][1][i]['UserID']+'" uname="'+data['data'][1][i]['RealName']+'">转让本班</span> ';
                            }else{
                                output+='<span class="t_exit btn_normal" uid="'+data['data'][1][i]['UserID']+'" uname="'+data['data'][1][i]['RealName']+'">请离本班</span> ';
                            }
                        }else{
                            if(data['data'][1][i]['IsMe']==1){
                                output+='<span class="t_changesubject btn_normal" uid="'+data['data'][1][i]['UserID']+'">修改学科</span> ';
                                output+='<span class="t_exit btn_normal" uid="'+data['data'][1][i]['UserID']+'" uname="'+data['data'][1][i]['RealName']+'">退出本班</span> ';
                            }
                        }
                        output+='</td></tr>';
                    }
                }else{
                    output+='<tr><td colspan="5" style="text-align: center;">'+data['data']+'</td></tr>';
                }
            }else{
                output+='<tr><td colspan="5" style="text-align: center;">数据载入失败！请重试。</td></tr>';
            }
            output+='</tbody></table>';
            $('.teacherinfo').html(output);
        });
    },
    //教师相关操作
    teacherAbout:function(){
//邀请教师加入班级
        $('.addTeaBtn').live('click',function(){
            var tmp_str='<p>向已经注册账号的教师发送邀请。(可输入账号/手机号/邮箱)</p>'+
                '<p>教师账户：<input type="text" name="" size="20" id="teausername"/></p>';
            $.myDialog.normalMsgBox('addteadiv','邀请教师加入',550,tmp_str,3);
        });
        //确认邀请
        $('#addteadiv .normal_yes').live('click',function(){
            var tmp_str='邀请已经发送成功。';
            var ClassID=$('.loadClass li.current').attr('cid');
            var UserName=$.trim($('#teausername').val());
            $.post(U('MyClass/addTea2Class'),{'ClassID':ClassID,'UserName':UserName,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                $.myDialog.normalMsgBox('addteadiv','邀请成功',450,tmp_str,1);
                var tmp_arr=data['data'].split('|');
                $('.teacherinfo table').append('<tr id="tr'+tmp_arr[3]+'"><td>'+UserName+'<span class="hfontspan">(邀请中)</span></td><td>'+tmp_arr[4]+'</td><td>'+tmp_arr[1]+'</td><td>'+tmp_arr[2]+'</td><td class="lastOperate"><span class="t_exit btn_normal" uname="'+UserName+'" uid="'+tmp_arr[3]+'">请离本班</span></td></tr>');
            });
        });
        //修改学科
        $('.t_changesubject').live('click',function(){
            var tmp_str='变更学科信息为：<select id="t_chsubject">'+
                '<option value="语文">语文</option>'+
                '<option value="数学">数学</option>'+
                '<option value="英语">英语</option>'+
                '<option value="物理">物理</option>'+
                '<option value="化学">化学</option>'+
                '<option value="生物">生物</option>'+
                '<option value="政治">政治</option>'+
                '<option value="历史">历史</option>'+
                '<option value="地理">地理</option>'+
                '</select><div id="changesubjectdiv_id" uid="'+$(this).attr('uid')+'"></div>';
            $.myDialog.normalMsgBox('changesubjectdiv','修改学科',450,tmp_str,3);
        });
        //修改学科
        $('#changesubjectdiv .normal_yes').live('click',function(){
            var tmp_str='修改学科成功。';
            var ClassID=$('.loadClass li.current').attr('cid');
            var Subject=$('#t_chsubject').val();
            var uid=$('#changesubjectdiv_id').attr('uid');
            $.post(U('MyClass/changeSubject'),{'ClassID':ClassID,'Subject':Subject,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                var tmp_arr=data['data'].split('|');
                $('#tr'+uid).find('td:eq(1)').html(tmp_arr[1]);
                $.myDialog.normalMsgBox('changesubjectdiv','修改学科',450,'修改学科成功',1);
            });
        });
        //请离班级 退出班级
        $('.t_exit').live('click',function(){
            if($('.teacherinfo table tr:eq(1) td:eq(4)').html()==''){
                $.myDialog.normalMsgBox('texitdiv',$(this).html(),450,'确认退出班级？<div id="t_exit_id" uid="'+$(this).attr('uid')+'"></div>',3);
            }else{
                var uname=$(this).attr('uname');
                $.myDialog.normalMsgBox('texitdiv','请离班级',450,'是否让用户【'+uname+'】离开班级？<div id="t_exit_id" uid="'+$(this).attr('uid')+'"></div>',3);
            }
        });
        //请离班级 退出班级确定
        $('#texitdiv .normal_yes').live('click',function(){
            var ClassID=$('.loadClass li.current').attr('cid');
            var id=$('#t_exit_id').attr('uid');
            var tit='离开班级';

            $.post(U('MyClass/exitClass'),{'uid':id,'ClassID':ClassID,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                $.myDialog.normalMsgBox('texitdiv',tit,450,'操作成功',1);
                if($('.teacherinfo table tr:eq(1) td:eq(4)').html()==''){
                    $('.loadClass li.current').remove();
                    if($('.loadClass li').length>0) $('.loadClass li:eq(0)').click();
                    else $('.addClass').click();
                }else{
                    $('#tr'+id).remove();
                }
            });
        });
        //转让本班
        $('.t_change').live('click',function(){
            //输入密码
            var tmp_str='<p>将班级转让给其他教师。(可输入账号/手机号/邮箱)</p><p class="hfontspan">只能转让给同班级下的其他教师</p>'+
                '<div><p>请输入您的登录密码：<input name="c_password" id="c_password" class="txt_input" value="" type="password" size="20" /></p></div>'+
                '<div>请输入其他教师账户：<input name="c_teacher" id="c_teacher" class="txt_input" value="" type="text" size="20" /></div>';
            $.myDialog.normalMsgBox('tchangediv','转让本班',450,tmp_str,3);
        });
        //转让本班确定
        $('#tchangediv .normal_yes').live('click',function(){
            var pwd=$.trim($('#c_password').val());
            var uname=$.trim($('#c_teacher').val());
            var ClassID=$('.loadClass li.current').attr('cid');
            var tit='转让班级';

            if(pwd.length<6){
                $.myDialog.normalMsgBox('msgdiv','提示信息',450,'请输入正确的密码！',2);
                return;
            }if(uname==''){
                $.myDialog.normalMsgBox('msgdiv','提示信息',450,'请输入教师账户！',2);
                return;
            }

            $.post(U('MyClass/changeClassCreator'),{'uname':uname,'pwd':pwd,'ClassID':ClassID,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                $.myDialog.normalMsgBox('tchangediv',tit,450,'操作成功',1);
                $('.titInfo div.current').click();
            });
        });
        //同意教师加入
        $('.t_pass').live('click',function(){
            var uname=$(this).attr('uname');
            if(uname=='') uname=$(this).parent().parent().find('td:eq(0)').html();
            var tmp_str='是否确定将教师：<span class="gfont">'+uname+'</span>加入本班？<div id="t_pass_id" uid="'+$(this).attr('uid')+'"></div>';
            $.myDialog.normalMsgBox('teapassdiv','加入班级',450,tmp_str,3);
        });
        //确认同意教师加入
        $('#teapassdiv .normal_yes').live('click',function(){
            var tmp_str='教师<span class="gfont">'+$('#teapassdiv .gfont').html()+'</span>加入班级成功.';
            var ClassID=$('.loadClass li.current').attr('cid');
            var id=$('#t_pass_id').attr('uid');
            $.post(U('MyClass/checkTeacher'),{'id':id,'ClassID':ClassID,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                if(data['data'].indexOf('success')!=-1){
                    var tmp_str='加入班级成功！';
                    $.myDialog.normalMsgBox('teapassdiv','加入班级',450,tmp_str,1);
                    $('#tr'+id).find('.t_pass').remove();
                    $('#tr'+id).find('.hfontspan').remove();
                }else{
                    $.myDialog.normalMsgBox('teapassdiv','加入班级',450,data['data'],2);
                }
            });
        });
        //改名
        $('.classRename').live('click',function(){
            var tmp_str=$.myCommon.loading();
            $.myDialog.normalMsgBox('renamediv','班级更名',450,tmp_str);
            $.post(U('MyClass/getClassName'),{'ClassID':$('.loadClass .current').attr('cid'),'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                tmp_str='<select class="normal bLeft" id="rename_grade" NAME="rename_grade">'+
                    '<option value="">载入中请稍候...</option>'+
                    '</select>';
                tmp_str+=' <input name="rename_classname" size="15" type="text" value="'+data['data'][2]+'" id="rename_classname" />班';

                $.myDialog.normalMsgBox('renamediv','班级更名',450,tmp_str,3);
                $.myClass.getGrade('rename_grade',data['data'][1]);
            });
        });
        //改名确定
        $('#renamediv .normal_yes').live('click',function(){
            $.myDialog.normalMsgBox('msgdiv','提示信息',450,'正在提交请稍候...');
            var rename_grade=$('#rename_grade').val();
            var rename_classname=$.trim($('#rename_classname').val());
            if(rename_grade==''){
                $.myDialog.normalMsgBox('msgdiv','提示信息',450,'请选择年级！',2);
                return;
            }
            if(rename_classname==''){
                $.myDialog.normalMsgBox('msgdiv','提示信息',450,'请填写班级名称！',2);
                return;
            }
            if(rename_classname.length>20){
                $.myDialog.normalMsgBox('msgdiv','提示信息',450,'输入过长,限20字符内！',2);
                return;
            }
            var ClassID=$('.loadClass .current').attr('cid');
            $.post(U('MyClass/setClassName'),{"ClassID":ClassID,'GradeID':rename_grade,'ClassName':rename_classname,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                $('#msgdiv .tcClose').click();
                var tmp_str=data['data'].split('#$#');
                $('.loadClass .current').html(tmp_str[1]);
                $('#classname'+ClassID).html(tmp_str[1]);
                $.myDialog.normalMsgBox('renamediv','班级更名',450,'修改班级名称成功！',1);
            });
        });
    },
    //载入班级动态
    loadClassDynamic:function(){
        $('.dataInfo').empty().append('<div class="dynamicInfo"><ul></ul></div><div class="dynamicMore" page="1" ismore="1">更多动态</div>');
        $('.dynamicMore').click();
    },
    //班级动态相关操作
    classDynamicAbout:function(){
        //载入更多动态
        $('.dynamicMore').live('click',function(){
            if($(this).html().indexOf('正在载入')!=-1 || $(this).attr('ismore')=='0'){
                return;
            }
            $(this).html('正在载入...');
            var page=$(this).attr("page");
            var classID=$.myClass.getCurrentClassID();
            $.post(U('MyClass/getClassDynamic'),{'ClassID':classID,'page':page,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    $('.dynamicInfo ul').append('<li>'+data['data']+'</li>');
                    return false;
                }
                if(data['data'][2]=='0'){
                    $('.dynamicMore').attr('ismore',0);
                    $('.dynamicMore').html("没有动态了");
                    return;
                }
                var output='';
                for(var i in data['data'][1]){
                    output+='<li>'+data['data'][1][i]['Content']+'<span class="loadTime">'+data['data'][1][i]['LoadTime']+'</span></li>';
                }
                $('.dynamicInfo ul').append(output);
                if(data['data'][3]=='0'){
                    $('.dynamicMore').attr('ismore',0);
                    $('.dynamicMore').html("没有动态了");
                }else{
                    $('.dynamicMore').html("更多动态 > > > ");
                    $('.dynamicMore').attr('page',parseInt($('.dynamicMore').attr('page'))+1);
                }
            });
        });
    }
    //滚动条事件
//    windowOnScroll:function(){
//        $('#workBox').scroll(function(){
//            if($('#workBox').scrollTop()>$('#rightTop').outerHeight()){
//                $('#rightTop').css({'margin-bottom':$('.titInfo').outerHeight()+'px'});
//                $('.titInfo').css({'position':'fixed','top':'0px','left':'110px','width':(parseInt($('.wRight').width())-2)+'px'});
//            }else{
//                $('#rightTop').css({'margin-bottom':'0px'});
//                $('.titInfo').css({'position':'','top':'','left':''});
//            }
//        });
//    }
}