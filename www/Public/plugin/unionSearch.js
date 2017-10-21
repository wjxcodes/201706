//添加多个元素绑定相关事件进行查询。
var UnionSearch = {
    url : '',
    elements : [], //绑定的元素集合
    params : {},  //参数值集合
    //全局操作判断。id:当前正在操作params中的哪一个值 需返回boolean值 
    requestBeforeHandler : function(id){ return true;}, 
    //返回数据后的处理
    callback : function(data){}, 
    //存入相关元素信息
    addElement : function(selector, eventName, opts){
        var us = this;
        us.elements.push([selector, opts]); //将相关绑定的元素加入集合，参数1为请求参数，2为选项
        $(selector).each(function(){
            $(this).live(eventName, function(){
                var options = $.extend({
                    id : '',
                    param : '', 
                    //单个触发事件进行的操作 需返回boolean值
                    beforeHandler : function(){ return true; } 
                }, opts);
                var that = $(this);
                us.params[options.id] = us.getValue(that, options.param);
                if(us.requestBeforeHandler(options.id) && options.beforeHandler.call(that))
                    us.request();
            });
        });
    },

    request : function(){
        $.post(this.url, this.params, this.callback);
    },

    getValue : function(obj, attr){
        if(attr){
            return obj.attr(attr);
        }
        var tagName = obj[0].tagName.toLowerCase();
        if(tagName == 'select' || tagName == 'input' || tagName == 'textarea')
            return obj.val();
        return '';
    }
}