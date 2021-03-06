var subjectID=Cookie.Get("SubjectId"); //学科

$(document).ready(function(){
    $(document).bind("selectstart",function(){return false;});

    //学科不存在返回到登录后的首页
    $.myCommon.checkSubject(U('/Home','',false));

    if(subjectID){
        $.myZn.init();
    }
});
//智能组卷
jQuery.myZn={
    parentName:'', //学科名称
    init:function(){
        this.loadStep1(); //载入第一界面 事件
        this.loadStep2(); //载入第二界面 事件
        this.loadStep3(); //载入第三界面 事件
        this.loadStep4(); //载入第四界面 事件
        $.myZn.initDivXdBox(); //载入框架

        $.myTest.showTestEvevt(); //载入试题事件
        //窗口改变事件
        $(window).resize(function() { $.myZn.initDivXdBox(); });
    },
    //载入第一界面 事件
    loadStep1:function(){
        this.setSubjectName(); //设置学科名称
        this.showStep1(); //载入第一界面
        this.checkAll(); //知识点或章节全选
        this.getChapterFirst(); //获取章节版本
        this.selectKlByRange(0); //显示知识点（章节）列表

        this.chapterFirstrChange(); //章节切换改变
        this.knowledgeSonSelectShow(); //知识点子类选中显示
        this.knowledgeParentSelect(); //知识点父类选中
        this.knowledgeSonSelect(); //知识点子类选中
        this.knowledgeSelectCheckedColor(); //知识点选中项颜色改变
        this.knowledgeSelectChecked(); //知识点选中改变
        this.knowledgeSelectAll(); //知识点全选
        this.toStep2Click(); //跳转至第二步
    },
    loadStep2:function(){
        this.getGradeList(); //年级列表
        this.getDocType(); //获取选题范围
        this.resetBgColor(); //重置背景颜色
        this.checkGrade(); //检测年级
        this.checkType(); //检测题型
        this.totalNum(); //统计试卷总分数

        this.changeTestNumEvent(); //选题数量变更事件
        this.changeIntelNumEvent(); //固定小题数量试题切换事件
        this.changeNumListEvent(); //重新统计试题数量
        this.addTestType(); //新增题型
        this.delTestType(); //删除题型
        this.testChooseStatusChange(); //试题选做状态改变
        this.testChooseNumChange(); //试题选做数量改变
        this.testTypeSelectChange(); //题型切换
        this.docTypeSelectAll(); //文档属性 全选按钮点击事件
        this.gradeSelectAll(); //年级全选
        this.docTypeClickEvent(); // 文档属性点击事件
        this.gradeClickEvent(); //年级点击事件
        this.toStep3Click(); //跳转至第三部
        this.toStep1Click(); //跳转至第一步
    },
    loadStep3:function(){
        this.startSelectTest(); //开始选题按钮
        $.BlockMove.init();
    },
    loadStep4:function(){
        this.toTestCenter(); //进入试卷中心
        this.addTestIntoTestBox(); //加入试题进入试题栏
    },
/********************************************************************
 * 第一界面开始
 ********************************************************************/
    //获取章节版本
    getChapterFirst:function(){
        $.post(U('Ga/Index/getData'),{'style':'chapter','subjectID':subjectID,'times':Math.random()},function(data){
            if($.myCommon.backLogin(data)==false){
                return false;
            }
            var output='<label for="rangeSelect">选择考查范围:</label><select id="rangeSelect">';
            output+='<option value="0">'+ $.myZn.parentName+'综合库</option>';
            for(var i=0;i<data['data'].length;i++){
                output+='<option value ="'+data['data'][i]['ChapterID']+'">'+$.myZn.parentName+data['data'][i]['ChapterName']+'</option>';
            }
            output+='</select>';
            $('.selectRange').html(output);
        });
    },
    //设置学科名称
    setSubjectName:function(){
        $.myZn.parentName= $.myCommon.getSubjectNameFromParent();
        $('#loca_text span').html($.myZn.parentName); //学科名
    },
    //设置显示第一界面
    showStep1:function(){
        $('#step1').css('display','block'); //显示第一界面
    },
    //切换版本
    chapterFirstrChange:function(){
        $(document).on('change','#rangeSelect',function(){
            var rid = $('#rangeSelect').val();
            $.myZn.selectKlByRange(rid);
        });
    },
    /**
     * 根据考查范围(教材版本)选择对应的知识点
     * @param rid 版本值 rid=0获得全部知识点内容rid>0从相应章节版本获得知识点内容
     */
    selectKlByRange:function(rid){
        $('#categorycheckbox').html($.myCommon.loading());
        if(rid==0){
            $.post(U('Ga/Index/getData'),{'style':'knowledge','subjectID':subjectID,'haveLayer':2,'times':Math.random()},function(data){
                $.myZn.createKlHtmlByAjaxData(data,rid);
            });
        }else{
            $.post(U('Ga/Index/getData'),{'style':'chapter','pID':rid,'haveLayer':2,'times':Math.random()},function(data){
                $.myZn.createKlHtmlByAjaxData(data,rid);
            });
        }
        $('#wizardbox').css({'width':'100%','height':$(window).height()-$('#righttop').outerHeight(),'overflow-y':'auto'});
        $('.xd_box').css('height','auto');
        $.myZn.initDivXdBox();
    },
    /**
     * 根据ajax返回数据生成知识点html
     * @param data ajax返回的json数组
     * @param rid 版本值
     */
    createKlHtmlByAjaxData:function(data,rid){
        if($.myCommon.backLogin(data)==false){
            return false;
        }
        var output='',sub='',klId='',klName='',ctlist='';//ctlist当知识点来自章节版本增加类ctlist

        //解决ajax返回数据名称问题
        if(rid==0) {
            sub    = 'sub';
            klId   = 'KlID';
            klName = 'KlName';
        }else{
            sub    = 'sub';
            klId   = 'ChapterID';
            klName = 'ChapterName';
            ctlist = 'chapter';
        }
        for(var i=0;i<data['data'].length;i++){
            output+='<table style="margin:10px 0px;" border="0" width="100%" cellspacing="0"><tr><td colspan="3" style="background-color:#ededed;padding:5px 0px 5px 8px;font-weight:bold;"><label><input type="checkbox" value="'+data['data'][i][klId]+'" name="kllist" class="kllist kllistall" />'+data['data'][i][klName]+'</label></td></tr>';
            if(data['data'][i][sub]){
                output+='<tr>';
                for(var j=0;j<data['data'][i][sub].length;j++){
                    if(j%3==0) output+='</tr><tr>';
                    output+='<td width="33%" valign="top" style="padding:6px 0px 6px 8px;"><div class="zsddiv" style="position:relative;width:100%;"><label><input type="checkbox" value="'+data['data'][i][sub][j][klId]+'" name="kllist" class="kllist plist '+ctlist+'" />'+data['data'][i][sub][j][klName]+'</label>';
                    if(data['data'][i][sub][j]['Last']==0)  output+='<span class="childkl" did="'+data['data'][i][sub][j][klId]+'" opendiv="false">&nbsp;</span></div>';
                    output+='</td>';
                }
                if(j%3==1) output+='<td></td><td></td>';
                if(j%3==2) output+='<td></td>';
                output+='</tr>';
            }
            output+='</table>';
        }
        $('#categorycheckbox').html(output);
        $('#cate_checkall').find('a').html('全部选中');
    },
    /**
     * 根据ajax返回数据生成知识点详细菜单
     * @param data  ajax返回的json数组
     * @param $this 引用的jquery对象
     * @param fromChapter 是否来自章节版本 解决数据名称问题
     */
    createKlDetailByAjaxData:function(data,$this,fromChapter){
        if($.myCommon.backLogin(data)==false){
            return false;
        }
        var klId='KlID',klName='KlName';
        if(fromChapter){
            klId='ChapterID';
            klName='ChapterName';
        }
        if(data['data']){
            var checkstr='';
            var colorstr='';
            if($this.prev().find('.plist').attr('checked')=='checked'){
                checkstr=' checked="checked" ';
                colorstr=' style="color:#00a0e8" ';
            }
            var tmp_str='<div class="childdiv"><div class="t"><div class="tl"></div><div class="tz"></div><div class="tr"></div></div><div class="zc"><div class="zz">';
            for(var tmp_i in data['data']){
                tmp_str+='<label '+colorstr+'><input type="checkbox" value="'+data['data'][tmp_i][klId]+'" name="kllist" class="kllist childlist" '+checkstr+' />'+data['data'][tmp_i][klName]+'</label>';
            }
            tmp_str+='</div></div><div class="b"><div class="bl"></div><div class="bz"></div><div class="br"></div></div></div>';
            $this.after(tmp_str);
            $this.css({backgroundPosition:'-18px 50%'});
        }
    },
    /**
     * 知识点或章节全选 全选，全不选
     * @author demo
     * @date 2014年10月21日
     */
    checkAll:function(checkID,inputName){
        if($('#'+checkID).attr('checked')=='checked'){
            $('#'+checkID).parent().css({'color':'#00a0e8'});
            $('input[name="'+inputName+'[]"]').each(function(){
                if($(this).attr('disabled')!='disabled'){
                    $(this).attr('checked',true);
                    $(this).parent().css({'color':'#00a0e8'});
                }
            });
        }else{
            $('#'+checkID).parent().css({'color':'#000'});
            $('input[name="'+inputName+'[]"]').each(function(){
                $(this).parent().css({'color':'#000'});
                $(this).attr('checked',false);
            });
        }
    },
    //验证文档属性和年级组合 是否全选
    checkGradeDocTypeSelectAll:function(checkID,inputName){
        var checkednum=$('input[name="'+inputName+'[]"]:checked').length;
        var checknum=$('input[name="'+inputName+'[]"]').length;
        var disablenum=$('input[name="'+inputName+'[]"][disabled="disabled"]').length;
        if(checknum==parseInt(checkednum)+parseInt(disablenum)){
            $('#'+checkID).attr('checked',true);
        }else{
            $('#'+checkID).attr('checked',false);
        }
    },
    //知识点子类
    knowledgeSonSelectShow:function(){
        $(document).on('click', '.childkl', function () {
            var _this = $(this);
            $('.zsddiv').css({'position': 'static'});
            _this.parent().css({'position': 'relative'});
            _this.next().css({'position': 'absolute'});

            $('.childkl').each(function () {
                if ($(this).next().length > 0 && _this.attr('did') != $(this).attr('did')) {
                    if ($(this).next().length > 0) {
                        $(this).next().css({'display': 'none'});
                        $(this).attr('opendiv', 'false');
                        $(this).css({backgroundPosition: '4px 50%', 'zoom': '0'});
                    }
                }
            });
            if (_this.attr('opendiv') == 'false') {
                if (_this.next().length > 0) {
                    _this.next().css('display', 'inline-block');
                    _this.css({backgroundPosition: '-18px 50%'});
                } else {
                    _this.addClass('ico_ddt');
                    var tmp_id = _this.attr('did');
                    if (_this.prev().children().hasClass('chapter')) {//知识点来自于章节版本
                        $.post(U('Ga/Index/getData'), {'style':'chapter','pID': tmp_id ,'times': Math.random()}, function (data) {
                            $.myZn.createKlDetailByAjaxData(data, _this, true);
                        });
                    } else {
                        $.post(U('Ga/Index/getData'),{'style':'knowledge','subjectID':subjectID, 'pID':tmp_id, 'times': Math.random()}, function (data) {
                            $.myZn.createKlDetailByAjaxData(data, _this, false);
                        });
                    }
                    if (_this.next().find('.t').width() != _this.next().find('.zc').width()) {
                        _this.next().find('.t').width(_this.next().find('.zc').width());
                        _this.next().find('.b').width(_this.next().find('.zc').width());
                    }
                    _this.removeClass('ico_ddt');
                }
                _this.attr('opendiv', 'true');
            } else {
                _this.next().css('display', 'none');
                _this.attr('opendiv', 'false');
                _this.css({backgroundPosition: '4px 50%'});
            }
        });
    },
    //父类知识点选择
    knowledgeParentSelect:function(){
        $(document).on('change','.plist',function(){
            $(this).css('background-color','#fff');
            if($(this).attr('checked')=='checked'){
                if($(this).parent().next().next().length>0){
                    $(this).parent().parent().find('.childlist').each(function(){
                        $(this).attr('checked','checked');
                        $(this).parent().css('color','#00a0e8');
                        $(this).attr('val','yes');
                    });
                }
            }else{
                if($(this).parent().next().next().length>0){
                    $(this).parent().parent().find('.childlist').each(function(){
                        $(this).attr('checked',false);
                        $(this).parent().css('color','#000');
                        $(this).attr('val','no');
                    });
                }
            }
        });
    },
    //子类知识点选择
    knowledgeSonSelect:function(){
        $('.childlist').live('click', function () {
            var _this = $(this);
            var allinput = 0;
            var checkinput = 0;
            _this.parent().parent().find('input').each(function () {
                if ($(this).attr('checked') == 'checked') {
                    checkinput += 1;
                }
                allinput += 1;
            });
            var ckinput = _this.parent().parent().parent().parent().parent();
            if (allinput == checkinput) {
                ckinput.find('.plist').attr('checked', 'checked');
                ckinput.find('.plist').css('background-color', '#fff');
                ckinput.find('label:eq(0)').css('color', '#00a0e8');
                ckinput.find('.plist').attr('val', 'yes');
            } else if (checkinput > 0) {
                ckinput.find('.plist').attr({'checked': 'checked'});
                ckinput.find('.plist').css('background-color', '#ccc');
                ckinput.find('label:eq(0)').css('color', '#000');
                ckinput.find('.plist').attr('val', 'no');
            } else if (checkinput == 0) {
                ckinput.find('.plist').attr('checked', false);
                ckinput.find('.plist').css('background-color', '#fff');
                ckinput.find('label:eq(0)').css('color', '#000');
                ckinput.find('.plist').attr('val', 'no');
            }
        });
    },
    //知识点字体变色
    knowledgeSelectCheckedColor:function(){
        $('.kllist').live('click',function(){
            if($(this).attr('checked')=='checked'){
                $(this).parent().css('color','#00a0e8');
                $(this).attr('val','yes');
            }else{
                $(this).parent().css('color','#000');
                $(this).attr('val','no');
            }
        });
    },
    //知识点分类变色 选中
    knowledgeSelectChecked:function(){
        $('.kllistall').live('click',function(){
            if($(this).attr('checked')=='checked'){
                $(this).parent().css('color','#00a0e8');
                $(this).attr('checked',true);
                $(this).parent().parent().parent().parent().find('input').each(function(){
                    $(this).parent().css('color','#00a0e8');
                    $(this).attr('checked',true);
                    $(this).attr('val','yes');
                });
            }else{
                $(this).parent().css('color','#000');
                $(this).attr('checked',false);
                $(this).parent().parent().parent().parent().find('input').each(function(){
                    $(this).parent().css('color','#000');
                    $(this).attr('checked',false);
                    $(this).attr('val','no');
                });
            }
        });
    },
    //知识点全选
    knowledgeSelectAll:function(){
        $('#cate_checkall').click(function(){
            if($(this).find('a').html()=='全部选中'){
                $('#categorycheckbox input').attr('checked','checked');
                $('#categorycheckbox input').attr('val','yes');
                $('#categorycheckbox input').parent().css('color','#00a0e8');
                $(this).find('a').html('取消全选');
            }else{
                $('#categorycheckbox input').attr('checked',false);
                $('#categorycheckbox input').attr('val','no');
                $('#categorycheckbox input').parent().css('color','#000');
                $(this).find('a').html('全部选中');
            }
        });
    },
    //跳转第一步
    toStep1Click:function(){
        $('.tostep1').click(function(){
            $('.step').css('display','none');
            $('#step1').css('display','block');
            $.myZn.initDivXdBox();
        });
    },
    //初始化布局
    initDivXdBox:function(){
        $("#wizardbox").height($(window).height()-$('#righttop').outerHeight());
        $('.step').each(function(){
            if($(this).css('display')=='block'){
                var b = $(window).height();
                var c = $("#righttop").outerHeight();
                var d = $(this).find('div:eq(0)').outerHeight();
                var e = $(this).find('div.last').outerHeight();
                var thisheight=b-d-c-e-28;
                if($(this).find('.xd_box').height()>0){
                    $(this).find('.xd_box').height(thisheight);
                    $(this).find('.xd_box').css({'overflow-y':'auto','overflow-x':'hidden'});
                }
            }
        });
    },
/********************************************************************
 * 跳转按钮结束
 * 第二界面开始
 ********************************************************************/
    //获取年级
    getGradeList:function(){
        var gradelist='';
        $('input[name="gradelist[]"]').each(function(){
            if($(this).attr("checked")=='checked'){
                gradelist+=$(this).val()+",";
            }
        })
        return gradelist;
    },
    //检查试题数量
    checkChoose:function(){
        var typearr=new Array();
        var defscore=new Array();
        var total=new Array();
        var num_arr=new Array();
        var types_arr=new Array();
        var t_arr=new Array();
        var typeidarr=new Array();
        var defname=new Array();
        var testTotal=0;
        var i;

        for(i=0;i<Types.length;i++){
            typeidarr[i]=Types[i]['TypesID'];
            defscore[i]=Types[i]['Num'];
            defname[i]=Types[i]['TypesName'];
            total[i]=0;
        }
        var jj=0;
        var ts='请设置试题数量！';
        $('#quesnumsetting tr').each(function(i){
            if(ts=='请设置试题数量！' && $(this).find('.typelist').val()!='0' && i!=$('#quesnumsetting tr').length-1) ts='';
            if($(this).find('.choose').attr('checked')=='checked'){
                var numRegExp=/^[0-9]$/;
                if(!numRegExp.test($(this).find('.choose_x').val())){
                    ts='请输入正确的选做题数据';
                }else if(parseInt($(this).find('.choose_x').val())>parseInt($(this).find('.typelist').val())){
                    ts='选做数量大于总数量，请更正';
                }
            }
        });

        $('#quesnumsetting tr').each(function(i){
            if($(this).find('.typelist').val()!='0' && $(this).find('.typelist').length>0){
                t_arr[jj]=i;
                num_arr[jj]=$(this).find('.typelist').val();
                testTotal+=parseInt($(this).find('.typelist').val());
                types_arr[jj]=$(this).find('.typestyle').val();
                jj++;
            }
        });
        if(testTotal>70){
            ts='试题总数量超出限制，请更正';
        }
        for(i in types_arr){
            for(j in typeidarr){
                if(typeidarr[j]==types_arr[i]){
                    total[j]=parseInt(total[j])+parseInt(num_arr[i]);
                }
            }
        }
        var msg='';
        for(i in total){
            if(total[i]>defscore[i]){
                msg+=','+defname[i];
            }
        }
        if(msg!=''){
            ts='您设置【'+msg.substr(1)+'】的题数超出最大限制了，请更改后再试';
        }
        if(ts){
            $.myDialog.showMsg(ts,1);
            return false;
        }
        return true;
    },
    //获取题型列表
    getTypesStr:function(num,name,str,n,typesscore,selecttype,intelNum,typeID,dscore,ifdo,ifpoint,maxscore,chooseNum,selectNum){
        if(num<1) return '';
        var typesStr;
        if(n%2==0){
            typesStr='<tr style="font-size:15px;background:#fff;"><td height="35" width="25"><a class="sqyicon"/></td><td>选取<select name="typelist[]" class="typelist">';
        }else{
            typesStr='<tr style="font-size:15px;background:#edeef0;"><td height="35" width="25"><a class="sqyicon"/></td><td>选取<select name="typelist[]" class="typelist">';
        }
        for(var i=0;i<=num;i++){
            typesStr+='<option value="'+i+'"';
            if(i==selectNum){
                typesStr+='selected="selected"';
            }
            typesStr+='>'+i+name+'</option>';
        }
        typesStr+='</select><input type="hidden" name="IntelName"  class="IntelName" value="'+name+'"></td><td>'+str+'</td>';
        if(selecttype==0 && (ifdo==1 || chooseNum=='0' || chooseNum==undefined)){
            typesStr+='<td><div class="intel">每'+name+'<select name="IntelNum[]" class="IntelNum">';
        }else{
            typesStr+='<td><div class="intel" style="display:none">每'+name+'<select name="IntelNum[]" class="IntelNum">';
        }
        for(var i in Types){
            if(Types[i]['TypesID']==typeID){
                for(var j in Types[i]['IntelNum']){
                    typesStr+='<option value="'+Types[i]['IntelNum'][j]+'"';
                    if(Types[i]['IntelNum'][j]==intelNum){
                        typesStr+='selected=selected';
                    }
                    typesStr+='>'+Types[i]['IntelNum'][j]+'小题</option>';
                }
            }
        }
        typesStr+='</select></div></td>';
        typesStr+='<td>每题分值<select name="numlist[]" class="numlist">';
        var tmpstr='';
        var step=1;
        if(ifpoint=='1') step=0.5;
        if(maxscore<dscore) maxscore=dscore;
        for(var i=0;i<maxscore;){
            i+=step;
            typesStr+='<option value="'+i+'"'+ (dscore==i ? 'selected="selected"' : "") +'>'+i+'分</option>';
        }
        var chooseStr = '';
        var checkedStr = '';
        if(ifdo==1){
            tmpstr=' style="display:none;" ';
            chooseNum=1;
        }else if(chooseNum=='0' || chooseNum==undefined){
            chooseStr=' style="display:none;" ';
            chooseNum=1;
        }else{
            checkedStr='checked="checked"';
        }
        typesStr+='</select><input name="typesscore[]" class="typesscore" type="hidden" value="'+typesscore+'"/><input name="selecttype[]" class="selecttype" type="hidden" value="'+selecttype+'"/></td><td><div'+tmpstr+'><input name="choose[]" class="choose" type="checkbox" value="1" '+checkedStr+'/>选做 <span '+chooseStr+'><input name="choose_x[]" class="choose_x" size="2" value='+chooseNum+' maxlength="1"/>题</span></td><td><span class="t_add">&nbsp</span> <span class="t_remove">&nbsp</span></div></td></tr>';
        return typesStr;
    },
    //获取题型select
    getTypesSelect:function(arr,tagid){
        var tmp_arr='<select name="typestyle[]" class="typestyle">';
        var selected='';
        for(var i in arr){
            selected='';
            if(arr[i]['TypesID']==tagid){
                selected=' selected="selected" ';
            }
            tmp_arr+='<option IfDo="'+arr[i]['IfDo']+'" value="'+arr[i]['TypesID']+'"'+selected+'>'+arr[i]['TypesName']+'</option>';
        }
        tmp_arr+='</select>';
        return tmp_arr;
    },
    //获取选题范围
    getDocType:function(){
        var doctypelist="";
        $('input[name="doctype[]"]').each(function(){
            if($(this).attr("checked")=='checked'){
                doctypelist+=$(this).val()+",";
            }
        })
        return doctypelist;
    },
    //验证选做数量是否正常
    checkInputChooseNum:function($this,val,withNotice){
        var curTotalNum=$this.parents('tr').last().find('.typelist option:selected').val();

        var tmpMsg='';
        var tmpFlag=true;

        if(parseInt(curTotalNum)<2){
            $this.parents('tr').last().find('.choose').click();
            tmpMsg='当前不足两道试题不能设置选做';
            tmpFlag=false;
        }else if (isNaN(val)) {
            $this.val(1);
            $.myZn.totalNum();
            tmpMsg='请输入合法的字符:1-' + curTotalNum + '之间的数字';
            tmpFlag=false;
        }else if(parseInt(val)>=parseInt(curTotalNum)){
            $this.val(1);
            $.myZn.totalNum();
            tmpMsg='选做题数超过了当前选题总数,请输入1-' + curTotalNum + '之间的数字';
            tmpFlag=false;
        }

        if(tmpFlag==false){
            $.myDialog.showMsg(tmpMsg,1,3);
        }

        return true;
    },
    //选题范围和年级的关系
    checkGrade:function(){
        $.myZn.checkGradeDocTypeSelectAll('checkall','doctype');//判断选题范围是否全部选中
        //年级和选题范围关系
        var gradeID='';//年级ID参照物
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
        $.myZn.checkGradeDocTypeSelectAll('checktype','gradelist');//判断年级是否全部选中
    },
    /**
     * 年级和选题范围的关系
     * @author demo
     * @date 2014年10月21日
     */
    checkType:function(){
        $.myZn.checkGradeDocTypeSelectAll('checktype','gradelist');
        //年级和选题范围关系
        var gradeID='';
        $('input[name="gradelist[]"]').each(function(){
            if($(this).attr('checked')=='checked'){
                gradeID +=$(this).attr('value')+',';
            }
        });
        if(gradeID==''){
            $("input[name='doctype[]']").each(function(){
                $(this).attr('disabled',false);
            });
            $('#checkall').attr('disabled',false);
            $('#checkall').attr('checked',false);
            return;
        }
        lastIndex  = gradeID.lastIndexOf(',');
        gradeID = gradeID.substring(0,lastIndex);
        var gradeArr=gradeID.split(',');
        //判断选中的年级ID是否在选题范围的年级ID里
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
        $.myZn.checkGradeDocTypeSelectAll('checkall','doctype');
    },
    //统计试卷总分数
    totalNum:function(){
        var output=0;
        $('.typelist').each(function(){
            var tr=$(this).parents('tr').last();
            if(tr.find('.choose').attr('checked')=='checked'){
                output=output+parseFloat(tr.find('.numlist').val())*parseFloat(tr.find('.choose_x').val());
            }else if(tr.find('.intel').css('display')=='block'){
                output=output+parseFloat($(this).val())*parseFloat(tr.find('.IntelNum option:selected').val())*parseFloat(tr.find('.numlist').val());
            }else{
                output=output+parseFloat($(this).val())*parseFloat(tr.find('.numlist').val());
            }
        });

        output=output.toFixed(1);
        if(output.indexOf('.0')!=-1) output=output.substring(0,output.length-2);
        $('#totalNum').html(output);
    },
    //选题数量变更事件
    changeTestNumEvent:function (){
        $('.typelist').live('change',function(){
            //选取试题小于选做题
            var tr=$(this).parents('tr').last();
            if(tr.find('.choose').attr('checked')=='checked'){
                if(parseInt(tr.find('.typelist option:selected').val())<=parseInt(tr.find('.choose_x').val())){
                    $.myDialog.showMsg('选取试题数量需要大于选做试题数量', 1 ,3);
                    tr.find('.typelist').val(parseInt(tr.find('.choose_x').val())+1);
                }
            }
            $.myZn.totalNum();
        });
    },
    //固定小题数量试题切换事件
    changeIntelNumEvent:function(){
        $('.IntelNum').live('change',function(){$.myZn.totalNum();});
    },
    //选题数量变更事件
    changeNumListEvent:function(){
        $('.numlist').live('change',function(){$.myZn.totalNum();});
    },
    //新增题型 超出限制
    addTestType:function(){
        $('.t_add').live('click',function(){
            if($(this).parent().parent().parent().find('tr').length>20){
                alert('亲爱的老师,题目太多了,给孩子们点时间,让他们思考和总结!');
            }else{
                $(this).parent().parent().after($(this).parent().parent().clone());
                $.myZn.resetBgColor();
            }
            $.myZn.totalNum();
        });
    },
    //删除题型 最有一个
    delTestType:function(){
        $('.t_remove').live('click',function(){
            if($(this).parent().parent().parent().find('tr').length<=2){
                alert('亲爱的老师,题目太少了,孩子们得不到充足的训练!');
            }else{
                $(this).parent().parent().remove();
                $.myZn.resetBgColor();
            }
            $.myZn.totalNum();
        });
    },
    //试题选做状态改变
    testChooseStatusChange:function(){
        $('.choose').live('change',function(){
            if($(this).attr('checked')=='checked'){
                $(this).parents('.intel').last().hide();
                $(this).next('span').css({'display':'inline','font-size':'12px'});
            }else{
                var typeid=$(this).parents('tr').last().find('.typestyle option:selected').val();
                for(var i in Types){
                    if(typeid==Types[i]['TypesID'] && Types[i]['SelectType']==0){
                        $(this).parents('.intel').last().show();
                    }
                }
                $(this).next('span').css({'display':'none'});
            }

            var flag=true;
            if($('.choose_x:visible').length>0){
                $('.choose_x:visible').each(function () {
                    flag =$.myZn.checkInputChooseNum($(this), $(this).val(), true);
                    if (flag == false) {
                        return false;
                    }
                });
            }
            $.myZn.totalNum();
        });
    },
    //试题选做数量改变
    testChooseNumChange:function(){
        $('.choose_x').live('change',function(){
            if(!$.myZn.checkInputChooseNum($(this),$(this).val(),true)){
                return false;
            }
            var flag=true;
            if($('.choose_x:visible').length>1) {
                $('.choose_x:visible').each(function () {
                    flag = $.myZn.checkInputChooseNum($(this), $(this).val(), false);
                    if (flag == false) {
                        return false;
                    }
                });
            }
            $.myZn.totalNum();
        });
    },
    //题型切换
    testTypeSelectChange:function(){
        $('.typestyle').live('change',function(){
            var tag=$(this).parent().parent();
            if($(this).find('option:selected').attr('IfDo')==1){
                tag.find('td:eq(5) div').css({'display':'none'});
                tag.find('.choose').attr('checked',false);
                tag.find('td:eq(5) span').css('display','none');
                tag.find('.choose_x').val(1);
            }else{
                tag.find('td:eq(5) div').css({'display':'block'});
            }
            var typeId = $(this).find('option:selected').val();
            var typesArray=new Array();
            for(var i in Types){
                typesArray[Types[i]['TypesID']]=Types[i];
            }
            var optionStr = '';
            for(var i=0;i<=typesArray[typeId]['Num'];i++){
                optionStr +='<option value='+i+'>'+i+typesArray[typeId]['IntelName']+'</option>';
            }
            var selectStr='';
            if(typesArray[typeId]['SelectType']==0){
                selectStr+='每'+typesArray[typeId]['IntelName']+'<select name="IntelNum" class="IntelNum">';
                for(var i in typesArray[typeId]['IntelNum']){
                    selectStr+='<option value="'+typesArray[typeId]['IntelNum'][i]+'"';
                    selectStr+='>'+typesArray[typeId]['IntelNum'][i]+'小题</option>';
                }
                tag.find('.intel').show().html(selectStr);
            }else{
                tag.find('.intel').hide().html('<select name="IntelNum" class="IntelNum"><option value="0">0</option></select>');
            }
            tag.find('td:eq(1) select').html(optionStr);
            var scoreStr = '';
            if(typesArray[typeId]['MaxScore']<typesArray[typeId]['DScore']) typesArray[typeId]['MaxScore']=typesArray[typeId]['DScore'];
            if(typesArray[typeId]['IfPoint']=='1'){
                for(var i=0;i<typesArray[typeId]['MaxScore'];){
                    i+=0.5;
                    if(i==typesArray[typeId]['DScore']){
                        scoreStr+='<option value='+i+' selected>'+i+'分</option>';
                    }else{
                        scoreStr+='<option value='+i+'>'+i+'分</option>';
                    }
                }
            }else{
                for(var i=0;i<typesArray[typeId]['MaxScore'];){
                    i++;
                    if(i==typesArray[typeId]['DScore']){
                        scoreStr+='<option value='+i+' selected>'+i+'分</option>';
                    }else{
                        scoreStr+='<option value='+i+'>'+i+'分</option>';
                    }
                }
            }
            tag.find('td:eq(4) select').html(scoreStr);
            $.myZn.totalNum();
        });
    },
    //文档属性 全选按钮点击事件
    docTypeSelectAll:function(){
        $('#checkall').live('click',function(){
            $.myZn.checkAll('checkall','doctype');
            $.myZn.checkGrade();
        });
    },
    //年级全选按钮点击事件
    gradeSelectAll:function (){
        $('#checktype').live('click',function(){
            $.myZn.checkAll('checktype','gradelist');
            $.myZn.checkType();
        });
    },
    // 文档属性点击事件
    docTypeClickEvent:function(){
        $(".doctype").live('click',function(){
            $.myZn.checkGrade();
            var flag=0;
            $('.doctype').each(function(){
                if($(this).attr('checked')=='checked'){
                    $(this).parent().css({'color':'#00a0e8'});
                }else{
                    $(this).parent().css({'color':'#000'});
                    flag=1;
                }
            });
            if(flag==1){
                $('#checkall').attr('checked',false);
                $('#checkall').parent().css({'color':'#000'});
            }
            else{
                $('#checkall').attr('checked','checked');
                $('#checkall').parent().css({'color':'#00a0e8'});
            }
        });
    },
    //年级点击事件
    gradeClickEvent:function() {
        $(".checktype").live('click', function () {
            $.myZn.checkType();
            var flag = 0;
            $('.checktype').each(function () {
                if ($(this).attr('checked') == 'checked') {
                    $(this).parent().css({'color': '#00a0e8'});
                } else {
                    $(this).parent().css({'color': '#000'});
                    flag = 1;
                }
            });
            if (flag == 1) {
                $('#checktype').attr('checked', false);
                $('#checktype').parent().css({'color': '#000'});
            }
            else {
                $('#checktype').attr('checked', 'checked');
                $('#checktype').parent().css({'color': '#00a0e8'});
            }
        });
    },
    //跳转第二步
    toStep2Click:function(){
        $('.tostep2').live('click',function(){
            var ts=0;
            $('#categorycheckbox input').each(function(){
                if($(this).attr('checked')=='checked') ts=1;
            });
            if(!ts){
                $.myDialog.showMsg('请选择知识点',1);
                return false;
            }
            $('.step').css('display','none');
            $('#step2').css('display','block');
            if($('.testRound').length==0){
                $('#quesnumsetting').html($.myCommon.loading());
                $.get(U('Ga/Index/getTypes?id='+subjectID),{'times':Math.random()},function(data){
                    $('#quesnumsetting').html('');
                    if($.myCommon.backLogin(data)==false){
                        return false;
                    };
                    if(data['data'][3]==0){
                        var output ='<div class="gradeList">年&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;级：<label style="color: rgb(0, 160, 232)"><input type="checkbox" checked="checked"  name="checkGrade" value="" id="checktype">全选</label> ';
                    }else{
                        var output ='<div class="gradeList">年&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;级：<label><input type="checkbox" checked="checked"  name="checkGrade" value="" id="checktype">全选</label> ';
                    }

                    for(var j in data['data'][2]){
                        if(data['data'][3]==0){
                            output+='<label style="color: rgb(0, 160, 232)"><input type="checkbox" checked="checked" name="gradelist[]" class="checktype" value="'+data['data'][2][j]['GradeID']+'"/>'+data['data'][2][j]['GradeName']+'&nbsp;</label>';
                        }else if(data['data'][3]==data['data'][2][j]['GradeID']){
                            output+='<label style="color: rgb(0, 160, 232)"><input type="checkbox" checked="checked" name="gradelist[]" class="checktype" value="'+data['data'][2][j]['GradeID']+'"/>'+data['data'][2][j]['GradeName']+'&nbsp;</label>';
                        }else{
                            output+='<label><input type="checkbox" name="gradelist[]" class="checktype" value="'+data['data'][2][j]['GradeID']+'"/>'+data['data'][2][j]['GradeName']+'&nbsp;</label>';
                        }
                    }
                    output +='</div>';
                    output +='<div class="testRound">选题范围：<label><input name="doctype" type="checkbox" value="0" class="doctypeall" id="checkall" checked="checked"/>全选</label>';
                    for(var i in data['data'][1]){
                        output+='<label><input name="doctype[]" type="checkbox" value="'+data['data'][1][i]['TypeID']+'" class="doctype" thisid="'+data['data'][1][i]['GradeList']+'" checked="checked"/>'+data['data'][1][i]['TypeName']+'</label> ';
                    }
                    output+='</div>';

                    $('#quesnumsetting').append(output);
                    output='';
                    Types=data['data'][0];

                    if(typeof(data['data'][4])=='undefined' || data['data'][4]==null || data['data'][4]==''){
                        for(var i=0;i<data['data'][0].length;i++){
                            output+=$.myZn.getTypesStr(data['data'][0][i]['Num'],data['data'][0][i]['IntelName'],$.myZn.getTypesSelect(data['data'][0],data['data'][0][i]['TypesID']),i,data['data'][0][i]['TypesScore'],data['data'][0][i]['SelectType'],data['data'][0][i]['IntelNum'],data['data'][0][i]['TypesID'],data['data'][0][i]['Score'],data['data'][0][i]['IfDo'],data['data'][0][i]['IfPoint'],data['data'][0][i]['MaxScore']);
                        }
                   }else{
                        for(var i=0;i<data['data'][4].length;i++){
                            output+= $.myZn.getTypesStr(data['data'][4][i]['Num'],data['data'][5][data['data'][4][i]['TypesID']],$.myZn.getTypesSelect(data['data'][0],data['data'][4][i]['TypesID']),i,data['data'][4][i]['TypesScore'],data['data'][4][i]['SelectType'],data['data'][4][i]['IntelNum'],data['data'][4][i]['TypesID'],data['data'][4][i]['Score'],data['data'][4][i]['IfDo'],data['data'][4][i]['IfPoint'],data['data'][4][i]['MaxScore'],data['data'][4][i]['ChooseNum'],data['data'][4][i]['selectNum']);
                        }
                    }
                    $('#quesnumsetting').append('<table border="0" cellpadding="5" cellpaseing="0">'+output+'<tr><td colspan="4"><span class="fs_tit">总分：</span><span id="totalNum">0</span><span>分</span></td></tr></table>');
                    $.myZn.checkGrade();
                    $.myZn.initDivXdBox();
                    $.myZn.totalNum();
                });
            }
        });
    },
/********************************************************************
 * 第二界面结束
 * 第三界面开始
 ********************************************************************/
    //跳转第三步
    toStep3Click:function(){
        $('.tostep3').click(function(){
            if(!$.myZn.getDocType()){
                $.myDialog.showMsg('请选择选题范围！',1);
                return false;
            }
            if(!$.myZn.getGradeList()){
                $.myDialog.showMsg('请选择年级！',1);
                return false;
            }
            if(!$.myZn.checkChoose()){
                return ;
            }
            $('.step').css('display','none');
            $('#step3').css('display','block');
            $.myZn.initDivXdBox();
        });
    },
    //重置题型背景色
    resetBgColor:function(){
        var t_length=$('#quesnumsetting tr').length-1;
        $('#quesnumsetting tr').each(function(i){
            if(i==t_length) return;
            if(i%2==0) $(this).css({'background-color':'#fff'});
            else  $(this).css({'background-color':'#edeef0'});
        });
    },
    //智能组卷事件
    startSelectTest: function () {
        $('#submit').click(function () {
            $('.step').css('display', 'none');
            $('#step4').css('display', 'block');
            $('#queslistbox').html('');
            $('#quesresult').html('<p class="list_ts"><span class="ico_dd">正在组卷请稍候...</span></p>');

            //获取知识点
            var kllist = ''; //知识点
            $('#categorycheckbox .kllist').each(function () {
                if ($(this).attr('checked') == 'checked' && $(this).attr('val') != 'no') kllist += $(this).val() + ',';
            });
            var klfromchapter = $('#rangeSelect').val();     //判断知识点是否章节版本

            if (!kllist) {
                $.myDialog.showMsg('请选择知识点', 1);
                $('.list_ts').html('组卷失败，请选择知识点');
                return false;
            }

            //获取选题范围
            var doctype = ''; //选题范围
            $('.doctype').each(function () {
                if ($(this).attr('checked') == 'checked') {
                    doctype += ',' + $(this).val();
                }
            });
            if (doctype == '') {
                $.myDialog.showMsg('请选择选题范围', 1);
                return false;
            } else {
                doctype = doctype.substring(1);
            }
            //获取年级
            var gradeID = '';
            $('.checktype').each(function () {
                if ($(this).attr('checked') == 'checked') gradeID += ',' + $(this).val();
            });
            if (gradeID == '') {
                $.myDialog.showMsg('请选择年级', 1);
                return false;
            } else {
                gradeID = gradeID.substring(1);
            }

            var typelist = '';     //试题类型数量
            var numlist = '';        //试题分值
            var typestyle = '';    //试题类型id
            var typesscore = '';    //试题计分方式
            var selecttype = '';    //试题选题方式
            var choosetype = '';    //选做方式
            var choosenum = '';    //选做数量
            var testTotle = 0;    //试题总数
            var intelName = '';    //选取单位
            var intelNum = '';    //选取数量
            $('.typelist').each(function () {
                if ($(this).val() != 0) {
                    typelist += $(this).val() + ',';
                    numlist += $(this).parent().parent().find('.numlist').val() + ',';
                    testTotle += parseInt($(this).val());
                    typestyle += $(this).parent().parent().find('.typestyle').val() + ',';
                    typesscore += $(this).parent().parent().find('.typesscore').val() + ',';
                    selecttype += $(this).parent().parent().find('.selecttype').val() + ',';
                    intelName += $(this).parent().parent().find('.IntelName').val() + ',';
                    if ($(this).parent().parent().find('td').eq(3).css('visibility') == 'visible') {
                        intelNum += $(this).parent().parent().find('.IntelNum').val() + ',';
                    } else {
                        intelNum += '0' + ',';
                    }

                    if ($(this).parent().parent().find('.choose').attr('checked') == 'checked') {
                        choosetype += '1,';
                    } else {
                        choosetype += '0,';
                    }
                    choosenum += $(this).parent().parent().find('.choose_x').val() + ',';
                }
            });
            if (testTotle > 70) {
                $.myDialog.showMsg('试题数量超过限制！', 1);
                $('.list_ts').html('组卷失败！试题数量超过限制.');
                return false;
            }
            if (!typelist) {
                $.myDialog.showMsg('请选择试题类型', 1);
                $('.list_ts').html('组卷失败，请选择试题类型');
                return false;
            }
            typelist = typelist.substring(0, typelist.length - 1);
            numlist = numlist.substring(0, numlist.length - 1);
            typestyle = typestyle.substring(0, typestyle.length - 1);
            typesscore = typesscore.substring(0, typesscore.length - 1);
            selecttype = selecttype.substring(0, selecttype.length - 1);
            intelNum = intelNum.substring(0, intelNum.length - 1);
            kllist = kllist.substring(0, kllist.length - 1);
            choosetype = choosetype.substring(0, choosetype.length - 1);
            choosenum = choosenum.substring(0, choosenum.length - 1);
            var diff = $('#diffNum').html();        //难度值
            var kl = $('#klNum').html();        //知识点覆盖率
            if (kl == '0%') {
                $.myDialog.showMsg('知识点覆盖率不能为0%', 1);
                $('.list_ts').html('组卷失败，知识点覆盖率不能为0%');
                return false;
            }
            kl = '0.' + kl.replace('%', '');
            $.post(U('Ga/Index/ga'), {'subject': subjectID, 'typelist': typelist, 'numlist': numlist, 'intelNum': intelNum, 'typestyle': typestyle, 'typesscore': typesscore, 'selecttype': selecttype, 'kl': kl, 'kllist': kllist, 'klfromchapter': klfromchapter, 'diff': diff, 'choosetype': choosetype, 'choosenum': choosenum, 'doctype': doctype, 'gradeid': gradeID, 'times': Math.random()}, function (data) {
                if ($.myCommon.backLogin(data) == false) {
                    $('#quesresult').html('<p class="list_ts">' + data['data'] + '</p>');
                    return false;
                }
                if (typeof(data['data']) != 'string') {
                    $('#quesresult').html('<p class="quesresult-title"><cite>试卷总分：<b>' + data['data'][3] + '</b></cite><cite>当前试卷难度：<b>' + data['data'][2] + '</b></cite></p><input type="hidden" name="autoaddid" class="autoaddid" value="' + data['data'][5] + '">');
                    var testString = '';
                    var typesArr = typestyle.split(',');
                    for (var i in data['data'][0]) {
                        testString += '<div class="intelTitle">' +
                            shuzi[i] + '、' + $.myTest.getTypes(Types, typesArr[i], 'TypesName') +
                            '(共' + data['data'][4][i]['testNum'] + '题' +
                            (parseInt(data['data'][4][i]['chooseNum']) == 0 ? '' : '任选' + data['data'][4][i]['chooseNum'] + '题') +
                            (data['data'][4][i]['preTest'] == 0 ? '' : '，每题' + data['data'][4][i]['preTest'] + '分') + '，共' +
                            data['data'][4][i]['score'] + '分)</div>';
                        testString += $.myTest.showTest(data['data'][0][i]);
                    }
                    $('#queslistbox').html(testString);
                } else {
                    $('#quesresult').html('<p class="list_ts">' + data['data'] + '</p>');
                }

                $('.addquessel').css('display', 'none');
                $('.selmore').css('display', 'none');
                $('.selpicleft').css('display', 'none');
                $('.delques').css('display', 'none');
                $.myZn.initDivXdBox();
            });
        });
    },
/********************************************************************
 * 第三界面结束
 * 第四界面开始
 ********************************************************************/
    //进入试卷中心
    toTestCenter:function(){
        $('#topapercenter').click(function(){
            if($('.addquessel').length==0){
                $.myDialog.normalMsgBox('msgbox','系统提示',450,'没有试题！请等待或重新组卷；',4);
                return false;
            }
            //载入中
            $.myDialog.normalMsgBox('msgbox','下面转入试卷中心',450,$.myCommon.loading(),4);

            //重置试题篮 题型名称 试题数量
            var juan1=[];
            juan1.push('parthead1@$@1@$@第I卷（选择题）@$@请点击修改第I卷的文字说明');
            var juan2=[];
            juan2.push('parthead2@$@1@$@第II卷（非选择题）@$@请点击修改第II卷的文字说明');

            //存入试题和试卷初始cookie
            $('#quescount',window.parent.document).html(0);
            editData.clear();
            editData.set('init',juan1.join('@#@')+'@#@'+juan2.join('@#@'));

            $('#quescountdetail tr',window.parent.document).remove();
            $('#quescount',window.parent.document).empty().html(0);
            $('#quesbanklist',window.parent.document).remove();

            $('.delques').addClass('addquessel').removeClass('delques');
            $('#step4Scroll').scrollTop(0);
            $.myZn.addPaperCenter();
        });
    },
    //进入组卷中心前的加入试卷动画
    addPaperCenter:function(){
        var thisTop=$('#step4Scroll').scrollTop();

        setTimeout(function(){
            if(thisTop==0) thisTop=parseInt($('#queslistbox').offset().top)-parseInt($('#quesresult').offset().top);
            else thisTop+=parseInt($('.delques').last().parents('.quesbox').last().outerHeight())+10;

            if($('.addquessel').eq(0).parents('.quesbox').last().prev().find('.addquessel,.delques').length==0){
                thisTop+=parseInt($('.addquessel').eq(0).parents('.quesbox').last().prev().outerHeight());
            }
            $('.addquessel').eq(0).click();
            $('#step4Scroll').animate({"scrollTop":thisTop},50,function(){
                if($('.addquessel').length==0){
                    $.myZn.jumpPaperCenter();
                }else{
                    $.myZn.addPaperCenter();
                }
            });
        },50);
    },
    //组卷中心跳转
    jumpPaperCenter:function (){
        var updateid=$('.autoaddid').val();
        $.ajax({
            type: "POST",
            cache: false,
            url: U('Ga/Index/editContent'),
            data: {'Content':editData.getall(),'ID':updateid,'times':Math.random()},
            success: function(msg){
                if(msg['data']=='OK'){
                    window.location.href=U('Home/Index/zuJuan');
                }else{
                    //失败后，加入日志记录
                    window.location.href=U('Home/Index/zuJuan');
                }
            }
        });
    },
    //全部选入试题栏
    addTestIntoTestBox:function (){
        $('#selectallques').live('click',function(){
            $('.addques').each(function(){
                $(this).click();
            });
        });
    }
}
/********************************************************************
 * 第四界面结束
 ********************************************************************/