jQuery.myMain = {
    //初始化
    init:function(){
        $('.SubjectID').find('optgroup').hide();
        //载入完善信息
        $.myMain.loadInfo();
        $.post(U('Home/Index/getData'),{'style':'area','pID':0,'times':Math.random()},function(data){
            if(data.data){
                var tmp_str = '<option value="">选择省份</option>';
                $.each(data.data,function(){
                    tmp_str+='<option value="'+this.AreaID+'">'+this.AreaName+'</option>';
                });
                $('#province').html(tmp_str);
            }
        });
    },
    //显示错误信息
    successMsg:function(obj){
        obj.parents('.form-item').find('.item-msg').css('display','block').html('<i class="true iconfont">&#xe631;</i>');
    },
    errorMsg:function(obj,title){
        obj.parents('.form-item').find('.item-msg').css('display','block').html('<i class="false iconfont">&#xe634;</i>'+title);
    },
    //载入完善信息
    loadInfo:function(){
        $('#infodiv .SchoolID').html('<option value="">请选择地区</option>');
        $('.AreaID').live('change',function(){
            var _this=$(this);
            var tmp_str='';
            var tmp_id=_this.val();
            _this.nextAll('select').html('<option value="">选择地区</option>');
            $('.SchoolID').html('<option value="">请选择学校</option>');
            if(tmp_id=='' || typeof(tmp_id)=='undefined') return;
            if(_this.find("option:selected").attr('iflast')==1){
                $.myMain.loadSchool(tmp_id);
                return;
            }
            $.post(U('Home/Index/getData'),{'style':'area','pID':tmp_id,'times':Math.random()},function(data){
                if($.myCommon.backLogin(data)==false){
                    return false;
                };
                if(data.data){
                    var tmp_str = '<option value="">选择地区</option>';
                    $.each(data.data,function(){
                        tmp_str+='<option value="'+this.AreaID+'">'+this.AreaName+'</option>';
                    });
                    if(_this.next('select').length > 0){
                        _this.removeAttr('name').next('select').html(tmp_str);
                    }else{
                        _this.removeAttr('name').after('<select class="short-select AreaID" name="AreaID">'+tmp_str+'</select>');
                    }
                }else if(_this.next('select')){
                    $.myMain.loadSchool(tmp_id);
                    _this.attr('name','AreaID').next('select').remove();
                }
            });
        });
        //切换年级学科联动
        $('.GradeID').live('change',function(){
            var tmp_id=$(this).val();
            $('.SubjectID').val('');
            $('.SubjectID').find('optgroup').hide();
            if(tmp_id=='' || typeof(tmp_id)=='undefined') return;
            if(tmp_id < 5){
                $('.SubjectID').find('optgroup').eq(0).show();
            }else{
                $('.SubjectID').find('optgroup').eq(1).show();
            }
        });
        //确定完善信息
        $('.normal_yes').live('click',function(){
            var err=0;
            //判断邮箱
            var Email=$('.Email');
            if(!/^[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9]{2,4})+$/.test(Email.val())){
                $.myMain.errorMsg(Email,'请输入正确的邮箱!');
                err=1;
            }else {
                $.myMain.successMsg(Email);
            }
            
            //判断地区
            var AreaID= $('select[name=AreaID]');
            if(!/^\d{1,5}$/.test(AreaID.val())){
                $.myMain.errorMsg(AreaID,'请选择所在地区!');
                err=1;
            }else {
                $.myMain.successMsg(AreaID);
            }
            //判断学校
            var SchoolID=$('.SchoolID');
            if(!/^\d{1,7}$/.test(SchoolID.val())){
                $.myMain.errorMsg(SchoolID,'请选择所在学校!');
                err=1;
            }else {
                $.myMain.successMsg(SchoolID);
            }
            //判断年级
            var GradeID=$('.GradeID');
            if(!/^[0-9]$/.test(GradeID.val())){
                $.myMain.errorMsg(GradeID,'请选择所在年级!');
                err=1;
            }else {
                $.myMain.successMsg(GradeID);
            }
            //判断学科
            var SubjectID=$('.SubjectID');
            if(!/^\d{1,2}$/.test(SubjectID.val())){
                $.myMain.errorMsg(SubjectID,'请选择所在学科!');
                err=1;
            }else {
                $.myMain.successMsg(SubjectID);
            }
            var Address = $('.Address');
            if(!/^\s*$/.test(Address.val()) && !/^[\u4E00-\u9FA5\d\w\-\/]+$/.test(Address.val())){
                $.myMain.errorMsg(Address,'请输入您的地址!');
                err=1;
            }else if(!/^\s*$/.test(Address.val())) {
                $.myMain.successMsg(Address);
            }
            if(err==1) return false;
            $('#complete').submit();
        });
    },
    //载入学校
    loadSchool:function(tmp_id){
        $.post(U('Home/Index/getData'),{'style':'areaToSchool','areaID':tmp_id,'times':Math.random()},function(data){
            var tmp_str = '<option value="">请选择学校</option>';
            $.each(data['data'][1],function(){
                tmp_str += '<option value="'+this.AreaID+'">'+this.AreaName+'</option>';
            });
            $('.SchoolID').html(tmp_str);
        });
    }
}
$.myMain.init();