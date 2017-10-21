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
<script src="/Public/plugin/template.js" type="text/javascript"></script>
<style>
    #tags{
        font-size:14px;
    }
    .duplicateTag li{
        display: inline;
        float:left;
        padding:0px;
        margin-bottom:5px;
        margin-right:5px;
        line-height:20px;
        height:20px;
        border:1px solid #ccc;
        background-color:#eec;
        cursor: pointer;
    }
    .duplicateTag .duplicationid{
        padding:3px 5px 2px 5px;
        text-decoration: none;
        border-bottom: none;
        font-size:13px;
    }
    .cancelRemoveDuplication{
        padding:1px 5px;
        cursor:pointer;
        background-color:#ffc;
    }
    .duplicateTag .duplicationid:hover,
    .cancelRemoveDuplication:hover{
        background-color: #fff;
        color:#666;
    }

    .includeDuplication{
        background-color:#bb0000;
        color:#fff;
    }

    .removeDuplicationBtn{
        cursor:pointer;
        padding:4px 2px;
        border:1px outset #999; 
        background-color:#cde;
    }

    #duplicateContent{
        clear:both;
        overflow:auto;
        border:1px solid #cde;
    }

    .anchorElement{
        margin-bottom:10px;
        border-bottom:5px solid #ff3333;
    }
    .anchorElement:hover{
         background-color: #FFFFee;
    }
    .anchorElementSelected{
        background-color: #FFFFCC;
    }
    #orginalTestTitle,
    .duplicationsTitle,
    .auchorElementTitle{
        padding-top:2px;
        padding-bottom:2px;
        padding-left:5px;
        padding-right:2px;
        margin-bottom: 3px;
        background-color:#cde;
        border:1px solid #ccc;
        font-weight: bold;
    }
    .duplicationsContent .anchorElement{
        margin-bottom:2px;
        border-bottom:none;
    }
    .duplicationsTitle{
        background-color:#ccc;
    }
    .duplicationsContent{
        margin-left:20px;
    }
    #orginalTestTitle{
        margin-top:5px;
        font-size:13px;
        margin-bottom:0px;
    }
    #orginalTest{
        padding:2px;
        margin-bottom: 5px;
        height:70px;
        overflow-y:auto;
        overflow-x:hidden;
        border-bottom:1px solid #cde;
        border-left:1px solid #cde;
        border-right:1px solid #cde;
    }
</style>
<script language="javascript">
    var docid = <?php echo ($docid); ?>;
    var isDumplate = <?php echo ($in); ?>;
    var page = 1;
    $(document).ready(function(){
        $('.nowedit').live('click',function(){
                var a=$(this).attr('thisid');
                jInfo('加载中请稍候。。。','加载数据');
                //获取数据
                $.post(U('Test/Test/showDuplicate'),{'id':a,'in':isDumplate,'times':Math.random()}, function(data){
                    jClose();
                    //权限验证
                    if(checkPower(data)=='error'){
                        return false;
                    }
                    jFrame(data['data'],'编辑试题：编号'+a);
                });
        });
        $('.showedit').live('click',function(){
            var a=$(this).attr('thisid');
            jInfo('加载中请稍候。。。','加载数据');
            //获取数据
            $.post(U('Test/Test/showDuplicate'),{'id':a,'in':isDumplate,'times':Math.random()}, function(data){
                jClose();
                //权限验证
                if(checkPower(data)=='error'){
                    return false;
                }
                jFrame(data['data'],'试题展示：编号'+a);
            });
        });

        //标记重复
        $('.btlock').live('click',function(){
            var that = $(this);
            var keyValue = that.attr('thisid');
            var name='duplicate'+keyValue+'[]';
            var duplicate='';
            if($('[name="'+name+'"]').length>0){
                $('[name="'+name+'"]').each(function(){
                    if($(this).attr('checked')=='checked'){
                        duplicate += $(this).val()+',';
                    }
                });
            }
            if(duplicate==''){
                alert('请选择重复试题！');
                return;
            }
            if(!keyValue){
                alert('请选择标记项！');
                return false;
            }
            jInfo('标记中请稍候。。。','标记数据');
            $.post(U('Test/Test/mark'),{'Duplicate':duplicate,'id':keyValue,'times':Math.random()}, function(data){
                jClose();
                //权限验证
                if(checkPower(data)=='error'){
                    return false;
                }
                var msg = data['data'];
                if('success' == msg){
                    markDuplication(that, duplicate);
                }else{
                    alert(msg);
                }
            });
            return false;
        });
        //取消标记重复
        $('.btunlock').live('click',function(){
            var that = $(this);
            var keyValue = $(this).attr('thisid');
            var name='duplicate'+keyValue+'[]';
            var duplicate=$('#duplicationTestId').val();
            // if($('[name="'+name+'"]').length>0){
            //     $('[name="'+name+'"]').each(function(){
            //         if($(this).attr('checked')=='checked'){
            //             duplicate += $(this).val()+',';
            //         }
            //     });
            // }
            if(duplicate==''){
                alert('请选择要取消的重复试题！');
                return;
            }
            if(!keyValue){
                alert('请选择取消标记项！');
                return false;
            }
            jInfo('取消标记中请稍候。。。','取消标记数据');
            $.post(U('Test/Test/mark'),{'Duplicate':duplicate,'id':keyValue,'style':'unlock','times':Math.random()},
                    function(data){
                jClose();
                //权限验证
                if(checkPower(data)=='error'){
                    return false;
                }
                var msg = data['data'];
                if('success' == msg){
                    that.attr('class','btlock').css('color','').html('标记').siblings('.marked').html('');
                }else{
                    alert(msg);
                }
            });
            return false;
        });
        
        $('.mainCheckbox').live('click', function(){
            var checkbox = $(this);
            var checked = checkbox.attr('checked') || false;
            $(this).parents('.anchorElement').find('.duplicationsContent .anchorElementCheckbox').each(function(){
                $(this).attr('checked', checked);
            });
        });

        $('.appendToHere span').click(function(){
            loadTest();
        });
        $('#removeDuplidate').click(function(){
            var num = $("#rdNum").val();
            if(!num){
                num = 3;
            }
            Cookie.Set('DUPLICATE_MANAGE', num);
            loadTest();
        });
    });
    var template = new Template();
    var _setupOptions = $.extend(setupOptions,{
        timeout:0
    });
    $.ajaxSetup(_setupOptions); //防止超时
    function loadTest(){
        Cookie.Set('DUPLICATE_MANAGE', $('#rdNum').val());
        var num = 0;
        $('.record').each(function(){
            num++;
        });
        if(!page){
            return false;
        }
        var prepage = Cookie.Get('DUPLICATE_MANAGE');
        jInfo('数据加载中....','正在查询');
        $.post(U('Test/Test/duplicateTest'),
            {'docid' : docid , 'in' : isDumplate, 'page' : num, 'prepage' : prepage},
            function(data){
                if(checkPower(data)=='error'){
                    jClose();
                    return false;
                }
                countPage = data['data']['countPage'];
                var result = data['data']['list'];
                if(result && result.length > 0){
                    $('.appendToHere').before(template.setParams(['datas','page']).render($('#template').html(), result, page++));
                }else{
                    $('.appendToHere span').html('该试卷暂无试题').off('click');
                }
                jClose();
                if(num+result.length >= countPage){ //试题加载完毕
                    $('.appendToHere span').html('试题已全部加载完毕').off('click');
                    page = false;
                }
            }
        );
    }

    //-------------------------自定义去重------------------------
    $(".customRemoveDuplication").live('click', function(){
        var id = $(this).attr('testid');//orginalTest
        var orginalTest = $(this).parents('tr').find('.testdivbak2 a').html();
        var html = template.setParams(['datas','orginalTest']).render($('#removeDuplication').html(), id, orginalTest);
        jFrame(html,'自定义去重，试题ID【'+id+'】');
        var height = $('#popup_container').height();
        $('#popup_message').css({
            'overflow' : 'hidden'
        });
        drawContentHeight();
        // var height = 450;
        // $('#popup_container').css({
        //     'height' : height,
        //     'top' : ($(window).height() - height) / 2 - 50,
        // });
        return false;
    });
    //自定义去重确认
    $('.markDuplicationBtn').live('click', function(){
        var that = $(this);
        var id = that.siblings('.current').val();
        var dupid = [];
        $('#duplicateContent .mainCheckbox').each(function(){
            var that = $(this);
            if(that.attr('checked')){
                var arr = [];
                arr.push(that.val());
                that.parents('.anchorElement').find('.duplicationsContent .anchorElementCheckbox').each(function(){
                    var children = $(this);
                    if(children.attr('checked')){
                        arr.push(children.val());
                    }
                });
                dupid.push(arr.join('|'));
            }
        });
        if(dupid.length == 0){
            alert('重复试题编号不能为空！');
            that.siblings('.duplicationId').focus();
            return false;
        }
        if(!parseInt(id)){
            return false;
        }
        if(dupid.length > 1){
            if(!window.confirm('确定以下所选中的试题为相同的试题？')){
                return false;
            }
        }
        that.html('加载中....');
        customRemoveDuplication(id, dupid);
    })

    //标重检查
    $('.checkDuplicationBtn').live('click', function(){
        //提交前执行一次标签生成
        var duplicationid = $('.duplicationId');
        createTagsHandler(duplicationid, duplicationid.val());
        var dupid = getIds();
        var that = $(this);
        if(dupid.length == 0){
            alert('重复试题编号不能为空！');
            that.siblings('.duplicationId').focus();
            return false;
        }
        that.html('加载中....');
        $.post(U('Test/Test/checkDuplicateTest'), {'dupid':dupid.join(','), 'isDumplate':isDumplate}, function(data){
            that.html('标重检查');
            if(checkPower(data)=='error'){
                return false;
            }
            var msg = data['data'];
            if('success' == msg){
                alert('当前所选试题暂未标重');
            }else if(typeof(msg) === 'object'){
                //返回的如果是数组则对显示已标重的试题
                for(var val in msg){
                    var id = msg[val]['TestID'];
                    var dupid = msg[val]['Duplicate'];
                    $('#tags .duplicationid').each(function(){
                        var that = $(this);
                        if(id == that.html()){
                            that.addClass('includeDuplication')
                        }else if(!that.attr('dupid')){
                            that.removeClass('includeDuplication');
                        }
                        that.attr('dupid', dupid);
                    });
                }
            }
        });
    });


    //输入框事件绑定
    $('.duplicationId').live('keyup',function(e){
        e = e || event;
        var code = e.keyCode || e.which;
        var that = $(this);
        if(32 == code && '' != val){
            var val = that.val();
            createTagsHandler(that, val);
            drawContentHeight();
        }
    });
    // $('.duplicationId').live('blur', function(){
    //     var that = $(this);
    //     createTagsHandler(that, that.val());
    // });

    //绑定取消去重事件
    $('.cancelRemoveDuplication').live('click', function(){
        var val = $('.duplicationId').val() + ' ';
        var that = $(this);
        var id = that.prev().html();
        var reg = new RegExp('[\\s]'+id+'\\s+|^'+id+'\\s+', 'g');
        $('.duplicationId').val(val.replace(reg, ' ').replace(/\s{2,}/g, ' ').replace(/^\s/, ''));
        that.parent().remove();
        var anchorElement = $('#anchorElement'+id);
        if(!anchorElement.parent().hasClass('duplicationsContent')){
            anchorElement.remove();
        }
        drawContentHeight();
        return false;
    });


    //存在重复试题时显示重复试题信息
    $('.duplicationid').live('click', function(){
        var that = $(this);
        var id = that.html();
        var anchorElement = $('#anchorElement'+id);
        if(anchorElement.length > 0){
            scrollTo(id);
            return false;
        }
        that.html('加载中..');
        $.post(U('Test/Test/getDuplicateTest'),{'id':id,'in':isDumplate,'times':Math.random()}, function(data){
            that.html(id);
            if(checkPower(data)=='error'){
                return false;
            }
            var mark = false;
            if(data['data'][0]){
                mark = data['data'][0];
            }
            if(mark === false){
                return false;
            }
            showDuplicateTest(that, data['data'][0], data['data'][1]);
        });
        return false;
    });

    //显示重复试题列表
    function showDuplicateTest(that, data, duplications){
        duplications = duplications || [];
        var id = data['TestID'];
        var html = $("#duplicateContent").html();
        var anchor = 'anchorElement'+id;
        $('#duplicateContent').append(getDuplicateTestHtml(id, anchor, data['Test'], 'mainCheckbox', false, data['Duplicate']));
        if(!that.hasClass('includeDuplication')){
            $('#'+anchor).find('.mainCheckbox').attr('checked', true);
        }
        if(duplications.length > 0){
            var duplicationsHtml = '<div class="duplicationsTitle">本题已标记为以下试题的重复试题：</div>'
            for(var i=0; i<duplications.length; i++){
                var _anchor = 'anchorElement'+duplications[i]['TestID'];
                var test = duplications[i]['Test'];
                //容错！查看该试题是否已经已经存在于标签中
                var isDisable = true;
                var ids = getIds();
                for(var k=0; k<ids.length; k++){
                    if(duplications[i]['TestID'] == ids[k]){
                        //如果该试题存在标签内，同时已经显示，则删除它
                        var anchorElement = $('#anchorElement'+ids[k]);
                        if(anchorElement.length > 0){
                            anchorElement.remove();
                        }
                        isDisable = true;
                        break;
                    }
                }
                duplicationsHtml += getDuplicateTestHtml(duplications[i]['TestID'], _anchor, test, '', isDisable);
            }
            $('#'+anchor).find('.duplicationsContent').html(duplicationsHtml);
        }
        that.attr('href', '#'+anchor);
        scrollTo(data['TestID']);
    }

    //添加重复试题html
    function getDuplicateTestHtml(id, anchor, test, className, isDisable, duplicate){
        if(duplicate && duplicate != 0){
            duplicate = "<font color='red'>该题存在重复试题，重复试题编号：【"+duplicate+"】</font>";
        }else{
            duplicate = '';
        }
        var disable = '';
        if(isDisable){
           disable = ' disabled="disabled" checked="checked"'; 
        }
        return '<div class="anchorElement" id="'+anchor+'"><div class="auchorElementTitle"><input type="checkbox" class="anchorElementCheckbox '+className+'" value="'+id+'"'+disable+'/>&nbsp;试题编号[&nbsp;'+id+'&nbsp;]&nbsp;&nbsp;'+duplicate+'</div>'+test+'<div class="duplicationsContent"></div></div>';
    }

    //计算内容显示区域高度
    function drawContentHeight(){
        var content_h = $('#popup_content').height();
        var elements_h = $('#elements').outerHeight();
        var tags_h = $('#tags').outerHeight();
        var duplicateContent_h = content_h - elements_h - tags_h - 80;
        $('#duplicateContent').height(duplicateContent_h)
    }

    //内容滚动至指定试题处
    function scrollTo(id){
        $('#anchorElement'+id).addClass('anchorElementSelected').siblings('.anchorElement').each(function(){
            $(this).removeClass('anchorElementSelected');
        });
        window.location.href = '#anchorElement'+id;
    }

    //获取自定义重题id
    function getIds(){
        var dupid = [];
        $('#tags').find('.duplicationid').each(function(){
            dupid.push($(this).html());
        });
        return dupid;
    }

    //生成重复试题标签内容
    function createTagsHandler(that, val){
        var arr = splitDuplicationId(val);
        if(arr.length == 0){
            return;
        }
        that.val(arr.join(' ')+' ');
        $('#tagli').html(generateTag(arr));
    }

    //分解输入的内容
    function splitDuplicationId(val){
        if('' == val){
            return [];
        }
        var arr = val.split(/\s/g).sort();
        if(arr.length == 0){
            return [];
        }
        //去除重复的id
        var i = arr.length - 1;
        for(; i>0; i--){
            if(arr[i] === arr[i-1]){
                arr.splice(i,1);
            }
        }
        return arr;
    }

    //创建重复试题标签
    function generateTag(arr){
        var html = '<ul class="duplicateTag">';
        for(var i=0; i<arr.length; i++){
            if(parseInt(arr[i])){
                html += '<li><a class="duplicationid" title="查看该试题及其重题" href="">'+ arr[i] + '</a><span class="cancelRemoveDuplication" title="取消排重">x</span></li>';
            }
        }
        //将标签中不存在的试题从试题显示列表中移除
        $('#duplicateContent').children('.anchorElement').each(function(){
            var that = $(this);
            var id = that.attr('id');
            var mark = false;
            for(var i=0; i<arr.length; i++){
                if('anchorElement'+ arr[i] === id){
                    mark = true;
                }
            }
            if(!mark){
                that.remove();
            }
        });
        return html += '</ul>';
    }   

    //试题标重
    function markDuplication(obj, dupid){
        obj.attr('class','btunlock').css('color','red').html('取消');
        var marked = obj.siblings('.marked');
        if(marked.length == 0){
            marked = $('<div class="marked"></div>');
            obj.before(marked);
        }
        if(typeof(dupid) === 'string')
            dupid = getMinDupid(dupid.split(','));
        marked.html(markedTest(dupid));
    }
    //获取最小重复试题id
    function getMinDupid(dupid){
        var min = 0;
        for(var i=0; i<dupid.length; i++){
            if(min == 0){
                min = dupid[i];
            }else if(dupid[i] < min && !isNaN(parseInt(dupid[i]))){
                min = dupid[i];
            }
        }
        return min;
    }

    //试题自定义去重
    function customRemoveDuplication(id, dupid){
        $.post(U('Test/Test/customRemoveDuplication'), {'id':id, 'dupid':dupid.join(',')}, function(data){
            $('.markDuplicationBtn').html('标记重复');
            if(checkPower(data)=='error'){
                return false;
            }
            var msg = parseInt(data['data']);
            if(!isNaN(msg)){
                var td = $('#duplicate'+id);
                var obj = td.find('.btlock');
                if(obj.length == 0){
                    obj = td.find('.btunlock');
                }
                // if(obj.length == 0){
                //     obj = $('<a class="btunlock" href="javascript:;"  thisid="'+msg+'"><font color="red">取消</font></a>')
                //     td.append(obj);
                // }
                markDuplication(obj, msg);
                alert('去重成功！');
                jClose();
            }else{
                alert(data['data']);
            }
        });
    }

    //已标记试题
    function markedTest(id){
        if(typeof(id) !== 'number'){
            id = id.replace(/,$/, '');
        }
        return "<a href='javascript:void(0);' class='showedit' thisid='"+id+"'>已标重试题("+id+")</a>";
    }

    //--------------------以下为模板使用函数------------------
    function getTest(html){
        if(!html){
            return '<p>无</p>';
        }
        return '<p>'+html;
    }
    function getCheckbox(dataDuplicate, duplicate, duplicateTestId, testId){
        var str = '<input type="checkbox" value="'+ duplicateTestId+'" name="duplicate'+testId+'[]"';
        if(dataDuplicate!=0 && (duplicate == dataDuplicate || dataDuplicate==duplicateTestId)){
            str += ' checked="checked"';
        }
        return str + '/>';
    }

</script>

<script type='text/html' id='removeDuplication'>
    <div id='elements'>
        重复试题编号：[多个编号请用空格分开。红色标签为已标重]<br>
        <input type='text' class='duplicationId' style='width:550px;'/>
        <input type='hidden' class='current' value='<%= datas %>'/>
        <span class='checkDuplicationBtn removeDuplicationBtn' title='所选的试题是否已标重'>标重检查</span>
        <span class='markDuplicationBtn removeDuplicationBtn'>标记重复</span>
    </div>
    <div id='tags'><div id='tagli'></div><div style='clear:both;'></div><div id='orginalTestTitle'>原试题：[&nbsp;<%= datas %>&nbsp;]</div><div id='orginalTest'><%= orginalTest %></div><div style='clear:both;'></div></div>
    <div id='duplicateContent' style='overflow:auto;'></div>
</script>

<script type='text/html' id='template'>
    <tr>
        <td colspan="6" height='20' align="center" style='background-color:#cde;'>
            第&nbsp;<%= page %>&nbsp;部分数据
        </td>
    </tr>
    <% 
        for(var i=0; i<datas.length; i++) {
            var data = datas[i];
    %>
            <tr class="row record" jl=''>
                <td><%= data.TestID %></td>
                <td width="300">
                    <div class="text_source2">
                        来源：
                        <a href="javascript:;" title="<%= data.DocID %>:<%= data.DocName %>"><%= data.DocID %>:<%= data.DocName %></a>
                    </div>
                    <div class="testdivbak2">
                        <a href="javascript:void(0);" class="nowedit" thisid="<%= data.TestID %>">
                            <%= getTest(data.Test) %>
                        </a>
                    </div>
                </td>

                <!--输出第一道相似题-->
                <td>
                    <% 
                        if(data.duplicate && data.duplicate[0] && data.duplicate[0]['testid'] != 0){ 
                            var duplicateTest = data.duplicate[0];
                            var checked = '';
                            if(data.Duplicate!=0 && (data.Duplicate == duplicateTest.testid || data.Duplicate == duplicateTest.duplicate )){
                                checked = ' checked="checked"';
                            }
                    %>
                            <div class="text_source2">
                                <input type="checkbox" value="<%= duplicateTest.testid %>" name="duplicate<%= data.TestID %>[]"<%= checked %>/>
                                来源：
                                <span title="<%= duplicateTest.docid %>:<%= duplicateTest.docname %>"><%= duplicateTest.docid %>:<%= duplicateTest.docname %></span>
                            </div>
                            <div class="testdivbak2">
                                <a href="javascript:void(0);" class="showedit" thisid="<%= duplicateTest.testid %>">
                                    <%= getTest(duplicateTest.test) %>
                                </a>
                            </div>
                    <% }else{ %>
                        无
                    <% } %>
                </td>
                <!--------end 输出第一道相似题----------->

                <!--------输出第二道相似题--------------->
                <td>
                    <% 
                        if(data.duplicate && data.duplicate[1] && data.duplicate[1]['testid'] != 0){ 
                            var duplicateTest = data.duplicate[1];
                            var checked = '';
                            if(data.Duplicate!=0 && (data.Duplicate == duplicateTest.testid || data.Duplicate == duplicateTest.duplicate)){
                                checked = ' checked="checked"';
                            }
                    %>
                        <div class="text_source2">
                            <input type="checkbox" value="<%= duplicateTest.testid %>" name="duplicate<%= data.TestID %>[]"<%= checked %>/>
                            来源：
                            <span title="<%= duplicateTest.docid %>:<%= duplicateTest.docname %>"><%= duplicateTest.docid %>:<%= duplicateTest.docname %></span>
                        </div>
                        <div class="testdivbak2">
                            <a href="javascript:void(0);" class="showedit" thisid="<%= duplicateTest.testid %>">
                                <%= getTest(duplicateTest.test) %>
                            </a>
                        </div>
                    <% }else{ %>
                        无
                    <% } %>
                </td>
                <!-----------end 输出第二道相似题---------->

                <td>
                    <div class="">
                        <p>
                            <% if(data.duplicate && data.duplicate.ids){ %>
                                <% 
                                    for(var test in data.duplicate.ids){
                                        test = data.duplicate.ids[test];
                                %>
                                    <%= getCheckbox(data.Duplicate,test.duplicate, test.testid, data.TestID) %>
                                    <a href="javascript:void(0);" class="showedit" thisid="<%= test.testid %>"><%= test.testid %></a>
                                    <br/>
                                <% } %>
                            <% } %>
                        </p>
                    </div>
                </td>
                <td>
                    <div  id="duplicate<%= data.TestID %>">
                        <% if(data.duplicate && data.duplicate[0] && data.duplicate[0]['testid'] != 0){ %>
                            <% if(data.Duplicate == 0){ %>
                                <a class="btlock" href='javascript:;' thisid="<%= data.TestID %>">标记</a>
                            <% }else{ %>
                                <input type='hidden' id='duplicationTestId' value='<%= data.Duplicate %>'/>
                                <div class='marked'><%= markedTest(data.Duplicate) %></div>
                                <a class="btunlock" href='javascript:;'  thisid="<%= data.TestID %>"> <font color="red">取消</font>
                                </a>
                            <% } %>
                            &nbsp;&nbsp;
                        <% }else if(data.Duplicate == 0){ %>
                            <a class="btlock" href='javascript:;' thisid="<%= data.TestID %>">标记</a>
                        <% } %>
                    </div>
                    <a href='javascript:;' class='customRemoveDuplication' testid="<%= data.TestID %>">自定义去重</a>
                </td>
            </tr>
    <% 
        }
    %>
</script>
<!-- 主页面开始 -->
<div id="main" class="main" >
    <!-- 主体内容  -->
    <div class="content" >
        <div class="title">
            <?php echo ($pageName); ?> [
            <a href="<?php echo U('Test/Test/index',array('errortest'=>1));?>">错误试题查看</a>
            ]  [
            <A HREF="/Test/Test">返回试题列表</A>
            ]  [
            <A HREF="javascript:history.go(-1);">返回上一页</A>
            ]
        </div>
        <div>每页题数：<input type="text" id='rdNum' value='3' style='width:50px;'> <button id='removeDuplidate'>排重</button></div>
        <!--  功能操作区域  -->
        
        <!-- 列表显示区域  -->
        <div class="list" >
            <div id="result" class="result none"></div>
            <!-- Think 系统列表组件开始 -->
            <form method="POST" action="" id="form1" enctype="multipart/form-data">
                <table class="list" cellpadding="5" cellspacing="0" border="1">
                    <tr>
                        <td height="5" colspan="6" class="topTd" ></td>
                    </tr>
                    <tr class="row" >
                        <th width="40">编号</th>
                        <th width="300">试题及来源</th>
                        <th width="50">重复试题1及来源</th>
                        <th width="50">重复试题2及来源</th>
                        <th width="50">其他重复试题编号</th>
                        <th width="45">操作</th>
                    </tr>
                    <tr class='appendToHere'>
                        <td colspan="6" height='20' align="center"><span style='display:block; width:100%; line-height:20px; height:20px;text-align:center;cursor:pointer;'><a href='javascript:;' onclick='return false;'>加载更多试题</a></span></td>
                    </tr>
                    <tr>
                        <td height="5" colspan="6" class="bottomTd"></td>
                    </tr>
                </table>
                <!-- Think 系统列表组件结束 --> </form>
        </div>
        <!--  分页显示区域 -->
        <div class="page"><?php echo ($page); ?></div>
        <!-- 列表显示区域结束 --> </div>
    <!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

</body>
</html>