<?php
/**
 * 统计model基类
 * @author demo
 * @date 2015-5-6
 */
namespace Statistics\Model;
use Common\Model\BaseModel;
class StatisticsModel extends BaseModel{

    protected $cache = null;

    protected $cacheName = '';

    protected $statTableName = 'Statistics';

    protected $classify = 0; //统计数据分类

    public function __construct(){
        parent::__construct();
        $this->cache = \Think\Cache::getInstance('file');
        $cache = $this->cache->get($this->cacheName);
        if(empty($cache)){
            $this->cache->set($this->cacheName,array());
        }
    }

    /**
     * @返回缓存数据
     * @param string $key 缓存键
     * @return mixed
    */
    protected function getCacheInfo($key){
        $cache = $this->cache->get($this->cacheName);
        return $cache[$key];
    }

    /**
     * @缓存是否存在
     * @param string $key 缓存键
     * @return boolean 存在返回true
    */
    protected function isCache($key){
        $cache = $this->cache->get($this->cacheName);
        return !empty($cache[$key]);
    }

    /**
     * @设置缓存
     * @param string $key 缓存的键
     * @param string $value 缓存值
     * @return void
    */
    protected function setCache($key,$value){
        $cache = $this->cache->get($this->cacheName);
        $cache[$key] = $value;
        $this->cache->set($this->cacheName,$cache);
    }

    /**
     * @清除指定日期的缓存文件
     * @param string|array $key 指定的换成前缀。为数组的格式是：array(xxx,xxxx);如果删除指定键的缓存，设置$limitDate为0
     * @param int $limitDate 需删除的缓存天数。为当前时间-limitDate
     * @return void
    */
    protected function clearCache($key,$limitDate=60){
        $cache = $this->cache->get($this->cacheName);
        if($limitDate == 0){
            unset($cache[$key]);
        }else{
            $prev = strtotime(date('Y-m-d',time()))-60*60*24*$limitDate;

            for($i=0; $i<$limitDate; $i++){
                if(is_string($key)){
                    $name = $key.$prev;
                    if(isset($cache[$name])){
                        unset($cache[$name]);
                    }
                }else{
                    foreach ($key as $value) {
                        $name = $value.$prev;
                        if(isset($cache[$name])){
                            unset($cache[$name]);
                        }
                    }
                }
                $prev += 60*60*24;
            }
        }
        $this->cache->set($this->cacheName,$cache);
    }

    /**
     * 清除缓存文件
     * @return void
     */
    public function clearAllCache(){
        $this->cache->set($this->cacheName,array());
    }

    /**
     * @检查最后更新是否为当天
     * @param int $date 比对的时间戳
     * @return boolean 如果是当天的时间，返回true
    */
    protected function isCurrent($date){
        $time = date('Y-m-d',time());
        return $time == date('Y-m-d',$date);
    }

    /**
     * @返回需查询的起始和截止时间戳
     * @param string $limit 值为d/天，w/周，m/月，或者为一个合法的日期格式字符串
     * @return array 返回起始时间和截止时间的数组
    */
    protected function getQueryIntervalDate($limit='d'){
        $result = array();
        switch(strtolower($limit)){
            case 'w' : {
                $current = strtotime(date('Y-m-d',time()));
                $date = getdate($current);
                $day = $date['wday']+7;
                $day = $current-60*60*24*$day;
                $result['startDate'] = $day;
                $result['endDate'] = $day + 60*60*24*7+1;
                #dump(date('Y-m-d',$result['startDate']).','.date('Y-m-d',$result['endDate']));
                #dump($result['startDate'].','.$result['endDate']);
            }
            break;
            case 'm' : {
                $month = strtotime(date('Y-m-1'),time());
                $date = getdate($month);
                $date = handleDate('getDays',$date['mon']);
                $result['startDate'] = $month - 60*60*24*$date;
                $result['endDate'] = $month - 60*60*24+1;
            }
            break;
            case 'cw' : {
                $date = getdate(time());
                $day = strtotime(date('Y-m-d',time()));
                $result['startDate'] = $day - (5-$date['wdy'])*60*60*24;
                $result['endDate'] = $day;
            }
        }
        return $result;
    }

    /**
     * @插入最近更新信息
     * @author demo
    */
    protected function addData($data){
        foreach($data as $key=>$value){
            $order = $value['StatOrder'];
            unset($data[$key]['StatOrder']);
            foreach($data[$key] as $k=>$v){
                $d = array('StatDescription'=>$k,'StatData'=>$v,'StatOrder'=>$order, 'StatClassify'=>$this->classify);
                $this->getModel($this->statTableName)->insertData(
                    $d
                );
            }
        }
    }

    /**
     * @返回最后生成的StatOrder
    */
    protected function getBiggestOrder(){
        $max = $this->getModel($this->statTableName)->selectData(
            'MAX(`StatOrder`) as max',
            'StatClassify='.$this->classify
        );
        if($max && $max[0]['max'])
            return $max[0]['max'];
        return 0;
    }

    /**
     * @查询最大StatOrder的上一个StatOrder值
    */
    protected function getPrevOrder(){
        $max = $this->getBiggestOrder();
        $prev = $this->getModel($this->statTableName)->selectData(
            '`StatOrder`',
            'StatOrder<'.$max.' AND StatClassify='.$this->classify,
            'StatOrder desc'
        );
        return $prev[0]['StatOrder'];
    }

    /**
     * @返回处理的结果集
     * @param array 给定的数据查询结果
     * @return array
    */
    protected function disposeResult($result){
        if(empty($result)){
            return array();
        }
        $data = array();
        foreach($result as $k=>$v){
            $data[$v['StatDescription']] = $v['StatData'];
        }
        unset($result);
        return $data;
    }

    /**
     * @计算更新的数量
     * @param array $after 减数
     * @param array $before 被减数
     * @return array
    */
    protected function computeUpgradeNum($after,$before){
        foreach($after as $k=>$v){
            if(!isset($before[$k])){
                $before[$k] = 0;
            }
            if('StatTime' != $k){
                $after[$k] = $v-(int)$before[$k];
            }
        }
        return $after;
    }

    /**
     * 计算中间间隔多少个周期
     * @param int $time 上一个时间点
     * @param int $cycle 周期计算以此为基准
     * @author demo
     * @return int
     */
    protected function getIntervalCountOfCycle($time, $cycle=7){
        $time = (int)$time;
        $date = strtotime(date('Y-m-d'));
        return floor(($date - $time) / ($this->day * $cycle));
    }
    /**
     * 系统统计页面的信息
     * @param string $flagStr 统计参数标记
     * @return array
     * @author demo
     */
    public function totalSystem($flagStr){
        $totalMsg=array();
        $cacheStat=S($flagStr);
        if($cacheStat) return $cacheStat;
        if($flagStr){
            $flagArr=explode(',',$flagStr);
            $sc = $this->getModel('StatisticsCounter');
            $data = $sc->getCounter($flagArr);
            foreach($flagArr as $i=>$iFlagArr){
                switch($iFlagArr){
                    case 'testNum':
                        $totalMsg['testNum']=(int)$data['testNum'];
                        break;
                    case 'selfTestNum':
                        $totalMsg['selfTestNum']=(int)$data['selfTestNum'];
                        break;
                    case 'zujuanNum':
                        $totalMsg['zujuanNum']=(int)$data['zujuanNum'];
                        break;
                    case 'shijuanNum':
                        $totalMsg['shijuanNum']=(int)$data['shijuanNum'];
                        break;
                    case 'classNum':
                        $totalMsg['classNum']=(int)$data['classNum'];
                        break;
                    case 'homeWorkNum':
                        $totalMsg['homeWorkNum']=(int)$data['homeWorkNum'];
                        break;
                    case 'caseDownNum':
                        $totalMsg['caseDownNum']=(int)$data['caseDownNum'];
                        break;
                    case 'caseHomeWorkNum':
                        $totalMsg['caseHomeWorkNum']=(int)$data['caseHomeWorkNum'];
                        break;
                    case 'studentAnswerNum':
                        $totalMsg['studentAnswerNum']=(int)$data['studentAnswerNum'];
                        break;
                    case 'appNum':
                        $totalMsg['appNum']=(int)$this->getAppNum();
                        break;
                    case 'schoolNum':
                        $totalMsg['schoolNum']=(int)$data['schoolNum'];
                        break;
                    case 'teacherNum':
                        $totalMsg['teacherNum']=(int)$data['teacherNum'];
                        break;
                    case 'studentNum':
                        $totalMsg['studentNum']=(int)$data['studentNum'];
                        break;
                    case 'userMoneyList':
                        $totalMsg['userMoneyList']=$this->getUserLucreList()['LucreList'];
                        break;
                    case 'moneyTotal':
                        $totalMsg['moneyTotal']=round($data['moneyTotal'],0);
                        break;
                    case 'customTest':
                        $totalMsg['customTest']=(int)$this->getCustomTestList();
                        break;
                }
            }
        }
        S($flagStr,$totalMsg,60);//每分钟更新一次数据
        return $totalMsg;

    }
    /**
     * 校本题库试题分值统计排行
     * @author demo
     */
    public function getCustomTestList(){
        $beginDate=date('Y-m-01', strtotime(date("Y-m-d")));
        $startTime=strtotime($beginDate); //开始时间戳
        //需要稳定时间差
        $lastTime=strtotime(date('Y-m-d', strtotime("$beginDate +1 month -1 day")))+24*3600-1; //结束时间戳
        $totalList=$this->unionSelect('payTotalList',10,$startTime,$lastTime);
        $LastCustomTestList=array();
        foreach($totalList as $i=>$iTotalList){
            if(count($LastCustomTestList)<5){
                if(!empty($totalList[$i]['UserName'])){
                    $LastCustomTestList[$i]['PointTotal']=$totalList[$i]['PointTotal'];
                    $LastCustomTestList[$i]['testTotal']=$totalList[$i]['testTotal'];
                    $LastCustomTestList[$i]['UserName']=R('Common/UserLayer/showUserName',array($totalList[$i]['UserName'],$totalList[$i]['RealName'],$totalList[$i]['Whois']));
                }
            }
        }
        return $LastCustomTestList;
    }

    /**
     * 用户收益排行榜
     * @autor
     */
    public function getUserLucreList(){
        $totalList=$this->unionSelect('payTotalAll',10);
        foreach($totalList as $i=>$iTotalList){
            if(!empty($totalList[$i]['UserID'])){
                $userArr[]=$totalList[$i]['UserID'];
            }
        }
        $userIDStr=implode(',',$userArr);
        $userNameMsg = array();
        if($userIDStr){
        $userNameMsg=$this->getModel('User')->selectData(
            'UserID,UserName,Whois,RealName',
            'UserID in ('.$userIDStr.')'
        );
        }

        foreach($userNameMsg as $i=>$iUserName){
            $nameMsg[$iUserName['UserID']]=$iUserName;
        }
        // $result['shareTotal']=round($total[0]['total'], 0); //四舍五入
        $userMsg=array();
        foreach($totalList as $i=>$iTotalList){
            if(count($userMsg)<5){
                if(!empty($nameMsg[$totalList[$i]['UserID']]['UserName'])){
                    $userMsg[$i]['total']=$totalList[$i]['personTotal'];
                    $userMsg[$i]['UserName']=R('Common/UserLayer/showUserName',array($nameMsg[$totalList[$i]['UserID']]['UserName'],$nameMsg[$totalList[$i]['UserID']]['RealName'],$nameMsg[$totalList[$i]['UserID']]['Whois']));
                }
            }
        }
        $result['LucreList']=$userMsg;
        return $result;
    }


    /**
     * 获取APP用户数量
     */
    private function getAppNum(){
        $appMsg=R('Common/AppPlatformLayer/getStatistic',[$platform=['android']]);
        if($appMsg){
            $appDownNum=$appMsg['android']['installations']+11195;//设备安装次数 友盟平台数据加上ApiCloud平台数据
        }else{
            $appDownNum=11195;         //没有获取到的话，就默认7394次；
        }
        return $appDownNum;
    }
}