/**
 * 模板组卷JS
 */
jQuery.dirTemplate={
    tempContent:'', //存储模板内容
    subjectID:Cookie.Get("SubjectId"),
    init:function(){
        var self=this;
        self.layoutOne();//第一页面函数
        self.layoutTwo();//第二页面函数
        self.layoutThree();//第三页面函数
        self.initDivXdBox();//初始化

        $.myTest.showTestEvevt(); //载入试题事件
        $(window).resize(function() { $.dirTemplate.initDivXdBox();});
    },
    layoutOne:function(){
        $('#dir1').css('display','block');
        this.showLayoutOne();//展示第一个页面
        this.tempAttrClick();//与目标属性相关的点击事件
        this.checkedChangeColor();//设置选中项变色
        this.delThisTpl();//删除个人模板
        this.skipTwo();//跳转到第二页面
    },
    layoutTwo:function(){
        this.tempClick();//与模板相关的点击事件
        this.testClick();//与试题相关的点击事件
        this.scoresClick();//与分数相关的点击事件
        this.showLayoutTwo();//展示第二页面
        this.skipOne();//跳转第一个页面
        this.skipThree();//跳转到第三个页面
        this.upMove();//题型上移
        this.downMove();//题型下移
        this.setEmptyTest();//试卷设置为空时，给出提示
        this.knowledgeTabChange("#tabs","#tab_conbox","click",'');//知识点选项卡切换
    },
    layoutThree:function(){
        this.skipFour();//跳转到第四版试卷预览
    },
    /**
     * 模板组卷页面初始化
     */
    initDivXdBox:function(){
        $("#wizardbox").height($(window).height()-$('#righttop').outerHeight());
        $("#wizardbox").css({'overflow-x':'hidden'});
        $('.step').each(function(){
            if($(this).css('display')=='block'){
                var windwidth=$(window).width();
                var wd = parseInt(windwidth);
                var b = $(window).height();
                var c = $("#righttop").outerHeight();
                var d = $(this).find('div:eq(0)').outerHeight();
                var e = $(this).find('div.last').outerHeight();
                var thisheight=b-d-c-e-30;
                if($(this).find('.dir').height()>=0){
                    $(this).find('.dir').height(thisheight);
                    $(this).find('.dir').css({'position':'relative','overflow-y':'auto','overflow-x':'hidden'});
                }
                if(wd<800){
                    $(this).find('.dir2').css({'width':'800','position':'relative'});
                    $(this).find('.dir').css({'overflow-x':'auto'});
                }else{
                    $(this).find('.dir2').removeAttr('style');
                }
            }
        });
    },
    /**
     * 第一个页面
     */
    showLayoutOne:function(){
        var self=this;
        var gradeArr='';
        $('#loca_text span').html($('#cursubject',window.parent.document).text());
        $('.grademsg').html('<span class="boxloading">年级加载中请稍候...</span>');
        $("input[name='examtype']").eq(0).attr('checked','checked');
        $("input[name='choosetype']").eq(2).attr('checked','checked');
        $("input[name='chooseattr']").eq(0).attr('checked','checked');
        var attrid=$("input[name='examtype']").eq(0).attr('defaultstyle');
        $('input[name="chooseattr"]').each(function(){
            if($(this).val()==attrid){
                $(this).attr('disabled',false);
                $(this).attr('checked',true);
            }
        })
        $("input[name='chooseattr']").eq(1).attr('disabled',false);
        $('.checkall').attr('checked',true);
        $('#checkall').attr('checked',true);
        $('.areaall').attr('checked',true);
        $('#areaall').attr('checked',true);
        //获取年级
        $.ajax({
            type: "POST",
            cache: false,
            url: U('Index/getData'),
            data: {'style':'grade','subjectID':self.subjectID,'times':Math.random()},
            success: function(msg){
                if($.myCommon.backLogin(msg)==false){
                    gradeArr+=msg['data'];
                    return false;
                }
                gradeArr+='<label><input type="checkbox"  name="checkGrade" value="" id="checktype" checked="checked">全选</label> ';
                for(i=0;i<msg['data'].length;i++){
                    gradeArr+='<label><input type="checkbox" name="gradelist[]" checked="checked" class="checktype" value="'+msg['data'][i]['GradeID']+'"/>'+msg['data'][i]['GradeName']+'&nbsp;</label>';
                }
                $('.grademsg').html(gradeArr);
                self.checkGrade();//页面初始化时，试题类型和年级的关系
                $('input:checked').parent().attr('style','color:#00a0e9');
            }
        });
    },
    /**
     * ajax根据条件获取模板
     * 参数1 组卷方式ID
     * 参数2 考试类别ID
     * 参数3 模板ID
     * 参数4 条件
     */
    getTempArr:function(choosetypeid,examtypeid,id,where,showDel){
        if(!where){
            $('.selmb').html('<span class="boxloading">模板加载中请稍候...</span>');
        }else{
            $('.selmb1').html('<span class="boxloading">模板加载中请稍候...</span>');
        }
        if(where){
            examtypeid='';
        }
        var backcolor="";
        var temp='';
        var testtypename=''

        $.ajax({
            type: "POST",
            cache: false,
            url: U('Index/getTemplateList'),
            data: {'choosetypeid':choosetypeid,'examtypeid':examtypeid,'SubjectID':this.subjectID,'times':Math.random()},
            success: function(data){
                if($.myCommon.backLogin(data)==false){
                    $('.selmb').html(data.data);
                    return false;
                };
                if(data['data'][0]['content'] && data['data'][0]['content']!=''){
                    for(var i in data['data'][0]['content']){
                        if(data['data'][0]['content'][i]['TempID']==id){
                            backcolor='color:#00a0e9;border-color:#00a0e9;"';
                        }else{
                            backcolor='';
                        }
                        if(where==0){
                            $('.mbsel').css({'display':'block'});
                            var delButton='';
                            if(showDel==1){
                                if(choosetypeid!=3){
                                    var delButton='<span class="templatelist-cite"><cite>'+data['data'][0]['content'][i]['UpdateTime']+'</cite><b class="delThisTpl" thisName="'+data['data'][0]['content'][i]['TempName']+'" tplID='+data['data'][0]['content'][i]['TempID']+'>X</b></span>'; //设置删除按钮
                                }
                            }
                            temp+='<div class="templatelist elli" mbid='+data['data'][0]['content'][i]['TempID']+' '+backcolor+' title="'+data['data'][0]['content'][i]['TempName']+'">'+'<span class="context elli">'+data['data'][0]['content'][i]['TempName']+'</span>'+delButton+'</div>';
                        }else{
                            temp+='<div class="templatelist1 elli" mbid='+data['data'][0]['content'][i]['TempID']+' '+backcolor+' title="'+data['data'][0]['content'][i]['TempName']+'">'+  data['data'][0]['content'][i]['TempName']+'</div>';
                        }
                        if(where==0){
                            $('.selmb').html();
                            $('.selmb').html(temp);
                        }else{
                            $('.selmb1').html();
                            $('.selmb1').html(temp);
                        }
                    }
                }else{
                    if(!where){
                        $('.selmb').html('<div style="color:#F53131;padding:5px;">没有可选模版</div>');
                    }else{
                        $('.selmb1').html('<div style="color:#F53131;padding:5px;">没有可选模版</div>');
                    }
                }
            }
        })
    },
    // 用户删除自己的模板
    delThisTpl:function(){
        $('.delThisTpl').live('click',function(){
            var tempName=$(this).attr('thisName');
            var tplID=$(this).attr('tplID');
            $.myDialog.normalMsgBox('delCaseTpl','温馨提醒',450,'<div><b class="delTpl"  tplID="'+tplID+'">您确定删除【'+tempName+'】模板？</b></div>',3);
        })
        $('#delCaseTpl .normal_yes').live('click',function(){
            var tplID=$('#delCaseTpl .delTpl').attr('tplID');
            $.post(U('Index/delDirTplByID'),{'tplID':tplID},function(msg){
                if($.myCommon.backLogin(msg)==false){
                    return false;
                };
                var data=msg['data'];
                if(data==1){
                    $('.templatelist').each(function(){
                        if($(this).attr('mbid')==tplID){
                            $(this).remove();
                        }
                    })
                    $('#delCaseTpl .tcClose').click();
                    $.myDialog.showMsg('删除成功！');
                }else{
                    $.myDialog.showMsg('删除失败',1);
                }
            });
        })
    },
    /**
     *全选，全不选
     *@author demo
     *@date 2014年10月21日
     */
    checkAll:function(checkID,inputName){
        if($('#'+checkID).attr('checked')=='checked'){
            $('input[name="'+inputName+'[]"]').each(function(){
                if($(this).attr('disabled')!='disabled'){
                    $(this).attr('checked',true);
                }
            });
        }else{
            $('input[name="'+inputName+'[]"]').each(function(){
                $(this).attr('checked',false);
            });
        }
    },
    /**
     *验证是否全选
     *@author demo
     *@date 2014年10月21日
     */
    checkNums:function(checkID,inputName){
        var checkednum=$('input[name="'+inputName+'[]"]:checked').length;
        var checknum=$('input[name="'+inputName+'[]"]').length;
        var disablenum=$('input[name="'+inputName+'[]"][disabled="disabled"]').length;
        if(checknum==parseInt(checkednum)+parseInt(disablenum)){
            $('#'+checkID).attr('checked',true);
        }else{
            $('#'+checkID).attr('checked',false);
        }
    },
    /**
     * 试题类型和年级的关系
     * @author demo
     * @date 2014年10月21日
     */
    checkGrade:function(){
        this.checkNums('checkall','doctype');//判断试题题型是否全部选中
        //年级和试题类型关系
        var gradeID='';//年级ID参照物
        var grades=[];
        $('input[name="doctype[]"]').each(function(){
            if($(this).attr('checked')=='checked'){
                gradeID+=$(this).attr('thisid')+',';
            }
        });
        if(gradeID==''){
            $("input[name='gradelist[]']").attr('disabled',false);
        }else{
            $("input[name='gradelist[]']").each(function(){
                //循环年级，判断年级ID是否在相同的年级ID里
                if((','+gradeID+',').indexOf(','+$(this).attr('value')+',')==-1){
                    $(this).attr('checked',false);
                    $(this).attr('disabled','disabled');
                }else{
                    $(this).attr('disabled',false);
                }
            });
        }
        this.checkNums('checktype','gradelist');//判断年级是否全部选中
    },
    /**
     * 年级和试题类型的关系
     * @author demo
     * @date 2014年10月21日
     */
    checkType:function(){
        var self=this;
        self.checkNums('checktype','gradelist');
        //年级和试题类型关系
        var gradeID=self.getGradeList();
        if(gradeID==''){
            $('#checkall').attr('checked',false);
            $("input[name='doctype[]']").each(function(){
                $(this).attr('checked',false);
                $(this).attr('disabled','disabled');
            });
            return;
        }
        var gradeArr=gradeID.split(',');
        //判断选中的年级ID是否在试题类型的年纪ID里
        $("input[name='doctype[]']").each(function(){
            var j=0;
            for(var i in gradeArr){
                if((','+$(this).attr('thisid')+',').indexOf(','+gradeArr[i]+',')!=-1)
                    j++;
            }
            if(j>0){
                $(this).attr('disabled',false);
            }else{
                $(this).attr('checked',false);
                $(this).attr('disabled','disabled');
            }
        });
        self.checkNums('checkall','doctype');
    },
    //模板属性的点击事件
    tempAttrClick:function(){
        var self=this;
        //绑定考试类别
        $('.examtype').change(function(){
            var examtypeonlyid=$(this).attr('defaultstyle');
            var choosetypeid=$('input[name="choosetype"]:checked').val();
            if(choosetypeid=='2'){
                $('input[name="chooseattr"]').attr('disabled',false);
                $('input[name="chooseattr"]').each(function(){
                    if($(this).val()==examtypeonlyid){
                        $(this).attr('disabled',false);
                        $(this).attr('checked',true);
                    }
                })
                self.tempContent="";
            }else{
                $('input[name="chooseattr"]').attr('disabled',true);
                $('input[name="chooseattr"]').each(function(){
                    if($(this).val()==examtypeonlyid){
                        $(this).attr('disabled',false);
                        $(this).attr('checked',true);
                    }
                })
                var examtypeid=$(this).val();
                var choosetypeid=$('input[name="choosetype"]:checked').val();
                if(choosetypeid!='2'){
                    self.getTempArr(choosetypeid,examtypeid,'',0,1);
                }

            }
        });
        //绑定组卷方式
        $('.choosetype').change(function(){
            var examtypeid=$("input[name='examtype']:checked").val();
            var defaultstyle=$("input[name='examtype']:checked").attr('defaultstyle');
            var choosetypeid=$(this).val();
            if(choosetypeid=="2"){
                self.tempContent="";
                $('.mbsel').hide();
                $('input[name="chooseattr"]').attr('checked',false);
                $('input[name="chooseattr"]').attr('disabled',false);
                $('input[name="chooseattr"]').each(function(){
                    if($(this).val()==defaultstyle){
                        $(this).attr('disabled',false);
                        $(this).attr('checked',true);
                    }
                });
            }else{
                $('.mbsel').show();
                $('input[name="chooseattr"]').attr('disabled',true);
                $('input[name="chooseattr"]').each(function(){
                    if($(this).val()==defaultstyle){
                        $(this).attr('disabled',false);
                        $(this).attr('checked',true);
                    }
                });
                self.getTempArr(choosetypeid,examtypeid,'',0,1);
            }

        });
        //选择模板
        $('.templatelist').live('click',function(){
            var thisObjChoose=$(this).attr('haveChoose');
            if(thisObjChoose==1){
                return false;
            }
            $('.errormsg').html('');
            if($('.selmb').next().attr('class')!='mbloading'){
                $('.selmb').after('<span class="mbloading"><span class="boxloading">模板载入中请稍候...</span></span>');
            }
            $('.templatelist').css({'color':'#333','border-color':'#ccc'});
            $('.templatelist').each(function(){
                $(this).attr('haveChoose','0');
            })
            $(this).attr('haveChoose','1');
            $(this).css({'color':'#00a0e9','border-color':'#00a0e9'});
            var mbid=$(this).attr('mbid');
            var choosetypeid=$('input[name="choosetype"]:checked').val();
            $.post(U('Index/getTemplateByID'),{'mbid':mbid,'choosetypeid':choosetypeid,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                if(data['status']=='1'){
                    var doctypearr=data['data']['doctype'].split(',');
                    $("input[name='doctype[]']").attr('checked',false);
                    $("input[name='selectall']").attr('checked',false);
                    $('#checkall').parent().attr('style',''); //针对试题类型选择
                    $('input[name="doctype[]"]').parent().attr('style','');
                    for(var i=0;i<doctypearr.length;i++){
                        $("input[name='doctype[]']").each(function(){
                            if($(this).val()==doctypearr[i]){
                                $(this).parent().attr('style','color:#00a0e9'); //设置选中框颜色
                                $(this).attr('checked',true);
                            }
                        });
                    }
                    var checkednum=$("input[name='doctype[]']:checked").length;
                    var checknum=$("input[name='doctype[]']").length;
                    if(checkednum==checknum){
                        $("input[name='selectall']").attr('checked',true);
                    }
                    if(data['data']['gradelist']!=undefined){
                        var gradeList=data['data']['gradelist'];
                        self.tempContent=data['data'];
                        $('.checktype').each(function(){
                            if(gradeList.indexOf($(this).val())!=-1){
                                $(this).attr('checked',true);
                                $(this).parent().attr('style','color:#00a0e9'); //设置选中框颜色
                            }else{
                                $(this).attr('checked',false);
                                $(this).parent().attr('style',''); //设置选中框颜色
                            }
                        });
                        var checkLen=$('input[name="gradelist[]"]').length;
                        var len=0;
                        $('.checktype').each(function(){
                            if($(this).attr('checked')=='checked'){
                                len+=1;
                            }
                        });
                        if(checkLen==len){
                            $('#checktype').attr('checked',true);
                            $('#checktype').parent().attr('style','color:#00a0e9'); //设置选中框颜色
                        }else{
                            $('#checktype').attr('checked',false);
                            $('#checktype').parent().attr('style',''); //设置选中框颜色
                        }
                    }
                    //年级
                    if(data['data']['arealist']!=undefined){
                        if(data['data']['arealist']==0){
                            $('input[name="areaall"]').attr('checked',true);
                            $('input[name="areaall"]').parent().attr('style','color:#00a0e9'); //设置选中框颜色
                            $('input[name="area[]"]').each(function(){
                                $(this).attr('checked',true);
                                $(this).parent().attr('style','color:#00a0e9'); //设置选中框颜色
                            });
                        }else{
                            $('input[name="areaall"]').attr('checked',false);
                            $('input[name="areaall"]').parent().attr('style',''); //设置选中框颜色
                            $('input[name="area[]"]').each(function(){
                                if((','+data['data']['arealist']+',').indexOf(','+$(this).val()+',')!=-1){
                                    if($(this).attr('checked')!=true){
                                        $(this).attr('checked',true);
                                        $(this).parent().attr('style','color:#00a0e9'); //设置选中框颜色
                                    }
                                }else{
                                    $(this).attr('checked',false);
                                    $(this).parent().attr('style','');
                                }
                            })
                        }
                    }

                    $('input[name="chooseattr"]').attr('disabled',true);
                    $('input[name="chooseattr"]').each(function(){
                        if($(this).val()==data['data']['chooseattr']){
                            $(this).attr('disabled',false);
                            $(this).attr('checked',true);
                        }
                    })
                    $('.selmb').next().remove();
                }
            });
        });
        /**
         * 试题类型全选按钮点击事件
         * @author demo
         * @date 2014年10月21
         */
        $('#checkall').live('click',function(){
            self.checkAll('checkall','doctype');
            self.checkGrade();
        });
        /**
         * 年级全选按钮点击事件
         * @author demo
         * @date 2014年10月21
         */
        $('#checktype').live('click',function(){
            self.checkAll('checktype','gradelist');
            self.checkType();
        });
        /**
         * 试题类型的点击事件
         * @author demo
         * @date 2014年10月21
         */
        $(".checkall").live('click',function(){
            self.checkGrade();
        });
        /**
         * 年级点击事件
         * @author demo
         * @date 2014年10月21
         */
        $(".checktype").live('click',function(){
            self.checkType();
        });
        //地区点击事件
        $("#areaall").live('click',function(){
            self.checkAll('areaall','area');
        });
        //地区点击事件
        $(".areaall").live('click',function(){
            self.checkNums('areaall','area');//判断试题题型是否全部选中
        });
    },
    //设置选中项，颜色改变
    checkedChangeColor:function(){
        $('input').live('click',function(){
            $('input').parent().attr('style',''); //去除所有input框父级颜色
            $('input:checked').parent().attr('style','color:#00a0e9'); //设置选中框颜色
        });
    },
    //第一步错误信息数据
    checkStep1Error:function(attr,spanname,msg){
        if(typeof(attr)=='undefined' || !attr){
            if($(spanname).children('span').eq(0).attr('class')  !='errormsg'){
                $(spanname).append("<span class='errormsg'>"+msg+"</span>");
            }
            return false;
        }else{
            $(spanname).children('span').eq(0).remove();
        }
    },
    //跳转第二步
    skipTwo:function(){
        var self=this;
        $('.tostep2').live('click',function(){
            if($('.selmb').next().attr('class')=="mbloading" && $('input[name="choosetype"]:checked').val()!='2'){
                $.myDialog.showMsg('模板正在载入请稍候...',1);
                return false;
            }

            var error=0; //错误记录
            var tmp=0; //临时数据记录

            //数据验证 2014年8月30日添加/
            var templatelist='';
            var spanname='';
            var doctype='';
            var msg='';
            var examtype=$("input[name='examtype']:checked").val();//考试类别验证
            if(examtype==''){
                error++;
                msg='考试类别没有选择！';
                spanname='.exametypebehind';
                if($(spanname).next().attr('class') !='errormsg'){
                    $(spanname).append("<span class='errormsg'>"+msg+"</span>");
                }
            }

            //验证试题类型
            $("input[name='doctype[]']").each(function(){
                if($(this).attr("checked")=='checked'){
                    doctype+=$(this).val()+",";
                }
            });
            tmp=self.checkStep1Error(doctype,'.doctypebehind','试题类型没有选择！');
            if(tmp==false) error++;

            //验证试题属性
            var chooseattr=$("input[name='chooseattr']:checked").val();
            tmp=self.checkStep1Error(chooseattr,'.chooseattrbehind','试题属性没有选择！');
            if(tmp==false) error++;

            //验证年级
            var gradelist=self.getGradeList();
            tmp=self.checkStep1Error(gradelist,'.grademsg','所需年级没有选择！');
            if(tmp==false) error++;

            //组卷方式验证(根据组卷方式，点击下一步，显示页面不同)
            var choosetype=$("input[name='choosetype']:checked").val();
            if(choosetype!='0' && choosetype!='1'){
                //根据组卷方式，显示模板框，验证模板是否选择
                $('.templatelist').each(function(){
                    if($(this).css('color')=='rgb(0, 160, 233)' || $(this).css('color')=='#00a0e9'){
                        templatelist=$(this).attr('mbid');
                    }
                });
                if(choosetype!='2' && templatelist == ''){
                    error++;
                    msg='模板没有选择！';
                    spanname='.tempbehind';
                    if($(spanname).children('div').next().attr('class') !='errormsg'){
                        $(spanname).append("<span class='errormsg'>"+msg+"</span>");

                    }
                    // 提示效果
                    $(spanname).find(".errormsg").animate({"opacity":.2},20,function(){
                        $(spanname).find(".errormsg").animate({"opacity":1},20)
                    })
                }else{
                    spanname='.tempbehind';
                    $(spanname).children('div').next().remove();
                }
                if(error>0){
                    $.myDialog.showMsg('请完善信息!',1);
                    return false;
                }
                //验证通过跳转
                self.showLayoutTwo(self.tempContent);
                $('#dir3').children('div').eq(2).children().children().children().children('td').eq(0).children('div').removeClass('tostep1').addClass('tostep2');
                $('.step').css('display','none');
                $('#dir2').css('display','block');
                $('.errormsg').remove();
                self.initDivXdBox();
            }else{
                if(error>0){
                    $.myDialog.showMsg('请完善信息!',1);
                    return false;
                }
                $('#dir3').children('div').eq(2).children().children().children().children('td').eq(0).children('div').removeClass('tostep2').addClass('tostep1');
                $('.step').css('display','none');
                $('#dir3').css('display','block');
                self.initDivXdBox();
            }
        });
    },
    /**
     * 第二个页面
     */
    //第二页面
    showLayoutTwo:function(tempcontent){
        if(!tempcontent){
            this.initDataTwo();
        }else{
            this.loadTemplate(tempcontent);
        }
    },
    //初始化数据
    initDataTwo:function(){
        var self=this;
        $('.addtpl').css('display','block');
        $('#dir2 .dir').html(self.initCustom());
        var tempInfo = new Array();
        var examtype = $("input[name='examtype']:checked").parent().text().replace(/[ \s]/g,'');
        var subjectName = Subject[self.subjectID]['SubjectName'];
        var docStr = '';
        var typeStr = '';
        var attr = '';
        //获取年份和月份
        var d=new Date();
        var y=d.getFullYear();
        var m=d.getMonth()+1;
        var examtypeName=$("input[name='examtype']:checked").attr('showName');
        var maintitle=y+"-"+(y+1)+"学年度"+schoolName+m+"月"+examtypeName+"卷";
        tempInfo['tempname'] = subjectName+examtype+'模板';
        tempInfo['maintitle'] = maintitle;
        tempInfo['subtitle'] = '副标题';
        tempInfo['totalscore'] = '0';
        tempInfo['notice'] = '(做题要求及分值解释)';
        var paper1 = '';
        var paper2 = '';
        var h = 0;
        this.setTitleMsg(tempInfo['tempname'],tempInfo['maintitle'],tempInfo['subtitle'],tempInfo['notice'],tempInfo['totalscore'],tempInfo['partname1'],tempInfo['partname2'],
            tempInfo['partheaddes1'],tempInfo['partheaddes2']);
        $.each(Types[self.subjectID],function(h,paper){
            var order='';
            var i=1;
            var j=1;
            if(paper["Volume"] ==1 ){
                order='0_'+i+'_'+h;
                paper1 += self.initDefType(0,h,order,paper.TypesID,paper.TypesName,0,0);
                parseInt(i)+1;
            }else{
                order='1_'+j+'_'+h;
                paper2 += self.initDefType(1,h,order,paper.TypesID,paper.TypesName,0,0);
                j++;
            }
        });
        $('.partbody').eq(0).html(paper1);
        $('.partbody').eq(1).html(paper2);
        attr = $('input[name="chooseattr"]:checked').val();
        $('input[name="doctype[]"]:checked').each(function(){
            docStr += $(this).val()+',';
        })
        var index = docStr.lastIndexOf(',');
        docStr = docStr.substring(0,index);
        typeStr = '<input type="hidden" class="chooseattr" value="'+attr+'">' +
        '<input type="hidden" class="doctype" value="'+docStr+'">';
        $('.totalscore').after(typeStr);
        $('#questypehead0_0').find('.uptypes').css({opacity:'0.5'});
        $('.questypehead').last().find('.downtypes').css({opacity:'0.5'});
    },
    //通过知识点id获取知识点内容
    roundsGet:function(contentArr,type){
        var self=this;
        var contentStr=contentArr.join(',');
        $.post(U("Index/getData"),{"style":type,"ID":contentStr,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            }
            var knowledgeArr = new Array();
            if(type == 'knowledgeList'){
                $.each(data['data'],function(i){
                    knowledgeArr[data['data'][i]['KlID']] = data['data'][i]['KlName'];
                })
            }else{
                $.each(data['data'],function(i){
                    knowledgeArr[data['data'][i]['ChapterID']] = data['data'][i]['ChapterName'];
                })
            }
            self.roundsOutput(knowledgeArr,contentArr);
        })
    },
    //将获取到的知识点内容加入页面
    roundsOutput:function(roundContent,contentArr){
        var roundStr = '';
        var partArr = new Array();
        $.each(contentArr,function(h,part){
            partArr = part.split(',');
            roundStr = '';
            $.each(partArr,function(f,con){
                roundStr += roundContent[con]+'<br>';
            })
            var index = roundStr.lastIndexOf('<br>');
            roundStr = roundStr.substring(0,index);
            if(partArr[0]=='all'){
                roundStr='全部';
            }
            $('.rounds:not(th)').eq(h).html(roundStr);
        })
    },
    //载入模板
    loadTemplate:function(tempcontent){
        var self=this;
        var num=0;          //题型序号
        var testOrder = 0;  //上一题型最后试题的序号
        var m = 1;          //选做题对比序号
        var order = '';     //试题序号
        var rounds = 0;     //知识点数组键值
        var initTestStr = '';//初始化试题字符串
        var prev = '';//上次的题号
        var roundstype = '';//章节或者知识点字符串
        var firstTest = 0;  //选做题序号
        var roundArr = new Array(); //知识点，章节数组
        //初始化框架
        var initstr = self.initCustom();
        $('#dir2 .dir').html(initstr);
        //设置标题信息
        if(tempcontent['maintitle'].indexOf('xxx学校')!=-1){
            tempcontent['maintitle']=tempcontent['maintitle'].replace('xxx学校',schoolName);
        }
        this.setTitleMsg(tempcontent['tempname'],tempcontent['maintitle'],tempcontent['subtitle'],tempcontent['notice'],0,
            tempcontent[0]['parthead'],tempcontent[1]['parthead'],tempcontent[0]['partheaddes'],tempcontent[1]['partheaddes']);
        var templateStr = '';
        $('.addtpl').css('display','block');
        $('.partbody').eq(0).html('');
        $('.partbody').eq(1).html('');
        //循环分卷
        $.each(tempcontent,function(i,part){
            if(!isNaN(i)){
                //循环题型
                $.each(part,function(j,type){
                    if(!isNaN(j)){
                        templateStr = '';       //题型字符串
                        //构造题型字符串
                        if(self.testTypeMsg(type.typeid)){
                            var orderNum=parseInt(j)+1;
                            if(i == 0){
                                templateStr += self.initDefType(i,j,orderNum,type.typeid,type.questypename,'',type.ifHidden);
                                num = j;
                            }else{
                                num = $('.paperpart').eq(0).find('.questypehead').length*1+j*1;
                                templateStr += self.initDefType(i,num,orderNum,type.typeid,type.questypename,'',type.ifHidden);
                            }
                        }
                        $('.partbody').eq(i).append(templateStr);
                        var count = 0;   //此选做题的总题数（几选几的第一个'几'）
                        //循环试题
                        $.each(type,function(n,test){
                            if(!isNaN(n)){
                                //记录知识点数据
                                roundArr[rounds] = test.rounds;
                                rounds++;
                                if(n!=0){
                                    var tmpStr = '';    //存储试题序号内容
                                    tmpCount=new Array();   //序号的内容数组
                                    tmpStr = $('.order:not(th)').last().html();
                                    tmpCount=tmpStr.split('~');
                                    if(tmpCount[1]){            //带小题
                                        prev = tmpCount[1];
                                    }else{                      //不带小题
                                        prev = tmpCount[0];
                                    }
                                }
                                order = self.getOrder(n,testOrder,test.nums,prev);      //获取试题序号
                                //获取选做题序号
                                if(test.choosenum!=0){
                                    if(test.choosenum != m ){
                                        m += 1;
                                        $("li[choosenum = '"+(test.choosenum-1)+"']").each(function(q){
                                            choosenumArr = $("li[choosenum = '"+(test.choosenum-1)+"']").eq(q).find('.ifchoose1').html(count)
                                        })
                                        count = 1;
                                    }else if((parseInt(n)+1) == self.getArrLength(type)){
                                        count++;
                                        $("li[choosenum = '"+test.choosenum+"']").each(function(q){
                                            choosenumArr = $("li[choosenum = '"+test.choosenum+"']").eq(q).find('.ifchoose1').html(count);
                                        })
                                        m += 1;
                                    }else{
                                        count++;
                                    }
                                }
                                //构造并载入试题
                                initTestStr = self.loadTest(type.typeid,test.ifchoose,count,order,test.nums,'knowledge',test.rounds,test.scores,test.diff,test.testchoose,test.choosenum,i,num,n,'');
                                $('#questypehead'+i+'_'+num).find('table.testset tr:last').after(initTestStr);
                                if((parseInt(n)+1) == self.getArrLength(type)){
                                    testOrder = self.getTestOrder(n,initTestStr);
                                }
                            }
                        })
                    }
                })
            }
        });
        //传递考试类别，考试类型数据
        var docStr = '';
        docStr += '<input type="hidden" class="chooseattr" value="'+tempcontent['chooseattr']+'">' +
        '<input type="hidden" class="doctype" value="'+tempcontent['doctype']+'">';
        $('.totalscore').after(docStr);
        //加载知识点，章节
        if($('input[name="chooseattr"]:checked').val()==1){
            roundstype = 'knowledgeList';
        }else{
            roundstype = 'chapterList';
        }
        if(roundArr.length>0) self.roundsGet(roundArr,roundstype);
        //重置题型试题上下移按钮
        self.initTypeMove();
        $('.partbody').each(function(){
            $(this).find('.questypehead').each(function(){
                self.initTestMove($(this).attr('id'));
            })
        })
        //重置分数
        self.initScores();
    },

    //设置标题
    setTitleMsg:function(tempname,maintitle,subtitle,notice,score,partname1,partname2,partheaddes1,partheaddes2){
        $('.tempName').text(tempname);
        $('.maintitle').text(maintitle);
        $('.subtitle').text(subtitle);
        $('.notice').html(notice);
        if(score!=-1) $('.totalscore').text('满分'+score+'分');
        $('#partname1').text(partname1);
        $('#partname2').text(partname2);
        $('#partnote1').html(partheaddes1);
        $('#partnote2').html(partheaddes2);
    },
    //题型初始化
    initDefType:function(paperNum,typeNum,orderNum,typesID,typesName,nums,ifHidden){
        var tmpname    = '章节';
        var chooseAttr = this.getChooseAttr();
        var order      = paperNum+'_'+orderNum+'_'+typeNum;
        if(orderNum.length>2 && orderNum.indexOf('_')!=-1){
            order=orderNum;
        }
        if(chooseAttr==1) tmpname='考点';
        var typeStr = '';
        var addScores=0;
        if(nums!=0){
            addScores = $.myTest.getTypes(Types[this.subjectID],typesID,'DScore');
        }
        if(paperNum == ''){
            paperNum = 0;
        }
        if(typeNum == ''){
            typeNum = 0;
        }
        typeStr += '<div id="questypehead'+paperNum+'_'+typeNum+'" class="questypehead">'+
        '<div class="typepart">';
        if(ifHidden==undefined || ifHidden=='0'){
            typeStr += '<div class="testType" id="testtype'+typesID+'">';
        }else{
            typeStr += '<div class="testType" id="testtype'+typesID+'" style="color:#b2b2b2">';
        }
        typeStr += '<div class="tit"><span class="tips">'+shuzi[typeNum]+'、'+'</span>'+
        '<span class="questypename">'+typesName+'</span>'+
        '<span class="questypescore">(满分'+0+'分)</span></div>';
        if(ifHidden==undefined || ifHidden=='0'){
            typeStr += '<div class="typeset_left"><a class="ifHidden" ifhidden="0" href="#">' +
            '<span class="icoimg hide"></span><span class="showtext">隐藏</span></a></div>';
        }else{
            typeStr += '<div class="typeset_left"><a class="ifHidden" ifhidden="1" href="#">' +
            '<span class="icoimg"></span><span class="showtext">显示</span></a></div>';
        }
        typeStr += '</div>'+
        '<div class = "typeset" order="'+order+'">'+
        '<a class = "addtest">+ 添加试题</a>'+
        '<a class = "uptypes">上移</a>'+
        '<a class = "downtypes">下移</a>'+
        '<a class = "edittypes">编辑</a>'+
        '<a class = "setallscores">批量设分</a>'+
        '<a class="deltypes">删除</a>'+
        '</div>'+
        '</div>'+
        '<table class="testset">'+
        '<tr>'+
        '<th class="order">题序</th><th class="nums">题量</th><th class="rounds">'+tmpname+'</th><th class="scores">分值</th><th class="diff">难度</th><th class="testchoose">类型</th><th class="set">操作</th>'+
        '</tr>';
        for(p=0;p<nums;p++){
            typeStr += '<tr id="questest0_0_0" class="questest"><td>第<span class="order">1</span>题</td>' +
            '<td class="nums">1</td>' +
            '<td><span class="rounds" style="display:block;text-align:left">全部</span><input type="hidden" class="roundsid" value="all"></td>' +
            '<td class="scores" scores="'+addScores+'">'+addScores+'分</td>' +
            '<td class="diff" diff="'+3+'">一般</td>' +
            '<td><span  class="testchoose">'+this.getTestChoose($.myTest.getTypes(Types[this.subjectID],typesID,"TypesStyle"))+'</span><input type="hidden" class="testchoosenum" value="0"></td>' +
            '<td class=choosenum'+0+'><a class="edittest" isedit="1">修改</a><a class="deltest">删除</a><a class="upmove">上移</a><a class="downmove">下移</a></td>' +
            '</tr>';
        }

        typeStr += '</table></div>';
        return typeStr;
    },
    //初始化框架
    initCustom:function(){
        return '<div class="titleset">'+
            '<div id="tempName" class="tempName settestmsg maininfo" style="border:none; margin-bottom:20px;">数据载入中</div>'+
            '<div id="maintitle" class="maintitle settestmsg maininfo">数据载入中请稍候...</div>'+
            '<div id="subtitle" class="subtitle settestmsg maininfo">数据载入中请稍候...</div>'+
            '<div class="totalscore">数据载入中请稍候...</div>'+
            '<div id="notice" class="settestmsg tleft maininfo zysx"><div class="tleft">注意事项：</div><div class="notice">数据载入中请稍候...</div></div>'+
            '</div>'+
            '<div class="paperpart">'+
            '<div class="parthead" id="parthead1">'+
            '<div class="partmenu">'+
            '<a class="addquestype">添加新题型</a>'+
            '<a class="editpart settestmsg maininfo">设置</a>'+
            '</div>'+
            '<div class="partheadbox" id="partheadbox1">'+
            '<div class="partname settestmsg maininfo" id="partname1">第I卷（选择题）</div>'+
            '<div class="partheaddes settestmsg maininfo" id="partnote1">请点击修改第I卷的文字说明</div>'+
            '</div>'+
            '</div>'+
            '<div class="partbody">数据载入中请稍候...</div>'+
            '</div>'+
            '<div class="paperpart">'+
            '<div class="parthead" id="parthead2">'+
            '<div class="partmenu">'+
            '<a class="addquestype">添加新题型</a>'+
            '<a class="editpart settestmsg maininfo">设置</a>'+
            '</div>'+
            '<div class="partheadbox" id="partheadbox2">'+
            '<div class="partname settestmsg maininfo" id="partname2">第II卷（非选择题）</div>'+
            '<div class="partheaddes settestmsg maininfo" id="partnote2">请点击修改第II卷的文字说明</div>'+
            '</div>'+
            '</div>'+
            '<div class="partbody">数据载入中请稍候...</div>'+
            '</div>';
    },
    /**
     * 题型上移
     */
    upMove:function(){
        var self=this;
        $('.uptypes').live('click',function(){
            var thisOrder=$(this).parent().attr('order');//位置标识
            var orderNum=thisOrder.split('_');//将位置标识拆分成数组
            var testObject='#questypehead'+orderNum[0]+'_'+orderNum[2];//移动对象
            //如果是最顶端的就不能上移
            if(orderNum[2]=='0'){
                return false;
            }else if(orderNum[1]=='1' && orderNum[2]!=0){//外部移动
                var nextQuestype=parseInt(orderNum[0])-1;
                var nextTestNum=parseInt(orderNum[2])-1;
                var nextObject=$('#questypehead'+nextQuestype+'_'+nextTestNum);
                if(nextObject){
                    nextObject.after($(testObject));
                }else{
                    return false;
                }
            }else{//内部移动
                var nextTestNum=parseInt(orderNum[2])-1;
                var nextObject=$('#questypehead'+orderNum[0]+'_'+nextTestNum);
                if($(this).next().css('opacity')=='0.5'){//下移按钮样式变化
                    $(this).next().css({opacity:'1'});
                    nextObject.find('.downtypes').css({opacity:'0.5'});
                }
                if(nextObject.find('.uptypes').css('opacity')=='0.5'){//上移按钮样式变化
                    $(this).css({opacity:'0.5'});
                    nextObject.find('.uptypes').css({opacity:'1'});
                }
                $(testObject).after(nextObject);//移动
            }
            //重置题型编号
            self.resetTypes();
            //重置试题编号
            self.resetTests();
            //重置位置编号
            self.resetOrder();
        })
    },
    //题型下移
    downMove:function(){
        var self=this;
        $('.downtypes').live('click',function(){
            var testNum=self.getTestNum();//获取各部分试题数量
            var thisOrder=$(this).parent().attr('order');//位置标识
            var orderNum=thisOrder.split('_');//将位置标识拆分成数组
            var testObject='#questypehead'+orderNum[0]+'_'+orderNum[2];//移动对象
            if(orderNum[1]<testNum[orderNum[0]]){//内部移动
                var nextTestNum=parseInt(orderNum[2])+1;
                var nextObject=$('#questypehead'+orderNum[0]+'_'+nextTestNum);
                if($(this).prev().css('opacity')=='0.5'){//上移按钮样式变化
                    $(this).prev().css({opacity:'1'});
                    nextObject.find('.uptypes').css({opacity:'0.5'});
                }
                if(nextObject.find('.downtypes').css('opacity')=='0.5'){//下移按钮样式变化
                    $(this).css({opacity:'0.5'});
                    nextObject.find('.downtypes').css({opacity:'1'});
                }
                $(testObject).before(nextObject);//移动
            }else if(orderNum[1]==testNum[orderNum[0]]){//外部移动
                var nextQuestype=parseInt(orderNum[0])+1;
                var nextTestNum=parseInt(orderNum[2])+1;
                var nextObject=$('#questypehead'+nextQuestype+'_'+nextTestNum);
                if(nextObject){
                    nextObject.before($(testObject));
                }else{
                    return false;
                }

            }else{
                return false;
            }
            //重置题型编号
            self.resetTypes();
            //重置试题编号
            self.resetTests();
            //重置位置编号
            self.resetOrder();
        })
    },
    //获取试题数量
    getTestNum:function(){
        var num=new Array();
        $('.paperpart').each(function(i){
            var n=0;
            $('.paperpart').eq(i).find('.questypehead').each(function(j){
                n++;
            });
            num[i]=n;
        });
        return num;
    },
    //模板点击事件
    tempClick:function(){
        var self=this;
        //标题变色
        $('.subtitle,.maintitle,.zysx').live('mouseenter',function(){
            $(this).css({'color':'#d17151','border-color':'#f6c863'});
        });
        $('.subtitle,.maintitle,.zysx').live('mouseleave',function(){
            $(this).css({'color':'#000','border-color':'#ccc'});
        });
        //修改标题弹出框
        $('.settestmsg').live('click',function(){
            //获取页面试卷信息
            var tempname=$('.tempName').text();
            var maintitle=$('.maintitle').text();
            var subtitle=$('.subtitle').text();
            var partname1=$('#partname1').text();
            var partname2=$('#partname2').text();
            var notice=$('.notice').html().replace(/<br>/g,"\n");
            var resettestmsg1=$('.partheaddes').eq(0).html().replace(/<br>/g,"\n");
            var resettestmsg2=$('.partheaddes').eq(1).html().replace(/<br>/g,"\n");
            var title='编辑试卷信息';
            var idname='settestmsg';
            var tmp_str='';
            tmp_str+='      <div class="addtypes_box bdrC" id="setmain"><div class="h450">';
            tmp_str+='        <div class="test_boxlist" id="s_tempName">';
            tmp_str+='            <span class="addtypes_box_name">模板名称:</span>';
            tmp_str+='            <span class="boxlist_sel">';
            tmp_str+='                <input class="editPaperIpt" type="text" name="tempname" value="'+tempname+'" size="42">';
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist" id="s_maintitle">';
            tmp_str+='            <span class="addtypes_box_name">主标题:</span>';
            tmp_str+='            <span class="boxlist_sel">';
            tmp_str+='                <input class="editPaperIpt" type="text" name="maintitle" value="'+maintitle+'" size="42">';
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist" id="s_subtitle">';
            tmp_str+='            <span class="addtypes_box_name">副标题:</span>';
            tmp_str+='            <span class="boxlist_sel">';
            tmp_str+='                <input class="editPaperIpt" type="text" name="subtitle" value="'+subtitle+'" size="42">';
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist" id="s_notice">';
            tmp_str+='            <div class="addtypes_box_name" style="display:inline-block;float:left;">注意事项:</div>';
            tmp_str+='            <div class="boxlist_sel" style="display:inline-block;">';
            tmp_str+='                <textarea class="editPaperText" name="notice" id="noticemsg" rows="3" cols="40" style="resize: none;">'+notice+'</textarea>';
            tmp_str+='            </div>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist" id="s_partname1">';
            tmp_str+='            <span class="addtypes_box_name">Ⅰ卷名称:</span>';
            tmp_str+='            <span class="boxlist_sel">';
            tmp_str+='                <input class="editPaperIpt" type="text" name="partname1" value="'+partname1+'" size="42">';
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist" id="s_partnote1">';
            tmp_str+='            <div class="addtypes_box_name" style="display:inline-block;float:left;">Ⅰ卷描述:</div>';
            tmp_str+='            <div class="boxlist_sel" style="display:inline-block;">';
            tmp_str+='                <textarea class="editPaperText" name="partheaddes1" id="partheaddes1" rows="3" cols="40" style="resize: none;">'+resettestmsg1+'</textarea>';
            tmp_str+='            </div>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist" id="s_partname2">';
            tmp_str+='            <span class="addtypes_box_name">Ⅱ卷名称:</span>';
            tmp_str+='            <span class="boxlist_sel">';
            tmp_str+='                <input class="editPaperIpt" type="text" name="partname2" value="'+partname2+'" size="42">';
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist" id="s_partnote2">';
            tmp_str+='            <div class="addtypes_box_name" style="display:inline-block;float:left;">Ⅱ卷描述:</div>';
            tmp_str+='            <div class="boxlist_sel" style="display:inline-block;">';
            tmp_str+='                <textarea class="editPaperText" name="partheaddes2" id="partheaddes2" rows="3" cols="40" style="resize: none;">'+resettestmsg2+'</textarea>';
            tmp_str+='            </div>';
            tmp_str+='        </div>';
            tmp_str+='    </div></div>';
            $.myDialog.normalMsgBox(idname,title,550,tmp_str,3);

            var tmpid=$('#s_'+$(this).attr('id'));
            if(tmpid.length>0){
                tmpid.css({'background-color':'#dff1fa'});
                tmpid.find('.addtypes_box_name').css({'color':'#00a0e9'});
                $('#setmain .h450').scrollTop(tmpid.offset().top-$('#setmain .h450').offset().top);
            }
        });
        //修改标题确定
        $("#settestmsg .normal_yes").live('click',function(){
            if($('input[name="tempname"]').val()==""){
                $.myDialog.showMsg('大标题不能为空！',1);
                return false;
            }
            if($('input[name="tempname"]').val().length>60){
                $.myDialog.showMsg('大标题输入太长,限60字符！',1);
                return false;
            }
            if($('input[name="maintitle"]').val()==""){
                $.myDialog.showMsg('主标题不能为空！',1);
                return false;
            }
            if($('input[name="maintitle"]').val().length>200){
                $.myDialog.showMsg('主标题输入太长,限200字符！',1);
                return false;
            }
            if($('input[name="subtitle"]').val()==""){
                $.myDialog.showMsg('副标题不能为空！',1);
                return false;
            }
            if($('input[name="subtitle"]').val().length>200){
                $.myDialog.showMsg('副标题输入太长,限200字符！',1);
                return false;
            }
            if($('#noticemsg').val()==""){
                $.myDialog.showMsg('注意事项不能为空！',1);
                return false;
            }
            var  noticemsg=$('#noticemsg').val().replace(/\n/g,"<br/>");
            if($('input[name="partname1"]').val()==""){
                $.myDialog.showMsg('Ⅰ卷名称不能为空！',1);
                return false;
            }
            if($('input[name="partname1"]').val().length>200){
                $.myDialog.showMsg('Ⅰ卷名称输入太长,限200字符！',1);
                return false;
            }
            var partheaddes1 = $('#partheaddes1').val().replace(/\n/g,"<br/>");
            if($('#partheaddes1').val()==""){
                $.myDialog.showMsg('Ⅰ卷描述不能为空！',1);
                return false;
            }
            if($('input[name="partname2"]').val()==""){
                $.myDialog.showMsg('Ⅱ卷名称不能为空！',1);
                return false;
            }
            if($('input[name="partname2"]').val().length>200){
                $.myDialog.showMsg('Ⅱ卷名称输入太长,限200字符！',1);
                return false;
            }
            var partheaddes2 = $('#partheaddes2').val().replace(/\n/g,"<br/>");
            if($('#partheaddes2').val()==""){
                $.myDialog.showMsg('Ⅱ卷描述不能为空！',1);
                return false;
            }
            self.setTitleMsg($('input[name="tempname"]').val(),$('input[name="maintitle"]').val(),$('input[name="subtitle"]').val(), $.myCommon.dangerHTML(noticemsg),'-1',$('input[name="partname1"]').val(),$('input[name="partname2"]').val(),$.myCommon.dangerHTML(partheaddes1),$.myCommon.dangerHTML(partheaddes2));
            $('#settestmsg .tcClose').click();
        });
        //保存模板
        $('.addtpl').live('click',function(){
            var typeId = $('input[name=examtype]:checked').val();
            var tempname = $('.tempName').html();
            var choosetype=$("input[name='choosetype']:checked").val();//我的模板
            var examtypeid=$("input[name='examtype']:checked").val();
            $.myDialog.normalMsgBox('tplloading','数据加载中请稍候...',450,'<span class="boxloading">模板加载中请稍候...</span>',4);
            $.post(U('Index/getTemplateList'),{'SubjectID':Cookie.Get("SubjectId"),'examtypeid':examtypeid,'choosetypeid':choosetype,'check':1,'times':Math.random()},function(data){
                $('#tplloading .tcClose').click();
                if($.myCommon.backLogin(data)==false){
                    return false;
                }

                var templatelistid='';
                var adddiv='';
                if(data.data=='1'){
                    adddiv='<label><input type="radio" name="creatorup" value="2" class="creatorup" functionName="saveSysTemplateList">对系统模板替换</label>';
                }
                if(choosetype=='4'){
                    var otherchecked='checked';
                    style='style="display:none;"'
                }else if(choosetype=='2'){
                    choosetype='4';
                    var otherchecked="checked";
                    style='style="display:none;"'
                }else{
                    var otherchecked="checked";
                    style='style="display:none;"'
                }
                $('.templatelist').each(function(){
                    if($(this).css('color')=='rgb(0, 160, 233)' || $(this).css('color')=='#00a0e9'){
                        templatelistid=$(this).attr('mbid');
                    }
                });
                var idname="addtplmsg";
                var title="保存模板";
                var tmp_str='<div>'+
                    '<p>模板名称：<span class="boxlist_sel"><input type="text" name="tempname" value="'+tempname+'" class="tempname" size="43"></span></p>'+
                    '</div>'+
                    '<div class="templist">'+
                    '<p>模板存储：<label><input type="radio" name="creatorup" class="creatorup" value="1" '+ otherchecked+'>新模板 </label><label><input type="radio" name="creatorup" value="0" class="creatorup">对已有模板替换 </label>'+adddiv+
                    '</p>'+
                    '</div>'+
                    '<div '+style+' class="mytpl">'+
                    '<label class="typename" >我的模板：</label>'+
                    '<div class="showtypeval">'+
                    '<div class="selmb1">'+

                    '</div>'+
                    '</div>'+
                    '</div>'+
                    '<input type="hidden" name="havetypeid" value="'+typeId+'">';
                $.myDialog.normalMsgBox(idname,title,500,tmp_str,3);
            });
        });
        $('#addtplmsg .normal_yes').live('click',function(){
            var tempname=$.trim($('input[name="tempname"]').val());
            var havetypeid=$('input[name="havetypeid"]').val();
            var ifnew=$('input[name="creatorup"]:checked').val();
            $('.tempName').html(tempname);
            var functionName='saveTemplate';
            var templatelistid;
            if($('.questest').length<1){
                $.myDialog.showMsg('模板数据不能为空!',1);
                return false;
            }
            if(tempname==""){
                $.myDialog.showMsg('模板名称不能为空!',1);
                return false;
            }
            if(tempname.length>200){
                $.myDialog.showMsg('模板名称过长,限60字符!',1);
                return false;
            }
            if(ifnew=='0'){
                $('.templatelist1').each(function(){
                    if($(this).css('color')=='rgb(255, 255, 255)' || $(this).css('color')=='#fff'){
                        templatelistid=$(this).attr('mbid');
                    }
                });
                if(typeof(templatelistid)=="undefined"){
                    $.myDialog.showMsg('您还没有选中要替换的模板！',1);
                    return false;
                }

            }
            if(ifnew=='1'){
                templatelistid='';
            }
            if(ifnew=='2'){
                $('.templatelist1').each(function(){
                    if($(this).css('color')=='rgb(255, 255, 255)' || $(this).css('color')=='#fff'){
                        templatelistid=$(this).attr('mbid');
                    }
                });
                if(typeof(templatelistid)=="undefined"){
                    $.myDialog.showMsg('您还没有选中要替换的模板！',1);
                    return false;
                }
                functionName=$('input[name="creatorup"]:checked').attr('functionName');
            }
            var Content=self.getDir2Data();
            if(!Content) return;
            $.post(U("Index/"+functionName),{'typeId':havetypeid,'subjectId':self.subjectID,'content':Content,'templateListId':templatelistid,'ifsystem':ifnew,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                $('#addtplmsg .tcClose').click();
                $.myDialog.showMsg(data['data']);
            })
        });
        $('.creatorup').live('change',function(){
            var i=0;
            var templist='';
            var templatelistid='';
            if($(this).val()=='0'){
                var examtypeid=$("input[name='examtype']:checked").val();
                var choosetype='4';
                $('.templatelist').each(function(){
                    if($(this).css('color')=='rgb(0, 160, 233)'|| $(this).css('color')=='#00a0e9'){
                        var templatelistid=$(this).attr('mbid');
                    }
                });
                self.getTempArr(choosetype,examtypeid,templatelistid,1,0);
                $('.mytpl').show();
                $.myDialog.tcDivPosition('addtplmsg');//层位置
            }else if($(this).val()=='2'){
                $('.mytpl').show();
                $('.selmb1').html('<span class="boxloading">模板加载中请稍候...</span>');
                $.myDialog.tcDivPosition('addtplmsg');//层位置
                var examtypeid=$("input[name='examtype']:checked").val();
                $.post(U("Index/getTemplateList"),{'SubjectID':self.subjectID,'examtypeid':examtypeid,'choosetypeid':'3','times':Math.random()},function(data){
                    if($.myCommon.backLogin(data)==false){
                        $('.selmb1').html('<div style="color:#F53131;padding:5px;">'+data['data']+'</div>');
                        $.myDialog.tcDivPosition('addtplmsg');//层位置
                        return false;
                    }
                    for(i in data['data'][0]['content']){
                        templist+='<div class="templatelist1 elli" mbid="'+data['data'][0]['content'][i]['TempID']+'">'+data['data'][0]['content'][i]['TempName']+'</div>';
                    }
                    $('.selmb1').html(templist);
                    $.myDialog.tcDivPosition('addtplmsg');//层位置
                });
            }else{
                $('.mytpl').hide();
            }
        });
        $('.templatelist1').live('click',function(){
            $('.templatelist1').css({'border-color':'#ddd','background-color':'#fff','color':'#333'});
            $(this).css({'border-color':'#00a0e9','background-color':'#00a0e9','color':'#fff'});
            //把选中模板名称放到设置名称中
            var clickTempName=$(this).html();
            $('.tempname').val(clickTempName);
        });
        //添加题型弹出框
        $('.addquestype').live('click',function(){
            var partid=self.getPartID(this);//得到卷编号
            var title='添加题型';
            var idname='boxaddtypes';
            var tmp_str='';
            tmp_str+='       <div class="addtypes_box">';
            tmp_str+='        <div class="test_boxlist msgW">';
            tmp_str+='            <span class="addtypes_box_name">题型列表:</span>';
            tmp_str+='            <span class="boxlist_sel">';
            tmp_str+='               <select  name="boxtypelist" id="boxtypelist">';
            tmp_str+='                    <option value="">-请选择题型-</option>';
            for(var i in Types[self.subjectID]){
                tmp_str+='<option value="'+Types[self.subjectID][i]['TypesID']+'">'+Types[self.subjectID][i]['TypesName']+'</option>';
            }

            tmp_str+='                </select>';
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist msgW">';
            tmp_str+='           <span class="addtypes_box_name">试题名称:</span>';
            tmp_str+='            <span class="boxlist_whereposition boxlist_sel">';
            tmp_str+='                <input type="text" name="newtypename" value="">';
            tmp_str+='           </span>';
            tmp_str+='<input type="hidden" name="partid" value="'+partid+'">';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist msgW">';
            tmp_str+='            <span class="addtypes_box_name">添加到:</span>';
            tmp_str+='            <span class="boxlist_sel">';
            tmp_str+='                <select  name="boxaddtypenow" id="boxaddtypenow">';
            tmp_str+='                    <option value="">-请选择-</option>';
            $('.questypename').each(function(){
                tmp_str+='                    <option value="'+$(this).parent().parent().parent().parent().attr('id')+'">'+$(this).html()+'</option>';

            })
            tmp_str+='                </select>';
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist msgW">';
            tmp_str+='            <span class="addtypes_box_name">位置:</span>';
            tmp_str+='            <span class="boxlist_whereposition">';
            tmp_str+='                <label><input type="radio" name="boxaddposition" value="before" > 之前</label>';
            tmp_str+='                <label><input type="radio" name="boxaddposition" value="after" checked> 之后</label>   ';
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="type_IfHidden">';
            tmp_str+='            <span class="addtypes_box_name">是否隐藏:</span>';
            tmp_str+='            <span class="boxlist_IfHidden">';
            tmp_str+='                <label><input type="radio" name="IfHidden" value="1" >是</label>';
            tmp_str+='                <label><input type="radio" name="IfHidden" value="0" checked>否</label>   ';
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist msgW">';
            tmp_str+='           <span class="addtypes_box_name">试题数量:</span>';
            tmp_str+='            <span class="boxlist_whereposition boxlist_sel">';
            tmp_str+='                <input type="text" name="boxaddnum" value="0" maxlength="2" size="2"> 道（仅必做题）  ';
            tmp_str+='           </span>';
            tmp_str+='<input type="hidden" name="partid" value="'+partid+'">';
            tmp_str+='        </div>';
            tmp_str+='    </div>';
            $.myDialog.normalMsgBox(idname,title,350,tmp_str,3);
        });
        //绑定试题类型选择change
        $('#boxtypelist').live('change',function(){
            var newtypename=$.trim($.myCommon.removeHTML($(this).children('option:selected').text()));
            if($(this).children('option:selected').val()==""){
                $('input[name="newtypename"]').val('');
            }else{
                $('input[name="newtypename"]').val(newtypename);
            }
        })
        //添加题型，点击确定
        $("#boxaddtypes .normal_yes").live('click',function(){
            var reg=/^[0-9]*$/;
            var paperNum=$('input[name="partid"]').val();
            var typesname=$.trim($.myCommon.removeHTML($('input[name="newtypename"]').val()));
            var typesnameid=$('#boxtypelist option:selected').val();
            var boxaddtypenow=$('#boxaddtypenow option:selected').text();//添加到
            var boxaddtypenowval=$('#boxaddtypenow option:selected').val();//页面元素ID
            var boxaddposition=$('input[name="boxaddposition"]:checked').val();
            var boxIfHidden=$('input[name="IfHidden"]:checked').val();
            var boxtypesaddnum=$('input[name="boxaddnum"]').val();
            if(typesnameid==""){
                $.myDialog.showMsg('题型没有选择！',1);
                return false;
            }
            if(typesname==""){
                $.myDialog.showMsg('题型名称不能为空！',1);
                return false;
            }
            if(typesname.length>20){
                $.myDialog.showMsg('题型名过长,限20字符！',1);
                return false;
            }
            if(!reg.test(boxtypesaddnum)){
                $.myDialog.showMsg('试题数量只能为数字(正整数)！',1);
                return false;
            }
            var testtypearr=self.initDefType('','','',typesnameid,typesname,boxtypesaddnum,boxIfHidden);//分数为空
            var idArr = new Array();
            var typeId = 0;
            if(boxaddtypenowval!=""){
                if(boxaddposition=="before"){
                    $('#'+boxaddtypenowval).before(testtypearr);
                    typeId = boxaddtypenowval;
                }else{
                    idArr = boxaddtypenowval.split('_');
                    typeId = idArr[0]+'_'+(parseInt(idArr[1])+1);
                    $('#'+boxaddtypenowval).after(testtypearr);
                }
            }else{
                if($('#parthead'+paperNum).next().find('.questypehead').length>0){
                    var idTmp = $('#parthead'+paperNum).next().find('.questypehead').last().attr('id');
                    idArr = idTmp.split('_');
                    typeId = idArr[0]+'_'+(parseInt(idArr[1])+1);
                }else{
                    typeId = 'questypehead'+paperNum+'_0';
                }
                $('#parthead'+paperNum).next().append(testtypearr);
            }
            self.initScores();
            self.resetTests();
            self.resetTypes();
            self.resetOrder();
            if(boxtypesaddnum>0){
                self.initTypeMove();
                self.initTestMove(typeId);
            }else{
                self.initTypeMove()
            }
            $('#boxaddtypes .tcClose').click();
        });
        //编辑题型弹出框
        $('.edittypes').live('click',function(){
            var boxtypesName=self.getBoxTypesName(this);
            var boxTypesRow=self.getQuesTypeHead(this);
            //缺少参数
            var title='编辑题型';
            var idname='boxedittypes';
            var tmp_str='';
            tmp_str+='      <div class="addtypes_box editTypeMsg">';
            tmp_str+='        <div class="test_boxlist">';
            tmp_str+='            <span class="addtypes_box_name">题型名称：</span>';
            tmp_str+='            <span class="boxlist_sel">';
            tmp_str+='                <input type="text" name="boxtesttypesName" value="'+boxtypesName+'">';
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist">';
            tmp_str+='            <span class="addtypes_box_name">移动到：</span>';
            tmp_str+='            <span class="boxlist_sel">';
            tmp_str+='                <select  name="boxedittypenow" id="boxedittypenow">';
            tmp_str+='                    <option value="">-请选择-</option>';
            $('.questypename').each(function(){
                if($(this).parent().parent().parent().parent().attr('id')!=boxTypesRow){
                    tmp_str+='                    <option value="'+$(this).parent().parent().parent().parent().attr('id')+'">'+$(this).html()+'</option>';
                }
            })
            tmp_str+='                </select><span class="tips">(可选)</span>';
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist">';
            tmp_str+='            <span class="addtypes_box_name">位置：</span>';
            tmp_str+='            <span class="boxlist_whereposition">';
            tmp_str+='                <label><input type="radio" name="boxeditposition" value="before"> 之前</label>';
            tmp_str+='                <label><input type="radio" name="boxeditposition" value="after" checked> 之后</label>';
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <span id="boxTypesRow" style="display:none;" boxTypesName="'+boxTypesRow+'"></span>';
            tmp_str+='    </div>';
            $.myDialog.normalMsgBox(idname,title,350,tmp_str,3);
        });
        //编辑题型确定
        $('#boxedittypes .normal_yes').live("click",function(){
            var boxTypesRow=$('#boxTypesRow').attr('boxTypesName');//题型名称id
            var boxTypesName=$.trim($.myCommon.removeHTML($('input[name="boxtesttypesName"]').val()));//设置的提醒名称
            var boxedittypenowid=$.trim($.myCommon.removeHTML($("#boxedittypenow option:selected").val()));//要移动的题型位置
            var boxedittypenow=$.trim($.myCommon.removeHTML($("#boxedittypenow option:selected").html()));//选中的移动的题型名称
            var boxeditposition=$.trim($.myCommon.removeHTML($('input[name="boxeditposition"]:checked').val()));//位置

            if(boxTypesName==""){
                $.myDialog.showMsg('试题名称不能为空！',1);
                return false;
            }
            if(boxTypesName.length>20){
                $.myDialog.showMsg('试题名称过长,限20字符！',1);
                return false;
            }
            $('#'+boxTypesRow).children().eq(0).children().eq(0).children().eq(0).find('.questypename').html(boxTypesName);
            if(boxedittypenowid!=""){

                if(boxeditposition=="before"){
                    $('#'+boxTypesRow).insertBefore($('#'+boxedittypenowid));
                }else{
                    $('#'+boxTypesRow).insertAfter($('#'+boxedittypenowid));
                }
            }
            self.initTypeMove();
            self.resetTypes();
            self.resetTests();
            $('#boxedittypes .tcClose').click();
        });
        //弹出框标题背景
        $('.test_boxlist').live('mouseenter',function(){
            $(this).css({'background-color':'#dff1fa'});
            $(this).find('.addtypes_box_name').css({'color':'#00a0e9'});
        });
        $('.test_boxlist').live('mouseleave',function(){
            $(this).css({'background-color':'#f4f4f4'});
            $(this).find('.addtypes_box_name').css({'color':'#767676'});
        });
    },
    //分数操作事件
    scoresClick:function(){
        var self=this;
        //批量设分弹出框
        $('.setallscores').live('click',function(){
            var setscoreid = $(this).parent().parent().parent().attr('id');
            var testtypeid =  $(this).parent().prev().attr('id').match(/\d+/g);
            var typemsg = self.testTypeMsg(testtypeid);
            var title='批量设分';
            var idname='setscores';
            var setscores='';//批量设分弹窗
            setscores = '';
            setscores += '本题分值：<input type="text" class="score" name="score" style="height:20px;text-align:left" maxlength="4" value="'+typemsg['DScore']+'">';
            setscores += '<input type="hidden" name="scoreid" value="'+setscoreid+'">';
            $.myDialog.normalMsgBox(idname,title,300,setscores,3);
        });
        //批量设分确认操作
        $('#setscores .normal_yes').live("click",function(){
            var scores = $("input[name=score]").val();
            if(self.checkFloat(scores)){
                var scoreid = $("input[name=scoreid]").val();
                self.setAllScores(scores,scoreid);
            }else{
                $.myDialog.showMsg('所设小题分数必须小于100且不能为0！',1);
                return false;
            }
        });
        //分值验证
        $('.boxscore').live('change',function(){
            var scoreval=$(this).val();
            if(!self.checkFloat(scoreval)){
                $.myDialog.showMsg('所设小题分数必须小于100且不能为0！',1);
            }
        });
    },
    //批量设分方法
    setAllScores:function(scores,scoreid){
        var self=this;
        var oldscore = new Array();
        var result = 0;
        var index = 0;
        $('#'+scoreid).find('.scores:not(th)').each(function(q,val){
            oldscore = $('#'+scoreid).find('.scores:not(th)').eq(q).html().match(/\d+(\.\d)*/g);
            var scoreStr='';
            var testScore = '';
            if(oldscore.length == 1){
                scoreStr = scores+'分';
                result = scores;
            }else{
                $.each(oldscore,function(d,value){
                    if(d%2==1){
                        scoreStr += scores+'分<br>';
                        testScore += scores+',';
                    }else{
                        scoreStr +=  '第'+value+'小题';
                    }
                })
                index = testScore.lastIndexOf(',');
                result = testScore.substring(0,index);
            }
            $('#'+scoreid).find('.scores:not(th)').eq(q).attr('scores',result);
            $('#'+scoreid).find('.scores:not(th)').eq(q).html('<span>'+scoreStr+'</span>');
        })
        self.initScores();
        $('#setscores .tcClose').click();
    },

    //重置分数
    initScores:function(){
        var type = 0;
        var scoren = 0;
        var currscoren='';
        var scores = 0;
        var scorehtml = '';
        var scoretotal = new Array();
        var resulttotal = 0;
        var result = 0;
        var chooseOrder = 0;
        var choose = 1;
        $('.questypehead').each(function(type){
            result=0;
            $(this).find('.scores:not(th)').each(function(){
                if($(this).prev().prev().prev().find('.chooseOrders').length>0){
                    var ifchoose = $(this).prev().prev().prev().find('.ifchoose').text();
                    if($(this).prev().prev().prev().find('.chooseOrders').text()==chooseOrder && choose>ifchoose){
                        return true;
                    }else if($(this).prev().prev().prev().find('.chooseOrders').text()!=chooseOrder){
                        chooseOrder++;
                        choose = 1;
                    }
                    choose++;
                }
                scorehtml = $(this).html();
                if($(this).prev().prev().html() > 1){
                    scoretotal = '';
                    scores = 0;
                    scoretotal = scorehtml.match(/\d+(\.\d)*/g);
                    $.each(scoretotal,function(scoren,currscoren){
                        if(scoren%2==1){
                            scores = currscoren*1+scores*1;
                        }
                    })
                }else{
                    scores = scorehtml.match(/\d+(\.\d)*/g);
                }
                result = result*1 + scores*1;
            });
            $('.questypehead').eq(type).find('.questypescore').html('(满分'+result+'分)');
            resulttotal = result*1+resulttotal*1;
        })
        $('.totalscore').html('满分:'+resulttotal+'分')
    },
    //分数处理
    dealScore:function(scores){
        var currScore = '';
        var tmpScore = '';
        if(scores.lastIndexOf(',')!=-1){
            var tmpScore = scores.split(',');
            for(var p=0;p<tmpScore.length;p++){
                currScore += '第'+(parseInt(p)+1)+'小题'+tmpScore[p]+'分<br>';
            }
        }else{
            currScore = scores+'分';
        }
        return currScore;
    },
    //试题操作事件
    testClick:function(){
        var self=this;
        /*
         *添加试题
         */
        $('.addtest').live('click',function(){
            //根据该题型，判断该题型的最大数量限制
            var i=0;
            var typeid=self.getAddTestID(this);
            var thistypeid=$(this).parents('.questypehead').find('.testType').attr('id').replace('testtype','');
            var typemsg=self.getTypeMsg(thistypeid);
            var nowtotal=self.getTypeNum(typeid,typemsg[1]);//目前已有试题数量
            if(nowtotal>=typemsg[0]){
                $.myDialog.showMsg('试题数量已经是极限了，不能再加了',1);
                return false;
            }

            var getifchoose=self.addGetIfChoose(this);
            var chooseattr=self.getChooseAttr();

            var testtypeid=self.getTypeID(this);
            var testmsg=self.testTypeMsg(testtypeid);
            var startScore=1;

            var forNum=testmsg['MaxScore'];
            var intelNum=testmsg['IntelNum'];
            if(parseInt(testmsg['MaxScore'])!=testmsg['MaxScore']){ //如果是小数
                startScore='0.5';
                forNum=testmsg['MaxScore']/0.5;
            }
            var l;
            //添加知识点
            var knowwhere='';
            if(chooseattr=='1'){
                l="knowledge";
                knowwhere='knowledgesel';
                $('#'+knowwhere+'').html('<span class="boxloading">模板加载中请稍候...</span>');
            }else{
                l="chapter";
                knowwhere='chapterknowledgesel';
                $('#'+knowwhere+'').html('<span class="boxloading">模板加载中请稍候...</span>');
            }
            $.post( U('Index/getData'), {'style':l,'subjectID':self.subjectID,'times':Math.random()}, function(data){
                    if($.myCommon.backLogin(data)==false){
                        return false;
                    };
                    if(l=='knowledge'){
                        data['data']= $.myCommon.setKnowledgeOption(data['data'],'');
                    }else if(l=='chapter'){
                        data['data']=$.myCommon.setChapterOption(data['data'],'');
                    }
                    $('#'+knowwhere+'').append(data['data']);
            });

            var title='添加试题';
            var idname='addtestbox';
            var tmp_str='';
            tmp_str+=' <div class="add_box">';
            tmp_str+='        <div class="test_boxlist">';
            tmp_str+='<input type="hidden" name="nowtotal" value="'+nowtotal+'">';
            tmp_str+='<input type="hidden" name="defnum" value="'+typemsg[0]+'">';
            tmp_str+='            <span class="addtypes_box_name">考查属性:</span>';
            tmp_str+='            <span class="boxlist_sel">';
            tmp_str+='                <label><input type="radio" name="boxtypesName" value="0" testtypeid="'+testtypeid+'" class="boxtypesName radiotype" checked>&nbsp;必做题&nbsp;</label>';
            if(testmsg['IfDo']=='0'){
                tmp_str+='                <label><input type="radio" name="boxtypesName" value="1" class="boxtypesName radiotype" testtypeid="'+testtypeid+'"  >&nbsp;选做&nbsp;</label>';
            }else{
                tmp_str+='                <label style="display:none"><input type="radio" name="boxtypesName" value="1" class="boxtypesName radiotype" disabled >&nbsp;选做&nbsp;</label>';
            }
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist putong">';
            tmp_str+='            <span class="addtypes_box_name">添加题数:</span>';
            tmp_str+='            <span class="boxlist_sel">';
            tmp_str+='              <select class="boxtestchooseltotal" testtypeid="'+testtypeid+'" style="width:50px">';
                                    for(var i=1;i<=9;i++){      //可以添加9道题
            tmp_str+='                   <option value="'+i+'"> '+i+' </option>';
                                    }
            tmp_str+='              </select> <font>道</font>';
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist duosel">';
            tmp_str+='            <span class="addtypes_box_name">几选几:</span>';
            tmp_str+='            <span class="boxlist_sel">';
            tmp_str+='                <input type="text" name="boxtestchooseltotal1" maxlength="1" size="1" value="3" class="boxtestchooseltotal" testtypeid="'+testtypeid+'"> 选 ';
            tmp_str+='                <input type="text" name="boxtestchooselend" maxlength="1" size="1" value="1">';
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist">';
            tmp_str+='            <span class="addtypes_box_scorename addtypes_box_name">试题数量:</span>';
            tmp_str+='            <div class="testnumlist_box">';
            tmp_str+='                <div class="testnumlist testlistsign list_1">';
            tmp_str+='                    <span class="boxlist_whereposition testnuminto">';
            tmp_str+='                       <label> 第1题 ';
            if(testmsg['IfChooseNum']==0){
                tmp_str+='                           <span class="boxlist_sel canchange"><select name="boxtestnum_1" class="smalltest" testid="1" testtypeid="'+testtypeid+'" style="width:50px"><option value="1">-请选择-</option> ';
                    for(var m in intelNum){
                        tmp_str+='<option value="'+intelNum[m]+'">'+intelNum[m]+'</option>';
                    }
                tmp_str+='   </select>小题</span>';
            }
            tmp_str+='                      </label>';
            tmp_str+='                    </span>';
            if(testmsg['IfChooseType']=='0'){
                tmp_str+='                    <span class="box_testchoose">';
                tmp_str+='                        <font class="boxlistsmalltest">试题类型:</font>';
                tmp_str+='                        <label><input type="radio" value="0" name="boxtestchoose_1" class="boxtestchoose_1" checked>默认</label>';
                tmp_str+='                        <label><input type="radio" value="1" name="boxtestchoose_1" class="boxtestchoose_1">选择</label>';
                tmp_str+='                        <label><input type="radio" value="2" name="boxtestchoose_1" class="boxtestchoose_1">选择非选择混合</label>';
                tmp_str+='                        <label><input type="radio" value="3" name="boxtestchoose_1" class="boxtestchoose_1">非选择</label>';
                tmp_str+='                    </span>';
            }

            tmp_str+='                </div>';
            tmp_str+='            </div>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist">';
            tmp_str+='            <span class="addtypes_box_scorename addtypes_box_name">分值:</span>';
            tmp_str+='            <span class="boxtest_score scorelist_box">';
            tmp_str+='               <p class="scorelistsign scorelist_1">第1题<span class="boxlist_sel">' +
                                        '<select class="boxscore" name="boxscore1"  style="width:50px">';
                                        for(var i=1;i<=forNum;i++){
                                            var select='';
                                            if(i==testmsg['DScore']){
                                                select='selected';
                                            }
            tmp_str+=                                '<option value="'+(i*startScore)+'" '+select+' >'+(i*startScore)+'</option>';
                                        }

            tmp_str+=                            '</select> 分</span>' +
                                    '</p>';
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist">';
            tmp_str+='            <span class="addtypes_box_scorename addtypes_box_name">难度:</span>';
            tmp_str+='            <span class="boxtest_score difflist_box">';
            tmp_str+='                <span>第1题<label class="test_diffsign" title="0.801-0.999"><input type="radio" name="boxdiff_1" value="1" qdid="1" textcont="容易" title="0.801-0.999">容易</label>';
            tmp_str+='<label class="test_diffsign" title="0.601-0.800"><input type="radio" name="boxdiff_1" value="2" qdid="2" textcont="较易" title="0.601-0.800">较易</label>';
            tmp_str+='<label class="test_diffsign" title="0.501-0.600"><input type="radio" name="boxdiff_1" value="3" qdid="3" checked textcont="一般" title="0.501-0.600">一般</label>';
            tmp_str+='<label class="test_diffsign" title="0.301-0.500"><input type="radio" name="boxdiff_1" value="4" qdid="4" textcont="较难" title="0.301-0.500">较难</label>';
            tmp_str+='<label class="test_diffsign" title="0.001-0.300"><input type="radio" name="boxdiff_1" value="5" qdid="5" textcont="困难" title="0.001-0.300">困难</label><br></span>';
            tmp_str+='        </span>';
            tmp_str+='        </div>';
            var tmpname='知识点';
            tmp_str+='        <div class="test_boxlist">';
            tmp_str+='            <span class="addtypes_box_scorename addtypes_box_name">所添'+tmpname+':</span>';
            tmp_str+='            <div id="knowledgelist_box">';
            tmp_str+='                <ul class="tabs knowledgelist_box" id="tabs">';
            tmp_str+='                   <li class="knowledgelistsign knowlist_1 thistab" knowid="1"><a href="#">第(1)题</a></li>';
            tmp_str+='                </ul>';
            if(chooseattr=='1'){
                tmp_str+='            <span class="addknowledge-wrap">';
                tmp_str+='               <select name="boxknowledge" id="knowledgesel" class="knowledgeoption">';
                tmp_str+='                    <option value="">-请选择-</option>';
                tmp_str+='               </select>';
                tmp_str+='               <span class="addknowledge" id="addknowledge">添加知识点</span>';
                tmp_str+='            </span>';
            }else{
                tmpname='章节';
                tmp_str+='            <span class="boxtest_score">';
                tmp_str+='               <select name="boxknowledge" id="chapterknowledgesel" class="testoption">';
                tmp_str+='                    <option value="">-请选择-</option>';
                tmp_str+='               </select>';
                tmp_str+='               <span class="addchapter" id="addchapter">添加章节</span>';
                tmp_str+='            </span>';
            }
            tmp_str+='                <ul class="tab_conbox knowlist_box" id="tab_conbox">';
            tmp_str+='                    <li class="tab_con knowboxsign knowlistbox_1" style="display:list-item">';
            tmp_str+='                       <p>请给第(1)题添加'+tmpname+'!</p>';
            tmp_str+='                    </li>';
            tmp_str+='                </ul>';
            tmp_str+='            </div>';
            tmp_str+='        </div>';
            tmp_str+='<span class="intotypeid" typeid="'+typeid+'"></span>';
            tmp_str+='<span class="getifchoose" getifchoose="'+getifchoose+'"></span>';
            tmp_str+='<span class="testtypeid" testtypeid="'+testtypeid+'"></span>';
            tmp_str+='<input type="hidden" name="thistypeid" value="'+thistypeid+'">';
            tmp_str+='    </div>';

            $.myDialog.normalMsgBox(idname,title,680,tmp_str,3);
        });
        //章节考查点联动
        $('.testoption').live('change',function(){
            var chooseattr=self.getChooseAttr();
            if(chooseattr=='2'){
                var l='chapter';
            }
            $(this).nextAll(".testoption").remove();
            var tt=$(this);
            if(tt.val()=='') return;
            $.post(U("Index/getData"),{'subjectID':self.subjectID,'style':l,'pID':tt.val(),'times':Math.random()},function(data){
                var output='';
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                output=$.myCommon.setChapterOption(data['data'],'');
                if(data['data']){
                    tt.after('<select class="testoption" '+data['data'][3]+'>'+'<option value="">请选择章节</option>'+output+'</select>');
                }
            });
        });
        //切换题型隐藏显示状态
        $('.ifHidden').live('click',function(){
            var ifHidden=$(this).attr('ifhidden');
            if(ifHidden=='1'){
                $(this).attr('ifhidden','0');
                $(this).find('.showtext').text('隐藏');
                $(this).parent().parent().attr('style','color:#000');
                $(this).find('.icoimg').addClass('hide');
            }else{
                $(this).attr('ifhidden','1');
                $(this).find('.showtext').text('显示');
                $(this).parent().parent().attr('style','color:#ddd');
                $(this).find('.icoimg').removeClass('hide');
            }
        });
        //知识点考查点联动
        $('.knowledgeoption').live('change',function(){
            var chooseAttr=self.getChooseAttr();
            if(chooseAttr=='1'){
                var l='knowledge';
            }
            $(this).nextAll(".knowledgeoption").remove();
            var tt=$(this);
            if(tt.val()=='') return;
            $.post(U("Index/getData"),{'subjectID':self.subjectID,'style':l,'pID':tt.val(),'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                var selectStr=$.myCommon.setKnowledgeOption(data['data'],'','knowledge');
                if(data['data']){
                    tt.after('<select class="knowledgeoption" '+data['data'][3]+'>'+'<option value="">请选择知识点</option>'+selectStr+'</select>');
                }
            });
        });
        //添加试题点击确定时
        $('#addtestbox .normal_yes').live('click',function(){
            var intotypeid=$('.intotypeid').attr('typeid');
            var getifchoose=$('.getifchoose').attr('getifchoose');
            var boxtesttype=$('input[name="boxtypesName"]:checked').val();
            var thistypeid=$('input[name="thistypeid"]').val();
            var typemsg=self.getTypeMsg(thistypeid);
            var error="";
            var errormsg="";
            var boxtestchoosel1='';
            //缺少验证
            if(boxtesttype=='0'){
                boxtestchoosel1=$('.boxtestchooseltotal').find('option:selected').val();//验证试题数
            }else{
                boxtestchoosel1=$('input[name="boxtestchooseltotal1"]').val();//验证试题数
            }

            if(boxtestchoosel1=="" || boxtestchoosel1==''){
                $.myDialog.showMsg("试题数不能为空！",1);
                return false;
            }
            $('.boxscore').each(function(){//验证试题分数
                if($(this).val()=='' || !self.checkFloat($(this).val())){
                    error='1';
                }
            })
            //boxtestchooseltotal1   boxtestchooselend  boxtypesName
            if(boxtesttype=='1'){
                if($('input[name="boxtestchooseltotal1"]').val()<=$('input[name="boxtestchooselend"]').val()){
                    $.myDialog.showMsg('实际选题数必须小于选题总数!',1);
                    return false;
                }
            }
            if(error){
                $.myDialog.showMsg('所设小题分数必须小于100且不能为0！',1);
                return false;
            }
            if(errormsg){
                $.myDialog.showMsg('请给试题'+errormsg+'添加知识点！',1);
                return false;
            }
            //验证结束
            /*调取添加试题函数的思路：
             使用for循环，每道试题，一次一次调取插入函数 */
            //题型编号  intotypeid
            //第一个参数，是否有小题
            //试题的类型
            //几道小题及每道小题的分值
            //该题的难度
            //该题的知识点
            //判断试题数量限制问题
            var testtotal=0;
            for(var i=0;i<boxtestchoosel1;i++){
                var smalltestnum=$('select[name="boxtestnum_'+(i+1)+'"] option:selected').val();//小题数量
                if(typeof(smalltestnum) =='undefined'){
                    smalltestnum=1;
                }
                testtotal=testtotal+parseInt(smalltestnum);
            }
            if(typemsg[1]!=1){
                testtotal=boxtestchoosel1;
            }
            var nowtotal=$('input[name="nowtotal"]').val();
            var defnum=$('input[name="defnum"]').val();
            if((parseInt(nowtotal)+parseInt(testtotal))>defnum){
                $.myDialog.showMsg('您给该题型添加的试题数量超过最大限制！',1);
                return false;
            }
            var boxtestchooselend=$('input[name="boxtestchooselend"]').val();//验证试题数
            for(var i=0;i<boxtestchoosel1;i++){
                var smalltestdiffval = $('input[name="boxdiff_'+(i+1)+'"]:checked').val();
                var smalltesttype=$('input[name="boxtestchoose_'+(i+1)+'"]:checked').val();//试题类型
                if(typeof(smalltesttype)=='undefined'){
                    smalltesttype="";
                }
                var smalltestnum=$('select[name="boxtestnum_'+(i+1)+'"] option:selected').val();//小题数量
                //根据题型信息判断，传递试题分值
                if(smalltestnum=='1' || typeof(smalltestnum)=="undefined"){
                    smalltestnum='1';
                    var smalltestscore=$('select[name="boxscore'+(i+1)+'"] option:selected').val();//没有小题分值
                }else{
                    var smalltestscore="";
                    for(var x=0;x<smalltestnum;x++){
                        //有小题的话的分值
                        smalltestscore+=$('select[name="boxscore'+(i+1)+'_'+(x+1)+'"] option:selected').val()+',';
                    }
                    var scolastindx=smalltestscore.lastIndexOf(',');
                    smalltestscore=smalltestscore.substring(0,scolastindx);
                }
                var smalltestknowledge="";
                var smalltestknowledgeid="";
                $('.knowlistres_'+(i+1)+'').each(function(){
                    smalltestknowledgeid+=$(this).attr('knowledgeidarr')+',';
                    smalltestknowledge+=$(this).text().replace('✕','')+',';//该题的知识点
                })
                var ledlastindex=smalltestknowledge.lastIndexOf(',');
                smalltestknowledge=smalltestknowledge.substring(0,ledlastindex);
                var ledidlastindex=smalltestknowledgeid.lastIndexOf(',');
                smalltestknowledgeid=smalltestknowledgeid.substring(0,ledidlastindex);
                if(boxtesttype=='1'){//根据判断是普通还是多选，传递几选几的第一个几
                    var chooseji=boxtestchoosel1;
                }else{
                    chooseji='';
                }
                if(smalltestknowledgeid=='' || smalltestknowledge==""){
                    smalltestknowledgeid="";
                    smalltestknowledge="";
                }
                var iftypenum=0;
                if(boxtesttype=='1'){
                    iftypenum=boxtestchooselend;
                }
                if(boxtesttype=='1'){
                    smalltestnum='';
                    smalltestscore=$('select[name="boxscore1"] option:selected').val();
                }
                var tdresult=self.loadTest(thistypeid,iftypenum,chooseji,'',smalltestnum,smalltestknowledge,smalltestknowledgeid,smalltestscore,smalltestdiffval,smalltesttype);
                $('#'+intotypeid).children().eq(1).children().eq(0).children().last().after(tdresult);
            }
            self.initTestMove(intotypeid);
            self.resetTests();//调用重置函数
            self.initScores();
            $('#addtestbox .tcClose').click();
        });
        /*
         *编辑试题
         */
        $('.edittest').live('click',function(){
            //修改试题调取获取试题函数
            var i=0;
            var typeid=$(this).parent().parent().parent().parent().parent().attr('id');
            var thistypeid=$(this).parent().parent().parent().parent().parent().find('.testType').attr('id').replace('testtype','');
            var typemsg=self.getTypeMsg(thistypeid);
            var nowtotal=self.getTypeNum(typeid,typemsg[1]);//目前已有试题数量
            var l;
            var testlistmsg=self.getTest(this);
            var testtotalnum=self.getTest(this).length;//试题数量
            var testmsg=self.testTypeMsg(testlistmsg[0]['typeId']);
            var startScore=1;
            var forNum=testmsg['MaxScore'];
            if(parseInt(testmsg['MaxScore'])!=testmsg['MaxScore']){ //如果是小数
                startScore='0.5';
                forNum=testmsg['MaxScore']/0.5;
            }
            if(testtotalnum=='1'){
                var testtype='0';//获取试题类型
            }else{
                testtype='1';
            }
            var intelNumSelect='';
            var intelNum=testmsg['IntelNum'];
            var alltestid="";
            var testnum="";
            var testscorearr=new Array();
            var testknowledgearr=new Array();
            var testknowledgeidarr=new Array();
            var testdiff= new Array();
            for(var id=0;id<testtotalnum;id++){
                alltestid+=testlistmsg[id]['testId']+",";
                testnum+=testlistmsg[id]['nums']+",";
                testscorearr[id]=testlistmsg[id]['scores'];
                testknowledgearr[id]=testlistmsg[id]['knowledge'];
                testknowledgeidarr[id]=testlistmsg[id]['rounds'];
                testdiff[id]=testlistmsg[id]['diff'];
            }
            var index = alltestid.lastIndexOf(',');
            alltestid = alltestid.substring(0,index);//获取所有试题ID

            var alltestidarr=new Array();
            var alltestidarr=alltestid.split(',');
            var start=alltestidarr[0].split('_');
            var startnum=start[2];//获取开始题行数

            var index1 = testnum.lastIndexOf(',');
            var testnumarr = testnum.substring(0,index1);//获取试题下的小题数量
            testnumarr=testnumarr.split(',');
            var bestarr=new Array();//
            for(var i=0;i<testscorearr.length;i++){
                bestarr[i]=new Array();
                for(var j=0;j<testscorearr[i].length;j++){
                    bestarr[i]=testscorearr[i].split(',');//试题分数
                }
            }
            var bestled=new Array();//知识点文字
            for(var a=0;a<testknowledgearr.length;a++){
                bestled[a]=new Array();
                for(var b=0;b<testknowledgearr[a].length;b++){
                    bestled[a]=testknowledgearr[a].split(',');
                }
            }
            var bestledid=new Array();
            for(var y=0;y<testknowledgeidarr.length;y++){
                bestledid[y]=new Array();
                for(var u=0;u<testknowledgeidarr[y].length;u++){
                    bestledid[y]=testknowledgeidarr[y].split(',');
                }
            }
            var chooseattr=self.getChooseAttr();//第一步的获取考查方式
            var typeid=self.getAddTestID(this);

            //添加知识点
            var knowwhere='';
            if(chooseattr=='1'){
                l="knowledge";
                knowwhere='knowledgesel';
                $('#'+knowwhere+'').html('<span class="boxloading">模板加载中请稍候...</span>');
            }else{
                l='chapter';
                knowwhere='chapterknowledgesel';
                $('#'+knowwhere+'').html('<span class="boxloading">模板加载中请稍候...</span>');
            }
            $.ajax({
                type: "POST",
                cache: false,
                url: U('Index/getData'),
                data: {'style':l,'subjectID':self.subjectID,'times':Math.random()},
                success: function(data){
                    if($.myCommon.backLogin(data)==false){
                        return false;
                    };
                    if(data['data'][0]['KlID']){
                        data['data']=$.myCommon.setKnowledgeOption(data['data'],'','knowledge');
                    }else{
                        data['data']= $.myCommon.setChapterOption(data['data'],'','chapter');
                    }
                    $('#'+knowwhere+'').append(data['data']);
                }
            });
            var title='编辑试题';
            var idname='edittestbox';
            var tmp_str='';
            tmp_str+=' <div class="add_box">';
            tmp_str+='        <div class="test_boxlist">';
            tmp_str+='<input type="hidden" name="nowtotal" value="'+nowtotal+'">';
            tmp_str+='<input type="hidden" name="defnum" value="'+typemsg[0]+'">';
            tmp_str+='<input type="hidden" name="thistypeid" value="'+thistypeid+'">';
            tmp_str+='            <input type="hidden" name="alltestid" value="'+alltestid+'">';
            tmp_str+='            <input type="hidden" name="startnum" value="'+startnum+'">';
            tmp_str+='            <span class="addtypes_box_name">考查属性:</span>';
            tmp_str+='            <span class="boxlist_sel">';
            if(testtype=='0'){
                tmp_str+='                <label><input type="radio" name="boxtypesName" value="0" class="boxtypesName radiotype" checked>&nbsp;必做题&nbsp;</label>';
            }else{
                tmp_str+='                <label style="display:none;"><input type="radio" name="boxtypesName" value="0" class="boxtypesName radiotype" disabled >&nbsp;必做题&nbsp;</label>';
            }
            if(testtype=='1'){
                tmp_str+='                <label><input type="radio" name="boxtypesName" value="1" class="boxtypesName radiotype" checked >&nbsp;选做&nbsp;</label>';
            }else{
                tmp_str+='                <label style="display:none;"><input type="radio" name="boxtypesName" value="1" class="boxtypesName radiotype" disabled>&nbsp;选做&nbsp;</label>';
            }
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            if(testtype=='0'){
                tmp_str+='        <div class="test_boxlist putong" style="display:block;">';
                tmp_str+='            <span class="addtypes_box_name">添加题数:</span>';
                tmp_str+='            <span class="boxlist_sel">';
                tmp_str+='              <select class="boxtestchooseltotal" testtypeid="'+testlistmsg[0]['typeId']+'" style="width:50px" name="boxtestchooseltotal">';
                for(var i=1;i<=9;i++){      //可以添加9道题
                    tmp_str+='                   <option value="'+i+'"> '+i+' </option>';
                }
                tmp_str+='              </select> <font>道</font>';
                tmp_str+='            </span>';
                tmp_str+='        </div>';
            }else{
                tmp_str+='        <div class="test_boxlist duosel" style="display:block;">';
                tmp_str+='            <span class="addtypes_box_name">几选几:</span>';
                tmp_str+='            <span class="boxlist_sel">';
                tmp_str+='                <input type="text" name="boxtestchooseltotal1" maxlength="1" size="1" value="'+testtotalnum+'" class="boxtestchooseltotal" testtypeid="'+testlistmsg[0]['typeId']+'"> 选 ';
                tmp_str+='                <input type="text" name="boxtestchooselend" maxlength="1" size="1" value="'+testlistmsg[0]['ifchoose']+'">';
                tmp_str+='            </span>';
                tmp_str+='        </div>';
            }


            tmp_str+='        <div class="test_boxlist">';
            tmp_str+='            <span class="addtypes_box_scorename addtypes_box_name">试题数量:</span>';
            tmp_str+='            <div class="testnumlist_box">';
            for(var i=0;i<testtotalnum;i++){
                tmp_str+='              <div class="testnumlist testlistsign list_'+(i+1)+'">';
                tmp_str+='              <span class="boxlist_whereposition">';
                tmp_str+='                  <label> 第'+(i+1)+'题 ';
                if(testmsg['IfChooseNum']==0){
                      tmp_str+='<span class="boxlist_sel canchange"><select name="boxtestnum_'+(i+1)+'" class="smalltest" testid="'+(i+1)+'"  style="width:50px"><option value="1">-请选择-</option> ';
                    for(var m in intelNum){
                        var intelNumSelect='';
                        if(intelNum[m]==testnumarr[i]){
                            intelNumSelect='selected';
                        }
                        tmp_str+='<option value="'+intelNum[m]+'" '+intelNumSelect+'>'+intelNum[m]+'</option>';
                    }
                    tmp_str+='   </select>小题</span>';
                }
                tmp_str+='</label>';
                tmp_str+='              </span>';

                if(testmsg['IfChooseType']=='0'){
                    tmp_str+='              <span class="box_testchoose">';
                    tmp_str+='<font class="boxlistsmalltest">试题类型:</font>';
                    if(testlistmsg[i]['testchoose']=='0'){
                        tmp_str+='                  <label><input type="radio" value="0" name="boxtestchoose_'+(i+1)+'" class="boxtestchoose_'+(i+1)+'" checked>默认</label>';
                    }else{
                        tmp_str+='                  <label><input type="radio" value="0" name="boxtestchoose_'+(i+1)+'" class="boxtestchoose_'+(i+1)+'">默认</label>';
                    }
                    if(testlistmsg[i]['testchoose']=='1'){
                        tmp_str+='                  <label><input type="radio" value="1" name="boxtestchoose_'+(i+1)+'" class="boxtestchoose_'+(i+1)+'" checked>选择</label>';
                    }else{
                        tmp_str+='                  <label><input type="radio" value="1" name="boxtestchoose_'+(i+1)+'" class="boxtestchoose_'+(i+1)+'" >选择</label>';
                    }
                    if(testlistmsg[i]['testchoose']=='2'){
                        tmp_str+='                  <label><input type="radio" value="2" name="boxtestchoose_'+(i+1)+'" class="boxtestchoose_'+(i+1)+'" checked>选择非选择混合</label>';
                    }else{
                        tmp_str+='                  <label><input type="radio" value="2" name="boxtestchoose_'+(i+1)+'" class="boxtestchoose_'+(i+1)+'">选择非选择混合</label>';
                    }
                    if(testlistmsg[i]['testchoose']=='3'){
                        tmp_str+='                  <label><input type="radio" value="3" name="boxtestchoose_'+(i+1)+'" class="boxtestchoose_'+(i+1)+'" checked>非选择</label>';
                    }else{
                        tmp_str+='                  <label><input type="radio" value="3" name="boxtestchoose_'+(i+1)+'" class="boxtestchoose_'+(i+1)+'">非选择</label>';
                    }
                    tmp_str+='              </span>';
                }
                tmp_str+='              </div>';
            }
            tmp_str+='            </div>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist">';
            tmp_str+='            <span class="addtypes_box_scorename addtypes_box_name">分值:</span>';
            tmp_str+='            <span class="boxtest_score scorelist_box">';
            //双层循环
            if(testtype=='0' && testmsg['IfChooseNum']=='0'){
                for(var z=0;z<bestarr.length;z++){
                    if(bestarr[z].length!='1'){
                        tmp_str+='<span class="scorelistsign scorelist_'+(z+1)+'">';
                        for(var x=0;x<bestarr[z].length;x++){
                            tmp_str+='<p class="havesmall_'+(z+1)+' fat_son_'+(z+1)+'_'+(x+1)+'">第'+(z+1)+'题<font class="boxlistsmalltest"> 第('+(x+1)+')小题 </font><span class="boxlist_sel">' +
                            '<select class="boxscore" style="width:50px"  name="boxscore'+(z+1)+'_'+(x+1)+'">';
                            for(var i=1;i<=forNum;i++){
                                var select='';
                                if((i*startScore)==bestarr[z][x]){
                                    select='selected';
                                }
                                tmp_str+='<option value="'+(i*startScore)+'" '+select+' >'+(i*startScore)+'</option>';
                            }

                            tmp_str+='</select>' +
                            '分</span></p>';
                        }
                        tmp_str+='</span>';
                    }else{
                        tmp_str+='<p class="scorelistsign scorelist_'+(z+1)+'">第'+(z+1)+'题<span class="boxlist_sel">' +
                        '<select class="boxscore" style="width:50px" name="boxscore'+(z+1)+'">';
                        for(var i=1;i<=forNum;i++){
                            var select='';
                            if((i*startScore)==bestarr[z][0]){
                                select='selected';
                            }
                            tmp_str+='<option value="'+(i*startScore)+'" '+select+' >'+(i*startScore)+'</option>';
                        }

                        tmp_str+='</select> 分</span>' +
                        '</p>';
                    }
                }
            }else{
                tmp_str+='<p class="scorelistsign scorelist_1">第1题<span class="boxlist_sel">' +
                '<select class="boxscore" style="width:50px" name="boxscore1">';
                for(var i=1;i<=forNum;i++){
                    var select='';
                    if((i*startScore)==bestarr[0][0]){
                        select='selected';
                    }
                    tmp_str+=                                '<option value="'+(i*startScore)+'" '+select+' >'+(i*startScore)+'</option>';
                }

                tmp_str+=                            '</select> 分</span>' +
                '</p>';
            }
            tmp_str+='            </span>';
            tmp_str+='        </div>';
            tmp_str+='        <div class="test_boxlist">';
            tmp_str+='            <span class="addtypes_box_scorename addtypes_box_name">难度:</span>';
            tmp_str+='            <span class="boxtest_score difflist_box">';
            $.each(testdiff,function(p,val){
                if(testdiff[p]=="1"){
                    tmp_str+='<span>第'+(p*1+1)+'题<label class="test_diffsign " title="0.801-0.999"><input type="radio" name="boxdiff_'+(p+1)+'" value="1" qdid="1" textcont="容易" checked title="0.801-0.999">容易</label>';
                }else{
                    tmp_str+='<span>第'+(p*1+1)+'题<label class="test_diffsign " title="0.801-0.999"><input type="radio" name="boxdiff_'+(p+1)+'" value="1" textcont="容易" qdid="1" title="0.801-0.999">容易</label>';
                }
                if(testdiff[p]=="2"){
                    tmp_str+='<label class="test_diffsign " title="0.601-0.800"><input type="radio" name="boxdiff_'+(p+1)+'" value="2" qdid="2" textcont="较易" checked title="0.601-0.800">较易</label>';
                }else{
                    tmp_str+='<label class="test_diffsign " title="0.601-0.800"><input type="radio" name="boxdiff_'+(p+1)+'" value="2" qdid="2" textcont="较易" title="0.601-0.800">较易</label>';
                }
                if(testdiff[p]=="3"){
                    tmp_str+='<label class="test_diffsign " title="0.501-0.600"><input type="radio" name="boxdiff_'+(p+1)+'" value="3" qdid="3" checked textcont="一般" title="0.501-0.600">一般</label>';
                }else{
                    tmp_str+='<label class="test_diffsign " title="0.501-0.600"><input type="radio" name="boxdiff_'+(p+1)+'" value="3" qdid="3" textcont="一般" title="0.501-0.600">一般</label>';
                }
                if(testdiff[p]=="4"){
                    tmp_str+='<label class="test_diffsign " title="0.301-0.500"><input type="radio" name="boxdiff_'+(p+1)+'" value="4" qdid="4" checked textcont="较难" title="0.301-0.500">较难</label>';
                }else{
                    tmp_str+='<label class="test_diffsign " title="0.301-0.500"><input type="radio" name="boxdiff_'+(p+1)+'" value="4" qdid="4" textcont="较难" title="0.301-0.500">较难</label>';
                }
                if(testdiff[p]=="5"){
                    tmp_str+='<label class="test_diffsign " title="0.001-0.300"><input type="radio" name="boxdiff_'+(p+1)+'" value="5" qdid="5" checked textcont="困难" title="0.001-0.300">困难</label>';
                }else{
                    tmp_str+='<label class="test_diffsign " title="0.001-0.300"><input type="radio" name="boxdiff_'+(p+1)+'" value="5" qdid="5" textcont="困难" title="0.001-0.300">困难</label>';
                }
                tmp_str+='<br></span>';
            })
            tmp_str+='            </span>';
            tmp_str+='        </div>';

            tmp_str+='        <div class="test_boxlist">';
            tmp_str+='            <span class="addtypes_box_scorename addtypes_box_name">所添知识点:</span>';
            tmp_str+='            <div id="knowledgelist_box">';
            tmp_str+='                <ul class="tabs knowledgelist_box" id="tabs">';
            for(var q=0;q<testtotalnum;q++){
                if(q=='0'){
                    tmp_str+='                  <li class="knowledgelistsign thistab knowlist_'+(q+1)+'" knowid="'+(q+1)+'"><a href="#">第'+(q+1)+'题</a></li>';
                }else{
                    tmp_str+='                  <li class="knowledgelistsign knowlist_'+(q+1)+'" knowid="'+(q+1)+'"><a href="#">第'+(q+1)+'题</a></li>';
                }
            }

            tmp_str+='                </ul>';
            if(chooseattr=='1'){
                tmp_str+='            <span class="add-knowledge-box">';
                tmp_str+='               <select name="boxknowledge" id="knowledgesel" class="knowledgeoption">';
                tmp_str+='                    <option value="">-请选择-</option>';
                tmp_str+='               </select>';
                tmp_str+='               <span class="addknowledge" id="addknowledge">添加知识点</span>';
                tmp_str+='            </span>';
            }else{
                tmpname='章节';
                tmp_str+='            <span class="add-knowledge-box">';
                tmp_str+='               <select name="boxknowledge" id="chapterknowledgesel" class="testoption">';
                tmp_str+='                    <option value="">-请选择-</option>';
                tmp_str+='               </select>';
                tmp_str+='               <span class="addchapter" id="addchapter">添加章节</span>';
                tmp_str+='            </span>';
            }
            tmp_str+='                <ul class="tab_conbox knowlist_box" id="tab_conbox">';
            for(var h=0;h<testtotalnum;h++){
                if(h=="0"){
                    tmp_str+='                   <li class="tab_con knowboxsign knowlistbox_'+(h+1)+'" style="display: list-item;">';
                }else{
                    tmp_str+='                   <li class="tab_con knowboxsign knowlistbox_'+(h+1)+'">';
                }
                for(var l=0;l<bestled[h].length;l++){
                    if(bestled[h][l]=="全部"){
                        tmp_str+='              <p>请给第('+(h+1)+')题添加知识点!</p>';
                    }else{
                        tmp_str+='<p class="knowlistres_'+(h+1)+'" knowledgeidarr="'+bestledid[h][l]+'" knownum_testnum="'+bestledid[h][l]+'_'+(h+1)+'">'+bestled[h][l]+'<font class="delknowledge" title="移除" alt="移除" >✕</font></p>';
                    }
                }
                tmp_str+='                   </li>';
            }
            tmp_str+='                </ul>';
            tmp_str+='            </div>';
            tmp_str+='        </div>';
            tmp_str+='<span class="testtypeid" testtypeid="'+testlistmsg[0]['typeId']+'"></span>';
            tmp_str+='<span class="choosenum" choosenum="'+testlistmsg[0]['choosenum']+'"></span>';
            tmp_str+='    </div>';

            $.myDialog.normalMsgBox(idname,title,680,tmp_str,3);
        });
        //编辑试题点击确定
        $('#edittestbox .normal_yes').live('click',function(){
            var startnum=$('input[name="startnum"]').val();
            var alltestid=$('input[name="alltestid"]').val();
            var thistypeid=$('input[name="thistypeid"]').val();
            var choosenum=$('.choosenum').attr('choosenum');
            var typemsg=self.getTypeMsg(thistypeid);
            var error="";
            var errormsg="";
            var alltestidarr=new Array();
            alltestidarr=alltestid.split(',');
            //处理试题ID
            var idarr=new Array();
            idarr=alltestidarr[0].split('_');
            idarr.pop();
            var starttestnum=idarr.join('_');
            //缺少验证
            var boxtesttype=$('input[name="boxtypesName"]:checked').val();
            if(boxtesttype=='1'){
                var boxtestchoosel1=$('input[name="boxtestchooseltotal1"]').val();//验证试题数
                var boxtestchooselend=$('input[name="boxtestchooselend"]').val();//验证试题数
            }else{
                var boxtestchoosel1=$('select[name="boxtestchooseltotal"] option:selected').val();//验证试题数
            }
            if(boxtestchoosel1=="" || boxtestchoosel1==''){
                $.myDialog.showMsg("试题数不能为空！",1);
                return false;
            }
            $('.boxscore').each(function(){//验证试题分数
                if($(this).val()=='' || !self.checkFloat($(this).val())){
                    error='1';
                }
            })
            if(boxtesttype=='1'){
                if($('input[name="boxtestchooseltotal1"]').val()<=$('input[name="boxtestchooselend"]').val()){
                    $.myDialog.showMsg('实际选题数必须小于选题总数!',1);
                    return false;
                }
            }
            if(error){
                $.myDialog.showMsg('所设小题分数必须小于100且不能为0！',1);
                return false;
            }
            //判断是否减少
            var movenum=boxtestchoosel1*1+1;
            if(alltestidarr.length>boxtestchoosel1){
                for(var h=movenum;h<=alltestidarr.length;h++){
                    var key=h*1-1;
                    $('#'+alltestidarr[key]).remove();
                }
            }
            //空出试题ID差
            if(boxtestchoosel1>alltestidarr.length){
                var addnum=boxtestchoosel1-alltestidarr.length;
                if(boxtesttype=='1'){
                    self.changeID(self.chooseNumToID(choosenum),addnum);
                }else{
                    self.changeID(alltestid,addnum);
                }

            }
            //判断试题数量限制问题
            var testtotal=0;
            for(var i=0;i<boxtestchoosel1;i++){
                var smalltestnum=$('select[name="boxtestnum_'+(i+1)+'"] option:selected').val();//小题数量
                if(typeof(smalltestnum)=='undefined'){
                    smalltestnum=1;
                }
                testtotal=testtotal+parseInt(smalltestnum);
            }
            if(boxtesttype!=1){
                if(testtotal!=$('.boxscore').length){
                    $.myDialog.showMsg('小题设置数量与实际小题数量不一致！',1);
                    return false;
                }
            }
            var nowtotal=$('input[name="nowtotal"]').val();
            var defnum=$('input[name="defnum"]').val();
            if(typemsg[0]!=1){
                testtotal=boxtestchoosel1;
            }
            if((parseInt(nowtotal)+parseInt(testtotal))>defnum){
                $.myDialog.showMsg('您给该题型添加的试题超过最大数量！',1);
                return false;
            }
            for(var i=0;i<boxtestchoosel1;i++){
                var smalltestdiffval = $('input[name="boxdiff_'+(i+1)+'"]:checked').val();
                var smalltesttype=$('input[name="boxtestchoose_'+(i+1)+'"]:checked').val();//试题类型
                var smalltestnum=$('select[name="boxtestnum_'+(i+1)+'"] option:selected').val();//小题数量
                if(smalltestnum=='1' || typeof(smalltestnum)=='undefined'){
                    var smalltestscore=$('select[name="boxscore'+(i+1)+'"] option:selected').val();//没有小题分值
                }else{
                    var smalltestscore="";
                    for(var x=0;x<smalltestnum;x++){
                        //有小题的话的分值
                        smalltestscore+=$('select[name="boxscore'+(i+1)+'_'+(x+1)+'"] option:selected').val()+',';
                    }
                    var scolastindx=smalltestscore.lastIndexOf(',');
                    smalltestscore=smalltestscore.substring(0,scolastindx);
                }
                var smalltestknowledge="";
                var smalltestknowledgeid="";
                $('.knowlistres_'+(i+1)+'').each(function(){
                    //缺少知识点ID
                    smalltestknowledgeid+=$(this).attr('knowledgeidarr')+',';
                    smalltestknowledge+=$(this).text().replace('✕','')+',';//该题的知识点
                })
                var ledlastindex=smalltestknowledge.lastIndexOf(',');
                smalltestknowledge=smalltestknowledge.substring(0,ledlastindex);
                var ledidlastindex=smalltestknowledgeid.lastIndexOf(',');
                smalltestknowledgeid=smalltestknowledgeid.substring(0,ledidlastindex);
                if(boxtesttype=='1'){
                    smalltestnum='';
                    smalltestscore=$('select[name="boxscore1"] option:selected').val();
                }
                self.loadTest(thistypeid,boxtestchooselend,boxtestchoosel1,'',smalltestnum,smalltestknowledge,smalltestknowledgeid,smalltestscore,smalltestdiffval,smalltesttype,choosenum,'','','',starttestnum+'_'+(startnum*1+i));
            }
            var typemsg = new Array();
            var typemsgId = '';
            var typeMsg = starttestnum.match(/\d+/g);
            var typeMsgId = 'questypehead'+typeMsg[0]+'_'+typeMsg[1];
            self.initTestMove(typeMsgId);
            self.initScores();
            self.resetTests();
            $('#edittestbox .tcClose').click();
        });
        //删除题型
        $('.deltypes').live('click',function(){
            $(this).parent().parent().parent().remove();
            self.initTypeMove();
            self.resetTypes();
            self.resetTests();
            self.resetOrder();
            self.initScores();
        });
        //上移试题
        $('.upmove').live('click',function(){
            var testtype = 0;
            if($(this).parent().parent().find('.chooseOrders').length == 1){
                testtype = 1;
            }
            var nowprevobj = 0;
            if(testtype=="0"){
                if($(this).parent().parent().prev().find('.chooseOrders').length == 1){
                    nowprevobj = 1;
                }
            }else{
                var classtmp = $(this).parent().parent().find('.chooseOrders').html();
                if($('.chooseOrders:contains("'+classtmp+'")').first().parent().parent().parent().prev().find('.chooseOrders').length==1){
                    nowprevobj = 1;
                }
            }
            if(testtype=="0"){
                if($(this).parent().parent().prev().attr('class')==undefined){
                    return false;
                }
                if(nowprevobj!='0' && nowprevobj!='null'){//是选做题
                    var moveprevobj=$(this).parent().parent().prev().find('.chooseOrders').html();
                    if($(this).parent().parent().prev().find('.upmove').css('opacity')=='0.5'){
                        $('.chooseOrders:contains("'+moveprevobj+'")').parent().parent().parent().find('.upmove').css({opacity:'1'});
                        $(this).css({opacity:'0.5'});
                    }
                    if($(this).next().css('opacity')=='0.5'){
                        $(this).next().css({opacity:'1'});
                        $('.chooseOrders:contains("'+moveprevobj+'")').parent().parent().parent().find('.downmove').css({opacity:'0.5'});
                    }
                    $(this).parent().parent().after($('.chooseOrders:contains("'+moveprevobj+'")').parent().parent().parent());
                }else if(nowprevobj=='0' && nowprevobj!='null'){
                    if($(this).parent().parent().prev().find('.upmove').css('opacity')=='0.5'){
                        $(this).parent().parent().prev().find('.upmove').css({opacity:'1'});
                        $(this).css({opacity:'0.5'});
                    }
                    if($(this).next().css('opacity')=='0.5'){
                        $(this).next().css({opacity:'1'});
                        $(this).parent().parent().prev().find('.downmove').css({opacity:'0.5'});
                    }
                    $(this).parent().parent().prev().before($(this).parent().parent());
                }else{
                    return false;
                }
            }else{
                if(!$('.chooseOrders:contains("'+classtmp+'")').eq(0).parent().parent().parent().prev().attr('id')){
                    return false;
                }
                if(nowprevobj!='0' && nowprevobj!='null'){
                    var moveprevobj=$('.chooseOrders:contains("'+classtmp+'")').eq(0).parent().parent().parent().prev().find('.chooseOrders').html();
                    var prevobj = $('.chooseOrders:contains("'+classtmp+'")').eq(0).parent().parent().parent().prev();
                    if($(prevobj).find('.upmove').css('opacity') == '0.5'){
                        $('.chooseOrders:contains("'+classtmp+'")').parent().parent().parent().find('.upmove').css({opacity:'0.5'});
                        $('.chooseOrders:contains("'+moveprevobj+'")').parent().parent().parent().find('.upmove').css({opacity:'1'});
                    }
                    if($(this).next().css('opacity')=='0.5'){
                        $('.chooseOrders:contains("'+classtmp+'")').parent().parent().parent().find('.downmove').css({opacity:'1'});
                        $('.chooseOrders:contains("'+moveprevobj+'")').parent().parent().parent().find('.downmove').css({opacity:'0.5'});
                    }
                    $('.chooseOrders:contains("'+moveprevobj+'")').first().parent().parent().parent().before($('.chooseOrders:contains("'+classtmp+'")').parent().parent().parent());
                }else if(nowprevobj==0 && nowprevobj!='null'){
                    var prevobj = $('.chooseOrders:contains("'+classtmp+'")').eq(0).parent().parent().parent().prev();
                    if($(prevobj).find('.upmove').css('opacity')=='0.5'){
                        $('.chooseOrders:contains("'+classtmp+'")').parent().parent().parent().find('.upmove').css({opacity:'0.5'});
                        $(prevobj).find('.upmove').css({opacity:'1'});
                    }
                    if($(this).next().css('opacity')=='0.5'){
                        $('.chooseOrders:contains("'+classtmp+'")').parent().parent().parent().find('.downmove').css({opacity:'1'});
                        $(prevobj).find('.downmove').css({opacity:'0.5'});
                    }
                    $(prevobj).before($('.chooseOrders:contains("'+classtmp+'")').parent().parent().parent());
                }else{
                    return false;
                }
            }
            self.resetTests();
        });
        //下移试题
        $('.downmove').live('click',function(){
            var testtype = 0;
            if($(this).parent().parent().find('.chooseOrders').length == 1){
                testtype = 1;
            }
            var nownextobj = 0;
            if(testtype=="0"){
                if($(this).parent().parent().next().find('.chooseOrders').length == 1){
                    nownextobj = 1;
                }
            }else{
                var classtmp = $(this).parent().parent().find('.chooseOrders').html();
                if($('.chooseOrders:contains("'+classtmp+'")').last().parent().parent().parent().next().find('.chooseOrders').length==1){
                    nownextobj = 1;
                }
            }
            if(testtype=="0"){
                if($(this).parent().parent().next().attr('class')==undefined){
                    return false;
                }
                if(nownextobj!='0' && nownextobj!='null'){
                    var movenextobj=$(this).parent().parent().next().find('.chooseOrders').html();
                    if($(this).parent().parent().next().find('.downmove').css('opacity') == '0.5'){
                        $(this).css({opacity:'0.5'});
                        $('.chooseOrders:contains("'+movenextobj+'")').parent().parent().parent().find('.downmove').css({opacity:'1'})
                    }
                    if($(this).prev().css('opacity')=='0.5'){
                        $(this).prev().css({opacity:'1'});
                        $('.chooseOrders:contains("'+movenextobj+'")').parent().parent().parent().find('.upmove').css({opacity:'0.5'})
                    }
                    $(this).parent().parent().before($('.chooseOrders:contains("'+movenextobj+'")').parent().parent().parent());
                }else if(nownextobj=='0' && nownextobj!='null'){
                    if($(this).parent().parent().next().find('.downmove').css('opacity') == '0.5'){
                        $(this).css({opacity:'0.5'});
                        $(this).parent().parent().next().find('.downmove').css({opacity:'1'})
                    }
                    if($(this).prev().css('opacity')=='0.5'){
                        $(this).prev().css({opacity:'1'});
                        $(this).parent().parent().next().find('.upmove').css({opacity:'0.5'})
                    }
                    $(this).parent().parent().next().after($(this).parent().parent());
                }else{
                    return false;
                }
            }else{
                if(!$('.chooseOrders:contains("'+classtmp+'")').last().parent().parent().parent().next().attr('id')){
                    return false;
                }
                if(nownextobj!='0' && nownextobj!='null'){
                    var moveprevobj=$('.chooseOrders:contains("'+classtmp+'")').last().parent().parent().parent().next().find('.chooseOrders').html();
                    if($('.chooseOrders:contains("'+moveprevobj+'")').parent().parent().parent().find('.downmove').css('opacity')=='0.5'){
                        $('.chooseOrders:contains("'+moveprevobj+'")').parent().parent().parent().find('.downmove').css({opacity:'1'});
                        $('.chooseOrders:contains("'+classtmp+'")').parent().parent().parent().find('.downmove').css({opacity:'0.5'});
                    }
                    if($(this).prev().css('opacity')=='0.5'){
                        $('.chooseOrders:contains("'+classtmp+'")').parent().parent().parent().find('.upmove').css({opacity:'1'});
                        $('.chooseOrders:contains("'+moveprevobj+'")').parent().parent().parent().find('.upmove').css({opacity:'0.5'});
                    }
                    $('.chooseOrders:contains("'+moveprevobj+'")').last().parent().parent().parent().after($('.chooseOrders:contains("'+classtmp+'")').parent().parent().parent());
                }else if(nownextobj==0 && nownextobj!='null'){
                    var prevobj = $('.chooseOrders:contains("'+classtmp+'")').last().parent().parent().parent().next();
                    if($(prevobj).find('.downmove').css('opacity')=='0.5'){
                        $('.chooseOrders:contains("'+classtmp+'")').parent().parent().parent().find('.downmove').css({opacity:'0.5'});
                        $(prevobj).find('.downmove').css({opacity:'1'});
                    }
                    if($(this).prev().css('opacity')=='0.5'){
                        $('.chooseOrders:contains("'+classtmp+'")').parent().parent().parent().find('.upmove').css({opacity:'1'});
                        $(prevobj).find('.upmove').css({opacity:'0.5'});
                    }
                    $(prevobj).after($('.chooseOrders:contains("'+classtmp+'")').parent().parent().parent());
                }else{
                    return false;
                }
            }
            self.resetTests();
        });
        //删除试题
        $('.deltest').live('click',function(){
            //0 普通题  1选做题
            var testtype = 0;
            if($(this).parent().parent().find('.chooseOrders').length == 1){
                testtype = 1;
            }
            var delallsel=$(this).parent().parent().find('.chooseOrders').html();
            var testId = $(this).parent().parent().attr('id');
            var testArr = new Array();
            testArr = testId.match(/\d+/g);
            var typeId = 'questypehead'+testArr[0]+'_'+testArr[1];
            if(testtype=="0"){
                $(this).parent().parent().remove();
                self.initTestMove(typeId);
                self.resetTests();
                self.initScores();
            }else{
                $.myDialog.normalMsgBox('deltestmsg','信息提示',450,'<div><b class="delallselname" delallselname="'+delallsel+'" typeId="'+typeId+'">该题为选做题，您确定删除？</b></div>',3);
            }
        });
        //删除选做题
        $('#deltestmsg .normal_yes').live('click',function(){
            var delclassname=$('#deltestmsg .delallselname').attr('delallselname');
            var typeId = $('#deltestmsg .delallselname').attr('typeId');
            $('.chooseOrders:contains("'+delclassname+'")').parent().parent().parent().remove();
            self.initTestMove(typeId);
            self.resetTests();
            self.initScores();
            $('#deltestmsg .tcClose').click();
        });
        //点击试题切换选项卡
        $(".boxattrtest").live('click',function(){
            var ordernum=$(this).attr('numid');
            if(ordernum==""){
                return false;
            }
            $('.knowlist_'+ordernum+'').addClass("thistab").siblings("li").removeClass("thistab");
            $("#tab_conbox").children().eq(ordernum-1).show().siblings().hide();
        });
        //根据知识点调取考查点
        $('#addknowledge').live('click',function(){
            var testnum=$(".thistab").attr('knowid');//题号
            if(typeof(testnum)=="undefined" || testnum==""){
                $.myDialog.showMsg('请选择题号！',1);
                return false;
            }
            if(!$('.knowledgeoption:eq(0)').val()){
                $.myDialog.showMsg('请选择正确知识点！',1);
                return false;
            }
            if( $('.knowlistbox_'+testnum+'').find('p').eq(0).attr('class')==undefined){
                $('.knowlistbox_'+testnum+'').find('p').remove();
            }
            var xx_s="";
            var kid=$('.knowledgeoption').last().val().replace('t','');
            var tmp_position=0;
            if(!kid){
                tmp_position=1;
                kid=$('.knowledgeoption').last().prev().val().replace('t','');
            }
            var ok='';
            //先验证是不是已经选中过了，不能重复选中
            $('.knowlistres_'+testnum+'').each(function(){
                var tmpstr=$(this).attr("knownum_testnum");
                var ifright=''+kid+'_'+testnum+'';
                if( tmpstr == ''+ifright+'' ){
                    $.myDialog.showMsg("该考点已存在！",1);
                    ok=1;
                }
            });
            if(ok==1){
                return false;
            }
            $('.knowledgeoption').each(function(i){
                if($(this).find('option:selected').val()!="")
                    xx_s+=' >> '+$(this).find("option:selected").text();
            });
            var knowcontmp='<p class="knowlistres_'+testnum+'" knowledgeidarr="'+kid+'" knownum_testnum="'+kid+'_'+testnum+'">'+xx_s+'<font class="delknowledge" title="移除" alt="移除" >✕</font></p>';
            $('.knowlistbox_'+testnum+'').append(knowcontmp);

        });
        //根据章节调取考查点
        $('#addchapter').live('click',function(){
            var testnum = $(".thistab").attr('knowid');//题号
            if(typeof(testnum)=="undefined" || testnum==""){
                $.myDialog.showMsg('请选择题号！',1);
                return false;
            }
            if(!$('.testoption:eq(1)').val()){
                $.myDialog.showMsg('请选择正确章节！',1);
                return false;
            }
            if( $('.knowlistbox_'+testnum+'').find('p').eq(0).attr('class')==undefined){
                $('.knowlistbox_'+testnum+'').find('p').remove();
            }
            var cid=$('.testoption').last().val().replace('c','');
            var tmp_position=0;
            if(!cid){
                tmp_position=1;
                cid=$('.testoption').last().prev().val().replace('c','');
            }
            var ok='';
            //先验证是不是已经选中过了，不能重复选中
            $('.knowlistres_'+testnum+'').each(function(){
                var tmpstr=$(this).attr("knownum_testnum");
                var ifright=''+cid+'_'+testnum+'';
                if( tmpstr == ''+ifright+'' ){
                    $.myDialog.showMsg("该考查章节已存在！",1);
                    ok=1;
                }
            });
            if(ok==1){
                return false;
            }
            var xx_s="";
            $('.testoption').each(function(i){
                if(!(tmp_position==1 && $('.testoption').length==(i+1)))
                    xx_s+=' >> '+$(this).find("option:selected").text();
            });
            var knowcontmp='<p class="knowlistres_'+testnum+'" knowledgeidarr="'+cid+'" knownum_testnum="'+cid+'_'+testnum+'">'+xx_s+'<font class="delknowledge" title="移除" alt="移除" >✕</font></p>';
            $('.knowlistbox_'+testnum+'').append(knowcontmp);

        });
        //知识点移除 请给第(2)题添加知识点！
        $('.delknowledge').live('click',function(){
            var clname=new Array();
            var boxid=new Array();
            var kbox=$(this).parent().parent().attr('class');
            clname=kbox.split(' ');
            var liid=clname[2].split('_');
            $(this).parent().remove();
            if($('.knowlistbox_'+liid[1]+'').children().length=='0'){
                $('.knowlistbox_'+liid[1]+'').html('<p>请给第('+liid[1]+')题添加知识点！</p>')
            }

        });
        //普通题试题数量变化触发
        $('.boxtestchooseltotal').live('change',function(){
            var total=$(this).val();
            var testtypeid=$(this).attr('testtypeid');
            var testmsg=self.testTypeMsg(testtypeid);
            self.createTestNumList(total,testmsg);
            self.createScoreList(total,testmsg);
            self.createTestList(total);
            self.createKnowList(total);
        });
        //几选几变化触发
        $('input[name="boxtestchooseltotal1"]').live('change',function(){
            var total=$(this).val();
            var testtypeid=$(this).attr('testtypeid');
            var testmsg=self.testTypeMsg(testtypeid);
            self.createTestNumList(total,testmsg);
            self.createScoreList(1,testmsg);
            self.createTestList(total);
            self.createKnowList(total);
        });
        //试题类型选择
        $('.boxtypesName').live('change',function(){
            if($(this).val()=='0'){
                var testtypeid=$(this).attr('testtypeid');
                var testmsg=self.testTypeMsg(testtypeid);
                var testtotal=$('input[name="boxtestchooseltotal"]').val();
                self.createTestNumList(testtotal,testmsg);
                self.createScoreList(testtotal,testmsg);
                self.createTestList(testtotal);
                self.createKnowList(testtotal);
                $('.canchange').show();
                $('.smalltest').val('1');
                $("input[name='boxtestchooseltotal']").next().next().remove();
                $('.duosel').hide();
                $('.putong').show();
            }else{
                var testtypeid=$(this).attr('testtypeid');
                var testmsg=self.testTypeMsg(testtypeid);
                var testtotal=$('input[name="boxtestchooseltotal1"]').val();
                self.createTestNumList(testtotal,testmsg);
                self.createScoreList(1,testmsg);
                self.createTestList(testtotal);
                self.createKnowList(testtotal,testmsg);
                $('.canchange').hide();
                $("input[name='boxtestchooselend']").next().remove();
                $('.duosel').show();
                $('.putong').hide();
            }
        });
        //修改试题小题绑定
        $('.smalltest').live('change',function(){
            var listid=$(this).attr('testid');
            var testtypeid=$('.testtypeid').attr('testtypeid');
            var testmsg=self.testTypeMsg(testtypeid);
            var setlen=$(this).val();
            var reg=/^[1-9][0-9]?$/;

            if(!reg.test(setlen)){
                $.myDialog.showMsg('设置小题数必须是数字并且不能为0！',1);
                $(this).val('1');
                setlen=1;
            }
            var startScore=1;
            var forNum=testmsg['MaxScore'];
            if(parseInt(testmsg['MaxScore'])!=testmsg['MaxScore']){ //如果是小数
                startScore='0.5';
                forNum=testmsg['MaxScore']/0.5;
            }
            var havelen=$('.havesmall_'+listid+'').length;
            if(havelen =='' && setlen > 1){
                var smalltmp='<span class="scorelistsign scorelist_'+listid+'">';
                for(var i=0;i<setlen;i++){
                   // smalltmp+='<p class=" havesmall_'+listid+' fat_son_'+listid+'_'+(i+1)+'">第'+listid+'题<font class="boxlistsmalltest"> 第('+(i+1)+')小题 </font><span class="boxlist_sel"><input type="text" size="4" maxlength="4" name="boxscore'+listid+'_'+(i+1)+'" class="boxscore" value="'+testmsg['DScore']+'">分</span></p>';
                    smalltmp+='<p class=" havesmall_'+listid+' fat_son_'+listid+'_'+(i+1)+'">第'+listid+'题<font class="boxlistsmalltest"> 第('+(i+1)+')小题 </font><span class="boxlist_sel"><select class="boxscore" style="width:44px" name="boxscore'+listid+'_'+(i+1)+'">';
                    for(var j=1;j<=forNum;j++){
                        var select='';
                        if((j*startScore)==testmsg['DScore']){
                            select='selected';
                        }
                        smalltmp+=                                '<option value="'+(j*startScore)+'" '+select+' >'+(j*startScore)+'</option>';
                    }

                    smalltmp+=                            '</select>' +
                    '分</span></p>';
                }
                smalltmp+='</span>';
                $('.scorelist_'+listid+'').before(smalltmp);
                $('.scorelist_'+listid+'').last().remove();
                $('.havesmall_'+listid+'').last().next().remove();
            }else{
                if(havelen > setlen){
                    //移除*
                    setlen=setlen*1+1;
                    for(var i=setlen;i<=havelen;i++){
                        $('.fat_son_'+listid+'_'+i+'').remove();
                    }
                    if($('.havesmall_'+listid+'').length==1){
                        $('.havesmall_'+listid+'').find('font').eq(0).before(' ');
                        $('.havesmall_'+listid+'').find('font').eq(0).hide();
                        $('.havesmall_'+listid+'').find('.boxscore').eq(0).attr('name','boxscore'+listid+'');
                    }
                }else{
                    //追加
                    smalltmp='';
                    havelen=havelen*1+1;
                    for(var i=havelen;i<=setlen;i++){
                        //smalltmp+='<p class=" havesmall_'+listid+' fat_son_'+listid+'_'+i+'">第'+listid+'题<font class="boxlistsmalltest"> 第('+(i)+')小题 </font><span class="boxlist_sel"><input type="text" size="4" maxlength="4" name="boxscore'+listid+'_'+i+'" class="boxscore"> 分</span></p>';
                        smalltmp+='<p class=" havesmall_'+listid+' fat_son_'+listid+'_'+(i)+'">第'+listid+'题<font class="boxlistsmalltest"> 第('+(i)+')小题 </font><span class="boxlist_sel"><select class="boxscore" style="width:50px" name="boxscore'+listid+'_'+(i)+'">';
                        for(var j=1;j<=forNum;j++){
                            var select='';
                            if((j*startScore)==testmsg['DScore']){
                                select='selected';
                            }
                            smalltmp+=                                '<option value="'+(j*startScore)+'" '+select+' >'+(j*startScore)+'</option>';
                        }

                        smalltmp+=                            '</select>' +
                        '分</span></p>';
                    }
                    if($('.havesmall_'+listid+'').length==1){
                        $('.havesmall_'+listid+'').find('font').eq(0).before(' ');
                        $('.havesmall_'+listid+'').find('font').eq(0).hide();
                    }
                    $('.havesmall_'+listid+'').find('font').eq(0).show();
                    $('.havesmall_'+listid+'').last().after(smalltmp);
                    $('.havesmall_'+listid+'').find('.boxscore').eq(0).attr('name','boxscore'+listid+'_'+1+'');

                }
            }

        });
    },
    //获取一行数据
    getTest:function(obj){
        var self=this;
        var testArr = new Array();
        var testId = $(obj).parent().parent().attr('id');
        var typeId = $('#'+testId).parent().parent().prev().find('.testType').attr('id').match(/\d+/);
        var knowledge='';
        if($('#'+testId).find('.chooseOrders').length > 0){
            testArr['ifdo'] = $('#'+testId).find('.ifchoose').html();
            var choose = $('#'+testId).find('.chooseOrders').html();
            var tests = $('.chooseOrders:contains("'+choose+'")');
            $('.chooseOrders:contains('+choose+')').each(function(f){
                testArr[f] = new Array();
                testArr[f]['typeId'] = typeId;
                testArr[f]['testId'] = $(tests).eq(f).parent().parent().parent().attr('id');
                testArr[f]['choosenum'] = $(tests).eq(f).parent().attr('choosenum');
                testArr[f]['ifchoose'] = $(tests).eq(f).parent().find('.ifchoose').html();
                testArr[f]['ifchoose1'] = $(tests).eq(f).parent().find('.ifchoose1').html();
                testArr[f]['nums'] = $(tests).eq(f).parent().parent().next().html();
                testArr[f]['rounds'] = $(tests).eq(f).parent().parent().parent().find('.roundsid').attr('value');
                knowledge = $(tests).eq(f).parent().parent().parent().find('.rounds').html();
                testArr[f]['knowledge'] = self.transBr(knowledge);
                testArr[f]['scores'] = $(tests).eq(f).parent().parent().parent().find('.scores').attr('scores');
                testArr[f]['diff'] = $(tests).eq(f).parent().parent().parent().find('.diff').attr('diff');
                testArr[f]['testchoose'] = $(tests).eq(f).parent().parent().parent().find('.testchoosenum').val();
            })
        }else{
            testArr[0] = new Array();
            testArr[0]['typeId'] = typeId;
            testArr[0]['testId'] = testId;
            testArr[0]['ifchoose'] = 0;
            testArr[0]['choosenum'] = 0;
            testArr[0]['nums'] = $('#'+testId).find('.nums').html();
            testArr[0]['rounds'] = $('#'+testId).find('.roundsid').val();
            knowledge = $('#'+testId).find('.rounds').html();
            testArr[0]['knowledge'] = self.transBr(knowledge);
            testArr[0]['scores'] = $('#'+testId).find('.scores').attr('scores');
            testArr[0]['diff'] = $('#'+testId).find('.diff').attr('diff');
            testArr[0]['testchoose'] = $('#'+testId).find('.testchoosenum').val();
        }
        return testArr;
    },
    //获取试题知识点内容格式转化，(由br转换成逗号隔开)
    transBr:function(knowledgeData){
        var knowledgeArr = new Array();
        var knowledgeInfo='';
        if(knowledgeData.lastIndexOf('<br>')!=-1){
            knowledgeArr = knowledgeData.split('<br>');
            knowledgeInfo = knowledgeArr.join(',');
        }else{
            knowledgeInfo = knowledgeData;
        }
        return knowledgeInfo;
    },

    //统计第二步页面数据
    getDir2Data:function(){
        if($('.questypehead').length=0){
            $.myDialog.normalMsgBox('showmsg','提示信息',400,'没有题型数据！请确认。',2);
            return;
        }
        if($('.questest').length=0){
            $.myDialog.normalMsgBox('showmsg','提示信息',400,'没有试题数据！请确认。',2);
            return;
        }
        var self=this;
        var i = 0;
        var j = 0;
        var n = 0;
        var dir2Str = '';
        var lastIndex='';
        dir2Str += '[{"tempname":"'+$('.tempName').text()+'",';
        dir2Str += '"chooseattr":"'+self.getChooseAttr()+'",';
        dir2Str += '"doctype":"'+self.getDocType()+'",';
        dir2Str += '"gradelist":"'+self.getGradeList()+'",';
        dir2Str += '"arealist":"'+self.getAreaList()+'",';
        dir2Str += '"maintitle":"'+$('.maintitle ').text()+'",';
        dir2Str += '"subtitle":"'+$('.subtitle').text()+'",';
        dir2Str += '"subjectID":"'+self.subjectID+'",';
        dir2Str += '"notice":"'+$('.notice').html().replace(/\n/g,"<br/>")+'",';
        $('.paperpart').each(function(i){
            dir2Str += i+':{';
            dir2Str += '"parthead":"'+$('.partname').eq(i).html()+'",';
            dir2Str += '"partheaddes":"'+$('.partheaddes').eq(i).html().replace(/\n/g,"<br/>")+'",';
            $('.paperpart').eq(i).find('.questypehead').each(function(j){
                dir2Str += j+':{';
                dir2Str += '"questypename":"'+$(this).find('.questypename').text()+'",';
                dir2Str += '"ifHidden":"'+$(this).find('.ifHidden').attr('ifhidden')+'",';
                dir2Str += '"typeid":"'+$(this).find('.testType').attr('id').match(/\d+/)+'",';
                $('.paperpart').eq(i).find('.questypehead').eq(j).find('.questest').each(function(n){
                    dir2Str += n+':{';
                    if($(this).find('.chooseOrders').length>0){
                        dir2Str += '"ifchoose":"'+$(this).find('.ifchoose').html()+'",';
                        dir2Str += '"choosenum":"'+$(this).find('.chooseOrders').html()+'",';
                    }else{
                        dir2Str += '"ifchoose":"0",';
                        dir2Str += '"choosenum":"0",';
                    }
                    dir2Str += '"nums":"'+$(this).find('.nums').text()+'",';
                    dir2Str += '"rounds":"'+$(this).find('.roundsid').attr('value')+'",';
                    dir2Str += '"scores":"'+$(this).find('.scores').attr('scores')+'",';
                    dir2Str += '"diff":"'+$(this).find('.diff').attr('diff')+'",';
                    dir2Str += '"testchoose":"'+$(this).find('.testchoosenum').attr('value')+'"},';
                })
                var lastIndex = dir2Str.lastIndexOf(',');
                dir2Str = dir2Str.substring(0,lastIndex);
                dir2Str += '},';
            })
            lastIndex = dir2Str.lastIndexOf(',');
            dir2Str = dir2Str.substring(0,lastIndex);
            dir2Str += '},';
        })
        lastIndex = dir2Str.lastIndexOf(',');
        dir2Str = dir2Str.substring(0,lastIndex);
        dir2Str += '}]';
        var json = eval(dir2Str);
        return json[0];
    },
    //添加一行试题
    //2几 1几 '' 小题数 知识点文字 知识点ID 分数 难度 选题类型 '' '' '' '' 试题ID
    loadTest:function(typesstyle,ifchoose,ifchoose1,order,nums,knowledge,rounds,scores,diff,choosetest,choosenum,paper,type,test,testid){
        var testStr = '';
        if(knowledge=="knowledge"){
            knowledge="加载中...";
        }else if(knowledge==""){
            knowledge="全部";
        }
        if(paper == '' || typeof(paper) == 'undefined'){
            paper = 0;
        }
        if(type == ''|| typeof(type) == 'undefined'){
            type = 0;
        }
        if(test == '' || typeof(test) == 'undefined'){
            test = 0;
        }
        if(nums == ''|| typeof(nums)=='undefined'){
            nums = 1;
        }
        if(choosenum == '' || typeof(choosenum) == 'undefined'){
            choosenum = 0;
        }
        if(order == ''){
            order == 1;
        }
        if(rounds == '' || typeof(rounds) == 'undefined'){
            rounds = 'all';
        }
        if(choosetest == '' || choosetest == 0 || typeof(choosetest) == 'undefined'){
            choosetest = $.myTest.getTypes(Types[this.subjectID],typesstyle,'TypesStyle');
        }
        if(testid == '' || typeof(testid)=='undefined'){
            testStr = this.structureTest(ifchoose,ifchoose1,order,nums,knowledge,rounds,scores,diff,choosetest,choosenum,paper,type,test,testid);
            return testStr;
        }else{
            if($('#'+testid).length == 0){
                var addtestStr = this.structureTest(ifchoose,ifchoose1,order,nums,knowledge,rounds,scores,diff,choosetest,choosenum,paper,type,test,testid);
                if(ifchoose>0){
                    $('.chooseOrders:contains("'+choosenum+'")').parent().parent().parent().last().after(addtestStr);
                }else{
                    var idArr = testid.split('_');
                    testid = idArr[0]+'_'+idArr[1]+'_'+(idArr[2]-1);
                    $('#'+testid).after(addtestStr);
                }
            }else{
                $('#'+testid).find('.ifchoose1').html(ifchoose1);
                $('#'+testid).find('.ifchoose').html(ifchoose);
                $('#'+testid).find('.nums').html(nums);
                $('#'+testid).find('.rounds').html(this.knowledgeDeal(knowledge));
                $('#'+testid).find('.roundsid').attr('value',rounds);
                $('#'+testid).find('.scores').attr('scores',scores);
                $('#'+testid).find('.scores').html(this.dealScore(scores));
                $('#'+testid).find('.diff').attr('diff',diff);
                $('#'+testid).find('.diff').html(Diff[diff]);
                $('#'+testid).find('.testchoose').text(this.getTestChoose(choosetest));
                $('#'+testid).find('.testchoosenum').val(choosetest);
            }
        }
    },
    //构造一行试题
    structureTest:function(ifchoose,ifchoose1,order,nums,knowledge,rounds,scores,diff,choosetest,choosenum,paper,type,test,testid){
        var testStr = '';
        if(testid == '' || typeof(testid)=='undefined'){
            testid = 'questest'+paper+'_'+type+'_'+test;
        }
        if(ifchoose == '0' || typeof(ifchoose) == 'undefined'){
            testStr += '<tr id="'+testid+'" class="questest"><td>第<span  class="order">'+order+'</span>题</span></td>';
        }else{
            testStr += '<tr id="'+testid+'" class="questest"><td>第<span class="order">'+order+'</span>题' +
            '<li choosenum="'+choosenum+'">(选做<span class="chooseOrders">'+choosenum+'</span> <span class="ifchoose1">' +
            ''+ifchoose1+'</span>选<span class="ifchoose">'+ifchoose+'</span>)</li></td>';
        }
        testStr += '<td class="nums">'+nums+'</td>' +
        '<td><span class="rounds" style="display:block;text-align:left">'+this.knowledgeDeal(knowledge)+'</span><input type="hidden" class="roundsid" value="'+rounds+'"></td>' +
        '<td class="scores" scores='+scores+'>'+this.dealScore(scores)+'</td>' +
        '<td class="diff" diff="'+diff+'">'+Diff[diff]+'</td>' +
        '<td><span  class="testchoose">'+this.getTestChoose(choosetest)+'</span><input type="hidden" class="testchoosenum" value="'+choosetest+'"></td>' +
        '<td class="choosenum'+choosenum+'"><a class="edittest" isedit="1">修改</a><a class="deltest">删除</a><a class="upmove">上移</a><a class="downmove">下移</a></td>' +
        '</tr>';
        return testStr;
    },

    //如果有添加，在试题id下的数据批量加上某一数据
    changeID:function(lastid,incr){
        var lastIdArr = new Array();
        var currIdArr = new Array();
        lastIdArr = lastid.split('_');
        $('#'+lastid).parent().find('.questest').each(function(){
            currIdArr = $(this).attr('id').split('_');
            if(parseInt(currIdArr[2]) > parseInt(lastIdArr[2])){
                currIdArr[2] = parseInt(currIdArr[2])+incr;
                $(this).attr('id',currIdArr.join('_'));
            }
        })

    },
    //多知识点换行处理
    knowledgeDeal:function(knowledge){
        var knowledgeArr = new Array();
        var knowledgeStr='';
        if(knowledge.lastIndexOf(',')!=-1){
            knowledgeArr = knowledge.split(',');
            knowledgeStr = knowledgeArr.join('<br>');
        }else{
            knowledgeStr = knowledge;
        }
        return knowledgeStr;
    },

    //重置试题上下移样式
    initTestMove:function(typeId){
        var i = 0;
        $('#'+typeId).find('.questest').each(function(){
            $(this).find('.upmove').css({opacity:'1'});
            $(this).find('.downmove').css({opacity:'1'});
        })
        if($('#'+typeId).find('.questest').eq(0).find('.chooseOrders').length > 0){
            var addFirstChoose = $('#'+typeId).find('.questest').eq(0).find('.chooseOrders').html();
            $('#'+typeId).find('.chooseOrders:contains('+addFirstChoose+')').parent().parent().parent().find('.upmove').css({opacity:'0.5'});
        }else{
            $('#'+typeId).find('.questest').eq(0).find('.upmove').css({opacity:'0.5'});
        }
        if($('#'+typeId).find('.questest').last().find('.chooseOrders').length > 0){
            var addLastChoose = $('#'+typeId).find('.questest').last().find('.chooseOrders').html();
            $('#'+typeId).find('.chooseOrders:contains('+addLastChoose+')').parent().parent().parent().find('.downmove').css({opacity:'0.5'});
        }else{
            $('#'+typeId).find('.downmove').last().css({opacity:'0.5'});
        }
    },
    //获取当前题型的下的已添加的试题数量
    getTypeNum:function(id,howtotal){
        var num=0;
        $('#'+id).find('.nums').each(function(i){
            var everynum=$(this).text();
            if(i!=0){
                if(howtotal=='1'){
                    num=num+parseInt(everynum);
                }else{
                    if(everynum>1){
                        num=num+1;
                    }
                }
            }
        })
        return num;
    },
    //添加试题获取题型id
    getAddTestID:function(obj){
        return $(obj).parents('.questypehead').attr('id');
    },
    //获取是否选做
    addGetIfChoose:function(obj){
        var ifchoosearr = new Array();
        var i = 0;
        $(obj).parent().parent().parent().parent().parent().find('.questest').each(function(i){
            if($(this).find('.order').children().eq(0).attr('ifdo')==1){
                ifchoosearr[i] = $(this).find('.order').children().eq(2).attr('choosenum');
            }
        })
        if(ifchoosearr == null){
            return 0;
        }
        return ifchoosearr.pop();
    },
    //通过选作题号获取改选做题最后题目id
    chooseNumToID:function(choosenum){
        return $('.chooseOrders:contains('+choosenum+')').last().parent().parent().parent().attr('id');
    },
    /*
     * 创建试题数量列表
     */
    createTestNumList:function (num,testmsg){
        var boxtype=$('input[name="boxtypesName"]:checked').val();
        var end=$("input[name='boxtestchooselend']").val();
        var intelNum=testmsg['IntelNum'];
        if(typeof(end)=="undefined"){
            end=1;
        }
        var reg=/^\+?[1-9]*$/;
        if($.isNumeric(num) && reg.test(num) ){
            if($.isNumeric(end) && reg.test(end)){
                if(num < end){
                    if($('input[name="boxtypesName"]:checked').val()=="1"){
                        $("input[name='boxtestchooselend']").next().remove();
                        $("input[name='boxtestchooselend']").after('&nbsp;&nbsp;<span style="color:#fe7676">实际选题数必须小于选题总数!</span>');
                        $('.boxtestchooseltotal').val();
                    }
                    return false;
                }else{
                    if($('input[name="boxtypesName"]:checked').val()=="2"){
                        $("input[name='boxtestchooselend']").next().remove();
                    }else{
                        $("input[name='boxtestchooseltotal']").next().next().remove();
                    }
                    //验证成功！根据total for 循环
                    var listlen=$('.testlistsign').length;
                    if( listlen == 0){
                        $('.testnumlist_box').empty();
                        var testtmp='';
                        for(var i=0;i<num;i++){
                            testtmp+='<div class="testnumlist testlistsign list_'+(i+1)+'">';
                            testtmp+='<span class="boxlist_whereposition">';
                            testtmp+='   <label> 第'+(i+1)+'题 ';
                            if(testmsg['IfChooseNum']==0){
                                testtmp+='<span class="boxlist_sel canchange"><select name="boxtestnum_'+(i+1)+'" class="smalltest" testid="'+(i+1)+'"  style="width:50px"><option value="1">-请选择-</option> ';
                                for(var m in intelNum){
                                    testtmp+='<option value="'+intelNum[m]+'">'+intelNum[m]+'</option>';
                                }
                                testtmp+='   </select>小题</span>';
                             //   testtmp+='<span class="boxlist_sel canchange"><input type="text" name="boxtestnum_'+(i+1)+'" class="smalltest" maxlength="2" size="2" value="1" testid="'+(i+1)+'"> 小题</span>';
                            }
                            testtmp+='</label>';
                            testtmp+='</span>';
                            if(testmsg['ifchooseType']==0){
                                testtmp+='<span class="box_testchoose">';
                                testtmp+='<font class="boxlistsmalltest">试题类型:</font>';
                                testtmp+='<input type="radio" value="0" name="boxtestchoose_'+(i+1)+'" class="boxtestchoose_'+(i+1)+'" checked>;默认';
                                testtmp+='<input type="radio" value="1" name="boxtestchoose_'+(i+1)+'" class="boxtestchoose_'+(i+1)+'">;选择';
                                testtmp+='<input type="radio" value="2" name="boxtestchoose_'+(i+1)+'" class="boxtestchoose_'+(i+1)+'">;选择非选择混合';
                                testtmp+='<input type="radio" value="3" name="boxtestchoose_'+(i+1)+'" class="boxtestchoose_'+(i+1)+'">;非选择';
                                testtmp+='</span>';
                            }
                            testtmp+='</div>';
                        }

                        $('.testnumlist_box').append(testtmp);
                        if(boxtype=='1'){
                            $('.canchange').hide();
                        }

                    }else{
                        if(listlen > num){
                            //执行remove移除
                            num=num*1+1;
                            var testtmp='';
                            for(var i=num;i<=listlen;i++){
                                $('.list_'+i+'').remove();
                            }
                        }else{
                            //执行after追加
                            var testtmp='';
                            var lastlen=num-listlen;
                            listlen=listlen*1+1;
                            for(var i=listlen;i<=num;i++){
                                testtmp+='<div class="testnumlist testlistsign list_'+i+'">';
                                testtmp+='<span class="boxlist_whereposition">';
                                testtmp+='   <label> 第'+i+'题 ';
                                if(testmsg['IfChooseNum']==0){
                                    testtmp+='<span class="boxlist_sel canchange"><select name="boxtestnum_'+i+'" class="smalltest" testid="'+i+'"  style="width:50px"><option value="1">-请选择-</option> ';
                                    for(var m in intelNum){
                                        testtmp+='<option value="'+intelNum[m]+'">'+intelNum[m]+'</option>';
                                    }
                                    testtmp+='   </select>小题</span>';
                                }
                                testtmp+='</label>';
                                testtmp+='</span>';
                                if(testmsg['IfChooseType']=='0'){
                                    testtmp+='<span class="box_testchoose">';
                                    testtmp+='    <font class="boxlistsmalltest">试题类型:</font>';
                                    testtmp+='    <label><input type="radio" value="0" name="boxtestchoose_'+i+'" class="boxtestchoose_'+i+'" checked>默认</label>';
                                    testtmp+='    <label><input type="radio" value="1" name="boxtestchoose_'+i+'" class="boxtestchoose_'+i+'">选择</label>';
                                    testtmp+='    <label><input type="radio" value="2" name="boxtestchoose_'+i+'" class="boxtestchoose_'+i+'">选择非选择混合</label>';
                                    testtmp+='    <label><input type="radio" value="3" name="boxtestchoose_'+i+'" class="boxtestchoose_'+i+'">非选择</label>';
                                    testtmp+='</span>';
                                }
                                testtmp+='</div>';
                            }
                            $('.testlistsign').last().after(testtmp);
                            if(boxtype=='1'){
                                $('.canchange').hide();
                            }
                        }
                    }
                }
            }else{
                $("input[name='boxtestchooselend']").next().remove();
                $("input[name='boxtestchooselend']").after('&nbsp;&nbsp;<span style="color:#fe7676">实际选题数必须是数字并且不能为0!</span>');
                return false;
            }
        }else{
            if($('input[name="boxtypesName"]:checked').val()=="2"){
                $("input[name='boxtestchooselend']").next().remove();
                $("input[name='boxtestchooselend']").after('&nbsp;&nbsp;<span style="color:#fe7676">实际选题数必须是数字并且不能为0!</span>');
                $('.boxtestchooseltotal').val("");
            }else{
                $("input[name='boxtestchooseltotal']").next().next().remove();
                $("input[name='boxtestchooseltotal']").next().after('&nbsp;&nbsp;<span style="color:#fe7676">所设题数必须是数字并且不能为0!</span>');
                $('.boxtestchooseltotal').val("");
            }
            return false;
        }
    },
    /*
     *创建分值列表
     */
    createScoreList:function(num,testmsg){
        //验证成功！根据total for 循环
        var reg=/^\+?[1-9]*$/;
        if(!reg.test(num)){
            return false;
        }
        var startScore=1;
        var forNum=testmsg['MaxScore'];
        if(parseInt(testmsg['MaxScore'])!=testmsg['MaxScore']){ //如果是小数
            startScore='0.5';
            forNum=testmsg['MaxScore']/0.5;
        }
        var listlen=$('.scorelistsign').length;
        if( listlen == 0){
            $('.scorelist_box').empty();
            var scroetmp='';
            for(var i=0;i<num;i++){
                scroetmp+='               <p class="scorelistsign scorelist_'+(i+1)+'">第'+(i+1)+'题<span class="boxlist_sel">' +
                '<select class="boxscore" style="width:50px" name="boxscore'+(i+1)+'">';
                for(var j=1;j<=forNum;j++){
                    var select='';
                    if((j*startScore)==testmsg['DScore']){
                        select='selected';
                    }
                    scroetmp+=                                '<option value="'+(j*startScore)+'" '+select+' >'+(j*startScore)+'</option>';
                }

                scroetmp+=                            '</select> 分</span>' +
                '</p>';
               // scroetmp+='<p class="scorelistsign scorelist_'+(i+1)+'"> 第'+(i+1)+'题<span class="boxlist_sel"><input type="text" name="boxscore'+(i+1)+'" maxlength="4" size="4" class="boxscore" value="'+testmsg['DScore']+'">分</span></p>';
            }
            $('.scorelist_box').append(scroetmp);
        }else{
            if(listlen > num){
                //执行remove移除
                num=num*1+1;
                var scroetmp='';
                for(var i=num;i<=listlen;i++){
                    $('.scorelist_'+i+'').remove();
                }
            }else{
                //执行after追加
                scroetmp='';
                listlen=listlen*1+1;
                for(var i=listlen;i<=num;i++){
                    scroetmp+='               <p class="scorelistsign scorelist_'+(i)+'">第'+(i)+'题<span class="boxlist_sel">' +
                    '<select class="boxscore" style="width:50px" name="boxscore'+(i)+'">';
                    for(var j=1;j<=forNum;j++){
                        var select='';
                        if((j*startScore)==testmsg['DScore']){
                            select='selected';
                        }
                        scroetmp+=                                '<option value="'+(j*startScore)+'" '+select+' >'+(j*startScore)+'</option>';
                    }

                    scroetmp+=                            '</select> 分</span>' +
                    '</p>';
                   // scroetmp+='<p class="scorelistsign scorelist_'+(i)+'"> 第'+(i)+'题<span class="boxlist_sel"><input type="text" name="boxscore'+(i)+'" class="boxscore" maxlength="4" size="4" value="'+testmsg['DScore']+'">分</span></p>';
                }
                $('.scorelistsign').last().after(scroetmp);
            }
        }
    },

    /*
     *创建试题表
     */
    createTestList:function(num){
        //验证成功！根据total for 循环
        var reg=/^\+?[1-9]*$/;
        if(!reg.test(num)){
            return false;
        }
        var difflist = '';
        var listlen = $('.test_diffsign').length/5;
        if(listlen > num){
            for(var i = num*1+1;i<=listlen;i++){
                $('input[name=boxdiff_'+i+']').parent().parent().remove();
            }
        }else{
            var difflist = '';
            for(var i=listlen;i<num;i++){
                difflist+='<span>第'+(i+1)+'题<label class="test_diffsign " title="0.801-0.999"><input type="radio" name="boxdiff_'+(i+1)+'" value="1" textcont="容易" qdid="1">容易</label>' +
                '<label class="test_diffsign " title="0.601-0.800"><input type="radio" name="boxdiff_'+(i+1)+'" value="2" textcont="较易" qdid="2">较易</label>' +
                '<label class="test_diffsign " title="0.501-0.600"><input type="radio" name="boxdiff_'+(i+1)+'" value="3" textcont="一般" qdid="3" checked>一般</label>' +
                '<label class="test_diffsign " title="0.301-0.500"><input type="radio" name="boxdiff_'+(i+1)+'" value="4" textcont="较难" qdid="4">较难</label>' +
                '<label class="test_diffsign " title="0.001-0.300"><input type="radio" name="boxdiff_'+(i+1)+'" value="5" textcont="困难" qdid="5">困难</label>';
                difflist+='<br></span>';
            }

            $('.difflist_box').append(difflist);
        }
    },

    /*
     *创建知识点列表
     */
    createKnowList:function(num){
        var reg=/^\+?[1-9]*$/;
        if(!reg.test(num)){
            return false;
        }
        var listlen=$('.knowledgelistsign').length;
        if( listlen == 0){
            $('.knowledgelist_box').empty();//上部框
            var knowtmp='';
            for(var i=0;i<num;i++){
                if(i==0){
                    knowtmp+='<li class="knowledgelistsign thistab knowlist_'+(i+1)+'" knowid="'+(i+1)+'"><a href="#">第'+(i+1)+'题</a></li>';
                }else{
                    knowtmp+='<li class="knowledgelistsign knowlist_'+(i+1)+'" knowid="'+(i+1)+'"><a href="#">第'+(i+1)+'题</a></li>';
                }
            }
            $('.knowledgelist_box').append(knowtmp);
            //下部框
            $('.knowlist_box').empty();//上部框
            var knowboxtmp='';
            for(var i=0;i<num;i++){
                if(i==0){
                    knowboxtmp+='<li class="tab_con knowboxsign knowlistbox_'+(i+1)+'"  style="display: list-item;"><p>请给第('+(i+1)+')题添加知识点！</p></li>';
                }else{
                    knowboxtmp+='<li class="tab_con knowboxsign knowlistbox_'+(i+1)+'"><p>请给第('+(i+1)+')题添加知识点！</p></li>';
                }
            }
            $('.knowlist_box').append(knowboxtmp);
        }else{
            if(listlen > num){//执行remove移除
                num=num*1+1;
                var scroetmp='';
                for(var i=num;i<=listlen;i++){
                    $('.knowlist_'+i+'').remove();
                }
                for(var i=num;i<=listlen;i++){
                    $('.knowlistbox_'+i+'').remove();
                }
                $('.knowlist_1').addClass("thistab").siblings("li").removeClass("thistab");
                $("#tab_conbox").children().eq(0).show().siblings().hide();
            }else{//执行after追加
                var  knowtmp='';
                var  knowboxtmp='';
                listlen=listlen*1+1;
                for(var i=listlen;i<=num;i++){
                    knowtmp+='<li class="knowledgelistsign knowlist_'+i+'" knowid="'+i+'" diff="3"><a href="#">第('+i+')题</a></li>';
                }
                $('.knowledgelistsign').last().after(knowtmp);
                for(var k=listlen;k<=num;k++){
                    knowboxtmp+='<li class="tab_con knowboxsign knowlistbox_'+k+'"><p>请给第('+k+')题添加知识点！</p></li>';
                }
                $('.knowboxsign').last().after(knowboxtmp);
            }
        }
    },
    //获取修改题型的名称
    getBoxTypesName:function(obj){
        return $.trim($.myCommon.removeHTML($(obj).parent().prev().find('.questypename').text()));
    },
    //编辑题型获取题型id
    getQuesTypeHead:function(obj){
        var typeHead="";
        return  typeHead = $(obj).parent().parent().parent().attr('id');
    },
    //获取试卷卷数ID
    getPartID:function(obj){
        var partID = $(obj).parent().parent().attr('id');
        return partID.substr(partID.indexOf('1'),partID.length);
    },

    //重置题型上下移样式
    initTypeMove:function(){
        $('.typeset').each(function(){
            $(this).find('.uptypes').css({opacity:'1'});
            $(this).find('.downtypes').css({opacity:'1'});
        })
        if($('.paperpart').eq(0).find('.questypehead').length != '0'){
            $('.typeset').eq(0).find('.uptypes').css({opacity:'0.5'});
        }
        if($('.paperpart').eq(1).find('.questypehead').length != '0'){
            $('.typeset').last().find('.downtypes').css({opacity:'0.5'});
        }

    },
    //跳转第一步
    skipOne:function(){
        var self=this;
        $('.tostep1').live('click',function(){
            var tempStr=self.getDir2Data();
            if(!tempStr) return;
            self.tempContent=tempStr;
            $('.step').css('display','none');
            $('#dir1').css('display','block');
            $('.addtpl').css('display','none');
            self.initDivXdBox();
        });
    },
    //跳转第三步
    skipThree:function(){
        var self=this;
        $('.tostep3').live('click',function(){
            //获取第二页，内容数据函数
            var testLen=$('.questest').length;
            if(testLen=='0'){
                $.myDialog.normalMsgBox('emptyTest','温馨提示',550,'<div style="color:red;"><b>您还没有设置试题数量！去 <span class="emptySetTest"> <b style="color:green;cursor:pointer"> 设置 </b> </span>试题</b></div>',5);
                return false;
            }
            var tempStr=self.getDir2Data();
            if(!tempStr) return;
            self.tempContent=tempStr;
            self.dir3ZuJuan(self.tempContent);
            $('.step').css('display','none');
            $('#dir3').css('display','block');
            self.initDivXdBox();
        });
    },
    //第二步，数据为空时点击设置，弹出设置提醒框
    setEmptyTest:function(){
        $('.emptySetTest').live('click',function(){
            $('#dir2').find('.addtest').eq(0).click();
            $('#emptyTest .tcClose').click();
        })
    },
    /**
     * 第三个页面
     */
    //第三步所需函数
    skipFour:function(){
        var self=this;
        //试卷预览
        $('#topapercenter').live('click',function(){
            //获取第二页，内容数据函数
            var tmpstr=self.getDir2Data();
            if(!tmpstr) return;
            $.myDialog.normalMsgBox('toCenterMsgbox','下面转入试卷中心',450,$.myCommon.loading(),4);
            self.dir3ZuJuanShow(tmpstr);
        });
    },
    //显示选做题
    getChooseText:function(startnum,n,m){
        var tmp_str_1='';
        var tmp_str_2='';
        var tmp_i;
        for(tmp_i=0;tmp_i<n;tmp_i++){
            tmp_str_1+='、'+(parseInt(startnum)+tmp_i);
        }
        for(tmp_i=0;tmp_i<m;tmp_i++){
            tmp_str_2+='、'+shuzi[tmp_i];
        }
        return '请考生在第 '+tmp_str_1.substr(1)+' '+shuzi[n-1]+'题中任选'+shuzi[m-1]+'道做答，注意：只能做所选定的题目。如果多做，则按所做的第'+tmp_str_2.substr(1)+'个题目计分。';
    },
    dir3ZuJuan:function(dirmsgjson){
        $("#dir3 .dir").html('<p class="list_ts"><span class="ico_dd">数据加载中请稍候...</span></div>');//等待提示
        var self=this;
        var titleStr='';//试卷标题
        var typeStr='';//分卷
        var quseStr='';//题型
        var testStr='';//试题
        var quseStrs='';//没有试题的题型
        var typeStrs='';
        var minClass='';//最小高度样式
        var chooseStr='';//选作的外层
        var chooseNum=1;//每道选作的数量
        var choosePro=new Array();//选作的提示
        //将传递过来的数据中，不参与查找试题的部分找出来，只保留查找试题的条件
        var titles=new Array();
        var test='';                         //将条件部分，继续转换成JSON格式字符串
        var n=0,m=0,v=0;//循环标识
        var lastIndex='';
        var score=0,quesTypeScore=0,totalScore=0;       //分值统计
        var scores=new Array();        //有小题的时候分值
        for(var i in dirmsgjson){//循环参数，分割数据
            if(i==n){
                m=0;
                var title=new Array();//将数据分开放入
                test +='"'+i+'":{';
                for(var j in dirmsgjson[i]){
                    if(j==m){
                        v=0;
                        var titleList=new Array();
                        test +='"'+j+'":{';
                        test +='"typeid":"'+dirmsgjson[i][j]['typeid']+'",';
                        test +='"ifHidden":"'+dirmsgjson[i][j]['ifHidden']+'",';
                        score=0;
                        for(var k in dirmsgjson[i][j]){
                            if(k==v){
                                test +='"'+k+'":{';
                                for(var g in dirmsgjson[i][j][k]){
                                    test += '"'+g+'":"'+dirmsgjson[i][j][k][g]+'",';
                                }
                                lastIndex  = test.lastIndexOf(',');
                                test = test.substring(0,lastIndex);
                                test +='},';
                                v++;
                            }else{
                                titleList[k]=dirmsgjson[i][j][k];
                            }
                        }
                        totalScore +=score;
                        lastIndex  = test.lastIndexOf(',');
                        test = test.substring(0,lastIndex);
                        test +='},';
                        m++;
                        title[j]=titleList;
                    }else{
                        title[j]=dirmsgjson[i][j];
                    }
                }
                lastIndex  = test.lastIndexOf(',');
                if(lastIndex!=-1 && test.length-lastIndex==1){
                    test = test.substring(0,lastIndex);
                }else{
                    test +='"0":"null"'
                }
                test +='},';
                titles[i]=title;
                n++;

            }else{
                titles[i]=dirmsgjson[i];
            }
        }
        lastIndex  = test.lastIndexOf(',');
        test = test.substring(0,lastIndex);//去掉分卷最后的,
        test='[{"SubjectID":"'+dirmsgjson['subjectID']+'","chooseattr":"'+dirmsgjson['chooseattr']+'","arealist":"'+dirmsgjson['arealist']+'","gradelist":"'+dirmsgjson['gradelist']+'","doctype":"'+dirmsgjson['doctype']+'",'+test+'}]';
        var content=eval(test);                         //将条件转换成JSON数组

        $.ajax({
            type: "POST",
            cache: false,
            url: U('Index/getTestByContent'),
            data: {'Content':content[0],'Temp':dirmsgjson,'times':Math.random()},
            success: function(msg){
                var z=1;
                var x=0;
                var l=0;//循环校验码，用于判定只有题型的情况
                if($.myCommon.backLogin(msg)==false){
                    $("#dir3 .dir").html(msg.data);
                    return false;
                };
                if(msg['data']==null){
                    n=0;
                    for(var i in titles){
                        if(i==n){
                            quseStrs='';
                            m=0;
                            for(var j in titles[i]){
                                if(j==m){
                                    quseStrs +='<div id="dir3Qusetypename'+j+'"><div class="dir3TestTypes"><span class="dir3Tips">'+shuzi[x]+
                                    '、</span><span class="dir3Questypenames">'+titles[i][j]['questypename']+
                                    '</span><span class="dir3Questypescore">(满分'+titles[i][j]['questypescore']+
                                    '分)</span></div></div>';
                                    m++;
                                    x++;
                                }
                            }
                            n++;
                            typeStr += '<div class="dir3Parthead"><div class="dir3Paperpart"><strong>'+titles[i]['parthead']+
                            '</strong><div class="dir3Partheaddes">'+titles[i]['partheaddes']+'</div></div>'+quseStrs;
                        }
                    }
                }
                n=0;
                for(var a in msg['data'][0]){//循环试卷
                    quseStr='';
                    if(msg['data'][0][a]==''){
                        quseStrs='<div class="quesbody noQuse"><span><strong>该分卷下没有题型</strong></span></div>';
                    }else{
                        for(var b in msg['data'][0][a]){
                            quseStrs='';
                            testStr='';
                            quesTypeScore=0;
                            for(var d in msg['data'][0][a][b]){
                                if(isNaN(d)){
                                    continue;
                                }
                                //分值计算
                                score='';
                                if(msg['data'][0][a][b][d]['scores']!='0'){
                                    score='(本题'+msg['data'][0][a][b][d]['scores']+'分)';
                                    //如果为选做题的话，应计算真实分值
                                    if(content[0][a][b][d]['choosenum']!='0'){
                                        //判断是否是选作题的第一道题
                                        if(d!=0 && content[0][a][b][d]['choosenum']==content[0][a][b][d-1]['choosenum']){
                                            msg['data'][0][a][b][d]['scores']=0;
                                        }else{
                                            msg['data'][0][a][b][d]['scores']=msg['data'][0][a][b][d]['scores']*content[0][a][b][d]['ifchoose'];
                                        }
                                    }
                                    quesTypeScore +=parseFloat(msg['data'][0][a][b][d]['scores']);
                                }
                                //选做提示
                                if(content[0][a][b][d]['choosenum']!='0'){
                                    //判断是否是选作题的第一道题
                                    if(d!=0 && content[0][a][b][d]['choosenum']==content[0][a][b][d-1]['choosenum']){
                                        chooseStr='';
                                        chooseNum++;
                                        var endNum=parseInt(d);
                                        if(endNum>msg['data'][0][a][b].length || content[0][a][b][d]['choosenum']!=content[0][a][b][endNum]['choosenum']){
                                            choosePro[content[0][a][b][d]['choosenum']]=self.getChooseText(minNum,chooseNum,content[0][a][b][d]['ifchoose']);
                                            chooseNum=1;
                                        };
                                    }else{
                                        var minNum=z;
                                        chooseStr='<div id="dir3Choose'+content[0][a][b][d]['choosenum']+'" class="choosetext"></div>';
                                    }
                                }
                                if(msg['data'][0][a][b][d]['test'].indexOf('【小题')!=-1){//重新计算真实的小题数量，防止出现属性标错的情况，用于试卷预览Cookie小题分数
                                    msg['data'][0][a][b][d]['testnum']=msg[0][a][b][d]['test'].split('【小题').length-1;
                                }
                                testStr +=chooseStr+'<li class="dir3TestList" thisid="'+msg['data'][0][a][b][d]['testid']+'" testnum="'+msg['data'][0][a][b][d]['testnum']+'">';
                                testStr +='<div>'+
                                '<div class="quesbody">'+
                                '<span><strong>试题'+z+'.'+score+'</strong></span>'+
                                '<span thisid="'+msg['data'][0][a][b][d]['testid']+'">'+msg['data'][0][a][b][d]['test']+'</span>'+
                                '</div>'+
                                '<div class="quesanswer" tid="'+msg['data'][0][a][b][d]['testid']+'" show="0">'+
                                '<p class="list_ts"><span class="ico_dd">载入数据请稍候...</span></p>'+
                                '</div>'+
                                '</div>';
                                testStr +='</li>';
                                z++;
                            }
                            //判断是否有题，如果没题则给题型加上高度
                            if(testStr==''){
                                minClass=" dir3MinHeig";
                                testStr='<li class="dir3TestList" thisid="0"><div><div class="quesbody"><span><strong>该题型下没有试题</strong></span></div></div></li>'
                            }
                            totalScore +=parseFloat(quesTypeScore);
                            quseStr +='<div id="dir3Qusetypename'+x+'" class="dir3Qusetypename'+minClass+'"><div class="dir3TestTypes"';
                            if(msg['data'][0][a][b]['ifHidden']=='1'){
                                quseStr+='style="color:#b2b2b2"';
                            }
                            quseStr +='><strong><span class="dir3Tips">'+shuzi[x]+
                            '、</span><span class="dir3Questypenames">'+titles[a][b]['questypename']+
                            '</span><span class="dir3Questypescore">(满分'+quesTypeScore+
                            '分)</span></strong></div><ul class="dir3TypeName">'+testStr+'</ul></div>';
                            x++;
                            l++;
                            if(msg['data'][0][a][l]==undefined && titles[a][l] !=undefined){//判断是否只有题型
                                for(var i=l;i<titles[a].length;i++){
                                    quseStrs +='<div id="dir3Qusetypename'+x+'" class="dir3Qusetypename"><div class="dir3TestTypes"><strong><span class="dir3Tips">'+shuzi[x]+
                                    '、</span><span class="dir3Questypenames">'+titles[a][i]['questypename']+
                                    '</span><span class="dir3Questypescore">(满分'+titles[a][i]['questypescore']+
                                    '分)</span></strong></div></div>';
                                    x++;
                                }
                            }
                        }
                    }
                    typeStr += '<div class="dir3Parthead"><div class="dir3Paperpart"><strong>'+titles[a]['parthead']+
                    '</strong><div class="dir3Partheaddes">'+titles[a]['partheaddes']+'</div>'+quseStr+quseStrs+'</div></div>';//拼分卷
                    n++;
                    if(msg['data'][0][n]==undefined && titles[n]!=undefined){
                        for(var i=l;i<titles[n].length;i++){
                            quseStrs +='<div id="dir3Qusetypename'+b+'"><div class="dir3TestTypes dir3MinHeig"><strong><span class="dir3Tips">'+shuzi[x]+
                            '、</span><span class="dir3Questypenames">'+titles[n][i]['questypename']+
                            '</span><span class="dir3Questypescore">(满分'+titles[n][i]['questypescore']+
                            '分)</span></strong></div></div>';
                            x++;
                        }
                        typeStrs += '<div class="dir3Parthead"><div class="dir3Paperpart"><strong>'+titles[n]['parthead']+
                        '</strong><div class="dir3Partheaddes">'+titles[n]['partheaddes']+'</div>'+quseStrs+'</div>';
                    }
                }
                titleStr = '<div class="dir3Titleset"><div class="dir3Tempname">'+
                '<input type="hidden" value="'+dirmsgjson['subjectID']+'" id="dir3SubjectID" /><strong>'+titles["tempname"]+
                '<input type="hidden" value="'+msg['data'][1]+'" id="dir3LogPaper" />'+
                '</strong></div><div class="dir3Maintitle"><strong>'+titles['maintitle']+
                '</strong></div><div class="dir3Subtitle">'+titles['subtitle']+
                '</div><div class="dir3Totalscore">总分：'+totalScore+
                '分</div><div class="tleft">注意事项：</div><div class="dir3Notice">'+titles['notice']+
                '</div></div></div>';
                $("#dir3 .dir").html(titleStr+typeStr);
                for(var i in choosePro){
                    $('#dir3Choose'+i).html(choosePro[i]);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert( "获取数据失败！请重试。" );
            }
        });
    },
    //获取记分方式
    getTypeMsg:function(typeid){
        var typemsg=Types[this.subjectID];
        var typemsgarr=new Array();
        for(i=0;i<typemsg.length;i++){
            if(typemsg[i]['TypesID']==typeid){
                typemsgarr[0]=typemsg[i]['Num'];
                typemsgarr[1]=typemsg[i]['TypesScore'];
                typemsgarr[2]=typemsg[i]['MaxScore'];
            }
        }
        return typemsgarr;
    },
    /**
     *获取题型信息
     */
    testTypeMsg:function(testtypeid){
        var subjectID;
        if(this.tempContent['subjectID']){
            subjectID = this.tempContent['subjectID'];
        }else if(this.subjectID){
            subjectID = this.subjectID;
        }else{
            var data = new Array();
            data['data'] = new Array();
            data['data']['data'] = '请登录！下面转入登录页面。';
            data['data']['url'] = U('Index/index');
            data['status'] = '30205';
            $.myCommon.backLogin(data);
        }
        var ifchooseType=Types[subjectID];
        for(var i=0;i<ifchooseType.length;i++){
            if(Types[subjectID][i]['TypesID']==testtypeid){
                return  Types[subjectID][i];
            }
        }
    },
    /**
     * 验证设置分数是否合法
     * @param num
     * @returns {boolean}
     */
    checkFloat:function(num){
        var reg=/^[1-9]?[0-9](\.[1-9])?$/;
        if(!reg.test(num) || num=='0'){
            return false;
        }else{
            return true;
        }
    },
    /**
     * 获取试题类型
     * @param testchoose
     * @returns {string}
     */
    getTestChoose:function(testchoose){
        if(testchoose == 1){
            return '选择题';
        }else if(testchoose == 2){
            return '选择非选择混合';
        }else if(testchoose == 3){
            return '非选择题';
        }
    },
    //获取 chooseattr 模板
    getChooseAttr:function(){
        return $("input[name='chooseattr']:checked").val();//试题属性
    },
    //获取年级
    getGradeList:function(){
        var gradelist='';
        $("input[name='gradelist[]']").each(function(){
            if($(this).attr("checked")=='checked'){
                gradelist+=","+$(this).val();
            }
        });
        if(gradelist!='') gradelist=gradelist.substr(1);
        return gradelist;
    },
    //获取地区
    getAreaList:function(){
        if($('#areaall').attr('checked')=='checked'){
            return '0';
        }
        var arealist='';
        $("input[name='area[]']").each(function(){
            if($(this).attr("checked")=='checked'){
                arealist+=","+$(this).val();
            }
        });
        if(arealist!='') arealist=arealist.substr(1);
        return arealist;
    },
    //添加试题获取题型typeid
    getTypeID:function(obj){
        var typeid='';
        typeid = $(obj).parents('.questypehead').find('.testType').attr('id');
        return typeid.match(/\d+/);
    },
    //获取当前题型下试题数
    getArrLength:function(test){
        var arrLength = 0;
        $.each(test,function(time,value){
            if(typeof(value)=="object"){
                arrLength++;
            }
        })
        return arrLength;
    },

    //单个题型处理完毕，保存最后的序号
    getTestOrder:function(n,testStr){
        var testOrder;
        if(n==0){
            var testTmp = new Array();
            var strmp = $('.order:not(th)').last().html();
            testTmp = strmp.split('~');
            if(testTmp[1]){
                testOrder = testTmp[1];
            }else{
                testOrder = testTmp[0];
            }
        }else{
            var testTmp = new Array();
            var lastTest = new Array();

            testTmp = $('.order:not(th)').last().html();
            lastTest=testTmp.split('~');
            if(lastTest.length == 2){
                testOrder = lastTest[1];
            }else{
                testOrder = lastTest[0];
            }
        }
        return testOrder;
    },
    //获取初始化序号
    getOrder:function(test,testOrder,nums,prev){
        var order='';
        if(nums == 1){
            if(test == 0){
                order = (parseInt(testOrder)+1);
            }else{
                order = (parseInt(prev)+1);
            }
        }else{
            if(test == 0){
                order = (parseInt(testOrder)+1)+'~'+(parseInt(testOrder)+parseInt(nums));
            }else{
                order = (parseInt(prev)+1)+'~'+(parseInt(prev)+parseInt(nums));
            }
        }
        return order;
    },
    //试卷题序重置
    resetTests:function(){
        var nums = 0;
        var ifchoose = 1;
        var choosenumtmp = 0;
        var choosenum = 0;
        var count = 0;
        $('.paperpart').each(function(paper){
            $('.paperpart').eq(paper).find('.questypehead').each(function(type){
                if(paper == '0'){
                    count = type;
                }else{
                    count = $('.paperpart').eq(0).find('.questypehead').length+parseInt(type)
                }
                $('.paperpart').eq(paper).find('.questypehead').eq(type).find('.questest').each(function(test){
                    $(this).attr('id','questest'+paper+'_'+count+'_'+test);
                })
            })
        })

        for(var f=0;f<$('.questest').length;f++){
            nums = $('.questest').eq(f).find('.nums').html();
            var tmpNum;
            if(nums==1){
                if(f==0){
                    $('.questest').find('.order').eq(f).html((parseInt(f)+1));
                }else{
                    tmpNum = $('.questest').find('.order').eq(f-1).html();
                    var partNum = tmpNum.split('~');
                    if(partNum[1]){
                        var prevNum = partNum[1].match(/\d+/);
                    }else{
                        var prevNum = partNum[0].match(/\d+/);
                    }
                    $('.questest').find('.order').eq(f).html((parseInt(prevNum)+1));
                }
            }else{
                if(f==0){
                    tmpNum = '第0~0题';
                }else{
                    tmpNum = $('.questest').find('.order').eq(f-1).html();
                }
                var partNum = tmpNum.split('~');
                if(partNum[1]){
                    var prevNum = partNum[1].match(/\d+/);
                }else{
                    var prevNum = partNum[0].match(/\d+/);
                }
                $('.questest').find('.order').eq(f).html((parseInt(prevNum)+1)+'~'+(parseInt(prevNum)+parseInt(nums)));
            }
            if($('.questest').eq(f).find('.chooseOrders').length =='1'){
                var choose = $('.questest').eq(f).find('.ifchoose1').html();
                choosenumtmp++;
                $('.questest').eq(f).find('.chooseOrders').html(ifchoose);
                $('.questest').eq(f).find("li[choosenum]").attr('choosenum',ifchoose);
                if(choose == choosenumtmp){
                    ifchoose += 1;
                    choosenumtmp = 0;
                }
            }
        }
    },

    //重置位置标识
    resetOrder:function(){
        $('.paperpart').each(function(i){
            var n= 0,c=0;
            var order='';
            $('.paperpart').eq(i).find('.questypehead').each(function(j){
                n++;
                if(i == '0'){
                    c = j;
                }else{
                    c = $('.paperpart').eq(0).find('.questypehead').length+parseInt(j)
                }
                order=i+'_'+n+'_'+c;
                $(this).find('.typeset').attr('order',order);
            })
        })
    },
    //重置题型
    resetTypes:function(){
        var count = 0;
        $('.questypehead').each(function(i){
            $(this).find('.tips').html(shuzi[i]+'、');
        })
        $('.paperpart').each(function(paper){
            $('.paperpart').eq(paper).find('.questypehead').each(function(type){
                if(paper == '0'){
                    count = type;
                }else{
                    count = $('.paperpart').eq(0).find('.questypehead').length+parseInt(type)
                }
                $('.paperpart').eq(paper).find('.questypehead').eq(type).attr('id','questypehead'+paper+'_'+count);
            })
        })
    },
    //获取试题类型
    getDocType:function(){
        var doctype='';
        $("input[name='doctype[]']").each(function(){
            if($(this).attr("checked")=='checked'){
                doctype+=","+$(this).val();
            }
        });
        if(doctype!='') doctype=doctype.substr(1);
        return doctype;
    },
    //拼接cookie的字符串和展示试卷
    dir3ZuJuanShow:function(data){
        var self=this;
        var titles = []; //标题等试卷信息字段
        var parthead = []; //分卷信息
        var questypename = []; //题型信息
        var flag=new Array();  //临时计数 标记是否已经执行过 0试卷 1题型
        var questype=''; //记录题型属性
        var parttype=''; //记录分卷属性
        var cookieScore=0; //记录试题分值
        var test = []; //试题信息
        var parts=''; //合并后cookie数据
        var z=1; //分卷数量 从1开始
        var testID=new Array();
        var testsID=new Array();
        var testListID=new Array();
        var subjectID=$('#dir3SubjectID').val();
        $('.dir3Parthead').each(function(i){
            testsID=[];
            $('.dir3Parthead').eq(i).find('.dir3Qusetypename').each(function(j){
                testID=[];
                $('.dir3Parthead').eq(i).find('.dir3Qusetypename').eq(j).find('.dir3TestList').each(function(k){
                    testID[k]=$(this).attr('thisid');
                });
                testsID[j]=testID;
            })
            testListID[i]=testsID;
        });

        //标题等试卷信息字段
        titles.push('maintitle@$@1@$@'+data['maintitle']);
        titles.push('subtitle@$@1@$@'+data['subtitle']);
        titles.push('seal@$@1');
        titles.push('marktag@$@1@$@绝密★启用前');
        titles.push('testinfo@$@1@$@考察范围：xxx；考试时间：100分钟；命题人：xxx；');
        titles.push('studentinput@$@0@$@学校:__________姓名:__________班级:__________学号:__________');
        titles.push('score@$@1');
        titles.push('notice@$@1@$@'+data['notice']);

        //处理试卷信息，格式化为cookie模式
        for(var a in data){

            if(isNaN(a)){
                continue;
            }

            var l=1; //记录分卷下的题型数量
            flag[0]=0; //重置分卷记录

            //循环分卷
            for(var b in data[a]){

                if(isNaN(b) && flag[0]==0){ //仅记录一次
                    flag[0]=1;
                    parttype='parthead'+z+'@$@1@$@'+data[a]['parthead']+'@$@'+data[a]['partheaddes']+'@#@{#fenjuan#}';//分卷和题型拼接
                    continue;
                }

                //过滤掉非数字键值
                if(isNaN(b)){
                    continue;
                }

                flag[1]=0; //重置题型记录

                //循环题型
                for(var c in data[a][b]){

                    if(isNaN(c) && flag[1]==0){ //仅记录一次
                        flag[1]=1;

                        //处理题型是否隐藏 因模板组卷定义和试卷中心定义相反 所以做转换
                        if(data[a][b]['ifHidden']=='1'){
                            data[a][b]['ifHidden']='0';
                        }else{
                            data[a][b]['ifHidden']='1';
                        }

                        //获取题型的属性
                        for(var f in Types[subjectID]){
                            if(Types[subjectID][f]['TypesID']==data[a][b]['typeid']){
                                questype=Types[subjectID][f]['DScore']+'|'+Types[subjectID][f]['TypesScore']+'|'+Types[subjectID][f]['IfDo'];
                                break;
                            }
                        }

                        questype='questypehead'+z+'_'+l+'@$@'+(data[a][b]['ifHidden'])+'@$@'+data[a][b]['questypename']+'@$@（题型注释）@$@1@$@{#test#}@$@'+questype;//分卷和题型拼接
                        continue;
                    }

                    //过滤掉非数字键值
                    if(isNaN(c)){
                        continue;
                    }

                    //获取试题字符串
                    if(testListID[a][b][c]!='0'){ //判断如果没有搜索到试题，就从cookie踢出
                        //防止出现有小题标签引起的分数错误问题，大题分数(S),小题数量(M),平均分(P)向下取整，最后一个小题分数为S-(M-1)*P,其他小题为平均分的规则计算
                        //应该考虑确定小题数量并且指定好小题分数的情况
                        if(data[a][b][c]['scores'].indexOf(',')==-1 && data[a][b][c]['nums']!=1){//未指定小题数量但是试题中有小题标签时应该重新处理小题分数
                            var testNum=$('.dir3TestList[thisid='+testListID[a][b][c]+']').attr('testnum');
                            var avgScore=(parseInt(data[a][b][c]['scores'])/testNum).toFixed(1);
                            var endScore=Math.abs((parseInt(data[a][b][c]['scores'])-avgScore*(testNum-1)).toFixed(1));
                            var score='';
                            for(var ii=0;ii<testNum-1;ii++){
                                score+=Math.abs(avgScore)+',';
                            }
                            cookieScore=score+endScore;
                        }else{//指定好小题数量并且有小题分数时应按指定好的
                            cookieScore=data[a][b][c]['scores'];
                        }
                        test.push(testListID[a][b][c]+'|'+data[a][b][c]['nums']+'|'+cookieScore+'|'+data[a][b][c]['ifchoose']+'|'+data[a][b][c]['choosenum']);
                    }else{
                        continue;
                        //test.push(0);
                    }
                }
                questypename.push(questype.replace('{#test#}',test.join(';')));
                test=[];
                questype='';
                l++; //题型数量++
            }
            parthead.push(parttype.replace('{#fenjuan#}',questypename.join('@#@')));
            questypename=[];
            z++; //分卷数量++
        }
        /*
         if(parseInt(a)==i){//判断下标是否为数字
         var j=0;
         var l=1;
         qtns='';
         parthead='';
         for(var b in data[a]){//循环分卷
         if(parseInt(b)==j){
         var k=0;
         test ='';       //试题
         questypename='';//题型
         if(data[a][b]['ifHidden']=='1'){
         data[a][b]['ifHidden']='0';
         }else{
         data[a][b]['ifHidden']='1';
         }
         questypename += 'questypehead'+z+'_'+l+'@$@'+(data[a][b]['ifHidden'])+'@$@';
         for(var c in data[a][b]){//循环题型
         if(parseInt(c)==k){
         if(testListID[a][b][c]!='0'){//判断如果没有搜索到试题，就从cookie踢出
         //防止出现有小题标签引起的分数错误问题，大题分数(S),小题数量(M),平均分(P)向下取整，最后一个小题分数为S-(M-1)*P,其他小题为平均分的规则计算
         //应该考虑确定小题数量并且指定好小题分数的情况
         if(data[a][b][c]['scores'].indexOf(',')==-1 && data[a][b][c]['nums']!=1){//未指定小题数量但是试题中有小题标签时应该重新处理小题分数
         var testNum=$('.dir3TestList[thisid='+testListID[a][b][c]+']').attr('testnum');
         var avgScore=(parseInt(data[a][b][c]['scores'])/testNum).toFixed(1);
         var endScore=Math.abs((parseInt(data[a][b][c]['scores'])-avgScore*(testNum-1)).toFixed(1));
         var score='';
         for(var ii=0;ii<testNum-1;ii++){
         score+=Math.abs(avgScore)+',';
         }
         cookieScore=score+endScore;
         }else{//指定好小题数量并且有小题分数时应按指定好的
         cookieScore=data[a][b][c]['scores'];
         }
         test +=testListID[a][b][c]+'|'+data[a][b][c]['nums']+'|'+cookieScore+'|'+data[a][b][c]['ifchoose']+'|'+data[a][b][c]['choosenum']+';';
         }else{
         test +='';
         }
         k++;
         }else{
         questypename +=data[a][b]['questypename']+'@$@'; //题型拼接
         break;
         }
         }
         for(var f in Types[subjectID]){                            //拼题型的属性
         if(Types[subjectID][f]['TypesID']==data[a][b]['typeid']){
         questype='@$@'+Types[subjectID][f]['DScore']+'|'+Types[subjectID][f]['TypesScore']+'|'+Types[subjectID][f]['IfDo'];
         break;
         }
         }
         if(test!=''){
         test =test.substring(0,test.length-1);//去掉一个题型最后的;分隔符
         }else{
         test='0';
         }
         qtns += questypename+'(题型注释)@$@1@$@'+test+questype+'@#@';//题型和试题属性拼接
         j++;
         }else{
         parthead +=data[a][b]+'@$@';  //分卷名称和描述拼接
         }
         l++;
         }
         parthead =parthead.substring(0,parthead.length-3);//去掉一个分卷最后的@$@分隔符
         parts+='parthead'+z+'@$@1@$@'+parthead+'@#@'+qtns;//分卷和题型拼接
         i++;
         z++;
         }else{
         }*/

        parts =titles.join('@#@')+'@#@'+parthead.join('@#@'); //合并cookie数据

        var logPaperID=$('#dir3LogPaper').val();
        $.post(U("Index/updateLog"),{'id':logPaperID,'Param':parts,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            };
            if(data['status']=='1'){
                //记录数据
                editData.clear();
                editData.set('paperstyle',parts);
                var tmparr=self.setLeftMsg();
                $('#quescountdetail',window.parent.document).empty().html(tmparr[1]);
                $('#quescount',window.parent.document).empty().html(tmparr[0]);
                $('#quesbanklist',window.parent.document).remove();
                location.href=U("Home/Index/zuJuan");
            }
        })

    },
    setLeftMsg:function(){
        var testname=new Array();
        var testnum=new Array();
        var i=0;
        var testtotal=0;
        var baifen=new Array();
        var smallbaifen=new Array();
        var y;
        var z;
        var k=0;
        var f=55;
        var result=new Array();
        $('.dir3Qusetypename').each(function(i){
            testname[i]=$(this).find('.dir3Questypenames').html();//获取题型名称
            var x=0;
            $(this).find('.dir3TestList').each(function(){
                if($(this).attr('thisid')!='0'){
                    if(parseInt($(this).attr('testnum'))==0){
                        x+=1;
                    }else{
                        x+=parseInt($(this).attr('testnum'));
                    }
                }
            })
            testnum[i]=x;
        })
        for(y in testnum){
            testtotal=testtotal+parseInt(testnum[y]);
        }
        for(y in testnum){
            baifen[y]=(((parseInt(testnum[y])/testtotal)*100).toFixed());
            smallbaifen[y]=(parseInt(testnum[y])/testtotal).toFixed(2);
        }

        var output="<table cellpadding='0' cellspacing='2' border='0' align='center'><tbody>";
        for(z=0;z<baifen.length;z++){
            k=Math.floor(smallbaifen[z]*f);
            output+="<tr title='占"+baifen[z]+"%'><td align='right' title='" + testname[z] + "'"+(testname[z].length>6 ? ' width="105" ' : '')+">" + testname[z] + "：</td>" + "<td align='left'><span class='bilibox' style='width:" + f + "px;'>" + "<span class='bilibg' style='width:" + k + "px;'></span>" + "</span></td>" + "<td align='right'>" + testnum[z] + "题</td>" + "<td><a class='emptyquestype' href='javascript:void(0);' title='清空 " + testname[z] + "'></a></td></tr>";
        }
        output+="</tbody></table>";
        result[0]=testtotal;
        result[1]=output;
        return result;
    },
    //知识点选项卡切换方法
    knowledgeTabChange:function(tabtit,tab_conbox,shijian,num) {
        $(tab_conbox).find("li").hide();
        $(tabtit).find("li:first").addClass("thistab").show();
        $(tab_conbox).find("li:first").show();

        $(tabtit).find("li").live(shijian,function(){
            var knowid=$(this).attr('knowid');
            var diff = $(this).attr('diff');
            $("input[name='boxdiff']").attr('checked',false);
            $("input[name='boxdiff']").each(function(){
                if($(this).val()==''+diff+''){
                    $(this).attr('checked',true);
                }
            })
            $(this).addClass("thistab").siblings("li").removeClass("thistab");
            var activeindex = $(tabtit).find("li").index(this);
            $(tab_conbox).children().eq(activeindex).show().siblings().hide();
            return false;
        });
    }
};

$.dirTemplate.init();

