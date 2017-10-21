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
class UserLevelModel extends BaseModel{

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
            $levelList[$iUserLevel['LevelID']]=$iUserLevel;
        }
        S('levelList',$levelList);//按照经验标示排序
    }
    /**
     * 获取缓存数据
     * @author demo
     */
    public function getCache($str='levelList',$num=0){
        switch($str){
            case 'levelList':
                $buffer=S('levelList');
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
     * 根据用户经验等级及用户动作，获取该东西的权限值
     * @param string $userName 用户名
     * @param string $valueDesc 权限名称
     * @param string $testID 试题ID
     * @return string
     * @author demo
     */
    public function getUserLevelPower($userName,$valueDesc,$testID){
        $result='';
        if($valueDesc){
            $levelMsg=$this->getLevelMsg($userName);
            if($levelMsg){
                $powerByList=SS('powerByList');
                $levelID=$levelMsg[0]['LevelID'];
                if(!empty($valueDesc)){
                    $result=$powerByList[$levelID][$valueDesc];  //根据用户经验等级及用户动作，获取该东西的权限值
                }else{
                    return $powerByList[$levelID];
                }
                if($result['IfLucre']==1 && !empty($testID)) {
                    $lastResult = $this->getModel('UserLucre')->insertLucreByTag($userName, $valueDesc, $testID);
                    if ($lastResult) {
                        return $lastResult;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 获取用户等级信息
     * @param string $userName 用户名
     * @param int $ifNext=0 是否查询下一级
     * @return array
     * @author demo
     */
    public function getLevelMsg($userName,$ifNext=0){
        $userMsg=$this->getModel('User')->getInfoByName($userName);   //获取用户信息经验值
        $levelMsg=$this->selectData(            //根据用户经验值，获取用户等级
            '*',
            'LevelExpMin <='.$userMsg[0]['ExpNum'].' and LevelExpMax >'.$userMsg[0]['ExpNum']
        );
        if(empty($levelMsg)){
            //用户目前是最高等级情况下
            $levelMsg=$this->selectData(
                '*',
                'LevelExpMin <='.$userMsg[0]['ExpNum']
            );
        }
        if($ifNext){
            $nextLevelMsg=$this->selectData(            //根据用户经验值，获取用户等级
                '*',
                'LevelExpMin >'.$levelMsg[0]['LevelExpMax'],
                'LevelID asc'
            );
            $levelMsg[1]=$nextLevelMsg[0];
        }
        return $levelMsg;
    }

    /**
     * 获取经验对照表
     * @author demo
     */
    public function levelPower(){
        $powerByLevel=SS('powerByLevel');
        foreach($powerByLevel as $i=>$iPowerByLevel){
            foreach($powerByLevel[$i] as $j=>$jPowerByLevel){
                $newList[$jPowerByLevel['ValueName']]['name']=$jPowerByLevel['ValueName'];
                $newList[$jPowerByLevel['ValueName']]['list'][$i]=$powerByLevel[$i][$j]['LevelDesc'];
            }
        }
        $levelList=SS('levelList');
        foreach($newList as $j=>$jNewList){
            foreach($newList[$j]['list'] as $k=>$kNewList){
                foreach($levelList as $i=>$iLevelist){
                    if(!$newList[$j]['list'][$i]){
                        $newList[$j]['list'][$i]='';
                    }
                    ksort($newList[$j]['list']);
                }
            }
        }
        $result[0]=$newList;
        $result[1]=$levelList;
        return $result;
    }

    /**
     * 批量获取用户等级信息
     * @param $arr array 用户信息数组
     * @author demo
     */
    public function getLevelMsgs($arr){
          //减少查询
          //获取用户等级信息
          $levelInfo = $this->selectData(
                '*',
                '1=1'
          );
          $result = [];
          if($levelInfo) {
              if($arr) {
                  foreach ($arr as $i => $iArr) {
                      $result[$iArr['UserID']] = '';
                      foreach ($levelInfo as $j => $jLevelInfo) {
                            if(($iArr['ExpNum']>=$jLevelInfo['LevelExpMin'] && $iArr['ExpNum']<=$jLevelInfo['LevelExpMax'])
                                               ||
                                $iArr['ExpNum']>=$jLevelInfo['LevelExpMin']){//此处防止等级溢出

                                $result[$iArr['UserID']] = $jLevelInfo['LevelName'];//获取对应等级

                            }

                      }
                  }
              }
          }
          return $result;
    }
}
?>