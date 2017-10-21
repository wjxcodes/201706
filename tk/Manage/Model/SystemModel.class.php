<?php
/**
 * @author demo
 * @date 2015年1月6日
 */
/**
 *系统参数管理类，用于处理系统参数相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class SystemModel extends BaseModel{
    /**
     * 更新缓存；
     * @author demo
     */
    public function setCache(){
        $buffer=$this->selectData(
                '*',
                '1=1',
                'SysID ASC');
        foreach($buffer as $i=>$iBuffer){
            $list[$iBuffer['TagName']][$iBuffer['FieldName']]=$iBuffer['Content'];
        }
        S('system',$list);
    }
    /**
     * 获取缓存；
     * @param string $str 缓存名称
     * @param int $num 防止死循环
     * @return array
     * @author demo
     */
    public function getCache($str='system',$num=0){
        switch($str){
            case 'system':
                $buffer=S('system');
                break;
            default :
                return false;
                break;
        }
        if(empty($buffer) and $num==0){
            $this->setCache();
            $buffer=$this->getCache($str,1);
        }
        return $buffer;
    }
}
?>