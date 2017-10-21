$.TestPreview = {
    url : '',
    us : {},
    init : function(url, reloadParams){
        var that = this;
        that.url = url;
        that.us = that.getSearcher();
        that.us.params = reloadParams;
        that.bindEvent();
        //查找触发相关事件的元素。当同时可能需要操作的多个元素，该元素总是对应最后一个
        var lastElement = false; 
        //根据可能已经存在的有效参数进行相关的处理
        for(var param in that.us.params){
            var value = that.us.params[param];
            if(value != '0'){
                for(var i=0; i<that.us.elements.length; i++){
                    var option = that.us.elements[i][1];
                    if(option.id == param){
                        $(that.us.elements[i][0]).each(function(){
                            if($(this).attr(option.param) == value){
                                option.beforeHandler.call($(this));
                                lastElement = $(this);
                            }
                        });
                    }
                }
            }
        }
        if(lastElement){
            lastElement.trigger('click');
        }else{
            $('.nr_box .subject').eq(0).trigger('click');
        }
    },

    //将相关元素绑定事件
    bindEvent : function(){
        var that = this;
        that.us.addElement('.nr_box .subject', 'click', {
            id : 'sid', 
            param : 'params',
            beforeHandler : function(){
                $.TestPreview.turn($(this), '.subject');
                return true;
            }
        });

        that.us.addElement('.nr_box .type', 'click', {
            id : 'tid', 
            param : 'params',
            beforeHandler : function(){
                $.TestPreview.turn($(this), '.type');
                return true;
            }
        });

        that.us.addElement('.nr_box .area', 'click', {
            id : 'area', 
            param : 'params',
            beforeHandler : function(){
                $.TestPreview.turn($(this), '.area');
                return true;
            }
        });

        that.us.addElement('.nr_box .grade', 'click', {
            id : 'grade', 
            param : 'params',
            beforeHandler : function(){
                $.TestPreview.turn($(this), '.grade');
                return true;
            }
        });

        that.us.addElement('.nr_box .year', 'click', {
            id : 'year', 
            param : 'params',
            beforeHandler : function(){
                $.TestPreview.turn($(this), '.year');
                return true;
            }
        })

        that.us.addElement('.loadmore', 'click', {
            id : 'page',
            param : 'params',
            beforeHandler : function(){
                var loadmore = $(this);
                var page = parseInt(loadmore.attr('params'));
                if(page == $('#page').val()){
                    return false;
                }
                that.us.params.page = ++page;
                loadmore.attr('params', page);
                return true;
            }
        });
    },
    //显示数据列表
    showDocList : function(datas){
        var that = this;
        var data = datas['data'][0];
        var content = $('#content');
        if(!data){
            content.html(that.getReport('暂无试题'));
            $('.loadmore').hide();
            return false;
        }else{
            $('.loadmore').show();
        }
        $('.loading').remove();
        var html = '';
        for(var i=0; i<data.length; i++){
            if(i % 2){
                html += '<tr class="bg_lightblue">';
            }else{
                html += '<tr>';
            }
            html += '<td align="center">'+data[i].docyear+'</td>';
            html += '<td align="center">'+data[i].typename+'</td>';
            if(!data[i]['areaname']){
                data[i]['areaname'] = '通用';
            }
            html += '<td align="center">'+data[i].areaname+'</td>';
            html += '<td class="sjtit">';
            html += '<a href="'+that.url+'/content/id/'+data[i].docid+'" title="'+data[i].docname+'" target="_blank">'+data[i].docname+'</a>';
            html += '</td>';
            var time = data[i].introtime;
            html += '<td align="center">'+time+'</td>';
            html += '</tr>';
        }
        var page = $('#page');
        if(data.length > 0){
            var total = parseInt(datas['data'][1]); //记录总数
            if(isNaN(total)){
                total = 1;
            }
            var num = parseInt(datas['data'][2]); //分页数
            if(isNaN(num)){
                num = 1;
            }
            var countPage = Math.ceil(total / num);
            var loadmore = $('.loadmore');
            if(countPage > page.val()){
                page.val(countPage);
            }
            if(countPage > loadmore.attr('params')){
                loadmore.find('a').html('加载更多···');
            }else{
                loadmore.find('a').html('试题已全部加载完毕');
            }
        }else{
            page.val(1);
        }
        content.append(html);
    },

    //返回一个查询对象
    getSearcher : function(){
        var that = this;
        return $.extend(UnionSearch, {
            url : that.url+'/getDocList',
            requestBeforeHandler : function(id){
                this.params['times'] = Math.random();
                return true;
            },
            callback : function(datas){
                that.showDocList(datas);
            }
        });
    },

    //在相关元素触发相关事件时进行的操作
    turn : function(curret,slibling){
        var content = $('#content');
        content.html(this.getReport('加载中...'));
        $('.loadmore').attr('params', 1).hide();
        $('#page').val(1);
        this.us.params.page = 1;
        curret.addClass('this').siblings(slibling).removeClass('this');
    },

    getReport : function(msg){
        return '<tr class="loading"><td align="center" colspan="4">'+msg+'</td></tr>';
    }
}