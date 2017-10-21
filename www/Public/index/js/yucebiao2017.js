$(function () {
    var yucebiaoJs = {
        init: function () {
            var _this = this;
            _this.photoSwipe();
            _this.goTopHtml();
            _this.template();
            _this.checkDevice();

            var $pageName = $("body[data-page]").data("page");
            if ($pageName) {
                _this.checkNavCurrent($pageName);
            }
        },

        photoSwipe: function () {

            $(".J_pswp").on("click", function (event) {

                var $thisimg = $(this).find("img")[0];
                var evn = event || window.event;
                evn.preventDefault();
                var newimg = new Image();
                newimg.src = $(this).attr("href");
                $(newimg).load(function () {
                    var $this = $(this);
                    var pswpElement = $(".pswp").get(0);
                    // build items array
                    var items = [
                        {
                            src: newimg.src,
                            w: newimg.width,
                            h: newimg.height
                        }
                    ];
                    // define options (if needed)
                    var options = {
                        history: false,
                        focus: false,
                        showHideOpacity: true,
                        getThumbBoundsFn: function (index) {
                            var pageYScroll = window.pageYOffset || document.documentElement.scrollTop;
                            var rect = $thisimg.getBoundingClientRect();

                            return {x: rect.left, y: rect.top + pageYScroll, w: rect.width};
                        }

                    };

                    options.mainClass = 'pswp--minimal--dark';
                    options.barsSize = {top: 0, bottom: 0};
                    options.captionEl = false;
                    options.fullscreenEl = false;
                    options.shareEl = false;
                    options.bgOpacity = 0.55;
                    options.tapToClose = true;
                    options.tapToToggleControls = true;

                    var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
                    gallery.init();
                })
            })
        },

        goTopHtml: function () {

            $(document).scroll(function () {
                var scorllTop = $(document).scrollTop();
                if (scorllTop > 500) {
                    $("#goTop").fadeIn();
                } else {
                    $("#goTop").hide();
                }
            });

            $("#goTop").on("click",function (event) {
                var evn = event || window.event;
                evn.preventDefault();

                $("body,html").animate({
                    scrollTop: 0
                }, 1000,"swing");
            })
        },

        checkDevice: function () {

            // 修改下载链接地址
            var ua = navigator.userAgent.toLowerCase();
            var IOS = {"href": "http://www.tk.com/Aat/App/index.html"};
            var android = {"href": "http://www.tk.com/Aat/App/index.html"};
            var downloadBtn = $("#subjectContent").find(".btn"),
                btnUrl = downloadBtn.attr("href");
            if (/iphone|ipad|ipod/.test(ua) && btnUrl == 'javascript:;') {
                downloadBtn.attr(IOS);
            } else {
                downloadBtn.attr(android);
            }
        },

        checkNavCurrent: function (subject) {
            var item = $("#navBar").find("a");
            item.each(function () {
                var e = $(this),
                    mark = e.attr("data-mark");
                if (mark == subject) {
                    e.closest("li").addClass("on").siblings("li").removeClass("on");
                }
            })
        },
        template: function () {
            var htmlCodes = [
                '{{each DotList as value i}}',
                '{{if DotList[i].DotTit }}',
                '    <li>',
                '        <h3 class="row"><span>{{DotList[i].DotTit}}</span></h3>',
                '        <p class="row analyze"><span><b>考向分析：</b>{{DotList[i].DotAnalyze}}</span></p>',
                '        <div class="row content">',
                '            <div class="ques-attr">',
                '                <cite class="round',
                '                {{if (DotList[i].Frequency >= 5 && (Subject=="zhengzhi" || Subject=="huaxue"))}}',
                '                 bg-red',
                '                {{else if (DotList[i].Frequency >= 4 && Subject=="lishi" || Subject=="shengwu")}}',
                '                 bg-red',
                '                {{else if (DotList[i].Frequency >= 7 && Subject=="wuli")}}',
                '                 bg-red',
                '                {{else if (DotList[i].Frequency >= 6 && (Subject=="dili" || Subject=="yuwen"))}}',
                '                 bg-red',
                '                {{else if (DotList[i].Frequency >= 10 && (Subject=="likeshuxue" || Subject=="wenkeshuxue"))}}',
                '                 bg-red',
                '                {{else}}',
                '                 bg-grey',
                '                {{/if}}',
                '                ">{{DotList[i].Frequency}}</cite><br>',
                '                <span>必考点提示</span>',
                '            </div>',
                '            <div class="ques-attr">',
                '                <cite class="round',
                '                {{if (DotList[i].BlindSpot !=="" && DotList[i].BlindSpot <= 3 && (Subject=="zhengzhi" || Subject=="dili" || Subject=="likeshuxue" || Subject=="wenkeshuxue"))}}',
                '                 bg-yellow',
                '                {{else if ( DotList[i].BlindSpot !=="" && (Subject=="yuwen") && (i==5 || i==7 || i==12 || i==13 || i==17 || i==19 || i==21 || i==26 || i==32 || i==33 || i==34) )}}',
                '                 bg-yellow',
                '                {{else if ( DotList[i].BlindSpot !=="" && DotList[i].BlindSpot <= 2 && (Subject=="wuli"))}}',
                '                 bg-yellow',
                '                {{else if ( DotList[i].BlindSpot !=="" && DotList[i].BlindSpot < 2.5 && (Subject=="shengwu"))}}',
                '                 bg-yellow',
                '                {{else if ( DotList[i].BlindSpot !=="" && DotList[i].BlindSpot <= 1.5 && (Subject=="huaxue"))}}',
                '                 bg-yellow',
                '                {{else if ( DotList[i].BlindSpot !=="" && DotList[i].BlindSpot <= 6 && (Subject=="lishi"))}}',
                '                 bg-yellow',
                '                {{else}}',
                '                 bg-grey',
                '                {{/if}}',
                '                ">{{DotList[i].BlindSpot===""?"-":DotList[i].BlindSpot}}</cite><br>',
                '                <span>易失分提示</span>',
                '            </div>',
                '        </div>',
                '        <div class="row ques-attr ques-type">',
                '            <cite>常考题型：</cite>',
                '            <span>{{ DotList[i].QuesType }}</span>',
                '        </div>',
                '        <div class="row ques-attr ques-num">',
                '            <cite>密卷考题索引：</cite>',
                '            <span>{{DotList[i].QuesNum ===""?"/":DotList[i].QuesNum}}</span>',
                '        </div>',
                '        <div class="row ques-attr ques-diff">',
                '            <cite>难易程度：</cite>',
                '            {{if DotList[i].Diff == 1}}',
                '                <span class="i-star-s">',
                '                    <a class="star-a"></a>',
                '                    <a class="star-c"></a>',
                '                    <a class="star-c"></a>',
                '                </span>',
                '            {{else if DotList[i].Diff == 2}}',
                '                <span class="i-star-s">',
                '                    <a class="star-a"></a>',
                '                    <a class="star-a"></a>',
                '                    <a class="star-c"></a>',
                '                </span>',
                '            {{else if DotList[i].Diff == 3}}',
                '                <span class="i-star-s">',
                '                    <a class="star-a"></a>',
                '                    <a class="star-a"></a>',
                '                    <a class="star-a"></a>',
                '                </span>',
                '            {{else}}',
                '                <span class="i-star-s">',
                '                    <a class="star-c"></a>',
                '                    <a class="star-c"></a>',
                '                    <a class="star-c"></a>',
                '                </span>',
                '            {{/if}}',
                '        </div>',
// '        <div class="row down-btn">',
// '            <a href="javascript:;" class="btn">下载app,巧练考点专项</a>',
// '        </div>',
                '    </li>',
                '{{/if}}',
                '{{/each}}'
            ].join("");

            if (window.data && data.Subject) {
                var render = template.compile(htmlCodes);
                var html = render(data);
                $("#subjectContent").html(html);
                $(".subjectContent li").show();
            }
        }
    };

    yucebiaoJs.init();
});
