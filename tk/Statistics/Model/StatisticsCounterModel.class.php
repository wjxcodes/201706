<?php
/**
 * 统计计数器
 * @author demo 2015-12-16
 */
namespace Statistics\Model;
use Common\Model\BaseModel;
class StatisticsCounterModel extends BaseModel{

    /**
     * 统计参数，统计数据必须在此数组中
     */
    public $rules = array(
        'testNum' => 'getTestNum',
        'selfTestNum' => 'getSelfTestNum',
        'zujuanNum' => 'getZujuanNum',
        'shijuanNum' => 'getShijuanNum',
        'classNum' => 'getClassNum',
        'homeWorkNum' => 'getHomeWorkNum',
        'caseDownNum' => 'getCaseDownNum',
        'caseHomeWorkNum' => 'getCaseHomeWorkNum',
        'studentAnswerNum' => 'getStudentAnswerNum',
        'schoolNum' => 'getSchoolNum',
        'teacherNum' => 'getTeacherNum',
        'studentNum' => 'getStudentNum',
        'moneyTotal' => 'getUserLucreList',
    );

    /**
     * 填充数据到记录中
     * @返回结果集
     * @author demo 
     */
    public function preset(){
        $result = array();
        foreach($this->rules as $key=>$value){
            $arr = $this->presetAnRecord($key, $value);
            if(!empty($arr)){
                $result += $arr;
            }
        }
        return $result;
    }

    /**
     * 填充单条数据到记录中
     * @param string $key $this->rules中的有效键
     * @param string $value $this->rules中对应$key的有效值
     * @return array
     * @author demo 
     */
    public function presetAnRecord($key, $value=''){
        //当该值为空时，使用rules的方法
        if(empty($value)){
            $value = $this->rules[$key];
        }
        if($this->isContains($key) && method_exists($this, $value)){
            $val = $this->getValue($key);
            $act = $val >= 0 ? 'update' : 'add';
            $val = (float)$this->$value(); //得出结果
            $this->save($key, $val, $act);
            return array($key=>$val); //返回数据
        }
        return array();
    }

    /**
     * 添加内容
     * @param string $param
     * @return int 返回试题数量，当不是一个有效的参数时(isContains)为false时，返回0, 保存操作失败，返回-1
     * @author demo 
     */
    public function increase($param, $num=1){
        if($this->isContains($param)){
            $val = $this->getValue($param);
            $act = 'add';
            if($val >= 0){
                $act = 'update';
                $val = $val + $num;
            }else{
                $val = $num;
            }
            if($this->save($param, $val, $act) === false){
                return -1;
            }
            return $val;
        }
        return 0;
    }

    /**
     * 返回数据
     * @param array 
     * @author demo 
     */
    public function getCounter($params=array()){
        $params = array_unique($params);
        $where = array();
        foreach($params as $value){
            //不在$this->rules中设置的内容将不会被显示
            if($this->isContains($value))
                $where[] = "'{$value}'";
        }
        if(count($where) == 0){
            return array();
        }
        $where = 'Name IN('.implode(',', $where).')';
        $result = (array)$this->selectData('Name, Count', $where);
        foreach($result as $key=>$value){
            $result[$value['Name']] = $value['Count'];
            unset($result[$key]);
        }
        //计算$result键与$params的差集，然后补全记录
        $key = array_keys($result);
        $diff = array();
        if(count($params) >= count($key)){
            $diff = array_diff($params, $key);
        }else{
            $diff = array_diff($key, $params);
        }
        foreach($diff as $value){
            $arr = $this->presetAnRecord($value);
            if(!empty($arr)){
                $result += $arr;
            }
        }
        return $result;
    }

    /**
     * $val是否存在于rules中
     * @param string $val
     * @return boolean
     * @author demo 
     */
    public function isContains($val){
        return isset($this->rules[$val]);
    }

    /**
     * 保存数据
     * @param string $name
     * @param int $count
     * @param string $act 操作方式
     * @author demo 
     */
    public function save($name, $count=1, $act='add'){
        $data = array(
            'Count'=>$count
        );
        if('update' == $act){
            $this->updateData($data, "Name='{$name}'");
        }else if('add' == $act){
            $data['Name'] = $name;
            $this->insertData($data);
        }
    }

    /**
     * 返回指定参数的数量
     * @param string $name
     * @return int 如果记录存在返回>=0的值，或者返回-1
     * @author demo 
     */
    private function getValue($name){
        $result = $this->findData('Count', "Name='{$name}'");
        if(empty($result)){
            return -1;
        }
        return (float)$result['Count'];
    }

    //--------------------------------以下方法由原StatisticsModel中提出--------------------------------

    /**
     * 获取试题总数
     */
    private function getTestNum(){
        $testNum=$this->getModel('TestAttrReal')->selectCount(
            '1=1',
            'TestID'
        );
        return $testNum;
    }

    /**
     * 获取原创题数量
     */
    private function getSelfTestNum(){
        $count=$this->unionSelect('getTestDocTypeTotal');
        $selfCreate=$count[0]['totalCount']; //原创试题总数
        return $selfCreate;
    }
    /**
     * 获取组卷次数
     */
    private function getZujuanNum(){
        $zujuanNum=$this->getModel('User')->selectData(      //组卷次数
            'sum(ComTimes) as zjNum',
            '1=1'
        );
        return $zujuanNum[0]['zjNum'];
    }

    /**
     * 获取生成试卷份数
     */
    private function getShijuanNum(){
        $shijuanNum=$this->getModel('DocDown')->selectCount(
            '1=1',
            'DownID'
        );
        return $shijuanNum;
    }

    /**
     * 获取创建班级数
     */
    private function getClassNum(){
        //创建班级总数
        $createClass=$this->getModel('ClassList')->selectCount(
            '1=1',
            'ClassID'
        );
        return $createClass;
    }

    /**
     * 获取布置作业次数
     */
    private function getHomeWorkNum(){
        //增加缓存
        $time = S('indexHomeWorkNum')['time'];
        if($time!=date('YmdHi',time())){
            //布置作业总数
            $homeWorkTotal=$this->getModel('UserWorkUser')->selectCount(
                '1=1',
                'WTUID'
            );
            $homeWorkTotal+=$this->unionSelect('userWorkClassCountUserID');
            $homeWorkTotal&&S('indexHomeWorkNum',['data'=>$homeWorkTotal,'time'=>date('YmdHi',time())]);
        }
        return S('indexHomeWorkNum')['data'];
    }

    /**
     * 导学案下载次数
     */
    private function getCaseDownNum(){
        $caseDownTotal=$this->getModel('DocDown')->selectCount(       //导学案下载次数
            'DownStyle=3',
            'DownID'
        );
        return $caseDownTotal;
    }

    /**
     * 导学案推送课堂次数
     */
    private function getCaseHomeWorkNum(){
        $caseHomeWork=$this->getModel('CaseTpl')->selectCount(        //导学案推送至课堂
            'IfSystem=2',
            'TplID'
        );
        return $caseHomeWork;
    }

    /**
     * 获取答题数量
     */
    private function getStudentAnswerNum(){
        $studentAnswerNum=$this->getModel('UserAnswerRecord')->selectCount(    //学生答题数量
            '1=1',
            'AnswerID'
        );
        return $studentAnswerNum;
    }

    
    /**
     * 开通学校
     */
    private function getSchoolNum(){
        $schoolNum=$this->getModel('UserIp')->selectCount(           //覆盖学校
            '1=1',
            'IPID'
        );
        return $schoolNum;
    }

    /**
     * 注册教师数量
     */
    private function getTeacherNum(){
        $teacherNum=$this->getModel('User')->selectCount(          //注册教师数量
            'Whois=1',
            'UserID'
        );
        return $teacherNum;
    }

    /**
     * 注册学生数量
     */
    private function getStudentNum(){
        $studentNum=$this->getModel('User')->selectCount(          //注册学生数量
            'Whois=0',
            'UserID'
        );
        return $studentNum;
    }


    /**
     * 获取奖金总额
     */
    private function getMoneyNum(){
        $userMoneyNum=$this->getModel('User')->selectData(
            'SUM(Cz) as moneyTotal',
            '1=1'
        );
        return $userMoneyNum[0]['moneyTotal'];
    }


    /**
     * 用户收益统计
     * @author demo 
     */
    private function getUserLucreList(){
        $total=$this->getModel('Pay')->selectData(
            'sum(PayMoney) as total',
            '1=1'
        );
        return round($total[0]['total'], 0); //四舍五入
    }
}