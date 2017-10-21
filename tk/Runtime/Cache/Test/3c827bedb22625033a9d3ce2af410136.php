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
var URL = '/Test/Test';
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
    <!--<div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="新增" onclick="" class="btadd add imgButton"></div>-->
    <!--<div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="编辑" onclick="" class="btedit edit imgButton"></div>-->
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="out" value="修改" onclick="" class="btout out imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="outAndDelete" value="移出" onclick="" class="btoutAndDelete outAndDelete imgButton"></div>
    <!--<div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>-->
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>

    <!-- 查询区域 -->
    <FORM METHOD="POST" ACTION="<?php echo U('Test/Test/introlist');?>">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="name" value="<?php echo ($_REQUEST['name']); ?>" title="试题ID查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR><TD class="tRight" width="80">查询方式：</TD>
            <TD colspan="2"><label><input name="searchStyle" class="searchStyle" type="radio" value="any" <?php if(($_REQUEST['searchStyle']== 'any') or ($_REQUEST['searchStyle']== '')): ?>checked="checked"<?php endif; ?>/> 模糊查询</label>
            <label><input name="searchStyle" class="searchStyle" type="radio" value="normal" <?php if($_REQUEST['searchStyle']== 'normal'): ?>checked="checked"<?php endif; ?>/> 精确查询</label>
            </TD>
            <TD class="tRight" width="80">查询字段：</TD>
            <TD colspan="2">
                <SELECT class="medium bLeft" NAME="field">
                <option value="">全部字段</option>
                    <option value="test" <?php if(($_REQUEST['field']) == "test"): ?>selected="selected"<?php endif; ?>>题文</option>
                    <option value="answer" <?php if(($_REQUEST['field']) == "answer"): ?>selected="selected"<?php endif; ?>>答案</option>
                    <option value="analytic" <?php if(($_REQUEST['field']) == "analytic"): ?>selected="selected"<?php endif; ?>>解析</option>
                    <option value="remark" <?php if(($_REQUEST['field']) == "remark"): ?>selected="selected"<?php endif; ?>>备注</option>
                    <option value="docname" <?php if(($_REQUEST['field']) == "docname"): ?>selected="selected"<?php endif; ?>>文档名称</option>
                </SELECT>
            </TD>
            <td colspan="3"></td>
        </TR>
        <TR><TD class="tRight" width="80">试题内容：</TD>
            <TD><INPUT TYPE="text" NAME="Test" class="medium" value="<?php echo ($_REQUEST['Test']); ?>" ></TD>
            <TD class="tRight" width="80">试题编号：</TD>
            <TD><INPUT TYPE="text" NAME="TestID" class="medium" value="<?php echo ($_REQUEST['TestID']); ?>" ></TD>
            <TD class="tRight" width="80">难度：</TD>
            <TD><SELECT class="medium bLeft" NAME="Diff">
            <option value="">选择</option>
            <?php if(is_array($diffArray)): $i = 0; $__LIST__ = $diffArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($_REQUEST['Diff']) == $node): ?>selected="selected"<?php endif; ?>><?php echo ($node[0]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            </SELECT></TD>
            <TD class="tRight" width="80">排序：</TD>
            <TD><SELECT class="medium bLeft" NAME="orderby">
            <option value="">选择</option>
            <option value="TestID ASC" <?php if(($_REQUEST['orderby']) == "TestID ASC"): ?>selected="selected"<?php endif; ?>>试题正序</option>
            <option value="TestID DESC" <?php if(($_REQUEST['orderby']) == "TestID DESC"): ?>selected="selected"<?php endif; ?>>试题倒序</option>
            <option value="LoadTime ASC" <?php if(($_REQUEST['orderby']) == "LoadTime ASC"): ?>selected="selected"<?php endif; ?>>时间正序</option>
            <option value="LoadTime DESC" <?php if(($_REQUEST['orderby']) == "LoadTime DESC"): ?>selected="selected"<?php endif; ?>>时间倒序</option>
            </SELECT></TD>
        </tr>
        <tr>
            <TD class="tRight" width="80">所属学科：</TD>
            <TD><SELECT id="searchsubject" class="medium bLeft" NAME="SubjectID" check='Require' warning="所属学科不能为空">
            <option value="">请选择</option>
            <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label='<?php echo ($vo["SubjectName"]); ?>'>
                <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == "Think.request.SubjectID"): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php else: ?>
                <option value="0">请添加学科</option><?php endif; ?>
            </SELECT></TD>
            <TD class="tRight" width="90">所属知识点：</TD>
            <TD colspan="5"><SELECT id="searchknowledge" class="medium bLeft selectKnowledge" NAME="KlID[]">
            <option value="">请选择</option>
            </SELECT></TD>
        </tr>
        <tr>
            <TD class="tRight" width="80">所属题型：</TD>
            <TD><SELECT id="searchtypes" class="medium bLeft" NAME="TypesID" check='Require' warning="所属题型不能为空">
            <option value="">请选择</option>
            </SELECT></TD>
            <!-- <TD class="tRight" width="90">所属专题：</TD>
            <TD><SELECT id="searchspecial" class="medium bLeft" NAME="SpecialID">
            <option value="">请选择</option>
            </SELECT></TD> -->
            <td class="tRight" width="90px">使用范围：</td>
                            <td>
                                <select class="medium bLeft" name="ShowWhere">
                                    <option value="">请选择</option>
                                    <option value="1" <?php if(($_REQUEST['ShowWhere']) == "1"): ?>selected="selected"<?php endif; ?>>通用</option>
                                    <option value="0" <?php if(($_REQUEST['ShowWhere']) == "0"): ?>selected="selected"<?php endif; ?>>组卷专用</option>
                                    <option value="2" <?php if(($_REQUEST['ShowWhere']) == "2"): ?>selected="selected"<?php endif; ?>>提分专用</option>
                                    <option value="3" <?php if(($_REQUEST['ShowWhere']) == "3"): ?>selected="selected"<?php endif; ?>>前台禁用</option>
                                </select>
                            </td>
        </TR>
        </TABLE>
    </div>
        </FORM>
</div>
<!-- 功能操作区域结束 -->
<div style="display:block;clear:both;">
    <pre>*友情提示：
    1、由于索引建立时间间隔所致，部分试题未能及时显示，请耐心等待
    2、【修改】前台显示，【移出】前台不显示</pre>
</div>
<!-- 列表显示区域  -->
<div class="list" >
<div id="result" class="result none"></div>
<!-- Think 系统列表组件开始 -->
<table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
    <tr><td height="5" colspan="8" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="8"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th width="30">编号</th>
        <th width="100">错误</th>
        <th width="450">试题</th>
        <th width="100">学科/题型/专题/难度/类型/年级/用户自评分值</th>
        <th width="100">知识点</th>
        <th width="100">章节</th>
        <th width="40">操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
        <td><input type="checkbox" class="key" value="<?php echo ($node["testid"]); ?>"></td>
        <td>
            <?php echo ($node["testid"]); ?>
            <?php if(($node["duplicate"]) == "0"): else: ?><a href="javascript:void(0)" class="showDuplicate" thisid="<?php echo ($node["duplicate"]); ?>"><font color="red">重复</font></a><br /><?php endif; ?></td>
        <td id="error<?php echo ($node["testid"]); ?>"><font color="red"><?php echo ((isset($node["error"]) && ($node["error"] !== ""))?($node["error"]):"<font color='black'>无</font>"); ?></font></td>
        <td width="450">
        <div>文档：<?php echo ($node["docid"]); ?>:<?php echo ($node["docname"]); ?></div>
        <div style="width:450px;overflow:auto;"><p><font color="blue">【题文】</font><?php echo ((isset($node["test"]) && ($node["test"] !== ""))?($node["test"]):"<font color='red'>无</font>"); ?>
        <p><font color="blue">【答案】</font><?php echo ((isset($node["answer"]) && ($node["answer"] !== ""))?($node["answer"]):"<font color='red'>无</font>"); ?>
        <p><font color="blue">【解析】</font><?php echo ((isset($node["analytic"]) && ($node["analytic"] !== ""))?($node["analytic"]):"<font color='red'>无</font>"); ?>
        <p><font color="blue">【备注】</font><?php echo ((isset($node["remark"]) && ($node["remark"] !== ""))?($node["remark"]):"<font color='red'>无</font>"); ?>
        </div></td>
        <td>
            <?php echo ((isset($node["subjectname"]) && ($node["subjectname"] !== ""))?($node["subjectname"]):"<font color='red'>无</font>"); ?><br/>
            <span id="types<?php echo ($node["testid"]); ?>"><?php echo ((isset($node["typesname"]) && ($node["typesname"] !== ""))?($node["typesname"]):"<font color='red'>无</font>"); ?></span><br/>
            <?php echo ((isset($node["specialname"]) && ($node["specialname"] !== ""))?($node["specialname"]):"<font color='red'>无</font>"); ?><br/>
            <span id='diff<?php echo ($node["testid"]); ?>'><?php echo ((isset($node["diff"]) && ($node["diff"] !== ""))?($node["diff"]):"<font color='red'>无</font>"); ?></span><br/>
            <span id="choose<?php echo ($node["testid"]); ?>"><?php if(($node["ifchoose"]) == "0"): ?>非选择题<?php endif; if(($node["ifchoose"]) == "1"): ?>复合题<?php endif; if(($node["ifchoose"]) == "2"): ?>多选题<?php endif; if(($node["ifchoose"]) == "3"): ?>单选题<?php endif; ?></span><br/>
            <span id="grade<?php echo ($node["testid"]); ?>"><?php echo ((isset($node["gradename"]) && ($node["gradename"] !== ""))?($node["gradename"]):"<font color='red'>无</font>"); ?></span><br>
            <span><?php echo ((isset($node["Score"]) && ($node["Score"] !== ""))?($node["Score"]):"未设置分值"); ?></span>
        </td>
        <td id="knowledge<?php echo ($node["testid"]); ?>"><?php echo ($node["klnameonly"]); ?></td>
        <td id="chapter<?php echo ($node["testid"]); ?>"><?php echo ($node["chapternameall"]); ?></td>
        <!--<td><?php echo ($node["klnameonly"]); ?></td>
        <td><?php echo ($node["chapternameall"]); ?></td>-->
        <td>
            <a href="javascript:void(0);" class="nowedit" thisid="<?php echo ($node["testid"]); ?>">修改</a>&nbsp;&nbsp;<br/>
        </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="8" class="bottomTd"></td></tr>
</table>
<!-- Think 系统列表组件结束 -->
</div>
<!--  分页显示区域 -->
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<form id="hiddenform" action="?" method="post" style="display:none">
<input name="id" id="testidlist" value=""/>
</form>
<script language="javascript">
$(document).ready(function(){
    $('.nowedit').live('click',function(){
            var a=$(this).attr('thisid');
            jInfo('加载中请稍候。。。','加载数据');
            //获取数据
            $.get(U('Test/Test/editIntro?id='+a+'&'+Math.random()), function(data){
                //权限验证
                if(checkPower(data)=='error'){
                    return false;
                }
                jFrame(data['data'],'编辑试题：编号'+a);
            });
    });
    $('.delhang').live('click',function(){
            $(this).parent().remove();
    });
    $('.delhang').live('mouseover',function(){
            $(this).css({'background-color':'#f00','color':'#fff'});
    });
    $('.delhang').live('mouseout',function(){
            $(this).css({'background-color':'#fff','color':'#f00'});
    });
    $('.xt_title').live('click',function(){
            var idx = $(this).attr('id').replace('xt','');
            changext(idx);
    });
    //通用添加数据
    function autoAddAttr(name,input,tag){
        if(name == 'Knowledge'){
            if($('.select'+name).last().val().indexOf(tag)==-1){
                    alert('请选择正确的知识点');
                    return false;
                }
        }
        

        var kid=$('.select'+name).last().val().replace(tag,'');
        var xx_s="";
        $('.select'+name).each(function(){
            xx_s+=' >> '+$(this).find("option:selected").text();
        });
        var xx=input.replace('#value#',kid).replace('#str#',xx_s.replace(/┉/g,'').replace('┝',''));
        var obj='#'+name.toLowerCase()+'List';
        if($(obj).html().indexOf('value="'+kid+'"')==-1 && $(obj).html().indexOf('value='+kid+'')==-1){
            $(obj).append(xx);
            $('.delhang').css({'padding':'0px 5px','cursor':'pointer','color':'#f00'});
        }
    }
    
    $('#addSkill').live('click',function(){
        autoAddAttr('Skill',inputskill,'t');
    });
    $('#addCapacity').live('click',function(){
        autoAddAttr('Capacity',inputcapacity,'t');
    });
    $('#addkl').live('click',function(){
        autoAddAttr('Knowledge',input,'t');
    });
    $('#addcp').live('click',function(){
        //if($('.chapter').last().val().indexOf('c')==-1){
        //    alert('请选择正确的数据');
        //    return false;
        //}
        if(!$('.selectChapter:eq(1)').val()){
            alert('请选择正确的数据');
            return false;
        }
        
        var cid=$('.selectChapter').last().val().replace('c','');
        var tmp_position=0;
        if(!cid){
            tmp_position=1;
            cid=$('.selectChapter').last().prev().val().replace('c','');
        }
        var xx_s="";
        $('.selectChapter').each(function(i){
            if(!(tmp_position==1 && $('.selectChapter').length==(i+1)))
            xx_s+=' >> '+$(this).find("option:selected").text();
        });
        var xx=inputcp.replace('#value#',cid).replace('#str#',xx_s.replace(/┉/g,'').replace('┝',''));
        if($('#chapterList').html().indexOf('value="'+cid+'"')==-1 && $('#chapterList').html().indexOf('value='+cid+'')==-1){
            $('#chapterList').append(xx);
            $('.delhang').css({'padding':'0px 5px','cursor':'pointer','color':'#f00'});
        }
    });
    
    /*切换选项卡*/
    function changext(idx){
        $('.xt_con').hide();
        $('.xt_con').addClass('none');
        $('.xt_con_'+idx).removeClass('none');
        $('.xt_con_'+idx).show();
        $('.xt_title').removeClass('xtcurrent');
        $('.xt_title').removeClass('xt');
        $('.xt_title').addClass('xt');
        $('#xt'+idx).addClass('xtcurrent');
    }
    
    var y=0;
    $('#getWidth').live('click',function(){
        y=1;
        var testid=$('#TestID').val();
        $.post(U('Test/Test/getOptionWidth'),{'id':testid,'style':1,'ifintro':2},function(msg){
            //权限验证
            if(checkPower(msg)=='error'){
                return false;
            }
            var data = msg['data'];
            if(data){
                var output=new Array();
                if(data.length==1){
                    output[0]='选项长度：'+data[0][1]+'<br/>选项数量：'+data[0][0];
                    $('.optionwidth:eq(0) input').val(data[0][1]);
                    $('.optionnum:eq(0) input').val(data[0][0]);
                }else{
                    for(var i in data){
                        output[i]='选项'+(parseInt(i)+1)+'长度：'+data[i][1]+'<br/>选项'+(parseInt(i)+1)+'数量：'+data[i][0];
                        $('.optionwidth:eq('+i+') input').val(data[i][1]);
                        $('.optionnum:eq('+i+') input').val(data[i][0]);
                    }
                }
                $('#widthCon').html(output.join('<br/>'));
            }else{
                alert('获取宽度失败！');
            }
            y=0;
        },'json');
    });
    var x=0;
    $('#datasave').live('click',function(){
        if(x){
            alert('正在提交请稍候。。。');
            return false;
        }
        if($('#types').val()==''){
            alert('请选择题型');
            $('#types').focus();
            return false;
        }
        var score = $('#score').val();
        if(score && /[^\d|,|(?=\d{1,2}\.\d{1})]/.test(score)){
            alert('分值必须为英文逗号分隔是数字或者纯数字');
            return false;
        }
        x=1;
        if(x){
            var testid=$('#TestID').val();
            var typesid=$('#types').val();
            var specialid=$('#special').val();
            var docid=$('#DocID').val();
            var choose='';
            if($('.choose').length>0){
                $('.choose').each(function(){
                    if($(this).attr('checked')=='checked'){
                        choose = $(this).val();
                    }
                });
            }
            
            var kl='';
            if($('.kl').length>0){
                $('.kl').each(function(){
                        kl += $(this).val()+",";
                });
                kl=kl.substring(0, kl.length-1);
            }
            var skill='';
                if($('.skill').length>0){
                    $('.skill').each(function(){
                        skill += $(this).val()+",";
                    });
                    skill=skill.substring(0, skill.length-1);
                }
                var capacity='';
                if($('.capacity').length>0){
                    $('.capacity').each(function(){
                        capacity += $(this).val()+",";
                    });
                    capacity=capacity.substring(0, capacity.length-1);
                }
            var cp='';
            if($('.cp').length>0){
                $('.cp').each(function(){
                        cp += $(this).val()+",";
                });
                cp=cp.substring(0, cp.length-1);
            }
            var mark='';
            if($('.mark').length>0){
                $('.mark').each(function(){
                        mark += $(this).val()+",";
                });
                mark=mark.substring(0, mark.length-1);
            }
            var chooselist='';
            var optionwidth='';
            var optionnum='';
            var choose_arr=new Array();
            var result;
            var optionwidtharr=new Array();
            var optionnumarr=new Array();
            var remark=$('#Remark').val();
            //复合题
            if(choose==1){
                for(var ii=1;ii<50;ii++){
                    result=-1;
                    $('.choose'+ii).each(function(){
                        if($(this).attr('checked')=='checked'){
                            result = $(this).val();
                        }
                    });
                    if(result==-1) break;
                    optionwidtharr[ii-1]=$('.optionwidth'+ii).val();
                    optionnumarr[ii-1]=$('.optionnum'+ii).val();
                    choose_arr[ii-1]=result;
                }
                chooselist=choose_arr.join(',');
                optionwidth=optionwidtharr.join(',');
                optionnum=optionnumarr.join(',');
            }else{
                optionwidth=$('.optionwidth1').val();
                optionnum=$('.optionnum1').val();
            }
            var status='';
            if($('.status').length>0){
                $('.status').each(function(){
                    if($(this).attr('checked')=='checked'){
                        status = $(this).val();
                    }
                });
            }

            
            var dfstyle='';
            if($('.DfStyle').length>0){
                $('.DfStyle').each(function(){
                    if($(this).attr('checked')=='checked'){
                        dfstyle = $(this).val();
                    }
                });
            }
            
            var diff=$('#Diff').val();
            //提交数据
            $.ajax({
               type: "POST",
               cache: false,
               url: U('Test/Test/saveIntro'),
               data: "TestID="+testid+"&TypesID="+typesid+"&skill="+skill+"&capacity="+capacity+"&kl="+kl+"&cp="+cp+"&SpecialID="+specialid+"&DocID="+docid+"&Mark="+mark+"&Status="+status+"&Remark="+remark+"&DfStyle="+dfstyle+"&Diff="+diff+"&IfChoose="+choose+"&ChooseList="+chooselist+"&OptionWidth="+optionwidth+"&OptionNum="+optionnum+'&Score='+score,
               success: function(msg){
                //权限验证
                if(checkPower(msg)=='error'){
                    return false;
                }
                msg = msg['data'];
                    x=0;
                 $('#popup_container').append( msg );
                 $('#popup_container').remove();
                 $("#popup_overlay").remove();
               },
               error: function(XMLHttpRequest, textStatus, errorThrown){
                 x=0;
                 alert( "保存数据失败！请重试。" );
               }
            });
        }
    });
    $('.showDuplicate').live('click',function(){
        var a=$(this).attr('thisid');
        jInfo('加载中请稍候。。。','加载数据');
        //获取数据
        $.post(U('Test/Test/duplicateList'),{'duplicate':a,'in':1,'times':Math.random()}, function(data){
            jClose();
            //权限验证
            if(checkPower(data)=='error'){
                return false;
            }
            jFrame(data['data'],'清除重复');
        },'json');
    })

    //客观主观切换
    $('#kg').live('click',function(){
        $('.zgdf').hide();
        $('.kgdf').show();
        changext(1);
    });
    $('#zg').live('click',function(){
        $('.kgdf').hide();
        $('.zgdf').show();
    });
    
    /*切换选项卡*/
    function changext(idx){
        $('.xt_con').hide();
        $('.xt_con').addClass('none');
        $('.xt_con_'+idx).removeClass('none');
        $('.xt_con_'+idx).show();
        $('.xt_title').removeClass('xtcurrent');
        $('.xt_title').removeClass('xt');
        $('.xt_title').addClass('xt');
        $('#xt'+idx).addClass('xtcurrent');
    }
});
    //载入默认章节
    $('#adddcp').live('click',function(){
        var result='';
        $('.kl').each(function(){
            result += $(this).val()+",";
        });
        var kl=result.substring(0, result.length-1);
        var testid=$('#TestID').val();
        $.get(U('Test/Test/getchapter?kl='+kl+'&id='+testid+'&'+Math.random()),function(msg){
            //权限验证
            if(checkPower(msg)=='error'){
                return false;
            }
            var data = msg['data'];
            if(data){
                var flag=0;
                for(var i=0;i<data.length;i++){
                    var xx=inputcp.replace('#value#',data[i]['ChapterID']).replace('#str#',data[i]['ChapterName']);
                    if($('.cpinput').html().indexOf('value="'+data[i]['ChapterID']+'"')==-1 && $('.cpinput').html().indexOf("value='"+data[i]['ChapterID']+"'")==-1 && $('.cpinput').html().indexOf('value='+data[i]['ChapterID']+' ')==-1){
                        $('.cpinput').append(xx);
                        flag=1;
                    }
                }
                $('.delhang').css({'padding':'0px 5px','cursor':'pointer','color':'#f00'});
                if(!flag){
                    alert('默认章节已经全部载入！');
                }
            }else{
                alert('暂无对应章节！');
            }
        },'json');
    });
        //搜索知识点
    $('.searchknowledge').live('change',function(){
        var s=$('#subject').val();
        $(this).nextAll(".searchknowledge").remove();
        var tt=$(this);
        if(tt.val()=='') return;
        $.get(U('Test/Test/getdata?s='+s+'&l=k&pid='+tt.val()+'&'+Math.random()),function(msg){
            //权限验证
            if(checkPower(msg)=='error'){
                return false;
            }
            var data = msg['data'];
            output='';
            if(data[0]){
                output+='<option value="">'+data[2]+'</option>';
                for(datan in data[1]){
                    if(data[1][datan]['Last']==1) output+='<option value="t'+data[1][datan]['KlID']+'">'+data[1][datan]['KlName']+'</option>';
                    else output+='<option value="'+data[1][datan]['KlID']+'">'+data[1][datan]['KlName']+'</option>';
                }
                tt.after('<select class="medium searchknowledge" '+data[3]+' name="KlID[]">'+output+'</select>');
            }
        },'json');
    });
    $('.selectChapter').chapterSelectChange('/Test/Test');
    $('#searchsubject').subjectSelectChange('/Test/Test',{'style':'getMoreData','list':'knowledge,types,special','search':'search'});
    $('.selectKnowledge').knowledgeSelectChange('/Test/Test');
</script>
<!-- 主页面结束 -->

</body>
</html>