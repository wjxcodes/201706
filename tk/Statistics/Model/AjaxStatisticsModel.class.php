<?php
/**
 * ajax分段统计  818e0022dcb2f27c5c15955cbedf6bb7
 * @author demo
 * @date 2015-5-16
 */
namespace Statistics\Model;
class AjaxStatisticsModel extends StatisticsModel{

    protected $cacheName = 'systemAjaxStatistics';

    protected $day = 0;

    protected $classify = 1; //统计数据分类

    protected $intevalDate = 0;

    protected $mapping = array(
        'zcyhsl' => array('注册用户数量', '人'),
        'zhyrh' =>  array('周活跃用户', '人'),
        'xtstsl' =>  array('系统试题总量', '题'),
        'xttjsl' =>  array('系统套卷总量', '套'),
        'xtzjcs' =>  array('系统组卷次数', '次'),
        'zsjgxl' =>  array('周试卷更新量', '套'),
        'zstgxl' =>  array('周试题更新量', '题'),
        'zxzjsl' =>  array('周新组卷数量', '套'),
        'xsstl' =>  array('学生刷题量', '题'),
        'gkbsyrs' =>  array('高考版使用人数' ,'人'),
        'syxxsl' =>  array('使用学校数量' ,'所'),
        'bzzysl' =>  array('布置作业数量' ,'道'),
        'tbbsyrs' =>  array('同步版使用人数' ,'人')
    ); 

    public function __construct(){
        parent::__construct();
        $this->intevalDate = $this->getQueryIntervalDate('w');
        $this->day = 60 * 60 * 24;
    }

    public function getResult($param){
        if(!$this->isExpire($param)){
            $order = (int)$this->getBiggestOrder();
            if(!$order){
                $order = 1;
            }
            $currentData = $this->disposeResult($this->getDataByOrder($order));
            $expireDate = $this->getCacheInfo('expireDate');
            if($currentData){
                $expireDate = strtotime(date('Y-m-d', $currentData['StatTime'])) + $this->day*7;
            }
            if(!$expireDate){
                $expireDate = 0;
            }
            $newData = array();
            //当前时间大于失效期
            if(time() >= $expireDate){
                //如果currentData的长度（加上StatTime）等于mapping的长度（加1）
                //此操作用于区分当前周期的数据是否添加完成
                $currentDataSize = count($currentData);
                $mappingSize = count($this->mapping);
                if($currentDataSize == ($mappingSize+1)){
                    $order++;
                }
                $newData = $this->disposeResult($this->getDataByOrder($order, $param));
                if(!$newData){
                    $mondayOfLastWeek= $this->intevalDate['startDate'] + $this->day; //获取上上周日时间戳
                    $mondayOfWeek= $this->intevalDate['endDate'] + $this->day; //获取上周日时间戳
                    //当order为1时，当前表中暂无数据，本次操作将插入两个周期的数据
                    if(1 == $order){
                        foreach($this->mapping as $key=>$value){
                            $this->fetchData($mondayOfLastWeek-$this->day*7, $mondayOfLastWeek, $order, $key);
                        }
                        $this->addStatTime($mondayOfLastWeek, $order++); //插入第一组数据后同时插入统计时间
                    }
                    $newData = $this->fetchData($mondayOfLastWeek, $mondayOfWeek, $order, $param);
                    //如果currentDataSize加上当前新插入($newData)的一条数据等于$this->mapping的长度，则同时插入统计时间
                    if($currentDataSize+1 == $mappingSize){
                        $this->addStatTime($mondayOfWeek, $order);
                        $this->setCache('expireDate', $this->intevalDate['endDate'] + $this->day * 8);
                    }
                }
            }else{
                $newData = $this->disposeResult($this->getDataByOrder($order, $param));
            }
            //查询出上一个order的数据
            $preData = $this->disposeResult($this->getDataByOrder(--$order, $param));
            $data = $this->combindData($newData, $preData, $param);
            $this->setCache($param, $data);
        }
        return $this->getCacheInfo($param);
    }

    /**
     * 返回当前的数据关系
     * @return array
     */
    public function getMapping(){
        return $this->mapping;
    }

    /**
     * 从数据库中提取相关数据
     * @param int $mondayOfLastWeek 上周一时间戳 00:00
     * @param int $mondayOfWeek 本周一时间戳 00:00
     * @param int $order 插入序号
     * @param string $des 插入当前行的数据描述
     * @return array
     */
    protected function fetchData($mondayOfLastWeek, $mondayOfWeek, $order, $des){
        //注册用户数量
        $num = 0;
        if('zcyhsl' == $des){
            $lastUserNum=$this->getModel('User')->selectCount( //对上周用户数量进行统计
                ' LoadDate <'.$mondayOfWeek,
                'UserID'
            );
            $num = (int)$lastUserNum; 
        }else if('zhyrh' == $des){ //周活跃用户
            $lastHotUserNum=$this->getModel('User')->selectCount( //对上周活跃用户数量进行统计
                ' LastTime >='.$mondayOfLastWeek.' and LastTime <'.$mondayOfWeek,
                'UserID'
            );
            $num = (int)$lastHotUserNum;
        }else if('xtstsl' == $des){ //系统试题总量
            $lastTestNum=$this->getModel('TestAttrReal')->selectCount( //对上周系统试题总量进行统计
                ' FirstLoadTime <'.$mondayOfWeek,
                'TestID'
            );
            $num = (int)$lastTestNum;
        }else if('xttjsl' == $des){ //系统套卷总量
            $lastDocNum=$this->getModel('Doc')->selectCount( //对上周系统套卷总量进行统计
                'IntroFirstTime <'.$mondayOfWeek,
                'DocID'
            );
            $num = (int)$lastDocNum;
        }else if('xtzjcs' == $des){ //系统组卷次数
            $lastComNum=$this->getModel('User')->sumData( //对上周系统组卷次数进行统计
                'ComTimes',
                'LastTime <'.$mondayOfWeek
            );
            $num = (int)$lastComNum;
        }else if('zsjgxl' == $des){  //周试卷更新量
            $lastDocWeekNum=$this->getModel('Doc')->selectCount( //对上周周试卷更新量进行统计
                'IntroFirstTime >= '.$mondayOfLastWeek.' and IntroFirstTime <'.$mondayOfWeek,
                'DocID'
            );
            $num = (int)$lastDocWeekNum;
        }else if('zstgxl' == $des){ //周试题更新量
            $lastTestWeekNum=$this->getModel('TestAttrReal')->selectCount( //对上周周试题更新量进行统计
                'FirstLoadTime >='.$mondayOfLastWeek.' and FirstLoadTime <'.$mondayOfWeek,
                'TestID'
            );
            $num = (int)$lastTestWeekNum;
        }else if('zxzjsl' == $des){ //周新组卷数量
            $lastComWeekNum=$this->getModel('User')->sumData( //对上周周新组卷数量进行统计
                'ComTimes',
                'LastTime >= '.$mondayOfLastWeek.' and LastTime <'.$mondayOfWeek
            );
            $num = (int)$lastComWeekNum;
        }else if('bzzysl' == $des){  //布置作业数量
            $lastWorkNum=$this->getModel('UserSendWork')->selectCount( //对上周布置作业数量进行统计
                ' SendTime <'.$mondayOfWeek,
                'SendID'
            );
            $num = (int)$lastWorkNum;
        }else if('xsstl' == $des){ //教师提交学生总数 学生刷题量
            $newLastFreshNum=$this->getModel('UserTestRecordAttr')->sumData( //对上上周周新组卷数量进行统计
                'TestAmount',
                ' LoadTime <'.$mondayOfWeek
            );
            $num = (int)$newLastFreshNum;
        }else if('gkbsyrs' == $des){   //手机端做题量 电脑端做题量 高考版使用人数 Version=1
            $lastComAatNum=$this->getModel('User')->selectData( //对上周周新组卷数量进行统计
                'count(UserID) as total',
                'Whois=0 and Version=1 and LastTime <'.$mondayOfWeek
            );
            $num = (int)$lastComAatNum[0]['total'];
        }else if('tbbsyrs' == $des){ //同步版使用人数 Version=2
            $lastSynNum=$this->getModel('User')->selectData( //对上周周新组卷数量进行统计
                'count(UserID) as total',
                'Whois=0 and Version=2 and LastTime <'.$mondayOfWeek
            );
            $num = (int)$lastSynNum[0]['total'];
        }else if('syxxsl' == $des){  //使用学校数量
            $lastSchoolNum=$this->getModel('UserIp')->selectCount( //对上周布置作业数量进行统计
                ' AddTime <'.$mondayOfWeek,
                'IPID'
            );
            $num = (int)$lastSchoolNum;
        }
        $totalContent[0][$des]=(int)$num;
        foreach($totalContent as $key=>$value){
            $totalContent[$key]['StatOrder'] = $order;
        }
        $this->addData($totalContent);
        return $this->disposeResult($this->getDataByOrder($order, $des));
    }

    /**
     * 返回指定order的结果
     * @int $order 序号
     * @string $des 统计数据描述
     * @author demo
     * @date 
     */
    protected function getDataByOrder($order, $des=''){
        $where = "StatOrder={$order} AND StatClassify={$this->classify}";
        if($des){
            $where .= " AND StatDescription='{$des}'";
        }
        return $this->getModel($this->statTableName)->selectData(
            '*', 
            $where, 
            'StatDescription DESC'
        );
    }

    /**
     * 验证当前的缓存是否有效
     * @param string $param 每次请求的内容
     * @return boolean 失效返回false
     */
    protected function isExpire($param){
        $cache = $this->getCacheInfo($param);
        if(!$cache){
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
     * 整合数据到缓存
     * @param array $current 新数据
     * @param array $last 上一条数据
     * @return array
     */
    protected function combindData($current, $last, $param){
        $increment = $this->computeUpgradeNum($current, $last); //计算增量
        $data = array();
        foreach($increment as $key=>$value){
            $data[] = array(
                $this->mapping[$param][0], 
                (int)$last[$key], 
                (int)$current[$key], 
                (int)$increment[$key],
                $this->mapping[$param][1]
            );
        }
        return $data;
    }

    /**
     * 插入统计时间
     */
    private function addStatTime($time, $order){
        $this->addData(
            array(
                array(
                    'StatTime' => $time,
                    'StatOrder' => $order
                )
            )
        );
    }
}