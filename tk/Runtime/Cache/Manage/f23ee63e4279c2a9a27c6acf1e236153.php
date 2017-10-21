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
var URL = '/Manage/System';
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
<style>
    *{margin:0;padding:0;}
body{font-size:14px;font-family:"Microsoft YaHei";}
ul,li{list-style:none;}

#tab{position:relative;}
#tab .tabList ul li{
    float:left;
    background:#fefefe;
    background:-moz-linear-gradient(top, #fefefe, #ededed);
    background:-o-linear-gradient(left top,left bottom, from(#fefefe), to(#ededed));
    background:-webkit-gradient(linear,left top,left bottom, from(#fefefe), to(#ededed));
    border:1px solid #ccc;
    padding:5px 0;
    width:100px;
    text-align:center;
    margin-left:-1px;
    position:relative;
    cursor:pointer;
}
#tab .tabCon{
    position:absolute;
    left:-1px;
    top:32px;
    width:100%;
}
#tab .tabCon .list td,#tab .tabCon .list tr,#tab .tabCon .list table{border:1px solid #ccc;
padding:2px;}
#tab .tabCon input{background: #fff;}
#tab .tabCon textarea{background: #fff;line-height:1.4em;}
#tab .tabCon .list td .deltr{margin-left:5px;color:#c00;}
#tab .tabCon .list td .deltr:hover{text-decoration: underline;}
#tab .tabCon .list input.large{width:100%;height:2.8em; line-height:2.8em;float: left;border:none;padding:3px 0;text-indent: 5px;font-size: 13px;}
#tab .tabCon .list textarea.large{resize:vertical;width:100%;float: left;border:none;padding:3px 0;text-indent: 5px;font-size: 13px;}
#tab .tabCon .list input.large:hover,#tab .tabCon .list input.large:focus,#tab .tabCon .list textarea.large:hover,#tab .tabCon .list textarea.large:focus{outline:1px solid #75B8FC;outline-offset:0px;}
#tab .tabCon div{
    padding:10px;
    opacity:0;
    filter:alpha(opacity=0);
}
#tab .tabList li.cur{
    border-bottom:none;
    background:#fff;
}
#tab .tabCon div.cur{
    opacity:1;
    filter:alpha(opacity=100);
}
.addTr{display: block;width:60px;text-align: center;line-height: 20px;
    padding:2px 6px;border-radius: 3px;border:#aaa 1px solid;
    background-color:#C3E1F2;
    margin-bottom:5px;
}
.addTr:hover{opacity: 0.8;filter:alpha(opacity=80);
    cursor:pointer;
}
.deltr:hover{
    cursor:pointer;
}
</style>
<!-- 主页面开始 -->
<div id="main" class="main">
    <!-- 主体内容  -->
    <div class="content">
        <div class="title"><?php echo ($pageName); ?> [<a href="<?php echo U('System/updateCache');?>">更新缓存</a>] [<a href="<?php echo U('System/pickJs');?>">压缩js文件</a>]</div>
        <!--  功能操作区域  -->
        <!-- 功能操作区域结束 -->
        <div>
            <div id="result" class="result none"></div>
            <form method="POST" action="" id="form1">
                    <div id="tab">
                        <div class="tabList">
                            <ul>
                                <li class="cur">官网</li>
                                <li>组卷</li>
                                <li>提分</li>
                                <li>教师端</li>
                                <li>考试</li>
                                <li>后台</li>
                            </ul>
                        </div>
                        <div class="tabCon">
                            <div class="cur">                               
                                <table cellpadding="0" cellspacing="0" class="list" border="0">
                                    <tr>
                                        <font class='addTr' classname='indexTr'>添加一行</font>
                                    </tr>
                                    <tr>
                                        <th class="tright" width="31%">字段名称</th>
                                        <th class="tright" width="31%">字段内容</th>
                                        <th class="tright" width="31%">字段描述</th>
                                        <th class="tright" width="6%">操作</th>
                                    </tr>
                                    <?php if($edit["Index"]): if(is_array($edit['Index'])): $i = 0; $__LIST__ = $edit['Index'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class='indexTr'>
                                            <td class="tLeft">
                                                <input type='hidden' bs='Index_SysID_' NAME='Index_SysID_<?php echo ($key); ?>' value='<?php echo ($node["SysID"]); ?>'>
                                                <input type="text" class="large bLeft copyname" bs='Index_Name_' NAME="Index_Name_<?php echo ($key); ?>" value="<?php echo ($node["FieldName"]); ?>" />
                                            </td>
                                            <td class="tLeft">
                                                <textarea type="text" class="large bLeft"  bs='Index_Cont_' NAME="Index_Cont_<?php echo ($key); ?>"><?php echo ($node["Content"]); ?></textarea>
                                            </td>
                                            <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Index_Desc_' NAME="Index_Desc_<?php echo ($key); ?>" ><?php echo ($node["Description"]); ?></textarea>
                                            </td>
                                            <td class="tLeft">
                                                <font class='deltr' style="color:red;" SysID='<?php echo ($node["SysID"]); ?>'>删除</font>
                                            </td>
                                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                    <?php else: ?>
                                    <tr class='indexTr'>
                                        <td class="tLeft">
                                            <input type='hidden' bs='Index_SysID_' NAME='Index_SysID_0' value=''>
                                            <input type="text" class="large bLeft copyname" bs='Index_Name_' NAME="Index_Name_0" value="" />
                                        </td>
                                        <td class="tLeft">
                                            <textarea type="text" class="large bLeft "  bs='Index_Cont_' NAME="Index_Cont_0"></textarea>
                                        </td>
                                        <td class="tLeft">
                                        <textarea type="text" class="large bLeft" bs='Index_Desc_' NAME="Index_Desc_0" ></textarea>
                                        </td>
                                        <td class="tLeft">
                                            <font class='deltr' style="color:red;" SysID=''>删除</font>
                                        </td>
                                    </tr><?php endif; ?>
                                </table>
                            </div>
                            <div>
                               <table cellpadding="0" cellspacing="0" class="list" border="0">
                                    <tr>
                                        <font class='addTr' classname='homeTr'>添加一行</font>
                                    </tr>
                                    <tr>
                                        <th class="tright" width="31%">字段名称</th>
                                        <th class="tright" width="31%">字段内容</th>
                                        <th class="tright" width="31%">字段描述</th>
                                        <th class="tright" width="6%">操作</th>
                                       
                                    </tr>
                                    <?php if($edit["Home"]): if(is_array($edit['Home'])): $i = 0; $__LIST__ = $edit['Home'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class='homeTr'>
                                            <td class="tLeft">
                                                <input type='hidden' bs='Home_SysID_' NAME='Home_SysID_<?php echo ($key); ?>' value='<?php echo ($node["SysID"]); ?>'>
                                                <input type="text" class="large bLeft copyname" bs='Home_Name_' NAME="Home_Name_<?php echo ($key); ?>" value="<?php echo ($node["FieldName"]); ?>" />
                                            </td>
                                            <td class="tLeft">
                                                <textarea type="text" class="large bLeft" bs='Home_Cont_' NAME="Home_Cont_<?php echo ($key); ?>" ><?php echo ($node["Content"]); ?></textarea>
                                            </td>
                                            <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Home_Desc_' NAME="Home_Desc_<?php echo ($key); ?>" ><?php echo ($node["Description"]); ?></textarea>
                                            </td>
                                            <td class="tLeft">
                                                <font class='deltr' style="color:red;" SysID='<?php echo ($node["SysID"]); ?>'>删除</font>
                                            </td>                                            
                                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                    <?php else: ?>
                                        <tr class='homeTr'>
                                            <td class="tLeft">
                                                <input type='hidden' bs='Home_SysID_' NAME='Home_SysID_0' value=''>
                                                <input type="text" class="large bLeft copyname" bs='Home_Name_' NAME="Home_Name_0" value="" />
                                            </td>
                                            <td class="tLeft">
                                                <textarea type="text" class="large bLeft" bs='Home_Cont_' NAME="Home_Cont_0"></textarea>
                                            </td>
                                            <td class="tLeft">
                                                <textarea type="text" class="large bLeft" bs='Home_Desc_' NAME="Home_Desc_0" ></textarea>
                                            </td>
                                             <td class="tLeft">
                                            <font class='deltr' style="color:red;" SysID=''>删除</font>
                                            </td>
                                        </tr><?php endif; ?>
                                </table>
                            </div>
                            <div>
                                <table cellpadding="0" cellspacing="0" class="list" border="0">
                                    <tr>
                                        <font class='addTr' classname='AatTr'>添加一行</font>
                                    </tr>
                                    <tr>
                                        <th class="tright" width="31%">字段名称</th>
                                        <th class="tright" width="31%">字段内容</th>
                                        <th class="tright" width="31%">字段描述</th>
                                        <th class="tright" width="6%">操作</th>
                                       
                                    </tr>
                                    <?php if($edit["Aat"]): if(is_array($edit['Aat'])): $i = 0; $__LIST__ = $edit['Aat'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class='AatTr'>
                                        <td class="tLeft">
                                            <input type='hidden' bs='Aat_SysID_' NAME='Aat_SysID_<?php echo ($key); ?>' value='<?php echo ($node["SysID"]); ?>'>
                                            <input type="text" class="large bLeft copyname" bs='Aat_Name_' NAME="Aat_Name_<?php echo ($key); ?>" value="<?php echo ($node["FieldName"]); ?>"/>
                                        </td>
                                        <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Aat_Cont_' NAME="Aat_Cont_<?php echo ($key); ?>"><?php echo ($node["Content"]); ?></textarea>
                                        </td>
                                        <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Aat_Desc_' NAME="Aat_Desc_<?php echo ($key); ?>"><?php echo ($node["Description"]); ?></textarea>
                                        </td>
                                        <td class="tLeft">
                                            <font class='deltr' style="color:red;" SysID='<?php echo ($node["SysID"]); ?>'>删除</font>
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                    <?php else: ?>
                                     <tr class='AatTr'>
                                        <td class="tLeft">
                                            <input type='hidden' bs='Aat_SysID_' NAME='Aat_SysID_0' value=''>
                                            <input type="text" class="large bLeft copyname" bs='Aat_Name_' NAME="Aat_Name_0" value=""/>
                                        </td>
                                        <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Aat_Cont_' NAME="Aat_Cont_0"></textarea>
                                        </td>
                                        <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Aat_Desc_' NAME="Aat_Desc_0"></textarea>
                                        </td>
                                        <td class="tLeft">
                                            <font class='deltr' style="color:red;" SysID=''>删除</font>
                                        </td>
                                    </tr><?php endif; ?>
                                </table>
                            </div>
                            <div>
                                <table cellpadding="0" cellspacing="0" class="list" border="0">
                                    <tr>
                                        <font class='addTr' classname='teacherTr'>添加一行</font>
                                    </tr>
                                    <tr>
                                        <th class="tright" width="31%">字段名称</th>
                                        <th class="tright" width="31%">字段内容</th>
                                        <th class="tright" width="31%">字段描述</th>
                                        <th class="tright" width="6%">操作</th>
                                       
                                    </tr>
                                    <?php if($edit["Teacher"]): if(is_array($edit['Teacher'])): $i = 0; $__LIST__ = $edit['Teacher'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class='teacherTr'>
                                        <td class="tLeft">
                                            <input type='hidden' bs='Teacher_SysID_' NAME='Teacher_SysID_<?php echo ($key); ?>' value='<?php echo ($node["SysID"]); ?>'>
                                            <input type="text" class="large bLeft copyname" bs='Teacher_Name_' NAME="Teacher_Name_<?php echo ($key); ?>" value="<?php echo ($node["FieldName"]); ?>" />
                                        </td>
                                        <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Teacher_Cont_' NAME="Teacher_Cont_<?php echo ($key); ?>" ><?php echo ($node["Content"]); ?></textarea>
                                        </td>
                                        <td class="tLeft">
                                        <textarea type="text" class="large bLeft" bs='Teacher_Desc_' NAME="Teacher_Desc_<?php echo ($key); ?>" ><?php echo ($node["Description"]); ?></textarea>
                                        </td>
                                        <td class="tLeft">
                                            <font class='deltr' style="color:red;" SysID='<?php echo ($node["SysID"]); ?>'>删除</font>
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                    <?php else: ?>
                                    <tr class='teacherTr'>
                                        <td class="tLeft">
                                            <input type='hidden' bs='Teacher_SysID_' NAME='Teacher_SysID_0' value=''>
                                            <input type="text" class="large bLeft copyname" bs='Teacher_Name_' NAME="Teacher_Name_0" value="" />
                                        </td>
                                        <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Teacher_Cont_' NAME="Teacher_Cont_0" ></textarea>
                                        </td>
                                        <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Teacher_Desc_' NAME="Teacher_Desc_0" ></textarea>
                                        </td>
                                        <td class="tLeft">
                                            <font class='deltr' style="color:red;" SysID=''>删除</font>
                                        </td>
                                    </tr><?php endif; ?>
                                </table>
                            </div>
                            <div>
                                <table cellpadding="0" cellspacing="0" class="list" border="0">
                                    <tr>
                                        <font class='addTr' classname='examTr'>添加一行</font>
                                    </tr>
                                    <tr>
                                        <th class="tright" width="31%">字段名称</th>
                                        <th class="tright" width="31%">字段内容</th>
                                        <th class="tright" width="31%">字段描述</th>
                                        <th class="tright" width="6%">操作</th>

                                    </tr>
                                    <?php if($edit["Exam"]): if(is_array($edit['Exam'])): $i = 0; $__LIST__ = $edit['Exam'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class='examTr'>
                                        <td class="tLeft">
                                            <input type='hidden' bs='Exam_SysID_' NAME='Exam_SysID_<?php echo ($key); ?>' value='<?php echo ($node["SysID"]); ?>'>
                                            <input type="text" class="large bLeft copyname" bs='Exam_Name_' NAME="Exam_Name_<?php echo ($key); ?>" value="<?php echo ($node["FieldName"]); ?>" />
                                        </td>
                                        <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Exam_Cont_' NAME="Exam_Cont_<?php echo ($key); ?>" ><?php echo ($node["Content"]); ?></textarea>
                                        </td>
                                        <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Exam_Desc_' NAME="Exam_Desc_<?php echo ($key); ?>" ><?php echo ($node["Description"]); ?></textarea>
                                        </td>
                                        <td class="tLeft">
                                            <font class='deltr' style="color:red;" SysID='<?php echo ($node["SysID"]); ?>'>删除</font>
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                    <?php else: ?>
                                    <tr class='examTr'>
                                        <td class="tLeft">
                                            <input type='hidden' bs='Exam_SysID_' NAME='Exam_SysID_0' value=''>
                                            <input type="text" class="large bLeft copyname" bs='Exam_Name_' NAME="Exam_Name_0" value="" />
                                        </td>
                                        <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Exam_Cont_' NAME="Exam_Cont_0" ></textarea>
                                        </td>
                                        <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Exam_Desc_' NAME="Exam_Desc_0" ></textarea>
                                        </td>
                                        <td class="tLeft">
                                            <font class='deltr' style="color:red;" SysID=''>删除</font>
                                        </td>
                                    </tr><?php endif; ?>
                                </table>
                            </div>
                            <div>
                                <table cellpadding="0" cellspacing="0" class="list" border="0">
                                    <tr>
                                        <font class='addTr' classname='adminTr'>添加一行</font>
                                    </tr>
                                    <tr>
                                        <th class="tright" width="31%">字段名称</th>
                                        <th class="tright" width="31%">字段内容</th>
                                        <th class="tright" width="31%">字段描述</th>
                                        <th class="tright" width="6%">操作</th>

                                    </tr>
                                    <?php if($edit["Admin"]): if(is_array($edit['Admin'])): $i = 0; $__LIST__ = $edit['Admin'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$node): $mod = ($i % 2 );++$i;?><tr class='adminTr'>
                                        <td class="tLeft">
                                            <input type='hidden' bs='Admin_SysID_' NAME='Admin_SysID_<?php echo ($key); ?>' value='<?php echo ($node["SysID"]); ?>'>
                                            <input type="text" class="large bLeft copyname" bs='Admin_Name_' NAME="Admin_Name_<?php echo ($key); ?>" value="<?php echo ($node["FieldName"]); ?>" />
                                        </td>
                                        <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Admin_Cont_' NAME="Admin_Cont_<?php echo ($key); ?>" ><?php echo ($node["Content"]); ?></textarea>
                                        </td>
                                        <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Admin_Desc_' NAME="Admin_Desc_<?php echo ($key); ?>" ><?php echo ($node["Description"]); ?></textarea>
                                        </td>
                                        <td class="tLeft">
                                            <font class='deltr' style="color:red;" SysID='<?php echo ($node["SysID"]); ?>'>删除</font>
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                    <?php else: ?>
                                    <tr class='adminTr'>
                                        <td class="tLeft">
                                            <input type='hidden' bs='Admin_SysID_' NAME='Admin_SysID_0' value=''>
                                            <input type="text" class="large bLeft copyname" bs='Admin_Name_' NAME="Admin_Name_0" value="" />
                                        </td>
                                        <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Admin_Cont_' NAME="Admin_Cont_0" ></textarea>
                                        </td>
                                        <td class="tLeft">
                                            <textarea type="text" class="large bLeft" bs='Admin_Desc_' NAME="Admin_Desc_0" ></textarea>
                                        </td>
                                        <td class="tLeft">
                                            <font class='deltr' style="color:red;" SysID=''>删除</font>
                                        </td>
                                    </tr><?php endif; ?>
                                </table>
                            </div>
                        </div>
                     </div>
                    <div>
                        <TD ></TD>
                        <div class="handle-save-empty"><div style="width:85%;margin:5px 5px 5px 600px">
                        <div class="impBtn fLeft"><INPUT tag='form1' u="<?php echo U('System/save');?>" TYPE="button" value="保存" class="save imgButton mysubmit"></div>
                        <div class="impBtn fLeft m-l10"><INPUT TYPE="reset" class="reset imgButton" value="清空" ></div>
                        </div></div>
                    </div>
            </form>
        </div>
        <!-- 列表显示区域结束 -->
    </div>
    <!-- 主体内容结束 -->
</div>
<script>
/**
 * 选项卡
 */
window.onload = function() {
    var oDiv = document.getElementById("tab");
    var oLi = oDiv.getElementsByTagName("div")[0].getElementsByTagName("li");
    var aCon = oDiv.getElementsByTagName("div")[1].getElementsByTagName("div");
    var timer = null;
    show(0);
    for (var i = 0; i < oLi.length; i++) {
        oLi[i].index = i;
        oLi[i].onclick = function() {
            show(this.index);
        }
    }
    function show(a) {
        index = a;
        var alpha = 0;
        for (var j = 0; j < oLi.length; j++) {
            oLi[j].className = "";
            aCon[j].className = "";
            aCon[j].style.opacity = 0;
            aCon[j].style.filter = "alpha(opacity=0)";
            aCon[j].style.display = 'none';
        }
        aCon[index].style.display = 'block';
        oLi[index].className = "cur";
        clearInterval(timer);
        timer = setInterval(function() {
            alpha += 2;
            alpha > 100 && (alpha = 100);
            aCon[index].style.opacity = alpha / 100;
            aCon[index].style.filter = "alpha(opacity=" + alpha + ")";
            alpha == 100 && clearInterval(timer);
        },
        5)
    }
}
/**
 * 复制一行，并修改名称参数
 */
$('.addTr').click(function(){
        var className=$(this).attr('classname');
        $('.'+className).last().after($('.'+className).eq(0).clone());
        $('.'+className).last().find('input').val('');
        $('.'+className).last().find('font').eq(0).attr({'SysID':''});
        $('.'+className).each(function(i){
            var newname=$(this).find('td').eq(0).children().eq(0).attr('bs')+i;
            var new2name=$(this).find('td').eq(0).children().eq(1).attr('bs')+i;
            $(this).find('td').eq(0).children().eq(0).attr({'name':newname});
            $(this).find('td').eq(0).children().eq(1).attr({'name':new2name});
            var contname=$(this).find('td').eq(1).children().attr('bs')+i;
            $(this).find('td').eq(1).children().attr({'name':contname}); 
            var descname=$(this).find('td').eq(2).children().attr('bs')+i;
            $(this).find('td').eq(2).children().attr({'name':descname});
            
        })
})
/**
 * 判断字段名称重复 
 */
$(".copyname").live('focusout',function() {
    var tagname=$(this).val();
    var inputname=$(this).attr('name');
    $(this).attr({'use':'used'})
    var clname=$(this).parent().parent().attr('class');
    $('.'+clname).each(function(){
        if($(this).find('input').eq(1).val()==tagname && $(this).find('input').eq(1).attr('use')!='used'){
            if(confirm('字段名称重复！')){
                $('input[name="'+inputname+'"]').val('');
            }else{
                $('input[name="'+inputname+'"]').val('');
            }
        }
    })
    $(this).attr({'use':''})
});
//删除行
$('.deltr').live('click',function(){
    var clname=$(this).parent().parent().attr('class');
    if($('.'+clname).length==1){ //如果只有一行，不执行删除
        return false;
    }
    var sysID=$(this).attr('SysID');
    var obj=$(this);
    if(sysID!=''){ 
        if(!confirm('确定要删除该条记录么？')) return false;
        $.post(U('System/delete'),{'SysID':sysID},function(data){
                var msg=data['data'];
                if(msg['msg']=='ok'){
                    obj.parent().parent().remove();
                }else{
                    alert('删除失败！');
                }
            })
    }else{
        $(this).parent().parent().remove();
    }
})

</script>
<!-- 主页面结束 -->

</body>
</html>