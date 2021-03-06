//组卷
jQuery.zujuan = {
    docWidth:550,
    //初始化
    init:function(){
        this.initDivBoxHeight(); //重置框架
        this.loadPaper(); //载入试卷
        this.loadEvent(); //载入事件
        this.setSeal(); //设置试卷版式左侧 竖型信息框
        //窗口改变大小事件
        $(window).resize(function(){$.zujuan.initDivBoxHeight();});
    },
    //载入事件
    loadEvent:function(){
        //载入左侧事件
        this.paperSettings();//标题操作事件
        this.paperReset();//重置 清空所有
        this.setPaperStructure();//试卷结构快捷设置
        this.leftIcoEvent();//左侧图标事件
        this.paperHeaderEvent();//试卷头事件
        this.leftTitleEvent();//左侧标题事件
        this.leftEditEvent();//左侧图标编辑
        this.leftTestClickEvent();//左侧试题点击事件
        //载入右侧事件
        this.rightTitleEditEvent();//右侧标题编辑
        this.rightTypeMouse();//右侧题型事件
        this.rightPartHeadEvent();//右侧分卷事件
        this.addTypesEvent();//设置新题型事件
        this.rightTestEvent();//右侧试题事件
        this.testDetailEvent();//试题详细事件
        this.testReplaceEvent();//替换框事件
        this.testDeleteEvent();//删除试题
        this.testMoveUp();//上移试题
        this.testMoveDown();//下移试题
        this.changeTestTitleEvent();//修改标题事件
        //载入弹出框事件
        this.rightMenuNav();//右侧导航菜单收起或展开事件
        this.downPaper();//下载试卷弹出框
        this.downAnswer();//答题卡弹出框
        this.paperAnalyze();//试卷分析
        this.paperSave();//试卷存档
        this.paperWork();//留作业
        this.paperRecycle();//回收站
        this.raidoChange();//标题事件 单选切换
        this.oneKeyReplaceTest();//一键更换试卷
        this.loadIconExam();//联考配置
    },
    //载入试卷结构
    loadPaper:function(){
        this.leftTopNav(); //左上侧快捷导航
        this.leftCenter(); //左侧结构
        this.loadPaperRight(); //载入右侧框架
        this.loadPaperCenter(); //载入试卷版式内容
        this.setzonghe(); //载入综合属性
        this.setStyle(0); //初始化版式
        this.initTest(); //载入试题
    },
    //设置试卷版式左侧 竖型信息框
    setSeal:function(){
        if ($.browser.msie && ($.browser.version == "9.0" || $.browser.version == "10.0") ) {
            $('#pui_seal').css({'filter':"progid:DXImageTransform.Microsoft.Matrix(sizingMethod='auto expand', M11=cos(90), M12=-sin(90), M21=sin(90), M22=cos(90))"});
        }
    },
    //重置框架
    initDivBoxHeight:function(){
        $('#leftdiv').css('height',$(window).height());
        $('#paperstruct_body').css('height',$(window).height()-60);
        $('#rightdiv').css('width',$(window).width()-$('#leftdiv').width());
        $('#rightdiv').css('height',$(window).height());
    },
    //获取以id为键值的题型数据
    getTypesByID:function(){
        var typesArr=new Array();
        for(var i in Types){
            for(var j in Types[i]){
                typesArr[Types[i][j]['TypesID']]=Types[i][j];
            }
        }
        return typesArr;
    },
    /**************************
     * 左侧结构
     **************************/
    //左上侧快捷导航
    leftTopNav:function(){
        $('#paperstruct_head').html('<a id="paperstruct_title">试卷结构</a><a id="papersettings">设置</a>&nbsp;(<a id="paperstylelist">&nbsp;快捷&nbsp;</a>)&nbsp;<a id="paperreset" title="组卷中心重新初始化">重置</a>');
        $('#paperstylelist').append('<div id="quickstyles" style="z-index:999;top: 35px; display: none;">'+
            '<a>默认样式</a>'+
            '<a>标准样式</a>'+
            '<a>测验样式</a>'+
            '<a>作业样式</a>'+
            '</div>');
    },
    //左侧结构
    leftCenter:function(){
        $('#paperstruct_body').html('<div id="s_paper" style="padding-bottom:50px;" onselectstart="return false"></div>');
        $('#s_paper').append('<div id="s_paperhead" style="margin-bottom:5px;"><strong>卷头</strong></div>');
        $('#s_paper').append('<div id="s_paperheaditem" style="padding-bottom:10px; border-bottom:#34414e solid 1px"></div>');

        this.initLeftHtml();

        //卷体
        $('#s_paper').append('<div id="s_paperbody" style="margin-bottom:0px;  border-top:#4d6175 solid 1px;padding-top:15px;"><div id="s_paperhead"><strong>卷体</strong></div></div>');

        this.initfenjuanHtml();
    },
    //初始化左侧html数据
    initLeftHtml:function(){
        var params1 = { code: "maintitle", title: "主标题", text: "文本" };
        var params2 = { code: "subtitle", title: "副标题", text: "文本" };
        var params3 = { code: "seal", title: "装订线" };
        var params4 = { code: "marktag", title: "保密标记", text: "文本" };
        var params5 = { code: "testinfo", title: "试卷信息栏", text: "文本" };
        var params6 = { code: "studentinput", title: "考生输入栏", text: "文本" };
        var params7 = { code: "score", title: "誊分栏" };
        var params8 = { code: "notice", title: "注意事项栏", note: "文本" };
        var html = this.getLeftBoxHtml(params1) + this.getLeftBoxHtml(params2) +
            this.getLeftBoxHtml(params3) + this.getLeftBoxHtml(params4) +
            this.getLeftBoxHtml(params5) + this.getLeftBoxHtml(params6) +
            this.getLeftBoxHtml(params7) + this.getLeftBoxHtml(params8);
        $("#s_paperheaditem").append(html);
    },
    //获取左侧标题每行的结构
    getLeftBoxHtml:function(params){
        var html = [];
        if (typeof params == "undefined") { return html.join(''); }
        if (typeof params.code == "undefined") { return html.join(''); }
        if (typeof params.title == "undefined") { return html.join(''); }
        html.push('<div id="s_'+params.code+'">');
        html.push('<a class="s_itemicon"/>');
        html.push('<a class="s_display_open"/>');
        html.push('<a class="s_edit" title="设置试卷'+params.title+'"/>');
        html.push('<a class="s_element">'+params.title+'</a>');
        html.push('</div>');
        return html.join('');
    },
    //左侧分卷
    initfenjuanHtml:function() {
        var params1 = { code: "1", title: "第I卷（选择题）", text: "文本" };
        var params2 = { code: "2", title: "第II卷（非选择题）", text: "文本" };
        var html = this.getfenjuanBoxHtml(params1) + this.getfenjuanBoxHtml(params2);
        $("#s_paperbody").append(html);
    },
    //左侧分卷每行的结构
    getfenjuanBoxHtml:function(params){
        var html = [];
        if (typeof params == "undefined") { return html.join(''); }
        if (typeof params.code == "undefined") { return html.join(''); }
        if (typeof params.title == "undefined") { return html.join(''); }

        html.push('<div class="s_paperpart" id="s_paperpart'+params.code+'">');
        html.push('<div class="s_parthead" id="s_parthead'+params.code+'">');
        html.push('<a class="s_itemicon"/>');
        html.push('<a class="s_display_open"/>');
        html.push('<a class="s_edit" title="设置分卷头部"/>');
        html.push('<span class="s_partname" id="s_partname'+params.code+'">'+params.title+'</span>');
        html.push('</div>');
        html.push('</div>');

        return html.join('');
    },
    /**************************
     * 右侧结构
     **************************/
    //载入右侧框架
    loadPaperRight:function(){
        //右侧内容
        $('#pui_root').append('<div id="pui_seal" title="装订线" style="cursor: pointer; background-position: 0% 0%; background-attachment: scroll; background-repeat: repeat; background-image: none; background-size: auto; color: rgb(0, 0, 0); background-origin: padding-box; background-clip: border-box; background-color: rgb(236, 236, 236);">'+
            '<div class="pui_sealline">…………○…………外…………○…………装…………○…………订…………○…………线…………○…………</div>'+
            '<div id="pui_sealinput">'+
            '<table width="100%" border="0" cellpadding="0" cellspacing="0">'+
            '<tbody>'+
            '<tr>'+
            '<td class="pui_sealblock" style="background-position: 0% 0%; background-attachment: scroll; background-repeat: repeat; background-image: none; background-size: auto; background-origin: padding-box; background-clip: border-box; background-color: rgb(153, 153, 153);"/>'+
            '<td align="center">'+
            '<div id="pui_sealinputtext">学校:___________姓名：___________班级：___________考号：___________</div>'+
            '</td>'+
            '<td class="pui_sealblock" style="background-position: 0% 0%; background-attachment: scroll; background-repeat: repeat; background-image: none; background-size: auto; background-origin: padding-box; background-clip: border-box; background-color: rgb(153, 153, 153);"/>'+
            '</tr>'+
            '</tbody>'+
            '</table>'+
            '</div>'+
            '<div class="pui_sealline">…………○…………内…………○…………装…………○…………订…………○…………线…………○…………</div>'+
            '</div>');
        $('#pui_root').append('<div id="pui_main"></div>');
        $('#pui_main').append('<div id="pui_head"></div>');
        $('#pui_head').append('<div id="iconExam" class="iconExamNo" title="联考标记"></div>');
        $('#pui_head').append('<div id="pui_marktag" title="保密标记"></div>');
        $('#pui_head').append('<div id="pui_title">'+
            '<div id="pui_maintitle" title="试卷主标题"></div>'+
            '<div id="pui_subtitle" title="试卷副标题"></div>'+
            '</div>');
        $('#pui_head').append('<div id="pui_testinfo" title="试卷信息栏"></div>');
        $('#pui_head').append('<div id="pui_studentinput" title="考生信息填写栏" style="display: none;"></div>');
        $('#pui_head').append('<div id="pui_score" title="试卷誊分栏" style="display:none;"></div>');
        $('#pui_head').append('<div id="pui_notice" title="考生注意事项栏" style="display:none;">'+
            '<div id="pui_noticetip">注意事项：</div>'+
            '<div id="pui_noticetext">1．答题前填写好自己的姓名、班级、考号等信息<br/>2．请将答案正确填写在答题卡上</div>'+
            '</div>');

        //分卷1
        $('#pui_main').append('<div id="pui_body"></div>');
        $('#pui_body').append('<div class="paperpart" id="paperpart1"></div>');
        $('#paperpart1').append('<div class="parthead" id="parthead1"></div>');
        $('#parthead1').append('<div class="partmenu" style="display: none;">'+
            '<a class="addquestype">添加新题型</a>'+
            '<a class="editpart">设置</a>'+
            '</div>');
        $('#parthead1').append('<div class="partheadbox" id="partheadbox1">'+
            '<div class="partname" id="partname1">第I卷（选择题）</div>'+
            '<div class="partnote" id="partnote1">请点击修改第I卷的文字说明</div>'+
            '</div><div class="effect" />');

        //分卷2
        $('#pui_body').append('<div class="paperpart" id="paperpart2"></div>');
        $('#paperpart2').append('<div class="parthead" id="parthead2"></div>');
        $('#parthead2').append('<div class="partmenu" style="display: none;">'+
            '<a class="addquestype">添加新题型</a>'+
            '<a class="editpart">设置</a>'+
            '</div>');
        $('#parthead2').append('<div class="partheadbox" id="partheadbox2">'+
            '<div class="partname" id="partname2">第II卷（非选择题）</div>'+
            '<div class="partnote" id="partnote2">请点击修改第II卷的文字说明</div>'+
            '</div><div class="effect" />');

        //右侧导航
        $('#rightmenu').html('<div>'+
            '<a id="paperdownload" title="下载word格式的试卷">下载</a>'+
            '</div>'+
            '<div id="othermenu">'+
            '    <a id="answersheet" title="生成答题卡">答题卡</a>'+
            '    <a id="paperanalyze" title="试卷分析">分析</a>'+
            '    <a id="papersave" title="保存当前试卷">存档</a>'+
            '    <a id="paperwork" class="td_btn" title="布置作业">布置作业</a>'+
            '    <a id="paperrecycle" title="试题回收">回收站</a>'+
            '    <a id="replace" title="一键更换试卷">批量替换</a>'+
            '</div>'+
            '<div>'+
            '    <a id="switchmenu" title="收起列表">收起</a>'+
            '</div>');
    },
    //获取分卷信息
    loadPaperCenter:function(){
        var juan=editData.getfenjuan(); //获取分卷信息
        if(!juan || juan==''){
            juan=new Array();
            juan[0]='';
            juan[1]='';
            juan[2]='';
            juan[3]='';
//            for(var ii in Types){
//                if(Types[ii]['Volume']==1){
//                    juan[0]+='^'+Types[ii]['TypesName'];
//                    juan[2]+='^'+Types[ii]['DScore']+'|'+Types[ii]['TypesScore']+'|'+Types[ii]['IfDo'];
//                }else if(Types[ii]['Volume']==2){
//                    juan[1]+='^'+Types[ii]['TypesName'];
//                    juan[3]+='^'+Types[ii]['DScore']+'|'+Types[ii]['TypesScore']+'|'+Types[ii]['IfDo'];
//                }
//            }
            for(var ii in juan){
                if(juan[ii]=="") juan[ii]=new Array();
                else juan[ii]=juan[ii].substring(1).split('^');
            }
        }
        var jfstrs='',jfstrx=''; //顶部打分栏 上 下
        var fj1='',fj2=''; //分卷1 2
        var j1=0,j2=0; //分卷循环 1 2

        $('#s_paperpart1').append('<div class="s_partbody" style="padding:5px;"></div>');
        //查找卷一题型并设置
        for(j1=0;j1<juan[0].length;j1++){
            $('#s_paperpart1 .s_partbody').append($.zujuan.getLeftTypeBoxHtml(1,j1+1,j1,juan[0][j1]));
            jfstrs+='<td>'+shuzi[j1]+'</td>';
            jfstrx+='<td>&nbsp;</td>';
            fj1+=$.zujuan.getRightTypeBoxHtml(1,j1+1,j1,juan[0][j1]);
        }

        $('#s_paperpart2').append('<div class="s_partbody"></div>');
        //查找卷二题型并设置
        for(j2=0;j2<juan[1].length;j2++){
            $('#s_paperpart2 .s_partbody').append($.zujuan.getLeftTypeBoxHtml(2,j2+1,j1+j2,juan[1][j2]));
            jfstrs+='<td>'+shuzi[j2+j1]+'</td>';
            jfstrx+='<td>&nbsp;</td>';
            fj2+=$.zujuan.getRightTypeBoxHtml(2,j2+1,j1+j2,juan[1][j2]);
        }

        var fenlan=[];
        fenlan.push('<table align="center" border="1" cellpadding="0" cellspacing="0" id="pui_scoretable" border="1" borderColor="#555">');
        fenlan.push('<tbody><tr>');
        fenlan.push('<td>题号</td>');
        fenlan.push(jfstrs);
        fenlan.push('<td>总分</td>');
        fenlan.push('</tr><tr><td>得分</td>');
        fenlan.push(jfstrx);
        fenlan.push('<td>&nbsp;</td>');
        fenlan.push('</tr></tbody></table>');
        $('#pui_score').html(fenlan.join(''));

        //分卷1题型和数据
        $('#paperpart1').append('<div class="partbody">'+fj1+'</div>');

        //分卷2题型和数据
        $('#paperpart2').append('<div class="partbody">'+fj2+'</div>');
    },
    /**
     * 左侧题型头部
     * 参数1 分卷数 1或2
     * 参数2 题型序号 从1开始
     * 参数3 题型汉字序号 顺延
     * 参数4 题型名称
     */
    getLeftTypeBoxHtml:function(juanNum,typeNum,totalNum,typeName){
        return '<div class="s_questype" id="s_questype'+juanNum+'_'+typeNum+'">'+
            '<div class="s_questypehead" id="s_questypehead'+juanNum+'_'+typeNum+'">'+
            '<a class="s_itemicon"/>'+
            '<a class="s_display_open"/>'+
            '<a class="s_edit" title="设置题型头部"/>'+
            '<span class="s_questypeindex" id="s_questypeindex'+juanNum+'_'+typeNum+'">'+
            '<b>'+shuzi[totalNum]+'、</b>'+
            '</span>'+
            '<span class="s_questypename" id="s_questypename'+juanNum+'_'+typeNum+'">'+typeName+'</span>'+
            '</div>'+
            '<div class="s_questypebody ui-sortable">'+
            '</div>';
    },
    /**
     * 右侧题型头部
     * 参数1 分卷数 1或2
     * 参数2 题型序号 从1开始
     * 参数3 题型汉字序号 顺延
     * 参数4 题型名称
     */
    getRightTypeBoxHtml:function(juanNum,typeNum,totalNum,typeName){
        return '<div class="questype" id="questype'+juanNum+'_'+typeNum+'">'+
            '<div class="questypehead" id="questypehead'+juanNum+'_'+typeNum+'">'+
            '<div class="questypemenu" style="display: none;">'+
            '<a class="editscore" style="opacity: 0.5;">改分</a>'+
            '<a class="empty" style="opacity: 0.5;">清空</a>'+
            '<a class="del">删除</a>'+
            '<a class="edit">设置</a>'+
            '<a class="moveup">上移</a>'+
            '<a class="movedn">下移</a>'+
            '</div>'+
            '<div class="questypeheadbox" id="questypeheadbox'+juanNum+'_'+typeNum+'">'+
            '<table border="0" width="100%" cellpadding="0" cellspacing="0">'+
            '<tbody><tr>'+
            '<td width="1"><div class="questypescore" id="questypescore'+juanNum+'_'+typeNum+'" style="width:120px;display:;">'+
            '<table title="评分栏" border="1" cellpadding="0" cellspacing="0" bordercolor="#666666"><tbody><tr>'+
            '<td width="55" height="20" align="center"> 评卷人 </td>'+
            '<td width="55" height="20" align="center"> 得  分 </td>'+
            '</tr><tr>'+
            '<td height="30" align="center">&nbsp;</td>'+
            '<td height="30" align="center">&nbsp;</td>'+
            '</tr></tbody></table>'+
            '</div></td><td>'+
            '<div class="questypetitle">'+
            '<span class="questypeindex"><b>'+shuzi[totalNum]+'、</b></span>'+
            '<span class="questypename" id="questypename'+juanNum+'_'+typeNum+'">'+typeName+'</span>'+
            '<span class="questypedscore" id="questypedscore'+juanNum+'_'+typeNum+'"></span>'+
            '<span class="questypenote" id="questypenote'+juanNum+'_'+typeNum+'">（题型注释）</span>'+
            '</div>'+
            '</td></tr></tbody></table>'+
            '</div>'+
            '</div>'+
            '<div class="questypebody">'+
            '</div>'+
            '</div>';
    },
    //存档类型显示
    updateExamSave:function(){
        var ifExam=editData.getAttr(2);
        if(ifExam>0 && $('#iconExam').attr('class')=='iconExamNo') {
            $('#iconExam').removeClass('iconExamNo').addClass('iconExam');
            // 联考配置流程
            jQuery.myRepeat.examProcess('#rightdiv',1);
        }
        if(ifExam==0 && $('#iconExam').attr('class')=='iconExam') {
            $('#iconExam').removeClass('iconExam').addClass('iconExamNo');
            $("#layer-progress").remove();
        }
    },
    //右侧联考试卷标记
    loadIconExam:function(){
        $('#iconExam').live('click',function(){
            //弹出框
            var idName='examDiv';
            var tmp_str='<div>'+
                '<table border="1" class="table f-roman" bordercolor="#ccc" width="100%" cellspacing="0" cellpadding="15"><tbody>'+
                '<tr><td align="center"><b>联考试卷</b></td>'+
                '<td><label><input name="IfExam" class="IfExam" value="1" type="radio" />标记</label> <label><input name="IfExam" class="IfExam" value="0" type="radio" />取消</label> </td></tr>'+
                '<tr class="trExam"><td align="center"><b>答题卡格式</b></td>'+
                '<td><label><input name="AnswerStyle" class="AnswerStyle" value="1" type="radio" />统一答题卡</label> <label><input name="AnswerStyle" class="AnswerStyle" value="2" type="radio" />ab卷</label> </td></tr>'+
                '<tr class="trExam"><td align="center"><b>存档类型</b></td>'+
                '<td><label><input name="IfSave" class="IfSave" value="0" type="radio" />新存档</label> <label class="oldSave"><input name="IfSave" class="IfSave" value="0" type="radio" />覆盖存档</label> </td></tr>'+
                '<tr><td align="center" colspan="2"><div id="showExaminfo"></div>'+
                '<input type="button" value="保存设置" id="examDivSubmit"/> '+
                '<input type="button" value="取消窗口" did="'+idName+'" class="tcClose"/>'+
                ' <a id="getSaveCode" href="javascript:;" class="f12">获取提取码</a>'+
                '</td></tr>'+
                '</tbody></table>'+
                '<table class="trExam table f-roman" width="100%" cellspacing="0" cellpadding="15"><tbody>'+
                '<tr><td align="center" colspan="2">'+
                '<span style="display:inline-block"><input type="button" value="下载试卷（默认）" id="examDownPaper"/><br/><a href="javascript:void(0);" id="examAutoPaper" class="f12">手动下载试卷</a></span> '+
                '<span style="display:inline-block"><input type="button" value="下载答题卡（默认）" id="examDownAnswer"/><br/><a href="javascript:void(0);" id="examAutoAnswer" class="f12">手动下载答题卡</a></span>'+
                '</td></tr>'+
                '</tbody></table>'+
                '</div>'+
                '<div id="explain">'+
                '<b style="color:#000;">什么是联考试卷？</b>'+
                '<hr style="border:0;border-bottom:1px solid #C1D3FB;"/>'+
                ' 联考试卷是将当前组卷中心内的试卷用来考试。 <br/>联考流程：组联考试卷 > 下载答题卡 > 考试中心配置考试信息 > 考试 > 扫描答题卡 > 阅卷 > 成绩分析。'+
                '</div>';
            $.myDialog.tcLoadDiv("设置联考试卷",400,idName);
            $('#'+idName+' .content').html(tmp_str);
            
            //初始化数据
            var answerStyle=editData.getAttr(2);
            var ifExam=0;
            if(answerStyle==1 || answerStyle==2) ifExam=1;
            else answerStyle=1;
            $('.IfExam[value="'+ifExam+'"]').attr('checked','checked');
            $('.AnswerStyle[value="'+answerStyle+'"]').attr('checked','checked');
            showExamSave();
            
            if(ifExam==0){ //隐藏更多选项
                showExamTr(0);
            }
            
            $.myDialog.tcShowDiv(idName);
            $('#div_shadow').css({'display':'block'});
        });
        
        //获取提取码跳转
        $('#getSaveCode').live('click',function(){
            var saveID=editData.getAttr(1);
            if(!saveID || saveID==0){
                alert('请保存试卷为联考试卷，然后再获取提取码');
                return false;
            }//获取提取码
            $.myRepeat.clipBoard(saveID);
        });
        
        //存档类型显示
        function showExamSave(){
            var saveID=editData.getAttr(1);
            if(!saveID || saveID==0){
                $('.IfSave:eq(0)').attr('checked','checked');
                $('.oldSave').hide();
            }else{
                $('.IfSave:eq(1)').val(saveID);
                $('.IfSave:eq(1)').attr('checked','checked');
                $('.oldSave').show();
            }
        }
        //联考属性显示
        function showExamTr(val){
            if(val==0){
                $('.trExam').hide();
            }else{
                $('.trExam').show();
            }
            $.myDialog.tcDivPosition('examDiv');
        }
        //保存为联考试卷
        $('#examDivSubmit').live('click',function(){
            //改变icon样式
            var ifExam=$('.IfExam:checked').val();
            var saveid=$('.IfSave:checked').val();
            var answerStyle=$('.AnswerStyle:checked').val();
            //判断是否已经存档
            if(!saveid){
                saveid=0;
            }
            
            if(ifExam==0){
                answerStyle=0;
                $.myDialog.tcCloseDiv('examDiv');
                editData.addAttr(saveid,answerStyle);
                $.zujuan.updateExamSave();
                return false;
            }
            
            //更新coookie
            //没有试题不能设置为联考试卷
            if($("div.quesdiv").length===0){
                alert('请添加试题，然后设置联考试卷。');
                return false;
            }
            editData.addAttr(saveid,answerStyle);
            //为试卷存档
            var papername=$('#pui_maintitle').html();
            var testList=editData.gettestid();
            
            $.myDialog.normalMsgBox('tplloading','提示信息',450,'数据保存中请稍候...',5);
            $.post(U('Index/savePaper'),{'saveid':saveid,'papername':papername,'paperpwd':'','data':editData.getall(),'testlist':testList,'times':Math.random()},function(data){
                $('#tplloading .tcClose').click();
                if($.myCommon.backLogin(data)==false){
                    $('#showExaminfo').html('<span class="hfont">'+data.data+'</span>');
                    return false;
                }
                $.myDialog.showMsg(' 存档成功 ');
                $('#showExaminfo').html('');
                editData.editAttr(data.data,1);
                showExamSave();
            });
            $.zujuan.updateExamSave();
        });
        //切换联考属性
        $('.IfExam').live('change',function(){
            if($(this).val()==0){
                showExamTr(0);
                return;
            }
            showExamTr(1);
        });
        //下载默认联考试卷
        $('#examDownPaper').live('click',function(){
            //输入验证码
            //弹出框
            var idName='verifyDiv';
            var tmpStr='<div class="verify c"><div class="verifyInput">'+
                            '<div>'+
                                '<b>验证码</b>'+
                                '<input id="verifyCode" type="text" style="width:50px;padding:4px;font-size:14px;font-weight:bold;margin-left: 8px;" maxlength="4"/>'+
                            '</div>'+
                            '<div class="verifyImgbox">'+
                                '<img id="verifyImg" height="30" title="点击刷新" url="'+U('Home/Index/verify')+'" src="'+U('Home/Index/verify')+'" alt="验证码" style="cursor:pointer;"/>'+
                            '</div>'+
                            '<div style="color:#777;font-size:11px;margin-left:5px;line-height:1.2">不区分<br /> 大小写</div>'+
                            '</div>'+
                            '<div id="koudiantishi"></div>'+
                        '</div>';
            $.myDialog.normalMsgBox(idName,"下载默认试卷",400,tmpStr,3);
        });
        
        //下载试卷验证码确认
        $('#verifyDiv .normal_yes').live('click',function(){
            var downType='2'; //教师组卷下载
            var result=$.workDown.getSubmitData(downType);
            if(result===false){
                return false;
            }

            var data=result[0];
            var url=result[1];
            var addCookie=result[2];

            var issaverecord = 1;
            var ifShare = 0;
            var docversion = '.docx';
            var papersize = 'A3H';
            var papertype = 'student';

            data.push('"issaverecord":'+issaverecord);
            data.push('"ifShare":'+ifShare);
            data.push('"docversion":"'+docversion+'"');
            data.push('"papersize":"'+papersize+'"');
            data.push('"papertype":"'+papertype+'"');
            data.push('"key":"'+key+'"');
            data.push('"times":"'+Math.random()+'"');
            var tmp_data=eval('({'+data.join(',')+'})');

            //加入cookiejson
            if(addCookie==1) tmp_data["cookiestr"]=$.caseCommon.tempContent;

            $.workDown.submitData(url,tmp_data);
        });
        
        //下载默认联考试卷答题卡
        $('#examDownAnswer').live('click',function(){
            //判断是否是联考数据
            var style=editData.getAttr(2);
            if(typeof(style)=='undefined' || style=='' || style=='0'){
                $.myDialog.showMsg('请设置当前试卷为联考试卷，并保存设置后操作；');
                return false;
            }
            
            //弹出提示框
             //弹出框
            var idName='answerDiv';
            var tmpStr='<div class="verify c">'+
                            '<div id="dtktishi"></div>'+
                        '</div>';
            
            //如果是ab卷则弹出选择框 选择a卷或b卷
            if(style=='2'){
                $.myDialog.normalMsgBox(idName,"下载默认答题卡",400,tmpStr,5);
                $('#dtktishi').before('<div class="paperStyleAnswer">'+
                    '<span class="paperStyleBtn bgbt an02" aid="a"><span class="an_left"></span><a>a卷</a><span class="an_right"></span></span> '+
                    '<span class="paperStyleBtn bgbt an02" aid="b"><span class="an_left"></span><a>b卷</a><span class="an_right"></span></span>'+
                '</div>');
            }else{
                $.myDialog.normalMsgBox(idName,"下载默认答题卡",400,tmpStr,3);
                $.zujuan.answerDown();
            }
        });
        //下载ab卷
        $('.paperStyleBtn').live('click',function(){
            var style=$(this).attr('aid');
            $('.paperStyleBtn').removeClass('an01').addClass('an02');
            $(this).removeClass('an02').addClass('an01');
            $.zujuan.answerDown(style);
        });
        //手动下载联考试卷
        $('#examAutoPaper').live('click',function(){
            $('#paperdownload').click();
        });
        
        //手动下载联考试卷答题卡
        $('#examAutoAnswer').live('click',function(){
            $('#answersheet').click();
        });
        
    },
    //右侧导航菜单收起或展开事件
    rightMenuNav:function(){
        //收起
        $('#switchmenu').live('click',function(){
            if($('#othermenu').height()>0){
                $('#othermenu').animate({"height":"0px"},"slow",function(){
                    $('#othermenu').css('display','none');
                    $('#switchmenu').css({'background-position':'17px -218px'});
                    $('#switchmenu').attr('title','展开列表');
                    $('#switchmenu').html('展开');
                });
            }else{
                $('#othermenu').css('display','block');
                $('#othermenu').animate({"height":"360px"},"slow",function(){
                    $('#switchmenu').css({'background-position':'-40px -218px'});
                    $('#switchmenu').attr('title','收起列表');
                    $('#switchmenu').html('收起');
                });
            }
        });
    },
    //下载试卷弹出框
    downPaper:function(){
        $('#paperdownload').live('click',function(){
            var idName='downdiv';
            winHeight = $(window).height();
            //显示选择项
            $.myDialog.tcLoadDiv("下载Word试卷",502,idName);
            //载入弹出框信息
            $.get(U('Home/Index/zjDown'),{'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                var str_tmp='<input name="zjdown_type" id="zjdown_type" value="2" type="hidden"/>';
                $('#'+idName+' .content').html(data['data']+str_tmp);
                $.myDialog.tcShowDiv(idName);
                $('#div_shadow').css({'display':'block'});
                
                //如果联考试卷则不分享
                var ifExam=editData.getAttr(1);
                if(ifExam>0){
                    $('#shareto').attr('checked',false);
                    $('#shareto').parent().hide();
                }

                // 浏览器窗口小于768时，重置弹出框高度
                if(winHeight<540){
                    $("#downdiv .content").css("padding","0 10px");
                    $("#downdiv .content .table").css("margin","3px");
                    $("#downdiv .table td").css("padding","2px");
                    $("#downdiv .content .verify").css({"padding-top":"2px","margin-bottom":"2px"});
                }
            });
        });
    },
    //答题卡弹出框
    downAnswer:function(){
        $('#answersheet').live('click',function(){
            var idName='answerdiv';
            //显示选择项
            $.myDialog.tcLoadDiv("生成答题卡",420,idName);
            var tmp_str='<div style="margin-left:0px;margin-top:5px;">'+
                '<table style="margin:0 auto;" border="0" cellpadding="0" cellspacing="0"><tbody>'+
                '<tr><td align="right"><b>答题卡类型：</b></td>'+
                '<td><label><input class="sheettype" name="sheettype" checked="checked" type="radio" value="0">普通用卡</label>'+
                '<label><input class="sheettype" name="sheettype" type="radio" value="1">联考用卡</label>'+
                '</td></tr>'+
                '<tr><td colspan="2" style="height:5px;"></td></tr>'+
                '<tr><td colspan="2" style="height:1px;background:#ccc;line-height:1px;"></td></tr>'+
                '<tr><td colspan="2" style="height:5px;"></td></tr>'+
                '<tr class="sheetStyleTr none"><td align="right"><b>答题卡格式：</b></td>'+
                '<td><label><input class="sheetstyle" name="sheetstyle" checked="checked" type="radio" value="0">a卷</label>'+
                '<label><input class="sheetstyle" name="sheetstyle" type="radio" value="1">b卷</label>'+
                '</td></tr>'+
                '<tr><td colspan="2" style="height:5px;"></td></tr>'+
                '<tr><td colspan="2" style="height:1px;background:#ccc;line-height:1px;"></td></tr>'+
                '<tr><td colspan="2" style="height:5px;"></td></tr>'+
                '<tr><td align="right"><b>答题卡样式：</b></td>'+
                '<td><select id="sheetchoice">'+
                '<option value="1">普通表格型</option>'+
                '<option value="2">标准题卡型</option>'+
                '<option value="3">选择密集型</option>'+
                '</select></td></tr>'+
                '<tr><td></td>'+
                '<td>'+
                '<div id="sheetviewbox">'+
                '<div id="sheetview" class="sheet1"></div>'+
                '<div id="icon"></div>'+
                '</div>'+
                '</td>'+
                '</tr>'+
                '<tr><td colspan="2" style="height:5px;"></td></tr>'+
                '<tr><td colspan="2" style="height:1px;background:#ccc;line-height:1px;"></td></tr>'+
                '<tr><td colspan="2" style="height:5px;"></td></tr>'+
                '<tr><td align="right"><b id="editionName">版本选择：</b></td>'+
                '<td id="editionContent">'+
                '<span class="checkspan"><input type="radio" name="docversion" class="docversion" value="docx" checked="checked"/>Word 2007/2010，文件扩展名为<span class="red">docx</span></span>'+
                '<br/>'+
                '<span class="checkspan"><input type="radio" name="docversion" class="docversion" value="doc"/>Word 2000/2003，文件扩展名为<span class="red">doc</span></span>'+
                '<br/>'+
                '</td>'+
                '</tr>'+
                '<tr><td colspan="2" style="height:5px;"></td></tr>'+
                '<tr><td colspan="2" style="height:1px;background:#ccc;line-height:1px;"></td></tr>'+
                '<tr><td colspan="2" style="height:5px;"></td></tr>'+
                '<tr><td id="dtktishi" colspan="2" style="height:5px;"></td></tr>'+
                '<tr>'+
                '<td colspan="2" align="center">'+
                '<div style="position:relative;" id="buttonbox">'+
                '<input type="button" value="配置答题卡" id="answercfg" style="display:none;height:25px;"/>'+
                '<input type="button" value="下载答题卡" id="answerbtn" style="height:25px;"/>'+
                '<input type="button" value="关闭窗口" did="'+idName+'" class="tcClose" style="height:25px;"/>'+
                '</div>'+
                '</td>'+
                '</tr>'+
                '</tbody></table>'+
                '</div><div>&nbsp;</div>';

            $('#'+idName+' .content').html(tmp_str);
            $("#sheetview").removeClass().addClass("sheet1");

            $.myDialog.tcShowDiv(idName);
            $('#div_shadow').css({'display':'block'});
            
            //如果是联考试卷优先出现联考答题卡
            var ifExam=editData.getAttr(1);
            if(ifExam>0){
                $('.sheettype:eq(1)').click();
                $.zujuan.loadSheetStyle();
            }
            //如果是ab卷显示ab卷选项卡
            $.zujuan.showABJuan();
            
        });
        //答题卡类型切换
        $('.sheettype').live('click',function() {
            $.zujuan.loadSheetStyle();
        });
        //答题模板改变
        $("#sheetchoice").live('change',function() {
            this.blur();
            var value = this.value;
            $("#sheetview").removeClass().addClass("sheet" + value);
        });
        //配置答题卡
        $('#answercfg').live('click',function(){
            if(!editData.getAttr(1)){
                $.myDialog.showMsg("请先保存为联考试卷！",1);
                return;
            }
            $.myCommon.go(U('Index/answer'));
        });
        //生成答题卡
        $("#answerbtn").live('click',function() {
            $.zujuan.answerDown();
        });
    },
    showABJuan:function(){
        var style=editData.getAttr(2);
        if(style=='2' && $('.sheettype:checked').val()=='1'){
            $('.sheetStyleTr').removeClass('none');
        }else{
            $('.sheetStyleTr').addClass('none');
        }
    },
    //下载答题卡
    answerDown:function(style){
        if($('#dtktishi').html().indexOf('正在生成请稍候')!=-1){
            return;
        }
        $('#dtktishi').html('<p class="list_ts"><span class="ico_dd">正在生成请稍候...</span></p>');
        var quescount = $("div.quesdiv").length;
        if (quescount == 0) {
            $.myDialog.showMsg("当前没有试题，请先手动或智能挑选试题。",1);
            $.myDialog.tcCloseDiv('answerdiv');
            return;
        }
        
        if(typeof(style)==='undefined' || style==='') style=0;
        var dataJson=$.zujuan.getAnswerParam(style);
        
        $.post(U('Index/arswerDown'),dataJson,function(data){//验证权限
            if($.myCommon.backLogin(data)==false){
                $.myDialog.tcCloseDiv('answerdiv');
                return false;
            }
            if($('#hiddenfrm').length==0){
                $("body").append("<iframe id='hiddenfrm' name='hiddenfrm' border='0' width='0' height='0'></iframe>");
                $("body").append("<div id='hiddendiv'></div>");
            }
            $('#hiddendiv').html(data.data);
        });
    },
    //载入答题卡下载类型
    getAnswerParam:function(style){
        var sheettype =1;
        var sheetxml = editData.getall();
        var sheetinput ='A3';
        var cursubject=$('#cursubject',window.parent.document);
        var thisLayout=cursubject.attr('layout');
        if(typeof(thisLayout)!='undefined' && thisLayout==='A4'){
            sheetinput=thisLayout;
        }
        var docversion ='pdf';
        var isie=($.browser.msie ? "true" : "false");
        if($('.sheettype').length>0){
            var sheettype = $('.sheettype:checked').val();
            var sheetinput = $("#sheetchoice").val();
            var docversion = $(".docversion:checked").val();
        }
        if(typeof(sheetinput)=='undefined' || sheetinput=='') sheetinput='A3';
        
        if(typeof(style)==='undefined' || style==='') style=0;
        
        return {'style':style,'sheettype':sheettype,'sheetxml':sheetxml,'sheetinput':sheetinput,'docversion':docversion,'isie':isie,'times':Math.random()};
    },
    //载入答题卡下载类型
    loadSheetStyle:function(){
            var sheetType=$('.sheettype:checked').val();
            switch(sheetType){
                case '0':
                    $('#sheetchoice').html('<option value="1">普通表格型</option>'+
                    '<option value="2">标准题卡型</option>'+
                    '<option value="3">选择密集型</option>');
                    $('#editionContent').html('<span class="checkspan"><input type="radio" name="docversion" class="docversion" value="docx" checked="checked"/>Word 2007/2010，文件扩展名为<span class="red">docx</span></span>'+
                        '<br/>'+
                        '<span class="checkspan"><input type="radio" name="docversion" class="docversion" value="doc"/>Word 2000/2003，文件扩展名为<span class="red">doc</span></span>'+
                        '<br/>');
                    $("#sheetview").removeClass().addClass("sheet1");
                    $('#answercfg').hide();
                    break;
                case '1':
                    $('#sheetchoice').html('<option value="a3">A3版式</option>'+
                    '<option value="a4">A4版式</option>');
                    $('#editionContent').html('<span class="checkspan"><input type="radio" name="docversion" class="docversion" value="pdf" checked="checked"/>PDF文档，文件扩展名为<span class="red">pdf</span></span>'+
                        '<br/>');
                    $("#sheetview").removeClass().addClass("sheeta3");
                    $('#answercfg').show();
                    break;
            }
            $.zujuan.showABJuan();
    },
    //试卷分析
    paperAnalyze:function(){
        $('#paperanalyze').live('click',function(){
            tmp_sum=$('.quesindex').length; //试题总数
            if(!tmp_sum){
                $.myDialog.showMsg("当前没有试题，请先手动或智能挑选试题。",1);
                $.myDialog.tcCloseDiv('analyticdiv');
                return;
            }
            var idName='analyticdiv';
            var tmp_str_1,tmp_str_2,tmp_sum,tmp_sum_i;
            //显示选择项
            $.myDialog.tcLoadDiv("试卷分析",712,idName);
            var tmp_str='<div id="analydiv" class="analydiv">'+
                '<div style="font-weight:bold;text-align:center;margin-bottom:15px;padding-top:5px;line-height:28px;">'+
                '<div style="font-size: 16px; display: block;">'+$('#pui_maintitle').html()+'</div>'+
                '<div style="font-size: 16px; display: block;">'+$('#pui_subtitle').html()+'</div>'+
                '</div>'+
                '<div style="margin-bottom:10px;">'+
                '<table width="668" border="0" cellpadding="0" cellspacing="0">'+
                '<tr><td width="330" valign="top"><table border="1" style="border:1px solid #dddddd;" width="330" cellpadding="0" cellspacing="0">'+
                '<tr style="background:#f1f1f1;font-size:15px;line-height:30px;font-weight:bold;">'+
                '<td style="border:0;padding:3px;" width="50%">按题型统计（总题数：<span id="quescount" class="blue">？</span>题）</td></tr>'+
                '<tr><td style="border:0;padding:3px;" valign="top"><div id="qylayout">正在分析请稍候...</div></td> </tr>'+
                '</table></td>'+
                '<td width="330" valign="top"><table border="1" style="margin-left:7px;border:1px solid #dddddd;" width="330" cellpadding="0" cellspacing="0">'+
                '<tr style="background:#f1f1f1;font-size:15px;line-height:30px;font-weight:bold;">'+
                '<td style="border:0;padding:3px;">按难易统计（总体难度：<span id="quesdiffavg" class="blue">？</span>）</td>'+
                '</tr>'+
                '<tr><td style="border:0;padding:3px; padding:3px" valign="top"><div id="difflayout" >正在分析请稍候...</div></td></tr>'+
                '</table></td></tr></table>'+
                '</div>';
            $('#'+idName+' .content').html(tmp_str);
            $.myDialog.tcShowDiv(idName);
            $('#div_shadow').css({'display':'block'});

            //分析试卷
            tmp_str='';
            tmp_str_1='';
            tmp_sum=0; //试题总数
            $.post(U('Home/Index/analytic'),{'data':editData.getall(),'times':Math.random(),'subjectid':subjectID},function(data){
                if($.myCommon.backLogin(data)==false){
                    var tmpstr='<div id="analydiv" class="analydiv"><img src=/Public/default/image/fxmodel.png></div>';
                    $('#'+idName+' .content').html(tmpstr);
                    $.myDialog.tcShowDiv(idName);
                    return false;
                }
                for(var tmp_i in data['data'][1]){
                    tmp_str_1+='<div style="height:20px;line-height:20px;border-bottom:1px solid #efefef;">';
                    tmp_str_1+='<span style="display:inline-block;width:120px;overflow:hidden;text-align:left;" title="'+data['data'][1][tmp_i][3]+'">'+data['data'][1][tmp_i][0]+'</span>';
                    tmp_str_1+='<span style="display:inline-block;width:45px;overflow:hidden;text-align:right;">'+data['data'][1][tmp_i][1]+'题</span>';
                    tmp_str_1+='<span style="display:inline-block;width:70px;overflow:hidden;">'+data['data'][1][tmp_i][2]+'</span>';
                    tmp_str_1+='</div>';
                    tmp_sum+=data['data'][1][tmp_i][1];
                }

                $('#s_paperbody .s_questype').each(function(){
                    tmp_str+='<div style="height:20px;line-height:20px;border-bottom:1px solid #efefef;">';
                    tmp_str+='<span style="display:inline-block;width:120px;height:22px;overflow:hidden;">'+$(this).find('.s_questypename').html()+'</span>';
                    var tmpBottomTestNum=$('#'+$(this).attr('id').replace('s_','')).find('.quesindex').length;
                    var tmpTopTestNum=$('#'+$(this).attr('id').replace('s_','')).find('.quesindexnum').length;
                    if(typeof(tmpBottomTestNum)=='undefined') tmpBottomTestNum=0;
                    if(typeof(tmpTopTestNum)=='undefined') tmpTopTestNum=0;
                    if(tmpTopTestNum!=0 && tmpBottomTestNum!=0) tmpTopTestNum=0;
                    tmp_sum_i=tmpTopTestNum+tmpBottomTestNum;
                    tmp_str+='<span style="display:inline-block;width:45px;overflow:hidden;text-align:right;">'+tmp_sum_i+'题</span>';
                    tmp_str+='<span style="display:inline-block;width:70px;overflow:hidden;">'+ (tmp_sum_i!=0 ? '('+(parseInt(tmp_sum_i)/parseInt(tmp_sum)*100).toFixed(1)+'%)' : '' )+'</span>';
                    tmp_str+='</div>';
                });

                var tmp_i,tmp_j;
                tmp_str_2='<div id="quesListbox">'+
                    '<table border="0" style="border:1px solid #dddddd;" width="668px" cellpadding="0" cellspacing="0">'+
                    '<tbody>'+
                    '<tr style="background:#f1f1f1;font-size:15px;line-height:30px;font-weight:bold;">'+
                    '<td align="center">序号</td>'+
                    '<td align="center">题型及难度</td>'+
                    '<td>标题及知识点分类</td>'+
                    '</tr>';
                for(tmp_i in data['data'][2]){
                    var thisTitle=data['data'][2][tmp_i][5];
                    if(thisTitle=='' || thisTitle==null || thisTitle=='null') thisTitle='个人试题';

                    tmp_str_2+='<tr title="题号：'+data['data'][2][tmp_i][0]+'|题型：'+data['data'][2][tmp_i][2]+'|难度：'+data['data'][2][tmp_i][3]+'">'+
                        '<td align="center" width="40" style="line-height:24px;padding:5px 0px">'+data['data'][2][tmp_i][1]+'</td>'+
                        '<td align="center" width="80" style="line-height:24px;padding:5px 0px">'+
                        '<span style="font-size:12px;">'+data['data'][2][tmp_i][2]+'</span><br/>'+data['data'][2][tmp_i][7]+
                        '</td>'+
                        '<td style="line-height:24px;padding:5px 0px">'+
                        '<div><b>'+thisTitle+'</b></div>';
                    for(tmp_j in data['data'][2][tmp_i][6]){
                        tmp_str_2+='<div>'+data['data'][2][tmp_i][6][tmp_j]+'</div>';
                    }
                    tmp_str_2+='</td></tr>'+
                        '<tr>'+
                        '<td colspan="3" style="background:#efefef;line-height:1px;height:1px;padding:0;"></td>'+
                        '</tr>';
                }
                tmp_str_2+='</tbody></table></div>';
                $('#quesdiffavg').html(data['data'][0]);
                $('#quescount').html(tmp_sum);
                $('#qylayout').html(tmp_str);
                $('#difflayout').html(tmp_str_1);
                $('#analydiv').append(tmp_str_2);
            });
        });
    },
    //试卷存档
    paperSave:function(){
        $('#papersave').live('click',function(){
            var idName='savediv';
            var tmp_str='<div style="margin-bottom:20px;">'+
                '<table border="0"><tbody>'+
                '<tr><td align="center"><b>试卷存档名称：</b></td></tr>'+
                '<tr><td>'+
                '<input type="text" id="savepapername" value="'+$('#pui_maintitle').html()+'" style="width:380px;padding:5px 0;color:#555;text-align:center;"/>'+
                '</td></tr>'+
                '<tr><td align="center">'+
                '<table border="0" cellpadding="0" cellspacing="0"><tbody>'+
                '<tr>'+
                '<td><input type="checkbox" id="saveshowkey"/></td>'+
                '<td><span style="font-size:12px;color:#000;">设置存档密码</span></td>'+
                '<td>'+
                '<div id="savekeybox" style="display:none;padding-left:3px;">'+
                '<span class="paperkeyicon"/>'+
                '<input type="text" id="savekey" style="width:120px;border:1px solid #000;color:#f00;" maxlength="20"/>'+
                '</div>'+
                '</td>'+
                '</tr></tbody></table>'+
                '</td></tr>'+
                '<tr><td><div style="display:inline-block;width:100%;height:1px;border-top:1px solid #ccc;"/></td></tr>'+
                '<tr><td align="center"><div id="showsaveinfo"></div>'+
                '<input type="button" value="确定存档" id="savesubmit"/>'+
                '<input type="button" value="取消窗口" did="'+idName+'" class="tcClose"/>'+
                '</td></tr>'+
                '</tbody></table>'+
                '</div>'+
                '<div id="explain">'+
                '<b style="color:#000;">什么是试卷存档？</b>'+
                '<hr style="border:0;border-bottom:1px solid #C1D3FB;"/>'+
                ' 试卷存档是将当前组卷中心内的试卷保存起来， 以后可以重新恢复到组卷中心继续编辑修改出题组卷。 存档的试卷可以在【组卷历史存档】中找到。'+
                '</div>';
            $.myDialog.tcLoadDiv("试卷存档",400,idName);
            $('#'+idName+' .content').html(tmp_str);
            $.myDialog.tcShowDiv(idName);
            $('#div_shadow').css({'display':'block'});
            $('#savepapername').focus();
        });
        //试卷存档密码
        $('#saveshowkey').live('click',function(){
            if($(this).attr('checked')=='checked'){
                $('#savekeybox').css('display','block');
                $('#savekey').focus();
            }else{
                $('#savekeybox').css('display','none');
                $('#savekey').val('');
            }
        });
        //存档确认
        $('#savesubmit').live('click',function(){
            if($('#showsaveinfo').html().indexOf('正在存档请稍候')!=-1){
                return;
            }
            $('#showsaveinfo').html('<p class="list_ts"><span class="ico_dd">正在存档请稍候...</span></p>');
            var papername=$.trim($('#savepapername').val());
            if(papername.length==0){
                $('#showsaveinfo').html('<span class="hfont">请填写存档名。</span>');
                $('#savepapername').focus();
                return;
            }
            if(papername.length>60){
                $('#showsaveinfo').html('<span class="hfont">文档名名过长，限60字符。</span>');
                $('#savepapername').focus();
                return;
            }
            var testList=editData.gettestid();
            if(!testList){
                $.myDialog.showMsg('没有试题，请选择试题！',1);
                $(this).next().click();
                return;
            }
            var paperpwd=$('#savekey').val();
            $.post(U('Index/savePaper'),{'papername':papername,'paperpwd':paperpwd,'data':editData.getall(),'testlist':testList,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    $('#showsaveinfo').html('<span class="hfont">'+data.data+'</span>');
                    return false;
                }
                $.myDialog.showMsg(' 存档成功 ');
                $('#showsaveinfo').html('');
                $('#savesubmit').next().click();
            });
        });
    },
    //留作业
    paperWork:function(){
        $('#paperwork').live('click',function(){
            var idName='zuJuanAddWork';
            var tmp_str='<div style="margin-bottom:20px;">'+
                '<div class="normal_btn">' +
                '<span class="normal_yes bgbt an01" id="addWorkToSave"><span class="an_left"></span><a>仅保存作业</a><span class="an_right"></span></span>' +
                '<span class="normal_no bgbt an02" id="addWorkToStu"><span class="an_left"></span><a>布置作业给学生</a><span class="an_right"></span></span>' +
                '</div>';
            $.myDialog.normalMsgBox(idName,"留作业选择",400,tmp_str);
        });

        //布置作业到学生
        $('#addWorkToStu').live('click',function(){
            $('#zuJuanAddWork .tcClose').click(); //关闭之前的窗口
            var testList=editData.gettestid();
            if(!testList){
                $.myDialog.showMsg("当前没有试题，请先手动或智能挑选试题。",1);
                return;
            }
            var cookieData=editData.getall();
            var myDate = new Date();
            var sname=$('#cursubject',window.parent.document).html();
            var workName=myDate.getFullYear()+'年'+(myDate.getMonth()+1)+'月'+myDate.getDate()+'日'+sname+'作业';
            $.myWorkCommon.addWorkPaperAction(workName,testList,cookieData,'','zujuan');
        });
        //留作业
        $('#addWorkToSave').live('click',function(){
            // if(editData.isIncludePrivateTest()){
            //     $.myDialog.showMsg('包含个人试题的试卷暂不支持留作业功能',1);
            //     return false;
            // }
            var idName='addworktosave';
            var myDate = new Date();
            var sname=$('#cursubject',window.parent.document).html();
            if(sname.indexOf('<div')!=-1 || sname.indexOf('<DIV')!=-1){
                var tmp_a=sname.split(/</i);
                sname=tmp_a[0];
            }
            var workname=myDate.getFullYear()+'年'+(myDate.getMonth()+1)+'月'+myDate.getDate()+'日'+sname+'作业';
            var tmp_str='<div style="margin-bottom:20px;">'+
                '<table border="0"><tbody>'+
                '<tr><td align="center"><b>作业名称：</b></td></tr>'+
                '<tr><td>'+
                '<input type="text" id="workpapername" value="'+workname+'" style="width:380px;padding:5px 0;color:#555;text-align:center;"/>'+
                '</td></tr>'+
                '<tr><td align="center">'+
                '</td></tr>'+
                '<tr><td><div style="display:inline-block;width:100%;height:1px;border-top:1px solid #ccc;"/></td></tr>'+
                '<tr><td align="center"><div id="showworkinfo"></div><div class="w220">'+
                '<span did="'+idName+'" class="tcClose bgbt an01 fr"><span class="an_left"></span><a>取消窗口</a><span class="an_right"></span></span>'+
                '<span id="worksubmit" class="bgbt an01"><span class="an_left"></span><a>留作业</a><span class="an_right"></span></span>'+
                '</div></td></tr>'+
                '</tbody></table>'+
                '</div>'+
                '<div id="explain">'+
                '<b style="color:#000;">留作业的用途？</b>'+
                '<hr style="border:0;border-bottom:1px solid #C1D3FB;"/>'+
                ' 留作业是将当前组卷中心内的试题保存为作业， 以后可以在布置作业中布置、查看或修改作业。 所留作业可以在【作业模块->布置作业】中找到。'+
                '</div>';
            $.myDialog.normalMsgBox(idName,"留作业",400,tmp_str);
            $('#workpapername').focus();
        });
        //留作业确定
        $('#worksubmit').live('click',function(){
            // if(editData.isIncludePrivateTest()){
            //     $.myDialog.showMsg('包含个人试题的试卷暂不支持留作业功能',1);
            //     return false;
            // }
            if($('#showworkinfo').html().indexOf('正在保存请稍候')!=-1){
                return;
            }
            $('#showworkinfo').html('<p class="list_ts"><span class="ico_dd">正在保存请稍候...</span></p>');
            var papername=$.trim($('#workpapername').val());
            if(papername.length==0){
                $('#showworkinfo').html('<span class="hfont">请填写作业名。</span>');
                $('#workpapername').focus();
                return;
            }
            if(papername.length>60){
                $('#showworkinfo').html('<span class="hfont">作业名过长，限60字符。</span>');
                $('#workpapername').focus();
                return;
            }
            var testList=editData.gettestid();
            if(!testList){
                $('#showworkinfo').html('');
                $.myDialog.showMsg('没有试题，请选择试题！',1);
                $(this).prev().click();
                return;
            }
            $.post(U('Home/Index/saveWork'),{'papername':papername,'data':editData.getall(),'testlist':testList,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    $('#showworkinfo').html('<span class="hfont">'+data['data']+'</span>');
                    return false;
                };
                if(data['data']=='success'){
                    $.myDialog.showMsg('留作业成功！下面转入【布置作业】');
                    $('#showworkinfo').html('');
                    $('#savesubmit').prev().click();
                    location.href=U('Work/Work/addWork');
                }
            });
        });
    },
    //回收站
    paperRecycle:function(){
        $('#paperrecycle').live('click',function(){
            var idName='recyclediv';
            var tmp_str='<div id="recquesbox">';
            var tmp_str_1=editData.get('rectest');
            if(!tmp_str_1){
                tmp_str+='<div align="center" style="font-size:14px;font-weight:bold;padding-top:10px;color:#999;">'+
                    '当前没有可以恢复的试题。'+
                    '<br/>'+
                    '在组卷中心内剔除过的试题都在此集中，最多能保存50题。'+
                    '</div>';
            }else{
                tmp_str+='<table border="0" width="565px"><tbody>';
                var tmp_str_2=tmp_str_1[1].split('@^@');
                var tmp_str_3;
                for(var tmp_i in tmp_str_2){
                    tmp_str_3=tmp_str_2[tmp_i].split('@%@');
                    tmp_str+='<tr class="queslist" id="recqueslist'+tmp_str_3[0]+'">'+
                        '<td>'+
                        '<input type="checkbox" class="checkbox" value="'+tmp_str_3[0]+'"/>'+
                        '</td>'+
                        '<td class="id">'+tmp_str_3[0]+'</td>'+
                        '<td class="title">'+
                        '<span style="color:#333;font-weight:bold;">(难度：'+tmp_str_3[1]+'|题型：'+tmp_str_3[3]+')</span>'+
                        tmp_str_3[2]+
                        '</td>'+
                        '<td class="time">'+tmp_str_3[4]+'</td>'+
                        '</tr>'+
                        '<tr>'+
                        '<td colspan="5" class="line"></td>'+
                        '</tr>';
                }

                tmp_str+='</tbody></table>'+
                    '</div><div align="center" style="margin-top:5px;">'+
                    '<input type="button" value="恢复试题" id="recsubmit"/>'+
                    '<input type="button" value="移出回收站" id="recremove"/>'+
                    '<input type="button" value="取消窗口" did="'+idName+'" class="tcClose"/>';
            }
            tmp_str+='</div>';
            $.myDialog.tcLoadDiv("试题回收站",580,idName);
            $('#'+idName+' .content').html(tmp_str);
            $.myDialog.tcShowDiv(idName);
            $('#div_shadow').css({'display':'block'});
        });
        //恢复试题
        $('#recsubmit').live('click',function(){
            var testList=new Array();
            var k=0;
            var tmp_id;
            $(this).parent().prev().find('.checkbox').each(function(){
                if($(this).attr('checked')=='checked'){
                    tmp_id=$(this).val();
                    testList[k]=tmp_id;
                    k++;
                    $.zujuan.delRecTest(tmp_id);
                }
            });
            if(k!=0){
                //获取试题并插入
                $.post(U('Index/getZjTestById'),{'id':testList.join(','),'width': $.zujuan.docWidth,'times':Math.random()},function(data){
                    if($.myCommon.backLogin(data)==false){
                        return false;
                    };
                    if(data['data']){
                        var typesArr=$.zujuan.getTypesByID();
                        var tmp_str,tmp_i,tmp_j,fenjuan,typenumb,leftstr,rightstr,dscore,nowscore;
                        nowscore=1;
                        for(var tmp_i in testList){
                            fenjuan=0;//分卷id
                            typenumb=-1;//题型序号
                            tmp_id=testList[tmp_i];
                            //试题分卷
                            var tmpTypesID=typesArr[data['data'][0][tmp_id]['typesid']];
                            fenjuan=tmpTypesID['Volume'];
                            dscore=tmpTypesID['DScore']+'|'+tmpTypesID['TypesScore']+'|'+tmpTypesID['IfDo'];
                            nowscore= $.myTest.getScoreNum(parseInt(data['data'][0][tmp_id]['testnum']),tmpTypesID['DScore'],tmpTypesID['TypesScore']);

                            if(fenjuan==0) return;
                            //遍历分卷下题型 判断试题插入位置
                            $('#s_paperpart'+fenjuan+' .s_questypename').each(function(i){
                                if($(this).html()==data['data'][0][tmp_id]['typesname']){
                                    if(typenumb==-1) typenumb=(i+1);
                                }
                            });
                            //更新试题篮
                            $.myTest.updateMainTypes(data['data'][0][tmp_id]['testnum'],data['data'][0][tmp_id]['typesname']);

                            //如果题型不存在加入左侧题型 右侧题型  试题篮题型
                            tmp_str=data['data'][0][tmp_id]['test'];
                            if(tmp_str.indexOf('【题号')!=-1){
                                var tmp_1=tmp_str['test'].split('【题号');
                                for(var jj=1;jj<tmp_1.length;jj++){
                                    tmp_1[jj]='<span class="quesindexnum">　1　</span>'+tmp_1[jj].substring(tmp_1[jj].indexOf('】')+1);
                                }
                                tmp_str=tmp_1.join('');
                            }
                            if(tmp_str.indexOf('【小题')!=-1){
                                tmp_str='<p>'+tmp_str.replace(/【小题[0-9]*】/g,'<span class="quesindex"><b></b></span><span class="quesscore"></span>');
                            }else{
                                tmp_str='<p><span class="quesindex"><b></b></span><span class="quesscore"></span><span class="tips"/>'+tmp_str;
                            }
                            leftstr='<div class="s_quesdiv" id="s_quesdiv'+tmp_id+'" title="题号：'+tmp_id+' | 难度：'+data['data'][0][tmp_id]['diffname']+' | '+data['data'][0][tmp_id]['docname']+' | '+data['data'][0][tmp_id]['typesname']+'">'+
                                '<span class="s_quesindex" queschildnum="'+data['data'][0][tmp_id]['testnum']+'"></span>'+
                                '<span class="s_questitle">'+data['data'][0][tmp_id]['docname']+'</span>'+
                                '</div>';
                            rightstr='<div class="quesbox" typesid="'+data['data'][0][tmp_id]['typesid']+'" id="quesbox'+tmp_id+'" onselectstart="return false">'+
                                '<div class="quesopmenu">'+
                                '<a class="detail">详细</a>'+
                                '<a class="replace">替换</a>'+
                                '<a class="del">删除</a>'+
                                '<a class="moveup">上移</a>'+
                                '<a class="movedn">下移</a>'+
                                '</div>'+
                                '<div class="quesdiv" id="quesdiv'+tmp_id+'">'+
                                '<div>'+tmp_str+'</div>'+
                                '</div></div>';
                            if(typenumb==-1){
                                //加入题型
                                leftstr='<div class="s_questype" id="s_questype'+fenjuan+'_1">'+
                                    '<div class="s_questypehead" id="s_questypehead'+fenjuan+'_1">'+
                                    '<a class="s_itemicon"></a>'+
                                    '<a class="s_display_open"></a>'+
                                    '<a class="s_edit" title="设置题型头部"></a>'+
                                    '<span class="s_questypeindex" id="s_questypeindex'+fenjuan+'_1">'+
                                    '<b></b>'+
                                    '</span>'+
                                    '<span class="s_questypename" id="s_questypename'+fenjuan+'_1">'+data['data'][0][tmp_id]['typesname']+'</span>'+
                                    '</div>'+
                                    '<div class="s_questypebody ui-sortable">'+leftstr+'</div>'+
                                    '</div>';
                                rightstr='<div class="questype" id="questype'+fenjuan+'_1">'+
                                    '<div class="questypehead" id="questypehead'+fenjuan+'_1">'+
                                    '<div class="questypemenu" style="display: none;">'+
                                    '<a class="editscore" style="opacity: 0.5;">改分</a>'+
                                    '<a class="empty" style="opacity: 0.5;">清空</a>'+
                                    '<a class="del">删除</a>'+
                                    '<a class="edit">设置</a>'+
                                    '<a class="moveup" style="opacity: 0.5;">上移</a>'+
                                    '<a class="movedn" style="opacity: 0.5;">下移</a>'+
                                    '</div>'+
                                    '<div class="questypeheadbox" id="questypeheadbox'+fenjuan+'_1">'+
                                    '<table border="0" width="100%" cellpadding="0" cellspacing="0">'+
                                    '<tbody>'+
                                    '<tr>'+
                                    '<td width="1">'+
                                    '<div class="questypescore" id="questypescore'+fenjuan+'_1" style="width: 120px; display: block;">'+
                                    '<table title="评分栏" border="1" cellpadding="0" cellspacing="0" bordercolor="#666666" style="border-top-width: 1px; border-left-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-top-style: solid; border-left-style: solid; border-right-style: solid; border-bottom-style: solid; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0);">'+
                                    '<tbody>'+
                                    '<tr>'+
                                    '<td width="55" height="20" align="center" style="border-top-width: 1px; border-left-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-top-style: solid; border-left-style: solid; border-right-style: solid; border-bottom-style: solid; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0);"> 评卷人 </td>'+
                                    '<td width="55" height="20" align="center" style="border-top-width: 1px; border-left-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-top-style: solid; border-left-style: solid; border-right-style: solid; border-bottom-style: solid; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0);"> 得 分 </td>'+
                                    '</tr>'+
                                    '<tr>'+
                                    '<td height="30" align="center" style="border-top-width: 1px; border-left-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-top-style: solid; border-left-style: solid; border-right-style: solid; border-bottom-style: solid; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0);"> </td>'+
                                    '<td height="30" align="center" style="border-top-width: 1px; border-left-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-top-style: solid; border-left-style: solid; border-right-style: solid; border-bottom-style: solid; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0);"> </td>'+
                                    '</tr>'+
                                    '</tbody>'+
                                    '</table>'+
                                    '</div>'+
                                    '</td>'+
                                    '<td>'+
                                    '<div class="questypetitle">'+
                                    '<span class="questypeindex">'+
                                    '<b></b>'+
                                    '</span>'+
                                    '<span class="questypename" id="questypename'+fenjuan+'_1">'+data['data'][0][tmp_id]['typesname']+'</span>'+
                                    '<span class="questypedscore" id="questypedscore'+fenjuan+'_1"></span>'+
                                    '<span class="questypenote" id="questypenote'+fenjuan+'_1">题型注释</span>'+
                                    '</div>'+
                                    '</td>'+
                                    '</tr>'+
                                    '</tbody>'+
                                    '</table>'+
                                    '</div>'+
                                    '</div>'+
                                    '<div class="questypebody">'+rightstr+'</div>'+
                                    '</div>';
                                $('#s_paperpart'+fenjuan+' .s_partbody').append(leftstr);
                                $('#paperpart'+fenjuan+' .partbody').append(rightstr);

                                //加入题型
                                editData.addtype(fenjuan,data['data'][0][tmp_id]['typesname'],'题型注释',tmp_id+'|'+data['data'][0][tmp_id]['testnum']+'|'+nowscore+'|0|0',dscore);
                                $.zujuan.changejfl(1);//增加填分栏最后一个
                            }else{
                                //加入左侧试题 右侧试题 重置试题篮数量
                                $('#s_questype'+fenjuan+'_'+typenumb+' .s_questypebody').append(leftstr);
                                $('#questype'+fenjuan+'_'+typenumb+' .questypebody').append(rightstr);
                                editData.addtest(tmp_id,data['data'][0][tmp_id]['testnum'],data['data'][0][tmp_id]['typesname'],data['data'][0][tmp_id]['typesid']);
                            }
                            //重置题型序号
                            $.zujuan.resetTypesID();
                        }
                    }

                    $.zujuan.updateTestNum();//更新试题序号
                    $.zujuan.updateTypesNum();//更新题型序号
                    $.zujuan.resetScore();//更新试题分值
                });
                $(this).next().next().click();
            }else{
                alert('请选择试题进行恢复。');
            }
        });
        //移除回收站
        $('#recremove').live('click',function(){
            var iftest=1;
            $(this).parent().prev().find('.checkbox').each(function(){
                if($(this).attr('checked')=='checked'){
                    iftest=0;
                    $.zujuan.delRecTest($(this).val());
                }
            });
            if(iftest==1) alert('请选择试题进行移除。');
        });
    },
    //重置 清空所有
    paperReset:function(){
        $('#paperreset').live('click',function(){
            if(!confirm('确认清空所有试题？')){
                return;
            }
            //清空试题
            $('#quescount', window.parent.document).html(0);
            var g='';
            var f=55;
            var h=0;
            var k=0;
            var t='0.0';
            var _this;
            var output='';
//            for(var i=0;i<Types.length;i++){
//                g=Types[i]['TypesName'];
//                output+="<tr>" + "<td align='right' title='" + g + "'"+(g.length >6 ? " width='105' " : '')+">" + g + "：</td>" + "<td align='left'><span class='bilibox' style='width:" + f + "px;'>" + "<span class='bilibg' style='width:" + k + "px;'></span>" + "</span></td>" + "<td align='right'>" + h + "题</td>" + "<td><a class='emptyquestype' href='javascript:void(0);' title='清空 " + g + "'></a></td>" + "</tr>";
//            }
//            $('#quescountdetail tbody', window.parent.document).empty().html(output);
            $('#quescountdetail tbody', window.parent.document).empty();
            //清空结构
            editData.clear();
            //初始化cookie
            editData.setCookieInit();
            window.location.reload(true);
        });
    },
    /******************************
     * 标题操作事件开始
     *******************************/
    //标题操作事件
    paperSettings:function(){
        $('#papersettings').live('click',function(){
            var idName='setdiv';
            //显示选择项
            $.myDialog.tcLoadDiv("试卷设置",580,idName);
            //载入弹出框信息
            $('#'+idName+' .content').html('<div class="setmain" id="setmain"></div>'+
                '<div class="btm_btn">'+
                '<input class="set_btn" type="button" id="setsubmitbtn" value="确定设置"/>'+
                '<input type="button" id="setclosebtn" value="关闭窗口"/>'+
                '</div>');

            $.myDialog.tcShowDiv(idName);
            $('#div_shadow').css({'display':'block'});
            $.zujuan.initValues();//数据初始化

            $('#setmain div').removeClass('box_current');
            $('#setmain .settitle').css({'background-color':'#e4e7e9','color':'#333'});

        });
    },
    //标题修改框内容
    getDivBoxHtml:function(params) {
        var html = [];
        if (typeof params == "undefined") { return html.join(''); }
        if (typeof params.code == "undefined") { return html.join(''); }
        if (typeof params.title == "undefined") { return html.join(''); }

        html.push('<div class="setbox" id="' + params.code + '">');
        html.push('<table border="0" cellpadding="0" cellspacing="0"><tr><td class="settitle">' + params.title + '</td>');
        html.push('<td valign="top">');
        html.push('<table border="0" cellpadding="0" cellspacing="0">');

        html.push('<tr><td style="height:5px;"></td><td style="height:5px;"></td></tr>');//5px

        html.push('<tr><td align="right" width="70">是否显示</td><td >');

        html.push('<table border="0" cellpadding="0" cellspacing="0"><tr>');
        html.push('<td><input type="radio" id="radio_' + params.code + '1" name="radio_' + params.code + '" value="1" class="radio"/></td>');
        html.push('<td><label for="radio_' + params.code + '1"><a class="show"></a></label></td>');
        html.push('<td><label for="radio_' + params.code + '1"><span class="radiospan1">显示</span></label></td>');
        html.push('<td><input type="radio" id="radio_' + params.code + '2" name="radio_' + params.code + '" value="0" class="radio"/></td>');
        html.push('<td><label for="radio_' + params.code + '2"><a class="hide"></a></label></td>');
        html.push('<td><label for="radio_' + params.code + '2"><span class="radiospan1">不显示</span></label></td>');
        html.push('</tr></table>');

        html.push('</td></tr>');

        if (params.score) {
            html.push('<tr><td align="right" width="70">评分栏</td><td >');
            html.push('<table border="0" cellpadding="0" cellspacing="0"><tr>');
            html.push('<td><input type="radio" id="radio_' + params.code + '_score1" name="radio_' + params.code + '_score" value="1" /></td>');
            html.push('<td><label for="radio_' + params.code + '_score1"><a class="show"></a></label></td>');
            html.push('<td><label for="radio_' + params.code + '_score1"><span class="radiospan2">显示</span></label></td>');
            html.push('<td><input type="radio" id="radio_' + params.code + '_score2" name="radio_' + params.code + '_score" value="0" /></td>');
            html.push('<td><label for="radio_' + params.code + '_score2"><a class="hide"></a></label></td>');
            html.push('<td><label for="radio_' + params.code + '_score2"><span class="radiospan2">不显示</span></label></td>');
            html.push('</tr></table>');

            html.push('</td></tr>');
        }
        if (params.text && params.text.length > 0) {
            html.push('<tr><td align="right">&nbsp;' + params.text + '</td><td valign="top">&nbsp;<input name="text_' + params.code + '" type="text" class="text" /></td></tr>');
        }
        if (params.dscoretext) {
            html.push('<tr><td align="right">&nbsp;' + params.dscoretext + '</td><td valign="top">&nbsp;<select name="select_' + params.code + '" >');
            for(var tmp_i=1;tmp_i<61;tmp_i++){
                html.push('<option value="'+tmp_i+'">'+tmp_i+'</option>');
            }
            html.push('</select></td></tr>');
        }
        if (params.note && params.note.length > 0) {
            html.push('<tr><td align="right">&nbsp;' + params.note + '</td><td valign="top">&nbsp;<textarea name="textarea_' + params.code + '" class="text"></textarea></td></tr>');
        }
        html.push('<tr><td style="height:5px;"></td><td style="height:5px;"></td></tr>');
        html.push('</table></td></tr></table>');
        html.push('</div>');
        return html.join('');
    },
    //展示标题修改框
    initDivHtml:function () {
        var params1 = { code: "divmaintitle", title: "主标题", text: "文本" };
        var params2 = { code: "divsubtitle", title: "副标题", text: "文本" };
        var params3 = { code: "divseal", title: "装订线" };
        var params4 = { code: "divmarktag", title: "保密标记", text: "文本" };
        var params5 = { code: "divtestinfo", title: "试卷信息栏", text: "文本" };
        var params6 = { code: "divstudentinput", title: "考生输入栏", text: "文本" };
        var params7 = { code: "divscore", title: "誊分栏" };
        var params8 = { code: "divnotice", title: "注意事项栏", note: "文本" };
        var html = this.getDivBoxHtml(params1) + this.getDivBoxHtml(params2) +
            this.getDivBoxHtml(params3) + this.getDivBoxHtml(params4) +
            this.getDivBoxHtml(params5) + this.getDivBoxHtml(params6) +
            this.getDivBoxHtml(params7) + this.getDivBoxHtml(params8);
        $("#setmain").html(html);
    },

    //初始化标题弹出框数据
    initValues:function(){
        this.initDivHtml();
        var tmp_str,tmp_str_1;
        //主标题
        tmp_str=editData.get('maintitle');
        $('#divmaintitle :text').val(tmp_str[2]);
        $('#divmaintitle :radio').each(function(){
            if($(this).val()==tmp_str[1]) $(this).attr('checked','checked');
        });
        $('#divmaintitle :text').attr("disabled",tmp_str[1]==1 ? false : true);
        //副标题
        tmp_str=editData.get('subtitle');
        $('#divsubtitle :text').val(tmp_str[2]);
        $('#divsubtitle :radio').each(function(){
            if($(this).val()==tmp_str[1]) $(this).attr('checked','checked');
        });
        $('#divsubtitle :text').attr("disabled",tmp_str[1]==1 ? false : true);
        //装订线
        tmp_str=editData.get('seal');
        $('#divseal :radio').each(function(){
            if($(this).val()==tmp_str[1]) $(this).attr('checked','checked');
        });
        //保密标记
        tmp_str=editData.get('marktag');
        $('#divmarktag :text').val(tmp_str[2]);
        $('#divmarktag :radio').each(function(){
            if($(this).val()==tmp_str[1]) $(this).attr('checked','checked');
        });
        $('#divmarktag :text').attr("disabled",tmp_str[1]==1 ? false : true);
        //试卷信息栏
        tmp_str=editData.get('testinfo');
        $('#divtestinfo :text').val(tmp_str[2]);
        $('#divtestinfo :radio').each(function(){
            if($(this).val()==tmp_str[1]) $(this).attr('checked','checked');
        });
        $('#divtestinfo :text').attr("disabled",tmp_str[1]==1 ? false : true);
        //考生输入栏
        tmp_str=editData.get('studentinput');
        $('#divstudentinput :text').val(tmp_str[2]);
        $('#divstudentinput :radio').each(function(){
            if($(this).val()==tmp_str[1]) $(this).attr('checked','checked');
        });
        $('#divstudentinput :text').attr("disabled",tmp_str[1]==1 ? false : true);
        //誊分栏
        tmp_str=editData.get('score');
        $('#divscore :radio').each(function(){
            if($(this).val()==tmp_str[1]) $(this).attr('checked','checked');
        });
        //注意事项栏
        tmp_str=editData.get('notice');
        $('#divnotice textarea').val(tmp_str[2].replace(/<br\/>/ig,"\r\n"));
        $('#divnotice :radio').each(function(){
            if($(this).val()==tmp_str[1]) $(this).attr('checked','checked');
        });
        $('#divnotice textarea').attr("disabled",tmp_str[1]==1 ? false : true);
        //分卷
        var j=0;
        $('.s_parthead').each(function(i){
            var tmp_id=$(this).attr('id').replace('s_','');
            var params={ code: "div"+tmp_id, title: "分卷"+(i+1)+"头部", text: "卷标", note: "卷注" };
            $('#setmain').append($.zujuan.getDivBoxHtml(params));

            tmp_str=editData.get(tmp_id);
            $('#div'+tmp_id+' :text').val(tmp_str[2]);
            $('#div'+tmp_id+' textarea').val(tmp_str[3].replace(/<br\/>/ig,"\r\n"));
            $('#div'+tmp_id+' :radio').each(function(){
                if($(this).val()==tmp_str[1]) $(this).attr('checked','checked');
            });
            $('#div'+tmp_id+' :text').attr("disabled",tmp_str[1]==1 ? false : true);
            $('#div'+tmp_id+' textarea').attr("disabled",tmp_str[1]==1 ? false : true);

            $(this).next().find('.s_questypehead').each(function(){
                var tmp_id=$(this).attr('id').replace('s_','');
                var params={ code: "div"+tmp_id, title: "题型"+shuzi[j]+"头部", text: "题型名称", note: "题型注释", score: 1};
                $('#setmain').append($.zujuan.getDivBoxHtml(params));
                j++;

                tmp_str=editData.get(tmp_id);
                $('#div'+tmp_id+' :text').val(tmp_str[2]);
                $('#div'+tmp_id+' textarea').val(tmp_str[3].replace(/<br\/>/ig,"\r\n"));
                tmp_str_1=tmp_str[6].split('|');
                $('#div'+tmp_id+' select').val(tmp_str_1[0]);
                $('#div'+tmp_id+' :radio').each(function(i){
                    if(i<2){
                        if($(this).val()==tmp_str[1]) $(this).attr('checked','checked');
                    }else{
                        if($(this).val()==tmp_str[4]) $(this).attr('checked','checked');
                    }
                });

                $('#div'+tmp_id+' :text').attr("disabled",tmp_str[1]==1 ? false : true);
                $('#div'+tmp_id+' textarea').attr("disabled",tmp_str[1]==1 ? false : true);
                $('#div'+tmp_id+' select').attr("disabled",tmp_str[1]==1 ? false : true);
            });
        });
    },
    /******************************
     * 标题操作事件结束
     *******************************/
    //默认 标准 测验 作业
    setStyle:function(str,reload){
        if(typeof(reload)=='undefined' || reload!=1) reload=0;
        var tmp_str;

        //获取年份和月份
        var d=new Date();
        var y=d.getFullYear();
        var m=d.getMonth()+1;
        if(m<7){
            y=y-1;
        }
        var maintitle=y+"-"+(y+1)+"学年度"+school+""+m+"月月考卷";
        
        //更新联考图标样式
        $.zujuan.updateExamSave();
        
        switch(str){
            case '默认样式':
                tmp_str=editData.gettitle('maintitle');
                if(tmp_str) maintitle=tmp_str;
                var subtitle="试卷副标题";
                tmp_str=editData.gettitle('subtitle');
                if(tmp_str) subtitle=tmp_str;
                var marktag="绝密★启用前";
                tmp_str=editData.gettitle('marktag');
                if(tmp_str) marktag=tmp_str;
                var testinfo="考试范围：xxx；考试时间：100分钟；命题人：xxx";
                tmp_str=editData.gettitle('testinfo');
                if(tmp_str) testinfo=tmp_str;
                var studentinput="学校:___________姓名：___________班级：___________考号：___________";
                tmp_str=editData.gettitle('studentinput');
                if(tmp_str) studentinput=tmp_str;
                var notice="1．答题前填写好自己的姓名、班级、考号等信息<br/>2．请将答案正确填写在答题卡上";
                tmp_str=editData.gettitle('notice');
                if(tmp_str) notice=tmp_str;
                $.zujuan.setPaper({ code: "maintitle", display: 1, title: maintitle});
                $.zujuan.setPaper({ code: "subtitle", display: 1, title: subtitle});
                $.zujuan.setPaper({ code: "seal", display: 1 });
                $.zujuan.setPaper({ code: "marktag", display: 1, title: marktag});
                $.zujuan.setPaper({ code: "testinfo", display: 1, title: testinfo});
                $.zujuan.setPaper({ code: "studentinput", display: 0, title: studentinput});
                $.zujuan.setPaper({ code: "score", display: 1 });
                $.zujuan.setPaper({ code: "notice", display: 1, title: notice});
                //记录数据
                editData.set('maintitle','maintitle@$@1@$@'+maintitle);
                editData.set('subtitle','subtitle@$@1@$@'+subtitle);
                editData.set('seal','seal@$@1');
                editData.set('marktag','marktag@$@1@$@'+marktag);
                editData.set('testinfo','testinfo@$@1@$@'+testinfo);
                editData.set('studentinput','studentinput@$@0@$@'+studentinput);
                editData.set('score','score@$@1');
                editData.set('notice','notice@$@1@$@'+notice);
                //卷体
                $('.s_parthead').each(function(){
                    var _this=$(this);
                    var tmp_id=$(this).attr('id').replace('s_','');
                    var tmp_title=$(this).find('.s_partname').html();
                    var tmp_text=$('#'+tmp_id+" .partnote").html();
                    tmp_str=editData.getvalue(tmp_id,2);
                    if(tmp_str) tmp_title=tmp_str;
                    tmp_str=editData.getvalue(tmp_id,3);
                    if(tmp_str) tmp_text=tmp_str;
                    $.zujuan.setContent({ code: tmp_id, display: 1, title: tmp_title, text: tmp_text});
                    editData.set(tmp_id,tmp_id+'@$@1@$@'+tmp_title+"@$@"+tmp_text);

                    if(reload==1){
                        return; //对初始载入数据不添加题型
                    }

                    _this.next().find('.s_questypehead').each(function(){
                        var tmp_i;
                        var tmp_test=0;
                        var tmp_score="0|1";
                        var tmp_str_1=$(this).find('.s_questypename').html();

                        tmp_id=$(this).attr('id').replace('s_','');
                        tmp_title=$(this).find('.s_questypename').html();
                        tmp_text=$('#'+tmp_id+" .questypenote").html();
                        tmp_str=editData.getvalue(tmp_id,2);
                        if(tmp_str) tmp_title=tmp_str;
                        tmp_str=editData.getvalue(tmp_id,3);
                        if(tmp_str) tmp_text=tmp_str;
                        tmp_str=editData.getvalue(tmp_id,5);
                        if(tmp_str) tmp_test=tmp_str;
                        tmp_str=editData.getvalue(tmp_id,6);
                        if(tmp_str) tmp_score=tmp_str;
                        else{
                            for(tmp_i in Types){
                                if(tmp_str_1==Types[tmp_i]['TypesName']) tmp_score=Types[tmp_i]['DScore']+'|'+Types[tmp_i]['TypesScore']+'|'+Types[tmp_i]['IfDo'];
                            }
                        }
                        $.zujuan.setText({ code: tmp_id, display: 1, title: tmp_title, text: tmp_text, score: 1});

                        editData.set(tmp_id,tmp_id+'@$@1@$@'+tmp_title+"@$@"+tmp_text+"@$@"+1+'@$@'+tmp_test+'@$@'+tmp_score);

                    });
                });
                break;
            case '标准样式':
                $.zujuan.setPaper({ code: "maintitle", display: 1 });
                $.zujuan.setPaper({ code: "subtitle", display: 1 });
                $.zujuan.setPaper({ code: "seal", display: 1 });
                $.zujuan.setPaper({ code: "marktag", display: 0 });
                $.zujuan.setPaper({ code: "testinfo", display: 1 });
                $.zujuan.setPaper({ code: "studentinput", display: 0 });
                $.zujuan.setPaper({ code: "score", display: 0 });
                $.zujuan.setPaper({ code: "notice", display: 1 });
                //记录数据
                editData.setdisplay('maintitle',1);
                editData.setdisplay('subtitle',1);
                editData.setdisplay('seal',1);
                editData.setdisplay('marktag',0);
                editData.setdisplay('testinfo',1);
                editData.setdisplay('studentinput',0);
                editData.setdisplay('score',0);
                editData.setdisplay('notice',1);
                //卷体
                $('.s_parthead').each(function(){
                    var tmp_id=$(this).attr('id').replace('s_','');
                    editData.setvalue(tmp_id,1,1);
                    $.zujuan.setContent({ code: tmp_id, display: 1});
                });
                $('.s_questypehead').each(function(){
                    var tmp_id=$(this).attr('id').replace('s_','');
                    editData.setvalue(tmp_id,1,1);
                    editData.setvalue(tmp_id,1,4);
                    $.zujuan.setText({ code: tmp_id, display: 1, score: 1});
                });
                break;
            case '测验样式':
                $.zujuan.setPaper({ code: "maintitle", display: 1 });
                $.zujuan.setPaper({ code: "subtitle", display: 0 });
                $.zujuan.setPaper({ code: "seal", display: 0 });
                $.zujuan.setPaper({ code: "marktag", display: 0 });
                $.zujuan.setPaper({ code: "testinfo", display: 0 });
                $.zujuan.setPaper({ code: "studentinput", display: 1 });
                $.zujuan.setPaper({ code: "score", display: 0 });
                $.zujuan.setPaper({ code: "notice", display: 0 });
                //记录数据
                editData.setdisplay('maintitle',1);
                editData.setdisplay('subtitle',0);
                editData.setdisplay('seal',0);
                editData.setdisplay('marktag',0);
                editData.setdisplay('testinfo',0);
                editData.setdisplay('studentinput',1);
                editData.setdisplay('score',0);
                editData.setdisplay('notice',0);
                //卷体
                $('.s_parthead').each(function(){
                    var tmp_id=$(this).attr('id').replace('s_','');
                    editData.setvalue(tmp_id,0,1);
                    $.zujuan.setContent({ code: tmp_id, display: 0});
                });
                $('.s_questypehead').each(function(){
                    var tmp_id=$(this).attr('id').replace('s_','');
                    editData.setvalue(tmp_id,1,1);
                    editData.setvalue(tmp_id,0,4);
                    $.zujuan.setText({ code: tmp_id, display: 1, score: 0});
                });
                break;
            case '作业样式':
                $.zujuan.setPaper({ code: "maintitle", display: 1 });
                $.zujuan.setPaper({ code: "subtitle", display: 0 });
                $.zujuan.setPaper({ code: "seal", display: 0 });
                $.zujuan.setPaper({ code: "marktag", display: 0 });
                $.zujuan.setPaper({ code: "testinfo", display: 0 });
                $.zujuan.setPaper({ code: "studentinput", display: 0 });
                $.zujuan.setPaper({ code: "score", display: 0 });
                $.zujuan.setPaper({ code: "notice", display: 0 });
                //记录数据
                editData.setdisplay('maintitle',1);
                editData.setdisplay('subtitle',0);
                editData.setdisplay('seal',0);
                editData.setdisplay('marktag',0);
                editData.setdisplay('testinfo',0);
                editData.setdisplay('studentinput',0);
                editData.setdisplay('score',0);
                editData.setdisplay('notice',0);

                //卷体
                $('.s_parthead').each(function(){
                    var tmp_id=$(this).attr('id').replace('s_','');
                    editData.setvalue(tmp_id,0,1);
                    $.zujuan.setContent({ code: tmp_id, display: 0});
                });
                $('.s_questypehead').each(function(){
                    var tmp_id=$(this).attr('id').replace('s_','');
                    editData.setvalue(tmp_id,0,1);
                    editData.setvalue(tmp_id,0,4);
                    $.zujuan.setText({ code: tmp_id, display: 0, score: 0});
                });
                break;
            default :
                var paperstyle=editData.getall();
                if(!paperstyle){
                    $.zujuan.setStyle('默认样式',1);
                    return ;
                }
                tmp_str=editData.get('maintitle');
                if(tmp_str===false) editData.set('maintitle','maintitle@$@1@$@'+maintitle);
                if(tmp_str[2]) maintitle=tmp_str[2];
                tmp_display = tmp_str[1]==='0' ? 0 : 1;
                $.zujuan.setPaper({ code: "maintitle", display: tmp_display , title: maintitle});

                var subtitle="试卷副标题";
                tmp_str=editData.get('subtitle');
                if(tmp_str===false) editData.set('subtitle','subtitle@$@1@$@'+subtitle);
                if(tmp_str[2]) maintitle=tmp_str[2];
                if(tmp_str[2]) subtitle=tmp_str[2];
                tmp_display = tmp_str[1]==='0' ? 0 : 1;
                $.zujuan.setPaper({ code: "subtitle", display: tmp_display, title: subtitle});

                if(editData.getvalue('seal',1)==='0'){
                    tmp_display = 0;
                }else{
                    tmp_display = 1;
                    editData.set('seal','seal@$@1');
                }
                $.zujuan.setPaper({ code: "seal", display: tmp_display });

                var marktag="绝密★启用前";
                tmp_str=editData.get('marktag');
                if(tmp_str===false) editData.set('marktag','marktag@$@1@$@'+marktag);
                if(tmp_str[2]) marktag=tmp_str[2];
                tmp_display = tmp_str[1]==='0' ? 0 : 1;
                $.zujuan.setPaper({ code: "marktag", display: tmp_display, title: marktag});

                var testinfo="考试范围：xxx；考试时间：100分钟；命题人：xxx";
                tmp_str=editData.get('testinfo');
                if(tmp_str===false)  editData.set('testinfo','testinfo@$@1@$@'+testinfo);
                if(tmp_str[2]) testinfo=tmp_str[2];
                tmp_display = tmp_str[1]==='0' ? 0 : 1;
                $.zujuan.setPaper({ code: "testinfo", display: tmp_display, title: testinfo});

                var studentinput="学校:___________姓名：___________班级：___________考号：___________";
                tmp_str=editData.get('studentinput');
                if(tmp_str===false)  editData.set('studentinput','studentinput@$@0@$@'+studentinput);
                if(tmp_str[2]) studentinput=tmp_str[2];
                tmp_display = tmp_str[1]===1 ? 1 : 0;
                $.zujuan.setPaper({ code: "studentinput", display: tmp_display, title: studentinput});

                if(editData.getvalue('score',1)==='0'){
                    tmp_display = 0;
                }else{
                    tmp_display = 1;
                    editData.set('score','score@$@1');
                }
                $.zujuan.setPaper({ code: "score", display: tmp_display });

                var notice="1．答题前填写好自己的姓名、班级、考号等信息<br/>2．请将答案正确填写在答题卡上";
                tmp_str=editData.get('notice');
                if(tmp_str===false)  editData.set('notice','notice@$@1@$@'+notice);
                if(tmp_str[2]) notice=tmp_str[2];
                tmp_display = tmp_str[1]==='0' ? 0 : 1;
                $.zujuan.setPaper({ code: "notice", display: tmp_display, title: notice});

                var tmp_arr;
                var tmp_arr_1;
                var tmp_i;
                tmp_arr=paperstyle.split('@#@');
                for(tmp_i in tmp_arr){
                    tmp_arr_1=tmp_arr[tmp_i].split('@$@');
                    if(tmp_arr_1[0]=='attr') continue;
                    if(tmp_arr_1.length==7){
                        $.zujuan.setText({ code: tmp_arr_1[0], display: tmp_arr_1[1], title: tmp_arr_1[2], text: tmp_arr_1[3], score: tmp_arr_1[4]});
                    }else if(tmp_arr_1.length==4){
                        $.zujuan.setContent({ code: tmp_arr_1[0], display: tmp_arr_1[1], title: tmp_arr_1[2], text: tmp_arr_1[3]});
                    }else if(tmp_arr_1.length==3){
                        $.zujuan.setPaper({ code: tmp_arr_1[0], display: tmp_arr_1[1], title: tmp_arr_1[2]});
                    }else if(tmp_arr_1.length==2){
                        $.zujuan.setPaper({ code: tmp_arr_1[0], display: tmp_arr_1[1]});
                    }
                }
                break;
        }
    },
    //设置卷面 params格式 { code: "maintitle", display: 0, title: "主标题" };
    setPaper:function(params) {
        if (typeof params == "undefined") { return ; }
        if (typeof params.code == "undefined") { return ; }
        if(params.display==1){
            $('#s_'+params.code+' .s_display_close').addClass('s_display_open');
            $('#s_'+params.code+' .s_display_close').removeClass('s_display_close');
            $('#pui_'+params.code).css('display','block');
        }else if(params.display==0){
            $('#s_'+params.code+' .s_display_open').addClass('s_display_close');
            $('#s_'+params.code+' .s_display_open').removeClass('s_display_open');
            $('#pui_'+params.code).css('display','none');
        }
        if(params.title && params.title.length>0){
            if(params.code=="notice") $('#pui_'+params.code+"text").html(params.title);
            else $('#pui_'+params.code).html(params.title);
        }
        return ;
    },
    //设置卷体params格式 { code: "parthead1", display: 0, title: "第I卷（选择题）", text: "请点击修改第I卷的文字说明"};
    setContent:function(params) {
        if (typeof params == "undefined") { return ; }
        if (typeof params.code == "undefined") { return ; }
        if (typeof params.display == "undefined") { return ; }
        if(params.display==1){
            $('#s_'+params.code+' .s_display_close').addClass('s_display_open');
            $('#s_'+params.code+' .s_display_close').removeClass('s_display_close');
            $('#'+params.code).css('display','block');
        }else if(params.display==0){
            $('#s_'+params.code+' .s_display_open').addClass('s_display_close');
            $('#s_'+params.code+' .s_display_open').removeClass('s_display_open');
            $('#'+params.code).css('display','none');
        }
        var tmp_id;
        //if(params.title && params.title.length>0){
        var tmp_id=$('#s_'+params.code+" span").attr("id").replace('s_','');
        $('#s_'+tmp_id).html(params.title);
        $('#'+tmp_id).html(params.title);
        //}
        //if(params.text && params.text.length>0){
        tmp_id=$('#s_'+params.code+" span").attr("id").replace('s_','');
        $('#'+tmp_id).next().html(params.text);
        //}

        return ;
    },
    //设置卷体params格式 { code: "questypehead2_1", display: 0, title: "现代文阅读", text: "题型注释", score: 0, dscore: 分值|计分方式|选做};
    setText:function(params) {
        if (typeof params == "undefined") { return ; }
        if (typeof params.code == "undefined") { return ; }
        if (typeof params.display == "undefined") { return ; }
        if(params.display==1){
            $('#s_'+params.code+' .s_display_close').addClass('s_display_open');
            $('#s_'+params.code+' .s_display_close').removeClass('s_display_close');
            $('#'+params.code).css('display','block');
        }else if(params.display==0){
            $('#s_'+params.code+' .s_display_open').addClass('s_display_close');
            $('#s_'+params.code+' .s_display_open').removeClass('s_display_open');
            $('#'+params.code).css('display','none');
        }
        if(params.score==1){
            $('#'+params.code+' .questypescore').css('display','block');
        }else if(params.score==0){
            $('#'+params.code+' .questypescore').css('display','none');
        }
        //if(params.title && params.title.length>0){
        $('#s_'+params.code+' .s_questypename').html(params.title);
        $('#'+params.code+' .questypename').html(params.title);
        //}
        if(params.dscore){
            var tmpArr=editData.checkscore(params.code);
            if(tmpArr=='') return ;
            //显示试题分值
            if(tmpArr[0]==-1){
                $('#'+params.code+' .questypedscore').html($.zujuan.getScoreText(tmpArr[1].length,tmpArr[2],1));
            }else{
                $('#'+params.code+' .questypedscore').html($.zujuan.getScoreText(tmpArr[1].length,tmpArr[1][0]));
            }
        }
        //if(params.text && params.text.length>0){
        $('#'+params.code+' .questypenote').html(params.text);
        //}

        return ;
    },

    //试卷结构快捷设置
    setPaperStructure:function(){
        $('#paperstylelist').live('mouseenter',function(){
            $('#quickstyles').css('display','block');
        });
        $('#paperstylelist').live('mouseleave',function(){
            $('#quickstyles').css('display','none');
        });
        $('#quickstyles a').live('click',function(){
            $('#quickstyles').css('display','none');
            $.zujuan.setStyle($(this).html());
        });
    },

    //左侧图标事件
    leftIcoEvent:function(){
        $('#s_paperheaditem .s_display_open').live('click',function(){
            editData.setvalue($(this).parent().attr('id').replace('s_',''),0,1);
            $.zujuan.setPaper({ code: $(this).parent().attr('id').replace('s_',''), display: 0 });
        });
        $('#s_paperheaditem .s_display_close').live('click',function(){
            editData.setvalue($(this).parent().attr('id').replace('s_',''),1,1);
            $.zujuan.setPaper({ code: $(this).parent().attr('id').replace('s_',''), display: 1 });
        });
    },
    //试卷头事件
    paperHeaderEvent:function(){
        $('#s_parthead .s_display_open').live('click',function(){
            editData.setvalue($(this).parent().attr('id').replace('s_',''),0,1);
            $.zujuan.setContent({ code: $(this).parent().attr('id').replace('s_',''), display: 0 });
        });
        $('#s_parthead .s_display_close').live('click',function(){
            editData.setvalue($(this).parent().attr('id').replace('s_',''),1,1);
            $.zujuan.setContent({ code: $(this).parent().attr('id').replace('s_',''), display: 1 });
        });
        $('#s_paperbody .s_display_open').live('click',function(){
            editData.setvalue($(this).parent().attr('id').replace('s_',''),0,1);
            $.zujuan.setText({ code: $(this).parent().attr('id').replace('s_',''), display: 0 });
        });
        $('#s_paperbody .s_display_close').live('click',function(){
            editData.setvalue($(this).parent().attr('id').replace('s_',''),1,1);
            $.zujuan.setText({ code: $(this).parent().attr('id').replace('s_',''), display: 1 });
        });
    },

    //左侧标题事件
    leftTitleEvent:function(){
        $('.s_partname').live('click',function(){
            var tmp_id=$('#'+$(this).attr('id').replace('s_partname','parthead'));
            $('#rightdiv').animate({"scrollTop":tmp_id.offset().top+$('#rightdiv').scrollTop()-$('#topdiv').offset().top-$('#topdiv').height()-20},500,function(){
                $.myTest.showAlpha(tmp_id);
            });
        });
        $('.s_questypeindex').live('click',function(){
            var tmp_id=$('#'+$(this).attr('id').replace('s_questypeindex','questype'));
            $('#rightdiv').animate({"scrollTop":tmp_id.offset().top+$('#rightdiv').scrollTop()-$('#topdiv').offset().top-$('#topdiv').height()-20},500,function(){
                $.myTest.showAlpha(tmp_id);
            });
        });
        $('.s_questypename').live('click',function(){
            var tmp_id=$('#'+$(this).attr('id').replace('s_questypename','questype'));
            $('#rightdiv').animate({"scrollTop":tmp_id.offset().top+$('#rightdiv').scrollTop()-$('#topdiv').offset().top-$('#topdiv').height()-20},500,function(){
                $.myTest.showAlpha(tmp_id);
            });
        });
    },
    //左侧图标编辑
    leftEditEvent:function(){
        $('.s_edit').live('click',function(){
            $('#papersettings').click();
            var tmp_id=$(this).parent().attr('id').replace('s_','');
            $('#setmain div').removeClass('box_current');
            $('#div'+tmp_id).addClass('box_current');
            $('#setmain .settitle').css({'background-color':'#e4e7e9','color':'#333'});
            $('#div'+tmp_id+" .settitle").css({'background-color':'#dff1fa','color':'#00a0e9'});
            $('#setmain').scrollTop($('#div'+tmp_id).offset().top-$('#setmain').offset().top);
        });
    },
    //左侧试题点击事件
    leftTestClickEvent:function(){
        $('.s_quesdiv').live('click',function(){
            var tmp_id=$('#quesbox'+$(this).attr('id').replace('s_quesdiv',''));
            $('#rightdiv').animate({scrollTop: tmp_id.offset().top+$('#rightdiv').scrollTop()-$('#rightdiv').offset().top-3}, "slow",function(){
                $.myTest.showAlpha(tmp_id);
            });
        });
    },
    //右侧标题编辑
    rightTitleEditEvent:function(){
        $('#pui_marktag').live('click',function(){
            $('#s_marktag .s_edit').click();
        }).live('mouseenter',function(){
            $(this).css('color','#d17100');
            $(this).css('cursor','pointer');
        }).live('mouseleave',function(){
            $(this).css('color','#000000');
        });
        $('#pui_maintitle').live('click',function(){
            $('#s_maintitle .s_edit').click();
        }).live('mouseenter',function(){
            $(this).css('color','#d17100');
            $(this).css('cursor','pointer');
        }).live('mouseleave',function(){
            $(this).css('color','#000000');
        });
        $('#pui_subtitle').live('click',function(){
            $('#s_subtitle .s_edit').click();
        }).live('mouseenter',function(){
            $(this).css('color','#d17100');
            $(this).css('cursor','pointer');
        }).live('mouseleave',function(){
            $(this).css('color','#000000');
        });
        $('#pui_testinfo').live('click',function(){
            $('#s_testinfo .s_edit').click();
        }).live('mouseenter',function(){
            $(this).css('color','#d17100');
            $(this).css('cursor','pointer');
        }).live('mouseleave',function(){
            $(this).css('color','#000000');
        });
        $('#pui_testinfo').live('click',function(){
            $('#s_testinfo .s_edit').click();
        }).live('mouseenter',function(){
            $(this).css('color','#d17100');
            $(this).css('cursor','pointer');
        }).live('mouseleave',function(){
            $(this).css('color','#000000');
        });
        $('#pui_studentinput').live('click',function(){
            $('#s_studentinput .s_edit').click();
        }).live('mouseenter',function(){
            $(this).css('color','#d17100');
            $(this).css('cursor','pointer');
        }).live('mouseleave',function(){
            $(this).css('color','#000000');
        });
        $('#pui_score').live('click',function(){
            $('#s_score .s_edit').click();
        }).live('mouseenter',function(){
            $(this).css('color','#d17100');
            $(this).css('cursor','pointer');
            $(this).find('table').css('border','1px solid #d17100');
            $(this).find('td').css('border','1px solid #d17100');
        }).live('mouseleave',function(){
            $(this).css('color','#000000');
            $(this).find('table').css('border','1px solid #000000');
            $(this).find('td').css('border','1px solid #000000');
        });
        $('#pui_notice').live('click',function(){
            $('#s_notice .s_edit').click();
        }).live('mouseenter',function(){
            $(this).css('color','#d17100');
            $(this).css('cursor','pointer');
        }).live('mouseleave',function(){
            $(this).css('color','#000000');
        });
        $('#pui_seal').live('click',function(){
            $('#s_seal .s_edit').click();
        }).live('mouseenter',function(){
            $('.pui_sealline').css('background-color','#fdf8ec');
            $('.pui_sealblock').css('background-color','#f6c863');
            $(this).css('color','#d17100');
            $(this).css('cursor','pointer');
        }).live('mouseleave',function(){
            $(this).css('color','#000000');
            $('.pui_sealline').css('background-color','#eeeeee');
            $('.pui_sealblock').css('background-color','#999999');
        });
    },
    //右侧题型事件
    rightTypeMouse:function(){
        $('.questypehead').live('mouseenter',function(){
            $(this).css({'border':'0px solid #f6c863','background-color':'#fdf8ec','color':'#d17100'});
            $(this).find('.questypescore table').css('border','1px solid #ffffff');
            $(this).find('.questypescore td').css('border','1px solid #d17100');
            $(this).parent().css('border','1px solid #f6c863');


            $(this).find('.questypemenu .moveup').css('opacity','1');
            $(this).find('.questypemenu .movedn').css('opacity','1');
            $(this).find('.questypemenu .empty').css('opacity','1');
            $(this).find('.questypemenu .editscore').css('opacity','1');
            //上移动
            if($(this).parents('.partbody').last().find('.questypehead').first().attr('id')==$(this).attr('id')){
                $(this).find('.questypemenu .moveup').css('opacity','0.5');
            }
            //下移动
            if($(this).parents('.partbody').last().find('.questypehead').last().attr('id')==$(this).attr('id')){
                $(this).find('.questypemenu .movedn').css('opacity','0.5');
            }
            //清空
            if($(this).next().find('.quesbox').length==0){
                $(this).find('.questypemenu .empty').css('opacity','0.5');
                $(this).find('.questypemenu .editscore').css('opacity','0.5');
            }
            $(this).find('.questypemenu').css('display','block');
        }).live('mouseleave',function(){
            $(this).css({'border':'1px solid #ffffff','color':'#000000','background-color':'#ffffff'});
            $(this).find('.questypescore table').css('border','1px solid #000000');
            $(this).find('.questypescore td').css('border','1px solid #000000');
            $(this).parent().css('border','1px solid #ffffff');
            $(this).find('.questypemenu').css('display','none');
        }).find('.questypeheadbox').live('click',function(){
            $('#s_'+$(this).parent().attr('id')+' .s_edit').click();
        });
        $('.questypemenu .del').live('click',function(){
            if($(this).parents('.questypebody').last().find('.quesbox').length>0){
                if(!confirm("清除题型会清除题型下所有试题！")){
                    return;
                }
            }
            //删除题型
            var tmp_id=$(this).parent().parent().attr('id');
            var tmp_idn;
            var num=-1;
            var tmp_i=-1;
            $('.questypehead').each(function(i){
                if($(this).attr('id')==tmp_id){
                    //移入回收站
                    $(this).next().find('.quesbox').each(function(){
                        tmp_idn=$(this).attr('id').replace('quesbox','');
                        $.zujuan.addRecTest(tmp_idn);
                    });
                    tmp_i=i;
                    return;
                }
            });

            if(tmp_i!=-1){
                $('#quescountdetail tr',window.parent.document).each(function(i){
                    if(tmp_i==i){
                        $(this).remove();
                        return ;
                    }
                });
                $('.s_questype').each(function(i){
                    if(tmp_i==i){
                        $(this).remove();
                        return ;
                    }
                });
            }
            //更新试题篮
            $.myTest.updateMainTypes(0,0);
            $(this).parent().parent().parent().remove();
            $.zujuan.updateTypesNum();//更新题型序号
            editData.deltype(tmp_id);
            $.zujuan.resetTypesID();//更新题型编号
            $.zujuan.updateTestNum();//更新试题序号
            $.zujuan.changejfl(-1);//去掉填分栏最后一个
        });
        //批量设置题型分值
        $('.questypemenu .editscore').live('click',function(){
            var testtype=$(this).parent().parent();//题型ID
            var testtypeid=testtype.attr('id');//题型ID
            var defscore=editData.gettypelistmsg(testtypeid);
            var defscorearr=defscore[0].split(',');
            var i=0,j=0;
            var testnumber=new Array();
            var typeObj=testtype.next().find('.quesbox');
            var testlen=0;
            var tmplen=0;
            var _this;
            typeObj.each(function(i){
                _this=$(this);
                tmplen=_this.find('.quesindex').length;
                if(tmplen>0){
                    _this.find('.quesindex').each(function(i){
                        var intnum =$(this).find('b').html().split('．');
                        testnumber[j]=intnum[0];
                        j++;
                    });
                }else{
                    _this.find('.quesindexnum').each(function(i){
                        testnumber[j]=$(this).html();
                        j++;
                    });
                    tmplen= _this.find('.quesindexnum').length;
                }
                testlen+=tmplen;
            });
            $.zujuan.setScore(testlen,defscorearr,testnumber,'',testtypeid);
        });
        $('.questypemenu .empty').live('click',function(){
            if($(this).css('opacity')=='0.5') return;
            var typeHtmlID=$(this).parent().parent().attr('id');
            var thisTypeNextID=$(this).parent().next().attr('id');
            tmp='';
            tmp+='<input type="hidden" name="typeHtmlID" id="typeHtmlID" value="'+typeHtmlID+'">';
            tmp+='<input type="hidden" name="thisTypeNextID" id="thisTypeNextID" value="'+thisTypeNextID+'">';
            $.myDialog.normalMsgBox('yesorno','提示',450,'<b>您确定清除该题型下的试题？<b>'+tmp,3);
        });
        $('#yesorno .normal_yes').live('click',function(){
            var typeHtmlID=$('#typeHtmlID').val();
            var thisTypeNextID=$('#thisTypeNextID').val();
            $('#'+typeHtmlID+'').next().empty(); 
            $('#s_'+typeHtmlID).next().empty(); 
            var num=0;
            var style='';
            var tmp_str=$('#'+thisTypeNextID+'').find('.questypename').html();
            $('#quescountdetail tr',window.parent.document).each(function(){
                if($(this).find('td').first().attr('title')==tmp_str){
                    num=0-parseInt($(this).find('td').eq(2).html().replace('题'));
                    style=tmp_str;
                    return ;
                }
            });
            //更新试题篮
            $.myTest.updateMainTypes(num,style);
            editData.deltypetest(style);
            $(this).css('opacity',0.5);
            $.zujuan.updateTestNum();//更新试题序号
            $.zujuan.resetScore();//更新试题分值
            $('#yesorno .tcClose').click();
        });
        $('.questypemenu .edit').live('click',function(){
            //设置题型
            $('#s_'+$(this).parent().parent().attr('id')+' .s_edit').click();
        });
        $('.questypemenu .moveup').live('click',function(){
            //上移题型
            if($(this).css('opacity')==0.5) return;
            //移动题型
            $.zujuan.changeTypes($(this).parent().parent().parent().attr('id'),$(this).parent().parent().parent().prev().attr('id'));
            $(this).parent().parent().mouseenter();
        });
        $('.questypemenu .movedn').live('click',function(){
            //下移题型
            if($(this).css('opacity')==0.5) return;
            //移动题型
            $.zujuan.changeTypes($(this).parent().parent().parent().next().attr('id'),$(this).parent().parent().parent().attr('id'));
            $(this).parent().parent().mouseleave();
        });
    },
    //右侧分卷事件
    rightPartHeadEvent:function(){
        $('.parthead').live('mouseenter',function(){
                $(this).parent().css('border','1px solid #f6c863');
                $(this).css({'color':'#d17100','background-color':'#fdf8ec','border':'0px solid #f6c863'});
                $(this).find('.partmenu').css('display','block');
            }).live('mouseleave',function(){
                $(this).parent().css('border','1px solid #ffffff');
                $(this).css({'color':'#000000','background-color':'#ffffff','border':'1px solid #ffffff'});
                $(this).find('.partmenu').css('display','none');
        });
        $('.parthead').find('.partheadbox').live('click',function(){
            $('#s_'+$(this).parent().attr('id')+' .s_edit').click();
        });
        $('.parthead').find('.editpart').live('click',function(){
            $('#s_'+$(this).parent().parent().attr('id')+' .s_edit').click();
        });
    },
    //设置新题型事件
    addTypesEvent:function(){
        $('.addquestype').live('click',function(){
            var idName='newaddtypes';
            $.myDialog.tcLoadDiv('添加新题型',450,idName);
            var tmp_str='<div style="padding:10px 0;">'+
                '<table border="0" style="margin:0 auto;line-height: 2em;"><tbody>'+
                '<tr><td align="right"><strong>题型名称：</strong></td>'+
                '<td><input type="text" id="newquestypename" value="未命名题型"/></td></tr>'+
                '<tr><td align="right"><strong>默认每题分值：</strong></td>'+
                '<td><select type="text" id="newquestypescore">';
            for(var i=1;i<61;i++){
                tmp_str+='<option value="'+i+'">'+i+'</option>';
            }
            tmp_str+='</select></td></tr>'+
                '<tr><td valign="top" align="right"><strong>题型注释：</strong></td>'+
                '<td><textarea id="newquestypenote" style="width:300px;height:60px;resize:none;font-size:13px;">（新题型的注释）</textarea></td></tr>'+
                '<tr><td class="btm_btn" colspan="2" align="center">'+
                '<input type="button" value="确定" fjid="'+$(this).parent().parent().attr('id')+'" id="addtypesbutton" />'+
                '<input class="tcClose" did="'+idName+'" type="button" value="取消"/>'+
                '</td></tr>'+
                '</tbody></table></div>';
            $('#'+idName+' .content').html(tmp_str);
            $.myDialog.tcShowDiv(idName);
            $('#div_shadow').css({'display':'block'});
        });
        //添加新题型
        $('#addtypesbutton').live('click',function(){
            var tmp_id=$(this).attr('fjid');
            var tmp_i=0;
            var tmp_str_1=$('#'+tmp_id).next().find('.questypeindex').last().find('b').html();
            var status='';
            var tmp_fj=tmp_id.replace('parthead','');
            var tmp_title=$('#newquestypename').val();
            var tmp_text=$('#newquestypenote').val().replace(/\n/g,'<br/>');
            var tmp_dscore=$('#newquestypescore option:selected').val()+'|1|0';
            $('.questypename').each(function(){
                if($(this).html()==tmp_title){
                    $.myDialog.showMsg('题型名称不能重复!',1);
                    if(status==''){
                        status='0';
                    }
                }
            })
            if(status=='0'){
                return false;
            }
            tmp_i+=parseInt($('#parthead'+tmp_fj).next().find('.questype').length);
            tmp_i+=1;
            var tmp_str='<div class="questype" id="questype'+tmp_fj+'_'+tmp_i+'">'+
                '<div class="questypehead" id="questypehead'+tmp_fj+'_'+tmp_i+'">'+
                '<div class="questypemenu" style="display: none;">'+
                '<a class="empty" style="opacity: 0.5;">清空</a>'+
                '<a class="del">删除</a>'+
                '<a class="edit">设置</a>'+
                '<a class="moveup" style="opacity: 1;">上移</a>'+
                '<a class="movedn" style="opacity: 0.5;">下移</a>'+
                '</div>'+
                '<div class="questypeheadbox" id="questypeheadbox'+tmp_fj+'_'+tmp_i+'">'+
                '<table border="0" width="100%" cellpadding="0" cellspacing="0"><tbody>'+
                '<tr>'+
                '<td width="1">'+
                '<div class="questypescore" id="questypescore'+tmp_fj+'_'+tmp_i+'" style="width:120px;display:;">'+
                '<table title="评分栏" border="1" cellpadding="0" cellspacing="0" bordercolor="#666666"><tbody>'+
                '<tr>'+
                '<td width="55" height="20" align="center"> 评卷人 </td>'+
                '<td width="55" align="center"> 得  分 </td>'+
                '</tr>'+
                '<tr>'+
                '<td height="30" align="center"> </td>'+
                '<td> </td>'+
                '</tr>'+
                '</tbody></table>'+
                '</div>'+
                '</td>'+
                '<td>'+
                '<div class="questypetitle">'+
                '<span class="questypeindex">'+
                '<b></b>'+
                '</span>'+
                '<span class="questypename" id="questypename'+tmp_fj+'_'+tmp_i+'">'+tmp_title+'</span>'+
                '<span class="questypedscore" id="questypedscore'+tmp_fj+'_'+tmp_i+'"></span>'+
                '<span class="questypenote" id="questypenote'+tmp_fj+'_'+tmp_i+'">'+tmp_text+'</span>'+
                '</div>'+
                '</td>'+
                '</tr>'+
                '</tbody></table>'+
                '</div>'+
                '</div>'+
                '<div class="questypebody">'+
                '</div>'+
                '</div>';
            $('#'+tmp_id).next().append(tmp_str);
            tmp_str='<div class="s_questype" id="s_questype'+tmp_fj+'_'+tmp_i+'">'+
                '<div class="s_questypehead" id="s_questypehead'+tmp_fj+'_'+tmp_i+'">'+
                '<a class="s_itemicon"/>'+
                '<a class="s_display_open"/>'+
                '<a class="s_edit" title="设置题型头部"/>'+
                '<span class="s_questypeindex" id="s_questypeindex'+tmp_fj+'_'+tmp_i+'">'+
                '<b></b>'+
                '</span>'+
                '<span class="s_questypename" id="s_questypename'+tmp_fj+'_'+tmp_i+'">'+tmp_title+'</span>'+
                '</div>'+
                '<div class="s_questypebody ui-sortable">'+
                '</div>'+
                '</div>';
            $('#s_'+tmp_id).next().append(tmp_str);

            $.zujuan.updateTypesNum(); //更新题型序号

            //添加main题型
            var len=parseInt($('#parthead'+tmp_fj).next().find('.questype').length);
            if(tmp_fj==2) len+=parseInt($('#parthead1').next().find('.questype').length);
            $.myTest.addMainTypes(tmp_title,0,len-1);
            //写入cookie
            editData.addtype(tmp_fj,tmp_title,tmp_text,0,tmp_dscore);

            $(this).next().click();
            $.zujuan.changejfl(1); //增加填分栏最后一个
        });
    },
    //批量设置分数
    //testlen试题长度 defscorearr分值数组 testnumber试题序号数组 testidarr试题id，testtypeid题型id
    setScore:function(testlen,defscorearr,testnumber,testidarr,testtypeid){
        var idName="setscore";
            var title="修改试题分值";
            var tmp_score='';
            if(testlen>1){
                tmp_score='<div class="setscoreall">' +
                    '<span class="setscoreleft">批量设分：</span>' +
                    '<span><input type="text" name="setallscore" id="setallscore" size="3" maxlength="4" /></span> ' +
                    '<span><a herf="javascript:void(0);" class="sBtn" id="setallscorebutton">设置</a></span>' +
                    '</div>';
            }
            if(tmp_score==''){
                tmp_score+='<div class="setscorelist">';
            }else{
                tmp_score+='<div class="setscorelist mt40">';
            }
            for(var i in defscorearr){
                tmp_score+='<div><span class="setscoreleft">第'+testnumber[i]+'题 分值： </span><span><input type="text" name="setscore" size="3" maxlength="4" value="'+defscorearr[i]+'" class="resetscore" ></span></div>';
            }
            tmp_score+='<input type="hidden" name="testid" value="'+testidarr+'" class="needtestid"><input type="hidden" name="testtypeid" value="'+testtypeid+'" class="needtesttypeid">';
            tmp_score+='</div>';
            $.myDialog.normalMsgBox(idName,title,350,tmp_score,3);
            if(testlen<8){
                $('#setscore .setscorelist').css({'height':testlen*30});
                $.myDialog.tcDivPosition(idName);
            }
    },
    //右侧试题事件
    rightTestEvent:function(){
        $('.quesbox').live('mouseenter',function(){
            $(this).css('border','1px solid #336699');
            $(this).find('.quesopmenu .moveup').css('opacity','1');
            $(this).find('.quesopmenu .movedn').css('opacity','1');
            //上移动
            if($(this).parent().find('.quesbox').first().attr('id')==$(this).attr('id')){
                $(this).find('.quesopmenu .moveup').css('opacity','0.5');
            }
            //下移动
            if($(this).parent().find('.quesbox').last().attr('id')==$(this).attr('id')){
                $(this).find('.quesopmenu .movedn').css('opacity','0.5');
            }

            $(this).find('.quesopmenu').css('display','block');
        }).live('mouseleave',function(){
            $(this).css('border','1px solid #ffffff');
            $(this).find('.quesopmenu').css('display','none');
        });

        $('.quesdiv').live('click',function(){
            var tmp_id=$('#s_'+$(this).attr('id'));
            $('#paperstruct_body').animate({"scrollTop":tmp_id.offset().top+$('#paperstruct_body').scrollTop()-$('#topdiv').offset().top-$('#topdiv').height()-100},500,function(){
                $.myTest.showAlpha(tmp_id);
            });
        });

        //改分
        $('.quesopmenu .setscore').live('click',function(){
            var testtypeid=$(this).parent().parent().parent().prev().attr('id');//题型ID
            var testid=$(this).parent().parent().attr('id').replace('quesbox','');//试题ID
            var defscore=editData.gettypelistmsg(testtypeid,testid);
            var defscorearr=defscore[0].split(',');
            var testidarr=defscore[1].substr(1);
            var i=0;
            var testnumber=new Array();
            var testlen=$('#quesdiv'+testid+' .quesindex ').length;
            if(testlen>0){
                $('#quesdiv'+testid+' .quesindex ').each(function(i){
                    var intnum =$(this).find('b').html().split('．');
                    testnumber[i]=intnum[0];
                });
            }else{
                $('#quesdiv'+testid+' .quesindexnum ').each(function(i){
                    testnumber[i]=$(this).html();
                });
                testlen=$('#quesdiv'+testid+' .quesindexnum ').length;
            }
            $.zujuan.setScore(testlen,defscorearr,testnumber,testidarr,testtypeid);
        });
        //批量设置分数 辅助工具
        $('#setallscorebutton').live('click',function(){
            var score=$('#setallscore').val();
            var tmp=$.zujuan.checkNumeric(score);
            if('success'!=tmp){
                $.myDialog.normalMsgBox('showmsg','错误信息',450,tmp,2);
                return false;
            }
            $('#setscore .resetscore').each(function(){
                $(this).val(score);
            });
        });
        $('#setscore .normal_yes').live('click',function(){
            var scorearr=new Array();
            var idarr=new Array();
            var i=0;
            var j;
            var errormsg='';
            var tmp='';
            $('.resetscore').each(function(i){
                scorearr[i]=$(this).val();
                tmp= $.zujuan.checkNumeric(scorearr[i]);
                if('success'!=tmp){
                    errormsg=tmp;
                }
            });
            if(errormsg!=""){
                $.myDialog.showMsg(errormsg,'1');
                return false;
            }
            var rsetscore=scorearr.join(',');
            var testid=$('.needtestid').val();
            var testtypeid=$('.needtesttypeid').val();
            
            if(testid===''){
                //题型
                var testnum=0;
                $('#'+testtypeid).next().find('.quesbox').each(function(){
                    rsetscore='';
                    var testoldid=$(this).attr('id').replace('quesbox','');
                    var num=$(this).find('.quesindex').length;
                    if(num===0) num=$(this).find('.quesindexnum').length;
                    
                    for(var i=0;i<num;i++){
                        if(rsetscore=='') rsetscore=scorearr[testnum+i];
                        else rsetscore+=','+scorearr[testnum+i];
                    }
                    testnum+=num;
                    editData.edittestscore(testtypeid,testoldid,rsetscore);
                });
            }else{
                idarr=testid.split(',');
                for(j=0;j<idarr.length;j++){
                    editData.edittestscore(testtypeid,idarr[j],rsetscore);
                }
            }
            
            $.zujuan.resetScore(testtypeid);//更新试题分值
            $('#setscore .tcClose').click();
        });
        
        //设置选做题
        $('.quesopmenu .choosetest').live('click',function(){
            //当前试题id
            var testid = $(this).parent().parent().attr('id').replace('quesbox','');
            
            //当前选择的题是否是选做题 如果是获取对应数据
            var typesArr=$.zujuan.getTypesByID();
            var testlist=new Array(); //可以做选做题的试题列表
            var j=0;
            $('.quesbox').each(function(){
                var _this=$(this);
                if(typesArr[_this.attr('typesid')]['IfDo']=='0'){
                    testlist[j]=_this.attr('id').replace('quesbox','');
                    j++;
                }
            });
            
            if(testlist.length<1){
                alert('没有可作为选做题的试题。');
                return false;
            }
            var chooselist=editData.getChooseList(testid); //选做题列表 当前试题对应的选做题组及数据列表

            //弹出对话框
            var idName="choosetest";
            var title="配置选做题";
            var tmp_score='';
            //选做题是否
            tmp_score='<div class="setchoosetest">' +
                    '<table border="1" class="table f-roman" bordercolor="#ccc" width="100%" cellspacing="0" cellpadding="15"><tbody>'+
                        '<tr><td align="center" width="100"><b>选做题组</b></td>'+
                            '<td><select name="selectchoosenum" class="selectchoosenum">'+
                                '</select>'+
                            '<label><input name="addchooseselect" class="addchooseselect" type="button" value="新增"/></label>'+
                            '<label><input name="delchooseselect" class="delchooseselect" type="button" value="删除"/></label>'+
                        '</td></tr>';
                    
            //选做题类型 几选几
            tmp_score+='<tr><td align="center"><b>选做类型</b></td>'+
                            '<td><span><select name="setchoosemax" class="setchoosemax">'+
                            '</select></span>选'+
                            '<span><select name="setchoosemin" class="setchoosemin">'+
                            '</select></span>'+
                        '</td></tr>';
            
            //选做题列表
            tmp_score+='<tr><td align="center"><b>试题列表</b></td>'+
                            '<td><span class="choosetestvalue"></span></td></tr>'+
                        '</table></div>';
            $.myDialog.normalMsgBox(idName,title,450,tmp_score,3);
            
            //选做数据处理
            var chooseListGroup=new Array; //每组选做题对应的试题列表
            var chooseListDo=new Array; //每组选做题对应的试题选做数量
            var tmpList1,tmpList2;
            var chooseNum=0;
            for(var i in chooselist[1]){
                chooseNum++;
                tmpList1=chooselist[1][i].split(';');
                for(var j in tmpList1){
                    tmpList2=tmpList1[j].split('|');
                    if(typeof(chooseListGroup[i])=='undefined' || chooseListGroup[i]=='') chooseListGroup[i]=tmpList2[0];
                    else chooseListGroup[i]+=','+tmpList2[0];
                    chooseListDo[i]=tmpList2[4];
                }
            }
            //载入选做题组
            var currentChoose=chooselist[0].split('|');
            var select=''; //选项内容
            var thisSelect=''; //当前是否选中
            if(chooseNum>0){
                for(var i=0;i<chooseNum;i++){
                    if(currentChoose.length>1 && currentChoose[3]==(i+1)) thisSelect=' selected="selected" ';
                    else thisSelect='';
                    select+='<option subtest="'+chooseListGroup[(i+1)]+'" do="'+chooseListDo[i+1]+'" value="'+(i+1)+'"'+thisSelect+'>第'+(i+1)+'组</option>';
               }
            }else{
                select+='<option value="1" subtest="" do="1" selected="seleced">第1组</option>';
            }
            $('.selectchoosenum').html(select);
            
            //获取当前试题所在选做题组有几道题
            var currentChooseNum=0;
            if(currentChoose.length>1){
                var tmpList1=chooselist[1][currentChoose[3]].split(',');
                currentChooseNum=tmpList1.length;
            }
            
            //载入几选几数据
            var select='';
            for(var i=2;i<6;i++){
                if(currentChooseNum>1 && currentChooseNum==i) thisSelect=' selected="selected" ';
                else if(currentChooseNum<1 && i==3) thisSelect=' selected="selected" ';
                else thisSelect='';
                 select+='<option value="'+i+'"'+thisSelect+'>'+i+'</option>';
            }
            $('.setchoosemax').html(select);
            
            select='';
            for(var i=1;i<5;i++){
                if(currentChooseNum>1 && currentChoose[4]==i) thisSelect=' selected="selected" ';
                else if(currentChooseNum<1 && i==1) thisSelect=' selected="selected" ';
                else thisSelect='';
                 select+='<option value="'+i+'"'+thisSelect+'>'+i+'</option>';
            }
            $('.setchoosemin').html(select);
            
            //载入所有试题列表
            select='';
            var tmpList1,tmpList2,tmpOrder;
            for(var i in testlist){
                    tmpOrder=new Array;
                    var k=0;
                    $('#quesdiv'+testlist[i]).find('.quesindex').each(function(){
                        tmpOrder[k]=$(this).find('b').text().replace('．','');
                        k++;
                    });
                    
                    select+='<span style="display:inline-block"><label><input name="choosevalue[]" class="choosevalue" type="checkbox" value="'+testlist[i]+'"/>第'+tmpOrder.join('、')+'题</label></span> ';
            }
            $('.choosetestvalue').html(select);
            $.zujuan.chooseLoadDefault();//载入当前选做题组数据
        });
        //选做题组切换
        $('.selectchoosenum').live('change',function(){
            $.zujuan.chooseLoadDefault();//载入当前选做题组数据
        });
        //选做题选中和取消
        $('.choosevalue').live('change',function(){
            //修改当前题组所包含的试题数据
            var subTest=$('.selectchoosenum').find('option:selected').attr('subtest');
            if(typeof(subTest)=='undefined') subTest='';
            var currentTest=$(this).val();
            if($(this).attr('checked')=='checked'){
                if((','+subTest+',').indexOf(','+currentTest+',')==-1){
                    if(subTest=='') subTest=currentTest;
                    else subTest+=','+currentTest;
                }
            }else{
                if((','+subTest+',').indexOf(','+currentTest+',')!=-1){
                    if(subTest==currentTest) subTest='';
                    else subTest=(','+subTest+',').replace(','+currentTest+',','').replace(/(^,*)|(,*$)/g, "");
                }
            }
            $('.selectchoosenum').find('option:selected').attr('subtest',subTest);
        });
        //几选几中最后一个几改变时的事件
        $('.setchoosemin').live('change',function(){
            var maxdo=$('.setchoosemax').find('option:selected').val();
            var mindo=$(this).find('option:selected').val();
            
            if(maxdo<=mindo){
                alert('您设置的数量超出最大数量。');
                return false;
            }
            $('.selectchoosenum option:selected').attr('do',mindo);
        });
        //新增选做题组
        $('.addchooseselect').live('click',function(){
            //判断当前题组是否已经设置过
            var thisBool=$.zujuan.chooseCheckCurrent();
            if(thisBool==false){
                return false;
            }
            //插入新的题组
            var maxnum=$(".selectchoosenum").find('option').length+1;
            $(".selectchoosenum").append("<option value='"+(maxnum)+"'>第"+(maxnum)+"组</option>"); 
            $(".selectchoosenum").find('option').attr('selected',false);
            $(".selectchoosenum").find('option:last').attr('selected','selected');
            
            $.zujuan.chooseLoadDefault();//载入当前选做题组数据
        });
        //删除选做题组
        $('.delchooseselect').live('click',function(){
            //提示确认删除当前题组
            if(confirm('是否确认删除当前选做题组。')){
                $('.selectchoosenum').find("option:selected").remove();
                if($('.selectchoosenum').find('option').length<=0){
                    //是否是最后一个选做题组 如果是则点击对话框确定按钮
                    $('#choosetest .normal_yes').click();
                    return true;
                }else{
                    //题组排序
                    $('.selectchoosenum').find('option').each(function(i){
                        $(this).val(i+1);
                        $(this).html('第'+(i+1)+'组');
                    });
                }
            }
            $.zujuan.chooseLoadDefault();//载入当前选做题组数据
        });
        //选做题确认
        $('#choosetest .normal_yes').live('click',function(){
            //如果没有选做题则删除所有
            var optionLen=$('.selectchoosenum').find('option').length;
            var firstOption=$('.selectchoosenum').find('option:eq(0)').attr('subtest');
            if(optionLen==0 || firstOption==''){
                editData.chooseClearAll();
                //重置选做题
                $.zujuan.resetChoose();
                $.zujuan.resetScore();
                $('#choosetest .tcClose').click();
                return true;
            }
            
            //判断当前题组是否已经正确设置
            var thisBool=$.zujuan.chooseCheckCurrent();
            if(thisBool==false){
                return false;
            }
            
            //对已经存在的数据进行同步cookie
            editData.chooseClearAll();
            $('.selectchoosenum').find('option').each(function(){
                var thisdo=$(this).attr('do');
                var thisgroup=$(this).val();
                if(typeof(thisdo)=='undefined' || thisdo=='') thisdo=1;
                editData.chooseSetValue($(this).attr('subtest'),thisgroup,thisdo);
            });
            //重置选做题
            $.zujuan.resetChoose();
            $.zujuan.resetScore();
            $('#choosetest .tcClose').click();
        });
    },
    //设置综合属性
    setzonghe:function(){
        var thisstyle=editData.getAttr(4);
        var setstyle=$('#subjectmore',window.parent.document);
        if(typeof(setstyle)=='undefined') return false;
        if(setstyle.val()==thisstyle) setstyle.attr('checked','checked');
        else setstyle.attr('checked',false);
    },
    //检查选做题数据是否符合要求
    chooseCheckCurrent:function(){
        //判断当前题组是否已经设置过
        var subTest=$('.selectchoosenum').find('option:selected').attr('subtest');
        if(subTest==''){
            alert('请先设置当前题组数据。');
            return false;
        }
        //判断当前题组是否设置正确
        var tmpArr=subTest.split(',');
        var maxdo=parseInt($('.setchoosemax option:selected').val());
        var mindo=parseInt($('.setchoosemin option:selected').val());
        if(tmpArr.length!=maxdo){
            alert('请先正确设置当前题组数据数量。');
            return false;
        }
        
        if(maxdo<=mindo){
            alert('选做类型错误，请设置例如3选1这样的数据。');
            return false;
        }
        
        //大题在页面上必须连续
        var tmpArr=new Array;
        var i=0;
        $('.choosetestvalue').each(function(){
            if($(this).attr('checked')=='checked'){
                tmpArr[i]=$(this).parent().text().replace(/第|题/g,'');
                i++;
            }
        });
        for(i=1;i<tmpArr.length;i++){
            if(tmpArr[i]-tmpArr[i-1]!=1){
                alert('选做题序号必须连续。');
                return false;
            }
        }
        
        return true;
    },
    //载入选做题数据
    chooseLoadDefault:function(){
        //获取当前选做题组及所选试题
        var currentGroup=$('.selectchoosenum').find('option:selected').val();
        var currentTestList=$('.selectchoosenum').find('option:selected').attr('subtest');
        
        //几选几问题
        var subtest=$('.selectchoosenum option:selected').attr('subtest');
        var thisdo=$('.selectchoosenum option:selected').attr('do');
        if(typeof(thisdo)=='undefined' || thisdo=='') thisdo=1;
        var subtestlen=3;
        if(typeof(subtest)!='undefined' && subtest!=''){
            subtestlen=subtest.split(',').length;
        }
        $('.setchoosemax option').attr('selected',false);
        $('.setchoosemax option:eq('+(subtestlen-2)+')').attr('selected','selected');
        $('.setchoosemin option').attr('selected',false);
        $('.setchoosemin option:eq('+(thisdo-1)+')').attr('selected','selected');
        
        //获取已经在选做题组中的其他试题
        var tmpObject=[];
        $('.selectchoosenum').find('option').each(function(){
            var tmpList=$(this).attr('subtest');
            if(tmpList!='' && currentGroup!=$(this).val()) tmpObject.push(tmpList);
        });
        var testList=tmpObject.join(',');

        //试题列表中排除已经在选做题组的试题
        //选中当前选做题组中的数据
        $('.choosevalue').each(function(){
            $(this).attr('checked',false);
            $(this).removeAttr("disabled"); 
            var thisval=$(this).val();
            if((','+currentTestList+',').indexOf(','+thisval+',')!=-1){
                $(this).attr('checked','checked');
            }
            if((','+testList+',').indexOf(','+thisval+',')!=-1){
                $(this).attr("disabled","disabled");
            }
        });
    },
    //验证数据是否是小于两位的数字 或小数
    checkNumeric:function(num){
        var reg=/^[1-9]?[0-9](\.[1-9])?$/;
        if(reg.test(num)){
            return 'success';
        }
        return "所设分值必须为两位数字或小数且不能为空！";
    },
    //修改题型下面的试题分数显示样式
    setScoreType:function(testtypeid,scoremsg){
        if(scoremsg=='') return;
        var i;
        var testmsg;
        var substart=testtypeid.indexOf('d')*1+1;
        var idnum=testtypeid.substr(substart,testtypeid.length);
        if(scoremsg[0]=='-1'){//分数不同
            testmsg=$.zujuan.getScoreText(scoremsg[1].length,scoremsg[2],1);//显示试题分值
            $('#'+testtypeid).next().find('.quesscore').each(function(i){
                $(this).html(' (本题'+scoremsg[1][i]+'分) ');
            })
        }else{//分数相同
            if(scoremsg[1]!=""){
                testmsg= $.zujuan.getScoreText(scoremsg[1].length,scoremsg[1][0]);//显示试题分值
            }else{
                testmsg='';
            }
            $('#'+testtypeid).next().find('.quesscore').each(function(){
                $(this).html('');
            })
        }
        $('#questypedscore'+idnum).html(testmsg);
    },
    //试题详细事件
    testDetailEvent:function(){
        $('.quesopmenu .detail').live('click',function(){
            var tmp_id=$(this).parents('.quesbox').last().attr('id').replace('quesbox','');
            var idName="textdiv";
            var tmp_str='数据加载中...';
            $.myDialog.tcLoadDiv("试题详细",600,idName);
            $('#'+idName+' .content').html(tmp_str);
            $.myDialog.tcShowDiv(idName);
            $('#div_shadow').css({'display':'block'});

            $.post(U('Index/getDetailTestById'),{"id":tmp_id,'width':$.zujuan.docWidth,"rand":Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                tmp_str='<div style="border:1px solid #ddd;background:#fff;width:538px; margin:10px auto; margin-bottom:10px;height:295px;overflow:hidden;overflow-y:scroll;padding:5px;">'+
                    '<div style="background:#f2f2f2; padding:10px; line-height:22px; width:502px;">'+
                    '<div><b>题号：</b><span>'+data['data'][0][tmp_id]['testid']+'</span>，<b>题型：</b>'+data['data'][0][tmp_id]['typesname']+'，<b>难度：</b>'+data['data'][0][tmp_id]['diffname']+'，<b>日期：</b>'+data['data'][0][tmp_id]['firstloadtime']+'</div>'+
                    '<div><b>标题/来源：</b>'+data['data'][0][tmp_id]['docname']+'</div>'+
                    '</div><div><div class="quesbody">'+data['data'][0][tmp_id]['test']+'</div>'+
                    '<div class="quesanswer" style="display:block;"><p><font color="009FE9">【答案】</font>'+ $.myTest.removeLeftTag(data['data'][0][tmp_id]['answer'],'<p>')+'</div>';
                if(data['data'][0][tmp_id]['analytic']) tmp_str+='<div class="quesparse" style="display:block;"><p><font color="009FE9">【解析】</font>'+$.myTest.removeLeftTag(data['data'][0][tmp_id]['analytic'],'<p>')+'</div>';
                if(data['data'][0][tmp_id]['analytic']) tmp_str+='<div class="quesremark" style="display:block;"><p><font color="009FE9">【备注】</font>'+$.myTest.removeLeftTag(data['data'][0][tmp_id]['remark'],'<p>')+'</div>';
                tmp_str+='</div></div>';
                $('#'+idName+' .content').html(tmp_str);
                $.myDialog.tcDivPosition(idName);
            });
        });
    },
    //替换框事件
    testReplaceEvent:function(){
        $('.quesopmenu .replace').live('click',function(){
            var tmp_allid='';
            //替换
            $(this).parents('.questypebody').last().find('.quesbox').each(function(){
                tmp_allid+=','+$(this).attr('id').replace('quesbox','');
            });
            tmp_allid=tmp_allid.substring(1);
            var tmp_id=$(this).parents('.quesbox').last().attr('id').replace('quesbox','');
            var idName="replacediv";
            var tmp_str='数据加载中请稍候...';
            $.myDialog.tcLoadDiv("试题替换--准备替换当前第"+$('#s_quesdiv'+tmp_id+' .s_quesindex').html().replace('．','')+"题【ID="+tmp_id+"】",580,idName);
            $('#'+idName+' .content').html(tmp_str);
            $.myDialog.tcShowDiv(idName);
            $.zujuan.getSameTest(tmp_id,tmp_allid,idName);
        });
        //替换框内点击刷新，替换试题
        $('.sameRefresh').live('click',function(){
            var idName="replacediv";
            var tmp_str='数据加载中请稍候...';
            $('#'+idName+' .content').html(tmp_str);
            $.myDialog.tcShowDiv(idName);
            var tmp_id=$(this).attr('tmpId');
            var tmp_allid=$(this).attr('tmpAllId');
            var idName=$(this).attr('idName');
            $.zujuan.getSameTest(tmp_id,tmp_allid,idName);
        });
        //弹出框点击显示答案
        $('#diaqueslistbox .diaquesbody').live('click',function(){
            if($(this).next().css('display')=='none'){
                $(this).next().css('display','block');
            }else{
                $(this).next().css('display','none');
            }
        });
        //弹出框点击显示试题
        $('#diaquesswitch .diaques').live('click',function(){
            $.zujuan.showDiaTest((parseInt($(this).html())-1));//替换  试题的显示层
        });
        //确定替换
        $('.diarepalcebtn').live('click',function(){
            //防止重复点击
            if($(this).checkClick()==false) return false;
            $('#diaqueslistbox .diaquesbox').each(function(){
                if($(this).css('display')=='block'){
                    var tmp_str=$(this).find('.diaquesbody div').html();
                    var tmp_i=1; //小题数量 默认1
                    var tmp_str_1,tmp_str_2,tmp_str_3;

                    if($(this).find('.diaquesbody .quesindex').length>0){
                        tmp_i=$(this).find('.diaquesbody .quesindex').length;
                    }else if($(this).find('.diaquesbody .quesindexnum').length>0){
                        tmp_i=$(this).find('.diaquesbody .quesindexnum').length;
                    }

                    //此处添加兼容自有试题匹配的正则表达式 2015-4-18 doowan
                    tmp_str_1=$('#replacediv .title b').html().match(/【ID=(\d+|c\d+)】/i);//原试题id
                    var tmp_id=tmp_str_1[1];

                    //加入回收站
                    $.zujuan.addRecTest(tmp_id);
                    var tmp_j=1
                    if($('#quesdiv'+tmp_id+' .quesindex').length>0){
                        tmp_j=$('#quesdiv'+tmp_id+' .quesindex').length;//原试题小题
                    }else if($('#quesdiv'+tmp_id+' .quesindexnum').length>0){
                        tmp_j=$('#quesdiv'+tmp_id+' .quesindexnum').length;//原试题小题
                    }

                    tmp_str_1=$(this).find('td:eq(0)').html().split(/<SPAN[^>]*>/i);
                    var tmp_source=tmp_str_1[3].substring(0,tmp_str_1[3].length-7);//试题来源
                    var tmp_newid=tmp_str_1[1].match(/\d+/g);//试题id
                    tmp_str_2=tmp_str_1[2].split(/<\/SPAN>/i);
                    var tmp_newdiff=tmp_str_2[0];//试题难度
                    //移出回收站
                    $.zujuan.delRecTest(tmp_newid);

                    if(tmp_j!=tmp_i){
                        var num=parseInt(tmp_i)-parseInt(tmp_j);
                        //更新试题篮
                        $.myTest.updateMainTypes(num,$('#quesbox'+tmp_id).parent().prev().find('.questypename').html());
                    }
                    tmp_str_3=$('#s_quesdiv'+tmp_id).attr('title').split(' | ');
                    $('#s_quesdiv'+tmp_id).attr('title','题号：'+tmp_newid+' | 难度：'+tmp_newdiff+' | '+tmp_source+' | '+tmp_str_3[tmp_str_3.length-1]);
                    $('#s_quesdiv'+tmp_id+' .s_questitle').html(tmp_source);
                    $('#s_quesdiv'+tmp_id+' .s_quesindex').attr('queschildnum',tmp_i);
                    $('#quesdiv'+tmp_id).html(tmp_str).attr('id','quesdiv'+tmp_newid);
                    $('#quesbox'+tmp_id).attr('id','quesbox'+tmp_newid);
                    $('#s_quesdiv'+tmp_id).attr('id','s_quesdiv'+tmp_newid);
                    editData.replacetest(tmp_id,tmp_newid+'|'+tmp_i)//改变cookie
                    $('#replacediv .tcClose').click();
                    $.zujuan.updateTestNum();//更新试题序号
                    $.zujuan.resetScore();//更新试题分值
                }
            });
        });
    },
    //一键更换试卷试题
    oneKeyReplaceTest:function(){
        var self=this;
        //批量替换弹框
        $('#replace').live('click',function(){
            var tmp_score = '<div>确定替换整个试卷吗？</div>';

            $.myDialog.normalMsgBox('replaceAlert','提示',350,tmp_score,3);
        });
        //确定批量替换
        $('#replaceAlert .normal_yes').live('click',function(){
            $('#replaceAlert .tcClose').click();
            //防止重复点击
            if($(this).checkClick()==false) return false;
            var testID=editData.gettestid();//获取试卷试题ID
            if(testID){
                $.myDialog.normalMsgBox("replacediv","试卷替换",580,$.myCommon.loading(),5);
                $.post(U('Index/getSameTestIDByID'),{'id':testID},function(data){
                    if($.myCommon.backLogin(data)==false){
                        return false;
                    }
                    if(data['status']==1) {
                        var e=data['data'];
                        for (var i in e) {
                            //改变cookie,没有可替换的试题就不替换
                            if(i!=e[i]['testID']) {
                                editData.replacetest(i, e[i]['testID'] + '|' + e[i]['testNum'])
                            }
                        }
                        $('#pui_body').html('');
                        self.loadPaper();
                        $.myDialog.tcCloseDiv("replacediv");
                    }
                })
            }
        })
    },
    //试题替换，查找类型相同的试题
    getSameTest:function(tmp_id,tmp_allid,idName){
        $.post(U('Index/getSameById'),{"id":tmp_id,"allid":tmp_allid,"rand":Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                $.myDialog.tcCloseDiv(idName);//失败时关闭数据加载提示信息
                return false;
            }
            var total=data['data']['total'];
            var dataArr=data['data']['data'];
            var tmp_str='<div id="diaquesswitch" class="rel pagebox">';
            var tmp_len=dataArr[1];
            if(tmp_len>10) tmp_len=10;
            for(var tmp_i=0;tmp_i<tmp_len;tmp_i++){
                tmp_str+='<a class="diaques">'+(tmp_i+1)+'</a>';
            }
            if(total>10){
                tmp_str+='<a class="sameRefresh" href="javascript:;" idname="'+idName+'" tmpId="'+tmp_id+'" tmpAllId="'+tmp_allid+'">刷新</a>';
            }
            tmp_str+='<a class="replaceTest diarepalcebtn" href="#">确认替换</a>';
            tmp_str+='</div>';
            
            tmp_str+='<div id="diaqueslistbox">';
            for(tmp_i in dataArr[0]){
                tmp_str+='<div class="diaquesbox" style="position: relative; cursor: default; display: none;">'+
                    '<div class="diaquestitle">'+
                    '<table border="0"><tbody><tr>'+
                    '<td>题号ID：<span style="font-weight:bold;">'+dataArr[0][tmp_i]['testid']+'</span>'+
                    '，难度：<span>'+dataArr[0][tmp_i]['diffname']+'</span>'+
                    '，标题：<span>'+dataArr[0][tmp_i]['docname']+'</span>'+
                    '</td>'+
                    '<td align="right">'+
                    '</td>'+
                    '</tr></tbody></table>'+
                    '</div>'+
                    '<div class="diaquescontent" style="padding:5px;line-height:18px;">'+
                    '<div class="diaquesbody">'+
                    '<div>'+ $.myTest.changeTagToNum(dataArr[0][tmp_i]['test'],1)+'</div>'+
                    '</div>'+
                    '<div class="diaquesanswerparse">'+
                    '<div><p><font color="#00a0e9">【答案】</font>'+$.myTest.changeTagToNum(dataArr[0][tmp_i]['answer'],1)+'</div>'+
                    '<div><p><font color="#00a0e9">【解析】</font>'+$.myTest.changeTagToNum(dataArr[0][tmp_i]['analytic'],1)+'</div>'+
                    '<div><p><font color="#00a0e9">【备注】</font>'+$.myTest.changeTagToNum(dataArr[0][tmp_i]['remark'],1)+'</div>'+
                    '</div>'+
                    '</div>'+
                    '</div>';
            }
            tmp_str+='</div>';
            $('#'+idName+' .content').html(tmp_str);
            $.myDialog.tcDivPosition(idName);
            $.zujuan.showDiaTest(0);//替换  试题的显示层
        });
    },
    //替换  试题的显示层
    showDiaTest:function(num){
        if($('#diaquesswitch').length>0){
            $('#diaquesswitch a').removeClass('divques_current');
            $('#diaquesswitch a:eq('+num+')').addClass('divques_current');
            $('#diaqueslistbox .diaquesbox').css({'display':'none'});
            $('#diaqueslistbox .diaquesbox:eq('+num+')').css({'display':'block'});
        }
    },
    //删除试题
    testDeleteEvent:function(){
        $('.quesopmenu .del').live('click',function(){
            var testid=$(this).parents('.quesbox').last().attr('id').replace('quesbox','');//题型ID
            var tmp_str=$(this).parents('.questype').last().find('.questypename').html();
            var ifchoose=editData.checkifchoose(testid);
            if(ifchoose[0]==0){
                $.myDialog.normalMsgBox('delthistestmsg','信息提示',450,'<div><b>您确定删除该试题？</b><input type="hidden" name="delthistest" value="'+testid+'"><input type="hidden" name="thisTestMsg" value="'+tmp_str+'"></div>',3);
            }else{
                var ifchoose_str=ifchoose[1].join(',');
                $.myDialog.normalMsgBox('delchoosetestmsg','信息提示',450,'<div><b>该题为多选题，您确定删除？</b><input type="hidden" name="delchoosemsg" value="'+ifchoose_str+'"><input type="hidden" name="tmp_strmsg" value="'+tmp_str+'"></div>',3);
            }
        });
        //确定删除当前试题
        $('#delthistestmsg .normal_yes').live('click',function(){
            var testid=$("input[name='delthistest']").val();
            var tmp_str=$("input[name='thisTestMsg']").val();
            $.zujuan.deleteTest(testid,tmp_str); //删除试题公共方法
            $('#delthistestmsg .tcClose').click();
        });
        //确定删除试题
        $('#delchoosetestmsg .normal_yes').live('click',function(){
            var testid=$("input[name='delchoosemsg']").val();
            var tmp_str=$("input[name='tmp_strmsg']").val();
            $.zujuan.deleteTest(testid,tmp_str); //删除试题公共方法
            $('#delchoosetestmsg .tcClose').click();
        });
    },
    //删除试题公共方法
    deleteTest:function(testid,testtypename){
        var testidarr=new Array();
        var num,i;
        if(testid==""){
            alert('没有获取到试题参数！！');
            return false;
        }
        var testidarr=testid.split(',');
        num=0;
        for(i=0;i<testidarr.length;i++){
            num+=parseInt($('#s_quesdiv'+testid+' .s_quesindex').attr('queschildnum'));
        }
        //删除试题时进行小题数量检查 dowoan 2015-4-20
        if(num == 0){
            num = 1;
        }
        num=0-num;
        //更新试题篮
        $.myTest.updateMainTypes(num,testtypename);
        for(i in testidarr){
            if(testidarr.length>1 && i==0){
                $('#choosetext'+testidarr[i]).remove();
            }
            editData.deltest(testidarr[i]);//试题移除题型
            $.zujuan.addRecTest(testidarr[i]);//加入回收站
            $('#quesbox'+testidarr[i]).remove();
            $('#s_quesdiv'+testidarr[i]).remove();
        }
        $.zujuan.updateTestNum();//更新试题序号
        $.zujuan.resetChoose();//更新选做题描述
        $.zujuan.resetScore();//更新试题分值
    },
    //上移试题
    testMoveUp:function(){
        $('.quesopmenu .moveup').live('click',function(){
            if($(this).css('opacity')==0.5) return;

            var div=$(this).parents('.quesbox').last();
            var nowdiv=div.attr('id').replace('quesbox','');
            var prediv=div.prev().attr('id');

            if(prediv.indexOf('choosetext')!=-1){
                prediv=div.prev().prev().attr('id');
            }
            prediv=prediv.replace('quesbox','');
            $.zujuan.changeTest(nowdiv,prediv,0);
            $('#quesbox'+nowdiv).css({'border':'1px solid #ffffff','color':'#000000','background-color':'#ffffff'});
            $('#quesbox'+nowdiv).find('.quesopmenu').css('display','none');
        });
    },
    //下移试题
    testMoveDown:function(){
        $('.quesopmenu .movedn').live('click',function(){
            if($(this).css('opacity')==0.5) return;
            var div=$(this).parents('.quesbox').last();
            var nowdiv=div.attr('id').replace('quesbox','');
            var prediv=div.next().attr('id');

            if(prediv.indexOf('choosetext')!=-1){
                prediv=div.next().next().attr('id');
            }
            prediv=prediv.replace('quesbox','');
            $.zujuan.changeTest(prediv,nowdiv,0);
            $('#quesbox'+nowdiv).css({'border':'1px solid #ffffff','color':'#000000','background-color':'#ffffff'});
            $('#quesbox'+nowdiv).find('.quesopmenu').css('display','none');
        });
    },
    //重置题型编号
    resetTypesID:function (){
        var tmp_i=1;
        var tmp_id='';
        var tmp_str_1,tmp_str_2;
        $('.s_questype').each(function(){
            tmp_str_1=$(this).attr('id').split('_');
            if(tmp_id != tmp_str_1[1]){
                tmp_id=tmp_str_1[1];
                tmp_i=1;
            }
            $(this).attr('id','s_'+tmp_id+'_'+tmp_i);
            $(this).find('.s_questypehead').attr('id','s_'+tmp_id.replace('questype','questypehead')+'_'+tmp_i);
            $(this).find('.s_questypeindex').attr('id','s_'+tmp_id.replace('questype','questypeindex')+'_'+tmp_i);
            $(this).find('.s_questypename').attr('id','s_'+tmp_id.replace('questype','questypename')+'_'+tmp_i);
            tmp_i=tmp_i+1;
        });
        tmp_i=1;
        $('.questype').each(function(){
            tmp_str_1=$(this).attr('id').split('_');
            if(tmp_id != tmp_str_1[0]){
                tmp_id=tmp_str_1[0];
                tmp_i=1;
            }
            $(this).attr('id',tmp_id+'_'+tmp_i);
            $(this).find('.questypehead').attr('id',tmp_id.replace('questype','questypehead')+'_'+tmp_i);
            $(this).find('.questypeheadbox').attr('id',tmp_id.replace('questype','questypeheadbox')+'_'+tmp_i);
            $(this).find('.questypescore').attr('id',tmp_id.replace('questype','questypescore')+'_'+tmp_i);
            $(this).find('.questypename').attr('id',tmp_id.replace('questype','questypename')+'_'+tmp_i);
            $(this).find('.questypenote').attr('id',tmp_id.replace('questype','questypenote')+'_'+tmp_i);
            tmp_i=tmp_i+1;
        });
    },

    //更新题型序号
    updateTypesNum:function(){
        //更新右侧题型序号
        var tmp_k=0;
        $('.questypeindex').each(function(i){
            $(this).find('b').html(shuzi[tmp_k]+'、');
            tmp_k++;
        });
        //更新左侧题型序号
        tmp_k=0;
        $('.s_questypeindex').each(function(i){
            $(this).find('b').html(shuzi[tmp_k]+'、');
            tmp_k++;
        });
    },
    //更新试题序号
    updateTestNum:function (){
        //更新左侧试题序号
        var tmp_k=1;
        $('#s_paperbody .s_quesindex').each(function(i){
            if(parseInt($(this).attr('queschildnum'))!=1){
                $(this).html(tmp_k+'-'+(tmp_k+parseInt($(this).attr('queschildnum'))-1)+'．');
                tmp_k=tmp_k+parseInt($(this).attr('queschildnum'))-1;
            }else $(this).html(tmp_k+'．');
            tmp_k++;
        });
        //更新右侧试题序号
        tmp_k=1;
        var oldK=1;
        $('#pui_body .quesbox').each(function(i){
            oldK=tmp_k;
            $(this).find('.quesindexnum').each(function(){
                $(this).html($(this).html().replace(/\d+/,oldK));
                oldK++;
            });
            if($(this).find('.quesindex').length==0){
                tmp_k=oldK;
            }else{
                $(this).find('.quesindex').each(function(){
                    $(this).find('b').html(tmp_k+'．');
                    tmp_k++;
                });
            }
        });
    },
    //更新选做题描述
    resetChoose:function(){
        //清除所有描述
        $('.choosetext').remove();
        
        var tmp_str=editData.gettypelist(7);
        var tmp_i,tmp_j,tmp_k,tmp_l,tmp_m,tmp_num,tmp_str_1,tmp_str_2,tmp_str_3,tmp_str_4,tmp_str_5,tmp_str_6;
        var tmp_arr=new Array();
        var tmp_arr2=new Array();
        var tmp_arr3=new Array();
        for(tmp_i in tmp_str){
            tmp_str_1=tmp_str[tmp_i];
            if(tmp_str_1[5]!=0){
                tmp_str_2=tmp_str_1[5].split(';');
                for(tmp_k in tmp_str_2){
                    tmp_str_4=tmp_str_2[tmp_k].split('|');
                    if(tmp_str_4[4]!='0'){
                        if(tmp_arr3[tmp_str_4[3]]=='' || typeof(tmp_arr3[tmp_str_4[3]])=='undefined'){
                            tmp_arr[tmp_str_4[3]]=tmp_str_4[0];
                            tmp_arr2[tmp_str_4[3]]=tmp_str_4[4];
                            tmp_arr3[tmp_str_4[3]]=1;
                        }else{
                            tmp_arr3[tmp_str_4[3]]++;
                        }
                    }
                }
            }
        }
        if(tmp_arr){
            for(var tmp_i in tmp_arr){
                if($('#choosetext'+tmp_arr[tmp_i]).length==0){
                    $('#quesbox'+tmp_arr[tmp_i]).before('<div id="choosetext'+tmp_arr[tmp_i]+'" class="choosetext">'+ $.zujuan.getChooseText($('#quesdiv'+tmp_arr[tmp_i]+' .quesindex b').html().replace(/[^\d]*/i,''),tmp_arr3[tmp_i],tmp_arr2[tmp_i])+'</div>');
                }else{
                    $('#choosetext'+tmp_arr[tmp_i]).html($.zujuan.getChooseText($('#quesdiv'+tmp_arr[tmp_i]+' .quesindex b').html().replace(/[^\d]*/i,''),tmp_arr3[tmp_i],tmp_arr2[tmp_i]));
                }
            }
        }
    },
    //更新试题分值
    resetScore:function(testtypeid){
        var tmp_str=editData.gettypelist(7);
        var testmsg=new Array();
        var tmp_i;

        $('.questype').each(function(){
            if($(this).find('.questypebody .quesbox').length==0){
                $(this).find('.questypedscore').html('');
            }
        });
        for(tmp_i in tmp_str){
            if(tmp_str[tmp_i][0]==testtypeid || typeof(testtypeid)=='undefined'){
                testmsg[tmp_i]=editData.checkscore(tmp_str[tmp_i][0]);
                //修改题型下面的试题分数显示样式
                $.zujuan.setScoreType(tmp_str[tmp_i][0],testmsg[tmp_i]);
            }
        }
    },
    //设置单题分值
    setSigleScore:function(typesid,num,score){
        $('#'+typesid).parent().find('.quesscore:eq('+num+')').html('（本题'+score+'分）');
    },
    //清除题型下所有单题分值
    clearSigleScore:function(typesid){
        $('#'+typesid).parent().find('.quesscore').each(function(){
            $(this).html('');
        });
    },
    //显示试题分值
    getScoreText:function(tmp_num,tmp_score,tmp_type){
        if(tmp_type==1){
            return '：共'+tmp_num+'题 共'+tmp_score+'分';
        }else{
            var tmp_total=(parseFloat(tmp_num)*parseFloat(tmp_score)); //总分
            return '：共'+tmp_num+'题 每题'+tmp_score+'分 共'+tmp_total+'分';
        }

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
    //更新填分栏题号
    changejfl:function(num){
        if(num==1){
            $('#pui_score tr:eq(0) td').last().before('<td style="border-top-width: 1px; border-left-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-top-style: solid; border-left-style: solid; border-right-style: solid; border-bottom-style: solid; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0);">'+shuzi[$('#pui_score tr:eq(0) td').length-2]+'</td>');
            $('#pui_score tr:eq(1) td').last().before('<td style="border-top-width: 1px; border-left-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-top-style: solid; border-left-style: solid; border-right-style: solid; border-bottom-style: solid; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0);"> </td>');
        }else if(num=-1){
            $('#pui_score tr:eq(0) td').last().prev().remove();
            $('#pui_score tr:eq(1) td').last().prev().remove();
        }
    },
    //移动题型
    changeTypes:function(t1,t2){
        var tmp_i=-1;
        $('#pui_body .questype').each(function(i){
            if(t1==$(this).attr('id')){
                tmp_i=i;
            }
        });
        $('#'+t2).find('.questypehead').css({'border':'1px solid #ffffff','color':'#000000','background-color':'#ffffff'});
        $('#'+t2).find('.questypescore table').css('border','1px solid #000000');
        $('#'+t2).find('.questypescore td').css('border','1px solid #000000');
        $('#'+t2).css('border','1px solid #ffffff');
        $('#'+t2).find('.questypemenu').css('display','none');;
        $('#'+t1).after($('#'+t2));
        $('#s_'+t1).after($('#s_'+t2));
        $('#quescountdetail tr:eq('+tmp_i+')',window.parent.document).after($('#quescountdetail tr:eq('+(parseInt(tmp_i)-1)+')',window.parent.document));
        editData.changetypes($('#'+t1).find('.questypehead').attr('id'),$('#'+t2).find('.questypehead').attr('id'));//更新cookie
        $.zujuan.updateTypesNum();//更新题型序号
        $.zujuan.resetTypesID();//更新题型编号
        $.zujuan.updateTestNum();//更新试题序号
    },
    //移动试题 //把试题t1放在t2 up 之前0 之后1
    changeTest:function (t1,t2,up){
        if(t1=='' || typeof(t1)=='undefined' || t2=='' || typeof(t2)=='undefined'){
            return 0;
        }
        if(typeof(up)=='undefined' || up=='') up=0;
        var test1msg=editData.checkifchoose(t1);
        var test2msg=editData.checkifchoose(t2);
        var i,flag,idlist,leftlist,jump;
        flag=0;
        //t1 t2在同一个选做题内
        if((test1msg[0]==0 && test2msg[0]==0) || (test1msg[0]==1 && test2msg[0]==1 && (','+test1msg[1].join(',')+',').indexOf(','+t2+',')!=-1)){
            if(up==0){
                $('#quesbox'+t2).before($('#quesbox'+t1));
                $('#s_quesdiv'+t2).before($('#s_quesdiv'+t1));
            }else{
                $('#quesbox'+t2).after($('#quesbox'+t1));
                $('#s_quesdiv'+t2).after($('#s_quesdiv'+t1));
            }
            editData.changetest(t1,t2,up);//更新cookie
        }else{
            //是否是选做题
            if(test1msg[0]==1){
                for(i in test1msg[1]){
                    if(i==0) flag=test1msg[1][i];
                    test1msg[1][i]='#quesbox'+test1msg[1][i];
                }
            }else{
                test1msg[1][0]='#quesbox'+test1msg[1][0];
            }

            leftlist=test1msg[1].join(',');
            leftlist=leftlist.replace(/quesbox/g,'s_quesdiv');
            if(flag==0){
                idlist=test1msg[1].join(',');
            }else{
                idlist='#choosetext'+flag+','+test1msg[1].join(',');
            }

            if(test2msg[0]==1){
                if(up==0){
                    $('#choosetext'+test2msg[1][0]).before($(idlist));
                    $('#s_quesdiv'+test2msg[1][0]).before($(leftlist));
                }else{
                    $('#quesbox'+test2msg[1][test2msg[1].length-1]).after($(idlist));
                    $('#s_quesdiv'+test2msg[1][test2msg[1].length-1]).after($(leftlist));
                }
            }else{
                if(up==0){
                    $('#quesbox'+t2).before($(idlist));
                    $('#s_quesdiv'+t2).before($(leftlist));
                }else{
                    $('#quesbox'+t2).after($(idlist));
                    $('#s_quesdiv'+t2).after($(leftlist));
                }
            }
            editData.changetest(test1msg[1].join(',').replace(/#quesbox/g,''),test2msg[1].join(',').replace(/#quesbox/g,''),up);//更新cookie
        }
        $.zujuan.updateTestNum();//更新试题序号
        $.zujuan.checkTestMsg();//检查多选题题序是否正确
        return 1;
    },
    //把试题移动到空题型内
    changeTestToTypes:function(testid,typeid){
        if(testid=='' || typeof(testid)=='undefined' || typeid=='' || typeof(typeid)=='undefined'){
            return 0;
        }
        var test1msg=editData.checkifchoose(testid);
        //是否是选做题
        var flag=0;
        if(test1msg[0]==1){
            for(i in test1msg[1]){
                if(i==0) flag=test1msg[1][i];
                test1msg[1][i]='#quesbox'+test1msg[1][i];
            }
        }else{
            test1msg[1][0]='#quesbox'+test1msg[1][0];
        }
        var leftlist=test1msg[1].join(',');
        leftlist=leftlist.replace(/quesbox/g,'s_quesdiv');
        var idlist='';
        if(flag==0){
            idlist=test1msg[1].join(',');
        }else{
            idlist='#choosetext'+flag+','+test1msg[1].join(',');
        }
        $(idlist).appendTo($('#'+typeid+'').next());
        $(leftlist).appendTo($('#s_'+typeid).next());
        $.zujuan.updateTestNum();//更新试题序号
        $.zujuan.checkTestMsg();//检查多选题题序是否正确
        return editData.changeTestToTypes(test1msg[1].join(',').replace(/#quesbox/g,''),typeid);//更新cookie
    },

    //检查多选题题序是否正确
    checkTestMsg:function (){
        var textarr=new Array();
        var testnumarr=new Array();
        var test_arr=new Array();
        var num_arr=new Array();
        var j;
        $('.choosetext').each(function(){
            var _this=$(this);
            var textmsg=_this.html();//获取提示框信息
            textarr=textmsg.split(' ');//空格分割信息，获取题号
            testnumarr=textarr[1].split('、');//以、分割 获取试题数
            //获取id
            var nexttestid=_this.next().attr('id').replace('quesbox','');
            //重置当前编号
            _this.attr('id','choosetext'+nexttestid);
            //获取题号
            var nexttestnum=$(this).next().find('.quesindex b').html();
            //获取编号
            test_arr=nexttestnum.split('．');
            num_arr=new Array();
            for(j=0;j<testnumarr.length;j++){
                num_arr[j]=parseInt(test_arr[0])+j;
            }
            textarr[1]=num_arr.join('、');
            _this.html(textarr.join(' '));
        })
    },
    //加入试题到回收站
    addRecTest:function(id){
        var tmp_str=$('#s_quesdiv'+id).attr('title');
        var tmp_str_1=tmp_str.split('难度：');
        var tmp_str_2=tmp_str_1[1].split(' | ');
        editData.addrectest(id,tmp_str_2[0],tmp_str_2[1],tmp_str_2[2]);
    },
    //回收站内移除试题
    delRecTest:function(id){
        $('#recqueslist'+id).next().remove();
        $('#recqueslist'+id).remove();
        editData.delrectest(id);
    },
    //修改标题事件
    changeTestTitleEvent:function(){
        $("#setsubmitbtn").live('click',function() {
            $.zujuan.resetPaper(); //修改标题后重置标题
        });
        $("#setclosebtn").live('click',function() {
            $.workDown.close(); //关闭对话框
        });
    },
    //修改标题后重置标题
    resetPaper:function (){
        var tmp_id,tmp_title,tmp_display,tmp_text,tmp_score,tmp_str;
        tmp_str=[];
        var tmp_str_1='@%@';
        var tmp_str_2;
        var tmp_str_3=0;
        var tmp_str_4;
        var tmp_dscore=-1;
        var tmp_testList=-1;

        $('#setmain div.setbox ').removeClass('box_current');
        $('#setmain div.setbox .settitle').css({'background-color': 'rgb(2, 87, 156)','color': 'rgb(255, 255, 255)'});
        $('#setmain div.setbox').each(function(){
            if($(this).find('.settitle').html().indexOf('题型')!=-1){
                tmp_str_2=$(this).find('input:text').val();
                if(tmp_str_1.indexOf('@%@'+tmp_str_2+'@%@')!=-1){
                    $('#setmain').animate({scrollTop:$(this).offset().top - $('#setmain').offset().top + $('#setmain').scrollTop()},1000)
                    $(this).addClass('box_current');
                    $(this).find('td').eq(0).css({'background-color':'rgb(255, 221, 0)','color': 'rgb(0, 0, 0)'});
                    $.myDialog.showMsg('所设题型名称重复!请检查.',1);
                    tmp_str_3=1;
                    return false;
                }else{
                    tmp_str_1+=tmp_str_2+'@%@';
                }
            }
        });
        if(tmp_str_3==1) return false;

        $('#setmain div.setbox').each(function(){
            tmp_id=$(this).attr('id');
            tmp_title=-1;
            tmp_text=-1;
            tmp_display=-1;
            tmp_score=-1;
            tmp_dscore=-1;
            tmp_testList=-1;
            $('#'+tmp_id+" :radio").each(function(i){
                if(i<2){
                    if($(this).attr('checked')=='checked') tmp_display=$(this).val();
                }else{
                    if($(this).attr('checked')=='checked') tmp_score=$(this).val();
                }
            });
            if($('#'+tmp_id+' :text').length>0)
                tmp_title=$.trim($.myCommon.removeHTML($('#'+tmp_id+' :text').val()));
            if($('#'+tmp_id+' select').length>0)
                tmp_dscore=$('#'+tmp_id+' select').val();
            if($('#'+tmp_id+' textarea').length>0){
                tmp_text= $.myCommon.dangerHTML($('#'+tmp_id+' textarea').val()).replace(/\n/ig,'<br/>');
            }
            if(tmp_id=='divnotice'){
                tmp_text=-1;
                tmp_title=$.myCommon.dangerHTML($('#'+tmp_id+' textarea').val()).replace(/\n/ig,'<br/>');
            }
            if(tmp_score!=-1){
                var tmp_oldstr=$('#'+tmp_id.replace('div','')+' .questypename').html();
                $('#quescountdetail tr',window.parent.document).each(function(){
                    if($(this).find('td').first().attr('title')==tmp_oldstr){
                        $(this).find('td').first().attr('title',tmp_title);
                        $(this).find('td').first().html(((tmp_title.length > 5) ? tmp_title.substr(0, 5) + "_": tmp_title)+'：');
                        return false;
                    }
                });

                tmp_str_4=editData.getvalue(tmp_id.replace('div',''),6);
                $.zujuan.setText({ code: tmp_id.replace('div',''), display: tmp_display, title: tmp_title, text: tmp_text, score: tmp_score});
                tmp_testList=editData.getvalue(tmp_id.replace('div',''),5);
                tmp_str.push(tmp_id.replace('div','')+'@$@'+tmp_display+'@$@'+tmp_title+'@$@'+tmp_text+'@$@'+tmp_score+'@$@'+tmp_testList+'@$@'+tmp_str_4);
            }else if(tmp_text!=-1){
                $.zujuan.setContent({ code: tmp_id.replace('div',''), display: tmp_display, title: tmp_title, text: tmp_text});
                tmp_str.push(tmp_id.replace('div','')+'@$@'+tmp_display+'@$@'+tmp_title+'@$@'+tmp_text);
            }else if(tmp_title!=-1){
                $.zujuan.setPaper({ code: tmp_id.replace('div',''), display: tmp_display, title: tmp_title});
                tmp_str.push(tmp_id.replace('div','')+'@$@'+tmp_display+'@$@'+tmp_title);
            }else{
                $.zujuan.setPaper({ code: tmp_id.replace('div',''), display: tmp_display});
                tmp_str.push(tmp_id.replace('div','')+'@$@'+tmp_display);
            }
        });
        
        //保存联考数据
        var saveID=editData.getAttr(1);
        var answerStyle=editData.getAttr(2);

        //记录数据
        editData.clear();
        editData.set('maintitle',tmp_str.join('@#@'));
        if(typeof(saveID)!='undefined') editData.addAttr(saveID,answerStyle);
        $("#setclosebtn").click();
    },
    //标题事件 单选切换
    raidoChange:function(){
        $('.radio').live('click',function(){
            $(this).attr('checked','checked');
            var tmp_disabled,tmp_id;
            if($(this).val()==1) tmp_disabled=false;
            if($(this).val()==0) tmp_disabled=true;
            tmp_id=$(this).attr('name').replace('radio_','');
            $('#'+tmp_id+' :text').attr('disabled',tmp_disabled);
            $('#'+tmp_id+' textarea').attr('disabled',tmp_disabled);
        });
    },
    //获取项目是否显示
    getIfShow:function(item){
        if($(item).attr('class')=='s_display_open'){
            return true;
        }
        return false;
    },
    //删除试题
    delQuesByID:function(id){
        //清除试题栏 清除cookie
        $('#quesbox'+id+' .quesopmenu .del').click();
    },
    //载入试题
    initTest:function(){
        var testList=editData.gettestid();
        if(testList){
            //获取试题
            $.post(U('Index/getZjTestById'),{"id":testList,"width":$.zujuan.docWidth,"rand":Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                }
                //gettestbycookie   获取以试题id为数据的数据集   根据
                if(!data['data'][0]) return false;
                //根据cookie插入试题
                var tmp_str_1=editData.gettestlist();
                var len=tmp_str_1.length;
                if(len<=1) return false;

                var typesArr=$.zujuan.getTypesByID();
                var a_1,a_2,a_3,a_4,t_k,tmp_1,tmp_str,html,lefthtml,chooseA;
                a_1=0;//计数
                a_2='';//字符串
                a_3=1;//序号
                a_4=1;//序号
                var tmp_str_2,tmp_str_3,move;
                for(var i=0;i<len-1;i++){
                    if(tmp_str_1[i][2]!=0){
                        html=[];
                        lefthtml=[];
                        tmp_str_2=tmp_str_1[i][1].split(';');
                        for(var j=0;j<tmp_str_2.length;j++){
                            tmp_str_3=tmp_str_2[j].split('|');
                            if(!data['data'][0][tmp_str_3[0]]){
                                //删除当前题
                                $.zujuan.delQuesByID(tmp_str_3[0]);
                                continue;
                            }
                            tmp_str=data['data'][0][tmp_str_3[0]];

                            move='';
                            if(tmp_str_3[4]!='0') move=' move="mv'+tmp_str_3[4]+'" ';

                            if(tmp_str['docname']==null || tmp_str['docname']=='') tmp_str['docname']=tmp_str['typesname']+(j+1);

                            lefthtml.push('<div class="s_quesdiv" '+move+'id="s_quesdiv'+tmp_str['testid']+'" title="题号：'+tmp_str['testid']+' | 难度：'+tmp_str['diffname']+' | '+tmp_str['docname'].replace(/\"/g,'\'')+' | '+tmp_str['typesname']+'">'+
                                '<span class="s_quesindex" queschildnum="'+tmp_str['testnum']+'">'+(tmp_str['testnum']>1 ? a_3+'-'+(a_3+parseInt(tmp_str['testnum'])-1) : a_3)+'．</span>'+
                                '<span class="s_questitle">'+tmp_str['docname']+'</span>'+
                                '</div>');

                            if(typesArr[tmp_str['typesid']]['IfDo']=='0') chooseA='<a class="choosetest">选做</a>';
                            else chooseA='';
                            html.push('<div class="quesbox" typesid="'+tmp_str['typesid']+'" id="quesbox'+tmp_str['testid']+'">'+
                                '<div class="quesopmenu" style="display: none;">'+
                                '<a class="setscore">改分</a>'+
                                '<a class="detail">详细</a>'+
                                '<a class="replace">替换</a>'+
                                '<a class="del">删除</a>'+
                                chooseA+
                                '<a class="moveup">上移</a>'+
                                '<a class="movedn">下移</a>'+
                                '</div>'+
                                '<div class="quesdiv" id="quesdiv'+tmp_str['testid']+'">'+
                                '<div><p>');
                            if(tmp_str['test'].indexOf('【题号')!=-1){
                                a_4=a_3;
                                tmp_1=tmp_str['test'].split('【题号');
                                for(var jj=1;jj<tmp_1.length;jj++){
                                    tmp_1[jj]='<span class="quesindexnum">　'+a_4+'　</span>'+tmp_1[jj].substring(tmp_1[jj].indexOf('】')+1);
                                    a_4++;
                                }
                                tmp_str['test']=tmp_1.join('');
                            }

                            if(tmp_str['test'].indexOf('【小题')!=-1){
                                tmp_1=tmp_str['test'].split('【小题');
                                for(var jj=1;jj<tmp_1.length;jj++){
                                    tmp_1[jj]='<span class="quesindex"><b>'+a_3+'．</b></span><span class="quesscore"></span>'+tmp_1[jj].substring(tmp_1[jj].indexOf('】')+1);
                                    a_3++;
                                }
                                tmp_str['test']=tmp_1.join('');
                            }else if(a_4==1){
                                html.push('<span class="quesindex"><b>'+a_3+'．</b></span><span class="quesscore"></span>');
                                a_3++;
                            }else{
                                a_3=a_4;
                            }
                            a_4=1;
                            //兼容未去除<p>起始标签的内容
                            if(tmp_str['test'].indexOf('<p>') == 0){
                                tmp_str['test'] = tmp_str['test'].replace(/<p>/, '');
                            }
                            html.push('<span class="tips"/>'+ $.myTest.removeLeftTag(tmp_str['test'],'<p>')+'</div>'+
                                '</div>'+
                                '</div>');
                        }
                        $('#'+tmp_str_1[i][3]).next().append(html.join(''));
                        $('#s_'+tmp_str_1[i][3]).next().append(lefthtml.join(''));
                    }
                }
                var title=$('#pui_maintitle').html();
                $.zujuan.resetScore();//更新试题分值
                $.zujuan.resetChoose();//更新选做题描述
            }).error(function() { alert("试卷获取失败！");
            }).complete(function() {
                $.myCommon.loadingHide();
            });
        }
    }

}
var flagMove=0; //验证移动是否完成
$.fn.sort = function(options) {
    var opts = $.extend({}, $.fn.sort.defaults, options);
    var container = this;
    var itemClass = opts.itemClass;

    $(container).find('.'+itemClass).live('mousedown',function(e) {
        if(flagMove==1){
            alert("正在移动，请稍候。");
            return ;
        }
        flagMove=1;
        if(e.which != 1 || $(e.target).is(opts.dragExclude)){// e.which = 1 表示左击
            return;
        }
        e.preventDefault();// 阻止选中文本
        var x = e.pageX;//鼠标点击的位置
        var y = e.pageY;//

        //当前父类的offset
        var partop = $(this).parent().offset().top;

        var __this = $(this); // 点击的DOM
        //if(!__this.attr('move')){
        //    __this.attr('move','move'+Math.round(Math.random()*100000))
        //}
        var itemMore = __this;
        var firstObj = __this;
        var movestr=__this.attr('id');
        if(typeof(__this.attr('move'))!='undefined' && __this.attr('move')!=''){
            itemMore = container.find('.'+itemClass+'[move='+__this.attr('move')+']');
            movestr=container.find('.'+itemClass+'[move='+__this.attr('move')+']:eq(0)').attr('id');
            firstObj=itemMore.eq(0);
        }
        var ifmove=0;
        var moveholder;
        var movelen=itemMore.length;
        var tmpResult=1;
        itemMore.map(function(i){
            //对需要移动的块批量处理
            //错误情况终止
            if(typeof(movestr)=='undefined'){
                $('.normaldiv').remove();
                _this.css({position:'static', opacity: 1});
                return;
            }
            var _this = $(this);
            var w = _this.width();//移动块宽高
            var h = _this.height();
            var left = _this.offset().left;//移动块相对位置left和top
            var top = _this.offset().top;
            var sr=$('#paperstruct_body').scrollTop();
            var itemX = x;//选中元素的差值
            var itemY = y-(top-partop);
            var wid;
            // 添加虚线框
            _this.attr('key',Math.round(Math.random()*100000));
            _this.before('<div thisid="'+movestr+'" class="normaldiv __holder'+_this.attr('key')+' '+itemClass+'"></div>');
            wid = $('.__holder'+_this.attr('key'));
            wid.css({border:'1px dashed #ccc', height:h, width:w});
            // 保持原来的宽高
            _this.css({position:'absolute', height:h, width:w, opacity: 0.5, 'z-index': 1001, left: 0, top:top-partop});
            // 绑定mousemove事件
            $(document).mousemove(function (e) {
                //if(ifmove>0 && ifmove<itemMore.length){
                //    moveholder.
                //    continue;
                //}
                e.preventDefault();
                //检测占位符是否在一起
                $('.normaldiv').each(function(ti){
                    if(ti!=0) $(this).remove();
                });
                wid=$('.normaldiv:eq(0)');
                var srr=$('#paperstruct_body').scrollTop()-sr;
                var l = (e.pageX - itemX);
                var t = (e.pageY - itemY)+srr;
                _this.css({'left': l, 'top': t});

                var w2 = w / 2;//移动块宽高的一半
                var h2 = h / 2;
                // 选中块的中心坐标
                var ml = _this.offset().left + w2;
                var mt = _this.offset().top + h2;
                var nowId=_this.attr('id');
                // 遍历所有块的坐标
                $('.s_questypebody').each(function (k) {
                    var obj = $(this);
                    if (!(obj.children().length == 0 || (obj.children().length == movelen && movestr.indexOf(',' + nowId + ',') != -1))) {
                        $(this).children().not(_this).not(wid).each(function (i) {
                            var obj = $(this);
                            var p = obj.offset();
                            var a1 = p.left;
                            var a2 = p.left + obj.width();
                            var a3 = p.top;
                            var a4 = p.top + obj.height();
                            var a5 = p.top + obj.height() / 2;

                            if (a1 < ml && ml < a2 && a3 < mt && mt < a4) {
                                //判断父元素是否允许插入
                                if (obj.parent().hasClass(opts.noInsertClass)) {
                                    return;
                                }
                                //回调函数验证是否允许移动
                                if (opts.ifDrag(_this, obj.parent()) == false) {
                                    return;
                                }
                                if (mt > a5) {
                                    //插入元素下部时判断上下元素是否允许插入
                                    if (obj.attr('move') == obj.next().attr('move') && obj.next().attr('move') != undefined) {
                                        return;
                                    } else {
                                        wid.insertAfter(this);
                                        ifmove += 1;
                                        moveholder = wid;
                                    }
                                } else {
                                    //插入元素上部分时判断元素是否允许插入
                                    if (obj.attr('move') == obj.prev().attr('move') && obj.prev().attr('move') != undefined) {
                                        return;
                                    } else {
                                        wid.insertBefore(this);
                                        ifmove += 1;
                                        moveholder = wid;
                                    }
                                }
                                return;
                            }
                        });
                    } else {
                        var p = obj.offset();
                        var a1 = p.left;
                        var a2 = p.left + obj.width();
                        var a3 = p.top;
                        var a4 = p.top + obj.height() + _this.height();
                        if (a1 < ml && ml < a2 && a3 < mt && mt < a4) {
                            //判断当前元素是否允许插入
                            if (obj.hasClass(opts.noInsertClass)) {
                                return;
                            }
                            //回调函数验证是否允许移动
                            if (opts.ifDrag(_this, obj) == false) {
                                return;
                            }
                            //元素是否允许插入
                            ifmove += 1;
                            moveholder = wid;
                            obj.append(wid);
                        }
                    }
                });
            });
            // 绑定mouseup事件
            $(document).mouseup(function() {
                $(document).off('mouseup').off('mousemove');//移除绑定的事件
                if($('.normaldiv').length==0){
                    flagMove=0;
                    return;
                }

                var p = wid.offset();//得到虚线框的位置信息
                var tleft=p.left-left;
                var ttop=p.top-top;
                if(ttop<0) ttop=0;
                _this.animate({'left':tleft, 'top': ttop}, 100, function() {
                    //移动到虚线框的位置
                    //判断有没有移动
                    if(left!=0 || top!=0){
                        opts.dragComplete(_this);//回调函数
                    }
                    itemMore.removeAttr('style');
                    //试题放在原题型下，并且仅有当前移动试题是不做cookie操作
                    var typeid=wid.parent().prev().attr('id').replace('s_','');
                    var nowlength=0;
                    wid.parent().find('.s_quesdiv').each(function(){
                        if($(this).attr('thisid')=='' || typeof($(this).attr('thisid'))=='undefined') nowlength++;
                    });
                    if(movelen==nowlength && _this.parent().prev().attr('id')==wid.parent().prev().attr('id')){
                        wid.replaceWith(itemMore);
                        $('.normaldiv').remove();
                        flagMove=0;
                        return;
                    }
                    if(wid.prev('.s_quesdiv').length>0){
                        var tid=wid.prev('.s_quesdiv').attr('id').replace('s_quesdiv','');
                        //移动试题 //把试题t1放在t2 up 之前0 之后1changetest(t1,t2,up);
                        tmpResult=$.zujuan.changeTest(movestr.replace('s_quesdiv',''),tid,1);
                    }else if(wid.next('.s_quesdiv').length>0){
                        //判断虚线框下面的试题是否是当前试题
                        if(wid.next('.s_quesdiv').attr('id')==firstObj.attr('id')){
                            wid.replaceWith(itemMore);
                            $('.normaldiv').remove();
                            flagMove=0;
                            return;
                        }
                        var tid=wid.next('.s_quesdiv').attr('id').replace('s_quesdiv','');
                        tmpResult=$.zujuan.changeTest(movestr.replace('s_quesdiv',''),tid,0);
                    }else{
                        tmpResult=$.zujuan.changeTestToTypes(movestr.replace('s_quesdiv',''),typeid);
                    }
                    if(tmpResult==0){

                    }
                    wid.replaceWith(itemMore);
                    $('.normaldiv').remove();
                    flagMove=0;
                });
            });
        });
    });
    $.fn.sort.defaults = {
        itemClass: "",
        dragExclude: "input, textarea",
        noInsertClass:"noInsert",
        dragComplete: function() {},
        ifDrag: function(){}
    };
};

/**
 * 配置说明
 * itemClass：需要拖动排序的元素的类名；多个连续不可分割的增加move属性，move值为相同的,比如move="move"
 * dragComplete：完成回调函数，参数item为当前移动对象
 * ifDrag：判断回调函数，参数item为当前移动对象，target为放置的父元素的对象（实时的）
 * （itemClass为必须参数，其它均为非必需）
 *//**
 $('.s_questypebody').sort({
    itemClass:'s_quesdiv',
    dragComplete:function(item){
    },
    ifDrag:function(item,target){
        //if(item.attr('move')=='move'&&target.attr('id') == 'test2'){
        //    return false;
        //}
    }
}); */
//默认科目
var subjectID=Cookie.Get("SubjectId");

$(document).ready(function(){
    $('#main').bind("selectstart",function(){return false;});
    $('#textdiv').live("selectstart",function(){return false;});
    $('#replacediv').live("selectstart",function(){return false;});
    $('#analyticdiv').live("selectstart",function(){return false;});
    //学科不存在返回到登录后的首页
    $.myCommon.checkSubject(U('Index/main'));
    if(subjectID){
        $.zujuan.init();
    }
});
