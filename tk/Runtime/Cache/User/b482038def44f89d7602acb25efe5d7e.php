<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html lang="zh-CN">
<head>
<title>智慧云题库云平台</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
<link rel="stylesheet" href="/Public/index/css/wln-base.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"/>
<link rel="stylesheet" href="/Public/index/css/style.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"/>
</head>
<body>
<div class="hzb">
<div class="title"><h5>提示</h5><span class="an_gb"><a href="javascript:history.go(-1)"></a></span></div>
<div class="hzb_box">
<table  border="0" align="center" cellpadding="0" cellspacing="0" class="ts_nr">
<tr>
    <td  style="font-size:20px;"><span class="cw"></span><?php echo ($msg_detail); ?></td>
</tr>
</table>
<div class="fb-btn-wrap"><input class="nor-btn" type="button" id="submit01" value="确定" ><a class="back-zujuan" href="<?php echo U('/Home','',false);?>">返回组卷系统</a></div>
</div>
<script>
$(function(){
    var url = '<?php echo ($jumpUrl); ?>';
    $('#submit01').on('click',function(){
        if(url.toLowerCase().indexOf('javascript') != -1){
            window.history.back(-1);
        }else{
               window.location.href=url;
        }
        
    });
});
</script>
</div>
</body>
</html>