<?php
/**
 * @author demo 
 * @date 2014年10月23日
 */
/**
 * 管理员模型类，用于处理管理员相关操作
 */
namespace Home\Model;
class LogSearchModel extends BaseModel{
    /**
     * 累加关键字次数
     * @param $keyWords string 关键字
     * @param $subjectID int 学科ID
     * @return bool
     * @author demo
     */
    public function addKeyWord($keyWords,$subjectID,$username){
        $keyWords=formatString('stripTags',$keyWords);
        $result=$this->selectKeyWord($keyWords);
        if($result){
            $lastResult=$this->updateKeyNum($result[0]['Nums'],$result[0]['SearchID'],$subjectID,$username);
        }else{
            $lastResult=$this->insertKeyWords($keyWords,$subjectID,$username);
        }
        return $lastResult;
    }
    /**
     * 查询关键字
     * @param string $keyWords 关键词
     * @return array
     * @author demo
     */
    public function selectKeyWord($keyWords){
        $where='KeyWord="'.$keyWords.'"';
        return $this->selectData(
            '*',
            $where
        );
    }
    /**
     * 累加次数
     * @param int $nowNums  关键字查询次数
     * @param int $searchID  关键字ID
     * @param int $subjectID  最后一次，搜索所在的学科
     * @return bool
     * @author demo
     */
    public function updateKeyNum($nowNums,$searchID,$subjectID,$username){
        $data['Nums']=$nowNums+1;
        $data['LastTime']=time();
        $data['UserName']=$username;
        $data['SubjectID']=$subjectID;
        $ok=$this->updateData(
            $data,
            'SearchID='.$searchID
        );
        return $ok;
    }
    /**
     * 录入新搜索内容
     * @param string $keyWords  新搜索内容
     * @param int $subjectID  最后一次，搜索所在的学科
     * @return bool
     * @author demo
     */
    public function insertKeyWords($keyWords,$subjectID,$username){
        $data['UserName']=$username;
        $data['KeyWord']=$keyWords;
        $data['Nums']=1;
        $data['LastTime']=time();
        $data['SubjectID']=$subjectID;
        $ok=$this->insertData($data);
        return $ok;
    }

}