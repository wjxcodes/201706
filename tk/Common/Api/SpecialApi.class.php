<?php
/**
 * 学科接口
 * @author demo 2015-1-5
 */
namespace Common\Api;
class SpecialApi extends CommonApi{

    /**
     * ID作键的专题
     * @author demo 16-4-11
     */
    public function special(){
        return SS('special');
    }

    /**
     * 专题父级一级分类
     * @author demo 16-4-11
     */
    public function specialParent(){
        return SS('specialParent');
    }

    /**
     * 专题树状图，学科ID为索引
     * @author demo 16-4-11
     */
    public function specialTree(){
        return SS('specialTree');
    }
}