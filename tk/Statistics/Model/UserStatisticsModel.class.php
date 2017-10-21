<?php
/**
 * 用户统计  7056d02e995942069c83940a3a0de3f7.php
 * @author demo
 * @date 2015-5-9
 */
namespace Statistics\Model;
class UserStatisticsModel extends SystemStatisticsModel{
    protected $cacheName = 'userStatistics';
    protected $classify = 2; //统计数据分类
    protected $mapping = array(
        'zcjssl' => array('注册教师数量', '人'),
        'zcxssl' =>  array('注册学生数量', '个'),
        'cjbjsl' =>  array('创建班级数量', '个')/*,
        'xttjsl' =>  array('平均师生比例', '人')*/
    );

    /**
     * 从数据库中提取相关数据
     */
    protected function fetchData($mondayOfLastWeek, $mondayOfWeek, $order){
        $userModel = $this->getModel('User');
        $total = array();
        //查询区间学生注册数量
        $total[0]['zcxssl']= (int)$userModel->selectCount(
            '',
            "Whois=0 AND LoadDate>={$mondayOfLastWeek} AND LoadDate<{$mondayOfWeek}",
            'UserID'
        );

        //查询区间老师注册数量
        $total[1]['zcjssl'] = (int)$userModel->selectCount(
            "Whois=1 AND LoadDate>={$mondayOfLastWeek} AND LoadDate<{$mondayOfWeek}",
            'UserID'
        );

        //查询区间创建班级数量
        $total[2]['cjbjsl'] = (int)$this->getModel('ClassList')->selectCount(
            "LoadTime>={$mondayOfLastWeek} AND LoadTime<{$mondayOfWeek}",
            'ClassID'
        );
        $total[3]['StatTime'] = $mondayOfWeek;
        foreach($total as $key=>$value){
            $total[$key]['StatOrder'] = $order;
        }
        $this->addData($total);
        return $this->disposeResult($this->getDataByOrder($order));
    }
}