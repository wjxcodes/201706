<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<TITLE>『<?php echo (C("WLN_WEB_NAME")); ?>管理平台』</TITLE>
<FRAMESET FRAMEBORDER=0 framespacing=0 border=0 rows="50, *,32">
<FRAME SRC="<?php echo U('Public/top');?>" name="top" FRAMEBORDER=0 NORESIZE SCROLLING='no' marginwidth=0 marginheight=0>
<FRAMESET FRAMEBORDER=0  framespacing=0 border=0 COLS="200,7, *" id="frame-body">
    <FRAME SRC="<?php echo U('Public/menu');?>" FRAMEBORDER=0 id="menu-frame" name="menu">
    <frame src="<?php echo U('Public/drag');?>" id="drag-frame" name="drag-frame" frameborder="no" scrolling="no">
    <FRAME SRC="<?php echo U('Public/'.$jumpParam);?>" FRAMEBORDER=0 id="main-frame" name="main">
</FRAMESET>
<FRAME SRC="<?php echo U('Public/footer');?>" name="footer" FRAMEBORDER=0 NORESIZE SCROLLING='no' marginwidth=0 marginheight=0>
</FRAMESET>
</html>