//校本题库
$.CustomMicroList = {
    page : 1,
    lock : 'load',
    subjectID : '',
    us : null,
    init : function(){
        $(document).bind("selectstart",function(){return false;});
        //默认科目
        this.subjectID=Cookie.Get("SubjectId");
        this.us = this.getSearcher();
        this.showAttributes();
        this.initDivBoxHeight();
        $(window).bind("resize",function() { $.CustomMicroList.initDivBoxHeight();});
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
        this.selectPageIcon();
        this.dataOtherContent();
        this.edit();
        this.del();
    },

    getSearcher : function(){
        var that = this;
        return $.extend(UnionSearch, {
            url : U('Custom/MicroClass/getMicroList'),
            params : {
                knowledge : 0,   
                subject : that.subjectID,
                grade :  0,
                page : 1
            },
            requestBeforeHandler : function(id){
                if(this.lock){
                    alert('数据加载中，请稍候！');
                    return false;
                }
                this.lock = true;
                if('page' != id){
                    this.params.page = 1;
                }
                this.params['rand'] = Math.random();
                //that.clearpage();
                $('#queslistbox').html('<p class="list_ts"><span class="ico_dd">数据加载中请稍候...</span></p>');
                return true;
            },
            callback : function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                that.us.lock = false;
                var total = 0, page = 0, recordTotal = 0;
                if(typeof(data['data']) !== 'string'){
                    page = that.us.params.page;   //当前页
                    recordTotal = data['data'][1];//总记录数
                    total = Math.ceil(recordTotal / data['data'][2]); //总页数
                }
                that.showPage(total,page,recordTotal);
                that.us.addElement('#pagelistbox .page', 'click', {
                    id : 'page', 
                    param : 'val'
                });
                var template = $('#template');
                if(data['data'][0] && data['data'][0].length == 0){
                    $('#queslistbox').html();
                }else{
                    var temp = new Template();
                    $('#queslistbox').html(temp.render(template.html(),data['data']));
                }
            }
        });
    },

    //校本题库在列表页面初期，需加载的部分属性
    showAttributes : function(){
        var that = this;
        if(that.subjectID){
            var str='';
            //显示学科及属性
            $.post(U('Custom/MicroClass/getGradeInit'),{'id':that.subjectID,'m':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    lock='';
                    return false;
                }
                $('#loca_text').html('&nbsp;校本微课&nbsp;>&nbsp;'+data['data'][0]);
                $('#categorytop').append('('+data['data'][0]+')');
                var template = new Template();
                $('#filterbox').html(template.render($('#selectionTemplate').html(),data));
                $('#treecon').html(template.render($('#treeTemplate').html(),data));

                $('#treecon').tree({
                    expanded: 'li:first'
                });

                $('#treecon .tree-parentspan').each(function(){
                    $(this).click(function(){
                        $(this).next().css('background-color','#3333ff');
                    });
                });

                that.us.addElement('#categorytop', 'click', {
                    id : 'knowledge',
                    param : 'val'
                });

                //知识点分页查询
                that.us.addElement('#treecon .treeTarget', 'click', {
                    id : 'knowledge', 
                    param : 'val',
                    beforeHandler : function(){
                        $(this).css('background-color','#3333ff').siblings('.treeTarget').css('background-color','#3399ff');
                        return true;
                    }
                });

                //年级查询
                that.us.addElement('#quesgradeselect .target', 'click', {
                    id : 'grade', 
                    param : 'val',
                    beforeHandler : function(){
                        $(this).addClass('button_current').siblings('.target').removeClass('button_current');
                        return true;
                    }
                });

                //前后翻页查询
                that.us.addElement('#pagediv .pageTarget','click', {
                    id : 'page',
                    param : 'val',
                    beforeHandler : function(){
                        var current = $(this);
                        //获取当前页
                        var currentPage = parseInt($('#curpage').html());
                        //向前翻页，同时页数当前页数不等于1时
                        if(current.hasClass('prev_page') && currentPage > 2){
                            currentPage = currentPage - 1;
                            current.attr('val', currentPage);
                        }else if(current.hasClass('next_page')){
                            var total = parseInt($('#pagecount').html())
                            if(currentPage <= total - 1){
                                currentPage = currentPage + 1;
                                current.attr('val', currentPage);
                            }
                        }
                        that.us.params.page = currentPage;
                        return true;
                    }
                });

                $('#quesgradeselect .target').eq(0).trigger('click');
            });
        }
    },

    initDivBoxHeight : function() { 
        var a = $(window).width();
        var b = $(window).height();
        var c = 0;
        $(".mleft,.mright").height(b - c - 2);
        $("#rightdiv").width(a-$("#leftdiv").outerWidth());
        $("#leftdiv").height(b - c-2);
        $("#rightdiv").height(b - c-2);
        $("#categorytreebox").height(b - c - $('#categorytop').outerHeight());
    },
    //此函数是三角分页的相识函数，但是实现有差异，所以重新编写
    selectPageIcon : function(){
        var that = this;
        $("#selectpageicon").live('mouseenter',function() {
            var page = parseInt($("#curpage").text());
            var pagecount = parseInt($("#pagecount").text());
            if (pagecount <= 1) { return; }

            html = [];
            html.push("<div id=\"quicktopage\" p='page' style=\"top:" + ($(this).height() - 1) + "px;\">");
            var i; var max = 20;
            var spacing = (pagecount > max) ? parseInt(pagecount / max) : 1;
            for (i = 1; i <= pagecount; i += spacing) {
                html.push("<a val='"+i+"' class=\"" + ((page == i) ? "current" : "") + "\">No." + i + "</a>");
            }
            if (i - spacing < pagecount) {
                html.push("<a class=\"" + ((page == pagecount) ? "current" : "") + "\">No." + pagecount + "</a>");
            }
            html.push("</div>");
            $(this).append(html.join(""));
            that.us.addElement('#quicktopage a', 'click', {
                id : 'page',
                param : 'val'
            });
        }).live('mouseleave',function() {
             $("#quicktopage").empty().remove();
        });
    },
    //点击试题内容时，显示的相关的试题答案，解析等内容
    dataOtherContent : function(){
        $('.showMicro').live('click',function(){
            var mid=$(this).attr('mid');
            var startid=$(this).attr('startid');
            
            var html='<iframe width="650" frameborder="0" height="420" src="/Custom/MicroClass/play/mid/'+mid+'/startid/'+startid+'"></iframe>';
            var idName='mcvideo';
            $.myDialog.normalMsgBox(idName,'微课视频',680,html,5);
        });
        $('.quesinfo').live('click',function(){
            var adiv=$(this).next('.quesanswer');
            var tid=adiv.attr('tid');
            if(tid=='0') return;
            if($(this).next('.quesanswer').css('display')=='block'){
                $(this).next('.quesanswer').css('display','none');
                $(this).parent().find('.quesparse').css('display','none');
                $(this).parent().find('.quesremark').css('display','none');
            }else{
                if($(this).next('.quesanswer').attr('show')==0){
                    $.post(U('Custom/CustomTestStore/getOneTestById'),{'id':tid,'width':500,'s':Math.random()},function(data){
                        if($.myCommon.backLogin(data)==false){
                            return false;
                        };
                        if(data['data'][0]=='success'){
                            var str='<div class="quesanswer_tit">答案</div><p>'+
                                data['data'][1]['answer'];
                            if(data['data'][1]['analytic'] && data['data'][1]['analytic']!='</p>'){
                                str+='<div class="quesanswer_tit">解析</div><p>'+data['data'][1]['analytic'];
                            }
                            if(data['data'][1]['kllist']){
                                str+='<div class="quesanswer_tit">知识点</div><p>'+data['data'][1]['kllist'];
                            }
                            if(data['data'][1]['remark'] && data['data'][1]['remark']!='</p>'){
                                str+='<div class="quesanswer_tit">备注</div><p>'+data['data'][1]['remark'];
                            }
                            adiv.html(str);
                            adiv.attr('show',1);
                        }else{
                            alert(data['data']);
                        }
                    });
                }
                $(this).next('.quesanswer').css('display','block');
                $(this).parent().find('.quesparse').css('display','block');
                $(this).parent().find('.quesremark').css('display','block');
            }
        });
    },
    //试题编辑
    edit : function(){
        $('.customTestEdit').live('click', function(){
            var that = $(this);
            var id = that.attr("mid");
            window.location.href = U('MicroClass/edit/mid/'+id);
        });
    },
    //删除试题
    del : function(){
        $('.deletion').live('click',function(){
            if(!window.confirm('您确定删除该微课？')){
                return false;
            }
            var that = $(this);
            var data = {};
            data['mid'] = that.attr('mid');
            
            $.post(U('Custom/MicroClass/del'),data,function(result){
                if($.myCommon.backLogin(result)==false){
                    return false;
                };
                var data = result['data']
                if(data == 'success'){
                    alert('微课已删除！');
                    $('#quesgradeselect .button_current').click();
                    return false;
                }
                alert(data);
            })
        });
    },
    //初始化相关分页数据
    clearpage : function(){
        $("#pagelistbox").html('');
        $("#quescount").html('?');
        $("#curpage").html('?');
        $("#selectpageicon").css({display: "none" });
        $("#pagecount").html('?');
    },
    
    showPage : function(total,prepage,recordTotal){
        if(total == 0){
            $('#pagediv').hide();
            return false;
        }
        $('#pagediv').show();
        var lastpagelist='<div class="pagebox">';
        var showPageList = 10;
        var index = Math.floor((prepage - 0.123) / showPageList);
        var i = showPageList * index;
        var end = i + showPageList;
        if(prepage > 1){
            lastpagelist+='<a class="page" val="1" href="javascript:void(0);">首页</a>';
        }else{
            lastpagelist+='<span class="disabled">首页</span>';
        }
        if(index > 0){
            lastpagelist += '<a class="page" val="'+ (i) +'" href="javascript:void(0);">上' + showPageList + '页</a>'
        }
        while(++i <= end && i <= total){
            if(i == prepage){
                lastpagelist+='<span class="current">'+ i +'</span>';
            }else{
                lastpagelist+='<a class="page" val="'+ i +'" href="javascript:void(0);">'+i+'</a>';
            }
        }
        if(i <= total){
            lastpagelist+='<a val="'+ (i) +'" href="javascript:void(0);" class="page">下' + showPageList + '页</a>';
        }
        if(prepage < total){
            lastpagelist+='<a class="page" val="'+ total +'" href="javascript:void(0);" title="最后1页">末页</a>';
        }else{
            lastpagelist+='<span class="disabled">末页</span>';
        }
        lastpagelist+='</div>';
        $("#pagelistbox").html(lastpagelist);
        $("#quescount").html(recordTotal);
        $("#curpage").html(prepage);
        $("#selectpageicon").css({display: (total == 1 ? 'none' : "inline-block")});
        $("#pagecount").html(total);
    }
};