<?php
/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-6-10
 * Time: 下午4:34
 */
namespace AatApi\Controller;
class MyHomeworkController extends BaseController
{
    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
    }

    /**
     * 获取动态
     * @param int p 页码  true
     * @return array
     * [
     * "data"=>[
     *   [
     *       "date"=>"2015-11-11", //动态的日期 如果是当前分页第一个，或者是和前一条数据日期不一致，则有此字段
     *       "time"=>"14:35", //动态的时间
     *       "content"=>"内容"//动态的内容
     *   ]
     * ], 
     * "info"=>"success", 
     * "status"=>1
     * ]
     * @author demo
     */
    public function getNew(){
        $this->checkRequest();
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Class/classNews', $userID,$pageSize=5);
        if($IData[0]==0){
            return $this->setError($IData[1], 1);
        }
        //格式化时间和学科名和用户名
        $tempTime = [];//辅助数组
        $result = [];
        foreach ($IData[1] as $i => $iListDb) {
            $tempTime[$i]['UnixTime'] = $iListDb['LoadTime'];
            if(date('Y-m-d',$tempTime[$i]['UnixTime'])!==date('Y-m-d',$tempTime[$i-1]['UnixTime'])){
                $result[$i]['date'] = date('Y-m-d',$tempTime[$i]['UnixTime']);
            }
            $result[$i]['time'] = date('H:i', $iListDb['LoadTime']);
            $result[$i]['content'] = $iListDb['Content'];
        }
        $this->setBack($result);
    }

    /**
     * 获取未做试题列表
     * @param int p 页码 true
     * @return array
     * [
     * "data"=>[
     *   "list"=>[
     *       [
     *           "WorkID"=>"961", //作业ID，获取sendID时需要
     *           "ClassID"=>"19", //班级ID，获取sendID时需要
     *           "WorkName"=>"2015年11月2日高中语文作业", //作业名称，如果不为空，优先显示，如果为空，显示SubjectName+'作业'作为作业名
     *           "WorkStyle"=>"0", //作答形式（0在线作答 1下载作答），下载作答时提示用户去web端下载word文档
     *           "WorkType"=>"1", //作业类型（1 试题作业 2导学案发布) 如果是导学案，跳转导学案，导学案没有做的提示用户导学案到web端作答
     *           "TestNum"=>"2", //作业中的试题数量
     *           "SendID"=>"8925", //单次作业记录ID
     *           "UserName"=>"admin", //发布作业的用户的用户名
     *           "SubjectName"=>"语文", //作业学科名
     *           "StartTime"=>"11-02 18:09",//作业允许作答开始时间 
     *           "EndTime"=>"11-05 23:59", //作业允许作答结束时间
     *           "LoadDate"=>"2011-1-1",
     *           "LoadTime"=>"18:09", //作业布置的时间 
     *           "IDType"=>"send_id", //识别ID的类型（取值work_id、send_id），如果是work_id，需要请求MyHomeworkExercise-indexCreate接口，参数为wordID和classID，从而获取sendID；如果是send_id类型，直接通过Aat-MyHomeworkExercise-getTest接口获取试题
     *           "Flag"=>"out_date"//作业状态，（取值no_start、normal、out_date），no_start表示作答时间未到，界面上要有提示，normal表示正常可以作答，out_date表示作业超时，但是也可以作答，界面加一个超时的提示而已
     *       ], 
     *      ...
     *   ]
     * ], 
     * "info"=>"success", 
     * "status"=>1
     * ]
     * @author demo
     */
    public function getUndo() {
        $this->checkRequest();
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Class/undoHomework', $userID,$pageSize=5,$isApp=true);
        if($IData[0]==0){
            $this->setError($IData[1], 1);
        }else{
            $this->setBack(['list' => $IData[1]['list']]);
        }
    }

    /**
     * 获取试题记录列表
     * @param int p 页码  true
     * @return array
     * [
     * "data"=>[
     *   "list"=>[
     *       [
     *           "SendID"=>"10", //单次作业ID
     *           "Status"=>"1", //作业状态（取值 0未做 1已提交 2已完成）
     *           "WorkStyle"=>"0", //作答形式（0在线作答 1下载作答），下载作答时提示用户去web端下载word文档
     *           "WorkType"=>"1",//作业类型（1 试题作业 2导学案发布) 如果是导学案，跳转导学案，导学案没有做的提示用户导学案到web端作答 
     *           "TestNum"=>"1", 
     *           "ClassName"=>"31", 
     *           "UserName"=>"demo", //发布作业者用户名
     *           "SubjectName"=>"语文", 
     *           "StartTime"=>"07-07 16:00", //作业允许作答开始时间
     *           "EndTime"=>"07-07 23:00", //作业允许作答结束时间
     *           "SendTime"=>"09-28 19:02", //学生提交作业时间
     *           "CheckTime"=>"01月01日08:00", //老师批改作业时间
     *           "DoTime"=>1, 
     *           "CorrectRate"=>0, //作业分数，满分100，如98
     *           "LoadDate"=>"星期一", //作业布置的日期 如果是当前分页第一个，或者是和前一条数据日期不一致，则有此字段
     *           "LoadTime"=>"07月07日16:31", //作业布置的时间
     *           "WorkName"=>"作业"
     *       ], 
     *       ...
     *   ]
     * ], 
     * "info"=>"success", 
     * "status"=>1
     * ]
     * @author demo
     */
    public function getRecord() {
        $this->checkRequest();
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Class/doneHomework', $userID,$pageSize=5,$isApp=true);
        if($IData[0]==0){
            $this->setError($IData[1], 1);
        }else{
            $this->setBack(['list' => $IData[1]['list']]);
        }
    }

    /**
     * 获取某用户所有所在的班级
     * @return array
     *   [
     *   "data"=> [
     *       [
     *           "ClassID"=> "20", 
     *           "Status"=> "0", 
     *           "ClassName"=> "高三伟伟历史班"
     *       ],
     *       ...
     *   ], 
     *   "info"=> "success", 
     *   "status"=> 1
     * ]
     * [
     *   "data"=> null, 
     *   "info"=> "暂时还没有加入的班级！", 
     *   "status"=> 0
     * ]
     * @author demo
     */
    public function getClasses() {
        $this->checkRequest();
        $IData = $this->getApiAat('Class/userClasses', $this->getUserID());
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        $this->setError($IData[1], 1);
    }

    /**
     * 获取某班级的信息
     * @param int class_id 班级id  true
     * @return array
     * [
     * "data"=>[
     *   "class_info"=>[
     *       "ClassID"=>"20", 
     *       "ClassName"=>"高三伟伟历史班", 
     *       "Creator"=>"momo", 
     *       "LoadTime"=>"2015年09月28日 10:03", 
     *       "OrderNum"=>"10009", 
     *       "SchoolName"=>"河北省石家庄市长安区河北省石家庄市第十三中学", 
     *       "StudentAll"=>1, 
     *       "teacherAmount"=>1
     *   ], 
     *   "student"=>[
     *       [
     *           "UserID"=>"68", 
     *           "UserName"=>"15838201264", 
     *           "RealName"=>"周欣", 
     *           "UserPic"=>"http://192.168.4.99:8010/Uploads/userAvatar/2015/0716/55a7691444aa7535717.jpg"
     *      ]
     *   ], 
     *   "teacher"=>[
     *       [
     *           "UserName"=>"yangweiwei", 
     *           "RealName"=>"momo", 
     *           "SubjectName"=>"语文", 
     *           "isCreator"=>1
     *       ]
     *   ], 
     *   "recommend_class"=>[ ], 
     *   "ranking"=>[
     *       "last"=>[
     *           "UserID"=>"68", 
     *           "UserName"=>"15838201264", 
     *           "RealName"=>"周欣", 
     *           "UserPic"=>"http://192.168.4.99:8010/Uploads/userAvatar/2015/0716/55a7691444aa7535717.jpg"
     *       ], 
     *       "avg"=>[
     *           "UserID"=>"68", 
     *           "UserName"=>"15838201264", 
     *           "RealName"=>"周欣", 
     *           "UserPic"=>"http://192.168.4.99:8010/Uploads/userAvatar/2015/0716/55a7691444aa7535717.jpg"
     *      ], 
     *       "improve"=>[
     *           "UserID"=>"68", 
     *           "UserName"=>"15838201264", 
     *           "RealName"=>"周欣", 
     *           "UserPic"=>"http://192.168.4.99:8010/Uploads/userAvatar/2015/0716/55a7691444aa7535717.jpg"
     *       ]
     *   ]
     * ], 
     * "info"=>"success", 
     * "status"=>1
     * ]
     * @author demo
     */
    public function getClassInfo() {
        $this->checkRequest();
        $classID = $_REQUEST['class_id'];
        $IData = $this->getApiAat('Class/classInfoByID', $classID,$isApp=true);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        $this->setError($IData[1], 1);
    }

    /**
     * 获取某用户某班级的作业列表
     * @author demo
     */
    public function getClassWork() {
        $this->checkRequest();
        $classID = $_REQUEST['class_id'];
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Class/classHomework', $classID,$userID,$isApp=true);
        if($IData[0]==0){
            $this->setError($IData[1], 1);
        }else{
            $this->setBack(['list' => $IData[1]['list']]);
        }
    }

    /**
     * 申请加入班级
     * @param string class_num  老师手机号或班级编号  true
     * @return array
     * [
     *   "data"=>[
     *       [
     *           "ClassID"=>"519", 
     *           "ClassName"=>"高一空班", 
     *           "Creator":"张三",
     *           "OrderNum"=>"10429",//班级编号
     *           "SchoolFullName":"漳州市第四十三中学",
     *           "Status":"0"//申请状态 -1 可以申请 0已经加入班级 1或者2 正在审核中
     *       ]
     *   ], 
     *   "info"=>"success", 
     *   "status"=>1
     * ]
     * [
     *   "data"=>null, 
     *   "info"=>"您所加入的班级不存在，请核实输入内容！", 
     *   "status"=>0
     * ]
     * @author demo
     */
    public function newClass() {
        $this->checkRequest();
        $searchKey = $_REQUEST['class_num'];//用户输入的信息（班级编号或者手机号）
        $userID = $this->getUserID();
        $IData = $this->getApiAat('Class/searchClassList', $searchKey,$userID,$isApp=true);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        $this->setError($IData[1], 1);
    }

    /**
     * 加入班级
     * @param int cid 班级id  true
     * @return array
     *    [
     *       "data"=>null, 
     *       "info"=>"success", 
     *       "status"=>1
     *   ]
     * @author demo
     */
    public function introClass(){
        $classID=$_POST['cid'];
        $userID =$this->getUserID();
        $IData = $this->getApiAat('Class/joinClass', $classID,$userID);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        $this->setError($IData[1], 1);
    }

}