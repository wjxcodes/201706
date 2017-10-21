//手动组卷通用方法
jQuery.thisCommon = {
    //竖型分割线鼠标事件
    uiResizable:function(){
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
    }
}
//知识点选题方法
jQuery.knowledgeTest = {
    //初始化
    init:function(){
        this.loadClassList(); //载入分类列表
        this.loadTestEvent(); //载入试题事件
        this.initDivBoxHeight(); //重置页面框架
        this.knowledgeTreeClick(); //知识点目录树点击变色
        this.loadPageEvent(); //载入分页事件

        $.thisCommon.uiResizable(); //知识点树鼠标事件
        $(window).bind("resize",function() {$.knowledgeTest.initDivBoxHeight();});
    },
    //载入分页事件
    loadPageEvent:function(){
        this.jumpToPage(); //页码分页跳转
        this.goPrevPage(); //上一页
        this.goNextPage(); //下一页
        this.quickToPage(); //快速分页跳转
    },
    //载入试题事件
    loadTestEvent:function(){
        this.getTest(); //载入试题
        this.showTestByOrder(); //载入排序事件
        this.showTestByDiff(); //按照难度查看试题
        this.showTestByKnowledge(); //按照知识点查看试题
        this.showTestBySource(); //按照文档来源查看试题
        this.knowledgeAllTest(); //显示全部知识点试题
        this.showTestByTypes(); //根据试题类型查看试题
        this.showTestByDocTypes(); //根据文档类型查看试题
    },
    //载入分类列表
    loadClassList:function(){
        var subjectName= $.myCommon.getSubjectNameFromParent();
        $('#loca_text').html(subjectName);
        //显示学科及属性
        $.post(U('Manual/Index/getZsdInit'),{'id':subjectID,'m':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                lock='';
                return false;
            }
            $('#categorytop').append('('+subjectName+')');

            var str='<div class="filterbox_li"><table border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="45">题型</td><td><span id="questypeselect"><a href="javascript:void(0);" class="button_current" qdid="0">全部</a>';
            for(var i in data['data'][1]){
                str+='<a href="javascript:void(0);" class="button" qdid="'+data['data'][1][i]['TypesID']+'">'+data['data'][1][i]['TypesName']+'</a>';
            }
            str+='</span></td><td> </td></tr></tbody></table></div><div class="filterbox_li"><table  border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="65">难度系数</td><td><span id="quesdiffselect"><a href="javascript:void(0);" class="button_current" qdid="0">全部</a>';
            for(var i in data['data'][2]){
                str+='<a href="javascript:void(0);" class="button" qdid="'+data['data'][2][i]['DiffID']+'" title="'+data['data'][2][i]['DiffArea']+'">'+data['data'][2][i]['DiffName']+'</a>';
            }
            str+='</span></td><td> </td></tr></tbody></table></div><div class="filterbox_li" ><table  border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="65">文档类型</td><td><span id="quesdoctypeselect"><a href="javascript:void(0);" class="button_current" qdid="0">全部</a>';
            for(var i in data['data'][4]){
                str+='<a href="javascript:void(0);" class="button" qdid="'+data['data'][4][i]['TypeID']+'" title="'+data['data'][4][i]['TypeName']+'">'+data['data'][4][i]['TypeName']+'</a>';
            }
            //文档来源
            str+='</span></td><td> </td></tr></tbody></table></div><div class="filterbox_li" style="background:none"><table  border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="65">文档来源</td><td><span id="quesdocSourceselect"><a href="javascript:void(0);" class="button_current" qdid="0">全部</a>';
            for(var i in data['data'][5]){
                str+='<a href="javascript:void(0);" class="button" qdid="'+data['data'][5][i]['SourceID']+'" title="'+data['data'][5][i]['SourceName']+'">'+data['data'][5][i]['SourceName']+'</a>';
            }
            str+='</span></td><td> </td></tr></tbody></table></div>';

            $('#filterbox').html(str);

            //显示相应id的知识点
            str='';
            for(var i=0;i<data['data'][3].length;i++){
                str+='<li><span></span><a href="#" class="zsd" qyid="'+data['data'][3][i]['KlID']+'" title="'+data['data'][3][i]['KlName']+'">'+ $.myCommon.cutString(data['data'][3][i]['KlName'],26,'...')+'</a>';
                if(data['data'][3][i]['sub']){
                    str+='<ul>';
                    for(var j=0;j<data['data'][3][i]['sub'].length;j++){
                        str+='<li><span></span><a href="#" class="zsd" qyid="'+data['data'][3][i]['sub'][j]['KlID']+'" title="'+data['data'][3][i]['sub'][j]['KlName']+'">'+$.myCommon.cutString(data['data'][3][i]['sub'][j]['KlName'],24,'...')+'</a>';
                        if(data['data'][3][i]['sub'][j]['sub']){
                            str+='<ul>';
                            for(var k=0;k<data['data'][3][i]['sub'][j]['sub'].length;k++){
                                str+='<li><span></span><a href="#" class="zsd" qyid="'+data['data'][3][i]['sub'][j]['sub'][k]['KlID']+'" title="'+data['data'][3][i]['sub'][j]['sub'][k]['KlName']+'">'+$.myCommon.cutString(data['data'][3][i]['sub'][j]['sub'][k]['KlName'],22,'...')+'</a></li>';
                            }
                            str+='</ul>';
                        }
                        str+='</li>';
                    }
                    str+='</ul>';
                }
                str+='</li>';
            }
            $('#treecon').html(str);

            $('#treecon').tree({
                expanded: 'li:first'
            });
        });
    },
    //页面跳转函数
    gotoPage:function(num){
        $("#rightdiv").scrollTop(0);
        if(num<1 && page==1){
            return false;
        }
        page=num;
        $.knowledgeTest.getTest();//获取试题
    },
    //上一页
    goPrevPage:function(){
        $('.prev_page').live('click',function(){
            if($.myPage.goPrevPage()==false) return false;
            $.knowledgeTest.gotoPage(page);
        });
    },
    //下一页
    goNextPage:function(){
        $('.next_page').live('click',function(){
            if($.myPage.goNextPage()==false) return false;
            $.knowledgeTest.gotoPage(page);
        });
    },
    //页面跳转事件绑定
    jumpToPage:function(){
        $('.pagebox a').live('click',function(){
            $.knowledgeTest.gotoPage(parseInt($(this).attr('page')));
        });
    },
    //分页快速跳转
    quickToPage:function(){
        $('#quicktopage a').live('click',function(){
            $.myPage.quickToPage($(this));
            $.knowledgeTest.gotoPage(page);
        });
    },
    //按照知识点查看试题
    showTestByKnowledge:function(){
        $('.zsd').live('click',function(){
            $('.zsd').css('background-color','#3366ff');
            $('.zsd').attr('jl','');
            $(this).css('background-color','#3399ff');
            $(this).attr('jl','down');
            page=1;
            $.knowledgeTest.getTest();//获取试题
        });
    },
    //知识点目录树点击变色
    knowledgeTreeClick:function(){
        $('.tree span').live('click',function(){
            $('.zsd').css('background-color','#3399ff');
            $('.zsd').attr('jl','');
            $(this).next().css('background-color','#3399ff');
            $(this).next().attr('jl','down');
        });
    },
    //显示全部知识点试题
    knowledgeAllTest:function(){
        $('#categorytop').css('cursor','pointer').live('click',function(){
            page=1;
            $('.zsd').css({'background-color':'#3333ff','border-color':'#3E5061','font-weight':'normal'});
            $('.zsd').attr('jl','');
            $.knowledgeTest.getTest();//获取试题
        });
    },
    //根据试题类型查看试题
    showTestByTypes:function(){
        $('#questypeselect a').live('click',function(){
            $(this).setButtonBackground();
            $.knowledgeTest.getTest();//获取试题
        });
    },
    //根据文档类型查看试题
    showTestByDocTypes:function(){
        $('#quesdoctypeselect a').live('click',function(){
            $(this).setButtonBackground();
            $.knowledgeTest.getTest();//获取试题
        });
    },
    //根据难度查看试题
    showTestByDiff:function(){
        $('#quesdiffselect a').live('click',function(){
            $(this).setButtonBackground();
            $.knowledgeTest.getTest(); //获取试题
        });
    },
    //根据文档来源查看试题
    showTestBySource:function(){
        $('#quesdocSourceselect a').live('click',function(){
            $(this).setButtonBackground();
            $.knowledgeTest.getTest(); //获取试题
        });
    },
    //根据排序查看试题
    showTestByOrder:function(){
        $('#list_px a.button').live('click',function(){
            page=1;
            $(this).aOrderColor(); //排序变色
            $.knowledgeTest.getTest(); //获取试题
        });
    },
    //获取难度当前值
    getDiff:function(){
        return $('#quesdiffselect').getButtonSelected('qdid');
    },
    //获取文档类型当前值
    getDocType:function(){
        return $('#quesdoctypeselect').getButtonSelected('qdid');
    },
    //获取排序当前值
    getOrder:function(){
        return $('#list_px').getButtonSelected('type');
    },
    //获取文档来源当前值
    getSource:function(){
        return $('#quesdocSourceselect').getButtonSelected('qdid');
    },
    //获取知识点当前值
    getKnowledge:function(){
        var output=0;
        $('.zsd').each(function(){
            if($(this).attr('jl')=='down'){
                output=$(this).attr('qyid');
                return true;
            }
        });
        return output;
    },
    //获取题型当前值
    getTypes:function (){
        return $('#questypeselect').getButtonSelected('qdid');
    },
    //ajax获取试题
    getTest:function(){
        var kid=this.getKnowledge();
        var tid=this.getTypes();
        var dtid=this.getDocType();
        var dif=this.getDiff();
        var order=this.getOrder();
        var sourceID=this.getSource();
        $("#queslistbox").html('<p class="list_ts"><span class="ico_dd">试题加载中请稍候...</span></p>');

        lock='test';
        $.post(U('Home/Index/getTestList'),{'sid':subjectID,'kid':kid,'tid':tid,'dtid':dtid,'dif':dif,'o':order,'page':page,'sourceid':sourceID,'rand':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                $("#queslistbox").html('<p class="list_ts">抱歉！暂时没有符合条件的试题，请尝试更换查询条件。</p>');
                $.myPage.showPage(0,1,page,1);
                lock='';
                return false;
            };
            if(data['data'][1]!=0) $("#queslistbox").html($.myTest.showTest(data['data'][0]));
            else $("#queslistbox").html('<p class="list_ts">抱歉！暂时没有符合条件的试题，请尝试更换查询条件。</p>');
            $.myPage.showPage(data['data'][1],data['data'][2],page,1);
            lock='';
        });
    },
    //重置框架
    initDivBoxHeight:function() {
        var a = $(window).width();
        var b = $(window).height();
        var c = 0;
        $(".mleft,.mright").height(b - c - 2);
        $("#rightdiv").width(a-$("#leftdiv").outerWidth());
        $("#leftdiv").height(b - c-2);
        $("#rightdiv").height(b - c-2);
        $("#categorytreebox").height(b - c - $('#categorytop').outerHeight());
    }
}
//章节选题方法
jQuery.chapterTest = {
    subjectName:'', //学科名称
    //初始化
    init:function(){
        this.loadClassList(); //载入分类列表
        this.loadTestEvent(); //载入试题事件
        this.initDivBoxHeight(); //重置页面框架
        this.loadPageEvent(); //载入分页事件

        $.thisCommon.uiResizable(); //知识点树鼠标事件
        $(window).bind("resize",function() {$.chapterTest.initDivBoxHeight();});
    },
    //载入分页事件
    loadPageEvent:function(){
        this.jumpToPage(); //页码分页跳转
        this.goPrevPage(); //上一页
        this.goNextPage(); //下一页
        this.quickToPage(); //快速分页跳转
    },
    //载入试题事件
    loadTestEvent:function(){
        this.showChapterBBEvent(); //显示版本事件
        this.showChapterBB(); //显示章节版本

        this.treeSpanClick(); //树形结构变色
        this.treeCategoryClick(); //显示所有章节试题事件

        this.showTestByOrder(); //载入排序事件
        this.showTestByDiff(); //按照难度查看试题
        this.showTestByChapter(); //按照知识点查看试题
        this.showTestByTypes(); //根据试题类型查看试题
        this.showTestByDocType(); //根据文档类型查看试题
        this.showTestByAbility(); //根据能力值查看试题
        this.showTestBySource(); //根据文档来源查看试题
    },
    //载入分类列表
    loadClassList:function(){
        //显示学科及属性
        $.chapterTest.subjectName=$.myCommon.getSubjectNameFromParent();
        $.post(U('Home/Index/getTypes'),{'id':subjectID,'style':1,'m':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            };
            var str='';
            str='<div class="filterbox_li"><table border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="45">题型</td><td><span id="questypeselect"><a href="javascript:void(0);" class="button_current" qdid="0">全部</a>';
            for(var i in data['data'][1]){
                str+='<a href="javascript:void(0);" class="button" qdid="'+data['data'][1][i]['TypesID']+'">'+data['data'][1][i]['TypesName']+'</a>';
            }
            str+='</span></td><td> </td></tr></tbody></table></div><div class="filterbox_li"><table  border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="65">难度系数</td><td><span id="quesdiffselect"><a href="javascript:void(0);" class="button_current" qdid="0">全部</a>';
            for(var i in data['data'][2]){
                str+='<a href="javascript:void(0);" class="button" qdid="'+data['data'][2][i]['DiffID']+'" title="'+data['data'][2][i]['DiffArea']+'">'+data['data'][2][i]['DiffName']+'</a>';
            }
            str+='</span></td><td> </td></tr></tbody></table></div><div class="filterbox_li" ><table  border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="65">文档类型</td><td><span id="quesdoctypeselect"><a href="javascript:void(0);" class="button_current" qdid="0">全部</a>';
            for(var i in data['data'][4]){
                str+='<a href="javascript:void(0);" class="button" qdid="'+data['data'][4][i]['TypeID']+'" title="'+data['data'][4][i]['TypeName']+'">'+data['data'][4][i]['TypeName']+'</a>';
            }
            //文档类型
            str+='</span></td><td> </td></tr></tbody></table></div><div class="filterbox_li" style="background:none"><table  border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="65">文档来源</td><td><span id="quessourceselect"><a href="javascript:void(0);" class="button_current" qdid="0">全部</a>';
            for(var i in data['data'][6]){
                str+='<a href="javascript:void(0);" class="button" qdid="'+data['data'][6][i]['SourceID']+'" title="'+data['data'][6][i]['SourceName']+'">'+data['data'][6][i]['SourceName']+'</a>';
            }
            str+='</span></td><td> </td></tr></tbody></table></div>';
            $('#filterbox').html(str);
            str='';
            str+='<div class="filterbox_li" style="background:none;display:none;"><table  border="0" cellpadding="0" cellspacing="0"><tbody><tr><td>“<span class="nowchapter"></span>”课后推荐习题　</td><td><span id="quesabilityselect"><a href="javascript:void(0);" class="button_current" qdid="0">全部</a>';
            for(var i in data['data'][5]){
                str+='<a href="javascript:void(0);" class="button" qdid="'+data['data'][5][i]['AbID']+'" title="'+data['data'][5][i]['AbilitName']+'">'+data['data'][5][i]['AbilitName']+'</a>';
            }
            str+='</span></td><td> </td></tr></tbody></table></div>';
            $('#ability').hide();
            $('#ability').html(str);
        });
    },
    //页面跳转函数
    gotoPage:function(num){
        $("#rightdiv").scrollTop(0);
        if(num<1 && page==1){
            return false;
        }
        page=num;
        $.chapterTest.getTest();//获取试题
    },
    //上一页
    goPrevPage:function(){
        $('.prev_page').live('click',function(){
            if($.myPage.goPrevPage()==false) return false;
            $.chapterTest.gotoPage(page);
        });
    },
    //下一页
    goNextPage:function(){
        $('.next_page').live('click',function(){
            if($.myPage.goNextPage()==false) return false;
            $.chapterTest.gotoPage(page);
        });
    },
    //页面跳转事件绑定
    jumpToPage:function(){
        $('.pagebox a').live('click',function(){
            $.chapterTest.gotoPage(parseInt($(this).attr('page')));
        });
    },
    //分页快速跳转
    quickToPage:function(){
        $('#quicktopage a').live('click',function(){
            $.myPage.quickToPage($(this));
            $.chapterTest.gotoPage(page);
        });
    },
    //显示版本事件
    showChapterBBEvent:function(){
        $('#curcategory').live('mouseenter',function(){
            $('#othercategories').css({'display':'block'});
        });
        $('#curcategory').live('mouseleave',function(){
            $('#othercategories').css({'display':'none'});
        });
        //切换版本
        $('#othercategories a').live('click',function(){
            //切换版本
            var tmp_cid=$('#curcategoryname').attr('categoryid');
            var tmp_cname=$('#curcategoryname').html();
            $('#curcategoryname').attr('categoryid',$(this).attr('cid'));
            $('#curcategoryname').html($(this).attr('title'));
            $(this).parent().remove();
            $('#othercategories').css({'display':'none'});
            $('#othercategories').append('<div><a class="banben" title="'+tmp_cname+'" cid="'+tmp_cid+'">〓'+tmp_cname+'</a></div>');
            //初始化路径
            $('#loca_text').html($.chapterTest.subjectName+$(this).attr('title'));
            //初始化条件
            $.chapterTest.initSelect();
            //载入章节
            $.chapterTest.getChapterList($('#curcategoryname').attr('categoryid'));
            //载入试题
            $.chapterTest.getTest();
        });
    },
    //显示章节版本
    showChapterBB:function(){
        //显示相应id的版本
        $.post(U('Home/Index/getData'),{'style':'chapter','subjectID':subjectID,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            };
            var str='<span id="curcategory" style="position:relative;">';
            str+='<span id="curcategoryname" categoryid="'+data['data'][0]['ChapterID']+'" title="'+$.chapterTest.subjectName+data['data'][0]['ChapterName']+'">'+data['data'][0]['ChapterName']+'</span><a id="categorymore"></a>';
            if(data['data'].length>1){
                str+='<div id="othercategories" style="top:32px;_top:16px;z-index:100;display:none;">';
                for(var i=1;i<data['data'].length;i++){
                    str+='<div>'+
                        '<a class="banben" title="'+data['data'][i]['ChapterName']+'" cid="'+data['data'][i]['ChapterID']+'">〓'+data['data'][i]['ChapterName']+'</a>'+
                        '</div>';
                }
                str+='</div>';
            }
            str+='</span>';
            $('#loca_text').html($.chapterTest.subjectName+data['data'][0]['ChapterName']);
            $('#categorytop .tit:eq(0)').append(str);
            //显示当前版本章节
            $.chapterTest.getChapterList(data['data'][0]['ChapterID']);
            //显示当前版本试题
            $.chapterTest.getTest();
        });
    },
    //重组树形结构
    getChapterSub:function(thisData,layer){
        if(typeof(layer)=='undefined' || layer=='') layer=1;
        var this_str='';
        for(var i=0;i<thisData.length;i++){
            this_str+='<li><span></span><a href="#" class="cp" qyid="'+thisData[i]['ChapterID']+'" title="'+thisData[i]['ChapterName']+'">'+ $.myCommon.cutString(thisData[i]['ChapterName'],(28-layer*2),'...')+'</a>';
            if(thisData[i]['sub']){
                this_str+='<ul>'+ $.chapterTest.getChapterSub(thisData[i]['sub'],layer+1)+'</ul>';
            }
            this_str+='</li>';
        }
        return this_str;
    },
    //载入章节
    getChapterList:function(id){
        $('#treecon').html('<p class="list_ts" style="color:#fff">数据加载中请稍候...</p>');
        $.post(U('Home/Index/getData'),{'style':'chapter','pID': id,'haveLayer':3,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            };
            if(data['data']){
                $('#treecon').html($.chapterTest.getChapterSub(data['data']));

                $('#treecon').tree({
                    expanded: 'li:first'
                });
            }
        });
    },
    //初始化分类条件
    initSelect:function (){
        if($('#questypeselect a:eq(0)').attr('class').indexOf('button_current')==-1){
            $('#questypeselect a:eq(0)').setButtonBackground();
        }
        if($('#quesdiffselect a:eq(0)').attr('class').indexOf('button_current')==-1){
            $('#quesdiffselect a:eq(0)').setButtonBackground();
        }
        if($('#quesdoctypeselect a:eq(0)').attr('class').indexOf('button_current')==-1){
            $('#quesdoctypeselect a:eq(0)').setButtonBackground();
        }
        if($('#list_px a:eq(0)').attr('class').indexOf('button_current')==-1){
            $('#list_px a:eq(0)').setButtonBackground();
        }
    },
    //初始化能力选项
    initAbilitySelect:function(){
        $('#quesabilityselect a:eq(0)').setButtonBackground();
    },
    //按照章节查看试题
    showTestByChapter:function(){
        $('.cp').live('click',function(){
            $('#quesabilityselect a').addClass('button').removeClass('button_current');
            $('#quesabilityselect a').first().addClass('button_current').removeClass('button');

            $('.cp').css('background-color','#3399ff');
            $('.cp').attr('jl','');
            $(this).css('background-color','#3333ff');
            $(this).attr('jl','down');
            if(typeof($(this).prev().attr('class'))=='undefined'){
                $('#ability').show();
                $('.filterbox_li').last().show();
                $('.filterbox_li').eq(2).attr('style','');
                $('.nowchapter').html($(this).text());
            }else{
                $('#ability').hide();
                $('.filterbox_li').last().hide();
                $('.filterbox_li').eq(2).attr('style','background: none');
                $('#quesabilityselect a').addClass('button').removeClass('button_current');
                $('#quesabilityselect a').first().addClass('button_current').removeClass('button');
            }
            $('#loca_text').html($.chapterTest.subjectName+$('#curcategoryname').html()+' > '+$.chapterTest.getPathForChapter($(this)));

            page=1;
            //显示试题
            $.chapterTest.getTest();//获取试题
        });
    },
    //获取章节路径
    getPathForChapter:function(obj){
        var tmpStr='';
        tmpStr=obj.html()+' > ' +tmpStr;
        if(obj.parent().parent().attr('id')!='treecon'){
            tmpStr=$.chapterTest.getPathForChapter(obj.parent().parent().prev()) +tmpStr;
        }
        return tmpStr;
    },
    //树形结构变色
    treeSpanClick:function(){
        $('.tree span').live('click',function(){
            $('.cp').css('background-color','#3399ff');
            $('.cp').attr('jl','');
            $(this).next().css('background-color','#3333ff');
            $(this).next().attr('jl','down');
        });
    },
    //显示所有章节试题事件
    treeCategoryClick:function(){
        $('#categorytop').css('cursor','pointer').live('click',function(){
            $.chapterTest.initSelect(); //初始化分类选项
            page=1;
            $('.cp').css({'background-color':'#3399ff','border-color':'#3E5061','font-weight':'normal'});
            $('.cp').attr('jl','');
            //初始化路径
            $('#loca_text').html($.chapterTest.subjectName+$('#curcategoryname').html());
            $.chapterTest.getTest();//获取试题
        });
    },
    //根据试题类型查看试题
    showTestByTypes:function(){
        $('#questypeselect a').live('click',function(){
            $.chapterTest.initAbilitySelect(); //初始化分类选项
            $(this).setButtonBackground();
            $.chapterTest.getTest();//获取试题
        });
    },
    //根据试题难度查看试题
    showTestByDiff:function(){
        $('#quesdiffselect a').live('click',function(){
            $.chapterTest.initAbilitySelect(); //初始化分类选项
            $(this).setButtonBackground();
            $.chapterTest.getTest();//获取试题
        });
    },
    //根据文档类型查看试题
    showTestByDocType:function(){
        $('#quesdoctypeselect a').live('click',function(){
            $.chapterTest.initAbilitySelect(); //初始化分类选项
            $(this).setButtonBackground();
            $.chapterTest.getTest();//获取试题
        });
    },
    //根据能力值查看试题
    showTestByAbility:function(){
        $('#quesabilityselect a').live('click',function(){
            $.chapterTest.initSelect(); //初始化分类选项
            $(this).setButtonBackground();
            $.chapterTest.getTest();//获取试题
        });
    },
    //根据文档来源查看试题
    showTestBySource:function(){
        $('#quessourceselect a').live('click',function(){
            $.chapterTest.initAbilitySelect(); //初始化分类选项
            $(this).setButtonBackground();
            $.chapterTest.getTest();//获取试题
        });
    },
    //根据排序查看试题
    showTestByOrder:function(){
        $('#list_px a.button').live('click',function(){
            page=1;
            $(this).aOrderColor();
            $.chapterTest.getTest();//获取试题
        });
    },
    //获取难度当前值
    getDiff:function(){
        return $('#quesdiffselect').getButtonSelected('qdid');
    },
    //获取排序当前值
    getOrder:function(){
        return $('#list_px').getButtonSelected('type');
    },
    //获取当前能力值
    getAbility:function(){
        return $('#quesabilityselect').getButtonSelected('qdid');
    },
    //获取题型当前值
    getTypes:function (){
        return $('#questypeselect').getButtonSelected('qdid');
    },
    //获取文档类型当前值
    getDocType:function(){
        return $('#quesdoctypeselect').getButtonSelected('qdid');
    },
    //获取章节当前值
    getChapter:function(){
        var output=0;
        $('.cp').each(function(){
            if($(this).attr('jl')=='down'){
                output=$(this).attr('qyid');
                return true;
            }
        });
        if(output==0) output=$('#curcategoryname').attr('categoryid');
        return output;
    },
    //获取文档来源当前值
    getSource:function(){
        return $('#quessourceselect').getButtonSelected('qdid');
    },
    //ajax获取试题
    getTest:function(){
        var cid=this.getChapter();
        var tid=this.getTypes();
        var dif=this.getDiff();
        var dtid=this.getDocType();
        var order=this.getOrder();
        var abi=this.getAbility();
        var sourceID=this.getSource();
        $("#queslistbox").html('<p class="list_ts"><span class="ico_dd">试题加载中请稍候...</span></p>');
        lock='test';
        $.post(U('Home/Index/getTestList'),{'randoms':1,'sid':subjectID,'sourceid':sourceID,'abi':abi,'cid':cid,'tid':tid,'dtid':dtid,'dif':dif,'o':order,'page':page,'rand':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                lock='';
                $.myPage.showPage(0,1,page,1);
                $("#queslistbox").html('<p class="list_ts">抱歉！暂时没有符合条件的试题，请尝试更换查询条件。</p>');
                return false;
            };
            if(data['data'][1]!=0) $("#queslistbox").html($.myTest.showTest(data['data'][0]));
            else $("#queslistbox").html('<p class="list_ts">抱歉！暂时没有符合条件的试题，请尝试更换查询条件。</p>');
            $.myPage.showPage(data['data'][1],data['data'][2],page,1);
            lock='';
        });
    },
    //重置框架
    initDivBoxHeight:function() {
        var a = $(window).width();
        var b = $(window).height();
        var c = 0;
        $(".mleft,.mright").height(b - c - 2);
        $("#rightdiv").width(a-$("#leftdiv").outerWidth());
        $("#leftdiv").height(b - c-2);
        $("#rightdiv").height(b - c-2);
        $("#categorytreebox").height(b - c - $('#categorytop').outerHeight());
    }
}
//试卷出题方法
jQuery.docTest = {
    //初始化
    init:function(){
        this.initDivBoxHeight(); //重置页面框架

        if ($('#showDocID').val() != '') {
            this.openTestDoc($('#showDocID').val(),$('#showSubjectID').val());
        }

        this.loadClassList(); //载入分类列表
        this.loadDocEvent(); //载入试题事件
        this.loadPageEvent(); //载入分页事件

        $(window).bind("resize",function() {$.docTest.initDivBoxHeight();});
    },
    //载入分页事件
    loadPageEvent:function(){
        this.jumpToPage(); //页码分页跳转
        this.goPrevPage(); //上一页
        this.goNextPage(); //下一页
        this.quickToPage(); //快速分页跳转
    },
    //载入文档事件
    loadDocEvent:function(){
        this.barClick(); //导航标签切换事件
        this.paperView(); //ajax获取试题
        this.addTestAll(); //加入全部试题
        this.paperClose(); //关闭试卷选项卡

        this.getKeyword(); //关键字获取试卷
        this.getDoc(); //获取试卷

        this.showDocByOrder(); //载入排序事件
        this.showDocByGrade(); //按照难度查看试题
        this.showDocByArea(); //按照知识点查看试题
        this.showDocByDocType(); //根据试题类型查看试题
    },
    //载入默认分类
    loadClassList:function(){
        $('#loca_text').html($.myCommon.getSubjectNameFromParent());
        //载入初始数据
        $.get(U('Manual/Index/getDocInit?id=' + subjectID + '&times=' + Math.random()), function (data) {

            if ($.myCommon.backLogin(data) == false) {
                return false;
            }

            var str = '';
            str = '<a href="javascript:void(0);" class="button_current" thisid="0" qdid="0">全部</a>';
            for (var i in data['data'][1]) {
                str += '<a href="javascript:void(0);" class="button" thisid="' + data['data'][1][i]['GradeList'] + '" qdid="' + data['data'][1][i]['TypeID'] + '">' + data['data'][1][i]['TypeName'] + '</a>';
            }
            $('#questypeselect').html(str);

            str = '';
            str = '<a href="javascript:void(0);" class="button_current" qdid="0">全部</a>';
            for (var i in data['data'][2]) {
                str += '<a href="javascript:void(0);" class="button" qdid="' + data['data'][2][i]['AreaID'] + '">' + data['data'][2][i]['AreaName'] + '</a>';
            }
            $('#quesareaselect').html(str);
            str = '';
            str = '<a href="javascript:void(0);" class="button_current" qdid="0">全部</a>';
            for (var i in data['data'][3]) {
                str += '<a href="javascript:void(0);" class="button" qdid="' + data['data'][3][i]['GradeID'] + '">' + data['data'][3][i]['GradeName'] + '</a>';
            }
            $('#questypegrade').html(str);
        });
    },
    //页面跳转函数
    gotoPage:function(num){
        $("#switchbox").scrollTop(0);
        if(num<1 && page==1){
            return false;
        }
        page=num;
        $.docTest.getDoc();//获取文档
    },
    //上一页
    goPrevPage:function(){
        $('.prev_page').live('click',function(){
            if($.myPage.goPrevPage()==false) return false;
            $.docTest.gotoPage(page);
        });
    },
    //下一页
    goNextPage:function(){
        $('.next_page').live('click',function(){
            if($.myPage.goNextPage()==false) return false;
            $.docTest.gotoPage(page);
        });
    },
    //页面跳转事件绑定
    jumpToPage:function(){
        $('.pagebox a').live('click',function(){
            $.docTest.gotoPage(parseInt($(this).attr('page')));
        });
    },
    //分页快速跳转
    quickToPage:function(){
        $('#quicktopage a').live('click',function(){
            $.myPage.quickToPage($(this));
            $.docTest.gotoPage(page);
        });
    },
    //验证是否全部选取
    clickNums:function(ID, className) {
        var clicknum = $('#' + ID + ' a').length;
        var canclick = $('#' + ID + ' .' + className).length;
        var click = clicknum - canclick;
        var allClick = '';//是否选中全部按钮的标识
        if ($('#' + ID + ' a').eq(0).attr('class').indexOf('noclick') != -1) {
            $('#' + ID + ' a').eq(0).addClass('button');
            $('#' + ID + ' a').eq(0).removeClass('noclick');
        }
        if (parseInt(click) <= 1) {//判断是否全部属于
            $('#' + ID + ' a').removeClass('noclick');
            $('#' + ID + ' a').addClass('button');
            $('#' + ID + ' a').each(function () {//如果参照有选中的就不选择全部
                if ($(this).attr('class').indexOf('button_current') != -1) {
                    allClick = 'false';
                }
            });
            if (allClick == '') {
                $('#' + ID + ' a').removeClass('button_current');
                $('#' + ID + ' a').eq(0).addClass('button_current');
            }
        }
    },
    //根据年级查看文档
    showDocByGrade:function(){
        $('#questypegrade a').live('click', function () {
            //类型年级不匹配
            if ($(this).attr('class') == 'noclick') return false;

            $(this).setButtonBackground();

            var gradeID = $(this).attr('qdid');//年级ID
            var gradeList = '';//类型年级ID
            $("#questypeselect a").each(function () {
                gradeList = $(this).attr('thisid');
                //循环年级，判断年级ID是否在相同的年级ID里
                if (gradeID == 0) {//如果点击年级全部按钮，则恢复所有类型
                    if ($(this).attr('class') == 'noclick') {
                        $(this).addClass('button');
                        $(this).removeClass('noclick');
                    }
                } else {
                    if ((',' + gradeList + ',').indexOf(',' + gradeID + ',') == -1) {
                        //排除全部按钮
                        if ($(this).attr('qdid') != '0') {
                            $(this).removeClass('button');
                            $(this).addClass('noclick');
                        }
                    } else {
                        $(this).addClass('button');
                        $(this).removeClass('noclick');
                    }
                }
            });
            $.docTest.clickNums('questypeselect', 'button');
            $.docTest.getDoc();//获取文档
        });
    },
    //根据文档类型查看文档
    showDocByDocType:function(){
        $('#questypeselect a').live('click', function () {
            //类型年级不匹配
            if ($(this).attr('class') == 'noclick') return false;

            $(this).setButtonBackground();

            var gradeID = $(this).attr('thisid');//类型年级ID
            var gradeList = '';//年级ID
            var button = '';//全部按钮是否可点击标识
            $("#questypegrade a").each(function () {
                gradeList = $(this).attr('qdid');
                //循环年级，判断年级ID是否在相同的年级ID里
                if (gradeID == 0) {//如果点击类型全部按钮，则恢复所有年级
                    if ($(this).attr('class') == 'noclick') {
                        $(this).addClass('button');
                        $(this).removeClass('noclick');
                    }
                } else {
                    if ((',' + gradeID + ',').indexOf(',' + gradeList + ',') == -1) {
                        $(this).addClass('noclick');
                        $(this).removeClass('button');
                    } else {
                        $(this).addClass('button');
                        $(this).removeClass('noclick');
                    }
                }
            });

            $.docTest.clickNums('questypegrade', 'button');
            $.docTest.getDoc();//获取文档
        });
    },
    //按照省份查看文档
    showDocByArea:function(){
        $('#quesareaselect a').live('click', function () {
            $(this).setButtonBackground();
            $.docTest.getDoc();//获取文档
        });
    },
    //排序文档
    showDocByOrder:function(){
        $('#doc_px a').live('click', function () {
            page=1;
            $(this).aOrderColor();
            $.docTest.getDoc();//获取文档
        });
    },
    //导航标签切换事件
    barClick:function(){
        $('#bartitle .nav').live('click', function (event) {
            event.stopPropagation();
            var _this = $(this);
            $('#bartitle .nav_current').addClass('nav').removeClass('nav_current');
            _this.addClass('nav_current').removeClass('nav');
            var id = _this.attr('id');
            if (id == 'paperlisthandler') {
                //修改样式
                $('#paperlistbox').css({'display': 'block'});
                $('#paperviewbox').css({'display': 'none'});
                $('#switchbox').scrollTop(0);
            } else {
                //修改样式
                $('#paperlistbox').css({'display': 'none'});
                $('#paperviewbox').css({'display': 'block'});
                $('.papercontent').css({'display': 'none'});
                $('#papercontent' + id.replace('papernav', '')).css({'display': 'block'});
                $('#switchbox').scrollTop(0);
            }
        });
    },
    //获取省份当前值
    getArea:function() {
        return $('#quesareaselect').getButtonSelected('qdid');
    },
    //获取类型当前值
    getType:function() {
        return $('#questypeselect').getButtonSelected('qdid');
    },
    //获取年级当前值
    getGrade:function() {
        return $('#questypegrade').getButtonSelected('qdid');
    },
    //获取关键字当前值
    getKey:function(){
        return $('#keyword').val();
    },
    //获取关键字当前值
    getOldKey:function(){
        var oldKey=$('#keepKeyWord').val();
        $('#keepKeyWord').val($('#keyword').val());
        return oldKey;
    },
    //获取排序当前值
    getOrder:function() {
        return $('#doc_px').getButtonSelected('type');
    },
    //创建试卷div
    createDocDiv:function(docID, docname) {
        var str = '<span class="nav_current" id="papernav' + docID + '">' +
            '<table border="0" cellpadding="0" cellspacing="0">' +
            '<tbody><tr>' +
            '<td><a class="papericon"/></td>' +
            '<td class="cursor"><a title="【编号' + docID + '】' + docname + '">' + (docname.length > 10 ? docname.substr(0, 10) : docname) + '</a></td>' +
            '<td><a class="paperclose" did="' + docID + '" title="关闭"/></td>' +
            '</tr></tbody>' +
            '</table>' +
            '</span>';
        $('#paperlisthandler').after(str);

        //修改样式
        $('#bartitle .nav_current').addClass('nav').removeClass('nav_current');
        $('#bartitle #papernav' + docID).addClass('nav_current').removeClass('nav');
        //创建div
        $('#paperlistbox').css({'display': 'none'});
        $('#paperviewbox').css({'display': 'block'});
        $('#paperviewbox .papercontent').css({'display': 'none'});
        $('#paperviewbox').append('<div class="papercontent" id="papercontent' + docID + '" style="margin:5px;"></div>');
    },
    //显示试卷div
    showDocDiv:function(docID) {
        $('#bartitle .nav_current').addClass('nav').removeClass('nav_current');
        $('#bartitle #papernav' + docID).addClass('nav_current').removeClass('nav');
        $('.papercontent').css({'display': 'none'});
        $('#paperlistbox').css({'display': 'none'});
        $('#paperviewbox').css({'display': 'block'});
        $('#papercontent' + docID).css({'display': 'block'});
        $('#switchbox').scrollTop(0);
    },
    //从首页点击过来
    openTestDoc:function(docID,thisSubjectID) {
        if(thisSubjectID!=subjectID){
            return false;
        }

        //检测试卷是否存在
        if ($('#papernav' + docID).length > 0) {
            $.docTest.showDocDiv(docID);
            return false;
        }
        //提示载入
        $.myDialog.showMsg('试题载入请稍候...',0,0);

        //提取试卷
        $.post(U('Manual/Index/getDocTest'), 'did=' + docID + '&' + Math.random(), function (data) {
            if ($.myCommon.backLogin(data) == false) {
                return false;
            }
            ;
            //创建试卷框架
            $.docTest.createDocDiv(docID, data['data'][0][0][0]['docname']);
            //写入数据
            var tmp_str = '<div style="text-align:center;margin:15px;font-size:16px;font-weight:bolder;color:#005AA0;">【编号' + docID + '】' + data['data'][0][0][0]['docname'] + '</div>' +
                '<div style="font-weight:bold;margin:10px;text-align:center;position:relative;">试卷类型：' +
                '<span style="color:#005AA0;">' + data['data'][0][0][0]['typename'] + '</span>' +
                ' | 适用省份：<span style="color:#005AA0;">' + data['data'][0][0][0]['areaname'] + '</span>' +
                ' | 试卷年份：<span style="color:#005AA0;">' + data['data'][0][0][0]['docyear'] + '年</span> | 上传日期：' +
                '<span>' + data['data'][0][0][0]['introfirsttime'] + '</span> | 题数： <span>' + data['data'][1][0].length + '</span><div class="bgbt an01 addtestall" hidevalue="0"><span class="an_left"></span><a>全部加入</a><span class="an_right"></span></div></div>';
            tmp_str += $.myTest.showTest(data['data'][1][0]);
            $("#papercontent" + docID).html(tmp_str);
            if(tmp_str.indexOf('delques')>0){ //判断该试卷中，是否已经有加入试卷的试题，如果有，那么全部加入修改为全部去除
                $('#papercontent'+docID).find('.addtestall').find('a').eq(0).html('全部去除');
                $('#papercontent'+docID).find('.addtestall').removeClass('an01').addClass('an02');
            }
            $('#switchbox').scrollTop(0);
            //载入完成
            $('#msgdiv').css({'display': 'none', 'opacity': 0});
            $('#div_shadowmsgdiv').css({'display': 'none'});
        });
    },
    //ajax获取试题
    paperView:function(){
        $('.paperview').live('click', function () {
            //检测试卷是否存在
            var docID = $(this).attr('did');
            $.docTest.openTestDoc(docID,subjectID);
        });
    },
    //加入全部试题
    addTestAll:function(){
        $('.addtestall').live('click', function () {
            var resarr = '';
            var overarr = new Array();
            var result;
            if ($(this).find('a').eq(0).html()=='全部加入') {
                $(this).parent().parent().find('.addquessel').each(function (i) {
                    var thistypeid = $(this).attr('qyid');
                    if (typeof(overarr[thistypeid]) != 'undefined') {
                        return true;
                    }
                    result = $.myTest.checkIfOver(this);
                    if (!result[0]) {
                        if (resarr.indexOf(',' + result[1]) == -1) {
                            resarr += ',' + result[1];
                        }
                        return true;
                    }
                    editData.addtest($(this).attr('quesid'), $(this).attr('childnum'), $(this).attr('qyname'), $(this).attr('qyid'));
                    $.myTest.updateMainTypes($(this).attr('childnum'), $(this).attr('qyname'));
                    var testid = $(this).attr('quesid');
                    $('#selmore' + testid).hide();
                    $('#selpicleft' + testid).hide();
                    $(this).addClass('delques');
                    $(this).removeClass('addquessel');
                });
                if (resarr != '') $.myDialog.showMsg('题型【' + resarr.substr(1) + '】试题量已满！', 1);
                $(this).removeClass('an02').addClass('an01');
                $(this).find('a').eq(0).html('全部去除');
            } else {
                $(this).parent().parent().find('.delques').each(function (i) {
                    var tmp_str = editData.selecttest($(this).attr('quesid'));
                    var testid = $(this).attr('quesid');
                    $('#selmore' + testid).css('display', 'inline-block');//样式不能show
                    $('#selpicleft' + testid).css('display', 'inline-block');//样式不能show
                    if (tmp_str) {
                        editData.deltest($(this).attr('quesid'));
                        $.myTest.updateMainTypes(0 - $(this).attr('childnum'), tmp_str);
                        $(this).addClass('addquessel');
                        $(this).removeClass('delques');
                    }
                });
                $(this).find('a').eq(0).html('全部加入');
                $(this).removeClass('an01').addClass('an02');
            }
        });
    },
    //关闭试卷选项卡
    paperClose:function(){
        $('.paperclose').live('click', function (event) {
            event.stopPropagation();
            var docID = $(this).attr('did');
            var currentid = $('#bartitle .nav_current').attr('id').replace('papernav', '');

            if (currentid == 'paperlisthandler' || currentid == docID) $('#paperlisthandler').click();
            else $.docTest.showdocdiv(currentid);
            $('#papernav' + docID).remove();
            $('#papercontent' + docID).remove();
        });
    },
    //搜索关键字试卷
    getKeyword:function() {
        $('#searchsubmit').live('click',function(){
            page=1;
            $.docTest.getDoc();//获取文档
        });
    },

    //ajax获取试卷
    getDoc:function() {
        var tid = $.docTest.getType();
        var area = $.docTest.getArea();
        var orderby = $.docTest.getOrder();
        var grade = $.docTest.getGrade();
        var key=$.docTest.getKey();
        var oldKey=$.docTest.getOldKey();
        $("#paperlist_content").html('<p class="list_ts"><span class="ico_dd">正在查询请稍候...</span></p>');
        lock='doc';
        $.post(U('Home/Index/getDocList'), {'sid': subjectID, 'tid': tid,'key':key,'oldkey':oldKey, 'area': area, 'grade': grade, 'o': orderby, 'page': page, 'times': Math.random()}, function (data) {
            if ($.myCommon.backLogin(data) == false) {
                lock = '';
                $.myPage.showPage(0, 1,page,1);
                return false;
            }
            if (typeof(data['data'][1]) != 'undefined' && data['data'][1] != 0) $("#paperlist_content").html($.docTest.showDoc(data['data'][0]));
            else $("#paperlist_content").html('<p class="list_ts">抱歉！暂时没有符合条件的试卷，请尝试更换查询条件。</p>');
            $.myPage.showPage(data['data'][1], data['data'][2],page,1);
            lock = '';
        });
    },
    //排列文档
    showDoc:function(data) {
        var str = '<div class="questablebox" style="margin:0;">' +
            '<table cellspacing="0" class="questable">' +
            '<colgroup><col class="odd"/><col class="even"/><col class="odd"/><col class="even"/><col class="odd"/></colgroup>' +
            '<tr>' +
            '<th align="center" width="50" class="oddth">编号</th>' +
            '<th align="center" class="eventh" width="65">分类</th>' +
            '<th align="center" class="oddth"  width="130">试卷属性</th>' +
            '<th align="left" class="oddth">试卷标题</th>' +
            '<th align="center" class="eventh" width="80">日期</th>' +
            '</tr>';
        for (var i in data) {
            str += '<tr class="doc_tr">' +
                '<td align="center"><span>' + data[i]['docid'] + '</span></td>' +
                '<td align="center"><span id="paperleve' + data[i]['docid'] + '" levelid="1">' + data[i]['typename'] + '</span></td>' +
                '<td align="center">' + data[i]['docyear'] + '年 | ' + (data[i]['areaname'] == "" ? '通用' : data[i]['areaname']) +
                '</td>' +
                '<td style="line-height:18px;"><a href="javascript:;" did="' + data[i]['docid'] + '" class="paperview" style="font-size:14px;">' + data[i]['docname'] + '</a>' +
                '</td>' + '<td align="center">' + data[i]['introfirsttime'] + '</td>' +
                '</tr>';
        }
        str += '</table></div>';
        return str;
    },
    //重置框架
    initDivBoxHeight:function() {
        var a = $(window).width();
        var b = $(window).height();
        var c = $("#rightdiv").height();
        var d = $("#righttop").height();
        var e = $("#papernav").height();
        $("#rightdiv").width(a);
        $("#rightdiv").height(b - 1);
        $("#switchbox").height(b - d - e - 21);
    }
}
//检索页方法
jQuery.searchTest = {
    //载入默认方法
    init:function(){
        this.loadClassList(); //载入分类信息
        this.searchTest(); //载入检索事件
        this.initDivBoxHeight(); //重置页面框架
        this.showTestByOrder(); //载入排序事件
        this.loadPageEvent(); //载入分页事件

        $('#searchsubmit').clickByEnter(); //回车触发搜索

        $(window).bind("resize",function() {$.searchTest.initDivBoxHeight();});
    },
    //载入分页事件
    loadPageEvent:function(){
        this.jumpToPage(); //页码分页跳转
        this.goPrevPage(); //上一页
        this.goNextPage(); //下一页
        this.quickToPage(); //快速分页跳转
    },
    //载入分类信息
    loadClassList:function(){
        lock='getTypes'; //锁定ajax
        //显示学科题型及难度
        $.post(U('Home/Index/getTypes'),{'id':subjectID,'m':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                lock=''; //解锁
                return false;
            }
            var str='<label><input type="radio" name="questype" checked="checked" value="0"/><span class="pointer">不限</span></label>';
            for(var i in data['data'][1]){
                str+='<label><input type="radio" name="questype" value="'+data['data'][1][i]['TypesID']+'"/><span class="pointer">'+data['data'][1][i]['TypesName']+'</span></label>';
            }
            $('#questypebox').html(str);
            str='<label><input type="radio" name="quesdiff" checked="checked" value="0"/><span class="pointer">不限</span></label>';
            for(var i in data['data'][2]){
                str+='<label title="'+data['data'][2][i]['DiffArea']+'"><input type="radio" name="quesdiff" value="'+data['data'][2][i]['DiffID']+'"/><span class="pointer">'+data['data'][2][i]['DiffName']+'</span></label>';
            }
            $('#quesdiffbox').html(str);
            str='<label><input type="radio" name="questime" checked="checked" value="0"/><span class="pointer">不限</span></label>';
            var data_value=new Array('onemonth','threemonth','sixmonth','oneyear','oneyearold');
            var data_str=new Array('一个月内','三个月内','半年内','一年内','一年外');
            for(var i in data_value){
                str+='<label><input type="radio" name="questime" value="'+data_value[i]+'"/><span class="pointer">'+data_str[i]+'</span></label>';
            }
            $('#questimebox').html(str);
            $('#loca_text span').html($.myCommon.getSubjectNameFromParent());

            //选项变色
            $('#questypebox').radioColor();
            $('#quesdiffbox').radioColor();
            $('#questimebox').radioColor();

            lock=''; //解锁
        },'json');
    },
    searchTest:function(){
        //检索
        $('#searchsubmit').click(function(){
            //验证锁定
            if($.myCommon.ajaxLoading()==false){
                return false;
            }
            page=1;
            var keywords=$('#keyword').val();
            var oldkey= $.docTest.getOldKey();
            if($('#keyword').val().length<=1){
                $.myDialog.normalMsgBox('showmsg','提示信息',450,'搜索关键字长度必须大于1！',2);
                return false;
            }
            $.post(U('Manual/Index/addKeyWord'),'keywords='+keywords+'&oldkey='+oldkey+'&subjectID='+subjectID+'&times='+Math.random(),function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
            })
            $.searchTest.getTest();
        });
    },
    //页面框架跳整
    initDivBoxHeight:function () {
        var a = $(window).width();
        var b = $(window).height();
        $("#rightdiv").width(a);
        $("#rightdiv").height(b);
    },

    //ajax获取试题
    getTest:function(){
        var keyword=$('#keyword').val();
        var keepKeyWord=$('#keepKeyWord').val();

        if(keyword=='' && keepKeyWord!=''){
            keyword=$('#keepKeyWord').val();
        }
        if(keyword==''){
            return false;
        }
        var starttime=new Date().getTime();
        var newkeyword= $.myCommon.cutString(keyword, 24)+'...';
        $('#keywordtext').html(newkeyword);
        $('#keywordtext').attr('title',keyword);
        var time=$.searchTest.getTime();
        var tid=$.searchTest.getTypes();
        var dif=$.searchTest.getDiff();
        var order=$.searchTest.getOrder();
        $("#queslistbox").html('<p class="list_ts"><span class="ico_dd">正在查询请稍候...</span></p>');
        lock='test';
        $.post(U('Home/Index/getTestList'),'sid='+subjectID+'&time='+time+'&tid='+tid+'&dif='+dif+'&o='+order+'&key='+(keyword)+'&page='+page+'&'+Math.random(),function(data){
            if($.myCommon.backLogin(data)==false){
                lock='';
                $("#queslistbox").html('<p class="list_ts">'+data.data+'</p>');
                $.myPage.showPage(0,1,page,1);
                $('#elapsedtime').html(new Date().getTime()-starttime);
                return false;
            };
            if(typeof(data['data'][0])!='' && data['data'][0]!=''){
                $('#keepKeyWord').val(keyword);
                $("#queslistbox").html($.myTest.showTest(data['data'][0]));
                $.myPage.showPage(data['data'][1],data['data'][2],page,1);
            }else{
                $("#queslistbox").html('<p class="list_ts">抱歉！暂时没有符合条件的试题，请尝试更换查询条件。</p>');
                $.myPage.showPage(0,1,page,1);
            }
            $('#elapsedtime').html(new Date().getTime()-starttime);
            lock='';
        },'json');
    },
    //根据排序查看试题
    showTestByOrder:function(){
        $('#list_px a').live('click',function(){
            page=1;
            $(this).aOrderColor(); //排序变色
            $.searchTest.getTest();//获取试题
        });
    },
    //获取排序当前值
    getOrder:function(){
        return $('#list_px').getButtonSelected('type');
    },
    //获取难度当前值
    getDiff:function(){
        return $('#quesdiffbox').getRadioChecked();
    },
    //获取题型当前值
    getTypes:function(){
        return $('#questypebox').getRadioChecked();
    },
    //获取时间当前值
    getTime:function(){
        return $('#questimebox').getRadioChecked();
    },
    //页面跳转事件绑定
    jumpToPage:function(){
        $('.pagebox a').live('click',function(){
            $.searchTest.gotoPage(parseInt($(this).attr('page')));
        });
    },
    //页面跳转函数
    gotoPage:function(num){
        $('#searchresult').scrollTop(0);
        if(num<1 && page==1){
            return false;
        }
        page=num;
        $.searchTest.getTest();//获取试题
    },
    //上一页
    goPrevPage:function(){
        $('.prev_page').live('click',function(){
            if($.myPage.goPrevPage()==false) return false;
            $.searchTest.gotoPage(page);
        });
    },
    //下一页
    goNextPage:function(){
        $('.next_page').live('click',function(){
            if($.myPage.goNextPage()==false) return false;
            $.searchTest.gotoPage(page);
        });
    },
    //分页快速跳转
    quickToPage:function(){
        $('#quicktopage a').live('click',function(){
            $.myPage.quickToPage($(this));
            $.searchTest.gotoPage(page);
        });
    }
}

var subjectID=Cookie.Get("SubjectId"); //默认科目
Types=parent.Types; //获取main页面题型数据
window.onerror=function(){return true;}
$(document).bind("selectstart",function(){return false;});

$(document).ready(function(){
    //学科不存在返回到登录后的首页
    $.myCommon.checkSubject(U('/Home','',false));

    if(subjectID){
        switch(mark){
            case 'knowledge':
                $.knowledgeTest.init();
                break;
            case 'chapter':
                $.chapterTest.init();
                break;
            case 'doc':
                $.docTest.init();
                break;
            case 'search':
                $.searchTest.init();
                break;
        }
    }

    $.myTest.showTestEvevt(); //载入试题事件
    $.myPage.showQuickSkip();//展示三角形快速跳转分页
});