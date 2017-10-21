$.DocSave = {
    init : function(){
        var subjectID=Cookie.Get("SubjectId");
        $('#loca_text span').html($.myCommon.getSubjectNameFromParent());
        $.DocSave.pageInit();
        //上一页
        $.DocSave.toPrevPage(page, $('.prev_page'));
        //下一页
        $.DocSave.toNextPage(page, $('.next_page'));

        $.myPage.showQuickSkip();
    },

    toPrevPage : function(page, obj, eventName){
        eventName = eventName || 'click';
        obj.live(eventName, function(){
            var page=parseInt(page);
            if(page>1){
                $.DocSave.gotoPage(page-1);
            }else{
                $.DocSave.gotoPage(1);
            }
        });
    },

    toNextPage : function(page, obj, eventName){
        eventName = eventName || 'click';
        obj.live(eventName, function(){
            var page=parseInt(page);
            if(page<parseInt($('#pagecount').html())){
                $.DocSave.gotoPage(page+1);
            }
            else{
                if(parseInt($('#pagecount').html())>1) $.DocSave.gotoPage(parseInt($('#pagecount').html()));
            }
        });
    },

    pageInit : function() {
        $.DocSave.initDivHeight();
        $.DocSave.ajaxGetPaper();
        $.DocSave.bindEvent();
        $(window).resize(function() { $.DocSave.initDivHeight(); });
    },

    gotoPage : function(pagenum) {
        var dated = "";
        $("a.dated").each(function() {
            if ($(this).hasClass("dated_current")) {
                dated = $(this).attr("id");
            }
        });
        page=pagenum;
        $.DocSave.ajaxGetPaper(dated, pagenum);
    },

    initDivHeight : function() {
        // $('#pagelistbox').css({'height':'20px'});
        var height = $(window).height()-$('#paperinfod').outerHeight()-$('#pagelistbox').outerHeight()-$('#righttop').outerHeight();
        height =height -13;
        $("#paperlistbox").css({ height: height ,'overflow':'auto'});
    },

    ajaxGetPaper : function(dated, page) {
        if (typeof dated == "undefined" || dated == null) { dated = $(".dated").get(0).id; }
        if (typeof page == "undefined" || page == null || page <= 1) { page = 1; }
        var subjectid = Cookie.Get("SubjectId");
        if(subjectid=='' || subjectid=='undefined' || subjectid==null){
            $.myDialog.showMsg('请选择科学!',1);
            return false;
        }
        var area = $("#area").val();
        $.post(U('Home/Index/saveList'),{datediff:dated,'subjectID':subjectid,page:page ,m:Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            };
                var paperdata = data['data'][0];
                var papercount = data['data'][1];
                var percount = data['data'][2];
                var pagecount = Math.ceil(parseInt(data['data'][1])/parseInt(data['data'][2]));

                if (pagecount == 0) { pagecount = 1; }
                if (page <= 1) { page = 1; }
                if (page >= pagecount) { page = pagecount; }

                $("a.dated").removeClass("dated_current");
                $("#" + dated).addClass("dated_current");

                $("#pagecount").html(pagecount);
                $("#selectpageicon").css({display: (pagecount <= 1) ? "none" : "inline-block" });
                $.DocSave.tableList(paperdata);
                $.myPage.showPage(papercount,percount,page,1);
        });
    },

    tableList : function(paperdata) {
        var html = [];
        html.push("<table border=\"0\" cellspacing=\"0\" class=\"table\" width=\"100%\">");
        html.push("<colgroup>" +
        "<col class=\"odd\"/>" +
        "<col class=\"even\"/>" +
        "<col class=\"odd\"/>" +
        "<col class=\"even\"/>" +
        "<col class=\"odd\"/>" +
        "<col class=\"even\"/>" +
        "<col class=\"odd\"/>" +
        "</colgroup>");
        html.push("<tr>" +
        "<th align=\"center\" width=\"50\">编号</th>" +
        "<th align=\"left\" >存档试卷名称</th>" +
        "<th align=\"left\" width=\"160\">日期</th>" +
        "<th align=\"left\" width=\"190\">操作</th>" +
        "</tr>");
        if(paperdata) {
            for (var i = 0, len = paperdata.length; i < len; i++) {
                var paper = paperdata[i];
                var id = paper.SaveID;
                var papername = paper.SaveName;
                var unixTimestamp = new Date(paper.LoadTime*1000); 
                var savetime = unixTimestamp.toLocaleString();
                var pwd = paper.SavePwd;
                var code = paper.SaveCode;
                var examDesc='';
                if(code!=0) examDesc='<font color="#f00">[联考试卷]</font>';

                html.push("<tr><td width=\"30\" align=\"center\">" + id + "</td><td class=\"paperName\">"+(pwd==0 ? '' : "<span class=\"paperkeyicon\" title=\"带密码的存档试卷\"></span>") +examDesc+ papername + "</td><td style=\"font-size:12px; color:#999999\">" + savetime + "</td>" +
                    "<td>"+
                    "<div class=\"reload an02 bgbt\" paperid=\"" + id + "\" paperkey=\""+(pwd==0 ? "" : "?" )+"\" style=\"float:left; margin-top:6px; margin-right:10px\"><span class=\"an_left\"></span><a>恢复存档</a><span class=\"an_right\"></span></div>"+
                    "<div class=\"delpaper an02 bgbt\"  paperid=\"" + id + "\" style=\"float:right; margin-top:6px; margin-right:10px\"><span class=\"an_left\"></span><a>删除</a><span class=\"an_right\"></span></div>"+
                    (code==0 ? '':"<div class=\"paperCode an02 bgbt\" paperid=\"" + id + "\" paperkey=\""+(pwd==0 ? "" : "?" )+"\" style=\"float:left; margin-top:6px; margin-right:10px\"><span class=\"an_left\"></span><a>获取提取码</a><span class=\"an_right\"></span></div>"));
            }
        }else{
            html.push("<tr><td colspan=\"4\" align=\"center\">暂无信息</td></tr>");
        }
        html.push("</table>");
        $("#paperlistbox").html(html.join(""));
        $.DocSave.initDivHeight();
    },

    getSubjectName : function(id) {
        if ($("#area").val() == 1) { return ""; }
        var subject = parent.subjectID;
        for (var i = 0; i < subject.length; i++) {
            var quesbanks = subject[i].sub;
            for (var j = 0; j < quesbanks.length; j++) {
                if (quesbanks[j].SubjectID == id) {
                    return "(" + quesbanks[j].SubjectName + ")";
                }
            }
        }
        return "";
    },
    
    bindEvent : function() {
        $("div.reload").live("click", function() {
            var id = $(this).attr("paperid");
            var key = $(this).attr("paperkey");
            if(key!=''){
                $.DocSave.setPaperPwd(id);
                return;
            }
            $.DocSave.reloadPaper(id,key);
        });
        $("div.delpaper").live("click", function() {
            var paperid = $(this).attr("paperid");
            var paperName=$(this).parent().parent().children('.paperName').html();
            $.myDialog.normalMsgBox('delSelfPaper','温馨提醒',450,'<div><b class="delThisPaper"  paperid="'+paperid+'">您确定删除【'+paperName+'】文档？</b></div>',3);
        });
        $('#delSelfPaper .normal_yes').live('click',function(){
            var paperID=$('#delSelfPaper .delThisPaper').attr('paperid');
            $.DocSave.delPaper(paperID,'delSelfPaper');
        });
        
        //获取提取码
        $("div.paperCode").live("click", function() {
            var paperid = $(this).attr("paperid");
            $.myRepeat.clipBoard(paperid);
        });
        
        $("a.dated").click(function() {
            page=1;
            var dated = $(this).attr("id");
            $.DocSave.ajaxGetPaper(dated);
        });

        //展示三角形快速跳转分页
        $.myPage.showQuickSkip();
        //分页快速跳转
        $("#quicktopage a").live("click", function() {
            var page = $(this).text().replace("No.", "");
            $("#quicktopage").hide();
            $.DocSave.gotoPage(page);
        });
        
        $("#pagelistbox a").live("click",function(){
            $.DocSave.gotoPage($(this).attr('page'));
        });
        
        $('#setpwdbtn').live('click',function(){
            var key=$('.paperkeyinput').val();
            if(key.length<1){
                $.myDialog.showMsg('请输入密码！',1);
                $('.paperkeyinput').focus();
                return;
            }
            var id=$(this).attr('did');
            $.DocSave.reloadPaper(id,key);
        });
    },

    setPaperPwd : function(id){
        var idname='savepwddiv';
        $.myDialog.tcLoadDiv('存档密码？',300,idname);
        var tmp_str='<div style="margin-left:30px;margin-right:20px;">'+
            '<table border="0">'+
            '<tbody>'+
            '<tr>'+
            '<td valign="top">'+
            '<a class="ok"></a>'+
            '</td>'+
            '<td valign="top">'+
            '<span style="font-size:13px;font-weight:bold;line-height:20px;">'+
            '请输入存档时所设置的密码：'+
            '<br/>'+
            '<span class="paperkeyicon"></span>'+
            '<input type="text" class="paperkeyinput" style="width:120px;border:1px solid #000;color:#f00;" maxlength="20"/>'+
            '<span style="display:inline-block;width:3px;"></span>'+
            '<div class="an01 bgbt" did="'+id+'" id="setpwdbtn" style="float:right; margin-top:6px; margin-right:10px"><span class="an_left"></span><a>确定</a><span class="an_right"></span></div>'+
            '</span>'+
            '</td>'+
            '</tr>'+
            '</tbody>'+
            '</table>'+
            '</div>';
        $('#'+idname+' .content').html(tmp_str);
        $.myDialog.tcShowDiv(idname);
        $('#div_shadow').css({'display':'block'});
    },
    
    delPaper : function(paperid,objID){
        $.ajax({
            type: "post",
            url: U('Home/Index/delSave'),
            data: { id: paperid,'times':Math.random() },
            success: function(data) {
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                $('#'+objID+' .tcClose').click();
                var dated = "";
                $("a.dated").each(function() {
                    if ($(this).hasClass("dated_current")) {
                        dated = $(this).attr("id");
                    }
                });
                var page = $("#curpage").html();
                $.DocSave.ajaxGetPaper(dated, page);
            },
            error: function() { }
        });
    },
    reloadPaper : function(paperid,paperkey){
        $.ajax({
            type: "post",
            url: U('Home/Index/isSavePwd'),
            data: {id:paperid,key:paperkey,'times':Math.random() },
            success: function(data) {
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                if (typeof (data['data']) == "string") {
                    if (data['data'].indexOf("@#@") >= 0) {
                        editData.clear();
                        editData.set('init',data['data']);
                        parent.$.myMain.basketInit();
                        window.location.href = U('Home/Index/zuJuan');
                    } else {
                        $.myDialog.showMsg("密码错误！",1);
                    }
                } else {
                        $.myDialog.showMsg("恢复失败！",1);
                }
            },
            error: function() { $.myDialog.showMsg("恢复失败！",1); }
        });
    }
}


$.Down = {
    area : 1,
    init : function(){
        var subjectID=Cookie.Get("SubjectId");
        $('#loca_text span').html($.myCommon.getSubjectNameFromParent());
        $.Down.pageInit();
         //上一页
        $.Down.toPrevPage(page, $('.prev_page'));
        //下一页
        $.Down.toNextPage(page, $('.next_page'));
        $.Down.downloadFile();
        $('#tmpdown').live('click',function(){
            $('#paperdowndiv .tcClose').click();
        });

        //展示三角形快速跳转分页
        $.myPage.showQuickSkip();
    },

    toPrevPage : function(page, obj, eventName){
        eventName = eventName || 'click';
        obj.live(eventName, function(){
            page=parseInt(page);
            if(page>1){
                $.Down.gotoPage(page-1);
            }else{
                $.Down.gotoPage(1);
            }
        });
    },

    toNextPage : function(page, obj, eventName){
        eventName = eventName || 'click';
        obj.live(eventName, function(){
            page=parseInt(page);
            if(page<parseInt($('#pagecount').html())){
                $.Down.gotoPage(page+1);
            }
            else{
                if(parseInt($('#pagecount').html())>1) $.Down.gotoPage(parseInt($('#pagecount').html()));
            }
        });
    },
    initDivHeight : function() {
        // $('#pagelistbox').css({'height':'20px'});
           var height = $(window).height()-$('#paperinfod').outerHeight()-$('#pagelistbox').outerHeight()-$('#righttop').outerHeight();
           height =height -13;

        $("#paperlistbox").css({ height: height ,'overflow-y':"auto"});
    },

    ajaxGetPaper : function(dated, page) {
        if (typeof dated == "undefined" || dated == null) { dated = $(".dated").get(0).id; }
        if (typeof page == "undefined" || page == null || page <= 1) { page = 1; }
        var subjectID = Cookie.Get("SubjectId");
        $.post(U('Home/Index/saveDown'),{datediff:dated,'subjectID':subjectID,page:page, area:$.Down.area ,m:Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            };
            var paperdata = data['data'][0];
            var papercount = data['data'][1];
            var percount = data['data'][2];
            var pagecount = Math.ceil(parseInt(data['data'][1])/parseInt(data['data'][2]));

            if (pagecount == 0) { pagecount = 1; }
            if (page <= 1) { page = 1; }
            if (page >= pagecount) { page = pagecount; }

            $("a.dated").removeClass("dated_current");
            $("#" + dated).addClass("dated_current");

            $("#pagecount").html(pagecount);
            $("#selectpageicon").css({ display: (pagecount <= 1) ? "none" : "inline-block" });
            $.Down.tableList(paperdata);
            $.myPage.showPage(papercount,percount,page,1);
        });
    },

    tableList : function(paperdata) {
        var html = [];
        html.push("<table border=\"0\" cellspacing=\"0\" class=\"table\" width=\"100%\">");
        html.push("<colgroup>" +
        "<col class=\"odd\"/>" +
        "<col class=\"even\"/>" +
        "<col class=\"odd\"/>" +
        "<col class=\"even\"/>" +
        "<col class=\"odd\"/>" +
        "<col class=\"even\"/>" +
        "<col class=\"odd\"/>" +
        "</colgroup>");
        html.push("<tr>" +
        "<th align=\"center\" width=\"30\">编号</th>" +
        "<th><span style=\"display:inline-block;height:23px;\">"+
        "<select id=\"area\">"+
        "<option value=\"1\""+($.Down.area==1 ? ' selected="selected" ' : '')+">当前题库</option>"+
        "<option value=\"0\""+($.Down.area==0 ? ' selected="selected" ' : '')+">全部题库</option>"+
        "</select>"+
        "</span></th>"+
        "<th align=\"left\">已下载试卷名称</th>" +
        "<th align=\"left\" width=\"30\">点值</th>" +
        "<th align=\"left\" width=\"100\">日期</th>" +
        "<th align=\"left\" width=\"100\">下载时IP</th>" +
        "<th align=\"left\" width=\"180\">操作</th>" +
        "</tr>");
        if(paperdata) {
            for (var i = 0, len = paperdata.length; i < len; i++) {
                var paper = paperdata[i];
                var id = paper.DownID;
                var papername = paper.DocName;
                var unixTimestamp = new Date(paper.LoadTime*1000); 
                var savetime = unixTimestamp.toLocaleString();
                var point = paper.Point;
                var ip = paper.IP;
                var subjectid = paper.SubjectID;

                html.push("<tr><td width=\"30\" align=\"center\">" + id + "</td><td>"+$.Down.getSubjectName(subjectid)+"</td><td class=\"paperName\">"  + papername + "</td><td>" + point + "点</td><td>" + savetime + "</td><td><a href='http://www.baidu.com/s?wd=" + ip + "' target='_blank'>" + ip + "</a></td>" +
                    "<td>"+
                    "<div class=\"an02 bgbt\" style=\"float:left; margin-top:6px; margin-right:10px\"><span class=\"an_left\"></span><a class=\"paperdown\" did=\"" + paper.DownID + "\" pid=\"" + paper.id + "\">下载存档</a><span class=\"an_right\"></span></div>"+
                    "<div class=\"an02 delPaper bgbt\" paperid=\"" + id + "\" style=\"float:right; margin-top:6px; \"><span class=\"an_left\"></span><a href=\"#\">删除</a><span class=\"an_right\"></span></div>"+
                    "</td></tr>");
            }
        }else{
             html.push('<tr><td colspan="7">抱歉！没有历史下载。</td></tr>');
        }
        html.push("</table>");
        $("#paperlistbox").html(html.join(""));
        $.Down.initDivHeight();
    },

    getSubjectName : function(id) {
        var subject = parent.subject;
        for(var i in subject){
            var quesbanks = subject[i].sub;
            for (var j = 0; j < quesbanks.length; j++) {
                   if(quesbanks[j]['SubjectID']==id) return  quesbanks[j].SubjectName ;
            }
        }
        return "";
    },
    
    bindEvent : function() {
        $("div .delPaper").live("click", function() {
            var paperid = $(this).attr("paperid");
            var paperName=$(this).parent().parent().children('.paperName').html();
            $.myDialog.normalMsgBox('delMySelfPaper','温馨提醒',450,'<div><b class="delThisPaper"  paperid="'+paperid+'">您确定删除【'+paperName+'】文档？</b></div>',3);
        });
        $('#delMySelfPaper .normal_yes').live('click',function(){
            var paperID=$('#delMySelfPaper .delThisPaper').attr('paperid');
            $.Down.delPaper(paperID,'delMySelfPaper');
        })

        $("a.dated").click(function() {
            page=1;
            var dated = $(this).attr("id");
            $.Down.ajaxGetPaper(dated);
        });

        $("#area").live('change',function() {
            page=1;
            var dated;
            $("a.dated").each(function(){
                if($(this).attr('class').indexOf('dated_current')!=-1)  dated = $(this).attr("id");
            });
            $.Down.area=$("#area").val();
            $.Down.ajaxGetPaper(dated);
        });
        //分页快速跳转
        $("#quicktopage a").live("click", function() {
            $.myPage.quickToPage($(this));
            $.Down.gotoPage(page);
        });
        
        $("#pagelistbox a").live("click",function(){
            $.Down.gotoPage($(this).attr('page'));
        });
    },
        
    delPaper : function(paperid,objID){
        $.ajax({
            type: "post",
            url: U('Home/Index/delDown'),
            data: { id: paperid,'times':Math.random() },
            success: function(data) {
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                var dated = "";
                $("a.dated").each(function() {
                    if ($(this).hasClass("dated_current")) {
                        dated = $(this).attr("id");
                    }
                });
                var page = $("#curpage").html();
                $('#'+objID+' .tcClose').click();
                $.Down.ajaxGetPaper(dated, page);
            },
            error: function() { }
        });
    },

    gotoPage : function(pagenum) {
        var dated = "";
        $("a.dated").each(function() {
            if ($(this).hasClass("dated_current")) {
                dated = $(this).attr("id");
            }
        });
        page=pagenum;
        $.Down.ajaxGetPaper(dated, pagenum);
    },

    pageInit : function() {
        $.Down.initDivHeight();
        $.Down.ajaxGetPaper();
        $.Down.bindEvent();
        $(window).resize(function() { $.Down.initDivHeight(); });
    },

    downloadFile : function(){
        //试卷存档下载
        $('.paperdown').live('click',function(){
            $.myDialog.showMsg('正在载入请稍候...');
            var _this=$(this);
            $.post(U('Home/Index/loadSaveWord'),{'DownID':$(this).attr('did'),'id':$(this).attr('pid'),'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                var tmp_str='<p align="center">'+_this.parent().parent().parent().find('td:eq(2)').html()+'</p><p align="center">请【<a href="'+data['data']+'" target="_blank" id="tmpdown">点击这里</a>】下载文档。</p><p></p>';
                $.myDialog.normalMsgBox('paperdowndiv','存档下载',450,tmp_str);
                
            });
        });
    }
}


jQuery.myUser = {
    pRetainTime:0, //手机验证时间
    eRetainTime:0, //邮箱验证时间
    sendTimeStr:'',//倒计时程序
    grade : 0,
    //初始化
    init:function(grade){
        this.initDivHeight();
        //this.showPower();
        this.reset();
        this.changeInfo();
        this.checkEmail();//验证邮箱事件
        this.checkPhone(); //验证手机事件
        this.editSubmit();//编辑后提交
        this.changeGrade();//年级切换

        this.grade = grade;
        $(window).resize(function() { $.myUser.initDivHeight(); });
    },
    initDivHeight:function() {
        var height = $(window).height()-$('#righttop').outerHeight()-10;
        $("#main").css({'height': height,'border':'1px solid #ccc','overflow-y':'auto','overflow-x':'hidden'});
    },
    dateDiff:function(sDate1, sDate2){
        var aDate, oDate1, oDate2, iDays;
        aDate = sDate1.split("-");
        oDate1 = new Date(aDate[1] + '/' + aDate[2] + '/' + aDate[0]);

        aDate = sDate2.split("-");
        oDate2 = new Date(aDate[1] + '/' + aDate[2] + '/' + aDate[0]);
        iDays = parseInt((oDate1 - oDate2) / 1000 / 60 / 60 / 24);
        return iDays;
    },
    /*
    //显示权限
    showPower:function(){
        $('.showPower').click(function(){
            $.myDialog.normalMsgBox('myPower','我的权限列表','450','正在加载...',0);
            var uid=Cookie.Get("user_UID");
            $.post(U('User/Home/userPower'),{'uid':uid},function(e){
                if($.myCommon.backLogin(e)==false){
                    return false;
                }
                if(e.status==1){
                    var powerHTML='';
                    if(e.data.UserPower=='all'){
                        powerHTML ='<div class="powerAll">您拥有系统所有权限，请保护好账户安全！</div>';
                    }else {
                        powerHTML='<ul style="padding:0;">';
                        $.each(e.data.UserPower, function (i, type) {
                            powerHTML += '<li class="powerTable"><span class="powerName">' + type.PowerName + '</span><span class="powerValue">' + type.Value + '</span></li>';
                        });
                        powerHTML+='</ul>';
                    }
                    $.myDialog.normalMsgBox('myPower','我的权限列表','450',powerHTML,0);
                }
            });
        })
    },
    */
    //重置按钮
    reset:function(){
        $("#reset").click(function() {
            $('#change').click();
        });
    },
    //修改信息
    changeInfo:function(){
        $('#change').click(function(){
        if($('#change').html().indexOf('取消修改')!=-1){
            $('#change').html('修改');
            $('.con').css({'display':'block'});
            $('.showinput').css({'display':'none'});
        }else{
            $('#change').html('取消修改');
            $('.showinput').css({'display':'block'});
            $('.con').css({'display':'none'});
            if($('.selectArea').length == 1) {
                $('#sf').areaSelectLoad('Home/Index', areaParent);
            }
        }
    });
        $('.selectArea').areaSelectChange('Home/Index',1);
    },
    //年级切换
    changeGrade:function(){
        $('#GradeID').live('change',function(){
            var tmp_id=$(this).val();
            $('#SubjectID').html('<option value="">请选择年级</option>');
            if(tmp_id=='' || typeof(tmp_id)=='undefined') return;
            $.myUser.loadSubject(1);
        });
    },
    //获取地区id
    getAreaID:function (){
        var tmp_id=$('.selectArea').last().find("option:selected").val();
        if($('.selectArea').last().find("option:selected").attr('iflast')==0){
            tmp_id=0;
        }
        return tmp_id;
    },
    //编辑后提交
    editSubmit:function(){
        $('#uniteditsubmit').click(function(){
            var err=0;
            $('.showinput').each(function(){
                $(this).find('span').last().html('');
            });
            //判断姓名
            var RealName=$('#RealName').val();
            if(RealName.length<1){
                $.myUser.showError('RealName','请填写正确的姓名!');
                err=1;
            }
            //判断密码
            var Passwordy=$('#Passwordy').val();
            var Password=$('#Password').val();
            var Password2=$('#Password2').val();
            if(Passwordy!='' && Passwordy.length<6){
                $.myUser.showError('Passwordy','请填写正确的原密码!');
                err=1;
            }
            if(Passwordy!='' && (Password.length<6 || Password.length>18)){
                $.myUser.showError('Password','请填写6-18位新密码!');
                err=1;
            }
            if(Passwordy!='' && Password!=Password2){
                $.myUser.showError('Password2','两次输入的新密码不一致!');
                err=1;
            }
            //判断地址
            var Address=$('#Address').val();
            if(Address.length<5){
                $.myUser.showError('Address','请输入正确的地址!');
                err=1;
            }
            //判断地区
            var AreaID=$.myUser.getAreaID();
            if(AreaID==0 || AreaID=='' || typeof(AreaID)=='undefined'){
                $.myUser.showError('AreaID','请选择所在地区!');
                err=1;
            }
            //判断学校
            var SchoolID=$('#school').val();
            if(SchoolID=='' || typeof(SchoolID)=='undefined'){
                $.myUser.showError('SchoolID','请选择所在学校!');
                err=1;
            }
            //判断年级
            var GradeID=$('#GradeID').val();
            if(GradeID=='' || typeof(GradeID)=='undefined'){
                $.myUser.showError('GradeID','请选择所在年级!');
                err=1;
            }
            //判断学科
            var SubjectID=$('#SubjectID').val();
            if(SubjectID=='' || typeof(SubjectID)=='undefined'){
                $.myUser.showError('SubjectID','请选择所在年级!');
                err=1;
            }
            if(err==1) return false;
            $.myDialog.normalMsgBox('msgdiv','保存信息',450,'正在保存请稍候...',1);
            $.post(U('User/Home/changeInfo'),{'RealName':RealName,'Password':Password,'Password2':Password2,'Passwordy':Passwordy,'Address':Address,'AreaID':AreaID,'SchoolID':SchoolID,'GradeID':GradeID,'SubjectID':SubjectID,'times':Math.random()},function(data){
                $('#msgdiv .tcClose').css({'display':'none'});
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                if(data['data']=='success'){
                    $.myDialog.normalMsgBox('msgdiv','完善个人信息',450,'修改成功!',1);
                    $('#msgdiv .normal_no').bind('click',function(){
                        location.reload();
                    });
                }else{
                    var tmp_arr=new Array();
                    tmp_arr=data['data'].split('#@#');
                    if(tmp_arr[0].indexOf('error')!=-1){
                        var tmp_arr2=new Array();
                        tmp_arr2=tmp_arr[1].split('#$#');
                        var tmp_arr3=new Array();
                        for(var ii=0;ii<tmp_arr2.length;ii++){
                            tmp_arr3=tmp_arr2[ii].split('|');
                            $.myUser.showError(tmp_arr3[0],tmp_arr3[1]);
                        }
                    }else{
                        $.myDialog.normalMsgBox('msgdiv','完善个人信息',450,'修改失败;'+data['data'],2);
                        if(data['data']=='请重新登录'){
                            $('#msgdiv .tcClose').css({'display':'none'});
                            $('#msgdiv .normal_no').bind('click',function(){
                                window.parent.location.href=U('/Home','',false);
                            });
                        }
                    }
                }
            });
        });
    },
    //显示错误信息
    showError:function(id,str){
        $('#'+id+'err').html('<font color="#ff0000">'+str+'</font>');
    },
    //载入学科
    loadSubject:function(type){
        $('#SubjectID').html('<option value="">请选择学科</option>');
        var gid=0;
        if(type==1){
            gid=$('#GradeID').val();
        }else{
            gid=$.myUser.grade;
        }
        if(typeof(gid)=='undefined' || gid=='' || gid==0){
            $('#SubjectID').html('<option value="">请选择年级</option>');
            return;
        }
        $.post(U('Home/Index/getData'),{'style':'getGradeSubject','gradeID':gid,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                output+='<option value="">'+data['data']+'</option>';
                if(output!='') $('#SubjectID').html(output);
                return false;
            };
            var sid= parent.subjectID;
            if(typeof(sid)=='undefined' || sid=='') sid=0;
            var output='';
            var j;
            var selected='';
            if(data['data']){
                for(var i in data['data']){
                    selected='';
                    if(sid==data['data'][i]['SubjectID']) selected=' selected="selected" ';
                    output+='<option value="'+data['data'][i]['SubjectID']+'" '+selected+'>'+data['data'][i]['SubjectName']+'</option>';
                }
            }
            if(output!='') $('#SubjectID').html(output);
        });
    },
    //验证手机事件
    checkPhone:function(){
        $('.checkPhone').live('click',function(){
            var phoneNum=$('#phoneCodeValue').text();
            var status=$(this).attr('status');
            $.myUser.alertCheckPhoneHtml(phoneNum,status);
            if($.myCommon.pRetainTime!=0){
                $.myCommon.showLeaveTime();
            }
        });

        //发送手机验证码
        $("#checkPhone .getPhoneRand").die().live('click',function(){
            //验证图形验证码
            var imgCode=$('#verifyCode').val();
            var phoneNum=$('#checkPhone .phoneNum').val();
            if(phoneNum==''){
                $('#checkPhone .phoneError').html('请填写手机号！');
                return false;
            }
            if($.myCommon.checkPhoneNum(phoneNum)==false){
                $('#checkPhone .phoneError').html('请填写正确的手机号！');
                return false;
            }
            if(imgCode.length!=4){
                $('#checkPhone .phoneError').html('请填写图片验证码！');
                $('#verifyCode').focus();
                return false;
            }
            $('#checkPhone .phoneError').html('');
            //发送手机短信验证码
            $.myCommon.sendPhoneCode(phoneNum,imgCode);
        });

        //提交验证码进行验证
        $('#checkPhone .normal_yes').live('click',function(){
            if(lock != ''){//检测AJAX请求是否被锁定
                return false;
            }
            var phoneNum=$('#checkPhone .phoneNum').val();
            var phoneCode=$('#phoneSaveCode').val();
            var phoneAttr=$('#phoneStatus').val();
//          if(phoneNum==''){
//              $('#checkPhone .phoneError').html('请填写手机号！');
//              return false;
//          }
//          if($.myCommon.checkPhoneNum(phoneNum)==false){
//              $('#checkPhone .phoneError').html('请填写正确的手机号！');
//              return false;
//          }
//          if(phoneCode==''){
//              $('#checkPhone .phoneError').html('请填写验证码！');
//              return false;
//          }
            $('#checkPhone .phoneError').html('');//清除错误信息
            lock = 'checkPhoneCode';//锁定请求
            //$.post(U('User/Home/checkPhoneCode'),{'phoneNum':phoneNum,'code':phoneCode,'phoneAttr':phoneAttr,'rand':Math.random()},function(e){
            $.post(U('User/Home/checkPhoneCode'),{'phoneNum':phoneNum,'phoneAttr':phoneAttr,'rand':Math.random()},function(e){
                lock = '';//解除请求锁定

                if(e.data=='success'){
                    if(phoneAttr == 'edit'){//修改已认证的手机
                        //清除倒计时
                        $.myCommon.clearLeaveTime();
                        $.myUser.alertCheckPhoneHtml('','set');
                        return false;
                    }
                    $('#checkPhone .tcClose').click();
                    $.myDialog.showMsg('验证成功！');
                    $('#phoneCodeValue').html(phoneNum);
                    $('.ifCheckPhone').html('<span>*已验证</span><span class="pointer blue checkPhone" status="edit">[修改]</span>');
                }else{
                    $('#checkPhone .phoneError').html(e.data);
                    return false;
                }
            })
        });
    },
    /**
     * 弹出认证手机号弹出框
     * 参数1 phoneNum string 手机号
     * 参数2 status string 认证状态
     * 作者 
     */
    alertCheckPhoneHtml:function(phoneNum,status){
        var title="认证";
        var ifWrite='';
        if(status=='set') {
            title="设置";
        }else if(status=='edit'){
            title="修改";
            ifWrite='disabled="true"　readOnly="true"';
        }
        var content = '<div class="phoneContent">' +
                      '    <p class="pc-item">' +
                      '        <span class="item-label">手机号：</span>' +
                      '        <input type="text" value="'+phoneNum+'" class="phoneNum" '+ifWrite+'>' +
                      '        <input type="hidden" value="'+status+'" id="phoneStatus">' +
                      '    </p>' +
                      '    <p class="pc-item">' +
                      '        <span class="item-label">图片验证码：</span>' +
                      '        <input type="text" name="verifyCode" id="verifyCode" size="4" maxlength="4"/>' +
                      '        <span>' +
                      '            <img width="95" height="36" id="verifyImg" src="'+U('Home/Index/verify')+'" url="'+U('Home/Index/verify')+'" BORDER="0" ALT="点击刷新验证码" style="cursor:pointer" align="absmiddle">' +
                      '        </span>' +
                      '    </p>'+
//                    '    <p class="pc-item">' +
//                    '        <span class="item-label">短信验证码：</span>' +
//                    '        <input type="text" name="phoneSaveCode" id="phoneSaveCode" size="6" maxlength="6"/>' +
//                    '        <input type="button" class="pointer send-code getPhoneRand" id="sendPhoneCode" value="获取短信验证码">' +
//                    '    </p>'+
                      '    <p class="pc-item f-yahei phoneError"></p>'
                      '</div>';
        $.myDialog.normalMsgBox('checkPhone',title+'手机',400,content,3);

    },
    /**
     * 弹出认证邮箱弹出框
     * 参数1 email string 邮箱
     * 参数2 status string 认证状态
     * 作者 
     */
    alertCheckEmailHtml:function(email,status){
        var title = '认证';
        var emailHtml = '<input type="text" value="'+email+'" name="email" class="email" />';
        if(status == 'set'){//需要输入邮箱
            title = "设置";
        }else if(status == 'edit'){//修改已认证的邮箱
            title = "修改";
            emailHtml = '<input type="text" value="'+email+'" name="email" disabled="true"　readOnly="true" class="email" />';
        }
        var content = '<div class="emailContent">' +
                      '    <p class="ec-item">' +
                      '        <span class="item-label">邮箱：</span>'+
                               emailHtml+
                      '        <input type="hidden" value="'+status+'" id="emailStatus">' +
                      '    </p>' +
//                    '    <p class="ec-item">' +
//                    '        <span class="item-label">验证码：</span>'+
//                    '        <input type="text" name="emailSaveCode" id="emailSaveCode" size="6" maxlength="6" class="emailCode"/>'+
//                    '        <input type="button" class="pointer send-code getEmailRand" value="获取验证码" />' +
//                    '    </p>'+
                      '    <p class="ec-item emailError"></p>' +
                      '</div>';
        $.myDialog.normalMsgBox('checkEmail',title+'邮箱',400,content,3);
    },
    /**
     * 显示倒计时程序
     * 作者 
     */
    showEmailTime:function(){
        var second=120;//发送时间间隔，默认为2分钟
        if($.myUser.eRetainTime!=0){second=$.myUser.eRetainTime;}
        $("#checkEmail .pointer").val('重新发送('+second+')');//改变发送按钮文字提示
        $("#checkEmail .pointer").removeClass('getEmailRand');//清除class类名，防止重复点击
        //倒计时程序
        $.myUser.sendTimeStr = setInterval(function () {
            second--;
            if (second <= 0) {

                $.myUser.clearLeaveTime();//清除倒计时
                return false;
            }
            if($('#div_shadowcheckEmail').length==0){
                $.myUser.eRetainTime=second;
            }
            $("#checkEmail .pointer").val('重新发送('+second+')');
        }, 1000);
    },
    /**
     * 清除倒计时
     * 作者 
     */
    clearLeaveTime:function(){
        $("#checkEmail .pointer").addClass('getEmailRand');
        $("#checkEmail .pointer").val('发送验证码');
        clearInterval($.myUser.sendTimeStr);
    },
    /**
     * 验证邮箱事件
     * 作者 
     */
    checkEmail:function(){
        var self=this;
        $('.checkEmail').live('click',function(){
            //邮箱验证分为三种情况
            //1、没有邮箱时
            //2、有邮箱但是未验证
            //3、已验证邮箱，但是想更换
            //第一和第二种情况下，邮箱输入框应该为可输入状态
            //第三种情况，更换邮箱应该先验证之前已认证过的邮箱，此步骤，邮箱号不可更改
            //对原来的邮箱验证通过后，出现新的邮箱验证窗口，邮箱号为可输入状态
            var status = $(this).attr('status');//邮箱认证状态
            var email = $('#emailValue').text();//邮箱号
            self.alertCheckEmailHtml(email,status);//弹出认证弹出框
            if($.myUser.eRetainTime!=0){//倒计时未清零时，继续之前的倒计时
                self.showEmailTime();
            }
        });
        //发送邮箱验证码
        $("#checkEmail .getEmailRand").die().live('click',function(){
            if(lock!=''){//请求是否被锁定
                return false;
            }
            var email=$('input[name="email"]').val();//邮箱号
            if(email==''){
                $('#checkEmail .emailError').html('邮箱不能为空！');
                return false;
            }
            if($.myCommon.checkEmail(email)==false){
                $('#checkEmail .emailError').html('请输入正确的邮箱！');
                return false;
            }
            lock = 'sendEmail';//锁定请求
            var sendLoad = layer.load();//等待提示
            //发送验证码
            $.post(U('User/Home/sendEmailCode'),{'email':email},function(e){
                layer.close(sendLoad);//关闭等待提示
                lock = '';//解除请求锁定
                //因为检查是否登录的backLogin函数会直接弹出错误提示
                //导致无法判断是否成功的信息，来确定倒计时是否停止并清零
                if(e.data=='success'){//如果发送成功,显示倒计时
                    self.showEmailTime();
                }else{
                    $("#checkEmail .pointer").addClass('getEmailRand');
                    $.myUser.clearLeaveTime();//清除倒计时
                }
                $('#checkEmail .emailError').html('');//清除错误信息
                if($.myCommon.backLogin(e)==false){
                    return false;
                }

            })
        });
        //提交验证码进行验证
        $('#checkEmail .normal_yes').live('click',function(){
            if(lock = ''){//检测AJAX请求是否被锁定
                return false;
            }
            var code=$('#emailSaveCode').val();
            var email=$('input[name="email"]').val();
            var emailAttr=$('#emailStatus').val();
//          if(email==''){
//              $('#checkEmail .emailError').html('请填写邮箱！');
//              return false;
//          }
//          if($.myCommon.checkEmail(email)==false){
//              $('#checkEmail .emailError').html('请输入正确的邮箱！');
//              return false;
//          }
//          if(code==''){
//              $('#checkEmail .emailError').html('请填写验证码！');
//              return false;
//          }
            lock = 'checkEmailCode';//锁定请求
            //$.post(U('User/Home/checkEmailCode'),{'email':email,'code':code,'emailAttr':emailAttr},function(e){
            $.post(U('User/Home/checkEmailCode'),{'email':email,'emailAttr':emailAttr},function(e){
                lock = '';//解除请求锁定
                if($.myCommon.backLogin(e)==false){
                    return false;
                }
                if(e['data']=='success'){
                    if(emailAttr=='edit'){
                        $.myUser.clearLeaveTime();//清除倒计时
                        self.alertCheckEmailHtml('','set');//弹出设置新邮箱的弹出框
                        return false;
                    }
                    $('#checkEmail .tcClose').click();
                    $.myDialog.showMsg('验证成功！');
                    $('#emailValue').html(email);
                    $('.ifCheckEmail').html('<span>*已认证</span><span class="pointer blue checkEmail" status="edit">[修改]</span>');
                }else{
                    $('#checkEmail .emailError').html(e.data);
                }
            })
        });
    }
}


$.UserMessage = {
    init : function(){
        this.initDivHeight();
        var subjectID=Cookie.Get("SubjectId");
        $('#loca_text span').html($.myCommon.getSubjectNameFromParent());
        this.pageInit();

        this.toPrevPage(page, $('.prev_page'));
        this.toNextPage(page, $('.next_page'));

        $.myPage.showQuickSkip();
    },

    toPrevPage : function(page, obj, eventName){
        eventName = eventName || 'click';
        obj.live(eventName, function(){
            page = parseInt(page);
            if(page > 1){
                $.UserMessage.gotoPage(page-1);
            }else{
                $.UserMessage.gotoPage(1);
            }
        });
    },

    toNextPage : function(page, obj, eventName){
        eventName = eventName || 'click';
        obj.live(eventName, function(){
            page=parseInt(page);
            if(page<parseInt($('#pagecount').html())){
                $.UserMessage.gotoPage(page+1);
            }else{
                if(parseInt($('#pagecount').html())>1) 
                    $.UserMessage.gotoPage(parseInt($('#pagecount').html()));
            }
        });
    },

    initDivHeight : function(){
        // $('#pagelistbox').css({'height':'20px'});
        var height = $(window).height()-$('#paperinfod').outerHeight()-$('#pagelistbox').outerHeight()-$('#righttop').outerHeight();
        height =height -13;
        $("#commentbox").css({ 'height': height ,'overflow-y':'auto','overflow-x':'hidden'});
    },

    ajax : function(userid, curpage, pagesize){
        if (userid == null || userid == undefined) { userid = 1; }
        if (curpage == null || curpage == undefined || curpage <= 0) { curpage = 1; }
        if (pagesize == null || pagesize == undefined || pagesize <= 0) { pagesize = 10; }
        var postdata = { userid: userid, curpage: curpage, pagesize: pagesize,times:Math.random() };
        $.ajax({
            type: "post",
            url: U('Home/Index/commentList'),
            data: postdata,
            success: function(data) {
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                if(data['data'][0] == null || data['data'][0] == undefined){
                    $("#commentbox").html('<div class="ly_list_box"><p class="list_ts">抱歉！暂时没有评论。</p></div>');
                }else{
                    $.UserMessage.commentList(data['data'][0]);
                    $.myPage.showPage(data['data'][1],data['data'][2],curpage,1);
                }
            },
            error: function() { }
        });
    },
    commentList : function(){
        var json = arguments[0];
        var html = [];
        if (json == null || json == undefined) {
            html.push("<div style='margin:5px 0;'>");
            html.push("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td style='padding:5px 0;'>暂时没有试题反馈。</td><td>（查看试题时，点击试题旁</td><td><a class='comment_icon'></a></td><td>&nbsp;图标即可评价试题。）</td></tr></table>");
            html.push("</div>");
            $("#commentbox").html(html.join(""));
            return;
        }
        var len = json.length;
        for (var i = 0; i < len; i++) {
            var d = json[i];
            var id = d.ID;
            var username = d.UserName;
            var quesscore = parseFloat(d.Score / 2).toFixed(1);
            if (quesscore == 0) { quesscore = "<span style=\"font-weight:bold;\">0</span>"; }
            if (quesscore == 5) { quesscore = "<span style=\"font-weight:bold;color:#f90;\">" + quesscore + "</span>"; }
            var userip = d.IP;
            userip = userip.substr(0, userip.lastIndexOf("."));
            if (userip.length > 0) {
                userip = userip + ".<span style=\"font-family:宋体;\">*</span>";
                userip = "（ip:" + userip + "）";
            }
            var unixTimestamp = new Date(d.LoadDate*1000); 
            var timon = unixTimestamp.toLocaleString();
            var comment = d.Content.replace(/</ig, "[").replace(/>/ig, "]");
            var quesid=d.TestID;
            var s = [];
            s.push("<div class=\"ly_list_box\">");
            s.push("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr>" +
            "<td style=\"color:#666;font-size:12px;\"><font style=\"color:#005ba2;font-size:14px\">" + username + "</font>&nbsp;" + userip +
            "<p style=\"padding:0px; line-height:24px; margin:0px;\">于" + timon + "给题号：<a href=\"javascript:void(0);\" class=\"ques\" id=\"ques" + quesid + "\">" + quesid + "</a>试题打<span style=\"font-size:11px;\">" + quesscore + "分</span></p></td>" +
            "<td align=\"right\">" +
            "</td></tr></table>");

            s.push("<div style=\"margin:2px 0;\">" + comment + "</div>");
            s.push("</div>");
            html.push(s.join(""));
        }
        $("#commentbox").html(html.join(""));
        this.initDivHeight();
    },
    bindEvent : function(){
        $("a.dated").click(function() {
            page=1;
            var userid = 0;
            if ($(this).attr('id') == 'mycomment') {
                userid = 1;
            }
            
            $("a.dated_current").removeClass("dated_current");
            $(this).addClass("dated_current");

            $.UserMessage.ajax(userid);
        });
        $("#pagelistbox a").live("click",function(){
            $.UserMessage.gotoPage($(this).attr('page'));
        });
        //展示三角形快速跳转分页
        $.myPage.showQuickSkip();
        //分页快速跳转
        $("#quicktopage a").live("click", function() {
            var page = $(this).text().replace("No.", "");
            $("#quicktopage").hide();
            $.UserMessage.gotoPage(page);
        });
        
        $("#refresh,#prevpage,#nextpage").click(function() {
            var userid = 0;
            var curpage = parseInt($("#curpage").html());
            var pagecount = parseInt($("#pagecount").html());
            var topage = 1;
            if ($("a.commentset").index($("a.commentset_cur")) == 0) {
                userid = 1;
            }

            if (this.id == "prevpage") { topage = curpage - 1; }
            if (this.id == "nextpage") { topage = curpage + 1; }
            if (topage <= 0 || topage > pagecount) { return; }

            $.UserMessage.ajax(userid, topage);
        });

        $("a.ques").live("click", function() {
            var quesid = $(this).attr('id').replace("ques", "");
            $.UserMessage.getQuesDetail(quesid);
        });
    },

    gotoPage : function(pagenum){
        var userid = 0;
        $("a.dated").each(function() {
             if ($(this).attr('id') == 'mycomment' && $(this).attr('class').indexOf('dated_current')!=-1) {
                userid = 1;
            }
        });
        page=pagenum;
        $.UserMessage.ajax(userid,page);
    },

    getQuesDetail : function(quesid){
        var idname='testdiv';
        var tmp_id=quesid;
        $.myDialog.tcLoadDiv('试题详细id='+tmp_id,600,idname);
        var tmp_str='试题加载中请稍候...';
        $('#'+idname+' .content').html(tmp_str);
        $.myDialog.tcShowDiv(idname);
        $('#div_shadow').css({'display':'block'});
        $.ajax({
            url: U('Home/Index/getDetailTestById'),
            type: "post",
            data: { id: tmp_id,'times':Math.random() },
            success: function(data) {
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                tmp_str='<div style="width:590px;height:330px;border:1px solid #039;overflow-x:hidden;overflow-y:scroll;position:relative;">'+
                            '<div>'+
                            '<div style="background:#efefef;border-bottom:1px solid #ccc;padding:5px;line-height:18px;">'+
                            '<div><b>题号：</b><span>'+data['data'][0][tmp_id]['testid']+'</span>，<b>题型：</b>'+data['data'][0][tmp_id]['typesname']+'，<b>难度：</b>'+data['data'][0][tmp_id]['diffname']+'，<b>日期：</b>'+data['data'][0][tmp_id]['firstloadtime']+'</div>'+
                            '<div><b>标题/来源：</b>'+data['data'][0][tmp_id]['docname']+'</div>'+
                            '</div>'+
                            '</div><div><div class="quesbody"><p>'+data['data'][0][tmp_id]['test']+'</div>'+
                            '<div class="quesanswer" style="display:block;"><p><font color="red">【答案】</font>'+data['data'][0][tmp_id]['answer']+'</div>'+
                            '<div class="quesparse" style="display:block;"><p><font color="red">【解析】</font>'+data['data'][0][tmp_id]['analytic']+'</div>'+
                            '</div>'+
                            '</div>';
                $('#'+idname+' .content').html(tmp_str);
                $.myDialog.tcDivPosition(idname);
            },
            error: function() { }
        });
    },

    pageInit : function() {
        this.ajax(0);
        this.bindEvent();
    }
}


var DirectoryManager = {
    addDir : function(){
        var tmpStr='<div class="addnewcata">'+
                    '<div class="tit">添加目录</div>'+
                    '<div class="con">'+ 
                    '<div class="condiv"><span class="l_tit">目录名称：</span><input id="catalogName" maxlength="10" name="catalogName" type="text" /></div>'+
                    '<div class="condiv"><span class="l_tit">父级目录：</span><select id="fatherID" name="fatherID">'+
                    '<option value="0">载入中请稍候...</option>'+
                    '</select></div>'+
                    '<div class="condiv"><span class="l_tit">排&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;序：'+
                    '</span><input id="orderID" name="OrderID" type="text" value="99" /></div>'+
                    '<div class="l_addcatalog"><span class="bgbt an01"><span class="an_left"></span><a>创建</a><span class="an_right"></span></span></div>'+
                    '<div class="des"><p>温馨提示:</p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;①不能在有试题的文件夹下创建新的文件夹；<br />'+
                    '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;②最多创建两级目录；<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;③排序数字越小越靠前；<br /></div>'+
                    '</div>'+
                    '</div>';
        $.myDialog.normalMsgBox('catalogdiv','添加目录',335,tmpStr);
        $.TestSave.getGrade('fatherID');
    },
    confirmAdd : function(){
        $.myDialog.normalMsgBox('msgdiv','提示信息',450,'正在提交请稍候...');
        var catalogName=$.trim($('#catalogName').val());
        var fatherID=$('#fatherID').val();
        var OrderID=$('#orderID').val();
        if(catalogName==''){
            $.myDialog.normalMsgBox('msgdiv','提示信息',450,'请填写目录名称！',2);
            return;
        }
        var output='';
        $.post(U('User/Home/saveCatalog'),{'catalogName':catalogName,'fatherID':fatherID,'OrderID':OrderID,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            };
            $('#catalogdiv .tcClose').click();
            var tmp_str=data['data'].split('|');
            $.myDialog.normalMsgBox('msgdiv','提示信息',450,'创建目录成功！',1);
            //载入目录
            if(fatherID==0){
                str='<li id="fa'+tmp_str[1]+'"><span></span><a href="#" class="zsd" id="cata'+tmp_str[1]+
                    '">'+tmp_str[2]+'</a></li>';
                $("#treecon").append(str);
                $('#treecon').tree({
                    expanded: 'li:first'
                });
            }else{
                var cid=$("#child"+fatherID).attr('role');
                if(cid==undefined){
                    str='<ul id="child'+fatherID+'"><li><span></span><a href="#" class="zsd" id="cata'+tmp_str[1]+
                        '">'+tmp_str[2]+'</a></li></ul>';
                    $('#fa'+fatherID).append(str);
                }else{
                    str='<li><span></span><a href="#" class="zsd" id="cata'+tmp_str[1]+
                        '">'+tmp_str[2]+'</a></li>';
                    $("#child"+fatherID).append(str);     
                }
                $('#treecon').tree({
                    expanded: 'li:first'
                });
            }
        });
    },

    removeDir : function(){
        var cid=$("[jl='down']").attr('id').replace('cata','');
        var cname=$("[jl='down']").html();
        if(cid=='all' || cid==0){
            $.myDialog.normalMsgBox('msgdivs','提示信息',450,'不能删除系统目录！',2);
            return;
        }
        if(cid==undefined){
            $.myDialog.normalMsgBox('msgdivs','提示信息',450,'请选定要删除的目录！',2);
            return;
        }
        if(cname==undefined){
            $.myDialog.normalMsgBox('msgdivs','提示信息',450,'请选定要删除的目录！',2);
            return;
        }
        var tmp_str='删除<span class="cataname">'+cname+'</span>，将删除该目录下的所有目录和收藏的试题！';
        $.myDialog.normalMsgBox('renamedivd','目录删除',510,tmp_str,3);
    },

    confirmDeletion : function(){
        $.myDialog.normalMsgBox('msgdivd','提示信息',450,'正在提交请稍候...');
        var rename_catalogid=$("[jl='down']").attr('id').replace('cata','');
        if(rename_catalogid==''){
            $.myDialog.normalMsgBox('msgdivd','提示信息',450,'获取数据失败！',2);
            return;
        }
        $.post(U('User/Home/delCatalogByID'),{"catalogID":rename_catalogid,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            };
            $('#msgdivd .tcClose').click();
            $.myDialog.normalMsgBox('renamedivd','目录删除',450,'删除成功！',1);
            var fid=$("[jl='down']").parent().parent().attr('id');
            $("[jl='down']").parent().remove();
            if($('#'+fid+' li').length==0){
                $('#'+fid).parent().children('span').attr('class','');
                $('#'+fid).remove();
            }
            $('#all').click();
            $('#treecon').tree();
        });
    },

    updateDirName : function(){
        var name=$("[jl='down']").html();
        var id=$("[jl='down']").attr('id').replace('cata','');
        if(id=='all' || id==0){
            $.myDialog.normalMsgBox('msgdivs','提示信息',450,'不能修改系统目录！',2);
            return;
        }
        var tmpStr='<div class="addnewcata">'+
        '<div class="con">'+ 
        '<div class="condiv"><span class="l_tit">目录名称：</span><input id="catalogName" maxlength="10" name="catalogName" value="'+name+'" type="text" /></div>'+
        '<div class="l_addcatalog"><span class="bgbt an01"><span class="an_left"></span><a>修改</a><span class="an_right"></span></span></div>'+
        '<input type="hidden" id="catalogid" name="catalogid" value="'+id+'" /></div>';
         $.myDialog.normalMsgBox('editcatalogdiv','修改目录',335,tmpStr);
    },

    confirmModifyOfName : function(){
        $.myDialog.normalMsgBox('msgdiv','提示信息',450,'正在提交请稍候...');
        var catalogName=$.trim($('#catalogName').val());
        var catalogID=$('#catalogid').val();
        if(catalogName==''){
            $.myDialog.normalMsgBox('msgdiv','提示信息',450,'目录名称不能为空！',2);
            return;
        }
        if(catalogName.indexOf('#$#')!=-1){
            $.myDialog.normalMsgBox('msgdiv','提示信息',450,'目录名称不能有特殊字符！',2);
            return;
        }
        var output='';
        $.post(U('User/Home/setCatalogName'),{'catalogName':catalogName,'catalogID':catalogID,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            };
            $('#editcatalogdiv .tcClose').click();
            var tmp_str=data['data'].split('#$#');
            $.myDialog.normalMsgBox('msgdiv','提示信息',450,'修改成功！',1);
            //载入新目录名称
            $('#cata'+catalogID).text(tmp_str[1]);
        });
    },

    showTestWithinDir : function(){
        $('.zsd').live('click', function(){
            $('.zsd').css('background-color','#3399ff');
            $('.zsd').attr('jl','');
            $(this).css('background-color','#3333ff');
            $(this).attr('jl','down');
            var cataName=$(this).html();
            var cata_str='<span>>'+cataName+'</span>';
            $('#categorylocation span').last().remove();
            $('#categorylocation').append(cata_str);
            $("#paperlistbox").html('<p class="list_ts"><span class="ico_dd">数据加载中请稍候...</span></div>');
            page=1;
            //显示试题
            var cataId=$(this).attr('id').replace('cata','');
            $.TestSave.ajaxGetPaper(cataId,page)//获取试题
        });
    }
}

$.TestSave = {
    init : function(){
        $(document).bind("selectstart",function(){return false;});
        this.initDivHeight();
        this.toPrevPage(page, $('.prev_page'));
        this.toNextPage(page, $('.next_page'));
        $(window).bind(
            "resize",
            function() {
                $.TestSave.initDivBoxHeight();
            }
        );
        var subjectID=Cookie.Get("SubjectId");
        $('#loca_text span').html($.myCommon.getSubjectNameFromParent());
        $("#paperlistbox").html('<p class="list_ts"><span class="ico_dd">数据加载中请稍候...</span></p>');//等待提示
        $.TestSave.pageInit();
        //展示三角形快速跳转分页
        $.myPage.showQuickSkip();
        $.TestSave.initDivBoxHeight();
        $('.ui-resizable-e').mousedown(function(e){
            var x = e.pageX;
            var z =$('#leftdiv').width();
            $(document).bind('mousemove', function(e){
                var c = parseInt(z) + (e.pageX - x);
                if(c<200) c=200;
                if(c>400) c=400;
                $('#leftdiv').css('width',c+"px");
                $('#rightdiv').css('left',c+"px");
                $("#rightdiv").width($(window).width()-$("#leftdiv").outerWidth());
            });
            $(document).mouseup(function(a) {
                $(document).unbind('mousemove');
            });
        });
        //20140918添加开始
        //添加目录，弹出框
        $('.addcatalog').live('click',function(){
            DirectoryManager.addDir();
        });
        //添加目录确定
        $('#catalogdiv .l_addcatalog').live('click',function(){
            DirectoryManager.confirmAdd();
        });
        //删除目录
        $('.deletecata').live('click',function(){
            DirectoryManager.removeDir();
        });
        //删除目录确定
        $('#renamedivd .normal_yes').live('click',function(){
            DirectoryManager.confirmDeletion();
        });
        //修改目录名称
        $('.editcatalog').live('click',function(){
            DirectoryManager.updateDirName();
        });
        //修改目录确定
        $('#editcatalogdiv .l_addcatalog').live('click',function(){
            DirectoryManager.confirmModifyOfName();
        });
        //按照目录查看试题
        DirectoryManager.showTestWithinDir();

        $('.tree span').live('click',function(){
            $('.zsd').css('background-color','#3399ff');
            $('.zsd').attr('jl','');
            $(this).next().css('background-color','#3333ff');
            $(this).next().attr('jl','down');
        });

        //2014年9月22日新加
        //收藏试题移动
        $.TestSave.moveCollectTest();
        $.TestSave.saveMovesCollectTest();

        $.myTest.showTestEvevt();
        $.myPage.showQuickSkip();
    },

    moveCollectTest : function(){
       $(".removepaper").live("click",function(){
            var paperid = $(this).parent().attr("paperid");
            //弹出框，移动至某个目录下
            var tmpStr='<div class="addnewcata">'+
                        '<div class="con">'+ 
                        '<div class="condiv"><span class="l_tit">移动至：</span><select id="fatherID" name="fatherID">'+
                        '<option value="0">载入中请稍候...</option>'+
                        '</select><input type="hidden" value="'+paperid+'" id="tmpID" /></div>'+
                        '<div class="l_addcatalog"><span class="bgbt an01"><span class="an_left"></span><a>移动</a><span class="an_right"></span></span></div>'+
                        '</div>'+
                        '</div>';
            $.myDialog.normalMsgBox('catalogdivr','移动目录',335,tmpStr);
            $.TestSave.getByTestID('fatherID',paperid);
        }); 
    },

    saveMovesCollectTest : function(){
        //试题收藏移动确定
        $('#catalogdivr .l_addcatalog').live('click',function(){
            var tmp_id=$('#tmpID').val();
            var cataid=$('#fatherID').val();
            $.myDialog.showMsg('正在移动请稍候...',0,0);
            $.ajax({
                type: "post",
                url: U('Home/Index/updateFavSave'),
                data: { id: tmp_id,catalogID:cataid,'times':Math.random()},
                success: function(msg) {
                    if($.myCommon.backLogin(msg)==false){
                        $('#catalogdivr .tcClose').click();
                        return false;
                    };
                    $('#catalogdivr .tcClose').click();
                    $.myDialog.showMsg('移动成功！');
                    var fid=$("[jl='down']").attr('id').replace('cata','');

                    if(fid!='all'){
                        $("a.zsd").each(function() {
                            if ($(this).hasClass("tree-item-active")) {
                                dated = $(this).attr("id").replace('cata','');
                            }
                        });
                        var page = $("#curpage").html();
                        $.TestSave.ajaxGetPaper(dated, page);
                    }
                },
                error: function() { $('#favdiv .tcClose').click(); $.myDialog.showMsg('移动失败',1);}            
            });
        });    
    },

    initDivHeight : function(){
        // $('#pagelistbox').css({'height':'20px'});
        var height = $(window).height()-$('#paperinfod').outerHeight()-$('#pagelistbox').outerHeight()-$('#righttop').outerHeight();
        height = height -13;
        $("#paperlistbox").css({ height: height });
    },

    initDivBoxHeight : function(){
        var a = $(window).width();
        var b = $(window).height();
        var c = 0;
        $(".mleft,.mright").height(b - c - 2);
        $("#rightdiv").width(a-$("#leftdiv").outerWidth());
        $("#leftdiv").height(b - c-2);
        $("#rightdiv").height(b - c-2);
        $("#categorytreebox").height(b - c - $('#categorytop').outerHeight());
    },

    toPrevPage : function(page, obj, eventName){
        eventName = eventName || 'click';
        obj.live(eventName, function(){
            page = parseInt(page);
            if(page > 1){
                $.TestSave.gotoPage(page-1);
            }else{
                $.TestSave.gotoPage(1);
            }
        });
    },

    toNextPage : function(page, obj, eventName){
        eventName = eventName || 'click';
        obj.live(eventName, function(){
            page=parseInt(page);
            if(page<parseInt($('#pagecount').html())){
                $.TestSave.gotoPage(page+1);
            }else{
                if(parseInt($('#pagecount').html())>1) 
                    $.TestSave.gotoPage(parseInt($('#pagecount').html()));
            }
        });
    },

    catalogList : function(){
        var str='';
        if(typeof(fid)=='undefined' || fid=='') fid=0;
        //显示目录
        var subjectID = Cookie.Get("SubjectId");
        $.post(U('User/Home/userCatalog'),{'fid':fid,'subjectID':subjectID,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            };
            //显示相应id的目录
            str=' <li><span></span><a href="#" class="zsd" id="all" jl="down">全部</a></li><li><span></span><a href="#" class="zsd" id="0">未分类</a></li>';
            for(var i=0;i<data['data'][0].length;i++){
                str+='<li id="fa'+data['data'][0][i]['CatalogID']+'"><span></span><a href="#" class="zsd" id="cata'+data['data'][0][i]['CatalogID']+'">'+data['data'][0][i]['CatalogName']+'</a>';
                if(data['data'][0][i]['child']){
                    str +='<ul id="child'+data['data'][0][i]['CatalogID']+'">';
                    for(var j=0;j<data['data'][0][i]['child'].length;j++){
                        str+='<li><span></span><a href="#" class="zsd" id="cata'+data['data'][0][i]['child'][j]['CatalogID']+'">'+data['data'][0][i]['child'][j]['CatalogName']+'</a>';
                        str+='</li>';
                    }
                    str +='</ul>';
                }
                str+='</li>';
            }
            $('#treecon').html(str);
            var cataName=$('#all').html();
            var cata_str='<span>>'+cataName+'</span>';
            $('#categorylocation').append(cata_str);
            $('#all').addClass('tree-item-active');
            $('#all').css('background-color','#3399ff');
            $('#treecon').tree({
                expanded: 'li:first'
            });
        });
    },
    getGrade : function(str, fid){
        if(typeof(fid)=='undefined' || fid=='') fid=0;
        $.post(U('User/Home/getUserCatalog'),{'fid':fid,'times':Math.random()},function(data){
            var output='';
            var j;
            var selected='';
            if($.myCommon.backLogin(data)==false){
                 output+='<option value="0">顶级目录...</option>';
                 $('#'+str).html(output);
                return false;
            };
            output+='<option value="0">顶级目录...</option>';
            for(var i in data['data']){
                if(fid==data['data'][i]['catalogID']) selected=' selected="selected" ';
                output+='<option value="'+data['data'][i]['CatalogID']+'" '+selected+'>'+data['data'][i]['CatalogName']+'</option>';
            }
            $('#'+str).html(output);
        });
    },

    ajaxGetPaper : function(cataId, page){
        if (typeof cataId == "undefined" || cataId == null) { cataId = 'all'; }//默认加载全部
        if (typeof page == "undefined" || page == null || page <= 1) { page = 1; }
        var subjectID = Cookie.Get("SubjectId");
        $.post(U('Home/Index/testFav'),{cataID:cataId, 'subjectID':subjectID,page:page ,m:Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            };
            if(data.data){
                var paperdata = data['data'][0];
                var papercount = data['data'][1];
                var percount = data['data'][2];
                var pagecount = Math.ceil(parseInt(data['data'][1])/parseInt(data['data'][2]));

                if(papercount==0){
                    $("#paperlistbox").html('<p class="list_ts tips2">暂时没有收藏。</p>');
                    return false;
                }

                if (pagecount == 0) { pagecount = 1; }
                if (page <= 1) { page = 1; }
                if (page >= pagecount) { page = pagecount; }
                $("#pagecount").html(pagecount);
                $.TestSave.tableList(paperdata);
                $.myPage.showPage(papercount,percount,page,1);
            }else{
                $.myPage.showPage(0,1,page,1)
                $("#paperlistbox").html('<p class="list_ts tips2">暂时没有收藏。</p>');
            }
        });
    },

    tableList : function(paperdata) {
        var html = [];
        if(paperdata) {
            html.push($.myTest.showTest(paperdata));
        }else{
            html.push("<div style='padding:10px;'>暂无试题收藏记录！</div>");
        }
        $("#paperlistbox").html(html.join(""));
        $('.fav').css('display','none');
        $('.quesdiv').each(function(){
            if($(this).find('.quesbody').height()<100){
                $(this).find('.quesbody').height(100);
            }
            var test_str='<div class="an_del_box" paperid="'+$(this).attr('id').replace('quesdiv','')+'">'+
                         '<div class="an02 bgbt delpaper" style="margin-top:6px; margin-right:10px"><span class="an_left"></span>'+
                         '<a href="#">取消收藏</a><span class="an_right"></span></div>'+
                         '<div class="an02 bgbt removepaper" style="margin-top:6px; margin-right:10px"><span class="an_left"></span>'+
                         '<a href="#">移动试题</a><span class="an_right"></span></div></div>';
            $(this).append(test_str);
        });
        this.initDivHeight();
    },

    bindEvent : function() {
        $(".delpaper").live("click", function() {
            var paperid = $(this).parent().attr("paperid");
            $.TestSave.delPaper(paperid);
        });
        $("#pagebox").hover(function() {
            var page = parseInt($("#pagebox").text())
            var pagecount = parseInt($("#pagecount").text());
            if (pagecount <= 1) { return; }

            html = [];
            html.push("<div id=\"quicktopage\" style=\"top:" + ($(this).height() - 1) + "px;\">");
            var i; var max = 20;
            var spacing = (pagecount > max) ? parseInt(pagecount / max) : 1;
            for (i = 1; i <= pagecount; i += spacing) {
                html.push("<a class=\"" + ((page == i) ? "current" : "") + "\">No." + i + "</a>");
            }
            if (i - spacing < pagecount) {
                html.push("<a class=\"" + ((page == pagecount) ? "current" : "") + "\">No." + pagecount + "</a>");
            }
            html.push("</div>");
            $(this).append(html.join(""));
        }, function() {
            $("#quicktopage").empty().remove();
        });

        $("#pagebox #quicktopage a").live("click", function() {
            var page = $(this).text().replace("No.", "");
            $("#quicktopage").hide();
            $.TestSave.gotoPage(page);
        });
        
        $("#pagelistbox a").live("click",function(){
            $.TestSave.gotoPage($(this).attr('page'));
        });
    },
    delPaper : function(paperid){
        $.ajax({
            type: "post",
            url: U('Home/Index/delFavSave'),
            data: { id: paperid,'times':Math.random() },
            success: function(data) {
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                if (data['data'].toLowerCase() == "true") {
                    $.myDialog.showMsg('删除收藏成功！');
                    var dated = "";
                    $("a.zsd").each(function() {
                        if ($(this).hasClass("tree-item-active")) {
                            dated = $(this).attr("id").replace('cata','');
                        }
                    });
                    var page = $("#curpage").html();
                    $.TestSave.ajaxGetPaper(dated, page);
                } else {
                   $.myDialog.showMsg(data['data'],1);
                }
            },
            error: function() { $.myDialog.showMsg('删除收藏失败',1); }
        });
    },
    gotoPage : function(pagenum){
        var cataID = "";
        $("a.zsd").each(function() {
            if ($(this).hasClass("tree-item-active")) {
                cataID = $(this).attr("id").replace('cata','');
            }
        });
        page=pagenum;
        $.TestSave.ajaxGetPaper(cataID, pagenum);
    },

    getByTestID : function(str,id){
        if(typeof(id)=='undefined'||id=='') return;
        $.post(U('Home/Index/getCataByTestID'),{'id':id,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            };
            var cid=data['data'][0]['CatalogID'];
            $.TestSave.getCata(str,cid);
        });
    },

    getCata : function(str,cid,fid){
        if(typeof(fid)=='undefined' || fid=='') fid=0;
        //显示目录
        var subjectID = Cookie.Get("SubjectId");
        $.post(U('User/Home/userCatalog'),{'fid':fid,'subjectID':subjectID,'times':Math.random()},function(data){
            var output='';
            if($.myCommon.backLogin(data)==false){
                output +='<option value="0">未分类</option>';
                $('#'+str).html(output);
                return false;
            };
            for(var i=0;i<data['data'][0].length;i++){
                if(data['data'][0][i]['CatalogID']==cid)continue;
                if(data['data'][0][i]['child']){
                    output +='<optgroup label="'+data['data'][0][i]['CatalogName']+'"></optgroup>';
                    for(var j=0;j<data['data'][0][i]['child'].length;j++){
                        if(data['data'][0][i]['child'][j]['CatalogID']==cid)continue;
                        output +='<option value="'+data['data'][0][i]['child'][j]['CatalogID']+'">&nbsp;&nbsp;'+data['data'][0][i]['child'][j]['CatalogName']+'</option>';
                    }
                }else{
                    if(data['data'][0][i]['CatalogID']==cid)continue;
                    output +='<option value="'+data['data'][0][i]['CatalogID']+'">'+data['data'][0][i]['CatalogName']+'</option>'
                }
            }
            if(output==''){
                output = '<option value="0">未分类</option>';
            }
            $('#'+str).html(output);
        });
    },

    pageInit : function(){
        this.initDivHeight();
        this.catalogList();
        this.ajaxGetPaper();
        this.bindEvent();
        $(window).resize(function() { $.TestSave.initDivHeight(); });
    }
}