var FormatTextManager = {
    err : '',
    attributes : {},
    html : '',
    identifier : '',
    types : null,
    alt: '智慧云题库组卷系统',
    isForamt : true,
    init : function(){
        this.attributes = {
            'topic' : [],
            'optionNum' : 0,
            'ifChoose' : 0
        }
        this.err = '';
    },
    //格式化试题内容 返回试题是否格式化成功 不成功为true
    formatContent : function(){
        FormatTextManager.init();
        for(var editor in $.Editor.instance){
            var ue = $.Editor.instance[editor];
            var info = FormatTextManager.process.call(ue);
            if(info !== true){
                FormatTextManager.err = info;
                break;
            }
        }
        return FormatTextManager.err != '';
    },

    process : function(){
        FormatTextManager.identifier = this.getOpt('textarea');
        return new ParseText(this.selection.getRange().document.body).parse();
    },

    getTopic : function(){
        var attributes = {
            'IfChoose' : this.attributes.ifChoose,
            'OptionNum' : this.attributes.optionNum
        };
        var complex = [];
        for(var i=0; i<this.attributes.topic.length; i++){
            complex.push({
                'no' : (i+1),
                'type' : this.attributes.topic[i]
            });
        }
        attributes['complex'] = complex;
        //console.log(attributes);
        return attributes;
    },

    hasChooseTest: function(){
        var types = this.types.find('option:selected');
        var testStyle = types.attr('typesstyle');
        var ifchoosetype = types.attr('ifchoosetype');
        return testStyle == 1 && ifchoosetype == 1;
    },

    hasTestNum : function(){
        var html = this.types.find('option:selected').html();
        if(html == '完形填空'){
            return true;
        }
        return false;
    }
}

function ParseText(dom,rules){
    rules = rules || {
        'unableTag' : ['script', 'style', 'object', 'iframe', 'embed', 'input', 'select' ,'div', 'ol', 'a'],
        'ableTag' : ['strong', 'em', 'td', 'tr', 'table', 'tbody', 'thead', 'tfoot', 'li','u','sup','sub'] //在属性为空，并且格式化后(mode=mark)时，运行存在的标签[process()]
    }
    this.parse = function(){
        appendSegmentTag(dom);
        recursion(dom.childNodes);
        mergeNearTextNode(dom);
        var formatText = new FormatText(FormatTextManager, dom);
        formatText.format();
        return true;
    }

    //将顶层dom下的第一级的文本节点加上p标签
    function appendSegmentTag(dom){
        for(var i=dom.childNodes.length-1; i>=0; i--){
            if(dom.childNodes[i].nodeType == 3){
                dom.childNodes[i].nodeValue = dom.childNodes[i].nodeValue.replace(/\r\n|\r|\n/g, '');
                if(dom.childNodes[i].nodeValue){
                    var seg = document.createElement('p');
                    var text = document.createTextNode(dom.childNodes[i].nodeValue);
                    seg.appendChild(text);
                    dom.appendChild(seg);
                }
                dom.removeChild(dom.childNodes[i]);
            }
        }
    }

    //将多个相邻的文本元素合并为一个
    function mergeNearTextNode(element){
        var next = element.childNodes[0];
        while(next && next.nextSibling){
            var i = next.childNodes.length-1;
            for(; i>0; i--){
                if(next.childNodes[i].nodeType == 3 && next.childNodes[i-1].nodeType == 3){
                    next.childNodes[i-1].nodeValue += next.childNodes[i].nodeValue;
                    next.removeChild(next.childNodes[i]);
                }
            }
            next = next.nextSibling;
        }
    }

    //将当前节点的内容合并至父节点，返回一个对象
    function mergeToParentNode(element){
        if(!element.parentNode){
            return;
        }
        replaceToNode(element);
    }

    //替换当前节点，如果存在子节点则将子节点加入到下一个兄弟节点之前
    function replaceToNode(current){
        var isExist = isExistElementNode(current);
        if(isExist){
            mergeNearTextNode(current);
            var i = current.childNodes.length-1;
            for(; i>=0; i--){
                var next = current.nextSibling;
                if(next){
                    current.parentNode.insertBefore(current.childNodes[i], next);
                }else{
                    current.parentNode.appendChild(current.childNodes[i]);
                }
            }
        }else{
            var text = getNodeText(current);
            if(current.nextSibling){
                current.parentNode.insertBefore(text, current.nextSibling);
            }else{
                current.parentNode.appendChild(text);
            }
            current.parentNode.removeChild(current);
        }
    }

    function isExistElementNode(element){
        var next = element.childNodes[0];
        while(next){
            if(1 == next.nodeType){
                return true;
            }
            next = next.nextSibling;
        }
        return false;
    }

    //返回当前节点的文本，仅当前节点
    function getNodeText(element){
        var i = element.childNodes.length-1;
        var text = '';
        for(; i>=0; i--){
            if(element.childNodes[i].nodeType == 3){
                text = element.childNodes[i].nodeValue + text;
            }
            element.removeChild(element.childNodes[i]);
        }
        return document.createTextNode(text);
    }

    //当前节点是否为保留节点
    function isExistenceNode(element, tagRules){
        var tagName = element.tagName.toUpperCase();
        var mark = true;
        for(var i=0; i<tagRules.length; i++){
            if(tagRules[i].toUpperCase() == tagName){
                mark = false;
                break;
            }
        }
        return mark;
    }

    function setStyle(element){
        var tagname = element.tagName.toUpperCase();
        if(tagname == 'IMG'){
            var style = element.style.cssText.toUpperCase();
            if(style.indexOf('WORD.GIF') >= 0 || style.indexOf('LOCALIMAGE.PNG') >= 0){
                element.parentNode.removeChild(element);
                return;
            }
            element.alt = FormatTextManager.alt;
            element.style.cssText += 'vertical-align:middle;';
            return;
        }
        if(tagname == 'TD' || tagname == 'TABLE'){
            // element.removeAttribute('width');
            // element.removeAttribute('height');
            // element.removeAttribute('valign');
        }
        //将li标签转换为相关小题标签
        if(tagname == 'LI'){
            var paragraph = document.createElement('p');
            paragraph.innerHTML = '1,'+element.innerHTML.replace(/<.*?[^>]>/g, '');
            element.parentNode.parentNode.insertBefore(paragraph, element.parentNode);
            //parent.removeChild(next);
            return;
        }
        var csstext = element.style.cssText;
        if(csstext !== null && !isModeOfTancestor(element)){
            element.removeAttribute('style');
        }
        csstext = csstext.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
        if(csstext.indexOf('TEXTDECORATIONUNDERLINE') >= 0){
            element.innerHTML = element.innerHTML.replace(/&nbsp;|\s| /g, '_');
            //element.setAttribute('style', '');
        }
    }

    //返回当前节点到body的层级
    function getLevel(element){
        var level = 0;
        while(element.parentNode && element.parentNode !== dom){
            element = element.parentNode;
            level++;
        }
        return level;
    }

    //查找祖先是否存在mode属性
    function isModeOfTancestor(element){
        while(element && element.parentNode){
            var mode = element.parentNode.getAttribute('mode');
            if(mode && mode.toUpperCase() == 'MARK'){
                return true;
            }
            if(element.parentNode.tagName.toUpperCase() == 'BODY'){
                return false;
            }
            element = element.parentNode;
        }
        return false;
    }

    function isDomNode(element){
        return element.nodeType == 1;
    }

    //返回编辑器顶层(body下一级)是否为粘贴内容
    function isCopy(element){
        return element.getAttribute('mode') || '';
    }

    function recursion(element){
        for(var i=0; i<element.length; i++){
            if(element[i].childNodes.length > 0){
                recursion(element[i].childNodes);
            }
            //删除注释信息
            if(8 == element[i].nodeType){
                element[i].parentNode.removeChild(element[i]);
            }
            if(isDomNode(element[i])){
                process(element[i]);
            }  
        }
    }

    function process(element){
        //如果是非保留标签或者注释信息则删除
        if(!isExistenceNode(element,rules['unableTag'])){
            element.parentNode.removeChild(element);
            return;
        }
        if(!isCopy(element)){
            setStyle(element);
            if(isExistenceNode(element,rules['ableTag']) && 
                    element.attributes.length == 0 && 
                        getLevel(element) >= 1){
                mergeToParentNode(element);
            }
            element.setAttribute('mode', 'mark');
        }
    }
}

function FormatText(manager, dom){
    var html = dom.innerHTML;
    var textarea = manager.identifier;
    var content = [];
    
    this.format = function(){
        if(textarea == 'Test'){
            return formatTest();
        }else if(textarea == 'Answer'){
            return formatAnswer();
        }
        return formatAnalyze();
    }

    function clearTag(str){
        return str.replace(/&nbsp;| /ig, '').replace(/<(?!img).*?[^<>]>/ig,'');
    }

    function formatTest(){
        var wxtk = manager.hasTestNum();
        if(wxtk){
            var pattern = /(_|&nbsp;){1,}\s{0,}[A-Za-z0-9]{1,2}\s{0,}(_|&nbsp;){1,}/img;
            formatTw(dom, pattern);
        }
        if(!wxtk && manager.hasChooseTest() && dom.innerHTML.indexOf('小题') == -1){
            var list = [];
            for(var i=0; i<dom.childNodes.length; i++){
                list.push(dom.childNodes[i].innerHTML);
            }
            var num = getExistenceChooseNum(list);
            if(num == 0){
                manager.err = '选择题型题文部分需包含相关选项！';
            }
            manager.attributes.optionNum = num;
            return;
        }
        var options = [];
        addTestTag(function(node){
            manager.attributes.topic.push(0);
        });
        var arr = dom.innerHTML.split('【小题】');
        arr.shift();
        for(var i=0; i<arr.length; i++){
            options.push(getExistenceChooseNum([' ',arr[i]]));
        }
        var str = options.toString();
        manager.attributes.optionNum = (str == '' ? 0 : str);
    }

    function formatAnswer(){
        var num = addTestTag();
        if(manager.attributes.topic.length > 0 && manager.attributes.topic.length != num){
            manager.err = '试题答案小题数量不一致！';
            return;
        }
        content = dom.innerHTML;
        var match = content.match(/【小题】/g) || [];
        if(match.length == 0){
            manager.attributes.ifChoose = getTestType(content);
        }else{
            manager.attributes.ifChoose = 1;
            var arr = content.split('【小题】');
            for(var i=1; i<arr.length; i++){
                manager.attributes.topic[i-1] = getTestType(arr[i]);
            }
        }
    }

    function formatAnalyze(){
        addTestTag();
        content = dom.innerHTML;
        var match = content.match(/【小题】/g) || [];
        var isEmpty = (clearTag(content) == '');
        if(!isEmpty && match.length != manager.attributes.topic.length){
            manager.err = '答案小题数量和小题解析数量不一致！';
        }
    }

    function getExistenceChooseNum(list){
        var data = [];
        for(var i=0; i<list.length; i++){
            if(list[i]){
                data.push(list[i].replace(/<.*?[^>]>/g,''));
            }
        }
        var pattern = /([A-Z]\s?(?:\)|\.|，|\,|。|）|】|\]|．){1,})/g;
        //i=1忽略第一行的题目信息
        data.shift(); //删除题目信息
        for(var i=0; i<data.length; i++){
            data[i] = (data[i].match(pattern));
        }
        data = data.toString().replace(/\[|\]|"|\r\n|\r|\n/g, '').split(',');
        var obj = {};
        var num = 0;
        for(var i=0; i<data.length; i++){
            if('' == data[i]){
                continue;
            }
            var k = data[i].replace(/[^A-Z]/, '');
            if(!obj[k]){
                num++;
                obj[k] = k;
            }
        }
        return num;
    }

    function formatTw(node, pattern){
        if(!manager.isForamt){
            return;
        }
        for(var i=0; i<node.childNodes.length; i++){
            if(node.childNodes[i].nodeType == 1){
                formatTw(node.childNodes[i], pattern);
            }
            if(node.childNodes[i].nodeType == 3 && pattern.test(node.childNodes[i].nodeValue)){
                node.childNodes[i].nodeValue = node.childNodes[i].nodeValue.replace(pattern, '【题号】').
                                    replace(/(\s|&nbsp;){0,}(【题号】)(\s|&nbsp;){0,}/ig, '$2');
            }
        }
    }

    //添加小题标签，返回小题标签数量
    function addTestTag(func){
        if(!manager.isForamt){
            return;
        }
        func = func || function(text){};
        var hint = 0;
        var pattern = /^[&nbsp;|\(|（|【|\[|\s]{0,}(\s{0,}\d{1,2}\s{0,}(?:\)|\.|，|\,|。|）|】|\]|．|、){1,}|【小题】)(.*?$)/im;
        //如果已经格式，则返回其匹配的结果长度
        // var match = dom.innerHTML.match(/小题】/g) || [];
        // if(match.length != 0){
        //     return match.length;
        // }
        for(var i=0; i<dom.childNodes.length; i++){
            if(dom.childNodes[i].nodeType != 1){
                continue;
            }
            var textNode = getFirstTextNode(dom.childNodes[i], pattern);
            if(textNode){
                var parent = getParentNode(textNode);
                if(parent.innerHTML.indexOf('题号') < 0 && parent.innerHTML.indexOf('小题') < 0){
                    textNode.nodeValue = textNode.nodeValue.replace(pattern,'【小题】$2');
                }
                hint++;
                func(parent);
            }        
        }
        return hint;
    }

    //返回node中第一个匹配pattern的文本节点
    function getFirstTextNode(node, pattern){
        if(node.childNodes.length == 0){
            return null;
        }
        for(var i=0; i<node.childNodes.length; i++){
            if(node.childNodes[i].nodeType == 1){
                return getFirstTextNode(node.childNodes[i], pattern);
            }
            if(node.childNodes[i].nodeType == 3 && pattern.test(node.childNodes[i].nodeValue)){
                return node.childNodes[i];
            }
        }
    }

    function getParentNode(node){
        while(node.parentNode){
            if(node.parentNode === dom){
                return node;
            }
            node = node.parentNode;
        }
    }

    function removeTag(str){
        //查找带下划线的内容
        var exp = new RegExp('(<[A-Za-z0-9]{1,}.*?TEXT-DECORATION:\s{0,}underline.*?>)(.*?)([^<]<\/[A-Za-z0-9]{1,}>)','img');
        str = str.replace(exp,function(val,g1,g2,g3){
                        return (g1+g2.replace(/&nbsp;/ig,'__')+g3);
                    }).
                    replace(/_\s{1,2}_/g,'__').
                    replace(/(<(?!\/)(?!p|span|img|table|td|tr|thead|tfoot|tbody|em|strong).*?[^>]>)|(<\/(?!p|span|img|table|td|tr|thead|tfoot|tbody|em|strong).*?>)/img,'').
                    replace(/(<(?!img)\w{1,})(.*?)(>)/img, function(val, g1, g2, g3){
                        if(/mode=["|']self["|']/img.test(g2)){
                            return g1+g2+g3;
                        }
                        return g1+g3;
                    });
        return str;
    }

    function getTestType(val){
        val = val.replace(/<.*?[^<>]>/img,'').replace('【小题】','').toUpperCase().replace(/(&NBSP;){1,}/g,' ').replace(/^\s{1,}|\s{1,}$/g,'');
        if(val == ''){
            manager.err = '试题答案有误，请检查';
            return false;
        }
        if(manager.hasChooseTest()){
            var arr = val.split(/\s{1,}/) || [];
            //匹配单选
            if(arr.length == 1 && /[A-Z]/.test(arr[0])){
                return 3;
            }
            return 2;
        }
        return 0;
    }
}