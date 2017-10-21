/**
 * 生成导学案模板
 * @author demo 
 */
jQuery.caseCommon={
    subjectID:Cookie.Get("SubjectId"),//学科ID
    chapterID:0,//最终章节ID
    chapterName:'',//章节名称
    chapterPath:'',//章节路径
    canChangePath:'', //可修改导学案路径
    showPath:'', //第二部显示路径
    tempType:0,//模板类型
    tplID:0,//模板ID
    tempContent:{},//cookie拆分后的内容
    docwidth:550,//索引
    cookie:localData.get('caseStyle'),
    systemForum:['预习案','探究案','练习案'],
    changeChapterID:'',   //点击下一章，上一章时，做条件筛选
    thisChapterName:'', //当前章节名称
    //初始化
    init:function(){
        var self=this;
        lock='';//ajax请求标识
        $('#loca_text span').html($('#cursubject',window.parent.document).text());
        self.ifCookie();//判断cookie是否存在，存在就显示第二步页面，不存在显示第一步页面
        self.setTestStyle(testStyle);
        self.bindSkipEvent();//绑定页面跳转事件
        self.layoutOne();//第一页面函数
        self.layoutTwo();//第二页面函数
        self.layoutThree();//第三页面函数\
        self.InitDivXdBox();//初始化宽高
        $(window).resize(function() {//改变窗口大小后初始化宽高
            self.InitDivXdBox();
        });
    },
    ifCookie:function(){
        var s='subjectID@$@'+this.subjectID+'@#@';
        if(this.cookie && this.cookie.indexOf(s) != -1){
            this.getChapterID();//获取章节ID
            this.showLayoutTwo();
        }else{
            $('#dir1').css('display','block');
            localData.remove('caseStyle');
        }
    },
    layoutOne:function(){
        this.showLayoutOne();//展示第一页面
        this.firstChapterSelectChange(URL);//章节联动
        this.chapterSelectChange(URL);//章节联动
        this.bindChooseType();//绑定组卷方式
        this.changeTemplate();//选中模板
        this.changeTempBackColor();//改变模板名称背景颜色
        this.delThisTpl(); //删除自己模板
    },
    layoutTwo:function(){
        $.myTest.showTestEvevt(); //载入试题事件
        this.changeWhere();//改变内容获取条件
        this.move();//上下移动
        this.editTitle();//编辑标题弹出框
        this.editTitleSure();//编辑标题确定
        this.menuClick();//栏目相关点击事件
        this.contentClick();//内容相关操作的点击事件
        this.saveTemp();//保存模板弹框
        this.saveTempSure();//保存模板确定
        this.changeSaveTempTyle();//切换保存模板类型
        this.setEmptyTest();//试卷设置为空时，给出提示
        this.addTestChangeChapter(); //添加试题时，点击章节切换
    },
    layoutThree:function(){
        this.sendCaseWork();//发布导学案弹框
        this.sendCaseWorkSure();//发布导学案确定
        this.caseWordDown();//导学案word下载
    },
    //展示第一步页面
    showLayoutOne:function(){
        var self=this;
        var cookieIfSystem=self.tempContent['tempType'];
        var caseTplID=self.tempContent['tplID'];
        var cookieLastChapterID=this.chapterID;

        if(cookieIfSystem=='4'){
            $("input[name='choosetype']").get(1).checked=true; //个人模版
        }else if(cookieIfSystem=='3'){
            $("input[name='choosetype']").get(0).checked=true; //系统模版
        }else{
            $("input[name='choosetype']").get(2).checked=true; //自定义模版
        }

        if(this.subjectID){
            var chapterID=-1;
            if(cookieLastChapterID!=''){
                chapterID=cookieLastChapterID;
                if(caseTplID!='0'){
                    self.getTempArr(cookieIfSystem,cookieLastChapterID,caseTplID,0,1);
                }
            }

            $.post(U('Guide/Case/getChapterList'),{'subjectID':this.subjectID,'chapterID':chapterID,'times':Math.random()},function(data){
                //权限验证
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                var msg=data['data'];
                var selectID=0;
                if(msg[0]!=0){
                    selectID=msg[0][0];
                }
                $.caseCommon.setChapterTop(msg[1],selectID);

                if(typeof(msg[2])!='undefined'){
                    var len=msg.length;
                    var output='';
                    for(var i=2;i<len;i++){
                        output='';
                        output+=$.myCommon.setChapterOption(msg[i],msg[0][i-1],'chapter');
                        if(i==2){
                            $('.chapterbehind').html('<select class="selectChapter" name="chapterID[]">'+output+'</select>');
                        }else{
                            $('.chapterbehind').append('<select class="selectChapter" name="chapterID[]">'+output+'</select>');
                        }
                    }
                    $.caseCommon.getChapterName();
                    $('.tempPath').html($.caseCommon.showPath);
                }
            });
        }
    },
    //展示第二步页面
    showLayoutTwo:function(){
        this.showWhickStep(2);
        $("#dir2 .dir").html('<p class="list_ts"><span class="ico_dd">数据加载中请稍候...</span></div>');//等待提示
        var arr=this.splitCookie();//拆分cookie
        var tpl=this.combinationDirTwoHtml($.caseCommon.showPath,arr);//组合页面HTML
        $('#dir2 .dir').html(tpl);
        $('.errormsg').remove();
        this.getChapterPath();//获取章节路径
        this.getLore();//加载内容
    },
    //绑定调转事件
    bindSkipEvent:function(){
        this.skipTwo();  // 第一界面 下一步
        this.skipOne();  // 第二界面 跳转第一步
        this.skipThree();// 第二界面 跳转第三步
        this.skipTwoFromThree();//第三界面 从第三步跳转到第二步
    },
    /**
     * @param int step 跳转到第几步
     */
    showWhickStep:function(step){
        $('.step').hide();
        step = '#dir'+step;
        $(step).show();
        this.InitDivXdBox();
    },
    /***************************************
     * 通用
     **************************************/
    //初始化宽高
    InitDivXdBox:function(){
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
    //获取最终章节ID
    getChapterLast:function(){
        return $('.selectChapter').last().val()
    },
    /**************************************
    *第一界面
    *************************************/
    //设置版本内容
    setChapterTop:function(chapterTop,selectID){
        var chapterHtml='';
        chapterHtml+='<select name="chapterFirst" id="chapterFirst" class=""><option value="">-请选择教材-</option>';
        var selected='';
        for(var i in chapterTop){
            selected='';
            if(selectID==chapterTop[i]["ChapterID"]) selected=' selected="selected" ';
            chapterHtml+='<option value="'+chapterTop[i]["ChapterID"]+'"'+selected+'>'+chapterTop[i]["ChapterName"]+'</option>';
        }
        chapterHtml+='</select>';
        $('.intoChapterFirst').html(chapterHtml);
    },
    //设置模板选择内容
    setTestStyle:function(testStyle){
        var styleHtml='';
        for(var i in testStyle){
            styleHtml+='<label><input type="radio" name="choosetype" value='+testStyle[i]["val"]+' class="choosetype" />'+testStyle[i]["styleName"]+'&nbsp;</label>';
        }
        $('.intoTestStyle').html(styleHtml);
    },
    /**
     * 章节change事件
     * @param string url 调取数据路径
     */
    firstChapterSelectChange:function(url){
        var self=this;
        $('#chapterFirst').live('change',function(){
            var _this=$(this);
            if(_this.find('option:selected').attr('last')==1){
                return ;
            }
            $('.selectChapter').eq(0).nextAll("select").remove();
            var values=_this.find('option:selected').val();
            if(values=='') return;
            var param={"style":"chapter","pID":values};
            $.post(U('Index/getData'),param,function(msg){
                //权限验证
                if($.myCommon.backLogin(msg)==false){
                    return false;
                }
                var data=msg['data'];
                if(data){
                    var output='<option value="">—请选择—</option>';
                    output+=$.myCommon.setChapterOption(data,0,'chapter');
                    $('.selectChapter').eq(0).html(output);
                    $('.mbsel').css({'display':'none'});

                }
            });
        });
    },

    /**
     * 章节change事件
     * @param string url 调取数据路径
     */
    chapterSelectChange:function(url){
        var self=this;
        $('.selectChapter').live('change',function(){
            var _this=$(this);
            if(_this.find('option:selected').attr('last')==1){
                return ;
            }
            _this.nextAll("select").remove();
            var values=_this.find('option:selected').val();
            if(values=='') return;
            var param={"style":"chapter","pID":values};
            $.post(U('Index/getData'),param,function(msg){
                //权限验证
                if($.myCommon.backLogin(msg)==false){
                    return false;
                }
                var data=msg['data'];
                if(data){
                    var output='<option value="">—请选择—</option>';
                    output+=$.myCommon.setChapterOption(data,0,'chapter');
                    _this.after('<select class="selectChapter" name="chapterID[]" >'+output+'</select>');
                }else{
                    var choosetypeid=$('input[name="choosetype"]:checked').val();
                    self.getTempArr(choosetypeid,values.replace('c',''),'',0,1);
                }
            });
        });
    },
    
    /*
     *ajax根据条件获取模板
     * 参数1 chooseTypeID 组卷方式ID
     * @param int chooseType 系统，个人
     * @param int chapterID 对应章节ID
     * @param int where 0 在第一步显示，1在模板替换中显示
     * @param int showDel 是否显示删除按钮
     * @author demo
     */
    getTempArr:function(chooseTypeID,chapterID,id,where,showDel){
        if(chooseTypeID=='2') return false;//判断如果是自定义模板，不查询模板列表 自定义模板ID为2
        if(!where){
            $('.selmb').html('');
            $('.selmb').html('<span class="boxloading">模板加载中请稍候...</span>');
        }else{
            $('.selmb1').html('');
            $('.selmb1').html('<span class="boxloading">模板加载中请稍候...</span>');
        }
        if(chapterID=='' && !where){
            $('.selmb').html('<div style="color:#F53131;padding:5px;">您还没有选择至最终章节！</div>');
            return false;
        }
        var backcolor="";
        var temp='';
        $.ajax({
            type: "POST",
            cache: false,
            url: U('Guide/Case/getTemplateList'),
            data: {'choosetypeid':chooseTypeID,'subjectID':this.subjectID,'chapterID':chapterID,'times':Math.random()},
            success: function(data){
                if($.myCommon.backLogin(data)==false){
                    $('.selmb').html(data.data);
                    return false;
                };
                if(data['data']['content'] && data['data']['content']!=''){
                    for(var i in data['data']['content']){
                        if(data['data']['content'][i]['TplID']==id){
                            backcolor='style="color:#00a0e9;border-color:#00a0e9;"';
                        }else{
                            backcolor='';
                        }
                        if(where==0){
                            $('.mbsel').css({'display':'block'});
                            var delButton='';
                            if(showDel==1){
                                if(chooseTypeID!=3){
                                    var delButton='<span class="templatelist-cite"><cite>'+data['data']['content'][i]['UpdateTime']+'</cite><b class="delThisTpl" thisName="'+data['data']['content'][i]['TempName']+'" tplID='+data['data']['content'][i]['TplID']+'>X</b></span>';//设置删除按钮
                                }
                            }
                            temp+='<div class="templatelist" mbid='+data['data']['content'][i]['TplID']+' '+backcolor+' title="'+data['data']['content'][i]['TempName']+'" >'+'<span class="context elli">'+data['data']['content'][i]['TempName']+'</span>'+delButton+'</div>';
                        }else{
                            temp+='<div class="templatelist1 elli" mbid='+data['data']['content'][i]['TplID']+' '+backcolor+' title="'+data['data']['content'][i]['TempName']+'">'+  data['data']['content'][i]['TempName']+'</div>';
                        }
                    }
                    if(where==0){
                        $('.selmb').html();
                        $('.selmb').html(temp);
                    }else{
                        $('.selmb1').html();
                        $('.selmb1').html(temp);
                    }
                }else{
                    if(where==0){
                        $('.mbsel').css({'display':'block'});
                        $('.selmb').html('<div style="color:#F53131;padding:5px;">没有可选模版</div>');
                    }else{
                        $('.selmb1').html('<div style="color:#F53131;padding:5px;">没有可选模版</div>');
                    }
                }
            }
        })
    },
    delThisTpl:function(){
        $('.delThisTpl').live('click',function(){
            var tempName=$(this).attr('thisName');
            var tplID=$(this).attr('tplID');
            $.myDialog.normalMsgBox('delCaseTpl','温馨提醒',450,'<div><b class="delTpl"  tplID="'+tplID+'">您确定删除【'+tempName+'】模板？</b></div>',3);
        })
        $('#delCaseTpl .normal_yes').live('click',function(){
            var tplID=$('#delCaseTpl .delTpl').attr('tplID');
            $.post(U('Guide/Case/delCaseTplByID'),{'tplID':tplID},function(msg){
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
    //选择模板，调取对应模板数据
    changeTemplate:function(){
        var self=this;
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
            self.tplID=mbid;
            var choosetypeid=$('input[name="choosetype"]:checked').val();
            $.post(U('Guide/Case/getTemplateByID'),{'mbid':mbid,'choosetypeid':choosetypeid,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                if(data['status']=='1'){
                    self.setCookie(data['data']);
                    $('.selmb').next().remove();
                }
            });
        });
    },
    //跳转到第二步
    skipTwo:function(){
        var self=this;
        $('.tostep2').live('click',function(){
            //验证章节数据是否存在
            var lastChapterID=self.getChapterLast();
            if(lastChapterID.indexOf('c')!=0){ //判断选择至最终章节
                self.showError('chapterbehind','章节请选择至最终章节！');
                return false;
            }else{
                $('chapterbehind').children().find('span').eq(0).remove();
            }

            //验证模板数据是否加载完成
            if($('.selmb').next().attr('class')=="mbloading" && $('input[name="choosetype"]:checked').val()!='2'){
                $.myDialog.showMsg('模板正在载入请稍候...',1);
                return false;
            }

            //验证模板是否需要选择
            var templatelist='';
            var choosetype=$("input[name='choosetype']:checked").val();
            if(choosetype!='2'){
                if($('.templatelist').length==0){
                    self.showError('tempbehind','没有模板可供选择！');
                    return false;
                }

                // 根据组卷方式，显示模板框，验证模板是否选择
                $('.templatelist').each(function(){
                    if($(this).css('color')=='rgb(0, 160, 233)' || $(this).css('color')=='#00a0e9'){
                        templatelist=$(this).attr('mbid');
                    }
                });

                if(templatelist == ''){
                    if($(".tempbehind").find(".errormsg").length==0){
                        self.showError('tempbehind','模板没有选择！');
                    }
                    $(".tempbehind").find(".errormsg").animate({"opacity":.2},20,function(){
                        $(".tempbehind").find(".errormsg").animate({"opacity":1},20)
                    })
                    return false;
                }else{
                    self.showError('tempbehind');
                }
            }

            //写入全局章节数据
            self.tempType=choosetype;
            self.getChapterID();//获取章节ID
            self.getChapterName();//获取章节名称
            self.showLayoutTwo();//展示第二步页面

        });
    },
    /**
     * 导学案通用验证函数
     * @param string className 样式类名
     * @param string showMsg 提示信息
     */
    showError:function(className,showMsg){
        if(showMsg!=''){
            if($('.'+className+'').find('span').attr('class') !='errormsg'){
                $('.'+className+'').append('<span class="errormsg">'+showMsg+'</span>');
            }
        }else{
            $('.'+className+'').children('div').next().remove();
        }
    },
    /**************************
     *第二界面
     *************************************/
    //组合第二步页面HTML
    combinationDirTwoHtml:function(tmpPath,data){
        var dirTwoStr='';
        //组合标题
        dirTwoStr+=this.combinationTitleHtml(tmpPath,data['tempName'].replace('$!$','·'),data['tempDesc']);
        //组合板块
        if(data['tempHead']) {
            dirTwoStr += this.combinationForumHtml(data['tempHead']);
        }
        return dirTwoStr;
    },
    //判断第二步是否有内容
    ifHaveContent:function(){
        if($('#dir2 .lore').length>0){
            return false;
        }
        return true;
    },
    /**
     * 组合标题HTML
     * 参数1 tempName 标题名称
     * 参数2 tempDesc 描述
     * 返回 {string}
     */
    combinationTitleHtml:function(tempPath,tempName,tempDesc){
        var str='';
       if(tempPath=='undefined'){
            tempPath='正在加载中';
        }
        str+='<div class="tempPath">'+tempPath+'</div>';
        str+='<div class="tempTop"> ' +
                    '<div class="tempHeadName editTitle pointer">' +  
                        '<div class="tempName">'+tempName+'</div>' +
                    '</div> ' +
                    '<div class="tempDesc editTitle pointer">'+tempDesc+'</div> ' +
                '</div>';
        return str;
    },
    /**
     * 组合板块HTML
     * 参数 data 板块数据
     * 返回 {string}
     */
    combinationForumHtml:function(data){
        //循环data
        var forumStr='';
        var n=0;
        for(var i= 0,len=data.length;i<len;i++){
            n=i+1;
            forumStr+='<div id="tempHead'+n+'" class="tempModel">';
            forumStr+=    '<!-- 标题 -->';
            forumStr+=    '<div class="tempHeadBox">';

            forumStr+='<div class="menu tempSetBtn">'+
                          '<span class="btnBgBox">'+
                              '<a href="javaScript:;" class="addMenu" fid="'+n+'">添加</a>'+
                              '<a href="javaScript:;" class="editTitle">设置</a>'+
                          '</span>'+
                      '</div>';

            forumStr+='<div class="tempHead pointer">'+
                          '<div class="editTitle">'+
                              '<span id="headName'+n+'" class="headName">'+
                                  data[i]['head']+
                              '</span>'+
                              '(<span id="tempForum'+n+'" class="tempForum">'+
                                  data[i]['forum']+
                              '</span>)'+
                          '</div>'+
                        '</div></div>';
            forumStr +=this.combinationMenuHtml(n,data[i]['menu']);//组合栏目HTML
            forumStr+='</div>';
            forumStr+='</div>';
        }
        return forumStr;
    },
    /**
     * 组合栏目HTML
     * 参数1 fid 板块ID
     * 参数2 data 栏目数据
     * 返回 {string}
     */
    combinationMenuHtml:function(fid,data){
        var menuStr='';
        var n=0;
        var self=this;
        if(data.length) {
            for (var i = 0, len = data.length; i < len; i++) {
                var numStyle=0;
                var ifHave = 0;
                n = i + 1;
                if(menuSubject[this.subjectID]['o'+data[i]['menuID']]){
                    numStyle=menuSubject[this.subjectID]['o'+data[i]['menuID']]['NumStyle'];
                }
                if(data[i]['menuContent']!=''){
                    ifHave = 1;
                }
                menuStr += self.joinMenuHtml(fid, n, data[i]['ifTest'], data[i]['ifAnswer'], data[i]['menuName'], data[i]['menuID'],numStyle,ifHave);
            }
        }
        return menuStr;
    },
    //默认的cookie,用户在选中自定义导学案的时候使用
    defaultCookie:function(){
        var subjectMenu='';
        var i,j;
        var menuArray={};
        var menuSubjectMsg=menuSubject[this.subjectID];
        for(i in menuSubjectMsg){
            if(typeof(menuArray[menuSubjectMsg[i]['ForumID']])=='undefined'){
                menuArray[menuSubjectMsg[i]['ForumID']]=[];
            }
            var leng=menuArray[menuSubjectMsg[i]['ForumID']].length;
            if(typeof(menuArray[menuSubjectMsg[i]['ForumID']].length)=='undefined'){
                leng=0;
            }
            menuArray[menuSubjectMsg[i]['ForumID']][leng]=menuSubjectMsg[i];
        }
        for( i in menuArray){
            subjectMenu+='tempForum'+i+'@$@'+forumMsg[i]['otherName']+'@$@'+forumMsg[i]['name']+'@#@';
            for(j in menuArray[i]){
                subjectMenu+='tempMenu'+i+'_'+(parseInt(j)+1)+'@$@'+menuArray[i][j]['MenuName']+'@$@@$@'+menuArray[i][j]['IfTest']+'|'+menuArray[i][j]['IfAnswer']+'|'+menuArray[i][j]['MenuID']+'@#@';
            }
        }
        var dCookie='subjectID@$@'+this.subjectID+'@#@'+
        'chapterID@$@'+this.chapterID+'@#@' +
        'tempType@$@'+this.tempType+'@#@' +
        'tplID@$@'+this.tplID+'@#@' +
        'tempName@$@'+this.chapterName+'@#@' +
        'showPath@$@'+this.showPath.replace('$!$','·')+'@#@' +
        'tempDesc@$@班级:__________姓名:__________设计人:__________日期:__________@#@' +subjectMenu;
        return dCookie;
    },
    //跳转第一步
    skipOne:function(){
        var self=this;
        //跳转第一步
        $('.tostep1').live('click',function(){
            $.caseCommon.showWhickStep(1);
        });
    },
    //跳转第三步
    skipThree:function(){
        var self=this;
        //跳转第三步
        $('.tostep3').live('click',function(){
            if(self.ifHaveContent()){//如果没有内容提示
                $.myDialog.normalMsgBox('emptyTest','温馨提示',550,'<div style="color:red;"><b>您还没有设置导学案相关内容！去 <span class="emptySetTest"> <b style="color:green;cursor:pointer"> 设置 </b> </span>试题</b></div>',5);
                return false;
            }
            self.cookieRemoveDuplicate();//排除重复
            self.showWhickStep(3);
            self.buildNewContent(self.tempContent);
        });
    },
    //第二步，数据为空时点击设置，弹出设置提醒框
    setEmptyTest:function(){
        $('.emptySetTest').live('click',function(){
            $('#dir2').find('.addLore').eq(0).click();
            $('#emptyTest .tcClose').click();
        })
    },
    /**
     * 下移
     * 参数1 移动对象的ID前缀
     * 参数2 移动对象的class名称
     * 参数3 下移按钮的class名称
     * 参数4 上移按钮的class名称
     * 参数5 点击对象
     */
    removeDown:function (idName,className,downClassName,upClassName,obj){
        var self=this;
        var thisOrder=obj.parent().attr('order');//位置标识
        var orderNum=thisOrder.split('_');//将位置标识拆分成数组
        var testObject='#'+idName+thisOrder;//移动对象
        var nextTestNum='';
        var nextObject='';
        if(orderNum[2]){//知识或者试题的移动
            //orderNum[0]是板块ID
            //orderNum[1]是栏目在板块的位置
            //orderNum[2]是知识或试题在栏目的位置
            nextTestNum=parseInt(orderNum[2])+1;
            nextObject=$('#'+idName+orderNum[0]+'_'+orderNum[1]+'_'+nextTestNum);
        }else{
            nextTestNum=parseInt(orderNum[1])+1;
            nextObject=$('#'+idName+orderNum[0]+'_'+nextTestNum);
        }
        if(!nextObject){//如果没有下一个对象则不移动
            return false;
        }else {
            if (obj.prev().css('opacity') == '0.5') {//上移按钮样式变化
                obj.prev().css({opacity: '1'});
                nextObject.find('.'+upClassName).css({opacity: '0.5'});
            }
            if (nextObject.find('.'+downClassName).css('opacity') == '0.5') {//下移按钮样式变化
                obj.css({opacity: '0.5'});
                nextObject.find('.'+downClassName).css({opacity: '1'});
            }
            $(testObject).before(nextObject);//移动
        }
        //重置题型编号
        self.resetTypes();
        //重置位置编号
        self.resetOrder(orderNum[0],className);
        //重置试题编号
        self.resetTests();
        //重置栏目cookie
        self.resetMenuCookie('tempHead'+orderNum[0],'tempMenuBox',orderNum[0]);
        //组合cookie
        self.joinCookie()
    },
    //上移
    upMove:function(idName,className,downClassName,upClassName,obj){
        var self=this;
        var thisOrder=obj.parent().attr('order');//位置标识
        var orderNum=thisOrder.split('_');//将位置标识拆分成数组
        var testObject='#'+idName+thisOrder;//移动对象
        var nextTestNum='';
        var nextObject='';
        if(orderNum[2]){//知识或者试题的移动
            //orderNum[0]是板块ID
            //orderNum[1]是栏目在板块的位置
            //orderNum[2]是知识或试题在栏目的位置
            nextTestNum=parseInt(orderNum[2])-1;
            nextObject=$('#'+idName+orderNum[0]+'_'+orderNum[1]+'_'+nextTestNum);
        }else{
            nextTestNum=parseInt(orderNum[1])-1;
            nextObject=$('#'+idName+orderNum[0]+'_'+nextTestNum);
        }
        if(nextTestNum<0 ||!nextObject){//如果没有下一个对象则不移动
            return false;
        }else {
            if (obj.prev().css('opacity') == '0.5') {//上移按钮样式变化
                obj.prev().css({opacity: '1'});
                nextObject.find('.'+upClassName).css({opacity: '0.5'});
            }
            if (nextObject.find('.'+downClassName).css('opacity') == '0.5') {//下移按钮样式变化
                obj.css({opacity: '0.5'});
                nextObject.find('.'+downClassName).css({opacity: '1'});
            }
            $(nextObject).before($(testObject));//移动
        }
        //重置题型编号
        self.resetTypes();
        //重置位置编号
        self.resetOrder(orderNum[0],className);
        //重置试题编号
        self.resetTests();
        //重置栏目cookie
        self.resetMenuCookie('tempHead'+orderNum[0],'tempMenuBox',orderNum[0]);
        //组合cookie
        self.joinCookie()
    },
    //试卷题序重置
    resetTests:function(){
        var self=this;
        $('.tempModel').each(function(paper){
            var menu=[];//以栏目ID为键，已栏目内试题数量为值的数组
            $('.tempModel').eq(paper).find('.tempMenuBox').each(function(type){
                var menuObj=$(this);//当前栏目对象
                var numStyle=menuObj.attr('numSty');//编号类型
                var menuID=menuObj.attr('mid');//栏目ID
                if(!menu[menuID]){//如果该栏目内的试题数量不存在则赋值为1
                    menu[menuID]=1;
                }
                var testNum=menu[menuID];//题号
                menuObj.find('.lore').each(function(test){
                    var fid=paper+1;//版块ID
                    $(this).attr('id','lore'+fid+'_'+type+'_'+test);
                    $(this).find('.btnBgBox').attr('order',fid+'_'+type+'_'+test);
                    var nums=$(this).attr('num');//小题数量
                    var order='';//页面显示的题序
                    if(nums==1){//没有小题的情况
                        order=parseInt(testNum);
                        if (numStyle == '1') {//文字序号
                            order = shuzi[parseInt(test)];
                        }
                    }else{
                        //获取序号字符串
                        order=self.returnOrderString(nums,testNum,numStyle,test);
                    }
                    testNum=(parseInt(testNum)+parseInt(nums));
                    $(this).find('.order').html(order);
                });
                menu[menuID]=testNum;
            })
        });
    },
    /**
     * 返回序号字符串
     * 参数1 num 小题数量
     * 参数2 tmpNum 上一道题序号
     * 参数3 numStyle 序号类型
     * @returns {string}
     */
    returnOrderString:function(num,tmpNum,numStyle){
        var order = '';
        if(numStyle=='1'){//汉字序号
            if(num==1) {
                order = shuzi[(parseInt(tmpNum)-1)];
            }else{
                var start=parseInt(tmpNum)-1;
                order = shuzi[start]+'~'+shuzi[(start+parseInt(num)-1)];
            }
        }else {//数字序号
            if(num==1) {
                order = parseInt(tmpNum);
            }else{
                order=(parseInt(tmpNum))+'~'+(parseInt(tmpNum-1)+parseInt(num));
            }
        }
        return order;
    },
    //重置位置标识
    resetOrder:function (fid,childClass){
        $('#tempHead'+fid+' .'+childClass).each(function(i){
            var m=parseInt(i)+1;
            var order=fid+'_'+m;
            $(this).find('.btnBgBox').attr('order',order);
            $(this).find('.menuName').attr('order',order);
            $(this).attr('id','tempMenu'+order);
        })
    },
    //重置题型
    resetTypes:function (){
        var count = 0;
        $('.questypehead').each(function(i){
            $(this).find('.tips').html(listNum[i]+'、');
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
    setCookie:function(data){
        this.tempContent=data;
        this.joinCookie();
    },
    /**
     * 拆分cookie
     * [tempName:'第一课时 集合的含义',tempDesc:'班级_____',tempHead1:{head:'课前预习',forum:'预习案'}]
     */
    splitCookie:function(){
        this.lock = '';
        //this.cookie = this.joinCookie();
        //1首先判断tempContent是否存在，如果不存在，使用cookie
        if(!this.tempContent['chapterID']) {
            if(this.chapterID) {//如果章节不存在，说明是直接显示第二步
                var c = 'chapterID@$@' + this.chapterID + '@#@';
                //如果cookie不存在或者改变学科或者改变章节后，使用默认cookie
                if (!this.cookie  || this.cookie.indexOf(c) == -1) {
                    this.cookie = this.defaultCookie();
                }
            }
        }else {
            //如果存储cookie内容变量里的章节ID和用户修改后的章节ID不一样，则重置第二页面
            if (this.tempContent['chapterID'] != this.chapterID || this.tempContent['subjectID']!=this.subjectID) {
                this.cookie = this.defaultCookie();
            }
        }
        if(this.tplID!=0 && this.tempType==2){
            this.tplID=0;
            this.cookie=this.defaultCookie();
        }
        if(this.cookie){//cookie变量有值时说明第二页面需要重新载入
            var testArr = this.cookie.split('@#@');
            var len = testArr.length;

            var content = [];
            var menu1 = [];//第一个板块的栏目信息
            var menu2 = [];//第二个板块栏目信息
            var menu3 = [];//第三个板块的栏目信息
            var forum = {};
            for (var i = 0; i < len; i++) {
                if(testArr[i]==''){
                    continue;
                }
                content[i] = testArr[i].split('@$@');
                if (content[i].length == 2) {
                    this.tempContent[content[i][0]] = content[i][1];//标题信息
                } else if (content[i].length == 3) {
                    forum[content[i][0]] = [content[i][1], content[i][2]];
                } else if (content[i].length == 4) {//栏目及内容信息
                    var t = content[i][0].replace('tempMenu', '').split('_');
                    var m = parseInt(t[0]);
                    var n = parseInt(t[1]) - 1;
                    var menuType = content[i][3].split('|');
                    var menuStr = {
                        'menuName': content[i][1],
                        'ifTest': menuType[0],
                        'ifAnswer': menuType[1],
                        'menuID': menuType[2],
                        'menuContent': content[i][2],
                        'menuOrder': content[i][0]
                    };

                    if (m == 1) {
                        menu1[n] = menuStr;
                    } else if (m == 2) {
                        menu2[n] = menuStr;
                    } else {
                        menu3[n] = menuStr;
                    }
                }

            }
            if(typeof(forum['tempForum1'])=='undefined'){
                forum['tempForum1']=[];
            }
            if(typeof(forum['tempForum2'])=='undefined'){
                forum['tempForum2']=[];
            }
            if(typeof(forum['tempForum3'])=='undefined'){
                forum['tempForum3']=[];
            }
            forum['tempForum1'].push(menu1);
            forum['tempForum2'].push(menu2);
            forum['tempForum3'].push(menu3);
            this.tempContent["forum"] = forum;
        }
        this.chapterID=this.tempContent['chapterID'];
        if(this.tempContent['tplID']!=this.tplID && this.tplID!=0){
            this.tempContent['tplID']=this.tplID;
        }else{
            this.tplID=this.tempContent['tplID'];
        }
        this.tempType=this.tempContent['tempType'];
        this.canChangePath=this.tempContent['tempName'];
        this.joinCookie();
        //预载入框架
        var str=this.tempTpl();
        return str;
        //载入内容
    },
    //将拆分的cookie数据组合成可以用temp插件使用的格式
    tempTpl:function(){
        var tempTitle=this.tempContent;
        var test={
            tempName: tempTitle['tempName'],
            tempDesc: tempTitle['tempDesc'],
            showPath: tempTitle['showPath'],
            tempHead: [
                {
                    head: tempTitle['forum']['tempForum1'][0],
                    forum: tempTitle['forum']['tempForum1'][1],
                    menu: tempTitle['forum']['tempForum1'][2]
                },
                {
                    head: tempTitle['forum']['tempForum2'][0],
                    forum: tempTitle['forum']['tempForum2'][1],
                    menu: tempTitle['forum']['tempForum2'][2]
                },
                {
                    head: tempTitle['forum']['tempForum3'][0],
                    forum: tempTitle['forum']['tempForum3'][1],
                    menu: tempTitle['forum']['tempForum3'][2]
                }
            ]
        };
        return test;
    },
    //cookie排重
    cookieRemoveDuplicate:function(){
        var data=this.tempContent['forum'];//获取内容
        var conStr='';//ID字符串
        var num=0;//循环标示
        for(var i in data){//循环板块
            var content=data[i][2];//栏目内容
            var len=content.length;
            var conArr=[];//栏目内容拆分后的数组
            for(var j=0;j<len;j++){//循环栏目
                if(content[j]['menuContent']!='') {
                    //将内容拆分成数组
                    conArr = content[j]['menuContent'].split(';');
                    //去除重复数据后合并数据
                    for (var k = 0; k < conArr.length; k++) {
                        //第一个板块的第一个ID肯定不重复
                        if (num == 0 && j == 0 && k == 0) {
                            conStr=conArr[k]+';';
                            continue;
                        } else if (conStr.indexOf(conArr[k]) != -1) {
                            conArr.splice(k, 1);
                        }
                        if(typeof (conArr[k])!='undefined') {
                            conStr += conArr[k] + ';';
                        }
                    }
                    data[i][2][j]['menuContent'] = conArr.join(';');
                }
            }
            num++;
        }
        //将cookie内容数据换成去除重复的数据
        this.tempContent['forum']=data;
        //组合新cookie
        this.joinCookie();
    },
    //组合cookie
    joinCookie:function(){
        var data=this.tempContent;
        var titles=[];
        //标题等试卷信息字段
        titles.push('subjectID@$@'+data['subjectID']);//章节信息
        titles.push('chapterID@$@'+data['chapterID']);//章节信息
        titles.push('tempType@$@'+data['tempType']);//模板类型
        titles.push('tplID@$@'+data['tplID']);//模板ID
        titles.push('tempName@$@'+data['tempName']);//导学案标题
        titles.push('tempDesc@$@'+data['tempDesc']);//导学案描述
        for(var i in data['forum']){
            titles.push(i+'@$@'+data['forum'][i][0]+'@$@'+data['forum'][i][1]);//栏目标题
            for(var j in data['forum'][i][2]){//栏目内容
                var menu=data['forum'][i][2][j];
                titles.push(menu['menuOrder']+'@$@'+menu['menuName']+'@$@'+menu['menuContent']+'@$@'+menu['ifTest']+'|'+menu['ifAnswer']+'|'+menu['menuID']);
            }

        }
        this.cookie=titles.join('@#@');
        localData.set('caseStyle',this.cookie);
    },
    //通过cookie获取试题或知识点
    getLore:function(){
        var content=this.tempContent;
        var self=this;
        if(lock!=''){
            return false;
        }
        lock='Case/buildTestContent';
        if(content){
            $.post(U('Guide/Case/buildTestContent'),{'content':content},function(msg){
                //权限验证
                if($.myCommon.backLogin(msg)==false){
                    return false;
                }
                var data=msg['data'];
                if(data['status']=='success'){
                    //改变数据结构并将内容写入界面
                    self.changeStyle(data['forum']);
                }
                lock='';
            })
        }
    },
    //改变返回数据格式
    changeStyle:function(data){
        var self=this;
        for(var i in data){
            var iData=data[i][2];
            var content=[];
            for(var j in iData){
                var test=[];
                var testC=iData[j]['testContent'];
                var n=0;
                for(var k in testC){
                    if(k=='' || !testC[k]['system']){
                        continue;
                    }
                    var identifying='';
                    if(iData[j]['ifTest']=='0'){
                        if(testC[k]['system']=='1'){
                            identifying='u';
                        }else {
                            identifying = 'l';
                        }
                    }else{
                        if(testC[k]['system']=='1'){
                            identifying='c';
                        }
                    }
                    test[n] = {
                        'childNum': testC[k]['testNum'],
                        'testID': identifying+k.replace('remark',''),
                        'test': testC[k]['Lore'],
                        'answer': testC[k]['Answer'],
                        'system': testC[k]['system']
                    };
                    n = parseInt(n) + 1;
                }
                content['ifTest']=iData[j]['ifTest'];
                content['ifAnswer']=iData[j]['ifAnswer'];
                content['content']=test;
                //组合栏目下内容HTML字符串
                var contentStr=self.joinContentHtml(content);
                //写入对应结构
                $('#'+iData[j]['menuOrder']).find('.waiting').remove();//去掉等待提示
                $('#'+iData[j]['menuOrder']).find('.menuContent').append(contentStr);
                //重置编号
                self.resetTests();
            }
        }

    },
    //修改标题
    editTitle:function(){
        var self=this;
        $('#dir2').on('click','.editTitle',function(){
            $.myDialog.normalMsgBox('editTitleDiv','设置标题',700,'正在加载请稍候...');
            var chapterArr=new Array();
            //cookie已存在的情况下
            var tempName=$('.tempName').html();
            var tempDesc=$('.tempDesc').html();
            var titleName=[];
            $('.tempHead').each(function(i){
                var headName=$(this).find('.headName').html();
                var forumName=$(this).find('.tempForum').html();
                titleName[i]={'headName':headName,'forumName':forumName};
            });
            var tmpStr= '<table class="alertTempTitle tempSetItem itemCurt">' +
                '<tr>' +
                '<td class="tit" width="90"><h5>导学案名称</h5></td>'+
                '<td class="con">' +
                '<div class="tempSetItemCon">' +
                '<input style="width:380px" type="text" maxlength="200" name="alertTempName" value="'+tempName+'" class="alertTempName">' +
                '</div>'+
                '</td>' +
                '</tr>' +
                '</table>';
            tmpStr  +=  '<table class="alertTempTitle tempSetItem">' +
            '<tr>' +
            '<td class="tit" width="90"><h5>导学案描述</h5></td>'+
            '<td class="con">' +
            '<div class="tempSetItemCon">' +
            '<textarea name="alertTempDesc"class="alertTempDesc">'+
            tempDesc+
            '</textarea>' +
            '</div>' +
            '</td>' +
            '</tr>' +
            '</table>';
            for(var i in titleName){
                var n=parseInt(i)+1;
                tmpStr +='<table class="alertHead tempSetItem">'+
                '<tr>'+
                '<td  class="tit" width="90">' +
                '<h5>'+self.systemForum[i]+'</h5>' +
                '</td>'+
                '<td class="con">'+
                '<div class="headRight tempSetItemCon">' +
                '<p>' +
                '<span>主标题</span>'+
                '<input id="alertHead'+n+'" name="alertHead'+n+'" type="text" value="'+titleName[i]['headName']+'">'+
                '</p>' +
                '<p>' +
                '<span>副标题</span>'+
                '<input id="alertForum'+n+'" name="alertForum'+n+'" type="text" value="'+titleName[i]['forumName']+'">'+
                '</p>' +
                '</div>'+
                '</td>'+
                '</tr>' +
                '</table>';
            }
            var titleStr='<div class="tempSet">'+tmpStr+'</div>';
            $.myDialog.normalMsgBox('editTitleDiv','设置标题',600,titleStr,3);
            return false;
        });
    },
    //修改标题确定
    editTitleSure:function(){
        var self=this;
        $('#editTitleDiv .normal_yes').live('click',function(){
            var chapterArr=new Array();
            if(self.canChangePath!=''){
                var chapterArr=self.canChangePath.split('·');
                var tempName=chapterArr[chapterArr.length-1];
                self.tempContent['tempName']=$('.alertTempName').val().replace('·','$!$');
                self.canChangePath=self.canChangePath.replace(tempName,$('.alertTempName').val().replace('·','$!$'));
            }else{
                var chapterArr=self.tempContent['tempName'].split('·');
                var tempName=chapterArr[chapterArr.length-1];
                self.tempContent['tempName']=$('.alertTempName').val().replace('·','$!$');
                self.canChangePath=self.tempContent['tempName'].replace(tempName,$('.alertTempName').val().replace('·','$!$'));
            }
            self.tempContent['tempDesc']=$('.alertTempDesc').val();
            self.tempContent['forum']['tempForum1'][0]=$('#alertHead1').val();
            self.tempContent['forum']['tempForum1'][1]=$('#alertForum1').val();
            self.tempContent['forum']['tempForum2'][0]=$('#alertHead2').val();
            self.tempContent['forum']['tempForum2'][1]=$('#alertForum2').val();
            self.tempContent['forum']['tempForum3'][0]=$('#alertHead3').val();
            self.tempContent['forum']['tempForum3'][1]=$('#alertForum3').val();
            //验证信息
            if(self.tempContent['tempName']==''){
                alert('导学案名称不能为空');
                return false;
            }
            if(self.tempContent['tempDesc']==''){
                alert('导学案描述不能为空');
                return false;
            }
            if(self.tempContent['forum']['tempForum1'][0]==''){
                alert('预习案主标题不能为空');
                return false;
            }
            if(self.tempContent['forum']['tempForum1'][1]==''){
                alert('预习案副标题不能为空');
                return false;
            }
            if(self.tempContent['forum']['tempForum2'][0]==''){
                alert('探究案主标题不能为空');
                return false;
            }
            if(self.tempContent['forum']['tempForum2'][1]==''){
                alert('探究案副标题不能为空');
                return false;
            }
            if(self.tempContent['forum']['tempForum3'][0]==''){
                alert('练习案主标题不能为空');
                return false;
            }
            if(self.tempContent['forum']['tempForum3'][1]==''){
                alert('练习案副标题不能为空');
                return false;
            }
            //关闭弹出框
            $('#editTitleDiv .tcClose').click();
            //改变页面标题信息
            self.changTitle();
            //组合cookie
            self.joinCookie();
        });
    },
    //改变页面标题信息
    changTitle:function(){
        var self=this;
        $('.tempName').html(self.tempContent['tempName'].replace('$!$','·'));
        $('.tempDesc').html(self.tempContent['tempDesc']);
        $('#headName1').html(self.tempContent['forum']['tempForum1'][0]);
        $('#tempForum1').html(self.tempContent['forum']['tempForum1'][1]);
        $('#headName2').html(self.tempContent['forum']['tempForum2'][0]);
        $('#tempForum2').html(self.tempContent['forum']['tempForum2'][1]);
        $('#headName3').html(self.tempContent['forum']['tempForum3'][0]);
        $('#tempForum3').html(self.tempContent['forum']['tempForum3'][1]);
    },
    /**
     * 获取板块下的栏目
     * 参数1 fid 板块ID
     */
    getMenuByFID:function(fid){
        var self=this;
        if(lock!=''){//判断ajax请求是否锁定
            return false;
        }
        //锁定ajax请求
        lock='Case/ajaxGetMenu';
        $.post(U('Index/getData'),{'style':'caseMenu','forumID':fid,'subjectID':this.subjectID},function(e){
            if($.myCommon.backLogin(e)==false){
                return false;
            }
            //组合下拉列表框内容
            var optionStr='<option value="">请选择</option>';
            if(e['data']){
                for(var i in e.data){
                    optionStr +='<option value="'+ e['data'][i]['IfTest']+'_'+ e['data'][i]['IfAnswer']+'_'+e['data'][i]['MenuID']+'_'+e['data'][i]['NumStyle']+'">'+ e['data'][i]['MenuName']+'</option>';
                }

            }
            $('#menuList').html(optionStr);
            lock='';//解除ajax请求锁定
        });
    },
    /**
     * 获取板块已添加的栏目信息
     * 参数1 fid 板块ID
     */
    getBoxMenu:function(fid){
        var menuList=[];
        $('#tempHead'+fid).find('.tempMenu').each(function(j){
            var n=j+1;
            var menuName=$(this).find('.menuName').html();
            //去掉栏目名称的【】符号
            var name=menuName.replace(/[ \【,\】]/g,'');
            //组合栏目内容，包括栏目名称和栏目位置标识
            menuList[j]={'order':fid+'_'+n,'menuName':name};
        });
        //组合下拉列表框
        var optionStr='<option value="">请选择</option>';
        if(menuList.length>0){
            for(var i in menuList){
                optionStr +='<option value="'+menuList[i]['order']+'">'+menuList[i]['menuName']+'</option>';
            }
        }
        $('#boxAddMenuList').html(optionStr);
    },
    //和栏目相关的点击事件
    menuClick:function(){
        var self=this;
        //添加栏目
        $('#dir2').on('click','.addMenu',function(){
            var fid=$(this).attr('fid');
            var len=$('#tempHead'+fid).find('.tempMenuBox').length;
            if(len!=0){
                var ifShowMenu=1;
            }else{
                var ifShowMenu=0;
            }
            self.editMenu('add',fid,'',ifShowMenu);
            //获取栏目列表
            self.getMenuByFID(fid);
            //获取已添加栏目列表
            self.getBoxMenu(fid);
        });
        //自动生成栏目名称
        $(document).on('change','#menuList',function(){
            var newName=$.trim($.myCommon.removeHTML($(this).children('option:selected').text()));
            //如果未选择栏目，栏目名称为空，否则就为选中的栏目名称
            if($(this).children('option:selected').val()==""){
                $('input[name="menuName"]').val('');
            }else{
                $('input[name="menuName"]').val(newName);
            }
        });
        //编辑栏目名称
        $('#dir2').on('click','.editMenu',function(){
            var order=$(this).parent('.btnBgBox').attr('order');
            if(typeof(order)=='undefined'){
               var order=$(this).attr('order');
            }
            var menuName=$('#tempMenu'+order).find('.menuName').html();
            var fid=order.split('_')[0];
            self.editMenu('edit',order,menuName.replace(/[ \【,\】]/g,''));
            //获取已添加栏目列表
            self.getBoxMenu(fid);
        });
        //移除栏目
        $('#dir2').on('click','.removeMenu',function(){
            var order=$(this).parent('.btnBgBox').attr('order');
            var typeName=$(this).parent().parent().next().find('h5').html();
            $.myDialog.normalMsgBox('delMenumsg','信息提示',450,'<div><b class="delMenu"  order="'+order+'">您确定删除该'+typeName+'栏目？</b></div>',3);

        });
        //确定移除栏目
        $('#delMenumsg .normal_yes').live('click',function(){
            var order=$('#delMenumsg .delMenu').attr('order');
            var fid=order.split('_')[0];
            $('#tempMenu'+order).remove();
            //重置位置标识
            self.resetOrder(fid,'tempMenuBox');
            //修改cookie
            self.resetMenuCookie('tempHead'+fid,'tempMenuBox',fid);
            //组合cookie
            self.joinCookie();
            $('#delMenumsg .tcClose').click();
        });
        //添加栏目确定
        $('#editMenuDiv .normal_yes').live('click',function(){
            self.addMenuSure();
        });
    },
    /**
     * 编辑栏目
     * 参数1 str 操作标识
     * 参数2 fid 板块ID
     * 参数3 menuName 栏目名称
     */
    editMenu:function(str,fid,menuName,ifShowMenu){
        var menuStr='';
        var style='';
        var act='添加';
        if(str=='edit'){
            act='编辑';
        }
        if(ifShowMenu==0){
            style='style="display:none;"';
        }
        $.myDialog.normalMsgBox('editMenuDiv',act+'栏目',400,'正在加载请稍候...');
        if(str=='add'){
            menuStr +='<div class="tempEditItem">' +
            '<span class="tit">栏目列表</span>' +
            '<span class="con">' +
            '<select id="menuList" name="menu">' +
            '<option value="">正在加载请稍候...</option>' +
            '</select>' +
            '</span>' +
            '</div>';
        }
        var tempStr ='<div class="tempMenuEdit">' +
            menuStr+
            '<div class="tempEditItem">' +
            '<span class="tit">栏目名称</span>' +
            '<span class="con"><input class="text" type="text" name="menuName" value="'+menuName+'" /></span>' +
            '</div>'+
            '<div class="tempEditItem" '+style+'>' +
            '<span class="tit">添加到</span>' +
            '<span class="con">' +
            '<select id="boxAddMenuList" name="boxAddMenu">' +
            '<option value="">正在加载请稍候...</option>' +
            '</select>' +
            '<span class="beforeAfter">' +
            '<label for="before">' +
            '<input type="radio" class="radio" id="before" name="boxAddPosition" value="before">之前' +
            '</label>' +
            '<label for="after">' +
            '<input class="radio" type="radio" name="boxAddPosition" value="after" checked="checked" id="after">之后' +
            '</label>' +
            '</span>' +
            '</span>' +
            '</div>'+
            '<div>' +

            '</div>' +
            '<input type="hidden" value="'+fid+'" id="fid" name="fid">'+
            '</div>';
        $.myDialog.normalMsgBox('editMenuDiv',act+'栏目',460,tempStr,3);
    },
    // 添加栏目确定
    addMenuSure:function(){
        var self=this;
        var fid=$('#fid').val();//板块ID或者位置标识
        var type=$('#menuList').children('option:selected').val();//栏目属性
        //栏目名称
        var menuName=$.trim($.myCommon.removeHTML($('input[name="menuName"]').val()));
        var order=$('#boxAddMenuList option:selected').val();//被选元素属性
        var position=$('input[name="boxAddPosition"]:checked').val();//位置
        //如果有属性则为添加栏目
        var menuStr;
        var ifAdd=true;
        if(type) {
            var menuType = type.split('_');
            menuStr = self.joinMenuHtml(fid, '', menuType[0], menuType[1],menuName,menuType[2],menuType[3]);
        }else{
            $('#tempMenu'+fid).find('.menuName').html('【'+menuName+'】');
            $('#tempMenu'+fid+'.btnBgBox').find('.addLore').html('+ 添加内容');
            //$('#tempMenu'+fid).find('.titMenuB').html('--【'+menuName+'】--');
            ifAdd=false;
            menuStr = $('#tempMenu'+fid);

        }
        //三种位置情况
        if(order && order!=fid){//是否有被选元素
            if(position=='before'){//在被选元素之前
                $('#tempMenu'+order).before(menuStr);
            }else if(position=='after'){//在被选元素之后
                $('#tempMenu'+order).after(menuStr);
            }
        }else if(ifAdd) {
            $('#tempHead' + fid).append(menuStr);
        }
        if(!ifAdd){
            fid=fid.split('_')[0];
        }
        //关闭弹出框
        $('#editMenuDiv .tcClose').click();
        //重置位置编号
        self.resetOrder(fid,'tempMenuBox');
        //修改cookie
        self.resetMenuCookie('tempHead'+fid,'tempMenuBox',fid);
        //组合cookie
        self.joinCookie();
    },
    /**
     * 重置栏目cookie
     * 参数1 id 板块ID
     * 参数2 childClass 栏目class
     * 参数3 forumID 板块标识
     * 作者 
     */
    resetMenuCookie:function(id,childClass,forumID){
        var self=this;
        var menuContent=[];
        $('#'+id+' .' + childClass).each(function (j) {
            var order = $(this).attr('id');
            var menuName = $(this).find('.menuName').html().replace(/[ \【,\】]/g, '');
            var ifTest = $(this).attr('ifTest');
            var ifAnswer = $(this).attr('ifAnswer');
            var menuID = $(this).attr('mid');
            var content = self.joinContentCookie(order);
            var menuArr = {
                'ifAnswer': ifAnswer,
                'ifTest': ifTest,
                'menuID': menuID,
                'menuContent': content,
                'menuName': menuName,
                'menuOrder': order
            };
            menuContent.push(menuArr);
        });
        self.tempContent['forum']['tempForum'+forumID][2]=menuContent;
    },
    /**
     * 组合栏目内容cookie字符串
     * 参数 menuID 栏目ID
     * 返回 string 栏目内容cookie字符串
     * 作者 
     */
    joinContentCookie:function(menuID){
        var content=[];
        var self=this;
        $('#'+menuID+' .lore').each(function(i){
            var loreID=self.clearIdLetter($(this).attr('lID'));//试题ID或者知识ID
            var ifSystem=$(this).attr('system');//是否是系统知识或试题
            var testNum=$(this).attr('num');//小题数量
            content.push(loreID+'|'+ifSystem+'|'+testNum);
        });
        var contentStr='';
        if(content.length>0){
            contentStr=content.join(';');
        }
        return contentStr;
    },
    /**
     * 组合栏目HTML字符串
     * 参数1 fid 板块ID
     * 参数2 num 栏目在板块的序号 可以为空
     * 参数3 ifTest 是否是试题
     * 参数4 ifAnswer 是否有答案
     * 参数5 menuName 栏目名称
     * 参数6 menuID 栏目ID
     * 参数7 numStyle 序号类型
     * 参数8 ifHave 是否有内容
     * 返回 string
     * 作者 
     */
    joinMenuHtml:function(fid,num,ifTest,ifAnswer,menuName,menuID,numStyle,ifHave){
        var contentName='试题';//标题名称
        if(ifTest==0){
            contentName='内容';
        }
        var answerStr='';//是否有答案
        var cNum=3;//等待提示横跨的表格数
        if(ifTest==0 && ifAnswer==1){
            cNum=4;
            answerStr +='<td class="td loreAnswer b">答案</td>';
        }
        var waiting='';//等待提示
        if(ifHave){
            waiting='<tr class="waiting hang"><td class="td" style="height:30px;" colspan="'+cNum+'">加载中...</tdc></tr>';
        }
        var menuStr = '<div id="tempMenu'+fid+'_'+num+'" class="tempMenuBox" ifTest="'+ifTest+'" ifAnswer="'+ifAnswer+'" mid="'+menuID+'" numSty="'+numStyle+'">' +
            '<div class="menuTitle tempMenu">' +
            '<div class="tempSetBtn tempSetBtnB">' +
            '<span class="btnBgBox" order="'+fid+'_'+num+'">'+
            '<a class="addLore temp-add-btn" href="javaScript:;">+ 添加'+ contentName+'</a>'+
            '<a href="javaScript:;" class="editMenu">编辑</a>' +
            '<a href="javaScript:;" class="removeMenu">删除</a>'+
            '<a href="javaScript:;" class="menuUpMove">上移</a>' +
            '<a href="javaScript:;" class="menuDownMove">下移</a>' +
            '</span>'+
            '</div>'+
            '<div class="tempMenuTit">' +
            '<h5 title="点击修改导学案栏目名称" class="menuName editMenu" order="'+fid+'_'+num+'" style="cursor:pointer">【'+menuName+'】</h5>' +
            '</div>' +
            '</div>'+
            '<table class="menuContent tempMenuConList">'+
            '<tbody>'+
            '<tr class="hang">'+
            '<td class="td menuNum b">编号</td>'+
            '<td class="td">'+
            '<div class="menuCon c b">' +contentName+'</div>' +
            '</td>'+answerStr+
            '<td class="td handle c">操作</td>'+
            '</tr>' +
                waiting+
            '</tbody>' +
            '</table>' +
            // '<div class="addLore">' +
            // '<span>+添加'+menuName+contentName+'</span>'+
            // '</div>' +
            '</div>';
        return menuStr;
    },
    //内容的一些点击事件
    contentClick:function(){
        var self=this;
        //添加内容弹出框
        $('#dir2').on('click','.addLore',function(e,fromReplace) {
            $('.tempMenuBox').removeClass('onClick');
            $('.btnBgBox').removeClass('onClick');
            $(this).parents('.tempMenuBox').addClass('onClick');
            page=1;
            if(fromReplace==''){
                fromReplace=false;
            }
            self.addContent(false,fromReplace);
        });
        //切换内容来源
        $(document).on('click','.testTab a',function(){
            lock='';//解除绑定事件
            page=1;//重置页码
            $('.testTab').find('a').removeClass('curt');
            $('#pagelistbox').html('');
            $(this).attr('class','curt');
            var sid=$(this).attr('sid');
            var ifTest=$('.onClick').attr('ifTest');

            self.changStyle(sid,ifTest);
        });
        //添加内容确定
        $('#addLoreDiv .addCase').live('click',function(){
            var errorMsg='';
            var ifTest=$('.onClick').attr('ifTest');
            var ifOnly=$.caseCommon.checkOnlyTest($(this).attr('quesid'),ifTest); //JS验证试题存在函数
            if(!ifOnly){
                if(ifTest==0){
                    errorMsg='知识';
                }else{
                    errorMsg='试题';
                }
                $.myDialog.showMsg(errorMsg+'已存在！',1);
                return false;
            }
            if(lock!=''){
                return false;
            }
            lock='addContentSure';
            $(this).addClass('delCase');
            $(this).removeClass('addCase');
            var data={};
            data['tid']=$(this).attr('quesid');
            data['childNum']=$(this).attr('childnum');
            data['order']=$('.onClick').attr('id').replace('tempMenu','');
            data['ifTest']=$('.onClick').attr('ifTest');
            data['ifAnswer']=$('.onClick').attr('ifAnswer');
            var content=$('#quesdiv'+data['tid']).find('.testInfo').html();
            if(content.indexOf('【小题')!=-1){
                data['content']=content.replace(/【小题[0-9]*】/g,'<span class="quesindex"><b></b></span><span class="quesscore"></span>');
            }else{
                data['content']='<p><span class="quesindex"><b></b></span><span class="quesscore"></span><span class="tips"/>'+content.substring(3);
            }
            data['answer']=$('#quesdiv'+data['tid']).find('.loreAnswer').html();
            if(typeof(data['answer'])=='undefined'){
                data['answer']='';
            }
            data['system']=0;
            if(data['tid'].indexOf('c')!=-1 || data['tid'].indexOf('u')!=-1){
                data['system']=1;
            }
            self.addContentSure(data);
        });
        //展示或隐藏答案
        $(document).on('click','.testInfo',function(){
            var obj=$(this);
            self.showAnswer(obj);
        });
        //移除内容
        $('#dir2').on('click','.removeLore',function(){
            var order=$(this).parent('.btnBgBox').attr('order');
            var typeName=$(this).parents('.tempMenuBox').children().find('h5').html();
            var testNum=$(this).parents('.hang').children('.order').html();
            $.myDialog.normalMsgBox('delLoremsg','信息提示',450,'<div><b class="delLore"  order="'+order+'">您确定删除'+typeName+'栏目下的第'+testNum+'条记录？</b></div>',3);
        });
        $('#delLoremsg .normal_yes').live('click',function(){
            var order=$('#delLoremsg .delLore').attr('order');
            $('#lore'+order).remove();
            //重置编号
            self.resetTests();
            //更新cookie
            var fid=order.split('_')[0];
            self.resetMenuCookie('tempHead'+fid,'tempMenuBox',fid);
            //组合cookie
            self.joinCookie();
            $('#delLoremsg .tcClose').click();
        })
        //试题详情
        $('#dir2').on('click','.loreInfo',function(){
            var tmp_id=$(this).parent('.btnBgBox').attr('tid');
            var idname="textdiv";
            var tmp_str='数据加载中请稍候...';
            $.myDialog.tcLoadDiv("试题详细",600,idname);
            $('#'+idname+' .content').html(tmp_str);
            $.myDialog.tcShowDiv(idname);
            $('#div_shadow').css({'display':'block'});
            $.post(U('Index/getDetailTestById'),{"id":tmp_id,'width':self.docwidth,"rand":Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                tmp_str='<div style="width:582px;height:330px;border:1px solid #039;overflow-x:hidden;overflow-y:scroll;position:relative;">'+
                '<div>'+
                '<div style="background:#efefef;border-bottom:1px solid #ccc;padding:5px;line-height:18px;">'+
                '<div><b>题号：</b><span>'+data['data'][0][tmp_id]['testid']+'</span>，<b>题型：</b>'+data['data'][0][tmp_id]['typesname']+'，<b>难度：</b>'+data['data'][0][tmp_id]['diffname']+'，<b>日期：</b>'+data['data'][0][tmp_id]['firstloadtime']+'</div>'+
                '<div><b>标题/来源：</b>'+data['data'][0][tmp_id]['docname']+'</div>'+
                '</div>'+
                '</div><div><div class="quesbody"><p>'+data['data'][0][tmp_id]['test']+'</div>'+
                '<div class="quesanswer" style="display:block;"><p><font color="red">【答案】</font>'+data['data'][0][tmp_id]['answer']+'</div>';
                if(data['data'][0][tmp_id]['analytic']) tmp_str+='<div class="quesparse" style="display:block;"><p><font color="red">【解析】</font>'+data['data'][0][tmp_id]['analytic']+'</div>';
                if(data['data'][0][tmp_id]['analytic']) tmp_str+='<div class="quesremark" style="display:block;"><p><font color="red">【备注】</font>'+data['data'][0][tmp_id]['remark']+'</div>';
                tmp_str+='</div></div>';
                $('#'+idname+' .content').html(tmp_str);
                $.myDialog.tcDivPosition(idname);
            });
        });
        //替换框
        $('#dir2').on('click','.loreReplace',function(){
            $('.tempMenuBox').removeClass('onClick');
            $('.btnBgBox').removeClass('onClick');
            $(this).parent('.btnBgBox').addClass('onClick');
            var testAllID=self.getTestID()[1].replace('|',',');
            testAllID=testAllID.replace('@$@','');
            var len=parseInt(testAllID.length);
            testAllID=testAllID.substr(0,len-1);
            var testID=$(this).parent('.btnBgBox').attr('tid');
            var idName="replacediv";
            var tmp_str='数据加载中请稍候...';
            $.myDialog.normalMsgBox(idName,"试题替换--准备替换试题【ID="+testID+"】",580,tmp_str,5);
            $.myTest.getSameTest(testID,testAllID,idName);
        });
        //弹出框点击显示试题
        $(document).on('click','#diaquesswitch .diaques',function(){
            var num=parseInt($(this).html())-1;
            $.myTest.showDiaTest(num);//替换  试题的显示层
        });
        //替换框内点击刷新，替换试题
        $(document).on('click','.sameRefresh',function(){
            var testID=$(this).attr('tmpId');
            var testAllID=$(this).attr('tmpAllId');
            var idName=$(this).attr('idname');
            $.myTest.getSameTest(testID,testAllID,idName);
        });
        //确定替换
        $(document).on('click','#replacediv .diarepalcebtn',function(){
            var tid='';
            var order=$('.onClick').attr('order');
            var tmpStr='';
            var childNum=1;
            $('#diaqueslistbox .diaquesbox').each(function(){
                if($(this).css('display')=='block'){
                    $(this).find('.diaquesbody .quesindex').html('');
                    tmpStr=$(this).find('.diaquesbody div').html();
                    if(tmpStr.indexOf('【小题')!=-1){
                        tmpStr=tmpStr.replace(/【小题[0-9]*】/g,'<span class="quesindex"><b></b></span><span class="quesscore"></span>');
                    }else{
                        tmpStr='<p><span class="quesindex"><b></b></span><span class="quesscore"></span><span class="tips"/>'+tmpStr.substring(3);
                    }
                    childNum=$(this).attr('num');
                    tid=$(this).find('td:eq(0)').html().split(/<SPAN[^>]*>/i);
                    tid=tid[1].match(/\d+/g);//试题id
                }
            });
            $('#replacediv .tcClose').click();
            $('#lore'+order).attr('num',childNum);
            $('#lore'+order).attr('lid',tid);
            $('#lore'+order).find('.quesbody').html(tmpStr);//写入数据
            $('#lore'+order).find('.btnBgBox').attr('tid',tid);
            //重置编号
            self.resetTests();
            //更新cookie
            var fid=order.split('_')[0];
            self.resetMenuCookie('tempHead'+fid,'tempMenuBox',fid);
            //组合cookie
            self.joinCookie();
        });
        //修改知识
        $('#dir2').on('click','.lore .editLore',function(){
            var ifSystem=$(this).parent('.btnBgBox').attr('system');
            var system=0;
            if(ifSystem==0){
                system=1;
            }
            $('.tempMenuBox').removeClass('onClick');
            $('.btnBgBox').removeClass('onClick');
            $(this).parent('.btnBgBox').addClass('onClick');
            var tid=self.clearIdLetter($(this).parent().attr('tid'));
            self.editLore(tid,system);
        });
        //系统知识替换
        $('#dir2').on('click','.systemLoreReplace',function(){
             var attr=$(this).parent();
             attr=attr.attr('order');
             $(this).parents('.tempMenuBox').find('.addLore').trigger('click',attr);
        });
        //修改知识保存
        $(document).on('click','#addStudiv .normal_yes,.saveLore .nBtn',function(){
            if($('#addLoreDiv').length>0){
                self.saveLore(true);
            }else{
                self.saveLore(false);
            }

        });
        //添加知识点
        $(document).on('click','.addLoreButton',function(){
            self.addLore();
        });
        //返回知识点列表
        $(document).on('click','.back .nBtn',function(){//绑定返回事件
            $('.testTab a[sid="1"]').click();
        });
        //添加知识取消按钮
        $(document).on('click','#addLoreDiv .normal_no',function(){
            $('#addLoreDiv .tcClose').click();
        });
        $('#keywordBtn').live('click', function(){
            style=$('#addLoreDiv .curt').attr('sid');
            self.getTestStyle(style);//获取试题
        });
    },
    //清除ID前的字母标识
    clearIdLetter:function(id){
        var lid=id.replace(/^[a-z]/g,'');
        return lid;
    },
    //添加内容弹出框
    /**
     * @param sid 添加知识后停留界面选择
     * @param fromReplace 是否自来系统知识替换
     */
    addContent:function(sid,fromReplace){
        var msgDivWidth=($(window).width()-100)<1200?($(window).width()-80):1200;
        var msgDivHeight=($(window).height()-200)<600?($(window).height()-180):600;
        var self=this;
        var ifTest=$('.onClick').attr('ifTest');
        var menuName=$('.onClick').find('.menuName').html().replace(/[ \【,\】]/g,'');
        var name='试题';
        var catalog='';
        var filterBox='';
        var listPx='';
        var i;
        var buttom='';
        self.changeChapterID='';
        $.post(U('Guide/Case/checkChapter'),{'chapterID':self.chapterID},function(msg){
            if($.myCommon.backLogin(msg)==false){
                return false;
            }
            var data=msg['data'];
            for(i in data){
                var nextID=parseInt(i)+1;
                var lastID=parseInt(i)-1;
                if(data[i]['ChapterID']==self.chapterID){
                    self.thisChapterName=data[i]['ChapterName'];
                }
                if(data[i]['ChapterID']==self.chapterID && i==0){
                    buttom='<span class="jump-chapter-box"><span class="jumpChapter button" chapterName="'+data[nextID]['ChapterName']+'" chapterID="'+data[nextID]['ChapterID']+'">下一章</span></span>';
                }else if(data[i]['ChapterID']==self.chapterID && data.length==nextID){
                    buttom='<span class="jump-chapter-box"><span class="jumpChapter button" chapterName="'+data[lastID]['ChapterName']+'" chapterID="'+data[lastID]['ChapterID']+'">上一章</span></span>';
                }else if(data[i]['ChapterID']==self.chapterID && data.length!=nextID && i!=0){

                    buttom='<span class="jump-chapter-box"><span class="jumpChapter button" chapterName="'+data[lastID]['ChapterName']+'" chapterID="'+data[lastID]['ChapterID']+'">上一章</span><span class="jumpChapter button" chapterName="'+data[nextID]['ChapterName']+'" chapterID="'+data[nextID]['ChapterID']+'">下一章</span></span>';
                }
            }
            if(ifTest==0){
                name='知识';
                filterBox='<div class="addLoreButton none" onselectstart="return false"><a class="nBtn">添加知识点</a></div><div class="back none" onselectstart="return false"><a class="nBtn">返回</a></div>';
            }else{
                catalog='<a href="javascript:;" sid="2">我的收藏</a>';
                filterBox='<div id="filterbox">正在加载请稍候...</div>';
                listPx='<div id="list_px">' +
                '<span>排序：</span>' +
                '<a href="#" class="button button_current" type="0">默认</a>' +
                '<a href="#" type="pdown" class="button">人气<b></b></a>' +
                '<a href="#" type="ddown" class="button">难易度<b></b></a>' +
                '<a href="#" type="tdown" class="button">上传时间<b></b></a>' +
                '<div id="pagediv">共<a id="quescount"></a>道题</div>' +
                '</div>';
            }
            var cNameTmp='';
            if(fromReplace==false || fromReplace==undefined){
                fromReplace='';
            }else{
                cNameTmp=' fromReplace';
            }
            var str='<div class="testTab'+cNameTmp+'" fromReplace="'+fromReplace+'">' +
                '<a href="javascript:;" class="curt" sid="0">系统'+name+'</a>' +
                '<a href="javascript:;" sid="1">我的'+name+'</a>' +
                catalog+
                '</div>' +
                '<div class="testConCurt">'+
                '<div id="rightdiv" style="overflow-y:scroll;min-height:300px; height:'+msgDivHeight+'px;position:relative;">' +
                '<div id="righttop" class="knowCrumbs">' +
                '<div id="categorylocation">' +
                '<span class="nowPath">选题范围：</span> >' +
                '<span id="loca_text" class="oldChapterPath">'+self.chapterPath+'</span>'+buttom +
                '</div>' +
                '</div>'+filterBox+listPx+
                '<div id="queslistbox">' +
                '<p class="list_ts">' +
                '<span class="ico_dd">'+name+'加载中请稍候...</span>' +
                '</p>' +
                '</div>'+
                '<div id="pagelistbox"></div>'+
                '</div>' +
                '</div>';
            $.myDialog.normalMsgBox('addLoreDiv','添加'+menuName+name,msgDivWidth,str);
            if(sid!==false){//添加知识后知识Tab跳转
                $('.testTab a[sid="' +sid+'"]').click();
                return false;
            }
            self.addTest(ifTest);
        });
    },
    /**
     * 重置试题添加弹出框大小
     *
     */
    resetTestBox:function(){
        var w = ($(window).width()-100)<1200?($(window).width()-80):1200;
        var h = ($(window).height()-200)<600?($(window).height()-180):600;
        if(w<600) w=600;
        if(h<300) h=300;
        $("#addLoreDiv").width(w);
        $('#rightdiv').height(h);//弹出框高度
    },
    /**
     * 添加试题，点击下一章，上一章
     */
    addTestChangeChapter:function(){
        var self=this;
        var i,button;
        $('.jumpChapter').live('click',function(){
            self.changeChapterID=$(this).attr('chapterID'); //赋值参数
            var replaceName='>>'+$(this).attr('chapterName');
            $.post(U('Guide/Case/checkChapter'),{'chapterID':self.changeChapterID},function(msg){
                if($.myCommon.backLogin(msg)==false){
                    return false;
                }
                var nowPath=$('.oldChapterPath').text();
                var newPath=nowPath.replace('>>'+self.thisChapterName,replaceName);
                $('.oldChapterPath').text(newPath);
                $('.jumpChapter').remove();
                var data=msg['data'];
                for(i in data){
                    var nextID=parseInt(i)+1;
                    var lastID=parseInt(i)-1;
                    if(data[i]['ChapterID']==self.changeChapterID){
                        self.thisChapterName=data[i]['ChapterName'];
                    }
                    if(data[i]['ChapterID']==self.changeChapterID && i==0){
                        button='<span class="jump-chapter-box"><span class="jumpChapter button" chapterName="'+data[nextID]['ChapterName']+'" chapterID="'+data[nextID]['ChapterID']+'">下一章</span></span>';
                    }else if(data[i]['ChapterID']==self.changeChapterID && data.length==nextID){
                        button='<span class="jump-chapter-box"><span class="jumpChapter button" chapterName="'+data[lastID]['ChapterName']+'" chapterID="'+data[lastID]['ChapterID']+'">上一章</span></span>';
                    }else if(data[i]['ChapterID']==self.changeChapterID && data.length!=nextID && i!=0){
                        button='<span class="jump-chapter-box"><span class="jumpChapter button" chapterName="'+data[lastID]['ChapterName']+'" chapterID="'+data[lastID]['ChapterID']+'">上一章</span><span class="jumpChapter button" chapterName="'+data[nextID]['ChapterName']+'" chapterID="'+data[nextID]['ChapterID']+'">下一章</span></span>';
                    }
                }
                $('.oldChapterPath').after(button);
                var ifTest=$('.onClick').attr('ifTest');
                self.addTest(ifTest);
            })
        })
    },
    /**
     * 获取试题或者知识
     * 参数 ifTest 是否是试题 0不是 1是
     * 作者 
     */
    addTest:function(ifTest){
        var self=this;
        if(ifTest==1) {
            //显示学科及属性
            self.showTestAttr(self.subjectID, 1);
            self.getTestStyle(0);
        }else{
            //获取知识
            self.getTestStyle(4);//跳转到系统知识
        }
    },
    /**
     * 切换内容来源
     * 参数1 sid 来源标识
     * 参数2 ifTest 是否是试题
     */
    changStyle:function(sid,ifTest){
        if(sid==1 && ifTest==0){//个人知识
            sid=3;
        }else if(sid==0 && ifTest==0){//系统知识
            sid=4;
        }
        if(sid==0){//系统试题
            $('#filterbox').show();
            $('#list_px').show();
            $('#filterbox .docType').show();
        }else if(sid==1){//个人试题
            $('#filterbox').show();
            $('#list_px').hide();
            $('#filterbox .docType').hide();
        }else{//我的收藏
            $('#filterbox').hide();
            $('#list_px').hide();
            $('.back').hide();//隐藏返回按钮
            //个人只是显示添加按钮 系统知识隐藏按钮
            if(sid==4){
                $('.addLoreButton').hide();
            }else if(sid==3){
                $('.addLoreButton').show();
            }
        }
        this.getTestStyle(sid);
    },
    //获取试题的方式
    getTestStyle:function(style){
        var self=this;
        var cid=self.chapterID;
        if(self.changeChapterID!=''){
            cid=self.changeChapterID;
        }
        var tid= self.getTypes();
        var dif=self.getDiff();
        var dtid=self.getDocType();
        var order=self.getOrder();
        var abi=self.getAbility();
        var key = self.getKeywords();
        var menuID=$('.onClick').attr('mid');
        var useFunction='';
        var where =[];
        var ifTest=1;
        switch (parseInt(style)) {
            case 0:
                useFunction='Index/getTestList';
                where={'key':key,'randoms':1,'sid':self.subjectID,'abi':abi,'cid':cid,'tid':tid,'dtid':dtid,'dif':dif,'o':order,'page':page,'perpage':10,'rand':Math.random()};
                break;
            case 1:
                if(order==0){
                    order='def';
                }
                useFunction='Custom/CustomTestStore/getTestList';
                where={'key':key,'randoms':1,'subject':self.subjectID,'abi':abi,'chapter':cid,'types':tid,'dtid':dtid,'diff':dif,'order':order,'page':page,'perpage':10,'rand':Math.random()};
                break;
            case 2:
                useFunction='Index/testFav';
                where={'cataID':'all','page':page ,'subjectID':self.subjectID,'m':Math.random()};
                break;
            case 3:
                useFunction='Guide/Case/ajaxGetLore';
                where={'ifSystem':0,'id':0,'chapterID':cid,'menuID':menuID,'page':page,subjectID:self.subjectID};
                ifTest=0;
                break;
            case 4:
                useFunction='Guide/Case/ajaxGetLore';
                where={'ifSystem':1,'id':0,'chapterID':cid,'menuID':menuID,'page':page,subjectID:self.subjectID};
                ifTest=0;
                break;
        }
        //获取试题
        self.getTest(useFunction,where,2,page,ifTest);
        lock='';//释放绑定事件
    },
    /**
     * 添加内容确定
     * 参数1 data 内容
     * 作者 
     */
    addContentSure:function(data){
        var self=this;
        var c={
            'ifTest':data['ifTest'],
            'ifAnswer':data['ifAnswer'],
            'content':{
                0:{
                    'childNum':data['childNum'],
                    'testID':data['tid'],
                    'test':data['content'],
                    'system':data['system'],
                    'answer':data['answer']
                }
            }
        };
        var contentStr=self.joinContentHtml(c);
        lock='';
        if($('.testTab').hasClass('fromReplace')){//来自知识替换
            var order=$('.fromReplace').attr('fromReplace');
            $('#lore'+order).replaceWith(contentStr);
            //替换时关闭弹出框
            $('#addLoreDiv .tcClose').click();
        }else {
            //将内容写入页码
            $('#tempMenu' + data['order']).find('.menuContent').append(contentStr);
        }
        //重置编号
        self.resetTests();
        //修改cookie
        var fid=data['order'].split('_')[0];
        self.resetMenuCookie('tempHead'+fid,'tempMenuBox',fid);
        //组合cookie
        self.joinCookie();
    },
    //组合内容HTML字符串
    //[{ifTest:0,ifAnswer:0,content:{0:{childNum:1,testID:23789,test:'<p>阅读下面文章回答问题</p>}}]
    joinContentHtml:function(content){
        //循环第一层获取是否是试题，是否有答案
        var str='';
        var answer='';
        var key='';
        if(content['ifTest']==1){
            key='<a href="javaScript:;" class="loreInfo">详情</a>' +
            '<a href="javaScript:;" class="loreReplace">替换</a>';
        }

        for(var i in content['content']){
            if(isNaN(i)){
                continue;
            }
            if(content['content'][i]['system']!=0 && content['ifTest']==0){
                key='<a href="javaScript:;" class="editLore">编辑</a>';
            }else if(content['content'][i]['system']==0 && content['ifTest']==0){//系统知识添加替换
                key='<a href="javaScript:;" class="systemLoreReplace">替换</a>';
            }

            str+='<tr class="hang lore" num="'+content['content'][i]['childNum']+'" lid="'+content['content'][i]['testID']+'" system="'+content['content'][i]['system']+'">' +
            '<td class="td order"></td>' +
            '<td class="td"><div class="menuCon quesbody">'+content['content'][i]['test']+'</div></td>';
            if(content['ifAnswer']==1 && content['ifTest']=='0'){
                str +='<td class="td lAnswer"><div class="lAnswerCon">'+content['content'][i]['answer']+'</div></td>';
            }
            str+='<td class="td handle">' +
            '<div class="tempSetBtn tempSetBtnB">'+
            '<span class="btnBgBox" order="" tid="'+content['content'][i]['testID']+'" system="'+content['content'][i]['system']+'">'+
            key+
            '<a href="javaScript:;" class="removeLore">删除</a>'+
            '<a href="javaScript:;" class="loreUpMove">上移</a>'+
            '<a href="javaScript:;" class="loreDownMove">下移</a>'+
            '<span>'+
            '<div>'+
            '</td>' +
            '</tr>';
        }
        return str;
    },
    //获取试题
    changeWhere:function(){
        var self=this;
        var style='';
        //根据试题类型查看试题
        $('#questypeselect a').live('click',function(){
            if(lock!='') return false;
            lock='questypeselect';
            page=1;
            $('#quesabilityselect a').addClass('button').removeClass('button_current');
            $('#quesabilityselect a').first().addClass('button_current').removeClass('button');
            $('#questypeselect .button_current').addClass('button').removeClass('button_current');
            $(this).addClass('button_current');
            $(this).removeClass('button');
            style=$('#addLoreDiv .curt').attr('sid');
            self.getTestStyle(style);//获取试题
        });
        //根据难度查看试题
        $('#quesdiffselect a').live('click',function(){
            if(lock!='') return false;
            lock='quesdiffselect';
            page=1;
            $('#quesabilityselect a').addClass('button').removeClass('button_current');
            $('#quesabilityselect a').first().addClass('button_current').removeClass('button');
            $('#quesdiffselect .button_current').addClass('button').removeClass('button_current');
            $(this).addClass('button_current');
            $(this).removeClass('button');
            style=$('#addLoreDiv .curt').attr('sid');
            self.getTestStyle(style);//获取试题
        });
        //根据文档类型查看试题
        $('#quesdoctypeselect a').live('click',function(){
            if(lock!='') return false;
            lock='quesdoctypeselect';
            page=1;
            $('#quesabilityselect a').addClass('button').removeClass('button_current');
            $('#quesabilityselect a').first().addClass('button_current').removeClass('button');
            $('#quesdoctypeselect .button_current').addClass('button').removeClass('button_current');
            $(this).addClass('button_current');
            $(this).removeClass('button');
            style=$('#addLoreDiv .curt').attr('sid');
            self.getTestStyle(style);//获取试题
        });
        //根据能力值查看试题
        $('#quesabilityselect a').live('click',function(){
            if(lock!='') return false;
            lock='quesabilityselect';
            page=1;
            $('#questypeselect a').addClass('button').removeClass('button_current');
            $('#questypeselect a').first().addClass('button_current').removeClass('button');
            $('#quesdiffselect a').addClass('button').removeClass('button_current');
            $('#quesdiffselect a').first().addClass('button_current').removeClass('button');
            $('#quesdoctypeselect a').addClass('button').removeClass('button_current');
            $('#quesdoctypeselect a').first().addClass('button_current').removeClass('button');
            $('#quesabilityselect .button_current').addClass('button').removeClass('button_current');
            $(this).addClass('button_current');
            $(this).removeClass('button');
            style=$('#addLoreDiv .curt').attr('sid');
            self.getTestStyle(style);//获取试题
        });
        //根据排序查看试题
        $('#list_px a.button').live('click',function(){
            if(lock!='') return false;
            lock='list_px';
            page=1;
            var tmp_str=$(this).attr('type');
            if($(this).attr('class').indexOf('button_current')!=-1){
                if(tmp_str.indexOf('down')!=-1) {
                    $(this).attr('type',tmp_str.replace('down','up'));
                    $(this).find('b').css({'background-position':'-7px 0px'});
                }
                else{
                    $(this).attr('type',tmp_str.replace('up','down'));
                    $(this).find('b').css({'background-position':'0px 0px'});
                }
            }else{
                $('#list_px .button_current').addClass('button').removeClass('button_current');
                $(this).addClass('button_current');
            }
            style=$('#addLoreDiv .curt').attr('sid');
            self.getTestStyle(style);//获取试题
        });
        //加载更多
        $('#addLoreDiv .pagebox a').live('click',function(){
            var page=parseInt($(this).attr('page'))+1;
            var total=parseInt($(this).attr('total'));
            style=$('#addLoreDiv .curt').attr('sid');
            var ifTest=$('.onClick').attr('ifTest');
            self.goToPage(page,style,ifTest);
        });
    },
    /**
     * 下一页
     * 参数1 num 页码
     * 参数2 style 内容类型
     */
    goToPage:function(num,style,ifTest){
        var self=this;
        if(lock!='') return false;
        lock='page';
        if(num<1 && page==1){
            lock='';
            return false;
        }
        page=num;
        self.changStyle(style,ifTest);//获取试题
    },
    //获取已加入的试题ID和知识ID
    getTestID:function(){
        var testID1='';
        var testID2='';
        var ifTest=0;
        $('.tempModel').each(function(){
            $(this).find('.tempMenuBox').each(function(){
                ifTest=$(this).attr('ifTest');
                $(this).find('.lore').each(function(){
                    if(ifTest==0){
                        testID1+='@$@'+$(this).attr('lid')+'|';
                    }else{
                        testID2+='@$@'+$(this).attr('lid')+'|';
                    }
                })
            })
        });
        var testID=[testID1,testID2];
        return testID;
    },
    /**
     * 判断内容是否已加入导学案
     * 参数1 id 试题或者知识ID
     * 参数2 ifTest 是否是试题
     * 返回值 {boolean}
     * 作者 
     */
    ifAdd:function(id,ifTest){
        var self=this;
        var testID=self.getTestID();
        var tid='@$@'+id+'|';
        if(testID[ifTest] && testID[ifTest].indexOf(tid)!=-1){
            return true;
        }else{
            return false;
        }
    },
    //获取章节ID
    getChapterID:function(){
        this.chapterID=this.clearIdLetter($('.selectChapter').last().val());
    },
    //根据章节ID获取章节路径
    getChapterPath:function(){
        var self=this;
        if(!self.chapterID){
            return false;
        }
        $.post(U("Index/getData"),{"style":'chapterList','subjectID':self.subjectID,'list':"chapterList",'ID':self.chapterID},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            }
            self.chapterPath=data['data'][0]['ChapterName'];
        });
    },
    //获取最终章节名称
    getChapterName:function(){
        var allPath=$('#chapterFirst option:selected').html();
        $('.selectChapter option:selected').each(function(){
            allPath+='·'+$(this).html().replace('·','$!$');
        })
        this.canChangePath=allPath;
        this.chapterName=$('.selectChapter option:selected').last().html().replace('·','$!$');//.replace(/(^[0-9])\W([0-9])?/g,'');
        this.showPath=allPath.replace($('.selectChapter option:selected').last().html().replace('·','$!$'),'');
    },
    //展示答案
    showAnswer:function(obj){
        var self=this;
        var adiv=obj.next('.quesanswer');
        var tid=adiv.attr('tid');
        if(tid=='0') return;
        if(obj.next('.quesanswer').css('display')=='block'){
            obj.next('.quesanswer').css('display','none');
            obj.parent().find('.quesparse').css('display','none');
            obj.parent().find('.quesremark').css('display','none');
        }else{
            if(obj.next('.quesanswer').attr('show')==0){
                if(lock!=''){
                    return false;
                }
                lock='Index/getOneTestById';
                $.post(U('Index/getOneTestById'),{'id':tid,'width':500,'s':Math.random()},function(data){
                    lock='';
                    if($.myCommon.backLogin(data)==false){
                        return false;
                    }
                    if(data['data'][0]=='success'){
                        var result=data['data'][1][0][0];
                        var str='<div class="quesanswer_tit">答案</div><p>'+
                            result['answer'];
                        if(result['analytic'] && result['analytic']!='</p>'){
                            str+='<div class="quesanswer_tit">解析</div><p>'+result['analytic'];
                        }
                        if(result['kllist']){
                            str+='<div class="quesanswer_tit">知识点</div><p>'+result['kllist'];
                        }
                        if(result['remark'] && result['remark']!='</p>'){
                            str+='<div class="quesanswer_tit">备注</div><p>'+result['remark'];
                        }
                        adiv.html(str);
                        adiv.attr('show',1);
                    }else{
                        alert(data['data']);
                    }
                });
            }
            obj.next('.quesanswer').css('display','block');
            obj.parent().find('.quesparse').css('display','block');
            obj.parent().find('.quesremark').css('display','block');
        }
    },
    /***
     * 展示学科下试题属性条件（通用）
     * 参数1 subjectID 学科ID
     * 参数2 docTypeShow 文档类型是否显示 0不显示 1显示
     * 作者 
     */
    showTestAttr:function(subjectID,docTypeShow){
        var self=this;
        if(lock!=''){
            return false;
        }
        var str='';
        var ifShow='';//是否显示
        if(docTypeShow==0){
            ifShow='display:none;';
        }
        lock='Index/getTypes';
        $.post(U('Index/getTypes'),{'id':subjectID,'style':1,'m':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            }
            str='<div class="filterbox_li"><table border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="45">题型</td><td><span id="questypeselect"><a href="javascript:void(0);" class="button_current" qdid="0">全部</a>';
            for(var i in data['data'][1]){
                str+='<a href="javascript:void(0);" class="button" qdid="'+data['data'][1][i]['TypesID']+'">'+data['data'][1][i]['TypesName']+'</a>';
            }
            str+='</span></td><td> </td></tr></tbody></table></div><div class="filterbox_li"><table  border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="65">难度系数</td><td><span id="quesdiffselect"><a href="javascript:void(0);" class="button_current" qdid="0">全部</a>';
            for(var i in data['data'][2]){
                str+='<a href="javascript:void(0);" class="button" qdid="'+data['data'][2][i]['DiffID']+'" title="'+data['data'][2][i]['DiffArea']+'">'+data['data'][2][i]['DiffName']+'</a>';
            }
            str+='</span></td><td> </td></tr></tbody></table></div>';
            str+='<div class="filterbox_li docType" style="background:none;';
            str+=ifShow+'"><table  border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="65">文档类型</td><td><span id="quesdoctypeselect"><a href="javascript:void(0);" class="button_current" qdid="0">全部</a>';
            for(var i in data['data'][4]){
                str+='<a href="javascript:void(0);" class="button" qdid="'+data['data'][4][i]['TypeID']+'" title="'+data['data'][4][i]['TypeName']+'">'+data['data'][4][i]['TypeName']+'</a>';
            }
            str+='</span></td><td> </td></tr></tbody></table></div>';

            //2015-8-13  关键词查询
            str+='<div class="filterbox_li"><table border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="45">关键词</td><td><input type="text" id="keywords"><button id="keywordBtn">搜索</button></td><td> </td></tr></tbody></table></div>';

            $('#filterbox').html(str);
            str='';
            str+='<div class="filterbox_li" style="background:none;display:none;"><table  border="0" cellpadding="0" cellspacing="0"><tbody><tr><td>“<span class="nowchapter"></span>”课后推荐习题　</td><td><span id="quesabilityselect"><a href="javascript:void(0);" class="button_current" qdid="0">全部</a>';
            for(var i in data['data'][5]){
                str+='<a href="javascript:void(0);" class="button" qdid="'+data['data'][5][i]['AbID']+'" title="'+data['data'][5][i]['AbilitName']+'">'+data['data'][5][i]['AbilitName']+'</a>';
            }
            str+='</span></td><td> </td></tr></tbody></table></div>';
            $('#ability').html(str);
            lock='';
        });

    },
    //模板新增知识到页面和数据库
    addLore:function(){
        var menuID=$('.onClick').attr('mid');
        var ifAnswer=$('.onClick').attr('ifAnswer');
        if(ifAnswer==='0'){
            ifAnswer='';
        }else{
            ifAnswer='add';
        }
        var data={
            'data':{
                'Lore':'',
                'Answer':ifAnswer,
                'MenuID':menuID,
                'ChapterID':this.chapterID,
                'showChapterZone': '',
                'LoreID':0
            }
        };
        var html=this.editContentHtml(data['data']);
            html+='<div class="saveLore"><a class="nBtn">保存</a></div>';
        if($('#pagelistbox').length>0){//添加知识时去除可能存在的加载更多
            $('#pagelistbox').html('');
        }
        $('.addLoreButton').hide();//隐藏添加知识按钮
        $('.back').show();//显示返回按钮
        $('#addLoreDiv .nowPath').html('当前章节:');//更换导航文字
        $("#queslistbox").html(html);//@todo 优化界面样式
        this.loreEditAlert(data);
    },
    //编辑内容HTML
    editContentHtml:function(data){
        var contentStr='<form action="'+U('Guide/Case/saveTempLore')+'" method="post" id="saveForm">'+
            '<input type="hidden" name="LoreID" value="'+data["LoreID"]+'"/>'+
            '<input type="hidden" name="MenuID" value="'+data["MenuID"]+'"/>'+
            '<div class="popupbox">'+
            '<div class="popupnr">';
        if(data['showChapterZone']!='' && data['showChapterZone']!=undefined) {
            contentStr += '<div id="showChapterZone" style="clear:both;margin-top:10px;">' + data['showChapterZone'] + '</div>';
        }
        contentStr += '<input type="hidden" id="chapterRealValue" name="ChapterID" value="'+data['ChapterID']+'">';
        contentStr +='</div> <div class="popuptit">知识内容</div> <div class="popupnr loreEditor"></div>';
        if(data['Answer']!=''){
            contentStr +='<div class="popuptit">知识答案</div>'+
            '<div class="popupnr anwserEditor"></div>';
        }
        contentStr +='</div></form>';
        return contentStr;
    },
    //知识编辑弹出框
    loreEditAlert:function(data){
        var opt = {
            toolbars: [[
                'source', 'bold', 'italic', 'underline', '|', 'fontsize', 'forecolor', '|', 'simpleupload', 'scrawl','|','wordimage'
            ]],
            initialFrameWidth : '100%'
        };
        $.Editor.init(U(caseUploadUrl+'/upload?dir=customTest'));
        $.Editor.container = $('.loreEditor');
        $.Editor.createContent(data['data']['Lore'], $.extend({
            'textarea' : 'Lore',
            'title' : '知识'
        },opt));
        if(data['data']['Answer']) {
            if(data['data']['Answer']==='add'){
                data['data']['Answer']='';
            }
            $.Editor.container = $('.anwserEditor');
            $.Editor.createAnalyze(data['data']['Answer'], $.extend({
                'textarea': 'Answer',
                'title': '答案'
            }, opt));
        }
    },
    /**
     * 修改知识
     * 参数1 知识ID
     * 参数2 是否是系统知识
     * 作者 
     */
    editLore : function(id,ifSystem){
        var self = this;
        var msgDivWidth=($(window).width()-100)<1000?($(window).width()-80):1000;
        if(msgDivWidth<600) msgDivWidth=600;        if(lock!=''){
            return false;
        }
        lock = 'Case/editMyLore';
        $.post(U('Guide/Case/editMyLore'),{'id':id,'ifSystem':ifSystem, r:Math.random()},function(data){
            lock = '';
            if($.myCommon.backLogin(data)==false){
                return false;
            }
            var html='<div class="addStudivCon">'+self.editContentHtml(data['data'])+ '</div>';
            $.myDialog.normalMsgBox('addStudiv', '编辑知识', msgDivWidth, html, 3);
            self.loreEditAlert(data);//编辑器弹出框
            $('#cancelSave').click(function(){
                $('#addStudiv .tcClose').trigger('click');
            });
        });
        return false;
    },
    //保存知识
    /**
     * @param fromLoreList 是否来自我的知识列表->编辑知识->触发的保存
     */
    saveLore:function(fromLoreList){
        var self=this;
        if(lock!=''){
            $.myDialog.normalMsgBox('successDialog','错误', 500, '数据正在加载中，请稍候！' , 2);
            return false;
        }
        //对输入的知识点进行处理
        var inputLore=$.trim($('textarea[name="Lore"]').val());
        if(inputLore==''){
            alert('知识点内容不能为空');
            return false;
        }
        var form = $('#saveForm');
        var data = form.serialize();//表单内容

        lock = form.attr('action');
        $.post(form.attr('action'), data, function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            }
            if(data['data'][0] == 'success'){
                $.myDialog.normalMsgBox('successDialog','结果', 500, '保存成功！' , 1);
                lock='';
                var act='edit';
                var order=$('.onClick').attr('order');//获取修改知识的位置
                if(!fromLoreList){
                    if(typeof (order)=='undefined'){
                         order=$('.onClick').attr('id').replace('tempMenu','');
                         act='add';
                    }
                    self.changeTempLore(data['data'][1],order,act);//改变模板页面内容
                }else{
                    //如果模板中已含该知识点更改该知识点
                    var loreID=$('input[name="LoreID"]').val();
                    $('.onClick .handle .btnBgBox').each(function(){
                          if('u'+loreID==$(this).attr('tid')){
                               order=self.clearIdLetter($(this).attr('order'));
                               self.changeTempLore(data['data'][1],order,act);//改变模板页面内容
                               return false;
                          }
                    });
                }
                if(fromLoreList){
                    self.addContent($('.testTab a.curt').attr('sid'));
                }
                $('#successDialog .normal_no').click(function(){//点击确定后关闭弹出框
                    $('#successDialog .tcClose').trigger('click');
                    $('#addStudiv .tcClose').trigger('click');
                });
            }else{
                $.myDialog.normalMsgBox('successDialog','结果', 500, data['data'] , 2);
            }
        });
    },
    /**
     * 改变模板知识内容
     * 参数1 id 知识ID
     * 参数2 order 位置标识
     * 参数3 act 操作标识
     * 作者 
     */
    changeTempLore:function(id,order,act){
        var self=this;
        $.post(U('Guide/Case/ajaxGetLoreByID'),{'id':id},function(e){
            if($.myCommon.backLogin(e)==false){
                return false;
            }
            if(e['data'][0] == 'success'){
                var data = e['data'][1];
                if(act==='edit') {//@todo 里面的代码可写成函数
                    $('#lore' + order).attr({'ifSystem': '1', 'lid': 'u' + id});
                    $('#lore' + order).find('.quesbody').html(data['Lore']);
                    if (data['Answer']) {
                        $('#lore' + order).find('.lAnswer').html(data['Answer']);
                    }
                    $('#lore' + order).find('.btnBgBox').attr({'ifSystem': '1', 'tid': 'u' + id});
                    //修改cookie
                    var fid = order.split('_')[0];
                    self.resetMenuCookie('tempHead' + fid, 'tempMenuBox', fid);
                    //组合cookie
                    self.joinCookie();
                }else{
                    var ifAnswer=0;
                    if(data['Answer']!=''){
                        ifAnswer=1;
                    }
                    var content={
                        'ifTest':0,
                        'ifAnswer':ifAnswer,
                        'childNum':1,
                        'testID':id,
                        'content':data['Lore'],
                        'answer':data['Answer'],
                        'system':1,
                        'order':order
                    };
                    self.addContentSure(content);
                }
            }else{
                $.myDialog.normalMsgBox('successDialog','结果', 500, '载入失败，请刷新后重试！' , 2);
            }
        });
    },
    /**
     * 显示试题
     * 参数1 arr 数据
     * 参数2 style 样式
     * 参数3 ifTest 是否是试题
     * 参数4 ifSystem 知识点系统还是个人
     * 返回 string
     */
    showTest:function(arr,style,ifTest,ifSystem){
        var output='';
        for(var i=0;i<arr.length;i++){
            if(arr[i]['testid']==null||arr[i]['testid']==''||arr[i]['testid']=='undefined'){
                continue;
            }
            var sty="";
            var classname='addquessel';
            var testInfo='quesbody';
            if(style==2){
                classname='addCase';
                testInfo='testInfo';
                sty='style="display:none;"';
                if($.caseCommon.ifAdd(arr[i]['testid'],ifTest)){
                    classname='delCase';
                }
            }else if(editData.ifhavetest(arr[i]['testid'])){
                classname='delques';
                sty='style="display:none;"';
            }
            output+='<div class="quesbox" id="quesbox'+arr[i]['testid']+'">';
            output+='<div class="quesbox_inner">'+
            '<div class="quesinfobox">';
            if(arr[i]['docname']) {
                output+='<div class="quesinfo_tit">标题/来源：<span class="questitle">' + arr[i]['docname'] + '</span></div>';
            }
            var str='题号：';
            if(ifTest==0){
                str='知识编号：';
            }
            output+='<div><table border="0" cellpadding="0" cellspacing="0">'+
            '<tbody><tr><td>'+str+arr[i]['testid'];
            if(arr[i]['typesname']!=undefined){
                output+='，题型：'+arr[i]['typesname'];
            }
            if(arr[i]['diffxing']!=undefined){
                output+='，难度：</td><td>'+arr[i]['diffxing']+'</td>';
            }else{
                output +='</td>';
            }
            output+='</tr></tbody>'+
            '</table></div>';
            output+='<div class="quesmenu">&nbsp;&nbsp;日期：'+arr[i]['firstloadtime']+'&nbsp;&nbsp;</div>';
            output+='</div>';

            output+='<div class="quesdiv" id="quesdiv'+arr[i]['testid']+'" onselectstart="return false;" oncopy="return false;" style="-moz-user-select:none;">'+
            '<div class="'+testInfo+'"><p>'+arr[i]['test']+'</p></div>';
            if(ifTest==0){
                if(arr[i]['Answer']=='undefined' || arr[i]['Answer']==''){
                    output += '</div>';
                }else {
                    output+='<div class="quesanswer" tid="'+arr[i]['testid']+'" show="1">'+
                    '<div class="quesanswer_tit">答案</div>' +
                    '<span class="loreAnswer">' + arr[i]['Answer'] + '</span>' +
                    '</div>' +
                    '</div>';
                }
            }else{
                output+='<div class="quesanswer" tid="'+arr[i]['testid']+'" show="0">' +
                '<p class="list_ts">' +
                '<span class="ico_dd">载入数据请稍候...</span>' +
                '</p>' +
                '</div>'+
                '</div>';
            }
            output+='<div class="quesinfobox">';
            var property='';
            if(ifTest==1){
                output += '<div class="quesother"><a id="fav' + arr[i]['testid'] + '" class="fav" title="收藏试题" thisquestitle="' + arr[i]['docname'] + '"/><a id="comment' + arr[i]['testid'] + '" class="comment" title="评价试题"/><a id="correction' + arr[i]['testid'] + '" class="correction" title="纠错试题"/></div>';
                property='" questitle="'+arr[i]['docname']+'" qyid="'+arr[i]['typesid']+'" qyname="'+arr[i]['typesname']+'" qyisselect="'+arr[i]['typesisselect']+'" qdid="'+arr[i]['diffid']+'" qdname="'+arr[i]['diffname'];
            }
            output+='<div class="quesmenu">';
            if(ifTest==0 && ifSystem==0){//编辑知识(仅个人知识)
                output+='<a id="" class="editLoreButton" href="javascript:"></a>';
            }
            output+='<a id="quesselect'+arr[i]['testid']+'" class="'+classname+'" quesid="'+arr[i]['testid']+'" childnum="'+arr[i]['testnum']+property+'"/></a><span class="selmore" childnum="'+arr[i]['testnum']+'" qyid="'+arr[i]['typesid']+'" id="selmore'+arr[i]['testid']+'" '+sty+' testid='+arr[i]['testid']+'></span><span class="selpicleft" id="selpicleft'+arr[i]['testid']+'"'+sty+'></span>';

            output+='</div>'+
            '</div>'+
            '<div class="quesparse"></div>';
            output+='</div>';
            output+='</div>';
        }
        return output;
    },
    //获取难度当前值
    getDiff:function(){
        return $('#quesdiffselect').getButtonSelected('qdid');
    },
    //获取排序当前值
    getOrder:function(){
        return $('#list_px').getButtonSelected('type');
    },
    //获取当前能力值
    getAbility:function(){
        return $('#quesabilityselect').getButtonSelected('qdid');
    },
    //获取题型当前值
    getTypes:function (){
        return $('#questypeselect').getButtonSelected('qdid');
    },
    //获取文档类型当前值
    getDocType:function(){
        return $('#quesdoctypeselect').getButtonSelected('qdid');
    },

    getKeywords : function(){
        return $('#keywords').val();
    },
    /**
     * ajax获取试题
     * @param useFunction 使用的函数
     * @param where 查找试题的条件
     * @param style 展示样式
     * @param page 当前页码
     * @param ifTest 是否是试题
     * @author demo
     */
    getTest:function(useFunction,where,style,page,ifTest){
        var self=this;
        if(useFunction.indexOf('/')===0){
            useFunction = useFunction.substring(1);
        }
        if(style==2 && page!=1){//瀑布式分页
            $("#pagelistbox").html('<p class="list_ts c"><span class="ico_dd">加载中请稍候...</span></p>');
        }else {
            $("#queslistbox").html('<p class="list_ts c"><span class="ico_dd">试题加载中请稍候...</span></p>');
        }
        lock=useFunction;
        $.post(U(useFunction),where,function(data){
            if($.myCommon.backLogin(data)==false){
                self.showPage(0,1,page,style);
                $("#queslistbox").html('<p class="list_ts pt50">抱歉！暂时没有符合条件的内容，请尝试更换查询条件。</p>');
                return false;
            }
            $('#addLoreDiv .nowPath').html('选题范围:');//重置导航文字
            if(typeof (data['data'])!='string' && data['data'][1]!=0) {
                if (style == 2 && page!=1) {
                    $("#queslistbox").append(self.showTest(data['data'][0], style,ifTest,where.ifSystem));
                } else {
                    $("#queslistbox").html(self.showTest(data['data'][0], style,ifTest,where.ifSystem));
                }
                self.showPage(data['data'][1],data['data'][2],page,style);
                if(ifTest==0){
                    $(".editLoreButton").on('click',function(){
                        var tid=self.clearIdLetter($(this).next().attr('quesid'));//试题id
                        self.editLore(tid,0);
                    });
                }
            }else{
                if(ifTest==0){//没有知识就添加
                    var sid=$('.testTab a.curt').attr('sid');
                    if(sid==1){//我的知识
                        $("#queslistbox").html('<div class="emptyLore">您还没有自己的知识点呢,赶快添加吧~</div>');
                    }else if(sid==0){//系统知识
                        $("#queslistbox").html('<div class="emptyLore">该章节暂时没有系统知识。</div>');
                    }
                    return false;
                }
                $("#queslistbox").html('<p class="list_ts pt50">抱歉！暂时没有符合条件的内容，请尝试更换查询条件。</p>');
                self.showPage(0,1,page,style);
            }
            lock='';
        });
    },
    //瀑布式分页效果
    showPage:function(total,prePage,page){
        var pageCount = Math.ceil(total / prePage);//总页数
        var lastPageList = '<div class="pagebox loadMore">';
        if(total<=10 ){
            lastPageList='';
        }else if(page==pageCount){//
            lastPageList='<div class="pageboxNo"><a page="'+page+'">加载完毕</a></div>';
        }else {
            lastPageList += '<a page="' + page + '">加载更多</a></div>';
        }
        $("#pagelistbox").html(lastPageList);
        $("#quescount").html(total);
    },
    //移动
    move:function(){
        var self=this;
        //栏目下移
        $('.menuDownMove').live('click',function(){
            self.removeDown('tempMenu','tempMenuBox','menuDownMove','menuUpMove',$(this));
        });
        //栏目上移
        $('.menuUpMove').live('click',function(){
            self.upMove('tempMenu','tempMenuBox','menuUpMove','menuDownMove',$(this));
        });
        //知识下移
        $('.loreDownMove').live('click',function(){
            self.removeDown('lore','lore','loreDownMove','loreUpMove',$(this));
        });
        //知识上移
        $('.loreUpMove').live('click',function(){
            self.upMove('lore','lore','loreUpMove','loreDownMove',$(this));
        });
    },
    //绑定组卷方式
    bindChooseType:function(){
        var self=this;
        //绑定组卷方式
        $('.choosetype').change(function(){
            var lastChapterID=$('.selectChapter').last().val();
            var choosetypeid=$(this).val();
            if(choosetypeid=="2"){
                tempcontent="";
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
                if(lastChapterID.indexOf('c')!=0){ //判断选择至最终章节
                    $('.selmb').show();
                    $('.shownone').show();
                    $('.selmb').html('<div style="color:#F53131;padding:5px;">章节请选择至最终章节！</div>');
                    return false;
                }
                var chapterID=self.getChapterLast().replace('c','');
                $('.mbsel').show();
                $('input[name="chooseattr"]').attr('disabled',true);
                $('input[name="chooseattr"]').each(function(){
                    if($(this).val()==defaultstyle){
                        $(this).attr('disabled',false);
                        $(this).attr('checked',true);
                    }
                });
                self.getTempArr(choosetypeid,chapterID,'',0,1);
            }
        });
    },
    //改变选中模板背景颜色
    changeTempBackColor:function(){
        $('.templatelist1').live('click',function(){
            $('.templatelist1').css({'border-color':'#ddd','background-color':'#fff','color':'#333'});
            $(this).css({'border-color':'#00a0e9','background-color':'#00a0e9','color':'#fff'});
        })
    },
    //保存模板
    saveTemp:function(){
        var adddiv='';
        var self=this;
        $('.addtpl').live('click',function(){
            if(self.ifHaveContent()){//如果没有内容提示
                if(!window.confirm('模板内容为空，确定要保存模板吗？')){
                    return false;
                }
            }
            var chapterArr=new Array();
            var tempName=self.tempContent['tempName'].replace('$!$','·');
            $.myDialog.normalMsgBox('tplloading','数据加载中请稍候...',450,'<span class="boxloading">模板加载中请稍候...</span>',4);
            $.post(U('Guide/Case/getTemplateList'),{'subjectID':Cookie.Get("SubjectId"),'chapterID':self.chapterID,'check':1,'times':Math.random()},function(data){
                $('#tplloading .tcClose').click();
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                var choosetype=$("input[name='choosetype']:checked").val();//我的模板
                var templatelistid='';
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
                    '<p>模板名称：<span class="boxlist_sel"><input type="text" name="tempname" value="'+tempName+'" class="tempname" size="43" functionName="saveSysTemplateList"></span></p>'+
                    '</div>'+
                    '<div class="templist">'+
                    '<p style="padding:14px 0;">模板存储：<label><input type="radio" name="creatorup" class="creatorup" value="1" '+ otherchecked+'>新模板 </label><label><input type="radio" name="creatorup" value="0" class="creatorup" function="saveTemp">对已有模板替换 </label>'+adddiv+
                    '</p>'+
                    '</div>'+
                    '<div '+style+' class="mytpl">'+
                    '<label class="typename" >我的模板：</label>'+
                    '<div class="showtypeval">'+
                    '<div class="selmb1">'+

                    '</div>'+
                    '</div>'+
                    '</div>';
                $.myDialog.normalMsgBox(idname,title,500,tmp_str,3);
            });
        });
    },
    //保存模板确定
    saveTempSure:function(){
        var self=this;
        $('#addtplmsg .normal_yes').live('click',function(){

            var ifNew=$('input[name="creatorup"]:checked').val();
            var tempName=$.trim($('input[name="tempname"]').val()).replace('·','$!$');
            self.tempContent['tempName']=tempName;
            var functionName='saveTemp';
            var templatelistid;
            // if($('.questest').length<1){  //模版中的试题数据不能为空
            // showmsg('模板数据不能为空!',1);
            // return false;
            // }
            if(tempName==""){
                $.myDialog.showMsg('模板名称不能为空!',1);
                return false;
            }
            if(tempName.length>=300){
                $.myDialog.showMsg('模板名称过长,限100字符!',1);
                return false;
            }
            if(ifNew=='0'){
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
            $('.tempName').html(self.tempContent['tempName'].replace('$!$','·'));
            $.caseCommon.tempContent['tempType']='4';
            if(ifNew=='1'){
                templatelistid='';
            }
            if(ifNew=='2'){
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
                $.caseCommon.tempContent['tempType']='3';
            }
            $.caseCommon.tempContent['tempName']=$.trim($('input[name="tempname"]').val()).replace('·','$!$');
            $.caseCommon.tempContent['tplID']=templatelistid;
            $.caseCommon.joinCookie();

            var Content=$.caseCommon.tempContent;
            $.post(U("Guide/Case/"+functionName),{'subjectID':this.subjectID,'content':Content,'cid':$.caseCommon.chapterID,'tName':$.trim($('input[name="tempname"]').val()),'tplID':templatelistid,'ifNew':ifNew,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                var msg=data['data'];
                $('#addtplmsg .tcClose').click();
                if(msg['status']=='1'){
                    //保存成功后，执行修改第一步数据内容
                    var lastChapterID=self.chapterID;
                    var tplID=self.tplID;
                    var tempType=self.tempType;
                    if(tempType!='2'){ //如果是自定义模版
                        $.caseCommon.getTempArr(tempType,lastChapterID,tplID,1);
                    }
                    $.myDialog.showMsg(msg['msg']); //成功提示
                }else{
                    $.myDialog.showMsg(msg['msg'],1); //错误提示
                }

            })
        })
    },
    //切换保存模板的类型
    changeSaveTempTyle:function(){
        var self=this;
        $('.creatorup').live('change',function(){
            var i=0;
            var templist='';
            var templatelistid='';
            var chapterID= $.caseCommon.chapterID;//getChapterLast().replace('c','');
            if($(this).val()=='0'){ //调取用户自己模版 即IfSystem=1
                var choosetype='4';
                var templatelistid='';
                $('.templatelist').each(function(){
                    if($(this).css('color')=='rgb(0, 160, 233)'|| $(this).css('color')=='#00a0e9'){
                         templatelistid=$(this).attr('mbid');
                    }
                });
                self.getTempArr(choosetype,chapterID,templatelistid,1,0);
                $('.mytpl').show();
                $.myDialog.tcDivPosition('addtplmsg');//层位置
            }else if($(this).val()=='2'){ //调取系统模版 即IfSystem=1
                $('.mytpl').show();
                $('.selmb1').html('<span class="boxloading">模板加载中请稍候...</span>');
                $.myDialog.tcDivPosition('addtplmsg');//层位置
                $.post(U("Guide/Case/getTemplateList"),{'subjectID':Cookie.Get("SubjectId"),'chapterID':chapterID,'choosetypeid':'3','times':Math.random()},function(data){
                    if($.myCommon.backLogin(data)==false){
                        $('.selmb1').html('<div style="color:#F53131;padding:5px;">'+data['data']+'</div>');
                        $.myDialog.tcDivPosition('addtplmsg');//层位置
                        return false;
                    };
                    if(data['data']['content']==null){
                        $('.selmb1').html('<span style="color:red">没有可替换系统模板！</span>');
                        return false;
                    }
                    for(i in data['data']['content']){
                        templist+='<div class="templatelist1 elli" mbid="'+data['data']['content'][i]['TplID']+'">'+data['data']['content'][i]['TempName']+'</div>';
                        $('.selmb1').html(templist);
                    }
                    $.myDialog.tcDivPosition('addtplmsg');//层位置
                });
            }else{
                $('.mytpl').hide();
            }
        })
    },
    /***************************************
     *第三界面
     **************************************/
    /**
     * 将分割后的cookie内容，试题ID替换为真实试题
     * @author demo
     */
    buildNewContent:function(content){
        var self=this;
        $("#dir3 .dir").html('<p class="list_ts"><span class="ico_dd">数据加载中请稍候...</span></div>');//等待提示
        if(content){
            $.post(U('Guide/Case/buildTestContent'),{'content':content,'ifShowNum':1},function(msg){
                //权限验证
                if($.myCommon.backLogin(msg)==false){
                    return false;
                }
                var data=msg['data'];
                if(data['status']=='success'){
                    var htmlContent=self.buildCaseHtml(data);
                    $("#dir3 .dir").html(htmlContent);
                }else{
                    $("#dir3 .dir").html(data); //数据为空时，没有查询到相关数据，返回提示内容
                }
            })
        }else{
            return '';
        }

    },
    /**
     * 将PHP处理的数据内容，转换成HTML页面
     * @author demo
     */
    buildCaseHtml:function(content){
        var i,j,k;
        var html='';
        html+='<div class="guide_preview">'+
        '<div class="tempname">'+content['tempName'].replace('$!$','·')+'</div>'+
        '<div class="tempdesc">'+content['tempDesc']+'</div>';
        for(i in content['forum']){
            html+='<div class="temp">'+
            '<div class="temphead">'+
            '<div class="tempforum">'+
            '<span>'+content['forum'][i][0]+'·'+content['forum'][i][1]+'</span>'+
            '</div>'+
            '</div>';

            for(j in content['forum'][i][2]){
                var testNum=1;
                html+='<div class="tempbox">'+
                '<div class="tempmemu">'+content['forum'][i][2][j]['menuName']+'</div>'+
                '<div class="lorecontent">';
                if(content['forum'][i][2][j]['testContent']==""){
                    if(content['forum'][i][2][j]['ifTest']==1){
                        content['forum'][i][2][j]['testContent']='试题内容为空！';
                    }else{
                        content['forum'][i][2][j]['testContent']='知识内容为空！';
                    }
                    html+='<div class="temptext">'+content['forum'][i][2][j]['testContent']+'</div>';
                }else {
                    if(content['forum'][i][2][j]['testContent']){
                        for (k in content['forum'][i][2][j]['testContent']){
                            if (content['forum'][i][2][j]['testContent'][k]['Lore'] && content['forum'][i][2][j]['testContent'][k]['Lore'] != '') {
                                if( menuSubject[this.subjectID]['o'+content['forum'][i][2][j]['menuID']]['NumStyle']=='1'){
                                    html += '<div class="temptext">' + content['forum'][i][2][j]['testContent'][k]['Lore'] + '</div>';
                                    testNum += parseInt(content['forum'][i][2][j]['testContent'][k]['testNum']);
                                }else{
                                    html += '<div class="temptext">' + content['forum'][i][2][j]['testContent'][k]['Lore'] + '</div>';
                                    testNum += parseInt(content['forum'][i][2][j]['testContent'][k]['testNum']);
                                }

                            } else {
                                if(content['forum'][i][2][j]['ifTest']==1){
                                    content['forum'][i][2][j]['testContent'][k]['Lore']='未查询到该试题！';
                                }else{
                                    content['forum'][i][2][j]['testContent'][k]['Lore']='未查询到该知识！';
                                }
                                html += '<div class="temptext">' + content['forum'][i][2][j]['testContent'][k]['Lore'] + '</div>';
                            }
                        }
                    }
                }

                html+=    '</div>';
                html+= '</div>';
            }
        }
        html+='</div>';
        return html;
    },
    // 发布导学案点击
    sendCaseWork:function(){
        var self=this;
        $('.sendWork').live('click',function(){
            var setName='';
            setName += '<div class="releasePlanMsgCon">您确定将该导学案发布？<br/><br/>导学案名称：<input type="text" class="setTplName" name="TplName" style="width:310px;height:20px;text-align:left" maxlength="200" value="'+self.tempContent['tempName'].replace('$!$','·')+'"></div>';
            $.myDialog.normalMsgBox('sendWork','发布导学案',450,setName,3);
        })
    },
    //导学案发布确定
    sendCaseWorkSure:function(){
        var self=this;
        $('#sendWork .normal_yes').live('click',function(){
            var data=new Array();
            var tplName=$('.setTplName').val(); //模板名称
            var chapterID=self.getChapterLast().replace('c',''); //最终章节
            if(!chapterID){
                chapterID=self.tempContent['chapterID'];
            }
            $.post(U('Guide/Case/addWorkTpl'),{'content':self.tempContent,'tplName':tplName,'ChapterID':chapterID},function(msg){
                //权限验证
                if($.myCommon.backLogin(msg)==false){
                    return false;
                }
                var data=msg['data'];
                if(data['status']=='success'){
                    $('#sendWork .tcClose').click();
                    $.myDialog.showMsg(data['msg']);
                }else{
                    $.myDialog.showMsg(data['msg']);
                }
            })

        })
    },
    // word 下载
    caseWordDown:function(){
        var self=this;
        $('#paperdownload').live('click',function(){
            var idname='downdiv';
            //显示选择项
            $.myDialog.tcLoadDiv("下载Word试卷",500,idname);

            //载入弹出框信息
            $.get(U('Home/Index/zjDown'),{'z':'1','t':'1','times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                var str_tmp='<input name="zjdown_type" id="zjdown_type" value="3" type="hidden"/>';
                $('#'+idname+' .content').html(data['data']+str_tmp);
                $.myDialog.tcShowDiv(idname);
                $('#div_shadow').css({'display':'block'});
            });
        });
    },
    //第三步跳转到第二步
    skipTwoFromThree:function(){
        var self=this;
        $('.Retostep2').live('click',function(){
            $('.step').css('display','none');
            $('#dir2').css({display:'block'});
            self.InitDivXdBox();
        });
    },
    /******************第三页面结束************************/

    /**************************
     * 导学案公共函数
     ***************************/

     /**
      * 检查cookie中是否含有当前试题
      * @param testID 当前试题ID
      * @param ifTest 当前ID是不是试题
      * @return bool
      * @author demo
      */
    checkOnlyTest:function(testID,ifTest){
         var self=this;
         var content=this.tempContent;
         var i, j, k;
         var testArr={};
         var testMsg={};
         var lastBack=true;
         var forum=content['forum'];
         if(forum){
             for(i in forum){
                 for(j in forum[i][2]){
                     if(forum[i][2][j]['ifTest']==ifTest){
                         if(forum[i][2][j]['testContent']){
                             testArr=forum[i][2][j]['testContent'].split(';');
                             for(k in testArr){
                                 testMsg=testArr[k].split('|');
                                 if(testMsg[0]==testID){
                                     lastBack=false;
                                 }
                             }
                         }
                     }
                 }
             }
         }

         return lastBack;

    }

};
