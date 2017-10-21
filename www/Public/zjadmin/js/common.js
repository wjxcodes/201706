(function() {
    window.Cookie = (function() {
        return {
            Set: function(a, b, c) {
                var d = new Date();
                d.setTime(d.getTime() + c * 24 * 60 * 60 * 1000);
                document.cookie = a + "=" + encodeURIComponent(b) + ";expires=" + d.toGMTString() + ";path=/"
            },
            Get: function(a) {
                var c=document.cookie.split(a+'=');
                if(c.length>1){
                    var d=c[c.length-1].split(';');
                    if(d.length>1){
                        return decodeURIComponent(d[0])
                    }else{
                        return decodeURIComponent(c[c.length-1])
                    }
                }else{
                    return null
                }

                //var b = document.cookie.match(new RegExp("(^| )" + a + "=([^;]*)(;|$)"));
                //if(a=='paperstyle') alert(b.length);
                //if (b != null) {
                //    return decodeURIComponent(b[2])
                //} else {
                //    return null
                //}
            },
            Del: function(a) {
                var b = new Date();
                b.setTime(b.getTime() - 100000);
                var c = this.Get(a);
                if (c != null) {
                    document.cookie = a + "=;expires=" + b.toGMTString() + ";path=/"
                }
            },
            Has: function(name){
                var ck=document.cookie.indexOf(name+"=");
                if(ck==-1) {
                    return false;
                }
                return true;
            }
        }
    })()
})();
function changeUrl(url){
    var tmpArr=url.split('/');
    var len=tmpArr.length;
    switch(len){
        case 3:
            var output=new Array();
            for(var i=1;i<len-1;i++){
                output[i]=tmpArr[i];
            }
            return '/';
            break;
        case 4:
            var output=new Array();
            for(var i=1;i<len-2;i++){
                output[i]=tmpArr[i];
            }
            return output.join('/')+'/';
            break;
    }
    return url;
}
//js  U方法生成访问路径
function U(models,vars,suffix){
    if(typeof(suffix)!='boolean') suffix=true;
    if(typeof(vars)=='undefined') vars='';
    if(typeof(local)=='undefined') local='/';

    var depr='/';
    var lastName='.html';
    var leftstr=local;
    var wenhao='?'; //结尾数据

    //去掉开头的斜杠
    var tmp=models.substr(0,1);
    if(tmp==depr){
        models=models.substr(1,models.length);
        leftstr='/';
    }

    //处理了连接中的数据到数组
    var tmpVar,tmpStr1,tmpStr2,tmpStr3,tmpStr4;

    if(vars!='' && typeof(vars)=='object'){
        tmpVar=new Array();
        var j=0;
        for(var i in vars){
            tmpVar[j]=i+'='+vars[i];
            j++;
        }
        vars=tmpVar.join('&');
    }

    tmpStr1=models.split(depr);
    if(tmpStr1[tmpStr1.length-1].indexOf('?')!=-1){
        tmpStr2=tmpStr1[tmpStr1.length-1].split('?');
        tmpStr1[tmpStr1.length-1]=tmpStr2[0];
        if(vars!='') vars=tmpStr2[1]+'&'+vars;
        else vars=tmpStr2[1];
    }

    //处理vars
    if(vars!=''){
        tmpStr2=new Array();
        tmpStr3=vars.split('&');
        if(tmpStr3.length==1 && tmpStr3[0].indexOf('=')==-1){
            wenhao+=tmpStr3[0];
        }else{
            for(var i=0;i<tmpStr3.length;i++){
                tmpStr4=tmpStr3[i].split('=');
                tmpStr2[i*2]=tmpStr4[0];
                tmpStr2[i*2+1]=tmpStr4[1];
            }
        }
    }

    //合并数组tmpstr1 tmpstr2
    var myUrl='';
    if(tmpStr1){
        if(leftstr.indexOf('Index')!=-1 && tmpStr1[0]!='Index' || tmpStr1.length==3){
            leftstr='/';
        }

        leftstr+=tmpStr1.join(depr);
        if(vars!='') leftstr+=depr+tmpStr2.join(depr);
        myUrl=leftstr;
    }
    if(myUrl.indexOf('.html')!=-1) myUrl=myUrl.replace('.html','');

    //添加后缀
    if(suffix==true){
        if(myUrl.substr(-1,1)!='/' && myUrl.indexOf(lastName)==-1){
            myUrl+=lastName;
        }
    }

    if(wenhao!='?'){
        myUrl+=wenhao;
    }

    return myUrl;
}
function toHex( n ) {
    var result = ''
    var start = true;
    for ( var i=32; i>0; ) {
        i -= 4;
        var digit = ( n >> i ) & 0xf;

        if (!start || digit != 0) {
            start = false;
            result += digitArray[digit];
        }
    }
    return ( result == '' ? '0' : result );
}
function checkNowUrl(){
    if(window.location.href.indexOf(U('Public/login'))==-1){
        window.location.href=U('Public/login');
    }
}
function login_onclick(){
    var DevicePath,ret,n,mylen;
    try{
        //建立操作我们的锁的控件对象，用于操作我们的锁
        var s_simnew31;
        //创建插件或控件
        if(navigator.userAgent.indexOf("MSIE")>0 && !navigator.userAgent.indexOf("opera") > -1){
            s_simnew31=new ActiveXObject("Syunew3A.s_simnew3");
        }else{
            s_simnew31= document.getElementById('s_simnew31');
        }

        //查找是否存在锁,这里使用了FindPort函数
        DevicePath = s_simnew31.FindPort(0);
        if( s_simnew31.LastError!= 0 ){
            window.alert ( "没有发现加密锁 ,请检查是否插入 .");
            checkNowUrl();
            return ;
        }

        //'读取锁的ID
        if($('#KeyID').length==1) $('#KeyID').val(toHex(s_simnew31.GetID_1(DevicePath))+toHex(s_simnew31.GetID_2(DevicePath)));
        if( s_simnew31.LastError!= 0 ){
            window.alert( "获取加密狗数据失败,错误代码是:"+s_simnew31.LastError.toString());
            checkNowUrl();
            return ;
        }

        //获取设置在锁中的用户名
        //先从地址0读取字符串的长度,使用默认的读密码"FFFFFFFF","FFFFFFFF"
        if($('#KeyValue').length==1) $('#KeyValue').val(s_simnew31.YReadString(0,12, "C2FA91E3", "F3BE2368", DevicePath));
        if( s_simnew31.LastError!= 0 ){
            window.alert(  "获取加密狗数据失败,错误代码是:"+s_simnew31.LastError.toString());
            checkNowUrl();
            return ;
        }

        //这里返回对随机数的HASH结果
        $('#EncData').val(s_simnew31.EncString($('#KeyRnd').val(),DevicePath));
        if( s_simnew31.LastError!= 0 ){
            window.alert( "获取加密狗数据失败,错误代码是:"+s_simnew31.LastError.toString());
            checkNowUrl();
            return ;
        }
    }catch (e){
        alert(e.name + ": " + e.message+"。可能是没有安装相应的控件或插件");
        checkNowUrl();
    }
}
function loginCheck(){
    var DevicePath,ret,n,mylen;
    try{
        //建立操作我们的锁的控件对象，用于操作我们的锁
        var s_simnew31;
        //创建插件或控件
        if(navigator.userAgent.indexOf("MSIE")>0 && !navigator.userAgent.indexOf("opera") > -1){
            s_simnew31=new ActiveXObject("Syunew3A.s_simnew3");
        }else{
            s_simnew31= document.getElementById('s_simnew31');
        }

        //查找是否存在锁,这里使用了FindPort函数
        DevicePath = s_simnew31.FindPort(0);
        if( s_simnew31.LastError!= 0 ){
            window.alert ( "没有发现加密锁 ,请检查是否插入 .");
            checkNowUrl();
            return ;
        }

        //'读取锁的ID
        var keyID=Cookie.Get("keyID");
        var rnd=Cookie.Get("rnd");
        var encData=Cookie.Get("encData");
        if(keyID!=(toHex(s_simnew31.GetID_1(DevicePath))+toHex(s_simnew31.GetID_2(DevicePath)))){
            window.alert ( "加密锁不匹配，请检查加密锁.");
            checkNowUrl();
            return ;
        }
        if( s_simnew31.LastError!= 0 ){
            window.alert( "获取加密狗数据失败,错误代码是:"+s_simnew31.LastError.toString());
            checkNowUrl();
            return ;
        }

        //这里返回对随机数的HASH结果
        if(encData!=s_simnew31.EncString(rnd,DevicePath)){
            window.alert ( "加密锁验证失败，请检查加密锁.");
            checkNowUrl();
            return ;
        }
        if( s_simnew31.LastError!= 0 ){
            window.alert( "获取加密狗数据失败,错误代码是:"+s_simnew31.LastError.toString());
            checkNowUrl();
            return ;
        }
    }catch (e){
        alert(e.name + ": " + e.message+"。可能是没有安装相应的控件或插件");
        checkNowUrl();
    }
}
$(document).ready(function(){
    if($('#EncData').length==0 && softdog==1){
        loginCheck();
    }
    //树形菜单
    if($('.lists').length>0){
        $('.lists').each(function(){
            $(this).click(function(){
                if ($(this).attr('jl').indexOf('down')!=-1){
                    $(this).attr('jl','');
                    $(this).css('background-color','#FFF');
                    if($(this).find('input').length>0){
                        $(this).find('input').first().attr('checked',false);
                    }
                }else {
                    $(this).attr('jl','down');
                    $(this).css('background-color','#CF9'); 
                    if($(this).find('input').length>0){
                        $(this).find('input').first().attr('checked',true);
                    }
                }
            });
            $(this).mouseover(function(){
                $(this).css('background-color','#CFC');
            });
            $(this).mouseout(function(){
                if ($(this).attr('jl').indexOf('down')==-1){
                    $(this).css('background-color','#FFF');
                }else{
                    $(this).css('background-color','#CF9'); 
                }
            });
        });
    }
    //提交表单 
    if($('.mysubmit').length>0){
        $('.mysubmit').click(function(){
            var mytag = $(this).attr('tag');
            var myurl = $(this).attr('u');
            var x=1;
            $('#'+mytag).find('input').each(function(){
                if($(this).attr('check')=='Require'){
                    if($(this).val()==''){
                        alert($(this).attr('warning'));
                        $(this).focus();
                        x=0;
                        return false;
                    }
                }else if($(this).attr('check')=='radio'){
                    var thisname=$(this).attr('name');
                    var thischeck='';
                    $('input[name="'+thisname+'"]').each(function(){
                        if($(this).attr('checked')=='checked'){
                            thischeck=1;
                        }
                    });
                    if(!thischeck){
                        x=0;
                        alert($(this).attr('warning'));
                        return false;
                    }
                }else if($(this).attr('check')=='email'){
                    var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
                    if(!reg.test($(this).val())){
                        x=0;
                        alert($(this).attr('warning'));
                         return false;
                     }
                }
            });
            
            $('#'+mytag).find('textarea').each(function(){
                if($(this).attr('check')=='Require'){
                    if($(this).val()==''){
                        alert($(this).attr('warning'));
                        $(this).focus();
                        x=0;
                        return false;
                    }
                }
            });
            $('#'+mytag).find('select').each(function(){
                if($(this).attr('check')=='Require'){
                    if($(this).val()==''){
                        alert($(this).attr('warning'));
                        $(this).focus();
                        x=0;
                        return false;
                    }
                }
            });
            if(x){
                $('#'+mytag).attr('action',myurl);
                $('#'+mytag).submit();
            }
        });
    }
     //添加
    if($('.btadd').length>0){
        $('.btadd').click(function(){
            location.href  = U(URL+"/add");
        });
    }
    //编辑
    if($('.btedit').length>0){
        $('.btedit').each(function(){
            $(this).click(function(){
                var keyValue = $(this).attr('thisid');
                if(!keyValue){
                    keyValue = getSelectCheckboxValue();
                }
                if(!keyValue){
                    alert('请选择编辑项！');
                    return false;
                }
                location.href =  U(URL+"/edit?id="+keyValue);
            });
        });
    }
    //入库
    if($('.btintro').length>0){
        $('.btintro').click(function(){
            var keyValue = $(this).attr('thisid');
            if(!keyValue){
                keyValue = getSelectCheckboxValues();
            }
            if(!keyValue){
                alert('请选择入库项！');
                return false;
            }
            jInfo('入库中请稍候。。。','加载数据');
            var str="<form id='btgetform' action='"+ U(URL+'/intro') + "' method='post'><input name='id' type='hidden' value='"+keyValue+"'/></form><script>document.getElementById('btgetform').submit();</script>";
            $('body').append(str);
        });
    }
    //出库做修改
    if($('.btout').length>0){
        $('.btout').click(function(){
            var keyValue = $(this).attr('thisid');
            if(!keyValue){
                keyValue = getSelectCheckboxValues();
            }
            if(!keyValue){
                alert('请选择修改项！');
                return false;
            }
            if (window.confirm('确实要修改选择项吗？')){
                jInfo('移出中请稍候。。。','加载数据');
                var str="<form id='btgetform' action='"+ U(URL+'/out') + "' method='post'><input name='id' type='hidden' value='"+keyValue+"'/></form><script>document.getElementById('btgetform').submit();</script>";
                $('body').append(str);
            }
        });
    }
    //出库
    if($('.btoutAndDelete').length>0){
        $('.btoutAndDelete').click(function(){
            var keyValue = $(this).attr('thisid');
            if(!keyValue){
                keyValue = getSelectCheckboxValues();
            }
            if(!keyValue){
                alert('请选择移除项！');
                return false;
            }
            if (window.confirm('确实要移除选择项吗？')){
                jInfo('移除中请稍候。。。','加载数据');
                var str="<form id='btgetform' action='"+ U(URL+'/outAndDelete') + "' method='post'><input name='id' type='hidden' value='"+keyValue+"'/></form><script>document.getElementById('btgetform').submit();</script>";
                $('body').append(str);
            }
        });
    }
    //删除
    if($('.btdelete').length>0){
        $('.btdelete').click(function(){
            var keyValue = $(this).attr('thisid');
            if(!keyValue){
                keyValue = getSelectCheckboxValues();
            }
            if(!keyValue){
                alert('请选择删除项！');
                return false;
            }
            if (window.confirm('确实要删除选择项吗？')){
                jInfo('删除中请稍候。。。','加载数据');
                var str="<form id='btgetform' action='"+ U(URL+'/delete') + "'  method='post'><input name='id' type='hidden' value='"+keyValue+"'/></form><script>document.getElementById('btgetform').submit();</script>";
                $('body').append(str);
            }
        });
    }
    //批量编辑
    if($('.bteditArr').length>0){
        $('.bteditArr').click(function(){
            var keyValue = $(this).attr('thisid');
            var groupID = $('#groupID').val();
            if(!keyValue){
                keyValue = getSelectCheckboxValues();
            }
            if(!keyValue){
                alert('请选择编辑项！');
                return false;
            }
            var str="<form id='btgetform' action='"+ U(URL+'/editArr') + "' method='post'><input name='id' type='hidden' value='"+keyValue+"'/><input name='groupID' type='hidden' value='"+groupID+"'/></form><script>document.getElementById('btgetform').submit();</script>";
            $('body').append(str);
        });
    }
    //删除文档
    if($('.btdel').length>0){
        $('.btdel').click(function(){
            var keyValue = $('.btdel').attr('thisid');
            if(!keyValue){
                keyValue = getSelectCheckboxValues();
            }
            if(!keyValue){
                alert('请选择删除项！');
                return false;
            }
            if (window.confirm('确实要删除选择项吗？')){
                jInfo('删除中请稍候。。。','加载数据');
                var str="<form id='btgetform' action='"+ U(URL+'/del') + "' method='post'><input name='id' type='hidden' value='"+keyValue+"'/></form><script>document.getElementById('btgetform').submit();</script>";
                $('body').append(str);
            }
        });
    }
    //导出
    if($('.btexport').length>0){
        $('.btexport').click(function(){
            if (window.confirm('确实要导出当前所有数据吗？')){
                $('#form1').attr('target','_blank');
                var data=$('#form1').serialize();
                var thisUrl=$('#form1').attr('action');
                //处理同一个控制器下，不同数据导出
                var urlObj=new Array();
                urlObj=thisUrl.split('/');
                if(urlObj.length>3){
                    thisUrl='/'+urlObj[1]+'/'+urlObj[2];
                }
                var url= U(thisUrl+'/export');
                $.post(url,data,function(msg){
                   // 权限验证
                    if(checkPower(msg)=='error'){
                        return false;
                    }
                    var result=msg['data'];
                    if(result['ifPage']==1){
                        jInfo('加载中请稍候。。。','加载数据');
                        if(result['data']){
                            jClose();
                            jFrame(result['data'],'可下载列表');
                        }
                    }else{
                        $('#form1').attr('action',thisUrl+'/export?p=all');
                        $('#form1').submit();
                        if($('#form1').attr('action').indexOf('/export?p=all')!=-1){
                            var newUrl=$('#form1').attr('action').replace('/export?p=all','');
                            $('#form1').attr('action',newUrl);
                        }else{
                            $('#form1').attr('action',thisUrl);
                        }

                    }
                })
            }
        });
    }
    //手动提取试题
    if($('.btdr').length>0){
        $('.btdr').click(function(){
            var keyValue  = getSelectCheckboxValues();
                if(!keyValue){
                    alert('请选择提取项！');
                    return false;
                }
                if (window.confirm('确实要提取吗？如果已提取过则覆盖原有数据')){
                    $('#form1').attr('action',U(URL+"/testsave"));
                    $('#form1').submit();
                }
        });
    }
    
    //手动批量提取
    if($('.btdrall').length>0){
        $('.btdrall').click(function(){
            var keyValue = $(this).attr('thisid');
            if(!keyValue){
                keyValue = getSelectCheckboxValues();
            }
            if(!keyValue){
                alert('请选择提取项！');
                return false;
            }
            if (window.confirm('确实要提取选择项吗？')){
                jInfo('提取中请稍候。。。','加载数据');
                var str="<form id='btgetform' action='"+ U(URL+'/testsave') + "' method='post'><input name='id' type='hidden' value='"+keyValue+"'/></form><script>document.getElementById('btgetform').submit();</script>";
                $('body').append(str);
            }
        });
    }
    //自动提取 获取选择行的id列表 
    if($('.btget').length>0){
        $('.btget').each(function(){
            $(this).click(function(){
                var keyValue = $(this).attr('thisid');
                if(!keyValue){
                    keyValue = getSelectCheckboxValues();
                }
                if(!keyValue){
                    alert('请选择提取项！');
                    return false;
                }
                jInfo('提取中请稍候。。。','加载数据');
                var str="<form id='btgetform' action='"+ U(URL+'/getDocData') + "' method='post'><input name='id' type='hidden' value='"+keyValue+"'/></form><script>document.getElementById('btgetform').submit();</script>";
                $('body').append(str);
            });
        });
    }
    //刷新
    if($('.btflush').length>0){
        $('.btflush').click(function(){
            location.reload();
        });
    }
    //排序
    if($('.btsort').length>0){
        $('.btsort').click(function(){
            location.href = U(URL+"/sort");
        });
    }
    //选中全部checkbox
    if($('#CheckAll').length>0){
        $('#CheckAll').click(function(){
            var tag = $(this).attr('tag');
            $('#'+tag).find('.key').each(function(){
                if($('#CheckAll').attr('checked')=='checked') $(this).attr('checked',true);
                else $(this).attr('checked',false);
            });
        });
    }
    
    //权限添加反选
    if($('#AllPower').length>0){
        $('#AllPower').click(function(){
            $('.power').each(function(){
                if($('#AllPower').attr('checked')=='checked'){
                    $(this).attr('checked',true);
                }else{
                    $(this).attr('checked',false);
                }
            })
        })
    }
    
    //显示高级搜索
    if($('#showText').length>0){
        $('#showText').click(function(){
            if($('#searchM').css('display')=='block'){
                $('#searchM').css({'display':'none'});
                $('#showText').val('高级');
                $('#key').css({'display':'block'});
            }else{
                $('#searchM').css({'display':'block'});
                $('#showText').val('隐藏');
                $('#name').val('');
                $('#key').css({'display':'none'});
            }
        });
    }

    //ajax翻页
    $('.ajaxPage a').live('click',function(){
        var url=$(this).attr('data');
        if(url=='' || typeof(url)=='undefined'){
            return false;
        }
        $('#popup_message').html('数据加载中。。。');
        $.get(url,function(data){
            //权限验证
            if(checkPower(data)=='error'){
                $('#popup_message').html('数据加载失败！');
                return false;
            }
            $('#popup_message').html(data['data']);
        });
    });
    // if($('.inputDate').length>0){
    //     //加载js文件
    //     var urls = new Array();
    //     urls.push("Public/plugin/calendar/calendar.js");
    //     load_script(urls);
    //     var srcs = new Array();
    //     srcs.push("Public/plugin/calendar/calendar.css");
    //     load_css(srcs);
    //     $('.inputDate').bind('mouseenter',function(){
    //         jQuery.extend(DateInput.DEFAULT_OPTS, {
    //             month_names: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
    //             short_month_names: ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二"],
    //             short_day_names: ["日", "一", "二", "三", "四", "五", "六"],
    //             dateToString: function(date) {
    //                 var month = (date.getMonth() + 1).toString();
    //                 var dom = date.getDate().toString();
    //                 if (month.length == 1) month = "0" + month;
    //                 if (dom.length == 1) dom = "0" + dom;
    //                 return date.getFullYear() + "-" + month + "-" + dom;
    //             }
    //         });
    //         $('.inputDate').each(function(){
    //             $(this).date_input();
    //         });
    //         $('.inputDate').unbind('mouseenter');
    //     });
    // }


    //载入日期控件

    if($(".inputDate").length>0 || $(".inputTime").length>0){
        // 加载js文件
        var urls = new Array();
        urls.push("Public/plugin/laydate/laydate.js");
        load_script(urls);
    }
    //隐藏载入提示
    $('#loader').hide();
});
//获取checkbox选择项 返回数据1,数据2,数据3
function getSelectCheckboxValues(){
    var result='';
    $('.key').each(function(){
        if($(this).attr('checked')=='checked'){
            result += $(this).val()+",";
        }
    });
    return result.substring(0, result.length-1);
}

//获取checkbox选择项的第一个
function getSelectCheckboxValue(){
    var thisvalue='';
    $(".key").each(function(){
        if($(this).attr('checked')){
            thisvalue = $(this).val();
        }
    });
    if(thisvalue) return thisvalue;
    else return false;
}
/**
* 权限验证提示
* @author demo
* @date 2014年11月20日
*/
function checkPower(data){
    if(typeof(data.status)=='undefined' &&  (typeof data=='string') && data.constructor==String){
        alert(data);
        return 'error';
    }
    if(typeof(data.status)!='undefined' && data.status!=1){
        var msg='';
        var url='';
        if(typeof(data['data']['url'])=='undefined' || data['data']['url']==''){
            msg=data['data'];
        }else{
            msg=data['data']['data'];
            url=data['data']['url'];
        }
        alert(msg);
        if(url){
            location.href=url;
        }
        return 'error';
    }
}

//设置为参数形式，便于复用  2015-7-31
var setupOptions = {
    cache:false,
    timeout:29000,//29秒超时
    error:function(XMLHttpRequest, textStatus, errorThrown){
        var errstr='';
        var status=XMLHttpRequest.status;
        if(status==0 && textStatus=='error') return false;
        switch (status){
            case(500):
                errstr="服务器系统内部错误";
                break;
            case(401):
                errstr="访问被拒绝";
                break;
            case(403):
                errstr="无权限执行此操作";
                break;
            case(404):
                errstr="未找到请求地址";
                break;
            case(408):
                errstr="请求超时";
                break;
            default:
                errstr="网络错误:"+status;
        }
        var second = 5;
        var errorStr = errstr+'<p>当前页面会在<span class="spanSeconds">'+second+'</span>秒后自动刷新...</p>';
        jInfo(errorStr,'网络问题');
        var error = setInterval(function () {
            second--;
            if (second <= 0) {
                location.reload();
                clearInterval(error);
            }
            $('.spanSeconds').html(second);
        }, 1000);
    }
}
//初始化ajax全局信息
$.ajaxSetup(setupOptions);

/**
 * 构造option 选项
 * @param data   array  对应json数据
 * @param id     int    选中项ID
 * @param biaoji string 识别对应数据
 * @return string
 * @author demo
 */
function setOption(data,id,biaoji){
        var str='';
        var bjone='';
        var bjtwo='';
        var i,j;
        
        if(data!=null){
            switch(biaoji){
                case 'special':
                    str='<option value="">-请选择-</option>';
                    for(i in data){
                        str+='<option value="'+(data[i]['sub'] ? '' : data[i]['SpecialID'])+'"'
                        +(data[i]['SpecialID']==id ? "selected=\"selected\"" : "") +'>'+(data[i]==data[data[i].length-1] ? '┕' : '┝')+''+data[i]['SpecialName']+'';
                        if(data[i]!=data[data.length-1]){
                            bjone='┃';
                        }else{
                            bjonw=' ';
                        }
                        if(data[i]['sub']){
                            for(j in data[i]['sub']){
                                if(data[i]['sub'][j]==data[i]['sub'][data.length-1]){
                                    bjtwo='┕';
                                }else{
                                    bjtwo='┝';
                                }
                            str+='<option value="'+data[i]['sub'][j]['SpecialID']+'" '+(data[i]['sub'][j]['SpecialID']==id ? "selected=\"selected\"" : "")+'>'+bjone+bjtwo+data[i]['sub'][j]['SpecialName']+'</option>';
                            }
                        }
                    }
                    break;
                case 'types':
                    str='<option value="">-请选择-</option>';
                    for(i=0;i<data.length;i++){
                        str+='<option value="'+data[i]['TypesID']+'" '+(data[i]['TypesID']==id ? "selected=\"selected\"" : "") +' typesstyle='+data[i]['TypesStyle']+'>'+data[i]['TypesName']+'</option>';
                    }
                    break;
                case 'chapter':
                    str='<option value="">-请选择-</option>';
                    for(i in data){
                        if(data[i]['Last']==1)
                            str+='<option value="c'+data[i]['ChapterID']+'" last="1" '+(data[i]['ChapterID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['ChapterName']+'</option>';
                        else
                            str+='<option value="'+data[i]['ChapterID']+'" '+(data[i]['ChapterID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['ChapterName']+'</option>';
                        if(data[i]['sub'])
                        for(j=0;j<data[i]['sub'].length;j++){
                            if(data[i]['Last']==1)
                            str+='<option value="c'+data[i]['sub'][j]['ChapterID']+'" last="1" '+(data[i]['sub'][j]['ChapterID']==id ? "selected=\"selected\"" : "") +'>　　'+data[i]['sub'][j]['ChapterName']+'</option>';
                            else
                            str+='<option value="'+data[i]['sub'][j]['ChapterID']+'" '+(data[i]['sub'][j]['ChapterID']==id ? "selected=\"selected\"" : "") +'>　　'+data[i]['sub'][j]['ChapterName']+'</option>';
                        }
                    }
                    break;
                case 'knowledge':
                    str='<option value="">-请选择-</option>';
                    for(i in data){
                        if(data[i]['Last']==1){
                            str+='<option last="1" value="t'+data[i]['KlID']+'" '+(data[i]['KlID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['KlName']+'</option>';
                        }else {
                            str+='<option last="0" value="'+data[i]['KlID']+'" '+(data[i]['KlID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['KlName']+'</option>';
                        }
                        if(data[i]['sub'])
                        for(j=0;j<data[i]['sub'].length;j++){
                            if(data[i]['Last']==1)
                               str+='<option last="1" value="t'+data[i]['sub'][j]['KlID']+'" '+(data[i]['sub'][j]['KlID']==id ? "selected=\"selected\"" : "") +'>　　'+data[i]['sub'][j]['KlName']+'</option>';
                            else
                               str+='<option last="0" value="'+data[i][j]['KlID']+'" '+(data[i][j]['KlID']==id ? "selected=\"selected\"" : "") +'>　　'+data[i][j]['KlName']+'</option>';                        
                        }
                    }
                    break;
                case 'skill':
                    str='<option value="">-请选择-</option>';
                    for(i in data){
                        if(data[i]['Last']==1){
                            str+='<option last="1" value="t'+data[i]['SkillID']+'" '+(data[i]['SkillID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['SkillName']+'</option>';
                        }else {
                            str+='<option last="0" value="'+data[i]['SkillID']+'" '+(data[i]['SkillID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['SkillName']+'</option>';
                        }
                        if(data[i]['sub'])
                        for(j=0;j<data[i]['sub'].length;j++){
                            if(data[i]['Last']==1)
                               str+='<option last="1" value="t'+data[i]['sub'][j]['SkillID']+'" '+(data[i]['sub'][j]['SkillID']==id ? "selected=\"selected\"" : "") +'>　　'+data[i]['sub'][j]['SkillName']+'</option>';
                            else
                               str+='<option last="0" value="'+data[i][j]['SkillID']+'" '+(data[i][j]['SkillID']==id ? "selected=\"selected\"" : "") +'>　　'+data[i][j]['SkillName']+'</option>';                        
                        }
                    }
                    break;
                case 'capacity':
                    str='<option value="">-请选择-</option>';
                    for(i in data){
                        if(data[i]['Last']==1){
                            str+='<option last="1" value="t'+data[i]['CapacityID']+'" '+(data[i]['CapacityID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['CapacityName']+'</option>';
                        }else {
                            str+='<option last="0" value="'+data[i]['CapacityID']+'" '+(data[i]['CapacityID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['CapacityName']+'</option>';
                        }
                        if(data[i]['sub'])
                        for(j=0;j<data[i]['sub'].length;j++){
                            if(data[i]['Last']==1)
                               str+='<option last="1" value="t'+data[i]['sub'][j]['CapacityID']+'" '+(data[i]['sub'][j]['CapacityID']==id ? "selected=\"selected\"" : "") +'>　　'+data[i]['sub'][j]['CapacityName']+'</option>';
                            else
                               str+='<option last="0" value="'+data[i][j]['CapacityID']+'" '+(data[i][j]['CapacityID']==id ? "selected=\"selected\"" : "") +'>　　'+data[i][j]['CapacityName']+'</option>';                        
                        }
                    }
                    break;
                case 'ability':
                    str='<option value="">-请选择-</option>';
                    for(var i in data){
                        str+='<option value="'+data[i]['AbID']+'">'+data[i]['AbilitName']+'</option>';
                    }
                    break;
                case 'grade':
                    str='<option value="">-请选择-</option>';
                    for(var i in data){
                        str+='<option value="'+data[i]['GradeID']+'">'+data[i]['GradeName']+'</option>';
                    }
                    break;
                case 'area':
                    str='<option value="">-请选择-</option>';
                    for(var i in data){
                        str+='<option value="'+data[i]['AreaID']+'" last="'+data[i]['Last']+'">'+data[i]['AreaName']+'</option>';
                    }
                    break;
                case 'skillList':
                    for(var i in data){
                        str+='<div>'+data[i]['SkillName']+' <span class="delhang">x</span><input class="skill" name="skill[]" type="hidden" value="'+data[i]['SkillID']+'"/></div>';
                    }
                    break;
                case 'capacityList':
                    for(var i in data){
                        str+='<div>'+data[i]['CapacityName']+' <span class="delhang">x</span><input class="capacity" name="capacity[]" type="hidden" value="'+data[i]['CapacityID']+'"/></div>';
                    }
                    break;
                case 'knowledgeList':
                    for(var i in data){
                        str+='<div>'+data[i]['KlName']+' <span class="delhang">x</span><input class="kl" name="kl[]" type="hidden" value="'+data[i]['KlID']+'"/></div>';
                    }
                    break;
                case 'chapterList':
                    for(var i in data){
                        str+='<div>'+data[i]['ChapterName']+' <span class="delhang">x</span><input class="cp" name="cp[]" type="hidden" value="'+data[i]['ChapterID']+'"/></div>';
                    }
                    break;
                case 'caseMenu':
                    str='<option value="">-请选择-</option>';
                    for(var i in data){
                        str+='<option value="'+data[i]['MenuID']+'">'+data[i]['MenuName']+'</option>';
                    }
                    break;
                case 'subject':
                    str='<option value="">-请选择-</option>';
                    for(var i in data){
                        str+='<option value="'+data[i]['SubjectID']+'">'+data[i]['OldSubjectName']+'</option>';
                    }
                    break;
                default:
                    str+='<option value="">无数据！</option>';
            }
            return str;
        }
        return '<option value="">请添加该属性！</option>';
}

//页面事件汇总
$.fn.extend({
    /**
     * 选中input checkbox数据
     * @param string str 需要选中的id以逗号间隔
     */
    inputCheck:function(str){
        str=','+str+',';
        this.each(function(){
            var inputVal=$(this).val();
            if(inputVal=='') return;

            if(str.indexOf(','+inputVal+',')!=-1){
                $(this).attr('checked','checked');
            }

            $(this).parent().removeClass('sfred');
            if($(this).attr('checked')=='checked'){
                $(this).parent().addClass('sfred');
            }
            $(this).click(function(){
                if($(this).attr('checked')=='checked'){
                    $(this).parent().addClass('sfred');
                }else{
                    $(this).parent().removeClass('sfred');
                }
            });
        });
    },
    /**
     * ajax获取select数据 适用于单一联动
     * @param string url 调用数据路径
     */
    ajaxGetSelectData:function(url,post,modelName,id){
        var _this=$(this);
        _this.html('<option value="">加载中。。。</option>');
        $.post(url,post,function(data){
            //权限验证
            if(checkPower(data)=='error'){
                _this.html('<option value="">加载失败</option>');
                return false;
            }
            var chapterstr= setOption(data['data'],id,modelName);
            _this.html('<option value="">请选择</option>'+chapterstr);
        });
    },
    /**
     * 学科列表change事件
     * @param string url 调用数据路径
     * @param array param 调用数据类型
     */
    gradeSelectChange:function(url,params){
        $(this).live('change',function(){
            var gradeID=$(this).val();
            if(typeof(gradeID)=='undefined' || gradeID=='') return false;
//            if(params['ifConfirm']==1){
//                var msg=confirm('将清空与当前学科关联信息！');
//                if(!msg) return false;
//                $('#chapterList').html('');  //清空已有关联章节
//                $('#knowledgeList').html(''); //清空已有关联知识点
//            }
//            if(params['search']=='search'){
//                $('#searchchapter').nextAll('select').remove();
//                $('#searchknowledge').nextAll('select').remove();
//            }else{
//                $('#chapter').nextAll('select').remove();
//                $('#knowledge').nextAll('select').remove();
//            }
            $.post(U(url+'/getData'),{'style':params['style'],'gradeID':gradeID,'list':params['list']},function(data){
                if(checkPower(data)=='error'){
                    return false;
                }
                var output='';
                for(var i in data['data']){
                    output=setOption(data['data'][i],0,i);
                    if(params['search']=='search'){
                        $('#search'+i).html(output);
                    }else{
                        $('#'+i).html(output);
                    }
                }
            });
        });
    },
    /**
     * 学科列表change事件
     * @param string url 调用数据路径
     * @param array param 调用数据类型
     */
    subjectSelectChange:function(url,params){
        $(this).live('change',function(){
            var subjectID=$(this).val();
            if(params['ifConfirm']==1){
                var msg=confirm('将清空与当前学科关联信息！');
                if(!msg) return false;
                $('#chapterList').html('');  //清空已有关联章节
                $('#knowledgeList').html(''); //清空已有关联知识点
            }
            if(params['search']=='search'){
                $('#searchchapter').nextAll('select').remove();
                $('#searchknowledge').nextAll('select').remove();
            }else{
                $('#chapter').nextAll('select').remove();
                $('#knowledge').nextAll('select').remove();
            }
            $.post(U(url+'/getData'),{'style':params['style'],'subjectID':subjectID,'list':params['list']},function(data){
                if(checkPower(data)=='error'){
                    return false;
                }
                var output='';
                for(var i in data['data']){
                    output=setOption(data['data'][i],0,i);
                    if(params['search']=='search'){
                        $('#search'+i).html(output);
                    }else{
                        $('#'+i).html(output);
                    }
                }
            });
        });
    },
    /**
     * 章节change事件
     * @param string url 调取数据路径
     */
    chapterSelectChange:function(url){
        $(this).live('change',function(){
            var _this=$(this);
            if(_this.find('option:selected').attr('last')==1){
                return ;
            }
            _this.nextAll("select").remove();
            var values=_this.find('option:selected').val();
            if(values=='') return;
            var param={"style":"chapter","pID":values};
            $.post(U(url+"/getData"),param,function(msg){
                //权限验证
                if(checkPower(msg)=='error'){
                    return false;
                }
                var data=msg['data'];
                if(data){
                    var output='';
                    output+=setOption(data,0,'chapter');
                    _this.after('<select class="selectChapter" name="chapterID[]" >'+output+'</select>');
                }
            });
        });
    },
    /**
     * 知识点change事件
     * $param string url 调取数据路径
     * author  
     */
    knowledgeSelectChange:function(url){
        $(this).live('change',function(){
            var _this=$(this);
            if(_this.find('option:selected').attr('last')==1){
                return ;
            }
            _this.nextAll("select").remove();
            var values=_this.find('option:selected').val();
            if(values=='') return;
            var param={'style':'knowledge','pID':values};
            $.post((url+'/getData'),param,function(msg){
                //权限验证
                if(checkPower(msg)=='error'){
                    return false;
                }
                var data=msg['data'];
                if(data){
                    var output='';
                    output+=setOption(data,0,'knowledge');
                    _this.after('<select class="selectKnowledge" name="KlID[]">'+output+'</select>');
                }
            })
        })
    },
    /**
     * 能力change事件
     * $param string url 调取数据路径
     * author  
     */
    capacitySelectChange:function(url){
        $(this).live('change',function(){
            var _this=$(this);
            if(_this.find('option:selected').attr('last')==1){
                return ;
            }
            _this.nextAll("select").remove();
            var values=_this.find('option:selected').val();
            if(values=='') return;
            var param={'style':'capacity','pID':values};
            $.post((url+'/getData'),param,function(msg){
                //权限验证
                if(checkPower(msg)=='error'){
                    return false;
                }
                var data=msg['data'];
                if(data){
                    var output='';
                    output+=setOption(data,0,'capacity');
                    _this.after('<select class="selectCapacity" name="CapacityID[]">'+output+'</select>');
                }
            })
        })
    },
    /**
     * 技能change事件
     * $param string url 调取数据路径
     * author  
     */
    skillSelectChange:function(url){
        $(this).live('change',function(){
            var _this=$(this);
            if(_this.find('option:selected').attr('last')==1){
                return ;
            }
            _this.nextAll("select").remove();
            var values=_this.find('option:selected').val();
            if(values=='') return;
            var param={'style':'skill','pID':values};
            $.post((url+'/getData'),param,function(msg){
                //权限验证
                if(checkPower(msg)=='error'){
                    return false;
                }
                var data=msg['data'];
                if(data){
                    var output='';
                    output+=setOption(data,0,'skill');
                    _this.after('<select class="selectSkill" name="SkillID[]">'+output+'</select>');
                }
            })
        })
    },
    /**
     * 地区列表change事件
     * @param string url 调用数据路径
     * @param bool school 是否查学校
     */
    areaSelectChange:function(url,school){
        $(this).live('change',function(){
            var _this=$(this);
            if(!school || typeof(school)=='undefined'){
                school=0;
            }

            if(_this.find('option:selected').attr('last')==1 && school==0){
                return false;
            }
            if(_this.find('option:selected').attr('last')==1 && school==1){
                var values=_this.find('option:selected').val();
                if(values=='') return;
                $.post(U(url+'/getData'),{"style":"areaToSchool",'areaID':values,'times':Math.random()},function(msg){
                    // 权限验证
                    if(checkPower(msg)=='error'){
                        return false;
                    }
                    var data=msg['data'];
                    var tmp_arr='<option value="">-请选择-</value>';
                    if(data[0]=='school'){
                        if(data[1]!=""){
                            for(var i in data[1]){
                                tmp_arr+='<option value='+data[1][i]['AreaID']+'>'+data[1][i]['AreaName']+'</option>';
                            }
                            $("#school").show();
                            $('#schooladd').hide();
                            $("#school").html(tmp_arr);
                        }else{
                            $('#schooladd').show();
                            $('#school').hide();
                        }
                    }
                });
                return false;
            }
            _this.nextAll("select").remove();
            var values=_this.find('option:selected').val();
            if(values=='') return;
            $.post(U(url+"/getData"),{"style":"area","pID":values,'times':Math.random()},function(msg){
                //权限验证
                if(checkPower(msg)=='error'){
                    return false;
                }
                var data=msg['data'];
                if(data){
                    var output='';
                    output+=setOption(data,0,'area');
                    _this.after('<select class="selectArea" name="AreaID[]">'+output+'</select>');
                }
            });
        });
    },
    /**
     * 载入默认地区数据
     * @param string url 调用数据路径
     * @param string str 默认地区的数据id以逗号间隔
     */
    areaSelectLoad:function(url,str){
        var _this=$(this);
        _this.find('option').each(function(){
            if(str.indexOf('|'+$(this).val()+'|')!=-1){
                $(this).attr('selected','selected');
                if($(this).attr('last')==1) return;
                $.post(U(url+"/getData"),{"style":"area","pID":_this.val(),'times':Math.random()},function(msg){
                    //权限验证
                    if(checkPower(msg)=='error'){
                        return false;
                    }
                    var data=msg['data'];
                    if(data){
                        var output='';
                        output+=setOption(data,0,'area');
                        _this.after('<select class="selectArea" name="AreaID[]">'+output+'</select>');
                        if($('.selectArea').last().val()==""){
                            $('.selectArea').last().areaSelectLoad(url,str);
                        }
                    }
                });
            }
        });
    },
    /**
     * 载入默认知识点数据
     * @param string url 调用数据路径
     * @param string str 默认地区的数据id以逗号间隔
     */
    knowledgeSelectLoad:function(url,str){
        var _this=$(this);
        _this.find('option').each(function(){
            if(str.indexOf('|'+$(this).val()+'|')!=-1){
                $(this).attr('selected','selected');
                if($(this).attr('last')==1) return;
                $.post(U(url+"/getData"),{"style":"knowledge","pID":_this.val(),'times':Math.random()},function(msg){
                    //权限验证
                    if(checkPower(msg)=='error'){
                        return false;
                    }
                    var data=msg['data'];
                    if(data){
                        var output='';
                        output+=setOption(data,0,'knowledge');
                        _this.after('<select class="selectKnowledge" name="KlID[]">'+output+'</select>');
                        if($('.selectKnowledge').last().val()==""){
                            $('.selectKnowledge').last().knowledgeSelectLoad(url,str);
                        }
                    }
                });
            }
        });
    },
    /**
     * 载入默认章节数据
     * @param string url 调用数据路径
     * @param string str 默认地区的数据id以逗号间隔
     */
    chapterSelectLoad:function(url,str){
        var _this=$(this);
        _this.find('option').each(function(){
            if(str.indexOf('|'+$(this).val()+'|')!=-1){
                $(this).attr('selected','selected');
                if($(this).attr('last')==1) return;
                $.post(U(url+"/getData"),{"style":"chapter","pID":_this.val(),'times':Math.random()},function(msg){
                    //权限验证
                    if(checkPower(msg)=='error'){
                        return false;
                    }
                    var data=msg['data'];
                    if(data){
                        var output='';
                        output+=setOption(data,0,'chapter');
                        _this.after('<select class="selectChapter" name="chapterID[]">'+output+'</select>');
                        if($('.selectChapter').last().val()==""){
                            $('.selectChapter').last().chapterSelectLoad(url,str);
                        }
                    }
                });
            }
        });
    },
    /**
     * 修改页面加载
     * @param string url        调用数据路径
     * @param array  params     调取数据所需参数
     * @param int    subjectID  调取数据对应学科ID
     * @author demo
     */
    allSelectLoad:function(url,params){
        var i;
        var output='';
        $.post(U(url+"/getData"),{"style":params['style'],'subjectID':params['subjectID'],'list':params['list'],'idList':params['idList']},function(data){
            if(checkPower(data)=='error'){
                return false;
            }
            for(i in data['data']){
                output=setOption(data['data'][i],params['idList'][i],i);
                $('#'+i).html(output);
            }
            $('.delhang').css({'padding':'0px 5px','cursor':'pointer','color':'#f00'});
       });
    },
    /**
     * 文档属性切换改变是否测试属性 修改数据的input class样式必须为IfTest
     * @param string url 调用数据路径
     */
    docTypeSelectChange:function(url){
        var _this=$(this);
        _this.live('change',function(){
            if(_this.val()>0){
                $.post(U(url+'/getData'),{"style":'docType','ID':_this.val(),'times':Math.random()},function(msg){
                    //权限验证
                    if(checkPower(msg)=='error'){
                        return false;
                    }
                    var data=msg['data'];
                    if(data=='2'){
                        $('input.IfTest:eq(0)').attr('checked','checked');
                    }
                    if(data=='1'){
                        $('input.IfTest:eq(1)').attr('checked','checked');
                    }
                });
            }
        });
    }
});
//定义全局变量 获取相对路径
window['localhostPath'] = this.location.protocol + "//" + this.location.host + "/";
//加载JS ----urls可以是以逗号‘,’分割的字符串，也可以是数组
function load_script(urls) {
    //判断是否为数组
    if (!(urls instanceof Array ||
        (!(urls instanceof Object) && (Object.prototype.toString.call((urls)) == '[object Array]') ||
            typeof urls.length == 'number' && typeof urls.splice != 'undefined' &&
                typeof urls.propertyIsEnumerable != 'undefined' && !urls.propertyIsEnumerable('splice')))) {
        urls = urls.split(',');
    }
    //var localhostPath = this.location.protocol + "//" + this.location.host;
    var script;
    for (var i = 0; i < urls.length; i++) {
        script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = localhostPath + urls[i];
        document.getElementsByTagName('head')[0].appendChild(script);
    }
}
//加载css样式 ----urls可以是以逗号‘,’分割的字符串，也可以是数组
function load_css(srcs) {
    var css;
    //判断是否为数组
    if (!(srcs instanceof Array ||
        (!(srcs instanceof Object) && (Object.prototype.toString.call((srcs)) == '[object Array]') ||
            typeof srcs.length == 'number' && typeof srcs.splice != 'undefined' &&
                typeof srcs.propertyIsEnumerable != 'undefined' && !srcs.propertyIsEnumerable('splice')))) {
        srcs = srcs.split(',');
    }
    for (var i = 0; i < srcs.length; i++) {
        css = document.createElement("link");
        css.rel = 'stylesheet';
        css.type = 'text/css';
        css.href = localhostPath + srcs[i];
        document.getElementsByTagName("head")[0].appendChild(css);
    }
}

/**
 * 列表页字段值切换
 * @param url string 调用数据路径
 * @param that object 操作对象
 * @author demo
 */
function exchange(url,that){
    if(!window.confirm('确定修改当前状态？')){
        return false;
    }
    if($(that).attr('status')!=''){
        var status=$(that).attr('status');
    }
    var id=$(that).parent().attr('wid');
    $.post(U(url+'/replace'),{'wid':id,'status':status},function(data){
        //权限验证
        if(checkPower(data)=='error'){
            return false;
        }
        $(that).hide().siblings().show();
        alert(data.data);
    })
}

/**
 * 列表页字段值批量切换
 * @param url 调用数据路径
 * @param that  操作外围对象
 * @author demo
 */
function valueChanges(url,that){
    if(!window.confirm('确定修改当前状态？')){
        return false;
    }
    var idList='';
    var statusList='';
    $(that).find('input[class="key"]:checked').each(function(i){
        idList+=','+$(this).parent().parent().find('.status').attr('wid');
        statusList+=','+$(this).parent().parent().find('.system').attr('status');
    })
    idList=idList.substring(1);
    statusList=statusList.substring(1);
    $.post(U(url+'/replace'),{'wid':idList,'status':statusList},function(data){
        //权限验证
        if(checkPower(data)=='error'){
            return false;
        }
        $(that).find('input[class="key"]:checked').each(function(){
            $(this).parent().parent().find('.status').find('a:visible').hide().siblings().show();
        });
        alert(data.data);
    })
}

/**
 * 自动获取文件名
 * @param source object 原文件名对象
 * @param that object 点击的对象
 * @param target object 转换后文件名显示位置
 * @return string 转换后的文件名
 * @author demo
 */
 function getName(source,that,target){
    $(that).click(function(){
        var str=$(source).val();
        if(str=='') return;
        var docName=str.match(/[^\.\/\\]*\./ig).join('');
        var start=docName.substr(0,1);
        var end=docName.substr(docName.length-1,1);
        if(start=='/' || start=='\\') docName=docName.substr(1);
        if(end=='.') docName=docName.substr(0,docName.length-1);
        $(target).val(docName);
    })
}

var digitArray = new Array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f');
if(typeof(local)=='undefined'){
    var local = '/Manage/Index/index';
}
local=changeUrl(local);
