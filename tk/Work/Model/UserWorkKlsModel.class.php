<?php
/**
 * @author demo 
 * @date 2014年11月20日
 */
/**
 * 学生作业生成对应知识点历次能力值记录类
 * 可以得到@todo
 */
namespace Work\Model;
class UserWorkKlsModel extends BaseModel{

    /**
     * 查询班级知识点能力值，用于生成班级知识点能力值雷达
     * @param int $subjectID 学科ID
     * @param int $classID 班级ID
     * @param int $klID 知识点ID
     * @return array|null
     * @author demo
     */
    public function classKlAvgAbility($subjectID,$classID,$klID){
        $db=$this->groupData(
            'SubjectID,ClassID,KlID,AVG(Ability) AS Ability,WorkID,AddTime',
            ['SubjectID'=>$subjectID,
            'ClassID'=>$classID,
            'KlID'=>$klID],
            'WorkID',
            'AddTime DESC',
            3
            );
        return $db?$db:null;
    }

}