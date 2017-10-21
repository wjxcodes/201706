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
<div id="main" class="main" >
    <!-- 主体内容  -->
    <div class="content" >
			
        <div class="title"><?php echo ($pageName); ?></div>
        <!-- 查询区域 -->
        <div class="operate">
            <!-- <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="output" value="导出" onclick="" class="btexport output imgButton"></div> -->
			<div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
            <form id="form1" method="post" action="<?php echo U('Statistics/StatisticsB/homeWorkList');?>">
                <div class="fRig">
                    <div class="fLeft">
                        <span id="key">
                    <input id="name" TYPE="text" NAME="name" value="<?php echo ($_REQUEST['name']); ?>" title="教师用户名查询" class="medium" /></span></div>
                    <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
                    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
                </div>
                <!-- 高级查询区域 -->
                <div id="searchM" class=" none search cBoth">
                    <table border="0" cellpadding="1" cellspacing="3" width="100%">
                        <input type="hidden" name="diffExport" value="1">
                        <tr><td class="tRight" width="90">教师用户名：</td>
                            <td><input type="text" name="teacherName" class="small" value="<?php echo ($_REQUEST['TestID']); ?>" /></td>
                            <td class="tRight" width="80">试题数量：</td>
                            <td>
                                <input type="text" name="num" class="small" value="<?php echo ($_REQUEST['TestID']); ?>" />
                            </td>
                            <td class="tRight" width="80">作答方式</td>
                            <td>
                                <select class="small bLeft" name="workStyle">
                                    <option value="">选择</option>
                                    <option value="0" <?php if(($_REQUEST['workStyle']) == "0"): ?>selected="selected"<?php endif; ?>>在线作答</option>
                                    <option value="1" <?php if(($_REQUEST['workStyle']) == "1"): ?>selected="selected"<?php endif; ?>>下载作答</option>
                                </select>
                            </td>
                            <td class="tRight" width="80">作业类型</td>
                            <td>
                                <select class="medium bLeft" name="workType">
                                    <option value="">选择</option>
                                    <option value="1" <?php if(($_REQUEST['workType']) == "1"): ?>selected="selected"<?php endif; ?>>试题作业</option>
                                    <option value="2" <?php if(($_REQUEST['workType']) == "2"): ?>selected="selected"<?php endif; ?>>导学案作业</option>
                                </select>
                            </td>
                        </tr>
						<tr>
							<td class="tRight" width="100">所属学科：</td>
							<td class="tLeft" >
								<select id="subject" class="large bLeft" name="SubjectID" check='Require' warning="所属学科不能为空">
									<option value="">请选择</option>
									<?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label="<?php echo ($vo["SubjectName"]); ?>">
											<?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $edit["SubjectID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
											</optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
									<?php else: ?>
										<option value="0">请添加学科</option><?php endif; ?>
								</select>
							</td>
						</tr>
						<tr>
                            <td width="60" class="tRight">日期：</td>
                            <td width="350">
							<input type="text"  class="medium inputTime" value="<?php echo ($_GET['time']); ?>" name="StartTime">-
                            <input type="text" value="<?php echo ($_GET['End']); ?>" class="medium inputTime" name="EndTime">
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
        <!-- 列表显示区域  -->
        <div class="list" >
            <div id="result" class="result none"></div>
            <!-- Think 系统列表组件开始 -->
            <table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
                <tr><td height="5" colspan="10" class="topTd" ></td></tr>
                <tr class="row" >
                    <th>作业ID</th>
                    <th>作业名称</th>
                    <th>出题教师用户名</th>
                    <th>试题数量/知识数量</th>
                    <th>开始时间</th>
                    <th>结束时间</th>
                    <th>作答人数</th>
                    <th>查看</th>
                </tr>
                <?php if(is_array($workList)): $i = 0; $__LIST__ = $workList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
                    <td><?php echo ($node["WorkID"]); ?></td>
                    <td><?php echo ($node["WorkName"]); echo ($node["WorkType"]); ?></td>
                    <td><?php echo ($node["UserName"]); ?></td>
                    <td><?php echo ($node["TestNum"]); ?>/<?php echo ($node["LoreNum"]); ?></td>
                    <td><?php echo ($node["StartTime"]); ?></td>
                    <td><?php echo ($node["EndTime"]); ?></td>
                    <td><?php echo ($workDoNum[$node["WorkID"]]?$workDoNum[$node["WorkID"]]:0); ?></td>
                    <td><a href="<?php echo U('Statistics/StatisticsB/userWorkInfo',array('id'=>$node['WorkID']));?>">详情</a></td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <tr><td height="5" colspan="10" class="bottomTd"></td></tr>
            </table>
            <!-- Think 系统列表组件结束 -->
        </div>
        <!--  分页显示区域 -->
        <div class="page"><?php echo ($page); ?></div>
        <!-- 列表显示区域结束 -->
    </div>
    <!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>