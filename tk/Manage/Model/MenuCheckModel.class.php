<?php
/**
 * 菜单权限代码检查类
 * @author demo
 * @date 2014-11-11    
*/
namespace Manage\Model;
use Common\Model\PowerCheckModel;
class MenuCheckModel extends PowerCheckModel{
    /**
     * 重载PowerCheckModel方法
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
                    if(stripos($str,$v) !== false || ($k == 'method' && strrpos($v,'-index') === false)){
                        unset($data[$keys][$key]);
                        continue;
                    }        
                }
            }
        }
        return $data;
    }
}