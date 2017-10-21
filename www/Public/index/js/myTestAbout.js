/**
 * 官网首页用户中心试题相关的JS
 * @author demo
 * @date 2015/12/10.
 */
//本地数据存储(复制于组卷端common.js文件),用于历史存档页面恢复存档时使用
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
//试卷历史存档
jQuery.indexMyTestAbout = {
    loading:'',//等待提示
    alertWindow:'',//弹框
    thisPage:1,//当前页码
    ifLock:'',//锁定标识
    subjectName:'',//学科名称
    subjectID : 0,//默认学科ID
    dated : "all",
    functionName : '',
    style : '',//当前所属程序
    //历史存档初始化程序
    init:function(){
        this.subjectID = $.cookie("SubjectId");
        this.style = 'DocSave';
        this.functionName = 'User/IndexCenter/myTestDocSaveList';
        this.myTestAboutClick();
        this.docClickBind();
        this.ajaxGetPaper();
    },
    //试题收藏初始化
    myTestFavInit:function(){
        this.subjectID = $.cookie("SubjectId");
        this.getMyTestFavData();
        this.loadFavTree();
        this.myTestFavClick();
    },
    //试题反馈初始化
    myTestCommentInit:function(){
        this.subjectID = $.cookie("SubjectId");
        this.getMyTestCommentData();
        this.myTestCommentClick();
    },
    //历史下载初始化
    myDownInit:function(){
        this.subjectID = $.cookie("SubjectId");
        this.style = 'Down';
        this.functionName = 'User/IndexCenter/myTestDownList';
        this.myTestAboutClick();
        this.myDownBindClick();
        this.ajaxGetPaper();
        $('#tmpdown').live('click',function(){
            $('#paperdowndiv .tcClose').click();
        });
    },
    //重置列表序号
    resetOrderNum:function(idName,childClass){
        $('#'+idName+' .'+childClass).each(function(i){
            var m=parseInt(i)+1;
            $(this).find('.order').html(m);
        })
    },
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
     */
    showPage:function(total,prePage,page) {
        this.clearPage();
        var pageNum = Math.floor((page - 1) / 10);//页码基数
        var pageCount = Math.ceil(total / prePage);//总页数
        var lastPageList = total+' 条记录 '+page+'/'+pageCount+'页              ';
        if (page > 1) {
            var prev = parseInt(page)-1;
            lastPageList += '<a page="'+prev+'" href="javascript:void(0);">上一页</a>';
        }
        if (page < pageCount) {
            var next = parseInt(page)+1;
            lastPageList += '<a page="' + next + '" href="javascript:void(0);" title="下一页">下一页</a>';
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

        $("#pagelistbox").html(lastPageList);
    },
    //试题相关的一些公共事件
    myTestAboutClick:function(){
        var self = this;
        //时间TAB点击事件
        $('.dated').on('click',function(){
            $(this).addClass('on');//添加选中状态
            $('.tab-panel').addClass('on');//跟通用的TAB切换有些区别
            self.thisPage = 1;//初始化页码
            self.dated = $(this).attr('id');//获取时间标识
            self.ajaxGetPaper();//获取内容
        });
        //切换学科
        $('.subject').on('change',function(){
            self.subjectID = $(this).val();//学科ID
            self.thisPage = 1;//初始化第一页
            self.ajaxGetPaper();
        });
        //切换分页
        $('#pagelistbox').on('click','a',function(){
            self.thisPage = $(this).attr('page');
            self.ajaxGetPaper();
        });
    },

    loadFavTree : function(){
        var obj = $('#favTree');
        $.post(U('User/IndexCenter/getUserCatalog'), {
                'fid' : 0
            }, function(result){
                var data = result['data'][0];
                var html = '<li><a href="javascript:;" class="on favTop showContent" favid="all">全部</a></li>';
                if(data.length > 0){
                    html += res(data, false, '<li class="fav"><a href="javascript:;" favid="%ID%" class="favTop showContent">%NAME%</a><a href="javascript:;" class="showChildGroup" title="显示子收藏夹"><i class="iconfont select-arrow">&#xe607;</i></a></li>');
                }
                obj.html(html);
            });
        var _this = this;
        $('.showContent').live('click', function(that){
            var that = $(this);
            if(that.hasClass('favTop')){
                $('#favChildGroup').hide();
            }
            _this.getMyTestFavData(that.attr('favid'));
            that.addClass('on').parent().siblings('li').each(function(){
                $(this).find('a').removeClass('on');
            });
        });
        //是否在编辑模式
        var isEditMode = function(){
            var html = $('#editFavInfo').find('span').html();
            return html == '完成';
        }
        var appendEditor = function(that){
            if(that.hasClass('favTop') && that.parent().index() == 0){ //不能编辑全部的内容
                return;
            }
            var html = '<a title="重命名" href="javascript:;" class="rename"><i class="iconfont">&#xe601;</i></a>&nbsp;<a title="删除" href="javascript:;" class="delFavDir"><i class="iconfont"></i></a>';

            if(that.hasClass('favTop')){
                html = '<a title="添加子收藏夹" href="javascript:;" class="addChildFav"><i class="iconfont">+</i></a>&nbsp;'+html;
            }
            html = '<span class="this-group-handle editFav">'+html+'</span>';
            that.parent().append(html);
        }
        //编辑目录
        $('#editFavInfo').click(function(){
            var span = $(this).find('span');
            var html = '';
            if(!isEditMode()){
                html = '完成';
                $('.showContent').each(function(){
                    appendEditor($(this));
                });
            }else{
                $('.showContent').parent().find('.editFav').remove();
                html = '编辑';
            }
            span.html(html);
        });
        var addFavGroup = function(fun, parentId){
            parentId = parentId || 0;
            layer.open({
                type:1,
                title:'添加收藏夹',
                area: ['360px', '200px'], //宽高
                btn: ['确定', '取消'],
                content:'<div style="margin:5px;">收藏夹名称：<input type="text" id="favName" style="width:99%;"/>排序：<input type="text" id="favOrder" style="width:99%;" value="99"/></div>',
                yes: function(){
                    var name = $('#favName').val();
                    if(!name){
                        alert('收藏夹名称不能为空！');
                        return false;
                    }
                    var order = $('#favOrder').val();
                    if(!parseInt(order)){
                        alert('排序规则必须为数字！');
                        return false;
                    }
                    var data = {
                        'parent' : parentId,
                        'name' : name,
                        'order' : order
                    }
                    $.post(U('User/IndexCenter/addFav'), data, function(result){
                        result = result['data'];
                        if(result.indexOf('success') < 0){
                            alert(result);
                        }else{
                            alert('添加成功！');
                            layer.closeAll();
                            result = result.replace('success|','');
                            fun({
                                'id' : result,
                                'name' : name
                            });
                        }
                    });
                }
            });
        }
        //添加子收藏夹
        $('#addFavGroup').click(function(){
            addFavGroup(function(data){
                var html = $('<li class="fav"><a href="javascript:;" favid="'+data['id']+'" class="favTop showContent">'+data['name']+'</a><a href="javascript:;" class="showChildGroup" title="显示子收藏夹"><i class="iconfont select-arrow">&#xe607;</i></a></li>');
                $('#favTree').append(html);
                if(isEditMode()){
                    appendEditor(html.find('.showContent'));
                }
            });
        });
        $('.addChildFav').live('click', function(){
            var that = $(this);
            var id = that.parents('.fav').find('.showContent').attr('favid');
            addFavGroup(function(data){
                var html = $('<li class="fav"><a href="javascript:;" class="showContent" favid="'+data.id+'">'+data.name+'</a></li>');
                $('#favChildGroup').find('ul').append(html);
                if(isEditMode()){
                    appendEditor(html.find('.showContent'));
                }
            }, id);
        });
        //目录重命名
        $('.editFav .rename').live('click', function(){
            var obj = $(this).parents('.fav').find('.showContent');
            var id = obj.attr('favid');
            layer.open({
                type:1,
                title:'重命名',
                area: ['260px', '150px'], //宽高
                btn: ['确定', '取消'],
                content:'<div style="margin:5px;"><input type="text" id="rename" style="width:200px;" value="'+obj.html()+'"/></div>',
                yes: function(){
                    var name = $('#rename').val();
                    $.post(U('User/IndexCenter/rename'),{'id':id, 'name':name}, function(result){
                        result = result['data'];
                        if(result !== 'success'){
                            alert(result);
                        }else{
                            obj.html(name);
                            alert('修改成功！');
                            layer.closeAll();
                        }
                    });
                }
            });
        });
        //删除目录
        $('.editFav .delFavDir').live('click', function(){
            var obj = $(this).parents('.fav').find('.showContent');
            var id = obj.attr('favid');
            layer.open({
                type:1,
                title:'删除收藏夹',
                area: ['350px', '150px'], //宽高
                btn: ['确定', '取消'],
                content:'<div style="margin:5px; width:98%;">删除该收藏夹，将会同时删除所有子收藏夹和试题！确定删除？</div>',
                yes: function(){
                    $.post(U('User/IndexCenter/delCatalogByID'),{'catalogID':id}, function(result){
                        result = result['data'];
                        if(result !== 'success'){
                            alert(result);
                        }else{
                            $('.fav').eq(0).find('.showContent').trigger('click');
                            obj.parent().remove();
                            alert('删除成功！');
                            layer.closeAll();
                        }
                    });
                }
            });
        });
        obj.find('.showChildGroup').live('click', function(){
            var favChildGroup = $('#favChildGroup');
            favChildGroup.hide();
            var that = $(this);
            var _parent = that.prev();
            var id = _parent.attr('favid');
            _parent.addClass('on').parent().siblings('li').each(function(){
                $(this).find('a').removeClass('on');
            });
            $.post(U('User/IndexCenter/getUserCatalog'), {
                'fid' : 0
            }, function(result){
                var mark = false;
                var data = result['data'][0];
                var childGroup = favChildGroup.find('ul').html('');
                for(var val in data){
                    val = data[val];
                    if(val.CatalogID == id && val.child){
                        mark = true;
                        var group = val.child;
                        for(var v in group){
                            var html = $('<li class="fav"><a href="javascript:;" class="showContent" favid="'+group[v].CatalogID+'">'+group[v].CatalogName+'</a></li>');
                            childGroup.append(html);
                            if(isEditMode()){
                                appendEditor(html.find('.showContent'));
                            }
                        }
                    }
                }
                if(mark){
                    favChildGroup.show();
                }
            });
        });

    },

    //收藏试题点击事件
    myTestFavClick:function(){
        var self = this;
        //显示或隐藏答案 点击试题题文
        $(document).on('click','.testBody',function(){
            var adiv=$(this).next('.testAnswer');
            var tid=adiv.attr('tid');
            if(tid=='0'){
                return false;
            }
            if($(this).next('.testAnswer').css('display')=='block'){
                $(this).next('.testAnswer').css('display','none');
                $(this).parent().find('.quesparse').css('display','none');
                $(this).parent().find('.quesremark').css('display','none');
            }else{
                if($(this).next('.testAnswer').attr('show')==0){
                    self.loading = layer.load();//等待提示
                    $.post(U('User/IndexCenter/myTestFavOneInfo'),{'id':tid},function(data){
                        layer.close(self.loading);//关闭等待提示
                        if(data['status'] == '1'){
                            var testOther = data['data'];
                            var str = '<div class="answer-item">' +
                                '    <h3 class="tit"><b>答案</b></h3>' +
                                '    <div class="answer-context f-roman">'+testOther['answer']+'</div>' +
                                '</div>';
                            if(testOther['analytic']!='' && testOther['analytic']!='</p>'){
                                str += '<div class="answer-item">' +
                                    '    <h3 class="tit"><b>解析</b></h3>' +
                                    '    <div class="answer-context f-roman">'+testOther['analytic']+'</div>' +
                                    '</div>';
                            }
                            if(testOther['kllist']){
                                str += '<div class="answer-item">' +
                                    '    <h3 class="tit"><b>知识点</b></h3>' +
                                    '    <div class="answer-context f-roman">' +
                                    '        <div>' + testOther['kllist'] + '</div>' +
                                    '    </div>' +
                                    '</div>';
                            }
                            if(testOther['remark'] && testOther['remark']!='</p>'){
                                str += '<div class="answer-item">' +
                                    '    <h3 class="tit"><b>备注</b></h3> ' +
                                    '    <div class="answer-context f-roman">'+testOther['remark']+'</div> ' +
                                    '</div>';
                            }

                            adiv.html(str);
                            adiv.attr('show',1);
                        }else{
                            layer.msg(data['data'],{icon:5});
                        }
                    });
                }
                adiv.css('display','block');
            }
        });
        //删除
        $(document).on('click','.delFav',function(){
            var tid = $(this).parent().attr('tid');
            if(tid == 0){
                return false;
            }
            var alertHTML = '<div class="alertMessage">' +
                '    <b class="delThisPaper">您确定要删除？</b>' +
                '</div>';
            self.alertWindow = layer.open({
                type:1,
                title:'温馨提醒',
                area: ['260px', '150px'], //宽高
                btn: ['确定', '取消'],
                content:alertHTML,
                yes: function(){
                    layer.close(self.alertWindow); //如果设定了yes回调，需进行手工关闭
                    self.deleteTestFav(tid);//移除内容
                }
            });
        });
        //移动
        $(document).on('click','.moveFav',function(){
            var tid = $(this).parent().attr('tid');
            if(tid == 0){
                return false;
            }
            $.post(U('User/IndexCenter/getUserCatalog'), {
                'fid' : 0
            }, function(result){
                var data = result['data'][0];
                if(data.length == 0){
                    alert("请先创建收藏夹！");
                    return false;
                }
                var html = res(data, '<option value="%ID%">%NAME%</option>', '<optgroup label="%NAME%"></optgroup>');
                var html = '<div id="tplMoveTest">' +
                            '    <div class="move-test-content"> ' +
                            '        <div class="g-control-group">' +
                            '            <label for="">移动试题至：</label>' +
                            '            <select name="" id="catalog">' + html +'</select>' +
                            '        </div>' +
                            '    </div>' +
                            '</div>';
                self.alertWindow = layer.open({
                    type:1,
                    title:'移动试题',
                    area: ['360px', '200px'], //宽高
                    btn: ['确定', '取消'],
                    content:html,
                    yes: function(){
                        //self.deleteTestFav(tid);//移除内容
                        var data = {};
                        var catalog = $('#catalog').val();
                        if(!catalog){
                            alert('请选择收藏夹');
                            return false;
                        }
                        data['catalogID'] = catalog;
                        data['id'] = tid;
                        $.post(U('User/IndexCenter/updateFavSave'), data, function(result){
                            layer.close(self.alertWindow); //如果设定了yes回调，需进行手工关闭   
                            if(result['data'] === 'success'){
                                alert('移动成功！');
                            }else{
                                alert('操作失败！');
                            }
                        });
                    }
                });
            });
        });
        //评论
        $('.commentOfFav').live('click', function(){
            var that = $(this);
            var id = that.parent().attr('tid');
            var html = $('#tplCommentTest').html();
            layer.open({
                type: 1,
                title: "评论试题",
                area: ["590px", "540px"],
                shadeClose: true,
                content: html,
                success : function(dom){
                    dom = $(dom);
                    dom.find('.scorebox').ScoreBox({
                        callback : function(score, current){
                            $('.quesscore').html(score).attr('val', score);
                        }
                    });
                    dom.find('.commentTestId').html(id);
                    dom.find('.addComment').click(function(){
                        var content = dom.find('.commentContent').val();
                        if(content == ''){
                            alert('评论内容不能为空！');
                            dom.find('.commentContent').focus();
                            return false;
                        }
                        var data = {
                            'comment' : content,
                            'score' : dom.find('.quesscore').html(),
                            'id' : id
                        }
                        $.post(U('User/IndexCenter/addComment'), data, function(result){
                            result = result['data'];
                            if(result == 'success'){
                                alert('评论成功！');
                            }else{
                                alert(result);
                            }
                            layer.closeAll();
                        });
                    });
                    $.post(U('User/IndexCenter/commentList'), {'id':id}, function(result){
                        result = result['data'];
                        appendCommentList(result, dom);
                    });
                },
                yes : function(){

                }
            });
        });
        //加载评论列表
        var appendCommentList = function(result, dom){
            var list = result['comments'];
            var paginator = result['pagtion'];
            var panel = dom.find('.commentListPanel');
            var pagePanel = dom.find('.paginator');
            if(panel.find('li').length > 0){
                panel.html('');
            }
            if(list['data'].length > 0){
                var html = $('#commentList').html();
                for(var i=0; i<list['data'].length; i++){
                    panel.append(html.replace('%NAME%', list['data'][i]['UserName']).
                         replace('%TIME%', list['data'][i]['LoadDate']).
                         replace('%DES%', list['data'][i]['Content']));
                }
                html = list["count"]+' 条记录 '+ list["page"]+ '/' + Math.ceil(list["count"]/list["prepage"]) +' 页';
                for(var val in paginator){
                    val = paginator[val];
                    if(val.c != ''){
                        html += '<span class="current pagetion">'+val.n+'</span>'
                    }else{
                        html += '<a href="'+val.a+'" class="pagetion">'+val.n+'</a>'
                    }
                }
                pagePanel.html(html);
            }else{
                pagePanel.html('');
                panel.html('<li class="clearfix">暂无评论</li>');
            }
        };
        $('.pagetion').live('click', function(){
            var that = $(this);
            $.get(that.attr('href'), function(result){
                result = result['data'];
                appendCommentList(result, that.parents('.paginator').parent());
            });
            return false;
        });
        $.Editor.setEditor('__URL__/upload?dir=correctTest', '.editor', '我来说两句~', {
            'toolbars': [['bold', 'italic', 'underline', '|', 'fontsize', 'forecolor', '|', 'simpleupload', 'scrawl', 'wordimage']],
            'textarea' : 'correctcontent',
            'initialFrameWidth' : '100%',
            'initialFrameHeight' : 120,
            'title' : '纠错内容'
        });
        $.Editor.instance['correctcontent'].addListener('click', function(){
            var content = $.Editor.instance['correctcontent'].getContentTxt();
            if('我来说两句~' == content){
                $.Editor.instance['correctcontent'].setContent('<p></p>');
                $.Editor.instance['correctcontent'].focus();
            }
        });
        $.Editor.instance['correctcontent'].addListener('blur', function(){
            var content = $.Editor.instance['correctcontent'].getContentTxt();
            if('' == content){
                $.Editor.instance['correctcontent'].setContent('<p>我来说两句~</p>');
            }
        });
        //纠错
        $(".correctError").live("click",function(){
            var testid = $(this).parent().attr('tid');
            var html = $("#tplErrorTest").html().replace('%ID%', testid);
            layer.open({
                type: 1,
                zIndex : 111,
                title: "试题纠错",
                area: ["590px", "375px"],
                shift:5,
                btn : ['保存', '取消'],
                shadeClose: true,
                yes : function(index, layero){
                    var errType = layero.find('input[name="errType"]:checked');
                    if(0 == errType.length){
                        errType = 0;
                    }else{
                        var arr = [];
                        errType.each(function(){
                            arr.push($(this).val());
                        });
                        errType = arr.join(',');;
                    }
                    var err = $.Editor.instance['correctcontent'].getContent();
                    if(err.indexOf('我来说两句') >= 0){
                        alert('请输入内容');
                        return false;
                    }
                    var data = {
                        'testID' : testid,
                        'TypeID' : errType,
                        'correctcontent' : err
                    }
                    $.post(U('User/IndexCenter/correct'), data, function(result){
                        result = result['data'];
                        if(result['status'] == 'success'){
                            alert('保存成功！');
                            layer.close(index);
                        }else{
                            alert('添加失败！');
                        }
                    });
                    return false;
                },
                content: html
            });
        });
        //加入试卷
        $('.delques1').live('click',function(){
            var tmp_str=editData.selecttest($(this).attr('quesid'));
            var testid=$(this).attr('quesid');
            if(tmp_str){
                editData.deltest($(this).attr('quesid'));
                $.myTest.updateMainTypes(0-$(this).attr('childnum'),tmp_str);
                $(this).addClass('addquessel1').removeClass('delques1').parent().removeClass('added');
                $(this).find('.iconfont').html('加入试卷');
                $(this).next().addClass('selmore1');
            }
        });

        $('.selmore1').live('click',function(){
            var typemsg=editData.gettypename();
            var testid=$(this).attr('testid');
            var num=$(this).attr('childnum');
            var qyid=$(this).attr('qyid');
            var len=typemsg.length;
            var idname='testinsert';
            var i;
            var tmp_str='';
            tmp_str+='<div><div class="add-test-content" id="tplAddTest" >请选择所加入：<select name="testtypemsg" id="testtypemsg">'+
                '<option value="" text="reempty">请选择</option>';
            for(i=0;i<len;i++){
                tmp_str+='<option value="'+typemsg[i][0]+'">'+typemsg[i][1]+'</option>';
            }
            tmp_str+='</select>';
            tmp_str+='<input type="hidden" name="testid" id="thistestid" value="'+testid+'">';
            tmp_str+='<input type="hidden" name="num" id="num" value="'+num+'">';
            tmp_str+='<input type="hidden" name="qyid" id="qyid" value="'+qyid+'"></div></div>';
            var typedivid='';
            var typename='';
            $('#testtypemsg').live('change', function(){
                typedivid = $(this).val();
                typename = $(this).find('option:selected').text();
            });
            layer.open({
                type: 1,
                title: '选入试题【编号：'+testid+'】入栏',
                area: ["450px", "180px"],
                shadeClose: true,
                btn: ["确定", "取消"],
                content: tmp_str,
                yes:function(){
                    var qyid=$('#qyid').val();
                    var num=$('#num').val();
                    if(typedivid==''){
                        alert('您还没有选择题型!',1);
                        return false;
                    }
                    //试题ID 小题数 题型名称 题型ID
                    //检验添加的试题是否超出
                    var result= $.myTest.checkIfOver(qyid,typename);
                    if(!result[0]){
                        layer.open({
                            title : '提示',
                            area : ['200px', '150px'],
                            btn : ['确定'],
                            content : '题型【'+result[1]+'】的题数已超出限制数量！',
                            yes : function(){
                                layer.close(2);
                            }
                        });
                        return false;
                    }
                    if(!editData.addtest(testid,num,typename,qyid))
                        return false;
                    self.appendTest($('#quesselect'+testid), num, typename);
                    layer.closeAll();
                }
            })
        });

        $('.addquessel1').live('click',function(){
            var that = $(this);
            var typeid=that.attr('qyid');
            var typename=that.attr('qyname');
            var result= $.myTest.checkIfOver(typeid,typename);
            if(!result[0]){
                layer.open({
                    title : '提示',
                    area : ['200px', '150px'],
                    btn : ['确定'],
                    content : '题型【'+result[1]+'】的题数已超出限制数量！',
                    yes : function(){
                        layer.close(2);
                    }
                });
                return false;
            }else{
                if(!editData.addtest(that.attr('quesid'),that.attr('childnum'),that.attr('qyname'),that.attr('qyid'),that.attr('score'),that.attr('choosenum'),that.attr('choosetype'))){
                    return false;
                }
                self.appendTest(that, that.attr('childnum'), typename);
            }
        });
    },

    appendTest : function(that, num, name, typeid){
        var testid=that.attr('quesid');
        that.addClass('delques1').removeClass('addquessel1').parent().addClass('added');
        that.find('.iconfont').html('移除试卷');
        that.next().removeClass('selmore1');
    },
    //试题反馈的点击事件
    myTestCommentClick:function(){
        var self = this;
        var isClick = 'all';
        //试题反馈切换
        $('.comment').on('click',function(){
            $('.tab-panel').addClass('on');//跟通用的TAB切换有些区别
            var act = $(this).attr('id');
            if(act == isClick){//重复点击不做任何处理
                return false;
            }
            if(act == 'all'){//切换至所有评价
                isClick = 'all';
                self.getMyTestCommentData(0,10,1);
            }else if (act == 'my'){//切换至我的评价
                isClick = 'my';
                self.getMyTestCommentData(1,10,1);
            }
        });
        $(document).on('click','.showTestDetail',function(){
            var testID = $(this).attr('tid');
            self.loading = layer.load();//等待提示
            $.post(U('Home/Index/getDetailTestById'),{'id':testID},function(e){
                layer.close(self.loading);//关闭等待提示
                if(e.status == 1){
                    self.alertOneTestHTML(e.data[0][testID]);
                }else{
                    layer.msg(e.data,{icon:5});
                }
            });
        })
    },
    //弹框展示试题内容
    alertOneTestHTML:function(data){
        var contentHTML = '<div id="commentDetailTest" class="">' +
                          '    <div class="test-detail-msg-content">' +
                          '        <div class="u-test-item g-test-item">' +
                          '            <div class="test-item-head clearfix">' +
                          '                <div class="left test-base-info">' +
                          '                    <h4 class="test-title f-roman">'+data.docname+'</h4>' +
                          '                    <div class="test-attr-site">' +
                          '                        <span class="test-attr">' +
                          '                            <b>题号：</b>' +data.testid+
                          '                        </span>' +
                          '                        <span class="test-attr">' +
                          '                            <b>题型：</b>' +data.typesname+
                          '                        </span>' +
                          '                        <span class="test-attr">' +
                          '                            <b>难度：</b>' +
                          '                            <i class="test-diff-icon i-star-s">' + data.diffxing+'</i>' +
                          '                        </span>' +
                          '                    </div>' +
                          '                </div>' +
                          '                <span class="right timer">'+data.firstloadtime+'</span>' +
                          '            </div>' +
                          '            <div class="test-item-body f-roman">' +data.test+'' +
                          '                <div class="test-answer-content">' +
                          '                    <div class="answer-item">' +
                          '                        <h3 class="tit"><b>答案</b></h3>' +
                          '                        <div class="answer-context f-roman">'+data.answer+'</div>' +
                          '                    </div>' +
                          '                    <div class="answer-item">' +
                          '                        <h3 class="tit"><b>解析</b></h3>' +
                          '                        <div class="answer-context f-roman">'+data.analytic+'</div>' +
                          '                    </div>' +
                          '                    <div class="answer-item">' +
                          '                        <h3 class="tit"><b>备注</b></h3>' +
                          '                        <div class="answer-context f-roman">'+data.remark+'</div>' +
                          '                    </div>' +
                          '                </div>' +
                          '            </div>' +
                          '        </div>' +
                          '    </div>' +
                          '</div>';
        layer.open({
            type : 1,
            area : ['580px','500px'],
            title : '试题详情',
            content : contentHTML
        });
    },
    //历史存档点击事件
    docClickBind:function(){
        var self = this;
        //删除历史存档
        $("#myDocSaveListC").on("click",'.del', function() {
            var paperID = $(this).parent().attr("order");
            var paperName=$('#order'+paperID).find('.paperName').html();
            var alertHTML = '<div class="alertMessage">' +
                            '    <b class="delThisPaper" paperid="'+paperID+'">您确定删除【'+paperName+'】文档？</b>' +
                            '</div>';
            self.alertWindow = layer.open({
                type:1,
                title:'温馨提醒',
                area: ['620px', '150px'], //宽高
                btn: ['确定', '取消'],
                icon:3,
                content:alertHTML,
                yes: function(){
                    layer.close(self.alertWindow); //如果设定了yes回调，需进行手工关闭
                    self.delPaper(paperID);//移除内容
                }
            });
        });
        //恢复存档
        $("#myDocSaveListC").on("click",'.reload', function() {
            var id = $(this).parent().attr("order");
            var key = $(this).parent().attr("paperKey");
            if(key!=''){
                self.alertWritePW(id);
                return false;
            }
            self.reloadPaper(id,key);
        });
    },
    //展示试题结构
    myTestFavHTML:function(arr){
        var output='';
        for(var i=0;i<arr.length;i++){
            if(arr[i]['testid']==null || arr[i]['testid']=='' ||  arr[i]['testid']=='undefined'){
                continue;
            }
            var parentClassName='',classname='addquessel1',nextClassName='selmore1';
            var name = '加入试卷';
            if(editData.ifhavetest(arr[i]['testid'])){
                classname='delques1';
                parentClassName = 'added';
                nextClassName = ''
                name = '移除试卷';
            }
            output += '<div class="u-test-item g-test-item" id="testList'+arr[i]['testid']+'">' +
                      '    <div class="test-item-head clearfix">' +
                      '        <div class="left test-base-info">' +
                      '            <h4 class="test-title f-roman">' + arr[i]['docname'] + '</h4>' +
                      '            <div class="test-attr-site">' +
                      '                <span class="test-attr">' +
                      '                    <b>题号：</b>' + arr[i]['testid'] +
                      '                </span>' +
                      '                <span class="test-attr">' +
                      '                    <b>题型：</b>' + arr[i]['typesname'] +
                      '                </span>' +
                      '                <span class="test-attr">' +
                      '                    <b>难度：</b>' + arr[i]['diffxing'] +
                      '                </span>' +
                      '            </div>' +
                      '        </div>' +
                      '        <span class="right timer">'+arr[i]['firstloadtime']+'</span>' +
                      '    </div>' +
                      '    <div class="test-item-body f-roman">'+
                      '        <div class="testBody" title="点击查看解析">'+arr[i]['test']+'</div>'+
                      '        <div class="test-answer-content testAnswer" tid="'+arr[i]['testid']+'" show="0" style="display:none"></div>' +
                      '    </div>' +
                      '    <div class="test-item-footer clearfix">' +
                      '        <div class="left" tid="'+arr[i]['testid']+'">' +
                      '            <a class="g-btn delFav" href="javascript:;">' +
                      '                <i class="iconfont">&#xe60b;</i>删除' +
                      '            </a>' +
                      '            <a class="g-btn moveFav" href="javascript:;">' +
                      '                <i class="iconfont">&#xe608;</i>移动' +
                      '            </a>' +
                      '            <a class="g-btn commentOfFav" href="javascript:;">' +
                      '                <i class="iconfont">&#xe605;</i>评论' +
                      '            </a>' +
                      '            <a class="g-btn correctError" href="javascript:;">' +
                      '                <i class="iconfont">&#xe606;</i>纠错' +
                      '            </a>' +
                      '        </div>' +
                      '        <div class="right" tid="'+arr[i]['testid']+'">' +
                      '<span href="javascript:;" class="add-test-group '+parentClassName+'">' +
                      '      <span class="add-test-paper g-btn '+classname+'"  qdname="'+arr[i]['diffname']+'" qdid="'+ arr[i]['diff'] +'" qyisselect="'+ arr[i]['typesisselect']+'" qyname="'+arr[i]['typesname']+'" qyid="'+ arr[i]['typesid'] +'" childnum="'+ (arr[i]['testnum'] == 0 ? 1 : arr[i]['testnum'])+'" quesid="'+ arr[i]['testid'] +'" classify="1" id="quesselect'+ arr[i]['testid'] +'"> <i class="iconfont">'+name+'</i>' +
                      '      </span> <i class="iconfont g-btn select-arrow '+ nextClassName +'" testid="'+ arr[i]['testid'] +'" id="selmore'+ arr[i]['testid'] +'" qyid="'+ arr[i]['typesid'] +'" childnum="'+ (arr[i]['testnum'] == 0 ? 1 : arr[i]['testnum']) +'"></i>' +
                      '  </span>' +
                      '        </div>' +
                      '    </div>' +
                      '</div>';
        }
        return output;
    },
    //我的收藏试题列表
    myTestFavList:function(paperdata) {
        var self = this;
        var html = [];
        if(paperdata) {
            html.push(self.myTestFavHTML(paperdata));
        }else{
            html.push("<div style='padding:10px;'>暂无试题收藏记录！</div>");
        }
        $("#myTestFavList").html(html.join(""));
    },
    //获取收藏的试题内容
    getMyTestFavData:function(cataId){
        var self = this;
        self.loading = layer.load();//等待提示
        if (typeof cataId == "undefined" || cataId == null) { cataId = 'all'; }//默认加载全部
        $.post(U('User/IndexCenter/myTestFavList'),{catalogID:cataId, 'subjectID':self.subjectID,page:self.thisPage ,m:Math.random()},function(data){
            if(data.status == 1){
                var paperdata = data['data'][0];
                var papercount = data['data'][1];
                var percount = data['data'][2];
                var pagecount = Math.ceil(parseInt(data['data'][1])/parseInt(data['data'][2]));

                if(papercount==0){
                    $("#paperlistbox").html('<p class="list_ts tips2">暂时没有收藏。</p>');
                    return false;
                }

                if (pagecount == 0) { pagecount = 1; }
                if (self.page <= 1) { self.page = 1; }
                if (self.page >= pagecount) { self.page = pagecount; }
                //$("#pagecount").html(pagecount);
                self.myTestFavList(paperdata);
                self.showPage(papercount,percount,self.thisPage);
            }else{
                $("#myTestFavList").html('<p class="list_ts tips2">暂时没有收藏。</p>');
            }
            layer.close(self.loading);//关闭等待提示
        });
    },
    //删除收藏试题
    deleteTestFav:function(tid){
        var self = this;
        self.ifLock = 'User/IndexCenter/myTestFavDelete';
        $.post(U('User/IndexCenter/myTestFavDelete'),{tid:tid,subjectID:self.subjectID},function(e){
            if(e.status == 1){
                //提示成功
                layer.msg('删除成功',{icon:1});
                //移除页面试题
                $('#testList'+tid).remove();
                //重新获取数据
                self.getMyTestFavData('all');
            }else{//删除失败
                layer.msg(e.data,{icon:5});
            }
        });
    },
    //移动收藏试题
    moveCollectTest:function(){
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
    //确定移动收藏试题
    saveMovesCollectTest:function(){
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
    //获取可移动的收藏目录
    getCollectMenu:function(tid){

    },
    //获取试题反馈
    getMyTestCommentData:function(ifUser,curPage,pageSize){
        var self = this;
        if (ifUser == null || ifUser == undefined) { ifUser = 0; }
        if (curPage == null || curPage == undefined || curPage <= 0) { curPage = 1; }
        if (pageSize == null || pageSize == undefined || pageSize <= 0) { pageSize = 10; }
        $.post(U('User/IndexCenter/getMyTestCommentList'),{'ifUser':ifUser,'curPage':curPage,'pageSize':pageSize},function(e){
            if(e['data'][1] == 0 || e['data'][0] == []){
                $("#commentBox").html('<div class="no-data-tips"><p class="list_ts">抱歉！暂时没有评论。</p></div>');
                $('#pagelistbox').html('');
            }else{
                $.indexMyTestAbout.showMyTestCommentList(e['data'][0]);
                self.showPage(e['data'][1], e['data'][2], curPage, 1);
            }
        });
    },
    //展示试题反馈列表HTML
    showMyTestCommentList:function(commentData){
        var commentList = commentData;
        var commentHTML = '';
        if (commentList == null || commentList == undefined) {
            commentHTML = '<div class="no-data-tips">暂无记录</div>';
            $("#commentBox").html(commentHTML);
            return false;
        }
        var len = commentList.length;
        commentHTML ='<ul class="comment-test-list">';
        for (var i = 0; i < len; i++) {
            var d = commentList[i];
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
            commentHTML += '<li class="clearfix">' +
                            //'<img class="user-photo" src="" alt="头像" width="42" height="42">' +
                           '    <div class="ct-right">' +
                           '        <div class="clearfix">' +
                           '            <p class="left elli">' +
                           '                <a class="user" href="javascript:;">'+ d.UserName +'</a>' +
                           '                <span class="helper">给' +
                           '                    <a href="javascript:;" class="link showTestDetail" tid="'+ d.TestID+'">题号:'+ d.TestID +'</a>打' +
                           '                    <b class="score-num">2.5</b>分' +
                           '                </span>' +
                           '            </p>' +
                           '            <cite class="right">'+ timon +'</cite>' +
                           '        </div>' +
                           '        <div class="ct-item-context">'+comment+'</div>' +
                           '    </div>' +
                           '</li>';
        }
        commentHTML += '</ul>';
        $("#commentBox").html(commentHTML);
    },
    //获取历史存档列表数据
    ajaxGetPaper:function() {
        var self = this;
        self.loading = layer.load();//等待提示
        var area = $("#area").val();
        $.post(U(self.functionName),{dateDiff:self.dated,'subjectID':self.subjectID,page:self.thisPage,area:area,m:Math.random()},function(data){

            /*if($.myCommon.backLogin(data)==false){
                return false;
            };*/
            self.subjectName = data['data'][3];
            var paperData = data['data'][0];
            var paperCount = data['data'][1];
            var perCount = data['data'][2];
            var pageCount = Math.ceil(parseInt(data['data'][1])/parseInt(data['data'][2]));

            if (pageCount == 0) { pageCount = 1; }
            if (self.thisPage <= 1) { self.thisPage = 1; }
            if (self.thisPage >= pageCount) { self.thisPage = pageCount; }
            //写入页面内容
            var listHTML = '';
            if(self.style == 'DocSave'){
                listHTML = self.myDocSaveTableList(paperData);
            }else if(self.style == 'Down'){
                listHTML = self.myDownTableList(paperData);
            }
            $('#my'+self.style+'ListC').html(listHTML);
            layer.close(self.loading);//关闭等待提示
            //展示分页
            if(paperCount>0) {//在有数据的情况下
                self.showPage(paperCount, perCount, self.thisPage, 1);
            }else{
                $('#pagelistbox').html('');
            }
        });
    },
    //历史存档列表
    myDocSaveTableList:function(paperData) {
        var docListHTML = '';
        var self = this;
        if(paperData.length){
            var length = paperData.length;
            for(var i =0; i<length; i++){
                var order = i+1;
                var unixTimestamp = new Date(paperData[i]['LoadTime']*1000);
                var saveTime = unixTimestamp.toLocaleString();
                var pwd = '',paperKey = '';
                if(paperData[i]['SavePwd']!=0){
                    pwd = '<i class="iconfont">&#xe607;</i>';
                    paperKey = '?';
                }
                docListHTML += '<tr id="order'+paperData[i]['SaveID']+'" class="docList">' +
                               '    <td align="center" class="order">'+order+'</td>' +
                               '    <td align="center">'+self.subjectName+'</td>' +
                               '    <td align="center">'+pwd+
                               '        <span class="paperName">'+paperData[i]['SaveName']+'</span>' +
                               '    </td>' +
                               '    <td align="center"><span class="timer">'+saveTime+'</span></td>' +
                               '    <td align="center">' +
                               '        <span class="handle" order="'+paperData[i]['SaveID']+'" paperKey="'+paperKey+'">' +
                               '            <a href="javascript:;" class="reload link">恢复存档</a>|<a href="javascript:;" class="del link">删除</a>' +
                               '        </span>' +
                               '    </td>' +
                               '</tr>';
            }
        }else{
            docListHTML = '<tr><td align="center" colspan="5" height="40" style="font-size: 16px">暂无数据</td></tr>';
        }
        return docListHTML;
    },
    //历史下载列表
    myDownTableList:function(paperData) {
        var downListHTML = '';
        var self = this;
        if(paperData.length){
            for (var i = 0, len = paperData.length; i < len; i++) {
                var paper = paperData[i];
                var id = paper.DownID;
                var unixTimestamp = new Date(paper.LoadTime*1000);
                var saveTime = unixTimestamp.toLocaleString();
                var ip = paper.IP;
                var order = i+1;
                downListHTML += '<tr id="order'+paperData[i]['DownID']+'" class="downList">' +
                    '<td align="center" class="order">'+order+'</td>' +
                    '<td align="center">'+self.subjectName+'</td>' +
                    '<td align="center"><span class="paperName">'+paperData[i]['DocName']+'</span></td>' +
                    '<td align="center">'+ paperData[i]['Point'] + '点</td>' +
                    '<td align="center">' +
                    '<span class="timer">'+saveTime+'</span>' +
                    '</td>' +
                    '<td align="center">' +
                    '<span class="handle" order="'+paperData[i]['DownID']+'" pid="'+paperData[i]['id']+'">' +
                    '<a href="javascript:;" class="down link">下载存档</a>|<a href="javascript:;" class="del link">删除</a></span>' +
                    '</td>' +
                    '</tr>';
            }
        }else{
            downListHTML='<tr><td align="center" colspan="6" height="40" style="font-size: 16px">暂无数据</td></tr>';
        }
        return downListHTML;
    },

    //删除存档列
    delPaper:function(paperID){
        var self = this;
        if(self.ifLock!=''){
            return false;
        }
        self.ifLock = 'User/IndexCenter/myTestDelete';
        $.post(U('User/IndexCenter/myTestDelete'),{id:paperID,style:self.style,times:Math.random()},function(data){
            self.ifLock = '';
            if(data.status){
                $('#order' + paperID).remove();//页面移除
                if(self.style == 'DocSave') {
                    self.resetOrderNum('myDocSaveListC', 'docList');//重置列表序号
                }else if(self.style == 'Down') {
                    self.resetOrderNum('myDownListC', 'downList');//重置列表序号
                }
                layer.msg('删除成功！',{icon:1});
            }else{
                layer.msg(data.data,{icon:5});
            }
            self.ajaxGetPaper();
        });
    },
    //弹框输入存档密码
    alertWritePW:function(id){
        var alertHTML = '<div class="alertMessage">'+
            '<span>'+
            '请输入存档时所设置的密码：'+
            '<span class="paperkeyicon"></span>'+
            '<input type="text" class="paperKeyInput" style="width:120px;border:1px solid #000;color:#f00;" maxlength="20"/>'+
            '<span style="display:inline-block;width:3px;"></span>'+
            '</span>'+
            '</div>';
        self.alertWindow = layer.open({
            type:1,
            title:'提示',
            area: ['380px', '150px'], //宽高
            btn: ['确定', '取消'],
            icon:3,
            content:alertHTML,
            yes: function(){
                var key = $('.paperKeyInput').val();
                if(key == ''){
                    layer.msg('请输入存档密码！',{icon:5});
                }else {
                    self.reloadPaper(id,key);//移除内容
                }
            }
        });
    },
    //恢复存档
    reloadPaper:function(paperID,paperKey){
        var self = this;
        if(self.ifLock != ''){
            return false;
        }
        self.loading = layer.load();//等待提示
        self.ifLock = 'index/isSavePwd';
        $.post(U('Home/Index/isSavePwd'),{id:paperID,key:paperKey,'times':Math.random()},function(data) {
            /*if($.myCommon.backLogin(data)==false){
             return false;
             }*/
            self.ifLock = '';
            layer.close(self.loading);//关闭等待提示
            if (typeof (data['data']) == "string") {
                if (data['data'].indexOf("@#@") >= 0) {
                    editData.clear();
                    editData.set('init',data['data']);
                    layer.close(self.alertWindow); //关闭弹框
                    layer.msg('恢复成功！',{icon:1});//提示成功
                    window.location.href = U('Home/Index/main?u=Index_Zujuan');//跳转到组卷中心
                } else {
                    layer.msg("密码错误！",{icon:5});
                }
            } else {
                layer.close(self.alertWindow); //关闭弹框
                layer.msg("恢复失败,请重试！",{icon:5});
            }
        });
    },
    //历史下载的JS事件
    myDownBindClick:function() {
        var self = this;
        $("#myDownListC").on("click",'.del', function() {
            var paperID = $(this).parent().attr("order");//下载ID
            var paperName=$('#order'+paperID).find('.paperName').html();
            var alertHTML = '<div class="alertMessage">' +
                            '    <b class="delThisPaper"  paperid="'+paperID+'">您确定删除【'+paperName+'】文档？</b>' +
                            '</div>';
            self.alertWindow = layer.open({
                type : 1,
                title:'温馨提醒',
                area : ['640px','150px'],
                content : alertHTML,
                btn : ['确定', '取消'],
                yes : function(){
                    layer.close(self.alertWindow); //如果设定了yes回调，需进行手工关闭
                    self.delPaper(paperID);//移除内容
                }
            });
        });
        //下载文档
        $("#myDownListC").on("click",'.down', function(e){
            var id = $(this).parent().attr('pid');//类似于安全码的数据
            var paperID = $(this).parent().attr("order");//下载ID
            self.downloadFile(paperID,id);
        });
        //切换试题库
        $("#area").on('change',function() {
            self.thisPage = 1;
            self.ajaxGetPaper();
        });
    },
    //下载文档
    downloadFile:function(docID,pid){
        var self = this;
        if(self.ifLock != ''){
            return false;
        }
        self.loading = layer.load();//等待提示
        self.ifLock = 'User/IndexCenter/loadSaveWord';
        //试卷存档下载
        $.post(U('Home/Index/loadSaveWord'),{'DownID':docID,'id':pid,'times':Math.random()},function(data){
            self.ifLock = '';
            layer.close(self.loading);//关闭等待提示
            if(data.status == 1){
                var paperName = $('#order'+docID).find('.paperName').html();
                var alertHTML = '<div class="alertMessage">' +
                                '    <p align="center">'+paperName+'</p>' +
                                '    <p align="center">请【<a href="'+data['data']+'" target="_blank" id="tmpdown">点击这里</a>】下载文档。</p>' +
                                '</div>'
                self.alertWindow = layer.open({
                    type : 1,
                    title : '存档下载',
                    area : ['460px','150px'],
                    content:alertHTML
                });
            }else{
                layer.msg('下载出错，请重试！',{icon:5});
            }
        });
    }
};