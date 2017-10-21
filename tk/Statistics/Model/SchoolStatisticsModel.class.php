<?php
/**
 * 学校统计
 * @author demo
 * @date 2015-5-9
 */
namespace Statistics\Model;
class SchoolStatisticsModel extends SystemStatisticsModel{
    protected $cacheName = 'schoolStatistics';
    protected $classify = 4; //统计数据分类
    /**
     * 从数据库中提取相关数据
     */
    protected function fetchData($mondayOfLastWeek, $mondayOfWeek, $order){
        $data = $this->unionSelect('areaUserTotal', $mondayOfLastWeek, $mondayOfWeek);
        if(!empty($data)){
            $area = SS('areaParentPath');
            $result = array();
            $index = -1;
            foreach($data as $value){
                $areaId = $area[$value['AreaID']][0]['AreaID'];
                if(!$areaId){
                    $areaId = 0;
                }
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
        }else{
            $this->addData(
                array(
                    array(
                        'StatTime' => $mondayOfWeek,
                        'StatOrder' => $order
                    )
                )
            );
        }
        return $this->disposeResult($this->getDataByOrder($order));
    }

    protected function process($data){
        $area = SS('areaParentPath');
        $result = array();
        $index=1;
        foreach($data as $key=>$value){
            if('StatTime' === $value[0]){
                continue;
            }
            $result[$key] = $value;
            if(0 === $value[0]){
                $result[$key][0] = '其他';
            }else{
                $result[$key][0] = $area[$value[0]+1][0]['AreaName'];
            }
            $result[$key][4] = '所';
        }
        return $result;
    }
}