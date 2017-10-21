<?php
/**
 * @author demo
 * @date 2014年8月18日
 */
/**
 * 试题纠错模型类，用于处理试题纠错管理相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class CorrectLogModel extends BaseModel{
    public function stat($params=array()){
        $where = '1=1';
        if(!$params['page']){
            $params['page'] = 1;
        }
        if($params['prepage']){
            $params['prepage'] = $params['prepage'];
        }else{
            $params['prepage'] = C('WLN_PERPAGE');
        }
        if($params['username']){
            $where .= ' AND UserName="'.$params['username'].'"';
        }
        if($params['subjectid']){
            $where .= ' AND SubjectID='.$params['subjectid'];
        }
        if($params['begintime']){
            $time = strtotime($params['begintime']);
            if($time)
                $params['begintime'] = $time;
        }else{
            $params['begintime'] = 0;
        }
         if($params['endtime']){
            $time = strtotime($params['endtime']);
            if($time)
                $params['endtime'] = $time;
        }else{
            $params['endtime'] = time();
        }
        $where .= ' AND Ctime BETWEEN '.$params['begintime'].' AND '.$params['endtime'];

        $count = $this->dbConn->table('(SELECT UserName FROM zj_correct_log WHERE '.$where.' GROUP BY UserName) t')
                ->count();
        $limit = page($count, $params['page'], $params['prepage']);
        $limit = (($limit-1)*$params['prepage']).','.$params['prepage'];
        $data = $this->dbConn->table('zj_correct_log l')
            ->field('UserName as username,SUM(IF(IfError=1,1,0)) AS error, SUM(IF(IfAnswer=0, 1, 0)) AS undisposed, SUM(IF(IfAnswer=1, 1, 0)) AS dispose, SubjectID as subjectid')
            ->where($where)
            ->group('UserName')
            ->limit($limit)
            ->select();
        return array(
            'count' => $count,
            'data' => $data,
            'params' => $params
        );
    }
}
