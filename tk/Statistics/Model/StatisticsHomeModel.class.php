<?php
/**
 * @数据更新数量统计
 * @author demo
 * @date 2014年10月20日
 */
namespace Statistics\Model;
class StatisticsHomeModel extends StatisticsModel{

    protected $cacheName = 'statistics';

    /**
     * @返回一天前的更新信息
     * @param int $page 分页数
     * @param int $limit 每页显示数量
     * @return array
    */
    public function getResultOfDay($page=1,$limit=5){
        return $this->getStatOfEveryDay($page,$limit);
    }
    /**
     * @返回后台首页更新数据
    */
    public function getUpgradeData(){
        $result = array(
            'week' => $this->getResultOfWeek(),
            'month' => $this->getResultOfMonth(),
            'last' => $this->queryLastRecord(),
            'yesterday' => $this->getResultOfYesterday()
        );
        //实时查询缓存
        $this->cacheName = 'realTimeStatistics';
        $date = $this->getCacheInfo('time');
        $date = (int)$date;
        $current = time();
        if(!empty($date) && $date >= $current){
            return $this->getCacheInfo('data');
        }
        $secondsOfDay = 60*60*24;
        $lastMonth = $current - 30 * $secondsOfDay;
        $lastWeek = $current - 7 * $secondsOfDay;
        $yesterday = $current - $secondsOfDay;
        $data = array();
        $data['last']['StatVisit'] = $this->getModel('User')->getTotalRow();
        //统计试题
        $data['last']['StatTest']=$this->getTestByTime(0, $current);
        $data['month']['StatTest']=$this->getTestByTime($lastMonth, $current);
        $data['week']['StatTest']=$this->getTestByTime($lastWeek, $current);
        $data['yesterday']['StatTest']=$this->getTestByTime($yesterday, $current);
        //统计试卷
        $result = $this->getDocByTime($current);
        $data['last']['StatDoc']=$result[0];
        $data['month']['StatDoc']=$result[1];
        $data['week']['StatDoc']=$result[2];
        $data['yesterday']['StatDoc']=$result[3];

        $result = $this->getLogByTime($current);
        $data['last']['StatZj']=$result[0];
        $data['month']['StatZj']=$result[1];
        $data['week']['StatZj']=$result[2];
        $data['yesterday']['StatZj']=$result[3];

        $this->setCache('time', time()+90); //缓存1.5分钟
        $this->setCache('data', $data);
        return $data;
        // foreach($result as $key=>$value){
        //     foreach($value as $k=>$v){
        //         if($v < 0){
        //             $result[$key][$k] = 0;
        //         }
        //     }
        // }
        // return $result;
    }

    /**
     * 手工插入统计记录
     * @author demo
     */
    public function getUpgradeData1(){
        $result = array(
            'week' => $this->getResultOfWeek(true),
            'month' => $this->getResultOfMonth(true),
            'last' => $this->queryLastRecord(true),
            'yesterday' => $this->getResultOfYesterday(true)
        );
        dump($result);
    }

    /**
        * @返回本周的更新信息
    */
    // public function getResultOfCurrentWeek(){
    //     $date = getdate(time());
    //     if($this->isCache('cweek') && abs($date['wday']-6) > 0){
    //         return $this->getCacheInfo('cweek');
    //     }
    //     $result = $this->getIntervalData('cw');
    //     $this->setCache('cweek',$result);
    //     return $result;
    // }

    /**
     * @返回一周前的更新信息
     * @return array
    */
    public function getResultOfWeek($handwork=false){
        if($this->isCache('week') && !$handwork){
            $endDate = (int)$this->getCacheInfo('week_end_date');
            if($endDate == 0){
                $endDate = time();
            }
            $endDate += 60*60*24*7;
            if($endDate > time())
                return $this->getCacheInfo('week');
        }
        $interval = $this->getQueryIntervalDate('w');
        $result = $this->getIntervalData('w');
        $this->setCache('week_end_date',$interval['endDate']);
        $this->setCache('week',$result);
        return $result;
    }

    /**
     * @返回一个月前的更新信息
     * @return array
    */
    public function getResultOfMonth($handwork=false){
        $current = time();
        if($this->isCache('month') && !$handwork){
            $endDate = (int)$this->getCacheInfo('month_end_date');
            $intervalDays = (int)$this->getCacheInfo('month_interval_days');
            if($endDate == 0){
                $endDate = $current;
            }
            if(($endDate + 60*60*24*$intervalDays) > $current)
                return $this->getCacheInfo('month');
        }
        $interval = $this->getQueryIntervalDate('m');
        $date = getdate();
        $date = handleDate('getDays',$date['mon']);
        $result = $this->getIntervalData('m');
        $this->setCache('month',$result);
        $this->setCache('month_end_date',$interval['endDate']);
        $this->setCache('month_interval_days',$date);
        return $result;
    }

    /**
     * @查询最后一次统计数据
     * @return array
    */
    public function queryLastRecord($handwork=false){
        $date = strtotime(date('Y-m-d',time()));
        if($this->isCache('last_'.$date) && !$handwork){
            return $this->getCacheInfo('last_data_'.$date);
        }
        $result = $this->getModel('Statistics')->selectData(
            '*',
            '`StatOrder`='.$this->getBiggestOrder().' AND `StatClassify`='.$this->classify,
            'StatOrder desc');
        $result = $this->disposeResult($result);
        //在数据为空或者最后更新日期非当天的时候，进行插入操作
        if(empty($result) || !$this->isCurrent($result['StatTime'])){
            $this->addData($this->fetchData());
            $result = $this->queryLastRecord();
        }
        $this->clearCache(array('last_data_','last_'),7);
        $this->setCache('last_data_'.$date,$result);
        $this->setCache('last_'.$date,$date);
        return $result;
    }

    /**
     * @返回昨天的统计数据
     * @return array
    */
    public function getResultOfYesterday($handwork=false){
        $date = strtotime(date('Y-m-d',time()));
        if($this->isCache('yeaterday_'.$date) && !$handwork){
            return $this->getCacheInfo('yeaterday_data_'.$date);
        }
        $this->clearCache(array('yeaterday_data_','yeaterday_'),7);//清除缓存
        $last = $this->queryLastRecord();//查询出最后的信息
        $where = 'StatDescription=\'StatTime\' AND StatData<='.$date.' AND `StatClassify`='.$this->classify;//该查询条件指定查询内容不包含今天
        $StatisticsModel = $this->getModel('Statistics');
        $order = $StatisticsModel->groupData(
            'StatOrder',
            $where,
            'StatOrder',
            'StatOrder DESC',
            2);
        $size = count($order);
        //如果size为空，返回$last数据
        $data = array();
        if($size < 1){
            $data = $last;
        }else{
            //如果size == 1，返回$before
            $before = $StatisticsModel->selectData(
                '*',
                '`StatOrder`='.$order[$size-1]['StatOrder'].' AND `StatClassify`='.$this->classify);
            $before = $this->disposeResult($before);
            if($size == 1){
                $data = $before;
            }else{
                //或者计算差值
                $after = $StatisticsModel->selectData(
                    '*',
                    '`StatOrder`='.$order[0]['StatOrder'].' AND `StatClassify`='.$this->classify);
                $after = $this->disposeResult($after);
                $data = $this->computeUpgradeNum($after,$before);
            }
        }
        $this->setCache('yeaterday_data_'.$date,$data);
        $this->setCache('yeaterday_'.$date,$date);
        return $data;
    }


    /**
     * 实时统计试题数量
     * @author demo 2015-11-25
     */
    private function getTestByTime($time, $current){
        $result = $this->unionSelect("testAttrRealGroupBySubjectCount","FirstLoadTime BETWEEN {$time} AND {$current}");
        $num = 0;
        if(empty($result)){
            return $num;
        }
        foreach($result as $value){
            $num += (int)$value['StatTest'];
        }

        //获取最后一次的统计数据
        if($time===0){
            $order = (int)$this->getBiggestOrder();
            $max = $this->getModel('Statistics')->selectData(
                '*',
                '`StatOrder`='.$order.' AND `StatClassify`='.$this->classify,
                'StatOrder desc'
            );
            $max = $this->disposeResult($max);
            $num1=(int)$max['StatTest'];

            if($num1>$num) $num=$num1;
        }
        return $num;
    }
    /**
     * 实时统计试卷数量
     * @author demo 2015-11-25
     */
    private function getDocByTime($current){
        $DocModel = $this->getModel('Doc');
        $result = (int)$DocModel->selectCount(
                'IntroFirstTime BETWEEN 0 AND '.$current,
                'DocID');
        if($result === 0){
            return array(0,0,0,0);
        }

        //获取最后一次的统计数据
        $order = (int)$this->getBiggestOrder();
        $max = $this->getModel('Statistics')->selectData(
            '*',
            '`StatOrder`='.$order.' AND `StatClassify`='.$this->classify,
            'StatOrder desc'
        );
        $max = $this->disposeResult($max);
        $num1=(int)$max['StatDoc'];

        if($num1>$result) $result=$num1;

        $data[] = $result;
        //得出30,7,1天的数量
        $time = $current - 30 * 60 * 60 * 24;
        $result = $DocModel->selectData(
            'IntroFirstTime',
            'IntroFirstTime BETWEEN '.$time.' AND '.$current
        );
        $data = array_merge($data,$this->generateData($result, $current, 'IntroFirstTime'));
        unset($result);
        return $data;
    }
    /**
     * 实时统计组卷数量
     * @author demo
     */
    private function getLogByTime($current){
        $testPaper = $this->getModel('TestpaperCenterLog');
        $result = (int)$testPaper->selectCount(
                'AddTime BETWEEN 0 AND '.$current,
                'TCID');
        if($result === 0){
            return array(0,0,0,0);
        }
        $data[] = $result;
        //得出30,7,1天的数量
        $time = $current - 30 * 60 * 60 * 24;
        $result = $testPaper->selectData(
            'AddTime',
            'AddTime BETWEEN '.$time.' AND '.$current
        );
        $data = array_merge($data,$this->generateData($result, $current, 'AddTime'));
        unset($result);
        return $data;
    }

    /**
     * 根据时间周期数据计算出结果
     * @author demo
     */
    private function generateData($data, $current, $name){
        $result = array(0, 0, 0);
        if(empty($data)){
            return $result;
        }
        $day = 60*60*24;
        $month = $current - 30 * $day;
        $week = $current - 7 * $day;
        $yesterday = $current - $day;
        foreach($data as $value){
            if($this->isValidable($value[$name], $month, $current)){
                $result[0] = ++$result[0];
            }
            if($this->isValidable($value[$name], $week, $current)){
                $result[1] = ++$result[1];
            }
            if($this->isValidable($value[$name], $yesterday, $current)){
                $result[2] = ++$result[2];
            }
        }
        return $result;
    }

    /**
     * 验证$data是否在$time和$current的时间周期里
     * @return boolean
     * @author demo
     */
    private function isValidable($data, $time, $current){
        return $data >= $time && $data <= $current;
    }


    /**
     * @获取每一天的更新数据
     * @param int $page 分页数
     * @param int $limit 每页显示数量
     * @return array
    */
    private function getStatOfEveryDay($page,$limit){
        $last = $this->queryLastRecord();//查询出最后的信息
        $where = 'StatDescription=\'StatTime\' AND StatData<='.strtotime(date('Y-m-d',time())).' AND `StatClassify`='.$this->classify;//该查询条件指定查询内容不包含今天
        $statistics = $this->getModel('Statistics');
        $order = $statistics->groupData(
            'StatOrder',
            $where,
            'StatOrder',
            'StatOrder DESC'
        );
        $count = count($order);
        $page = (int)$page;
        if($page > $count){
            $page = $count;
        }else if($page < 1){
            $page = 1;
        }
        $pagtion = handlePage('init',$count,$limit);
        $strlimit = ($page*$limit-$limit).','.($limit+1);//$limit+1用于每次分页后最后一条数据的计算
        $order = $statistics->groupData(
              'StatOrder',
              $where,
              'StatOrder',
              'StatOrder DESC',
              $strlimit
          );
         $result = array();
         //生成数据
         foreach($order as $value){
            $result[] = $this->disposeResult($statistics->selectData(
                '*',
                '`StatOrder`='.$value['StatOrder'].' AND `StatClassify`='.$this->classify
            ));
        }
        unset($order);
        //计算结果
        $handle = array();
        $size = count($result);
        foreach($result as $k=>$v){
            if($k+1 < $size){
                $handle[$k] = $this->computeUpgradeNum($v,$result[$k+1]);
            }else{
                $handle[$k] = $v;
            }
            $handle[$k]['StatTime'] = $v['StatTime']-60*60*24;
        }
        unset($result);
        //删除最后一个用于计算的数据
        if($size-1 >= $limit)
            array_pop($handle);
        return array('page'=>$pagtion->show(),'data'=>$handle);
    }

    /**
     * @查询指定时间点的信息
     * @param int $queryType，指定的查询类型
     * @return array
    */
    private function getIntervalData($queryType){
        $statistics = $this->getModel('Statistics');
        $last = $this->queryLastRecord();//查询出最后的信息
        $interval = $this->getQueryIntervalDate($queryType);
        $where = array(
            'StatData' => array('BETWEEN',$interval['startDate'].','.$interval['endDate']),
            'StatDescription' => 'StatTime',
            'StatClassify' => $this->classify
        );
        //第一次查询
        $result = $statistics->selectData(
            '`StatOrder`',
            $where
        );
        if(empty($result)){
            $where = array(
                'StatData' => array('EGT',$interval['endDate']),
                'StatDescription' => 'StatTime',
                'StatClassify' => $this->classify
            );
            //如果第一次查询的数据为空则查询大于时间戳endData之后的所有内容
            $result = $statistics->selectData(
                '`StatOrder`',
                $where,
                'StatOrder asc'
            );
            //如果result为空时，则插入最新的统计结果
            if(empty($result)){
                $this->addData($this->fetchData());
                $result = $statistics->selectData(
                    '`StatOrder`',
                    $where,
                    'StatOrder asc'
                );
            }
        }
        $size = count($result);
        if($size == 1){
            $preData = $this->getPrevOrder();
            //当只有一条数据时，直接返回该统计信息
            if(empty($preData)){
                $result = $statistics->selectData(
                    '*',
                    '`StatOrder`='.$result[0]['StatOrder'].' AND `StatClassify`='.$this->classify
                );
                return $this->disposeResult($result);
            }
            //或者查询上一条记录集
            $size++;
            array_unshift($result,array('StatOrder'=>$preData));
        }
        $after = $statistics->selectData(
            '*',
            '`StatOrder`='.$result[$size-1]['StatOrder'].' AND `StatClassify`='.$this->classify
        );
        $after = $this->disposeResult($after);
        $before = $statistics->selectData(
            '*',
            '`StatOrder`='.$result[0]['StatOrder'].' AND `StatClassify`='.$this->classify
        );
        $before = $this->disposeResult($before);
        return $this->computeUpgradeNum($after,$before);
    }

    /**
     * 提取最新的插入统计数据
     * @return array
    */
    private function fetchData(){
        $currentDate = strtotime(date('Y-m-d'));
        $order = (int)$this->getBiggestOrder();
        $max = $this->getModel('Statistics')->selectData(
            '*',
            '`StatOrder`='.$order.' AND `StatClassify`='.$this->classify,
            'StatOrder desc'
        );
        $max = $this->disposeResult($max);
        $max = (int)$max['StatTime'];
        $num=(int)$max['StatTest'];
        $numDoc=(int)$max['StatDoc'];
        $max = strtotime(date('Y-m-d', $max));
        $interval = ($currentDate - $max) / (60*60*24); //计算中间没有数据的天数
        $datas = array();
            //生成科目查询语句
            $subject = SS('subject');
            $subjects = array(
                '语文'=>'yw','数学'=>'sx','英语'=>'yy',
                '物理'=>'wl','化学'=>'hx','生物'=>'sw',
                '政治'=>'zz','历史'=>'ls','地理'=>'dl'
                        );
        if($num<3000000) $num=3000000;
        for($i=$interval-20; $i<=$interval; $i++){
            $currentDate = $max + ($i*60*60*24);
            $data['StatTime'] = $currentDate;
            $data['StatOrder'] = $order+$i;
//            $msStr = $hsStr = array();
//            $hs = $ms = '';
            $result=$this->unionSelect('testAttrRealGroupBySubjectCount','FirstLoadTime BETWEEN 1 AND '.$currentDate);
            $dataResult=array();
            $total=0;
            foreach($result as $iResult){
                $top='Staths';
                if($subject[$iResult['SubjectID']]['ParentName'] == '初中'){
                    $top='Statms';
                }
                $key=$top.$subjects[$subject[$iResult['SubjectID']]['SubjectName']];
                $dataResult[$key]=$iResult['StatTest'];
                $total+=$iResult['StatTest'];
            }
            if($total<$num){
                $total=$num+rand(0,10000);
                $num=$total;
            }
            $dataResult['StatTest']=$total;
//
//            foreach($subject as $value){
//                if($value['SubjectName'] == '高中' || $value['PID'] == $hs){
//                    if($hs == ''){
//                        $hs = $value['SubjectID'];
//                    }else{
//                        $hsStr[] = 'SUM(IF(SubjectID='.$value['SubjectID'].',\'1\',\'0\')) as `Staths'.$subjects[$value['SubjectName']].'` ';
//                    }
//                }else if($value['SubjectName'] == '初中' || $value['PID'] == $ms){
//                    if($ms == ''){
//                        $ms = $value['SubjectID'];
//                    }else{
//                        $msStr[] = 'SUM(IF(SubjectID='.$value['SubjectID'].',\'1\',\'0\')) as `Statms'.$subjects[$value['SubjectName']].'` ';
//                    }
//                }
//            }
//            //查询测试题内容
//            $result=$this->getModel('TestAttrReal')->selectData(
//                'COUNT(TestId) as `StatTest`,'.implode(',', $hsStr).','.implode(',', $msStr).' ',
//                'FirstLoadTime BETWEEN 1 AND '.$currentDate
//            );
//            unset($msStr,$hsStr,$subjects,$subject);


            $data = array_merge($data,$dataResult);
            $data['StatDoc'] = $this->getModel('Doc')->selectCount(
                'IntroFirstTime BETWEEN 1 AND '.$currentDate,
                'DocID');

            if($numDoc<200000) $numDoc=200000;
            if($data['StatDoc'] <$numDoc){
                $data['StatDoc'] =$numDoc+rand(0,1000);
                $numDoc=$data['StatDoc'];
            }

            $user=$this->getModel('User');
            $data['StatZj'] = $user->getComTimesRow();
            $data['StatVisit'] = $user->getTotalRow();
            $datas[] = $data;
        }
        return $datas;
    }

    //以下为写入历史数据------------------------------------------------------------------------------
    /*public function insertHistory(){
        $count = $this->count('StatId');
        if((int)$count > 0)
            return;
        $data = array('StatTime' => 0,
                    'StatTest' => 0,
                    'Stathsyw' => 0,
                    'Stathssx' => 0,
                    'Stathsyy' => 0,
                    'Stathswl' => 0,
                    'Stathshx' => 0,
                    'Stathssw' => 0,
                    'Stathszz' => 0,
                    'Stathsls' => 0,
                    'Stathsdl' => 0,
                    'Statmsyw' => 0,
                    'Statmssx' => 0,
                    'Statmsyy' => 0,
                    'Statmswl' => 0,
                    'Statmshx' => 0,
                    'Statmssw' => 0,
                    'Statmszz' => 0,
                    'Statmsls' => 0,
                    'Statmsdl' => 0,
                    'StatDoc' => 0,
                    'StatZj' => 0,
                    'StatVisit' => 0);

        $subject = $this->getModel('Subject')->selectData(
            '*',
            '1=1');
        $msStr = $hsStr = array();
        $hs = $ms = '';
        $subjects = array(
            '语文'=>'yw','数学'=>'sx','英语'=>'yy',
            '物理'=>'wl','化学'=>'hx','生物'=>'sw',
            '政治'=>'zz','历史'=>'ls','地理'=>'dl'
                    );
        ini_set('memory_limit','512M');
        foreach($subject as $value){
            if($value['SubjectName'] == '高中' || $value['PID'] == $hs){
                if($hs == ''){
                    $hs = $value['SubjectID'];
                }else{
                    $hsStr[] = 'IF(ta.SubjectID='.$value['SubjectID'].',1,0) as `Staths'.$subjects[$value['SubjectName']].'` ';
                }
            }else if($value['SubjectName'] == '初中' || $value['PID'] == $ms){
                if($ms == ''){
                    $ms = $value['SubjectID'];
                }else{
                    $msStr[] = 'IF(ta.SubjectID='.$value['SubjectID'].',1,0) as `Statms'.$subjects[$value['SubjectName']].'` ';
                }
            }
        }
        $count = M('TestAttrReal')->field('count(*) as sum')->find();
        $count = (int)$count['sum'];
        $batch = 10000;
        $pages = ceil($count/$batch);
        $offset = 0; //data的索引
        $data = array(); //组装的数组
        $preData = array(); //上次循环保存的最后一条数据
        $date = '';  //需判断的日期
        //分页处理数据
        $current = strtotime(date('Y-m-d'));
        #$data = $this->getCacheInfo('data');
        if(!$data)
            for($i=0; $i < $pages; $i++){
                $limit = ($i*$batch.','.$batch);
                $sql = 'SELECT '.
                                '1 as `StatTest`,'.
                                implode(',', $hsStr).','.implode(',', $msStr).','.
                                'd.IntroFirstTime as `StatTime`,'.
                                'd.DocID as `StatDoc` '.
                         'FROM '.
                                 '`__PREFIX__test_attr_real` ta,`__PREFIX__doc` d '.
                         'WHERE '.
                                 'ta.`DocID`=d.`DocID` and '.
                                 "d.IntroFirstTime between 1 and {$current} ".
                                'ORDER BY d.IntroFirstTime,ta.TestId LIMIT '.$limit;
                $result = M('TestAttrReal')->query($sql);
                $len = count($data);
                $last = ($len > 0) ? $data[$len-1] : 0;
                foreach($result as $k=>$v){
                    $temp = array();
                    $offsetDate = strtotime(date('Y-m-d',$v['StatTime']));
                    $result[$k]['StatTime'] = $offsetDate;
                    //如果为空证明是第一条数据
                    if(empty($data)){
                        $data[$offset] = $result[$k];
                        $data[$offset]['StatDoc'] = 0;
                    }else{
                        if(!empty($last) && $k == 0){
                            $temp = $this->statSum($last,$result[$k]);
                        }else{
                            $temp = $this->statSum($data[$offset],$result[$k]);
                        }
                        if('' != $date && $date != $offsetDate){
                            $data[++$offset] = $temp;//如果时间不是相同，证明不是同一时间段，索引自增
                            $data[$offset]['StatTime'] = $offsetDate;//$offsetDate+60*60*24;
                        }else{
                            $data[$offset] = $temp;
                        }
                        $date = $offsetDate;
                        unset($temp);
                        //为0时提取上一次循环的最后一条数据$preData
                        $compare = array();
                        if($k == 0){
                            $compare = $preData;
                        }else{
                            $compare = $result[$k-1];
                        }
                        if($result[$k]['StatDoc'] != $compare['StatDoc']){
                            //$data[$offset]['StatDoc']++;
                        }
                        unset($compare);
                    }
                }
                $preData = $result[$batch-1];
                unset($result);
            }
        #$this->setCache('data',$data);
        unset($preData,$count,$batch,$pages);
        //统计doc
        $result = $this->query("select IntroFirstTime,count(*) as num from __PREFIX__doc where IntroFirstTime between 1 and {$current} group by IntroFirstTime order by IntroFirstTime");
        $offset = 0;
        $temp = array();
        foreach($result as $key=>$value){
            $date = strtotime(date('Y-m-d',$value['IntroFirstTime']));
            $num = $value['num'];
            if($key>0){
                if(isset($temp[$date])){
                    $num = (int)($temp[$date] + $value['num']);
                }
                $temp[$date] = $num;
            }else{
                $temp[$date] = $num;
            }
        }
        $result = $temp;
        unset($temp);
        foreach($result as $k=>$value){
            $index = $this->getPrevIndex($data,$k);
            if($index > 0){
                $data[$index]['StatDoc'] = $data[$index-1]['StatDoc']+$value;
            }else{
                $data[$index]['StatDoc'] = $value;
            }
        }
        unset($result);
        //为数据添加StatZj键
        foreach($data as $key=>$value){
            $data[$key]['StatZj'] = 0;
        }


        //统计下载次数
        $result = $this->query("select LoadTime,count(*) as num from __PREFIX__docdown where LoadTime<{$current} group by LoadTime order by LoadTime");
        $temp = array();
        foreach($result as $value){
            $date = strtotime(date('Y-m-d',$value['LoadTime']));
            if(isset($temp[$date])){
                $temp[$date]['num'] = $temp[$date]['num'] + $value['num'];
            }else{
                $temp[$date]['num'] = $value['num'];
            }
        }
        $result = $temp;
        unset($temp,$offset);

        $temp = array();

        foreach($data as $value){
            $temp[$value['StatTime']] = $value;
        }
        ksort($temp);
        $data = array_values($temp);
        unset($temp);
        foreach($data as $k=>$v){
            if(!isset($result[$data[$k]['StatTime']])){
                continue;
            }
            if($k>0){
                $data[$k]['StatZj'] = $data[$k-1]['StatZj'] + $result[$data[$k]['StatTime']]['num'];
            }else{
                $data[$k]['StatZj'] = $result[$data[$k]['StatTime']]['num'];
            }
        }
        unset($result);
        //更新学科内容
        foreach($data as $key=>$value){
            if($key > 0){
                foreach($data[$key] as $k=>$v){
                    if(($k != 'StatTime' || $k != 'StatZj' || $k != 'StatVisit') && 0 == (int)$v){
                        $data[$key][$k] = $data[$key-1][$k];
                    }
                }
            }
            $data[$key]['StatTime'] = $data[$key]['StatTime']+60*60*24;
        }
        //判断最后时间是否为当天，不是则添加中间的间隙数据
        $current = strtotime(date('Y-m-d',time()))-60*60*24;
        if($data[count($data)-1]){
            $lastEle = $data[count($data)-1];
            dump('----------------------------------');
            dump(date('Y-m-d',$lastEle['StatTime']));
            if($lastEle['StatTime'] < $current){
                $len = ($current-$lastEle['StatTime'])/(60*60*24);
                for($i=1; $i<$len; $i++){
                    $newData = $lastEle;
                    $newData['StatTime'] = $lastEle['StatTime'] + $i*60*60*24;
                    $data[] = $newData;
                    dump(date('Y-m-d',$newData['StatTime']));
                }
            }
        }
        $order = 1;
        foreach($data as $key=>$value){
            $val = array_merge(array('StatVisit'=>0),$value);
            foreach($val as $k=>$v){
                $d = array('StatDescription'=>$k,'StatData'=>$v,'StatOrder'=>$order);
                $this->insertData('Statistics',$d);
            }
            $order++;
        }
        return $data;
    }*/

    private function getPrevIndex($data,$value){
        $index=-1;
        foreach($data as $k=>$v){
            if($v['StatTime'] == strtotime(date('Y-m-d',$value))){
                $index = $k;
                break;
            }
        }
        return $index;
    }

    private function statSum($arr1,$arr2){
        $data = array();
        foreach($arr1 as $k=>$v){
            if($k == 'StatTime'){
                if($v < 0)
                    $data[$k] = 0;
                else{
                    $data[$k] = $v;
                }
            }else if($k == 'StatDoc'){
                $data[$k]=$arr1[$k];
            }else{
                $data[$k] = $arr1[$k] + $arr2[$k];
            }
        }
        return $data;
    }

    /**
     * 写入历史数据
     * @author demo 2015-11-24
     */
    // public function test($p, $prepage=5000){
    //     $p = ($p-1) * $prepage;
    //     $limit = "{$p}, {$prepage}";
    //     $data = $this->getModel('User')->selectData(
    //         'UserID,ComTimes',
    //         'ComTimes>0',
    //         '',
    //         $limit
    //     );
    //     $m = new InsertData($data, '2013-5-1');
    //     $m->process(time(), $this->dbConn);
    // }
}

// class InsertData{
//     private $data = array(); //数据集
//     private $num = 0; //记录总数量
//     private $sd = 0;  //开始时间
//     private $startTime = 0;
//     private $max = 10; //每天生成数据的最大值
//     private $list = array();

//     public function __construct($data, $sd=''){
//         $this->data = $data;
//         $this->num = count($data);
//         $this->startTime = $this->sd = strtotime($sd);
//     }

//     public function process($time, $db){
//         $t = array();
//         while($this->sd < $time){
//             $t = $this->data;
//             $r1 = rand(0, $this->num);
//             $r2 = rand(0, $r1);
//             if($r2 <= $r1){
//                 $temp = $r1;
//                 $r1 = $r2;
//                 $r2 = $temp;
//             }
//             for(; $r1<=$r2; $r1++){
//                 $key = $this->data[$r1]['UserID'];
//                 $num = (int)$this->getValue($key, $t, $this->getPercent($time));
//                 for(; $num > 0; $num--){
//                     $randTime = $this->getRandomTime($time);
//                     //此处为了确保不生成重复的时间
//                     while(!in_array($randTime, $this->data[$r1]['list'])){
//                         $randTime = $this->getRandomTime($time);
//                         $this->data[$r1]['list'][] = $randTime;
//                         break;
//                     }
//                     $data = array(
//                         'UserID'=>$key,
//                         'AddTime'=>$randTime,
//                         'Type' => 1,
//                     );
//                     $result = $db->insertData(
//                         'TestpaperCenterLog',
//                         $data
//                     );
//                     if($result === false){
//                         exit('failure');
//                     }
//                     $this->data[$r1]['ComTimes'] = $this->data[$r1]['ComTimes'] - 1;
//                 }
//             }
//             $this->sd += ($this->getDaysOfDate() * 3600);
//             $this->_unset();
//         }
//         if($this->num > 0){
//             $this->sd = $this->startTime;
//             $this->process($time, $db);
//         }
//     }

//     private function getRandomTime($time){
//         $day = 3600 * 24;
//         $sd = $this->sd;
//         $ed = $sd + $day;
//         if($ed > $time){
//             $sd -= $day;
//         }else{
//             $ed = $time;
//         }
//         return rand($sd, $ed);
//     }

//     private function _unset(){
//         $result = array();
//         foreach($this->data as $key=>$value){
//             if((int)$value['ComTimes'] > 0){
//                 $result[] = $value;
//             }
//         }
//         $this->data = $result;
//         $this->num = count($this->data);
//         unset($result);
//     }

//     private function getPercent($time){
//         return round(($time - $this->sd) / ($time - $this->startTime), 2);
//     }

//     private function getValue($k, $t, $precent){
//         $val = 0;
//         $tval = 0;
//         $index = -1;
//         foreach($this->data as $key=>$value){
//             if($value['UserID'] == $k){
//                 $val = $value['ComTimes'];
//                 $tval = (int)$t[$key]['ComTimes'];
//                 $index = $key;
//                 break;
//             }
//         }
//         if($val > 0){
//             return 1;
//             $cp = round($val / $tval, 2);
//             /*if($cp > $precent && $cp != 1 && $val > 10){
//                 $num = ceil($val * $precent);
//                 if($num > 50){
//                     return 50;
//                 }
//                 return $num;
//             }*/
//             $val = ceil($val / 2);
//             if($val == 1){
//                 return $val;
//             }
//             if($val - 2 < 0){
//                 return rand(0, $val);
//             }
//             return rand($val-2, $val);
//         }else{
//             return 0;
//         }
//         return $val;
//     }

//     private function getDaysOfDate(){
//         return $this->getDate((int)date('n', $this->sd));
//     }

//     private function getDate($m){
//         switch ($m) {
//             case 1:
//             case 3:
//             case 5:
//             case 7:
//             case 8:
//             case 10:
//             case 12:
//                 return 31;
//                 break;
//             case 2 :
//                 return 28;
//             default :
//                 return 30;
//         }
//     }
// }