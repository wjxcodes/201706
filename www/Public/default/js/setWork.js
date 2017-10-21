// 日期控件
$.myCommon.addDateControl();
//布置作业
$.myWorkCommon={
    workStyle:'test', //页面类型 test case
    workName:'作业', //页面类型 作业 导学案
    init:function(workStyle){
        if(workStyle=='case'){
            $.myWorkCommon.workStyle=workStyle;
            $.myWorkCommon.workName='导学案';
        }
    },

    //布置作业内容
    addWorkPaperAction:function(workName,testList,cookieData,workID,addStyle){
        var testStr='';
        var dataStr='';
        var idStr='';
        var workAddStyle='';
        var showTitle='';

        testStr      += '<input name="testList" type="hidden" value="'+testList+'" class="testList"/>';
        dataStr      += '<input name="data" type="hidden" value="'+cookieData+'" class="data"/>';
        idStr        += '<input name="work_WorkID" type="hidden" value="'+workID+'" class="work_WorkID"/>';
        workAddStyle += '<input name="workAddStyle" type="hidden" value="'+addStyle+'" class="workAddStyle"/>';
        showTitle    += '【'+workName+'】';

        var tmp_str='<div class="work_tit">'+$.myWorkCommon.workName+'名称：<input name="work_name" class="work_name" size="50" type="text" value="'+workName+'"/></div>'+
            '<div class="work_tit">'+$.myWorkCommon.workName+'对象</div>'+
            '<div class="work_classlist">'+$.myCommon.loading()+'</div>'+
            '<div class="work_tit">答题方式</div>'+
            '<div class="work_teststyle">'+
            '    <div class="work_online workItem workItemChecked"><i class="iconfont">&#xe600;</i><input name="work_style" class="work_style" type="radio" value="0" checked="checked"/>在线作答（适合选择题）</div>'+
            '    <div class="work_down workItem"><i class="iconfont">&#xe601;</i><input name="work_style" class="work_style" type="radio" value="1"/>下载作答（适合主观题）</div>'+
            '</div>'+
            '<div class="work_tit">答题时间</div>'+
            '<div class="work_time">'+
            '    <div class="work_start">'+
            '        <div>开始时间(此时间之后可以看到'+$.myWorkCommon.workName+')</div>'+
            '        <div><input name="work_starttime" class="work_starttime inputTime" type="text" placeholder="开始时间" value=""/></div>'+
            '    </div>'+
            '    <div class="work_end">'+
            '        <div>结束时间(此时间之前可以做答'+$.myWorkCommon.workName+')</div>'+
            '        <div><input name="work_endtime" class="work_endtime inputTime" type="text" placeholder="结束时间" value=""/></div>'+
            '    </div>'+
            '</div>'+
            '<div class="work_tit">答题顺序</div>'+
            '<div class="work_testorder">'+
            '    <div class="work_ordersame workItem workItemChecked"><i class="iconfont">&#xe600;</i><input name="work_order" class="work_order" type="radio" value="0" checked="checked"/>所有学生题目顺序相同</div>'+
            '    <div class="work_orderrand workItem"><i class="iconfont">&#xe601;</i><input name="work_order" class="work_order" type="radio" value="1"/>所有学生题目顺序不相同</div>'+
            '</div>'+
            '<div class="work_tit">'+$.myWorkCommon.workName+'描述</div>'+
            '<div class="work_description"><textarea name="work_area" class="work_area"></textarea></div>'+
            idStr+testStr+dataStr+workAddStyle+
            '<input name="work_UserID" type="hidden" value="" class="work_UserID"/><p></p>';


        $.myDialog.normalMsgBox('addworkdiv','布置'+$.myWorkCommon.workName+''+showTitle+'',570,tmp_str,3);
        var output='';
        //载入布置作业数据 班级
        $.post(U('Work/MyClass/getClassList'),{'times':Math.random()},function(data){
            var tit='提示信息';
            if($.myCommon.backLogin(data)==false){
                $('.work_classlist').html('班级加载失败!');
                return false;
            }
            output='';
            if(data['data'][0]=='success'){
                for(var i in data['data'][1]){
                    output+='<span class="class_li"><input type="checkbox" name="class_select" class="class_select" value="'+data['data'][1][i]['ClassID']+'" title="'+data['data'][1][i]['ClassName']+'"/><span class="class_text">'+data['data'][1][i]['ClassName']+'</span></span>';
                }
                $('.work_classlist').html(output);
            }else if(data['data'][0]=='add'){
                $('#addworkdiv .tcClose').click();
                $.myDialog.normalMsgBox('loadclassmsgdiv',tit,450,'您还未加入任何班级！是否立刻加入？',3);
                $(document).on('click', '#loadclassmsgdiv .normal_yes', function () {
                    window.location = U('Work/MyClass/myClass');
                });
            }else{
                $.myDialog.normalMsgBox('msgdiv',tit,450,data['data'][1],2);
            }
        });

        // 布置作业日期插件
        // 选择时间日期
        var inputTime = $('.inputTime');
        // 选择起止时间
        // if (startTime.length > 0) {
        //     var start = {
        //         elem: "#startTime",
        //         format: 'YYYY-MM-DD hh:mm:ss',
        //         max: '2099-06-16 23:59:59', //最大日期
        //         istime: true,
        //         istoday: true,
        //         festival: true,
        //         choose: function(datas) {
        //             end.min = datas; //开始日选好后，重置结束日的最小日期
        //             end.start = datas; //将结束日的初始值设定为开始日
        //         }
        //     };
        //     var end = {
        //         elem: "#endTime",
        //         format: 'YYYY-MM-DD hh:mm:ss',
        //         max: '2099-06-16 23:59:59',
        //         istime: true,
        //         istoday: true,
        //         festival: true,
        //         choose: function(datas) {
        //             start.max = datas; //结束日选好后，重置开始日的最大日期
        //         }
        //     };
        //     laydate(start);
        //     laydate(end);
        // }

        // 输入时间
        if (inputTime.length > 0) {
            inputTime.on("focus", function() {
                laydate({
                    format: 'YYYY-MM-DD hh:mm:ss',
                    max: '2099-06-16 23:59:59',
                    istime: true,
                    istoday: true,
                    festival: true
                });
            });
        };

        // 设置默认时间段
        function setWorkDefTime() {
            //补齐数位
            function digit(num) {
                return num < 10 ? '0' + (num | 0) : num;
            };
            //获得当月天数
            function getDays() {
                var date = new Date();
                var year = date.getFullYear();
                var mouth = date.getMonth()+1;
                //定义当月的天数；
                var days;
                //当月份为二月时，根据闰年还是非闰年判断天数
                if (mouth == 2) {
                    // 判断闰年
                    days = ((year % 4 === 0 && year % 100 !== 0) || year % 400 === 0) ? 29 : 28;
                } else if (mouth == 1 || mouth == 3 || mouth == 5 || mouth == 7 || mouth == 8 || mouth == 10 || mouth == 12) {
                    days = 31;
                } else {
                    days = 30;
                }
                return days;
            }
            var work_starttime = $(".work_starttime");
            var work_endtime = $(".work_endtime");
            var defTimeStrat = " " + "18:00:00",
                defTimeEnd = " " + "21:00:00";
            var MyDate = new Date();
            var YY = MyDate.getFullYear();
            var MM = MyDate.getMonth()+1;
            var DD = MyDate.getDate();
            var HH = MyDate.getHours();
            var MI = MyDate.getMinutes();
            var Day = MyDate.getDay();

            // 设置默认开始时间
            var defStartFullTime = YY + '-' + digit(MM) + '-' + digit(DD) + defTimeStrat;
            work_starttime.val(defStartFullTime);
            // 默认结束时间
            var endYY = YY;
            var endMM = digit(MM);
            var endDD = digit(DD);

            // 默认周六作答时间为两天
            if (Day == 6) {
                endDD = digit(DD + 2);
            }
            if (endDD > getDays()) {
                endMM = parseInt(endMM) + 1;
                if (endMM > 12) {
                    endMM = 1;
                    endYY += 1;
                }
                endDD = endDD - getDays();
            }
            // 设置默认结束时间
            var defEndFullTime = endYY + '-' + digit(endMM) + '-' + digit(endDD) + defTimeEnd;
            work_endtime.val(defEndFullTime);

        }
        setWorkDefTime();
        $.myWorkCommon.addWorkContent();
    },
    addWorkContent:function(){
        //布置作业确定
        $('#addworkdiv .normal_yes').live('click',function(){
            var self=$(this);
            if(self.attr('clicked')==1){
                return false;
            }
            self.attr('clicked',1);
            var WorkID=$('.work_WorkID').val();
            var WorkName=$('.work_name').val();
            var ClassUser=$('.work_UserID').val();

            //zujuan
            var addStyle = $('.workAddStyle').val();
            var testList = '';
            var testData = '';
            if(addStyle == 'zujuan'){
                testList=$('.testList').val();
                testData=$('.data').val();
            }

            if(ClassUser==''){
                self.attr('clicked',0);
                $.myDialog.normalMsgBox('msgdiv','提示信息',450,'请选择班级',2);
                return;
            }

            var WorkStyle=0;
            $('.work_style').each(function(){
                if($(this).attr('checked')=='checked') WorkStyle=$(this).val();
            });
            var WorkStartTime=$('.work_starttime').val();
            var WorkEndTime=$('.work_endtime').val();
            var WorkOrder=0;
            $('.work_order').each(function(){
                if($(this).attr('checked')=='checked') WorkOrder=$(this).val();
            });
            var WorkDescription=$('.work_area').val();

            $.myDialog.normalMsgBox('loadingMsg','提示信息',450,'正在创建'+$.myWorkCommon.workName+'请稍候...');
            $.post(U('Work/Work/assignLeavedUserWork?workStyle='+$.myWorkCommon.workStyle),{'workID':WorkID,'workName':WorkName,'userList':ClassUser,'assignStyle':WorkStyle,'startTime':WorkStartTime,'endTime':WorkEndTime,'assignOrder':WorkOrder,'description':WorkDescription,'data':testData,'testList':testList,'workAddStyle':addStyle,'times':Math.random()},function(data){
                var tit='发布'+$.myWorkCommon.workName;
                $('#loadingMsg .tcClose').click();
                if($.myCommon.backLogin(data)==false){
                    self.attr('clicked',0);
                    return false;
                }
                $('#addworkdiv .tcClose').click();
                $.myDialog.normalMsgBox('msgdiv',tit,450,'发布'+$.myWorkCommon.workName+'成功！',1);
            });
        });
        //点击班级
        $('.class_text').live('click',function(){
            var tit=$(this).html()+'学生列表';
            var ClassID=$(this).prev().val();

            //获取班级下已经被选中的userid
            var UserIDList=$('.work_UserID').val();
            var userlist='';
            var tmp_arr_1,tmp_arr_2,i,j,select;
            if(('|'+UserIDList+'|').indexOf('|'+ClassID+';')!=-1){
                tmp_arr_1=UserIDList.split('|');
                for(i in tmp_arr_1){
                    tmp_arr_2=tmp_arr_1[i].split(';');
                    if(tmp_arr_2[0]==ClassID){
                        userlist=tmp_arr_2[1];
                    }
                }
            }
            //显示班级里的学生
            $.post(U('Work/MyClass/getStudentList'),{'ClassID':ClassID,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    $.myDialog.normalMsgBox('userdiv',tit,450,'获取学生列表失败 请重试...',2);
                    return false;
                }
                if(data['data'][0]=='success'){
                    if(userlist=='0') select='checked="checked"';
                    else select='';
                    var output='<span><label><input name="UserID" class="UserAll" type="checkbox" value="'+ClassID+'" '+select+'/>全部</label></span>';
                    for(var i in data['data'][1]){
                        if(userlist=='0' || (','+userlist+',').indexOf(','+data['data'][1][i]['UserID']+',')!=-1) select='checked="checked"';
                        else select='';
                        output+='<span><label><input name="UserID" class="UserID" type="checkbox" value="'+data['data'][1][i]['UserID']+'" '+select+'/>'+data['data'][1][i]['RealName']+'</label></span>';
                    }
                    output+='<input name="userdiv_classid" type="hidden" value="'+ClassID+'" class="userdiv_classid"/>';
                    $.myDialog.normalMsgBox('userdiv',tit,450,output,3);
                }else{
                    $.myDialog.normalMsgBox('userdiv',tit,450,data['data'],2);
                }
            });
        });
        //显示班级下的学生 选择确定
        $('#userdiv .normal_yes').live('click',function(){
            var ClassID=$('.UserAll').val();
            if($('.UserAll').attr('checked')!='checked'){
                clearClass(ClassID);
                var userlist='';
                $('.UserID').each(function(){
                    if($(this).attr('checked')=='checked'){
                        userlist+=','+$(this).val();
                    }
                });
                if(userlist!=''){
                    userlist=userlist.substr(1);
                    $('.class_select[value="'+ClassID+'"]').attr('checked','checked').css({'background-color':'#ccc'});
                }else{
                    $('.class_select[value="'+ClassID+'"]').attr('checked',false).css({'background-color':'#fff'});
                }
                addClassUser(ClassID,userlist);
            }else{
                clearClass(ClassID);
                addClass(ClassID);
                $('.class_select[value="'+ClassID+'"]').attr('checked','checked').css({'background-color':'#fff'});
            }
            $('#userdiv .tcClose').click();
        });
        //选择班级
        $('.class_select').live('click',function(){
            var ClassID=$(this).val();
            if($(this).attr('checked')=='checked'){
                addClass(ClassID);
            }else{
                clearClass(ClassID);
            }
        });
        //选择全部学生
        $('.UserAll').live('click',function(){
            if($(this).attr('checked')=='checked'){
                $('.UserID').each(function(){
                    $(this).attr('checked','checked');
                });
            }else{
                $('.UserID').each(function(){
                    $(this).attr('checked',false);
                });
            }
        });
        //选择单个学生
        $('.UserID').live('click',function(){
            var flag=0;
            if($(this).attr('checked')!='checked'){
                $('.UserAll').attr('checked',false);
            }else{
                $('.UserID').each(function(){
                    if($(this).attr('checked')!='checked'){
                        flag=1;
                    }
                });
                if(flag==0){
                    $('.UserAll').attr('checked','checked');
                }
            }
        });
        //清除work_UserID下的对应班级数据
        function clearClass(ClassID){
            var UserIDList=$('.work_UserID').val();
            //|11;11,11,11,1||
            if(('|'+UserIDList+'|').indexOf('|'+ClassID+';')!=-1){
                var reg=new RegExp("\\|"+ClassID+";[^\\|]*\\|","i");
                var tmp_str=('|'+UserIDList+'|').replace(reg,'|');
                if(tmp_str=='|'){
                    $('.work_UserID').val('');
                }else{
                    tmp_str=tmp_str.substr(1,tmp_str.length-2);
                    $('.work_UserID').val(tmp_str);
                }
            }
        }
        //添加work_UserID下的对应班级数据
        function addClass(ClassID){
            clearClass(ClassID);
            var UserIDList=$('.work_UserID').val();
            var output='';
            if(UserIDList==''){
                output=ClassID+';0';
            }else{
                output=UserIDList+'|'+ClassID+';0';
            }
            $('.work_UserID').val(output);
        }
        //添加work_UserID下的对应用户数据
        function addClassUser(ClassID,UserID){
            var UserIDList=$('.work_UserID').val();
            //|11;11,11,11,1||
            var output='';
            if(('|'+UserIDList+'|').indexOf('|'+ClassID+';')!=-1){
                var tmp_arr=UserIDList.split('|');
                var tmp_arr_1,tmp_arr_2;
                var i=0;
                var j=0;
                for(i in tmp_arr){
                    tmp_arr_1=tmp_arr[i].split(';');
                    if(tmp_arr_1[0]==ClassID){
                        output+='|'+tmp_arr[i]+','+UserID;
                    }else{
                        output+='|'+tmp_arr[i];
                    }
                }
            }else{
                if(UserIDList=='') output=ClassID+';'+UserID;
                else output=UserIDList+'|'+ClassID+';'+UserID;
            }
            $('.work_UserID').val(output);
        }
        //切换作业方式
        $('.work_style').live('click',function(){
            resetWorkStyle();
            $(this).parent().addClass('workItemChecked');
        });
        $('.work_online,.work_down').live('click',function(){
            resetWorkStyle();
            $(this).find('input').attr('checked','checked');
            $(this).find('.iconfont').html('&#xe600;');
            $(this).addClass('workItemChecked');
        });
        function resetWorkStyle(){
            var $checkItem =$('.work_online,.work_down');
            $checkItem.find('.iconfont').html('&#xe601;');
            $checkItem.removeClass('workItemChecked');
        }
        //切换答题顺序
        $('.work_order').live('click',function(){
            resetWorkOrder();
            $(this).parent().addClass('workItemChecked');
        });
        $('.work_ordersame,.work_orderrand').live('click',function(){
            resetWorkOrder();
            $(this).find('input').attr('checked','checked');
            $(this).find('.iconfont').html('&#xe600;');
            $(this).addClass('workItemChecked');
        });
        function resetWorkOrder(){
            var $checkItem =$('.work_ordersame,.work_orderrand');
            $checkItem.find('.iconfont').html('&#xe601;');
            $checkItem.removeClass('workItemChecked');
        }
    }
}