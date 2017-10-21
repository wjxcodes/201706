<?php
return array( //'配置项'=>'配置值'
              'DB_TYPE'             => 'mysql',
              // 数据库类型
              'DB_HOST'             => '127.0.0.1',
              // 服务器地址
              'DB_NAME'             => 'tk',
              // 数据库名
              'DB_USER'             => 'root',
              // 用户名
              'DB_PWD'              => 'root',
              // 密码
              'DB_PORT'             => 3306,
              // 端口
              'DB_DEBUG'              =>  false,  // 数据库调试模式 3.2.3新增

              'DB_PREFIX'           => 'zj_',
              // 数据库表前缀
              'DB_SQL_LOG'          => true,
              // 记录SQL信息
              'DB_FIELDS_CACHE'    => true,      // 启用字段缓存
              // 数据库字段缓存
              'SHOW_SQL_ERROR'      => 2,
              // 终止错误sql
              'SHOW_PAGE_TRACE'     => false,
              // 显示页面Trace信息
              'LOG_RECORD'          => true,
              // 进行日志记录
              'LOG_LEVEL'           => 'EMERG,ALERT,CRIT,ERR,WARN,NOTICE,INFO,DEBUG,SQL',
              // 允许记录的日志级别
              /*'SHOW_RUN_TIME'      => true,         // 运行时间显示
              'SHOW_ADV_TIME'        => true,         // 显示详细的运行时间
              'SHOW_DB_TIMES'        => true,         // 显示数据库查询和写入次数
              'SHOW_CACHE_TIMES'     => true,         // 显示缓存操作次数
              'SHOW_USE_MEM'         => true,         // 显示内存开销
              'SHOW_LOAD_FILE'       => true,         // 显示加载文件数
              'SHOW_FUN_TIMES'       => true,         // 显示函数调用次数
              */
              'APP_FILE_CASE'       => true,
              // 是否检查文件的大小写 对Windows平台有效
              'WLN_HTTP_LIMIT'          => false,
              //开启防采集

              'APP_SUB_DOMAIN_DEPLOY'=>1, // 开启子域名配置子域名配置 *格式如: '子域名'=>array('分组名/[模块名]','var1=a&var2=b');

              // 为系统关闭而设定的参数
              'WLN_CLOSE_END_TIME' => '2015-7-15 11:10',
              //关闭时间，单位分钟
              'WLN_ClOSE_REASON' => '系统维护中，预计{#$closeEndTime#}更新完毕，给您带来的不便深表歉意.如遇特殊情况时间顺延。',
              //关闭后提示信息
              'WLN_ClOSE_TRUE_AAT' => 0,
              //关闭提分系统
              'WLN_ClOSE_TRUE_HOME' => 0,
              //关闭组卷系统
              'WLN_ClOSE_TRUE_TEACHER' => 0,
              //关闭教师系统
              'WLN_ClOSE_TRUE_MANAGE' => 0,
              //关闭后台系统
              'WLN_ClOSE_TRUE_INDEX' => 0,
              //关闭官网系统

              /*'APP_SUB_DOMAIN_DEPLOY'=>1, // 开启子域名配置
              子域名配置
              *格式如: '子域名'=>array('分组名/[模块名]','var1=a&var2=b');

              'APP_SUB_DOMAIN_RULES'=>array(
                  'zujuan'=>array('Home/'),  // admin域名指向Admin分组
                  'tifen'=>array('Aat/'),  // test域名指向Test分组
              ),*/

              'WLN_URL_404'             => 'http://118.190.90.246/404.html',
              // 定义错误跳转页面URL地址
              'WLN_HTTP'                => 'http://118.190.90.246',
              // 网站地址
              'WLN_INDEX_OPEN_BACK'    => 0,
              //开启备用索引服务器 0不开启 1开启
              'WLN_INDEX_OPEN_EXCLUDE_REPEAT'    => 1,
              //开启排重功能 0不开启 1开启
              'WLN_INDEX_HOST'          => '127.0.0.1',
              // 索引服务器地址
              'WLN_INDEX_HOST_BACK'    => '127.0.0.1',
              //备用 索引服务器地址
              'WLN_DOC_HOST'            => 'http://127.0.0.1:9006',
              //doc转换服务器地址 本地存储请用空"" 远程存储结尾不加/
              'WLN_DOC_HOST_IN'         => 'http://127.0.0.1:9006',
              'WLN_DOC_OPEN_CHECK'            => 0,
              //是否开启word文档上传检查服务 0不开启 1开启 程序需要判断 检测服务器和原服务器是同一台机器则检测没有意义
              'WLN_DOC_HOST_CHECK'     => 'http://127.0.0.1:9006',
              //doc转换服务器地址 本地存储请用空"" 远程存储结尾不加/
              'WLN_DOC_HOST_IN_CHECK'  => 'http://127.0.0.1:9006',
              //doc转换服务器内网地址
              'DOC_HOST_KEY'        => '*&@!#^@JHGB@ITEI@&*!TEOU',
              //doc转换服务器验证码
              'REG_KEY'             => '$%^$#@T$EasdR$#FR$EF$#$F$F',
              //注册找回密码验证码
              'UPLOAD_KEY'            => 'hn12DyiBHm5sKD2vFbbiSmB6m8hsyGmB',
              //用于上传文件生成token
              'APP_KEY'               => 'fjieo89regfjfKDfsd9023rfkdfsdDds',
			  
			  'API_KEY'         => 'fjRV(*&%$*HNFD^ASIDY(*A&SFHUs',//获取基础数据安全码
              //用于APP开发中生成token使用
              'MAIL_INFO'           => array(
                  'Host'     => 'smtp.ym.163.com',
                  'Username' => 'angle@ty.com',
                  'Password' => '12345678',
                  'FromName' => '平台项目组'),
              'WLN_STATISTICAL_CODE'    => array(
                  'HOME'  => '',
                  'AAT'   => '',
                  'INDEX' => '',
                  'EXAM' => '',),
              // 统计代码
              'WLN_NOT_CHECK_LOGIN_IP'  => 1,
              // 是否不检查用户登录ip改变 如果改变则需重新登录
              'SHOW_PAGE_ERROR_MORE'=> 1,
              // 是否开启错误页的详细显示 0不开起  1开启

              'WLN_SEND_AUTH_MOBILE'=>1, //是否开启手机短信验证 0未开启 1开启
              'WLN_SEND_AUTH_EMAIL'=>1, //是否开启邮箱验证 0未开启 1开启
              'WLN_SEND_AUTH_TIMES'=>5, //验证码每天发送次数
              'WLN_SEND_AUTH_OUT_TIME'=>5, //验证码过期时间 单位：分钟

              //支付宝参数
              'ALIPAY_CONFIG'=>array(
                  'partner' =>'2088901505895358',   //这里是你在成功申请支付宝接口后获取到的PID；
                  'key'=>'36wk16t2njtxs5qu4l90v7gjhlfvbftw',//这里是你在成功申请支付宝接口后获取到的Key
                  'sign_type'=>strtoupper('MD5'),
                  'input_charset'=> strtolower('utf-8'),
                  'cacert'=> VENDOR_PATH.'Alipay\\cacert.pem',
                  'transport'=> 'http',
                  'seller_email'=>'12345678@qq.com'//这里是卖家的支付宝账号，也就是你申请接口时注册的支付宝账号
              ),

              //QQ登录参数
              'WLN_QQ_LOGIN_CONFIG'=>array(
                  'appid'       => '101264765',//id
                  'appkey'      => 'fe458b4c4d475ae4cca200265f04eaef',//key
                  'callback'    => 'http://www.ty.com/Index-Index-QQCallBack',//回调地址
                  'scope'       => 'get_user_info',//要获得的权限
                  'errorReport' => true,//是否显示错误信息
              ),

              'WLN_PICK_JS'=>'',
              //js压缩扩展名原版本为空 压缩版.min
              'WLN_OPEN_INTERFACE'=>0,
              //是否开启对外接口0不开启1开启客户端模式2服务器模式
              'WLN_OPEN_INTERFACE_URL'=>'',
              //接口地址
);
?>
