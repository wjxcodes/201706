<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<TITLE>『<?php echo (C("WLN_WEB_NAME")); ?>管理平台』</TITLE>
<link rel="stylesheet" type="text/css" href="/Public/zjadmin/css/style.css" />
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="/Public/plugin/tree1.js"></script>
<style>
html{overflow-x : hidden;}
ul{margin:0px;padding:0px;}
#files ul li.on{font-weight:600;background-color:#cdd }
#files ul li.on a:active,#files ul li.on a:focus{outline:none;border:none}
#files a{display: block;}

</style>
<base target="main" />
<script language='javascript'>
$(function(){
    $('#files').tree({
        expanded: 'li:first'
    });
});
</script>
</HEAD>
<body>
<div id="menu" class="menu">
<TABLE class="list shadow noborder" cellpadding="5" cellspacing="0" border="1">
<tr>
    <td height='5' colspan=7 class="topTd" ></td>
</tr>
<TR class="row" >
    <th class="tCenter space"><IMG SRC="/Public/zjadmin/images/home.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="" align="absmiddle"> <?php if(isset($_GET['title'])): echo ($_GET['title']); endif; if(!isset($_GET['title'])): ?>后台首页<?php endif; ?> </th>
</TR>
<TR>
    <TD><table border='0'><tr><td>
<ul id="files">
<?php if(is_array($menu)): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><li><A HREF="#" id="<?php echo ($key); ?>" title="<?php echo ($item['Description']); ?>"><?php echo ($item['MenuName']); ?></A>
        <?php if($item['sub']): ?><ul>
        <?php if(is_array($item['sub'])): $i = 0; $__LIST__ = $item['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U($sub['NewMenuUrl']);?>" title="<?php echo ($sub['Description']); ?>"><?php echo ($sub['MenuName']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul><?php endif; ?>
    </li><?php endforeach; endif; else: echo "" ;endif; ?>
</ul></td></tr></table></td>
</TR>
<tr>
    <td height='5' colspan=7 class="bottomTd"></td>
</tr>
</TABLE>
</div>
</body>

<script type="text/javascript">
    $(function(){
        var li = $("#files ul li");
        li.click(function(){
        var e = $(this);
            li.removeClass("on");
            e.addClass("on");
            // alert(e.parent().html());

        })
    })
</script>
</html>