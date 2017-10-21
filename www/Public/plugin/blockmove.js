/**
 *  拖动条插件
 */
;(function($){
    $.BlockMove={
        init:function () {
            var w = $('.hkbg').width();
            var maxFloat = w - 5;
            var minFloat = w / 100 - 5;
            var num;
            var x;
            //##初始位置
            $('.hkblock').css({'left': (w / 2 - 5) + 'px'});
            //数值框
            $('.hkbox').css({'left': (w / 2 - 31) + 'px'});
            //线条长度
            $('.hkline').width($('.hkbg').width() / 2);
            //##
            //点击到指定位置
            $('.hkbar').live('click',function (e) {
                var tid=0;
                //横向偏移距离
                x = e.pageX - $(this).offset().left;
                //计算数值
                num = x / w;
                if(num<0.01) num=0.01;
                if($(this).prev().html().indexOf('%')!=-1){
                    tid=1;
                }
                $.BlockMove.triggerMove(num,tid,$(this).prev().prev(),true);
            });
            //左移动
            $('.scrollleft').live('click',function () {
                var tid = $(this).parents('.hk_sz').next().next().attr('tid');
                if (tid == 1) {//考点覆盖
                    num = parseInt($('#klNum').html().replace('%', '')) / 100 - 0.01;
                } else {
                    num = parseFloat($('#diffNum').html()) - 0.01;
                }
                if (num <= 0) num = 0.01;
                $.BlockMove.triggerMove(num, tid,$(this).parents('.hk_sz').next().next(),false);

            });
            //右移动
            $('.scrollright').live('click',function () {
                var tid = $(this).parents('.hk_sz').next().next().attr('tid');
                if (tid == 1) {//考点覆盖
                    num = parseInt($('#klNum').html().replace('%', '')) / 100 + 0.01;
                } else {
                    num = parseFloat($('#diffNum').html()) + 0.01;
                }
                if (num >= 1) num = 1;
                $.BlockMove.triggerMove(num, tid,$(this).parents('.hk_sz').next().next(),false);
            });
            $('.hkblock').each(function(){
                var _this=$(this);
                $(this).bind("mousedown",function(e){
                    var initX= e.pageX;
                    var hx=parseInt($(this).css('left'));
                    $(document).bind("mousemove",function(e){
                        x = e.pageX-initX+hx<0?0:(e.pageX-initX+hx>=(maxFloat+5)?maxFloat+5:e.pageX-initX+hx);
                        if(x < minFloat) x = minFloat;
                        num = x / w;
                        if(num<=0.01) num=0.01;
                        else if(num>=1) num=1.00;
                        if(_this.next().html().indexOf('%')!=-1)  $.BlockMove.triggerMove(num,1,_this,false);
                        else $.BlockMove.triggerMove(num,0,_this,false);
                    }).bind("mouseup",function(){
                        $(this).unbind("mousemove");
                        $(this).unbind("mouseout");
                        $(this).unbind("mouseup");
                    });
                });
            });
        },
        //移动函数
        triggerMove:function(num, type,$this,ifanimate){
            num=Number(num);

            //计算x宽度
            var x=$('.hkbar').width();
            x=(num*x).toFixed(0);

            var w = $('.hkbg').width();
            var left = w * num - 5;
            var minFloat = w / 100 - 5;
            if(left<minFloat) left=minFloat;
            if (type == 1) {
                num = (num * 100).toFixed(0) + '%';
            }else{
                num = num.toFixed(2);
            }
            if(ifanimate){
                $this.animate({'left': left + 'px'});
                //数值框
                $this.next().animate({'left': (left + 5 - 31) + 'px'}).html(num);
                //线条长度
                $this.prev().animate({'width': x + 'px'});
            }else {
                $this.css({'left': left + 'px'});
                //数值框
                $this.next().css({'left': (left + 5 - 31) + 'px'}).html(num);
                //线条长度
                $this.prev().width(left + 5);
            }
        }
    }
})(jQuery);
