<?php
/**
 * 原创模板model
 * @author demo 2015-9-7
 */
namespace Yc\Model;
class OriginalityTemplateModel extends OriginalityTplBaseModel{

    /**
     * 实现父类抽象方法
     */
    public function getCondition($type=''){
        return 'TID='.$this->getId();
    }

    /**
     * 返回后台管理相关数据
     * @param array $params 查询参数
     * @return array 分页信息及总页数
     * @author demo 
     */
    public function getListByPagtion($params){
        $where = $params;
        foreach($where as $key=>$value){
            if(empty($value)){
                unset($where[$key]);
            }
        }
        $count = $this->selectCount($where, 'TID');
        $this->pagtion['page'] = page($count, $this->pagtion['page'], $this->pagtion['recordsNum']);
        return array($this->getList($where), $count);
    }

    /**
     * 返回结果，本结果当主键不为空时，将返回一条记录
     * @param string $where
     * @param int $id 主键id，不为空时，按照主键id查询
     * @return array
     * @author demo 
     */
    public function getList($where=''){
        if(empty($where)){
            $where = '1=1';
        }else if(is_array($where)){
            $temp = [];
            while(list($k, $v) = each($where)){
                if($k === 'SID'){
                    $temp[] = "t.SID='{$v}'";
                }else if('SubjectID' == $k){
                    if(strpos($v, ',') >= 0){
                        $temp[] = "t.SubjectID IN({$v})";
                    }else{
                        $temp[] = 't.SubjectID='.$v;
                    }
                }else{
                    $temp[] = "`{$k}`='{$v}'";
                }
            }
            $where = implode(' AND ', $temp);
            unset($temp);
        }
        $id = $this->getId();
        if(!empty($id)){
            $where .= ' AND TID='.$id;
        }
        $limit = ($this->pagtion['recordsNum'] * ($this->pagtion['page'] - 1)).','.$this->pagtion['recordsNum'];
        $sql = "SELECT t.*, s.Title as StageTitle, a.AdminName, audit.Status AS Status FROM `zj_originality_template` t LEFT JOIN `zj_originality_stage` s ON s.SID=t.SID LEFT JOIN `zj_originality_audit` audit ON audit.TID=t.TID  LEFT JOIN `zj_admin` a ON a.AdminID=t.Admin WHERE %s ORDER BY s.Order DESC LIMIT {$limit}";
        // exit(sprintf($sql, $where));
        return M()->query(sprintf($sql, $where));
    }

    /**
     * 添加组卷模板的数据到该模板中 
     * @param array $content
     * @param string $tplName 模板名称
     * @param string $tplType 试卷类型
     * @return boolean
     * @author demo 
     */
    public function save($content, $tplName, $tplType){
        //获取期数id
        $os = new OriginalityStageModel();
        $stage = $os->getCurrentStage();
        if(empty($stage)){
            $this->setErrorMessage('30509');
            return false;
        }
        $stage = $stage['SID'];
        //检查该模板是否已经存在
        $tpl = $this->findData('TID', "SID={$stage} AND SubjectID={$content['SubjectID']}");
        $tid = empty($tpl) ? 0 : $tpl['TID'];
        $isAppend = ($tid === 0); //是否是插入操作
        if(!$isAppend){
            //此处暂时取消修改操作
            $this->setErrorMessage('22010');//该原创模板已存在，且禁止修改！
            return false;
        }
        $list = array_merge($content[0], $content[1]);
        unset($content[0], $content[1]);
        $templateData['SubjectID'] = $content['SubjectID'];
        $templateData['DocType'] = $tplType;
        $templateData['SID'] = $stage;
        $templateData['Admin'] = $content['Admin'];
        $templateData['Title'] = $tplName;
        $result = true;
        if($isAppend){
            $templateData['AddTime'] = time();
            $result = $this->insert($templateData);
            $tid = $this->getId(); //获取插入id
        }else{
            $templateData['ModifiedTime'] = time();
            $result = $this->update($templateData);
        }
        unset($templateData, $content);
        if($result === false){
            $this->setErrorMessage('30307');
            return false;
        }
        //写入模板试题数据
        $ott = new OriginalityTemplateTestModel();
        $result = $ott->save($list, $tid, $isAppend);
        if($result === false){
            $this->setErrorMessage($ott->getErrorMessage());
            return false;
        }
        return true;
    }

    /**
     * 根据期次或者学科id查询模板数据
     * @param int $stageId
     * @param int $subjectId
     * @return array
     */
    public function getTemplates($stageId, $subjectId=''){
        $where = 'SID='.(int)$stageId;
        if(!empty($subjectId)){
            $where .= ' AND SubjectID='.$subjectId;
        }
        return $this->select(
            'TID,SubjectID,DocType,Admin,ModifiedTime,AddTime', 
            $where, 
            'TID DESC'
        );
    }

    /**
     * 检查所有试题是否已经选题
     * @return boolean|array 如果所有试题已经选题，将返回true，或者返回模板试题列表
     * @author demo 
     */
    public function isFinal(){
        $ott = new OriginalityTemplateTestModel();
        $data = $ott->getTplTestById($this->getId(), 'TTID');
        if(empty($data)){
            return false;
        }
        $list = array();
        foreach($data as $value){
            $list[] = (int)$value['TTID'];
        }
        $ort = new OriginalityRelateTestModel();
        $data = $ort->getSelectedTestByIdList($list, 'TTID');
        $selectedList = array();
        foreach($data as $value){
            $selectedList[] = (int)$value['TTID'];
        }
        $result = array_diff($list, $selectedList);
        if(count($result) === 0)
            return true; //如果结果为0，则证明所有试题已经选过题
        //当试题选题满足百分之50的时候，也可以通过选题验证
        $list = floor(count($list) * 0.5);
        if(count($selectedList) >= $list){
            return true;
        }
        return $result;
    }

    /**
     * 验证改模板是否还能添加试题
     * @return boolean 能返回true
     * @author demo 
     */
    public function isAllow(){
        $sql = "SELECT a.Status,s.EndTime FROM `zj_originality_template` t LEFT JOIN `zj_originality_audit` a ON a.TID=t.TID LEFT JOIN `zj_originality_stage` s ON t.SID=s.SID WHERE t.TID=".$this->getId();
        $result = M()->query($sql);
        if(empty($result)){
            return true;
        }
        $result = $result[0];
        return ((int)$result['Status'] < 1 && time() < $result['EndTime']);
    }
}