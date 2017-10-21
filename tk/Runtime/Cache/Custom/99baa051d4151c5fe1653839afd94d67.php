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
    .warning{
        background-color: #FF9999;
    }
    button{
        cursor:pointer;
        padding:2px 2px;
        border:1px outset #999; 
        background-color:#cde;
    }
    .formatInfo{
        width: 300px;
    }
    .formatInfo li{
        border-bottom:2px solid #ccc;
        list-style-type: none;
        margin: 5px 0px;
    }
    .formatInfo li p{
        font-size:14px;
        margin-left:5px;
    }
</style>
<!-- 主页面开始 -->
<div id="main" class="main" >
<!-- 主体内容  -->
<div class="content" >
<div class="title"><?php echo ($pageName); ?> [ <A HREF="<?php echo U('Custom/CustomDocUpload/index');?>">返回列表</A> ]  [ <A HREF="javascript:history.go(-1);">返回上一页</A> ] <font color='red'>【请尽量在导入试题之前，对试题进行格式化】</font></div>
<!--  功能操作区域  -->
<div class="operate">
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="试题格式化" onclick="" class="btformat edit imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="导入" onclick="" class="extract add imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="移除" onclick="" class="btremove edit imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
</div>
<!-- 功能操作区域结束 -->

<!-- 列表显示区域  -->
<div class="list" >
<div id="result" class="result none"></div>
<!-- Think 系统列表组件开始 -->
<form id="form1" name="form1" action="?" method="post">
<table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
    <tr><td height="5" colspan="11" class="topTd" ></td></tr>
    <tr class="row" >
        <th width="20"><input type="checkbox" id="CheckAll" tag="checkList"></th>
        <th>题序</th>
        <?php if(is_array($tag_array)): $i = 0; $__LIST__ = $tag_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><th><?php echo ($vo["TagName"]); ?></th><?php endforeach; endif; else: echo "" ;endif; ?>
        <th>
            <label for="choose">格式化</label><input type="checkbox" id='choose'>
        </th>
        <th>操作</th>
    </tr>
    <?php if(is_array($newarr)): $t = 0; $__LIST__ = $newarr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($t % 2 );++$t;?><tr class="row lists" jl=''>
        <td><input type="checkbox" name="key[]" class="key" value="<?php echo ($t); ?>"></td>
        <td><?php echo ($t); ?></td>
        <?php if(is_array($node)): $i = 0; $__LIST__ = $node;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><td><div style="width:180px;height:100px;overflow:auto;" class='box'><p><?php echo ($vo); ?></div></td><?php endforeach; endif; else: echo "" ;endif; ?>
        <td align='center'><input type="checkbox" class='format'></td>
        <td>
            <a href="#" class='editTest'>编辑</a><br>
            <a href="#" class='remove'>移除</a>
        </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    <tr><td height="5" colspan="11" class="bottomTd"></td></tr>
</table>
<input name="DocID" type="hidden" id='duid' value="<?php echo ($edit["DUID"]); ?>"/>
<?php if(is_array($start)): $i = 0; $__LIST__ = $start;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><input name="start[]" type="hidden" value="<?php echo ($vo); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
<?php if(is_array($testfield)): $i = 0; $__LIST__ = $testfield;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><input name="testfield[]" type="hidden" value="<?php echo ($vo); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
</form>
<!-- Think 系统列表组件结束 -->
</div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->
<div id='doSomeThing' style='display:none;'>
    <table class="list" cellpadding="5" cellspacing="0" border="1">
        <tr>
            <td width='120' align='right'>题文</td>
            <td width="*">
                <div class='editContainersTest'></div>
            </td>
        </tr>
        <tr>
            <td width='120' align='right'>答案</td>
            <td width="*">
                <div class='editContainersAnswer'></div>
            </td>
        </tr>
        <tr>
            <td width='120' align='right'>解析</td>
            <td width="*">
                <div class='editContainersAnalytic'></div>
            </td>
        </tr>
        <tr>
            <td width='120' align='right'>备注</td>
            <td width="*">
                <textarea id="remark" rows="5" style='width:90%;'></textarea>
            </td>
        </tr>
        <tr>
            <td width='120' align='right'>题型</td>
            <td width="*">
                <select id="types"></select>
            </td>
        </tr>
        <tr>
            <td colspan="2" align='right'>
                <button type='button' id='saveForm' class='mulitOperate'>保存</button>
                <button type='button' class='cancelForm'>取消</button>
            </td>
        </tr>
    </table> 
</div>
<script type='text/javascript' src='/Public/plugin/formatTest.js'></script>
<script type='text/javascript' src='/Public/zjadmin/js/originalityTpl.js'></script>
<script type="text/javascript" src="/Public/plugin/ueditor/ueditor.config.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/ueditor/ueditor.all.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/editor.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script>
    var types = new Object(<?php echo ($types); ?>);
    var currentRow = null;
    var currentType = 0;
    $(document).ready(function(){
        FormatTextManager = $.extend(FormatTextManager, {
            formatContent : function(){
                FormatTextManager.init();
                var columns = currentRow.find('td');
                for(var i=0; i<columns.length; i++){
                    if(i >= 2 && i <= 4){
                        var element = columns.eq(i).find('.box');
                        element.data('index', i);
                        var info = FormatTextManager.process.call(element);
                        if(info !== true){
                            FormatTextManager.err = info;
                            break;
                        }
                    }
                }
                return FormatTextManager.err != '';
            },

            process : function(){
                var that = $(this);
                var index = that.data('index');
                var identifier = 'Test';
                if(3 == index){
                    identifier = 'Answer';
                }else if(4 == index){
                    identifier = 'Analytic';
                }
                FormatTextManager.identifier = identifier;
                return new ParseText(that[0]).parse();
            },

            hasChooseTest: function(){
                var type = getCurrenTestTypeName(currentRow);
                type = getType(type);
                var testStyle = type['TypesStyle'];
                var ifchoosetype = type['IfChooseType'];
                return testStyle == 1 && ifchoosetype == 1;
            },

            hasTestNum : function(){
                var type = getCurrenTestTypeName(currentRow);
                if(type == '完形填空'){
                    return true;
                }
                return false;
            }
        });

        config = $.extend(config, {
            'editZone' : '<tr class="editForm"><td colspan="11"></td></tr>'
        });

        $('.cancelForm').click(function(){
            hideForm();
        });

        $.Editor.init(U('Index/upload?dir=customTest'),'.editContainers');

        $('.editTest').live('click', function(){
            jInfo('加载中...','编辑试题');
            var that = $(this);
            var _parent = that.parents('.row');
            if(_parent.hasClass('extractComplate')){
                jClose();
                alert('已提取的试题不能再编辑！');
                return false;
            }
            showForm(that);
            var data = getData(_parent);
            $.Editor.container = $('.editContainersTest');
            if($.Editor.instance['Test']){
                $.Editor.container.html('');
            }
            $.Editor.createContent(data['Test']);

            $.Editor.container = $('.editContainersAnswer');
            if($.Editor.instance['Answer']){
                $.Editor.container.html('');
            }
            $.Editor.createSolution(data['Answer']);

            $.Editor.container = $('.editContainersAnalytic');
            if($.Editor.instance['Analytic']){
                $.Editor.container.html('');
            }
            $.Editor.createAnalyze(data['Analytic']);
            $('#remark').text(data['Remark']);
            var typeSelect = $('#types');
            typeSelect.html('').append($('<option value="0">请选择</option>'));
            for(var i=0; i<types.length; i++){
                var option = $('<option value="'+types[i]['TypesID']+'">'+types[i]['TypesName']+'</option>');
                typeSelect.append(option);
            }
            typeSelect.val(data['TypesID']);
            toTop(_parent);
            jClose();
            return false;
        });

        $('#saveForm').click(function(){
            var type = $('#types');
            if('0' == type.val()){
                alert('请选择题型');
                return false;
            }
            var prev = $(this).parents('.editForm').prev();
            var columns = prev.find('td');
            columns.eq(2).find('.box').html($.Editor.instance['Test'].getContent());
            columns.eq(3).find('.box').html($.Editor.instance['Answer'].getContent());
            columns.eq(4).find('.box').html($.Editor.instance['Analytic'].getContent());
            columns.eq(5).find('.box').html(type.find(':checked').text());
            columns.eq(6).find('.box').html($('#remark').text());
            hideForm();
        });

        $('.remove').live('click', function(){
            hideForm();
            var that = $(this).parents('.row');
            if(that.hasClass('extractComplate')){
                alert('已提取的试题不能被移除！');
                return false;
            }
            that.remove();
            $('.row').each(function(index){
                $(this).find('td').eq(1).html(index);
            });
        });

        $('.btformat').click(function(){
            var rows = getSelectedRows('format');
            if(rows.length == 0){
                alert('请选择需格式化的试题！');
                return false;
            }
            var html = '';
            for(var i=0; i<rows.length; i++){
                currentRow = rows[i];
                html += getFormatInfo(currentRow);
            }
            html = '<ul class="formatInfo">'+html+'</ul>'
            jFrame(html, '格式化完成');
        });

        $('.formatInfo a').live('click', function(){
            var list = $('.row');
            var order = $(this).attr('order');
            for(var i=0; i<list.length; i++){
                var obj = $(list[i]);
                if(obj.find('td').eq(1).html() == order){
                    jClose();
                    obj.find('.editTest').trigger('click');
                    break;
                }
            }
            return false;
        });

        $('.btremove').live('click', function(){
            var list = getSelectedRows('key');
            for(var i=0; i<list.length; i++){
                list[i].find('.remove').trigger('click')
            }
        });

        $('.extract').live('click', function(){
            hideForm();
            var rows = getSelectedRows('key');
            if(rows.length == 0){
                alert('请选择试题');
                return false;
            }
            if(!window.confirm('该操作可能会影响之前的数据，确定导入试题？')){
                return false;
            }
            jInfo('数据处理中...', '保存试题');
            save(rows[0]);
        });

        $('#choose').click(function(){
            var format = $('.row .format');
            format.each(function(){
                var that = $(this);
                that.attr('checked', !that.attr('checked'));
            });
           
        });
    });

    function getFormatInfo(row){
        var data = getContent(row);
        var order = row.find('td').eq(1).html();
        var html = '<li><p><a href="#" title="编辑" order="'+order+'">第'+order+'题</a>：';
        if(typeof(data) === 'string'){
            html += ('<font color="red">'+data+'</font></li>');
        }else{
            data = data['attributes'];
            var ifChoose = data['IfChoose'];
            if(0 == ifChoose){
                ifChoose = '非选择题';
            }else{
                ifChoose = '选择题';
            }
            html += '本题为：'+ifChoose+'。共有小题'+data['complex'].length+'道小题</p>';
            var optionNum = data['OptionNum'];
            if(typeof(optionNum) === 'string'){
                optionNum = optionNum.split(/,/g);
            }
            for(var i=0; i<data['complex'].length; i++){
                var type = data['complex'][i]['type'];
                if(1 == type){
                    type = '复合题';
                }else if(2 == type){
                    type = '多选题';
                }else if(3 == type){
                    type = '单选题';
                }
                var option = optionNum[i] || 0;
                html += '<p>小题'+data['complex'][i]['no']+'：题型为['+type+'],选项数量为['+option+']个</p>';
            }
            html += '</li>';
        }
        return html;
    }
    

    function save(row){
        currentRow = row;
        var data = getContent(currentRow);
        if(typeof(data) === 'string'){
            toTop(currentRow);
            currentRow.find('td').addClass('warning');
            jClose();
            alert(data);
            return false;
        }
        data['docid'] = $('#duid').val();
        data['last'] = row.nextAll('.row').length > 0 ? 1 : 0; //用于后台对后续数据的处理
        $.post(U('Custom/CustomDocUpload/testSave'), data, function(result){
            //权限验证
            jClose();
            if(checkPower(result)=='error'){
                return false;
            }
            if('success' == result['data']){
                next = currentRow.nextAll('.row');
                currentRow.addClass('extractComplate').find('td').css('background-color', '#CCFFCC');
                if(next.length > 0){
                    if(currentRow.hasClass('warning')){
                        currentRow.removeClass('warning');
                    }
                    toTop(currentRow);
                    save($(next[0]));
                }else{
                    alert('入库完成！');
                }
            }
        });
    }

    function toTop(row){
        $(document).scrollTop(row.offset().top);
    }

    //返回选中的所有行
    function getSelectedRows(className){
        var list = [];
        $('.row td .'+className).each(function(){
            var that = $(this);
            if(that.attr('checked') && !that.parents('.row').hasClass('extractComplate')){
                list.push(that.parents('.row'));
            }
        });
        return list;
    }

    function getContent(row){
        var data = {};
        FormatTextManager.init();
        if(!row.find('.foramt').attr('checked')){
            FormatTextManager.isForamt = false; 
        }else{
            FormatTextManager.isForamt = true; 
        }
        FormatTextManager.formatContent();
        if(FormatTextManager.err != ''){
            return FormatTextManager.err;
        }
        data = getData(row);
        if(!data['TypesID']){
            return '无效的题型参数！';
        }
        // console.log(FormatTextManager.attributes);
        // console.log(data);
        return {
            'attributes' : FormatTextManager.getTopic(),
            'data' : data,
            'OrderRule' : row.find('td').eq(1).html()
        }
    }

    function getData(row){
        var columns = row.find('td');
        var data = {};
        data['Test'] = columns.eq(2).find('.box').html();
        data['Answer'] = columns.eq(3).find('.box').html();
        data['Analytic'] = columns.eq(4).find('.box').html();
        data['Remark'] = columns.eq(6).find('.box').html().replace(/<.*?[^>]>/g, '');
        var types = getType(getCurrenTestTypeName(row));
        if(types['TypesID']){
            data['TypesID'] = types['TypesID'];
        }else{
            data['TypesID'] = 0;
        }
        return data;
    }

    function getCurrenTestTypeName(row){
        return row.find('td').eq(5).find('.box').html().replace(/<.*?[^>]>|\s+/g, '');
    }

    function getType(type){
        for(var i=0; i<types.length; i++){
            if(type == types[i]['TypesName']){
                type = types[i];
                break;
            }
        }
        return type;
    }
</script>

</body>
</html>