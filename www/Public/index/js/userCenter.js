jQuery.myFavorite = {
    init: function () {
        var self = this;
        self.editGroup();//编辑分组
        self.addGroupTest();//添加分组
        self.moveTest();//移动试题
        self.commentTest();//评论试题
        self.reportErrorTest();//纠错试题
    },
    //重命名分组
    editGroup: function () {
        var editItem = $(".fav-menu-nav li,.fav-group-child li").not(".default");
        var handleTpl = '<span class="this-group-handle">' +
                        '    <a href="javascript:;" title="重命名"><i class="iconfont">&#xe601;</i></a>' +
                        '    <a href="javascript:;" title="删除"><i class="iconfont">&#xe602;</i></a>' +
                        ' </span>';

        $(".editGroupAll").each(function () {
            var e = $(this);
            e.click(function () {
                e.toggleClass("on");
            });
            e.toggle(
                function () {
                    editItem.append(handleTpl);
                    e.html('<i class="iconfont">&#xe601;</i>完成');
                },
                function () {
                    editItem.find(".this-group-handle").remove();
                    e.html('<i class="iconfont">&#xe601;</i>编辑');
                })
        })
    },
    //添加分组
    addGroupTest: function () {
        var tplAddGroupTest = $("#tplAddGroupTest").html();
        layer.open({
            type: 1,
            title: "添加分组",
            area: ["350px", "180px"],
            shadeClose: true,
            btn: ["添加", "取消"],
            content: tplAddGroupTest
        });
    },

    //dialog-移动试题

    moveTest: function () {
        var tplMoveTest = $("#tplMoveTest").html();
        layer.open({
            type: 1,
            title: "选择移动位置",
            area: ["300px", "180px"],
            shadeClose: true,
            btn: ["确定", "取消"],
            content: tplMoveTest
        });

    },

    //dialog-评论试题
    commentTest: function () {
        var tplCommentTest = $("#tplCommentTest").html();
        layer.open({
            type: 1,
            title: "评论试题",
            area: ["590px", "530px"],
            shadeClose: true,
            content: tplCommentTest
        });
    },
    //dialog-试题纠错
    reportErrorTest: function () {
        var tplErrorTest = $("#tplErrorTest").html();
        layer.open({
            type: 1,
            title: "试题纠错",
            area: ["590px", "375px"],
            shadeClose: true,
            content: tplErrorTest
        });
    },
    //dialog-加入试卷
    addTest: function () {
        var tplAddTest = $("#tplAddTest").html();
        layer.open({
            type: 1,
            title: "选择加入位置",
            area: ["300px", "180px"],
            shadeClose: true,
            btn: ["确定", "取消"],
            content: tplAddTest
        })
    }
};

//编辑资料


//校本试题
jQuery.myTestFav={
    init:function(){
        var self = this;
        self.treeModel();
    },
    treeModel:function(){
        $('.showChildTree').live('click', function(){
            var e = $(this);
            var treeChild = e.nextAll("ul");
            if(treeChild.is(":visible")){
                treeChild.hide();
                e.removeClass("tree-active").text("+");
            }else{
                treeChild.show();
                e.addClass("tree-active").text("-");
            }
        });

        //知识点树滚动固定
        var isListen = $(".knowledgeTreeWrap");
        if(isListen.length>0){
            $(window).on("scroll",function(){
                var top = isListen.attr("data-offset-spy");
                var fixed = isListen.find(".knowledge-tree-wrap");
                var topH = isListen.offset().top + parseInt(top);
                var scrolltop = $(window).scrollTop();

                if (scrolltop >= topH) {
                    fixed.addClass("knowledgeTreeFixed");
                } else{
                    fixed.removeClass("knowledgeTreeFixed");
                }
            })
        }else{
            return false;
        }

    }
};
/*********************************用户中心公共部分涉及函数*****************************************/
jQuery.userCenterCommon={
    //清除分页内容
    clearPage:function(){
        $("#pagelistbox").html('');
        $("#quescount").html('?');
        $("#curpage").html('?');
        $("#selectpageicon").css({display: "none" });
        $("#pagecount").html('?');
    },
        /**
     * 展示分页
     * @param total int 总数量
     * @param prePage int 每页显示数量
     * @param page int 当前页码
     */
    showPage:function(total,prePage,page) {
        $.userCenterCommon.clearPage();;
        var pageNum = Math.floor((page - 1) / 10);//页码基数
        var pageCount = Math.ceil(total / prePage);//总页数
        var lastPageList = total+' 条记录 '+page+'/'+pageCount+'页              ';
        if (page > 1) {
            var prev = parseInt(page)-1;
            lastPageList += '<a page="'+prev+'" href="javascript:void(0);">上一页</a>';
        }
        if (page < pageCount) {
            var next = parseInt(page)+1;
            lastPageList += '<a page="' + next + '" href="javascript:void(0);" title="下一页">下一页</a>';
        }
        if (page > 10) {
            lastPageList += '<a page="' + (pageNum * 10) + '" href="javascript:void(0);">上十页</a>';
        }
        for (var i = pageNum * 10 + 1; i <= (pageNum + 1) * 10 && i <= pageCount; i++) {
            if (i != page) {
                lastPageList += '<a page="' + i + '" href="javascript:void(0);">' + i + '</a>';
            } else {
                lastPageList += '<span class="current">' + i + '</span>';
            }
        }
        if (pageCount - (pageNum + 1) * 10 > 0) {
            lastPageList += '<a page="' + ((pageNum + 1) * 10 + 1) + '"href="javascript:void(0);">下十页</a>';
        }

        $("#pagelistbox").html(lastPageList);
    }
};
/**
 * 用户分享文档相关
 */
jQuery.userShareDoc={
    loading:'',//等待提示
    alertWindow:'',//弹框
    thisPage:1,//当前页码
    ifLock:'',//锁定标识
    dated : "all",
    functionName : '',
    
    //用户分享获取记录
    myShareDocInit:function(){
        this.functionName='User/IndexCenter/myShareDocList';
        this.ajaxGetMyShareDoc();
        this.myShareDocListClick();
    },
    //获取历史存档列表数据
    ajaxGetMyShareDoc : function() {
        var self = this;
        self.loading = layer.load();//等待提示
        $.post(U(self.functionName),{dateDiff:self.dated,page:self.thisPage,m:Math.random()},function(data){

            var paperData = data['data'][0];
            var paperCount = data['data'][1];
            var perCount = data['data'][2];
            var pageCount = Math.ceil(parseInt(data['data'][1])/parseInt(data['data'][2]));

            if (pageCount == 0) { pageCount = 1; }
            if (self.thisPage <= 1) { self.thisPage = 1; }
            if (self.thisPage >= pageCount) { self.thisPage = pageCount; }
            //写入页面内容
            var listHTML='';
            if(paperData!=''){
                for(var i in paperData){
                    listHTML+='<tr id="order'+paperData[i]['DownID']+'">' +
                    '<td>'+paperData[i]['DownID']+'</td>' +
                    '<td>'+paperData[i]['UserName']+'</td>' +
                    '<td class="paperName">'+paperData[i]['DocName']+'</td>' +
                    '<td>'+paperData[i]['ShareTime']+'</td>' +
                    '<td align="center">' +
                    '<span class="handle" order="'+paperData[i]['DownID']+'" pid="'+paperData[i]['id']+'">' +
                    '<a href="javascript:;" class="down link">下载存档</a></span>' +
                    '</td>' +
                    '</tr>';
                }
            }else{
                listHTML+= '<tr><td height="40" align="center" style="font-size: 16px" colspan="6">暂无数据</td></tr>';
            }
            $('#myShareList').html(listHTML);
            layer.close(self.loading);//关闭等待提示
            //展示分页
            if(paperCount>0) {//在有数据的情况下
                $.userCenterCommon.showPage(paperCount, perCount, self.thisPage, 1);
            }else{
                $('#pagelistbox').html('');
            }
        });
    },
    // 时间切换(今天，昨天，本周，本月)
    myShareDocListClick:function(){
        var self = this;
        //时间TAB点击事件
        $('.dated').on('click',function(){
            $(this).addClass('on');//添加选中状态
            $('.tab-panel').addClass('on');//跟通用的TAB切换有些区别
            self.thisPage = 1;//初始化页码
            self.dated = $(this).attr('id');//获取时间标识
            self.ajaxGetMyShareDoc();//获取内容
        });
        //切换学科
        $('.subject').on('change',function(){
            self.subjectID = $(this).val();//学科ID
            self.thisPage = 1;//初始化第一页
            self.ajaxGetMyShareDoc();
        });
        //切换分页
        $('#pagelistbox').on('click','a',function(){
            self.thisPage = $(this).attr('page');
            self.ajaxGetMyShareDoc();
        });
        //下载文档
        $("#myShareList").on("click",'.down', function(e){
            var id = $(this).parent().attr('pid');//类似于安全码的数据
            var paperID = $(this).parent().attr("order");//下载ID
            self.downloadFile(paperID,id);
        });
    },
    //点击下载分享记录文档
    downloadFile : function(docID,pid){
        var self = this;
        if(self.ifLock != ''){
            return false;
        }
        self.loading = layer.load();//等待提示
        self.ifLock = 'IndexCenter/loadSaveWord';
        //试卷存档下载
        $.post(U('Home/Index/loadSaveWord'),{'DownID':docID,'id':pid,'times':Math.random()},function(data){
            self.ifLock = '';
            layer.close(self.loading);//关闭等待提示
            if(data.status == 1){
                var paperName = $('#order'+docID).find('.paperName').html();
                var alertHTML = '<div class="alertMessage">' +
                                '    <p align="center">'+paperName+'</p>' +
                                '    <p align="center">请【<a href="'+data['data']+'" target="_blank" id="tmpdown">点击这里</a>】下载文档。</p>' +
                                '</div>';
                self.alertWindow = layer.open({
                    type : 1,
                    title : '存档下载',
                    area : ['460px','150px'],
                    content:alertHTML
                });
            }else{
                layer.msg('下载出错，请重试！',{icon:5});
            }
        });
    }
};
/**
 * 用户金币内容
 */
 jQuery.userGold={
    loading:'',//等待提示
    alertWindow:'',//弹框
    thisPage:1,//当前页码
    ifLock:'',//锁定标识
    dated : "all",
    functionName : '',
    
    //金币获取记录
    myGoldInit:function(){
        this.functionName='User/IndexCenter/myGoldList';
        this.ajaxGetGold();
        this.myGoldListClick();
    },
    //获取金币记录列表数据
    ajaxGetGold : function() {
        var self = this;
        self.loading = layer.load();//等待提示
        $.post(U(self.functionName),{dateDiff:self.dated,page:self.thisPage,m:Math.random()},function(data){

            var paperData = data['data'][0];
            var paperCount = data['data'][1];
            var perCount = data['data'][2];
            var pageCount = Math.ceil(parseInt(data['data'][1])/parseInt(data['data'][2]));

            if (pageCount == 0) { pageCount = 1; }
            if (self.thisPage <= 1) { self.thisPage = 1; }
            if (self.thisPage >= pageCount) { self.thisPage = pageCount; }
            //写入页面内容
            var listHTML='';
            if(paperData.length){
                for(var i in paperData){
                    listHTML+='<tr><td>'+paperData[i]['PayID']+'</td><td>'+paperData[i]['PayName']+'</td><td>'+paperData[i]['PayMoney']+'</td><td>'+paperData[i]['AddTime']+'</td>' +
                    '</tr>';
                }
            }else{
                 listHTML+= '<tr><td height="40" align="center" style="font-size: 16px" colspan="6">暂无数据</td></tr>';
            }
            $('#myGoldList').html(listHTML);
            layer.close(self.loading);//关闭等待提示
            //展示分页
            if(paperCount>0) {//在有数据的情况下
                $.userCenterCommon.showPage(paperCount, perCount, self.thisPage, 1);
            }else{
                $('#pagelistbox').html('');
            }
        });
    },
    //时间切换(今天，昨天，本周，本月)
    myGoldListClick:function(){
        var self = this;
        //时间TAB点击事件
        $('.dated').on('click',function(){
            $(this).addClass('on');//添加选中状态
            $('.tab-panel').addClass('on');//跟通用的TAB切换有些区别
            self.thisPage = 1;//初始化页码
            self.dated = $(this).attr('id');//获取时间标识
            self.ajaxGetGold();//获取内容
        });
        //切换学科
        $('.subject').on('change',function(){
            self.subjectID = $(this).val();//学科ID
            self.thisPage = 1;//初始化第一页
            self.ajaxGetGold();
        });
        //切换分页
        $('#pagelistbox').on('click','a',function(){
            self.thisPage = $(this).attr('page');
            self.ajaxGetGold();
        });
    }
 };

/**
 * 用户资料
 */
jQuery.userInfo = {
    alertWindow:'',//弹框对象
    init:function(){
        this.myInfoClick();
    },
    myInfoClick:function(){
        var self = this;
        //修改资料
        $('.editMyData').on('click',function(){
            $(this).hide();
            $('.data-form-site .data-context').hide();
            $('.data-form-site .data-form-ipt-item').show();
            $('.editBtnGroup').show();
        });
        //取消修改
        $('.unEdit').on('click',function(){
            $('.data-form-site .data-form-ipt-item').hide();
            $('.editBtnGroup').hide();
            $('.editMyData').show();
            $('.data-form-site .data-context').show();
        });
        //地区事件
        $(document).on('change','.AreaID',function(){
            $(this).nextAll('select').remove();
            $('.selectSchool').html('<option value="0">请选择地区</option>');
            var areaID = $(this).val();
            if(!areaID){
                return false;
            }
            if($(this).find("option:selected").attr('iflast') == 1){
                self.showSchool(areaID);
                return false;
            }
            $(this).after('<select name="AreaID[]" class="AreaID"><option value="0">加载中..</option></select>');
            self.showArea($(this),areaID);
        });
        //保存修改
        $('.submit').on('click',function(){
            var c = $(this);
            c.val('保存中...').attr('disabled', 'disabled');
            var updateData = {
                'RealName': self.getRealName(),
                'AreaID' : self.getAreaID(),
                'SchoolID':self.getSchoolID(),
                'GradeID' : self.getGradeID(),
                'SubjectID' : self.getSubjectID()
            };
            var ifCheck = true;
            $.each(updateData, function (i, k) {
                if (k === false) {
                    ifCheck = false;
                    return false;
                }
            });
            if(ifCheck == false){
                c.val('保 存').removeAttr('disabled');
                return false;
            }
            var sendLoad = layer.load();//等待提示
            $.post(U('User/IndexCenter/saveMyInfo'),updateData,function(e){
                layer.close(sendLoad);//关闭等待提示
                c.val('保 存').removeAttr('disabled');
                if(e.status == 1){
                    self.alertWindow=layer.open({
                        title:'温馨提醒',
                        area: ['260px', '150px'], //宽高
                        btn: ['确定'],
                        content:'<div style="text-align: center;font-size: 18px;">修改成功</div>',
                        icon:6,
                        yes: function(){
                            layer.close(self.alertWindow); //如果设定了yes回调，需进行手工关闭
                            location.reload();
                        }
                    });
                    return false;
                }else {
                    layer.msg(e.data, {icon: 5});
                    return false;
                }
            });
        });
        //修改密码
        //认证手机
        //认证邮箱
    },
    //展示地区
    showArea:function(obj,areaID){
        $.post(U('User/IndexCenter/getData'),{'style':'area','pID':areaID,'times':Math.random()},function(e){
            var nextSelect = '';
            if(e.status == 1){
                nextSelect+='<option value="0">请选择</option>';
                $.each(e.data,function(i,k){
                    var isEnd= k['Last']==1?1:0;
                    nextSelect+='<option value="'+k['AreaID']+'" iflast="'+isEnd+'">'+k['AreaName']+'</option>';
                });
            }
            obj.next('select').html(nextSelect);
        });
    },
    //根据地区ID展示学校
    showSchool:function(areaID){
        $.post(U('User/IndexCenter/ajaxGetSchoolList'),{'areaID':areaID},function(e){
            var schoolSelect='';
            if(e.status == 1){
                schoolSelect += '<option value="0">请选择学校</option>';
                $.each(e.data,function(i,k){
                    schoolSelect+='<option value="'+k['SchoolID']+'">'+k['SchoolName']+'</option>';
                });
            }
            $('#myInfo').find('.selectSchool').html(schoolSelect);
        });
    },
    //获取用户真实姓名
    getRealName:function(){
        var realName = $('#realName').val();
        var realNameLength = realName.replace(/[^\x00-\xff]/g, 'xxx').length;//一个汉字3个字节utf8
        if (realNameLength < 6||realNameLength > 30) {
            this.showMsg('姓名长度必须大于两个汉字且小于10个汉字！','error',true);
            return false;
        }
        return realName;
    },
    //获取用户地区ID
    getAreaID:function(){
        var areaID = $('#myInfo').find('.AreaID:last').val();
        if(isNaN(areaID)||areaID==0){
            layer.msg('请选择所在地区！',{icon:5});
            return false;
        }
        return areaID;
    },
    //获取用户学校ID
    getSchoolID:function(){
        var schoolID = $('#myInfo').find('.selectSchool').val();
        if(isNaN(schoolID)||schoolID==0){
            layer.msg('请选择所在学校！',{icon:5});
            return false;
        }
        return schoolID;
    },
    //获取年级ID
    getGradeID:function(){
        var gradeID = $('#myInfo').find('.gradeSelect').val();
        if(isNaN(gradeID)||gradeID==0){
            this.showMsg('请选择年级！','error',true);
            return false;
        }
        return gradeID;
    },
    //获取学科ID
    getSubjectID:function(){
        var subjectID = $('#myInfo').find('.subjectSelect').val();
        if(isNaN(subjectID)||subjectID<12 || subjectID>20){
            layer.msg('请选择学科！',{icon:5});
            return false;
        }
        return subjectID;
    },
    //绑定账号弹框
    //解除绑定
    unbindThird:function(){
        var tplUnbindWln = $(".tplUnbindWln").html();
        $(".unbindBtn").on("click",function(){
            layer.open({
                type:1,
                title:"解除绑定",
                area:["360px","400px"],
                shadeClose:true,
                content:tplUnbindWln


            })
        })
    }
};

/**
 * 关注
 */
jQuery.follow = {
      init:function(){
          this.bindEvent();
      },
      bindEvent:function(){
          var that = this;
          $(document).on('mouseover','.unFollowSpace',function(){
              $(this).find('i').html('-');
              $(this).find('cite').text('取消关注');
          });
          $(document).on('mouseout','.unFollowSpace',function(){
              var status = $(this).attr('status');
              if($(this).attr('myfollower')==1 || status==1){
                  $(this).find('i').html('&#xe61c;');
                  $(this).find('cite').text('相互关注');
              }else{
                  $(this).find('i').html('&#8730;');
                  $(this).find('cite').text('已关注');
              }
          });

          //点击事件
          $(document).on('click','.unFollowSpace',function(){
              var that = $(this);
              var fid  = that.attr('fid');
              var index = layer.load();//等待提示
              $.post(U('User/IndexCenter/unFollow'),{ 'fid': fid}, function(data) {
                  layer.close(index);
                  layer.open({
                      type: 1,
                      title: "提示信息",
                      shadeClose: true,
                      content: '<p style="text-align: center;padding:10px;">'+data.data.info+'</p>'
                  });
                    if(data.data.status==1){
                        if(that.attr('myfollower')==1){
                            that.find('i').html('&#8724;');
                            that.find('cite').text('点击关注');
                            that.removeClass('unFollowSpace').addClass('followSpace');
                            return false;
                        }
                        location.href = location.href;
                    }
              });
          });
          $(document).on('click','.followSpace',function(){
              var that = $(this);
              var fid = that.attr('fid');
              var index = layer.load();//等待提示
              $.post(U('User/IndexCenter/doFollow'),{ 'fid': fid}, function(data) {
                  layer.close(index);
                  layer.open({
                      type: 1,
                      title: "提示信息",
                      shadeClose: true,
                      content: '<p style="text-align: center;padding:10px;">'+data.data.info+'</p>'
                  });
                  if(data.data.status==1){
                      if(that.attr('myfollower')==1){
                          that.find('i').html('&#xe61c;');
                          that.find('cite').text('相互关注');
                          that.removeClass('followSpace').addClass('unFollowSpace');
                      }
                  }
              });
          });
      }
};
