<?php
/**
 * @author demo
 * @date 2014年10月14日
 */
/**
 * 用户权限组类，用于处理用户权限组相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class PowerUserModel extends BaseModel{

    /**
     * 根据模块入口生成用户权限
     * @author demo
     */
    public function setCache(){
        $powerUser=$this->getModel('PowerUser')->selectData(
            '*',
            '1=1');
        $group = $this->getGroupName();
              //分组名称;
        $newArr=array();
        $pList=$this->getModel('PowerUserList')->buildCache();
        foreach($powerUser as $iPowerUser){
            $powerUserID[$iPowerUser['PUID']]=$iPowerUser;
            foreach($group as $j=>$jGroup){
                if($iPowerUser['GroupName']==$j){
                    $newArr[$j]['GroupName']=$jGroup;
                    $newTag=explode(',',$iPowerUser['ListID']);
                    $TagArr=array();
                    if($newTag[0]=='all'){
                        $TagArr[]='all';
                    }else{
                        foreach($newTag as $k=>$kNewTag){
                            $TagArr[]=$pList[$newTag[$k]]['PowerTag'];
                        }
                    }
                    $iPowerUser['TagArr']=$TagArr;
                    $newArr[$j]['groupList'][$iPowerUser['PUID']]=$iPowerUser;
                   
                }
            }
        }
        S('powerUserGroup',$newArr);
        S('powerUserId',$powerUserID);

    }
    /**
     * 获取生成用户组权限
     * @author demo
     */
    public function getCache($str='powerUserGroup',$num=0){
        switch($str){
            case 'powerUserGroup':
                $buffer=S('powerUserGroup');
                break;
            case 'powerUserId':
                $buffer=S('powerUserId');
                break;
            default:
                return false;
        }
        if(empty($buffer) && $num==0){
            $this->setCache();
            $buffer=$this->getCache($str,1);
        }
        return $buffer;
    }

    /**
     * 返回数据结果
     * @param int $rule 指定用户组顺序
     * @param boolean $groupIdList 是否返回相同顺序的用户组编号
     * @return array
     * @author demo 2015-11-3 
     */
    public function getTable($rule, $groupIdList=false){
        $group = SS('powerUserId');
        $list = array();
        $result = array();
        //用户组名称描述
        $title = array();
        $title[] = '';
        foreach($group as $key=>$value){
            if(!in_array($value['OrderID'], $rule)){
                unset($group[$key]);
            }else{
                $name = $group[$key]['UserGroup'];
                unset($group[$key]);
                $title[] = $name;
                $group[$key]['list'] = explode(',',$value['ListID']);
                unset($name);
            }
        }
        $result[] = $title;
        $powerList = SS('powerUserByID');
        $order = $this->getOrder($powerList);
        foreach($order as $key=>$value){
            $r = $this->getPowerList($group, $powerList, $value);
            $name = str_replace(array('不限','次数','不允许','允许', '保存'),'',$value['name']);
            $name = preg_replace('/\d+.*$/i', '', $name);
            array_unshift($r, $name);
            $result[] = $r;
            unset($r);
        }
        if($groupIdList){
            return array($result, array_keys($group));
        }
        return $result;
    }

    /**
     * 返回处理后的权限数据
     * @param array $group 用户组信息
     * @param array $powerList 用户具体权限列表
     * @param array getOrder()中的数据
     * @return array
     * @author demo 2015-11-3
     */
    private function getPowerList($group, $powerList, $order){
        $arr = array();
        foreach($group as $key=>$value){
            $des = '';
            $id = array_shift(array_intersect($value['list'], $order['list']));
            if($id){
                $info = $powerList[$id];
                $unit = '';
                if($info['Value'] == 'all'){
                    $unit = 'Y';
                }else if(0 == (int)$info['Value']){
                    $unit = 'N';
                }else{
                    if((int)$info['Unit'] !== 0){
                        $unit = \Manage\Model\PowerUserListModel::$powerCycle[$info['Unit']];
                        if($info['TypeName']){
                            $unit = '('.$info['TypeName'].'/'.mb_substr($unit,3,6).')';
                        }else{
                            $unit = '('.mb_substr($unit,3,6).')';
                        }
                    }else{
                        $unit = $info['TypeName'];
                    }
                    if(170 == $id){
                        $unit = 'Y';
                    }else{
                        $unit = $info['Value'].$unit;
                    }
                }
                $des = $unit;
            }else{
                $des = 'N';
            }   
            $arr[] = $des;
        }
        return $arr;
    }

    /**
     * 返回权限指定顺序的数据
     * @param array $list 用户具体权限列表，一般为缓存
     * @param int $s 指定的OrderID下限，默认为0
     * @param int $e 指定的OrderID上限，默认为100
     * @return array
     * @author demo 2015-11-3
     */
    private function getOrder($list, $s=0, $e=100){
        $arr = array();
        foreach($list as $value){
            if($value['OrderID'] >= $s && $value['OrderID'] < $e){
                $order = $value['OrderID'];
                if(!array_key_exists($order, $arr)){
                }
                $arr[$order]['name'] = $value['PowerName'];
                $arr[$order]['list'][] = $value['ListID'];
            }
        }
        return $arr;
    }
    /**
     * 获取教师所属组名称
     * @author demo
     */
    public function getGroupName(){
        return array('1' => '组卷',
              '2' => '提分',
              '3' => '教师');
    }
}