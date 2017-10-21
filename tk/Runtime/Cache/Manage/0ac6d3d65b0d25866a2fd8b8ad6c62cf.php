<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
var URL = '/Manage/ProcError';
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
    $(document).ready(function () {
        //选择正则
        $('#preg').change(function(){
            var start = $(this).children('option:selected').attr('data');
            $('#start').val(start);
        });
        //搜索
        $('#search').click(function(){
            var self = $(this);
            var load = $('#searchLoad');
            var url = $(this).attr('url');
            var preg = $('#preg').val();
            var subject = $('#subject').val();
            var start = $('#start').val();
            var size = $('#size').val();
            if(!preg||!subject||!start||!size){
                alert('请填写完整搜索条件！');
                return false;
            }
            if(start<1){
                alert('至少从第 1 题开始！');
                return false;
            }
            if(size>50){
                alert('每次搜索数量最大50！');
                return false;
            }
            self.hide();//禁用
            load.show();
            $.post(url,{start:start,size:size,subject:subject,preg:preg},function(e){
                //权限验证
                var c = '';
                if(checkPower(e)=='error'){
                    //出现错误
                    c += '<tr class="row tmp">';
                    c += '<td colspan="4" style="text-align: center;color:#FF0000;">'+ e.data +'</td>'
                    c += '</tr>';
                    $('.tmp').remove();
                    $('#head').after(c);
                    load.hide();
                    self.show();
                    return false;
                }
                if(e['data'][0] == 'success'){
                    //搜索到匹配数据
                    $.each(e['data'][1],function(i,k){
                        var id = k.testID;
                        var hr = '<hr style="border-top: 1px dashed;" />';
                        var content = (k.test?k.test:'')+ (k.answer?k.answer:'')+ (k.analytic?k.analytic:'')+ (k.remark?k.remark:'');
                        var docContent = (k.docTest?k.docTest:'')+ (k.docAnswer?k.docAnswer:'')+ (k.docAnalytic?k.docAnalytic:'')+ (k.docRemark?k.docRemark:'');
                        var newContent = (k.newTest?k.newTest:'')+ (k.newAnswer?k.newAnswer:'')+ (k.newAnalytic?k.newAnalytic:'')+ (k.newRemark?k.newRemark:'');
                        var newDocContent = (k.newDocTest?k.newDocTest:'')+ (k.newDocAnswer?k.newDocAnswer:'')+ (k.newDocAnalytic?k.newDocAnalytic:'')+ (k.newDocRemark?k.newDocRemark:'');
                        var leftContent = (content?content:'zj_test_real表数据无匹配')+hr+(docContent?docContent:'zj_test_doc表数据无匹配');
                        var rightContent = (newContent?newContent:'zj_test_real表数据无匹配')+hr+ (newDocContent?newDocContent:'zj_test_doc表数据无匹配');

                        c += '<tr class="row tmp tr" down="0" id="test-'+id+'">';
                        c += '<td style="vertical-align: middle;text-align: center;"><input type="checkbox" class="cBox" value="'+ id +'"></td>';
                        c += '<td style="text-align: center;">'+ id +'</td>';
                        //显示原始信息
                        c += '<td style="color: #CCCCCC;">'+ leftContent +'</td>';
                        //显示新信息
                        c += '<td style="color: #CCCCCC;">'+ rightContent +'</td>'
                        c += '</tr>';
                    });
                    $('#start').val(parseInt(start)+parseInt(size));//累加开始题目序号
                }else if(e['data'][0] == 'error'){
                    //此页中没有匹配数据
                    c += '<tr class="row tmp">';
                    c += '<td colspan="4" style="text-align: center;">'+ e['data'][1] +'</td>'
                    c += '</tr>';
                    $('#start').val(parseInt(start)+parseInt(size));//累加开始题目序号
                    //如果设置了自动搜索，则触发下一次的搜索
                    var check = $('#auto').attr('checked');
                    if(check == 'checked'){
                        setTimeout(function(){
                            self.click();
                        },1000);
                    }
                }
                $('.tmp').remove();
                c = c.replace(/!@#/g,'<em>');
                c = c.replace(/#@!/g,'</em>');
                $('#head').after(c);
                load.hide();
                self.show();
            });

        });
        //全选
        $('#checkAll').click(function () {
            var row = $('.list .tr');
            if ($('#checkAll').attr('checked')) {
                $('.cBox').attr('checked', true);
                row.css('background-color','#CF9').attr('down',1);
            } else {
                $('.cBox').attr('checked', false);
                row.css('background-color','#FFF').attr('down',0);
            }
        });
        //单个checkbox事件
        $('.list').on('click','.tr',function(){
            var self = $(this);
            var cBox = $(this).find('.cBox');
            var cBoxStatus = self.attr('down');
            if(cBoxStatus == 1){
                //取消选择
                cBox.attr('checked',false);
                self.attr('down',0);
                self.css('background-color','#FFF');
            }else{
                //选择
                cBox.attr('checked',true);
                self.attr('down',1);
                self.css('background-color','#CF9');
            }
            if (!cBox.checked) {
                $('#checkAll').attr('checked', false);
            }
            var count = $('.cBox').length; //获取总数
            var checkCount = $('.cBox:checked').length; //获取选中个数
            if (count == checkCount) {
                $('#checkAll').attr('checked', true);
            }
        });
        $('.list').on({
            mouseover:function(){
                $(this).css('background-color','#CFC');
            },
            mouseout:function(){
                var cBoxStatus = $(this).find('.cBox').attr('checked');
                if(cBoxStatus == 'checked'){
                    $(this).css('background-color','#CF9');
                }else{
                    $(this).css('background-color','#FFF');
                }
            }
        },'.tr');
        //替换
        $('#replace').click(function(){
            var self = $(this);
            var load = $('#replaceLoad');
            if(!$('.cBox:checked').length){
                alert('请选择替换的试题！');
                return false;
            }
            var testID = [];
            $('.cBox:checked').each(function(){
                testID.push($(this).val());
            });
            var url = self.attr('url');
            var preg = $('#preg').val();
            if(!preg){
                alert('选择正则重新搜索后替换！');
                return false;
            }
            self.hide();
            load.show();
            $.post(url,{testID:testID,'preg':preg},function(e){
                //权限验证
                if(checkPower(e)=='error'){
                    return false;
                }
                    if(e['data']['errorID'].length){
                        //如果有错误的,错误列标红
                        var errorID = '';
                        $.each(e.data.errorID,function(i,k){
                            errorID += k+' ';
                            $('#test-'+k).css('color','red');
                        });
                        alert('试题编号为：'+errorID+'的试题替换出错，编号已经标红，请重试！');
                    }
                    if(e.data.successID){
                        //已经替换的移除
                        $.each(e.data.successID,function(i,k){
                            $('#test-'+k).remove();
                        });
                    }
                load.hide();
                self.show();
            });
        });
        //搜索指定TestID的试题
        $('#getNo').click(function(){
            var url = $(this).attr('url');
            var testID = $('#testID').val();
            var subject = $('#subject').val();
            var preg = $('#preg').val();
            if(!subject||!preg){
                alert('请先选择学科和正则！');
                return false;
            }
            var pregInt = /^[1-9]\d*$/;
            if(!pregInt.test(testID)){
                alert('请正确填写要搜索的试题编号！');
                return false;
            }
            $.post(url,{testID:testID,subject:subject},function(e){
                //权限验证
                if(checkPower(e)=='error'){
                    return false;
                }
                    $('#start').val(e.data);
                    $('#search').click();
            });
        });
        //切换模式
        $('.changeModel').live('click',function(){
            if($(this).val()=='normal'){
                $('.single').css({'display':'none'});
                $('.normal').css({'display':'block'});
            }else if($(this).val()=='single'){
                $('.single').css({'display':'block'});
                $('.normal').css({'display':'none'});
            }
        });
        //单题替换之查看
        $('.sShow').live('click',function(){
            var testID=$('.sTestID').val();
            if(testID=='' || testID<0){
                alert('请填写正确的试题id');
                $('.sTestID').focus();
                return false;
            }
            $.post('<?php echo U("ProcError/getSingleTest");?>',{'testID':testID,'times':Math.random()},function(data){
                //权限验证
                if(checkPower(data)=='error'){
                    return false;
                }
                var c='';
                if(data.data){
                    var leftContent='';
                    var rightContent='';
                    leftContent='【题文】'+data['data']['Test'];
                    leftContent+='<br/>【答案】'+data['data']['Answer'];
                    leftContent+='<br/>【解析】'+data['data']['Analytic'];
                    leftContent+='<br/>【备注】'+data['data']['Remark'];
                    leftContent+='<hr style="border:1px dashed #ccc;"/>';
                    leftContent+='【题文】'+data['data']['DocTest'];
                    leftContent+='<br/>【答案】'+data['data']['DocAnswer'];
                    leftContent+='<br/>【解析】'+data['data']['DocAnalytic'];
                    leftContent+='<br/>【备注】'+data['data']['DocRemark'];
                    c += setNewLine(testID,leftContent,rightContent);
                }else{
                    //此页中没有匹配数据
                    c += setOneLine('没有找到试题');
                }
                $('.tmp').remove();
                $('#head').after(c);
            });
        });
        $('.sLook').live('click',function(){
            $('.sReplace').attr('flag',0);
            var testID=$('.sTestID').val();
            if(testID=='' || testID<0){
                alert('请填写正确的试题id');
                $('.sTestID').focus();
                return false;
            }
            var oldStr=$('.sOld').val();
            if(oldStr==''){
                alert('请填写需要替换的数据');
                $('.sOld').focus();
                return false;
            }
            var replaceStr=$('.sReplace').val();
            $.post('<?php echo U("ProcError/replaceSingleTest");?>',{'testID':testID,'oldStr':oldStr,'replaceStr':replaceStr,'flag':0,'times':Math.random()},function(data){
                //权限验证
                if(checkPower(data)=='error'){
                    return false;
                }
                var c='';
                if(data.data){
                    var leftContent='';
                    var rightContent='';
                    leftContent='【题文】'+data['data']['Test'];
                    leftContent+='<br/>【答案】'+data['data']['Answer'];
                    leftContent+='<br/>【解析】'+data['data']['Analytic'];
                    leftContent+='<br/>【备注】'+data['data']['Remark'];
                    leftContent+='<hr style="border:1px dashed #ccc;"/>';
                    leftContent+='【题文】'+data['data']['DocTest'];
                    leftContent+='<br/>【答案】'+data['data']['DocAnswer'];
                    leftContent+='<br/>【解析】'+data['data']['DocAnalytic'];
                    leftContent+='<br/>【备注】'+data['data']['DocRemark'];
                    rightContent='【题文】'+data['data']['NewTest'];
                    rightContent+='<br/>【答案】'+data['data']['NewAnswer'];
                    rightContent+='<br/>【解析】'+data['data']['NewAnalytic'];
                    rightContent+='<br/>【备注】'+data['data']['NewRemark'];
                    rightContent+='<hr style="border:1px dashed #ccc;"/>';
                    rightContent+='【题文】'+data['data']['NewDocTest'];
                    rightContent+='<br/>【答案】'+data['data']['NewDocAnswer'];
                    rightContent+='<br/>【解析】'+data['data']['NewDocAnalytic'];
                    rightContent+='<br/>【备注】'+data['data']['NewDocRemark'];
                    c += setNewLine(testID,leftContent,rightContent);
                    $('.sReplace').attr('flag',1);
                    $('.sDo').attr('oldStr',oldStr);
                    $('.sDo').attr('replaceStr',replaceStr);
                }else{
                    //此页中没有匹配数据
                    c += setOneLine('没有找到试题');
                }

                c = c.replace(/!@#/g,'<em>');
                c = c.replace(/#@!/g,'</em>');
                $('.tmp').remove();
                $('#head').after(c);
            },'json');
        });
        $('.sDo').live('click',function(){
            if(!confirm('是否确认要替换')){
                return false;
            }
            if($('.sReplace').attr('flag')==0){
                alert('请先预览后替换！');
                return false;
            }
            var oldStr=$('.sOld').val();
            var replaceStr=$('.sReplace').val();

            if($(this).attr('oldStr')!=oldStr || $(this).attr('replaceStr')!=replaceStr){
                alert('请先预览后替换！');
                return false;
            }
            $('.sReplace').attr('flag',0);
            var testID=$('.sTestID').val();
            if(testID=='' || testID<0){
                alert('请填写正确的试题id');
                $('.sTestID').focus();
                return false;
            }
            var oldStr=$('.sOld').val();
            if(oldStr==''){
                alert('请填写需要替换的数据');
                $('.sOld').focus();
                return false;
            }
            var replaceStr=$('.sReplace').val();
            $.post('<?php echo U("ProcError/replaceSingleTest");?>',{'testID':testID,'oldStr':oldStr,'replaceStr':replaceStr,'flag':1,'times':Math.random()},function(data){
                //权限验证
                if(checkPower(data)=='error'){
                    return false;
                }
                c='';
                if(data.data){
                }else{
                    //此页中没有匹配数据
                    c += setOneLine('没有找到试题');
                }
                $('.tmp').remove();
                $('#head').after(c);
            },'json');
        });
    });
    //返回一行数据
    function setNewLine(testID,leftContent,rightContent){
        return '<tr class="row tmp tr" id="test-'+testID+'">'+
                '<td style="vertical-align: middle;text-align: center;"><input type="checkbox" class="cBox" value="'+ testID +'"></td>'+
                '<td style="text-align: center;">'+ testID +'</td>'+
            //显示原始信息
                '<td style="color: #999;">'+ leftContent +'</td>'+
            //显示新信息
                '<td style="color: #999;">'+ rightContent +'</td>'+
                '</tr>';
    }
    //返回一行提示数据
    function setOneLine(str){
        return '<tr class="row tmp"><td colspan="4" style="text-align: center;">'+str+'</td></tr>';
    }

</script>
<!-- 主页面开始 -->
<div id="main" class="main">
    <!-- 主体内容  -->
    <div class="content">
        <div class="title"><?php echo ($pageName); ?>
            [<a href="<?php echo U('ProcError/pregList');?>" id="editPreg">正则管理</a>]
            <label><input id="normal" class="changeModel" name="changeModel" type="radio" value="normal" checked="checked"/>多题处理</label>
            <label><input id="single" class="changeModel" name="changeModel" type="radio" value="single"/>单题处理</label>
        </div>
        <!--  功能操作区域  -->
        <div class="operate normal">
            <!-- 查询区域 -->
            <div class="impBtn hMargin fLeft shadow">
                <input id="replace" type="button" class="intro imgButton" value="替换" url="<?php echo U('ProcError/replaceTest');?>">
                <input id="replaceLoad" class="imgButton" type="button" value="稍等" style="display: none;cursor: default;" />
            </div>
            <div class="impBtn hMargin fLeft shadow">
                <input id="search" type="button" class="search imgButton" value="搜索" url="<?php echo U('ProcError/searchTest');?>">
                <input id="searchLoad" class="imgButton" type="button" value="稍等" style="display: none;cursor: default;" />
            </div>
            <div class="fLeft">
                <input id="auto" type="checkbox" value="1" style="vertical-align: middle" /> <label for="auto">自动</label>
                第
                <input type="text" class="small" title="TestID" value="" id="start">
                题开始的
                <input type="text" class="small" title="数量" value="50"  id="size">
                道题目
                学科：
                <select id="subject" class="medium bLeft">
                    <option value="" data="">请选择</option>
                    <?php if(is_array($subject)): $i = 0; $__LIST__ = $subject;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo["readOnly"] == true): ?><option value="" disabled style="text-align: center;"><?php echo ($vo["name"]); ?></option>
                    <?php else: ?>
                    <option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </select>
                处理正则：
                <select id="preg" class="medium bLeft">
                    <option value="">请选择</option>
                    <?php if(is_array($preg)): $i = 0; $__LIST__ = $preg;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo[PregID]); ?>" data="<?php echo ($vo[StartNo]); ?>"><?php echo ($vo["PregName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
                （<a href="javascript:;" id="getNo" title="请选择学科并填写试题编号！" url="<?php echo U('ProcError/getNoByTestID');?>">搜索</a>试题编号
                <input type="text" class="small" title="TestID" value="" id="testID">试题）
            </div>
        </div>
        <div class="operate single none ">
            试题id:<input type="text" name="sTestID" class="small sTestID" value=""/>
            <label><input name="sShow" class="search imgButton sShow" type="button" value="查看"/></label>
            把<textarea cols="30" rows="1" name="sOld" class="sOld" ></textarea>
            替换为<textarea cols="30" rows="1" name="sReplace" class="sReplace" flag="0"></textarea>
            <label><input name="sLook" class="search imgButton sLook" type="button" value="预览"/></label>
            <label><input name="sDo" class="intro imgButton sDo" type="button" value="替换"/></label>
        </div>
        <!-- 功能操作区域结束 -->

        <!-- 列表显示区域  -->
        <div>
            <!-- Think 系统列表组件开始 -->
            <table id="checkList" class="list" border="1" style="word-break: break-all; word-wrap: break-word;">
                <tr>
                    <td height="5" colspan="4" class="topTd"></td>
                </tr>
                <tr id="head" class="row">
                    <th style="width: 60px;"><input type="checkbox" id="checkAll"></th>
                    <th style="width: 60px;">编号</th>
                    <th style="">原试题</th>
                    <th style="">替换预览试题</th>
                </tr>
                <tr>
                    <td height="5" colspan="4" class="bottomTd"></td>
                </tr>
            </table>
            <!-- Think 系统列表组件结束 -->
        </div>
    </div>
    <!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->
</body>
</html>