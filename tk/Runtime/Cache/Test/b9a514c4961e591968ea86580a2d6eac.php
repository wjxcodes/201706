<?php if (!defined('THINK_PATH')) exit();?><style>
<?php if($edit["DfStyle"] == 1): ?>.kgdf{display:none;}
<?php else: ?>
.zgdf{display:none;}<?php endif; ?>
</style>
<table cellpadding="5" cellspacing="0" class="list" border="1">
<tr><td height="5" colspan="2" class="topTd" ></td></tr>
<tr>
    <td class="tRight" style="width:100px">试题预览：</td>
    <td class="tLeft"><div style="height:250px;width:430px;overflow:auto;">
    <p>【题文】<?php echo ((isset($show["test"]) && ($show["test"] !== ""))?($show["test"]):'无'); ?>
    </div></td>
</tr>
<tr><td height="5" colspan="2" class="bottomTd" ></td></tr>
</table>