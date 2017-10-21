<?php
/**
 * @author demo 
 * @date 2014年11月20日
 */
/**
 * 学生作业生成对应知识点能力值类
 */
namespace Work\Model;
class UserWorkKlModel extends BaseModel{
    /**
     * 更新用户能力值和答题数量数据，如果没有就新增
     * @param int $subjectID 学科ID
     * @param int $classID 班级ID
     * @param int $userID 用户ID
     * @param int $klID 知识点ID
     * @param array $data 需要保存的数据 键必须和字段名相同
     * @author demo 5.5.27
     */
    public function updateAbilityAmount($subjectID,$classID,$userID,$klID,$data){
        $db = $this->findData(
            'WorkKlID,RightAmount,WrongAmount,UndoAmount,NotAmount,AllAmount',
            ['SubjectID'=>$subjectID,'ClassID'=>$classID,'UserID'=>$userID,'KlID'=>$klID],
            'AddTime DESC'
        );//因为需要累加数据，所以必须是最后一次的数据
        if($db){//存在则更新数据
            //统计数量需要累加数据库中的已有数据
            $data['RightAmount'] += $db['RightAmount'];
            $data['WrongAmount'] += $db['WrongAmount'];
            $data['UndoAmount'] += $db['UndoAmount'];
            $data['NotAmount'] += $db['NotAmount'];
            $data['AllAmount'] += $db['AllAmount'];
            $this->updateData(
                $data,
                ['WorkKlID'=>$db['WorkKlID']]
            );
        }else{//不存在则新增数据
            $this->insertData($data);
        }
    }




}