/**
 * Created by 邵敬超 on 2016/3/8.
 */
    $.WLN_docs={
        init:function(){
            var e = this;
            e.sideNav();
            e.maoNav();
            e.scrollListen();
        },
    //结构目录
    sideNav:function(){
        var sideMenuTar = $("#sideMenu").find(".J_ExpTrigger");
        sideMenuTar.on("click",function(){
            var e = $(this);
            var isOpen = e.hasClass("open");
            if(isOpen){
                e.removeClass("open");
                e.next("ul").slideUp();
            }else{
                e.addClass("open");
                e.next("ul").slideDown();
            }
        })
    },
    //锚点导航
    maoNav:function(){
        var isListenLeft = $("#sideMenu"),
            isListenRight = $(".bs-docs-sidebar"),
            leftTop = isListenLeft.offset().top,
            rightTop = isListenRight.offset().top,
            w = $(window).width(),
			h = $(window).height(),
			nowHeight = isListenRight.height();
					if(nowHeight>h){
						isListenRight.height(h-50-rightTop);
						isListenRight.css({'overflow-y':'auto','overflow-x':'visible'});
					}
			
				
        var listener = function(){
            $(window).on("scroll",function(){
                var scrolltop = $(document).scrollTop();
                if(scrolltop>=leftTop){
                    isListenLeft.removeClass("affix-top").addClass("affix");
					if(nowHeight>h){
						isListenRight.height(h-50);
						isListenRight.css({'overflow-y':'auto','overflow-x':'visible'});
					}
                }else{
                    isListenLeft.removeClass("affix").addClass("affix-top");
					if(nowHeight>h){
						isListenRight.height(h-50-rightTop);
						isListenRight.css({'overflow-y':'auto','overflow-x':'visible'});
					}
                }
                if(scrolltop>=rightTop){
                    isListenRight.removeClass("affix-top").addClass("affix");
                }else{
                    isListenRight.removeClass("affix").addClass("affix-top");
                }
            })
        };
        if(w >= 992){
            listener();
        }
    },
    scrollListen:function(){
        // var 获取楼层距离顶部的距离
        $(window).scroll(function(){
            var loftBox = $("#sectionContent");
            var item = loftBox.find(".section-item");
            var menu = $("#maoNav");
            var TOP = $(document).scrollTop();
            var loftId = "";
            item.each(function(){
                var e = $(this);
                if( TOP > e.offset().top - 140){
                    loftId = "#" + e.attr("id");
                }else{
                    return false;
                }
            });
            // 设置当前楼层样式
            var currentLoft = menu.find(".active").closest("li");
            var navLoft = menu.find("a");
            if(loftId && currentLoft.find("a").attr("href") != loftId){
                currentLoft.removeClass("active");
                menu.find("[href="+loftId+"]").closest("li").addClass("active");
            }
            navLoft.on("click",function(){
                var e = $(this);
                navLoft.removeClass("active");
                e.closest("li").addClass("active");
            })
        })
    }
    };
