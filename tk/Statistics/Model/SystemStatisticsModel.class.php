<?php
/**
 * 系统统计model已修改为AjaxStatisticsModel，目前作为部分统计model的基类
 * @author demo
 * @date 2015-5-6
 */
namespace Statistics\Model;
class SystemStatisticsModel extends StatisticsModel{

    protected $day = 0;

    protected $classify = 1; //统计数据分类

    protected $intevalDate = 0;

    protected $mapping = array();  

    public function __construct(){
        parent::__construct();
        $this->intevalDate = $this->getQueryIntervalDate('w');
        $this->day = 60 * 60 * 24;
    }

    /**
     * 返回数据
     * @return array
     */
    public function getResult(){
        if(!$this->isExpire()){
            $order = $this->getBiggestOrder();
            if(!$order){
                $order = 0;
            }
            $currentData = $this->disposeResult($this->getDataByOrder($order));
            $expireDate = $this->getCacheInfo('expireDate');
            //如果$order的数据存在，计算出区间数据
            $cycleCount = 0;
            if($currentData){
                $cycleCount = $this->getIntervalCountOfCycle($currentData['StatTime']);
            }
            if(!$expireDate && $currentData){
                $expireDate = $currentData['StatTime'] + $this->day * 7;
            }
            //如果不存在数据，当前最近一个周期的时间小于当前时间
            if(!$currentData || time() >= $expireDate){
                $order++;
                $mondayOfLastWeek= $this->intevalDate['startDate'] + $this->day; //获取上上周日时间戳
                $mondayOfWeek= $this->intevalDate['endDate'] + $this->day; //获取上周日时间戳
                //当order为1（没有数据）或者与上阶段的数据相差1个周期以上，将插入两个周期的数据
                if(1 == $order || $cycleCount > 1){
                    $this->fetchData($mondayOfLastWeek-$this->day*7, $mondayOfLastWeek, $order++);
                }
                $currentData = $this->fetchData($mondayOfLastWeek, $mondayOfWeek, $order);
                $this->setCache('expireDate', $this->intevalDate['endDate'] + $this->day * 8);
            }
            //查询出上一个order的数据
            $order--;
            $preData = $this->disposeResult($this->getDataByOrder($order));
            $increment = $this->computeUpgradeNum($currentData, $preData); //计算增量
            $data = array();
            foreach($increment as $key=>$value){
                $data[] = array(
                    $key, 
                    (int)$preData[$key], 
                    (int)$currentData[$key], 
                    (int)$increment[$key]
                );
            }
            $this->setCache('data', $data);
        }
        $data = $this->getCacheInfo('data');
        return $this->process($data);
    }

    /**
     * 验证当前的缓存是否有效
     * @return boolean 失效返回false
     */
    protected function isExpire(){
        if(!$this->isCache('data')){
            return false;
        }     
        $expireDate = $this->getCacheInfo('expireDate');
        if(!$expireDate){
            return false;
        }
        if($expireDate < time()){
            return false;
        }
        return true;
    }

    /**
     * 从数据库中提取相关数据
     * @param int $mondayOfLastWeek 上周一时间戳 00:00
     * @param int $mondayOfWeek 本周一时间戳 00:00
     * @return array
     */
    protected function fetchData($mondayOfLastWeek, $mondayOfWeek, $order){
        $UserModel = $this->getModel('User');
        //注册用户数量
        $lastUserNum=$UserModel->selectCount( //对上周用户数量进行统计
            ' LoadDate <'.$mondayOfWeek,
            'UserID'
        );
        $this->debugSql();
        $totalContent[0]['zcyhsl'] = (int)$lastUserNum;
        //周活跃用户
        $lastHotUserNum=$UserModel->selectCount( //对上周活跃用户数量进行统计
            ' LastTime >='.$mondayOfLastWeek.' and LastTime <'.$mondayOfWeek,
            'UserID'
        );
        $this->debugSql();
        $totalContent[1]['zhyrh'] = (int)$lastHotUserNum;
        $TestAttrReal = $this->getModel('TestAttrReal');
        //系统试题总量
        $lastTestNum=$TestAttrReal->selectCount( //对上周系统试题总量进行统计
            ' FirstLoadTime <'.$mondayOfWeek,
            'TestID'
        );
        $this->debugSql();
        $totalContent[2]['xtstsl'] = (int)$lastTestNum;
        $Doc = $this->getModel('Doc');
        //系统套卷总量
        $lastDocNum=$Doc->selectCount( //对上周系统套卷总量进行统计
            'IntroFirstTime <'.$mondayOfWeek,
            'DocID'
        );
        $this->debugSql();
        $totalContent[3]['xttjsl'] = (int)$lastDocNum;
        //系统组卷次数
        $lastComNum=$UserModel->sumData( //对上周系统组卷次数进行统计
            'ComTimes',
            'LastTime <'.$mondayOfWeek
        );
        $this->debugSql();
        $totalContent[4]['xtzjcs'] = (int)$lastComNum;
        //周试卷更新量
        $lastDocWeekNum=$Doc->selectCount( //对上周周试卷更新量进行统计
            'IntroFirstTime >= '.$mondayOfLastWeek.' and IntroFirstTime <'.$mondayOfWeek,
            'DocID'
        );
        $this->debugSql();

        $totalContent[5]['zsjgxl'] = (int)$lastDocWeekNum;
        //周试题更新量
        $lastTestWeekNum=$TestAttrReal->selectCount( //对上周周试题更新量进行统计
            'FirstLoadTime >='.$mondayOfLastWeek.' and FirstLoadTime <'.$mondayOfWeek,
            'TestID'
        );
        $this->debugSql();
        $totalContent[6]['zstgxl'] = (int)$lastTestWeekNum;
        //周新组卷数量
        $lastComWeekNum=$UserModel->sumData( //对上周周新组卷数量进行统计
            'ComTimes',
            'LastTime >= '.$mondayOfLastWeek.' and LastTime <'.$mondayOfWeek
        );
        $this->debugSql();
        if(!$lastLastComWeekNum) $lastLastComWeekNum=0;
        if(!$lastComWeekNum) $lastComWeekNum=0;
        $totalContent[7]['zxzjsl'] = (int)$lastComWeekNum;
        //布置作业数量
        $lastWorkNum=$this->getModel('UserSendWork')->selectCount( //对上周布置作业数量进行统计
            ' SendTime <'.$mondayOfWeek,
            'SendID'
        );
        $this->debugSql();
        $totalContent[8]['bzzysl'] = (int)$lastWorkNum;
        //教师提交学生总数
        //学生刷题量
        $newLastFreshNum=$this->getModel('UserTestRecordAttr')->sumData( //对上上周周新组卷数量进行统计
            'TestAmount',
            ' LoadTime <'.$mondayOfWeek
        );
        $this->debugSql();
        // $newLastFreshNum=0;
        // foreach($lastFreshNum as $i=>$iLastFreshNum){
        //     $newLastFreshNum+=count(explode(',',$iLastFreshNum['Content']));
        // }
        $totalContent[9]['xsstl']=(int)$newLastFreshNum;
        //手机端做题量
        //电脑端做题量
        //高考版使用人数 Version=1
        $lastComAatNum=$UserModel->selectData( //对上周周新组卷数量进行统计
            'count(UserID) as total',
            'Whois=0 and Version=1 and LastTime <'.$mondayOfWeek
        );
        $this->debugSql();

        $totalContent[10]['gkbsyrs']=(int)$lastComAatNum[0]['total'];
        //同步版使用人数 Version=2
        $lastSynNum=$UserModel->selectData( //对上周周新组卷数量进行统计
            'count(UserID) as total',
            'Whois=0 and Version=2 and LastTime <'.$mondayOfWeek
        );
        $this->debugSql();
        $totalContent[11]['tbbsyrs']=(int)$lastSynNum[0]['total'];
        //使用学校数量
        $lastSchoolNum=$this->getModel('UserIp')->selectCount( //对上周布置作业数量进行统计
            ' AddTime <'.$mondayOfWeek,
            'IPID'
        );
        $this->debugSql();
        $totalContent[12]['syxxsl']=(int)$lastSchoolNum;
        $totalContent[13]['StatTime'] = $mondayOfWeek;
        foreach($totalContent as $key=>$value){
            $totalContent[$key]['StatOrder'] = $order;
        }
        $this->addData($totalContent);
        return $this->disposeResult($this->getDataByOrder($order));
    }

    /**
     * 返回指定order的结果
     * @author demo
     * @date 
     */
    protected function getDataByOrder($order){
        return $this->getModel($this->statTableName)->selectData(
            '*', 
            'StatOrder='.$order.' AND StatClassify='.$this->classify, 
            'StatDescription DESC'
        );
    }

    /**
     * 对缓存的数据进行处理
     * @param array $data
     * @return array
     */
    protected function process($data){
        $result = array();
        foreach($data as $value){
            $title = $value[0];
            if('StatTime' == $title){
                continue;
            }
            list($des, $unit) = $this->mapping[$title];
            $result[] = array($des, $value[1], $value[2], $value[3], $unit);
        }
        return $result;
    }


    protected function debug($time){
        echo date('Y-m-d', $time);
    }

    protected function debugSql(){
    }
}