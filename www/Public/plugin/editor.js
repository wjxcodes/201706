$.Editor = {
    container : null,
    //默认的编辑器加载内容
    html : '<div class="editorContainer">'+
                '<div class="editor"></div>'+
            '</div>',
    url : '', //处理数据的url
    config : '', //配置文件
    instance : {},
    //初始化内容，
    //url：处理数据的url地址，必须指定
    //container：编辑器将被添加到此区域
    init : function(url, container){
        container = container || '.editContainers';
        $.Editor.container = $(container);
        $.Editor.url = url;

        var config={
            /* 上传图片配置项 */
            "imageActionName": "uploadimage", /* 执行上传图片的action名称 */
            "imageFieldName": "upfile", /* 提交的图片表单名称 */
            "imageMaxSize": 2048000, /* 上传大小限制，单位B */
            "imageAllowFiles": [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* 上传图片格式显示 */
            "imageCompressEnable": true, /* 是否压缩图片,默认是true */
            "imageCompressBorder": 1600, /* 图片压缩最长边限制 */
            "imageInsertAlign": "none", /* 插入的图片浮动方式 */
            "imageUrlPrefix": "", /* 图片访问路径前缀 */
            "imagePathFormat": "Uploads/customTest/image/{yyyy}/{mm}/{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
            /* {filename} 会替换成原文件名,配置这项需要注意中文乱码问题 */
            /* {rand:6} 会替换成随机数,后面的数字是随机数的位数 */
            /* {time} 会替换成时间戳 */
            /* {yyyy} 会替换成四位年份 */
            /* {yy} 会替换成两位年份 */
            /* {mm} 会替换成两位月份 */
            /* {dd} 会替换成两位日期 */
            /* {hh} 会替换成两位小时 */
            /* {ii} 会替换成两位分钟 */
            /* {ss} 会替换成两位秒 */
            /* 非法字符 \ : * ? " < > | */
            /* 具请体看线上文档: fex.baidu.com/ueditor/#use-format_upload_filename */

            /* 涂鸦图片上传配置项 */
            "scrawlActionName": "uploadscrawl", /* 执行上传涂鸦的action名称 */
            "scrawlFieldName": "upfile", /* 提交的图片表单名称 */
            "scrawlPathFormat": "Uploads/customTest/scrawl/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
            "scrawlMaxSize": 2048000, /* 上传大小限制，单位B */
            "scrawlUrlPrefix": "", /* 图片访问路径前缀 */
            "scrawlInsertAlign": "none",

            /* 截图工具上传 */
            "snapscreenActionName": "uploadimage", /* 执行上传截图的action名称 */
            "snapscreenPathFormat": "/ueditor/php/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
            "snapscreenUrlPrefix": "", /* 图片访问路径前缀 */
            "snapscreenInsertAlign": "none", /* 插入的图片浮动方式 */

            /* 抓取远程图片配置 */
            "catcherLocalDomain": ["127.0.0.1", "localhost", "img.baidu.com"],
            "catcherActionName": "catchimage", /* 执行抓取远程图片的action名称 */
            "catcherFieldName": "source", /* 提交的图片列表表单名称 */
            "catcherPathFormat": "/ueditor/php/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
            "catcherUrlPrefix": "", /* 图片访问路径前缀 */
            "catcherMaxSize": 2048000, /* 上传大小限制，单位B */
            "catcherAllowFiles": [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* 抓取图片格式显示 */

            /* 上传视频配置 */
            "videoActionName": "uploadvideo", /* 执行上传视频的action名称 */
            "videoFieldName": "upfile", /* 提交的视频表单名称 */
            "videoPathFormat": "/ueditor/php/upload/video/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
            "videoUrlPrefix": "", /* 视频访问路径前缀 */
            "videoMaxSize": 102400000, /* 上传大小限制，单位B，默认100MB */
            "videoAllowFiles": [
                ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
                ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid"], /* 上传视频格式显示 */

            /* 上传文件配置 */
            "fileActionName": "uploadfile", /* controller里,执行上传视频的action名称 */
            "fileFieldName": "upfile", /* 提交的文件表单名称 */
            "filePathFormat": "/ueditor/php/upload/file/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
            "fileUrlPrefix": "", /* 文件访问路径前缀 */
            "fileMaxSize": 51200000, /* 上传大小限制，单位B，默认50MB */
            "fileAllowFiles": [
                ".png", ".jpg", ".jpeg", ".gif", ".bmp",
                ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
                ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid",
                ".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
                ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml"
            ], /* 上传文件格式显示 */

            /* 列出指定目录下的图片 */
            "imageManagerActionName": "listimage", /* 执行图片管理的action名称 */
            "imageManagerListPath": "/ueditor/php/upload/image/", /* 指定要列出图片的目录 */
            "imageManagerListSize": 20, /* 每次列出文件数量 */
            "imageManagerUrlPrefix": "", /* 图片访问路径前缀 */
            "imageManagerInsertAlign": "none", /* 插入的图片浮动方式 */
            "imageManagerAllowFiles": [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* 列出的文件类型 */

            /* 列出指定目录下的文件 */
            "fileManagerActionName": "listfile", /* 执行文件管理的action名称 */
            "fileManagerListPath": "/ueditor/php/upload/file/", /* 指定要列出文件的目录 */
            "fileManagerUrlPrefix": "", /* 文件访问路径前缀 */
            "fileManagerListSize": 20, /* 每次列出文件数量 */
            "fileManagerAllowFiles": [
                ".png", ".jpg", ".jpeg", ".gif", ".bmp",
                ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
                ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid",
                ".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
                ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml"
            ] /* 列出的文件类型 */

        };
        $.Editor.config = config;
    },

    //创建编辑器
    setEditor : function(url, container, text, opts){
        this.init(url, container);

        text = text || '';
        opts = $.extend({
            'textarea' : 'Content',
            'initialFrameHeight' : 100,
            'allowEmptyValue' : false,
            'title' : '编辑器',
            'autotypeset':'',
            'allowDivTransToP':false,
            toolbars: [
                [
                    'anchor', //锚点
                    'undo', //撤销
                    'redo', //重做
                    'bold', //加粗
                    'indent', //首行缩进
                    'italic', //斜体
                    'underline', //下划线
                    'strikethrough', //删除线
                    'subscript', //下标
                    'fontborder', //字符边框
                    'superscript', //上标
                    'formatmatch', //格式刷
                    'source', //源代码
                    'blockquote', //引用
                    'pasteplain', //纯文本粘贴模式
                    'selectall', //全选
                    'print', //打印
                    'preview', //预览
                    'horizontal', //分隔线
                    'removeformat', //清除格式
                    'time', //时间
                    'date', //日期
                    'unlink', //取消链接
                    'insertrow', //前插入行
                    'insertcol', //前插入列
                    'mergeright', //右合并单元格
                    'mergedown', //下合并单元格
                    'deleterow', //删除行
                    'deletecol', //删除列
                    'splittorows', //拆分成行
                    'splittocols', //拆分成列
                    'splittocells', //完全拆分单元格
                    'deletecaption', //删除表格标题
                    'inserttitle', //插入标题
                    'mergecells', //合并多个单元格
                    'deletetable', //删除表格
                    'cleardoc', //清空文档
                    'insertparagraphbeforetable', //"表格前插入行"
                    'insertcode', //代码语言
                    'fontfamily', //字体
                    'fontsize', //字号
                    'paragraph', //段落格式
                    'simpleupload', //单图上传
                    'edittable', //表格属性
                    'edittd', //单元格属性
                    'link', //超链接
                    'spechars', //特殊字符
                    'searchreplace', //查询替换
                    'help', //帮助
                    'justifyleft', //居左对齐
                    'justifyright', //居右对齐
                    'justifycenter', //居中对齐
                    'justifyjustify', //两端对齐
                    'forecolor', //字体颜色
                    'backcolor', //背景色
                    'insertorderedlist', //有序列表
                    'insertunorderedlist', //无序列表
                    'fullscreen', //全屏
                    'directionalityltr', //从左向右输入
                    'directionalityrtl', //从右向左输入
                    'rowspacingtop', //段前距
                    'rowspacingbottom', //段后距
                    'imagenone', //默认
                    'imageleft', //左浮动
                    'imageright', //右浮动
                    'imagecenter', //居中
                    'wordimage', //图片转存
                    'lineheight', //行间距
                    'edittip ', //编辑提示
                    'customstyle', //自定义标题
                    'autotypeset', //自动排版
                    'touppercase', //字母大写
                    'tolowercase', //字母小写
                    'background', //背景
                    'scrawl', //涂鸦
                    'inserttable', //插入表格
                    'drafts', // 从草稿箱加载
                    'charts', // 图表
                ],
            ]
        }, opts);
        var newer = $($.Editor.html);
        return $.Editor.addEidtor(newer, text, opts);//返回ue对象实例,在非form下(如:使用ajax时,可以使用此对象的getContent()方法获取内容) @
    },

    addEidtor : function(newer,text,opts){
        var id = $.Editor.generateUniqueid();
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
        return ue;//返回对象实例
    },

    //创建题文
    createContent : function(text, opts){
        text = text || '';
        opts = $.extend({
            'textarea' : 'Test',
            'initialFrameHeight' : 100,
            'allowEmptyValue' : false,
            'title' : '题文'
        }, opts);
        var newer = $($.Editor.html);
        $.Editor.addEidtor(newer, text, opts);
    },
    //创建解析
    createAnalyze : function(text, opts){
        text = text || '';
        opts = $.extend({
            'textarea' : 'Analytic',
            'initialFrameHeight' : 100,
            'allowEmptyValue' : true, 
            'title' : '解析'
        }, opts);
        var newer = $($.Editor.html);
        $.Editor.addEidtor(newer, text, opts);
    },
    //创建答案
    createSolution : function(text, opts){
        text = text || '';
        opts = $.extend({
            'textarea' : 'Answer',
            'initialFrameHeight' : 100,
            'allowEmptyValue' : true,
            'title' : '答案'
        }, opts);
        var newer = $($.Editor.html);
        $.Editor.addEidtor(newer, text, opts);
    },

    //自动创建id
    generateUniqueid : function(prefix){
        return 'prefix_' + (new Date().getTime()%10000000).toString(36) + Math.random().toString(36).substring(2, 6);
    },

    createEditor : function(id,opts){
        opts = $.extend({
            toolbars: [[
             'bold', 'italic', 'underline', '|', 'fontsize', 'forecolor', '|', 'simpleupload', 'scrawl', 'wordimage','|', 'inserttable'
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
            serverUrl: $.Editor.url,
            'defaultConfig': $.Editor.config
            //retainOnlyLabelPasted : true
            //autoHeightEnabled : false
        },opts);
        var ue = UE.getEditor(id, opts);
        var textarea = ue.getOpt('textarea');
        $.Editor.instance[textarea] = ue;
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