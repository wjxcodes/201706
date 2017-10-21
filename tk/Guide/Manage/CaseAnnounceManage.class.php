<?php
/**
 * @author demo
 * @date 2015-5-9
 */
/**
 * 导学案栏目配置类，用于导学案栏目的相关操作
 */
namespace Guide\Manage;
class CaseAnnounceManage extends BaseController  {
    var $moduleName = '导学案发布管理';
    /**
     * 浏览导学案发布列表
     * @author demo
     */
    public function index() {
        $pageName = '导学案发布管理';
        $subjectArray = SS('subjectParentId'); //父类数据集
        $map=array();
        $data=' WorkType=2 ';
            if($this->ifSubject && $this->mySubject){
                $data .= ' AND SubjectID in ('.$this->mySubject.')';
            }
            if($_REQUEST['name']){
                //简单查询
                $map['name']=$_REQUEST['name'];
                $data.=' AND WorkName like "%'.$_REQUEST['name'].'%" ';
            }else{
                //高级查询
                if($_REQUEST['SubjectID']){
                    if($this->ifSubject && $this->mySubject){
                        if(!in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                            $this->showerror('您不能搜索非所属学科题型！');
                        }
                    }
                    $map['SubjectID']=$_REQUEST['SubjectID'];
                    $data.=' AND SubjectID ="'.$_REQUEST['SubjectID'].'" ';
                }
                if($_REQUEST['UserName']){
                    $map['UserName']=$_REQUEST['UserName'];
                    $data.=' AND UserName like "%'.$_REQUEST['UserName'].'%" ';
                }
            }
        $perpage=C('WLN_PERPAGE');
        $userWork = $this->getModel('UserWork');
        $count = $userWork->selectCount(
            $data,
            'WorkID'); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性;
        $page=page($count,$_GET['p'],$perpage).','.$perpage;
        $list = $userWork->pageData(
            'WorkID,UserName,WorkName,WorkStyle,StartTime,EndTime,WorkOrder,Message,SubjectID,LoadTime,IfDelete',
            $data,
            'WorkID desc',
            $page); //获取题型数据集
        $this->pageList($count,$perpage,$map);
        if($list){
            $subject=SS('subject');
            foreach($list as $i=>$iList){
                $list[$i]['SubjectName']=$subject[$iList['SubjectID']]['ParentName'].$subject[$iList['SubjectID']]['SubjectName'];
            }
        }
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('subjectArray', $subjectArray); //题型数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 查看导学案发布记录
     * @author demo
     */
    public function edit() {
        $workID=$_GET['id']; //获取数据标识
        //判断数据标识
        if(empty($workID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '导学案发布记录详情';
        $edit = $this->getModel('UserWork')->selectData(
            '*',
            'WorkID='.$workID);
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712'); //您不能编辑非所属学科栏目！
            }
        }
        $forumArray = array();
        $count=0;
        $cookieArrTmp=unserialize($edit[0]['CookieStr'])['forum'];
        foreach($cookieArrTmp as $i=>$iCookieArrTmp){
            foreach($iCookieArrTmp[2] as $jCookieArrTmp){
                $forumArray[$count]['Order']=$count;
                $forumArray[$count]['forum']=$iCookieArrTmp[0].'('.$iCookieArrTmp[1].')';
                $forumArray[$count]['MenuName']=$jCookieArrTmp['menuName'];
                $forumArray[$count]['ContentList']=$this->getModel('CaseLoreDoc')->formatCaseCookie($jCookieArrTmp['menuContent'],$jCookieArrTmp['ifTest'].'|'.$jCookieArrTmp['ifAnswer']);
                $count++;
            }
        }
        $subject=SS('subject');
        $edit[0]['SubjectName']=$subject[$edit[0]['SubjectID']]['ParentName'].$subject[$edit[0]['SubjectID']]['SubjectName'];
        /*载入模板标签*/
        $this->assign('edit', $edit[0]);
        $this->assign('forumArray',$forumArray);//学案详细数据
        $this->assign('pageName', $pageName);
        $this->display('CaseAnnounce/edit');
    }

    /**
     * 作答情况
     * @author demo
     */
    public function respond(){
        $workID=$_REQUEST['WorkID'];
        $pageName='作答情况列表';
        //判断数据标识
        if(empty($workID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $map=array();
        $data='a.WorkID='.$workID;
        $map['WorkID']=$workID;
        if($this->ifSubject && $this->mySubject){
            $data .= ' AND a.SubjectID in ('.$this->mySubject.')';
        }
        if($_REQUEST['name']){
            //简单查询
            $map['name']=$_REQUEST['name'];
            $data.=' AND a.SendID = "'.$_REQUEST['name'].'" ';
        }else{
            //高级查询
            if($_REQUEST['SubjectID']){
                if($this->ifSubject && $this->mySubject){
                    if(!in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                        $this->setError('30712',1);
                    }
                }
                $map['SubjectID']=$_REQUEST['SubjectID'];
                $data.=' AND a.SubjectID ="'.$_REQUEST['SubjectID'].'" ';
            }
            if($_REQUEST['Status']){
                $map['Status']=$_REQUEST['Status'];
                $data.=' AND a.Status = "'.$_REQUEST['Status'].'" ';
            }
        }
        $perpage=C('WLN_PERPAGE');
        $userSendWork = $this->getModel('UserSendWork');
        $count = $userSendWork->unionSelect(
            'userSendWorkSelectCount',
            $data
        );
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性;
        $page=page($count,$_GET['p'],$perpage).','.$perpage;
        $list = $userSendWork->unionSelect(
            'userSendWorkSelectPageByWhere',
            $data,
            $page
        );
        if($list){
            $subject=SS('subject');
            foreach($list as $i=>$iList){
                $list[$i]['SubjectName']=$subject[$iList['SubjectID']]['ParentName'].$subject[$iList['SubjectID']]['SubjectName'];
                if($iList['DoTime']<60){
                    $list[$i]['DoTime']=$list[$i]['DoTime'].'秒';
                }else{
                    $list[$i]['DoTime']=formatString('timeConversion',$list[$i]['DoTime']);
                }
            }
        }
        $subjectArray=SS('subjectParentId');
        $this->pageList($count,$perpage,$map);
        $this->assign('pageName',$pageName);
        $this->assign('workID',$workID);
        $this->assign('subjectArray',$subjectArray);
        $this->assign('list',$list);
        $this->display();
    }

    /**
     * 导学案作答详情
     * @author demo
     */
    public function answerDetail(){
        $sendID=$_GET['SendID'];
        $pageName="导学案作答详情";
        //判断数据标识
        if(empty($sendID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $edit=$this->getModel('UserSendWork')->unionSelect(
            'userWorkSelectBySendID',
            $sendID
        );
        //处理作答记录
        //试题数据
        $host=C('WLN_DOC_HOST');
        $answerBuffer=$this->getModel('UserAnswerRecord')->selectData(
            'TestID,AnswerText,OrderID,IfRight',
            'SendID='.$sendID,
            'Number ASC,OrderID ASC'
        );
        $answerArr=array();
        if($answerBuffer){
            foreach($answerBuffer as $answerBuffern){
                $answerArr[$answerBuffern['TestID']][$answerBuffern['OrderID']]=$answerBuffern;
            }
            unset($answerBuffer);
        }
        $testReal=$this->getModel('TestReal');
        $buffer=$testReal->getTestIndex(array('testid','testnum','test','answer'),array('TestID'=>$edit[0]['TestList']),'',array('page'=>1,'perpage'=>100));
        if($buffer[0]) $buffer=R('Common/TestLayer/reloadTestArr',array($buffer[0]));
        $tmpArr=explode(',',$edit[0]['TestList']);
        foreach($tmpArr as $i=>$iTmpArr){
            $edit[0]['TestInfo'][$i]['Test']=R('Common/TestLayer/strFormat',array($buffer[$iTmpArr]['test']));
            $edit[0]['TestInfo'][$i]['realAnswer']=$buffer[$iTmpArr]['answer'];
            if($buffer[$iTmpArr]['testnum']>1){
                for($j=0;$j<$buffer[$iTmpArr]['testnum'];$j++){
                    $edit[0]['TestInfo'][$i]['userAnswer'].=(empty($answerArr[$iTmpArr][$j+1]['AnswerText']) ? '<p style="font-size:20px;font-weight:bold">【小题'.($j+1).'】空</p>' : '<p style="font-size:20px;font-weight:bold">【小题'.($j+1).'】'.$answerArr[$iTmpArr][$j+1]['AnswerText'].'</p>');
                    $edit[0]['TestInfo'][$i]['result'].=($answerArr[$iTmpArr][$j+1]['IfRight']==2 ? '<p style="color:green;font-size:20px;font-weight:bold">【小题'.($j+1).'】√</p>' : '<p style="color:red;font-size:20px;font-weight:bold">【小题'.($j+1).'】X</p>');
                }
            }else{
                switch ($answerArr[$iTmpArr][0]['IfRight']){
                    case "-1":
                        $edit[0]['TestInfo'][$i]['result']='<p style="color:red;font-size:20px;font-weight:bold">未答题</p>';
                        break;
                    case "0":
                        $edit[0]['TestInfo'][$i]['result']='<p style="color:red;font-size:20px;font-weight:bold">无法判断</p>';
                        break;
                    case "1":
                        $edit[0]['TestInfo'][$i]['result']='<p style="color:green;font-size:20px;font-weight:bold">√</p>';
                        break;
                    case "2":
                        $edit[0]['TestInfo'][$i]['result']='<p style="color:red;font-size:20px;font-weight:bold">X</p>';
                        break;
                }
                $edit[0]['TestInfo'][$i]['userAnswer']=(empty($answerArr[$iTmpArr][0]['AnswerText']) ? '空' : stripslashes($answerArr[$iTmpArr][0]['AnswerText']));
            }
        }

        $classData = $this->getModel('ClassList')->selectData(
            'ClassName,SchoolFullName',
            'ClassID='.$edit[0]['ClassID']
        );
        if($edit[0]['DoTime']<60){
            $edit[0]['DoTime']=$edit[0]['DoTime'].'秒';
        }else{
            $edit[0]['DoTime']=formatString('timeConversion',$edit[0]['DoTime']);
        }
        $edit[0]['ClassName']=$classData[0]['ClassName'];
        $edit[0]['SchoolName']=$classData[0]['SchoolFullName'];
        $subject=SS('subject');
        $edit[0]['SubjectName']=$subject[$edit[0]['SubjectID']]['ParentName'].$subject[$edit[0]['SubjectID']]['SubjectName'];
        $edit[0]['CorrectRate']=($edit[0]['CorrectRate']*100).'%';
        $this->assign('pageName',$pageName);
        $this->assign('edit',$edit[0]);
        $this->display();
    }
}

