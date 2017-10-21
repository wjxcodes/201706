<?php
/**
 * @author demo
 * @date 2014年10月14日
 */
/**
 * 管理员权限组类，用于处理管理员权限组相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class PowerAdminModel extends BaseModel{
   /**
    *
    * 后台权限缓存数据
    * @author demo
    *
    */
    public function setCache(){
        $powerAdminList=$this->getModel('PowerAdminList');
        $powerArr=$this->selectData(
            '*',
            '1=1');
        $newAdmin=$powerAdminList->buildCache();
        foreach($powerArr as $i=>$jPowerArr){
            $newGroup[$jPowerArr['PUID']]['ListID']=$jPowerArr['ListID'];
            $newGroup[$jPowerArr['PUID']]['IfDefault']=$jPowerArr['IfDefault'];
            $newGroup[$jPowerArr['PUID']]['AdminGroup']=$jPowerArr['AdminGroup'];
            $newGroup[$jPowerArr['PUID']]['PUID']=$jPowerArr['PUID'];
            $powerIdArr=explode(',',$jPowerArr['ListID']);
            foreach($powerIdArr as $j=>$jPowerIdArr){
                 $newGroup[$jPowerArr['PUID']]['sub'][$newAdmin[0][$jPowerIdArr]['PowerTag']]=$newAdmin[0][$jPowerIdArr];
            }
        }
        S('powerAdmin',$newGroup);
    }
   /**
    * 获取缓存
    * @param string 需要的缓存名称
    * @return array
    * @author demo
    */
    public function getCache($str='powerAdmin',$num=0){
        switch($str){
            case 'powerAdmin':
                $buffer=S('powerAdmin');
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