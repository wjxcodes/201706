<?php
/**
 * @author demo 
 * @date 2014年9月9日
 * @update 2015年9月29日
 */
/**
 * 问卷调查类，用于处理用户的使用反馈情况                    
 */
namespace Index\Model;
class QuestionModel extends BaseModel{
    
    /**
     * 添加问卷
     * @param array $data 数据表字段数组
     * @return bool
     * @author demo
     */
    public function addQuestion($data){
        return $this->insertData($data);
    }
}