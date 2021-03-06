jQuery.workDown = {
    //初始化
    init:function(){
        this.checkBoxClick(); //复选框点击事件
        this.downPaperEvent(); //下载试卷按钮事件
        this.downFeedBack(); //下载反馈点击事件
    },
    //复选框点击事件
    checkBoxClick:function(){
        $("span.checkspan").live('click',function() {
            $(this).find('input').attr("checked", true);
        });
    },
    //提示信息
    addMsgStyle:function(msg) {
        return "<span style=\"font-size:14px;font-weight:bold;display:block;overflow:hidden;\">" + msg + "</span>";
    },
    //下载试卷按钮事件
    downPaperEvent:function(){
        $("#submitbtn,#wordicon").live('click',function() {
            $.workDown.submit();
        });
        $("#closebtn").live('click',function() {
            var thisid=$(this).parents().find('.dialog').last().attr('id');
            $.workDown.close(thisid);
        });
    },
    //获取当前时间
    getCurrentTime:function() {
        var d = new Date();
        return d.getFullYear() + "/" +
            (d.getMonth() + 1) + "/" +
            d.getDate() + " " +
            d.getHours() + ":" +
            d.getMinutes() + ":" +
            d.getSeconds() + "." + d.getMilliseconds();
    },
    //时间差
    timeDiff:function(time1, time2) {//毫秒差
        var time1Ary = time1.split(".");
        var time2Ary = time2.split(".");
        var d1 = new Date(time1Ary[0]);
        var d2 = new Date(time2Ary[0]);
        var timediff = parseInt(d1 - d2) + parseInt(time1Ary[1] - time2Ary[1]);
        return timediff;
    },
    //获取选中的值
    getCheckedValue:function (tname){
        var tvalue='';
        $('.'+tname).each(function(){
            if($(this).attr("checked")) tvalue=$(this).val();
        });
        return tvalue;
    },
    //提示信息
    alert:function(str) {
        $('#koudiantishi').html(str).show();
    },
    
    getSubmitData:function(down_type) {
        var tishi=$('#koudiantishi').html();
        if(tishi.indexOf('正在生成请稍候')!=-1){
            return false;
        }
        var verifycode = $("#verifyCode").val(); //验证码
        $("#verifyCode").val('');
        if (verifycode.length != 4) {
            var msg = $.workDown.addMsgStyle("验证码不正确，请重新填写。（不用区分大小写）");
            $("#verifyImg").click();
            $.workDown.alert(msg);
            return false;
        }
        var data=[]; //数据返回
        var url=''; //提交地址
        var addCookie=0; //加入cookiejson
        
        var subjectID=Cookie.Get("SubjectId");
        $.workDown.alert($.workDown.addMsgStyle('<p class="list_ts c"><span class="ico_dd">正在生成请稍候...</span></p>'));
        
        switch(down_type){
            case '1':
                //教师作业下载
                var down_id=$('#zjdown_id').val(); //作业id
                var workStyle=$('#workStyle').val(); //作业id
                url=U('Work/workDown');
                data.push('"WorkID":'+down_id);
                data.push('"workStyle":"'+workStyle+'"');
                break;
            case '2':
                //组卷下载
                var quescount = $("div.quesdiv").length;
                if (quescount == 0) {
                    var msg = $.workDown.addMsgStyle("当前没有试题，请先手动或智能挑选试题。");
                    $.workDown.alert(msg);
                    return false;
                }
                url=U('Index/create');
                data.push('"testList":"'+editData.gettestid()+'"');
                data.push('"SubjectID":"'+subjectID+'"');
                data.push('"cookiestr":"'+editData.getall()+'"');
                break;
            case '3':
                //导学案下载
                var quescount = $("span.quesindex").length+$("span.quesindexnum").length;
                if (quescount == 0) {
                    var msg = $.workDown.addMsgStyle("当前没有知识或试题，请先挑选知识或试题。");
                    $.workDown.alert(msg);
                    return false;
                }
                url=U('Guide/Case/create');
                data.push('"testList":""');
                data.push('"SubjectID":"'+subjectID+'"');
                //data.push('"cookiestr":"'+ localData.get('caseStyle')+'"');
                //data.push('"cookiestr":'+ $.caseCommon.tempContent);
                addCookie=1; //加入cookiejson
                break;
        }
        
        data.push('"verifycode":"'+verifycode+'"');
        
        var result=Array();
        result[0]=data;
        result[1]=url;
        result[2]=addCookie;
        return result;
    },
    //提交数据生成试卷
    submit:function() {
        var down_type=$('#zjdown_type').val(); //提交类型
        var result=$.workDown.getSubmitData(down_type);
        if(result==false){
            return;
        }
        
        var data=result[0];
        var url=result[1];
        var addCookie=result[2];
        
        var issaverecord = $("#saverecord").attr("checked")=='checked' ? 1 : 0 ;
        var ifShare = $("#shareto").attr("checked")=='checked' ? 1 : 0 ;
        var docversion = $.workDown.getCheckedValue("docversion");
        var papersize = $.workDown.getCheckedValue("papersize");
        var papertype = $.workDown.getCheckedValue("papertype");

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
    },
    //ajax生成试卷
    submitData:function(url,tmp_data) {
        $.post(url,tmp_data,function(data){
            if($.myCommon.backLogin(data)==false){
                $.workDown.alert($.workDown.addMsgStyle(data['data']));
                return false;
            }
            if(data['data'].indexOf('success')!=-1){
                var tmp_str=data['data'].split('#$#');
                if($('#tmp_iframe').length==0) $('body').append('<div id="tmp_iframe" style="display:none;"></div>');
                $('#tmp_iframe').html(tmp_str[1]);
                $('#verifyImg').click();
            }else{
                if(data['data'][0]==0){
                    $.workDown.showLimitDown(data['data'][1]);
                }else{
                    $.workDown.alert($.workDown.addMsgStyle(data['data']));
                }
            }
            if($('#downdiv').length>0) $.myDialog.tcDivPosition('downdiv');
            else $.myDialog.tcDivPosition('examDiv');
        });
    },
    showLimitDown:function(testList){
        if(testList){
            var showList='';
            for(var i in testList){
                showList+='<p>试卷中第<i style="color:red">'+testList[i]['TestNum']+'</i>题，您没有权限下载！ 请【<a class="red" target="_top" href="'+U('Index/About/operLevelDetail')+'">升级权限</a>】或删除以上试题。</p>';
            }
            $('#downdiv .tcClose').click();
            $.myDialog.normalMsgBox('limitDownMsg','温馨提示',500,showList,5);
        }
    },
    //关闭提示框
    close:function(id) {
        if(typeof(id)=='undefined' || id=='') {
            var dialogs = $("div.dialog");
            for(var i = 0; i < dialogs.length; i++) {
                dialogs.eq(i).find("a.tcClose").click();
            }
            return ;
        }
        
        $('#'.id).find('.tcClose').click();
    },
    //用户下载反馈
    downFeedBack:function(){
        $('.downFeedback').live('click',function(){
            var boxID='userFeedback';
            var boxTitle='用户下载反馈';
            var errortype=[['1','公式不显示',''],['2','Word打不开',''],['3','Word乱码',''],['0','其他','checked']];
            var tmp_str='<div id="correctiondata" style="display:none;"></div>'+'<div style="font-size:14px;"><p><span>错误类型：</span><span>';
            for(x in errortype){
                tmp_str+='<label><input type="checkbox" value="'+errortype[x][1]+'" name="feedbacktype" class="errortype" title="'+errortype[x][1]+'" '+errortype[x][2]+' >'+errortype[x][1]+'&nbsp;&nbsp;</label>';
            }
            tmp_str+='</span></p></div>'+
            '<p>错误描述：</p><div class="correctionBox"><textarea cols="65" rows="4" id="feedbackContent">我来说两句~</textarea>'+
            '</div>';
            var downContent="";
            $.myDialog.normalMsgBox(boxID,boxTitle,500,tmp_str,3);
        });
        $('#userFeedback .normal_yes').live('click',function(){
            var typeVal='';
            $('.errortype').each(function(){
                if($(this).attr('checked')){
                    typeVal+=$(this).val()+',';
                }
            });
            //保存docSave 所需参数
            var cookieContent = '',paperName='';
            var down_type=$('#zjdown_type').val(); //提交类型
            switch(down_type){
                case '1'://教师作业
                    cookieContent = $('#zjdown_id').val(); //作业记录作业ID
                    paperName = $('#li'+cookieContent).find('.workTitle').html(); //文档名称
                    break;
                case '2'://试卷
                    cookieContent = editData.getall(); //cookie内容
                    paperName = $('#pui_maintitle').html(); //文档名称
                    break;
                case '3'://导学案
                    cookieContent = editData.getall('caseStyle');//cookie内容
                    paperName = $('.tempname').html();
                    break;
            }
            var testList=editData.gettestid(); //试题ID ，分割

            //feedback 反馈内容所需参数
            var content=$('#feedbackContent').val();
            $.post(U('Home/Index/addFeedBack'),{'typeVal':typeVal,'content':content,'testList':testList,'cookieContent':cookieContent,'paperName':paperName},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                var msg=data['data'];
                if(msg['status']=='1'){
                    $.myDialog.showMsg('反馈成功！');
                    $('#userFeedback .tcClose').click();
                }else{
                    $.myDialog.showMsg('提交失败！');
                }
            })
        })
    }
}
$.workDown.init();