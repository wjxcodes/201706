<?php
/**
 * @author demo
 * @date 2014年11月17日
 */
/**
 * 配置设置模型类，用于处理配置相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
 class SystemConfigModel extends BaseModel{

    /**
     * 缓存数组
     * @author demo
     */
    public function setcache(){
        $buffer = $this->selectData(
            '*',
            '1=1',
            'ConfigID desc');
        foreach($buffer as $ibuffer){
            $systemConfig[$ibuffer['Type']][$ibuffer['ConfigName']]['ConfigID']=$ibuffer['ConfigID'];
            $systemConfig[$ibuffer['Type']][$ibuffer['ConfigName']]['ConfigName']=$ibuffer['ConfigName'];
            $systemConfig[$ibuffer['Type']][$ibuffer['ConfigName']]['Value']=$ibuffer['Value'];
            /*
             * 未用到，以后可能会用到
            $systemConfig[$ibuffer['Type']][$ibuffer['ConfigName']]['Title']=$ibuffer['Title'];
            $systemConfig[$ibuffer['Type']][$ibuffer['ConfigName']]['Desc']=$ibuffer['Desc'];
            $systemConfig[$ibuffer['Type']][$ibuffer['ConfigName']]['Type']=$ibuffer['Type'];
            $systemConfig[$ibuffer['Type']][$ibuffer['ConfigName']]['AddTime']=$ibuffer['AddTime'];
            $systemConfig[$ibuffer['Type']][$ibuffer['ConfigName']]['EditTime']=$ibuffer['EditTime'];
            $systemConfig[$ibuffer['Type']][$ibuffer['ConfigName']]['EditUserName']=$ibuffer['EditUserName'];
            */
        }
        S('systemConfig',$systemConfig);;
    }
    /**
     * 获取缓存数据
     * @param int $type 配置组
     * @param string $configName 配置名称
     * @return array 该配置组下该配置的所有信息
     * @author demo
     */
    public function getCache($type,$configName){
        $configList=S('systemConfig');
        return $configList[$type][$configName];
    }
 } 