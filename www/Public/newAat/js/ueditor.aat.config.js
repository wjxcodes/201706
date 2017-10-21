/**
 * ueditor完整配置项
 * 可以在这里配置整个编辑器的特性
 */
/**************************提示********************************
 * 所有被注释的配置项均为UEditor默认值。
 * 修改默认配置请首先确保已经完全明确该参数的真实用途。
 * 主要有两种修改方案，一种是取消此处注释，然后修改成对应参数；另一种是在实例化编辑器时传入对应参数。
 * 当升级编辑器时，可直接使用旧版配置文件替换新版配置文件,不用担心旧版配置文件中因缺少新功能所需的参数而导致脚本报错。
 **************************提示********************************/

(function () {
    /**
     * 配置项主体。注意，此处所有涉及到路径的配置别遗漏URL变量。
     */
    ueditorAatConfig = {
        config: {
            serverUrl: U("Aat/Base/upload?dir=userAnswer")

            //工具栏上的所有的功能按钮和下拉框，可以在new编辑器的实例时选择自己需要的从新定义
            , toolbars: [[
                'bold', 'italic', 'underline', '|', 'forecolor', '|',
                'fontsize', 'insertorderedlist', 'insertunorderedlist', '|',
                'justifyleft', 'justifycenter', '|',
                 'simpleupload', 'scrawl'
            ]]

            , focus: true //初始化时，是否让编辑器获得焦点true或false

            , initialFrameHeight: 70  //初始化编辑器高度,默认320

            //启用自动保存
            , enableAutoSave: false
            //fontsize
            //字号
            , 'fontsize': [11, 12, 14, 16, 18, 20, 24]

            //elementPathEnabled
            //是否启用元素路径，默认是显示
            , elementPathEnabled: false

            //wordCount
            , wordCount: false          //是否开启字数统计
            , maximumWords: 2000 ,      //允许的最大字符数


            /*****----------------------一以下为系统配置----------------------------------*/
            /* 上传图片配置项 */
            "imageActionName": "uploadimage", /* 执行上传图片的action名称 */
            "imageFieldName": "upfile", /* 提交的图片表单名称 */
            "imageMaxSize": 3072000, /* 上传大小限制，单位B */
            "imageAllowFiles": [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* 上传图片格式显示 */
            "imageCompressEnable": true, /* 是否压缩图片,默认是true */
            "imageCompressBorder": 1600, /* 图片压缩最长边限制 */
            "imageInsertAlign": "none", /* 插入的图片浮动方式 */
            "imageUrlPrefix": "", /* 图片访问路径前缀 */
            "imagePathFormat": "/ueditor/php/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
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
            "scrawlPathFormat": "/Uploads/userAnswer/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
            "scrawlMaxSize": 3072000, /* 上传大小限制，单位B */
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
        }
    }

})();
