$.fn.Tips = function(opts, style){
    var elements = {};
    return $(this).each(function(){
        var opt = $.extend({
            'delay' : 1000 * 5,
            'container' : $('body'),
            'speed' : 5,
            'repeat' : true
        },opts);
        var interval = 0;
        var that = $(this);
        if(!opt.repeat){
            var cancel = $('<span>&nbsp;&nbsp;&nbsp;<a href="#" style="text-de">不再提示</a></span>');
            cancel.click(function(){
                /*alert('test');*/
            });
            that.append(cancel);
        }
        that.hide();
        that.css($.extend({
            'position':'absolute',
            'padding' : '5px 10px',
            'font-size' : '14px',
            'color' : 'red',
            'background-color' : '#fff',
            'border-bottom' : '1px solid #00A0E9',
            'border-left' : '1px solid #00A0E9',
            'border-right' : '1px solid #00A0E9',
            'z-index' : '9999'
        },style));
        opt.container.append(that);
        var cw = opt.container.width();
        var w = that.outerWidth();
        var h = that.outerHeight();
        if(w < cw){
            w = (cw - w) / 2;
        }else{
            w = cw;
        }
        that.offset({
            top : 0-h-10,
            left : w
        })
        var aspect = 'd';
        var pause = false;
        effect();
        that.show();
        
        function effect(){
            interval = setInterval(func, opt.speed);
        }
        
        function func(){
            var offset = that.offset();
            if(aspect == 'd' && offset.top <= 0){
                that.css({'top' : offset.top + 1});
            }else if(aspect == 'u' && offset.top >= (0 - h)){
                that.css({'top' : offset.top - 1});
            }else{
                if(aspect == 'd'){
                    aspect = 'u';
                    setTimeout(effect, opt.delay);                    
                }else{
                    that.remove();
                }
                clearInterval(interval);
            }
        }
    });
}