<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo ($pageName); ?> - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>"/>
    <meta name="description" content="<?php echo ($config["Description"]); ?>"/>
    <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/work.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script type="text/javascript">
        var local = "<?php echo U('WorkReport/classIndex');?>";
        var classID='<?php echo ($classID); ?>';
    </script>
</head>
<body>
<script type="text/javascript" src="/Public/plugin/echarts-plain.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script><!--echarts必须放在body内-->
<div id="workDiv" class="homeWorkReportClass">
    <div id="workBox" class="PublicBox">
        <div class="classList wLeft">
            <div class="classTit">班级列表</div>
            <div class="loadClass">
                <div class="prev" onselectstart="return false;" oncontextmenu="return false" title="向上滚动">向上移动</div>
                <div class="bd"></div>
                <div class="next" onselectstart="return false;" oncontextmenu="return false" title="向下滚动">向下移动</div>
            </div>
        </div>
        <div class="teacher">
            <div class="righttop">
                <div id="categorylocation">
                    <span class="newPath">当前位置：</span>> 学情评价 > 作业动态学情分析
                </div>
            </div>
            <div class="Publictitle">
                <h3 class="addTit">教学效果学情评估</h3>
            </div>
            <div class="dataInfo">
                <div class="infoLeft fl">
                    <div class="leftTop">考点教学效果雷达 <div class="Helpexplain ico_Helpexplain"
                                                       title="最近1-3次代表该知识点下最后三次生成能力值的
大小;能力值取值-3至3数值越大能力值越高，雷达
图折线越接近外部"></div></div>
                    <div class="leftInfo" id="radarImg"></div>
                </div>
                <div class="infoRight fr">
                    <div class="rightTop">历次作业平均分 <div class="Helpexplain ico_Helpexplain" title="每个点代表每次布置的作业，
学生作答分值的平均分"></div></div>
                    <div class="rightInfo" id="lineImg"></div>
                </div>
            </div>

            <div class="classStudent">
                <div class="studentTit">学生报告信息
                    <div class="message">
                       提示：灰色姓名是未激活的学生账号
                    </div>
                </div>
                <div class="studentInfo"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/Public/plugin/jquery.SuperSlide.2.1.1.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<!--[if lte IE 6]>
<script type="text/javascript" src="/Public/plugin/png.js"></script>
<script>DD_belatedPNG.fix('a,img,div,span');</script>
<![endif]-->
<script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript">
    $(function(){
        $.myHomeWorkReport.init();
        $(window).bind("resize", function () {
            $.myHomeWorkReport.initDivBoxHeight();
            $.myHomeWorkReport.classOver(true);
            if($.myHomeWorkReport.klPolar!=null){
                $.myHomeWorkReport.klPolar.resize();
            }
            if($.myHomeWorkReport.scoreLine!=null){
                $.myHomeWorkReport.scoreLine.resize();
            }
        });
    });
    $.myHomeWorkReport= {
        c: $('.homeWorkReportClass'),
        klPolar: null,
        scoreLine: null,
        init: function () {
            var c = this.c, self = this;
            c.unbind();
            self.initDivBoxHeight();
            self.changeClass();
            self.studentReport();
            self.loadClass();
            self.classOver(false);
            $(document).live('selectstart contextmenu',function(){
                return false;
            });
        },
        initDivBoxHeight: function () {
            var a = $(window).width();
            var b = $(window).height();
            if (a < 800) a = 800;
            $("#workBox").height(b).width(a);
            var e = parseInt(a) - parseInt($('.classList').width()) - 35;
            if (e < 600) e = 600;
            $(".teacher").width(e);
            var lh=b-$('.classTit').outerHeight(true);
            $('.loadClass').height(lh);
            $('.classList').height(b);
        },
        //载入班级
        loadClass:function(){
            var self = this;
            $('.loadClass .bd').html($.myCommon.loading());
            $.post(U('Work/MyClass/getClassList'),{'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    $('.loadClass').html('<p style="text-align: center;">班级加载失败</p>');
                    return false;
                }
                var tit='提示信息';
                if(data['data'][0]=='success'){
                    var output='<ul>';
                    var current='';
                    for(var i in data['data'][1]){
                        if(classID){
                            if(classID==data['data'][1][i]['ClassID']){
                                current=' class="current"';
                            }else{
                                current ='';
                            }
                        }else{
                            if(i==0){
                                current=' class="current"';
                            }else{
                                current ='';
                            }
                        }
                        output+='<li'+current+' cid="'+data['data'][1][i]['ClassID']+'" title="'+data['data'][1][i]['ClassName']+'">'+data['data'][1][i]['ClassName']+'</li>';
                    }
                    output+='</ul>';
                    $('.loadClass .bd').html(output);
                    classID=$('.loadClass li.current').attr('cid');
                    self.loadStudentInfo(classID);
                }else if(data['data'][0]=='add'){
                    $('.loadClass').html('<p style="text-align: center;">请添加班级</p>');
                    $.myDialog.normalMsgBox('loadclassmsgdiv',tit,450,'您还未加入任何班级！是否立刻加入？',3);
                    $('#loadclassmsgdiv .normal_yes').live('click',function(){
                        location.href=U('MyClass/myClass');
                    });
                    return false
                }
            });
        },
        /**
         * 班级过多增加点击滚动
         * @param ifNumChange 班级数目变化引起的班级过载 班级显示高度变化/增加班级/删除班级/解散班级
         */
        classOver:function(ifNumChange){
            if(ifNumChange==true){
                var ulStr=$('.loadClass .bd .tempWrap').html();
                $('.loadClass .bd').html(ulStr);
                $('.loadClass ul').css({'top':0});
            }
            //班级数目过载
            if($('.loadClass ul').height()>$('.loadClass').height()){
                if($('.prev:visible').length==0) {
                    $('.prev,.next').slideDown(10);
                }
                var lh = $(window).height()-$('.classTit').outerHeight(true)-40;

                //计算班级应显示的个数
                var num=Math.floor(lh/$('.loadClass ul li').outerHeight());
                jQuery(".loadClass").slide({mainCell:".bd ul",autoPage:true,type:'slide',effect:"top",autoPlay:false,vis:num,trigger:"click",scroll:num,easing:'swing'});
            }else{
                //判断点击条是否显示 显示则隐藏
                if($('.prev:visible').length>0){
                    $('.prev,.next').slideUp(10);
                    $('.loadClass .bd').html('<ul>'+$('.loadClass ul').html()+'</ul>');
                }
            }
        },
        //生成能力雷达
        _klPolar: function (data) {
            if (this.klPolar) {
                //this.klPolar.dispose();
                this.klPolar = null;
            }
            if (!data.indicator.length || !data.series.length) {
                $('#radarImg').html('<span class="noData">暂时没有考点能力值数据！</span>');
                return false;
            }
            $('#radarImg').html('').removeAttr('_echarts_instance_');
            this.klPolar = echarts.init(document.getElementById('radarImg'));
            this.klPolar.showLoading({
                text: '正在努力的读取数据中...'   //loading话术
            });
            var option = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    x: 'center',
                    data: ['最近第1次', '最近第2次', '最近第3次']
                },
                toolbox: {
                    show: true,
                    feature: {
                        saveAsImage: {show: true}
                    }
                },
                calculable: true,
                polar: [
                    {
                        indicator: data.indicator,
                        radius: 70
                    }
                ],
                series: [
                    {
                        name: '知识点掌握情况',
                        type: 'radar',
                        itemStyle: {
                            normal: {
                                areaStyle: {
                                    type: 'default'
                                }
                            }
                        },
                        data: data.series
                    }
                ]
            };
            this.klPolar.setOption(option);
        },
        //生成折线图
        _scoreLine: function (data) {
            if (this.scoreLine) {
                //this.scoreLine.dispose();
                this.scoreLine = null;
            }
            if (!data.xAxis.length || !data.series.length) {
                $('#lineImg').html('<span class="noData">暂时没有作业数据！</span>');
                return false;
            }
            $('#lineImg').html('').removeAttr('_echarts_instance_');
            this.scoreLine = echarts.init(document.getElementById('lineImg'));
            this.scoreLine.showLoading({
                text: '正在努力的读取数据中...'   //loading话术
            });
            var option = {
                toolbox: {
                    show: true,
                    feature: {
                        saveAsImage: true
                    }
                },
                tooltip: {
                    trigger: 'axis'
                },
                xAxis: [
                    {
                        type: 'category',
                        boundaryGap: false,
                        data: data.xAxis
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        axisLabel: {
                            formatter: '{value} 分'
                        },
                        splitArea: {show: true}
                    }
                ],
                series: [
                    {
                        name: '作业平均分',
                        type: 'line',
                        data: data.series

                    }
                ]
            };
            this.scoreLine.setOption(option);
        },
        //获取当前班级id
        getCurrentClassId: function () {
            return this.c.find('.loadClass li.current').attr('cid');
        },
        //切换班级
        changeClass: function () {
            var c = this.c;
            var self = this;
            c.on('click', '.loadClass li', function () {
                classID = $(this).attr('cid');
                if(self.loadStudentInfo(classID)){
                    c.find('.loadClass li').removeClass('current');
                    $(this).addClass('current');
                }
            });
        },
        //获取作业动态学情分析及学生列表
        loadStudentInfo: function (classID) {
            if(lock!=''){
                return false;
            }
            var self = this;
            $('.studentInfo,#radarImg,#lineImg').html($.myCommon.loading());
            lock='classIndexData';
            $.post(U('WorkReport/classIndexData'), {'classID': classID}, function (e) {
                lock='';
                if ($.myCommon.backLogin(e) == false) {
                    return false;
                }
                self.setStudentList(e.data.studentList);
                self._klPolar(e.data.radarInfo);//雷达图
                self._scoreLine(e.data.lineInfo);//折线图
            });
            return true;
        },
        //查看学生作业动态学情分析
        studentReport: function () {
            var self = this;
            this.c.on('click', '.studentInfo li', function () {
                if ($(this).attr('class') == 'noClick') {
                    return false;
                }
                var cid = self.getCurrentClassId();
                location.href = U('WorkReport/studentIndex?id=' + $(this).attr('uid') + '&cid=' + cid);
            });
        },
        //加载班级学生
        setStudentList:function(studentList){
            if(studentList) {
                var str = '';
                str += '<ul>';
                for (var i in studentList) {
                    str += '<li uid="' + studentList[i]['UserID'] + '" title="' + studentList[i]['UserName'] +
                            '" class="';
                    if (studentList[i]['RealName']) {
                        str += 'click ';
                    }
                    str += '">';
                    if (studentList[i]['RealName']) {
                        str += studentList[i]['RealName'] + '(' + studentList[i]['OrderNum'] + ')';
                    } else {
                        str += studentList[i]['OrderNum'] + '(未激活)';
                    }
                    str += '</li>';
                }
                str += '</ul>';
                $('.studentInfo').html(str);
                return false;
            }
            $('.classStudent .studentInfo').html('<p style="text-align:center;color:#ccc;">该班级没有学生,赶快添加学生吧!</p>');
        }
    }
</script>

</body>
</html>