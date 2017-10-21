//js  U方法生成访问路径
function U(models,vars,suffix){
    if(typeof(suffix)!='boolean') suffix=true;
    if(typeof(vars)=='undefined') vars='';
    if(typeof(local)=='undefined') local='/';

    var depr='/';
    var lastName='.html';
    var leftstr=local;
    var wenhao='?'; //结尾数据

    //去掉开头的斜杠
    var tmp=models.substr(0,1);
    if(tmp==depr){
        models=models.substr(1,models.length);
        leftstr='/';
    }

    //处理了连接中的数据到数组
    var tmpVar,tmpStr1,tmpStr2,tmpStr3,tmpStr4;

    if(vars!='' && typeof(vars)=='object'){
        tmpVar=new Array();
        var j=0;
        for(var i in vars){
            tmpVar[j]=i+'='+vars[i];
            j++;
        }
        vars=tmpVar.join('&');
    }

    tmpStr1=models.split(depr);
    if(tmpStr1[tmpStr1.length-1].indexOf('?')!=-1){
        tmpStr2=tmpStr1[tmpStr1.length-1].split('?');
        tmpStr1[tmpStr1.length-1]=tmpStr2[0];
        if(vars!='') vars=tmpStr2[1]+'&'+vars;
        else vars=tmpStr2[1];
    }

    //处理vars
    if(vars!=''){
        tmpStr2=new Array();
        tmpStr3=vars.split('&');
        if(tmpStr3.length==1 && tmpStr3[0].indexOf('=')==-1){
            wenhao+=tmpStr3[0];
        }else{
            for(var i=0;i<tmpStr3.length;i++){
                tmpStr4=tmpStr3[i].split('=');
                tmpStr2[i*2]=tmpStr4[0];
                tmpStr2[i*2+1]=tmpStr4[1];
            }
        }
    }

    //合并数组tmpstr1 tmpstr2
    var myUrl='';
    if(tmpStr1){
        if(leftstr.indexOf('Index')!=-1 && tmpStr1[0]!='Index' || tmpStr1.length==3){
            leftstr='/';
        }

        leftstr+=tmpStr1.join(depr);
        if(vars!='') leftstr+=depr+tmpStr2.join(depr);
        myUrl=leftstr;
    }

    //添加后缀
    if(suffix==true){
        if(myUrl.substr(-1,1)!='/' && myUrl.indexOf(lastName)==-1){
            myUrl+=lastName;
        }
    }

    if(wenhao!='?'){
        myUrl+=wenhao;
    }

    return myUrl;
}

//通用
jQuery.myCommon = {
    pRetainTime:0,
    sendTimeStr:'',
    //url路径 以横线间隔
    changeUrl:function(url){
        var tmpArr=url.split('/');
        var len=tmpArr.length;
        switch(len){
            case 3:
                var output=new Array();
                for(var i=1;i<len-1;i++){
                    output[i]=tmpArr[i];
                }
                return '/';
                break;
            case 4:
                var output=new Array();
                for(var i=1;i<len-2;i++){
                    output[i]=tmpArr[i];
                }
                return output.join('/')+'/';
                break;
        }
        return url;
    },
    // 页面加载中
    loadingShow:function(context){
        var lorder = $("#loadTips");
        if(lorder){
            var mainContent = $('body');
            var loaderLay = '<div id="loadTips" class="context-loader-container">' +
                '    <div class="context-loader large-format-loader">' +
                '        <p><img alt="" height="64" src="/Public/default/image/publ/gear-lg.gif" width="64"></p>' +
                '        <p class="loadTipsContext">'+context+'</p>' +
                '    </div>' +
                '</div>';
            mainContent.append(loaderLay);
        }else{
            $('#loadTips').show();
        }
    },
    loadingHide:function(){
        var loader = $('#loadTips');
        if(loader.length>0){
            $('#loadTips').hide();
        }
    },
    //数据载入中
    loading:function(){
        return '<p class="list_ts"><span class="ico_dd">数据加载中请稍候...</span></p>';
    },
    //ajax加载中提示
    ajaxLoading:function(){
        if(lock!=''){
            var str='数据加载中请稍候...';
            $.myDialog.showMsg(str,1,1);
            return false;
        }
        return true;
    },
    //跳转
    go:function(str){
        if($('#basket').length>0) $.myMain.basketInit(); //调取设置试卷中心，试卷试题数量信息函数.(解决cookie存在，试题数量相关信息不存在问题)
        if($('#iframe').length>0){
            $('#iframe').attr('src',str);
        }else if($('#iframe',window.parent.document).length>0){
            $('#iframe',window.parent.document).attr('src',str);
        }
    },
    //验证码
    verifyImg:function(){
        //重载验证码
        var timenow = new Date().getTime();
        var src = U('Home/Index/verify?time='+timenow);
        $('#verifyImg').attr('src',src);
        $("#verifyCode").val("");
    },
    //验证登录状态
    backLogin:function(data){
        if(typeof(data.status)=='undefined' &&  (typeof data=='string') && data.constructor==String){
            if($('#verifyImg').length>0) $.myCommon.verifyImg();
            $.myDialog.normalMsgBox('msgdiv','错误提示',500,data,2);
            if($(document).height()>$('#div_shadowmsgdiv').height()){
                $('#msgdiv').css({'max-height':$('#div_shadowmsgdiv').height()});
                var height=$('#div_shadowmsgdiv').height()-120;
                $('.normal_msg').eq(0).css({'height':height});
                $('.normal_msg').eq(0).css({'overflow-y':'auto'});
            }
            return false;
        }
        if(typeof(data.status)!='undefined' && data.status!=1){
            var msg='';
            var url='';
            if(typeof(data['data']['url'])=='undefined' || data['data']['url']==''){
                msg=data['data'];
            }else{
                msg=data['data']['data'];
                url=data['data']['url'];
            }
            if(url){
                msg+='<input class="jumpUrl" name="jumpUrl" type="hidden" value="'+url+'" />';
                $.myDialog.normalMsgBox('jumpdiv','错误提示',500,msg,2);
            }else{
                $.myDialog.normalMsgBox('errordiv','错误提示',500,msg,2);
            }
            return false;
        }
        return true;
    },
    //检测学科是否存在  如果不存在则跳转到首页
    checkSubject:function(jump){
        if(typeof(subjectID)=='undefined' || subjectID==null || isNaN(subjectID) || subjectID==0){
            if(typeof(jump)=='undefined' || jump=='') return false;
            else{
                parent.location.href=jump;
            }
        }
        return true;
    },
    //过滤html标签及@$危险字符
    removeHTML:function(str){
        str = str.replace(/<\/?[^>]*>|\@|\$/g,"");
        return str;
    },
    //过滤html标签
    dangerHTML:function(str){
        str = str.replace(/<a.*?>|<\/a>|<table>|<\/table>|<iframe>|<\/iframe>|<object>|<\/object>|<scrip.*?>|<\/script>/gi,"");
        return str;
    },
    //截取字符串(包括中文）
    cutString:function (str,len){
        var strlen = 0;
        var s = "";
        for(var i = 0;i < str.length;i++){
            if(str.charCodeAt(i) > 128){
                strlen += 2;
            }else{
                strlen++;
            }
            s += str.charAt(i);
            if(strlen >= len){
                return s ;
            }
        }
        return s;
    },

    //选择学科对话框
    chooseSubject:function (){
        if($('#chooseSubject').length==0 && $('#chooseSubject',window.parent.document).length==0){
            if(typeof(subject)=='undefined' || subject==''){
                var subject=parent.subject;
            }
            var output='<div id="quesbanklist_inner"><div class="edu">';
            for(i in subject){
                output+='<div class="eduname">'+subject[i]['FullName']+'</div><div class="banks">';
                for(j=0;j<subject[i]['sub'].length;j++){
                    output+='<a class="bank" sid="'+subject[i]['sub'][j]['SubjectID']+'" title="点击换到 '+subject[i]['sub'][j]['SubjectName']+'">'+subject[i]['sub'][j]['SubjectName']+'</a>';

                }
                output+='</div>';
            }
            output+='</div>';
            $.myDialog.normalMsgBox('chooseSubject','请选择学科',280,output,5);
        }
    },
    //处理JSON数据 设置章节option
    setChapterOption:function(data,id){
        var str='';
        var i,j;
        for(i in data){
            if(data[i]['Last']==1)
                str+='<option value="c'+data[i]['ChapterID']+'" '+(data[i]['ChapterID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['ChapterName']+'</option>';
            else
                str+='<option value="'+data[i]['ChapterID']+'" '+(data[i]['ChapterID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['ChapterName']+'</option>';
            if(data[i]['sub'])
                for(j=0;j<data[i]['sub'].length;j++){
                    if(data[i]['Last']==1)
                        str+='<option value="c'+data[i]['sub'][j]['ChapterID']+'" '+(data[i]['sub'][j]['ChapterID']==$id ? "selected=\"selected\"" : "") +'>　　'+data[i]['sub'][j]['ChapterName']+'</option>';
                    else
                        str+='<option value="'+data[i]['sub'][j]['ChapterID']+'" '+(data[i]['sub'][j]['ChapterID']==$id ? "selected=\"selected\"" : "") +'>　　'+data[i]['sub'][j]['ChapterName']+'</option>';
                }
        }
        return str;
    },

    //处理JSON数据 设置知识点option
    setKnowledgeOption:function(data,id){
        var str='';
        var i,j;
        for(i in data){
            str+='<option value="'+data[i]['KlID']+'" '+(data[i]['KlID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['KlName']+'</option>';
            if(data[i]['sub'])
                for(j=0;j<data[i].length;j++){
                    str+='<option value="'+data[i][j]['KlID']+'" '+(data[i][j]['KlID']==id ? "selected=\"selected\"" : "") +'>　　'+data[i][j]['KlName']+'</option>';
                }
        }
        return str;
    },

    //加载视频
    loadVideoFrame:function(){
        $('.videolist').live('click',function(){
            if($.myCommon.ajaxLoading()==false){
                return false;
            }

            var idName='videodiv';
            var title=$(this).attr('tname');
            var kid=$(this).attr('kid');
            var tid=$(this).attr('tid');

            $.myDialog.tcLoadDiv(title,640,idName);
            
            $('#'+idName+' .content').html('<iframe src="'+U('Home/Index/video?kid='+kid+'&tid='+tid)+'" allowfullscreen="" width="640" height="480" frameborder="0"></iframe>');
            $('#'+idName+' .content').css({"padding":"0px","background":"none"});
            $.myDialog.tcShowDiv(idName);
            $('#div_shadow'+idName).css({'display':'block'});
            $('#tsid .tcClose').live('click',function(){$.myDialog.tcCloseDiv(idName)});
//            $.post(U('Home/Index/video?kid='+kid+'&tid='+tid),{'times':Math.random()},function(data){
//                if($.myCommon.backLogin(data)==false){
//                    $('#'+idName+' .content').html('<div>加载失败</div>');
//                    lock=''; //解锁
//                    return false;
//                }
//                
//                $('#'+idName+' .content').html(data['data']);
//                $.myDialog.tcShowDiv(idName);
//                $('#div_shadow'+idName).css({'display':'block'});
//                $('#tsid .tcClose').live('click',function(){$.myDialog.tcCloseDiv(idName)});
//                lock=''; //解锁
//            });
            return false;
        });
    },
    //子框架获取学科名称
    getSubjectNameFromParent:function(){
        if($('#cursubject .bank_current',window.parent.document).length>0) return $('#cursubject .bank_current',window.parent.document).text()
        else return $('#cursubject',window.parent.document).html();
    },
    //字符串截取
    cutString:function(str,len,lastStr){
        if(typeof(lastStr)=='undefined') lastStr='';
        var newLength = 0;
        var newStr = "";
        var chineseRegex = /[^\x00-\xff]/g;
        var singleChar = "";
        var strLength = str.replace(chineseRegex,"**").length;
        for(var i = 0;i < strLength;i++)
        {
            singleChar = str.charAt(i).toString();
            if(singleChar.match(chineseRegex) != null)
            {
                newLength += 2;
            }
            else
            {
                newLength++;
            }
            if(newLength > len)
            {
                break;
            }
            newStr += singleChar;
        }

        if(lastStr!='' && strLength > len)
        {
            newStr += lastStr;
        }
        return newStr;
    },
    //载入日期控件
    addDateControl:function(){
        // 加载js文件
        var urls = new Array();
        urls.push("Public/plugin/laydate/laydate.js");
        load_script(urls);
    },
    //发送手机验证码
    sendPhoneCode:function(phoneNum,imgCode){
        if(lock != ''){
            return false;
        }
        lock = 'sendPhoneCode';
        var sendLoad = layer.load();//等待提示
        $.post(U('User/Home/sendPhoneCode'),{'phoneNum':phoneNum,'imgCode':imgCode,'rand':Math.random()},function(e){
            lock = '';
            layer.close(sendLoad);//关闭等待提示
            $('#checkPhone .phoneError').html('');
            if(e.data=='success'){
                $.myCommon.showLeaveTime();
            }else{
                $.myDialog.normalMsgBox('msgdiv','完善个人信息',450,e.data,1);
                return false;
            }
        });
    },
    //显示倒计时
    showLeaveTime:function(){
        var second=60;
        if($.myCommon.pRetainTime!=0){second=$.myCommon.pRetainTime;}
        $("#sendPhoneCode").val('重新发送('+second+')');
        $(".pointer").removeClass('getPhoneRand');

        $.myCommon.sendTimeStr = setInterval(function () {
            second--;
            if (second <= 0) {
                $.myCommon.clearLeaveTime();
                return false;
            }
            if($('#div_shadowcheckPhone').length==0){
                $.myCommon.pRetainTime=second;
            }
            $("#sendPhoneCode").val('重新发送('+second+')');
        }, 1000);
    },
    //清除倒计时程序
    clearLeaveTime:function(){
        $.myCommon.pRetainTime=0;
        $(".pointer").addClass('getPhoneRand');
        $(".pointer").val('获取短信验证码');
        clearInterval($.myCommon.sendTimeStr);
    },
    //验证手机号
    checkPhoneNum:function(phoneNum){
        if(phoneNum.length < 11){
            return false;
        }
        var phoneReg=/^0?(13[0-9]|15[012356789]|17[067]|18[0-9]|14[57])[0-9]{8}$/;
        if(!phoneReg.test(phoneNum)){
            return false;
        }
        return true;
    },
    //验证邮箱好
    checkEmail:function(email){
        var emailReg = /^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;
        if(!emailReg.test(email)){
            return false;
        }
        return true;
    },
    //根据弹出框ID，自动计算弹出框的大小
    resetBoxMsg:function(idName,setWidth){
        var windHeight=$(window).height();
        var windWidth=$(window).width();
        var boxContentHeight=$('#'+idName+' .normal_msg').height();
        var boxContentWidth=$('#'+idName+' .normal_msg').width();
        if(typeof(setWidth)=='undefined'){   //没有设置宽度
            var changeWidth=(windWidth>boxContentWidth)? (windWidth-80) :boxContentWidth;
            $('#'+idName).width(changeWidth);
        }
        if(boxContentHeight<windHeight){
            $('#'+idName+' .normal_msg').height(boxContentHeight-80);
        }
        $.myDialog.tcDivPosition(idName);
    }
}
//分页
jQuery.myPage = {
    //清除分页内容
    clearPage:function(){
        $("#pagelistbox").html('');
        $("#quescount").html('?');
        $("#curpage").html('?');
        $("#selectpageicon").css({display: "none" });
        $("#pagecount").html('?');
    },
    /**
     * 展示分页
     * @param total int 总数量
     * @param prePage int 每页显示数量
     * @param page int 当前页码
     * @param style int 样式，0一行，1上下两行分页
     */
    showPage:function(total,prePage,page,style) {
        $.myPage.clearPage();
        var pageNum = Math.floor((page - 1) / 10);//页码基数
        var pageCount = Math.ceil(total / prePage);//总页数
        var lastPageList = '<div class="pagebox">';
        if (page == 1) {
            lastPageList += '<span class="disabled">首页</span>';
        } else {
            lastPageList += '<a page="1" href="javascript:void(0);">首页</a>';
        }
        if (page > 10) {
            lastPageList += '<a page="' + (pageNum * 10) + '" href="javascript:void(0);">上十页</a>';
        }
        for (var i = pageNum * 10 + 1; i <= (pageNum + 1) * 10 && i <= pageCount; i++) {
            if (i != page) {
                lastPageList += '<a page="' + i + '" href="javascript:void(0);">' + i + '</a>';
            } else {
                lastPageList += '<span class="current">' + i + '</span>';
            }
        }
        if (pageCount - (pageNum + 1) * 10 > 0) {
            lastPageList += '<a page="' + ((pageNum + 1) * 10 + 1) + '"href="javascript:void(0);">下十页</a>';
        }
        if (page < pageCount) {
            lastPageList += '<a page="' + pageCount + '" href="javascript:void(0);" title="最后1页">末页</a>';
        } else {
            lastPageList += '<span class="disabled">末页</span></div>';
        }
        $("#pagelistbox").html(lastPageList);
        if (parseInt(style)==1) {
            $("#quescount").html(total);
            $("#curpage").html(page);
            $("#selectpageicon").css({display: (pageCount <= 1) ? "none" : "inline-block"});
            $("#pagecount").html(pageCount);
        }
    },
    //展示三角形快速跳转分页
    showQuickSkip:function(){
        $("#selectpageicon").live('mouseenter',function() {
            var page = parseInt($("#curpage").text());
            var pageCount = parseInt($("#pagecount").text());
            if (pageCount <= 1) { return; }

            var html = [];
            html.push("<div id=\"quicktopage\" style=\"top:" + ($(this).height() - 1) + "px;\">");
            var i; var max = 20;
            var spacing = (pageCount > max) ? parseInt(pageCount / max) : 1;
            for (i = 1; i <= pageCount; i += spacing) {
                html.push("<a class=\"" + ((page == i) ? "current" : "") + "\">No." + i + "</a>");
            }
            if (i - spacing < pageCount) {
                html.push("<a class=\"" + ((page == pageCount) ? "current" : "") + "\">No." + pageCount + "</a>");
            }
            html.push("</div>");
            $(this).append(html.join(""));
        }).live('mouseleave',function() {
            $("#quicktopage").empty().remove();
        });
    },
    //快速分页改变样式和页码
    quickToPage:function(obj){
        $('#quicktopage a').removeClass('current');
        obj.addClass('current');
        var nowpage=obj.html().replace('No.','');
        $("#quicktopage").empty().remove();
        page=parseInt(nowpage);
    },
    //上一页
    goPrevPage:function(){
        if(parseInt($('#pagecount').text())<2) return false;
        page=parseInt(page);
        if(page>1){
            page=page-1;
        }else{
            page=1;
        }
    },
    //下一页
    goNextPage:function(){
        if(parseInt($('#pagecount').text())<2) return false;
        page=parseInt(page);
        if(page<parseInt($('#pagecount').html())){
            page=page+1;
        }
        else{
            if(parseInt($('#pagecount').html())>1) page=parseInt($('#pagecount').html());
        }
    }
}
//对话框
jQuery.myDialog = {
    //载入层
    tcLoadDiv:function(str,width,id){
        var zIndex=999;
        if(0==$('#div_shadow'+id).length){
            $('body').append('<div id="div_shadow'+id+'" class="div_shadow"></div>');
            if($('.div_shadow').length>0) zIndex+=$('.div_shadow').length*2;
            $('#div_shadow'+id).css({
                "position":"absolute",
                "-ms-filter":"progid:DXImageTransform.Microsoft.Alpha(Opacity=30)",
                "filter":"alpha(opacity=30)",
                "opacity":".3",
                "z-index":zIndex,
                "background":"#000000"
            });
        }else{
            $('#div_shadow'+id).css({'display':'block'});
        }

        if(0==$('#'+id).length){
            var tmp_strd='<div id="'+id+'" class="dialog">';
            if(str){
                tmp_strd+='<div class="bar" onselectstart="return false;" oncopy="return false;">'+
                    '<span class="title" onselectstart="return false;">'+
                    '<b>'+(str.length>40 ? str.substr(0,40) : str)+'</b>'+
                    '</span>'+
                    '<a class="tcClose" did="'+id+'">×关闭</a>'+
                    '</div>';
            }
            tmp_strd+='<div class="content"></div></div>';
            $('body').append(tmp_strd);
            zIndex++;

            $('#'+id).css({
                "opacity":0,
                "z-index":zIndex,
                "position":"absolute"
            });
        }else{
            $('#'+id).css({'display':'block'});
            if(str) $('#'+id+' .title b').html(str);
        }
        $('#'+id).css({
            "width":width+"px"
        });
    },
    //显示层
    tcShowDiv:function(idName){
        $.myDialog.tcDivPosition(idName);//层位置
        $('#'+idName).css('display','block');
        $('#'+idName).animate({'opacity':1},300,function(){
            $('#'+idName).css({'display':'block','opacity':1});
        });
    },
    //关闭层
    tcCloseDiv:function (id){
        $('#'+id).animate({opacity: '0'}, 300,function(){
            if($('#'+id).find('iframe').length>0) $('#'+id).remove();
            $('#'+id).remove();
            $('#div_shadow').remove();
            $('#div_shadow'+id).remove();
        });
    },
    //层位置
    tcDivPosition:function(id){
        if($('#'+id).length>0){
            $('#div_shadow'+id).css({
                "top":$(document).scrollTop(),
                "left":$(document).scrollLeft(),
                "width":$(window).width(),
                "height":$(window).height()
            });
            var theight=$(document).scrollTop()+($(window).height()-$('#'+id).height())/2.3;
            $('#'+id).css({
                "top": theight>0 ? theight : 0,
                "left":$(document).scrollLeft()+($(window).width()-$('#'+id).width())/2
            });
        }
    },
    /**
     * 信息提示框 可填3个参数
     * 参数1 需要提示的内容
     * 参数2 背景切换 1显示错误图标 0显示正确图标
     * 参数3 关闭时间 为-1不关闭 默认1秒
     */
    showMsg:function(g,e){
        var idName='msgdiv';
        var time=arguments[2]?arguments[2]:1; //可以设置第三个参数 过多久后自动关闭

        //载入层
        $.myDialog.tcLoadDiv('',(g.length*30)+30,idName);
        var position='';
        if(e==1) position=' style="background-position:-35px 0px;" ';
        var tmp_str='<div style="height:60px;"><span class="msgleft"'+position+'></span><span style="font-family:\'微软雅黑\',\'黑体\';font-size:18px;font-weight:bold;color:#555;line-height:60px;">'+g+'</span></div>';
        $('#'+idName+' .content').html(tmp_str);
        $.myDialog.tcDivPosition(idName);//层位置
        $('#'+idName).css({'display':'block','opacity':1});

        if(time!=-1){
            $('#'+idName).animate({'opacity':0.8},time*1000,function(){
                $('#'+idName).remove();
            });
            $('#div_shadow'+idName).remove();
        }else{
            $('#div_shadow'+idName).css({'display':'block'});
        }
    },
    /**
     * 载入通用提示框
     * 参数1 id
     * 参数2 标题
     * 参数3 长度
     * 参数4 内容
     * 参数5 标志  1成功按钮 2失败按钮  3选择按钮  4确定按钮  5无按钮
     */
    normalMsgBox:function(idName,title,length,tmpStr,flag,bt1,bt2){
        if(typeof(flag)=='undefined') flag=0;
        if(typeof(title)=='undefined') title='提示信息';
        var button1='确定';
        var button2='取消';
        if(typeof(bt1)!='undefined' && bt1!='') button1=bt1;
        if(typeof(bt2)!='undefined' && bt2!='') button2=bt2;

        if(flag==1){//success
            tmpStr='<div class="normal_msg"><div class="normal_info">'+tmpStr+'</div><div class="mormal_success"></div></div>'+
                '<div class="normal_btn">'+
                '<span class="normal_no bgbt an01" idName="'+idName+'"><span class="an_left"></span><a>'+button1+'</a><span class="an_right"></span></span>'+
                '</div>';
        }else if(flag==2){ //error
            tmpStr='<div class="normal_msg"><div class="normal_info">'+tmpStr+'</div><div class="mormal_error"></div></div>'+
                '<div class="normal_btn">'+
                '<span class="normal_no bgbt an01" idName="'+idName+'"><span class="an_left"></span><a>'+button1+'</a><span class="an_right"></span></span>'+
                '</div>';
        }else if(flag==3){ //选择
            tmpStr='<div class="normal_msg">'+tmpStr+'</div>'+
                '<div class="normal_btn">'+
                '<span class="normal_yes bgbt an01" only="1"><span class="an_left"></span><a>'+button1+'</a><span class="an_right"></span></span>'+
                '<span class="normal_no bgbt an02" idName="'+idName+'"><span class="an_left"></span><a>'+button2+'</a><span class="an_right"></span></span>'+
                '</div>';
        }else if(flag==4){ //确定
            tmpStr='<div class="normal_msg">'+tmpStr+'</div>'+
                '<div class="normal_btn">'+
                '<span class="normal_no bgbt an01" idName="'+idName+'"><span class="an_left"></span><a>'+button1+'</a><span class="an_right"></span></span>'+
                '</div>';
        }else if(flag==5){ //无按钮
            tmpStr='<div class="normal_msg">'+tmpStr+'</div>';
        }
        $.myDialog.tcLoadDiv(title,length,idName);
        $('#'+idName+' .content').html(tmpStr);
        $.myDialog.tcShowDiv(idName);
        $('#div_shadow'+idName).css({'display':'block'});
    },
    //载入在提示框中载入数据
    normalAddCon:function(idName,tmpStr){
        $('#'+idName+' .content').html(tmpStr);
        $.myDialog.tcDivPosition(idName);//层位置
    }
}

//按钮鼠标事件
$(document).on('mouseenter','.bgbt',function(){
    if($(this).attr('class').indexOf('an01')!=-1) $(this).addClass('an02').removeClass('an01');
    else $(this).addClass('an01').removeClass('an02');
}).on('mouseleave','.bgbt',function(){
    if($(this).attr('class').indexOf('an01')!=-1) $(this).addClass('an02').removeClass('an01');
    else $(this).addClass('an01').removeClass('an02');
});

//关闭通用提示框
$(document).on('click','.normal_no',function(event){
    $.myDialog.tcCloseDiv($(this).attr('idName'));
    event.stopPropagation();
});

//关闭弹出框事件
$(document).on('click','.tcClose',function(event){
    $.myDialog.tcCloseDiv($(this).attr('did'));
    event.stopPropagation();
});

//窗口改变大小事件
$(window).resize(function(){
    //弹出层根据窗口变化
    $('.dialog').each(function(){
        $.myDialog.tcDivPosition($(this).attr('id'));
    });
});

//验证码刷新
$('#verifyImg').live('click',function(){
    $.myCommon.verifyImg();
});

//弹出框移动
$(document).on("mousedown",'.bar',function(e){
    var X,Y;
    var hX,hY;
    var maxScrollX,maxScrollY;
    var minScrollX,minScrollY;
    var tmove = false;

    var _this=$(this).parent();
    X=e.pageX;
    Y=e.pageY;
    hX=parseInt(_this.css('left').replace('px'));
    hY=parseInt(_this.css('top').replace('px'));
    maxScrollX=parseInt($(window).width())-_this.width();
    maxScrollY=parseInt($(window).height())-_this.height();
    $("body").bind("mousemove",function(e){
        var left=e.pageX-X+hX<0?0:(e.pageX-X+hX>=maxScrollX?maxScrollX:e.pageX-X+hX);
        var top=e.pageY-Y+hY<0?0:(e.pageY-Y+hY>=maxScrollY?maxScrollY:e.pageY-Y+hY);
        if(top<0) top=0;
        _this.css('left',parseInt(left)+'px');
        _this.css('top',parseInt(top)+'px');
    }).bind("mouseup",function(){
        $(this).unbind("mousemove");
        $(this).unbind("mouseout");
        $(this).unbind("mouseup");
    }).bind("mouseleave",function(){
        $(this).unbind("mousemove");
        $(this).unbind("mouseout");
        $(this).unbind("mouseup");
    });
});

//错误跳转
$('#jumpdiv .normal_no').live('click',function(){
    if($('#jumpdiv .jumpUrl').length>0) parent.location.href=$('#jumpdiv .jumpUrl').val();
});

//初始化ajax全局信息
$.ajaxSetup({
    cache:false,
    async: true,
    timeout:29000,//29秒超时
    error:function(XMLHttpRequest, textStatus, errorThrown){
        var errstr='';
        var status=XMLHttpRequest.status;
        if(status==0 && textStatus=='error') return false;
        switch (status){
            case(500):
                errstr="服务器系统内部错误";
                break;
            case(401):
                errstr="访问被拒绝";
                break;
            case(403):
                errstr="无权限执行此操作";
                break;
            case(404):
                errstr="未找到请求地址";
                break;
            case(408):
                errstr="请求超时";
                break;
            case(0):
                errstr="请求超时";
                break;
            default:
                errstr="网络问题，访问被中断:"+status;
        }
        var second = 5;
        var errorStr = errstr+'<p>当前页面会在<span class="spanSeconds">'+second+'</span>秒后自动刷新</p>';
        //jInfo(errorStr,'网络问题');
        $.myDialog.normalMsgBox('网络问题',errstr,500,errorStr,2);
        var error = setInterval(function () {
            second--;
            if (second <= 0) {
                clearInterval(error);
                window.location.reload();
            }
            $('.spanSeconds').html(second);
        }, 1000);
    }
});

//插件
$.fn.extend({
    //回车事件绑定点击
    clickByEnter:function(){
        var enter=this;
        $(document).keypress(function(e) {
            if(e.which == 13) {
                enter.click();
            }
        });
    },

    //input radio 选项变色 格式如关键字选题
    radioColor:function() {
        var radio=this;
        //检查需要变色的选项
        radio.find('label input:checked').next('span.pointer').css({'color':'#00A0E9'});

        //点击变色
        radio.find('label').click(function(){
            radio.find('label span.pointer').css({'color':'#000'});
            radio.find('input').attr('checked',false);
            $(this).find('span.pointer').css({'color':'#00A0E9'});
            $(this).find('input').attr('checked','checked');
        });
    },
    //a 排序 选项变色 改属性
        aOrderColor:function() {
            var tmp_str=$(this).attr('type');
            if($(this).attr('class').indexOf('button_current')!=-1){
                if(tmp_str.indexOf('down')!=-1) {
                    $(this).attr('type',tmp_str.replace('down','up'));
                    $(this).find('b').css({'background-position':'-7px 0px'});
                }
                else{
                    $(this).attr('type',tmp_str.replace('up','down'));
                    $(this).find('b').css({'background-position':'0px 0px'});
                }
            }else{
                $(this).parent().children('.button_current').addClass('button').removeClass('button_current');
                $(this).addClass('button_current');
            }
    },
    //分类查找试题 背景变色
    setButtonBackground:function(){
        page=1;
        $(this).parent().children('.button_current').addClass('button').removeClass('button_current');
        $(this).addClass('button_current');
    },
    //获取选中值 attr需要获取的属性
    getButtonSelected:function(attr){
        var output=0;
        $(this).children('a').each(function(){
            if($(this).attr('class').indexOf('button_current')!=-1){
                output=$(this).attr(attr);
                return true;
            }
        });
        return output;
    },
    //获取选中值 radio的值
    getRadioChecked:function(){
        var output=0;
        $(this).find('input').each(function(){
            if($(this).attr('checked')=='checked'){
                output=$(this).val();
                return true;
            }
        });
        return output;
    },
    //防止重复点击
    checkClick:function(){
        if($(this).attr('ifclick')==1){
            return false;
        }
        $(this).attr('ifclick',1);
        return true;
    },
    /**
     * 知识点change事件
     * $param string url 调取数据路径
     * author  
     */
    knowledgeSelectChange:function(url){
        $(this).live('change',function(){
            var _this=$(this);
            if(_this.find('option:selected').attr('last')==1){
                return ;
            }
            _this.nextAll("select").remove();
            var values=_this.find('option:selected').val();
            if(values=='') return;
            var param={'style':'knowledge','pID':values};
            $.post((url+'/getData'),param,function(msg){
                //权限验证
                if($.myCommon.backLogin(msg)==false){
                    return false;
                }
                var data=msg['data'];
                if(data){
                    var output='';
                    output+=setOption(data,0,'knowledge');
                    _this.after('<select class="selectKnowledge" name="KlID[]">'+output+'</select>');
                }
            })
        })
    }
});

//cookie
(function() {
    window.Cookie = (function() {
        return {
            Set: function(a, b, c) {
                var d = new Date();
                d.setTime(d.getTime() + c * 24 * 60 * 60 * 1000);
                document.cookie = a + "=" + encodeURIComponent(b) + ";expires=" + d.toGMTString() + ";path=/"
            },
            Get: function(a) {
                var c=document.cookie.split(a+'=');
                if(c.length>1){
                    var d=c[c.length-1].split(';');
                    if(d.length>1){
                        return decodeURIComponent(d[0])
                    }else{
                        return decodeURIComponent(c[c.length-1])
                    }
                }else{
                    return null
                }

                //var b = document.cookie.match(new RegExp("(^| )" + a + "=([^;]*)(;|$)"));
                //if(a=='paperstyle') alert(b.length);
                //if (b != null) {
                //    return decodeURIComponent(b[2])
                //} else {
                //    return null
                //}
            },
            Del: function(a) {
                var b = new Date();
                b.setTime(b.getTime() - 100000);
                var c = this.Get(a);
                if (c != null) {
                    document.cookie = a + "=;expires=" + b.toGMTString() + ";path=/"
                }
            },
            Has: function(name){
                var ck=document.cookie.indexOf(name+"=");
                if(ck==-1) {
                    return false;
                }
                return true;
            }
        }
    })();
})();

//本地数据存储
var localData = {
    hname:location.hostname?location.hostname:'localStatus',
    isLocalStorage : window.localStorage ? true : false,
    dataDom:null,
    initDom:function(){ //初始化userData
        if(!this.dataDom){
            try{
                this.dataDom = document.createElement('input');//这里使用hidden的input元素
                this.dataDom.type = 'hidden';
                this.dataDom.style.display = "none";
                this.dataDom.addBehavior('#default#userData');//这是userData的语法
                document.body.appendChild(this.dataDom);
                var exDate = new Date();
                exDate = exDate.getDate()+30;
                this.dataDom.expires = exDate.toUTCString();//设定过期时间
            }catch(ex){
                return false;
            }
        }
        return true;
    },
    set:function(key,value){
        if(this.isLocalStorage){
            window.localStorage.setItem(key,value);
        }else
        if(this.initDom()){
            this.dataDom.load(this.hname);
            this.dataDom.setAttribute(key,value);
            this.dataDom.save(this.hname)
        }else{
            Cookie.Set(key,value,7);
        }
    },
    get:function(key){
        if(this.isLocalStorage){
            return window.localStorage.getItem(key);
        }else
        if(this.initDom()){
            this.dataDom.load(this.hname);
            return this.dataDom.getAttribute(key);
        }else{
            return Cookie.Get(key);
        }
    },
    remove:function(key){
        if(this.isLocalStorage){
            window.localStorage.removeItem(key);
        }else
        if(this.initDom()){
            this.dataDom.load(this.hname);
            this.dataDom.removeAttribute(key);
            this.dataDom.save(this.hname)
        }else{
            Cookie.Del(key);
        }
    }
};

//用于任务大厅的cookie清除  2015-12-10
var mode = Cookie.Get('repeatTask');
if(mode){
    window.onbeforeunload = function(){
        Cookie.Del('repeatTask');
        $.get(U('MissionHall/clearSession'),function(){});
    }
}

//定义全局变量 获取相对路径
window['localhostPath'] = this.location.protocol + "//" + this.location.host + "/";
//加载JS ----urls可以是以逗号‘,’分割的字符串，也可以是数组
function load_script(urls) {
    //判断是否为数组
    if (!(urls instanceof Array ||
        (!(urls instanceof Object) && (Object.prototype.toString.call((urls)) == '[object Array]') ||
            typeof urls.length == 'number' && typeof urls.splice != 'undefined' &&
                typeof urls.propertyIsEnumerable != 'undefined' && !urls.propertyIsEnumerable('splice')))) {
        urls = urls.split(',');
    }
    //var localhostPath = this.location.protocol + "//" + this.location.host;
    var script;
    for (var i = 0; i < urls.length; i++) {
        script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = localhostPath + urls[i];
        document.getElementsByTagName('head')[0].appendChild(script);
    }
}
//加载css样式 ----urls可以是以逗号‘,’分割的字符串，也可以是数组
function load_css(srcs) {
    var css;
    //判断是否为数组
    if (!(srcs instanceof Array ||
        (!(srcs instanceof Object) && (Object.prototype.toString.call((srcs)) == '[object Array]') ||
            typeof srcs.length == 'number' && typeof srcs.splice != 'undefined' &&
                typeof srcs.propertyIsEnumerable != 'undefined' && !srcs.propertyIsEnumerable('splice')))) {
        srcs = srcs.split(',');
    }
    for (var i = 0; i < srcs.length; i++) {
        css = document.createElement("link");
        css.rel = 'stylesheet';
        css.type = 'text/css';
        css.href = localhostPath + srcs[i];
        document.getElementsByTagName("head")[0].appendChild(css);
    }
}



if(typeof(local)=='undefined'){
    var local = '/Home/Index/index';
}
local = $.myCommon.changeUrl(local);

var lock=''; //全局锁定ajax
var page=1; //定义页码为1
var shuzi=new Array('一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五','十六','十七','十八','十九','二十');
var Types=''; //题型



//2014-12-15 
function addData(data,params){
    params = $.extend({
        isSelected : false,
        selectedById : 0,
        frontChar : '',
        val : false, //提取json中的指定值
        text : false //提取json中指定的描述
    },params);
    if(params.val === false || params.text === false){
        return false;
    }
    var string = '';
    for(var i=0; i<data.length; i++){
        var elements = data[i];
        var val = elements[params['val']];
        var text = elements[params['text']];
        string += getOption(params['frontChar']+val, text, (params.isSelected && params.selectedById == val));
    }
    return string;
}

function getOption(val,text,isSelected,className){
    var str = '<option value="'+val+'"';
    if(className){
        str += ' class="'+className+'"';
    }
    if(isSelected){
        str += ' selected="selected"';
    }
    return str += ('>' + text + '</option>');
}

var nodes = [];
function addStrips(data){
    for(var d in data){
        var node = new Node(data[d]['SpecialID'],data[d]['SpecialName']);
        if(nodes.length > 0){
            nodes[nodes.length-1].last(node);
        }
        nodes.push(node);
        if(data[d]['sub']){
            addChildNode(data[d]['sub'],nodes[nodes.length-1]);
        }
    }
}

function addChildNode(sub,parent){
    for(var i in sub){
        var node = new Node(sub[i]['SpecialID'],sub[i]['SpecialName']);
        parent.add(node);
        if(typeof(sub[i]['sub']) !== 'undefined'){
            addChildNode(sub[i]['sub'],node);
        }
    }
}

function Node(v,t){
    this.v = v;
    this.t = t;
    this.p = null;
    this.c = [];
    this.ln = null;

    this.last = function(node){
        this.ln = node;
    }

    this.add = function(node){
        if(this.c.length > 0){
           this.c[this.c.length-1].last(node); 
        }
        node.p = this;
        this.c.push(node); 
    }

    this.getLevel = function(){
        var parent = this.p;
        var s = 0;
        while(parent != null){
            s++;
            parent = parent.p;
        }
        return s;
    }

    this.isTop = function(){
        return this.p == null;
    }

    this.getText = function(){
        var text = '';
        var s = this.getLevel();
        for(; s>0; s--){
            if(this.p.ln != null)
                text += '┃';
            else
                text += '&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        text += (this.ln != null ? '┝ ' : '┕ ');
        var val = this.c.length == 0 ? this.v : '';
        text = getOption(val,text + this.t);
        for(var i=0; i<this.c.length; i++){
            text += this.c[i].getText();
        }
        return text;
    }
}