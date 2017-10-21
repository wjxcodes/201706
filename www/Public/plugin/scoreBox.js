$.fn.ScoreBox = function(opts){
    return $(this).each(function(){
        var score = false;
        opts = $.extend({
            'rules' : [[1,1],[2,1],[3,2],[4,2],[5,3],[6,3],[7,4],[8,4],[9,5],[10,5]],
            // 'rules' : [[1,1],[2,2],[3,3],[4,4],[5,5],[6,6],[7,7],[8,8],[9,9],[10,10]],
            'targetElement' : '<a></a>',
            'targetClassName' : 'scoreEle',
            'def' : 5, //默认值
            'isBindEvent' : true,
            'former' : 'icon-star', //that对象所在的类
            //样式所在类
            'classes' : {
                'full' : 'start1',
                'half' : 'start2',
                'none' : 'start3'
            },
            callback : function(score, current){}
        },opts);
        var that = $(this);
        if(opts.former)
            that.addClass(opts.former);
        var elements = getElements(opts.rules);
        for(var i=0; i<elements.length; i++){
            var element = $(opts.targetElement);
            element.addClass(opts.targetClassName);
            element.attr({
                'size' : elements[i].length
            }).addClass(opts.classes.none);
            that.append(element);
            //加载默认分值
            var className = '';
            if(elements[i].length == 2){
                if(opts.rules[elements[i][0]][0] == opts.def){
                    className = opts.classes.half;
                }else if(opts.rules[elements[i][1]][0] == opts.def){
                   className = opts.classes.full;
                }
            }else if(opts.rules[elements[i][0]][0] == opts.def){
                className = opts.classes.full;
            }
            if(className != ''){
                previousAttachClass(element, element.index(), opts);
                _addClass(element, className, opts);
                opts.callback(opts.def, element);
            }
        }
        if(!opts.isBindEvent){
            return false;
        }
        var targetElements = that.find('.'+opts.targetClassName);
        targetElements.live('mousemove', function(e){
            var element = $(this);
            var s = move(elements, element, opts, e);
            if(score === false || s != score){
                score = s;
                opts.callback(s, element);
            }
        });
        targetElements.live('mouseout', function(e){
            var current = $(this);
            var index = current.index();
            if(index == 0 && e.pageX <= current.offset().left){
                _removeClass(current, opts.targetClassName);
                current.addClass(opts.classes.none);
                opts.callback(0, current);
                score = false;
            }
        });
    });
    
    function move(elements, current, opts, e){
        var index = current.index();
        previousAttachClass(current, index, opts);
        var size = current.attr('size');
        var width = current.width();
        var left = current.offset().left;
        width = width / size;
        var score = 0;
        var className = '';
        if(2 == size){
            if((e.pageX - left) < width){
                className = opts.classes.half;
                score = opts.rules[elements[index][0]][0];
            }else{
                className = opts.classes.full;
                score = opts.rules[elements[index][1]][0];
            }
        }else if(1 == size){
            className = opts.classes.full;
            score = opts.rules[elements[index][0]][0];
        }
        _addClass(current, className, opts);
        return score;
    }

    //将current之前的元素加上full样式
    function previousAttachClass(current, index, opts){
        current.siblings('.'+opts.targetClassName).each(function(){
            var sibling = $(this);
            var i = sibling.index();
            if(i != index){
                _removeClass(sibling, opts.targetClassName);
                if(i < index){
                    sibling.addClass(opts.classes.full);
                }else{
                    sibling.addClass(opts.classes.none);
                }
            }
        });
    }

    //将当前元素加上给定的类
    function _addClass(current, className, opts){
        _removeClass(current, opts.targetClassName);
        if(!current.hasClass(className)){
            current.addClass(className);
        }
    }

    function _removeClass(element, hold){
        var arr = element.attr('class').split(/\s+/);
        for(var i=0; i<arr.length; i++){
            if(arr[i] != hold){
                element.removeClass(arr[i]);
            }
        }
    }

    /*
     * 根据rules进行分解元素
     * 返回最终需生成的结果数组
    */
    function getElements(rules){
        var elements = [];
        var pair = false;
        for(var i=0; i<rules.length-1; i++){
            if(isSame(rules, i, i+1)){
                pair = true;
                elements.push([i, i+1]);
            }else if(!pair){
                elements.push([i]);
                if(i == rules.length - 2){
                    elements.push([i+1]);
                }
            }
        }
        return elements;
    }

    function isSame(rules, i, ci){
        return rules[i][1] == rules[ci][1];
    }
}