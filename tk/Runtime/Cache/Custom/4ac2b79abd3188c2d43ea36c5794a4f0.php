<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>微课添加</title>
<meta name="keywords" content="微课添加" />
<meta name="description" content="微课添加" />
    
<link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
<link type="text/css" href="/Public/default/css/customTest.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <!--[if lte IE 6]>
    <script type="text/javascript" src="/Public/plugin/png.js"></script>
    <script>DD_belatedPNG.fix('a,img,div,span');</script>
    <![endif]-->
    <script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/customTest.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <!--引入CSS-->
    <link rel="stylesheet" type="text/css" href="/Public/plugin/webuploader/webuploader.css">
    <!--引入JS-->
    <script type="text/javascript" src="/Public/plugin/webuploader/webuploader.min.js"></script>
</head>
<body>
<div id="righttop">
    <div id="categorylocation">
        <span class="nowPath">当前位置：</span>> <span id="loca_text"> <a href='<?php echo U('Custom/MicroClass/index');?>'>校本微课</a></span> > <span
            id="loca_text"> <?php echo ($pageName); ?></span>
    </div>
</div>

<div id="divbox" style='position:relative;'>
    <div class="content_01" id="wdgl">
        <div class="wdtj_box">  
            <div class="content">
                <!-- 内容显示区域  -->
                <form action="" method="post" id='test-form'>
                    <input type="hidden" name='MID' value='<?php echo ($MID); ?>' id='MID'/>
                    <input type="hidden" name='verifyCode' id='verifyCode' value='<?php echo ($verifyCode); ?>'/>
                    <div class="title"><span class="fl">微课添加</span></div>            
                    <table cellpadding="5" cellspacing="0" class="" border="0" style='width:100%;'>
                        <tbody>
                        <tr>
                            <td align="right" class="tRight" style="width:80px">微课名称：</td>
                            <td class="tLeft">
                                <span class='boxlist_sel'><input type="text" name='MName' id='MName'/></span>
                            </td>
                        </tr>
                        <tr class="selItem">
                            <td align="right" class="tRight" style="width:80px">年级：</td>
                            <td class="tLeft">
                                <select id="grade" class="large bLeft" name="GradeID">
                                    <option value="">请选择</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="selItem">
                            <td align="right" class="tRight" style="width:80px">知识点：</td>
                            <td class="tLeft knowledge-select-change-container">
                                <select id="knowledge" class="selection bLeft"><option value="">请选择</option>
                                </select> 
                                <span class='append'>添加</span>
                                <div id="klinput" class='inputs'>
                                </div>
                            </td>
                        </tr>
                        <tr class="selItem">
                            <td align="right" class="tRight" style="width:80px">课件上传：</td>
                            <td class="tLeft chapter-select-change-container">
                                <div id="uploader" class="wu-example">
                                <!--用来存放文件信息-->
                                <div id="thelist" class="uploader-list"></div>
                                <div class="btns">
                                    <div id="picker">选择文件</div>
                                    <button id="ctlBtn" class="btn btn-default">开始上传</button> 
                                </div>
                                <div class="beizhu">* 请上传mp4格式视频</div>
                            </div>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" class="tRight" style="width:80px">备注：</td>
                            <td class="tLeft">
                                <textarea name="Remark" id="remark" rows="5" style='width:90%;resize:horizontal;'></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr> 
                        </tbody>
                    </table>
                        <div style="width:85%;padding:0 15px 20px;overflow:hidden">
                            <div class="bgbt an01" style="float:left; margin-top:6px; margin-right:10px"><span class="an_left"></span><a href='#' id='save'>保存</a><span class="an_right"></span></div>
                        </div>
                </form>
            <!-- 内容显示区域结束 -->
            </div>
        </div>
    </div>

    <div id='test'></div>
  <!--  分页显示区域 -->
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var editData=[<?php echo ($data); ?>];
        if(typeof(editData[0])!='undefined'){
            $('#MID').val(editData[0]['MID']);
            $('#MName').val(editData[0]['MName']);
            $('#remark').val(editData[0]['Remark']);
            var html='';
            for(var i in editData[0]['KlArr']){
                html+='<div><a href="#" class="input-del" title="移除">x</a><span>'+editData[0]['KlArr'][i]+'</span><input class="KlID_inputs" value="'+i+'" type="hidden"></div>'
            }
            $('#klinput').html(html);
            
            var html='';
            for(var i in editData[0]['UrlArr']){
                html+='<div id="WU_FILE_0" class="item"><h4 class="info">微课视频'+(i+1)+'</h4><p class="state">上传成功<span class="delHang">X<input name="url[]" class="uploadUrl" value="'+editData[0]['UrlArr'][i]+'" type="hidden"></sapn></p><div class="progress progress-striped active" style="display: none;"><div class="progress-bar" role="progressbar" style="width: 100%;"></div></div></div>';
            }
            $('#thelist').html(html);
            
        }
        var url = '/Custom/MicroClass';
        if(url.indexOf('/')===0){
            url = url.substring(1);
        }
        $.CustomTestStore.url=url;
        var act = '<?php echo ($act); ?>';
        var params = {};
        if($.CustomTestStore.subjectID){
            $('#knowledge').html('<option value="">加载中请稍候...</option>');
            params['subjectID'] = $.CustomTestStore.subjectID;
            params['list'] = ['knowledge','grade'];
            params['style'] = 'getMoreData';
            params = $.CustomTestStore.formatParams(params);
            $.post(U($.CustomTestStore.url+'/getData'),params,function(result){
                if($.myCommon.backLogin(result)==false){
                    return false;
                };
                var datas = result['data'];
                var thisgrade=$.CustomTestStore.grade;
                if(typeof(editData[0])!='undefined') thisgrade=editData[0]['GradeID']
                $('#grade').html('<option value="">请选择年级</option>'+$.CustomTestStore.addData(datas['grade'],{
                        val:'GradeID',
                        text:'GradeName',
                        isSelected : true,
                        selectedById:thisgrade
                    }
                ));
                $('#knowledge').html('<option value="">请选择主干知识点</option>'+$.CustomTestStore.addData(datas['knowledge'],{val:'KlID',text:'KlName'}));
            },'json');
        }
        $('.knowledge-select-change-container').SelectChangeHandle({
            classname:'.selection',
            subjectid:$.CustomTestStore.subjectID,
            url:U(url+'/getData'),
            kv : 'KlID',
            vv : 'KlName',
            identify : 'knowledge'
        });
        
        var uploader = WebUploader.create({
            // swf文件路径
            swf: '/Public/plugin/webuploader/Uploader.swf',
            // 文件接收服务端。
            server: U(url+'/fileupload'),
            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: '#picker',
            // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
            resize: false
        });
        // 当有文件被添加进队列的时候
        uploader.on( 'fileQueued', function( file ) {
            $('#thelist').append( '<div id="' + file.id + '" class="item">' +
                '<h4 class="info">' + file.name + '</h4>' +
                '<p class="state">等待上传...</p>' +
            '</div>' );
        });
        // 文件上传过程中创建进度条实时显示。
        uploader.on( 'uploadProgress', function( file, percentage ) {
            var $li = $( '#'+file.id ),
                $percent = $li.find('.progress .progress-bar');

            // 避免重复创建
            if ( !$percent.length ) {
                $percent = $('<div class="progress progress-striped active">' +
                  '<div class="progress-bar" role="progressbar" style="width: 0%">' +
                  '</div>' +
                '</div>').appendTo( $li ).find('.progress-bar');
            }

            $li.find('p.state').text('上传中');

            $percent.css( 'width', percentage * 100 + '%' );
        });

        uploader.on( 'uploadSuccess', function( file ,response) {
            var input ='';
            var url=response['data']['url'];
            if(typeof(url)!='undefined' && url!='') input='<input name="url[]" class="uploadUrl" type="hidden" value="'+url+'" />';
            var del='<sapn class="delHang">X</span>';
            $( '#'+file.id ).find('p.state').html(response['data']['msg']+del+input);
        });

        uploader.on( 'uploadError', function( file ,response) {
            $( '#'+file.id ).find('p.state').text('上传出错');
        });

        uploader.on( 'uploadComplete', function( file ,response) {
            $( '#'+file.id ).find('.progress').fadeOut();
        });
        $('#ctlBtn').on( 'click', function() {  
                uploader.upload();
                return false;
        });
        $('.delHang').live('click',function(){
            var par=$(this).parent().parent();
            if(confirm('确认删除'+par.find('.info').html()+'?')){
                par.remove();
            }
        });
        $('#save').live('click',function(){
            var data={};
            data['Act'] = act;
            data['VerifyCode'] = $('#verifyCode').val();
            data['MID'] = $('#MID').val();
            data['SubjectID'] = $.CustomTestStore.subjectID;
            var grade = $('#grade').val();
            if(!grade){
                grade = 0;
            }
            data['GradeID'] = grade;
            var source = $('#MName').val();
            if(!source){
                source = '';
                $.CustomTestStore.showDialog('msgdiv','错误提示',500,'微课名称不能为空',2);
                $('#source').focus();
                return false;
            }
            data['MName'] = source;
            data['KlID'] = [];
            var knowledge = $(".KlID_inputs");
            knowledge.each(function(){
                data['KlID'].push($(this).val());
            });
            if(data['KlID'].length == 0){
                $.CustomTestStore.showDialog('msgdiv','错误提示',500,'请选择知识点',2);
                $('#knowledge').focus();
                return false;
            }
            data['Url'] = [];
            var uploadUrl = $(".uploadUrl");
            uploadUrl.each(function(){
                data['Url'].push($(this).val());
            });
            if(data['Url'].length == 0){
                $.CustomTestStore.showDialog('msgdiv','错误提示',500,'请上传微课。',2);
                return false;
            }
            data['Remark'] = $('#remark').val();
            
            //载入中
            $.myDialog.normalMsgBox('msgbox','保存微课',450,"数据保存中，请稍候...",4);
            $.post(U($.CustomTestStore.url+'/save'),data,function(result){
                if($.myCommon.backLogin(result)==false){
                    return false;
                }
                var msg = result['data'];
                        $.CustomTestStore.showDialog('msgdiv','提示',500,msg,3,'继续添加','查看微课');
                        $('.normal_yes').click(function(){
                            window.location.href = U('Custom/MicroClass/add');
                        });
                        $('.normal_no').click(function(){
                            window.location.href = U('Custom/MicroClass/index');
                        });
            });
        });
    });
</script>
</body>
</html>