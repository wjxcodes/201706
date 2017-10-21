<?php
/**
 * @author demo
 * @date 2014年12月29日
 */
/**
 * 用户等级管理模型类，用于用户等级管理相关操作
 */
namespace User\Model;
use Common\Model\BaseModel;
class UserLevelValueModel extends BaseModel{

    /**
     * 设置缓存
     * @author demo
     */
    public function setCache(){
        $userLevel=$this->selectData(
            '*',
            '1=1'
        );
        foreach($userLevel as $i=>$iUserLevel){
            $levelValueList[$iUserLevel['LevelID']][$iUserLevel['ValueDesc']]=$iUserLevel;
            $ValueList[$iUserLevel['ValueID']]=$iUserLevel;
        }
        S('powerByLevel',$levelValueList);//按照等级分布获取等级权限
        S('powerByID',$ValueList);//按照权限标示排序
    }
    /**
     * 获取缓存数据
     * @author demo
     */
    public function getCache($str='powerByLevel',$num=0){
        switch($str){
            case 'powerByLevel':
                $buffer=S('powerByLevel');
                break;
            case 'powerByID':
                $buffer=S('powerByID');
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

}
?>