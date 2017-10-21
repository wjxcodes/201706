//检测作业
$.myWork={
    init:function(workStyle){
        if(workStyle=='case'){
            $.myWorkCommon.init(workStyle);
        }

        this.initDivBoxHeight();
        this.loadWork(1);//载入作业
        this.workAbout();
        this.goToPage();//作业分页跳转
        $(window).resize(function() { $.myWork.initDivBoxHeight(); });
    },
    initDivBoxHeight:function(){
        var a = $(window).width();
        var b = $(window).height();
        if(a<790) a=790;
        $("#workBox").width(a).height(b);
        if($('.emptyWork').length>0) {
            $.myWork.initEmptyWork();
        }
    },
    //作业为空
    initEmptyWork:function(){
        if($('.emptyWork').length==0) {
            var tmpStr='';

            switch($.myWorkCommon.workStyle){
                case 'test':
                    tmpStr = $.myWork.initEmptyWorkContent(
                        '> 作业模块 > 布置作业',
                        '您还没有作业 ，快去留作业吧!',
                        '<div class="des">留作业流程：<a href="'+U('Manual/Index/zj')+'">手工出题</a>（<a href="'+U('Ga/Index/index')+'">智能组卷</a>）> <a href="'+U('Home/Index/zuJuan')+'">试卷预览</a> > <a href="'+U('Home/Index/zuJuan')+'">创建作业</a> > <a href="'+U('Work/Work/addWork?workStyle='+ $.myWorkCommon.workStyle)+'">分配作业</a></div>'+
                            '<div class="selectStyle"><a href="'+U('Manual/Index/zj')+'">手工出题</a><a href="'+U('Ga/Index/index')+'">智能组卷</a></div>'
                    );
                    break;
                case 'case':
                    tmpStr = $.myWork.initEmptyWorkContent(
                        '> 高效同步课堂 > 分发导学案',
                        '您还没有导学案 ，快去创建导学案吧!',
                        '<div class="selectStyle"><a href="'+U('Guide/Case/index')+'">创建导学案</a></div>'
                    );
                    break;
            }
            $('#workBox').html(tmpStr);
        }
        var b = $(window).height();
        var h = b - $('#righttop').outerHeight() - 30;
        var h1= $('.emptyWrap').height();
        $('.emptyWork').height(h);
        $('.emptyWrap').css({'paddingTop':(h-h1)/2+'px'})
    },
    //数据为空时的字符串
    initEmptyWorkContent:function(path,title,content){
        var subjectName = $('#cursubject',window.parent.document).text();
        return '<div id="righttop">'+
            '<div id="categorylocation">'+
            '<span class="newPath">当前位置：</span>> <span id="loca_text"><span>'+subjectName+'</span> '+path+'</span>'+
            '</div>'+
            '</div>'+
            '<div class="emptyWork">'+
            '<div class="emptyWrap">'+
            '<h1>'+title+'</h1>'+content+
            '</div></div>';
    },
    //载入作业
    loadWork:function(page){
        $('.workHistory').html($.myCommon.loading());
        $.post(U('Work/Work/getLeavedUserWork?workStyle='+ $.myWorkCommon.workStyle),{'page':page,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                $('.workHistory').html('载入失败');
                return false;
            }

            var tmpStr='<table class="addwork_table hasTable">'+
                '<thead><tr>';
            switch($.myWorkCommon.workStyle){
                case 'test':
                    tmpStr+='<th>作业名称</th>'+
                        '<th>学科</th>'+
                        '<th>题数</th>'+
                        '<th>存档时间</th>'+
                        '<th>布置作业</th>'+
                        '<th>操作</th>';
                    break;
                case 'case':
                    tmpStr+='<th>导学案名称</th>'+
                            '<th>学科</th>'+
                            '<th>课时</th>'+
                            '<th>题数/知识点</th>'+
                            '<th>存档时间</th>'+
                            '<th>分发导学案</th>'+
                            '<th>操作</th>';
                    break;
            }
            tmpStr+='</tr></thead><tbody>';

            if(data['data'][0]!='success'){
                $.myWork.initEmptyWork();
                return false;
            }

            for(var i in data['data'][1]){
                switch($.myWorkCommon.workStyle){
                    case 'test':
                        tmpStr+='<tr id="tr'+data['data'][1][i]['SaveID']+'">'+
                            '<td><span class="btn_normal addwork_show" wid="'+data['data'][1][i]['SaveID']+'">'+data['data'][1][i]['SaveName']+'</span></td>'+
                            '<td>'+data['data'][1][i]['SubjectName']+'</td>'+
                            '<td>'+data['data'][1][i]['TestNum']+'</td>'+
                            '<td>'+data['data'][1][i]['LoadTime']+'</td>'+
                            '<td><a class="td_btn" wid="'+data['data'][1][i]['SaveID']+'">布置作业</a></td>'+
                            '<td class="lastOperate"><span class="btn_normal addwork_edit" wid="'+data['data'][1][i]['SaveID']+'">修改名称</span>  <span class="btn_normal addwork_show" wid="'+data['data'][1][i]['SaveID']+'">查看</span>  <span class="btn_normal addwork_del" wid="'+data['data'][1][i]['SaveID']+'">删除</span></td>'+
                            '</tr>';
                        break;
                    case 'case':
                        tmpStr+='<tr id="tr'+data['data'][1][i]['TplID']+'">'+
                            '<td><span class="btn_normal addwork_show" wid="'+data['data'][1][i]['TplID']+'">'+data['data'][1][i]['TempName']+'</span></td>'+
                            '<td>'+data['data'][1][i]['SubjectName']+'</td>'+
                            '<td>'+data['data'][1][i]['ChapterName']+'</td>'+
                            '<td>'+data['data'][1][i]['TestNum']+'/'+data['data'][1][i]['LoreNum']+'</td>'+
                            '<td>'+data['data'][1][i]['AddTime']+'</td>'+
                            '<td><a class="td_btn" wid="'+data['data'][1][i]['TplID']+'">分发导学案</a></td>'+
                            '<td class="lastOperate"><span class="btn_normal addwork_edit" wid="'+data['data'][1][i]['TplID']+'">修改名称</span>  <span class="btn_normal addwork_show" wid="'+data['data'][1][i]['TplID']+'">查看</span>  <span class="btn_normal addwork_del" wid="'+data['data'][1][i]['TplID']+'">删除</span></td>'+
                            '</tr>';
                        break;
                }
            }

            tmpStr+='</tbody></table>';
            $('.workHistory').html(tmpStr);

            $.myPage.showPage(data['data'][2],data['data'][3],page,0);
        });
    },
    //作业操作相关
    workAbout:function(){
        this.changeWorkName(); //修改作业名称
        this.showWorkContent(); //查看作业
        this.delWork(); //删除作业
        this.addWorkAction(); //布置作业
    },
    //修改作业名称
    changeWorkName:function(){
        //修改作业名称
        $('.addwork_edit').live('click',function(){
            var paperid=$(this).attr('wid');
            var tmp_str='新'+ $.myWorkCommon.workName+'名称：<input name="addwork_edit_name" id="addwork_edit_name" maxlength="30" type="text" value="'+$('#tr'+paperid+' td:eq(0) .addwork_show').html()+'"/><span id="addwork_edit_paperid" wid="'+paperid+'"></span>';
            $.myDialog.normalMsgBox('editaddworkdiv','修改'+ $.myWorkCommon.workName+'名称',500,tmp_str,3);
            $('#addwork_edit_name').focus();
        });
        //确认修改作业名称
        $('#editaddworkdiv .normal_yes').live('click',function(){
            var paperid=$('#editaddworkdiv #addwork_edit_paperid').attr('wid');
            var papername=$.trim($.myCommon.removeHTML($('#editaddworkdiv #addwork_edit_name').val()));
            if(papername==''){
                $.myDialog.normalMsgBox('msgdiv','信息提示',450,'请输入'+ $.myWorkCommon.workName+'名称！',2);
                return;
            }
            if(papername.length>30){
                $.myDialog.normalMsgBox('msgdiv','信息提示',450,'输入过长,限30字符！',2);
                return;
            }
            $.myDialog.normalMsgBox('loadingMsg','修改'+ $.myWorkCommon.workName+'名称',450,'正在修改'+ $.myWorkCommon.workName+'名称请稍候...');
            $.post(U('Work/Work/changeLeavedUserWorkName?workStyle='+$.myWorkCommon.workStyle),{'id':paperid,'newName':papername,'times':Math.random()},function(data){
                $('#loadingMsg .tcClose').click();
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                $('#tr'+paperid+' td:eq(0) .addwork_show').html(papername);
                $.myDialog.normalMsgBox('editaddworkdiv','修改'+ $.myWorkCommon.workName+'名称',450,'修改'+ $.myWorkCommon.workName+'名称成功！',1);
            });
        });
    },
    //查看作业
    showWorkContent:function(){
        $('.addwork_show').live('click',function(){
            $.myDialog.normalMsgBox('loadingMsg','加载'+ $.myWorkCommon.workName,450,'正在加载'+ $.myWorkCommon.workName+'请稍候...');
             var paperID=$(this).attr('wid');

            $.post(U('Work/Work/showLeavedUserWork?workStyle='+$.myWorkCommon.workStyle),{'id':paperID,'times':Math.random() },function(data){
                $('#loadingMsg .tcClose').click();
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                if (typeof (data['data']) == "object") {
                    switch($.myWorkCommon.workStyle){
                        case 'test':
                            if (data['data'][0].indexOf("@#@") >= 0) {
                                editData.clear();
                                editData.set('init',data['data'][0]);
                                parent.jQuery.myMain.basketInit();
                                window.location.href = U('Home/Index/zuJuan');
                            } else {
                                $.myDialog.normalMsgBox('errormsg','加载'+ $.myWorkCommon.workName,450,"加载失败！",2);
                            }
                            break;
                        case 'case':
                            $.caseCommon.setCookie(data['data']);
                            window.location.href = U('Guide/Case/index');
                            break;
                    }
                } else {
                    $.myDialog.normalMsgBox('errormsg','加载'+ $.myWorkCommon.workName,450,"加载失败！",2);
                }
            });
        });
    },
    //删除作业
    delWork:function(){
        $('.addwork_del').live('click',function(){
            var paperid=$(this).attr('wid');
            $.myDialog.normalMsgBox('deladdworkdiv','删除'+ $.myWorkCommon.workName,450,'确认删除'+ $.myWorkCommon.workName+'【<span id="tmp_paperid" wid="'+paperid+'"></span>'+$(this).parent().parent().find('.addwork_show').html()+"】",3);
        });
        //确认删除作业
        $('#deladdworkdiv .normal_yes').live('click',function(){
            var paperid=$('#deladdworkdiv #tmp_paperid').attr('wid');
            $.myDialog.normalMsgBox('loadingMsg','删除'+ $.myWorkCommon.workName,450,'正在删除'+ $.myWorkCommon.workName+'请稍候...');
            $.post(U('Work/Work/delLeavedUserWork?workStyle='+$.myWorkCommon.workStyle),{'id':paperid,'times':Math.random()},function(data){
                $('#loadingMsg .tcClose').click();
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                $('#tr'+paperid).remove();
                $.myDialog.normalMsgBox('deladdworkdiv','删除'+ $.myWorkCommon.workName,450,'删除'+ $.myWorkCommon.workName+'成功！',1);
                $.myWork.loadWork(1);
            });
        });
    },

    //布置作业事件
    addWorkAction:function(){
        $('.td_btn').live('click',function(){
            var self=$(this);
            var workName=self.parents('tr').last().find('.addwork_show').eq(0).text();
            var workID=self.attr('wid');
            $.myWorkCommon.addWorkPaperAction(workName,'','',workID,'');
        });
    },

    //分页跳转
    goToPage:function(){
        $("#pagelistbox a").live("click",function(){
            $.myWork.loadWork($(this).attr('page'));
        });
    }
}
//检测作业
$.myWorkCheck={
    init:function(workStyle){
        if(workStyle=='case'){
            $.myWorkCommon.init(workStyle);
        }

        this.initDivBoxHeight(false);
        this.loadClass();
        this.classAbout();
        this.workAbout();
        this.goToPage();
        $(window).resize(function() { $.myWorkCheck.initDivBoxHeight(true); });
    },
    initDivBoxHeight:function(ifResize){
        var a = $(window).width();
        var b = $(window).height();
        if(a<790)  a=790;
        $("#workBox").width(a).height(b);
        var c = $('.w-left').outerWidth();
        $('.w-right').width(a-c-40);
        var lh=b-$('.class-tit').outerHeight(true);
        $('.load-class').height(lh);
        if(ifResize==true){//刷新窗口不引起班级过载载入班级时自动过载
            $.myWorkCheck.classOver(true);
        }
        if($('.emptyBox').length>0){
            $.myWorkCheck.initEmptyWork();
        }
    },
    //作业为空
    initEmptyWork:function(){
        var tmpStr='<div class="emptyBox">'+
            '<div class="emptyWrap">'+
            '<h1>本班没有布置过'+ $.myWorkCommon.workName+',快去布置'+ $.myWorkCommon.workName+'吧!</h1>'+
            '<div class="assignWork"><a href="'+U('Work/Work/addWork?workStyle='+$.myWorkCommon.workStyle)+'">布置'+ $.myWorkCommon.workName+'</a></div></div></div>';
        $('#assignedWork').html(tmpStr);
        var b = $(window).height();
        var h = b - $('#rightTop').outerHeight(true)-$('.public-title').outerHeight(true)-10;
        var h1= $('.emptyWrap').height();
        $('.emptyBox').height(h);
        $('.emptyWrap').css({'paddingTop':(h-h1)/3+'px'});
    },
    loadClass:function(){
        $('.load-class .bd').html($.myCommon.loading());
        $.post(U('Work/MyClass/getClassList'),{'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                $('.load-class').html('加载失败');
                return false;
            }
            var tit='提示信息';
            if(data['data'][0]=='success'){
                var output='<ul>';
                var current='';
                for(var i in data['data'][1]){
                    //默认班级选择
                    if(classID!=0){
                        if(data['data'][1][i]['ClassID']==classID){
                            current = ' class="current"';
                        } else {
                            current = '';
                        }
                    } else {
                        if (i == 0) {
                            current = ' class="current"';
                        } else {
                            current = '';
                        }
                    }
                    output+='<li'+current+' cid="'+data['data'][1][i]['ClassID']+'" title="'+data['data'][1][i]['ClassName']+'">'+data['data'][1][i]['ClassName']+'</li>';
                }
                output+='</ul>';
                $('.load-class .bd').html(output);
                $.myWorkCheck.classOver();//班级过载
                $.myWorkCheck.loadWork(1);//载入作业
            }else if(data['data'][0]=='add'){
                $('.load-class').html('请添加班级');
                $('#assignedWork').html('<p style="text-align:center;background-color: #FFF;padding:20px 0px;">请添加班级</p>');
                $.myDialog.normalMsgBox('loadclassmsgdiv',tit,450,'您还未加入任何班级！是否立刻加入？',3);
            }
        });
    },
    /**
     * 班级过多增加点击滚动
     * @param ifNumChange  班级显示高度变化引起可显示数目变化
     */
    classOver:function(ifNumChange){
        if(ifNumChange==true){
            var ulStr=$('.load-class .bd .tempWrap').html();
            $('.load-class .bd').html(ulStr);
            //$('.load-class ul').css({'top':0});
        }
        //班级数目过载
        if($('.load-class ul').height()>$('.load-class').height()){
            if($('.prev:visible').length==0) {
                $('.prev,.next').slideDown(10);
            }
            var lh = $(window).height()-$('.class-tit').outerHeight(true)-40;

            //计算班级应显示的个数
            var num=Math.floor(lh/$('.load-class ul li').outerHeight());
            jQuery(".load-class").slide({mainCell:".bd ul",autoPage:true,type:'slide',effect:"top",autoPlay:false,vis:num,trigger:"click",scroll:num,easing:'swing'});
        }else{
            //判断点击条是否显示 显示则隐藏
            if($('.prev:visible').length>0){
                $('.prev,.next').slideUp(10);
                $('.load-class .bd').html('<ul>'+$('.load-class ul').html()+'</ul>');//重置班级
            }
        }
    },
    //班级相关操作
    classAbout:function(){
        //班级跳转
        $('#loadclassmsgdiv .normal_yes').live('click',function(){
            location.href=U('Work/MyClass/myClass');
        });
        //切换班级
        $('.load-class li').live('click',function(){
            $('.load-class li').removeClass('current');
            $(this).addClass('current');
            $.myWorkCheck.loadWork(1);
        });
    },
    /**
     * 载入作业
     * @param page 页数 初始值设为1
     */
    loadWork:function(page){
        var cid=$('.load-class li.current').attr('cid');
        $('#assignedWork').html($.myCommon.loading());

        $.post(U('Work/Work/getAssignedWork?workStyle='+ $.myWorkCommon.workStyle),{'ClassID':cid,'p':page,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                $('#assignedWork').html('<p style="text-align:center;background: #FFF;padding:10px 0px;">'+data['data']+'</p>');
                return false;
            }
            var outDate    = '';//是否过期
            var outEndtime = '';
            var outStartTime ='';
            var order      = '';//作业顺序
            var style      = '';//作业方式
            var text       = '';
            var textColor  = '';
            //默认状态
            if(data['data'][0]=='success'){
                var tmpStr='<ul>';
                for(var i in data['data'][1][0]){
                    outDate    = 'bc-gray';//是否过期
                    outEndtime = 't-blue';
                    outStartTime = 't-gray';
                    order   = '';//作业顺序
                    style   = '<b class="s-tag bg-blue">在线</b>';//作业方式
                    text    ='查看';
                    textColor ='round-btn-green';

                    tmpData=data['data'][1][0][i];

                    if(tmpData['ifStart']==1){
                        outStartTime = 't-blue';
                    }
                    if(tmpData['outDate']==1){
                        outDate='bc-red';
                        outEndtime='t-red';
                    }

                    if(tmpData['WorkStyle']==1){
                        style='<b class="s-tag bg-gray">下载</b>';
                    }

                    if(tmpData['WorkOrder']==1){
                        order='<b class="s-tag bg-green">乱序</b>';
                    }

                    if(tmpData['CorrectNum']<tmpData['StudentCount']){
                        text='批阅';
                        textColor='round-btn-orange';
                    }

                    //差异化处理 是否显示知识数量
                    var zhishi='';
                    switch($.myWorkCommon.workStyle){
                        case 'test':
                            break;
                        case 'case':
                            zhishi = '<div class="test-total">知识'+tmpData['LoreNum']+'个</div>';
                            break;
                    }

                    //处理按钮
                    var piyue='';
                    var shanchu='';
                    var xaizai='';
                    if(tmpData['WorkStyle']==1){
                        shanchu='<a class="delWork round-btn round-btn-blue ml10" href="javascript:;" wid="'+tmpData['WorkID']+'">删除</a>';
                    }else{
                        if(tmpData['TestNum']==0){
                            shanchu = '<a class="delWork round-btn round-btn-blue ml10" href="javascript:;" wid="' + tmpData['WorkID'] + '">删除</a>';
                        }else {
                            if (tmpData['SendNum'] != 0) {
                                //为可以批阅的数据添加超链接
                                tmpData['WorkName'] = '<a class="checkDetail" wid="' + tmpData['WorkID'] + '">' + tmpData['WorkName'] + '</a>';
                                piyue = '<a class="checkDetail round-btn ' + textColor + '" href="javascript:;" wid="' + tmpData['WorkID'] + '">' + text + '</a>';
                            } else {
                                shanchu = '<a class="delWork round-btn round-btn-blue ml10" href="javascript:;" wid="' + tmpData['WorkID'] + '">删除</a>';
                            }
                        }
                    }
                    xaizai='<a class="downWork round-btn round-btn-blue ml10" href="javascript:;" wid="'+tmpData['WorkID']+'">下载</a>';


                    tmpStr += '<li class="jc-li-item '+outDate+'" id="li'+tmpData['WorkID']+'">'+
                        '<table class="jc-li-item-table"><tr class="jc-li-info"><td class="item-idx-td"><div class="item item-idx">'+
                        '<h4 class="idx-tit f-yahei">'+style+order+' <span class="workTitle">'+tmpData['WorkName']+'</span></h4>'+
                        '<div class="idx-item">';
                         if(tmpData['WorkStyle']==1){
                            tmpStr+='<span class="tit">下载</span>';
                         }else{
                            tmpStr+='<span class="tit">提交</span>';
                         }
                        tmpStr+='<span class="status-bar"> <i class="bg-blue" style="width:'+(tmpData['SendNum']/tmpData['StudentCount']*100).toFixed(2)+'%">进度条</i></span><span class="status-num">'+
                        '<span class="num-on">'+tmpData['SendNum']+'</span>'+'/'+tmpData['StudentCount']+'</span></div>';
                        if(tmpData['WorkStyle']==1 || tmpData['TestNum']==0){
                            tmpStr+='<div class="idx-item" style="display: none">';
                        }else{
                            tmpStr+='<div class="idx-item">';
                        }
                        tmpStr+='<span class="tit">批改</span>'+
                        '<span class="status-bar"> <i class="bg-orange" style="width:'+(tmpData['CorrectNum']/tmpData['StudentCount']*100).toFixed(2)+'%">进度条</i></span><span class="status-num">'+
                        '<span class="num-on">'+tmpData['CorrectNum']+'</span>'+'/'+tmpData['StudentCount']+'</span></div>'+
                        '</div></td><td class="item-time-td"><div class="item item-time">'+
                        '<div class="t '+outStartTime+'">开始：'+tmpData['StartTime']+'</div>'+
                        '<div class="t '+outEndtime+'">截止：'+tmpData['EndTime']+'</div></div></td>'+
                        '<td class="item-stat-td">' +
                        '<div class="item item-stat">';
                    tmpStr += zhishi;
                    tmpStr += '<div class="test-total">试题'+tmpData['TestNum']+'道</div>' +
                        '</div></td>'+
                        '<td class="jc-li-btn-td"><div class="jc-li-btn">';

                    tmpStr += piyue;
                    tmpStr += xaizai;
                    tmpStr += shanchu;

                    tmpStr += '</div></td></tr></table></li>';
                }
                tmpStr+="</ul>";
                $('#assignedWork').html(tmpStr);
                $('#pagelistbox').show();
                $('.resultFilter').show();
                $.myPage.showPage(data['data'][1][1],data['data'][1][2],page,1);
                $.myPage.showQuickSkip();
            }else{
                $.myPage.clearPage();
                $('#pagelistbox').hide();
                $('.resultFilter').hide();
                $.myWorkCheck.initEmptyWork();
            }
        });
    },
    //作业操作相关
    workAbout:function(){
        //下载作业
        $('.downWork').live('click',function(){
            var idname='downdiv';
            var wid=$(this).attr('wid');
            //显示选择项
            $.myDialog.tcLoadDiv("下载Word试卷",502,idname);
            //载入弹出框信息
            var thisUrl=U('Home/Index/zjDown?z=1');
            switch($.myWorkCommon.workStyle){
                case 'test':
                    thisUrl=U('Home/Index/zjDown?z=1');
                    break;
                case 'case':
                    thisUrl=U('Home/Index/zjDown?z=1&t=1');
                    break;
            }
            $.get(thisUrl,{'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                var str_tmp='<input name="zjdown_type" id="zjdown_type" value="1" type="hidden"/>'+
                    '<input name="zjdown_id" id="zjdown_id" value="'+wid+'" type="hidden"/>'+
                    '<input name="workStyle" id="workStyle" value="'+ $.myWorkCommon.workStyle+'" type="hidden"/>';
                $('#'+idname+' .content').html(data['data']+str_tmp);
                $.myDialog.tcShowDiv(idname);
                $('#div_shadow'+idname).css({'display':'block'});
            });
        });
        //批改作业
        $('.checkDetail').live('click',function(){
            var cid=$('.load-class li.current').attr('cid');
            location.href=U('Work/Work/checkWorkDetail?workStyle='+ $.myWorkCommon.workStyle+'&id='+$(this).attr('wid')+'&cid='+cid);
        });
        $('.jc-li-item').live('mouseover',function(){
            $(this).addClass('bc-blue');
        });
        $('.jc-li-item').live('mouseout',function(){
            $(this).removeClass('bc-blue');
        });
        //删除作业
        $('.delWork').live('click',function(){
          $.myDialog.normalMsgBox('delWorkDiv','删除作业',450,'是否确定要删除该作业？<input name="delWorkID" id="delWorkID" type="hidden" value="'+$(this).attr('wid')+'"/>',3);
        });
        //删除作业确定
        $('#delWorkDiv .normal_yes').live('click',function(){
          var WorkID=$('#delWorkID').val();
          $.post(U('Work/Work/delAssignedWork?workStyle='+ $.myWorkCommon.workStyle),{'WorkID':WorkID,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
              return false;
            }
            var tit='删除作业';
            $('#li'+WorkID).fadeOut().remove();
            $.myDialog.normalMsgBox('delWorkDiv',tit,450,'删除作业成功',1);
            $.myWorkCheck.loadWork(1);
          });
        });
    },
    //分页跳转
    goToPage:function(){
        $('#pagediv .next_page,#pagediv .prev_page,#pagediv #quicktopage a').live('selectstart contextmenu',function(){return false;})
        $("#pagelistbox a").live("click",function(){
            $.myWorkCheck.loadWork($(this).attr('page'));//获取作业
        });
        $('#pagediv .prev_page').live('click',function(){
            if($.myPage.goPrevPage()==false) return false;
            $.myWorkCheck.loadWork(page);
        });
        $('#pagediv .next_page').live('click',function(){
            if($.myPage.goNextPage()==false) return false;
            $.myWorkCheck.loadWork(page);
        });
        $('#pagediv #quicktopage a').live('click',function(){
            $.myPage.quickToPage($(this));
            $.myWorkCheck.loadWork(page);
        });
    }
}
//批改作业详细
$.myWorkCheckDetail={
    init:function(workStyle){
        if(workStyle=='case'){
            $.myWorkCommon.init(workStyle);
        }
        this.initDivBoxHeight(false);
        this.loadClass();
        this.classAbout();
        this.switchTab();
        this.workAbout();
        this.commentAbout();
        this.correctAbout();
        this.loadUserWork();
        $(window).resize(function() { $.myWorkCheckDetail.initDivBoxHeight(true); });
    },
    initDivBoxHeight:function(ifResize){
        var a = $(window).width();
        var b = $(window).height();
        if(a<790) a=790;
        $("#workBox").width(a).height(b);
        var c = $('.w-left').outerWidth();
        $('.w-right').width(a-c-40);
        var lh=b-$('.class-tit').outerHeight();
        $('.load-class').height(lh);
        if(ifResize==true){//刷新窗口不引起班级过载载入班级时自动过载
            $.myWorkCheckDetail.classOver(true);
        }
    },
    loadClass:function(){
        $('.load-class .bd').html($.myCommon.loading());
        $.post(U('Work/MyClass/getClassList'),{'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                $('.loadClass').html('加载失败');
                return false;
            }
            var tit='提示信息';
            if(data['data'][0]=='success'){
                var output='<ul>';
                var current='';
                for(var i in data['data'][1]){
                    if(data['data'][1][i]['Status']!='0'){
                        continue;
                    }
                    if(classID==data['data'][1][i]['ClassID']){
                        current=' class="current"';
                    }else{
                        current ='';
                    }
                    output+='<li'+current+' cid="'+data['data'][1][i]['ClassID']+'" title="'+data['data'][1][i]['ClassName']+'">'+data['data'][1][i]['ClassName']+'</li>';
                }
                output+='</ul>';
                $('.load-class .bd').html(output);
                $.myWorkCheckDetail.classOver();
            }else if(data['data'][0]=='add'){
                $('.load-class').html('');
                $.myDialog.normalMsgBox('loadclassmsgdiv',tit,450,'您还未加入任何班级！是否立刻加入？',3);
                return false
            }
            var curClass  = $('.load-class .current').text();
            var subjectID = Cookie.Get("SubjectId");
            var curSub    = parent.jQuery.myMain.getQuesBank(subjectID)['SubjectName'];
            curSub=curSub.substr(-2,2);
            $('.add-work-info').html(curClass+' > '+curSub+'作业');
        });
    },
    classOver:function(ifNumChange){
        if(ifNumChange==true){
            var ulStr=$('.load-class .bd .tempWrap').html();
            $('.load-class .bd').html(ulStr);
            $('.load-class ul').css({'top':0});
        }
        //班级数目过载
        if($('.load-class ul').height()>$('.load-class').height()){
            if($('.prev:visible').length==0) {
                $('.prev,.next').slideDown(10);
            }
            var lh = $(window).height()-$('.class-tit').outerHeight(true)-40;

            //计算班级应显示的个数
            var num=Math.floor(lh/$('.load-class ul li').outerHeight());
            jQuery(".load-class").slide({mainCell:".bd ul",autoPage:true,type:'slide',effect:"top",autoPlay:false,vis:num,trigger:"click",scroll:num,easing:'swing'});
        }else{
            //判断点击条是否显示 显示则隐藏
            if($('.prev:visible').length>0){
                $('.prev,.next').slideUp(10);
                $('.load-class .bd').html('<ul>'+$('.load-class ul').html()+'</ul>');
            }
        }
    },
    //班级相关操作
    classAbout:function(){
        //切换班级
        $('.load-class li').live('click',function(){
            location.href = U('Work/Work/checkWork?workStyle='+ $.myWorkCommon.workStyle+'&cid=' + $(this).attr('cid'));
        });
        //权限检测 自动跳转
        $('#powerNotice .normal_no').live('click',function(){
            history.go(-1);
        });
        $('#powerNotice .tcClose').live('click',function(){
            history.go(-1);
        });
    },
    //滑动门切换
    switchTab:function(){
        $('.cw-tab li').live('click',function(){
            var tid = $(this).attr('tabid');
            $('.cw-tab li').removeClass('on');
            $(this).addClass('on');
            $('.tabData').hide();
            $('#tab'+tid).show();
            switch(tid){
                case '1':
                    if(userWork){
                        $('.tabData').hide();
                        $('#tab1').show();
                        return false;
                    }
                    $.myWorkCheckDetail.loadUserWork();
                    break;
                case '2':
                    if(!userWork){
                        return false;
                    }
                    $.myWorkCheckDetail.loadWorkError();
                    break;
                case '3':
                    if(!userWork){
                        return false;
                    }
                    $.myWorkCheckDetail.loadWorkDetail();
                    break;
            }
        });
    },
    loadUserWork:function(){
        var self=this;
        $('#tab1').show();
        $('#tab1 .cw-details tbody').html('<tr><td colspan="4">'+$.myCommon.loading()+'</td></tr>');
        if(lock!=''){
            return false;
        }
        lock='getworkinfo';
        $.post(U('Work/Work/getWorkInfo?workStyle='+ $.myWorkCommon.workStyle),{'WorkID':workID,'ClassID':classID,'times':Math.random()},function(data){
            lock='';
            if(data['status']==20566 || data['status']==20565 || data['status']=='30214' || data['status']==20567){
                $.myDialog.normalMsgBox('powerNotice','错误提示',550,data['data']+'5秒后返回到班级作业列表页,点击确定立刻返回!',2);
                setTimeout(function(){history.go(-1)},5000);
                return false;
            }
            if($.myCommon.backLogin(data)==false){
                $('#tab1 .cw-details tbody').html('<tr><td colspan="4" style="text-align: center;padding:8px 0px;">'+data['data']+'请重试</td></tr>');
                return false;
            }
            if(userWork==''){
                userWork      = data['data'][1];
                testCategory  = data['data'][2];
                testCount     = data['data'][3];
            }
            handleHtml(data['data'][1],data['data'][2]);
        });

        function handleHtml(param,category) {
            var tmpStr='';//增加初始化
            var temp;
            var tc;
            var order;
            var realOrder;
            var color = '';
            for (var i in param) {
                order = 1;
                realOrder=1;
                tmpStr += '<tr class="cw-tr" id="tr' + param[i]['SendID'] + '"><td><div class="cw-td-name">' +
                    param[i]['NewName'] + '</div></td><td><div class="cw-td-info f-roman" id="user'+param[i]['UserID']+'">';
                if (param[i]['Content']) {
                    for (var j in param[i]['Content']) {
                        temp  = param[i]['Content'][j];
                        color = self.getColor(temp['status']);
                        if(category) {//导学案
                            if (tc != category[temp['testid'] + '-' + temp['judgeid']]) {
                                tc = category[temp['testid'] + '-' + temp['judgeid']];
                                if(j!=1){
                                    tmpStr+='</fieldset>';
                                }
                                tmpStr+='<fieldset class="case-category"><cite class="s-tag">'+tc+'</cite><div class="inline">';
                                order = 1;
                            }
                        }
                        tmpStr += '<span class="true-false-icon correct bc-' + color + '" sid="' + temp['sendid'] + '" tid="' + temp['testid'] + '" jid="'+temp['judgeid']+'" ifchoose="' + temp['ifchoose'] + '" oid="'+order+'" roid="'+realOrder+'" ifcorrect="'+temp['ifcorrect']+'">' + order + '</span>';
                        order++;
                        realOrder++;
                    }
                    if(category) {//导学案
                        tmpStr+='</div></fieldset>';
                    }
                    tmpStr += '</div></td><td><div class="cw-td-percent">';
                    if (param[i]['Delay'] == 0) {
                        tmpStr += '否';
                    } else {
                        tmpStr += '是';
                    }
                    tmpStr += '</div></td><td><div class="cw-td-handle">';
                    if (param[i]['Status'] == -1) {
                        tmpStr += '<p class="gray-tips">-</p>';
                    } else if (param[i]['Status'] == 1) {
                        tmpStr += '<a class="stuDetail round-btn round-btn-orange" href="javascript:;" oid="' + i + '">详细</a><a class="comment round-btn round-btn-blue ml10" href="javascript:;" oid="' + i + '" sid="'+param[i]['SendID']+'">写评语</a>';
                    } else {
                        tmpStr += '<a class="stuDetail round-btn round-btn-orange" href="javascript:;" oid="' + i + '">详细</a><a class="commented round-btn round-btn-gray ml10" href="javascript:;">已评论</a>';
                    }
                    tmpStr += '</div></td></tr>';
                } else {
                    tmpStr += '<p class="gray-tips">未提交作业</p>';
                }
                $('#tab1 .cw-details tbody').html(tmpStr);
            }
        }
    },
    loadWorkError:function(){
        $('#tab2 .cw-details tbody').html('<tr class="cw-tr"><td colspan="5">'+$.myCommon.loading()+'</td></tr>');
        if(testCheck){
            $.myWorkCheckDetail.handleTestCheck(testCheck,sendNum);
            return false;
        }
        $.post(U('Work/Work/getTestCheck?workStyle='+ $.myWorkCommon.workStyle),{'WorkID':workID,'ClassID':classID,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                $('#tab2 .cw-details tbody').html('<tr class="cw-tr"><td colspan="5" style="text-align:center;padding:8px 0px;">还没有学生提交作业</td></tr>');
                return false;
            }
            if(data['data'][0]=='success'){
                testCheck = data['data'][1];
                sendNum   = data['data'][2];
                $.myWorkCheckDetail.handleTestCheck(data['data'][1],data['data'][2]);
            }else{
                $('#tab2 .cw-details tbody').html('<tr class="cw-tr"><td colspan="5" style="text-align:center;padding:8px 0px;">还没有学生提交作业</td></tr>');
            }
        });
    },
    //处理错误排行
    handleTestCheck:function(param,sendNum){
        var tmpStr   = '';
        var errorPer = '';
        var rightPer = '';
        for(var i in param) {
            errorPer=((1-param[i]["rightNum"]/sendNum)*100).toFixed(0)+'%';//错误率
            rightPer=(param[i]["rightNum"]/sendNum*100).toFixed(0)+'%';//正确率
            tmpStr += '<tr class="cw-tr" oid="'+param[i]['order']+'"><td><div class="cw-td-test-num">' + param[i]['order'] +
                '</div></td>' +
                '<td><div class="cw-td-error-num"><span class="err-num-bar"><i class="bg-err" style="width:' + errorPer + '">错误</i></span><span class="error-status">' + (sendNum - param[i]["rightNum"]) + '/' +
                sendNum + ' 人</span>' +
                '</div></td>' ;
                 if(param[i]['ifChoose']==1){
                     tmpStr+='<td><div class="cw-td-choose">' + (param[i]['right']=='A'?'<b style="color:#00a0e9;">'+param[i]['A']+'</b>':param[i]['A']) + '</div></td>'+
                     '<td><div class="cw-td-choose">' +(param[i]['right']=='B'?'<b style="color:#00a0e9;">'+param[i]['A']+'</b>':param[i]['B']) + '</div></td>'+
                     '<td><div class="cw-td-choose">' +  (param[i]['right']=='C'?'<b style="color:#00a0e9;">'+param[i]['A']+'</b>':param[i]['C'] )+ '</div></td>'+
                     '<td><div class="cw-td-choose">' + (param[i]['right']=='D'?'<b style="color:#00a0e9;">'+param[i]['A']+'</b>':param[i]['D']) + '</div></td>'+
                     '<td><div class="cw-td-choose">' + param[i]['E'] + '</div></td>';
                 }else{
                     tmpStr+='<td><div class="cw-td-choose" style="color:#ccc">' + '-' + '</div></td>'+
                     '<td><div class="cw-td-choose" style="color:#ccc">' + '-' + '</div></td>'+
                     '<td><div class="cw-td-choose" style="color:#ccc">' + '-' + '</div></td>'+
                     '<td><div class="cw-td-choose" style="color:#ccc">' + '-' + '</div></td>'+
                     '<td><div class="cw-td-choose" style="color:#ccc">' + '-' + '</div></td>';
                 }

                 tmpStr+='<td><div class="cw-td-error-rate">' + rightPer + '</div></td>';
                 tmpStr+='<td><div class="cw-td-test-type">';
            if (param[i]['ifChoose'] == 1) {
                tmpStr += '选择题';
            } else {
                tmpStr += '非选择题';
            }
            tmpStr += '</div></td><td><div class="cw-td-handle-2"><a class="errorDetail round-btn round-btn-blue" oid="' + param[i]['order'] + '" tid="'+param[i]['testID']+'" jid="'+param[i]['judgeID']+'" href="javascript:;">详情</a></div></td></tr>';
        }
        $('#tab2 .cw-details tbody').html(tmpStr);

    },
    loadWorkDetail:function(){
        var self = this;
        $('.ques-index-box').html($.myCommon.loading());
        $('.cw-stu-list-box').html($.myCommon.loading());
        $('.test-list').html($.myCommon.loading());
        //载入试题序号
        self.loadTestOrder();
        //载入学生
        loadStudent();
        if(struct && testInfo){
            $.myWorkCheckDetail.handleTestContent(struct,testInfo,'');
            return false;
        }
        //载入试题内容
        var ifTest = 1;
        if(testInfo) ifTest = 0;
        $.post(U('Work/Work/getTestContent?workStyle='+ $.myWorkCommon.workStyle),{'workID':workID,'ifTest':ifTest,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                $('.test-list').html('<p style="text-align: center;">获取作业失败</p>');
                return false;
            }
            struct = data['data'][0];
            if(ifTest==1) testInfo = data['data'][1];
            $.myWorkCheckDetail.handleTestContent(struct,testInfo,'');
        });

        //载入学生
        function loadStudent(){
            if(userWork) {
                var userListHtml = '<a href="javascript:;" uid="0" class="on">默认作业</a>';
                for (var i in userWork) {
                    if (userWork[i]['Status'] == -1) {
                        userListHtml += '<b>' + userWork[i]['NewName'] + '</b>';
                    } else {
                        userListHtml += '<a href="javascript:;" uid="' + userWork[i]['UserID'] + '" sid="'+userWork[i]['SendID']+'">' + userWork[i]['NewName'] + '</a>';
                    }
                }
                $('.cw-stu-list-box').html(userListHtml);
                return false;
            }
            $('.cw-stu-list-box').html('<p style="text-align: center;">加载学生失败,请点击左侧班级重试</p>');
        }
        //样式待优化
        //$('#workBox').scroll(function(){
        //        var oriWidth = $('.cw-ques-mini-nav').width();
        //        if($('#workBox').scrollTop() > 180){
        //            $('.cw-ques-mini-nav').addClass('cw-ques-mini-nav-fix').width(oriWidth);
        //        }else{
        //            $('.cw-ques-mini-nav').removeClass('cw-ques-mini-nav-fix').width('auto');
        //        }
        //});

        $('#workBox').scroll(function(){
              var height = $('#workBox').scrollTop();
              if(height>200){
                  $('.wln-backtop').fadeIn(300);
              }else{
                  $('.wln-backtop').fadeOut(300);
              }
        });
        $('.wln-backtop').live('click',function(){
               $('#workBox').animate({scrollTop:0},500);
        });
    },
    //载入默认题号顺序
    loadTestOrder:function(){
        var tmpStr = '<p style="text-align: center;">试题序号载入失败,请点击左侧班级重试</p>';
        var tc = '';
        if(userWork){
            var order = 1;
            tmpStr='';
            for(var i in userWork){
                if(userWork[i]['Status']!=-1){
                    if(userWork[i]['Content']){
                        for(var j in userWork[i]['Content']){
                            var temp=userWork[i]['Content'][j];
                            if(testCategory) {//导学案
                                if (tc != testCategory[temp['testid'] + '-' + temp['judgeid']]) {
                                    tc = testCategory[temp['testid'] + '-' + temp['judgeid']];
                                    if(j!=1){
                                        tmpStr+='</fieldset>';
                                    }
                                    tmpStr+='<fieldset class="case-category"><cite class="s-tag">'+tc+'</cite><div class="inline">';
                                    order = 1;
                                }
                            }
                            tmpStr += '<span class="true-false-icon correct bc-gray" sid="' + temp['sendid'] + '" tid="' + temp['testid'] + '" jid="'+temp['judgeid']+'" ifchoose="' + temp['ifchoose'] + '" oid="'+order+'">' + order + '</span>';
                            order++;
                        }
                        if(testCategory) {//导学案
                            tmpStr+='</div></fieldset>';
                        }
                        $('.ques-index-box').html(tmpStr);
                        return false;
                    }
                }
            }
            //未找到数据
            $('.ques-index-box').html(tmpStr);
            return false;
        }
        //数据为空
        $('.ques-index-box').html(tmpStr);
    },
    //处理作业详细内容
    handleTestContent:function(struct,testInfo,userAnswer) {
        if ($.myWorkCommon.workStyle == 'test') {
            handleTestContentOfTest(struct, testInfo, userAnswer);
        } else {
            handleTestContentOfCase(struct, testInfo, userAnswer);
        }
        function handleTestContentOfTest(struct, testInfo, userAnswer) {
            var tmpStr = '';
            var temp;
            var testOrder = 1;
            for (var i in struct) {
                tmpStr += '<div class="temp f-roman">';
                tmpStr += '<div class="questypetitle cq-tit"><b class="cq-num">' + struct[i]['chinesenum'] + '、</b><span class="questypename" id="questypename1_1">' + struct[i]['questype'] + '</span></div>';
                for (var j in struct[i]['test']) {
                    temp = struct[i]['test'][j];
                    tmpStr += '<div class="testBody ques-body">';
                    if (testInfo[temp] && testInfo[temp]['error'] == 0) {
                        //题文部分
                        tmpStr += '<div class="questext" id="testid' + temp + '">';
                        tmpStr += testInfo[temp]['testsplit']['content'];
                        tmpStr += '</div>';
                        //对小题,答案解析处理
                        for (var k = 0; k < testInfo[temp]['testnum']; k++) {
                            if (testInfo[temp]['testnum'] > 1) {//复合题
                                   //特殊情况小题题文为空
                                    tmpStr += '<div class="subtest">';
                                    tmpStr += '<span class="quesindex"><b>' + testOrder + '. </b></span>' + testInfo[temp]['testsplit'][k];
                                    tmpStr += '</div>';
                            }
                            //答案
                            tmpStr += '<div class="answer"><h5 class="site-tit">答案:</h5>';
                            tmpStr += testInfo[temp]['answersplit'][k];
                            tmpStr += '</div>';
                            //解析
                            tmpStr += '<div class="analytic"><h5 class="site-tit">解析:</h5>';
                            tmpStr += testInfo[temp]['analyticsplit'][k];
                            tmpStr += '</div>';
                            //用户答案部分
                            if (userAnswer) {
                                tmpStr += '<div class="uanswer cw-ques-cite-box cite-face-true"><div class="cq-stu-answer">';

                                tmpStr += '<div class="useranswer">';
                                if (userAnswer[temp][k]['UserAnswer'] == '') {
                                    userAnswer[temp][k]['UserAnswer'] = '学生未作答';
                                }
                                tmpStr += '<h4 class="tit">用户答案: </h4>' +'<div class="cq-sa-context">'+userAnswer[temp][k]['UserAnswer']+'</div>';
                                tmpStr += '</div>';
                                tmpStr += '<div class="dotted-line"></div>';
                                tmpStr += '<div class="correct cq-teacher-comment">';

                                tmpStr += '<div class="cq-tc-score"><i class="tit">评分：</i>';
                                tmpStr += $.myTest.starShow(userAnswer[temp][k]['Start']);
                                tmpStr += '</div>';
                                if (userAnswer[temp][k]['IfCorrect'] == 0) {
                                    userAnswer[temp][k]['CorrectContent'] = '未批改';
                                }
                                tmpStr += '<div class="cq-tc-text"><i class="tit">评语：</i>' +
                                    '<span class="text">' + userAnswer[temp][k]['CorrectContent'] + '</span>';
                                tmpStr += '</div>';

                                tmpStr += '</div>';

                                tmpStr += '</div>';
                                tmpStr += '</div>';
                            }
                            testOrder++;
                        }
                    } else {
                        //数据丢失时显示处理
                        tmpStr += '<div class="lost" style="text-align:left;margin-left:23px;padding:10px  0px;"><b>'+testOrder+'．</b>[未找到该题数据]</div>';
                        //testOrder += testInfo[temp]['testnum'];//数据丢失不再保持题号,和之前序号保持一致
                    }
                    tmpStr += '</div>';
                }
                tmpStr += '</div>';
            }
            $('.test-list').html(tmpStr);
        }

        function handleTestContentOfCase(struct, testInfo, userAnswer) {
            var tmpStr = '';
            var temp;
            for (var i in struct) {
                tmpStr += '<div class="temp f-roman">';
                tmpStr += '<div class="title">' + '<h3 class="cq-tit">'+struct[i]['titlealias'] + struct[i]['title']+'</h3></div>';//一级分类
                for (var j in struct[i]['test']) {//二级分类循环
                    var testOrder = 1;
                    temp = struct[i]['test'][j];
                    tmpStr += '<div class="subtemp">' + '<h4 class="subtemp-title">'+temp['menuName']+'</h4>' ;
                    if (temp['testlist']) {//含有试题或者知识
                        for (var k in temp['testlist']) {//试题
                            if (testInfo[temp['testlist'][k]] && testInfo[temp['testlist'][k]]['error']==0) {//含有该试题信息
                                if (temp['ifTest'] == 0) {//知识
                                      tmpStr+= '<div class="lore">'+testInfo[temp['testlist'][k]]['Lore']+'</div>';
                                      if(testInfo[temp['testlist'][k]]['Answer']) {
                                          tmpStr += '<div class="loreanswer">' + testInfo[temp['testlist'][k]]['Answer'] + '</div>';
                                      }
                                } else {//试题
                                    tmpStr+= '<div class="questext" id="testid'+temp['testlist'][k]+'">'+testInfo[temp['testlist'][k]]['testsplit']['content']+'</div>';//题文

                                    for(var l=0;l<testInfo[temp['testlist'][k]]['testnum'];l++){
                                         if(testInfo[temp['testlist'][k]]['testnum']>1){//复合体
                                             tmpStr += '<div class="subtest">';
                                             tmpStr += '<span class="quesindex"><b>' + testOrder + '. </b></span>' + testInfo[temp['testlist'][k]]['testsplit'][l];
                                             tmpStr += '</div>';
                                         }
                                        //答案
                                        tmpStr += '<div class="answer"><h5 class="site-tit">答案:</h5>';
                                        tmpStr += testInfo[temp['testlist'][k]]['answersplit'][l];
                                        tmpStr += '</div>';
                                        //解析
                                        tmpStr += '<div class="analytic"><h5 class="site-tit">解析:</h5>';
                                        tmpStr += testInfo[temp['testlist'][k]]['analyticsplit'][l];
                                        tmpStr += '</div>';
                                        if(userAnswer){
                                            tmpStr += '<div class="uanswer cw-ques-cite-box cite-face-true"><div class="cq-stu-answer">';

                                            tmpStr += '<div class="useranswer">';
                                            if (userAnswer[temp['testlist'][k]][l]['UserAnswer'] == '') {
                                                userAnswer[temp['testlist'][k]][l]['UserAnswer'] = '学生未作答';
                                            }
                                            tmpStr += '<h4 class="tit">用户答案:</h4>' +'<div class="cq-sa-context">'+ userAnswer[temp['testlist'][k]][l]['UserAnswer'];
                                            tmpStr += '</div></div>';
                                            tmpStr += '<div class="dotted-line"></div>';
                                            tmpStr += '<div class="correct cq-teacher-comment">';
                                            tmpStr += '<div class="cq-tc-score"><i class="tit">评分：</i>';
                                            tmpStr += $.myTest.starShow(userAnswer[temp['testlist'][k]][l]['Start']);
                                            tmpStr += '</div>';
                                            if (userAnswer[temp['testlist'][k]][l]['IfCorrect'] == 0) {
                                                userAnswer[temp['testlist'][k]][l]['CorrectContent'] = '未批改';
                                            }
                                            tmpStr += '<div class="cq-tc-text"><i class="tit">评语：</i>' +
                                                '<span class="text">' + userAnswer[temp['testlist'][k]][l]['CorrectContent'] + '</span>';
                                            tmpStr += '</div>';

                                            tmpStr += '</div>';

                                            tmpStr += '</div>';
                                            tmpStr += '</div>';
                                        }
                                         testOrder++;
                                    }

                                }
                            }else{
                                //tmpStr += '<div class="lost" style="text-align:left;margin-left:23px;padding:10px 0px;"><b>'+testOrder+'．</b>[未找到该题数据]</div>';
                                //testOrder += testInfo[temp['testlist'][k]]['testnum'];//数据丢失不再保持题号,和之前序号保持一致
                            }
                        }
                    }
                    tmpStr+='</div>';
                }
                tmpStr+='</div>';
            }
            $('.test-list').html(tmpStr);
        }
    },
    //获取试题状态颜色
    getColor:function(status){
         var color='';
         switch(status){
             case 0:
                color = 'gray';
                break;
             case 1:
                 color = 'green';
                 break;
             case 2:
                 color = 'red';
                 break;
             case 3:
                 color = 'blue';
                 break;
             case 4:
                 color = 'orange';
                 break;
         }
        return color;
    },
    //作业相关操作:查看详情(评分)/写评语
    workAbout:function(){
        var self = this;
        //第一界面
        //作业详细
        $('.stuDetail').live('click',function(){
            $.myDialog.normalMsgBox('msgdiv','信息提示',550, $.myCommon.loading());
            var oid=$(this).attr('oid');
            if(userWork[oid]){
                var tmpStr='';
                var temp=userWork[oid];
                tmpStr+='<div class="workInfo">'+
                    '<div class="workInfoTit"><span class="line">一一一</span><span class="commentTit"><b>'+temp['NewName']+'</b>同学的作业情况</span><span class="line">一一一</span></div>'+
                    '<table>'+
                    '<tr>'+
                    '<td>是否超期</td>'+
                    '<td class="workInfoContent">'+(temp['Delay']==1?'是':'否')+'</td>'+
                    '</tr>'+
                    '<tr>'+
                    '<td>状态</td>'+
                    '<td class="workInfoContent">'+(temp['Status']==2?'已评论':'已提交')+'</td>'+
                    '</tr>'+
                    '<tr>'+
                    '<td>做题时间</td>'+
                    '<td class="workInfoContent">'+temp['DoTime']+'秒</td>'+
                    '</tr>'+
                    '<tr>'+
                    '<td>提交时间</td>'+
                    '<td class="workInfoContent">'+temp['SendTime']+'</td>'+
                    '</tr>'+
                    '<tr>'+
                    '<td>审查时间</td>'+
                    '<td class="workInfoContent">'+temp['CheckTime']+'</td>'+
                    '</tr>'+
                    '<tr>'+
                    '<td>教师评论</td>'+
                    '<td class="workInfoContent">'+temp['Comment']+'</td>'+
                    '</tr>'+
                    '</table>'+
                    '</div>';
                    $.myDialog.normalMsgBox('msgdiv','作业详细',550,tmpStr,4);
            }
        });
        //第二界面
        //错误详细
        $('.errorDetail').live('click',function(){
            var tid=$(this).attr('tid');
            var jid=$(this).attr('jid');
            var ifTest=1;
            if(testInfo){
                ifTest=0
            }
            $.myDialog.normalMsgBox('errorDetail','信息提示',700, $.myCommon.loading());
            $.post(U('Work/Work/getTestCheckDetail?workStyle='+ $.myWorkCommon.workStyle),{ 'workID': workID,'jid':jid,'tid':tid,'ifTest':ifTest}, function(data) {
                if($.myCommon.backLogin(data)==false){
                    $.myDialog.normalMsgBox('errorDetail','信息提示',650, data['data'],4);
                    return false;
                }
                var answerStatus=data['data'][1];
                if(ifTest==1){
                    testInfo=data['data'][2];
                }
                var str ='<div class="ques-details-tab"><b class="on" href="javascript:;">题文</b><span class="toggle"><a href="javascript:;" class="showAnswer">显示答案</a><a href="javascript:;" class="showAnaly">显示解析</a></span></div>';
                str+='<div class="mr-ques-scroll-wrap">'+
                     '<div class="mr-ques-details">'+
                     '<div class="ques-item-wrap f-roman">'+
                     '<div class="qi-text">'+
                     '<div class="ques-body">';
                str+=testInfo[tid]['testsplit']['content'];
                   if(jid!=0){
                      str+='<b>'+jid+'．(复合题)</b>'+testInfo[tid]['testsplit'][jid]
                   }
                    str+='</div>'+
                    '</div>';

                    str+='<div class="correct-info">'+
                    '<div class="sub-test-wrap">'+
                    '<div class="qi-answer" style="display:none;"><h5 class="qi-item-tit">答案</h5>'+testInfo[tid]['answersplit'][jid]+'</div>'+
                    '<div class="qi-explain" style="display:none;"><h5 class="qi-item-tit">解析</h5>'+testInfo[tid]['analyticsplit'][jid]+'</div>';
                    str+='</div></div></div></div></div>';
                if(answerStatus['ifchoose']){
                    str+='<div class="cw-choice-ques-status f-yahei">';
                    var choose=['A','B','C','D'];
                    for(var i in choose){
                        var ifTrue='';
                        if(answerStatus['correct']==choose[i]){

                            ifTrue='true';
                        }
                        str+='<dl class="choice-item-fours '+ifTrue+'">'+
                            '<dt class="radio-item">'+choose[i]+'</dt>'+
                            '<dd class="ri-num">'+answerStatus[choose[i]]+'人</dd></dl>';
                    }
                    str+="</div>";
                }
                $.myDialog.normalMsgBox('errorDetail','错题排行详情',700,str);
                // 设置弹框结构高度
                   var winHeight = $(window).height();
                   if(answerStatus['ifchoose']){
                    // 选择题height
                    $(".ques-item-wrap").height(winHeight-350);
                }else{
                    // 非选择题height
                    $(".ques-item-wrap").height(winHeight-223);
                }
                   $("#errorDetail .content").css("padding","15px");
                   $.myDialog.tcDivPosition("errorDetail");
            });
        });
        //按序号排序
        $('.cw-th-test-num a').live('click',function(){
            return false;
            var arr = handleTestCheckContent(testCheck,sortByOrder);
            $.myWorkCheckDetail.handleTestCheck(arr);
        });
        //按错误率排序
        $('.cw-th-error-num a').live('click',function(){
            return false;
            var arr = handleTestCheckContent(testCheck,sortByRight);
            $.myWorkCheckDetail.handleTestCheck(arr);
        });
        //错误详情排序
        function handleTestCheckContent(testCheck,byWhich){
            var arr=[];
            var newArr=[];
            for(var i in testCheck){
                arr.push(testCheck[i]);
            }
            arr.sort(byWhich);
            return arr;
        }
        //按序号
        function sortByOrder(x,y){
            return x['order']-y['order'];
        }
        //按正确率
        function sortByRight(x,y){
            return x['right']-y['right'];
        }
        //第三界面
        //查看某个学生的作业
        $('.cw-stu-list-box a').live('click',function(){
            $('.cw-stu-list-box a').removeClass('on');
            $(this).addClass('on');

            var uid = $(this).attr('uid');
            if(uid==0){//默认作业
                self.loadTestOrder();
                $.myWorkCheckDetail.handleTestContent(struct,testInfo,'');
            }else{//用户作业
                $('.ques-index-box').html($('#user'+uid).html());
                var sid = $(this).attr('sid');
                $.post(U('Work/Work/getUserAnswer?workStyle='+ $.myWorkCommon.workStyle),{'workID':workID,'sid':sid,'times':Math.random()},function(data){
                                        if($.myCommon.backLogin(data)==false){
                        alert('获取用户作业数据失败');
                        return false;
                    }
                    var userAnswer = data['data'][1];//用户答案

                    $.myWorkCheckDetail.handleTestContent(struct,testInfo,userAnswer);
                });
            }
        });
        /*通用事件处理*/
        //序号滚动
        $('.ques-index-box span').live('click',function(){
              var tid = $(this).attr('tid');
              var position = $('#testid'+tid).offset();
              $('#workBox').animate({scrollTop:position.top},300);
        });
        //
        /*显示答案解析*/
        $('.showAnswer').live('click',function(){
            if($(this).hasClass('on')){
                $(this).removeClass('on').text("显示答案");
                $('.qi-answer').slideUp(400);
            }else{
                $(this).addClass('on').text("隐藏答案");
                $('.qi-answer').slideDown(400);
            }
        });
        //错误详情显示解析
        $('.showAnaly').live('click',function(){
            if($(this).hasClass('on')){
                $(this).removeClass('on').text("显示解析");
                $('.qi-explain').slideUp(400);
            }else{
                $(this).addClass('on').text("隐藏解析");
                $('.qi-explain').slideDown(400);
            }
        });
    },
    //批改相关
    correctAbout:function(){
        //规则:常规->correctDiv 快速->quickcorrectDiv
        //常规批阅
        $('.cw-tr .correct').live('click',function(){
            $('.cw-tr .correct').removeClass('check');
            $(this).addClass('check');
            getCorrectContent(this,'','correctDiv');
        });
        //快速批阅
        $('#quickCorrect').live('click',function(){
            if(!userWork) return false;
            if(lock!='') return false;
            var fromWhere='quickcorrectDiv';
            $.myDialog.normalMsgBox(fromWhere,'快速批阅',750, $.myCommon.loading());
            getQuickCorrectContent(fromWhere);
        });
        //处理快速批阅内容
        function getQuickCorrectContent(fromWhere){
            //加入学生列表
            var str = '';
            var defaultUserID = '';//默认值
                str+='<div class="mark-left">'+
                '<div class="ml-search f-yahei">'+
                '<h3 class="tit">学生列表</h3>'+
                '<div class="ml-search-box">'+
                '<span class="search-border">'+
                '<input id="userSelectName" class="txt-input" type="text" value="张三">'+
                '<a id="userSelectBtn" class="search-btn iconfont" href="javascript:;"></a></span></div></div>'+
                '<div class="stu-list-box">'+
                '<ul class="stu-list">';
            //学生列表
            var first=0;
            for(var i in userWork){
                if(userWork[i]['SendID']!=0){//只获取提交作业的用户
                    if(first==0){
                        str+='<li class="on" uid ="'+userWork[i]['UserID']+'" sid="'+userWork[i]['SendID']+'">';
                        defaultUserID = userWork[i]['UserID'];
                    }else{
                        str+='<li uid="'+userWork[i]['UserID']+'" sid="'+userWork[i]['SendID']+'">';
                    }
                    str+='<span class="icon-photo iconfont"></span>'+
                        '<span class="username">'+userWork[i]['NewName']+'</span>'+
                        '<span class="stu-test-stat"><i>批改：</i>'+
                        ' <b>'+userWork[i]['CorrectNum']+'</b>/'+testCount+'</span></li>';
                    first=1;
                }
            }
            str+='</ul></div></div>';
            //获取默认试题jquery对象
            var defaultTestObj=$('#user'+defaultUserID).find('span').eq(0);
            str+='<div class="mark-right">';
            //获取学生试题序号状态
            var orderList= $('#user'+defaultUserID).html();
            str+='<div class="mr-ques-wrap">'+
                '<div class="f-roman" id="quickTestOrder">'+
                orderList+'</div>'+
                //'<div class="mr-ques-bar"></div>'+
                '</div>';
            str+='<div id="testWrap">';

            //内容处理
            getCorrectContent(defaultTestObj,str,fromWhere);
        }
        //搜索学生
        $('#userSelectBtn').live('click',function() {
            var name = $('#userSelectName').val();
            if (!name) {
                alert('请输入要搜索的学生名称');
                return false;
            }
            name = $.trim(name);
            var pattern = eval('/' + name + '/');
            var ifFind  = false;
            $('.stu-list li').each(function () {
                var subjectName = $(this).find('.username').text();
                if (pattern.test(subjectName)) {
                    //if($(".stu-list").height()>$(".stu-list-box").height()){//控制滚动条
                    //    var scroll = Math.abs($(this).offset().top-$(this).parent('.stu-list').offset().top);
                    //    $(".stu-list-box").animate({scrollTop:scroll},1000);
                    //}
                    //$(this).trigger('click');
                    $(this).find('.username').addClass('red');
                    ifFind = true;
                }
            });
            if(ifFind==false){
                alert('没有找到该学生!');
                return false;
            }

        });
        //切换题号
        $('#quickTestOrder span').live('click',function(){
            $('#quickTestOrder span').removeClass('check');
            $(this).addClass('check');
            if($('.compound-test-tab').length>0){//有复合题
                 var tid  = $(this).attr('tid');
                 var curTid = $('.compound-test-tab').find('span').eq(0).attr('tid');
                 if(curTid == tid){//同一道题
                     var jid = parseInt($(this).attr('jid'));
                     $('.compound-test-tab').find('span[jid="' + jid + '"]').trigger('click');
                     return false;
                 }
             }
             $('#testWrap').html($.myCommon.loading());
             getCorrectContent(this,'','quickSwitch');
        });
        //切换学生
        $('.stu-list li').live('click',function(){
            $('.stu-list li').removeClass('on');
            $(this).addClass('on');
            $('.stu-list li .username').removeClass('red');

            var sid = $(this).attr('sid');
            var uid = $(this).attr('uid');
            var prevObj = $('#quickTestOrder').find('span.check');
            var prevROid = prevObj.attr('roid');//当前真实序号
            var prevJid = prevObj.attr('jid');//复合题号
            $('#quickTestOrder').html($('#user'+uid).html());
            var curObj=$('#quickTestOrder').find('span[roid="'+prevROid+'"]');
                curObj.addClass('check');

            if(prevJid==0){//单题
                getCorrectContent(curObj,'','quickSwitch');
            }else{//复合
                var jObj = $('.compound-test-tab').find('span.check');
                var jArr = [];
                $(jObj).each(function(){
                    jArr.push($(this).attr('jid'));
                });
                getCorrectContent(curObj,'','quickSwitch',jArr);
            }
        });
        /**
         * 处理批阅内容-获取试题和答案
         * @param domObj 批阅试题的对应的dom对象
         * @param otherHtml 是否加入其它html
         * @param fromWhere 常规批阅还是快速批阅
         * @param jidArr 复合题数组
         * @returns str
         */
        function getCorrectContent(domObj,otherHtml,fromWhere,jidArr){
            if(lock!='') return false;
            if(fromWhere!='quickSwitch') $.myDialog.normalMsgBox(fromWhere,'提示信息',700, $.myCommon.loading());
            var sid  = parseInt($(domObj).attr('sid'));
            var tid  = $(domObj).attr('tid');//不做整化处理
            var ifTest = 1; if(testInfo) ifTest=0;
            lock = fromWhere;
            $.post(U('Work/Work/getTestCorrect?workStyle='+ $.myWorkCommon.workStyle),{'sid': sid,'tid':tid,'wid':workID,'cid':classID,'ifTest':ifTest}, function(data) {
                lock = '';
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                if(data['data'][0]=='success'){
                    var userAnswer = data['data'][1];
                    if(ifTest==1) testInfo = data['data'][2];
                    if(testInfo && testInfo[tid]) {
                        handleCorrectHtml(testInfo[tid],userAnswer,domObj,otherHtml,fromWhere,jidArr);
                    }else{
                        $.myDialog.normalMsgBox(fromWhere,'提示信息',700, '获取试题失败',2);
                        return false;
                    }
                }
            });
        }
        //处理批阅内容html处理
        function handleCorrectHtml(testInfo,userAnswer,that,otherHtml,fromWhere,jidArr){
            var sid  = parseInt($(that).attr('sid'));
            var tid  = $(that).attr('tid');//不做整化处理
            var jid  = parseInt($(that).attr('jid'));
            var oid  = parseInt($(that).attr('oid'));
            var roid = parseInt($(that).attr('roid'));
            var ifcorrect = parseInt($(that).attr('ifcorrect'));
            var str='';
            if(otherHtml) str+=otherHtml;

            var testIndex = 0;//开始索引
            var testIndexEnd  = oid-1;//结束索引
            var realIndexEnd  = -1;//导学案的结束索引
            if($.myWorkCommon.workStyle=='case'){
                realIndexEnd = roid-1;
            }

            if(jid!=0){//复合题
                testIndex = oid-jid;//试题顺序索引位置
                testIndexEnd  = testIndex + parseInt(testInfo['testnum'])-1;
                if($.myWorkCommon.workStyle=='case'){
                    realIndexEnd = roid+ parseInt(testInfo['testnum'])-jid-1;
                }
            }
            //头部
            str+='<div class="ques-details-tab"><b class="on" href="javascript:;">题文</b><span class="toggle"><a href="javascript:;" class="showAnswer on">隐藏答案</a><a href="javascript:;" class="showAnaly on">隐藏解析</a></span>';
            str+='</div>';
            // 复合题小题滚动
            if(jid!=0){
                str+='<div class="compound-test-tab-wrap f-yahei"><a class="compound-prev"><</a><b class="site-tit">包含小题：</b><div class="compound-test-tab">';
                str+='<div class="sub-test-tab">';
                for(var i=testIndex;i<=testIndexEnd;i++){
                    var theObj=$(that).parent().children().eq(i);
                    if(oid==i+1){
                        str+=theObj.addClass('check').prop('outerHTML');
                    }else{
                        str+=theObj.removeClass('check').prop('outerHTML');
                    }
                }
                str+='</div>';
                str+='</div><a class="compound-next">></a></div>';
            }
            //主体
            str+='<div class="mr-ques-scroll-wrap">';
            str+='<div class="mr-ques-details">';
            //内容
            str+='<div class="ques-item-wrap f-roman">';
            str+='<div class="qi-text"><div class="ques-body">';
            str+='<p>';
            str+=$.myTest.removeLeftTag(testInfo['testsplit']['content'],'<p>')+'</div></div>';
            //答案解析用户答案
            str+='<div class="correct-info">';
            var hidden='';
            for(var t=0;t< parseInt(testInfo['testnum']);t++) {
                //小题
                if (jid != 0) {
                    if (oid == t+testIndex+1) {//显示当前题目
                        hidden = '';
                    } else {
                        hidden = 'hidden';
                    }
                    str+='<div id="subTest'+(t+1)+'" class="sub-test-wrap '+hidden+'">';
                    str+='<div class="sub-test">';
                    str+='<b class="sub-test-title">' + (t+testIndex+1) + '．</b>';//小题加入题号
                    str+=testInfo['testsplit'][t];
                    str+='</div>';
                }else{
                    str+='<div id="subTest" class="sub-test-wrap">';
                }

                //用户答案
                str += '<div class="qi-uanswer qi-item-context"><h5 class="qi-item-tit">学生答案:</h5>';
                str += userAnswer[t]['UserAnswer'];
                str += '</div>';
                //系统答案
                str += '<div class="qi-answer qi-item-context"><h5 class="qi-item-tit">参考答案:</h5>';
                str += testInfo['answersplit'][t];
                str += '</div>';
                //系统解析
                str += '<div class="qi-explain qi-item-context"><h5 class="qi-item-tit">解析:</h5>';
                str += testInfo['analyticsplit'][t];
                str += '</div>';
                
                //评分/评语
                str += '<div class="qi-ucorrect mr-comment qi-item-context">';
                if (userAnswer[t]['IfCorrect']=='1') {
                    str += '<div class="mr-c-star f-yahei"><b class="tit">评分：</b>' + $.myTest.starShow(userAnswer[t]['Star']) + '</div><div' +
                        ' class="mr-c-text-done"><div class="context"><b>评语 : </b>' + userAnswer[t]['CorrectContent'] + '</div></div>';
                } else {
                    str += '<div class="mr-c-star f-yahei"><b class="tit">评分：</b>' + $.myTest.starSpan() +
                        '</div><div class="mr-c-text">' +
                        '<textarea name="correctContent" class="correctContent" cols="30" rows="10" placeholder="您的评语对学生理解这道题会有很大的帮助"></textarea>' +
                        '</div>';
                }
                str+='</div>';
                str+='</div>';
            }
            str+='</div></div></div></div>';

            var nextStatus,endIndex;
            if($.myWorkCommon.workStyle=='case'){
                endIndex=realIndexEnd;
            }else{
                endIndex=testIndexEnd;
            }

            if(checkNextStatus(endIndex,sid)){
                nextStatus=1;
            }else{
                nextStatus=0;
            }

            str+='<div class="qm-btm-btn f-yahei">';
            if(ifcorrect==1){
                str+='<a class="qm-btn btn-unable btnCorrect" href="javascript:;" sid="'+sid+'" tid="'+testInfo['testid']+'">'+
                    '<i class="iconfont"></i>'+'已经批阅'+'</a>';
            }else{
                str+='<a class="qm-btn btn-done btnCorrect" href="javascript:;" sid="'+sid+'" tid="'+testInfo['testid']+'">'+
                    '<i class="iconfont"></i>'+'提交批改'+'</a>';
            }

            if(nextStatus==1){
                str+='<a class="qm-btn btn-next btnNext ml10" href="javascript:;" sid="'+sid+'" indexid="'+(endIndex+1)+'">'+
                    '<i class="iconfont"></i>'+'下一道'+'</a>';
            }else{
                str+='<a class="qm-btn btn-unable btnNext ml10" href="javascript:;">'+
                    '<i class="iconfont"></i>'+'没有了'+'</a>';
            }
            str+='</div>';

            if(fromWhere=='correctDiv'){
                $.myDialog.normalMsgBox(fromWhere,'批阅',700, str);
                // 设置弹框结构高度
                var winHeight = $(window).height();
                $(".mr-ques-scroll-wrap").height(winHeight-270);
                $("#correctDiv .content").css("padding","15px 15px 10px");
                $.myDialog.tcDivPosition("correctDiv");
            }else if(fromWhere=='quickSwitch') {
                $('#testWrap').html(str);
                if(jidArr){
                    for(var i in jidArr){
                       $('.compound-test-tab').find('span').each(function(){
                           if(!$(this).hasClass('check')) {
                               if ($(this).attr('jid') == jidArr[i]) {
                                   $(this).trigger('click');
                               }
                           }
                       });
                    }
                }
                //修正高度
                var winHeight = $(window).height(),
                    mrQuesHeight = $(".mr-ques-wrap").outerHeight();
                $(".mr-ques-scroll-wrap").height(winHeight - mrQuesHeight - 234);
                $.myDialog.tcDivPosition("quickcorrectDiv");
            }else{
                str+='</div>';//对应mark-right
                str+='</div>';//对应testWrap
                $.myDialog.normalMsgBox(fromWhere,'快速批阅',750,str);
                $('#quickTestOrder').find('span').eq(0).addClass('check');//默认选中第一个元素
                // 设置弹框结构高度
                var winHeight = $(window).height(),
                    mrQuesHeight = $(".mr-ques-wrap").outerHeight(),
                    mrCompoundHeight = $(".compound-test-tab-wrap").outerHeight();
                var Visible =$(".compound-test-tab-wrap").is(":visible");
                $(".stu-list-box").height(winHeight-240);
                if(Visible){
                    $(".mr-ques-scroll-wrap").height(winHeight-mrQuesHeight-mrCompoundHeight-234);
                }else{
                    $(".mr-ques-scroll-wrap").height(winHeight-mrQuesHeight-234);
                }
                $("#quickcorrectDiv .content").css("padding","0");
                $.myDialog.tcDivPosition("quickcorrectDiv");
                //用户名长度处理
                $(".stu-list-box .username").each(function() {
                    var stuListNameLang = $(this).text().length;
                    if (stuListNameLang > 3) {
                        var usernamestr = $(this).text().substr(0,3);
                        $(this).html(usernamestr + "<span class='f-yahei'>…</span>");
                    }
                });
            }
            //复合题目左右滚动
            var oSubTest = $('.sub-test-tab');
            if(oSubTest.length>0){
                var oSpan       = $('.sub-test-tab').find('span');
                var spanLen     = oSpan.length;
                var singleWidth = oSpan.eq(0).outerWidth(true);
                var oPrev  = $('.compound-prev');
                var oNext  = $('.compound-next');
                var maxNum = Math.floor(oSubTest.width()/singleWidth);
                if(spanLen*singleWidth<=oSubTest.width()){
                    oPrev.addClass('unable');
                    oNext.addClass('unable');
                }else{
                    oPrev.addClass('unable');
                    var time = 0; var num  = 3;//每次移动的个数
                    oPrev.click(function(){
                        if(time>0) time--;
                        clickScroll();
                    });
                    oNext.click(function(){
                        if(time<(spanLen-maxNum)/num) time++;
                        clickScroll();
                    });
                    function clickScroll(){
                        time==0?oPrev.addClass('unable'):oPrev.removeClass('unable');
                        time>=(spanLen-maxNum)/num?oNext.addClass("unable"):oNext.removeClass("unable");
                        oSubTest.animate({left:-time*singleWidth*num},300);
                    }
                }
            }
            //星星分值
            $.myTest.starEvent();
        }
        //复合体小题
        $('.compound-test-tab span').live('click',function(){
            var jid = $(this).attr('jid');
            var ifcorrect = $(this).attr('ifcorrect');
            if($(this).hasClass(('check'))){
                $(this).removeClass('check');
                $('#subTest'+jid).slideUp(500);

            }else{
                $(this).addClass('check');
                $('#subTest'+jid).slideDown(800);
            }
            var needCorrect=false;
            $('.compound-test-tab span.check').each(function(){
                if($(this).attr('ifcorrect')==0){
                    needCorrect=true;
                }
            });
            var winDiv = '';
            winDiv = checkFromWhere();
            if(needCorrect){
                $('#'+winDiv+' .btnCorrect').removeClass('btn-unable').addClass('btn-done').html('<i' +
                    ' class="iconfont"></i>'+'提交批改');
            }else{
                $('#'+winDiv+' .btnCorrect').removeClass('btn-done').addClass('btn-unable').html('<i' +
                    ' class="iconfont"></i>'+'已批改');
            }
        });
        //检测下一道
        function checkNextStatus(indexEnd,sid){
            if($('#tr'+sid).find('.cw-td-info').find('span').length>indexEnd+1){
                return true;
            }
            return false
        }
        //下一道处理
        $('.btn-next').live('click',function(){
            var index = $(this).attr('indexid');
            var sid   = $(this).attr('sid');
            var winDiv='';
            winDiv=checkFromWhere();
            if(winDiv=='correctDiv'){
                $('#tr'+sid).find('.cw-td-info').find('span:eq('+index+')').trigger('click');
                return false;
            }
            $('#quickTestOrder').find('span:eq('+index+')').trigger('click');
        });
        //提交事件
        $('.btn-done').live('click',function(){
            if(lock!='') return false;
            var winDiv= '' ;
            winDiv = checkFromWhere();
            var sid = $(this).attr('sid');
            var tid = $(this).attr('tid');
            var scoreArr=[];
            var contentArr=[];
            var jidArr=[];
            var ifMany=false;
            var ifCorrectAll = true;
            if($('.sub-test-wrap').length==1){//单题
                var curJDom='';
                if(winDiv=='correctDiv'){
                    curJDom=$('#tr'+sid).find('.cw-td-info').find('span.check');
                }else{
                    curJDom=$('#quickTestOrder').find('span.check');
                }

                var score = $('#'+winDiv+' .quesscore').text();
                var content = $('#'+winDiv+' .correctContent').val();
                var jid = curJDom.attr('jid');
                if(content==''){
                    alert('请输入评语');
                    $('#'+winDiv+' .correctContent').focus();
                    return false;
                }
                scoreArr.push(score);
                contentArr.push(content);
                jidArr.push(jid);
            }else{
                if($('.sub-test-wrap:visible').length==0){
                    alert('请选择批阅的小题');
                    return false;
                }
                $('.compound-test-tab span.check').each(function(i){
                    if($(this).attr('ifcorrect')==0){
                        var jid = $(this).attr('jid');
                        jidArr.push(jid);
                        var score=$('.sub-test-wrap:visible').eq(i).find('.quesscore').text();
                        var content= $('.sub-test-wrap:visible').eq(i).find('.correctContent').val();
                        if(content==''){
                            ifCorrectAll=false;
                        }
                        scoreArr.push(score);
                        contentArr.push(content);
                    }
                });
                ifMany=true;
            }
            if(!ifCorrectAll){
                alert('您还有试题未批改呢,批改完了再提交吧!');
                return false;
            }
            lock = 'correctTest';
            $.post(U('Work/Work/correctTest?workStyle='+ $.myWorkCommon.workStyle),{ 'sid': sid,'tid':tid,'jid':jidArr,'wid':workID,'score':scoreArr,'content':contentArr}, function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                if(data['data'][0]=='success'){
                    lock = '';
                    if(ifMany){
                        var contentOrder=0;
                        $('.compound-test-tab span.check').each(function(i){
                            if($(this).attr('ifcorrect')==0){
                                var roid=parseInt($(this).attr('roid'))-1;
                                if($(this).attr('ifchoose')==0){
                                    if(scoreArr[i]==10){
                                        $(this).addClass('bc-green');
                                        $('#tr'+sid).find('.cw-td-info').find('span:eq('+roid+')').addClass('bc-green');
                                        if('quickcorrectDiv'==winDiv){
                                            $('#quickTestOrder').find('span:eq('+roid+')').addClass('bc-green');
                                        }
                                    }else if(scoreArr[i]==0){
                                        $(this).addClass('bc-red');
                                        $('#tr'+sid).find('.cw-td-info').find('span:eq('+roid+')').addClass('bc-red');
                                        if('quickcorrectDiv'==winDiv){
                                            $('#quickTestOrder').find('span:eq('+roid+')').addClass('bc-red');
                                        }
                                    }else{
                                        $(this).addClass('bc-orange');
                                        $('#tr'+sid).find('.cw-td-info').find('span:eq('+roid+')').addClass('bc-orange');
                                        if('quickcorrectDiv'==winDiv){
                                            $('#quickTestOrder').find('span:eq('+roid+')').addClass('bc-orange');
                                        }
                                    }
                                }
                                $(this).attr('ifcorrect','1');
                                $('#tr'+sid).find('.cw-td-info').find('span:eq('+roid+')').attr('ifcorrect','1');

                                var temp='<div class="mr-c-text-done">'+
                                    '<div class="context"><b>评语 : </b>'+contentArr[contentOrder]+'</div>'+
                                    '</div>';
                                $('.sub-test-wrap:visible').eq(i).find('.mr-c-text').replaceWith(temp);
                                contentOrder++;
                            }
                        });
                    }else{
                        var checkSpan = '';
                        var listSpan  = false;
                        if('quickcorrectDiv'==winDiv){
                            checkSpan = $('#quickTestOrder').find('span.check');
                            if(checkSpan.attr('ifchoose')==0){
                                var temproid = checkSpan.attr('roid');
                                    listSpan = $('#tr'+sid).find('.cw-td-info').find('span[roid="'+temproid+'"]');
                                if(scoreArr[0]==10){
                                    checkSpan.addClass('bc-green');
                                    listSpan.addClass('bc-green');
                                }else if(scoreArr[0]==0){
                                    checkSpan.addClass('bc-red');
                                    listSpan.addClass('bc-red');
                                }else{
                                    checkSpan.addClass('bc-orange');
                                    listSpan.addClass('bc-orange');
                                }
                            }
                        }else{
                            checkSpan = $('#tr'+sid).find('.cw-td-info').find('span.check');
                            if(checkSpan.attr('ifchoose')==0){
                                if(scoreArr[0]==10){
                                    checkSpan.addClass('bc-green');
                                }else if(scoreArr[0]==0){
                                    checkSpan.addClass('bc-red');
                                }else{
                                    checkSpan.addClass('bc-orange');
                                }

                            }
                        }
                        checkSpan.attr('ifcorrect','1');
                        if(listSpan){
                            listSpan.attr('ifcorrect','1');
                        }
                        var temp='<div class="mr-c-text-done">'+
                            '<div class="context"><b>评语 : </b>'+contentArr[0]+'</div>'+
                            '</div>';
                        $('.sub-test-wrap').find('.mr-c-text').replaceWith(temp);
                    }
                    $('#'+winDiv+' .btn-done').html('<i class="iconfont"></i>已批阅').addClass("btn-unable").removeClass('btn-done');
                    for(var k in userWork){//本地缓存
                        if(userWork[k]['SendID']==sid){
                            userWork[k]['CorrectNum']=parseInt(userWork[k]['CorrectNum'])+parseInt(jidArr.length);
                        }
                    }
                    //快速批阅窗口
                    if('quickcorrectDiv'==checkFromWhere()){
                        var curNumObj=$('.stu-list').find('li[sid="'+sid+'"]').find('.stu-test-stat b');
                         var curCorNum=parseInt(curNumObj.html());
                         curCorNum+=parseInt(jidArr.length);
                         curNumObj.html(curCorNum);
                    }
                }
            });
        });
        //判断是常规批阅还是快速批阅
        function checkFromWhere(){
            var winDiv = '';
            if($('#quickcorrectDiv').length>0){
                winDiv='quickcorrectDiv';
            }else{
                winDiv='correctDiv'
            }
            return winDiv;
        }
    },
    //评论相关
    commentAbout:function(){
        //写评语
        $('.comment').live('click',function(){
            if(!userWork){
                return false;
            }
            var sid=$(this).attr('sid');
            if(sid==0){
                if(!userWork){
                    $.myDialog.showMsg('请稍候,数据加载中...',1,3);
                    $.myWorkCheckDetail.loadUserWork();
                    return false;
                }
            }
            var oid=undefined;
            if(sid!=0){
               oid=$(this).attr('oid');
            }
            $.myDialog.normalMsgBox('commentdiv','信息提示',550,$.myCommon.loading());
            getCommentWindow(sid,oid);
        });
        //载入评语界面
        function getCommentWindow(sid,oid){
            var tit='';
            var tmpStr='';
            if(sid==0){
                if(userWork){
                    tit='一键写评语';
                    var userlist='';
                    var status='';
                    var disable='';
                    var inputDisable='';
                    for(var i in userWork){
                        status='';
                        if(userWork[i]['Status']==-1){
                              status='<span class="red">(未做)</span>';
                        }else if(userWork[i]['Status']==2){
                              status='<span class="green">(已评)</span>';
                        }
                        if(status!=''){
                            disable = ' class="disable"';
                            inputDisable = ' disabled="true"';
                        }else{
                            disable = '';
                            inputDisable = '';
                        }
                        userlist+='<li isselect="0"'+disable+'uid="'+userWork[i]['UserID']+'"><input type="checkbox"'+inputDisable+' sid="'+userWork[i]['SendID']+'">'+userWork[i]['NewName']+status+'</li>';
                    }
                }
                tmpStr='<div class="commentCon">'+
                    '<div class="commentTit">选择学生</div>'+
                    '<div class="userlist"><ul>'+userlist+'</ul></div>'+
                    '<div class="commentTit">评语</div>'+
                    '<div><textarea class="commentArea">请输入您的评语...</textarea></div>';
            }else{
                tit='写评语';
                var uname=$('#tr'+sid+' td:eq(0) .cw-td-name').html();
                tmpStr='<div class="commentCon">'+
                    '<div class="commentTit">评语 <small style="color:#009fe9;">'+uname+'</small></div>'+
                    '<div><textarea class="commentArea">请输入您的评语...</textarea></div>'+
                    '<input name="comment_id" id="comment_id" type="hidden" oid="'+oid+'"value="'+sid+'" />';
            }
            $.myDialog.normalMsgBox('commentdiv',tit,550,tmpStr,3);
        }
        //首次聚焦清空教师评语提示语
        $('.commentArea').live('focus',function(){
            if($(this).text().indexOf('请输入您的评语')!=-1){
                $(this).text('');
            }
        });
        //写评语确定
        $('#commentdiv .normal_yes').live('click',function(){
            var sid=0;
            var uid='';
            var usid=[];
            var content=$.trim($('.commentArea').val());
            if(content=='' || content==null){
                $.myDialog.normalMsgBox('msgdiv','提示信息',450,'请填写评语！',2);
                return false;
            }

            if($('#comment_id').length>0) {
                var oid = $('#comment_id').attr('oid');
                    sid = $('#comment_id').val();
            }

            if(sid==0){
                $('.userlist li').each(function(){
                    if($(this).attr('isselect')=='1'){
                        if(uid=='') {
                            uid=$(this).attr('uid');
                        } else {
                            uid+=','+$(this).attr('uid');
                        }
                        usid.push($(this).children('input').attr('sid'));
                    }
                });
                if(uid==''){
                    $.myDialog.normalMsgBox('msgdiv','提示信息',450,'请选择学生！',2);
                    return false;
                }
            }
            $.post(U('Work/Work/setComment?workStyle='+ $.myWorkCommon.workStyle),{'sid':sid,'uid':uid,'content':content,'workID':workID,'classID':classID,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    alert(data['data']);
                    return false;
                }

                var date=new Date();
                date=date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDay()+' '+date.getHours()+':'+date.getMinutes();

                if(sid){
                    $('#tr'+sid+' .comment').removeClass('comment').addClass('commented round-btn-gray').html('已评论');
                    userWork[oid]['Comment']=content;
                    userWork[oid]['CheckTime']=date;
                    userWork[oid]['Status']=2;
                }

                if(uid && usid){//一键评论setset
                    for(var i in usid){
                        $('#tr'+usid[i]+' .comment').removeClass('comment').addClass('commented round-btn-gray').html('已评论');
                    }
                    if (!usid.indexOf) {
                        usid.prototype.indexOf = function (obj) {
                            for (var i = 0; i < this.length; i++) {
                                if (this[i] == obj) {
                                    return i;
                                }
                            }
                            return -1;
                        }
                    }
                    for(var j in userWork){
                           if(usid.indexOf(userWork[j]['SendID'])!==-1){
                               userWork[j]['Comment']=content;
                               userWork[j]['CheckTime']=date;
                               userWork[j]['Status']=2;
                           }
                    }
                }
                $('#commentdiv .tcClose').click();
            });
        });
        //选择评论用户
        $('.userlist li').live('click',function(){
            if($(this).find('span').length==0){
                if($(this).attr('isselect')=='0'){
                    $(this).attr('isselect','1');
                    $(this).find('input').attr({'checked':true});
                }else{
                    $(this).attr('isselect','0');
                    $(this).find('input').attr({'checked':false});
                }
            }
        });
    }
}
