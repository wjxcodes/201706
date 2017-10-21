//页面事件汇总
$.fn.extend({
    /**
     * 选中input checkbox数据
     * @param string str 需要选中的id以逗号间隔
     */
    inputCheck:function(str){
        str=','+str+',';
        this.each(function(){
            var inputVal=$(this).val();
            if(inputVal=='') return;

            if(str.indexOf(','+inputVal+',')!=-1){
                $(this).attr('checked','checked');
            }

            $(this).parent().removeClass('sfred');
            if($(this).attr('checked')=='checked'){
                $(this).parent().addClass('sfred');
            }
            $(this).click(function(){
                if($(this).attr('checked')=='checked'){
                    $(this).parent().addClass('sfred');
                }else{
                    $(this).parent().removeClass('sfred');
                }
            });
        });
    },
    /**
     * ajax获取select数据 适用于单一联动
     * @param string url 调用数据路径
     */
    ajaxGetSelectData:function(url,post,modelName,id){
        var _this=$(this);
        _this.html('<option value="">加载中。。。</option>');
        $.post(url,post,function(data){
            //权限验证
            if($.myCommon.backLogin(data)=='error'){
                    _this.html('<option value="">加载失败</option>');
                    return false;
                }
            var chapterstr= setOption(data['data'],id,modelName);
            _this.html('<option value="">请选择</option>'+chapterstr);
        });
    },
    /**
     * 学科列表change事件
     * @param string url 调用数据路径
     * @param array param 调用数据类型
     */
    subjectSelectChange:function(url,params){
        if(url.indexOf('/')===0){
            url = url.substring(1);
        }
        $(this).live('change',function(){
            var subjectID=$(this).val();
            if(params['ifConfirm']==1){
                var msg=confirm('将清空与当前学科关联信息！');
                if(!msg) return false;
                $('#chapterList').html('');  //清空已有关联章节
                $('#knowledgeList').html(''); //清空已有关联知识点
            }
            if(params['search']=='search'){
                $('#searchchapter').nextAll('select').remove();
                $('#searchknowledge').nextAll('select').remove();
            }else{
                $('#chapter').nextAll('select').remove();
                $('#knowledge').nextAll('select').remove();
            }
            $.post(U(url+'/getData'),{'style':params['style'],'subjectID':subjectID,'list':params['list']},function(data){
                if($.myCommon.backLogin(data)=='error'){
                        return false;
                }
                var output='';
                for(var i in data['data']){
                    output=setOption(data['data'][i],0,i);
                    if(params['search']=='search'){
                        $('#search'+i).html(output);
                    }else{
                        $('#'+i).html(output);
                    }
                }
            });
        });
    },
    /**
     * 章节change事件
     * @param string url 调取数据路径
     */
    chapterSelectChange:function(url){
        $(this).live('change',function(){
            var _this=$(this);
            if(_this.find('option:selected').attr('last')==1){
                return ;
            }
            _this.nextAll("select").remove();
            var values=_this.find('option:selected').val();
            if(values=='') return;
            var param={"style":"chapter","pID":values};
            $.post(U(url+"/getData"),param,function(msg){
                //权限验证
                if($.myCommon.backLogin(msg)=='error'){
                        return false;
                }
                var data=msg['data'];
                if(data){
                    var output='';
                    output+=setOption(data,0,'chapter');
                    _this.after('<select class="selectChapter" name="chapterID[]" >'+output+'</select>');
                }
            });
        });
    },
    /**
     * 知识点change事件
     * $param string url 调取数据路径
     * author  
     */
    knowledgeSelectChange:function(url){
        $(this).live('change',function(){
            var _this=$(this);
            if(_this.find('option:selected').attr('last')==1){
                return ;
            }
            _this.nextAll("select").remove();
            var values=_this.find('option:selected').val();
            if(values=='') return;
            var param={'style':'knowledge','pID':values};
            $.post(U(url+'/getData'),param,function(msg){
                //权限验证
                if($.myCommon.backLogin(msg)=='error'){
                        return false;
                }
                var data=msg['data'];
                if(data){
                    var output='';
                    output+=setOption(data,0,'knowledge');
                    _this.after('<select class="selectKnowledge" name="KlID[]">'+output+'</select>');
                }
            })
        })
    },
    /**
     * 地区列表change事件
     * @param string url 调用数据路径
     * @param bool school 是否查学校
     */
    areaSelectChange:function(url,school){
        $(this).live('change',function(){
            var _this=$(this);
            if(!school || typeof(school)=='undefined'){
                school=0;
            }

            if(_this.find('option:selected').attr('last')==1 && school==0){
                return false;
            }
            if(_this.find('option:selected').attr('last')==1 && school==1){
                var values=_this.find('option:selected').val();
                if(values=='') return;
                $.post(U(url+'/getData'),{"style":"areaToSchool",'areaID':values,'times':Math.random()},function(msg){
                    // 权限验证
                    if($.myCommon.backLogin(msg)=='error'){
                            return false;
                    }
                    var data=msg['data'];
                    var tmp_arr='<option value="">-请选择-</value>';
                    if(data[0]=='school'){
                        if(data[1]!=""){
                            for(var i in data[1]){
                                tmp_arr+='<option value='+data[1][i]['AreaID']+'>'+data[1][i]['AreaName']+'</option>';
                            }
                            $("#school").show();
                            $('#schooladd').hide();
                            $("#school").html(tmp_arr);
                        }else{
                            $('#schooladd').show();
                            $('#school').hide();
                        }
                    }
                });
                return false;
            }
            _this.nextAll("select").remove();
            var values=_this.find('option:selected').val();
            if(values=='') return;
            $.post(U(url+"/getData"),{"style":"area","pID":values,'times':Math.random()},function(msg){
                //权限验证
                if($.myCommon.backLogin(msg)=='error'){
                        return false;
                }
                var data=msg['data'];
                if(data){
                    var output='';
                    output+=setOption(data,0,'area');
                    _this.after('<select class="selectArea" name="AreaID[]">'+output+'</select>');
                }
            });
        });
    },
    /**
     * 载入默认地区数据
     * @param string url 调用数据路径
     * @param string str 默认地区的数据id以逗号间隔
     */
    areaSelectLoad:function(url,str){
        var _this=$(this);
        _this.find('option').each(function(){
            if(str.indexOf('|'+$(this).val()+'|')!=-1){
                $(this).attr('selected','selected');
                if($(this).attr('last')==1) return;
                $.post(U(url+"/getData"),{"style":"area","pID":_this.val(),'times':Math.random()},function(msg){
                    //权限验证
                    if($.myCommon.backLogin(msg)=='error'){
                        return false;
                    }
                    var data=msg['data'];
                    if(data){
                        var output='';
                        output+=setOption(data,0,'area');
                        _this.after('<select class="selectArea" name="AreaID[]">'+output+'</select>');
                        if($('.selectArea').last().val()==""){
                            $('.selectArea').last().areaSelectLoad(url,str);
                        }
                    }
                });
            }
        });
    },
    /**
     * 载入默认知识点数据
     * @param string url 调用数据路径
     * @param string str 默认地区的数据id以逗号间隔
     */
    knowledgeSelectLoad:function(url,str){
        var _this=$(this);
        _this.find('option').each(function(){
            if(str.indexOf('|'+$(this).val()+'|')!=-1){
                $(this).attr('selected','selected');
                if($(this).attr('last')==1) return;
                $.post(U(url+"/getData"),{"style":"knowledge","pID":_this.val(),'times':Math.random()},function(msg){
                    //权限验证
                    if($.myCommon.backLogin(msg)=='error'){
                            return false;
                    }
                    var data=msg['data'];
                    if(data){
                        var output='';
                        output+=setOption(data,0,'knowledge');
                        _this.after('<select class="selectKnowledge" name="KlID[]">'+output+'</select>');
                        if($('.selectKnowledge').last().val()==""){
                            $('.selectKnowledge').last().knowledgeSelectLoad(url,str);
                        }
                    }
                });
            }
        });
    },
    /**
     * 载入默认章节数据
     * @param string url 调用数据路径
     * @param string str 默认地区的数据id以逗号间隔
     */
    chapterSelectLoad:function(url,str){
        var _this=$(this);
        _this.find('option').each(function(){
            if(str.indexOf('|'+$(this).val()+'|')!=-1){
                $(this).attr('selected','selected');
                if($(this).attr('last')==1) return;
                $.post(U(url+"/getData"),{"style":"chapter","pID":_this.val(),'times':Math.random()},function(msg){
                    //权限验证
                    if($.myCommon.backLogin(msg)=='error'){
                            return false;
                    }
                    var data=msg['data'];
                    if(data){
                        var output='';
                        output+=setOption(data,0,'chapter');
                        _this.after('<select class="selectChapter" name="chapterID[]">'+output+'</select>');
                        if($('.selectChapter').last().val()==""){
                            $('.selectChapter').last().chapterSelectLoad(url,str);
                        }
                    }
                });
            }
        });
    },
    /**
     * 修改页面加载
     * @param string url        调用数据路径
     * @param array  params     调取数据所需参数
     * @param int    subjectID  调取数据对应学科ID
     * @author demo
     */
    allSelectLoad:function(url,params){
        var i;
        var output='';
        $.post(U(url+"/getData"),{"style":params['style'],'subjectID':params['subjectID'],'list':params['list'],'idList':params['idList']},function(data){
            if($.myCommon.backLogin(data)=='error'){
                    return false;
            }
            for(i in data['data']){
                output=setOption(data['data'][i],params['idList'][i],i);
                $('#'+i).html(output);
            }
            $('.delhang').css({'padding':'0px 5px','cursor':'pointer','color':'#f00'});
       });
    },
    /**
     * 文档属性切换改变是否测试属性 修改数据的input class样式必须为IfTest
     * @param string url 调用数据路径
     */
    docTypeSelectChange:function(url){
        var _this=$(this);
        _this.live('change',function(){
            if(_this.val()>0){
                $.post(U(url+'/getData'),{"style":'docType','ID':_this.val(),'times':Math.random()},function(msg){
                    //权限验证
                    if($.myCommon.backLogin(msg)=='error'){
                            return false;
                    }
                    var data=msg['data'];
                    if(data=='2'){
                        $('input.IfTest:eq(0)').attr('checked','checked');
                    }
                    if(data=='1'){
                        $('input.IfTest:eq(1)').attr('checked','checked');
                    }
                });
            }
        });
    }
});
/**
 * 构造option 选项
 * @param data   array  对应json数据
 * @param id     int    选中项ID
 * @param biaoji string 识别对应数据
 * @return string
 * @author demo
 */
function setOption(data,id,biaoji){
        var str='';
        var bjone='';
        var bjtwo='';
        var i,j;
        
        if(data!=null){
            switch(biaoji){
                case 'special':
                    str='<option value="">-请选择-</option>';
                    for(i in data){
                        str+='<option value="'+(data[i]['sub'] ? '' : data[i]['SpecialID'])+'"'
                        +(data[i]['SpecialID']==id ? "selected=\"selected\"" : "") +'>'+(data[i]==data[data[i].length-1] ? '┕' : '┝')+''+data[i]['SpecialName']+'';
                        if(data[i]!=data[data.length-1]){
                            bjone='┃';
                        }else{
                            bjonw=' ';
                        }
                        if(data[i]['sub']){
                            for(j in data[i]['sub']){
                                if(data[i]['sub'][j]==data[i]['sub'][data.length-1]){
                                    bjtwo='┕';
                                }else{
                                    bjtwo='┝';
                                }
                            str+='<option value="'+data[i]['sub'][j]['SpecialID']+'" '+(data[i]['sub'][j]['SpecialID']==id ? "selected=\"selected\"" : "")+'>'+bjone+bjtwo+data[i]['sub'][j]['SpecialName']+'</option>';
                            }
                        }
                    }
                    break;
                case 'types':
                    str='<option value="">-请选择-</option>';
                    for(i=0;i<data.length;i++){
                        str+='<option value="'+data[i]['TypesID']+'" '+(data[i]['TypesID']==id ? "selected=\"selected\"" : "") +' typesstyle='+data[i]['TypesStyle']+'>'+data[i]['TypesName']+'</option>';
                    }
                    break;
                case 'chapter':
                    str='<option value="">-请选择-</option>';
                    for(i in data){
                        if(data[i]['Last']==1)
                            str+='<option value="c'+data[i]['ChapterID']+'" last="1" '+(data[i]['ChapterID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['ChapterName']+'</option>';
                        else
                            str+='<option value="'+data[i]['ChapterID']+'" '+(data[i]['ChapterID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['ChapterName']+'</option>';
                        if(data[i]['sub'])
                        for(j=0;j<data[i]['sub'].length;j++){
                            if(data[i]['Last']==1)
                            str+='<option value="c'+data[i]['sub'][j]['ChapterID']+'" last="1" '+(data[i]['sub'][j]['ChapterID']==id ? "selected=\"selected\"" : "") +'>　　'+data[i]['sub'][j]['ChapterName']+'</option>';
                            else
                            str+='<option value="'+data[i]['sub'][j]['ChapterID']+'" '+(data[i]['sub'][j]['ChapterID']==id ? "selected=\"selected\"" : "") +'>　　'+data[i]['sub'][j]['ChapterName']+'</option>';
                        }
                    }
                    break;
                case 'knowledge':
                    str='<option value="">-请选择-</option>';
                    for(i in data){
                        if(data[i]['Last']==1){
                            str+='<option value="t'+data[i]['KlID']+'" '+(data[i]['KlID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['KlName']+'</option>';
                        }else {
                            str+='<option value="'+data[i]['KlID']+'" '+(data[i]['KlID']==id ? "selected=\"selected\"" : "") +'>'+data[i]['KlName']+'</option>';
                        }
                        if(data[i]['sub'])
                        for(j=0;j<data[i]['sub'].length;j++){
                            if(data[i]['Last']==1)
                               str+='<option value="t'+data[i]['sub'][j]['KlID']+'" '+(data[i]['sub'][j]['KlID']==id ? "selected=\"selected\"" : "") +'>　　'+data[i]['sub'][j]['KlName']+'</option>';
                            else
                               str+='<option value="'+data[i][j]['KlID']+'" '+(data[i][j]['KlID']==id ? "selected=\"selected\"" : "") +'>　　'+data[i][j]['KlName']+'</option>';                        
                        }
                    }
                    break;
                case 'ability':
                    str='<option value="">-请选择-</option>';
                    for(var i in data){
                        str+='<option value="'+data[i]['AbID']+'">'+data[i]['AbilitName']+'</option>';
                    }
                    break;
                case 'grade':
                    str='<option value="">-请选择-</option>';
                    for(var i in data){
                        str+='<option value="'+data[i]['GradeID']+'">'+data[i]['GradeName']+'</option>';
                    }
                    break;
                case 'area':
                    str='<option value="">-请选择-</option>';
                    for(var i in data){
                        str+='<option value="'+data[i]['AreaID']+'" last="'+data[i]['Last']+'">'+data[i]['AreaName']+'</option>';
                    }
                    break;
                case 'knowledgeList':
                    for(var i in data){
                        str+='<div>'+data[i]['KlName']+' <span class="delhang">x</span><input class="kl" name="kl[]" type="hidden" value="'+data[i]['KlID']+'"/></div>';
                    }
                    break;
                case 'chapterList':
                    for(var i in data){
                        str+='<div>'+data[i]['ChapterName']+' <span class="delhang">x</span><input class="cp" name="cp[]" type="hidden" value="'+data[i]['ChapterID']+'"/></div>';
                    }
                    break;
                case 'caseMenu':
                    str='<option value="">-请选择-</option>';
                    for(var i in data){
                        str+='<option value="'+data[i]['MenuID']+'">'+data[i]['MenuName']+'</option>';
                    }
                    break;
                default:
                    str+='<option value="">无数据！</option>';
            }
            return str;
        }
        return '<option value="">请添加该属性！</option>';
}