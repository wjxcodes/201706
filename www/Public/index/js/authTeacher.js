$(document).ready(function () {
        //删除上传文件按钮
        $('.upload-btn').live('click',function(){
            $(this).siblings('input[type=file]').click();
        });
        $('input[type=file]').live('change',function(){
            $(this).parents('span').find('.file-name').text($(this).val());
        });
        $('input[type=file]').each(function(){
            if($(this).val()) $(this).parents('span').find('.file-name').text($(this).val());
        });
        $('.del-btn').on('click',function(){
            $obj = $(this).parents('span').find('input[type=file]');
            var nameName =$obj.attr('name');
            var idName =$obj.attr('id');
            var changeName =$obj.attr('change');
            $obj.remove();
            $(this).parents('span').find('p').prepend('<input id="'+idName+'" type="file" name="'+nameName+'" style="display:none;">');
            $(this).siblings('.file-name').text('');
        });
        //验证
        $.indexCommon = $.extend($.indexCommon, {
            loginCallBack : function(status){
                if(1 == status){
                    window.location.href = window.location.href;
                }
            }
        });
        var formValid={
            show:function(dom){
                dom.parent().find(".errormsg").show();
                dom.removeClass("bdc").addClass("bdBlue");
            },
            hide:function(dom){
                dom.parent().find(".errormsg").hide();
                dom.removeClass("bdBlue").addClass("bdc");
            },
            successMsg:function(obj){
                obj.parents('.form-item').find('.item-msg').css('display','block').html('<i class="true iconfont">&#xe631;</i>');
            },
            errorMsg:function(obj,title){
                obj.parents('.form-item').find('.item-msg').css('display','block').html('<i class="false iconfont">&#xe634;</i>'+title);
            },
            onSubmit:function(){
                var self=this;
                $("#submit01").click(function() {
                    var realName = $('#id-realName-input');
                    if(!/^[\u4E00-\u9FA5]{2,10}$/.test(realName.val())){
                        self.errorMsg(realName,'真实姓名应为2-10个汉字');
                        realName.focus();
                        return false;
                    }else{
                        self.successMsg(realName);
                    }
                    
                    var idnumber = $('#id-idnumber-input').replace(/(^\s+)|(\s+$)/g, "");
                    if(!idnumber.val().match(/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/)){
                        self.errorMsg(idnumber,'身份证号格式不对');
                        idnumber.focus();
                        return false;
                    }else{
                        self.successMsg(idnumber);
                    }
                    var qualification = $('#id-qualification-input').replace(/(^\s+)|(\s+$)/g, "");
                    if(!qualification.val().match(/(^[789]\d{14}$)|(^19\d{15}$)|(^20[01]\d{14}$)/)){
                        self.errorMsg(qualification,'资格证书编号有误');
                        qualification.focus();
                        return false;
                    }else{
                        self.successMsg(qualification);
                    }
                    
                    var allow = ['jpg','jpeg','gif','png'];
                    var quaPicSrc = $('#id-quaPicSrc-file');
                    if(authInfo == 0 || quaPicSrc.val()){
                        if(allow.indexOf(quaPicSrc.val().toLowerCase().substring(quaPicSrc.val().lastIndexOf('.')+1)) == -1){
                            self.errorMsg(quaPicSrc,'请上传教师资格证照片');
                            return false;
                        }else{
                            self.successMsg(quaPicSrc);
                        }
                    }
                    
                    
                    var grade = $('#id-grade-input').replace(/(^\s+)|(\s+$)/g, "");
                    if(grade.val().length<2 || grade.val().length>20){
                        self.errorMsg(grade,'等级证书编号有误');
                        $('#id-grade-input').focus();
                        return false;
                    }else{
                        self.successMsg(grade);
                    }
                    
                    var gradePic = $('#id-gradePic-file');
                    if(authInfo == 0 || gradePic.val()){
                        if(allow.indexOf(gradePic.val().toLowerCase().substring(gradePic.val().lastIndexOf('.')+1)) == -1){
                            self.errorMsg(gradePic,'请上传教师等级证照片');
                            return false;
                        }else{
                            self.successMsg(gradePic);
                        }
                    }
                    
                    $('#form').submit();
                    
                })
            },
            init:function(){
                var self=this;
                $("input[type='text']").css({'paddingLeft':'5px'});
                self.onSubmit();
            }
        }
        formValid.init();
    });