<?php
return array(
    'DEFAULT_CONTROLLER'=>'Default',//默认的控制器名称
    'WLN_AAT_OPEN_REGISTER' => 1, // 0不开放 1开放
    'TAGLIB_PRE_LOAD' => 'html',
    'WLN_OPTIONS' => [
        '1' => 'A',
        '2' => 'B',
        '3' => 'C',
        '4' => 'D',
        '5' => 'E',
        '6' => 'F',
        '7' => 'G',
        '8' => 'H',
        '9' => 'I',
        '10' => 'J',
        '11' => 'K',
        '12' => 'L'
    ],
    'WLN_TYPE_FILTER' => [
        '14' => '124',
    ],//学科ID为键，值为该学科要过滤的题型ID，逗号分隔
    'APP_KEY'               => 'fjieo89regfjfKDfsd9023rfkdfsdDds',//用于APP开发中生成token使用
    'NETWORK_TYPE' => [
        -1 => '未知网络',
        0 => '无网络',
        1 => 'WIFI',
        2 => '2G',
        3 => '3G',
        4 => '4G',
    ],
    
    'ERROR_51001' => '请输入联系方式！',
    'ERROR_51002' => '反馈失败，请重试！',
    'ERROR_51003' => '专题ID为空！',
    'ERROR_51004' => '专题不存在或锁定！',
    'ERROR_51005' => '暂时没有试卷！',
    'ERROR_51006' => '同步学习版需选择教材，请进入个人中心来选择教材！',
    'ERROR_51007' => '获取套卷试卷类型失败，请重试！',
    'ERROR_51008' => '反馈内容至少大于四个汉字！',
	'ERROR_51009' => '没有权限查看此试卷,或者试卷不存在！',
);