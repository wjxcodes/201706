<?php
/**
 * @date 2014年10月24日
 * @author demo 
 */
/**
 * 用户知识点数据类
 * 此数据表用户存数用户不同知识点的答题情况和能力值等信息
 * @todo 此类中函数多数需要修改，使用缓存方式，优化效率问题
 */
namespace Aat\Model;

use Common\Model\BaseModel;
class UserTestKlModel extends BaseModel
{
    /**
     * 更新用户能力值和答题数量数据，如果没有就新增【PersonalReportAction使用】
     * @param int $subjectID 学科ID
     * @param int $userName 用户名
     * @param int $klID 知识点ID
     * @param array $data 需要保存的数据 键必须和字段名相同 （包含能力值和其它字段）
     * @author demo
     */
    public function updateAbilityAmount($subjectID,$userName,$klID,$data){
        $result = $this->selectData(
            'TestKlID,RightAmount,WrongAmount,UndoAmount,NotAmount,AllAmount',
            ['SubjectID'=>$subjectID,'UserName'=>$userName,'KlID'=>$klID],
            'LoadTime DESC');//因为需要累加数据，所以必须是最后一次的数据，其实也就一条数据正常情况下
        $db=$result[0];
        if($db){
            //存在则更新数据
            //统计数量需要累加数据库中的已有数据
            //【注意】data数组还有其它字段
            $data['RightAmount'] += $db['RightAmount'];
            $data['WrongAmount'] += $db['WrongAmount'];
            $data['UndoAmount'] += $db['UndoAmount'];
            $data['NotAmount'] += $db['NotAmount'];
            $data['AllAmount'] += $db['AllAmount'];
            $this->updateData(
                $data,
                ['SubjectID'=>$subjectID,'UserName'=>$userName,'KlID'=>$klID]);
        }else{
            //不存在则新增数据
             $this->insertData(
                 $data);
        }
    }

    /**
     * 返回知识点（正确情况 能力值）的树形结构
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @return array
     * @author demo
     */
    public function getKlInfo($userName, $subjectID) {
//        $knowledge = SS('klBySubject3')[$subjectID];//知识点
        $knowledge = $this->getApiCommon('Knowledge/klBySubject3')[$subjectID];
        $klListDb = $this->selectData(
            $field = 'KlID,AllAmount,RightAmount,WrongAmount,UndoAmount,NotAmount,KlAbility,LoadTime',
            $where = ['UserName' => $userName, 'SubjectID' => $subjectID],
            $order = 'KlID ASC,TestKlID DESC'
        );//数据库user_test_kl中查询到的用户学科下知识点情况
//        $klListDb = $this->groupData(
//                $field = 'KlID,sum(AllAmount) AllAmount,sum(RightAmount) RightAmount,sum(WrongAmount) WrongAmount,sum(UndoAmount) UndoAmount,sum(NotAmount) NotAmount,KlAbility,LoadTime',
//                $where = ['UserName' => $userName, 'SubjectID' => $subjectID],
//                $group = 'KlID',
//                $order = 'KlID ASC');
        $result = [];//返回数据集
        $klList = [];//知识点ID为键的$klListDb
        //转化成以知识点ID为下标的数据集
        if ($klListDb) {
            foreach ($klListDb as $iKlListDb) {
                $klList[$iKlListDb['KlID']] = $iKlListDb;
            }
        }
        //循环两层知识点赋值
        foreach ($knowledge as $i => $iKnowledge) {
            if ($iKnowledge['sub']) {//第二级知识点
                foreach ($iKnowledge['sub'] as $j => $jKnowledge) {
                    $jKlID = $jKnowledge['KlID'];
                    $jKlList = $klList[$jKlID];
                    $rightAmount = $jKlList['RightAmount']?$jKlList['RightAmount']:0;
                    $allAmount = $jKlList['RightAmount'] + $jKlList['WrongAmount'];//用于计算正确率的总数
//                  用于计算正确率的总数  if ($userName == '68698189@qq.com') {//@@@68698189@qq.com
//                        $rightAmount = $jKlList['RightAmount'] + 14 - 5;
//                        $allAmount = $jKlList['RightAmount'] + $jKlList['WrongAmount'] + 14;
//                    }
                    $result[$i]['sub'][$j]['klID'] = $jKnowledge['KlID'];
                    $result[$i]['sub'][$j]['klName'] = $jKnowledge['KlName'];
                    $result[$i]['sub'][$j]['allAmount'] = $jKlList['AllAmount']?$jKlList['AllAmount']:0;
                    $result[$i]['sub'][$j]['rightWrongAmount'] = $allAmount;
                    $result[$i]['sub'][$j]['rightAmount'] = $rightAmount;
                    $result[$i]['sub'][$j]['wrongAmount'] = $jKlList['WrongAmount']?$jKlList['WrongAmount']:0;
                    $result[$i]['sub'][$j]['undoAmount'] = $jKlList['UndoAmount']?$jKlList['UndoAmount']:0;
                    $result[$i]['sub'][$j]['notAmount'] = $jKlList['NotAmount']?$jKlList['NotAmount']:0;
                    $result[$i]['sub'][$j]['klAbility'] = $jKlList['KlAbility']?$jKlList['KlAbility']:'';
                    $result[$i]['sub'][$j]['loadTime'] = $jKlList['LoadTime']?$jKlList['LoadTime']:0;
                    $result[$i]['sub'][$j]['rightRate'] = round($rightAmount / ($allAmount ? $allAmount : 1), 2) * 100;
                }
            }

            $iKlID = $iKnowledge['KlID'];
            $iKlList = $klList[$iKlID];
            $rightAmount = $iKlList['RightAmount']?$iKlList['RightAmount']:0;
            $allAmount = $iKlList['RightAmount'] + $iKlList['WrongAmount'];//用于计算正确率的总数
            //@@@68698189@qq.com
//            if ($userName == '68698189@qq.com') {
//                $rightAmount = $iKlList['RightAmount'] - 5 + 28;
//                $allAmount = $iKlList['RightAmount'] + $iKlList['WrongAmount'] + 28;
//            }
            $result[$i]['klID'] = $iKnowledge['KlID'];
            $result[$i]['klName'] = $iKnowledge['KlName'];
            $result[$i]['allAmount'] = $iKlList['AllAmount']?$iKlList['AllAmount']:0;
            $result[$i]['rightWrongAmount'] = $allAmount?$allAmount:0;
            $result[$i]['rightAmount'] = $rightAmount?$rightAmount:0;
            $result[$i]['wrongAmount'] = $iKlList['WrongAmount']?$iKlList['WrongAmount']:0;
            $result[$i]['undoAmount'] = $iKlList['UndoAmount']?$iKlList['UndoAmount']:0;
            $result[$i]['notAmount'] = $iKlList['NotAmount']?$iKlList['NotAmount']:0;
            $result[$i]['klAbility'] = $iKlList['KlAbility']?$iKlList['KlAbility']:'';
            $result[$i]['loadTime'] = $iKlList['LoadTime']?$iKlList['LoadTime']:0;
            $result[$i]['rightRate'] = round($rightAmount / ($allAmount ? $allAmount : 1), 2) * 100;
        }

        return $result;
    }
}