$.CustomTestStore = { 
    url : '',
    requestMark : false,
    knowledge : 0,
    chapter : 0,
    skill:0,
    capacity:0,
    grade : '',
    types : '',
    subjectID : Cookie.Get('SubjectId'),
    username :  Cookie.Get('wln_user_USER'),
    act : '',
    host : '',
    isFormat : false,

    //相关数据显示对话框
    showDialog : function(elemnt,title,delay,msg,type,bt1,bt2){
        $.myDialog.normalMsgBox(elemnt,title,delay,msg,type,bt1,bt2);
    },
    init : function(act,data,ifImage){
        this.act = act;
        this.InitDivHeight();
        if(!ifImage){
            //图片版没有editor以及format，保存使用不同的逻辑
            $.Editor.init(U($.CustomTestStore.url+'/upload?dir=customTest'));
            FormatTextManager.types = $('#types');
            FormatTextManager.init();
            $('#format').click(function(){
                $.CustomTestStore.isFormat = false;
                if(FormatTextManager.formatContent()){
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
                    //console.log(U($.CustomTestStore.url+'/save'));
                    console.log(data);
                    $.post(U($.CustomTestStore.url+'/save'),data,function(result){
                        //console.log(result);return false;
                        $.Editor.requestMark = false;
                        if($.myCommon.backLogin(result)==false){
                            return false;
                        }
                        var msg = result['data'];
                        //msg += '<br>点击 <strong>确定</strong> 再添一题，点击 <strong>取消</strong> 跳转我的试题！</a>'
                        $.CustomTestStore.showDialog('msgdiv','提示',500,msg,3,'继续添加','查看试题');
                        $('.normal_yes').click(function(){
                            window.location.href = U('Custom/CustomTestStore/add');
                        });
                        $('.normal_no').click(function(){
                            window.location.href = U('Custom/CustomTestStore/index');
                        });
                    });
                }
                return false;
            });
        }
        if(this.act == 'add'){
            if(!ifImage){
                $.Editor.container = $('.editContainersTest');
                $.Editor.createContent();
                $.Editor.container = $('.editContainersAnswer');
                $.Editor.createSolution('', {'allowEmptyValue':true});
                $.Editor.container = $('.editContainersAnalytic');
                $.Editor.createAnalyze();
            }
        }else if(this.act == 'edit' || this.act == 'originality'){
            if(this.act == 'originality'){
                this.act = 'add';
            }
            this.editing(data);
        }
        $('.toggleAttributes').click(function(){
            var that = $(this);
            $(this).parents('tr').nextAll('tr').toggle();
            if(that.html() == '隐藏'){
                that.html('展开');
            }else{
                that.html('隐藏');
            }
        });
        this.setBasicData();
        this.showTip('您当前正在编辑协同命制试题！');
    },
    //显示格式化例子信息
    showTestDemo:function(){
        $.CustomTestStore.showDialog('msgdiv','格式化例子',600,'<img src="/Public/default/image/custom_test_desc.png">',4);
    },
    //显示添加原创题的tip信息在页面顶部
    showTip:function(message){
        if(Cookie.Get('user_ORIGINALITY')){
            var tip = '<div>' +
                '<span style="color: #333333" >'+(message?message:'您当前正在编辑协同命制试题！')+'</span>' +
                '<a id="goIndexOriginality" style="text-decoration: underline;cursor: pointer">返回协同命制平台</a> ' +
                '<a id="quitOriginality" style="color: #ff0000;text-decoration: underline;cursor: pointer">退出编辑</a>' +
                '</div>';
            $(tip).Tips({'delay' : 1000 * 7200});
        }
        $(document).on('click','#goIndexOriginality',function(){
            top.window.location.href=U('Yc/Originality/originality?SubjectID='+Cookie.Get('SubjectId'));
            Cookie.Del('user_ORIGINALITY');
        });
        $(document).on('click','#quitOriginality',function(){
            window.location.href=U('Custom/CustomTestStore/index');
            Cookie.Del('user_ORIGINALITY');
        });
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
        if($.Editor){
            $.Editor.container = $('.editContainersTest');
            $.Editor.createContent(basic['Test']);
            $.Editor.container = $('.editContainersAnswer');
            $.Editor.createSolution(basic['Answer'], {'allowEmptyValue':true});
            $.Editor.container = $('.editContainersAnalytic');
            $.Editor.createAnalyze(basic['Analytic']);
        }
        for(var val in data['knowledge']){
            if(!data['knowledge'][val]['KlID']){
                continue;
            }
            var container = $('.knowledge-select-change-container').find('.inputs');
            var knowledge = data['knowledge'][val];
            container.append($('<div></div>').SelectText({
                _text : knowledge['KlName'],
                val : knowledge['KlID'],
                _name : 'KlID'
            }));
        }
        for(var val in data['chapter']){
            if(!data['chapter'][val]['ChapterID']){
                continue;
            }
            var container = $('.chapter-select-change-container').find('.inputs');
            var chatper = data['chapter'][val];
            container.append($('<div></div>').SelectText({
                _text : chatper['ChapterName'],
                val : chatper['ChapterID'],
                _name : 'ChapterID'
            }));
        }

        for(var val in data['skill']){
                if(!data['skill'][val]['SkillID']){
                    continue;
                }
                var container = $('.skill-select-change-container').find('.inputs');
                var skill = data['skill'][val];
                container.append($('<div></div>').SelectText({
                    _text : skill['SkillName'],
                    val : skill['SkillID'],
                    _name : 'SkillID'
                }));
        }

        for(var val in data['capacity']){
                if(!data['capacity'][val]['CapacityID']){
                    continue;
                }
                var container = $('.capacity-select-change-container').find('.inputs');
                var capacity = data['capacity'][val];
                container.append($('<div></div>').SelectText({
                    _text : capacity['CapacityName'],
                    val : capacity['CapacityID'],
                    _name : 'CapacityID'
                }));
        }
    },

    InitDivHeight : function(){
        var height = $(window).height() - 50;
        $("#divbox").css({ 'height': height-5 ,'overflow-y':'auto','overflow-x':'hidden'});  
    },
    //基础数据：例如章节，知识点等等初始化加载的函数
    setBasicData : function(){
        var params = {};
        if(this.subjectID){
            $('#types').html('<option value="">加载中请稍候...</option>');
            $('#knowledge').html('<option value="">加载中请稍候...</option>');
            $('#special').html('<option value="">加载中请稍候...</option>');
            $('#chapter').html('<option value="">加载中请稍候...</option>');
            params['subjectID'] = this.subjectID;
            params['list'] = ['knowledge','chapter','special','types','grade','skill','capacity'];
            params['style'] = 'getMoreData';
            params = this.formatParams(params);
            $.post(U($.CustomTestStore.url+'/getData'),params,function(result){
                //console.log(result);return false;
                if($.myCommon.backLogin(result)==false){
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
                            testnum : 1,
                            TypesStyle : '%s',
                            IfChooseType : '%s'
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

                $('#skill').html('<option value="">请选技能</option>'+$.CustomTestStore.addData(datas['skill'],{val:'SkillID',text:'SkillName'}));
                $('#capacity').html('<option value="">请选能力</option>'+$.CustomTestStore.addData(datas['capacity'],{val:'CapacityID',text:'CapacityName'}));
            },'json');
        }
    },
    //将参数格式化
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
            var attributes = {};
            for(var attr in params['attributes']){
                attributes[attr] = params['attributes'][attr];
                //遍历在data中存在的键，同时键为%s，则提取data[attr]相关的值
                if(elements[attr] && attributes[attr] == '%s'){
                    attributes[attr] = elements[attr];
                }
            }
            string += this.getOption(params['frontChar']+val, text, (params.isSelected && params.selectedById == val), attributes);
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
        data['TTID'] = $('#ttid').val();
        data['TestID'] = $('#testid').val();
        if(!$.CustomTestStore.username){
            $.CustomTestStore.showDialog('msgdiv','错误提示',500,'用户名不能为空',2);
            return false;
        }
        //data['UserName'] = $.CustomTestStore.username;
        if(!$.CustomTestStore.subjectID){
            $.CustomTestStore.subjectID = 0;
            /*alert('学科不能为空！');
            return false;*/
        }
        data['SubjectID'] = $.CustomTestStore.subjectID;
        var grade = $('#grade').val();
        if(!grade){
            grade = 0;
            /*$.CustomTestStore.showDialog('msgdiv','错误提示',500,'请选择年级',2);
            $('#grade').focus();
            return false;*/
        }
        data['GradeID'] = grade;
        var source = $('#source').val();
        if(!source){
            source = '';
            /*$.CustomTestStore.showDialog('msgdiv','错误提示',500,'试题来源不能为空',2);
            $('#source').focus();
            return false;*/
        }
        data['Source'] = source;

        var types = $('#types').val();
        if(!types){
            types = parent.Types[$.CustomTestStore.subjectID][0]['TypesID'];
            /*$.CustomTestStore.showDialog('msgdiv','错误提示',500,'请选择题型',2);
            $('#types').focus();
            return false;*/
        }
        data['TypesID'] = types;
        
        var diff = $("input[name='diff']:checked").val();
        if(!diff){
            diff = 0.801;
            /*$.CustomTestStore.showDialog('msgdiv','错误提示',500,'请选择难度系数',2);
            $('input[name=diff]').focus();
            return false;*/
        }
        data['Diff'] = diff;
        //当是否格式化属性为false时，执行一次格式化
        if(!this.isFormat){
            FormatTextManager.isForamt = false;
            FormatTextManager.formatContent();
        }
        /*if(FormatTextManager.err != ''){
            $.CustomTestStore.showDialog('msgdiv','错误提示',500,FormatTextManager.err,2);
            return false;
        }*/

        var errors = [];
        for(var editor in $.Editor.instance){
            editor = $.Editor.instance[editor];
            var allowEmptyValue = editor.getOpt('allowEmptyValue');
            var content = editor.getContent().replace(/\r\n|\r|\n/g, '');
            if(!allowEmptyValue && content == ''){
                errors.push({
                    info : editor.getOpt('title'),
                    'editor' : editor
                });
            }else{
                var name = editor.getOpt('textarea');
                data[name] = content;
            }
        }
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
            /*$.CustomTestStore.showDialog('msgdiv','错误提示',500,'请选择知识点',2);
            $('#knowledge').focus();
            return false;*/
        }
        data['ChapterID'] = [];
        var chapter = $(".ChapterID_inputs");
        chapter.each(function(){
            data['ChapterID'].push($(this).val());
        });
        if(data['ChapterID'].length == 0){
           /* $.CustomTestStore.showDialog('msgdiv','错误提示',500,'请选择章节',2);
            $('#chapter').focus();
            return false;*/
        }

        data['SkillID'] = [];
        var skill = $(".SkillID_inputs");
        skill.each(function(){
            data['SkillID'].push($(this).val());
        });
        if(data['SkillID'].length == 0){
           /* $.CustomTestStore.showDialog('msgdiv','错误提示',500,'请选择章节',2);
            $('#chapter').focus();
            return false;*/
        }

        data['CapacityID'] = [];
        var capacity = $(".CapacityID_inputs");
        capacity.each(function(){
            data['CapacityID'].push($(this).val());
        });
        if(data['CapacityID'].length == 0){
           /* $.CustomTestStore.showDialog('msgdiv','错误提示',500,'请选择章节',2);
            $('#chapter').focus();
            return false;*/
        }

        return data;
    },
    //调试信息打印
    debugLog : function(str){
       console.log(str);
    }
};
/**
 * 图片试题上传JS类
 * @author demo
 */
$.ImageTest = {
    positionJson:[],//准备存放图片数据的变量，便于传回裁剪坐标
    picPath :'',//图片文件路径
    picData : [],//图片信息
    /**
     * 同步poll请求
     * @author demo
     */
    syncImage:function(no){
        var self = this;
        var post = function(no){
            $.post(U('Custom/CustomTestStore/webImagePoll'+'?n='+no+'&times='+Math.random()),function(e){
                if(e.status==1){
                    $('.submitBtn').show();
                    //传给函数，用于显示新图片dom
                    self.picData.push(e.data);
                    self.addImgList(e.data);
                    var oldsrc=$('.qrCode').attr('src');
                    var oldno=no;
                    no = parseInt(no)+1;
                    //更新二维码
                    $('.qrCode').attr('src',oldsrc.replace(oldno,no));
                }
                post(no);
            });
        };
        post(no);
    },
    /**
     * 获取验证码
     * @author demo
     */
    getQrCode:function(username){
        var self = this;
        var no = (new Date()).getTime();
        $('.qrCode').attr('src', U('Custom/CustomTestStore/qrCode') + '?n=' + no + '&u=' + username).show();
        setTimeout(function () {
            self.syncImage(no);
        }, 5000);
    },
    //本地图片上传
    avatarUpload:function() {
        var self = this;
        var key = $('#key').val();//上传验证安全码
        var username = $('#username').val();//用户名
        var fileUpload = $('#fileUpload');
        fileUpload.uploadify({
            'buttonText': '上传图片',
            'auto': true,
            'removeCompleted' : false,
            'swf': '/Public/plugin/uploadify/uploadify.swf',
            'uploader': U('Custom/CustomTestStore/avatarUpload?username=' + username + '&key=' + key),
            'method': 'post',
            'multi': false,
            'fileTypeDesc': '请上传.jpg;*.png;*.gif;类型文件',
            'fileTypeExts': '*.gif; *.jpg; *.png',
            'fileSizeLimit': '5120',
            'onSelectError': function (file, errorCode, errorMsg) {
                switch (errorCode) {
                    case -110:
                        alert('请上传小于' + fileUpload.uploadify('settings', 'fileSizeLimit') + "k的图片！");
                        break;
                }
            },
            'onUploadSuccess': function (file, data, response) {
                //每次成功上传后执行的回调函数，从服务端返回数据到前端
                data = eval('(' + data + ')');//格式化json字符串为json对象
                if(data.data.error){
                    alert(data.data.error);
                    return false;
                }else{
                    $('.submitBtn').show();
                    self.picData.push(data.data);
                    self.addImgList(data.data);
                }
            }
        });
    },
    /**
     * 图片裁切
     * @param e 图片信息
     */
    imageCutting:function(data,num){
        var e = data[num];
        var self = this;
        var originW = e.width;//存储原图的宽高，用于计算
        var originH = e.height;
        var frameW = 800;  //原图的缩略图固定宽度，作为一个画布，限定宽度，高度自适应，保证了原图比例
        var frameH = Math.round(frameW*originH/originW);//根据原图宽高比和画布固定宽计算画布高，即originImg加载上传图后的高。此处不能简单用.height()获取，有DOM加载的延迟
        if(originW<originH){
            frameH = 400;
            frameW = Math.round(frameH*originW/originH);
        }
        var rangeX = 4;  //初始缩放比例
        var rangeY = 3;
        var originImg = $('#originImg'+num);//画布
        self.picPath= e.picPath;
        $('#originBox'+num).height(frameH).width(frameW).show();
        originImg.attr('src', e.picPath).height(frameH).width(frameW);
        //准备好数据后，开始配置imgAreaSelect使得图片可选区
        originImg.imgAreaSelect({
            handles:true,//选区样式，四个角四个方框
            fadeSpeed: 300, //选区阴影建立和消失的渐变
            x1:0, y1:0, x2: 280, y2:210,//选区的初始位置和结束位置
            parent:$('#divbox'),
            onInit:function(){
            },
            onSelectChange: function (img, selection) {
                //选区改变时的触发事件
                //selection包括x1,y1,x2,y2,width,height几个量，分别为选区的偏移和高宽
                rangeX = selection.width / frameW;  //依据选取高宽和画布高宽换算出缩放比例
                rangeY = selection.height / frameH;
            },
            onSelectEnd: function (img, selection) {
                var newX = selection.x1;
                var newY = selection.y1;
                self.positionJson[num] = [];
                //放开选区后的触发事件
                //计算实际对于原图的裁剪坐标
                self.positionJson[num]['x1'] = Math.round(originW * newX / frameW);
                self.positionJson[num]['y1'] = Math.round(originH * newY / frameH);
                self.positionJson[num]['width'] = Math.round(rangeX * originW);
                self.positionJson[num]['height'] = Math.round(rangeY * originH);
            }
        });
    },
    //添加上传的图片到页面
    addImgList:function(data){
        var self = this;
        var length = $('.imgTest').length;
        var originH = self.picData[length]['height'];
        var originW = self.picData[length]['width'];
        var frameW = 300;  //原图的缩略图固定宽度，作为一个画布，限定宽度，高度自适应，保证了原图比例
        var frameH = Math.round(frameW*originH/originW);//根据原图宽高比和画布固定宽计算画布高，即originImg加载上传图后的高。此处不能简单用.height()获取，有DOM加载的延迟

        $('#fileUpload').uploadify('cancel', 'SWFUpload_0_'+length);
        var imgTestHtml = '<li class="clearfix imgTest" id="imgTest'+length+'">' +
            '        <div class="cut-box" id="originBox'+length+'" style="display: none;">' +
            '            <img src="" class="preview" id="originImg'+length+'">' +
            '        </div>'+
            '        <div class="imgContent img-area left">'+
            '            <div class="nor-btn img-btn imgCut" id="imgBtn'+length+'" num="'+length+'">裁剪</div>'+
            '            <img height="200" width="260"  src="'+data.picPath+'" alt="已上传图片" id="prevImg"/>' +
            '        </div>'+
            '        <div class="img-intro">'+
            '            <div class="textarea-desc">图片描述：</div>' +
            '            <div class="textarea-wrap">' +
            '                <textarea name="testText" class="testText"></textarea>' +
            '            </div>' +
            '        </div>'+

            '        <a class="del-btn iconfont" href="javascript:;" title="删除" num="'+length+'">&#xe606;</a>'+

            '</li>';
        $('#imgTestList').append(imgTestHtml);
    },
    //保存,重新上传
    avatarSave:function(num) {
        var self=this;
        var data = self.positionJson[num];
        var width = data.width;
        var height = data.height;
        var x1 = data.x1;
        var y1 = data.y1;
        var thisPicPath = self.picData[num].picPath;
        $.post(U('Custom/CustomTestStore/imgCutSave'), {picPath:thisPicPath, width: width, height: height, x1: x1, y1: y1}, function (e) {
            if($.myCommon.backLogin(e)==false){
                return false;
            }
            if(e.status==1){
                //裁切区域隐藏
                $('#originImg'+num).imgAreaSelect({
                    hide: true//选区样式，四个角四个方框
                });
                $('#originBox'+num).hide();//画布
                $('#imgTest'+num).find('.imgContetn').html('');
                var time = new Date().getTime();
                //原图片显示为新图
                $('#imgTest'+num).find('#prevImg').attr('src', e.data.picPath+'?'+time);
                self.picData[num] = e.data;
                self.picData[num]['picPath'] = e.data.picPath+'?'+time;

            }else{
                alert(e.data);
            }
        });
    },
    //获取试题属性用户填写
    getTestAttr:function(){
        var data= {};
        //年级
        data['GradeID'] = $('#grade').val();
        //题型
        data['TypesID'] = $('#types').val();
        //难度
        data['Diff'] = $("input[name='diff']:checked").val();
        //来源
        data['Source'] = $('#source').val();
        //知识点
        data['KlID'] = [];
        $('.KlID_inputs').each(function(){
            data['KlID'].push($(this).val());
        });
        //章节
        data['ChapterID'] = [];
        $('.ChapterID_inputs').each(function(){
            data['ChapterID'].push($(this).val());
        });

        //技能
        data['SkillID'] = [];
        $('.SkillID_inputs').each(function(){
            data['SkillID'].push($(this).val());
        });

        //能力
        data['CapacityID'] = [];
        $('.CapacityID_inputs').each(function(){
            data['CapacityID'].push($(this).val());
        });

        //备注
        data['Remark'] = $('#remark').val();
        return data;
    },
    //绑定一些图片操作事件
    bindImg:function(){
        var self = this;
        //裁切
        $(document).on('click','.imgCut',function(){
            var len = $('.saveImgCut').length;
            if(len>0){
                alert('请先保存之前裁切的图片！');
                return false;
            }
            var num = $(this).attr('num');
            self.imageCutting(self.picData,num);
            $('#imgBtn'+num).removeClass('imgCut');
            $('#imgBtn'+num).addClass('saveImgCut');
            $('#imgBtn'+num).html('保存');
        });
        //显示裁切后的图片
        $(document).on('click','.saveImgCut',function(){
            var num = $(this).attr('num');
            self.avatarSave(num);
            $('#imgBtn'+num).removeClass('saveImgCut');
            $('#imgBtn'+num).addClass('imgCut');
            $('#imgBtn'+num).html('裁切');
        });
        //保存试题
        $('#saveImgTest').on('click',function(){
            //遍历Dom，将图片地址和文字描述都记录进数组，然后提交
            var data = [];
            $('.imgTest').each(function(){
                var picPath = $(this).find('#prevImg').attr('src');
                var testText = $(this).find('.testText').val();
                var test = {'picPath':picPath,'testText':testText};
                data.push(test);
            });
            if(data.length==0){
                $.myDialog.normalMsgBox('msgdiv','提示',500,'<div>请先上传图片编辑！</div>',1);
                return false;
            }
            $.post(U('Custom/CustomTestStore/imgTestSave'),{'data':data,'attr':self.getTestAttr()},function(e){
                if(e.status == 1){
                    if($.myCommon.backLogin(e)==false){
                        return false;
                    }
                    var msg = e['data'];
                    //msg += '<br>点击 <strong>确定</strong> 再添一题，点击 <strong>取消</strong> 跳转我的试题！</a>'
                    $.CustomTestStore.showDialog('msgdiv','提示',500,msg,3,'继续添加','查看试题');
                    $('.normal_yes').click(function(){
                        window.location.href = U('Custom/CustomTestStore/photograph');
                    });
                    $('.normal_no').click(function(){
                        window.location.href = U('Custom/CustomTestStore/index');
                    });
                }else{
                    //提示失败原因
                    var alertHtml = '<div>'+e.data+' 请刷新本页面后重试！</div>';
                    $.myDialog.normalMsgBox('msgdiv','提示',500,alertHtml,1);
                }
            })
        });
        //移除
        $(document).on('click','.del-btn',function(){
            var num = $(this).attr('num');
            //裁切区域隐藏
            $('#originImg'+num).imgAreaSelect({
                hide: true//选区样式，四个角四个方框
            });
            $('#imgTest'+num).remove();
            var len = $('.imgTest').length;
            if(len<1){
                $('.submitBtn').hide();
            }

        });
    },
    init:function(username){
        this.getQrCode(username);
        this.avatarUpload();
        this.bindImg();
    }
};
/**
 * 单题上传类
 * @type {{init: $.customTestStoreTestAdd.init, pageInit: $.customTestStoreTestAdd.pageInit}}
 */
$.customTestStoreTestAdd = {
    init:function(data,originality,url,ifImage, act){
        //对url判断
        if(url.indexOf('/')===0){
            url = url.substring(1);
        }
        this.pageInit(data,originality,url,ifImage, act);
    },
    pageInit:function(data,originality,url,ifImage, act){
        $.CustomTestStore.url = url;
        /*原创协同命制品台跳过来的*/
        if(!originality['ttID'] && 'add' == act){
            $.CustomTestStore.init('add',data,ifImage);
        }else{
            if(originality['ttID']){
                $('#ttid').val(originality['ttID']);
                //知识点 题型
                data.basic = {};
                data.basic.TypesID = originality['typeID'];
                data.basic.SubjectID = Cookie.Get('SubjectId');
                data.basic.Diff = originality['diff'];
                var params = {
                    'ID' : originality['klID'],
                    'style' : 'knowledgeList',
                    'return' : 1
                };
                if(originality['klID']){
                    $.post(U('Custom/CustomTestStore/getData'),params,function(result){
                        data.knowledge = result['data'];
                        $.CustomTestStore.init('originality',data,ifImage);
                    });
                }
            }else{
                $.CustomTestStore.init('edit',data,ifImage);
            }
        }
        $('.knowledge-select-change-container').SelectChangeHandle({
            classname:'.selection',
            subjectid:$.CustomTestStore.subjectID,
            url:U(url+'/getData'),
            kv : 'KlID',
            vv : 'KlName',
            identify : 'knowledge'
        });
        $('.chapter-select-change-container').SelectChangeHandle({
            classname:'.selection',
            subjectid:$.CustomTestStore.subjectID,
            url:U(url+'/getData'),
            kv : 'ChapterID',
            vv : 'ChapterName',
            identify : 'chapter',
            allowEmptyVal : true
        });

        $('.skill-select-change-container').SelectChangeHandle({
            classname:'.selection',
            subjectid:$.CustomTestStore.subjectID,
            url:U(url+'/getData'),
            kv : 'SkillID',
            vv : 'SkillName',
            identify : 'skill',
        });
        $('.capacity-select-change-container').SelectChangeHandle({
            classname:'.selection',
            subjectid:$.CustomTestStore.subjectID,
            url:U(url+'/getData'),
            kv : 'CapacityID',
            vv : 'CapacityName',
            identify : 'capacity',
        });

    }
};

//添加试题的select下拉框插件
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
                if($.myCommon.backLogin(result)==false){
                    return false;
                };
                var datas = result['data'];
                //console.log(datas);
                finished = true;
                if(!datas || datas['length']=='0'){
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
            var _this=$(this);
            var text = [];
            var vals = [];
            var selects = getSelects();
            var size = selects.length;
            for(var i=0; i<size; i++){
                var _select = $(selects[i]);
                if(_select.val() == ''){
                    if(!opts.allowEmptyVal || i < 2){
                        var thiscontent=_this.parents('td').last().prev().html();
                        if(typeof(thiscontent)!='undefined' && thiscontent!='') thiscontent=thiscontent.replace('：','');
                        else thiscontent='';
                        if(thiscontent!='') alert('请选择'+thiscontent+'内容。');
                        else alert('请正确选择内容');
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
        //判断当前选中的项是否已经存在选中标签中
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

//选中的知识点，章节，在初始化或者选择后使用
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