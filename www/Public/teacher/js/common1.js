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
