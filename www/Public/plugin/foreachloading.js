//ajax循环加载数据 
(function($){
    $.fn.ForeachLoading = function(opts){
        return $(this).each(function(){
            var that = $(this);
            var opt = $.extend({
                url : '',
                extraData : {}, //可能发送的额外请求参数
                paramName : '', //分段请求的请求参数 params中 xxx的名称
                params : [],    //分段请求的参数数组  xxx=111  [111,222,333]
                element : '',   //需要生成的标签内容非jquery对象
                loading : function(){},  //正在加载是调用
                success : function(data){}, //成功后调用
                verify : function(data){}   //返回数据的验证
            },opts);
            var index = 0;
            var element = null;
            request(opt.params[index]);
            function request(param){
                element = $(opt.element);
                element.html(opt.loading());
                that.append(element);
                opt.extraData[opt.paramName] = opt.params[index];
                $.post(opt.url, opt.extraData, function(data){
                    if(opt.verify(data)){
                        return false;
                    }
                    element.html(opt.success(data['data'][0]));
                    if(opt.params[++index]){
                        request(opt.params[index]);
                    }
                })  
            }
        });
    }
})(jQuery);