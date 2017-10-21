<?php
/**
 * @author demo
 * @date 2015年12月30日
 */

namespace Aat\Api;


class ClassApi extends BaseApi
{
    /**
     * 描述：班级消息动态
     * @param $userID
     * @param int $pageSize 分页
     * @return array
     * @author demo
     */
    public function classNews($userID,$pageSize){
//        $dynamic=$this->getModel('Dynamic');
//        $configObjType = $dynamic->getDynamicType();
        $configObjType = $this->getApiCommon('Dynamic/getDynamicType');
        $objType = $configObjType['Class'];
        //获取用户的班级
//        $classDb = $this->getModel('ClassUser')->selectData(
//            'ClassID',
//            ['UserID'=>$userID,'Status'=>0]
//        );
        $classDb = $this->getApiWork('Class/selectData', 'ClassID', ['UserID'=>$userID,'Status'=>0], '', '', 'ClassUser');

        if(!$classDb){
            return [0,'暂时没有动态！（如果您还没有加入班级，请到 我的班级 中加入班级！）'];
        }
        $where = ['ObjType'=>$objType];
        // 查询满足要求的总记录数
//        $count = $dynamic->unionSelect('dynamicToCount',$userID,$objType);
        $count = $this->getApiCommon('Dynamic/unionSelect', 'dynamicToCount',$userID,$objType);

        if (!$count) {
            return [0,'班级下暂时没有动态消息！'];
        }
        $page = handlePage('init',$count,$pageSize,['type'=>1]);
        if($_REQUEST['p']>$page->totalPages){
            return [0,'动态已经加载完毕！'];
        }
        // 进行分页数据查询
        $pageStr=$page->firstRow . ',' . $page->listRows;
//        $listDb = $dynamic->unionSelect('dynamicToPageData',$userID,$where,$pageStr);
        $listDb = $this->getApiCommon('Dynamic/unionSelect', 'dynamicToPageData',$userID,$where,$pageStr);
        if($listDb){
            return [1,$listDb];
        }else{
            return [0,'动态数据查询错误，请重试！'];
        }
    }

    /**
     * 描述：未做作业列表
     * @param $userID
     * @param $pageSize
     * @param $isApp
     * @return array
     * @author demo
     */
    public function undoHomework($userID,$pageSize,$isApp){
        $baseController = $isApp?A('AatApi/Base'):A('Aat/Base');
        $where = 'wtu.UserID = '.$userID.' OR wtc.`Status`=0) AND zj_class_user.Status=0 AND (zj_user_send_work.Status IS NULL OR zj_user_send_work.Status=0';
        // 查询满足要求的总记录数
//        $count = $this->getModel('UserWorkClass')->unionSelect('workToClassCountByUserID',$userID);
        $count = $this->getApiWork('Work/unionSelect', 'workToClassCountByUserID',$userID);
        if (!$count) {
            return [0,'暂时没有未做作业！'];
        }
        $page = handlePage('init',$count,$pageSize,['type'=>1]);
        if($_REQUEST['p']>$page->totalPages){
            return [0,'记录已经加载完毕！'];
        }
        $show = $page->show(false); // 分页显示输出
        // 进行分页数据查询
        $pageStr=$page->firstRow . ',' . $page->listRows;
//        $listDb = $this->getModel('UserWorkClass')->unionSelect('UserWorkClassPageData',$userID,$where,$pageStr);
        $listDb = $this->getApiWork('Work/unionSelect','UserWorkClassPageData',$userID,$where,$pageStr);
        if(!$listDb){
            return [0,'数据查询失败，请重试！'];
        }
        $tempTime = [];//辅助数组
        //格式化时间和学科名和用户名
        $list = [];
        foreach ($listDb as $i => $iListDb) {
            $list[$i]['WorkID'] = $iListDb['WorkID'];
            $list[$i]['ClassID'] = $iListDb['ClassID'];
            $list[$i]['WorkName'] = ($iListDb['WorkType']==2?'[导学案] ':'').($iListDb['WorkName']?$iListDb['WorkName']:'作业');
            $list[$i]['WorkStyle'] = $iListDb['WorkStyle'];
            $list[$i]['WorkType'] = $iListDb['WorkType'];
            $list[$i]['Message'] = $iListDb['Message'];
            $list[$i]['TestNum'] = $iListDb['TestNum'];
            $list[$i]['Status'] = $iListDb['Status'];
            $list[$i]['SendID'] = $iListDb['SendID'];

            $realName = $baseController->getUserInfo($iListDb['UserName'])['RealName'];
            $list[$i]['UserName'] = $realName?$realName:$iListDb['UserName'];
            $list[$i]['SubjectName'] = $baseController->getSubjectName($iListDb['SubjectID']);
            $list[$i]['StartTime'] = date('m-d H:i', $iListDb['StartTime']);
            $list[$i]['EndTime'] = date('m-d H:i', $iListDb['EndTime']);
            $tempTime[$i]['UnixTime'] = $iListDb['LoadTime'];
            if($isApp){
                if(date('Y-m-d',$tempTime[$i]['UnixTime'])!==date('Y-m-d',$tempTime[$i-1]['UnixTime'])){
                    $list[$i]['LoadDate'] = date('Y-m-d',$tempTime[$i]['UnixTime']);
                }
                $list[$i]['LoadTime'] = date('H:i', $iListDb['LoadTime']);
            }else{
                $list[$i]['LoadDate'] = '星期' . $this->getWeekByTime($iListDb['LoadTime']);
                $list[$i]['LoadTime'] = date('m月d日H:i', $iListDb['LoadTime']);
            }

            if($iListDb['Status'] === null){
                $list[$i]['IDType'] = 'work_id';
            }else{
                $list[$i]['IDType'] = 'send_id';
            }
            $now = time();
            if ($now < $iListDb['StartTime']) {
                $list[$i]['Flag'] = 'no_start';
            }elseif ($now > $iListDb['EndTime']) {
                $list[$i]['Flag'] = 'out_date';
            }else{
                $list[$i]['Flag'] = 'normal';
                $leftTime = $iListDb['EndTime']-$now;
                $list[$i]['LeftTime'] = $this->toLeftTime($leftTime);
            }
            //下载链接
            if($iListDb['WorkStyle'] == 1){
                $list[$i]['DownUrl'] = $this->getDownUrl($path = $iListDb['StuWorkDown'],$docName = ($iListDb['WorkName']?$iListDb['WorkName']:'作业'));
            }
        }
        return [1,['show' => $show, 'list' => $list]];
    }

    /**
     * 描述：作业记录
     * @param $userID
     * @param $pageSize
     * @param $isApp
     * @return array
     * @author demo
     */
    public function doneHomework($userID,$pageSize,$isApp){
        $baseController = $isApp?A('AatApi/Base'):A('Aat/Base');
        $where = 'UserID='.$userID.' AND Status!=0';
        // 查询满足要求的总记录数
//        $count = $this->getModel('UserSendWork')->selectCount(
//            $where,
//            'SendID'
//        );

        $count = $this->getApiWork('Work/selectCount', $where, 'SendID', '', 'UserSendWork');
        if (!$count) {
            return [0,'暂时没有已完成的作业记录！'];
        }
        $page = handlePage('init',$count,$pageSize,['type'=>1]);
        if($_REQUEST['p']>$page->totalPages){
            return [0,'记录已经加载完毕！'];
        }
        $show = $page->show(false); // 分页显示输出
        // 进行分页数据查询
        $pageStr=$page->firstRow . ',' . $page->listRows;
//        $listDb=$this->getModel('UserSendWork')->unionSelect('userSendWorkSelectPage',$userID,$pageStr);
        $listDb = $this->getApiWork('Work/unionSelect', 'userSendWorkSelectPage',$userID,$pageStr);
        $tempTime = [];//辅助数组
        $list = [];
        //格式化时间和学科名和用户名
        foreach ($listDb as $i => $iListDa) {
            $list[$i]['SendID'] = $iListDa['SendID'];
            $list[$i]['Comment'] = $iListDa['Comment'];
            $list[$i]['Status'] = $iListDa['Status'];
            $list[$i]['WorkStyle'] = $iListDa['WorkStyle'];
            $list[$i]['WorkType'] = $iListDa['WorkType'];
            $list[$i]['Message'] = $iListDa['Message'];
            $list[$i]['TestNum'] = $iListDa['TestNum'];
            $list[$i]['ClassName'] = $iListDa['ClassName']?$iListDa['ClassName']:'我加入的班级';
            $list[$i]['UserName'] = $baseController->getUserInfo($iListDa['UserName'])['RealName'];
            $list[$i]['SubjectName'] = $baseController->getSubjectName($iListDa['SubjectID']);
            $list[$i]['StartTime'] = date('m-d H:i', $iListDa['StartTime']);
            $list[$i]['EndTime'] = date('m-d H:i', $iListDa['EndTime']);
            $list[$i]['SendTime'] = date('m-d H:i', $iListDa['SendTime']);
            $list[$i]['CheckTime'] = date('m月d日H:i', $iListDa['CheckTime']);
            $list[$i]['DoTime'] = $iListDa['DoTime'] < 60 ? 1 : (int)($iListDa['DoTime'] / 60);
            $list[$i]['CorrectRate'] = (int)($iListDa['CorrectRate'] * 100);
            $tempTime[$i]['UnixTime'] = $iListDa['LoadTime'];
            if($isApp){
                if(date('Y-m-d',$tempTime[$i]['UnixTime'])!==date('Y-m-d',$tempTime[$i-1]['UnixTime'])){
                    $list[$i]['LoadDate'] = date('Y-m-d',$tempTime[$i]['UnixTime']);
                }
                $list[$i]['LoadTime'] = date('H:i', $iListDa['LoadTime']);
            }else{
                $list[$i]['LoadDate'] = '星期' . $this->getWeekByTime($iListDa['LoadTime']);
                $list[$i]['LoadTime'] = date('m月d日H:i', $iListDa['LoadTime']);
            }

            $list[$i]['WorkName'] = ($iListDa['WorkType']==2?'[导学案] ':'').($iListDa['WorkName']?$iListDa['WorkName']:'作业');
            //下载链接
            if($iListDa['WorkStyle'] == 1){
                $list[$i]['DownUrl'] = $this->getDownUrl($path = $iListDa['StuWorkDown'],$docName = ($iListDa['WorkName']?$iListDa['WorkName']:'作业'));
            }
        }
        return [1,['show' => $show, 'list' => $list]];
    }

    /**
     * 描述：用户加入的班级
     * @param $userID
     * @return array
     * @author demo
     */
    public function userClasses($userID){
//        $data = $this->getModel('ClassUser')->unionSelect('classListByUserID',$userID);
        $data = $this->getApiWork('Class/unionSelect', 'classListByUserID',$userID);
        if ($data) {
            return [1,$data];
        } else {
            return [0,'暂时还没有加入的班级！'];
        }
    }

    /**
     * 描述：班级信息
     * @param $classID
     * @param $isApp
     * @return array
     * @author demo
     */
    public function classInfoByID($classID,$isApp){
        $baseController = $isApp?A('AatApi/Base'):A('Aat/Base');
        if (!$classID) {
            return [0,'查询参数错误，请刷新重试！'];
        }
        //班级名称 总人数（学生） 学生 老师 班级编号 班级创建时间 创建者
//        $data = $this->getModel('ClassList')->unionSelect('classListSelectByClassId',$classID);
        $data = $this->getApiWork('Class/unionSelect', 'classListSelectByClassId',$classID);
        if (!$data) {
            return [0, '班级信息不存在，请刷新重试！'];
        }
//        $cacheSubject = SS('subject');
        $cacheSubject = $this->getApiCommon('Subject/subject');
        $stuData = [];
        $teaData = [];
        foreach ($data as $iData) {
            //学生
            if ($iData['Whois'] == 0) {
                if ($iData['UserPic']) {
                    if (!preg_match('/^http:.*/i', $iData['UserPic'])) {//判断是不是QQ头像
                        $iData['UserPic'] = C('WLN_DOC_HOST') . $iData['UserPic'];//非QQ头像
                    }
                } else {//调用默认头像
                    $iData['UserPic'] = __ROOT__ . '/Public/newAat/images/default.jpg';
                }
                $stuData[] = [
                    'UserID' => $iData['UserID'],
                    'UserName' => $iData['UserName'],
                    'RealName' => $iData['RealName'],
                    'UserPic' => $iData['UserPic'],
                ];
            } elseif ($iData['Whois'] == 1) {
                //老师
                $teaData[] = array(
                    'UserName' => $iData['UserName'],
                    'RealName' => $iData['RealName'],
                    'SubjectName' => $cacheSubject[$iData['SubjectID']]['SubjectName'],
                    'isCreator' => $iData['UserName'] == $iData['Creator'] ? 1 : 0,
                );
            }
        }
        $realName = $baseController->getUserInfo($data[0]['Creator'])['RealName'];
        $classInfo = array(
            'ClassID' => $data[0]['ClassID'],
            'ClassName' => $data[0]['ClassName'],
            'Creator' => $realName ? $realName : $data[0]['Creator'],
            'LoadTime' => date('Y年m月d日 H:i'),
            'OrderNum' => $data[0]['OrderNum'],
            'SchoolName' => $data[0]['SchoolName'],
            'StudentAll' => count($stuData),
            'teacherAmount' => count($teaData)
        );
        $recommendClass = [];
        $ranking = $this->studentRanking($stuData, $classID);
        return [1,[
            'class_info' => $classInfo,
            'student' => $stuData,
            'teacher' => $teaData,
            'recommend_class' => $recommendClass,
            'ranking' => $ranking,
        ]];
    }

    /**
     * 描述：班级作业列表
     * @param $classID
     * @param $userID
     * @param $isApp
     * @return array
     * @author demo
     */
    public function classHomework($classID,$userID,$isApp){
        $baseController = $isApp?A('AatApi/Base'):A('Aat/Base');
        //判断所请求的班级是否为用户的班级
//        $userWorkClassModel = $this->getModel('UserWorkClass');
        $classUserWhere=array('ClassID'=>$classID,'UserID'=>$userID,'Status'=>0);
//        $classUserTmp = $this->getModel('ClassUser')->findData(
//            'CUID',
//            $classUserWhere
//        );
        $classUserTmp = $this->getApiWork('Class/findData', 'CUID', $classUserWhere, '', 'ClassUser');
        if(!$classUserTmp){
            return [0,'您没有权限查看此班级作业或者班级不存在！'];
        }
        $where = '(wtu.UserID = '.$userID.' OR wtc.Status=0) AND wtc.ClassID='.$classID;
        // 查询满足要求的总记录数
//        $count = $userWorkClassModel->unionSelect('userWorkClassCountData',$userID,$classID);
        $count = $this->getApiWork('Work/unionSelect', 'userWorkClassCountData',$userID,$classID);
        if (!$count) {
            return [0,'此班级下还没有作业记录！'];
        }
        $page = handlePage('init',$count,5,['type'=>1]);
        $show = $page->show(false); // 分页显示输出
        // 进行分页数据查询
        $pageStr=$page->firstRow . ',' . $page->listRows;
//        $listDb = $userWorkClassModel->unionSelect('UserWorkClassPageData',$userID,$where,$pageStr);
        $listDb = $this->getApiWork('Work/unionSelect', 'UserWorkClassPageData',$userID,$where,$pageStr);
        $list = [];
        //格式化时间和学科名和用户名
        foreach ($listDb as $i => $iListData) {
            $list[$i]['SendID'] = $iListData['SendID'];
            $list[$i]['WorkID'] = $iListData['WorkID'];
            $list[$i]['WorkName'] = ($iListData['WorkType']==2?'[导学案] ':'').($iListData['WorkName']?$iListData['WorkName']:'作业');
            $list[$i]['ClassID'] = $iListData['ClassID'];
            $list[$i]['Comment'] = $iListData['Comment'];
            $list[$i]['Status'] = $iListData['Status'];
            $list[$i]['WorkStyle'] = $iListData['WorkStyle'];
            $list[$i]['WorkType'] = $iListData['WorkType'];
            $list[$i]['Message'] = $iListData['Message'];
            $list[$i]['TestNum'] = $iListData['TestNum'];
            $realName=$baseController->getUserInfo($iListData['UserName'])['RealName'];
            $list[$i]['UserName'] = $realName?$realName:$iListData['UserName'];
            $list[$i]['SubjectName'] = $baseController->getSubjectName($iListData['SubjectID']);
            $list[$i]['StartTime'] = date('m月d日H:i', $iListData['StartTime']);
            $list[$i]['EndTime'] = date('m月d日H:i', $iListData['EndTime']);
            $list[$i]['SendTime'] = date('m月d日H:i', $iListData['SendTime']);
            $list[$i]['CheckTime'] = date('m月d日H:i', $iListData['CheckTime']);
            $list[$i]['DoTime'] = $iListData['DoTime'] < 60 ? 1 : (int)($iListData['DoTime'] / 60);
            $list[$i]['CorrectRate'] = (int)($iListData['CorrectRate'] * 100);
            $list[$i]['LoadDate'] = '星期' . $this->getWeekByTime($iListData['LoadTime']);
            $list[$i]['LoadTime'] = date('m月d日H:i', $iListData['LoadTime']);
            if($iListData['Status'] === null){
                $list[$i]['IDType'] = 'work_id';
            }else{
                $list[$i]['IDType'] = 'send_id';
            }
            if (time() < $iListData['StartTime']) {
                $list[$i]['Flag'] = 'no_start';
            }
            if (time() > $iListData['EndTime']) {
                $list[$i]['Flag'] = 'out_date';
            }
            //下载链接
            if($iListData['WorkStyle'] == 1){
                $list[$i]['DownUrl'] = $this->getDownUrl($path = $iListData['StuWorkDown'],$docName = ($iListData['WorkName']?$iListData['WorkName']:'作业'));
            }
        }
        return [1,['show' => $show, 'list' => $list]];
    }

    /**
     * 描述：搜索班级
     * @param $searchKey
     * @param $userID
     * @param $isApp
     * @return array
     * @author demo
     */
    public function searchClassList($searchKey,$userID,$isApp){
        $baseController = $isApp ? A('AatApi/Base') : A('Aat/Base');
//        $classListModel = $this->getModel('ClassList');
        $pregTel = checkString('checkIfPhone',$searchKey);//验证用户输入的是否是手机号
        //如果是手机号
        //通过手机号找到手机号用户的班级
        if (strlen($searchKey) == 11 && $pregTel != 0) {
//            $classInfo = $classListModel->unionSelect('getClassInfoByPhoneNum', $searchKey);
            $classInfo = $this->getApiWork('Class/unionSelect','getClassInfoByPhoneNum', $searchKey);
        } else {//如果不是手机号，默认为班级编号
            $reg = '/^\d+$/';
            if (preg_match($reg, $searchKey) == 0) {
                return [0, '请输入正确的班级编号！'];
            }
//            $classInfo = $classListModel->selectData(
//                'ClassID,ClassName,OrderNum,Creator,SchoolFullName',
//                ['OrderNum' => $searchKey]
//            );
            $classInfo = $this->getApiWork('Class/selectData','ClassID,ClassName,OrderNum,Creator,SchoolFullName',['OrderNum' => $searchKey],'','','ClassList');
        }
        $realName = $baseController->getUserInfo($classInfo[0]['Creator'])['RealName'];
        if (!$classInfo || !$classInfo[0]['ClassID']) {
            return [0, '您所加入的班级不存在，请核实输入内容！'];
        }
//        $classIDList = $this->getModel('ClassUser')->selectData(
//            'ClassID,Status',
//            ['UserID' => $userID]
//        );
        $classIDList = $this->getApiWork('Class/selectData','ClassID,Status',['UserID' => $userID],'','','ClassUser');
        $classID = [];//已加入的班级ID
        $status = [];//已加入班级的状态
        $classList = [];//可加入的班级列表
        foreach ($classIDList as $iClassIDList) {
            $classID[] = $iClassIDList['ClassID'];
            $status[$iClassIDList['ClassID']] = $iClassIDList['Status'];
        }
        foreach ($classInfo as $iClassInfo) {
            $iClassInfo['Status'] = -1;//未加入
            $iClassInfo['Creator'] = $realName ? $realName : $iClassInfo['Creator'];
            //判断是否是已加入的班级
            if (in_array($iClassInfo['ClassID'], $classID)) {
                $iClassInfo['Status'] = $status[$iClassInfo['ClassID']];
            }
            $classList[] = $iClassInfo;
        }
        if (!$classList) {//说明所有的班级都已加入
            return [0, '没有可加入的班级！'];
        }
        return [1, $classList];
    }

    /**
     * 描述：加入班级
     * @param $classID
     * @param $userID
     * @return array
     * @author demo
     */
    public function joinClass($classID,$userID){
        if(!$classID||!is_numeric($classID)){//判断是否是数字
            return [0,'请求参数错误，请重试！'];
        }
        //判断是否有该班级
        $data = $this->getApiWork('Class/findData', 'ClassName',['ClassID'=>$classID], '', 'ClassList');
        if(!$data){
            return [0,'请求参数错误，班级不存在！'];
        }
//        $result = $this->getModel('ClassUser')->insertData([
//            'ClassID'=>$classID,
//            'UserID'=>$userID,
//            'Status'=>1,
//            'SubjectID'=>0,
//            'LoadTime'=>time()
//        ]);
        $result = $this->getApiWork('Class/insertData',[
            'ClassID'=>$classID,
            'UserID'=>$userID,
            'Status'=>1,
            'SubjectID'=>0,
            'LoadTime'=>time()
        ], 'ClassUser');
        if($result){
            return [1,$result];
        }else{
            return [0,'申请失败，请重新申请加入！'];
        }
    }

    /**
     * 插入班级动态
     * 提交作业逻辑使用Exercise/ExerciseLogic
     * @param int $classID 班级ID
     * @param int $userID
     * @param string $content 动态内容
     * @param array $receiveUserArray 接收者ID（默认提交作业事件班级内所有成员都能看到）
     * @return bool 成功返回true
     * @author demo
     */
    public function insertClassDynamic($classID,$userID,$content,$receiveUserArray){
        if(!($classID&&$content&&$receiveUserArray&&is_array($receiveUserArray))){
            return false;
        }
//        $configObjType = $this->getModel('Dynamic')->getDynamicType();
        $configObjType = $this->getApiCommon('Dynamic/getDynamicType');
        $objType = $configObjType['Class'];
        $dynamicData = [
            'ObjType' => $objType,
            'ObjID' => $classID,
            'UserID' => $userID,
            'Content' => $content,
            'LoadTime' => time(),
        ];
        return R('Common/ClassLayer/addClassDynamic',[$dynamicData,$receiveUserArray]);
    }

    /**
     * 根据原始路径获取下载链接
     * @param $path string 原始下载路径
     * @param $docName string 文档名（不带后缀）
     * @return string 下载地址或者false
     * @author demo
     */
    private function getDownUrl($path, $docName) {
        $host = C('WLN_DOC_HOST');
        if ($host && $path) {
            $url = $host . R('Common/UploadLayer/getDocServerUrl', array($path, 'down', '', $docName));
            return $url;
        } else {
            //否则，下载链接为空
            return '';
        }
    }

    /**
     * 根据时间获取星期
     * @param int $time 时间秒为单位
     * @author demo
     */
    private function  getWeekByTime($time){
        $weekArray = ['日', '一', '二', '三', '四', '五', '六'];
        return $weekArray[date('w', $time)];
    }

    /**
     * 把秒换算为分钟小时天的表达
     * @param  int $s 时间秒为单位
     * @return string    返回的时间如23分钟；3小时；1天2小时
     * @author demo
     */
    private function toLeftTime($s){
        if($s<3600){
            //计算分钟
            $leftTime = floor($s/60).'分钟';
        }else{
            //计算小时
            $leftHour = floor($s/3600);

            if($leftHour<24){
                //5小时
                $leftTime = $leftHour.'小时';
            }else{
                //1天2小时
                $leftTime = floor($leftHour/24).'天'.($leftHour%24).'小时';
            }
        }
        return $leftTime;
    }

    /**
     * 根据指定算法对学生作业答题情况进行排序
     * 说明：last:最后一次测试 avg：最后10次测试平均 improve：最后一次和倒数第二次的成绩差
     * @param array $studentArray 用户数组 UserID UserName RealName UserPic
     * @param int $classID 班级ID
     * @return array 排序后的用户名数组 last avg improve
     * @author demo
     */
    private function studentRanking($studentArray, $classID) {
        if ($studentArray && is_array($studentArray)) {
            $data = [];//存储last avg improve数据 用作array_multisort排序
            foreach ($studentArray as $i => $iStudentArray) {
//                $studentDb = $this->getModel('UserSendWork')->selectData(
//                    'CorrectRate',
//                    ['UserID' => $iStudentArray['UserID'], 'ClassID' => $classID],
//                    'SendID DESC',
//                    '10'
//                );
                $studentDb = $this->getApiWork('Work/selectData','CorrectRate',
                    ['UserID' => $iStudentArray['UserID'], 'ClassID' => $classID],
                    'SendID DESC',
                    '10','UserSendWork');
                $data['last'][$i] = $studentDb[0]['CorrectRate'];
                $sum = 0;//总正确率
                foreach ($studentDb as $m) {
                    $sum += $m['CorrectRate'];
                }
                $data['avg'][$i] = $sum / count($studentDb);//平均正确率
                $data['improve'][$i] = $studentDb[0]['CorrectRate'] - $studentDb[1]['CorrectRate'];
            }
            $dataLast = $studentArray;
            $dataAvg = $studentArray;
            $dataImprove = $studentArray;
            array_multisort($data['last'], SORT_DESC, $dataLast);//通过$data['last']对$dataLast排序
            array_multisort($data['avg'], SORT_DESC, $dataAvg);
            array_multisort($data['improve'], SORT_DESC, $dataImprove);
            $rankArray = [
                'last' => $dataLast[0],
                'avg' => $dataAvg[0],
                'improve' => $dataImprove[0],
            ];
            return $rankArray;
        } else {
            return false;
        }
    }

}