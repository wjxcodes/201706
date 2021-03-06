<?php
return array(
    //'配置项'=>'配置值'
    'WLN_OPEN_REGEDIT'             => 1, // 是否开启注册
    'WLN_OPEN_INVIT'             => 0, // 是否开启邀请码 0不开启 1开启
    'MOBILE_UPLOAD_KEY'            => 'hn12DyiBHm5sKD2vFbbiSmB6m8hsyGmB',//用于上传文件生成token

    //组卷错误代码 从2开始
    //Index控制器
    'ERROR_20102'   =>  '公告标识错误！',
    'ERROR_20103'   =>  '公告不存在！',
    'ERROR_20104'   =>  '您选的试题数量太多，无法组卷！',
    'ERROR_20108'   =>  '邮箱与用户名不匹配！',
    'ERROR_20109'   =>  '暂停注册！',
    'ERROR_20111'   =>  '视频参数有误！',
    'ERROR_20112'   =>  '视频不存在',
    'ERROR_20113'   =>  '提交的内容提交失败！',
    'ERROR_20114'   =>  '提交的内容不能为空！',
    'ERROR_20115'   =>  '没有替换试题！',
    'ERROR_20116'   =>  '抱歉！试卷不存在。',
    'ERROR_20117'   =>  '抱歉！暂时没有符合条件的试卷，请尝试更换查询条件。',
    'ERROR_20118'   =>  '暂时没有收藏。',
    'ERROR_20119'   =>  '删除收藏失败',
    'ERROR_20120'   =>  '存档失败，请重试',
    'ERROR_20121'   =>  '服务器连接失败，请重试',
    'ERROR_20122'   =>  '存档数据异常。',

    'ERROR_20201'   =>  '连接服务器失败！',
    'ERROR_20202'   =>  '页面标识错误！',
    'ERROR_20203'   =>  '对不起！您没有该功能操作权限',

    'ERROR_20206'   =>  '请填写用户名！',
    'ERROR_20207'   =>  '当前权限已有存档%s份，存档失败！',
    'ERROR_20208'   =>  '今日已下载(%s次)，请明天再试！',
    'ERROR_20209'   =>  '今日已使用【智能组卷】 %s 次，请明天再试！',
    'ERROR_20210'   =>  '今日已使用【模板组卷】%s 次，请明天再试！',
    'ERROR_20211'   =>  '您目前没有此功能权限！',
    'ERROR_20213'   =>  '该试题正在优化中暂时不能删除！',
    'ERROR_20214'   =>  '您所在的用户组该文件本月下载次数共(%s)次，已使用完毕，请下月再试！',
    'ERROR_20215'   =>  '收藏失败！当前权限仅能收藏%s道试题。',
    'ERROR_20216'   =>  '模板名称重复，请修改模板名称！',
    'ERROR_20217'   =>  '您不能对正在审核中的文档进行操作！',
    'ERROR_20218'   =>  '该文档的试题暂未提取！',
    'ERROR_20219'   =>  '布置作业[下载作答]失败，您当前的权限每月仅能下载%s次！',
    'ERROR_20220'   =>  '试题不能为空！请选择试题后组卷',
    'ERROR_20221'   =>  '文档路径有误！',
    'ERROR_20222'   =>  '试题收藏保存失败！',
    'ERROR_20223'   =>  '移动失败！',
    'ERROR_20224'   =>  '评论失败！',
    'ERROR_20225'   =>  '邀请码有误',
    'ERROR_20226'   =>  '邀请码已被使用过！',
    //Dir控制器
    'ERROR_20301'   => '模板保存失败！请重试...',
    'ERROR_20302'   => '模板替换失败！请重试...',
    'ERROR_20303'   => '系统替换失败！您没有权限...',
    'ERROR_20304'   => '系统替换失败！请重试...',
    'ERROR_20305'   => '您已经存在:%s个模板，请使用替换功能！',
    'ERROR_20306'   => '没有查询到所需年级...请选择其他学科',
    'ERROR_20307'   => '数据调取失败！请重试...',
    'ERROR_20308'   => '没有可选模板！',
    //User控制器
    'ERROR_20401'   => '找不到所属地区',
    'ERROR_20402'   => '抱歉，没有搜索到该地区学校...',
    'ERROR_20404'   => '目录id获取失败！',
    'ERROR_20405'   => '请填写目录名称！',
    'ERROR_20406'   => '修改目录名称失败！',
    'ERROR_20407'   => '删除目录失败！',
    'ERROR_20408'   => '删除试题失败！',
    'ERROR_20409'   => '目录不存在！',
    'ERROR_20410'   => '请重试！',
    'ERROR_20411'   => '请填写正确的原密码!',
    'ERROR_20412'   => '请输入正确的联系电话!',
    //CustomTestStore组卷校本题库错误代码
    'ERROR_20801'  => '当前没有可以被投稿的原创题模版！',
    'ERROR_20802'  => '<strong>%s</strong> 已过期，不能投稿！',
    'ERROR_20803'  => '<strong>%s</strong> 没有可以投稿的试题！',
    'ERROR_20804'  => '投稿失败，刷新页面重试！',
    'ERROR_20805'  => '该试题已经投稿该原创题模版！',
    'ERROR_20807'  =>  '上传验证失败或者过期，请重新扫描二维码！',//二维码
    'ERROR_20808'  =>  '获取图片地址超时！',//二维码


);