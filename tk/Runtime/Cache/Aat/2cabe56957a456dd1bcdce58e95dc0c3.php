<?php if (!defined('THINK_PATH')) exit();?><div class="top">
    <div class="top_left fl"></div>
    <div class="top_cen fc" style="font:'微软雅黑'">
        <div class="fl pl7 chapterTitle">请选择所学的教材</div>
        <a class="fr an_close" href="javascript:;"></a>
    </div>
    <div class="top_right fr"></div>
</div>
<div class="zj userChapterC">
    <div class="tab">
        <div class="tabnav01">
        </div>
        <div class="tabPanel">
        </div>
    </div>
    <div style="display:none;color: rgb(180, 5, 4); text-align: center;" class="errorMsg"></div>
    <div class="an03 pt20 mc saveChapterID"> <a href="javascript:;">保 存</a></div>
</div>
<div class="bot">
    <div class="bot_left fl"></div>
    <div class="bot_cen fc"></div>
    <div class="bot_right fr"></div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var AatUserChapter = {
            c:$('.userChapterC'),//container 自身
            dialog:function(){
                var c = this.c;
                //显示标题
                var subjectName = $('.xk_this').find('a').html();
                $('.chapterTitle').html(subjectName+'-请选择所学的教材');
                //获取初始化的数据
                $.post(U('Aat/Chapter/getType'),{times:Math.random()},function(e){
                    if(e.status == 1){
                        var tab = '',tabPanel = '',className='',firstID = '';
                        $.each(e.data,function(i,k){
                            if(i == 0){
                                firstID = k.chapterID;
                                c.find('.tabnav01').attr('data',firstID);
                                className = 'this';
                            }else{
                                className = '';
                            }
                            tab += '<a href="javascript:;" data=".tabPanel'+ k.chapterID +'" chapterID="'+k.chapterID+'" class="'+className+'">'+ k.chapterName +'</a>';
                            tabPanel += '<div class="tabPanel'+ k.chapterID +'" style="display:none;">加载中...</div>';
                        });
                        c.find('.tabnav01').html(tab);
                        c.find('.tabPanel').html(tabPanel);
                        //默认显示第一个tabPanel内容
                        c.find('.tabPanel'+firstID).show();
                        //填充第一个版本的教材
                        $.post(U('Aat/Chapter/getBook'), {id: firstID,times:Math.random()}, function (e) {
                            var books = '';
                            var bookStr=','+e.data.selectedBook+',';
                            $.each(e.data.book,function(i,k){
                                if(bookStr.indexOf(','+k.chapterID+',')!=-1){
                                    books += '<span class="blockSpan blockSpanSelected" data="'+ k.chapterID +'" study="1">'+ k.chapterName +'</span>';
                                }else{
                                    books += '<span class="blockSpan" data="'+ k.chapterID +'" study="0">'+ k.chapterName +'</span>';
                                }
                            });
                            c.find('.tabPanel'+firstID).html(books);
                        });
                    }
                });
            },
            tab:function(){
                var c = this.c;
                c.find('.tab').aTab({
                    tab: '.tabnav01',
                    afterClick: function (item) {
                        //对第一个tab的内容进行获取数据
                        var id = item.attr('chapterID');
                        c.find('.tabnav01').attr('data',id);
                        $.post(U('Aat/Chapter/getBook'), {id: id,times:Math.random()}, function (e) {
                            var book = '';
                            var bookStr=','+e.data.selectedBook+',';
                            $.each(e.data.book,function(i,k){
                                if(bookStr.indexOf(','+k.chapterID+',')!=-1){
                                    book += '<span class="blockSpan blockSpanSelected" data="'+ k.chapterID +'" study="1">'+ k.chapterName +'</span>';
                                }else{
                                    book += '<span class="blockSpan" data="'+ k.chapterID +'" study="0">'+ k.chapterName +'</span>';
                                }
                            });
                            c.find('.tabPanel'+ id).html(book);
                        });
                    }
                });
            },
            clickBook:function(){
                var c = this.c;//container 自身
                c.on('click','.blockSpan',function(){
                    var self = $(this);
                    var study = self.attr('study');
                    if(study == 0){
                        //当前没有选中
                        self.addClass('blockSpanSelected').attr('study',1);
                    }else{
                        //当前选中
                        self.removeClass('blockSpanSelected').attr('study',0);
                    }
                });
            },
            save:function(){
                var c = this.c;//container 自身
                var chapterID = [];
                var errorMsg = c.find('.errorMsg');
                var submit = c.find('.saveChapterID');

                submit.click(function(){
                    //检测是否选择学科
                    if(!AatCommon.getSubjectID()){
                        errorMsg.html('请先选择学科！').show();
                        return false;
                    }
                    var typeID = c.find('.tabnav01').attr('data');
                    //只对当前栏目下的书本做提交
                    c.find('.tabPanel'+typeID).find('.blockSpan[study=1]').each(function(i,k){
                        chapterID.push($(k).attr('data'));
                    });
                    submit.find('a').html('保 存 中...');
                    errorMsg.hide();
                    $.post(U('Aat/Chapter/update'),{chapterIDString:chapterID,times:Math.random()},function(e){
                        submit.find('a').html('保 存');
                        if(e.status == 1){
                            $('.userChapter').aBox('close');
                        }else{
                            errorMsg.html(e.data).show();
                        }
                    });
                });
            },
            init:function(){
                this.c.unbind();
                this.dialog();
                this.tab();
                this.clickBook();
                this.save();
            }
        };
        AatUserChapter.init();
    });
</script>