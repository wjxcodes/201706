<?php
return array( //'配置项'=>'配置值'
              'LOAD_EXT_CONFIG'                    =>'local,sdk',
              //载入配置
              'WLN_VERSION'                        =>'1.0.0',
              //版本号
              'WLN_KEEP_VERSION'                   =>'豫B1-20150111',
              //备案号
              'WLN_UPDATE_FILE_DATE'               =>'?20160729',
              //js.css文件更新日期


              'TAGLIB_PRE_LOAD'                    =>'html',
              //import tags
              'DB_PARAMS'                          =>array(\PDO::ATTR_CASE=>\PDO::CASE_NATURAL),
              //数据字段大小写问题处理

              //'配置项'=>'配置值'
              'DEFAULT_C_LAYER'                    =>'Controller',
              // 默认的控制器层名称
              'MULTI_MODULE'                       =>true,
              // 是否允许多模块 如果为false 则必须设置 DEFAULT_MODULE
              'MODULE_DENY_LIST'                   =>array(
                  'Common',
                  //通用 错误码30开头
                  //通用下的系统 以Index为例就是38开头
                  //通用下的系统 以Marking为例就是3X1开头     以此类推
                  'Runtime'
                  //临时文件
              ),
              // 禁止访问的模块列表
              'MODULE_ALLOW_LIST'                  =>array(
                  'Index',
                  //官网  错误码80开头
                  'Task',
                  //任务大厅  错误码81开头
                  'Yc',
                  //原创平台  错误码82开头

                  'Home',
                  //组卷端 错误码20开头
                  'Ga',
                  //智能组卷 错误码21开头
                  'Dir',
                  //模板组卷 错误码22开头
                  'Guide',
                  //导学案 错误码23开头
                  'Manual',
                  //手动组卷 错误码24开头

                  'Aat',
                  //提分端 错误码50开头
                  'AatApi',
                  //提分移动端 错误码51开头
                  'Exercise',
                  //练习 错误码52开头
                  'Teacher',
                  //教师端 错误码40开头

                  'Manage',
                  //后台 错误码1开头
                  'Doc',
                  //文档 错误码1X1开头
                  'Test',
                  //试题 错误码1X2开头
                  'Work',
                  //作业 错误码1X3开头
                  'User',
                  //用户 错误码1X4开头
                  'Custom',
                  //校本题库 错误码1X5开头
                  'Statistics',
                  //统计 错误码1X6开头

                  'School',
                  //校长端 错误码70开头

                  'Analysis',
                  //数据分析 错误码90开头

                  'Marking',
                  //阅卷 错误码X10开头
              ),
              // 配置你原来的分组列表
              'DEFAULT_MODULE'                     =>'Index',
              // 配置你原来的默认分组

              'URL_CASE_INSENSITIVE'               =>true,
              // 默认false 表示URL区分大小写 true则表示不区分大小写
              'URL_PATHINFO_DEPR'                  =>'/',
              // 简化url层次
              'URL_MODEL'                          =>2,
              //REWRITE 模式
              'URL_HTML_SUFFIX'                    =>'html',
              // html
              'URL_ROUTER_ON'                      =>true,
              // 开启路由

              'URL_ROUTE_RULES'                    =>array(
                  'Index/Index/index'                            =>'/',

                  //首页链接
                  '/^Doc\/(\d+)$/'                               =>'Doc/Doc/showContent?id=:1',
                  '/^Doc\/userContent\/(\d+)$/'                  =>'Doc/Doc/userContent?id=:1',
                  //用户资源详情页
                  //试卷出题详情页
                  '/^UserResource-index.*$/'                           =>'/Doc/user.html',

                  '/^Task$/'                                        =>'Task/MissionHall/index',
                  //特色校本内容
                  'Home/Index/index'                             =>'/Home',
                  '/^Aat/Default/index$/'                       =>'/Aat',

                  '/^(Original|yc)$/'                                       =>'/Yc',
                  '/^Yc$/'                                       =>'Yc/Originality/originality',
                  //协同命制中心
                  //二维码
                  '/^Test\/(\d+)$/'                              =>'Test/TestPreview/testDetail?id=:1',
              ),
              // 路由规则

              'TMPL_EXCEPTION_FILE'                =>APP_PATH.'Common/View/error.html',
              // 定义公共错误模板
              'TMPL_VAR_IDENTIFY'                  =>'array',
              //模板变量识别
              'TMPL_STRIP_SPACE'                   =>'false',
              //是否去除模板文件里面的html空格与换行
              'TMPL_CACHE_ON'                      =>'false',
              //是否开启模板编译缓存
              'TMPL_L_DELIM'                       =>'{#',
              //模板标签开头
              'TMPL_R_DELIM'                       =>'#}',
              //模板标签结尾
              //'TMPL_CACHE_TIME'     => 3600,        //模板缓存有效期 0为永久
              //'TMPL_DENY_PHP'       =>  'true',     //是否禁用PHP原生代码
              'TAGLIB_BEGIN'                       =>'{#',
              //标签库标签开始标记
              'TAGLIB_END'                         =>'#}',
              //标签库标签结束标记

              'TOKEN_ON'                           =>true,
              // 是否开启令牌验证
              'TOKEN_NAME'                         =>'__hash__',
              // 令牌验证的表单隐藏字段名称
              'TOKEN_TYPE'                         =>'md5',
              //令牌哈希验证规则 默认为MD5
              'TOKEN_RESET'                        =>true,
              //令牌验证出错后是否重置令牌 默认为true


              'WLN_OPEN_SOFTKEY'                   =>0,
              // 是否开启加密狗
              'WLN_COOKIE_TIMEOUT'                 =>72000,
              //cookie过期时间

              'WLN_HOME_USER_AUTH_KEY'             =>'wln_user',
              //home端Cookie
              'WLN_WLN_USER_AUTH_KEY'              =>'wln_admin',
              //后台cookie
              'WLN_TEACHER_USER_AUTH_KEY'          =>'wln_teacher',
              //teacher端Cookie
              'WLN_EXAM_USER_AUTH_KEY'             =>'wln_exam',
              //exam端Cookie
              // cookie key 组卷cookie
              'WLN_AAT_USER_AUTH_KEY'              =>'wln_aat',
              //提分cookie
              //开启送书活动
              'WLN_SEND_BOOK'                      =>0,
              //0 关闭 1开启

              'WLN_NUM_LIST'                       =>array(
                  '一',
                  '二',
                  '三',
                  '四',
                  '五',
                  '六',
                  '七',
                  '八',
                  '九',
                  '十',
                  '十一',
                  '十二',
                  '十三',
                  '十四',
                  '十五',
                  '十六',
                  '十七',
                  '十八',
                  '十九',
                  '二十'
              ),
              'WLN_PERPAGE'                        =>20,
              //per page number      //大写数字的列表

              'WLN_TEST_DIFF'                      =>array(
                  '1'=>array(
                      '容易',
                      '4',
                      '5',
                      '0.801',
                      '0.999'
                  ),
                  '2'=>array(
                      '较易',
                      '6',
                      '6',
                      '0.601',
                      '0.800'
                  ),
                  '3'=>array(
                      '一般',
                      '7',
                      '9',
                      '0.501',
                      '0.600'
                  ),
                  '4'=>array(
                      '较难',
                      '10',
                      '10',
                      '0.301',
                      '0.500'
                  ),
                  '5'=>array(
                      '困难',
                      '11',
                      '12',
                      '0.001',
                      '0.300'
                  )
              ),
              //试题难度等级
              'WLN_DOWN_STYLE' =>  array(
                  '1'=>'试卷',
                  '2'=>'作业',
                  '3'=>'导学案',
                  '4'=>'答题卡'
              ),
              //下载类型
              'WLN_TEST_DATA'                      =>array(
                  4 =>0.9,
                  5 =>0.8,
                  6 =>0.7,
                  7 =>0.6,
                  8 =>0.55,
                  9 =>0.5,
                  10=>0.4,
                  11=>0.3,
                  12=>0.2
              ),
              //试题分值对应难度
              'WLN_TEST_STYLE_NAME'                =>array(
                  '1'=>'自适应训练',
                  '2'=>'专题模块训练（考点）',
                  '3'=>'整卷练习（试卷）',
                  '4'=>'整卷练习（自定义）',
                  '5'=>'阶段测试',
                  '6'=>'目标测试',
                  '7'=>'专题模块训练（章节）',
                  '8'=>'专题练习'
              ),
              //练习名称
              'WLN_TASK_FAILURE_MSG'               =>array(
                  '0'=>'知识点章节错误|1',
                  '1'=>'试题改错|2'
              ),
              'WLN_DOWNLOAD_RANDOM_CONTENT'        =>array(
                  '1',
                  '/Uploads/001.png'
              ),
              //试卷下载插入内容中的随机字符串

              /**************平台支付金额配置******************/
              'WLN_POINT_MONEY'                    =>1,
              //1分对应1元人民币
              'WLN_SHARE_DOC_MONEY'                =>0.1,
              //分享文档一次对应0.5元人民币
              'WLN_CHECK_TEST_MONEY'               =>0.5,
              //审核试题每题
              'WLN_TAG_TEST_MONEY'                 =>0.4,
              //标引试题每题
              /***********************************************/
              'WLN_TASK_TIMEOUT'                   =>2 * 60 * 60,
              //教师端任务超时时间 单位秒
              'WLN_TASK_TIMEOUT_DELPOINT'          =>1,
              //教师端任务超时后每题扣固定分值
              'WLN_TASK_SUCCESS_DELTEST'           =>1,
              //教师端任务发现需要删除的试题每次加固定分值
              'WLN_SOURCE_PIC_PATH'                =>'/Public/default/image/',
              //文档来源LOGO图片默认路径
              'APP_PLATFORM_KEY'                   =>array(
                  'APICLOUD'=>array(
                      'ANDROID'=>array(
                          'APP_ID' =>'A6963314303655',
                          'APP_KEY'=>'590F0CA0-8661-6F6B-7E12-213A6665D3D7',
                      ),
                  ),
                  'YOUMENG' =>array(
                      'AUTH_TOKEN'     =>'0ybg4jrWG2xBVl5Zrv6X',
                      'ANDROID_APP_KEY'=>'55af5ab7e0f55a1e5b002f90'
                      //智能提分Android版标识
                  ),
              ),
              //APICloud和友盟的API 认证，友盟15分钟最大500次请求
              'WLN_LOGIN_FAILED_CHECK'             =>array(
                  'USER'=>array(
                      'TIME' =>1800,
                      'COUNT'=>30
                  ),
                  'IP'  =>array(
                      'TIME' =>1800,
                      'COUNT'=>1000
                  ),
              ),
              //新用户注册时，要添加到的指定高级组及保持该权限的天数
              'WLN_REGISTER_GROUP'                 =>array(
                  'group'=>44,
                  //高级组所在分组id
                  'day'  =>30
                  // 单位：天 支持合法的php时间加减运算
              ),
              /*
                  本配置文件与‘REGISTER_GROUP’结合使用，同时优先级较高
                  注意：当endTime小于当前时间时，将使用‘REGISTER_GROUP’的参数
                        当endTime小于beginTime时，endTime的时间=beginTime+7天
              */
              'WLN_SPECIAL_INTERVAL_REGISTER_GROUP'=>array(
                  'group'    =>44,
                  // 参考REGISTER_GROUP 名称需一致
                  'day'      =>'+3 months +1 day',
                  // 同上
                  'beginTime'=>'2015-9-26',
                  //指定起始时间 格式：Y-m-d
                  'endTime'  =>'2015-10-8'
                  //指定截止时间 格式：Y-m-d 17日结束需写成18日
              ),

              //##支付宝配置
              //不同模块个性化配置,请重定义
              ##Warning:两个通知地址请使用对外链接地址 本地调试请基于域名配置服务器 此处不支持本地ip模式调试
              'WLN_ALI_NOTIFY_URL'                 =>'http://www.ty.com/User/Index/getNotify',
              //线上地址
              //支付宝异步通知地址
              'WLN_ALI_RETURN_URL'                 =>'http://www.ty.com/User/Index/getReturn',
              //线上地址
              //支付宝支付后的回调地址
              'WLN_ALI_SUCCESS_PAGE'               =>'/User/Index/orderResult',
              //支付宝支付成功后跳转地址
              'WLN_ALI_ERROR_PAGE'                 =>'/User/Index/orderResult',
              //支付宝支付失败后跳转地址
              //##

              //##通用错误代码 从9开始[按功能分块]
              //验证码相关
              'ERROR_30101'                        =>'图片验证码有误！',
              'ERROR_30102'                        =>'短信验证码错误！',
              'ERROR_30103'                        =>'邮箱验证码有误！',
              'ERROR_30104'                        =>'请填写验证码！',
              'ERROR_30105'                        =>'请填写正确的验证码！',
              'ERROR_30106'                        =>'手机验证码发送失败！请重试。',
              'ERROR_30107'                        =>'验证次数已超出！请明天再试。',
              'ERROR_30108'                        =>'未开启手机短信验证功能！',
              'ERROR_30109'                        =>'验证码已过期！',
              'ERROR_30110'                        =>'未开启邮箱验证功能！',
              'ERROR_30111'                        =>'邮箱验证码发送失败！请重试。',
              'ERROR_30112'                        =>'用户信息错误，请联系我们！',
              'ERROR_30113'                        =>'数据验证错误，请重新操作！',
              'ERROR_30114'                        =>'请填写查询条件！',
              'ERROR_30115'                        =>'短信验证码发送频繁！',
              //令牌验证错误
              //账户密码验证相关
              'ERROR_30201'                        =>'请填写账号！',
              'ERROR_30202'                        =>'请填写密码！',
              'ERROR_30203'                        =>'您的账号已被锁定，请联系管理员！',
              'ERROR_30204'                        =>'您填写的账户或密码不正确！',
              'ERROR_30205'                        =>'请登录！下面转入登录页面。',
              'ERROR_30206'                        =>'用户身份有误，请确认！',
              'ERROR_30207'                        =>'两次输入的密码不一致!',
              'ERROR_30208'                        =>'原密码不正确!',
              'ERROR_30209'                        =>'删除收藏失败！',
              'ERROR_30210'                        =>'您的访问过于频繁!',
              'ERROR_30211'                        =>'请输入有效的11位手机号码',
              'ERROR_30212'                        =>'邮箱格式错误',
              'ERROR_30214'                        =>'用户不存在',
              'ERROR_30216'                        =>'真实姓名必须大于2个汉字小于10个汉字！',
              'ERROR_30217'                        =>'用户昵称重复,请更换',
              'ERROR_30218'                        =>'昵称必须2-5个汉字或4个以上字母数字组合！',
              'ERROR_30221'                        =>'请填写6-18位密码！',
              'ERROR_30222'                        =>'用户名/手机号/邮箱重复，请更换！',
              'ERROR_30223'                        =>'模板标识不能为空！',

              'ERROR_30224'                        =>'您输入的手机号已注册，请使用【找回密码】',
              'ERROR_30225'                        =>'邮箱已被其他用户使用，请更换！',
              //@找回密码
              'ERROR_30227'                        =>'请填写正确的邮箱！',
              'ERROR_30228'                        =>'验证信息错误！请重新取回密码。',
              'ERROR_30229'                        =>'请使用学生账号登录！',
              'ERROR_30230'                        =>'请使用教师账号登录！',
              'ERROR_30231'                        =>'请使用家长账号登录！',
              'ERROR_30232'                        =>'请使用校长账号登录！',
              //数据异常相关
              'ERROR_30301'                        =>'数据标识错误！',
              'ERROR_30302'                        =>'删除失败！',
              'ERROR_30303'                        =>'更新失败',
              'ERROR_30304'                        =>'无效数据！',
              'ERROR_30305'                        =>'处理失败！',
              'ERROR_30306'                        =>'数据不存在！',
              'ERROR_30307'                        =>'保存失败！',
              'ERROR_30308'                        =>'操作失败！',
              'ERROR_30309'                        =>'非法操作！已禁止',
              'ERROR_30310'                        =>'添加失败！',
              'ERROR_30311'                        =>'修改失败！',
              'ERROR_30312'                        =>'数据验证错误，请重新操作！',
              'ERROR_30313'                        =>'您没有权限查看该内容！',
              //URL空设置
              'ERROR_30314'                        =>'您访问的页面不存在，请联系管理员！',
              //上传相关
              'ERROR_30402'                        =>'仅允许上传docx文档！',
              'ERROR_30403'                        =>'您上传的文件类型有误！请上传*.xls,*.xlsx;文件',
              //没有引用,暂时保留
              'ERROR_30404'                        =>'上传文件大小超过限制，请上传小于1M的文件！',
              //下载异常
              'ERROR_30405'                        =>'下载异常！请稍后联系管理员。',
              'ERROR_30407'                        =>'图片裁切替换失败！',
              //文档错误
              'ERROR_30501'                        =>'文档生成失败，请重试!',
              'ERROR_30502'                        =>'查询内容不是数字!',
              'ERROR_30503'                        =>'试题排重已禁用!',
              'ERROR_30504'                        =>'查询发生异常！',
              'ERROR_30505'                        =>'您不能搜索非所属学科估分记录！!',
              'ERROR_30506'                        =>'您不能删除非所属学科估分记录！!',
              'ERROR_30507'                        =>'您没有权限删除非所属学科内容！',
              'ERROR_30508'                        =>'请选择学科！',
              'ERROR_30509'                        =>'请先设置原创模板期次！',
              'ERROR_30510'                        =>'模板试题数据插入失败！',
              'ERROR_30511'                        =>'模板试题知识点数据插入失败！',
              'ERROR_30512'                        =>'试题id：【%s】学科或题型为空，请补全信息后重试！',
              //加密狗
              'ERROR_30601'                        =>'无效加密狗！',
              'ERROR_30602'                        =>'加密狗数据验证失败！',
              'ERROR_30603'                        =>'加密狗绑定用户有误！',
              //权限相关
              'ERROR_30604'                        =>'您没有访问此页面的权限!',
              //Model层或者Public下
              'ERROR_30701'                        =>'该试题正在优化中暂时不能编辑！',
              'ERROR_30702'                        =>'已完成优化的试题不能再编辑！',
              'ERROR_30703'                        =>'您选择的试题已不存在！请选择试题后组卷！',
              'ERROR_30704'                        =>'%s',
              //站点开关
              'ERROR_30705'                        =>'文档%s已入库',
              'ERROR_30706'                        =>'该文件不存在',
              'ERROR_30707'                        =>'文档标签规则不能为空！',
              'ERROR_30708'                        =>'解析分配，上传后待审核的试卷暂时不能进行该操作！',
              'ERROR_30709'                        =>'重置密码失败，请重试！',
              'ERROR_30710'                        =>'账号未激活，请查看邮箱内的激活邮件',
              'ERROR_30711'                        =>'无法打开 MS Word',
              'ERROR_30712'                        =>'您不能操作非所属学科内容！',

              'ERROR_30713'                        =>'无法打开这个文件',
              'ERROR_30714'                        =>'添加失败！上传word文档出错请重试。错误信息:【%s】',
              'ERROR_30715'                        =>'文档有误，请检查文档是否正确上传！',
              'ERROR_30716'                        =>'转换信息保存失败!请重试.',
              'ERROR_30717'                        =>'您提取的文档标签有误！',
              'ERROR_30718'                        =>'用户名重复请更换！',
              'ERROR_30719'                        =>'日期格式有误！',
              'ERROR_30720'                        =>'error|输入为空',
              'ERROR_30721'                        =>'error|非法数据【%s】，请更换',
              'ERROR_30725'                        =>'%s',
              'ERROR_30726'                        =>'请填写完整参数',
              'ERROR_30727'                        =>'请选择最后一级地区数据！',
              'ERROR_30728'                        =>'加密锁对应用户不存在！',
              'ERROR_30730'                        =>'请选择学科',
              'ERROR_30731'                        =>'文档写入失败',
              'ERROR_30732'                        =>'您没有找回密码的方式！请联系管理员。',
              'ERROR_30733'                        =>'您没有选择学科！',
              'ERROR_30734'                        =>'班级不存在！',
              'ERROR_30735'                        =>'请选择年级！',
              'ERROR_30736'                        =>'请选择学校！',
              'ERROR_30737'                        =>'写入文档失败！请重试...',
              'ERROR_30738'                        =>'题型：%s试题数量超出最大限制，请重新设置！',
              'ERROR_30739'                        =>'用户名请使用邮箱或手机号！',

              'ERROR_30802'                        =>'请输入教师账号！',
              'ERROR_30803'                        =>'验证错误！请刷新页面后重试。',
              'ERROR_30804'                        =>'密码错误',
              'ERROR_30805'                        =>'作业信息载入失败！',
              'ERROR_30806'                        =>'数据调取失败！请重试...',
              'ERROR_30807'                        =>'模板名称重复，请修改模板名称！',
              'ERROR_30808'                        =>'系统替换失败！您没有权限...',
              'ERROR_30809'                        =>'导学案知识和试题超出！请控制在100以内。',
              'ERROR_30810'                        =>'导学案数据为空！请加入试题或知识后重试。',
              'ERROR_30811'                        =>'删除失败，请不要删除编号为1的超级管理员！',
              'ERROR_30812'                        =>'您没有操作的权限！',
              'ERROR_30813'                        =>'文档未转换，请提取后预览',
              'ERROR_30814'                        =>'无法提取，该文档已经审核完成/失败！',
              'ERROR_30815'                        =>'文档不存在！',
              'ERROR_30816'                        =>'删除失败，请不要删除最近7天内的日志！',
              'ERROR_30817'                        =>'文档名称重复！',
              'ERROR_30818'                        =>'您没有权限提取文档【%s】！',
              'ERROR_30819'                        =>'文档被锁定，您无法提取试题！',
              'ERROR_30820'                        =>'您不能提取非所属学科文档【%s】！',
              'ERROR_30821'                        =>'文档标签规则不能为空！',
              'ERROR_30822'                        =>'添加失败！请添加word文档。',
              'ERROR_30823'                        =>'密码长度过短！至少6位。',
              'ERROR_30824'                        =>'更改状态失败！',
              'ERROR_30825'                        =>'IP地址格式错误！',


              'ERROR_30826'                        =>'您目前没有此功能权限！',
              'ERROR_30827'                        =>'今日已下载(%s次)，请明天再试！',
              'ERROR_30828'                        =>'您所在的用户组该文件本月下载次数共(%s)次，已使用完毕，请下月再试！',
              'ERROR_30829'                        =>'收藏失败！当前权限仅能收藏%s道试题。',
              'ERROR_30830'                        =>'布置作业[下载作答]失败，您当前的权限每月仅能下载%s次！',
              'ERROR_30831'                        =>'当前权限已有存档%s份，存档失败！',
              'ERROR_30832'                        =>'今日已使用【智能组卷】 %s 次，请明天再试！',
              'ERROR_30833'                        =>'今日已使用【模板组卷】%s 次，请明天再试！',
              'ERROR_30834'                        =>'您已经存在:%s个模板，请使用替换功能！',
              'ERROR_30835'                        =>'您需要登录才能进行相关操作！',
              'ERROR_30836'                        =>'该账号身份不是老师，请检查！',
              'ERROR_30837'                        =>'该账号身份不是学生，请检查！',
              'ERROR_30838'                        =>'该账号身份不是兼职，请检查！',

                //支付宝会员权限活动时间价格配置
                'IS_PROMOTION'    => 1,//是否在活动中,手工开启
                'PROM_BEGIN_TIME' => strtotime(date('2016-2-1')),//活动开始时间,unix时间戳
                'PROM_END_TIME'   => strtotime(date('2016-2-29 23:59:59')),//活动结束时间,unix时间戳
                'PROMOTION_SLOGAN'=> '金猴迎春，新年送福！买会员三折并买一送一',
                'VIP_PRICE'       => 15,
                'SUPER_VIP_PRICE' => 30

);
?>