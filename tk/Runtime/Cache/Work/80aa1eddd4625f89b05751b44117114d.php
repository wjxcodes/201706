<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title><?php echo ($pageName); ?> - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <link type="text/css" href="/Public/default/css/common.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/zjwork.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script>
    var local='<?php echo U('Work/checkWorkDetail');?>';
    var workID    = '<?php echo ($WorkID); ?>';
    var classID   = '<?php echo ($ClassID); ?>';
    var userWork  = '';//作业数据
    var testCategory = ''; //试题栏目名称
    var testCount = '';

    var testInfo  = '';//试题信息


    var testCheck = '';//作业错误数据
    var sendNum;       //发送作业人数


    var testContent  = '';//试题内容
    var struct       = '';
    </script>
</head>
<body>
<div id="workDiv">
    <div id="workBox" class="check-work">
        <div class="w-left class-list">
            <div class="class-tit f-yahei">班级列表</div>
            <div class="load-class">
                <div class="prev" title="向上滚动" onselectstart="return false;" oncontextmenu="return false">向上移动</div>
                <div class="bd"></div>
                <div class="next" title="向下滚动" onselectstart="return false;" oncontextmenu="return false" title="向上滚动">向下移动</div>
            </div>
        </div>
        <div class="w-right">
            <div id="rightTop" class="crumbs-wrap">
                 <div class="g-crumbs">
                       <span class="now-path">当前位置：</span>
                       <span id="loca_text"></span> > 作业模块 > 作业详细
                </div>
            </div>
            <!--批改作业标题-->
            <div class="public-title">
                <h3 class="tit f-yahei">批改作业</h3>

                <div class="add-work-info">
                    <div class="info">
                        <span>班级</span>
                         <cite>></cite>
                          <span>作业</span>
                    </div>
                </div>
            </div>
            <div class="mg-classWork-wrap">
                <div class="cw-tab f-yahei">
                    <ul>
                        <li class="on" tabid="1">
                            <a href="javascript:">答题情况</a>
                        </li>
                        <li tabid="2">
                            <a href="javascript:">错题排行</a>
                        </li>
                        <li tabid="3">
                            <a href="javascript:">作业详情</a>
                        </li>
                    </ul>
                </div>
                <!--答题情况-->
                <div id="tab1" class="tabData cw-tab-container">
                    <div class="cw-tips clearfix">
                        <div class="cw-ques-tag">
                                <span> <i class="bc-green"></i>
                                    做对
                                </span>
                                <span> <i class="bc-red"></i>
                                    做错
                                </span>
                                <span>
                                    <i class="bc-orange"></i>
                                     半对
                                </span>
                                <span>
                                    <i class="bc-blue"></i>
                                    未批阅
                                </span>
                                <span>
                                    <i class="bc-gray"></i>
                                    未做
                                </span>
                        </div>
                        <div class="tips-handle-btn">
                                <span class="tips-txt">
                                    <i class="iconfont">&#xe600;</i>
                                    提示：点击题号可直接批阅查看试题
                                </span>
                                <span class="tips-btn">
                                    <a id="quickCorrect" class="nor-btn" href="javascript:">快速批阅</a>
                                    <a class="nor-btn tips-btn-comment comment" sid="0" href="javascript:">一键写评语</a>
                                </span>
                        </div>
                    </div>
                    <div class="cw-details">
                        <table class="cw-table">
                            <thead>
                            <tr class="cw-thead-tr">
                                <th class="cw-th-name">
                                    <span>姓名</span>
                                </th>
                                <th class="cw-th-info">
                                    <span>做题情况</span>
                                </th>
                                <th class="cw-th-percent">
                                    <span>是否超时</span>
                                </th>
                                <th class="cw-th-handle">
                                    <span>操作</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!--错题排行-->
                <div id="tab2" class="tabData cw-tab-container">
                    <div class="cw-details mt30">
                        <table class="cw-table">
                            <thead>
                            <tr class="cw-thead-tr">
                                <th class="cw-th-test-num">
                                    <span><a href="javascript:;">题号</a></span>
                                </th>
                                <th class="cw-th-error-num">
                                    <span><a href="javascript:;">答错人数</a></span>
                                </th>
                                <th class="cw-th-choose">
                                    <span>A</span>
                                </th>
                                <th class="cw-th-choose">
                                    <span>B</span>
                                </th>
                                <th class="cw-th-choose">
                                    <span>C</span>
                                </th>
                                <th class="cw-th-choose">
                                    <span>D</span>
                                </th>
                                <th class="cw-th-choose" style="width:70px;">
                                    <span>其他</span>
                                </th>
                                <th class="cw-th-error-rate">
                                    <span>正确率</span>
                                </th>
                                <th class="cw-th-test-type">
                                    <span>试题类型</span>
                                </th>
                                <th class="cw-th-handle-2">
                                    <span>操作</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--作业详情-->
                <div id="tab3" class="tabData cw-tab-container">
                    <!--题目快捷导航-->
                    <div class="cw-ques-mini-nav">
                        <div class="ques-index-box f-roman">
                        </div>
                    </div>
                    <!--题目快捷导航-结束-->
                    <!--提交作业学生-->
                    <div class="cw-stu-list-box"></div>
                    <div class="test-list"></div>
                    <!--提交作业学生-结束-->
                    <div class="wln-backtop"><a title="回到顶部" href="javascript:;" class="backtop"><div class="arrow"></div><div class="stick"></div></a></div>
                </div>
            </div>
       </div>
    </div>
</div>
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="/Public/plugin/jquery.SuperSlide.2.1.1.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/default/js/paper.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/default/js/workdown.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/default/js/setWork.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>\
<script type="text/javascript" src="/Public/default/js/work.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>\
<!--[if lte IE 6]>
<script type="text/javascript" src="/Public/plugin/png.js"></script>
<script>DD_belatedPNG.fix('a,img,div,span');</script>
<![endif]-->
<script>
    $(function(){
        var subjectID=Cookie.Get("SubjectId");
        $('.add-work-info span').html(parent.jQuery.myMain.getQuesBank(subjectID)['SubjectName']);//再处理
        $.myWorkCheckDetail.init();
        $(document).bind("selectstart",function(){return false;});
    });
</script>

</body>
</html>