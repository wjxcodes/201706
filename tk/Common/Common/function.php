<?php
/**
 * 通用函数库，该项目下所有通用函数
 */
$sqltrace = array();
$globalArr = array();
//记录sql执行语句
function setSqlTrace($sql){
    global $sqltrace;
    $sqltrace[] = $sql;
}
//返回sql执行语句
function getSqlTrace(){
    global $sqltrace;
    return implode('<br>', $sqltrace);
}

//查看是否移动端登陆
function isMobile() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        return true;

    //此条摘自TPM智能切换模板引擎，适合TPM开发
    if(isset ($_SERVER['HTTP_CLIENT']) &&'PhoneClient'==$_SERVER['HTTP_CLIENT'])
        return true;
    //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
        //找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
    //判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array(
            'nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile'
        );
        //从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    //协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

//对传入的变量进行转义
if (phpversion() < '5.3.0') {
    set_magic_quotes_runtime(0);
    @ ini_set('magic_quotes_sybase', 0);
}
if (get_magic_quotes_gpc()) {
    $_GET = stripslashes_deep($_GET);
    $_POST = stripslashes_deep($_POST);
    $_COOKIE = stripslashes_deep($_COOKIE);
} else {
    $_GET = add_magic_quotes($_GET);
    $_POST = add_magic_quotes($_POST);
    $_COOKIE = add_magic_quotes($_COOKIE);
    $_SERVER = add_magic_quotes($_SERVER);
    $_REQUEST = array_merge($_GET, $_POST);
}

/**
 * 去除转义字符；
 * @param array $value 待转义数组
 * @return array 转义后的数组
 * @author demo
 */
function stripslashes_deep($value) {
    if (is_array($value)) {
        $value = array_map('stripslashes_deep', $value);
    }
    elseif (is_object($value)) {
        $vars = get_object_vars($value);
        foreach ($vars as $key => $data) {
            $value-> {$key}= stripslashes_deep($data);
        }
    } else {
        $value = stripslashes($value);
    }
    return $value;
}
/**
 * 添加转义字符；
 * @param array $array 待转义数组
 * @return array 转义后的数组
 * @author demo
 */
function add_magic_quotes($array) {
    foreach ((array) $array as $k => $v) {
        if (is_array($v)) {
            $array[$k] = add_magic_quotes($v);
        } else {
            $array[$k] = addslashes($v);
        }
    }
    return $array;
}

/**
 * 提取分页信息
 * @param int $count 数据总数
 * @param int $page 当前页数
 * @param int $limit 限定数量
 * @return int
 * @author demo
 * @date 2014-10-23
 */
function page($count,$page,$limit=10){
    if($page=='' || !is_numeric($page) || $page <= 1){
        return 1;
    }
    $n = ceil($count/$limit);
    if($page > $n){
        return $n;
    }
    return $page;
}

/**
 * 根据参数判断并获取缓存
 * @param string $cacheName 缓存名称
 * @return array
 * @author demo
 */
function SS($cacheName){
    $param=array();
    $param['style']='getSingle';
    $param['cacheName']=$cacheName;
    return SD($param);
}
/**
 * 获取带参数固定缓存数据集
 * 包括：能力，地区，学科、专题、题型、知识点、章节、文档属性、用户权限、管理员权限、模板组卷考试类型、自定义打分规则、年级数据
 * ability area subject special types knowledge chapter doctype powerUserList powerAdminList dirExamtype testMark classGrade
 * @param string $param 数组
 *                  ['style'] 调用数据类型
 *                  ['subject'] 学科名称ID
 *                  ['pID'] 父类id
 * @return array
 * @author demo
 */
function SD($param=array()) {
    $apiCache=D('ApiCache');
    $style=$param['style'].'Cache';
    return $apiCache->$style($param);
}


/**
 * 描述：使用tool文件夹下的方法集合
 * @param string $className 类名或者第三方工具类的路径
 * @param string $functionName 方法名
 * @param array $param 参数集合
 * @author demo
 */
function useToolFunction($className,$functionName='',$param=array()){
    $thisClassName=explode('/',$className);
    $thisClassName=$thisClassName[count($thisClassName)-1];
    if(!class_exists($thisClassName)){
        $importClassName=str_replace('/','.',$className);
        import('Common.Tool.'.$importClassName);
    }
    $model = new $thisClassName(); // 实例化
    if($functionName) {
        return call_user_func_array(array($model, $functionName), $param);
    }
    return $model;
}

/**
 * 描述：格式化字符串通用方法，调用Tool/String.class.php中方法
 * @param string $functionName 函数名
 * @author demo
 */
function string($functionName){
    $param = func_get_args();
    $param = array_splice($param,1);
    return useToolFunction('String',$functionName,$param); //支持3个参数超出用数组
}

/**
 * 描述：格式化字符串通用方法，调用Tool/StringFormat.class.php中方法
 * @param string $functionName 函数名
 * @author demo
 */
function formatString($functionName){
    $param = func_get_args();
    $param = array_splice($param,1);
    return useToolFunction('StringFormat',$functionName,$param); //支持3个参数超出用数组
}

/**
 * 描述：检测字符串通用方法，调用Tool/StringCheck.class.php中方法
 * @param string $functionName 函数名
 * @author demo
 */
function checkString($functionName){
    $param = func_get_args();
    $param = array_splice($param,1);
    return useToolFunction('StringCheck',$functionName,$param); //支持3个参数超出用数组
}

/**
 * 日期相关处理 调用Tool/DateHandle.class.php中方法
 * @param string $functionName 函数名
 * @notice 以后对应日期处理相关函数 请封装至Tool/DateHandle.class.php类中
 * @author demo
 */
function handleDate($functionName){
    $param = func_get_args();
    $param = array_splice($param,1);
    return useToolFunction('DateHandle',$functionName,$param); //支持3个参数超出用数组
}

/**
 * 分页相关处理
 * @param $functionName
 * 请统一此方法处理分页相关处理
 */
function handlePage($functionName){
    //@notice 调用Page类相关方法,请先调用init方法
    $param = func_get_args();
    $param = array_splice($param,1);
    return useToolFunction('WLNPage',$functionName,$param); //支持3个参数超出用数组
}

/**
 * 描述：控制器扩展对空模型进行提示
 * @author demo
 */
function __hack_module(){
    emptyUrl();
}

/**
 * 描述：控制器扩展对空方法进行提示
 * @author demo
 */
function __hack_action(){
    emptyUrl();
}

/**
 * 描述：不存在的链接进行错误提示
 * @author demo
 */
function emptyUrl(){
    if(($a=A(CONTROLLER_NAME,'Manage'))!==false){
        $b=ACTION_NAME;
        $a->$b();
        return ;
    }
    $map = C('LOSE_URL_JUMP');
    if(isset($map[CONTROLLER_NAME])){
        header('Location:'.$map[CONTROLLER_NAME]);
        exit;
    }
    if(IS_AJAX){
        R('Common/SystemLayer/ajaxSetError',array('00139',1));
    }
    R('Common/SystemLayer/jumpError');
    exit();
}

/**
 * 上传数据到远程
 * @param string $postData 提交数据
 * @param string $postUrl 提交地址
 * @return String
 * @author demo
 */
function CURL($postData,$postUrl){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $postUrl);
    curl_setopt($curl, CURLOPT_POST, 1 );
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl,CURLOPT_USERAGENT,"Mozilla/4.0");
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    $result = curl_exec($curl);
    $error = curl_error($curl);
    $output = $error ? $error : $result;
    return $output;
}

/**
 * 描述：插件调用分组下通用方法
 * @author demo
 */
function WB($fName,$data){
    //如果是远程则请求服务器上数据
    if(C('WLN_CURRENT_MODEL')==0){
        $param=array();
        $param['functionName']=$fName;
        $param['param']=$data;
        $output=D('ApiPlugin')->getActionResult($param);
        if($output['result'])  return $output['result'];
        exit();
    }
    $output=R(MODULE_NAME.'/Base/'.$fName,$data);

    if($output) return $output;
}

/**
 * 描述：对远程的C方法进行转向
 * @author demo
 */
function WC($configName,$value=''){
    //如果是远程则转入ApiPlugin接口 getPublicResult
    if(C('WLN_CURRENT_MODEL')==0){
        $param=array();
        $param['configName']=$configName;
        $param['value']=$value;
        $output=D('ApiPlugin')->getConfigResult($param);
        if($output['result'])  return $output['result'];
    }
    return C($configName,$value);
}

/**
 * 描述：对远程的D方法进行转向
 * @author demo
 */
function WD($name='',$layer=''){
    //如果是远程则转入ApiPlugin接口 getMethodResult
    if(C('WLN_CURRENT_MODEL')==0){
        $apiPlugin=D('ApiPlugin');
        $apiPlugin->setName($name,$layer);
        return $apiPlugin;
    }

    return D($name,$layer);
}
/**
 * 描述：对远程的R方法进行转向
 * @author demo
 */
function WR($url,$vars=array(),$layer=''){
    //如果是远程则转入ApiPlugin接口 getPublicResult
    if(C('WLN_CURRENT_MODEL')==0){
        $param=array();
        $param['functionName']=$url;
        $param['layerName']=$layer;
        $param['param']=$vars;
        $output=D('ApiPlugin')->getPublicResult($param);
        if($output['result'])  return $output['result'];
    }
    return R($url,$vars,$layer);
}
/**
 * 描述：对远程的S方法进行转向
 * @author demo
 */
function WS($cacheName,$value=''){
    //如果是远程则转入ApiPlugin接口 getPublicResult
    if(C('WLN_CURRENT_MODEL')==0){
        $param=array();
        $param['cacheName']=$cacheName;
        $param['value']=$value;
        $output=D('ApiPlugin')->getCacheResult($param);
        if($output['result'])  return $output['result'];
    }
    return SS($cacheName,$value);
}

/**
 * Token检查
 * @return bool
 * @author demo
 */
function checkToken($data){
    if(C('TOKEN_ON')){
        $name   = C('TOKEN_NAME');
        if(!isset($data[$name]) || !isset($_SESSION[$name])) { // 令牌数据无效
            return false;
        }
        // 令牌验证
        list($key,$value)  =  explode('_',$data[$name]);

        if(isset($_SESSION[$name][$key]) && $value && $_SESSION[$name][$key] === $value) { // 防止重复提交
            if(!IS_AJAX){//Ajax重复提交兼容 否则前台所有Ajax都要追加设置变更Token
                unset($_SESSION[$name][$key]); // 验证完成销毁session
            }
            return true;
        }
        // 开启TOKEN重置
        if(C('TOKEN_RESET')) unset($_SESSION[$name][$key]);
        return false;
    }
    //Toekn未开启直接返回true
    return true;
}

/**
 * 描述：curl模拟post
 * @todo 之后可以使用一个功能更丰富的curl类作为全站的工具类
 * @param string $url
 * @param array $paramData
 * @param array $header
 * @param bool $isPost
 * @return bool|array
 * @author demo
 */
function simpleCurl($url,$paramData,$header,$isPost=true)
{
    // 创建一个新cURL资源
    $ch = curl_init();
    // 设置URL和相应的选项
    $options = [
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HEADER => 0,
        CURLOPT_URL => $url,
        CURLOPT_POST => $isPost,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_DNS_CACHE_TIMEOUT => 600,
//            CURLOPT_PROXY => 'http://10.162.196.165:3333',
//            CURLOPT_PROXYUSERPWD => 'getSms:wln#tianxing',
    ];
    if($isPost){
        $options[CURLOPT_POSTFIELDS] = $paramData;
    }
    if($header){
        $options[CURLOPT_HTTPHEADER] = $header;
    }
    curl_setopt_array($ch, $options);
    // 抓取URL并把它传递给浏览器
    $result = curl_exec($ch);
    // 关闭cURL资源，并且释放系统资源
    curl_close($ch);
    return $result;//可能取值false string json xml
}

/**
 * 描述：获取静态方法
 * @return string
 * @author demo
 */
function getStaticFunction($modelName,$functionName){
    $modelName='\\'.str_replace('/','\\Model\\',getMap($modelName)).'Model';
    if(method_exists($modelName,$functionName)){
        $param=func_get_args();
        $param=array_slice($param, 2);

        return call_user_func_array(array($modelName, $functionName), $param);
        //return $modelName::$functionName($param[0], $param[1], $param[2]);
    }
    return $modelName::$$functionName;

}

/**
 * 描述：获取map
 * @return array
 * @author demo
 */
function getMap($modelName){
    $cache = S('models');
    if(empty($cache)){
        $model = D('Common/PowerCheck');
        $cache = $model->upgradeModelCache();
        S('models', $cache);
    }

    if($cache[$modelName]){
        return $cache[$modelName];
    }

    return $modelName;
    // return $map=array(
    //     //智能组卷
    //     'Ga'=>'Ga/Ga',

    //     //Doc文档
    //     'DocAbiCapt'=>'Doc/DocAbiCapt',
    //     'DocArea'=>'Doc/DocArea',
    //     'DocDown'=>'Doc/DocDown',
    //     'Doc'=>'Doc/Doc',
    //     'DocSave'=>'Doc/DocSave',
    //     'DocShare'=>'Doc/DocShare',
    //     'DocSource'=>'Doc/DocSource',
    //     'DocTestData'=>'Doc/DocTestData',
    //     'DocType'=>'Doc/DocType',
    //     'DocFile'=>'Doc/DocFile',
    //     'RandomIdentification'=>'Doc/RandomIdentification',

    //     //Test试题
    //     'Test'=>'Test/Test',
    //     'TestAttr'=>'Test/TestAttr',
    //     'TestReal'=>'Test/TestReal',
    //     'TestAttrReal'=>'Test/TestAttrReal',
    //     'TestDown'=>'Test/TestDown',
    //     'TestJudge'=>'Test/TestJudge',

    //     //Work下班级管理部分 提分端也要使用
    //     'ClassGrade'=>'Work/ClassGrade',
    //     'ClassList'=>'Work/ClassList',
    //     'ClassUser'=>'Work/ClassUser',
    //     'UserSendWork'=>'Work/UserSendWork',
    //     'UserWorkClass'=>'Work/UserWorkClass',
    //     'UserWorkKl'=>'Work/UserWorkKl',
    //     'UserWorkKls'=>'Work/UserWorkKls',
    //     'UserWork'=>'Work/UserWork',
    //     'UserWorkUser'=>'Work/UserWorkUser',

    //     //Home下
    //     'TestpaperCenterLog'=>'Home/TestpaperCenterLog',

    //     //Index下
    //     'Feedback' =>'Index/Feedback',
    //     'Question'=>'Index/Question',

    //     //导学案
    //     'CaseTpl' => 'Guide/CaseTpl',//导学案模板
    //     'CaseLoreQuery' => 'Guide/CaseLoreQuery',//导学案个人知识与系统知识处理

    //     //模板组卷Dir
    //     'DirExamType'=>'Dir/DirExamType',//模板组卷考试类型

    //     //用户
    //     'User' => 'User/User',//用户
    //     'RegisterLog' => 'User/RegisterLog',//注册日志
    //     'UserCatalog' => 'User/UserCatalog',//用户收藏目录
    //     'UserChapter' => 'User/UserChapter',//用户章节
    //     'UserCollect' => 'User/UserCollect',//用户收藏试题
    //     'UserInvitation' => 'User/UserInvitation',//邀请码
    //     'UserExp' => 'User/UserExp',
    //     'UserExpRecord' => 'User/UserExpRecord',

    //     //后台
    //     'Index'                =>   'Manage/Index',          //索引查询
    //     'AutoInc'              =>   'Manage/AutoInc',        //自增编号
    //     'Chapter'               =>   'Manage/Chapter',      //章节
    //     'ChapterKl'            =>   'Manage/ChapterKl',      //章节知识点
    //     'Knowledge'            =>   'Manage/Knowledge',      //知识点
    //     'Dynamic'              =>   'Manage/Dynamic',        //班级动态
    //     'DynamicTo'            =>   'Manage/DynamicTo',      //班级动态日志
    //     'KlStudy'              =>   'Manage/KlStudy',        //考点学习管理
    //     'LogSms'               =>   'Manage/LogSms',         //短信验证码
    //     'Log'                  =>   'Manage/Log',            //全站用户操作
    //     'News'                 =>   'Manage/News',           //前后台获取新闻用户操作
    //     'OrderList'            =>   'Manage/OrderList',      //订单处理
    //     'PartnerSchool'        =>   'Manage/PartnerSchool',  //合作学校
    //     'Pay'                  =>   'Manage/Pay',            //平台支付
    //     'UserIp'               =>   'Manage/UserIp',         //用户IP
    //     'UserGroup'            =>   'Manage/UserGroup',      //用户所在分组
    //     'School'                =>   'Manage/School',      //学校
    //     'SoftKey'              =>   'Manage/SoftKey',        //加密狗验证类
    //     'SystemEditionLog'     =>   'Manage/SystemEditionLog',      //系统编辑
    //     'TmpDocKl'             =>   'Manage/TmpDocKl',       //自动入库文档知识点
    //     'TmpFilePath'          =>   'Manage/TmpFilePath',    //存储自动入库文件路径
    //     'TmpLog'               =>   'Manage/TmpLog',         //自动入库日志
    //     'Topic'                =>   'Manage/Topic',          //程序生成的文件 ->提分端使用
    //     'TopicPaper'           =>   'Manage/TopicPaper',     //程序生成的文件 ->提分端使用
    //     'Types'                =>   'Manage/Types',          //题型编辑
    //     //原创平台
    //     'OriginalityRelateTest' => 'Original/Model',

    // );
}

/**
 * 通用获取API方法 用于__call
 * @param string $functionName 当前调用的方法名称
 * @param string $args 参数数组
 * @return mixed
 * @author demo
 */
function getApi($functionName,$args){

    $module=str_replace('getApi','',$functionName);

    $interface=C('WLN_OPEN_INTERFACE');
    if($interface===1){
        $postUrl=C('WLN_OPEN_INTERFACE_URL');
        $postData=array();
        $postData['md']=$module;
        $postData['pm']=serialize($args);
        $postData['mod'] = MODULE_NAME;
        $result=CURL($postData,$postUrl);
        return unserialize($result); //返回数据
    }

    //处理$args[0]目前结构为Grade/grade
    $urlArray=explode('/',$args[0]);

    $model=A($module.'/'.$urlArray[0],'Api');
    if($model===false) return '';
    $param=$args;
    $param=array_slice($param, 1);
    return call_user_func_array(array($model, $urlArray[1]), $param);
}

/**
 * 兼容低版本的函数<5.5
 * @author demo
 */
if(!function_exists('array_column')){
     function array_column($input, $columnKey, $indexKey=null){
         $columnKeyIsNumber   = (is_numeric($columnKey))?true:false;
         $indexKeyIsNull      = (is_null($indexKey))?true :false;
         $indexKeyIsNumber    = (is_numeric($indexKey))?true:false;
         $result                         = array();
         foreach((array)$input as $key=>$row){
             if($columnKeyIsNumber){
                 $tmp= array_slice($row, $columnKey, 1);
                 $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null;
             }else{
                 $tmp= isset($row[$columnKey])?$row[$columnKey]:null;
             }
             if(!$indexKeyIsNull){
                 if($indexKeyIsNumber){
                     $key = array_slice($row, $indexKey, 1);
                     $key = (is_array($key) && !empty($key))?current($key):null;
                     $key = is_null($key)?0:$key;
                 }else{
                     $key = isset($row[$indexKey])?$row[$indexKey]:0;
                 }
             }
             $result[$key] = $tmp;
         }
         return $result;
     }
}


/**
 * 获取当前路径
 * @param string $dep 路径间隔
 * @return string
 * @author demo
 */
function SP($dep = '/') {
    if (empty($dep)) {
        $dep = C('URL_PATHINFO_DEPR');
    }
    return MODULE_NAME . $dep . CONTROLLER_NAME . $dep . ACTION_NAME;
}

/**
 * 获取当前模型对应db类
 * @author demo
 */
function SM() {
    //处理$args[0]目前结构为Grade/grade
    $param = func_get_args();

    $urlArray = explode('/', $param[0]);
    $modelName = $urlArray[0];

    if (file_exists(COMMON_PATH . 'Model/' . $modelName . 'Model.class.php')) {
        $model = D($modelName);
    } else {
        $newModelName=getMap($modelName);
        if($newModelName==$modelName){
            $model = D('Base');
            $model->setModelName($modelName);
        }else{
            $model = D($newModelName);
        }
    }

    if (count($urlArray) == 1)
        return $model;

    $param = array_slice($param, 1);
    return call_user_func_array(array($model, $urlArray[1]), $param);
}

/**
 * 获取当前模型对应逻辑类
 * @author demo
 */
function SL() {
    //处理$args[0]目前结构为Grade/grade
    $param = func_get_args();

    $urlArray = explode('/', $param[0]);
    $modelName = $urlArray[0];

    if (file_exists(COMMON_PATH . 'Logic/' . $modelName . 'Logic.class.php')) {
        $model = D($modelName, 'Logic');
    } else {
        //错误记录
        $data = array();
        $data['description'] = '逻辑层声明失败，模型【' . $modelName . '】不存在';
        D('LogError')->setLine($data);
        E('逻辑层声明失败，模型【' . $modelName . '】不存在');
        exit();
    }

    if (count($urlArray) == 1)
        return $model;

    $param = array_slice($param, 1);
    return call_user_func_array(array($model, $urlArray[1]), $param);
}

/**
 * 获取接口数据
 * @return null
 * @author demo
 */
function SA() {
    //处理$args[0]目前结构为Grade/grade
    $param = func_get_args();
    $params = explode('/', $param[0]);
    $modelName = $params[0];
    if (file_exists(COMMON_PATH . 'Api/' . $modelName . 'Api.class.php')) {
        $model = D($modelName, 'Api');
    } else {
        //错误记录
        $data = array();
        $data['description'] = '接口层声明失败，接口【' . $modelName . '】不存在';
        D('LogError')->setLine($data);
        E('接口层声明失败，接口【' . $modelName . '】不存在');
        exit();
    }

    if (count($params) == 1)
        return $model;

    $param = array_slice($param, 1);
    return call_user_func_array(array($model, $params[1]), $param);
}

/**
 * 获取路径参数数据
 * @return null
 * @author demo
 */
function SU($num) {
    $param = $_SERVER['REQUEST_URI'];
    $param = str_replace('.' . C('URL_HTML_SUFFIX'), '', $param); //去掉后缀
    $param = preg_replace('/\?\=/', C('URL_PATHINFO_DEPR'), $param); //替换问号和等号
    $paramArray = explode(C('URL_PATHINFO_DEPR'), $param);
    return $paramArray[$num];
}