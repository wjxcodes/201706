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
var URL = '/Custom/CustomDocUpload';
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
<style>
    button{
        cursor:pointer;
        padding:2px 2px;
        border:1px outset #999; 
        background-color:#cde;
    }
</style>
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content" >
<div class="title"><?php echo ($pageName); ?></div>
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="提取" onclick="" class="btextract edit imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
    
    <!-- 查询区域 -->
    <FORM METHOD="POST" ACTION="/Custom/CustomDocUpload">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="name" value="<?php echo ($_REQUEST['name']); ?>" title="题型查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR>
            <TD class="tRight" width="60">题型：</TD>
            <TD><INPUT TYPE="text" NAME="TypesName" class="small" value="<?php echo ($_REQUEST['TypesName']); ?>" ></TD>
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
        <th>文档名称</th>
        <th>作者</th>
        <th>所属学科</th>
        <th>审核用户</th>
        <th>添加时间</th>
        <th>用户修改时间</th>
        <th>最后审核时间</th>
        <th>文档状态</th>
        <th width='80'>操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
        <td><input type="checkbox" class="key" value="<?php echo ($node["DUID"]); ?>"></td>
        <td><?php echo ($node["DUID"]); ?></td>
        <td><?php echo ($node["Title"]); ?></td>
        <td><?php echo ($node["UserName"]); ?></td>
        <td><?php echo ($node["SubjectName"]); ?></td>
        <td><?php echo ($node["AdminName"]); ?></td>
        <td><?php if($node["AddTime"] != 0): echo (date("Y-m-d H:i:s",$node["AddTime"])); endif; ?></td>
        <td><?php if($node["ModifiedTime"] != 0): echo (date("Y-m-d H:i:s",$node["ModifiedTime"])); endif; ?></td>
        <td><?php if($node["LastAuditTime"] != 0): echo (date("Y-m-d H:i:s",$node["LastAuditTime"])); endif; ?></td>
        <td><?php echo ($node["Status"]); ?></td>
        <td>
            <a href="<?php echo U('Custom/CustomDocUpload/notadopt',array('duID'=>$node[DUID]));?>" >审核不通过</a><br/>
            <a href="<?php echo U('Custom/CustomDocUpload/Adopt',array('duID'=>$node[DUID]));?>">审核通过</a><br/>
            <a href="<?php echo U('Custom/CustomDocUpload/showWord',array('docID'=>$node[DUID],'style'=>1));?>">下载文档</a><br/>
            <a href="<?php echo U('Custom/CustomDocUpload/uploadWord',array('docid'=>$node[DUID],'style'=>1));?>" title='标引后的word' class='upload' docid='<?php echo ($node["DUID"]); ?>'>上传文档</a>
            <?php if(!empty($node['DocPath'])): ?><br/><a href="<?php echo U('Custom/CustomDocUpload/extractTest',array('docid'=>$node[DUID]));?>" class='extract'>提取试题</a>
            <br/><a href="<?php echo U('Custom/CustomTest/index',array('docid'=>$node[DUID]));?>" class='extract'>查看试题</a><?php endif; ?>
        </td>
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
<div id='doSomeThing' style='display:none;'>
    <form action="<?php echo U('Custom/CustomDocUpload/uploadWord');?>" target="uploadFrame" method="post" enctype="multipart/form-data">
        <input type="hidden" name='id' id='primaryKeyId'>
        <input type="hidden" name='docid'/>
        <table class="list" cellpadding="5" cellspacing="0" border="1">
            <tr>
                <td width='120' align='right'>选择文档</td>
                <td width="*">
                    <input type="file" name='doc' class="formElement"/>
                </td>
            </tr>
            <tr>
                <td colspan="2" align='right'>
                    <button type='submit' id='saveForm' class='mulitOperate'>保存</button>
                    <button type='button' class='cancelForm'>取消</button>
                </td>
            </tr>
        </table> 
    </form>
    <iframe src="" frameborder="0" scrolling="no" style='display:none;' name='uploadFrame'></iframe>
</div>
<script src='/Public/zjadmin/js/originalityTpl.js'></script>
<script>
    var obj = 0;
    config = $.extend(config, {
        'editZone' : '<tr class="editForm"><td colspan="11"></td></tr>'
    });
    $('.upload').live('click', function(){
        var that = $(this);
        obj = that;
        showForm(that);
        var form = $('#doSomeThing form');
        form.find('input[name="docid"]').val(that.attr('docid'));
        form.find('input[name="doc"]').val('');
        return false;
    });
    $('.cancelForm').click(function(){
        hideForm();
    });
    $('#doSomeThing form').submit(function(){
        jInfo('上传中请稍候.....','上传数据');
    });
    function uploadCb(info){
        jClose();
        if(info != 'success'){
            alert(info);
        }else{
            var id = obj.attr('docid');
            if(obj.siblings('.extract').length == 0)
                var extractUrl=U('Custom/CustomDocUpload/extractTest?docid='+id);
                obj.after($('<br/><a href="'+extractUrl+'") >提取试题</a>'));
            hideForm();
            alert('保存成功！');
        }
    }
</script>
<!-- 主页面结束 -->

</body>
</html>