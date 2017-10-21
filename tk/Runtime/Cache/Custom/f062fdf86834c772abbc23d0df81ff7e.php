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
var URL = '/Custom/CustomTestCopy';
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
<script language="javascript">
$(document).ready(function(){
    $('.nowedit').live('click',function(){
            var a=$(this).attr('thisid');
            jInfo('加载中请稍候。。。','加载数据');
            //获取数据
            $.get(U('Custom/CustomTestCopy/edit?id='+a+'&'+Math.random()), function(data){
                jClose();
                //权限验证
                if(checkPower(data)=='error'){
                    return false;
                }
                jFrame(data['data'],'编辑试题：编号'+a);
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
        $.post(U('Custom/CustomTestCopy/check'),{'id':keyValue,'m':Math.random()}, function(data){
                jClose();
                //权限验证
                if(checkPower(data)=='error'){
                    return false;
                }
                $('body').append(data['data']);
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
        $.post(U('Custom/CustomTestCopy/check'),{'id':keyValue,'Status':1,'m':Math.random()}, function(data){
            jClose();
            //权限验证
            if(checkPower(data)=='error'){
                return false;
            }
            $('body').append(data['data']);
        });
    });
    $('.selectChapter').chapterSelectChange('/Custom/CustomTestCopy');
    $('.selectKnowledge').knowledgeSelectChange('/Custom/CustomTestCopy');

    $('#addkl').live('click',function(){
        if($('.selectKnowledge').last().val().indexOf('t')==-1){
            alert('请选择正确的知识点');
            return false;
        }
        
        var kid=$('.selectKnowledge').last().val().replace('t','');
        var xx_s="";
        $('.selectKnowledge').each(function(){
            xx_s+=' >> '+$(this).find("option:selected").text();
        });
        var xx=input.replace('#value#',kid).replace('#str#',xx_s.replace(/┉/g,'').replace('┝',''));
        
        
        if($('#knowledgeList').html().indexOf('value="'+kid+'"')==-1 && $('#knowledgeList').html().indexOf('value='+kid+'')==-1){
            $('#knowledgeList').append(xx);
            $('.delhang').css({'padding':'0px 5px','cursor':'pointer','color':'#f00'});
        }
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
    //载入默认章节
    $('#adddcp').live('click',function(){
        var result='';
        $('.kl').each(function(){
            result += $(this).val()+",";
        });
        var kl=result.substring(0, result.length-1);
        var testid=$('#TestID').val();
        $.get(U('Custom/CustomTestCopy/getchapter?kl='+kl+'&id='+testid+'&'+Math.random()),function(msg){
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
    
    $('#Diff').live('keydown',function(e){
        if(e.keyCode==13){
           $('#datasave').click(); //处理事件
        }
    }); 
    var y=0;
    $('#getWidth').live('click',function(){
        y=1;
        var testid=$('#TestID').val();
        $.post(U('Custom/CustomTestCopy/getOptionWidth'),{'id':testid,'style':1,'m':Math.random()},function(msg){
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
    var sdata=0;
    $('#datasave').live('click',function(){
        if(x){
            alert('正在提交请稍候。。。');
            return false;
        }
        sdata=0;
        if($('#types').val()==''){
            alert('请选择题型');
            $('#types').focus();
            return false;
        }
        /*if($('#klinput').html()==''){
            alert('请添加知识点');
            $('#knowledge').focus();
            return false;
        }*/
        //主观客观打分
        if($('#kg').attr('checked')=='checked'){
            var fs=0;
            var tmp_arr=new Array();
            var xttimes=parseInt($(".mark").length)/parseInt($('#xttimes').val());
            $(".mark").each(function(i){
                if($(this).val()){
                    tmp_arr=$(this).val().split("|");
                    if(tmp_arr[1]>-1 && tmp_arr[1]<4) fs+=parseInt(tmp_arr[1]);
                }
                if((i+1)%xttimes==0 && i!=0){
                    if(fs<4 || fs>12){
                        alert('请选择正确的打分数据');
                        sdata=1;
                        return false;
                    }
                    fs=0;
                }
            });
        }else{
            var xsdiff=$('#Diff').val();
            if(xsdiff<0 || xsdiff>=1){
                    alert('请填入正确的难度值');
                    $('#Diff').focus();
                    return false;
            }
        }
        
        if(sdata){
            return false;
        }
        x=1;
        if(x){
            var testid=$('#TestID').val();
            var typesid=$('#types').val();
            
            var kl='';
            if($('.kl').length>0){
                $('.kl').each(function(){
                        kl += $(this).val()+",";
                });
                kl=kl.substring(0, kl.length-1);
            }
            var cp='';
            if($('.cp').length>0){
                $('.cp').each(function(){
                        cp += $(this).val()+",";
                });
                cp=cp.substring(0, cp.length-1);
            }
            
            var specialid=$('#special').val();
            var docid=$('#DocID').val();
            
            var mark='';
            if($('.mark').length>0){
                $('.mark').each(function(){
                        mark += $(this).val()+",";
                });
                mark=mark.substring(0, mark.length-1);
            }
            
            var choose='';
            if($('.choose').length>0){
                $('.choose').each(function(){
                    if($(this).attr('checked')=='checked'){
                        choose = $(this).val();
                    }
                });
            }
            var chooselist='';
            var optionwidth='';
            var optionnum='';
            var choose_arr=new Array();
            var result;
            var optionwidtharr=new Array();
            var optionnumarr=new Array();
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
            var remark=$('#Remark').val();
            
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
               url: U('Custom/CustomTestCopy/save'),
               data: "TestID="+testid+"&TypesID="+typesid+"&kl="+kl+"&cp="+cp+"&SpecialID="+specialid+"&DocID="+docid+"&Mark="+mark+"&Status="+status+"&Remark="+remark+"&DfStyle="+dfstyle+"&Diff="+diff+'&IfChoose='+choose+'&ChooseList='+chooselist+'&OptionWidth='+optionwidth+'&OptionNum='+optionnum,
               success: function(msg){
                //权限验证
                x=0;
                if(checkPower(msg)=='error'){
                    return false;
                }
                msg = msg['data'];
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
    //获取与之相关的重复试题列表
    $('.showDuplicate').live('click',function(){
        var a=$(this).attr('thisid');
        jInfo('加载中请稍候。。。','加载数据');
        //获取数据
        $.post(U('Custom/CustomTestCopy/duplicateList'),{'duplicate':a,'times':Math.random()}, function(data){
            jClose();
            //权限验证
            if(checkPower(data)=='error'){
                return false;
            }
            jFrame(data['data'],'清除重复');
        });
    });
});
</script>
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content" >
<div class="title"><?php echo ($pageName); ?> </div>
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
    <!-- 查询区域 -->
    <FORM METHOD="POST" ACTION="/Custom/CustomTestCopy">
    <div class="fRig">
        <div class="fLeft"><span id="key"><INPUT id="name" TYPE="text" NAME="name" value="<?php echo ($_REQUEST['name']); ?>" title="试题编号查询" class="medium" ></span></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
    </div>
    <!-- 高级查询区域 -->
    <div id="searchM" class=" none search cBoth">
        <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
        <TR><TD class="tRight" width="80">试题编号：</TD>
            <TD><INPUT TYPE="text" NAME="TestID" class="small" value="<?php echo ($_REQUEST['TestID']); ?>" ></TD>
            <TD class="tRight" width="80">所属学科：</TD>
            <TD><SELECT id="subject" class="w90px bLeft" NAME="SubjectID" check='Require' warning="所属学科不能为空">
            <option value="">请选择</option>
            <?php if($subjectArray): if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label='<?php echo ($vo["SubjectName"]); ?>'>
                <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["SubjectID"]); ?>" <?php if(($item["SubjectID"]) == $edit["SubjectID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php else: ?>
                <option value="0">请添加学科</option><?php endif; ?>
            </SELECT></TD><TD class="tRight" width="50">状态：</TD>
            <TD><SELECT class="w90px bLeft" NAME="Status">
            <option value="">—请选择—</option>
            <?php if(is_array($testStatus)): $i = 0; $__LIST__ = $testStatus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            </SELECT></TD>
        </tr>
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
    <tr><td height="5" colspan="8" class="topTd" ></td></tr>
    <tr class="row" style='text-align:center'>
        <th width="40">编号</th>
        <th width="350">试题内容</th>
        <th width="50">试题状态</th>
        <th width="200">学科/题型/专题/类型/难度/年级/用户名</th>
        <th width="60">添加时间</th>
        <th width="45">操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
        <td>
        <?php echo ($node["TestID"]); ?>
        </td>
        <td width="400">
            <div class="text_source">来源：<a href="" title=""><?php echo ($node["Source"]); ?></a></div>
            <div class="testdivbak" style="height:120px; width:500px; overflow-Y:scroll">
                <a href="javascript:void(0);" class="" thisid="<?php echo ($node["TestID"]); ?>"><p><?php echo ((isset($node["Test"]) && ($node["Test"] !== ""))?($node["Test"]):"无</p>"); ?></a>
            </div>
        </td>
        <td id="error<?php echo ($node["TestID"]); ?>"><font color="red"><?php echo ($node["Status"]); ?></font></td>
        <td><?php echo ((isset($node["SubjectName"]) && ($node["SubjectName"] !== ""))?($node["SubjectName"]):"<font color='red'>无</font>"); ?><br/>
            <span id="types<?php echo ($node["TestID"]); ?>"><?php echo ((isset($node["TypesName"]) && ($node["TypesName"] !== ""))?($node["TypesName"]):"<font color='red'>无</font>"); ?></span><br/>
            <span id="special<?php echo ($node["TestID"]); ?>"><?php echo ((isset($node["SpecialName"]) && ($node["SpecialName"] !== ""))?($node["SpecialName"]):"<font color='red'>无</font>"); ?></span><br/>
            <span id="choose<?php echo ($node["TestID"]); ?>"><?php if(($node["IfChoose"]) == "0"): ?>非选择题<?php endif; if(($node["IfChoose"]) == "1"): ?>复合题<?php endif; if(($node["IfChoose"]) == "2"): ?>多选题<?php endif; if(($node["IfChoose"]) == "3"): ?>单选题<?php endif; ?></span><br/>
            <span id="diff<?php echo ($node["TestID"]); ?>"><?php echo ((isset($node["Diff"]) && ($node["Diff"] !== ""))?($node["Diff"]):"<font color='red'>无</font>"); ?></span><br/>
            <span id="grade<?php echo ($node["TestID"]); ?>"><?php echo ((isset($node["SubjectName"]) && ($node["SubjectName"] !== ""))?($node["SubjectName"]):"<font color='red'>无</font>"); ?>【<?php echo ((isset($node["GradeName"]) && ($node["GradeName"] !== ""))?($node["GradeName"]):"<font color='red'>无</font>"); ?>】</span><br>
            <span >【<?php echo ($node["UserName"]); ?>】</span>
        </td>
        <td id="knowledge<?php echo ($node["TestID"]); ?>"><?php echo (date("Y-m-d H:i:s",$node["AddTime"])); ?></td>
        <td><a href="<?php echo U('Custom/CustomTestCopy/showMsg',array('id'=>$node[TestID]));?>" class="showmsg" thisid="<?php echo ($node["PaperID"]); ?>">查看详情</a>&nbsp;</td>
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
<!-- 主页面结束 -->

</body>
</html>