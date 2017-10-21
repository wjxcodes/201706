<?php if (!defined('THINK_PATH')) exit();?><div class="specialC" style="display: none;">
    <ul class="list_zsd">
    </ul>
    <div class="errorMsg"></div>
    <div class="submitMsg" style="display: none;">
        <div style="position: absolute;left: 30%;top: 5px;">正在为您智能组卷，请稍等...</div>
    </div>
    <script id="specialListTpl" type="text/html">
        {%each data as i%}
        <li>
            <div class="zsd_bt fl" style="cursor: pointer">
                <a class="ico_zd {%if i.sub%}ico_zd_01{%else%}ico_zd_03{%/if%} fl"></a>{%i.klName%}
            </div>
            {%#star(i.rightAmount,i.rightWrongAmount)%}
            <div class="an_box fr" data="{%i.klID%}">
                <a href="javascript:;" class="an01">
                    <span class="an_left"></span><span class="an_cen">开始练习</span><span class="an_right"></span>
                </a>
            </div>
        </li>
        <ul style="display: none;">
            {%each i.sub as j%}
            <li class="lidj02">
                <div class="zsd_bt fl">
                    <a class="ico_zd ico_zd_03 fl"></a>{%j.klName%}
                </div>
                {%#star(j.rightAmount,j.rightWrongAmount)%}
                <div class="an_box fr" data="{%j.klID%}">
                    <a href="javascript:;" class="an01">
                        <span class="an_left"></span><span class="an_cen">开始练习</span><span class="an_right"></span>
                    </a>
                </div>
            </li>
            {%/each%}
        </ul>
        {%/each%}
    </script>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        AatKnowlodgeDialog = {
            c:$('.specialC'),
            _getList:function(){
                //post获取数据
                var c = this.c;
                $.post(U('Default/ajaxKl'), {'subject_id': AatCommon.getSubjectID(),times:Math.random()}, function (e) {
                    $('#dialogAat .contentLoading').hide();
                    c.show();
                    if (e.status == 1) {
                        template.helper('star', AatCommon.star);
                        var listTpl = template('specialListTpl',e);
                        c.find('.list_zsd').html(listTpl);
                    } else {
                        c.find('.list_zsd').html(e.data);
                    }
                });
            },
            showList:function(){
                var c = this.c;
                this._getList();
                //绑定滚动事件
                c.find('.list_zsd').slimScroll({
                    height:c.find('.list_zsd').height(),
                    alwaysVisible: true
                });
                //树形结构绑定点击事件
                c.on('click', '.zsd_bt', function () {
                    $(this).parent().find('.ico_zd_01').switchClass('ico_zd_01', 'ico_zd_02', 1);
                    $(this).parent().find('.ico_zd_02').switchClass('ico_zd_02', 'ico_zd_01', 1);
                    $(this).parent().next('ul').toggle('blind');
                });
            },
            start:function(){
                var c = this.c;
                //点击开始做题事件绑定
                c.on('click', '.an_box', function () {
                    var kl_id = $(this).attr('data');
                    //隐藏错误信息
                    c.find('.errorMsg').hide();
                    //隐藏出题按钮
                    c.find('.an_box').hide();
                    //显示进度条
                    c.find('.submitMsg').progressbar({
                        value: false
                    }).show();
                    $.post(U('Default/ajaxGetTest'), {
                        'id': 2,
                        'SubjectID': AatCommon.getSubjectID(),
                        'KID': kl_id,
                        times:Math.random()
                    }, function (e) {
                        if (e.status == 1) {
                            window.location.href = U('Exercise/index?id=' + e.data.record_id);
                        } else {
                            //隐藏进度条
                            c.find('.submitMsg').hide();
                            //显示出题按钮
                            c.find('.an_box').show();
                            //显示错误信息
                            c.find('.errorMsg').html(e.data).show().effect('shake');
                        }
                    });
                });
            },
            init:function(){
                this.c.unbind();//取消上次的绑定事件
                this.showList();//显示知识点列表
                this.start();//点击开始测试事件
            }
        };
        AatKnowlodgeDialog.init();

    });
</script>