$.CustomTestList = {
    page : 1,
    lock : false,
    subjectID : '',
    us : null,
    init : function(){
        $(document).bind("selectstart",function(){return false;});
        //默认科目
        this.subjectID=Cookie.Get("SubjectId");
        this.us = this.getSearcher();
        Types=parent.Types;
        this.showAttributes();
        $('.ui-resizable-e').mousedown(function(e){
            var x = e.pageX;
            var z =$('#leftdiv').width();
            $(document).bind('mousemove', function(e){
                var c = parseInt(z) + (e.pageX - x);
                if(c<200) c=200;
                if(c>400) c=400;
                $('#leftdiv').css('width',c+"px");
                $('#rightdiv').css('left',c+"px");
                $("#rightdiv").width($(window).width()-$("#leftdiv").outerWidth());
            });
            $(document).mouseup(function(a) {
                $(document).unbind('mousemove');
            });
        });
        // this.selectPageIcon();
        this.dataOtherContent();
        this.edit();
        this.del();
        $.myTest.showTestEvevt();
        this.setOriginality();

         $('#emptybasket').live('click',function(){
            if(confirm('确认清空所有试题？')){
                $('.delques1').each(function(){
                    $(this).trigger('click');
                });
                $('#quescountdetail').find('tr').each(function(){
                    var td = $(this).find('td');
                    var typename = td.eq(0).attr('title');
                    editData.deltypetest(typename);
                    var num = parseInt(td.eq(2).html().replace('题',''));
                    $.myTest.updateMainTypes(num*-1, typename);
                });
                $.CustomTestList.loadTestBasket();
            }
        });

        this.loadTestBasket();

        $('.delques1').live('click',function(){
            var tmp_str=editData.selecttest($(this).attr('quesid'));
            var testid=$(this).attr('quesid');
            if(tmp_str){
                editData.deltest($(this).attr('quesid'));
                $.myTest.updateMainTypes(0-$(this).attr('childnum'),tmp_str);
                $(this).addClass('addquessel1').removeClass('delques1').parent().removeClass('added');
                $(this).find('.iconfont').html('加入试卷');
                $(this).next().addClass('selmore1');
            }
        });

        $('.selmore1').live('click',function(){
            var typemsg=editData.gettypename();
            var testid=$(this).attr('testid');
            var num=$(this).attr('childnum');
            var qyid=$(this).attr('qyid');
            var len=typemsg.length;
            var idname='testinsert';
            var i;
            var tmp_str='';
            tmp_str+='<div><div class="add-test-content" id="tplAddTest" >请选择所加入：<select name="testtypemsg" id="testtypemsg">'+
                '<option value="" text="reempty">请选择</option>';
            for(i=0;i<len;i++){
                tmp_str+='<option value="'+typemsg[i][0]+'">'+typemsg[i][1]+'</option>';
            }
            tmp_str+='</select>';
            tmp_str+='<input type="hidden" name="testid" id="thistestid" value="'+testid+'">';
            tmp_str+='<input type="hidden" name="num" id="num" value="'+num+'">';
            tmp_str+='<input type="hidden" name="qyid" id="qyid" value="'+qyid+'"></div></div>';
            var typedivid='';
            var typename='';
            $('#testtypemsg').live('change', function(){
                typedivid = $(this).val();
                typename = $(this).find('option:selected').text();
            });
            layer.open({
                type: 1,
                title: '选入试题【编号：'+testid+'】入栏',
                area: ["450px", "180px"],
                shadeClose: true,
                btn: ["确定", "取消"],
                content: tmp_str,
                yes:function(){
                    var qyid=$('#qyid').val();
                    var num=$('#num').val();
                    if(typedivid==''){
                        alert('您还没有选择题型!',1);
                        return false;
                    }
                    //试题ID 小题数 题型名称 题型ID
                    //检验添加的试题是否超出
                    var result= $.myTest.checkIfOver(qyid,typename);
                    if(!result[0]){
                        layer.open({
                            title : '提示',
                            area : ['200px', '150px'],
                            btn : ['确定'],
                            content : '题型【'+result[1]+'】的题数已超出限制数量！',
                            yes : function(){
                                layer.close(2);
                            }
                        });
                        return false;
                    }
                    if(!editData.addtest(testid,num,typename,qyid))
                        return false;
                    $.CustomTestList.appendTest($('#quesselect'+testid), num, typename);
                    layer.closeAll();
                }
            })
        });

        $('.addquessel1').live('click',function(){
            var that = $(this);
            var typeid=that.attr('qyid');
            var typename=that.attr('qyname');
            var result= $.myTest.checkIfOver(typeid,typename);
            if(!result[0]){
                layer.open({
                    title : '提示',
                    area : ['200px', '150px'],
                    btn : ['确定'],
                    content : '题型【'+result[1]+'】的题数已超出限制数量！',
                    yes : function(){
                        layer.close(2);
                    }
                });
                return false;
            }else{
                if(!editData.addtest(that.attr('quesid'),that.attr('childnum'),that.attr('qyname'),that.attr('qyid'),that.attr('score'),that.attr('choosenum'),that.attr('choosetype'))){
                    return false;
                }
                $.CustomTestList.appendTest(that, that.attr('childnum'), typename);
            }
        });
    },
    //添加试题
    appendTest : function(that, num, name, typeid){
        $.myTest.updateMainTypes(num, name);
        var testid=that.attr('quesid');
        that.addClass('delques1').removeClass('addquessel1').parent().addClass('added');
        that.find('.iconfont').html('移除试卷');
        that.next().removeClass('selmore1');
    },
    //加载试题栏
    loadTestBasket : function(){
        var tmpData=editData.gettestlist();
        if(!tmpData){
            $('#quescountdetail').find('tbody').html('');
            $('#quescount').html('0');
        }
        for(var i=0; i<tmpData.length; i++){
            $.myTest.updateMainTypes(tmpData[i][2], tmpData[i][0]);
        }
    },

    getSearcher : function(){
        var that = this;
        return $.extend(UnionSearch, {
            url : U('Custom/CustomTestStoreN/getTestList'),
            params : {
                diff : 0,
                knowledge : 0,   
                order :  'def',
                subject : that.subjectID,
                types :  0,
                page : 1
            },
            requestBeforeHandler : function(id){
                if(that.lock){
                    alert('数据加载中，请稍候！');
                    return false;
                }
                that.lock = true;
                if('page' != id){
                    this.params.page = 1;
                }
                this.params['rand'] = Math.random();
                //that.clearpage();
                $('#queslistbox').html('<p class="list_ts"><span class="ico_dd">试题加载中请稍候...</span></p>');
                return true;
            },
            callback : function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                that.lock = false;
                var total = 0, page = 0, recordTotal = 0;
                if(typeof(data['data']) !== 'string'){
                    page = that.us.params.page;   //当前页
                    recordTotal = data['data'][1];//总记录数
                    total = Math.ceil(recordTotal / data['data'][2]); //总页数
                }
                that.showPage(total,page,recordTotal);
                that.us.addElement('#pagediv .page', 'click', {
                    id : 'page', 
                    param : 'val'
                });
                var template = $('#template');
                if(data['data'][0] && data['data'][0].length == 0){
                    $('#queslistbox').html();
                }else{
                    var temp = new Template();
                    $('#queslistbox').html(temp.render(template.html(),data['data']));
                }
            }
        });
    },

    //校本题库在列表页面初期，需加载的部分属性
    showAttributes : function(){
        var that = this;
        if(that.subjectID){
            var str='';
            //显示学科及属性
            $.post(U('Custom/CustomTestStoreN/getZsdInit'),{'id':that.subjectID,'m':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    that.lock=false;
                    return false;
                }
                $('#loca_text').html('&nbsp;校本试题&nbsp;>&nbsp;'+data['data'][0]);
                var template = new Template();
                $('#filterbox').html(template.render($('#selectionTemplate').html(),data));
                $('#treecon').html(template.render($('#treeTemplate').html(),data));

                that.us.addElement('#allknowledge', 'click', {
                    id : 'knowledge',
                    param : 'val'
                });

                //知识点分页查询
                that.us.addElement('#treecon .treeTarget', 'click', {
                    id : 'knowledge', 
                    param : 'val'
                });

                //题型查询
                that.us.addElement('#questypeselect .target', 'click', {
                    id : 'types', 
                    param : 'val',
                    beforeHandler : function(){
                        $(this).addClass('checked').siblings('.target').removeClass('checked');
                        return true;
                    }
                });

                //难度查询
                that.us.addElement('#quesdiffselect .target', 'click', {
                    id : 'diff', 
                    param : 'val',
                    beforeHandler : function(){
                        $(this).addClass('checked').siblings('.target').removeClass('checked');
                        return true;
                    }
                });

                //排序查询
                that.us.addElement('#list_px .target', 'click', {
                    id : 'order',
                    param : 'val',
                    beforeHandler : function(){
                        var that = $(this);
                        var order = that.attr('val');
                        var str = order.replace(/Asc|Desc/g, '');
                        order = (order == str+'Asc' ? str+'Desc' : str+'Asc');
                        str = order.replace(str, '');
                        var css = {'background-position':'-7px 0px'};
                        if(str == 'Asc'){
                            css = {'background-position':'0px 0px'};
                        }
                        that.attr('val' ,order).find('b').css(css);
                        that.addClass('button_current').siblings('.target').removeClass('button_current');
                        return true;
                    }
                });

                //前后翻页查询
                that.us.addElement('#pagediv .pageTarget','click', {
                    id : 'page',
                    param : 'val',
                    beforeHandler : function(){
                        var current = $(this);
                        //获取当前页
                        var currentPage = parseInt($('#curpage').html());
                        //向前翻页，同时页数当前页数不等于1时
                        if(current.hasClass('prev_page') && currentPage > 2){
                            currentPage = currentPage - 1;
                            current.attr('val', currentPage);
                        }else if(current.hasClass('next_page')){
                            var total = parseInt($('#pagecount').html())
                            if(currentPage <= total - 1){
                                currentPage = currentPage + 1;
                                current.attr('val', currentPage);
                            }
                        }
                        that.us.params.page = currentPage;
                        return true;
                    }
                });

                $('#questypeselect .target').eq(0).trigger('click');
            });
        }
    },

    //点击试题内容时，显示的相关的试题答案，解析等内容
    dataOtherContent : function(){
        $('.otherContent').live('click',function(){
            var that = $(this);
            var tid=that.attr('tid');
            if(tid=='0') return;
            var otherContentContainer = $('#testid'+tid);
            if(otherContentContainer.html() != ''){
                if(that.html() == '显示解析'){
                    that.html('隐藏解析');
                    otherContentContainer.show();
                }else{
                    that.html('显示解析');
                    otherContentContainer.hide();
                }
                return false;
            }
            otherContentContainer.html('加载中.........');
            $.post(U('Custom/CustomTestStoreN/getOneTestById'),{'id':tid,'width':500,'s':Math.random()},function(data){
                    if($.myCommon.backLogin(data)==false){
                        return false;
                    };
                    if(data['data'][0]=='success'){
                        var template = new Template();
                        $('#testid'+tid).html(template.render($('#otherContent').html(), data));
                        that.html('隐藏解析');
                    }else{
                        otherContentContainer.html('');
                        alert(data['data']);
                    }
                });
        });
    },
    //试题编辑
    edit : function(){
        $('.customTestEdit').live('click', function(){
            var that = $(this);
            var id = that.attr("testid");
            //编辑前进行数据验证
            $.get(U('Custom/CustomTestStoreN/editVerify?testid='+id),function(response){
                if($.myCommon.backLogin(response)==false){
                    return false;
                };
                window.location.href = U('Custom/CustomTestStoreN/edit?testid='+id+'&verify='+response['data']);
            })
        });
    },
    //删除试题
    del : function(){
        $('.deletion').live('click',function(){
            if(!window.confirm('您确定删除该试题？')){
                return false;
            }
            var that = $(this);
            var data = {};
            data['testid'] = that.attr('testid');
            data['verify'] = that.attr('verify');
            var tmp_str=editData.selecttest(data['testid']);
            if(tmp_str){
                if(editData.deltest(data['testid'])){
                    updatemaintypes(0-$(this).attr('childnum'),tmp_str);
                }
            }
            $.post(U('Custom/CustomTestStoreN/del'),data,function(result){
                if($.myCommon.backLogin(result)==false){
                    return false;
                };
                var data = result['data']
                if(data == 'success'){
                    alert('试题已删除！');
                    $.Searcher.targetElements[0]['obj'].find('.target').eq(0).trigger('click');
                    return false;
                }
                alert(data);
            })
        });
    },
    //初始化相关分页数据
    clearpage : function(){
        $("#pagelistbox").html('');
        $("#quescount").html('?');
        $("#curpage").html('?');
        $("#selectpageicon").css({display: "none" });
        $("#pagecount").html('?');
    },
    
    showPage : function(total,prepage,recordTotal){
        if(total == 0){
            $('#pagediv').hide();
            return false;
        }
        
        var lastpagelist='<div class="pagebox">';
        var showPageList = 10;
        var index = Math.floor((prepage - 0.123) / showPageList);
        var i = showPageList * index;
        var end = i + showPageList;
        if(prepage > 1){
            lastpagelist+='<a class="page" val="1" href="javascript:void(0);">首页</a>&nbsp;';
        }else{
            lastpagelist+='<span class="disabled">首页</span>&nbsp;';
        }
        if(index > 0){
            lastpagelist += '<a class="page" val="'+ (i) +'" href="javascript:void(0);">上' + showPageList + '页</a>&nbsp;'
        }
        while(++i <= end && i <= total){
            if(i == prepage){
                lastpagelist+='<span class="current">'+ i +'</span>&nbsp;';
            }else{
                lastpagelist+='<a class="page" val="'+ i +'" href="javascript:void(0);">'+i+'</a>&nbsp;';
            }
        }
        if(i <= total){
            lastpagelist+='<a val="'+ (i) +'" href="javascript:void(0);" class="page">下' + showPageList + '页</a>&nbsp;';
        }
        if(prepage < total){
            lastpagelist+='<a class="page" val="'+ total +'" href="javascript:void(0);" title="最后1页">末页</a>';
        }else{
            lastpagelist+='<span class="disabled">末页</span>';
        }
        lastpagelist+='</div>';
        $('#pagediv').html(lastpagelist).show();
        // $("#pagelistbox").html(lastpagelist);
        // $("#quescount").html(recordTotal);
        // $("#curpage").html(prepage);
        // $("#selectpageicon").css({display: (total == 1 ? 'none' : "inline-block")});
        // $("#pagecount").html(total);
    },
    //原创题投稿
    setOriginality:function(){
        $(document).on('click','#setOriginality',function(){
            var testID = $(this).attr('data-testID');
            $.post(U('Custom/CustomTestStoreN/originalityTemplate'),{},function(e){
                if(e.status==1){
                    e.data.testID = testID;
                    var msg = template('originalityTemplate', e.data);
                    layer.open({
                        title : '选择投稿知识点',
                        area : ['750px', '450px'],
                        btn : ['确定','取消'],
                        content : msg,
                        yes : function(){
                            var ttIDDom = $('.ttID:checked');
                            var ttID = ttIDDom.val();
                            var testID = ttIDDom.attr('data-testID');
                            if(!ttID||!testID){
                                layer.open({
                                    title : '失败',
                                    area : ['200px', '150px'],
                                    btn : ['确定'],
                                    content : '请选择您投稿的试题模版！',
                                    yes : function(){
                                        layer.close(2);
                                    }
                                });
                                // $.myDialog.normalMsgBox('msgDiv','失败',500,'请选择您投稿的试题模版！',2);
                                return false;
                            }
                            $.post(U('Custom/CustomTestStoreN/setTestOriginality'),{ttID:ttID,testID:testID},function(e){
                                // if(e.status==1){
                                //     // $.myDialog.tcCloseDiv('originalityMsgDiv');
                                //     // $.myDialog.normalMsgBox('msgDiv','成功',500, e.data,1);
                                // }else{
                                //     // $.myDialog.tcCloseDiv('originalityMsgDiv');
                                //     // $.myDialog.normalMsgBox('msgDiv','失败',500, e.data,2);
                                // }
                                layer.open({
                                    title : (e.status == 1 ? '成功' : '失败'),
                                    area : ['200px', '150px'],
                                    btn : ['确定'],
                                    content : e.data,
                                    yes : function(){
                                        layer.closeAll();
                                    }
                                });
                            });
                        }
                    });
                    // $.myDialog.normalMsgBox('originalityMsgDiv','选择投稿知识点',750,msg,3);
                }else{
                    layer.open({
                        title : '失败',
                        area : ['200px', '150px'],
                        btn : ['确定'],
                        content : e.data,
                        yes : function(){
                            layer.closeAll();
                        }
                    });
                    // $.myDialog.normalMsgBox('msgDiv','失败',500, e.data,2);
                }
            });
        });
        // $(document).on('click','#originalityMsgDiv .normal_yes',function(){
        //     var ttIDDom = $('.ttID:checked');
        //     var ttID = ttIDDom.val();
        //     var testID = ttIDDom.attr('data-testID');
        //     if(!ttID||!testID){
        //         $.myDialog.normalMsgBox('msgDiv','失败',500,'请选择您投稿的试题模版！',2);
        //         return false;
        //     }
        //     $.post(U('Custom/CustomTestStore/setTestOriginality'),{ttID:ttID,testID:testID},function(e){
        //         if(e.status==1){
        //             $.myDialog.tcCloseDiv('originalityMsgDiv');
        //             $.myDialog.normalMsgBox('msgDiv','成功',500, e.data,1);
        //         }else{
        //             $.myDialog.tcCloseDiv('originalityMsgDiv');
        //             $.myDialog.normalMsgBox('msgDiv','失败',500, e.data,2);
        //         }
        //     });
        // });
    }
};