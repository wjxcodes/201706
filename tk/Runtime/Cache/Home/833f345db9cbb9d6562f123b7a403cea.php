<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title><?php echo ($pageName); ?> - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/index.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <!--[if lte IE 6]>
    <script type="text/javascript" src="/Public/plugin/png.js"></script>
    <script>DD_belatedPNG.fix('.tabs span,.tabs div');</script>
    <![endif]-->
</head>
<body>
<div id="box" style="position:relative;">
    <div id="explorer">
    您的IE浏览器版本太低，建议安装以下浏览器来访问组卷系统，兼容好速度快。<br />
    <a id="ie" title="IE8.0浏览器" href="http://www.baidu.com/s?wd=ie8+%CF%C2%D4%D8&rsv_bp=0&rsv_spt=3&inputT=4203" target="_blank"></a><a id="chrome" title="谷歌浏览器(推荐)" href="http://www.google.cn/chrome/intl/zh-CN/landing_chrome.html" target="_blank"></a><a id="firefox" title="火狐浏览器(推荐)" href="http://firefox.com.cn/download/" target="_blank"></a><a id="safari" title="Apple苹果浏览器" href="http://www.apple.com.cn/safari/download/" target="_blank"></a><a id="opera" title="Opera浏览器" href="http://cn.opera.com/download/" target="_blank"></a>
    </div>
    <!-- 顶部广告位 -->
    <div class="top-active-wrap" style="display: none;"><span class="hide-top-active" title="关闭">关闭</span><a target="_blank" class="top-active w1000" href="<?php echo U('/Task','',false);?>"><img src="/Public/index/imgs/task/active/task-active-bar.jpg"></a></div>
    <!-- 顶部广告位 end-->
    <div class="content_box">
        <div style="margin-right:338px;">
            <div class="content_01" id="tktj">
                <div class="title"><a href="<?php echo U('Statistics/Index/index');?>" title='查看每日统计'><span class="fl">统计信息</span></a><span class="fr">现在是：<i id="currenttime"></i><font>&nbsp;&nbsp; |&nbsp;&nbsp;</font> 当前ip：<?php echo ($IP); ?> <font>&nbsp;&nbsp; |&nbsp;&nbsp;</font> 总访问量：<span class='TjVisit'>?</span>人</span></div>
                <div class="nr_box">
                    <p><strong>总量：</strong>试题：<font><span class='TjTotalTest'>?</span></font>题，试卷：<font><span class='TjTotalDoc'>?</span></font>份，所有用户共组卷：<font><span class='TjTotalZj'>?</span></font>次。</p>
                    <p><strong>最近30天：</strong>试题：<font><span class='bTotalTest'>?</span></font>题，试卷：<font><span class='bTotalDoc'>?</span></font>份，所有用户共组卷：<font><span class='bTotalZj'>?</span></font>次。</p>
                    <p><strong>最近7天：</strong>试题：<font><span class='sTotalTest'>?</span></font>题，试卷：<font><span class='sTotalDoc'>?</span></font>份，所有用户共组卷：<font><span class='sTotalZj'>?</span></font>次。</p>
                    <p><strong>最近1天：</strong>试题：<font><span class='yTotalTest'>?</span></font>题，试卷：<font><span class='yTotalDoc'>?</span></font>份，所有用户共组卷：<font><span class='yTotalZj'>?</span></font>次。</p>
                    <a href="<?php echo U('Statistics/Index/index');?>" style='float:right;margin-right:10px;font-size:13px;'>查看每日更新</a>
                    <div style='float:none;clear:both;'></div>
                </div>
            </div>

            <div class="tabbed_content">
                <div class="tabbed_content_title">
                    <div class="tit fl">推荐内容<span>|</span></div>
                    <div class="newTestpaper fl"><a href="javascript:parent.$.myCommon.go(U('Manual/Index/sj'));">最新试卷</a></div>
                    <a href="javascript:parent.$.myCommon.go(U('Manual/Index/sj'));" class="more fr">更多</a>
                    <div class="tabs fr">
                        <div class="moving_bg">&nbsp;</div>
                        <span class="tab_item" did="0"></span>
                        <span class="tab_item" did="1"></span>
                        <span class="tab_item" did="2"></span>
                    </div>
                    <div class="clear"></div>
                </div>
            
                <div class="slide_content">                        
                    <div class="tabslider" id="tabslider"><p class="list_ts"><span class="ico_dd">数据加载中请稍候...</span></p>
                    </div>
                </div>
            </div>
        </div>
    <div style="width:328px; position:absolute; right:0px; top:0px">
        <div class="content_01" id="xtgg" style="margin-bottom:10px">
            <div class="title"><span class="fl">系统公告</span><a class="more fr" href="<?php echo U('Index/newsList');?>">更多</a></div>
            <div class="nr_box" id="news">
            <?php if(is_array($news)): $i = 0; $__LIST__ = $news;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><p><a href="<?php echo U('Index/newsCon?id='.$vo['NewID']);?>" title="<?php echo ($vo["NewTitle"]); ?>" style='color:#<?php echo ($vo["Color"]); ?>'><?php echo ($vo["NewTitle"]); ?></a></p><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
        <div class="content_01" id="xtgk">
            <div class="title"><span class="fl">系统特色</span></div>
            <div class="nr_box">
                <p class="text">智能组卷系统既能实现全智能化组卷，又能结合教师个人的出题经验进行手工出题组卷。丰富的试卷版式一键切换，直接生成word文档所见即所得。</p>
                <p>知识点出题：精确定位，高考组卷首选</p>
                <p>教材章节出题：教材同步，课后推荐习题</p>
                <p>试卷出题：每日更新，所见即所得</p>
                <p>关键词找题：选题不再大海捞针</p>
                <p>智能组卷：简单设置，秒速组出完美试卷</p>
                <p>模板组卷：“双向细目表”选题，定制试卷</p>
                <p>学情评价：作业、练习实时监控反馈</p>
            </div>
        </div>
    </div>
</div>
</div>
<div id="footer" style="width:100%;">
    <div style="border-top:1px solid #bbb;"></div>
    <div style="font-family:'Arial'; font-size:12px; text-indent:20px; line-height:30px; padding-top:5px;">智慧云题库云平台</div>
</div>
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
<script>
var local='<?php echo U('Index/content');?>';
</script>
<script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/default/js/paper.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script>
var subjectID=Cookie.Get("SubjectId");
jQuery.myContent = {
    //初始化
    init:function(){
        this.loadBrowser(); //载入浏览器
        this.loadDocList(); //载入文档列表
        this.initDivHeight(); //重置框架
        this.loadTableMouse(); //载入滑动门事件
        this.statistics(); //异步加载统计信息
        this.jumpPaperView(); //ajax跳转试卷出题

        $(window).resize(function() { $.myContent.initDivHeight(); });
        //载入时间
        setInterval(function(){
            var curDate    = new Date();
            var dateFormat = curDate.getFullYear() + "年" + (parseInt(curDate.getMonth())+1) + "月" + curDate.getDate() + "日 " + curDate.getHours() + ":" + curDate.getMinutes() + ":" + curDate.getSeconds();
            $("#currenttime").text(dateFormat);
        },1000);
    },
    //重置框架
    initDivHeight:function() {
        var height = $(window).height()-$('#footer').outerHeight();
        $("#box").css({ 'height': height-5 ,'overflow-y':'auto','overflow-x':'hidden'});
    },
    //载入浏览器
    loadBrowser:function(){
        if ($.browser.msie && ($.browser.version == "6.0" || $.browser.version == "7.0") ) {
            $('#explorer').css({'display':'block'});
        }
    },
    //载入文档列表
    loadDocList:function(){
        $.post(U('Index/getDocList'),{'sid':subjectID,'o':'rdown','perpage':21,'times':Math.random()},function(data){
            var tmp_str='';
            var j=1;
            if($.myCommon.backLogin(data)==false){
                return false;
            }
            //没有选择学科
            if(data['data']=='chooseSubject'){
                $.myCommon.chooseSubject();
                return false;
            }
            for(var tmp_i in data['data'][0]){
                if(j%7==1){
                    tmp_str+='<ul class="testcon"';
                    if(j!=1) tmp_str+=' style="display:none;" ';
                    tmp_str+='>';
                }
                tmp_str+='<li>'+
                        '<div class="quesinfobox">'+
                        '<div class="quesinfo_tit" ><span>['+data['data'][0][tmp_i]['subjectname']+data['data'][0][tmp_i]['typename']+']</span><a class="jumppaperview" style="font-size:14px;" did="'+data['data'][0][tmp_i]['docid']+'" sid="'+subjectID+'" href="#">'+data['data'][0][tmp_i]['docname']+'</a></div>'+
                        '</div>'+
                        '</li>';
                if(j%7==0) tmp_str+='</ul>';
                j++;
            }
            $('#tabslider').html(tmp_str);
        });
    },
    //载入滑动门事件
    loadTableMouse:function(){
        $(".tab_item").mouseover(function() {
            var background = $(this).parent().find(".moving_bg");

            $(background).stop().animate({
                left: $(this).position()['left']
            }, {
                duration: 300
            });

            $('.testcon').css('display','none');
            var tmp_id=$(this).attr('did');
            $('.testcon :eq('+tmp_id+')').css('display','block');
        });
    },
    //异步加载统计信息
    statistics:function(){
        $.get('<?php echo U("Statistics/Index/home");?>',{'times':Math.random()},function(r){
            if($.myCommon.backLogin(r)==false){
                return false;
            }
            if(r['data']['stat'] == 'error'){
                var tktj = $('#tktj');
                var html = tktj.html();
                tktj.html(html.replace(/\?/g,'0'));
            }else{
                for(var val in r['data']){
                    var obj = $('#tktj').find('.'+val);
                    obj.html(r['data'][val]);
                }
            }
        });
    },
    //ajax跳转试卷出题
    jumpPaperView:function(){
        $('.jumppaperview').live('click',function(){
            //检测试卷是否存在
            var tmp='';
            var docID=$(this).attr('did');
            var subjectID=$(this).attr('sid');
            //提示载入
            $('#iframe',parent.document.body).attr('src',U('Manual/Index/sj?docID='+docID+'&subjectID='+subjectID));
        });
    }
}
//默认科目
$(document).ready(function(){
    var start1 = '2015-11-05 12:00';
    var end1 = '2015-11-24';
    new BannerTimer(start1, end1).task(topBannerShow);
    setInterval($.myContent.statistics, 5000);
    $.myContent.init();
});

/*专题限时弹出*/
            // 活动1-顶部固定条
            // 活动2-弹出框
            // var start2 = '2015-11-03 12:00';
            // var end2 = '2015-11-04';
            // new BannerTimer(start2, end2).task(bannerShow);

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
    setTimeout(function(){(function(){
            setTimeout(
                function(){
                    $(".top-active-wrap").slideDown(1000);
                },1000);
            $(".hide-top-active").on("click",function(){
                $(this).parents(".top-active-wrap").slideUp(500);
            });
        })();
    },1000);
}
</script>
<div style="display:none;">
    <?php echo (C("WLN_STATISTICAL_CODE.HOME")); ?>
</div>
</body>
</html>