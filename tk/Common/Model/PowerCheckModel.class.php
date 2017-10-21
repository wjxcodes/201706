<?php
/**
 * Action权限检查类
 * @author demo
 * @date 2014-10-31    
*/
namespace Common\Model;
class PowerCheckModel  extends BaseModel{
    //忽略所检测的文件
    protected static $ignore = array(
        'BaseAction'=>'*',
        'EmptyAction'=>'*',
        'PowerAdminListAction'=>'check,ajaxSave',
        'UserPowerAction'=>'check,ajaxSave'
    );
    protected $autoCheckFields = false;    //不自动检测数据表字段信息

    /**
     * 提取指定的目录的文件
     * @param array $list 外部数据
     * @param array $paths 定义多个路径信息
     * @param string $delimiter 指定的分隔符
     * @param string $if=1 是否比对,返回指定路径下所有的function名称
     * @param array 返回查询到的控制器和方法
     * @return array
     */
    public function getComparesData($list,$paths=array(),$delimiter='/',$if=1){
        $data = array();
        foreach($paths as $path){
            $files = $this->getFiles($path);
            foreach($files as $file){
                $className = $this->getFileName($path,$file);
                if(array_key_exists($className,self::$ignore)){
                    $values = self::$ignore[$className];
                    if(stripos($values,'*') !== false){
                        continue;
                    }
                }
                if(!class_exists($className,false)){
                    include_once($file);
                }
                $methods = $this->getMethodsOfClass($className);
                $className = str_replace(array('\\Controller\\', '\\Wln\\'), $delimiter, $className);
                $className = preg_replace('/Controller|Wln$/', '', $className);
                foreach($methods as $method){
                    $method['method'] = $className.$delimiter.$method['method'];
                    $data[$path][] = $method;
                }
            }
        }
        if($if){
            return $this->compare($list,$data);
        }
        return $data;
    }
    /**
     * 查找出数据库信息和方法的差集
     * @param $list 数据列表
     * @param $data 函数列表
     * @return array
    */
    protected function compare($list,$data){
        $tags = array();
        foreach($list as $value){
            $tags[] = $value['PowerTag'];
        }
        $str = implode(',', $tags);
        foreach($data as $keys=>$values){
            foreach($values as $key=>$value){
                foreach($value as $k=>$v){
                    if($k == 'method' && strpos($str,$v) !== false){
                        unset($data[$keys][$key]);
                        continue;
                    }        
                }
            }
        }
        return $data;
    }
    /**
     * 获取指定类的public 方法
     * @param $className 类名
     * @return array 
    */
    private function getMethodsOfClass($className){
        $reflection = new \ReflectionClass($className);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $parentClass = $reflection->getParentClass();
        $data = array();
        $ignore = self::$ignore[$className];
        foreach($methods as $method){
            $methodName =  $method->getName();
            if($parentClass->hasMethod($methodName) || ($ignore && strpos($ignore,$methodName) !== false)){
                continue;
            }
            $data[] = array('method' => $methodName,
                            'comment' => str_replace(array("\r\n",'\r','\n','*','/'), '', $method->getDocComment()));
        }
        return $data;
    }
    /**
     * 获取指定目录下的所有action文件
     * @param $path 目录路径
     * @return array 
    */
    private function getFiles($path){
        //找出模块下Wln文件夹中的所有控制器
        $list = array();
        if('Manage' == strtolower($path)){
            //文件夹层级过多时，可能存在问题，后期需优化
            $paths = array();
            $paths[] = glob(APP_PATH.'*', GLOB_ONLYDIR);
            while(!empty($paths[0])){
                foreach($paths[0] as $val){
                    //如果是目录，同时该路径包Manage
                    if(is_dir($val) && strpos(strtolower($val), 'Manage') === false){
                        $files = glob($val.'/*',GLOB_ONLYDIR);
                        if(!empty($files)){
                            array_push($paths, $files);
                        }
                        if($this->isValidDir($val)){
                            $files = glob($val.'/*');
                            if(!empty($files))
                                $list = array_merge($list, $files);
                        }
                    }
                }
                array_shift($paths);
            }
            // var_dump($paths);
        }
        $path = APP_PATH.$path.'/Controller/';
        return array_merge($list, glob($path.'*Controller.class.php'));
    }

    /**
     * 判断是否为有效的控制器路径
     * @author demo 
     */
    private function isValidDir($val){
        $val = str_replace(APP_PATH, '', $val);
        return substr_count($val, '/') == 1 && strpos($val, '/Wln') !== false;
    }

    /**
     * 只获取Action的文件名
     * @param $path 文件路径信息Manage
     * @param $fileName 文件名
     * @return string 
    */
    private function getFileName($path,$fileName){
        $fileName = str_replace(APP_PATH, '', $fileName);
        $fileName = str_replace('.class.php', '', $fileName);
        return str_replace('/', '\\', $fileName);
    }


    /**
     * 更新model缓存
     * @author demo 15-12-29
     */

    public function upgradeModelCache(){
        $cache = array();
        $this->findFiles(APP_PATH, $cache);
        return $cache;
    }

    /**
     * 递归查询
     * @author demo 
     */
    private function findFiles($dir,&$cache){
        $files = glob($dir.'/*');
        foreach($files as $value){
            if(is_dir($value)){
                $dir = $this->getDirName($value);
                if($dir === 'model'){
                    $files = glob($value.'/*');
                    foreach($files as $file){
                        $ns = $this->getNameSpace($file);
                        $name = $this->getModelName($ns);
                        $cache[$name] = $ns;
                    }
                }else{
                    $this->findFiles($value,$cache);
                }
            }
        }
    }
    //返回模型名称
    private function getModelName($file){
        return trim(strrchr($file, '/'), '/');
    }
    //返回命名空间
    private function getNameSpace($file){
        $file = str_replace(APP_PATH.'/', '', $file);
        return str_replace('Model/', '', strstr($file, 'Model.', true));
    }
    //返回当前目录名称
    private function getDirName($path){
        $path = strtolower($path);
        return trim(strrchr($path, '/'),'/');
    }
}