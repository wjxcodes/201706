<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title> - 组卷系统 - 智慧云题库云平台</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="keywords" content="组卷,题库">
        <meta name="description" content="组卷,题库">
        <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet">
        <link type="text/css" href="/Public/default/css/customTest.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet">
        <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="/Public/plugin/tips.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
        <script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
        <script type="text/javascript" src="/Public/default/js/customTest.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>

    </head>
    <body>
        <div id="righttop">
            <div id="categorylocation">
                <span class="nowPath">当前位置：</span>&gt; <span id="loca_text"> <a
                    href="<?php echo U('Custom/CustomTestStore/index');?>">校本题库</a></span> &gt; <span
                    id="loca_text"> <a href="<?php echo U('Custom/CustomTestStore/customNav');?>">添加试题</a></span> &gt; <span
                    id="loca_text"> 文档上传</span>
            </div>
        </div>
        <!--我的文档-头部-->
        <div class="my-doc-top clearfix">
            <div class="upload-area left">
                <form class="uploadForm" action="<?php echo U('Custom/CustomTestStore/docUpload');?>" target="editDocFrame" method="post"
                      enctype="multipart/form-data">
                    <table class="form-table-inner">
                        <tr>
                            <td><span class="area-tit">添加文档：</span></td>
                            <td><input class="file-input" name="doc" type="file"></td>
                            <td><button class="uploadButton nor-btn">上传</button><span class="docname"></span>（word文档1.5M以内）
                                </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="search-area right">
                <form action="<?php echo U('Custom/CustomTestStore/docList');?>" method="post">
                    <table class="form-table-inner">
                        <tr>
                            <td><input name="status" value="0" type="hidden">
                                <span class="area-tit">查询文档：</span></td>
                            <td><input class="search-input" name="title" type="text"></td>
                            <td><button class="search-btn iconfont" title="查询">&#xe607;</button></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
        <!--我的文档-头部-end-->
        <!--文档内容-->
        <div class="my-doc-body content-area">
            <div class="content-list-area">
                <div class="content-list-top clearfix">
                    <ul class="event-target left">
                        <li class="target-handler">待审核</li>
                        <li class="target-handler">审核中</li>
                        <li class="target-handler">审核完成</li>
                        <li class="target-handler">审核失败</li>
                    </ul>
                    <div id="pagtion" class="right clearfix">
                        <div class="left search-result"><span class="num"><b><?php echo ($count); ?></b>条记录</span>|
                            <span class="num">共<b><?php echo ($pageCount); ?></b>页</span></div>
                        <table class="page-area-wrap left">
                            <tr>
                                <td><a class="able" href="<?php echo ($pagtion['p']); ?>">上一页</a>
                                </td>
                                <?php if($pageCount > 0): ?><td>
                                        <select class="page-nav f-song" name="pageNav" id="">
                                            <?php $__FOR_START_18169__=0;$__FOR_END_18169__=$pageCount;for($i=$__FOR_START_18169__;$i < $__FOR_END_18169__;$i+=1){ if($page == ($i+1)): ?><option selected="selected" value="<?php echo ($i+1); ?>">第<?php echo ($i+1); ?>页</option>
                                                <?php else: ?>
                                                    <option value="<?php echo ($i+1); ?>">第<?php echo ($i+1); ?>页</option><?php endif; } ?>
                                        </select>
                                    </td><?php endif; ?>
                                <td><a class="next-page able" href="<?php echo ($pagtion['n']); ?>">下一页</a></td>

                            </tr>
                        </table>
                    </div>
                </div>
                <div class="list-area">
                    <table  class="list-area-table">
                        <tbody>
                        <tr class="list-area-header">
                            <td align="center" width="50">编号</td>
                            <td align="center" width="*">文档名称</td>
                            <td align="center" width="60">学科</td>
                            <td align="center" width="150">最后编辑时间</td>
                            <td align="center" width="150">上传时间</td>
                            <?php if($status == 0): ?><td align="center" width="100">审核</td><?php endif; ?>
                            <td align="center" width="150">操作</td>
                        </tr>
                         <?php if(count($list) == 0): ?><tr>
                                <td style='height:33px;line-height:33px;' colspan="<?php if($status == 0): ?>7<?php else: ?>6<?php endif; ?>" align="center">暂无数据</td>
                            </tr>
                        <?php else: ?>
                            <?php if(is_array($list)): foreach($list as $key=>$record): ?><tr class="list-area-content" duid="<?php echo ($record["DUID"]); ?>">
                                    <td align="center" width="50"><?php echo ($record["DUID"]); ?></td>
                                    <?php if($record["Status"] == 1 or $record["Status"] == 2): ?><td width="*" align="center"><?php echo ((isset($record["Title"]) && ($record["Title"] !== ""))?($record["Title"]):"无"); ?></td>
                                    <?php else: ?>
                                    <td align="center" width="*"><a href='#' title='点击修改文档名' class="modityTitle"><?php echo ((isset($record["Title"]) && ($record["Title"] !== ""))?($record["Title"]):"无"); ?></a></td><?php endif; ?>
                                    <td align="center" width="60"><?php echo ((isset($subjects[$record['SubjectID']]) && ($subjects[$record['SubjectID']] !== ""))?($subjects[$record['SubjectID']]):"暂无"); ?></td>
                                    <td width="100" align="center"><?php echo (date("Y-m-d H:i:s",$record["ModifiedTime"])); ?></td>
                                    <td width="100" align="center"><?php echo (date("Y-m-d H:i:s",$record["AddTime"])); ?></td>
                                    <?php if($status == 0): ?><td class="submit-handle" align="center">
                                        <a href="#" class="submitCheck nor-btn" title="编辑完成后提交到管理员进行审核">提交审核</a>
                                    </td><?php endif; ?>
                                    <td class="other-handle" width="100" align="center">
                                    <a href="<?php echo U('Custom/CustomTestStore/wordDownload',array('id'=>$record['DUID']));?>" class='nor-btn'>下载</a>
                                    <?php if($record["Status"] == 0 or $record["Status"] == 3): ?><a href="#" class='edit nor-btn' title="替换文档">替换</a>
                                    <a href="#" class='del nor-btn'>删除</a><?php endif; ?>
                                    <?php if($record["Status"] == 0): ?><br/><?php endif; ?>
                                    </td>
                                </tr><?php endforeach; endif; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--文档内容-end-->
        <!-- 隐藏的iframe，用于文档上传 -->
        <iframe scrolling="no" style="display:none;" id="editDocFrame" name="editDocFrame" frameborder="0"></iframe>
        <!-- 此处为弹框需获取的html结构 -->
        <div id="editDoc" style="display:none;">
            <form class="uploadForm" action="<?php echo U('Custom/CustomTestStore/docUpload');?>" target="editDocFrame" method="post"
                  enctype="multipart/form-data">
                <input name="id" value="%id%" type="hidden">
                <table style="border:1px solid #ccc;" border="1" cellpadding="5" cellspacing="0" width="100%">
                    <tbody>
                    <tr>
                        <td align="right" width="70">文档编号</td>
                        <td>%duid%</td>
                    </tr>
                    <tr>
                        <td align="right" width="70">文档名称</td>
                        <td>%docname%</td>
                    </tr>
                    <tr>
                        <td colspan="2"><input name="doc" type="file">&nbsp;
                            <button type="button" class="uploadButton">保存</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <script type='text/javascript'>
            $(document).ready(function(){
                showTip();
            });
            var showTip =function(){
                $.CustomTestStore.showTip('您当前正在编辑协同命制试题,新上传文档将标注为原创题文档！');
            };
            var status = "<?php echo ($status); ?>";
            if(!status){
                status = 1;
            }
            var targetHandler = $('.event-target .target-handler');
            targetHandler.eq(status).addClass('event-target-this');
            targetHandler.live('click', function(){
                window.location.href=U("Custom/CustomTestStore/docList?status="+$(this).index());
            })

            $('.page-nav').change(function(){
                var that = $(this);
                var val = that.val();
                var href = that.parent().prev().find('a').attr('href');
                href = href.replace(/-p-(\d+)-/g, '-p-'+val+'-');
                window.location.href=href;
            });

            //编辑文档名称
            $('.modityTitle').live('click', function(){
                var that = $(this);
                //隐藏其他的文本框
                that.parents('tr').siblings('tr').find('.modityTitleSave').trigger('click');
                var title = that.html();
                that.parent().html("<input type='text' title='"+title+"' class='modityTitleText' value='"+title+"' maxlength='100'><button class='modityTitleSave'>保存</button>");
                return false;
            })

            //编辑文档名称保存
            $('.modityTitleSave').live('click', function(){
                var that = $(this);
                var text = that.prev();
                var title = text.attr('title');
                var val = text.val();
                if(title != val){
                    if(val == ''){
                        alert('文档名不能为空！');
                        text.val(title);
                        return false;
                    }
                    var data = {
                        'id' : that.parents('tr').attr('duid'),
                        'docname' : val
                    }
                    $.post(U('Custom/CustomTestStore/docNameUpdate'), data, function(result){
                        if($.myCommon.backLogin(result)==false){
                            return false;
                        }
                        that.parent().html("<a href='#' title='点击修改文档名' class='modityTitle'>"+val+"</a>");
                    });
                }else{
                    that.parent().html("<a href='#' title='点击修改文档名' class='modityTitle'>"+val+"</a>");
                }
            });
            
            //提交管理员进行审核
            $('.submitCheck').live('click', function(){
                if(!window.confirm('确定将文档提交至管理员审核文档？')){
                    return false;
                }
                var _parent = $(this).parents('tr');
                var id = _parent.attr('duid');
                $.post(U('Custom/CustomTestStore/submitCheck'), {'id':id}, function(result){
                    if($.myCommon.backLogin(result)==false){
                        return false;
                    }
                    alert('文档已提交审核，请耐心等待！');
                    _parent.remove();
                });
            })
            
            //修改文档
            $('.edit').live('click', function(){
                var _parent = $(this).parents('tr');
                var docname = _parent.find('td').eq(1).find('a').html();
                var html = $('#editDoc').html();
                var id = _parent.attr('duid');
                html = html.replace('%duid%', id).replace('%docname%', docname).replace('%id%', _parent.attr('duid'));
                $.myDialog.normalMsgBox('test','修改文档',650,html,5);
            });

            //删除文档
            $('.del').live('click', function(){
                if(!window.confirm('确定要删除该文档？')){
                    return false;
                }
                var that = $(this);
                var _parent = that.parents('tr');
                var data = {
                    'id' : _parent.attr('duid')
                };
                $.post(U('Custom/CustomTestStore/docDel'), data, function(result){
                    if($.myCommon.backLogin(result)==false){
                        return false;
                    }
                    alert('删除成功！');
                    _parent.remove();
                });
                return false;
            });

            var repeatUploadLock = false;
            //文档上传验证
            $('.uploadButton').live('click', function(){
                if(repeatUploadLock){
                    return false;
                }
                var that = $(this);
                if(that.prev().val() == ''){
                    alert('请选择文档');
                    return false;
                }
                repeatUploadLock = true;
                that.parents('form').submit();
                that.prev().val('');
            });

            resize();
            $(window).resize(function(){
                resize();
            });

            $(document).click(function(e){
                e = e || event;
                var element = e.target || e.srcElement;
                var mark = null;
                while(element){
                    if(element.className == 'modityTitleSave' || element.className == 'modityTitleText'){
                        mark = element;
                        break;
                    }
                    element = element.parentNode;
                }
                var modityTitleSaveEle = $('.modityTitleSave');
                if(!mark && modityTitleSaveEle.length > 0){
                    var prev = modityTitleSaveEle.prev();
                    prev.val(prev.attr('title'));
                    modityTitleSaveEle.trigger('click');
                }
            });

            function uploadCb(data){
                repeatUploadLock = false;
                $('#editDocFrame').attr('src', '');
                data = data[0];
                if(typeof data === 'string'){
                    alert(data);
                    $('.tcClose').trigger('click');
                }else{
                    var obj = null;
                    var list = $('.list-area .list-area-content');
                    list.each(function(){
                        var that = $(this);
                        if(that.attr('duid') == data['DUID']){
                            obj = that;
                            return;
                        }
                    });
                    if(0 == list.length){
                        window.location.href=U('Custom/CustomTestStore/docList');
                        return;
                    }
                    if(!obj){
                        list = list.eq(0);
                        obj = list.clone();
                        list.before(obj);
                    }else{
                        $('.tcClose').trigger('click');
                    }
                    flushList(obj, data);
                    alert('保存成功！');
                }
            }

            var subjects = new Object(<?php echo ($subjectsJSON); ?>)
            function flushList(obj, data){
                obj.attr('duid', data['DUID']);
                var td = obj.find('td');
                td.eq(0).html(data['DUID']);
                if(!data['Title']){
                    data['Title'] = '无';
                }
                td.eq(1).find('a').html(data['Title']);
                td.eq(2).html(subjects[data['SubjectID']]);
                td.eq(3).html(data['IsTpl']?'是':'否');
                td.eq(4).html(data['ModifiedTime']);
                td.eq(5).html(data['AddTime']);
            }

            function resize(){
                $('.list-area').height($(window).height() - 202);
            }
        </script>
    </body>
</html>