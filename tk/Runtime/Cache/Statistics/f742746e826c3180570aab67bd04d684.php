<?php if (!defined('THINK_PATH')) exit();?>
<!--顶部导航-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="zh-CN">
<head>
    <title>教学统计</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <meta property="qc:admins" content="167741560767461006375" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <link type="text/css" href="/Public/index/css/wln-base.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/index/css/index.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link rel="stylesheet" href="/Public/index/css/style.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"/>
	<HEAD>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<TITLE>『<?php echo (C("WLN_WEB_NAME")); ?>管理平台』</TITLE>
	<link rel="stylesheet" type="text/css" href="/Public/zjadmin/css/style.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" />
	<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
	<!--基础文件，分别是jQuery基库和拖拽UI插件-->
	<script src="/Public/plugin/jquery.ui.draggable.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" type="text/javascript"></script>
	<script src="/Public/plugin/testOperation.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" type="text/javascript"></script>
	<!-- 对话框核心JS文件和对应的CSS文件-->
	<script src="/Public/plugin/alert/jquery.alerts.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" type="text/javascript"></script>
	<link href="/Public/plugin/alert/jquery.alerts.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" type="text/css" media="screen" />
	<script type="text/javascript" src="/Public/plugin/ueditor/ueditor.config.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
	<script type="text/javascript" src="/Public/plugin/ueditor/ueditor.all.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
	<script type="text/javascript" src="/Public/plugin/editor.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
	<script LANGUAGE="JavaScript">
//指定当前组模块URL地址 
var URL = '/Statistics/StatisticsB';
var APP     =     '';
var PUBLIC = '/Public';
var softdog=0;
<?php if(C("openKeysoft")== 1): ?>softdog=1;<?php endif; ?>
</script>
<script type="text/javascript" src="/Public/zjadmin/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
</HEAD>
</head>
<div class="top-logo-wrap w1000">
    <a class="top-logo" href="javascript:void(0)">
        <img src="/Public/index/imgs/publ/logo.png" alt="logo"/>
    </a>
</div>
<div class="top-nav-fixed">
    <div class="top-nav-wrap">
        <div class="top-nav w1000">
			<span>
            <a class="top-nav-item" href="<?php echo U('Statistics/Index/index');?>">教师列表</a>
			</span>
			<span>
            <a class="top-nav-item" href="<?php echo U('Statistics/StatisticsB/Teacherzj');?>">教师组卷下载统计</a>
			</span>
            <span class="top-nav-item">
                <a class="tn-link" href="<?php echo U('Statistics/StatisticsB/studentWorkList');?>">学生做题统计</a>
            </span>
            <span class="top-nav-item">
                <a class="tn-link" href="<?php echo U('Statistics/StatisticsB/wrongTest');?>">学生错题统计</a>
            </span>
			<span class="top-nav-item">
                <a class="tn-link" href="<?php echo U('Statistics/StatisticsB/teachercheck');?>">教师批改统计</a>
            </span>
			<span class="top-nav-item">
                <a class="tn-link" href="<?php echo U('Statistics/StatisticsB/teacherComment');?>">教师评语统计</a>
            </span>
			<span class="top-nav-item">
                <a class="tn-link" href="<?php echo U('Statistics/StatisticsB/wkvedio');?>">微课(视频)统计</a>
            </span>
        </div>
    </div>
</div>
<!--顶部导航-end-->
<!-- 主页面开始 -->
<script type="text/javascript" src="http://echarts.baidu.com/build/dist/echarts.js"></script>
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content" >
<div class="title"><?php echo ($pageName); ?> [ <A HREF="javascript:history.go(-1);">返回上一页</A> ]</div>
<!--  功能操作区域  -->
<div class="operate" >
    <!-- <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="output" value="导出" onclick="" class="btexport output imgButton"></div> -->
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>

    <!-- 查询区域 -->
    <FORM  id="form1" METHOD="POST" ACTION="<?php echo U('Statistics/StatisticsB/index');?>">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="name" value="<?php echo ($_REQUEST['name']); ?>" title="用户名查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <!-- <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div> -->
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR>
            <TD class="tRight" width="60">用户名：</TD>
            <TD><INPUT TYPE="text" NAME="UserName" class="small" value="<?php echo ($_REQUEST['UserName']); ?>" ></TD>
            <TD class="tRight" width="80">真实姓名：</TD>
            <TD ><INPUT TYPE="text" NAME="RealName" class="small" value="<?php echo ($_REQUEST['RealName']); ?>"></TD>
            <TD class="tRight" width="60">地区：</TD>
            <TD><select id="sf" class="w90px selectArea">
                <option value="">选择省份</option>
                <?php if(is_array($arrArea)): $i = 0; $__LIST__ = $arrArea;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><option value="<?php echo ($sub["AreaID"]); ?>" last="<?php echo ($sub["Last"]); ?>"><?php echo ($sub["AreaName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            </SELECT></TD>
            <TD class="tRight" width="45">学校：</TD>
            <TD class="tLeft" width="200" >
            <SELECT class="w90px bLeft" NAME="SchoolID" id='school'>
                <option value="">请选上级</option>
            </SELECT>
            </td>
            <TD class="tRight" width="60">日期：</TD>
            <TD width="350"><INPUT TYPE="text" NAME="Start" class="medium inputTime" value="<?php echo ($_REQUEST['Start']); ?>"> - 
            <INPUT TYPE="text" NAME="End" class="medium inputTime" value="<?php echo ($_REQUEST['End']); ?>">
            </TD>
        </TR>
        </TABLE>
    </div>
        </FORM>
</div>
<script>
$(document).ready(function(){
    var areaParent="<?php echo ($areaParent); ?>";
    $('.selectArea').areaSelectChange("/Statistics/StatisticsB",1);
    if("<?php echo ($act); ?>"=="edit"){
        $('#sf').areaSelectLoad('/Statistics/StatisticsB',areaParent);
    }
});
</script>
<!-- 功能操作区域结束 -->

<!-- 列表显示区域  -->
<div class="list" >
<div id="result" class="result none"></div>
<!-- Think 系统列表组件开始 -->
<table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
    <tr><td height="5" colspan="14" class="topTd" ></td></tr>
    <tr class="row tCenter" >
        <th width="8%">用户</th>
        <th width="8%">真实姓名</th>
        <th width="8%">联系方式</th>
        <th width="8%">邮箱</th>
        <th width='5%'>省</th>
        <th width='5%'>市</th>
        <th width='5%'>区/县</th>
        <th width='12%'>学校名称</th>
        <th width='6%'>所属学科</th>
        <th width="6%">身份</th>
        <th>下载次数</th>
        <th>智能组卷次数</th>
        <th>模板组卷次数</th>
        <th>组卷总次数</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists tCenter" jl=''>
        <td><?php echo ($node["UserName"]); ?></td>
        <td><?php echo ($node["RealName"]); ?></td>
        <td><?php echo ($node["Phonecode"]); ?></td>
        <td><?php echo ($node["Email"]); ?></td>
        <td><?php echo ($node["province"]); ?></td>
        <td><?php echo ($node["city"]); ?></td>
        <td><?php echo ($node["AreaName"]); ?></td>
        <td><?php echo ($node["SchoolName"]); ?></td>
        <td><?php echo ($node["SubjectName"]); ?></td>
        <td>【<?php if(($node["Whois"]) == "0"): ?>学生<?php else: ?>教师<?php endif; ?>】</td>
        <td><?php echo ($node["ComTimes"]); ?></td>
        <td><?php echo ($node["dirTotal"]); ?></td>
        <td><?php echo ($node["tplTotal"]); ?></td>
        <td><?php echo ($node["allTotal"]); ?></td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="14" class="bottomTd"></td></tr>
</table>
<!-- Think 系统列表组件结束 -->
</div>
<!--  分页显示区域 -->
<div class="page" style="text-align: center";><?php echo ($page); ?></div>

<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->

</div>
<div id = "main1" style="height:400px; width: 1662px;
    margin-left: 6%;"></div>
<script type="text/javascript">
		//路径配置
		require.config({
			paths:{
				echarts:'http://echarts.baidu.com/build/dist'
			}
		});	

		//使用
		require(
			[
				'echarts',
				'echarts/chart/bar',
				'echarts/chart/line'
			],
			function (ec) {
				// 基于准备好的dom，初始化echarts图表
				var myChart = ec.init(document.getElementById('main1'));
				var option = {
				    title : {
				        text: '教师组卷下载统计',
				        subtext: '柳泉中学'
				    },
				    tooltip : {
				        trigger: 'axis'
				    },
				    legend: {
				        data:['下载次数','智能组卷次数','模板组卷次数','组卷总次数']
				    },
				    toolbox: {
				        show : true,
				        feature : {
				            mark : {show: false},
				            dataView : {show: true, readOnly: false},
				            magicType : {show: true, type: ['line', 'bar']},
				            restore : {show: true},
				            saveAsImage : {show: true}
				        }
				    },
				    calculable : true,
				    xAxis : [
				        {
				            type : 'category',
				            data:<?php echo ($RealName); ?>
				        }
				    ],
				    yAxis : [
				        {
				            type : 'value'
				        }
				    ],
				    series : [
				        {
				            name:'下载次数',
				            type:'bar',
				            data:<?php echo ($ComTimes); ?>,
				            markPoint : {
				                data : [
				                    {type : 'max', name: '最大值'},
				                    {type : 'min', name: '最小值'}
				                ]
				            },
							markLine : {
				                data : [
				                    {type : 'average', name: '平均值'}
				                ]
				            }
				        },
				        {
				            name:'智能组卷次数',
				            type:'bar',
				            data:<?php echo ($dirTotal); ?>,
				            markPoint : {
				                data : [
				                    {type : 'max', name: '最大值'},
				                    {type : 'min', name: '最小值'}
				                ]
				            },
				            markLine : {
				                data : [
				                    {type : 'average', name: '平均值'}
				                ]
				            }
				        },
						{
				            name:'模板组卷次数',
				            type:'bar',
				            data:<?php echo ($tplTotal); ?>,
				            markPoint : {
				                data : [
				                    {type : 'max', name: '最大值'},
				                    {type : 'min', name: '最小值'}
				                ]
				            },
				            markLine : {
				                data : [
				                    {type : 'average', name: '平均值'}
				                ]
				            }
				        },
						{
				            name:'组卷总次数',
				            type:'bar',
				            data:<?php echo ($allTotal); ?>,
				            markPoint : {
				                data : [
				                    {type : 'max', name: '最大值'},
				                    {type : 'min', name: '最小值'}
				                ]
				            },
				            markLine : {
				                data : [
				                    {type : 'average', name: '平均值'}
				                ]
				            }
				        }
				    ]
				};
				//为echart对象加载数据
				myChart.setOption(option);
			}
		);
</script>
<!-- 主页面结束 -->

</body>
</html>