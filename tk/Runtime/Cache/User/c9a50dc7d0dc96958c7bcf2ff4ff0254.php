<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>用户信息</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="在线组卷系统,用户信息,升级,宣传赚点" />
    <meta name="description" content="在线组卷系统用户信息升级宣传赚点" />
    <link type="text/css" href="/Public/default/css/common.css" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/user.css" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script>
    var local='<?php echo U('Index/main');?>';
    </script>
    <script type="text/javascript" src="/Public/default/js/common.js"></script>
    <script type="text/javascript" src="/Public/default/js/getSelectData.js"></script>
</head>
<body>
<div id="rightdiv">
    <div id="rightTop" class="crumbs-wrap">
        <div id="categorylocation" class="g-crumbs">
            <span class="now-path">当前位置：</span>
            <span id="local_text"></span>
            >  用户档案 > 权限介绍
        </div>
    </div>
    <div id="main">
    <div class="user-level">
    <!-- 用户权限分类 -->
        <div class="ul-index-container clearfix">
            <span class="ul-item-group">
                <span class="ul-item-group-tit">
                    <h4>集体用户：</h4>
                </span>
                <?php if(is_array($group["team"])): $i = 0; $__LIST__ = $group["team"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$up): $mod = ($i % 2 );++$i; if($up['belong']): ?><span class="ul-item ul-used">
                            <span class="ul-used-tips">
                              您当前的账号权限是 ：
                              <span class="red"><?php echo ($userGroup["name"]); ?></span> <b></b>
                            </span>
                            <i class="iconfont">&#xe60b;</i>
                            <cite><?php echo ($up["name"]); ?></cite>
                        </span>
                    <?php else: ?>
                        <span class="ul-item">
                            <i class="iconfont">&#xe60c;</i>
                            <cite><?php echo ($up["name"]); ?></cite>
                        </span><?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </span>
            <span class="ul-item-group">
                <span class="ul-item-group-tit">
                    <h4>个人用户：</h4>
                </span>
                <?php if(is_array($group["person"])): $i = 0; $__LIST__ = $group["person"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$up): $mod = ($i % 2 );++$i; if($up['belong']): ?><span class="ul-item ul-used">
                            <span class="ul-used-tips">
                                                      您当前的账号权限是 ：
                              <span class="red"><?php echo ($up["name"]); ?></span> <b></b>
                            </span>
                            <i class="iconfont">&#xe60b;</i>
                            <cite><?php echo ($up["name"]); ?>
                                <?php if($up["name"] == 'VIP专享用户'): ?><a class="goToBuy" href="javascript:">购买</a>
                                <?php elseif($up["name"] == 'VIP至尊用户'): ?>
                                <a class="goToBuy" href="javascript:">购买</a><?php endif; ?>
                            </cite>
                        </span>
                    <?php else: ?>
                        <span class="ul-item">
                            <i class="iconfont">&#xe611;</i>
                            <cite><?php echo ($up["name"]); ?>
                                <?php if($up["name"] == 'VIP专享用户'): ?><a class="goToBuy" href="javascript:">购买</a>
                                <?php elseif($up["name"] == 'VIP至尊用户'): ?>
                                <a class="goToBuy" href="javascript:">购买</a><?php endif; ?>
                            </cite>
                        </span><?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </span>
            <div class="f-yahei" style="font-size:16px;text-align:center;color:#f00;padding-top:15px;"><?php echo ($slogan); ?></div>
        </div>
        <!-- 用户权限分类 end-->
           <!-- 用户权限详情 -->
        <div class="ul-table-container">
            <table class="ul-table" id="userLevelTable">
                <thead>
                <tr>
                        <th><span>网站功能</span></th>
                        <th><span><?php echo ($header[1]); ?></span></th>
                        <th><span><?php echo ($header[2]); ?></span></th>
                        <th><span><?php echo ($header[3]); ?></span></th>
                        <th><span><?php echo ($header[4]); ?></span></th>
                        <th><span><?php echo ($header[5]); ?></span></th>
                        <th><span><?php echo ($header[6]); ?></span></th>
                </tr>
                </thead>
                <tbody>
                    <?php if(is_array($list)): foreach($list as $key=>$record): ?><tr>
                            <td>
                                <span>
                                    <?php if($record[0] == 'Y'): ?><i class="iconfont true"></i>&nbsp;
                                    <?php elseif($record[0] == 'N'): ?>
                                        <i class="iconfont false"></i>&nbsp;
                                    <?php else: ?>
                                            <?php echo ($record[0]); endif; ?>
                                </span>
                            </td>
                            <td>
                                <span>
                                    <?php if($record[1] == 'Y'): ?><i class="iconfont true"></i>&nbsp;
                                    <?php elseif($record[1] == 'N'): ?>
                                        <i class="iconfont false"></i>&nbsp;
                                    <?php else: ?>
                                            <?php echo ($record[1]); endif; ?>
                                </span>
                            </td>
                            <td>
                                <span>
                                    <?php if($record[2] == 'Y'): ?><i class="iconfont true"></i>&nbsp;
                                    <?php elseif($record[2] == 'N'): ?>
                                        <i class="iconfont false"></i>&nbsp;
                                    <?php else: ?>
                                            <?php echo ($record[2]); endif; ?>
                                </span>
                            </td>
                            <td>
                                <span>
                                    <?php if($record[3] == 'Y'): ?><i class="iconfont true"></i>&nbsp;
                                    <?php elseif($record[3] == 'N'): ?>
                                        <i class="iconfont false"></i>&nbsp;
                                    <?php else: ?>
                                            <?php echo ($record[3]); endif; ?>
                                </span>
                            </td>
                            <td>
                                <span>
                                    <?php if($record[4] == 'Y'): ?><i class="iconfont true"></i>&nbsp;
                                    <?php elseif($record[4] == 'N'): ?>
                                        <i class="iconfont false"></i>&nbsp;
                                    <?php else: ?>
                                            <?php echo ($record[4]); endif; ?>
                                </span>
                            </td>
                            <td>
                                <span>
                                    <?php if($record[5] == 'Y'): ?><i class="iconfont true"></i>&nbsp;
                                    <?php elseif($record[5] == 'N'): ?>
                                        <i class="iconfont false"></i>&nbsp;
                                    <?php else: ?>
                                            <?php echo ($record[5]); endif; ?>
                                </span>
                            </td>
                            <td>
                                <span>
                                    <?php if($record[6] == 'Y'): ?><i class="iconfont true"></i>&nbsp;
                                    <?php elseif($record[6] == 'N'): ?>
                                        <i class="iconfont false"></i>&nbsp;
                                    <?php else: ?>
                                            <?php echo ($record[6]); endif; ?>
                                   </span>
                            </td>
                        </tr><?php endforeach; endif; ?>
                    <tr class="ul-table-price">
                        <td><span>价格</span></td>
                        <td><span></span></td>
                        <td><span></span></td>
                        <td><span></span></td>
                        <td>0</td>
                        <td><span><s>原价:50 元/月</s><br />优惠价:<?php echo ($vipPrice); ?>元/月</span></td>
                        <td><span><s>原价:100 元/月</s><br />优惠价:<?php echo ($superVipPrice); ?>元/月</span></td>
                    </tr>
                    <tr>
                        <td><span>操作</span></td>
                        <td><span>咨询电话：400-0383-483 或联系QQ： 5686547852</span></td>
                        <td><span>咨询电话：400-0383-483 或联系QQ： 5686547852</span></td>
                        <td><span>咨询电话：400-0383-483 或联系QQ： 5686547852</span></td>
                        <td></td>
                        <td><a class="goToBuy nor-btn" href="javascript:">购买</a><br /><span class="red"><?php echo ($slogan); ?></span></td>
                        <td><a class="goToBuy nor-btn" href="javascript:">购买</a><br /><span class="red"><?php echo ($slogan); ?></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- 用户权限详情 end-->
    </div>
    </div>

</div>
<script type="text/javascript" src="/Public/default/js/user.js"></script>
<script type="text/javascript">
    $(function(){
        //当前用户等级对应权限高亮
        $("#userLevelTable tr th:nth-child(<?php echo ($eq); ?>),#userLevelTable tr td:nth-child(<?php echo ($eq); ?>)").addClass("used-on");
        //支付链接跳转
        $(document).on('click','.goToBuy',function(){
            window.open(U('User/Index/operOrder'));
        });
        $('#main').height($(window).height()-52);
        $.myUser.init(<?php echo ($user["GradeID"]); ?>);
    });
</script>
</body>
</html>