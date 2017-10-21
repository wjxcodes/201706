$(document).ready(function(){
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
            location.href  = URL+"/add";
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
            location.href =  URL+"/intro?id="+keyValue;
        });
    }
     //出库
    if($('.btout').length>0){
        $('.btout').click(function(){
            var keyValue = $(this).attr('thisid');
                if(!keyValue){
                    keyValue = getSelectCheckboxValues();
                }
                if(!keyValue){
                    alert('请选择出库项！');
                    return false;
                }
            location.href =  URL+"/out?id="+keyValue;
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
                location.href =  URL+"/edit?id="+keyValue;
            });
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
                location.href =  URL+"/delete?id="+keyValue;
            }
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
                location.href =  URL+"/del?id="+keyValue;
            }
        });
    }
    //导出
    if($('.btexport').length>0){
        $('.btexport').click(function(){
            if (window.confirm('确实要导出当前所有数据吗？')){
                $('#form1').attr('action',$('#form1').attr('action')+'-export');
                $('#form1').attr('target','_blank');
                $('#form1').submit();
                $('#form1').attr('target','_self');
                $('#form1').attr('action',$('#form1').attr('action').replace('-export',''));
            }
        });
    }
     //导入
    if($('.btdr').length>0){
        $('.btdr').click(function(){
            var keyValue  = getSelectCheckboxValues();
                if(!keyValue){
                    alert('请选择导入项！');
                    return false;
                }
                if (window.confirm('确实要导入吗？如果已导入过则覆盖原有数据')){
                    $('#form1').attr('action',URL+"/testsave");
                    $('#form1').submit();
                }
        });
    }
    //提取 获取选择行的id列表 
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
                var str='';
                str="<form id='btgetform' action='"+URL+"/getdata' method='post'><input name='id' type='hidden' value='"+keyValue+"'/></form><script>document.getElementById('btgetform').submit();</script>";
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
            location.href = URL+"/sort";
        });
    }
    if($('.inputDate').length>0){
        //加载js文件
        var urls = new Array();
        urls.push("Public/plugin/calendar/calendar.js");
        load_script(urls);
        var srcs = new Array();
        srcs.push("Public/plugin/calendar/calendar.css");
        load_css(srcs);
        $('.inputDate').bind('mouseenter',function(){
            jQuery.extend(DateInput.DEFAULT_OPTS, {
                month_names: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                short_month_names: ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二"],
                short_day_names: ["日", "一", "二", "三", "四", "五", "六"],
                dateToString: function(date) {
                    var month = (date.getMonth() + 1).toString();
                    var dom = date.getDate().toString();
                    if (month.length == 1) month = "0" + month;
                    if (dom.length == 1) dom = "0" + dom;
                    return date.getFullYear() + "-" + month + "-" + dom;
                }
            });
            $('.inputDate').each(function(){
                $(this).date_input();
            });
            $('.inputDate').unbind('mouseenter');
        });
    }
    
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
    //隐藏载入提示
    $('#loader').hide();
});
/*
 *信息提示框不关闭
 */
function showtext(g,e){
    var idname='msgdiv';
    tcLoadDiv('',(g.length*28)+30,idname);
    var position='';
    if(e==1) position=' style="background-position:-35px 0px;" ';
    var tmp_str='<div style="height:60px;"><span class="msgleft"'+position+'></span><span style="font-family:\'微软雅黑\',\'黑体\';font-size:20px;font-weight:bold;color:#555;line-height:60px;">'+g+'</span></div>';
    $('#'+idname+' .content').html(tmp_str);
    tcDivPosition(idname);//层位置
    $('#'+idname).css({'display':'block','opacity':1});
    $('#div_shadow'+idname).css({'display':'block'});
}
/*
 * 解决对话框ajax时处理错误信息的问题　
 */
function processErrorMsg(str){
    var error = 'errorMsg:';
    var i = str.indexOf(error);
    if(i == 0){
        return str.substr(error.length);
    }
    return true;
}

/**
* 权限验证提示
* @author demo
* @date 2014年11月20日
*/
function backLogin(data){
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
//初始化ajax全局信息
$.ajaxSetup({
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
                clearInterval(error);
                window.location.reload();
            }
            $('.spanSeconds').html(second);
        }, 1000);
    }
});
//2014-12-15 
function addData(data,params){
    params = $.extend({
        isSelected : false,
        selectedById : 0,
        frontChar : '',
        val : false, //提取json中的指定值
        text : false //提取json中指定的描述
    },params);
    if(params.val === false || params.text === false){
        return false;
    }
    var string = '';
    for(var i=0; i<data.length; i++){
        var elements = data[i];
        var val = elements[params['val']];
        var text = elements[params['text']];
        string += getOption(params['frontChar']+val, text, (params.isSelected && params.selectedById == val));
    }
    return string;
}

function getOption(val,text,isSelected,className){
    var str = '<option value="'+val+'"';
    if(className){
        str += ' class="'+className+'"';
    }
    if(isSelected){
        str += ' selected="selected"';
    }
    return str += ('>' + text + '</option>');
}

var nodes = [];
function addStrips(data){
    for(var d in data){
        var node = new Node(data[d]['SpecialID'],data[d]['SpecialName']);
        if(nodes.length > 0){
            nodes[nodes.length-1].last(node);
        }
        nodes.push(node);
        if(data[d]['sub']){
            addChildNode(data[d]['sub'],nodes[nodes.length-1]);
        }
    }
}

function addChildNode(sub,parent){
    for(var i in sub){
        var node = new Node(sub[i]['SpecialID'],sub[i]['SpecialName']);
        parent.add(node);
        if(typeof(sub[i]['sub']) !== 'undefined'){
            addChildNode(sub[i]['sub'],node);
        }
    }
}

function Node(v,t){
    this.v = v;
    this.t = t;
    this.p = null;
    this.c = [];
    this.ln = null;

    this.last = function(node){
        this.ln = node;
    }

    this.add = function(node){
        if(this.c.length > 0){
           this.c[this.c.length-1].last(node); 
        }
        node.p = this;
        this.c.push(node); 
    }

    this.getLevel = function(){
        var parent = this.p;
        var s = 0;
        while(parent != null){
            s++;
            parent = parent.p;
        }
        return s;
    }

    this.isTop = function(){
        return this.p == null;
    }

    this.getText = function(){
        var text = '';
        var s = this.getLevel();
        for(; s>0; s--){
            if(this.p.ln != null)
                text += '┃';
            else
                text += '&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        text += (this.ln != null ? '┝ ' : '┕ ');
        var val = this.c.length == 0 ? this.v : '';
        text = getOption(val,text + this.t);
        for(var i=0; i<this.c.length; i++){
            text += this.c[i].getText();
        }
        return text;            
    }
}

function print(obj){
    var str = '';
    for(var value in obj){
        if(typeof(obj[value]) === 'object'){
            str += print(obj[value]);
        }else{
            str += value+':'+obj[value] + '\r\n';
        }
    }
    str+='\r\n';
    return str;
}

//处理JSON数据
function setOption(data,id,biaoji){
    var str='';
    var bjone='';
    var bjtwo='';
    var i,j;
    if(biaoji=='chapter'){
        for(i in data){
            if(data[i]['Last']==1)
                str+='<option value="c'+data[i]['ChapterID']+'" '+(data[i]['ChapterID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['ChapterName']+'</option>';
            else
                str+='<option value="'+data[i]['ChapterID']+'" '+(data[i]['ChapterID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['ChapterName']+'</option>';
            if(data[i]['sub'])
                for(j=0;j<data[i]['sub'].length;j++){
                    if(data[i]['Last']==1)
                        str+='<option value="c'+data[i]['sub'][j]['ChapterID']+'" '+(data[i]['sub'][j]['ChapterID']==$id ? "selected=\"selected\"" : "") +'>　　'+data[i]['sub'][j]['ChapterName']+'</option>';
                    else
                        str+='<option value="'+$arrnn['ChapterID']+'" '+($arrnn['ChapterID']==$id ? "selected=\"selected\"" : "") +'>　　'+$arrnn['ChapterName']+'</option>';
                }
        }
    }else if(biaoji=='knowledge'){
        for(i in data){
            str+='<option value="'+data[i]['KlID']+'" '+(data[i]['KlID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['KlName']+'</option>';
            if(data[i]['sub'])
                for(j=0;j<data[i].length;j++){
                    str+='<option value="'+data[i][j]['KlID']+'" '+(data[i][j]['KlID']==id ? "selected=\"selected\"" : "") +'>　　'+data[i][j]['KlName']+'</option>';
                }
        }
    }
    return str;
}
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