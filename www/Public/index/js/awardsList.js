    jQuery.awardsList = {
        init: function() {
            var e = this;
            this.modalTab();
            this.backTop();
        },
        // 通用切换
        modalTab: function() {
            $('.g-tab .tab-nav li').each(function() {
                var e = $(this);
                var trigger = e.closest('.g-tab-panel').attr("data-toggle");
                if (trigger == "hover") {
                    e.mouseover(function() {
                        $showtabs(e);
                    });
                    e.click(function() {
                        return false;
                    });
                } else {
                    e.click(function() {
                        $showtabs(e);
                        return false;
                    });
                }
            });
            $showtabs = function(e) {
                var detail = e.attr("data-href");
                e.closest('.g-tab .tab-nav').find("li").removeClass("on");
                e.closest('.g-tab').find(".tab-body .tab-panel").removeClass("on");
                e.addClass("on");
                $(detail).addClass("on");
            };

        },

        // 返回顶部
        backTop: function() {
            $("#goTop").click(function() {
                // $(document.body||document.documentElement).scrollTop=0;
                document.documentElement.scrollTop = 0;
                document.body.scrollTop = 0;
            });
            window.onscroll = function() {
                var x = document.body.scrollTop || document.documentElement.scrollTop;
                if (x < 1) $("#goTop").css("display", "none");
                else $("#goTop").css("display", "block");
            };
        }
    };