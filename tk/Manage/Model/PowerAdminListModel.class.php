<?php
/**
 * @author demo 
 * @date 2014年10月14日
 */
/**
 * 管理员权限列表类，用于处理管理员权限相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class PowerAdminListModel extends BaseModel{
    /**
     * 构造所需数据结构(生成缓存所需)
     * @author demo 
     *
     */
    public function buildCache(){
        $newAdmin=array(); //以id为键的数据集
        $powerAdminList=$this->selectData(
            '*',
            '1=1',
            'OrderID ASC,ListID ASC');
        foreach($powerAdminList as $i=>$iPowerAdminList){
            $newAdmin[0][$iPowerAdminList['ListID']]=$iPowerAdminList;
            $newAdmin[1][$iPowerAdminList['PowerTag']]=$iPowerAdminList;
        }
        return $newAdmin;
    }
    /**
     * @后台权限缓存数据
     * @author demo
     */
    public function setCache(){
        $newAdmin=$this->buildCache();
        S('powerAdminList',$newAdmin[0]); //管理员权限ID为键
        S('powerAdminListTag',$newAdmin[1]); //管理员权限ID为键
    }
    /**
     * 获取缓存
     * @param string $str 缓存名称
     * @param int $num 防止死循环
     * @return array
     * @author demo
     */
    public function getCache($str='powerAdminList',$num=0){
        switch($str){
            case 'powerAdminList':
                $buffer=S('powerAdminList');
                break;
            case 'powerAdminListTag':
                $buffer=S('powerAdminListTag');
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
}