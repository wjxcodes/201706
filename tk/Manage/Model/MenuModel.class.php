<?php
/**
 * @author demo  
 * @date 2014年11月28日
 */
/**
 * 后台菜单类，用处理菜单配置相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class MenuModel extends BaseModel{
    /**
     * 根据菜单数组分类菜单；
     * @param array $list 菜单字符串
     * @return array
     * @author demo
     */
    public function formatMenu($list){
        if(empty($list) || !is_array($list)) return;
        $menuArray=array();
        foreach ($list as $i => $iList) {
            if($iList['PMenu']==0){
                $menuArray[$iList['MenuID']]=$iList;
            }
        }
        foreach ($list as $i => $iList) {
            if($iList['PMenu']!=0){
                $menuArray[$iList['PMenu']]['sub'][]=$iList;
            }
        }
        return $menuArray;
    }
    /**
     * 菜单缓存数据
     * @author demo
     */
    public function setCache(){
        $menuList = $this->selectData(
            '*',
            ' 1=1 ',
            'MenuOrder asc');
        $node = $this->formatMenu($menuList);
        S('menu',$node); //所有树状菜单分类
        S('menuList',$menuList); //所有菜单数据集 菜单排序字段正序
    }
    /**
     * 获取缓存
     * @param string $str 缓存名称
     * @param int $num 防止死循环
     * @return array
     * @author demo
     */
    public function getCache($str='menu',$num=0){
        switch($str){
            case 'menu':
                $buffer=S('menu');
                break;
            case 'menuList':
                $buffer=S('menuList');
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