//用户ajax数据返回处理, data：返回的数据，fun：成功调用的方法，
$.TestPreviewDetail = {
    init : function(subjectid){
        $.Editor.setEditor('__URL__/upload?dir=correctTest', '.editor', '我来说两句~', {
            'toolbars': [['bold', 'italic', 'underline', '|', 'fontsize', 'forecolor', '|', 'simpleupload', 'scrawl', 'wordimage']],
            'textarea' : 'correctcontent',
            'initialFrameWidth' : '100%',
            'initialFrameHeight' : 120,
            'title' : '纠错内容'
        });
        $.Editor.instance['correctcontent'].addListener('click', function(){
            var content = $.Editor.instance['correctcontent'].getContentTxt();
            if('我来说两句~' == content){
                $.Editor.instance['correctcontent'].setContent('<p></p>');
                $.Editor.instance['correctcontent'].focus();
            }
        });
        $.Editor.instance['correctcontent'].addListener('blur', function(){
            var content = $.Editor.instance['correctcontent'].getContentTxt();
            if('' == content){
                $.Editor.instance['correctcontent'].setContent('<p>我来说两句~</p>');
            }
        });
        this.reportErrorTest(subjectid);
    },
    //dialog-试题纠错
    reportErrorTest: function(subjectid) {
        $(".reportErrorTestBtn").live("click",function(){
            var panel = $("#tplErrorTest");
            var content = panel.find('.submit-error-content');
            var testid = $(this).attr('testid');
            var html = content.find('.contentTestId').html(testid);
            panel.show();
            layer.open({
                type: 1,
                zIndex : 111,
                title: "试题纠错",
                area: ["590px", "375px"],
                shift:5,
                btn : ['保存', '取消'],
                shadeClose: true,
                yes : function(index, layero){
                    var errType = layero.find('input[name="errType"]:checked');
                    if(0 == errType.length){
                        errType = 0;
                    }else{
                        var arr = [];
                        errType.each(function(){
                            arr.push($(this).val());
                        });
                        errType = arr.join(',');;
                    }
                    var err = $.Editor.instance['correctcontent'].getContent();
                    if(err.indexOf('我来说两句') >= 0){
                        alert('请输入内容');
                        return false;
                    }
                    var data = {
                        'testID' : testid,
                        'SubjectId' : subjectid,
                        'TypeID' : errType,
                        'correctcontent' : err
                    }
                    // console.log(data);
                    $.post(U('Home/Index/correct'), data, function(result){
                        $.Basis.ajaxResponse(result, {
                            doResponded : function(data, statusCode, ok){
                                if(ok){
                                    alert('保存成功！');
                                    panel.hide();
                                    layer.close(index);
                                }else{
                                    if('30205' == statusCode || '30835' == statusCode){
                                        $('.topLoginButton').trigger('click');
                                    }else{
                                        alert(data);
                                    }
                                }
                                return true;
                            }
                        });
                        
                    });
                    return false;
                },
                content: content
            });
        })
    }
}

$.Basis = {
    ajaxResponse : function(response, funcs){
        funcs = $.extend({
            'doResponded' : function(response, ok){ 
                return true; 
            },
            'redirect' : function(url, data, isTop){
                // window.location.href = url; 
            },
            'unknown' : function(msg){ alert('发生未知的错误！您可以向我们反馈该信息！'); }
        },funcs);
        var data = null, url = false, pro = null;
        if(response['data']){
            pro = typeof(response['data']);
        }
        if(pro === 'object'){
            data = response['data'];
            url = response['url'] || false;
        }else if(pro === 'string'){
            data = response['data'];
        }else{
            funcs.unknown(response);
            return;
        }
        if(funcs.doResponded(data, response.status, (1 == response.status)) && !url){
            return;
        }
        funcs.redirect(url, data);
        return;
    }
}