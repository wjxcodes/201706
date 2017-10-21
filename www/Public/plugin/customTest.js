$.CustomTestStore = { 
    url : '',
    requestMark : false,
    knowledge : 0,
    chapter : 0,
    grade : '',
    types : '',
    subjectID : Cookie.Get('SubjectId'),
    username :  Cookie.Get('user_USER'),
    act : '',
    host : '',
    isFormat : false,
    showDialog : function(elemnt,title,delay,msg,type){
        normalmsgbox(elemnt,title,delay,msg,type);
    },
    init : function(act,data){
        this.act = act;
        this.InitDivHeight();
        $.Editor.init($.CustomTestStore.url+'-upload');
        FormatTextManager.types = $('#types');
        FormatTextManager.init();
        $('#format').click(function(){
            //$.Editor.createStandard();
            $.CustomTestStore.isFormat = false;
            if($.CustomTestStore.formatContent()){
                 $.CustomTestStore.showDialog('msgdiv','错误提示',500,FormatTextManager.err,2); 
                 return false;
            }
            $.CustomTestStore.isFormat = true;
            $.CustomTestStore.showDialog('msgdiv','提示',500,'试题格式化完成！请重新检查内容，确认无误后点击[保存]！',1);
        });
        $('#save').click(function(){
            if($.Editor.requestMark){
                $.CustomTestStore.showDialog('msgdiv','提示',500,'内容已提交，正在处理中，请稍候！',2);
                return false;
            }
            var data = $.CustomTestStore.validate();
            if(data){
                $.Editor.requestMark = true;
                $.post($.CustomTestStore.url+'-save',data,function(result){
                    $.Editor.requestMark = false;
                    if(backLogin(result)==false){
                        return false;
                    };
                    data = result['data'];
                    $.CustomTestStore.showDialog('msgdiv','提示',500,data,1);
                    $('.normal_no').click(function(){
                        window.location.href = $.CustomTestStore.url+'-index';
                    });
                });
            }
            return false;
        });

        if(this.act == 'add'){
            $.Editor.createContent();
            $.Editor.createSolution();
            $.Editor.createAnalyze();
        }else{
            this.editing(data);
        }
        this.setBasicData();
    },
    //编辑内容
    editing : function(data){
        var basic = data['basic'];
        if(basic['SubjectID'] != $.CustomTestStore.subjectID){
            alert('所编辑的试题与当前学科不符！');
            window.location.href = $.CustomTestStore.url;
            return false;
        }
        $.CustomTestStore.types = basic['TypesID'];
        $.CustomTestStore.grade = basic['GradeID'];
        $.CustomTestStore.setTestNum(basic['TestNum']);
        $('.difficulty input').each(function(){
            if($(this).val() == basic['Diff']){
                $(this).attr('checked',true);
            }
        });
        $('#source').val(basic['Source']);
        $('#remark').val(basic['Remark']);
        $.Editor.createContent(basic['Test']);
        $.Editor.createSolution(basic['Answer']);
        $.Editor.createAnalyze(basic['Analytic']);
        for(var val in data['knowledge']){
            var container = $('.knowledge-select-change-container').find('.inputs');
            var knowledge = data['knowledge'][val];
            container.append($('<div></div>').SelectText({
                _text : knowledge['KlName'],
                val : knowledge['KlID'],
                _name : 'KlID'
            }));
        }
        for(var val in data['chapter']){
            var container = $('.chapter-select-change-container').find('.inputs');
            var chatper = data['chapter'][val];
            container.append($('<div></div>').SelectText({
                _text : chatper['ChapterName'],
                val : chatper['ChapterID'],
                _name : 'ChapterID'
            }));
        }
    },

    InitDivHeight : function(){
        var height = $(window).height() - 50;
        $("#divbox").css({ 'height': height-5 ,'overflow-y':'auto','overflow-x':'hidden'});  
    },

    setBasicData : function(){
        var params = {};
        if(this.subjectID){
            $('#types').html('<option value="">加载中......</option>');
            $('#knowledge').html('<option value="">加载中......</option>');
            $('#special').html('<option value="">加载中......</option>');
            $('#chapter').html('<option value="">加载中......</option>');
            params['subjectID'] = this.subjectID;
            params['list'] = ['knowledge','chapter','special','types','grade'];
            params['style'] = 'getMoreData';
            params = this.formatParams(params);
            $.post($.CustomTestStore.url+'-getData',params,function(result){
                if(backLogin(result)==false){
                    return false;
                };
                var datas = result['data'];
                $('#types').html('<option value="">请选择题型</option>'+$.CustomTestStore.addData(datas['types'],
                    {
                        val : 'TypesID',
                        text : 'TypesName',
                        isSelected : true,
                        selectedById : $.CustomTestStore.types,
                        attributes : {
                            testnum : 1
                        }
                    }
                ));
                $('#grade').html('<option value="">请选择年级</option>'+$.CustomTestStore.addData(datas['grade'],{
                        val:'GradeID',
                        text:'GradeName',
                        isSelected : true,
                        selectedById:$.CustomTestStore.grade
                    }
                ));
                $('#knowledge').html('<option value="">请选择主干知识点</option>'+$.CustomTestStore.addData(datas['knowledge'],{val:'KlID',text:'KlName'}));
                $('#chapter').html('<option value="">请选章节，避免超纲</option>'+$.CustomTestStore.addData(datas['chapter'],{val:'ChapterID',text:'ChapterName'}));
            },'json');
        }
    },
    //格式化试题内容
    formatContent : function(){
        FormatTextManager.init();
        $('.editContainers').find('.editorContainer').each(function(){
            var ue = UE.getEditor($(this).attr('editor'));
            FormatTextManager.process.call(ue);
        });
        $.CustomTestStore.setTestNum(FormatTextManager.attributes.topic.length);
        return (!$.CustomTestStore.isFormat && 
                    FormatTextManager.attributes.topic.length > 0 && 
                        FormatTextManager.err != '')
    },

    formatParams : function(params){
        var param = [];
        for(var p in params){
            var str = '';
            if(p === 'list'){
                str = 'list='+params[p].join(',');
            }else{
                str = p+'='+params[p];
            }
            param.push(str);
        } 
        return param.join('&');
    },

    addData : function(data,params){
        if(!data)
            return false;
        params = $.extend({
            isSelected : false,
            selectedById : 0,
            frontChar : '',
            val : false,  //提取json中的指定值
            text : false, //提取json中指定的描述
            attributes : {} //附加属性
        },params);
        if(params.val === false || params.text === false){
            return false;
        }
        var string = '';
        for(var i=0; i<data.length; i++){
            var elements = data[i];
            var val = elements[params['val']];
            var text = elements[params['text']];
            string += this.getOption(params['frontChar']+val, text, (params.isSelected && params.selectedById == val), params['attributes']);
        }
        return string;
    },

    getOption : function(val,text,isSelected,attribute,className){
        var str = '<option value="'+val+'"';
        if(attribute){
            for(var attr in attribute){
                str += ' '+attr+'='+attribute[attr];
            }
        }
        if(className){
            str += ' class="'+className+'"';
        }
        if(isSelected){
            str += ' selected="selected"';
        }
        return str += ('>' + text + '</option>');
    },

    setTestNum : function(num){
        //$('.statistic').html('试题内容&nbsp;&nbsp;&nbsp;共有<strong style="color:red;">&nbsp;'+num+'&nbsp;</strong>道小题');
    },

    validate : function(){
        var data = {};
        data['act'] = this.act;
        data['verifyCode'] = $('#verifyCode').val();
        data['TestID'] = $('#testid').val();
        if(!$.CustomTestStore.username){
            $.CustomTestStore.showDialog('msgdiv','错误提示',500,'用户名不能为空',2);
            return false;
        }
        data['UserName'] = $.CustomTestStore.username;
        if(!$.CustomTestStore.subjectID){
            alert('学科不能为空！');
            return false;
        }
        data['SubjectID'] = $.CustomTestStore.subjectID;
        var grade = $('#grade').val();
        if(!grade){
            $.CustomTestStore.showDialog('msgdiv','错误提示',500,'请选择年级',2);
            $('#grade').focus();
            return false;
        }
        data['GradeID'] = grade;
        var source = $('#source').val();
        if(!source){
            $.CustomTestStore.showDialog('msgdiv','错误提示',500,'试题来源不能为空',2);
            $('#source').focus();
            return false;
        }
        data['Source'] = source;

        var types = $('#types').val();
        if(!types){
            $.CustomTestStore.showDialog('msgdiv','错误提示',500,'请选择题型',2);
            $('#types').focus();
            return false;
        }
        data['TypesID'] = types;
        
        var diff = $("input[name='diff']:checked").val();
        if(!diff){
            $.CustomTestStore.showDialog('msgdiv','错误提示',500,'请选择难度系数',2);
            $('input[name=diff]').focus();
            return false;
        }
        data['Diff'] = diff;
        //当是否格式化属性为false时，执行一次格式化
        if(!this.isFormat)
            this.formatContent();

        var errors = [];
        $('.editorContainer').each(function(){
            var that = $(this);
            var editor = UE.getEditor(that.attr('editor'));
            var allowEmptyValue = that.attr('allowEmptyValue');
            var content = editor.getContent();
            if(allowEmptyValue == 0 && content == ''){
                errors.push({
                    info : that.find('.containerTitle').html(),
                    'editor' : editor
                });
            }else{
                var name = editor.getOpt('textarea');
                data[name] = content;
            }
        });
        if(errors.length > 0){
            var error = [];
            for(var i=0; i<errors.length; i++){
                error.push(errors[i]['info']+'不能为空\r\n');
            }
            $.CustomTestStore.showDialog('msgdiv','错误提示',500,error.join('，'),2);
            errors[0]['editor'].focus();
            return false;
        }

        //提取小题信息
        data['attributes'] = FormatTextManager.getTopic();
        
        data['KlID'] = [];
        var knowledge = $(".KlID_inputs");
        knowledge.each(function(){
            data['KlID'].push($(this).val());
        });
        data['Remark'] = $('#remark').val();
        if(data['KlID'].length == 0){
            $.CustomTestStore.showDialog('msgdiv','错误提示',500,'请选择知识点',2);
            $('#knowledge').focus();
            return false;
        }
        data['ChapterID'] = [];
        var chapter = $(".ChapterID_inputs");
        chapter.each(function(){
            data['ChapterID'].push($(this).val());
        });
        if(data['ChapterID'].length == 0){
            $.CustomTestStore.showDialog('msgdiv','错误提示',500,'请选择章节',2);
            $('#chapter').focus();
            return false;
        }
        return data;
    },
    //调试信息打印
    debugLog : function(str){
       console.log(str);
    }
}

$.Editor = {
    container : null,
    //默认的编辑器加载内容
    html : '<div class="editorContainer">'+
                '<div class="editor"></div>'+
            '</div>',
    url : '', //处理数据的url
    //初始化内容，
    //url：处理数据的url地址，必须指定
    //container：编辑器将被添加到此区域
    init : function(url, container){
        container = container || '.editContainers';
        $.Editor.container = $(container);
        $.Editor.url = url;
    },

    addEidtor : function(newer,text,opts,allowEmptyValue){
        allowEmptyValue = allowEmptyValue || 0;
        var id = $.Editor.generateUniqueid();
        newer.attr('editor',id);
        newer.attr('allowEmptyValue', allowEmptyValue)
        newer.find('.editor').attr('id',id);
        $.Editor.container.append(newer);
        var ue = $.Editor.createEditor(id,opts);
        ue.ready(function(){
            if(text){
                this.setContent(text);
            }else{
                this.setContent('');
            }
        });
    },

    //创建题文
    createContent : function(text, opts){
        text = text || '';
        opts = opts || {
            'textarea' : 'Test',
            'initialFrameHeight' : 100 
        };
        var newer = $($.Editor.html);
        newer.find('.editor').before(
            '<div class="editorContainerTop">'+
                 '<span class="containerTitle">题文</span>'+
            '</div>'
        );
        $.Editor.addEidtor(newer, text, opts);
    },
    //创建解析
    createAnalyze : function(text, opts){
        text = text || '';
        opts = opts || {
            'textarea' : 'Analytic',
            'initialFrameHeight' : 100 
        };
        var newer = $($.Editor.html);
        newer.find('.editor').before(
            '<div class="editorContainerTop">'+
                 '<span class="containerTitle">解析</span>'+
            '</div>'
        );
        $.Editor.addEidtor(newer, text, opts, 1);
    },
    //创建答案
    createSolution : function(text, opts){
        text = text || '';
        opts = opts || {
            'textarea' : 'Answer',
            'initialFrameHeight' : 100
        };
        var newer = $($.Editor.html);
        newer.find('.editor').before(
            '<div class="editorContainerTop">'+
                 '<span class="containerTitle">答案</span>'+
            '</div>'
        );
        $.Editor.addEidtor(newer, text, opts);
    },

    //自动创建id
    generateUniqueid : function(prefix){
        return 'prefix_' + (new Date().getTime()%10000000).toString(36) + Math.random().toString(36).substring(2, 6);
    },

    createEditor : function(id,opts){
        opts = $.extend({
            toolbars: [[
             'bold', 'italic', 'underline', '|', 'fontsize', 'forecolor', '|', 'scrawl', 'wordimage', '|', 'inserttable'
            ]],
            initialFrameWidth : '90%', 
            initialFrameHeight : 80,
            autoFloatEnabled : false,
            elementPathEnabled: false,
            wordCount: false,
            initialContent : '',
            enableAutoSave : false,
            textarea : '',
            catchRemoteImageEnable:false,
            serverUrl: $.Editor.url
        },opts);
        var ue = UE.getEditor(id, opts);
        /*UE.registerUI('dialog', function(editor, uiName) {
            //创建dialog
            var dialog = new UE.ui.Dialog({
                //指定弹出层中页面的路径，这里只能支持页面,因为跟addCustomizeDialog.js相同目录，所以无需加路径
                iframeUrl:'Public/default/js/ueditor/formula.html?'+Math.random(),
                //需要指定当前的编辑器实例
                editor:editor,
                //指定dialog的名字
                name:uiName,
                //dialog的标题
                title:"输入公式",

                //指定dialog的外围样式
                cssRules:"width:600px;height:400px;",

                //如果给出了buttons就代表dialog有确定和取消
                buttons:[
                    {
                        className:'edui-okbutton',
                        label:'确定',
                        onclick:function () {
                            var latex = $.trim(dialog.getDom('iframe').contentWindow.$('#matheq_latex').val());
                            if ('' != latex) {
                                this.editor.execCommand('insertHtml', '<var _type="latex">' + latex) + '</var>';
                            }
                            dialog.close(true);
                        }
                    },
                    {
                        className:'edui-cancelbutton',
                        label:'取消',
                        onclick:function () {
                            dialog.close(false);
                        }
                    }
                ]
            });
            //创建一个button
            var btn = new UE.ui.Button({
                name:'dialogbutton' + uiName,
                title:'公式',
                //需要添加的额外样式，指定icon图标，这里默认使用一个重复的icon
                cssRules:'background-position: -200px -40px;',
                onclick:function () {
                    //渲染dialog
                    dialog.render(); 
                    dialog.open();
                }
            });
            return btn;
        });*/
        return ue;
    }
}

$.fn.SelectChangeHandle = function(option){
    return $(this).each(function(){
        var opts = $.extend({
            loadBtn : '.append',
            classname : 'select',
            loadArea : '.inputs',
            subjectid : false,
            allowEmptyVal : false, //选择内容时允许包含空值
            kv : '', //从json中提取的option value值 必须
            vv : '', //从json中提取的option innerHTML值 必须
            identify : '', //发送请求到后台以及返回数据的识别码 必须
            url : '',
            callback : function(result,currentSel,selects){
                if(backLogin(result)==false){
                    return false;
                };
                var datas = result['data'];
                if(!datas){
                    return false;
                }
                currentSel.nextAll(opts.classname).remove();
                var newer = $('<select></select>');
                newer.attr('class' , currentSel.attr('class'));
                newer.append($.CustomTestStore.getOption('','请选择'));
                for(var i=0; i<datas.length; i++){
                    var data = datas[i];
                    newer.append($.CustomTestStore.getOption(data[opts.kv],data[opts.vv]));
                }
                currentSel.after(newer);
            }
        },option);
        if(!opts.subjectid){
            alert('请选择学科');
            return false;
        }
        var that = $(this);
        that.find(opts.classname).live('change',function(){
            var _select = $(this);
            if(_select.val() == ''){
                $(this).nextAll(opts.classname).remove();
                return false;
            }
            var data = {};
            data['subjectID'] = opts.subjectid;
            data['pID'] = _select.val();
            data['style'] = opts.identify;
            sendRequest(_select,data);
        });
        that.find(opts.loadBtn).click(function(){
            var text = [];
            var vals = [];
            var selects = getSelects();
            var size = selects.length;
            for(var i=0; i<size; i++){
                var _select = $(selects[i]);
                if(_select.val() == ''){
                    if(!opts.allowEmptyVal){
                        alert('请正确选择内容');
                        _select.focus();
                        return false;
                    }
                }else{
                    var selected = _select.find('option:checked');
                    text.push(selected.text());
                    vals.push(selected.val());
                }
            }
            if(text.length == 0){
                return false;
            }
            vals = vals[vals.length-1];
            if(inputIsExist(vals)){
                var div = $('<div></div>');
                that.find(opts.loadArea).append(div.SelectText({
                    _text : text.join('&nbsp;&gt;&nbsp;'),
                    _name : opts.kv,
                    val : vals
                }));
            }
        });

        function inputIsExist(val){
            var inputs = $(opts.loadArea).find('input');
            for(var i=0; i<inputs.length; i++){
                if(val == inputs[i].value){
                    return false;
                }
            }
            return true;
        }
        
        function sendRequest(currentSel,data){
            data['r'] = Math.random();
            $.post(opts.url,data,function(result){
                opts.callback(result,currentSel,getSelects());
            });
        }

        function getSelects(){
            return that.find(opts.classname);
        }
    });
}

$.fn.SelectText = function(opts){
    opts = $.extend({
        loadArea : '.inputs',
        delBtn : '.input-del',
        _text : '',
        _name : '',
        val : ''
    },opts);

    return $(this).each(function(){
        var that = $(this);
        that.append($(getText()));
        that.find(opts.delBtn).click(function(){
            that.remove();
        });
    });

    function getText(){
        //兼容
        if(/^(>>)|(.*?)|(>>)$/.test(opts._text)){
            opts._text = opts._text.replace(/^(>>)(.*?)(>>){0,}$/,'$2');
            opts._text = opts._text.replace(/>>/ig,'&nbsp;&gt;&nbsp;');
        }
        return "<a href='#' class='input-del' title='移除'>x</a><span>"+opts._text+"</span><input type='hidden' class='"+opts._name+"_inputs' value='"+opts.val+"'>";
    }
}