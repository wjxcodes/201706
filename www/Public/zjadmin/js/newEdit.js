jQuery.newEdit={
    init:function(){
        this.initBox();
        this.testTypeAbout();
        this.attrAbout();
        this.testAbout();
        this.dataSave();
    },
    //宽高初始化
    initBox:function(){
        var _height = $(window).height()-80;
        //var boxWidth = 0;
        var wrapWidth = 0;
        //$('.nr_box a').each(function(){
        //    boxWidth += $(this).outerWidth()+8;
        //});
        var windowWidth = $(window).width();
        if(windowWidth<1000){
            wrapWidth = windowWidth-20;
        }else{
            wrapWidth = 1000;
        }
        if(wrapWidth<750){
            wrapWidth=750;
        }
        var marginWidth=wrapWidth-370;
        var h=_height-60;
        $('#popup_message').height(_height);
        $('#wrap').width(wrapWidth).height(_height);
        $('.top_nr_box').height(h).width(wrapWidth);
        $('.main_left').width(marginWidth-2);
        $('.main_left table').width((marginWidth-2)*0.98);
        $('.main_left table').css({'table-layout':'fixed','word-break':'break-all'});
        $('.styl_box').height(h);
        $('.main_right').height(h).width(wrapWidth-marginWidth);
    },
    //左侧测试类型相关操作
    testTypeAbout:function(){
        var y=0;
        //计算宽度
        $('#getWidth').live('click',function(){
            y=1;
            var testid=$('#TestID').val();
            $.post(U("Test/Test/getOptionWidth"),{'id':testid,'style':1,'m':Math.random()},function(msg){
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
    },
    //通用添加数据
    autoAddAttr:function(name,input,tag){
        if($('.select'+name).last().val().indexOf(tag)==-1){
            alert('请选择正确的知识点');
            return false;
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
    },
    //属性标注相关操作
    attrAbout:function(){
        var input='<div>#str# <span class="delhang">x</span><input class="kl" name="kl[]" type="hidden" value="#value#"/></div>';
        var inputcp='<div>#str# <span class="delhang">x</span><input class="cp" name="cp[]" type="hidden" value="#value#"/></div>';
        var inputskill='<div>#str# <span class="delhang">x</span><input class="skill" name="skill[]" type="hidden" value="#value#"/></div>';
        var inputcapacity='<div>#str# <span class="delhang">x</span><input class="capacity" name="capacity[]" type="hidden" value="#value#"/></div>';
        // 章节选择
        $('.selectChapter').chapterSelectChange('/Test/Test');
        //知识点选择
        $('.selectKnowledge').knowledgeSelectChange('/Test/Test');
        //技能选择
        $('.selectSkill').skillSelectChange('/Test/Test');
        //能力选择
        $('.selectCapacity').capacitySelectChange('/Test/Test');
        
        //添加技能
        $('#addSkill').live('click',function(){
            $.newEdit.autoAddAttr('Skill',inputskill,'t');
        });
        //添加能力
        $('#addCapacity').live('click',function(){
            $.newEdit.autoAddAttr('Capacity',inputcapacity,'t');
        });
        //添加知识点
        $('#addkl').live('click',function(){
            $.newEdit.autoAddAttr('Knowledge',input,'t');
        });
        //添加章节
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
        //系统提示按键(载入默认章节)
        $('#adddcp').live('click',function(){
            var result='';
            $('.kl').each(function(){
                result += $(this).val()+",";
            });
            var kl=result.substring(0, result.length-1);
            var testid=$('#TestID').val();
            $.get(U('Test/Test/getchapter?kl='+kl+'&id='+testid+'&time='+Math.random()),function(msg){
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
        //关闭按钮相关操作
        $('.delhang').live('click',function(){
            $(this).parent().remove();
        });
        $('.delhang').live('mouseover',function(){
            $(this).css({'background-color':'#f00','color':'#fff'});
        });
        $('.delhang').live('mouseout',function(){
            $(this).css({'background-color':'#fff','color':'#f00'});
        });
    },
    //试题打分相关操作
    testAbout:function(){
        //客观主管打分切换
        $('#kg').live('click',function(){//客观
            $('.zgdf').hide();
            $('.kgdf').show();
            changext(1);
        });
        $('#zg').live('click',function(){//主观
            $('.kgdf').hide();
            $('.zgdf').show();
        });
        $('.xt_title').live('click',function(){
            var idx = $(this).attr('id').replace('xt','');
            changext(idx);
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
        //主观打分难度值事件
        $('#Diff').live('keydown',function(e){
            if(e.keyCode==13){
                $('#datasave').click(); //处理事件
            }
        });
    },
    //保存事件
    dataSave:function(){
        var x=0;
        var sdata=0;
        //保存
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
            var score = $('#score').val();
            if(score && /[^\d|,|(?=\d{1,2}\.\d{1})]/.test(score)){
                alert('分值必须为英文逗号分隔是数字或者纯数字');
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
                    url: U("Test/Test/save"),
                    data: "TestID="+testid+"&TypesID="+typesid+"&skill="+skill+"&capacity="+capacity+"&kl="+kl+"&cp="+cp+"&SpecialID="+specialid+"&DocID="+docid+"&Mark="+mark+"&Status="+status+"&Remark="+remark+"&DfStyle="+dfstyle+"&Diff="+diff+'&IfChoose='+choose+'&ChooseList='+chooselist+'&OptionWidth='+optionwidth+'&OptionNum='+optionnum+'&Score='+score,
                    success: function(msg){
                        //权限验证
                        x=0;
                        if(checkPower(msg)=='error'){
                            return false;
                        }
                        alert('保存成功！');
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
    }
}