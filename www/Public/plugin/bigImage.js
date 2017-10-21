(function(){
    /**
     * ͼƬ�Ŵ󣬶���Ҫ�Ŵ��image������¼���
     * ʹ��ʾ����
     * $('#test').on('click','.st_box img',function(){
     *   $(this).bigImage();
     *});
     * css��Ҫ�ģ�
     *.msg-img-wrap{display:block;position:relative;overflow:visible}
     *.msg-img-wrap .hide-msg-img{ display:block;text-align:center;width:40px;height:40px;line-height:40px;position:absolute;
     *   right:-40px;top:0;color:#bbb;font-size:30px;cursor:pointer;background: #000000;filter:Alpha(opacity=50);opacity: 0.5;}
     *.msg-img-wrap .hide-msg-img:hover{color:#ccc;text-decoration:none}
     * @param options �������
     * @author �ܱ�
     */
    $.fn.bigImage = function(options){
        var config = $.extend({}, $.fn.bigImage.defaults, options);
        var self = $(this);
        //��ȡͼƬ����ͼƬ��׺
        var srcSmall = self.attr('src');
        if(!srcSmall){//�Ҳ���src���ԣ�����
            return false;
        }
        //С��ָ����С������
        var smallHeight = self.height();
        var smallWidth = self.width();
        var smallScale = (smallHeight/smallWidth).toFixed(2);
        if(smallHeight<config.height||smallWidth<config.width){
            return false;
        }
        var suffixStart = srcSmall.lastIndexOf('.');//��׺λ��
        var nameStart = srcSmall.lastIndexOf('/');//�ļ�����ʼλ��
        var suffix = srcSmall.substring(suffixStart+1).toLowerCase();//��׺
        var name = srcSmall.substring(nameStart+1,suffixStart);//�ļ�������������׺
        var prefix = srcSmall.substring(0,nameStart);//ǰ׺
        if(name.indexOf('image')==-1||config.suffix.indexOf(suffix)==-1){
            //ͼƬ��������image�����ߺ�׺������Ҫ��ֹͣ
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
        //��ȡԭ�����ܴ�ͼ�����м����С
        var bigImage = new Image();
        //modal��������
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
            //���ͼƬ�ȴ��ڻ���
            if(bigHeightOriginal>wHeight){
                bigHeight = wHeight*0.8;
                bigWidth = (bigHeight*bigWidthOriginal)/bigHeightOriginal;
            }
            if(bigWidthOriginal>wWidth){
                bigWidth = wWidth*0.8;
                bigHeight = (bigWidth*bigHeightOriginal)/bigWidthOriginal;
            }
            var left = (wWidth - bigWidth) / 2;
            var top = ((wHeight - bigHeight) / 2);//����20px
            top = top<0?0:top;
            var imageDiv = '<div id="'+bigImageID+'Image" style="z-index:101;position:fixed;left:'+left+'px;top:'+top+'px;' +
                'width:'+bigWidth+'px;height:'+bigHeight+'px;"><span class="msg-img-wrap"><a class="hide-msg-img iconfont">&#xe602;</a>' +
                '<img src="'+bigSrc+'" style="height:'+bigHeight+'px;width:'+bigWidth+'px;"></span></div>';
            $('body').append(modalDiv+imageDiv);
        };
        //�󶨹ر��¼�
        $('body').on('click','#'+bigImageID+'Modal,.hide-msg-img',function(){
            getBigImage(false);
        });
        //��β����
        bigImage.onload = function() {
            var bigHeight = this.height,bigWidth = this.width,bigScale = (bigHeight/bigWidth).toFixed(2);
            if(bigHeight>smallHeight&&bigWidth>smallWidth&&Math.abs(bigScale-smallScale)<config.scale){
                getBigImage(true,bigHeight,bigWidth);
            }
        };
        bigImage.src = bigSrc;//����д��onload���棬�����л��������»�ʧЧ
    };
    /**
     * ͼƬ�Ŵ���Ĭ�ϲ���
     * width height ����Ŵ��ͼƬ����С���px
     * suffix����Ŵ��ͼƬ�ĺ�׺
     * scale��ͼ��Сͼ��߱ȵķ�ֵ�����Ը���ѧ�Ƶ���,���鲻����0.1
     * @type {{width: number, height: number, suffix: string, scale:float}}
     * @author �ܱ�
     */
    $.fn.bigImage.defaults = {
        width:80,
        height:80,
        suffix:'png|jpg|jpeg|gif',
        scale:0.02
    };
})();
