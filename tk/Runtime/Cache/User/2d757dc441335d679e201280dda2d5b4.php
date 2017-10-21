<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>用户信息</title>
    <meta name="keywords" content="在线组卷系统,用户信息,升级,宣传赚点" />
    <meta name="description" content="在线组卷系统用户信息升级宣传赚点" />
    <link type="text/css" href="/Public/default/css/common.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/user.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/Public/plugin/layer/layer.js"></script>
    <script>
        var local='<?php echo U('Index/main');?>';
        var areaParent="<?php echo ($areaParent); ?>";
    </script>
    <script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
</head>
<body>
<div id="rightdiv">
    <div id="righttop">
        <div id="categorylocation">
            <span style="width:65px;">当前位置：</span>> <span id="loca_text"> 用户档案 > 用户信息 </span>
        </div>
    </div>

    <div id="main">
                <!-- 用户等级奖励信息 -->
            <div class="user-info-content">
                <div class="ls-user-info-box clearfix">
                    <span class="ls-user-photo">
                        <img class="userPic" height="64" width="64" src="/Public/default/image/user/photo.jpg" alt="头像">
                        <span class="photo-wrapper"></span>
                    </span>
                    <span class="ls-user-info">
                        <a class="head elli f-yahei" href="javascript:;">
                            <?php if($user["Nickname"] != ''): echo ($user["Nickname"]); ?>
                            <?php else: ?>
                                <?php echo ($user["OkName"]); endif; ?>
                        </a>
                        <div class="ls-user-level">
                            <span class="ls-user-cite years-service f-song"> <i class="icon"></i>
                                <span class="groupName"><?php echo ($user["UserGroup"]); ?></span>
                            </span>
                            <span class="level-name"><?php echo ($levelMsg[0]["LevelName"]); ?><cite class="level-num"></cite></span>
                        </div>
                    </span>
                    <div class="up-level">
                        <div class="up-level-next">
                            <!-- level-进度条 -->
                            <p class="ul-process">
                                <span class="ul-level-top f-yahei">
                                    <s class="left"> <?php echo ($levelMsg[0]["LevelName"]); ?></s>
                                    <s class="right"><?php echo ($levelMsg[1]["LevelName"]); ?> </s>
                                </span>
                                <span class="process-wrap">
                                    <span class="process-pct" style="width:<?php echo ($levelMsg["baifen"]); ?>%"></span>
                                </span>

                                <span class="ul-tips">升级还需<em><?php echo ($levelMsg["expCha"]); ?></em>点经验值  <a class="blue" href="<?php echo U('Home/userExpList');?>">做任务</a></span>
                            </p>
                            <cite class="next-level-name f-yahei"><a href="<?php echo U('User/Home/levelInfo');?>">（等级特权）</a></cite>
                        </div>
                    </div>
                </div>
                <!-- 奖励值 -->
                <div class="award-site"><span class="award-item">经验值<cite><?php echo ($user["ExpNum"]); ?></cite></span><span class="award-item">金币<cite><?php echo ((isset($user["Cz"]) && ($user["Cz"] !== ""))?($user["Cz"]):"0"); ?></cite><a href="<?php echo U('Home/recordList');?>" class="helper f-yahei">（收入记录）</a></span></div>
                <!-- 奖励值 END-->
            </div>
            <!-- 用户等级奖励信息 -->
        <div class="contentbox" style="position:relative;">
            <div class="contentbox_title">基本信息</div>
            <div>
                <span class="item">用 户 ID：</span>
                <strong style="color:#029fe8"><?php echo ($user["UserName"]); ?></strong>
                <span id="savelogin" style="display:none">（
                    您已在本机设置自动登录，
                    <a href="javascript:CloseAutoLogin();">点击这里可以取消</a>。）
                </span>
            </div>
            <div><span class="item">登录次数：</span><?php echo ($user["Logins"]); ?></div>
            <div><span class="item">上次登录时间：</span><?php echo ($user["LastTime"]); ?></div>
            <div><span class="item">上次登录IP：</span><?php echo ($user["LastIP"]); ?></div>
            <div class="fgx"></div>
            <?php if($user["UserJZRQ"] != ''): ?><div><span class="item">有效期截止：</span><?php echo ($user["UserJZRQ"]); ?></div>
            <div><span class="item">剩余：</span><?php echo ($user["UserJZSY"]); ?>天</div><?php endif; ?>
            <div>
                <span class="item">用户权限组：</span>
                <?php echo ($user["UserGroup"]); if($user["nowPowerName"] != ''): ?>【<?php echo ($user["nowPowerName"]); ?>】<?php endif; ?>
                    &nbsp;<a href="<?php echo U('Home/userLevelIntro');?>" style="cursor: pointer;text-decoration:none;">所有权限</a>
                    &nbsp;<a target="_top" href="<?php echo U('Index/operOrder');?>"
                             style="cursor: pointer;text-decoration:none;">
                购买权限</a>
            </div>
            <?php if($user["IpUserGroup"] != ''): ?><div><span class="item">ip权限组：</span><?php echo ($user["IpUserGroup"]); ?></div><?php endif; ?>
            <div><span class="item">组卷下载次数：</span><?php echo ((isset($user["Times"]) && ($user["Times"] !== ""))?($user["Times"]):"0"); ?></div>
        </div>
        <div class="contentbox" style="position:relative;background-color:#fff;">
            <div class="contentbox_title">详细信息 <a id="change" class="pointer">修改</a></div>
            <div style="position:relative;">
                <form method="post" action="<?php echo U('Home/info');?>">
                    <div class="con">
                        <span class="item">姓　　名：</span>
                        <span>
                           <?php echo ($user["RealName"]); ?>
                        </span>
                    </div>
                    <div class="con">
                        <span class="item">教师身份认证：</span>
                        <span>
                           <?php echo ($user["authTitle"]); ?>
                           <?php if(in_array($user['IfAuth'],array(1,2,3))): ?><a href="<?php echo U('User/Index/showAuthInfo',array('status'=>$user['IfAuth']));?>" target="_blank" alt="查看认证信息">
                            查看认证信息</a>
                           <?php elseif($user['Whois'] == 1): ?>
                           <a href ="<?php echo U('User/Index/authTeacher');?>" target="_blank" alt="查看认证信息">
                           去认证</a><?php endif; ?>
                        </span>
                    </div>
                    <div class="auth-info-content" id="showAuthInfo" style="display:none;">
                     <table class="g-table g-table-bordered">
                        <thead>
                            <tr>
                                <th>身份证号</th>
                                <th>教师资格证号</th>
                                <th>教师等级证号</th>
                                <th>认证时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr jl=''>
                                <td><?php echo ($authData["IDNumber"]); ?></td>
                                <td><?php echo ($authData["Qualification"]); ?> <a href="<?php echo ($authData["QuaPicSrc"]); ?>" target="_blank" class="link">查看图片</a></td>
                                <td><?php echo ($authData["Grade"]); ?> <a href="<?php echo ($authData["GradePicSrc"]); ?>" target="_blank" class="link">查看图片</a></td>
                                <td><?php echo (date("Y-m-d H:i:s",$authData["AuthTime"])); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                    <div class="showinput none">
                        <span class="item">姓　　名：</span>
                        <input name="RealName" id="RealName" type="text" size="50" value="<?php echo ($user["RealName"]); ?>"/>
                        <span id="RealNameerr"></span>
                    </div>
                    <div class="con">
                        <span class="item">密　　码：</span>
                        <span>******</span>
                    </div>
                    <div class="showinput pwd none">
                        <span class="item">原 密 码：</span>
                        <input name="Passwordy" id="Passwordy" type="password" size="50"/>
                        * 不修改请留空<span id="Passwordyerr"></span>
                    </div>
                    <div class="showinput pwd none">
                        <span class="item">新 密 码：</span>
                        <input name="Password" id="Password" type="password" size="50"/>
                        <span id="Passworderr"></span>
                    </div>
                    <div class="showinput pwd none">
                        <span class="item">确认新密码：</span>
                        <input name="Password2" id="Password2" type="password" size="50"/>
                        <span id="Password2err"></span>
                    </div>
                    <div class="con">
                        <span class="item">地　　区：</span>
                        <span><?php echo ($user["AreaStr"]); ?></span>
                    </div>
                    <div class="showinput none">
                        <span class="item">地　　区：</span>
                        <select name="AreaID[]" class="AreaID selectArea" id="sf">
                            <?php if(is_array($arrArea)): $i = 0; $__LIST__ = $arrArea;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><option value="<?php echo ($sub["AreaID"]); ?>" last="<?php echo ($sub["Last"]); ?>"><?php echo ($sub["AreaName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                    <div class="con">
                        <span class="item">学　　校：</span>
                        <span><?php echo ($user["SchoolName"]); ?></span>
                    </div>
                    <div class="showinput none">
                        <span class="item">学　　校：</span>
                            <select name="SchoolID" id="school">
                                <option value="">请选择地区</option>
                                <?php if(is_array($schoolList)): $i = 0; $__LIST__ = $schoolList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><option value="<?php echo ($list["SchoolID"]); ?>" <?php if(($user["SchoolID"]) == $list["SchoolID"]): ?>selected="selected"<?php endif; ?>><?php echo ($list["SchoolName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        <span id="SchoolIDerr"></span>
                    </div>
                    <div class="con">
                        <span class="item">年　　级：</span>
                        <span><?php echo ($user["GradeName"]); ?></span>
                    </div>
                    <div class="showinput none">
                        <span class="item">年　　级：</span>
                        <select class="normal bLeft" id='GradeID' name="GradeID">
                                    <option value="0">请选择学科</option>
                                    <?php if($gradeList): if(is_array($gradeList)): $i = 0; $__LIST__ = $gradeList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><optgroup label="<?php echo ($vo["GradeName"]); ?>">
                                            <?php if($vo['sub']): if(is_array($vo['sub'])): $i = 0; $__LIST__ = $vo['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item["GradeID"]); ?>" <?php if(($item["GradeID"]) == $user["GradeID"]): ?>selected="selected"<?php endif; ?>>　　<?php echo ($item["GradeName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                            </optgroup><?php endforeach; endif; else: echo "" ;endif; ?>
                                    <?php else: ?>
                                        <option value="0">请添加学科</option><?php endif; ?>
                        </select>
                        <span id="GradeIDerr"></span>
                    </div>
                    <div class="con">
                        <span class="item">学　　科：</span>
                        <span><?php echo ($user["SubjectName"]); ?></span>
                    </div>
                    <div class="showinput none">
                        <span class="item">学　　科：</span>
                        <select name="SubjectID" id="SubjectID">
                            <option value="">请选择年级</option>
                            <?php if(is_array($subjectArray)): $i = 0; $__LIST__ = $subjectArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><option value="<?php echo ($list["SubjectID"]); ?>" <?php if(($user["SubjectName"]) == $list["SubjectName"]): ?>selected="selected"<?php endif; ?>><?php echo ($list["SubjectName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            
                        </select>
                        <span id="SubjectIDerr"></span>
                    </div>
                    <div class="con">
                        <span class="item">地　　址：</span>
                        <span><?php echo ($user["Address"]); ?></span>
                    </div>
                    <div class="showinput none">
                        <span class="item">地　　址：</span>
                        <input name="Address" id="Address" type="text" size="50" value="<?php echo ($user["Address"]); ?>"/>
                        <span id="Addresserr"></span>
                    </div>
                    <div class="con">
                        <span class="item">手　　机：</span>
                        <span id="phoneCodeValue"><?php echo ($user["Phonecode"]); ?></span>
                        <div class="ifCheckPhone">
                            <?php if($user["CheckPhone"] != '0'): ?><span> * 已验证</span><span class="pointer blue checkPhone" status="edit">[修改]</span>
                            <?php elseif($user["Phonecode"] == ''): ?>
                            <span class="pointer blue checkPhone" status="set">设置手机</span>
                            <?php else: ?>
                            <span class="pointer blue checkPhone" status="check">验证手机</span><?php endif; ?>
                        </div>
                    </div>
                    <div class="con">
                        <span class="item">邮　　箱：</span>
                        <span id="emailValue"><?php echo ($user["Email"]); ?></span>
                        <div class="ifCheckEmail">
                            <?php if($user["CheckEmail"] != '0'): ?><span> * 已验证</span><span class="pointer blue checkEmail" status="edit">[修改]</span>
                            <?php elseif($user["Email"] == ''): ?>
                            <span class="pointer blue checkEmail" status="set">设置邮箱</span>
                            <?php else: ?>
                            <span class="pointer blue checkEmail" status="check">更改/验证邮箱</span><?php endif; ?>
                        </div>
                    </div>
                    <div class="showinput none" style="margin-left:20px; padding-top:20px;line-height:30px;">
                        <div class="showinput none"><span id="SaveIDerr"></span></div>
                        <input name="UserID" value="<?php echo ($user["UserID"]); ?>" type="hidden"/>
                        <input class="" type="button" value="修改" id="uniteditsubmit"/>
                        <input class="" type="button" value="取消" id="reset"/>
                    </div>
                </form>
                <p>&nbsp;</p><p>&nbsp;</p>
            </div>
        </div>
    </div>

</div>
<script type="text/javascript" src="/Public/default/js/user.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript" src="/Public/default/js/getSelectData.js<?php echo (C("UPDATE_FILE_DATE")); ?>"></script>
<script type="text/javascript">
    //用户
    $.myUser.init(<?php echo ($user["GradeID"]); ?>);
</script>
</body>
</html>