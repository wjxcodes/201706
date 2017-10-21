<?php
/**
 * 校本题库从库章节Model类
 * @auhtor 
 * @date 2015-4-7
 */
namespace Custom\Model;
use Common\Model\BaseModel;
class CustomTestChapterCopyModel extends BaseModel{

    protected $modelName = 'CustomTestChapterCopy';

    /**
     * 删除数据；
     * @param int $testId 要删除的试题id
     * @param string $chapterId 要删除的章节id，默认为空时删除$testId所有章节
     * @return boolean
     * @author demo
     */
    public function deleteData($testId,$chapterId=''){
        $testId = ltrim($testId, \Test\Model\TestQueryModel::DIVISION);
        $where = 'TestID='.(int)$testId;
        if($chapterId != ''){
            $where .= " AND ChapterID IN({$chapterId})";
        }
        return parent::deleteData(
            $where);
    }

    /**
     * 根据id查询章节信息
     * @param string $testid 试题id，多个id用,分割
     * @author demo
     * @return array
     */
    public function getChapters($testid){
        $result = $this->selectData(
            '*',
            "TestID IN({$testid})",
            'TestID desc');
        $data = array();
        foreach($result as $value){
            if(!$data[$value['TestID']]){
                $data[$value['TestID']] = array();
            }
            $data[$value['TestID']][] = $value['ChapterID'];
        }
        unset($result);
        return $data;
    }

    /**
     * 新增、修改数据
     * @param array $param 格式 array('testid'=>xx,'chapter'=>array(1,2,3...))
     * @param boolean 失败返回false
     * @return bool
     * @author demo
     */
    public function saveData($param){
        $diff = $this->findDifference($param['testid'],$param['chapter']);
        if(!empty($diff)){
            $insertData = $deleteData = array();
            foreach($diff as $val){
                //忽略为空数据
                if(!empty($val)){
                    if(in_array($val,$param['chapter'])){ //需新增的数据
                        $param['testid'] = ltrim($param['testid'], \Test\Model\TestQueryModel::DIVISION);
                        $insertData[] = array('TestID'=>$param['testid'],'ChapterID'=>$val);
                    }else{ //需删除的数据
                        $deleteData[] = $val;
                    }
                }
            }
            unset($diff);
            if(!empty($insertData)){
                $result = $this->addAllData($insertData);
                if($result === false)
                    return false;
            }
            if(!empty($deleteData)){
                $deleteData = implode(',', $deleteData);
                //------------------此处未进行sql检查-------------------
                $this->deleteData($param['testid'],$deleteData);
            }
        }
        return true;
    }

    /**
     * 查找指定试题的章节内容，同时根据comparer对比数据。返回与comparer的差集数据
     * @param int $id 试题id
     * @param array $comparer 对比的数据 格式：array('chapterid','chapterid1'...);
     * @return array 返回比较后的章节id数组
     * @author demo
     */
    public function findDifference($id,$comparer){
        $id = ltrim($id, \Test\Model\TestQueryModel::DIVISION);
        //确保返回数据的正确性
        foreach($comparer as $k=>$v){
            $comparer[$k] = (int)$v;
        }
        $data = $this->selectData(
            'chapterID',
            array('TestID'=>$id));
        $orginal = array();
        foreach($data as $value){
            $orginal[] = (int)$value['chapterID'];
        }
        unset($data);
        return array_merge(array_diff($comparer, $orginal),array_diff($orginal, $comparer));
    }
}