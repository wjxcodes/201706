<?php
/**
 * @date 2014年12月10日
 * @author demo 
 */
/**
 * 项目反应理论算法类
 * 使用方法：
 * $irtModel = new \Exercise\Model\IrtModel();
 * $irtModel->testData = [....];
 * $irtModel->getAbilityValue();
 */
namespace Aat\Model;

use Common\Model\BaseModel;
class IrtModel extends BaseModel
{
    protected $autoCheckFields = false; //不自动检测数据表字段信息
    public $a = 1;//区分度
    public $d = 1.7;//量表因子
    /**
     * 数据格式，根据ifChoose不同，格式不同
     * 【注意】暂时没有处理复合题
     * ifChoose取值 0主观题,1复合体,2多选,3单选
     * ifRight取值 -1未做,0无法判断,1错误,2正确
     * diff取值 decimal(4,3)
     * [
     * ['ifChoose'=>0,'diff'=>0.231,'score'=>2.5,'fullScore'=>5],
     * ['ifChoose'=>2或者3, 'diff'=>0.231,'ifRight'=>2],
     * ]
     * 【重要】这里根据isChoose设置不同字段主要目的是兼容之前提分的数据格式
     */
    public $testData = [];


    /**
     * 根据试题信息获得用户能力值
     * @return float 能力值[-3,3]
     * @author demo
     */
    public function getAbilityValue() {
        //1. 格式化数据为所需数据
        $irtData = $this->toIrtFormat();

        if($irtData == false){
            return null;
        }
        //2. 计算
        //$t1 = microtime(true);
        $ability = $this->_MLE($irtData['diff'],$irtData['ifRight'],$irtData['c']);//核心
        //$t2 = microtime(true);echo '耗时'.round($t2-$t1,5).'秒<br />';
        //3. 返回
        return $ability;
    }

    /**
     * 格式化为Irt计算所需要格式
     * @return array 难度系数、猜测系数、ifRight转化
     * @author demo
     */
    private function toIrtFormat(){
        $testData = $this->testData;
        $diff = [];//试题属性表中试题难度值
        $ifRight = [];//试题作答正确情况 取值：0错误 1正确
        $c = [];//试题猜测系数 取值：单选0.25 多选0.09091 不考虑0即非选择题
        $normalModel = $this->getModel('Normal');
        foreach ($testData as $i => $iTestData) {
            if ($iTestData['IfChoose'] > 1&&$iTestData['IfRight']!=-1) {
                //如果是选择题且是作答了的题目则计算能力值
                $diff[$i] = $normalModel->pro2Val($iTestData['Diff']);//转换难度系数为Z值
                $ifRight[$i] = $iTestData['IfRight'] == 2 ? 1 : 0;//if_right 取值：1正确 0错误未作答
                $c[$i] = $iTestData['IfChoose'] == 3 ? 0.25000 : 0.09091;
            }
        }
        if(!(count($diff)==count($ifRight)&&count($ifRight)==count($c)&&count($diff)>0)){
            return false;
        }
        return [
            'diff'=>$diff,
            'ifRight'=>$ifRight,
            'c'=>$c,
        ];
    }

    /**
     * 极大似然法计算能力值
     * @param $z array 试题难度参数
     * @param $ifRight array 答题正确情况 1正确 0错误
     * @param $c array 试题猜测系数
     * @return float 小数点后2位 能力值 [-3,3]
     * @author demo
     */
    private function _MLE($z, $ifRight, $c) {
        //$b 难度参数取值[-3,3]
        //$a 项目区分度默认1
        $a = $this->a;
        //$D 量表因子1.7
        $D = $this->d;
        //$i能力值区间 [-3,3]
        //先精确到0.01
        $arr = [];
        $arr2 = [];
        $arr3 = [];
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
     * 项目反映理论曲线
     * @author demo
     */
    public function item($c,$z) {
        //$b 难度参数取值[-3,3]
        //$a 项目区分度默认1
        $a = $this->a;
        //$D 量表因子1.7
        $D = $this->d;
        //$i能力值区间 [-3,3]
        $arr = [];
        for ($i = -2.5; $i <= 2.5; $i += 0.1) {
            $L = 1;
            $L = $L * ($c + (1 - $c) / (1 + exp(-$D * $a * ($i - $z))));
            $arr[]= [$i,$L];
        }
        return $arr;
    }
}