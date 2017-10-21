(function(){
    /**
     * 图片放大，对需要放大的image做点击事件绑定
     * 使用示例：
     * $('#test').on('click','.st_box img',function(){
     *   $(this).bigImage();
     *});
     * css需要的：
     *.msg-img-wrap{display:block;position:relative;overflow:visible}
     *.msg-img-wrap .hide-msg-img{ display:block;text-align:center;width:40px;height:40px;line-height:40px;position:absolute;
     *   right:-40px;top:0;color:#bbb;font-size:30px;cursor:pointer;background: #000000;filter:Alpha(opacity=50);opacity: 0.5;}
     *.msg-img-wrap .hide-msg-img:hover{color:#ccc;text-decoration:none}
     * @param options 插件参数
     * @author 周斌
     */
    $.fn.bigImage = function(options){
        var config = $.extend({}, $.fn.bigImage.defaults, options);
        var self = $(this);
        //提取图片名和图片后缀
        var srcSmall = self.attr('src');
        if(!srcSmall){//找不到src属性，出错
            return false;
        }
        //小于指定大小不操作
        var smallHeight = self.height();
        var smallWidth = self.width();
        var smallScale = (smallHeight/smallWidth).toFixed(2);
        if(smallHeight<config.height||smallWidth<config.width){
            return false;
        }
        var suffixStart = srcSmall.lastIndexOf('.');//后缀位置
        var nameStart = srcSmall.lastIndexOf('/');//文件名开始位置
        var suffix = srcSmall.substring(suffixStart+1).toLowerCase();//后缀
        var name = srcSmall.substring(nameStart+1,suffixStart);//文件名，不包含后缀
        var prefix = srcSmall.substring(0,nameStart);//前缀
        if(name.indexOf('image')==-1||config.suffix.indexOf(suffix)==-1){
            //图片名不包含image，或者后缀不符合要求，停止
            return false;
        }
        var bigNameNum = Number(name.substring(5))-1;
        var bigName = '';
        if(bigNameNum<10){
            bigName = '00'+bigNameNum;
        }else if(bigNameNum<100){
            bigName = '0'+bigNameNum;
        }else{
            bigName = bigNameNum;
        }
        bigName = 'image'+String(bigName);
        var bigSrc = prefix+'/'+bigName+'.'+suffix;
        //获取原生可能大图，进行计算大小
        var bigImage = new Image();
        //modal操作方法
        var bigImageID = 'bigImageID';
        var getBigImage = function (show,bigHeightOriginal,bigWidthOriginal) {
            if(show === false){
                $('#'+bigImageID+'Modal').remove();
                $('#'+bigImageID+'Image').remove();
                return false;
            }
            var modalDiv = '<div id="'+bigImageID+'Modal" style="position: fixed;top:0;left: 0;background: #000000;' +
                'filter:Alpha(opacity=50);opacity: 0.5;width: 100%;height: 100%;z-index: 98;"></div> ';
            var wWidth = $(window).width();
            var wHeight = $(window).height();
            var bigHeight = bigHeightOriginal;
            var bigWidth = bigWidthOriginal;
            //如果图片比窗口还大
            if(bigHeightOriginal>wHeight){
                bigHeight = wHeight*0.8;
                bigWidth = (bigHeight*bigWidthOriginal)/bigHeightOriginal;
            }
            if(bigWidthOriginal>wWidth){
                bigWidth = wWidth*0.8;
                bigHeight = (bigWidth*bigHeightOriginal)/bigWidthOriginal;
            }
            var left = (wWidth - bigWidth) / 2;
            var top = ((wHeight - bigHeight) / 2);//靠上20px
            top = top<0?0:top;
            var imageDiv = '<div id="'+bigImageID+'Image" style="z-index:101;position:fixed;left:'+left+'px;top:'+top+'px;' +
                'width:'+bigWidth+'px;height:'+bigHeight+'px;"><span class="msg-img-wrap"><a class="hide-msg-img iconfont">&#xe602;</a>' +
                '<img src="'+bigSrc+'" style="height:'+bigHeight+'px;width:'+bigWidth+'px;"></span></div>';
            $('body').append(modalDiv+imageDiv);
        };
        //绑定关闭事件
        $('body').on('click','#'+bigImageID+'Modal,.hide-msg-img',function(){
            getBigImage(false);
        });
        //结尾部分
        bigImage.onload = function() {
            var bigHeight = this.height,bigWidth = this.width,bigScale = (bigHeight/bigWidth).toFixed(2);
            if(bigHeight>smallHeight&&bigWidth>smallWidth&&Math.abs(bigScale-smallScale)<config.scale){
                getBigImage(true,bigHeight,bigWidth);
            }
        };
        bigImage.src = bigSrc;//必须写在onload后面，否则有缓存的情况下会失效
    };
    /**
     * 图片放大插件默认参数
     * width height 允许放大的图片的最小宽高px
     * suffix允许放大的图片的后缀
     * scale大图和小图宽高比的阀值，可以根据学科调整,建议不超过0.1
     * @type {{width: number, height: number, suffix: string, scale:float}}
     * @author 周斌
     */
    $.fn.bigImage.defaults = {
        width:80,
        height:80,
        suffix:'png|jpg|jpeg|gif',
        scale:0.02
    };
})();
