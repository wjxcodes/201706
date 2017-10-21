<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>组卷历史存档</title>
    <meta id="keywordsmeta" name="keywords" content="在线组卷系统,存档,组卷记录">
    <meta id="descriptmeta" name="description" content="在线组卷系统组卷历史存档">
    <link type="text/css" href="/Public/default/css/common.css" rel="stylesheet">
    <link type="text/css" href="/Public/default/css/user.css" rel="stylesheet">
        <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/Public/plugin/layer/layer.js"></script>
    </head>
<body>
    <div id="righttop">
        <div id="categorylocation">
            <span class="nowPath">当前位置：</span>
            &gt;
            <span id="loca_text">
                <span></span>
                &gt; <a href="<?php echo U('Home/info');?>" style="cursor: pointer;text-decoration:none;">用户档案</a> &gt; 用户经验任务介绍
            </span>
        </div>
    </div>
    <div id='main'>
        <div id="divbox" class="sc_list_box g-tab">
        <div id="paperinfod" class="tab-list-top">
            <div class="tab-list-head f-yahei"> <b>用户经验任务介绍</b> 
            </div>
        </div>
        <div id="paperlistbox">
            <div class="ul-table-container">
                <table class="ul-table">
                    <thead>
                        <tr>
                            <th><span>任务名称</span></th>
                            <th><span>非认证教师对应经验</span></th>
                            <th><span>认证教师对应经验</span></th>
                            <th><span>任务频率</span></th>
                            <th><span>当前任务状态</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(is_array($explist)): $i = 0; $__LIST__ = $explist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><tr>
                                <td>                                
                                    <span>
                                        <?php echo ($value["ExpDesc"]); ?>
                                    </span>
                                 </td>
                                 <td>                                
                                    <span>
                                        <?php echo ($value["ExpPoint"]); ?>
                                    </span>
                                 </td>
                                 <td>                                
                                    <span>
                                        <?php echo ($value["ExpAuthPoint"]); ?>
                                    </span>
                                 </td>
                                 <td>                                
                                    <span>
                                        <?php if($value["ExpTime"] == "0"): ?>仅一次<?php endif; ?>
                                        <?php if($value["ExpTime"] == "1"): ?>每天一次<?php endif; ?>
                                        <?php if($value["ExpTime"] == "2"): ?>每天多次<?php endif; ?>
                                    </span>
                                 </td>
                                 <td>                                
                                    <span>
                                         <?php if($value["done"] == "1"): ?><a href='javascript:;' style='color:green'>已完成</a>
                                            <?php else: ?>
                                            <?php if($value["open"] == "1"): ?><a <?php if(($value["target"]) == "1"): ?>target="_blank"<?php endif; ?> href="<?php echo ($value["url"]); ?>" style="color:red">去做任务</a>
                                            <?php else: ?>
                                                任务未开启<?php endif; endif; ?>
                                    </span>
                                 </td>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    </div>
</body>
<script type="text/javascript" src="/Public/default/js/user.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script>
    $.myUser.initDivHeight();
    $(window).resize(function() { $.myUser.initDivHeight(); });
</script>
</html>