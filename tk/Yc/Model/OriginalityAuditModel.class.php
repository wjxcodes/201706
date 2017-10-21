<?php
/**
 * 原创模板审核model
 * @author demo 2015-9-7
 */
namespace Yc\Model;
class OriginalityAuditModel extends OriginalityTplBaseModel{
    /**
     * 实现父类抽象方法
     */
    public function getCondition($type=''){
        return 'AID='.$this->getId();
    }

    /**
     * 返回后台管理相关数据
     * @param array $params 查询参数
     * @return array 分页信息及总页数
     * @author demo 
     */
    public function getListByPagtion($params){
        $where = $params;
        $temp = array();
        foreach($where as $k=>$v){
            if(!empty($v)){
                if($k == 'TID'){
                    $temp[] = "t.TID={$v}";
                }else if('SubjectID' == $k){
                    $temp[] = "t.SubjectID IN({$v})";
                }else if('UserID' == $k){
                    $temp[] = "a.UserID='{$v}'";
                }else{
                    $temp[] = "`{$k}`='{$v}'";
                }
            }
        }
        $where = $temp;
        unset($temp);
        $count = $this->unionSelect('countOriginalityAuditList', $where);
        if(empty($count)){
            $count = 0;
        }else{
            $count = $count['num'];
        }
        $this->pagtion['page'] = page($count, $this->pagtion['page'], $this->pagtion['recordsNum']);
        return array($this->getList($where), $count);
    }

    /**
     * 返回分页数据
     * @param string $where
     * @return array
     * @author demo 
     */
    public function getList($where=''){
        if(empty($where)){
            $where = '1=1';
        }else if(is_array($where)){
            $where = implode(' AND ', $where);
            unset($temp);
        }
        $limit = ($this->pagtion['recordsNum'] * ($this->pagtion['page'] - 1)).','.$this->pagtion['recordsNum'];
        // $sql = "SELECT a.*,u.UserName, t.Title, t.SubjectID, s.SID FROM `zj_originality_audit` a LEFT JOIN `zj_originality_template` t ON t.TID=a.TID LEFT JOIN `zj_originality_stage` s ON s.SID=t.SID LEFT JOIN `zj_user` u ON u.UserID=a.UserID WHERE %s ORDER BY a.TID DESC LIMIT {$limit}";
        // exit(sprintf($sql, $where));
       return $this->unionSelect('getOriginalityAuditList',$where,$limit);
        //$result = M()->query(sprintf($sql, $where));
        //return $result;
    }

    /**
     * 通过审核
     * @return boolean
     * @author demo 
     */
    public function pass(){
        return $this->update(array(
            'Status' => 3
        ));
    }

    /**
     * 根据模板id返回当前审核状态
     * @param int $tid 原创模板id
     * @return int
     * @author demo 
     */
    public function getStatusByTemplateId($tid){
        $result = $this->findData('Status', 'TID='.$tid);
        if(empty($result)){
            return -1; //无数据
        }
        return $result['Status'];
    }

    /**
     * 验证审核任务是否已经添加
     * @param int $tid
     * @return boolean
     * @author demo 
     */
    public function isCreate($tid){
        $result = $this->findData('AID,Status', "TID={$tid}");
        if(empty($result)){
            return false;
        }
        return $result['Status'] >= 1;
    }

    /**
     * 覆盖父类insert方法 
     * @author demo 
     */
    public function insert($data){
        $data['Status'] = 1;
        $data['AddTime'] = time();
        return parent::insert($data);
    }

    /**
     * 返回教师端的统计信息
     * @param int $userid
     * @return array
     * @author demo 
     */
    public function getInfo($userid){
        $result = $this->selectData(
            'Status',
            'UserID='.$userid
        );
        $list = array();
        foreach($result as $value){
            $status = $value['Status'];
            if(3 == $status){
                $status = 2;
            }
            if(array_key_exists($status, $list)){
                $list[$status] = ++$list[$value['Status']];
            }else{
                $list[$status] = 1;
            }
        }
        return $list;
    }
}