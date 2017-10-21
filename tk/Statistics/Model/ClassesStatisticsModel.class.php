<?php
/**
 * 班级创建区域统计
 * @author demo
 * @date 2015-5-11
 */
namespace Statistics\Model;
class ClassesStatisticsModel extends SystemStatisticsModel{
    protected $cacheName = 'classStatistics';
    protected $classify = 3; //统计数据分类
    /**
     * 从数据库中提取相关数据
     */
    protected function fetchData($mondayOfLastWeek, $mondayOfWeek, $order){
        $data = $this->unionSelect('areaClassTotal', 0, $mondayOfWeek);
        $area = SS('areaParentPath');
        $result = array();
        $index = -1;
        foreach($data as $value){
            $areaId = $area[$value['AreaID']][0]['AreaID'];
            if($result[$index][$areaId]){
                $result[$index][$areaId]++;
            }else{
                $index++;
                $result[$index][$areaId] = 1;
            }
        }
        $result[++$index]['StatTime'] = $mondayOfWeek;
        foreach($result as $key=>$value){
            $result[$key]['StatOrder'] = $order;
        }
        $this->addData($result);
        return $this->disposeResult($this->getDataByOrder($order));
    }

    protected function process($data){
        $area = SS('areaParentPath');
        $result = array();
        foreach($data as $key=>$value){
            if('StatTime' == $value[0]){
                continue;
            }
            $result[$key] = $value;
            $result[$key][0] = $area[$value[0]+1][0]['AreaName'];
            $result[$key][4] = '个';
        }
        return $result;
    }
}