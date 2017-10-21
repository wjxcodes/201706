<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<TITLE>『<?php echo (C("WLN_WEB_NAME")); ?>管理平台』</TITLE>
<FRAMESET FRAMEBORDER=0 framespacing=0 border=0 rows="50, *,32">
<FRAME SRC="<?php echo U('Teacher/Public/top');?>" name="top" FRAMEBORDER=0 NORESIZE SCROLLING='no' marginwidth=0 marginheight=0>
<FRAMESET FRAMEBORDER=0  framespacing=0 border=0 COLS="200,7, *" id="frame-body">
    <FRAME SRC="<?php echo U('Teacher/Public/menu');?>" FRAMEBORDER=0 id="menu-frame" name="menu">
    <frame src="<?php echo U('Teacher/Public/drag');?>" id="drag-frame" name="drag-frame" frameborder="no" scrolling="no">
    <?php if(!empty($u)): ?><FRAME SRC="<?php echo ($u); ?>" FRAMEBORDER=0 id="main-frame" name="main">
    <?php else: ?>
        <FRAME SRC="<?php echo U('Teacher/Public/main');?>" FRAMEBORDER=0 id="main-frame" name="main"><?php endif; ?>
</FRAMESET>

<FRAME SRC="<?php echo U('Teacher/Public/footer');?>" name="footer" FRAMEBORDER=0 NORESIZE SCROLLING='no' marginwidth=0 marginheight=0>
</FRAMESET>
</html>