<?php
/**
 * 班级相关操作方法
 * @author demo
 * @date 2015/8/11
 */
namespace Common\Controller;
class ClassLayerController extends CommonController{
    /**
     * 全站涉及班级动态记录操作
     * @param array $dynamicData
     * @param array $userID
     * @return bool
     * @author demo
     */
    public function addClassDynamic($dynamicData,$userID){
        $dynamic=$this->getModel('Dynamic');
        $dynamicID=$dynamic->addDynamic(
            $dynamicData
        );
        foreach($userID as $i=>$k){
            $dynamicToData[$i]['DynamicID'] = $dynamicID;
            $dynamicToData[$i]['UserID'] = $k;
        }
        $dynamicTo=$this->getModel('DynamicTo');
        $result=$dynamicTo->addDynamicTo(
            $dynamicToData
        );
        if($result){
            return true;
        }else{
            return false;
        }
    }
}