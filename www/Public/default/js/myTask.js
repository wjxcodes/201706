$.MyTask = {
    init : function(){
        var subjectID=Cookie.Get("SubjectId");
        // $('#loca_text span').html($.myCommon.getSubjectNameFromParent());
        $.MyTask.pageInit();
        //上一页
        $.MyTask.toPrevPage(page, $('.prev_page'));
        //下一页
        $.MyTask.toNextPage(page, $('.next_page'));

        $.myPage.showQuickSkip();

        $.myCommon.addDateControl();

        $('.doTask').live('click', function(){
            if(top.window){
                window.location.href = $(this).attr('href');
            }
            return false;
        })
    },

    toPrevPage : function(page, obj, eventName){
        eventName = eventName || 'click';
        obj.live(eventName, function(){
            var page=parseInt(page);
            if(page>1){
                $.MyTask.gotoPage(page-1);
            }else{
                $.MyTask.gotoPage(1);
            }
        });
    },

    toNextPage : function(page, obj, eventName){
        eventName = eventName || 'click';
        obj.live(eventName, function(){
            var page=parseInt(page);
            if(page<parseInt($('#pagecount').html())){
                $.MyTask.gotoPage(page+1);
            }
            else{
                if(parseInt($('#pagecount').html())>1) $.MyTask.gotoPage(parseInt($('#pagecount').html()));
            }
        });
    },

    pageInit : function() {
        $.MyTask.initDivHeight();
        $.MyTask.ajaxGetPaper();
        $.MyTask.bindEvent();
        $(window).resize(function() { $.MyTask.initDivHeight(); });
    },

    gotoPage : function(pagenum) {
        $.MyTask.ajaxGetPaper(pagenum);
    },

    initDivHeight : function() {
        // $('#pagelistbox').css({'height':'20px'});
        var height = $(window).height()-$('#paperinfod').outerHeight()-$('#pagelistbox').outerHeight()-$('#righttop').outerHeight();
        height =height -13;
        $("#paperlistbox").css({ height: height ,'overflow':'auto'});
    },

    ajaxGetPaper : function(page) {
        page = page || 1;
        var subjectid = Cookie.Get("SubjectId");
        if(subjectid=='' || subjectid=='undefined' || subjectid==null){
            $.myDialog.showMsg('请选择科学!',1);
            return false;
        }
        var obj = $('#sc_sx');
        var startTime = obj.find("input[name='startTime']").val();
        var endTime = obj.find("input[name='endTime']").val();
        var level = obj.find("select[name='level']").val();
        var status = obj.find("select[name='status']").val();
        var data = {
            'p':page,
            'startTime' : startTime,
            'endTime' : endTime,
            'level' : level,
            'status' : status,
            m:Math.random()
        };
        $.myDialog.normalMsgBox('test','提示',250,'加载中...');
        $.post(U('User/Home/myTasksList'),data,function(data){
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
            $("#pagecount").html(pagecount);
            $("#selectpageicon").css({display: (pagecount <= 1) ? "none" : "inline-block" });
            $.MyTask.tableList(paperdata);
            $.myPage.showPage(papercount,percount,page,1);
            $('.tcClose').trigger('click');
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
        "<th align=\"left\" >任务名称</th>" +
        "<th align=\"left\" width=\"160\">任务状态</th>" +
        "<th align=\"left\" width=\"190\">领取时间</th>" +
        "</tr>");
        if(paperdata) {
            for (var i = 0, len = paperdata.length; i < len; i++) {
                var paper = paperdata[i];
                var id = paper.MHLID;
                var title = paper.Title;
                var unixTimestamp = new Date(paper.recordAddTime*1000); 
                var savetime = unixTimestamp.toLocaleString();
                var status = parseInt(paper.Status);
                if(1 == status){
                    if(paper.Url.indexOf('/Teacher') === 0){
                        paper.Url = paper.Url.substr(1,paper.Url.length);
                        paper.Url = U('Teacher/Index/index?u=_'+paper.Url.replace('/','_'));
                        title += '&nbsp;&nbsp;[<a href="'+paper.Url+'" target="_blank">现在做任务</a>]';
                    }else{
                        title += '&nbsp;&nbsp;[<a href="'+paper.Url+'" class="doTask">现在做任务</a>]';
                    }
                }
                var info = '';
                switch(status){
                    case 0 :
                        info = '申请中';
                    break;
                    case 1 :
                    info = '已领取';
                    break;
                    case 2 :
                        info = '后台终止';
                    break;
                    case 3 :
                        info = '拒绝';
                    break;
                    case 4 :
                        info = '<font color="red">完成</font>';
                }

                html.push("<tr><td width=\"30\" align=\"center\">" + id +
                        "</td><td class=\"paperName\">" + title + 
                        "</td><td class=\"paperName\">" + info + 
                        "</td><td style=\"font-size:12px; color:#999999\">" + savetime + 
                        "</td>");
            }
        }else{
            html.push("<tr><td colspan=\"4\" align=\"center\">暂无信息</td></tr>");
        }
        html.push("</table>");
        $("#paperlistbox").html(html.join(""));
        $.MyTask.initDivHeight();
    },

    
    bindEvent : function() {
        $("div.reload").live("click", function() {
            var id = $(this).attr("paperid");
            var key = $(this).attr("paperkey");
            if(key!=''){
                $.MyTask.setPaperPwd(id);
                return;
            }
            $.MyTask.reloadPaper(id,key);
        });
        $("div.delpaper").live("click", function() {
            var paperid = $(this).attr("paperid");
            var paperName=$(this).parent().parent().children('.paperName').html();
            $.myDialog.normalMsgBox('delSelfPaper','温馨提醒',450,'<div><b class="delThisPaper"  paperid="'+paperid+'">您确定删除【'+paperName+'】文档？</b></div>',3);
        });
        $('#delSelfPaper .normal_yes').live('click',function(){
            var paperID=$('#delSelfPaper .delThisPaper').attr('paperid');
            $.MyTask.delPaper(paperID,'delSelfPaper');
        })

        $("a.dated").click(function() {
            page=1;
            var dated = $(this).attr("id");
            $.MyTask.ajaxGetPaper(dated);
        });

        //展示三角形快速跳转分页
        $.myPage.showQuickSkip();
        //分页快速跳转
        $("#quicktopage a").live("click", function() {
            var page = $(this).text().replace("No.", "");
            $("#quicktopage").hide();
            $.MyTask.gotoPage(page);
        });
        
        $("#pagelistbox a").live("click",function(){
            $.MyTask.gotoPage($(this).attr('page'));
        });
        
        $('#setpwdbtn').live('click',function(){
            var key=$('.paperkeyinput').val();
            if(key.length<1){
                $.myDialog.showMsg('请输入密码！',1);
                $('.paperkeyinput').focus();
                return;
            }
            var id=$(this).attr('did');
            $.MyTask.reloadPaper(id,key);
        });
        $('#search').click(function(){
            $.MyTask.ajaxGetPaper(1);
        });
    }
}