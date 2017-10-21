<?php if (!defined('THINK_PATH')) exit();?>﻿
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
var URL = '/Guide/CaseLoreDoc';
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
<div class="title"><?php echo ($pageName); ?></div>
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="新增" onclick="" class="btadd add imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="提取" onclick="" class="btdrall edit imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="lock" value="锁定" onclick="" class="btlock lock imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
    
    <!-- 查询区域 -->
    <FORM METHOD="POST" ACTION="/Guide/CaseLoreDoc">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="name" placeholder="请输入文档名称" value="<?php echo ($_REQUEST['name']); ?>" title="模板查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
        <!-- 高级查询区域 -->
        <div id="searchM" class=" none search cBoth">
            <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
                <TR>
                    <TD class="tRight" width="80">文档名称：</TD>
                    <TD ><INPUT TYPE="text" NAME="DocName" class="middle" value="<?php echo ($_REQUEST['DocName']); ?>"></TD>
                    <TD class="tRight" width="100">所属学科：</TD>
                    <TD class="tLeft" ><SELECT id="subject" class="bLeft" NAME="SubjectID" check='Require' warning="所属学科不能为空">
                        <option value="">请选择</option>
                        <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label="<?php echo ($vo["SubjectName"]); ?>">
                            <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $edit["SubjectID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                        </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
                        <?php else: ?>
                        <option value="0">请添加学科</option><?php endif; ?>
                    </SELECT></TD>
                    <TD class="tRight" width="80">修改状态：</TD>
                    <TD><SELECT class="small bLeft" NAME="Status">
                        <option value="">选择</option>
                        <option value="0">正常</option>
                        <option value="1">锁定</option>
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
    <tr><td height="5" colspan="10" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th width="3%">编号</th>
        <th width="20%">文档名称</th>
        <th width="6%">学科</th>
        <th width="33%">章节</th>
        <th width="7%">所属栏目</th>
        <th width="5%">作者 / 添加时间</th>
        <th width="15%">描述</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
            <td><input type="checkbox" class="key" value="<?php echo ($node["DocID"]); ?>"></td>
            <td><?php echo ($node["DocID"]); ?></td>
            <td><a href="#" class="btedit" thisid="<?php echo ($node["DocID"]); ?>"><?php echo ($node["DocName"]); ?>
                <?php if($node["IfGet"] == 0): ?><font color="red">(未提取)</font><?php endif; ?>
                <?php if($node["DocIntro"] == 1): ?><font color="red">(已入库)</font><?php endif; ?></a>
                <p>
                    doc-word:<a href="<?php echo U('CaseLoreDoc/showWord',array('docID'=>$node['DocID'],'style'=>1));?>" target="_blank">下载word</a><br/>
                    <?php if($node["DocHtmlPath"] != ''): ?>doc-html:<a href="<?php echo U('CaseLoreDoc/showWord',array('docID'=>$node['DocID']));?>" target="_blank">打开网页</a><?php endif; ?>
                </p>
            </td>
            <td><?php echo ($node["SubjectName"]); ?></td>
            <td><?php echo ($node["ChapterName"]); ?></td>
            <td><?php echo ($node["MenuName"]); ?></td>
            <td><?php echo ($node["Admin"]); ?> <br> <?php echo (date("Y-m-d H:i:s",$node["AddTime"])); ?> </td>
            <td><?php echo ($node["Description"]); ?></td>
            <td wid="<?php echo ($node["DocID"]); ?>" class="status"><?php if(($node["Status"]) == "1"): ?><a style="color:red;cursor:pointer" class="system" status="1">锁定</a>
                                                    <a style="cursor:pointer;display:none" class="system" status="0">正常</a>
                <?php else: ?><a style="cursor:pointer" class="system" status="0">正常</a>
                        <a style="cursor:pointer;display:none;color:red" class="system" status="1">锁定</a><?php endif; ?></td>
            <td>
                <div>
                    <a href="#" class="btedit" thisid="<?php echo ($node["DocID"]); ?>">编辑文档</a>
                </div>
                <div>
                    <a href="<?php echo U('CaseLoreDoc/testsave',array('DocID'=>$node['DocID']));?>" thisid="<?php echo ($node["DocID"]); ?>" class="getLore">提取知识</a>
                </div>
                <div>
                    <a href="<?php echo U('CaseLoreDoc/loreView',array('DocID'=>$node['DocID']));?>" thisid="<?php echo ($node["DocID"]); ?>" class="getLore">预览知识</a>
                </div>
                <div>
                    <a href="<?php echo U('CaseLore/index',array('DocID'=>$node['DocID']));?>" thisid="<?php echo ($node["DocID"]); ?>" class="getLore">查看知识</a>
                </div>
            </td>
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
<script>
    $('.system').click(function(){
        exchange('/Guide/CaseLoreDoc',$(this));
    })
    $('.lock').click(function(){
        var stop=0;
        if($('input[class="key"]:checked').length<1){
            alert('请选择操作项');
            return false;
        }
        $('input[class="key"]:checked').each(function(){
            if($(this).parent().parent().find('.status').find('a:visible').attr('status')==1){
                alert('您选择的数据中存在已锁定，请重试！');
                stop=1;
                return false;
            }
        })
        if(stop==1){
            return false;
        }
        valueChanges('/Guide/CaseLoreDoc',$('#checkList'));
    })
</script>

</body>
</html>