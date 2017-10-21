<?php
/**
 * 地区数据接口
 * @author demo 2015-1-5
 */
namespace Common\Api;
class AreaApi extends CommonApi{
    /**
     * 根据区域ID获取其子类地区
     * @param array $param 参数 格式:
     * $param = array(
     *     'style'=>'area',
     *     'pID'=>区域ID
     * )
     * @return array $return 返回数据 格式:
     * $return = array(
     *     [0] => Array
     *         (
     *            [AreaID] => 2 //区域ID
     *            [AreaName] => 东城区 //地区名称
     *            [Last] => 1   //是否是最后一级(是否包含子地区),1表示是最后一级,0表示包含不是最后一级
     *         ),
     *      ...
     * );
     * @author demo
     */
    public function areaCache($param){
        extract($param);
        $buffer=SS('areaChildList'); //获取地区子类
        return $buffer[$pID];
    }
    /**
     * 获取子类地区
     * @param array $param 参数 格式:
     * $param = array(
     * )
     * @return array $return 返回数据 格式:
     * $return = array(
     * );
     * @author demo
     */
    public function areaChildList(){
        return SS('areaChildList');
    }


    /**
     * 获取区域缓存
     * @author demo 16-5-20
     */
    public function areaList(){
        return SS('areaList');
    }
}