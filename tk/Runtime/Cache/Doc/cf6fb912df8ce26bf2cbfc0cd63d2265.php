<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>用户资源 - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="用户资源,<?php echo ($config["Keyword"]); ?>"/>
    <meta name="description" content="用户资源,<?php echo ($config["Description"]); ?>"/>
    <link rel="stylesheet" href="/Public/index/css/wln-base.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"/>
    <link rel="stylesheet" href="/Public/index/css/style.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"/>
</head>
<body>
<!--顶部导航-->
<div class="top-logo-wrap w1000">
    <a class="top-logo" href="/">
        <img src="/Public/index/imgs/publ/logo.png" alt="logo"/>
    </a>
</div>
<div class="top-nav-fixed">
    <div class="top-nav-wrap">
        <div class="top-nav w1000">
            <a class="top-nav-item" href="/">首页</a>
            <span class="top-nav-item">
                <a class="tn-link" href="<?php echo U('/Home');?>">组卷</a>
            </span>
            <span class="top-nav-item">
                <a class="tn-link" href="<?php echo U('/Aat');?>">提分</a>
            </span>
        </div>
    </div>
</div>
<!--顶部导航-end-->
<!-- 面包屑导航 -->
<div class="w1000 crumbs-wrap">
    <div class="g-crumbs"> <b class="now-path">当前位置：</b>
        <a href="/">首页</a> > <span>用户资源</span>
    </div>
</div>
<!-- 面包屑导航 end-->
<!-- banner -->
<div class="banner-area w1000">
    <img src="/Public/index/imgs/in-page/wln-bn.jpg" alt="智慧云题库云平台">
    <h1>用户资源</h1>
</div>
<!-- banner end-->

<div class="w1000">
    <div class="box">
        <div class="nr_box testpaper_box">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="testpaperScreening">
                <tr>
                    <th width="7%">学科：</th>
                    <td width="93%">
                        <?php if(empty($pageLinks['s']['c'])): ?><a class='subject this' params='0' href="<?php echo sprintf($pageLinks['s']['a'], '0');?>">全部</a>
                        <?php else: ?>
                        <a class='subject' params='0' href="<?php echo sprintf($pageLinks['s']['a'], '0');?>">全部</a><?php endif; ?>
                        <?php if(is_array($subjects)): foreach($subjects as $val=>$subject): if($pageLinks['s']['c'] == $val): ?><a class='subject this' params='<?php echo ($val); ?>' href="<?php echo sprintf($pageLinks['s']['a'], $val);?>"><?php echo ($subject); ?></a>
                        <?php else: ?>
                        <a class='subject' params='<?php echo ($val); ?>' href="<?php echo sprintf($pageLinks['s']['a'], $val);?>"><?php echo ($subject); ?></a><?php endif; endforeach; endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>类型：</th>
                    <td>
                        <?php if(empty($pageLinks['t']['c'])): ?><a class='type this' params='0' href="<?php echo sprintf($pageLinks['t']['a'], '0');?>">全部</a>
                        <?php else: ?>
                        <a class='type' params='0' href="<?php echo sprintf($pageLinks['t']['a'], '0');?>">全部</a><?php endif; ?>
                        <?php if(is_array($dynamicType)): foreach($dynamicType as $val=>$type): if($pageLinks['t']['c'] == $val): ?><a class='type this' params='<?php echo ($val); ?>' href="<?php echo sprintf($pageLinks['t']['a'], $val);?>"><?php echo ($type); ?></a>
                        <?php else: ?>
                        <a class='type' params='<?php echo ($val); ?>' href="<?php echo sprintf($pageLinks['t']['a'], $val);?>"><?php echo ($type); ?></a><?php endif; endforeach; endif; ?>
                    </td>
                </tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="testpaperlist">
                <thead>
                    <tr>
                        <th width="110" align="center">
                            <span>|</span>
                            类别
                        </th>
                        <th width="100" align="center">
                            <span>|</span>
                            学科
                        </th>
                        <th class="sjtit">试卷名称</th>
                        <th width="100" align="center">上传用户</th>
                        <th width="100" align="center">更新时间</th>
                    </tr>
                </thead>
                <tbody id='content'>
                    <?php if(count($userDynamicResult) == 0): ?><tr><td colspan="5"><div class="data-empty" style="padding:80px;"></div></td></tr>
                    <?php else: ?>
                    <?php if(is_array($userDynamicResult)): foreach($userDynamicResult as $val=>$record): if($val % 2 == 0): ?><tr>
                        <?php else: ?>
                    <tr><?php endif; ?>
                        <td align="center"><?php echo ($dynamicType[$record['Classification']]); ?></td>
                        <td align="center"><?php echo ((isset($subjects[$record['SubjectID']]) && ($subjects[$record['SubjectID']] !== ""))?($subjects[$record['SubjectID']]):"暂无数据"); ?></td>
                        <td class="sjtit">
                            <a target="_blank" title="<?php echo ($record["Title"]); ?>" href="<?php echo U('/Doc/userContent/'.$record['AssociateID']);?>"><?php echo ($record["Title"]); ?></a>
                        </td>
                        <td align="center"><?php echo formatString('hiddenUserName',$record['UserName']);;?></td>
                        <td align="center"><?php echo (date("Y/m/d",$record["AddTime"])); ?></td>
                    </tr><?php endforeach; endif; endif; ?>
                </tbody>

            </table>
            <div class="page-wrap">
                <div class="page-box">
                    <?php if(count($pages) > 1 ): if(is_array($pages)): foreach($pages as $val=>$links): if($links["c"] == 'c'): ?><a href="<?php echo ($links["a"]); ?>" class="current"><?php echo ($links["n"]); ?></a>
                    <?php else: ?>
                    <a href="<?php echo ($links["a"]); ?>"><?php echo ($links["n"]); ?></a><?php endif; endforeach; endif; endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id='token' value='<?php echo ($token); ?>'/>
<input type="hidden" id='page' value='1'/>
<div class="footer-wrap">
    <div class="footer-box clearfix w1000">
       <!--footer box--><?php echo ($config["IndexName"]); ?> <?php echo (C("WLN_VERSION")); ?>
    </div>
</div>
<script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/jquery.cookie.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/plugin/layer/layer.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/index/js/wlnBase.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
</body>
</html>
</body>
</html>