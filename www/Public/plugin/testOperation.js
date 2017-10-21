/**
 * 试题修改中，修改试题相关属性
 * 如试题小题数量，小题宽度
 */
jQuery.testOperation={
    //检测函数
    checkThreeNum:function(){
        //获取当前小题数量
        var xtNum=$('.xtList').length;
        var optionwidthNum=$('.optionwidth').length;
        var optionNumNum=$('.optionnum').length;
        var numArr={'xtNum':xtNum,'optionwidthNum':optionwidthNum,'optionNumNum':optionNumNum};
        
        var minNum=false; //最小值
        var bj=''; //标记
        //排序获取最小值
        for(var i in numArr){  
            if(minNum === false){
                minNum = numArr[i];
            }else if(minNum > numArr[i]){
                minNum = numArr[i];
                bj=i;
            }
        }
        if(bj!='xtNum'){
           bj='widthNum';
        }
        var result=new Array();
        // 判断所有选项是不是都一致
        for(var j in numArr){
            if(numArr[j]==minNum){
                result['bj']='all';
            }else{
                result['bj']=bj;
                result['minNum']=minNum;
            }
        }
        return result;
        
    },
    addTestWidthNum:function(){
        $('#addt').click(function(){
            var tmp_i='';
            var result=$.testOperation.checkThreeNum();
            if(result['bj']=='all'){//一样的情况
                tmp_i=(parseInt($('#xt p').length)+1);
                $('#wd').append('<p class="optionwidth_'+tmp_i+' optionwidth">小题'+tmp_i+'：<label><input type="text" class="optionwidth'+tmp_i+' bLeft"  warning="请填入选项宽度" size="2" name="optionwidth'+tmp_i+'" value="" ></lable> </p>');
                $('#num').append('<p class="optionnum_'+tmp_i+' optionnum">小题'+tmp_i+'：<label><input type="text" class="optionnum'+tmp_i+' bLeft"  warning="请填入选项数量" size="2" name="optionnum'+tmp_i+'" value="" ></label></p> ');
                $('#xt').append('<p class="xtList">小题'+tmp_i+'：<label><input type="radio" class="choose'+tmp_i+' bLeft"  check="raido" warning="请选择测试类型" name="IfChoose'+tmp_i+'" value="0" checked="checked"> 非选择题</label> '+
            '<label><input type="radio" class="choose'+tmp_i+' bLeft" name="IfChoose'+tmp_i+'" value="3"> 单选题</label> '+
            '<label><input type="radio" class="choose'+tmp_i+' bLeft" name="IfChoose'+tmp_i+'" value="2"> 多选题</label> '+
            '</p>');
            }else if(result['bj']=='widthNum'){ //小题属性设置与小题宽度及小题数量不一致
                tmp_i=(parseInt(result['minNum'])+1);
                $('#wd').append('<p class="optionwidth_'+tmp_i+' optionwidth">小题'+tmp_i+'：<label><input type="text" class="optionwidth'+tmp_i+' bLeft"  warning="请填入选项宽度" size="2" name="optionwidth'+tmp_i+'" value="" ></lable> </p>');
                $('#num').append('<p class="optionnum_'+tmp_i+' optionnum">小题'+tmp_i+'：<label><input type="text" class="optionnum'+tmp_i+' bLeft"  warning="请填入选项数量" size="2" name="optionnum'+tmp_i+'" value="" ></label></p> ');
            }else if(result['bj']=='xtNum'){ // 小题属性设置不一样
                tmp_i=(parseInt(result['minNum'])+1);
                $('#xt').append('<p class="xtList">小题'+tmp_i+'：<label><input type="radio" class="choose'+tmp_i+' bLeft"  check="raido" warning="请选择测试类型" name="IfChoose'+tmp_i+'" value="0" checked="checked"> 非选择题</label> '+
            '<label><input type="radio" class="choose'+tmp_i+' bLeft" name="IfChoose'+tmp_i+'" value="3"> 单选题</label> '+
            '<label><input type="radio" class="choose'+tmp_i+' bLeft" name="IfChoose'+tmp_i+'" value="2"> 多选题</label> '+
            '</p>');
            }

            var height=0;
            $('#xt p').each(function(){
                height+=$(this).height();
            });
            $('#xt').scrollTop(height);
        });
    },
    delTestWidthNum:function(){
        $('#delt').click(function(){
            var tmp_i=parseInt($('.xtList').length)-1;
            if(tmp_i==0){
                return false;
            }
            $('#xt p:eq('+tmp_i+')').remove();
           
            $('#wd p:eq('+tmp_i+')').remove();
            $('#num p:eq('+tmp_i+')').remove();
           if(tmp_i==0){
                $('#wd').html('');
                $('#wd').append('<p class="optionwidth_1 optionwidth"><label>小题1:<input type="text" class="optionwidth1 bLeft"  warning="请填入选项宽度" size="2" name="optionwidth1" value="'+optionWidth[0]+'" > ');
                $('#num').html('');
                $('#num').append('<p class="optionnum_1 optionwidth"><label>小题1:<input type="text" class="optionnum1 bLeft"  warning="请填入选项数量" size="2" name="optionnum1"     value="'+optionNum[0]+'" > ');
            }
            var height=0;
            $('#xt p').each(function(){
                height+=$(this).height();
            });
            $('#xt').scrollTop(height);
        });
    },
    clearAllWidthNum:function(){
        $('#deltall').click(function(){
            if(confirm('确定清除下面所有的小题！')){
                $('#xt p').each(function(){
                    $(this).remove();
                });
               $('#xt').append('<p class="xtList">小题1：<label><input type="radio" class="choose1 bLeft"  check="raido" warning="请选择测试类型" name="IfChoose1" value="0" checked="checked"> 非选择题</label> '+
            '<label><input type="radio" class="choose1 bLeft" name="IfChoose1" value="3"> 单选题</label> '+
            '<label><input type="radio" class="choose1 bLeft" name="IfChoose1" value="2"> 多选题</label> '+
            '</p>');
                $('#wd').html('');
                $('#wd').append('<p class="optionwidth_1 optionwidth"><label>小题1：<INPUT TYPE="text" class="optionwidth1 bLeft"  warning="请填入选项宽度" size="2" name="optionwidth1" value=" " > ');
                $('#num').html('');
                $('#num').append('<p class="optionnum_1 optionwidth"><label>小题1：<INPUT TYPE="text" class="optionnum1 bLeft"  warning="请填入选项数量" size="2" name="optionnum1"     value=" " > ');
            }
        });
    },
    testChooseChange:function(){
        $('.choose').click(function(){
            var i;
            if($(this).val()==1){
                $('#wd p').show();
                $('#num p').show();
                $('.optionwidth_1').html('小题1：<label><input type="text" class="optionwidth1 bLeft"  warning="请填入选项宽度" size="2" name="optionwidth1" value="" >');
                $('.optionnum_1').html('小题1：<label><input type="text" class="optionnum1 bLeft"  warning="请填入选项宽度" size="2" name="optionnum1" value="" >');
                $('#showxt').css({'display':'block'});
            }else{
                var tmp_i=(parseInt($('#xt p').length)+1);
                
                $('#xt').find('p').each(function(i){
                    
                    if(i>0){
                        $('.optionwidth_'+(i+1)+'').hide();
                        $('.optionnum_'+(i+1)+'').hide();
                    }else{
                        $('.optionwidth_'+(i+1)+'').html('<label><input type="text" class="optionwidth1 bLeft"  warning="请填入选项宽度" size="2" name="optionwidth1" value="" >');
                        $('.optionnum_'+(i+1)+'').html('<label><input type="text" class="optionnum1 bLeft"  warning="请填入选项宽度" size="2" name="optionnum1" value="" >');
                    }
                })
                if($('#xt p').length==0){
                        $('.optionwidth_1').html('<label><input type="text" class="optionwidth1 bLeft"  warning="请填入选项宽度" size="2" name="optionwidth1" value="" >');
                        $('.optionnum_1').html('<label><input type="text" class="optionnum1 bLeft"  warning="请填入选项宽度" size="2" name="optionnum1" value="" >');
                        
                }
                $('#showxt').css({'display':'none'});
            }
        });
    },
    resize:function(){
        var _height = $(window).height()-80;
        var boxWidth = 0;
        var wrapWidth = 0;
        $('.nr_box a').each(function(){
            boxWidth += $(this).outerWidth()+8;
        });
        var windowWidth = $(window).width();
        if(windowWidth<1000){
            wrapWidth = windowWidth-20;
        }else{
            wrapWidth = 1000;
        }
        if(wrapWidth<750){
            wrapWidth=750;
        }
        var marginWidth=wrapWidth-320;
        var h=_height-60;
        $('#popup_content').css('padding','5px');
        $('#popup_message').height(_height);
        $('#popup_message').css('overflow','hidden');
        $('#wrap').width(wrapWidth).height(_height);
        $('.top_nr_box').height(h).width(wrapWidth);
        $('.styl_box').height(h);
        $('.main_left').width(marginWidth-2);
        $('.main_right').height(h).width(wrapWidth-marginWidth);
    }
}