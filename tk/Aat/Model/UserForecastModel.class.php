<?php
/**
 * @author demo
 * @date 2014年4月14日
 */
/**
 * 用户预测分model类
 */
namespace Aat\Model;

use Common\Model\BaseModel;
class UserForecastModel extends BaseModel
{
    protected $normal_array = array();

    protected $a = 1;//$a 项目区分度默认1
    protected $d = 1.7;//$D 量表因子1.7
    protected $conditionN = 4;//间隔N轮有效测试（又能判断对错的作答）后的下一次可以生成能力值和预测分（计算还剩几次测试可以生成新预测分也需要此变量） 默认4
    protected $conditionS = 10;//知识点下大于s道测试题时该知识点可以生成能力值 默认10


    /**
     * 判断是否可以产生预测分
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @return bool 是否可以生成能力值
     * @author demo
     */
    public function ifAbility($userName, $subjectID) {
        //从上次产生预测分到本次测试是否够N次测试
        $testRecordTag = $this->getLastAbility($userName, $subjectID)['TestRecordID'];
        $testRecordTag = $testRecordTag?$testRecordTag:0;
        $recordCount = $this->selectCount(
            [
                'UserName' => $userName,
                'SubjectID' => $subjectID,
                'TestRecordID' => ['gt', $testRecordTag]
            ],
            'ForecastID'
        );
        return $recordCount >= $this->conditionN ? true : false;
    }

    /**
     * 判断是否生成知识点对应的能力值
     * @param $allAmount int 知识点下的作答题目总量
     * @return bool
     */
    public function ifKlAbility($allAmount) {
        return $allAmount >= $this->$conditionS ? true : false;
    }

    /**
     * 根据试题信息以及作答信息获得用户能力值
     * @param array $diff 试题属性表中试题难度值取值：[0,1]小数
     * @param array $ifRight 试题作答正确情况 取值：0错误 1正确
     * @param array $c 试题猜测系数 取值：单选0.25 多选0.09091 不考虑0即答题填空题，因为系统无法判断此类题正确与否
     * @return float 能力值[-3,3]
     * @author demo
     */
    public function getAbilityValue($diff, $ifRight, $c) {
        if (count($diff) != count($ifRight) || count($diff) != count($c) || count($ifRight) != count($c) || $diff == null) {
            //如果没有有效答题数据（数据为NULL），则返回NULL
            return null;
        }
        //转换难度系数为Z值
        foreach ($diff as $i => $k) {
            $diffZ[$i] = $this->_z($k);
        }
//        $t1 = microtime(true);
        $ability = $this->_mle($diffZ, $ifRight, $c);
//        $t2 = microtime(true);
//        echo '耗时'.round($t2-$t1,5).'秒<br />';
        return $ability;
    }

    /**
     * 根据学科和能力值计算标准卷得分即预测分
     * @todo 目前除了数学，其它学科没有标准卷，所以，没有标准卷的使用该学科的总分和能力值计算（不对的方式）
     * @param int $subjectId 学科
     * @param float $ability 能力值取值[-3,3]
     * @return int 预测分
     */
    public function getForecastScore($subjectId, $ability) {
        if($ability === null){
            //能力值如果是null，则预测分不能生成，返回-1
            return -1;
        }
        $a = $this->a;
        $D = $this->d;
        $standard = $this->getModel('Standard')->getStandard($subjectId);
        $forecastScore = 0;//预测分
        if($standard){
            //正确的计算方式
            $init_diff = $standard['diff'];
            $init_score = $standard['score'];
            $init_guess = $standard['guess'];
            foreach ($init_diff as $i => $diff) {
                $forecastScore += ($init_guess[$i] + (1 - $init_guess[$i]) / (1 + exp(-$D * $a * ($ability - $this->_z($diff))))) * $init_score[$i];
            }
        }else{
            //该学科没有标准卷，使用错误的方式 @todo 所有学科标准卷完善后，删除
//            $subjectCache = SS('subject');
            $subjectCache = $this->getApiCommon('Subject/subject');
            $subjectScore = $subjectCache[$subjectId]['TotalScore'];
            $forecastScore = round($subjectScore*(($ability+3)/6));
            if($forecastScore<16) $forecastScore += 16;
        }
        return $forecastScore;
    }

    /**
     * 获取指定用户testRecordID后的记录ID
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @param int $testRecordID testRecordID
     * @return string 逗号分隔的sendIDs
     * @author demo
     */
    public function getTestRecordIDs($userName,$subjectID,$testRecordID){
        $db = $this->selectData(
            'TestRecordID',
            ['UserName'=>$userName,'SubjectID'=>$subjectID,'testRecordID'=>['gt',$testRecordID]]
        );
        $testRecordIDs = '';
        if($db){
            foreach($db as $iDb){
                $array[] = $iDb['TestRecordID'];
            }
            $testRecordIDs = implode(',',$array);
        }
        return $testRecordIDs;
    }

    /**
     * 获取用户最后一次（最新的）能力值的记录
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @return float 能力值，小数点后3位
     * @author demo
     */
    public function getLastAbility($userName,$subjectID){
        $db = $this->selectData(
            'TestRecordID,ForecastAbility',
            ['UserName' => $userName, 'SubjectID' => $subjectID, 'ForecastScore' => ['neq', -1]],
            'LoadTime DESC'
            );
        $result = $db[0]?$db[0]:null;
        return $result;
    }

    /**
     * 根据UserName SubjectID查找最近上一次的记录
     * 目的是得到各种Amount数量【PersonalReport使用，累加各种Amount】
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @return array|null
     * @author demo
     */
    public function getLastRecord($userName, $subjectID) {
        $db = $this->selectData(
            'UserName,TestRecordID,SubjectID,RightAmount,WrongAmount,UndoAmount,NotAmount,AllAmount',
            ['UserName' => $userName, 'SubjectID' => $subjectID],
            'LoadTime DESC'
            );
        return $db[0]?$db[0]:null;
    }


    /**
     * 计算用户某学科的测试分数的排名信息
     * @param $userName
     * @param $forecastAbility
     * @param $subjectID
     * @return mixed
     * @author demo
     */
    public function getExerciseRanking($userName,$forecastAbility,$subjectID) {
        if($forecastAbility===null){
            //不生成能力值
            $rankingDb = $this->findData(
                'ExerciseRanking',
                [
                    'UserName'=>$userName,
                    'SubjectID'=>$subjectID,
                    'IsLast'=>1
                ],
                'LoadTime DESC'
            );
            if($rankingDb){
                //有当前排名
                $ranking = $rankingDb['ExerciseRanking']-1;//后面统一加1
            }else{
                //没有当前排名
                $ranking = $this->selectCount(
                    ['IsLast'=>1,'ForecastAbility'=>['gt',-3],'SubjectID'=>$subjectID],
                    'ForecastID'
                );
            }
        }else{
            $ranking = $this->selectCount(
                ['IsLast'=>1,'ForecastAbility'=>['gt',$forecastAbility],'SubjectID'=>$subjectID],
                'ForecastID'
            );
        }
        return $ranking ? ($ranking + 1) : 1;
    }

    /**
     * 获取指定测试记录用户的排名（击败考生百分比），用于用户查看测试报告
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @param int $testRecordID 测试记录ID
     * @return array  ['percent'=>击败考生百分比的分子,'rank'=>排名,'all'=>该学科下的测试用户数量]
     * @author demo
     */
    public function getRankingPercent($userName,$subjectID,$testRecordID){
        $rank = $this->selectData(
                'LoadTime,ExerciseRanking',
                array('UserName'=>$userName,
                      'SubjectID'=>$subjectID,
                      'TestRecordID'=>$testRecordID),
                'LoadTime DESC',
                '1' );
        //获取用户总数【注意：这个人数是发生排名的时候的人数，之后再新加的用户测试，不算进来，保证百分比在什么时候什么情况下都一致】
        $all=$this->groupData(
            'UserName',
            array('SubjectID'=>$subjectID,'ForecastScore'=>array('gt','0'),'LoadTime'=>array('lt',$rank[0]['LoadTime'])),
            'UserName');
        $all = count($all);
        $percent = 100-round($rank[0]['ExerciseRanking']/$all,2)*100;
        return array('percent'=>$percent,'rank'=>$rank[0]['ExerciseRanking'],'all'=>$all);
    }
    /**
     * 获取用户某学科下的做题总量
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @return int 试题总数量
     */
    public function getAllAmount($userName,$subjectID){
        $forecastDb = $this->selectData(
            'ForecastID,AllAmount,LoadTime',
            array('UserName' => $userName,'SubjectID'=>$subjectID),
            'ForecastID desc',
            '1');
        $allAmount = $forecastDb?$forecastDb[0]['AllAmount']:0;
        return $allAmount;
    }

    /**
     * 某学科生成预测分需要的测试次数
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @return int 需要的次数
     * @author demo
     */
    public function forecastNeedTimes($userName,$subjectID){
        $forecastData = $this->selectData(
            'ForecastAbility',
            ['UserName'=>$userName,'SubjectID'=>$subjectID],
            'ForecastID DESC',
            $this->conditionN+1
            );
        $all = $this->conditionN+1;//总次数（生成预测分间隔数）因为间隔$all次，不是第$all次生成，所以加1
        if(!$forecastData){//数据为空
            return $all;
        }
        if(count($forecastData)<$all){
            //如果用户的总测试次数少于需要的次数
            $needTimes = $all - count($forecastData);
            return $needTimes;
        }
        //必须===因为可能为0
        $n = -1;//最近一次生成预测分在数组中的位置
        foreach ($forecastData as $i => $iForecastData) {
            if ($iForecastData['ForecastAbility'] !== null) {
                $n = $i;
                break;
            }
        }
        if ($n == -1) {
            //如果查询最近的$all次都没有生成预测分，说明用户答题可能都是空，只需要下次答题不为空就可以
            $needTimes = 1;
        } else {
            $needTimes = $all - $n;//注意，数组是ForecastID的倒序
        }
        return $needTimes;
    }


    /**
     * 获取指定用户学科的测试信息
     * @param string $userName 用户名
     * @param int $subject_id 学科ID
     * @return array 返回【快速预测分 精准预测分 答题数量 排名信息 精准趋势 快速趋势 】信息
     * @author demo
     */
    public function getScoreInfo($userName, $subject_id, $isMobile=false) {
        $forecast = $this->selectData(
            'ForecastID,ForecastAbility,ForecastScore,LoadTime',
            array(
                'ForecastScore' => array('neq', '-1'),
                'UserName' => $userName,
                'SubjectID' => $subject_id,
            ),
            'ForecastID desc',
            10
            );
        $dateFormate = $isMobile?'n/j':'m月d日 H:i';
        if($forecast){
            foreach($forecast as $i=>$k){
                $forecast[$i]['LoadTime'] = date($dateFormate,$k['LoadTime']);
            }
        }
        $exercise = $this->selectData(
                'ForecastID,ExerciseScore,ExerciseRanking,AllAmount,RightAmount,WrongAmount,UndoAmount,LoadTime',
                array('UserName' => $userName,'SubjectID'=>$subject_id),
                'ForecastID desc',
                '10'
            );

        if($exercise){
            foreach($exercise as $i=>$k){
                $exercise[$i]['LoadTime'] = date($dateFormate,$k['LoadTime']);
            }
        }

//        //@@@68698189@qq.com
//        if ($userName == '68698189@qq.com') {
//            //修改预测分
//            if ($forecast) {
//                foreach ($forecast as $i => $k) {
//                    $forecast[$i]['ForecastScore'] = ($k['ForecastScore'] + 110) > 150 ? 145 : ($k['ForecastScore'] + 110);
//                }
//            }
//        }
        $result = array(
            'UserName'=>$userName,
            'ExerciseScore'=>$exercise?floatval($exercise[0]['ExerciseScore']):null,
            'ExerciseLine'=>$exercise,
            'ForecastScore'=>$forecast?$forecast[0]['ForecastScore']:null,
            'ForecastLine'=>$forecast,
            'AllAmount'=>$exercise?$exercise[0]['AllAmount']:null,
            'RightAmount'=>$exercise?$exercise[0]['RightAmount']:null,
            'WrongAmount'=>$exercise?$exercise[0]['WrongAmount']:null,
            'UndoAmount'=>$exercise?$exercise[0]['UndoAmount']:null,
        );
        return $result;
    }

    /**
     * 极大似然法计算能力值
     * @param $z array 试题难度参数
     * @param $ifRight array 答题正确情况 1正确 0错误
     * @param $c array 试题猜测系数
     * @return float 小数点后2位 能力值 [-3,3]
     */
    protected function _mle($z, $ifRight, $c) {
        //$b 难度参数取值[-3,3]
        //$a 项目区分度默认1
        $a = $this->a;
        //$D 量表因子1.7
        $D = $this->d;
        //$i能力值区间 [-3,3]
        //先精确到0.01
        for ($i = -3; $i <= 3; $i += 0.1) {
            $L = 1;
            foreach ($z as $n => $b) {
                if ($ifRight[$n]) {
                    $L = $L * ($c[$n] + (1 - $c[$n]) / (1 + exp(-$D * $a * ($i - $b))));
                } else {
                    $L = $L * (1 - ($c[$n] + (1 - $c[$n]) / (1 + exp(-$D * $a * ($i - $b)))));
                }
            }
            $arr[] = $L;
        }
        $i1 = array_search(max($arr), $arr);
        $result1 = $i1 * 0.1 + (-3);
        //精确到0.01
        for ($i2 = ($result1 - 0.1); $i2 <= ($result1 + 0.1); $i2 += 0.01) {
            $L2 = 1;
            foreach ($z as $n => $b) {
                if ($ifRight[$n]) {
                    $L2 = $L2 * ($c[$n] + (1 - $c[$n]) / (1 + exp(-$D * $a * ($i2 - $b))));
                } else {
                    $L2 = $L2 * (1 - ($c[$n] + (1 - $c[$n]) / (1 + exp(-$D * $a * ($i2 - $b)))));
                }
            }
            $arr2[] = $L2;
        }
        $i2 = array_search(max($arr2), $arr2);
        $result2 = $i2 * 0.01 + ($result1 - 0.1);
        //一下代码对上一步的结果result1继续精确到0.001
        for ($i3 = ($result1 - 0.01); $i3 <= ($result1 + 0.01); $i3 += 0.001) {
            $L3 = 1;
            foreach ($z as $n => $b) {
                if ($ifRight[$n]) {
                    $L3 = $L3 * ($c[$n] + (1 - $c[$n]) / (1 + exp(-$D * $a * ($i3 - $b))));
                } else {
                    $L3 = $L3 * (1 - ($c[$n] + (1 - $c[$n]) / (1 + exp(-$D * $a * ($i3 - $b)))));
                }
            }
            $arr3[] = $L3;
        }
        $i3 = array_search(max($arr3), $arr3);
        $result3 = $i3 * 0.001 + ($result2 - 0.01); //极大似然参数值
        if ($result3 > 3) {
            $result3 = 3.000;
        }
        if ($result3 < -3) {
            $result3 = -3.000;
        }
        return $result3;
    }

    /**
     * 计算Z值
     * @param $p float 需要转化的值 小数点后3位 0-1
     * @return float 对应Z值
     */
    protected function _z($p) {
        //载入表
        if (!$this->normal_array) {
            $this->normal_array = $this->getModel('Normal')->selectData(
                        'val,pro',
                        '1=1',
                        'pro desc');
        }
        //查表得p值
        if ($p > 0.5) {
            $p = 1 - $p;
            return -$this->_getNum($p);
        } else
            return $this->_getNum($p);
    }

    /**
     * 数组中折半查询相等值
     * @param float $num 需要转化的值 小数点后3位 0-0.5
     * @return float 对应Z值
     */
    private function _getNum($num) {
        $high = count($this->normal_array) - 1;
        $found = 0;
        $low = 0;
        while ($found == 0 && $low <= $high) {
            $mid = floor(($high + $low) / 2);
            settype($num, 'string');
            settype($this->normal_array[$mid]['pro'], 'string');
            if ($num < $this->normal_array[$mid]['pro'])
                $low = $mid + 1;
            elseif ($num > $this->normal_array[$mid]['pro'])
                $high = $mid - 1;
            else {
                $found = 1;
                $key = $mid;
            }
        }
        if (is_numeric($key)) {
            return $this->normal_array[$key]['val'];
        } else {
            return null;
        }
    }

}