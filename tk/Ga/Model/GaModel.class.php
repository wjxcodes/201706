<?php
/**
 * @author demo
 * @date 2014年8月4日
 * @update 2015年3月28日
 */

/**
 * 管理员模型类，用于处理管理员相关操作
 */
namespace Ga\Model;
use Common\Model\BaseModel;
class GaModel extends BaseModel
{
    protected $autoCheckFields = false; //不自动检测数据表字段信息
    protected $initSubjectID; //学科
    protected $initIfKl = 0; //是否是知识点 默认知识点
    protected $initKlCover = 0; //知识点个数
    protected $initKlPer = 1; //知识点覆盖率
    protected $initKl = array(); //知识点数据集
    protected $initDocType = array(); //文档类型
    protected $initDiff = 0; //难度值
    protected $initGradeID = 0; //年级

    protected $initPopu = array(); //初始种群
    protected $initFit = array(); //初始种群适应度
    protected $initPopuDiff = array(); //初始种群难度
    protected $initTestSum = array(); //初始种群试题总分值
    protected $initWheel = array(); //轮盘
    protected $bestPaper = array(); //最优解
    protected $bestTestSum = array(); //最优解总分值
    protected $calcPaper = array(); //算子
    protected $initTest = array(); //初始数据集
    protected $flag = 0; //跳出循环标志

    protected $initTypeList = array(); //每个题型试题题型
    protected $initValList = array(); //每个题型试题分值
    protected $initTypesScore = array(); //每个题型试题计分方式
    protected $initTypesTotal = array(); //每个题型试题总数 for choose
    protected $initSelectType = array(); //每个题型试题选题方式
    protected $initTypesSmallNum = array(); //每个题型小题数量
    protected $initChooseCheck = array(); //每个题型试题是否选做
    protected $initChooseNum = array(); //每个题型试题选做个数


    /**
     * 遗传算法函数
     * @param int $num 遗传代数
     * @param array $param 配置参数数组
     *           $this->setXk($param['SubjectID']);    //设置学科
     *           $this->setDocType($param['DocType']);    //设置文档类型
     *           $this->setKlType($param['KlType']);    //设置是否是知识点或者章节
     *           $this->setKlCover($param['KlCover']);    //设置知识点或章节 id
     *           $this->setDiff($param['Diff']);    //设置难度值
     *           $this->setKlPer($param['KlPer']);    //设置知识点覆盖率
     *           $this->setGrade($param['Grade']);    //设置年级
     *           $this->setChoose($param['Choose']);    //设置选做情况
     *           $this->setTypes($param['Types']);    //设置试题类型
     * @return array 返回最佳试题数组
     * @author demo
     * */
    public function main($num,$param)
    {
        //设置属性
        if($param['SubjectID']) $this->setSubjectID($param['SubjectID']);    //设置学科
        if($param['DocType']) $this->setDocType($param['DocType']);    //设置文档类型
        if($param['KlType']) $this->setKlType($param['KlType']);    //设置是否是知识点或者章节
        if($param['KlCover']) $this->setKlCover($param['KlCover']);    //设置知识点或章节 id
        if($param['Diff']) $this->setDiff($param['Diff']);    //设置难度值
        if($param['KlPer']) $this->setKlPer($param['KlPer']);    //设置知识点覆盖率
        if($param['Grade']) $this->setGrade($param['Grade']);    //设置年级
        if($param['Choose']) $this->setChoose($param['Choose']);    //设置选做情况
        if($param['Types']) $this->setTypes($param['Types']);    //设置试题类型

        $gen = 0; //自加代数变量
        $out = $this->initPopu(); //初始化种群
        if (!$out)
            return '';
        $this->initFit(); //种群适应度
        while ($gen < $num && $this->flag == 0) {
            $new_arr = array(); //新的种群
            $child   = 0;
            $xh      = floor(count($this->initPopu) / 2); //最大子代循环次数
            $this->productWheel(); //生成轮盘表
            while ($child < $xh && $this->flag == 0) {
                $this->chooseArithmeticOperators(); //选择算子
                $this->cross(); //交叉
                $this->mutation(); //变异
                $new_arr = $this->fitPaper($new_arr); //计算算子适应度
                $child++;
            }
            $this->initFit      = $new_arr[0]; //初始种群变更为新种群
            $this->initPopu     = $new_arr[1]; //初始种群适应度变更为新种群适应度
            $this->initPopuDiff = $new_arr[2]; //初始种群难度变更为新种群难度
            $this->initTestSum  = $new_arr[3]; //初始种群总分变更为新种群总分
            $gen++;
        }
        return $this->bestPaper();
    }

    /**
     * 设置学科
     * @param int $id 学科id
     * @return null
     * @author demo
     */
    protected function setSubjectID($id)
    {
        $this->initSubjectID = $id;
    }

    /**
     * 设置文档类型
     * @param string $docType 文档类型
     * @return null
     * @author demo
     */
    protected function setDocType($docType)
    {
        $this->initDocType = explode(',', $docType);
    }

    /**
     * 设置是否是知识点  必须先设置此属性才能设置 setKlCover属性
     * @param int $id 学科id
     * @return null
     * @author demo
     */
    protected function setKlType($id)
    {
        $this->initIfKl = $id;
    }

    /**
     * 设置知识点或章节 并找到对应知识点或章节的所有分类
     * @param String $str 知识点或章节字符串 id,id,id
     * @return null
     * @author demo
     */
    protected function setKlCover($str)
    {
        $output = '';

        //查找大类知识点下的所有知识点
        $arr = explode(',', $str);
        if ($this->initIfKl == 0) {
            $list = $this->getApiCommon('Knowledge/klList'); //获取知识点缓存数据
        }
        else {
            $list = $this->getApiCommon('Chapter/chapterIDList'); //获取章节缓存数据
        }
        foreach ($arr as $iArr) {
            $output .= $list[$iArr].','.$iArr.',';
        }
        $output            = explode(',', $output);
        $this->initKl      = array_unique(array_filter($output));
        $this->initKlCover = count($this->initKl);
    }


    /**
     * 设置知识点覆盖率
     * @param String $kl 知识点字符串 id,id,id
     * @return null
     * @author demo
     */
    protected function setKlPer($kl)
    {
        $this->initKlPer = $kl;
    }

    /**
     * 设置年级
     * @param String $gradeID 年级字符串 id,id,id
     * @return null
     * @author demo
     */
    protected function setGrade($gradeID)
    {
        $this->initGradeID = explode(',', $gradeID);
    }

    /**
     * 设置选做情况
     * @param array $arr 选做情况 $arr[0] 选做情况字符串0,0,1  $arr[1] 选做数量字符串1,1,1
     * @return null
     * @author demo
     */
    protected function setChoose($arr)
    {
        $this->initChooseCheck = explode(',', $arr[0]); //每个样本试题是否选做
        $this->initChooseNum   = explode(',', $arr[1]); //每个样本试题选做个数
    }

    /**
     * 设置难度值
     * @param float $diff 难度值
     * @return null
     * @author demo
     */
    protected function setDiff($diff)
    {
        $this->initDiff = $diff;
    }

    /**
     * 设置试题类型
     * @param array $types 题型属性数组
     *                  array（
     *                      0=>"id,id,id", //每个题型试题题型
     *                      1=>"id,id,id", //每个题型试题总数 for choose
     *                      2=>"id,id,id", //每个题型试题分值
     *                      3=>"id,id,id", //每个题型试题计分方式
     *                      4=>"id,id,id", //每个题型试题选题方式
     *                      5=>"id,id,id"  //每个题型小题数量
     *                  ）
     * @return null
     * @author demo
     */
    protected function setTypes($types)
    {
        $this->initTypeList      = explode(',', $types[0]); //每个题型试题题型
        $this->initTypesTotal    = explode(',', $types[1]); //每个题型试题总数 for choose
        $this->initValList       = explode(',', $types[2]); //每个题型试题分值
        $this->initTypesScore    = explode(',', $types[3]); //每个题型试题计分方式
        $this->initSelectType    = explode(',', $types[4]); //每个题型试题选题方式
        $this->initTypesSmallNum = explode(',', $types[5]); //每个题型小题数量
    }

    /**
     * 遗传算法函数 产生初始种群函数
     * @return int
     * @author demo
     */
    protected function initPopu()
    {
        //根据试题类型数量进行数据的随机提取 组合不同试卷
        $typesIDArr       = $this->initTypeList; //题型id
        $typesNumArr      = $this->initTypesTotal; //数量
        $typesSmallNumArr = $this->initTypesSmallNum; //小题数量
        $chooseIfArr      = $this->initChooseCheck; //每个样本试题是否选做

        //防重复数组
        $uniqueArr=array();

        $klList = $this->getApiCommon('Knowledge/knowledge');//获取知识点缓存数据

        //读取符合条件的数据集    根据知识点和试题类型
        //获取全部题型数据
        foreach ($typesIDArr as $i => $iTypes) {

            //重置方法
            $field=array('testid','diff','klid','testnum');
            $where=array();
            $where['ShowWhere'] = array(0,1); //仅显示通用和组卷端试题

            //文档类型id
            if ($this->initDocType) $where['DocTypeID'] =  $this->initDocType;

            //判断检索知识点还是章节
            if ($this->initIfKl == 0){
                $where['KlID'] =  $this->initKl;

                //如果该题型是选做题型则试题在选做知识点下选题
                if($chooseIfArr[$i]){
                    //获取已经选择知识点中的选做知识点
                    $chooseKl=array();
                    foreach($this->initKl as $iInitKl){
                        if($klList[$iInitKl]['IfInChoose']==1){
                            $chooseKl[]=$iInitKl;
                        }
                    }
                    //如果没有选做知识点则仍然使用全部知识点
                    if(!empty($chooseKl)){
                        $where['KlID'] =  $chooseKl;
                    }
                }
            }else{
                $where['ChapterID'] =  $this->initKl;
            }

            $where['Duplicate']= 0; //不重复的试题

            //年级id
            if ($this->initGradeID)
                $where['GradeID']= $this->initGradeID; //年级id

            //如果是选做题不允许带小题
            if ($chooseIfArr[$i])
                $where['TestNum']= 0;
            else if ($typesSmallNumArr[$i] > 1)
                $where['TestNum']= $typesSmallNumArr[$i];

            $where['TypesID']= $iTypes; //试题题型id

            $order='@random';
            $page=array('perpage'=>$typesNumArr[$i] * 200,'limit'=>$typesNumArr[$i] * 200); //选择试题的基数为需求的200倍

            $res=$this->getApiTest('Test/getTestIndex',$field,$where,$order,$page); //获取全部题型数据

            $this->initTest[$i] = $res[0];
            $searchCount         = $res[1];

            //根据数量随机分配试题类型    200个初代
            for ($j = 0; $j < 200; $j++) {
                if ($typesNumArr[$i] >= $searchCount) {
                    if ($searchCount) {
                        foreach ($res[0] as $kRes) {

                            //如果重复继续
                            if(in_array($kRes['testid'],$uniqueArr[$j])){
                                continue;
                            }
                            $uniqueArr[$j][]=$kRes['testid'];

                            $this->initPopu[$j][$i][] = array(
                                'testid' => $kRes['testid'],
                                'diff'   => $kRes['diff'],
                                'klid'   => $kRes['klid'],
                                'testnum'   => $kRes['testnum'],
                                'typesid' => $iTypes);
                        }
                    }
                }
                else {
                    $arrRand = $this->getApiTest('Test/getRandArr',array(), $typesNumArr[$i], $searchCount);
                    //根据随机数进行赋值题
                    foreach ($arrRand as $kArrRand) {
                        //如果重复继续
                        if(in_array($res[0][$kArrRand]['testid'],$uniqueArr[$j])){
                            continue;
                        }
                        $uniqueArr[$j][]=$res[0][$kArrRand]['testid'];

                        $this->initPopu[$j][$i][] = array(
                            'testid' => $res[0][$kArrRand]['testid'],
                            'diff'   => $res[0][$kArrRand]['diff'],
                            'klid'   => $res[0][$kArrRand]['klid'],
                            'testnum'   => $res[0][$kArrRand]['testnum'],
                            'typesid' => $iTypes);
                    }
                }

                //不足的试题补空
                $testNum = count($this->initPopu[$j][$i]);
                if ($testNum < $typesNumArr[$i]) {
                    for ($k = $testNum; $k < $typesNumArr[$i]; $k++) {
                        $this->initPopu[$j][$i][] = 0;
                    }
                }
            }
        }
        unset($res);
        //如果没有试题怎么做
        if ($this->initPopu) {
            return 1;
        }
        else {
            return 0;
        }
    }

    /**
     * 初始种群适应度计算
     * @author demo
     */
    protected function initFit()
    {
        foreach ($this->initPopu as $i => $iPopu) {
            $buffer                 = $this->calcFit($iPopu);
            $this->initFit[$i]      = $buffer[0];
            $this->initPopu[$i]     = $buffer[1];
            $this->initPopuDiff[$i] = $buffer[2];
            $this->initTestSum[$i]  = $buffer[3];
        }
    }

    /**
     * 算子适应度计算
     * @param array $arr 算子
     * @return mixed
     * @author demo
     */
    protected function fitPaper($arr)
    {
        $buffer                 = $this->calcFit($this->calcPaper[0]);
        $arr[0][count($arr[0])] = $buffer[0];
        $arr[1][count($arr[1])] = $buffer[1];
        $arr[2][count($arr[2])] = $buffer[2];
        $arr[3][count($arr[3])] = $buffer[3]; //更新计算后的算子
        $buffer                 = $this->calcFit($this->calcPaper[1]);
        $arr[0][count($arr[0])] = $buffer[0];
        $arr[1][count($arr[1])] = $buffer[1];
        $arr[2][count($arr[2])] = $buffer[2];
        $arr[3][count($arr[3])] = $buffer[3]; //更新计算后的算子
        return $arr;
    }

    /**
     * 是否计算小题数量
     * @param int $i 题型序号（非id）
     * @param int $num 小题数量
     * @return int
     * @author demo
     */
    protected function ifCalcSmallNum($i,$num){
        //计分方式和选题方式 决定是否需要计算小题数量
        $testNum=1;

        if($this->initTypesScore[$i]==1 && $this->initSelectType[$i]==1){
            if($num==0)  $num=1;
            $testNum=$num;
        }

        return $testNum;
    }

    /**
     * 修复试卷数量
     * @param array $arr 试题数组
     * @return array
     * @author demo
     */
    protected function fixPaper($arr){

        $tTotal = $this->initTypesTotal; //每个题型试题总数 for choose

        $paper1 = array(); //存储试题id 用于排重
        //用于排重
        foreach ($arr as $i => $iArr) {
            foreach ($iArr as $j => $jArr) {
                if($jArr!=0) $paper1[] = $jArr['testid'];
            }
        }

        if(empty($arr)) return $arr;

        //对当前试题进行数量验证
        foreach ($arr as $i => $iArr) {

            $test=$this->initTest[$i];

            //如果试题库小于要选择的试题数量 继续
            if(count($test)<$tTotal[$i]){
                continue;
            }

            $testNum=0;
            foreach ($iArr as $j => $jArr) {

                //试题超出 去除多余试题
                if($testNum>=$tTotal[$i]){
                    unset($arr[$i][$j]);
                    continue;
                }
                $testNum+=$this->ifCalcSmallNum($i,$jArr['testnum']);
            }
            //试题数量少 补充试题
            if($testNum<$tTotal[$i]){

                $num=$tTotal[$i]-$testNum; //缺少多少题 从试题库中取出相应的题并且不重复
                $nowTest=0; //已经添加多少试题

                //临界点超出则换题
                $zj=0; //计数限制循环次数
                while ($num != $nowTest and count($test) > 0 and $zj < 100) {
                    $zj++;
                    //随机选题进行替换
                    $l2 = rand(0, count($test) - 1);
                    while (in_array($test[$l2]['testid'], $paper1)) {
                        unset($test[$l2]);
                        if (!$test)
                            break;
                        sort($test);
                        $l2 = rand(0, count($test) - 1);
                    }
                    if (!$test)
                        break;

                    $nowTestNum=count($arr[$i]); //新加入的试题放置位置
                    $nowTest+=$this->ifCalcSmallNum($i,$test[$l2]['testnum']);

                    if($num < $nowTest){
                        //换掉题数比较多的
                        //获取试题最多的序号
                        $nowTestNum=0;
                        $nowTestID=0;
                        foreach($arr[$i] as $j=>$jArr){
                            if($jArr['testnum']>$arr[$i][$nowTestNum]['testnum']){
                                $nowTestNum=$j;
                                $nowTestID=$jArr['testid'];
                            }
                        }
                        if($arr[$i][$nowTestNum]['testnum']==0)
                            break;
                        else
                            $nowTest-=$arr[$i][$nowTestNum]['testnum'];

                        //移除需要替换的试题
                        if($nowTestID!=0){
                            unset($paper1[array_search($nowTestID,$paper1)]);
                        }
                    }

                    $paper1[]=$test[$l2]['testid'];//加入排重数组

                    $arr[$i][$nowTestNum]=array(
                        'testid' => $test[$l2]['testid'],
                        'diff'   => $test[$l2]['diff'],
                        'klid'   => $test[$l2]['klid'],
                        'testnum'   => $test[$l2]['testnum'],
                        'typesid' => $test[$l2]['typesid']);
                }
            }
        }
        return $arr;
    }

    /**
     * 计算试卷适应度
     * @param array $arr 试题数组
     * @return array
     * @author demo
     */
    protected function calcFit($arr)
    {
        $arr=$this->fixPaper($arr); //对试卷进行数量优化

        $countkl   = ''; //知识点个数
        $countdiff = 0; //试卷难度相加的值
        $countnum  = 0; //总分数

        $tstyle = $this->initTypeList; //试题题型
        $vlist  = $this->initValList; //试题分值
        $tTotal = $this->initTypesTotal; //每个题型试题总数 for choose
        $tscore = $this->initTypesScore; //试题计分方式
        $ccheck = $this->initChooseCheck; //试题是否选做
        $cnum   = $this->initChooseNum; //试题选做个数
        $selectType = $this->initSelectType; //每个题型试题选题方式

        //分析试题的 知识点个数 分值 难度*分值的和
        foreach ($arr as $i => $iArr) {
            $typeScore=0; //题型总分值
            foreach ($iArr as $j => $jArr) {
                //试题是否不存在
                if($jArr==0) continue;

                //是否是选做题
                if ($ccheck[$i]) {
                    $countdiff += $vlist[$i]*$cnum[$i]/$tTotal[$i]*$jArr['diff'];
                    $typeScore  = $vlist[$i]*$cnum[$i];
                    continue;
                }

                //试题分值
                if ($tscore[$i] == 1) {
                    if($jArr['testnum']==0) $jArr['testnum']=1;
                    $countdiff += $jArr['testnum']*$vlist[$i]*$jArr['diff'];
                    $typeScore  += $jArr['testnum']*$vlist[$i];
                }
                else if ($tscore[$i] == 2) {
                    $countdiff += $vlist[$i]*$jArr['diff'];
                    $typeScore  += $vlist[$i];
                }

                //知识点数量 在选定的范围内计算
                if ($jArr['klid']) {
                    foreach ($jArr['klid'] as $kArr) {
                        if (in_array($kArr, $this->initKl))
                            $countkl .= $kArr.',';
                    }
                }
            }
            $countnum+=$typeScore;
        }

        //试卷难度  $countdiff/$countnum        难度比例  知识点比例
        $countkl    = explode(',', $countkl);
        $countkl    = array_filter($countkl);
        $countkl    = array_unique($countkl);
        $paper_diff = $countdiff / $countnum;
        $fit        = (1 - abs(count($countkl) / $this->initKlCover - $this->initKlPer) + 1 - abs($paper_diff - $this->initDiff)) / 2;
        if ($fit == 1) {
            $this->bestPaper   = $arr;
            $this->bestTestSum = $countnum;
            $this->flag        = 1;
        }
        return array(
            0 => $fit,
            1 => $arr,
            2 => $paper_diff,
            3 => $countnum);
    }

    /**
     * 生成轮盘函数
     * @author demo
     */
    protected function productWheel()
    {
        $sum = array_sum($this->initFit);
        foreach ($this->initFit as $ii => $nn) {
            if ($ii > 0)
                $this->initWheel[$ii] = $nn / $sum + $this->initWheel[$ii - 1];
            else {
                $this->initWheel[$ii] = $nn / $sum;
            }
        }
        $this->initWheel[count($this->initFit) - 1] = 1;
    }

    /**
     * 选择算子函数
     * @author demo
     */
    protected function chooseArithmeticOperators()
    {
        $this->calcPaper = array(); //清空算子
        $l1              = rand(0, 1000000000) / 1000000000;
        $l2              = rand(0, 1000000000) / 1000000000;
        $out             = 0; //跳出条件 $out为2
        foreach ($this->initWheel as $ii => $nn) {
            if ($l1 < $nn) {
                if (!$this->calcPaper[0]) {
                    $this->calcPaper[0] = $ii;
                    $out += 1;
                }
            }
            if ($l2 < $nn) {
                if (!$this->calcPaper[1]) {
                    $this->calcPaper[1] = $ii;
                    $out += 1;
                }
            }
            if ($out == 2)
                break; //跳出
        }
        //更新算子
        $this->calcPaper[0] = $this->initPopu[$this->calcPaper[0]];
        $this->calcPaper[1] = $this->initPopu[$this->calcPaper[1]];
    }

    /**
     * 基因交叉杂交函数 概率0.6
     * @author demo
     */
    protected function cross()
    {
        //去除试题id
        $paper1 = array();
        $paper2 = array();
        foreach ($this->calcPaper[0] as $i => $iTest) {
            foreach ($iTest as $jTest) {
                if($jTest!=0) $paper1[] = $jTest['testid'];
            }
        }

        foreach ($this->calcPaper[1] as $i => $iTest) {
            foreach ($iTest as $jTest) {
                if($jTest!=0) $paper2[] = $jTest['testid'];
            }
        }

        foreach ($this->calcPaper[0] as $i => $iTest) {
            foreach ($iTest as $j => $jTest) {
                if($jTest==0 || empty($this->calcPaper[1][$i][$j]) || $this->calcPaper[1][$i][$j]['testid']==$jTest['testid']){
                    continue;
                }

                $l1 = rand(0, 100) / 100;
                //0.6的概率触发交叉
                if ($l1 < 0.6) {

                    //检查试题是否重复 重复不交叉    算子1的题在算子2内是否重复 反之亦然
                    if (in_array($this->calcPaper[0][$i][$j]['testid'], $paper2) || in_array($this->calcPaper[1][$i][$j]['testid'], $paper1)) {
                        continue;
                    }

                    //paper1或paper2数据对应id交换
                    unset($paper1[array_search($this->calcPaper[0][$i][$j]['testid'], $paper1)]);
                    $paper1[] = $this->calcPaper[1][$i][$j]['testid'];
                    unset($paper2[array_search($this->calcPaper[1][$i][$j]['testid'], $paper2)]);
                    $paper2[] = $this->calcPaper[0][$i][$j]['testid'];

                    //算子交换
                    $test                       = $this->calcPaper[0][$i][$j];
                    $this->calcPaper[0][$i][$j] = $this->calcPaper[1][$i][$j];
                    $this->calcPaper[1][$i][$j] = $test;
                }
            }
        }
    }

    /**
     * 变异函数 概率0.01
     * @author demo
     */
    protected function mutation()
    {
        foreach ($this->calcPaper as $i => $iPaper) {
            $paper = array();
            foreach ($iPaper as $j => $jPaper) {
                foreach ($jPaper as $k => $kPaper) {
                    if($kPaper!=0) $paper[] = $kPaper['testid'];
                }
            }

            foreach ($iPaper as $j => $jPaper) {
                foreach ($jPaper as $k => $kPaper) {
                    if($kPaper==0){
                        continue;
                    }

                    $l1 = rand(0, 1000) / 1000;
                    //0.01的概率触发变异
                    if ($l1 < 0.01) {
                        //随机取题 判断题型 在对应题型内取随机数
                        $l2 = rand(0, count($this->initTest[$j]) - 1);

                        //检查选做题题型 同时替换的试题带小题 则不变异
                        if($this->initChooseCheck[$j]>0 && $this->initTest[$j][$l2]['testnum']>1)
                            continue;

                        //检查试题是否重复 重复不变异
                        if (!in_array($this->initTest[$j][$l2]['testid'], $paper)) {
                            //改变检查id数组数据
                            unset($paper[array_search($this->calcPaper[$i][$j]['testid'], $paper)]);
                            $paper[] = $this->initTest[$j][$l2]['testid'];

                            $this->calcPaper[$i][$j][$k] = array(
                                'testid' => $this->initTest[$j][$l2]['testid'],
                                'diff'   => $this->initTest[$j][$l2]['diff'],
                                'klid'   => $this->initTest[$j][$l2]['klid'],
                                'testnum'   => $this->initTest[$j][$l2]['testnum'],
                                'typesid' => $this->initTest[$j][$l2]['typesid']);
                        }
                    }
                }
            }
        }
    }

    /**
     * 根据题型序号返回题型id
     * @param int $num 第N个题型
     * @return int
     * @author demo
     */
    protected function getTypesByNum($num)
    {
        $typesIDArr = $this->initTypeList; //题型id
        return $typesIDArr[$num];
    }

    /**
     * 保存最优策略函数
     * @return array
     * @author demo
     */
    protected function bestPaper()
    {
        $arrBest   = array(); //最优解
        $arrFit    = 0; //匹配度
        $arrDiff   = 0; //试卷难度
        $arrSum    = 0; //试卷总分值
        $testid    = array(); //试题id
        $testtypes = array(); //试题题型
        $output    = array(); //输出试卷
        if ($this->bestPaper) {
            $arrBest = $this->bestPaper;
            $arrSum  = $this->bestTestSum;
            $arrFit  = 1;
            $arrDiff = $this->initDiff;
        } else {
            arsort($this->initFit);
            $key     = key($this->initFit);
            $arrBest = $this->initPopu[$key];
            $arrDiff = $this->initPopuDiff[$key];
            $arrSum  = $this->initTestSum[$key];
            $arrFit  = current($this->initFit);
        }

        //对试卷中出现的不存在的试题放到最后
        foreach ($arrBest as $i => $iArrBest) {
            $total = count($iArrBest);
            foreach ($iArrBest as $j => $jArrBest) {
                if ($jArrBest != 0) {
                    $testID[] = $jArrBest['testid']; //获取试题id
                }
                else if ($j != $total - 1) {
                    for ($k = $j + 1; $k < $total; $k++) {
                        //如果存在则交换并记录获取的试题id
                        if ($iArrBest[$k] != 0) {
                            $arrBest[$i][$j] = $iArrBest[$k];
                            $arrBest[$i][$k] = 0;
                            $testID[]        = $iArrBest[$k]['testid']; //获取试题id
                            break;
                        }
                    }
                }
            }
        }

        $output[0] = $testID; //试题id数组
        $output[1] = $arrBest; //最优解
        $output[2] = $arrFit; //适应度
        $output[3] = $arrDiff; //难度
        $output[4] = $arrSum; //试卷总分值

        return $output;
    }
}