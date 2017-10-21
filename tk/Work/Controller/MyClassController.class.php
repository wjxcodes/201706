<?php
/**
 * 班级操作类
 * @date 2015年8月18日
 */
namespace Work\Controller;
class MyClassController extends BaseController{

    /**
     * 我的班级
     */
    public function myClass() {
    	$output = $this->getApiCommon('Subject/getSubjectFullList');
        $subject = json_encode($output, JSON_UNESCAPED_UNICODE);
        //载入key
        $username = $this->getCookieUserName();
        $key      = md5(C('DOC_HOST_KEY').$username.date("Y.m.d",time()));
        $gradeArray = $this->getData(array('style'=>'getSingle','cacheName'=>'gradeListSubject'));//年级
        $this->assign('gradeList',json_encode($gradeArray)); //年级内容
        $this->assign('subject', $subject); //学科
        $this->assign('key', $key); //密钥
        $this->assign('username', $username); //用户
        $this->display();
    }


    /**
     * 获取班级列表
     */
    public function getClassList(){
        $username = $this->getCookieUserName();

        if(empty($_POST['Status'])){
            $_POST['Status'] = 0;
        }
        $status   = $_POST['Status'];

        $buffer = $this->getModel('User')->selectData(
            'UserID',
            'UserName="'.$username.'"'
        );

        $userID = $buffer[0]['UserID'];
        //$userID为空做判断

        $where    = 'UserID='.$userID.' and ';
        if($status==1){//显示正常班级和受邀请班级 我的班级使用
            $where .= ' (Status=0 or Status=2) ';
        }else{//只显示正常班级
            $where .= ' Status=0 ';
        }

        $classUserBuffer = D('Base')->unionSelect('classUserSelectBy',$where);

        if(!$classUserBuffer)
            $this->setBack(array('add'));//没有班级

        $this->setBack(array('success',$classUserBuffer));
    }

    /**
     * 获取班级详细信息
     */
    public function getClassInfo(){
        $classID    = $_POST['id'];
        if(!is_numeric($classID))
            $this->setError('30301',1);//数据标识错误

        $buffer = D('ClassList')->selectData(
            'ClassID,OrderNum,Creator,LoadTime,SchoolFullName,ClassName,GradeID,StudentCount',
            'ClassID='.$classID
        );

        if(!$buffer)
            $this->setError('30734',1);


        $username   = $this->getCookieUserName();
        $userBuffer = $this->getModel('User')->selectData(
            'UserID,UserName,RealName',
            'UserName="'.$username.'"'
        );
        $userID          = $userBuffer[0]['UserID'];
        $classUserBuffer = D('ClassUser')->selectData(
            'ClassID,Status',
            'UserID='.$userID,
            'LoadTime DESC'
        );
        $status=0;
        if($classUserBuffer){
            foreach($classUserBuffer as $i=>$iClassUserBuffer){
                if($iClassUserBuffer['ClassID']==$classID){
                    $status=$iClassUserBuffer['Status'];
                    break;
                }
            }
        }
        //$buffer[0]['ClassName'] = $buffer[0]['ClassName'];
        $buffer[0]['LoadTime']  = date('Y-m-d',$buffer[0]['LoadTime']);
        $buffer[0]['IsCreator'] = 0;
        $buffer[0]['Status']    = $status;

        if($username==$buffer[0]['Creator']){
            $buffer[0]['IsCreator'] = 1;
            $buffer[0]['Creator']   = empty($userBuffer[0]['RealName']) ? $userBuffer[0]['UserName'] : $userBuffer[0]['RealName'];
        }else{
            $userBuffer = $this->getModel('User')->selectData(
                'UserID,UserName,RealName',
                'UserName="'.$buffer[0]['Creator'].'"'
            );
            $buffer[0]['Creator']=empty($userBuffer[0]['RealName']) ? $userBuffer[0]['UserName'] : $userBuffer[0]['RealName'];
        }
        //学生总数
        $buffer[0]['SCount'] = $buffer[0]['StudentCount'];// 隐藏真实字段名称 @todo 客户端字段映射
        unset($buffer[0]['StudentCount']);

        //教师总数
        $tCount= D('Base')->unionSelect('getClassTeacherCount',$classID);
        $buffer[0]['TCount'] = $tCount;


        $this->setBack(array('success',$buffer));
    }

    /**
     * 获取班级名称
     */
    public function getClassName(){
        $classID = $_POST['ClassID'];
        if(!is_numeric($classID))
            $this->setError('30301',1);//数据标识错误

        $gradeArr = SS('grade');
        $buffer   = D('ClassList')->selectData(
            '*',
            'ClassID='.$classID
        );
        if(!$buffer)
            $this->setError('30734',1);

        $className = $buffer[0]['ClassName'];
        $gradeName = $gradeArr[$buffer[0]['GradeID']]['GradeName'];
        $className = mb_substr($className,mb_strlen($gradeName,'UTF-8'),mb_strlen($className,'UTF-8')-mb_strlen($gradeName,'UTF-8')-mb_strlen('班','UTF-8'),'UTF-8');

        $output    = array();
        $output[0] = 'success';
        $output[1] = $buffer[0]['GradeID'];
        $output[2] = $className;
        $this->setBack($output);
    }

    /**
     * 修改班级名称
     */
    public function setClassName(){
        $classID   = $_POST['ClassID'];
        $gradeID   = $_POST['GradeID'];
        $username  = $this->getCookieUserName();
        $className = formatString('changeStr2Html',$_POST['ClassName']);

        if(!is_numeric($gradeID) || !is_numeric($classID))
            $this->setError('30301',1);//数据标识错误

        if($className==='')
            $this->setError('1X3013',1);

        $buffer = D('ClassList')->selectData(
            '*',
            'ClassID='.$classID
        );
        if(!$buffer)
            $this->setError('30734',1);

        if($buffer[0]['Creator']!=$username)
            $this->setError('1X3014',1);

        $oldClassName=$buffer[0]['ClassName'];
        //获取班级名称
        $gradeArr  = SS('grade');
        $className = $gradeArr[$gradeID]['GradeName'].$className.'班';
        $result    = D('ClassList')->updateData(
            array('ClassName'=>$className,'GradeID'=>$gradeID),
            'ClassID='.$classID
        );
        if($result===false)
            $this->setError('1X3015',1);
        //写入动态
        $buffer = D('ClassUser')->selectData(
            'UserID',
            'ClassID='.$classID
        );
        $userArr=array();
        foreach($buffer as $i=>$iBuffer){
            $userArr[]=$iBuffer['UserID'];
        }
        $this->classDynamic($classID,$userArr,'修改班级名称。班级名称由<span class="c_id" cid="'.$classID.'">#'.$oldClassName.'</span>改为<span class="c_id" cid="'.$classID.'">#'.$className.'</span>。');
        $this->setBack('success#$#'.$className);
    }


    /**
     * 检索学校
     */
    public function searchSchool(){
        $areaID     = $_POST['id'];
        $searchName = trim($_POST['key']);//学校名称
        //载入学校
        if(!is_numeric($areaID))
            $this->setError('30301',1);//数据标识错误

        $buffer = $this->getModel('School')->selectData(
            'SchoolID,SchoolName,Type',
            'AreaID='.$areaID.' and SchoolName like "%'.$searchName.'%" and Status=2 and Type<3',
            'OrderID ASC,SchoolID ASC'
        );
        if($buffer) {
            $this->setBack($buffer);
        }

        $this->setError('1X3101',1);
    }

    /**
     * 创建班级
     */
    public function createClass(){

        $schoolName     = $_POST['SchoolName'];
        $schoolID       = $_POST['SchoolID'];
        $areaID         = $_POST['AreaID'];
        $schoolFullName = $_POST['SchoolFullName'];
        $gradeID        = $_POST['GradeID'];
        $className      = $_POST['ClassName'];
        $subject        = $_POST['Subject'];

        $className      = formatString('changeStr2Html',$className);

        if(!$schoolID && !$schoolName){
            $this->setError('30736',1);
        }
        if(!$gradeID){
            $this->setError('30735',1);
        }
        if(!$className){
            $this->setError('1X3013',1);
        }
        if(!$subject){
            $this->setError('30730',1);
        }
        if(!$schoolID){
            //写入学校
            $buffer = $this->getModel('School')->selectData(
                'SchoolID',
                'SchoolName="'.$schoolName.'" and AreaID='.$areaID.' '
            );
            if($buffer) {
                $schoolID=$buffer[0]['SchoolID'];
            }else{
                $data=array();
                $data['SchoolName']=$schoolName;
                $data['AreaID']=$areaID;
                $data['Status']=0;
                $schoolID = $this->getModel('School')->insertData(
                    $data
                );
            }
        }

        if(!is_numeric($schoolID)){
            $this->setError('1X3017',1);
        }

        //获取学科id
        $gradeArr   = SS('grade');
        $className  = $gradeArr[$gradeID]['GradeName'].$className.'班';
        $subjectArr = SS('subject');
        $subjectID  = '';
        foreach($subjectArr as $i=>$iSubjectArr){
            if($iSubjectArr['SubjectName']==$subject && $iSubjectArr['PID']==$gradeArr[$gradeID]['SubjectID'])
                $subjectID=$i;
        }
        if(empty($subjectID))
            $this->setError('1X3018',1);

        //生成班级编号
        $orderNum = D('ClassList')->maxData(
            'OrderNum'
        );
        if($orderNum<10001) {
            $orderNum=10001;
        } else {
            $orderNum++;
        }

        //生成学生初始密码
        $pwd=rand(100001,999999);

        $username=$this->getCookieUserName();
        //构建班级数据
        $data=array();
        $data['ClassName'] = $className;
        $data['Creator']   = $username;
        $data['LoadTime']  = time();
        $data['OrderNum']  = $orderNum;
        $data['SchoolID']  = $schoolID;
        $data['GradeID']   = $gradeID;
        $data['ClassPwd']  = $pwd;
        $data['SchoolFullName'] = $schoolFullName;

        $classID=D('ClassList')->insertData(
            $data
        );
        if($classID===false)
            $this->setError('1X3019',1);

        //写入动态
        $buffer = $this->getModel('User')->selectData(
            'UserID',
            'UserName="'.$username.'"'
        );
        //写入班级人员
        $data=array();
        $data['ClassID']   = $classID;
        $data['UserID']    = $buffer[0]['UserID'];
        $data['Status']    = 0;
        $data['SubjectID'] = $subjectID;
        $data['LoadTime']  = time();
        D('ClassUser')->insertData(
            $data
        );
        $this->classDynamic($classID,array($buffer[0]['UserID']),'创建班级<span class="c_id" cid="'.$classID.'">#'.$className.'</span>。');
        //任务完成
        $this->getModel('MissionHallRecords')->finishTask($this->getCookieUserID());
        $this->setBack('success|'.$classID.'|'.$className);
    }

    /**
     * 加入已有班级
     */
    public function joinClass(){
        $classOrder = $_POST['classorder'];
        $subject    = $_POST['subject'];
        $username   = $this->getCookieUserName();

        if(!is_numeric($classOrder)){
            $this->setError('30301',1);//数据标识错误
        }
        //获取班级信息
        $buffer = D('ClassList')->selectData(
            '*',
            'OrderNum='.$classOrder
        );
        if(!$buffer)
            $this->setError('30734',1);

        $gradeID   = $buffer[0]['GradeID'];
        $classID   = $buffer[0]['ClassID'];
        $className = $buffer[0]['ClassName'];
        $creator   = $buffer[0]['Creator'];

        $buffer = $this->getModel('User')->selectData(
            'UserID',
            'UserName="'.$username.'"'
        );
        $userID=$buffer[0]['UserID'];

        //判断班级用户关系表

        $buffer=D('ClassUser')->selectData(
            '*',
            'ClassID='.$classID.' and UserID="'.$userID.'"'
        );
        if($buffer)
            $this->setError('1X3020',1);

        //获取学科id
        $gradeArr   = SS('grade');
        $subjectArr = SS('subject');
        $subjectID  = '';
        foreach($subjectArr as $i=>$iSubjectArr){
            if($iSubjectArr['SubjectName']==$subject && $iSubjectArr['PID']==$gradeArr[$gradeID]['SubjectID'])
                $subjectID=$i;
        }
        if(empty($subjectID))
            $this->setError('1X3018',1);

        $data=array();
        $data['ClassID']   = $classID;
        $data['SubjectID'] = $subjectID;
        $data['UserID']    = $userID;
        $data['Status']    = 1;
        $data['LoadTime']  = time();

        $result=D('ClassUser')->insertData(
            $data
        );
        if($result===false)
            $this->setError('1X3021',1);
        //写入动态
        $buffer = $this->getModel('User')->selectData(
            'UserID',
            'UserName="'.$creator.'"'
        );
        $creatorID=$buffer[0]['UserID'];

        $this->classDynamic($classID,array($creatorID,$userID),'申请加入班级<span class="c_id" cid="'.$classID.'">#'.$className.'</span>。');
        $this->setBack('success');
    }

    /**
     * 退出班级
     * @param int $ClassID 班级id
     * @param string $pwd 账户密码
     * @param int $pwd 用户id
     * @return string 识别符号
     */
    public function exitClass(){
        $pwd     = $_POST['pwd'];
        $classID = $_POST['ClassID'];
        $uid     = $_POST['uid'];

        if(!$classID){
            $this->setError('1X3022',1);
        }

        $username=$this->getCookieUserName();
        //检查用户是否是管理员
        $buffer = $this->getModel('User')->selectData(
            'UserID,Password',
            'UserName="'.$username.'" and Status=0'
        );

        if(!$buffer)
            $this->setError('1X3023',1);

        $userID   = $buffer[0]['UserID'];
        $password = $buffer[0]['Password'];
        $buffer = D('ClassList')->selectData(
            '*',
            'ClassID ='.$classID
        );

        if(!$buffer)
            $this->setError('30734',1);

        $creator = $buffer[0]['Creator'];
        //班级下的所有用户
        $buffer = D('ClassUser')->selectData(
            'UserID',
            'ClassID='.$classID
        );
        $userArr = array();
        foreach($buffer as $i=>$iBuffer){
            $userArr[]=$iBuffer['UserID'];
        }

        if($creator==$username){
            if($uid && $uid!=$userID){
                if(D('ClassUser')->deleteData(
                        'ClassID='.$classID.' and UserID='.$uid)===false){
                    $this->setError('1X3024',1);
                }else{
                    $buffer = $this->getModel('User')->selectData(
                        'UserName,RealName',
                        'UserID='.$uid
                    );
                    if(!$buffer) $this->setError('30214',1);
                    $uName=empty($buffer[0]['RealName']) ? $buffer[0]['UserName'] : $buffer[0]['RealName'];
                    $this->classDynamic($classID,$userArr,'把<span class="u_id" uid="'.$uid.'">@'.$uName.'</span>请出班级。');
                    $this->setBack('success');
                }
            }

            if ($password!=md5($username.$pwd) && $password!=md5($pwd)){
                $this->setError('30804',1);
            }
            if(strlen($pwd)<6){
                $this->setError('1X3026',1);
            }
            //删除班级表
            if(D('ClassList')->deleteData(
                    'ClassID='.$classID.' ')===false){
                $this->setError('30302',1);
            }else{
                //删除班级用户关系表
                $buffer = D('ClassUser')->selectData(
                    '*',
                    'ClassID='.$classID.' '
                );
                if($buffer){
                    $userIDList=array();
                    foreach($buffer as $i=>$iBuffer){
                        $userIDList[]=$iBuffer['UserID'];
                    }
                    $buffer = $this->getModel('User')->selectData(
                        'UserID,UserName,OrderNum',
                        'UserID in ('.implode(',',$userIDList).')'
                    );
                    $userIDList=array();
                    if($buffer){
                        foreach($buffer as $i=>$iBuffer){
                            if($iBuffer['UserName']==$iBuffer['OrderNum']) $userIDList[]=$iBuffer['UserID'];
                        }
                        if($userIDList){
                            $this->getModel('User')->deleteData(
                                'UserID in ('.implode(',',$userIDList).')'
                            );
                        }
                    }
                    if(D('ClassUser')->deleteData(
                            'ClassID='.$classID.' ')===false){
                        $this->setError('30302',1);
                    }
                }
                $this->classDynamic($classID,$userArr,'解散班级!');
                $this->setBack('success');
            }
        }else{
            //删除班级用户关系表
            if(D('ClassUser')->deleteData(
                    'ClassID='.$classID.' and UserID="'.$userID.'"')===false){
                $this->setError('30302',1);
            }else{
                $this->classDynamic($classID,$userArr,'退出班级!');
                $this->setBack('success');
            }
        }
    }

    /**
     * 载入学生数据
     */
    public function getStudentList(){
        $classID  = $_POST['ClassID'];
        $username = $this->getCookieUserName();

        //检查当前用户是否是创建者
        $buffer=D('ClassList')->selectData(
            'ClassName',
            'ClassID='.$classID.' and Creator="'.$username.'"'
        );
        $flag=0;
        if($buffer){
            $flag=1;
        }

        //增加where条件，如果学生申请入班，未审核通过，布置作业时将不显示，class_user Status = 0 状态
        $where='c.ClassID='.$classID.' and u.Whois=0 and u.Status=0 ';
        if($_POST['Status']!='all'){
            $where.='and c.Status=0';
        }
        $buffer=D('Base')->unionSelect('classUserSelectByWhere',$where);
        if(!$buffer)
            $this->setBack('没有学生数据！请添加学生');

        $output=array();
        foreach($buffer as $i=>$iBuffer){
            $output[$i]['UserID']   = $iBuffer['UserID'];
            $output[$i]['OrderNum'] = $iBuffer['OrderNum'];
            $output[$i]['UserName'] = $iBuffer['UserName'];
            $output[$i]['TmpPwd']   = $iBuffer['TmpPwd']==0 ? '学生个人设定' : $iBuffer['TmpPwd'];
            $output[$i]['RealName'] = empty($iBuffer['RealName']) ? $iBuffer['UserName'] : $iBuffer['RealName'];
            $output[$i]['LastTime'] = $iBuffer['LastTime']==0 ? '学生还未激活帐号' : date('Y-m-d H:i:s',$iBuffer['LastTime']);
            $output[$i]['Status']   = $iBuffer['Status'];
            if($flag){
                $output[$i]['IsCreator']=1;
            } else {
                $output[$i]['IsCreator']=0;
            }
            if($iBuffer['UserName']==$iBuffer['OrderNum']){
                $output[$i]['IsDel']=1;
            } else {
                $output[$i]['IsDel']=0;
            }
        }
        $this->setBack(array('success',$output));
    }

    /**
     * 载入教师数据
     */
    public function getTeacherList(){
        $classID  = $_POST['ClassID'];
        $username = $this->getCookieUserName();

        if(!is_numeric($classID))
            $this->setError('30301',1);//数据标识错误
        //检查当前用户是否是创建者
        $buffer=D('ClassList')->selectData(
            'Creator',
            'ClassID='.$classID
        );
        $creator=$buffer[0]['Creator'];
        $flag=0;
        if($creator==$username){
            $flag=1;
        }

        $buffer = D('Base')->unionSelect('classUserSelectById',$classID,'1');
        if(!$buffer)
            $this->setError('1X3081',1);

        $output     = array();
        $subjectArr = SS('subject');

        foreach($buffer as $i=>$iBuffer){
            $output[$i]['UserID']   = $iBuffer['UserID'];
            $output[$i]['RealName'] = $iBuffer['RealName']=="" ? formatString('hiddenUserName',$iBuffer['UserName']) : $iBuffer['RealName'];
            $output[$i]['LastTime'] = date('Y-m-d H:i:s',$iBuffer['LastTime']);
            $output[$i]['LoadTime'] = date('Y-m-d H:i:s',$iBuffer['LoadTime']);
            //课目不存在 统一设为未知
            if(!empty($subjectArr[$iBuffer['SubjectID']]['SubjectName'])){
                $output[$i]['SubjectName'] = $subjectArr[$iBuffer['SubjectID']]['SubjectName'];
            }else{
                $output[$i]['SubjectName'] = '未知';
            }
            $output[$i]['Status']   = $iBuffer['Status'];
            if($creator==$iBuffer['UserName']) {
                $output[$i]['IsCreatorMe'] = 1;
            } else {
                $output[$i]['IsCreatorMe'] = 0;
            }
            if($flag) {
                $output[$i]['IsCreator'] = 1;
            } else {
                $output[$i]['IsCreator'] = 0;
            }
            if($username==$iBuffer['UserName']) {
                $output[$i]['IsMe']=1;
            } else {
                $output[$i]['IsMe']=0;
            }
        }
        $this->setBack(array('success',$output));
    }

    /**
     * 获取班级动态
     */
    public function getClassDynamic(){
        $page     = $_POST['page'];
        $classID  = $_POST['ClassID'];
        $username = $this->getCookieUserName();

        if(!is_numeric($page) || $page<1) $page=1;
        if(!is_numeric($classID))
            $this->setError('30301',1);//数据标识错误

        $perPage  = 10;
        $buffer = $this->getModel('User')->getInfoByName($username);
        $userID   = $buffer[0]['UserID'];

        $dynamic=$this->getModel('Dynamic');
        $dynamicCon = $dynamic->getDynamicType();;
        $page=$page.','.$perPage;

        $buffer = $dynamic->unionSelect('dynameicToSelectBy',$dynamicCon['Class'], $userID, $classID, $page);
        $have=1; //是否还有动态
        $show=1; //当前是否有动态
        if(!$buffer){
            $show=0;
        } else{
            foreach($buffer as $i=>$iBuffer){
                $buffer[$i]['LoadTime']=date('Y-m-d H:i:s',$iBuffer['LoadTime']);
            }
        }
        if(!$buffer || count($buffer)<$perPage) $have=0;

        $this->setBack(array('success',$buffer,$show,$have));
    }

    /**
     * 添加学生数据 此方法对应三种上传方式
     */
    public function createStudent(){
        $con      = $_POST['con'];
        $num      = $_POST['num'];
        $classID  = $_POST['ClassID'];
        $postname = $_POST['user'];//问题

        //判断是否有权限添加学生
        $con      = formatString('stripTags',$con);
        $arr      = array();
        if($con){
            $arr=explode("\n",str_replace("\r",'',$con));
            $arr=array_filter($arr);
            if(count($arr)==0) $this->setError('30304',1);
        }else if(is_numeric($num)){
            if($num<1) $this->setError('30304',1);
            $arr=array_fill(0,$num,'');
        }else{
            $this->setError('30304',1);
        }
        $output=array();
        $output=$this->createStudent2db($arr,1,$classID,$postname);
        if(!is_array($output)) $this->setBack(array('error',$output));
        //D('MissionHallRecords')->finishTask($this->getCookieUserID());//任务完成
        $this->setBack($output);
    }

    /**
     * 添加学生数据的具体处理
     * @param array $arr 用户名称数组
     * @param int $ifCookie 用户名是否来自cookie
     * @pramm int $classID 班级id
     * @param string $postname 用户名称
     * @return array
     */
    private function createStudent2db($arr,$ifCookie=0,$classID,$postname){
        if($ifCookie)
            $username=$this->getCookieUserName();
        else{
            $username=$postname;
        }

        $buffer=D('ClassList')->selectData(
            '*',
            'ClassID='.$classID
        );
        if(!$buffer)
            return '班级不存在！';

        if($buffer[0]['Creator']!=$username)
            return '只有班级创建者才能添加学生！';
        $schoolID     = $buffer[0]['SchoolID']; //所属学校
        $gradeID      = $buffer[0]['GradeID']; //所属年级
        //$studentCount = $buffer[0]['StudentCount'];
        $password     = $buffer[0]['ClassPwd'];

        //获取班级下的真实学生数量
        $studentCount=D('ClassUser')->selectCount(
            'ClassID='.$classID.' and Status=0 and SubjectID=0',
            'CUID'
        );

        //地区
        $buffer = $this->getModel('School')->selectData(
            'AreaID',
            'SchoolID='.$schoolID
        );
        $areaID=$buffer[0]['AreaID'];

        if(($studentCount+count($arr))>100)
            return '班级人数不能超过100！';

        $user    = $this->getModel('User');
        $autoInc = $this->getModel('AutoInc');

        $orderNum=array();
        foreach($arr as $i=>$iArr){
            $orderNum[$i]=$autoInc->getOrderNum();
        }
        foreach($arr as $i=>$iArr){
            $data = array();
            $data['UserName'] = $orderNum[$i]."@forClass.com";
            $data['Password'] = md5($orderNum[$i]."@forClass.com".$password);
            $data['RealName'] = $iArr;
            $data['Sex']      = 0;
            $data['Phonecode']= '';
            $data['Email']    = '';
            $data['Address']  = '';
            $data['PostCode'] = '';
            $data['LoadDate'] = time();
            $data['LastIP']   = '';
            $data['SaveCode'] = $user->saveCode();
            $data['TmpPwd']   = $password;
            $data['OrderNum'] = $orderNum[$i];
            $data['AreaID']   = $areaID;
            $data['SchoolID'] = $schoolID;
            $data['GradeID']  = $gradeID;
            $userID=$user->insertData(
                $data
            );
            if($userID===false)
                return '生成学号失败';

            //插入classlist
            $data = array();
            $data['ClassID']   = $classID;
            $data['UserID']    = $userID;
            $data['Status']    = 0;
            $data['SubjectID'] = 0;
            $data['LoadTime']  = time();
            D('ClassUser')->insertData(
                $data
            );
            $data = array();
            $data['UserID']   = $userID;
            $data['OrderNum'] = $orderNum[$i];
			$data['UserName'] = $orderNum[$i]."@forClass.com";
            $data['TmpPwd']   = $password;
            $data['RealName'] = $iArr;
            $data['LastTime'] = '学生还未激活帐号';
            $output[]         = $data;

            $studentCount++;
        }

        $where='ClassID='.$classID;
        //记录班级学生总数
        D('Base')->unionSelect('conAddData','ClassList', 'StudentCount='.$studentCount, $where);
        $buffer=$user->selectData(
            'UserID',
            'UserName="'.$username.'"'
        );
        $userID=$buffer[0]['UserID'];
        $this->classDynamic($classID,array($userID),'添加学生账户!',$username);

        return array('success',$output);
    }

    /**
     * 删除班级内学生
     */
    public function delStu2Class(){
        $userID   = $_POST['id'];
        $classID  = $_POST['ClassID'];
        $username = $this->getCookieUserName();

        $buffer=D('ClassList')->selectData(
            '*',
            'ClassID='.$classID
        );
        $studentNum = $buffer[0]['StudentCount']; //班级学生数量

        if(!$buffer)
            $this->setError('30734',1);

        if($buffer[0]['Creator']!=$username)
            $this->setError('1X3027',1);

        $buffer=D('ClassUser')->selectData(
            'UserID',
            'ClassID='.$classID
        );
        $userArr=array();
        foreach($buffer as $i=>$iBuffer){
            $userArr[]=$iBuffer['UserID'];
        }
        $result=D('ClassUser')->deleteData(
            'UserID='.$userID.' and ClassID='.$classID
        );
        if($result===false)
            $this->setError('1X3028',1);
        if($result===0){ //防止重复提交
            $this->setBack('success');
        }

        //写入动态
        //判断学生是否有自己设定
        $buffer = $this->getModel('User')->selectData(
            'UserName,RealName,OrderNum',
            'UserID='.$userID
        );
        if($buffer[0]['UserName']==$buffer[0]['OrderNum']){
            $this->getModel('User')->deleteData(
                'UserID='.$userID
            );
            if(in_array($userID,$userArr)){
                unset($userArr[array_search($userID,$userArr)]);
            }
        }

        //更改班级学生数量
        $studentNum=$studentNum-1;
        if($studentNum<1) $studentNum=0;
        D('ClassList')->updateData(array('StudentCount'=>$studentNum),'ClassID='.$classID);

        $uName=empty($buffer[0]['RealName']) ? $buffer[0]['UserName'] : $buffer[0]['RealName'];
        $this->classDynamic($classID,$userArr,'把学生<span class="u_id" uid="'.$userID.'">@'.$uName.'</span>请出班级!');
        $this->setBack('success');
    }

    /**
     * 重置班级内学生密码
     */
    public function resetStuPwd(){
        $userID   = $_POST['id'];
        $classID  = $_POST['ClassID'];
        $username = $this->getCookieUserName();

        $buffer=D('ClassList')->selectData(
            '*',
            'ClassID='.$classID
        );
        if(!$buffer)
            $this->setError('30734',1);

        if($buffer[0]['Creator']!=$username)
            $this->setError('1X3029',1);

        $password = $buffer[0]['ClassPwd'];
        $user = $this->getModel('User');
        $buffer   = $user->selectData(
            'User',
            'UserID',
            'UserName="'.$username.'"'
        );
        $uid    = $buffer[0]['UserID'];
        $buffer = $user->selectData(
            'User',
            'UserName',
            'UserID='.$userID
        );
        $username = $buffer[0]['UserName'];
        $data     = array();
        $data['Password'] = md5($username.$password);
        $data['TmpPwd']   = $password;

        $result=$user->updateData(
            $data,
            'UserID='.$userID
        );
        if($result===false)
            $this->setError('1X3030',1);

        //写入动态
        $this->classDynamic($classID,array($uid),'重置了学生密码!');
        $this->setBack('success|'.$password);
    }

    /**
     * 审核学生进入班级
     */
    public function checkStudent(){
        $userID   = $_POST['id'];
        $classID  = $_POST['ClassID'];
        $username = $this->getCookieUserName();
        $buffer = $this->getModel('User')->selectData(
            'UserName,RealName,Status',
            'UserID='.$userID
        );
        if(!$buffer)
            $this->setError('30734',1);

        $uName  = empty($buffer[0]['RealName']) ? $buffer[0]['UserName'] : $buffer[0]['RealName'];
        $buffer = D('ClassList')->selectData(
            '*',
            'ClassID='.$classID
        );
        if(!$buffer)
            $this->setError('30214',1);

        if($buffer[0]['Creator']!=$username)
            $this->setError('1X3031',1);

        $result=D('ClassUser')->updateData(
            array('Status'=>0),
            'UserID='.$userID
        );
        if($result===false)
            $this->setError('1X3032',1);

        //写入动态
        $buffer=D('ClassUser')->selectData(
            'UserID',
            'ClassID='.$classID
        );

        //记录班级学生总数
        D('Base')->unionSelect('conAddData',
            'ClassList',
            'StudentCount=StudentCount+1',
            'ClassID='.$classID
        );

        $userArr=array();
        foreach($buffer as $i=>$iBuffer){
            $userArr[]=$iBuffer['UserID'];
        }
        $this->classDynamic($classID,$userArr,'通过了学生<span class="u_id" uid="'.$userID.'">@'.$uName.'</span>的入班申请!');
        $this->setBack('success');
    }

    /**
     * 下载学生列表
     */
    public function downStudentList(){
        $classID  = $_POST['ClassID'];
        $username = $this->getCookieUserName();

        //验证班级是否存在
        $buffer=D('ClassList')->selectData(
            'ClassName,Creator,GradeID',
            'ClassID='.$classID
        );
        if(!$buffer)
            $this->setError('30734',1);
        $creator = $buffer[0]['Creator'];

        //班级名称
        $gradeArr  = SS('grade');
        $className = $buffer[0]['ClassName'];

        //验证用户是否有权限下载
        $where  = 'c.ClassID='.$classID.' and u.Whois=0 and u.Status=0 and c.Status=0';
        $buffer = D('Base')->unionSelect('classUserSelectByWhere',$where);
        $flag=0;
        if($creator==$username){
            $flag=1;
        }
        if(!$buffer){
            $this->setError('1X3035',1);
        }
        if(!$flag){
            $this->setError('1X3034',1);
        }
        $output='<h2>学生的账号密码 </h2><h5>(备注：已登录过的学生密码不显示)</h5>'.
            '<table class=MsoNormalTable border=1 cellspacing=0 cellpadding=10 width=500>'.
            '<tr>'.
            ' <td width=20 style="width:15.0pt;border:solid black 1.0pt;mso-border-alt:  solid black .75pt;padding:0cm 0cm 0cm 0cm;height:27.0pt"></td>'.
            ' <td width=60 style="width:45.0pt;border:solid black 1.0pt;border-left:none; mso-border-left-alt:solid black .75pt;mso-border-alt:solid black .75pt; padding:0cm 0cm 0cm 0cm;height:27.0pt" align=center>学号</td>'.
            ' <td width=60 style="width:45.0pt;border:solid black 1.0pt;border-left:none; mso-border-left-alt:solid black .75pt;mso-border-alt:solid black .75pt; padding:0cm 0cm 0cm 0cm;height:27.0pt" align=center>密码</td>'.
            ' <td width=80 style="width:60.0pt;border:solid black 1.0pt;border-left:none; mso-border-left-alt:solid black .75pt;mso-border-alt:solid black .75pt; padding:0cm 0cm 0cm 0cm;height:27.0pt" align=center>学生姓名</td>'.
            '</tr>';
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                $output.='<tr>'.
                    ' <td width=20 style="width:15.0pt;border:solid black 1.0pt;mso-border-alt:  solid black .75pt;padding:0cm 0cm 0cm 0cm;height:27.0pt" align=center>'.($i+1).'</td>'.
                    ' <td width=60 style="width:45.0pt;border:solid black 1.0pt;border-left:none; mso-border-left-alt:solid black .75pt;mso-border-alt:solid black .75pt; padding:0cm 0cm 0cm 0cm;height:27.0pt" align=center>'.$iBuffer['OrderNum'].'</td>'.
                    ' <td width=60 style="width:45.0pt;border:solid black 1.0pt;border-left:none; mso-border-left-alt:solid black .75pt;mso-border-alt:solid black .75pt; padding:0cm 0cm 0cm 0cm;height:27.0pt" align=center>'.($iBuffer['TmpPwd']==0 ? "学生个人设定" : $iBuffer['TmpPwd']).'</td>'.
                    ' <td width=80 style="width:60.0pt;border:solid black 1.0pt;border-left:none; mso-border-left-alt:solid black .75pt;mso-border-alt:solid black .75pt; padding:0cm 0cm 0cm 0cm;height:27.0pt" align=center>'.$iBuffer['RealName'].'</td>'.
                    '</tr>';
            }
        }
        $output.='</table>';
        $doc = $this->getModel('Doc');
        $output=$doc->html2word(iconv('utf-8', 'GBK//IGNORE', $output),14);
        $style='.doc';
        $host=C('WLN_DOC_HOST');
        if($host){
            $urlPath=R('Common/UploadLayer/setWordDocument',array($output ,substr($style,1)));
            //验证下载word是否正常
            if(strstr($urlPath,'error') || !R('Common/UploadLayer/checkDownUrl',array($urlPath))){
                $this->setError('30501',1); //文档生成失败，请重试!
            }

            $url=$host.R('Common/UploadLayer/getDocServerUrl',array($urlPath,'down','',$className));
            $this->setBack('success|'.$url);
        }else{
            $hostIn=C('WLN_HTTP');
            $ePath='/Uploads/mht/'.date('Y/md/',time());
            $path=realpath('./').$ePath;
            if(!file_exists($path)) $doc->createpath($path);

            $content=$doc->getWordDocument( $output ,$hostIn);
            $docPath=$path.$className.'.mht';
            $ePath=$ePath.$className.$style;
            file_put_contents(iconv('UTF-8','GBK//IGNORE',$docPath),$content);

            //$newPath=$doc->htmltoword(iconv('UTF-8', 'GBK//IGNORE', $docPath),substr($style,1));未使用
            unlink(iconv('UTF-8','GBK//IGNORE',$docPath));
            $this->setBack('success|'.$ePath);
        }
    }

    /**
     * 邀请教师
     */
    public function addTea2Class() {
        $userInfo = $_POST['UserName'];
        $classID  = $_POST['ClassID'];
        $username = $this->getCookieUserName();

        if(empty($userInfo))
            $this->setError('30802',1);
        //验证班级
        $buffer=D('ClassList')->selectData(
            'ClassName',
            'ClassID='.$classID
        );
        if(!$buffer)
            $this->setError('30734',1);

        $className = $buffer[0]['ClassName'];

        //获取userid
        $buffer = $this->getModel('User')->selectData(
            'UserID',
            'UserName="'.$username.'"'
        );
        $userID = $buffer[0]['UserID'];

        //获取班级下教师
        $buffer=D('ClassUser')->selectData(
            'UserID,SubjectID,Status',
            'ClassID='.$classID
        );
        $userList  = array(); //已进入教师列表
        $userList2 = array(); //所有教师列表
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                if($iBuffer['SubjectID']!=0 and $iBuffer['Status']==0) $userList[]=$iBuffer['UserID'];
                if($iBuffer['SubjectID']!=0) $userList2[]=$iBuffer['UserID'];
            }
        }
        if(!in_array($userID,$userList)){
            $this->setError('1X3037',1);
        }
        //获取用户信息
        $user = $this->getModel('User');
        $buffer=$user->selectData(
            'UserID,UserName,RealName,Status,Whois,LastTime,SubjectStyle',
            'UserName="'.$userInfo.'"'
        );
        if(!$buffer){
            if(is_numeric($userInfo)){
                $buffer=$user->selectData(
                    'UserID,UserName,RealName,Status,Whois,LastTime,SubjectStyle',
                    'Phonecode="'.$userInfo.'"'
                );
            }else{
                $buffer=$user->selectData(
                    'UserID,UserName,RealName,Status,Whois,LastTime,SubjectStyle',
                    'Email="'.$userInfo.'"'
                );
            }
        }
        if(!$buffer) $this->setError('1X3038',1);
        if($buffer[0]['Status']==1) $this->setError('1X3039',1);
        if($buffer[0]['Whois']!=1) $this->setError('1X3040',1);
        $newUserID=$buffer[0]['UserID'];
        if(in_array($newUserID,$userList2)){
            $this->setError('1X3041',1);
        }
        //加入教师
        $data=array();
        $data['ClassID']=$classID;
        $data['UserID']=$newUserID;
        $data['Status']=2;

        $subjectID=$buffer[0]['SubjectStyle'];
        $subjectName='未知';
        if($subjectID==0) $subjectID=-1;
        else{
            $subjectBuffer=SS('subject');
            $subjectName=$subjectBuffer[$subjectID]['SubjectName'];
        }
        $data['SubjectID']=$subjectID;
        $data['LoadTime']=time();

        $result=D('ClassUser')->insertData(
            $data
        );
        if($result===false)
            $this->setError('1X3042',1);

        //写入动态
        $userArr   = array();
        $userArr[] = $userID;
        $userArr[] = $buffer[0]['UserID'];
        $uName     = empty($buffer[0]['RealName']) ? $buffer[0]['UserName'] : $buffer[0]['RealName'];

        $this->classDynamic($classID,$userArr,'邀请教师<span class="u_id" uid="'.$userID.'">@'.$uName.'</span>加入<span class="c_id" cid="'.$classID.'">#'.$className.'</span>!');
        $this->setBack('success|'.date('Y-m-d H:i:s',$data['LoadTime']).'|'.date('Y-m-d H:i:s',$buffer[0]['LastTime']).'|'.$newUserID.'|'.$subjectName);
    }

    /**
     * 接受邀请
     */
    public function enterTea2Class() {
        $classID  = $_POST['ClassID'];
        $username = $this->getCookieUserName();

        //验证班级
        $buffer = D('ClassList')->selectData(
            'ClassName,GradeID',
            'ClassID='.$classID
        );
        if(!$buffer)
            $this->setError('30734',1);

        $className = $buffer[0]['ClassName'];

        //获取userid
        $buffer = $this->getModel('User')->selectData(
            'UserID,SubjectStyle',
            'UserName="'.$username.'"'
        );
        $userID    = $buffer[0]['UserID'];
        $subjectID = $buffer[0]['SubjectStyle'];

        //判断用户是否在该班级下 并且状态为2
        $buffer=D('ClassUser')->selectData(
            'CUID,UserID,SubjectID,Status',
            'ClassID='.$classID
        );
        $error='1X3045';
        if($buffer){
            $cuID=0;
            $userArr=array();
            foreach($buffer as $i=>$iBuffer){
                if($iBuffer['UserID']==$userID){
                    if($iBuffer['Status']==0) $error='1X3043';
                    else if($iBuffer['Status']==1) $error='1X3044';
                    else{
                        $cuID=$iBuffer['CUID'];
                        $error='';
                    }
                }
                $userArr[]=$iBuffer['UserID'];
            }
            if(empty($error)){
                //修改用户班级状态
                if(D('ClassUser')->updateData(
                        array('Status'=>0,'SubjectID'=>$subjectID),
                        'CUID='.$cuID
                    )===false){
                    $this->setError('1X3305',1);
                }else{
                    $this->classDynamic($classID,$userArr,'接受邀请加入<span class="c_id" cid="'.$classID.'">#'.$className.'</span>!');
                    $this->setBack('success');
                }
            }else{
                $this->setError($error,1);
            }
        }else{
            $this->setError('30734',1);
        }
    }

    /**
     * 审核教师进入班级
     * @param int $userID  用户id
     * @param int $classID 班级id
     * @param string $username 当前用户名
     */
    public function checkTeacher(){
        $userID   = $_POST['id'];
        $classID  = $_POST['ClassID'];
        $username = $this->getCookieUserName();

        $buffer = $this->getModel('User')->selectData(
            'UserName,RealName,Status',
            'UserID='.$userID
        );
        if(!$buffer)
            $this->setError('30734',1);
        $uName  = empty($buffer[0]['RealName']) ? $buffer[0]['UserName'] : $buffer[0]['RealName'];
        $buffer = D('ClassList')->selectData(
            '*',
            'ClassID='.$classID
        );
        if(!$buffer)
            $this->setError('30214',1);

        if($buffer[0]['Creator']!=$username)
            $this->setError('1X3031',1);

        $result=D('ClassUser')->updateData(
            array('Status'=>0),
            'UserID='.$userID
        );
        if($result===false)
            $this->setError('1X3032',1);

        //写入动态
        $buffer=D('ClassUser')->selectData(
            'UserID',
            'ClassID='.$classID
        );
        $userArr=array();
        foreach($buffer as $i=>$iBuffer){
            $userArr[]=$iBuffer['UserID'];
        }
        $this->classDynamic($classID,$userArr,'通过了教师<span class="u_id" uid="'.$userID.'">@'.$uName.'</span>的入班申请!');
        $this->setBack('success');
    }

    /**
     * 改变学科
     */
    public function changeSubject(){
        //获取参数
        $classID  = $_POST['ClassID'];
        $username = $this->getCookieUserName();
        $subject  = $_POST['Subject'];

        //验证班级
        $buffer=D('ClassList')->selectData(
            'ClassName,GradeID',
            'ClassID='.$classID
        );
        if(!$buffer)
            $this->setError('30734',1);

        $gradeID = $buffer[0]['GradeID'];

        //获取userid
        $buffer = $this->getModel('User')->selectData(
            'UserID',
            'UserName="'.$username.'"'
        );
        $userID = $buffer[0]['UserID'];

        //获取班级用户表对应数据
        $buffer=D('ClassUser')->selectData(
            '*',
            'ClassID='.$classID.' and UserID='.$userID
        );
        $cuID = $buffer[0]['CUID'];

        //获取学科id
        $gradeArr   = SS('grade');
        $subjectArr = SS('subject');
        $subjectID  = '';
        foreach($subjectArr as $i=>$iSubjectArr){
            if($iSubjectArr['SubjectName']==$subject && $iSubjectArr['PID']==$gradeArr[$gradeID]['SubjectID'])
                $subjectID=$i;
        }
        if(empty($subjectID))
            $this->setError('1X3018',1);

        $result=D('ClassUser')->updateData(
            array('SubjectID'=>$subjectID),
            'CUID='.$cuID
        );
        if($result===false)
            $this->setError('1X3046',1);

        //写入动态
        $buffer=D('ClassUser')->selectData(
            'UserID',
            'ClassID='.$classID
        );
        $userArr=array();
        foreach($buffer as $i=>$iBuffer){
            $userArr[]=$iBuffer['UserID'];
        }
        $this->classDynamic($classID,$userArr,'改变学科为<span class="s_id" sid="'.$subjectID.'">#'.$subject.'</span>老师!');
        $this->setBack('success|'.$subject);
    }

    /**
     * 转让班级
     */
    public function changeClassCreator() {
        //获取参数
        $classID  = $_POST['ClassID'];
        $username = $this->getCookieUserName();
        $uName    = $_POST['uname'];
        $pwd      = $_POST['pwd'];

        $uName    = formatString('stripTags',$uName);

        if(strlen($pwd)<6){
            $this->setError('1X3026',1);
        }
        if($uName==''){
            $this->setError('1X3047',1);
        }

        //检查用户密码是否正确
        $user = $this->getModel('User');
        $buffer=$user->selectData(
            'UserID,UserName,Password',
            'UserName="'.$username.'"'
        );
        $password = $buffer[0]['Password'];

        if ($password!=md5($username.$pwd) && $password!=md5($pwd)){
            $this->setError('30804',1);
        }

        //检查新用户
        $buffer=$user->selectData(
            'UserID,UserName,RealName,Status,Whois',
            'UserName="'.$uName.'" or Email="'.$uName.'" or Phonecode="'.$uName.'"'
        );
        if(!$buffer) $this->setError('1X3051',1);
        if($buffer[0]['Status']!=0) $this->setError('1X3048',1);
        if($buffer[0]['Whois']!=1) $this->setError('1X3049',1);

        $userID=$buffer[0]['UserID'];
        $newName=$buffer[0]['UserName'];
        if($newName==$username) $this->setError('1X3050',1);
        $uName=empty($buffer[0]['RealName']) ? $buffer[0]['UserName'] : $buffer[0]['RealName'];

        //验证班级
        $buffer=D('ClassList')->selectData(
            'ClassName,Creator,GradeID',
            'ClassID='.$classID
        );
        if(!$buffer) $this->setError('30734',1);
        if($buffer[0]['Creator']!=$username) $this->setError('1X3052',1);

        //判断是否是班级下教师
        $buffer=D('ClassUser')->selectData(
            'Status',
            'ClassID='.$classID.' and UserID='.$userID
        );
        if(!$buffer) $this->setError('1X3053',1);
        if($buffer[0]['Status']!=0) $this->setError('1X3054',1);

        $result=D('ClassList')->updateData(
            array('Creator'=>$newName),
            'ClassID='.$classID
        );
        if($result===false)
            $this->setError('1X3055',1);

        //写入动态
        $buffer=D('ClassUser')->selectData(
            'UserID',
            'ClassID='.$classID
        );
        $userArr=array();
        foreach($buffer as $i=>$iBuffer){
            $userArr[]=$iBuffer['UserID'];
        }
        $this->classDynamic($classID,$userArr,'把班级转让给了<span class="u_id" uid="'.$userID.'">@'.$uName.'</span>老师!');
        $this->setBack('success');
    }

    /**
     * 上传xsl
     */
    public function uploadify() {
        $username = $_POST['user'];
        $s        = $_POST['codes'];
        $classID  = $_POST['ClassID'];

        if($s!=md5(C('DOC_HOST_KEY').$username.date("Y.m.d",time()))){
            $data['description'] = '您无权添加学生，上传失败';
            $data['msg'] = '用户名：'.$username.'，验证码：'.$s;
            D('Base')->unionSelect('addErrorLog',$data);
            exit('check error');
        }

        if (!empty($_FILES)) {

            $path=R('Common/UploadLayer/uploadExcel',array('','excel_stu'));
            if(is_array($path)){
                 exit($path[1]);
            }
            $arr=unserialize($path);
            foreach ($arr as $realArr) {
                $brr[] = formatString('stripTags',$realArr);
            }

        }
        if(empty($brr)){
            $data['description'] = '未提取到学生信息，请检查上传文档！，上传失败';
            $data['msg'] = $_FILES;
            D('Base')->unionSelect('addErrorLog',$data);
            exit('未提取到学生信息，请检查上传文档！');
        }
        $output=array();
        $output=$this->createStudent2db($brr,0,$classID,$username);
        if(!is_array($output)){
            $data['description'] = '返回数据格式错误，上传失败';
            $data['msg'] = $_FILES;
            D('Base')->unionSelect('addErrorLog',$data);
            exit($output);
        }
        exit('success');
    }
}