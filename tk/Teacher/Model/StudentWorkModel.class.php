<?php
/**
 * @author demo
 * @date 2014年10月23日
 * @update 2015年1月22日
 */
/**
 * 公式任务管理类
 */
namespace Teacher\Model;
use Common\Model\BaseModel;
class StudentWorkModel extends BaseModel{
    /**
     * 查询有公式编辑权限(IfEq=1)的用户
     * @param string $param 查询参数
     * @return array
     */
    public function getUsers($params){
        extract($params+array('p'=>1,'name'=>'','subject'=>''));
        $limit = 5;
        $user = $this->getModel('User');
        $where = 'IfEq=1 and Whois=1';
        if($name != ''){
            $where .= ' AND UserName=\''.$name.'\'';
        }
        if($subject != ''){
            $where .= ' AND UserName=\''.$subject.'\'';
        }
        $count = $user->getPageNum($where);
        import('ORG.Util.Page'); // 导入分页类
        $pagtion = handlePage('init',$count,$limit);// 实例化分页类 传入总记录数和每页显示的记录数

        $p = page($count,$p,$limit);
        $data = $this->dbConn->pageData(
            'User',
            'UserID,UserName,RealName',
            $where,
            'UserID DESC',
            $p.','.$limit);
        return array(
            'data'=>$data,
            'page'=>$pagtion->show()
        );
    }
    /**
     * 保存数据
     * @param array $data 需保存的数据
     * @param string type 操作类型，增加修改
     * @param boolean | int 当操作类型为add时，成功返回编号，或者返回false
                            当操作类型为edit时，成功返回true，或者返回false
     * @return bool
     */
    public function saveData($data,$type='add'){
        $data['Status'] = 0;
        $data['LastTime'] = time();
        if($type == 'add'){
            $data['AddTime'] = $data['LastTime'];
            unset($data['WorkID']);
            return $this->insertData(
                $data);
        }
        $where = 'WorkID='.$data['WorkID'];
        unset($data['WorkID']);
        return $this->updateData(
            $data,
            $where);
    }
    /**
     * 检查当前任务是否为指定的状态
     * @param string $ids 指定任务的id，多个id使用,区分
     * @param array $status 需检查的状态，默认检查为：0,1
     * @return mixed 是返回true，或者返回错误的数组
     */
    public function isSpecifiesState($ids,$status=array(0,1)){
        $where = 'WorkID IN('.$ids.')';
        $records = $this->dbConn->selectData(
            'StudentWork',
            'WorkID,Status',
            $where);
        $msg = array();
        foreach($records as $iRecord){
            if(!in_array($iRecord['Status'],$status)){
                $msg[] = $iRecord['WorkID'];
            }
        }
        if(!empty($msg))
            return $msg;
        return true;
    }
    /**
     * 删除数据
     * @param string $ids 任务id
     * @param string $username
     * @return mixed 成功返回ture，在验证时，发现无权限的操作，将在最终返回编号数组
     */
    public function deleteRecords($ids, $username){
        $where = 'WorkID IN('.$ids.')';
        $result = $this->dbConn->selectData(
            'StudentWork',
            'WorkID,Admin',
            $where,
            'WorkID desc');
        $msg = array();
        foreach($result as $iResult){
            $id = $iResult['WorkID'];
            if($username != $iResult['Admin']){
                $msg[] = $id;
            }else{
                $where = 'WorkID='.$id;
                $res = $this->deleteData(
                    $where);
                if($res !== false){
                    $this->getModel('StudentWorkList')->deleteRecordsByWorkId($id);
                }
            }
        }
        if(empty($msg))
            return true;
        return $msg;
    }
    /**
     * 更新任务状态
     * @param int $workID
     * @param int $status 状态
     * @param string $content 备注信息       
     * @return boolean 成功返回true
     */
    public function updateStatus($workID,$status,$content=''){
        $where = 'WorkID='.$workID;
        $data['Status'] = $status;
        $data['LastTime'] = time();
        if(!empty($content))
            $data['Content'] = $content;
        $result = $this->updateData(
            $data,
            $where);
        return $result !== false;
    }
    /**
     * 权限验证
     * @param string $userName 用户名
     * @return boolean 有权限将返回true 
     */
    // public function isPower($userName){
    //     return $userName == cookie(C('WLN_TEACHER_USER_AUTH_KEY'));
    // }
    /**
     * 统计前台公式任务信息
     * @param int $userName 用户
     * @return array
     */
    public function frontPageStatInfo($userName){
        $where['UserName'] = $userName;
        $field = 'Status,count(WorkID) as num';
        $result = $this->dbConn->groupData(
            'StudentWork',
            $field,
            $where,
            'Status',
            'Status'
            );
        $data = array();
        foreach($result as $iResult){
            $data[$iResult['Status']] = $iResult['num'];
        }
        unset($result);
        return $data;
    }

    /**
     * 统计明细信息
     * @param string userName 任务接收人用户名
     * @param array $param 请求参数
     * @return array
     */
    public function statisticDetail($userName,$param=array()){
        $status = array('ready'=>0,'audit'=>1,'finish'=>2,'restart'=>3);
        $start = strtotime($param['start']);
        $end = strtotime($param['end']);
        if(!$start){
            $start = strtotime(date('Y-m-d'));
        }
        if(!$end){
            $end = time();
        }
        $time = " AND `work`.AddTime BETWEEN {$start} AND {$end}";
        foreach($status as $i=>$iStatus){
            $doc =$this->dbConn->studentWorkByStatusUserName(
                $iStatus,
                $userName,
                $time);
            $data[$i] = $doc;
        }
        $test = $this->getModel('Test');
        foreach($data as $i=>$iDara){
            $mark = false;
            foreach($iDara as $j=>$jData){
                if(!$mark || $iDara[$j]['DocID'] != $iDara[$j-1]['DocID']){
                    $mark = true;
                    $data[$i][$j]['tn'] = 1; 
                    $data[$i][$j]['Equation'] = count($test->getEquations($jData['Equation']));
                }else if($iDara[$j]['DocID'] == $iDara[$j-1]['DocID']){
                    $index = $j-1;
                    while(empty($data[$i][$index]) && $index >= 0){
                        $index--;
                    }
                    $data[$i][$index]['tn']++;
                    $data[$i][$index]['Equation'] += count($test->getEquations($jData['Equation']));
                    unset($data[$i][$j]);
                }
            }
        }
        return $data;
    }
    /**
     * 使用情况统计
     * @param array $param 查询参数
     * @return array
     */
    public function statistic($param){
        $param = array_intersect_key($param,array(
            'username'=>false,
            'start'=>false,
            'end'=>false));
        extract($param);
        $where = '';
        if($userName){
            $where[] = 'work.UserName=\''.$userName.'\' ';
        }
        $start = strtotime($start);
        $end = strtotime($end);
        $time = 'AddTime';
        if(!$start){
            $start = strtotime(date('Y-m-d'));
        }
        if(!$end){
            $end = time();
        }
        $result = $this->dbConn->studentWorkByTime($time,$start,$end);
        $data = $this->getCount($result);
        unset($result);
        return $data;
    }
    /**
     * 将数组转换为指定规则
     * @param array $arr 需转换的数组
     * @return array
     */
    private function formatArr($arr){
        $data = array();
        $keys = array('s1','s2','s3','s4','w1','w2','w3','w4','e1','e2','e3','e4');
        foreach($arr as $i=>$iArr){
            if(!isset($arr['w1'])){
                $data['w1'] = $data['w2'] = $data['w3'] = $data['w4'] = 0;
                $data['s1'] = $data['s2'] = $data['s3'] = $data['s4'] = 0;
                $data['e1'] = $data['e2'] = $data['e3'] = $data['e4'] = 0;
            }
            if(!in_array($i,$keys)){
                $data[$i] = $iArr; 
            }
        }
        return $data;
    }
    /**
     * 计算试题数量和公式数量的总数
     * @param array $data 提取内容的数组
     * @param int $ii 生成数据的数组索引
     * @return array
    */
    private function getCount($data,$ii=0){
        $size = count($data);
        if($size == 1){
            $data = $this->process($data[0]);
            $data['w'.($data['Status']+1)] = 1;
            return array($data);
        }
        $newData = array();
        foreach($data as $i=>$iData){
            if($i <= $size-2){
                $current = $newData[$ii];
                if(empty($current)){
                    $current = $this->process($iData);
                    $current['w'.($current['Status']+1)] = 1;
                }
                $next = $this->process($data[$i+1]);
                //任务数量自增
                if($current['WorkID'] != $next['WorkID']){
                    $wni = 'w'.($next['Status']+1);
                    if(!$next[$wni]){
                        $next[$wni] = 0;    
                    }
                    $next[$wni]++;
                }
                if($current['DocID'] == $next['DocID'] || $current['UserName'] == $next['UserName']){
                    $newData[$ii] = $this->cumulate($next,$current);
                }else{                  
                    $newData[++$ii] = $next;
                }
                $newData[$ii]['Equation'] = '';
            }else{
                $this->getCount($newData,$ii);
            }
        }
        return $newData;
    }
    /**
     * 处理数组内容
     * @param array $arr 需处理的数组
     * @return array
     */
    private function process($arr){
        $arr = $this->formatArr($arr);
        $index = $arr['Status']+1;
        $nsi = 's'.$index;
        $nei = 'e'.$index;
        $arr[$nsi]++;
        $arr[$nei] = count($this->getModel('Test')->getEquations($arr['Equation']));
        return $arr;
    }
    /**
     * 计算指定值的数量
     * @param array $current 当前的数组
     * @param array $prev 之前的数组
     * @return array
     */
    private function cumulate($current,$prev){
        $keys = array('s1','s2','s3','s4','w1','w2','w3','w4','e1','e2','e3','e4');
        foreach($prev as $i=>$iPrev){
            if(in_array($i,$keys)){
                $current[$i] = $current[$i]+$prev[$i];
            }
        }
        return $current;
    }
}