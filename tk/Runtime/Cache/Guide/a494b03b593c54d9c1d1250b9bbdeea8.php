<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<TITLE>『<?php echo (C("WLN_WEB_NAME")); ?>管理平台』</TITLE>
<link rel="stylesheet" type="text/css" href="/Public/zjadmin/css/style.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" />
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
<!--基础文件，分别是jQuery基库和拖拽UI插件-->
<script src="/Public/plugin/jquery.ui.draggable.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" type="text/javascript"></script>
<script src="/Public/plugin/testOperation.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" type="text/javascript"></script>
<!-- 对话框核心JS文件和对应的CSS文件-->
<script src="/Public/plugin/alert/jquery.alerts.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" type="text/javascript"></script>
<link href="/Public/plugin/alert/jquery.alerts.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="/Public/plugin/ueditor/ueditor.config.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/ueditor/ueditor.all.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/editor.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script LANGUAGE="JavaScript">
//指定当前组模块URL地址 
var URL = '/Guide/CaseMyLore';
var APP     =     '';
var PUBLIC = '/Public';
var softdog=0;
<?php if(C("openKeysoft")== 1): ?>softdog=1;<?php endif; ?>
</script>
<script type="text/javascript" src="/Public/zjadmin/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
</HEAD>

<body>
<?php if(C("openKeysoft")== 1): ?><div style="display:none;"><embed id="s_simnew31"  type="application/npsyunew3-plugin" hidden="true"> </embed></div><?php endif; ?>
<div id="loader" >页面加载中...</div>
<script language="javascript"></script>
<script type="text/javascript" src="/Public/plugin/ueditor/ueditor.config.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/ueditor/ueditor.all.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/editor.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<!-- 主页面开始 -->
<div id="main" class="main" >
    <!-- 主体内容  -->
    <div class="content" >
        <div class="title">
            <?php echo ($pageName); ?> [
            <A HREF="javascript:history.go(-1);">返回上一页</A>
            ]
        </div>
        <!--  功能操作区域  -->
        <div class="operate">
            <!--<div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="新增" onclick="" class="btadd add imgButton"></div>-->
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="" class="btdelete delete imgButton"></div>
    <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="revert" value="刷新" onclick="" class="btflush revert imgButton"></div>
            <!-- 查询区域 -->
            <FORM METHOD="POST" ACTION="/Guide/CaseMyLore">
                <div class="fRig">
                    <div class="fLeft">
                        <span id="key">
                            <INPUT id="name" TYPE="text" NAME="name" value="<?php echo ($_REQUEST['name']); ?>" title="试题编号查询" class="medium" ></span>
                    </div>
                    <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class=" search imgButton"></div>
        <div class="impBtn hMargin fLeft shadow" ><input type="button" id="showText" name="adv" value="高级" onclick="" class=" adv imgButton"></div>
                </div>
                <!-- 高级查询区域 -->
                <div id="searchM" class=" none search cBoth">
                    <TABLE border="0" cellpadding="1" cellspacing="3" width="100%">
                        <TR>
                            <TD class="tRight" width="80">知识编号：</TD>
                            <TD>
                                <INPUT TYPE="text" NAME="LoreID" class="small" value="<?php echo ($_REQUEST['TestID']); ?>" >
                            </TD>
                            <TD class="tRight" width="80">板块：</TD>
                            <TD>
                                <select id="queryForum" title='板块不会被单独查询'>
                                    <option value="">请选择</option>
                                </select>
                            </TD>
                            <TD class="tRight" width="80">栏目：</TD>
                            <TD>
                                <select id="queryMenu" name='MenuID'>
                                    <option value="">请先选择栏目</option>
                                </select>
                            </TD>
                        </tr>
                    </TABLE>
                </div>
            </FORM>
        </div>
        <!-- 功能操作区域结束 -->

        <!-- 列表显示区域  -->
        <div class="list" >
            <div id="result" class="result none"></div>
            <!-- Think 系统列表组件开始 -->
            <table id="checkList" class="list" cellpadding="5" cellspacing="0" border="1">
                <tr>
                    <td height="5" colspan="8" class="topTd" ></td>
                </tr>
                <tr class="row" >
                    <th width="8">
                        <input type="checkbox" id="CheckAll" tag="checkList"></th>
                    <th width="30">编号</th>
                    <th width="50">学科</th>
                    <th width="600">内容</th>
                    <th width="60">板块</th>
                    <th width="60">栏目</th>
                    <th width="180">章节</th>
                    <th width="45">操作</th>
                </tr>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class="row lists" jl=''>
                    <td>
                        <input type="checkbox" class="key" value="<?php echo ($node["LoreID"]); ?>"></td>
                    <td><?php echo ($node["LoreID"]); ?></td>
                    <td><?php echo ($node["SubjectName"]); ?></td>
                    <td width="400">
                        <div class="testdivbak">
                            <a style="cursor:pointer" class="lore editLore" thisid="<?php echo ($node["LoreID"]); ?>">
                                <p><?php echo ((isset($node["Lore"]) && ($node["Lore"] !== ""))?($node["Lore"]):"无"); ?></p>
                            </a>
                        </div>
                    </td>
                    <td class="forum">
                        <?php echo ((isset($node["ForumName"]) && ($node["ForumName"] !== ""))?($node["ForumName"]):" <font color='red'>无</font>
                        "); ?>
                    </td>
                    <td class="menu">
                        <?php echo ((isset($node["MenuName"]) && ($node["MenuName"] !== ""))?($node["MenuName"]):" <font color='red'>无</font>
                        "); ?>
                    </td>
                    <td class="chapter">
                        <?php echo ((isset($node["ChapterName"]) && ($node["ChapterName"] !== ""))?($node["ChapterName"]):" <font color='red'>无</font>
                        "); ?>
                    </td>
                    <td>
                    <a href="javascript:void(0);" class="editLore" thisid="<?php echo ($node["LoreID"]); ?>">修改</a>
                    &nbsp;&nbsp;<br/>
                    <a href="javascript:void(0);" class="delLore" thisid="<?php echo ($node["LoreID"]); ?>">删除</a>
                </td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            <tr>
                <td height="5" colspan="8" class="bottomTd"></td>
            </tr>
        </table>
        <!-- Think 系统列表组件结束 --> </div>
    <!--  分页显示区域 -->
    <div class="page"><?php echo ($page); ?></div>
    <!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<script>
    var forums = new Object(<?php echo ($forums); ?>);
    var menuArray = new Object(<?php echo ($menuArray); ?>);
    //添加版块查询
    var qf = $('#queryForum');
    for(var forum in forums){
        qf.append('<option value="'+forum+'">'+forums[forum]['name']+'</option>');
    }
    //添加栏目查询
    var qm = $('#queryMenu');
    qf.change(function(){
        qm.find('option').eq(0).nextAll().remove();
        addMenu(qm, '', $(this).val());
    });
    $('.save').live('click',function(){
        var forumid = $('#forumsMenu').val();
        if($('#caseMenu').find('option:selected').val()==''){
            alert('请选择所属栏目');
            return false;
        }
        var menuID=$('#caseMenu').val();
        if($('.cp').length<1){
            alert('请选择章节');
            return false;
        }
        var chapterList = $('.cp').last().val();

        var loreID=$('.loreID').attr('value');
        var lore = $.Editor.instance['Lore'].getContent();
        var answer = $.Editor.instance['Answer'].getContent();
        $.post(U('Guide/CaseMyLore/save'),{'LoreID':loreID,'MenuID':menuID,'ChapterID':chapterList,'Lore':lore, 'Answer':answer},function(data){
            if(checkPower(data)=='error'){
                return false;
            }
            msg = data['data'];
            var chapterName=msg['ChapterName'];
            var obj;
            $('input[class="key"]').each(function(){
                if($(this).val()==loreID){
                    obj=$(this).parent().parent();
                }
            })
            obj = $(obj);
            obj.find('.lore').html('').html(lore);
            obj.find('.chapter').html(chapterName);
            obj.find('.menu').html(menuArray[menuID]['MenuName']);
            obj.find('.forum').html(forums[forumid]['name']);
            imgResize(obj)
            alert('修改成功！');
            $('#popup_container').remove();
            $("#popup_overlay").remove();
        })
    })
    $('.editLore').live('click',function(){
        var loreID=$(this).attr('thisid');
        jInfo('加载中请稍候。。。','加载数据');
        $.post(U('Guide/CaseMyLore/edit?LoreID='+loreID+'&'+Math.random()),function(data){
            //权限验证
            if(checkPower(data)=='error'){
                return false;
            }
            jFrame(data['data'],'编辑试题：编号'+loreID);
        })
    })

    $('.delLore').live('click', function(){
        if(!window.confirm('确定删除该数据？')){
            return false;
        }
        var that = $(this);
        var id = that.attr('thisid');
        var params = {
            'id' : id,
            'r' : Math.random()
        };
        $.post(U('Guide/CaseMyLore/delete'),params, function(data){
            if(checkPower(data)=='error'){
                return false;
            }
            that.parents('tr').remove();
            alert('删除成功！');
        });
    });

    $('.testdivbak').each(function(){
        imgResize($(this));
    });
    $('.selectChapter').chapterSelectChange('/Guide/CaseMyLore');
    
    //添加栏目信息
    function addMenu(obj, menuid, forumid){
        for(var menu in menuArray){
            menu = menuArray[menu];
            var html = '';
            if(forumid && forumid == menu.ForumID){
                if(menuid && menu.MenuID == menuid){
                    html = '<option value="'+menu.MenuID+'" selected="selected">'+menu.MenuName+'</option>'
                }else{
                    html = '<option value="'+menu.MenuID+'">'+menu.MenuName+'</option>'
                }
                obj.append($(html));
            }
        }
    }

    function imgResize(obj){
        obj.find('img').each(function(){
            var that = $(this);
            if(that.width() > 100){
                that.width(150);
            }
        });
    }
</script>
<!-- 主页面结束 -->

</body>
</html>