<?php
/**
 * @author demo
 * @date 2015年2月9日
 */
/**
 * 数据表操作Model类，用于处理数据表操作相关操作
 */
namespace Common\Model;
use Think\Model;
class ApiDbModel extends Model{
    protected $errors = array(); //添加错误描述
    protected $autoCheckFields = false; //不自动检测数据表字段信息
    /**
     * 格式化数据表名称；
     * @param string $table 数据表名称 无需前缀大驼峰
     * @return string
     * @author demo
     */
    public function formatTable($table){
        $table=preg_replace('/[A-Z]/','_\\0',$table);
        $table=substr(strtolower($table),1);
        return C('DB_PREFIX').$table;
    }
    /**
     * 重写框架core/model/save方法，加入错误日志记录
     * @author demo
     */
    public function save($data='',$options=array()){
        $result = parent::save($data,$options);
        $this->addSqlErrorLog($result);
        return $result;
    }
    /**
     * 重写框架core/model/delete方法，加入错误日志记录
     * @author demo
     */
    public function delete($options=array()) {
        $result = parent::delete($options);
        $this->addSqlErrorLog($result);
        return $result;
    }
    /**
     * 重写框架core/model/add方法，加入错误日志记录
     * @author demo
     */
    public function add($data='',$options=array(),$replace=false){
        $result = parent::add($data,$options,$replace);
        $this->addSqlErrorLog($result);
        return $result;
    }
    /**
     * 重写框架core/model/addAll方法，加入错误日志记录
     * @author demo
     */
    public function addAll($dataList,$options=array(),$replace=false){
        $result = parent::addAll($dataList,$options,$replace);
        $this->addSqlErrorLog($result);
        return $result;
    }
    /**
     * 重写框架core/model/select，加入错误日志记录
     * @author demo
     */
    public function select($options=array()){
        $result = parent::select($options);
        $this->addSqlErrorLog($result);
        return $result;
    }
    /**
     * 重写框架core/model/execute方法，加入错误日志记录
     * @author demo
     */
    public function execute($sql,$parse=false){
        $result = parent::execute($sql,$parse);
        $this->addSqlErrorLog($result);
        return $result;
    }
    /**
     * 设置错误信息
     * @param array $params 错误参数
     * @return void
     * @author demo
     */
    public function addErrorLog($params){
        $error = new \Common\Model\LogErrorModel();
        $error->setLine($params);
    }
    /**
     * 包含sql语句设置错误信息
     * @param string|array $errors 错误参数
     * @return $this 当前对象
     * @author demo
     */
    public function setErrors($errors){
        if(is_array($errors)){
            $this->errors = $errors;
        }else{
            $this->errors['description'] = $errors;
        }
        return $this;
    }
    /**
     * 加入sql错误日志记录
     * @param bool $result sql语句运行是否正确
     * @author demo
     */
    public function addSqlErrorLog($result){
        $sql=$this->getLastSql();
        if($sql) setSqlTrace($sql);
        if($result === false){
            $errorStr=$this->getDbError();
            $sqlError=getSqlTrace();
            $this->errors['sql'] =$sqlError. ($sqlError=='' ? '' : '<br/>') .$errorStr;
            $this->addErrorLog($this->errors);
            $showError = C('SHOW_SQL_ERROR');//取值 0 1 2
            if($showError == 1){
                exit('数据操作错误！请联系技术支持人员解决。');
            } elseif($showError == 2){
                echo '<meta charset="UTF-8">';
                exit($this->errors['sql']);
            }
        }
    }
    /******************************************************
     * 通用查询语句
     ******************************************************/
    /**
     * 插入数据；
     * @param string $table 数据表名称
     * @param array $data 插入数据表字段数组
     * @return bool
     * @author demo
     */
    public function insertData($table,$data){
        return $this->table($this->formatTable($table))
            ->data($data)
            ->add();
    }
    /**
     * 批量插入数据；
     * @param string $table 数据表名称
     * @param array $data 插入数据表字段数组
     * @return bool
     * @author demo
     */
    public function addAllData($table,$data){
        return $this->table($this->formatTable($table))->addAll($data);
    }
    /**
     * 更新数据；
     * @param string $table 数据表名称
     * @param array $data 更新数据表字段数组
     * @param array $where 更新条件
     * @return bool
     * @author demo
     */
    public function updateData($table,$data,$where){
        if(empty($data)) {
            $this->addSqlErrorLog(false);//人为添加SQL
            return false;
        }
        return $this->table($this->formatTable($table))
                ->data($data)
                ->where($where)
                ->save();
    }
    /**
     * 更新自加数据
     * @param string $table 数据表名称
     * @param string $value 更改字段
     * @param string $where 关联条件
     * @return array 标签内容
     * @author demo
     */
    public function conAddData($table,$value,$where){
        if(!$value || !$where) return false;
        //左值，右值，次数，组卷次数,学生数，充值，点击次数，返回次数，编辑次数,用户分成金额,用户经验值,作业批改数目
        $pergPrev=array(
            '/Lft=Lft/',
            '/Rgt=Rgt/',
            '/Times=Times/',
            '/ComTimes=ComTimes/',
            '/StudentCount=StudentCount/',
            '/Cz=Cz/',
            '/Points=Points/',
            '/Hits=Hits/',
            '/BackTimes=BackTimes/',
            '/EditTimes=EditTimes/',
            '/LucreNum=LucreNum/',
            '/ExpNum=ExpNum/',
            '/CorrectNum=CorrectNum/',
            '/uploadTimes=uploadTimes',
        );
        $pergNext=array('','','','','','','','','','','','','','');
        $result=preg_replace($pergPrev, $pergNext, $value);
        if(strlen($result)==strlen($value)) return false;
        return $this->execute('UPDATE '.$this->formatTable($table).' SET '.$value.' WHERE '.$where);
    }
    /**
     * 删除数据；
     * @param string $table 数据表名称
     * @param array $where 删除条件
     * @return bool
     * @author demo
     */
    public function deleteData($table,$where){
        if(empty($where)) return false;
        return $this->table($this->formatTable($table))
            ->where($where)
            ->delete();
    }
    /**
     * 按条件查询数据；
     * @param string $table 数据表名称
     * @param string $field 查询的字段
     * @param string $where 查询的条件
     * @param string $order 查询的排序
     * @param string $limit 查询的数量
     * @return array
     * @author demo
     */
    public function selectData($table,$field,$where,$order='',$limit=''){
        return $this->table($this->formatTable($table))
            ->field($field)
            ->where($where)
            ->order($order)
            ->limit($limit)
            ->select();
    }
    /**
     * 按条件查询数据；
     * @param string $table 数据表名称
     * @param string $field 查询的字段
     * @param string $where 查询的条件
     * @param string $group 分组条件
     * @param string $order 查询的排序
     * @param string $limit 查询的数量
     * @return array
     * @author demo
     */
    public function groupData($table,$field,$where,$group='',$order='',$limit=''){
        return $this->table($this->formatTable($table))
            ->field($field)
            ->where($where)
            ->group($group)
            ->order($order)
            ->limit($limit)
            ->select();
    }
    /**
     * 按条件精准查询数据(只查询一条数据)；
     * @param string $table 数据表名称
     * @param string $field 查询的字段
     * @param string $where 查询的条件
     * @param string $order 排序规则
     * @return array
     * @author demo
     */
    public function findData($table,$field,$where,$order){
        return $this->table($this->formatTable($table))
            ->field($field)
            ->where($where)
            ->order($order)
            ->find();
    }
    /**
     * 按条件查询总数；
     * @param string $table 数据表名称
     * @param string $where 查询的条件
     * @param string $field 聚合字段
     * @param string $rename 数据表重命名
     * @return int 数量
     * @author demo
     */
    public function selectCount($table,$where,$field='',$rename=''){
        return $this->table($this->formatTable($table).' '.$rename)
            ->where($where)
            ->count($field);
    }
    /**
     * 按条件分页列表
     * @param string $table 数据表名称
     * @param string $field 所需字段
     * @param string $where 关联条件
     * @param string $order 排序
     * @param int $page  当前页码,每页个数
     * @return array 查询内容
     * @author demo
     */
    public function pageData($table,$field,$where,$order,$page){
        return $this->table($this->formatTable($table))
            ->field($field)
            ->where($where)
            ->order($order)
            ->page($page)
            ->select();
    }
    /**
     * 对单一字段进行唯一查询
     * @param string $table 数据表名称
     * @param string $field 所需字段
     * @param string $where 关联条件
     * @return array 标签内容
     * @author demo
     */
    public function distinctData($table,$field,$where='',$order='',$limit=''){
        return $this->table($this->formatTable($table))
            ->Distinct(true)
            ->field($field)
            ->where($where)
            ->order($order)
            ->limit($limit)
            ->select();
    }
    /**
     * 关联插入数据库
     * @param string $table 数据表1名称
     * @param string $value='' 数据表1插入字段
     * @param string $field 表2所需字段
     * @param string $table2 数据表2名称
     * @param string $testID 试题ID
     * @return array
     * @author demo
     */
    public function insertSelect($table,$value='',$field,$table2,$testID){
        if($value){
            $insert='('.$value.')';
        }
        return $this->execute('INSERT INTO '.$this->formatTable($table).' '.$insert.' SELECT '.$field.' FROM '.$this->formatTable($table2).' WHERE TestID in ('.$testID.')');
    }
    /**
     * 获取最大的数据
     * @param string $table 数据表名称
     * @param string $field 查询的字段
     * @return int|bool
     * @author demo
     */
    public function maxData($table,$field){
        return $this->table($this->formatTable($table))->max($field); //得到最大数值
    }
    /**
     * 查询字段总数；
     * @param string $table 数据表名称
     * @param string $field 查询的字段
     * @param string $where 查询条件
     * @return int|bool
     * @author demo
     */
    public function sumData($table,$field,$where){
        return $this->table($this->formatTable($table))->where($where)->sum($field);
    }
    /**
     * 根据学科获取平均数
     * @param string $table 数据表名称
     * @param string $where 查询条件
     * @param string $field 查询平均字段
     * @return int
     * @author demo
     */
    public function avgData($table,$field,$where){
        //排除时间和分数是0的
        return $this->table($this->formatTable($table))->where($where)->avg($field);
    }


    /**
     * 去重连接查询
     * @param string $tableName 表名
     * @param array $joinTable 连接表名
     * @param string $field='*' 字段
     * @param array|string $where 条件
     * @return array
     * @author demo
     */
    public function getDuplicateTestJoinQuery($tableName, $joinTable, $field='*', $where=array()){
        $this->table($this->formatTable($tableName).' a');
        $this->join('LEFT JOIN '.$this->formatTable($joinTable).' t ON t.TestID=a.TestID');
        $this->field($field);
        $this->where($where);
        return $this->select();
    }
    /******************************************************
     * TestDoc 该模块下两条SQL需要优化！！！！！！！！！！！
     ******************************************************/
    /**
     * 根据Doc表主键id，从入库试题中提取公式
     * @param int $docId 文档ID
     * @return array
     * @author demo
     */
    public function TestDocRealSelectByDocID($docId){
             return $this->field('tar.TestID')
                ->table($this->formatTable('Doc').' d,'.$this->formatTable('TestDoc').' td,'.$this->formatTable('TestAttrReal').' tar')
                ->where('d.DocID=tar.DocID and d.DocID='.$docId.' AND td.TestID=tar.TestID')
                ->order('tar.TestID asc')
                ->select();
    }
    /**
     * 根据Doc表主键id，从入库试题中提取公式
     * @param int $docId 文档ID
     * @return array
     * @author demo
     */
    public function TestDocSelectByDocID($docId){
        return $this->field('tar.TestID')
            ->table($this->formatTable('Doc').' d,'.$this->formatTable('TestDoc').' td,'.$this->formatTable('TestAttr').' ta')
            ->where('d.DocID=ta.DocID and d.DocID='.$docId.' AND td.TestID=ta.TestID')
            ->order('tar.TestID asc')
            ->select();
    }
    /**
     * 根据字段条件查询试题下载数据
     * @param int $docId 文档ID
     * @return array
     * @author demo
     */
    public function testDocRealForDown($field,$where){
        return $this->field($field)
            ->table($this->formatTable('TestAttrReal').' tar')
            ->join('LEFT JOIN '.$this->formatTable('TestDoc').' td on td.TestID=tar.TestID')
            ->where($where)
            ->order('tar.TestID asc')
            ->select();
    }

    /**
     * 专题统计
     * @author demo
     */
    public function getTestDocTypeTotal(){
        return $this->field('count(a.TestID) as totalCount')
                ->table($this->formatTable('TestAttrReal').' a')
                ->join('LEFT JOIN '.$this->formatTable('Doc').' b on a.DocID=b.DocID')
                ->where('b.TypeID=3')
                ->select();
    }

    /******************************************************
     * Admin
     ******************************************************/
    /**
     * 获取指定管理员对应的权限；
     * @param string $adminName 用户名
     * @return array
     * @author demo
     */
    public function adminSelectPower($adminName){
        return $this->field('a.*,p.ListID')
            ->table($this->formatTable('Admin').' a')
            ->join('LEFT JOIN '.$this->formatTable('PowerAdmin').' p ON a.GroupID=p.PUID')
            ->where('a.AdminName="' . $adminName . '" AND a.Status=0')
            ->limit(1)
            ->select();
    }
    /**
     * 分页查询,管理员表关联管理组表；
     * @param string $where 查询的条件
     * @param string $order 排序
     * @param int $page 页数
     * @return array
     * @author demo,
     */
    public function adminSelectByPage($where,$order,$page){
        return $this->table($this->formatTable('Admin').' a')
            ->join('LEFT JOIN '.$this->formatTable('PowerAdmin').' g ON a.GroupID=g.PUID')
            ->where($where)
            ->order($order)
            ->page($page)
            ->select();
    }
    /******************************************************
     * ClassList 年级模块
     ******************************************************/
    /**
     * 获取班级总数
     * @param string $where 查询的条件
     * @return array
     * @author demo
     */
    public function classListCount($where){
        $count = $this->field("cl.*,s.*")
            ->table($this->formatTable('ClassList').' cl')
            ->join('LEFT JOIN '.$this->formatTable('School').' s ON s.SchoolID=cl.SchoolID')
            ->where($where)
            ->count(); // 查询满足要求的总记录数
        return $count;
    }
    /**
     * 获取班级分页数据
     * @param string $where 查询的条件
     * @param int $page  当前页码,每页个数
     * @return array
     * @author demo
     */
    public function classListPage($where,$page){
        $list = $this->field("cl.*,s.*")
            ->table($this->formatTable('ClassList').' cl')
            ->join('LEFT JOIN '.$this->formatTable('School').' s ON s.SchoolID=cl.SchoolID')
            ->where($where)
            ->order('cl.ClassID DESC')
            ->page($page)
            ->select();
        return $list;
    }
    /**
     * 根据班级ID查询班级成员相关信息
     * @param int $classID 班级ID
     * @return array
     * @author demo
     */
    public function classListSelectByClassId($classID){
        return $this->field('cl.ClassID,cl.ClassName,cl.Creator,cl.LoadTime,cl.OrderNum,cl.SchoolID,cl.GradeID,u.UserID,
        u.UserName,cu.SubjectID,u.RealName,u.Whois,cl.SchoolFullName AS SchoolName,u.UserPic')
            ->table($this->formatTable('ClassList').' AS cl')
            ->join('INNER JOIN '.$this->formatTable('ClassUser').' AS cu ON cl.ClassID=cu.ClassID AND cu.Status=0')
            ->join('INNER JOIN '.$this->formatTable('User').' AS u ON cu.UserID=u.UserID')
            ->where(array('cl.ClassID' => $classID))
            ->select();
    }
    /******************************************************
     * ClassUser 年级模块
     ******************************************************/
    /**
     * 班级成员名称
     * @param $where string 查询条件
     * @return array
     * @author demo
     */
    public function classUserSelectByWhere($where){
        return $this->field('u.UserID,u.UserName,u.RealName,u.TmpPwd,u.OrderNum,u.LastTime,c.Status')
                    ->table($this->formatTable('ClassUser').' c')
                    ->join('LEFT JOIN '.$this->formatTable('User').' u on u.UserID=c.UserID')
                    ->where($where)
                    ->order('c.CUID ASC')
                    ->select();
    }
    /**
     * 班级与班级成员信息管理查询
     * @param $where string 查询条件
     * @return array
     * @author demo
     */
    public function classUserSelectBy($where){
        return $this->field('u.ClassID,u.Status,l.ClassName')
            ->table($this->formatTable('ClassUser').' u')
            ->join('LEFT JOIN '.$this->formatTable('ClassList').' l on l.ClassID=u.ClassID')
            ->where($where)
            ->order('u.LoadTime DESC')
            ->select();
    }
    /**
     * 根据班级ID查询成员名称
     * @param $classID string 班级ID
     * @param $whois string 我是谁 教师/学生/家长   1为教师
     * @param $and string 其他查询条件
     * @return array
     * @author demo
     */
    public function classUserSelectById($classID,$whois='1',$and=''){
        return $this->field('u.UserID,u.UserName,u.RealName,u.LastTime,c.LoadTime,c.Status,c.SubjectID')
                    ->table($this->formatTable('ClassUser').' c')
                    ->join('LEFT JOIN '.$this->formatTable('User').' u on u.UserID=c.UserID')
                    ->where('c.ClassID='.$classID.' and u.Whois='.$whois.' and u.Status=0 '.$and)
                    ->order('c.CUID ASC')
                    ->select();
    }

    /**
     * 获取班级教师总数
     * @param $classID string 班级ID
     * @param $whois string 我是谁  1为教师
     * @param $and string 其他查询条件
     * @return int
     * @author demo
     */
    public function getClassTeacherCount($classID,$whois=1,$and=''){
        return $this->table($this->formatTable('ClassUser').' c')
            ->join('LEFT JOIN '.$this->formatTable('User').' u on u.UserID=c.UserID')
            ->where('c.Status=0 and '.'c.ClassID='.$classID.' and u.Whois='.$whois.' and u.Status=0 '.$and)
            ->count();
    }

    /**
     * 根据手机号获取班级名称和班级ID
     * @param $phoneNum 手机号
     * @return mixed
     * @author demo
     */
    public function getClassInfoByPhoneNum($phoneNum){
        return $this->field('cl.ClassID,cl.ClassName,cl.OrderNum,cl.Creator,cl.SchoolFullName')
            ->table($this->formatTable('User').' u')
            ->join('LEFT JOIN '.$this->formatTable('ClassList').' cl on u.UserName=cl.Creator')
            ->where('u.UserName="'.$phoneNum.'" or u.PhoneCode="'.$phoneNum.'"')
            ->select();
    }
    /******************************************************
     * UserWork 班级成员任务
     ******************************************************/
    /**
     * 班级成员任务分页查询
     * @param $classID 对应班级ID
     * @param $subjectID 对应学科ID
     * @param $page 分页条件
     * @param $workType=1 作业类型 1普通作业 2导学案
     * @return array
     * @author demo
     */
    public function userWorkClassSelectPage($classID,$subjectID,$page,$workType=1){
        return $this->field('w.WorkID,c.Status,w.StartTime,w.LoreNum,w.WorkName,w.EndTime,w.WorkStyle,w.WorkOrder,w
        .TestNum,w.LoreNum,w.SubjectID,w.IfDelete,c.WorkToID')
                    ->table($this->formatTable('UserWork').' w')
                    ->join('LEFT JOIN '.$this->formatTable('UserWorkClass').' c on w.WorkID=c.WorkID')
                    ->where('c.ClassID='.$classID.' and w.SubjectID='.$subjectID.' and w.WorkType='.$workType)
                    ->order('w.WorkID DESC')
                    ->page($page)
                    ->select();

    }
    /**
     * 获取作业总数
     * @param  $classID 对应班级ID
     * @param  $subjectID 对应学科ID
     * @param  $workType=1 作业类型 1普通作业 2导学案
     * @return int
     * @author demo
     */
    public function userWorkClassCount($classID,$subjectID,$workType=1){
        return $this->field('w.WorkID')
            ->table($this->formatTable('UserWork').' w')
            ->join('LEFT JOIN '.$this->formatTable('UserWorkClass').' c on w.WorkID=c.WorkID')
            ->where('c.ClassID='.$classID.' and w.SubjectID='.$subjectID.' and w.WorkType='.$workType)
            ->count();
    }
    /**
     * 按用户ID，班级ID统计任务
     * @param int $userID 用户ID
     * @param int $classID 班级ID
     * @return int
     * @author demo
     */
    public function userWorkClassCountData($userID,$classID){
        return $this->table($this->formatTable('UserWorkClass').' AS wtc')
            ->join('LEFT JOIN '.$this->formatTable('UserWorkUser').' AS wtu ON wtc.WorkToID=wtu.WorkToID')
            ->where('(wtu.UserID = '.$userID.' OR wtc.`Status`=0) AND wtc.ClassID='.$classID)
            ->count();
    }
    /******************************************
     *
     * 用户任务获取 userSendWork
     *
     ******************************************/
    /**
     * 根据任务发送ID
     * @param int $sendID 任务发送ID
     * @return array
     * @author demo
     */
    public function userWorkSelectBySendID($sendID){
        return $this->field('uw.UserName as uname,usw.WorkID,usw.ClassID,uw.TestList,uw.WorkName,usw.SubjectID,u.UserID,u.UserName,u.RealName,usw.CorrectRate,usw.Comment,usw.Content,usw.SendTime,usw.DoTime,usw.CheckTime,usw.Status,uw.WorkStyle,uw.StartTime,uw.EndTime,uw.WorkOrder,uw.Message,uw.TestNum')
                    ->table($this->formatTable('UserSendWork').' usw')
                    ->join('LEFT JOIN '.$this->formatTable('UserWork').' uw on uw.WorkID=usw.WorkID')
                    ->join('LEFT JOIN '.$this->formatTable('User').' u on u.UserID=usw.UserID')
                    ->where('usw.SendID='.$sendID)
                    ->select();
    }
    /**
     * 通过索引和数据库查询获取试题测试信息
     * @param $sendId int 作业测试记录ID
     * @return array 整个作业测试的试题信息和作答信息
     * @author demo
     */
    public function userSendWorkSelectById($sendId){
        return $this->field('a.*,b.*')
            ->table($this->formatTable('UserSendWork').' a')
            ->where('a.SendID=' . $sendId)
            ->join('LEFT JOIN '.$this->formatTable('UserWork').' b  ON a.WorkID=b.WorkID')
            ->select();
    }
    /**
     * 查询导学案发布情况总数
     * @param $where string 查询条件
     * @return array
     * @author demo
     */
    public function userSendWorkSelectCount($where){
        return $this->table($this->formatTable('UserSendWork').' AS a')
            ->join('LEFT JOIN '.$this->formatTable('User').' AS b ON a.UserID=b.UserID')
            ->join('LEFT JOIN '.$this->formatTable('ClassList').' AS c ON c.ClassID=a.ClassID')
            ->where($where)
            ->count();
    }
    /**
     * 分页查询导学案发布情况
     * @param int $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function userSendWorkSelectPageByWhere($where,$page){
        $result=$this->field('a.*,b.RealName,c.ClassName,c.SchoolFullName')
            ->table($this->formatTable('UserSendWork').' a')
            ->join('LEFT JOIN '.$this->formatTable('User').' b ON a.UserID=b.UserID')
            ->join('LEFT JOIN '.$this->formatTable('ClassList').' c ON c.ClassID=a.ClassID')
            ->where($where)
            ->order('a.SendID desc')
            ->page($page)
            ->select();
        return $result;
    }

    /*******************************************************
     * 用户答题结果表 UserAnswerRecord
     *******************************************************/
    /**
     * 按条件查询试题类型，答题结果，试题标签
     * @param string $where 搜索条件
     * @return array
     * @author demo
     */
    public function userAnswerRecordSelectByWhere($where){
        return $this->field('AnswerID,a.TestID,AnswerText,a.OrderID,b.IfChoose as TestIfChoose,c.IfChoose as AnswerIfChoose,a.TestType')
            ->table($this->formatTable('UserAnswerRecord').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestAttrReal').' b on a.TestID=b.TestID')
            ->join('LEFT JOIN '.$this->formatTable('TestJudge').' c on a.TestID=c.TestID and a.OrderID=c.OrderID')
            ->where($where)
            ->select();
    }
    /**
     * 按条件字段查询答题回复
     * @param string $field 查询字段
     * @return array
     * @author demo
     */
    public function userAnswerRecordSelectByField($field,$ids){
       return $this->field('AnswerID,a.TestID,IfRight,a.IfChoose,Diff,TestType')
            ->table($this->formatTable('UserAnswerRecord').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestAttrReal').' b on a.TestID=b.TestID')
            ->where([
                $field => ['in', $ids],
                'a.IfChoose' => ['gt', 1]
            ])
            ->order('a.LoadTime ASC')
            ->select();
    }
    /**
     * 获取某用户某学科的可判断正确的题目的答题信息和题目信息 正确否 难度 猜测系数
     * @param string $userName 用户名
     * @param int $subjectID 学科名称
     * @return array
     * @author demo
     */
     public function userAnswerRecordSelectByUserNameSubjectId($userName,$subjectID){
        return $this->field('KlID,a.TestID,IfRight,a.IfChoose,Diff')
                ->table($this->formatTable('UserAnswerRecord').' a')
                ->join('LEFT JOIN '.$this->formatTable('UserTestRecord').'  b on a.TestRecordID=b.TestID')
                ->join('LEFT JOIN '.$this->formatTable('TestAttrReal').'  c on a.TestID=c.TestID')
                ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' d on a.TestID=d.TestID')
                ->where(array(
                'UserName' => $userName,
                'b.SubjectID' => $subjectID,
                'a.IfChoose' => array('gt', 1)
                ))
                ->order('a.TestRecordID desc')
                ->limit(500)
                ->select();
     }
    /**
     *  获取最近500道有效作答 IfRight字段值为1或者2的
     * 用于计算知识点能力值 用于PersonalReport
     * 【重要】相同的题目做了多遍只算最后一次测试的作答情况
     * @param string $join 动态关联条件
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function userAnswerRecordSelectByJoinWhere($join,$where){
        return $this->field('KlID,a.TestID,IfRight,a.IfChoose,Diff')
            ->table($this->formatTable('UserAnswerRecord').' a')
            ->join('LEFT JOIN '.$join)
            ->join('LEFT JOIN '.$this->formatTable('TestAttrReal').' d ON a.TestID=d.TestID')
            ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' e ON a.TestID=e.TestID')
            ->where($where)
            ->order('a.LoadTime ASC')//正序排列的目的是让下面的循环中最新的作答情况覆盖旧的作答情况
            ->limit(500)
            ->select();
    }
    /**
     * 统计用户某学科下错误的试题数量
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @return int
     * @author demo
     */
    public function userAnswerRecordSelectCount($userName,$subjectID){
       return $this->table($this->formatTable('UserAnswerRecord').' AR')
            ->join('INNER JOIN '.$this->formatTable('UserTestRecord').' TR ON AR.TestRecordID=TR.TestID')
            ->where(array('TR.UserName' => $userName,'TR.SubjectID'=>$subjectID,'AR.ifRight'=>'1'))
            ->count('AR.AnswerID');
    }
    /**
     * 根据测试记录TestRecordID或者SendID获取知识点对应的试题和作答情况
     * @param string $field 字段
     * @param int $id 对应ID
     * @return array
     * @author demo
     */
    public function userAnswerRecordSelectByFieldId($field,$id){
        return $this->field('a.TestID,KlID,IfRight')
            ->table($this->formatTable('UserAnswerRecord').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' b on a.TestID=b.TestID')
            ->where([$field => $id])
            ->select();
    }

    /*********************************
     * 教师给班级分配任务 UserWorkToUser
     *********************************/
    /**
     * 按条件查询学生任务
     * @param $where string 查询条件
     * @return array
     * @author demo
     */
    public function userWorkSelectByWhere($where){
        return $this->field('u.UserID,u.UserName,u.RealName')
                                ->table($this->formatTable('UserWorkUser').' uwu')
                                ->join('LEFT JOIN '.$this->formatTable('UserWorkClass').' uwc on uwu.WorkToID=uwc.WorkToID')
                                ->join('LEFT JOIN '.$this->formatTable('ClassUser').' cu on cu.ClassID=uwc.ClassID and cu.UserID=uwu.UserID')
                                ->join('LEFT JOIN '.$this->formatTable('User').' u on u.UserID=uwu.UserID')
                                ->where($where)
                                ->order("cu.LoadTime ASC,cu.CUID ASC")
                                ->select();
    }
    /**
     * 根据任务ID，班级ID查询学生相关信息
     * @param $workID string 任务ID
     * @param $classID string 班级ID
     * @return array
     * @author demo
     */
    public function userWorkSelectByWorkIdClassID($workID,$classID){
        return $this->field('u.UserID,u.UserName,u.RealName')
                                ->table($this->formatTable('ClassUser').' cu')
                                ->join('LEFT JOIN '.$this->formatTable('UserWorkClass').' uwc on cu.ClassID=uwc.ClassID')
                                ->join('LEFT JOIN '.$this->formatTable('User').' u on u.UserID=cu.UserID')
                                ->where('uwc.WorkID='.$workID.' and uwc.ClassID='.$classID.' and u.Whois=0 and cu.Status=0')
                                ->order("cu.LoadTime ASC,cu.CUID ASC")
                                ->select();
    }

    /*******************************************************
     * 班级分配任务 WorkToClass
     *******************************************************/
    /**
     * 班级成员发布作业任务总数
     * @param int $userID 用户ID
     * @return int
     * @author demo
     */
    public function workToClassCountByUserID($userID){
        return  $this->table(''.$this->formatTable('UserWorkClass').' AS wtc')
            ->join('INNER JOIN '.$this->formatTable('ClassUser').' ON wtc.ClassID= '.$this->formatTable('ClassUser').'.ClassID AND '.$this->formatTable('ClassUser').'.UserID='.$userID)
            ->join('LEFT JOIN '.$this->formatTable('UserWorkUser').' AS wtu ON wtc.WorkToID=wtu.WorkToID')
            ->join('LEFT JOIN '.$this->formatTable('UserSendWork').' ON wtc.WorkID='.$this->formatTable('UserSendWork').'.WorkID AND '.$this->formatTable('UserSendWork').'.UserID='.$userID.' AND wtc.ClassID='.$this->formatTable('UserSendWork').'.ClassID')
            ->where('wtu.UserID = '.$userID.' OR wtc.`Status`=0) AND '.$this->formatTable('ClassUser').'.Status=0 AND ('.$this->formatTable('UserSendWork').'.Status IS NULL OR '.$this->formatTable('UserSendWork').'.Status=0')
            ->count();
    }
    /**
     * 根据userID分页查找该用户的作业情况
     * @param $userID int 要查找的用户ID
     * @param $where string 查找条件
     * @param $page string 分页条件
     * @return array
     * @author demo
     */
    public function UserWorkClassPageData($userID,$where,$page){
        return   $this->field('wtc.WorkID,uw.WorkName,uw.WorkType,uw.UserName,wtc.ClassID,uw.WorkStyle,uw.StartTime,uw.EndTime,uw.LoadTime,uw.Message,uw.TestNum,uw.SubjectID,
            '.$this->formatTable('UserSendWork').'.Status,'.$this->formatTable('UserSendWork').'.SendID,uw.StuWorkDown')
            ->table($this->formatTable('UserWorkClass').' AS wtc')
            ->join('INNER JOIN '.$this->formatTable('ClassUser').' ON wtc.ClassID='.$this->formatTable('ClassUser').'.ClassID AND '.$this->formatTable('ClassUser').'.UserID='.$userID)
            ->join('LEFT JOIN '.$this->formatTable('UserWorkUser').' AS wtu ON wtc.WorkToID=wtu.WorkToID')
            ->join('LEFT JOIN '.$this->formatTable('UserSendWork').' ON wtc.WorkID='.$this->formatTable('UserSendWork').'.WorkID AND '.$this->formatTable('UserSendWork').'.UserID='.$userID.' AND wtc.ClassID='.$this->formatTable('UserSendWork').'.ClassID')
            ->join('LEFT JOIN '.$this->formatTable('UserWork').' AS uw ON wtc.WorkID=uw.WorkID')
            ->where($where)
            ->order('uw.LoadTime DESC')
            ->limit($page)
            ->select();
    }
    /**
     * 根据班级ID，作业任务ID，用户ID查询作业信息
     * @param int $classID 班级ID
     * @param int $workID 作业任务ID
     * @param int $userID 用户ID
     * @return array
     * @author demo
     */
    public function userWorkClassSelectByIdArr($classID,$workID,$userID){
        return $this->field('wtc.WorkID')
            ->table($this->formatTable('UserWorkClass').' AS wtc')
            ->join('LEFT JOIN '.$this->formatTable('UserWorkUser').' AS wtu ON wtc.WorkToID=wtu.WorkToID')
            ->where('wtc.ClassID='.$classID.' AND wtc.WorkID='.$workID.' AND (wtc.Status=0 OR wtu.UserID='.$userID.')')
            ->select();
    }
    /**
     * 获取针对班级布置作业的人数
     * @return array
     * @author demo
     */
    public function userWorkClassCountUserID(){
        return $this->table($this->formatTable('UserWorkClass').' c')
            ->join('LEFT JOIN '.$this->formatTable('ClassUser').' u on u.ClassID=c.ClassID')
            ->count('u.UserID');
    }
    /******************************************************
     * UserCollect 用户收藏
     ******************************************************/
    /**
     * 分组查询用户对应的知识点所有收藏
     * @param string $KlList 关联知识点字符串
     * @param string $userName 用户姓名
     * @return array
     * @author demo
     */
    public function userCollectSelectByUserIDKlID($KlList,$userName){
        return $this->field('CollectID')
            ->table($this->formatTable('UserCollect'))
            ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' on '.$this->formatTable('UserCollect').'.TestID='.$this->formatTable('TestKlReal').'.TestID')
            ->where($this->formatTable('TestKlReal').'.KlID in('.$KlList.') AND '.$this->formatTable('UserCollect').'.UserName="'.$userName.'"')
            ->group($this->formatTable('TestKlReal').'.TestID')
            ->select();
    }
    /**
     * 根据学科ID，用户名，知识点统计用户收藏总数
     * @param string $userName 用户名
     * @param int $subjectID 学科名
     * @param string $KlList 知识点字符串
     * @return int
     * @author demo
     */
    public function userCollectSelectCount($userName,$subjectID,$KlList){
        return $this->table($this->formatTable('UserCollect'))
                    ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' on '.$this->formatTable('UserCollect').'.TestID='.$this->formatTable('TestKlReal').'.TestID')
                    ->where('UserName ="'.$userName.'" AND SubjectID ='.$subjectID.' AND '.$this->formatTable('TestKlReal').'.KlID in('.$KlList.')')
                    ->count('DISTINCT '.$this->formatTable('UserCollect').'.TestID');

    }

    /**
     * 根据学科ID，用户名，知识点分页查询用户收藏
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @param string $KlList 知识点字符串
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function userCollectSelectPageData($userName,$subjectID,$KlList,$page){
        return $this->table($this->formatTable('UserCollect'))
            ->field('CollectID,FavName,'.$this->formatTable('UserCollect').'.TestID,LoadTime')
            ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' on '.$this->formatTable('UserCollect').'.TestID='.$this->formatTable('TestKlReal').'.TestID')
            ->where('UserName ="'.$userName.'" AND SubjectID ='.$subjectID.' AND '.$this->formatTable('TestKlReal').'.KlID in('.$KlList.')')
            ->group(''.$this->formatTable('TestKlReal').'.TestID')
            ->order('LoadTime desc')
            ->limit($page)
            ->select();
    }
    /**
     * 获取用户身份及用户收藏的试题，用于通过判断用户身份来改变收藏试题来源
     * @return array
     * @author demo
     */
    public function userCollectSelectMsg(){
        return $this->field('UC.CollectID,U.UserName,U.Whois')
            ->table($this->formatTable('UserCollect').' UC')
            ->join('INNER JOIN '.$this->formatTable('User').' U ON UC.UserName=U.UserName')
            ->limit(0,1000)
            ->select();
    }
    /**
     * 获取用户各个学科下收藏试题ID
     * @return array
     * @author demo
     */
    public function userCollectSelectMsgByWhere(){
        return  $this->field('u.UserName,c.TestID,c.SubjectID')
            ->table($this->formatTable('UserCollect').' c')
            ->join('LEFT JOIN '.$this->formatTable('User').' u ON c.UserName=u.UserName')
            ->where(['u.Whois'=>0])//0 提分用户
            ->limit(0,500)
            ->select();
    }


    /******************************************************
     * UserTestRecord 用户答题
     ******************************************************/
    /**
     * 根据答题记录ID查询该次测试详细内容
     * @param int $recordID 测试ID
     * @return array
     * @author demo
     */
    public function userAnswerSelectByRecordId($recordID){
        return $this->field('TklID,uar.TestID,uar.Number,uar.OrderID,tkr.KlID,KlName,IfRight')
            ->table($this->formatTable('UserAnswerRecord').' AS uar')
            ->join('INNER JOIN '.$this->formatTable('TestKlReal').' AS tkr ON uar.TestID=tkr.TestID')
            ->join('LEFT JOIN '.$this->formatTable('Knowledge').' AS kl on tkr.KlID = kl.KlID')
            ->where('uar.TestRecordID='.$recordID)
            ->select();
    }
    /**
     * 根据用户名，知识点查看用户答题情况
     * @param $userName string 用户名
     * @param $klList string 知识点
     * @return int
     * @author demo
     */
    public function userAnswerRecordGroupByUserNameKlId($userName,$klList){
        $amount=$this->table($this->formatTable('UserAnswerRecord').' uar')
            ->join('LEFT JOIN '.$this->formatTable('UserTestRecord').' AS utr on uar.TestRecordID=utr.TestID')
            ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' AS tkr on uar.TestID=tkr.TestID')
            ->where('utr.UserName ="'.$userName.'" AND utr.Score <> -1 AND tkr.KlID in (\''.str_replace(',','\',\'',$klList).'\') AND uar.IfRight in (-1,1) ')
            ->order('uar.LoadTime desc')
            ->count('DISTINCT uar.TestID');
        return $amount;
    }
    /**
     * 根据用户名，知识点查看用户答题情况
     * @param string $userName 用户名
     * @param string $klList 知识点
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function userAnswerRecordSelectPageData($userName,$klList,$page){
        $result=$this->table($this->formatTable('UserAnswerRecord').' uar')
            ->join('LEFT JOIN '.$this->formatTable('UserTestRecord').' AS utr on uar.TestRecordID=utr.TestID')
            ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' AS tkr on uar.TestID=tkr.TestID')
            ->where('utr.UserName ="'.$userName.'" AND utr.Score <> -1 AND tkr.KlID in (\''.str_replace(',','\',\'',$klList).'\') AND uar.IfRight in (-1,1) ')
            ->field('uar.TestID,AnswerText,IfRight')
            ->group('uar.TestID')
            ->order('uar.LoadTime desc')
            ->limit($page)
            ->select();
        return $result;
    }
    /**
     * 根据用户名，知识点查看用户答题情况
     * @param string $userName 用户名
     * @param string $klList 知识点
     * @return array
     * @author demo
     */
    public function userAnswerRecordSelectByUserNameKlId($userName,$klList){
        $tmpQuery = $this->table($this->formatTable('UserAnswerRecord').' uar')
            ->field('AnswerID,uar.TestID,AnswerText,IfRight')
            ->join('LEFT JOIN '.$this->formatTable('UserTestRecord').' AS utr on uar.TestRecordID=utr.TestID')
            ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' AS tkr on uar.TestID=tkr.TestID')
            ->where('utr.UserName ="'.$userName.'" AND utr.Score <> -1 AND tkr.KlID in (\''.str_replace(',','\',\'',$klList).'\') AND uar.IfRight in (-1,1) ')
            ->order('uar.LoadTime')
            ->select(false);
        //去除重复
        return $this->field('AnswerID')
            ->table($tmpQuery . ' a')
            ->join('LEFT JOIN '.$this->formatTable('TestReal').' AS tr on a.TestID=tr.TestID')
            ->group('a.TestID')
            ->select();
    }

    /******************************************************
     * UserSendWork 成员发布作业
     ******************************************************/
    /**
     * 分页查询用户作业
     * @param int $userID 用户ID
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function userSendWorkSelectPage($userID,$page){
            return $this->field('usw.SendID,CorrectRate,uw.WorkType,Comment,usw.Status,SendTime,DoTime,CheckTime,uw.UserName,WorkStyle,StartTime,EndTime,uw.LoadTime,Message,TestNum,uw.SubjectID,ClassName,uw.StuWorkDown,uw.WorkName')
                ->table($this->formatTable('UserSendWork').' AS usw')
                ->join('LEFT JOIN '.$this->formatTable('UserWork').' AS uw ON usw.WorkID=uw.WorkID')
                ->join('LEFT JOIN '.$this->formatTable('ClassList').' AS cl ON usw.ClassID=cl.ClassID')
                ->where('usw.UserID='.$userID.' AND usw.Status!=0')
                ->order('usw.SendTime DESC')
                ->limit($page)
                ->select();
    }
    /******************************************************
     * DynamicTo 班级动态
     ******************************************************/
    /**
     * 根据学科类型，班级ID，用户ID查询班级动态
     * @param $objType string 班级动态标识(配置文件中的值)
     * @param $userID string 用户ID
     * @param $classID  string 班级ID
     * @param $page string 分页条件
     * @return array
     * @author demo
     */
    public function dynameicToSelectBy($objType,$userID,$classID,$page){
       return $this->field('d.Content,d.LoadTime')
                    ->table($this->formatTable('DynamicTo').' t')
                    ->join('LEFT JOIN '.$this->formatTable('Dynamic').' d on t.DynamicID=d.DynamicID and d.ObjType="' . $objType . '"')
                    ->where('t.UserID=' . $userID . ' and d.ObjID=' . $classID)
                    ->page($page)
                    ->order('t.DynamicToID desc')
                    ->select();
    }
    /**
     * 根据用户ID，学科类型，查询满足条件的总数
     * @param string $userID 用户ID
     * @param string $objType 学科类型
     * @return int
     * @author demo
     */
    public function dynamicToCount($userID,$objType){
        return $this->join('INNER JOIN '.$this->formatTable('DynamicTo').' dt ON d.DynamicID=dt.DynamicID AND dt.UserID='.$userID)
                    ->where('ObjType = "'.$objType.'"')
                    ->table($this->formatTable('Dynamic').' d')
                    ->count();
    }
    /**
     * 根据用户ID分页查询班级动态相关信息
     * @param string $userID 用户ID
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function dynamicToPageData($userID,$where,$page){
        return  $this->field('dt.UserID,d.Content,d.LoadTime')
                    ->join('INNER JOIN '.$this->formatTable('DynamicTo').' dt ON d.DynamicID=dt.DynamicID AND dt.UserID='.$userID)
                    ->where($where)
                    ->limit($page)
                    ->order('d.LoadTime DESC')
                    ->table($this->formatTable('Dynamic').' d')
                    ->select();
    }

    /******************************************************
     * ClassUser 班级成员
     ******************************************************/
    /**
     * 查询班级用户数据总数；
     * @param string $where 查询的条件
     * @return int
     * @author demo
     */
    public function classUserCount($where){
        $count=$this->field('cu.CUID')
            ->table($this->formatTable('ClassUser').' cu')
            ->join('LEFT JOIN '.$this->formatTable('User').' u ON cu.UserID=u.UserID')
            ->join('LEFT JOIN '.$this->formatTable('ClassList').' cl ON cu.ClassID=cl.ClassID')
            ->join('LEFT JOIN '.$this->formatTable('Subject').' s ON s.SubjectID=cu.SubjectID')
            ->join('LEFT JOIN '.$this->formatTable('School').' sc ON sc.SchoolID=cl.SchoolID')
            ->where($where)
            ->count();
        return $count;
    }
    /**
     * 查询班级用户数据看列表；
     * @param string $where 查询的条件
     * @param int $page  当前页码,每页个数
     * @return array
     * @author demo
     */
    public function classUserPage($where,$page){
        return $this->field('s.SubjectName,cl.*,sc.*,u.UserName,u.Whois,u.Email,u.RealName,cu.*')
            ->table($this->formatTable('ClassUser').' cu')
            ->join('LEFT JOIN '.$this->formatTable('User').' u ON cu.UserID=u.UserID')
            ->join('LEFT JOIN '.$this->formatTable('ClassList').' cl ON cu.ClassID=cl.ClassID')
            ->join('LEFT JOIN '.$this->formatTable('Subject').' s ON s.SubjectID=cu.SubjectID')
            ->join('LEFT JOIN '.$this->formatTable('School').' sc ON sc.SchoolID=cl.SchoolID')
            ->where($where)
            ->page($page)
            ->order('cu.CUID DESC')
            ->select();
    }
    /**
     * 根据班级用户主键CUID获取用户名；
     * @param int $CUID 主键id
     * @return string
     * @author demo
     */
    public function classUserByID($CUID){
        return $this->field('u.username')
            ->table($this->formatTable('ClassUser').' cu')
            ->join('LEFT JOIN '.$this->formatTable('User').' u ON u.UserID=cu.UserID')
            ->where('cu.CUID='.$CUID)
            ->find();
    }
    /**
     * 获取指定用户ID的班级列表
     * @param int $id 用户ID
     * @return array|bool 班级列表数组或者false错误
     * @author demo
     */
    public function classListByUserID($id){
        $where=['UserID'=>$id,'Status'=>0];
        return $this->field('u.ClassID,u.Status,l.ClassName')
            ->table($this->formatTable('ClassUser').' u')
            ->join('LEFT JOIN '.$this->formatTable('ClassList').' l ON l.ClassID=u.ClassID')
            ->where($where)
            ->order('u.LoadTime DESC')
            ->select();
    }
    /**
     * 获取指定班级ID的学生列表
     * @param int $cid 班级ID
     * @return array|bool 学生列表数组或者false错误
     * @author demo
     */
    public function classUserByClassID($cid){
        $where =['ClassID'=> $cid ,
            'Whois'=>0 ,
            'u.Status'=>0
        ];
        return $this->field('u.UserID,u.UserName,u.RealName,u.OrderNum,u.LastTime')
            ->table($this->formatTable('ClassUser').' c')
            ->join('LEFT JOIN '.$this->formatTable('User').' u ON u.UserID=c.UserID')
            ->where($where)
            ->order('c.CUID ASC')
            ->select();
    }
    /******************************************************
     * Test
     ******************************************************/
    /**
     * 根据评论id获取入库试题及评论
     * @param int $ID 评论id
     * @return array
     * @author demo
     */
    public function testMessageByID($ID){
        return $this->field('a.*,r.OptionWidth,r.OptionNum,r.TestNum,r.IfChoose,t.Test,t.Analytic,t.Answer')
            ->table($this->formatTable('Message').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestReal').' t ON a.TestID=t.TestID')
            ->join('LEFT JOIN '.$this->formatTable('TestAttrReal').' r ON r.TestID=t.TestID')
            ->where('ID='.$ID)
            ->select();
    }
    /******************************************************
     * TestReal
     ******************************************************/
    /**
     * 根据limit值获取某学科下的入库试题的zj_test_real表和zj_testdoc表的数据（题文、答案、解析）
     * @param int $subjectID 学科ID
     * @param string $limit 限制条数
     * @return arrary
     * @author demo
     */
    public function testRealSelectBySubjectIdLimit($subjectID,$limit){
        return $this->field('TR.TestID,TR.Test,TR.Analytic,TR.Answer,TR.Remark,TD.DocTest,TD.DocAnalytic,TD.DocAnswer,TD.DocRemark')
                    ->table($this->formatTable('TestReal').' TR')
                    ->join('INNER JOIN '.$this->formatTable('TestAttrReal').' AR ON TR.TestID=AR.TestID AND AR.SubjectID='.$subjectID)
                    ->join('INNER JOIN '.$this->formatTable('TestDoc').' TD ON AR.TestID=TD.TestID')
                    ->limit($limit)
                    ->order('TR.TestID ASC')
                    ->select();
    }
    /**
     * 获取指定testID的试题数据，包括zj_test_real和zj_testdoc表中的题文、答案、解析
     * @param array $testIDs 需要查找的TestIDs
     * @return array
     * @author demo
     */
    public function testRealSelectByTestIDs($testIDs){
         return $this->field('TR.TestID,TR.Test,TR.Analytic,TR.Answer,TR.Remark,TD.DocTest,TD.DocAnalytic,TD.DocAnswer,TD.DocRemark')
             ->table($this->formatTable('TestReal').' TR')
             ->join('INNER JOIN '.$this->formatTable('TestDoc').' TD ON TR.TestID=TD.TestID')
            ->where(array('TR.TestID'=>array('in',$testIDs)))
            ->order('TR.TestID ASC')
            ->select();
    }
    /**
     * 按题号及学科统计试题数量
     * @param int $testID 试题ID
     * @param int $subjectID 学科ID
     * @return int
     * @author demo
     */
    public function testRealCountByTestIdSubjectId($testID,$subjectID){
        return $this->table($this->formatTable('TestReal').' TR')
            ->join('LEFT JOIN '.$this->formatTable('TestAttrReal').' TA ON TR.TestID=TA.TestID')
            ->where('TR.TestID<='.$testID.' AND TA.SubjectID='.$subjectID)
            ->count('TR.TestID');
    }
    /**
     * 按条件搜索入库试题相关信息
     * @param string $where 搜索条件
     * @return array
     * @author demo
     */
    public function testRealSelectByWhere($where){
        return $this->field('a.TestID,a.Test,a.Answer,a.Analytic,a.Remark,t.SubjectID')
            ->table($this->formatTable('TestReal').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestAttrReal').' t on a.TestID=t.TestID')
            ->where($where)
            ->select();
    }
    /**
     * 根据试题id查找入库试题属性
     * @param int $ID 试题id
     * @return array
     * @author demo
     */
    public function testRealByID($ID){
        return $this->field('a.*,t.TypesID,t.SpecialID,t.SubjectID,t.IfChoose,t.Diff,t.DfStyle,t.Mark,t.TestNum,t.TestStyle,t.OptionWidth,t.OptionNum,t.GradeID')
            ->table($this->formatTable('TestReal').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestAttrReal').' t ON a.TestID=t.TestID')
            ->where('a.TestID=' . $ID)
            ->limit(1)
            ->select();
    }
    /**
     * 根据试题id查找入库试题属性
     * @param int $testList 试题id
     * @return array
     * @author demo
     */
    public function testRealSelectByIn($testList){
        return $this->table($this->formatTable('TestReal').' t')
                    ->join('LEFT JOIN '.$this->formatTable('TestAttrReal').' a on a.TestID=t.TestID')
                    ->where('t.TestID in ('.$testList.')')
                    ->select();
    }
    /******************************************************
     * Doc
     ******************************************************/
    /**
     * 文档地区关联统计总数
     * @param string $where 查询条件
     * @return int
     * @author demo
     */
    public function docSelectCount($where){
        return $this->table($this->formatTable('Doc').' a')
                    ->join('LEFT JOIN '.$this->formatTable('DocArea').' t ON a.DocID=t.DocID')
                    ->where($where)
                    ->count('a.DocID');
    }
    /**
     * 文档地区关联查询
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function docPageData($where,$page){
        return $this->field('a.*,group_concat(t.AreaID SEPARATOR ",") as AreaID, h.DocID as Hearing')
                    ->table($this->formatTable('Doc').' a')
                    ->join('LEFT JOIN '.$this->formatTable('DocArea').' t ON a.DocID=t.DocID')
                    ->join('LEFT JOIN '.$this->formatTable('DocHearing').' h ON h.DocID=a.DocID')
                    ->where($where)
                    ->group("a.DocID")
                    ->order('a.DocID desc')
                    ->page($page)
                    ->select();
    }
    /**
     * 删除与文档表对应的数据
     * @param string $table1 关联表名1
     * @param string $table2 关联表名2
     * @param int $docID  关联文档ID
     * @return bool
     * @author demo
     */
    public function docDeleteById($table1,$table2,$docID){
        return $this->execute('delete from '.$this->formatTable($table1).' WHERE TestID in (select TestID from '.$this->formatTable($table2).' where DocID in ('.$docID.')) ');
    }

    /**
     * 根据试题ID查找文档，教师任务相关信息
     * @param int $testID 试题ID
     * @return array
     * @author demo
     */
    public function docSelectByTestID($testID){
        return $this->field('`file`.UserName,`doc`.DocID,`work`.HasReplace,`work`.UserName as workUserName')
            ->table($this->formatTable("Doc").' `doc`')
            ->join('LEFT JOIN '.$this->formatTable("TeacherWorkList").' `list` ON `list`.DocID=`doc`.DocID')
            ->join('LEFT JOIN '.$this->formatTable("TeacherWork").' `work` ON `work`.WorkID=`list`.WorkID')
            ->join('LEFT JOIN '.$this->formatTable("DocFile").' `file` ON `doc`.DocID=`file`.DocID')
            ->join('LEFT JOIN '.$this->formatTable("Test").' `test` ON `test`.DocID=`doc`.DocID')
            ->where('`test`.TestID in('.$testID.')')
            ->find();
    }

    /**
     * 根据试题ID查询该试题的相关属性
     * @param int $testID 试题ID
     * @return array
     * @author demo
     */
    public function docSelectJoinByTestID($testID){
        return $this->field('t.TestID,t.Test,t.Answer,t.Remark,t.Analytic,a.TypesID,a.TestNum,a.IfChoose,a.OptionWidth,a.OptionNum')
            ->table($this->formatTable("Test").' t')
            ->join('LEFT JOIN '.$this->formatTable("TestAttr").' a on t.TestID=a.TestID')
            ->where('t.TestID=' . $testID)
            ->limit(1)
            ->select();
    }
    /******************************************************
     * Docsave 文档保存
     ******************************************************/
    /**
     * 按条件查询存档数据；
     * @param  string $where 查询条件
     * @param  string $page 分页条件
     * @return array
     * @author demo
     */
    public function docSavePageData($where,$page){
        return $this->field('a.*,s.SubjectName')
            ->table($this->formatTable('DocSave').' a')
            ->join('LEFT JOIN '.$this->formatTable('Subject').' s on a.SubjectID=s.SubjectID')
            ->where($where)
            ->order('a.SaveID desc')
            ->page($page)
            ->select();
    }
    /**
     * 多表联查统计分组数量
     * @param string $table 数据表名称
     * @param string $alias 表别名
     * @param string $where 查询条件
     * @param string $join 需要关联的表
     * @param string $field 需要统计的字段
     * @param string $group 分组的字段
     * @return atrray
     * @author demo
     */
    public function docSelectAllGroup($where){
        return $this->field('count(TestID) as Num,d.Admin')
            ->table($this->formatTable('Doc').' as d')
            ->join('left join '.$this->formatTable('TestAttrReal').' t on d.DocID=t.DocID')
            ->where($where)
            ->group('d.Admin')
            ->select();
    }

    /******************************************************
     * TestAttr 文档保存
     ******************************************************/
    /**
     * 查询数据的关联性
     * @param string $id 查询ID
     * @return array
     * @author demo
     */
    public function testSelectById($id){
        return $this->field('a.TestID,a.Test,a.Answer,a.Analytic,a.Remark,t.SubjectID')
            ->table($this->formatTable('TestReal').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestAttrReal').' t on a.TestID=t.TestID')
            ->where('a.TestID in ('.$id.')')
            ->select();
    }

    /******************************************************
     * TeacherWork 教师任务
     ******************************************************/
    /**
     * 按条件统计数量
     * @param string $where 查询条件
     * @return int 符合条件数量总数
     * @author demo
     */
    public function teacherCountData($where){
        return $this->table($this->formatTable('TeacherWork').' t')
                    ->join('LEFT JOIN '.$this->formatTable('TeacherWorkCheck').' wc on wc.WorkID=t.WorkID and wc.CheckTimes=t.CheckTimes')
                    ->where($where)
                    ->count();
    }
    /**
     * 按条件统计数量,根据文档ID统计
     * @param string $where 查询条件
     * @return int 符合条件数量总数
     * @author demo
     */
    public function teacherCountDataByDocID($where){
        return $this->table($this->formatTable('TeacherWork').' t')
            ->join('LEFT JOIN '.$this->formatTable('TeacherWorkCheck').' wc on wc.WorkID=t.WorkID and wc.CheckTimes=t.CheckTimes')
            ->join('LEFT JOIN '.$this->formatTable('TeacherWorkList').' tl on tl.WorkID= t.WorkID')
            ->where($where)
            ->count();
    }
    /**
     * 教师任务关联查询
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function teacherPageData($where,$page){
        return $this->field('t.WorkID,t.UserName,wc.UserName as CheckUser,uc.RealName as CheckReal,t.CheckTimes,u.RealName,t.Admin,t.AddTime,t.LastTime,t.Status,wc.Status as CheckStatus,t.Content,t.IfTask,t.HasReplace')
                    ->table($this->formatTable('TeacherWork').' t')
                    ->join('LEFT JOIN '.$this->formatTable('TeacherWorkCheck').' wc on wc.WorkID=t.WorkID and wc.CheckTimes=t.CheckTimes')
                    ->join('LEFT JOIN '.$this->formatTable('User').' u on t.username=u.username')
                    ->join('LEFT JOIN '.$this->formatTable('User').' uc on wc.username=uc.username')
                    ->where($where)
                    ->order('t.WorkID desc')
                    ->page($page)
                    ->select();
    }
    /**
     * 教师任务关联查询根据DocID
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function teacherPageDataByDocID($where,$page){
        return $this->field('t.WorkID,t.UserName,wc.UserName as CheckUser,uc.RealName as CheckReal,t.CheckTimes,u.RealName,t.Admin,t.AddTime,t.LastTime,t.Status,wc.Status as CheckStatus,t.Content,t.IfTask,t.HasReplace')
            ->table($this->formatTable('TeacherWork').' t')
            ->join('LEFT JOIN '.$this->formatTable('TeacherWorkCheck').' wc on wc.WorkID=t.WorkID and wc.CheckTimes=t.CheckTimes')
            ->join('LEFT JOIN '.$this->formatTable('TeacherWorkList').' tl on tl.WorkID= t.WorkID')
            ->join('LEFT JOIN '.$this->formatTable('User').' u on t.username=u.username')
            ->join('LEFT JOIN '.$this->formatTable('User').' uc on wc.username=uc.username')
            ->where($where)
            ->order('t.WorkID desc')
            ->page($page)
            ->select();
    }
    /**
     * 查询审核id对应审核错误统计；
     * @param array $IdArr 审核id数组
     * @return array
     * @author demo
     */
    public function teacherSumCheck($IdArr){
        return $this->field('wc.UserName,SUM(ta.RightNum) as RightNum,SUM(ta.CheckNum) as CheckNum,SUM(ta.LoseNum) as LoseNum,SUM(ta.CheckLoseNum) as CheckLoseNum')
            ->table($this->formatTable('TeacherWorkTestAttr').' ta')
            ->join('LEFT JOIN '.$this->formatTable('TeacherWorkTest').' wt on wt.WTID=ta.WTID')
            ->join('LEFT JOIN '.$this->formatTable('TeacherWorkCheck').' wc on wc.WCID=WT.WCID')
            ->where('wc.WCID in ('.implode(',',$IdArr).')')
            ->group('wc.UserName')
            ->select();
    }
    /**
     * 查询单个试题审核信息数据；
     * @param string $WCID 审核id
     * @param string $TestID 试题ID
     * @param string $checkTimes 审核次数
     * @return array
     * @author demo
     */
    public function teacherSingleInfo($WCID,$TestID,$checkTimes){
        return $this->field('wt.WTID,ta.AttrID,ta.NowRight,ta.RightNum,ta.Style,ta.LoseNum,ta.IfRight')
            ->table($this->formatTable('TeacherWorkTestAttr').' ta')
            ->join('LEFT JOIN '.$this->formatTable('TeacherWorkTest').' wt on wt.WTID=ta.WTID')
            ->where('wt.WCID='.$WCID.' and wt.CheckTimes='.$checkTimes.' and wt.TestID='.$TestID)
            ->select();
    }
    /**
     * 获取文档下的审核id情况；
     * @param string $WCID 审核id
     * @param string $checkTimes 审核次数
     * @param string $docID 文档id
     * @return array
     * @author demo
     */
    public function teacherWorkTestByDocInfo($WCID,$checkTimes,$docID){
        return $this->field('wt.TestID')
            ->table($this->formatTable('TeacherWorkTest').' wt')
            ->join('LEFT JOIN '.$this->formatTable('TestAttr').' ta on ta.TestID=wt.TestID')
            ->where('ta.DocID='.$docID.' and wt.CheckTimes='.$checkTimes.' and wt.WCID='.$WCID)
            ->select();
    }
    /**
     * 按条件获取审核任务试题属性关联审核任务试题；
     * @param int $where 条件
     * @param int $order 排序
     * @return array
     * @author demo
     */
    public function teacherCheckContent($where,$order){
        return $this->field('t.WTID,t.TestID,a.IfRight,a.Content,a.Style,a.Suggestion')
            ->table($this->formatTable('TeacherWorkTestAttr').' a')
            ->join('LEFT JOIN '.$this->formatTable('TeacherWorkTest').' t on a.WTID=t.WTID')
            ->where($where)
            ->order($order)
            ->select();
    }
    /**
     * 查询审核试题错误信息数据；
     * @param string $WCID 审核id
     * @param string $checkTimes 审核次数
     * @param string $did 文档id
     * @param string $testid 试题id
     * @return array
     * @author demo
     */
    public function teacherTestError($WCID,$checkTimes,$did,$testid=''){
        $testSearch='';
        if($testid) $testSearch=' and wt.TestID='.$testid;
        return $this->field('wt.TestID,ta.Content,ta.Style,ta.Suggestion')
            ->table($this->formatTable('TeacherWorkTestAttr').' ta')
            ->join('LEFT JOIN '.$this->formatTable('TeacherWorkTest').' wt on wt.WTID=ta.WTID')
            ->join('LEFT JOIN '.$this->formatTable('TestAttr').' a on a.TestID=wt.TestID')
            ->where('wt.WCID='.$WCID.' and wt.CheckTimes='.$checkTimes.' and IfRight>=0 and a.DocID='.$did.$testSearch)
            ->select();
    }
    /**
     * 查询试题审核信息数据；
     * @param string $WCID 审核id
     * @param string $checkTimes 审核次数
     * @param string $did 文档id
     * @return array
     * @author demo
     */
    public function teacherCheckInfo($WCID,$checkTimes,$did){
        return $this->field('wt.TestID,ta.AttrID,ta.WTID,ta.IfRight,ta.Content,ta.Style,ta.CheckResult,ta.Suggestion')
            ->table($this->formatTable('TeacherWorkTestAttr').' ta')
            ->join('LEFT JOIN '.$this->formatTable('TeacherWorkTest').' wt on wt.WTID=ta.WTID')
            ->join('LEFT JOIN '.$this->formatTable('TestAttr').' a on a.TestID=wt.TestID')
            ->where('wt.WCID='.$WCID.' and wt.CheckTimes='.$checkTimes.' and a.DocID='.$did)
            ->order('wt.TestID asc')
            ->select();
    }
    /**
     * 查询审核id对应文档错误统计；
     * @param array $wcArray 审核id数组
     * @param int $table 关联表 0 zj_test_attr_real 1 zj_test_attr
     * @return array
     * @author demo
     */
    public function teacherSumDoc($wcArray,$table){
        if($table) $table=$this->formatTable('TestAttr');
        else $table=$this->formatTable('TestAttrReal');
        return $this->field('a.DocID,SUM(ta.RightNum) as RightNum,SUM(ta.CheckNum) as CheckNum,SUM(IF(ta.Style="test",RightNum,0)) as TestRight,SUM(IF(ta.Style="test",LoseNum,0)) as TestLoseRight,SUM(IF(ta.Style="test",CheckNum,0)) as TestCheck,SUM(IF(ta.Style="test",CheckLoseNum,0)) as TestLoseCheck,SUM(ta.LoseNum) as LoseNum,SUM(ta.CheckLoseNum) as CheckLoseNum')
            ->table($this->formatTable('TeacherWorkTestAttr').' ta')
            ->join('LEFT JOIN '.$this->formatTable('TeacherWorkTest').' wt on wt.WTID=ta.WTID')
            ->join('LEFT JOIN '.$this->formatTable('TeacherWorkCheck').' wc on wc.WCID=WT.WCID')
            ->join('RIGHT JOIN '.$table.' a on a.TestID=wt.TestID')
            ->where('wc.WCID in ('.implode(',',$wcArray).')')
            ->group('a.DocID')
            ->select();
    }
    /**
     * 根据任务ID 查询标引任务
     * @param int $ID 任务ID
     * @author demo
     */
    public function teacherWorkSelectById($ID){
        return $this->field('w.DocID,d.DocName,w.WorkID,w.Status,w.CheckStatus')
            ->table($this->formatTable('TeacherWorkList').' w')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d on d.DocID=w.DocID')
            ->where('w.WorkID='.$ID)
            ->order('d.DocID asc')
            ->select();
    }
    /******************************************************
     * template 组卷模板
     ******************************************************/
    /**
     * 分页查询
     * @param string $page 当前页
     * @param string $where 查询的条件
     * @param string $order 排序依据
     * @return array 组卷模板数据
     * @author demo
     */
    public function templatePageData($where,$order,$page){
        return $this->field('a.TempID,a.SubjectID,a.TempName,a.IfDefault,a.UserName,a.AddTime,a.OrderID,t.TypeName')
            ->table($this->formatTable('DirTemplate').' a')
            ->join('LEFT JOIN '.$this->formatTable('DirExamType').' t on a.TypeID=t.TypeID')
            ->where($where)
            ->order($order)
            ->page($page)
            ->select();
    }
    /******************************************************
     * StudentWork 组卷模板
     ******************************************************/
    /**
     * 根据学生状态，名称时间调取相关数据
     * @param int $status 状态
     * @param string $userName 用户名
     * @param int $time 时间
     * @param array
     * @author demo
     */
    public function studentWorkByStatusUserName($status,$userName,$time){
        return $this->field('list.DocID,doc.DocName,test.TestID as tn,test.Equation')
            ->table($this->formatTable('Doc').' doc,'.$this->formatTable('Test').' test,'.$this->formatTable('StudentWorkList').' list,'.$this->formatTable('StudentWork').' `work`')
            ->where('doc.DocID=list.DocID AND test.DocID=doc.DocID AND work.WorkID=list.WorkID and `work`.Status={$status} AND `work`.UserName="'.$userName.'"'.$time)
            ->group('test.TestID')
            ->order('`work`.AddTime desc,doc.DocID desc')
            ->select();
    }
    /**
     * 根据时间关联查询学生工作信息
     * @param int $time 工作时间
     * @param int $start 开始时间
     * @param int $end 结束时间
     * @param array
     * @author demo
     */
    public function studentWorkByTime($time,$start,$end){
        $timeStr[]=sprintf('work.'.$time.' BETWEEN %d AND %d',$start,$end);
        return $this->field('work.WorkID,work.UserName,work.AddTime,work.Status,test.TestID,test.DocID,test.Equation')
            ->table($this->formatTable('Test').' test,'.$this->formatTable('Doc').' doc,'.$this->formatTable('StudentWorkList').' list,'.$this->formatTable('StudentWork').' work')
            ->where('test.DocID=doc.DocID AND doc.DocID=list.DocID AND work.WorkID=list.WorkID AND '.implode(' AND ', $timeStr))
            ->order('UserName DESC,AddTime DESC')
            ->select();
    }
    /**
     * 根据文档ID 获取任务相关信息
     * @param $docID int 文档ID
     * @return array
     * @author demo
     */
    public function studentWorkByDocId($docID){
        return $this->field('doc.`DocID`,doc.`DocName`,list.`Status`,list.`WLID`')
            ->table($this->formatTable('Doc').' doc,'.$this->formatTable('StudentWork').' work,'.$this->formatTable('StudentWorkList').' list')
            ->where('doc.IfIntro=0 AND doc.DocID=list.DocID AND list.WorkID=work.WorkID AND work.WorkID='.$docID)
            ->select();
    }
    /******************************************************
     * DocFile 文档上传
     ******************************************************/
    /**
     * 分页查看文档上传内容
     * @param string $where
     * @param string $page
     * @return array
     * @author demo
     */
    public function docFilePageData($where,$page){
        return $this->field('d.* , s.SubjectName ,s.PID,u.RealName')
            ->table($this->formatTable('DocFile').' d')
            ->join('LEFT JOIN '.$this->formatTable('Subject').' s ON d.SubjectID=s.SubjectID')
            ->join('LEFT JOIN '.$this->formatTable('User').' u ON u.UserName=d.UserName')
            ->where($where)
            ->order('FileID desc')
            ->page($page)
            ->select();
    }

    /**
     * 按条件查询文档上传相关信息
     * @param string $where 查询条件
     * @return int
     * @author demo
     */
    public function docFileSelectCountByWhere($where){
        return $this->field('`file`.DocName,`file`.FileID,`file`.LastLoad,`file`.Points,`file`.AddTime,`subject`.SubjectID,`subject`.SubjectName,`file`.DocID,doc.IfIntro,doc.IntroFirstTime,doc.IfTask,doc.Status')
            ->table($this->formatTable('DocFile').' `file`')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' doc ON doc.`DocID` = `file`.DocID')
            ->join('LEFT JOIN '.$this->formatTable('Subject').' subject ON subject.`SubjectID` = `file`.SubjectID')
            ->where($where)
            ->order('`doc`.IntroFirstTime asc,`file`.AddTime desc')
            ->count('FileID');
    }

    /**
     * 按条件查询文档上传相关信息
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return int
     * @author demo
     */
    public function docFilePageByDoc($where,$page){
        return $this->field('`file`.DocName,`file`.FileID,`file`.LastLoad,`file`.Points,`file`.FileDescription,`file`.AddTime,`file`.uploadTimes,`subject`.SubjectID,`subject`.SubjectName,`file`.DocID,doc.IfIntro,doc.IntroFirstTime,doc.IfTask,`file`.CheckStatus,doc.Status, doc.LoadTime')
            ->table($this->formatTable('DocFile').' `file`')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' doc ON doc.`DocID` = `file`.DocID')
            ->join('LEFT JOIN '.$this->formatTable('Subject').' subject ON subject.`SubjectID` = `file`.SubjectID')
            ->where($where)
            ->order('`doc`.IntroFirstTime asc,`file`.AddTime desc')
            ->limit($page)
            ->select();
    }

    /**
     * 根据类型生成where条件
     * @param $where string 查询条件
     * @return array
     * @author demo
     */
    public function docFileSelectByWhere($where){
        return $this->field('`doc`.DocID,`doc`.IfTask,`doc`.IntroFirstTime,`file`.*')
            ->table($this->formatTable('DocFile').' `file`')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' `doc` ON `doc`.DocID=`file`.DocID')
            ->where($where)
            ->select();
    }

    /**
     * 根据ID查找解析内容
     * @param $id string 查询ID
     * @return array
     * @author demo
     */
    public function docFileSelectById($id){
        return  $this->field('`file`.DocID,`file`.Admin,`file`.SubjectID,`file`.`UserName`,`file`.FileDescription')
            ->table($this->formatTable('DocFile').' `file`')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' `doc` ON `doc`.DocID=`file`.DocID')
            ->where('`file`.DocID IN('.$id.')')
            ->select();
    }

    /******************************************************
     * types 题型管理
     ******************************************************/
    /**
     * 根据条件分页查询
     * @param $where string 查询条件
     * @param $page string 分页条件
     * @return array
     * @author demo
     */
    public function typesSelectPageData($where,$page){
        return $this->table($this->formatTable('Types').' a')
            ->join('LEFT JOIN '.$this->formatTable('Subject').' s ON a.SubjectID=s.SubjectID')
            ->where($where)
            ->order('a.TypesID Desc')
            ->page($page)
            ->select();
    }
    /******************************************************
     * Knowledge 知识点模块
     ******************************************************/
    /**
     * 返回知识点能力值和答题情况的树形结构
     * @param string $userName 用户名
     * @param string $where 搜索条件
     * @return array
     * @author demo
     */
    public function knowledgeSelectByUserNameWhere($userName,$where){
        return $this->field('a.KlID,KlName,AllAmount,RightAmount,WrongAmount,UndoAmount,KlAbility,LoadTime')
                    ->table($this->formatTable('Knowledge').' a')
                    ->join('LEFT JOIN '.$this->formatTable('UserTestKl').' b on a.KlID=b.KlID and b.UserName=\''.$userName.'\'')
                    ->where($where)
                    ->order('OrderID asc,a.KlID asc')
                    ->select();
    }
    /******************************************************
     * Special 专题模块
     ******************************************************/
    /**
     * 专题分页
     * @param string $where 查询数据条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function specialPageData($where,$page){
        return $this->field('a.SpecialID,a.OrderID,a.SpecialName,s.SubjectName,s.*')
                    ->table($this->formatTable('Special').' a')
                    ->join('LEFT JOIN '.$this->formatTable('Subject').' s ON a.SubjectID=s.SubjectID')
                    ->where($where)->order('a.SpecialID Desc')
                    ->page($page)
                    ->select();
    }
    /******************************************************
     * TestMark 自定义打分
     ******************************************************/
    /**
     * 自定义打分分页
     * @param string $where 查询数据条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function testMarkPageData($where,$page){
        return $this->table($this->formatTable('TestMark').' a')
                    ->join('LEFT JOIN '.$this->formatTable('Subject').' s on a.SubjectID=s.SubjectID')
                    ->where($where)
                    ->order('a.MarkID desc')
                    ->page($page)
                    ->select();
    }
    /******************************************************
     * Word 分词
     ******************************************************/
    /**
     * 分词关联表联合统计
     * @param string $where 查询条件
     * @return int
     * @author demo
     */
    public function wordSelectCount($where){
        return $this->field('a.*,b.KlID')
            ->table($this->formatTable('Word').' a')
            ->join('LEFT JOIN '.$this->formatTable('WordKl').' b on a.WordID=b.WordID')
            ->where($where)
            ->order('a.WordID desc')
            ->count();
    }
    /**
     * 分页查询
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function wordPageData($where,$page){
        return $this->field('a.*,b.*')
            ->table($this->formatTable('Word').' a')
            ->join('LEFT JOIN '.$this->formatTable('WordKl').' b on a.WordID=b.WordID')
            ->where($where)
            ->order('a.WordID desc')
            ->page($page)
            ->select();
    }
    /******************************************************
     * UserTestRecord 测试记录
     ******************************************************/
    /**
     * 根据 测试记录ID，查看该记录详细信息
     * @param $id int 自增ID
     * @return array
     * @author demo
     */
    public function testUserRecordSelectById($id){
        return $this->field('t.UserName,t.Score,t.Style,t.LoadTime,t.RealTime,t.SubjectID,t.Content,a.TestAmount,a.AimScore,a.KlID,a.Diff,a.ChapterID,a.Cover,a.TypesID,a.TypesNum,a.DocID')
            ->table($this->formatTable('UserTestRecord').' t')
            ->join('LEFT JOIN '.$this->formatTable('UserTestRecordAttr').' a on t.TestID = a.TestRecordID')
            ->where('TestID='.$id)
            ->limit(1)
            ->select();
    }
    /**
     * 用户测试的分页查询
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function testUserRecordPageData($where,$page){
        return  $this->field($this->formatTable('UserTestRecord').'.TestID,Score,AimScore,'.$this->formatTable('UserTestRecord').'.LoadTime,KlID')
            ->table($this->formatTable('UserTestRecord'))
            ->join('LEFT JOIN '.$this->formatTable('UserTestRecordAttr').'  on '.$this->formatTable('UserTestRecord').'.TestID = '.$this->formatTable('UserTestRecordAttr').'.TestRecordID')
            ->where($where)
            ->order($this->formatTable('UserTestRecord').'.LoadTime desc')
            ->limit($page)
            ->select();
    }
    /**
     * 根据测试记录ID获取与学科相关信息
     * @param int $id 测试记录ID
     * @return array
     * @author demo
     */
    public function userTestRecordSelectById($id) {
        $result = $this->field('LoadTime,Content,Score,RealTime,UserName,'.$this->formatTable('UserTestRecord').'.SubjectID,SubjectName,'.$this->formatTable('UserTestRecord').'.Style')
                ->table($this->formatTable("UserTestRecord"))
                ->join('LEFT JOIN '.$this->formatTable('Subject').' on '.$this->formatTable('UserTestRecord').'.SubjectID = '.$this->formatTable('Subject').'.SubjectID')
                ->where('TestID=' . $id)
                ->select();
        return $result[0];
    }
    /**
     * 根据学科获取平均数
     * @param int $subjectID 学科ID
     * @return int
     * @author demo
     */
    public function userTestRecordAvgScoreBySubjectId($subjectID){
        //排除时间和分数是0的
        $result = $this->table($this->formatTable("UserTestRecord"))
                        ->where('Score!=0 and RealTime!=0 and SubjectID='.$subjectID)
                        ->avg('Score');
        return $result;
    }
    /**
     * 根据用户名，学科获取对应的考试结果
     * @param string $userName 学生姓名
     * @param int $subjectID 学生姓名
     * @return array
     * @author demo
     */
    public function userTestRecordListData($userName,$subjectID) {
        return $this->field('TestID,Score,RealTime,LoadTime,SubjectName')
            ->table($this->formatTable("UserTestRecord"))
            ->join('LEFT JOIN '.$this->formatTable("Subject").' on '.$this->formatTable("UserTestRecord").'.SubjectID = '.$this->formatTable("Subject").'.SubjectID')
            ->where(array('UserName'=>$userName,'Score'=>array('neq',-1),''.$this->formatTable("UserTestRecord").'.SubjectID'=>$subjectID))
            ->order('LoadTime desc')
            ->limit(10)
            ->select();
    }

    /**
     * 获取最近500道有效作答 IfRight字段值为1或者2的
     * 用于计算知识点能力值 用于PersonalReport
     * @param array $where 查询条件
     * @return array
     * @author demo
     */
    public function UserAnswerRecordSendWordByWhere($where){
         return  $this->field('d.KlID AS CKlID,e.KlID AS SKlID,a.TestID,a.IfRight,a.IfChoose,a.TestType')
            ->table($this->formatTable('UserAnswerRecord').' a')
            ->join('LEFT JOIN '.$this->formatTable('UserSendWork').' b ON a.SendID=b.SendID')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestKnowledge').' d ON a.TestID=d.TestID AND a.TestType=2')
            ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' e ON a.TestID=e.TestID AND a.TestType=1')
            ->where($where)
            ->order('a.LoadTime ASC')//正序排列的目的是让下面的循环中最新的作答情况覆盖旧的作答情况
            ->limit(500)
            ->select();
    }
    /**
     * 获取最近500道有效作答 IfRight字段值为1或者2的
     * 用于计算知识点能力值 用于PersonalReport
     * @param array $where 查询条件
     * @return array
     * @author demo
     */
    public function UserAnswerRecordUserTestRecordByWhere($where){
        return  $this->field('d.KlID AS CKlID,e.KlID AS SKlID,a.TestID,a.IfRight,a.IfChoose,a.TestType')
            ->table($this->formatTable('UserAnswerRecord').' a')
            ->join('LEFT JOIN '.$this->formatTable('UserTestRecord').' b ON a.TestRecordID=b.TestID')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestKnowledge').' d ON a.TestID=d.TestID AND a.TestType=2')
            ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' e ON a.TestID=e.TestID AND a.TestType=1')
            ->where($where)
            ->order('a.LoadTime ASC')//正序排列的目的是让下面的循环中最新的作答情况覆盖旧的作答情况
            ->limit(500)
            ->select();
    }

    /**
     * 获取最近500道有效作答 IfRight字段值为1或者2的
     * 用于计算技能能力值 用于PersonalReport
     * @param array $where 查询条件
     * @return array
     * @author demo
     */
    public function UserAnswerRecordSendWordByWhereSkill($where){
         return  $this->field('d.SkillID AS CSkillID,e.SkillID AS SSkillID,a.TestID,a.IfRight,a.IfChoose,a.TestType')
            ->table($this->formatTable('UserAnswerRecord').' a')
            ->join('LEFT JOIN '.$this->formatTable('UserSendWork').' b ON a.SendID=b.SendID')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestSkill').' d ON a.TestID=d.TestID AND a.TestType=2')
            ->join('LEFT JOIN '.$this->formatTable('TestSkill').' e ON a.TestID=e.TestID AND a.TestType=1')
            ->where($where)
            ->order('a.LoadTime ASC')//正序排列的目的是让下面的循环中最新的作答情况覆盖旧的作答情况
            ->limit(500)
            ->select();
    }
    /**
     * 获取最近500道有效作答 IfRight字段值为1或者2的
     * 用于计算技能能力值 用于PersonalReport
     * @param array $where 查询条件
     * @return array
     * @author demo
     */
    public function UserAnswerRecordUserTestRecordByWhereSkill($where){
        return  $this->field('d.SkillID AS CSkillID,e.SkillID AS SSkillID,a.TestID,a.IfRight,a.IfChoose,a.TestType')
            ->table($this->formatTable('UserAnswerRecord').' a')
            ->join('LEFT JOIN '.$this->formatTable('UserTestRecord').' b ON a.TestRecordID=b.TestID')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestSkill').' d ON a.TestID=d.TestID AND a.TestType=2')
            ->join('LEFT JOIN '.$this->formatTable('TestSkill').' e ON a.TestID=e.TestID AND a.TestType=1')
            ->where($where)
            ->order('a.LoadTime ASC')//正序排列的目的是让下面的循环中最新的作答情况覆盖旧的作答情况
            ->limit(500)
            ->select();
    }

    /**
     * 获取最近500道有效作答 IfRight字段值为1或者2的
     * 用于计算技能能力值 用于PersonalReport
     * @param array $where 查询条件
     * @return array
     * @author demo
     */
    public function UserAnswerRecordSendWordByWhereCapacity($where){
         return  $this->field('d.CapacityID AS CCapacityID,e.CapacityID AS SCapacityID,a.TestID,a.IfRight,a.IfChoose,a.TestType')
            ->table($this->formatTable('UserAnswerRecord').' a')
            ->join('LEFT JOIN '.$this->formatTable('UserSendWork').' b ON a.SendID=b.SendID')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestCapacity').' d ON a.TestID=d.TestID AND a.TestType=2')
            ->join('LEFT JOIN '.$this->formatTable('TestCapacity').' e ON a.TestID=e.TestID AND a.TestType=1')
            ->where($where)
            ->order('a.LoadTime ASC')//正序排列的目的是让下面的循环中最新的作答情况覆盖旧的作答情况
            ->limit(500)
            ->select();
    }
    /**
     * 获取最近500道有效作答 IfRight字段值为1或者2的
     * 用于计算技能能力值 用于PersonalReport
     * @param array $where 查询条件
     * @return array
     * @author demo
     */
    public function UserAnswerRecordUserTestRecordByWhereCapacity($where){
        return  $this->field('d.CapacityID AS CCapacityID,e.CapacityID AS SCapacityID,a.TestID,a.IfRight,a.IfChoose,a.TestType')
            ->table($this->formatTable('UserAnswerRecord').' a')
            ->join('LEFT JOIN '.$this->formatTable('UserTestRecord').' b ON a.TestRecordID=b.TestID')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestCapacity').' d ON a.TestID=d.TestID AND a.TestType=2')
            ->join('LEFT JOIN '.$this->formatTable('TestCapacity').' e ON a.TestID=e.TestID AND a.TestType=1')
            ->where($where)
            ->order('a.LoadTime ASC')//正序排列的目的是让下面的循环中最新的作答情况覆盖旧的作答情况
            ->limit(500)
            ->select();
    }
    /**
     * 分页查看提分测试记录数据
     * @param string $userName 关联用户名
     * @param string $subjectID 关联学科ID
     * @param string $limit 关联用户名
     * @return array
     * @author demo
     */
    public function userTestRecordPageBySubjectUserName($userName,$subjectID,$limit){
        return $this->field('a.TestID,a.Style,a.Score,a.RealTime,a.Content,a.LoadTime,b.TestRecordName,c.DocName')
            ->table($this->formatTable('UserTestRecord').' a')
            ->join('LEFT JOIN '.$this->formatTable('UserTestRecordAttr').' b ON b.TestRecordID=a.TestID')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' c ON c.DocID=b.DocID')
            ->where(['a.UserName'=>$userName,'a.SubjectID'=>$subjectID])
            ->order('a.LoadTime desc')
            ->limit($limit)
            ->select();

    }
    /******************************************************
     * Test 试题模块
     ******************************************************/
    /**
     * 根据试题ID查询相关详细信息
     * @param $testID int 试题ID
     * @return array
     * @author demo
     */
    public function testSelectByTestId($testID){
        return $this->field('a.*,t.TypesID,t.Duplicate,t.SpecialID,t.SubjectID,t.IfChoose,t.Diff,t.DfStyle,t.Mark,t.TestNum,t.TestStyle,t.OptionWidth,t.OptionNum,t.Score')
            ->table($this->formatTable('Test').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestAttr').' t ON a.TestID=t.TestID')
            ->where('a.TestID=' . $testID)
            ->limit(1)
            ->select();
    }
    /**
     * 根据试题ID查询入库试题相关详细信息
     * @param $testID int 试题ID
     * @return array
     * @author demo
     */
    public function testRealSelectById($testID){
        return $this->field('a.*,t.TypesID,t.SpecialID,t.SubjectID,t.IfChoose,t.Diff,t.DfStyle,t.Mark')
            ->table($this->formatTable('Test').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestAttrReal').' t ON a.TestID=t.TestID')
            ->where('a.TestID=' . $testID)
            ->limit(1)
            ->select();
    }
    /**
     * 根据试题查询文档相关信息
     * @param $testID int 试题ID
     * @return array
     * @author demo
     */
    public function testDocSelectByTestID($testID){
        return $this->field('d.*')
            ->table($this->formatTable('Test').' a')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d on a.DocID=d.DocID')
            ->where('a.TestID in ('.$testID.')')
            ->select();
    }
    /**
     * 根据入库试题文档查找相关试题
     * @param int $docID 文档ID
     * @return array
     * @author demo
     */
    public function testDocAttrSelectByDocID($docID){
        return $this->field('a.TestID,a.Test,a.DocID,d.DocName,d.Admin,t.SubjectID,t.TypesID,t.Duplicate')
            ->table($this->formatTable('TestReal').' a')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d ON d.DocID=a.DocID')
            ->join('LEFT JOIN '.$this->formatTable('TestAttr').' t ON t.TestID=a.TestID')
            ->where(' 1=1 and a.DocID='.$docID)
            ->order(' a.TestID asc ')
            ->select();
    }
    /**
     * 根据未入库试题文档查找相关试题(排重功能)
     * @param int $docID 文档ID
     * @param int $limit 查询限制条数
     * @return array
     * @author demo
     */
    public function testAttrSelectByDocID($docID, $limit=''){
        return $this->field('a.TestID,a.Test,a.DocID,d.DocName,d.Admin,t.SubjectID,t.TypesID,t.Duplicate')
            ->table($this->formatTable('Test').' a')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d ON d.DocID=a.DocID')
            ->join('LEFT JOIN '.$this->formatTable('TestAttr').' t ON t.TestID=a.TestID')
            ->where(' 1=1 and a.DocID='.$docID)
            ->order(' a.TestID asc ')
            ->limit($limit)
            ->select();
    }
    /******************************************************
     * TestReal 入库试题模块
     ******************************************************/
    /**
     * 根据testID查询入库试题相关信息
     * @param $testID int 入库试题ID
     * @return array
     * @author demo
     */
    public function testRealSelectByTestId($testID){
        return $this->field('a.*,t.TypesID,t.SpecialID,t.SubjectID,t.Diff,t.IfChoose,t.TestNum,t.DfStyle,t.Mark,t.TestStyle,t.OptionWidth,t.OptionNum,t.Duplicate,t.GradeID,t.Score')
            ->table($this->formatTable("TestReal").' a')
            ->join('LEFT JOIN '.$this->formatTable("TestAttrReal").' t ON a.TestID=t.TestID')
            ->where('a.TestID in (' . $testID.')')
            ->order('a.TestID desc')
            ->select();
    }
    /**
     * 根据入库试题文档查找相关试题
     * @param int $docID 文档ID
     * @return array
     * @author demo
     */
    public function testDocAttrRealSelectByDocID($docID, $limit=''){
        return $this->field('a.TestID,d.IntroFirstTime,a.Test,a.DocID,d.DocName,d.Admin,t.SubjectID,t.TypesID,t.Duplicate')
            ->table($this->formatTable('TestReal').' a')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d ON d.DocID=a.DocID')
            ->join('LEFT JOIN '.$this->formatTable('TestAttrReal').' t ON t.TestID=a.TestID')
            ->where(' 1=1 and a.DocID='.$docID)
            ->order(' a.TestID asc ')
            ->limit($limit)
            ->select();
    }
    /**
     * 根据testID查询入库试题与文档关联相关信息
     * @param $testID int 入库试题ID
     * @return array
     * @author demo
     */
    public function testRealDocSelectByTestId($testID){
        return $this->field('d.*')
            ->table($this->formatTable("TestReal").' a')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d on a.DocID=d.DocID')
            ->where('a.TestID in ('.$testID.')')
            ->order('a.TestID DESC')
            ->select();
    }
    /**
     * 试题与文档，标签关联查询
     * @param int $testID 试题ID
     * @param int $auto 类型 0未入库试题 1入库试题
     * @return array
     * @author demo
     */
    public function testSelectJoinById($testID,$auto=0){
        $dbName='TestAttr';
        if($auto==1){
            $dbName='TestAttrReal';
        }
        return $this->field('x.TestID,x.NumbID,d.DocTest,d.DocAnswer,d.DocAnalytic,d.DocRemark,x.TypesID,x.SubjectID,x.DocID,x.Duplicate')
            ->table($this->formatTable($dbName).' x')
            ->join('LEFT JOIN '.$this->formatTable('TestDoc').' d on x.TestID=d.TestID')
            ->where('x.TestID=' . $testID)
            ->order('x.TestID DESC')
            ->limit(1)
            ->select();
    }
    /**
     * 未入库试题与文档，标签关联查询
     * @param int $docID 试题ID
     * @return array
     * @author demo
     */
    public function testSelectByDocId($docID){
        return $this->field('d.IntroFirstTime,a.TestID,a.Test,a.DocID,d.DocName,d.Admin,t.SubjectID,t.SpecialID,t.Diff,t.TypesID,t.Duplicate')
            ->table($this->formatTable('Test').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestAttr').' t on a.TestID=t.TestID')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d on a.DocID=d.DocID')
            ->where('a.DocID='.$docID)
            ->order('a.TestID ASC')
            ->select();
    }
    /******************************************************
     * TestAttr 试题标识属性
     ******************************************************/
    /**
     * 按条件查询未入库试题标识总数
     * @param string $where 查询条件
     * @return int
     * @author demo
     */
    public function testAttrSelectCount($where){
        return $this->field('k.TestID')
            ->table($this->formatTable('TestAttr').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestKl').' k on a.TestID=k.TestID')
            ->join('LEFT JOIN '.$this->formatTable('DocArea').' r on a.DocID=r.DocID')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d on a.DocID=d.DocID')
            ->where($where)
            ->count();
    }
    /**
     * 按条件查询未入库试题聚合数量
     * @param string $where 查询条件
     * @return int
     * @author demo
     */
    public function testAttrSelectGroup($where,$group){
        return $this->field('count(k.TestID),'.$group)
            ->table($this->formatTable('TestAttr').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestKl').' k on a.TestID=k.TestID')
            ->join('LEFT JOIN '.$this->formatTable('DocArea').' r on a.DocID=r.DocID')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d on a.DocID=d.DocID')
            ->where($where)
            ->group($group)
            ->select();
    }
    /**
     * 按条件查询未入库试题聚合打分数据
     * @param string $where 查询条件
     * @return int
     * @author demo
     */
    public function testAttrSelectMark($where,$group){
        return $this->field('count(tam.TestID),'.$group)
            ->table($this->formatTable('TestAttr').' a')
            ->join('RIGHT JOIN '.$this->formatTable('TestAttrMark').' tam on a.TestID=tam.TestID')
            ->join('LEFT JOIN '.$this->formatTable('TestKl').' k on a.TestID=k.TestID')
            ->join('LEFT JOIN '.$this->formatTable('DocArea').' r on a.DocID=r.DocID')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d on a.DocID=d.DocID')
            ->where($where)
            ->group($group)
            ->select();
    }
    /**
     * 按条件查询入库试题标识总数
     * @param string $where 查询条件
     * @return int
     * @author demo
     */

    public function testAttrRealSelectCount($where){
        return $this->field('k.TestID')
            ->table($this->formatTable('TestAttrReal').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' k on a.TestID=k.TestID')
            ->join('LEFT JOIN '.$this->formatTable('DocArea').' r on a.DocID=r.DocID')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d on a.DocID=d.DocID')
            ->where($where)
            ->count();
    }

    /**
     * 按条件查询入库试题聚合数量
     * @param string $where 查询条件
     * @return int
     * @author demo
     */
    public function testAttrRealSelectGroup($where,$group){
        return $this->field('count(k.TestID) as TestNum,'.$group)
            ->table($this->formatTable('TestAttrReal').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' k on a.TestID=k.TestID')
            ->join('LEFT JOIN '.$this->formatTable('DocArea').' r on a.DocID=r.DocID')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d on a.DocID=d.DocID')
            ->where($where)
            ->group($group)
            ->select();
    }
    /**
     * 按条件查询入库试题聚合打分数据
     * @param string $where 查询条件
     * @return int
     * @author demo
     */
    public function testAttrRealSelectMark($where,$group){
        return $this->field('count(tam.TestID) as TestNum,'.$group)
            ->table($this->formatTable('TestAttrReal').' a')
            ->join('RIGHT JOIN '.$this->formatTable('TestAttrMark').' tam on a.TestID=tam.TestID')
            ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' k on a.TestID=k.TestID')
            ->join('LEFT JOIN '.$this->formatTable('DocArea').' r on a.DocID=r.DocID')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d on a.DocID=d.DocID')
            ->where($where)
            ->group($group)
            ->select();
    }
    /**
     * 根据知识点统计未入库试题数量
     * @param array $where 查询条件
     * @return int 总数
     * @author demo
     */
    public function testAttrKlSelectCount($where){
        return $this->field('k.TestID')
            ->table($this->formatTable('TestAttr').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestKl').' k on a.TestID=k.TestID')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d on a.DocID=d.DocID')
            ->where($where)
            ->count();
    }
    /**
     * 根据知识点统计未入库试题聚合数量
     * @param array $where 查询条件
     * @return int 总数
     * @author demo
     */
    public function testAttrKlSelectGroup($where,$group){
        return $this->field('count(k.TestID) as TestNum,'.$group)
            ->table($this->formatTable('TestAttr').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestKl').' k on a.TestID=k.TestID')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d on a.DocID=d.DocID')
            ->where($where)
            ->group($group)
            ->select();
    }
    /**
     * 根据知识点统计未入库试题聚合打分数量
     * @param array $where 查询条件
     * @return int 总数
     * @author demo
     */
    public function testAttrKlSelectMark($where,$group){
        return $this->field('count(tam.TestID) as TestNum,'.$group)
            ->table($this->formatTable('TestAttr').' a')
            ->join('RIGHT JOIN '.$this->formatTable('TestAttrMark').' tam on a.TestID=tam.TestID')
            ->join('LEFT JOIN '.$this->formatTable('TestKl').' k on a.TestID=k.TestID')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d on a.DocID=d.DocID')
            ->where($where)
            ->group($group)
            ->select();
    }
    /**
     * 根据知识点统计入库试题数量
     * @param array $where 查询条件
     * @return int 总数
     * @author demo
     */
    public function testAttrKlRealSelectCount($where){
        return $this->field('k.TestID')
            ->table($this->formatTable('TestAttrReal').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' k on a.TestID=k.TestID')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d on a.DocID=d.DocID')
            ->where($where)
            ->count();
    }
    /**
     * 根据知识点统计入库试题聚合数量
     * @param array $where 查询条件
     * @return int 总数
     * @author demo
     */
    public function testAttrKlRealSelectGroup($where,$group){
        return $this->field('count(k.TestID) as TestNum,'.$group)
            ->table($this->formatTable('TestAttrReal').' a')
            ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' k on a.TestID=k.TestID')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d on a.DocID=d.DocID')
            ->where($where)
            ->group($group)
            ->select();
    }

    /**
     * 根据知识点统计入库试题聚合打分数量
     * @param array $where 查询条件
     * @return int 总数
     * @author demo
     */
    public function testAttrKlRealSelectMark($where,$group){
        return $this->field('count(tam.TestID) as TestNum,'.$group)
            ->table($this->formatTable('TestAttrReal').' a')
            ->join('RIGHT JOIN '.$this->formatTable('TestAttrMark').' tam on a.TestID=tam.TestID')
            ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' k on a.TestID=k.TestID')
            ->join('LEFT JOIN '.$this->formatTable('Doc').' d on a.DocID=d.DocID')
            ->where($where)
            ->group($group)
            ->select();
    }

    /**
     * 分学科查询统计
     * @param string $where 分组统计条件
     * @return array
     * @author demo
     */
    public function testAttrRealGroupBySubjectCount($where){
        return $this->field('COUNT(SubjectID) as `StatTest` , SubjectID')
            ->table($this->formatTable('TestAttrReal'))
            ->where($where)
            ->group('SubjectID')
            ->select();
    }

    /******************************************************
     * CustomTest 校本题库试题
     ******************************************************/
    /**
     * 按统计校本题库试题总数
     * @param string $where 查询条件
     * @return int
     * @author demo
     */
    public function customTestSelectCount($where){
        return $this->field('a.*,b.*')
                ->table($this->formatTable('CustomTest').' a')
                ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' b on a.TestID=b.TestID')
                ->where($where)
                ->count();
    }

    /**
     * 按统计校本题库试题分页查看数据
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function customTestSelectByPageList($where,$page,$doc=0){
        $doc = (int)$doc;
        if($doc > 0){
            $this->join('LEFT JOIN '.$this->formatTable('CustomTestDoc').' doc ON doc.DocID='.$doc);
            $where .= ' AND doc.TestID = a.TestID';
            $order = 'doc.OrderRule ASC';
        }else{
            $order = 'a.TestID DESC';
        }
        return $this->field('a.*,b.*,c.UserName')
            ->table($this->formatTable('CustomTest').' a')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' b on a.TestID=b.TestID')
            ->join('LEFT JOIN '.$this->formatTable('User').' c ON c.UserID = b.UserID')
            ->where($where)
            ->page($page)
            ->order($order)
            ->select();
    }

    /**
     * 根据校本题库试题ID查询相关信息
     * @param int $testID 试题ID
     * @return array
     * @author demo
     */
    public function customTestSelectByTestId($testID, $fields=''){
        if(empty($fields)){
            $fields = '`test`.*,`attr`.*,`test`.Test as DocTest,`test`.Analytic as DocAnalytic,`test`.Answer as DocAnswer,`test`.Remark as DocRemark';
        }
        return $this->field($fields)
                ->table($this->formatTable('CustomTest').' test')
                ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' attr ON `attr`.TestID=`test`.TestID')
                ->where("`test`.TestID IN({$testID})")
                ->select();
    }

    /**
     * 根据$testID查询校本题库中的相关信息
     * @param int $testID 试题ID
     * @return array
     * @author demo
     */
    public function customTestFindByTestId($testID){
        return $this->field('`attr`.*,`test`.Test,`test`.Answer,`test`.Analytic,`test`.Source,`test`.Remark')
            ->table($this->formatTable('CustomTest').' `test`')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' `attr` ON `attr`.TestID=`test`.TestID')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestJudge').' `judge` ON `test`.TestID=`judge`.TestID')
            ->where('test.TestID='.$testID)
            ->find();
    }
    /**
     * 根据排序条件，精确查询相关内容
     * @param string $where 查询条件
     * @param string $order 排序条件
     * @param $if=0 查询判断
     * @return array
     * @author demo
     */
    public function customTestFindBy($where,$order,$if=0){
        if($if==1){
            $result= $this->field('count(`knowledge`.TestID) as num')
                ->table($this->formatTable('CustomTestKnowledge').' `knowledge`')
                ->join('LEFT JOIN '.$this->formatTable('CustomTest').' `test` ON `test`.TestID=`knowledge`.TestID')
                ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' `attr` ON `attr`.TestID=`test`.TestID')
                ->where($where)
                ->order($order)
                ->find();
        }else{
            $result= $this->field('count(`test`.TestID) as num')
                ->table($this->formatTable('CustomTest').' `test`')
                ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' `attr` ON `attr`.TestID=`test`.TestID')
                ->where($where)
                ->order($order)
                ->find();
        }
        return $result;
    }
    /**
     * 根据排序条件，精确查询相关内容
     * @param string $where 查询条件
     * @param string $limit 限制条件
     * @param string $order 排序条件
     * @param int $if=0 查询判断
     * @return array
     * @author demo
     */
    public function customTestSelectBy($where,$limit,$order,$if=0){
        if($if==1){
            $result = $this->distinct(true)->field('CONCAT("'.\Test\Model\TestQueryModel::DIVISION.'",`test`.TestID) as testid, `test`.Test as test, `test`.Source as docname, `attr`.AddTime as firstloadtime, `attr`.TypesID as typesid, `attr`.Diff as diff, `attr`.SubjectID as subjectid, `attr`.TestNum as testnum, `attr`.Status as status,"1" as partition')
                ->table($this->formatTable('CustomTestKnowledge').' `knowledge`')
                ->join('LEFT JOIN '.$this->formatTable('CustomTest').' `test` ON `test`.TestID=`knowledge`.TestID')
                ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' `attr` ON `attr`.TestID=`test`.TestID')
                ->where($where)
                ->limit($limit)
                ->order($order)
                ->select();
        }else{
            $result = $this->field( 'CONCAT("'.\Test\Model\TestQueryModel::DIVISION.'",`test`.TestID) as testid, `test`.Test as test, `test`.Source as docname, `attr`.AddTime as firstloadtime, `attr`.TypesID as typesid, `attr`.Diff as diff, `attr`.SubjectID as subjectid, `attr`.TestNum as testnum, `attr`.Status as status,"1" as partition')
                ->table($this->formatTable('CustomTest').' `test`')
                ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' `attr` ON `attr`.TestID=`test`.TestID')
                ->where($where)
                ->limit($limit)
                ->order($order)
                ->select();
        }
        return $result;
    }
    /**
     * 根据用户名及试题ID查询自定义试题及试题属性相关信息
     * @param $userName string 用户名
     * @param int $testID 试题ID
     * @return array
     * @author demo
     */
    public function customTestSelectByUserNameTestId($userName,$testID){
        return $this->field(' Analytic as analytic,Answer as answer,Remark as `remark`')
                    ->table($this->formatTable('CustomTest').' t, '.$this->formatTable('CustomTestAttr').' ta')
                    ->where('ta.TestID = t.TestID AND  ta.`UserName` = "'.$userName.'"  AND  t.`TestID` = '.$testID.' ')
                    ->limit(1)
                    ->select();
    }

    /**
     * 根据条件查询校本题库审核任务列表
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @param string $order 排序条件
     * @return array
     * @author demo
     */
    public function customTestSelectByPage($where,$page,$order){
        return $this->field('status.*,attr.UserID AS TestAuthorID,attr.IsTpl,task.UserName AS TaskUserName')
            ->table($this->formatTable('CustomTestTaskList').' task')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' attr ON task.TestID=attr.TestID')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestTaskStatus').' status  ON status.TestID=task.TestID')//任务状态大于-2的只会有一个人
            ->where($where)
            ->page($page)
            ->order($order)
            ->select();
    }

    /**
     * 校本题库试题审核列表页的总记录条数
     * @param string $where 查询条件
     * @return int 总数
     * @author demo
     */
    public function CustomTestStatusSelectByCount($where){
        return  $this->field('*')
            ->table($this->formatTable('CustomTestTaskList').' `task`')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestTaskStatus').' `status` ON `task`.TestID=`status`.TestID')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' `attr` ON `task`.TestID = `attr`.TestID')
            ->where($where)
            ->count();
    }
    /**
     * 根据TestID查询校本题库试题相关属性
     * @param string $testID 试题ID
     * @return array
     * @author demo
     */
    public function customTestSelectByTestIDDel($testID){
        return $this->field('a.*,b.*,c.IfLock')
            ->table($this->formatTable('CustomTest').' a')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' b on a.TestID=b.TestID')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestTaskStatus').' c on a.TestID=c.TestID')
            ->where('a.TestID in('.$testID.')')
            ->select();
    }
    /**
     * 根据TestID查询校本题库试题相关属性
     * @param string $testID 试题ID
     * @return array
     * @author demo
     */
    public function customTestSelectById($testID, $fields='*'){
        return $this->field($fields)
            ->table($this->formatTable('CustomTestAttr').' `attr`')
            ->join('LEFT JOIN '.$this->formatTable('CustomTest').' `test` ON `attr`.TestID=`test`.TestID')
            ->where('test.TestID IN('.$testID.')')
            ->select();
    }

    /******************************************************
     * CustomTestTaskLog 校本题库审核日志
     ******************************************************/

    /**
     * 校本题库审核日志统计总数
     * @param string $where 查询条件
     * @return int
     * @author demo
     */
    public function customTestTaskLogSelectCount($where){
        return $this->field('a.*,b.*')
            ->table($this->formatTable('CustomTestTaskLog').' a')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' b on a.TestID=b.TestID')
            ->where($where)
            ->count();
    }

    /**
     * 校本题库审核日志分页浏览
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function customTestTaskLogSelectByPage($where,$page){
        return $this->field('a.*,b.SubjectID,a.Status as LogStatus')
            ->table($this->formatTable('CustomTestTaskLog').' a')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' b on a.TestID=b.TestID')
            ->where($where)
            ->page($page)
            ->order('a.LogID DESC')
            ->select();
    }

    /**
     * 校本题库按条件查询审核日志
     * @param string $where 查询条件
     * @param string $limit 限制条数
     * @return array
     * @author demo
     */
    public function customTestTaskLogSelectByWhere($where,$limit=''){
        return $this->field('a.*,b.*,a.Status as LogStatus')
            ->table($this->formatTable('CustomTestTaskLog').' a')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' b on a.TestID=b.TestID')
            ->where($where)
            ->order('a.LogID DESC')
            ->limit($limit)
            ->select();
    }
    /**
     * 按月统计校本题库金额数
     * @param int $startTime 开始时间
     * @param int $endTime 结束时间
     * @return array
     * @author demo
     */
    public function customTestTaskLogTotalByMonth($startTime,$endTime){
        return $this->field('u.UserName,cl.UserName as groupUserName,SUM(cl.Point) as PointTotal,count(cl.LogID) as testTotal')
            ->table($this->formatTable('CustomTestTaskLog').' cl')
            ->join('LEFT JOIN '.$this->formatTable('User').' u on cl.UserName=u.UserID')
            ->where('cl.AddTime>'.$startTime.' and cl.AddTime<'.$endTime.' and cl.IfTotal=1')
            ->group('groupUserName')
            ->order('PointTotal Desc')
            ->limit('10')
            ->select();
    }
    /******************************************************
     * customTestCopy 校本题库副表
     ******************************************************/

    /**
     * 分页查询校本题库试题副本总数
     * @param string $where 查询条件
     * @return int
     * @author demo
     */
    public function customTestCopySelectCount($where){
        return $this->field('a.*,b.*')
            ->table($this->formatTable('CustomTestCopy').' a')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestAttrCopy').' b on a.TestID=b.TestID')
            ->where($where)
            ->count();
    }
    /**
     * 分页查询校本题库试题副本
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function customTestCopySelectByPage($where,$page){
        return $this->field('a.*,b.*,c.UserName')
            ->table($this->formatTable('CustomTestCopy').' a')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestAttrCopy').' b on a.TestID=b.TestID')
            ->join('LEFT JOIN '.$this->formatTable('User').' c ON c.UserID = b.UserID')
            ->where($where)
            ->page($page)
            ->order('a.TestID DESC')
            ->select();
    }
    /**
     * 根据试题ID 关联试题属性表查看详细试题属性
     * @param string $testID 试题ID
     * @return array
     * @author demo
     */
    public function customTestCopySelectById($testID){
       return $this->field('a.*,b.*')
            ->table($this->formatTable('CustomTestCopy').' a')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestAttrCopy').' b on a.TestID=b.TestID')
            ->where('a.TestID='.$testID)
            ->order('a.TestID DESC')
            ->select();
    }

    /******************************************************
     * CustomTestTaskList 校本题库过期列表
     ******************************************************/
    /**
     * 根据条件统计过期数据表统总数
     * @param string $where 查询条件
     * @return int
     * @author demo
     */
    public function customTestTaskListSelectCount($where){
        return $this->field('a.*,b.*')
            ->table($this->formatTable('CustomTestTaskList').' a')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' b on a.TestID=b.TestID')
            ->where($where)
            ->count();
    }

    /**
     * 根据条件分页查看过期数据表统总数
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function customTestTaskListSelectByPage($where,$page){
        return $this->field('a.*,b.*,a.Status as TestStatus,a.UserName as CheckUser,c.UserName as TestAuthorName,
        c.RealName as TestAuthorRealName,b.IsTpl')
            ->table($this->formatTable('CustomTestTaskList').' a')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' b on a.TestID=b.TestID')
            ->join('LEFT JOIN '.$this->formatTable('User').' c on b.UserID=c.UserID')
            ->where($where)
            ->page($page)
            ->order('a.AddTime DESC')
            ->select();
    }

    /**
     * 按条件搜索过期数据与用户属性关联查询生成SQL语句字符串
     * @param string $where 查询条件
     * @return string
     * @author demo
     */
    public function customTestTaskListSelectByWhere($where){
        return $this->field('a.ListID,b.*,a.Status as TestStatus,a.UserName as CheckUser')
            ->table($this->formatTable('CustomTestTaskList').' a')
            ->join('LEFT JOIN '.$this->formatTable('User').' b on b.UserName=a.UserName')
            ->where($where)
            ->order('a.ListID DESC')
            ->select(false);
    }
    /**
     * 用户过期次数分组统计
     * @param string $sqlString sql语句字符串
     * @return array
     * @author demo
     */
    public function customTestTaskListGroupBy($sqlString){
        return $this->field('c.*,count(*) as Total')
                    ->table($sqlString.' c')
                    ->group('CheckUser')
                    ->select();
    }

    /**
     * 分页显示分组统计用户过期数据
     * @param string $sqlString sql语句字符串
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function customTestTaskListGroupPage($sqlString,$page){
        return $this->field('c.*,count(*) as Total')
            ->table($sqlString.' c')
            ->page($page)
            ->group('CheckUser')
            ->order('Total DESC')
            ->select();
    }

    /**
     * 根据条件查询教师审核校本题库，所有扣分记录总数
     * @param string $where 统计总数
     * @return int
     * @author demo
     */
    public function customTestTaskListSelectCountByWhere($where){
        return $this->field('a.*,b.RealName,b.SubjectStyle')
            ->table($this->formatTable('CustomTestTaskList').' a')
            ->join('LEFT JOIN '.$this->formatTable('User').' b on b.UserName=a.UserName')
            ->where($where)
            ->count();
    }

    /**
     * 根据条件分页查看教师审核校本题库，所有扣分记录
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function customTestTaskListSelectByUserPage($where,$page){
        return $this->field('a.*,b.RealName,b.Cz,b.SubjectStyle')
            ->table($this->formatTable('CustomTestTaskList').' a')
            ->join('LEFT JOIN '.$this->formatTable('User').' b on b.UserName=a.UserName')
            ->where($where)
            ->page($page)
            ->order('a.ListID DESC')
            ->select();
    }
    /******************************************************
     * CustomTestTaskStatus 任务试题状态信息
     ******************************************************/
    /**
     * 根据条件查询教师审核校本题库，所有扣分记录总数
     * @param string $where 统计总数
     * @return int
     * @author demo
     */
    public function customTestTaskStatusSelectCount($where){
        return $this->field('a.*,b.SubjectID')
            ->table($this->formatTable('CustomTestTaskStatus').' a')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' b on a.TestID=b.TestID')
            ->where($where)
            ->count();
    }

    /**
     * 通过试题ID，查找任务试题状态信息及该试题学科
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function customTestTaskStatusSelectByPage($where,$page){
        return $this->field('a.*,b.SubjectID,b.UserID,b.LastUpdateTime,b.IsTpl,c.UserName,c.RealName')
            ->table($this->formatTable('CustomTestTaskStatus').' a')
            ->join('LEFT JOIN '.$this->formatTable('CustomTestAttr').' b on a.TestID=b.TestID')
            ->join('LEFT JOIN '.$this->formatTable('User').' c on b.UserID=c.UserID')
            ->where($where)
            ->order("AddTime DESC")
            ->page($page)
            ->select();
    }
    /******************************************************
     * TeacherWorkTestAttr 教师任务
     ******************************************************/
    /**
     * 根据任务ID，检查时间，文档ID 查询相关信息
     * @param int $WCID 任务ID
     * @param int $checkTimes 检查次数
     * @param int $docID 任务ID
     * @return array
     * @author demo
     */
    public function teacherWorkTestAttrGetSelectByIdArr($WCID,$checkTimes,$docID){
        return $this->field('wt.TestID,ta.AttrID,ta.WTID,ta.IfRight,ta.Content,ta.Style,ta.CheckResult,ta.Suggestion')
            ->table($this->formatTable('TeacherWorkTestAttr').' ta')
            ->join('LEFT JOIN '.$this->formatTable('TeacherWorkTest').' wt on wt.WTID=ta.WTID')
            ->join('LEFT JOIN '.$this->formatTable('TestAttr').' a on a.TestID=wt.TestID')
            ->where('wt.WCID='.$WCID.' and wt.CheckTimes='.$checkTimes.' and a.DocID='.$docID)
            ->order('wt.TestID asc')
            ->select();
    }
    /******************************************************
     * UserTestKl 教师任务
     ******************************************************/
    /**
     * 根据UserName SubjectID获取某用户某学科知识点能力值情况
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @return array
     * @author demo
     */
    public function userTestKlSelectByUserNameSubject($userName,$subjectID){
        return $this->field('TestKlID,UserName,a.SubjectID,TestRecordID,a.KlID,KlName,AllAmount,RightAmount,WrongAmount,UndoAmount,KlAbility,LoadTime')
                    ->table($this->formatTable('UserTestKl').' a')
                    ->join('LEFT JOIN '.$this->formatTable('Knowledge').'b on a.KlID = b.KlID')
                    ->where('UserName ="'.$userName.'" and  a.SubjectID ='.  $subjectID)
                    ->select();
    }

    /******************************************************
     * UserCustomGroup 用户自定义组
     ******************************************************/
    /**
     * 根据用户ID 查询用户所在分组名称
     * @param $userID int 用户ID
     * @return array
     * @author demo
     */
    public function userCustomGroupSelectByUserId($userID){
        return $this->field('b.GroupName')
                    ->table($this->formatTable('User').' a')
                    ->join('LEFT JOIN '.$this->formatTable('UserCustomGroup').' b on a.CustomGroup = b.GroupID')
                    ->where('a.UserID ='.$userID)
                    ->select();
    }

    /******************************************************
     * User 根据用户分组ID获取分组名称及用户信息
     ******************************************************/

     /**
      * 根据用户分组ID获取分组名称及用户信息
      * @param string $field 所需字段
      * @param string $where 搜索条件
      * @param string $page 分页条件
      * @return array
      * @author demo
      */
    public function userSelectByPageUserGroup($field,$where,$page){
        return $this->field($field)
        ->table($this->formatTable('User').' a')
        ->join('LEFT JOIN '.$this->formatTable('UserCustomGroup').' b on b.GroupID=a.CustomGroup')
        ->join('LEFT JOIN '.$this->formatTable('UserGroup').' c on a.UserID=c.UserID')
        ->where($where)
        ->group('a.UserID')
        ->order('a.UserID DESC')
        ->page($page)
        ->select();
    }
    /**
     * 根据用户分组ID获取分组名称及用户信息
     * @param string $field 所需字段
     * @param string $where 搜索条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function userSelectByPage($field,$where,$page){
        return $this->field($field)
            ->table($this->formatTable('User').' a')
            ->join('LEFT JOIN '.$this->formatTable('UserCustomGroup').' b on b.GroupID=a.CustomGroup')
            ->join('LEFT JOIN '.$this->formatTable('School').' c on c.SchoolID=a.SchoolID')
            ->where($where)
            ->order('UserID DESC')
            ->page($page)
            ->select();
    }
    /**
     * 根据用户分组ID获取分组名称及用户信息
     * @param string $where 搜索条件
     * @return array
     * @author demo
     */
    public function userSelectByPageCount($where){
        return $this->field('a.*,b.GroupName,c.UserID')
            ->table($this->formatTable('User').' a')
            ->join('LEFT JOIN '.$this->formatTable('UserCustomGroup').' b on b.GroupID=a.CustomGroup')
            ->join('LEFT JOIN '.$this->formatTable('UserGroup').' c on a.UserID=c.UserID')
            ->where($where)
            ->group('a.UserID')
            ->count('*');
    }
    /**
     * 根据用户分组ID获取分组名称及用户信息导出
     * @param string $where 搜索条件
     * @param string $limit 限制条数
     * @return array
     * @author demo
     */
    public function userSelectByExport($where,$limit=''){
        return $this->field('a.*,b.GroupName')
            ->table($this->formatTable('User').' a')
            ->join('LEFT JOIN '.$this->formatTable('UserCustomGroup').' b on b.GroupID=a.CustomGroup')
            ->where($where)
            ->order('UserID DESC')
            ->limit($limit)
            ->select();
    }

    /**
     * 按地区统计学校教师人数
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function userTeacherTotalBySchool($where){
        return $this->field('count(distinct UserID) as TeacherTotal,a.SchoolID,b.SchoolName,b.SchoolID,b.AreaID')
            ->table($this->formatTable('User').' a')
            ->join('LEFT JOIN '.$this->formatTable('School').' b on a.SchoolID=b.SchoolID')
            ->join('LEFT JOIN '.$this->formatTable('UserIp').' c on c.SchoolID=a.SchoolID')
            ->where($where.' and a.Whois=1')
            ->group('a.SchoolID')
            ->select();
    }

    /**
     * 按地区统计学校教师人数
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function userStudentTotalBySchool($where){
        return $this->field('count(distinct UserID) as StudentTotal,a.SchoolID,b.SchoolName,b.SchoolID,b.AreaID')
            ->table($this->formatTable('User').' a')
            ->join('LEFT JOIN '.$this->formatTable('School').' b on a.SchoolID=b.SchoolID')
            ->join('LEFT JOIN '.$this->formatTable('UserIp').' c on c.SchoolID=a.SchoolID')
            ->where($where.' and a.Whois=0')
            ->group('a.SchoolID')
            ->select();
    }

    /**
     * 根据用户ID,学校ID 查询相关
     * @param $field string 所需字段
     * @param $userID int 用户ID
     * @return array
     * @author demo
     */
    public function getUserSchoolMsgByUserID($field,$userID){
        return $this->field($field)
                ->table($this->formatTable('User').' a')
                ->join('LEFT JOIN '.$this->formatTable('School').' b on a.SchoolID=b.SchoolID')
                ->where('a.UserID='.$userID)
                ->select();
    }

    /******************************
     * 下面的是大联考统计模块的独立出来的关联查询
     * 待 该模块开发完成后，完善下面内容
     *******************************/

    /******************************************************
     * TjTest 考试统计
     ******************************************************/
    /**
     * 根据试卷ID查找试卷相关信息
     * @param string $paperID 试卷ID
     * @return array
     * @author demo
     */
    public function tjTestSelectByPaperID($paperID){
        return $this->field('TestListID,tjt.TestID,tjt.MixedNo,tjt.Score,tjt.ChooseType,tar.Diff')
            ->join('LEFT JOIN '.$this->formatTable('TestAttrReal').' tar ON tar.TestID=tjt.TestID')
            ->join('LEFT JOIN '.$this->formatTable('TjPaper').' tjp ON tjp.SaveID=tjt.SaveID')
            ->where('tjp.PaperID ='.$paperID)
            ->order('TestListID ASC')
            ->table($this->formatTable('TjTest').' tjt')
            ->select();
    }
    /******************************************************
     * TjAnswer 考试统计
     ******************************************************/
    /**
     * 试题答案统计
     * @param string $where 统计条件
     * @return array
     * @author demo
     */
    public function tjAnswerCountData($where){
        return $this->field('ta.AnswerID')
                ->table($this->formatTable('TjAnswer').' ta')
                ->join('LEFT JOIN '.$this->formatTable('Test').' zt ON ta.TestID=zt.TestID')
                ->order('ta.AnswerID ASC')
                ->where($where)
                ->count();
    }
    /**
     * 查询条件下试卷作答相表关字段数据；
     * @param string $where 查询的条件
     * @param string $page 查询的条件
     * @return array
     * @author demo
     */
    public function tjAnswerPageData($where,$page){
        return $this->field('ta.*,zt.Test')
                    ->table($this->formatTable('TjAnswer').' ta')
                    ->join('LEFT JOIN '.$this->formatTable('Test').' zt ON ta.TestID=zt.TestID')
                    ->order('ta.AnswerID ASC')
                    ->page($page)
                    ->where($where)
                    ->select();
    }
    /******************************************************
     * TjClass 统计班级
     ******************************************************/
    /**
     * 按条件搜索班级统计
     * @param $where string 搜索条件
     * @return int
     * @author demo
     */
    public function tjClassSelectCount($where){
        return $this->field('tc.ClassID')
                    ->table($this->formatTable('TjClass').' tc')
                    ->join('LEFT JOIN '.$this->formatTable('TjExamClass').' tec ON tc.ClassID=tec.ClassID')
                    ->join('LEFT JOIN '.$this->formatTable('TjExam').' te ON te.ExamID=tec.ExamID')
                    ->join('LEFT JOIN '.$this->formatTable('School').' zs ON tc.SchoolID=zs.SchoolID')
                    ->where($where)
                    ->count();
    }
    /**
     * 按条件搜索班级分页查询
     * @param $where string 搜索条件
     * @param $page string 分页条件
     * @return int
     * @author demo
     */
    public function tjClassSelectPageData($where,$page){
        return $this->field('tc.ClassID,tc.ClassName,tc.AddTime,te.ExamID,te.ExamName,zs.SchoolName')
            ->table($this->formatTable('TjClass').' tc')
            ->join('LEFT JOIN '.$this->formatTable('TjExamClass').' tec ON tc.ClassID=tec.ClassID')
            ->join('LEFT JOIN '.$this->formatTable('TjExam').' te ON te.ExamID=tec.ExamID')
            ->join('LEFT JOIN '.$this->formatTable('School').' zs ON tc.SchoolID=zs.SchoolID')
            ->order('tc.ClassID DESC')
            ->page($page)
            ->where($where)
            ->select();
    }


    /******************************************************
     * TjExam 统计模块（考试统计）
     ******************************************************/


    /******************************************************
     * 组卷，作业 统计模块记录统计相关
     ******************************************************/
    /**
     * 按条件统计用户智能组卷及下载数据统计
     * @param string $where 查询条件
     * @return string
     * @author demo
     */
    public function dirTotalGroup($where){
        return $this->field('a.UserName,b.SubjectStyle,b.ComTimes,b.Whois,b.AreaID,count(*) as dirTotal,c.SchoolName,b.RealName,b.Phonecode,b.Email')
            ->table($this->formatTable('LogIntellPaper').' a')
            ->join('LEFT JOIN '.$this->formatTable('User').' b on b.UserName=a.UserName')
            ->join('LEFT JOIN '.$this->formatTable('School').' c on c.SchoolID=b.SchoolID')
            ->where($where)
            ->group('a.UserName')
            ->order('dirTotal Desc')
            ->select();
    }

    /**
     * 按条件统计用户模板组卷及下载数据统计
     * @param string $where 查询条件
     * @return string
     * @author demo
     */
    public function tplTotalGroup($where){
        return $this->field('a.UserName,b.SubjectStyle,b.ComTimes,b.Whois,b.AreaID,count(*) as tplTotal,c.SchoolName,b.RealName,b.Phonecode,b.Email')
            ->table($this->formatTable('LogTplPaper').' a')
            ->join('LEFT JOIN '.$this->formatTable('User').' b on b.UserName=a.UserName')
            ->join('LEFT JOIN '.$this->formatTable('School').' c on c.SchoolID=b.SchoolID')
            ->where($where)
            ->group('a.UserName')
            ->order('tplTotal Desc')
            ->select();
    }
    /**
     * 指定周期内创建的班级及所在区域
     * @param $startTime string 开始时间
     * @param $endTime string 结束时间
     * @return array
     * @author demo
     *
     */
    public function areaClassTotal($startTime,$endTime){
        return $this->field('a.ClassID, b.AreaID')
                    ->table($this->formatTable('ClassList').' a')
                    ->join('LEFT JOIN '.$this->formatTable('School').' b on a.SchoolID=b.SchoolID')
                    ->where('a.LoadTime >='.$startTime.' AND a.LoadTime <'.$endTime)
                    ->order('b.AreaID')
                    ->select();
    }
        /**
     * 指定周期内的用户及所在区域
     * @param $startTime string 开始时间
     * @param $endTime string 结束时间
     * @return array
     * @author demo
     *
     */
    public function areaUserTotal($startTime,$endTime){
        return $this->field('a.UserID, b.AreaID')
                    ->table($this->formatTable('User').' a')
                    ->join('LEFT JOIN '.$this->formatTable('School').' b on a.SchoolID=b.SchoolID')
                    ->where('a.LastTime >='.$startTime.' AND a.LastTime <'.$endTime)
                    ->order('b.AreaID')
                    ->select();
    }

    /******************************************************
     * TopicPaper 专题与专题试卷表模块记录相关
     ******************************************************/
    /**
     * 根据条件查询专题跟专题试卷数据总数
     * @param $where string 查询条件
     * @return array
     * @author demo
     */
    public function topicPaperSelectCount($where){
        $count = $this->field("a.TopicPaperID")
            ->table($this->formatTable('TopicPaper').' a')
            ->join('LEFT JOIN '.$this->formatTable('Topic').' b on b.TopicID=a.TopicID')
            ->where($where)
            ->count(); // 查询满足要求的总记录数
        return $count;
    }

    /**
     * 根据条件查询专题试卷跟专题
     * @param $where string 查询条件
     * @param $page string 查询分页
     * @return array
     * @author demo
     */
    public function topicPaperSelectPageByWhere($where,$page){
        return $this->field('b.TopicName, a.*')
            ->table($this->formatTable('TopicPaper').' a')
            ->join('LEFT JOIN '.$this->formatTable('Topic').' b on b.TopicID=a.TopicID')
            ->where($where)
            ->order('a.TopicPaperID desc')
            ->page($page)
            ->select();
    }

    /**
     * 查询系统知识数据集
     * @param $field string 字段
     * @param $where string 条件
     * @param $order string 排序
     * @param $page string 分页
     * @return array
     * @author demo
     */
    public function caseLoreJoinSelect($field,$where,$order,$page){
        return $this->field($field)
            ->table($this->formatTable('CaseLoreAttr').' CLA')
            ->join('LEFT JOIN '.$this->formatTable('CaseLore').' CL on CL.LoreID=CLA.LoreID')
            ->join('LEFT JOIN '.$this->formatTable('CaseLoreDoc').' CLD on CLA.DocID=CLD.DocID')
            ->where($where)
            ->order($order)
            ->page($page)
            ->select();
    }
    /**
     * 查询系统知识数据集
     * @param $field string 字段
     * @param $where string 条件
     * @param $order string 排序
     * @param $page string 分页
     * @return array
     * @author demo
     */
    public function caseCustomLoreJoinSelect($field,$where,$order,$page){
        return $this->field($field)
            ->table($this->formatTable('CaseCustomLoreAttr').' CLA')
            ->join('LEFT JOIN '.$this->formatTable('CaseCustomLore').' CL on CL.LoreID=CLA.LoreID')
            ->where($where)
            ->order($order)
            ->page($page)
            ->select();
    }
    /**
     * 作业统计列表，用于后来作业模块
     * @param $where
     * @param $page
     * @return mixed
     * @author demo
     */
    public function userWorkStatistic($where,$page){
        return $this->field('WorkID,WorkName,SubjectID,WorkStyle,WorkType,UserName,StartTime,EndTime,TestNum,LoreNum')
            ->table($this->formatTable('UserWork'))
            ->where($where)
            ->order('WorkID DESC')
            ->page($page)
            ->select();
    }
    /**
     * 根据作业ID查找作答人数
     * @param $workIDList
     * @return mixed
     * @author demo
     */
    public function userWorkDoNum($workIDList){
        return $this->field('WorkID,count(WorkID) as DoNum')
            ->table($this->formatTable('UserSendWork'))
            ->where('WorkID in ('.$workIDList.')')
            ->group('WorkID')
            ->select();
    }
    /**
     * 获取用户作业数量
     * @param $where
     * @return int
     * @author demo
     */
    public function userWorkCount($where){
        $count = $this->field('uw.WorkID')
            ->table($this->formatTable('UserWork'))
            ->where($where)
            ->count(); // 查询满足要求的总记录数
        return $count;
    }

    /**
     * 作业用户作答情况
     * @param $where
     * @return mixed
     * @author demo
     */
    public function userSendWorkInfo($where){
        $result = $this->field('usw.*,u.UserName,u.RealName,c.ClassName')
            ->table($this->formatTable('UserSendWork').' AS usw')
            ->join('LEFT JOIN '.$this->formatTable('User').' AS u ON u.UserID=usw.UserID')
            ->join('LEFT JOIN '.$this->formatTable('ClassList').' AS c ON usw.ClassID=c.ClassID')
            ->where($where)
            ->order('usw.SendTime DESC')
            ->select();
        return $result;
        }

    /**
     * 暑假作业
     * @param $where 条件
     * @param $page 分页
     * @return mixed
     * @author demo
     */
    public function userSummerWork($where,$page){
        return $this->field('u.WorkID,UserName,WorkName,TestNum,LoreNum,count(us.WorkID) as DoNum')
            ->table($this->formatTable('UserWork').' AS u')
            ->join('LEFT JOIN '.$this->formatTable('UserSendWork').' AS us ON u.WorkID=us.WorkID')
            ->where($where)
            ->group('us.WorkID')
            ->order('WorkID DESC')
            ->page($page)
            ->select();
    }

    /**
     * 暑假作业数量
     * @param $where 条件
     * @return mixed
     * @author demo
     */
    public function userSummerWorkCount($where){
        return $this->field('u.WorkID')
            ->table($this->formatTable('UserWork').' AS u')
            ->join('LEFT JOIN '.$this->formatTable('UserSendWork').' AS us ON u.WorkID=us.WorkID')
            ->where($where)
            ->group('us.WorkID')
            ->select();
    }

    /**
     * 根据结果ID查询考试相关信息
     * @param $sendID int 用户提交ID
     * @param $testID string 该作业下的试题ID
     * @return array
     * @author demo
     */
    public function userWorkRecordSelectByMsg($sendID,$testID){
        return  $this->field('TestID,AnswerText,IfRight')
            ->table($this->formatTable('UserAnswerRecord').' a')
            ->where(' a.SendID='.$sendID.' and TestID in('.$testID.')')
            ->select();

    }

    /**
     * 获取已提交作业的用户名称和批改数目
     * @param $where 查询条件
     */
    public function getSendWorkInfo($where){
        return $this->field('u.UserID,u.UserName,u.RealName,us.CorrectNum,us.SendID')
                    ->table($this->formatTable('UserSendWork').' AS us')
                    ->join('LEFT JOIN '.$this->formatTable('User').' AS u ON u.UserID=us.UserID')
                    ->where($where)
                    ->order('us.SendTime ASC')
                    ->select();
    }

    /*********************UserExp 用户经验相关*************************************/
    /**
     * 用户分享记录分成按月累计
     * @param $startTime int 开始时间
     * @param $lastTime int 结束时间
     * @return array
     * @author demo
     */
    public function getDocShareListByMonth($startTime,$lastTime){
        return $this->field('count(a.AutherID) as total,b.UserName')
            ->table($this->formatTable('DocShare').' a')
            ->join('LEFT JOIN '.$this->formatTable('User'). ' b on a.AutherID=b.UserID')
            ->where('a.ShareTime>'.$startTime.' and a.ShareTime<'.$lastTime)
            ->group('b.UserName')
            ->order('total desc')
            ->limit(5)
            ->select();
    }

    /**
     * 按条件统计总数
     * @param $where array 统计条件
     * @author demo
     */
    public function getDocShareTotal($where){
        return $this->field('a.AutherID,a.SharerID,a.ShareTime,b.DocName,b.DownID')
            ->table($this->formatTable('DocShare').' a')
            ->join('LEFT JOIN '.$this->formatTable('DocDown').' b on a.DownID=b.DownID')
            ->where($where)
            ->count();
    }

    /**
     * 按条件查看用户分享文档记录
     * @param $where array 查看条件
     * @param $limit string 限制条件
     * @author demo
     */
    public function getDocShareList($where,$limit){
        return $this->field('a.AutherID,a.SharerID,a.ShareTime,b.DocName,b.DownID,c.UserName')
            ->table($this->formatTable('DocShare').' a')
            ->join('LEFT JOIN '.$this->formatTable('DocDown').' b on a.DownID=b.DownID')
            ->join('LEFT JOIN '.$this->formatTable('User').' c on c.UserID=a.AutherID')
            ->where($where)
            ->order('a.ShareTime desc')
            ->limit($limit)
            ->select();
    }
    /*********************UserExp 用户经验相关*************************************/
    /**
     * 根据权限名及用户名，获取经验相关信息
     * @param $expName string 经验动作标示
     * @param $userID int 用户ID
     * @return array
     * @author demo
     */
    public function getUserExpByMsg($expName,$userID){
        return $this->field('a.*,b.*,c.*')
            ->table($this->formatTable('UserExp').' a')
            ->join('LEFT JOIN '.$this->formatTable('UserExpRecord').' b on a.ExpName=b.ExpTag')
            ->join('LEFT JOIN '.$this->formatTable('User').' c on c.UserID=b.UserID')
            ->where('a.ExpName="'.$expName.'" and c.UserID='.$userID)
            ->order('b.ExpLogID desc')
            ->limit(0,1)
            ->select();
    }

    /*********************LogSms 验证码日志*************************************/

    /**
     * 通过条件分页获取验证码日志
     * @param $where
     * @return mixed
     * @author demo
     */
    public function LogSmsPageData($where){
        return $this->field('a.SmsID,a.Receive,a.Status,a.AddTime,a.CodeNum,a.IfConfirm,b.UserName,b.RealName')
            ->table($this->formatTable('LogSms').' a')
            ->join('LEFT JOIN '.$this->formatTable('User').' b on a.UserID=b.UserID')
            ->where($where['data'])
            ->order('SmsID desc')
            ->page($where['page'])
            ->select();
    }

    /******************************用户校本题库文档上传管理*************************************/
    /**
     * 原创上传列表查看，文档详细情况
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function getCustomDocUploadList($where,$page){
        return $list=$this->field('a.*,b.UserName,c.AdminName as AdminName')
            ->table($this->formatTable('CustomDocUpload').' a')
            ->join('LEFT JOIN '.$this->formatTable('User').' b on a.UserID=b.UserID')
            ->join('LEFT JOIN '.$this->formatTable('Admin').' c on a.AdminID=c.AdminID')
            ->where($where)
            ->order('DUID desc')
            ->page($page)
            ->select();
    }
    /****************************微课查询*******************************/
    /**
     * 按条件查询微课总数
     * @param array $where 限制条件
     * @return array
     * @author demo
     */
    public function countMicroClass($where){
        return $this->table($this->formatTable('MicroClass').' mc')
                ->join('LEFT JOIN '.$this->formatTable('MicroClassKl').' mck on mc.MID=mck.MID')
                ->where($where)
                ->count('DISTINCT mc.MID');
    }
    /**
     * 按条件查询微课列表
     * @param array $where 限制条件
     * @param array $page 查询条数 1,20
     * @return array
     * @author demo
     */
    public function pageMicroClass($where,$page){
        return $this->field('mc.MID,mc.MName,mc.SubjectID,mc.UserID,mc.GradeID,mc.Url,mc.AddTime,mc.Remark,group_concat(mck.KlID SEPARATOR ",") as KlID')
                ->table($this->formatTable('MicroClass').' mc')
                ->join('LEFT JOIN '.$this->formatTable('MicroClassKl').' mck on mc.MID=mck.MID')
                ->where($where)
                ->group('mc.MID')
                ->page($page)
                ->order('mc.MID desc')
                ->select();
    }

    /****************************全站支出********************************/
    /**
     * 全站校本题库按月支出pay
     * @param int $limit 限制条数
     * @param int $startTime 开始时间
     * @param int $lastTime 结束时间
     * @return array
     * @author demo
     */
    public function payTotalList($limit,$startTime,$lastTime){
        return $this->field('sum(a.PayMoney) as PointTotal,count(a.UserID) as testTotal,a.UserID,b.UserName,b.RealName,b.Whois,b.UserID')
            ->table($this->formatTable('Pay').' a')
            ->join('LEFT JOIN '.$this->formatTable('User').' b on a.UserID=b.UserID')
            ->where('a.PayName="校本题库试题" and a.AddTime>'.$startTime.' and a.AddTime<'.$lastTime.' and b.Status=0')
            ->group('a.UserID')
            ->order('PointTotal Desc,testTotal Desc')
            ->limit('0,'.$limit)
            ->select();
    }

    /**
     * 全站总支出
     * @param int 条数
     * @author demo
     */
    public function payTotalAll($limit){
        return $this->field('sum(a.PayMoney) as personTotal,a.UserID')
            ->table($this->formatTable('Pay').' a')
            ->group('a.UserID')
            ->order('personTotal Desc')
            ->limit('0,'.$limit)
            ->select();
    }

    /**
     * 返回指定试题id的文档数据
     * @author demo 2015-12-21
     */
    public function selectDocByTestId($where, $field=""){
        if(empty($field)){
            $field = "d.*";
        }
        return $this->field($field)
                    ->table($this->formatTable('Doc').' d')
                    ->join('LEFT JOIN '.$this->formatTable('TestAttrReal').' r ON d.DocID=r.DocID')
                    ->where($where)
                    ->select();
    }

    /*******************用户IP模块*************************/
    /**
     * 关联ip注册日志，分页查看列表
     * @param $where 查询条件
     * @param $page 分页条件
     * @return array
     * @author demo
     */
    public function getUserIPByPage($where,$page){
        return $this->field('ip.*,count(log.RegLogID) as total')
            ->table($this->formatTable('UserIp').' ip')
            ->join('LEFT JOIN '.$this->formatTable('RegisterLog').' log on log.IPID=ip.IPID')
            ->where($where)
            ->group('ip.IPID')
            ->order('IPID desc')
            ->page($page)
            ->select();
    }

    /**
     * 返回任务大厅指定用户的任务日志列表数据
     * @param $field string 所需字段
     * @param $where string 查询条件
     * @return array
     * @author demo 2015-12-15
     */
    public function getMissionHallLogList($field, $where){
        return $this->field($field)
                        ->table($this->formatTable('MissionHallLog').' l')
                        ->join('LEFT JOIN '.$this->formatTable('MissionHallRecords').' r ON l.MHRID=r.MHRID')
                        ->join('LEFT JOIN '.$this->formatTable('MissionHallModules').' m ON m.MHTID=r.MHTID')
                        ->join('LEFT JOIN '.$this->formatTable('MissionHallTasks').' t ON t.MHTID=m.MHTID')
                        ->where($where)
                        ->select();
    }

    /**
     * 返回任务大厅日志count数量
     * @param $where 查询条件
     * @return int
     * @author demo 2015-12-15
     */
    public function countMissionHallLogRecordList($where){
        return $this ->field('count(l.MHLID) as c')
                        ->table($this->formatTable('MissionHallLog').' l')
                        ->join('LEFT JOIN '.$this->formatTable('MissionHallRecords').' r ON l.MHRID=r.MHRID')
                        ->join('LEFT JOIN '.$this->formatTable('MissionHallAttr').' a ON a.MHTID=r.MHTID')
                        ->join('LEFT JOIN '.$this->formatTable('MissionHallTasks').' t ON t.MHTID=r.MHTID')
                        ->join('LEFT JOIN '.$this->formatTable('User').' u ON u.UserID=r.UserID')
                        ->where($where)
                        ->find();
    }

    /**
     * 返回任务大厅日志数量
     * @param $field string 所需字段
     * @param $where string 查询条件
     * @param $order string 排序条件
     * @param $limit string 限制条件
     * @author demo 2015-12-15
     */
    public function getMissionHallLogRecordList($field, $where, $order, $limit){
        return $this->field($field)
                        ->table($this->formatTable('MissionHallLog').' l')
                        ->join('LEFT JOIN '.$this->formatTable('MissionHallRecords').' r ON l.MHRID=r.MHRID')
                        ->join('LEFT JOIN '.$this->formatTable('MissionHallAttr').' a ON a.MHTID=r.MHTID')
                        ->join('LEFT JOIN '.$this->formatTable('MissionHallTasks').' t ON t.MHTID=r.MHTID')
                        ->join('LEFT JOIN '.$this->formatTable('MissionHallModules').' m ON m.MHTID=r.MHTID')
                        ->join('LEFT JOIN '.$this->formatTable('User').' u ON u.UserID=r.UserID')
                        ->where($where)
                        ->order($order)
                        ->limit($limit)
                        ->select();
    }

    /**
     * 返回任务大厅领取记录数量
     * @param int 分块ID
     * @return int
     * @author demo 2015-12-15
     */
    public function countMissionHallRecordList($tid){
        return $this->field('COUNT(r.MHRID) as c')
                        ->table($this->formatTable('MissionHallRecords').' r')
                       ->where('r.MHTID='.$tid)
                       ->find();
    }

    /**
     * 返回任务大厅领取列表
     * @author demo 2015-12-15
     */
    public function getMissionHallRecordList($tid, $limit){
        return $this->field('u.UserName,r.AddTime,COUNT(l.MHLID) as count')
                       ->table($this->formatTable('MissionHallRecords').' r')
                       ->join('LEFT JOIN '.$this->formatTable('MissionHallLog').' l ON l.MHRID=r.MHRID')
                       ->join('LEFT JOIN '.$this->formatTable('User').' u ON u.UserID=r.UserID')
                       ->where('r.MHTID='.$tid)
                       ->group('r.MHRID')
                       ->order('r.MHRID DESC')
                       ->limit($limit)
                       ->select();
    }

    /**
     * 根据任务领取id返回一条zj_mission_hall_modules的数据
     * @param $repeatTask int 领取id
     * @return array
     * @author demo 2015-12-15
     */
    public function findModulesByRecordId($repeatTask){
        return $this->field('JumpUrl,EndTime')
                    ->table($this->formatTable('MissionHallRecords').' r')
                   ->join('LEFT JOIN '.$this->formatTable('MissionHallModules').' m ON m.MHTID=r.MHTID')
                   ->join('LEFT JOIN '.$this->formatTable('MissionHallTasks').' t ON t.MHTID=m.MHTID')
                   ->where("MHRID={$repeatTask}")
                   ->find();
    }


    /**
     * 获得任务领取记录 关联任务表 用户表
     * @param string $field 要获取的字段
     * @param bool $join 关联表 默认关联用户表
     * @param string $where 条件
     * @param string $order 排序
     * @param string $limit 条数
     * @return array | false
     * @author demo
     */
    public function getMissionHallRecordByLimit($field,$where,$order,$limit){
        return $this->field($field)
            ->table($this->formatTable('MissionHallRecords').' mhr')
            ->join('LEFT JOIN '.$this->formatTable('User').' u on u.UserID = mhr.UserID')
            ->join('LEFT JOIN '.$this->formatTable('MissionHallTasks').' mht on mht.MHTID = mhr.MHTID')
            ->where($where)
            ->order($order)
            ->limit($limit)
            ->select();
    }

    /**
     * 返回数据，支持分页查询，当params给定page时
     * 默认排序规则置顶，最新HOT DESC,MHTID DESC
     * @param string $field 所需字段
     * @param string $where 查询条件
     * @param string $order 排序条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function getMissionHallTasksByPage($field,$where,$order,$page){
        return $this->field($field)
            ->table($this->formatTable('MissionHallTasks').' mht')
            ->join('LEFT JOIN '.$this->formatTable('Admin').' u on u.AdminID = mht.AdminID')
            ->join('LEFT JOIN '.$this->formatTable('MissionHallAttr').' mha on mha.MHTID = mht.MHTID')
            ->where($where)
            ->order($order)
            ->page($page)
            ->select();
    }

    /**
     * 原创模板审核列表
     * @param $where string 查询条件
     * @param $limit string 限制条件
     * @return array
     * @author demo 2015-12-15
     */
    public function getOriginalityAuditList($where, $limit){
        return $this->field('a.*,u.UserName, t.Title, t.SubjectID, s.SID')
                ->table($this->formatTable('OriginalityAudit').' a')
                ->join('LEFT JOIN '.$this->formatTable('OriginalityTemplate').' t ON t.TID=a.TID')
                ->join('LEFT JOIN '.$this->formatTable('OriginalityStage').' s ON s.SID=t.SID')
                ->join('LEFT JOIN '.$this->formatTable('User').' u ON u.UserID=a.UserID')
                ->where($where)
                ->limit($limit)
                ->order('a.TID')
                ->select();
    }

    /**
     * 原创模板审核列表记录数
     * @param $where string 查询条件
     * @return int
     * @author demo 2015-12-15
     */
    public function countOriginalityAuditList($where){
        return $this->field('COUNT(AID) AS num')
                        ->table($this->formatTable('OriginalityAudit').' a')
                        ->join('LEFT JOIN '.$this->formatTable('OriginalityTemplate').' t ON t.TID=a.TID')
                        ->where($where)
                        ->find();
    }

    /************* 定制化考相关**********************/


    /**
     * 定义考试ID
     * @param int $definitionID 定义考试ID
     * @return array
     * @author demo
     */
    public function getReserveDefinitionAddressByID($definitionID){
        return $this->field('a.*,b.*')
                    ->table($this->formatTable('ReserveDefinition').' a')
                    ->join('LEFT JOIN '.$this->formatTable('ReserveDefinitionAddress').' b on a.DefinitionID=b.DefinitionID')
                    ->where('a.DefinitionID='.$definitionID)
                    ->select();
    }

    /**
     * 按条件分页查询用户地址信息
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function getReserveUserAddress($where,$page){
        return $this->field('a.*,b.UserName')
            ->table($this->formatTable('ReserveAddress').' a')
            ->join('LEFT JOIN '.$this->formatTable('ReserveUser').' b on a.UserID=b.UserID')
            ->where($where)
            ->page($page)
            ->order('AddressID DESC')
            ->select();
    }

    /**
     * 分页查看用户操作日志
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @author demo
     * @return array
     */
    public function getReserveUserLog($where,$page){
        return $this->field('a.*,c.UserName')
            ->table($this->formatTable('ReserveLog').' a')
            ->join('LEFT JOIN '.$this->formatTable('ReserveUser').' c on c.UserID=a.UserID')
            ->where($where)
            ->page($page)
            ->order('LogID DESC')
            ->select();
    }

    /**
     * 分页查看
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function getReserveDefinitionOrderMeg($where,$page){
        return $this->field('a.*,b.Title,c.UserName')
            ->table($this->formatTable('ReserveOrder').' a')
            ->join('LEFT JOIN '.$this->formatTable('ReserveDefinition').' b on a.DefinitionID=b.DefinitionID')
            ->join('LEFT JOIN '.$this->formatTable('ReserveUser').' c on c.UserID=a.UserID')
            ->where($where)
            ->page($page)
            ->order('a.OrderID DESC')
            ->select();
    }

    /**
     * 定义考试涉及任务
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function getDefinitionTask($where,$page){
        return $this->field('a.*,b.Title')
            ->table($this->formatTable('ReserveTask').' a')
            ->join('LEFT JOIN '.$this->formatTable('ReserveDefinition').' b on a.DefinitionID=b.DefinitionID')
            ->where($where)
            ->page($page)
            ->order('a.TaskID DESC')
            ->select();
    }

    /**
     * 定义考试用户模板
     * @param string $where 查询条件
     * @param string $page 分页条件
     * @return array
     * @author demo
     */
    public function getDefinitionTemplateByUser($where,$page){
        return $this->field('a.*,b.UserName')
            ->table($this->formatTable('ReserveTemplate').' a')
            ->join('LEFT JOIN '.$this->formatTable('ReserveUser').' b on a.UserID=b.UserID')
            ->where($where)
            ->page($page)
            ->order('a.TemplateID DESC')
            ->select();
    }

    /**
     * 获取用户关注或者粉丝数目
     * @author demo
     */
    public function getFollowCountByGroup($field,$where,$group){
        return $this->table($this->formatTable('UserFollow'))
                    ->field($field)
                    ->where($where)
                    ->group($group)
                    ->select();
    }

    /************************大联考相关*********************************/

    /**
     * 班级所有学生的作答信息
     * @param int $paperID 试卷ID
     * @param int $classID 班级ID
     * @return array
     * @author demo
     *
     */
    public function getTjTestRecordByPaperIDAndClassID($paperID,$classID){
        return $this->field('tja.AnswerID,tja.TestRecordID,tja.TestListID,ttr.UserID,tja.TestID,tja.MixedNo,tja.Score')
            ->table($this->formatTable('TjAnswer').' tja')
            ->join('LEFT JOIN '.$this->formatTable('TjTestRecord').' ttr ON ttr.TestRecordID=tja.TestRecordID')
            ->where(['PaperID'=>$paperID,'ClassID'=>$classID,'tja.Score'=>['neq',-1]])
            ->select();
    }

    /**
     * 查询某次考试一些班级下所有知识点能力值，用于生成班级知识点能力值列表
     * @param int $paperID 考试ID
     * @param int $classID 班级ID
     * @param int $klID 知识点ID
     * @return array
     * @author demo
     */
    public function getTjClassByPaperIDClassIDKlID($paperID,$classID,$klID){
        return  $this->field('tjk.ClassKlID as ID,tjk.ClassID as NameID,tjk.KlID,Ability,ClassName as Name,KlName')
            ->table($this->formatTable('TjClassKl').' as tjk')
            ->join('LEFT JOIN '.$this->formatTable('TjClass').' as tjc on tjc.ClassID=tjk.ClassID')
            ->join('LEFT JOIN '.$this->formatTable('Knowledge').' as zjk on tjk.KlID=zjk.KlID')
            ->where([
                'tjk.PaperID'=>$paperID,
                'tjk.ClassID'=>array('in',$classID),
                'tjk.KlID'=>array('in',$klID),
                'tjk.Ability'=>array('neq','null')
            ])
            ->select();
    }

    /**
     * 查询条件班级名学校名
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function getTjExamClassByWhere($where){
        return $this->field('tc.ClassName,tc.ClassID,zs.SchoolName')
                    ->table($this->formatTable('TjExamClass').' zec')
                    ->join('LEFT JOIN '.$this->formatTable('TjClass').' tc ON tc.ClassID=zec.ClassID')
                    ->join('LEFT JOIN '.$this->formatTable('School').' zs ON zs.SchoolID=zec.SchoolID')
                    ->where($where)
                    ->select();
    }

    /**
     * 查询条件下学校相关数据字段
     * @param string $page 页数
     * @param string $perpage 显示数量
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function getTjExamClassByWherePage($page,$perpage,$where){
        return $this->field('zec.AddTime,te.ExamID,te.ExamName,zs.Type,zs.SchoolName,zs.SchoolID,zs.SchoolAddress,zs.AreaID')
                    ->table($this->formatTable('TjExamClass').' zec')
                    ->join('LEFT JOIN '.$this->formatTable('TjExam').' te ON te.ExamID=zec.ExamID')
                    ->join('LEFT JOIN '.$this->formatTable('School').' zs ON zs.SchoolID=zec.SchoolID')
                    ->order('zec.ExamClassID DESC')
                    ->page((isset ($page) ? $page : 1) . ',' . $perpage)
                    ->group('zs.SchoolName')
                    ->where($where)
                    ->select();
    }

    /**
     * 多表查询学校列表；
     * @param int $examID 考试ID
     * @return array
     * @author demo
     */
    public function getTjExamClassByExamID($examID){
        return $this->field('t.*,c.ClassName,s.SchoolName')
                    ->table($this->formatTable('TjExamClass').' t')
                    ->join('LEFT JOIN '.$this->formatTable('TjClass').' c on c.ClassID=t.ClassID')
                    ->join('LEFT JOIN '.$this->formatTable('School').' s on s.SchoolID=t.SchoolID')
                    ->where('t.ExamID='.$examID)
                    ->order('t.ExamClassID DESC')
                    ->select();
    }

    /**
     * 多表查询单个学校
     * @param int $examID 考试ID
     * @param int $schoolID 学校ID
     * @return array
     * @author demo
     */
    public function getTjExamClassByExamIDSchoolID($examID,$schoolID){
        return $this->field('t.*,c.ClassName,s.SchoolName')
                    ->table($this->formatTable('TjExamClass').' t')
                    ->join('LEFT JOIN '.$this->formatTable('TjClass').' c on c.ClassID=t.ClassID')
                    ->join('LEFT JOIN '.$this->formatTable('School').' s on s.SchoolID=t.SchoolID')
                    ->where('t.ExamID='.$examID.' and t.SchoolID='.$schoolID)
                    ->select();
    }

    /**
     *多表查询参加此次考试的学校
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function getTjExamClassSchoolByWhere($where){
        return $this->field('s.SchoolName,t.SchoolID')
                    ->table($this->formatTable('TjExamClass').' t')
                    ->join('LEFT JOIN '.$this->formatTable('School').' s on s.SchoolID=t.SchoolID')
                    ->where($where)
                    ->group('SchoolID')
                    ->select();
    }

    /**
     * 按照考试ID，获取学校，班级的信息，用于考试统计查询雷达图条件的生成；
     * @param $field 查询返回的字段
     * @param $where 学校ID
     * @return array
     * @author demo
     */
    public function getTjExamClassByFieldWhere($field,$where){
        return $this->field($field)
            ->table($this->formatTable('TjExamClass').' as tje')
            ->join('LEFT JOIN '.$this->formatTable('TjClass').' as tjc on tjc.ClassID=tje.ClassID')
            ->where($where)
            ->group('tje.ClassID')
            ->select();
    }

    /**
     * 获取学校列表信息
     * @param string $field 所需字段
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function getTjExamClassSchoolByFieldWhere($field,$where){
        return $this->field($field)
            ->table($this->formatTable('TjExamClass').' as tje')
            ->join('LEFT JOIN '.$this->formatTable('School').' as s on s.SchoolID=tje.SchoolID')
            ->where($where)
            ->group('tje.SchoolID')
            ->select();
    }

    /**
     * 查询考试对应的试卷信息
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function getTjExamTjPaperByWhere($where){
        return $this->field('te.ExamName,tp.PaperID,tp.SubjectType,tp.SubjectID')
                    ->table($this->formatTable('TjExam').' te')
                    ->join('LEFT JOIN '.$this->formatTable('TjPaper').' tp ON te.ExamID=tp.ExamID')
                    ->where($where)
                    ->select();
    }

    /**
     * 查询条件下学校相关数据字段总条数
     * @param string $where 查询条件
     * @return int
     * @author demo
     */
    public function getTjExamClassCountByWhere($where){
        return $this->field('zec.ExamClassID')
                    ->table($this->formatTable('TjExamClass').' zec')
                    ->join('LEFT JOIN '.$this->formatTable('TjExam').' te ON te.ExamID=zec.ExamID')
                    ->join('LEFT JOIN '.$this->formatTable('School').' zs ON zs.SchoolID=zec.SchoolID')
                    ->where($where)
                    ->count();
    }
    /**
     * 多表查询考试试卷列表
     * @param int $examID
     * @return array
     * @author demo
     */
    public function getTjPaperByExamID($examID){
        return $this->field('t.*,d.SaveName')
            ->table($this->formatTable('TjPaper').' t')
            ->join('LEFT JOIN '.$this->formatTable('DocSave').' d on d.SaveID=t.SaveID')
            ->where('t.ExamID='.$examID)
            ->order('t.PaperID DESC')
            ->select();
    }

    /**
     *多表查询单个试卷
     * @param string $paperID
     * @return array
     * @author demo
     */
    public function getTjPaperByPaperID($paperID){
        return $this->field('t.*,d.SaveName')
            ->table($this->formatTable('TjPaper').' t')
            ->join('LEFT JOIN '.$this->formatTable('DocSave').' d on d.SaveID=t.SaveID')
            ->where('t.PaperID='.$paperID)
            ->select();
    }

    /**
     * 多表查询一次考试对应的试卷信息
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function getTjPaperByWhere($where){
        return $this->field('tp.PaperID,tp.SubjectID,tp.SubjectType,ds.SaveName')
            ->table($this->formatTable('TjPaper').' tp')
            ->join('LEFT JOIN '.$this->formatTable('DocSave').' ds ON tp.SaveID=ds.SaveID')
            ->where($where)
            ->select();
    }

    /**
     * 获取【考试-学科】试题信息
     * @param int $paperID 试卷ID
     * @return array
     * @author demo
     */
    public function getTjTestByPaperID($paperID){
        return  $this->field('tjt.TestListID,tjt.TestID,tjt.Score,tkr.KlID')
            ->table($this->formatTable('TjTest').' tjt')
            ->join('LEFT JOIN '.$this->formatTable('TestKlReal').' tkr ON tkr.TestID=tjt.TestID')
            ->join('LEFT JOIN '.$this->formatTable('TjPaper').' tjp ON tjp.SaveID=tjt.SaveID')
            ->where(['tjp.PaperID'=>$paperID])
            ->select();
    }

    /**
     * 获取知识点能力值信息
     * @param int $paperID 考试卷ID
     * @param int $studentID 学生ID
     * @param int $klID 知识点ID
     * @return array
     * @author demo
     */
    public function getTjStudentKlByID($paperID,$studentID,$klID){
        return $this->field('tjk.StudentKlID as ID,tjk.StudentID as NameID,tjk.KlID,Ability,StudentName as Name,KlName')
            ->table($this->formatTable('TjStudentKl').' as tjk')
            ->join('LEFT JOIN '.$this->formatTable('TjStudent').' as tjs on tjs.StudentID=tjk.StudentID')
            ->join('LEFT JOIN '.$this->formatTable('Knowledge').' as zjk on tjk.KlID=zjk.KlID')
            ->where([
                'tjk.PaperID'=>$paperID,
                'tjk.StudentID'=>array('in',$studentID),
                'tjk.KlID'=>array('in',$klID)
            ])
            ->select();
    }

    /**
     *查询条件下学生信息相关表数据
     * @param string $pageData 分页内容条件
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function getTjStudentByPage($pageData,$where){
        return $this->field('ts.*,te.ExamName,te.ExamID')
            ->table($this->formatTable('TjStudent').' ts')
            ->join('LEFT JOIN '.$this->formatTable('TjExamClass').' tec ON tec.ClassID=ts.ClassID')
            ->join('LEFT JOIN '.$this->formatTable('TjExam').' te ON te.ExamID=tec.ExamID')
            ->order('ts.StudentID DESC')
            ->page($pageData)
            ->where($where)
            ->select();
    }

    /**
     * 查询条件下学生信息相关表数据总条数
     * @param string $where
     * @return int
     * @author demo
     */
    public function getTjStudentCountByWhere($where){
        return $this->field('ts.StudentID')
            ->table($this->formatTable('TjStudent').' ts')
            ->join('LEFT JOIN '.$this->formatTable('TjExamClass').' tec ON tec.ClassID=ts.ClassID')
            ->join('LEFT JOIN '.$this->formatTable('TjExam').' te ON te.ExamID=tec.ExamID')
            ->where($where)
            ->count();
    }
    /**
     * 查询条件下学生信息
     * @param string $field 所需字段
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function getTjStudentSchoolByWhere($field,$where){
        return $this->field($field)
            ->table($this->formatTable('TjStudent').' ts')
            ->join('LEFT JOIN '.$this->formatTable('School').' s ON s.SchoolID=ts.SchoolID')
            ->where($where)
            ->select();
    }

    /**
     * 查询条件下教师信息相关表数据总数
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function getTjTeacherCount($where){
        return $this->field('tt.TeacherID')
                ->table($this->formatTable('TjTeacher').' tt')
                ->join('LEFT JOIN '.$this->formatTable('TjClassTeacher').' tct ON tct.TeacherID=tt.TeacherID')
                ->join('LEFT JOIN '.$this->formatTable('TjPaper').' tp ON tct.ExamID=tp.ExamID')
                ->join('LEFT JOIN '.$this->formatTable('TjExam').' te ON te.ExamID=tp.ExamID')
                ->where($where)
                ->count();
    }


    /**
     * 查询条件下教师信息相关表数据
     * @param string $pageData 分页条件
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function getTjTeacherByPage($pageData,$where){
        return $this->field('tp.SubjectID,tt.TeacherID,tt.ClassID,tt.TeacherName,tt.AddTime,te.ExamName,te.ExamID')
            ->table($this->formatTable('TjTeacher').' tt')
            ->join('LEFT JOIN '.$this->formatTable('TjClassTeacher').' tct ON tct.TeacherID=tt.TeacherID')
            ->join('LEFT JOIN '.$this->formatTable('TjPaper').' tp ON tct.ExamID=tp.ExamID')
            ->join('LEFT JOIN '.$this->formatTable('TjExam').' te ON te.ExamID=tp.ExamID')
            ->order('tt.TeacherID DESC')
            ->page($pageData)
            ->where($where)
            ->select();
    }
    /**
     * 查询条件下考试记录相关表数据总数
     * @param string $where 查询条件
     * @return array
     * @author demo
     *
     */
    public function getTjTestRecordCount($where){
        return $this->field('tr.TestRecordID')
            ->table($this->formatTable('TjTestRecord').' tr')
            ->join('LEFT JOIN '.$this->formatTable('School').' zs ON zs.SchoolID=tr.SchoolID')
            ->join('LEFT JOIN '.$this->formatTable('TjStudent').' zts ON zts.StudentID=tr.UserID')
            ->where($where)
            ->count();
    }

    /**
     * 查询条件下考试记录相关表数据
     * @param string $pageData 分页条件
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function getTjTestRecordPage($pageData,$where){
        return $this->field('tr.TestRecordID,tr.PaperID,tr.Status,tr.Score,tr.SubjectiveScore,tr.ObjectiveScore,tr.AddTime,zs.SchoolName,zts.StudentName')
            ->table($this->formatTable('TjTestRecord').' tr')
            ->join('LEFT JOIN '.$this->formatTable('School').' zs ON zs.SchoolID=tr.SchoolID')
            ->join('LEFT JOIN '.$this->formatTable('TjStudent').' zts ON zts.StudentID=tr.UserID')
            ->order('tr.TestRecordID DESC')
            ->page($pageData)
            ->where($where)
            ->select();
    }

    /**
     * 查询单个学校考试记录相关表数据
     * @param string $pageData 分页条件
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function getTjTestRecordPageByWhere($pageData,$where){
        return $this->field('r.*,s.StudentName')
                    ->table($this->formatTable('TjTestRecord').' r')
                    ->join('LEFT JOIN '.$this->formatTable('TjStudent').' s ON s.StudentID=r.UserID')
                    ->order('r.TestRecordID DESC')
                    ->page($pageData)
                    ->where($where)
                    ->select();
    }

    /**
     * 获取某次考试某学校下班级学生考试分数，用于生成学校ms图
     * @param int $paperID 考试试卷ID
     * @param int $schoolID 学校ID
     * @return array|null
     * @author demo
     */
    public function getTjTestRecordGroupList($paperID,$schoolID){
        return $this->field('ClassName,group_concat(Score SEPARATOR ",") as Score')
                    ->table($this->formatTable('TjTestRecord').' tjr')
                    ->join($this->formatTable('TjClass').' tjc ON tjc.ClassID=tjr.ClassID')
                    ->where([
                        'PaperID'=>$paperID,
                        'tjr.SchoolID'=>$schoolID
                    ])
                    ->group('tjr.ClassID')
                    ->select();
    }

    /**
     * 查询条件下学生信息
     * @param string $field 所需字段
     * @param string $where 查询条件
     * @return array
     * @author demo
     */
    public function getTjStudentByWhere($field,$where){
        return $this->field($field)
            ->table($this->formatTable('TjStudent').' ts')
            ->join('LEFT JOIN '.$this->formatTable('School').' s ON s.SchoolID=ts.SchoolID')
            ->where($where)
            ->select();
    }


    /**
     * 查询试卷订单
     * @author demo 16-4-22
     */
    public function getUserDocOrder($field, $where, $isCount=false){
        $condition = array();
        if($where['DocID']){
            $condition[] = "ud.DocID={$where['DocID']}";
        }
        if($where['DocName']){
            $condition[] = "d.DocName LIKE '%{$where['DocName']}%'";
        }
        if($where['UserID']){
            $condition[] = "ud.UserID={$where['UserID']}";
        }
        if($where['SubjectID']){
            $condition[] = "d.SubjectID={$where['SubjectID']}";
        }
        $start = empty($where['Start']) ? 0 : strtotime($where['Start']);
        $end = empty($where['End']) ? time() : strtotime($where['End']);
        if($where['Type']){
            $condition[] = "ud.UseTime BETWEEN {$start} AND {$end}";
        }else{
            $condition[] = "ud.AddTime BETWEEN {$start} AND {$end}";
        }
        $limit = "LIMIT ".(((int)$where['p']-1) * $where['prepage']).' '.$where['prepage'];
        if($isCount){
            return $this->table($this->formatTable('UserDocInviteCode').' ud')
                    ->join('LEFT JOIN '.$this->formatTable('Doc').' d ON d.DocID=ud.DocID')
                    ->join('LEFT JOIN '.$this->formatTable('User').' u ON u.UserID=ud.UserID')
                    ->where(implode(' AND ', $condition))
                    ->count();
        }
        return $this->field($field)
                    ->table($this->formatTable('UserDocInviteCode').' ud')
                    ->join('LEFT JOIN '.$this->formatTable('Doc').' d ON d.DocID=ud.DocID')
                    ->join('LEFT JOIN '.$this->formatTable('User').' u ON u.UserID=ud.UserID')
                    ->join('LEFT JOIN '.$this->formatTable('Admin').' a ON a.AdminID=ud.AdminID')
                    ->where(implode(' AND ', $condition))
                    ->limit($limit)
                    ->order('ud.ID DESC')
                    ->select();
    }

    /**
     * 查询试卷分段数据
     * @param array $field
     * @param array $where
     * @param boolean $isCount 是否统计数据
     * @author demo 16-4-25
     */
    public function getDocScoreSegmentList($field, $where, $isCount=false){
        $condition = array();
        if($where['DocID']){
            $condition[] = "ud.DocID={$where['DocID']}";
        }
        if($where['DocName']){
            $condition[] = "d.DocName LIKE '%{$where['DocName']}%'";
        }
        if($where['SubjectID']){
            $condition[] = "d.SubjectID={$where['SubjectID']}";
        }
        if($where['ID']){
            $condition[] = 'ud.ID IN('.$where['ID'].')';
        }
        $limit = "LIMIT ".(((int)$where['p']-1) * $where['prepage']).' '.$where['prepage'];
        if($isCount){
            return $this->table($this->formatTable('DocScoreSegment').' ud')
                    ->join('LEFT JOIN '.$this->formatTable('Doc').' d ON d.DocID=ud.DocID')
                    ->where(implode(' AND ', $condition))
                    ->count();
        }
        return $this->field($field)
                    ->table($this->formatTable('DocScoreSegment').' ud')
                    ->join('LEFT JOIN '.$this->formatTable('Doc').' d ON d.DocID=ud.DocID')
                    ->where(implode(' AND ', $condition))
                    ->limit($limit)
                    ->order('ud.DocID,ud.BeginPosition')
                    ->select();
    }

    /**
     * 返回学生测试记录及相关文档数据
     * @author demo 16-4-27
     */
    public function getDocByRecordID($record){
        return $this->table($this->formatTable('UserTestRecordAttr').' a')
                    ->field('d.AatTestStyle, d.DocID')
                    ->join('LEFT JOIN '.$this->formatTable('Doc').' d ON d.DocID=a.DocID')
                    ->where('TestRecordID='.(int)$record)
                    ->find();
    }

    /**
     * 返回自评描述
     * @author demo
     */
    public function getEvaluateDescription($record){
        return $this->table($this->formatTable('UserTestRecordAttr').' a')
                    ->field('t.EvaluateDescription,t.AnswerTitle,t.TopicName, t.JumpUrl,a.TopicPaperID')
                    ->join('LEFT JOIN '.$this->formatTable('TopicPaper').' tp ON tp.TopicPaperID=a.TopicPaperID')
                    ->join('LEFT JOIN '.$this->formatTable('Topic').' t ON t.TopicID=tp.TopicID')
                    ->where('TestRecordID='.(int)$record.' AND a.TopicPaperID > 0')
                    ->find();
    }

    /**
     * 查询作答情况
     * @author demo 16-5-23
     */
    public function getStatUserAnswer($where, $limit){
        $total = $this->table($this->formatTable('UserAnswerRecord').' a')
                    ->field('count(a.TestID) as num, count(r.UserName) as times')
                    ->join('LEFT JOIN '.$this->formatTable('UserTestRecordAttr').' r ON r.TestRecordID=a.TestRecordID')
                    ->where($where)
                    ->group('r.UserName')
                    ->select();
        $count = count((array)$total);
        if(0 == $count){
            return array(
                'count' => 0,
                'total' => array(),
                'data' => array()
            );
        }
        $result = array();
        $result['count'] = $count;
        $result['total']['num'] = 0;
        $result['total']['times'] = 0;
        foreach($total as $value){
            $result['total']['num'] += $value['num'];
            $result['total']['times'] += $value['times'];
        }
        $data = $this->table($this->formatTable('UserAnswerRecord').' a')
                    ->field('count(a.TestID) as num,r.UserName')
                    ->join('LEFT JOIN '.$this->formatTable('UserTestRecordAttr').' r ON r.TestRecordID=a.TestRecordID')
                    ->where($where)
                    ->group('r.UserName')
                    ->order('num DESC')
                    ->limit($limit)
                    ->select();
        $result['data'] = $data;
        return $result;
    }

    public function statUserAnswerDetail($where, $limit){
        $count = $this->table($this->formatTable('UserAnswerRecord').' a')
                    ->field('AnswerID')
                    ->join('LEFT JOIN '.$this->formatTable('UserTestRecordAttr').' r ON r.TestRecordID=a.TestRecordID')
                    ->where($where)
                    ->count();
        if(empty($count)){
            return array();
        }
        $data = $this->table($this->formatTable('UserAnswerRecord').' a')
                    ->field('AnswerID,a.TestID, a.LoadTime')
                    ->join('LEFT JOIN '.$this->formatTable('UserTestRecordAttr').' r ON r.TestRecordID=a.TestRecordID')
                    ->where($where)
                    ->limit($limit)
                    ->order('a.LoadTime desc')
                    ->select();
        return array(
            'count' => $count,
            'data' => $data
        );
    }

    /**
     * 返回上传图片的信息
     * @author demo 16-5-31
     */
    public function getImages($page, $prepage=20){
        $data = $this->table($this->formatTable('SpecialImage').' i')
                    ->field('i.Title,COUNT(i.LoadTime) as num,i.LoadTime, u.UserName')
                    ->join('LEFT JOIN '.$this->formatTable('User').' u ON u.UserID=i.UserID')
                    ->order('i.LoadTime desc')
                    ->group('i.LoadTime')
                    ->where('1=1')
                    ->select();
        $count = count((array)$data);
        if(empty($count)){
            return array(0, array());
        }
        $limit = ($prepage*($page-1)).','.$prepage;
        $data = $this->table($this->formatTable('SpecialImage').' i')
                    ->field('i.Title,COUNT(i.LoadTime) as num,i.LoadTime, u.UserName')
                    ->join('LEFT JOIN '.$this->formatTable('User').' u ON u.UserID=i.UserID')
                    ->order('i.LoadTime desc')
                    ->group('i.LoadTime')
                    ->where('1=1')
                    ->limit($limit)
                    ->select();
        return array($count, $data);
    }

    /**
     * 返回上传图片详细信息
     * @author demo 16-6-2
     */
    public function getImagesInfo($params, $page, $prepage=20){
        $where[] = '1=1';
        if(!isset($params['Start'])){
            $params['Start'] = 0;
        }
        if(!isset($params['End'])){
            $params['End'] = time();
        }
        $where[] = "i.LoadTime BETWEEN {$params['Start']} AND {$params['End']}";
        if($params['Title']){
            $where[] = "i.Title LIKE '%{$params['Title']}%'";
        }
        $params['UserName'] = str_replace(' ', '', $params['UserName']);
        if($params['UserName']){
            $where[] = 'u.UserName="'.$params['UserName'].'"';
        }
        if($params['Status']){
            $where[] = 'i.Status='.$params['Status'].'';
        }
        $where = implode(' AND ', $where);
        $count = $this->table($this->formatTable('SpecialImage').' i')
                    ->field('count(i.IID) as num')
                    ->join('LEFT JOIN '.$this->formatTable('User').' u ON u.UserID=i.UserID')
                    ->order('i.LoadTime desc,IID desc')
                    ->where($where)
                    ->find();
        $count = (int)$count['num'];
        if(empty($count)){
            return array(0, array());
        }
        $limit = ($prepage*($page-1)).','.$prepage;
        $data = $this->table($this->formatTable('SpecialImage').' i')
                    ->field('i.IID, i.Status, i.Title,i.LoadTime,i.LoadTime, u.UserName, i.Path')
                    ->join('LEFT JOIN '.$this->formatTable('User').' u ON u.UserID=i.UserID')
                    ->order('i.LoadTime desc,IID desc')
                    ->where($where)
                    ->limit($limit)
                    ->select();
        return array($count, $data);
    }
    /**
     * 学校下载试卷活跃度统计
     * @param int $time 开始时间
     * @return mixed
     * @author demo
     */
    public function userSchoolUseDown($time){
        return $this->query('SELECT u.SchoolID,s.SchoolName,count(p.DownID) downnum FROM zj_doc_down p right join `zj_user` u on u.UserName=p.UserName left join zj_school s on s.SchoolID=u.SchoolID WHERE p.LoadTime>'.$time.' group by u.SchoolID order by downnum desc limit 0,100',true);
    }
    /**
     * 学校用户下载试卷活跃度统计
     * @param int $time 开始时间
     * @return mixed
     * @author demo
     */
    public function userSchoolUseDownUser($time,$schoolID){
        return $this->query('SELECT u.UserID,u.UserName,u.RealName,u.Phonecode,u.Email,u.Address,s.SchoolName,count(p.DownID) downnum FROM zj_doc_down p right join `zj_user` u on u.UserName=p.UserName left join zj_school s on s.SchoolID=u.SchoolID WHERE p.LoadTime>'.$time.' and u.SchoolID='.$schoolID.' group by u.UserID order by downnum desc limit 0,100',true);
    }
}
