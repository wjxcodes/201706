<?php
/**
 * 试卷打分分段管理模型
 * @author demo 16-4-22
 */
namespace Doc\Model;
class DocScoreSegmentModel extends BaseModel{


    /**
     * 保存数据
     * @param int $docid 文档id
     * @param array $data 有效键：'BeginPosition', 'EndPosition', 'Description'
     * @param int $id 主键id，当不为空时，为修改操作
     * @return boolean|array 失败返回false，或者当$id为0时，返回$ids 
     * @author demo 16-4-25
     */
    public function save($docid, $data, $id=0){
        $docid = (int)$docid;
        $id = (int)$id;
        if($id === 0){
            $ids = array();
            foreach($data as $key=>$value){
                $value['DocID'] = $docid;
                $result = $this->insertData($value);
                if($result === false){
                    return $result;
                }
                $ids[] = $result;
            }
            return $ids;
        }
        return ($this->updateData($data[0], 'ID='.$id) !== false);
    }

    /**
     * 根据给定的分值显示打分描述
     * @param int score
     * @return string
     * @author demo 16-4-22
     */
    public function getDescriptionByScore($score){
        $data = $this->findData('Description', "BeginPosition >= {$score} AND EndPosition <= {$score}");
        if(empty($data)){
            return '';
        }
        return $data['Description'];
    }

    /**
     * 根据给定的文档ID返回该分值段的所有信息
     * @param int $docid
     * @return array
     * @author demo 16-4-22 
     */
    public function getListByDocId($docid){
        return (array)$this->selectData('ID,BeginPosition,EndPosition,Description', "DocID={$docid}");
    }
}