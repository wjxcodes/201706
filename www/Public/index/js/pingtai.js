/**
 * 协同命制JS
 */
jQuery.orginality={
    init:function(){
        var self=this;
        self.subjectMenuOpen();//协同原创试卷写作说明展开全部
        self.orginalityNameOpen();//命题人展开收起
        self.TopAd(); //顶部图片适应
        //self.flashPicShow();//图片轮播
        self.showImage(); //显示图片
        self.selectInit();
    },
    /**
     * 协同原创试卷写作说明展开全部
     */ 
    subjectMenuOpen:function(){
        $(".outline .title").click(function(){
            var that = $(this);
            var content = that.siblings('.foldContent');
            var _h = parseInt(content.css('height'));
            if(_h != 230){
                content.css({
                    'height':'230'
                });
            $(".outline .title b").html("查看全部");
            }else{
                content.css({
                    'height':'auto'
                });
                $(".outline .title b").html("收起");
            }
        });
    },

    /**
     * 命题人 展开收起
     */
    orginalityNameOpen:function(){
        $(function(){$(".author .more").toggle(function(){
                $(this).siblings(".list-show").css({'height':'auto','overflow':'inherit'});
                $(this).html("收起");
                },function(){
                $(this).siblings(".list-show").css({'height':'24px','line-height':'24px','overflow':'hidden'});
                $(this).html("更多");
            });
            /*是否显示 “更多” 按钮*/
            $(".author").each(function(){
                if($(this).find(".list-show li").length <= 2) $(this).find(".more").css("display","none");
            });
        });
    },
     // JScript 文件
    TopAd:function(){
        var strTopAd="";
        
        //定义小图片内容
        var topSmallBanner='<div><a href="http://gk.Manage.com/" target=_blank style="width:100%; height:79px; background:url(Public/index/images/top_090901_s.png) center top no-repeat; display:block"></a></div>';
        
        //判断在那些页面上显示大图变小图效果，非这些地址只显示小图（或FLASH）
        if (location == "http://gk.Manage.com" || location == "http://gk.Manage.com" || location == "http://gk.Manage.com/" || true)
        {
            //定义大图内容
            strTopAd='<div id=adimage style="width:100%;margin: 0 auto;">'+
                        '<div id=adBig><a href="http://gk.Manage.com/" ' + 
                        'target=_blank style="width:100%; height:391px; background:url(Public/index/images/top_zzsc_b.jpg) center top; display:block">'+
                        '</A></div>'+
                        '<div id=adSmall style="display: none">';
            //strTopAd+=  topFlash;     
            strTopAd+=  topSmallBanner;  
            strTopAd+=  "</div></div>";
        }
        else
        {
            //strTopAd+=topFlash;
            strTopAd+=  topSmallBanner;  
        }
        strTopAd+='<div style="height:7px; clear:both;overflow:hidden;"></div>';
        return strTopAd;
    },
    flashPicShow:function(){
        document.write($.orginality.TopAd());
        $(function(){
        // 过两秒显示 showImage(); 内容
        setTimeout("showImage();",10000);
        // alert(location);
        });
    },
    showImage:function(){
        $("#adBig").slideUp(1000,function(){$("#adSmall").slideDown(1000);});
    },
    createSelect:function(select_container,index){

        //创建select容器，class为select_box，插入到select标签前
        var tag_select=$('<div></div>');//div相当于select标签
        tag_select.attr('class','select_box');
        tag_select.insertBefore(select_container);

        //显示框class为select_showbox,插入到创建的tag_select中
        var select_showbox=$('<div></div>');//显示框
        select_showbox.css('cursor','pointer').attr('class','select_showbox').appendTo(tag_select);

        //创建option容器，class为select_option，插入到创建的tag_select中
        var ul_option=$('<ul></ul>');//创建option列表
        ul_option.attr('class','select_option');
        ul_option.appendTo(tag_select);
        $.orginality.createOptions(index,ul_option);//创建option

        //点击显示框
        tag_select.toggle(function(){
            ul_option.show();
        },function(){
            ul_option.hide();
        });

        var li_option=ul_option.find('li');
        li_option.on('click',function(){
            $(this).addClass('selected').siblings().removeClass('selected');
            var valueT=$(this).text();//学科名
            var value=$(this).val();//学科ID
            select_showbox.text(valueT);
            $(this).parents(".select_box").siblings("select").val(value).trigger("change");//给select赋值  并激活 change 事件
            
            ul_option.hide();
        });

        li_option.hover(function(){
            $(this).addClass('hover').siblings().removeClass('hover');    
        },function(){
            li_option.removeClass('hover');
        });

    },
    createOptions:function(index,ul_list){
        //获取被选中的元素并将其值赋值到显示框中
        var options=selects.eq(index).find('option'),
            selected_option=options.filter(':selected'),
            selected_index=selected_option.index(),
            showbox=ul_list.prev();
            showbox.text(selected_option.text());
        
        //为每个option建立个li并赋值
        for(var n=0;n<options.length;n++){
            var tag_option=$('<li></li>'),//li相当于option
                txt_optiont=options.eq(n).text();
                txt_optionv=options.eq(n).val();
                tag_option.text(txt_optiont).val(txt_optionv).css('cursor','pointer').appendTo(ul_list);

            //为被选中的元素添加class为selected
            if(n==selected_index){
                tag_option.attr('class','selected');
            }
        }
    },
    selectInit:function(){
        var selects=$('select');//获取select

        for(var i=0;i<selects.length;i++){
            createSelect(selects[i],i);
        }

        function createSelect(select_container,index){

            //创建select容器，class为select_box，插入到select标签前
            var tag_select=$('<div></div>');//div相当于select标签
            tag_select.attr('class','select_box');
            tag_select.insertBefore(select_container);

            //显示框class为select_showbox,插入到创建的tag_select中
            var select_showbox=$('<div></div>');//显示框
            select_showbox.css('cursor','pointer').attr('class','select_showbox').appendTo(tag_select);

            //创建option容器，class为select_option，插入到创建的tag_select中
            var ul_option=$('<ul></ul>');//创建option列表
            ul_option.attr('class','select_option');
            ul_option.appendTo(tag_select);
            createOptions(index,ul_option);//创建option

            //点击显示框
            tag_select.toggle(function(){
                ul_option.show();
            },function(){
                ul_option.hide();
            });
            
            var li_option=ul_option.find('li');
            li_option.on('click',function(){
                $(this).addClass('selected').siblings().removeClass('selected');
                var valueT=$(this).text();//学科名
                var value=$(this).val();//学科ID
                select_showbox.text(valueT);
                $(this).parents(".select_box").siblings("select").val(value).trigger("change");//给select赋值  并激活 change 事件

                ul_option.hide();
            });

            li_option.hover(function(){
                $(this).addClass('hover').siblings().removeClass('hover');
            },function(){
                li_option.removeClass('hover');
            });

        }

        function createOptions(index,ul_list){
            //获取被选中的元素并将其值赋值到显示框中
            var options=selects.eq(index).find('option'),
                selected_option=options.filter(':selected'),
                selected_index=selected_option.index(),
                showbox=ul_list.prev();
            showbox.text(selected_option.text());

            //为每个option建立个li并赋值
            for(var n=0;n<options.length;n++){
                var tag_option=$('<li></li>'),//li相当于option
                    txt_optiont=options.eq(n).text();
                txt_optionv=options.eq(n).val();
                tag_option.text(txt_optiont).val(txt_optionv).css('cursor','pointer').appendTo(ul_list);

                //为被选中的元素添加class为selected
                if(n==selected_index){
                    tag_option.attr('class','selected');
                }
            }
        }
    },
    //首页数据初始化
    indexInit:function(){
        $.orginality.pngflashOpen();
        //主banner-ie8以下效果区别
        $(function () {
            var isIE = $.browser.msie && ($.browser.version == "8.0" || $.browser.version == "7.0" || $.browser.version == "6.0"),
                isIE6 = $.browser.msie && $.browser.version == "6.0";
            if (isIE) {
                jQuery(".wln-slider").slide({
                    titCell: ".hd span",
                    mainCell: ".bd ul",
                    effect: "fade",
                    autoPlay: true,
                    interTime: 5000,
                    delayTime: 500,
                    trigger: "click"
                });
            } else {
                jQuery(".wln-slider").slide({
                    titCell: ".hd span",
                    mainCell: ".bd ul",
                    effect: "fold",
                    autoPlay: true,
                    interTime: 5000,
                    delayTime: 1500,
                    trigger: "click"
                });
            }
            //题库平台介绍轮换
            jQuery(".fullSlide").slide({
                titCell: ".wi-tab-box span",
                mainCell: ".bd ul",
                effect: "fade",
                autoPlay: true,
                interTime: 4000,
                delayTime: 500,
                trigger: "mouseover"
            });
            //平台统计鼠标移入显示申请使用按钮
            (function () {
                var e = $(".info-five-area");
                e.hover(function () {
                    $(this).addClass("hover");
                },function(){
                    $(this).removeClass("hover");
                });
            }());
            //用户资源列表间隔背景色
            $(function () {
                $(".us-list-container li:nth-child(2n+2)").css({"background-color": "#f9f9f9"})
            });
            // footer题库服务导航斜线
            $(function () {
                $(".ws-fours-area:first-child").css({"background-image": "none", "padding-left": "0"})
            });

            $('.udListContainer a').each(function(){
                $(this).click(function(){
                    var that = $(this);
                    that.addClass('on').siblings('a').removeClass('on');
                    var content = $('.udContent');
                    content.find('.udType').html('加载中...');
                    content.find('.udTime').html('');
                    content.find('.udUser').html('');
                    content.find('a').html('');
                    $.orginality.updataContainer(that.attr('sid'), content);
                    return false;
                });
            });

            $('.duListContainer a').each(function(){
                $(this).click(function(){
                    var that = $(this);
                    that.addClass('on').siblings('a').removeClass('on');
                    var content = $('.duContent');
                    content.find('.udType').html('加载中...');
                    content.find('.udTime').html('');
                    content.find('a').html('');
                    $.orginality.getUpgrade(that.attr('sid'), content);
                    return false;
                });
            });

            $(document).ready(function () {
                setInterval(ajaxTotal, 5000);
            });
            function ajaxTotal() {
                var flag = 'testNum,zujuanNum,homeWorkNum,caseDownNum,studentAnswerNum';
                $.indexShowTotal.updateTotalMsg(flag)
            }
        });
    },
    //获取最近更新
    getUpgrade:function(s, container){
        var data = {
                perpage : '9',
                sid : s
        };
        $.post(U('Doc/Doc/getDocList') ,data, function(data){
            data = data['data'][0];
            var index = 0;
            for(var record in data){
                var content = container.find('li').eq(index++);
                record = data[record];
                var type = record['typename'] || '[未分类]';
                var title = record['docname'] || '';
                var addTime = record['introtime'] || '';
                if(!title && !addTime){
                    type = '无更多数据';
                }
                content.find('.udType').html(type);
                content.find('.udTime').html(addTime);
                var link = content.find('.udTitle a');
                var url = '/Doc/'+record['docid']+'.html';
                if(link.length == 0){
                    link = $('<a href="'+url+'" target="_blank">'+title+'</a>');
                    content.find('.udType').after(link);
                }else{
                    link.attr('href', url).html(title);
                }
            }
        });
    },
    
    //获取用户资源
    updataContainer:function(s, container) {
        var data = {
            perpage : '6',
            sid : s
        };
        $.post(U('Doc/Doc/ajaxGetUserResource'),data, function(data){
            data = data['data'];
            var types = data['type'];
            var index = 0;
            for(var record in data['rs']){
                var content = container.find('li').eq(index++);
                record = data['rs'][record];
                var type = record['Classification'];
                if(type){
                    type = '['+types[type]+']';
                }else{
                    type = '[未分类]';
                }
                var title = record['Title'] || '';
                var username = record['UserName'] || '';
                if(!title && !username){
                    type = '无更多数据';
                }
                var addTime = record['AddTime'] || '';
                content.find('.udType').html(type);
                content.find('.udTime').html(addTime);
                content.find('.udUser').html(username);
                var link = content.find('.udTitle a');
                var url = '/Doc/userContent/'+record['AssociateID']+'.html';
                if(link.length == 0){
                    link = $('<a href="'+url+'" target="_blank">'+title+'</a>');
                    content.find('.udType').after(link);
                }else{
                    link.attr('href', url).html(title);
                }
            }
        });
    },
    pngflashOpen:function(){

        /*专题限时弹出*/

            // 活动1-顶部固定条
            var start1 = '2016-04-14 12:00';
            var end1 = '9999-01-01';
            new BannerTimer(start1, end1).task(topBannerShow);

            // 活动2-弹出框
            var start2 = '2016-06-07';
            var end2 = '2016-06-10';
            new BannerTimer(start2, end2).task(bannerShow);

        /*
         startDate:开始时间。 endDate，截止时间，为空时，默认不过期
         格式：2005-05-07 00:00:00
         */
        function BannerTimer(startDate,endDate){
            endDate = endDate || false;
            //callback 回调函数
            this.task = function(callback){
                var current = Math.floor(new Date().getTime() / 1000);
                startDate = getTimestamp(startDate);
                if(!endDate){
                    current + 10; //截止时间为空时，则不过期
                }else{
                    endDate = getTimestamp(endDate);
                }
                if(startDate <= current && current < endDate){
                    callback();
                }
            }

            /*
             返回一个毫秒时间戳
             */
            function getTimestamp(date){
                date = date.split(/\s+/g);
                //仅提供日期
                var arr = [];
                if(1 == date.length){
                    arr = date[0].split('-');
                }else{
                    arr = arr.concat(date[0].split('-'),date[1].split(':'));
                }
                var dateObj = getDate(arr);
                return Math.floor(dateObj.getTime() / 1000);
            }

            function getDate(arr){
                for(var i=0; i<arr.length; i++){
                    if(!arr[i]){
                        arr[i] = 0;
                    }
                    arr[i] = arr[i].toString().replace(/^0/, '');
                }
                var instance = new Date();
                instance.setFullYear(arr[0]);
                instance.setMonth(parseInt(arr[1])-1);
                instance.setDate(arr[2]);
                instance.setHours(arr[3] || 0);
                instance.setMinutes(arr[4] || 0);
                instance.setSeconds(arr[5] || 0);
                return instance;
            }
        }

        // 活动1-顶部固定条
function topBannerShow(){
    $(".top-active-wrap").show();
    $(".hide-top-active").on("click",function(){
        $(this).parents(".top-active-wrap").hide();
    });
}
// 活动2-首页弹出框
        function bannerShow(){
            setTimeout(function(){
                var idxBanner = layer.open({
                    type: 1, //page层
                    area: ['520px', '320px'],//弹出框宽高
                    title: false,
                    closeBtn: 2,
                    shade: 0.6, //遮罩透明度
                    shadeClose: true,//点击遮罩层关闭
                    moveType: 1, //拖拽风格，0是默认，1是传统拖动
                    shift: 0, //0-6的动画形式，-1不开启
                    content: '<a id="imgBanner" target="_blank" href="'+U('Index/Special/zhenti2016')+'">' +
                    '<img src="Public/index/imgs/special/zhenti2016/sub.png" width="520" height="320" alt="2016高考真题征集活动" /></a>'
                });
                layer.style(idxBanner, {
                    backgroundColor: 'transparent',
                    'border-radius':'16px',
                    // 'overflow':'hidden',
                    'box-shadow':'none'
                });
            },1500);
        }
    }
}