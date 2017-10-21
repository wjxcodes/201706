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
var URL = '/Work/ClassUser';
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
<script>
$(document).ready(function(){
    var areaParent="<?php echo ($areaParent); ?>";
    $('.selectArea').areaSelectChange('/Work/ClassUser');
    if("<?php echo ($act); ?>"=="edit"){
        $('#sf').areaSelectLoad('/Work/ClassUser',areaParent);
    }
});
</script>
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content" >
<div class="title"><?php echo ($pageName); ?> </div>
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="编辑" onclick="" class="btedit edit imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
<!-- 查询区域 -->
    <FORM METHOD="POST" ACTION="/Work/ClassUser">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="esayusername" value="<?php echo ($_REQUEST['name']); ?>" title="班级编号" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR>
            <TD class="tRight" width="80">学生编号：</TD>
            <TD width="80"><INPUT TYPE="text" NAME="UserName" class="small" value=""></TD>
            <TD class="tRight" width="80">班级编号：</TD>
            <TD  width="80"><INPUT TYPE="text" NAME="OrderID" class="small" value="" ></TD>
            <TD class="tRight" width="80">学校编号：</TD>
            <TD width="80"><INPUT TYPE="text" NAME="SchoolID" class="small" value=""></TD>
            <TD class="tRight" width="80">学生状态：</TD>
            <TD width="80">
                <select name='Status'>
                    <option value=''>-请选择-</option>
                    <option value='0'>正常</option>
                    <option value='1'>申请待审核</option>
                    <option value='2'>邀请待审核</option>
                </select>
            </TD>
            </tr>
            <tr>
            <TD class="tRight" width="60">所属年级：</TD>
            <TD  width="80"><SELECT class="normal bLeft" NAME="GradeID">
            <option value="">请选择年级</option>
            <?php if($gradeArray): if(is_array($gradeArray)): $i = 0; $__LIST__ = $gradeArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="" disabled><?php echo ($vo["GradeName"]); ?></option>
                <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["GradeID"]); ?>" <?php if(($item["GradeID"]) == $_REQUEST['GradeID']): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["GradeName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
            <?php else: ?>
                <option value="0">请添加年级</option><?php endif; ?>
            </SELECT>
            </TD>
            <TD class="tRight" width="80">所属地区：</TD>
            <TD class="tLeft" ><select id="sf" class="selectArea" check='Require' warning="省份不能为空">
            <option value="">请选择省份</option>
            <?php if(is_array($areaArray)): $i = 0; $__LIST__ = $areaArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><option value="<?php echo ($sub["AreaID"]); ?>" last="<?php echo ($sub["Last"]); ?>"><?php echo ($sub["AreaName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            </select></TD>

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
    <tr><td height="5" colspan="9" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th width="10%">编号</th>
        <th>用户名</th>
        <th>用户属性</th>
        <th>所在班级名称及编号</th>
        <th>邮箱</th>
        <th>状态</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
        <td><input type="checkbox" class="key" value="<?php echo ($node["CUID"]); ?>_<?php echo ($node["ClassID"]); ?>_<?php echo ($node["SubjectID"]); ?>"></td>
        <td><?php echo ($node["CUID"]); ?></td>
        <td><a href="#" class="btedit" thisid="<?php echo ($node["CUID"]); ?>_<?php echo ($node["ClassID"]); ?>_<?php echo ($classID); ?>"><?php echo ($node["RealName"]); ?>(<?php echo ($node["UserName"]); ?>)</a></td>
        <td><?php if($node["Whois"] == 0): ?>学生
            <?php elseif($node["Whois"] == 1 ): ?>
                <font style="color:#63A307">老师</font>
            <?php elseif($node["Whois"] == 2 ): ?>
                家长
            <?php elseif($node["Whois"] == 3 ): ?>
                校长<?php endif; ?></td>
        <td><?php echo ($node["ClassName"]); ?>(<?php echo ($node["ClassID"]); ?>)</td>
        <td><?php echo ($node["Email"]); ?></td>
        <td>        
        <?php if($node["Status"] == 0 ): ?>审核通过<?php else: ?><font style="color:#FE7676;cursor:pointer;"onclick="checkok('<?php echo ($node["CUID"]); ?>',this) ">未审核</font><?php endif; ?>
        </td>
        <td><?php echo (date('y-m-d H:i:s',$node["LoadTime"])); ?></td>
        <td><a href="#" class="btedit" thisid="<?php echo ($node["CUID"]); ?>_<?php echo ($node["ClassID"]); ?>_<?php echo ($ClassID); ?>">编辑</a>&nbsp;</td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="9" class="bottomTd"></td></tr>
</table>
<!-- Think 系统列表组件结束 -->
</div>
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<script type='text/javascript'>
function checkok(id,obj){
    $.ajax({
        type:'POST',
        dataType:'json',
        url:U('Work/ClassUser/checkUser'),
        data:{CUID:id},
        success:function(msg){
            //权限验证
            if(checkPower(msg)=='error'){
                return false;
            }
            var data=msg['data'];
            if(data[0]=="OK"){
                $(obj).css("color","black");
                $(obj).html('审核通过');
            }else{
                alert('审核失败！请重试或联系管理员！');
            }
        }
    })
    
}
</script>

<!-- 主页面结束 -->

</body>
</html>