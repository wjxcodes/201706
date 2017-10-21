
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