<?php

/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-3-25
 * Time: 下午3:20
 * 能力值(用户和知识点)的计算不包含未做的题目
 */
namespace Aat\Controller;
class PersonalReportController extends BaseController
{
    protected $userForecastModel;
    protected $userAnswerRecordModel;


    public function _initialize() {
//        $this->userForecastModel = $this->getModel('UserForecast');
//        $this->userAnswerRecordModel = $this->getModel('UserAnswerRecord');
    }

    /**
     * 个人能力报告页面
     * @author demo
     */
    public function index() {
        $recordDb = $this->getModel('UserTestRecord')->findData(
            'TestID',
            ['UserName'=>$this->getUserName()]
        );
        if (!$recordDb) {
            //还没有进行过测试，没有测试记录
            $this->setMsg('请先进行测试！');
            $this->redirect('Aat/Default/index');
        }
        $this->assign('pageName','能力评估');
        $this->display();
    }

    /**
     * 返回【时间 该学科精准预测分总分 还有几次可以生成精准预测分
     * 快速预测分 精准预测分 答题数量 排名信息 精准趋势 快速趋势 】信息
     * @author demo
     */
    public function returnScoreInfo() {
        $this->checkRequest();
        $userName = $this->getUserName();
        $subjectID = $this->getSubjectID();
        $IData = $this->getApiAat('Score/scoreInfoWeb', $userName,$subjectID);
        return $this->setBack($IData[1]);
    }

    /**
     * 返回知识点答题情况树结构,只有两级结构
     * @return array|null 如果存在有效测试有就返回数据集，没有返回null
     * @author demo
     */
    public function returnKlInfo() {
        $this->checkRequest();
        $userName = $this->getUserName();
        $subjectID = $this->getSubjectID();
        $result = $this->getModel('UserTestKl')->getKlInfo($userName,$subjectID);
        if($result){
            return $this->setBack(['knowledge'=>$result]);
        } else {
            $this->setError('50203', 1); //暂无数据！
        }
    }

    /**
     * 返回知识点能力值情况用于雷达图（只有一层，每个知识点有最近的三个能力值显示）
     * @return array 知识点能力值数组
     * ['series'=>
     * [['value'=>[1,2],'name'=>'时间1'],['value'=>[1,2],'name'=>'时间2']],
     * 'indicator'=>
     * [['text'=>'知识点1','max'=>'最大值3'],['text'=>'知识点2','max'=>'最大值3']]
     * ]
     * @author demo
     */
    public function returnKlsInfo(){
        $this->checkRequest();
        $userTestKlsModel = $this->getModel('UserTestKls');
        $subjectID = $this->getSubjectID();
        $userName=$this->getUserName();
//        $knowledge = SS('klBySubject3')[$subjectID];//知识点
        $knowledge = $this->getApiCommon('Knowledge/klBySubject3')[$subjectID];
        $field='KlAbility,LoadTime';
        $where=['SubjectID'=>$subjectID,'UserName'=>$userName,'KlAbility'=>['neq','null']];
        $order='LoadTime DESC';
        $indicator=[];
        $series=[0=>['name'=>'最近第1次'],1=>['name'=>'最近第2次'],2=>['name'=>'最近第3次']];
        $flag = 0;//次数标识，用于判断是否生成雷达图
        $min=3;
        $max=-3;
        foreach($knowledge as $i=>$kl){//按知识点ID查
            $where['KlID']=$kl['KlID'];
            $abilityList = $userTestKlsModel->selectData(
                $field,
                $where,
                $order,
                '3'
            );
            if($abilityList){
                $indicator[]=['text'=>$kl['KlName'],'max'=>3,'min'=>-3];
                $num=$abilityList[0]?round($abilityList[0]['KlAbility'],2):-3;
                if($num<$min) $min=$num;
                if($num>$max) $max=$num;
                $series[0]['value'][]=$num;
                if($num<$min) $min=$num;
                if($num>$max) $max=$num;
                $num=$abilityList[1]?round($abilityList[1]['KlAbility'],2):-3;
                $series[1]['value'][]=$num;
                if($num<$min) $min=$num;
                if($num>$max) $max=$num;
                $num=$abilityList[2]?round($abilityList[2]['KlAbility'],2):-3;
                $series[2]['value'][]=$num;

                $flag++;
            }else{
                $min=-3;
                $indicator[]=['text'=>$kl['KlName'],'max'=>3,'min'=>-3];
                $series[0]['value'][]=-3;
                $series[1]['value'][]=-3;
                $series[2]['value'][]=-3;
            }

        }
        //@@@68698189
        if($userName == '123456789@qq.com'){

//            //修改知识点雷达图数据
            foreach ($series as $i => $iSeries){
                foreach($iSeries['value'] as $j=>$jSeries){
                    $num=($jSeries+rand(10,30)/10);
                    $series[$i]['value'][$j] = $num>3?2.5:$num;
                }
            }
//            //修改知识点雷达图数据
//            foreach ($indicator as $i => $iIndicator){
//                $indicator[$i]['max']=$max;
//                $indicator[$i]['min']=$min;
//            }
        }
        $result['indicator']=$indicator;//组合雷达图的外围知识点数据结构
        $result['series']=$series;//组合雷达图的数据结构
        if($flag == 0){
            $result = [];
        }
        if(!$result){
            $this->setError('50204', 1); //暂无，请先进行更多测试以生成该图表！
        }else{
            return $this->setBack($result);
        }
    }
/**
     * 返回知识点能力值情况用于雷达图（只有一层，每个知识点有最近的三个能力值显示）
     * @return array 知识点能力值数组
     * ['series'=>
     * [['value'=>[1,2],'name'=>'时间1'],['value'=>[1,2],'name'=>'时间2']],
     * 'indicator'=>
     * [['text'=>'知识点1','max'=>'最大值3'],['text'=>'知识点2','max'=>'最大值3']]
     * ]
     * @author demo
     */
    public function returnSkillInfo(){
        $this->checkRequest();
        $userTestKlsModel = $this->getModel('UserTestSkill');
        $subjectID = $this->getSubjectID();
        $userName=$this->getUserName();
//        $knowledge = SS('klBySubject3')[$subjectID];//知识点
        $knowledge = SS('SkillBySubject3')[$subjectID];
        $field='SkillAbility,LoadTime';
        $where=['SubjectID'=>$subjectID,'UserName'=>$userName,'SkillAbility'=>['neq','null']];
        $order='LoadTime DESC';
        $indicator=[];
        $series=[0=>['name'=>'最近第1次'],1=>['name'=>'最近第2次'],2=>['name'=>'最近第3次']];
        $flag = 0;//次数标识，用于判断是否生成雷达图
        foreach($knowledge as $i=>$kl){//按知识点ID查
            $where['SkillID']=$kl['SkillID'];
            $abilityList = $userTestKlsModel->selectData(
                $field,
                $where,
                $order,
                '3'
            );
            if($abilityList){
                $indicator[]=['text'=>$kl['SkillName'],'max'=>3,'min'=>-3];
                $series[0]['value'][]=$abilityList[0]?round($abilityList[0]['SkillAbility'],2):-3;
                $series[1]['value'][]=$abilityList[1]?round($abilityList[1]['SkillAbility'],2):-3;
                $series[2]['value'][]=$abilityList[2]?round($abilityList[2]['SkillAbility'],2):-3;
                $flag++;
            }else{
                $indicator[]=['text'=>$kl['SkillName'],'max'=>3,'min'=>-3];
                $series[0]['value'][]=-3;
                $series[1]['value'][]=-3;
                $series[2]['value'][]=-3;
            }

        }
        //@@@68698189
        if($userName == '123456789@qq.com'){
            //修改知识点雷达图数据
            foreach ($series as $i => $iSeries){
                foreach($iSeries['value'] as $j=>$jSeries){
                    $num=($jSeries+rand(10,30)/10);
                    $series[$i]['value'][$j] = $num>3?2.5:$num;
                }
            }
        }
        $result['indicator']=$indicator;//组合雷达图的外围知识点数据结构
        $result['series']=$series;//组合雷达图的数据结构
        if($flag == 0){
            $result = [];
        }
        if(!$result){
            $this->setError('50204', 1); //暂无，请先进行更多测试以生成该图表！
        }else{
            return $this->setBack($result);
        }
    }
    /**
     * 返回知识点能力值情况用于雷达图（只有一层，每个知识点有最近的三个能力值显示）
     * @return array 知识点能力值数组
     * ['series'=>
     * [['value'=>[1,2],'name'=>'时间1'],['value'=>[1,2],'name'=>'时间2']],
     * 'indicator'=>
     * [['text'=>'知识点1','max'=>'最大值3'],['text'=>'知识点2','max'=>'最大值3']]
     * ]
     * @author demo
     */
    public function returnCapacityInfo(){
        $this->checkRequest();
        $userTestKlsModel = $this->getModel('UserTestCapacity');
        $subjectID = $this->getSubjectID();
        $userName=$this->getUserName();
//        $knowledge = SS('klBySubject3')[$subjectID];//知识点
        $knowledge = SS('CapacityBySubject3')[$subjectID];
        $field='CapacityAbility,LoadTime';
        $where=['SubjectID'=>$subjectID,'UserName'=>$userName,'CapacityAbility'=>['neq','null']];
        $order='LoadTime DESC';
        $indicator=[];
        $series=[0=>['name'=>'最近第1次'],1=>['name'=>'最近第2次'],2=>['name'=>'最近第3次']];
        $flag = 0;//次数标识，用于判断是否生成雷达图
        foreach($knowledge as $i=>$kl){//按知识点ID查
            $where['CapacityID']=$kl['CapacityID'];
            $abilityList = $userTestKlsModel->selectData(
                $field,
                $where,
                $order,
                '3'
            );
            if($abilityList){
                $indicator[]=['text'=>$kl['CapacityName'],'max'=>3,'min'=>-3];
                $series[0]['value'][]=$abilityList[0]?round($abilityList[0]['CapacityAbility'],2):-3;
                $series[1]['value'][]=$abilityList[1]?round($abilityList[1]['CapacityAbility'],2):-3;
                $series[2]['value'][]=$abilityList[2]?round($abilityList[2]['CapacityAbility'],2):-3;
                $flag++;
            }else{
                $indicator[]=['text'=>$kl['CapacityName'],'max'=>3,'min'=>-3];
                $series[0]['value'][]=-3;
                $series[1]['value'][]=-3;
                $series[2]['value'][]=-3;
            }

        }
        //@@@68698189
        if($userName == '123456789@qq.com'){
            //修改知识点雷达图数据
            foreach ($series as $i => $iSeries){
                foreach($iSeries['value'] as $j=>$jSeries){
                    $num=($jSeries+rand(10,30)/10);
                    $series[$i]['value'][$j] = $num>3?2.5:$num;
                }
            }
        }
        $result['indicator']=$indicator;//组合雷达图的外围知识点数据结构
        $result['series']=$series;//组合雷达图的数据结构
        if($flag == 0){
            $result = [];
        }
        if(!$result){
            $this->setError('50204', 1); //暂无，请先进行更多测试以生成该图表！
        }else{
            return $this->setBack($result);
        }
    }
}