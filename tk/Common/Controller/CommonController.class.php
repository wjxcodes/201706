<?php
/**
 * 默认控制器
 * @author demo 15-12-24
 */
namespace Common\Controller;
use Think\Controller;
class CommonController extends Controller{

    private $mainModuleName = '';//原来分组的模块名
    //【Manage分组】manageVerifyPower 函数内赋值
    public $ifDiff=0; //是否区分用户
    public $ifSubject = 0; //是否区分学科
    public $mySubject = ''; //当前用户所属

    public function __construct(){
        parent::__construct();
        if(!defined('__URL__')) define('__URL__', __CONTROLLER__);
        if(!defined('__PUBLIC__')) define('__PUBLIC__', __ROOT__.'/Public');
    }

    /**
     * 获取带参数固定缓存数据集
     * 包括：能力，地区，学科、专题、题型、知识点、章节、文档属性、用户权限、管理员权限、模板组卷考试类型、自定义打分规则、年级数据
     * ability area subject special types knowledge chapter doctype powerUserList powerAdminList dirExamtype testMark classGrade
     * @param string $param 数组
     *                  ['style'] 调用数据类型
     *                  ['subject'] 学科名称ID
     *                  ['pID'] 父类id
     *                  ['return']为1则返回json为 2返回数据集
     * @return array
     * @author demo
     */
    public function getData($param=array()) {
        if(empty($param)) $param=$_POST;

        if(C('WLN_OPEN_INTERFACE')==1){
            $model=formatString('setFirstUpper',$param['style']);
            $result=$this->getApiCommon($model.'/'.$param['style'].'Cache',$param);
        }else{
            $result=SD($param);
        }

        $return=0;
        if(isset($param['return'])) $return=$param['return'];
        if(empty($return) && IS_AJAX) $return=1;
        if($return==1) return $this->setBack($result);
        return $result;
    }

    /**
     * 编辑器图片上传 2015-5-28
     * @author demo
     */
    public function upload(){
        $dir=$_GET['dir'];
        R('Common/UploadLayer/upload',array($dir));
    }

    /**
     * 返回错误码
     * @param string $errorNum 错误码 多个则以逗号间隔
     * @param int $flag 类型 默认0返回错误页面 1返回ajax数据 2返回字符串
     * @param string $url 跳转路径
     * @param string $replace 错误码中%s替换 多个则以逗号间隔
     * @return string|json
     * @author demo
     */
    protected function setError($errorNum,$flag=0,$url='',$replace='') {
        $mainModule = $this->getMainModuleName();
        if($mainModule=='Home'||$mainModule=='Exam'||$mainModule=='NewTeacher'){
            $tpl = $mainModule.'@Public/alert';
        }elseif($mainModule=='Index'){
            $tpl = $mainModule.'@Common/error';
        }else{
            $tpl = $mainModule.'@Public/error';
        }
        return R('Common/SystemLayer/ajaxSetError',array($errorNum,$flag,$url,$replace,$tpl));
    }

    /**
     * 返回正确数据
     * @param string $data 需要返回的数据
     * @return json
     * @author demo
     */
    protected function setBack($data) {
        //如果是服务器版则返回序列化数据
        if(C('WLN_CURRENT_MODEL')==1){
            $this->getModel('ApiPlugin')->outParam($data);//处理返回结果
        }
        if($this->getMainModuleName()=='Manage'){
            $return = [$data,__URL__,3];
        }else{
            $return = [$data];
        }
        R('Common/SystemLayer/ajaxSetBack',$return);
        exit();
    }

    /**
     * 输出错误信息并跳转
     * @param string $msg_detail 提示信息
     * @param string $link 跳转地址 默认上一页
     * @param bool $auto_redirect = true 是否自动跳转
     * @param int $seconds=3 跳转时间
     * @return array
     */
    protected function showError($msg_detail, $link='', $auto_redirect = true, $seconds = 3){
        $mainModule = $this->getMainModuleName();
        if($mainModule=='Index'){
            $tpl = $mainModule.'@Common/error';
        }else{
            $tpl = $mainModule.'@Public/error';
        }
        R('Common/SystemLayer/showErrorMsg',array($msg_detail,$link,$auto_redirect,$seconds,$tpl));

    }

    /**
     * 提示成功信息并跳转
     * @param  string   $msgDetail        提示信息
     * @param  string   $link=''            跳转地址 默认上一页
     * @param  bool     $autoRedirect =true   是否自动跳转
     * @param  int      $seconds =3       跳转时间
     * @return null
     * @author demo
     */
    protected function showSuccess($msgDetail, $link='', $autoRedirect = true, $seconds = 3) {
        $mainModule = $this->getMainModuleName();
        if($mainModule=='Home'){
            $tpl = $mainModule.'@Public/alert';
        }elseif($mainModule=='Index'){
            $tpl = $mainModule.'@Common/success';
        }else{
            $tpl = $mainModule.'@Public/success';
        }
        R('Common/SystemLayer/showSuccessMsg',array($msgDetail,$link,$seconds,$tpl));
    }

    /**
     * 调用模型或方法
     * @param string $modelName 模型名称
     * @param string $functionName 方法名称
     * @param string 更多参数
     * @return mixed
     * @author demo
     */
    protected function getModel($modelName, $functionName='') {
        static $_loadedModels = [];

        if(!isset($_loadedModels[$modelName])){
            $newModelName=getMap($modelName);

            $newModelName = explode('/', $newModelName);
            $newModelName = '\\'.$newModelName[0].'\\Model\\'.$newModelName[1].'Model';
            //判断文件夹下是否有对应文件
            $filePath=APP_PATH.str_replace('/','/Model/',$newModelName).'Model.class.php';
            if(!class_exists($newModelName)){
                $_loadedModels[$modelName] = new \Common\Model\BaseModel();
                $_loadedModels[$modelName]->setModelName($modelName);
            }else{
                $_loadedModels[$modelName] = new $newModelName();
            }
            if(APP_DEBUG && C('WLN_OPEN_INTERFACE') == 1){
                $_loadedModels[$modelName]->setSoapModel($modelName, MODULE_NAME);
            }
            // //开发环境，类文件不存在时，使用soap进行处理
            // if(!$isSoap && extension_loaded("soap") && ($modelName != 'Base' || $modelName != 'Model') &&
            //     ($_loadedModels[$modelName] instanceof \Think\Model || !is_subclass_of($_loadedModels[$modelName], '\Common\Model\BaseModel')) && APP_DEBUG){
            //     $client = new \SoapClient(null, array(
            //         'location'=>\Common\Model\BaseModel::SOAP_URI.'Manage/Soap/invoke/ns/'.$modelName,
            //         'uri' => \Common\Model\BaseModel::SOAP_URI
            //     ));
            //     return $client;
            // }
            // if($newModelName=='Base') $_loadedModels[$modelName]->setModelName($modelName);
        }

        if(empty($functionName)) return $_loadedModels[$modelName];
        $param=func_get_args();
        $param=array_slice($param, 2);

        return call_user_func_array(array($_loadedModels[$modelName], $functionName), $param);
    }

    /**
     * 描述：对不存在的方法进行处理
     * @author demo
     */
    function __call($functionName, $args){
        //getCookie方法
        if(strpos($functionName,'getCookie')===0){
            return $this->getCookieCommon($functionName,$args);
        }
        //setCookie方法
        if(strpos($functionName,'setCookie')===0){
            return $this->setCookieCommon($functionName,$args);
        }
        //获取接口方法
        if(strpos($functionName,'getApi')===0){
            return getApi($functionName,$args);
        }

        if( 0 === strcasecmp($functionName,ACTION_NAME.C('ACTION_SUFFIX'))) {
            if(method_exists($this,'_empty')) {
                // 如果定义了_empty操作 则调用
                $this->_empty($functionName,$args);
            }elseif(file_exists_case($this->view->parseTemplate())){
                // 检查是否存在默认模版 如果有直接输出模版
                $this->display();
            }else{
                if(C('SHOW_PAGE_ERROR_MORE')==1){
                    E(L('_ERROR_ACTION_').':'.ACTION_NAME);
                    return;
                }
                //记录错误信息
                D('Base')->addErrorLog(array('description'=>'ActionName:'.ACTION_NAME.' FunctionName:'.$functionName.'('.serialize($args).') source:'.$_SERVER['HTTP_REFERER']));
                emptyUrl();
                return;
            }
        }else{
            if(C('SHOW_PAGE_ERROR_MORE')==1){
                E(__CLASS__.':'.$functionName.L('_METHOD_NOT_EXIST_'));
                return;
            }
            //记录错误信息
            D('Base')->addErrorLog(array('description'=>__CLASS__.':'.$functionName.'('.serialize($args).')'));
            emptyUrl();
            return;
        }
    }

    /**
     * 描述：获取分组下的对应Cookie内容
     * @return array
     * @author demo
     */
    private function getCookie($cookieName,$moduleName=''){
        if(!$moduleName){
            $moduleName = $this->getMainModuleName();
        }
        if(C('WLN_OPEN_INTERFACE')==1){
            if($cookieName=='_USER' && ($moduleName=='Home' || $moduleName=='Manage')){
                return 'admin';
            }
            if($cookieName=='_UID' &&$moduleName=='Manage'){
                return 1;
            }
            if($cookieName=='_UID' &&$moduleName=='Home'){
                return 5;
            }
        }
        $moduleName = strtoupper(preg_replace('/\d*/','',$moduleName));
        return cookie(C('WLN_'.$moduleName.'_USER_AUTH_KEY').$cookieName);
    }

    /**
     * 通用获取cookie方法 用于__call
     * @param string $functionName 当前调用的方法名称
     * @param string $args 参数数组
     * @return mixed
     * @author demo
     */
    private function getCookieCommon($functionName,$args){
        $function=preg_replace('/^getCookie/','',$functionName);
        $moduleName = '';
        if(isset($args[0])){
            $moduleName = $args[0];
        }
        //以下方法判断存在的方法
        switch($function){
            case 'UserName':
                $user=$this->getCookie('_USER',$moduleName);
                if(!$user){
                    if(isset($_POST['userName'])) $user=$_POST['userName'];
                    else if(isset($_POST['UserName']))  $user=$_POST['UserName'];
                }
                return $user;
                break;
            case 'UserID':
                $userID = $this->getCookie('_UID',$moduleName);
                if(!$userID && isset($_POST['userID'])) $userID = $_POST['userID'];
                return $userID;
                break;
            case 'Code':
                $code=$this->getCookie('_CODE',$moduleName);
                if(!$code && isset($_POST['userCode'])) $code=$_POST['userCode'];
                return $code;
                break;
            case 'Time':
                return $this->getCookie('_TIME',$moduleName);
                break;
            default:
                return $this->getCookie('_'.strtoupper($function),$moduleName);
        }
    }
    /**
     * 描述：设置分组下的对应Cookie内容
     * @return array
     * @author demo
     */
    private function setCookie($cookieName,$value='',$option=null,$moduleName=''){
        if(!$moduleName){
            $moduleName = $this->getMainModuleName();
        }
        $moduleName = strtoupper(preg_replace('/\d*/','',$moduleName));
        cookie(C('WLN_'.$moduleName.'_USER_AUTH_KEY').$cookieName,$value,$option);
    }

    /**
     * 通用获取cookie方法 用于__call
     * @param string $functionName 当前调用的方法名称
     * @param string $args 参数数组
     * @return mixed
     * @author demo
     */
    private function setCookieCommon($functionName,$args){
        $function=preg_replace('/^setCookie/','',$functionName);
        //以下方法判断存在的方法
        switch($function){
            case 'UserName':
                $user=$this->setCookie('_USER',$args[0],$args[1],$args[2]);
                return $user;
                break;
            case 'UserID':
                return $this->setCookie('_UID',$args[0],$args[1],$args[2]);
                break;
            case 'Code':
                $code=$this->setCookie('_CODE',$args[0],$args[1],$args[2]);
                return $code;
                break;
            case 'Time':
                return $this->setCookie('_TIME',$args[0],$args[1],$args[2]);
                break;
            default:
                return $this->setCookie('_'.strtoupper($function),$args[0],$args[1],$args[2]);
        }
    }

    /**
     * 记录用户日志
     * @param string $module 模型名称
     * @param string $content 日志内容名称
     * @param string $username 用户名 兼容没有用户名的情况
     * @author demo
     */
    public function userLog($module,$content,$username=''){
        $mainModule = $this->getMainModuleName();
        $data=array();
        $data['IfAdmin']=$mainModule=='Manage'?1:0;
        $data['Module']=$module;
        $data['Content']=$content;
        if(!$username){
            $username = $this->getCookieUserName();
        }
        $data['UserName']=$username?$username:'未登录';
        $this->getApiCommon('System/addLog',$data);
    }
    public function adminLog($module,$content){
        $this->userLog($module,$content);
    }

    /**
     * 描述：获取原来分组的模块名
     * mainGroup:Aat AatApi Home Teacher Exam Manage Index School Marking
     * @return string
     * @author demo
     */
    protected function getMainModuleName() {
        if($this->mainModuleName){
            return $this->mainModuleName;
        }
        //先判断是不是后台
        if (file_exists(APP_PATH . MODULE_NAME . '/Manage/' . CONTROLLER_NAME . 'Manage.class.php') !== false) {
            return 'Manage';
        }

        $relationArray = [
            'Index' => 'Index',//官网
            'Task' => 'Index', //任务大厅
            'Yc' => 'Index', //原创平台
            'Home' => 'Home',//组卷端
            'Ga' => 'Home', //智能组卷
            'Dir' => 'Home', //模板组卷
            'Guide' => 'Home', //导学案
            'Manual' => 'Home', //手动组卷
            'Aat' => 'Aat',//提分端
            'AatApi' => 'AatApi',//提分移动端
            'Exercise' => 'Aat,AatApi',//练习
            'Teacher' => 'Teacher',//教师端
            'NewTeacher' => 'Home',//教师端
            'Exam' => 'Exam',//考试预订
            'Manage' => 'Manage', //后台
            'Doc' => 'Index,Home,Manage', //文档
            'Test' => 'Manage,Index', //试题
            'Work' => 'Home', //作业
            'User' => 'Aat,AatApi,Home,Index', //用户
            'Custom' => 'Home',//校本题库
            'Statistics' => 'Home,Manage,Index', //统计
            'School' => 'School', //校长端
            'Analysis' => 'Manage', //数据分析
            'Marking' => 'Manage', //阅卷
        ];
        $moduleName = $relationArray[MODULE_NAME];

        if (strpos($moduleName, ',') !== false) {
            //对应多个原分组，判断是哪个分组，根据控制器名称
            $moduleArray = explode(',', $moduleName);
            $controllerName = array_slice(preg_split("/(?=[A-Z])/", CONTROLLER_NAME), 1, 1)[0];
            if (($key = array_search($controllerName, $moduleArray)) !== false) {
                $this->mainModuleName = $moduleArray[$key];
            }else{
                //匹配失败时的处理
                if(property_exists($this,'relModuleName') && $this->relModuleName){
                    //查找是否定义了relModuleName变量(关系模块)并且非空,如果为真,则为对应分组名称
                    $this->mainModuleName = $this->relModuleName;
                }else{//如果以上情况全部失败,则获取第一个分组
                    $this->mainModuleName = $moduleArray[0];
                }
            }
        } else {
            $this->mainModuleName = $moduleName;
        }
        return $this->mainModuleName;
    }
	/**
	   描述：获取excel文件内容
     * 
     * @return array
     * $filePath 文档路径
	  $sheet  excel内的sheet 默认为0，sheet1
	**/
	public function excelread($filePath='', $sheet=0){
        import("Common.Tool.PHPExcel.PHPExcel");
        import("Common.Tool.PHPExcel.Reader.Excel5");
        import("Common.Tool.PHPExcel.Reader.Excel2007");
		
		header("Content-type: text/html; charset=utf-8");
		
        if(empty($filePath) or !file_exists($filePath))
	    // dump($filePath);die;
		{die('excel文件不存在');}
        $PHPReader = new \PHPExcel_Reader_Excel2007();        //建立reader对象
        if(!$PHPReader->canRead($filePath)){
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($filePath)){
                echo 'no Excel';
                return ;
            }
        }
        $PHPExcel = $PHPReader->load($filePath);        //建立excel对象
        $currentSheet = $PHPExcel->getSheet($sheet);        //**读取excel文件中的指定工作表*/
        $allColumn = $currentSheet->getHighestColumn();        //**取得最大的列号*/
		$allColumn = "F";
        $allRow = $currentSheet->getHighestRow();        //**取得一共有多少行*/
        $data = array();
        
		//读取excel文件中的所有单元格的内容
		for($rowIndex=1;$rowIndex<=$allRow;$rowIndex++){
			$g='';
		//循环读取每个单元格的内容。注意行从1开始，列从A开始
            for($colIndex='A';$colIndex<=$allColumn;$colIndex++){
                $addr = $colIndex.$rowIndex;
                $cell = $currentSheet->getCell($addr)->getValue();
                if($cell instanceof PHPExcel_RichText){ //富文本转换字符串
                    $cell = $cell->__toString();
                }
				if($cell ==''){
					$g++;
				}
                $data[$rowIndex][$colIndex] = $cell;
            }
			if($g > 5){
				unset($data[$rowIndex]);
				unset($g);
			}	
        }
        return $data;
    }  
	
	
	
	
	
	
}