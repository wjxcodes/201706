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
var URL = '/User/User';
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
<div class="content">
<!--  功能操作区域  -->
<div class="title"><?php echo ($pageName); ?> [ <A HREF="/User/User">返回列表</A> ]   [ <A HREF="javascript:history.go(-1);">返回上一页</A> ]</div>
<!-- 功能操作区域结束 -->
<div id="result" class="result none"></div>
<!-- 内容显示区域  -->

<TABLE cellpadding="5" cellspacing="0" class="list" border="1">
<tr><td height="5" colspan="7" class="topTd" ></td></tr>
<TR>
    <TD class="tRight" >上传用户名单：</TD>
    <TD class="tLeft" ><INPUT TYPE="file" NAME="userinfo" id="uploads"/><br/><a class="adduser_downexcel">点此下载excel模板</a>
    </TD>
</TR>
<TR class="userInfo" style="display:none">
    <TD class="tRight" >上传用户信息：</TD>
    <TD>
        <table cellpadding="7" cellspacing="0" class="list uploadInfo" border="1">
            <tr>
                <th>序号</th>
                <th>用户名</th>
                <th>密码</th>
                <th>真实姓名</th>
                <th>错误提示</th>
                <th>操作</th>
            </tr>
        </table>
    </TD>
</TR>
<TR>
    <TD class="tRight">所在地区：</TD>
    <TD class="tLeft">
        <SELECT class="medium bLeft selectArea" NAME="AreaID[]" id='area' >
            <option value="">——请省份——</option>
            <?php if(is_array($area_array)): $i = 0; $__LIST__ = $area_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["AreaID"]); ?>" ><?php echo ($vo["AreaName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
        </SELECT>
    </TD>
</TR>
<TR>
    <TD class="tRight" >所属学校：</TD>
    <TD class="tLeft" >
        <SELECT class="medium bLeft" NAME="areaid_school" id='school'>
            <option value="">——请选择上级——</option>
        </SELECT>
        <div id="schooladd" style="display:none"><input type="text" name='SchoolName' value=''>&nbsp;&nbsp;&nbsp;(<font style="color:red">没有搜到学校，请手动添加学校</font>)</div>
    </TD>
</TR>
<TR>
    <TD class="tRight tTop">用户身份：</TD>
    <TD class="tLeft">
        <label><INPUT TYPE="radio" class="bLeft" NAME="Whois" value="0"> 学生</label>
        <label><INPUT TYPE="radio" class="bLeft" NAME="Whois" value="1"> 教师</label>
    </TD>
</TR>
<TR>
    <TD class="tRight" width="100">所属学科：</TD>
    <TD class="tLeft" ><SELECT id="subject" class="large bLeft" NAME="SubjectID">
        <option value="">请选择</option>
        <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label="<?php echo ($vo["SubjectName"]); ?>">
            <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>">　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
        </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
        <?php else: ?>
        <option value="0">请添加学科</option><?php endif; ?>
    </SELECT></TD>
</TR>
<TR>
    <TD class="tRight" width="100">所属年级：</TD>
    <TD class="tLeft" >
        <select name="GradeID" id='grade' class="GradeID">
            <option value="">请先选择学科</option>
            <?php if(is_array($grade)): $i = 0; $__LIST__ = $grade;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$gvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($gvo["GradeID"]); ?>"><?php echo ($gvo["GradeName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
    </TD>
</TR>
<?php if($edit == ''): if(is_array($userPower)): $num = 0; $__LIST__ = $userPower;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$userPowerTmp): $mod = ($num % 2 );++$num;?><TR>
    <TD class="tRight tTop"><?php echo ($powerName[$num]); ?>：</TD>
    <TD class="tLeft">
        <?php if($num != 3): if(is_array($userPowerTmp)): $i = 0; $__LIST__ = $userPowerTmp;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vi): $mod = ($i % 2 );++$i;?><label><INPUT TYPE="radio" class="bLeft" name="groupname_<?php echo ($num); ?>" value="<?php echo ($vi["PUID"]); ?>" <?php if(in_array(($vi["PUID"]), is_array($default)?$default:explode(',',$default))): ?>checked="checked"<?php endif; ?>><?php echo ($vi["UserGroup"]); ?>&nbsp&nbsp</label><?php endforeach; endif; else: echo "" ;endif; ?>
        <?php else: ?>
        <?php if(is_array($userPowerTmp)): $i = 0; $__LIST__ = $userPowerTmp;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vi): $mod = ($i % 2 );++$i;?><label><INPUT TYPE="checkbox" class="bLeft" name="groupname_<?php echo ($num); ?>[]" value="<?php echo ($vi["PUID"]); ?>" <?php if(in_array(($vi["PUID"]), is_array($default)?$default:explode(',',$default))): ?>checked="checked"<?php endif; ?>><?php echo ($vi["UserGroup"]); ?>&nbsp&nbsp</label><?php endforeach; endif; else: echo "" ;endif; endif; ?>
    </TD>
</TR><?php endforeach; endif; else: echo "" ;endif; endif; ?>
<TR>
    <TD class="tRight" >到期日期：</TD>
    <TD class="tLeft" ><INPUT TYPE="text" class="bLeft inputDate" NAME="EndTime" value=""  check="Require" warning="请填写日期" /></TD>
</TR>
<?php if($customGroup): ?><TR>
    <TD class="tRight tTop">自定义分组：</TD>
    <TD class="tLeft">    
        <?php if(is_array($customGroup)): $i = 0; $__LIST__ = $customGroup;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vi): $mod = ($i % 2 );++$i;?><label><INPUT TYPE="radio" class="bLeft" name="customGroup" value="<?php echo ($vi["GroupID"]); ?>"><?php echo ($vi["GroupName"]); ?>&nbsp&nbsp</label><?php endforeach; endif; else: echo "" ;endif; ?>
    </TD>
</TR><?php endif; ?>
<TR>
    <TD ></TD>
    <TD class="center"><div style="width:85%;margin:5px">
        <INPUT TYPE="hidden" name="UserID" value="<?php echo ($edit["UserID"]); ?>">
        <INPUT TYPE="hidden" name="act" value="<?php echo ($act); ?>">
        <div class="impBtn fLeft"><INPUT tag='form1' TYPE="button" value="提交" class="upload imgButton mysubmit"></div>
        <div class="impBtn fLeft m-l10"><INPUT TYPE="reset" class="reset imgButton" value="清空" ></div>
    </div></TD>
</TR>
<tr><td height="5" colspan="7" class="bottomTd" ></td></tr>
</TABLE>

    <!-- 列表显示区域结束 -->
</div>
    <!-- 主体内容结束 -->
</div>
<script type="text/javascript" src="/Public/plugin/uploadify/jquery.uploadify.min.js"></script>
<link rel="stylesheet" type="text/css" href="/Public/plugin/uploadify/uploadify.css">
<script>
 $('.selectArea').areaSelectChange("/User/User",1);
$('#subject').subjectSelectChange('/User/User',{'style':'getMoreData','list':'grade'});
    $(function(){
        $("#uploads").uploadify({
            'formData'    : {'z':Math.random()},
            'swf'     : '/Public/plugin/uploadify/uploadify.swf',
            'uploader'      : U('User/User/uploadify'),
            'queueID'       : 'queues',
            'buttonText'    : '上传excel名单',
            'fileTypeExts'  : '*.xls; *.xlsx', //允许文件上传类型,和fileDesc一起使用.
            'fileTypeDesc'  : 'excel file',  //将不允许文件类型,不在浏览对话框的出现.
            'method'        : 'post',
            'auto'          : true,
            'multi'         : false,

            'onUploadSuccess':function(file, data, response){
                if(data.indexOf('Warning')!=-1 && data.indexOf('Content-Length')!=-1){
                    alert('文件大小超出限制');
                    return false;
                }else if(data.indexOf('Warning')!=-1){
                    alert(data);
                    return false;
                }else if(data=='type error' || data==''){
                    alert('您上传的文件类型有误！请上传*.xls,*.xlsx;文件');
                    return false;
                }else if(data.indexOf('filesize exceeds')!=-1){
                    alert('文件大小超出限制');
                    return false;
                }else if(data.indexOf('file not upload')!=-1){
                    alert('文件无法上传');
                    return false;
                }else if(data.indexOf('success')!=-1){
                    var userData=eval('('+data+')');
                    var userStr = '';
                    for(var i in userData.data){
                        if(i=='serial' || i=='return'){
                            continue;
                        }
                        if(userData['data'][i][2]==null | userData['data'][i][2]==''){
                            userData['data'][i][2] = '空';
                        }
                        if(userData['data'][i]['error']){
                            userStr += '<tr style="color:red">';
                        }else{
                            userStr += '<tr>';
                            userData['data'][i]['message']='';
                        }
                        userStr += '<td>'+userData['data'][i]['order']+'</td>' +
                                '<td>'+userData['data'][i][0]+'</td>'+
                                '<td>'+userData['data'][i][1]+'</td>'+
                                '<td>'+userData['data'][i][2]+'</td>'+
                                '<td>'+userData['data'][i]['message']+'</td>'+
                                '<td><a href="#" class="deluser">删除</a></td></tr>';
                    }
                    userStr += '<tr><td style="display:none" class="serialData">'+userData['data']['serial']+'</td></tr>';
                    $('.uploadInfo').find('td').remove();
                    $('.userInfo').show();
                    $('.uploadInfo').append(userStr);
                }else{
                    alert(data);
                    return false;
                }
            },
            'onUploadError':function(file, errorCode, errorMsg, errorString){
                alert(errorMsg);
            }
        });
    })
    var delOrder ='';
    //删除错误用户数据
    $('.deluser').live('click',function(){
        delOrder+=','+$(this).parent().parent().find('td').eq(0).html();
        $(this).parent().parent().remove();
    })

    //下载excel模板
    $('.adduser_downexcel').live('click',function(){
        location.href=U('User/User/excelUser');
    });

    //批量上传用户提交
    $('.upload').live('click',function(){
        var userName = new Array();
        var userInfo = new Array();
        if($('.uploadInfo').find('tr[style="color:red"]').length>0){
            var errorStr='';
            $('.uploadInfo').find('tr[style="color:red"]').each(function(i){
                errorStr+='第'+$(this).find('td').eq(0).html()+'行用户信息:'+$(this).find('td').eq(4).html()+'\n';
            })
            alert(errorStr);
            return false;
        }

        $('.uploadInfo').find('tr').each(function(i){
            var userTmp = new Array();
            $(this).find('td').each(function(j){
                if(j==4){
                    return true;
                }
                userTmp[j] = $(this).html();
            });
            if(userTmp==''){
                return true;
            }
            userInfo[i] = userTmp;
        });
        var powerInfo = new Array();
        if($("input[name='Whois']:checked").length>0){
            powerInfo[4] = $("input[name='Whois']:checked").val();
        }else{
            alert('请选择用户身份');
            return false;
        }
        if($("#subject").val()){
            powerInfo[5] = $("#subject").val();
        }
        if($(".GradeID").val()){
            powerInfo[6] = $(".GradeID").val();
        }
        if($('input[name="EndTime"]').val()){
            powerInfo[7] = $('input[name="EndTime"]').val();
        }
        if($('select[name="areaid_school"]').val()){
            powerInfo[8] = $('select[name="areaid_school"]').val();
        }
        powerInfo[9] = $('.selectArea[name="AreaID[]"]').last().val();
        powerInfo[1]=$("input[name='groupname_1']:checked").val();
        powerInfo[2]=$("input[name='groupname_2']:checked").val();
        powerInfo[3] = '';
        if($("input[name='groupname_3[]']:checked").val()){
            $("input[name='groupname_3[]']:checked").each(function(){
                powerInfo[3]+=$(this).val()+',';
            })
        }
        var lastIndex = powerInfo[3].lastIndexOf(',');
        powerInfo[3] = powerInfo[3].substring(0,lastIndex);
        var serialData = $('.serialData').html();
        delOrder = delOrder.substring(1);
        var groupID = $("input[name='customGroup']:checked").val();
        $.post(U('User/User/addUsers'),{'serialData':serialData,'powerInfo':powerInfo,'delOrder':delOrder,'groupID':groupID},function(data){
            if(checkPower(data)=='error'){
                return false;
            }
            alert('添加成功');
            window.location.href=U("User/User/index");
        })
    })
</script>