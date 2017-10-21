<?php
/**
 * @author demo 
 * @date 2014年10月14日
 */
/**
 * 用户权限列表类，用于处理用户权限相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class PowerUserListModel extends BaseModel{
    //周期计算格式
    public static $powerCycle = array(
        0=>'不进行周期计算',10=>'按天',20=>'按周',30=>'按月',40=>'按年'
    );
    //提示语填充字符串
    public static $fillTag = '[X]';
    /**
     * 生成缓存结构
     * @return array 所需缓存结构
     * @author demo
     */
    public function buildCache(){
        $list = $this->getModel('PowerUserList')->selectData(
            'ListID,PowerTag,Value,GroupName',
            '1=1',
            'GroupName ASC,OrderID ASC,ListID ASC');
        $powerIDArr = array(); //权限验证以id为键的数据集
        foreach($list as $i=>$iList){
            $powerIDArr[$iList['ListID']]=$iList;
        }
        return $powerIDArr;
    }

    /**
     * 验证权限标签是否存在逻辑冲突
     * @param string $tag 标签名称
     * @param string $value 该权限的赋值
     * @param string $group 分组
     * @return boolean 存在则返回true
     * @author demo 2015-8-29
     */
    public function verifyTag($tag, $value, $group){
        $arr = array('0', 'all');
        $result = $this->getModel('PowerUserList')->selectData(
            'PowerTag, Value',
            'PowerTag = "'.$tag.'" AND GroupName='.$group
        );
        if(count($result) == 0){
            return false;
        }
        foreach($result as $k=>$v){
            //如果存在相同值，则返回true
            if($v['Value'] == $value){
                return true;
            }
            if(!in_array($v['Value'], $arr)){
                unset($result[$k]);
            }else{
                $result[$k] = $v['Value'];
            }
        }
        //条件为真，则该权限标识中存在权限逻辑冲突或者$value与$result中的某个值存在重复
        return (in_array($value, $arr) && count($result) > 0);
    }
    /**
     * 生成权限缓存数据
     * @author demo
     */
    public function setCache(){
        $list = $this->selectData(
            'ListID,PowerName,PowerTag,Value,GroupName,Unit,OrderID,TypeName',
            '1=1',
            'OrderID ASC,GroupName ASC,ListID ASC');
        $powerTagArr = array(); //分组序号为键 需要验证的控制器数据集
        $powerIDArr = array(); //权限验证以id为键的数据集
        foreach($list as $i=>$iList){
            $powerTagArr[$iList['GroupName']][] = $iList['PowerTag'];
            $powerIDArr[$iList['PowerTag']]=$iList;
            $powerUserID[$iList['ListID']]=$iList;
        }
        foreach($powerTagArr as $i=>$iPowerTagArr){
            $powerTagArr[$i] = array_unique($iPowerTagArr);
        }
        S('powerUserByID',$powerUserID);//以ID为键的权限列表数据集
        S('powerUserList',$powerIDArr); //以控制器为键的权限列表数据集
        S('powerUserTag',$powerTagArr); //分组序号为键 需要验证的控制器数据集
    }
    /**
     * 获取权限缓存数据
     * @param string $str 缓存名称
     * @param int $num 防止死循环
     * @return array
     * @author demo
     */
    public function getCache($str='powerUserList',$num=0){
        switch($str){
            case 'powerUserList':
                $buffer=S('powerUserList');
                break;
            case 'powerUserTag':
                $buffer=S('powerUserTag');
                break;
            case 'powerUserByID':
                $buffer=S('powerUserByID');
                break;
           default:
                return false;
                break;
        }
        if(empty($buffer) && $num==0){
            $this->setCache();
            $buffer=$this->getCache($str,1);
        }
        return $buffer;
    }

    /**
     * 权限检查，需要验证则返回存在于$powerList中的action路径名称
     * @param string $action 名称
     * @param array $userGroupData 用户组数据
     * @param int $modGroup 模块分组id，默认为“组卷”分组
     * @return string|boolean 成功返回最终的action名称，或者返回true(无需验证)
     * @author demo 2015-8-28
     */
    public static function powerVerify($action, $userGroupData, $modGroup=1){
        $verifyList = array();
        //获取权限组选中的权限
        $cache = SS('powerUserGroup'); 
        $cache = $cache[$modGroup];
        //为空时，使用默认组权限
        $userGroup = $userGroupData['GroupID'];
        if(empty($userGroup) || $userGroupData['LastTime'] <= time()){
            foreach($cache['groupList'] as $value){
                if(1 == $value['IfDefault'] && 1 == $value['GroupName']){
                    $verifyList = $value['TagArr'];
                    break;
                }
            }
        }else{
            $verifyList = ($cache['groupList'][$userGroup]['TagArr']);
        }
        $verifyList = array_merge(array_unique($verifyList));
        //查找当前组的所有权限列表
        $allVerifyList = SS('powerUserTag')[$modGroup];
        //获取请求参数
        $get = self::getParams();
        $verify = true;
        //优先运用带参权限标签检查
        if(!empty($get)){
            $verify = self::isContainAllParamsVerify($allVerifyList, $verifyList, $action, $get);
        }

        //查看$action是否存在于所有权限列表的数据
        if(in_array($action, $allVerifyList)){
            return $action;
        }

        if(is_string($verify)){
            return $verify;
        }

        if(in_array(MODULE_NAME.'/'.CONTROLLER_NAME.'/*', $verifyList)){
            return MODULE_NAME.'/'.CONTROLLER_NAME.'/*';
        }

        return true;
    }

    /**
     * 检查$params中是否包含$verifyList中所有参数的内容
     * @param array $allVerifyList 所有权限列表
     * @param array $verifyList 所在组选中的权限，在判断通用权限时使用
     * @param string $action 请求action
     * @param array $params 请求参数
     * @return boolean|string
     * @author demo 2015-8-28
     */
    private static function isContainAllParamsVerify($allVerifyList, $verifyList, $action, $params){
        $commonTag = $specialTag = ''; //通用标签及具体标签
        //查找所有权限列表中的带参通用权限
        foreach($allVerifyList as $key=>$val){
            $p = self::resolveParam($val);
            $size = count($p);
            //当前验证列表中的模块名和action名称
            $format = rtrim(str_replace(implode('/', $p), '', $val), '/');
            //验证列表中不包含参数信息，同时$action不存在列表中
            if($size === 0 || !self::isBelongToTheModule($format, $action)){
                continue;
            }
            /*
                $counter:此变量的用于计算在比较后，
                是否与$p的长度一致，
                以此来确认$params中是否包含所有$p中的内容       
            */
            $counter = 0; 
            for($i=0; $i<$size; $i+=2){
                //当前参数键($p[$i])和值($p[$i+1])存在于$parmas中
                if(isset($params[$p[$i]]) && $params[$p[$i]] === $p[$i+1]){
                    $counter++;
                }
            }

            //比较
            if($counter == $size / 2){
                $p = implode('/', $p);
                $commonTag = MODULE_NAME.'/'.CONTROLLER_NAME.'/*/'.$p; //通用权限
                $specialTag = $action.'/'.$p; //具体权限
                break;
            }
        }
        //在所有权限列表中包含带有指定参数的权限。此处进一步确认是否为指定action的带参权限
        if(!empty($commonTag)){
            //优先验证具体权限是否存在，当前用户组权限列表或者所有权限列表
            if(in_array($specialTag, $verifyList) || in_array($specialTag, $allVerifyList)){
                return $specialTag;
            }
            //验证通用权限
            if(in_array($commonTag, $verifyList) || in_array($commonTag, $allVerifyList)){
                return $commonTag;
            }
        }
        return true;
    }

    /**
     * 返回参数片段，如果没有参数，将返回空数组
     * @param string $val 包含xxx/xxx/xxxx的记录
     * @return array
     * @author demo 2015-8-28
     */
    private static function resolveParam($val){
        $arr = explode('/', $val);
        if(count($arr) > 3){
            return array_splice($arr, 3);
        }
        return array();
    }

    /**
     * 验证$power是否属于权限：xxx/*下的一个方法，或者相等
     * @param string $url 验证的路径
     * @param string $power 当前权限
     * @return boolean
     * @author demo 2015-8-20
     */
    public static function isBelongToTheModule($url, $power){
        if(strpos($url, '*') === false){
            if($url === $power){
                return true;
            }
            return false;
        }
        //检查$power是否为$url下面的一个分组
        $url = strstr($url, '/*', true);
        if(stripos($power, $url) === 0){
            return true;
        }
        return false;
    }

    /**
     * 针对带self::$fillTag的提示语，进行文字替换
     * @param string $promptStr 需处理的提示语
     * @param array|string $fillStr 需填充的信息，可指定多个
     * @return 处理后的内容
     * @author demo 2015-8-19
     */
    public static function formatPrompt($promptStr, $fillStr=array()){
        if(is_string($fillStr)){
            $fillStr = array($fillStr);
        }
        $num = substr_count($promptStr, self::$fillTag);
        if($num === 0){
            return $promptStr;
        }
        $promptStr = explode(self::$fillTag, $promptStr);
        $leng = count($fillStr);
        //如果fillTag出现的次数大于fillStr的长度，补全fillStr的内容
        while($num >= ++$leng){
            $fillStr[] = '';
        }
        foreach($promptStr as $key=>$value){
            $promptStr[$key] = $value.$fillStr[$key];
        }
        return implode('', $promptStr);
    }

    /**
     * 按需返回指定的时间周期
     * @param int $type，为$this->powerCycle中的键
     * @return array array('begin'=>时间戳, 'end'=>时间戳)
     * @author demo 2015-8-18
     */
    public static function getCycleInterval($type){
        $type = (int)$type;
        if(!array_key_exists($type, self::$powerCycle)){
            $type = 0;
        }
        $b = $e = '';
        $time = time();
        switch ($type) {
            //日
            case 10:{
                $b = date('Y-m-d 0:0:0', $time);
                $e = date('Y-m-d 23:59:59', $time);
            }
            break;
            //周
            case 20:{
                $date = getdate($time);
                $monday = abs(abs(7 - ($date['wday']) + 1) - 7); //获取周一
                $day = pow(60, 2) * 24;
                $b = strtotime(date('Y-m-d', $time)) - $day * $monday;
                $e = $b + $day * 7 - 1; //23:59:59
                $b = date('Y-m-d', $b);
                $e = date('Y-m-d H:i:s', $e);
            }
            break;
            //月
            case 30:{
                $b = strtotime(date('Y-m-1 0:0:0', $time));
                $month = getdate($time);
                $month = $month['mon'];
                $days = handleDate('getDays',$month);
                $e = $b + $days * pow(60,2) * 24 - 1; //23:59:59 
                $b = date('Y-m-d', $b);
                $e = date('Y-m-d 23:59:59', $e);
            }
            break;
            //年
            case 40:{
                $b = date('Y-1-1 0:0:0', $time);
                $e = date('Y-12-31 23:59:59', $time);
            }
            break;
            default:{
                return array(0, $time+10000);          
            }
            break;
        }
        return self::convertTimestamp($b, $e);
    }
    /**
     * 将指定参数转换为时间戳
     * @param string $b 开始时间字符串
     * @param string $e 截止时间字符串
     * @return array
     * @author demo
     */
    private static function convertTimestamp($b, $e){
        $b = strtotime($b);
        if($b === false){
            $b = 0;
        }
        $e = strtotime($e);
        if($e === false){
            $e =  time()+10000;
        }
        return array($b, $e);
    }

    /**
     * 生成get参数
     * @return array 参数列表
     * @author demo 2015-8-28
     */
    private static function getParams(){
        $params = explode('/', __INFO__);
        $params = array_splice($params, 3);
        $new = array();
        $count = count($params);
        for($i=0; $i<$count; $i+=2){
            $new[$params[$i]] = $params[$i+1];
        }
        return $new;
    }
    /**
     * 获取分组名称
     * @return array
     * @author demo
     */
    public function getTeacherGroup(){
        return array('1' => '标引教师',
                     '2' => '审核教师',
                     '3' => '公式处理',
                     '4' => '上传试题',
                     '5' => '校本题库',
                     '6' => '原创模板审核');
    }
}