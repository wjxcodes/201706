function Template(left,right){
    var LEFT = left || '<%';
    var RIGHT = right || '%>';
    var _arguments = null;
    var _params = [];

    this.render = function(tpl, data){
        _arguments = arguments;
        tpl = tpl.replace(/(\/\/.*)/ig, '').
                    replace(/\r\n|\r|\n|\t/g,'').
                    //去除html和js注释
                    replace(/(<\!\-\-.*[^(?:\-\->)]\-\->|\/\*.*?[^\/]\*\/)/g,'').
                    //转义双引号
                    replace(/"/g,'\\"').
                    //处理限定符之内的内容
                    replace(new RegExp(LEFT+'.*?[^('+RIGHT+')]'+RIGHT,'ig'),function(val){
                        return val.replace(/\\"/g,'"').
                                    replace(RIGHT,'_temp_arr.push("').
                                    replace(new RegExp(LEFT+'(?!=)','img'),'");');
                    }).
                    //数据输出
                    replace(new RegExp('(' + LEFT + '\s{0,}=)(\s{0,}.*?\s{0,})(_temp_arr)', 'img'), '" +$2); _temp_arr') + '"); return _temp_arr.join("");';
        tpl = 'var _temp_arr = []; _temp_arr.push("' + tpl;
        if(_arguments.length == 1){
            return tpl;
        }
        if(_arguments.length == 2){
            return new Function('datas',tpl)(data);
        }
        return createFunction(tpl);

    }
    //自定义变量名，数组形式['arg1','arg2',...]，值与render(tpl,'arg1的实际值','arg1的实际值',...)对应，
    this.setParams = function(params){
        _params = params;
        return this;
    }

    function createFunction(tpl){
        var size = _arguments.length;
        var argsName = [];
        var args = [];
        for(var i=1; i<size; i++){
            var param = _params[i-1] || ('arg'+i);
            argsName.push('\"'+param+'\"');
            args.push('args['+i+']');
        }
        var content = 'return new Function('+argsName.join(',')+',tpl)('+args.join(',')+');';
        return new Function('tpl','args',content)(tpl,_arguments);
    }
}