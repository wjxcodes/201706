/**
 * @author 邵敬超
 * @date 2016/11/15
 */
define(function (require, exports, module) {

    // ie8以下浏览器提示信息
    if (!Object.create) {
        alert("您的浏览器版本过低，\n 请更换更高版本的浏览器进行操作！");
        $("#app").html('');
        $(".is-old-ie").show();
        return;
    }

    //扩展数组方法@去重
    Array.prototype.unique = function () {
        var _this = this;
        _this.sort();
        var newArray = [_this[0]];
        for (var i = 1; i < _this.length; i++) {
            if (_this[i] !== newArray[newArray.length - 1]) {
                newArray.push(_this[i])
            }
        }
        return newArray;
    };

    $.extend({
        console:console
    });

    //组件间通信
    window.Bus = new Vue();

    /*答题卡主体*/
    /**
 * @author 邵敬超
 * @date 2016/11/16
 */
// 答题卡头部信息
Vue.component("main-top", {
    template: "#tpl-main-top",
    props: ["title"],
    methods: {
        showTitleAlert: function () {
            app.alert.state_alertTitle = true;
        }
    }
});
// 试题头信息
Vue.component('ques-title-info', {
    template: '<div class="q-title-inner" v-if="item.display==1"><p>{{item.title}}</p></div>',
    props: ["item", "score"]
})

//考试信息栏 A4
Vue.component("exam-info-a4", {
    template: "#tpl-exam-info-a4",
    props: ["care", "type", "data"],
    data: function () {
        return {}
    },
    methods: {
        showAlertExamInfo: function () {
            app.alert.state_alertExamInfo = true;
        }
    }
});

//考试信息栏 A3
Vue.component("exam-info-a3", {
    template: "#tpl-exam-info-a3",
    props: ["care", "type", "data"],
    methods: {
        showAlertExamInfo: function () {
            app.alert.state_alertExamInfo = true;
        }
    }
});
/*版式*/
Vue.component("paper-style", {
    template: "#tpl-paper-style",
    props: [
        "dataState",
        "layout",
        "data",
        "care",
        "title",
        "type",
        "examPart",
        "modal",
        "score"
    ],
    data: function () {
        return {
            hlineHeight: 48,
            quesTemp: {
                choice: ["A", "B", "C", "D", "E", "F", "G"],
                trueFalse: ["√", "x"]
            },
            autoOrderChoice: false,
            saveOldOrderID: []
        }
    },
    methods: {
        /*切换选择题版式*/
        changeTypeItemStyle: function (i, j) {
            var style = app.data.paper[i].list[j].style;
            if (style == 0) {
                app.data.paper[i].list[j].style = 1;
            } else {
                app.data.paper[i].list[j].style = 0;
            }
            app.dataState += 1;
        },
        /*编辑分卷标题*/
        showAlertEditPartTitle: function () {
            app.alert.state_alertPartInfo = true;
        },
        /*编辑标题*/
        showAlertEditQuesTitle: function (pid, tid) {
            Bus.$emit("showAlertEditQuesTitle", pid, tid);
            app.alert.state_alertEditQuesTitle = true
        },
        /*添加选择题*/
        showAlertXuanzeti: function (i, j) {
            Bus.$emit("showAlertXuanzeti", {part: i, block: j});
            app.alert.state_alertXuanzeti = true
        },
        /*添加填空题*/
        showAlertTiankongti: function (i,j) {
            Bus.$emit("showAlertTiankongti",{part: i, block: j});
            app.alert.state_alertTiankongti = true
        },
        /*添加解答题*/
        showAlertJiedati: function (i,j) {
            Bus.$emit("showAlertJiedati",{part: i, block: j});
            app.alert.state_alertJiedati = true
        },
        /*添加英语作文*/
        showAlertEditEnZuowen: function (pid, tid) {
            Bus.$emit("showAlertEditEnZuowen", pid, tid);
            app.alert.state_alertEnZuowen = true
        },
        /*添加语文作文*/
        showAlertEditCnZuowen: function (pid, tid) {
            Bus.$emit("showAlertEditCnZuowen", pid, tid);
            app.alert.state_alertCnZuowen = true
        },
        /*下移*/
        moveDownThisQues: function (pid,tid) {
            app.moveDown(pid,tid);
        },
        /*上移*/
        moveUpThisQues: function (pid,tid) {
            app.moveUp(pid,tid);
        },
        /*删除*/
        delThisQues: function (pid,tid,oid) {
            app.delQuesType(pid,tid,oid);
        }
    }
});

/*解答题*/
Vue.component("jd-kong-template",{
    template:"#tpl-jd-kong-template",
    props:["contentItem","partItemIndex", "typeItemIndex", "subOrderID","score","itemLength"],
    data:function(){
        return {
            hoverThis:0
        }
    },
    computed:{
        // 试题id
        cardQuesId:function(){
            var $this = this;
            return $this.contentItem.order+'.'+$this.contentItem.small;
        },
        // formatHline:function(num){
        //     var val = parseInt(num);
        //     if (val != num && val > 1){
        //         return parseInt(num);
        //     }else{
        //         return val;
        //     }
        //
        //
        // },

        kongItemLen:function () {
            var $this = this;
          return ((( 0 <$this.contentItem.hline && $this.contentItem.hline < 1 )
              ? 1 :
              $this.contentItem.hline ) * ( $this.contentItem.kong== 0 ? 1 : $this.contentItem.kong ));
        },
        isSmall:function(){
            return this.contentItem.small!=0
        },
        isNoImgClass:function () {
            var $this = this;
          return {'hang-1-3':$this.contentItem.hline==0.3,
              'hang-1-1':$this.contentItem.hline>=1,
              'hang-1-2':$this.contentItem.hline==0.5,
              'no-line':$this.contentItem.uline==0 || ($this.contentItem.desc && $this.contentItem.desc.length > 0)};
        },
        isImgClass:function () {
            var $this = this;
            if($this.contentItem.hline){
                return {'hang-1-3':$this.contentItem.hline==0.3, 'hang-1-1':$this.contentItem.hline>=1, 'hang-1-2':$this.contentItem.hline==0.5};
            }
        },
        isImg:function(){
            var $this = this;
            if(!$this.contentItem.img || ($this.contentItem.img && $this.contentItem.img.length===0)){
                return false;
            }else if($this.contentItem.img && $this.contentItem.img.length>0){
                return true;
            }
        },
        hangHasImgStyle:function(){
            return {'min-height':Math.max(this.kongItemLen * 48 , 150) + 'px'};
        },
        isQuesBlock:function(){
            var $this = this;
            if($this.contentItem.hline){
                return $this.contentItem.hline>=2;
            }
        },
        isContentLastQuesBlock:function () {
            return this.isQuesBlock === true && (this.itemLength - 1) === this.subOrderID
        },
        isShowQuesBlockLine:function(){
            var $this = this;
            if($this.contentItem.hline){
                return $this.contentItem.hline>=2 && $this.subOrderID !==0 && $this.contentItem.small <= 1;
            }
        },
        // 短文改错
        isENError:function () {
            return this.contentItem.kong == 0 && this.contentItem.hline == 0;
        }
    },
    methods:{

        /*删除*/
        delThisQues: function (pid,tid,oid) {
            app.delQuesType(pid,tid,oid);
        },

        /*下移*/
        moveDownThisQues: function (pid,tid,oid) {
            app.ItemMoveDown(pid,tid,oid);
        },

        /*上移*/
        moveUpThisQues: function (pid,tid,oid) {
            app.itemMoveUp(pid,tid,oid);
        },

        /*单题编辑 - 监听编辑试题弹框*/
        showAlertEditJiedati:function(pid,tid,oid){
            app.$forceUpdate();
            Bus.$emit("showAlertEditJiedati",{
                pid:pid,
                tid:tid,
                oid:oid
            });
            app.alert.state_alertEditJiedati = true;
        },

        /*单题操作-鼠标移入*/
        hoverThisQues:function(over){
            this.hoverThis = over;
        }
    }
});

/*选答题*/
Vue.component("xuandati-template", {
    template: "#tpl-xuandati-template",
    props: ["dataState", "partItem", "partItemIndex", "typeItemIndex", "score","typeItem"],
    data: function () {
        return {
            hlineHeight: 48
        }
    },
    computed:{

        // 重新定义选答题数据
        newType2:function () {
            var data = this.typeItem;
            var _this = this;
            var newType = $.parseJSON(JSON.stringify(data));//解除vue原数据绑定
            var newContent = {
                order: [],
                img: [],
                desc: [],
                small: 0,
                kong: 0,
                uline: 0,
                hline: 0,
                score: ''
            };
            var content = newType.content;
            for (var j = 0; j < content.length; j++) {
                var contentItem = content[j];
                console.log(contentItem);
                newContent.order.push(contentItem.order);
                if(contentItem.img && contentItem.img.length>0){
                    newContent.img.push(contentItem.img);
                }
                if(contentItem.desc && contentItem.desc.length>0){
                    newContent.desc.push(contentItem.order + ". " +contentItem.desc);
                }
                newContent.hline = _this.getMaxNumber(j, content, 'hline');
                newContent.uline = _this.getMaxNumber(j, content, 'uline');
                newContent.score = _this.getMaxNumber(j, content, 'score');
            }
            newType.content = newContent;
            console.log(newType);
            return newType;
        },

        //计算空总数量
        computedKong:function () {
            var newHline = this.newType2.content.hline;
            var newDo = this.newType2.do;
            return (newHline<=0 ? 1 : newHline) * newDo;
        },

        isHasImg:function () {
            console.log(this.newType2);
            return this.newType2.content.img != null && this.newType2.content.img.length>0
        }
    },
    methods: {

        /*下移*/
        moveDownThisQues: function (pid,tid) {
            app.moveDown(pid,tid);
        },
        /*上移*/
        moveUpThisQues: function (pid,tid) {
            app.moveUp(pid,tid);
        },
        /*删除*/
        delThisQues: function (pid,tid) {
            app.delQuesType(pid,tid);
        },
        // 获取数据中最大值
        getMaxNumber: function (i, obj, attr) {
            if (i == 0) {
                return obj[0][attr];
            }
            if (i > 0) {
                return Math.max(obj[i - 1][attr], obj[i][attr])
            }
        },
        showAlertEditXuandati: function (pid, tid) {
            Bus.$emit("showAlertEditXuandati", pid, tid);
            app.alert.state_alertEditXuandati = true
        }
    }
});

/*描述*/
Vue.component("ques-desc",
    {
        template:'<p class="J_ques-desc" v-if="viewIf" v-html="vHtml"></p>',
        props:["contentItem"],
        data:function () {
            return {
            }
        },
        computed:{
            viewIf:function(){
                return this.contentItem.desc && this.contentItem.desc.length>0
            },
            vHtml:function(){
                if(this.viewIf){
                    return this.contentItem.desc.replace(/\n/g,"<br\>")
                }
            }
        }
    });;
    /*右侧操作栏*/
    /**
 * @author 邵敬超
 * @date 2016/11/16
 */
//选择版式
Vue.component("type-style", {
    template: "#tpl-type-style",
    props: ["layout"],
    data: function () {
        return {
            typeA4: {
                src: "/Public/default/image/answer/formatA4.png"
            },
            typeA3: {
                src: "/Public/default/image/answer/formatA3.png"
            }
        }
    },
    methods: {
        chooseTypeA3: function () {
            this.typeA3.checked = true;
            this.typeA4.checked = false;
            app.data.layout.style = 'A3';
            app.dataState += 1;
        },
        chooseTypeA4: function () {
            this.typeA3.checked = false;
            this.typeA4.checked = true;
            app.data.layout.style = 'A4';
            app.dataState += 1;
        }
    }
});

/*考试类型*/
Vue.component("exam-type", {
    template: "#tpl-exam-type",
    props: ["type"],
    data: function () {
        return {}
    },
    methods: {
        toggleExamType: function (event) {
            app.data.type.display = $(event.currentTarget).data("examtype");
            app.dataState += 1;
        }
    }
});

/*添加试题*/
Vue.component("add-test", {
    template: "#tpl-add-test",
    props: ["modal"],
    data: function () {
        return {
            comeFrom: "bar"
        }
    },
    methods: {
        showAlertTiankongti: function (come) {
            Bus.$emit("showAlertTiankongti", come);
            app.alert.state_alertTiankongti = true
        },
        showAlertXuanzeti: function (come) {
            Bus.$emit("showAlertXuanzeti", come);
            app.alert.state_alertXuanzeti = true
        },
        showAlertJiedati: function (come) {
            Bus.$emit("showAlertJiedati", come);
            app.alert.state_alertJiedati = true
        },
        showAlertXuandati: function (come) {
            Bus.$emit("showAlertXuandati", come);
            app.alert.state_alertXuandati = true
        },
        showAlertEnZuowen: function (come) {
            Bus.$emit("showAlertEnZuowen", come);
            app.alert.state_alertEnZuowen = true
        },
        showAlertCnZuowen: function (come) {
            Bus.$emit("showAlertCnZuowen", come);
            app.alert.state_alertCnZuowen = true
        }
    }
});

/*是否显示分数*/
Vue.component("score-view", {
    template: "#tpl-score-view",
    props: ["score"],
    watch: {
        "score.display": function (val) {
            app.dataState += 1;
            console.log(val);
        }
    }
});

/*是否显示禁答区*/
Vue.component("forbid-view", {
    template: "#tpl-forbid-view",
    data: function () {
        return {
            display: 1
        }
    },
    watch: {
        "display": function (val) {
            if (app.noWritable.display = (val - 0)) {
                layer.msg("已启用！", {time: 1000})
            } else {
                layer.msg("已禁用！", {time: 1000})
            }
            app.dataState += 1;
        }
    }
});

/*已添加试题*/
Vue.component("test-list", {
    template: "#tpl-test-list",
    props: ["modal", "examPart"],
    methods: {

        /*滚动到对应位置*/
        scrollThisPlace: function (e) {
            if($("#left-con").is(":animated")) return false;
            var $thisItemDiv = $($(e.currentTarget).data("href"));
            var itemTop = $thisItemDiv.offset().top - 50;

            var conScrollTop = $("#left-con").scrollTop();
            $("#left-con").animate({
                scrollTop: itemTop + conScrollTop
            }, 600,function () {
                $thisItemDiv.addClass("animated-flash");
                setTimeout(function () {
                    $thisItemDiv.removeClass("animated-flash");
                },1000);
            })


        },
        /*删除试题*/
        barDelThisQuesType: function (pid, tid) {
            app.delQuesType(pid, tid);
        },
        /*删除所有试题*/
        delAllQues: function (pid) {
            app.delAllQues(pid);
        }
    }
});

/*底部操作按钮*/
Vue.component("side-btm-handle", {
    props:["page"],
    template: "#tpl-side-btm-handle",
    methods: {
        // 保存答题卡
        saveAnswerJson: function () {
            var params = {
                Json: JSON.stringify(app.data),
                SaveID: editData.getAttr(1) || 0
            };
            var layerSave = layer.load(1);
            $.post(U("Home/Index/saveAnswerJson"), params, function (data) {
                layer.close(layerSave);
                if (data.info === 'success') {
                    layer.msg("保存成功！", {icon: 1, time: 1000, shade: 0.3})
                } else {
                    layer.msg("保存失败！" + JSON.stringify(data.data), {icon: 2, time: 1000, shade: 0.3})
                }
            })
        },
        // 下载答题卡
        answerDown: function () {

            // 字符串答题卡数据
            var answerData = $.parseJSON(JSON.stringify(app.data));
            // 下载答题卡接口
            function answerDownFunc(data) {

                var answerload = layer.msg("正在生成答题卡，请稍候...",{time:0});

                var params = {
                    sheetxml: JSON.stringify(data)
                };
                // 创建div接收程序返回js代码
                function createDiv(data) {
                    $("body").append("<div id='dtktishi'></div>" + data);
                }

                $.post(U("Home/Index/arswerMyDown"), params, function (data) {

                    layer.close(answerload);
                    //错误提示
                    if (typeof data.data === 'string') {
                        createDiv(data.data);
                        layer.alert($("#dtktishi").find("b").removeAttr("style").html(), {
                            icon: 5
                        })
                    }
                    //获取成功
                    if (typeof data.data === 'object') {
                        createDiv(data.data);
                        layer.alert($("#dtktishi").html(), {
                            icon: 6
                        });
                        setTimeout(function () {
                            window.open($("#dtktishi").find("a").attr("href"));
                        }, 500)
                    }
                });
            };
            // AB卷切换
            function toggleABPaper(type, data) {
                var ansPaper = data.paper;
                $.each(ansPaper, function (i) {
                    var ansList = ansPaper[i].list;
                    $.each(ansList, function (j) {
                        if (ansList[j].type == 0) {//是客观题
                            if (type == "B") {
                                if (ansList[j].style == 1) {
                                    ansList[j].style = 0;
                                } else {
                                    ansList[j].style = 1;
                                }
                            }
                        }
                    });
                });
                answerDownFunc(data);
            }

            // 获取答题卡版式，如果是AB卷弹出AB卷选择框，否则下载统一版式
            if (editData.getAttr(2) == '2') {
                layer.open({
                    type: 0,
                    icon: 0,
                    content: "请选择AB卷！",
                    area: ["240px", "160px"],
                    btn: ["A卷", "B卷"],
                    btn1: function () {
                        toggleABPaper("A", answerData);
                    },
                    btn2: function () {
                        toggleABPaper("B", answerData);
                    }
                });
            } else {
                answerDownFunc(answerData);
            }
        }
    }
});;
    /*弹框*/
    /**
 * @author 邵敬超
 * @date 2016/11/16
 */

/*========
 添加试题时自定义题号
 1. 激活题号设置功能
 2. 用户手动输入题号
 3. 输入同时检查题号合法性
 4. 合法即设置题号 否则不设置
 ========*/
Vue.component('set-ques-order', {
    template: '#tpl-set-ques-order',
    data: function () {
        return {
            suggestOrderID: '',//题号设置建议
            setterOrder: '',//题号设置建议
            layerTips: '',//题号设置建议tips
            toggleSetOrderID: false//手动设置是否激活
        }
    },

    props: ["selectedAddedTestNum"],
    computed: {
        computedOrderID: function () {
            return this.suggestOrderID || app.examOrderIDArray.max + 1;
        }
    },

    watch: {
        //传递题号设置激活状态
        "toggleSetOrderID": function () {
            Bus.$emit("toggleSetOrderID", this.toggleSetOrderID);
        }
    },

    methods: {
        // 题号设置建议
        showSuggestOrderID: function (e) {
            var thisTarget = e.target,
                examOrderID = app.examOrderIDArray,
                min = examOrderID.min,
                max = examOrderID.max,
                sugMsg = '';
            if (max == 0) {
                sugMsg += "还没有添加试题哦！"
            }
            if (0 < max) {
                if (!examOrderID.outOrder.length) {
                    sugMsg += '当前题号为：' + min + '-' + max + '！'
                } else {
                    sugMsg += "当前题号" + min + '-' + max + '中，';
                    sugMsg += '未使用的题号有：\n [ ' + (examOrderID.outOrder.join(", ")) + ' ].';
                }
            }
            this.layerTips = layer.tips(sugMsg, thisTarget, {
                tips: [1, '#3595CC'],
                time: 0
            });
        },
        // 隐藏提示信息
        hideSuggestOrderID: function () {
            layer.close(this.layerTips)
        },
        // 检查输入值合法性
        checkSetterOrderID: function (e) {
            var _this = this;
            var $currTarget = $(e.target);
            var setterOrder = $.trim($currTarget.val()) - 0;
            if (setterOrder > 0) {
                // 设置的试序号是否在试题序号数组中
                if ($.inArray(setterOrder, app.examOrderIDArray.order) !== -1) {
                    $currTarget.attr("able", 0);
                    $currTarget.val(_this.suggestOrderID);
                    layer.msg("试题序号<b> " + setterOrder + " </b>已存在 !", {time: 1500})
                    return
                }
                $currTarget.attr("able", 1);
                _this.suggestOrderID = setterOrder;
                $currTarget.val(_this.suggestOrderID);
                Bus.$emit("sendSetterOrderID", setterOrder);
            } else {
                $currTarget.val(_this.suggestOrderID);
                layer.msg("试题序号只能是大于 0 的数字！", {time: 2500});
            }
        }
    }
});
/*========
 添加到 ：选择分卷
 ========*/
Vue.component('select-part-to', {
    template: '<div class="itm-item">\
        <span class="item-name">添加到：</span><select class="w8em j_selectPartTo" v-model="selectPartTo">\
        <option value="0">第Ⅰ卷</option>\
        <option value="1">第Ⅱ卷</option>\
        </select></div>',
    props: ["addPartTo"],
    computed: {
        //默认添加到第几卷
        selectPartTo: function () {
            return this.addPartTo;
        }
    }
});

/*========
 编辑头部信息
 ========*/
Vue.component("tpl-alert-title", {
    template: "#tpl-alert-title",
    props: ["modal", "title"],
    data: function () {
        return {
            showHtml: '<i class="iconfont">&#xe608;</i>隐藏',
            hideHtml: '<i class="iconfont">&#xe609;</i>显示'
        };
    },
    computed: {
        paperName: function () {
            return $.extend(
                {},
                this.title.top,
                {html: this.title.top.display === 1 ? this.showHtml : this.hideHtml}
            );
        },
        mainTitle: function () {
            return $.extend(
                {},
                this.title.title,
                {html: this.title.title.display === 1 ? this.showHtml : this.hideHtml}
            );
        },
        subTitle: function () {
            return $.extend(
                {},
                this.title.sub,
                {html: this.title.sub.display === 1 ? this.showHtml : this.hideHtml}
            );
        }
    },
    methods: {
        yesBtn: function () {
            var $form = $("#form-title-info").find('input:text');
            var objTitle = {};
            // 缓存设置状态
            $.each($form, function () {
                objTitle[this.name] = {};
                objTitle[this.name]["content"] = this.value;
                objTitle[this.name]["display"] = !this.disabled;
            });
            app.data.top = objTitle.paperName;
            app.data.title = objTitle.mainTitle;
            app.data.sub = objTitle.subTitle;
            app.dataState += 1;
            app.alert.state_alertTitle = false;
        },
        toggleShowState: function (event) {
            var $this = $(event.currentTarget);
            var $thisValue = $this.parents('.itm-item').find("input"),
                $thisProp = $thisValue.prop("disabled");
            if ($thisProp === true) {
                $this.html(this.showHtml);
                $this.parents('.itm-item').find("input").prop({"disabled": false});
            } else if ($thisProp === false) {
                $this.html(this.hideHtml);
                $this.parents('.itm-item').find("input").prop({"disabled": true});
            }
        },
        closeThisModal: function () {
            app.alert.state_alertTitle = false;
        }
    }
});

/*========
 编辑答题卡
 ========*/
Vue.component("alert-exam-info", {
    template: "#tpl-alert-exam-info",
    props: ["care", "modal", "data"],
    data: function () {
        return {}
    },
    computed: {
        numDisplay: function () {
            return this.data.num.display;
        },
        codeDisplay: function () {
            return this.data.code.display;
        },
        careDisplay: function () {
            return this.data.care.display;
        },
        missDisplay: function () {
            return this.data.miss.display;
        }
    },
    methods: {
        careDisplayChange: function (event) {
            var $examInfoNotice = $("#examInfoNotice");
            if ($(event.currentTarget).prop("checked") === true) {
                $examInfoNotice.prop("disabled", false)
            } else {
                $examInfoNotice.prop("disabled", true)
            }

        },
        yesBtn: function () {
            var _this = this;
            _this.data.care.content = $("#examInfoNotice").val();
            _this.data.num.display = $("#J_numDisplay").prop("checked") === false ? 0 : 1;
            _this.data.code.display = $("#J_codeDisplay").prop("checked") === false ? 0 : 1;
            _this.data.care.display = $("#J_careDisplay").prop("checked") === false ? 0 : 1;
            _this.data.miss.display = $("#J_missDisplay").prop("checked") === false ? 0 : 1;
            app.dataState += 1;
            app.alert.state_alertExamInfo = false
        },
        closeThisModal: function () {
            app.alert.state_alertExamInfo = false
        }
    }
});


/*========
 编辑分卷信息
 ========*/

Vue.component("alert-part-info", {
    template: "#tpl-alert-part-info",
    props: ["examPart", "modal"],
    methods: {
        yesBtn: function () {
            var partInfoSetItem = $("#partInfoSet").find(".partInfoItem");
            partInfoSetItem.each(function (i) {
                var $this = $(this);
                // 更新数据
                app.data.paper[i].title = $this.find("[name=part-title-" + i + "]").val();
                app.data.paper[i].desc = $this.find("[name=part-desc-" + i + "]").val();
            });
            app.dataState += 1;
            app.alert.state_alertPartInfo = false
        },
        closeThisModal: function () {
            app.alert.state_alertPartInfo = false
        }
    }
});


/*========
 编辑-试题title
 ========*/
Vue.component("alert-edit-ques-title", {
    template: "#tpl-alert-edit-ques-title",
    props: ["modal", "examPart"],
    data: function () {
        return {
            currentPartID: '',
            currentTitleID: '' //记录当前点击的试题索引
        }
    },
    beforeCreate: function () {
        var _this = this;
        Bus.$on("showAlertEditQuesTitle", function (pid, tid) {
            _this.currentPartID = pid;
            _this.currentTitleID = tid;
        });
    },
    computed: {
        titleShowModal: function () {
            var _this = this;
            if (_this.currentPartID !== '' && _this.currentTitleID !== '') {
                return _this.examPart[_this.currentPartID].list[_this.currentTitleID].display;
            } else {
                return '0';
            }
        }
    },
    methods: {
        yesBtn: function (pid, tid) {
            // 更新数据
            app.data.paper[pid].list[tid].title = $('[name=ques-title-' + pid + '-' + tid + ']').val();
            app.data.paper[pid].list[tid].display = $('[name=ques-title-display-' + pid + '-' + tid + ']:checked').val() - 0;
            app.dataState += 1;
            app.alert.state_alertEditQuesTitle = false;
        },
        closeThisModal: function () {
            app.alert.state_alertEditQuesTitle = false
        }
    }
});


/*===================添加试题===================*/
/*Vue组件混合配置项------------------*/

var VueMixin_AddQuesOrderConfig = {
    props: ["modal", "examOrderIDArray"],
    data: function () {
        return {
            addFrom: '', //何种方式添加试题
            selectedAddedTestNum: 1,
            toggleSetOrderID: false, //是否设置题号
            getOrderID: ''
        }
    },
    created: function () {
        var _this = this;
        Bus.$on("toggleSetOrderID", function (val) {
            _this.toggleSetOrderID = val;
        });

        // 监听组件传递的值并更新
        Bus.$on("sendSetterOrderID", function (val) {
            _this.getOrderID = val;
        })
    },
    computed: {
        getOrderIDInit: function () {
            return this.examOrderIDArray.max + 1
        },
        computedOrderID: function () {
            var _this = this;

            /*仅当添加题数为1，且设置题号为true时，使用自定义题号，否则使用自动题号*/
            if (_this.selectedAddedTestNum == 1) {
                if (_this.toggleSetOrderID) {
                    return _this.getOrderID || _this.getOrderIDInit
                }
            }
            return _this.examOrderIDArray.max + 1;
        },

        /*添加试题题号范围提示*/
        computedOrderRange: function () {
            if (this.selectedAddedTestNum <= 1) {
                return "第" + this.computedOrderID + "题";
            } else {
                return "第" + this.computedOrderID + "题" + " ~ " +
                    "第" + (this.computedOrderID + this.selectedAddedTestNum - 1) + "题";
            }
        }
    }
};

/*Vue组件混合配置项-END-----------------*/


/*========
 添加-选择题
 ========*/
Vue.component("alert-xuanzeti", {
    mixins: [VueMixin_AddQuesOrderConfig],
    template: "#tpl-alert-xuanzeti",
    data: function () {
        return {
            addFrom: '', //何种方式添加试题
            choiceStyle: 1 //默认大写字母 客观题类型：1大写字母 2小写字母 3判断对错
        }
    },
    created: function () {
        var _this = this;
        // 监听添加事件
        Bus.$on("showAlertXuanzeti", function (data) {
            _this.addFrom = data;
            _this.computedOrderID = app.examOrderIDArray.max + 1;//计算题号
        });
    },
    methods: {
        yesBtn: function () {
            var shouldOrderID = app.examOrderIDArray.max + 1;
            var choiceNum = $("#choice-num").val() - 0,
                choiceStyleForm = $("[name=choice-style]:checked").val() - 0,
                optionNum = $("#option-num").val() - 0;

            // 临时保存添加的题目数据
            var addItem = [];

            // 检查用户设置题号
            if (app.checkUserSetOrderID(shouldOrderID)) {
                if ($.isNumeric(app.checkUserSetOrderID(shouldOrderID))) {
                    shouldOrderID = app.checkUserSetOrderID(shouldOrderID)
                }
            }
            for (var i = 0; i < choiceNum; i++) {
                addItem.push({
                    order: shouldOrderID + i,
                    style: choiceStyleForm,
                    small: 0,//默认不包含小题
                    num: optionNum
                });
            }

            // 添加到何处
            var _this = this,
                addFrom = _this.addFrom;
            if (addFrom === 'bar') {
                //来自侧边栏
                var newItem = {
                    title: "选择题",
                    display: 1,
                    type: 0,
                    style: 0,//默认竖版
                    content: addItem
                };
                var partIndex = $(".j_selectPartTo:visible").val() - 0;
                if (app.addToListNewTypeItem(newItem, partIndex)) {
                    app.alert.state_alertXuanzeti = false
                }
            } else {
                //来自页面
                if (app.addToListNewContentItem(addItem, addFrom)) {
                    app.alert.state_alertXuanzeti = false
                }
            }
        },
        closeThisModal: function () {
            app.alert.state_alertXuanzeti = false
        }
    }
});


/*========
 添加-填空题
 ========*/

/*填空题-小题*/
Vue.component("tiankongti-sub", {
    template: "#tpl-tiankongti-sub",
    props: [
        "selectedAddedTestNum",
        "itemIndex",
        "computedOrderID"
    ],
    data: function () {
        return {
            changeTestItemKong: 1
        }
    }
});

/*填空题*/
Vue.component("alert-tiankongti", {
    mixins: [VueMixin_AddQuesOrderConfig],
    template: "#tpl-alert-tiankongti",
    props: ["examPart"],
    data: function () {
        return {
            defValue: {
                title: "填空题"
            }
        }
    },
    created: function () {
        var _this = this;
        // 监听添加事件
        Bus.$on("showAlertTiankongti", function (data) {
            console.log(data);
            _this.addFrom = data;
            _this.computedOrderID = app.examOrderIDArray.max + 1;
        });
    },
    methods: {
        yesBtn: function () {
            var _this = this,
                addFrom = _this.addFrom,
                shouldOrderID = app.examOrderIDArray.max + 1,
                title = $("#lay_tiankongti").find("[role=title]").val(),
                selectedAddedTestFormItem = $("#add_tiankongti_form").find(".fillin-list-nub"),
                addItem = [];// 缓存新添加试题数组

            // 检查用户设置题号
            if (app.checkUserSetOrderID()) {
                if ($.isNumeric(app.checkUserSetOrderID())) {
                    shouldOrderID = app.checkUserSetOrderID()
                }
            }
            // 生成添加数据格式
            selectedAddedTestFormItem.each(function (i) {
                var $this = $(this);
                var itemHline = $this.find("[role=hline]").val() - 0,
                    itemKong = $this.find("[role=kong]").val() - 0,
                    itemHasSubOrder = $this.find("[role=hasSubOrder]").prop("checked"),
                    newItem = {
                        "order": shouldOrderID + i,
                        "small": 0,
                        "uline": 1,
                        'kong': 1,
                        'hline': itemHline,
                        "desc": '',
                        "img": [],
                        "score": 2
                    };
                //无小题
                if (!itemHasSubOrder) {
                    addItem.push(
                        $.extend({}, newItem, {
                            'kong': itemKong
                        }))
                }
                // 有小题
                if (itemHasSubOrder) {
                    for (var j = 0; j < itemKong; j++) {
                        addItem.push(
                            $.extend({}, newItem, {
                                'small': j + 1
                            }))
                    }
                }
            });
            // 添加
            if (addFrom === 'bar') {// 来自添加
                var newType = {
                    'title': title,
                    'display': 1,
                    'type': 1,
                    'content': addItem
                };
                var partIndex = $(".j_selectPartTo:visible").val() - 0;
                if (app.addToListNewTypeItem(newType, partIndex)) {
                    app.alert.state_alertTiankongti = false
                }
            } else {
                if (app.addToListNewContentItem(addItem, addFrom)) {
                    app.alert.state_alertTiankongti = false
                }
            }

        },
        closeThisModal: function () {
            app.alert.state_alertTiankongti = false;
        }
    }
});


/*========
 添加-解答题
 ========*/

// 添加小题组件
Vue.component("jiedati-add-test-item", {
    template: '#tpl-jiedati-add-test-item',
    props: ["addItemContentIndex", "addSubItemContentIndex", "computedOrderID"],
    data: function () {
        return {
            isAddQuesDesc: 0,//是否添加描述
            isAddSubTest: 0,//是否添加小题
            currentSubItemIndex: 0,//当前题号
            maxAddSubNum: 10,//最大添加数量
            selectedAddSubNum: 2 //默认添加数量
        }
    },
    watch: {
        selectedAddSubNum: function (v, o) {
            console.log(v, o);
            if (this.currentSubItemIndex >= v) {
                this.currentSubItemIndex = 0
            }
        }
    },

    methods: {
        //tab切换小题
        tabCurrentSubItem: function (tabid) {
            this.currentSubItemIndex = tabid - 0
        }
    }
});

//小题属性
Vue.component("jiedati-add-sub-test-item", {
    template: '#tpl-jiedati-add-sub-test-item',
    props: ["addItemContentIndex", "addSubItemContentIndex", "computedOrderID"],
    data: function () {
        return {
            isAddQuesImg: 0,//是否添加图片
            isAddQuesDesc: 0,//是否添加描述
            addQuesImgBy: '' //如何添加
        }
    }
});

/*解答题*/
Vue.component("alert-jiedati", {
    mixins: [VueMixin_AddQuesOrderConfig],
    template: "#tpl-alert-jiedati",
    data: function () {
        return {
            defValue: {
                title: "解答题"
            },
            maxAddNum: 10,//最大添加数量
            currentItemIndex: 0 //默认显示的试题标签索引
        }
    },
    watch: {
        "selectedAddedTestNum": function (v, o) {
            if (this.currentItemIndex >= v) {
                this.currentItemIndex = 0
            }
        }
    },
    created: function () {
        var _this = this;

        // 监听解答题弹出框事件
        Bus.$on("showAlertJiedati", function (data) {
            _this.addFrom = data; //弹框来源
            _this.computedOrderID = app.examOrderIDArray.max + 1;//计算题号
        });
    },
    methods: {

        // 解答题tab切换
        tabCurrentItem: function (tabid) {
            this.currentItemIndex = tabid - 0
        },

        //选择添加题数事件
        selectedAddedTestNumEvent: function (e) {
            var $target = e.currentTarget;
            this.currentItemIndex = this.currentItemIndex > $target.value ? $target.value : this.currentItemIndex;
        },

        // 获取试题属性配置 @$Item 试题对象 @hasSub  是否有小题
        getTestItemAttr: function ($Item, hasSub) {
            // ['order'=>11,'small'=>2,'uline'=>1,'kong'=>2,'hline'=>0.3,'score'=>3],
            var $order = $Item.attr("ques-id") - 0;
            var $small = hasSub === 0 ? 0 : $Item.attr("sub-test-id") - 0 + 1;
            var $score = $Item.find("[role=score]").val() - 0;
            var $uline = $Item.find("[role=uline]:checked").val() - 0;
            var $kong = $Item.find("[role=kong]:visible").val() - 0;
            var $hline = $Item.find("[role=hline]").val() - 0;
            // var $desc = $Item.find("[role=desc]:visible").val();
            return {
                order: $order,
                small: $small,
                uline: $uline,
                kong: $kong || 0,
                hline: $hline,
                score: $score,
                desc: '',
                img: []
            };
        },
        yesBtn: function () {
            var _this = this,
                addFrom = _this.addFrom,
                addItem = [],//缓存试题属性
                title = $("#lay_jiedati").find("[role=title]").val(), // 获取表单试题属性
                $jiedatiAddedQuesContent = $("#jiedatiAddedQuesContent"),//要添加的解答题容器
                $jiedatiItem = $jiedatiAddedQuesContent.find("[jiedati-ques-id]");

            // 遍历新试题数据
            $jiedatiItem.each(function () {
                var $this = $(this),
                    $hasSmall = $this.find('[role="has-sub-test"]:checked').val() - 0,
                    $subTestItem = $this.find("[sub-test-id]");

                // 没有小题
                if ($hasSmall == 0) {
                    addItem.push(_this.getTestItemAttr($subTestItem, 0));
                }
                // 有小题
                if ($hasSmall == 1) {
                    $subTestItem.each(function () {
                        var $subThis = $(this);
                        addItem.push(_this.getTestItemAttr($subThis, 1));
                    })
                }
            });

            if (addFrom === 'bar') {// 来自添加新题型
                var newType = {
                    'title': title,
                    'display': 1,
                    'type': 1,
                    'content': addItem
                };
                var partIndex = $(".j_selectPartTo:visible").val() - 0;
                if (app.addToListNewTypeItem(newType, partIndex)) {
                    app.alert.state_alertJiedati = false
                }
            } else {//来自当前试题添加
                if (app.addToListNewContentItem(addItem, addFrom)) {
                    app.alert.state_alertJiedati = false
                }
            }
        },
        closeThisModal: function () {
            app.alert.state_alertJiedati = false
        }
    }
});

/*========
 编辑解答题
 ========*/

// 图片上传组件 Webuploader
jQuery.uploader = {
    uploader: {},
    createUploader: function () {
        return WebUploader.create({
            pick: {
                id: '#filePicker',
                label: '点击选择图片'
            },
            dnd: '#uploader .queueList',
            compress: {
                width: 180,
                height: 180,
                quality: 100
            },
            paste: document.body,
            // 文件类型
            accept: {
                title: 'Images',
                extensions: 'jpg,jpeg,gif,png',
                mimeTypes: 'image/jpg,image/jpeg,image/gif,image/png'
            },
            // swf文件路径
            swf: '__PUBLIC__/plugin/webuploader/js/Uploader.swf',
            disableGlobalDnd: true,
            chunked: true,
            server: '/Home/Index/upload.html?dir=bbs&action=uploadimage',
            fileNumLimit: 1, //文件数量
            fileSizeLimit: 2 * 1024 * 1024,    // 2 M
            fileSingleSizeLimit: 2 * 1024 * 1024    // 2 M
        })
    },
    percentages: {}, // 所有文件的进度信息，key为file id
    state: "pedding",// 可能有pedding, ready, uploading, confirm, done.
    init: function (imgList, uploadSuccess) {

        var _this = this;

        $("#uploader").ready(function () {

            // 实例化
            _this.uploader = _this.createUploader();

            if (!WebUploader.Uploader.support()) {
                alert('Web Uploader 不支持您的浏览器！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
                throw new Error('WebUploader does not support the browser you are using.');
            }

            var $wrap = $('#uploader'), // 图片容器
                $queue = $('<ul class="filelist"></ul>').appendTo($wrap.find('.queueList')),
                $statusBar = $wrap.find('.statusBar'), // 状态栏，包括进度和控制按钮
                $info = $statusBar.find('.info'), // 文件总体选择信息。
                $upload = $wrap.find('.uploadBtn'), // 上传按钮
                $placeHolder = $wrap.find('.placeholder'), // 没选择文件之前的内容。
                $progress = $statusBar.find('.progress').hide(), // 总体进度条
                fileCount = 0, // 添加的文件数量
                fileSize = 0, // 添加的文件总大小
                state = _this.state;


            WebUploader.Uploader.register({
                'before-send-file': 'doSomthingAsync'
            }, {

                doSomthingAsync: function () {
                }
            });


            // 配置显示尺寸
            $("#uploadImgSize").on("change", function () {
                var compressSize = ~~$(this).val();
                _this.uploader.option("compress", {
                    width: compressSize,
                    height: compressSize,
                    quality: 100
                });
            });


            // 添加“添加文件”的按钮，
            _this.uploader.addButton({
                id: '#filePicker2',
                label: '继续添加'
            });

            // 更新进度
            function updateTotalProgress() {
                var loaded = 0,
                    total = 0,
                    spans = $progress.children(),
                    percent;

                $.each(_this.percentages, function (k, v) {
                    total += v[0];
                    loaded += v[0] * v[1];
                });

                percent = total ? loaded / total : 0;

                spans.eq(0).text(Math.round(percent * 100) + '%');
                spans.eq(1).css('width', Math.round(percent * 100) + '%');
                _this.updateStatus(fileCount, fileSize, $info);
            }

            // 设置状态
            function setState(val) {
                var stats;

                if (val === state) {
                    return;
                }

                $upload.removeClass('state-' + state);
                $upload.addClass('state-' + val);
                state = val;

                switch (state) {
                    case 'pedding':
                        $placeHolder.removeClass('element-invisible');
                        $queue.parent().removeClass('filled');
                        $queue.hide();
                        $statusBar.addClass('element-invisible');
                        _this.uploader.refresh();
                        break;

                    case 'ready':
                        $placeHolder.addClass('element-invisible');
                        $('#filePicker2').removeClass('element-invisible');
                        $queue.parent().addClass('filled');
                        $queue.show();
                        $statusBar.removeClass('element-invisible');
                        _this.uploader.refresh();
                        break;

                    case 'uploading':
                        $('#filePicker2').addClass('element-invisible');
                        $progress.show();
                        $upload.text('暂停上传');
                        break;

                    case 'paused':
                        $progress.show();
                        $upload.text('继续上传');
                        break;

                    case 'confirm':
                        $progress.hide();
                        $upload.text('开始上传').addClass('disabled');

                        stats = _this.uploader.getStats();
                        if (stats.successNum && !stats.uploadFailNum) {
                            setState('finish');
                            return;
                        }
                        break;
                    case 'finish':
                        stats = _this.uploader.getStats();
                        if (stats.successNum) {
                            // alert('上传成功');
                        } else {
                            // 没有成功的图片，重设
                            state = 'done';
                            location.reload();
                        }
                        break;
                }

                _this.updateStatus(fileCount, fileSize, $info);
            }

            _this.uploader.onUploadProgress = function (file, percentage) {
                var $li = $('#' + file.id),
                    $percent = $li.find('.progress span');

                $percent.css('width', percentage * 100 + '%');
                _this.percentages[file.id][1] = percentage;
                updateTotalProgress();
            };

            _this.uploader.onFileQueued = function (file) {
                fileCount++;
                fileSize += file.size;

                if (fileCount === 1) {
                    $placeHolder.addClass('element-invisible');
                    $("#uploadImgSize").prop("disabled", true);
                    $statusBar.show();
                }

                var $addFilesHtml = _this.addFiles(file);

                $queue.append($addFilesHtml);
                setState('ready');
                updateTotalProgress();
                $upload.click();

            };

            _this.uploader.onFileDequeued = function (file) {
                fileCount--;
                fileSize -= file.size;

                if (!fileCount) {
                    setState('pedding');
                }
                _this.removeFiles(file);
                updateTotalProgress();

            };

            _this.uploader.on('all', function (type) {
                var stats;
                switch (type) {
                    case 'uploadFinished':
                        setState('confirm');
                        break;

                    case 'startUpload':
                        setState('uploading');
                        break;

                    case 'stopUpload':
                        setState('paused');
                        break;
                }
            });
            // 上传完成 添加新图片路径
            _this.uploader.onUploadSuccess = function (file, res) {
                if (res.state == "SUCCESS") {
                    imgList.push([res.url]);
                    uploadSuccess && uploadSuccess();
                }
            };

            // 错误
            _this.uploader.onError = function (code) {
                switch (code) {
                    case 'Q_EXCEED_NUM_LIMIT':
                        alert("文件数量超过最大值，最多一次添加3个");
                        break;

                    case 'Q_EXCEED_SIZE_LIMIT':
                        alert("文件总大小超过限制，最高为 2M");
                        break;

                    case 'Q_TYPE_DENIED':
                        alert("请选择图片文件，可用文件类型jpg,jpeg,png");
                        break;
                }
            };

            // 上传
            $upload.on('click', function () {
                if ($(this).hasClass('disabled')) {
                    return false;
                }
                if (state === 'ready') {
                    _this.uploader.upload();
                } else if (state === 'paused') {
                    _this.uploader.upload();
                } else if (state === 'uploading') {
                    _this.uploader.stop();
                }
            });

            // 重试
            $info.on('click', '.retry', function () {
                _this.uploader.retry();
            });

            // 忽略
            $info.on('click', '.ignore', function () {
                alert('todo');
            });

            $upload.addClass('state-' + state);
            updateTotalProgress();
        })

    },
    //检查是否支持transition
    isSupportTransition: (function () {
        var s = document.createElement('p').style,
            r = 'transition' in s ||
                'WebkitTransition' in s ||
                'MozTransition' in s ||
                'msTransition' in s ||
                'OTransition' in s;
        s = null;
        return r;
    }()),

    //显示错误信息
    showError: function (code) {
        var text = '';
        switch (code) {
            case 'exceed_size':
                text = '文件大小超出';
                break;

            case 'interrupt':
                text = '上传暂停';
                break;

            default:
                text = '上传失败，请重试';
                break;
        }
        return text;
    },

    // 当有文件添加进来时执行，负责view的创建
    addFiles: function (file) {
        var _this = this,
            ratio = window.devicePixelRatio || 1, // 优化retina, 在retina下这个值是2
            thumbnailWidth = 110 * ratio, // 缩略图大小
            thumbnailHeight = 110 * ratio;

        var $li = $('<li id="' + file.id + '">' +
                '<p class="title">' + file.name + '</p>' +
                '<p class="imgWrap"></p>' +
                '<p class="progress"><span></span></p>' +
                '</li>'),

            $btns = $('<div class="file-panel">' +
                '<span class="cancel">删除</span>' +
                '<span class="rotateRight">向右旋转</span>' +
                '<span class="rotateLeft">向左旋转</span></div>').appendTo($li),
            $prgress = $li.find('p.progress span'),
            $wrap = $li.find('p.imgWrap'),
            $info = $('<p class="error"></p>');

        if (file.getStatus() === 'invalid') {
            $info.text(_this.showError(file.statusText)).appendTo($li);
        } else {

            // @todo lazyload
            $wrap.text('预览中');
            _this.uploader.makeThumb(file, function (error, src) {
                if (error) {
                    $wrap.text('不能预览');
                    return;
                }

                var img = $('<img src="' + src + '">');
                $wrap.empty().append(img);
            }, thumbnailWidth, thumbnailHeight);

            _this.percentages[file.id] = [file.size, 0];
            file.rotation = 0;
        }

        file.on('statuschange', function (cur, prev) {
            if (prev === 'progress') {
                $prgress.hide().width(0);
            } else if (prev === 'queued') {
                $li.off('mouseenter mouseleave');
                $btns.remove();
            }
            // 成功
            if (cur === 'error' || cur === 'invalid') {
                $info.text(_this.showError(file.statusText)).appendTo($li);
                _this.percentages[file.id][1] = 1;
            } else if (cur === 'interrupt') {
                $info.text(_this.showError('interrupt')).appendTo($li);
            } else if (cur === 'queued') {
                _this.percentages[file.id][1] = 0;
            } else if (cur === 'progress') {
                $info.remove();
                $prgress.css('display', 'block');
            } else if (cur === 'complete') {
                $li.append('<span class="success"></span>');
            }

            $li.removeClass('state-' + prev).addClass('state-' + cur);
        });

        $li.on('mouseenter', function () {
            $btns.stop().animate({height: 30});
        });

        $li.on('mouseleave', function () {
            $btns.stop().animate({height: 0});
        });

        $btns.on('click', 'span', function () {
            var index = $(this).index(),
                deg;

            switch (index) {
                case 0:
                    _this.uploader.removeFile(file);
                    return;

                case 1:
                    file.rotation += 90;
                    break;

                case 2:
                    file.rotation -= 90;
                    break;
            }
            if (_this.isSupportTransition) {
                deg = 'rotate(' + file.rotation + 'deg)';
                $wrap.css({
                    '-webkit-transform': deg,
                    '-mos-transform': deg,
                    '-o-transform': deg,
                    'transform': deg
                });
            } else {
                $wrap.css('filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation=' + (~~((file.rotation / 90) % 4 + 4) % 4) + ')');
            }
        });

        return $li;
    },

    // 负责view的销毁
    removeFiles: function (file) {
        delete this.percentages[file.id];
        $('#' + file.id).off().find('.file-panel').off().end().remove();
    },
    updateStatus: function (fileCount, fileSize, $info) {

        var text = '', stats, _this = this;

        if (_this.state === 'ready') {
            text = '选中' + fileCount + '张图片，共' + WebUploader.formatSize(fileSize) + '。';
        }
        else if (_this.state === 'confirm') {
            stats = _this.uploader.getStats();
            if (stats.uploadFailNum) {
                text = '已成功上传' + stats.successNum + '张图片，' +
                    stats.uploadFailNum + '张图片上传失败，<a class="retry" href="#">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>'
            }
        }
        else {
            stats = _this.uploader.getStats();
            text = '共' + fileCount + '张（' +
                WebUploader.formatSize(fileSize) +
                '），已上传' + stats.successNum + '张';

            if (stats.uploadFailNum) {
                text += '，失败' + stats.uploadFailNum + '张';
            }
        }

        $info.html(text);

    }
};

Vue.component("alert-edit-jiedati", {
    template: "#tpl-alert-edit-jiedati",
    props: ["modal"],
    data: function () {
        return {
            editWhich: '',//编辑哪道试题
            editDataCache: '',
            editData: {
                "order": '', "small": '', "uline": '', 'kong': '', 'hline': '', "score": '', 'desc': '',
                img: []
            }, //声明要编辑的试题数据
            addedImgList: []
        }
    },
    computed: {
        computedEditData: function () {
            return this.editDataCache;
        },
        /*短文改错题*/
        isENError: function () {
            return this.editDataCache.kong == 0 && this.editDataCache.hline == 0;
        }
    },
    created: function () {
        var _this = this;
        Bus.$on("showAlertEditJiedati", function (data) {
            _this.editWhich = data;
            console.log(data);

            // 解除数据绑定
            var saveQuesData = JSON.parse(JSON.stringify(_this.handleOldData(_this.editWhich)));

            // 合并数据
            _this.editDataCache = $.extend({}, _this.editData, saveQuesData);
        });
    },

    methods: {

        // 获取原数据
        handleOldData: function (p, changeData) {
            // 如果有第二个参数修改数据
            if (changeData) {
                app.data.paper[p.pid].list[p.tid].content.splice(p.oid, 1, changeData);
                app.dataState++;
                return;
            }
            return app.data.paper[p.pid].list[p.tid].content[p.oid];
        },
        yesBtn: function () {
            this.$forceUpdate();//组件数据初始化
            var _this = this;
            if (_this.isENError) {
                _this.computedEditData.desc = $(".J_content-edit").html()
            }

            _this.handleOldData(_this.editWhich, _this.computedEditData);
            app.alert.state_alertEditJiedati = false;
        },
        closeThisModal: function () {
            this.$forceUpdate();//组件数据初始化
            app.alert.state_alertEditJiedati = false
        }
    }
});

/*添加图片*/
Vue.component("add-img-comp", {
    template: "#tpl-add-img-comp",
    props: ["computedEditData"],
    data: function () {
        return {
            // editWhich: '',
            // editDataCache: '',
            // editData: {
            //     "order": '', "small": '', "uline": '', 'kong': '', 'hline': '', "score": '', 'desc': '',
            //     img: []
            // },
            //声明要编辑的试题数据
            // isAddQuesDesc: 0,
            addedImgList: [],
            isAddQuesImg: 0
            // addQuesImgBy: ''
        }
    },
    computed:{
        isAddQuesImg1:function () {
            var computedData = this.computedEditData;
            this.isAddQuesImg = (computedData.img && computedData.img.length>0) ? 1 : 0
        }
    },
    methods: {

        //根据试题序号获取试题ID题库
        tryGetTestID: function (order) {
            var id = null;
            try {
                id = editData.getTestIDByOrder(order)
            }
            catch (e) {
                $.console.warn(e);
                id = editData.getTestIDByOrder()
            }
            return id;
        },

        // 添加图片到缓存
        pushImgToCache: function (List) {
            var _this = this;
            var computedEditData = _this.computedEditData;
            if (computedEditData.img && computedEditData.img !== undefined) {
                if ($.isArray(computedEditData.img)) {
                    _this.computedEditData.img = computedEditData.img.concat(List);//添加图片到缓存数据
                } else {
                    return false;
                }
            } else {
                _this.computedEditData.img = List;//添加图片到缓存数据
            }
        },

        // 选用试题图片
        selectServerImage: function () {
            var _this = this;
            _this.addedImgList = [];// 缓存图片

            // 获取题库图片参数
            var thisOrder = 0;

            var params = {
                testID: _this.tryGetTestID(thisOrder)
            };

            var layerLoading = layer.load(2);
            $.get(U("/Home/Index/testImageChoose"), params, function (data) {
                layer.close(layerLoading);
                if (data.status === 1 && data.data && data.data.length > 0) {

                    // var imgList_demo = [
                    //     "/Public/default/image/answer/pic.jpg",
                    //     "/Public/default/image/answer/pic.jpg",
                    //     "/Public/default/image/answer/pic.jpg"
                    // ];

                    //弹出选择框
                    var layerindex = layer.open({
                        type: 0,
                        shift: 0,
                        area: ["700px", "500px"],
                        title: "请选择图片",
                        btn: ["确定", "取消"],
                        content: _this.outputImgList(data.data),//得到图片列表
                        yes: function () {
                            var imgSrc = $("#layer_serverImgList").find("li.selected").find("img").attr("src");
                            _this.addedImgList.push([imgSrc]);
                            _this.pushImgToCache(_this.addedImgList);
                            layer.close(layerindex);
                        },
                        success: function () {

                            //为图片绑定选择事件
                            $("#layer_serverImgList").on("click", "li", function () {
                                var $this = $(this);
                                $this.addClass("selected").siblings("li").removeClass("selected");
                            });
                        }
                    });
                } else {
                    layer.alert("抱歉，未找到相关试题图片！ <br>请使用其他添加方式！", {icon: 0})
                }
            });
        },

        // 将数据生成图片列表
        outputImgList: function (data) {
            var tmp = "<ul id='layer_serverImgList'>";
            for (var i = 0; i < data.length; i++) {
                tmp += "<li><div class='img-box'><img src=" + data[i] + "></div>";
            }
            tmp += "</ul>";
            return tmp;
        },

        //图片上传
        uploadThisImage: function () {
            var _this = this;
            _this.addedImgList = [];// 缓存图片
            var uploaderHtml = '<div id="uploader" class="wu-example">\
                                <div class="g-form" style="padding:20px 15px 0;">\
                                    最大显示尺寸：<select id="uploadImgSize">\
                                        <option value="100">100 * 100</option>\
                                        <option value="140">140 * 140</option>\
                                        <option value="180" selected>180 * 180</option>\
                                        <option value="220">220 * 220</option>\
                                        <option value="260">260 * 260</option>\
                                    </select>\
                                </div>\
                                <div class="queueList">\
                                    <div id="dndArea" class="placeholder">\
                                    <div id="filePicker"></div>\
                                    <p>或将照片拖到这里，单次最多可选1张</p>\
                                    </div>\
                                </div>\
                                <div class="statusBar" style="display:none;">\
                                        <div class="progress">\
                                        <span class="text">0%</span>\
                                        <span class="percentage"></span>\
                                        </div><div class="info"></div>\
                                        <div class="btns">\
                                        <div id="filePicker2"></div><div class="uploadBtn">开始上传</div>\
                                        </div>\
                                </div>\
                                <div id="cropper-wraper" style="display: none">\
                                <div id="cropper-img" style="width:100%;height:300px;border:2px solid #ddd;">\
                                    <img src="" alt="" />\
                                    </div>\
                                    <div class="clearfix">\
                                        <div id="img-preview" class="left" style="width:160px;height:160px;border:2px solid #ddd;margin-top:10px;"></div>\
                                    </div>\
                                </div>\
                            </div>';
            var layerUpImg = layer.open({
                type: 0,
                title: false,
                shift: -1,
                area: ["700px", "auto"],
                btn: ["确定", "取消"],
                content: uploaderHtml,
                success: function (layero) {
                    layero.find(".layui-layer-btn").css({
                        "text-align": "center",
                        "opacity": 0,
                        "padding-bottom": "20px"
                    });

                    // webuploaderReady
                    jQuery.uploader.init(_this.addedImgList, function () {
                        _this.pushImgToCache(_this.addedImgList);
                        app.updateDom();
                        layer.close(layerUpImg);
                        layer.msg("上传成功");
                    });
                }
            });
        },

        // 删除缓存图片
        delThisQuesImg: function (iid) {
            var _this = this;
            _this.computedEditData.img.splice(iid, 1);
            layer.msg('已删除！', {time: 1000});
        }
    }

});

Vue.component("add-desc-comp", {
    template: "#tpl-add-desc-comp",
    props: ["computedEditData"]
});

/*添加选答题*/
Vue.component("alert-xuandati", {
    mixins: [VueMixin_AddQuesOrderConfig],
    template: "#tpl-alert-xuandati",
    data: function () {
        return {
            defValue: {
                title: "选答题"
            },
            selectedAddedTestNum: 2//默认添加2道
        }
    },
    created: function () {
        var _this = this;
        Bus.$on("showAlertXuandati", function (data) {
            _this.addFrom = data;
            _this.computedOrderID = app.examOrderIDArray.max + 1;//计算题号
        });
    },
    methods: {
        yesBtn: function () {
            var _this = this,
                addFrom = _this.addFrom;
            // 获取表单试题属性
            var $xuandati = $("#lay_xuandati"),
                title = $xuandati.find("[role=title]").val(),
                $quesDo = $xuandati.find("[role=ques-do]").val() - 0,
                $hline = $xuandati.find("[role=hline]").val() - 0,
                $score = $xuandati.find("[role=score]").val() - 0 || 10;

            // 缓存新试题
            var addItem = [];
            for (var o = 0; o < this.selectedAddedTestNum; o++) {
                addItem.push({
                    order: this.computedOrderID + o,
                    small: 0,
                    uline: 0,
                    kong: 0,
                    desc: "",
                    img: [],
                    hline: $hline,
                    score: $score
                });
            }

            if (addFrom === 'bar') {// 来自添加
                var newType = {
                    'title': title,
                    'display': 1,
                    'type': 2,
                    'do': $quesDo,
                    'content': addItem
                };
                var partIndex = $(".j_selectPartTo:visible").val() - 0;
                if (app.addToListNewTypeItem(newType, partIndex)) {
                    app.alert.state_alertXuandati = false
                }
            } else {
                if (app.addToListNewContentItem(addItem, addFrom)) {
                    app.alert.state_alertXuandati = false
                }
            }
        },
        closeThisModal: function () {
            app.alert.state_alertXuandati = false
        }
    }
});

/*编辑选答题*/
Vue.component("alert-edit-xuandati", {
    props: ["modal"],
    template: "#tpl-alert-edit-xuandati",
    data: function () {
        return {
            editWhich: {},
            defValue: {
                title: "选答题"
            },
            editOrderID: 0, // 选择要编辑的试题
            editContent: "",
            editOrderArray: []//要编辑的题号汇总
        }
    },
    created: function () {
        var _this = this;
        Bus.$on("showAlertEditXuandati", function (pid, tid) {
            _this.editWhich.pid = pid;
            _this.editWhich.tid = tid;
            _this.editContent = _this.handleOldData(_this.editWhich);
            var orderArray = this.editOrderArray;
            if (typeof orderArray === "object" && orderArray.length > 0) {
                _this.editOrderID = this.editOrderArray[0]
            }
        });
    },
    computed: {
        // 试题题号数组
        editOrderArray: function () {
            var _this = this,
                newArray = [];
            if (_this.editContent != "") {
                $.each(_this.editContent, function (i, item) {
                    newArray.push(item.order);
                });
                return newArray;
            }
        },
        computedEditData: function () {
            return this.getComputedEditData();
        }
    },
    methods: {

        // 获取原数据
        handleOldData: function (p) {
            return JSON.parse(JSON.stringify(app.data.paper[p.pid].list[p.tid].content));
        },

        // 获取计算数据 写在methods方便切换题号是更新数据
        getComputedEditData: function () {
            var _this = this;
            var orderID = _this.editOrderID;
            if (orderID != "" && _this.editContent != "") {
                var filtered = _this.editContent.filter(function (el, i) {
                    return el["order"] == orderID;
                });
                if (filtered.length > 0) {
                    return filtered[0]
                }
            }
            return {};
        },

        // 切换题号
        changeQuesOrderID: function () {
            this.computedEditData = this.getComputedEditData();
        },
        yesBtn: function () {
            var _this = this,
                editWhich = _this.editWhich;

            // 获取表单试题属性
            var $xuandati = $("#lay_edit_xuandati"),
                title = $xuandati.find("[role=title]").val(),
                $order = $xuandati.find("[role=order]").find("option:selected").data("index"),
                $hline = $xuandati.find("[role=hline]").val() - 0,
                $score = $xuandati.find("[role=score]").val() - 0;
            app.data.paper[editWhich.pid].list[editWhich.tid].content[$order].hline = $hline;
            app.data.paper[editWhich.pid].list[editWhich.tid].content[$order].score = $score;
            app.data.paper[editWhich.pid].list[editWhich.tid].content[$order].img = this.computedEditData.img;
            app.data.paper[editWhich.pid].list[editWhich.tid].content[$order].desc = this.computedEditData.desc;
            app.alert.state_alertEditXuandati = false;
            app.updateDom();
        },
        closeThisModal: function () {
            app.alert.state_alertEditXuandati = false
        }
    }
});


/*========
 添加-语文作文
 ========*/

Vue.component("alert-cn-zuowen", {
    mixins: [VueMixin_AddQuesOrderConfig],
    template: "#tpl-alert-cn-zuowen",
    data: function () {
        return {
            isEdit: 0,
            defValue: {
                title: "作文",
                char: 800
            },
            addFor: {},
            editDataCache: '',
            editData: {
                "order": '', 'char': '', "score": '', 'desc': '',
                "img": []
            } //声明要编辑的试题数据
        }

    },
    created: function () {
        var _this = this;
        Bus.$on("showAlertEditCnZuowen", function (pid, tid) {
            _this.isEdit = 1;
            _this.addFor.pid = pid;
            _this.addFor.tid = tid;

            // 合并数据
            _this.editDataCache = $.extend({}, _this.editData, JSON.parse(JSON.stringify(_this.handleOldData(_this.addFor))));
        });
        Bus.$on("showAlertCnZuowen", function (data) {
            _this.isEdit = 0;
            _this.computedOrderID = app.examOrderIDArray.max + 1;
        })
    },
    computed: {
        computedEditData: function () {
            return this.editDataCache;
        }
    },
    methods: {

        // 获取原数据
        handleOldData: function (p) {
            return app.data.paper[p.pid].list[p.tid].content[0];
        },
        yesBtn: function () {
            var _this = this,
                pid = _this.addFor.pid,
                tid = _this.addFor.tid;
            var $zuowenForm = $("#CN_zuowenForm"),
                title = $zuowenForm.find("[name=cn-zuowen-title]").val(),
                char = $zuowenForm.find("[name=cn-zuowen-char]").val() - 0,
                partIndex = $(".j_selectPartTo:visible").val() - 0;
            // _this.defValue.title = title;
            // _this.defValue.char = char;

            /*添加*/
            if (_this.isEdit == 0) {
                var newOrder = _this.computedOrderID;
                var newType = {
                    'title': title,
                    'display': 1,
                    'type': 3,
                    'content': [
                        {'order': newOrder, 'char': char, 'score': 60, 'desc': '', "img": []}
                    ]
                };
                app.data.paper[partIndex].list.push(newType);
            }

            /*编辑*/
            if (_this.isEdit == 1) {
                app.data.paper[pid].list[tid].title = title;
                app.data.paper[pid].list[tid].content[0].char = char;
                app.data.paper[pid].list[tid].content[0].img = _this.computedEditData.img;
                app.data.paper[pid].list[tid].content[0].desc = _this.computedEditData.desc;
            }
            app.updateDom();
            app.alert.state_alertCnZuowen = false
        },
        closeThisModal: function () {
            app.alert.state_alertCnZuowen = false
        }
    }
});

/*========
 添加-英语作文
 ========*/

Vue.component("alert-en-zuowen", {
    mixins: [VueMixin_AddQuesOrderConfig],
    template: "#tpl-alert-en-zuowen",
    data: function () {
        return {
            isEdit: 0,
            defValue: {
                title: "书面表达",
                hline: 10
            },
            addFor: {},
            editDataCache: '',
            editData: {
                "order": '', 'uline': 1, 'hline': 10, 'score': 20, 'desc': '',
                "img": []
            } //声明要编辑的试题数据
        }
    },
    created: function () {
        var _this = this;
        Bus.$on("showAlertEditEnZuowen", function (pid, tid) {
            _this.addFor.pid = pid;
            _this.addFor.tid = tid;
            _this.isEdit = 1;
            // 合并数据
            _this.editDataCache = $.extend({}, _this.editData, JSON.parse(JSON.stringify(_this.handleOldData(_this.addFor))));
        });
        Bus.$on("showAlertEnZuowen", function (data) {
            _this.isEdit = 0;
            _this.computedOrderID = app.examOrderIDArray.max + 1;
        })
    },
    computed: {
        computedEditData: function () {
            return this.editDataCache;
        }
    },
    methods: {

        // 获取原数据
        handleOldData: function (p) {
            return app.data.paper[p.pid].list[p.tid].content[0];
        },
        yesBtn: function () {
            var _this = this,
                pid = _this.addFor.pid,
                tid = _this.addFor.tid;
            var $zuowenForm = $("#EN_zuowenForm"),
                title = $zuowenForm.find("[name=en-zuowen-title]").val(),
                hline = $zuowenForm.find("[name=en-zuowen-hline]").val() - 0;
            _this.defValue.title = title;
            _this.defValue.hline = hline;
            // 来自添加
            if (_this.isEdit == 0) {
                var newOrder = _this.computedOrderID;
                var newType = {
                    'title': title,
                    'display': 1,
                    'type': 4,
                    'content': [
                        {'order': newOrder, 'uline': 1, 'hline': hline, 'score': 20, 'desc': ''}
                    ]
                };
                var partLen = app.data.paper.length;
                app.data.paper[partLen - 1].list.push(newType);
            }
            // 来自编辑
            if (_this.isEdit == 1) {
                app.data.paper[pid].list[tid].title = title;
                app.data.paper[pid].list[tid].content[0].hline = hline;
                app.data.paper[pid].list[tid].content[0].img = _this.computedEditData.img;
                app.data.paper[pid].list[tid].content[0].desc = _this.computedEditData.desc;
            }
            app.updateDom();
            app.alert.state_alertEnZuowen = false
        },
        closeThisModal: function () {
            app.alert.state_alertEnZuowen = false
        }
    }
});;
    //数据
    var _vue_data = {
        computedPage:'',
        dataState: 0,
        /*====================辅助数据定义========================*/
        alert: {
            state_alertTitle: false,
            state_alertEditQuesTitle: false,
            state_alertExamInfo: false,
            state_alertTiankongti: false,
            state_alertXuanzeti: false,
            state_alertJiedati: false,
            state_alertEditJiedati: false,
            state_alertXuandati: false,
            state_alertEditXuandati: false,
            state_alertEnZuowen: false,
            state_alertCnZuowen: false,
            state_alertPartInfo: false
        },
        examOrderIDArray: {
            order: [],
            outOrder: [],
            max: 0,
            min: 0
        },
        answerType:'',
        data: {
            /*====================标准格式=====================*/
            top: {//顶部信息
                display: 1,
                content: "姓名____________班级____________考号____________座位号____________"
            },
            title: {//标题
                display: 1,
                content: "[标题]XX县高级中学高二下学期月考"
            },
            sub: { //副标题
                display: 1,
                content: "[副标题]XX答题卡"
            },
            layout: {//版式
                style: "A4"
            },
            type: {
                display: 1 //顶部类型（显示类型，顶部区域有N种固定类型）
            },
            code: {//条形码
                display: 1
            },
            num: {//准考证号
                display: 1,
                length: 10
            },
            care: { //注意事项
                display: 1,
                content: "1．答题前，请将姓名、班级、考场、座位号、准考证号填写清楚。\n" +
                "2．客观题答题，必须使用2B铅笔填涂，修改时用橡皮擦干净。\n" +
                "3．主观题使用黑色签字笔书写。\n" +
                "4．必须在题号所对应的答题区域内作答，超出答题区域书写无效。"
            },
            miss: {
                display: 1//缺考标记
            },
            score: {
                display: 1//是否显示分值
            },
            paper: [
                {
                    title: "第Ⅰ卷",
                    desc: '（请用2B铅笔填涂）', //描述
                    list: [{"title":"解答题","display":1,"type":1,"content":[{"order":1,"small":0,"uline":0,"kong":1,"hline":1,"score":1},{"order":2,"small":0,"uline":0,"kong":0,"hline":1,"score":1},{"order":3,"small":0,"uline":0,"kong":0,"hline":1,"score":1},{"order":4,"small":1,"uline":1,"kong":1,"hline":1,"score":1},{"order":4,"small":2,"uline":1,"kong":0,"hline":1,"score":1},{"order":5,"small":0,"uline":0,"kong":0,"hline":1,"score":1},{"order":6,"small":0,"uline":0,"kong":0,"hline":1,"score":1}]}]
                }, {
                    title: "第Ⅱ卷 ",
                    desc: '(请用0.5毫米黑色签字笔作答)', //描述
                    list: []
                }
            ]
        }
    };

    // 项目入口
    window.app = new Vue({
        el: "#app",//模版入口
        template: "#tpl-main",//模版
        data: _vue_data,//数据
        //计算属性
        computed: {
            // 获取答题卡类型
            "answerType":function(){
                return parseInt((editData.getAttr(2) || 0)) || 1;
            },
            // 答题卡头部信息
            "title": function () {
                var _this = this;
                return {top: _this.data.top, title: _this.data.title, sub: _this.data.sub}
            }
        },

        watch: {
            //dataState 数据状态标记，数据更改时执行 函数【setCardStorage】，自动更新 本地缓存数据【storageDATA】
            dataState: "setCardStorage"
        },
        // 项目实例创建完成过后执行
        created: function () {
            // 显示页面
            $("#answer-content").fadeIn();
            // 联考配置流程
            jQuery.myRepeat.examProcess('#left-con',2);
        },
        // html生成后执行
        mounted: function () {
            var _this = this;
            _this.updateCurrCardOrderIDArray();
            _this.getSavedData();
            _this.windowCloseAlertInfo();
        },

        // 程序入口方法
        methods: {

            //关闭网页提示信息
            windowCloseAlertInfo:function () {
                window.onbeforeunload = function(){
                    return false;
                };
            },

            // 缓存试题数据
            setCardStorage: function () {
                if (window.localStorage) {
                    var localData = {subjectID: Cookie.Get("SubjectId"), id: editData.getAttr(1), data: JSON.parse(JSON.stringify(this.data))};
                    localStorage.setItem("cardDataStorage", JSON.stringify(localData));
                }
                var _this = this;
                setTimeout(function(){
                    _this.computedPage = _this.computedPageHtml();
                },100);
                console.log('data updated!')
            },

            // 添加新试题
            addToListNewTypeItem: function (newType, index) {
                var _this = this;
                _this.data.paper[index].list.push(newType);
                layer.msg("添加成功！", {time: 1500});
                _this.updateDom();
                return true;
            },

            // 添加更多试题
            addToListNewContentItem: function (newItem, from) {
                var _this = this;
                var toPart = from.part,
                    toBlock = from.block,
                    content = _this.data.paper[toPart].list[toBlock].content;
                _this.data.paper[toPart].list[toBlock].content = content.concat(newItem);
                layer.msg("添加成功！", {time: 1500});
                _this.updateDom();
                return true;
            },

            /*移动*/
            quesTypeMove: function (pid, tid, num) {
                var _this = this;
                var spliceItem = _this.data.paper[pid].list.splice(tid, 1);
                _this.data.paper[pid].list.splice(tid + num, 0, spliceItem[0]);
            },

            /*移动*/
            quesItemMove: function (pid, tid, oid, num) {
                var _this = this;
                var spliceItem = _this.data.paper[pid].list[tid].content.splice(oid, 1);
                _this.data.paper[pid].list[tid].content.splice(oid + num, 0, spliceItem[0]);
            },

            /*上移*/
            moveUp: function (pid, tid) {
                this.quesTypeMove(pid, tid, -1);
                layer.msg("上移");
            },

            /*下移*/
            moveDown: function (pid, tid) {
                this.quesTypeMove(pid, tid, 1);
                layer.msg("下移")
            },

            /*item上移*/
            itemMoveUp: function (pid, tid, oid) {
                this.quesItemMove(pid, tid, oid, -1);
                layer.msg("上移");
            },

            /*item下移*/
            ItemMoveDown: function (pid, tid, oid) {
                this.quesItemMove(pid, tid, oid, 1);
                layer.msg("下移")
            },

            /**删除试题-自定义
             * param @pid number 分卷
             * param @tid number 大题
             * param @oid number orderID [可选]
             * */
            delQuesType: function (pid, tid, oid ) {
                var _this = this;
                layer.confirm("确定删除该题？", {icon: 3, title: '提示'},
                    function () {

                        // orderID 不存在
                        if(oid == null){

                            _this.data.paper[pid].list.splice(tid, 1);
                        }else{

                            //单题删除
                            _this.data.paper[pid].list[tid].content.splice(oid, 1);

                            // 判断是否要清空题型type
                            if(_this.data.paper[pid].list[tid].content.length===0){
                                _this.data.paper[pid].list.splice(tid, 1)
                            }
                        }
                        layer.msg('删除成功！', {icon: 1, time: 1000});
                        _this.updateDom();
                    })
            },

            /*删除试题-全部*/
            delAllQues: function (pid) {
                var _this = this;
                var partname = "第"+((pid+1)===2?'II':'I')+"卷";
                layer.confirm("确定删除 ["+partname+"] 所有试题？", {icon: 3, title: '提示'},
                    function () {
                        _this.data.paper[pid].list = [];
                        layer.msg('删除成功！', {icon: 1, time: 1000});
                        _this.updateDom();
                    })
            },
            /*更新dom 获取已添加试题题号时使用*/
            updateDom: function () {
                var _this = this;

                //更新dom时同时更新本地缓存数据
                _this.dataState += 1;

                // html更新后调用
                _this.$nextTick(function () {
                    _this.updateCurrCardOrderIDArray();
                    _this.computedPage = _this.computedPageHtml();
                });
            },
            computedPageHtml: function () {
                var pageStyle = this.data.layout.style;
                var pages = ($(".con-height").height() / 994) -0.15; //误差;
                var pagesCeil = Math.ceil(pages);
                var stylePageNum = this.data.layout.style === "A4" ? 2 : 6;
                return "预计打印" + Math.ceil(pagesCeil / stylePageNum) + "张  " +
                    "<small title='"+(Math.ceil(pages*100)/100)+"'>(" + pageStyle + " 共" + pagesCeil + "页)</small>";
            },
            // 检查用户设置题号
            checkUserSetOrderID: function () {
                var _this = this;
                // 如果是用户设置题号
                var $SetOrderIpt = $("#userSetOrderID");
                // 当前设置项为显示状态时
                if ($SetOrderIpt.filter(":visible").length > 0) {
                    // 题号存在
                    if ($.inArray($SetOrderIpt.val() - 0, _this.examOrderIDArray.order) != -1) {
                        layer.msg("题号 " + $SetOrderIpt.val() + " 不可用，请更换 ！");
                        return false;
                    }
                    // 题号小于0
                    if ($SetOrderIpt.val() - 0 <= 0) {
                        return false;
                    }
                    // 题号可用
                    if ($.inArray($SetOrderIpt.val() - 0, _this.examOrderIDArray.order) == -1) {
                        return $SetOrderIpt.val() - 0;
                    }
                    return true;
                }
            },

            // 更新已添加试题题号
            updateCurrCardOrderIDArray: function () {
                // 初始化试题序号，检查试题序号是否重复
                var _this = this;
                var $quesID = $("[card-ques-id]");//题号id
                console.log($quesID.length, '$quesID');
                var arrProto = []; //原始序号数据
                var arrInt = [];
                // 当前有试题
                if ($quesID.length > 0) {
                    $quesID.each(function () {
                        var $this = $(this),
                            item = $this.attr("card-ques-id");
                        arrProto.push(item - 0);
                        arrInt.push(parseInt(item));
                    });
                    // 当前无试题
                } else {
                    _this.examOrderIDArray.min = 0;
                    _this.examOrderIDArray.max = 0;
                    _this.examOrderIDArray.order = [];
                    _this.examOrderIDArray.outOrder = [];
                    layer.msg("当前没有试题哦！")
                }
                // 获取到试题题号后
                if (arrInt.length > 0) {
                    var savedArr = arrProto;
                    // 数组排重排序
                    arrInt = arrInt.unique().sort(function (a, b) {
                        return a - b
                    });
                    // 题号中不存在的值
                    var outArr = [];
                    for (var i = 1; i < _this.examOrderIDArray.max; i++) {
                        if ($.inArray(i, arrInt) == -1) {
                            outArr.push(i);
                        }
                    }
                    _this.examOrderIDArray.outOrder = outArr;
                    _this.examOrderIDArray.min = arrInt.slice(0, 1)[0];
                    _this.examOrderIDArray.max = arrInt.slice(-1)[0];
                    _this.examOrderIDArray.order = arrInt;

                    console.log("已添加：" + arrInt.length);

                    // 找出重复的题号
                    savedArr = savedArr.unique().sort(function (a, b) {
                        return a - b
                    });
                    /*无小题题号 与 有小题题号相同*/
                    var sameNumArr = [];
                    for (var i = 0; i < savedArr.length; i++) {
                        if (savedArr[i] === Math.floor(savedArr[i + 1])) {
                            sameNumArr.push(savedArr[i]);
                        }
                    }
                    /*题号相同 且 小题相同*/
                    var sameNumArr1 = [];
                    for (var i = 0; i < arrProto.length; i++) {
                        if (arrProto[i] === arrProto[i + 1]) {
                            sameNumArr1.push(parseInt(arrProto[i]));
                        }
                    }
                    // 判断是否重复
                    if (sameNumArr.length > 0 || sameNumArr1.length > 0) {
                        var sameOrderTips = JSON.stringify([].concat(sameNumArr, sameNumArr1).unique());
                        layer.alert("当前试题中：序号<span class='text-primary'> " + sameOrderTips + " </span>有重复，请检查！", {icon: 0})
                    }
                }
            },

            // 检查数据合法性
            _checkForTypeKey: function(type,item){
                var inLength = 0;//检查项目数量统计
                for(var m=0;m<type.length;m++){
                    if(type[m] in item && item.hasOwnProperty(type[m])){
                        inLength++;
                    }else{
                        $.console.warn("题号："+item.order+" 数据验证未通过！"+type[m]+"为必须字段")
                    }
                }
                return inLength === type.length ? true : false
            },

            // 检查数据方法
            _checkAnswerJson:function (data){
                // 检查项目
                var checkType = [
                    ['style', 'order', 'small', 'num'],
                    ["order", "small", "uline", 'kong', 'hline', "score"],
                    ["order", "small", "uline", 'kong', 'hline', "score"],
                    ['order', 'char', 'score'],
                    ['order', 'uline', 'hline', 'score']
                ];

                var _dataPaper = data.paper;

                // JSON.parse(JSON.stringify(data.paper));//缓存答题卡数据

                console.log(_dataPaper);

                var isPassCheck = true;//默认检查通过
                $.each(_dataPaper,function(i){//检查答题卡数据，通过返回true，否则返回false
                    var list = _dataPaper[i].list;
                    $.each(list,function(j){
                        var quesType = list[j].type-0;//缓存题型
                        var content = list[j].content;//缓存试题列表
                        $.each(content,function(k){
                            var contentItem = content[k];
                            isPassCheck = app._checkForTypeKey(checkType[quesType],contentItem);//调用检查数据方法

                            //修正hline
                            if("hline" in contentItem && contentItem.hline > 1 && contentItem.hline != parseInt(contentItem.hline)){
                                var cacheHline = contentItem.hline;
                                contentItem.hline = parseInt(cacheHline);//修正字段数值
                                $.console.warn("题号:"+contentItem.order + ",type="+quesType+"; hline"+cacheHline+" 为大于1非整数;"+"已修正为:"+contentItem.hline);
                            }

                            if(quesType == 4){//英语作文题型
                                if(contentItem.hline < 1){
                                    contentItem.hline = 10;//修正字段数值
                                    $.console.warn("type="+quesType+"; hline 不可以小于0;"+"已修正为10");
                                }
                            }
                        })
                    })
                });
                return isPassCheck;
            },

            /*@params
             * @params content string html
             * @params btnGroup array ['btn1','btn2']
             * @params fn1 btn1 执行函数
             * @params fn2 btn2 执行函数
             * @*/
            layerMsg:function(content,btnGroup,fn1,fn2){
                var fn2 = fn2 || function(){localStorage.setItem("cardDataStorage", null)};
                var layIndex = layer.open({
                    type:0,
                    icon:0,
                    content:content,
                    btn:btnGroup,
                    btn1:function(){
                        fn1();
                        layer.close(layIndex)
                    },
                    btn2:function(){
                        fn2();
                        layer.close(layIndex)
                    }
                })
            },

            // 获取保存的答题卡id
            tryGetSavedData:function () {
                var id = 0;
              try{
                  id = editData.getAttr(1);
              }  catch(e){
                  $.console.warn(e);
              }
              return id;
            },

            //获取保存的数据（本地缓存的，服务器自动生成的，已保存的）
            getSavedData:function(){
                var _this = this;
                var params = {
                    CookieStr:editData.getall(),
                    SaveID:_this.tryGetSavedData(),
                    Style:2
                };

                // this.getSaveDataHandle({});
                // 数据加载提示
                var cardLoading = layer.msg("数据加载中...",{'time':0,'shade':0.3});
                $.post(U('Home/Index/getAnswerJson'),params,function(data){

                    console.log(data);

                    // 数据获取成功隐藏提示
                    layer.close(cardLoading);
                    app.getSaveDataHandle(data);

                })
            },

            // 撤销数据恢复
            alertBackData:function (autoData,defaultData) {
                var Vue$this = this;
                layer.msg("数据恢复有问题？是否撤销恢复，使用默认数据？",{offset:[0],time:0,btn:["撤销恢复","关闭提示"],
                    success:function (layero) {
                        layero.find(".layui-layer-btn").css({"text-align":"center"});
                    },
                    btn1:function(){
                        if(autoData){
                            Vue$this.data = autoData;
                        }else{
                            Vue$this.data = defaultData
                        }
                        app.updateDom();
                    }
                });
            },

            getSaveDataHandle:function (data) {

                var Vue$this = this,
                    savedData = null,
                    autoData = null,
                    cardDataStorage = null,
                    defaultData = JSON.parse(JSON.stringify(_vue_data.data));

                /* @当前数据状态
                 * a. 修改状态 b. 保存状态 c. 系统默认数据
                 * 1. 上次修改未保存，提示是否恢复到之前修改状态
                 * 2. 有保存记录，且有上次修改未保存状态
                 * 3. 有保存记录，*/
                /*1.修改状态*/

                cardDataStorage = $.parseJSON(localStorage.getItem("cardDataStorage"));

                if( data.data && typeof data.data == "object"){

                    // 用户保存
                    if(data.data.Data){
                        if( Vue$this._checkAnswerJson(data.data.Data.DataStr)){
                            savedData = data.data.Data.DataStr;
                        }else{
                            $.console.warn("已保存答题卡数据有误，不可使用！")
                        }
                    }

                    // 程序生成
                    if(data.data.Auto){
                        if(Vue$this._checkAnswerJson(data.data.Auto)){
                            autoData = data.data.Auto;
                        }else{
                            $.console.warn("生成答题卡数据有误，不可使用！")
                        }
                    }

                    //使用自动数据
                    if(autoData){
                        Vue$this.data = autoData;
                    }

                }else{
                    layer.msg(data.toString());
                }
                Vue$this.updateDom();


                var contentStr = "<strong>当前有以下答题卡数据可用:</strong><br>" +
                    "(1).【上次操作】是您最近一次的修改未保存状态！<br>" +
                    "(2).【保存状态】是上次配置答题卡的保存状态！<br>请选择！";
                var btn = ["确定","取消"];
                var isThisLocalData = cardDataStorage &&
                    cardDataStorage.id == editData.getAttr(1) &&
                    cardDataStorage.subjectID == Cookie.Get("SubjectId");

                // 有保存记录，且有上次修改未保存状态
                if(isThisLocalData && savedData){
                    btn = ["上次操作","保存状态"];
                    Vue$this.layerMsg(contentStr,btn,function(){
                        Vue$this.data = $.extend(true,{},Vue$this.data,cardDataStorage.data);
                        Vue$this.updateDom();
                        Vue$this.alertBackData(autoData,defaultData);
                    },function(){
                        Vue$this.data = $.extend(true,{},Vue$this.data,savedData);
                        Vue$this.updateDom();
                        Vue$this.alertBackData(autoData,defaultData);
                    });
                    return;
                }

                //上次修改未保存，提示是否恢复到之前修改状态
                if(isThisLocalData){
                    contentStr = "<strong>有未保存的数据:</strong><br>是否恢复到上次编辑状态？";
                    Vue$this.layerMsg(contentStr,btn,function(){
                        Vue$this.data = $.extend(true,{},Vue$this.data,cardDataStorage.data);
                        Vue$this.updateDom();
                        Vue$this.alertBackData(autoData,defaultData);
                    });
                    return;
                }

                //有保存记录
                if(savedData){
                    contentStr = "<strong>当前有已保存的数据:</strong><br>是否使用已保存数据？";
                    Vue$this.layerMsg(contentStr,btn,function(){
                        Vue$this.data = $.extend(true,{},Vue$this.data,savedData);
                        Vue$this.updateDom();
                        Vue$this.alertBackData(autoData,defaultData);
                    });
                }
            }
        }
    });
});