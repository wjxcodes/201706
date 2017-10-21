<?php
/**
 * @author demo
 * @date 2015年12月28日
 */

namespace Aat\Api;

class KlAbilityApi extends BaseApi
{

    /**
     * 返回知识点能力值情况用于雷达图（只有一层，每个知识点有最近的三个能力值显示）
     * APP手机端使用
     * @param string $userName
     * @param int $subjectID
     * @return array 知识点能力值数组
     * [
     * 'series'=>[[1,1,1],[2,2,2],[3,3,3]],
     * 'indicator'=>['aa','bb','cc']
     * ]
     * @author demo
     */
    public function abilityRadarMobile($userName,$subjectID){
        $userTestKlsModel = $this->getModel('UserTestKls');
//        $knowledge = SS('klBySubject3')[$subjectID];//知识点
        $knowledge = $this->getApiCommon('Knowledge/klBySubject3')[$subjectID];
        $field='KlAbility,LoadTime';
        $where = ['SubjectID'=>$subjectID,'UserName'=>$userName,'KlAbility'=>['neq','null']];
        $order="LoadTime DESC";
        $indicator=[];
        $series=[];
        $flag = 0;//次数标识，用于判断是否生成雷达图
        foreach($knowledge as $i=>$kl){//按知识点ID查
            $where['KlID'] = $kl['KlID'];
            $abilityList=$userTestKlsModel->selectData(
                $field,
                $where,
                $order,
                3
            );
            if($abilityList){
                $indicator[]=$kl['KlName'];
                $series[0][]=$abilityList[0]?intval(($abilityList[0]['KlAbility']+3)*16.666):0;
                $series[1][]=$abilityList[1]?intval(($abilityList[1]['KlAbility']+3)*16.666):0;
                $series[2][]=$abilityList[2]?intval(($abilityList[2]['KlAbility']+3)*16.666):0;
                $flag++;
            }else{
                $indicator[]=$kl['KlName'];
                $series[0][]=0;
                $series[1][]=0;
                $series[2][]=0;
            }
        }
        //@@@68698189
//        if($userName == '68698189@qq.com'){
//            //修改知识点雷达图数据
//            foreach ($series as $i => $iSeries){
//                foreach($iSeries as $j=>$jSeries){
//                    $series[$i][$j] = ($jSeries+rand(10,30)/10)>3?2.5:($jSeries+rand(10,30)/10);
//                    $series[$i][$j] = ($series[$i][$j]+3)*1.666;
//                }
//            }
//        }
        $result['indicator']=$indicator;//组合雷达图的外围知识点数据结构
        $result['series']=$series;//组合雷达图的数据结构
        if($flag == 0){
            $result = [];
        }
        return $result;
    }

    /**
     * 描述：获取知识点两级属性结构的数据，包含用户的能力值和作答数据
     * 用于提分首页生成用户选择试题的树形结构
     * @param $userName
     * @param $subjectID
     * @return array
     * @author demo
     */
    public function userKlAbility($userName,$subjectID){
        return $this->getModel('UserTestKl')->getKlInfo($userName, $subjectID);
    }

    /**
     * 描述：获取用户不同等级的知识点二级数据接口
     * @param $username
     * @param $subjectID
     * @param $level
     * @return mixed
     * @author demo
     */
    public function userLevelKl($username,$subjectID,$level) {
        $min = $max = 0;//最小 最大分数
        if($level == 'e'){
            $min = 0;
            $max = 60;
        }elseif($level == 'd'){
            $min = 60;
            $max = 70;
        }elseif($level == 'c'){
            $min = 70;
            $max = 80;
        }elseif($level == 'b'){
            $min = 80;
            $max = 90;
        }elseif($level == 'a'){
            $min = 90;
            $max = 101;
        }
        $data = $this->getModel('UserTestKl')->getKlInfo($username, $subjectID);
        if ($data) {
            $result = [];
            foreach ($data as $i => $k) {
                if (array_key_exists('sub', $k)) {
                    //有第二级的知识点
                    foreach ($k['sub'] as $j => $m) {
                        //遍历第二级
                        $rateTwo = $m['rightRate'];
                        if($rateTwo>=$min&&$rateTwo<$max){
                            //符合条件则显示
                            $result[$i]['sub'][$j] = array(
                                'kl_id'=>$m['klID'],
                                'name'=>$m['klName'],
                                'rate'=>$rateTwo,
                                'amount'=>'答题量：'.$m['allAmount'] . ' 正确率：' . $rateTwo.'%',
                                'kl_ability'=>$m['klAbility']===null?'':$m['klAbility'],
                            );
                        }
                    }
                    if ($result[$i]['sub']) {
                        $result[$i]['kl_id'] = $k['klID'];
                        $result[$i]['name'] = $k['klName'];
                        $result[$i]['amount'] = '答题量：' . $k['allAmount'] . ' 正确率：' . $k['rightRate'] . '%';
                        $result[$i]['kl_ability'] = $k['klAbility'] === null ? '' : $k['klAbility'];
                    }
                }else{
                    //只有一级的知识点
                    $rateOne = $k['rightRate'];
                    if($rateOne>=$min&&$rateOne<$max){
                        $result[$i] = array(
                            'kl_id'=>$k['klID'],
                            'name'=>$k['klName'],
                            'amount'=>'答题量：'.$k['allAmount'] . ' 正确率：' . $k['rightRate'].'%',
                            'kl_ability'=>$k['klAbility']===null?'':$k['klAbility'],
                        );
                    }
                }
            }
            $result = $this->formatTreeArray($result);
            if($result){
                return [1,$result];
            }else{
                return [0,'该阶段暂时没有试题，请先进行基础练习！'];
            }
        } else {
            return [0,'获取知识点数据失败，请刷新页面重试！'];
        }
    }

    /**
     * 描述：用户联系后知识点情况，ExerciseReport页面使用
     * @param $recordID
     * @param $username
     * @param int $level
     * @return array
     * @author demo
     */
    public function userKlInfo($recordID,$username,$level=2){
        if(!$recordID){
            return [0,'数据参数错误，请重试！'];
        }
        //数据显示层级
        $level = $level == 2 ? 2 : 3;
        $check=$this->checkExerciseRID($recordID,$ifDone=true,$username);
        if ($check===false) {
            return [0,'数据参数错误，请重试！'];
        }
        //每一次测试中每一道试题的知识点信息和作答正确情况
        $knowledge = $this->getKlAnswer($recordID);

        //从数据库中查询到的知识点树形结构
        if(!$knowledge){
            return [0,'本次测试无有效做题数据（做对或者做错的试题），暂无数据！'];
        }

        $klHaveList=array(); //获取需要留下的知识点数据
        $klListData=array(); //知识点对于做题数据计算
        $klListTest=array(); //知识点下的试题id 防止试题重复累计
//        $knowledgeParent=SS('knowledgeParent');
        $knowledgeParent = $this->getApiCommon('Knowledge/knowledgeParent');
        foreach($knowledge as $iKnowledge){
            if($iKnowledge['IfRight']==0 || $iKnowledge['IfRight']==-1){
                continue; //没有作答做无法批改的数据不做记录；
            }

            $bj=$iKnowledge['TestID'].'-'.$iKnowledge['Number'].'-'.$iKnowledge['OrderID'];

            if($knowledgeParent[$iKnowledge['KlID']]){
                foreach($knowledgeParent[$iKnowledge['KlID']] as $iKnowledgeParent){
                    //防止重复累计

                    if(in_array($bj,$klListTest[$iKnowledgeParent['KlID']])) continue;
                    $klListTest[$iKnowledgeParent['KlID']][]=$bj;

                    $klHaveList[]=$iKnowledgeParent['KlID'];
                    //初始化
                    if(!$klListData[$iKnowledgeParent['KlID']]){
                        $klListData[$iKnowledgeParent['KlID']]=array(
                            'RightNum'=>0,
                            'TotalNum'=>0
                        );
                    }
                    //IfRight 0无法判断 -1未作答 1表示错误 2表示正确
                    $klListData[$iKnowledgeParent['KlID']]=array(
                        'RightNum'=>($iKnowledge['IfRight']==2 ? ($klListData[$iKnowledgeParent['KlID']]['RightNum']+1) : $klListData[$iKnowledgeParent['KlID']]['RightNum']),
                        'TotalNum'=>($klListData[$iKnowledgeParent['KlID']]['TotalNum']+1)
                    );
                }
            }
            //当前知识点
            $klHaveList[]=$iKnowledge['KlID'];
            //初始化
            if(!$klListData[$iKnowledge['KlID']]){
                $klListData[$iKnowledge['KlID']]=array(
                    'RightNum'=>0,
                    'TotalNum'=>0
                );
            }
            //IfRight 0无法判断 -1未作答 1表示错误 2表示正确
            $klListData[$iKnowledge['KlID']]=array(
                'RightNum'=>($iKnowledge['IfRight']==2 ? ($klListData[$iKnowledge['KlID']]['RightNum']+1) : $klListData[$iKnowledge['KlID']]['RightNum']),
                'TotalNum'=>($klListData[$iKnowledge['KlID']]['TotalNum']+1)
            );
        }
        $klHaveList=array_unique($klHaveList);

        //需要留下的知识点 目前仅有两级
        $treeArr=array(); //留下的知识点数组
//        $klBySubject3=SS('klBySubject3')[$check['SubjectID']];
        $klBySubject3 = $this->getApiCommon('Knowledge/klBySubject3')[$check['SubjectID']];
        foreach($klBySubject3 as $iKlBySubject3){
            $tmpArray=array();
            if(in_array($iKlBySubject3['KlID'],$klHaveList)){
                if($klListData[$iKlBySubject3['KlID']]['TotalNum']==0){
                    $rate='0%';
                }else{
                    $rate=ceil($klListData[$iKlBySubject3['KlID']]['RightNum']/$klListData[$iKlBySubject3['KlID']]['TotalNum']*100).'%';
                }
                $tmpArray=array(
                    'KlID'=>$iKlBySubject3['KlID'],
                    'KlName'=>$iKlBySubject3['KlName'],
                    'Amount'=>$klListData[$iKlBySubject3['KlID']]['RightNum'].'/'.$klListData[$iKlBySubject3['KlID']]['TotalNum'],
                    'Rate'=>$rate
                );
            }
            if($iKlBySubject3['sub']){
                foreach($iKlBySubject3['sub'] as $jKlBySubject3){
                    if(in_array($jKlBySubject3['KlID'],$klHaveList)){

                        if($klListData[$iKlBySubject3['KlID']]['TotalNum']==0){
                            $rate='0%';
                        }else{
                            $rate=ceil($klListData[$jKlBySubject3['KlID']]['RightNum']/$klListData[$jKlBySubject3['KlID']]['TotalNum']*100).'%';
                        }
                        $tmpArray['sub'][]=array(
                            'KlID'=>$jKlBySubject3['KlID'],
                            'KlName'=>$jKlBySubject3['KlName'],
                            'Amount'=>$klListData[$jKlBySubject3['KlID']]['RightNum'].'/'.$klListData[$jKlBySubject3['KlID']]['TotalNum'],
                            'Rate'=>$rate
                        );
                    }
                }
            }

            if($tmpArray) $treeArr[]=$tmpArray;
        }
        if ($treeArr) {
            return [1,$treeArr];
        } else {
            return [0,'获取数据错误，请重试！'];
        }

//        $knowledgeParent=SS('knowledgeParent');
        $knowledgeParent = $this->getApiCommon('Knowledge/knowledgeParent');
        $treeArr=[];
        foreach ($knowledge as $i=>$iKnowledge) {
            if(!empty($knowledgeParent[$iKnowledge['KlID']])){
                foreach($knowledgeParent[$iKnowledge['KlID']] as $jKnowledgeParent){
                    $treeArr[$i][]=$jKnowledgeParent;
                }
                array_push($treeArr[$i],$iKnowledge);
            }else{
                $treeArr[$i][0]=$iKnowledge;
            }
        }
        //设定知识点最多3级
        $r = [];
        $repeatColumnOne = [];
        $repeatColumnTwo = [];
        $repeatColumnThree = [];
        //以下2层循环转换知识点结构为可js遍历的树形结构
        foreach ($treeArr as $k) {
            foreach ($k as $i => $m) {
                if ($i == 0 && !in_array($m['KlID'], $repeatColumnOne)) {
                    $repeatColumnOne[] = $m['KlID'];
                    $m = array_merge($m, $this->getStat($m['KlID'], $recordID));
                    $r[] = $m;
                }
                if ($i == 1) {
                    $tmpID = array_search($k[0]['KlID'], $repeatColumnOne);
                    if (!in_array($m['KlID'], $repeatColumnTwo[$tmpID])) {
                        $repeatColumnTwo[$tmpID][] = $m['KlID'];
                        $m = array_merge($m, $this->getStat($m['KlID'], $recordID));
                        $r[$tmpID]['sub'][] = $m;
                    }
                }
                if ($i == 2) {
                    $tmpIDTwo = array_search($k[1]['KlID'], $repeatColumnTwo[$tmpID]);
                    if (!in_array($m['KlID'], $repeatColumnThree[$tmpID][$tmpIDTwo])) {
                        $repeatColumnThree[$tmpID][$tmpIDTwo][] = $m['KlID'];
                        $m = array_merge($m, $this->getStat($m['KlID'], $recordID));
                        $r[$tmpID]['sub'][$tmpIDTwo]['sub'][] = $m;
                    }
                }
            }
        }
        //以下三层循环对上面生成的结构进行修改，增加一次层树和第二层树中的试题情况
        foreach ($r as $i => $k) {
            $tmpAllOne = [];
            $tmpRightOne = [];
            foreach ($k['sub'] as $j => $m) {
                $tmpAllTwo = [];
                $tmpRightTwo = [];
                foreach ($m['sub'] as $n) {
                    $tmpAllTwo = array_unique(array_merge($tmpAllTwo, $n['allTest']));
                    $tmpRightTwo = array_unique(array_merge($tmpRightTwo, $n['rightTest']));
                }
                $r[$i]['sub'][$j]['allTest'] = array_unique(array_merge($tmpAllTwo, $m['allTest']));
                $r[$i]['sub'][$j]['rightTest'] = array_unique(array_merge($tmpRightTwo, $m['rightTest']));

                $tmpAllOne = array_unique(array_merge($tmpAllOne, $tmpAllTwo, $m['allTest']));
                $tmpRightOne = array_unique(array_merge($tmpRightOne, $tmpRightTwo, $m['rightTest']));
            }
            $r[$i]['allTest'] = array_unique(array_merge($tmpAllOne, $k['allTest']));
            $r[$i]['rightTest'] = array_unique(array_merge($tmpRightOne, $k['rightTest']));
        }
        $result = [];
        foreach($r as $i =>$k){
            $amount = count($k['rightTest']).'/'.count($k['allTest']);
            $rate = round(count($k['rightTest'])/count($k['allTest']),2)*100;
            $result[$i] = [
                'name'=>$k['KlName'],
                'amount'=>$amount,
                'rate'=>$rate,
                'kl_id'=>$k['KlID'],
            ];
            if(array_key_exists('sub',$k)){
                foreach($k['sub'] as $j=>$m){
                    $amount = count($m['rightTest']).'/'.count($m['allTest']);
                    $rate = round(count($m['rightTest'])/count($m['allTest']),2)*100;
                    $result[$i]['sub'][$j] = [
                        'name'=>$m['KlName'],
                        'amount'=>$amount,
                        'rate'=>$rate,
                        'kl_id'=>$m['KlID'],
                    ];
                    if ($level == 3) {
                        if (array_key_exists('sub', $m)) {
                            foreach ($m['sub'] as $h => $n) {
                                $amount = count($n['rightTest']) . '/' . count($n['allTest']);
                                $rate = round(count($n['rightTest']) / count($n['allTest']), 2) * 100;
                                $result[$i]['sub'][$j]['sub'][$h] = [
                                    'name' => $n['KlName'],
                                    'amount' => $amount,
                                    'rate' => $rate,
                                    'kl_id' => $n['KlID'],
                                ];
                            }
                        }
                    }
                }
            }
        }

        if ($result) {
            return [1,$result];
        } else {
            return [0,'获取数据错误，请重试！'];
        }
    }

    /**
     * 获取单次测试中对应知识点作答情况的试题ID
     * @param $klID int 知识点ID
     * @param $recordID int 测试记录ID
     * @return array 全部以及正确的试题ID
     * @author demo
     */
    private function getStat($klID, $recordID) {
        $arr = $this->getKlAnswer($recordID);
        $all = [];
        $right = [];
        //IfRight 0无法判断 -1未作答 1表示错误 2表示正确
        foreach ($arr as $k) {
            if ($k['KlID'] == $klID) {
                if ($k['IfRight'] != 0) {
                    $all[] = $k['TestID'];
                    if ($k['IfRight'] == 2) {
                        $right[] = $k['TestID'];
                    }
                }
            }
        }
        return ['allTest' => $all, 'rightTest' => $right];
    }

    /**
     * 获取每一次测试中每一道试题的知识点信息和作答正确情况
     * @param $recordID int 测试记录ID
     * @return array 试题对应知识点信息
     * @author demo
     */
    private function getKlAnswer($recordID) {
        $knowledge = $this->getModel('UserAnswerRecord')->unionSelect('userAnswerSelectByRecordId',$recordID);
        return $knowledge;
    }

    /**
     * 格式化二级知识点树的数组的键值（主要用于手机端），解决键值不连续的问题
     * @param array $array 需要整理的数组 【重要】必须是二级的知识点数组，并且用sub标识下一级
     * @return array 一级和二级的键都是连续的
     * @author demo
     */
    private function formatTreeArray($array){
        $array = array_values($array);
        foreach($array as $i=>$iArray){
            if($iArray['sub']){
                $array[$i]['sub'] = array_values($iArray['sub']);
            }
        }
        return $array;
    }



}