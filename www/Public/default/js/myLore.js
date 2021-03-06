$.MyLoreManager = {
    page : 1,
    template : new Template(),
    lock : false,
    rootChapter : null,
    menus : null,
    dialog : $.myDialog,
    url : '',
    widthProperties : {},
    modules : null, //板块信息
    subjectid : Cookie.Get('SubjectId'),

    init : function(url, rootChapter, menus, modules){
        var that = this;
        that.setWidthProperties();
        //对url判断
        if(url.indexOf('/')===0){
            url = url.substring(1);
        }
        this.url = url;
        that.rootChapter = rootChapter || {};
        that.menus = menus || {};
        that.modules = modules || {};
        //加载板块信息
        var moduleTag = $('#moduleSelectTag');
        var menuSelectTag = $('#menuSelectTag');
        that.loadModule(moduleTag, menuSelectTag);
        //初始化加载章节信息
        var defChapterSelect = $('#chapterRootSelect');
        that.loadChapter(defChapterSelect); 
        //添加内容列表
        $.myDialog.normalMsgBox('loadList', '提示', 500, '正在载入数据..', 5);
        $.post(U(that.url+'/getLoreList'),{'subjectid' : that.subjectid},function(data){
            if($.myCommon.backLogin(data) == false){
                return false;
            }
            that.loadListContent(data['data']); 
        })
        //添加知识
        $.Editor.init(U(that.url+'/upload?dir=lore'));
        $('#addLore').click(function(){
            that.loadContent();
            var h = ($(window).height()-200)<600?($(window).height()-180):600;
            $(".loreMsgCon").height(h);        });
        $('#searchLore').click(function(){
            that.searchLore();
        });
        $(window).resize(function(){
            that.initDivBoxHeight();
        });
    },

    setWidthProperties : function(){
        this.widthProperties['window'] = $(window).width();
        this.widthProperties['dialog'] = ($(window).width()-100)<1200?($(window).width()-80):1200;
        if((this.widthProperties['dialog'])<600) this.widthProperties['dialog']=600;    },

    initDivBoxHeight:function(){
        var a = $(window).width();
        var b = $(window).height();
        if(a<790) a=790;
        $("#loreBox").width(a).height(b);
    },

    //生成板块信息option
    loadModule : function(moduleTag, menuSelectTag){
        var that = this;
        var html = '';
        for(var m in that.modules){
             html += '<option value="'+m+'">'+that.modules[m]['name']+'</option>';
        }
        moduleTag.append(html);
        moduleTag.change(function(){
            var val = $(this).val();
            if('' == val || !that.menus[val]){
                that.loadMenu({}, menuSelectTag);
                return;
            }
            that.loadMenu(that.menus[val]['content'], menuSelectTag);
        });
    },

    //生成栏目信息option
    loadMenu : function(menus, menuSelectTag){
        var html = "<option value=''>请先选择板块</option>";
        for(var m in menus){
            var menu = menus[m];
            html += '<option value="'+menu['MenuID']+'">'+menu['MenuName']+'</option>';
        }
        menuSelectTag.html(html);
    },
    //生成章节信息option
    loadChapter : function(chapterRootSelect){
        var chapters = this.rootChapter;
        var html = '';
        for(var c in chapters){
            var chapter = chapters[c];
            html += '<option value="'+chapter.ChapterID+'">'+chapter.ChapterName+'</option>';
        }
        if(html){
            chapterRootSelect.append(html);
            chapterRootSelect.change(this.selectTagChangeHandler);
        }
    },

    //知识内容列表显示 
    //返回的数据格式 {result:{xxxx},page:{11,20}} result为数据信息，page为分页信息
    loadListContent : function(data){
        var that = this;
        var listPlace = $('#list');
        var html = that.template.setParams(['datas','num']).render($('#loreList').html(), data['result'], data['page'][0]);
        listPlace.html(html);
        listPlace.find('.showDetail').each(function(){
            $(this).click(function(){
                //发送数据
                that.showContent($(this).parent().attr('loreid'), $('#loreContentShow').html());
            });
        });
        listPlace.find('.editContent').each(function(){
            $(this).click(function(){
                that.edit($(this).parent().attr('loreid'));
            });
        });
        listPlace.find('.deletion').each(function(){
            $(this).click(function(){
                that.del($(this).parent().attr('loreid'));
            });
        });
        $.myPage.showPage(data['page'][0], data['page'][1], this.page);
        $('.pagebox a').each(function(){
            $(this).click(function(){
                that.page = $(this).attr('page');
                that.searchLore();
            });
        });
        that.initDivBoxHeight();
        $('.tcClose').trigger('click');
        //对列表中的图片标签进行缩放
        $('#list tr').each(function(){
            $(this).find('td').each(function(){
                var index = $(this).index();
                if(index < 2){
                    $(this).find('img').each(function(){
                        var that = $(this);
                        if(that.width() > 150)
                            $(this).css({'width':'150px'});
                    });
                }
            });
        });
    },

    //知识详情页面
    showContent : function(id, html){
        var that = this;
        $.post(U(that.url+'/getLoreList'), {'id' : id, 'subjectid' : that.subjectid}, function(result){
            if($.myCommon.backLogin(result) == false){
                return false;
            }
            var data = result['data']['result'][0];
            that.dialog.normalMsgBox('lorePreview', '我的知识详细', that.widthProperties['dialog'], that.template.render(html, data), 5);
            // 设置弹框高度
            var h = ($(window).height()-200)<600?($(window).height()-180):600;
            $("#lorePreview .loreContentShowCon").height(h);
            // 设置弹框表格高度，css
            var loreConh = parseInt($("#lorePreview .loreContentTable").height());
            if(loreConh<=h){
                $("#lorePreview .loreContentTable").height(h-1); $("#lorePreview .loreContentShowCon").css({"border":"none"});
            } if(loreConh>h){
                $("#lorePreview .loreContentTable").css({"margin-top":"-1px","margin-bottom":"-1px"});
            };
            that.dialog.tcDivPosition('lorePreview');            
            $('#lorePreview .normal_yes').click(function(){
                $('#lorePreview .normal_no').trigger('click');
                that.edit(id);
            });
            $('#lorePreview .normal_no').click(function(){
                $('.tcClose').trigger('click');
            });
        });
    },

    //通过给定chapterSelect选择器获取章节最终选中值
    getChapterValue : function(chapterSelect){
        var lastValue = chapterSelect.last().val();
        //如果选择元素为1个以上并且最终的值为空
        if(chapterSelect.length > 1 && lastValue == '')
            return false;
        return lastValue;
    },
    //知识查询
    searchLore : function(){
        var that = this;
        var form = $('#queryForm');
        var data = {
            'menuid' : form.find('#menuSelectTag').val(),
            'chapterid' : that.getChapterValue(form.find('.chapterSelectTag')),
            'page' : that.page,
            'subjectid' : that.subjectid
        };
        if(data.chapterid === false){
            $.myDialog.normalMsgBox('test', '错误', 500, '章节需选择到最后一级', 2);
            return false;
        }
        data.chapterid = data.chapterid.replace('c', '');
        $.myDialog.normalMsgBox('loadList', '提示', 500, '正在载入数据..', 5);
        $.post(U(that.url+'/getLoreList'),data,function(result){
            if($.myCommon.backLogin(result) == false){
                return false;
            }
            that.loadListContent(result['data']); 
        })
    },

    del : function(id){
        var that = this;
        $.myDialog.normalMsgBox('delRole','提示', 500, '确定删除试题？', 3);
        $('#delRole .normal_yes').click(function(){
            if(that.lock){
                return false;
            }
            that.lock = true;
            $.post(U(that.url+'/deleteMyLore'),{'id':id},function(data){
                that.lock = false;
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                var str = data['data'];
                if(str == 'success'){
                    $.myDialog.normalMsgBox('successDialog','结果', 500, '删除成功！' , 1);
                    $('#successDialog .normal_btn').click(function(){
                        $('.tcClose').trigger('click');
                        $.MyLoreManager.searchLore();
                    });
                }else{
                    $.myDialog.normalMsgBox('successDialog','结果', 500, '删除失败！' , 2);
                }
            });
        });
    },

    edit : function(id){
        var that = this;
        if(that.lock){
            return false;
        }
        that.lock = true;
        $.post(U(that.url+'/editMyLore'),{'id':id, r:Math.random()},function(data){
            that.lock = false;
            if($.myCommon.backLogin(data)==false){
                return false;
            }
            data = data['data'];
            that.loadContent({
                'Lore' : data['Lore'],
                'Answer' : data['Answer'],
                'MenuID' : data['MenuID'],
                'ChapterID' : data['ChapterID'],
                'showChapterZone' : data['showChapterZone'],
                'ForumID' : data['ForumID'],
                'LoreID' : id
            });
        });
        return false;
    },

    //章节选择事件
    selectTagChangeHandler : function(){
        var that = $(this);
        if(that.lock){
            return false;
        }
        that.lock = true;
        var val = that.val();
        if(that.nextAll('.chapterSelectTag').length > 0){
            that.nextAll('.chapterSelectTag').remove();
        }
        $.post(U('Index/getData'),{
            'style' : 'chapter',
            'pID' : val
        },function(result){
            that.lock=false;
            if($.myCommon.backLogin(result)==false){
                return false;
            }
            if(!result['data']){
              return false;
            }
            var newer = $('<select class="chapterSelectTag" ></select>');
            newer.html('<option value="">请选择</option>'+$.myCommon.setChapterOption(result['data'], val));
            newer.change($.MyLoreManager.selectTagChangeHandler);
            that.after(newer);
        });
    },

    //加载对话框中的内容
    loadContent : function(data){
        var h = ($(window).height()-200)<600?($(window).height()-180):600;
        var titleName = '编辑知识';
        if(!data){
            titleName = '添加知识';
        }
        data = data || {
            'LoreID' : 0,
            'Lore' : '',
            'Answer' : '',
            'MenuID' : '',
            'ChapterID' : '',
            'showChapterZone' : ''
        }
        var html = $('#editContent').html();
        html = this.template.render(html, data);
        this.dialog.normalMsgBox('addStudiv', titleName, this.widthProperties['dialog'], html, 5);
        // 设置窗口高度
        $(".loreMsgCon").height(h);
        this.dialog.tcDivPosition('addStudiv');
        var module = $('#showModuleMenu');
        var menu = $('#showContentMenu');
        this.loadModule(module, menu);
        this.loadChapter($('#loadContentSelect'));
        if(data['MenuID'] && data['ForumID']){
            module.val(data['ForumID']);
            module.trigger('change');
            menu.val(data['MenuID']);
        }
        var opt = {
            toolbars: [[
             'source', 'bold', 'italic', 'underline', '|', 'fontsize', 'forecolor', '|', 'simpleupload','wordimage', 'scrawl'
            ]],
            initialFrameWidth : '100%'
        };
        $.Editor.container = $('.loreEditor');
        $.Editor.createContent(data['Lore'], $.extend({
          'textarea' : 'Lore',
          'title' : '知识'
        },opt)); 
        $.Editor.container = $('.anwserEditor');
        $.Editor.createAnalyze(data['Answer'],  $.extend({
          'textarea' : 'Answer',
          'title' : '答案'
        },opt));
        $('#saveLore').click(this.save);
        $('#cancelSave').click(function(){
            $('.tcClose').trigger('click');
        });
    },
    //新增，修改保存
    save : function(){
        if($.MyLoreManager.lock){
            $.myDialog.normalMsgBox('successDialog','错误', 500, '数据正在加载中，请稍候！' , 2);
            return false;
        }
        var form = $('#saveForm');
        if($('#showContentMenu').val() == ''){
            $.myDialog.normalMsgBox('test', '错误', 500, '请选择栏目', 2);
            $('#showContentMenu').focus();
            return false;
        }
        var chapter = $.MyLoreManager.getChapterValue(form.find('.chapterSelectTag'));
        var chapterRealValue = $('#chapterRealValue');
        if(chapter === false || !chapter && chapterRealValue.val() == ''){
            $.myDialog.normalMsgBox('test', '错误', 500, '章节需选择到最后一级', 2);
            return false;
        }
        chapter = chapter.replace('c', '');
        if(chapter)
            chapterRealValue.val(chapter);
        data = form.serialize();
        $.MyLoreManager.lock = true;
        $.post(form.attr('action'), data, function(data){
            $.MyLoreManager.lock = false;
            if($.myCommon.backLogin(data)==false){
                return false;
            }
            if(data['data'] == 'success'){
                $.myDialog.normalMsgBox('successDialog','结果', 500, '保存成功！' , 1);
                $('#successDialog .normal_btn').click(function(){
                    // $('.tcClose').trigger('click');
                    $.MyLoreManager.searchLore();
                });
            }else{
                $.myDialog.normalMsgBox('successDialog','结果', 500, data['data'] , 2);
            }
        }); 
    },
    //分页显示数据
    showpage : function(total,prepage,recordTotal,container, showPageList){
        if(total == 0){
            container.hide();
            return false;
        }
        showPageList = showPageList || 10;
        container.show();
        var lastpagelist='<div class="pagebox">';
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
        container.html(lastpagelist);
    }
}