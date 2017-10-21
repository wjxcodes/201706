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
var URL = '/Doc/WlnDoc';
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
        <div class="title"><?php echo ($pageName); ?> [ <A HREF="javascript:history.go(-1);">返回上一页</A> ] 
		
		<!-- <span class="red">* 文档提取试题以后才会有相应html版</span>--> </div> 
        <!--  功能操作区域  -->
        <div class="operate">
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="新增" onclick="" class="btaddex add imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="编辑" onclick="" class="bteditxls edit imgButton"></div>
            <!-- <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="提取" onclick="" class="btdrall edit imgButton"></div> -->
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="check" value="审核" onclick="" class="btcheck check imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="lock" value="锁定" onclick="" class="btlock lock imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>

            <!-- 查询区域 -->
            <form method="post" action="/Doc/WlnDoc">
                <!-- <div class="fRig"> -->
                    <!-- <div class="fLeft"> -->
                        <!-- <span id="key"> -->
                            <!-- <input id="name" type="text" name="name" value="<?php echo ($_REQUEST['name']); ?>" title="文档名称" class="medium" > -->
                        <!-- </span> -->
                    <!-- </div> -->
                    <!-- <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div> -->
                    <!-- <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div> -->
                </div>
                <!-- 高级查询区域 -->
                <div id="searchM" class=" none search cBoth">
                    <table border="0" cellpadding="1" cellspacing="3" width="100%">
                        <tr>
                            <td class="tRight" width="80">文档名称：</td>
                            <td><input type="text" name="DocName" class="small" value="<?php echo ($_REQUEST['DocName']); ?>"></td>
                            <td class="tRight" width="50">学科：</td>
                            <td>
                                <select class="normal bLeft" id='subject' name="SubjectID">
                                    <option value="0">请选择学科</option>
                                    <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label="<?php echo ($vo["SubjectName"]); ?>">
                                            <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $_REQUEST['SubjectID']): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                            </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
                                    <?php else: ?>
                                        <option value="0">请添加学科</option><?php endif; ?>
                                </select>
                            </td>
                            <td class="tRight" width="80">管理员：</td>
                            <td><input type="text" name="Admin" class="small" value="<?php echo ($_REQUEST['Admin']); ?>" ></td>
                            <td class="tRight" width="50">状态：</td>
                            <td>
                                <select class="small bLeft" name="Status">
                                    <option value="">选择</option>
                                    <option value="0" <?php if(($_REQUEST['Status']) == "0"): ?>selected="selected"<?php endif; ?>>正常</option>
                                    <option value="1" <?php if(($_REQUEST['Status']) == "1"): ?>selected="selected"<?php endif; ?>>锁定</option>
                                </select>
                            </td>
                            <td class="tRight" width="100">所属年份：</td>
                            <td>
                                <select class="small bLeft" name="DocYear">
                                    <option value="">选择</option>
                                    <?php $__FOR_START_20266__=$thisYear;$__FOR_END_20266__=1990;for($vo=$__FOR_START_20266__;$vo > $__FOR_END_20266__;$vo+=-1){ ?><option value="<?php echo ($vo); ?>" {#eq name="vo" value="<?php echo ($_REQUEST['DocYear']); ?>"#}selected="selected"{#/eq#}> <?php echo ($vo); ?></option><?php } ?>
                                </select>
                            </td>
                            <td class="tRight" width="100"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="tRight" width="80">文档编号：</td>
                            <td><input type="text" name="DocID" class="small" value="<?php echo ($_REQUEST['DocID']); ?>"></td>
                            <td class="tRight" width="50">属性：</td>
                            <td>
                                <select class="normal bLeft" name="TypeID">
                                    <option value="0">请选择属性</option>
                                    <?php if($docTypeArray): if(is_array($docTypeArray)): $i = 0; $__LIST__ = $docTypeArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["TypeID"]); ?>"><?php echo ($vo["TypeName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                    <?php else: ?>
                                        <option value="0">请添加属性</option><?php endif; ?>
                                </select>
                            </td>
                            <td class="tRight" width="80">所属地区：</td>
                            <td>
                                <select name="AreaID[]" id="sf" class="selectArea" class="medium bLeft">
                                    <option value="">选择</option>
                                    <?php if(is_array($areaArray)): $i = 0; $__LIST__ = $areaArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["AreaID"]); ?>" last="<?php echo ($vo["Last"]); ?>"><?php echo ($vo["AreaName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </td>
                            <td class="tRight" width="50">年级：</td>
                            <td>
                                <select class="normal bLeft" id='grade' name="GradeID">
                                    <option value="0">请选择年级</option>
                                    <?php if($gradeArray): if(is_array($gradeArray)): $i = 0; $__LIST__ = $gradeArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($vo["GradeName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                    <?php else: ?>
                                        <option value="0">请添加年级</option><?php endif; ?>
                                </select>
                            </td>
                            <td class="tRight" width="50">是否入库：</td>
                            <td>
                                <select class="normal bLeft" name="IfIntro">
                                    <option value="">全部</option>
                                    <option value="0" <?php if(($_REQUEST['IfIntro']) == "0"): ?>selected="selected"<?php endif; ?>>未入库</option>
                                    <option value="1" <?php if(($_REQUEST['IfIntro']) == "1"): ?>selected="selected"<?php endif; ?>>已入库</option>
                                </select>
                            </td>
                            <td class="tRight">使用范围：</td>
                            <td>
                                <select class="normal bLeft" name="ShowWhere">
                                    <option value="">请选择</option>
                                    <option value="1" <?php if(($_REQUEST['ShowWhere']) == "1"): ?>selected="selected"<?php endif; ?>>通用</option>
                                    <option value="0" <?php if(($_REQUEST['ShowWhere']) == "0"): ?>selected="selected"<?php endif; ?>>组卷专用</option>
                                    <option value="2" <?php if(($_REQUEST['ShowWhere']) == "2"): ?>selected="selected"<?php endif; ?>>提分专用</option>
                                    <option value="3" <?php if(($_REQUEST['ShowWhere']) == "3"): ?>selected="selected"<?php endif; ?>>前台禁用</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
        <!-- 功能操作区域结束 -->

        <!-- 列表显示区域  -->
        <div class="list" >
            <div id="result" class="result none"></div>
            <!-- Think 系统列表组件开始 -->
            <table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
                <tr><td height="5" colspan="11" class="topTd" ></td></tr>
                <tr class="row">
                    <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
                    <th width="30">编号</th>
                    <th>文档名称</th>
                    <th>年份/省份/学科/类型/年级</th>
                    <th>描述</th>
                    <th>添加人/时间</th>
                    <th width="30">状态</th>
                    <th width="60">操作</th>
                </tr>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
                        <td><input type="checkbox" class="key" value="<?php echo ($node["DocID"]); ?>" name='DocID'></td>
                        <td><?php echo ($node["DocID"]); ?></td>
                        <td>
                            <a href="#" class="bteditxls" thisid="<?php echo ($node["DocID"]); ?>"><?php echo ($node["DocName"]); echo ($node["ShowWhere"]); ?></a>
                            <?php if($node["IfGet"] == 0): ?><font color="red">(未提取)</font><?php endif; ?>
                            <?php if($node["IfIntro"] == 1): ?><font color="red">(已入库)</font><?php endif; ?>
                            <!-- <p> -->
                                <!-- doc-word:<a href="<?php echo U('Doc/WlnDoc/showWord',array('docID'=>$node['DocID'],'style'=>1));?>" target="_blank">下载word</a><br/> -->
                                <!-- <?php if($node["DocHtmlPath"] != ''): ?>doc-html:<a href="<?php echo U('Doc/WlnDoc/showWord',array('docID'=>$node['DocID']));?>" target="_blank">打开网页</a><?php endif; ?> -->
                                <!-- <?php if($node["Hearing"] != 0): ?>-->
                                <!-- <br><a href='<?php echo U('Doc/WlnDoc/downloadAudioFile', array('docId'=>$node['Hearing']));?>'>下载听力</a> -->
                                <!--<?php endif; ?> -->
                            <!-- </p> -->
                        </td>
                        <td>
                            <?php echo ((isset($node["DocYear"]) && ($node["DocYear"] !== ""))?($node["DocYear"]):"<font color='red'>无</font>"); ?>/
                            <?php echo ((isset($node["AreaName"]) && ($node["AreaName"] !== ""))?($node["AreaName"]):"<font color='red'>无</font>"); ?>/
                            <?php echo ((isset($node["SubjectName"]) && ($node["SubjectName"] !== ""))?($node["SubjectName"]):"<font color='red'>无</font>"); ?>/
                            <?php echo ((isset($node["TypeName"]) && ($node["TypeName"] !== ""))?($node["TypeName"]):"<font color='red'>无</font>"); ?>/
                            <?php echo ((isset($node["GradeName"]) && ($node["GradeName"] !== ""))?($node["GradeName"]):"<font color='red'>无</font>"); ?>
                        </td>
                        <td><?php echo ($node["Description"]); ?></td>
                        <td><?php echo ($node["Admin"]); ?><br/><?php echo (date("Y-m-d H:i:s",$node["LoadTime"])); ?></td>
                        <td>
                            <span id="status<?php echo ($node["DocID"]); ?>">
                                <?php if(($node["Status"]) == "0"): ?><span class="btlock" thisid="<?php echo ($node["DocID"]); ?>">正常</span>
                                <?php else: ?>
                                    <span class="btcheck" thisid="<?php echo ($node["DocID"]); ?>">
                                        <font color="red">锁定</font>
                                    </span><?php endif; ?>
                            </span>/
                            <?php if(($node["IfTest"]) == "0"): ?><font color="red">非测试</font>
                            <?php else: ?>
                                <font>测试</font><?php endif; ?>
                        </td>
                        <td>
                            <a href="#" class="bteditxls" thisid="<?php echo ($node["DocID"]); ?>">编辑</a><br/>
                            <a href="<?php echo U('Doc/WlnDoc/testsavexls',array('DocID'=>$node['DocID']));?>">提取试题</a><br/>
                            <?php if($node["IfIntro"] != 1): ?><!-- <a href="<?php echo U('Test/Test/removeDuplicate',array('DocID'=>$node['DocID']));?>">重复试题 -->
                                <!-- </a><br/> -->
                                <!-- <a href="<?php echo U('Doc/WlnDoc/viewtest',array('DocID'=>$node['DocID']));?>">试题预览</a><br/> -->
                            <?php else: ?>
                                <!-- <a href="<?php echo U('Test/Test/removeDuplicate',array('DocID'=>$node['DocID'],'in'=>1));?>">入库排重</a><br/> --><?php endif; ?>
                            <a href="<?php echo U('Test/Test/index',array('DocID'=>$node['DocID']));?>">审核试题</a><br/>
                            <a href="<?php echo U('Test/Test/introlist',array('DocID'=>$node['DocID'],'ShowWhere'=>'All'));?>">入库试题</a>
                            <!-- <a href="#" title='生成解析任务' class='createTask' docid="<?php echo ($node['DocID']); ?>">生成任务</a> -->
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
<script type='text/html' id='createTaskPanel'>
    <table border="1" cellspacing="0" cellpadding="5" class="list" style='width:600px;'>
        <tr>
            <td width='80' align="right">任务描述：</td>
            <td><textarea name="FileDescription" id="FileDescription" style='width:95%' rows="10">%Description%</textarea></td>
        </tr>
        <tr>
            <td align="right">是否审核：</td>
            <td>
                <input type="radio" name='CheckStatus' value='1' id='checkStatus1'/><label for="checkStatus1">需审核</label>
                <input type="radio" name='CheckStatus' value='2' checked='checked' id='checkStatus2'/><label for="checkStatus2">无需审核</label>
            </td>
        </tr>
        <tr>
            <td align="right">指定教师：</td>
            <td><input type="text" name='UserName' id='taskDistributeTeacher' readonly='readonly'></td>
        </tr>
        <tr>
            <td align="right">选择教师</td>
            <td>
                <iframe src='' width='95%' height="200" id='myfram'></iframe>
            </td>
        </tr>
        <tr>
            <td colspan="2" align='center'>
                <input type='hidden' id="taskSubjectId" value='%subjectid%'>
                <input type='hidden' name='docid' value='%docid%'>
                <div class="impBtn fLeft">
                    <input type="button" class="save imgButton saveDocFile" value="保存">
                </div>
            </td>
        </tr>
    </table>
</script>
<!-- 主页面结束 -->
<script>
//编辑
    if($('.bteditxls').length>0){
        $('.bteditxls').each(function(){
            $(this).click(function(){
                var keyValue = $(this).attr('thisid');
                if(!keyValue){
                    keyValue = getSelectCheckboxValue();
                }
                if(!keyValue){
                    alert('请选择编辑项！');
                    return false;
                }
                location.href =  U(URL+"/editxls?id="+keyValue);
            });
        });
    }
    //生成解析任务
    $('.createTask').live('click', function(){
        jInfo('加载中..', '加载数据');
        var that = $(this);
        var docid = that.attr('docid');
        $.get(U('Doc/WlnDoc/createTask?id='+docid), function(result){
            jClose();
            //权限验证
            if(checkPower(result)=='error'){
                return false;
            }
            var data = result['data'];
            var html = $('#createTaskPanel').html()
                        .replace('%Description%', data['des'])
                        .replace('%docid%', data['did'])
                        .replace('%subjectid%', data['sid']);
            jFrame(html, '创建解析任务');
        });
        return false;
    });
    $('#taskDistributeTeacher').live('click', function(){
        var subjectid = $('#taskSubjectId').val();
        var data = 's=4';
        if(subjectid!=''){
            data +='&subjectID='+subjectid;
            $('#myfram').attr('src',U('Doc/WlnDoc/teacher?'+data));
        }
    });
    $('.saveDocFile').live('click', function(){
        var that = $(this);
        var _parent = that.parents('table');
        var data = {
            'id' : _parent.find('input[name="docid"]').val(),
            'CheckStatus' : _parent.find('input[name="CheckStatus"]:checked').val(),
            'FileDescription' : _parent.find('#FileDescription').text(),
            'UserName' : _parent.find('input[name="UserName"]').val(),
        }
        if(!data['CheckStatus']){
            alert('是否审核不能为空！');
            return false;
        }
        if(!data['UserName']){
            alert('教师不能为空！');
            return false;
        }
        $.post(U('Doc/WlnDoc/saveTask'), data, function(result){
            //权限验证
            if(checkPower(result)=='error'){
                return false;
            }
            if(result['data'] == 'success'){
                alert('解析任务添加成功！');
                jClose();
            }
        });
    });
    $('.btcheck').live('click',function(){
        var keyValue = $(this).attr('thisid');
        if(!keyValue){
            var result='';
            $('.key').each(function(){
                if($(this).attr('checked')=='checked'){
                    result += $(this).val()+",";
                }
            });
            keyValue = result.substring(0, result.length-1);
        }
        if(!keyValue){
            alert('请选择审核项！');
            return false;
        }
        jInfo('审核中请稍候。。。','审核数据');
        $.post(U('Doc/WlnDoc/check'),{'id':keyValue,'times':Math.random()}, function(msg){
            jClose();
            //权限验证
            if(checkPower(msg)=='error'){
                return false;
            }
            var data=msg['data'];
            $('body').append(data);
        });
    });
    $('.btlock').live('click',function(){
        var keyValue = $(this).attr('thisid');
        if(!keyValue){
            var result='';
            $('.key').each(function(){
                if($(this).attr('checked')=='checked'){
                    result += $(this).val()+",";
                }
            });
            keyValue = result.substring(0, result.length-1);
        }
        if(!keyValue){
            alert('请选择锁定项！');
            return false;
        }
        jInfo('锁定中请稍候。。。','锁定数据');
        $.post(U('Doc/WlnDoc/check'),{'Status':1,'id':keyValue,'times':Math.random()},function(msg){
            jClose();
            //权限验证
            if(checkPower(msg)=='error'){
                return false;
            }
            var data=msg['data'];
            $('body').append(data);

        });
    });
$('#subject').subjectSelectChange('/Doc/WlnDoc',{'style':'getMoreData','list':'grade'});


//添加
    if($('.btaddex').length>0){
        $('.btaddex').click(function(){
            location.href  = U(URL+"/addex");
        });
    }
	//编辑
    if($('.bteditxls').length>0){
        $('.bteditxls').each(function(){
            $(this).click(function(){
                var keyValue = $(this).attr('thisid');
                if(!keyValue){
                    keyValue = getSelectCheckboxValue();
                }
                if(!keyValue){
                    alert('请选择编辑项！');
                    return false;
                }
                location.href =  U(URL+"/editxls?id="+keyValue);
            });
        });
    }
</script>

</body>
</html>