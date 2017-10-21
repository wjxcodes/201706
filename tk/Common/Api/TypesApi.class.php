<?php
/**
 * 文档类型api
 * @author demo 2015-1-5
 */
namespace Common\Api;
class TypesApi extends CommonApi{
    /**
     * 获取题型以id为键的数据集
     * @return 题型数据集
     * @author demo
     */
    public function types(){
        return SS('types');
    }

    /**
     * 获取题型对应学科数据集
     * @return 题型数据集
     * @author demo
     */
    public function typesSubject(){
        return SS('typesSubject');
    }

    /**
     * 根据学科ID获取题型
     * @param array $param
     * ##参数说明:
     * $param = array(
     *    'style'=>'types',
     *    'subjectID'=>学科ID
     * )
     * ##
     * @return array $return
     * ##返回数据格式:
     * $return = array(
     *        [0] => Array
     *               (
     *                  [TypesID] => 79 //体型ID
     *                  [TypesName] => 选择题 //体型名称
     *                  [SubjectID] => 13 //学科ID
     *                  [OrderID] => 1 //排序
     *                  [Num] => 30 //最大体型数量
     *                  [Volume] => 1 //所在试卷 1卷1 2卷2
     *                  [DScore] => 5 //默认分值 默认1
     *                  [MaxScore] => 10 //最大分值 默认1
     *                  [TypesScore] => 2//试题分值类型；按小题计分1 按大题计分2
     *                  [IfSingle] => 0//题型是否对应单选（0是，1否）
     *                  [IfSearch] => 0//是否设置默认在本题型搜题参数（0是，1否）
     *                  [IfDo] => 0 //题型是否有选做题（0是，1否）
     *                  [IfChooseType] => 1 //题型是否有选择类型（0是，1否）
     *                  [IfChooseNum] => 1 //题型是否选择小题（0是，1否）
     *                  [TypesStyle] => 1 //默认题型类型（默认3，1选择题，2选择非选择混合，3非选择题）
     *                  [IfPoint] => 0 //题型是否需要0.5分（1是，0否 默认）
     *                  [SelectType] => 1 //选题方式 默认0忽略小题数量 1按小题数量 关联intelNum
     *                  [ScoreNormal] => 0 //试题任务加分值 默认 0
     *                  [ScoreIntro] => 0 //试题入库加分值 默认0
     *                  [ScoreMiss] => 0 //放弃标引扣分值 默认0
     *                  [ScorePic] => 0 //图片版加分值 默认0
     *                  [IntelName] => 题 //取单位（用于智能组卷题型选取，值可为个，篇等,默认值为题）
     *                  [IntelNum] => Array //
     *                      (
     *                         [0] => 0
     *                      )
     *                      //IntelNum说明:选取数量（默认0）用于智能组卷,选题方式为'是'时在智能组卷选题方式显示每'题'(选取单位)多少小题,逗号间隔的数字为数量可选项,例如：5,15,25 （英文逗号）关联SelectType
     *              ),
     *              ...
     * )
     * ##
     * @author demo
     */
    public function typesCache($param){
        extract($param);
        $buffer=$this->typesSubject(); //根据学科ID获取对应的题型
        return $buffer[$subjectID];
    }

    /**
     * 获取题型选取单位
     * @param array $param
     * ##参数说明:
     * $param = array(
     *    'style'=>'typesIntel',
     *    'subjectID'=>学科ID
     * )
     * ##
     * @return array $return
     * ##返回数据格式:
     * $return = array
     *        (
     *           [79] => 题 //题型选取单位
     *           [80] => 题 //题型选取单位
     *           [81] => 题 //题型选取单位
     *        )
     * ##
     * @author demo
     */
    public function typesIntelCache($param){
        $subjectTypes=$this->typesCache($param);
        $intelNameArray=array();
        foreach($subjectTypes as $i=>$iSubjectTypes){
            $intelNameArray[$iSubjectTypes['TypesID']]=$iSubjectTypes['IntelName'];
        }
        return $intelNameArray;
    }

    /**
     * 获取题型数据集
     * @return 题型数据集
     * @author demo
     */
    public function getTypeFullList(){
        $buffer = $this->typesSubject();

        $output=array();
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                foreach($iBuffer as $j=>$jBuffer){
                    $output[$i][$j]['TypesID']=$jBuffer['TypesID'];
                    $output[$i][$j]['TypesName']=$jBuffer['TypesName'];
                    $output[$i][$j]['SubjectID']=$jBuffer['SubjectID'];
                    $output[$i][$j]['TypesScore']=$jBuffer['TypesScore'];
                    $output[$i][$j]['DScore']=$jBuffer['DScore'];
                    $output[$i][$j]['Volume']=$jBuffer['Volume'];
                    $output[$i][$j]['IfDo']=$jBuffer['IfDo'];
                    $output[$i][$j]['Num']=$jBuffer['Num'];
                }
            }
        }
        return $output;
    }
}