<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
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
var URL = '/Ga/TypesDefault';
var APP     =     '';
var PUBLIC = '/Public';
var softdog=0;
<?php if(C("openKeysoft")== 1): ?>softdog=1;<?php endif; ?>
</script>
<script type="text/javascript" src="/Public/zjadmin/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
</HEAD>

<body>
<?php if(C("openKeysoft")== 1): ?><div style="display:none;"><embed id="s_simnew31"  type="application/npsyunew3-plugin" hidden="true"> </embed></div><?php endif; ?>
<div id="loader" >页面加载中...</div>
<!-- 主页面开始 -->
<div id="main" class="main" >
    <!-- 主体内容  -->
    <div class="content" >
        <div class="title"><?php echo ($pageName); ?><a href="<?php echo U('Ga/TypesDefault/updateCache');?>">更新缓存</a></div>
        <!--  功能操作区域  -->
        <div class="operate">
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="新增" onclick="" class="btadd add imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="编辑" onclick="" class="btedit edit imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>

            <!-- 查询区域 -->
            <FORM METHOD="POST" ACTION="/Ga/TypesDefault">
                <div class="fRig">
                    <div class="fLeft">
                        <span id="key">
                            <INPUT id="name" TYPE="text" NAME="DefaultID" value="<?php echo ($_REQUEST['DefaultID']); ?>" title="编号查询" class="medium" ></span></div>
                    <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
                    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
                </div>
                <!-- 高级查询区域 -->
                <div id="searchM" class=" none search cBothN">
                    <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
                        <TR>
                            <TD class="tRight" width="80">所属学科：</TD>
                            <TD><SELECT class="medium bLeft" NAME="SubjectID">
                                <option value="">选择</option>
                                <?php if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label='<?php echo ($vo["SubjectName"]); ?>'>
                                <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $edit["PID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
                            </SELECT></TD>
                        </TR>
                    </TABLE>
                </div>
            </FORM>
        </div>
        <!-- 功能操作区域结束 -->

        <!-- 列表显示区域  -->
        <div class="list" >
            <div id="result" class="result none"></div>
            <!-- Think 系统列表组件开始 -->
            <table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
                <tr><td height="5" colspan="11" class="topTd" ></td></tr>
                <tr class="row" >
                    <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
                    <th width="10%">编号</th>
                    <th>所属学科</th>
                    <th>所属地区</th>
                    <th>题型属性</th>
                    <th>操作</th>
                </tr>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
                    <td width="3%"><input type="checkbox" class="key" value="<?php echo ($node["DefaultID"]); ?>"></td>
                    <td width=""><?php echo ($node["DefaultID"]); ?></td>
                    <td width="15%"><?php echo ($node["SubjectName"]); ?></td>
                    <td width="40%"><?php echo ($node["AreaName"]); ?></td>
                    <td>
                        <table>
                            <?php if(is_array($node["TypesName"])): foreach($node["TypesName"] as $k=>$vo): ?><tr>
                                <td>
                                    <?php echo ($vo); ?>
                                </td>
                                <td>
                                    选取<?php echo ($node['Num'][$k]); echo ($node['IntelName'][$k]); ?>
                                </td>
                                <?php if($node['SelectType'][$k] == 0): ?><td>
                                    <?php if($node['IntelNum'] != ''): ?>每<?php echo ($node['IntelName'][$k]); echo ($node['IntelNum'][$k]); ?>小题
                                    <?php else: ?>
                                    每<?php echo ($node['IntelName'][$k]); ?>0小题<?php endif; ?>
                                </td><?php endif; ?>
                                <td>
                                    每题<?php echo ($node['Score'][$k]); ?>分
                                </td>
                                <td>
                                    选做：<?php if($node['ChooseNum'][$k] == 0): ?>无<?php else: echo ($node['ChooseNum'][$k]); ?>道<?php endif; ?>
                                </td>
                            </tr><?php endforeach; endif; ?>
                        </table>
                    </td>
                    <td><a href="#" class="btedit" thisid="<?php echo ($node["DefaultID"]); ?>">编辑</a>&nbsp;</td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <tr><td height="5" colspan="11" class="bottomTd"></td></tr>
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